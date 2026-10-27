<?php

function numberformatechnagetime($val){
 $value = strlen(number_format($val,2))!='5'?'0'.number_format($val,2):number_format($val,2);
 return $value;
}
function customeummarmonyformatenew($number){
 $num = $_SESSION['configdata']['number_format'] !='' ? $_SESSION['configdata']['number_format'] :2;
 $return = number_format($number, $num, '.', ',');
 return $return!='00.00' ? $return : '00.00';
}
function houreConvertRounvalue($fulltime){
  $hour=0;
  $minute=0;
  $splitTime1= explode(".",$fulltime);
  $hour = $splitTime1[0];
  $minute = $splitTime1[1];
  $hour = $hour + $minute/60;
  $minute = $minute%60;
  return converttwodigithourse(intval($hour).".".$minute);
}
function converttwodigithourse($time){
  $splitTime1= explode(".",$time);
  $hour = strlen($splitTime1[0]) =='1'?'0'.$splitTime1[0]:$splitTime1[0];
  $minute = strlen($splitTime1[1]) =='1'?'0'.$splitTime1[1]:$splitTime1[1];
  return $hour.".".$minute;
}
if($_GET['Rid']!=""){
 $table_name = "project_timesheets";
 $rid =  encodeId($_GET['Rid']);
 $rid = urldecode($rid);
 $url = API."?mode=togetdatabasepaimairy&rid=$rid&table=".$table_name;
 $response = json_decode(callAPI($url),1)?? [];
 $selected_date = $response['timesheet_date'];
 $row_id = $response['id'];
 $projectdetails = $response;
 $emp_id = $response['emp_id'];
}else{
 $selected_date = $_GET['CHANGE_DATE']=="" ? date('Y-m-d'): fromDate($_GET['CHANGE_DATE']);
 $emp_id = $_GET['EMP']!="" ? $_GET['EMP']:$_SESSION['emp_id'];
}
$daysname =array('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY');
$dates =  array();
$prevMon = date("Y-m-d", strtotime('monday this week', strtotime($selected_date)));
function isValidDateFormat($date, $format = 'Y-m-d'){
  $dt = DateTime::createFromFormat($format, $date);
  if($dt && $dt->format($format) === $date){ return date("Y-m-d",strtotime($dt->format($format)));
}else{
    $dd = explode('-', $date);
    return date("Y-m-d",strtotime($dd[0].'-'.$dd[1].'-'.$dd[2]));
  }
}
$dste = $_SESSION['config']['default_timesheet_date']==""?'01':$_SESSION['config']['default_timesheet_date'];
$premonth = date('m',strtotime($selected_date))+1;
$preyyear = date('Y',strtotime($selected_date));
if($premonth == '13'){
  $preyyear++;
  $premonth = '01';
}
$thismonth = date('Y-m',strtotime($selected_date));
$privvvdate = date($preyyear.'-'.$premonth.'-'.$dste);
$thisdate = date($thismonth.'-'.$dste);
$startingdate = isValidDateFormat($thisdate);
$endingdate = isValidDateFormat($privvvdate);
$dates =array();$datesx =array();$datesy =array();
$xx=0;
$yy=0;
for($i=0; $i<7; $i++){
 $d = date("Y-m-d", strtotime( $prevMon." + $i day"));
 if(strtotime($d) >= strtotime($startingdate) && strtotime($d) < strtotime($endingdate) && strtotime($d) >= strtotime($_SESSION['employee']['joining_date']) ){
   $datesx[]=$d;
 }
}
$dates = $datesx;
$rfmdfvsize = sizeof($dates);
$selected_week = implode(',',$dates);
$vurl = API."?mode=gettimesheetentries&emp_id=".$emp_id."&company_id=".$_SESSION['company_id']."&selected_week=".$selected_week."&startingdate=".$startingdate."&endingdate=".$endingdate;
$ts_data = callAPI($vurl);
$timesheetdata = json_decode($ts_data,1);
$timesheetdetails = $timesheetdata['timesheets'];
$task_names = $timesheetdata['task'];
$timesheet_dates = $timesheetdata['days'];
$submit = 0; $approve = 0;
$submit = $timesheetdata['billable_hrs']==""?'0':$timesheetdata['billable_hrs'];
$approve = $timesheetdata['approve_hrs']==''?'0':$timesheetdata['approve_hrs'];
$month_lock = $timesheetdata['month_lock'];

