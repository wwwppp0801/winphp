<?php
class DBModelIterator implements Iterator{
    private $stmt;
    private $cursor = -1;
    private $valid = true;
    private $modelClass;

    function __construct ($stmt,$modelClass)    {
        $this->stmt = $stmt;
        $this->modelClass=$modelClass;
        $this->next();
    }

    function current(){
        return $this->obj;
    }

    function next() {
        $this->cursor++;
        $data= $this->stmt->fetch (PDO::FETCH_ASSOC);
        if (empty ($data)){
            $this->valid = false;
        }
        if($this->obj){
            $this->obj->setData($data);
        }else{
            $this->obj=new $this->modelClass($data);
        }
    }

    function key()  {
        return $this->cursor;
    }

    function valid()    {
        return $this->valid;
    }

    function rewind()   {
        if($this->cursor!=0){
            throw new Exception("pdo iterator can not be rewind!");
        }
    }

}
abstract class DBModel{
    protected $_table;
    protected $_data;
    use EventEmitter;
    public function __construct($data=array()){
        $this->_data=$data;
    }
    public function setAutoClear($auto_clear=true){
        $this->getTable()->setAutoClear($auto_clear);
    }
    public function getAutoClear(){
        $this->getTable()->getAutoClear();
    }
    public function setDBObject(DBObject $dbobject){
        $this->getTable()->setDBObject($dbobject);
    }
    public function clear(){
        $this->_data=array();
        if($this->_table){
            $this->_table->clear();
        }
        return $this;
    }
    public function setData($data){
        $tmpData=array();
        foreach($this->getFieldList() as $field){
            if(isset($data[$field['name']])){
                $tmpData[$field['name']]=$data[$field['name']];
            }
        }
        $this->_data=$tmpData;
        return $this;
    }
    public function setDataMerge($data){
        $tmpData=array();
        foreach($this->getFieldList() as $field){
            if(isset($data[$field['name']])){
                $tmpData[$field['name']]=$data[$field['name']];
            }
        }
        if(!$this->_data){
            $this->_data=$tmpData;
        }else{
            $this->_data=array_merge($this->_data,$tmpData);
        }
        return $this;
    }

    public function getData($field = null){
        if(!is_null($field)){
            return $this->__get(self::zipKey($field));
        }
        $data=$this->_data;
        foreach($this->getFieldList() as $f){
            if(
                isset($data[$f['name']])
                && !is_null($data[$f['name']])
            ){
                if($f['type']=='int'){
                    $data[$f['name']]=intval($data[$f['name']]);
                }
            }
        }
        return $data;
    }

    protected function getTableName(){
        $tableName = preg_replace('/(.)([A-Z])/', '${1}_${2}',get_class($this));
        $tableName = preg_replace('/\\\\/', '',$tableName);
        return strtolower($tableName);
    }
    
    /**
     * 获取在权限系统中的对象表名，暂时直接用getTableName
     * TODO 分表后升级此方法
     * @return string 权限系统中的对象表名
     */
    public function getTablePermissionName(){
    	return $this->getTableName();
    }
    
