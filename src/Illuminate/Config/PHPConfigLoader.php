<?php namespace Illuminate\Config;

class PHPConfigLoader implements ConfigLoaderInterface {

	/**
	 * The path to load the PHP files from.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new PHP configuration loader.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Load the given configuration set.
	 *
	 * @param  string  $config
	 * @return array
	 */
	public function load($config)
	{
		if ( ! file_exists($path = $this->path.'/'.$config.'.php'))
		{
			throw new ConfigNotFoundException($config);
		}

		return require $path;
	}

}