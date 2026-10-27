<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$datakey["where"] = array('company_id'=> $_SESSION['company_id'], 'is_active'=>'0');
$dkey = urlencode(json_encode($datakey));
$url = API."?mode=gettabledetailswithconditions&table=attendance_key_types&data=".$dkey;
$cudata = callAPI($url);
$key_details = json_decode($cudata,1);
foreach($key_details as $key_details){
    $workcolorcode[$key_details['key_short_code']] = $key_details['color_code'];
}

?>

<script type="text/javascript">
    function submitrom(){
        var sub = $("#popformsubmit").serialize();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=edittimesheetbillablehrs&'+sub,
            success: function(response) {
                console.log(response);
                $("#timesheet_edit_modal").modal('hide');
                location.reload();
            }
        });
    }

    function submitromattendace(){
        var leavetype = $('input[name="presentcheck"]:checked').val();
        var availablelave =  $('#availablelave').val();
        var leave_days =  $('#leave_days').val();
        var leave_type_id = $(".leave_type_id").val();
        if(leave_type_id =="-1" && leavetype == '2'){
            $("#notedaysremaining").text("*Please Select Leave Category");
            return true;
        }
        if((Number(availablelave) < Number(leave_days)) && leavetype == '2'){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo $_SESSION['error_message'][8008]; ?>',
            })
        }else{
            var sub = $("#popupformattendace").serialize();
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: 'page=inserttempattendaceform&'+sub,
                success: function(response) {
                    var respons = JSON.parse(response);
                    if(respons['return'].presentcheck == '1'){


                    $("#timesheet_edit_modal").modal('hide');
                    jQuery.each(respons['leavedate'], function(i, val){
                            $(".empid"+respons['return'].emp_id+"date"+val).css("background-color","");
                            $(".empid"+respons['return'].emp_id+"date"+val).css("background","");
                            $(".empid"+respons['return'].emp_id+"date"+val).css("background-color","<?php echo $workcolorcode['A']; ?>");
                            $(".empid"+respons['return'].emp_id+"date"+val).html("A");
                        });
                    }else if(respons['return'].presentcheck == '3'){
                        $(".empid"+respons['return'].emp_id+"date"+respons['return'].attendance_date).css("background-color","");
                        $(".empid"+respons['return'].emp_id+"date"+respons['return'].attendance_date).css("background","");
                        $("#timesheet_edit_modal").modal('hide');
                        $(".empid"+respons['return'].emp_id+"date"+respons['return'].attendance_date).css("background-color","<?php echo $workcolorcode['W']; ?>");
                        $(".empid"+respons['return'].emp_id+"date"+respons['return'].attendance_date).html("P");
                    }else{
    $("#timesheet_edit_modal").modal('hide');
    var lastdate =  respons['leavedate'].length-1;
    console.log("maxid - "+lastdate);
    jQuery.each(respons['leavedate'], function(i, val){
        $(".empid"+respons['return'].emp_id+"date"+val).css("background-color","");
        $(".empid"+respons['return'].emp_id+"date"+val).css("background","");
        if(respons['return'].leave_type_id == '6'){
            if((respons['return'].start_half_day == '0' && i == '0') || (respons['return'].end_half_day == '0' && i == lastdate)){
                console.log("fisrt half");
                $(".empid"+respons['return'].emp_id+"date"+val).css("background","linear-gradient(to right, <?php echo $workcolorcode['W']; ?> 50%, <?php echo $workcolorcode['S']; ?> 50%)");
                $(".empid"+respons['return'].emp_id+"date"+val).html("S");
            }else{
                $(".empid"+respons['return'].emp_id+"date"+val).css("background-color","<?php echo $workcolorcode['S']; ?>");
                $(".empid"+respons['return'].emp_id+"date"+val).html("S");
                console.log("full half");
            }
        }else{
            if((respons['return'].start_half_day == '0' && i == '0') || (respons['return'].end_half_day == '0' && i == lastdate)){
                console.log("half");
                $(".empid"+respons['return'].emp_id+"date"+val).css("background","linear-gradient(to right, <?php echo $workcolorcode['W']; ?> 50%, <?php echo $workcolorcode['V']; ?> 50%)");
                $(".empid"+respons['return'].emp_id+"date"+val).html("V");
            }else{
                console.log("full");
                $(".empid"+respons['return'].emp_id+"date"+val).css("background-color","<?php echo $workcolorcode['V']; ?>");
                $(".empid"+respons['return'].emp_id+"date"+val).html("V");
            }
        }
    });
}

}
});
        }
    }

    function checkabsentorpresent(checkclass){
        if(checkclass == "0"){
            $(".absentdaterange").hide();
            $(".leaveaployeeform").show();

        }else if(checkclass == "1"){
            $(".leaveaployeeform").hide();
            $(".absentdaterange").show();

        }else{
            $(".leaveaployeeform").hide();
            $(".absentdaterange").hide();
        }
    }

    function selectleavetype(val,id,year,month){

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=getavailableleaveemployee&leavetype='+val+'&emp_id='+id+'&year='+year+'&month='+month,
            success: function(response){
                console.log(response);
                var respons = JSON.parse(response);
                if(respons.available_leaves == '0'){
                    $("#notedaysremaining").text("<?php echo $_SESSION['error_message'][8008]; ?>");

                }else{
                    $("#notedaysremaining").text("*Available Leave Is : "+respons.available_leaves);
                }
                if(respons.halfday == '0'){
                    $('input[name="start_half_day"]').attr("disabled", false);
                    $('input[name="end_half_day"]').attr("disabled", false);
                }else{
                    $('input[name="start_half_day"]').attr("disabled", true);
                    $('input[name="end_half_day"]').attr("disabled", true);
                }

                $("#availablelave").val(respons.available_leaves);
            }
        });

    }

    function tocheckdays(){
        var date1 =  $('#kt_datepicker_31').val();
        var date2 =  $('#kt_datepicker_32').val();
        var availablelave =  $('#availablelave').val();
        if(date1!="" && date2!=""){
            $.ajax({
                url: "ajax.php",
                data:'page=datediffval&date1='+date1+'&date2='+date2,
                type: "POST",
                success:function(data){
                    $('#leave_days').val(data);
                    console.log(availablelave+"- "+data);
                    if(Number(availablelave) < Number(data)){
                        Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo $_SESSION['error_message'][8008]; ?>',
            })
                    }
                },
                error:function (){}
            });
        }
    }

