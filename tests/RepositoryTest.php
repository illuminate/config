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


	public function testLoaderUsesConfigGroupInNamespaceAsDefault()
	{
		$config = $this->getRepository();
		$options = $this->getDummyOptions();
		$config->getLoader()->shouldReceive('load')->once()->with('config', 'namespace')->andReturn($options);
		$config->getLoader()->shouldReceive('groupExists')->once()->with('foo', 'namespace')->andReturn(false);

		$this->assertEquals('bar', $config->get('namespace::foo'));
	}


	public function testLoaderUsesGroupWhenItExists()
	{
		$config = $this->getRepository();
		$options = $this->getDummyOptions();
		$config->getLoader()->shouldReceive('load')->once()->with('foo', 'namespace')->andReturn($options);
		$config->getLoader()->shouldReceive('groupExists')->once()->with('foo', 'namespace')->andReturn(true);

		$this->assertEquals($options, $config->get('namespace::foo'));
		$this->assertEquals('bar', $config->get('namespace::foo.foo'));
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