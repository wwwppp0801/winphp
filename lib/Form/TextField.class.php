<?php

class Form_TextField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html(){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $value=htmlspecialchars($this->value);
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            "<input class='$class span6' ".($this->config['readonly']&&strlen(trim(value))!=0?'readonly':"")." type='text' name='{$this->name}'  value='".$value."'>";
        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
}
