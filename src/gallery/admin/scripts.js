/**
 * Клиентские скрипты АИ
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Михаил Красильников <mk@dvaslona.ru>
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
 * @package gallery
 */

/**
 * Глобальные переменные модуля
 */
window.Eresus.gallery =
{
	thumbsRebuild: {dialog: null}
};


jQuery('#content div.image a.delete').live('click', function (e)
{
	return confirm("Подтверждаете удаление изображения?");
});


/**
 * Запрашивает список групп в указанном разделе в диалоге изменения изображения
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
 * Загружает в форму список групп
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
			alert('Не удалось получить список групп с сервера. Обновите страницу и попробуйте ещё раз.');
	}
}


/**
 * Выполняет XMLHttpRequest к АИ плагина
 *
 * @param {String}   module    Имя модуля (всегда должно быть "gallery")
 * @param {String}   action    Запрашиваемое действие
 * @param {Function} callback  Обработчик ответа
 * @param дополнительные аргументы
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
 * Запускает процесс пересоздания миниатюр
 *
 * @param {Integer} newWidth
 * @param {Integer} newHeight
 * @type Boolean
 * @return false
 */
function rebuildThumbnails(newWidth, newHeight)
{
	var dialog = jQuery('#thumbsRebuildDialog');
	// Сохраняем переменные в глобальной области
	window.Eresus.gallery.thumbsRebuild.dialog = dialog;

	dialog.
		dialog({
			autoOpen: false,
			buttons: { "Закрыть": function() { jQuery(this).dialog('close'); }},
			draggable: false,
			modal: true,
			rsizable: false,
			title: 'Идёт пересоздание миниатюр...',
			open: function ()
			{
				jQuery('#thumbsRebuildErrors *').remove();
				jQuery('#thumbsRebuildMessage').text('Не закрывайте эту страницу до окончания процесса.');
				jQuery('.progressbar', this).progressbar().progressbar('value', 0);
				window.Eresus.galleryRequest('gallery', 'thumbsRebuildStart', galleryThumbsRebuildHandler,
					newWidth, newHeight);
			},
			close: function ()
			{
				window.Eresus.gallery.thumbsRebuild.dialog = null;
			}
		}).
		dialog('open');

	return false;
}


/**
 * Загружает в форму список групп
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
						window.Eresus.gallery.thumbsRebuild.dialog = true;
						jQuery('#settings').submit();
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
							window.Eresus.gallery.thumbsRebuild.dialog = true;
							jQuery('#settings').submit();
							return;
						}

						jQuery('#thumbsRebuildMessage').
							text('В процессе работы произошли ошибки. Возможно одна или несколько миниатюр не пересоздано.');
					}
					break;
			}
			break;

		default:
			alert('Не удалось получить ответ от сервера. Обновите страницу и попробуйте ещё раз.');
	}
}
