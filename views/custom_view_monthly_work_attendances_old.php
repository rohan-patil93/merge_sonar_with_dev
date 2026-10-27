<?php
$_SESSION['apply_js']['multiselest'] = "1";
$from_cur_year = date('Y');
$from_cur_month = date('m');
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
//echo $url;
$configesponse = callAPI($url);
$data = json_decode($configesponse,1);
$work_array = array('P'=>array('name'=>'Present', 'code'=>'P', 'color'=>'#5ed596'), 'A'=>array('name'=>'Absent', 'code'=>'A', 'color'=>'#f07167'), 'H'=>array('name'=>'Holiday', 'code'=>'H', 'color'=>'#d5bdaf'),'Z'=>array('name'=>'Half Day', 'code'=>'Z', 'color'=>'#b2b2b2'), 'L'=>array('name'=>'Leave', 'code'=>'L', 'color'=>'#ffafcc'), 'O'=>array('name'=>'Week Off', 'code'=>'O', 'color'=>'#eeeeee')  );
$workdays = $data['attendance']['working_days']; 
$workdays = json_decode($workdays,1);
foreach($workdays as $workday){
    $weekdays[]=strtolower($workday);
}
//echo "-----";print_r($weekdays);
$statusprogress = gettableStatusArray('project_timesheets');
$sprogress = $statusprogress['full'];
//print_r($sprogress);
?>


