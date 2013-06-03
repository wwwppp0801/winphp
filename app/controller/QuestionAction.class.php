<?php
class QuestionAction{
    public static $QUESTION_NUM=5;
    public static $STEP_CONFIG=array(
        0=>array('QUESTION_NUM'=>5,'time'=>180),
        1=>array('QUESTION_NUM'=>6,'time'=>240),
        2=>array('QUESTION_NUM'=>9,'time'=>600),
    );
    public function start(){
        //$_SESSION['starttime']=time();
        $_SESSION['all_starttime']=time();
        $_SESSION['step']=0;
        return array('redirect:/question');
    }
    public function index(){
        $_SESSION['starttime']=time();
        $step=intval(WinRequest::getParameter("step",0));
        $questions=$this->getQuestions($step);
        if(isset($_SESSION['wrong_question'])){
            //上次答错了，重新回答
            $wrong_question=$questions[$_SESSION['wrong_question']];
            unset($_SESSION['wrong_question']);
            /*
            $tmp=array();
            foreach($_SESSION['question_ids'] as $qid){
                $tmp[]=$questions[$qid];
            }
            $questions=$tmp;
            */
        }
        if(isset($_SESSION['wrong_time'])){
            $wrong_time=$_SESSION['wrong_time'];
            unset($_SESSION['wrong_time']);
        }
        shuffle($questions);
        $questions=array_slice($questions,0,self::$STEP_CONFIG[$step]['QUESTION_NUM']);
        $_SESSION['question_ids']=Utils::array2Simple($questions,'id');
        return array("question.tpl",array(
            'questions'=>$questions,
            'step'=>$step,
            'now'=>time(),
            'starttime'=>$_SESSION['starttime'],
            'wrong_question'=>isset($wrong_question)?$wrong_question:false,
            'wrong_time'=>isset($wrong_time)?$wrong_time:false,
            'time'=>self::$STEP_CONFIG[$step]['time'],
        ));
    }
    public function answer(){
        $step=intval(WinRequest::getParameter("step",0));
        $questions=$this->getQuestions($step);
        $is_right=true;
        for($i=0;$i<self::$STEP_CONFIG[$step]['QUESTION_NUM'];$i++){
            $qid=$_SESSION['question_ids'][$i];
            if(WinRequest::getParameter("q".($i+1))!=$questions[$qid]['answer']){
                $is_right=false;
                break;
            }
        }
        if($is_right){
            if($step==2){
                //最后一次回答，检查验证码
                $captcha=WinRequest::getParameter("captcha");
                if($captcha && $captcha==$_SESSION['captcha']){
                    unset($_SESSION['captcha']);
                    if(!$this->timeOK($step)){
                        $_SESSION['wrong_time']=1;
                    }else{
                        DB::insert("insert into answers(openid,starttime,endtime) values(?,?,?);",
                            $_SESSION['user']['openid'],
                            $_SESSION['all_starttime'],
                            time(),
                        );
                        return array("redirect:/question/right");
                    }
                }else{
                    $_SESSION['wrong_captcha']=true;
                }
            }else{
                if($this->timeOK($step)){
                    $_SESSION['step']+=1;
                }else{
                    $_SESSION['wrong_time']=1;
                }
            }
        }else{
            $_SESSION['wrong']=$qid;
        }
        return array("redirect:/question");
    }
    private function timeOK($step){
        $time=self::$STEP_CONFIG[$step]['time'];
        //比标准时间多算5s，就算是网络时延了
        if(time()-$_SESSION['starttime']<=$time+5){
            return true;
        }
    }

    public function right(){
        unset($_SESSION['step']);
        return array("text:answer_right");
    }

    private function getQuestions($step){
        require(ROOT_PATH."/config/questions.php");
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
