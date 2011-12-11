<?php
namespace clear;

require __DIR__ . "/Core.php";

Core::config('template_dir', TOP_DIR."/templates");
Core::config('cache_dir', TOP_DIR."/cache_write");

spl_autoload_register(__NAMESPACE__ .'\Core::autoload');

require_once TOP_DIR . '/vendors/twig/lib/Twig/Autoloader.php';
\Twig_Autoloader::register();

/**
 * index.php method
 * 
 * Single global function.  Gets to initial refernce to the
 * Core singleton and then proxies calls of Core::append_route()
 * 
 * @param $route ex: "Get /hello/:name"  
 * @return Core
 */
function with($route)
{
	return Core::instance()->append_route($route);
}

/**
 * this is where we will invoke the matched route
 */
register_shutdown_function(
	function()
	{
		Core::instance()->render_route();
	}
);

 
