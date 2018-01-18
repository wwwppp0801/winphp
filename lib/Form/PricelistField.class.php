<?php
namespace Form;
use Utils;
class PricelistField extends Field{
    protected $lists;
    public function __construct($config){
        parent::__construct($config);
        if(isset($config['mode'])){
            $this->mode=$config['mode'];
        }
    }

    public function to_html($is_new=false){
        $html="";
        $value = json_decode($this->value(), true);
        $lists = $value?$value['lists']:[['product'=>'', 'price'=>'', 'count'=>'']];
        $sum = $value?$value['sum']:0;
        $defaultValue = $this->value()?$this->value():json_encode(["type"=>"pricelist", "lists"=>[['product'=>'', 'price'=>'', 'count'=>'']]]);

        return "<div price-list json='{$this->value()}' label='{$this->label}' input='{$this->name}' jsonkefu='{$this->prompt}' jsonuser='{$this->user_prompt}' jsonmult='{$this->mult_prompt}' field-data='data.fields[\"".htmlspecialchars($this->label)."\"]'></div>";
    }

    public function template(){
        $label = $this->label;
        $kefu_prompt = $this->createPrompt();
        $user_prompt = $this->createUserPrompt();
        $html=<<<EOF
        <div class="control-group">
            <label class="control-label">{{label}}</label>
             <div class="controls controls-row">
                    <input type="text" class="span4" style="visibility:hidden;"/>
                    <kefu-help-mult-prompt jsonmult="{{multPrompt}}"></kefu-help-mult-prompt>
                    <kefu-help-qu-prompt data="fieldData" ng-if="fieldData"></kefu-help-qu-prompt>
             </div>
        </div>
        <div class="list-input" style="margin-top:-35px;">
            <div class="control-group" ng-repeat="item in data.lists track by \$index">
                <div class="controls controls-row list-input-wrap">
                    <span class="arrow">
                        <i ng-class="{disable:\$index === 0}" ng-click="up(\$index)">∧</i>
                        <i ng-class="{disable:\$index === data.lists.length -1}" ng-click="down(\$index)">∨</i>
                    </span>
                    <input type="text" class="span2 name" placeholder="服务名" ng-model="data.lists[\$index].name"/>
                    <input type="text" placeholder="单价(¥)" class="span1 price" ng-model="data.lists[\$index].price"/>
                    <span class="count clearfix span1">
                        <span class="add-btn minus " ng-class="{disable:data.lists[\$index].count == 0}" ng-click="data.lists[\$index].count = data.lists[\$index].count-1<0?0:data.lists[\$index].count-1"><b>-</b></span>
                        <span class="num">{{data.lists[\$index].count}}</span>
                        <span class="add-btn add" ng-click="data.lists[\$index].count = data.lists[\$index].count - 0 +1"><b>+</b></span>
                    </span>
                    <a class="remove" href="javascript:;" ng-click="remove(\$index)">删除</a>
                </div>
            </div>
        </div>
        <div class="total row" style="margin-bottom:20px;">
            <a class="price-list-add span1" style="margin-left:140px;" href="javascript:;" ng-click="add()">增加</a>
            <input type="hidden" name="{{inputName}}" value="{{data|priceListNoCount|json}}">
            <span class="total-price span2 offset1 text-right" style="font-size:18px;font-weight:bold;cursor:pointer;" value="{{data.sum}}">总价:{{data.lists|priceListSum}}</span>
        </div>
        <div class="row" style="margin-left:140px;"><div qu-sug no-use="1" data="fieldData" ng-if="fieldData"></div></div>
EOF;
        return $html;
    }

