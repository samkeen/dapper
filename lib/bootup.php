<?php
namespace thundercats;

require __DIR__ . "/Core.php";

Core::templateDir(TOP_DIR."/templates");
Core::cacheDir(TOP_DIR."/cache_write");

spl_autoload_register(__NAMESPACE__ .'\Core::autoload');

require_once TOP_DIR . '/vendors/twig/lib/Twig/Autoloader.php';
\Twig_Autoloader::register();

function with($route)
{
	return Core::instance()->appendRoute($route);
}

/**
 * this is where we will invoke the matched route
 */
register_shutdown_function(
	function()
	{
		Core::instance()->doItLive();
	}
);

 
