<?php

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://pilot.vendorstaq.com//api.php?mode=vendorapicallforvmslist&user_id=1&server=dev.infistaq.in&company_id=1&data_name=vendorstaq_1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);
//echo $response;

$datas = json_decode($response,1);
$post['chcek']='chcek';
$post['data']=$response;
//echo json_encode($post);
//echo "<pre>"; print_r($datas); echo "</pre>"; exit;


if(count((array)$datas)>0){

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://dev.infistaq.in/api.php?mode=tovmsobjothrsidecornjobvendors&user_id=1&server=pilot.vendorstaq.com&method=post&company_id=1&data_name=infistaqin_infisuite_2',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $post,
  ));

  $res = curl_exec($curl);
  //echo $res;
  $lodids = json_decode($res,1);
  //print_r($lodids); exit;

  if(count((array)$lodids['resids'])){
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://pilot.vendorstaq.com//api.php?mode=vmsvendorupdatetoapi&upload_ids='.implode(",",$lodids['resids']).'&ref_ids='.implode(",",$lodids['insertids']).'&user_id=1&server=dev.infistaq.in&company_id=1&data_name=vendorstaq_1',
        //CURLOPT_URL => 'https://pilot.vendorstaq.com//api.php?mode=vmsvendorupdatetoapi&upload_ids='.implode(",",$lodids['resids']).'&delete_mode=Y&user_id=25&company_id=1&data_name=vendorstaq_1',  
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
      ));

      $upresponse = curl_exec($curl);
      echo $upresponse;
  }
}

?>