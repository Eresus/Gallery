<?php
/**
 * Группа
 *
 * @version ${product.version}
 *
 * @copyright 2012, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Михаил Красильников <mk@dvaslona.ru>
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
 * Группа
 *
 * @property      int                    $id           Идентификатор
 * @property      int                    $section      Идентификатор раздела
 * @property      string                 $title        Название
 * @property      string                 $description  Описание
 * @property      string                 $position     Порядковый номер
 * @property-read Gallery_Entity_Image[] $allImages    все изображения в группе, включая отключенные
 * @property-read Gallery_Entity_Image[] $images       изображения в группе
 *
 * @package Gallery
 * @since 3.00
 */
class Gallery_Entity_Group extends ORM_Entity
{
	/**
	 * Перемещает объект вверх по списку
	 *
	 * @return void
	 *
	 * @since 2.00
	 */
	public function moveUp()
	{
		if ($this->position == 0)
		{
			return;
		}

		$table = ORM::getTable($this->plugin, 'Group');
		$q = $table->createSelectQuery();
		$e = $q->expr;
		$q->where($e->lAnd(
			$e->eq('section', $q->bindValue($this->section, null, PDO::PARAM_INT)),
			$e->lt('position', $q->bindValue($this->position, null, PDO::PARAM_INT))
		))->
			limit(1)->
			orderBy('position', ezcQuerySelect::DESC);

		/* @var Gallery_Entity_Group $swap */
		$swap = $table->loadOneFromQuery($q);
		if (!$swap)
		{
			return;
		}
		$pos = $this->position;
		$this->position = $swap->position;
		$swap->position = $pos;
		$table->update($swap);
		$table->update($this);
	}

	/**
	 * Перемещает объект вниз по списку
	 *
	 * @return void
	 */
	public function moveDown()
	{
		$table = ORM::getTable($this->plugin, 'Group');
		$q = $table->createSelectQuery();
		$e = $q->expr;
		$q->where($e->lAnd(
			$e->eq('section', $q->bindValue($this->section, null, PDO::PARAM_INT)),
			$e->gt('position', $q->bindValue($this->position, null, PDO::PARAM_INT))
		))->
			limit(1)->
			orderBy('position', ezcQuerySelect::ASC);

		/* @var Gallery_Entity_Group $swap */
		$swap = $table->loadOneFromQuery($q);
		if (!$swap)
		{
			return;
		}
		$pos = $this->position;
		$this->position = $swap->position;
		$swap->position = $pos;
		$table->update($swap);
		$table->update($this);
	}

	/**
	 * Вызывается перед удалением записи из БД
	 *
	 * @param ezcQuery $query  запрос, который будет выполнен для удаления записи
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function beforeDelete(ezcQuery $query)
	{
		$table = ORM::getTable($this->plugin, 'Image');
		foreach ($this->allImages as $image)
		{
			$table->delete($image);
		}
	}

	/**
	 * Возвращает объект как массив свойств
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'id' => $this->id,
			'section' => $this->section,
			'title' => $this->title,
			'description' => $this->description,
			'position' => $this->position
		);
	}

	/**
	 * Список изображений группы
	 *
	 * @return Gallery_Entity_Image[]
	 */
	protected function getAllImages()
	{
		$table = ORM::getTable($this->plugin, 'Image');
		$q = $table->createSelectQuery();
		$q->where($q->expr->eq('groupId', $q->bindValue($this->id, null, PDO::PARAM_INT)));
		$q->orderBy('position');
		return $table->loadFromQuery($q);
	}

	/**
	 * Список изображений группы
	 *
	 * @return Gallery_Entity_Image[]
	 */
	protected function getImages()
	{
		$table = ORM::getTable($this->plugin, 'Image');
		$q = $table->createSelectQuery();
		$q->where(
			$q->expr->lAnd(
				$q->expr->eq('groupId', $q->bindValue($this->id, null, PDO::PARAM_INT)),
				$q->expr->eq('active', $q->bindValue(true, null, PDO::PARAM_BOOL))
			)
		);
		$q->orderBy('position');
		return $table->loadFromQuery($q);
	}
}
