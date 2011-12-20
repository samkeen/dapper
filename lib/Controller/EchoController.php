<?php
/**
 * Original Author: sam
 * Date: 12/20/11
 * Time: 12:27 PM
 */
class EchoController
{
	
	function __construct()
	{
		$this->get = function($param){
			return $param;
		};
		$this->post = function($param){
			return $param;
		};
		$this->put = function($param){
			return $param;
		};
		$this->delete = function($param){
			return $param;
		};
		
	}

}

/**
 * OR in style of index.php
 */

		
EchoController()
	->learn()
	->get(	
		function($param){
			return $param;
		}
	)
	->learn()
	->post(	
		function($param){
			return $param;
		}
	)
	->learn()
	->get(
		function($param){
			return $param;
			
		}
	)
	->learn()
	->post(
		function($param){
			return $param;
		}
);


