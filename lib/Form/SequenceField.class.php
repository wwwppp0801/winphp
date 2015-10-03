<?php
namespace Form;
use Form\Field;

class SequenceField extends Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $html="<div class='control-group'>";

        $arr=json_decode($this->value(),true);
        $arr=is_array($arr)?$arr:[];
        $list=array_map(function($a){
            return "<li data-id='{$a['id']}'>".htmlspecialchars($a['name'])."</li>";
        },$arr);
 
        $html.= "<label class='control-label'>".htmlspecialchars($this->label)."</label>".
            "<div class='controls'>".
                '<ul class="sortable">'.implode("\n",$list).
                '</ul>'.
                '<input size="16" name='.$this->name.'  type="hidden" value="'.$this->value().'">';

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
ul.sortable {
  list-style: none;
  padding: 0;
  margin: 0;
}
ul.sortable li {
  display:inline-block;
  margin: 2px;
  padding: 2px 4px;
  border: 1px solid #dcdcdc;
}
</style>
EOF;
        return $css;
    }

    public function foot_js(){
        $js=<<<EOF
<script type="text/javascript" src="/kefu/static/js/jquery.sortable.min.js"></script>
<script>
(function(){

  function init(){
    (function(controlType){
        $('.'+controlType).each(function(i,elem){
            var ul=$(elem);
            ul.sortable();
            //ul.disableSelection();

            var input = ul.next('input');
            ul.parents("form").submit(function(e){
                var arr = [];
                ul.find('li').each(function(_,li){
                    var _li = $(li);
                    arr.push({id:_li.attr('data-id'),name:_li.html()}); 
                });

                input.val(JSON.stringify(arr));
            });
        });
    })('sortable');
  }

  init();
})();
</script>
EOF;
        return $js;
    }
}
