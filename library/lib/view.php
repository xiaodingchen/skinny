<?php
/**
 * view.php
 * smarty模板处理
 * */
use Smarty;
class lib_lib_view {

    private  $__smarty;

    public function __construct()
    {
        $this->__smarty = new Smarty();

        $this->setSmartyOptions();
    }

    public function getSmartyInstance()
    {
        return $this->__smarty;
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
        // print_r($this->__smarty->getPluginsDir());
        // exit;
        $tpl_arr = explode('/', $tpl);
        if (count($tpl_arr) < 2)
        {
            throw new \SmartyException('Tpl file path is not valid');
        }
        
        $template_dir = APP_DIR . '/' . $tpl_arr[0] . '/' . config::get('tpl.app_tpl_dir');
        $this->__smarty->setTemplateDir($template_dir);
        
        unset($tpl_arr[0]);
        
        $tpl = implode('/', $tpl_arr);
        foreach ($data as $key => $value)
        {
            $this->__smarty->assign($key, $value);
        }
        if ($return)
        {
            return $this->__smarty->fetch($tpl);
        }
        
        return $this->__smarty->display($tpl);
    }
    
    /**
     * 设置smarty选项
     * */
    protected function setSmartyOptions()
    {
        $compile_dir = config::get('tpl.compile_dir'); // 编译目录
        $cache_dir = config::get('tpl.cache_dir'); // 缓存目录
        $config_dir = CONFIG_DIR; // 配置目录
        $caching = config::get('tpl.caching'); // 是否开启缓存
        $cache_lifetime = config::get('tpl.cache_lifetime'); // 缓存时间
        $left_delimiter = config::get('tpl.left_delimiter'); // 设置左定界符
        $right_delimiter = config::get('tpl.right_delimiter'); // 设置右定界符
        
        $this->__smarty->setCompileDir($compile_dir)
                        ->setCacheDir($cache_dir)
                        ->setConfigDir($config_dir);
        
        $this->__smarty->setCaching($caching);
        $this->__smarty->setCacheLifetime($cache_lifetime);
        $this->__smarty->setLeftDelimiter($left_delimiter);
        $this->__smarty->setRightDelimiter($right_delimiter);
        // 设置插件目录
        // 框架插件目录
        //$plugins_dir[] = SCRIPT_DIR.'/plugins/smarty/';
        $plugins_dir = [];
        $apps = config::get('tpl.tpl_plugins_apps');
        if ($apps)
        {
            foreach ($apps as $app)
            {
                $plugins_dir[] = APP_DIR . '/' . $app . '/service/' . config::get('tpl.app_tpl_plugins_dir');
            }
        }

        $this->__smarty->addPluginsDir($plugins_dir);
        
    }
}
