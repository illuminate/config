<?php

class PHPLoaderTest extends PHPUnit_Framework_TestCase {

	public function testPHPLoading()
	{
		$loader = new Illuminate\Config\PHPConfigLoader(__DIR__.'/files');
		$this->assertEquals(array('name' => 'taylor'), $loader->load('config'));
	}


	/**
	 * @expectedException Illuminate\Config\ConfigNotFoundException
	 */
	public function testPHPThrowsExceptionWhenNotFound()
	{
		$loader = new Illuminate\Config\PHPConfigLoader(__DIR__.'/files');
		$this->assertEquals(array('name' => 'taylor'), $loader->load('foo.php'));
	}

}