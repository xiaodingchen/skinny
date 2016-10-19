<?php

/**
 * memcached.php
 *
 * */
use Memcached;
use RuntimeException;

class lib_cache_driver_memcached implements lib_cache_interface {

    protected $memcached;
    protected $prefix;
    public function __construct($servers, $prefix = '')
    {
        $this->memcached = $this->connect($servers);
        $this->setPrefix($prefix);
        
    }

    public function get($key)
    {
        $value = $this->memcached->get($this->prefix.$key);
        
        if ($this->memcached->getResultCode() == 0) {
            return $value;
        }
        
        return false;
    }

    public function set($key, $value = '', $seconds = 0)
    {
        return $this->memcached->set($this->prefix.$key, $value, $seconds);
    }

    public function delete($key)
    {
        return $this->memcached->delete($this->prefix.$key);
    }

    public function clear()
    {
        return $this->memcached->flush();
    }
    
    /**
     * Create a new Memcached connection.
     *
     * @param  array  $servers
     * @return \Memcached
     *
     * @throws \RuntimeException
     */
    public function connect(array $servers)
    {
        $memcached = $this->getMemcached();
    
        $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
    
        // For each server in the array, we'll just extract the configuration and add
        // the server to the Memcached connection. Once we have added all of these
        // servers we'll verify the connection is successful and return it back.
        foreach ($servers as $server) {
            $memcached->addServer(
                $server['host'], $server['port'], $server['weight']
            );
        }
    
        $memcachedStatus = $memcached->getVersion();
    
        if (! is_array($memcachedStatus)) {
            throw new RuntimeException('No Memcached servers added.');
        }
    
        if (in_array('255.255.255', $memcachedStatus) && count(array_unique($memcachedStatus)) === 1) {
            throw new RuntimeException('Could not establish Memcached connection.');
        }
    
        return $memcached;
    }
    
    /**
     * Get a new Memcached instance.
     *
     * @return \Memcached
     */
    protected function getMemcached()
    {
        return new Memcached;
    }
    
    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    /**
     * Set the cache key prefix.
     *
     * @param  string  $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = ! empty($prefix) ? $prefix.':' : '';
    }
}
