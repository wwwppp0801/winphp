<?php

class Form_DateField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html(){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
//                                            '<div class="input-append date date-picker" data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years">'.
                                                '<input size="16" name='.$this->name.'  type="text" value="'.htmlspecialchars($this->value).'" readonly class="m-wrap m-ctrl-medium datepicker">';
//                                                '<span class="add-on"><i class="icon-calendar"></i></span>'.
//                                            '</div>';
            //"<input class='date-input $class' type='hidden' name='{$this->name}'  value='".htmlspecialchars($this->value)."'>";
        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
}
