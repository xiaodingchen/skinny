<?php
/**
 * update.php
 *
 * */
use Symfony\Component\Console\Application as ConsoleApplication;

class lib_command_update implements lib_command_interface{
    
    public function commandTitle()
    {
        return '更新应用';
    }
    
    public function commandExplain()
    {
        return '';
    }
    
    public function handle(array $args = [])
    {
        $appList = app::getAppList();
        if(! $appList)
        {
            throw new RuntimeException ('No application found');
        }
        
        foreach ($appList as $appName)
        {
            $app = app::get($appName);
            
            $table = kernel::single('lib_database_dbtable_table', $app);
            $table->update();
        }
        
        logger::info('Applications database is up-to-date, ok.');
    }
    
    
}
