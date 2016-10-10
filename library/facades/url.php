<?php
/**
 * url.php
 * 
 * */
class lib_facades_url extends lib_facades_facade
{
    /**
     * Return the Request instance
     *
     * @var \Symfony\Component\HttpFoundation\Request;
     */

    private static $__url;

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        if (!static::$__url)
        {
            $routes = route::getRoutes();

            static::$__url = new lib_routing_urlgenerator($routes, request::instance());
        }
        return static::$__url;
    }
}