</script>

<?php

$leavess["where"] = array('is_active'=> '0','attendance_date'=>$_POST['timesheet_date'],'employee_id'=>$_POST['emp_id']);

$leavesencode = urlencode(json_encode($leavess));
$leaevurl = API."?mode=gettabledetailswithconditions&table=temp_attendance_details&data=".$leavesencode;
$leavedetails = callAPI($leaevurl);
$leavedetails1 = json_decode($leavedetails,1);
$leavedetails = $leavedetails1[0];

$url = API."?mode=gettimesheetworkentryedit&id=".urlencode($_REQUEST['id']);
$cudata = callAPI($url);
$key_details = json_decode($cudata,1);
$attendanceurl = API."?mode=getattendancedata&emp_id=".$_REQUEST['emp_id'].'&date='.$_REQUEST['timesheet_date'];
$attendancedata1 = callAPI($attendanceurl);
$attendancedata = json_decode($attendancedata1,1);

$attendancedata = count((array)$attendancedata) != 0?$attendancedata:$leavedetails1;
$lastkey = count((array)$attendancedata)-1;
$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=leave_categories&data=".$vdata;
$configesponse = callAPI($vurl);
$cate = json_decode($configesponse,1);

function houreConvertRounvalue($fulltime){
    $hour=0;
    $minute=0;
    $splitTime1= explode(":",$fulltime);
    $hour = $splitTime1[0];
    $minute = $splitTime1[1];
    $hour = $hour + $minute/60;
    $minute = $minute%60;
    return converttwodigithourse(intval($hour).":".$minute);
}
function converttwodigithourse($time){
    $splitTime1= explode(":",$time);
    $hour = strlen($splitTime1[0]) =='1'?'0'.$splitTime1[0]:$splitTime1[0];
    $minute = strlen($splitTime1[1]) =='1'?'0'.$splitTime1[1]:$splitTime1[1];
    return $hour.":".$minute;
}

