<?php
/**
 |--------------------------------------------------------------------------
 | 框架初始文件
 |--------------------------------------------------------------------------
 |
 | 定义目录、自动加载、全局类
 |
 */
require __DIR__ . '/paths.php';
require __DIR__ . '/autoload.php';
require LIB_DIR . '/kernel.php';

error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_COMPILE_ERROR);
kernel::startExceptionHandling();
$config = config::get('app');
$timezone = $config['timezone']?:8;
date_default_timezone_set('Etc/GMT'.($timezone>=0?($timezone*-1):'+'.($timezone*-1)));
