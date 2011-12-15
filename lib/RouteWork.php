<?php
namespace clear;
/**
 * Abstract the work to be done for the matched request Route
 */
class RouteWork {

	private $route;
	private $http_method;
	private $controller;
	private $uri_path_segments;
	private $executable_workload;
	private $targeted_view;
	private $exposed_work = array();
	
	function __construct($route, $http_method, $controller, $uri_path_segments)
	{
		$this->route = $route;
		$this->http_method = $http_method;
		$this->controller = $controller;
		$this->uri_path_segments = $uri_path_segments;
	}
	
	function route($route=null)
	{
		return $route===null ? $this->route : $this->route = $route;
	}
	function http_method($http_method=null)
	{
		return $http_method===null ? $this->http_method : $this->http_method = $http_method;
	}
	function controller($controller=null)
	{
		return $controller===null ? $this->controller : $this->controller = $controller;
	}
	function uri_path_segments($uri_path_segments=null)
	{
		return $uri_path_segments===null ? $this->uri_path_segments : $this->uri_path_segments = $uri_path_segments;
	}
	/**
	 * @param \Closure $workload
	 * Actually returns ExtractingClosure but reciever will not know the difference.
	 * @return \Closure
	 */
	function executable_workload(\Closure $workload=null)
	{
		if($workload)
		{
			$this->executable_workload = new ExtractingClosure($workload);
		}
		else
		{
			return $this->executable_workload;
		}
	}

	function targeted_view($targeted_view=null)
	{
		return $targeted_view===null ? $this->targeted_view : $this->targeted_view = $targeted_view;
	}
	function exposed_work($exposed_work=null)
	{
		return $exposed_work===null ? $this->exposed_work : $this->exposed_work = $exposed_work;
	}
	
}
