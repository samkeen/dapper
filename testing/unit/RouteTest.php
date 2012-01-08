<?php
namespace dapper;
require_once __DIR__ . "/../BaseCase.php";

/**
 * Test class for Route.
 * Generated by PHPUnit on 2011-12-13 at 08:29:38.
 */
class RouteTest extends \BaseCase {
	
    function testProperPath()
    {
		$route = new Route('GET', '/user'	);
        $this->assertEquals('/user',$route->path());
    }
	
	function testPathCaseSensitive()
	{
		$route = new Route('GET', '/User'	);
		$this->assertEquals('/User',$route->path());
	}
	
	function testInproperlySlashedPathIsCorrected()
	{
		$route = new Route('GET', '/user/');
		$this->assertEquals('/user',$route->path());
		
		$route = new Route('GET', 'user/');
		$this->assertEquals('/user',$route->path());
		
		$route = new Route('GET', '');
		$this->assertEquals('/',$route->path());
		
		$route = new Route('GET', '/user/:one');
		$this->assertEquals('/user/:one',$route->path());
		
		$route = new Route('GET', '/user//:one');
		$this->assertEquals('/user/:one',$route->path());
	}
	
	function testProperHttpMethod()
	{
		$route = new Route('GET', '/user'	);
		$this->assertEquals('GET', $route->http_method());
	}
	
	function testProperHttpMethodCaseNormalized()
	{
		$route = new Route('gEt', '/user'	);
		$this->assertEquals('GET', $route->http_method());
	}

	function testProperController()
	{
		$route = new Route('GET', '/user'	);
		$this->assertEquals('user', $route->controller_name());
	}
	
	function testProperControllerCaseNormalized()
	{
		$route = new Route('GET', '/usEr'	);
		$this->assertEquals('user', $route->controller_name());
	}

    function testProperUriPathSegments()
    {
		$route = new Route('GET', '/user/:id/:latest');
		$this->assertEquals(array(':id',':latest'), $route->uri_path_segments());
    }
	
	function testProperUriPathSegmentsCaseSensitive()
	{
		$route = new Route('GET', '/user/:iD/:Latest');
		$this->assertEquals(array(':iD',':Latest'), $route->uri_path_segments());
	}
	
	function testSegemenstWithOutColonIgnored()
	{
		$route = new Route('GET', '/user/id');
		$this->assertEquals(array(), $route->uri_path_segments());
	}
	
	function testSegemenstWithIllegalCharsIgnored()
	{
		$route = new Route('GET', '/user/:i-d');
		$this->assertEquals(array(), $route->uri_path_segments());
		
		$route = new Route('GET', '/user/:i-d/:classic');
		$this->assertEquals(array(':classic'), $route->uri_path_segments());
	}

    function testWork()
    {
		$route = new Route('GET', '/user/id');
		$closure = function(){};
		$route->work($closure);
		$this->assertInstanceOf('dapper\Work', $route->work());
		$this->assertSame($closure, $route->work()->closure());
    }

    function testViewNameSetterGetter()
    {
		$route = new Route('GET', '/user/id');
		$route->view_name('speed');
		$this->assertEquals('speed', $route->view_name());
    }
	
	function testViewNameSetterGetterCaseSensitive()
	{
		$route = new Route('GET', '/user/id');
		$route->view_name('spEEd');
		$this->assertEquals('spEEd', $route->view_name());
	}
    
    function testExtractPayloadReturnsEmptyArrayForRouteWithNoWork()
    {
        $route = new Route('get','/');
        $this->assertEquals(array(), $route->response_payload());
    }
    
    function testExtractPayloadReturnsEmptyArrayNoWorkExposedForRoute()
    {
        $route = new Route('get','/');
        $route->work(function(){$x = 'Hello';});
        $this->assertEquals(array(), $route->response_payload());
    }
	
}
