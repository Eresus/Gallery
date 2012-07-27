<?php
/**
 * Альбом
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
 * Альбом
 *
 * Альбом — это обёртка раздела сайта, упрощающая доступ к группам и изображениям в этом разделе.
 *
 * @property      int                    $id           Идентификатор
 *
 * @package Gallery
 * @since 3.00
 */
class Gallery_Entity_Album extends ORM_Entity
{
	/**
	 * Делает изображение обложкой альбома
	 *
	 * @param Gallery_Entity_Image $image
	 */
	public function setCover(Gallery_Entity_Image $image)
	{
		// TODO
	}

	/**
	 * Возвращает список изображений не привязанных к какой-либо группе
	 *
	 * @return Gallery_Entity_Image[]
	 */
	public function getOrphans()
	{
		$table = ORM::getTable($this->plugin, 'Image');
		$q = $table->createSelectQuery();
		$e = $q->expr;
		$q->where($e->lAnd(
			$e->eq('section', $q->bindValue($this->id, null, PDO::PARAM_INT)),
			$e->eq('groupId', $q->bindValue(0, null, PDO::PARAM_INT))
		));
		return $table->loadFromQuery($q);
	}
}
