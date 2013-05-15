<?php

class IndexController extends BaseController{

    public function indexAction(){
        return array('view'=>"text:hello");
    }
    
}
