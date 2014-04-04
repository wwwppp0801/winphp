<?php

abstract class Form_Field{
    protected $value;
    protected $name;
    protected $required;
    protected $error;
    protected $config;

    public function __construct($config){
        $this->config=$config;

        if(!isset($config['name'])){
            throw new Exception("name is required");
        }
        $this->name=$config['name'];

        if(isset($config['label'])){
            $this->label=$config['label'];
        }else{
            $this->label=$config['name'];
        }

        if(isset($config['required'])){
            $this->required=$config['required'];
        }else{
            $this->required=false;
        }

        if(isset($config['default'])){
            $this->value=$config['default'];
        }
        if (isset($config['validator'])){
            $this->validator=$config['validator'];
        }
    }
    public abstract function to_html();
    public function validate(&$values){
        if($this->required && strlen($values[$this->name])==0){
            $this->error="field '{$this->name}' is required.";
            return false;
        }
        $this->value=$values[$this->name];
        if($this->validator){
            if(is_callable(array($this->validator,"validate")) && $this->validator->validate($values)===false ){
                $this->error=$this->validator->error;
                return false;
            }
            if(is_callable($this->validator)){
                $ret=call_user_func($this->validator,$values);
                if($ret!==true){
                    $this->error=$ret;
                    return false;
                }
            }
        }
        
        
        return true;
    }
    public function clear(){
        if(isset($config['default'])){
            $this->value=$this->config['default'];
        }else{
            $this->value=null;
        }
        $this->error=false;
    }
    
    public function value(){
        return $this->value;
    }
    public function error(){
        return $this->error;
    }
    public function name(){
        return $this->name;
    }
    public function label(){
        return $this->label;
    }
}
