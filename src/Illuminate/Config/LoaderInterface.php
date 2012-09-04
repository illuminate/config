<?php namespace Illuminate\Config;

interface LoaderInterface {

	/**
	 * Load the given configuration group.
	 *
	 * @param  string  $environment
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	public function get($environment, $group, $namespace = null);

	/**
	 * Determine if the given group exists.
	 *
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return bool
	 */
	public function groupExists($group, $namespace = null);

	/**
	 * Add a new named path to the loader.
	 *
	 * @param  string  $name
	 * @param  string  $path
	 * @return void
	 */
	public function addNamedPath($name, $path);

}