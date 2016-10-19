<?php

/**
 * interface.php
 *
 * */
interface lib_cache_interface {

    public function get($key);

    public function set($key, $value, $seconds);

    public function delete($key);

    public function clear();
}
