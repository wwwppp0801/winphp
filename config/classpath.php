<?php
if(!defined('ROOT_PATH')){
    define("ROOT_PATH", dirname(dirname(__FILE__)));
}
if (!isset($IS_DEBUG)&&file_exists(ROOT_PATH.'/DEBUG')){
    global $IS_DEBUG;
    $IS_DEBUG=true;
}

spl_autoload_register(function ($classname){
    $file = str_replace('\\', '/', $classname);
    $file .= '.class.php';
    if (file_exists(ROOT_PATH."/app/$file")) {
        include_once ROOT_PATH."/app/$file";
    }
    else if (file_exists(ROOT_PATH."/lib/$file")) {
        include_once ROOT_PATH."/lib/$file";
    }
    else if (file_exists(WINPHP_PATH."/lib/$file")) {
        include_once WINPHP_PATH."/lib/$file";
    }
});
