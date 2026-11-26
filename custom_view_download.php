<?php
include_once 'includes/session.php';
$array_data = $_SESSION['download_data'];
$table = $_REQUEST['page']!="" ? $_REQUEST['page'] : 'download';
$data = "";
$i=1; foreach($array_data as $key=>$array){
    $show_array = $array;
    unset($show_array['data']);
    if($i==1){
         $data .= strtoupper(str_replace('_', ' ', implode(',', array_keys($show_array))));
    }
    $data .="\n";
    $data .= implode(',', $show_array );
    $m = 1;
    foreach($array['data'] as $listarray){
        $data .="\n";
        if($m==1){
         $data .= " ,".strtoupper(str_replace('_', ' ', implode(',', array_keys($listarray))));
         }
        $data .= " ,".implode(',', $listarray);
    $m++;
    }
    $i++;
}
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="'.$table.'.csv"');
echo $data; exit;
