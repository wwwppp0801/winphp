<?php
//创建一个测试用的sqlite3数据库
//example: sqlite3 order_food.db '.read script/testModel.sql'
assert_options(ASSERT_ACTIVE, 1);
//DB::init("mysql:host=localhost;dbname=inav_proj;port:3306",'root','');
class Bargain extends DBModel{
    public function getFieldList(){
        static $FIELD_LIST=array (
            array('name'=>'id',         'type'=>"int",'key'=>true,'defalut'=>null,'null'=>false,),
            array('name'=>'title',      'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'image',      'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'all_count',  'type'=>"int",'defalut'=>null,'null'=>false,),
        );
        return $FIELD_LIST;
    }
}
$b=new Bargain();
$b->delete(true);
$b->mTitle="title";
$b->mImage="image";
$b->mAllCount=1;
$b->save();


$b=new Bargain();
$b->select();
var_dump($b->getData());
$b->mImage="aaaaaaaaaaaaaaaaa";
$b->save();
assert("\$b->mImage === 'aaaaaaaaaaaaaaaaa'");
$b->clear();
assert("\$b->mImage === null");

$b->select();
assert("\$b->mImage === 'aaaaaaaaaaaaaaaaa'");

$b->update([
    'all_count'=>['all_count+1',DBTable::NO_ESCAPE],
]);

$b->select();
var_dump($b->getData());
assert("\$b->mAllCount == 2");




$objs=$b->orderby('id','desc')->find();
assert("count(\$objs) === 1");
assert("\$objs[0]->mTitle== 'title'");

var_dump("max memory: ".(memory_get_peak_usage(true)/1000)."KB");
