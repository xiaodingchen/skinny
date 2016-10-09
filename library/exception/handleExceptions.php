<?php

/**
 * handleExceptions.php
 * 
 * late-xiao@foxmail.com
 * */
class lib_exception_handleExceptions {

    /**
     * 定义全局错误处理
     * */
    public function bootstrap()
    {
        error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_COMPILE_ERROR);
        
        set_error_handler([$this, 'handleError']);
        
        set_exception_handler([$this, 'handleException']);
        
        register_shutdown_function([$this, 'handleShutdown']);
    
    }
    
    public function handleError($level, $message, $file = '', $line = 0, $context = array())
    {
        if (error_reporting() & $level)
        {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }
    
    public function handleException($e)
    {
    
    }
    
    public function handleShutdown()
    {
    
    }
}
