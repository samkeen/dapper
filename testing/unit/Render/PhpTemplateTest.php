<?php
namespace dapper\TemplateEngine;

require_once __DIR__ . "/../../BaseCase.php";

/**
 * Test class for Route.
 * Generated by PHPUnit on 2011-12-13 at 08:29:38.
 */
class TwigRenderTest extends \BaseCase {
	
    function testInstantiateNoErrors()
    {
        new PhpTemplate(array());
        // just a sanity test
        $this->assertTrue(true);
    }
    
}
