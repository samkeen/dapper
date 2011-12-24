<?php
namespace clear;
require_once __DIR__ . "/../BaseUnitTestCase.php";

class WorkTest extends \BaseUnitTestCase {
	
	function testInstantiateNoErrors()
    {
        new Work(function(){});
    }
    
    function testClosureMethodReturnsClosureFromConstructor()
    {
        $constructor_closure = function(){$x="hello world";};
        $work = new Work($constructor_closure);
        $this->assertSame($constructor_closure, $work->closure(),
            "The closure returned by Work::closure() should be the the 
            very same supplied to Work::__construct()");
    }
    
    function testInvokingWork()
    {
        $constructor_closure = function(){$x="hello world";};
        $work = new Work($constructor_closure);
        $result = $work();
        $this->assertSame(array('x'=>'hello world'), $result,
            "The result of \$work() should be array('x'=>'hello world')\n"
            ."It was instead:".print_r($result, true)
        );
    }
	
	
	
	
}
 
