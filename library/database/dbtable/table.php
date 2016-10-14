<?php

/**
 * table.php
 * 
 * 处理app下的数据表
 * */
use lib_static_app as App;
use Doctrine\DBAL\Schema\Schema;

class lib_database_dbtable_table {
    
    protected $app;
    protected $tableDirName;
    public function __construct(App $app = null)
    {
        $this->app = $app;
        $this->tableDirName = config::get('database.app_dbtable_dir', 'dbschema');
    }
    
    /**
     * 根据实际定义的dbschema生成实际创建表的dbal schema
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function createTableSchema()
    {
        $appId = $this->app->app_id;
        $schema = new Schema();
    }
    
    public function setApp(App $app)
    {
        $this->app = $app;
    }
}

