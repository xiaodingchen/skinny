<?php
/**
 * routes.php
 * 
 * late-xiao@foxmail.com
 * */
route::get('/', [ 'as' => 'topc', 'uses' => 'test_ctl_demo@test']);
route::get('del.html', [ 'as' => 'er', 'uses' => 'test_ctl_demo@del']);
