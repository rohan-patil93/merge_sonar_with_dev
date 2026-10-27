<?php
$_SESSION['apply_js']['multiselest'] = 1;
$getrid = $_GET['Rid'];
if($getrid!=""){
    $rids =  encodeId($_GET['Rid']);
    $rid = urldecode($rids);
    $cand = explode(':', $rid);
    $rowid = $cand['1'];
}else{
    $employee_id = $_SESSION['emp_id'];
}
$table_name = 'employee_terminations';
$vurl = API."?mode=getterminatesettings&table=employee_terminations&employee_id=".$_SESSION['emp_id']."&Rid=".$rowid;
$terminations = callAPI($vurl);
$terminations = json_decode($terminations,1);
$emp_termination_data = $terminations['employee_terminations'];
$rowid = $emp_termination_data['id'];
$empres = $terminations['employees'];
$empassets = $terminations['emp_assets'];
$pstdata = $terminations['process_task_settings'];
$separation_mode = $terminations['separation_mode'];
$relieve_type = $terminations['termination_type'];
$emp_display =""; $emp_display_select=""; $manager_display ="readonly"; $manager_display_select = "disabled";
if($getrid !=""){
    $hr_user_id = $terminations['hr_user_id'];
    $finance_user_id = $terminations['finance_connect'];
    $manager_user_id = $terminations['report_to'];
    if($_SESSION['userid']!= $emp_termination_data['created_by']){
        $emp_display ="readonly";  $emp_display_select ="disabled";
    }
    if($_SESSION['userid']== $manager_user_id){
        $manager_display =""; $manager_display_select="";
    }
}else{
    $hr_user_id = $terminations['hr_user_id'];
    $finance_user_id = $terminations['finance_connect'];
    $manager_user_id = $terminations['report_to'];
}

$row ="";
if(isset($_POST['submitform'])){
    unset($_POST['submitform']);
    $_POST['notice_period_required'] = isset($_POST['notice_period_required']) ? '1' : '0';
    $_POST['exclude_final_settlement'] = isset($_POST['exclude_final_settlement']) ? '1' : '0';
    $_POST['releive_date'] = dateconvertdbformat($_POST['employee_leaving_date']);
    $_POST['effective_date'] = dateconvertdbformat($_POST['effective_date']);
    $_POST['employee_leaving_date'] = dateconvertdbformat($_POST['employee_leaving_date']);
    $_POST['emp_id'] = $_SESSION['emp_id'];
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['is_active'] = '0';
    $_POST['manager_user_id'] = $manager_user_id;
    $_POST['hr_user_id'] = $hr_user_id;
    $_POST['finance_user_id'] = $finance_user_id;
    if($rowid!=""){
        $row = "&Rid=".$getrid;
        $_POST['updated_date'] = nowDate();
        $_POST['updated_by'] = $_SESSION['userid'];
        $url = API."?mode=savetermination&rid=".$rowid."&method=post";
        $response = file_post_contents($url, $_POST, 'update');
    }else{
        $data['status_date'] = nowDate();
        $data['from_date'] = date("Y-m-d");
        $data['to_date'] = date("Y-m-d");
        $_POST['current_status'] ='0';
        $_POST['created_date'] = nowDate();
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API."?mode=savetermination&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        $response;
        $response = json_decode($response,1);
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
        }
        if($response['status'] == 'sucess'){
            header('Location:index.php?option=COM_LIST_EMPLOYEE_TERMINATIONS');
        }
        header('Location:index.php?option=COM_LIST_EMPLOYEE_TERMINATIONS');
    }
}elseif(isset($_POST['submitmanagerform'])){
    if($rowid!=""){
        $row = "&Rid=".$getrid;
        $_POST['manager_review_date'] = str_replace('/', '-', $_POST['manager_review_date']);
        $datt['manager_review_date'] = date('Y-m-d',strtotime($_POST['manager_review_date']));
        $datt['remark_by_manager'] = $_POST['remark_by_manager'];
        $_POST['releive_date'] = str_replace('/', '-', $_POST['releive_date']);
        $datt['releive_date'] =  date('Y-m-d', strtotime($_POST['releive_date']));
        $datt['updated_date'] = nowDate();
        $datt['updated_by'] = $_SESSION['userid'];
        $url = API."?mode=savetermination&rid=".$rowid."&method=post";
        $response = file_post_contents($url, $datt, 'update');
        $cemp_id = $emp_termination_data['emp_id'];
        $carray = array('date_of_leaving'=>$datt['releive_date']);
        $cdata = urlencode(json_encode($carray));
        $curl = API."?mode=updaterowtabledata&table=employees&rid=".$cemp_id."&data=".$cdata;
        $empres = callAPI($curl);
        header('Location:index.php?option='.$_GET['option'].$row);
    }
}elseif(isset($_POST['exitform'])){
    if($rowid!=""){
        $_POST['relieve_type'] = json_encode($_POST['relieve_type'],1);
        $_POST['settle_on'];
        $_POST['settle_on'] = str_replace('/', '-', $_POST['settle_on']);
        $_POST['settle_on'] =  date('Y-m-d', strtotime($_POST['settle_on']));
        $row = "&Rid=".$getrid ;
        unset($_POST['exitform']);
        unset($_POST['emp_id']);
        $_POST['updated_date'] = nowDate();
        $_POST['updated_by'] = $_SESSION['userid'];
        $url = API."?mode=savetermination&rid=".$rowid."&method=post";
        $response = file_post_contents($url, $_POST, 'update');
        header('Location:index.php?option='.$_GET['option'].$row);
    }
}
$stausresult = ViewStatusData();

