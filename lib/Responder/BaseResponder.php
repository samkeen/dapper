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
use dapper\Env;
use dapper\Router;
use dapper\TemplateEngine\BaseTemplateEngine;

/**
 * @package dapper
 * @subpackage Responder
 */
abstract class BaseResponder
{
    protected static $response_codes = array(
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );
    /**
     * @var \dapper\Router
     */
    protected $router;
    /**
     * @var \dapper\TemplateEngine\BaseTemplateEngine
     */
    protected $template;
    /**
     * @var \dapper\Render\BaseRender
     */
    protected $renderer;
    /**
     * @var string (i.e. 'htm', 'json')
     */
    protected $requested_format;
    /**
     * @var \dapper\Env
     */
    protected $env;
    
    /**
     * @param \dapper\Router $router
     * @param \dapper\TemplateEngine\BaseTemplateEngine $template
     * @param \dapper\Env $env
     */
    function __construct(Router $router, BaseTemplateEngine $template, Env $env)
    {
        $this->router   = $router;
        $this->template = $template;
        $this->env      = $env;
        $this->requested_format = $env->requested_format();
        $this->construct_renderer();        
    }
    /**
     * @abstract
     * @return array An array of supported formats.
     * i.e. array('htm', 'json')
     */
    protected abstract function supported_formats(); 
    
    protected function responds_to_format($format)
    {
        return in_array($format, $this->supported_formats());
    }
    
    protected function construct_renderer()
    {
        if($this->responds_to_format($this->requested_format))
        {
            $renderer_for_format = 
                '\dapper\Render\\'.ucfirst(strtolower($this->requested_format))."Renderer";
            $this->renderer = new $renderer_for_format($this->env);
        }
        else
        {
            throw new \Exception("Format: [{$this->requested_format}] not supported");
        }
    }
    
    protected abstract function render_response($response_content);
    
    function complete()
    {
        if($route = $this->router->match_route())
        {
            $view_name        = $route->view_name();
            $template_payload = $route->response_payload();
            // @TODO convert errors to exceptions
            try
            {
                /*
                 * Templates are only applied if there is one present for the
                 * requested format (i.e. hello.htm.twig).
                 * Else, the pure data is passed to the Formatter.
                 * 
                 */
                $templated_content = $this->template->templatize(
                    $view_name, $this->requested_format, $template_payload
                );
                return $this->render_response($templated_content);
            }
            catch(\Exception $e) 
            {
                if($this->env->is_dev())
                {
                    $message = $e->getMessage()
                        . "\n{$e->getTraceAsString()}\n";
                }
                else
                {
                    $message = "";
                }
                $this->error_response(500, $message);
            }
        }
        else // send 404
        {
            $this->invoke_header(404);
            if($this->env->is_dev())
            {
                $requested_route = $this->router->requested_route();
                $message  = "Requested Route [{$requested_route->http_method()} {$requested_route->path()}] not found\n";
                $message .= "Known Routes:\n";
                $message .= print_r($this->router->learned_routes(), true);
                $message .= "\n";
                $this->template->render_error(404, $message);
            }
        }
    }
    /**
     * @param $response_code
     * @param null $response_message
     */
    function error_response($response_code, $response_message=null)
    {
        $this->invoke_header($response_code, $response_message);
        $this->template->render_error($response_code, $response_message);
    }

    protected function invoke_header($response_code, $response_message=null)
    {
        $header_text = $this->construct_response_header_text($response_code);
        $this->send_header($response_code, $header_text);
    }
    
    /**
     * The default behavior of this method is to do nothing.
     * Responders such as HttpResponder override this method in
     * order to make the actual calls to header().
     * 
     * @param $response_code
     * @param $header_text
     */
    protected function send_header($response_code, $header_text){}
    
    private static function response_code_text($response_code)
    {
        return isset(self::$response_codes[$response_code]) 
            ? self::$response_codes[$response_code]
            : null;
    }
    
    private function construct_response_header_text($response_code)
    {
        $header_text = "";
        $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) 
            ? $_SERVER['SERVER_PROTOCOL'] 
            : 'HTTP/1.1';
        if ($this->env->is_cgi_request())
        {
            $header_text = "Status: {$response_code} "
                .self::response_code_text($response_code);
        }
        else
        {
            $header_text = $server_protocol." {$response_code} "
                .self::response_code_text($response_code);
        }
        return $header_text;
    }
}
