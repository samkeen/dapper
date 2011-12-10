Very simple PHP web framework


```php
with("GET /hello/:name")
	->doWork(function(){
		$name = segment(':name');
		$message = "Hello $name";
	})
	->expose('message') // ->expose is optional. Defaults to all
	->render('hello');  // ->render is optional. Defaults to first URI segemnt
```