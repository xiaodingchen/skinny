<?php
/**
 * file.php
 * 
 * */

class lib_cache_driver_file implements lib_cache_interface{
    
    protected $config = [];
    
    public function __construct()
    {
        
    }
    
    public function getConfig()
    {
        if(! $this->config)
        {
            $this->config = config::get('cache.drivers.file', []);
        }
        
        return $this->config;
    }
    
    public function setConfig(array $configs)
    {
        $this->config = $configs;
    }
    
    public function get($key, $default = null)
    {
        
    }
    
    public function set($key, $value='')
    {
        
    }
    
    public function delete($key)
    {
        
    }
    
    public function clear()
    {
        
    }
}

