<?php
include_once'../dbconfig.php';
include_once'../comm.php';
$transaction_table = $_POST['transaction_table'];
$transaction_id = '';
$File_Uploads_Path = "/uploads/upload_transactions_with_ai/";
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
$data1["where"] = array('company_id'=> $_POST['company_id'],'api_code'=>'AIINTEGRATIONPUSHFILE','is_active'=>'0');
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
        $aiurl = "https://aidev.digixlabs.cloud/api/upload";
        $cfile = new CURLFile($filePath, mime_content_type($filePath), basename($filePath));
        $aipostData = [
            'file' => $cfile,
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
            echo 'Error: ' . curl_error($ch);
        }else{
            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $aiurl);
            curl_setopt($ch2, CURLOPT_POST, true);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, $aipostData);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true); 
            $headers = [
                'API-Token: X9mQ2Zp7Lk4T8wE5rV1yU0cN6bH3sAqD',
                'Content-Type: multipart/form-data' 
            ];
            curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);           
            if (curl_errno($ch2)) {
                echo 'Error: ' . curl_error($ch2);
            }else{  
                $response11 = curl_exec($ch2);
                $retres = json_decode($response11,1);
              if ($retres['status'] === 'completed'){ 
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
                    echo transactionDetailsUI($retres,$fullPathfilename);
               }else{
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
                    echo '<div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Oops.. Processing is taking longer than expected. Please re-upload the file. -  '.$retres['errorMessage'].'</p>   
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="../assets/images/work.png">
                        </div>   
                    </div>';
               }
            } 
           
        }
    }
}
function transactionDetailsUI($retres, $fullPathfilename){
    $matchValidationErrors = $retres['matchValidationErrors'];
    $validationWarnings = array_merge($retres['validationWarnings'],$retres['matchValidationWarnings']);
    $lineItems = $retres['lineItems'];
    $infisuiteLastId = $retres['infisuiteLastId'];
    $encodeId = deocdeId('id:'.$infisuiteLastId);
    $html  = '<ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#response" role="tab" aria-selected="false" tabindex="-1">Response</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#documents" role="tab" aria-selected="false" tabindex="-1">Documents</a>
                </li>
            </ul>';
   if(is_numeric($infisuiteLastId)){
        $html .= '<div class="tab-content tabs">
                    <div class="tab-pane card-body p-0 active show" id="response" role="tabpanel"> 
                        <div class="alert alert-primary alert-dismissible">
                        <strong class="text-dark">Transaction has been successfully created. </strong> <a class="text-primary" href="index.php?option=COM_INSERT_PURCHASE_ORDERS&Rid='.$encodeId.'">Click here to open the transaction.</a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        </button>
                    </div>';
   }else{
        $html .= '<div class="tab-content tabs">
                    <div class="tab-pane card-body p-0 active show" id="response" role="tabpanel"> 
                        <div class="alert alert-danger alert-dismissible">
                        <strong class="text-danger">We encountered an error while uploading the document. Please review and try uploading it again.</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        </button>
                    </div>';
   }
    $html .= '<div class="row ">
   <div class="col-lg-6">
   <table class="table table-hover m-b-0" style="font-size:13px;">
        <thead>
            <tr>
                <th>#</th>
                    <th>Error</th>
                </tr>
            </thead>
            <tbody>';

          $matcherorse1 =1; 
          foreach($matchValidationErrors as $matcherror){    
    $html .= '<tr>
                    <td class="p-2">'.$matcherorse1.'</td>
                    <td class="w-b-w-s p-2 text-danger">'.$matcherror.'</td>
                </tr>';
          $matcherorse1++;  }
    $html .= '</tbody>
        </table>  
    </div>
    <div class="col-lg-6">
        <table class="table table-hover m-b-0" style="font-size:13px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Warning</th>
                    </tr>
                </thead>
                <tbody>';

          $matcherorse1 =1; 
          foreach($validationWarnings as $matchworning){    
    $html .= '<tr>
                    <td class="p-2">'.$matcherorse1.'</td>
                    <td class="w-b-w-s p-2">'.$matchworning.'</td>
                </tr>';
          $matcherorse1++;  }
    $html .= '</tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Document Type</h5>
                        <p class="m-t-10 f-12 ">'.$retres['documentType'].'</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Party Name</h5>
                        <p class="m-t-10 f-12">'.$retres['supplierName'].'</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Transaction Number</h5>
                        <p class="m-t-10 f-12">'.$retres['poNumber'].'</p>
                    </div>
                </div>
                 <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Transaction Date</h5>
                        <p class="m-t-10 f-12">'.$retres['poDate'].'</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Ref Number</h5>
                        <p class="m-t-10 f-12">'.$retres['refNumber'].'</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Ref Date</h5>
                        <p class="m-t-10 f-12">'.$retres['refDate'].'</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Sub Total</h5>
                        <p class="m-t-10 f-12 ">'.$retres['subTotal'].'</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Tax Amount</h5>
                        <p class="m-t-10 f-12">'.$retres['headerTaxAmount'].'</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Discount Amount</h5>
                        <p class="m-t-10 f-12">'.$retres['headerDiscountAmount'].'</p>
                    </div>
                </div>
                 <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Additional Charges</h5>
                        <p class="m-t-10 f-12">'.$retres['additionalCharges'].'</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Rounding-Off</h5>
                        <p class="m-t-10 f-12">'.$retres['roundingOff'].'</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-600 m-b-0">Grand Total</h5>
                        <p class="m-t-10 f-12">'.$retres['grandTotal'].'</p>
                    </div>
                </div>
            </div>
            <table class="table table-hover m-b-0" style="font-size:13px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>HSN</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total Price</th>
                        <th>Tax</th>
                        <th>Discount</th>
                    </tr>
                </thead>
            <tbody>';
          $matcherorse1 =1; 
          foreach($lineItems as $litem){    
                $html .= '<tr>
                    <td class="p-2">'.$matcherorse1.'</td>
                    <td class="w-b-w-s p-2">'.$litem['productBarcode'].' - '.$litem['productName'].'</td>
                    <td class="w-b-w-s p-2">'.$litem['hsnSac'].'</td>
                    <td class="w-b-w-s p-2">'.$litem['uom'].'</td>
                    <td class="w-b-w-s p-2">'.$litem['quantity'].'</td>
                    <td class="w-b-w-s p-2">'.$litem['unitPrice'].'</td>
                    <td class="w-b-w-s p-2">'.$litem['totalPrice'].'</td>
                    <td class="w-b-w-s p-2">'.$litem['taxAmount'].'</td>
                    <td class="w-b-w-s p-2">'.$litem['discountAmount'].'</td>
                </tr>';
                $matcherorse1++;  
            }
    $html .= '</tbody>
        </table></div>';
    $html .= '<div class="tab-pane card-body p-0 text-center" id="documents" role="tabpanel"> 
            <iframe src="'.$fullPathfilename.'" height="700" width="1000" title="Iframe Example"></iframe>
        </div>';
    $html .= '</div>';
    return $html;
}