<?php
if(SHIFT_CHECK != 1){
  $genral_shift = 1;
}
$datemonthyeare = $_POST['att_month_year'] !=""?explode("-",$_POST['att_month_year']):explode("-",$_GET['SUBDATE']);
$mnth = ($datemonthyeare[1]!="")?$datemonthyeare[1]:date("m");
$yearr =($datemonthyeare[0]!="")?$datemonthyeare[0]:date("Y");
$yearmonth = $yearr."-".$mnth;
$timesheetstartdate = $_SESSION['config']['default_timesheet_date']==""?'01':$_SESSION['config']['default_timesheet_date'];
$workingentryurl = API."?mode=getemployeeworkattendances&month=".$mnth."&year=".$yearr."&monthstartday=".$timesheetstartdate."&emplist=".urlencode($_POST['task_for'])."&emp_id=".$_SESSION['emp_id']."&admin_type=".$_SESSION['admin_type']."&user_email=".$_SESSION['user_email'];
$workingentrys = callAPI($workingentryurl);
$workingentrys = json_decode($workingentrys,1);
$monthworkignday = $workingentrys['monthworkingdays'];
$actualworkingday = $workingentrys['actualworkingdays'];
$work_attendance_logs = $workingentrys['work_attendance_logs'];
$key_details = $workingentrys['attendance_key_types'];
foreach($work_attendance_logs as $walog){
  $work_attendance_log[$walog['type']] = $walog;
}
$dates = $workingentrys['date'];
$timesheets = $workingentrys['timesheet'];
$emplist = $workingentrys['emplist'];
$normalshifts = $workingentrys['shifts'][$genral_shift];
if($_POST['recordtype']=="discrepancy"){
  $emplist=array_intersect_key((array)$emplist,(array)$discrepancy);
}
$dates = $workingentrys['date'];
function worktyme_percentage($num_amount, $num_total) {
  if($num_total != 0 && $num_total !=""){
    $num_total = 1;
  }
  $count1 = $num_amount / $num_total;
  $count2 = $count1 * 100;
  $count = number_format($count2, 0);
  return $count;
}

$firstdate = array_slice((array)$dates,0,1)[0];
$lastdate = array_slice((array)$dates,-1,1)[0];

$valuedisplaytype = $workingentrys['valuedisplaytype'];
if($_SESSION['success_messages'] !="" && isset($_SESSION['success_messages'])){
    $success_messages = $_SESSION['success_messages'];
    unset($_SESSION['success_messages']);
}

if($_SESSION['insert_msg'] !=""){
      $msgcode = $_SESSION['insert_msg'];
      $_SESSION['insert_msg'] = "";
      ?>
      <script type="text/javascript">
          document.onreadystatechange = function(){
              if(document.readyState == "complete") {
                  tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
              }
          }
      </script>
  <?php
  }
?>
<script type="text/javascript">
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
//         url: "views/custom_insert_filters_attendance.php",
//         data:'filter_id='+filter_id,
//         type: "GET",

function showpopup(){
  var filter_id = document.getElementById('filtertbl_rowid').value;
   jQuery.ajax({
    url: "views/custom_insert_filters.php",
//url: "views/custom_filter_bulktransfer.php",
data:'filter_id='+filter_id+'&tablename=project_timesheets&tr_id=tr_id',
type: "GET",
success:function(data){
  $('#dynamic_popup1').html(data);
  $('#kt_modal_dynamic_popup').modal("show");
},
error:function (){}
}).done(function(){
});
}

function getnamelist(emplist){
  var selectednames = emplist;
  var attendancemonth = document.getElementById('att_month_year').value;
  var monthstartday = document.getElementById('monthstartday').value;
  var monyr = attendancemonth.split("-");
  var att_year = monyr[0];
  var att_month = monyr[1];

  window.location = "index.php?option=COM_LIST_WORK_ENTRY&month="+att_month+"&year="+att_year;
  $('#formsubmit').submit();

}
function edittimesheettime(id,timesheet_date,emp_id,workinghrs,emp_name,svaetype,month){
  var year = '<?php echo $yearr; ?>';
  jQuery.ajax({
    url: "views/custom_insert_work_entry_popup.php",
    data:'id='+id+_2'&timesheet_date='+timesheet_date+'&emp_id='+emp_id+'&workinghrs='+workinghrs+'&emp_name='+emp_name+'&attendancepop=1&year='+year+'&svaetype='+svaetype+"&month="+month,
    type: "POST",
    success:function(data){
      $('#workentrypop').html(data);
      $("#timesheet_edit_modal").modal('show');
    },
    error:function (){}
  }).done(function(){
  });

}

