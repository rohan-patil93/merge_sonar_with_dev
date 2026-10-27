<?php
$_SESSION['apply_js']['multiselest'] = "1";
$from_cur_year = date('Y');
$from_cur_month = date('m');
$table_name = 'project_timesheets';
if(isset($_GET['CHANGE_DATE'])) {
    $from_cur_year = date('Y', strtotime($_GET['CHANGE_DATE']."-01"));
    $from_cur_month = date('m', strtotime($_GET['CHANGE_DATE']."-01"));
}
if(isset($_POST['applyform'])) {

    $from_cur_year = date('Y', strtotime($_POST['from_date']."-01"));
    $from_cur_month = date('m', strtotime($_POST['from_date']."-01"));
}
$emp_id =$_SESSION['emp_id'];
if(isset($_GET['EMPID']) &&  $_GET['EMPID'] != "" ){
    $emp_id = $_GET['EMPID'];
}
$url = API."?mode=getmonthlytimesheetentries&year=".$from_cur_year."&month=".$from_cur_month."&empid=".$emp_id;
$configesponse = callAPI($url);
$data = json_decode($configesponse,1);
$work_array = array('P'=>array('name'=>'Present', 'code'=>'P', 'color'=>'#5ed596'), 'A'=>array('name'=>'Absent', 'code'=>'A', 'color'=>'#f07167'), 'H'=>array('name'=>'Holiday', 'code'=>'H', 'color'=>'#d5bdaf'),'Z'=>array('name'=>'Half Day', 'code'=>'Z', 'color'=>'#e76f51'), 'L'=>array('name'=>'Leave', 'code'=>'L', 'color'=>'#ffafcc'), 'O'=>array('name'=>'Week Off', 'code'=>'O', 'color'=>'#eeeeee')  );
$workdays = $data['attendance']['working_days'];
$workdays = json_decode($workdays,1);
foreach($workdays as $workday){
    $weekdays[]=strtolower($workday);
}
$statusprogress = gettableStatusArray('project_timesheets');
$sprogress = $statusprogress['full'];
$listactive = array(); $aboueactive = array();
foreach($data['timesheet'] as $listtime){
    if($listtime['current_status']==0){
        $listactive[]= $listtime;
    }else{
        $aboveactive[]= $listtime;
    }
}

$cur_status = 10;
if($_SESSION['user']['approval_manager']!= $_SESSION['user']['reports_to']){
    $cur_status = 8;
}
?>

