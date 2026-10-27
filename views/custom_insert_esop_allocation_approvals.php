<?php
$_SESSION['apply_js']['multiselest'] = "1";
$getrid =  encodeId($_GET['Rid']);
$decodrid = urldecode($getrid);
$spitrid = explode(':', $decodrid);
$rid = $spitrid['1'];
$url1 = API."?mode=getesopallocations&rid=".$rid."&page_type=APPROVAL";
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$esop_allocations = $result['esop_allocations'];
$esop_vesting_schedule = $result['esop_vesting_schedule'];
$esop_vesting_schedule_cnt = $result['esop_vesting_schedule_cnt'];
$statuse = ViewStatusData();
$status_progress = $result['status_progress'];
$currentdate = date('Y-m-d');
foreach($esop_vesting_schedule as $evs){
    if($evs['current_status'] == '0' && $currentdate <= $evs['till_date']){
        $nextvestingdate = $evs['allocation_date'];
        break;
    }
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
    function updateDynamicCurrentStataus(status,rid){
        if(status == 199){
            msg = "Approve";
        }else{
            msg ="Reject";
        }
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: 'Are You Sure Update The Status To '+msg,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                jQuery.ajax({
                    url: "ajax.php",
                    data:'page=updatedynamiccurrentstataus&table=esop_vesting_schedule&status='+status+'&rid='+rid,
                    type: "POST",
                    success:function(response){
                        console.log(response);
                        window.location.reload();
                    },
                    error:function (){}
                });
            }
        });


    }
</script>
<div class="card">
            <?php if($esop_allocations['id'] !=""){
                $workflowsteps = getWorkflowStatus('esop_allocations',$esop_allocations['id'],$esop_allocations['current_status']);
                echo $workflowsteps['current_status_ribbon'];
} ?>
            <div class="card-header">
                <h5>ESOP Allocation</h5>
                <div class="card-header-right table-card-header m-r-50">
                    <?php echo $workflowsteps['workflow_btn']; ?>
                </div>
            </div>
            <div class="card-body">
<?php echo $workflowsteps['breadcrumb']; ?>

