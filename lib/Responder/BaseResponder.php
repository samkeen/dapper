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
use clear\Env;
use clear\Router;
use clear\Render\BaseRender;

/**
 * @package clear
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
     * @var \clear\Router
     */
    protected $router;
    /**
     * @var \clear\Render\BaseRender
     */
    protected $render;
    /**
     * @var \clear\Env
     */
    protected $env;
    
    /**
     * @param \clear\Router $router
     * @param \clear\Render\BaseRender $render
     * @param \clear\Env $env
     */
    function __construct(Router $router, BaseRender $render, Env $env)
    {
        $this->router = $router;
        $this->render = $render;
        $this->env = $env;
    }
    
    function complete()
    {
        if($route = $this->router->match_route())
        {
            // @TODO convert errors to exceptions
            try
            {
                $view_name        = $route->view_name();
                $template_payload = $route->response_payload();
                $this->render->render_view($view_name, $template_payload);
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
                $message = "Requested Route [{$requested_route->http_method()} {$requested_route->path()}] not found\n";
                $message .= "Known Routes:\n";
                $message .= print_r($this->router->learned_routes(), true);
                $message .= "\n";
                $this->render->render_error(404, $message);
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
        $this->render->render_error($response_code, $response_message);
    }
    
    protected function invoke_header($response_code, $response_message=null)
    {
        $header_text = $this->construct_response_header_text($response_code);
        /*
         * some but not all render engines will render the sent headers in some way
         */
        $this->render->record_response_header($response_code, $header_text, $response_message);
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
