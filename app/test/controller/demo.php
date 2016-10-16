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
        view::make('test/dd.html', ['item'=>'this is item']);
    }
    
    public function item($it_id)
    {
        $url = url::action('test_ctl_demo@item',['itm_id'=>34,'item-id'=>21]);
        $this->display('test/item.html', ['id'=>$it_id, 'url'=>$url]);
    }
}
