<?php
if(!defined('ROOT_PATH')){
    define("ROOT_PATH", dirname(dirname(__FILE__)));
}
if (!isset($IS_DEBUG)&&file_exists(ROOT_PATH.'/DEBUG')){
    global $IS_DEBUG;
    $IS_DEBUG=true;
}

function __autoload($classname)
{
    static $classpath = array(
        "UrlMapper"=>"winphp/base/UrlMapper.class.php",
        "SystemException"=>"winphp/Exception.class.php",
        "BizException"=>"winphp/Exception.class.php",
        "ModelAndViewException"=>"winphp/Exception.class.php",
        "Interceptor"=>"winphp/base/Interceptor.class.php",
        "WinRequest"=>"winphp/WinRequest.class.php",
        "BaseController"=>"winphp/base/BaseController.class.php",
        "DefaultView"=>"winphp/base/DefaultView.class.php",
        "DefaultViewSetting"=>"config/DefaultViewSetting.class.php",

    );
    $classpath['Smarty']="lib/Smarty/Smarty.class.php";
    $file = @$classpath[$classname];
    if (! empty($file))
    {
        if ($file[0] == '/')
        {
            include_once ($file);
        }
        else
        {
            include_once (WINPHP_PATH.'/'.$file);
        }
    }
    else
    {
        if (preg_match("/Controller$/", $classname))
        {
            $classFile = ROOT_PATH."/app/controller/$classname.class.php";
            if (file_exists($classFile))
            {
                include_once ($classFile);
            }
        }
        else
        {
            $path = explode("_",$classname);
            $classname=array_pop($path);
            //$path = array_map("strtolower", $path);
            $path = implode("/", $path);
            
            
            $classFile = ROOT_PATH."/app/$path/$classname.class.php";

			if (file_exists($classFile))
            {
                include_once ($classFile);
            }else{
                $classFile = WINPHP_PATH."/lib/$path/$classname.class.php";
                if (file_exists($classFile))
                {
                    include_once ($classFile);
                }
            }
        }
    }
}
spl_autoload_register("__autoload");

