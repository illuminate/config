<?php

use Mockery as m;

class RepositoryTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testGetReturnsBasicItems()
	{
		$config = $this->getRepository();
		$options = $this->getDummyOptions();
		$config->getLoader()->shouldReceive('load')->once()->with('app', null)->andReturn($options);

		$this->assertEquals('bar', $config->get('app.foo'));
		$this->assertEquals('breeze', $config->get('app.baz.boom'));
		$this->assertEquals('blah', $config->get('app.code', 'blah'));
		$this->assertEquals('blah', $config->get('app.code', function() { return 'blah'; }));
	}


	public function testEntireArrayCanBeReturned()
	{
		$config = $this->getRepository();
		$options = $this->getDummyOptions();
		$config->getLoader()->shouldReceive('load')->once()->with('app', null)->andReturn($options);

		$this->assertEquals($options, $config->get('app'));
	}


	public function testLoaderGetsCalledCorrectForNamespaces()
	{
		$config = $this->getRepository();
		$options = $this->getDummyOptions();
		$config->getLoader()->shouldReceive('load')->once()->with('options', 'namespace')->andReturn($options);

		$this->assertEquals('bar', $config->get('namespace::options.foo'));
		$this->assertEquals('breeze', $config->get('namespace::options.baz.boom'));
		$this->assertEquals('blah', $config->get('namespace::options.code', 'blah'));
		$this->assertEquals('blah', $config->get('namespace::options.code', function() { return 'blah'; }));
	}


	protected function getRepository()
	{
		return new Illuminate\Config\Repository(m::mock('Illuminate\Config\LoaderInterface'));
	}


	protected function getDummyOptions()
	{
		return array('foo' => 'bar', 'baz' => array('boom' => 'breeze'));
	}

}