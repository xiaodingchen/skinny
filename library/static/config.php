<?php

class lib_static_config
{
    static $environment = 'production';

    static protected $_items = array();

    static protected $_loaded= array();

    static public function get_path()
    {
        return ROOT_DIR.'/config';
    }

    static private function parse_key($key)
    {
        $segments =  explode('.', $key);
        $group = $segments[0];
        if (count($segments) == 1){
            return array($group, null);
        }else{
            $item = implode('.', array_slice($segments, 1));

            return array($group, $item);
        }
    }

    static public function set($key, $value)
    {
        list($group, $item) = static::parse_key($key);
        static::load($group);
        if (is_null($item)) {
            static::$_items[$group] = $value;
        } else {
            array_set(self::$_items[$group], $item, $value);
        }
    }

    static public function get($key, $default=null)
    {
        list($group, $item) = static::parse_key($key);
        static::load($group);
        return array_get(static::$_items[$group], $item, $default);
    }

    private static function load($group)
    {
        $env = static::$environment;
        
        if (static::$_loaded[$group]==true)
        {
            return;
        }

        $items = static::realLoad($env, $group);

        static::$_items[$group] = $items;
        static::$_loaded[$group] = true;
    }

    private static function realLoad($environment, $group)
    {
        $items = array();
        
        $path = static::get_path();

		if (is_null($path))
		{
			return $items;
		}

        $files = kernel::single('lib_lib_filesystem');
        $file = "{$path}/{$group}.php";

        $items = [];

        if ($files->exists($file))
        {
            $items = $files->getRequire($file);
        }

        $file = "{$path}/{$environment}/{$group}.php";

		if ($files->exists($file))
		{
			$items = static::mergeEnvironment($items, $file);
		}
        return $items;
        //*/
    }

	/**
	 * Merge the items in the given file into the items.
	 *
	 * @param  array   $items
	 * @param  string  $file
	 * @return array
	 */
	static protected function mergeEnvironment(array $items, $file)
	{
        $files = kernel::single('lib_lib_filesystem');
		return array_replace_recursive($items, $files->getRequire($file));
	}
}
