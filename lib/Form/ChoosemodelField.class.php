<?php

class Form_ChoosemodelField extends Form_Field{
    private $model;
    public function __construct($config){
        parent::__construct($config);
        if(!class_exists($config['model'])){
            throw new ModelAndViewException("text:no this model:{$config['model']}",1,"text:no this model:{$config['model']}");
        }
        $this->model=$config['model'];
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
//                                            '<div class="input-append date date-picker" data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years">'.
                                                '<input name="'.$this->name.'"  type="text" value="'.$this->value.'" readonly class="span6 choosemodel'.($this->config['readonly']&&!$is_new?" readonly":"").'" model="'.$this->model.'">';
//                                                '<span class="add-on"><i class="icon-calendar"></i></span>'.
//                                            '</div>';
            //"<input class='date-input $class' type='hidden' name='{$this->name}'  value='".htmlspecialchars($this->value)."'>";
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
    #popup .content iframe{width:1000px;height:768px;overflow:auto;}
    .b-close{background:#fff;display:block;}
    .b-close span{float:right;width:20px;display:block;background:#000;color:#fff;text-align:center;cursor:pointer;}
</style>
EOF;
        return $css;
    }
    public function foot_js(){
        $js=<<<EOF
<script>
use("popup",function(){
    if(window.__init_choosemodel_field){
        return;
    }
    window.__init_choosemodel_field=true;
    $(".choosemodel").click(function(){
        if($(this).hasClass('readonly')){
            return;
        }
        var model=$(this).attr("model");
        var field=$(this).attr("name");
        window.choosemodelPopup=$('#popup').find('.content').html('').end().bPopup({
            content:'iframe', //'ajax', 'iframe' or 'image'
            contentContainer:'.content',
            iframeAttr:'scrolling="yes" frameborder="0"',
            loadUrl:'{%\$__controller->getUrlPrefix()%}/'+encodeURIComponent(model)+'?action=select&field='+encodeURIComponent(field) //Uses jQuery.load()
        });
        return false;
    
    });
    window.choosemodel=function(model,field,id){
        $(document.forms.main).find('[name="'+field+'"]').val(id);
    };
});
</script>
EOF;
        return $js;
    }
}

