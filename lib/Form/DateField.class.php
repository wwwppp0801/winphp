<?php

class Form_DateField extends Form_Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $class=$this->config['class'];
        $html="<div class='control-group'>";
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
//                                            '<div class="input-append date date-picker" data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years">'.
                                                '<input size="16" name='.$this->name.'  type="hidden" value="'.($this->value?htmlspecialchars($this->value):time()).'" readonly class="m-wrap m-ctrl-medium datepicker">';
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
	<link rel="stylesheet" type="text/css" href="/winphp/metronic/media/css/datepicker.css" />
EOF;
        return $css;
    }
    public function foot_js(){
        $js=<<<EOF
<script type="text/javascript" src="/winphp/metronic/media/js/bootstrap-datepicker.js"></script>
<script>
(function(){
    (function(controlType){
        $('.'+controlType).each(function(i,elem){
            var dt_picker=$(elem);
            var input=dt_picker.clone().attr({"type":"text","name":''}).insertAfter(dt_picker);
            input[controlType]({
                rtl : App.isRTL()
            });
            input.data(controlType).update(new Date(dt_picker.val()*1000-new Date().getTimezoneOffset() * 60 * 1000));
            //console.debug(dt_picker.parents("form"));
            dt_picker.parents("form").submit(function(e){
                var d=input.data(controlType).getDate();
                dt_picker.val(parseInt(d.getTime()/1000));
            });
        });
    })('datepicker');
})();
</script>
EOF;
        return $js;
    }
}
