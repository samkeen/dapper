<?php
/**
 * 
 */
require __DIR__ . "/../lib/Env.php";
spl_autoload_register('dapper\Env::autoload');
 ! defined('TOP_DIR')? define('TOP_DIR', realpath(__DIR__."/..")):null;

class BaseCase extends \PHPUnit_Framework_TestCase {
    

}