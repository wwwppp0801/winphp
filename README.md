## 项目介绍 ##
一个php框架，主要功能如下：
* 基于php5.4以上版本开发
* 基于php的namespace机制实现的autoload
* 简易的mvc
* 简易的orm框架，可支持mysql和sqlite（基于pdo_mysql和pdo_sqlite）
* 程序化配置的admin管理界面，很容易实现可扩展的后台管理系统
* 简易的脚手架功能，可以使用php内置的web server搭建测试环境，可以自动生成orm类和admin界面类



## 脚手架 ##
### 在ROOT_PATH下建立常规的目录结构 ###
```shell
sh ${WINPHP_PATH}/start.sh ${ROOT_PATH}
```

### 根据数据库中的表结构自动生成orm类和admin界面 ###
```shell
cd ${ROOT_PATH};php ${WINPHP_PATH}/webroot/install.php
```
* 以上命令，对于数据库中的“my_table”表默认会生成以下文件
  * ${ROOT_PATH}/app/Base/My/Table.class.php
  * ${ROOT_PATH}/app/My/Table.class.php
  * ${ROOT_PATH}/app/controller/admin/my/TableController.php
* 后两个文件，如果文件已经存在，则不会被覆盖。第一个文件，一定会被覆盖（所以不要在这个文件里添加自己的代码）

### 使用php内置的server进行调试 ###
```shell
php  -S locahost:8080 -t ${ROOT_PATH}/webroot ${ROOT_PATH}/webroot/route.php
```


### 执行基于框架的php脚本 ###
* 脚本文件都要放在 ${ROOT_PATH}/script 目录下
* 假设有脚本文件 ${ROOT_PATH}/script/test.php ，执行方法是
```shell
php ${ROOT_PATH}/webroot/route.php test.php
```
* test.php中可以引用所有项目的配置、类

## 目录结构 ##
* 分两个目录，应用目录（ROOT_PATH）和框架目录（WINPHP_PATH）
* app 放项目的php类文件，靠名字空间查找
* app/controller 放对外的controller类，靠url查找
* webroot 直接对外提供服务的目录（最好由nginx直接服务），除了route.php，其它都是静态文件
* conf/conf.php 配置文件，内有winphp路径、数据库等配置
* DEBUG 有这个文件的时候，自动进入调试模式，logger会打出debug信息
* lib php类也可以放这个目录，也能自动找到
* template smarty模版的根目录
* script 放项目脚本的目录

## 请求处理流程 ##
以/weixin/message请求为例
* nginx
* route.php
* WINPHP_PATH/lib/UrlMapper.class.php
* ROOT_PATH/app/controller/WeixinController.class.php的Interceptor的beforeAction方法
* ROOT_PATH/app/controller/WeixinController.class.php中的messageAction方法
* ROOT_PATH/app/controller/WeixinController.class.php的Interceptor的afterAction方法

action 方法的返回值：
* 两个元素的数组，第一个参数是view，第二个参数是data，如:["index.tpl",['name'=>'wangpeng']]
* 模版的根目录在ROOT_PATH/template
* 如需重定向可返回如["redirect:http://www.baidu.com"];
* 如需显示json数据可返回如["json:",['json'=>$data]]（除json外别的key都不读）


## 找类的逻辑 ##

### url找controller ###
* / 使用app/controller/IndexController::indexAction
* /weixin 使用app/controller/WeixinController::indexAction
* /weixin/message 使用app/controller/WeixinController::messageAction, 或app/controller/weixin/MessageController::indexAction
* /weixin/message/delete 使用app/controller/weixin/MessageController::deleteAction 或 使用app/controller/weixin/message/DeleteController::indexAction




### autoload 找类的逻辑 ###
* 在ROOT_PATH/app/，WINPHP_PATH/app，ROOT_PATH/lib，WINPHP_PATH/lib，找
* Weixin\Message 在Weixin目录下，Message类


## 数据库ORM功能 ##
DB、DBTable、DBModel三个类，执行不同层面的功能
* DB类，提供直接执行sql的功能
```php
//简单的select，queryForOne只返回一条结果，是个关联数组
$userinfo=DB::queryForOne("select * from users where openid=?",$openid);
//query返回查询结果的数组
$userinfo=DB::query("select * from users where nickname=?",$nickname);
//insert，多个参数顺序传入，返回值是新记录的自增id
$userid=DB::insert("insert into users(accesstoken,openid,nickname,gender,figure_url) values(?,?,?,?,?)",
                $access_token,$openid,$userinfo['nickname'],$userinfo['gender'],$userinfo['figure_url']
);
//update，返回值当然是affected rows
DB::update("update users set realname=?,phone=?,address=?,qq=? where openid=?",
            $_GET["realname"],
            $_GET["phone"],
            $_GET["address"],
            $_GET["qq"],
            $openid
);
```


