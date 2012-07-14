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

require_once TESTS_SRC_ROOT . '/gallery/classes/Entity/Image.php';

class Gallery_Entity_Image_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Gallery_Entity_Image::getOwner
	 */
	public function test_adminImageToggle()
	{
		$image = $this->getMockBuilder('Gallery_Entity_Image')->
			setMethods(array('getGroup', 'getAlbum'))->disableOriginalConstructor()->getMock();

		$image->expects($this->once())->method('getGroup')->will($this->returnValue('group'));
		$image->expects($this->once())->method('getAlbum')->will($this->returnValue('album'));

		$getOwner = new ReflectionMethod('Gallery_Entity_Image', 'getOwner');
		$getOwner->setAccessible(true);

		$plugin = new ReflectionProperty('Gallery_Entity_Image', 'plugin');
		$plugin->setAccessible(true);

		$gallery = new stdClass();
		$plugin->setValue($image, $gallery);

		$gallery->settings = array('useGroups' => false);
		$this->assertEquals('album', $getOwner->invoke($image));

		$gallery->settings['useGroups'] = true;
		$this->assertEquals('group', $getOwner->invoke($image));
	}
}