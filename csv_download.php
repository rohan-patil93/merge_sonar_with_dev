<?php

include('includes/session.php');
//print_r($_SESSION); exit;	
include('dbconfig.php');
include('comm.php');
$table = $_GET['table']; 
$array = array();
if($_GET['DISPLAY_ATTRIBUTE']!=""){
	//echo "Ravi";exit;
	$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$table;
	$page_response = callAPI($pageurl);
	$list = json_decode($page_response,1);
	$lists = json_decode($list['list_attributes'],1); 
	foreach ($lists as  $key=>$value) {
		if($value['insert_display_list']== 1 ){
			$array[]  = $key;
		}
	}

}else{
	//echo "Shankar"; exit;
	$no_array = array('id','company_id','current_status','status_date','from_date','to_date','created_date','created_by','updated_by','updated_date');

	if($table != "salary_template"){	
		if($table=='earnings' || $table=='deductions'){
			$array[] = 'Employee Number';
		}
		$vdata1["where"] = array('is_active'=> '0');
		$vdata = urlencode(json_encode($vdata1));
		$vurl = API."?mode=listofTableFieldsonly&table=".$table; 
    //echo $vurl; exit;
		$configesponse = callAPI($vurl);
		$array = json_decode($configesponse,1);
	//unset($array[0]);
		$array = array_diff($array,$no_array);
	}else{
		$array[] = 'employee_code';
		$vdata1["where"] = array('is_active'=> '0');
		$vdata = urlencode(json_encode($vdata1));
		$vurl = API."?mode=gettabledetailswithconditions&table=earnings&data=".$vdata;
		$earningsdata = callAPI($vurl);
		$earningsdata = json_decode($earningsdata,1);
		foreach ($earningsdata as  $earnings) {
			//if(strtolower($earnings['name']) != 'ctc'){
				$array[]  = $earnings['name'];
			//}
		}
		$array[] ="monthly_ctc";
		$array[] = "annual_ctc";
	}
}
if($table =='employee_other_earnings'){
	$array = array();
	$array[] = 'Employee Code';
	$array[] = 'Component Name';
	$array[] = 'Claim Amount';
}
	//print_r($array); exit;
	//$array = lcfirst(implode('', array_map('ucfirst', explode('_', $array ))));
$data = strtoupper(implode(',', $array));
	//echo $data; exit;
	//$data = "SLNo,Name,Age,Department,DOB,".$table;
	//
	//print_r(expression)
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="'.$table.'.csv"');
echo $data; exit;
?>