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
 * Utilizes plain old PHP templates
 * 
 * @package clear
 * @subpackage Render 
 */
class PhpTemplate extends BaseRender
{
    function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->view_file_suffix = '.htm.php';
    }

    /**
     * @param int $error_code
     * @param string $error_message
     */
    function render_error($error_code, $error_message)
    {
        echo $error_message;
    }

    /**
     * @abstract
     * @param string $view_name
     * @param array $payload
     */
    function render_view($view_name, array $payload=array())
    {
        extract($payload);
        include $this->config['template_dir']."/{$view_name}{$this->view_file_suffix}";
    }
}
