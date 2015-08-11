<?php
namespace Form;
use Form\Field;

class JsonField extends Field{
    public function __construct($config){
        parent::__construct($config);
        $this->supportType = ['text' => '文本', 'choice' => '单选', 'pricelist' => '价格项目', 'datetime'=>'时间', 'hidden'=>'隐藏'];
        //$this->supportType = ['text' => '文本', 'choice' => '单选'];

        foreach($this->supportType as $k => $v){
            $fieldClass ="Form\\Json". ucfirst($k) . "Field" ;
            $this->__fields[$k] = new $fieldClass(array("name"=>"json_".$k,"label"=>"",'required'=>false));
        }
    }

    public function getChoices(){
        $arr = [];
        foreach($this->supportType as $k=>$v){
            $arr[] = [$k, $v];     
        }

        return $arr;
    }

    public function getField(){
        $fieldClass ="Form\\Json". ucfirst($this->getFieldType()) . "Field" ;
        $data = array_merge($this->config,array('default'=>$this->value(), "label"=>""));
        $__field =new $fieldClass($data);
        return $__field;
    }

    public function getFieldType(){
        $data=json_decode($this->value(),true);
        if($data && $this->supportType[$data['type']]){
            return $data['type'];
        }
        return "text";   
    }

    public function to_html($is_new){
        $_field = $this->getField();
        $fieldType = $this->getFieldType();

//        if(isset($field)){
//            return $field->to_html();
//        }else{//create
            $data=json_decode($this->value(),true);
            $choice = new ChoiceField(array("name"=>"json_type_choice".rand(),"label"=>"{$this->label}",'type'=>'choice','choices'=>$this->getChoices(),'default'=>$fieldType,'required'=>false)); 
            $html ="<div class='json-field'><input name='{$this->name}' type='hidden' data-name='{$this->name}' value='{\"type\":\"text\"}'>". $choice->to_html($is_new);
            
            $html .='<div>';
            foreach($this->__fields as $k=>$field){
                if($fieldType == $k){
                    $html .= '<div class="type-wrap" style="display:block;" data-type="'.$k.'">'. $_field->to_html() . '</div>'; 
                }else{
                    $html .= '<div class="type-wrap" data-type="'.$k.'">'. $field->to_html() . '</div>'; 
                }
            }
            $html .="</div></div>";
//        }
        
        return $html;
    }
    public function head_css(){
        $field;// = $this->getField();
        $css = '';
        if(isset($field)){
            $css = $field->head_css();
        }else{
            foreach($this->__fields as $k=>$field){
                $css .= $field->head_css();
            }
        }

        $__css=<<<EOF
<style>
    .type-wrap{
        display:none;
    }
</style>
EOF;

        return $css.$__css;
    }
    
    public function foot_js(){
        $field;// = $this->getField();
        $js = '';
        if(isset($field)){
            $js = $field->foot_js();
        }else{
            foreach($this->__fields as $k=>$field){
                $js .= $field->foot_js();
            }
        }

        $__js=<<<EOF
<script>
(function(){
    if(window.__init_json_field){
        return;
    }
    window.__init_json_field=true;

    $(document).delegate(".json-field input[type=radio]",'change',function(){
        var _this = $(this);
        var wrap = _this.parent().parent().parent().parent().parent();
        wrap.next().children().hide();

        var name = wrap.prev().attr('name','').attr('data-name');

        var cur = wrap.next().find('div[data-type='+_this.val()+']').show();

        var input = cur.find('input[type=hidden]');
        if(!input.attr('data-name')){
            input.attr('data-name', input.attr('name'));
        }
        
        wrap.next().find('input[type=hidden]').each(function(){
            var _this = $(this); 
            var name='';
            if(name = _this.attr('data-name')){
                _this.attr('name', name);
            }
        });

        input.attr('name',name);
    });
})();
</script>
EOF;

        return $__js.$js;
    }
}



