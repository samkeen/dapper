<?php
/**
 * @package dapper
 */
namespace dapper;
/**
 * 
 * @package dapper
 */
class ExtractingClosure {
    
	/**
     * Protect $this + the internal vars of ExtractingClosure::transform()
     * 
     * @var array
     */
    private $use_statement_name_blacklist = array(
        'this',
        '__closure_uses',
        '__code',
        '__closure'
    );
    
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
    /**
     * @var array
     */
    private $exposed_var_names = array();
	
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
	 * @param array $__closure_uses 
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
	function transform(array $__closure_uses = null)
	{
        $__closure_uses = (array)$__closure_uses;
        $this->check_dissalowed_keywords($__closure_uses);
        $this->assert_valid_var_names($__closure_uses);
		// bring params into this namespace
	    extract($__closure_uses);
		// construct the use() statement to expose these params to the new closure 
		$__code = $this->build_closure_code_string($__closure_uses);
		$__closure = null;
		eval('namespace '.__NAMESPACE__.'; $__closure = '.$__code.';');
		return $__closure;
	}
    
    function exposed_var_names()
    {
        return $this->exposed_var_names;
    }
    
    private function build_closure_code_string($closure_uses)
    {
        $use_statement = $this->closure_use_statement(array_keys($closure_uses));
        $closure_append = "\n".trim($this->extraction_statement,"\n; ").";\n";
        $reflection_work = new \ReflectionFunction($this->initial_closure);
        $file = new \SplFileObject($reflection_work->getFileName());
        $file->seek($reflection_work->getStartLine()-1);
        $code = '';
        while ($file->key() < $reflection_work->getEndLine())
        {
            $expose_variables_match = array();
            $code_line = $file->current();
            /*
             * extract any found (+) notated variables
             */
            if(preg_match_all('/\+ ?\$(?P<var_name>\w+)/', $code_line, $expose_variables_match))
            {
                $this->exposed_var_names = array_merge(
                    $this->exposed_var_names, 
                    $expose_variables_match['var_name']
                );
            }
            $code .= $file->current();
            $file->next();
        }
        $begin = strpos($code, 'function');
        $end = strrpos($code, '}');
        $code = substr($code, $begin, $end - $begin + 1);
        $code = $this->replace_constants($code, dirname($file->getRealPath()));
        $code = preg_replace('/(return.*;)/','//$1',$code);
        $code = preg_replace('/(})$/',$closure_append.'$1',$code);
        $code = str_replace('function()','function() '.$use_statement, $code);
        return $code;
    }
	
	private function closure_use_statement(array $uses=null)
	{
        $use_statement = '';
        if($uses)
        {
            $use_statement = "use (".implode(", ", array_map(
                function($val){return "\${$val}";},
                $uses)).")";
        }
		return $use_statement;
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
    /**
     * When writing the new Closure, this will dissalow things like
     * "function use($this) {" <-putting $this in use() would be bad
     * 
     * @param $use_var_scope
     * @throws \InvalidArgumentException
     */
    private function check_dissalowed_keywords($use_var_scope)
    {
        $intersected_dissalowed_names = array_intersect_key(
            array_fill_keys($this->use_statement_name_blacklist, null),
            $use_var_scope
        );
        if($intersected_dissalowed_names)
        {
            throw new \InvalidArgumentException("The use scope names ["
            .implode(',', array_keys($intersected_dissalowed_names))."] are dissalowed\n"
            ."'use scope names' are the var names that will go into the closure use() statment");
        }
    }
    /**
     * @param $use_var_scope
     * @throws \InvalidArgumentException
     */
    private function assert_valid_var_names($use_var_scope)
    {
        foreach($use_var_scope as $var_name => $var_value)
        {
            if( ! preg_match('/^[a-z_]([\w]+)?$/i', $var_name))
            {
                throw new \InvalidArgumentException("use scope names must be valid PHP "
                ."var names.  [{$var_name}] is not a valid name\n"
                ."'use scope names' are the var names that will go into the closure use() statment");
            }
        }
    }
}
