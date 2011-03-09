/**
 * Галерея изображений
 *
 * Этот скрипт добавляет в пространство имён Eresus, вложенное пространство имён Galley, содержащее
 * все символы скрипта.
 *
 * @version ${product.version}
 *
 * @copyright 2008, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Александр Гаврилюк
 * @author Михаил Красильников <mk@3wstyle.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо по вашему выбору с условиями более поздней
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
 * $Id: fgallery.php 267 2010-06-22 14:54:37Z mk $
 */


/* Если пространство имён Eresus не объявлено, объявляем его. */
var Eresus;
if (!Eresus)
{
	Eresus = {};
	
	/**
	 * Возвращает размеры страницы
	 * 
	 * Массив состоит из элементов:
	 * 
	 * - pageWidth
	 * - pageHeight
	 * - windowWidth
	 * - windowHeight
	 * 
	 * @type Array
	 * 
	 * @author quirksmode.com
	 */
	Eresus.getPageSize = function()
	{
		var xScroll, yScroll;
		if (window.innerHeight && window.scrollMaxY) 
		{	
			xScroll = window.innerWidth + window.scrollMaxX;
			yScroll = window.innerHeight + window.scrollMaxY;
		} 
		else if (document.body.scrollHeight > document.body.offsetHeight)
		{ 
			// all but Explorer Mac
			xScroll = document.body.scrollWidth;
			yScroll = document.body.scrollHeight;
		} 
		else 
		{ 
			// Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
			xScroll = document.body.offsetWidth;
			yScroll = document.body.offsetHeight;
		}
		var windowWidth, windowHeight;
		if (self.innerHeight) 
		{	
			// all except Explorer
			if(document.documentElement.clientWidth)
			{
				windowWidth = document.documentElement.clientWidth; 
			} 
			else 
			{
				windowWidth = self.innerWidth;
			}
			windowHeight = self.innerHeight;
		} 
		else if (document.documentElement && document.documentElement.clientHeight) 
		{ 
			// Explorer 6 Strict Mode
			windowWidth = document.documentElement.clientWidth;
			windowHeight = document.documentElement.clientHeight;
		} 
		else if (document.body) 
		{ 
			// other Explorers
			windowWidth = document.body.clientWidth;
			windowHeight = document.body.clientHeight;
		}	
		
		// for small pages with total height less then height of the viewport
		if (yScroll < windowHeight)
		{
			pageHeight = windowHeight;
		} 
		else 
		{ 
			pageHeight = yScroll;
		}
		// for small pages with total width less then width of the viewport
		if(xScroll < windowWidth)
		{	
			pageWidth = xScroll;		
		} 
		else 
		{
			pageWidth = windowWidth;
		}
		arrayPageSize = {
			'pageWidth': pageWidth,
			'pageHeight': pageHeight,
			'windowWidth': windowWidth,
			'windowHeight': windowHeight
		};
		return arrayPageSize;
	};
	//-----------------------------------------------------------------------------
	
	/**
	 * Возвращает значения scroll страницы 
	 *
	 * @type Array
	 * 
	 * @author quirksmode.com
	 */
	Eresus.getPageScroll = function () 
	{
		var xScroll, yScroll;
		if (self.pageYOffset) 
		{
			yScroll = self.pageYOffset;
			xScroll = self.pageXOffset;
		} 
		else if (document.documentElement && document.documentElement.scrollTop)
		{	
			// Explorer 6 Strict
			yScroll = document.documentElement.scrollTop;
			xScroll = document.documentElement.scrollLeft;
		} 
		else if (document.body) 
		{
			// all other Explorers
			yScroll = document.body.scrollTop;
			xScroll = document.body.scrollLeft;	
		}
		arrayPageScroll = {'x': xScroll, 'y': yScroll};
		return arrayPageScroll;
	};
	//-----------------------------------------------------------------------------
}

/**
 * Пространство имён плагина
 */
