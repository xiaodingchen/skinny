<?php
/**
 * demo.php
 * 
 * 
 * */
class test_ctl_demo{
    
    public function test()
    {
        throw new Exception('哈哈哈');
        echo 11;
    }
    
    public function del()
    {
        view::make('test/dd.html', ['item'=>'this is item']);
    }
}
