<?php
if($_POST['rrid'] !=""){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $rid = $_POST['rrid'];
    $transaction_table = $_POST['transaction_table'];
    $apiusrl = API."?mode=getcrmleads&transaction_table=".$transaction_table."&rid=".$rid;
    $page_list = callAPI($apiusrl);
    $res = json_decode($page_list,1);

    $transaction = $res['transaction'];
    $crm_general_codes = $res['crm_general_codes'];
    $application_users = $res['application_users'];
    $countries =$res['countries'];
    $states =$res['states'];
    $cities =$res['cities'];
    $crm_companies =$res['crm_companies'];
    $crm_campaigns = $res['crm_campaigns'];
    $crm_lead_types =$res['crm_lead_types'];
    $crmleaddetailstypes = array();
    foreach($crm_lead_types as $crmlt){
        if($crmlt['type'] == 2){
            $crmleaddetailstypes[$crmlt['id']] =$crmlt;
        }
    }
    $crm_lead_details = $res['crm_lead_details'];
    foreach($crm_lead_details as $crmhist){
        if($crm_history_id == $crmhist['id']){
            $task_crm_history = $crmhist;
        }
    }
    $project_task_status = $res['project_task_status'][$transaction_table];
}
?>
<div class="card-header mb-3">
    <h5>Histories</h5>
</div>
<div class="card-block">
    <div class="scroll-widget">
        <div class="latest-update-box">
            <div class="row p-b-30">
                <div class="col-auto text-end update-meta p-r-0">
                    <i class="ti-server bg-c-red update-icon"></i>
                </div>
                <div class="col p-l-5">
                    <a href="#!"><h6><?php echo $transaction['created_date']; ?></h6></a>
                    <p class="text-muted m-b-0">Create Lead</p>
                </div>
            </div>
            <?php foreach($crm_lead_details as $crmld){
                    $meg = "";
                    if($crmld['lead_type'] == '2' || $crmld['lead_type'] == '3'){
                        $meg .= "<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>".$crmld['comment'];
                    }elseif($crmld['lead_type'] == '4'){
                        $meg = "<span class='d-block'>".$crmld['phone']." ".$crmld['time']."</span>".$crmld['sms_text'];
                    }elseif($crmld['lead_type'] == '5'){
                        $meg .= "<span class='d-block'>".$crmld['recipient_email_id']."</span>".$crmld['sms_text'];
                    }elseif($crmld['lead_type'] == '7' || $crmld['lead_type'] == '6'){ //meeting
                        $member = json_decode($crmld['member'],1);
                            $membermsg = "";
                            foreach($member as $members){
                                $membermsg .= "<span class='m-r-5'>".$_SESSION['users'][$members].",</span>";
                            }
                        $meg .= "<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>".$membermsg." <span class='d-block'>".$crmld['comment']."</span>";
                    }elseif($crmld['lead_type'] == '8'){ //visit
                         $meg .= "<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>";
                         $meg .= "<span class='d-block'>".$crmld['address']." ".$crmld['post_code']."</span>";
                         $meg .=$crmld['comment'];
                    }else{
                         $meg .= $crmld['comment'] !=""?$crmld['comment']:"-";
                    }


                ?>
            <div class="row p-b-30">
                <div class="col-auto text-end update-meta p-r-0">
                    <i class="<?php echo $crmleaddetailstypes[$crmld['lead_type']]['icon']; ?> bg-c-red update-icon"></i>
                </div>
                <div class="col p-l-5">
                    <a href="#!"><h6><?php echo $crmld['created_date']; ?></h6></a>
                    <p class="text-muted m-b-0"><?php echo $meg; ?></p>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>