?>
<script type="text/javascript">
    function getleavingdate(){
        var effective_date = document.getElementById('kt_datepicker_4_1').value;
        console.log("Submit Date = "+effective_date);
        var notice = 0;
        var numberofdays = 0;
        if(document.getElementById('notice_period_required').checked){
            notice = parseInt(document.getElementById('noticeperiod').value);
            console.log(notice);
            if(isNaN(notice)){
                notice = 0;
            }
            numberofdays = notice - parseInt(document.getElementById('shortfall').value);
        } else {
            numberofdays = parseInt(document.getElementById('shortfall').value);
        }
        $.ajax({
            url: "ajax.php",
            data:'page=getleavingdate&effective_date='+effective_date+"&numberofdays="+numberofdays,
            type: "POST",
            success: function(response) {
                console.log("response ="+response);
                document.getElementById('empleavingdate').value = response;
                document.getElementById('kt_datepicker_4_3').value = document.getElementById('empleavingdate').value;
            }
        });

    }

    function displaystausdrop(id,table,status_id){
  $.ajax({
      url: "ajax.php",
      data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
      type: "POST",
      success:function(data){
          console.log(data);
          $("#dropdownlist").html(data);
      },
      error:function (){}
  });

}

function statuspopup(status,id){
  var table_name = 'employee_terminations';
  jQuery.ajax({
      url: "views/custom_insert_status_popup.php",
      data:'table='+table_name+'&status='+status+'&rid='+id,
      type: "POST",
      success:function(data){
          $('#popup1').html(data);
          $('#kt_modal_KTDatatable_remote').modal("show");
      },
      error:function (){}
  });
}

function interquestion(emp_id){
    jQuery.ajax({
        url: "views/custom_employee_exit_inter_view_questions.php",
        data:'emp_id='+emp_id,
        type: "POST",
        success:function(data){
            $('#popup2').html(data);
            $('#kt_modal_KTDatatable_remote1').modal("show");
        },
        error:function (){}
    });
}

