<?php 
function smarty_modifier_startWith($string, $startStr)
{
    return (strncmp($string, $startStr, strlen($startStr)) === 0);
}


function smarty_modifier_toUTF8($string)
{
    return Utils::toUTF8($string);
}

function smarty_modifier_toGBK($string)
{
    return Utils::toGBK($string);
}

function smarty_modifier_decodeURIComponent($string)
{
	return Utils::_decodeURIComponent($string);
}
function smarty_modifier_numberFormat($string){
	return number_format($string);
}

function smarty_modifier_relTime($string){
    $now=time();
    $srcTS=intval($string);
    $hours = ($now - $srcTS) / 3600;
    if ($hours > 24) {
        $time = (date('Y',$srcTS)==date('Y',$now)?"":date('Y',$srcTS)."年") . date('n',$srcTS) . "月" . date('j',$srcTS) . "日";
    }
    else {
        if ($hours > 1) {
            $time = intval($hours) . "小时前";
        }
        else {
            $time = intval($hours * 60 < 1 ? 1 : $hours * 60). "分钟前";
        }
    }
    return $time;
}

function smarty_function_json_encode($params)
{
    return json_encode($params['obj']);
}

class DefaultView
{
    private $templateFile;
    private $local;
    private $data;
    
    public function DefaultView($view, $model)
    {
        $this->data = $model;
        $this->templateFile = $view;
    }
    
    public function render()
    {
        if (strstr($this->templateFile, "redirect:"))
        {
            $url = substr($this->templateFile, strlen("redirect:"));
            header("Location:".$url);
            return "";
        }
        else if (strstr($this->templateFile, "json:"))
        {
            $parameter = explode(':', $this->templateFile);
            $num = count($parameter);
            //example json:callback
            if ($num == 2)
            {
                $checkList = 'soso.com|qq.com';
                $callback = $parameter[1];                
            }
            //example json:soso.com|qq.com:callback
            else if ($num == 3)
            {
                $checkList = $parameter[1];
                $callback = $parameter[2];
            }
            else
            {
                throw new SystemException('json parameter error');
            }
            if($checkList&& !$_SERVER['HTTP_REFERER']){
                throw new SystemException("forbidden");
            }
            $preg = '/^http:\/\/[^\/?;]*\.('.$checkList.')(\/|$)/';
            $data = parse_url($_SERVER['HTTP_REFERER']);
            $check = "{$data['scheme']}://{$data['host']}";

            if(!empty($_SERVER['HTTP_REFERER']) && !preg_match($preg, $check))
            {
                throw new SystemException("forbidden");
            }

            if (!strlen($callback))
            {
                $callback = WinRequest::getParameter('callback');
            }

            $callback = preg_replace("/[^a-zA-Z0-9_]/", "", $callback);
            
            return $callback."(".json_encode($this->data).");";
        }
        else if (strstr($this->templateFile, "text:"))
        {
            $text = substr($this->templateFile, strlen("text:"));
            return $text;
        }
        else
        {
            return $this->getRenderOutput();
        }
    }
   
    private function getRenderOutput()
    {
        $template = DefaultViewSetting::getTemplate();
        if (!file_exists(DefaultViewSetting::getRootDir().$this->templateFile))
        {
            throw new SystemException("no this template:".$this->templateFile);
        }
        DefaultViewSetting::setTemplateSetting($template);
		//var_dump($this->data);
        $template->register_modifier('startWith', 'smarty_modifier_startWith');
        $template->register_modifier('toUTF8', 'smarty_modifier_toUTF8');
        $template->register_modifier('toGBK', 'smarty_modifier_toGBK');
		$template->register_modifier('decodeURIComponent', 'smarty_modifier_decodeURIComponent');
		$template->register_modifier('numberFormat','smarty_modifier_numberFormat');
		$template->register_modifier('relTime','smarty_modifier_relTime');
        $template->register_function('json_encode', 'smarty_function_json_encode');
        $template->assign($this->data);

        return $template->fetch($this->templateFile);
    }
    
}



