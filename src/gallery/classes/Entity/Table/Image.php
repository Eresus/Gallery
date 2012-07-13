<?php
/**
 * Таблица изображений
 *
 * @version ${product.version}
 *
 * @copyright 2012, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Михаил Красильников <mk@dvaslonas.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package Gallery
 *
 * $Id$
 */

/**
 * Таблица изображений
 *
 * @package Gallery
 * @since 3.00
 */
class Gallery_Entity_Table_Image extends ORM_Table
{
	/**
	 * Структура таблицы
	 */
	public function setTableDefinition()
	{
		$this->setTableName('gallery_images');
		$this->hasColumns(array(
			'id' => array(
				'type' => 'integer',
				'unsigned' => true,
				'autoincrement' => true,
			),
			'section' => array(
				'type' => 'integer',
				'unsigned' => true,
				'default' => null,
			),
			'title' => array(
				'type' => 'string',
				'length' => 255,
				'default' => null,
			),
			'image' => array(
				'type' => 'string',
				'length' => 128,
				'default' => null,
			),
			'thumb' => array(
				'type' => 'string',
				'length' => 128,
				'default' => null,
			),
			'posted' => array(
				'type' => 'timestamp'
			),
			'groupId' => array(
				'type' => 'integer',
				'unsigned' => true,
				'default' => 0,
			),
			'cover' => array(
				'type' => 'boolean',
				'default' => 0,
			),
			'active' => array(
				'type' => 'boolean',
				'default' => 0,
			),
			'position' => array(
				'type' => 'integer',
				'unsigned' => true,
				'default' => 0,
			),
		));
		$this->index('section', array('fields' => array('section')));
		$this->index('position', array('fields' => array('position')));
		$this->index('active', array('fields' => array('active')));
		$this->index('posted', array('fields' => array('posted')));
		$this->index('find_covers', array('fields' => array('section', 'active', 'cover')));
		$this->index('images_by_time', array('fields' => array('section', 'active', 'posted')));
		$this->index('images_by_position', array('fields' => array('section', 'active', 'position')));
		/* Проверенные индексы */
		$this->index('images_by_group_idx', array('fields' => array('group', 'active', 'position')));
	}

	/**
	 * Возвращает изображения, принадлежащие разделу или группе
	 *
	 * @param int|Gallery_Entity_Group $owner   идентификатор раздела или группа
	 * @param int                      $limit   максимальное количество возвращаемых групп
	 * @param int                      $offset  позиция с которой начать выборку
	 * @param bool                     $all     возвращать также отключенные изображения
	 *
	 * @return Gallery_Entity_Group[]
	 */
	public function findInSection($owner, $limit = null, $offset = 0, $all = true)
	{
		$q = $this->createSelectQuery();
		/* Строим условие выборки */
		$where = '1';
		if ($owner)
		{
			if ($owner instanceof Gallery_Entity_Group)
			{
				$where = $q->expr->eq('groupId', $q->bindValue($owner->id, null, PDO::PARAM_INT));
			}
			else
			{
				$where = $q->expr->eq('section', $q->bindValue($owner, null, PDO::PARAM_INT));
			}
		}

		if (false == $all)
		{
			$where = $q->expr->lAnd($where,
				$q->expr->eq('active', $q->bindValue(true, null, PDO::PARAM_BOOL)));
		}

		if ($this->plugin->settings['useGroups'])
		{
			// Отбираем только изображения, привязанные к группам
			$where = $q->expr->lAnd($where, $q->expr->neq('groupId', 0));
		}

		$q->where($where);
		$q->orderBy('position');

		return $this->loadFromQuery($q, $limit, $offset);
	}