function submitattendance(month,year,monthstartday){
  $('.submitattendancess').prop('disabled', true);
      swal({
          title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
          text: "<?php echo $_SESSION['error_message']['9034']; ?>",
          type: "info",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Submit",
          cancelButtonText: "Don't Submit",
          closeOnConfirm: true,
          closeOnCancel: true
        },
        function(isConfirm) {
          if(isConfirm) {
            $.ajax({
              url: "ajax.php",
              data:'page=insertemployeeworkattendances&month='+month+'&year='+year+'&monthstartday='+monthstartday,
              type: "POST",
              success:function(respons){
                console.log(respons);
                location.reload();
              },
              error:function (){}
            });
        }else{
            $('.submitattendancess').prop('disabled', false);
        }
    });
}

function donloadall(){
      var att_month_year = $("#att_month_year").val();
      $.ajax({
        url: "ajax.php",
        data:'page=insertworkattendancelogs&month_year='+att_month_year,
        type: "POST",
        success:function(respons){
          console.log(respons);
          $("#download_icon").html('<i class="ti-check"></i>');
          $("#import_btn").removeClass("disabled");
     //download_btn


        },
        error:function (){}
      });

      var url = 'custom_download.php?page=work_attendances&data=';
      window.open(url, '_blank').focus();
}
function getWorkAttendanceLogs(yearmonth){
     $.ajax({
        url: "views/custom_list_work_attendance_logs_popup.php",
        data:'yearmonth='+yearmonth,
        type: "POST",
        success:function(respons){
          console.log(respons);
          $('#popupm').html(respons);
          $('#kt_modal_KTDatatable_remotem').modal("show");

        },
        error:function (){}
      });
}

</script>
<style>

<style>
  th {
        background: #242424;
        position: sticky;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            color: white;
    }

    thead tr th {

        top: 0;
    }

    tbody tr th {

        top: 1.5em;
    }
     tr.first th {

        top: 0;
    }

    tr.second th {

        top: 1.5em;
    }
    </style>

