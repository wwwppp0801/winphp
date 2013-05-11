<?php
class Memcache_Pool{
    private static $pool=array();
    public static function getConnection($ip,$port){
        $port=intval($port);
        if(!IPUtils::is_ip($ip)){
            return false;
        }
        $key="{$ip}_{$port}";
        if(!self::$pool[$key]){
            self::$pool[$key]=memcache_connect($ip,$port);
        }
        return self::$pool[$key];
    }

}
