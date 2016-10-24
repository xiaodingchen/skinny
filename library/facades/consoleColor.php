<?php
/**
 * consoleColor.php
 * 
 * */

class lib_facades_consoleColor extends lib_facades_facade{

    private static $__cache;

    protected static function getFacadeAccessor()
    {
        if(! static::$__cache)
        {

            static::$__cache = new lib_command_colors();
        }

        return static::$__cache;
    }
}
