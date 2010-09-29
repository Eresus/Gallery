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

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @package Gallery
 * @subpackage Tests
 */
class EresusPropertyNotExistsException extends Exception
{
	function __construct($property = null, $class = null, $description = null, $previous = null)
	{
	}
	//-----------------------------------------------------------------------------
}



/**
 * @package Gallery
 * @subpackage Tests
 */
class EresusTypeException extends Exception
{
	function __construct($var = null, $expectedType = null, $description = null, $previous = null)
	{
	}
	//-----------------------------------------------------------------------------
}



/**
 * @package Gallery
 * @subpackage Tests
 */
class PluginsStub
{
	public $plugin;

	public function __construct()
	{
		$this->plugin = new Gallery();
	}
	//-----------------------------------------------------------------------------

	public function __destruct()
	{
		unset($this->plugin);
	}
	//-----------------------------------------------------------------------------

	public function load($name)
	{
		return $this->plugin;
	}
	//-----------------------------------------------------------------------------
}



/**
 * @package Gallery
 * @subpackage Tests
 */
class Gallery
{
	public $name = 'gallery';

	public $settings = array(
		'showItemMode' => 'default'
	);

	public function getDataURL()
	{
		return 'http://example.org/data/name/';
	}
	//-----------------------------------------------------------------------------

	public function clientListURL()
	{
		return 'http://example.org/name/';
	}
	//-----------------------------------------------------------------------------
}

