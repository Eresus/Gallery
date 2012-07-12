<?php
/**
 * Галерея изображений
 *
 * @version ${product.version}
 *
 * @copyright 2008, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Михаил Красильников <mk@3wstyle.ru>
 * @author Ghost
 * @author Olex
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
 * $Id$
 */

/**
 * Класс плагина
 *
 * @package Gallery
 */
class Gallery extends ContentPlugin
{
	/**
	 * Название плагина
	 *
	 * @var string
	 */
	public $title = 'Галерея изображений';

	/**
	 * Версия плагина
	 *
	 * @var string
	 */
	public $version = '${product.version}';

	/**
	 * Требуемая версия CMS
	 *
	 * @var string
	 */
	public $kernel = '3.00b';

	/**
	 * Описание плагина
	 *
	 * @var string
	 */
	public $description = 'Создание галерей изображений';

	/**
	 * Параметры плагина
	 *
	 * @var array
	 */
	public $settings = array(

		/* Свойства изображений */
		// Ширина изображения
		'imageWidth' => 800,
		// Высота изображения
		'imageHeight' => 600,

		/* Свойства миниатюр */
		// Ширина миниатюры
		'thumbWidth' => 120,
		// Высота миниатюры
		'thumbHeight' => 90,

		/* Список изображений */
		// Сортировка: date_asc, date_desc, manual
		'sort' => 'date_asc',
		// изображений на страницу
		'itemsPerPage' => 20,
		// Режим отображения изображений: normal, popup
		'showItemMode' => 'normal',

		/* Группы изображений */
		// Использовать группы
		'useGroups' => false,
		// Групп на страницу
		'groupsPerPage' => 10,

		// Накладывать логотип
		'logoEnable' => false,
		// Положение логотипа
		'logoPosition' => 'BR',
		// Вертикальный отступ
		'logoVPadding' => 10,
		// Горизонтальный отступ
		'logoHPadding' => 10,
	);

