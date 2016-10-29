<?php

/**
 * validator.php
 *
 * */
class lib_facades_validator extends lib_facades_facade {

    /**
     * The cache manager instance
     *
     * @var base_cache_manager
     */
    private static $__validator;

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        if (! static::$__validator)
        {
            static::$__validator = kernel::single('lib_validator_factory');
        }
        return static::$__validator;
    }
}
