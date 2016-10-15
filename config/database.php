<?php
/**
 * database.php
 *
 * */
return [
    'default' => 'default',
    'connections' => [
        'default' => [
            'driver'    => 'mysqli',
            'host'      => '127.0.0.1',
            'dbname'  => '',
            'user'  => '',
            'password'  => '',
            'charset'   => 'utf8',
         ]
    ],
    
    // 指定应用的表前缀,切记不要前缀名不要重复,默认表前缀为app_name
    'app_table_prefix' => [
        // 'app_name' => 'table_prefix',
    ],
    
    'app_dbtable_dir' => 'dbtable',
    'type_define' => [],

    
];
