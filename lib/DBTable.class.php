<?php
class DBTable{
    private $tableName;
    private $cols=array();
    private $wheres=array();
    private $orderBys=array();
    private $groupBys=array();
    private $limits=array();
    private $computed_cols=array();
    private $auto_clear=true;
    public function __construct($tableName,$auto_clear=true){
        $this->tableName=$tableName;
        $this->auto_clear=$auto_clear;
    }
    private function _auto_clear(){
        if($this->auto_clear){
            $this->clear();
        }
    }
    public function iterator(){
        list($where_sql,$where_vals)=$this->_where();
        list($dbh,$sth)=DB::execute_sql("select ".$this->_cols()." ".$this->_computed_cols()." from `{$this->tableName}`".$where_sql.$this->_groupby().$this->_orderby().$this->_limit(),$where_vals);
        $this->_auto_clear();
        return $sth;
    }
    public function clear(){
        $this->cols=array();
        $this->computed_cols=array();
        $this->wheres=array();
        $this->orderBys=array();
        $this->groupBys=array();
        $this->limits=array();
    }
    public function find(){
        list($where_sql,$where_vals)=$this->_where();
        $results=DB::query("select ".$this->_cols()." ".$this->_computed_cols()." from `{$this->tableName}`".$where_sql.$this->_groupby().$this->_orderby().$this->_limit(),$where_vals);
        $this->_auto_clear();
        return $results;
    }
    public function setCols($cols=null){
        $this->cols=$cols;
        return $this;
    }
    private function _cols(){
        if(!$this->cols){
            return " * ";
        }else{
            return " ".implode(",",array_map(function($col){
                return "`$col`";
            },$this->cols))." ";
        }
    }
    public function addComputedCol($col,$alias=null){
        $this->computed_cols[]=array($col,$alias);
        return $this;
    }
    public function addWhere($col,$value,$sign='=',$logic='and',$escapeValue=true){
        $this->wheres[]=array($col,$value,$sign,$logic);
        return $this;
    }
    public function count(){
        list($where_sql,$where_vals)=$this->_where();
        $results=DB::queryForCount("select count(*) from `{$this->tableName}`".$this->where_sql.$this->_groupby(),
            $this->where_vals
        );
        $this->_auto_clear();
        return $results;
    }
    public function orderBy($col,$sorttype='asc'){
        $this->orderBys[]=array($col,$sorttype);
        return $this;
    }
    public function insert($values){
        //$cols=implode(",",array_map(function($value){
        //    return "`$value`";
        //},array_keys($values)));
        $this->setCols(array_keys($values));
        $placeholder=implode(",",array_map(function($value){
            return "?";
        },array_keys($values)));
        $params=array("insert into `{$this->tableName}`(".$this->_cols().") values ($placeholder);");
        $params=array_merge($params,array_values($values));
        $results=call_user_func_array('DB::insert',$params);
        $this->_auto_clear();
        return $results;
    }
    public function select(){
        if($this->limits){
            $this->limits[1]=1;
        }else{
            $this->limit();
        }
        $results=current($this->find());
        $this->_auto_clear();
        return $results;
    }
    public function limit($start=null,$len=null){
        if(is_null($len)){
            if(is_null($start)){
                $start=0;$len=1;
            }else{
                $len=$start;$start=0;
            }
        }
        $this->limits=array(intval($start),intval($len));
        return $this;
    }
    private function _limit(){
        if(!$this->limits){
            return "";
        }
        return " limit {$this->limits[0]},{$this->limits[1]} ";
    }
    public function update($values,$force=false){
        $cols=implode(",",array_map(function($value){
            return "`$value`= ?";
        },array_keys($values)));
        //$this->setCols(array_keys($values));

        list($where_sql,$where_vals)=$this->_where();
        if(!$where_sql&&!$force){
            return false;
        }
        $params=array("update `{$this->tableName}` set $cols  $where_sql;");
        $params=array_merge($params,array_values($values),$where_vals);
        $results=call_user_func_array('DB::update',$params);
        $this->_auto_clear();
        return $results;
    }
    public function delete($force=false){
        if(!$force && !$this->wheres){
            return;
        }
        list($where_sql,$where_vals)=$this->_where();
        $results=DB::delete("delete from `{$this->tableName}` ". $this->where_sql,$this->where_vals);
        $this->_auto_clear();
        return $results;
    }
    
    private function _where(){
        if (!$this->wheres){
            return "";
        }
        $sql="";
        $values=array();
        foreach($this->wheres as $i=>$where){
            if($i!=0){
                $sql.=" {$where[3]} ";
            }
            
            $sql.= " `{$where[0]}` {$where[2]}";
            if($wheres[4]){
                $values[]=$where[1];
                $sql.=" ? ";
            }else{
                $sql.=" {$where[1]} ";
            }
        }
        return array(" where $sql ",$values);
    }
    private function _orderby(){
        if (!$this->orderBys){
            return "";
        }
        $sql='';
        foreach($this->orderBys as $orderby){
            if($i!=0){
                $sql.=" , ";
            }
            $sql.=" `{$orderby[0]}` {$orderby[1]} ";
        }
        return " order by $sql ";
    }
    public function groupBy($col){
        $this->groupBys[]=$col;
        return $this;
    }
    private function _groupby(){
        if (!$this->groupBys){
            return "";
        }
        $sql='';
        foreach($this->groupBys as $groupBy){
            if($i!=0){
                $sql.=" , ";
            }
            $sql.=" `{$groupBy}` ";
        }
        return " group by $sql ";
    }
    private function _computed_cols(){
        
        if (!$this->computed_cols){
            return "";
        }
        $sql="";
        foreach($this->computed_cols as $col){
            $sql.=" , $col[0] $col[1] ";
        }
        return $sql;
    }

}
