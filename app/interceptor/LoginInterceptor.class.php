<?php

class LoginInterceptor extends Interceptor{
    public function beforeAction(){
        if(!$_SESSION['user']){
            throw new ModelAndViewException("not login",1,"redirect:/");
        }
    }
}
