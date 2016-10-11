<?php

/**
 * controller.php
 * 
 * */

class lib_lib_controller {

    public function __construct(app $app)
    {
        $this->app = $app;
    }
    /**
     * 显示模板
     * 
     * @param string $tpl
     * @param array $data
     * @return void
     * */
    public function display($tpl, array $data = [])
    {
        return view::make($tpl, $data);
    }
    
    /**
     * 返回模板数据
     *
     * @param string $tpl
     * @param array $data
     * @return string
     * */
    public function fetch($tpl, array $data = [])
    {
        return view::make($tpl, $data, true);
    }
    
    /**
     * 获取表单数据
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     * */
    public function input($key = null, $default = null)
    {
        return request::input($key, $default);
    }
    
    /**
     * 判断是否是ajax请求
     * */
    public function isAjax()
    {
        return request::ajax();
    }
    
    /**
     * 返回json数据
     * 
     * @param array $data
     * @return void
     * */
    public function returnJson($data = [])
    {
        return response::json($data);
    }
}
