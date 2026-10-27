<?php
if($_POST['ajaxbank'] =="Y"){
    $employee_id = $_POST['employee_id'];
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=getempbanks&rid=".$_POST['rid'];
}else{
    $getrid = explode(':',encodeId($_GET['Rid']));
      $rid = $getrid[1];
    $url = API."?mode=getempbanks&rid=".$rid;
}
$response = callAPI($url);
$result = json_decode($response,1);
$banks = $result['banks'];
$emp_banks = $result['emp_banks'];
$emp_bank_status =
$_SESSION['apply_js']['multiselest'] = "1";
if($emp_banks['id'] !=""){
    $workflowsteps = getWorkflowStatus('emp_banks',$emp_banks['id'],$emp_banks['current_status']);
}
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
<div class="card">
    <?php echo $workflowsteps['current_status_ribbon'];?>
    <div class="card-header">
        <?php if($_POST['ajaxbank'] !="Y"){ ?>
            <h5>Bank Details</h5>
        <?php } ?>
        <div class="card-header-right table-card-header m-r-50" >
            <?php echo $workflowsteps['workflow_btn'];?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
        <?php echo $workflowsteps['breadcrumb'] ?>
        </div>
        <form method="post" id="bank_details_form" >
            <input type="hidden" name="emp_bank_rid" value="<?php echo $emp_banks['id']; ?>">
            <div class="row">
                <div class="col-xl-6 mt-2" >
                    <span class="form-label">Bank</span>
                    <div class="">
                        <select aria-label="Bank" class ="form-control form-control-lg form-select js-example-basic-single"  name="bank_id" >
                            <option value="">Select Any One</option>
                            <?php foreach ($banks as $bnk) {  ?>
                               <option <?php if($emp_banks['bank_id'] == $bnk['id']){ ?>selected <?php
} ?> value="<?php echo $bnk['id']; ?>" >
                                <?php echo $bnk['bank_name']; ?>
                              </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6 mt-2" >
                    <span  class="form-label">IFSC Code</span>
                    <input aria-label="Ifsc Code" type="text" class="form-control"  name="ifsc_code" value="<?php echo $emp_banks['ifsc_code']; ?>" placeholder="Enter IFSC Code"/>
                </div>
                <div class="col-lg-6 mt-2" >
                    <span  class="form-label">Account No</span>
                    <input aria-label="Bank Account No" type="text" class="form-control"  name="bank_account_no" value="<?php echo $emp_banks['bank_account_no']; ?>" placeholder="Enter Account No"/>
                </div>
                <div class="col-lg-6 mt-2" >
                    <span  class="form-label">Note</span>
                    <textarea aria-label="Enter Note" type="text" class="form-control"  name="request_note" placeholder="Enter Note"/><?php echo $emp_banks['request_note']; ?></textarea>
                </div>
            </div>
        </form>
    </div>
</div>

