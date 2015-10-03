<?php
namespace Form;
use Form\Field;

class MultpromptField extends Field{
    public function __construct($config){
        parent::__construct($config);
    }

    public function to_html($is_new){
        $inputName = $this->name;
        $value = $this->value();//json_decode($this->value(), true);
        //$value = $value?[]:$value['data'];

        $html  = '<div class="control-group">';
        $html .=     '<label class="control-label">'.htmlspecialchars($this->label).'</label>';
        $html .=     '<div class="controls">';
        //$html .=        "<input type='hidden' class=' mult-prompt' name='{$inputName}' value='{$value}'/>";
        $html .=         "<div pt-prompt name='{$inputName}' json='{$value}'></div>";
        $html .=     '</div>';
        $html .= '</div>';


        return $html;
    }
    public function template(){
        $html=<<<EOF
        <div>
            <input name="{{inputName}}" value="{{data|json}}" type="hidden"/>
            <div class="pt-wrap">
                <p>客服话术</p>
                <div pt-kefu-choice data="data.data" selected="select" deleted="delete"></div>  
            </div> 
            <div class="pt-wrap">
                <p>用户话术</p>
                <div class="prompt-type-container">
                    <label ng-repeat="type in supportType">
                        <span class="label" ng-class="{\'label-success\':user_prompt.type === type.value}" data-type="{{user_prompt.type}}" data-value="{{type.value}}" ng-click="changeType(type)">
                        {{type.name}}
                        </span>
                    </label>
                </div>
                <div ng-if="user_prompt.type === \'choice\' || user_prompt.type === \'checkbox\'">
                    <div pt-user-choice data="user_prompt.data"></div>  
                </div>
                <div ng-if="user_prompt.type === \'time\' || user_prompt.type === \'date\' || user_prompt.type === \'datetime\' ">
                    时间
                </div>
            </div> 
        </div>
EOF;
        return $html;
    }

    public function kefu_choice_template(){
        $html=<<<EOF
        <div class="prompt-choice-container">
            <div><input type="text" ng-model="new_prompt"/><input type="button" value="增加" ng-click="add()"/></div>
            <ul>
                <li class="pt-choice-item" ng-if="data" ng-repeat="item in data track by \$index" ng-class="{selected:data[\$index] === cur_kefu_prompt}" ng-click="select(item)">
                    <span ng-if="item.kefu_prompt">{{item.kefu_prompt.value}}</span>
                    <a ng-click="remove(\$index)" href="javascript:;">删除</a>
                </li>
            </ul>
        </div>
EOF;
        return $html;
    }

    public function user_choice_template(){
        $html=<<<EOF
        <div class="prompt-choice-container">
            <div><input type="text" ng-model="new_prompt"/><input type="button" value="增加" ng-click="add()"/></div>
            <ul>
                <li class="pt-choice-item" ng-if="data" ng-repeat="item in data track by \$index">
                    <span ng-if="item.value">{{item.value}}</span>
                    <a ng-click="remove(\$index)" href="javascript:;">删除</a>
                </li>
            </ul>
        </div>
EOF;
        return $html;
    }

    public function head_css(){
        $css=<<<EOF
<style>
.pt-choice-item span{
    display:inline-block; 
    width:90%;
}
.pt-wrap{
    border:1px solid #dcdcdc;
    margin-top:5px;
    padding:5px;
}
.pt-wrap label{
    display:inline;
}
.prompt-type-container .selected,
.prompt-choice-container .selected{
    background-color:#dcdcdc;
}
.prompt-type-container ul,
.prompt-choice-container ul{
    list-style:none;
    padding:0;
    margin:0;
}
</style>
EOF;
        return $css;
    }

