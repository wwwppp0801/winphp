<?php
class Form_PasswordField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html(){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            "<input class='$class span6' type='password' name='{$this->name}'>";
        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
    public function validate(&$values){
        $ret=parent::validate($values);
        if($ret){
            $this->value=$values[$this->name]=md5($values[$this->name]);
        }
        return $ret;
    }
}

