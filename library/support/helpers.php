<?php

use lib_support_str as Str;
use lib_support_arr as Arr;
use lib_support_collection as Collection;

if (! function_exists('dd'))
{

    /**
     * Dump the passed variables and end the script.
     *
     * @param dynamic mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($x)
        {
            var_dump($x);
        }, func_get_args());
        die();
    }
}

if (! function_exists('ends_with'))
{

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    function ends_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle)
        {
            if ($needle == substr($haystack, - strlen($needle))) return true;
        }
        
        return false;
    }
}

if (! function_exists('head'))
{

    /**
     * Get the first element of an array.
     * Useful for method chaining.
     *
     * @param array $array
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}

if (! function_exists('action'))
{

    /**
     * Generate a URL to a controller action.
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    function action($name, $parameters = array())
    {
        return url::action($name, $parameters);
    }
}

if (! function_exists('url'))
{

    /**
     * Generate a url for the application.
     *
     * @param string $path
     * @param mixed $parameters
     * @param bool $secure
     * @return string
     */
    function url($path = null, $parameters = array(), $secure = null)
    {
        return url::to($path, $parameters, $secure);
    }
}

if (! function_exists('route'))
{

    /**
     * Generate a URL to a named route.
     *
     * @param string $route
     * @param array $parameters
     * @return string
     */
    function route($route, $parameters = array())
    {
        return url::route($route, $parameters);
    }
}

if (! function_exists('with'))
{

    /**
     * Return the given object.
     * Useful for chaining.
     *
     * @param mixed $object
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }
}

if (! function_exists('value'))
{

    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (! function_exists('array_forget'))
{

    /**
     * Remove an array item from a given array using "dot" notation.
     *
     * @param array $array
     * @param string $key
     * @return void
     */
    function array_forget(&$array, $key)
    {
        $keys = explode('.', $key);
        
        while (count($keys) > 1)
        {
            $key = array_shift($keys);
            
            if (! isset($array[$key]) || ! is_array($array[$key]))
            {return;}
            
            $array = & $array[$key];
        }
        
        unset($array[array_shift($keys)]);
    }
}

if (! function_exists('array_get'))
{

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;
        
        if (isset($array[$key])) return $array[$key];
        
        foreach (explode('.', $key) as $segment)
        {
            if (! is_array($array) || ! array_key_exists($segment, $array))
            {return value($default);}
            
            $array = $array[$segment];
        }
        
        return $array;
    }
}

if (! function_exists('array_set'))
{

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array
     */
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;
        
        $keys = explode('.', $key);
        
        while (count($keys) > 1)
        {
            $key = array_shift($keys);
            
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key]))
            {
                $array[$key] = array();
            }
            
            $array = & $array[$key];
        }
        
        $array[array_shift($keys)] = $value;
        
        return $array;
    }
}

if (! function_exists('array_sort'))
{

    /**
     * Sort the array using the given callback.
     *
     * @param array $array
     * @param callable $callback
     * @return array
     */
    function array_sort($array, callable $callback)
    {
        return Arr::sort($array, $callback);
    }
}

if (! function_exists('array_sort_recursive'))
{

    /**
     * Recursively sort an array by keys and values.
     *
     * @param array $array
     * @return array
     */
    function array_sort_recursive($array)
    {
        return Arr::sortRecursive($array);
    }
}

if (! function_exists('array_dot'))
{

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array $array
     * @param string $prepend
     * @return array
     */
    function array_dot($array, $prepend = '')
    {
        $results = array();
        
        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $results = array_merge($results, array_dot($value, $prepend . $key . '.'));
            }
            else
            {
                $results[$prepend . $key] = $value;
            }
        }
        
        return $results;
    }
}

if (! function_exists('array_build'))
{

    /**
     * Build a new array using a callback.
     *
     * @param array $array
     * @param \Closure $callback
     * @return array
     */
    function array_build($array, Closure $callback)
    {
        return Arr::build($array, $callback);
    }
}

if (! function_exists('array_where'))
{

    /**
     * Filter the array using the given Closure.
     *
     * @param array $array
     * @param \Closure $callback
     * @return array
     */
    function array_where($array, Closure $callback)
    {
        $filtered = array();
        
        foreach ($array as $key => $value)
        {
            if (call_user_func($callback, $key, $value)) $filtered[$key] = $value;
        }
        
        return $filtered;
    }
}

