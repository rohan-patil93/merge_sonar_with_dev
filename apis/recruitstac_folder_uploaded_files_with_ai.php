<?php
include('../dbconfig.php');
include('../comm.php');
$transaction_table = $_POST['transaction_table'];
$transaction_id = '';
$data1["where"] = array('company_id'=> $_POST['company_id'],'api_code'=>'CANDIDATEFOLDERSCANOCR','is_active'=>'0');
$data1["field"] = "id, api_code, production_testing, production_url, testing_url";
$data = urlencode(json_encode($data1));
$masterurl = API."?mode=gettabledetailswithconditions&table=api_masters&company_id=".$_POST['company_id']."&data_name=".$_POST['data_name']."&userid=".$_POST['user_id']."&data=".$data; 
$postData = array();
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $masterurl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
}else{
    $masterresult = json_decode($response,1);
    if($masterresult[0]['id'] !=""){
        if($masterresult[0]['production_testing'] == '0'){
            $api_url = $masterresult[0]['production_url'];
        }else{
            $api_url = $masterresult[0]['testing_url'];
        }
       // $aiurl = "https://aidev.digixlabs.cloud/api/upload";
       // echo "sdcssv"; exit;    
        $aipostData = $_POST;
        //AI Data
        $insertarray = array('company_id'=>$_POST['company_id'],'application_table'=>$transaction_table,'transaction_id'=>$transaction_id,'api_master_id'=>$masterresult[0]['id'],'api_url'=>$api_url,'api_request_parameters'=>urlencode(json_encode($aipostData)),'request_date_time'=>date("Y-m-d H:i:s"),'is_active'=>'0','current_status'=>'0','created_date'=>date("Y-m-d H:i:s"),'created_by'=>$_POST['user_id'],'response_status'=>200);
        $inserturl = API."?mode=adddata&table=api_hits&method=post&company_id=".$_POST['company_id']."&data_name=".$_POST['data_name']."&userid=".$_POST['user_id']; 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $inserturl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $insertarray);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $insertresponse = curl_exec($ch);
        $insertresponsede = json_decode($insertresponse,1);
        $last_id = $insertresponsede['LASTID'];

        if (curl_errno($ch)) {
            echo json_encode(array('result'=>curl_error($ch2),'msg'=> 'api hits Not Inserted', 'status'=>'failed', 'lastid'=>''));
           // echo 'Error: ' . curl_error($ch);
        }else{
            $aipostData = array();
            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $api_url);
            curl_setopt($ch2, CURLOPT_POST, true);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, $aipostData);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true); 
            $headers = [
                'X-API-Key: recruit-9f3a2b8e1c7d4f6a0e5b2c9d8f1a4e7b',
                'Content-Type: multipart/form-data' 
            ];
            curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);           
            if (curl_errno($ch2)) {
                echo json_encode(array('result'=>curl_error($ch2),'msg'=> 'Oops! There seems to be a server-side issue. Please connect with our IT team to investigate and resolve it.', 'status'=>'failed', 'lastid'=>''));
            }else{  
                $response11 = curl_exec($ch2);
                $retres = json_decode($response11,1);
                $data21['api_response_details'] =  urlencode(json_encode($retres));
                $data21['response_date_time'] = nowDate();
                $data21['updated_date'] = nowDate();
                $data21['updated_by'] = $_POST['userid'];
                $inserturl = API."?mode=updaterowtabledata&table=api_hits&method=post&company_id=".$_POST['company_id']."&data_name=".$_POST['data_name']."&userid=".$_POST['user_id']."&rid=".$last_id; 
                $upch = curl_init();
                curl_setopt($upch, CURLOPT_URL, $inserturl);
                curl_setopt($upch, CURLOPT_POST, true);
                curl_setopt($upch, CURLOPT_POSTFIELDS, $data21);
                curl_setopt($upch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($upch);
                curl_close($upch); 
            } 
           
        }
    }
}

