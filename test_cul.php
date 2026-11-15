<?php
include_once 'includes/session.php';
include_once 'dbconfig.php';
include_once 'comm.php';
$type = $_POST['type'];
$url = API."?mode=singlerowtabledata&fid=id&rid=".$_POST['post_id']."&table=job_postings";
$response = callAPI($url);
$role = json_decode($response,1);
$title = $role['job_name'];
$content= base64_decode($role['job_desc']);

if($type!="CREATE"){
  $url = API."?mode=singlerowtabledata&fid=id&rid=".$_POST['publish_id']."&table=job_posting_publish_details";
    $pubres = callAPI($url);
    $pubdata = json_decode($pubres,1);
}
$return = $_SESSION['error_message']['1110'];

$curl = curl_init();
$response = curl_exec($curl);

$data['response_data'] = $response;
$res_data = json_decode($response,1);
$data['status'] = $res_data['status'];
$data['ref_id'] = $res_data['data']['ID'];
$data['type'] = $_POST['type'];
$data['post_id'] = $_POST['post_id'];
$data['source_id'] = $_POST['source_id'];
$data['publish_id'] = $_POST['publish_id'];
$data['company_id'] = $_POST['company_id'];
$url = API."?mode=toinsertupdatethejobpostingpublish&method=post";
$return = file_post_contents($url, $data, 'insert');
echo $return; exit;
