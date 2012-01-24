<?php
/**
 * @package dapper
 */
namespace dapper;
require_once __DIR__ . "/Env.php";
spl_autoload_register(__NAMESPACE__ .'\Env::autoload');

$env = new Env($writatble_dir = realpath(__DIR__."/../cache_write"), $is_dev = true);

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
        /**
         * We use a register_shutdown_function to simplify the front controller (index.php)
         * With the shutdown function in place there is no need for an explicit method call
         * at the end of index.php.
         */
        register_shutdown_function(
            /**
             * this is where we will invoke the matched route
             */
            function() use($router_instance, $env)
            {
                $top_dir = realpath(__DIR__."/..");
                $render_strategy = new TemplateEngine\Twig(
                    array(
                        'cache'             => "{$top_dir}/cache_write/twig_cache",
                        'auto_reload'         => true,
                        'debug'                => true,
                        'strict_variables'    => true,
                        'autoescape'        => true,
                    ),
                    "{$top_dir}/templates",
                    "{$top_dir}/vendors/twig/lib/Twig/Autoloader.php"
                );
                /**
                 * @TODO Need to determine Responder Type at runtime
                 */
                $responder = new Responder\HttpResponder(
                    $router_instance,
                    $render_strategy,
                    $env
                );
                $responder->complete();
            }
        );
    }
    /**
     * before we append another Route, see if the last appended
     * Route is a match, if so, stop appending routes and exit through
     * the registered shutdown function
     */
    if($router_instance->match_route())
    {
        exit();
    }
    return $router_instance->append_route($route);
}