<?php

/**
 * request.php
 * 
 * */
class lib_facades_request extends lib_facades_facade {

    private static $__request;

    protected static function getFacadeAccessor()
    {
        if (! static::$__request)
        {
            // 没有容器的临时策略
            if (! kernel::runningInConsole())
            {
                static::$__request = lib_http_request::createFromGlobals();
            }
            else
            {
                $i = 'index.php';
                $url = trim(config::get('app.url', 'http://localhost'), '/');
                $url = strpos($url, $i) === false ? $url . '/' : $url;
                $parsed_url = parse_url($url);
                
                $path = $parsed_url['path'];
                
                $_SERVER['SCRIPT_FILENAME'] = PUBLIC_DIR . '/' . $i;
                
                $_SERVER['SCRIPT_NAME'] = (str_contains($path, $i) ? str_replace('/' . $i, '', $path) : $path) . '/' . $i;
                
                static::$__request = lib_http_request::create($url, 'GET', [], [], [], $_SERVER);
            }
        }
        return static::$__request;
    }
}
