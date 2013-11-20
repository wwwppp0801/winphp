<?php

class LoginInterceptor extends Interceptor{
    public function beforeAction(){
        $model=WinRequest::getModel();
        $executeInfo=$model['executeInfo'];
        if(!$_SESSION['user'] && $executeInfo['methodName']!='loginAction'){
            throw new ModelAndViewException("not login",1,"redirect:/index/login?url=".urlencode($_SERVER['REQUEST_URI']));
        }
        else{
        	//WinRequest::mergeModel(array('user'=>$_SESSION['user']));
        	WinRequest::mergeModel(array('user'=>SystemUser::getCurrentUser()));
        }
    }
}
