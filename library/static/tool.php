<?php
/**
 * tool.php
 *
 * */
class lib_static_tool{
    
    /**
     * 判断是否是手机访问
     * 
     * @return bool
     * */
    public static function isMobile()
    {
        $user_agent = request::server('HTTP_USER_AGENT');
    
        $mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device)
        {
            if (stristr($user_agent, $device))
            {
                $is_mobile = true;
                break;
            }
        }
        return $is_mobile;
    }
    
    /**
     * 获取客户端IP地址
     * */
    public static function getClientIp()
    {
        $ip = false;
        
        /** LVS接入时，通过QVIA获取真实IP */
        $qvia = request::server('HTTP_QVIA');
        
        if ($qvia) {
        
            $ip = long2ip(hexdec(substr($qvia, 0, 8)));
            $_SERVER['REMOTE_ADDR'] = $ip;
            return $ip;
        }
        
        /** 直接IP */
        if (request::server('HTTP_CLIENT_IP')) {
            $ip = request::server('HTTP_CLIENT_IP');
        }
        
        /** nginx代理直接HTTP_X_REAL_IP */
        if (request::server('HTTP_X_REAL_IP')) {
            $_SERVER['REMOTE_ADDR'] = request::server('HTTP_X_REAL_IP');
        }
        
        /** 代理 */
        if (request::server('HTTP_X_FORWARDED_FOR')) {
        
            $ips = explode (", ", request::server('HTTP_X_FORWARDED_FOR'));
            if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
        
            for ($i = 0; $i < count($ips); $i++) {
                if (!preg_match('/^(?:10|172\.(?:1[6-9]|2\d|3[01])|192\.168)\./', $ips[$i])) {
                    if (version_compare(phpversion(), "5.0.0", ">=")) {
                        if (ip2long($ips[$i]) != false) {
                            $ip = $ips[$i];
                            break;
                        }
                    } else {
                        if (ip2long($ips[$i]) != -1) {
                            $ip = $ips[$i];
                            break;
                        }
                    }
                }
            }
        }
        
        if (!$ip) {
            return request::server('REMOTE_ADDR');
        }
        
        return $ip;
    }
    
    /**
     * Returns the ip是否在有效段内
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
}
