<?php
require_once("CaptchaAction.class.php");
class QuestionController extends BaseController{
    public static $QUESTION_NUM=5;
    public static $STEP_CONFIG=array(
        0=>array('QUESTION_NUM'=>5,'time'=>180),
        1=>array('QUESTION_NUM'=>6,'time'=>240),
        2=>array('QUESTION_NUM'=>9,'time'=>600),
    );
    public function __construct(){
        $this->addInterceptor(new LoginInterceptor());
    }
    
    public function startAction(){
        //$_SESSION['starttime']=time();
        $_SESSION['all_starttime']=time();
        $_SESSION['step']=0;
        $winnerModel=new WinnerModel();
        return array('start.tpl',array('winners'=>$winnerModel->getWinners()));
    }
    public function indexAction(){
        if(!isset($_SESSION['all_starttime'])){
            $_SESSION['all_starttime']=time();
        }
        if(!isset($_SESSION['step'])){
            $_SESSION['step']=0;
        }
        if(!isset($_SESSION['wrong_captcha'])){
            //输错验证码时需要保留当前的答案状态
            $_SESSION['starttime']=time();
        }
        $step=intval($_SESSION['step']);
        $questions=$this->getQuestions($step);
        if(isset($_SESSION['wrong_question'])){
            //上次答错了，重新回答
            $wrong_question=$questions[$_SESSION['wrong_question']];
            unset($_SESSION['wrong_question']);
        }
        if(isset($_SESSION['wrong_time'])){
            $wrong_time=$_SESSION['wrong_time'];
            unset($_SESSION['wrong_time']);
        }
        if(isset($_SESSION['wrong_captcha'])){
            $wrong_captcha=$_SESSION['wrong_captcha'];
            unset($_SESSION['wrong_captcha']);
        }
        if(isset($_SESSION['answers'])){
            $answers=$_SESSION['answers'];
            unset($_SESSION['answers']);
        }
        if(!$answers){
            //只有“保留答案”的状态，才重用上次的题目，否则都重新生成随机题目
            shuffle($questions);
            $questions=array_slice($questions,0,self::$STEP_CONFIG[$step]['QUESTION_NUM']);
            $_SESSION['question_ids']=Utils::array2Simple($questions,'id');
        }else{
            $tmp=array();
            foreach($_SESSION['question_ids'] as $qid){
                $tmp[]=$questions[$qid];
            }
            $questions=$tmp;
        }
        $now=time();
        return array("question.tpl",array(
            'questions'=>$questions,
            'step'=>$step,
            'now'=>$now,
            'starttime'=>$_SESSION['starttime'],
            'wrong_question'=>isset($wrong_question)?$wrong_question:false,
            'wrong_time'=>isset($wrong_time)?$wrong_time:false,
            'wrong_captcha'=>isset($wrong_captcha)?$wrong_captcha:false,
            'answers'=>isset($answers)?$answers:false,
            //剩余的答题时间
            'time'=>self::$STEP_CONFIG[$step]['time']-($now-$_SESSION['starttime']),
        ));
    }
    public function answerAction(){
        $step=intval($_SESSION['step']);
        $questions=$this->getQuestions($step);
        $is_right=true;
        if(!$_SESSION['question_ids']){
            return array("redirect:/question");
        }
        foreach($_SESSION['question_ids'] as $i=>$qid){
            //$qid=$_SESSION['question_ids'][$i];
            //var_dump($questions[$qid]);
            if(!$questions[$qid]||WinRequest::getParameter("q".($i+1))!=$questions[$qid]['answer']){
                $is_right=false;
                break;
            }
        }
        if($is_right){
            if($step==2){
                //最后一次回答，检查验证码
                $captcha=WinRequest::getParameter("captcha");
                if(CaptchaAction::check($captcha)){
                    if(!$this->timeOK($step)){
                        $_SESSION['wrong_time']=1;
                    }else{
                        $res=DB::insert("insert into answers(openid,starttime,endtime) values(?,?,?)",
                            $_SESSION['user']['openid'],
                            $_SESSION['all_starttime'],
                            time()
                        );
                        Soso_Logger::debug("insert result: $res, openid:{$_SESSION['user']['openid']},starttime:{$_SESSION['all_starttime']}");
                        unset($_SESSION['question_ids']);
                        unset($_SESSION['step']);
                        unset($_SESSION['all_starttime']);
                        $_SESSION['all_right']=true;
                        return array("redirect:/question/right");
                    }
                }else{
                    $_SESSION['wrong_captcha']=true;
                    //只是验证码输错了的话，需要保存当前的状态
                    $answers=array();
                    foreach($_SESSION['question_ids'] as $i=>$qid){
                        $answers[]=WinRequest::getParameter("q".($i+1));
                    }
                    $_SESSION['answers']=$answers;
                    return array("redirect:/question");
                }
            }else{
                if($this->timeOK($step)){
                    $_SESSION['step']+=1;
                }else{
                    $_SESSION['wrong_time']=1;
                }
            }
            unset($_SESSION['question_ids']);
        }else{
            $_SESSION['wrong_question']=$qid;
        }
        return array("redirect:/question");
    }
    
    public function rightAction(){
        if(!$_SESSION['all_right']){
            return array("redirect:/");
        }
        return array("right.tpl");
    }

    private function timeOK($step){
        $time=self::$STEP_CONFIG[$step]['time'];
        //比标准时间多算5s，就算是网络时延了
        if(time()-$_SESSION['starttime']<=$time+5){
            return true;
        }
    }


    private function getQuestions($step){
        require(ROOT_PATH."/resource/questions.php");
        if(!$questions[$step]){
            throw new BizException("no questions, step: $step");
        }
        $questions=$questions[$step];
        foreach($questions as $key=>&$question){
            $question['id']=$key;
        }
        return $questions;
    }
}
