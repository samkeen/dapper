## Goals

* **Compactness and low noise**
Acomplish whats needs in a way that makes sense with as little code as possible.

* **Law Of Demeter enforced**
Classes and methods are small and specific.  Strive to have proper separation of of concernes throughout the codebase.  

* **Laziness**
Do work only when you have to.

  * Only the matched Route's work is analyzed
  * As soon as a route is matched, parsing/analysis of further routes stops.

## Summary
A terse and restful PHP front controller based web framework.
At this point this framework is further into the experimental realm than the practical use realm.  I do plan to eventually take it squarely into the usable framework spectrum soon.
The focus of this framework is on the simplicity of the syntax of the front controller (index.php).
In that controller, route matching takes the form:
    "when you see this route, expose these parts from this work to this view."
Or in code:

``` php
<?php
with("GET /")
	->render('home');

with("GET /hello/:name")
	->do_work(function(){
		$message = "Hello {$path[':name']}";
	})
	->expose('message')
	->render('hello');
```

As you can see, closures and a [fluent interface](http://martinfowler.com/bliki/FluentInterface.html) are utilized to provide this syntax in a low noise, elegant way.

## Template Support 
A pluggable templating system is in place (The Render objects referenced below).
Possible template strategies range from:

* none (template-less)
* plain old php
* twig
* anthing that a Render adapter is written for.

## ORM support
This is currently the glaring omission from this framework.  It will be added in a pluggable manner similar to the templating strategy.

## Key Objects

* `Env`
Env contains the context of the request.  This includes all pertinent aspects of the 'Server' and the 'Request'. 

* `Route`
A Route is made up of a method (get, post, put, ...) and a path (/users/1, /posts/latest)
Routes are utilized in two situations in this system.

  * The request Route
  * The Router has many learned Routes

* `Work`
Learned Routes of the Router can optionally have Work to perform.  This Work of Route that is a
match to the request Route is invoked in the Responder.

* `Router`
The sole purpose of the Router is to attempt to match the request Route to one of its learned Routes.

* `Responder`
The Responder is responsible for stitching the result of the matched Route's Work with a RenderStrategy.  Then
send that render back to the Client.
Example Responders are HTTP and CLI.

* `Render`
Used by Responder to combine the data (result of the Route's work) with a view.

## Examples

More TBDocumented

``` php
<?php
with("GET /")
	->render('home');

with("GET /hello/:name")
	->do_work(function(){
		$message = "Hello {$path[':name']}";
	})
	->expose('message')
	->render('hello');
```