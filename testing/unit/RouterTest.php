<?php
namespace clear;
require_once __DIR__ . "/../BaseCase.php";

class RouterTest extends \BaseCase {
	
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
                '/user',
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
    
    function testRequestedRouteReturnsRouteFromCintructor()
    {
        $requested_route = $this->router->requested_route();
        $this->assertEquals('GET',$requested_route->http_method());
        $this->assertEquals('user',$requested_route->controller_name());
    }
    
    function testLearnedRoutesReturnsAllLearnedRoutes()
    {
        $this->router
            ->append_route('get /user')
            ->append_route('post /messages');
        $learned_routes = $this->router->learned_routes();
        $this->assertEquals(2, count($learned_routes));
        
        $first_learned_route = $learned_routes[0];
        $second_learned_route = $learned_routes[1];
        
        $this->assertEquals('GET',$first_learned_route->http_method());
        $this->assertEquals('user',$first_learned_route->controller_name());
        $this->assertEquals('POST',$second_learned_route->http_method());
        $this->assertEquals('messages',$second_learned_route->controller_name());
        
    }
    
    function testMatchRouteReturnsNullForNoMatch()
    {
        $this->router
            ->append_route('get /posts')
            ->append_route('post /messages');
        $this->assertNull($this->router->match_route());
    }
    
    function testMatchRouteReturnsTheCorrectRoute()
    {
        $this->router
            ->append_route('get /user')
            ->append_route('post /messages');
        $matched_route = $this->router->match_route();
        $this->assertEquals('GET',$matched_route->http_method());
        $this->assertEquals('user',$matched_route->controller_name());
    }
    
    function testMatchRouteExtraPathStillMatches()
    {
        $this->router
            ->append_route('get /user/bob')//<-- /bob "extra path"
            ->append_route('post /messages');
        $matched_route = $this->router->match_route();
        $this->assertEquals('GET',$matched_route->http_method());
        $this->assertEquals('user',$matched_route->controller_name());
    }
	
    function testRequestPathParamsReturnsEmptyArrayIfNoParamsSignified()
    {
        $router = new Router(
            new Route(
                "get",
                '/user/42',
                $is_request_route=true
            )
        );
        $router->append_route('get /user');//<-- no path params (i.e. /:id)
        $matched_route = $router->match_route();
        $this->assertEquals(array(), $matched_route->mapped_path_param_values());
    }
    function testRequestSinglePathParamReturnsProperParams()
    {
        $router = new Router(
            new Route(
                "get",
                '/user/42',
                $is_request_route=true
            )
        );
        $router->append_route('get /user/:id');
        $matched_route = $router->match_route();
        $this->assertEquals(array(':id' => 42), $matched_route->mapped_path_param_values());
    }
    function testRequestMultiplePathParamsReturnsProperParams()
    {
        $router = new Router(
            new Route(
                "get",
                '/user/42/simple',
                $is_request_route=true
            )
        );
        $router->append_route('get /user/:id/:style');
        $matched_route = $router->match_route();
        $this->assertEquals(array(':id' => 42, ':style' => 'simple'), $matched_route->mapped_path_param_values());
    }
    
    function testRequestMultiplePathParamsReturnsNullValueForParamsNotSuppliedInRequest()
    {
        $router = new Router(
            new Route(
                "get",
                '/user/42',
                $is_request_route=true
            )
        );
        $router->append_route('get /user/:id/:style');
        $matched_route = $router->match_route();
        $this->assertEquals(array(':id' => 42, ':style' => null), $matched_route->mapped_path_param_values());
    }
	
	
}
 
