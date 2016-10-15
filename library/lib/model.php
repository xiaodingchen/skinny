<?php

/**
 * model.php
 *
 * 数据模型基类
 * */
class lib_lib_model {

    /**
     * @var \app
     */
    public $app;
    
    private $table_define;
    
    private $__exists_schema = [];
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    /**
     * 返回一个数据库对象
     * 
     * */
    public function database()
    {
        return $this->app->database();
    }
    
    /**
     * 获取当前数据表名
     * 
     * @param bool $real
     * @return string
     * */
    public function getTableName($real = false)
    {
        $className = get_class($this);
        $tableName = substr($className,5+strpos($className,'_mdl_'));
        
        if($real)
        {
            $tableName = $this->app->app_id.'_'.$tableName;
            // 获取指定表前缀
            $prefixs = config::get('database.app_table_prefix', []);
            if($prefixs && array_key_exists($this->app->app_id, $prefixs))
            {
                $prefix = $prefixs[$this->app->app_id];
                $tableName = $prefix . '_' . $tableName;
            }
            
            return $tableName;
            
        }
        
        return $tableName;
    }
}