foreach($dates as $ddates){
 if(isset($timesheetdetails['0'][$ddates])){
  $projectdetails = $timesheetdetails['0'][$ddates];
  break;
}
}
$vendor_id = $_SESSION['employee']['vendor_id'];
$check_user_id = $_SESSION['employee']['user_id'];
$show_error = 'N';
if($emp_id!=""){
  if($_SESSION['emp_id'] == $emp_id){
    $userdisplayname = $_SESSION['employee']['display_name'];
    $data_employee = $_SESSION['login_details']['employees'];
  }else{
    $url = API."?mode=singlerowtabledata&table=employees&fid=id&rid=".$emp_id;
    $response = callAPI($url);
    $data_employee = json_decode($response,1);
    $userdisplayname = $data_employee['display_name'];
    $vendor_id = $data_employee['vendor_id'];
    if($_GET['mode']!=""){
      $check_user_id = $data_employee['user_id'];
    }
  }
}else{
  $useriddisplayid = (isset($projectdetails['created_by']))?$projectdetails['created_by']:$_SESSION['userid'];
  $userdisplayname = $_SESSION['users'][$useriddisplayname];
}
if($data_employee['billable'] == 2){
    $show_error = 'Y';
}
$row_id = $projectdetails['id'];
$empurl = API."?mode=singlerowtabledata&fid=id&rid=".$emp_id."&table=employees";
$empres = callAPI($empurl);
$empres = json_decode($empres,1);
$emp_bill = $empres['billable'];
if(isset($month_lock['id']) && $month_lock['id']!=""){
  if($month_lock['lock_type']==0){
    $emp_bill =1;
    $_GET['error'] = "9051";
  }
  if($month_lock['lock_type']==1 && $month_lock['lock_type_list']!=""){
      $lock_ven = json_decode($month_lock['lock_type_list'],1);
      if(in_array($vendor_id,$lock_ven)){
        $emp_bill =1;
        $_GET['error'] = "9051";
      }
  }
}
function timeConvertphp($val){
 $value = str_replace(' ', '', $val);
 $value = str_replace('m', '', $value);
 $value = str_replace('h', '.', $value);
 return $value;
}
if(isset($_POST['savetimesheet'])){
 $vurl = API."?mode=tocheckesginemployee&emp_id=".$_SESSION['emp_id'];
 $configesponse = callAPI($vurl);
 $check_data = json_decode($configesponse,1);
 if((strtotime($check_data['effective_to_date']) < strtotime($timesheet_dates[$rfmdfvsize])) && !isset($check_data['effective_to_date'])){
         echo '<script type="text/javascript">alert("check"); swal.fire({ title: "6010 : Please Define Exchange Rate",type: "warning",buttonsStyling: false,confirmButtonText: "OK",confirmButtonClass: "btn btn-danger"})</script>';
       }else{
         $x =1;
         $insertData['startingdate'] = $_POST['startingdate'];
         $insertData['endingdate'] = $_POST['endingdate'];
         foreach($_POST['rownumber'] as $taskkey=>$task) {
          $datename = "";
          $datename = "task".$task;
          $task = $_POST['task_name'][$taskkey];
          for ($i=0; $i<$rfmdfvsize; $i++){
            $billablehrs = timeConvertphp($_POST[$datename.'_billable'][$i]);
            $nonbillable_hrs = timeConvertphp($_POST[$datename.'_nonbillable'][$i]);
           $insert[] = array('rowid'=>$_POST[$datename.'_rowid'][$i],'emp_id'=>$emp_id,'company_id'=>$_SESSION['company_id'],'timesheet_date'=>date('Y-m-d',strtotime($timesheet_dates[$i])),'task_name'=>$task,'billable_hrs'=>$billablehrs,'nonbillable_hrs'=>$nonbillable_hrs,'is_active'=>'0','created_by'=>$_SESSION['userid'],'created_date'=>date('Y-m-d'),'vendor_id'=>$vendor_id,'work_order'=>$_POST['work_order']);
    $chage_date = date('Y-m-d',strtotime($timesheet_dates[$i]));
   }
          $x++;
        }
        $insertData['timesheetdata'] = $insert;
        $url = API."?mode=addtimesheetentries&method=post";
        $retunn_response = file_post_contents($url,$insertData,'insert');
        $retunn_response = json_decode($retunn_response,1);
        if(isset($retunn_response['error']) || $retunn_response['error'] == '404'){

      ?>
<script type="text/javascript">
  Swal.fire({
  icon: 'error',
  title: 'Oops...',
  text: '<?php echo $_SESSION['error_message']['9028']; ?>'
  });
  </script>
      <?php
        }else{
           header('location: index.php?option='.$_GET['option'].'&Rid='.$_GET['Rid'].'&CHANGE_DATE='.$_GET['CHANGE_DATE']);
   }

      }
    }

   $woidurl = API."?mode=getprojecttimesheetworkorderid&emp_id=".$emp_id."&startdate=".$datesx[0]."&enddate=".$datesx[count($datesx)-1];
   $woidcall = callAPI($woidurl);
    $woidresplist = json_decode($woidcall,1);











  function checkValidationholidayworkingdays($holidaylist,$working_days,$checkdate,$leavesemp=array(),$vl=""){
    $checkdateworkholiday = date('Y-m-d',strtotime($checkdate));
    $day = date('l',strtotime($checkdate));
    if(in_array($checkdateworkholiday, (array)$holidaylist)){
      return array("color"=>"background-color:#0dcaf0",'msg'=>"Holidays",'error'=>'tabindex="-1"','read'=>'Y');
    }elseif(in_array($checkdateworkholiday, (array)$leavesemp)){
      return array("color"=>'background-color:#e3c1c1','msg'=>"Leave",'error'=>'tabindex="-1"','read'=>'N');
    }elseif(!in_array($day, (array)$working_days)){
      return array("color"=>'background-color:#e1e1e1','msg'=>"No Working Day",'error'=>'tabindex="-1"','read'=>'Y');
    }else{
      return array("color"=>'','msg'=>"",'error'=>"",'read'=>'N');
    }
  }

  $getworkorderid = (isset($_GET['WID']) && $_GET['WID']!="" && $_GET['WID']!="0")?$_GET['WID']:$projectdetails['work_order'];

  $endingdate = date('Y-m-d', strtotime('-1 day', strtotime($endingdate)));
  $gettimesheetsholidaysleaveoffurl = API."?mode=gettimesheetsholidaysleaveoff&emp_id=".$emp_id."&startingdate=".$startingdate."&endingdate=".$endingdate;
  $gettimesheetsholidaysleaveoff = callAPI($gettimesheetsholidaysleaveoffurl);
  $gettimesheetsholidaysleaveoffres= json_decode($gettimesheetsholidaysleaveoff,1);
  $working_days = $gettimesheetsholidaysleaveoffres['workingdaysdetails'];
  $holidaylist = $gettimesheetsholidaysleaveoffres['holidaydetails'];
  $leavesemp = $gettimesheetsholidaysleaveoffres['leaves'];

  $minworkinghoursary = explode(":", $gettimesheetsholidaysleaveoffres['min_workig_hurse']);
  $minworkinghours = $minworkinghoursary[0].".".$minworkinghoursary[1];
  $empbillablestatus  = $gettimesheetsholidaysleaveoffres['empdetails']['billable'];
  $worderid = "";
  if($getworkorderid ==""){
    if(count($woidresplist)=='1'){
       $worderid = $woidresplist[0]['id'];
    }
  }else{
    $worderid = $getworkorderid;
  }
  $allowovertime = $gettimesheetsholidaysleaveoffres['work_order_list'][$worderid];
  ?>
  <style type="text/css">
   .table td,.table th {
    padding: 1.25rem 0.1rem !important;
  }
   .breadcrumb{
    z-index: 1 !important;
  }
