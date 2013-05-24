<?php

class IndexController extends BaseController{
    public function __construct(){
        $this->addInterceptor(new LoginInterceptor());
    }

    public function indexAction(){
        return array('view'=>"index.tpl",'model'=>array(
            'user'=>$_SESSION['user']
        ));
    }
    public function prizeAction(){
        return array('view'=>"index_prize.tpl",'model'=>array(
            'user'=>$_SESSION['user']
        ));
    }
    public function hireAction(){
        return array('view'=>"index_hire.tpl",'model'=>array(
            'user'=>$_SESSION['user']
        ));
    }
    public function applyAction(){
        return array('view'=>"index_apply.tpl",'model'=>array(
            'user'=>$_SESSION['user']
        ));
    }
    public function darenAction(){
        return array('view'=>"index_daren.tpl",'model'=>array(
            'user'=>$_SESSION['user']
        ));
    }
    
}
