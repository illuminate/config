<?php

class YamlLoaderTest extends PHPUnit_Framework_TestCase {

	public function testYamlLoadingUsingComponent()
	{
		$loader = new Illuminate\Config\YamlConfigLoader(__DIR__.'/files');
		$loader->component = true;
		$this->assertEquals(array('name' => 'taylor'), $loader->load('component'));
	}


	public function testYamlLoadingWithoutComponent()
	{
		$loader = new Illuminate\Config\YamlConfigLoader(__DIR__.'/files');
		$this->assertEquals(array('name' => 'taylor'), $loader->load('component'));
	}


	/**
	 * @expectedException Illuminate\Config\ConfigNotFoundException
	 */
	public function testYamlThrowsExceptionWhenNotFound()
	{
		$loader = new Illuminate\Config\YamlConfigLoader(__DIR__.'/files');
		$this->assertEquals(array('name' => 'taylor'), $loader->load('foo.yml'));	
	}

}