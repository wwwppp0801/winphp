<?php
assert_options(ASSERT_ACTIVE, 1);
DB::init("mysql:host=localhost;dbname=inav_proj;port:3306",'root','');
class Bargain extends DBModel{
    public static $FIELD_LIST=array (
        0 => 'id',
        1 => 'title',
        2 => 'image',
        3 => 'price',
        4 => 'value',
        5 => 'rebate',
        6 => 'bought',
        7 => 'source',
        8 => 'url',
        9 => 'address',
        10 => 'endtime',
        11 => 'fanli',
        12 => 'type',
        13 => 'city',
        14 => 'all_count',
    );

    public function getFieldList(){
        return self::$FIELD_LIST;
    }
}
$b=new Bargain();
$b->mId=1;
$b->select();
var_dump($b->mImage);
