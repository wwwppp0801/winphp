<?php
namespace Form;
use Form\Field;

class JsonPromptField extends Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new=false){
        $data=json_decode($this->value(),true);
        
        $html="";
        if($data && $data["type"] == "text"){
            $html .="<input type='hidden' name='{$this->name}'  value='".$this->value."'>";
        }else{
            $html .="<input type='hidden' name='{$this->name}'  value='{\"type\":\"prompt\"}'>";
        }
        
        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        return $html;
    }
}



