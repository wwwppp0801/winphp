<?php

class Form_UniqueValidator{
    public function __construct($model,$field_name){
        $this->model=$model;
        $this->field_name=$field_name;
    }
    public function validate($value){
        $this->model->add($this->field_name,$value,Restrictions::EQUAL);
        if($this->model->_count(0)){
            $this->error="$this->field_name 字段的值必须是唯一的！";
            return false;
        }else{
            return true;
        }

    }
}
