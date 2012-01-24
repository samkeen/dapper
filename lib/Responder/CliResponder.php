<?php
/**
 * Original Author: sam
 * Date: 12/30/11
 * Time: 11:28 AM
 * 
 * @package dapper
 * @subpackage Responder
 */
namespace dapper\Responder;

/**
 * Responds to a console interface 
 * - No calls to header()
 * 
 * @package dapper
 * @subpackage Responder
 */
class CliResponder extends BaseResponder
{
    /**
     * @return array An array of supported formats.
     * i.e. array('htm', 'json')
     */
    protected function supported_formats()
    {
        return array('htm', 'json', 'xml');
    }

    protected function render_response($response_content)
    {
        // TODO: Implement render_response() method.
    }

}
