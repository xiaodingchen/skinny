<?php
/**
 * cache.php
 * 
 * */
class lib_facades_cache extends lib_facades_facade{
    
    private static $__cache;
    
    protected static function getFacadeAccessor()
    {
        if(! static::$__cache)
        {
    
            static::$__cache = new lib_cache_cache();
        }
    
        return static::$__cache;
    }
}
