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
use dapper\Env;

/**
 * @package dapper
 * @subpackage Render 
 */
abstract class BaseRender
{
    /**
     * Some Renders display 'sent' headers in some way.  As the Responders 'sends'
     * headers it records them on the Render via record_response_header()
     * @var array
     */
    protected $response_headers;
    /**
     * @var \dapper\Env
     */
    protected $env;
    /**
     * @var mixed
     */
    protected $response_content;

    function __construct(Env $env)
    {
        $this->env = $env;
    }
    /**
     * @abstract
     * @return array
     */
    abstract function get_headers();
    /**
     * @abstract
     * @param string $response_content
     */
    abstract function init_response($response_content);
    /**
     * This is where we typically echo output
     * @abstract
     * 
     */
    abstract function send_response();
}
