<?php
namespace Page\Admin;
use DBModel;
class RangeFilter extends IFilter{

    public function setFilter(DBModel $model){
        parse_str($_GET['__filter'],$params);
        $start=$params[$this->getParamName()."__start"];
        $end=$params[$this->getParamName()."__end"];
        if($start){
            $model->addWhere($this->getParamName(),$start,">=");
        }
        if($end){
            $model->addWhere($this->getParamName(),$end,"<=");
        }
    }

    public function toHtml(){
        $html='';
        parse_str($_GET['__filter'],$params);
        $reqVal=$params[$this->getParamName()];
        $html.='<ul style="margin:0;" class="nav nav-pills filter range-filter">'.
            '<li class="span1">'.htmlspecialchars($this->getName()).'</li>'.
            '<li>from:<label><input class="" value="'.htmlspecialchars($params[$this->getParamName()."__start"]).'" type="text" name="'.$this->getParamName().'__start"></label></li>'."\n".
            '<li>to:<label><input class="" value="'.htmlspecialchars($params[$this->getParamName()."__end"]).'" type="text" name="'.$this->getParamName().'__end"></label></li>'."\n";
        $html.='</ul>';

        return $html;
    }

}


