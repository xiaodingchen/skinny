<?php

/**
 * model.php
 *
 * */
class lib_static_model {

    /**
     * 实例化一个model对象
     * 
     * @param string $appId
     * @param string $tableName
     * 
     * @return lib_lib_model
     * */
    public static function create($appId, $tableName)
    {
        if (! is_string($appId) || ! is_string($tableName))
        {throw new RuntimeException('The parameter type must be a string type');}
        
        if (! $appId || ! $tableName)
        {throw new RuntimeException('Application name must be specified!');}
        
        if (! $tableName)
        {throw new RuntimeException('Data table definition file name must be specified');}
        
        return app::get($appId)->model($tableName);
    }
}
