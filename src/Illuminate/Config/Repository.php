<?php namespace Illuminate\Config;

use ArrayAccess;
use Illuminate\Support\NamespacedItemResolver;

class Repository extends NamespacedItemResolver implements ArrayAccess {

	/**
	 * The loader implementation.
	 *
	 * @var Illuminate\Config\LoaderInterface 
	 */
	protected $loader;

	/**
	 * The current environment.
	 *
	 * @var string
	 */
	protected $environment;

	/**
	 * All of the configuration items.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Create a new configuration repository.
	 *
	 * @param  Illuminate\Config\LoaderInterface  $loader
	 * @param  string  $environment
	 * @return void
	 */
	public function __construct(LoaderInterface $loader, $environment)
	{
		$this->loader = $loader;
		$this->environment = $environment;
	}

	/**
	 * Determine if the given configuration value exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		$default = microtime(true);

		return $this->get($key, $default) != $default;
	}

	/**
	 * Get the specified configuration value.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		list($namespace, $group, $item) = $this->parseKey($key);

		// Configuration items are actually keyed by "collection", which is simply a
		// combination of each namespace and groups, which allows a unique way to
		// identify the arrays of configuration items for the particular files.
		$collection = $this->getCollection($group, $namespace);

		$this->load($group, $namespace, $collection);

		return array_get($this->items[$collection], $item, $default);
	}

	/**
	 * Set a given configuration value.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function set($key, $value)
	{
		list($namespace, $group, $item) = $this->parseKey($key);

		$collection = $this->getCollection($group, $namespace);

		// We'll need to go ahead and lazy load each configuration groups even when
		// we're just setting a configuration item so that the set item does not
		// get overwritten if a different item in the group is requested later.
		$this->load($group, $namespace, $collection);

		if (is_null($item))
		{
			$this->items[$collection] = $value;
		}
		else
		{
			array_set($this->items[$collection], $item, $value);
		}
	}

	/**
	 * Load the configuration group for the key.
	 *
	 * @param  string  $key
	 * @param  string  $namespace
	 * @param  string  $collection
	 * @return void
	 */
	protected function load($group, $namespace, $collection)
	{
		// If we've already loaded this collection, we will just bail out since we do
		// not want to load it again. Once items are loaded a first time they will
		// stay kept in memory within this class and not loaded from disk again.
		if (isset($this->items[$collection]))
		{
			return;
		}

		$items = $this->loader->load($this->environment, $group, $namespace);

		$this->items[$collection] = $items;
	}

	/**
	 * Parse an array of namespaced segments.
	 *
	 * @param  array  $segments
	 * @return array
	 */
	protected function parseNamespacedSegments(array $segments)
	{
		list($namespace, $group) = explode('::', $segments[0]);

		// If the group doesn't exist for the namespace, we'll assume it is the config
		// group so that any namespaces with just a single configuration file don't
		// have an awkward extra "config" identifier in each of their items keys.
		$item = null;

		if ($this->assumingGroup($segments, $group, $namespace))
		{
			list($item, $group) = array($group, 'config');
		}

		// If there is more than one segment, we will just slice off the first element
		// and combine the rest to get the item name, as it should be the remainder
		// of the segments after the group and namespace. Then, we'll return all.
		elseif (count($segments) > 1)
		{
			$item = implode('.', array_slice($segments, 1));
		}

		return array($namespace, $group, $item);
	}

	/**
	 * Determine if we should be assuming the configuration group.
	 *
	 * @param  array   $segments
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return bool
	 */
	protected function assumingGroup($segments, $group, $namespace)
	{
		return count($segments) == 1 and ! $this->loader->exists($group, $namespace);
	}

	/**
	 * Get the collection identifier.
	 *
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return string
	 */
	protected function getCollection($group, $namespace = null)
	{
		return $namespace ?: '*'.'::'.$group;
	}

	/**
	 * Add a new namespace to the loader.
	 *
	 * @param  string  $namespace
	 * @param  string  $hint
	 * @return void
	 */
	public function addNamespace($namespace, $hint)
	{
		return $this->loader->addNamespace($namespace, $hint);
	}

	/**
	 * Determine if the given configuration option exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->has($key);
	}

	/**
	 * Get a configuration option.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function offsetGet($key)
	{
		return $this->get($key);
	}

	/**
	 * Set a configuration option.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Unset a configuration option.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function offsetUnset($key)
	{
		$this->set($key, null);
	}

	/**
	 * Get the loader implementation.
	 *
	 * @return Illuminate\Config\LoaderInterface
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * Get all of the configuration items.
	 *
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}

}