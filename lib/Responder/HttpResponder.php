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
 * Responders use:
 * 
 * Templatizers : TemplateEngine injected in constructor
 *   and 
 * Formatters : formatter determined by optional URI extension (i.e. /users/1.say)
 *              Responders have a default format (htm in the case of the HttpResponder)
 * 
 * @package dapper
 * @subpackage Responder
 */
class HttpResponder extends BaseResponder
{

    protected function supported_formats()
    {
        return array('htm', 'json', 'xml');
    }
    
    function render_response($response_content)
    {
        function render_response($response_content)
        {
        /*
         * ex $this->renderer would be HtmRenderer
         * 
         */ 
        $this->renderer->init_response($response_content);
        /*
         * this allows a renderer to send things such as Content-type headers
         */
        $this->send_header(200, $this->renderer->get_headers());
        /*
         * this is typically where the renderer would echo the content
         */
        return $this->renderer->send_response();
        }
    }

    protected function send_header($response_code, $headers_text)
    {
        if(headers_sent($file, $line))
        {
            // @TODO build a logger : https://github.com/samkeen/dapper/issues/9
//            Env::log()->error(__METHOD__."  Headers already sent from {$file}::{$line}");
        }
        else
        {
            $headers_text = (array)$headers_text;
            foreach($headers_text as $header_text)
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

}
