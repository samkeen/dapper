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
    
    private $config = array(
   		'template_engine' => 'php',
   		'template_env' => array(),
   		'template_dir' => null,
   		'cache_dir' => null
   	);
        
    function __construct($config)
    {
        $this->config = array_merge($this->config, $config);
        if($this->config['template_engine']=="twig")
        {
            require_once TOP_DIR . '/vendors/twig/lib/Twig/Autoloader.php';
            \Twig_Autoloader::register();
        }
    }
    
    function init()
    {
        
    }
    
    function digest()
    {
        
    }
    
    function render()
    {
        
    }
    
    /**
     * @param Route|null $route
     */
    function render_route(Route $route = null)
    {
        if($route)
        {
            $this->render_view($route);
        }
        else
        {
            echo "<pre>\n";
            echo "Requested Route [{$this->requested_route->http_method()} {$this->requested_route->path()}] not found\n";
            echo "Known Routes:\n";
            echo print_r($this->routes, true);
            echo "\n</pre>";
        }
    }
    
    /**
     * @param Route $route
     * @throws \Exception
     */
    private function render_view(Route $route)
    {
        $view_name = $route->view_name();
        $template_payload = $route->template_payload();
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
