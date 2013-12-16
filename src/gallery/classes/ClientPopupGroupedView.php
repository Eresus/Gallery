<?php
/**
 * Класс представления "Просмотр во всплывающем блоке (с группами)"
 *
 * @version ${product.version}
 *
 * @copyright 2011, ООО "Два слона", http://dvaslona.ru/
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
 * Класс представления "Просмотр во всплывающем блоке (группами)"
 *
 * @package Gallery
 * @since 2.03
 */
class Gallery_ClientPopupGroupedView extends Gallery_ClientPopupView
{
    /**
     * Отрисовывает список изображений альбома для перехода к ним во всплывающем блоке.
     *
     * @return string
     *
     * @since 2.03
     */
    protected function renderImageList()
    {
        /* @var Gallery_Entity_Table_Group $table */
        $table = ORM::getTable(Eresus_Plugin_Registry::getInstance()->load('gallery'), 'Group');
        $groups = $table->findInSection(Eresus_Kernel::app()->getPage()->id);
        $jsArray = array();
        foreach ($groups as $group)
        {
            foreach ($group->images as $image)
            {
                /* @var Gallery_Entity_Image $image */
                $jsArray []= '"' . $image->imageURL . '"';
            }
        }
        $html = '<script type="text/javascript">Eresus.Gallery.images = ['.
            implode(',', $jsArray) . '];</script>';
        return $html;
    }
}

