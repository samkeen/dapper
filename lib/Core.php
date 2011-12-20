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
			
	private $config = array(
		'template_engine' => 'php',
		'template_env' => array(),
		'template_dir' => null,
		'cache_dir' => null
	);
	
	private $routes = array();
	/**
	 * @var Route
	 */
	private $requested_route;
	private $matched_request_route_params;
	
	/**
	 * @param string $request_method
	 * @param array $config
	 */
	function __construct($request_method, $config)
	{
		$this->config = array_merge($this->config, $config);
		$this->requested_route = new Route(
			$request_method, strtolower(trim(filter_input(INPUT_GET, '_c'))),
			$is_request_route = true
		);
		if($this->config['template_engine']=="twig")
		{
			require_once TOP_DIR . '/vendors/twig/lib/Twig/Autoloader.php';
			\Twig_Autoloader::register();
		}
	}
	
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
		$this->last_route()->work($work);
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
		$name_to_expose_to_view = trim($name_to_expose_to_view);
		if($name_to_expose_to_view !== "")
		{
			$this->last_route()->exposed_work_var_names(
				// remove empty elements and re-index
				array_values(array_filter(preg_split('/[\s,]/',$name_to_expose_to_view)))
			);
		}
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
		$view_name = trim($view_name);
		if( ! $view_name)
		{
			throw new \InvalidArgumentException('$view_name param should not be empty');
		}
		$this->last_route()->view_name($view_name);
		return $this;
	}
	
	
	/*
	 * All the following methods are not meant to be used in index.php.  They are
	 * internals meant for Core and bootup.php
	 */
	
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
	 * 
	 * @param string $route 
	 * @return Core
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
		$http_method = $match['method'];
		$this->routes[$route] = new Route(
			$http_method, $match['path']
		);
		return $this;
	}
	/**
	 * This extracts a 'payload' from the Route for the matched Route
	 * The 'payload' is a set scope of variables retrieved when invoking
	 * the Route's ExtractingClosure.
	 * This variable scope is intersected with the whitelist defined by ->exposse() 
	 * for the the given $route.
	 * Finally, this variable scope is sent to a rendering engine which will render 
	 * a view.
	 * 
	 * @param Route $route
	 * @throws \Exception
	 */
	function render_view(Route $route)
	{
		$template_payload = array();
		if($route_work = $route->work())
		{
			$template_payload = array_intersect_key(
				/*
				 * the param used when executing the Extracting closure signifies
				 * the variable scope that will be used (use()) for the ultimate
				 * execution of the closure.
				 */
				$route_work(array(
					self::URI_PATH_KEY_NAME => $this->matched_request_route_params)
				),
				/*
				 * an ExtractionClosre retuns all of its internal var scope as a key/val
				 * array. Of that array, $route->exposed_work_var_names() is a white list of keys 
				 * that determines what will be exposed to the view tier ($template_payload)
				 */
				array_fill_keys($route->exposed_work_var_names(), null)
			);
		}
		
		if($this->config['template_engine']=='twig')
		{
			$twig = $this->init_twig();
			echo $twig->render("content/{$route->view_name()}.htm.twig", $template_payload);
		} 
		else if($this->config['template_engine']=='php')
		{
			extract($template_payload);
			include __DIR__ . $this->config['template_dir']."/{$route->view_name()}.htm.php";
		}
		else
		{
			throw new \Exception("Unknown template engine: [".$this->config['template_engine']."]");
		}
	}
	/**
	 * @return \Twig_Environment
	 */
	private function init_twig()
	{
		return new \Twig_Environment(
			new \Twig_Loader_Filesystem($this->config['template_dir']),
			$this->config['template_env']
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
			echo "Requested Route [{$this->requested_route->http_method()} {$this->requested_route->path()}] not found\n";
			echo "Known Routes:\n";
			echo print_r($this->routes, true);
			echo "\n</pre>";
		}
	}
	
	/**
	 * 
	 * @return Route
	 */
	private function match_route()
	{
		$matched_route = null;
		$match = null;
		foreach($this->routes as $known_route)
		{
			if(    $this->requested_route->http_method() == $known_route->http_method()
				&& $this->requested_route->controller() == $known_route->controller())
			{
				$matched_route = $known_route;
				$this->match_route_keys_to_request_values(
					$matched_route->uri_path_segments(),
					$this->requested_route->uri_path_segments()
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
	 * @return Route
	 */
	function last_route()
	{
		if( ! $last_route = end($this->routes))
		{
			throw new Exception\InvalidStateException(__METHOD__."() called with no routes defined");
		}
		return $last_route;
	}
}