<style type="text/css">
.week_table tr th { 
    position: sticky; top: 0; /*background: #4099ff;*/ color: #FFFFFF; border-width: 3px; border-color: #333333; border-bottom: 3px solid #ffffff;}
.week_table tr td{height: 40px; border-width: 3px; border-color: #333333; /*box-shadow: 0px 10px 10px 10px #525252;*/ padding: 10px;}
.week_table tr td:last-child{border-right: none; border-bottom: none; border-top: none;}
.week_table tr td.week_p{background: #5ed596;}
.week_table tr td.week_a{background: #f07167;}
.week_table tr td.week_h{background: #d5bdaf;}
.week_table tr td.week_z{background: #b2b2b2;}
.week_table tr td.week_l{background: #ffafcc;}
.week_table tr td.week_o{background: #eeeeee;}

.badge-hover{box-shadow: 0 2px 7px #555555;}
.badge-hover:hover{box-shadow: none;}

.badge-sec{display: flex; flex-wrap: wrap;}
.badge-sec span{-webkit-box-flex: 1; -ms-flex: 1; flex: 1;}

</style>
<script type="text/javascript">
    function statuspopupActiveStatusOnly(status,id){
      //alert("data");
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
    function statuspopup(status,id){
        var table_name = 'project_timesheets';
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
    function displaystausdrop(id,table,status_id, date){
       //alert("id");
        $.ajax({
           url: "ajax.php",
          data:'page=current_status_drop_project_timesheets&id='+id+'&current_status='+status_id+'&table='+table+'&user_id='+$("#check_user_id").val(),        
          type: "POST",
           success:function(data){
            console.log(data);
            $("#dropdownlist_"+date).html(data);
          },
          error:function (){}
        });
    }
    function attendanceupload(type, date, num, detail){
        console.log("---------------");
        console.log("type:"+type+"/date:"+date+"/num:"+num);
        $.ajax({
            url: "ajax.php",
            data:'page=projecttimesheetmontly&date='+date+'&emp_id='+$("#emp_id").val()+'&type='+type+'&detail='+detail+'&work_order='+$("#getwork_order").val(),
            type: "POST",
            success:function(data){
                console.log(data);
                swal({
                    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                    text: 'This date ('+date+') employee '+detail,
                    type: "success",
                    confirmButtonText: "OK",                    
                    closeOnConfirm: true
                });
                $("#td_"+num).addClass("week_"+type);
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
</script>

<div class="card">
    <div class="card-header table-card-header">
        <h5>Timesheet</h5>
        <div class="card-header-right table-card-header ">

           <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
          </a>
      </div>
    </div>
    <div class="card-body">
      <div class="p-5 pt-3">
        <form class="kt-form" id="kt_form" method="post" action="" >
            <!-- <div class="col-lg-12 row">
                <div class="col-lg-3">
                    Date
                 </div>
                 <div class="col-lg-3">
                    Work Order
                 </div>
            </div> -->
            <div class="row mt-2">
                <label class="form-label col-lg-1 text-end">Date</label>
                <div class="col-lg-3">
                    <div class="input-group input-group-button"> 
                        <input type="month" name="from_date"onchange="changeselectdate()" value="<?php echo $from_cur_year."-".$from_cur_month?>"  id="selected_datess"  class="form-control form-control-sm width1" style="font-size: 14px;height: 40px !important;padding: 10px 15px;">  
                    </div>
                </div>
                <label class="form-label col-lg-1 text-end">Work Order</label>
                <div class="col-lg-3">
                    <select class="form-select js-example-basic-single" name="work_order" id="getwork_order" onchange="changeselectdate()"> 
                        <!-- <option value="0">Select Any one</option>  -->
                        <?php foreach($data['workorder']  as $workorder){ ?> 
                            <option value="<?php echo $workorder['id']; ?>"><?php echo $workorder['transaction_number']; ?></option>
                        <?php } ?>          
                    </select>
                </div>
                <!-- <div class="col-lg-2">
                    <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                       Submit
                    </button> 
                </div> -->
            </div>
        </form>
        <div class="row mt-3" id="tableview" >
            <div class="col-lg-8 mt-3 badge-sec">
                <?php foreach($work_array as $work){ ?>
                    <!-- <button class="btn waves-effect waves-light text-white mt-2" style="background-color:<?php // echo $work['color']; ?>;"><?php echo $work['code']; ?></button>
                     <span class="m-r-15"><?php // echo $work['name']; ?></span> -->
                    <span type="button" class="btn btn-white waves-effect waves-light f-13"><?php echo $work['name']; ?>
                        <span class="badge" style="background-color:<?php echo $work['color']; ?>;"><?php echo $work['code']; ?></span>
                    </span>
                <?php } ?>
            </div>
            <div class="col-lg-4 mt-3">
                
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="table-responsive mt-3 week_table">
                    <table class="table table-bordered">
                        <tr bgcolor="#1b1b1b">
                            <?php for($i=0; $i<7; $i++){
                                $dayNumber = $i;
                            ?> 
                                <th width="12%" class="text-center p-2"><?php echo date('l', strtotime("Sunday +{$dayNumber} days")); ?></th>
                            <?php  } ?> 
                            <th class="p-2">Status</th>
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
                            if(!in_array($day,(array)$weekdays)){ 
                                $weekoff = $weekoff+1;
                                $ccww = "o"; 
                            }
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
                                if($data['timesheet'][$predate]['current_status']==199){ 
                                    $viewstatus = "1"; 
                                }

                            }
                                if($wk==0){ echo "<tr>";  }
                            ?>
                            <?php 
                                if(strtotime($sdate) == strtotime($predate) && date('N',strtotime($sdate))!=0){
                                    for ($m=0; $m < date('w',strtotime($sdate)) ; $m++) { 
                                        echo "<td></td>";
                                        $wk++;
                                    }
                                }
                            ?>
                                <td id="td_<?php echo $num; ?>" class="week_<?php echo $ccww; ?>">
                                    <div class="float-end">
                                        <?php echo $num; ?>
                                    </div>
                                    <?php if($ccww=="" || ($ccedit==1 && $ccww !="" && $viewstatus !=1 ) ){ ?>
                                        <div class="d-block"> 
                                            <label class="form-label badge badge-md cursor-pointer badge-hover" style="background-color:#5ed596;" onclick="attendanceupload('p','<?php echo $predate; ?>', <?php echo $num; ?>,'Present')">P</label>
                                            <label class="form-label badge badge-md cursor-pointer badge-hover" style="background-color:#f07167" onclick="attendanceupload('a','<?php echo $predate; ?>', <?php echo $num; ?>,'Absent')">A</label>
                                        </div>
                                        <div class="d-block">
                                            <label class="form-label badge badge-md cursor-pointer badge-hover mb-0" style="background-color:#b2b2b2" onclick="attendanceupload('z','<?php echo $predate; ?>', <?php echo $num; ?>,'Half Day')">Z</label>
                                            <label class="form-label badge badge-md cursor-pointer badge-hover mb-0" style="background-color:#ffafcc" onclick="attendanceupload('l','<?php echo $predate; ?>', <?php echo $num; ?>,'Leave')">L</label>
                                        </div>
                                    <?php } ?>
                                    <?php if($ccww !="" && $viewstatus ==1){ ?>
                                        <label class="form-label badge f-12" style="color:#FFFFFF";> <?php echo $view_data; ?></label>
                                    <?php } ?>
                                </td>

                                <?php if(($k+1) == $t){
                                    for($n=$wk;$n<6; $n++ ){ ?>
                                        <td></td>
                                    <?php $wk++; }

                                }?>
         
                                <?php  if($wk==6){ ?>
                                <td>
                                    <?php 
                                        $table_name = 'project_timesheets'; $row_id ='';
                                        $cstatus = '';
                                        if(isset($data['timesheet'][$predate])){
                                            $row_id = $data['timesheet'][$predate]['id']; 
                                            $cstatus = $data['timesheet'][$predate]['current_status']; 
                                        }  
                                        if($row_id!="") { 
                                         $nmm= ViewStatusValue($cstatus,$table_name);  ?>
                                         <div class="btn-group">
                                          <button onclick="displaystausdrop(<?php echo $row_id; ?>,<?php echo "'".$table_name."'"; ?>,<?php echo $cstatus; ?>, '<?php echo $predate; ?>')" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#". $nmm['color']; ?>;    background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $nmm['name']; ?> </button>
                                          <div class="dropdown-menu" id="dropdownlist_<?php echo $predate; ?>">
                                          </div>
                                        </div>
                                        <?php }  ?>
                                </td>
                        </tr>
                        <?php }
                        $wk++;
                        if($wk==7){ $wk = 0; } 
                    } ?>            
                </table>
                <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $emp_id; ?>">
                <input type="hidden" name="optionvalue" id="optionvalue" value="COM_VIEW_MONTHLY_WORK_ATTENDANCES">
              </div>
            </div> 
            <div class="col-lg-4">
                    <div class="card">
                        <div class="card-block button-list text-center">
                            <!-- start flat Button -->
                            <button type="button" class="btn btn-primary waves-effect waves-light mb-0" data-bs-toggle="tooltip" data-placement="top" title="Total Days" style="margin-right:5px;">Total Days
                              <span class="badge d-block m-0 mt-2"><?php echo $total-($hlidays+$weekoff); ?></span>
                            </button>
                            <button type="button" class="btn btn-success waves-effect waves-light mb-0" data-bs-toggle="tooltip" data-placement="top" title="Present" style="margin-right:5px;">Present
                              <span class="badge d-block m-0 mt-2"><?php echo $pres; ?></span>
                            </button>
                            <button type="button" class="btn btn-danger waves-effect waves-light mb-0" data-bs-toggle="tooltip" data-placement="top" title="Absent" style="margin-right:5px;">Absent
                              <span class="badge d-block m-0 mt-2"><?php echo $asent; ?></span>
                            </button>
                            <button type="button" class="btn btn-warning waves-effect waves-light mb-0" data-bs-toggle="tooltip" data-placement="top" title="Leaves" style="margin-right:5px;">Leaves
                              <span class="badge d-block m-0 mt-2"><?php echo $leaves; ?></span>
                            </button>
                            
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>With Me</h5>
                        </div>
                        <div class="card-body custom-scroll text-center" style="max-height:200px;">
                            <!-- <span class="text-muted f-w-600">125</span> -->
                            <?php 
                                if(count((array)$data['timesheet'])>0){
                            foreach((array)$data['timesheet'] as $submited){ 
                                if($submited['current_status']<10){
                            ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-lg-3">
                                    <button data-bs-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;<?php echo $sprogress[$submited['current_status']]['name']; ?>&nbsp;&nbsp;" class="waves-effect waves-light btn-primary p-l-15 p-r-15 p-t-10 p-b-10 b-radius-5 border-0" style="background-color: #2ed8b6;"><i class="ti-calendar f-w-600"></i></button>
                                </div>
                                <div class="col-lg-6 p-0">
                                    <label class="form-label badge badge-md cursor-pointer badge-hover" style="background-color:<?php echo $_SESSION['status'][$submited['current_status']]['background_color']; ?>;" ><?php echo $sprogress[$submited['current_status']]['name']; ?></label>
                                    <h6 class="f-w-600"><?php echo fromDate($submited['timesheet_date']); ?></h6>
                                    
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label label label-success">Present</label>
                                </div>
                            </div>
                            <?php } } } else{?>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 100px!important;">
                            <?php } ?>
                            
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>With Manager</h5>
                        </div>
                        <div class="card-body custom-scroll text-center" style="max-height:175px;">
                            <!-- <span class="text-muted f-w-600">125</span> -->
                            <?php 
                                if(count((array)$data['timesheet'])>0){
                            foreach((array)$data['timesheet'] as $submited){ 
                                if($submited['current_status']>=10 && $submited['current_status'] <199){
                            ?>
                            <div class="row mt-3 pb-3 " style="border-bottom:1px dashed #cccccc;">
                                <div class="col-lg-3">
                                    <button data-bs-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;<?php echo $sprogress[$submited['current_status']]['name']; ?>&nbsp;&nbsp;" class="waves-effect waves-light btn-primary p-l-15 p-r-15 p-t-10 p-b-10 b-radius-5 border-0" style="background-color: <?php echo "#".$_SESSION['status'][$submited['current_status']]['background_color']; ?>"><i class="ti-calendar f-w-600"></i></button>
                                </div>
                                <div class="col-lg-6 p-0">
                                    
                                      <label class="form-label badge badge-md cursor-pointer badge-hover" style="background-color:<?php echo $_SESSION['status'][$submited['current_status']]['background_color']; ?>;" ><?php echo $sprogress[$submited['current_status']]['name']; ?></label>
                                      <h6 class="f-w-600"><?php echo fromDate($submited['timesheet_date']); ?></h6>
                                    
                                </div>
                                <div class="col-lg-3">

                                </div>
                            </div>
                            <?php } } } else{?>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 100px!important;">
                            <?php } ?>
                            
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Recently Approved</h5>
                        </div>
                        <div class="card-body custom-scroll text-center" style="max-height:175px;">
                            <!-- <span class="text-muted f-w-600">125</span> -->
                            <?php 
                                if(count((array)$data['timesheet'])>0){
                            foreach((array)$data['timesheet'] as $submited){ 
                                if( $submited['current_status'] ==199){
                            ?>
                            <div class="row mt-3 pb-3 " style="border-bottom:1px dashed #cccccc;">
                                <div class="col-lg-3">
                                    <button data-bs-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;<?php echo $sprogress[$submited['current_status']]['name']; ?>&nbsp;&nbsp;" class="waves-effect waves-light btn-primary p-l-15 p-r-15 p-t-10 p-b-10 b-radius-5 border-0" style="background-color: #2ed8b6;"><i class="ti-calendar f-w-600"></i></button>
                                </div>
                                <div class="col-lg-6 p-0">
                                    
                                      <label class="form-label badge badge-md cursor-pointer badge-hover" style="background-color:<?php echo $_SESSION['status'][$submited['current_status']]['background_color']; ?>;" ><?php echo $sprogress[$submited['current_status']]['name']; ?></label>
                                      <h6 class="f-w-600"><?php echo fromDate($submited['timesheet_date']); ?></h6>
                                    
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label label label-success">Present</label>
                                </div>
                            </div>
                            <?php } } } else{?>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 100px!important;">
                            <?php } ?>
                            
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
</div>
</div>