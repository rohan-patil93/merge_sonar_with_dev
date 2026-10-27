<?php 
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
$optiondetails = getOptionPageTableName($_POST['option']);
$url = API."?mode=getdynamictransactioninvoices&transaction_table=".$optiondetails['application_table_name']."&admin_type=".$_SESSION['admin_type'];
$apires = callAPI($url);
$result = json_decode($apires,1);
$transactiontables = $result['transaction_list'];
$status_progress = $result['status_progress'];
?>