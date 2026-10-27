<?php
echo '<pre>';
function syncCSRData($fromdb, $todb){
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://dev.infistaq.in/api.php?mode=getCSRRelatedData&user_id=1&server=dev.infistaq.in&company_id=1&data_name=$fromdb&target_db=$todb",
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

    $datas = json_decode($response,1);
    $post['chcek']='chcek';
    $post['data']=$response;

    if(count((array)$datas)>0){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://dev.infistaq.in/api.php?mode=insertOrUpdateCSRData&user_id=1&method=post&company_id=1&server=dev.infistaq.in&data_name=$todb",
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
        $decoded = json_decode($res, 1);
        print_r($decoded);
    }else{
        echo "No data found";
    }

    $syncData = [
        'table_names' =>json_encode([
            'csr_contractor_registations',
            'csr_project_tracking',
            'csr_proposal_data',
            'csr_setting',
            'csr_yearly_budget',
            'temp_csr_proposal_data',
            'transaction_approval_logs',
            'notifications',
            'notification_user'
        ])
    ];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://dev.infistaq.in/api.php?mode=insertSyncLog&method=post&user_id=1&server=dev.infistaq.in&company_id=1&data_name=$fromdb&target_db=$todb",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $syncData,
    ));

$response = curl_exec($curl);
curl_close($curl);

$decoded = json_decode($response, true);
print_r($decoded);

}

function syncTableData($fromdb, $todb, $table_name){
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://dev.infistaq.in/api.php?mode=getSingleTableData&user_id=1&server=dev.infistaq.in&company_id=1&data_name=$fromdb&table_name=$table_name&target_db=$todb",
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

    $datas = json_decode($response,1);
    $post['chcek']='chcek';
    $post['data']=$response;

    if(count((array)$datas)>0){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://dev.infistaq.in/api.php?mode=insertOrUpdateSingleTableData&user_id=1&server=dev.infistaq.in&method=post&company_id=1&data_name=$todb&table_name=$table_name",
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
        
        $decoded = json_decode($res, 1);
        print_r($decoded);
        return $decoded;
    }

}
