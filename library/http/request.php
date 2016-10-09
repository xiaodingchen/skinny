<?php
/**
 * request.php
 * 处理http request
 * */
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
class lib_http_request extends SymfonyRequest{
    /*
     * app name
     * @val string
     */
    protected $_app = null;
    
    /*
     * ctl name
     * @val string
     */
    protected $_ctl = null;
    
    /*
     * act name
     * @val string
     */
    protected $_act = null;
    
    /*
     * params
     * @val array
     */
    protected $_params = null;
    
    /**
     * The decoded JSON content for the request.
     *
     * @var string
     */
    protected $json;
    
    /**
     * The the root URL for the application.
     *
     * @var string
     */
    protected $root;
    
    /**
     * The Illuminate session store implementation.
     *
     * @var \Illuminate\Session\Store
     */
    protected $sessionStore;
    
    /**
     * Return the Request instance.
     *
     * @return \Illuminate\Http\Request
     */
    public function instance()
    {
        return $this;
    }
    
    /**
     * Get the request method
     * 
     * @return \Illuminate\Http\request
     * */
    public function getMethod()
    {
        return $this->getMethod();
    }
    
    /**
     * Get the root URL for the application.
     *
     * @return string
     */
    public function root()
    {
        if (!$this->root)
        {
            $this->root = rtrim($this->getSchemeAndHttpHost().$this->getBaseUrl(), '/');
        }
    
        return $this->root;
    }
    
    /**
     * Get the URL (no query string) for the request.
     *
     * @return string
     */
    public function url()
    {
        return rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/');
    }
    
    /**
     * Get the full URL for the request.
     *
     * @return string
     */
    public function fullUrl()
    {
        $query = $this->getQueryString();
    
        return $query ? $this->url().'?'.$query : $this->url();
    }
    
    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path()
    {
        $pattern = trim($this->getPathInfo(), '/');
    
        return $pattern == '' ? '/' : $pattern;
    }
    
    public function primaryDomain()
    {
        $host = $this->getHost();
        preg_match('/([a-z0-9][a-z0-9\-]*?\.(?:com|cn|net|org|gov|info|la|cc|co|me)(?:\.(?:cn|jp))?)$/i', $host, $matches);
        $primaryDomain = $matches[1] ?: $host;
        return $primaryDomain;
    }
    
    /**
     * set Path info
     *
     * @return string
     */
    public function setPathInfo($pathInfo)
    {
        $this->pathInfo = $pathInfo;
    }
    
    /**
     * Get the current encoded path info for the request.
     *
     * @return string
     */
    public function decodedPath()
    {
        return rawurldecode($this->path());
    }
    
    /**
     * Get a segment from the URI (1 based index).
     *
     * @param  string  $index
     * @param  mixed   $default
     * @return string
     */
    public function segment($index, $default = null)
    {
        return array_get($this->segments(), $index - 1, $default);
    }
    
    /**
     * Get all of the segments for the request path.
     *
     * @return array
     */
    public function segments()
    {
        $segments = explode('/', $this->path());
    
        return array_values(array_filter($segments, function($v) { return $v != ''; }));
    }
    
    /**
     * Determine if the current request URI matches a pattern.
     *
     * @param  dynamic  string
     * @return bool
     */
    public function is()
    {
        foreach (func_get_args() as $pattern)
        {
            if (str_is($pattern, urldecode($this->path())))
            {
                return true;
            }
        }
    
        return false;
    }
    
    /**
     * Determine if the request is the result of an AJAX call.
     *
     * @return bool
     */
    public function ajax()
    {
        return $this->isXmlHttpRequest();
    }
    
    /**
     * Determine if the request is over HTTPS.
     *
     * @return bool
     */
    public function secure()
    {
        return $this->isSecure();
    }
    
