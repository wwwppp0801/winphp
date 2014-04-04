<?php

class Form_UniqueValidator{
    public function __construct($model,$field_name){
        $this->model=$model;
        $this->field_name=$field_name;
    }
    public function validate($value){
        $this->model->addWhere($this->field_name,$value);
        if($this->model->count()){
            $this->error="$this->field_name 字段的值必须是唯一的！";
            return false;
        }else{
            return true;
        }
    }
}
