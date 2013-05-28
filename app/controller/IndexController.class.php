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
    public function disclaimerAction(){
        return array('view'=>"index_disclaimer.tpl",'model'=>array(
            'user'=>$_SESSION['user']
        ));
    }
    public function applyAction(){
        return array('view'=>"index_apply.tpl",'model'=>array(
            'user'=>$_SESSION['user'],
            'msg'=>WinRequest::getFlash("msg"),
            'phone'=>WinRequest::getParameter("phone"),
            'email'=>WinRequest::getParameter("email"),
            'awareness'=>WinRequest::getParameter("awareness"),
        ));
    }
    public function addApplyAction(){
        if(strlen(WinRequest::getParameter("phone"))<5){
            WinRequest::setFlash("msg","请填入正确的手机号");
        }else if(strpos(WinRequest::getParameter("email"),"@")===false){
            WinRequest::setFlash("msg","请填入正确的邮箱");
        }elseif(strlen(trim(WinRequest::getParameter("awareness")))==0){
            WinRequest::setFlash("msg","您对养乐多产品的是否了解?");
        }elseif(
            strlen(trim(WinRequest::getParameter("question_title1")))==0
            && strlen(trim(WinRequest::getParameter("question_url1")))==0
            && strlen(trim(WinRequest::getParameter("question_title2")))==0
            && strlen(trim(WinRequest::getParameter("question_url2")))==0
            && strlen(trim(WinRequest::getParameter("question_title3")))==0
            && strlen(trim(WinRequest::getParameter("question_url3")))==0
        ){
            WinRequest::setFlash("msg",'请提供您亲自在搜搜问问下面的"肠道健康"标签里面回答过的关于肠道健康相关问题的标题和链接，最少1条');
        }else{
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
        }
        return array('redirect:/index/apply?phone='.urlencode(WinRequest::getParameter("phone"))
            ."&email=".urlencode(WinRequest::getParameter("email"))
            ."&awareness=".urlencode(WinRequest::getParameter("awareness"))
        );
    }
    public function darenAction(){
        return array('view'=>"index_daren.tpl",'model'=>array(
            'user'=>$_SESSION['user']
        ));
    }
    
}
