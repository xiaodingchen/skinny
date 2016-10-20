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
        $commandClassName = $this->_checkCommand($command);
        $commandObj = new $commandClassName();
        if($commandObj instanceof lib_command_interface)
        {
            $title = $commandObj->commandTitle();
            $desc = $commandObj->commandExplain();
            logger::info('命令标题:'.$title);
            logger::info('使用说明:'. $desc);
        }
        else
        {
            logger::info("Error: {$commandClassName}  must implement the lib_command_interface interface.");
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

