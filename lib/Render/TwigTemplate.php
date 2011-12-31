<?php
/**
 * Original Author: sam
 * Date: 12/30/11
 * Time: 4:36 PM
 * 
 * @package clear
 * @subpackage Render
 * 
 */
namespace clear\Render;

/**
 * Utilizes the Twig template engine
 * 
 * @package clear
 * @subpackage Render 
 */
class TwigTemplate extends  BaseRender
{
    private $templates_dir;
    private $content_dir_name = 'content';
    
    function __construct($config, $templates_dir, $auto_loader_path=null)
    {
        parent::__construct($config);
        if($auto_loader_path)
        {
            require_once $auto_loader_path;
            \Twig_Autoloader::register();
        }
        $this->templates_dir = $templates_dir;
        $this->view_file_suffix = '.htm.twig';
    }
    
    function render_view($view_name, array $template_payload=array())
    {
        $twig = $this->init();
        $view_file_path = "{$this->content_dir_name}/{$view_name}{$this->view_file_suffix}";
        echo $twig->render($view_file_path, $template_payload);
    }

    /**
     * @param int $error_code
     * @param string $error_message
     */
    function render_error($error_code, $error_message)
    {
        echo '<div class="error">'
            . "\n<h3>Erorr</h3><pre>\n{$error_message}\n</div>\n</pre>\n";
    }

    private function init()
    {
        return new \Twig_Environment(
            new \Twig_Loader_Filesystem($this->templates_dir),
            $this->config
        );
    }

}
