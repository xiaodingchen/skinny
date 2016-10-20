<?php
/**
 * cache.php
 * 
 * */

return [
    
    'drivers' => [
        'file'=>[
            'prefix' => 'skinny_',
        ],
        
        'memecached'=>[
            'servers'=>[
                [
                    'host' => '127.0.0.1',
                    'port' => '11211',
                    'weight' => 1
                ]
            ],
        ]
    ],
    'default'=>'file',
    'prefix' => 'skinny_',
];

