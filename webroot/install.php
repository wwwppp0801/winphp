<?php
#define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
define('ROOT_PATH', getcwd());
#define('WINPHP_PATH',"/home/wp/Projects/winphp/");
require (ROOT_PATH."/config/conf.php");
#require (WINPHP_PATH."/config/classpath.php");
$tables=array_slice($argv,1);
if(!$tables){
    $tables=DBTool::showTables();
}
$modelTemplate = DefaultViewSetting::getTemplateWithSettings();
foreach ($tables as $table){
    $fields=DBTool::descTable($table);
    $paths=array_map("ucfirst",explode("_",$table));
    array_unshift($paths,"Base");
    $className=array_pop($paths);
    $namespaces=implode("\\",$paths);
    $fileName=$className.".class.php";
    $realpath=ROOT_PATH."/app/".implode("/",$paths);
    @mkdir($realpath,0777,true);
    $modelTemplate->assign('fields',$fields);
    $modelTemplate->assign('table',$table);
    $modelTemplate->assign('className',$className);
    $modelTemplate->assign('namespaces',$namespaces);
    $baseModelClass=<<<END_CLASS
<?php
namespace {%\$namespaces%};
use DBModel;
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
    echo "gen table base model $table;\n";
}

//generate model class

foreach ($tables as $table){
    $fields=DBTool::descTable($table);
    $paths=array_map("ucfirst",explode("_",$table));
    //array_unshift($paths,"Base");
    $className=array_pop($paths);
    $namespaces=implode("\\",$paths);
    $fileName=$className.".class.php";
    $realpath=ROOT_PATH."/app/".implode("/",$paths);
    @mkdir($realpath,0777,true);
    
    foreach($fields as $i=>$field){
        $fields[$i]['AttrName']=DBModel::zipKey($fields[$i]['Field']);
    }
    $modelTemplate->assign('fields',$fields);
    $modelTemplate->assign('table',$table);
    $modelTemplate->assign('className',$className);
    $modelTemplate->assign('namespaces',$namespaces);
    $baseModelClass=<<<END_CLASS
<?php
{%if \$namespaces%}
namespace {%\$namespaces%};
{%/if%}
class {%\$className%} extends \Base\{%if \$namespaces%}{%\$namespaces%}\{%/if%}{%\$className%} {

}

END_CLASS;

    if(!file_exists("$realpath/$fileName")){
        file_put_contents("$realpath/$fileName",$modelTemplate->fetch("string:".$baseModelClass));
        //var_dump($table,$fields);
        echo "gen table model $table;\n";
    }else{
        echo "table $table model already has an admin;\n";
    }

    $modelTemplate->clearAllAssign();
}


//generate base admin

foreach ($tables as $table){
    $fields=DBTool::descTable($table);
    $paths=array_map("ucfirst",explode("_",$table));
    //array_unshift($paths,"Base");
    $className=array_pop($paths);
    $namespaces=implode("\\",$paths);
    $fileName=$className."Controller.class.php";
    $realpath=ROOT_PATH."/app/controller/admin/".implode("/",array_map("strtolower",$paths));
    @mkdir($realpath,0777,true);
    
    foreach($fields as $i=>$field){
        $fields[$i]['AttrName']=DBModel::zipKey($fields[$i]['Field']);
    }
    $modelTemplate->assign('fields',$fields);
    $modelTemplate->assign('table',$table);
    $modelTemplate->assign('className',$className);
    $modelTemplate->assign('namespaces',$namespaces);
    $baseModelClass=<<<END_CLASS
<?php
use {%if \$namespaces%}\{%\$namespaces%}{%/if%}\{%\$className%};
class {%\$className%}Controller extends Page\Admin\Base {
    public function __construct(){
        parent::__construct();
        //\$this->addInterceptor(new AdminLoginInterceptor());
        //\$this->addInterceptor(new AdminAuthInterceptor());
        //\$this->addInterceptor(new AdminLogInterceptor());
        \$this->model=new {%\$className%}();
        \$this->model->orderBy("id","desc");
        //\$this->model->on('beforeinsert','beforeinsert',\$this);
        //\$this->model->on('beforeupdate','beforeupdate',\$this);

        \$this->form=new Form(array(
{%foreach \$fields as \$field%}{%if \$field.Field!='id'%}
            ['name'=>'{%\$field.Field%}','label'=>'{%\$field.Field%}','type'=>"text",'default'=>null,'required'=>false,],
{%/if%}{%/foreach%}
        ));
        \$this->list_display=array(
            'id',
{%foreach \$fields as \$field%}{%if \$field.Field!='id'%}
            ['label'=>'{%\$field.Field%}','field'=>function(\$model){
                return \$model->{%\$field.AttrName%};
            }],
{%/if%}{%/foreach%}
        );
        /*
        \$this->list_filter=array(
{%foreach \$fields as \$field%}{%if \$field.Field!='id'%}
            new Page_Admin_TextFilter(['name'=>'{%\$field.Field%}','paramName'=>'{%\$field.Field%}','fusion'=>false]),
{%/if%}{%/foreach%}
        );*/

    }

}

END_CLASS;

    if(!file_exists("$realpath/$fileName")){
        file_put_contents("$realpath/$fileName",$modelTemplate->fetch("string:".$baseModelClass));
        //var_dump($table,$fields);
        echo "gen table admin $table;\n";
    }else{
        echo "table $table already has an admin;\n";
    }

    $modelTemplate->clearAllAssign();
}

foreach ($tables as $table)
{
	$default_action = ['insert', 'delete', 'update', 'select'];
    $sql_tpl = "insert ignore into admin_psermission (name, module, action, ptype) values ('%s', '%s', '%s', '%s');";
    foreach ($default_action as $action){
        $name = $table."@".$action;
        $module = $table;
        $ptype = '0';        
    	$sql = sprintf($sql_tpl, $name, $module, $action, $ptype);
    	DB::execute_sql($sql);
    	echo $sql."\n";
    }
}
