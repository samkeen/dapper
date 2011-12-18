<?php
namespace clear;
/**
 * Abtract Work (that a route can have performed on its behalf)
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
		 * Lazilly convert the Closure to an ExtractingClosure
		 * and then invoke that with the suppyled $params
		 */
		$extracting_closure = new ExtractingClosure($this->closure);
		$closure = $extracting_closure->invoke($param);
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
