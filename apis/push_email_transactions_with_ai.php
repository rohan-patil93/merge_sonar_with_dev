<?php
include_once '../dbconfig.php';
$transaction_table = $_POST['transaction_table'];
$transaction_id = '';
$File_Uploads_Path = "uploads/upload_transactions_with_ai/";
$fileuploadeddetaials = $_FILES['file'];
if($fileuploadeddetaials['error'] != "4"){
    $temp = explode(".", $fileuploadeddetaials['name']);
    $Filename=  "file_with_ai_".time()."_".uniqid();
    $upload_transactions_file = $Filename.'.' .end($temp);
    $fullPath = SITEROOT.$File_Uploads_Path.$upload_transactions_file;
   move_uploaded_file($fileuploadeddetaials["tmp_name"], $fullPath);
}
$filePath = $fullPath;
$fullPathfilename = $File_Uploads_Path.$upload_transactions_file;
$aipostData = [
            'file' => $_FILES,
            'company_id' => $_POST['company_id'],
            'db_name' => $_POST['db_name'],
            'user_id' => $_POST['user_id'],
            'upload_transactions_file' => $upload_transactions_file
        ];
if($_POST['last_id'] !=""){  
    $_POST['ai_result'] = json_decode($_POST['ai_result'], true);
    $insertarray = array('api_response_details'=>json_encode($_POST),'response_date_time'=>date("Y-m-d H:i:s"),'is_active'=>'0','current_status'=>'0','updated_date'=>date("Y-m-d H:i:s"),'updated_by'=>$_POST['user_id']);
    $inserturl = API."?mode=updaterowtabledata&rid=".$_POST['last_id']."&table=api_hits&method=post&company_id=".$_POST['company_id']."&data_name=".$_POST['db_name']."&userid=".$_POST['user_id']; 
}else{      
    $_POST['ai_result'] = json_decode($_POST['ai_result'], true);
    $insertarray = array('company_id'=>$_POST['company_id'],'application_table'=>$transaction_table,'transaction_id'=>$transaction_id,'api_master_id'=>46,'api_url'=>'','api_request_parameters'=>json_encode($aipostData),'request_date_time'=>date("Y-m-d H:i:s"),'api_response_details'=>json_encode($_POST),'response_date_time'=>date("Y-m-d H:i:s"),'is_active'=>'0','current_status'=>'0','created_date'=>date("Y-m-d H:i:s"),'created_by'=>$_POST['user_id'],'response_status'=>200);
    $inserturl = API."?mode=adddata&table=api_hits&method=post&company_id=".$_POST['company_id']."&data_name=".$_POST['db_name']."&userid=".$_POST['user_id']; 
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $inserturl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $insertarray);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$insertresponse = curl_exec($ch);
$insertresponsede = json_decode($insertresponse,1);
$last_id = $insertresponsede['LASTID'];
echo json_encode(array('status'=>'success', 'LASTID'=>$last_id));
?>