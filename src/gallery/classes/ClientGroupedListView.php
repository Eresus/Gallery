<?php
/**
 * Галерея изображений
 *
 * Класс представления "Просмотр списка изображений (с группами)"
 *
 * @version ${product.version}
 *
 * @copyright 2011, ООО "Два слона", http://dvaslona.ru/
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
 * $Id: Exceptions.php 1004 2010-10-19 14:05:08Z mk $
 */


/**
 * Класс представления "Просмотр списка изображений (с группами)"
 *
 * @package Gallery
 * @since 2.03
 */
class GalleryClientGroupedListView extends GalleryClientListView
{
	/**
	 * Возвращает список групп
	 *
	 * @param int $sectionId  идентификатор раздела
	 * @param int $limit      максимальное количество групп
	 * @param int $offset     номер первой группы
	 *
	 * @return array
	 *
	 * @since 2.03
	 */
	protected function getItems($sectionId, $limit, $offset)
	{
		$items = GalleryGroup::find($sectionId, $limit, $offset, true);
		return $items;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает максимальное количество групп на странице
	 *
	 * @param Plugin $plugin
	 *
	 * @return int
	 *
	 * @since 2.03
	 */
	protected function getMaxCount(Plugin $plugin)
	{
		return $plugin->settings['groupsPerPage'];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает количество страниц в списке
	 *
	 * @param int $sectionId     идентификатор раздела
	 * @param int $itemsPerPage  количество изображений на странице
	 *
	 * @return int
	 *
	 * @since 2.03
	 */
	protected function countPageCount($sectionId, $itemsPerPage)
	{
		return ceil(GalleryGroup::count($sectionId) / $itemsPerPage);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает шаблон
	 *
	 * @param Plugin $plugin  объект плагина
	 *
	 * @return Template
	 *
	 * @since 2.03
	 */
	protected function getTemplate(Plugin $plugin)
	{
		return new Template('templates/' . $plugin->name . '/image-grouped-list.html');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект для отрисовки всплывающего блока
	 *
	 * @return GalleryClientPopupGroupedView
	 *
	 * @since 2.03
	 */
	protected function getPopupView()
	{
		return new GalleryClientPopupGroupedView();
	}
	//-----------------------------------------------------------------------------
}
