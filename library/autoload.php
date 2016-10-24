<?php

class ClassLoader {
    /**
     * Indicates if a ClassLoader has been registered.
     *
     * @var bool
     */
    protected static $_registed = false;
    
    /**
     * The array of class aliases.
     *
     * @var array
     */
    protected static $_aliases = array ();
    
    /**
     * app支持的类类型.(已废弃)
     *
     * @var array
     */
    protected static $_supportAppTypes = [ 
            'ctl',
            'mdl',
            'api',
            'service',
            'middleware' 
    ];
    
    /**
     * 已经注册了的类
     *
     * @var array
     */
    protected static $_registers = [ ];
    
    /**
     * app支持的类型和目录的对应关系, 如果$_supportAppTypes里有定义, 而此
     * 定义中不存在, 那么默认用类型名作为目录名.
     *
     *
     * @var array
     */
    protected static $_supportAppTypesCorrDirectory = [ 
            'ctl' => 'controller',
            'mdl' => 'model',
    ];
    
    protected static $_loadConflict = ['Smarty'];
    
    /**
     * Register the given class loader on the auto-loader stack.
     *
     * @return bool
     */
    public static function register() {
        if (! static::$_registed) {
            static::$_registed = spl_autoload_register ( array (
                    '\ClassLoader',
                    'load' 
            ) );
        }
    }
    
    /**
     * Add the alias to ClassLoader
     *
     * @param string $class            
     * @param string $alias            
     * @return bool
     */
    public static function addAlias($class, $alias) {
        static::$_aliases [$class] = $alias;
    }
    
    /**
     * Add the aliases to ClassLoader
     *
     * @param array $aliases            
     * @return bool
     */
    public static function addAliases($aliases) {
        if (is_array ( $aliases )) {
            static::$_aliases = array_merge ( static::$_aliases, $aliases );
        }
    }
    public static function commonLoad($appId, $className, $type, $classNamePath) {
        $typePath = static::$_supportAppTypesCorrDirectory [$type] ?: $type;
        $relativePath = sprintf ( '%s/%s/%s.php', $appId, $typePath, $classNamePath );
        
        $paths [] = APP_DIR . '/' . $relativePath;
        
        foreach ( $paths as $path ) {
            if (! static::$_registers [$path]) {
                if (file_exists ( $path )) {
                    include ($path);
                    static::$_registers [$path] = true;
                    return;
                }
            } else {
                return;
            }
        }
        
        throw new RuntimeException ( 'Don\'t find ' . $type . ' file:' . $className );
    }
    
    public static function libLoad($className)
    {
        $typePath = LIB_DIR;
        $tmpArr = explode('_', $className);
        
        // 解决composer自动加载冲突
        if(in_array($tmpArr[0], self::$_loadConflict))
        {
            return false;
        }
        
        if($tmpArr[0] != 'lib')
        {
            throw new RuntimeException ( 'Don\'t find :' . $className .' file.' );
        }
        unset($tmpArr[0]);
        $tmpStr = implode('/', $tmpArr) . '.php';
        $relativePath = $typePath . '/' . $tmpStr;
        $paths[] = $relativePath;
        foreach ( $paths as $path ) {
            if (! static::$_registers [$path]) {
                if (file_exists ( $path )) {
                    include ($path);
                    static::$_registers [$path] = true;
                    return;
                }
            } else {
                return;
            }
        }
        
        throw new RuntimeException ( 'Don\'t find lib' . ' file:' . $className );
    }
    
    /**
     * Load the given class file.
     *
     * @param string $class            
     * @return bool
     */
    public static function load($className) {
        // 检测alias
        if (array_key_exists ( $className, static::$_aliases )) {
            return class_alias ( static::$_aliases [$className], $className );
        }
        
        list ( $appId ) = $fragments = explode ( '_', $className );
        
        // 加载框架核心类，composer自动加载冲突类也在libload中处理
        if($appId == 'lib' || in_array($appId, static::$_loadConflict))
        {
            static::libLoad($className);
            return true;
        }
        // 加载app下的类
        $type = $fragments [1];
        switch ($type) {
            case 'mdl' :
                try {
                    static::commonLoad ( $appId, $className, $type, implode ( '/', array_slice ( $fragments, 2 ) ) );
                } catch ( RuntimeException $e ) {
                    $paths = [];
                    $relativePath = sprintf ( '%s/%s/%s.php', $appId, config::get('database.app_dbtable_dir', 'dbtable'), implode ( '_', array_slice ( $fragments, 2 ) ) );
                    $paths [] = APP_DIR . '/' . $relativePath;
        
                    foreach ( $paths as $path ) {
                        if (file_exists ( $path )) {
                            $parent_model_class = app::get ( $appId )->getParentModelClass();
                            eval ( "class {$className} extends {$parent_model_class}{ }" );
                            return true;
                        }
                    }
                    throw new RuntimeException ( 'Don\'t find model file "' . $className . '"' );
                }
                
                default:
                    static::commonLoad ( $appId, $className, $type, implode ( '/', array_slice ( $fragments, 2 ) ) );
        }
        
        return true;
    } // End Function
}

