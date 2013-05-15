<?php
class Memcache_Pool{
    private static $pool=array();
    public static function getConnection($ip,$port){
        $port=intval($port);
        if(!IPUtils::is_ip($ip)){
            throw new SystemException("ip: $ip:$port, error");
        }
        $key="{$ip}_{$port}";
        if(!self::$pool[$key]){
            $conn = new Memcache();
            $res=$conn->connect($ip,$port);
            if($res){
                self::$pool[$key]=$conn;
            }else{
                throw new SystemException("ip: $ip:$port, can't connect");
            }
        }
        return self::$pool[$key];
    }

}
