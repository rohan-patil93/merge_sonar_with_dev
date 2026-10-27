<?php
$timeprint = '0';
$maxtimeshow = 40;
$_SESSION['apply_js']['calender'] = '1';
$_SESSION['apply_js']['float'] = '1';
$_SESSION['apply_js']['amchart'] = '1';
$_SESSION['apply_js']['multiselest'] = 1;
$table_name = "request_work_from_home";

//rohan added leave policy
if (isset($_POST['submit']) && $_POST['submit']=='Add') {
        $data['effective_from_date'] = date('Y-m-d',strtotime(str_replace('/', '-', $_POST['effective_from_date'])));
        $data['effective_to_date'] = date('Y-m-d',strtotime(str_replace('/', '-', $_POST['effective_to_date'])));

        $data['emp_id'] = $_POST['emp_id'];
        $data['number_days'] = $_POST['number_days'];
        $data['special_request'] =  trim($_POST['special_request']);
        $data['request_type'] =  $_POST['request_type'];

        if($_POST['rid']==''){
            $data['company_id'] = $_SESSION['company_id'];
            $data['status_date'] = date('Y-m-d H:i:s');
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['created_by'] = $_SESSION['userid'];
            $data['current_status'] = 0;
            $data['is_active'] = 0;
            $url = API."?mode=adddata&table=".$table_name."&method=post";
            $response = file_post_contents($url, $data, 'insert');
            header("location:index.php?option=COM_INSERT_REQUEST_WORK_FROM_HOME");
        }else{
            $data['updated_date'] = date('Y-m-d H:i:s');
            $data['updated_by'] = $_SESSION['userid'];
            $url = API."?mode=updaterowtabledata&rid=".$_POST['rid']."&table=".$table_name."&method=post";
            $response = file_post_contents($url, $data, 'update');

            header("location:index.php?option=COM_INSERT_REQUEST_WORK_FROM_HOME&Rid=".deocdeId('id:'.$_POST['rid']));

        }
    }
//leave policy

if($timeprint == '1' && $maxtimeshow >= '1'){
    echo "gettocheckleavetypecriteria - ".date('m/d/Y h:i:s a')."</br>";
}

$emp_id = $_SESSION['emp_id'];
$leavecriterialisturl = API."?mode=gettocheckleavetypecriteria&emp_id=".$emp_id;
$leavecriterialistres = callAPI($leavecriterialisturl);
$leavecriterialistres = json_decode($leavecriterialistres,1);
$leavetype_detrails  = $leavecriterialistres['leavetype_detrails'];
$buddy_details  = $leavecriterialistres['buddy_details'];
if($timeprint == '1' && $maxtimeshow >= '1'){
    echo "gettabledetailswithconditions&table=employee_leaves - ".date('m/d/Y h:i:s a')."</br>";
}
$rid =  encodeId($_GET['Rid']);
$rid = explode(":",$rid);
 $Rid = $rid['1'];
if($Rid!=""){
    $vdata3["where"] = array('id'=>$Rid,'company_id'=>$_SESSION['company_id'],'is_active'=> 0);
    $vda3 = urlencode(json_encode($vdata3));
    $vurl3 = API."?mode=gettabledetailswithconditions&table=".$table_name."&data=".$vda3;
    $leave_emp = callAPI($vurl3);
    $employee_leaves = json_decode($leave_emp,1);
    $employee_leaves = $employee_leaves[0];
}

?>
<script type="text/javascript">
function tocheckdays(){

    var startDate = $('#effective_from_date').val();
    var endDate   = $('#effective_to_date').val();

    if(startDate && endDate){

        // Split DD-MM-YYYY
        var s = startDate.split('-');
        var e = endDate.split('-');

        var start = new Date(s[2], s[1]-1, s[0]);
        var end   = new Date(e[2], e[1]-1, e[0]);

        var diffTime = end - start;

        var diffDays = diffTime / (1000 * 60 * 60 * 24);

        if(!isNaN(diffDays) && diffDays >= 0){
            $('#number_days').val(diffDays + 1); // inclusive count
        }else{
            $('#number_days').val('');
        }
    }
}
function validateForm() {

    var requestType = document.querySelector('input[name="request_type"]:checked');
    var startDate   = document.getElementById("effective_from_date").value.trim();
    var endDate     = document.getElementById("effective_to_date").value.trim();
    var numberDays  = document.getElementById("number_days").value.trim();
    var specialReq  = document.querySelector('textarea[name="special_request"]').value.trim();

    // Request Type
    if (!requestType) {
        tostarnotify('top', 'right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',"Please select Request Type.",'');

        return false;
    }

    // Start Date
    if (startDate === "") {
        tostarnotify('top', 'right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',"Please select Start Date.",'');

        return false;
    }

    // End Date
    if (endDate === "") {
        tostarnotify('top', 'right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',"Please select End Date.",'');

        return false;
    }

    var s = startDate.split('-');
    var e = endDate.split('-');

    if (s.length !== 3 || e.length !== 3) {
        tostarnotify('top', 'right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',"Invalid date format. Please select valid dates.",'');

        return false;
    }

    var start = new Date(s[2], s[1]-1, s[0]);
    var end   = new Date(e[2], e[1]-1, e[0]);

    if (end < start) {
        tostarnotify('top', 'right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',"End Date cannot be earlier than Start Date.",'');

        return false;
    }

    // Number of days
    if (numberDays === "" || parseInt(numberDays) <= 0) {
        tostarnotify('top', 'right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',"Number of Days is invalid.",'');
        return false;
    }

    return true;
}
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

