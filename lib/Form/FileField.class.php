<?php

class Form_FileField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            "<input class='$class span6' type='text' name='{$this->name}'  value='".htmlspecialchars($this->value)."'>".
            '<a class="open_upload btn" href="javascript:;" class="button">上传</a>';
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
use("ckfinder",function(){
    var finder = new CKFinder();
    finder.basePath = '/upload/';
    var current_upload;
    finder.selectActionFunction = function(path){
        //$("form img").attr("src",path);
        current_upload.val(decodeURIComponent(path));
    };
    $(document).delegate(".open_upload",'click',function(){
        current_upload=$(this).prev("input");
        finder.popup();
        return false;
    });
});
</script>
EOF;
        return $js;
    }
}

