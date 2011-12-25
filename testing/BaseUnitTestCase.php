<?php
/**
 * 
 */
require __DIR__ . "/../lib/Env.php";
spl_autoload_register('clear\Env::autoload');

class BaseUnitTestCase extends \PHPUnit_Framework_TestCase {
		
	
}