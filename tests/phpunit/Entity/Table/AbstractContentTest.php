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

require_once TESTS_SRC_ROOT . '/gallery/classes/Entity/Table/AbstractContent.php';

class Gallery_Entity_Table_AbstractContentTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Gallery_Entity_Table_AbstractContent::findInSection
	 */
	public function test_findInSection()
	{
		$q = $this->getMock('ezcQuerySelect', array('where', 'bindValue', 'orderBy'));
		$q->expects($this->exactly(2))->method('bindValue')->with(
			$this->logicalOr(123, true),
			null,
			$this->logicalOr(PDO::PARAM_INT, PDO::PARAM_BOOL)
		);
		$q->expects($this->once())->method('orderBy')->with('position');
		$expr = $this->getMock('stdClass', array('eq', 'lAnd'));
		$expr->expects($this->exactly(2))->method('eq')->with($this->logicalOr('section', 'active'));
		$q->expr = $expr;

		$table = $this->getMockBuilder('Gallery_Entity_Table_AbstractContent')->
			disableOriginalConstructor()->setMethods(array('createSelectQuery', 'loadFromQuery'))->
			getMock();
		$table->expects($this->once())->method('createSelectQuery')->will($this->returnValue($q));
		$table->expects($this->once())->method('loadFromQuery')->with($q, 10, 20)->
			will($this->returnValue('array'));

		$p_hasActiveField = new ReflectionProperty('Gallery_Entity_Table_AbstractContent',
			'hasActiveField');
		$p_hasActiveField->setAccessible(true);
		$p_hasActiveField->setValue($table, true);

		$this->assertEquals('array', $table->findInSection(123, 10, 20));
	}

	/**
	 * @covers Gallery_Entity_Table_AbstractContent::countInSection
	 */
	public function test_countInSection()
	{
		$q = $this->getMock('ezcQuerySelect', array('where', 'bindValue'));
		$q->expects($this->exactly(2))->method('bindValue')->with(
			$this->logicalOr(123, true),
			null,
			$this->logicalOr(PDO::PARAM_INT, PDO::PARAM_BOOL)
		);
		$expr = $this->getMock('stdClass', array('eq', 'lAnd'));
		$expr->expects($this->exactly(2))->method('eq')->with($this->logicalOr('section', 'active'));
		$q->expr = $expr;

		$table = $this->getMockBuilder('Gallery_Entity_Table_AbstractContent')->
			disableOriginalConstructor()->setMethods(array('createCountQuery', 'count'))->getMock();
		$table->expects($this->once())->method('createCountQuery')->will($this->returnValue($q));
		$table->expects($this->once())->method('count')->with($q)->will($this->returnValue(456));

		$p_hasActiveField = new ReflectionProperty('Gallery_Entity_Table_AbstractContent',
			'hasActiveField');
		$p_hasActiveField->setAccessible(true);
		$p_hasActiveField->setValue($table, true);

		$this->assertEquals(456, $table->countInSection(123));
	}

	/**
	 * @covers Gallery_Entity_Table_AbstractContent::hasColumns
	 */
	public function test_hasColumns()
	{
		$table = $this->getMockBuilder('Gallery_Entity_Table_AbstractContent')->
			disableOriginalConstructor()->setMethods(array('foo'))->getMock();

		$m_hasColumns = new ReflectionMethod('Gallery_Entity_Table_AbstractContent',
			'hasColumns');
		$m_hasColumns->setAccessible(true);

		$p_hasActiveField = new ReflectionProperty('Gallery_Entity_Table_AbstractContent',
			'hasActiveField');
		$p_hasActiveField->setAccessible(true);

		$m_hasColumns->invoke($table, array('foo' => array()));
		$this->assertFalse($p_hasActiveField->getValue($table));

		$m_hasColumns->invoke($table, array('active' => array()));
		$this->assertTrue($p_hasActiveField->getValue($table));
	}
}