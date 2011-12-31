<?php
/**
 * Original Author: sam
 * Date: 12/24/11
 * Time: 11:23 PM
 * 
 * @package clear
 * @subpackage Util
 */
namespace clear;
/**
 * 
 * @package clear
 * @subpackage Util
 */
class Env
{
    
    private $request_method;
    private $request_path;
    /**
     * @var bool
     */
    private static $is_dev;
    
    const TOP_DIR = __DIR__;
    const REQUEST_PATH_KEY = '_c';
    /**
     * auto loader for the entire app
     * 
     * @param string $class
     * @return bool
     */
    static function autoload($class)
    {
        $class = str_replace('\\', '/', $class) . '.php';
        if( ! strstr($class, __NAMESPACE__)) {return false;}
        require(str_replace(__NAMESPACE__.'/','',$class));
    }
    /**
     * @param bool $is_dev
     */
    function __construct($is_dev=false)
    {
        self::$is_dev = $is_dev;
        if(self::is_commandline_request())
        {
            global $argc, $argv;
            if($argc!=3)
            {
                echo "WARN: request method (i.e. GET) and or request path (i.e. /user/42) NOT found\n";
                echo "USAGE:php index.php GET /user/42\n\n";
            }
            $this->request_method = isset($argv[1]) ? $argv[1] : null;
            $this->request_path   = isset($argv[2]) ? $argv[2] : null;
        }
        else
        {
            /*
             * note, couldn't use filter_input as that method does not acknowledge manual 
             * changes made to those arrays via the tests. 
             */
            $this->request_method = isset($_SERVER['REQUEST_METHOD'])
                ? strtolower(filter_var($_SERVER['REQUEST_METHOD'], FILTER_SANITIZE_STRING))
                : null;
            $this->request_path = isset($_GET[self::REQUEST_PATH_KEY])
                ? strtolower(filter_var(trim($_GET[self::REQUEST_PATH_KEY], FILTER_SANITIZE_STRING)))
                : null;
        }
    }
    
    function request_method()
    {
        return $this->request_method;
    }
    function request_path()
    {
        return $this->request_path;
    }
    
    static function log($message)
    {
        error_log($message);
    }
    
    static function is_dev()
    {
        return self::$is_dev;
    }

    static function is_cli_request()
    {
        return php_sapi_name()=="cli";
    }
    
    static function is_cgi_request()
    {
        return substr(php_sapi_name(), 0, 3) == 'cgi';
    }
    
    static function is_commandline_request()
    {
        return self::is_cli_request()
            && isset($_SERVER['SCRIPT_NAME'])
            && substr(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_BASENAME),0,5) == 'index';
    }
    
    
}
