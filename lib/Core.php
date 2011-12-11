<?php
namespace thundercats;

/**
 * Created by JetBrains PhpStorm.
 * User: sam
 * Date: 12/9/11
 * Time: 7:29 AM
 * To change this template use File | Settings | File Templates.
 */
 
class Core {
	
	const TEMPLATE_ENGINE = 'twig';
	const DEFAULT_CONTROLLER = 'home';
	
	private static $template_dir;
	private static $cache_dir;
	
	private $routes = array();
	private $requested_route;
	private $matched_request_route_params;
	private $request_method;
	
	private function __construct()
	{
		$this->requested_route = strtolower(trim(filter_input(INPUT_GET, '_c')));
		$this->request_method = $_SERVER['REQUEST_METHOD'];
	}
	
	static function autoload($class)
	{
		$class = str_replace('\\', '/', $class) . '.php';
		if( ! strstr($class, __NAMESPACE__)) {return false;}
    	require(str_replace(__NAMESPACE__.'/','',$class));
	}
	
	static function instance()
	{
		static $core;
		if(! $core)
		{
			$core = new self;
		}
		return $core;
	}
	
	public static function templateDir($template_dir)
	{
		self::$template_dir = $template_dir;
	}
	public static function cacheDir($cache_dir)
	{
		self::$cache_dir = $cache_dir;
	}
	
	function appendRoute($route)
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
	
	function doWork(\Closure $work)
	{
		$last_route = $this->last_route();
		$last_route->executable_workload($work);
		return $this;
	}
	
	function expose($name_to_expose_to_view)
	{
		$last_route = $this->last_route();
		$last_route->exposed_work( 
			array_fill_keys(array_map('trim', explode(',', $name_to_expose_to_view)),null)
		);
		return $this;
	}
	
	function render($view_name)
	{
		$last_route = $this->last_route();
		$last_route->targeted_view($view_name);
		return $this;
	}
	
	function renderView(RouteWork $route, $content_template_name=null, $layout_template_name=null)
	{
		$work = $this->append_to_closure(
			$route->executable_workload(), 
			"return get_defined_vars()",
			$this->matched_request_route_params
		);
		$completed_work = $work();
		$template_payload = array_intersect_key($completed_work, $route->exposed_work());
		if(self::TEMPLATE_ENGINE=='twig')
		{
			$loader = new \Twig_Loader_Filesystem(self::$template_dir);
			$twig = new \Twig_Environment($loader, array(
				'cache' 			=> self::$cache_dir.'/twig_cache',
				'auto_reload' 		=> true,
				'debug'				=> true,
				'strict_variables'	=> true,
				'autoescape'		=> true,
			));
			
			echo $twig->render("content/{$route->targeted_view()}.htm.twig", $template_payload);
		} 
		else if(self::TEMPLATE_ENGINE=='php')
		{
			extract($template_payload);
			include __DIR__ . self::$template_dir."/layouts/{$layout_template_name}.htm.php";
		}
	}
	
	function doItLive/*!!*/()
	{
		if($route = $this->match_route())
		{
			$this->renderView($route);
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
	
	private function match_route()
	{
		$matched_route = null;
		$disected_request_route = $this->disect_route($this->requested_route);
		$disected_request_route['controller']=$disected_request_route['controller']===""
			? self::DEFAULT_CONTROLLER
			: $disected_request_route['controller'];
		$match = null;
		foreach($this->routes as $known_route)
		{
			if(    $this->request_method == $known_route->http_method()
				&& $disected_request_route['controller'] == $known_route->controller())
			{
				$matched_route = $known_route;
				$this->matched_request_route_params = array_combine(
					$matched_route->uri_path_segments(), $disected_request_route['uri_path_segments']
				);
				break;
			}
		}
		return $matched_route;
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
		return array('controller' => $controller, 'uri_path_segments' => $uri_path_segments);
	}
	
	/**
	 * This is where things get a little crazy
	 * Take the closure supplied in the doWork call
	 *  - Steal its lines of code
	 *  - append $closure_append to the closure's lines of code
	 *    $closure_append = "return get_defined_vars()"
	 *  - use these lines of code to eval a new closure with
	 *    use ($param) added.
	 *  
	 * @param \Closure $closure
	 * @param string $closure_append 
	 * @param array $param
	 * @return \Closure
	 */
	private function append_to_closure(\Closure $closure, $closure_append, $param)
	{
		$closure_append = "\n".trim($closure_append,"\n; ").";\n";
		
		$reflection_work = new \ReflectionFunction($closure);
		$file = new \SplFileObject($reflection_work->getFileName());
		$file->seek($reflection_work->getStartLine()-1);
		$code = '';
		while ($file->key() < $reflection_work->getEndLine())
		{
			$code .= $file->current();
			$file->next();
		}
		$begin = strpos($code, 'function');
		$end = strrpos($code, '}');
		$code = substr($code, $begin, $end - $begin + 1);
		
		$code = $this->replace_constants($code, dirname($file->getRealPath()));
		
		$code = preg_replace('/(return.*;)/','//$1',$code);
		$code = preg_replace('/(})$/',$closure_append.'$1',$code);
		
		$closure = null;
		$code = str_replace('function()','function() use ($param)',$code);
		eval('namespace '.__NAMESPACE__.'; $closure = '.$code.';');
		return $closure;
	}
	
	/**
	 * @return RouteWork
	 */
	private function last_route()
	{
		return end($this->routes);
	}
	
	private function replace_constants($code, $path_to_code_file)
	{
		$code = str_replace('__DIR__',"'{$path_to_code_file}'", $code);
		return $code;
	}
}
