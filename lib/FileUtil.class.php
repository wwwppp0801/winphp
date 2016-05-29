<?php
//data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAEoAAAAoCAYAAABZ7GwgAAAE40lEQVR4nO2ZPZajOBSFv54zOcoqRBkhoXYAO4AdsCh2IHYg70ChQzI5dCavoCeAskEIW67p6j5zhhtVgX4v7z1dXf/4+fPnTw68xF9/egH/FRxEJeIgKhEHUYk4iErEQVQi/k5p5EzP6ZI4Yl7R1XL73Fv04JBNixIv5vIlTatYN/NYbfloa+TUEH1VtKvBPFYbqJ/PMU9EPxbxtUaQRJSsO7rNPD1j0bE3zx6556HnvHywJNYZTr6kkg5ji4mE+4bEahx/9ZFJLU7WtGL7KoS/ejKR0HBGElGRabj6nEI+b5VXE5HOaK7q8ZW91diPNiDZYayYI6ngqg22aFFPxhcfYtO/bgUpkeX9DXbexZBMlLea4XxbP+z71b9Z2axSwVtNf5r7XIJIoqe/9/GYfqTo6nntAlVL9GD4qMKVOEx/4hKMobxF1O26v7EUmxSG6UPDDU8qW29FVEjEamqrMcEzoVpa+TqinDGQwykkPssYx3A+Sd0193olrMY4w+l2i3wMsE5ty4MfcbeMjBGHJHwdwxdTLwUZjxJw29SmfBEpsm4RVsOi5t3JxNCPwdB+xIkCBTg/9Y9+P2forcXLdVT50UFZU2Mw1iNfVv43ibqdB/rwky2QlfHnscPgFby/wcfuS0ShmFJI8CE8Vg+ElQFyqi5MPYc9g2wEQihEb3GqfhlV35R6Uw2YJMGZzfpnfBb7xSiLDedUNeAiHWUB/VSfyCs64EpGuZIek5wI4cwJXzZMh6hElZbBuJcy4ZtSz+ORKKlQXRGcQBMZTjYRaSFQbTefdB6rNde6paun/x+Q1F01FXV/xScWZG/1pNEWUkOomlIPaLsfBPBNqeet5SIU9bQUVFtjdU9/A8gomy5B68xkR9tNJx9VR3PVDAbKSB2EnM9SOOm6WCoKVNuAHuj9jlgGfqQad1Nq1S9Sr6ZVzCdSwXg/xpdptjzeeQhOZ+gDhZqVDW0xPtI3K2nagnGjkWK66aHk2VX7a+zfCt4g6v+O41KciIOoRBxEJeIgKhG/jyhn0Nb/tul+Nd4/9QLDK2aZvGX0ZY/jeNfjetNk+w4kELV3j9pBtqNXoo5k2GRL1F0oNgohxObeFtVk0TXnVN3iTre5XoVXoDUSlPnyWkFSRMUXAnBZKfvtXW+BWYBmZbNxN5fv86qjk49n2gpa9WJ8Z+hPnrLpHsR4ix56/E6fL3rml8Czmy+osI6owOZdRpS3mu2VFe7RICq66ZIXWxFmJmm1KVnTymmMJ7u5911Fj1C0lac/GVy3dROSPfMmvMKEqbT632HmaFoTuo6orPQgl6uFTQTH4EYuWUkT7iYFz/rKghzLNbKsf3kpDjf+uT1J3QUOVJTYzz8XEXtZRGcEedWxF2chLqfFWHu/Dq0gENkN79k4xF/3o1I3vl76lmhX0rSTsbcs5ru1D+IeVQRPa+Auls7sA9/iR0UdzaepuoVQCvp4vUAW5CfL6NXr3++ifU9xL91ZzjdBFRnzl6YeecEUs8GxvdP+kaoxSOrmiu4NbMianclBw/JIv5P/bBcSVWYMJ41d9vUWPRd5Gen1dZslQRc9bR/p/5l66qoZzmLWPTPpsRoTelhPdVSgk97UUUlERX/T20Hoqy/r1fOa8dhc1Jtf6KY/IdAP4y4Rh3uQiIOoRBxEJeIgKhH/ADziCoE3jGT8AAAAAElFTkSuQmCC
class FileUtil{
    public static function getPublicImgUri(){
        //return BASE_URL.PUBLIC_IMAGE_URI;
        return BASE_URL;
    }
    public static function uploadFile($file,$base_dir,$allow_ext,$url_prefix=""){
        if(is_string($file)&&preg_match("/^data:/",$file)){
            return [self::_uploadDataURI($file,$base_dir,$allow_ext,$url_prefix)];
        }
        if(is_array($file['error'])){
            ///同一个表单名字下上传了多个文件，要分开存
            $ret=[];
            foreach ($file["error"] as $key => $error) {
                $tmp_file=[];
                foreach($file as $field => $arr){
                    $tmp_file[$field]=$arr[$key];
                }
                $ret[]=self::_uploadFile($tmp_file,$base_dir,$allow_ext,$url_prefix);
            }
            return $ret;
        }else{
            return [self::_uploadFile($file,$base_dir,$allow_ext,$url_prefix)];
        }
    }
    private static function _uploadDataURI($file,$base_dir,$allow_ext=null,$url_prefix=""){
        preg_match("/^data:image\/([a-z]+);base64,/",$file,$matches);
        $ext=$matches[1];
        if(!$ext){
            throw new ModelAndViewException("ext not exists",1,"json:",AppUtils::returnValue(['msg'=>"ext not exists"],99999));
        }
        $data=substr($file,strlen($matches[0]));
        //var_dump($data);
        $data=base64_decode($data);
        if($allow_ext){
            if(!in_array($ext,$allow_ext)){
                throw new ModelAndViewException("ext not allow: $ext",1,"json:",AppUtils::returnValue(['msg'=>"ext not allow: $ext"],99999));
            }
        }
        $md5=md5($data);
        $reldir=$md5[0]."/".$md5[1]."/".$md5[2]."/";
        @mkdir($base_dir.$reldir,0777,true);
        $file_path=$base_dir.$reldir.$md5.".$ext";
        @unlink($file_path);
        file_put_contents($file_path,$data);  
        /*
        if($_REQUEST['lowq']){
            $size=getimagesize($file_path);
            if(isset($size['mime'])){
                $type=preg_replace("/^[^\/]*\//","",$size['mime']);
            }else{
                $type=null;
            }
            ImageMagick::lowerQuality($file_path,$type);
        }*/
        $url_prefix="/".trim($url_prefix,"/")."/";
        return $url_prefix.$reldir.$md5.".$ext";
    }
    private static function _uploadFile($file,$base_dir,$allow_ext=null,$url_prefix=""){
        $ret=preg_match('/\.([a-zA-Z0-9]*)$/',$file['name'],$matches);
        if($ret&&$matches[1]){
            $ext=strtolower($matches[1]);
        }
        if($allow_ext){
            if(!in_array($ext,$allow_ext)){
                throw new ModelAndViewException("ext not allow: $ext",1,"json:",AppUtils::returnValue(['msg'=>"ext not allow: $ext"],99999));
            }
        }
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new ModelAndViewException("No file sent.",1,"json:",AppUtils::returnValue(['msg'=>'No file sent.'],99999));
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new ModelAndViewException("Exceeded filesize limit.",1,"json:",AppUtils::returnValue(['msg'=>'Exceeded filesize limit.'],99999));
            default:
                throw new ModelAndViewException("unknown error",1,"json:",AppUtils::returnValue(['msg'=>'upload unknown error'],99999));
        }
        
        $md5=md5_file($file["tmp_name"]);
        $reldir=$md5[0]."/".$md5[1]."/".$md5[2]."/";
        @mkdir($base_dir.$reldir,0777,true);
        $file_path=$base_dir.$reldir.$md5.".$ext";
        @unlink($file_path);
        move_uploaded_file($file["tmp_name"],$file_path);  
        if($_REQUEST['lowq']){
            $size=getimagesize($file_path);
            if(isset($size['mime'])){
                $type=preg_replace("/^[^\/]*\//","",$size['mime']);
            }else{
                $type=null;
            }
            ImageMagick::lowerQuality($file_path,$type);
        }
        $url_prefix="/".trim($url_prefix,"/")."/";
        return $url_prefix.$reldir.$md5.".$ext";
    }
}
