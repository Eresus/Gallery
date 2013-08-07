<?php
/**
 * Заглушки встроенных классов Eresus
 *
 * @package Eresus
 * @subpackage Tests
 */

use Mekras\TestDoubles\UniversalStub;
use Mekras\TestDoubles\MockFacade;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Заглушка для класса Eresus_Plugin
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Plugin extends UniversalStub
{
}

/**
 * Заглушка для класса ContentPlugin
 *
 * @package Eresus
 * @subpackage Tests
 */
class ContentPlugin extends Eresus_Plugin
{
}

/**
 * Заглушка для класса Eresus_Kernel
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Kernel extends MockFacade
{
}

/**
 * Заглушка для класса Eresus_CMS
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS extends MockFacade
{
}

/**
 * Заглушка для класса Eresus_CMS_Exception
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_Exception extends Exception
{
}

/**
 * Заглушка для класса Eresus_DB
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_DB extends MockFacade
{
}

/**
 * Заглушка для класса ezcQuery
 *
 * @package Eresus
 * @subpackage Tests
 */
class ezcQuery extends UniversalStub
{
}

/**
 * Заглушка для класса ezcQuerySelect
 *
 * @package Eresus
 * @subpackage Tests
 */
class ezcQuerySelect extends ezcQuery
{
    const ASC = 'ASC';
    const DESC = 'DESC';
}

/**
 * Абстрактная сущность
 *
 * @package ORM
 * @since 1.00
 */
abstract class ORM_Entity
{
	/**
	 * Модуль
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Атрибуты
	 *
	 * @var array
	 */
	private $attrs = array();

	/**
	 * Кэш геттеров
	 *
	 * @var array
	 */
	private $gettersCache = array();

	public function __construct(Eresus_Plugin $plugin, array $attrs = array())
	{
		$this->plugin = $plugin;
		$this->attrs = $attrs;
	}

	public function __get($key)
	{
		$getter = 'get' . $key;
		if (method_exists($this, $getter))
		{
			if (!isset($this->gettersCache[$key]))
			{
				$this->gettersCache[$key] = $this->$getter();
			}
			return $this->gettersCache[$key];
		}

		return $this->getProperty($key);
	}

	public function __set($key, $value)
	{
		$setter = 'set' . $key;
		if (method_exists($this, $setter))
		{
			$this->$setter($value);
		}
		else
		{
			$this->setProperty($key, $value);
		}
	}

	/**
	 * Возвращает таблицу этой сущности
	 *
	 * @return ORM_Table
	 *
	 * @since 1.00
	 */
	public function getTable()
	{
		$entityName = get_class($this);
		$entityName = substr($entityName, strrpos($entityName, '_') + 1);
		return ORM::getTable($this->plugin, $entityName);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает значение свойства
	 *
	 * Метод не инициирует вызов сеттеров, но обрабатывает значение фильтрами
	 *
	 * @param string $key    Имя свойства
	 * @param mixed  $value  Значение
	 *
	 * @return void
	 *
	 * @uses PDO
	 * @since 1.00
	 */
	public function setProperty($key, $value)
	{
		$this->attrs[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает значение свойства
	 *
	 * Читает значение непосредственно из массива свойств, не инициируя вызов геттеров
	 *
	 * @param string $key  имя свойства
	 *
	 * @return mixed  значение свойства
	 *
	 * @since 1.00
	 */
	public function getProperty($key)
	{
		if (isset($this->attrs[$key]))
		{
			return $this->attrs[$key];
		}

		return null;
	}
	//-----------------------------------------------------------------------------

	//@codeCoverageIgnoreStart
	/**
	 * Вызывается перед изменением в БД
	 *
	 * @param ezcQuery $query  запрос, который будет выполнен для сохранения записи
	 *
	 * @return void
	 *
	 * @since 1.00
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function beforeSave(ezcQuery $query)
	{
	}
	//@codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	//@codeCoverageIgnoreStart
	/**
	 * Вызывается после записи изменений в БД
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	public function afterSave()
	{
	}
	//@codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	//@codeCoverageIgnoreStart
	/**
	 * Вызывается перед удалением записи из БД
	 *
	 * @param ezcQuery $query  запрос, который будет выполнен для удаления записи
	 *
	 * @return void
	 *
	 * @since 1.00
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function beforeDelete(ezcQuery $query)
	{
	}
	//@codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	//@codeCoverageIgnoreStart
	/**
	 * Вызывается после удаления записи из БД
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	public function afterDelete()
	{
	}
	//@codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}

class ORM extends MockFacade {}

class ORM_Table
{
	protected function hasColumns(array $columns)
	{
	}
}

class HTTP extends MockFacade {}

function arg($arg)
{
    return @$GLOBALS['args'][$arg];
}