<?php

class RedirectController extends BaseController{
    public function indexAction(){
        return array('view'=>"text:redirect");
    }
}
