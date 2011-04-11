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

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

$root = realpath(dirname(__FILE__) . '/../..');

//PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist($root . '/src');
//PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist($root . '/src');

require_once dirname(__FILE__) . '/GalleryAbstractActiveRecordTest.php';
require_once dirname(__FILE__) . '/GalleryGroupTest.php';
require_once dirname(__FILE__) . '/GalleryImageTest.php';

class AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$suite->addTestSuite('GalleryAbstractActiveRecordTest');
		$suite->addTestSuite('GalleryGroupTest');
		$suite->addTestSuite('GalleryImageTest');

		return $suite;
	}
}
