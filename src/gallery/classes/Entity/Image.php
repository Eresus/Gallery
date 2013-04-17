<?php
/**
 * Изображение
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
 *
 * $Id: Image.php 1734 2012-12-11 11:14:47Z mk $
 */

/**
 * Изображение
 *
 * @property       int                  $id           идентификатор картинки
 * @property       int                  $section      идентификатор раздела
 * @property       string               $title        название изображения
 * @property       DateTime             $posted       дата добавления
 * @property       int                  $groupId      ID группы изображений
 * @property-read  Gallery_Entity_Group $group        группа изображений
 * @property       bool                 $cover        является ли обложкой альбома
 * @property       bool                 $active       является ли активным
 * @property       int                  $position     порядковый номер
 * @property-read  string               $imageURL     URL изображения
 * @property-read  string               $thumbURL     URL миниатюры
 * @property-read  Gallery_Entity_Album $album        альбом этого изображения
 * @property-write string               $image        свойство для загрузки файла изображения
 * @property-read  string               $showURL      URL или JavaScript для показа картинки
 *
 * @package Gallery
 * @since 3.00
 */
class Gallery_Entity_Image extends ORM_Entity
{
	/**
	 * Имя файла для загрузки
	 *
	 * @var string
	 */
	private $upload = null;

	/**
	 * Указывает на то что это изображение при сохранении надо сделать обложкой
	 *
	 * @var bool
	 */
	private $setAsCover = false;

	/**
	 * Сохраняет исходное состояние свойства $section
	 *
	 * @var int
	 */
	private $origSection = null;

	/**
	 * Сохраняет исходное состояние свойства $cover
	 *
	 * @var bool
	 */
	private $origCover = null;

	/**
	 * Сохраняет исходное состояние свойства $active
	 *
	 * @var bool
	 */
	private $origActive = null;

	/**
	 * Создаёт или пересоздаёт миниатюру
	 *
	 * @param int $width
	 * @param int $height
	 *
	 * @return void
	 */
	public function buildThumb($width = null, $height = null)
	{
		/* @var Gallery $plugin */
		$plugin = $this->plugin;

		$filename = $this->getProperty('image');
		$thumb = PhpThumbFactory::create($plugin->getDataDir() . $filename);

		$thumb->resize(
			$width ? $width : $plugin->settings['thumbWidth'],
			$height ? $height : $plugin->settings['thumbHeight']);
		$thumb->save($plugin->getDataDir() . $this->getProperty('thumb'));
	}

	/**
	 * Действия перед сохранением
	 *
	 * @param ezcQuery $query
	 *
	 * @throws Gallery_Exception_FileTooBigException
	 * @throws Gallery_Exception_UnsupportedFormatException
	 */
	public function beforeSave(ezcQuery $query)
	{
		/* @var Gallery_Entity_Table_Image $table */
		$table = ORM::getTable($this->plugin, 'Image');

		/* При смене раздела флаг "Обложка" должен быть сброшен */
		if (!$this->id && $this->section != $this->origSection && $this->cover)
		{
			$this->cover = false;
			$table->autoSetCover($this->origSection);
		}

		/* Если это единственное изображение в разделе, делаем его обложкой */
		if ($table->countInSection($this->section, false) == 0)
		{
			$this->cover = true;
			// Нам не надо сбрасывать флаг у других изображений раздела, потому что их нет
			$this->setAsCover = false;
		}

		if ($this->setAsCover)
		{
			$table->clearCovers($this->section);
			$this->setAsCover = false;
		}

		/*
		 * Если отключается изображение-обложка, либо сбрасывается флаг "Обложка", то обложкой должно
		 * стать другое активное изображение в том же разделе.
		 */
		$isDisablingActiveCover =
			$this->cover == true && $this->active == false && $this->origActive == true;

		$isDroppingCoverFlag = $this->cover == false && $this->origCover == true;

		if ($isDisablingActiveCover || $isDroppingCoverFlag)
		{
			$table->autoSetCover($this->section);
			$this->cover = false;
		}

		/*
		 * Если включается изображение НЕ-обложка, то проверяем, не надо ли сделать его обложкой.
		 */
		if ($this->cover == false && $this->active == true && $this->origActive == false)
		{
			$tmp = $table->findCover($this->section);
			if ($tmp == false)
			{
				$this->cover = true;
			}
		}

		if (null === $this->upload)
		{
			return;
		}

		$fileInfo = $_FILES[$this->upload];
		if ($fileInfo['error'] == UPLOAD_ERR_NO_FILE)
		{
			return;
		}

		if ($fileInfo['error'] == UPLOAD_ERR_INI_SIZE)
		{
			throw new Gallery_Exception_FileTooBigException();
		}
	}

	/**
	 * Действия после сохранения
	 *
	 * @throws Gallery_Exception_UploadException
	 */
	public function afterSave()
	{
		if (null === $this->upload)
		{
			return;
		}

		$fileInfo = $_FILES[$this->upload];
		if ($fileInfo['error'] == UPLOAD_ERR_NO_FILE)
		{
			return;
		}

		$ext = strtolower(substr(strrchr($fileInfo['name'], '.'), 1));
		if ($ext == 'jpeg')
		{
			$ext = 'jpg';
		}

		/* @var Gallery $plugin */
		$plugin = $this->plugin;
		$imageFileName = $plugin->getDataDir() . $this->id . '.' . $ext;
		if (!upload($this->upload, $imageFileName))
		{
			throw new Gallery_Exception_UploadException();
		}

		$this->setProperty('image', $this->id . '.' . $ext);
		$this->setProperty('thumb', $this->id . '-thmb.' . $ext);

		/*
		 * Если изображение слишком больше - уменьшаем
		 */
		$info = @getimagesize($imageFileName);
		if (
			$info[0] > $plugin->settings['imageWidth'] ||
			$info[1] > $plugin->settings['imageHeight']
		)
		{
			$thumb = PhpThumbFactory::create($imageFileName);
			$thumb->resize($plugin->settings['imageWidth'], $plugin->settings['imageHeight']);
			filedelete($imageFileName);
			$thumb->save($imageFileName);
		}

		if ($plugin->settings['logoEnable'])
		{
			$this->overlayLogo($plugin->getDataDir() . $this->getProperty('image'));
		}

		$this->buildThumb();

		$this->upload = null;

		$table = ORM::getTable($this->plugin, 'Image');
		$table->update($this);
	}

