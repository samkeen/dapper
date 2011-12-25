<?php
namespace clear;
require_once __DIR__ . "/../BaseUnitTestCase.php";

class CoreBasicsTest extends \BaseUnitTestCase {
	
	/**
	 * @var Router
	 */
	private $core;
	
	protected function setUp()
	{
		parent::setUp();
		$this->core = new Router('GET', array());
	}
    
    function testInstantiateThrowsNoErrors()
    {
        new Router('GET', array());
    }
		
	/**
     * @expectedException clear\Exception\InvalidStateException
     */
    function testDo_workInvalidStateExceptionThrown()
    {
		$this->core
			->do_work(function(){});
    }
	
	function testDo_workArrivesOnLastRoute()
	{
		$other_work = function(){$x=1;};
		$work_for_last_route = function(){};
		$this->core
			->append_route('GET /123')
			->do_work($other_work)
			->append_route('GET /abc')
			->do_work($work_for_last_route);
		$initial_workload = $this->core->last_route()->work()->closure();
		$this->assertSame($work_for_last_route, $initial_workload, 'The provided work to $this->core->do_work($work)'
			.' should be the same work returned by $this->core->last_route()->work()->closure()');
	}

    /**
     * @expectedException clear\Exception\InvalidStateException
     */
    function testBlindCallToExpose()
    {
		$this->core
			->expose('bob');
    }
	
	function testExposeArrivesOnLastRoute()
	{
		$other_expose_params = "message";
		$expose_params_for_last_route = "user";
		$this->core
			->append_route('GET /123')
			->expose($other_expose_params)
			->append_route('GET /abc')
			->expose($expose_params_for_last_route);
		$exposed_work = $this->core->last_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array('user'), 'The returned work to expose'
		 .' should have been an array with one element: array("user")');
	}
	
	function testExposeEmptyStringReturnsEmptyArray()
	{
		$work_to_expose = "";
		$this->core
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->core->last_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array(), 'The returned work to expose'
		 .' should have been an empty array:  array()');
	}
	function testExposeNullReturnsEmptyArray()
	{
		$work_to_expose = null;
		$this->core
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->core->last_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array(), 'The returned work to expose'
		 .' should have been an empty array:  array()');
	}
	
	function testExposeCommaDelimListReturnsPoperArray()
	{
		$work_to_expose = "user, message";
		$this->core
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->core->last_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array("user", "message"),
			'The returned work to expose'
			.' should have been an empty array:  array("user", "message")');
	}
	
	function testExposeSpaceDelimListReturnsPoperArray()
	{
		$work_to_expose = "user   message";
		$this->core
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->core->last_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array("user", "message"),
			'The returned work to expose'
			.' should have been an empty array:  array("user", "message")');
	}
	
	function testExposeTooManyCommasReturnsPoperArray()
	{
		$work_to_expose = "user, ,message,";
		$this->core
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->core->last_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array("user", "message"),
			'The returned work to expose'
			.' should have been an empty array:  array("user", "message")');
	}

    /**
     * @expectedException clear\Exception\InvalidStateException
     */
    function testBlindCallToRenderThrowsException()
    {
		$this->core
			->render('bob');
    }
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	function testEmptyCallToRenderThrowsException()
	{
		$this->core
			->append_route('GET /abc')
			->render('');
	}
	
	function testRenderViewLandsOnLastRoute()
	{
		$this->core
			->append_route('GET /ted')
			->render('ted')
			->append_route('GET /bob')
			->render('bob');
		$this->assertEquals('bob', $this->core->last_route()->view_name());
	}


    /**
     * @expectedException \InvalidArgumentException
     */
    function testAppendRouteThrowExceptionForUnknowHttpMethod()
    {
        $this->core
			->append_route('FU /');
    }
	/**
     * @expectedException \InvalidArgumentException
     */
    function testAppendRouteThrowExceptionForMissingPath()
    {
        $this->core
			->append_route('GET');
    }
	/**
     * @expectedException \InvalidArgumentException
     */
    function testAppendRouteThrowExceptionForMissingHttpMethod()
    {
        $this->core
			->append_route('/user');
    }

    function testAppendRouteHttpMethodAndPathProperlyParsed()
    {
        $last_route = $this->core
			->append_route('GET /user')
			->last_route();
		$this->assertEquals('GET', $last_route->http_method(),"The last route should be"
			. " the one defined by ->append_route() so it http_method should be GET and"
			. " controller should be user");
		$this->assertEquals('user', $last_route->controller(),"The last route should be"
			. " the one defined by ->append_route() so it http_method should be GET and"
			. " controller should be user");
    }
	
	function testAppendRoutePathSegemntsProperlyParsed()
	{
		$last_route = $this->core
			->append_route('GET /user/:id/:location')
			->last_route();
		$this->assertEquals(array(':id', ':location'), $last_route->uri_path_segments(),
			"The last route should be"
			. " the one defined by ->append_route() so its uri_path_segments should be "
			. " array(':id', ':location')");
	}
	
	
	
	
}
 
