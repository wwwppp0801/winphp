<?php

class Form_JsonArrayField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $arr=json_decode($this->value(),true);
        $arr=$arr?$arr:[];
        $links=array_map(function($a){
            return "<li><a target='_blank' href='javascript:;'>".htmlspecialchars($a)."</a><button type='button' class='close' aria-hidden='true'>&times;</button></li>";
        },$arr);
        $links=implode("\n",$links);
        $links=$links?"<ul>$links</ul>":"<ul></ul>";
        $html="<div class='control-group json_array'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            $links.
            '<input type="text" /><a class="json_array_add btn" href="javascript:;" class="button">添加</a>'.
            "<input type='hidden' name='{$this->name}'  value='".$this->value."'>";
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
    .json_array .close{
        float:none;
        margin:0 0 0 10px;
    }
</style>
EOF;
        return $css;
    }
    
    public function foot_js(){
        $js=<<<EOF
<script>
(function(){
    if(window.__init_json_array_field){
        return;
    }
    window.__init_json_array_field=true;

    var upload_btn;
    $(document).delegate(".json_array_add",'click',function(){
        upload_btn=$(this);
        var input=upload_btn.prev("input");
        var link_list;
        link_list=$(upload_btn).prevAll("ul");
        link_list.append("<li><a target='_blank' href='javascript:;'>"+input.val()+"</a><button type='button' class='close' aria-hidden='true'>&times;</button></li>");
        update_input_value();
        input.val("");
        return false;
    });
    $(".json_array").delegate('ul .close','click',function(){
        upload_btn=$(this).parents(".json_array").find('.json_array_add');
        $(this).parent('li').remove();
        update_input_value();
    });
    function update_input_value(){
        var link_list=$(upload_btn).prevAll("ul").find("li a");
        var input=$(upload_btn).next("input");
        var links=$.map(link_list,
            function(link){
                return $(link).html();
            }
        );
        input.val(JSON.stringify(links));
    }
})();
</script>
EOF;
        return $js;
    }
}



