<?php
class UrlMapper
{
    private $url;
    private $controller;
    private $action;
    public function __construct($url)
    {
        $this->url = trim($url," \t\n/");
        $tokens = array_filter(explode('/', $this->url));

        $this->controller=$this->getController($tokens);
        if(isset($tokens[0]) && method_exists($this->controller,$tokens[0]."Action")){
            $this->method=array($this->controller,$tokens[0]."Action");
            array_shift($tokens);
        }else{
            $this->action=$this->getAction($tokens);
            if(isset($tokens[0]) && method_exists($this->action,$tokens[0])){
                $this->method=array($this->action,$tokens[0]);
                array_shift($tokens);
            }else if(!$this->action && method_exists($this->controller,"indexAction")){
                $this->method=array($this->controller,"indexAction");
            }else if(method_exists($this->action,'index')){
                $this->method=array($this->action,"index");
            }
        }
        if(!$this->method){
            throw new SystemException("no match method: $url");
        }

        foreach($tokens as $i=>$token){
            if (strlen($token) > 0)
            {
                WinRequest::setParameter($i, $token);
            }
        }

    }

    public function getController(&$tokens=null)
    {
        if ($this->controller)
        {
            return $this->controller;
        }

        if(count($tokens)==0){
            $tokens=array('index');
        }

        for($i=count($tokens);$i>0;$i--){
            $ctokens=array_slice($tokens,0,$i);
            
            $urlPrefix=implode(array_slice($ctokens,0,count($ctokens)-1),"/");
            $classFile = ROOT_PATH."/app/controller/"
                .$urlPrefix."/"
                .ucfirst("{$ctokens[count($ctokens)-1]}Controller.class.php");
            if (file_exists($classFile)){
                require_once($classFile);
                $controllerClass=ucfirst($ctokens[count($ctokens)-1]."Controller");
                if (class_exists($controllerClass))
                {
                    $this->controller=new $controllerClass();
                    $this->controller->setUrlPrefix($urlPrefix);
                    $this->actionPath=implode($ctokens,'/');
                    $tokens=array_slice($tokens,$i);
                    return $this->controller;
                }else{
                    throw new SystemException("file: $classFile does not have class: $controllerClass");
                }
            }
        }
        $this->actionPath='';
        $this->controller=new BaseController();
        return $this->controller;
    }

    public function getAction(&$tokens)
    {
        if ($this->action)
        {
            return $this->action;
        }

        if(count($tokens)==0){
            $tokens=array('index');
        }

        for($i=count($tokens);$i>0;$i--){
            $ctokens=array_slice($tokens,0,$i);
            $classFile = ROOT_PATH."/app/controller/"
                .$this->actionPath."/"
                .implode(array_slice($ctokens,0,count($ctokens)-1),"/")
                .ucfirst("{$ctokens[count($ctokens)-1]}Action.class.php");
            if (file_exists($classFile)){
                require_once($classFile);
                $actionClass=ucfirst($ctokens[count($ctokens)-1]."Action");
                if (class_exists($actionClass)){
                    $this->action=new $actionClass();
                    $tokens=array_slice($tokens,$i);
                    return $this->action;
                }else{
                    throw new SystemException("file: $classFile does not have class: $actionClass");
                }
            }
        }

        return null;
    }
    public function getMethod()
    {
        if($this->method){
            return $this->method;
        }
        throw new SystemException("no method:".implode($tokens,"/"));
    }

}

