<?php
/**
 * manager.php
 *
 * 数据库处理类
 * 
 * */
use Doctrine\DBAL\Configuration;

use Doctrine\DBAL\DriverManager;

class lib_database_manager{
    
    protected $configuration;
    
    protected $connections = [];
    
    public function __construct(Configuration $config = null)
    {
        if(!$config)
        {
            $config = new Configuration();
        }
        
        $this->configuration = $config;
    }
    
    public function getConfiguration()
    {
        return $this->configuration;
    }
    
    /**
     * Get a database connection instance.
     *
     * @param  string  $name
     * @return Connection
     */
    public function connection($name = null)
    {
        list($name, $type) = $this->parseConnectionName($name);
        if(! $this->isExistConfig($name))
        {
            $name = $this->getDefaultConnection();
        }
        
        if(! isset($this->connections[$name]))
        {
            $connection = $this->makeConnection($name);
            $this->connections[$name] = $connection;
        }
        
        // 当指定了type, 同时使用master slave模式的情况下, 设置master或者slave
        if ( ! is_null($type) && $this->connections[$name] instanceof Doctrine\DBAL\Connections\MasterSlaveConnection)
        {
            $this->connections[$name]->connect($type);
        }
        
        return $this->connections[$name];
    }
    
    /**
     * Make the database connection instance.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Connection
     */
    
    protected function makeConnection($name)
    {
        $config = $this->getConfig($name);
        $connectionParams = $config;
        if (isset($config['slave']) || isset($config['master']))
        {
            $connectionParams['wrapperClass'] = 'Doctrine\DBAL\Connections\MasterSlaveConnection';
        }
        
        $conn = DriverManager::getConnection($connectionParams, $this->configuration);
        
        return $conn;
    }
    
    /**
     * Get the database Config
     *
     * @param  string  $name
     * @return void
     */
    public function getConfig($name = null)
    {
        $defaultName = $this->getDefaultConnection();
    
        $name = $name ?: $defaultName;
    
        $connections = config::get('database.connections');
    
        // 如果指定的配置为空, 则使用默认的数据库设置
        if (is_null($config = array_get($connections, $name)))
        {
            if (is_null($config = array_get($connections, $defaultName)))
            {
                throw new \InvalidArgumentException("Database [$defaultName] not configured.");
            }
        }
    
        return $config;
    }
    
    
    /**
     * 判断指定的数据库配置名字，是否进行了配置
     * 
     * @param string $name
     * @return bool
     * */
    public function isExistConfig($name = null)
    {
        if ($name === null) return false;
    
        return !is_null(array_get(config::get('database.connections'), $name, null));
    }
    
    /**
     * Parse the connection into an array of the name and read / write type.
     *
     * @param  string  $name
     * @return array
     */
    protected function parseConnectionName($name)
    {
        $name = $name ?: $this->getDefaultConnection();
        
        // 主从判断
        return ends_with($name, ['::master', '::slave'])
        ? explode('::', $name, 2) : [$name, null];
    }
    
    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return config::get('database.default');
    }
    
    /**
	 * Set the default connection name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultConnection($name)
    {
        config::set('database.default', $name);
    }
    
    public function clearConnections()
    {
        foreach($this->connections as $connection) {
            $connection->close();
        }
    
        unset($this->connections);
    }
    
}
