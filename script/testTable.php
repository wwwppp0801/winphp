<?php
assert_options(ASSERT_ACTIVE, 1);
DB::init("mysql:host=localhost;dbname=inav_proj;port:3306",'root','');
$table=new Table("bargain");
$row=$table->limit(2,2)->select();
assert("\$row['id'] === '3'");

$row=$table->addWhere('id',100)->select();
assert("\$row['id'] === '100'");
$rows=$table->addWhere('id',100,">")->limit(3)->find();
assert("count(\$rows) === 3");
assert("\$rows[0]['id'] == 101");

$rows=$table->addWhere('id',100,">")->orderby('id','desc')->limit(3)->find();
assert("count(\$rows) === 3");
assert("\$rows[0]['id'] == 5855");


$rows=$table->addWhere('id',5854,">")->addWhere("id",1,"<=",'or')->orderby('id','desc')->find();
assert("count(\$rows) === 2");
assert("\$rows[1]['id'] == 1");

foreach($table->addWhere("id",10,"<")->orderby("id")->iterator() as $i=>$row){
    //var_dump($i,$row['id']);
}
assert("\$row['id'] === '9'");
assert("\$i == 8");

$table->iterator();
$rows=$table->setCols(array("city"))->addComputedCol('count(*)','c')->groupby('city')->orderby("city")->find();
assert("count(\$rows) === 72");
assert("count(\$rows[0]) === 2");
assert("\$rows[71]['city'] === '黄山'");
assert("\$rows[71]['c'] == 4");




$table=new Table("fav_site");
$id=$table->insert(array("qq"=>'12031389','url'=>'http://www.wangp.org','name'=>'博客1'));
$row=$table->addWhere('id',$id)->select();
assert("\$id>0");
assert("\$row['name']=='博客1'");

$table->addWhere('id',$id)->update(array('name'=>'博客2'));
$row=$table->addWhere('id',$id)->select();
assert("\$row['name']=='博客2'");
$table->delete(true);
#$table->addWhere('id',$id)->delete();
