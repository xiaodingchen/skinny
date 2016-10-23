<?php
/**
 * index.php
 * 
 * 
 * */
class index_ctl_index extends lib_lib_controller{
    
    public function index()
    {
        
        echo '<h1>welcome</h1>';
    }
    
    public function test($id)
    {
        echo $id;
    }

    public function get()
    {
        $all = request::input();
        $this->returnJson($all);
    }

    public function view()
    {
        return $this->display('index/view.html',['view'=>'這是模板']);
    }
}
