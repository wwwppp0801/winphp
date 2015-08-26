<?php
namespace Form;
use Utils;
class PeopleField extends Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new=false){
        $html="";
        $value = json_decode($this->value(), true);
        $def = ['name'=>'', 'phone'=>'', 'address'=>'', 'ID_card'=>'', 'info'=>''];
        $fields = $value?$value['fields']:$def;
        $defaultValue = $this->value()?$this->value():json_encode(["type"=>"people", "fields"=>$def]);

        $html .="<div class='people-control' data-group='{$this->data_group}'>";
        $html .=  "<div class='control-group'>";
        $html .=    "<label class='control-label'>".htmlspecialchars($this->label)."</label>";
        $html .=    "<div class='controls controls-row'>";
        foreach(["name"=>"姓名","phone"=>"电话","address"=>"地址"] as $field=>$holder){
            $html .=   "<input type='text' class='span2 {$field}' data-name='{$field}' placeholder='{$holder}' value='".$fields[$field]."'/>";
        }

        $html .=    "</div>";
        $html .=  "</div>";

        $html .=  "<div class='control-group'>";
        $html .=    "<div class='controls controls-row'>";
        foreach(["ID_card"=>"身份证号","info"=>"备注"] as $field=>$holder){
            $html .=   "<input type='text' class='span3 {$field}' data-name='{$field}' placeholder='{$holder}' value='".$fields[$field]."'/>";
        }
        $html .=    "</div>";
        $html .=  "</div>";

        $html .= "<input type='hidden' name='{$this->name}' value='{$defaultValue}'></div>";

        if($this->error){
            $html.="<span class='error'>".$this->error."</span>";
        }
        return $html;
    }

    public function head_css(){
        $css=<<<EOF
<style>

</style>
EOF;
        return $css;
    }
    
    public function foot_js(){
        $js=<<<EOF
<script>
(function(){
    if(window.__init_people_field){
        return;
    }
    window.__init_people_field=true;

    (function(control){
        $("."+control).each(function(){
            var _this = $(this);
            _this.parents('form').on('submit', function(){
                var obj = {'type':'people', fields:{}, value:""};
                var text = [];
                _this.find('input[type=text]').each(function(){
                    obj.fields[$(this).attr('data-name')] = this.value;
                    text.push(this.value);
                });
                obj.value = text.join(' | ');
                _this.find('input[type=hidden]').val(JSON.stringify(obj));
            });    
        });
    })("people-control");

})();
</script>
EOF;
        return $js;
    }
}
