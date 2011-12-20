<?php
namespace clear;

require_once __DIR__ . "/Core.php";

spl_autoload_register(__NAMESPACE__ .'\Core::autoload');

/**
 * index.php method
 * 
 * Single global function.  Gets to initial refernce to the
 * Core manages it as a Singleton.  Then proxies calls of Core::append_route()
 * Also registers Core::render_route() and the PHP engine 
 * shutdown function
 * 
 * @param string $route ex: "Get /hello/:name"  
 * @return Core
 */
function with($route)
{
	static $instance;
	if( ! $instance)
	{
		/*
		 * default template_engine is plain old php ("php"), to use twig, make
		 * this call
		 * Core::config('template_engine', "twig");
		 */
		$config['template_engine'] = "twig";
		/*
		 * twig requires a env config array
		 */
		$config['template_env'] = array(
			'cache' 			=> TOP_DIR."/cache_write/twig_cache",
			'auto_reload' 		=> true,
			'debug'				=> true,
			'strict_variables'	=> true,
			'autoescape'		=> true,
		);
		$config['template_dir'] = TOP_DIR."/templates";
		$config['cache_dir'] = TOP_DIR."/cache_write";
		$instance = new Core($_SERVER['REQUEST_METHOD'], $config);
		register_shutdown_function(
			/**
			 * this is where we will invoke the matched route
			 */
			function() use($instance)
			{
				$instance->render_route();
			}
		);
	}
	return $instance->append_route($route);
}