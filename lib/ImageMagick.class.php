<?php
class ImageMagick{
    public static $convert="/usr/bin/convert";
    public static function lowerQuality($file_path,$type=null){
        $ret=system(self::$convert." -quality 75% -strip ".($type?"$type:":"")."$file_path $file_path.tmp",$status);
        if($ret!==false&&$status==0){
            rename("$file_path.tmp",$file_path);
        }else{
            Logger::error("lower jpeg quality fail:$file_path");
            return false;
        }
    }

    public static function resize($file_path,$to_path,$width=360,$type=null){
        $cmd=self::$convert." -quality 75% -strip -resize $width ".($type?"$type:":"")."$file_path ".($type?"$type:":"")."$to_path";
        $ret=system($cmd,$status);
        if($ret!==false&&$status==0){
            //rename("$file_path.tmp",$file_path);
        }else{
            Logger::error("resize fail:$file_path,cmd:$cmd");
            return false;
        }
    }
    public static function size($file){
        $webroot=ROOT_PATH."/webroot/";
        if(file_exists($webroot.$file)){
            $size=getimagesize($webroot.$file);
            if($size && isset($size[0]) && isset($size[1])){
                return ['width'=>$size[0],'height'=>$size[1]];
            }
        }
        return ['width'=>0,'height'=>0];
    }

}
