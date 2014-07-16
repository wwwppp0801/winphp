<?php 
class BaseController
{
    protected $interceptors = array();
    private $viewClass = "DefaultView";
    public function setViewClass($viewClass)
    {
        $this->viewClass = $viewClass;
    }
    public function addInterceptor($interceptor)
    {
        $this->interceptors[] = $interceptor;
    }
    
    /**
     can be override, select interceptors for an action
     by default, select all interceptors
     */
    public function loadIntercepters($method, $controller)
    {
        return $this->interceptors;
    }
    private $_url_prefix;
    public function setUrlPrefix($prefix){
        $this->_url_prefix=$prefix;
    }
    public function getUrlPrefix(){
        return $this->_url_prefix?"/".$this->_url_prefix:"";
    }

    public function getUrl(){
        $url=preg_replace("/Controller$/","",get_class($this));
        return $this->getUrlPrefix()."/$url";
    }

    
    public function process()
    {
        $mapper = WinRequest::getAttribute("mapper");
		
        $method = $mapper->getMethod();

        // 通过action判断权限
        $tAction = 'index';
        $tAction =$this->_REQUEST('action',$tAction);
        
        $executeInfo = array('controllerName'=>preg_replace("/[A-Z][a-z]+$/","",get_class($mapper->getController())), 
							'methodName'=>$method[1],
                            'actionName'=>preg_replace("/[A-Z][a-z]+$/","",get_class($method[0])),
                            'requestActionName'=>$tAction);

		WinRequest::mergeModel(array('executeInfo'=>$executeInfo));
		WinRequest::mergeModel(array('__controller'=>$this));
		WinRequest::mergeModel(array('version'=>VERSION));
		WinRequest::mergeModel(array('isDebug'=>IS_DEBUG));
		
		$interceptors = $this->loadIntercepters($method,$mapper->getController());
        try
        {
            foreach ($interceptors as $interceptor)
            {
                $interceptor->beforeAction();
            }
            list($view, $model) = $this->getViewAndModel(call_user_func($method));
            WinRequest::mergeModel($model);
            WinRequest::setView($view);
            for($i=count($interceptors)-1;$i>=0;$i--)
            {
                $interceptor=$interceptors[$i];
                $interceptor->afterAction();
            }
        }
        catch(ModelAndViewException $e)
        {
            list($view, $model) = $this->getViewAndModel( $e->getModelAndView());
            WinRequest::mergeModel($model);
            for($i=count($interceptors)-1;$i>=0;$i--)
            {
                $interceptor=$interceptors[$i];
                $interceptor->failAction();
            }
        }
        catch(Exception $e)
        {
            for($i=count($interceptors)-1;$i>=0;$i--)
            {
                $interceptor=$interceptors[$i];
                $interceptor->failAction();
            }
            throw $e;
        }
        
        $viewObj = new $this->viewClass($view, WinRequest::getModel());
        return $viewObj->render();
    }

    protected function _REQUEST($name, $default = null)
    {   
        return isset($_REQUEST[$name]) ? trim($_REQUEST[$name]) : $default;
    }
    
    private function getViewAndModel($modelAndView){
        if(isset($modelAndView['view'])){
            return array($modelAndView['view'],
                     $modelAndView['model']);
        }else{
            return $modelAndView;
        }
    }
}
