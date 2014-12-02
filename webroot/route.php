<?php
if (isset($_SERVER['REQUEST_URI'])&&preg_match('/(.*\.(php|png|jpg|jpeg|gif|js|css))(?:\?[^?]*)?$/', $_SERVER["REQUEST_URI"],$matches)) {
    if($matches[2]=='php'){
        ini_set('include_path',ini_get("include_path").":".dirname(__FILE__)."/ckfinder/core/connector/php");
        require (dirname(__FILE__).$matches[1]);
        return true;
    }
    
    return false;    // serve the requested resource as-is.
}

define('WINPHP_PATH', dirname(dirname(__FILE__)));
require (WINPHP_PATH."/config/classpath.php");
require (ROOT_PATH."/config/conf.php");

if(php_sapi_name()=='cli'){
    Logger::setLevel(4);
    require(ROOT_PATH.'/script/'.$argv[1]);
    exit();
}

Logger::open(LOG_PATH);
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

Logger::close();

