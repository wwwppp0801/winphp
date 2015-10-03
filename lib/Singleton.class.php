<?php
trait Singleton {
    private static $_instance;
    public static function getInstance(){
        if(!self::$_instance){
            self::$_instance=new self();
        }
        return self::$_instance;
    }
}
