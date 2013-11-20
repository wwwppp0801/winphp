<?php
class Form{
    private $fields;
    private $values;
    private $is_bind=false;
    private $is_validate=null;
    private $errors=array();

    public function __construct($fields=array()){
        $this->fields=array();
        foreach($fields as $field){
            if(is_array($field)){
                $fieldClass=$this->get_field_class($field['type']);
                unset($field['type']);
                $this->fields[]=new $fieldClass($field);
            }elseif(is_subclass_of($field,"Form_Field")){
                $this->fields[]=$field;
            }
        }
    }
    public function clear(){
        $this->is_bind=false;
        $this->is_validate=false;
        $this->errors=array();
        $this->raw_values=false;
        $this->values=false;
        foreach($this->fields as $field){
            $field->clear();
        }
    }
    private function get_field_class($type){
        switch($type){
            case "radio":
                return "Form_ChoiceField";
            default:
                return "Form_".ucfirst($type)."Field";
        }
    }
    public function bind($values=null){
        $this->is_bind=true;
        $this->raw_values=$values;
        return $this->validate();
    }
    public function validate(){
        $this->is_validate=null;
        $this->values=array();
        foreach($this->fields as $field){
            if($field->validate($this->raw_values)===false){
                $this->errors[$field->name()]=$field->error();
            }else{
                $this->values[$field->name()]=$field->value();
            }
        }
        if(!$this->errors){
            $this->is_validate=true;
        }
        return $this->is_validate;
    }
    public function getConfig(){
        return $this->config;
    }
    public function is_validate(){
        return $this->is_validate;

    }
    public function values(){
        if($this->is_validate()){
            return $this->values;
        }
    }
    public function value($name){
        if($this->values){
            return $this->values[$name];
        }
    }
    public function to_html(){
        $html='';
        foreach($this->fields as $field){
            $html.=$field->to_html();
        }
        return $html;
    }
    public function errors(){
        return $this->errors;
    }
}
