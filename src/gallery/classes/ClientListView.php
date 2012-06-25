<?php
/**
 * Класс представления "Просмотр списка изображений"
 *
 * @version ${product.version}
 *
 * @copyright 2011, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
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
 * $Id: Exceptions.php 1004 2010-10-19 14:05:08Z mk $
 */


/**
 * Класс представления "Просмотр списка изображений"
 *
 * @package Gallery
 * @since 2.03
 */
class Gallery_ClientListView
{
	/**
	 * Возвращает разметку представления
	 *
	 * @return void
	 *
	 * @since 2.03
	 */
	public function render()
	{
		global $page;

		$plugin = $GLOBALS['Eresus']->plugins->load('gallery');

		if ($page->subpage == 0)
		{
			$page->subpage = 1;
		}

		$maxCount = $this->getMaxCount($plugin);
		$startFrom = ($page->subpage - 1) * $maxCount;
		$items = $this->getItems($page->id, $maxCount, $startFrom);

		// Данные для подстановки в шаблон
		$data = array();
		$data['this'] = $plugin;
		$data['page'] = $page;
		$data['Eresus'] = $GLOBALS['Eresus'];
		$data['items'] = $items;

		/* Ищем обложку альбома */
		$data['cover'] = Gallery_Image::findCover($page->id);
		$totalPages = $this->countPageCount($page->id, $maxCount);

		if ($totalPages > 1)
		{
			$pager = new PaginationHelper($totalPages, $page->subpage);
			$data['pager'] = $pager->render();
		}
		else
		{
			$data['pager'] = '';
		}

		/* Создаём экземпляр шаблона */
		$tmpl = $this->getTemplate($plugin);

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		if ($plugin->settings['showItemMode'] == 'popup')
		{
			$view = $this->getPopupView();
			$html .= $view->render();
		}

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список изображений
	 *
	 * @param int $sectionId  идентификатор раздела
	 * @param int $limit      максимальное количество изображений
	 * @param int $offset     номер первого изображения
	 *
	 * @return array
	 *
	 * @since 2.03
	 */
	protected function getItems($sectionId, $limit, $offset)
	{
		$items = Gallery_Image::find($sectionId, $limit, $offset, true);
		return $items;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает максимальное количество изображений на странице
	 *
	 * @param Plugin $plugin
	 *
	 * @return int
	 *
	 * @since 2.03
	 */
	protected function getMaxCount(Plugin $plugin)
	{
		return $plugin->settings['itemsPerPage'];
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
		return ceil(Gallery_Image::count($sectionId, true) / $itemsPerPage);
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
		return new Template('templates/' . $plugin->name . '/image-list.html');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект для отрисовки всплывающего блока
	 *
	 * @return Gallery_ClientPopupView
	 *
	 * @since 2.03
	 */
	protected function getPopupView()
	{
		return new Gallery_ClientPopupView();
	}
	//-----------------------------------------------------------------------------
}
