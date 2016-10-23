<?php
/**
 * demo.php
 * 
 * 
 * */
class test_ctl_demo extends lib_lib_controller{
    
    public function test()
    {
        
        echo 2222;
    }
    
    public function del()
    {
        view::make('test/dd.html', ['item'=>'this is item']);
    }
    
    public function item($it_id)
    {
       
    }
}
