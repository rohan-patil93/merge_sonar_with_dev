<?php
//Outside Data
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://dev.infistaq.in/api.php?mode=getAllCSRData&user_id=1&server=dev.infistaq.in&company_id=1&data_name=infistaqin_infisuite_demo',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);
curl_close($curl);
//echo $response;

$datas = json_decode($response,1);
$post['chcek']='chcek';
$post['data']=$response;
//echo json_encode($post);
//echo "<pre>"; print_r($datas); echo "</pre>"; exit;


if(count((array)$datas)>0){
  //inside data update
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://dev.infistaq.in/api.php?mode=tovCSRObjOthrSideCornJob&user_id=1&server=pilot.vendorstaq.com&method=post&company_id=1&data_name=infistaqin_infisuite_2',
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

  curl_close($curl);
  //echo $res;
  $lodids = json_decode($res,1);
  print_r($lodids); exit;

}else{
  echo "No data found";
}

// outside data
$table_name = $_GET['table_name'];
$transaction_id = $_GET['transaction_id'];
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://dev.infistaq.in/api.php?mode=updateCSRTableData&user_id=1&server=dev.infistaq.in&company_id=1&data_name=infistaqin_infisuite_demo&table_name='.$table_name.'&transaction_id='.$transaction_id,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);
curl_close($curl);
//echo $response;

$datas = json_decode($response,1);
?>