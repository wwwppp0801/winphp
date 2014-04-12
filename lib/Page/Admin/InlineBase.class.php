<?php
class Page_Admin_InlineBase extends Page_Admin_Base{
    public $foreignKeyName;
    public function setForeignKey($id){
        //var_dump(DBModel::zipKey($this->foreignKeyName));
        $this->model->__set(DBModel::zipKey($this->foreignKeyName),$id);
        $this->foreignKey=$id;
        $this->_initForm();
    }
    public function setForeignKeyName($foreignKeyName){
        $this->foreignKeyName=$foreignKeyName;
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
