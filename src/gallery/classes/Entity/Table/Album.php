<?php
/**
 * Таблица альбомов (разделов сайта)
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

/**
 * Таблица альбомов (разделов сайта)
 *
 * @package Gallery
 * @since 3.00
 */
class Gallery_Entity_Table_Album extends ORM_Table
{
    /**
     * Структура таблицы
     */
    public function setTableDefinition()
    {
        $this->setTableName('pages');
        $this->hasColumns(array(
            'id' => array(
                'type' => 'integer',
                'unsigned' => true,
                'autoincrement' => true,
            ),
            'title' => array(
                'type' => 'string',
                'length' => 255,
                'default' => null,
            ),
            'description' => array(
                'type' => 'string',
                'length' => 65535,
                'default' => null,
            ),
            'position' => array(
                'type' => 'integer',
                'unsigned' => true,
                'default' => 0,
            ),
        ));
    }
}

