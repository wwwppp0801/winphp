<?php
class DB{
    public static function init($dsn,$username,$password,$name="root"){
        DBObject::init($dsn,$username,$password,$name);
    }
    public static function getInstance($name="root"){
        return DBObject::getInstance($name);
    }
    public static function __callStatic($name,$args){
        if(function_exists(get_class()."::".$name)){
            return call_user_func_array([self,$name],$args);
        }
        return call_user_func_array([DBObject::getInstance(),$name],$args);
    }
}