$h = $i =$h1 = $i1 = 0;
foreach($attendancedata as $key=>$attendresultf){
    $incres = $key+1;
    if($attendresultf['clock_in']!="" && $attendresultf['clock_out']!=""){
        $time1 = new DateTime($attendresultf['clock_in']);
        $time2 = new DateTime($attendresultf['clock_out']);
        $interval = $time1->diff($time2);
        $h += $interval->format('%h');
        $i +=$interval->format('%i');
    }
    if($attendancedata[$key]['clock_out']!="" && $attendancedata[$incres]['clock_in']!=""){
        $time3 = new DateTime($attendancedata[$key]['clock_out']);
        $time4 = new DateTime($attendancedata[$incres]['clock_in']);
        $interval = $time3->diff($time4);
        $h1 += $interval->format('%h');
        $i1 +=$interval->format('%i');
    }

}
$breackhrs =  houreConvertRounvalue($h1.':'.$i1);
$workinghrs = houreConvertRounvalue($h.':'.$i);
$minworkinghrs = $_REQUEST['workinghrs'] !=""?$_REQUEST['workinghrs']:'8:00:00';
?>
<div class="modal-header">
    <h5 class="modal-title">Attendance Info </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" >
    <div class="row">
        <div class="col-lg-8 ">
            <div class="row">
                <div class="col-xl-7 col-md-12">
                    <div class="card latest-update-card">
                        <div class="card-header">
                            <h5>Attendance Info<span class="form-label badge bg-primary p-2 m-l-5"><i class='ti-user'></i> <?php echo $_REQUEST['emp_name'];?> </span></h5>
                        </div>
                        <div class="card-block mt-4">
                            <div class="card proj-progress-card p-3">
                                <div class="row">
                                    <h6>Punch In At</h6>
                                    <h5 class="m-b-30 f-w-700"><?php if($attendancedata[$lastkey]['clock_out']!=''){ echo date('d M Y h:i:A',strtotime($attendancedata[0]['attendace_date'].' '.$attendancedata[0]['clock_in']));
}else{ echo 'Punch In Time Missing';
} ?></h5>
                                </div>
                            </div>
                            <div class="text-center">
                                <div data-label=" <?php echo $workinghrs; ?> Hrs" class="radial-bar custom-radial-bar radial-bar-0 radial-bar-lg radial-bar-info f-16">
                                </div>
                            </div>
                            <div class="card proj-progress-card p-3">
                                <div class="row">
                                    <h6>Punch Out At</h6>
                                    <h5 class="m-b-30 f-w-700"><?php if($attendancedata[$lastkey]['clock_out']!=''){ echo date('d M Y h:i:A',strtotime($attendancedata[$lastkey]['attendace_date'].' '.$attendancedata[$lastkey]['clock_out']));
}else{ echo 'Punch Out Time Missing';
}?></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="card proj-progress-card p-3 col-lg-6">
                                    <div class="row">
                                        <h6>Brack</h6>
                                        <h5 class="m-b-30 f-w-700"><?php echo $breackhrs; ?> Hrs</h5>
                                    </div>
                                </div>
                                <div class="card proj-progress-card p-3 col-lg-6">
                                    <div class="row">
                                        <h6>OverTime</h6>
                                        <h5 class="m-b-30 f-w-700">
                                            <?php
                                            if(strtotime($workinghrs) > strtotime($minworkinghrs)){
                                                $time1 = new DateTime($workinghrs);
                                                $time2 = new DateTime($minworkinghrs);
                                                $interval = $time1->diff($time2);
                                                $hh = $interval->format('%h');
                                                $ii =$interval->format('%i');
                                                echo $over = $hh.':'.$ii.' Hrs';
                                            }else{
                                                echo "00:00 Hrs";
                                            }
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-md-12">
                    <div class="card latest-update-card">
                        <div class="card-header">
                            <h5>Activity</h5>
                        </div>
                        <div class="card-block custom-scroll" style="height:500px;">
                            <div class="">
                                <?php if(!empty($attendancedata)){
                                    ?><div class="latest-update-box">
                                        <?php $h = $i = $s= 0; foreach($attendancedata as $key=>$attendresult){ $cls=""; if($key=='0'){ $cls = "p-t-20";
} ?>
                                        <div class="row p-b-30 <?php echo $cls;?>">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="ti-shift-right bg-c-green update-icon"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6>Punch In At</h6></a>
                                                <p class="text-muted m-b-0"><?php echo $attendresult['clock_in']; ?></p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="ti-shift-left f-w-600 bg-c-green update-icon"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6>Punch out At</h6></a>
                                                <p class="text-muted m-b-0"><?php echo $attendresult['clock_out']==""?"--:--:--":$attendresult['clock_out']; ?></p>
                                            </div>
                                        </div>
                                        <?php
                                    } echo '</div>';
}else{ ?>
                                        <div class="new-task">
                                            <h4 class="p-10 text-center"><i class="fa fa-warning"></i> No Activity Available</h4>
                                        </div>
                                    <?php    }    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <?php if($_POST['attendancepop'] != '1'){ ?>
                    <div class="row">
                        <form class="kt-form kt-form--label-right" action="" id="popformsubmit" method="post">
                            <div class="card latest-update-card">
                                <div class="card-header">
                                    <h5>Time Sheets</h5>
                                </div>
                                <div class="form-group row p-l-15 p-r-15">
                                    <?php foreach($key_details as $key_detail){ ?>
                                        <div class="col-lg-12 mt-3">
                                            <span><?php echo $key_detail['task_name'];?></span>
                                            <input aria-label="Popbillablehorse[]" type="text" name="popbillablehorse[]"  class="form-control" value="<?php echo $key_detail['billable_hrs'];?>">
                                            <input type="hidden" name="poptimesheetid[]"  class="form-control" value="<?php echo $key_detail['id'];?>">
                                            <input type="hidden" name="popbillablehorseold[]" class="form-control" value="<?php echo $key_detail['billable_hrs'];?>">
                                            <input type="hidden" name="timesheet_date[]" class="form-control" value="<?php echo $key_detail['timesheet_date'];?>">
                                        </div>
                                    <?php }  ?>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary m-r-10 float-right mb-2"  onclick="submitrom()" >Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php } ?>
                <?php if($_POST['attendancepop'] == '1'){ ?>
                    <div class="row" >
                        <form class="kt-form kt-form--label-right" action="" id="popupformattendace" method="post">
                            <div class="card latest-update-card">
                                <div class="card-header">
                                    <h5>Employee Working Details</h5>
                                </div>
                                <div class="form-radio p-2">
                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                            <input aria-label="Presentcheck" type="radio" name="presentcheck" <?php echo $leavedetails['attendance_type']==""?"checked":""?> class="d-none" value="3" onclick="checkabsentorpresent('3')" onkeydown="">
                                            <i class="helper"></i>Present
                                        </label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                            <input aria-label="Presentcheck" type="radio" name="presentcheck"  <?php echo $leavedetails['attendance_type']=="1"?"checked":""?> class="d-none" value="1" onclick="checkabsentorpresent('1')" onkeydown="">
                                            <i class="helper"></i>Absent
                                        </label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                            <input aria-label="Presentcheck" type="radio" name="presentcheck" <?php echo $leavedetails['attendance_type']=="2"?"checked":""?> class="d-none" value="2" onclick="checkabsentorpresent('0')" onkeydown="">
                                            <i class="helper"></i>Leave
                                        </label>
                                    </div>
                                </div>
                            <div class="absentdaterange" style="display: <?php echo $leavedetails['attendance_type']=="1"?"":"none"?>">
                                    <div class="p-2 row">
                                        <div class="col-lg-12">
                                            <?php
                                            $formdate = $leavedetails['leave_from'] !=""?$leavedetails['leave_from']:$_POST['timesheet_date'];
                                            $todate = $leavedetails['leave_to'] !=""?$leavedetails['leave_to']:$_POST['timesheet_date'];
                                            ?>
                                            <span class="form-label">Start Date(s)</span>
                                            <input aria-label="Input field" type="text"  class="form-control datepicker"  value="<?php echo  fromDate($formdate); ?>"  name="absent_from"  id="kt_datepicker_absent_from" />
                                        </div>
                                    </div>
                                    <div class="p-2 row">
                                        <div class="col-lg-12">
                                            <span class="form-label">End Date(s)</span>
                                            <input aria-label="Input field" type="text" class="form-control datepicker"  value="<?php echo  fromDate($todate); ?>"  name="absent_to"  id="kt_datepicker_32" />
                                        </div>
                                    </div>
                                </div>
                                <div class="leaveaployeeform" style="display: <?php echo $leavedetails['attendance_type']=="2"?"":"none"?>">
                                    <div class=" p-2">
                                        <span class="form-label">Leave Category</span></br>
                                        <select aria-label="Leave Type" class="col-lg-12 form-control form-select js-example-basic-single leave_type_id"  name="leave_type_id" data-val="<?php echo $role['half_day']; ?>" onchange="selectleavetype(this.value,'<?php echo $_POST['emp_id']; ?>','<?php echo $_POST['year']; ?>','<?php echo $_POST['month']; ?>')">
                                            <option value="-1"> Select Any one</option>
                                            <?php foreach ($cate as $cat) {  ?>
                                                <option <?php if($cat['id'] == $leavedetails['leave_type_id'] ){ ?> selected <?php
} ?> value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                            <?php  } ?>
                                        </select>
                                        <p id="notedaysremaining"></p>
                                    </div>
                                    <div class="p-2 row">
                                        <div class="col-lg-7">
                                            <?php
                                            $formdate = $leavedetails['leave_from'] !=""?$leavedetails['leave_from']:$_POST['timesheet_date'];
                                            $todate = $leavedetails['leave_to'] !=""?$leavedetails['leave_to']:$_POST['timesheet_date'];
                                            ?>
                                            <span class="form-label">Start Date(s)</span>
                                            <input aria-label="Input field" type="text" onchange="tocheckdays()" class="form-control datepicker"  value="<?php echo  fromDate($formdate); ?>"  name="leave_from"  id="kt_datepicker_31" />
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="checkbox-fade fade-in-primary mt-4">
                                                <label class="form-label">
                                                    <input aria-label="Start Half Day" type="checkbox" value="0" name="start_half_day" <?php echo $leavedetails['start_half_day'] =="0"?"checked":""; ?>>
                                                    <span class="cr">
                                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                    </span>
                                                    <span class="f-w-600">Half Day</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-2 row">
                                        <div class="col-lg-7">
                                            <span class="form-label">End Date(s)</span>
                                            <input aria-label="Input field" type="text" onchange="tocheckdays()" class="form-control datepicker"  value="<?php echo  fromDate($todate); ?>"  name="leave_to"  id="kt_datepicker_32_2" />
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="checkbox-fade fade-in-primary mt-4">
                                                <label class="form-label">
                                                    <input aria-label="End Half Day" type="checkbox" value="0" name="end_half_day" <?php echo $leavedetails['end_half_day'] =="0"?"checked":""; ?>>
                                                    <span class="cr">
                                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                    </span>
                                                    <span class="f-w-600">Half Day</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <input type="hidden" name="emp_id" value="<?php echo $_POST['emp_id']; ?>">
                                    <input type="hidden" name="attendance_date" value="<?php echo $_POST['timesheet_date']; ?>">
                                    <input type="hidden" id="availablelave" value="0">
                                    <input type="hidden" id="leave_days" value="1">
                                    <button type="button"  <?php echo $_POST['svaetype']!="insert"?"disabled":""; ?> class="btn btn-primary m-r-10 float-right mb-2" onclick="submitromattendace()" >Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php } ?>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
        </div>

    </div>
    <script type="text/javascript">
        $( ".datepicker" ).datepicker({
            showOn: "both",
            dateFormat: date_format,
            changeMonth: true,
            changeYear: true
        }).next(".datepicker").html("<i class='ti-calendar'></i>");
    </script>
    <script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

    <script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
    </script>
    <script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
    <script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
