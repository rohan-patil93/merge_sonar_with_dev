<?php 
include('includes/session.php');
//$array_data = json_decode(urldecode($_REQUEST['data']),1);
$array_data = $_SESSION['download_data'];
//print_r($_SESSION['head_data']);
$table = $_REQUEST['page'];
//$data = strtoupper(implode(',', $array));
$data = "";
$data .= strtoupper( $_SESSION['config']['name']).chr(26)."\n";
$data .= strtoupper(implode(',', $_SESSION['head_data']));
$i=1;
foreach($array_data as $array){
	$data .="\n";
	$data .= $i.",".strtoupper(implode(',', $array));
$i++;}

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="'.$table.'.csv"');
echo $data; exit;
?>