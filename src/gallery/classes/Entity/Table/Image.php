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
	}
}