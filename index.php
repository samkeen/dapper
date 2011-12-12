<?php
namespace clear;
const TOP_DIR = __DIR__;
require __DIR__."/lib/bootup.php";


with("GET /")
	->render('example');

with("GET /hello/:name")
	->do_work(function(){
		$message = "Hello {$param[':name']}";
	})
	->expose('message')
	->render('hello');

