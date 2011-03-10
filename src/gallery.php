<?php
/**
 * ������� �����������
 *
 * ������ ��������� ����������� �� ����� ��������.
 *
 * @version ${product.version}
 *
 * @copyright 2008, ��� "��� �����", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author ������ ������������ <mk@3wstyle.ru>
 * @author Ghost
 * @author Olex
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
 * $Id$
 */

/**
 * ����� �������
 *
 * @package Gallery
 */
class Gallery extends ContentPlugin
{
	/**
	 * �������� �������
	 *
	 * @var string
	 */
	public $title = '������� �����������';

	/**
	 * ������ �������
	 *
	 * @var string
	 */
	public $version = '${product.version}';


	/**
	 * ��������� ������ CMS
	 *
	 * @var string
	 */
	public $kernel = '2.14';

	/**
	 * �������� �������
	 *
	 * @var string
	 */
	public $description = '�������� ������� �����������';

	/**
	 * ��������� �������
	 *
	 * @var array
	 */
	public $settings = array(

		/* �������� ����������� */
		// ������ �����������
		'imageWidth' => 800,
		// ������ �����������
		'imageHeight' => 600,

		/* �������� �������� */
		// ������ ���������
		'thumbWidth' => 120,
		// ������ ���������
		'thumbHeight' => 90,

		/* ������ ����������� */
		// ����������: date_asc, date_desc, manual
		'sort' => 'date_asc',
		// ����������� �� ��������
		'itemsPerPage' => 20,
		// ����� ����������� �����������: normal, popup
		'showItemMode' => 'normal',

		/* ������ ����������� */
		// ������������ ������
		'useGroups' => false,
		// ����� �� ��������
		'groupsPerPage' => 10,

		// ����������� �������
		'logoEnable' => false,
		// ��������� ��������
		'logoPosition' => 'BR',
		// ������������ ������
		'logoVPadding' => 10,
		// �������������� ������
		'logoHPadding' => 10,
	);

