<?php
abstract class Page_Admin_Base extends BaseController{
    const PAGE_SIZE=10;
    
    private $_assigned=array();
    private function assign($k,$v){
        $this->_assigned[$k]=$v;
    }
    private $_templateName;
    private function display($templateName){
        $this->_templateName=$templateName;
    }
    
    public function indexAction() {
        $tAction = 'index';
        if($this->_REQUEST('__inline_admin_index',false)!==false){
            $__inline_admin_index=$this->_REQUEST('__inline_admin_index');
            unset($_REQUEST['__inline_admin_index']);
            $inlineAdmin=$this->inline_admin[$__inline_admin_index];
            $foreignKey=$this->_REQUEST($inlineAdmin->foreignKeyName);
            $_REQUEST['__success_url']=get_class($this).".php?action=read&id=$foreignKey";
            $inlineAdmin->setForeignKey($foreignKey);
            $inlineAdmin->indexAction();
            return;
        }
        $this->assign('pageAdmin',$this);
        $tAction =$this->_REQUEST('action',$tAction);
        
        $tForbiddenMethod = array("indexAction", "__construct","__destruct");
        if ($tAction != null && !in_array($tAction, $tForbiddenMethod) && method_exists($this, $tAction)) {
            $this->$tAction();
            return array($this->_templateName,$this->_assigned);
        }   
        return array('text:error param');
    }

    public function search(){
        $model=$this->model;
        $search=$this->_GET('search');
        $this->assign("search",$search);
        foreach($this->search_fields as $field){
            $model->addWhere($field,"%$search%",'like','or');
        }
        $this->index();
    }

    public function _index(){
        $model=$this->model;
        $page=$this->_GET('page',0);
        $model->limit($page*self::PAGE_SIZE,self::PAGE_SIZE);
        if($this->list_filter){
            foreach($this->list_filter as $filter){
                $filter->setFilter($model);
            }
        }
        if($this->order_by){
            foreach($this->order_by as $by){
                if($by[0]=='+'||$by[0]=='-'){
                    $order=($by[0]=='-'?'desc':'asc');
                    $by=substr($by,1);
                }else{
                    $order='asc';
                }
                $model->orderBy($by,$order);
            }
        }
        $modelDataList=$model->find();
        $this->assign("modelDataList",$modelDataList);

        $this->assign("_page",$page);
        $this->assign("_pageSize",self::PAGE_SIZE);
        $this->assign("_startIndex",$page*self::PAGE_SIZE);
        
        $model->clear();
        $this->assign("_allCount",$model->count());

        //$this->assign("pagination",$model->mPagination);
    }
    public function index(){
        $this->_index();
        $this->display("admin/base/index.html");
    }
    public function _create(){
        if($this->form->bind($_REQUEST)){
            $this->model->setData($this->form->values());
            if(false!==$this->model->save()){
                return true;
            }
        }
        $this->assign("__is_new",true);
        $this->assign("form",$this->form);
        return false;
    }
    public function create(){
        $__success_url=$this->_REQUEST('__success_url',Utils::get_default_back_url());
        $this->assign("__success_url",$__success_url);
        $result=$this->_create();
        if($result){
            $this->back("插入成功",$__success_url);
        }else{
            $this->display("admin/base/read.html");
        }
    }
    public function _read(){
        if(isset($_REQUEST['id'])){
            $this->model->addWhere('id',$_REQUEST['id']);
            $modelData=$this->model->select();
            $this->form->bind($modelData->getData());
            $this->assign("modelData",$modelData);
            $this->assign("__is_new",false);

            ////////////inline admin///////////////
            $inlines=array();
            if($this->inline_admin){
                foreach ($this->inline_admin as $inlineAdmin){
                    $inlineAdmin->setForeignKey($_REQUEST['id']);
                    $inlineModelDataList=$inlineAdmin->model->find();
                    $inlines[]=array('admin'=>$inlineAdmin,'modelDataList'=>$inlineModelDataList);
                }
            }
            $this->assign('inlines',$inlines);
            ////////////inline admin///////////////
        }else{
            $this->assign("__is_new",true);
        }
        $this->assign("form",$this->form);
    }
    public function read(){
        $__success_url=$this->_REQUEST('__success_url',Utils::get_default_back_url());
        $this->assign("__success_url",$__success_url);
        $this->_read();
        $this->display("admin/base/read.html");
    }
    public function _update(){
        $requestData=$_REQUEST;
        if($this->form->bind($requestData)){
            $data=$this->form->values();
            $data['id']=$requestData["id"];
            $this->model->setData($data);
            if(false!==$this->model->save()){
                return true;
            }
        }
        $this->assign("__is_new",false);
        
        $this->model;
        $modelData=$this->model->addWhere('id',$requestData['id'])->select();
        $this->assign("modelData",$modelData);
        
        $this->assign("form",$this->form);
        return false;
    }
    public function update(){
        $__success_url=$this->_REQUEST('__success_url',Utils::get_default_back_url());
        $this->assign("__success_url",$__success_url);
        $result=$this->_update();
        if($result){
            $this->back("更新成功",$__success_url);
        }else{
            $this->display("admin/base/read.html");
        }
    }
    public function _delete(){
        //$this->model->reset();
        $this->model->addWhere("id",$_REQUEST['id'])->delete();
    }
    public function delete(){
        $__success_url=$this->_REQUEST('__success_url',Utils::get_default_back_url());
        $this->assign("__success_url",$__success_url);
        
        $this->_delete();
        $this->back("删除成功",$__success_url);
    }

    public function __construct(){
        //parent::__construct();
        //$this->mCurrentUser = $_SESSION['currentUser'];
        /*
        if(!$this->mCurrentUser){
            exit();
        }
        */
        /*
        if(!$this->mCurrentUser || !$this->mCurrentUser->isAdmin()){
            $this->redirect("/Anonymous.php");
            exit();
        }
        $this->assign("user_englishname",$this->mCurrentUser->getName());
        $this->assign("_user",$this->mCurrentUser);
        */
        if(isset($_COOKIE['msg'])){
            $this->assign("msg",$_COOKIE['msg']);
        }
        //$this->register_function('__list_item', array($this, '__list_item'));
        //$this->register_function('__list_item_label', array($this, '__list_item_label'));
    }
    protected function _GET($name, $default = null)
    {   
        return isset($_GET[$name]) ? trim($_GET[$name]) : $default;
    }
    protected function _POST($name, $default = null)
    {   
        return isset($_POST[$name]) ? trim($_POST[$name]) : $default;
    }
    protected function _REQUEST($name, $default = null)
    {   
        return isset($_REQUEST[$name]) ? trim($_REQUEST[$name]) : $default;
    }
    function _setMsg($msg){
        $this->assign("msg",$msg);
        setcookie('msg',$msg);
    }
    protected function back($msg,$ref_url=false){
        if($msg){
            $this->_setMsg($msg);
        }
        if(!$ref_url){
            $ref_url=Utils::get_default_back_url();
        }
        Utils::redirect($ref_url);
    }
    
}