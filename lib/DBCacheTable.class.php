<?php
/*
 * 目的是用memcache来优化指定了id的查询，对外透明
 *
 * */
class DBCacheTable extends DBTable{
    private $kvStorage,$cacheKey;
    public function __construct($tableName,$auto_clear=true){
        parent::__construct($tableName,$auto_clear);
        $this->cacheKey="id";
    }
    public function setKvStorage($kvStorage){
        $this->kvStorage=$kvStorage;
    }
    private $old_auto_clear=null;
    private function closeAndSaveAutoClear(){
        if($this->old_auto_clear===null){
            $this->old_auto_clear=$this->getAutoClear();
        }
    }
    private function restoreAutoClear(){
        $this->setAutoClear($this->old_auto_clear);
        $this->old_auto_clear=null;
    }


    public function find(){
        if(!$this->kvStorage){
            return parent::find();
        }
        $keys=$this->findWhereKeys($this->wheres);
        if(!$keys){
            return parent::find();
        }
        $values=array_values($this->kvStorage->mget($keys));
        
        $have_null=array_reduce($values,function($res,$value){
            if($res||$value===null){
                return true;
            }
            return false;
        },false);

        if($have_null){
            $values=parent::find();
            $map=Utils::array2map($values,'id');
            $this->kvStorage->mset($map);
            return $values;
        }else{
            return $this->filterOtherConditions($values);
        }
    }

    public function update($values,$force=false){
        if(!$this->kvStorage){
            return parent::update($values,$force);
        }
        $keys=$this->findWhereKeys($this->wheres);
        if(!$keys){
            $this->closeAndSaveAutoClear();
            $results=parent::find();
            $keys=Utils::array2Simple($results,$this->cacheKey);
            //删除所有可能有修改的缓存
            $this->kvStorage->del($keys);
            $this->restoreAutoClear();
            return parent::update($values,$force);
        }

        $this->kvStorage->del($keys);
        return parent::update($values,$force);
    }
    public function delete($force=false){
        if(!$this->kvStorage){
            return parent::delete($force);
        }
        $keys=$this->findWhereKeys($this->wheres);
        if(!$keys){
            $this->closeAndSaveAutoClear();
            $results=parent::find();
            $keys=Utils::array2Simple($results,$this->cacheKey);
            //删除所有可能有修改的缓存
            $this->kvStorage->del($keys);
            $this->restoreAutoClear();
            return parent::delete($force);
        }

        $this->kvStorage->del($keys);
        return parent::delete($force);
    }
    private function findWhereKeys(){
        //找出where条件里定义的cacheKey条件
        //只处理简单的条件，复杂条件一律返回null
        //返回null就表示这次查询不会查缓存了
        if($this->cols){
            return null;
        }
        if($this->computed_cols){
            return null;
        }
        if($this->groupBys){
            return null;
        }

        $is_complicated=array_reduce($this->wheres,function($res,$where){
            if($res){
                return $res;
            }
            if(!is_array($where)){
                return true;
            }
            if(!in_array($where[2],['in','=','>','>=','<','<=','like'])){
                return true;
            }
            if($where[3]!='and'){
                return true;
            }
            if($where[4]===DBTable::NO_ESCAPE){
                return true;
            }
            return false;
        },false);

        if($is_complicated){
            return null;
        }

        $keys=array_reduce($this->wheres,function($res,$where){
            if($where[0]===$this->cacheKey){
                if($where[2]=='='){
                    $res[]=$where[1];
                }
                if($where[2]=='in'&&is_array($where[1])){
                    $res=array_merge($res,$where[1]);
                }
            }
            return $res;
        },[]);
        return array_unique($keys);
    }
    private function filterOtherConditions($values){
        //查询缓存的情况下，除cacheKey以外的其它sql where条件都没有执行，只好在php里模拟执行
        //条件较复杂情况，findWhereKeys首先不会返回，只需要考虑常见的条件
        //TODO process where
        $values=array_filter($values,function($value){
            foreach($this->wheres as $where){
                if(!$this->matchCondition($where,$value)){
                    return false;
                }
            }
            return true;
        });
        
        //process orderBy
        usort($values,function($a,$b){
            foreach($this->orderBys as $orderBy){
                $key=$orderBy[0];
                if($a[$key]==$b[$key]){
                    continue;
                }
                if($a[$key]>$b[$key]){
                    $res=1;break;
                }else{
                    $res=-1;break;
                }
            }
            if($orderBy[1]=='desc'){
                $res=-$res;
            }
            return $res;
        });
        //process limit
        if($this->limits){
            $values=array_slice($values,$this->limits[0],$this->limits[1]);
        }
        return $values;
        
    }
    public function matchCondition($where,$value){
        $v=$value[$where[0]];
        $sign=$where[2];
        $m=$where[1];
        $match=true;
        switch($sign){
            case '=':
                $match=($v==$m);
                break;
            case 'like':
                /*TODO 这里处理的不完美，只考虑应付大部分情况*/
                $m=preg_replace("/([.*?])/","\\\\$1",$m);
                $m=preg_replace("/(?<!\\\\)%/",".*",$m);
                $m=preg_replace("/(?<!\\\\)_/",".",$m);
                $match=preg_match("/^$m$/",$v);
                break;
            case 'in':
                return (array_search($v,$m)!==false);
            case "<":
                $match=($v<$m);
            case "<=":
                $match=($v<=$m);
            case ">":
                $match=($v>$m);
            case ">=":
                $match=($v>=$m);
            default:
                Logger::error("match condition error:".json_encode($wheres));
        }
        return $match;
    }

}

