<?php 
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://deskallocator.microchip.com/api.php?mode=sendcronjobcontractexpirynotifications&data_name=infisuite_seat&user_id=1&company_id=1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));
$response = curl_exec($curl);
$datas = json_decode($response,1);
return $response
?>