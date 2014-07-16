<?php

class Form_ChoiceField extends Form_Field{
    protected $choices;
    public function __construct($config){
        parent::__construct($config);
        if(!isset($config['choices'])){
            throw new Exception("field {$this->name} need set choices");
        }
        $this->choices=$config['choices'];
    }

    public function to_html(){
        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>";
        $html.="<div class='controls'>";
        foreach($this->choices as $choice){
            $value=$choice[0];
            $display=isset($choice[1])?$choice[1]:$value;
            $checked=($value==$this->value)?"checked='checked'":"";
            if(isset($this->config['readonly']) 
                && $this->config['readonly']) {
                $html.=$checked ? '<input size="16" type="text" value="'.$display.'" readonly /><input size="16" name='.$this->name.' type="hidden" value="'.htmlspecialchars($value).'" readonly />' : '';
            } else {
                $html.="<label class='radio'><div class='radio'><span><input type='radio' $checked name='{$this->name}' value='".htmlspecialchars($value)."'></span></div>$display</label>";
            }
        }
        if($this->error){
            $html.="<span class='error'>".$this->error."</span>";
        }
        $html.="</div>";
        $html.="</div>";
        return $html;
    }
}
