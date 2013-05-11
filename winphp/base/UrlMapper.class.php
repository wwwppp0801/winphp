<?php 
class UrlMapper
{
    private $url;
    private $controller;
    private $action;
    public function __construct($url)
    {
        $this->url = $url;
        $tokens = explode('/', $this->url);
        $this->controllerName = array_shift($tokens);
        $this->actionName = array_shift($tokens);
        $this->methodName = array_shift($tokens);
        while (count($tokens) > 0)
        {
            $key = array_shift($tokens);
            $value = array_shift($tokens);
            if (strlen($key) > 0)
            {
                WinRequest::setParameter($key, $value);
            }
        }
    }
    public function getActionName(){
    	if(!$this->actionName){
    		return "index";
    	}
        return $this->actionName;
    }
    public function getControllerName(){
    	if(!$this->controllerName){
    		return "index";
    	}
        return $this->controllerName;
    }
    public function getMethodName(){
        if(!$this->methodName){
    		return "index";
    	}
        return $this->methodName;
    }
    
    public function getController()
    {
        if ($this->controller)
        {
            return $this->controller;
        }
        $controllerName = $this->getControllerName();
        $className = ucfirst($controllerName."Controller");
        
        if (!class_exists($className))
        {
            throw new SystemException("no controller:{$controllerName}");
        }
        $controller = new $className($controllerName);
        $this->controller = &$controller;
        return $controller;
    }
    public function getAction()
    {
        if ($this->action)
        {
            return $this->action;
        }
        $actionName = $this->getActionName();
        $className = ucfirst($actionName)."Action";
        
        if(!class_exists($className)){
            $classFile = ROOT_PATH."/app/controller/{$this->getControllerName()}/$className.class.php";
            if (file_exists($classFile))
            {
                require_once ($classFile);
            }
        }
        if (class_exists($className))
        {
            $action = new $className();
            $this->action = $action;
            return $action;
		}elseif(method_exists($this->getController(),"{$actionName}Action")){
			//支持直接在controller里面写action方法
			return $this->getController();
		}
        else
        {
            throw new SystemException("no action:$actionName -> $className ");
        }
    }
    public function getMethod()
    {
        $methodName = $this->getMethodName();
        if (method_exists($this->getAction(), $methodName) || method_exists($this->getAction(), '__call'))
        {
            return $methodName;
        }elseif(method_exists($this->getController(),$this->getActionName()."Action")){
			//支持直接在controller里面写action方法
			return $this->getActionName()."Action";
		}
        else
        {
            throw new SystemException("no method:{$this->controllerName } -> {$this->getActionName()} -> $methodName ");
        }
    }
    
}