if (! function_exists('camel_case'))
{

    /**
     * Convert a value to camel case.
     *
     * @param string $value
     * @return string
     */
    function camel_case($value)
    {
        return Str::camel($value);
    }
}

if (! function_exists('last'))
{

    /**
     * Get the last element from an array.
     *
     * @param array $array
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if (! function_exists('array_pull'))
{

    /**
     * Get a value from the array, and remove it.
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function array_pull(&$array, $key, $default = null)
    {
        $value = array_get($array, $key, $default);
        
        array_forget($array, $key);
        
        return $value;
    }
}

if (! function_exists('array_except'))
{

    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    function array_except($array, $keys)
    {
        return array_diff_key($array, array_flip((array) $keys));
    }
}

if (! function_exists('array_first'))
{

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param array $array
     * @param \Closure $callback
     * @param mixed $default
     * @return mixed
     */
    function array_first($array, $callback, $default = null)
    {
        foreach ($array as $key => $value)
        {
            if (call_user_func($callback, $key, $value)) return $value;
        }
        
        return value($default);
    }
}

if (! function_exists('array_bind_key'))
{

    /**
     * 根据传入的数组和数组中值的键值，将对数组的键进行替换
     *
     * @param array $array
     * @param string $key
     */
    function array_bind_key($array, $key)
    {
        foreach ((array) $array as $value)
        {
            if (! empty($value[$key]))
            {
                $k = $value[$key];
                $result[$k] = $value;
            }
        }
        return $result;
    }
}

if (! function_exists('array_column'))
{

    function array_column($array, $key)
    {
        return Arr::pluck($array, $key);
    }
}

if (! function_exists('str_contains'))
{

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    function str_contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle)
        {
            if ($needle != '' && strpos($haystack, $needle) !== false) return true;
        }
        
        return false;
    }
}

if (! function_exists('str_is'))
{

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string $pattern
     * @param string $value
     * @return bool
     */
    function str_is($pattern, $value)
    {
        if ($pattern == $value) return true;
        
        $pattern = preg_quote($pattern, '#');
        
        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern) . '\z';
        
        return (bool) preg_match('#^' . $pattern . '#', $value);
    }
}

if (! function_exists('str_random'))
{

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     * @return string
     *
     * @throws \RuntimeException
     */
    function str_random($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes'))
        {
            $bytes = openssl_random_pseudo_bytes($length * 2);
            
            if ($bytes === false)
            {throw new \RuntimeException('Unable to generate random string.');}
            
            return substr(str_replace(array(
                '/','+','='
            ), '', base64_encode($bytes)), 0, $length);
        }
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
        // return static::quickRandom($length);
    }
}

if (! function_exists('starts_with'))
{

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    function starts_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle)
        {
            if ($needle != '' && strpos($haystack, $needle) === 0) return true;
        }
        
        return false;
    }
}

if (! function_exists('preg_replace_sub'))
{

    /**
     * Replace a given pattern with each value in the array in sequentially.
     *
     * @param string $pattern
     * @param array $replacements
     * @param string $subject
     * @return string
     */
    function preg_replace_sub($pattern, &$replacements, $subject)
    {
        return preg_replace_callback($pattern, function ($match) use(&$replacements)
        {
            return array_shift($replacements);
        
        }, $subject);
    }
}

if (! function_exists('format_fields'))
{

    function format_fields($field, $extendsFields)
    {
        $arrayField = explode(',', $field);
        foreach ((array) $arrayField as $value)
        {
            $extended = explode('.', $value);
            
            if (in_array($extended[0], $extendsFields))
            {
                $extendedCols[$extended[0]][] = $extended[1] ? $extended[1] : '*';
            }
            else
            {
                $cols[] = $value;
            }
        }
        $result['rows'] = $cols ? implode(',', $cols) : "*";
        foreach ((array) $extendedCols as $col => $value)
        {
            $result['extends'][$col] = implode(',', $value);
        }
        return $result;
    }
}

// 将一个字符串拼接至一个不包含自身的字符串中
if (! function_exists('str_append'))
{

    function str_append($string, $appendStr)
    {
        if ($string === '*') return $string;
        $arr = explode(',', $appendStr);
        foreach ($arr as $val)
        {
            $pos = strpos($string, $val);
            if ($pos === false)
            {
                $string .= "," . $val;
            }
        }
        return $string;
    }
}
if (! function_exists('studly_case'))
{

    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     * @return string
     */
    function studly_case($value)
    {
        return lib_support_str::studly($value);
    }
}

