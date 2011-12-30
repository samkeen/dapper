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
class CliResponse extends BaseResponder
{
    /**
     * @abstract
     * @param string $view_name
     * @param array $payload
     */
    function render_view($view_name, array $payload=array())
    {
        
    }

    /**
     * @param int $error_code
     * @param string $error_message
     */
    function render_error($error_code, $error_message)
    {
        echo "\n[error_code]:{$error_code}\n";
        echo "[error_message]:{$error_message}\n";
    }
}