	/**
	 * Возвращает свойство $dirData
	 *
	 * @return string
	 *
	 * @since 2.00
	 */
	public function getDataDir()
	{
		return $this->dirData;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает свойство $dirCode
	 *
	 * @return string
	 *
	 * @since 2.03
	 */
	public function getCodeDir()
	{
		return $this->dirCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог настроек
	 *
	 * @return string  HTML
	 */
	public function settings()
	{
		Eresus_Kernel::app()->getPage()->linkStyles($this->urlCode . 'admin.css');
		Eresus_Kernel::app()->getPage()->linkScripts($this->urlCode . 'admin.js');

		// Данные для подстановки в шаблон
		$data = array();
		$data['this'] = $this;
		$data['page'] = Eresus_Kernel::app()->getPage();
		$data['logo_exists'] = file_exists($this->dirData . 'logo.png');

		$tmplDir = Eresus_CMS::getLegacyKernel()->froot . 'templates/' . $this->name;
		$this->settings['tmplImageList'] = file_get_contents($tmplDir . '/image-list.html');
		$this->settings['tmplImageGroupedList'] =
			file_get_contents($tmplDir . '/image-grouped-list.html');
		$this->settings['tmplImage'] = file_get_contents($tmplDir . '/image.html');
		$this->settings['tmplPopup'] = file_get_contents($tmplDir . '/popup.html');

		// Создаём экземпляр шаблона
		$form = new EresusForm('ext/' . $this->name . '/templates/settings.html', LOCALE_CHARSET);
		foreach ($data as $key => $value)
		{
			$form->setValue($key, $value);
		}

		// Компилируем шаблон и данные
		$html = $form->compile();

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обновление настроек
	 *
	 * Загрузка рамок
	 *
	 * @return void
	 */
	public function updateSettings()
	{
		/*
		 * Загрузка логотипа
		 */
		if (isset($_FILES['logoImage']['tmp_name']) &&
			is_uploaded_file($_FILES['logoImage']['tmp_name']))
		{
			if (substr($_FILES['logoImage']['name'], -3) == 'png')
			{
				upload('logoImage', $this->dirData .'logo.png');
			}
			else
			{
				ErrorMessage('Логотип должен быть в формате PNG');
			}
		}

		$tmplDir = Eresus_CMS::getLegacyKernel()->froot . 'templates/' . $this->name;
		@file_put_contents($tmplDir . '/image-list.html', arg('tmplImageList'));
		@file_put_contents($tmplDir . '/image-grouped-list.html', arg('tmplImageGroupedList'));
		@file_put_contents($tmplDir . '/image.html', arg('tmplImage'));
		@file_put_contents($tmplDir . '/popup.html', arg('tmplPopup'));

		parent::updateSettings();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Действия при установке
	 *
	 * Метод создаёт необходимые таблицы в БД и директорию данных.
	 *
	 * @throws EresusRuntimeException
	 *
	 * @return void
	 */
	public function install()
	{
		parent::install();

		$table = ORM::getTable($this, 'Image');
		$table->create();

		/*
		 * Создаём таблицу групп
		 */
		$sql = "
			`id` int(10) unsigned NOT NULL auto_increment COMMENT 'Идентификатор',
			`section` int(10) unsigned default NULL COMMENT 'Привязка к разделу сайта',
			`title` varchar(255) default '' COMMENT 'Название группы',
			`description` text default '' COMMENT 'Описание',
			`position` int(10) unsigned NOT NULL default '0' COMMENT 'Порядковый номер',
			PRIMARY KEY  (`id`),
			KEY `list` (`section`, `position`)
		";
		$this->dbCreateTable($sql, 'groups');

		// Создаём директорию данных
		$this->mkdir();

		$ts = TemplateService::getInstance();
		try
		{
			$ts->installTemplates($this->dirCode . 'distrib', $this->name);
		}
		catch (Exception $e)
		{
			$this->uninstall();
			throw new EresusRuntimeException('Fail to install templates',
				'Не удалось установить шаблоны плагина. Подробная информация доступна в журнале.', $e);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Действия при удалении плагина
	 *
	 * Метод удаляет файлы данных плагина.
	 *
	 * @throws EresusRuntimeException
	 *
	 * @return void
	 */
	public function uninstall()
	{
		// Удаляем директорию данных
		$this->rmdir();

		$ts = TemplateService::getInstance();
		try
		{
			$ts->uninstallTemplates($this->name);
		}
		catch (Exception $e)
		{
			throw new EresusRuntimeException('Fail to uninstall templates',
				'Не удалось удалить шаблоны плагина. Подробная информация доступна в журнале.', $e);
		}

		parent::uninstall();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Формирование HTML-кода АИ
	 *
	 * @return string  HTML
	 */
	public function adminRenderContent()
	{
		$result = '';
		/* @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		switch (true)
		{
			/*
			 * Управление группами
			 * Этот блок должен располагаться до блока управления изображениями из-за того что иначе
			 * наличие аргумента id может быть ошибочно расценено как запрос на диалог изменения
			 * изображения.
			 */

			case arg('action') == 'group':
				$result = $this->adminRenderGroupsList();
				break;

			case arg('group_id') !== null:
				$result = $this->adminEditGroupDialog();
				break;

			case arg('action') == 'group_create':
				$result = $this->adminAddGroupDialog();
				break;

			case arg('action') == 'group_insert':
				$this->adminInsertGroup();
				break;

			case arg('action') == 'group_update':
				$this->adminUpdateGroup();
				break;

			case arg('group_up') !== null:
				$this->adminMoveUpGroup();
				break;

			case arg('group_down') !== null:
				$this->adminMoveDownGroup();
				break;

			case arg('group_delete') !== null:
				$this->adminDeleteGroup();
				break;

			/* Управление изображениями */

			case arg('action') == 'create':
				$result = $this->adminAddItem();
				break;

			case arg('action') == 'insert':
				$this->adminInsertImage();
				break;

			case arg('id') !== null:
				$result = $this->adminEditItem();
				break;

			case arg('update') !== null:
				$this->adminUpdateImage();
				break;

			case arg('toggle') !== null:
				$this->adminImageToggle(arg('toggle', 'int'));
				break;

			case arg('cover') !== null:
				$this->coverAction();
				break;

			case arg('delete') !== null:
				$this->delete(arg('delete', 'int'));
				break;

			case arg('up') !== null:
				$this->up(arg('up', 'int'));
				break;

			case arg('down') !== null:
				$this->down(arg('down', 'int'));
				break;

			/* Управление свойствами галереи */

			case arg('action') == 'props':
				$result = $this->adminRenderProperties();
				break;

			case arg('action') == 'props_update':
				$this->updateProperties();
				break;

			/* Действие по умолчанию */

			default:
				$result = $this->adminRenderImagesList();
				break;
		}

		/*
		 * Кнопки-"вкладки"
		 */
		$tabs = array('width' => '14em', 'items' => array());
		$tabs['items'] []= array(
			'caption' => 'Изображения',
			'url' => preg_replace('/&(id|pg)=\d+/', '', $page->url(null, array('action'))),
		);

		if ($this->settings['useGroups'])
		{
			$tabs['items'] []= array(
				'caption' => 'Группы',
				'url' => preg_replace('/&(id|pg)=\d+/', '', $page->url(array('action' => 'group'))),
			);
		}

		$tabs['items'] []= array(
			'caption' => 'Свойства галереи',
			'url' => preg_replace('/&(id|pg)=\d+/', '', $page->url(array('action' => 'props'))),
		);

		$result = $page->renderTabs($tabs) . $result;

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработчик XHR-запросов
	 *
	 * @return void
	 */
	public function adminRender()
	{
		$ctl = new Gallery_AdminXHRController;
		$ctl->execute(arg('args'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Формирование контента
	 *
	 * @return string
	 */
	public function clientRenderContent()
	{
		$this->clientCheckRequest();

		/* @var TClientUI $page */
		$page = Eresus_Kernel::app()->getPage();

		if ($page->topic)
		{
			$result = $this->clientRenderItem();
		}
		else
		{
			$result = $this->clientRenderList();
		}

		return $result;

	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL корня клиентского раздела
	 *
	 * @return string
	 */
	public function clientURL()
	{
		return Eresus_Kernel::app()->getPage()->clientURL(Eresus_Kernel::app()->getPage()->id);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL текущей страницы списка
	 *
	 * @return string
	 */
	public function clientListURL()
	{
		$url = $this->clientURL();

		/* @var TClientUI $page */
		$page = Eresus_Kernel::app()->getPage();
		if ($page->subpage)
		{
			$url .= 'p' . $page->subpage . '/';
		}

		return $url;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подключает библиотеку jQuery
	 *
	 * @return void
	 */
	public function linkJQuery()
	{
		Eresus_Kernel::app()->getPage()->
			linkScripts(Eresus_CMS::getLegacyKernel()->root . 'core/jquery/jquery.min.js');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает интерфейс списка изображений
	 *
	 * @return string  HTML
	 */
	private function adminRenderImagesList()
	{
		// Определяем текущую страницу списка
		$pg = arg('pg') ? arg('pg', 'int') : 1;

		$section = arg('section', 'int');

		$maxCount = $this->settings['useGroups'] ?
			$this->settings['groupsPerPage'] :
			$this->settings['itemsPerPage'];

		$startFrom = ($pg - 1) * $maxCount;

		if ($this->settings['useGroups'])
		{
			$items = Gallery_Group::find($section, $maxCount, $startFrom);
		}
		else
		{
			$items = Gallery_Image::find($section, $maxCount, $startFrom);
		}

		// Данные для подстановки в шаблон
		$data = array();
		$data['this'] = $this;
		$data['page'] = Eresus_Kernel::app()->getPage();
		$data['Eresus'] = $GLOBALS['Eresus'];
		$data['sectionId'] = arg('section', 'int');
		$data['items'] = $items;

		/* Шаблоны адресов действий */
		$data['urlEdit'] = str_replace('&', '&amp;',
			Eresus_Kernel::app()->getPage()->url(array('id' => '%s')));
		$data['urlToggle'] = str_replace('&', '&amp;',
			Eresus_Kernel::app()->getPage()->url(array('toggle' => '%s')));
		$data['urlCover'] = str_replace('&', '&amp;',
			Eresus_Kernel::app()->getPage()->url(array('cover' => '%s')));
		$data['urlUp'] = str_replace('&', '&amp;',
			Eresus_Kernel::app()->getPage()->url(array('up' => '%s')));
		$data['urlDown'] = str_replace('&', '&amp;',
			Eresus_Kernel::app()->getPage()->url(array('down' => '%s')));
		$data['urlDelete'] = str_replace('&', '&amp;',
			Eresus_Kernel::app()->getPage()->url(array('delete' => '%s')));

		if ($this->settings['useGroups'])
		{
			$totalPages = ceil(Gallery_Group::count($section) / $maxCount);
		}
		else
		{
			$totalPages = ceil(Gallery_Image::count($section) / $maxCount);
		}

		if ($totalPages > 1)
		{
			$pager = new PaginationHelper($totalPages, $pg,
				Eresus_Kernel::app()->getPage()->url(array('pg' => '%s')));
			$data['pager'] = $pager->render();
		}
		else
		{
			$data['pager'] = '';
		}

		Eresus_Kernel::app()->getPage()->linkStyles($this->urlCode . 'admin.css');
		Eresus_Kernel::app()->getPage()->linkScripts($this->urlCode . 'admin.js');

		/* Создаём экземпляр шаблона */
		if ($this->settings['useGroups'])
		{
			$tmpl = new Template('ext/' . $this->name . '/templates/image-grouped-list.html');
			// Изображения вне групп
			$data['orphans'] = Gallery_Image::findOrphans($section);
		}
		else
		{
			$tmpl = new Template('ext/' . $this->name . '/templates/image-list.html');
		}

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог добавления изображения
	 *
	 * @return string  HTML
	 */
	private function adminAddItem()
	{
		$this->linkJQuery();
		Eresus_Kernel::app()->getPage()->linkStyles($this->urlCode . 'admin.css');

		// Данные для подстановки в шаблон
		$data = array();
		$data['this'] = $this;
		$data['page'] = Eresus_Kernel::app()->getPage();
		$data['sectionId'] = arg('section', 'int');
		$data['defaultGroup'] = isset($_SESSION['gallery_default_group']) ?
			$_SESSION['gallery_default_group'] : null;

		if ($this->settings['useGroups'])
		{
			$data['groups'] = $this->dbSelect('groups', "`section` = " . arg('section', 'int'),
				'position');
		}

		// Создаём экземпляр шаблона
		$tmpl = new Template('ext/' . $this->name . '/templates/add-image.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог изменения изображения
	 *
	 * @return string  HTML
	 */
	private function adminEditItem()
	{
		$image = new Gallery_Image(arg('id', 'int'));

		Eresus_Kernel::app()->getPage()->linkStyles($this->urlCode . 'admin.css');
		Eresus_Kernel::app()->getPage()->linkScripts($this->urlCode . 'admin.js');

		// Данные для подстановки в шаблон
		$data = array();
		$data['this'] = $this;
		$data['page'] = Eresus_Kernel::app()->getPage();
		$data['pg'] = arg('pg', 'int');
		$data['image'] = $image;
		$data['sections'] = $this->buildGalleryList(0);

		if ($this->settings['useGroups'])
		{
			$data['groups'] = $this->dbSelect('groups', "`section` = {$image->section}", 'position');
		}

		// Создаём экземпляр шаблона
		$tmpl = new Template('ext/' . $this->name . '/templates/edit-image.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет изображение
	 *
	 * @throws DomainException
	 *
	 * @return void
	 */
	private function adminInsertImage()
	{
		if (empty($_FILES['image']['name']))
		{
			ErrorMessage(isset($form['message']) ? $form['message'] : 'Поле "файл" не заполнено');
			HTTP::goback();
		}

		$item = new Gallery_Image();
		$item->section = arg('section');
		$item->group = arg('group');
		$_SESSION['gallery_default_group'] = arg('group');
		$item->title = arg('title');
		$item->cover = arg('cover');
		$item->active = arg('active');
		$item->posted = gettime();
		$item->image = 'image'; // $_FILES['image'];

		try
		{
			$item->save();
		}
		catch (Gallery_Exception_FileTooBigException $e)
		{
			throw new DomainException('Размер загружаемого файла превышает максимально допустимый');
		}

		$url = 'admin.php?mod=content&section=' . $item->section;
		if (arg('pg'))
		{
			$url .= '&pg=' . arg('pg', 'int');
		}
		HTTP::redirect($url);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обновляет изображение
	 *
	 * @return void
	 * @see adminEditItem
	 */
	private function adminUpdateImage()
	{
		$image = new Gallery_Image(arg('update', 'int'));

		$new_section = arg('section', 'int');
		if ($new_section == $image->section)
		{
			$image->group = arg('group', 'int');
		}
		else
		{
			$image->section = $new_section;
			$image->group = arg('new_group', 'int');
		}
		$image->title = arg('title', 'dbsafe');
		$image->posted = arg('posted', 'dbsafe');
		$image->cover = arg('cover', 'int');
		$image->active = arg('active', 'int');
		$image->image = 'image'; // $_FILES['image'];

		$image->save();

		HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает интерфейс списка групп
	 *
	 * @return string  HTML
	 */
	private function adminRenderGroupsList()
	{
		/*
		 * Описание таблицы групп
		 */
		$table = array(
			'name' => $this->__table('groups'),
			'key'=> 'id',
			'sortMode' => 'position',
			'sortDesc' => false,
			'columns' => array(
				array('name' => 'title', 'caption' => 'Название'),
			),
			'condition' => "`section`='" . arg('section', 'int') . "'",
			'controls' => array(
				'delete' => '',
				'edit' => '',
				'position' => '',
			),
			'tabs' => array(
				'width' => '180px',
				'items' => array(
					array('caption' => 'Добавить группу', 'name' => 'action', 'value' => 'group_create'),
				),
			),
		);

		/* @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderTable($table, null, 'group_');
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог добавления группы
	 *
	 * @return string  HTML
	 */
	private function adminAddGroupDialog()
	{
		$form = array(
			'name' => 'AddForm',
			'caption' => 'Добавление группы',
			'width' => '600px',
			'fields' => array (
				array ('type' => 'hidden', 'name' => 'action', 'value' => 'group_insert'),
				array ('type' => 'hidden', 'name' => 'section', 'value' => arg('section', 'int')),
				array ('type' => 'edit', 'name' => 'title', 'label' => 'Название',
					'width' => '100%', 'maxlength' => '255'),
				array('type' => 'html', 'name' => 'description', 'height' => '400px',
					'label' => 'Описание'),
			),
			'buttons' => array('ok', 'cancel'),
		);

		/* @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderForm($form);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог изменения группы
	 *
	 * @return string  HTML
	 */
	private function adminEditGroupDialog()
	{
		$item = $this->dbItem('groups', arg('group_id', 'int'));

		$form = array(
			'name' => 'editGroup',
			'caption' => 'Изменение группы',
			'width' => '600px',
			'fields' => array (
				array ('type' => 'hidden', 'name' => 'action', 'value' => 'group_update'),
				array ('type' => 'hidden', 'name' => 'section', 'value' => arg('section', 'int')),
				array ('type' => 'hidden', 'name' => 'id', 'value' => $item['id']),
				array ('type' => 'edit', 'name' => 'title', 'label' => 'Название',
					'width' => '100%', 'maxlength' => '255'),
				array('type' => 'html', 'name' => 'description', 'height' => '400px',
					'label' => 'Описание'),
			),
			'buttons' => array('ok', 'apply', 'cancel'),
		);

		/* @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderForm($form, $item);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет группу
	 *
	 * @return void
	 */
	private function adminInsertGroup()
	{
		$item = new Gallery_Group();
		$item->section = arg('section');
		$item->title = arg('title');
		$item->description = arg('description');

		// Код определения позиции желательно перенести в Gallery_Group
		$maxPosition = $this->dbSelect('groups', "`section` = '{$item->section}'", null,
			'MAX(`position`) AS `value`');
		$item->position = intval($maxPosition[0]['value']) + 1;

		$item->save();

		HTTP::redirect(arg('submitURL') . '&action=group');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обновляет группу
	 *
	 * @return void
	 */
	private function adminUpdateGroup()
	{
		$item = $this->dbItem('groups', arg('id', 'int'));

		$item['title'] = arg('title', 'dbsafe');
		$item['description'] = arg('description', 'dbsafe');

		$this->dbUpdate('groups', $item);

		$url = arg('submitURL');
		if (strpos($url, '=group') === false)
		{
			$url .= '&action=group';
		}

		HTTP::redirect($url);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещает группу вверх по списку
	 *
	 * @return void
	 */
	private function adminMoveUpGroup()
	{
		$group = new Gallery_Group(arg('group_up', 'int'));
		$group->moveUp();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещает группу вниз по списку
	 *
	 * @return void
	 */
	private function adminMoveDownGroup()
	{
		$group = new Gallery_Group(arg('group_down', 'int'));
		$group->moveDown();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаляет группу
	 *
	 * @return void
	 */
	private function adminDeleteGroup()
	{
		$id = arg('group_delete', 'int');
		$group = new Gallery_Group($id);
		$group->delete();

		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обновление свойств галереи
	 */
	private function updateProperties()
	{
		$id = arg('section', 'int');
		$item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', '`id` = "'.$id.'"');

		$item['title'] = arg('title', 'dbsafe');
		$item['created'] = arg('created', 'dbsafe');
		$item['active'] = arg('active', 'int');
		$item['content'] = arg('content', 'dbsafe');

		Eresus_CMS::getLegacyKernel()->db->updateItem('pages', $item, "`id` = '".$id."'");
		HTTP::redirect(arg('submitURL'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаление записи
	 *
	 * @param int $id Номер записи
	 */
	private function delete($id)
	{
		$image = new Gallery_Image($id);
		$image->delete();

		HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет правильность запроса
	 *
	 * В случае если запрос содержит лишние элементы, возвращает HTTP/404
	 *
	 * @return void
	 */
	private function clientCheckRequest()
	{
		/* @var TClientUI $page */
		$page = Eresus_Kernel::app()->getPage();
		/* Собираем вместе ожидаемые элементы URL */
		$acceptUrl = Eresus_CMS::getLegacyKernel()->request['path'] .
			($page->subpage !== 0 ? 'p' . $page->subpage . '/' : '');

		if ($page->topic)
		{
			$acceptUrl .= $page->topic !== false ? $page->topic . '/' : '';
		}

		/* Сравниваем с переданным URL */
		if ($acceptUrl != Eresus_CMS::getLegacyKernel()->request['url'])
		{
			$page->httpError(404);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает контент для страницы списка изображений
	 *
	 * @return string  HTML
	 */
	private function clientRenderList()
	{
		if ($this->settings['useGroups'])
		{
			$view = new Gallery_ClientGroupedListView();
		}
		else
		{
			$view = new Gallery_ClientListView();
		}

		$html = $view->render();
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка представления "Просмотр изображения"
	 *
	 * @return string  HTML
	 */
	private function clientRenderItem()
	{
		/* @var TClientUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$id = intval($page->topic);

		if ($id != $page->topic)
		{
			$page->httpError(404);
		}

		$image = new Gallery_Image($id);

		if (!$image || !$image->active)
		{
			$page->HttpError(404);
		}

		// Данные для подстановки в шаблон
		$data = array();
		$data['this'] = $this;
		$data['page'] = $page;
		$data['Eresus'] = $GLOBALS['Eresus'];
		$data['image'] = $image;
		if ($this->settings['useGroups'])
		{
			$data['album'] = new Gallery_AlbumGrouped($page->id);
		}
		else
		{
			$data['album'] = new Gallery_Album($page->id);
		}
		$data['album']->setCurrent($data['image']);

		// Создаём экземпляр шаблона
		$tmpl = new Template('templates/' . $this->name . '/image.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создаёт список разделов типа "Галерея изображений" для использования в шаблонах
	 *
	 * @param int $root             Идентификатор корневого раздела
	 * @param int $level[optional]  Уровень вложенности ветки
	 *
	 * @return array  Описания разделов
	 */
	private function buildGalleryList($root, $level = 0)
	{
		useLib('sections');
		$lib = new Sections();

		$sections = $lib->children($root);

		// Описания разделов выбранной ветки
		$branch = array();

		/*
		 * Отбираем только разделы типа "Галерея изображений" или разделы,
		 * содержащие дочерние разделы этого же типа.
		 */
		foreach ($sections as $section)
		{
			$item = array(
				'id' => $section['id'],
				'selectable' => false,
				'caption' => $section['caption'],
				'level' => $level
			);

			if ($section['type'] == $this->name)
			{
				$item['selectable'] = true;
			}

			$subItems = $this->buildGalleryList($section['id'], $level + 1);

			if ($item['selectable'] || $subItems)
			{
				$branch []= $item;
			}

			$branch = array_merge($branch, $subItems);
		}

		return $branch;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог свойств галереи
	 *
	 * @return string  HTML
	 */
	private function adminRenderProperties()
	{
		$item = Eresus_CMS::getLegacyKernel()->db->
			selectItem('pages', '`id`="' . arg('section', 'int') . '"');

		$form = array(
			'name' => 'contentEditor',
			'caption' => 'Текст на странице',
			'width' => '800px',
			'fields' => array(
				array('type' => 'hidden', 'name' => 'action', 'value' => 'props_update'),
				array('type' => 'hidden', 'name' => 'textupdate', 'value' => '1'),
				array('type' => 'edit', 'name' => 'title', 'label' => 'Название', 'width' => '150px'),
				array('type' => 'edit', 'name' => 'created', 'label' => 'Дата создания',
					'width' => '150px', 'comment' => 'ГГГГ-ММ-ДД ЧЧ:ММ:СС'),
				array('type' => 'checkbox', 'name' => 'active', 'label' => 'Активна'),
				array('type' => 'html', 'name' => 'content', 'height' => '400px',
					'label' => 'Описание<div class="ui-state-warning"><em>Внимание!</em> Для того, чтобы ' .
					'это описание показывалось посетителям, надо добавить в ' .
					'<a href="admin.php?mod=plgmgr&id=gallery">шаблон</a> вывод текста страницы (альбома).' .
					'</div>'),
			),
			'buttons'=> array('ok', 'apply', 'cancel'),
		);

		/* @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderForm($form, $item);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещение изображения вверх по списку
	 *
	 * @param int $id  Идентификатор изображения
	 *
	 * @return void
	 */
	private function up($id)
	{
		$image = new Gallery_Image($id);
		$image->moveUp();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещение изображения вниз по списку
	 *
	 * @param int $id  Идентификатор изображения
	 *
	 * @return void
	 */
	private function down($id)
	{
		$image = new Gallery_Image($id);
		$image->moveDown();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Включает или отключает изображение
	 *
	 * @param int $id  Идентификатор изображения
	 */
	private function adminImageToggle($id)
	{
		$image = new Gallery_Image($id);
		$image->active = ! $image->active;
		$image->save();

		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Делает указанное в запросе Изображение обложкой альбома
	 *
	 * @return void
	 */
	private function coverAction()
	{
		$id = arg('cover', 'int');

		$image = new Gallery_Image($id);
		$image->cover = true;
		$image->save();

		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

}
