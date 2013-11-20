<?php

class Form_TextAreaField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html(){
        $class=$this->config['class'];
        $html= "<label>".htmlspecialchars($this->label)."</label>".
            "<textarea ".($class?" class='$class' ":"")." name='{$this->name}'>{$this->value}</textarea>";
        if($this->error){
            $html.="<span class='error'>".$this->error."</span>";
        }
        return $html;
    }
}
