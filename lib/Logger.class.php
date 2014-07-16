<?php 
class Logger {
    public static function setLevel($level = 1) {
        PLogger::get()->setLevel($level);
    }
    
    public static function open($path = false) {
        PLogger::get()->open($path);
    }
    
    public static function close() {
        PLogger::get()->close();
    }
    public static function error($str) {
        PLogger::get()->error($str);
    }
    
    public static function info($str) {
        PLogger::get()->info($str);
    }
    
    public static function debug($str) {
        PLogger::get()->debug($str);
    }
}