</style>
<script type="text/javascript">
  $(document).ready(function() {
   console.log("=========DDDD====");
    var minDate = new Date();
    $("#selected_date").datepicker({

      minDate: minDate
    });
  });
  function calculateRowSum(row,col) {
    calucationrecheck();
  }
  function calculateweeklys(){

  console.log("used calc");
    var tab=0;
    var tanb=0;
    for(let t=1; t<=<?php echo $rfmdfvsize; ?>; t++){
     tab = tab+timeConvert(document.getElementById('task_r'+t+'_total_billable').value);
     tanb =tanb+timeConvert(document.getElementById('task_r'+t+'_total_nonbillable').value);
   }
   tab = houreConvertRounvalue(tab.toFixed(2));
   tanb = houreConvertRounvalue(tanb.toFixed(2));
   document.getElementById('weekly_total_billable').value =tab;
   document.getElementById('weekly_total_nonbillable').value = tanb;
 }
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
    function displaystausdrop(id,table,status_id){
      $.ajax({
       url: "ajax.php",
      data:'page=current_status_drop_project_timesheets&id='+id+'&current_status='+status_id+'&table='+table+'&user_id='+$("#check_user_id").val(),
      type: "POST",
       success:function(data){
        console.log(data);
        $("#dropdownlist").html(data);
      },
      error:function (){}
    });
    }
    function removetaskrows(){
      $(".itemRow:checked").each(function() {
       var id = this.value;
        $(this).closest('tr').remove();
         $.post("ajax.php",{"page":"deletetimesheets","id":id},function(data){
          var msg = <?php echo "'".$_SESSION['error_message']['3002']."'" ?>;
          swal.fire({
           title: msg,
           type: "success",
           buttonsStyling: false,
           confirmButtonText: "<i class='la la-hand-o-up'></i>OK",
           confirmButtonClass: "btn btn-info",
           showCancelButton: false,
           cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
           cancelButtonClass: "btn btn-default"
         })
        });
       });

      $('#checkAll').prop('checked', false);
      calucationrecheck();
    }
    function addtaskrows(datename){
      var xTable=document.getElementById('kt_datatable1');
      var body = xTable.tBodies[0];
      var rows = body.rows;
      var index = Number(document.getElementById('taskrows').value)+1;
      document.getElementById('taskrows').value = index;
      var datename = datename+index;
      var billable = 'task_r'+index+'_billable_c';
      var nonbillable = 'task_r'+index+'_nonbillable_c';
      console.log(billable+" -- "+nonbillable);
      var htmlRows = '';
      var temmpp ='';
         <?php $xk=1; $i=0; foreach($dates as $kef => $datev){
           $datevalidation = checkValidationholidayworkingdays($holidaylist,$working_days,$datev,$leavesemp);
           ?>
          temmpp += '<td style="<?php echo $datevalidation['color']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $datevalidation['msg']; ?>"><input aria-label="Input field" class="form-control itemRow  text-right  timesheet_time <?php if($empbillablestatus == "1" && TIMESHEET_BILLABLE_CHECk == "Y"){ echo "d-none";
}?>" <?php echo $datevalidation['error']; ?> type="text" onblur="calculateRowSum('+index+','+'<?php echo $xk; ?>'+')" value="00.00" name="'+datename+'_billable[]"
 <?php if($emp_bill==1){ echo "readonly";
} ?>
 <?php if($datevalidation['read'] == "Y"){ echo "readonly";
} ?>  id="'+billable+'<?php echo $xk; ?>" maxlength="5" tabindex="" data-rowcol="'+index+'-<?php echo $xk; ?>" ><input aria-label="Input field" class="form-control  text-right mt-2  timesheet_time
 <?php if($empbillablestatus == "0" && TIMESHEET_BILLABLE_CHECk == "Y"){ echo "d-none";
}?>" type="text" <?php echo $datevalidation['error']; ?> onblur="calculateRowSum('+index+','+'<?php echo $xk; ?>'+')"  value="00.00" name="'+datename+'_nonbillable[]" id="'+nonbillable+'<?php echo $xk; ?>" maxlength="5" data-rowcol="'+index+'-<?php echo $xk; ?>"></td>';
          <?php  $xk++; $i++;
} ?>

          htmlRows += '<td> <input type="hidden" name="rownumber[]" value="'+index+'"> <input type="hidden" name="rowidscomin[]" value="'+index+'"><div class="checkbox-fade fade-in-primary checkbox mt-2 ml-2"> <label class="form-label"><input aria-label="Checkbox" class="itemRow" type="checkbox"><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></td><td><input aria-label="Task Name[]" class="form-control " type="text" name="task_name[]"></td>  <input type="hidden" id="rowid" name="'+datename+'_rowid[]" value="" /> <td class="text-center"> <span class="symbol symbol-35 symbol-info <?php if(TIMESHEET_BILLABLE_CHECk == "Y"){ echo "d-none";
}?>"><span class="form-label badge bg-success font-weight-bold f-14 m-t-15"> B</span> </span></br><span class="symbol symbol-35 symbol-info mt-2
 <?php if(TIMESHEET_BILLABLE_CHECk == "Y"){ echo "d-none";
}?>"><span class="form-label badge bg-danger font-weight-bold f-14 m-t-10">NB</span></span></td>';
          htmlRows += temmpp;
          htmlRows += '<td ><input aria-label="Input field" class="form-control text-right font-weight-bolder <?php if($empbillablestatus == "1" && TIMESHEET_BILLABLE_CHECk == "Y"){ echo "d-none";
}?>" tabindex="-1"  value="00.00" type="text" readonly id="total_billable_row'+index+'" > <input aria-label="Input field" class="form-control text-right mt-2 font-weight-bolder
 <?php if($empbillablestatus == "0" && TIMESHEET_BILLABLE_CHECk == "Y"){ echo "d-none";
}?>" value="00.00"  tabindex="-1" type="text" readonly id="total_nonbillable_row'+index+'"> </td>';
          htmlRows += '</tr>';
          rows[rows.length - 1].insertAdjacentHTML('afterend',htmlRows);
          $('[data-bs-toggle="tooltip"]').tooltip();
        }
        function timeConvert(value){
         var valu = value.replaceAll(" ",'');
         var valus = valu.replaceAll("h",'.');
         var value = valus.replaceAll("m",'');
         return Number(value);
       }
       function calucationrecheck(){
        var totalrows = parseInt(document.getElementById('taskrows').value);
        var billable = "";
        var nonbillable = '';
        var totalbillable = 0;
        var totalnonbillable = 0;
        console.log("totalrows ---- to check:"+totalrows);
        console.log("Step 1 ---- to check1");
        for(let row=1; row<=totalrows; row++){
          console.log("Step 2 ---- to check "+row);
         billable = 'task_r'+row+'_billable_c';
         nonbillable = 'task_r'+row+'_nonbillable_c';
         totalbillable = 0;
         totalnonbillable = 0;
         if(document.getElementById(billable+1)){
          for(let k=1; k<=<?php echo $rfmdfvsize; ?>; k++){
            totalbillable += timeConvert(document.getElementById(billable+k).value);
            totalnonbillable += timeConvert(document.getElementById(nonbillable+k).value);
          }

          totalbillable = houreConvertRounvalue(totalbillable.toFixed(2));
          totalnonbillable = houreConvertRounvalue(totalnonbillable.toFixed(2));
          document.getElementById('total_billable_row'+row).value = totalbillable;
          document.getElementById('total_nonbillable_row'+row).value = totalnonbillable;
        }
      }
      console.log("Step3 ---- to check1");
      var totalbillablecolumn = 0;
      var totalnonbillablecolumn = 0;
      var columbillable="";
      var columnonbillable="";
      for(let t=1; t<=<?php echo $rfmdfvsize; ?>; t++){
        console.log("Step1 ---- to check "+t);
        totalbillablecolumn = 0;
        totalnonbillablecolumn = 0;
        for(let row=1; row<=totalrows; row++){
         columbillable = 'task_r'+row+'_billable_c'+t;
         columnonbillable = 'task_r'+row+'_nonbillable_c'+t;
         if(document.getElementById(columbillable)){
           totalbillablecolumn += timeConvert(document.getElementById(columbillable).value);
           totalnonbillablecolumn += timeConvert(document.getElementById(columnonbillable).value);
         }
       }
       console.log("Step4 ---- to check1");
       totalbillablecolumn = houreConvertRounvalue(totalbillablecolumn.toFixed(2));
       totalnonbillablecolumn = houreConvertRounvalue(totalnonbillablecolumn.toFixed(2));
       document.getElementById('task_r'+t+'_total_billable').value = totalbillablecolumn;
       document.getElementById('task_r'+t+'_total_nonbillable').value = totalnonbillablecolumn;
       console.log("Step5 ---- to check1");
     }
     console.log("Step6 ---- to check1");
     calculateweeklys();
   }
   function houreConvertRounvalue(fulltime){
    var hour=0;
    var minute=0;
    if(fulltime.toString().length=='1'){
     return converttwodigithourse(fulltime+'.0');
   }else{
     var splitTime1= fulltime.toString().split('.');
     hour = parseInt(splitTime1[0]);
     minute = parseInt(splitTime1[1]);
     hour = hour + minute/60;
     minute = minute%60;
     return converttwodigithourse(parseInt(hour)+"."+minute);
   }
 }
 function converttwodigithourse(time){
  var splitTime1= time.toString().split('.');
  var hour = splitTime1[0].length =='1'?'0'+splitTime1[0]:splitTime1[0];
  var minute = splitTime1[1].length =='1'?'0'+splitTime1[1]:splitTime1[1];
  return hour+"."+minute;
}
function changeselectdate(){
  var selected_date =  $("#selected_datess").val();
  var emp_id =  $("#getemp_id").val();
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
   window.location = "index.php?option="+option+"&EMP="+emp_id+"&CHANGE_DATE="+selected_date+"&WID="+getwork_order+"&mode="+mode;
  }
}

