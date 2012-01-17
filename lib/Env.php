<?php
/**
 * Original Author: sam
 * Date: 12/24/11
 * Time: 11:23 PM
 * 
 * @package dapper
 * @subpackage Util
 */
namespace dapper;
/**
 * 
 * @package dapper
 * @subpackage Util
 */
class Env
{
    private $writable_dir;
    private $request_method;
    private $request_path;
    private $path_extension;
    private $requested_format;
    /**
     * @var bool
     */
    private $is_dev;
    
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
     * @param string $writable_dir
     * @param bool $is_dev
     */
    function __construct($writable_dir, $is_dev=false)
    {
        $this->writable_dir = $writable_dir;
        $this->is_dev = $is_dev;
        if(   static::is_commandline_request())
        {
            global $argc, $argv;
            if($argc!=3)
            {
                echo "USAGE:php index.php {method} {path}\n\n"; 
                echo "ex: php index.php GET /user/42\n\n";
            }
            $this->request_method = isset($argv[1]) ? $argv[1] : null;
            $this->set_request_path(isset($argv[2]) ? $argv[2] : null);
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
            $raw_request_path = isset($_GET[self::REQUEST_PATH_KEY])
                ? strtolower(filter_var(trim($_GET[self::REQUEST_PATH_KEY], FILTER_SANITIZE_STRING)))
                : null;
            $this->set_request_path($raw_request_path);
        }
        $this->set_requested_format();
    }
    
    function writable_dir()
    {
        return $this->writable_dir;
    }
    function request_method()
    {
        return $this->request_method;
    }
    function request_path()
    {
        return $this->request_path;
    }
    function requested_format()
    {
        return $this->path_extension;
    }
    
    static function log($message)
    {
        error_log($message);
    }
    
    function is_dev()
    {
        return $this->is_dev;
    }

    function is_cli_request()
    {
        return php_sapi_name()=="cli";
    }
    
    function is_cgi_request()
    {
        return substr(php_sapi_name(), 0, 3) == 'cgi';
    }
    
    function is_commandline_request()
    {
        return self::is_cli_request()
            && isset($_SERVER['SCRIPT_NAME'])
            && substr(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_BASENAME),0,5) == 'index';
    }
    /** 
     * This looks for and extracts any format extentions in the path
     * i.e. /users/1.json
     * 
     * @param string $raw_path
     */
    private function set_request_path($raw_path)
    {
        $match = $path = null;
        $disected_path = pathinfo($raw_path);
        if(isset($disected_path['extension']) && $disected_path['extension'])
        {
            $path = "{$disected_path['dirname']}/{$disected_path['filename']}";
            $this->path_extension = $disected_path['extension'];
        }
        else
        {
            $path = $raw_path;
        }
        $this->request_path = $path;
    }
    
    private function set_requested_format()
    {
        $this->requested_format = $this->path_extension ?: $this->sniff_accept_headers();
    }
    /**
     * @todo Implement the actual http_accept sniffer
     * @see https://github.com/samkeen/dapper/issues/14
     * @return string
     */
    private function sniff_accept_headers()
    {
        return 'htm';
    }
    
    
}