    /**
     * Determine if the request contains a given input item key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key)
    {
        $keys = is_array($key) ? $key : func_get_args();
    
        $input = $this->all();
    
        foreach ($keys as $value)
        {
            if ( ! array_key_exists($value, $input)) return false;
        }
    
        return true;
    }
    
    /**
     * Determine if the request rcontains a non-empty value for an input item.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();
    
        foreach ($keys as $value)
        {
            if ($this->isEmptyString($value)) return false;
        }
    
        return true;
    }
    
    /**
     * Determine if the given input key is an empty string for "has".
     *
     * @param  string  $key
     * @return bool
     */
    protected function isEmptyString($key)
    {
        $boolOrArray = is_bool($this->input($key)) || is_array($this->input($key));
    
        return ! $boolOrArray && trim((string) $this->input($key)) === '';
    }
    
    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function all()
    {
        return array_merge_recursive($this->input(), $this->files->all());
    }
    
    /**
     * Retrieve an input item from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public function input($key = null, $default = null)
    {
        $input = $this->getInputSource()->all() + $this->query->all();
    
        return array_get($input, $key, $default);
    }
    
    /**
     * Get a subset of the items from the input data.
     *
     * @param  array  $keys
     * @return array
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
    
        $results = [];
    
        $input = $this->all();
    
        foreach ($keys as $key)
        {
            array_set($results, $key, array_get($input, $key, null));
        }
    
        return $results;
    }
    
    /**
     * Get all of the input except for a specified array of items.
     *
     * @param  array  $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
    
        $results = $this->all();
    
        array_forget($results, $keys);
    
        return $results;
    }
    
    /**
     * Retrieve a query string item from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public function query($key = null, $default = null)
    {
        return $this->retrieveItem('query', $key, $default);
    }
    
    /**
     * Determine if a cookie is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasCookie($key)
    {
        return ! is_null($this->cookie($key));
    }
    
    /**
     * Retrieve a cookie from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public function cookie($key = null, $default = null)
    {
        return $this->retrieveItem('cookies', $key, $default);
    }
    
    /**
     * Retrieve a file from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile|array
     */
    public function file($key = null, $default = null)
    {
        return array_get($this->files->all(), $key, $default);
    }
    
    /**
     * Determine if the uploaded data contains a file.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasFile($key)
    {
        if (is_array($file = $this->file($key))) $file = head($file);
    
        return $file instanceof \SplFileInfo && $file->getPath() != '';
    }
    
    /**
     * Retrieve a header from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public function header($key = null, $default = null)
    {
        return $this->retrieveItem('headers', $key, $default);
    }
    
    /**
     * Retrieve a server variable from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public function server($key = null, $default = null)
    {
        return $this->retrieveItem('server', $key, $default);
    }
    
    /**
     * Retrieve a parameter item from a given source.
     *
     * @param  string  $source
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    protected function retrieveItem($source, $key, $default)
    {
        if (is_null($key))
        {
            return $this->$source->all();
        }
        else
        {
            return $this->$source->get($key, $default, true);
        }
    }
    
    
    /**
     * Merge new input into the current request's input array.
     *
     * @param  array  $input
     * @return void
     */
    public function merge(array $input)
    {
        $this->getInputSource()->add($input);
    }
    
    /**
     * Replace the input for the current request.
     *
     * @param  array  $input
     * @return void
     */
    public function replace(array $input)
    {
        $this->getInputSource()->replace($input);
    }
    
    /**
     * Get the JSON payload for the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        if ( ! isset($this->json))
        {
            $this->json = new ParameterBag((array) json_decode($this->getContent(), true));
        }
    
        if (is_null($key)) return $this->json;
    
        return array_get($this->json->all(), $key, $default);
    }
    
    /**
     * Get the input source for the request.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource()
    {
        if ($this->isJson()) return $this->json();
    
        return $this->getMethod() == 'GET' ? $this->query : $this->request;
    }
    
    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        return str_contains($this->header('CONTENT_TYPE'), '/json');
    }
    
    /**
     * Determine if the current request is asking for JSON in return.
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();
    
        return isset($acceptable[0]) && $acceptable[0] == 'application/json';
    }
    
    /**
     * Get the data format expected in the response.
     *
     * @param  string  $default
     * @return string
     */
    public function format($default = 'html')
    {
        foreach ($this->getAcceptableContentTypes() as $type)
        {
            if ($format = $this->getFormat($type)) return $format;
        }
    
        return $default;
    }
    
