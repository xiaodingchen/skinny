<?php
/**
 * demo.php
 * 
 * 
 * */
class test_ctl_demo extends lib_lib_controller{
    
    public function test()
    {
        $objTable = kernel::single('lib_database_dbtable_table', app::get('test'));
        $objTable->update();
        echo 2222;
    }
    
    public function del()
    {
        $objModel = app::get('test')->model('user');
        $data['username'] = '测试二';
        $data['point'] = 110;
        $data['sex'] = 0;
        $data['wedlock'] = 2;
        $data['regtime'] = time();
        $objModel->insert($data);
        view::make('test/dd.html', ['item'=>'this is item']);
    }
    
    public function item($it_id)
    {
       
    }
}
