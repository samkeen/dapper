<?php
/**
 * @package dapper
 */
namespace dapper;

/**
 * Work (that a route can have performed on its behalf)
 * Behaves as a closure (__invoke) but when called it is 
 * it is transformed into an ExtractingClosure and then invoked.
 * 
 * @package dapper
 */
class Work {

    private $closure;
    
    function __construct(\Closure $workload)
    {
        $this->closure = $workload;
    }
    /**
     * @param array|null $param
     * @return mixed
     */
    public function __invoke(array $param = null)
    {
        /*
         * Lazily convert the Closure to an ExtractingClosure
         * and then invoke that with the supplied $params
         */
        $extracting_closure = new ExtractingClosure($this->closure);
        $closure = $extracting_closure->transform($param);
        /*
         * array(
         *   path    => array(),
         *   message => ""
         * )
         */
        $template_payload = array_intersect_key(
            /*
             * the param used when executing the Extracting closure signifies
             * the variable scope that will be used (use()) for the ultimate
             * execution of the closure.
             */
            $closure(),
            array_fill_keys($extracting_closure->exposed_var_names(), null)
        );
        
        
        return $template_payload;
    }
    
    /**
     * simple getter
     * Used for testing
     * 
     * @return \Closure
     */
    function closure()
    {
        return $this->closure;
    }
    
    
}
