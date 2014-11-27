<?php

class Form_DatetimeField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $value=$this->value?htmlspecialchars($this->value):time();
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".(isset($this->config['readonly']) && $this->config['readonly']
            ? '<input size="16" type="text" value="'.date('Y-m-d H:i:s', $value).'" readonly><input size="16" name='.$this->name.'  type="hidden" value="'.$value.'">'
            : '<input size="16" name='.$this->name.' type="hidden" value="'.$value.'" class="m-wrap m-ctrl-medium datetimepicker">'); 
        if($this->error){
            $html.="<span class='help-inline'>".$this->error."</span>";
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }

    public function value(){
        if($this->config['auto_update']){
            return time();
        }
        return $this->value;
    }
    public function head_css(){
        $css=<<<EOF
	<link rel="stylesheet" type="text/css" href="/winphp/metronic/media/css/datetimepicker.css" />
EOF;
        return $css;
    }
    public function foot_js(){
        $js=<<<EOF
<script type="text/javascript" src="/winphp/metronic/media/js/bootstrap-datetimepicker.js"></script>
<script>
(function(){
    (function(controlType){
        $('.'+controlType).each(function(i,elem){
            var dt_picker=$(elem);
            var input=dt_picker.clone().attr({"type":"text","name":''}).insertAfter(dt_picker);
            input[controlType]({
                format:'yyyy-mm-dd hh:ii:ss',
                rtl : App.isRTL()
            });
            input.data(controlType).update(new Date(dt_picker.val()*1000));
            //console.debug(dt_picker.parents("form"));
            dt_picker.parents("form").submit(function(e){
                var d=input.data(controlType).getDate();
                dt_picker.val(parseInt(d.getTime()/1000));
            });
        });
    })('datetimepicker');
})();
</script>
EOF;
        return $js;
    }
}
