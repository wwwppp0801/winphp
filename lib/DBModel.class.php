<?php
class DBModelIterator implements Iterator{
    //真tmd费劲，其实就为了改写下current
    public function __construct(Traversable $traversable,$modelClass){
        while(!$traversable instanceof Iterator){
            $traversable=$traversable->getIterator();
        }
        $this->modelClass=$modelClass;
        $this->iterator=$traversable;
    }
    function rewind() {
        return $this->iterator->rewind();
    }

    function current() {
        $data=$this->iterator->current();
        if($this->obj){
            $this->obj->setData=$data;
        }else{
            $this->obj=new $this->modelClass($data);
        }
        return $this->obj;
    }

    function key() {
        return $this->iterator->key();
    }

    function next() {
        return $this->iterator->next();
    }

    function valid() {
        return $this->iterator->valid();
    }
    
}
abstract class DBModel{
    protected $_table;
    protected $_data;
    public function __construct($data=array()){
        $this->_data=$data;
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
        if($this->_data['id']){
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
        $data=$table->addWhere('id',$this->mId)->select();
        if($data){
            $this->_data=$data;
            return $this;
        }
        return false;
    }
    public function addWhere(){
        $table=$this->getTable();
        return call_user_func_array($table,func_get_args());
    }
    public function find(){
        $datas= $this->getTable()->find();
        return array_map(function($data){
            return new self($data);
        },$datas);
    }
    public function iterator(){
        $iterator=$this->getTable()->iterator();
        return new DBModelIterator($iterator,get_class($this));
    }
    public function __call($name,$args){
        $name="{$name}_id";
        if(isset(self::$foreign_keys[$name])){
            if($this->_data[$name]){
                
            $foreign=new self::$foreign_keys[$name]();
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
            if (in_array($key, $this->getFieldList())) {
                return $key;
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
