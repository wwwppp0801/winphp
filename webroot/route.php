<?php
if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
}

define('ROOT_PATH', dirname(dirname(__FILE__)));
require (ROOT_PATH."/config/classpath.php");
require (ROOT_PATH."/config/conf.php");

if(php_sapi_name()=='cli'){
    Soso_Logger::setLevel(4);
    require(ROOT_PATH.'/script/'.$argv[1]);
    exit();
}

Soso_Logger::open(LOG_PATH);
try
{
    $mapper = new UrlMapper($_SERVER['SCRIPT_NAME']);
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

