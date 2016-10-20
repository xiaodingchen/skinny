<?php
/**
 * file.php
 * 
 * */

class lib_cache_driver_file implements lib_cache_interface{
    
    protected $prefix;
    
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }
    
    
    public function get($key)
    {
        $file = $this->_getPath($key);
        if(! file_exists($file))
        {
            return false;
        }
        
        $fileContent = file_get_contents($file);
        if(! $fileContent)
        {
            return false;
        }
        
        $contents = unserialize($fileContent);
        $expriy = $contents['expriy'];
        // 验证缓存是否过期
        if(time() > $expriy && $expriy !== 0)
        {
            unlink($file);
            return false;
        }
        
        if(! isset($contents['value']))
        {
            return false;
        }
        
        return $contents['value'];
    }
    
    public function set($key, $value, $seconds = 0)
    {
        $path = CACHE_DIR;
        if(! file_exists($path))
        {
            if(! mkdir($path, 0775, 1))
            {
                return false;
            }
        }
        
        if(! is_writable($path))
        {
            return false;
        }
        
        $file = $this->_getPath($key);
        $contents['value'] = $value;
        $contents['expriy'] = 0;
        if($seconds > 0)
        {
            $contents['expriy'] = time()+$seconds;
        }
        
        return file_put_contents($file, serialize($contents), LOCK_EX);
        
    }
    
    public function delete($key)
    {
        $file = $this->_getPath($key);
        
        return @unlink($file);
    }
    
    public function clear()
    {
        $path = CACHE_DIR;
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    @unlink($file);
                }
            }
            closedir($handle);
        }
        
        return true;
    }
    
    protected function _getPath($key)
    {
        $key = $this->_getRealKey($key);
        $filename = md5($key);
        $path = CACHE_DIR;
        $filepath = $path.'/'.$filename.'.php';
        
        return $filepath;
    }
}

