<?php
namespace Form;
use Form\Field;

class JsonChoiceField extends Field{
    public function __construct($config){
        parent::__construct($config);

        if(isset($config['model'])){
            if(!class_exists($config['model'])){
                throw new ModelAndViewException("text:no this model:{$config['model']}",1,"text:no this model:{$config['model']}");
            }
            $this->model=$config['model'];
            $this->filter=$config['filter'];
            //$this->filter_field=$config['filter_field'];
            $this->field=$config['field'];
        }
    }

    public function to_html($is_new=false){
        $class=$this->config['class'];
        $data=json_decode($this->value(),true);
        $links = "";
        
        $html="<div class='control-group json_array'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>";

        if($data && $data["type"] == "choice"){
            $arr=$data?$data["lists"]:[];
            $links=array_map(function($a){
                return "<li><a target='_blank' href='javascript:;'>".htmlspecialchars($a)."</a><button type='button' class='close' aria-hidden='true'>&times;</button></li>";
            },$arr);
            $links=implode("\n",$links);
        }
        $links=$links?"<ul>$links</ul>":"<ul></ul>";

        $html.= $links.'<input type="text" class="json-choice-add-input" '.($this->model?'readonly field ="'.$this->field.'" filter="'.$this->filter.'" model="'.$this->model.'"':'').'/><a class="json_array_add btn" href="javascript:;" class="button">添加</a>';

        $html .="<input type='hidden' name='{$this->name}'  value='".$this->value."'>";
        
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

    #popup .content iframe{width:1000px;height:768px;overflow:auto;}
    .b-close{background:#fff;display:block;}
    .b-close span{float:right;width:20px;display:block;background:#000;color:#fff;text-align:center;cursor:pointer;}
</style>
EOF;
        return $css;
    }
    
    public function foot_js(){
        $admin_url=$this->config['admin_url'];
        if(!$admin_url){
            $admin_url="/admin";
        }
        $js=<<<EOF
<script>
use("popup",function(){
    if(window.__init_json_choice_field){
        return;
    }
    window.__init_json_choice_field=true;

    var upload_btn;
    $(document).delegate(".json_array_add",'click',function(){
        upload_btn=$(this);
        var input=upload_btn.prev("input");
        if($.trim(input.val()) == ""){
            return;
        }
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
        input.val(JSON.stringify({type:"choice", lists:links}));
    }

    var form;
    //choice model
    $('input.json-choice-add-input').filter(function(_,item){
           return !!$(item).attr('model'); 
    }).click(function(){
        form = $(this).parents('form');
        var model=$(this).attr("model").replace(/\\\\/g,'/');
        var field=$(this).attr("field");
        var filter=$(this).attr('filter');
        //var filter_field=$(this).attr('filter_field');
        var params = encodeURIComponent(filter+'='+encodeURIComponent(form.find('input[name='+filter+']').val()));

        window.choosemodelPopup=$('#popup').find('.content').html('').end().bPopup({
            content:'iframe', //'ajax', 'iframe' or 'image'
            contentContainer:'.content',
            iframeAttr:'scrolling="yes" frameborder="0"',
            loadUrl:'$admin_url/'+encodeURIComponent(model)+'?__action=select&field='+encodeURIComponent(field)+'&__filter='+params //Uses jQuery.load()
        });
        return false;
    });

    window.choosemodelField=function(model,field,value){
        form && form.find('input.json-choice-add-input')
                    .filter(function(_,item){
                        return !!$(item).attr('model'); 
                    }).val(value);
    };

});
</script>
EOF;
        return $js;
    }
}



