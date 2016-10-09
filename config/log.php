<?php
/**
 * log.php
 */

return array(
    /*
    |--------------------------------------------------------------------------
    | 记录等级
    |--------------------------------------------------------------------------
    |
    | 可配置emergency/alert/critical/error/warning/notice/info/debug
    |
    */
    'record_level' => 'debug',
    
    /*
    |--------------------------------------------------------------------------
    | 默认驱动
    |
    |--------------------------------------------------------------------------
    |
    | 可配置file/syslog
    |
    */
    'default' => 'file',

	/*
	|--------------------------------------------------------------------------
	| Logging Configuration
	|--------------------------------------------------------------------------
	|
	| Here you may configure the log settings for your application. Out of
	| the box, Laravel uses the Monolog PHP logging library. This gives
	| you a variety of powerful log handlers / formatters to utilize.
	|
	| Available Settings: "single", "daily", "syslog", "errorlog"
	|
	*/
    'log' => 'daily',
);
