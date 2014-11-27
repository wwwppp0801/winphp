<?php

class Form_SimpleFileField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group simple_file'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            "<input class='$class span6' type='text' name='{$this->name}'  value='".htmlspecialchars($this->value)."'>";
        $html.='</div>';
        $html.='<div class="controls"><iframe height="40" class="span6" scrolling="yes" frameborder="0"></iframe></div>';
        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        return $html;
    }
    public function head_css(){
        $css=<<<EOF
    <style>
        #popup .content iframe{width:1000px;height:768px;}
        .b-close{background:#fff;display:block;}
        .b-close span{float:right;width:20px;display:block;background:#000;color:#fff;text-align:center;cursor:pointer;}
    </style>
EOF;
        return $css;
    }
    public function foot_js(){
        $js=<<<EOF
<script>


window.__init_simple_file=false;
use('jquery_form',function(){
    if(window.__init_simple_file){
        return;
    }
    var container;
    $(".simple_file").find("iframe").each(function(i,e){
        var iframe=$(this);
        iframe.load(function(){
            iframe.contents().find("form").submit(function(e){
                e.stopPropagation();
                e.preventDefault();
                container=iframe.parents(".simple_file");
                $(this).ajaxSubmit({
                    dataType:"json",
                    success:function(data){
                        if(data.errno!=0){
                            alert("上传失败");
                        }
                        var _imgs=data.rst;
                        container.find("input").val(_imgs[0]);
                    }
                });
                return false;
            });
        });
        iframe.attr("src","/winphp/simple_json_files_upload.html?"+Math.random());
    });

});
</script>
EOF;
        return $js;
    }
}


