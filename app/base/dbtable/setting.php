<?php
 
return array (
  'columns' => 
  array (
    'app' => array(
        //'type'=>'varchar(50)',
        'type' => 'string',
        'length' => 50,
        //'pkey' => true,
        'comment' => 'app名',
    ),
    'key' => array(
        'type' => 'string',
        'comment' => 'setting键值',
        
    ),
    'value' => array(
        'type' => 'text',
        'comment' => 'setting存储值',
    ),
  ),
  'primary' => ['app', 'key'],
  'comment' => 'setting存储',
);
