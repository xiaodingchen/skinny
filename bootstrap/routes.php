<?php
/**
 * routes.php
 * 
 * 
 * */
route::get('/', [ 'as' => 'index', 'uses' => 'index_ctl_index@index']);
route::get('test/{id}.html', [ 'as' => 'index.test', 'uses' => 'index_ctl_index@test']);
route::get('get.html', [ 'as' => 'index.get', 'uses' => 'index_ctl_index@get']);
route::get('view.html', [ 'as' => 'index.view', 'uses' => 'index_ctl_index@view']);