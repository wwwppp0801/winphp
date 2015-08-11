<?php
namespace Form;

abstract class Field{
    protected $value;
    protected $name;
    protected $required;
    protected $error;
    protected $config;
    protected $prompt;
    protected $is_set=false;

    public function __construct($config){
        $this->config=$config;

        if(!isset($config['name'])){
            throw new Exception("name is required");
        }
        $this->name=$config['name'];

        if(isset($config['label'])){
            $this->label=$config['label'];
        }else{
            $this->label=$config['name'];
        }

        if(isset($config['required'])){
            $this->required=$config['required'];
        }else{
            $this->required=false;
        }

        if(isset($config['default'])){
            $this->value=$config['default'];
        }
        if (isset($config['validator'])){
            $this->validator=$config['validator'];
        }
        if (isset($config['prompt'])){
            $this->prompt = $config['prompt']; 
        }
        if (isset($config['user_prompt'])){
            $this->user_prompt = $config['user_prompt']; 
        }
        if (isset($config['data_group'])){
            $this->data_group = $config['data_group']; 
        }
    }
    public abstract function to_html($is_new);
    public function foot_js(){
        return "";
    }
    public function head_css(){
        return "";
    }
    public function jsonToChoice($str){
        if(!$str) return false;

        $arr = json_decode($str, true); 
        if($arr && $arr['type'] == "choice"){
            return $arr; 
        }
        return false;
    }
    public function createPrompt(){
        $data = $this->jsonToChoice($this->prompt);
        if($data){
            $li = array_map(function($item){
                return '<li><a href="javascript:void(0);">'.$item.'</a></li>';
            }, $data['lists']); 

            $li=implode("\n",$li);
            $html=""; 
            $html .= !$li?"":'<div class="dropdown clearfix btn-group" style="margin-left:20px;">'.
                        '<button class="btn btn-default dropdown-toggle prompt" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.
                            '快捷回复'.
                            '<span class="caret"></span>'.
                        '</button>'.
                        '<ul class="dropdown-menu tags" aria-labelledby="dropdownMenu1">'.
                           $li. 
                        '</ul>'.
                      '</div>';
            return $html;
        }else{
            return ''; 
        }
    }
    public function createUserPrompt(){
        if($this->jsonToChoice($this->user_prompt)) {
            return "<input type='hidden' class='user_prompt' value='{$this->user_prompt}'>";
        }
        return '';
    }
    public function validate(&$values){
        if($this->required && strlen($values[$this->name])==0){
            $this->error="字段不能为空.";
            return false;
        }
        if(isset($values[$this->name])){
            $this->is_set=true;
            $this->value=$values[$this->name];
        }
        if($this->validator){
            if(is_callable(array($this->validator,"validate")) && $this->validator->validate($values)===false ){
                $this->error=$this->validator->error;
                return false;
            }
            if(is_callable($this->validator)){
                $ret=call_user_func($this->validator,$values);
                if($ret!==true){
                    $this->error=$ret;
                    return false;
                }
            }
        }
        
        
        return true;
    }
    public function is_set(){
        return $this->is_set;
    }
    public function clear(){
        if(isset($config['default'])){
            $this->value=$this->config['default'];
        }else{
            $this->value=null;
        }
        $this->error=false;
    }
    
    public function value(){
        return $this->value;
    }
    public function error(){
        return $this->error;
    }
    public function name(){
        return $this->name;
    }
    public function label(){
        return $this->label;
    }
}
