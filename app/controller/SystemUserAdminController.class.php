<?php
class SystemUserAdminController extends Page_Admin_Base {
    public function __construct(){
        parent::__construct();
        $this->addInterceptor(new LoginInterceptor());
        $this->model=new SystemUser();
        //$this->model->on('beforeinsert','beforeinsert',$this);
        //$this->model->on('beforeupdate','beforeupdate',$this);

        $privilege_choices=array();
        foreach(SystemUser::getAllPrivileges()as $k =>$v){
            $privilege_choices[]=array($k,$v);
        }
        $this->form=new Form(array(
            /*
            array('type'=>'text',"name"=>"name","label"=>'name',"required"=>true,'class'=>'wide'),
            array('type'=>'text',"name"=>"url","label"=>'url',"required"=>true,'class'=>'wide'),
            array('type'=>'text',"name"=>"img","label"=>'img','class'=>'wide'),
            array('type'=>'text',"name"=>"tags","label"=>'tags','class'=>'wide'),
            array('type'=>'text',"name"=>"status","label"=>'status','default'=>1,'class'=>'wide'),
            array('type'=>'text',"name"=>"ctime","label"=>'ctime','default'=>time(),'class'=>'wide'),
             */

            array('name'=>'name',      'type'=>"text",'defalut'=>null,'required'=>true,),
            array('name'=>'ctime',      'type'=>"datetime",'defalut'=>null,'null'=>false,),
            array('name'=>'passwd',      'type'=>"password",'defalut'=>null,'required'=>true,),
            array('name'=>'passwd_again',      'type'=>"password",'defalut'=>null,'required'=>false,),
            array('name'=>'valid',      'type'=>"text",'defalut'=>null,'null'=>false,),
            array('name'=>'privilege',      'type'=>"choice",'choices'=>$privilege_choices,'defalut'=>null,'null'=>false,'required'=>true,),
        ));
        $this->list_display=array('id','name',
            array('label'=>'权限','field'=>array($this,'display_privilege')),
            array('label'=>'创建时间','field'=>array($this,'display_ctime')),
        );
        /*
        $this->list_filter=array(
            new Admin_SiteTagsFilter()
        );
        $this->inline_admin=array(
            new Page_Admin_InlineSiteModule($this,'site_id'),
        );
        $this->multi_actions=array(
            array('label'=>'添加到module','action'=>'javascript:add_to_module();return false;'),
        );*/
        
        $this->search_fields=array('name');
    }
    public function display_ctime($modelData){
        return strftime("%Y-%m-%d",$modelData->mCtime);
    }   
    public function display_privilege($modelData){
        return $modelData->privilege();
    }
}
