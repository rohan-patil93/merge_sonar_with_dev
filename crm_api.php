<?php
ini_set('display_errors', 1);
include_once('dbconfig.php');
include_once('includes/dbconnection.php');
date_default_timezone_set('Asia/Kolkata');
include_once("model/user.class.php");
include_once("model/account_user.class.php");
//include_once("model/crm_user.class.php");
$userObj=new userClass();
//$crmuserObj=new crmuserClass();
//print_r($_GET); exit;
$method = $_GET['method'];
$mode = $_GET['mode'];
if($method == 'post'){
	//$post_array = array('insertcrmleads'=>'insertCRMLeads','insertcrmleaddetails'=>'insertCRMLeadDetails','dynamicupdatestatus'=>'dynamicUpdateStatus','leadconverttocontacts'=>'leadConvertToContacts');
	$post_array = array('leadandcontactassigning'=>'leadAndContactAssigning','importcsv' => 'importCSV','createsalesinvoice'=>'createSalesInvoice');
	if(isset($post_array[$mode])){
		include_once("model/crm_user.class.php");
		$crmuserObj=new crmuserClass();
		$fun = $post_array[$mode];
		$details = $crmuserObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}
}else{
	//$check_array = array('getcrmleads'=>'getCRMLeads','setstatecity'=>'setStateCity');
	$check_array = array('getleadandcontactassigned'=>'getLeadAndContactAssigned','testapi'=>'testAPI','getsalesinvoicedynamicdata' => 'getSalesInvoiceDynamicData','getsalesinvoicedynamicexcellist'=> 'getSalesInvoiceDynamicExcelList','getcrmaidashboards'=>'getCRMAIDashboards');
	if(isset($check_array[$mode])){
		include_once("model/crm_user.class.php");
		$crmuserObj=new crmuserClass();
		$fun = $check_array[$mode];
		$details = $crmuserObj->$fun($_GET);
		echo json_encode($details);
	}
	if($mode == 'salesInvoiceThroughDeliveryOrderCreated'){
		$details = $userObj->salesInvoiceThroughDeliveryOrderCreated($_GET);
		echo json_encode($details);
	}



}
?>