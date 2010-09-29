<?php
/**
 * Галерея изображений
 *
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author Михаил Красильников <mk@3wstyle.ru>
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
 * @subpackage Tests
 *
 * $Id$
 */

include_once dirname(__FILE__) . '/helpers.php';
include_once dirname(__FILE__) . '/../../src/gallery/classes/AbstractActiveRecord.php';
include_once dirname(__FILE__) . '/../../src/gallery/classes/Image.php';

/**
 * @package Gallery
 * @subpackage Tests
 */
class GalleryImageTest extends PHPUnit_Framework_TestCase
{
	private $fixture;

	/**
	 * Setup test enviroment
	 */
	protected function setUp()
	{
		// @codeCoverageIgnoreStart
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->plugins = new PluginsStub();
		$this->fixture = new GalleryImageTest_Stub();
		// @codeCoverageIgnoreEnd
	}
	//-----------------------------------------------------------------------------

	/**
	 * Cleanup test enviroment
	 */
	protected function tearDown()
	{
		// @codeCoverageIgnoreStart
		unset($this->fixture);
		unset($GLOBALS['Eresus']);
		// @codeCoverageIgnoreEnd
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getTableName
	 * @covers GalleryImage::getTableName
	 */
	public function testGetTableName()
	{
		$this->assertEquals('images', $this->fixture->getTableName());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getAttrs
	 * @covers GalleryImage::getAttrs
	 */
	public function testGetAttrs()
	{
		$this->assertEquals(10, count($this->fixture->getAttrs()));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем чтение свойства $imageURL
	 * @covers GalleryImage::getImageURL
	 */
	public function testGetImageURL()
	{
		$this->assertEquals('http://example.org/data/name/1.jpg', $this->fixture->imageURL);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем чтение свойства $thumbURL
	 * @covers GalleryImage::getThumbURL
	 */
	public function testGetThumbURL()
	{
		$this->assertEquals('http://example.org/data/name/1-thmb.jpg', $this->fixture->thumbURL);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем чтение свойства $showURL
	 * @covers GalleryImage::getShowURL
	 */
	public function testGetShowURL()
	{
		$this->assertEquals('http://example.org/name/123/', $this->fixture->showURL);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем чтение свойства $showURL
	 * @covers GalleryImage::getShowURL
	 */
	public function testGetShowUrlPopup()
	{
		$GLOBALS['Eresus']->plugins->plugin->settings['showItemMode'] = 'popup';
		$this->assertEquals('http://example.org/data/name/1.jpg#gallery-popup', $this->fixture->showURL);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку свойства $group
	 * @covers GalleryImage::setGroup
	 */
	public function testSetGroup()
	{
		$this->fixture->group = 123;
		$this->assertEquals(123, $this->fixture->_getProperty('groupId'));
	}
	//-----------------------------------------------------------------------------

	/* */
}

/**
 * @package Gallery
 * @subpackage Tests
 */
class GalleryImageTest_Stub extends GalleryImage
{
	function __construct()
	{
		parent::__construct();
		// Предовтращаем использование одного и того же экземпляра во все всех тестах.
		self::plugin($GLOBALS['Eresus']->plugins->load('gallery'));
		$this->setProperty('id', '123');
		$this->setProperty('image', '1.jpg');
		$this->setProperty('thumb', '1-thmb.jpg');
	}
	//-----------------------------------------------------------------------------

	public function _getProperty($key)
	{
		return parent::getProperty($key);
	}
	//-----------------------------------------------------------------------------

}
