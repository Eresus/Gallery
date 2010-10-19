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

/**
 * ���������� ���������� ������
 */
window.Eresus.gallery = 
{
	thumbsRebuild: {dialog: null}
};


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
		window.Eresus.galleryRequest('gallery', 'getGroups', galleryImageEditLoadGroups, inputSection.val());
	}
	else
	{
		blockGroup.hide();
	}
});


jQuery('#settings span.hint-control').
live('mouseenter', function (e)
{
	var control = jQuery(e.target); 
	control.next('.hint-block').css('left', control.position().left + 'px').fadeIn();
}).
live('mouseleave', function (e)
{
	jQuery(e.target).next('.hint-block').fadeOut();
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
	var url = this.siteRoot + '/admin.php?mod=ext-' + module + '&args=';
	var args = '/' + action + '/';
	for (var i = 3; i < arguments.length; i++)
	{
		args += arguments[i] + '/';
	}
	url += encodeURIComponent(args);
	jQuery.ajax({url: url, success: callback, error: callback, dataType: 'json'});
};


/**
 * ��������� ������� ������������ ��������
 * 
 * @param {Integer} newWidth
 * @param {Integer} newHeight
 * @type Boolean
 * @return false
 */
function rebuildThumbnails(newWidth, newHeight)
{
	var dialog = jQuery('#thumbsRebuildDialog');
	// ��������� ���������� � ���������� �������
	window.Eresus.gallery.thumbsRebuild.dialog = dialog;
	
	dialog.
		dialog({
			autoOpen: false,
			buttons: { "�������": function() { jQuery(this).dialog('close'); }},
			draggable: false,
			modal: true,
			rsizable: false,
			title: '��� ������������ ��������...',
			open: function ()
			{
				jQuery('#thumbsRebuildErrors *').remove();
				jQuery('#thumbsRebuildMessage').text('�� ���������� ��� �������� �� ��������� ��������.');
				jQuery('.progressbar', this).progressbar().progressbar('value', 0);
				window.Eresus.galleryRequest('gallery', 'thumbsRebuildStart', galleryThumbsRebuildHandler,
					newWidth, newHeight);
			},
			close: function ()
			{
				jQuery('#settings').submit();
			}
		}).
		dialog('open');
	
	return false;
}


/**
 * ��������� � ����� ������ �����
 * 
 * @param {Object|XMLHttpRequest}    data
 * @param {String}                   textStatus
 * @param {XMLHttpRequest|Exception} extra
 * @type void
 */
function galleryThumbsRebuildHandler(data, textStatus, extra)
{
	if (!window.Eresus.gallery.thumbsRebuild.dialog)
	{
		return;
	}
	
	switch (textStatus)
	{
		case 'success':
			switch (data.action)
			{
				case 'start':
					if (data.ids.length)
					{
						window.Eresus.gallery.thumbsRebuild.ids = data.ids;
						window.Eresus.gallery.thumbsRebuild.total = data.ids.length;
						jQuery('#thumbsRebuildLeft').text(data.ids.length);
						window.Eresus.galleryRequest('gallery', 'thumbsRebuildNext', galleryThumbsRebuildHandler,
							window.Eresus.gallery.thumbsRebuild.ids[0], data.width, data.height);
					}
					else
					{
						window.Eresus.gallery.thumbsRebuild.dialog.dialog('close');
						return;
					}
				break;
				
				case 'build':
					if (data.status != 'success')
					{
						jQuery('<div>').text(data.status).appendTo('#thumbsRebuildErrors');
					}
					window.Eresus.gallery.thumbsRebuild.ids.shift();
					var left = window.Eresus.gallery.thumbsRebuild.ids.length;
					jQuery('#thumbsRebuildLeft').text(left);
					var done = window.Eresus.gallery.thumbsRebuild.total - left;
					var progress = Math.round(100 / window.Eresus.gallery.thumbsRebuild.total * done);
					jQuery('.progressbar', window.Eresus.gallery.thumbsRebuild.dialog).
						progressbar('value', progress);
					
					if (window.Eresus.gallery.thumbsRebuild.ids.length)
					{
						window.Eresus.galleryRequest('gallery', 'thumbsRebuildNext',
							galleryThumbsRebuildHandler, window.Eresus.gallery.thumbsRebuild.ids[0], 
							data.width, data.height);
					}
					else
					{
						if (jQuery('#thumbsRebuildErrors div').length == 0)
						{
							window.Eresus.gallery.thumbsRebuild.dialog.dialog('close');
							return;
						}

						jQuery('#thumbsRebuildMessage').
							text('� �������� ������ ��������� ������. �������� ���� ��� ��������� �������� �� �����������.');
					}
				break;
			}
		break;
		
		default:
			alert('�� ������� �������� ����� �� �������. �������� �������� � ���������� ��� ���.');
	}
}
