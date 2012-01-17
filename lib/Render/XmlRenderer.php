<?php
/**
 * Original Author: sam
 * Date: 12/30/11
 * Time: 4:36 PM
 * 
 * @package dapper
 * @subpackage Render
 * 
 */
namespace dapper\Render;

/**
 * Utilizes the Twig template engine
 * 
 * @package dapper
 * @subpackage Render 
 */
class XmlRenderer extends  BaseRender
{
    
    function init_response($response_content)
    {
        $this->response_content = $response_content;
    }

    function get_headers()
    {
        return array('Content-type: text/xml');
    }
    
    function send_response()
    {
        echo $this->response_content;
    }
}
