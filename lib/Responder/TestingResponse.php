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
class TestingResponse extends BaseResponder
{
    public $payload;
    public $view_name;
    public $error_code;
    public $error_message;

    /**
     * @abstract
     * @param string $view_name
     * @param array $payload
     */
    function render_view($view_name, array $payload=array())
    {
        $this->view_name = $view_name;
        $this->payload   = $payload;
    }

    /**
     * @param int $error_code
     * @param string $error_message
     */
    function render_error($error_code, $error_message)
    {
        $this->error_code    = $error_code;
        $this->error_message = $error_message;
    }
}
