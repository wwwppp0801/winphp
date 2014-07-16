<?php
class Page_Admin_HiddenFilter extends Page_Admin_IFilter{

    public function setFilter(DBModel $model){
        parse_str($_GET['__filter'],$params);
        $paramNames=$this->getParamName();
        if(!is_array($paramNames)){
            $paramNames=[$paramNames];
        }

        foreach($paramNames as $paramName){
            if(isset($params[$paramName])){
                $model->addWhere($paramName,$params[$paramName]);
            }
        }
    }

    public function toHtml(){
        $html='';
        parse_str($_GET['__filter'],$params);
        /*
        $html.='<ul style="margin:0;" class="nav nav-pills filter">'.
            '<li class="span1">'.htmlspecialchars($this->getName()).'</li>'.
            '<li '.($reqVal?"":'class="active"').'><label class="radio-inline"><input '.($reqVal?"":'checked="checked"').' type="radio" name="'.$this->getParamName().'" value="">全部</label></li>'."\n";
        
        foreach($this->choices as $choice){
            $html.="<li><label><input type='radio' name='".$this->getParamName()."' ".
                ($choice[0]==$reqVal?"checked=checked":"").
                " value={$choice[0]}>{$choice[1]}</label></li>\n";
        }
        $html.='</ul>';
         */
        $paramNames=$this->getParamName();
        if(!is_array($paramNames)){
            $paramNames=[$paramNames];
        }
        foreach($paramNames as $paramName){
            if(isset($params[$paramName])){
                $html.="<input type='hidden' name='".$paramName."' ".
                    " value={$params[$paramName]}>";
            }
        }
        return $html;
    }

}

