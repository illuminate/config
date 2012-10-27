<?php namespace Illuminate\Config;

class ApcFileLoader extends FileLoader {

	/**
	 * Load the given configuration group.
	 *
	 * @param  string  $environment
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	public function load($environment, $group, $namespace = null)
	{
		$key = $environment.$group.$namespace;

		if ($this->apcExists($key)) return $this->apcFetch($key);

		return $this->loadFromFile($environment, $group, $namespace);
	}

	/**
	 * Load the configuration from the file.
	 *
	 * @param  string  $environment
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	protected function loadFromFile($environment, $group, $namespace)
	{
		$config = $this->callParentLoad($environment, $group, $namespace);

		$this->apcStore($environment.$group.$namespace, $config);

		return $config;
	}

	/**
	 * Call the parent load function.
	 *
	 * @param  string  $environment
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	protected function callParentLoad($environment, $group, $namespace)
	{
		return parent::load($environment, $group, $namespace);
	}

	/**
	 * Determine if a key exists in APC.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	protected function apcExists($key)
	{
		return apc_exists($key);
	}

	/**
	 * Retrieve a value from APC.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected function apcFetch($key)
	{
		return apc_fetch($key);
	}

	/**
	 * Store a value in APC.
	 *
	 * @param  string  $key
	 * @param  array   $value
	 * @return void
	 */
	protected function apcStore($key, $value)
	{
		apc_store($key, $value);
	}

}