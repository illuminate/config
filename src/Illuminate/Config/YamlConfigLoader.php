<?php namespace Illuminate\Config;

class YamlConfigLoader implements ConfigLoaderInterface {

	/**
	 * The path to load the YAML files from.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Indicates if the Symfony YAML component should be used.
	 *
	 * @var bool
	 */
	public $component = false;

	/**
	 * Create a new YAML configuration loader.
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
		if ( ! file_exists($path = $this->path.'/'.$config.'.yml'))
		{
			throw new ConfigNotFoundException($config);
		}

		$yaml = file_get_contents($path);

		// If the YAML PHP extension is installed we will use that as it should be
		// faster than the Symfony component. If it isn't present, we will fall
		// back on the component as that should work on any PHP environment.
		if (function_exists('yaml_parse') and ! $this->component)
		{
			return yaml_parse($yaml);
		}
		else
		{
			$parser = new \Symfony\Component\Yaml\Parser;

			return $parser->parse($yaml);
		}
	}

}