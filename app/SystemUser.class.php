<?php
class SystemUser extends DBModel{

    const PRIVILEGE_VIEW=0;
    const PRIVILEGE_EDIT=1;
    const PRIVILEGE_REVIEW=2;
    const PRIVILEGE_ANY=3;

    public function getFieldList(){
        static $FIELD_LIST=array (
            array('name'=>'id',         'type'=>"int",'key'=>true,'defalut'=>null,'null'=>false,),
            array('name'=>'name',      'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'passwd',      'type'=>"string",'defalut'=>null,'null'=>false,),
            //'是否有效 0:无效 1:有效'
            array('name'=>'valid',      'type'=>"int",'defalut'=>null,'null'=>false,),
            //权限:0,只能浏览;1:普通,浏览修改不能发布;2:高级,处理升级消息及发布;3:超级,除有前面权限外，可增加系统用户。
            array('name'=>'privilege',      'type'=>"int",'defalut'=>null,'null'=>false,),
            array('name'=>'ctime',      'type'=>"int",'defalut'=>null,'null'=>false,),
        );
        return $FIELD_LIST;
    }

    public function isValid(){
        return $this->mValid==1;
    }
    public function couldView(){
        return $this->isValid()&&$this->mPrivilege>=self::PRIVILEGE_VIEW;
    }
    public function couldEdit(){
        return $this->isValid()&&$this->mPrivilege>=self::PRIVILEGE_EDIT;
    }
    public function couldReview(){
        return $this->isValid()&&$this->mPrivilege>=self::PRIVILEGE_REVIEW;
    }
    public function couldAny(){
        return $this->isValid()&&$this->mPrivilege>=self::PRIVILEGE_ANY;
    }

    public static function getCurrentUser(){
        $systemUser=new SystemUser();
        $systemUser->setData($_SESSION['user']);
        return $systemUser;
    }

    static $PRIVILEGE_TABLE=array(self::PRIVILEGE_VIEW=>'查看',self::PRIVILEGE_EDIT=>'编辑',self::PRIVILEGE_REVIEW=>'审核',self::PRIVILEGE_ANY=>'管理员');
    public function privilege(){
        return self::$PRIVILEGE_TABLE[$this->mPrivilege];
    }
    public static function getAllPrivileges(){
        return self::$PRIVILEGE_TABLE;
    }
    
}
