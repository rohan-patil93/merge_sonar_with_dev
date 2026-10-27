<?php
include('includes/session.php');
include('dbconfig.php');
include('comm.php');
$type = $_POST['type'];
$url = API."?mode=singlerowtabledata&fid=id&rid=".$_POST['post_id']."&table=job_postings";
$response = callAPI($url);
$role = json_decode($response,1);
//print_r($role);
$title = $role['job_name'];
$content= base64_decode($role['job_desc']);

if($type!="CREATE"){
  $url = API."?mode=singlerowtabledata&fid=id&rid=".$_POST['publish_id']."&table=job_posting_publish_details";
    $pubres = callAPI($url);
    $pubdata = json_decode($pubres,1);
}
$return = $_SESSION['error_message']['1110'];
//$content= $role['job_desc'];
//echo "Test Start</br>"; //exit;

$curl = curl_init();
/*if($type=="CREATE"){
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://infisuite.com/beta/school/wp-json/infi/v1/jobs/create',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('title'=>$title,'content'=>$content,'data'=>base64_encode(array('data_basename'=>'job_posting_candidates','post_id'=>'10','company_id'=>'1'))),
    CURLOPT_HTTPHEADER => array(
      'Authorization: Basic YWRtaW46U3VwZXJAMTIz'
    ),
  ));
}
if($type!="CREATE"){
  $data_array = array('post_status'=>'publish','title'=>$title,'content'=>$content);
  if($type=="MODIFY"){
    $data_array['post_status'] = 'publish';
    $return = $_SESSION['error_message']['1111'];
  }
  if($type=="DRAFT"){
    $data_array['post_status'] = 'Draft';
    $return = $_SESSION['error_message']['1113'];
  }
  if($type=="TRASH"){
    $data_array['post_status'] = 'Trash';
    $return = $_SESSION['error_message']['1112'];
  }
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://infisuite.com/beta/school/wp-json/infi/v1/jobs/modify/?title=Vue2&content=Vue2&job_id='.$pubdata['ref_id'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $data_array,
    CURLOPT_HTTPHEADER => array(
      'Authorization: Basic YWRtaW46U3VwZXJAMTIz'
    ),
  ));    
}*/
$response = curl_exec($curl);
curl_close($curl);

//echo "Title:".$title;
//echo "content:".$content;
//echo "Test End".$response;
$data['response_data'] = $response; 
$res_data = json_decode($response,1);
$data['status'] = $res_data['status'];
//$res_data = json_decode($_POST['data'],1);
$data['ref_id'] = $res_data['data']['ID'];
$data['type'] = $_POST['type']; 
$data['post_id'] = $_POST['post_id'];
$data['source_id'] = $_POST['source_id'];
$data['publish_id'] = $_POST['publish_id'];
$data['company_id'] = $_POST['company_id'];
$url = API."?mode=toinsertupdatethejobpostingpublish&method=post";
$return = file_post_contents($url, $data, 'insert');
//$role = json_decode($response,1);
echo $return; exit;
?>