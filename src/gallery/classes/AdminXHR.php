<?php
/**
 * Контроллер XHR-запросов
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
 *
 * $Id: AbstractActiveRecord.php 356 2010-09-08 08:08:15Z mk $
 */


/**
 * Контроллер XHR-запросов
 *
 * @package Gallery
 */
class Gallery_AdminXHR
{
	/**
	 * Выполняет действия контроллера
	 *
	 * @param string $args  Аргументы запроса
	 *
	 * @throws BadMethodCallException
	 *
	 * @return void
	 */
	public function execute($args)
	{
		$args = explode('/', urldecode($args));

		/* Отбрасываем пустые элементы с концов */
		array_shift($args);
		array_pop($args);

		// Получаем имя действия
		$method = 'action' . array_shift($args);

		if (!method_exists($this, $method))
		{
			throw new BadMethodCallException("Method $method does not exists in class " .
				get_class($this));
		}

		$result = call_user_func_array(array($this, $method), $args);

		$result = $this->prepareResponseData($result);

		$json = json_encode($result);
		die($json);
	}

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
		/* @var Gallery_Entity_Table_Group $table */
		$table = ORM::getTable($GLOBALS['Eresus']->plugins->load('gallery'), 'Group');
		$groups = $table->findInSection($sectionId);
		return $groups;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запускает процесс перестройки миниатюр
	 *
	 * @param int $newWidth   новая ширина миниатюр
	 * @param int $newHeight  новая высота миниатюр
	 *
	 * @return array
	 *
	 * @since 2.01
	 */
	protected function actionThumbsRebuildStart($newWidth, $newHeight)
	{
		$table = ORM::getTable($GLOBALS['Eresus']->plugins->load('gallery'), 'Image');
		$images = $table->findAll();

		$ids = array();
		foreach ($images as $image)
		{
			$ids []= $image->id;
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

		$table = ORM::getTable($GLOBALS['Eresus']->plugins->load('gallery'), 'Image');
		try
		{
			/* @var Gallery_Entity_Image $image */
			$image = $table->find($imageId);
			$image->buildThumb($width, $height);
		}
		catch (Exception $e)
		{
			$response['status'] = $e->getMessage();
		}

		return $response;
	}

	/**
	 * Подготавливает данные для передачи json_encode
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	private function prepareResponseData($data)
	{
		switch (true)
		{
			case is_object($data):
				if (method_exists($data, 'toArray'))
				{
					$data = $data->toArray();
				}
				else
				{
					$data = get_object_vars($data);
				}
				$data = $this->prepareResponseData($data);
				break;

			case is_array($data):
				foreach ($data as $key => $value)
				{
					$data[$key] = $this->prepareResponseData($value);
				}
				break;
		}
		return $data;
	}
}
