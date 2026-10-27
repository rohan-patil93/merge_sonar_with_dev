<?php 
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
//print_r($_POST);
$admin_type = $_SESSION['admin_type'];
if($_POST['page_type'] == "HR_ADMIN_DASHBOARD"){
	$url = API."?mode=employeedashboard&emp_id=".$_SESSION['emp_id']."&admin_type=".$admin_type."&user_email=".$_SESSION['user_email']; 
	$response = callAPI($url);
	$data = json_decode($response,1);

}
$request= array();

$request['model'] = "gpt-4o-2024-08-06";
$request['store'] = true;
$messageary = array(array("role"=>"system","content"=>"You are an AI assistant."),array("role"=>"user","content"=>"Can you please provide a summary of the data below? Please include only 5 major bullet points, and start each point on a new line with a new bullet, \n\n".$response));
$request['messages'] = $messageary;
$request['temperature'] = 0.2;



//echo "<pre>";  print_r($request);
$send_req = json_encode($request);
//echo "</br></br></br></br>output json</br></br></br></br>";
//echo $send_req;


/**/
$curlurl = "https://api.openai.com/v1/chat/completions";
$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => $curlurl,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_POSTFIELDS =>$send_req,
	CURLOPT_HTTPHEADER => array(
  		'Content-Type: application/json',
  		'Authorization: Bearer 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',
  	),
));
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$returnresult = json_decode($response,1);

/**/
?>

<div class="modal-header  ">
	<h5 class="modal-title">Dashboard Summary</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body w-b-w-s">
<?php 
	$explodedata = explode("-",$returnresult['choices'][0]['message']['content']);
	$i = 0;
	foreach($explodedata as $bulpoint){
		if($i != 0){
			echo "<span class='d-block  m-2 f-14 f-w-600'>".$i.")".$bulpoint."</span>";
		}
		$i++;
	}
//echo $returnresult['choices'][0]['message']['content']; ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" >Close</button>
</div>


