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

define("LOG_PATH", ROOT_PATH."/log");
define("IS_DEBUG", $IS_DEBUG);

