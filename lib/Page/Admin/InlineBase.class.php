<?php
trait Page_Admin_InlineBase{
    public $foreignKeyName;
    public function setForeignKey($id){
        //var_dump(DBModel::zipKey($this->foreignKeyName));
        //$this->model->__set(DBModel::zipKey($this->foreignKeyName),$id);
        $this->model->addWhere($this->foreignKeyName,$id);
        $this->foreignKey=$id;
        $this->_initForm();
    }
    public function setForeignKeyName($foreignKeyName){
        $this->foreignKeyName=$foreignKeyName;
    }
    private $relationship="multi";//single or multi
    public function setRelationship($relationship){
        $this->relationship=$relationship;
    }
    public function getRelationship(){
        return $this->relationship;
    }
    public $couldCreate=true;
    public function setCouldCreate($couldCreate){
        $this->couldCreate=$couldCreate;
    }
    public function couldCreate(){
        return $this->couldCreate;
    }
    private function _initForm(){
        $foreignKeyName=$this->foreignKeyName;
        $foreignKey=$this->foreignKey;
        $formConfig=array_map(function($field)use($foreignKeyName,$foreignKey){
            if($field->name()!=$foreignKeyName){
                return $field;
            }else{
                return new Form_NoHtmlField(["name"=>$this->foreignKeyName,"default"=>$id]);
            }
        },$this->form->getConfig());
        //var_dump($foreignKeyName,$formConfig);
        $this->form=new Form($formConfig);
    }
    public function _update(){
        $this->_initForm();
        return parent::_update();
    }
}
