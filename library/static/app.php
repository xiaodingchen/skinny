<?php
/**
 * app.php
 * */

class lib_static_app {

    private static $__instance = array();

    public function __construct($app_id)
    {
        $this->app_id = $app_id;
        $this->app_dir = APP_DIR . '/' . $app_id;
        $this->public_app_dir = PUBLIC_DIR . '/app/' . $app_id;
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
}
