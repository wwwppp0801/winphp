<?php
class UserController extends BaseController{
    public function __construct(){
        $this->addInterceptor(new LoginInterceptor());
    }
    private function getRedirect(){
        switch(WinRequest::getParameter("redirect")){
            case "/question":
                $redirect='/question';
                break;
            case "/question/captcha":
                $redirect='/question/captcha';
                break;
            case "/question/right":
                $redirect='/question/right';
                break;
            default:
                $redirect='/';
                break;
        }
        return $redirect;
    }
    public function updateAction(){
        $openid=$_SESSION['user']['openid'];
        if(!$openid){
            return array("redirect:/");
        }
        DB::update("update users set realname=?,phone=?,address=?,qq=? where openid=?",
            WinRequest::getParameter("realname"),
            WinRequest::getParameter("phone"),
            WinRequest::getParameter("address"),
            WinRequest::getParameter("qq"),
            $openid
        );
        $user=DB::queryForOne("select * from users where openid=?",$openid);
        $_SESSION['user']['realname']=$user['realname'];
        $_SESSION['user']['phone']=$user['phone'];
        $_SESSION['user']['address']=$user['address'];
        $_SESSION['user']['qq']=$user['qq'];
        return array("redirect:".$this->getRedirect());

    }
    public function indexAction(){
        $openid=$_SESSION['user']['openid'];
        $step=intval($_GET['step']);
        if(!$openid){
            return array("redirect:/");
        }
        return array("user.tpl",array(
            'user'=>$_SESSION['user'],
            'step'=>$step,
            'redirect'=>$this->getRedirect(),
        ));
    }
}
