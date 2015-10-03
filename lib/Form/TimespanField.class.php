<?php
namespace Form;
use Form\Field;

class TimespanField extends Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $html="<div class='control-group'>";
        $value=$this->value?htmlspecialchars($this->value):1;
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
                '<input size="16" class="span4 timespan" type="text" value="'.($value/3600).'" '.((isset($this->config['readonly']) && $this->config['readonly'])?'readonly':'').'><input size="16" name='.$this->name.'  type="hidden" value="'.$value.'">';

        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
    
    public function foot_js(){
        $js=<<<EOF
<script>
(function(){

  function init(){
    (function(controlType){
        $('input.'+controlType).each(function(i,elem){
            var timespan=$(elem);
            var input = timespan.next('input');
            timespan.parents("form").submit(function(e){
                var v = parseInt(timespan.val()) * 60 * 60;
                if(!isNaN(v)){
                    input.val(v);
                }
            });
        });
    })('timespan');
  }

  init();
})();
</script>
EOF;
        return $js;
    }
}