function startWorkflow(rid,transaction_table){
    Swal.fire({
        title: '<?php echo $_SESSION['error_message']['1011'];?>',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: 'Yes',
        denyButtonText: `No`,
    }).then((result) => {
        if (result.isConfirmed) {
            jQuery.ajax({
                url: "ajax.php",
                data:'page=startworkflow&transaction_table='+transaction_table+'&rid='+rid,
                type: "POST",
                success:function(data){
                    console.log(data);
                    var data1 = JSON.parse(data);
                    console.log(data1.error);
                    if(data1.error == "404"){
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: '<?php echo $_SESSION['error_message']['1012'];?>'
                        })
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001'];?>','');
                        location.reload();
                    }
                }
            });
        }
    })

}

function statuspopupnew(id,table_name,status,transaction_id,workflow_id,type){
     jQuery.ajax({
         url: "views/custom_insert_status_popup_new.php",
         data:'table='+table_name+'&status='+status+'&rid='+id+'&transaction_id='+transaction_id+'&workflow_id='+workflow_id+"&type="+type,
         type: "POST",
         success:function(data){
             $('#popup1').html(data);
             $('#kt_modal_KTDatatable_remote').modal("show");
         },
         error:function (){}
     });
 }
 function formsubmit(id){
     var separation_mode =  $("#kt_select2_41").val();
     var reason_for_leaving = $("#kt_select2_42").val();
     var submiteddate = $("#kt_datepicker_4_1").val();
     $(".Separationmode").text("");
     $(".reasonforleaving").text("");
     $(".submiteddateerrer").text("");
     if(separation_mode ==""){
         $(".Separationmode").text("Please Select Separation Mode");
         return false;
     }
     if(reason_for_leaving ==""){
         $(".reasonforleaving").text("Please Select Reason For Leaving");
         return false;
     }
     if(submiteddate ==""){
         $(".submiteddateerrer").text("Please Select Submited Date");
         return false;
     }
     $("#"+id).submit();
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
<?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
<div class="card">
    <?php
    if($emp_termination_data['current_status'] !=""){
        $workflowsteps = getWorkflowStatus($table_name, $rowid, $emp_termination_data['current_status']);
        echo $workflowsteps['current_status_ribbon'];
    }?>
    <div class="card-header table-card-header">
        <h5>Separation <?php  if($rowid!=""){ ?> <span class="form-label label label-primary"> <i class="fa fa-user"></i> <?php echo ucwords($empres['display_name']); ?></span> <?php
} ?></h5>
        <div class="card-header-right table-card-header ">
            <?php echo $workflowsteps['workflow_btn'];?>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i> Back</a></div>
            </div>
            <?php
            if($emp_termination_data['current_status'] !=""){
                echo $workflowsteps['breadcrumb'];
            }?>
            <div class="">
                <div class="card-body">
                    <div class="row col-lg-12">
                        <?php
                        if($emp_termination_data['current_status'] ==ST_DRAFT || $employee_leaves['current_status'] ==ST_ACTIVE || $emp_termination_data['current_status'] ==ST_SEND_BACK || $emp_termination_data['current_status'] ==ST_CALLBACK || $emp_termination_data['current_status']==""){
}else{
                            $disbalediv = 'style="pointer-events: none; opacity: 0.6;"';
                        }  ?>
                        <div class="col-lg-7 col-md-12 col-sm-12 col-12" <?php echo $disbalediv; ?>>
                            <form class="kt-form" id="employee_submited" method="post" action="index.php?option=COM_INSERT_EMPLOYEE_TERMINATIONS" >
                                <div class="form-group row">
                                    <span class="col-g-4 col-md-6 col-sm-6 col-12 col-form-label">Separation Mode<span class="text-danger">*</span></span>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                        <select aria-label="Select option" class="form-select js-example-basic-single" <?php echo $emp_display_select; ?> id="kt_select2_41"  name="termination_type" >
                                            <option value="">Select Any One</option>
                                            <?php foreach($separation_mode as $modekey=>$modevalue) { if($modekey!=""){ ?>
                                                <option value="<?php echo $modekey;?>" <?php echo $emp_termination_data['termination_type']==$modekey ? 'selected':'';?> ><?php echo $modevalue; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger Separationmode"></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <span class="col-g-4 col-md-6 col-sm-6 col-12 col-form-label">Submit On</span>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class=" date">
                                            <input aria-label="Input field" type="text" class="form-control datepicker"  onchange="getleavingdate()" <?php echo $emp_display; ?> name="effective_date" id="kt_datepicker_4_1" value="<?php echo fromDate($emp_termination_data['effective_date']);?>" />
                                            <span class="text-danger submiteddateerrer"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <span class="col-g-4 col-md-6 col-sm-6 col-12 col-form-label">Please select if notice period is required</span>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 col-form-label">
                                        <div class="checkbox-zoom zoom-primary mt-1">
                                            <label class="checkbox checkbox-outline checkbox-primary">
                                                <input aria-label="Checkbox" type="checkbox" <?php echo $emp_display_select; ?> id="notice_period_required" name="notice_period_required" onclick="getleavingdate()" <?php echo $emp_termination_data['notice_period_required']=='1'?'checked':'';?> onkeydown="" />    <span class="cr">
                                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <span></span>
                                                Notice Required
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <span class="col-g-4 col-md-6 col-sm-6 col-12 col-form-label">Reason For Leaving<span class="text-danger">*</span></span>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                        <select aria-label="Select option" class="form-select js-example-basic-single " <?php echo $emp_display_select; ?> id="kt_select2_42"  name="termination_reason" >
                                            <option value="">Select Any One</option>
                                            <?php foreach($separation_mode as $modekey=>$modevalue) { ?>
                                                <option value="<?php echo $modekey;?>" <?php echo $emp_termination_data['termination_reason']==$modekey ? 'selected':'';?> ><?php echo $modevalue; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger reasonforleaving"></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <span class="col-g-4 col-md-6 col-sm-6 col-12 col-form-label">Notice Period</span>
                                    <?php $terminations['notice_period'] = $terminations['notice_period']!=""? $terminations['notice_period']:NO_OF_NOTICE_DAYS; ?>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="input-group input-group-button ">
                                            <input aria-label="Input field" type="text" <?php echo $emp_display; ?> class="form-control form-control-sm width1" id="noticeperiod" onchange="getleavingdate()" aria-describedby="basic-addon2" name="noticeperiod_days" value="<?php echo $terminations['notice_period'];?>"  readonly>
                                            <span class="input-group-text addon-style">
                                                <span class="">Days</span>
                                            </span>
                                        </div>
                                    </div>                            </div>
                                    <div class="form-group row">
                                        <span class="col-g-4 col-md-6 col-sm-6 col-12 col-form-label">ShortFall in Notice Period</span>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="input-group input-group-button ">
                                                <input aria-label="Input field"  type="text" <?php echo $emp_display; ?> class="form-control form-control-sm width1" id="shortfall"  onchange="getleavingdate()" aria-describedby="basic-addon2" name="shortfalldays_in_noticeperiod" value="<?php echo $rowid!=''? $emp_termination_data["shortfalldays_in_noticeperiod"] : '0';?>" >
                                                <span class="input-group-text addon-style">
                                                    <span class="">Days</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <span class="col-g-4 col-md-6 col-sm-6 col-12 col-form-label">Tentative Leaving Date</span>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="date">
                                                <input aria-label="Empleavingdate" type="text"  id="empleavingdate" readonly class="form-control datepicker" value="<?php echo fromDate($emp_termination_data['employee_leaving_date']); ?>"  name="employee_leaving_date" />

                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <span class="col-g-4 col-md-6 col-sm-6 col-12 col-form-label">Please select if exclude from final settlement</span>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12 col-form-label">
                                            <div class="checkbox-zoom zoom-primary mt-1">
                                                <label class="checkbox checkbox-outline checkbox-primary">
                                                    <input aria-label="Checkbox" type="checkbox" <?php echo $emp_display_select; ?>  name="exclude_final_settlement" <?php echo $emp_termination_data['exclude_final_settlement']=='1'?'checked':'';?>/>
                                                    <span class="cr">
                                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                    </span>
                                                    <span></span>
                                                    Exclude from Final Settlement
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <span class="col-g-4 col-md-6 col-sm-6 col-12 col-form-label">Remarks</span>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                            <textarea aria-label="Text area" type="text" <?php echo $emp_display; ?> class="form-control" name="remarks_by_employee" ><?php echo $emp_termination_data['remarks_by_employee'];?></textarea>
                                        </div>
                                    </div>
                                    <?php $disable = "";
                                    if($_SESSION['user_id'] == $emp_termination_data['created_by'] || $rowid =="" || $emp_termination_data['current_status']==ST_ACTIVE || $emp_termination_data['current_status']==ST_SEND_BACK){
}else{ $disable = "disabled";
}

                                    ?>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <?php if($hr_user_id=="0" || $finance_user_id=="0" || $manager_user_id=="0"){ $disable ="disabled"; ?>
                                                <span class="text-danger f-12"><?php echo $_SESSION['error_message']['9001']; ?></span></br>
                                            <?php } ?>
                                            <input type="hidden" name="submitform" value="exit">
                                            <button type="button" onclick="formsubmit('employee_submited')" id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  <?php echo $disable; ?>  name="submitform" value="exit" ><i class="la la-check"></i>Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <?php if($_SESSION['userid']==$manager_user_id || $_SESSION['userid']!=$hr_user_id) { ?>

<?php
    $disbalediv = ""; if($emp_termination_data['current_status'] ==ST_APPROVED1 || $employee_leaves['current_status'] ==ST_APPROVED2 || $emp_termination_data['current_status'] ==ST_APPROVED3){
}else{
        $disbalediv = 'style="pointer-events: none; opacity: 0.6;"';
    }  ?>

                            <div class="col-lg-5 float-right" style="display: none;" >
                                <form class="kt-form" id="kt_form" method="post" action="" >
                                    <div class="card">
                                        <div class="card-header table-card-header">
                                            <h5>Manager Review </h5>
                                        </div>
                                        <div class="card-body" <?php echo $disbalediv; ?>>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <span class="form-label">Manager Review Date</span>
                                                    <input aria-label="Input field" type="text" class="form-control datepicker" <?php echo $manager_display; ?>  value="<?php echo fromDate($emp_termination_data['manager_review_date']);?>" name="manager_review_date" />
                                                </div>
                                                <div class="form-group">
                                                    <span class="form-label">Leaving Date</span>
                                                    <input aria-label="Input field" type="text" class="form-control datepicker"  value="<?php echo fromDate($emp_termination_data['releive_date']); ?>"  name="releive_date" />
                                                </div>
                                                <div class="form-group row">
                                                    <span class="col-12 form-label">Notes</span>
                                                    <div class="col-12">
                                                        <textarea aria-label="Text area" type="text" class="form-control" <?php echo $manager_display; ?> name="remark_by_manager" ><?php echo $emp_termination_data['remark_by_manager'];?></textarea>
                                                    </div>
                                                </div>
                                                <?php if(count((array)$empassets)>0){ ?>
                                                    <div class="form-group row">
                                                        <span class="col-4 col-form-label font-weight-bolder h6">Assets</span>
                                                        <div class="col-7">
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">
                                                            <tr>
                                                                <th scope="col">SL No.</th>
                                                                <th scope="col">Assets </th>
                                                                <th scope="col">Status</th>
                                                            </tr>
                                                        <?php
                                                        $assetstatus = array('0'=>'Allocated','1'=>'Accepted','2'=>'Rejected','3'=>'Return');
                                                        $k=1; foreach($empassets as $empasset){ ?>
                                                            <tr>
                                                                <td><?php echo $k; ?></td>
                                                                <td><?php echo $empasset['asset_description']; ?> </td>
                                                                <td><?php echo $assetstatus[$empasset['current_status']]; ?></td>
                                                            </tr>
                                                            <?php $k++;
} ?>
                                                        </table>
                                                    </div>
                                                    <?php } ?>
                                                    <?php if(count((array)$pstdata)>0){ ?>
                                                        <div class="form-group row">
                                                        <span class="col-4 col-form-label font-weight-bolder h6">Process Tasks</span>
                                                        <div class="col-7">
                                                        </div>
                                                    </div>
                                                        <div class="">
                                                            <?php foreach($pstdata as $pst){ ?>
                                                                <div class="checkbox-inline col-lg-10">
                                                                    <label class="checkbox checkbox-outline checkbox-primary">
                                                                        <input aria-label="Checkbox" type="checkbox" disabled name=""  value="<?php echo $term;?>" />
                                                                        <span></span>
                                                                        <?php echo $pst['task_name']; ?>
                                                                    </label>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"
                                                            <?php echo  $manager_display_select; ?>  name="submitmanagerform" value="exit"  <?php echo $emp_termination_data['current_status'] !=ST_APPROVED1?"disabled":""; ?> ><i class="la la-check"></i>Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>


                                <?php } ?>
                                <?php if($_SESSION['userid']!=$hr_user_id) { ?>

                                        <div class="card-header table-card-header">
                                            <h5>Exit Interview </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <span class="col-lg-4 col-form-label">Exit Interview </span>
                                                <div class="col-lg-7">
                                                    <button class="btn btn-warning btn-shadow-hover font-weight-bold mr-2" type="button" onclick="interquestion(<?php echo $emp_termination_data['emp_id']; ?>)">Questions</button>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                            <span class="col-4 col-form-label">Remarks</span>
                                            <div class="col-7">
                                                <textarea aria-label="Remarks By Admin" type="text" class="form-control" name="remarks_by_admin" ><?php echo $emp_termination_data['remarks_by_admin'];?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <span class="col-4 col-form-label">Settle on</span>
                                            <div class="col-7">
                                                <div class=" date">
                                                    <input aria-label="Input field" type="text" class="form-control datepicker"  value="<?php echo dateFormate($emp_termination_data['settle_on']);?>"  name="settle_on" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ========= -->
                                        <div class="form-group row">
                                            <span class="col-4 col-form-label"></span>
                                            <div class="col-8 col-form-label">
                                                <?php foreach($relieve_type as $term=>$termvalue) { ?>

                                                    <div class="checkbox-fade fade-in-primary">
                                                        <label class="form-label">
                                                            <input aria-label="Relieve Type[]" type="checkbox" name="relieve_type[]" <?php echo in_array($term,(array)json_decode($emp_termination_data['relieve_type'],1)) ? 'checked':'';?> value="<?php echo $term;?>" />
                                                            <span class="cr">
                                                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                            </span>
                                                            <span> <?php echo $termvalue; ?></span>
                                                        </label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <input type="hidden" name="emp_id" value="<?php echo $emp_termination_data['emp_id']; ?>">
                                                <div class="col-lg-12">
                                                    <button id="kt_login_signin_submit_3" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="exitform" value="exit" <?php echo $emp_termination_data['settle_on']!=""?"disabled":""; ?> ><i class="la la-check"></i>Save</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ========= -->
                                        </div>

                                <?php } ?>
                            </div>
                        </div>

                        </div>
                    </div>
                </div>
                <?php echo $todisplaythree = todisplaythree(array('table'=>$table_name,'originator'=>$emp_termination_data['created_by'],'id'=>$_GET['Rid'],'dis_query'=>'yes','dis_timeline'=>'yes','dis_comments'=>'yes'));      ?>

            </div>
        </div>
    </div>
</div>
</div>

<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1">

        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
