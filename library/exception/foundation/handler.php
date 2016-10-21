<?php
/**
 * handler.php
 * 
 * */
use lib_exception_contracts_exceptionHandler as exceptionHandler;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class lib_exception_foundation_handler implements exceptionHandler {
    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     */
    use lib_exception_trait_console;
    
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
        if($request->ajax())
        {
            $data = [];
            $data['code'] = $e->getCode();
            $data['msg'] = $e->getMessage();
            return response::json($data)->send();
        }
        
        $debug = config::get('app.debug', false);
        if($debug)
        {
            return $this->renderExceptionWithWhoops($e);
        }
        
        
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
