<?php
include_once'../dbconfig.php';
include_once'../comm.php';
$transaction_table = $_POST['transaction_table'];
$transaction_id = '';
$data1["where"] = array('company_id'=> $_POST['company_id'],'api_code'=>'CANDIDATESCREENINGGENERATEQUESTION','is_active'=>'0');
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
        $api_url = "https://recruitstaq.digixlabs.cloud/api/screening/generate/";
        $aipostData = $_POST;
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
        }else{
            $ch2 = curl_init();
            curl_setopt_array($ch2, array(
              CURLOPT_URL => 'https://callen-ungossiping-nonoily.ngrok-free.dev/api/screening/generate/',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              //CURLOPT_POSTFIELDS => array('candidate_id' => ' 182','job_posting_id' => ' 61'),
              CURLOPT_POSTFIELDS => $aipostData,
              CURLOPT_HTTPHEADER => array(
                'X-API-Key: recruit-9f3a2b8e1c7d4f6a0e5b2c9d8f1a4e7b'
              ),
            ));
            if (curl_errno($ch2)) {
                echo json_encode(array('result'=>curl_error($ch2),'msg'=> 'Oops! There seems to be a server-side issue. Please connect with our IT team to investigate and resolve it.', 'status'=>'failed', 'lastid'=>''));
            }else{  
                $response11 = curl_exec($ch2);
                $retres = json_decode($response11,1);
                $screening = $retres['screening'];
                $questions = $screening['questions'];
                $screeningwhatsid = $screening['_id'];                
                $buttonhtml = '<button type="button" class="btn btn-primary float-right mb-1" id="ai_spproved_and_start" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click here to Start Screening" onclick="approvedstartScreening(\''.$screeningwhatsid.'\')"><i class="ti-light-bulb"></i> Scre. Start</button>';
                $question = '<div class="row " ><table class="table table-bordered table-md">
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                        </tr>';    
                $sq = 1;
                foreach($questions as $qou){
                  $question .= '<tr>
                            <td class="p-2">'.$sq.'</td>
                            <td class="p-2">'.$qou['question'].'</td>
                            </tr>';
                }
                $question .= '</table>';
                $question .= $buttonhtml;
                $question .= '</div>';   
                echo json_encode(array('status'=>'success','result'=>$retres, 'question'=> $question, 'buttonhtml'=> $buttonhtml));
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

