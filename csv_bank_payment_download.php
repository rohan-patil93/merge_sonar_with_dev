<?php
include('includes/session.php');
include('dbconfig.php');
include('comm.php');
$head_array = array('PYMT_PROD_TYPE_CODE','PYMT_MODE','DEBIT_ACC_NO','BNF_NAME','BENE_ACC_NO','BENE_IFSC','AMOUNT','DEBIT_NARR','CREDIT_NARR','MOBILE_NUM','EMAIL_ID', 'REMARK', 'PYMT_DATE', 'REF_NO', 'ADDL_INFO1', 'ADDL_INFO2', 'ADDL_INFO3', 'ADDL_INFO4', 'ADDL_INFO5', 'LEI_NUMBER');
$data[] = $head_array;
$pay_year =$_GET['pay_year'];
$pay_month = $_GET['pay_month'];
$vurl = API."?mode=tortrivepaymetbankdetails&pay_year=".$pay_year."&pay_month=".$pay_month."&pay_area_id=".$_GET['pay_area_id']."&version=1"; 
$exdetails = callAPI($vurl);
$exdetails = json_decode($exdetails,1);
$bankdata = $exdetails['bank_details'];
$empdata = $exdetails['emp_details'];
$emp_amount = $exdetails['emp_amount_details'];
foreach ($emp_amount  as $eamo){
	$net_amount[$eamo['employee_id']] = $eamo['net'];
}
//echo "<pre>"; print_r($exdetails);exit;
$listdate = $pay_year.'-'.$pay_month.'-01';
$narra = json_decode($bankdata['narration'],1);
$str="";
foreach($narra as $key=>$narr){
	//print_r($narr); echo key($narr);
	 if(key($narr) == 'text'){ $str.= $narr['text']; }
	 if(key($narr) == 'list'){
	 if( $narr['list'] == 'M'){ $str.= date('M', strtotime($listdate)); }
	 if($narr['list'] == 'Y'){ $str.= $pay_year; }
	 }
}
$todate = date('d-m-Y');
foreach($empdata as $emp){
	//$addarray = array();
	$emp_account = $emp['bank_account_no']!="0" ? $emp['bank_account_no'] : $emp['emp_bank_account_no'];
	$emp_blink = $emp['bank_link']!="" ? $emp['bank_link'] : $emp['emp_bank_link'];
	//$addarray = array($bankdata['payment_code'],$emp['payment_mode'],$bankdata['bank_account_number'],strtoupper($emp['display_name']),$emp_account,$emp_blink,'137200',$str,$str,$bankdata['customer_mobile'],$bankdata['customer_email'],,$todate,,,,,,,);
	$addarray = array('0'=>trim($bankdata['payment_code']),'1'=>$emp['payment_mode'],'2'=>$bankdata['bank_account_number'],'3'=>strtoupper($emp['display_name']),'4'=>$emp_account,'5'=>$emp_blink,'6'=>round($net_amount[$emp['id']]),'7'=>$str,'8'=>$str,'9'=>trim($bankdata['customer_mobile']),'10'=>trim($bankdata['customer_email']),'11'=>'','12'=>$todate,'13'=>'','14'=>'','15'=>'','16'=>'','17'=>'','18'=>'','19'=>'');
	$data[] = $addarray;
}
//echo "<pre>"; print_r($data);exit;
array_to_csv_download($data);

function array_to_csv_download($array, $filename = "export.csv", $delimiter=",") {
    header('Content-Type: application/csv');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    header('Pragma: no-cache');
    header("Expires: 0");
	
    $f = fopen('php://output', 'w');
    foreach ($array as $line) {
	//fwrite($f, '<cols><col min="1" max="1" width="100" customWidth="1"/><col min="2" max="6" width="30" customWidth="1"/></cols>');
	//fwrite($this->sheetFilePointer, '<cols><col min="1" max="1" width="100" customWidth="1"/><col min="2" max="6" width="30" customWidth="1"/></cols>');
        fputcsv($f, $line, $delimiter);
    }
}  

?>