if (! function_exists('snake_case'))
{

    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    function snake_case($value, $delimiter = '_')
    {
        return lib_support_str::snake($value, $delimiter);
    }
}

if (! function_exists('collect'))
{

    /**
     * Create a collection from the given value.
     *
     * @param mixed $value
     * @return \Illuminate\Support\Collection
     */
    function collect($value = null)
    {
        return new Collection($value);
    }
}

if (! function_exists('data_get'))
{

    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param mixed $target
     * @param string|array $key
     * @param mixed $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key))
        {return $target;}
        
        $key = is_array($key) ? $key : explode('.', $key);
        
        foreach ($key as $segment)
        {
            if (is_array($target))
            {
                if (! array_key_exists($segment, $target))
                {return value($default);}
                
                $target = $target[$segment];
            }
            elseif ($target instanceof ArrayAccess)
            {
                if (! isset($target[$segment]))
                {return value($default);}
                
                $target = $target[$segment];
            }
            elseif (is_object($target))
            {
                if (! isset($target->{$segment}))
                {return value($default);}
                
                $target = $target->{$segment};
            }
            else
            {
                return value($default);
            }
        }
        
        return $target;
    }
}
if (! function_exists('data_get'))
{

    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param mixed $target
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) return $target;
        
        foreach (explode('.', $key) as $segment)
        {
            if (is_array($target))
            {
                if (! array_key_exists($segment, $target))
                {return value($default);}
                
                $target = $target[$segment];
            }
            elseif ($target instanceof ArrayAccess)
            {
                if (! isset($target[$segment]))
                {return value($default);}
                
                $target = $target[$segment];
            }
            elseif (is_object($target))
            {
                if (! isset($target->{$segment}))
                {return value($default);}
                
                $target = $target->{$segment};
            }
            else
            {
                return value($default);
            }
        }
        
        return $target;
    }
}
if (! function_exists('format_filesize'))
{

    function format_filesize($filesize)
    {
        $bytes = floatval($filesize);
        switch ($bytes) {
            case $bytes < 1024:
                $result = $bytes . 'B';
                break;
            case ($bytes < pow(1024, 2)):
                $result = strval(round($bytes / 1024, 2)) . 'KB';
                break;
            default:
                $result = $bytes / pow(1024, 2);
                $result = strval(round($result, 2)) . 'MB';
                break;
        }
        return $result;
    }
}

/**
 * PHP < 5.5 兼容函数
 */
if (! function_exists('array_column')) {
    function array_column($input, $column_key, $index_key = null)
    {
        if ($index_key !== null) {
            // Collect the keys
            $keys = array();
            $i = 0; // Counter for numerical keys when key does not exist

            foreach ($input as $row) {
                if (array_key_exists($index_key, $row)) {
                    // Update counter for numerical keys
                    if (is_numeric($row[$index_key]) || is_bool($row[$index_key])) {
                        $i = max($i, (int) $row[$index_key] + 1);
                    }

                    // Get the key from a single column of the array
                    $keys[] = $row[$index_key];
                } else {
                    // The key does not exist, use numerical indexing
                    $keys[] = $i++;
                }
            }
        }

        if ($column_key !== null) {
            // Collect the values
            $values = array();
            $i = 0; // Counter for removing keys

            foreach ($input as $row) {
                if (array_key_exists($column_key, $row)) {
                    // Get the values from a single column of the input array
                    $values[] = $row[$column_key];
                    $i++;
                } elseif (isset($keys)) {
                    // Values does not exist, also drop the key for it
                    array_splice($keys, $i, 1);
                }
            }
        } else {
            // Get the full arrays
            $values = array_values($input);
        }

        if ($index_key !== null) {
            return array_combine($keys, $values);
        }

        return $values;
    }
}

if(!function_exists('readline')){

    function readline($prompt){
        echo $prompt;
        //ob_flush();
        $input = '';
        while(1){
            $key = fgetc(STDIN);
            switch($key){
                case "\n":
                    return $input;

                default:
                    $input.=$key;
            }
        }
    }

    function readline_add_history($line){
        //...
    }

    function readline_completion_function($callback){

    }
}

