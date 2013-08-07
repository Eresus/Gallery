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

define('TESTS_SRC_ROOT', realpath(__DIR__ . '/../../src'));

require_once 'stubs.php';
require_once TESTS_SRC_ROOT . '/gallery/classes/ClientListView.php';
require_once TESTS_SRC_ROOT . '/gallery/classes/ClientPopupView.php';
require_once TESTS_SRC_ROOT . '/gallery/classes/Exception/UploadException.php';

/**
 * Универсальная заглушка
 */
class UniversalStub implements ArrayAccess
{
    public function __get($a)
    {
        return $this;
    }

    public function __call($a, $b)
    {
        return $this;
    }

    public function offsetExists($offset)
    {
        return true;
    }

    public function offsetGet($offset)
    {
        return $this;
    }

    public function offsetSet($offset, $value)
    {
        ;
    }

    public function offsetUnset($offset)
    {
        ;
    }

    public function __toString()
    {
        return '';
    }
}


/**
 * Фасад к моку для эмуляции статичных методов
 *
 */
class MockFacade
{
    /**
     * Мок
     *
     * @var object
     */
    private static $mock;

    /**
     * Устанавливает мок
     *
     * @param object $mock
     *
     * @return void
     */
    public static function setMock($mock)
    {
        self::$mock = $mock;
    }
    //-----------------------------------------------------------------------------

    /**
     * Вызывает метод мока
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callstatic($method, $args)
    {
        if (self::$mock && method_exists(self::$mock, $method))
        {
            return call_user_func_array(array(self::$mock, $method), $args);
        }

        return new UniversalStub();
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

