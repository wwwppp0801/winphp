<?php
namespace Form;
use Form\Field;
//只是为了绑定参数，不输出表单项
class NoHtmlField extends Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        return "";
    }
}


