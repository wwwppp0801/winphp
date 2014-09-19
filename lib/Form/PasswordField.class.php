<?php
class Form_PasswordField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            "<input class='$class span5' disabled='disabled' type='password' name='{$this->name}'>".
            "<input class='span1 enable_input' type='button' value='修改'>"
            ;
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
    public function foot_js(){
        $js=<<<EOF
<script>
(function(){
{%if \$__is_new%}
    $('.enable_input').prev("input").removeAttr("disabled");
{%/if%}
    $('.enable_input').click(function(){
        $(this).prev("input").removeAttr("disabled");
    });
})();
</script>
EOF;
        return $js;
    }
}

