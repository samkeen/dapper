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
	
	public function __invoke($param)
	{
		$closure = $this->invoke_closure($param);
		return $closure();
	}
	
	/**
	 * This is where things get a little crazy
	 * Take the closure supplied in the doWork call
	 *  - Steal its lines of code
	 *  - append $this->extraction_statement to the closure's lines of code
	 *  - use these lines of code to eval a new closure with
	 *    use ($param) added.
	 * 
	 * @param $param
	 * @return \Closure
	 */
	private function invoke_closure($param)
	{
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
		$code = str_replace('function()','function() use ($param)',$code);
		eval('namespace '.__NAMESPACE__.'; $closure = '.$code.';');
		return $closure;
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
