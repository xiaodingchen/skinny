<?php
abstract class lib_facades_facade {
    /**
     * Alias to getFacadeRoot
     *
     * @return mixed
     */
    public static function instance() {
        return static::getFacadeRoot();
    }
    
    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot() {
        if (is_object($object = static::getFacadeAccessor())) {
            return $object;
        }
        throw new \RuntimeException(sprintf('Facade:%s need getFacadeAccessor method return object', get_class()));
    }
    
    /**
     * Get the registered name of the component.
     *
     * @return object
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor() {
        throw new \RuntimeException("Facade does not implement getFacadeAccessor method.");
    }
    
    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method            
     * @param array $args            
     * @return mixed
     */
    public static function __callStatic($method, $args) {
        $instance = static::getFacadeRoot();
        
        switch (count($args)) {
            case 0 :
                return $instance->$method();
            
            case 1 :
                return $instance->$method($args [0]);
            
            case 2 :
                return $instance->$method($args [0], $args [1]);
            
            case 3 :
                return $instance->$method($args [0], $args [1], $args [2]);
            
            case 4 :
                return $instance->$method($args [0], $args [1], $args [2], $args [3]);
            
            default :
                return call_user_func_array(array (
                        $instance,
                        $method 
                ), $args);
        }
    }
}