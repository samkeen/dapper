<?php
namespace dapper;
require __DIR__ . "/../lib/bootup.php";


with("GET /")
	->render('example');

with("GET /hello/:name")
	->do_work(function(){
		$message = "Hello {$path[':name']}";
	})
	->expose('message')
	->render('hello');