<?php
namespace clear;
/**
 * 
 */
class ExtractingClosure {
	
	/**
	 * This is the statement that is added to the Closure in order
	 * to export its variables
	 * 
	 * @var string
	 */
	private $extraction_statement = "return get_defined_vars()";
	/**
	 * @var \Closure
	 */
	private $initial_closure;
	
	function __construct(\Closure $initial_closure)
	{
		$this->initial_closure = $initial_closure;
	}

	
	/**
	 * This is where things get a little crazy
	 * Take the closure supplied in the doWork call
	 *  - Steal its lines of code
	 *  - append $this->extraction_statement to the closure's lines of code
	 *  - use these lines of code to eval a new closure with
	 *    use ($param) added.
	 * 
	 * @param array $closure_uses 
	 * ex: array(
	 *   'path' => array(
	 *     ':name' => 'bob' 
	 * )
	 * Will result in 
	 * $path = array(':name' => 'bob');
	 * being defined in this functions namespace and
	 * 'use ($path)' 
	 * being included in the construction of the new closure.
	 * 
	 * @return \Closure
	 */
	function invoke(array $closure_uses = null)
	{
		// bring paramd into this namespace
		extract($closure_uses);
		// construct the use() statement to expose these params to the new closure 
		$use_statement = $this->closure_use_statement(array_keys($closure_uses));
		$closure_append = "\n".trim($this->extraction_statement,"\n; ").";\n";
		
		$reflection_work = new \ReflectionFunction($this->initial_closure);
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
		$code = str_replace('function()','function() '.$use_statement, $code);
		eval('namespace '.__NAMESPACE__.'; $closure = '.$code.';');
		return $closure;
	}
	
	private function closure_use_statement($uses)
	{
		return "use (".implode(", ", array_map(
			function($val){return "\${$val}";},
			$uses)).")";
	}
	
	/**
	 * @param $code
	 * @param $path_to_code_file
	 * @return mixed
	 */
	private function replace_constants($code, $path_to_code_file)
	{
		$code = str_replace('__DIR__',"'{$path_to_code_file}'", $code);
		return $code;
	}
}
