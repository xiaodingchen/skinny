<?php
/**
 * redis.php
 *
 * */
class lib_facades_redis extends lib_facades_facade
{
    /**
     * The redis instance
     *
     * @var base_redis_database
     */
    private static $__redis;

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        if (!static::$__redis)
        {
            static::$__redis = new lib_redis_client();
        }
        return static::$__redis;
    }
}
