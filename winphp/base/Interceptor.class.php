<?php

class Interceptor{
    public function beforeAction(){}
    public function afterAction(){}
    //called when action throw ModelAndViewException
    public function failAction(){}
}
