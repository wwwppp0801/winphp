<?php
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
require (ROOT_PATH."/config/classpath.php");
require (ROOT_PATH."/config/conf.php");
$tables=array_slice($argv,1);
if(!$tables){
    $tables=DBTool::showTables();
}
$modelTemplate = DefaultViewSetting::getTemplateWithSettings();
foreach ($tables as $table){
    $fields=DBTool::descTable($table);
    $paths=array_map("ucfirst",explode("_",$table));
    $className="Base_".implode("_",$paths);
    $fileName=array_pop($paths).".class.php";
    $realpath=ROOT_PATH."/app/Base/".implode("/",$paths);
    @mkdir($realpath,0777,true);
    $modelTemplate->assign('fields',$fields);
    $modelTemplate->assign('table',$table);
    $modelTemplate->assign('className',$className);
    $baseModelClass=<<<END_CLASS
<?php
class {%\$className%} extends DBModel{

    public function getFieldList(){
        static \$FIELD_LIST=[
            {%foreach \$fields as \$field%}{%strip%}
            [
            'name'=>'{%\$field.Field%}',
            'type'=>
                {%if preg_match('/int/',\$field.Type)%}
                "int",
                {%else%}
                "string",
                {%/if%}
            'key'=>{%var_export(!!\$field.Key,1)%},
            'defalut'=>{%var_export(\$field.Default,1)%},
            'null'=>{%if \$field.Null=='YES'%}true{%else%}false{%/if%},
            ],
            {%/strip%}
            {%/foreach%}
        ];
        return \$FIELD_LIST;
    }
}
END_CLASS;
    file_put_contents("$realpath/$fileName",$modelTemplate->fetch("string:".$baseModelClass));
    $modelTemplate->clearAllAssign();
    //var_dump($table,$fields);
    echo "gen table $table;\n";
}