	/**
	 * Возвращает количество групп в указанном разделе
	 *
	 * @param int  $id   ID раздела сайта
	 * @param bool $all  возвращать также отключенные изображения
	 *
	 * @return int
	 */
	public function countInSection($id, $all = true)
	{
		$q = $this->createCountQuery();

		$where = $q->expr->eq('section', $q->bindValue($id, null, PDO::PARAM_STR));
		if (false == $all)
		{
			$where = $q->expr->lAnd($where,
				$q->expr->eq('active', $q->bindValue(true, null, PDO::PARAM_BOOL)));
		}

		$q->where($where);
		return $this->count($q);
	}

	/**
	 * Сбрасывает флаг "Обложка альбома" у всех изображений в указанном разделе
	 *
	 * @param int $section
	 */
	public function clearCovers($section)
	{
		$q = DB::getHandler()->createUpdateQuery();
		$q->update($this->getTableName())->
			set('cover', $q->bindValue(false, null, PDO::PARAM_BOOL))->
			where($q->expr->eq('section', $q->bindValue($section, null, PDO::PARAM_INT)));
		DB::execute($q);
	}

	/**
	 * Автоматически выбирает изображение для обложки в указанном разделе
	 *
	 * @param int  $section  Идентификатор раздела
	 *
	 * @return void
	 *
	 * @since 2.00
	 */
	public function autoSetCover($section)
	{
		/*
		 * ez Components не поддерживает LIMIT в запросах UPDATE, так что делаем два запроса
		 */
		$q = DB::getHandler()->createSelectQuery();
		$e = $q->expr;
		$q->select('id');
		$q->from($this->getTableName());
		$q->where($e->lAnd(
			$e->eq('section', $q->bindValue($section, null, PDO::PARAM_INT)),
			$e->eq('active', $q->bindValue(true, null, PDO::PARAM_BOOL)),
			$e->neq('cover', $q->bindValue(true, null, PDO::PARAM_BOOL))
		));
		$q->limit(1);

		switch ($this->plugin->settings['sort'])
		{
			case 'date_asc':
				$q->orderBy('posted', ezcQuerySelect::DESC);
				break;

			case 'date_desc':
				$q->orderBy('posted', ezcQuerySelect::ASC);
				break;

			case 'manual':
				$q->orderBy('position');
				break;
		}

		try
		{
			$tmp = DB::fetch($q);
		}
		catch (DBQueryException $e)
		{
			// Нет такой записи. Ничего делать не надо
			return;
		}

		$q = DB::getHandler()->createUpdateQuery();
		$e = $q->expr;
		$q->update($this->getTableName());
		$q->set('cover', true);
		$q->where($e->eq('id', $q->bindValue($tmp['id'], null, PDO::PARAM_INT)));

		DB::execute($q);
	}

	/**
	 * Ищет обложку для указанного раздела
	 *
	 * @param int $section
	 *
	 * @return Gallery_Image|bool  Возвращает изображение или FALSE, если обложка отсутствует
	 */
	public function findCover($section)
	{
		$q = DB::getHandler()->createSelectQuery();
		$e = $q->expr;
		$q->select('*');
		$q->from($this->getTableName());
		$q->where($e->lAnd(
			$e->eq('section', $q->bindValue($section, null, PDO::PARAM_INT)),
			$e->eq('active', $q->bindValue(true, null, PDO::PARAM_BOOL)),
			$e->eq('cover', $q->bindValue(true, null, PDO::PARAM_BOOL))
		));

		$image = $this->loadOneFromQuery($q);
		if (!$image)
		{
			return false;
		}
		return $image;
	}

	/**
	 * @param ORM_Entity $entity
	 */
	public function persist(ORM_Entity $entity)
	{
		/* Вычисляем порядковый номер */
		$q = DB::getHandler()->createSelectQuery();
		$e = $q->expr;
		$q->select($q->alias($e->max('position'), 'maxval'));
		$q->from($this->getTableName());
		$q->where($e->eq('section', $q->bindValue($entity->section, null, PDO::PARAM_INT)));
		$result = DB::fetch($q);
		$entity->position = $result['maxval'] + 1;

		parent::persist($entity);
	}
}