</style>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid">
    <?php if($success_messages !="" ){?>
         <div class="alert alert-info alert-dismissible">
              <strong><?php echo $success_messages; ?> </strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
    <?php } ?>
    <div class="card card-custom mb-5 ">
      <div class="card-header">
        <h5>Work Attendance</h5>
        <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
            <i class="icofont icofont-reply"></i>
            Back
          </a>
        </div>
      </div>
      <div class="p-4">
        <form class="form" id="formsubmit" method="POST">
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-3">
              <div class="row">
                <div class="ml-12">
                  <div class="form-group row">
                    <div class="col-10">
                      <input type="hidden" name="monthstartday" id="monthstartday" value="<?php echo $_SESSION['config']['default_payroll_day']; ?>">
                      <input aria-label="Att Month Year" class="form-control" type="month" name="att_month_year" value="<?php echo $yearr."-".$mnth?>"  id="att_month_year" />

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4"> <div class="dropdown mt-1">
              <div class="form-radio">
                <div class="radio radio-inline">
                  <label class="form-label">
                    <input aria-label="Recordtype" type="radio" name="recordtype" <?php echo $_POST['recordtype']=='discrepancy'?"checked":"";?> class="d-none" value="discrepancy"/>
                    <i class="helper"></i>Discrepancy Records
                  </label>
                </div>
                <div class="radio radio-inline">
                  <label class="form-label">
                    <input aria-label="Recordtype" type="radio" name="recordtype" <?php echo $_POST['recordtype']!='discrepancy'?"checked":"";?> value="all" class="d-none"/>
                    <i class="helper"></i>All Records
                  </label>
                </div>
              </div>
            </div></div>
            <div class="col-lg-2">
              <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $_POST['filtertbl_rowid']; ?>">
              <input type="hidden" name="task_for" id="filter" value='<?php echo $_POST['task_for']; ?>'>
              <input type="hidden" name="attendancemonth" id="attendancemonth" value="<?php echo date('Y-m-d');?>">
              <input type="hidden" name="process_type" id="process_type" value="1">

            </div>
            <div class="col-lg-2"> <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="ti-filter"></i> Apply Search</button></div>
          </form>
          <div class="row mb-5">
            <div class="col-lg-3"></div>
            <div class="col-lg-9 mt-3">
              <?php
              foreach ($key_details as $attname) {
                ?>
                <button class="btn  waves-effect waves-light text-white mt-2" style="background-color: <?php echo strtolower($attname['color_code']);?>"><?php echo $attname['display_value'];?></button>
                <span class="m-r-15"><?php echo $attname['key_name'];?></span>
              <?php } ?>
            </div>
          </div>


        </div>
        <div class="row">
            <div class="col-lg-5">
          </div>
          <div class="col-lg-7 text-right">
            <?php
              $btndisable = "";
              if(isset($work_attendance_log['S'])){
                $btndisable = "disabled";
              }
            ?>
            <button type="submit" class="btn btn-primary waves-effect waves-light <?php echo $btndisable; ?>" id="download_btn"  onclick="donloadall()"   data-container="body" data-toggle="tooltip" data-placement="bottom" title=""> <span id="download_icon"><?php echo isset($work_attendance_log['D'])?'<i class="ti-check"></i>':'<i class="ti-cloud-down"></i>'; ?></span> Download CSV <span class="badge bg-warning text-white">1</span></button>

            <a href="index.php?option=COM_INSERT_SWAP_IN_OUT&TYPE=withoutattendancesetting&yearmonth=<?php echo $yearmonth; ?>" type="submit" class="btn btn-primary waves-effect waves-light <?php echo isset($work_attendance_log['D'])?'':'disabled'; ?> <?php echo $btndisable; ?>" id="import_btn"  data-container="body" data-toggle="tooltip" data-placement="bottom" title=""> <?php echo isset($work_attendance_log['I'])?'<i class="ti-check"></i>':'<i class="ti-cloud-up"></i>'; ?> Upload <span class="badge bg-warning text-white">2</span></a>
            <?php $Rrid = deocdeId("id:".$work_attendance_log['I']['import_swap_in_out_id']); ?>

            <a href="index.php?option=COM_INSERT_SWAP_IN_OUT&TYPE=withoutattendancesetting&yearmonth=<?php echo $yearmonth; ?>&Rid=<?php echo $Rrid; ?>"  class="btn btn-primary waves-effect waves-light <?php echo isset($work_attendance_log['I'])?'':'disabled'; ?> <?php echo $btndisable; ?>" id="review_btn">  <?php echo isset($work_attendance_log['R'])?'<i class="ti-check"></i>':'<i class="ti-cloud-down"></i>'; ?> Review <span class="badge bg-warning text-white">3</span></a>

           <button type="submit" class="btn btn-primary waves-effect waves-light  submitattendancess <?php echo $btndisable; ?>"  onclick="submitattendance('<?php echo $mnth; ?>','<?php echo $yearr; ?>','<?php echo $timesheetstartdate; ?>')"   data-container="body" data-toggle="tooltip" data-placement="bottom" title="Please check the data before submit, Once you submit the data you cannot make any changes here."> <?php echo isset($work_attendance_log['S'])?'<i class="ti-check"></i>':'<i class="ti-thumb-up"></i>'; ?> Submit Attendance <span class="badge bg-warning text-white">4</span> </button>

           <button type="submit" class="btn btn-info waves-effect waves-light <?php echo count((array)$work_attendance_log) =='0'?"disabled":""; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Attendance Logs" onclick="getWorkAttendanceLogs('<?php echo $yearmonth; ?>')"><i class="ti-time"></i> </button>


          <div class=" mt-4 work-entry">
          <table class="table table-bordered">
                <tr>
              <th scope="col" rowspan="2">Work Entries</th>
              <th scope="col" colspan="32" class="f-14" >
                <?php echo date('d-M-y',strtotime($firstdate))." To ".date('d-M-y',strtotime($lastdate)); ?>
              </th>
            </tr>
            <tr style="position: sticky; top: 0; background-color: #ffffff;">
            <td>P</td>
              <td>A</td>
              <?php  foreach($dates as $date1){ ?>
                <td colspan="" class="f-14"><?php echo date('d',strtotime($date1)); ?></td>
              <?php } ?>
            </tr>

        <tbody>
            <?php $mk=1;  $dis_dates['slno'] = "SLNO"; $dis_dates['emp_id'] = "emp_id"; $dis_dates['name'] = "Name";
            foreach($emplist as $keye => $elist){ //print_r($elist); ?>
             <tr>
              <th scope="col">

                <div class="align-middle float-left ml-2">
                <span class="f-12"><?php echo $mk; ?></span>
                  <?php if($elist['image']!=""){?>
                    <img src="uploads/all/<?php echo $elist['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                  <?php }else{ ?>
                    <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($elist['display_name']); ?></span>
                  <?php } ?>
                  <div class="d-inline-block">
                    <p class="f-10"><?php echo $elist['emp_code']." - ".$elist['display_name']; ?></p>
                    <?php
                    $download[$mk]['emp_id'] = $keye;
                    $download[$mk]['name'] = str_replace("'","",$elist['emp_code']." - ".$elist['display_name']); ?>
                  </div>
                </div>
              </th>
              <td class=" f-12 f-w-600 line-height26"><?php echo $monthworkignday[$keye];?></td>
              <td class=" f-12 f-w-600 line-height26"><?php echo $actualworkingday[$keye];?></td>
              <?php $dl=0; foreach($dates as $date2){
                $shiftlicheck = $shiftlocheck = "";
                if(SHIFT_CHECK != 1){
                  $officestart = $normalshifts['clock_in'];
                  $officestart = '09:30:00';
                  $officeclose = $normalshifts['clock_out'];
                  $officeclose = "19:10:00";
                  if($officestart <= $timesheets[$keye][$date2]['clock_in'] && $timesheets[$keye][$date2]['clock_in'] !=""){
                    $shiftlicheck = "LI";
                  }
                  if($officeclose >= $timesheets[$keye][$date2]['clock_out'] && $timesheets[$keye][$date2]['clock_out'] !=""){
                     $shiftlocheck = "LO";
                  }
                }
                $workingday = $timesheets[$keye][$date2]['day'];
                $display_value = $timesheets[$keye][$date2]['display_value'];
                $workingtypecolor = "background-color : ".$timesheets[$keye][$date2]['color_code'];
                if($timesheets[$keye][$date2]['strthalfday'] =='0' || $timesheets[$keye][$date2]['endthalfday']=='0' || $display_value=='Z'){
                  $workingtypecolor = "background : linear-gradient(to right, ".$timesheets[$keye][$date2]['working_color']." 50%, ".$timesheets[$keye][$date2]['color_code']." 50%)" ;
                  $workingtypecolor = "background : linear-gradient(to right, ".$timesheets[$keye][$date2]['working_color']." 50%, #ff005c 50%)" ;

                }
                ?>
                <td colspan="" class="position-relative LI-LO f-12 f-w-600 line-height26 empid<?php echo $keye; ?>date<?php echo $date2; ?>" onclick="edittimesheettime('<?php echo $timesheetid; ?>','<?php echo $date2; ?>','<?php echo $keye; ?>','<?php echo $workingentrys['min_working_hours_full']; ?>','<?php echo $elist['display_name']; ?>','<?php echo $valuedisplaytype; ?>','<?php echo $mnth; ?>')" align="center"
                  style="<?php echo $workingtypecolor; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                  <?php
                      echo $display_value;
                      $download[$mk][$dl] =$display_value;
                      $dis_dates[$dl] = $date2;
                  ?>
                  <span class="position-absolute"><?php echo $shiftlicheck; ?></span>
                  <span class="position-absolute"><?php echo $shiftlocheck; ?></span></td>
                <?php $dl++;
} ?>
              </tr>
            <?php $mk++;
} ?>
            </tbody>
          </table>
          <input type="hidden" id="download" value="<?php echo urlencode(json_encode($download,1));  $_SESSION['download_data'] = $download;  $_SESSION['head_data'] = $dis_dates; ?> "/>
        </div>
      </div>
      <div class="modal fade" id="timesheet_edit_modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content " id="workentrypop">

          </div>
        </div>
      </div>
