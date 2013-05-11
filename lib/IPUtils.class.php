<?php
class IPUtils{
    public static function get_host_ips($domain){
        $records=dns_get_record($domain);
        $ips=array();
        foreach ($records as $record){
            if($record['type']=='A'){
                $ips[]=$record['ip'];
            }
        }
        return  $ips;
    }
    public static function is_ip($ip)
    {
        $arr = explode('.', $ip);
        if (count($arr) != 4)
        {
            return false;
        }
        for ($i = 0; $i < 4; $i++)
        {
            if (!is_numeric($arr[$i]) || $arr[$i] < 0 || $arr[$i] > 255)
            {
                return false;
            }
        }
        return true;
    }

    public static function get_ip_seg($ip)
    {
        $arr = explode('.', $ip);
        array_splice($arr, -1, 1);
        return join('.', $arr);
    }
    //use shell to get my ip address
    public static function get_local_ip()
    {
        static $local_ip;
        if (! isset ($local_ip))
        {
            $local_ip = system('/sbin/ifconfig eth1 | grep "inet addr"|awk \'{print $2}\'|sed -e "s/^.*://g"');
            if (!self::is_ip($local_ip))
            {
                $local_ip = system('/sbin/ifconfig eth0 | grep "inet addr"|awk \'{print $2}\'|sed -e "s/^.*://g"');
            }
        }
        return self::is_ip($local_ip)?$local_ip:false;
    }

}
