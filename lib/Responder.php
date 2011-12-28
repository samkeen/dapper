<?php
/**
 * @package clear
 */
namespace clear;
/**
 * Original Author: sam
 * Date: 12/24/11
 * Time: 9:52 AM
 */

/**
 * Responder recieves a Route and them compiles a reponse to that Route
 * Possible responses (always a string of some sort):
 * - http HTML rendered page
 * - http JOSN data
 * - http 404
 * - simple string for response to commandline client.
 * 
 * @package clear
 */
class Responder
{
    /**
     * @var \clear\Router
     */
    private $router;
    /**
     * @var array
     */
    private $config = array(
   		'template_engine' => 'php',
   		'template_env' => array(),
   		'template_dir' => null,
   		'cache_dir' => null
   	);
        
    function __construct($config, Router $router)
    {
        $this->config = array_merge($this->config, $config);
        if($this->config['template_engine']=="twig")
        {
            require_once TOP_DIR . '/vendors/twig/lib/Twig/Autoloader.php';
            \Twig_Autoloader::register();
        }
        $this->router = $router;
    }
    
    function init()
    {
        
    }
    
    function digest()
    {
        
    }

    
    /**
     * 
     */
    function render()
    {
        if($route = $this->router->match_route())
        {
            $view_name = $route->view_name();
            $template_payload = $this->router_extract_payload($route);
            $this->render_view($view_name, $template_payload);
        }
        else
        {
            $requested_route = $this->router->requested_route();
            echo "<pre>\n";
            echo "Requested Route [{$requested_route->http_method()} {$requested_route->path()}] not found\n";
            echo "Known Routes:\n";
            echo print_r($this->router->learned_routes(), true);
            echo "\n</pre>";
        }
    }

    /**
     * @param $view_name
     * @param array $template_payload
     * @throws \Exception
     */
    private function render_view($view_name, array $template_payload=array())
    {
        if($this->config['template_engine']=='twig')
        {
            $twig = $this->init_twig();
            echo $twig->render("content/{$view_name}.htm.twig", $template_payload);
        } 
        else if($this->config['template_engine']=='php')
        {
            extract($template_payload);
            include __DIR__ . $this->config['template_dir']."/{$view_name}.htm.php";
        }
        else
        {
            throw new \Exception("Unknown template engine: [".$this->config['template_engine']."]");
        }
    }
    
    /**
     * @return \Twig_Environment
     */
    private function init_twig()
    {
        return new \Twig_Environment(
            new \Twig_Loader_Filesystem($this->config['template_dir']),
            $this->config['template_env']
        );
    }
    
    

}
