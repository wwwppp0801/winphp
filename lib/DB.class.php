<?php
class DB{
    private static $dsn;
    private static $username;
    private static $password;
    private static $dbh;
    private static $lastQuery;
    public static function init($dsn,$username,$password){
        //$this->user = 'root';
        //$this->pass = '';
        //$dns = $this->engine.':dbname='.$this->database.";host=".$this->host;
        list(self::$dsn,self::$username,self::$password)=array($dsn,$username,$password);

    }
    public static function beginTransaction(){
        Logger::debug('beginTransaction');
        self::getDBH()->beginTransaction();
    }
    public static function commit(){
        Logger::debug('commitTransaction');
        self::getDBH()->commit();
    }
    public static function rollBack(){
        Logger::debug('rollbackTransaction');
        self::getDBH()->rollBack();
    }
    private static function getDBH(){
        if(!self::$dbh){
            self::$dbh = new PDO(self::$dsn,self::$username,self::$password);
        }
        return self::$dbh;
    }
    public static function execute_sql($sql){
        Logger::debug($sql);
        $params=func_get_args();
        array_shift($params);
        if(isset($params[0]) &&count($params)==1 && is_array($params[0])){
            $params=$params[0];
        }
        self::$lastQuery=array($sql,$params);
        try{
            $dbh=self::getDBH();
            $sth=$dbh->prepare($sql);
            $res=$sth->execute($params);
            if($res===false){
                Logger::error("sql:$sql;".var_export($dbh->errorInfo(),true)
                    .var_export($dbh->errorCode(),true)
                    .var_export($params,true)
                );
            }
        }catch(Exception $e){
            throw new SystemException("exec sql error, ".self::$dsn." '".self::getLastQuery()."'");
        }

        return array(self::$dbh,$sth);
    }
    public static function getLastQuery(){
        $params=self::$lastQuery[1];
        $i=0;
        return preg_replace_callback('/\?/',function($matches)use($params,&$i){
            if(is_string($params[$i])){
                $ret="'{$params[$i]}'";
            }else{
                $ret=$params[$i];
            }
            $i++;
            return $ret;
        },self::$lastQuery[0]);
    }

    public static function query($sql){
        list($dbh,$sth)=call_user_func_array('DB::execute_sql',func_get_args());
        return $sth->fetchAll( PDO::FETCH_ASSOC );
    }
    public static function queryForCount($sql){
        list($dbh,$sth)=call_user_func_array('DB::execute_sql',func_get_args());
        $res = $sth->fetch( PDO::FETCH_ASSOC );
        if ($res){
        	return current($res);
        }
        return 0;
    }

    public static function queryForOne($sql){
        list($dbh,$sth)=call_user_func_array('DB::execute_sql',func_get_args());
        return $sth->fetch( PDO::FETCH_ASSOC );
    }

    public static function insert(){
        list($dbh,$sth)=call_user_func_array('DB::execute_sql',func_get_args());
        return $dbh->lastInsertId();
    }

    public static function update(){
        list($dbh,$sth)=call_user_func_array('DB::execute_sql',func_get_args());
        return $sth->rowCount();
    }

    public static function delete(){
        list($dbh,$sth)=call_user_func_array('DB::execute_sql',func_get_args());
        return $sth->rowCount();
    }
}
