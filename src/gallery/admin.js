/**
 * ������� �����������
 *
 * ���������� ������� ��
 *
 * @version ${product.version}
 *
 * @copyright 2010, ��� "��� �����", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
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
 * @package gallery
 *
 * $Id$
 */


jQuery('#content div.image a.delete').live('click', function (e)
{
	return confirm("������������� �������� �����������?");
});


/**
 * ������������ ������ ����� � ��������� ������� � ������� ��������� �����������
 * 
 * @param {Event} e
 * @type void
 */
jQuery('#input-section').live('change', function (e)
{
	var form = jQuery('#editImage').get(0);
	var blockGroup = jQuery('#input-group-block');
	var inputSection = jQuery('#input-section');
	var inputGroup = jQuery('#input-group');
	
	if (inputSection.val() != form.original_section.value)
	{
		blockGroup.show();
		inputGroup.attr('disabled', 'disabled');
		jQuery('*', inputGroup).remove();
		window.Eresus.galleryRequest('fgallery', 'getGroups', galleryImageEditLoadGroups, inputSection.val());
	}
	else
	{
		blockGroup.hide();
	}
});


/**
 * ��������� � ����� ������ �����
 * 
 * @param {Object|XMLHttpRequest}    data
 * @param {String}                   textStatus
 * @param {XMLHttpRequest|Exception} extra
 * @type void
 */
function galleryImageEditLoadGroups(data, textStatus, extra)
{
	switch (textStatus)
	{
		case 'success':
			var inputGroup = jQuery('#input-group');
			inputGroup.removeAttr('disabled');
			for (var i = 0; i < data.length; i++)
			{
				jQuery('<option />').text(data[i].title).attr('value', data[i].id).appendTo(inputGroup);
			}
		break;
		
		default:
			alert('�� ������� �������� ������ ����� � �������. �������� �������� � ���������� ��� ���.');
	}
}


/**
 * ��������� XMLHttpRequest � �� �������
 * 
 * @param {String}   module    ��� ������ (������ ������ ���� "gallery")
 * @param {String}   action    ������������� ��������
 * @param {Function} callback  ���������� ������
 * @param �������������� ���������
 * 
 * @type void
 */
window.Eresus.galleryRequest = function (module, action, callback)
{
	var url = this.siteRoot + '/admin.php?mod=ext-' + module + '&args=/' + action + '/';
	for (var i = 3; i < arguments.length; i++)
	{
		url += arguments[i] + '/';
	}
	jQuery.ajax({url: url, success: callback, error: callback, dataType: 'json'});
};
