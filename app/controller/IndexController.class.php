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
            'user'=>$_SESSION['user'],
            'msg'=>WinRequest::getFlash("msg"),
        ));
    }
    public function addApplyAction(){
        DB::insert('insert into apply(openid,phone,email,awareness,question_url1,question_url2,question_url3,question_title1,question_title2,question_title3) values (?,?,?,?,?,?,?,?,?,?);',
            $_SESSION['user']['openid'],
            WinRequest::getParameter("phone"),
            WinRequest::getParameter("email"),
            WinRequest::getParameter("awareness"),
            WinRequest::getParameter("question_url1"),
            WinRequest::getParameter("question_url2"),
            WinRequest::getParameter("question_url3"),
            WinRequest::getParameter("question_title1"),
            WinRequest::getParameter("question_title2"),
            WinRequest::getParameter("question_title3")
        );
        WinRequest::setFlash("msg","申请成功！");
        return array('redirect:/index/apply');
    }
    public function darenAction(){
        return array('view'=>"index_daren.tpl",'model'=>array(
            'user'=>$_SESSION['user']
        ));
    }
    
}
