<?php
namespace Form;
use Form\Field;

class PromptField extends Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $value=htmlspecialchars($this->value, ENT_QUOTES);
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls just-prompt'>";

        $html.= $this->createPrompt("left");
        $html.=$this->createUserPrompt();

        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
}
