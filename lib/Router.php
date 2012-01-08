<?php
/**
 * @package dapper
 */
namespace dapper;

/**
 * 
 * The Router is taught a list of Routes
 * It is responsible for taking a Request Route and matching it to one of it's 
 * learned Routes.
 * 
 * 
 * @package dapper
 */
class Router {
	
	/**
	 * the name used to expose path elements to the do_work
	 * i.e. (path in this case)
	 * ->do_work(function(){
	 *	 $message = "Hello {$path[':name']}";
	 * })
	 */
	const URI_PATH_KEY_NAME = 'path';
	/**
     * @var array
     */		
	private $learned_routes = array();
	/**
	 * @var Route
	 */
	private $requested_route;
    /**
     * @var array
     */
    private $known_http_methods = array('GET', 'PUT', 'POST', 'DELETE', 'OPTIONS');
	
	/**
     * @param Route $request_route
     */
	function __construct(Route $request_route)
	{
		$this->requested_route = $request_route;
	}
	
	/*
	 * doWork, expose, and render are considered the 'public api'
	 * meant for index.php.
	 * All other methods are meant for use from Router
	 */
	
	/**
	 * Router teaching method used in index.php
	 * 
	 * @param \Closure $work
	 * @return Router
	 */
	function do_work(\Closure $work)
	{
		$this->last_learned_route()->work($work);
		return $this;
	}
	
	/**
	 * Router teaching method used in index.php
	 * 
	 * @param $view_name
	 * @return Router
	 */
	function render($view_name)
	{
		$view_name = trim($view_name);
		if( ! $view_name)
		{
			throw new \InvalidArgumentException('$view_name param should not be empty');
		}
		$this->last_learned_route()->view_name($view_name);
		return $this;
	}
	
	
	/*
	 * All the following methods are not meant to be used in index.php.  They are
	 * internals meant for Router and bootup.php
	 */
	
	/**
	 * 
	 * @param string $route 
	 * @return Router
	 */
	function append_route($route)
	{
		$match = null;
		/*
		 * look for pattern "{http method} {http path}"
		 */
		if( ! preg_match('/^(?P<method>\w+) +(?P<path>.*)$/', $route, $match)
		   || (trim($match['path'])==''))
		{
			throw new \InvalidArgumentException("Malformed route [$route]."
			 ."Routes should start w/ {HTTP method} "
			 ."followed by a {URI path segment}\nex: 'GET /user'");
		}
		$http_method = strtoupper($match['method']);
        if( ! in_array($http_method, $this->known_http_methods))
        {
            throw new \InvalidArgumentException("Unknown HTTP Method [{$http_method}].\n"
                ."Known methods [".implode(', ', $this->known_http_methods)."]");
        }
		$this->learned_routes[] = new Route(
			$http_method, $match['path']
		);
		return $this;
	}
    /**
     * @return Route
     */
    function requested_route()
    {
        return $this->requested_route;
    }
    /**
     * @return array
     */
    function learned_routes()
    {
        return $this->learned_routes;
    }
	
	/**
	 * 
	 * @return Route
	 */
	function match_route()
	{
		$matched_route = null;
		$match = null;
		foreach($this->learned_routes as $known_route)
		{
			if(    $this->requested_route->http_method() == $known_route->http_method()
				&& $this->requested_route->controller_name() == $known_route->controller_name())
			{
				$matched_route = $known_route;
                $matched_route->mapped_path_param_values(
                    $this->match_route_keys_to_request_values(
                        $matched_route->uri_path_segments(),
                        $this->requested_route->uri_path_segments()
                    )
                );
				break;
			}
		}
		return $matched_route;
	}
	
	/**
     * note: Left public for testing purposes.
	 * @return Route
	 */
	function last_learned_route()
	{
		if( ! $last_route = end($this->learned_routes))
		{
			throw new Exception\InvalidStateException(__METHOD__."() called with no routes defined");
		}
		return $last_route;
	}
    
    /**
     * Take the route defined for a URI and for each placeholder (/:name) look
     * for a value in the request URI.  The mapping is set to
     * $this->matched_request_route_params
     * 
     * @param array $route_keys
     * @param array $request_values
     * @return array
     * ex:
     * <pre>array(
     *   ':name' => 'bob'
     * )</pre>
     */
    private function match_route_keys_to_request_values(array $route_keys, array $request_values)
    {
        $mapped_path_param_values = array();
        foreach($route_keys as $uri_segment_index => $route_key)
        {
            $mapped_path_param_values[$route_key] = isset($request_values[$uri_segment_index])
                ? $request_values[$uri_segment_index]
                : null;
        }
        return $mapped_path_param_values;
    }
}