Eresus.Gallery = 
{
		/**
		 * Всплывающий блок
		 * 
		 * @type jQuery
		 */
		popup: null,

		/**
		 * Оверлей
		 * 
		 * @type jQuery
		 */
		overlay: null,
		
		/**
		 * Список адресов доступных изображений раздела
		 * 
		 * Заполняется сервером.
		 */
		images: [],
		
		/**
		 * Индекс текущего изображения
		 * 
		 * Если false, значит всплывающий блок скрыт
		 */
		imageIndex: false
};

/**
 * Определяет индекс указанного изображения в массиве images и сохраняет его в imageIndex
 *
 * @param {String} url
 *
 * @type void
 */
Eresus.Gallery.findImageIndex = function (url)
{
	url = url.replace(/#.*$/, '');
	for (var i = 0; i < Eresus.Gallery.images.length; i++)
	{
		if (Eresus.Gallery.images[i] == url)
		{
			Eresus.Gallery.imageIndex = i;
			return;
		}
	}
	Eresus.Gallery.imageIndex = false;
};
//-----------------------------------------------------------------------------

/**
 * Вызывает всплывающий блок
 *
 * @param {Event} e
 *
 * @type Boolean
 * @return FALSE для подавления перехода если удалось открыть блок и TRUE в случае ошибки, чтобы
 * браузер прошёл по ссылке
 */
Eresus.Gallery.imageClickHandler = function (e)
{
	/*
	 * Внимание! FireFox, Chrome и Opera пропускают сюда только нажатия левой кнопки. IE пропускает
	 * ещё и нажатие колеса.
	 */

	/* Если кликнули не по <a> */
	if (e.currentTarget.nodeName.toLowerCase() != 'a')
	{
		return true;
	}

	var anchor = jQuery(e.currentTarget);

	/* Если в href нет маркера */
	if (!anchor.attr('href').match(/#gallery-popup$/))
	{
		return true;
	}

	Eresus.Gallery.showPopup(anchor.attr('href'));

	return false;
};
//-----------------------------------------------------------------------------

/**
 * Вычисляет и выставляет оверлея
 *
 * @type void
 */
Eresus.Gallery.resizeOverlay = function ()
{
	var pageSize = Eresus.getPageSize();
	
	Eresus.Gallery.overlay.css({
		width: pageSize.pageWidth + 'px',
		height: pageSize.pageHeight + 'px'
	});
};
//-----------------------------------------------------------------------------

/**
 * Вычисляет и выставляет размер блока
 *
 * @type void
 */
Eresus.Gallery.resizePopup = function ()
{
	var pageSize = Eresus.getPageSize();
	var pageScroll = Eresus.getPageScroll();
	
	var offsetTop = Math.round((pageSize.windowHeight - Eresus.Gallery.popup.height()) / 2, 10); 
	var popupTop = pageScroll.y + offsetTop;
	if (popupTop < 0)
	{
		popupTop = 0;
	}
	var offsetLeft = Math.round((pageSize.windowWidth - Eresus.Gallery.popup.width()) / 2, 10);
	var popupLeft = pageScroll.x + offsetLeft;
	if (popupLeft < 0)
	{
		popupLeft = 0;
	}
	Eresus.Gallery.popup.css({
		top:	popupTop + 'px',
		left:	popupLeft + 'px'
	});
};
//-----------------------------------------------------------------------------

/**
 * Обновляет состояние ЭУ (Назад, Вперёд)
 * 
 * @type void
 */
Eresus.Gallery.resetControls = function ()
{
	var uiPrev = jQuery('.js-gallery-prev', Eresus.Gallery.popup);
	var uiNext = jQuery('.js-gallery-next', Eresus.Gallery.popup);
	
	if (Eresus.Gallery.imageIndex === false)
	{
		uiPrev.hide();
		uiNext.hide();
	}
	else
	{
		uiPrev.show();
		uiNext.show();
	}
	
	if (Eresus.Gallery.imageIndex === 0)
	{
		uiPrev.hide();
	}
	if (Eresus.Gallery.imageIndex === Eresus.Gallery.images.length - 1)
	{
		uiNext.hide();
	}
};
//-----------------------------------------------------------------------------

/**
 * Открывает изображение во всплывающем блоке
 *
 * @param {String} image
 *
 * @type void
 */
Eresus.Gallery.showPopup = function (image)
{
	Eresus.Gallery.overlay.show();
	Eresus.Gallery.resizeOverlay();
	
	Eresus.Gallery.findImageIndex(image);
	
	Eresus.Gallery.resetControls();
	
	Eresus.Gallery.popup.show();
	Eresus.Gallery.resizePopup();

	jQuery('#gallery-popup-image').
		load(Eresus.Gallery.resizePopup).
		removeAttr('width').
		removeAttr('height').
		attr('src', image);
};
//-----------------------------------------------------------------------------

/**
 * Открывает предыдущее изображение
 *
 * @type void
 */
Eresus.Gallery.showPrev = function ()
{
	if (Eresus.Gallery.imageIndex === false)
	{
		alert('Error!');
		return;
	}

	Eresus.Gallery.imageIndex--;
	Eresus.Gallery.resetControls();
	
	jQuery('#gallery-popup-image').
		load(Eresus.Gallery.resizePopup).
		attr('src', Eresus.Gallery.images[Eresus.Gallery.imageIndex]);
};
//-----------------------------------------------------------------------------

/**
 * Открывает следующее изображение
 *
 * @type void
 */
Eresus.Gallery.showNext = function ()
{
	if (Eresus.Gallery.imageIndex === false)
	{
		alert('Error!');
		return;
	}

	Eresus.Gallery.imageIndex++;
	Eresus.Gallery.resetControls();
	
	jQuery('#gallery-popup-image').
		load(Eresus.Gallery.resizePopup).
		attr('src', Eresus.Gallery.images[Eresus.Gallery.imageIndex]);
};
//-----------------------------------------------------------------------------

/**
 * Закрывает всплывающий блок
 *
 * @param {Event} e
 * 
 * @type void
 */
Eresus.Gallery.closePopup = function (e)
{
	Eresus.Gallery.popup.hide();
	jQuery('#gallery-popup-image').attr('src', '');
	Eresus.Gallery.overlay.hide();
	Eresus.Gallery.popupActive = false;
	e.preventDefault();
};
//-----------------------------------------------------------------------------

/**
 * Обработчик нажатий клавиш
 * 
 * @param {Event} e
 * 
 * @type void
 */
Eresus.Gallery.onKeyDown = function (e)
{
	if (Eresus.Gallery.popupActive)
	{
		if (e.keyCode == 27)
		{
			Eresus.Gallery.closePopup();
		}
	}
};
//-----------------------------------------------------------------------------

/**
 * Инициализация
 *
 * @type void
 */
Eresus.Gallery.init = function ()
{
	/*
	 * Сохраняем ссылку на всплывающий блок
	 * Вешаем на блок нужные обработчики
	 */
	Eresus.Gallery.popup = jQuery('#gallery-popup');
	jQuery('.js-gallery-close', Eresus.Gallery.popup).click(Eresus.Gallery.closePopup);
	jQuery('.js-gallery-prev', Eresus.Gallery.popup).click(Eresus.Gallery.showPrev);
	jQuery('.js-gallery-next', Eresus.Gallery.popup).click(Eresus.Gallery.showNext);
	
	//Сохраняем на оверлей
	Eresus.Gallery.overlay = jQuery('<div class="ui-gallery-overlay"></div>');
	// Переносим оверлей и блок в body
	jQuery('body').
		append(Eresus.Gallery.overlay).
		append(Eresus.Gallery.popup);

	// Вешаем обработчики
	jQuery('#Content a').live('click', Eresus.Gallery.imageClickHandler);
	jQuery(document).keydown(Eresus.Gallery.onKeyDown);
};
//-----------------------------------------------------------------------------


jQuery(document).ready(Eresus.Gallery.init);
