<?php

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://dev.infistaq.in/api.php?mode=getUsersData&user_id=1&server=dev.infistaq.in&company_id=1&data_name=infistaqin_infisuite_2',
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
    CURLOPT_URL => 'https://dev.infistaq.in/api.php?mode=insertUsersData&user_id=1&method=post&company_id=1&data_name=infistaqin_infisuite_demo',
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
  print_r($lodids);

}else{
  echo "No data found";
}

?>