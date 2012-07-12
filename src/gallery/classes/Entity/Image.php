<?php
/**
 * Изображение
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
 * Изображение
 *
 * @property      int                  $id           идентификатор картинки
 * @property      int                  $section      идентификатор раздела
 * @property      string               $title        название изображения
 * @property      Gallery_Entity_Group $group        группа изображений
 * @property      bool                 $cover        является ли обложкой альбома
 * @property      bool                 $active       является ли активным
 * @property      int                  $position     порядковый номер
 * @property-read string               $thumbURL     URL миниатюры
 *
 * @package Gallery
 */
class Gallery_Entity_Image extends ORM_Entity
{
	/**
	 * Геттер свойства $group
	 *
	 * @return Gallery_Entity_Group|Gallery_NullObject
	 */
	protected function getGroup()
	{
		$table = ORM::getTable($this->plugin, 'Group');
		try
		{
			return $table->find($this->getProperty('groupId'));
		}
		catch (DomainException $e)
		{
			return new Gallery_NullObject();
		}
	}

	/**
	 * Геттер свойства $thumbURL
	 *
	 * @return string
	 */
	protected function getThumbURL()
	{
		return $this->plugin->getDataURL() . $this->getProperty('thumb');
	}
}