<?php

class Form_HiddenField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $html="<input type='hidden' name='{$this->name}'  value='".htmlspecialchars($this->value)."'>";
        return $html;
    }
}

