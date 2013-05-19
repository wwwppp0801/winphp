<?php
$row = 1;
if (($handle = fopen("questions_utf8.csv", "r")) !== FALSE) {
    $results=array();
    while (($data = fgetcsv($handle, 3000, ";")) !== FALSE) {
        if(intval($data[0])==1){
            $results[]=array();
        }
        if(!is_numeric($data[0])){
            continue;
        }
        if(isset($results[count($results)-1])){
            $results[count($results)-1][]=array(
                'title'=>$data[1],
                'choices'=>array(
                    $data[2],$data[3],$data[4],
                ),
                'answer'=>intval($data[5]),
            );
        }
        //var_dump($data);
    }
    $var_def=var_export($results,1);
    file_put_contents("questions.php","<?php\n\$questions=$var_def;");
    fclose($handle);
}
