<?php
/**
 * Альбом
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
 * $Id$
 */


/**
 * Альбом
 *
 * Только для КИ!
 *
 * Альбом — это список изображений в определённом разделе сайта
 *
 * @package Gallery
 * @since 2.03
 */
class Gallery_Album implements Iterator, Countable
{
	/**
	 * Идентификатор раздела сайта, которому соответствует альбом
	 *
	 * @var int
	 */
	protected $sectionId;

	/**
	 * Массив изображений
	 *
	 * @var array
	 * @see Iterator
	 */
	protected $items = array();

	/**
	 * Указатель на текущее изображение
	 *
	 * @var int
	 * @see Iterator
	 */
	protected $position = 0;

	/**
	 * Текущее (просматриваемое посетителем) изображение
	 *
	 * @var GalleryImage
	 */
	protected $current;

	/**
	 * Следующее (относительно просматриваемого посетителем) изображение
	 *
	 * @var GalleryImage
	 */
	protected $next;

	/**
	 * Предыдущее (относительно просматриваемого посетителем) изображение
	 *
	 * @var GalleryImage
	 */
	protected $prev;

	/**
	 * Показывает, загружены ли объекты из БД
	 *
	 * @var bool
	 */
	protected $loaded = false;

	/**
	 * Создаёт новый альбом на основе заданного раздела сайта
	 *
	 * @param int $sectionId  идентификатор раздела сайта
	 *
	 * @return Gallery_Album
	 *
	 * @since 2.03
	 */
	public function __construct($sectionId)
	{
		$this->sectionId = $sectionId;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает "магические" свойства
	 *
	 * @param string $property
	 *
	 * @return mixed
	 *
	 * @since 2.03
	 */
	public function __get($property)
	{
		switch ($property)
		{
			case 'next':
				return $this->getNext();
			break;

			case 'prev':
				return $this->getPrev();
			break;
		}

		return null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		$this->load();
		$this->position = 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Iterator::current()
	 */
	public function current()
	{
		$this->load();
		return $this->items[$this->position];
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Iterator::key()
	 */
	public function key()
	{
		return $this->position;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Iterator::next()
	 */
	public function next()
	{
		++$this->position;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Iterator::valid()
	 */
	public function valid()
	{
		$this->load();
		return isset($this->items[$this->position]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Countable::count()
	 */
	public function count()
	{
		$this->load();
		return count($this->items);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает изображение альбома в качестве текущего
	 *
	 * @param GalleryImage $image
	 *
	 * @return void
	 *
	 * @since 2.03
	 */
	public function setCurrent(GalleryImage $image)
	{
		$this->current = $image;
		$this->next = -1;
		$this->prev = -1;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Загружет объекты из БД, если они не были загружены ранее
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
		$this->items = GalleryImage::find($this->sectionId, null, null, true);
		$this->loaded = true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает следующее изображение в альбоме или null.
	 *
	 * @return GalleryImage|null
	 *
	 * @since 2.03
	 */
	protected function getNext()
	{
		if (!$this->current)
		{
			return null;
		}
		if ($this->next !== -1)
		{
			return $this->next;
		}
		$this->load();
		for ($i = 0; $i < count($this->items); $i++)
		{
			if ($this->items[$i]->id == $this->current->id)
			{
				if ($i + 1 < count($this->items))
				{
					$this->next = $this->items[$i + 1];
					return $this->next;
				}
				break;
			}
		}
		return null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает предыдущее изображение в альбоме или null.
	 *
	 * @return GalleryImage|null
	 *
	 * @since 2.03
	 */
	protected function getPrev()
	{
		if (!$this->current)
		{
			return null;
		}
		if ($this->prev !== -1)
		{
			return $this->prev;
		}
		$this->load();
		for ($i = 0; $i < count($this->items); $i++)
		{
			if ($this->items[$i]->id == $this->current->id)
			{
				if ($i > 0)
				{
					$this->prev = $this->items[$i - 1];
					return $this->prev;
				}
				break;
			}
		}
		return null;
	}
	//-----------------------------------------------------------------------------
}
