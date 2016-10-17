<?php

/**
 * table.php
 * 
 * 处理app下的数据表
 * */
use lib_static_app as App;

class lib_database_dbtable_table {
    
    protected static $_define = [];
    protected $app;
    protected $tableDirName;
    
    // 自定义数据类型
    protected $typeDefines;
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->tableDirName = config::get('database.app_dbtable_dir', 'dbtable');
        $this->typeDefines = config::get('database.type_define', []);
    }
    
    
    /**
     * 
     * 更新数据表
     * */
    public function update()
    {
        if(! $this->iterator())
        {
            return false;
        }
        foreach ($this->iterator() as $fileInfo)
        {
           $fileName = $fileInfo->getFilename();
           if($this->filter($fileName))
           {
               $this->key = substr($fileName,0,-4);
               $this->updateTable();
           }
           else
           {
               continue;
           }
        }
    }
    
    public function updateTable()
    {
        $appId = $this->app->app_id;
        $db = $this->app->database();
        $realTableName = $this->realTableName();
        $toSchema = $this->createTableSchema();
        // 如果存在原始表, 则通过原始表建立schema对象
        if ($db->getSchemaManager()->tablesExist($realTableName))
        {
            $fromSchema = new \Doctrine\DBAL\Schema\Schema([$db->getSchemaManager()->listTableDetails($realTableName)], [], $db->getSchemaManager()->createSchemaConfig());
        }
        
        // 否则建立空schema
        else
        {
            $fromSchema = new \Doctrine\DBAL\Schema\Schema();
        }
        
        // 安全模式, 删除drop columns的相关语句
        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $changeTable = current($schemaDiff->changedTables);
        $changeTable->removedColumns = [];
        $queries = $schemaDiff->toSaveSql($db->getDatabasePlatform());
        
        foreach($queries as $sql)
        {
            logger::info($sql);
            $db->exec($sql);
        }
        
    }
    
    /**
     * 根据实际定义的dbschema生成实际创建表的dbal schema
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function createTableSchema()
    {
        $db = $this->app->database();
        $schema = new \Doctrine\DBAL\Schema\Schema();
        $table = $schema->createTable($this->realTableName());

        $define = $this->realLoad();
        // 建立字段
        foreach($define['columns'] as $columnName => $columnDefine)
        {
            list($type, $options) = $columnDefine['doctrineType'];
            $table->addColumn($columnName, $type, $options);
        }

        // 建立主键
        if ($define['primary']) $table->setPrimaryKey($define['primary']);
        
        // 建立索引
        if ($define['index'])
        {
            foreach((array)$define['index'] as $indexName => $indexDefine)
            {
                if (strtolower($indexDefine['prefix'])=='unique')
                {
                    $table->addUniqueIndex($indexDefine['columns'], $indexName);
                }
                else
                {
                    $table->addIndex($indexDefine['columns'], $indexName);
                }
            }
        }
        
        return $schema;
    }
    
    /**
     * 返回真是的表名
     * */
    public function realTableName()
    {
        $tableName = $this->app->app_id.'_'.$this->key();
        // 获取指定表前缀
        $prefixs = config::get('database.app_table_prefix', []);
        if($prefixs && array_key_exists($this->app->app_id, $prefixs))
        {
            $prefix = $prefixs[$this->app->app_id];
            $tableName = $prefix . '_' . $this->key();
        }
        
        return $tableName;
    }
    
    /**
     * 读取表定义文件
     * 
     * 
     * */
    public function realLoad()
    {
        $realTableName = $this->realTableName();
        if(!static::$_define[$realTableName])
        {
            $path = $this->app->app_dir . '/' . $this->tableDirName . '/' . $this->key . '.php';
        
            $define = $this->loadDefine($path);
        
            static::$_define[$realTableName] = $define;
        }
        
        return static::$_define[$realTableName];
        
    }
    
    
    
    /**
     * 读取表定义文件
     * 
     * @param string $path
     * */
    public function loadDefine($path)
    {
        $define = require($path);
        
        foreach($define['columns'] as $k=>$v)
        {
            $define['columns'][$k]['doctrineType'] = $this->createDoctrineType($v);
        }
    
        if (isset($define['primary']))
        {
            $define['primary'] = (array)$define['primary'];
        }
    
        return $define;
    }
    
    public function iterator()
    {
        $tmpDir = '';
        if(is_dir($this->app->app_dir . '/' . $this->tableDirName))
        {
            $tmpDir = $this->app->app_dir . '/' . $this->tableDirName;
            $coreDir = new DirectoryIterator($tmpDir);
            
            return $coreDir;
        }
        
        return false;
    }
    

    public function filter($fileName){
        return substr($fileName,-4,4)=='.php' && is_dir($this->getPathname());
    }
    
    public function getPathname(){
        return $this->iterator()->getPathname();
    }
    
    public function key()
    {
        return $this->key;
    }
    
    
    
    /**
     * 处理DoctrineType
     * 
     * @param array $columnDefine
     * @return array
     * */
    public function createDoctrineType($columnDefine)
    {
        $options = [];
        $options['notnull'] = ($columnDefine['required']) ? true : false;
        $convertKeys = ['autoincrement', 'comment', 'default', 'fixed', 'precision', 'scale', 'length', 'unsigned'];
        array_walk($convertKeys, function($key) use ($columnDefine, &$options) {
            if (isset($columnDefine[$key])) $options[$key] = $columnDefine[$key];
        });
    
            $type = $columnDefine['type'];
            switch (true)
            {
                case is_array($primType =$type):
                    $type = 'string';
                    $options['length'] = array_reduce(array_keys($primType), function($max, $item) {
                        $itemLenth = strlen($item);
                        return $itemLenth > $max ? $itemLenth : $max;
                    });
                    break;
                case $this->isExistDefine($type):
                    @list($type, $initOptions) = $this->getDefineDoctrineType($type);
                    $initOptions = is_array($initOptions) ? $initOptions : [];
                    $options = array_merge($options, array_intersect_key($initOptions, array_flip(['precision', 'scale', 'fixed', 'length', 'unsigned'])));
                    break;
            }
    
            return [$type, $options];
    
    }
    
    // 自定义数据类型
    public function getDefineDoctrineType($type)
    {
        if (!$type) return null;
        return $this->typeDefines[$type]['doctrineType'];
    }
    
    public function isExistDefine($type)
    {
        return $this->typeDefines[$type] ? true : false;
    }
    
    public function getDefineFuncInput($type)
    {
        if (!$type) return null;
        return $this->typeDefines[$type]['func_input'];
    }
    
    public function getDefineFuncOutput($type)
    {
        if (!$type) return null;
        return $this->typeDefines[$type]['func_output'];
    }
    
    public function getDefineSql($type)
    {
        if (!$type) return null;
        return $this->typeDefines[$type]['sql'];
    }
    
   
}

