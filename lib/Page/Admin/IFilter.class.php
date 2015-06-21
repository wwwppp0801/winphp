<?php

namespace Page\Admin;
use DBModel;

abstract class IFilter{
    abstract public function setFilter(DBModel $model);
    abstract public function toHtml();
    public function getName(){
        if(isset($this->name)){
            return $this->name;
        }
        return get_class($this);
    }
    public function getParamName(){
        return $this->paramName;
    }
    public function __construct($params=[]){
        foreach($params as $name=>$val){
            $this->$name=$val;
        }
    }
}
