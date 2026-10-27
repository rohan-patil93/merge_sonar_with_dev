<?php
include('includes/session.php');
include('dbconfig.php');
include('comm.php');

$head_array = array('Transaction type (Within Bank (WIB)/NEFT (NFT)/RTGS (RTG)/IMPS (IFC))','Debit Account no Should be exactly 12 digit','IFSC (Always 11 character alphanumeric and 5th character always 0 (zero)) (For ICICI bank accounts keep it blank)','Beneficiary Account No (Max length for other bank 34 character alphanumeric and for ICICI Bank 12 digit number )','Beneficiary Name (Max length 32 Character) (No Special Character is allowed but Space is allowed)','Amount (Should not be more than 15 digit including decimals and paise)','Remarks for Client(should not be more than 21 characters)','Remarks for Beneficiary (should not be more than 30 characters)');

$pay_year =$_GET['pay_year'];
$pay_month = $_GET['pay_month'];
 $vurl = API."?mode=tortrivepaymetbankdetails&pay_year=".$pay_year."&pay_month=".$pay_month."&pay_area_id=".$_GET['pay_area_id']."&version=1"; 
$exdetails = callAPI($vurl);
$exdetails = json_decode($exdetails,1);
$empdatas = $exdetails['data'];
$company_details = $exdetails['company_details'];
$data[] = array('Trace Account (Where amount to be reversed in case of transaction failure)',$company_details['bank_account'] );
$data[] = array('Date of Transaction (Current Date or Future date for scheduled transaction)', date('M d, Y'));
$data[] = $head_array;
//echo "<pre>"; print_r($exdetails);exit;
/*$listdate = $pay_year.'-'.$pay_month.'-01';
$narra = json_decode($bankdata['narration'],1);
$str="";
foreach($narra as $key=>$narr){
	if(key($narr) == 'text'){ 
	 	$str.= $narr['text']; 
	}
	if(key($narr) == 'list'){
	 	if( $narr['list'] == 'M'){
	 		$str.= date('M', strtotime($listdate)); 
	 	}
	 	if($narr['list'] == 'Y'){ 
	 		$str.= $pay_year; 
	 	}
	}
}*/
$viewdate = $pay_year."-".$pay_month."-01";
$viewsal = date('M Y', strtotime($viewdate));
$new_viewsal = "Salary ".$viewsal;

foreach($empdatas as $empdata){
	$new_viewsal_code = "Salary ".$viewsal." - ".$empdata['emp_code'];
	$addarray = array('0'=>'NFT','1'=>$company_details['bank_account'],'2'=>$empdata['bank_link'],'3'=>$empdata['bank_account_no'],'4'=>$empdata['display_name'],'5'=>$empdata['salary_component_actual'],'6'=>$new_viewsal_code,'7'=>$new_viewsal);
	$data[] = $addarray;
}
$filename = $new_viewsal." Employee Bank Detais.csv";
//echo "<pre>"; print_r($data);exit;
array_to_csv_download($data,$filename);

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