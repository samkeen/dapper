<?php
namespace clear;

/**
 * Initiate the system
 * Pull in Core and pass key Env values to it.  
 * Set up auto loader
 */
require __DIR__ . "/Core.php";

/*
 * default template_engine is plain old php ("php"), to use twig, make
 * this call
 * Core::config('template_engine', "twig");
 */
Core::config('template_engine', "twig");
/*
 * twig requires a env config array
 */
Core::config('template_env',array(
	'cache' 			=> TOP_DIR."/cache_write/twig_cache",
	'auto_reload' 		=> true,
	'debug'				=> true,
	'strict_variables'	=> true,
	'autoescape'		=> true,
));
Core::config('template_dir', TOP_DIR."/templates");
Core::config('cache_dir', TOP_DIR."/cache_write");

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
		$instance = new Core($_SERVER['REQUEST_METHOD']);
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