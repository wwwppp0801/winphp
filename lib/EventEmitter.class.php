<?php

trait EventEmitter{
    private $_event_map=[];
    public function trigger($eventName,$args=[]){
        if(!isset($this->_event_map[$eventName])||!$this->_event_map){
            return;
        }
        foreach($this->_event_map[$eventName] as $func){
            if(call_user_func_array($func,$args)===false){
                return false;
            }
        }
    }
    public function on($eventName,$callable){
        if(!is_callable($callable)||!is_string($eventName)){
            return false;
        }
        if(!isset($this->_event_map[$eventName])){
            $this->_event_map[$eventName]=[];
        }
        $this->_event_map[$eventName][]=$callable;
    }
}
