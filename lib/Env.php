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

}
