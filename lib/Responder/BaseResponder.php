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
     * @var array
     */
    protected $config;
    /**
     * @var string
     */
    protected $view_file_suffix;
    /**
     * @var \clear\Router
     */
    protected $router;
    /**
     * @param array $config
     * @param \clear\Router $router
     */
    function __construct(array $config, \clear\Router $router)
    {
        $this->config = $config;
        $this->router = $router;
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
                $this->render_view($view_name, $template_payload);
            }
            catch(\Exception $e) 
            {
                if(Env::is_dev())
                {
                    $message = $e->getMessage()
                        . "<pre>\n{$e->getTraceAsString()}\n</pre>";
                }
                else
                {
                    $message = "";
                }
                $this->send_header(500);
                $this->render_error(500, $message);
            }
        }
        else // send 404
        {
            $this->send_header(404);
            if(Env::is_dev())
            {
                $requested_route = $this->router->requested_route();
                $message  = "<pre>\n";
                $message .= "Requested Route [{$requested_route->http_method()} {$requested_route->path()}] not found\n";
                $message .= "Known Routes:\n";
                $message .= print_r($this->router->learned_routes(), true);
                $message .= "\n</pre>";
                $this->render_error(404, $message);
            }
        }
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
    /**
     * The default behavior of this method is to send HTTP headers.
     * Concrete classes are free to override that behavior.
     * 
     * @param $response_code
     */
    protected function send_header($response_code)
    {
        if(headers_sent($file, $line))
        {
            // @TODO build a logger 
            // @see https://github.com/samkeen/clear/issues/9
//            Env::log()->error(__METHOD__."  Headers already sent from {$file}::{$line}");
        }
        else
        {
            $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) 
                ? $_SERVER['SERVER_PROTOCOL'] 
                : 'HTTP/1.1';
            if (Env::is_cgi_request())
            {
                header("Status: {$response_code} "
                    .self::response_code_text($response_code),
                    true
                );
            }
            else
            {
                header($server_protocol." {$response_code} "
                    .self::response_code_text($response_code),
                    true,
                    $response_code
                );
            }
        }
    }
    private static function response_code_text($response_code)
    {
        return isset(self::$response_codes[$response_code]) 
            ? self::$response_codes[$response_code]
            : null;
    }
}
