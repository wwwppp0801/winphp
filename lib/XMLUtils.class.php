<?php
class XMLUtils
{
    static function entities($string) { 
           return str_replace ( array ( '&', '"', "'", '<', '>', '�' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;', '&apos;' ), $string ); 
    }
    public static function from_array($array,$root="root"){
        $body="";
        if(!is_array($array)){
            $body=self::entities($array);
        }else{
            if(array_keys($array)===range(0,count($array)-1)){
                foreach($array as $k=>$v){
                    $body.=self::from_array($v,$root);
                }
                return $body;
            }else{
                foreach($array as $k=>$v){
                    $body.=self::from_array($v,$k);
                }
            }
        }
        return "<$root>".$body."</$root>";
    }
    public static function to_array($xml,$fix_array=array()) {
        if (empty($xml)) {
            return false;
        }   
        $res = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
//        file_put_contents("/tmp/test.xml",$xml);
        if (false === $res) {
            return false;
        }   
        $array = (array)$res;
        foreach ($array as $key=>$item) {
            $array[$key] = self::struct_to_array($item);
        }
        if(is_string($fix_array)){
            $fix_array=array($fix_array);
        }
        foreach($fix_array as $raw_fix){
            $tmp=&$array;
            foreach(explode(".",$raw_fix) as $field){
                if(!isset($tmp[$field])){
                    break;
                }
                $tmp=&$tmp[$field];
            }
            if(array_keys($tmp)!==range(0,count($tmp)-1)){
                $tmp=array($tmp);
            }
        }
        return $array;
    }   
    private static function struct_to_array($item) {
        if(!is_string($item)) {
            $item = (array)$item;
            foreach ($item as $key=>$val) {
                $item[$key] = self::struct_to_array($val);
            }   
        }
        if(!$item){
            return "";
        }
        return $item;
    }   
}
