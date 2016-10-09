<?php
/**
 |--------------------------------------------------------------------------
 | 定义paths
 |--------------------------------------------------------------------------
 |
 | 常用的目录定义, 都在此处定义
 |
 */
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_COMPILE_ERROR);
date_default_timezone_set('PRC');
require __DIR__ . '/paths.php';
require __DIR__ . '/autoload.php';
require LIB_DIR . '/kernel.php';
kernel::startExceptionHandling();