</script>
<?php if($show_error != 'N'){
  $_GET['error'] = 9142;

    include_once '404.php';

}else{ ?>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid j-pro border-0">
    <div class="flex-row j-content">
      <?php if($_GET['error'] !=''){ ?>
      <div class="alert alert-warning alert-dismissible">
        <strong><?php echo $_GET['error']." : ".$_SESSION['error_message'][$_GET['error']]; ?> </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php } ?>
      <div class="card">
        <div class="card-header table-card-header mb-2">
          <h5><?php echo "Weekly Time Log";  ?>
            <span class="form-label badge bg-primary p-2 m-l-5">
              <i class='ti-user'></i> <?php echo $userdisplayname; ?>
            </span>
          </h5>
          <button type="button" class="btn p-0 border-0 bg-transparent ti-info-alt f-20 text-primary" onclick="proceduressteps()" data-bs-toggle="tooltip" data-placement="bottom" title="Go Tour" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
          <div class="card-header-right table-card-header ">
            <?php
            $table_name = 'project_timesheets';
            if($row_id!="") {
            $nmm= ViewStatusValue($projectdetails['current_status'],$table_name);  ?>
            <div class="btn-group">
              <button onclick="displaystausdrop(<?php echo $row_id; ?>,<?php echo "'".$table_name."'"; ?>,<?php echo $projectdetails['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#". $nmm['color']; ?>;    background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $nmm['name']; ?>
              </button>
              <div class="dropdown-menu" id="dropdownlist"></div>
            </div>
            <?php } ?> &nbsp;
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>Back
            </a>
            &nbsp;
          </div>
        </div>
        <?php echo $dis_status=todisplaystatuslist(array('table'=>$table_name,'grand_total'=>'0','current_status'=>$projectdetails['current_status'])); ?>
        <div class="card-custom mt-3">
          <form method="post" action="#" class="">
            <div class="card-body">
              <input type="hidden" id="error_message" value="<?php echo $_SESSION['error_message']['9027'];?>" />
              <input type="hidden" name="emp_id" id="getemp_id" value="<?php echo $emp_id; ?>">
              <input type="hidden" name="optionvalue" id="optionvalue" value="<?php echo $_GET['option']; ?>">
              <input type="hidden" name="check_user_id" id="check_user_id" value="<?php echo $check_user_id; ?>">
              <input type="hidden" name="mode" id="mode" value="<?php echo $_GET['mode']; ?>">
              <div class="row align-items-center">
                <div class="col-lg-1 text-end">
                  <span class="f-w-600">Date</span>
                </div>
                <div class="col-lg-3">
                  <div class="">
                    <input aria-label="Select date" type="text" class="form-control datepickerprevious"  placeholder="Select date" value="<?php echo fromDate($selected_date); ?>" name="selected_date" id="selected_datess"  onchange="changeselectdate()"/>
                    <p class="text-danger f-12 errordate position-absolute"></p>
                  </div>
                </div>
                <div class="col-lg-2 text-end">
                  <span class="f-w-600">Purchase Order</span>
                </div>
                <div class="col-lg-2">
                  <select aria-label="Work Order" class="form-select js-example-basic-single" name="work_order" id="getwork_order" onchange="changeselectdate()">
                    <option value="0">Select Any one</option>
                      <?php $getworkordravailable = "0"; foreach($woidresplist as $woidresplists){ ?>
                    <option <?php if($getworkorderid== $woidresplists['id'] || count($woidresplist)=='1'){ $getworkordravailable=$woidresplists['id']; echo "selected";
} ?> value="<?php echo $woidresplists['id']; ?>"> <?php echo $woidresplists['transaction_number']; ?> </option>
                    <?php }  ?>
                  </select>
                  <p class="text-danger f-12 errorworkorder position-absolute"></p>
                </div>
              </div>
            </div>
            <div class="row workorderselectshow" <?php if($getworkordravailable=="0"){ echo 'style="display:none"';
} ?> >
              <div class=" text-right" style="text-align: right;">
                <div class="btn btn-icon w-auto btn-clean d-inline-flex align-items-center btn-sm px-2 mr-2">
                  <span class="text-dark-50 font-weight-bolder font-size-base f-w-600 f-14">Submited Hrs : <span class=" form-label badge bg-primary f-14"><?php echo $submit; ?></span></span>
                </div>
                <div class="btn btn-icon w-auto btn-clean d-inline-flex align-items-center btn-sm px-2 mr-2">
                  <span class="text-dark-50 font-weight-bolder font-size-base f-w-600 f-14">Approved Hrs :
                    <span class="form-label badge bg-info f-14"><?php echo $approve; ?></span>
                  </span>
                </div>
                <div class="btn btn-icon w-auto btn-clean d-inline-flex align-items-center btn-sm px-2 mr-2 <?php if(TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>">
                  <span class="text-dark-50 font-weight-bolder font-size-base mr-3 f-w-600 f-14">Billable&nbsp;</span>
                  <span class="symbol symbol-35 symbol-info">
                    <span class="form-label badge bg-success font-weight-bold f-14 m-t-5">B</span>
                  </span>
                </div>
                <div class="btn btn-icon w-auto btn-clean d-inline-flex align-items-center btn-sm px-2 mr-2 <?php if(TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>">
                  <span class="text-dark-50 font-weight-bolder font-size-base mr-3 f-w-600 f-14">Non Billable&nbsp;</span>
                  <span class="symbol symbol-35 symbol-info">
                    <span class="form-label badge bg-danger font-weight-bold f-14 m-t-5">NB</span>
                  </span>
                </div>
              </div>
            </div>

            <div style="clear: both;"></div>
            <input type="hidden" id="startingdate" name="startingdate" value="<?php echo $startingdate; ?>" />
            <input type="hidden" id="endingdate" name="endingdate" value="<?php echo $endingdate; ?>" />
            <input type="hidden" id="minworkinghours"  value="<?php echo $minworkinghours; ?>" />
            <input type="hidden" id="allowovertime" value="<?php echo ($allowovertime=="0")?$allowovertime:'1'; ?>" />
            <input type="hidden" id="error_message_timeextra" value="<?php echo $_SESSION['error_message']['9035'];?> <?php echo ($allowovertime=="0")?"24":$minworkinghours; ?>Hrs" />
            <div class="card-custom">
              <div class="card-body">
                <div class="table-responsive">
                  <table  class="table table-hover " id="kt_datatable1">
                    <thead>
                      <tr>
                        <th scope="col"></th>
                        <th scope="col" style="width: 12%;">Task</th>
                        <th scope="col" style="width: 4%;"></th>
                        <?php  foreach($timesheet_dates as $datekey =>$timesheetdate) { $d=0; $d++; $datename = "day".$d."date";
                        $datevalidation = checkValidationholidayworkingdays($holidaylist,$working_days,$timesheetdate,$leavesemp);
                        ?>
                        <th scope="col" class="text-center" style="<?php echo $datevalidation['color']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $datevalidation['msg']; ?>">
                          <input type="hidden" value="<?php echo $datekey; ?>" name="<?php echo $datename; ?>"/>
                          <?php echo date("l",strtotime($timesheetdate));  ?>
                          <p> <?php echo fromDate($timesheetdate); ?></p>
                        </th>
                        <?php   } ?>
                        <th scope="col">
                          <?php $cctim =  count((array)$timesheetdetails)==0 ? 1 : count((array)$timesheetdetails);?>
                          <input type="hidden" id="taskrows" value="<?php echo $cctim;?>" />
                        </th>
                      </tr>
                    </thead>

                    <?php if($timesheetdetails!='') { $j=1;  $gt = array();
                      $gtt = array();
                        foreach($timesheetdetails as $taskslist =>$taskslistdata) {
                        $d=0; $datename = "task".$j;
                        $idss = array();
                        for($rm=1; $rm<=$rfmdfvsize; $rm++){
                          if($taskslistdata[$timesheet_dates[$rm-1]]['id']!=""){
                          $idss[$taskslist][$rm]= $taskslistdata[$timesheet_dates[$rm-1]]['id'];
                        }
                      }
                    ?>
                    <tr class="itemRow" >
                      <input type="hidden" name="rowidscomin[]" value="<?php echo $j; ?>">
                      <td>
                        <div class="checkbox-fade fade-in-primary checkbox mt-2 ml-2">
                          <label class="form-label">
                            <input aria-label="Checkbox" class="itemRow" type="checkbox" value="<?php echo implode(",",$idss[$taskslist]); ?>">
                            <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                          </label>
                        </div>
                      </td>
                      <td>
                        <input type="hidden" name="rownumber[]" value="<?php echo $j;?>">
                        <input aria-label="Task Name[]" class="form-control" type="text" name="task_name[]"  value="<?php echo $taskslistdata[$timesheet_dates[$j-1]]['task_name'];?>" >
                      </td>
                      <td class="text-center">
                        <span class="symbol symbol-35 d-inline-block symbol-info <?php if(TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" >
                          <span class="form-label badge bg-success font-weight-bold f-14 m-t-15"> B</span>
                        </span>
                        <span class="symbol symbol-35 d-inline-block symbol-info mt-2 <?php if(TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>">
                          <span class="form-label badge bg-danger font-weight-bold f-14 m-t-10">NB</span>
                        </span>
                      </td>
                      <?php

                      $totalbillable = 0;
                      $total_nonbillable = 0;
                      $tabbilable = 1;
                      $tabnonbilable = 1;
                      for($r=1; $r<=$rfmdfvsize; $r++){
                       $billable_hrs =  numberformatechnagetime($taskslistdata[$timesheet_dates[$r-1]]['billable_hrs']);
                       $nonbillable_hrs =  numberformatechnagetime($taskslistdata[$timesheet_dates[$r-1]]['nonbillable_hrs']);
                       $totalbillable += $taskslistdata[$timesheet_dates[$r-1]]['billable_hrs'];
                       $total_nonbillable += $taskslistdata[$timesheet_dates[$r-1]]['nonbillable_hrs'];
                       $id = $taskslistdata[$timesheet_dates[$r-1]]['id'];
                       $gt[$r-1]['billable'] = $gt[$r-1]['billable'] + $taskslistdata[$timesheet_dates[$r-1]]['billable_hrs'];
                       $gt[$r-1]['nonbillable'] = $gt[$r-1]['nonbillable'] + $taskslistdata[$timesheet_dates[$r-1]]['nonbillable_hrs'];
                       $datevalidation = checkValidationholidayworkingdays($holidaylist,$working_days,$timesheet_dates[$r-1],$leavesemp);
                       ?>
                      <td style="<?php echo $datevalidation['color']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $datevalidation['msg']; ?>">
                         <input type="hidden" id="rowid_2" name="<?php echo $datename;?>_rowid[]" value="<?php echo $id;?>" />
                         <input aria-label="Input field" <?php echo $datevalidation['error']; ?> <?php if($datevalidation['read'] == 'Y'){ echo "readonly";
} ?>  class="form-control itemRow bg-success-o-40 text-right timesheet_time
 <?php if($empbillablestatus == '1' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" type="text" onblur="calculateRowSum('<?php echo $j;?>','<?php echo $r;?>')"
 <?php if($emp_bill==1){ echo "readonly";
} ?>  name="<?php echo $datename;?>_billable[]" id="task_r<?php echo $j;?>_billable_c<?php echo $r;?>" value="<?php echo $taskslistdata[$timesheet_dates[$r-1]]['billable_hrs']=='0'?'00.00':$billable_hrs; ?>" maxlength="5" tabindex=""  data-rowcol="<?php echo $j.'-'.$r; ?>">

                         <input aria-label="Input field" <?php echo $datevalidation['error']; ?> class="form-control bg-warning-o-40 text-right timesheet_time <?php if($empbillablestatus == '0' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" type="text" onblur="calculateRowSum('<?php echo $j;?>','<?php echo $r;?>')" value="<?php echo $taskslistdata[$timesheet_dates[$r-1]]['nonbillable_hrs']=='0'?'00.00':$nonbillable_hrs; ?>" name="<?php echo $datename;?>_nonbillable[]" id="task_r<?php echo $j;?>_nonbillable_c<?php echo $r;?>" maxlength="5" data-rowcol="<?php echo $j.'-'.$r; ?>">
                      </td>
                      <?php }
                      $gtt['billable'] = $gtt['billable'] +  $totalbillable;
                      $gtt['nonbillable'] = $gtt['nonbillable'] +  $total_nonbillable;

                      ?>
                      <td>
                        <?php
                        $totalbillable = numberformatechnagetime($totalbillable);
                        $total_nonbillable = numberformatechnagetime($total_nonbillable);
                        ?>
                        <input aria-label="Input field" class="form-control font-weight-bolder text-right <?php if($empbillablestatus == '1' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" tabindex="-1"  type="text" id="total_billable_row<?php echo $j;?>" name="<?php echo $datename;?>_totalbillable" readonly value="<?php echo houreConvertRounvalue($totalbillable); ?>">
                        <input aria-label="Input field" class="form-control font-weight-bolder text-right <?php if($empbillablestatus == '0' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" type="text" tabindex="-1" name="<?php echo $datename;?>total_nonbillable"  id="total_nonbillable_row<?php echo $j;?>" readonly value="<?php echo houreConvertRounvalue($total_nonbillable); ?>">
                      </td>
                    </tr>
                    <?php $d++; $j++;  $tabbilable++;
                    $tabnonbilable++;
}
} else { $d=0; $j=1; $datename = "task".$j;  ?>
                    <tr>
                      <td>
                        <input type="hidden" name="rownumber[]" value="<?php echo 1;?>">
                        <div class="checkbox-fade fade-in-primary checkbox mt-2 ml-2">
                          <label class="form-label">
                            <input aria-label="Checkbox" class="itemRow" type="checkbox" value="-1">
                            <span class="cr">
                             <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                           </span>
                          </label>
                        </div>
                      </td>
                      <td>
                        <input aria-label="Task Name[]" class="form-control" type="text" name="task_name[]" >
                      </td>
                      <td class="text-center">
                        <span class="symbol symbol-35 d-inline-block symbol-info <?php if(TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>">
                          <span class="form-label badge bg-success font-weight-bold f-14 m-t-15"> B</span>
                        </span>
                        <span class="symbol symbol-35 d-inline-block symbol-info  mt-2 <?php if(TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>">
                          <span class="form-label badge bg-danger font-weight-bold f-14 m-t-10">NB</span>
                        </span>
                      </td>
                      <?php
                      $totalbillable = 0;
                      $total_nonbillable = 0;
                      for($r=1; $r<=$rfmdfvsize; $r++){

                      $totalbillable += $taskslistdata[$timesheet_dates[$r-1]]['billable_hrs'];
                      $total_nonbillable += $taskslistdata[$timesheet_dates[$r-1]]['nonbillable_hrs'];
                      $id = $taskslistdata[$timesheet_dates[$r-1]]['id'];
                      $datevalidation = checkValidationholidayworkingdays($holidaylist,$working_days,$timesheet_dates[$r-1],$leavesemp);
                      ?>
                      <td style="<?php echo $datevalidation['color']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $datevalidation['msg']; ?>">
                        <input type="hidden" id="rowid_3" name="<?php echo $datename;?>_rowid[]" value="" />
                        <input aria-label="Input field" <?php echo $datevalidation['error']; ?> <?php if($datevalidation['read'] == 'Y'){ echo "readonly";
} ?>  class="form-control itemRow  text-right timesheet_time
 <?php if($empbillablestatus == '1' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" type="text" onblur="calculateRowSum('<?php echo $j;?>','<?php echo $r;?>')"
 <?php if($emp_bill==1){ echo "readonly";
} ?> name="<?php echo $datename;?>_billable[]" id="task_r<?php echo $j;?>_billable_c<?php echo $r;?>" value="<?php echo customeummarmonyformatenew($taskslistdata[$timesheet_dates[$r-1]]['billable_hrs']=='0'?'0':$taskslistdata[$timesheet_dates[$r-1]]['billable_hrs']); ?>" maxlength="5" tabindex="" data-rowcol="<?php echo $j.'-'.$r; ?>">
                        <input aria-label="Input field"  class="form-control  text-right mt-2  timesheet_time <?php if($empbillablestatus == '0' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" <?php echo $datevalidation['error']; ?> type="text" onblur="calculateRowSum('<?php echo $j;?>','<?php echo $r;?>')" value="<?php echo $taskslistdata[$timesheet_dates[$r-1]]['nonbillable_hrs']=='0'?'0':customeummarmonyformatenew($taskslistdata[$timesheet_dates[$r-1]]['nonbillable_hrs']); ?>" name="<?php echo $datename;?>_nonbillable[]" id="task_r<?php echo $j;?>_nonbillable_c<?php echo $r;?>" maxlength="5" data-rowcol="<?php echo $j.'-'.$r; ?>">
                      </td>
                      <?php } ?>
                      <td >
                        <input aria-label="Input field"  class="form-control text-right  font-weight-bolder <?php if($empbillablestatus == '1' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" type="text" id="total_billable_row<?php echo $j;?>" readonly name="<?php echo $datename;?>_totalbillable"  tabindex="-1" value="<?php echo customeummarmonyformatenew($totalbillable); ?>">
                        <input aria-label="Input field"  class="form-control text-right mt-2  font-weight-bolder <?php if($empbillablestatus == '0' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" type="text" id="total_nonbillable_row<?php echo $j;?>" name="<?php echo $datename;?>total_nonbillable" tabindex="-1"  readonly value="<?php echo customeummarmonyformatenew($total_nonbillable); ?>">
                      </td>
                    </tr>
                    <?php $j++; $d++;
} ?>
                    <tfoot>
                      <tr>
                        <td></td>
                        <th scope="col" colspan="2"> Total</th>
                        <?php
                        for($r=1; $r<=$rfmdfvsize; $r++){

                        ?>
                        <th scope="col">
                          <input aria-label="Input field" class="form-control font-weight-bolder text-right  <?php if($empbillablestatus == '1' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" name="total_bilable_date" tabindex="-1" type="text" readonly id="task_r<?php echo $r;?>_total_billable" value="<?php echo houreConvertRounvalue(numberformatechnagetime($gt[$r-1]['billable']));?>">

                          <input aria-label="Input field" class="form-control font-weight-bolder text-right mt-2 <?php if($empbillablestatus == '0' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>"  name="total_non_bilable_date" tabindex="-1" type="text" readonly id="task_r<?php echo $r;?>_total_nonbillable" value="<?php echo houreConvertRounvalue(numberformatechnagetime($gt[$r-1]['nonbillable']));?>">
                        </th>
                        <?php } ?>
                        <th scope="col">
                          <input aria-label="Input field" class="form-control font-weight-bolder text-right font-weight-bolder <?php if($empbillablestatus == '1' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" tabindex="-1" type="text" readonly id="weekly_total_billable" name="weekly_total_billable" value="<?php echo houreConvertRounvalue(customeummarmonyformatenew($gtt['billable'])); ?>">

                          <input aria-label="Input field" class="form-control font-weight-bolder text-right mt-2 font-weight-bolder  <?php if($empbillablestatus == '0' && TIMESHEET_BILLABLE_CHECk == 'Y'){ echo "d-none";
}?>" tabindex="-1" type="text" readonly id="weekly_total_nonbillable" name="weekly_total_nonbillable" value="<?php echo houreConvertRounvalue(customeummarmonyformatenew($gtt['nonbillable'])); ?>">
                        </th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <?php
                $edisable = "disabled";
                if(($projectdetails['current_status'] == ST_ACTIVE || $projectdetails['current_status'] == ST_SEND_BACK || $projectdetails['current_status']=="") && ($projectdetails['created_by'] == $_SESSION['userid'] || $projectdetails['created_by']=="")){
                  $edisable = "";
                  }

                ?>


                <div class="mt-2">
                  <button class="btn btn-danger waves-effect waves-light btn-sm f-14" id="removeRows" onclick="removetaskrows()" name="delete_btn" type="button">- Delete</button> &nbsp;

                  <button class="btn btn-primary waves-effect waves-light btn-sm f-14" onclick="addtaskrows('task')" id="addRows" name="add_more_btn"  type="button">+ Add More</button>
                </div>
                <div class="card-footer mt-4 p-0">
                  <input type="hidden" id="kt_login_signin_submit"  class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 ml-5" name="savetimesheet" value="Save">
                  <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 btn-sm f-14" name="submit_value" <?php echo $edisable;  ?>  value="continue">
                    <i class="la la-check-circle"></i>Save
                  </button>
                  <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2 btn-sm f-14">
                    <i class="la la-close"></i>Cancel
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>

        <?php echo $todisplaythree = todisplaythree(array('table'=>$table_name,'originator'=>$projectdetails['created_by'],'encode'=>'no','id'=>$projectdetails['id'],'dis_query'=>'yes','dis_timeline'=>'yes','dis_comments'=>'yes'));
        ?>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<?php } ?>
