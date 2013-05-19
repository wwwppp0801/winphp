<?php
class DB{
    private static $dsn;
    private static $username;
    private static $password;
    private static $dbh;
    public static function init($dsn,$username,$password){
        //$this->user = 'root'; 
        //$this->pass = ''; 
        //$dns = $this->engine.':dbname='.$this->database.";host=".$this->host; 
        list(self::$dsn,self::$username,self::$password)=array($dsn,$username,$password);
    
    }
    private static function execute_sql($sql){
        $params=func_get_args();
        if (count($params)>2||is_scalar($params[1])){
            array_shift($params);
        }else{
            $params=$params[1];
        }
        try{
            if(!self::$dbh){
                self::$dbh = new PDO(self::$dsn,self::$username,self::$password); 
            }
            $sth=self::$dbh->prepare($sql);
            $res=$sth->execute($params);
            if($res===false){
                Soso_Logger::error(var_export(self::$dbh->errorInfo(),true)
                    .var_export(self::$dbh->errorCode,true)
                    .var_export($params,true)
                );
            }
        }catch(Exception $e){
            throw new SystemException("exec sql error, ".self::$dsn." '$sql'");
        }
        
        return array(self::$dbh,$sth);
    }

    public static function query($sql){
        list($dbh,$sth)=call_user_func_array('DB::execute_sql',func_get_args());
        return $sth->fetchAll( PDO::FETCH_ASSOC );
    }
    public static function queryForCount($sql){
        list($dbh,$sth)=call_user_func_array('DB::execute_sql',func_get_args());
        return current($sth->fetch( PDO::FETCH_ASSOC ));
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
