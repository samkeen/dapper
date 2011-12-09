<?php
namespace thundercats;
/**
 * Created by JetBrains PhpStorm.
 * User: sam
 * Date: 12/9/11
 * Time: 7:42 AM
 * To change this template use File | Settings | File Templates.
 */
class RouteWork {

	private $route;
	private $http_method;
	private $controller;
	private $uri_path_segments;
	private $workload;
	private $targeted_view;
	/**
	 * @var public 
	 */
	private $exposed_work;
	
	function __construct($route, $http_method, $controller, $uri_path_segments)
	{
		$this->route = $route;
		$this->http_method = $http_method;
		$this->controller = str_replace(array('.', '/'), '', $controller);
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
	 * @param null $workload
	 * @return \Closure
	 */
	function workload(\Closure $workload=null)
	{
		return $workload===null ? $this->workload : $this->workload = $workload;
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
