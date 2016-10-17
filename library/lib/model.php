<?php

/**
 * model.php
 *
 * 数据模型基类
 * */
use lib_static_app as App;
use \Doctrine\DBAL\Exception\UniqueConstraintViolationException as UniqueConstraintViolationException;
use \Doctrine\DBAL\Exception\NotNullConstraintViolationException as NotNullConstraintViolationException;


class lib_lib_model {

    /**
     * @var \app
     */
    public $app;
    
    private $table_define;
    
    private $__exists_schema = [];
    
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->tableDirName = config::get('database.app_dbtable_dir', 'dbtable');
        $this->schema = $this->getSchema();
        $this->idColumn = $this->schema['primary'];
        if(! is_array( $this->idColumn ) && array_key_exists( 'autoincrement',$this->schema['columns'][$this->idColumn]))
        {
            $this->idColumnAutoincrement = $this->schema['columns'][$this->idColumn]['autoincrement'];
        }
        
    }
    
    public function getSchema()
    {
        $table = $this->getTableName();
        $path = $this->app->app_dir . '/' . $this->tableDirName . '/' . $table . '.php';
        if(!isset($this->__exists_schema[$this->app->app_id][$table])){
            $this->__exists_schema[$this->app->app_id][$table] = $this->getTableDefine()->loadDefine($path);
        }
        
        return $this->__exists_schema[$this->app->app_id][$table];
    }
    
    public function getTableDefine()
    {
        if (!$this->table_define) $this->table_define = kernel::single('lib_database_dbtable_table', $this->app);
        
        return $this->table_define;
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
     * 
     * */
    
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
    
    /**
     * 获取多条数据
     * 
     * @param string $cols
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @param array|string $orderBy
     * @return array
     * */
    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderBy=null)
    {
        
        if ($filter == null) $filter = array();
        if (!is_array($filter)) throw new \InvalidArgumentException('filter param not support not array');
    
        $offset = (int)$offset<0 ? 0 : $offset;
        $limit = (int)$limit < 0 ? 100000 : $limit;
        $orderBy = $orderBy ? $orderBy : $this->defaultOrder;
    
        $qb = $this->database()->createQueryBuilder();
        $qb->select($cols)
        ->from($this->getTableName(1))
        ->setFirstResult($offset)
        ->setMaxResults($limit);
    
        $qb->where($this->_filter($filter));
        // orderby 同时支持array和string
        if ($orderBy)
        {
            $orderBy = is_array($orderBy) ? implode(' ', $orderBy) : $orderBy;
            array_map(function($o) use (&$qb){
                $permissionOrders = ['asc', 'desc', ''];
                @list($sort, $order) = explode(' ', trim($o));
                if (!in_array(strtolower($order), $permissionOrders)  ) throw new \InvalidArgumentException("getList order by do not support {$order} ");
                $qb->addOrderBy($qb->getConnection()->quoteIdentifier($sort), $order);
            }, explode(',', $orderBy));
        }
    
        $stmt = $qb->execute();
        $data = $stmt->fetchAll();
    
        $this->formatData($data, $cols);
    
        return $data;
    }
    
    /**
     * 处理输出数据
     *
     * @param array $filter
     * @param string $cols
     */
    public function formatData(&$rows, $cols='*')
    {
        if($rows)
        {
            // 目前不支持 字段别名
            $useColumnKeys = array_keys($rows[0]);
            $columnDefines = $this->_columns();
    
            foreach($useColumnKeys as $columnKey)
            {
                $columnType = $columnDefines[$columnKey]['type'];
    
                if ($func = kernel::single('lib_database_dbtable_table', $this->app)->getDefineFuncOutput($columnType))
                {
                    array_walk($rows, function(&$row, $func) use ($func, $columnKey){
                        $row[$columnKey] = call_user_func($func, $row[$columnKey]);
                    });
                }
            }
    
            return $rows;
    
        }
    }
    
    /**
     * 获取多条数据
     *
     * @param string $cols
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @param array|string $orderBy
     * @return array
     * */
    public function getRow($cols='*', $filter=array(), $orderType=null){
        $data = $this->getList($cols, $filter, 0, 1, $orderType);
        if($data){
            return $data['0'];
        }else{
            return $data;
        }
    }
    
    /**
     * 插入数据
     *
     * @var array $data
     @ @return integer|bool
     */
    public function insert(&$data)
    {
        $this->checkInsertData($data);
        $prepareUpdateData = $this->prepareInsertData($data);
        $qb = $this->database()->createQueryBuilder();
    
        $qb->insert($this->database()->quoteIdentifier($this->getTableName(1)));
    
        array_walk($prepareUpdateData, function($value, $key) use (&$qb) {
            $qb->setValue($key, $qb->createNamedParameter($value));
        });
    
            try {
                $stmt = $qb->execute();
            }
            // 主键重
            catch (UniqueConstraintViolationException $e)
            {
                logger::error($e);
                return false;
            }
    
            $insertId = $this->lastInsertId($data);
            if ($this->idColumnAutoincrement)
            {
                $data[$this->idColumn] = $insertId;
            }
    
            return isset($insertId) ? $insertId : true;
    }
    
    /**
     * delete
     *
     * @param mixed $filter
     * @access public
     * @return void
     */
    public function delete($filter)
    {
        $qb = $this->database()->createQueryBuilder();
        $qb->delete($this->database()->quoteIdentifier($this->getTableDefine(1)))
        ->where($this->_filter($filter));
    
        return $qb->execute() ? true : false;
    }
    
    /**
     * delete
     *
     * @param mixed $data
     * @param mixed $filter
     * @access public
     * @return void
     */
    public function update($data, $filter)
    {
        if (count((array)$data)==0) return true;
        $prepareUpdateData = $this->prepareUpdateData($data);
        $qb = $this->database()->createQueryBuilder();
        $qb->update($this->database()->quoteIdentifier($this->getTableName(1)))
        ->where($this->_filter($filter));
    
        array_walk($prepareUpdateData, function($value, $key) use (&$qb) {
            $qb->set($key, $qb->createNamedParameter($value));
        });
        $stmt = $qb->execute();
    
    
        return $stmt>0?$stmt:true;
    }
    
    /**
     * replace
     *
     * @param array $data
     * @param array $filter
     * @return mixed
     */
    public function replace($data,$filter)
    {
        // todo: 现在逻辑简单, 但是对于Exception的处理上会有问题
        if ($return = $this->insert($data)===false)
        {
            $return = $this->update($data, $filter);
        }
        return $return;
    }
    
    public function count($filter=null)
    {
        $total = $this->database()->createQueryBuilder()
        ->select('count(*) as _count')->from($this->getTableName(true))->where($this->_filter($filter))
        ->execute()->fetchColumn();
    
        return $total;
    }
    
    /**
     * 获取lastInsertId
     *
     * @param integer|null $data
     * @param integer|null
     */
    public function lastInsertId($data = null)
    {
        if ($this->idColumnAutoincrement)
        {
            $insertId = $this->database()->lastInsertId();
        }
        else
        {
            if (!is_array($this->idColumn))
            {
                $insertId = isset($data[$this->idColumn]) ? $data[$this->idColumn] : null;
            }
            else
            {
                $insertId = null;
            }
        }
        return $insertId;
    }
    
    /**
     * 检测inser条数据, 是否有必填数据没有处理t
     *
     * @param integer|null $data
     * @param integer|null
     */
    public function checkInsertData($data)
    {
        foreach($this->_columns() as $columnName => $columnDefine)
        {
            if(!isset($columnDefine['default']) && $columnDefine['required'] && $columnDefine['autoincrement']!=true)
            {
                // 如果当前没有值, 那么抛错
                if(!isset($data[$columnName]))
                {
                    throw new \InvalidArgumentException($columnName . 'Not null');
                }
            }
        }
    }
    
    
    private function prepareUpdateData($data)
    {
        return $this->prepareUpdateOrInsertData($data);
    }
    
    private function prepareInsertData($data)
    {
        return $this->prepareUpdateOrInsertData($data);
    }
    
    private function prepareUpdateOrInsertData($data)
    {
        $columnDefines = $this->_columns();
        $return = [];
        array_walk($columnDefines, function($columnDefine, $columnName) use (&$return, $data) {
    
            if ($func = $this->getTableDefine()->getDefineFuncInput($columnDefine['type']))
            {
                if ($funcResult = call_user_func($func, $data[$columnName]))
                {
                    $return[$this->database()->quoteIdentifier($columnName)] = $funcResult;
                }
                else return;
            }
            elseif ($columnDefine['required'] && ($data[$columnName] === '' || is_null($data[$columnName])))
            {
                return;
            }
            elseif (!isset($data[$columnName]))
            {
                return;
            }
            else
            {
                if(is_array($data[$columnName])) $data[$columnName] = serialize($data[$columnName]);
    
                $return[$this->database()->quoteIdentifier($columnName)] = $data[$columnName];
            }
        });
        return $return;
    }
    
    
    public function _columns()
    {
        
        return $this->schema['columns'];
    }
    
    /**
     * filter
     *
     * 因为parent为反向关联表. 因此通过 _getPkey(), 反向获取关系. 并删除
     *
     * @param array $filter
     * @param misc $subSdf
     */
    public function _filter($filter = array()){
        if ($filter == null) $filter = array();
    
        $filterObj = kernel::single('lib_database_filter');
        $filterResult = $dbeav_filter->filterParser($filter,$this);
        return $filterResult;
    }
}
