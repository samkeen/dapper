<?php
namespace clear;
require_once __DIR__ . "/../BaseCase.php";

class RouterBasicsTest extends \BaseCase {
	
	/**
	 * @var Router
	 */
	private $router;
	
	protected function setUp()
	{
		parent::setUp();
		$this->router = new Router(
            new Route(
                "get",
                '/',
                $is_request_route=true
            )
        );
	}
    
    /**
     * @expectedException \InvalidArgumentException
     */
    function testAppendRouteThrowsProperExceptionForInvalidHttpMethod()
    {
        $this->router->append_route('FU /');		
    }
		
	/**
     * @expectedException clear\Exception\InvalidStateException
     */
    function testDo_workInvalidStateExceptionThrown()
    {
		$this->router
			->do_work(function(){});
    }
	
	function testDo_workArrivesOnLastRoute()
	{
		$other_work = function(){$x=1;};
		$work_for_last_route = function(){};
		$this->router
			->append_route('GET /123')
			->do_work($other_work)
			->append_route('GET /abc')
			->do_work($work_for_last_route);
		$initial_workload = $this->router->last_learned_route()->work()->closure();
		$this->assertSame($work_for_last_route, $initial_workload, 'The provided work to $this->core->do_work($work)'
			.' should be the same work returned by $this->core->last_learned_route()->work()->closure()');
	}

    /**
     * @expectedException clear\Exception\InvalidStateException
     */
    function testBlindCallToExpose()
    {
		$this->router
			->expose('bob');
    }
	
	function testExposeArrivesOnLastRoute()
	{
		$other_expose_params = "message";
		$expose_params_for_last_route = "user";
		$this->router
			->append_route('GET /123')
			->expose($other_expose_params)
			->append_route('GET /abc')
			->expose($expose_params_for_last_route);
		$exposed_work = $this->router->last_learned_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array('user'), 'The returned work to expose'
		 .' should have been an array with one element: array("user")');
	}
	
	function testExposeEmptyStringReturnsEmptyArray()
	{
		$work_to_expose = "";
		$this->router
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->router->last_learned_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array(), 'The returned work to expose'
		 .' should have been an empty array:  array()');
	}
	function testExposeNullReturnsEmptyArray()
	{
		$work_to_expose = null;
		$this->router
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->router->last_learned_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array(), 'The returned work to expose'
		 .' should have been an empty array:  array()');
	}
	
	function testExposeCommaDelimListReturnsPoperArray()
	{
		$work_to_expose = "user, message";
		$this->router
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->router->last_learned_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array("user", "message"),
			'The returned work to expose'
			.' should have been an empty array:  array("user", "message")');
	}
	
	function testExposeSpaceDelimListReturnsPoperArray()
	{
		$work_to_expose = "user   message";
		$this->router
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->router->last_learned_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array("user", "message"),
			'The returned work to expose'
			.' should have been an empty array:  array("user", "message")');
	}
	
	function testExposeTooManyCommasReturnsPoperArray()
	{
		$work_to_expose = "user, ,message,";
		$this->router
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $this->router->last_learned_route()->exposed_work_var_names();
		$this->assertEquals($exposed_work, array("user", "message"),
			'The returned work to expose'
			.' should have been an empty array:  array("user", "message")');
	}

    /**
     * @expectedException clear\Exception\InvalidStateException
     */
    function testBlindCallToRenderThrowsException()
    {
		$this->router
			->render('bob');
    }
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	function testEmptyCallToRenderThrowsException()
	{
		$this->router
			->append_route('GET /abc')
			->render('');
	}
	
	function testRenderViewLandsOnLastRoute()
	{
		$this->router
			->append_route('GET /ted')
			->render('ted')
			->append_route('GET /bob')
			->render('bob');
		$this->assertEquals('bob', $this->router->last_learned_route()->view_name());
	}


    /**
     * @expectedException \InvalidArgumentException
     */
    function testAppendRouteThrowExceptionForUnknowHttpMethod()
    {
        $this->router
			->append_route('FU /');
    }
	/**
     * @expectedException \InvalidArgumentException
     */
    function testAppendRouteThrowExceptionForMissingPath()
    {
        $this->router
			->append_route('GET');
    }
	/**
     * @expectedException \InvalidArgumentException
     */
    function testAppendRouteThrowExceptionForMissingHttpMethod()
    {
        $this->router
			->append_route('/user');
    }

    function testAppendRouteHttpMethodAndPathProperlyParsed()
    {
        $last_route = $this->router
			->append_route('GET /user')
			->last_learned_route();
		$this->assertEquals('GET', $last_route->http_method(),"The last route should be"
			. " the one defined by ->append_route() so it http_method should be GET and"
			. " controller_name should be user");
		$this->assertEquals('user', $last_route->controller_name(),"The last route should be"
			. " the one defined by ->append_route() so it http_method should be GET and"
			. " controller_name should be user");
    }
	
	function testAppendRoutePathSegemntsProperlyParsed()
	{
		$last_route = $this->router
			->append_route('GET /user/:id/:location')
			->last_learned_route();
		$this->assertEquals(array(':id', ':location'), $last_route->uri_path_segments(),
			"The last route should be"
			. " the one defined by ->append_route() so its uri_path_segments should be "
			. " array(':id', ':location')");
	}
	
	
	
	
}
 
