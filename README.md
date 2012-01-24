## At this point, this is still an experiment.

I do plan to continue to develop Dapper, but it is still in its infancy and is still prone to large implementation changes.
Once the code base settles down and I get profiling test in place plus better documentation I'll tag a 1.0 release.

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
                 $name = ucfirst($path[':name']);
                +$message = "Hello {$name}";
        })
        ->render('hello');
```

As you can see, closures and a [fluent interface](http://martinfowler.com/bliki/FluentInterface.html) are utilized to provide this syntax in a low noise, elegant way.

This code snippet also demonstrates a notation invented for the framework; placing an addition mark (**+**) infront of a variable assigment.

This (+) syntax was choosen for a couple of reasons

- Reuse, it is used in UML to mark something as 'public'
- It has no side effect (that i can detect) to the assignemnt statement and the file will still lint (php -l) with no errors.

This has the effect of exposing that particular variable to the view.  So in the case above, `$message` will be exposed to the view, but `$name` will not.

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
		+$message = "Hello {$path[':name']}";
	})
	->render('hello');
```