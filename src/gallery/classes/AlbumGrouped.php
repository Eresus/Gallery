<?php
/**
 * Галерея изображений
 *
 * Альбом с группами
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
 * Альбом с группами
 *
 * Только для КИ!
 *
 * Альбом — это список изображений в определённом разделе сайта
 *
 * @package Gallery
 * @since 2.03
 */
class Gallery_AlbumGrouped extends Gallery_Album
{
    /**
     * Загружает объекты из БД, если они не были загружены ранее
     *
     * @return void
     *
     * @since 2.03
     */
    protected function load()
    {
        if ($this->loaded)
        {
            return;
        }

        $table = ORM::getTable(Eresus_CMS::getLegacyKernel()->plugins->load('gallery'), 'Image');
        $q = $table->createSelectQuery();
        $e = $q->expr;

        $q->where(
            $e->lAnd(
                $e->eq('section', $q->bindValue($this->sectionId, null, PDO::PARAM_INT)),
                $e->eq('active', $q->bindValue(true, null, PDO::PARAM_BOOL)),
                $e->neq('groupId', 0)
            ))->
            orderBy('groupId');

        $this->items = $table->loadFromQuery($q);

        $this->loaded = true;
    }
}

