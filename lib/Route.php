<?php
namespace clear;
/**
 * Abstract Of a Route (that can optionally have Work to do)
 */
class Route {

	private $path;
	private $http_method;
	private $controller;
	private $uri_path_segments;
	private $targeted_view;
	private $exposed_work_var_names = array();
	
	/**
	 * A route can optionally have work to perform
	 * 
	 * @var \Closure
	 */
	private $work;
	
	private $known_http_methods = array('GET', 'PUT', 'POST', 'DELETE', 'OPTIONS');
	
	function __construct($http_method, $path)
	{
		$this->set_http_method($http_method);
		$disected_path = $this->disect_path($path);
		$this->path = $disected_path['path'];
		$this->controller = $disected_path['controller'];
		$this->uri_path_segments = $disected_path['uri_path_segments'];
	}

	function path($route=null)
	{
		return func_num_args() ? $this->path = $route : $this->path;
	}
	function http_method($http_method=null)
	{
		return func_num_args() ? $this->http_method = $http_method : $this->http_method;
	}
	function controller($controller=null)
	{
		return func_num_args() ? $this->controller = $controller : $this->controller;
	}
	/**
	 * @param array|null $uri_path_segments
	 * @return array|null
	 */
	function uri_path_segments(array $uri_path_segments=null)
	{
		return func_num_args() ? $this->uri_path_segments = $uri_path_segments : $this->uri_path_segments;
	}
	
	function targeted_view($targeted_view=null)
	{
		return func_num_args() ? $this->targeted_view = $targeted_view : $this->targeted_view;
	}

	/**
	 * @param \Closure|null $work
	 * @return \Closure
	 */
	function work(\Closure $work=null)
	{
		if(func_num_args())
		{
			$this->work = new Work($work);
		}
		else
		{
			return $this->work;
		}
	}
	
	function exposed_work_var_names($exposed_work_variable_names=null)
	{
		return func_num_args() 
			? $this->exposed_work_var_names = $exposed_work_variable_names 
			: $this->exposed_work_var_names;
	}
	
	private function set_http_method($http_method)
	{
		$http_method = strtoupper(trim($http_method));
		if( ! in_array($http_method, $this->known_http_methods))
		{
			throw new \InvalidArgumentException("Unknown HTTP Method [{$http_method}].\n"
				."Known methods [".implode(', ', $this->known_http_methods)."]");
		}
		$this->http_method = $http_method;
	}
	
	/**
	 * @param string $raw_route ex: "/hello/:name"
	 * @return array ex: array(
	 * 		'controller' => "hello",
	 * 		'uri_path_sements' => array(
	 * 			0 => ':name'
	 * 		)
	 * )
	 */
	private function disect_path($raw_route)
	{
		$uri_path_segments = array_filter(explode('/', trim($raw_route, '/')));
		$controller = array_shift($uri_path_segments);
		/*
		 * only allow pattern /:\w+/ for uri path segments
		 */
		$uri_path_segments = array_values(array_filter(
			$uri_path_segments,
			function($element){
				return $element[0]==':'
					&& ! preg_match('/:.*\W+/', $element);
			}
		));
		$post_controller_path = $uri_path_segments 
			? "/".implode('/',array_map('trim', $uri_path_segments)) 
			: "";
		return array(
			'path' => "/{$controller}{$post_controller_path}",
			'controller' => $controller==="" ? "/" : strtolower($controller),
			'uri_path_segments' => $uri_path_segments
		);
	}
	
}
