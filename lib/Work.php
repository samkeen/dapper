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
		return $closure();
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
