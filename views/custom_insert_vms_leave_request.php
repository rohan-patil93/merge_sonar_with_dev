<?php
$timeprint = '0';
$maxtimeshow = 40;
$_SESSION['apply_js']['calender'] = '1';
$_SESSION['apply_js']['float'] = '1';
$_SESSION['apply_js']['amchart'] = '1';
$_SESSION['apply_js']['multiselest'] = 1;
$table_name = "employee_leaves";
//rohan added leave policy
if(isset($_POST['submit'])){

    $data['hr_user_id'] = $_POST['hr_user_id'];
    $data['finance_user_id'] = $_POST['finance_user_id'];
    $data['leave_from'] = date("Y-m-d",strtotime($_POST['leave_from']));
    $data['start_half_day'] = $_POST['start_half_day'];
    $data['leave_to'] = date("Y-m-d",strtotime($_POST['leave_to']));
    $data['end_half_day'] = $_POST['end_half_day'];
    $data['special_request'] = $_POST['special_request'];
    $data['leave_days'] = $_POST['days'];
    $data['employee_id'] = $_SESSION['emp_id'];
    $data['company_id'] = $_SESSION['company_id'];
    $data['rid'] = $_POST['rid'];
    $data['user_id'] = $_SESSION['userid'];
    $data['applying_late'] = ((strtotime(date("d-m-Y")) - strtotime($_POST['leave_from'])) / (60 * 60 * 24));
    $data['current_status'] = '0';
    $data['is_active'] = '0';
    if($_POST['submit']=='Add'){
        $data['status_date'] = nowDate();
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
            $url = API."?mode=insertvmsleaverequest&table=".$table_name."&method=post";//custom leave policy url
            $response = file_post_contents($url, $data, 'insert');
            $res = json_decode($response,1);

            if($res['leavestatus']['leaveaplye']=='1'){
                $msgg = str_replace("{[value]}",$res['leavestatus']['error_msg'],$_SESSION['error_message'][$res['leavestatus']['error_code']]);

                ?>
                <script type="text/javascript">
                    document.onreadystatechange = function () {
                        if (document.readyState == "complete"){
                            swal({
                                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                                text: '<?php echo $msgg; ?>',
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Yes",
                                cancelButtonText: "No",
                                closeOnConfirm: true,
                                closeOnCancel: true
                            },
                            function(isConfirm) {
                            });
                        }
                    }
                </script>
                <?php
            }else{
                $response = $res['leaveinsert'];
                if($response['status'] == 'fail'){
                    if(isset($_SESSION['error'][$response['error_no']])){
                        $dis_err = explode(",", $response['other']);
                        foreach ($dis_err as $va) {
                            $dis_er [] = $dis_labels[$va];
                        }
                        $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
                    }else{
                        $error_message = $response['message'];
                    }
                }else{


}

}

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
    $vdata3["field"] = "id,leave_type_id,leave_from,leave_to,leave_days,start_half_day,end_half_day,special_request,current_status,created_by";
    $vda3 = urlencode(json_encode($vdata3));
    $vurl3 = API."?mode=gettabledetailswithconditions&table=employee_leaves&data=".$vda3;
    $leave_emp = callAPI($vurl3);
    $employee_leaves = json_decode($leave_emp,1);
    $employee_leaves = $employee_leaves[0];
}
if($timeprint == '1' && $maxtimeshow >= '1'){
    echo "gettabledetailswithconditions&table=employees- ".date('m/d/Y h:i:s a')."</br>";
}
$vdata1["where"] = array('is_active'=> 0);
$vdata1["field"] = "id,image,display_name";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
$configesponse = callAPI($vurl);
$emplos = json_decode($configesponse,1);
foreach ($emplos as  $value) {
    $empdata[$value['id']] =$value;
}
if($timeprint == '1' && $maxtimeshow >= '1'){
    echo "tosearchempleavesbydate - ".date('m/d/Y h:i:s a')."</br>";
}
$leavedate = date('Y-m-d');
$otstdate = date('Y-m-d', strtotime('-20 day', strtotime($leavedate)));
$ottodate = date('Y-m-d', strtotime('+20 day', strtotime($leavedate)));
$evurl = API."?mode=tosearchempleavesbydate&start=".$otstdate."&end=".$ottodate."&emp_id=".$_SESSION['emp_id'];
$otheremp = callAPI($evurl);
$otheremp = json_decode($otheremp,1);

$leavelist = array();
foreach($otheremp as $loopdetails){
    if($loopdetails['current_status'] == ST_APPROVED){
        $leavelist[ST_APPROVED][] = $loopdetails;
    }elseif($loopdetails['current_status'] == ST_ACTIVE){
        $leavelist[ST_ACTIVE][] = $loopdetails;
    }else{
        $leavelist['inpro'][] = $loopdetails;
    }
}
if($timeprint == '1' && $maxtimeshow >= '1'){
    echo "status start  - ".date('m/d/Y h:i:s a')."</br>";
}
$stausresult = ViewStatusData();
if($timeprint == '1' && $maxtimeshow >= '1'){
    echo "status end - ".date('m/d/Y h:i:s a')."</br>";
}
?>
<script type="text/javascript">
    function tocheckdays(){
        var date1 =  $('#kt_datepicker_31').val();
        var date2 =  $('#kt_datepicker_32').val();
        var leave_type_id =  $('.leave_type_id').val();
        var old_leave_days = $("#old_leave_days").val();
        var end_half_day = $('#end_half_day').val();
        var start_half_day = $('#start_half_day').val();
        var rid = $("#rid").val();

        console.log("Date:"+date1+":Date2:"+date2+"--old_leave_days--"+old_leave_days+"---start_half_day--"+start_half_day+"---end_half_day---"+end_half_day);
        if(date1!="" && date2!=""){
            $("#kt_login_signin_submit").css("pointer-events","visible");
            $.ajax({
                url: "ajax.php",
                data:'page=leavedaystoworkdays&leave_from='+date1+'&leave_to='+date2+'&start_half_day='+start_half_day+'&end_half_day='+end_half_day,
                type: "POST",
                success:function(data){
                    console.log(data);
                    var datas = JSON.parse(data);

                    $('#leave_days').val(datas.leave_days);
                },
                error:function (){}
            });
        }
    }
    function validateForm(){
        var date1 =  $('#kt_datepicker_31').val();
        var date2 =  $('#kt_datepicker_32').val();
        var leave_cat =  $('#kt_select2_41').val();
        if(date1 !="" && date2 !=""){
            $.ajax({
                url: "ajax.php",
                data:'page=leavedatecheck&date1='+date1+'&date2='+date2+'&leave_cat='+leave_cat,
                type: "POST",
                success:function(data){
                    if(data !=""){
                        return false;
                    }
                },
                error:function (){}
            });
        }
    }
    function selectLeaveType(value){
        if(value ==""){
            $(".leaveformshoehide").css("pointer-events","none");
            $(".leaveformshoehide").css("opacity","0.3");
        }else{
            $(".leaveformshoehide").css("pointer-events","visible");
            $(".leaveformshoehide").css("opacity","1");
        }
        $.post("ajax.php",{"value":value,"page":"selectleavetypehalfdayavailableornot"},function(data){
            var datas = JSON.parse(data);
            console.log(datas.leave_details);
            if(datas.leave_details['duration_allowed'] == '0'){
                var privday = datas.leave_details['duration_leave_count']!=""?datas.leave_details['duration_leave_count']:"0";
                $(".startdatepicker").datepicker("change",{ minDate: -privday });
            }
            var availablelv = datas.leave_details['available_leave'];
            if(datas.leave_details['halfday'] == '0'){
                $(".halfdayselect option[value='1']").prop('disabled',false);
                $(".halfdayselect option[value='2']").prop('disabled',false);
            }else{
                $(".halfdayselect option[value='1']").prop('disabled',true);
                $(".halfdayselect option[value='2']").prop('disabled',true);
            }
            $(".hilidayorweekend").html('<span class="form-label label label-md bg-info ml-2">Available leaves - '+availablelv+'</span>');
        });
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
                <?php
                if($employee_leaves['id']!=""){
                    $table = 'employee_leaves';
                    $workflowsteps = getWorkflowStatus($table,$employee_leaves['id'],$employee_leaves['current_status']);
                    echo $workflowsteps['current_status_ribbon'];
                }
                ?>
                <div class="card card-custom " id="kt_page_sticky_card">
                    <div class="card-header table-card-header ">
                        <h5>Leave Request  <?php if($rowid!=""){ ?> <span class="form-label label label-primary"> <i class="fa fa-user"></i> <?php echo ucwords($$empdata[$leave_details['employee_id']]['display_name']); ?></span> <?php
} ?> </h5>
                        <div class="card-header-right">
                            <?php  echo $workflowsteps['workflow_btn']; ?>
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                            <?php if($Rid!=""){ ?>
                            <div class="btn-group <?php echo $Rid; ?>">
                  <button onclick="displaystausdrop(<?php echo $Rid; ?>,<?php echo "'".$table_name."'"; ?>,<?php echo $employee_leaves['current_status'] ; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$table1['current_status']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$table1['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$table1['current_status']]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$table1['current_status']]['name']; ?> </button>
                  <div class="dropdown-menu" id="dropdownlist_<?php echo $rid; ?>">

                  </div>
              </div>
              <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                        </div>
                        <?php
                        if($error_message !=''){ ?>
                            <div class="alert alert-danger background-danger alert-dismissible">
                                <strong><?php echo $error_message; ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php } ?>
                        <input type="hidden" name="old_leave_days" id="old_leave_days" value="<?php echo $employee_leaves['leave_days'] !=""?$employee_leaves['leave_days']:'0'; ?>">
                        <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                            <input type="hidden" name="rid" id="rid" value="<?php echo $Rid; ?>">
                            <input type="hidden" name="hr_user_id" id="hr_user_id" value="<?php echo $buddy_details['hr_connect']; ?>">
                            <input type="hidden" name="finance_user_id" id="finance_user_id" value="<?php echo $buddy_details['finance_connect']; ?>">
                            <?php  $leavae = array(array('leves'=>10,'cons'=>5),array('leves'=>12,'cons'=>0),array('leves'=>20,'cons'=>10),array('leves'=>7,'cons'=>0),array('leves'=>10,'cons'=>0));?>
                                <?php
                                    if($employee_leaves['current_status'] ==ST_DRAFT || $employee_leaves['current_status'] ==ST_ACTIVE || $employee_leaves['current_status'] ==ST_SEND_BACK || $employee_leaves['current_status'] ==ST_CALLBACK || $employee_leaves['current_status']==""){
}else{
                                        $disbalediv = 'style="pointer-events: none; opacity: 0.8;"';
                                    }  ?>

                                    <div class="form-group col-lg-6 p-2 float-left"  <?php echo $disbalediv; ?>>

                                        <div class="leaveformshoehide" >
                                            <div class="row ">
                                                <span class="form-label">Start Date(s)</span>
                                                <div class=" col-lg-6 form-group">
                                                    <input aria-label="Input field" type="text" onchange="tocheckdays()" class="form-control startdatepicker"  value="<?php echo  $_POST['leave_from']!=""?$_POST['leave_from']:dateFormate($employee_leaves['leave_from']); ?>"  name="leave_from"  id="kt_datepicker_31" />
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <?php $starthlfdy = $_POST['start_half_day']!=""?$_POST['start_half_day']:$employee_leaves['start_half_day']; ?>

                                                    <select aria-label="Start Half Day" class="form-control form-select js-example-basic-single halfdayselect"   name="start_half_day" id="start_half_day" onchange="tocheckdays()">
                                                        <option value="0" <?php echo $starthlfdy == "0"?"selected":""; ?> > Full Day</option>
                                                        <option value="1" <?php echo $starthlfdy == "1"?"selected":""; ?>> Start Half</option>
                                                        <option value="2" <?php echo $starthlfdy == "2"?"selected":""; ?>> End Half</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <span class="form-label mb-1 mt-3">End Date(s)</span>
                                                <div class="col-lg-6 form-group">
                                                    <div class=" date ">
                                                        <input aria-label="Input field" type="text" onchange="tocheckdays()" class="form-control enddatepicker"  value="<?php echo  $_POST['leave_to']!=""?$_POST['leave_to']:dateFormate($employee_leaves['leave_to']); ?>"  name="leave_to"  id="kt_datepicker_32" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <?php $endhlfdy = $_POST['end_half_day']!=""?$_POST['end_half_day']:$employee_leaves['end_half_day']; ?>

                                                    <select aria-label="End Half Day" class="form-control form-select js-example-basic-single halfdayselect"  name="end_half_day" id="end_half_day" onchange="tocheckdays()">
                                                        <option value="0" <?php echo $endhlfdy == "0"?"selected":""; ?>> Full Day</option>
                                                        <option value="1" <?php echo $endhlfdy == "1"?"selected":""; ?>> Start Half</option>
                                                        <option value="2" <?php echo $endhlfdy == "2"?"selected":""; ?>> End Half</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="form-group col-lg-6 ">
                                                    <span class="form-label">Leave Days</span>
                                                    <?php  $cleave_days = $_POST['days']!=""?$_POST['days']:$employee_leaves['leave_days'];
                                                    $cleave_days = $cleave_days !="" ? $cleave_days : 0;
                                                    ?>
                                                    <input aria-label="Days" type="text" class="form-control" name="days" value="<?php echo $_POST['days']!=""?$_POST['days']:$employee_leaves['leave_days']; ?>" id="leave_days" readonly="">
                                                </div>
                                                <div class="col-lg-6 hilidayorweekend mt-4">

                                                </div>
                                            </div>
                                            <div class="form-group col-lg-12 form-group">
                                                <span class="form-label">Special Request</span>
                                                <textarea aria-label="Special Request" type="text" class="form-control" name="special_request" rows="5"><?php echo $_POST['special_request']!=""?$_POST['special_request']:$employee_leaves['special_request'];?></textarea>
                                            </div>
                                            <div class="card-footer">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <input type="hidden" name="submit" value="Add" />

                                                <?php if($buddy_details['hr_connect']=="0" || $buddy_details['report_to']=="0"){  ?>
                                                    <span class="text-danger f-12"><?php echo $_SESSION['error_message']['9001']; ?></span></br>
                                                <?php } ?>
                                                <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" <?php //if($employee_leaves['current_status'] == ST_ACTIVE OR $employee_leaves['current_status'] ==ST_SEND_BACK OR $employee_leaves['current_status'] ==""){}else{ echo "disabled"; } ?>  name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if($_SESSION['config']['show_leave_member']){ ?>
                                <div class="col-lg-4" style="float: left;">
                                    <div class="card card-custom card-stretch gutter-b">
                                        <div class="card-header border-0">
                                            <h3 class="card-title font-weight-bolder text-dark">Who else wil be out</h3>
                                        </div>
                                        <?php foreach ($otheremp as $othemp) {
                                            if($othemp['employee_id'] !=$empl['employee_id']){
                                                ?>
                                                <div class="card-body pt-2">
                                                    <div class="d-flex align-items-center">
                                                        <?php if($empdata[$othemp['employee_id']]['image'] !=''){ ?>
                                                            <div class="symbol symbol-60 symbol-sm flex-shrink-0 ">
                                                                <img class="img-80" src="uploads/all/<?php echo $empdata[$othemp['employee_id']]['image']; ?>" alt="Details">
                                                            </div>
                                                        <?php }else{ ?>
                                                            <div class="symbol mr-3 img-radius p-15">
                                                                <span class="symbol-label font-size-h5 img-80"><?php echo initials($empdata[$othemp['employee_id']]['display_name']); ?></span>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="ml-4">
                                                            <div class="text-dark-75 font-weight-bolder font-size-lg mb-0"><?php echo $empdata[$othemp['employee_id']]['display_name']; ?></div>
                                                            <a href="#" class="text-muted font-weight-bold text-hover-primary"><?php echo date('dS M Y',strtotime($othemp['leave_from'])) ; ?> TO <?php echo date('dS M Y',strtotime($othemp['leave_to'])) ; ?> </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group col-lg-6" style="float: left;">
                                    <div class="card card-custom">

                                        <div class="card-header">
                                            <h5>My Leaves</h5>

                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="calendar_cat" id="calendar_cat" value="6">
                                            <div id="calendar"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="row p-4">


                            <div class="col-xl-4 col-md-4">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>Pending Submit For Approval</h5>
                                    </div>
                                    <div class="card-block custom-scroll" style="height: 300px;">
                                        <div class="table-responsive">
                                            <?php if(count((array)$leavelist[ST_ACTIVE])!='0'){  ?>
                                                <table class="table table-hover m-b-0">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Date</th>
                                                            <th scope="col">Days</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $mk=1; foreach($leavelist[ST_ACTIVE] as $leavel1){ ?>
                                                            <tr >
                                                                <td class="p-2"><?php echo $mk; ?></td>
                                                                <td class="p-2"><a href="index.php?option=COM_INSERT_LEAVE_REQUEST&Rid=<?php  echo deocdeId("id:".$leavel1['id']); ?>"><span class="form-label label label-success p-2 cursor-pointer"><i class="ti-pencil-alt"></i> <?php echo $leavel1['leave_from']." To ".$leavel1['leave_to']; ?></span></td>
                                                                    <td class="text-right p-2"><span class="form-label label label-primary p-2"><?php echo number_format($leavel1['leave_days'],1);//((strtotime($leavel1['leave_to']) - strtotime($leavel1['leave_from']))  / (60 * 60 * 24))+1; ?> Day</span></td>
                                                                </tr>
                                                                <?php $mk++;
} ?>
                                                            </tbody>
                                                        </table>
                                                    <?php    }else{ ?>
                                                        <div class="card-px text-center py-20 m-t-30">
                                                            <p class="text-muted f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Leaves </p>
                                                            <div class="text-center px-4">
                                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                            </div>
                                                        </div>

                                                    <?php     } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4">
                                        <div class="card table-card">
                                            <div class="card-header">
                                                <h5>In-Process</h5>
                                            </div>
                                            <div class="card-block custom-scroll" style="height: 300px;">
                                                <div class="table-responsive">
                                                    <?php if(count((array)$leavelist['inpro'])!='0'){  ?>

                                                        <table class="table table-hover m-b-0">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col">Days</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $mk=1; foreach($leavelist['inpro'] as $leavel1){ ?>
                                                                    <tr >
                                                                        <td class="p-2"><?php echo $mk; ?></td>
                                                                        <td class="p-2"><a href="index.php?option=COM_INSERT_LEAVE_REQUEST&Rid=<?php  echo deocdeId("id:".$leavel1['id']); ?>" class="cursor_pointer"><span class="form-label label label-success p-2"><i class="ti-pencil-alt"></i> <?php echo $leavel1['leave_from']." To ".$leavel1['leave_to']; ?></span></td>
                                                                            <td class="text-right p-2"><span class="form-label label label-primary p-2"><?php echo number_format($leavel1['leave_days'],1);// ((strtotime($leavel1['leave_to']) - strtotime($leavel1['leave_from']))  / (60 * 60 * 24))+1; ?> Day</span></td>
                                                                        </tr>
                                                                        <?php $mk++;
} ?>
                                                                    </tbody>
                                                                </table>
                                                            <?php    }else{ ?>
                                                                <div class="card-px text-center py-20 m-t-30">
                                                                    <p class="text-muted f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Leaves </p>
                                                                    <div class="text-center px-4">
                                                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                                    </div>
                                                                </div>

                                                            <?php     } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-md-4">
                                                <div class="card table-card">
                                                    <div class="card-header">
                                                        <h5>Approved Leaves</h5>
                                                    </div>
                                                    <div class="card-block custom-scroll" style="height: 300px;">

                                                        <div class="table-responsive">
                                                            <?php if(count((array)$leavelist[ST_APPROVED])!='0'){  ?>
                                                                <table class="table table-hover m-b-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">Date</th>
                                                                            <th scope="col">Days</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php $mk=1; foreach($leavelist[ST_APPROVED] as $leavel1){ ?>
                                                                            <tr >
                                                                                <td class="p-2"><?php echo $mk; ?></td>
                                                                                <td class="p-2"><a href="index.php?option=COM_INSERT_LEAVE_REQUEST&Rid=<?php  echo deocdeId("id:".$leavel1['id']); ?>" class="cursor_pointer"><span class="form-label label label-success p-2"><i class="ti-pencil-alt"></i> <?php echo $leavel1['leave_from']." To ".$leavel1['leave_to']; ?></span></td>
                                                                                    <td class="text-right p-2"><span class="form-label label label-primary p-2"><?php echo number_format($leavel1['leave_days'],1); //echo ((strtotime($leavel1['leave_to']) - strtotime($leavel1['leave_from']))  / (60 * 60 * 24))+1; ?> Day</span></td>
                                                                                </tr>
                                                                                <?php $mk++;
} ?>
                                                                            </tbody>
                                                                        </table>
                                                                    <?php    }else{ ?>
                                                                        <div class="card-px text-center py-20 m-t-30">
                                                                            <p class="text-muted f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Leaves </p>
                                                                            <div class="text-center px-4">
                                                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                                            </div>
                                                                        </div>

                                                                    <?php     } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <?php

                                                    echo $todisplaythree = todisplaythree(array('table'=>'employee_leaves','originator'=>$_SESSION['userid'],'encode'=>'no','id'=>$Rid,'dis_query'=>'yes','dis_timeline'=>'yes','dis_comments'=>'yes'));
                                                    ?>

                                                </div>


                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
