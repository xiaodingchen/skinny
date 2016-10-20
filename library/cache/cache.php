<?php
/**
 * cache.php
 * 
 * */
use lib_support_arr as Arr;
use lib_cache_driver_null as NullStore;
use lib_cache_driver_memcached as MemcachedStore;
use lib_cache_driver_apc as ApcStore;
use lib_cache_driver_file as FileStore;

class lib_cache_cache{
    
    public function __construct()
    {
        
    }
    
    /**
     * Get a cache store instance by name.
     *
     * @param  string|null  $name
     * @return lib_cache_interface
     */
    public function store($name = null)
    {
        if(! $name)
        {
            $name = config::get('cache.default');
        }
        
        if(! $name)
        {
            return $this->createNullDriver();
        }
        
        $config = config::get('cache.drivers' . $name, []);
        
        if(! $config && !in_array($name, 'apc', 'file'))
        {
            throw new RuntimeException($name.' cache store configure not found.');
        }
        
        $driver = ucfirst($name);
        $method = "create{$driver}Driver";
        
        return call_user_func_array([$this, $method], [$config]);
    }
    
    /**
     * Get the cache prefix.
     *
     * @param  array  $config
     * @return string
     */
    protected function getPrefix(array $config)
    {
        return Arr::get($config, 'prefix') ?: config::get('cache.prefix');
    }
    
    /**
     * Create an instance of the APC cache driver.
     *
     * @param  array  $config
     * @return base_cache_repository
     */
    
    protected function createFileDriver(array $config)
    {
        $prefix = $this->getPrefix($config);
    
        return new FileStore($prefix);
    } 
    
    /**
     * Create an instance of the Null cache driver.
     *
     * @return base_cache_repository
     */
    protected function createNullDriver()
    {
        return new NullStore;
    }
    
    /**
     * Create an instance of the Memcached cache driver.
     *
     * @param  array  $config
     * @return base_cache_repository
     */
    protected function createMemcachedDriver(array $config)
    {
        $prefix = $this->getPrefix($config);
    
        $memcached = new MemcachedStore($config['servers'], $prefix);
        return $memcached;
    }
    
    /**
     * Create an instance of the APC cache driver.
     *
     * @param  array  $config
     * @return base_cache_repository
     */
    protected function createApcDriver(array $config)
    {
        $prefix = $this->getPrefix($config);
    
        return new ApcStore($prefix);
    }
    
    public function __call($method, $args)
    {
        $obj = $this->store();
        
        return call_user_func_array([$obj, $method], $args);
    }
    
    
}

