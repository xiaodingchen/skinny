<?php


use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use lib_http_request as Request;
use lib_http_response as Response;
use lib_routing_route as Route;
use lib_pipeline_pipeline as Pipeline;
use lib_support_collection as Collection;

class lib_routing_router
{

	/**
	 * The route collection instance.
	 *
	 * @var \Illuminate\Routing\RouteCollection
	 */
	protected $routes;
    

	/**
	 * The currently dispatched route instance.
	 *
	 * @var \Illuminate\Routing\Route
	 */
	protected $current;

	/**
	 * The request currently being dispatched.
	 *
	 * @var \Illuminate\Http\Request
	 */
	protected $currentRequest;

	/**
	 * All of the short-hand keys for middlewares.
	 *
	 * @var array
	 */
	protected $middleware = [];
    

	/**
	 * The globally available parameter patterns.
	 *
	 * @var array
	 */
	protected $patterns = array();
    

	/**
	 * The route group attribute stack.
	 *
	 * @var array
	 */
	protected $groupStack = array();


	/**
	 * Create a new Router instance.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $events
	 * @param  \Illuminate\Container\Container  $container
	 * @return void
	 */
    //	public function __construct(Dispatcher $events, Container $container = null)
  	public function __construct()
	{
        //		$this->events = $events;
        //		$this->routes = new RouteCollection;
        
        $this->routes = new lib_routing_collection;
        //		$this->container = $container ?: new Container;
        //		$this->bind('_missing', function($v) { return explode('/', $v); });
	}

	/**
	 * Register a new GET route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function get($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD'], $uri, $action);
	}

	/**
	 * Register a new POST route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function post($uri, $action)
	{
		return $this->addRoute('POST', $uri, $action);
	}

	/**
	 * Register a new PUT route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function put($uri, $action)
	{
		return $this->addRoute('PUT', $uri, $action);
	}

	/**
	 * Register a new PATCH route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function patch($uri, $action)
	{
		return $this->addRoute('PATCH', $uri, $action);
	}

	/**
	 * Register a new DELETE route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function delete($uri, $action)
	{
		return $this->addRoute('DELETE', $uri, $action);
	}

	/**
	 * Register a new OPTIONS route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function options($uri, $action)
	{
		return $this->addRoute('OPTIONS', $uri, $action);
	}

	/**
	 * Register a new route responding to all verbs.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function any($uri, $action)
	{
		$verbs = array('GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE');

		return $this->addRoute($verbs, $uri, $action);
	}

	/**
	 * Register a new route with the given verbs.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function match($methods, $uri, $action)
	{
		return $this->addRoute($methods, $uri, $action);
	}
    
	/**
	 * Create a route group with shared attributes.
	 *
	 * @param  array     $attributes
	 * @param  \Closure  $callback
	 * @return void
	 */
	public function group(array $attributes, Closure $callback)
	{
		$this->updateGroupStack($attributes);

		// Once we have updated the group stack, we will execute the user Closure and
		// merge in the groups attributes when the route is created. After we have
		// run the callback, we will pop the attributes off of this group stack.
		call_user_func($callback, $this);

		array_pop($this->groupStack);
	}

	/**
	 * Update the group stack with the given attributes.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	protected function updateGroupStack(array $attributes)
	{
		if ( ! empty($this->groupStack))
		{
			$attributes = $this->mergeGroup($attributes, last($this->groupStack));
		}

		$this->groupStack[] = $attributes;
	}

	/**
	 * Merge the given array with the last group stack.
	 *
	 * @param  array  $new
	 * @return array
	 */
	public function mergeWithLastGroup($new)
	{
		return $this->mergeGroup($new, last($this->groupStack));
	}
    

	/**
	 * Merge the given group attributes.
	 *
	 * @param  array  $new
	 * @param  array  $old
	 * @return array
	 */
	public static function mergeGroup($new, $old)
	{
		$new['prefix'] = static::formatGroupPrefix($new, $old);

		if (isset($new['domain'])) unset($old['domain']);

        $new['where'] = array_merge(array_get($old, 'where', []), array_get($new, 'where', []));

        //return array_merge_recursive(array_except($old, array('namespace', 'prefix', 'where')), $new);
		return array_merge_recursive(array_except($old, array('prefix')), $new);
	}

	/**
	 * Format the prefix for the new group attributes.
	 *
	 * @param  array  $new
	 * @param  array  $old
	 * @return string
	 */
	protected static function formatGroupPrefix($new, $old)
	{
		if (isset($new['prefix']))
		{
			return trim(array_get($old, 'prefix'), '/').'/'.trim($new['prefix'], '/');
		}

		return array_get($old, 'prefix');
	}

