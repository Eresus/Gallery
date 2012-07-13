<?php
/**
 * Автоматические тесты
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

require_once TESTS_SRC_ROOT . '/gallery/classes/Entity/Table/Album.php';

class Gallery_Entity_Table_Album_Test extends PHPUnit_Extensions_Database_TestCase
{
	/**
	 * @var PDO
	 */
	private $pdo = null;

	/**
	 * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	private $connection = null;

	/**
	 * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	public function getConnection()
	{
		if (null === $this->connection)
		{
			$this->pdo = new PDO('sqlite::memory:');
			$this->pdo->query('CREATE TABLE pages (id INTEGER, name TEXT, owner INTEGER, title TEXT, ' .
				'caption TEXT, description TEXT, hint TEXT, keywords TEXT, position INTEGER, ' .
				'active INTEGER, access INTEGER, visible INTEGER, template TEXT, type TEXT, ' .
				'content TEXT, options TEXT, created TEXT, updated TEXT)');
			$this->pdo->query('CREATE UNIQUE INDEX id ON pages (id);');
			$this->connection = $this->createDefaultDBConnection($this->pdo, ':memory:');
		}
		return $this->connection;
	}

	/**
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet()
	{
		return $this->createFlatXmlDataSet(__DIR__ . '/Album.fixtures/db.xml');
	}

	public function test_find()
	{
		//$table = $this->getMockForAbstractClass('Gallery_Entity_Table_Album');
		//$page = $table->find(1);
	}
}