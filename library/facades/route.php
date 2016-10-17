<?php


class lib_facades_route extends lib_facades_facade
{
    /**
	 * The router instance
	 *
	 * @var lib_routing_router
	 */
    private static $__router;

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        if (!static::$__router)
        {
            static::$__router =  kernel::single('lib_routing_router', request::instance());
            route::boot();
        }
        return static::$__router;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    protected static function boot()
    {
        /*if (kernel::routesAreCached())
        {
            self::loadCachedRoutes();
        }
        else
        {
            self::loadRoutes();
        }*/
        self::loadRoutes();
    }

	/**
	 * Load the cached routes for the application.
	 *
	 * @return void
	 */
	/*protected static function loadCachedRoutes()
	{
        include kernel::getCachedRoutesPath();
	}*/

	/**
	 * Load the application routes.
	 *
	 * @return void
	 */
	protected function loadRoutes()
	{
        $paths[] = BOOT_DIR.'/routes.php';

        $file = kernel::single('lib_lib_filesystem');
        foreach($paths as $path)
        {
            if ($file->exists($path)) return require($path);
        }

        throw new \ErrorException('Cannot load routes.');
	}
}
