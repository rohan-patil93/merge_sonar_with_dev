<?php
$logFile = 'webhook_logs.json';
$rawPostData = file_get_contents("php://input");
header('Content-Type: text/plain'); // Ensure readable output
$data = json_decode($rawPostData, true);
file_put_contents($logFile, json_encode($data, JSON_PRETTY_PRINT), FILE_APPEND);
file_put_contents("webhook_all_logs.json", json_encode($data, JSON_PRETTY_PRINT), FILE_APPEND);
header("HTTP/1.1 200 OK");
$urlrefkey = $_GET['refkey'];
$explodeurl = explode("?action=",$urlrefkey);
if($explodeurl[1] == 'liveness-success'){	
	session_start();
	include_once('../dbconfig.php');
	include_once('../comm.php');
	if(file_exists($logFile)){
		$logContent = file_get_contents($logFile);
		$logContent = str_replace("null","",$logContent);
		$user_reference_id = $explodeurl[0];
		$decoderefkey = decodeString($user_reference_id);
		$explodekey = explode("-",$decoderefkey);
		$company_id = $explodekey[0];
		$api_hit_id = $explodekey[1];
		$dataname = $explodekey[2];
		$postdata['webhook_response'] =$logContent;
		$postdata['updated_date'] = nowDate();
		$url = API."?mode=updaterowtabledata&rid=".$api_hit_id."&table=api_hits&method=post&data_name=".$dataname;
		$response = file_post_contents($url, $postdata, 'update');
		file_put_contents($logFile, ''); 
	}
}else{
	session_start();
	include_once('../dbconfig.php');
	include_once('../comm.php');
	$user_reference_id = $explodeurl[0];
	$decoderefkey = decodeString($user_reference_id);
	$explodekey = explode("-",$decoderefkey);
	$company_id = $explodekey[0];
	$api_hit_id = $explodekey[1];
	$dataname = $explodekey[2];
	$logContent['response_code'] = 102;
  	$logContent['api_hit_id'] = $api_hit_id;
  	$logContent['response_message'] = $explodeurl[1];
  	$postdata['webhook_response'] =$data = json_encode($logContent, true);
	$postdata['updated_date'] = nowDate();
	$url = API."?mode=updaterowtabledata&rid=".$api_hit_id."&table=api_hits&method=post&data_name=".$dataname;
	$response = file_post_contents($url, $postdata, 'update');
}
?>