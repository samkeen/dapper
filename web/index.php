<?php
namespace dapper;
require __DIR__ . "/../src/bootup.php";


with("GET /")
    ->render('example');

with("GET /hello/:name")
    ->do_work(function(){
        +$message = "Hello {$path[':name']}";
    })
    ->render('hello');