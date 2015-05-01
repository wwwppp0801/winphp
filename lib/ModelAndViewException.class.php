<?php
class ModelAndViewException extends Exception
{
    public function __construct($message, $errorCode = 0,$view="error.tpl",$model=array())
    {
        parent::__construct($message, $errorCode);
        $this->view=$view;
        $this->model=$model;
    }
    public function getModelAndView(){
        return array('view'=>$this->view,'model'=>$this->model);
    }
}