* DBTable类，提供拼sql语句的功能
```php
//依赖DB作为底层接口
DB::init("mysql:host=localhost;dbname=inav_proj;port:3306",'root','');
//构造函数的参数是表名
$table=new DBTable("bargain");
//select只返回1条结果,这条命令实际上是返回第3条结果
$row=$table->limit(2,2)->select();
//返回id=100的结果
$row=$table->addWhere('id',100)->select();
assert("\$row['id'] === '100'");
//find返回所有符合条件的结果，这里会返回3条id>100的结果
$rows=$table->addWhere('id',100,">")->limit(3)->find();
assert("count(\$rows) === 3");
assert("\$rows[0]['id'] == 101");
//当然，也可以加上排序的条件
$rows=$table->addWhere('id',100,">")->orderby('id','desc')->limit(3)->find();
assert("count(\$rows) === 3");
assert("\$rows[0]['id'] == 5855");

//当然可以增加多个where条件
$rows=$table->addWhere('id',5854,">")->addWhere("id",1,"<=",'or')->orderby('id','desc')->find();
assert("count(\$rows) === 2");
assert("\$rows[1]['id'] == 1");
//iterator方法和find用法一样，不同之处在于它返回的不是数组，而是一个迭代器。访问大量结果的时候，用迭代器遍历可以节省内存
foreach($table->addWhere("id",10,"<")->orderby("id")->iterator() as $i=>$row){
    var_dump($i,$row['id']);
}
assert("\$row['id'] === '9'");
assert("\$i == 8");

$table->iterator();
//可以groupby，可以增加sql计算出的列，也可以指定要返回的列（setCols），
$rows=$table->setCols(array("city"))->addComputedCol('count(*)','c')->groupby('city')->orderby("city")->find();
assert("count(\$rows) === 72");
assert("count(\$rows[0]) === 2");
assert("\$rows[71]['city'] === '黄山'");
assert("\$rows[71]['c'] == 4");




$table=new DBTable("fav_site");
//插入新记录，只需传入关联数组，返回自增id
$id=$table->insert(array("qq"=>'12031389','url'=>'http://www.wangp.org','name'=>'博客1'));
$row=$table->addWhere('id',$id)->select();
assert("\$id>0");
assert("\$row['name']=='博客1'");

//update 当然要加where条件指定被update的记录
$table->addWhere('id',$id)->update(array('name'=>'博客2'));
$row=$table->addWhere('id',$id)->select();
assert("\$row['name']=='博客2'");

//update 字段自增的case
$table->update([
    'all_count'=>['all_count+1',DBTable::NO_ESCAPE],
]);

//delete，如果不加任何where条件，必须用参数true来强制执行。这是为了防止不小心清空整个表
$table->delete(true);
#$table->addWhere('id',$id)->delete();


```

```php
//DBModel是一个被继承的虚类，在配置项中指定字段名和字段类型。当然也可以在里面实现其他行为
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
//根据id查一个Bargain对象的简单方法：
Bargain::getById($id);


$b=new Bargain();
//支持addWhere，但如果指定了id，id会作为唯一的查询条件，返回对象（或者查不到返回false）
$b->addWhere("id",$id)->select();
//可以用访问属性的方式访问所有字段
var_dump($b->mImage,$b->mAllCount);



//使用save可以修改一个属性
$b->mImage="aaaaaaaaaaaaaaaaa";
$b->save();
var_dump($b->mImage);


//拿到对象的关联数组数据
$b->getData();

//获取单个字段，推荐第一种方式
$b->mAllCount;
$b->getData("all_count");


//修改记录，如果有id，执行update，如果没有id，执行insert(id保存在$b->mId)
$b->mUrl="http://www.baidu.com/";
//也可以用setData设置字段
$b->setData("url","http://www.baidu.com/");
$b->save();

//update 当然要加where条件指定被update的记录
$b->addWhere('id',$id)->update(array('name'=>'博客2'));
$b=$b->addWhere('id',$id)->select();

//update 字段自增的case
$b->update([
    'all_count'=>['all_count+1',DBTable::NO_ESCAPE],
]);


//强制insert,返回新记录的id
$id=$b->insert(['url'=>"xxx","address"=>"yyy"]);


//clear可以清空对象里的所有数据
$b->clear();
var_dump($b->mImage);//应该为空

//重新查一次，可以检查刚才的save是否生效
$b=$b->addWhere('id',10)->select();
var_dump($b->mImage);

//同样也支持iterator和find，不同之处在于返回值不再是关联数组，而是一个真正的对象
foreach($b->addWhere("id",10,"<")->iterator() as $model){
    var_dump($model->mId);
}
foreach($b->addWhere("id",10,"<")->find() as $model){
    var_dump($model->mId);
}

//一般的用法和table相差不远
$objs=$b->addWhere('id',5854,">")->addWhere("id",1,"<=",'or')->orderby('id','desc')->find();
assert("count(\$objs) === 2");
assert("\$objs[1]->mId== 1");

```
