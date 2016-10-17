<?php
/**
 * app.php
 * */

class lib_static_app {

    private static $__instance = array();

    public function __construct($app_id = null)
    {
        if($app_id)
        {
            $appList = self::getAppList();
            if(! in_array($app_id, $appList))
            {
                throw new LogicException("{$app_id} not found");
            }
            
            $this->app_id = $app_id;
            $this->app_dir = APP_DIR . '/' . $app_id;
            $this->public_app_dir = PUBLIC_DIR . '/app/' . $app_id;
        }
        
    }
    
    /**
     * 获取所有的应用列表
     *
     * @return array
     * */
    public static function getAppList()
    {
        $tmpDir = APP_DIR;
        
        $appList = [];
        foreach (new DirectoryIterator($tmpDir) as $file)
        {
            $fileName = $file->getFilename();
            if(is_dir($tmpDir . '/' . $fileName) && $fileName != '.' && $fileName != '..')
            {
                $appList [] = $fileName;
            }
        }
        
        return $appList;
    }

    public static function get($app_id)
    {
        if (! isset(self::$__instance[$app_id]))
        {
            self::$__instance[$app_id] = new app($app_id);
        }
        return self::$__instance[$app_id];
    }

    
    public function controller($controller)
    {
        return kernel::single($this->app_id . '_ctl_' . $controller, $this);
    }

    public function model($model)
    {
        return kernel::single($this->app_id . '_mdl_' . $model, $this);
    }
    
    
    
    /**
     * 
     * 取得一个数据库资源
     * */
    public function database()
    {
        $prefix = $this->getDataBasePrefix();
        
        return db::connection($prefix);
    }
    
    /**
     * 每个应用都可以设置自己的数据库,在database.php文件中配置
     * */
    public function getDataBasePrefix()
    {
        return $this->app_id;
    }
    
    public function getParentModelClass()
    {
        return 'lib_lib_model';
    }
}
