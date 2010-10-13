<?php
/**
 * Галерея изображений
 *
 * Контроллер XHR-запросов
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
 *
 * $Id: AbstractActiveRecord.php 356 2010-09-08 08:08:15Z mk $
 */


/**
 * Контроллер XHR-запросов
 *
 * @package Gallery
 */
class GalleryAdminXHRController extends GalleryEresusAdminXHRController
{
	/**
	 * Возвращает список групп в указанном разделе
	 *
	 * @param int $sectionId
	 * @return array
	 *
	 * @since 2.00
	 */
	protected function actionGetGroups($sectionId)
	{
		$sectionId = intval($sectionId);
		$groups = GalleryGroup::find($sectionId);
		return $groups;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запускает процесс перестройки миниатюр
	 *
	 * @return array
	 *
	 * @since 2.01
	 */
	protected function actionThumbsRebuildStart()
	{
		$query = DB::getHandler()->createSelectQuery();
		$query->select('id')->from(GalleryImage::getDbTableStatic('GalleryImage'));
		$raw = DB::fetchAll($query);

		$ids = array();
		foreach ($raw as $image)
		{
			$ids []= $image['id'];
		}
		return array('action' => 'start', 'ids' => $ids);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Пересоздаёт миниатюру
	 *
	 * @param int $imageId
	 * @return array
	 *
	 * @since 2.01
	 */
	protected function actionThumbsRebuildNext($imageId)
	{
		$response = array('action' => 'build', 'id' => $imageId, 'status' => 'success');
		try
		{
			$image = new GalleryImage($imageId);
			$image->buildThumb();
		}
		catch (Exception $e)
		{
			$response['status'] = $e->getMessage();
		}

		return $response;
	}
	//-----------------------------------------------------------------------------
}
