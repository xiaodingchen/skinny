<?php
/**
 * list.php
 * 
 * */

class lib_command_set_list implements lib_command_interface{
    
    public function commandTitle()
    {
        return '列出所有命令';
    }
    
    public function commandExplain()
    {
        return '';
    }
    
    public function handle(array $args = [])
    {
        $commandObj = new lib_command_command();
        $commands = config::get('command', []);
        $commands = array_merge($commandObj->getDefaultDefineCommand(), $commands);
        
        foreach ($commands as $key => $val)
        {
            $obj = new $val;
            consoleColor::outputText(str_repeat(' ', 2).str_pad($key, 30).$obj->commandTitle(), 'success');
            
        }
        
        return true;
    }
}
