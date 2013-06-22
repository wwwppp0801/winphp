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

        "Utils"=>"lib/Utils.class.php",
        "DB"=>"lib/DB.class.php",
        "DBTable"=>"lib/DBTable.class.php",
        "DBModel"=>"lib/DBModel.class.php",
        "IPUtils"=>"lib/IPUtils.class.php",
        "Soso_Logger"=>"lib/Logger.class.php",
        'Memcache_Pool'=>"lib/Memcache_Pool.class.php",
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
            include_once (ROOT_PATH.'/'.$file);
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
        else if (preg_match("/(Model|Manager)$/", $classname))
        {
            $path = preg_replace('/([a-z])([A-Z])/', '$1/$2', $classname);
            $path = explode("/", $path);
            $path = array_map("strtolower", $path);
            array_pop($path);
            $path = implode("/", $path);
            $classFile = ROOT_PATH."/app/model/$path/$classname.class.php";
			if (file_exists($classFile))
            {
                include_once ($classFile);
            }
        }
        else if (preg_match("/Interceptor/", $classname))
        {
            $classFile = ROOT_PATH."/app/interceptor/$classname.class.php";
            if (file_exists($classFile))
            {
                include_once ($classFile);
            }
        }
    }
}
spl_autoload_register("__autoload");

