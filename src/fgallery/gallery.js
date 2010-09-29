/**
 * ������� �����������
 *
 * ���������� �������
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
 * @package FGallery
 *
 * $Id: fgallery.php 267 2010-06-22 14:54:37Z mk $
 */


jQuery('#Content a').live('click', function (e)
{
	/* ���� ������ �� ����� ������ ���� */
	if (e.which != 1)
	{
		return;
	}
	
	/* ���� �������� �� �� <a> */
	if (e.currentTarget.nodeName.toLowerCase() != 'a')
	{
		return;
	}
	
	var anchor = jQuery(e.currentTarget);
	
	/* ���� � href ��� ������� */
	if (anchor.attr('href').substr(-14) != '#gallery-popup')
	{
		return;
	}
	
	var stub = anchor.clone();
	stub.lightBox({
		fixedNavigation: false,
		imageLoading: '$(httpRoot)ext/fgallery/lightbox/lightbox-ico-loading.gif',
		imageBtnPrev: '$(httpRoot)ext/fgallery/lightbox/lightbox-btn-prev.gif',
		imageBtnNext: '$(httpRoot)ext/fgallery/lightbox/lightbox-btn-next.gif',
		imageBtnClose: '$(httpRoot)ext/fgallery/lightbox/lightbox-btn-close.gif',
		imageBlank: '$(httpRoot)ext/fgallery/lightbox/lightbox-blank.gif'
	});
	stub.click();
	
	return false;
});
