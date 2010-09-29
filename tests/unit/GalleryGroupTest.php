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
include_once dirname(__FILE__) . '/../../src/gallery/classes/Group.php';

/**
 * @package Gallery
 * @subpackage Tests
 */
class GalleryGroupTest extends PHPUnit_Framework_TestCase
{
	private $fixture;

	/**
	 * Setup test enviroment
	 */
	protected function setUp()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->plugins = new PluginsStub();
		$this->fixture = new GalleryGroupTest_Stub();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Cleanup test enviroment
	 */
	protected function tearDown()
	{
		unset($this->fixture);
		unset($GLOBALS['Eresus']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getTableName
	 *
	 */
	public function testGetTableName()
	{
		$this->assertEquals('groups', $this->fixture->getTableName());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getAttrs
	 *
	 */
	public function testGetAttrs()
	{
		$this->assertEquals(5, count($this->fixture->getAttrs()));
	}
	//-----------------------------------------------------------------------------

	/* */
}

/**
 * @package Gallery
 * @subpackage Tests
 */
class GalleryGroupTest_Stub extends GalleryGroup
{
	function __construct()
	{
		parent::__construct();
		// Предовтращаем использование одного и того же экземпляра во все всех тестах.
		self::plugin($GLOBALS['Eresus']->plugins->load('gallery'));
	}
	//-----------------------------------------------------------------------------

	public function _getProperty($key)
	{
		return parent::getProperty($key);
	}
	//-----------------------------------------------------------------------------

}
