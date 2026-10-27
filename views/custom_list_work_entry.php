<?php

$datakey["where"] = array('company_id'=> $_SESSION['company_id'], 'is_active'=>'0');
$dkey = urlencode(json_encode($datakey));
$url = API."?mode=gettabledetailswithconditions&table=attendance_key_types&data=".$dkey;
$cudata = callAPI($url);
$key_details = json_decode($cudata,1);
$datemonthyeare = explode("-",$_POST['att_month_year']);
$mnth = ($datemonthyeare[1]!="")?$datemonthyeare[1]:date("m");
$yearr =($datemonthyeare[0]!="")?$datemonthyeare[0]:date("Y");
$workingentryurl = API."?mode=getemployeeworkingentrys&month=".$mnth."&year=".$yearr."&monthstartday=".$_SESSION['config']['default_payroll_day']."&emplist=".urlencode($_POST['task_for']);
$workingentrys = callAPI($workingentryurl);
$workingentrys = json_decode($workingentrys,1);

$dates = $workingentrys['date'];
$timesheets = $workingentrys['timesheet'];
$emplist = $workingentrys['emplist'];
$discrepancy = $workingentrys['discrepancy'];
if($_POST['recordtype']=="discrepancy"){
    $emplist=array_intersect_key($emplist,$discrepancy);
}

function worktyme_percentage($num_amount, $num_total) {
  if($num_total != 0 && $num_total !=""){
    $num_total = 1;
  }
  $count1 = $num_amount / $num_total;
  $count2 = $count1 * 100;
  $count = number_format($count2, 0);
  return $count;
}
$partial_color = filterArrayValue($key_details,'key_short_code','R');
$partial_color = $partial_color[0]['color_code'];

$firstdate = array_slice((array)$dates,0,1)[0];
$lastdate = array_slice((array)$dates,-1,1)[0];

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
function edittimesheettime(id,timesheet_date,emp_id,workinghrs,emp_name){

 jQuery.ajax({
  url: "views/custom_insert_work_entry_popup.php",
  data:'id='+id+_2'&timesheet_date='+timesheet_date+'&emp_id='+emp_id+'&workinghrs='+workinghrs+'&emp_name='+emp_name,
  type: "POST",
  success:function(data){
      $('#workentrypop').html(data);
       $("#timesheet_edit_modal").modal('show');
  },
  error:function (){}
  }).done(function(){
});

}

</script>

<div class="d-flex flex-column-fluid j-content ">
    <div class="container-fluid j-pro">
        <div class="card card-custom mb-5 ">
            <div class="card-header">
                <h5>Work Entry</h5>
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
                                        <input aria-label="Att Month Year" class="form-control" type="month" name="att_month_year" value="<?php echo $yearr."-".$mnth?>"  id="att_month_year"  max="2020-03-30"/>

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
                <button type="button" class="btn waves-effect waves-light hor-grd btn-grd-info" onclick="showpopup();"><i class="ti-user"></i> Select Employees</button>
            </div>
            <div class="col-lg-2"> <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="ti-filter"></i> Apply Search</button></div>
        </form>
        <div class="row mb-5">
            <div class="mt-3 text-right">
                <?php
                foreach ($key_details as $attname) {
                    ?>
                    <button class="btn  waves-effect waves-light text-white" style="background-color: <?php echo strtolower($attname['color_code']);?>"><?php echo $attname['key_short_code'];?></button>
                    <span class="m-r-20"><?php echo $attname['key_name'];?></span>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="table-responsive mt-4 work-entry">
        <table class="table table-bordered">
            <tr>
                <th scope="col" rowspan="2">Work Entries</th>
                <th scope="col" colspan="31" >

                    <?php echo date('d-M-y',strtotime($firstdate))." To ".date('d-M-y',strtotime($lastdate)); ?>
                </th>
            </tr>
            <tr>
                <?php foreach($dates as $date1){ ?>
                    <td colspan=""><?php echo date('d',strtotime($date1)); ?></td>
                <?php } ?>

            </tr>
            <?php foreach($emplist as $keye => $elist){ ?>
 <tr>
                    <th scope="col">


<div class="align-middle float-left ml-2">
<?php if($elist['image']!=""){?>
                      <img src="uploads/all/<?php echo $elist['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                    <?php } else { ?>
                      <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($elist['display_name']); ?></span>
                    <?php } ?>
                      <div class="d-inline-block">
                    <a href="#!"><?php echo $elist['display_name']; ?></a>
                  </div>

 </div>

                    </th>
                    <?php foreach($dates as $date2){
                        $billable_hrs = $timesheets[$keye][$date2]['billable_hrs']!=""?$timesheets[$keye][$date2]['billable_hrs']:0;
                        $workingday = $timesheets[$keye][$date2]['day'];
                        $minworkinghrs = $workingentrys['min_working_hours'] ==""?"8":$workingentrys['min_working_hours'];
                        $workingper = worktyme_percentage($billable_hrs,$minworkinghrs);
                        $workingtypecolor = $workingday == 'W'?$partial_color:$timesheets[$keye][$date2]['color_code'];
                        $timesheetid = $timesheets[$keye][$date2]['timesheetid'];
                        ?>
                        <td colspan="" class="text-white" <?php if($timesheetid!=""){ ?>onclick="edittimesheettime('<?php echo $timesheetid; ?>','<?php echo $date2; ?>','<?php echo $keye; ?>','<?php echo $workingentrys['min_working_hours_full']; ?>','<?php echo $elist['display_name']; ?>')"<?php
} ?> align="center"
                            style="background: linear-gradient(to right, rgb(46 216 182) <?php echo $workingper; ?>%, <?php echo $workingtypecolor;  ?> <?php echo $workingper-100; ?>%);" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $workingday;  ?></td>
                        <?php } ?>
                    </tr>

                <?php } ?>
            </table>
        </div>
    </div>
    <div class="modal fade" id="timesheet_edit_modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content " id="workentrypop">

            </div>
        </div>
    </div>

