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
    public function __construct($data=array()){
        $this->_data=$data;
    }
    public function clear(){
        $this->_data=array();
        if($this->_table){
            $this->_table->clear();
        }
        return $this;
    }
    public function setData($data){
        $this->_data=$data;
    }
    public function getData(){
        return $this->_data;
    }
    protected function getTableName(){
        return strtolower(get_class($this));
    }
    protected function getTable(){
        if(!$this->_table){
            $this->_table=new Table($this->getTableName());
        }
        return $this->_table;
    }
    public function save(){
        $table=$this->getTable();
        if($this->mId){
            return $table->addWhere("id",$this->mId)->update($this->_data);
        }else{
            $id=$table->insert($this->_data);
            $this->mId=$id;
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
    public function addWhere(){
        $table=$this->getTable();
        call_user_func_array(array($table,"addWhere"),func_get_args());
        return $this;
    }
    public function find(){
        $datas= $this->getTable()->find();
        $class_name=get_class($this);
        return array_map(function($data)use($class_name){
            return new $class_name($data);
        },$datas);
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
            foreach($this->getFieldList()as $f){
                if($f['foreign']){
                    $keys[$f['name']]=$f['foreign'];
                }
            }
        }
        return $keys;
    }
    public function __call($name,$args){
        $name="{$name}_id";
        if($foreign_name=$this->getForeignKeys()[$name]){
            if($this->_data[$name]){
            $foreign=new $foreign_name();
            $foreign->mId=$this->_data[$name];
            $foreign->select();
            return $foreign;
            }
        }
        $trace = debug_backtrace();
        trigger_error('Undefined property via __call(): '.$key.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_ERROR);
    }
    
    
    public function __set($key,$value){
        $key = $this->extractKey($key);
        if ($key !== false) {
            $this->_data[$key] = $value;
            return $value;
        }
        $trace = debug_backtrace();
        trigger_error('Undefined property via __set(): '.$key.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_ERROR);
        return false;
    }

    public function __get($key){
        $key = $this->extractKey($key);
        if ($key !== false) {
            return isset($this->_data[$key]) ? $this->_data[$key] : null;
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
                    return $key;
                }
            }
        }
		
        return false;
	}
	
	public function __isset($key){
        $key = $this->extractKey($key);
        if ($key !== false) {
            return isset($this->_data[$key]);
        }
        return false;
	}
	
	public function __unset($key){
        $key = $this->extractKey($key);
        if ($key !== false) {
            unset($this->_data[$key]);
        }
	}
    abstract public function getFieldList();
}
