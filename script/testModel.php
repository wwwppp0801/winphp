<?php
assert_options(ASSERT_ACTIVE, 1);
DB::init("mysql:host=localhost;dbname=inav_proj;port:3306",'root','');
class Bargain extends DBModel{
    public function getFieldList(){
        static $FIELD_LIST=array (
            array('name'=>'id',         'type'=>"int",'key'=>true,'defalut'=>null,'null'=>false,),
            array('name'=>'title',      'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'image',      'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'price',      'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'value',      'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'rebate',     'type'=>"float",'defalut'=>null,'null'=>false,),
            array('name'=>'bought',     'type'=>"int",'defalut'=>null,'null'=>false,),
            array('name'=>'source',     'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'url',        'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'address',    'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'endtime',    'type'=>"int",'defalut'=>null,'null'=>false,),
            array('name'=>'fanli',      'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'type',       'type'=>"int",'defalut'=>null,'null'=>false,),
            array('name'=>'city',       'type'=>"string",'defalut'=>null,'null'=>false,),
            array('name'=>'all_count',  'type'=>"int",'defalut'=>null,'null'=>false,),
        );
        return $FIELD_LIST;
    }
}
$b=new Bargain();
$b->mId=1;
$b->select();
var_dump($b->getData());
$b->mImage="aaaaaaaaaaaaaaaaa";
$b->save();
var_dump($b->mImage);
$b->clear();
var_dump($b->mImage);
$b->mId=1;
$b->select();
var_dump($b->mImage);
foreach($b->addWhere("id",10,"<")->iterator() as $model){
    var_dump($model->mId);
}
foreach($b->addWhere("id",10,"<")->find() as $model){
    var_dump($model->mId);
}



$objs=$b->addWhere('id',5854,">")->addWhere("id",1,"<=",'or')->orderby('id','desc')->find();
assert("count(\$objs) === 2");
assert("\$objs[1]->mId== 1");

var_dump(memory_get_peak_usage(true)/1000);