<div class="flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                <div class="card card-custom " id="kt_page_sticky_card">
    if($employee_leaves['current_status'] !=""){

        $workflowsteps = getWorkflowStatus($table_name, $Rid, $employee_leaves['current_status']);
        echo $workflowsteps['current_status_ribbon'];
    }?>
                    <div class="card-header table-card-header">
                        <h5>Work From Home / On-Duty Request  </h5>
                        <div class="card-header-right">

                            <?php echo $workflowsteps['workflow_btn'];?>
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>

            </div>
            <div class="row">
                <div class=" col-lg-12">
                <?php
            if($employee_leaves['current_status'] !=""){
                echo $workflowsteps['breadcrumb'];
            }?>
        </div>
                <div class=" col-lg-6">

                <div class="card-body" <?php if($employee_leaves['current_status']>0){echo 'style="pointer-events: none; opacity: 0.7;"';
} ?>>
                    <?php if($error_message !=''){ ?>
                    <div class="alert alert-danger background-danger alert-dismissible">
                        <strong><?php echo $error_message; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php } ?>
                    <input type="hidden" name="old_leave_days" id="old_leave_days" value="<?php echo $employee_leaves['leave_days'] !=""?$employee_leaves['leave_days']:'0'; ?>">
                    <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                        <input type="hidden" name="rid" id="rid" value="<?php echo $Rid; ?>">
                        <div class="card card-custom gutter-b card-stretch">
                            <div class=" d-flex flex-column">
                                <?php echo $dis_status=todisplayWorkflowstatuslist(array('table'=>$table,'rid'=>$employee_leaves['id'],'current_status'=>$employee_leaves['current_status'])); ?>
                            </div>
                        </div>
                        <div class="leaveformshoehide row col-lg-12" >
                            <div class="col-lg-12 ">
                            <?php
                            if (!empty($employee_leaves['request_type']) || $employee_leaves['request_type'] == '0') {
                                $selected_type = $employee_leaves['request_type'];
                            }
                            else {
                                $selected_type = '0';
                            }
                            ?>
                                <span class="form-label">Request Type</span>
                                <div class="form-radio ">
                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                            <input aria-label="Request Type" type="radio" name="request_type" value="0"
                                                <?php if($selected_type == '0') { echo 'checked';
} ?>>
                                            <i class="helper"></i>Work From Home
                                        </label>
                                    </div>

                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                            <input aria-label="Request Type" type="radio" name="request_type" value="1"
                                                <?php if($selected_type == '1') { echo 'checked';
} ?>>
                                            <i class="helper"></i>On-Duty
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-6 ">
                                <span class="form-label">Start Date(s)</span>
                                <div class=" form-group">
                                    <input aria-label="Input field" type="text" onchange="tocheckdays()" class="form-control startdatepicker"  value="<?php echo  $_POST['effective_from_date']!=""?$_POST['effective_from_date']:dateFormate($employee_leaves['effective_from_date']); ?>"  name="effective_from_date"  id="effective_from_date" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-label">End Date(s)</span>
                                <div class=" form-group">
                                    <div class=" date ">
                                        <input aria-label="Input field" type="text" onchange="tocheckdays()" class="form-control enddatepicker"  value="<?php echo  $_POST['effective_to_date']!=""?$_POST['effective_to_date']:dateFormate($employee_leaves['effective_to_date']); ?>"  name="effective_to_date"  id="effective_to_date" />
                                    </div>
                                </div>
                            </div>


                            <div class=" col-lg-6 ">
                                <span class="form-label">Number Of  Days</span>
                                <?php  $cleave_days = $_POST['days']!=""?$_POST['days']:$employee_leaves['leave_days'];
                                $cleave_days = $cleave_days !="" ? $cleave_days : 1;
                                ?>
                                <input aria-label="Number Days" type="text" class="form-control" name="number_days" value="<?php echo $_POST['number_days']!=""?$_POST['number_days']:$employee_leaves['number_days']; ?>" id="number_days" readonly="">
                            </div>

                            <div class=" col-lg-6 form-group">
                                <span class="form-label">Special Request</span>
                                <textarea aria-label="Special Request" type="text" class="form-control" name="special_request" rows="5"><?php echo $_POST['special_request']!=""?$_POST['special_request']:$employee_leaves['special_request'];?></textarea>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>" />
                                        <input type="hidden" name="submit" value="Add" />
                                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" <?php if($employee_leaves['current_status'] == ST_ACTIVE || $employee_leaves['current_status'] ==ST_SEND_BACK || $employee_leaves['current_status'] ==""){
}else{ echo "disabled";
} ?>  name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
                <div class="col-lg-6" >
                    <div class="card card-custom">

                        <div class="card-header">
                            <h5>My Work From Home Requests</h5>

                        </div>
                        <div class="card-body">
                            <input type="hidden" name="calendar_cat" id="calendar_cat" value="28">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
