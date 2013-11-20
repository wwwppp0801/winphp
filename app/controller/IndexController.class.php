<?php

class IndexController extends BaseController{
    public function __construct(){
        $this->addInterceptor(new LoginInterceptor());
    }

    public function indexAction(){
        return array("index.tpl");
    }
    public function loginAction(){
        if($_POST){
            if($user=$this->valid($_POST['username'],$_POST['password'])){
                $_SESSION['user']=$user;
                return array("redirect:".(isset($_REQUEST['url'])?$_REQUEST['url']:"/"));
            }else{
                return array("redirect:".$_SERVER['REQUEST_URI']);
            }
        }
        return array("admin/login.tpl",array('url'=>$_GET['url']));
    }
    public function logoutAction(){
        unset($_SESSION['user']);
        return array("redirect:/Index/login");
    }

    private function valid($username, $password){
        //for test
        //return array('name'=>$username, 'privilege'=>1);
        $password = md5($password);

        $tbl = new DBTable("system_user");
        $tbl->addWhere('name', $username);
        $tbl->addWhere('passwd', $password);
        $tbl->addWhere('valid', '1');

        return $tbl->select();
    }
}
