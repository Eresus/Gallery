/**
 * ������� �����������
 *
 * ���� ������ ��������� � ������������ ��� Eresus, ��������� ������������ ��� Galley, ����������
 * ��� ������� �������.
 *
 * @version ${product.version}
 *
 * @copyright 2008, ��� "��� �����", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author ��������� ��������
 * @author ������ ������������ <mk@3wstyle.ru>
 *
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� �� ������ ������ � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 * @package Gallery
 *
 * $Id: fgallery.php 267 2010-06-22 14:54:37Z mk $
 */


/* ���� ������������ ��� Eresus �� ���������, ��������� ���. */
var Eresus;
if (!Eresus)
{
	Eresus = {};
	
	/**
	 * ���������� ������� ��������
	 * 
	 * ������ ������� �� ���������:
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
	 * ���������� �������� scroll �������� 
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
 * ������������ ��� �������
 */
Eresus.Gallery = 
{
		/**
		 * ����������� ����
		 * 
		 * @type jQuery
		 */
		popup: null,

		/**
		 * �������
		 * 
		 * @type jQuery
		 */
		overlay: null,
		
		/**
		 * ������� ��������� (��������) �����
		 */
		popupActive: false
};

/**
 * �������� ����������� ����
 *
 * @param {Event} e
 *
 * @type Boolean
 * @return FALSE ��� ���������� �������� ���� ������� ������� ���� � TRUE � ������ ������, �����
 * ������� ������ �� ������
 */
Eresus.Gallery.imageClickHandler = function (e)
{
	/*
	 * ��������! FireFox, Chrome � Opera ���������� ���� ������ ������� ����� ������. IE ����������
	 * ��� � ������� ������.
	 */

	/* ���� �������� �� �� <a> */
	if (e.currentTarget.nodeName.toLowerCase() != 'a')
	{
		return true;
	}

	var anchor = jQuery(e.currentTarget);

	/* ���� � href ��� ������� */
	if (!anchor.attr('href').match(/#gallery-popup$/))
	{
		return true;
	}

	Eresus.Gallery.showPopup(anchor.attr('href'));

	return false;
};
//-----------------------------------------------------------------------------

/**
 * ��������� � ���������� �������
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
 * ��������� � ���������� ������ �����
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
 * ��������� ����������� �� ����������� �����
 *
 * @param {String} image
 *
 * @type void
 */
Eresus.Gallery.showPopup = function (image)
{
	Eresus.Gallery.overlay.show();
	Eresus.Gallery.resizeOverlay();
	Eresus.Gallery.popup.show();
	Eresus.Gallery.resizePopup();

	jQuery('#gallery-popup-image').
		load(Eresus.Gallery.resizePopup).
		removeAttr('width').
		removeAttr('height').
		attr('src', image);
	Eresus.Gallery.popupActive = true;
};
//-----------------------------------------------------------------------------

/**
 * ��������� ����������� ����
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
 * ���������� ������� ������
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
 * �������������
 *
 * @type void
 */
Eresus.Gallery.init = function ()
{
	/*
	 * ��������� ������ �� ����������� ����
	 * ������ �� ���� ������ �����������
	 */
	Eresus.Gallery.popup = jQuery('#gallery-popup');
	jQuery('.js-gallery-close', Eresus.Gallery.popup).click(Eresus.Gallery.closePopup);
	
	//��������� �� �������
	Eresus.Gallery.overlay = jQuery('<div class="ui-gallery-overlay"></div>');
	// ��������� ������� � ���� � body
	jQuery('body').
		append(Eresus.Gallery.overlay).
		append(Eresus.Gallery.popup);

	// ������ �����������
	jQuery('#Content a').live('click', Eresus.Gallery.imageClickHandler);
	jQuery(document).keydown(Eresus.Gallery.onKeyDown);
};
//-----------------------------------------------------------------------------


jQuery(document).ready(Eresus.Gallery.init);
