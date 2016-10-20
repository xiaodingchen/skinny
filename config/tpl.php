<?php 
	
	return [
		'app_tpl_dir' => 'view',
		'compile_dir' => DATA_DIR . '/' . 'tpl_c',
		'left_delimiter' => '<{',
		'right_delimiter' => '}>',
		'caching' => false,
		'cache_dir' => CACHE_DIR . '/' . 'tpl_cache',
		'cache_lifetime' => 0,
        
		// 每个app下都可以定义smarty插件
		'tpl_plugins_apps' => [],
		// 插件目录位置为：appName/service/plugins/smarty, 你可以自定义app_tpl_plugins_dir这个选项
		'app_tpl_plugins_dir' => 'plugins/smarty',

	];
