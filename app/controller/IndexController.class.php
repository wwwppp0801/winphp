<?php

class IndexController extends BaseController{

    public function indexAction(){
        return array('view'=>"index.tpl",'model'=>array(
            'user'=>$_SESSION['user']
        ));
    }
    
}
