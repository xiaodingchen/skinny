<?php
//use Symfony\Component\HttpKernel\Exception\HttpException;
//use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use lib_pipeline_pipeline as Pipeline;
class kernel {

    private static $__singleton_instance = [];
    private static $__routeMiddleware = [];
    private static $__middleware = [];
    private static $__exception_instance = null;
    private static $__running_in_console = null;
    public static $base_url = null;

    /**
     * boot
     * */
    public static function boot()
    {
        $pathinfo = request::getPathInfo();
        
        // 生成part
        if(isset($pathinfo{1})){
            if($p = strpos($pathinfo,'/',2)){
                $part = substr($pathinfo,0,$p);
            }else{
                $part = $pathinfo;
            }
        }else{
            $part = '/';
        }
        
        static::registRouteMiddleware();
        
        //$response = route::dispatch(request::instance());
        $response = static::sendRequestThroughRouter(request::instance());
        $response->send();
    }

    public static function removeIndex($root) {
        $i = 'index.php';

        return str_contains($root, $i) ? str_replace('/'.$i, '', $root) : $root;
    }

    public static function baseUrl($full=false){
        $c = ($full) ? 'true' : 'false';
        if(!isset(self::$__base_url[$c]) || defined('BASE_URL')){
            if(defined('BASE_URL')){

                if($full){
                    self::$__base_url[$c] = constant('BASE_URL');
                }else{
                    $url = parse_url(constant('BASE_URL'));
                    if(isset($url['path'])){
                        self::$__base_url[$c] = $url['path'];
                    }else{
                        self::$__base_url[$c] = '';
                    }
                }
            }else{
                if(!isset(self::$base_url)){
                    self::$base_url = static::removeIndex(request::getBaseUrl());
                    // 目前的方式是保持request的纯洁性. 在base_url中做特殊处理.
                }

                if(self::$base_url == '/'){
                    self::$base_url = '';
                }

                if($full){
                    self::$__base_url[$c] = static::removeIndex(request::root());
                }else{
                    self::$__base_url[$c] = self::$base_url;
                }
            }
        }

        return self::$__base_url[$c];
    }
    
/**
     * Send the given request through the middleware / router.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    static public function sendRequestThroughRouter($request) 
    {
        return (new Pipeline())
                    ->send($request)
                    ->through(static::$__middleware)
                    ->then(static::dispatchToRouter());
    }

    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter()
    {
        return function ($request) {
            return route::dispatch($request);
        };
    }
    

    static public function registRouteMiddleware()
    {
        foreach (static::$__routeMiddleware as $key => $middleware)
        {
            route::middleware($key, $middleware);
        }
    }
    
    /**
     * 错误处理
     */
    public static function startExceptionHandling()
    {
        if (! isset(self::$__exception_instance))
        {
            self::$__exception_instance = kernel::single('lib_exception_handleExceptions');
        }
        
        self::$__exception_instance->bootstrap();
    }
    
    /**
     * 判断PHP运行模式
     * */
    static public function runningInConsole()
    {
        if (static::$__running_in_console == null) {
            return php_sapi_name() == 'cli';
        }
    
        return static::$__running_in_console;
    }

    /**
     * 设置命令模式
     * */
    static public function simulateRunningInConsole()
    {
        static::$__running_in_console = true;
    }
    
    /**
     * 当前运行环境
     * */
    static public function environment()
    {
        return 'production';
    }
    
    /**
     * 获取一个对象的实例
     *
     * @param string $className 类名
     * @param mixed $arg 参数
     * @return object
     *
     */
    public static function single($className, $arg = null)
    {
        if (is_object($arg))
        {
            $key = get_class($arg);
            $key = '__class__' . $key;
        }
        else
        {
            $key = md5('__key__' . serialize($arg));
        }
        
        if (! isset(self::$__singleton_instance[$className][$key]))
        {
            self::$__singleton_instance[$className][$key] = new $className($arg);
        }
        
        return self::$__singleton_instance[$className][$key];
    }
}