    protected function getTable(){
        if(!$this->_table){
            $this->_table=new DBTable($this->getTableName());
        }
        return $this->_table;
    }
    public function save(){
        $table=$this->getTable();
        if($this->mId){
            if($this->trigger("before_update",[$this,$this->_data])!==false){
                $ret=$table->addWhere("id",$this->mId)->update($this->_data);
                $this->trigger("after_update",[$this,$ret,$this->_data]);
            }
            return $ret;
        }else{
            $this->trigger("before_insert",[$this,$this->_data]);
            $id=$table->insert($this->_data);
            $this->mId=$id;
            $this->trigger("after_insert",[$this,$this->mId,$this->_data]);
            return !!$id;
        }
        return false;
    }
    public function select(){
        $table=$this->getTable();
        if($this->mId){
            $table->addWhere('id',$this->mId);
        }
        $data=$table->select();
        if($data){
            $this->_data=$data;
            return $this;
        }
        return false;
    }
    public function delete(){
        $table=$this->getTable();
        if($this->mId){
            $table->addWhere("id",$this->mId);
        }
        //return $table->delete();
        if($this->trigger("before_delete",[$this])!==false){
            $ret=call_user_func_array(array($table,"delete"),func_get_args());
            $this->trigger("after_delete",[$this]);
        }
        return $ret;
        //return false;
    }
    public function addWhere(){
        $table=$this->getTable();
        call_user_func_array(array($table,"addWhere"),func_get_args());
        return $this;
    }
    public function addWhereRaw(){
        $table=$this->getTable();
        call_user_func_array(array($table,"addWhereRaw"),func_get_args());
        return $this;
    }
    public function orderBy(){
        $table=$this->getTable();
        call_user_func_array(array($table,"orderBy"),func_get_args());
        return $this;
    }
    public function groupBy(){
        $table=$this->getTable();
        call_user_func_array(array($table,"groupBy"),func_get_args());
        return $this;
    }
    public function setCols(){
        $table=$this->getTable();
        call_user_func_array(array($table,"setCols"),func_get_args());
        return $this;
    }
    public function limit(){
        $table=$this->getTable();
        call_user_func_array(array($table,"limit"),func_get_args());
        return $this;
    }
    public function addComputedCol(){
        $table=$this->getTable();
        call_user_func_array(array($table,"addComputedCol"),func_get_args());
        return $this;
    }
    public function count(){
        $table=$this->getTable();
        return call_user_func_array(array($table,"count"),func_get_args());
    }
    public function find(){
        $datas= $this->getTable()->find();
        $class_name=get_class($this);
        return array_map(function($data)use($class_name){
            return new $class_name($data);
        },$datas);
    }
    public function findData($fields){
        if(is_string($fields)){
            $fields=[$fields];
        }
        $models=$this->find();
        $results=[];
        foreach($models as $model){
            $result=[];
            foreach($fields as $field){
                $result[$field]=$model->getData($field);
            }
            if(count($fields)==1){
                $result=current($result);
            }
            $results[]=$result;
        }
        return $results;
    }
    public function findMap($fieldName='id'){
        $field=null;
        foreach($this->getFieldList() as $f){
            if($f['name']==$fieldName){
                //return $key;
                $field=$f;
            }
        }
        if(!$field){
            throw new Exception("no field : $fieldName");
        }
        $objs=$this->find();
        
        $map=[];
        foreach($objs as $obj){
            if($obj){
                $map[$obj->getData($fieldName)]=$obj;
            }
        }
        return $map;
    }
    public function insert($data){
        $this->setData($data);
        //$this->setDataMerge($data);
        $this->trigger("before_insert",[$this]);
        $id=$this->getTable()->insert($this->_data);
        $this->mId=$id;
        $this->trigger("after_insert",[$this,$this->mId,$this->_data]);
        return $id;
    }
    public function update($data){
        if($this->mId){
            $this->addWhere("id",$this->mId);
            if($this->trigger("before_update",[$this,$data])!==false){
                $ret=$this->getTable()->update($data);
                if($ret){
                    $this->setDataMerge($data);
                }
                $this->trigger("after_update",[$this,$ret,$data]);
                return $ret;
            }else{
                return false;
            }
        }else{
            return $this->getTable()->update($data);
        }
    }
    public function iterator(){
        $iterator=$this->getTable()->iterator();
        return new DBModelIterator($iterator,get_class($this));
    }
    private $_foreign_keys;
    public function getForeignKeys(){
        $keys=$this->_foreign_keys;
        if(!$keys){
            $keys=array();
            foreach($this->getFieldList() as $f){
                if($f['foreign']){
                    $keys[$f['name']]=$f['foreign'];
                }
            }
        }
        return $keys;
    }
    public function __call($name,$args){
        $name="{$name}_id";
        $foreign_keys=$this->getForeignKeys();
        if($foreign_name=$foreign_keys[$name]){
            if($this->_data[$name]){
            $foreign=new $foreign_name();
            $foreign->mId=$this->_data[$name];
            $foreign->select();
            return $foreign;
            }
        }
        $trace = debug_backtrace();
        trigger_error('Undefined property via __call(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_ERROR);
    }


    public function __set($key,$value){
        $field = $this->extractKey($key);
        if ($field !== false) {
            $key=$field['name'];
            $this->_data[$key] = $value;
            return $value;
        }
        $trace = debug_backtrace();
        trigger_error('Undefined property via __set(): '.$key.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_ERROR);
        return false;
    }

    public function __get($key){
        $field = $this->extractKey($key);
        if ($field !== false) {
            $key=$field['name'];
            $value=isset($this->_data[$key]) ? $this->_data[$key] : null;
            if(!is_null($value)){
                
            }
            return $value;
        }
        $trace = debug_backtrace();
        trigger_error('Undefined property via __get(): '.$key.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_ERROR);
        return null;
    }

	protected function extractKey($key){
        if ($key[0] == 'm') {
            $key=substr($key,1);
            $key=strtolower($key[0]).substr($key, 1);
            $key = preg_replace('/([A-Z])/', '_${1}', $key);
            $key = strtolower($key);
            foreach($this->getFieldList() as $f){
                if($f['name']==$key){
                    //return $key;
                    return $f;
                }
            }
        }

        return false;
	}
    public static function zipKey($key){
        $key=preg_replace_callback('/_([a-z])/', function($matches){
            return strtoupper($matches[1]);
        }, $key);
        return "m".ucfirst($key);
    }

	public function __isset($key){
        $field = $this->extractKey($key);
        if ($field !== false) {
            $key=$field['name'];
            return isset($this->_data[$key]);
        }
        return false;
	}

	public function __unset($key){
        $field = $this->extractKey($key);
        if ($field !== false) {
            $key=$field['name'];
            unset($this->_data[$key]);
        }
	}
    
    /*id,weixin_id,baidu_uid...*/
    public static function __callStatic($name,$args){
        if(function_exists(get_class()."::".$name)){
            return call_user_func_array([self,$name],$args);
        }
        
        if (substr($name,0,5) == 'getBy') {
            $key=lcfirst(substr($name,5));
            $key = preg_replace('/([A-Z])/', '_${1}', $key);
            $key = strtolower($key);
            $class=get_called_class();
            $m = new $class();
            return $m->addWhere($key, $args[0])->select();
        }
        if (substr($name,0,8) == 'getAllBy') {
            $key=lcfirst(substr($name,8));
            $key = preg_replace('/([A-Z])/', '_${1}', $key);
            $key = strtolower($key);
            $class=get_called_class();
            $m = new $class();
            return $m->addWhere($key, $args[0])->find();
        }

        //return call_user_func_array([self::$redis,$name],$args);
        $trace = debug_backtrace();
        trigger_error('Undefined property via __call(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_ERROR);
    }
    abstract public function getFieldList();
}
