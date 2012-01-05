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
 * @package dapper
 * @subpackage Responder
 */
class HttpResponder extends BaseResponder
{
    


    protected function send_header($response_code, $header_text)
    {
        if(headers_sent($file, $line))
        {
            // @TODO build a logger 
            // @see https://github.com/samkeen/dapper/issues/9
//            Env::log()->error(__METHOD__."  Headers already sent from {$file}::{$line}");
        }
        else
        {
            if ($this->env->is_cgi_request())
            {
                header($header_text, true);
            }
            else
            {
                header($header_text, true, $response_code);
            }
        }
    }

}
