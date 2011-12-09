<?php
namespace thundercats;
const TOP_DIR = __DIR__;
require __DIR__."/lib/bootup.php";


with("GET /home")
	->doWork(function(){
		$environments['apc'] 	= Util::arr_extract_key_pattern('/apc\./',ini_get_all());
		$environments['xhprof'] = Util::arr_extract_key_pattern('/xhprof\./',ini_get_all());
		$environments['xdebug'] = Util::arr_extract_key_pattern('/xdebug\./',ini_get_all());
	})
	->expose('environments')
	->render('home');

with("GET /hello/:name")
	->doWork(function(){
		$message = "Hello";//.segment(':name');
	})
	->expose('message')
	->render('hello');

