<?php

class kernel {

    private static $__singleton_instance = [];

    private static $__exception_instance = null;

    /**
     * 错误处理
     */
    public static function startExceptionHandling()
    {
        if (! isset(self::$__exception_instance))
        {
            self::$__exception_instance = kernel::single('lib_exception_handleExceptions');
        }
        
        self::$__exception_instance->bootstrap();
    }

    /**
     * 获取一个对象的实例
     *
     * @param string $className 类名
     * @param mixed $arg 参数
     * @return object
     *
     */
    public static function single($className, $arg = null)
    {
        if (is_object($arg))
        {
            $key = get_class($arg);
            $key = '__class__' . $key;
        }
        else
        {
            $key = md5('__key__' . serialize($arg));
        }
        
        if (! isset(self::$__singleton_instance[$className][$key]))
        {
            self::$__singleton_instance[$className][$key] = new $className($arg);
        }
        
        return self::$__singleton_instance[$className][$key];
    }
}