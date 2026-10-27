<?php
if($_POST['ajaxcall'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $finance_tax_year = $_POST['finance_tax_year']!=""?$_POST['finance_tax_year']:"";
    $transaction_id = $_POST['transaction_id'];
    $url = API."?mode=getemtasksendforapprove&transaction_id=".$transaction_id."&admin_type=".$_SESSION['admin_type']."&finance_tax_year=".$finance_tax_year; $api = callAPI($url);
    $response = callAPI($url);
    $result = json_decode($response,1);
     $transaction_activity_schedule = $result['transaction_activity_schedule'];

}
?>
<script type="text/javascript">
function checkVendorValidationEmpty(){
    $("#submitvendordetailform").submit();
}
function getmSMENumber(){
    if($("#msme_register_yes_no").is(":checked")) {
        $("#msme_number_id").show('slow');
    }else{
        $("#msme_number_id").hide('slow');
    }
}

</script>
<?php $notsendtoapprove = 0; ?>
<div class="modal-header">
    <h5 class="modal-title">Vendor details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <?php

                $transactionactivityschedule = $pendingtask =  array();
                foreach($transaction_activity_schedule as $tas){
                    $alldone = $requerdcnt =$mandatory = 0;
                    if($tas['current_status'] == ST_ACTIVE){
                        $transactionactivityschedule[ST_ACTIVE][] = $tas;

                        $check_list = json_decode($tas['check_list'], 1);
                        foreach($check_list as $check_lits){
                            if($check_lits['mandatory_checklist'] =='Y'){
                                $notsendtoapprove = 1;
                                $pendingtask[] = $tas;
                                break;
                            }
                        }
                    }else{
                        $check_list = json_decode($tas['check_list'], 1);
                        foreach($check_list as $check_lits){
                            if($check_lits['checklist_completed'] =='Y'){
                                $alldone++; // solve count
                            }
                            if($check_lits['mandatory_checklist'] =='Y'){
                                $requerdcnt++; // mandatory count
                            }
                            if($check_lits['mandatory_checklist']=='Y' && $check_lits['checklist_completed'] !='Y'){
                                $mandatory++; // all mandatory solve requerd
                            }
                        }
                        if($requerdcnt <= $alldone && $mandatory == '0'){
                            $transactionactivityschedule['SEND_FOR_APPROVE'][] = $tas;
                        }else{
                            $transactionactivityschedule['PENDDING_TASK'][] = $tas;
                            $pendingtask[] = $tas;
                            $notsendtoapprove = 1;
                        }
                    }
                }
            ?>

            <div class="">
                <div class="card">
                    <div class="card-header">
                        <h5>Pending Tasks</h5>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">

                        <?php if(count((array)$pendingtask) !=0){ ?>
                        <table class="table table-hover m-b-0 " >
                            <?php foreach($pendingtask as $pendingtask){ ?>
                            <tr >
                                <th scope="col" class="p-2"><?php echo $pendingtask['task_name']; ?></th>
                            </tr>
                            <?php } ?>
                        </table>
                        <?php }else{ ?>
                        <div class="card-px text-center py-20 mt-2">
                            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Pending Tasks </p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <?php if($notsendtoapprove == 0){ ?>
    <button type="button" class="btn btn-primary waves-effect waves-light" id="popsubmitforreview_btn" onclick="UpdateSendVerificationProfileStatus()">Submit For Review</button>
<?php }else{ echo "<span class='text-danger'><i class='ti-alert'></i>".$_SESSION['error_message']['9176']."</span>";
} ?>
</div>
