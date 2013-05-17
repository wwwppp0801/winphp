<?php 
global $IS_DEBUG;
if (file_exists(ROOT_PATH.'/DEBUG'))
{
    $IS_DEBUG = true;
    ini_set('track_errors', true);
    ini_set("display_errors", "On");
    ini_set('error_reporting', E_ALL & ~E_NOTICE);
    Soso_Logger::setLevel(3);
}
else
{
    $IS_DEBUG=false;
    Soso_Logger::setLevel(1);
}
date_default_timezone_set('Asia/Shanghai');


DB::init("mysql:yangleduo;host=localhost",'root','');
define("LOG_PATH", ROOT_PATH."/log/");
define("IS_DEBUG", $IS_DEBUG);
define("VERSION", 1);

define("APPKEY", "0cc556d6aebf015ed5fb170769b2ecf7");
define("APPID", "100444400");
define("DOMAIN_NAME", "yakult.aoxpro.com");

