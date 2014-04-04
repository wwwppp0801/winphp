<?php
class BaseTestCase extends PHPUnit_Framework_TestCase{

    public function __construct(){
        parent::__construct();
        
        static $loaded=false;
        if(!$loaded){
            $loaded=true;
            define('ROOT_PATH', dirname(dirname(__FILE__)));
            require (ROOT_PATH."/config/classpath.php");
            require (ROOT_PATH."/config/conf.php");
            Logger::setLevel(4);
        }
    }
}
