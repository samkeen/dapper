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
 * @package clear
 * @subpackage Render 
 */
abstract class BaseRender
{
    /**
     * @var array
     */
    protected $config;
    /**
     * Some Renders display 'sent' headers in some way.  As the Responders 'sends'
     * headers it records them on the Render via record_response_header()
     * @var array
     */
    protected $response_headers;
    
    function __construct(array $config=array())
    {
        $this->config = $config;
    }

    /**
     * @abstract
     * @param string $view_name
     * @param array $payload
     */
    abstract function render_view($view_name, array $payload=array());
    /**
     * @abstract
     * @param int $error_code
     * @param string $error_message
     */
    abstract function render_error($error_code, $error_message);
    function record_response_header($response_code, $header_text, $response_message)
    {
        $this->response_headers[$response_code][] = array(
            'response_code'    => $response_code,
            'header_text'      => $header_text,
            'response_message' => $response_message,
        );
    }
}
