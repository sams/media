<?php
/**
 * Imagick Medium Adapter Test Case File
 *
 * Copyright (c) 2007-2008 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5
 * CakePHP version 1.2
 *
 * @package    media
 * @subpackage media.tests.cases.libs.medium.adapter
 * @author     David Persson <davidpersson@qeweurope.org>
 * @copyright  2007-2008 David Persson <davidpersson@qeweurope.org>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/davidpersson/media
 */
App::import('Vendor','Media.ImageMedium', array('file' => 'medium'.DS.'image.php'));
App::import('Vendor','Media.DocumentMedium', array('file' => 'medium'.DS.'document.php'));
App::import('Vendor','ImagickMediumAdapter', array('file' => 'medium'.DS.'adapter'.DS.'imagick.php'));
require_once dirname(__FILE__) . DS . '..' . DS . '..' . DS . '..' . DS . '..' . DS . 'fixtures' . DS . 'test_data.php';
/**
 * Test Imagick Image Medium Class
 *
 * @package    media
 * @subpackage media.tests.cases.libs.medium.adapter
 */
class TestImagickImageMedium extends ImageMedium {
	var $adapters = array('Imagick');
}
/**
 * Test Imagick Document Medium Class
 *
 * @package    media
 * @subpackage media.tests.cases.libs.medium.adapter
 */
class TestImagickDocumentMedium extends DocumentMedium {
	var $adapters = array('Imagick');
}
/**
 * Imagick Medium Adapter Test Case Class
 *
 * @package    media
 * @subpackage media.tests.cases.libs.medium.adapter
 */
class ImagickMediumAdapterTest extends CakeTestCase {
	function start() {
		parent::start();
		$this->TestData = new MediumTestData();
	}

	function end() {
		parent::end();
		$this->TestData->flushFiles();
	}

	function skip()
	{
		$this->skipUnless(extension_loaded('gd'), 'GD extension not loaded');
	}

	function showImage($string, $mimeType = null) {
		echo '<img src="data:'.$mimeType.';base64,'.base64_encode($string).'" />';
	}

	function testBasic() {
		$result = new TestImagickImageMedium($this->TestData->getFile('image-jpg.jpg'));
		$this->assertIsA($result, 'object');

		$Medium = new TestImagickImageMedium($this->TestData->getFile('image-jpg.jpg'));
		$result = $Medium->toString();
		$this->assertTrue(!empty($result));
	}

	function testInformation() {
		$Medium = new TestImagickImageMedium($this->TestData->getFile('image-jpg.jpg'));

		$result = $Medium->width();
		$this->assertEqual($result, 70);

		$result = $Medium->height();
		$this->assertEqual($result, 47);
	}

	function testManipulation() {
		$Medium = new TestImagickImageMedium($this->TestData->getFile('image-jpg.jpg'));
		$Medium->fit(10,10);
		$this->assertTrue($Medium->width() <= 10);
		$this->assertTrue($Medium->height() <= 10);

		$Medium = new TestImagickImageMedium($this->TestData->getFile('image-jpg.jpg'));
		$Medium = $Medium->convert('image/png');
		$result = $Medium->mimeType;
		$this->assertEqual($result, 'image/png');

		$Medium = new TestImagickImageMedium($this->TestData->getFile('image-jpg.jpg'));
		$Medium = $Medium->convert('image/png');
		$tmpFile = TMP . uniqid('test_suite_');
		$tmpFile = $Medium->store($tmpFile);
		$this->assertEqual(MimeType::guessType($tmpFile), 'image/png');
		unlink($tmpFile);
	}

	function testMake() {
		$instructions = array('convert' => 'image/png','zoomCrop' => array(10, 10));
		$Medium = TestImagickImageMedium::make($this->TestData->getFile('image-jpg.jpg'), $instructions);
		$this->assertIsA($Medium, 'Medium');
//		$this->showImage($Medium->toString(),'image/jpg');
	}

}
?>