<?php
define ( 'ROOT_DIR', realpath ( __DIR__ . '/../' ) );

// 您可以更改这个目录的位置来获得更高的安全性
define ( 'DATA_DIR', ROOT_DIR . '/data' );
define ( 'CACHE_DIR', DATA_DIR . '/cache' );

define ( 'CACHE_ROUTE_DIR', ROOT_DIR . '/data' );
// define('THEME_DIR', ROOT_DIR.'/themes');
define ( 'PUBLIC_DIR', ROOT_DIR . '/public' ); // 同一主机共享文件
define ( 'THEME_DIR', PUBLIC_DIR . '/themes' );
define ( 'WAP_THEME_DIR', ROOT_DIR . '/wapthemes' );

define ( 'MEDIA_DIR', PUBLIC_DIR . '/images' );
define ( 'SCRIPT_DIR', ROOT_DIR . '/script' );
define ( 'APP_DIR', ROOT_DIR . '/app' );
define ( 'CONFIG_DIR', ROOT_DIR . '/config' );
define ( 'BOOT_DIR', ROOT_DIR . '/bootstrap' );
define ( 'LIB_DIR', ROOT_DIR . '/library' );
define ( 'TMP_DIR', sys_get_temp_dir () );
