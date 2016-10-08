<?php

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/
require __DIR__.'/../vendor/autoload.php';


/*
|--------------------------------------------------------------------------
| Register Skinny Auto Loader
|--------------------------------------------------------------------------
|
*/
require __DIR__.'/../library/autoload.php';
\ClassLoader::register();


/*
|--------------------------------------------------------------------------
| 添加alias列表到ClassLoader
|--------------------------------------------------------------------------
|
| 添加alias列表到ClassLoader
|
*/
$aliases = require __DIR__.'/aliases.php';
\ClassLoader::addAliases($aliases);
