<?php
/**
 * Галерея изображений
 *
 * @version ${product.version}
 *
 * @copyright 2008, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Михаил Красильников <mk@dvaslona.ru>
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
    public $kernel = '3.01a';

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
     * Конструктор
     *
     * @since 3.00
     */
    public function __construct()
    {
        parent::__construct();
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Автозагрузчик классов
     *
     * @param $className
     *
     * @return bool
     *
     * @since 3.00
     */
    public function autoload($className)
    {
        if (substr($className, 0, 8) == 'PhpThumb')
        {
            $filename = $this->getCodeDir() . '/phpthumb/ThumbLib.inc.php';
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
            return class_exists($className, false) || interface_exists($className, false);
        }
        return false;
    }

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
        $page = Eresus_Kernel::app()->getPage();
        $page->linkStyles($this->urlCode . 'admin.css');
        $page->linkScripts($this->urlCode . 'admin.js');
        $page->linkJsLib('webshim');

        // Данные для подстановки в шаблон
        $data = array();
        $data['this'] = $this;
        $data['page'] = $page;
        $data['logo_exists'] = file_exists($this->dirData . 'logo.png');

        $tmplDir = Eresus_CMS::getLegacyKernel()->froot . 'templates/' . $this->name;
        $this->settings['tmplImageList'] = file_get_contents($tmplDir . '/image-list.html');
        $this->settings['tmplImageGroupedList'] =
            file_get_contents($tmplDir . '/image-grouped-list.html');
        $this->settings['tmplImage'] = file_get_contents($tmplDir . '/image.html');
        $this->settings['tmplPopup'] = file_get_contents($tmplDir . '/popup.html');

        // Создаём экземпляр шаблона
        $form = new EresusForm('ext/' . $this->name . '/templates/settings.html');
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
     * @throws RuntimeException
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

        $ts = TemplateService::getInstance();
        try
        {
            $ts->setContents(arg('tmplImageList'), 'image-list.html', $this->name);
            $ts->setContents(arg('tmplImageList'), 'image-list.html', $this->name);
            $ts->setContents(arg('tmplImageGroupedList'), 'image-grouped-list.html', $this->name);
            $ts->setContents(arg('tmplImage'), 'image.html', $this->name);
            $ts->setContents(arg('tmplPopup'), 'popup.html', $this->name);
        }
        catch (Exception $e)
        {
            throw new RuntimeException(
                'Не удалось изменить один или несколько шаблонов. Подробная информация доступна в журнале.',
                0,
                $e);
        }

        parent::updateSettings();
    }
    //-----------------------------------------------------------------------------

    /**
     * Действия при установке
     *
     * Метод создаёт необходимые таблицы в БД и директорию данных.
     *
     * @throws Eresus_CMS_Exception
     *
     * @return void
     */
    public function install()
    {
        parent::install();

        $table = ORM::getTable($this, 'Image');
        $table->create();

        $table = ORM::getTable($this, 'Group');
        $table->create();

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
            throw new Eresus_CMS_Exception(
                'Не удалось установить шаблоны плагина. Подробная информация доступна в журнале.',
                0,
                $e);
        }
    }

    /**
     * Действия при удалении плагина
     *
     * Метод удаляет файлы данных плагина.
     *
     * @throws Eresus_CMS_Exception
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
            throw new Eresus_CMS_Exception(
                'Не удалось удалить шаблоны плагина. Подробная информация доступна в журнале.',
                0,
                $e);
        }

        parent::uninstall();
    }

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
            'url' => preg_replace('/&(id|pg)=\d+/', '', $page->url(null)),
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
        $ctl = new Gallery_AdminXHR;
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

        /* @var Gallery_Entity_Table_AbstractContent $table */
        if ($this->settings['useGroups'])
        {
            $table = ORM::getTable($this, 'Group');
        }
        else
        {
            $table = ORM::getTable($this, 'Image');
        }

        $items = $table->findInSection($section, $maxCount, $startFrom, false);

        // Данные для подстановки в шаблон
        $vars = array();
        $vars['this'] = $this;
        $vars['page'] = Eresus_Kernel::app()->getPage();
        $vars['Eresus'] = Eresus_CMS::getLegacyKernel();
        $vars['sectionId'] = arg('section', 'int');
        $vars['items'] = $items;

        /* Шаблоны адресов действий */
        $vars['urlEdit'] = str_replace('&', '&amp;',
            Eresus_Kernel::app()->getPage()->url(array('id' => '%s')));
        $vars['urlToggle'] = str_replace('&', '&amp;',
            Eresus_Kernel::app()->getPage()->url(array('toggle' => '%s')));
        $vars['urlCover'] = str_replace('&', '&amp;',
            Eresus_Kernel::app()->getPage()->url(array('cover' => '%s')));
        $vars['urlUp'] = str_replace('&', '&amp;',
            Eresus_Kernel::app()->getPage()->url(array('up' => '%s')));
        $vars['urlDown'] = str_replace('&', '&amp;',
            Eresus_Kernel::app()->getPage()->url(array('down' => '%s')));
        $vars['urlDelete'] = str_replace('&', '&amp;',
            Eresus_Kernel::app()->getPage()->url(array('delete' => '%s')));

        $totalPages = ceil($table->countInSection($section) / $maxCount);

        if ($totalPages > 1)
        {
            $pager = new PaginationHelper($totalPages, $pg,
                Eresus_Kernel::app()->getPage()->url(array('pg' => '%s')));
            $vars['pager'] = $pager->render();
        }
        else
        {
            $vars['pager'] = '';
        }

        Eresus_Kernel::app()->getPage()->linkStyles($this->urlCode . 'admin.css');
        Eresus_Kernel::app()->getPage()->linkScripts($this->urlCode . 'admin.js');

        /* Создаём экземпляр шаблона */
        if ($this->settings['useGroups'])
        {
            $tmpl = new Template('ext/' . $this->name . '/templates/image-grouped-list.html');
            // Изображения вне групп
            $table = ORM::getTable($this, 'Album');
            /* @var Gallery_Entity_Album $album */
            $album = $table->find($section);
            $vars['orphans'] = $album->getOrphans();
        }
        else
        {
            $tmpl = new Template('ext/' . $this->name . '/templates/image-list.html');
        }

        // Компилируем шаблон и данные
        $html = $tmpl->compile($vars);

        return $html;
    }
    //-----------------------------------------------------------------------------

    /**
     * Добавление изображения
     *
     * @throws DomainException
     *
     * @return string  HTML
     *
     * @TODO Переделать через ORM
     */
    private function adminAddItem()
    {
        $request = Eresus_CMS::getLegacyKernel()->request;
        if ('POST' == $request['method'])
        {
            $image = new Gallery_Entity_Image($this);
            $image->section = arg('section');
            $image->groupId = arg('group') ? arg('group') : 0;
            $_SESSION['gallery_default_group'] = arg('group');
            $image->title = arg('title');
            $image->cover = arg('cover') ? arg('cover') : false;
            $image->active = arg('active');
            $image->posted = new DateTime();
            $image->image = 'image'; // $_FILES['image'];

            if ($this->isImageInputValid($image))
            {
                $table = ORM::getTable($this, 'Image');
                try
                {
                    $table->persist($image);
                }
                catch (Gallery_Exception_FileTooBigException $e)
                {
                    throw new DomainException('Размер загружаемого файла превышает максимально допустимый');
                }

                $url = 'admin.php?mod=content&section=' . $image->section;
                if (arg('pg'))
                {
                    $url .= '&pg=' . arg('pg', 'int');
                }
                HTTP::redirect($url);
            }
        }

        Eresus_Kernel::app()->getPage()->linkStyles($this->urlCode . 'admin.css');

        // Данные для подстановки в шаблон
        $data = array();
        $data['this'] = $this;
        $data['page'] = Eresus_Kernel::app()->getPage();
        $data['sectionId'] = arg('section', 'int');
        $data['defaultGroup'] = isset($_SESSION['gallery_default_group'])
            ? $_SESSION['gallery_default_group']
            : null;
        $data['image'] = isset($image) ? $image : null;

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

    /**
     * Возвращает диалог изменения изображения
     *
     * @return string  HTML
     */
    private function adminEditItem()
    {
        $table = ORM::getTable($this, 'Image');
        /* @var Gallery_Entity_Image $image */
        $image = $table->find(arg('id', 'int'));

        /* @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $page->linkStyles($this->urlCode . 'admin.css');
        $page->linkScripts($this->urlCode . 'admin.js');

        // Данные для подстановки в шаблон
        $data = array();
        $data['this'] = $this;
        $data['page'] = $page;
        $data['pg'] = arg('pg', 'int');
        $data['image'] = $image;
        $data['sections'] = $this->buildGalleryList(0);

        if ($this->settings['useGroups'])
        {
            /* @var Gallery_Entity_Table_Group $table */
            $table = ORM::getTable($this, 'Group');
            $data['groups'] = $table->findInSection($image->section);
        }

        // Создаём экземпляр шаблона
        $tmpl = new Template('ext/' . $this->name . '/templates/edit-image.html');

        // Компилируем шаблон и данные
        $html = $tmpl->compile($data);

        return $html;
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
        /* @var Gallery_Entity_Table_Image $table */
        $table = ORM::getTable($this, 'Image');
        /* @var Gallery_Entity_Image $image */
        $image = $table->find(arg('update', 'int'));

        $new_section = arg('section', 'int');
        if ($new_section == $image->section)
        {
            $image->groupId = arg('group', 'int');
        }
        else
        {
            $image->section = $new_section;
            $image->groupId = arg('new_group', 'int');
        }
        $image->title = arg('title', 'dbsafe');
        $image->posted = new DateTime(arg('posted'));
        $image->cover = arg('cover', 'int');
        $image->active = arg('active', 'int');
        $image->image = 'image'; // $_FILES['image'];

        $table->update($image);

        HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
    }

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
        $group = new Gallery_Entity_Group($this);
        $group->section = arg('section');
        $group->title = arg('title');
        $group->description = arg('description');

        // Код определения позиции желательно перенести в Gallery_Group
        $maxPosition = $this->dbSelect('groups', "`section` = '{$group->section}'", null,
            'MAX(`position`) AS `value`');
        $group->position = intval($maxPosition[0]['value']) + 1;

        $table = ORM::getTable($this, 'Group');
        $table->persist($group);

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
        /* @var Gallery_Entity_Table_Group $table */
        $table = ORM::getTable($this, 'Group');
        /* @var Gallery_Entity_Group $group */
        $group = $table->find(arg('group_up', 'int'));
        $table->moveUp($group);
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
        /* @var Gallery_Entity_Table_Group $table */
        $table = ORM::getTable($this, 'Group');
        /* @var Gallery_Entity_Group $group */
        $group = $table->find(arg('group_down', 'int'));
        $table->moveDown($group);
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
        /* @var Gallery_Entity_Table_Group $table */
        $table = ORM::getTable($this, 'Group');
        $group = $table->find($id);
        $table->delete($group);

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
        /* @var Gallery_Entity_Table_Image $table */
        $table = ORM::getTable($this, 'Image');
        $image = $table->find($id);
        $table->delete($image);

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

        /* @var Gallery_Entity_Table_Image $table */
        $table = ORM::getTable($this, 'Image');
        /* @var Gallery_Entity_Image $image */
        $image = $table->find($id);

        if (!$image || !$image->active)
        {
            $page->HttpError(404);
        }

        // Данные для подстановки в шаблон
        $data = array();
        $data['this'] = $this;
        $data['page'] = $page;
        $data['Eresus'] = Eresus_CMS::getLegacyKernel();
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
        $lib = Eresus_Kernel::app()->getLegacyKernel()->sections;

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
        /* @var Gallery_Entity_Table_Image $table */
        $table = ORM::getTable($this, 'Image');
        /* @var Gallery_Entity_Image $image */
        $image = $table->find($id);
        $table->moveUp($image);
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
        /* @var Gallery_Entity_Table_Image $table */
        $table = ORM::getTable($this, 'Image');
        /* @var Gallery_Entity_Image $image */
        $image = $table->find($id);
        $table->moveDown($image);
        HTTP::goback();
    }

    /**
     * Включает или отключает изображение
     *
     * @param int $id  Идентификатор изображения
     */
    private function adminImageToggle($id)
    {
        $table = ORM::getTable($this, 'Image');
        /* @var Gallery_Entity_Image $image */
        $image = $table->find($id);
        $image->active = ! $image->active;
        $table->update($image);

        HTTP::goback();
    }

    /**
     * Делает указанное в запросе Изображение обложкой альбома
     *
     * @throws Eresus_CMS_Exception_NotFound
     *
     * @return void
     */
    private function coverAction()
    {
        $id = arg('cover', 'int');

        $table = ORM::getTable($this, 'Image');
        /* @var Gallery_Entity_Image $image */
        $image = $table->find($id);
        if (null === $image)
        {
            throw new Eresus_CMS_Exception_NotFound('Запрошенное изображение не найдено');
        }
        $image->album->setCover($image);

        HTTP::goback();
    }

    /**
     * Проверяет правильность заполнения свойств изображения
     *
     * @param Gallery_Entity_Image $image
     *
     * @return bool
     * @since 3.00
     */
    private function isImageInputValid(Gallery_Entity_Image $image)
    {
        // TODO FIXME заменить 'image' на значение из $image
        if (empty($_FILES['image']['name']))
        {
            ErrorMessage(isset($form['message']) ? $form['message'] : 'Поле "файл" не заполнено');
            return false;
        }
        return true;
    }
}

