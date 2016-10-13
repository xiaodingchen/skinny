<?php
/**
 * db.php
 *
 * */
use Doctrine\DBAL\Configuration;

class lib_facades_db extends lib_facades_facade{
    
    private static $__db;
    
    protected static function getFacadeAccessor()
    {
        if(! static::$__db)
        {
            $configuration = new Configuration();
            $logger = new lib_database_logger();
            $configuration->setSQLLogger($logger);
            
            static::$__db = new lib_database_manager($configuration);
        }
        
        return static::$__db;
    }
}
