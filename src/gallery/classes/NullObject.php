<?php
/**
 * NULL (Специальный случай)
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
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


/**
 * NULL
 *
 * Объект этого класса можно возвращать если запрошен несуществующий объект.
 *
 * Объект NullObject возвращает NULL при обращении к любому свойству или методу, что
 * предотвращает возникновение ошибок "Property|Method not exists".
 *
 * @package Gallery
 */
class Gallery_NullObject
{
    /**
     * @param mixed $name
     *
     * @return null
     *
     * @since 2.01
     */
    public function __get($name)
    {
        return null;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     *
     * @return void
     *
     * @since 2.01
     */
    public function __set($name, $value)
    {
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return null
     *
     * @since 2.01
     */
    public function __call($method, $args)
    {
        return null;
    }
}