    public function head_css(){
        $css=<<<EOF
<style>
.add-btn{
display: block;
float: left;
width: 15px;
height: 15px;
background-color: rgb(244, 80, 80);
color: #fff;
border-radius: 4px;
text-align: center;
line-height:15px;
cursor:pointer;
}

.add-btn b{
display:block;
}
.add-btn.disable{
background-color: #dcdcdc;
}

.count{
    padding-top:5px;
}
.count .num{
display: block;
float: left;
padding:0 3px;
height:20px;
text-align: center;
}

.list-input-wrap .remove{
    display:none;
    margin-left:-5px;
}
.list-input-wrap:hover .remove{
    display:inline;
}
.list-input-wrap .arrow{
    display:none;
    border:1px solid #dcdcdc;
}
.list-input-wrap .arrow i{
    display:block;
    text-align:center;
    line-height:12px;
    cursor:pointer;
}
.list-input-wrap .arrow i:hover{
    background-color:#f0f0f0;
}
.list-input-wrap:hover .arrow{
    display:block;
    float:left;
    width:15px;
    margin-left:-17px;
}
</style>
EOF;
        return $css;
    }
    
    public function foot_js(){
        $template = str_replace(PHP_EOL,'', $this->template());
        $js=<<<EOF
<script>
(function(){

    app.filter('priceListSum', function () {
        return function(list){
            if(!list || !angular.isArray(list)) return 0;

            var sum =  list.filter(function(item){
                return !item.name || !item.price || !item.count || !isNaN(item.price - 0);
            }).reduce(function(a1, a2){
                a1 += a2.count * a2.price;
                return a1;
            },0);

            sum = Math.round(sum * 100) / 100;
            return sum || 0;
        };
    });

    app.filter('priceListNoCount', function(){
        return function(data){
            var ret = angular.copy(data);
            ret.lists = data.lists.filter(function(item){
                return item.count;
            });
            return ret;
        }
    });

    app.directive("priceList", function(){
        return {
            restrict : "A",
            scope :{
                inputName : '@input',
                dataJson : '@json',
                label : '@label',
                kefuPrompt : '@jsonkefu',
                userPrompt : '@jsonuser',
                multPrompt : '@jsonmult',
                fieldData : '=fieldData'
            },
            transclude:true,
            template:'$template',
            link:function(scope, element, attrs){
                //trigger change
                scope.\$watch('data.lists', function(){
                    //next tick
                    if(!scope.dataInit){
                        setTimeout(function(){
                            element.find('input').eq(0).trigger('change');
                        },1);
                    }else{
                        scope.dataInit = false;
                    }
                }, true);
            },
            controller : ['\$scope', '\$filter', function(scope, filter){
                scope.data = scope.dataJson?JSON.parse(scope.dataJson):{type:'pricelist', lists:[{name:'', count:0, price:''}], sum:0};
                //忽略这次data change
                scope.dataInit = true;
        
                scope.add = function(){
                    //check last if name is '' 
                    if(scope.data.lists.length && scope.data.lists[scope.data.lists.length - 1].name.match(/^\s*$/)) {
                        alert("请完成当前项");
                        return;
                    }

                    scope.data.lists.push({name:'', count:0, price:''});
                };

                scope.remove = function(index){
                    scope.data.lists.splice(index, 1);
                };

                scope.up = function(index){
                    if(index == 0) return;
                
                    var item = scope.data.lists.splice(index, 1);  
                    scope.data.lists.splice(index - 1, 0, item[0]);
                };
                scope.down = function(index){
                    if(index == scope.data.lists.length - 1) return;

                    var item = scope.data.lists.splice(index, 1);  
                    scope.data.lists.splice(index + 1, 0, item[0]);

                };

                scope.\$watch('data.lists', function(){
                    scope.data.sum = filter('priceListSum')(scope.data.lists);
                    var arr = scope.data.lists
                                        .filter(function(item){
                                            return item.count;
                                        })
                                        .map(function(item){
                                            return item.name + '(单价:'+ item.price +' 数量:'+item.count+')';    
                                        });

                    scope.data.value = arr.join();
                    //scope.data.value

                    scope.data.lists = scope.data.lists.map(function(item){
                        if(isNaN(item.price - 0) && item.price != "-"){
                            item.price = 0;
                        }

                        return item;
                    });
                }, true);
            }]
        };
    });
})();
</script>
EOF;
        return $js;
    }
}
