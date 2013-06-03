<?php 
class Utils {
    
    public static function toUTF8($str) {
        return mb_convert_encoding($str, 'UTF-8', 'GBK');
    }
	
    public static function toGBK($str) {
        return mb_convert_encoding($str, 'GBK', 'UTF-8');
    }
    
    public static function curlGet($url, $timeout = 1, $headerAry = '') {
        //var_dump($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //output时忽略http响应头
        curl_setopt($ch, CURLOPT_HEADER, false);
        //设置http请求的头部信息 每行是数组中的一项
        //当url中用ip访问时，允许用host指定具体域名
        if ($headerAry != '') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerAry);
        }
        
        $res = curl_exec($ch);
        
        return $res;
    }
    
    public static function curlPost($url, $data, $timeout = 1, $headerAry = '') {
        $ch = curl_init();
        //var_dump($url);
        //var_dump($data);
        //var_dump($headerAry);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //output时忽略http响应头
        curl_setopt($ch, CURLOPT_HEADER, false);
        //设置http请求的头部信息 每行是数组中的一项
        //当url中用ip访问时，允许用host指定具体域名
        if ($headerAry != '') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerAry);
        }
        $res = curl_exec($ch);
        //var_dump($url);
        return $res;
    }

    public static function array2Simple($pArray, $pField){
        $tRet = array();
        foreach ($pArray as $index=>$item) {
            $tRet[] = $item[$pField];
        }   
        return $tRet;
    }

    
    public static function memcacheGet($ip, $port, $key) {
        $memcache_obj = memcache_connect($ip, $port);
        if ($memcache_obj === false) {
            Soso_Logger::error("memcache_connect error. $ip:$port:$key\n");
            return false;
        }
        
        $res = memcache_get($memcache_obj, $key);
        if (false === $res) {
            Soso_Logger::error("memcache_get error. $ip:$port:$key\n");
            memcache_close($memcache_obj);
            return false;
        }
        memcache_close($memcache_obj);
        return $res;
    }
    
    public static function getClientIP() {
        if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif ($_SERVER["HTTP_CLIENT_IP"]) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif ($_SERVER["REMOTE_ADDR"]) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = false;
        }
        $ip = explode(",", $ip, 2);
        $ip = trim($ip[0]);
        return $ip;
    }

}
