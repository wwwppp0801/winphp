<?php

class DefaultViewSetting
{
    public static function getTemplate()
    {
        include_once (WINPHP_PATH.'/lib/Smarty/Smarty.class.php');
        $template = new Smarty();
        return $template;
    }
    public static function getTemplateWithSettings(){
        $template=self::getTemplate();
        self::setTemplateSetting($template);
        return $template;
    }
    public static function getRootDir()
    {
        return ROOT_PATH."/template/";
    }

    public static function setTemplateSetting($template)
    {
        $template->caching = false;
        //		$template->caching = true;
        $template->cache_dir = ROOT_PATH."/ctemplates/";
        $template->php_handling = false;

        $template->template_dir = self::getRootDir();
        $template->addTemplateDir(WINPHP_PATH."/template/",'winphp');
        $template->compile_dir = ROOT_PATH."/ctemplates/";
        //		echo $template->template_dir;
        $template->config_dir   = ROOT_PATH."/config/";
        $template->compile_check = true;
        // this helps if php is running in 'safe_mode'
        $template->use_sub_dirs = false;
        $template->left_delimiter='{%';
        $template->right_delimiter='%}';
        // register dynamic block for every template instance
        //$template->register_block('dynamic', 'smarty_block_dynamic', false);      
        $template->registerPlugin("function","build_url",array(get_class(),'build_url'));

    }
    public static function build_url($params,$template){

        //static $DEFAULT_ACTION="/s";
        static $DEFAULT_ACTION="";
        //static $DEFAULT_INCLUDE=array('ie');
        //除这些参数以外，其他均不透传
        //$all_params=array( 'wd', 'cl', 'ct', 'tn', 'rn', 'ie', 'f', 'lm', 'si', 'usm', 'z', 'ch', 'sts', 'vit', 'dsp', 'trh', 'trb', 'tre','la','lo','st','nojc','haobd');
        //参数默认值，当前值和默认值相等，则不用包含这个参数，但在DEFAULT_INCLUDE中的仍会被包含
        //$default_value=array('sts'=>'','cl'=>3,'ct'=>0,'tn'=>'baidu','rn'=>10,'ie'=>'utf-8','lm'=>0,'usm'=>0,'z'=>3,'ch'=>0,'trh'=>0,'si'=>'');

        list($rewrite_params_str,$exclude,$include,$action,$set_default_action)=array($params['params'],$params['exclude'],$params['include'],$params['action'],$params['set_default_action']);
        $meaningful_param_names=array('params','exclude','include','action','set_default_action');


        //////////////////处理action参数////////////////////
        if($set_default_action){
            $DEFAULT_ACTION=$set_default_action;
            if(count($params)==1&&$params['set_default_action']){
                //只是设置了默认action，不输出url
                return;
            }
        }
        $action=$action?$action:$DEFAULT_ACTION;

        /////////////////处理rewrite_params 参数/////////////////
        //
        $rewrite_params=array();
        foreach($params as $k=>$v){
            if(!in_array($k,$meaningful_param_names)){
                $rewrite_params[$k]=urlencode($v);
            }
        }
        if(is_string($rewrite_params_str)){
            $tmp=array();
            parse_str($rewrite_params_str,$tmp);
            foreach($tmp as $k =>$v){
                $tmp[$k]=urlencode($v);
            }
            $rewrite_params=array_merge($rewrite_params,$tmp);
        }elseif(is_array($rewrite_params_str)){
            foreach($rewrite_params_str as $k=>$v){
                if(!in_array($k,$meaningful_param_names)){
                    $rewrite_params[$k]=urlencode($v);
                }
            }
        }
        ////////////////处理include 参数////////////////////////
        if(!is_array($include)){
            $include=array($include);
        }
        $include=array_unique(array_filter($include));
        ////////////////处理exclude 参数////////////////////////
        if(!is_array($exclude)){
            $exclude=array($exclude);
        }
        $exclude=array_unique(array_filter($exclude));

        ///////////////获得current_params///////////////////////
        $current_params=$_GET;

        $result=array();

        foreach($current_params as $k=>$v){
            /*
            if(!in_array($k,$all_params)){
                continue;
            }
            if(isset($default_value[$k]) && $default_value[$k]==$v){
                //和默认值相等，就不用出现在结果里了
                continue;
            }*/
            if(in_array($k,$exclude)){
                continue;
            }
            if(strlen($v)==0){
                //空串的值，不要往下传了
                continue;
            }
            $result[$k]=urlencode($v);
        }
        foreach($include as $k){
            if(!isset($result[$k]) ){
                if(isset($current_params[$k])){
                    $result[$k]=urlencode($current_params[$k]);
                }
            }
        }/*
        foreach($DEFAULT_INCLUDE as $k){
            if( !in_array($k,$exclude)
                && !isset($result[$k]) 
                && isset($default_value[$k])
            ){
                    $result[$k]=$default_value[$k];
                }
        }*/
        foreach($rewrite_params as $k=>$v){
            $result[$k]=$v;
        }

        $res=array();
        foreach($result as $k=>$v){
            $res[]="$k=$v";
        }
        return $action."?".implode($res,"&");
    }
}
?>
