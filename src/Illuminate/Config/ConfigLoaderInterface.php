<?php namespace Illuminate\Config;

interface ConfigLoaderInterface {

	/**
	 * Load the given configuration set.
	 *
	 * @param  string  $config
	 * @return array
	 */
	function load($config);

}