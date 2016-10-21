<?php
/**
 * handler.php
 * 
 * */
use lib_exception_contracts_exceptionHandler as exceptionHandler;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class lib_exception_foundation_handler implements exceptionHandler {
    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        
    }
    
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        
    }
    
    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Exception  $e
     * @return void
     */
    public function renderForConsole(Exception $e)
    {
        $output = new ConsoleOutput();
        (new ConsoleApplication)->renderException($e, $output);
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
}
