<?php
class Page_Admin_ChoiceFilter extends Page_Admin_IFilter{

    public function setFilter(DBModel $model){
        parse_str($_GET['__filter'],$params);
        $reqVal=$params[$this->getParamName()];
        if(count($reqVal)==1&&($reqVal[0]==='0'||$reqVal[0])){
            $model->addWhere($this->getParamName(),$reqVal);
        } elseif(count($reqVal)>1) {
            $model->addWhere($this->getParamName(),$reqVal,'in');
        }
    }

    public function toHtml(){
        $html='';
        parse_str($_GET['__filter'],$params);
        $reqVal=$params[$this->getParamName()];
        $html.='<ul style="margin:0;" class="nav nav-pills filter">'.
            '<li class="span1">'.htmlspecialchars($this->getName()).'</li>'.
            '<li '.($reqVal?"":'class="active"').'><label class="radio-inline"><input '.($reqVal?"":'checked="checked"').' type="radio" name="'.$this->getParamName().'" value="">全部</label></li>'."\n";
        
        foreach($this->choices as $choice){
            $html.="<li><label><input type='radio' name='".$this->getParamName()."' ".
                ($choice[0]==$reqVal?"checked=checked":"").
                " value={$choice[0]}>{$choice[1]}</label></li>\n";
        }
        $html.='</ul>';
        return $html;
    }

}

