<?php
/**
 * @package clear
 */
namespace clear;
require_once __DIR__ . "/Env.php";
spl_autoload_register(__NAMESPACE__ .'\Env::autoload');

$env = new Env();

/**
 * index.php method
 * 
 * Single global function.  Gets initial reference to the
 * Router and manages it as a Singleton.  Then proxies calls of Router::append_route()
 * Also registers Router::render() and the PHP engine 
 * shutdown function
 * 
 * @param string $route ex: "Get /hello/:name"  
 * @return Router
 */
function with($route)
{
    global $env;
	static $router_instance;
	if( ! $router_instance)
	{
		$router_instance = new Router(
            new Route( 
                $env->request_method(),
                $env->request_path(),
                $is_request_route = true
            )
        );
		register_shutdown_function(
			/**
			 * this is where we will invoke the matched route
			 */
			function() use($router_instance)
			{
                $render_engine = new Responder\TwigTemplateResponse(
                    array(
                        'cache' 			=> TOP_DIR."/cache_write/twig_cache",
                        'auto_reload' 		=> true,
                        'debug'				=> true,
                        'strict_variables'	=> true,
                        'autoescape'		=> true,
                    ),
                    $router_instance,
                    TOP_DIR."/templates",
                    TOP_DIR . '/vendors/twig/lib/Twig/Autoloader.php'
                );
                $render_engine->complete();
			}
		);
	}
	return $router_instance->append_route($route);
}