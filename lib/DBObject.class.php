<?php
class DBObject{
    private $dsn;
    private $username;
    private $password;
    private $dbh;
    private $lastQuery;
    private function __construct($dsn,$username,$password){
        list($this->dsn,$this->username,$this->password)=array($dsn,$username,$password);
    }
    private static $_instances=[];
    public static function getInstance($name="root"){
        if(isset(self::$_instances[$name])){
            return self::$_instances[$name];
        }
        return null;
    }
    public static function init($dsn,$username,$password,$name="root"){
        self::$_instances[$name]=new self($dsn,$username,$password);
        return self::$_instances[$name];
    }
    
    public function beginTransaction(){
        Logger::debug('beginTransaction');
        $this->getDBH()->beginTransaction();
    }
    public function commit(){
        Logger::debug('commitTransaction');
        $this->getDBH()->commit();
    }
    public function rollBack(){
        Logger::debug('rollbackTransaction');
        $this->getDBH()->rollBack();
    }
    public function getDriverName(){
        Logger::debug("getDriverName");
        return $this->getDBH()->getAttribute(PDO::ATTR_DRIVER_NAME);
    }
    private $__callbacks=[];
    public function addCallback($name,$callback){
        if(is_callable($callback)){
            if(!isset($this->__callbacks[$name])){
                $this->__callbacks[$name]=[];
            }
            $this->__callbacks[$name][]=$callback;
        }
    }
    
    private $__first_use=false;
    private function getDBH(){
        if(!$this->dbh){
            $this->dbh = new PDO($this->dsn,$this->username,$this->password);
            if(!$this->__first_use && $this->__callbacks['first_use']){
                $this->__first_use=true;
                if(isset($this->__callbacks['first_use'])){
                    foreach($this->__callbacks['first_use'] as $_cb){
                        call_user_func($_cb,$this);
                    }
                }
            }
        }
        return $this->dbh;
    }
    public function execute_sql($sql){
        Logger::debug($sql);
        $params=func_get_args();
        array_shift($params);
        if(isset($params[0]) &&count($params)==1 && is_array($params[0])){
            $params=$params[0];
        }
        $this->lastQuery=array($sql,$params);
        try{
            $dbh=$this->getDBH();
            $sth=$dbh->prepare($sql);
            if(!$sth){
                Logger::error("sql:$sql;".var_export($dbh->errorInfo(),true)
                    .var_export($dbh->errorCode(),true)
                    .var_export($params,true)
                );
            }
            $res=$sth->execute($params);
            if($res===false){
                Logger::error("sql:$sql;".var_export($dbh->errorInfo(),true)
                    .var_export($dbh->errorCode(),true)
                    .var_export($params,true)
                );
            }
        }catch(Exception $e){
            throw new SystemException("exec sql error, ".$this->dsn." '".$this->getLastQuery()."'", 500);
        }

        return array($this->dbh,$sth);
    }
    public function getLastQuery(){
        $params=$this->lastQuery[1];
        $i=0;
        return preg_replace_callback('/\?/',function($matches)use($params,&$i){
            if(is_string($params[$i])){
                $ret="'{$params[$i]}'";
            }else{
                $ret=$params[$i];
            }
            $i++;
            return $ret;
        },$this->lastQuery[0]);
    }

    public function query($sql){
        list($dbh,$sth)=call_user_func_array([$this,'execute_sql'],func_get_args());
        return $sth->fetchAll( PDO::FETCH_ASSOC );
    }
    public function queryForCount($sql){
        list($dbh,$sth)=call_user_func_array([$this,'execute_sql'],func_get_args());
        $res = $sth->fetch( PDO::FETCH_ASSOC );
        if ($res){
        	return current($res);
        }
        return 0;
    }

    public function queryForOne($sql){
        list($dbh,$sth)=call_user_func_array([$this,'execute_sql'],func_get_args());
        return $sth->fetch( PDO::FETCH_ASSOC );
    }

    public function insert(){
        list($dbh,$sth)=call_user_func_array([$this,'execute_sql'],func_get_args());
        return $dbh->lastInsertId();
    }

    public function update(){
        list($dbh,$sth)=call_user_func_array([$this,'execute_sql'],func_get_args());
        return $sth->rowCount();
    }

    public function delete(){
        list($dbh,$sth)=call_user_func_array([$this,'execute_sql'],func_get_args());
        return $sth->rowCount();
    }
}