	/**
	 * Действия после удаления
	 */
	public function afterDelete()
	{
		if ($this->cover)
		{
			/** @var Gallery_Entity_Table_Image $table  */
			$table = $this->getTable();
			$table->autoSetCover($this->section);
		}
	}

	/**
	 * Геттер свойства $group
	 *
	 * @return Gallery_Entity_Group|Gallery_NullObject
	 */
	protected function getGroup()
	{
		$table = ORM::getTable($this->plugin, 'Group');
		try
		{
			return $table->find($this->getProperty('groupId'));
		}
		catch (DomainException $e)
		{
			return new Gallery_NullObject();
		}
	}

	/**
	 * Геттер свойства $imageURL
	 *
	 * @return string
	 */
	protected function getImageURL()
	{
		return $this->plugin->getDataURL() . $this->getProperty('image');
	}

	/**
	 * Геттер свойства $thumbURL
	 *
	 * @return string
	 */
	protected function getThumbURL()
	{
		return $this->plugin->getDataURL() . $this->getProperty('thumb');
	}

	/**
	 * Геттер свойства $album
	 *
	 * @return Gallery_Entity_Album
	 */
	protected function getAlbum()
	{
		$table = ORM::getTable($this->plugin, 'Album');
		return $table->find($this->section);
	}

	/**
	 * Сеттер свойства $image
	 *
	 * @param string $value
	 */
	protected function setImage($value)
	{
		$this->upload = $value;
	}

	/**
	 * Сеттер свойства $cover
	 *
	 * @param int $value
	 */
	protected function setCover($value)
	{
		$this->setProperty('cover', $value);
		$this->setAsCover = $value;
	}

	/**
	 * Геттер свойства $showURL
	 *
	 * @return string
	 */
	protected function getShowURL()
	{
		/** @var Gallery $plugin */
		$plugin = $this->plugin;
		if ('popup' == $plugin->settings['showItemMode'])
		{
			$url = $this->imageURL . '#gallery-popup';
		}
		else
		{
			$url = $plugin->clientListURL() . $this->id . '/';
		}

		return $url;
	}

	/**
	 * Накладывает логотип
	 *
	 * @param string $file
	 *
	 * @throws Gallery_Exception_UnsupportedFormatException
	 * @throws LogicException
	 *
	 * @return void
	 */
	private function overlayLogo($file)
	{
		/* @var Gallery $plugin */
		$plugin = $this->plugin;

		$logoFile = $plugin->getDataDir() . 'logo.png';
		if (!is_file($logoFile))
		{
			eresus_log(__METHOD__, LOG_WARNING, 'No file %s', $logoFile);
			return;
		}

		$type = getimagesize($file);
		$type = $type[2];
		switch ($type)
		{
			case IMAGETYPE_PNG:
				$src = imageCreateFromPNG($file);
				break;
			case IMAGETYPE_JPEG:
				$src = imageCreateFromJPEG($file);
				break;
			case IMAGETYPE_GIF:
				$src = imageCreateFromGIF($file);
				break;
			default:
				throw new Gallery_Exception_UnsupportedFormatException($type);
		}
		imagealphablending($src, true);
		$logo = imageCreateFromPNG($logoFile);
		imagealphablending($logo, true);

		$settings =$plugin->settings;

		if ($logo)
		{
			$sw = imageSX($src);
			$sh = imageSY($src);
			$lw = imageSX($logo);
			$lh = imageSY($logo);

			switch ($settings['logoPosition'])
			{
				case 'TL':
					$x = $settings['logoHPadding'];
					$y = $settings['logoVPadding'];
					break;
				case 'TR':
					$x = $sw - $lw - $settings['logoHPadding'];
					$y = $settings['logoVPadding'];
					break;
				case 'BL':
					$x = $settings['logoHPadding'];
					$y = $sh - $lh - $settings['logoVPadding'];
					break;
				case 'BR':
					$x = $sw - $lw - $settings['logoHPadding'];
					$y = $sh - $lh - $settings['logoVPadding'];
					break;
				default:
					throw new LogicException('Invalid logo position');
			}
			imagesavealpha($src, true);
			imagecopy($src, $logo, $x, $y, 0, 0, $lw, $lh);
			imagesavealpha($src, true);
			switch ($type)
			{
				case IMAGETYPE_PNG:
					imagePNG($src, $file);
					break;
				case IMAGETYPE_JPEG:
					imageJPEG($src, $file);
					break;
				case IMAGETYPE_GIF:
					filedelete($file);
					$file = preg_replace('/gif$/', 'png', $file);
					imagePNG($src, $file);
					$this->setProperty('image', basename($file));
					$this->setProperty('thumb', preg_replace('/gif$/', 'png', $this->getProperty('thumb')));
					break;
			}
			imageDestroy($logo);
			imageDestroy($src);
		}
	}
}