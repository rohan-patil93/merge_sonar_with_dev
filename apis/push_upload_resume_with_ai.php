<?php
include_once'../dbconfig.php';
include_once'../comm.php';
$transaction_table = $_POST['transaction_table'];
$transaction_id = '';
$File_Uploads_Path = "/uploads/all/";
$fileuploadeddetaials = $_FILES['files'];
if($fileuploadeddetaials['error'] != "4"){
    $temp = explode(".", $fileuploadeddetaials['name']);
    $Filename=  "file_with_ai_".time()."_".uniqid();
    $upload_transactions_file = $Filename.'.' .end($temp);
    $fullPath = SITEROOT.$File_Uploads_Path.$upload_transactions_file;
   move_uploaded_file($fileuploadeddetaials["tmp_name"], $fullPath);
}
$filePath = $fullPath;
$fullPathfilename = $File_Uploads_Path.$upload_transactions_file;
$data1["where"] = array('company_id'=> $_POST['company_id'],'api_code'=>'CANDIDATEPUSHRESUME','is_active'=>'0');
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
        $cfile = new CURLFile($filePath, mime_content_type($filePath), basename($filePath));
        $aipostData = [
            'files' => $cfile,
            'company_id' => $_POST['company_id'],
            'data_name' => $_POST['data_name'],
            'user_id' => $_POST['user_id'],
            'upload_transactions_file' => $upload_transactions_file
        ];
        $insertarray = array('company_id'=>$_POST['company_id'],'application_table'=>$transaction_table,'transaction_id'=>$transaction_id,'api_master_id'=>$masterresult[0]['id'],'api_url'=>$api_url,'api_request_parameters'=>json_encode((array)$aipostData),'request_date_time'=>date("Y-m-d H:i:s"),'is_active'=>'0','current_status'=>'0','created_date'=>date("Y-m-d H:i:s"),'created_by'=>$_POST['user_id'],'response_status'=>200);
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
        }else{
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
                if ($retres['success']){
                    $msg = $retres['results'][0]['message'];
                    if ($retres['results'][0]['profile_created']){ 
                        $lastid = $retres['results'][0]['crm_response']['candidates']['LASTID'];
                        $decode = deocdeId("id:".$lastid);
                        echo json_encode(array('result'=>$retres, 'msg'=> $msg, 'status'=>'success', 'lastid'=>$lastid, 'encodeid'=>$decode));
                    }else{
                       echo json_encode(array('result'=>$retres, 'msg'=> $msg,'status'=>'failed', 'lastid'=>''));
                    }
                }else{
                    $msg =  '<div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Oops! There seems to be a server-side issue. Please connect with our IT team to investigate and resolve it.</p>   
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="../assets/images/work.png">
                        </div>   
                    </div>';
                    echo json_encode(array('result'=>$retres,'msg'=> $msg, 'status'=>'failed', 'lastid'=>''));
                }
                $data21['api_response_details'] =  json_encode((array)$retres);
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
            } 
           
        }
    }
}

