<?php

class Form_RichTextareaField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            "<textarea class='$class span6 richTextarea' name='{$this->name}'>{$this->value}</textarea>";
        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }

    public function foot_js(){
    
        $js=<<<EOF
<script>
(function(){
if(window.__init_rich_textarea){
    return;
}
window.__init_rich_textarea=true;
use('ckeditor',function(){
    use('ckeditor.jquery',function(){
        $("textarea.richTextarea").ckeditor();
    });
});
})();
</script>
EOF;
        return $js;
    }
}
