<?php
/**
 * Таблица абстрактного наполнения раздела
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
 * Таблица абстрактного наполнения раздела
 *
 * @package Gallery
 * @since 3.00
 */
abstract class Gallery_Entity_Table_AbstractContent extends ORM_Table
{
	/**
	 * Возвращает включенные элементы в указанном разделе
	 *
	 * @param int $id      ID раздела сайта
	 * @param int $limit   максимальное количество возвращаемых элементов
	 * @param int $offset  позиция с которой начать выборку
	 *
	 * @return array
	 */
	abstract public function findInSection($id, $limit = null, $offset = 0);

	/**
	 * Возвращает количество элементов в указанном разделе
	 *
	 * @param int $id  ID раздела сайта
	 *
	 * @return int
	 */
	public function countInSection($id)
	{
		$q = $this->createCountQuery();
		$q->where($q->expr->eq('section', $q->bindValue($id, null, PDO::PARAM_STR)));
		return $this->count($q);
	}
}