    public function foot_js(){
        $template = str_replace(PHP_EOL,'', $this->template());
        $user_choice = str_replace(PHP_EOL,'', $this->user_choice_template());
        $kefu_choice = str_replace(PHP_EOL,'', $this->kefu_choice_template());

        $js=<<<EOF
<script>
(function(){

    app.service('promptAdd', function(\$http){
        return {
            add : function(text, field_id, func){
                \$http.get('/admin/prompt/add?text='+encodeURIComponent(text)+'&field_id='+field_id)
                      .then(function(res){
                            if(res && res.data && res.data.status == 0){
                                func(res.data);
                            }else if(res && res.data){
                                alert(res.data.msg);
                            }
                      });
            }
        };
    });

    app.directive('ptUserChoice', function (promptAdd) {
        return {
            restrict: 'A',
            scope: {
                data: '='
                //selected: '&'
            },
            transclude: true,
            template: '$user_choice',
            link: function (scope, element, attrs) {
                scope.fieldId = element.parents('form').find('input[name=id]').val();
            },
            controller:['\$scope',function(scope){
                scope.add = function(){
                    if(!scope.new_prompt) return;
                    promptAdd.add(scope.new_prompt, scope.fieldId, function(res){
                        scope.data.push({id:res.data.id, value:scope.new_prompt});
                        scope.new_prompt= '';
                    });
                };
                scope.remove = function(index){
                    scope.data.splice(index, 1);
                };
            }]
        }
    });


    app.directive('ptKefuChoice', function (promptAdd) {
        return {
            restrict: 'A',
            scope: {
                data: '=',
                selected: '&',
                deleted:'&'
            },
            transclude: true,
            template: '$kefu_choice',
            link: function (scope, element, attrs) {
                scope.fieldId = element.parents('form').find('input[name=id]').val();
            },
            controller:['\$scope',function(scope){
                scope.add = function(){
                    if(!scope.new_prompt) return;
                    promptAdd.add(scope.new_prompt, scope.fieldId, function(res){
                        var obj = {kefu_prompt:{id:res.data.id, value:scope.new_prompt},user_prompt:{type:'choice',data:[]}};
                        scope.data.push(obj);
                        scope.select(obj);
                        scope.new_prompt= '';
                    });
                };
                scope.select = function(cur){
                    scope.cur_kefu_prompt = cur;
                    scope.selected()(cur);
                };
                scope.remove = function(index){
                    var del = scope.data[index]; 
                    scope.data.splice(index, 1);
                    scope.deleted()(del);
                };
            }]
        }
    });

    app.directive('ptPrompt', [function () {
        var index=0;
        return {
            restrict: 'A',
            scope: {
                inputName: '@name',
                dataJson: '@json'
                //selected: '&'
            },
            transclude: true,
            template: '$template',//.replace(/(_prompt_type_name_)/g,"$1"+(++index)),
            link: function (scope, element, attrs) {
            },
            controller:['\$scope',function(scope){
                scope.cur = {};
                scope.supportType= [
                    {name:'单选',value:'choice'}, 
                    {name:'多选',value:'checkbox'},
                    {name:'时间(今天内)',value:'time'},
                    {name:'日期',value:'date'},
                    {name:'时间',value:'datetime'}
                ];
                scope.data = scope.dataJson?JSON.parse(scope.dataJson) : {"type":"multPrompt", data:[]};

                scope.select = function(cur_kefu_prompt){
                    scope.cur = cur_kefu_prompt;
                    scope.user_prompt = scope.cur.user_prompt;
                };
                scope.delete = function(del){
                    if(scope.cur === del) {
                        scope.cur = {};
                        scope.user_prompt = null;
                    }
                };

                scope.changeType = function(type){
                    if(scope.user_prompt){
                        if((type.value == "time" || type.value == "date" || type.value == "datetime") && (scope.user_prompt.type == "checkbox" || scope.user_prompt.type=="choice")){
                            if(confirm("会丢失选项列表，你确定?")){
                                scope.user_prompt.type = type.value;
                                scope.user_prompt.data = [];
                            }
                        }else{
                            scope.user_prompt.type = type.value;
                        }
                    }
                };
            }]
        }
    }]);
})();
</script>
EOF;
        return $js;
    }
}
