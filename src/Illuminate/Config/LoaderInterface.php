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
	 * Add a hint for locating namespaces.
	 *
	 * @param  string  $namespace
	 * @param  string  $hintPath
	 * @return void
	 */
	public function addNamespaceHint($namespace, $hintPath);

}