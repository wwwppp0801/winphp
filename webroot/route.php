<?php
var_dump($_SERVER);
exit();
define('ROOT_PATH', dirname(dirname(__FILE__)));
require (ROOT_PATH."/config/classpath.php");
require (ROOT_PATH."/config/conf.php");

Soso_Logger::open(LOG_PATH);
try
{
    $mapper = new UrlMapper(WinRequest::getParameter($_SERVER['SCRIPT_NAME']));
    WinRequest::setAttribute("mapper", $mapper);
    $controller = $mapper->getController();
   
    $output = $controller->process();
}
catch(SystemException $e)
{
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
    if ($IS_DEBUG)
    {
        echo $e;
    }
}
print $output;

Soso_Logger::close();

