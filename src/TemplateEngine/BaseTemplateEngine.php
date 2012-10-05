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
 * @package dapper
 * @subpackage TemplateEngine 
 */
abstract class BaseTemplateEngine
{
    /**
     * @var array
     */
    protected $config;
    
    function __construct(array $config=array())
    {
        $this->config = $config;
    }
    /**
     * @abstract
     * @param string $view_name
     * @param string $format
     * @param array $payload
     * 
     * @return string
     */
    abstract function templatize($view_name, $format, array $payload=array());
    /**
     * @abstract
     * @param int $error_code
     * @param string $error_message
     * 
     * @return string
     */
    abstract function templatize_error($error_code, $error_message);
}