	/**
	 * Get the prefix from the last group on the stack.
	 *
	 * @return string
	 */
	protected function getLastGroupPrefix()
	{
		if ( ! empty($this->groupStack))
		{
			$last = end($this->groupStack);
			return isset($last['prefix']) ? $last['prefix'] : '';
		}

		return '';
	}
    
	/**
	 * Add a route to the underlying route collection.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	protected function addRoute($methods, $uri, $action)
	{
 		return $this->routes->add($this->createRoute($methods, $uri, $action));
	}

	/**
	 * Create a new route instance.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  mixed   $action
	 * @return \Illuminate\Routing\Route
	 */
	protected function createRoute($methods, $uri, $action)
	{
		// If the route is routing to a controller we will parse the route action into
		// an acceptable array format before registering it and creating this route
		// instance itself. We need to build the Closure that will call this out.
		if ($this->actionReferencesController($action))
		{
            $action = $this->convertToControllerAction($action);
            
		}
        //        echo '<pre>';
        //        var_dump($action);
        //        exit;

		$route = $this->newRoute(
			$methods, $uri = $this->prefix($uri), $action
		);

		// If we have groups that need to be merged, we will merge them now after this
		// route has already been created and is ready to go. After we're done with
		// the merge we will be ready to return the route back out to the caller.
		if ( ! empty($this->groupStack))
		{
			$this->mergeGroupAttributesIntoRoute($route);
		}

		$this->addWhereClausesToRoute($route);

		return $route;
	}

	/**
	 * Create a new Route object.
	 *
	 * @param  array|string $methods
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return \Illuminate\Routing\Route
	 */
	protected function newRoute($methods, $uri, $action)
	{
		return new lib_routing_route($methods, $uri, $action);
	}
    
	/**
	 * Prefix the given URI with the last prefix.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected function prefix($uri)
	{
		return trim(trim($this->getLastGroupPrefix(), '/').'/'.trim($uri, '/'), '/') ?: '/';
	}

	/**
	 * Add the necessary where clauses to the route based on its initial registration.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @return \Illuminate\Routing\Route
	 */
	protected function addWhereClausesToRoute($route)
	{

		$route->where(
			array_merge($this->patterns, array_get($route->getAction(), 'where', []))
		);

		return $route;
	}

	/**
	 * Merge the group stack with the controller action.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @return void
	 */
	protected function mergeGroupAttributesIntoRoute($route)
	{
		$action = $this->mergeWithLastGroup($route->getAction());

		$route->setAction($action);
	}
    

	/**
	 * Determine if the action is routing to a controller.
	 *
	 * @param  array  $action
	 * @return bool
	 */
	protected function actionReferencesController($action)
	{
		if ($action instanceof Closure) return false;

		return is_string($action) || is_string(array_get($action, 'uses'));
	}


	/**
	 * Add a controller based route action to the action array.
	 *
	 * @param  array|string  $action
	 * @return array
	 */
	protected function convertToControllerAction($action)
	{
		if (is_string($action)) $action = array('uses' => $action);

		// Here we will set this controller name on the action array just so we always
		// have a copy of it for reference if we need it. This can be used while we
		// search for a controller name or do some other type of fetch operation.
		$action['controller'] = $action['uses'];

		return $action;
	}
    
	/**
	 * Dispatch the request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function dispatch(lib_http_request $request)
	{
		$this->currentRequest = $request;

		// If no response was returned from the before filter, we will call the proper
		// route instance to get the response. If no route is found a response will
		// still get returned based on why no routes were found for this request.
		//$response = $this->callFilter('before', $request);

        /*
		if (is_null($response))
		{
			$response = $this->dispatchToRoute($request);
		}
        */
        $response = $this->dispatchToRoute($request);
		$response = $this->prepareResponse($request, $response);

		// Once this route has run and the response has been prepared, we will run the
		// after filter to do any last work on the response or for this application
		// before we will return the response back to the consuming code for use.
		//$this->callFilter('after', $request, $response);

