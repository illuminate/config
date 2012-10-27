<?php namespace Illuminate\Config;

use Illuminate\Filesystem;

class FileLoader implements LoaderInterface {

	/**
	 * The filesystem instance.
	 *
	 * @var Illuminate\Filesystem
	 */
	protected $files;

	/**
	 * The default configuration path.
	 *
	 * @var string
	 */
	protected $defaultPath;

	/**
	 * All of the named path hints.
	 *
	 * @var array
	 */
	protected $hints = array();

	/**
	 * Create a new file configuration loader.
	 *
	 * @param  Illuminate\Filesystem  $files
	 * @param  string  $defaultPath
	 * @return void
	 */
	public function __construct(Filesystem $files, $defaultPath)
	{
		$this->files = $files;
		$this->defaultPath = $defaultPath;
	}

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
		$items = array();

		// First we'll get the root configuration path for the environment which is
		// where all of the configuration files live for that namespace, as well
		// as any environment folders with their specific configuration items.
		$path = $this->getPath($namespace);

		if (is_null($path))
		{
			return $items;
		}

		// First we'll get the main configuration file for the groups. Once we have
		// that we can check for any environment specific files, which will get
		// merged on top of the main arrays to make the environments cascade.
		$file = "{$path}/{$group}.php";

		if ($this->files->exists($file))
		{
			$items = $this->files->getRequire($file);
		}

		// Finally we're ready to check for the environment specific configuration
		// file which will be merged on top of the main arrays so that they get
		// precedence over them if we are currently in an environments setup.
		$file = "{$path}/{$environment}/{$group}.php";

		if ($this->files->exists($file))
		{
			$items = array_merge($items, $this->files->getRequire($file));
		}

		return array_dot($items);
	}

	/**
	 * Determine if the given group exists.
	 *
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return bool
	 */
	public function exists($group, $namespace = null)
	{
		$path = $this->getPath($namespace);

		// To check if a group exists, we will simply get the path based on the
		// namespace, and then check to see if this files exists within that
		// namespace. False is returned if no path exists for a namespace.
		$file = "{$path}/{$group}.php";

		return ! is_null($path) and $this->files->exists($file);
	}

	/**
	 * Get the configuration path for a namespace.
	 *
	 * @param  string  $namespace
	 * @return string
	 */
	protected function getPath($namespace)
	{
		if (is_null($namespace))
		{
			return $this->defaultPath;
		}
		elseif (isset($this->hints[$namespace]))
		{
			return $this->hints[$namespace];
		}
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
		$this->hints[$namespace] = $hint;
	}

	/**
	 * Get the Filesystem instance.
	 *
	 * @return Illuminate\Filesystem
	 */
	public function getFilesystem()
	{
		return $this->files;
	}

}