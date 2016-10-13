<?php
/**
 * logger.php
 * 记录sql执行记录
 * 
 * */
use Doctrine\DBAL\Logging\SQLLogger;

class lib_database_logger implements SQLLogger{
    
    private static $__mysql_query_excutions = 0;
    public function startQuery($sql, array $params = null, array $types = null)
    {
        logger::debug(sprintf('sql:%d %s', ++static::$__mysql_query_excutions, $sql), ['params'=>$params, 'type'=>$types]);
    } 
    
    public function stopQuery()
    {
        return true;
    }
}
