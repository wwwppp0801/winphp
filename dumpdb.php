<?php

define('ROOT_PATH', dirname(__FILE__));
require (ROOT_PATH."/config/classpath.php");
require (ROOT_PATH."/config/conf.php");

list($dbh,$sth)=DB::execute_sql("select * from users where realname <> '';");
$fp=fopen(ROOT_PATH.'/db/'.date("Y_m_d"),"w+");
while($row=$sth->fetch( PDO::FETCH_ASSOC)){
    fputcsv($fp,array_values($row));
}
