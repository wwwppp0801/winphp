<?php
/*
 * 可以配合winphp/template/pagination.tpl
 * 所有index、page都是从0开始
 */
class Pagination {
    public function __construct($allCount=0,$pageSize=10,$page=null){
        if(is_null($page)){
            $this->page=intval(isset($_GET['page'])?$_GET['page']:0);
        }else{
            $this->page=intval($page);
        }
        $this->pageSize=$pageSize;
        $this->allCount=$allCount;
    }
    public function setAllCount($allCount){
        $this->allCount=$allCount;
    }
    public function setPageSize($pageSize){
        $this->pageSize=$pageSize;
    }

    public function page(){
        if($this->page<=$this->maxPage()){
            return $this->page;
        }else{
            return 0;
        }
    }
    public function pageSize(){
        return $this->pageSize;
    }
    public function startPage(){
        $startPage=max(0,$this->page()-3);
        return $startPage;
    }
    public function endPage(){
        $endPage=min($this->maxPage(),$this->page()+4);
        return $endPage;
    }
    public function startIndex(){
        return $this->page()*$this->pageSize();
    }
    public function endIndex(){
        if($this->page<$this->maxPage()){
            return ($this->page()+1)*$this->pageSize()-1;
        }else{
            return $this->allCount - $this->startIndex();
        }
    }
    public function haveNext(){
        return $this->page()<$this->maxPage();
    }
    public function havePrev(){
        return $this->page()>0;
    }
    public function maxPage(){
        return floor($this->allCount/$this->pageSize);
    }

}
