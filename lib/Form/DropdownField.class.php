<?php
namespace Form;
use Utils;
class DropdownField extends Field{
    protected $choices;
    public function __construct($config){
        parent::__construct($config);
        if(!isset($config['choices'])){
            throw new Exception("field {$this->name} need set choices");
        }
        if(Utils::is_assoc($config['choices'])){
            $this->choices=[];
            foreach($config['choices']as $k=>$v){
                $this->choices[]=[$k,$v];
            }
        }else{
            $this->choices=$config['choices'];
        }
    }

    public function to_html($is_new){
        $value = $this->value?$this->value:$this->config['default'];
        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>";
        $html.="<div class='controls'>";
        $html.=     '<div class="dropdown clearfix btn-group">'.
                        "<input type='hidden' value='{$this->value}' name='{$this->name}'/>".
                        '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';


        $li = '';
        $checked='';
        foreach($this->choices as $choice){
            $value=$choice[0];
            $display=isset($choice[1])?$choice[1]:$value;
            if($value==$this->value||$value==$this->config['default']){
                $checked = $display;
            }
            
            $li .='<li><a href="javascript:void(0);" value="'.$value.'">'.$display.'</a></li>';
        }

 
        $html.=             $checked;       
        $html.=             '<span class="caret"></span>'.
                        '</button>'.
                        '<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';       
        $html.=          $li;
        $html.=         '</ul>';
        $html.=     '</div>';

        if($this->error){
            $html.="<span class='error'>".$this->error."</span>";
        }
        $html.="</div>";
        $html.="</div>";
        return $html;
    }

    public function foot_js(){
        $js=<<<EOF
<script>
(function(){
    if(window.__init_dropdown_field){
        return;
    }
    window.__init_dropdown_field=true;

    $(document).delegate(".dropdown li a",'click',function(){
        var _this=$(this);
        _this.parent().parent().parent().find('input[type=hidden]').val(_this.attr('value'));
        _this.parent().parent().parent().find('.dropdown-toggle').html(_this.html()+'<span class="caret"></span>');
    });
})();
</script>
EOF;
        return $js;
    }
}
