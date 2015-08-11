<?php
namespace Form;
use Form\Field;

class TextField extends Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group' data-group='{$this->data_group}'>";
        $value=htmlspecialchars($this->value, ENT_QUOTES);
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            "<input class='$class span6' ".($this->config['readonly']&&($this->config['default']||!$is_new&&strlen(trim($value))!=0)?'readonly':"")." type='text' name='{$this->name}'  value='".$value."'>";

        $html.=$this->createPrompt();
        $html.=$this->createUserPrompt();

        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
}
