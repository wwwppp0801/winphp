<?php

class Form_JsonFilesField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $arr=json_decode($this->value(),true);
        $arr=$arr?$arr:[];
        $links=array_map(function($a){
            $fi = pathinfo($a);
            if(in_array(strtolower($fi['extension']), ['png','jpg','gif','bmp','jpeg'])) {
                $a = '<li><a target="_blank" href="'.$a.'">'.'<img width="320px" src="'.htmlspecialchars($a).'"></img></a>';
            } else {
                $a = "<li><a target='_blank' href='".$a."'>".htmlspecialchars($a)."</a>";
            }
            return $a."<button type='button' class='close' aria-hidden='true'>&times;</button></li>";
        },$arr);
        $links=implode("\n",$links);
        $links=$links?"<ul>$links</ul>":"";
        $html="<div class='control-group json_files'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            $links.
            '<a class="json_files_open_upload btn" href="javascript:;" class="button">上传</a>'.
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
    .json_files .close{
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
use('ckfinder',function(){
    var finder = new CKFinder();
    finder.basePath = '/upload/';
    var upload_btn;
    finder.selectActionFunction = function(path){
        //$("form img").attr("src",path);
        var link_list;
        link_list=$(upload_btn).prev("ul");
        if(!link_list||link_list.length==0){
            link_list=$("<ul></ul>").insertBefore(upload_btn);
        }
        link_list.append("<li><a target='_blank' href='"+path+"'>"+path+"</a><button type='button' class='close' aria-hidden='true'>&times;</button></li>");
        update_input_value();
    };
    $(document).delegate(".json_files_open_upload",'click',function(){
        upload_btn=$(this);
        finder.popup();
        return false;
    });
    $(".json_files").delegate('ul .close','click',function(){
        upload_btn=$(this).parents(".json_files").find('.json_files_open_upload');
        $(this).parent('li').remove();
        update_input_value();
    });
    function update_input_value(){
        var link_list=$(upload_btn).prev("ul").find("li a");
        var input=$(upload_btn).next("input");
        var links=$.map(link_list,
        function(link){
            return $(link).attr("href");
        });
        input.val(JSON.stringify(links));
    }
});
</script>
EOF;
        return $js;
    }
}


