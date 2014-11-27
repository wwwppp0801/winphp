<?php

class Form_SimpleJsonFilesField extends Form_Field{
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
        $links="<ul>$links</ul>";
        $html="<div class='control-group simple_json_files'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
            $links.
            '<iframe height="40" class="span6" scrolling="yes" frameborder="0"></iframe>'.
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
    .simple_json_files .close{
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
    window.__init_simple_json_files=false;
    use('jquery_form',function(){
        if(window.__init_simple_json_files){
            return;
        }
        window.__init_simple_json_files=true;
        
        var container;
        $(".simple_json_files").find("iframe").each(function(i,e){
            var iframe=$(this);
            iframe.load(function(){
                iframe.contents().find("form").submit(function(e){
                    e.stopPropagation();
                    e.preventDefault();
                    container=iframe.parents(".simple_json_files");
                    $(this).ajaxSubmit({
                        dataType:"json",
                        success:function(data){
                            if(data.errno!=0){
                                alert("上传失败");
                            }
                            var _imgs=data.rst;
                            for(var i=0;i<_imgs.length;i++){
                                container.find("ul").append("<li><a target='_blank' href='"+_imgs[i]+"'>"+_imgs[i]+"</a><button type='button' class='close' aria-hidden='true'>&times;</button></li>");
                            }
                            update_input_value();
                        }
                    });
                    return false;
                });
            });
            iframe.attr("src","/winphp/simple_json_files_upload.html?"+Math.random());
        });


        $(".simple_json_files").delegate('ul .close','click',function(){
            container=$(this).parents(".simple_json_files");
            $(this).parent('li').remove();
            update_input_value();
        });
        function update_input_value(){
            var link_list=container.find("ul li a");
            var input=container.find("input:last");
            var links=$.map(link_list,
            function(link){
                return $(link).attr("href");
            });
            input.val(JSON.stringify(links));
        }
    });
})();
</script>
EOF;
        return $js;
    }
}



