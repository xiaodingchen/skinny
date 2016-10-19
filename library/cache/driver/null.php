<?php

/**
 * null.php
 *
 * */
class lib_cache_driver_null implements lib_cache_interface {

    public function get($key)
    {

    }

    public function set($key, $value, $minutes)
    {

    }

    public function delete($key)
    {

    }

    public function clear()
    {

    }
}
