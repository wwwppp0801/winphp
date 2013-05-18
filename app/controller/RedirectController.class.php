<?php
//array(2) { ["access_token"]=> string(32) "78A260B7D87C39F94AC7D52D22E75DB0" ["expires_in"]=> string(7) "7776000" } 
//string(83) "callback( {"client_id":"100444400","openid":"D4C5F1340E70E1106A8E6851DD7B1316"} ); " 
//string(714) "{ "ret": 0, "msg": "", "nickname": "[h1]凡人芃", "gender": "男", "figureurl": "http:\/\/qzapp.qlogo.cn\/qzapp\/100444400\/D4C5F1340E70E1106A8E6851DD7B1316\/30", "figureurl_1": "http:\/\/qzapp.qlogo.cn\/qzapp\/100444400\/D4C5F1340E70E1106A8E6851DD7B1316\/50", "figureurl_2": "http:\/\/qzapp.qlogo.cn\/qzapp\/100444400\/D4C5F1340E70E1106A8E6851DD7B1316\/100", "figureurl_qq_1": "http:\/\/q.qlogo.cn\/qqapp\/100444400\/D4C5F1340E70E1106A8E6851DD7B1316\/40", "figureurl_qq_2": "http:\/\/q.qlogo.cn\/qqapp\/100444400\/D4C5F1340E70E1106A8E6851DD7B1316\/100", "is_yellow_vip": "1", "vip": "1", "yellow_vip_level": "7", "level": "7", "is_yellow_year_vip": "1" } "

class RedirectController extends BaseController{
    public function qqAction(){
        ///get access token
        $code=$_GET['code'];
        $state=$_GET['state'];
        $raw=Utils::curlGet("https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=".APPID."&client_secret=".APPKEY."&code=$code&state=state&redirect_uri=".urlencode("http://".DOMAIN_NAME));
        parse_str($raw,$arr);
        //var_dump($arr);
        $access_token=$arr['access_token'];
        if(!$access_token){
            throw new BizException("get access_token error:".$raw);
        }

        ///get open id
        $raw=Utils::curlGet("https://graph.qq.com/oauth2.0/me?access_token={$access_token}");
        //var_dump($raw);
        //remove "callback()"
        $lpos = strpos($raw, "(");
        $rpos = strrpos($raw, ")");
        $raw  = substr($raw, $lpos + 1, $rpos - $lpos -1);

        $user = json_decode($raw,true);
        if (isset($user['error'])){
            throw new BizException( $user['error'].":".$user['error_description']);
        }
        $openid=$user['openid'];


        $userinfo=DB::queryForOne("select * from users where openid=?",$openid);
        if(!$userinfo){
            ///get user info
            $userinfo=Utils::curlGet("https://graph.qq.com/user/get_user_info?access_token=$access_token&oauth_consumer_key=".APPID."&openid=$openid");
            $userinfo=json_decode($userinfo,true);
            $userinfo['figureurl']=$userinfo['figureurl_1']?$userinfo['figureurl_1']:$userinfo['figureurl_qq_1'];
            DB::insert("insert into users(accesstoken,openid,nickname,gender,figureurl) values(?,?,?,?,?)",
                $access_token,$openid,$userinfo['nickname'],$userinfo['gender'],$userinfo['figureurl']
            );
        }
        //var_dump($userinfo);
        $_SESSION['user']['openid']=$openid;
        $_SESSION['user']['access_token']=$access_token;
        $_SESSION['user']['nickname']=$userinfo['nickname'];
        $_SESSION['user']['gender']=$userinfo['gender'];
        $_SESSION['user']['figureurl']=$userinfo['figureurl'];
        return array('view'=>"redirect:/");
    }
    public function logoutAction(){
        session_unset();
        return array('view'=>"redirect:/");
    }
    public function qqloginAction(){
        $appid=APPID;
        //$scope="get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
        $scope="get_user_info";
        $callback="http://".DOMAIN_NAME."/redirect/qq";

        //$_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
        //没有存session，忽略了state的检查
        $state = md5(uniqid(rand(), TRUE)); //CSRF protection

        $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
            . $appid . "&redirect_uri=" . urlencode($callback)
            . "&state=".$state
            . "&scope=".$scope;
        header("Location:$login_url");
    }


}
