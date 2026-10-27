<?php
$result =array();
$liveness_match_score_limit = 60;
$request_id=$_GET['request_id'];
if($request_id !=""){
	$logFile = 'webhook_logs.json';
	if (file_exists($logFile)) {
		$logContent = file_get_contents($logFile);
		//echo "<pre>"; //print_r($logContent);
	   	$logContent1 = str_replace("null","",$logContent);
	   	$logContent1 = str_replace("}{","},{",$logContent);
	   	$data = json_decode("[".$logContent1."]", true);
	   	foreach($data as $dataresult){
	   		if($request_id == $dataresult['request_id']){
	   			$matchresult = $dataresult;
	   			break;
	   		}
	   	}
	   	$result['response_code'] = 200;
		$result['api_hit_id'] = '';
		$liveness = ($matchresult['result']['liveness']['prediction'] == "real")?"true":"false";
	   	$face_match = ($matchresult['result']['face_match']['match_score'] > $liveness_match_score_limit)?"true":"false";
	   	if($liveness == "false"){
	   		$result['response_message'] = 'Fake liveness';
	   	}elseif($face_match == "false"){
	   		$result['response_message'] = 'Not Match User';
	   	}else{
	   		$result['response_message'] = 'Liveness And Face Match Success';
	   	}
		$result['result']['liveness'] = $liveness;
		$result['result']['face_match'] = $face_match;
	   	$result['result']['face_match_score'] = $matchresult['result']['face_match']['match_score'];
	   	$result['location']['latitude'] = $matchresult['result']['location']['latitude'];
	   	$result['location']['longitude'] = $matchresult['result']['location']['longitude'];
	}else{
		$result['response_code'] = 404;
		$result['api_hit_id'] = '';
		$result['response_message'] = 'Result Not Available';
	}
}else{
	$result['response_code'] = 404;
	$result['api_hit_id'] = '';
	$result['response_message'] = 'Request Id Is Empty';
}
echo json_encode($result);
?>