<?php
/**
 * handler.php
 * 
 * */
use Exception;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyDisplayer;
use lib_exception_contracts_exceptionHandler as exceptionHandler;

class lib_exception_foundation_handler implements exceptionHandler {
    
    use \lib_exception_foundation_trait_console;

    protected $dontReport = [];

    /**
     * Report or log an exception.
     *
     * @param \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        if ($this->shouldReport($e)) logger::error($e);
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param \Exception $e
     * @return bool
     */
    public function shouldReport(Exception $e)
    {
        return ! $this->shouldntReport($e);
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param \Exception $e
     * @return bool
     */
    protected function shouldntReport(Exception $e)
    {
        foreach ($this->dontReport as $type)
        {
            if ($e instanceof $type) return true;
        }
        return false;
    }

    protected function ajax()
    {
        return (request::ajax() || request::wantsJson());
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        if ($this->ajax())
        {
            return $this->renderHttpJsonException($e);
        }
        elseif ($this->isHttpException($e))
        {return $this->renderHttpException($e);}
        
        $debug = config::get('app.debug', false);
        if ($debug)
        {
            return $this->renderExceptionWithWhoops($e);
        }
        else
        {
            // 当错误异常
            return $this->renderHttpException(new ServiceUnavailableHttpException());
        
        }
    
    }

    protected function renderHttpJsonException(Exception $e)
    {
        // 如果不是httpException, 那么就统一抛出503错误.
        if (! $this->isHttpException($e))
        {
            if (config::get('app.debug', false) === false)
            {
                $e = new ServiceUnavailableHttpException();
            }
            else
            {
                return $this->renderExceptionWithWhoops($e);
            }
        }
        $status = $e->getStatusCode();
        $redirect = $this->getHttpExceptionUrl($e);
        $json = [
            'code' => $status,
            'error' => true,
            'redirect' => $redirect
        ];
        return new lib_http_response_json($json, $status);
    }

    protected function getHttpExceptionUrl($e)
    {
        $status = $e->getStatusCode();
        
        if (! $status)
        {
            $status = 503;
        }
        
        if (kernel::single('lib_lib_filesystem')->exists(PUBLIC_DIR . "/{$status}.html"))
        {            
            // 这种方式在站点拉开的情况下有点问题, 后续会统一处理.
            return kernel::baseUrl(1) . "/{$status}.html";
        }
        
        return false;
    }

    /**
     * Render the given HttpException.
     *
     * @param \Symfony\Component\HttpKernel\Exception\HttpException $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $e)
    {
        if ($url = $this->getHttpExceptionUrl($e))
        {return redirect::away($url);}
        
        return (new SymfonyDisplayer(false))->createResponse($e);
    }

    /**
     * Determine if the given exception is an HTTP exception.
     *
     * @param \Exception $e
     * @return bool
     */
    protected function isHttpException(Exception $e)
    {
        return $e instanceof HttpException;
    }

    /**
     * 使用whoops错误处理组件
     */
    protected function renderExceptionWithWhoops(Exception $e)
    {
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        
        return new SymfonyResponse($whoops->handleException($e), $e->getStatusCode(), $e->getHeaders());
    }
}
