<?php
namespace clear;

/**
 * 
 * 
 */
class Core {
	
	/**
	 * the name used to expose path elements to the do_work
	 * i.e. (path in this case)
	 * ->do_work(function(){
	 *	 $message = "Hello {$path[':name']}";
	 * })
	 */
	const URI_PATH_KEY_NAME = 'path';
		
	private static $config = array(
		'template_engine' => 'php',
		'template_env' => array(),
		'template_dir' => null,
		'cache_dir' => null
	);
	
	private $routes = array();
	private $requested_route;
	private $matched_request_route_params;
	private $request_method;
	
	/*
	 * doWork, expose, and render are considered the 'public api'
	 * meant for index.php.
	 * All other methods are meant for use from Core
	 */
	
	/**
	 * index.php method
	 * 
	 * @param \Closure $work
	 * @return Core
	 */
	function do_work(\Closure $work)
	{
		$this->last_route()->executable_workload($work);
		return $this;
	}
	
	/**
	 * index.php method
	 * 
	 * @param $name_to_expose_to_view
	 * @return Core
	 */
	function expose($name_to_expose_to_view)
	{
		$this->last_route()->exposed_work( 
			array_fill_keys(array_map('trim', explode(',', $name_to_expose_to_view)),null)
		);
		return $this;
	}
	
	/**
	 * index.php method
	 * 
	 * @param $view_name
	 * @return Core
	 */
	function render($view_name)
	{
		$this->last_route()->targeted_view($view_name);
		return $this;
	}
	
	
	/*
	 * All the following methods are not meant to be used in index.php.  They are
	 * internals meant for Core and bootup.php
	 */
	
	/**
	 * singleton instance accesor
	 * 
	 * @return Core
	 */
	static function instance()
	{
		static $core;
		if(! $core)
		{
			$core = new self;
		}
		return $core;
	}
	
	/**
	 * privatized constructor.
	 * @see $this->__construct()
	 */
	private function __construct()
	{
		$this->requested_route = strtolower(trim(filter_input(INPUT_GET, '_c')));
		$this->request_method = $_SERVER['REQUEST_METHOD'];
		if(self::$config['template_engine']=="twig")
		{
			require_once TOP_DIR . '/vendors/twig/lib/Twig/Autoloader.php';
			\Twig_Autoloader::register();
		}
	}
	
	/**
	 * autoloader for the entire app
	 * 
	 * @param string $class
	 * @return bool
	 */
	static function autoload($class)
	{
		$class = str_replace('\\', '/', $class) . '.php';
		if( ! strstr($class, __NAMESPACE__)) {return false;}
    	require(str_replace(__NAMESPACE__.'/','',$class));
	}
	
	/**
	 * Setter for static env config values
	 * 
	 * @see self::$config
	 * @param string $config_key
	 * @param mixed $config_value
	 * @return void
	 */
	public static function config($config_key, $config_value)
	{
		if( ! array_key_exists($config_key, self::$config))
		{
			throw new \Exception("Unknown config key [{$config_key}]");
		}
		self::$config[$config_key] = $config_value;
	}
	
	/**
	 * 
	 * @param string $route 
	 * @return Core
	 */
	function append_route($route)
	{
		$match = null;
		if( ! preg_match('/^(GET|PUT|POST|DELETE) +(.*)$/', $route, $match)
		   || (trim($match[2])==''))
		{
			throw new \Exception("Malformed route [$route]."
			 ."Routes should start w/ HTTP method GET|PUT|POST|DELETE "
			 ."followed by a URI path segment");
		}
		$http_method = $match[1];
		$disected_route = $this->disect_route($match[2]);
		$this->routes[$route] = new RouteWork(
			$route, $http_method, $disected_route['controller'], $disected_route['uri_path_segments']
		);
		return $this;
	}
	
	function render_view(RouteWork $route)
	{
		$template_payload = array();
		if($route_work = $route->executable_workload())
		{
			$template_payload = array_intersect_key(
				$route_work(array(
					self::URI_PATH_KEY_NAME => $this->matched_request_route_params)
				),
				$route->exposed_work()
			);
		}
		
		if(self::$config['template_engine']=='twig')
		{
			$twig = $this->init_twig();
			echo $twig->render("content/{$route->targeted_view()}.htm.twig", $template_payload);
		} 
		else if(self::$config['template_engine']=='php')
		{
			extract($template_payload);
			include __DIR__ . self::$config['template_dir']."/{$route->targeted_view()}.htm.php";
		}
		else
		{
			throw new \Exception("Unknown template engine: [".self::$config['template_engine']."]");
		}
	}
	/**
	 * @return \Twig_Environment
	 */
	private function init_twig()
	{
		return new \Twig_Environment(
			new \Twig_Loader_Filesystem(self::$config['template_dir']),
			self::$config['template_env']
		);
	}
	/**
	 * For the given request URI look for a matched route defined in 
	 * index.php.  If found, render it.
	 * 
	 * @return void
	 */
	function render_route()
	{
		if($route = $this->match_route())
		{
			$this->render_view($route);
		}
		else
		{
			echo "<pre>\n";
			echo "Requested Route [{$this->request_method} {$this->requested_route}] not found\n";
			echo "Known Routes:\n";
			echo print_r($this->routes, true);
			echo "\n</pre>";
		}
		
	}
	
	/**
	 * 
	 * @return RouteWork
	 */
	private function match_route()
	{
		$matched_route = null;
		$disected_request_route = $this->disect_route($this->requested_route);
		$match = null;
		foreach($this->routes as $known_route)
		{
			if(    $this->request_method == $known_route->http_method()
				&& $disected_request_route['controller'] == $known_route->controller())
			{
				$matched_route = $known_route;
				$this->match_route_keys_to_request_values(
					$matched_route->uri_path_segments(),
					$disected_request_route['uri_path_segments']
				);
				break;
			}
		}
		return $matched_route;
	}
	/**
	 * Take the route defined for a URI and for each placeholder (/:name) look
	 * for a value in the request URI.  The mapping is set to
	 * $this->matched_request_route_params
	 * 
	 * @param array $route_keys
	 * @param array $request_values
	 * @return void
	 */
	private function match_route_keys_to_request_values(array $route_keys, array $request_values)
	{
		foreach($route_keys as $uri_segment_index => $route_key)
		{
			$this->matched_request_route_params[$route_key] = isset($request_values[$uri_segment_index])
				? $request_values[$uri_segment_index]
				: null;
		}
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
	private function disect_route($raw_route)
	{
		$uri_path_segments = explode('/', trim($raw_route, '/'));
		$controller = array_shift($uri_path_segments);
		$controller = $controller==="" ? "/" : $controller;
		return array('controller' => $controller, 'uri_path_segments' => $uri_path_segments);
	}
	
	/**
	 * @return RouteWork
	 */
	private function last_route()
	{
		return end($this->routes);
	}
}
