<?php
/**
 * Автоматические тесты
 *
 * @version ${product.version}
 *
 * @copyright 2012, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Михаил Красильников <mk@dvaslona.ru>
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
 */

require_once TESTS_SRC_ROOT . '/gallery.php';
require_once TESTS_SRC_ROOT . '/gallery/classes/Entity/Table/AbstractContent.php';
require_once TESTS_SRC_ROOT . '/gallery/classes/Entity/Table/Image.php';
require_once TESTS_SRC_ROOT . '/gallery/classes/Entity/Album.php';
require_once TESTS_SRC_ROOT . '/gallery/classes/Entity/Image.php';

class GalleryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Gallery::adminImageToggle
     */
    public function test_adminImageToggle()
    {
        $gallery = new Gallery();

        $adminImageToggle = new ReflectionMethod('Gallery', 'adminImageToggle');
        $adminImageToggle->setAccessible(true);

        $image = new Gallery_Entity_Image($gallery);
        $image->active = true;
        $table = $this->getMock('Gallery_Entity_Table_Image', array('find', 'update'));
        $table->expects($this->once())->method('find')->with(45)->will($this->returnValue($image));
        $table->expects($this->once())->method('update')->with($image);

        $orm = $this->getMock('stdClass', array('getTable'));
        $orm->expects($this->any())->method('getTable')->with($gallery, 'Image')->
            will($this->returnValue($table));

        ORM::setMock($orm);

        $adminImageToggle->invoke($gallery, 45);
        $this->assertFalse($image->active);
    }

    /**
     * @covers Gallery::coverAction
     */
    public function test_coverAction()
    {
        $gallery = new Gallery();

        $orm = $this->getMock('stdClass', array('getTable'));
        $orm->expects($this->any())->method('getTable')->will($this->returnCallback(
            function ($plugin, $name)
            {
                PHPUnit_Framework_Assert::assertInstanceOf('Gallery', $plugin);
                switch ($name)
                {
                    case 'Image':
                        $image = new Gallery_Entity_Image($plugin);
                        $image->section = 123;
                        $table = PHPUnit_Framework_MockObject_Generator::getMock('Gallery_Entity_Table_Image',
                            array('find'));
                        $table->expects(PHPUnit_Framework_TestCase::once())->method('find')->with(1)->
                            will(PHPUnit_Framework_TestCase::returnValue($image));
                        break;

                    case 'Album':
                        $album = PHPUnit_Framework_MockObject_Generator::getMock('Gallery_Entity_Album',
                            array('setCover'), array($plugin));
                        $album->expects(PHPUnit_Framework_TestCase::once())->method('setCover');

                        $table = PHPUnit_Framework_MockObject_Generator::getMock('Gallery_Entity_Table_Album',
                            array('find'));
                        $table->expects(PHPUnit_Framework_TestCase::once())->method('find')->with(123)->
                            will(PHPUnit_Framework_TestCase::returnValue($album));
                        break;

                    default:
                        $table = null;
                }
                return $table;
            }));

        ORM::setMock($orm);

        $GLOBALS['args'] = array('cover' => 1);

        $coverAction = new ReflectionMethod('Gallery', 'coverAction');
        $coverAction->setAccessible(true);
        $coverAction->invoke($gallery);
    }
}

