<?php
namespace thundercats;
const TOP_DIR = __DIR__;
require __DIR__."/lib/bootup.php";



with("GET /hello/:name")
	->doWork(function(){
		$message = "Hello {$param[':name']}";
	})
	->expose('message')
	->render('hello');

