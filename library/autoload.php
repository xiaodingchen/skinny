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
     * app支持的类类型.
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
        if (defined ( 'CUSTOM_CORE_DIR' ))
            $paths [] = CUSTOM_CORE_DIR . '/' . $relativePath;
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
        if($tmpArr[0] != 'lib')
        {
            throw new RuntimeException ( 'Don\'t find file:' . $className );
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
        
        if (in_array ( $fragments [1], static::$_supportAppTypes )) {
            $type = $fragments [1];
            switch ($type) {
                case 'ctl' :
                case 'api' :
                case 'middleware' :
                case 'service':
                    static::commonLoad ( $appId, $className, $type, implode ( '/', array_slice ( $fragments, 2 ) ) );
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
            }
        } else {
            static::libLoad($className);
        }
    } // End Function
}

