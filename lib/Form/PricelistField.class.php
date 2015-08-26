<?php
namespace Form;
use Utils;
class PricelistField extends Field{
    protected $lists;
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new=false){
        $html="";
        $value = json_decode($this->value(), true);
        $lists = $value?$value['lists']:[['product'=>'', 'price'=>'', 'count'=>'']];
        $sum = $value?$value['sum']:0;
        $defaultValue = $this->value()?$this->value():json_encode(["type"=>"pricelist", "lists"=>[['product'=>'', 'price'=>'', 'count'=>'']]]);

        foreach($lists as $k=>$list){
            if($k == 0){
                $html .="<div class='price-list-control'>";
            }
            $html .="<div class='control-group'>";
            if($k == 0){
            $html .=    "<label class='control-label'>".htmlspecialchars($this->label)."</label>";
            }
            $html .=    "<div class='controls controls-row'>";
            $html .= $this->getInputs($list);
            if($k == 0){
            //$html .=        '<a class="price-list-add span1" href="javascript:;">增加</a>';
            $html.=$this->createPrompt();
            $html.=$this->createUserPrompt();
            }

            $html .=    "</div>";
            $html .=  "</div>";
        }

        $html .= "<div class='total row' style='margin-bottom:20px;'><a class='price-list-add span1' style='margin-left:140px;' href='javascript:;'>增加</a><input type='hidden' name='{$this->name}' value='{$defaultValue}'><span class='total-price span2 offset1 text-right' style='font-size:18px;font-weight:bold;cursor:pointer;' value='{$sum}'>总价:{$sum}</span></div></div>";

        if($this->error){
            $html.="<span class='error'>".$this->error."</span>";
        }
        return $html;
    }

    public function getInputs($list=[]){
        $html = "";
        $html .=        '<input type="text" class="span2 name" placeholder="服务名" value="'.$list['name'].'"/>';
        $html .=        '<input type="text" placeholder="单价(¥)" class="span1 price" data-type="number" value="'.$list['price'].'"/>';
        //$html .=        ' X ';
        //$html .=        '<input type="text" placeholder="数量" class="span1 count" data-type="number" value="'.$list['count'].'"/>';
        $html .=        '<input type="number" placeholder="数量" class="span1 count"  min="1" max="100" data-type="number" value="'.($list['count']?$list['count']:1).'"/>';

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
        $inputs = $this->getInputs();
        $js=<<<EOF
<script>
(function(){
    if(window.__init_price_list_field){
        return;
    }
    window.__init_price_list_field=true;

    $(document).delegate(".price-list-add",'click',function(){
        var wrap = $(this).parents(".price-list-control");
        wrap.find('.total').before("<div class='control-group'><div class='controls controls-row'>"+'$inputs'+"</div></div>");
    }).delegate(".total-price", 'click',function(){
        var form = $(this).parents("form");
        form.find('input[name=field-总价]').val($(this).attr('value'));
    });
    
    function getObj(container){
        var _this = container;
            var arr = {'type':'pricelist',lists:[],value:'',sum:0};
            var list=[];
            _this.find('.controls').each(function(){
                var _name = $('.name', this);
                var _price = $('.price', this);
                var _count = $('.count', this);

                if($.trim(_name.val()) != '' && $.trim(_price.val()) != '' && $.trim(_count.val()) != ''){
                    arr.lists.push({'name':_name.val(), 'price':_price.val(), 'count':_count.val()});
                    list.push(_name.val() + '(单价:'+_price.val()+' 数量:'+_count.val()+')');
                    //arr.value += _name.val() + '(单价:'+_price.val()+' 数量:'+_count.val()+')' +' , ' ;
                }
            });
            arr.value = list.join();

        return arr;
    }

    function setValue(wrap){
        var arr = getObj(wrap);
        var total = $('.total-price', wrap);
        if(arr && arr.lists && arr.lists.length){
            var sum = arr.lists.reduce(function(a,b){
                return a + b.price * b.count; 
            }, 0);

            if(!isNaN(sum)){
                sum = Math.round(sum * 100) / 100;
                total.html('总价：'+sum).attr('value', sum);
                arr.sum = sum;
            }
        }

        wrap.find('.total input').val(JSON.stringify(arr));
    }

    $(document).delegate('.price-list-control input[type=text]',  'change', function(){
        var wrap = $(this).parents('.price-list-control');
        setValue(wrap);
    });

    /*
    setTimeout(function(){
        var price = $('.price-list-control');
        if(price.length){
            price.each(function(){
                var wrap = $(this);
                setValue(wrap);
            });
        }
        setTimeout(arguments.callee, 300);
    }, 300);*/
})();
</script>
EOF;
        return $js;
    }
}
