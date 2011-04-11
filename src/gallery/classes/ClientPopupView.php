<?php
/**
 * Галерея изображений
 *
 * Класс представления "Просмотр во всплывающем блоке"
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
 * Класс представления "Просмотр во всплывающем блоке"
 *
 * @package Gallery
 * @since 2.03
 */
class GalleryClientPopupView
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
		$plugin = $GLOBALS['Eresus']->plugins->load('gallery');
		$plugin->linkJQuery();
		$GLOBALS['page']->linkScripts($plugin->getCodeURL() . 'gallery.js');
		$GLOBALS['page']->linkStyles($plugin->getCodeURL() . 'gallery.css');

		$tmpl = new Template('templates/' . $plugin->name . '/popup.html');
		$popup = $tmpl->compile();
		$html = '<div id="gallery-popup">' . $popup . '</div>';

		$html .= $this->renderImageList();

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает список изображений альбма для перехода к ним во всплывающем блоке.
	 *
	 * @return string
	 *
	 * @since 2.03
	 */
	protected function renderImageList()
	{
		$items = GalleryImage::find($GLOBALS['page']->id, null, null, true);
		$jsArray = array();
		foreach ($items as $item)
		{
			$jsArray []= '"' . $item->imageURL . '"';
		}
		$html = '<script type="text/javascript">Eresus.Gallery.images = ['.
			implode(',', $jsArray) . '];</script>';
		return $html;
	}
	//-----------------------------------------------------------------------------
}