<style type="text/css">
.week_table tr th {
    position: sticky; top: 0;  color: #FFFFFF; border-width: 3px; border-color: #333333; border-bottom: 3px solid #ffffff;}
.week_table tr td{height: 40px; border-width: 3px; border-color: #333333;  padding: 10px;}
.week_table tr td:last-child{ border-top: none;}
.week_table tr td.week_p{background: #2a9d8f6b;}
.week_table tr td.week_a{background: #d628286e;}
.week_table tr td.week_h{background: #d5bdaf;}
.week_table tr td.week_z{background: #e76f51a1;}
.week_table tr td.week_l{background: #e9c46a7a;}
.week_table tr td.week_o{background: #eeeeee;}

.badge-hover{box-shadow: none; padding: 0; width: 25px; height: 20px; border-radius: 3px; line-height: 20px;}
.badge-hover:hover{box-shadow: none;}

.badge-sec{display: flex; flex-wrap: wrap;}
.badge-sec span{-webkit-box-flex: 1; -ms-flex: 1; flex: 1;}

</style>
<script type="text/javascript">
    function statuspopupActiveStatusOnly(status,id){
      var table_name = 'project_timesheets';
      jQuery.ajax({
       url: "views/custom_insert_status_project_timesheets_popup.php",
       data:'table='+table_name+'&status='+status+'&rid='+id,
       type: "POST",
       success:function(data){
        $('#popup1').html(data);
        $('#kt_modal_KTDatatable_remote').modal("show");
      },
      error:function (){}
    });
    }
    function submitProjectWorkflowPopUp(status, month, year){
        var table_name = 'project_timesheets';
        jQuery.ajax({
           url: "views/custom_insert_month_projecttimesheet_status_popup.php",
           data:'table='+table_name+'&status='+status+'&month='+month+'&year='+year,
           type: "POST",
           success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
          },
          error:function (){}
        });
      }

    function attendanceupload(type, date, num, detail){
        console.log("type:"+type+"/date:"+date+"/num:"+num);
        $.ajax({
            url: "ajax.php",
            data:'page=projecttimesheetmontly&date='+date+'&emp_id='+$("#emp_id").val()+'&type='+type+'&detail='+detail+'&work_order='+$("#getwork_order").val(),
            type: "POST",
            success:function(data){
                console.log(data);
                var obj = $.parseJSON(data);
                console.log(obj.weekoff_days);

                $("#td_"+num).removeClass();
                $("#td_"+num).addClass("week_"+type);
                $("#pp_val").html(obj.present_days);
                $("#ab_val").html(obj.absent_day);
                var total_work_days = obj.work_days;
                var total_fill_days = obj.leave_days+obj.present_days+obj.absent_day;
                if(total_work_days == total_fill_days){
                    $("#status_id").show();
                }else{
                    $("#status_id").hide();
                }
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','Project timesheet is submitted successfully','');
            },
            error:function (){}
        });
    }

    function changeselectdate(){
      var selected_date =  $("#selected_datess").val();
      var emp_id =  $("#emp_id").val();
      var getwork_order = $("#getwork_order").val();
      var option = $("#optionvalue").val();
      var error = '0';
      $(".errordate").text("");
      $(".errorworkorder").text("");
      if(getwork_order == '0'){
        error = 1;
        $(".errorworkorder").text("Please Select Work Order");
      }
      if(selected_date == ''){
        error = 1;
        $(".errordate").text("Please Select Date");
      }
      if(error == "0" || $("#getwork_order option").length == '1'){
       window.location = "index.php?option="+option+"&EMP="+emp_id+"&CHANGE_DATE="+selected_date+"&WID="+getwork_order;
      }
    }
    function alradysubmitedmanager(){

           tostarnotify('top','right','fa fa-thumbs-up','form-label label label-danger  pt-2 pb-2','animated flipInX','animated flipOutX','Project timesheet already submitted to manage','');
    }
</script>

<div class="card">
    <div class="card-header table-card-header">
        <h5>Monthly Timesheet</h5>
        <span class="form-label badge bg-primary p-2 m-l-5"><i class='ti-user'></i> <?php echo $_SESSION['users'][$_SESSION['userid']]; ?> </span></h5>
        <div class="card-header-right table-card-header ">

           <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
          </a>
      </div>
    </div>
    <?php echo $dis_status=todisplaystatuslist(array('table'=>$table_name,'grand_total'=>'0','current_status'=>$projectdetails['current_status'])); ?>
    <div class="card-body">
      <div class="p-5 pt-3">
        <form class="kt-form" id="kt_form" method="post" action="" >

            <div class="row mt-2">
                <div class="col-lg-1 text-end">
                    <span class="form-label mt-2 f-w-600">Date</span>
                </div>
                <div class="col-lg-3">
                    <div class="input-group input-group-button">
                        <input aria-label="From Date" type="month" name="from_date" onchange="changeselectdate()" value="<?php echo $from_cur_year."-".$from_cur_month; ?>"  id="selected_datess"  class="form-control form-control-sm width1" style="font-size: 14px;height: 40px !important;padding: 10px 15px;">
                    </div>
                </div>
                <div class="col-lg-1 text-end">
                    <span class="form-label mt-2 f-w-600">Work Order</span>
                </div>
                <div class="col-lg-3">
                    <select aria-label="Work Order" class="form-select js-example-basic-single" name="work_order" id="getwork_order" onchange="changeselectdate()">

                        <?php if(count((array)$data['workorder'])==0){ ?>
                            <option value="0">Select Any one</option>
                        <?php } ?>

                        <?php foreach((array)$data['workorder']  as $workorder){ ?>
                            <option value="<?php echo $workorder['id']; ?>"><?php echo $workorder['transaction_number']; ?></option>
                        <?php } ?>
                    </select>
                </div>


                <div class="col-lg-4">
                    <div class="card-header-right table-card-header float-right">
                        <div class="btn-group workflow-btn" id="status_id" <?php if(count((array)$listactive)==0 || ($data['all_data']['work_days'] != ($data['all_data']['present_days']+$data['all_data']['absent_day']+$data['all_data']['leave_days']))){ ?> style="display:none;" <?php
} ?>  >

                            <button type="button" class="btn btn-info btn-md waves-effect waves-light" onclick="submitProjectWorkflowPopUp(<?php echo $cur_status; ?>,<?php echo $from_cur_month; ?>,<?php echo $from_cur_year; ?>)">
                                <span class="fa fa-thumbs-up text-white"></span> &nbsp; Submit For Approval
                            </button>
                        </div>
                        <div class="btn-group workflow-btn" id="status_procee_id" <?php if((count((array)$aboveactive)>0 && (count((array)$listactive)==0 || ($data['all_data']['work_days'] != ($data['all_data']['present_days']+$data['all_data']['absent_day']+$data['all_data']['leave_days']))))){ ?> style="" <?php
}else{ ?> style="display:none;"  <?php
} ?>  >

                            <button type="button" class="btn btn-info btn-md waves-effect waves-light" style="pointer-events: none;">
                                <span class="fa fa-thumbs-up text-white"></span> &nbsp; APPROVAL PENDING
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
        <div class="row mt-3" id="tableview" >
            <div class="col-lg-8 mt-3 badge-sec">
                <?php foreach($work_array as $work){ ?>

                    <span type="button" class="btn btn-white waves-effect waves-light f-13"><?php echo $work['name']; ?>
                        <span class="badge" style="background-color:<?php echo $work['color']; ?>;"><?php echo $work['code']; ?></span>
                    </span>
                <?php } ?>
            </div>
            <div class="col-lg-4 mt-3">

            </div>
        </div>

        <div class="row">
            <?php if(count((array)$data['workorder'])>0){ ?>
            <div class="col-lg-8">
                <div class="table-responsive mt-3 week_table">
                    <table class="table table-bordered">
                        <tr style="background-color: #1b1b1b;">
                            <?php for($i=0; $i<7; $i++){
                                $dayNumber = $i;
                            ?>
                                <th scope="col" class="text-center p-2" style="width: 12%;"><?php echo date('l', strtotime("Sunday +{$dayNumber
} days")); ?></th>
                            <?php  } ?>

                        </tr>
                        <?php
                        $sdate = $from_cur_year."-".$from_cur_month."-01";
                        $t = date('t',strtotime($sdate));
                        $wk = 0;
                        $hlidays = 0; $leaves=0; $pres=0; $asent = 0; $weekoff = 0;
                        $total =0;
                        for ($k=0; $k <$t ; $k++) {
                            $total = $total+1;
                            $ccww = ""; $ccedit=0; $viewstatus =0;
                            $predate = date("Y-m-d", strtotime($sdate . " +$k days"));
                            $num = date("j", strtotime($predate));
                            $day = strtolower(date("l", strtotime($predate)));
                            if( in_array($predate,(array)$data['listholidays'])){
                                $ccww = "h";
                                $hlidays = $hlidays+1;
                            }
                            if( in_array($predate,(array)$data['leave'])){
                                    $leaves = $leaves+1;
                                     $ccww = "l";
                            }
                            $checkcurrent_status = '0';
                            if( in_array($predate,(array)$data['timesheet_date'])){
                                $ccedit = 1;
                                $ccww = "a";
                                $view_data = "Absent";
                                if($data['timesheet'][$predate]['billable_hrs']==8){
                                    $ccww = "p";
                                    $view_data = "Present";
                                    $pres= $pres+1;
                                }
                                if($data['timesheet'][$predate]['billable_hrs']==4){
                                    $ccww = "z";
                                    $view_data = "Half Day";
                                    $pres= $pres+0.5;
                                }


                                if(isset($data['timesheet'][$predate]) && $data['timesheet'][$predate]['current_status']>0){
                                    $checkcurrent_status = '1';
                                }

                            }
                            if(!in_array($day,(array)$weekdays)){
                                $weekoff = $weekoff+1;
                                $ccww = "o";
                            }
                            if($wk==0){ echo "<tr>";
}
                            ?>
                            <?php
                                if(strtotime($sdate) == strtotime($predate) && date('N',strtotime($sdate))!=0){
                                    for ($m=0; $m < date('w',strtotime($sdate)) ; $m++) {
                                        echo "<td></td>";
                                        $wk++;
                                    }
                                }
                            ?>
                                <td id="td_<?php echo $num; ?>" class="week_<?php echo $ccww; ?>" <?php if(strtotime($predate) > strtotime(date('Y-m-d'))){ echo "style='pointer-events: none;opacity: 0.4;'";
} ?>>
                                    <div class="float-end" >
                                        <?php echo $num; ?>
                                    </div>
                                    <?php if( ($ccww=="" || ($ccedit==1 && $ccww !="" && $viewstatus !=1 ))&& $ccww!='o' ){ ?>
                                        <div class="d-block">
                                            <span class="form-label badge badge-md cursor-pointer badge-hover" style="background-color:#2a9d8f;" <?php if($checkcurrent_status !=1){ ?> onclick="attendanceupload('p','<?php echo $predate; ?>',<?php echo $num; ?>,'Present')" <?php
}else{ ?> onclick="alradysubmitedmanager()" <?php
} ?>  role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">P</span>
                                            <span class="form-label badge badge-md cursor-pointer badge-hover" style="background-color:#d62828" <?php if($checkcurrent_status !=1){ ?>  onclick="attendanceupload('a','<?php echo $predate; ?>',<?php echo $num; ?>,'Absent')"<?php
}else{ ?> onclick="alradysubmitedmanager()" <?php
} ?> role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">A</span>
                                        </div>
                                        <div class="d-block">
                                            <span class="form-label badge badge-md cursor-pointer badge-hover mb-0" style="background-color:#e76f51" <?php if($checkcurrent_status !=1){ ?>  onclick="attendanceupload('z','<?php echo $predate; ?>',<?php echo $num; ?>,'Half Day')"<?php
}else{ ?> onclick="alradysubmitedmanager()" <?php
} ?> role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Z</span>
                                            <span class="form-label badge badge-md  badge-hover mb-0" style="background-color:#e9c46a">L</span>
                                        </div>
                                    <?php } ?>
                                    <?php if($ccww !="" && $viewstatus ==1){ ?>
                                        <span class="form-label badge f-12" style="color:#FFFFFF";> <?php echo $view_data; ?></span>
                                    <?php } ?>
                                </td>

                                <?php if(($k+1) == $t){
                                    for($n=$wk;$n<6; $n++ ){ ?>
                                        <td></td>
                                    <?php $wk++;
}

                                }?>

                                <?php  if($wk==6){ ?>

                        </tr>
                        <?php }
                        $wk++;
                        if($wk==7){ $wk = 0;
}
                    } ?>
                </table>

              </div>
            </div>
            <div class="col-lg-4">
                    <div class="card">
                        <div class="card-block button-list text-center">

                            <button type="button" class="btn btn-primary waves-effect waves-light mb-0 p-2" data-bs-toggle="tooltip" data-placement="top" title="Total Days" style="margin-right:5px;">Total
                              <span class="badge d-block m-0 mt-2"><?php echo  $data['all_data']['work_days']; ?></span>
                            </button>
                            <button type="button" class="btn btn-success text-white waves-effect waves-light mb-0 p-2" data-bs-toggle="tooltip" data-placement="top" title="Present" style="margin-right:5px;background:#2a9d8f;">Present
                              <span class="badge d-block m-0 mt-2" id="pp_val" ><?php echo  $data['all_data']['present_days']; ?></span>
                            </button>
                            <button type="button" class="btn btn-danger text-white waves-effect waves-light mb-0 p-2" data-bs-toggle="tooltip" data-placement="top" title="Absent" style="margin-right:5px;background:#d62828;">Absent
                              <span class="badge d-block m-0 mt-2" id="ab_val"><?php echo  $data['all_data']['absent_day']; ?></span>
                            </button>
                            <button type="button" class="btn btn-warning text-white waves-effect waves-light mb-0 p-2" data-bs-toggle="tooltip" data-placement="top" title="Leaves" style="margin-right:5px;background:#e9c46a;">Leaves
                              <span class="badge d-block m-0 mt-2"><?php echo  $data['all_data']['leave_days']; ?></span>
                            </button>

                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>With Me</h5>
                        </div>
                        <div class="card-body custom-scroll text-center" style="max-height:200px;">

                            <?php
                               $avbl = 0; if(count((array)$data['timesheet'])>0){
                            foreach((array)$data['timesheet'] as $submited){
                                if($submited['current_status']<10){ $avbl++;
                            ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-lg-2 p-1">
                                    <button data-bs-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;<?php echo $sprogress[$submited['current_status']]['name']; ?>&nbsp;&nbsp;" class="waves-effect waves-light btn-primary p-l-15 p-r-15 p-t-10 p-b-10 b-radius-5 border-0" style="background-color: #2ed8b6;"><i class="ti-calendar f-w-600"></i></button>
                                </div>
                                <div class="col-lg-6 p-1">
                                    <span class="form-label badge badge-md cursor-pointer text-dark" style="background-color:<?php echo $_SESSION['status'][$submited['current_status']]['background_color']; ?>;" ><?php echo $sprogress[$submited['current_status']]['name']; ?></span>
                                    <h6 class="f-w-600"><?php echo fromDate($submited['timesheet_date']); ?></h6>

                                </div>
                                <div class="col-lg-4 p-1">
                                    <span class="form-label label label-success">Present</span>
                                </div>
                            </div>
                            <?php }
}
} if($avbl == 0){ ?>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 100px!important;">
                            <?php } ?>

                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>With Manager</h5>
                        </div>
                        <div class="card-body custom-scroll text-center" style="max-height:175px;">

                            <?php
                            $avbl = 0;
                                if(count((array)$data['timesheet'])>0){
                            foreach((array)$data['timesheet'] as $submited){
                                if($submited['current_status']>=10 && $submited['current_status'] <199){ $avbl++;
                            ?>
                            <div class="row mt-3 pb-3 " style="border-bottom:1px dashed #cccccc;">
                                <div class="col-lg-2 p-1">
                                    <button data-bs-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;<?php echo $sprogress[$submited['current_status']]['name']; ?>&nbsp;&nbsp;" class="waves-effect waves-light btn-primary p-l-15 p-r-15 p-t-10 p-b-10 b-radius-5 border-0" style="background-color: <?php echo "#".$_SESSION['status'][$submited['current_status']]['background_color']; ?>"><i class="ti-calendar f-w-600"></i></button>
                                </div>
                                <div class="col-lg-6 p-1">

                                      <span class="form-label badge badge-md cursor-pointer text-dark" style="background-color:<?php echo $_SESSION['status'][$submited['current_status']]['background_color']; ?>;" ><?php echo $sprogress[$submited['current_status']]['name']; ?></span>
                                      <h6 class="f-w-600"><?php echo fromDate($submited['timesheet_date']); ?></h6>

                                </div>
                                <div class="col-lg-3 p-1">

                                </div>
                            </div>
                            <?php }
}
}
                            if($avbl == 0){ ?>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 100px!important;">
                            <?php } ?>

                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Recently Approved</h5>
                        </div>
                        <div class="card-body custom-scroll text-center" style="max-height:175px;">

                            <?php
                            $avbl = 0;
                                if(count((array)$data['timesheet'])>0){
                            foreach((array)$data['timesheet'] as $submited){
                                if( $submited['current_status'] ==199){ $avbl++;
                            ?>
                            <div class="row mt-3 pb-3 " style="border-bottom:1px dashed #cccccc;">
                                <div class="col-lg-2 p-1">
                                    <button data-bs-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;<?php echo $sprogress[$submited['current_status']]['name']; ?>&nbsp;&nbsp;" class="waves-effect waves-light btn-primary p-l-15 p-r-15 p-t-10 p-b-10 b-radius-5 border-0" style="background-color: #2ed8b6;"><i class="ti-calendar f-w-600"></i></button>
                                </div>
                                <div class="col-lg-6 p-1">

                                      <span class="form-label badge badge-md cursor-pointer text-dark" style="background-color:<?php echo $_SESSION['status'][$submited['current_status']]['background_color']; ?>;" ><?php echo $sprogress[$submited['current_status']]['name']; ?></span>
                                      <h6 class="f-w-600"><?php echo fromDate($submited['timesheet_date']); ?></h6>

                                </div>
                                <div class="col-lg-3 p-1">
                                    <span class="form-label label label-success">Present</span>
                                </div>
                            </div>
                            <?php }
}
}
                            if($avbl == 0){ ?>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 100px!important;">
                            <?php } ?>

                        </div>
                    </div>
            </div>
            <?php }else{?>
                <div>
                    <div class="text-center text-danger f-26 m-t-30">
                        Employee work order is not available. Please check with admin email
                        <?php echo $_SESSION['config']['admin_email']; ?>
                    </div>
                </div>
            <?php } ?>
             <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $emp_id; ?>">
                <input type="hidden" name="optionvalue" id="optionvalue" value="COM_VIEW_MONTHLY_WORK_ATTENDANCES">
        </div>
    </div>
</div>
</div>
</div>