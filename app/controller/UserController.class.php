<?php
class UserController extends BaseController{
    public function updateAction(){
        $openid=$_SESSION['user']['openid'];
        if(!$openid){
            return array("redirect:/");
        }
        DB::update("update users set realname=?,phone=?,address=? where openid=?",
            WinRequest::getParameter("realname"),
            WinRequest::getParameter("phone"),
            WinRequest::getParameter("address"),
            $openid
        );
        $user=DB::queryForOne("select * from users where openid=?",$openid);
        $_SESSION['user']['realname']=$user['realname'];
        $_SESSION['user']['phone']=$user['phone'];
        $_SESSION['user']['address']=$user['address'];
        return array("redirect:/");
    }
    public function indexAction(){
        $openid=$_SESSION['user']['openid'];
        if(!$openid){
            return array("redirect:/");
        }
        return array("user.tpl",array('user'=>$_SESSION['user']));
    }
}
