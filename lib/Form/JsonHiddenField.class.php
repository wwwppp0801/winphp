<?php
namespace Form;
use Form\Field;

class JsonHiddenField extends Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new=false){
        $data=json_decode($this->value(),true);
        $html="";

        if($data && $data["type"] == "hidden"){
            $value = $data["default"];
        }else{
            $value = "";
        }

        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>";

        $html.="<input class='json-hidden span6' ".($this->config['readonly']&&($this->config['default']||!$is_new&&strlen(trim($value))!=0)?'readonly':"")." type='text'  value='".$value."' placeholder='输入默认值'>";
        $html.="<input type='hidden' name='{$this->name}'  value='{\"type\":\"hidden\",\"default\":\"".$value."\"}'>";


        $html.='</div>';
        $html.='</div>';

       
        return $html;
    }

    public function foot_js(){
        $__js=<<<EOF
<script>
(function(){
    if(window.__init_json_hidden_field){
        return;
    }
    window.__init_json_hidden_field=true;

    $('.json-hidden').each(function(){
        var _this = $(this);
        _this.parents('form').on('submit', function(){
            _this.next('input').val(JSON.stringify({type:'hidden', default:_this.val()}));
        });
    });
})();
</script>
EOF;
        return $__js;
    }


}