    public function getBrowser() {
        $agent= $this->getServer('HTTP_USER_AGENT');
        $browser= 'others';
        $browser_ver= '-';
    
        if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)){
            $browser='Safari';
            $browser_ver=$regs[1];
        }
        if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)){
            $browser='IE';
            $browser_ver= $regs[1];
        }
        if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)){
            $browser='Opera';
            $browser_ver=$regs[1];
        }
        if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)){
            $browser='FireFox';
            $browser_ver=$regs[1];
        }
        return array(
            'name'=>$browser,
            'ver'=>$browser_ver,
        );
    }
    
    /**
     * Returns the ip是否在有效段内, 临时放在此类中
     *
     * @param string $ip
     * @param string $range
     * @return array The client IP addresses
     */
    static function ipInRange($ip, $range)
    {
        if($ip === $range) {
            return true;
        }
        if (strpos($range, '/') !== false) {
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
            } else {
                $x = explode('.', $range);
                while(count($x)<4) $x[] = '0';
                list($a,$b,$c,$d) = $x;
                $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);
                $wildcard_dec = pow(2, (32-$netmask)) - 1;
                $netmask_dec = ~ $wildcard_dec;
    
                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else {
            if (strpos($range, '*') !==false) {
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }
    
            if (strpos($range, '-')!==false) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float)sprintf("%u",ip2long($lower));
                $upper_dec = (float)sprintf("%u",ip2long($upper));
                $ip_dec = (float)sprintf("%u",ip2long($ip));
                return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
            }
            return false;
        }
    
    }
    
    /*
     * set app name
     * @param string $value
     * @return self
     */
    public function set_app_name($value)
    {
        $this->_app = $value;
        return $this;
    }//End Function
    
    /*
     * set ctl name
     * @param string $value
     * @return self
     */
    public function set_ctl_name($value)
    {
        $this->_ctl = $value;
        return $this;
    }//End Function
    
    /*
     * set act name
     * @param string $value
     * @return self
     */
    public function set_act_name($value)
    {
        $this->_act = $value;
        return $this;
    }//End Function
    
    /*
     * get ctl name
     * @return string
     */
    public function get_app_name()
    {
        return $this->_app;
    }//End Function
    
    /*
     * get ctl name
     * @return string
     */
    public function get_ctl_name()
    {
        return $this->_ctl;
    }//End Function
    
    /*
     * get act name
     * @return string
     */
    public function get_act_name()
    {
        return $this->_act;
    }//End Function
    
    /*
     * get all exists param by $key
     * @param string $key
     * @param boolean $ext
     * @param string $def
     * @return self
     */
    public function get_param($key)
    {
        if (array_key_exists($key, $this->_paramas))
        {
            return $this->_params[$key];
        }
        return $def;
    }//End Function
    
    /*
     * set params
     * @param string $key
     * @param mixed $val
     * @return self
     */
    public function set_param($key, $val)
    {
        if($val==null){
            return $this->del_param($key);
        }
        $this->_params[$key] = $val;
        return $this;
    }//End Function
    
    /*
     * get all params
     * @param boolean $ext
     * @return array
     */
    public function get_params()
    {
        return (array)$this->_params;
    }//End Function
    
    /*
     * set params
     * @param array $arr
     * @return self
     */
    public function set_params( $arr)
    {
        $this->_params = (array)$this->_params + (array) $arr;
        foreach($this->_params AS $key=>$val){
            if($val == null)
                $this->del_param($key);
        }
        return $this;
    }//End Function
    
    public function clear_params() {
        $this->_params = array();
    }
    
    /*
     * delete param by $key
     * @param string $key
     * @return self
     */
    public function del_param($key)
    {
        if(isset($this->_params[$key]))
            unset($this->_params[$key]);
        return $this;
    }//End Function
    
}
