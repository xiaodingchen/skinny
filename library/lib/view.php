<?php
/**
 * view.php
 * smarty模板处理
 * */
use Smarty;
class lib_lib_view {

    private static $__smarty;

    public function __construct()
    {
        if (! static::$__smarty)
        {
            static::$__smarty = new Smarty();
        }
        
        $this->setSmartyOptions();
    }

    public function getSmartyInstance()
    {
        return static::$__smarty;
    }

    /**
     * 解析模板
     * 
     * @param string $tpl
     * @param array $data
     * @param bool $return
     * @return void|string
     */
    public function make($tpl, array $data = [], $return = false)
    {
        $tpl_arr = explode('/', $tpl);
        if (count($tpl_arr) < 2)
        {
            throw new \SmartyException('Tpl file path is not valid');
        }
        
        static::$__smarty->template_dir = APP_DIR . '/' . $tpl_arr[0] . '/' . config::get('tpl.app_tpl_dir');
        unset($tpl_arr[0]);
        
        $tpl = implode('/', $tpl_arr);
        foreach ($data as $key => $value)
        {
            static::$__smarty->assign($key, $value);
        }
        if ($return)
        {
            return static::$__smarty->fetch($tpl);
        }
        
        return static::$__smarty->display($tpl);
    }
    
    /**
     * 设置smarty选项
     * */
    protected function setSmartyOptions()
    {
        static::$__smarty->compile_dir = config::get('tpl.compile_dir'); // 编译目录
        static::$__smarty->cache_dir = config::get('tpl.cache_dir'); // 缓存目录
        static::$__smarty->config_dir = CONFIG_DIR; // 配置目录
        static::$__smarty->caching = config::get('tpl.caching'); // 是否开启缓存
        static::$__smarty->cache_lifetime = config::get('tpl.cache_lifetime'); // 缓存时间
        static::$__smarty->left_delimiter = config::get('tpl.left_delimiter'); // 设置左定界符
        static::$__smarty->right_delimiter = config::get('tpl.right_delimiter'); // 设置右定界符
        // 设置插件目录
        // 框架插件目录
        $plugins_dir[] = SCRIPT_DIR.'/plugins/smarty';
        $apps = config::get('tpl.tpl_plugins_apps');
        if ($apps)
        {
            foreach ($apps as $app)
            {
                $plugins_dir[] = APP_DIR . '/' . $app . '/service/' . config::get('tpl.app_tpl_plugins_dir');
            }
    
            static::$__smarty->plugins_dir = $plugins_dir;
        }
    }
}
