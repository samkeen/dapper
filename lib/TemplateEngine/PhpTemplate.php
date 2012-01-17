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
class PhpTemplate extends  BaseTemplateEngine
{    
    function __construct($config)
    {
        parent::__construct($config);
        $this->view_file_extension = 'php';
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
        $view_file_path = "{$this->config['templates_dir']}/{$view_name}.{$format}.{$this->view_file_extension}";
        if( ! file_exists("{$this->templates_dir}/{$view_file_path}"))
        {
            $templated_content = $template_payload;  
        }
        else
        {
            extract($template_payload);
            ob_start();
            include $view_file_path;
            $templated_content = ob_get_clean();
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
        return $error_message;
    }

}
