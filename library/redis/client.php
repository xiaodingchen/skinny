<?php

/**
 * client.php
 *
 * */
use Closure;
use Predis\Client;

class lib_redis_client {
    
    /**
     * The scene clients instance.
     *
     * @var array
     */
    protected $sceneClients;
    
    /**
     * The clients instance.
     *
     * @var array
     */
    protected $clients;
    
    static private $scriptMaps = null;
    
    static private $useCount = 0;
    
    protected $client;
    

    public function scene($name = null)
    {
        if (isset($this->sceneClients[$name])) return $this->sceneClients[$name];
    
        if (is_null($connName = config::get('redis.scenes.'.$name.'.connection', null)))
        {
            $connName = 'default';
        }
        
        if(! $name)
        {
            $name = $connName;
        }
    
        $this->sceneClients[$name] = new $this->sceneClient($name, $this->connection($connName));
    
        return $this->sceneClients[$name];
    }
    
    public function sceneClient($name, $client)
    {
        $this->sceneName = $name;
    
        $this->client = $client;
    }
    
    /**
     * Get a specific Redis connection instance.
     *
     * @param  string  $name
     * @return \Predis\ClientInterface|null
     */
    protected function connection($name, $options = [])
    {
        if (isset($this->clients[$name])) return $this->clients[$name];
    
        if (is_null($connectionConfig = config::get('redis.connections.'.$name)))
        {
            throw new InvalidArgumentException("Redis connection [$name] is not defined");
        }
    
        $servers = (array) array_get($connectionConfig, 'servers');
    
        $configOptions = (array) array_get($connectionConfig, 'options');
    
        $options = $options + $configOptions;
    
        // 如果存在多个server配置, 默认为集群
        if (isset($servers[1]))
        {
            return $this->clients[$name] = new Client($servers, $options);
        }
        else
        {
            return $this->clients[$name] = new Client(array_pop($servers), $options);
        }
    }
    
    public function __call($commandID, $arguments)
    {
    
        static::$useCount++;
        
        $arguments[0] = $this->sceneName.':'.$arguments[0];
        logger::debug(sprintf('REDIS:%d %s, arguments: %s', static::$useCount, $commandID, var_export($arguments, 1)));
    
        return $this->client->__call($commandID, $arguments);
    }
    
    public function loadScripts($names)
    {
        $scriptMaps = static::$scriptMaps ? : (static::$scriptMaps = config::get('redis.scripts'));
        foreach((array)$names as $name)
        {
            if ($class = $scriptMaps[$name])
            {
                $this->client->getProfile()->defineCommand($name, $class);
            }
        }
    }
    
    public function getClient()
    {
        return $this->client;
    }
    
    public function subscribe($channels, Closure $callback, $scene, $method = 'subscribe')
    {
        $loop = $this->scene($scene)->pubSubLoop();
    
        call_user_func_array([$loop, $method], (array) $channels);
    
        foreach ($loop as $message) {
            if ($message->kind === 'message' || $message->kind === 'pmessage') {
                call_user_func($callback, $message->payload, $message->channel);
            }
        }
    
        unset($loop);
    }
    
    /**
     * Subscribe to a set of given channels with wildcards.
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @param  string  $scene
     * @return void
     */
    public function psubscribe($channels, Closure $callback, $scene = null)
    {
        return $this->subscribe($channels, $callback, $scene, __FUNCTION__);
    }
    
    public function flushAllResources()
    {
        $connections = config::get('redis.connections');
        foreach (array_keys($connections) as $connName)
        {
            $this->connection($connName)->flushdb();
        }
    }
}
