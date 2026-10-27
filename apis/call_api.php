<?php
if(!isset($_POST['API_TYPE']) && $_POST['API_TYPE'] !="D"){
  include('../includes/session.php');
}else{
  session_start();
  $_SESSION['company_id'] = 1;
  $_SESSION['user_id'] = 1;
  $_SESSION['DB_NAME'] = $_POST['data_name'];
}
include_once '../dbconfig.php';
include_once '../comm.php';
date_default_timezone_set('Asia/Kolkata');
$api_code = $_REQUEST['api_code'];


  //Get details Pass API Code 
  $masterurl = API."?mode=singlerowtabledata&fid=api_code&rid=".$api_code."&table=api_masters";
  $materres = callAPI($masterurl);
  $master_data = json_decode($materres,1);
  $api_request_parameters = json_decode($master_data['api_request_parameters'], 1);
  $request =$request1 = array();
  foreach($api_request_parameters as $api_request){ //get paramiter In Database To replace with Post Values 
      if(isset($_FILES['card_image']) AND $api_request == "card_image"){
        $fileTmpPath = $_FILES['card_image']['tmp_name'];
        if(file_exists($fileTmpPath)) {
            $fileContent = file_get_contents($fileTmpPath);
            $base64Encoded = base64_encode($fileContent);
            $request1[$api_request] = $base64Encoded;
        }
      }elseif($api_request == "card_image" AND $api_code == "FACEMATCH"){
          $userdataapi = API."?mode=singlerowtabledata&fid=id&rid=".$_POST['user_id']."&table=application_users";
          $userdatacallapi = callAPI($userdataapi);
          $user_detail = json_decode($userdatacallapi,1);
          if($user_detail['user_photo'] !=""){
            $fileTmpPath = SITEURL."uploads/all/".$user_detail['user_photo'];
            $fileContent = file_get_contents($fileTmpPath);
            $base64Encoded = base64_encode($fileContent);
            $request1[$api_request] = $base64Encoded;
          }else{
            $request1[$api_request] ="";
          }

      }elseif(isset($_FILES['user_image']) AND $api_request == "user_image"){
        $fileTmpPath = $_FILES['user_image']['tmp_name'];
        if(file_exists($fileTmpPath)) {
            $fileContent = file_get_contents($fileTmpPath);
            $base64Encoded = base64_encode($fileContent);
            $request1[$api_request] = $base64Encoded;
        }
      }elseif($api_request == "face_match" && $api_code == "FACELIVENESS"){
        
        $userdataapi = API."?mode=singlerowtabledata&fid=id&rid=".$_POST['user_id']."&table=application_users";
        $userdatacallapi = callAPI($userdataapi);
        $user_detail = json_decode($userdatacallapi,1);
        if($user_detail['user_photo'] !=""){
          $fileTmpPath = SITEURL."uploads/all/".$user_detail['user_photo'];
          $fileContent = file_get_contents($fileTmpPath);
          $base64Encoded = base64_encode($fileContent);
          $request[$api_request] = $base64Encoded;
        }else{
          $request[$api_request] ="";
        }
      }elseif(isset($_FILES['face_match']) && $api_request == "face_match"){
        $fileTmpPath = $_FILES['face_match']['tmp_name'];
        if(file_exists($fileTmpPath)) {
            $fileContent = file_get_contents($fileTmpPath);
            $base64Encoded = base64_encode($fileContent);
            $request[$api_request] = $base64Encoded;
        }
      }else{
        if($api_code == "FACELIVENESS"){
            $request[$api_request] = $_POST[$api_request]; 
        }else{
            $request1[$api_request] = $_POST[$api_request]; 
        }
      }
  }
  $request['mode'] = "sync";
  $request['task_id'] = "8bbb54f3-d299-4535-b00e-e74d2d5a3997"; 
  $request['data'] = $request1;

  $send_req = json_encode($request);
  if($master_data['production_testing'] == '0'){
    $api_url = $master_data['production_url'];
  }else{
    $api_url = $master_data['testing_url'];
  }
  $in_array['company_id'] = $_SESSION['company_id'];
  $in_array['application_table'] = $_POST['transaction_table'];
  $in_array['transaction_id'] = $_POST['transaction_id'];
  $in_array['api_master_id'] = $master_data['id'];
  $in_array['api_url'] = $api_url;
  $in_array['api_request_parameters'] = $send_req;
  $in_array['request_date_time'] = nowDate();
  $in_array['is_active'] = '0';
  $in_array['current_status'] ='0';
  $in_array['created_date'] = nowDate();
  $in_array['created_by'] = $_SESSION['userid'];
  $url = API."?mode=adddata&table=api_hits&method=post"; 
  $dataresponse = file_post_contents($url, $in_array, 'insert');
  $res = json_decode($dataresponse,1);
  $last_id = $res['LASTID']; 
  if($api_code == "FACELIVENESS"){
      $stringvalue = $_POST['company_id']."-".$last_id."-".$_POST['data_name'];
      $encryptedDbName = encodeString($stringvalue);
      $request['user_reference_id'] = $encryptedDbName;
      $request['response_url'] = "https://dev.infistaq.in/apis/liveness.php?refkey=".$encryptedDbName;
      $request['redirect_url'] = "https://dev.infistaq.in/apis/liveness.php?refkey=".$encryptedDbName;
      $send_req = json_encode($request);
  }
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url,
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
      'app-id: 68a83c79126f9d00287c9bb8',
      'api-key: SQRPZD3-HZZM3CX-P62GH67-313MMFP',
    ),
   
  ));
  $response = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $returnresult = json_decode($response,1);
  $returnresult['response_code'] = $httpCode;
  $returnresult['api_hit_id'] = $last_id;
  if($api_code == "FACELIVENESS" AND $httpCode == "200"){
    $returnresult['result']['request_id'] = $returnresult['result']['request_id'];
    $returnresult['result']['webhook_security_key'] = $returnresult['result']['webhook_security_key']; //webhookid
    $returnresult['result']['expires_at'] = $returnresult['result']['expires_at'];
    $returnresult['result']['sdk_url'] = $returnresult['result']['sdk_url']; //faceliveness url
  }
  if($httpCode == "503" || $httpCode == "504"){
    $returnresult['response_message'] = "The server is currently unavailable. Please try again after sometime. If the issue persists, contact your administrator for assistance";
  }
 
  $result = json_encode($returnresult);
  echo $result;
  $data['response_status'] = $httpCode;
  $data['api_response_details'] = $result;
  $data['response_date_time'] = nowDate();
  $data['updated_date'] = nowDate();
  $data['updated_by'] = $_SESSION['userid'];
  $url = API."?mode=updaterowtabledata&rid=".$last_id."&table=api_hits&method=post";
  $response = file_post_contents($url, $data, 'update');

?>

