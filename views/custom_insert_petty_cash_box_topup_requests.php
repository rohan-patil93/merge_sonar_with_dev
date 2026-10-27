<?php
$_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['getpettycashrequestsbtn'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertpettycashrequests&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_PETTY_CASH_BOX_TOPUP_REQUESTS");
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }

}

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url1 = API."?mode=getpettycashrequests&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$petty_cash_boxes = $result['petty_cash_boxes'];
$pcbtr = $result['petty_cash_box_topup_requests'];

?>
<script type="text/javascript">
function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}
function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: "Please note the status of the document will change to "+new_status_name,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
    function(){
        console.log("dataa aa");
        jQuery.ajax({
            url: "ajax.php",
            data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(response){
                console.log(response);
                var res = JSON.parse(response);
                window.location.reload();
                if(res.transaction_update.status=='sucess'){
                     window.location.reload();
                    swal({
                        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                        text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                        type: "success",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function () {
                        window.location.reload();
                    });
                }else{
                    swal({
                        title: "Oops...",
                        text: res.transaction_update.message,
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "Ok",
                        confirmButtonClass: "btn-danger",
                        closeOnCancel: false
                    });
                }
            },
            error:function (){}
        });
    });
}
</script>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>

<div class="card custom-card">
    <?php
        if($pcbtr['current_status']!=""){
          $workflowsteps = getWorkflowStatus('petty_cash_box_topup_requests',$pcbtr['id'],$pcbtr['current_status']);
          echo $workflowsteps['current_status_ribbon'];
        }
    ?>
    <div class="card-header">
        <h5>Peety Cash Request</h5>
        <div class="card-header-right table-card-header">
            <?php echo $workflowsteps['workflow_btn'];  ?>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <?php echo $workflowsteps['breadcrumb']; ?>
        </div>
        <form method="post" class="<?php echo ($pcbtr['current_status'] ==ST_ACTIVE || $pcbtr['current_status'] =="")?"":"event_disabled"; ?>">
            <div class="row" style="padding-left: 200px;padding-right: 200px;">
                <div class="form-group col-lg-6 w-full">
                    <span class="form-label">Petty Cash</span>
                    <select aria-label="Petty Cash Boxe" name="petty_cash_boxe_id" class="form-control form-select js-example-basic-single" required="">
                    <option value="">Select Any One </option>
                    <?php foreach($petty_cash_boxes as $pcb) {  ?>
                        <option value="<?php echo $pcb['pc_box_id']; ?>" <?php echo $pcb['pc_box_id'] ==$pcbtr['petty_cash_boxe_id']?"selected":""; ?>><?php echo $pcb['pc_box_code']."-".$pcb['pc_box_description']." (Limit -".$pcb['pc_limit'].")"; ?></option>
                    <?php } ?>
                </select>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Request Type</span>
                    <div class="form-radio">
                        <div class="radio radio-inline" style="pointer-events: none;">
                          <label class="form-label">
                            <input aria-label="Request Type" type="radio" name="request_type" value="A" <?php echo $pcbtr['request_type'] =="A"?"checked":""; ?> readonly>
                            <i class="helper"></i>Auto
                          </label>
                        </div>
                        <div class="radio radio-inline">
                          <label class="form-label">
                            <input aria-label="Request Type" type="radio" name="request_type" value="M" <?php echo ($pcbtr['request_type'] =="M" || $petty_cash_requests['request_type'] =="")?"checked":""; ?>>
                            <i class="helper"></i>Manual
                          </label>
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Request Amount</span>
                    <input aria-label="Request Amount" type="numbert" class="form-control text-right" name="request_amount" value="<?php echo $pcbtr['request_amount']; ?>" required>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Request Date</span>
                    <input aria-label="Transaction Date" type="text" class="form-control datepickernotprevious" name="transaction_date" value="<?php echo fromDate($pcbtr['transaction_date']); ?>" required>
                </div>
                <div class="col-lg-12 form-group">
                    <span class="form-label">Description</span>
                    <textarea aria-label="Description" type="text" class="form-control" name="description" style="height: 125px;"><?php echo $pcbtr['description']; ?></textarea>
                </div>
            </div>
            <div class="row" style="padding-left: 200px;padding-right: 200px;">
                <div class="form-group col-lg-6 <?php echo ($workflowsteps['next_status'] ==ST_APPROVED && $workflowsteps['next_step_owner'] == $_SESSION['userid'])?"":"event_disabled"; ?>">
                    <span class="form-label">Final Topup Amount</span>
                    <input aria-label="Final Amount" type="numbert" class="form-control text-right" name="final_amount" value="<?php echo $pcbtr['final_amount']; ?>" required>
                </div>
            </div>
            <div class="mt-5 <?php echo ($pcbtr['current_status'] ==ST_ACTIVE || $pcbtr['current_status'] =="" || ($workflowsteps['next_status'] ==ST_APPROVED && $workflowsteps['next_step_owner'] == $_SESSION['userid']))?"":"event_disabled"; ?>">

                <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button type="submit" class="btn btn-primary waves-effect waves-light" name="getpettycashrequestsbtn"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
            </div>
        </form>
    </div>
</div>

