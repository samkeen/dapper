<?php
/**
 * Original Author: sam
 * Date: 12/30/11
 * Time: 11:28 AM
 * 
 * @package clear
 * @subpackage Responder
 */
namespace clear\Responder;

/**
 * @package clear
 * @subpackage Responder
 */
class PhpTemplateResponse extends BaseResponder
{
    function __construct($config, $matched_route)
    {
        $this->view_file_suffix = '.htm.php';
        parent::__construct($config, $matched_route);
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
        include __DIR__ . $this->config['template_dir']."/{$view_name}{$this->view_file_suffix}";
    }
}
