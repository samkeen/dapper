<?php
/**
 * @package dapper
 */
namespace dapper;
/**
 * Route plays 2 similar roles.
 * - The Router is taught a list of Routes.
 * - The Router recives a request Route that it then attempts to match one
 * of its learned Routes.
 * 
 * 
 * @package dapper
 */
class Route {

	private $path;
	private $http_method;
	private $controller_name;
	private $uri_path_segments;
	private $targeted_view_name;
	private $exposed_work_var_names = array();
    /**
     * ex: array(':name' => 'bob')
     * @var array
     */
    private $mapped_path_param_values;
	
	/**
	 * A route can optionally have work to perform
	 * 
	 * @var \Closure
	 */
	private $work;
	
	/**
	 * @param $http_method
	 * @param $path
	 * @param bool $is_requested_route If true, this is the route that the client
	 * requested (as opposed to route patterns in index.php)
	 */
	function __construct($http_method, $path, $is_requested_route=false)
	{
        $this->http_method = strtoupper(trim($http_method));
		$disected_path = $this->disect_path($path, $is_requested_route);
		$this->path = $disected_path['path'];
		$this->controller_name = $disected_path['controller_name'];
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
    /**
     * The controller signified in the path (ex: for path '/user/bob' the 
     * controller name is 'user')
     * 
     * @param null|string $controller
     * @return null|string
     */
	function controller_name($controller=null)
	{
		return func_num_args() ? $this->controller_name = $controller : $this->controller_name;
	}
	/**
     * This is just the path (minus the controller) exploded into an array.
     * ex: for path '/user/bob', 
     * $uri_path_segments would be array('bob') (user is the controller name so it
     * is not included in $uri_path_segments)
     * 
	 * @param array|null $uri_path_segments
	 * @return array|null
	 */
	function uri_path_segments(array $uri_path_segments=null)
	{
		return func_num_args() ? $this->uri_path_segments = $uri_path_segments : $this->uri_path_segments;
	}
	/**
	 * [s|g]etter for the view name 
     * It is set via the Router teaching method ::render()
	 * 
	 * @param string|null $targeted_view_name
	 * @return string|null
	 */
	function view_name($targeted_view_name=null)
	{
		return func_num_args() 
			? $this->targeted_view_name = $targeted_view_name 
			: $this->targeted_view_name;
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
	/**
     * @param array|null $exposed_work_variable_names
     * @return array
     */
	function exposed_work_var_names($exposed_work_variable_names=null)
	{
		return func_num_args() 
			? $this->exposed_work_var_names = (array)$exposed_work_variable_names 
			: $this->exposed_work_var_names;
	}
    /**
     * These are the path param placeholders (i.e. /user/:name) mapped
     * to the matched request route values
     * i.e.  
     * <pre>array (
     *      ':name' => 'bob'
     * )</pre>
     * 
     * @param array|null $mapped_path_param_values
     * @return array|null
     * 
     * 
     * @TODO move method to Router
     * 
     */
    function mapped_path_param_values(array $mapped_path_param_values=null)
    {
        return func_num_args() 
            ? $this->mapped_path_param_values = (array)$mapped_path_param_values 
            : $this->mapped_path_param_values;
    }
    
    /**
     * If this Route has Work, this extracts a 'payload' from the Route
     * The 'payload' is a set scope of variables retrieved when invoking
     * the Route's ExtractingClosure.
     * This variable scope is intersected with the whitelist defined by ->exposse() 
     * for the the given $route.
     * 
     * @return array
     */
    function response_payload()
    {
        $template_payload = array();
        if($route_work = $this->work())
        {
            $template_payload = array_intersect_key(
                /*
                 * the param used when executing the Extracting closure signifies
                 * the variable scope that will be used (use()) for the ultimate
                 * execution of the closure.
                 */
                $route_work(array(
                    Router::URI_PATH_KEY_NAME => $this->mapped_path_param_values())
                ),
                /*
                 * an ExtractionClosre retuns all of its internal var scope as a key/val
                 * array. Of that array, $route->exposed_work_var_names() is a white list of keys 
                 * that determines what will be exposed to the view tier ($template_payload)
                 */
                array_fill_keys($this->exposed_work_var_names(), null)
            );
        }
        return $template_payload;
    }
	
	/**
	 * @param string $raw_route ex: "/hello/:name"
	 * @param boolean $is_requested_route If true, this is the route that the client
	 * requested (as opposed to route patterns in index.php).
	 * @return array ex: array(
	 * 		'controller_name' => "hello",
	 * 		'uri_path_sements' => array(
	 * 			0 => ':name'
	 * 		)
	 * )
	 */
	private function disect_path($raw_route, $is_requested_route=false)
	{
		$uri_path_segments = array_filter(explode('/', trim($raw_route, '/')));
		$controller = array_shift($uri_path_segments);
		if( ! $is_requested_route)
		{
			/*
			 * enforce placeholder pattern
			 * ex: /users/:name
			 */
			$uri_path_segments = array_values(array_filter(
				$uri_path_segments,
				function($element){
					return $element[0]==':'
						&& ! preg_match('/:.*\W+/', $element);
				}
			));
		}
		$post_controller_path = $uri_path_segments 
			? "/".implode('/',array_map('trim', $uri_path_segments)) 
			: "";
		return array(
			'path' => "/{$controller}{$post_controller_path}",
			'controller_name' => $controller==="" ? "/" : strtolower($controller),
			'uri_path_segments' => $uri_path_segments
		);
	}
    
    
	
}
