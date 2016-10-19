<?php
/**
 * cache.php
 * 
 * */
use lib_support_arr as Arr;
use lib_cache_driver_null as NullStore;
use lib_cache_driver_memcached as MemcachedStore;
use lib_cache_driver_apc as ApcStore;

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
        
        $config = config::get('cache.' . $name, []);
        
        if(! $config && $name != 'apc')
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
    
    /* protected function createSecacheDriver(array $config)
    {
        $prefix = $this->getPrefix($config);
    
        $size = Arr::get($config, 'size', '1g');
    
        $file = Arr::get($config, 'file', 'secache');
    
        return $this->repository(new SecacheStore(new base_cache_store_secacheEngine(CACHE_DIR, $file, $size), $prefix));
    } */
    
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
    
    
}

