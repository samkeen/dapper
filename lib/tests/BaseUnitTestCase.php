<?php
/**
 * 
 */
class BaseUnitTestCase extends PHPUnit_Framework_TestCase {
		
	static function autoload($class)
	{
		$lib_dir = realpath(__DIR__.'/../');
		$class = str_replace('\\', '/', $class) . '.php';
		if( ! strstr($class, 'clear/')) {return false;}
		$class = str_replace('clear/','',$class);
    	require("$lib_dir/$class");
	}
}

spl_autoload_register('BaseUnitTestCase::autoload');