		return $response;
	}

	/**
	 * Dispatch the request to a route and return the response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function dispatchToRoute(lib_http_request $request)
	{
		$route = $this->findRoute($request);

        $response = $this->runRouteWithinStack($route, $request);
        // 如果控制器不返回任何信息, 意味着它决定自己做输出处理, 那么可以放弃对它采取后续行动
        if (is_null($response))
        {
            exit;
        }

		$response = $this->prepareResponse($request, $response);

		// After we have a prepared response from the route or filter we will call to
		// the "after" filters to do any last minute processing on this request or
		// response object before the response is returned back to the consumer.
		//$this->callRouteAfter($route, $request, $response);

		return $response;
	}

	/**
	 * Run the given route within a Stack "onion" instance.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	protected function runRouteWithinStack(Route $route, Request $request)
	{
        $middleware = $this->gatherRouteMiddlewares($route);
        return (new Pipeline())
        ->send($request)
                               ->through($middleware)
                               ->then(function($request) use ($route) {
                                   return $this->prepareResponse(
                                       $request,
                                       //$route->run($request) 
                                       $this->runRoute($route, $request)
                                   );
                               });
	}

    protected function runRoute($route, $request)
    {
        $cacheStrategys = config::get('page_cache.pages');

		if ($request->getMethod() == 'GET' && $cacheStrategys != null) {
            if ($route->getName() !== null && ($cacheStrategy = $cacheStrategys[$route->getName()])) {
                $cacheKey = md5($request->fullUrl());
                $timeout = (int)$cacheStrategy['timeout'] !==0 ? $cacheStrategy['timeout'] : 1;
                return unserialize(cache::store('controller-cache')->remember($cacheKey, $timeout, function() use ($request, $response, $route) {
                    return serialize($route->run($request));
                }));
            }
        }
		return $route->run($request);        
    }

	/**
	 * Gather the middleware for the given route.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @return array
	 */
	public function gatherRouteMiddlewares(Route $route)
	{
		$return = Collection::make($route->middleware())->map(function($m)
		{
			return Collection::make(array_get($this->middleware, $m, $m));

		});
        return $return->collapse()->all();
	}
    
	/**
	 * Find the route matching a given request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Routing\Route
	 */
	protected function findRoute($request)
	{
		$this->current = $route = $this->routes->match($request);

		//return $this->substituteBindings($route);
        // 目前不支持bind
        return $route;
	}

	/**
	 * Get all of the defined middleware short-hand names.
	 *
	 * @return array
	 */
	public function getMiddleware()
	{
		return $this->middleware;
	}

	/**
	 * Register a short-hand name for a middleware.
	 *
	 * @param  string  $name
	 * @param  string  $class
	 * @return $this
	 */
	public function middleware($name, $class)
	{
		$this->middleware[$name] = $class;

		return $this;
	}

	/**
	 * Set a global where pattern on all routes
	 *
	 * @param  string  $key
	 * @param  string  $pattern
	 * @return void
	 */
	public function pattern($key, $pattern)
	{
		$this->patterns[$key] = $pattern;
	}

	/**
	 * Set a group of global where patterns on all routes
	 *
	 * @param  array  $patterns
	 * @return void
	 */
	public function patterns($patterns)
	{
		foreach ($patterns as $key => $pattern)
		{
			$this->pattern($key, $pattern);
		}
	}
    


	/**
	 * Create a response instance from the given value.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @param  mixed  $response
	 * @return \Illuminate\Http\Response
	 */
	protected function prepareResponse($request, $response)
	{
		if ( ! $response instanceof SymfonyResponse)
		{
			$response = new lib_http_response($response);
		}

		return $response->prepare($request);
	}
    

	/**
	 * Get the currently dispatched route instance.
	 *
	 * @return \Illuminate\Routing\Route
	 */
	public function current()
	{
		return $this->current;
	}

	/**
	 * Get the current route name.
	 *
	 * @return string|null
	 */
	public function currentRouteName()
	{
		return ($this->current()) ? $this->current()->getName() : null;
	}
    

	/**
	 * Get the request currently being dispatched.
	 *
	 * @return \Illuminate\Http\Request
	 */
	public function getCurrentRequest()
	{
		return $this->currentRequest;
	}

	/**
	 * Get the underlying route collection.
	 *
	 * @return \Illuminate\Routing\RouteCollection
	 */
	public function getRoutes()
	{
		return $this->routes;
	}

	/**
	 * Set the route collection instance.
	 *
	 * @param  \Illuminate\Routing\RouteCollection  $routes
	 * @return void
	 */
	public function setRoutes(RouteCollection $routes)
	{
		$this->routes = $routes;
	}

	/**
	 * Get the controller dispatcher instance.
	 *
	 * @return \Illuminate\Routing\ControllerDispatcher
	 */
	public function getControllerDispatcher()
	{
		if (is_null($this->controllerDispatcher))
		{
			$this->controllerDispatcher = new ControllerDispatcher($this, $this->container);
		}

		return $this->controllerDispatcher;
	}
}
