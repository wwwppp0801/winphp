<?php
class DBTool{
    public static function showTables($pPattern="",$pIndex=0){
        if(DB::getDriverName()=='sqlite'){
            $sql = "select name from sqlite_master where type='table' and name not like 'sqlite_%' order by name;";
        }else{
            //mysql
            $sql = "show tables like '$pPattern%';";
        }
        $list=DB::query($sql);
        if (!$list) {
            return [];
        }
        return Utils::array2Simple($list);
    }
    public static function descTable($table){
        if(DB::getDriverName()=='sqlite'){
            $sql=DB::queryForOne("select sql from sqlite_master where type='table' and name=?;",$table);
            if(!$sql){
                return [];
            }
            $sql=$sql['sql'];
            preg_match_all('/\[([^]]+)\](.*?)(--.*)?,?$/mi',$sql,$matches);
            //var_dump($sql,$matches);
            $fields=[];
            foreach (array_keys($matches[0]) as $i){
                $fieldName=$matches[1][$i];
                $tokens=array_filter(array_map(strtolower,array_map(trim,preg_split("/\s+/",$matches[2][$i]))));
                $fields[]=[
                    'Field'=>$fieldName,
                    'Key'=>in_array('key',$tokens),
                    'Type'=>$tokens[0],
                ];
            }
            return $fields;
        }

        $list=DB::query("desc `$table`;");
        if (!$list) {
            return [];
        }
        return $list;
    }
}
