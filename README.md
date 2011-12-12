Clear | Expound | Neutron | Quark | Terse | Sketch | Neutrino

A terse and restful PHP front controller based web framework.
The focus of this framework is on the syntax of the route matching in the front controller (index.php). 
"when you see this route, expose this from this work to this view."

## Template Support 

* templateless
* plain old php
* twig

## ORM support

??

## initializer word
- with
- upon
- when

## dialects


### logical

```php
with("GET /hello/:name")
	->do_work(function(){
		$name = segment(':name');
		$message = "Hello $name";
	})
	->expose('message') // ->expose is optional. Defaults to all
	->render('hello');  // ->render is optional. Defaults to first URI segemnt
```

### fluent dialects

Alternate Syntax

```php
with('GET /')
  ->expose('message')
  ->from_work(function(){
    $name = segment(':name');
	$message = "Hello $name";
  })
  ->to_view('hello');
```

```php
with('GET /')->
  expose('message')->
  from_work(function(){
    $name = segment(':name');
	$message = "Hello $name";
  })->
  to_view('hello');
```

```php
with('GET /')
  ->
  expose('message')
  ->
  from_work(function(){
    $name = segment(':name');
    $message = "Hello $name";
  })
  ->
  to_view('hello');
```

```php
with('GET /')
->
expose('message')
->
from_work(function(){
  $name = segment(':name');
  $message = "Hello $name";
})
->
to_view('hello');
```

```php
upon('GET /')
  ->expose('message')
  ->from_work(function(){
    $name = segment(':name');
	$message = "Hello $name";
  })
  ->to_view('hello');
```

```php
upon('GET /')->

  expose('message')->
  
  from_work(function(){
    $name = segment(':name');
	$message = "Hello $name";
  })->
  
  to_view('hello');
```

```php
upon('GET /')->
  expose('message')->
  from(function(){
    $name = segment(':name');
	$message = "Hello $name";
  })->
  to_view('hello');
```

```php
_for('GET /')
  ->expose('message')
  ->from_work(function(){
    $name = segment(':name');
	$message = "Hello $name";
  })
  ->to_view('hello');
```

```php
for_('GET /')
  ->expose('message')
  ->from_work(function(){
    $name = segment(':name');
	$message = "Hello $name";
  })
  ->to_view('hello');
```

## Examples

simplist
 |
 |
 |
\./
complex