<div class="row">
    <input type="hidden" id="rid" value="<?php echo $rid; ?>">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5>Employee Detail</h5>
            </div>
            <div class="card-body">
                <div class="row" id="insertfrmopen">
                    <div class="form-group col-lg-4">
                        <span class="form-label d-block f-w-600">Employee Code</span>
                        <span><?php echo $esop_allocations['emp_code']; ?></span>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label d-block f-w-600">Employee Name <span class="text-danger">*</span></span>
                        <span><?php echo $esop_allocations['emp_name']; ?></span>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label d-block f-w-600">Date of Joning</span>
                        <span><?php echo $esop_allocations['date_of_joining'] !=""?fromDate($esop_allocations['date_of_joining']):"-"; ?></span>
                    </div>
                    <div class="form-group col-lg-4 mt-2">
                        <span class="form-label d-block f-w-600">Designation </span>
                        <span><?php echo $esop_allocations['designation']; ?></span>

                    </div>
                    <div class="form-group col-lg-4 mt-2">
                        <span class="form-label d-block f-w-600">Annual CTC <span class="text-danger">*</span></span>
                        <span><?php echo $esop_allocations['annual_ctc']; ?></span>

                    </div>
                    <div class="form-group col-lg-4 mt-2">
                        <span class="form-label d-block f-w-600">Tenure (Year)</span>
                        <span><?php echo $esop_allocations['tenure']; ?></span>

                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5>ESOP Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-lg-4">
                        <span class="form-label d-block f-w-600">Grant Price</span>
                        <span><?php echo customeummarmonyformateifine($esop_allocations['grant_price']); ?></span>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label d-block f-w-600">Options Granted</span>
                        <span><?php echo $esop_allocations['option_granted']; ?></span>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label d-block f-w-600">Discount per Option</span>
                        <span><?php echo customeummarmonyformateifine($esop_allocations['discount_per_option']); ?></span>
                    </div>
                    <div class="form-group col-lg-4 mt-2">
                        <span class="form-label d-block f-w-600">Total Discount</span>
                        <span><?php echo customeummarmonyformateifine($esop_allocations['total_discount']); ?></span>
                    </div>
                    <div class="form-group col-lg-4 mt-2">
                        <span class="form-label d-block f-w-600">Price Per Option </span>
                        <span><?php echo customeummarmonyformateifine($esop_allocations['expected_option_benefit']); ?></span>
                    </div>
                    <div class="form-group col-lg-4 mt-2">
                        <span class="form-label d-block f-w-600">Total Expected Benefit</span>
                        <span><?php echo customeummarmonyformateifine($esop_allocations['total_expected_benefit']); ?></span>
                    </div>
                    <div class="form-group col-lg-4 mt-2">
                        <span class="form-label d-block f-w-600">Benefit, CTC</span>
                        <span><?php echo $esop_allocations['benefit_ctc']; ?></span>
                    </div>
                    <div class="form-group col-lg-4 mt-2">
                        <span class="form-label d-block f-w-600">Notes</span>
                        <span><?php echo $esop_allocations['note']; ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-7">

        <div class="card">
                <div class="card-header">
                    <h5>Bucket</h5>

                </div>
                <div class="card-body">


                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card prod-p-card card-green mb-2 ">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h7 class="m-b-5 text-white">Next Vesting Date</h7>
                                                <h3 class="m-b-0 f-w-700 text-white f-18"><?php echo $nextvestingdate !=""?date("M d-Y",strtotime($nextvestingdate)):"-"; ?></h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fa fa-calendar-check text-c-blue f-18"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card prod-p-card card-blue mb-2">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h7 class="m-b-5 text-white">Total Share</h7>
                                                <h3 class="m-b-0 f-w-700 text-white f-18"><?php echo customNanAndEmptyValidation($esop_vesting_schedule_cnt['total_share']); ?></h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-database text-c-blue f-18"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card prod-p-card card-yellow mb-2">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h7 class="m-b-5 text-white">Allocated Share</h7>
                                                <h3 class="m-b-0 f-w-700 text-white f-18"><?php echo customNanAndEmptyValidation($esop_vesting_schedule_cnt['allocated_share']); ?></h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-database text-c-blue f-18"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row text-center mt-3">
                                <div class="col-lg-4">
                                    <span class="form-label f-w-700 d-block">ESOP Plan</span>
                                    <span><?php echo $esop_allocations['plan_name']; ?></span>
                                </div>
                                <div class="col-lg-4">
                                    <span class="form-label f-w-700 d-block">Grant Date</span>
                                    <span><?php echo fromDate($esop_allocations['date_of_grant']); ?></span>
                                </div>
                                <div class="col-lg-4">
                                    <span class="form-label f-w-700 d-block">Bucket Name</span>
                                    <span><?php echo $esop_allocations['bucket_name']; ?></span>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <span class="form-label f-w-700 d-block">Discount(%)</span>
                                    <span><?php echo $esop_allocations['discount']; ?></span>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <span class="form-label f-w-700 d-block">BS Value</span>
                                    <span><?php echo $esop_allocations['bs_option_value']; ?></span>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <span class="form-label f-w-700 d-block">Total Shares</span>
                                    <span><?php echo $esop_allocations['options_granted']; ?></span>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-3  text-center">
                                    <div class="s-caption  text-primary"><i class="fas fa-database  f-20"></i></div>
                                    <div class="s-cont d-inline-block mt-2">
                                        <h6 class="f-w-700 m-b-2"><?php echo customNanAndEmptyValidation($esop_vesting_schedule_cnt['un_allocated_share']); ?></h6>
                                        <p class="m-b-0 f-12">Un-Allocate Shares</p>
                                    </div>
                                </div>
                                <div class="col-3 b-l-default  text-center">
                                    <div class="s-caption  text-primary"><i class="fas fa-money-bill-alt text-c-red f-20"></i></div>
                                    <div class="s-cont d-inline-block mt-2">
                                        <h6 class="f-w-700 m-b-2"><?php echo customeummarmonyformateifine($esop_vesting_schedule_cnt['total_ammount']); ?></h6>
                                        <p class="m-b-0 f-12">Total Due Amount</p>
                                    </div>
                                </div>
                                <div class="col-3 b-l-default  text-center">
                                    <div class="s-caption  text-primary"><i class="fas fa-money-bill-alt text-c-red f-20"></i></div>
                                    <div class="s-cont d-inline-block mt-2">
                                        <h6 class="f-w-700 m-b-2"><?php echo customeummarmonyformateifine($esop_vesting_schedule_cnt['allocated_ammount']); ?></h6>
                                        <p class="m-b-0 f-12">Total Pay Amount</p>
                                    </div>
                                </div>
                                <div class="col-3 b-l-default  text-center">
                                    <div class="s-caption  text-primary"><i class="fas fa-money-bill-alt text-c-red f-20"></i></div>
                                    <div class="s-cont d-inline-block mt-2">
                                        <h6 class="f-w-700 m-b-2"><?php echo customeummarmonyformateifine($esop_vesting_schedule_cnt['un_allocated_ammount']); ?></h6>
                                        <p class="m-b-0 f-12">Total Schedule Amount</p>
                                    </div>
                                </div>
                            </div>

                    <?php
                    $vesting_schedule = json_decode($esop_allocations['vesting_schedule'],1);
                    ?>
                    <div class=" mt-4">
                        <table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
                            <thead>
                                <tr>

                                    <th scope="col">Allocation Date</th>
                                    <th scope="col" class="text-right">(%)</th>
                                    <th scope="col">Till Date</th>
                                    <th scope="col" class="text-right">Share</th>
                                    <th scope="col" class="text-right">Amount</th>
                                    <th scope="col">Status</th>
                                    <?php if(in_array(ESOP_ADMIN_ROLE,(array)$_SESSION['role_ids'])){ ?>
                                    <th scope="col" class="text-center">Action</th>
                                <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1;
                                if(count((array)$esop_vesting_schedule) !="0"){
                                    $vestingschedule = $esop_vesting_schedule;
                                }else{
                                    $vestingschedule = $vesting_schedule;
                                }
                                foreach($vestingschedule as $vestingschedule){
                                    if(count((array)$esop_vesting_schedule) !="0"){
                                        $vesting_percentage = $vestingschedule['Percentage'];
                                        $allocation_date =  $vestingschedule['allocation_date'];
                                    }else{
                                        $vesting_percentage = $vestingschedule['vesting_percentage'];
                                        $allocation_date = $vestingschedule['vesting_date'];
                                    }
                                    $current_status = $vestingschedule['current_status']!=""?$vestingschedule['current_status']:0;
                                    ?>
                                    <tr>

                                        <td><?php echo fromDate($allocation_date); ?></td>
                                        <td class="text-right"><?php echo $vesting_percentage; ?>%</td>
                                        <td><?php echo fromDate($vestingschedule['till_date']); ?></td>
                                        <td class="text-right"><?php echo $vestingschedule['allocation_share']; ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformateifine($vestingschedule['total_amount']); ?></td>
                                        <td>
                                             <span class="form-label label label-md" style="color:<?php echo $statuse[$current_status]['color']; ?>; background-color:<?php echo $statuse[$current_status]['background_color']; ?>;"><?php echo $status_progress[$current_status]['name']; ?></span>
                                        </td>
                                        <?php if(in_array(ESOP_ADMIN_ROLE,(array)$_SESSION['role_ids'])){ ?>
                                        <td class="text-center">
                                            <?php
                                    if($vestingschedule['current_status'] == 0 && $currentdate >= $allocation_date && $currentdate <= $vestingschedule['till_date']){ ?>
                                    <div class="btn-group" >
                                        <button type="button" class="btn btn-success btn-sm waves-effect waves-light"  onclick="updateDynamicCurrentStataus('<?php echo ST_APPROVED; ?>','<?php echo $vestingschedule['id']; ?>')"> <i class="ti-thumb-up"></i> Allocate</button>
                                        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="updateDynamicCurrentStataus('<?php echo ST_REJECTED; ?>','<?php echo $vestingschedule['id']; ?>')"><i class="ti-thumb-down"></i> Reject</button>
                                    </div>
                                        <?php }else{ echo "No Action";
} ?>
                                    </td>
                                    <?php } ?>

                                    </tr>
                                    <?php $i++;
} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
