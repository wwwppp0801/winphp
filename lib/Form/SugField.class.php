<?php

class Form_SugField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $value=htmlspecialchars($this->value, ENT_QUOTES);
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            "<input class='$class span6 sug' autocomplete='off' ".($this->config['readonly']&&strlen(trim($value))!=0?'readonly':"")." type='text' name='{$this->name}'  value='".$value."'>";
        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
    public function head_css(){
        $css=<<<EOF
<style>
    #tipsContainer{position:absolute;background:#fff;border:1px solid #ccc;}
    #tipsContainer div{padding:0 0 0 8px;}
    #tipsContainer .focus{background:#bbb;}
</style>
EOF;
        return $css;
    }
    public function foot_js(){
        $url=$this->config['url'];
        $js=<<<EOF
<script>
use("suggest",function(){
    if(window.__init_suggest_field){
        return;
    }
    window.__init_suggest_field=true;
    var tipsContainer=$("<div id='tipsContainer'/>").prependTo("body");
    var input=$(".sug").each(function(i,e){
        $(e).smartbox({
            tipsContainer:tipsContainer,
            url:"$url"
        });
    });
    input.on("smartbox.beforeShow",function(){
        var offset=$(this).offset();
        offset.top+=$(this).height()+10;
        tipsContainer.offset(offset);
        tipsContainer.width($(this).width()+10);
    });
});
</script>
EOF;
        return $js;
    }
}

