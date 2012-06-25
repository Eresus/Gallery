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
		$groups = Gallery_Group::find($sectionId);
		return $groups;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запускает процесс перестройки миниатюр
	 *
	 * @param int $newWidth   новая ширина миниатюр
	 * @param int $newHeihgt  новая высота миниатюр
	 * @return array
	 *
	 * @since 2.01
	 */
	protected function actionThumbsRebuildStart($newWidth, $newHeight)
	{
		$query = DB::getHandler()->createSelectQuery();
		$query->select('id')->from(Gallery_Image::getDbTableStatic('Gallery_Image'));
		$raw = DB::fetchAll($query);

		$ids = array();
		foreach ($raw as $image)
		{
			$ids []= $image['id'];
		}
		return array('action' => 'start', 'ids' => $ids, 'width' => $newWidth, 'height' => $newHeight);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Пересоздаёт миниатюру
	 *
	 * @param int $imageId
	 * @param int $width
	 * @param int $height
	 * @return array
	 *
	 * @since 2.01
	 */
	protected function actionThumbsRebuildNext($imageId, $width, $height)
	{
		$response = array('action' => 'build', 'id' => $imageId, 'status' => 'success',
			'width' => $width, 'height' => $height);
		try
		{
			$image = new Gallery_Image($imageId);
			$image->buildThumb($width, $height);
		}
		catch (Exception $e)
		{
			$response['status'] = $e->getMessage();
		}

		return $response;
	}
	//-----------------------------------------------------------------------------
}
