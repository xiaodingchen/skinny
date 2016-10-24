<?php

/**
 * help.php
 * 
 * */

class lib_command_set_help implements lib_command_interface {

    /**
     * 指令执行
     *
     * @param array $args
     * @return void
     *
     */
    public function handle(array $args=[])
    {
        $command = $args[0];
        if(! $command)
        {
            consoleColor::outputText('command list','warning');
            $command = 'list';
        }
        $commandClassName = $this->_checkCommand($command);
        $commandObj = new $commandClassName();
        if($commandObj instanceof lib_command_interface)
        {
            if($command == 'list' && !$args[0])
            {
                $commandObj->handle($args);
                return true;
            }
            $title = $commandObj->commandTitle();
            $desc = $commandObj->commandExplain();
            consoleColor::outputText(str_repeat(' ', 2).str_pad('command title', 30).$title, 'success');
            consoleColor::outputText(str_repeat(' ', 2).str_pad('command title', 30).$desc, 'success');
        }
        else
        {
            consoleColor::outputText("Error: {$commandClassName}  must implement the lib_command_interface interface.", 'error');
            exit;
        }
    }

    /**
     * 指令使用说明
     *
     * @return string
     *
     */
    public function commandExplain()
    {
        $str = '';
        
        return $str;
    }

    /**
     * 指令简短描述
     *
     * @return string
     *
     */
    
    public function commandTitle()
    {
        return '命令帮助';
    }
    
    /**
     * 验证command是否注册
     *
     * @param string $command
     * @return string $commandClassName
     * */
    protected function _checkCommand($command)
    {
        $obj = new lib_command_command();
        return $obj->checkCommand($command);
    }
}

