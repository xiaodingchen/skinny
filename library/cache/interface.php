<?php
/**
 * interface.php
 *
 * */
interface lib_cache_interface{
    public function getConfig();
    public function setConfig(array $configs);
    public function get($key, $default = null);
    public function set($key, $value = '');
    public function delete($key);
    public function clear();
}
