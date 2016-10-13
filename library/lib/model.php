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
    
    public function database()
    {
        return $this->app->database();
    }
    
    public function getTableName($real = false)
    {
        $className = get_class($this);
        $tableName = substr($className,5+strpos($className,'_mdl_'));
        
        if($real)
        {
            $tableName = $this->app->app_id.'_'.$tableName;
            // 获取指定表前缀
            $prefixs = config::get('database.app_table_prefix', []);
            if($prefixs)
            {
                $prefix = $prefixs[$this->app->app_id];
                $tableName = $prefix . '_' . $tableName;
            }
            
            return $tableName;
            
        }
        
        return $tableName;
    }
}
