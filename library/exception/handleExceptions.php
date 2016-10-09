<?php

/**
 * handleExceptions.php
 * 全局错误处理，前期只做简单的错误处理，后期需细分
 * 
 * late-xiao@foxmail.com
 * */
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
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
        if(kernel::runningInConsole())
        {
            return $this->renderForConsole($e);
        }
        // 需要区分http请求模式，后期扩展
        return $this->renderExceptionWithWhoops($e);
        
    }
    
    public function handleShutdown()
    {
        $error = error_get_last();
        if ( ! is_null($error) && $this->isFatal($error['type']))
        {
            $this->handleException($this->fatalExceptionFromError($error, 0));
        }
    }
    
    /**
     * Create a new fatal exception instance from an error array.
     *
     * @param  array  $error
     * @param  int|null  $traceOffset
     * @return \Symfony\Component\Debug\Exception\FatalErrorException
     */
    protected function fatalExceptionFromError(array $error, $traceOffset = null)
    {
    	return new FatalErrorException(
    		$error['message'], $error['type'], 0, $error['file'], $error['line'], $traceOffset
    	);
    }

    /**
     * Determine if the error type is fatal.
     *
     * @param  int  $type
     * @return bool
     */
    protected function isFatal($type)
    {
        $a = in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
        
        return $a;
    }

    /**
     * 使用whoops错误处理组件
     * */
    protected function renderExceptionWithWhoops(Exception $e)
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());

        return new SymfonyResponse(
            $whoops->handleException($e),
            $e->getStatusCode(),
            $e->getHeaders()
            );
    }
    
    /**
     * 命令模式下错误处理
     * */
    protected function renderForConsole(Exception $e)
    {
        $output = new ConsoleOutput();
        (new ConsoleApplication)->renderException($e, $output);
    }
}
