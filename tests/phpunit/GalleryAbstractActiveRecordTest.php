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

/**
 * @package Gallery
 * @subpackage Tests
 */
class GalleryAbstractActiveRecordTest extends PHPUnit_Framework_TestCase
{
	private $fixture;

	/**
	 * Setup test enviroment
	 */
	protected function setUp()
	{
		// @codeCoverageIgnoreStart
		$this->fixture = new GalleryAbstractActiveRecordTest_Stub();
		// @codeCoverageIgnoreEnd
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getDbTable
	 * @covers Gallery_AbstractActiveRecord::getDbTable
	 */
	public function testGetDbTable()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->plugins = new PluginsStub();

		$this->assertEquals('gallery_mytable', $this->fixture->getDbTable());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getDbTableStatic
	 * @covers Gallery_AbstractActiveRecord::getDbTableStatic
	 */
	public function testGetDbTableStatic()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->plugins = new PluginsStub();

		$this->assertEquals('gallery_mytable', GalleryAbstractActiveRecordTest_Stub::getDbTableStatic('GalleryAbstractActiveRecordTest_Stub'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getDbTableStatic
	 * @covers Gallery_AbstractActiveRecord::getDbTableStatic
	 * @expectedException EresusTypeException
	 */
	public function testGetDbTableStatic_fail()
	{
		$this->assertEquals('gallery_mytable', GalleryAbstractActiveRecordTest_Stub::getDbTableStatic('stdClass'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем вброс исключения EresusPropertyNotExistsException при обращении к несуществующему
	 * свойству.
	 *
	 * @expectedException EresusPropertyNotExistsException
	 */
	public function testGetUnexistentProperty()
	{
		$x = $this->fixture->unexistent;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем чтение неустановленного свойства.
	 *
	 */
	public function testGetUnsetProperty()
	{
		$this->assertNull($this->fixture->int);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем вброс исключения EresusPropertyNotExistsException при обращении к несуществующему
	 * свойству.
	 *
	 * @expectedException EresusPropertyNotExistsException
	 */
	public function testSetUnexistentProperty()
	{
		$this->fixture->unexistent = true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку свойства неподдерживаемого типа
	 *
	 * @expectedException EresusTypeException
	 */
	public function testSetUnsupportedType()
	{
		$this->fixture->unsupported = null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение полей типа 'bool'
	 *
	 */
	public function testSetGetBool()
	{
		$this->fixture->bool = true;
		$this->assertTrue($this->fixture->bool);
		$this->fixture->bool = false;
		$this->assertFalse($this->fixture->bool);
		$this->fixture->bool = 'yes';
		$this->assertTrue($this->fixture->bool);
		$this->fixture->bool = null;
		$this->assertFalse($this->fixture->bool);
		$this->fixture->bool = '0';
		$this->assertFalse($this->fixture->bool);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение полей типа 'int'
	 *
	 */
	public function testSetGetInt()
	{
		$this->fixture->int = 100;
		$this->assertEquals(100, $this->fixture->int);
		$this->fixture->int = '101';
		$this->assertEquals(101, $this->fixture->int);
		$this->fixture->int = '102abc';
		$this->assertEquals(102, $this->fixture->int);
		$this->fixture->int = 'abc103';
		$this->assertEquals(0, $this->fixture->int);
		$this->fixture->int = '104a105';
		$this->assertEquals(104, $this->fixture->int);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение полей типа 'string'
	 *
	 */
	public function testSetGetString()
	{
		$this->fixture->string = 'test1';
		$this->assertEquals('test1', $this->fixture->string);
		$this->fixture->string = '0123456789abc';
		$this->assertEquals('0123456789', $this->fixture->string);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем isNew
	 *
	 */
	public function testIsNew()
	{
		$this->assertTrue($this->fixture->isNew());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем вызов сеттера
	 *
	 */
	public function testCallSetter()
	{
		$this->fixture->int2 = 123;
		$this->assertTrue($this->fixture->_success);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем вызов геттера
	 *
	 */
	public function testCallGetter()
	{
		$this->assertEquals(123, $this->fixture->int2);
	}
	//-----------------------------------------------------------------------------

	/* */
}

/*******************************************************************************
 *
 * ЗАГЛУШКИ
 *
 *******************************************************************************/

/**
 * @package Gallery
 * @subpackage Tests
 */
class GalleryAbstractActiveRecordTest_Stub extends Gallery_AbstractActiveRecord
{
	public $_success = false;

	public function getTableName()
	{
		return 'mytable';
	}
	//-----------------------------------------------------------------------------

	public function getAttrs()
	{
		return array(
			'unsupported' => array('type' => null),
			'bool' => array('type' => PDO::PARAM_BOOL),
			'int' => array('type' => PDO::PARAM_INT),
			'string' => array('type' => PDO::PARAM_STR, 'maxlength' => 10),
		);
	}
	//-----------------------------------------------------------------------------

	protected function setInt2()
	{
		$this->_success = true;
	}
	//-----------------------------------------------------------------------------

	protected function getInt2()
	{
		return 123;
	}
	//-----------------------------------------------------------------------------
}


function eresus_log()
{
	;
}
//-----------------------------------------------------------------------------