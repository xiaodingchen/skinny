<?php
/**
 * redirect.php
 * 
 * */
class lib_facades_redirect extends lib_facades_facade
{
    /**
     * The routing redirector instance
     *
     * @var lib_routing_redirector
     */
    private static $__redirect;

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor() {
        if (!static::$__redirect)
        {
            static::$__redirect = new lib_routing_redirector(url::instance());
        }
        return static::$__redirect;
    }

}
