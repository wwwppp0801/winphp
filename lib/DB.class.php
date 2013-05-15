<?php
class DB{
    private static function execute_sql($sql){
        $params=func_get_args();
        if (count($params)>2||is_scalar($params[1])){
            array_shift($params);
        }else{
            $params=$params[1];
        }
        try{
            if(!self::$dbh){
                self::$dbh = new PDO("sqlite:".PROJECT_ROOT."/db/sqlite.db"); 
            }
            $sth=self::$dbh->prepare($sql);
            $sth->execute($params);
        }catch(Exception $e){
            var_dump($e);
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
