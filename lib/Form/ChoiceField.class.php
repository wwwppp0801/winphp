<?php
namespace Form;
use Utils;
class ChoiceField extends Field{
    protected $choices;
    public function __construct($config){
        parent::__construct($config);
        if(!isset($config['choices'])){
            throw new Exception("field {$this->name} need set choices");
        }
        if(Utils::is_assoc($config['choices'])){
            $this->choices=[];
            foreach($config['choices']as $k=>$v){
                $this->choices[]=[$k,$v];
            }
        }else{
            $this->choices=$config['choices'];
        }
    }

    public function to_html($is_new){
        $html="<div class='control-group' data-group='{$this->data_group}'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>";
        $html.="<div class='controls'>";
        foreach($this->choices as $choice){
            $value=$choice[0];
            $display=isset($choice[1])?$choice[1]:$value;
            $checked=($value==$this->value||$value==$this->config['default'])?"checked='checked'":"";
            if(isset($this->config['readonly']) 
                && $this->config['readonly']) {
                $html.=$checked ? '<input size="16" type="text" value="'.$display.'" readonly /><input size="16" name='.$this->name.' type="hidden" value="'.htmlspecialchars($value).'" readonly />' : '';
            } else {
                $html.="<label class='radio inline'><input type='radio' $checked name='{$this->name}' value='".htmlspecialchars($value)."'>$display</label>";
            }
        }
        $html.=$this->createPrompt();
        $html.=$this->createUserPrompt();

        if($this->error){
            $html.="<span class='error'>".$this->error."</span>";
        }
        $html.="</div>";
        $html.="</div>";
        return $html;
    }
}
