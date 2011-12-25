<?php
/**
 * @package clear
 */
namespace clear;
require_once __DIR__ . "/Env.php";
spl_autoload_register(__NAMESPACE__ .'\Env::autoload');

/**
 * index.php method
 * 
 * Single global function.  Gets initial reference to the
 * Router and manages it as a Singleton.  Then proxies calls of Router::append_route()
 * Also registers Router::render_route() and the PHP engine 
 * shutdown function
 * 
 * @param string $route ex: "Get /hello/:name"  
 * @return Router
 */
function with($route)
{
	static $instance;
	if( ! $instance)
	{
		/*
		 * default template_engine is plain old php ("php"), to use twig, make
		 * this call
		 * Router::config('template_engine', "twig");
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
		$instance = new Router($_SERVER['REQUEST_METHOD'], $config);
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