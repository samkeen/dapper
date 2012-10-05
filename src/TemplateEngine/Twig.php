<?php
/**
 * Original Author: sam
 * Date: 12/30/11
 * Time: 4:36 PM
 * 
 * @package dapper
 * @subpackage TemplateEngine
 * 
 */
namespace dapper\TemplateEngine;

/**
 * Utilizes the Twig template engine
 * 
 * @package dapper
 * @subpackage TemplateEngine 
 */
class Twig extends  BaseTemplateEngine
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
        $this->view_file_extension = 'twig';
    }
    /**
     * @param string $view_name
     * @param string $format
     * @param array $template_payload
     * 
     * @return string
     */
    function templatize($view_name, $format, array $template_payload=array())
    {
        $templated_content = null;
        $content_template_path = 
            "{$this->content_dir_name}/{$view_name}.{$format}.{$this->view_file_extension}";
        if( ! file_exists("{$this->templates_dir}/{$content_template_path}"))
        {
            $templated_content = $template_payload;  
        }
        else
        {
            $twig = $this->init();
            $templated_content = $twig->render($content_template_path, $template_payload);
        }
        return $templated_content;
    }
    /**
     * @param int $error_code
     * @param string $error_message
     * 
     * @return string
     */
    function templatize_error($error_code, $error_message)
    {
        return '<div class="error">'
            . "\n<h3>Erorr</h3><pre>\n{$error_message}\n</pre>\n</div>\n";
    }

    private function init()
    {
        return new \Twig_Environment(
            new \Twig_Loader_Filesystem($this->templates_dir),
            $this->config
        );
    }

}
