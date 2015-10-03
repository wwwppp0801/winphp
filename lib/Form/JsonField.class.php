<?php
namespace Form;
use Form\Field;

class JsonField extends Field{
    public function __construct($config){
        parent::__construct($config);
        $this->allSupportType = [
            'text' => '文本', 
            'choice' => '单选', 
            'pricelist' => '价格项目', 
            'datetime'=>'时间', 
            'hidden'=>'隐藏', 
            'people'=>'人物关系',
            'prompt'=>'话术',
        ];

        if(isset($config['supportType'])){
            $support = $config['supportType'];
            $this->supportType = [];
            foreach($this->allSupportType as $k => $v){
                if(in_array($k, $support)) {
                    $this->supportType[$k] = $this->allSupportType[$k] ;
                }
            }
            if(count($this->supportType) == 0){
                throw new Exception("supportType is empty");
            }
        }else{
            $this->supportType = $this->allSupportType; 
        }

        foreach($this->supportType as $k => $v){
            $fieldClass ="Form\\Json". ucfirst($k) . "Field" ;
            $this->__fields[$k] = new $fieldClass(array("name"=>"json_".$k,"label"=>"",'required'=>false));
        }
    }

    public function getAllFields(){
        $fields = [];
        foreach($this->allSupportType as $k => $v){
            $fieldClass ="Form\\Json". ucfirst($k) . "Field" ;
            $fields[$k] = new $fieldClass(array("name"=>"json_".$k,"label"=>"",'required'=>false));
        }

        return $fields;
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
            $html ="<div class='json-field'><input type='hidden' data-name='{$this->name}' value=''>". $choice->to_html($is_new);
            
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
            foreach($this->getAllFields() as $k=>$field){
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
            foreach($this->getAllFields() as $k=>$field){
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

        var name = wrap.prev().attr('data-name');

        var cur = wrap.next().find('div[data-type='+_this.val()+']').show();

        var input = cur.find('input[type=hidden]');
        
        wrap.next().find('input[type=hidden]').each(function(){
            var _this = $(this); 
            _this.attr('name', '');
        });

        input.attr('name',name);
    });
})();
</script>
EOF;

        return $__js.$js;
    }
}