	public function __construct()
	{
		parent::__construct();
		EresusClassAutoloader::add($this->dirCode . 'gallery.autoload.php');
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� �������� $urlData
	 *
	 * @return string
	 *
	 * @since 2.00
	 */
	public function getDataURL()
	{
		return $this->urlData;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� �������� $dirData
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
	 * ���������� �������� $urlCode
	 *
	 * @return string
	 *
	 * @since 2.00
	 */
	public function getCodeURL()
	{
		return $this->urlCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ��������
	 *
	 * @return string  HTML
	 */
	public function settings()
	{
		global $Eresus, $page;

		$page->linkStyles($this->urlCode . 'admin.css');
		$page->linkScripts($this->urlCode . 'admin.js');

		// ������ ��� ����������� � ������
		$data = array();
		$data['this'] = $this;
		$data['page'] = $page;
		$data['logo_exists'] = file_exists($this->dirData . 'logo.png');

		$tmplDir = $Eresus->froot . 'templates/' . $this->name;
		$this->settings['tmplImageList'] = file_get_contents($tmplDir . '/image-list.html');
		$this->settings['tmplImageGroupedList'] =
			file_get_contents($tmplDir . '/image-grouped-list.html');
		$this->settings['tmplImage'] = file_get_contents($tmplDir . '/image.html');
		$this->settings['tmplPopup'] = file_get_contents($tmplDir . '/popup.html');

		// ������ ��������� �������
		$form = new EresusForm('ext/' . $this->name . '/templates/settings.html', LOCALE_CHARSET);
		foreach ($data as $key => $value)
		{
			$form->setValue($key, $value);
		}

		// ����������� ������ � ������
		$html = $form->compile();

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ��������
	 *
	 * �������� �����
	 *
	 * @return void
	 */
	public function updateSettings()
	{
		global $Eresus;

		/*
		 * �������� ��������
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
				ErrorMessage('������� ������ ���� � ������� PNG');
			}
		}

		$tmplDir = $Eresus->froot . 'templates/' . $this->name;
		@file_put_contents($tmplDir . '/image-list.html', arg('tmplImageList'));
		@file_put_contents($tmplDir . '/image-grouped-list.html', arg('tmplImageGroupedList'));
		@file_put_contents($tmplDir . '/image.html', arg('tmplImage'));
		@file_put_contents($tmplDir . '/popup.html', arg('tmplPopup'));

		parent::updateSettings();
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������� ��� ���������
	 *
	 * ����� ������ ����������� ������� � �� � ���������� ������.
	 *
	 * @return void
	 */
	public function install()
	{
		global $Eresus;

		parent::install();

		/*
		 * ������ ������� �����������
		 */
		$sql = "
			`id` int(10) unsigned NOT NULL auto_increment COMMENT '������������',
			`section` int(10) unsigned default NULL COMMENT '�������� � ������� �����',
			`title` varchar(255) default NULL COMMENT '�������� ��������',
			`image` varchar(128) default NULL COMMENT '��� ����� �������� ��������',
			`thumb` varchar(128) default NULL COMMENT '��� ����� ���������',
			`posted` datetime default NULL COMMENT '���� � ����� ����������',
			`groupId` int(10) unsigned NOT NULL default '0' COMMENT '�������� � ������',
			`cover` tinyint(1) unsigned default '0' COMMENT '������� �������',
			`active` tinyint(1) unsigned default '0' COMMENT '����������',
			`position` int(10) unsigned NOT NULL default '0' COMMENT '���������� �����',
			PRIMARY KEY  (`id`),
			KEY `section` (`section`),
			KEY `position` (`position`),
			KEY `active` (`active`),
			KEY `posted` (`posted`),
			KEY `findCovers` (`section`, `active`, `cover`),
			KEY `imagesByTime` (`section`, `active`, `posted`),
			KEY `imagesByPosition` (`section`, `active`, `position`)
			";
		$this->dbCreateTable($sql, 'images');

		/*
		 * ������ ������� �����
		 */
		$sql = "
			`id` int(10) unsigned NOT NULL auto_increment COMMENT '������������',
			`section` int(10) unsigned default NULL COMMENT '�������� � ������� �����',
			`title` varchar(255) default '' COMMENT '�������� ������',
			`description` text default '' COMMENT '��������',
			`position` int(10) unsigned NOT NULL default '0' COMMENT '���������� �����',
			PRIMARY KEY  (`id`),
			KEY `list` (`section`, `position`)
		";
		$this->dbCreateTable($sql, 'groups');

		// ������ ���������� ������
		$this->mkdir();

		/* ������ ���������� �������� */
		$tmplDir = $Eresus->froot . 'templates/' . $this->name;
		$umask = umask(0000);
		@mkdir($tmplDir, 0777);
		umask($umask);

		/* �������� ������� */
		$files = glob($this->dirCode . 'distrib/*.html');
		foreach ($files as $file)
		{
			$target = $tmplDir . '/' . basename($file);
			copy($file, $target);
			chmod($target, 0666);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������� ��� �������� �������
	 *
	 * ����� �������� ����� ������ �������.
	 *
	 * @return void
	 */
	public function uninstall()
	{
		global $Eresus;

		// ������� ���������� ������
		$this->rmdir();

		/* ������� ������� */
		$tmplDir = $Eresus->froot . 'templates/' . $this->name;
		$files = glob($tmplDir . '/*');
		foreach ($files as $file)
		{
			filedelete($file);
		}

		/* ������� ���������� �������� */
		@rmdir($tmplDir);

		parent::uninstall();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������������ HTML-���� ��
	 *
	 * @return string  HTML
	 */
	public function adminRenderContent()
	{
		global $page;

		switch (true)
		{
			/*
			 * ���������� ��������
			 * ���� ���� ������ ������������� �� ����� ���������� ������������� ��-�� ���� ��� �����
			 * ������� ��������� id ����� ���� �������� ��������� ��� ������ �� ������ ���������
			 * �����������.
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

			/* ���������� ������������� */

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

			/* ���������� ���������� ������� */

			case arg('action') == 'props':
				$result = $this->adminRenderProperties();
			break;

			case arg('action') == 'props_update':
				$result = $this->updateProperties();
			break;

			/* �������� �� ��������� */

			default:
				$result = $this->adminRenderImagesList();
			break;
		}

		/*
		 * ������-"�������"
		 */
		$tabs = array('width' => '14em', 'items' => array());
		$tabs['items'] []= array(
			'caption' => '�����������',
			'url' => preg_replace('/&(id|pg)=\d+/', '', $page->url(null, array('action'))),
		);

		if ($this->settings['useGroups'])
		{
			$tabs['items'] []= array(
				'caption' => '������',
				'url' => preg_replace('/&(id|pg)=\d+/', '', $page->url(array('action' => 'group'))),
			);
		}

		$tabs['items'] []= array(
			'caption' => '�������� �������',
			'url' => preg_replace('/&(id|pg)=\d+/', '', $page->url(array('action' => 'props'))),
		);

		$result =
			$page->renderTabs($tabs) .
			$result;

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� XHR-��������
	 *
	 * @return void
	 */
	public function adminRender()
	{
		$ctl = new GalleryAdminXHRController;
		$ctl->execute(arg('args'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������������ ��������
	 *
	 * @return string
	 */
	public function clientRenderContent()
	{
		global $page;

		$this->clientCheckRequest();

		$result = '';

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
	 * ���������� URL ����� ����������� �������
	 *
	 * @return string
	 */
	public function clientURL()
	{
		global $page;

		return $page->clientURL($page->id);
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� URL ������� �������� ������
	 *
	 * @return string
	 */
	public function clientListURL()
	{
		global $page;

		$url = $this->clientURL();

		if ($page->subpage)
		{
			$url .= 'p' . $page->subpage . '/';
		}

		return $url;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ���������� jQuery
	 *
	 * @return void
	 */
	public function linkJQuery()
	{
		global $Eresus, $page;

		$page->linkScripts($Eresus->root . 'core/jquery/jquery.min.js');
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������������ ��������� ������ �����������
	 *
	 * @return string  HTML
	 */
	private function adminRenderImagesList()
	{
		global $page;

		// ���������� ������� �������� ������
		$pg = arg('pg') ? arg('pg', 'int') : 1;

		$section = arg('section', 'int');

		$maxCount = $this->settings['useGroups'] ?
			$this->settings['groupsPerPage'] :
			$this->settings['itemsPerPage'];

		$startFrom = ($pg - 1) * $maxCount;

		if ($this->settings['useGroups'])
		{
			$items = GalleryGroup::find($section, $maxCount, $startFrom);
		}
		else
		{
			$items = GalleryImage::find($section, $maxCount, $startFrom);
		}

		// ������ ��� ����������� � ������
		$data = array();
		$data['this'] = $this;
		$data['page'] = $page;
		$data['Eresus'] = $GLOBALS['Eresus'];
		$data['sectionId'] = arg('section', 'int');
		$data['items'] = $items;

		/* ������� ������� �������� */
		$data['urlEdit'] = str_replace('&', '&amp;', $page->url(array('id' => '%s')));
		$data['urlToggle'] = str_replace('&', '&amp;', $page->url(array('toggle' => '%s')));
		$data['urlCover'] = str_replace('&', '&amp;', $page->url(array('cover' => '%s')));
		$data['urlUp'] = str_replace('&', '&amp;', $page->url(array('up' => '%s')));
		$data['urlDown'] = str_replace('&', '&amp;', $page->url(array('down' => '%s')));
		$data['urlDelete'] = str_replace('&', '&amp;', $page->url(array('delete' => '%s')));

		if ($this->settings['useGroups'])
		{
			$totalPages = ceil(GalleryGroup::count($section) / $maxCount);
		}
		else
		{
			$totalPages = ceil(GalleryImage::count($section) / $maxCount);
		}

		if ($totalPages > 1)
		{
			$pager = new PaginationHelper($totalPages, $pg, $page->url(array('pg' => '%s')));
			$data['pager'] = $pager->render();
		}
		else
		{
			$data['pager'] = '';
		}

		$page->linkStyles($this->urlCode . 'admin.css');
		$page->linkScripts($this->urlCode . 'admin.js');

		/* ������ ��������� ������� */
		if ($this->settings['useGroups'])
		{
			$tmpl = new Template('ext/' . $this->name . '/templates/image-grouped-list.html');
			// ����������� ��� �����
			$data['orphans'] = GalleryImage::findOrphans($section);
		}
		else
		{
			$tmpl = new Template('ext/' . $this->name . '/templates/image-list.html');
		}

		// ����������� ������ � ������
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ���������� �����������
	 *
	 * @return string  HTML
	 */
	private function adminAddItem()
	{
		global $page;

		$this->linkJQuery();
		$page->linkStyles($this->urlCode . 'admin.css');

		// ������ ��� ����������� � ������
		$data = array();
		$data['this'] = $this;
		$data['page'] = $page;
		$data['sectionId'] = arg('section', 'int');
		$data['defaultGroup'] = isset($_SESSION['gallery_default_group']) ?
			$_SESSION['gallery_default_group'] : null;

		if ($this->settings['useGroups'])
		{
			$data['groups'] = $this->dbSelect('groups', "`section` = " . arg('section', 'int'),
				'position');
		}

		// ������ ��������� �������
		$tmpl = new Template('ext/' . $this->name . '/templates/add-image.html');

		// ����������� ������ � ������
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ��������� �����������
	 *
	 * @return string  HTML
	 */
	private function adminEditItem()
	{
		global $page;

		$image = new GalleryImage(arg('id', 'int'));

		$page->linkStyles($this->urlCode . 'admin.css');
		$page->linkScripts($this->urlCode . 'admin.js');

		// ������ ��� ����������� � ������
		$data = array();
		$data['this'] = $this;
		$data['page'] = $page;
		$data['pg'] = arg('pg', 'int');
		$data['image'] = $image;
		$data['sections'] = $this->buildGalleryList(0);

		if ($this->settings['useGroups'])
		{
			$data['groups'] = $this->dbSelect('groups', "`section` = {$image->section}", 'position');
		}

		// ������ ��������� �������
		$tmpl = new Template('ext/' . $this->name . '/templates/edit-image.html');

		// ����������� ������ � ������
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� �����������
	 *
	 * @return void
	 */
	private function adminInsertImage()
	{
		if (empty($_FILES['image']['name']))
		{
			ErrorMessage(isset($form['message']) ? $form['message'] : '���� "����" �� ���������');
			HTTP::goback();
		}

		$item = new GalleryImage();
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
		catch (GalleryFileTooBigException $e)
		{
			throw new DomainException('������ ������������ ����� ��������� ����������� ����������');
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
	 * ��������� �����������
	 *
	 * @return void
	 * @see adminEditItem
	 */
	private function adminUpdateImage()
	{
		global $page;

		$image = new GalleryImage(arg('update', 'int'));

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

		HTTP::redirect($page->url());
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������������ ��������� ������ �����
	 *
	 * @return string  HTML
	 */
	private function adminRenderGroupsList()
	{
		global $page;

		/*
		 * �������� ������� �����
		 */
		$table = array(
			'name' => $this->__table('groups'),
			'key'=> 'id',
			'sortMode' => 'position',
			'sortDesc' => false,
			'columns' => array(
				array('name' => 'title', 'caption' => '��������'),
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
					array('caption' => '�������� ������', 'name' => 'action', 'value' => 'group_create'),
				),
			),
		);

		$result = $page->renderTable($table, null, 'group_');
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ���������� ������
	 *
	 * @return string  HTML
	 */
	private function adminAddGroupDialog()
	{
		global $page;

		$form = array(
			'name' => 'AddForm',
			'caption' => '���������� ������',
			'width' => '600px',
			'fields' => array (
				array ('type' => 'hidden', 'name' => 'action', 'value' => 'group_insert'),
				array ('type' => 'hidden', 'name' => 'section', 'value' => arg('section', 'int')),
				array ('type' => 'edit', 'name' => 'title', 'label' => '��������',
					'width' => '100%', 'maxlength' => '255'),
				array('type' => 'html', 'name' => 'description', 'height' => '400px',
					'label' => '��������'),
			),
			'buttons' => array('ok', 'cancel'),
		);

		$result = $page->renderForm($form);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ��������� ������
	 *
	 * @return string  HTML
	 */
	private function adminEditGroupDialog()
	{
		global $page;

		$item = $this->dbItem('groups', arg('group_id', 'int'));

		$form = array(
			'name' => 'editGroup',
			'caption' => '��������� ������',
			'width' => '600px',
			'fields' => array (
				array ('type' => 'hidden', 'name' => 'action', 'value' => 'group_update'),
				array ('type' => 'hidden', 'name' => 'section', 'value' => arg('section', 'int')),
				array ('type' => 'hidden', 'name' => 'id', 'value' => $item['id']),
				array ('type' => 'edit', 'name' => 'title', 'label' => '��������',
					'width' => '100%', 'maxlength' => '255'),
				array('type' => 'html', 'name' => 'description', 'height' => '400px',
					'label' => '��������'),
			),
			'buttons' => array('ok', 'apply', 'cancel'),
		);

		$result = $page->renderForm($form, $item);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������
	 *
	 * @return void
	 */
	private function adminInsertGroup()
	{
		$item = new GalleryGroup();
		$item->section = arg('section');
		$item->title = arg('title');
		$item->description = arg('description');

		// ��� ����������� ������� ���������� ��������� � GalleryGroup
		$maxPosition = $this->dbSelect('groups', "`section` = '{$item->section}'", null,
			'MAX(`position`) AS `value`');
		$item->position = intval($maxPosition[0]['value']) + 1;

		$item->save();

		HTTP::redirect(arg('submitURL') . '&action=group');
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������
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
	 * ���������� ������ ����� �� ������
	 *
	 * @return void
	 */
	private function adminMoveUpGroup()
	{
		$group = new GalleryGroup(arg('group_up', 'int'));
		$group->moveUp();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ���� �� ������
	 *
	 * @return void
	 */
	private function adminMoveDownGroup()
	{
		$group = new GalleryGroup(arg('group_down', 'int'));
		$group->moveDown();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������� ������
	 *
	 * @return void
	 */
	private function adminDeleteGroup()
	{
		$id = arg('group_delete', 'int');
		$group = new GalleryGroup($id);
		$group->delete();

		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������� �������
	 */
	private function updateProperties()
	{
		global $Eresus;

		$id = arg('section', 'int');
		$item = $Eresus->db->selectItem('pages', '`id` = "'.$id.'"');
		$item = array();

		$item['title'] = arg('title', 'dbsafe');
		$item['created'] = arg('created', 'dbsafe');
		$item['active'] = arg('active', 'int');
		$item['content'] = arg('content', 'dbsafe');

		$Eresus->db->updateItem('pages', $item, "`id` = '".$id."'");
		HTTP::redirect(arg('submitURL'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������� ������
	 *
	 * @param int id ����� ������
	 */
	private function delete($id)
	{
		global $page;

		$image = new GalleryImage($id);
		$image->delete();

		HTTP::redirect($page->url());
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������������ �������
	 *
	 * � ������ ���� ������ �������� ������ ��������, ��������� HTTP/404
	 *
	 * @return void
	 */
	private function clientCheckRequest()
	{
		global $Eresus, $page;

		/* �������� ������ ��������� ��������� URL */
		$acceptUrl = $Eresus->request['path'] .
			($page->subpage !== 0 ? 'p' . $page->subpage . '/' : '');

		if ($page->topic)
		{
			$acceptUrl .= $page->topic !== false ? $page->topic . '/' : '';
		}

		/* ���������� � ���������� URL */
		if ($acceptUrl != $Eresus->request['url'])
		{
			$page->httpError(404);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������� ��� �������� ������ �����������
	 *
	 * @return string  HTML
	 */
	private function clientRenderList()
	{
		if ($this->settings['useGroups'])
		{
			$view = new GalleryClientGroupedListView();
		}
		else
		{
			$view = new GalleryClientListView();
		}

		$html = $view->render();
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������������� "�������� �����������"
	 *
	 * @return string  HTML
	 */
	private function clientRenderItem()
	{
		global $page;

		$id = intval($page->topic);

		if ($id != $page->topic)
		{
			$page->httpError(404);
		}

		$image = new GalleryImage($id);

		if (!$image || !$image->active)
		{
			$page->HttpError(404);
		}

		// ������ ��� ����������� � ������
		$data = array();
		$data['this'] = $this;
		$data['page'] = $page;
		$data['Eresus'] = $GLOBALS['Eresus'];
		$data['image'] = $image;
		$data['images'] = GalleryImage::find($page->id, null, null, true);

		// ������ ��������� �������
		$tmpl = new Template('templates/' . $this->name . '/image.html');

		// ����������� ������ � ������
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������ ������ �������� ���� "������� �����������" ��� ������������� � ��������
	 *
	 * @param int $root             ������������� ��������� �������
	 * @param int $level[optional]  ������� ����������� �����
	 *
	 * @return array  �������� ��������
	 */
	private function buildGalleryList($root, $level = 0)
	{
		useLib('sections');
		$lib = new Sections();

		$sections = $lib->children($root);

		// �������� �������� ��������� �����
		$branch = array();

		/*
		 * �������� ������ ������� ���� "������� �����������" ��� �������,
		 * ���������� �������� ������� ����� �� ����.
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

			$subitems = $this->buildGalleryList($section['id'], $level + 1);

			if ($item['selectable'] || $subitems)
			{
				$branch []= $item;
			}

			$branch = array_merge($branch, $subitems);
		}

		return $branch;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ������� �������
	 *
	 * @return string  HTML
	 */
	private function adminRenderProperties()
	{
		global $Eresus, $page;

		$item = $Eresus->db->selectItem('pages', '`id`="' . arg('section', 'int') . '"');

		$form = array(
			'name' => 'contentEditor',
			'caption' => '����� �� ��������',
			'width' => '800px',
			'fields' => array(
				array('type' => 'hidden', 'name' => 'action', 'value' => 'props_update'),
				array('type' => 'hidden', 'name' => 'textupdate', 'value' => '1'),
				array('type' => 'edit', 'name' => 'title', 'label' => '��������', 'width' => '150px'),
				array('type' => 'edit', 'name' => 'created', 'label' => '���� ��������',
					'width' => '150px', 'comment' => '����-��-�� ��:��:��'),
				array('type' => 'checkbox', 'name' => 'active', 'label' => '�������'),
				array('type' => 'html', 'name' => 'content', 'height' => '400px',
					'label' => '��������<div class="ui-state-warning"><em>��������!</em> ��� ����, ����� ' .
					'��� �������� ������������ �����������, ���� �������� � ' .
					'<a href="admin.php?mod=plgmgr&id=gallery">������</a> ����� ������ �������� (�������).' .
					'</div>'),
			),
			'buttons'=> array('ok', 'apply', 'cancel'),
		);

		$result = $page->renderForm($form, $item);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ����������� ����������� ����� �� ������
	 *
	 * @param int $id  ������������� �����������
	 *
	 * @return void
	 */
	private function up($id)
	{
		$image = new GalleryImage($id);
		$image->moveUp();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ����������� ����������� ���� �� ������
	 *
	 * @param int $id  ������������� �����������
	 *
	 * @return void
	 */
	private function down($id)
	{
		$image = new GalleryImage($id);
		$image->moveDown();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������� ��� ��������� �����������
	 *
	 * @param int $id  ������������� �����������
	 */
	private function adminImageToggle($id)
	{
		$image = new GalleryImage($id);
		$image->active = ! $image->active;
		$image->save();

		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������ ��������� � ������� ����������� �������� �������
	 *
	 * @return void
	 */
	private function coverAction()
	{
		$id = arg('cover', 'int');

		$image = new GalleryImage($id);
		$image->cover = true;
		$image->save();

		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

}
