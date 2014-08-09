<?php
class Page_Admin_TimeRangeFilter extends Page_Admin_IFilter{

    public function setFilter(DBModel $model){
        parse_str($_GET['__filter'],$params);
        $start_time=$params[$this->getParamName()."__start"];
        $end_time=$params[$this->getParamName()."__end"];
        if($start_time){
            $model->addWhere($this->getParamName(),strtotime($start_time),">=");
        }
        if($end_time){
            $model->addWhere($this->getParamName(),strtotime($end_time),"<=");
        }
    }

    public function toHtml(){
        $html='';
        parse_str($_GET['__filter'],$params);
        $reqVal=$params[$this->getParamName()];
        $html.='<ul style="margin:0;" class="nav nav-pills filter">'.
            '<li class="span1">'.htmlspecialchars($this->getName()).'</li>'.
            '<li>start:<label class="radio-inline"><input class="datetimepicker" value="'.htmlspecialchars($params[$this->getParamName()."__start"]).'" type="text" name="'.$this->getParamName().'__start"></label></li>'."\n".
            '<li>end:<label class="radio-inline"><input class="datetimepicker" value="'.htmlspecialchars($params[$this->getParamName()."__end"]).'" type="text" name="'.$this->getParamName().'__end"></label></li>'."\n".
            '<li>示例：'.date("Y-m-d H:i:s").'</li>'."\n";
        $html.='</ul>';

        return $html;
    }

}


