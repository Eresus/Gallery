<?php
/**
 * Галерея изображений
 *
 * Альбом с группами
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author Михаил Красильников <mk@3wstyle.ru>
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
 * Альбом с группами
 *
 * Только для КИ!
 *
 * Альбом — это список изображений в определённом разделе сайта
 *
 * @package Gallery
 * @since 2.03
 */
class Gallery_AlbumGrouped extends GalleryAlbum
{
	/**
	 * Загружет объекты из БД, если они не были загружены ранее
	 *
	 * @return void
	 *
	 * @since 2.03
	 */
	protected function load()
	{
		if ($this->loaded)
		{
			return;
		}

		$q = DB::getHandler()->createSelectQuery();
		$e = $q->expr;

		$q->where(
			$e->lAnd(
				$e->eq('section', $q->bindValue($this->sectionId, null, PDO::PARAM_INT)),
				$e->eq('active', $q->bindValue(true, null, PDO::PARAM_BOOL)),
				$e->neq('groupId', 0)
			))->
			orderBy('groupId');

		$this->items = GalleryImage::load($q);

		$this->loaded = true;
	}
	//-----------------------------------------------------------------------------
}
