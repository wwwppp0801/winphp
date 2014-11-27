<?php
trait ExportToCsvAction{
    public static function printCsvRow($row){
		$csv_terminated = "\n";
		$csv_separator = ",";
		$csv_enclosed = '"';
		$csv_escaped = "\\";

        $insert = '';
        $fields_cnt = count($row);
        $tmp_str = '';
        foreach ($row as $v) {
            $tmp_str .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $v) . $csv_enclosed . $csv_separator;
        }

        $tmp_str = substr($tmp_str, 0, -1);
        $insert .= $tmp_str;

        echo Utils::toGBK($insert);
        echo $csv_terminated;
    }
    public function exportToCsvAction(){
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		//header("Content-Length: " . strlen($out));
		header("Content-type: text/x-csv");
		header("Content-Disposition:filename=order.csv");

		$insert = '';
        foreach($this->list_filter as $filter){
            $filter->setFilter($this->model);
        }


        $row=[];
        foreach ($this->list_display as $list_item){
            if(is_string($list_item)){
                $row[]=$list_item;
            }elseif(isset($list_item['label'])){
                $row[]=$list_item['label'];
            }else{
                $row=strval($list_item);
            }
        }
        self::printCsvRow($row);

        $this->model->setAutoClear(false);
        $count=$this->model->count();
        for($i=0;$i<=$count/100;$i++){
            $this->model->limit($i*100,100);
            $csv_data=$this->model->find();
            foreach ($csv_data as $modelData) {
                $row=[];
                foreach ($this->list_display as $list_item){
                    if(is_array($list_item)&&isset($list_item['label'])){
                        $list_item=$list_item['field'];
                    }
                    if(is_string($list_item)){
                        $val=$modelData->getData($list_item);
                    } elseif(is_callable($list_item)){
                        $val=call_user_func($list_item,$modelData,$this,$csv_data);
                    }else{
                        $val=strval($list_item);
                    }
                    $row[]=trim(strip_tags($val));
                }
                self::printCsvRow($row);
            }
        }

		//echo $out;
		die();
    }
}
