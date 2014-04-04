<?php
class DBTool{
    public static function showTables($pPattern="",$pIndex=0){
        $sql = "show tables like '$pPattern%';";
        $list=DB::query($sql);
        if (!$list) {
            return [];
        }
        return Utils::array2Simple($list);
    }
    public static function descTable($table){
        $list=DB::query("desc `$table`;");
        if (!$list) {
            return [];
        }
        return $list;
    }
}
