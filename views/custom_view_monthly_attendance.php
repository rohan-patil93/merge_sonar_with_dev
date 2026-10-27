<?php

    if(isset($_POST['date']) && $_POST['date']!=""){
        include_once '../includes/session.php';
        include_once '../dbconfig.php';
        include_once '../comm.php';
        $vdata = $_POST['date'];
    }else{
        $vdata = date('Y-m-d');
    }
    if(isset($_POST['employee_id']) && $_POST['employee_id']!=""){
        $employee_id = $_POST['employee_id'];
    }
    $ndj = date('j', strtotime($vdata));
    $tndj  = date('j');
    $vurl = API."?mode=togetdatewiseattendancedata&employee_id=".$employee_id."&date=".$vdata;
    $configesponse = callAPI($vurl);
    $data = json_decode($configesponse,1);
    $emp_data = $data['emp_data'];
    $atten = $data['main_attendancedata'];
    $attendancedata = $data['attendancedata'];
    $att_setting = $data['att_setting'];
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
    $minworkinghrs = $att_setting['maximum_hours_cap'] !=""?$att_setting['maximum_hours_cap']:'8:00:00';
?>
<script type="text/javascript">
    function toviewthedateclock(vdate,employee_id){
        jQuery.ajax({
            url: "views/custom_view_monthly_attendance.php",
            data:'employee_id='+employee_id+"&date="+vdate,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#viewalldatedataonce").html(data);
            },
            error:function (){}
        });
    }
</script>
<style>

        #date-list {
            display: block;


        }

        .date-item {
            background-color: #ffffff;
            color: #000000;
            border: 1px solid #ccc;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 18px;
            font-size: 12px;
            display: inline-block;
            vertical-align: top;
        }
        .date-item-top{
            background-color: #ffa500;
            border: 1px solid #ffa500;
            color: #FFFFFF;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 20px;
            display: inline-block;
            vertical-align: top;
        }
        .date-item-danger{
            background-color: #ff0000;
            border: 1px solid #ff0000;
            color: #FFFFFF;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 18px;
            font-size: 12px;
            display: inline-block;
            vertical-align: top;
        }
        .date-item-att{
            background-color: #008000;
            border: 1px solid #008000;
            color: #FFFFFF;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 20px;
            display: inline-block;
            vertical-align: top;
        }
        .date-item-gray{
            background-color: #dcdcdc;
            border: 1px solid #dcdcdc;
            color: #000000;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 18px;
            font-size: 12px;
            display: inline-block;
            vertical-align: top;
        }

</style>

<div class="card card-custom" id="viewalldatedataonce">
    <div class="card-header table-card-header">
        <h5>  My Attendances</h5>
    </div>
     <div class="card-body">
        <div class="row">
            <div class="col-lg-4 col-md-12">
                <div class="card latest-update-card">
                    <div class="card-header">
                        <h5>Calendar</h5>
                    </div>
                    <div class="card-block custom-scroll   mt-5 m-l-20 " >
                        <?php   $no_week = date('w', strtotime(date('Y')."-".date('m')."-01"));
                    $days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
                    $listdays = array('M', 'T', 'W', 'T', 'F', 'S', 'S');
                    $no_dd = date('d');

                    $allweekdays = json_decode($data['weekatt']['working_days'],1);
                    foreach($data['attedance'] as $attedances){
                      $attdate[] = $attedances['attendace_date'];
                      $dat = date('d',strtotime($attedances['attendace_date']));
                      $attday[] = $dat;

                    }

                    foreach($listdays as $listday){?>
                        <div class="date-item-top f-w-600 f-10"><?php echo $listday; ?></div>
                    <?php  } ?>
                    <div class="row"></div>

                        <?php $cal =1; for($i=1;$i<$no_week;$i++){ ?>
                        <div class="date-item f-10" style="border-color: #FFFFFF;"></div>
                        <?php  $cal++;
} ?>
                        <?php
                            for($i=1;$i<=$days_in_month;$i++){
                            $dacss = "date-item";

                            if(in_array($i,(array)$attday)){
                               $dacss = "date-item-att";
                            }
                             $wdate = date('Y')."-".date('m')."-".$i;
                             $ndate = date('Y-m-d',strtotime($wdate));
                             $wday = date('l',strtotime($wdate));
                             $cdate = "'".date('Y-m-d', strtotime($wdate))."'";


                            if( $no_dd>=$i && !in_array($i,(array)$attday) && in_array($wday, (array)$allweekdays) ){
                               $dacss = "date-item-danger";
                            }
                            if( !in_array($wday, (array)$allweekdays) || in_array($ndate,(array)$data['month_holidays']) ){
                               $dacss = "date-item-gray";
                            }
                            if( $tndj==$i){
                               $dacss = "date-item";
                            }
                            if( $ndj==$i){
                               $dacss = "date-item-att";
                            }
                            ?>
                            <div class="<?php echo $dacss; ?> f-10 cursor-pointer" onclick="toviewthedateclock(<?php echo $cdate.",".$employee_id; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $i; ?></div>
                            <?php
                                if($cal == 7){
                                    $cal = 0; ?>
                                <div class="row"></div>
                            <?php
                                }
                            ?>
                        <?php $cal++;
} ?>
                    </div>
                </div>
            </div>


            <div class="col-lg-5 col-md-12">
                <div class="card latest-update-card">
                    <div class="card-header">
                        <h5>Attendance Info<span class="form-label badge bg-primary p-2 m-l-5"><i class='ti-user'></i> <?php echo $emp_data['display_name'];?> </span></h5>
                    </div>
                    <div class="card-block mt-4">
                        <div class="card proj-progress-card p-3">
                            <div class="row">
                                <h6>Punch In At</h6>
                                <h5 class="m-b-30 f-w-700"><?php if($atten['clock_in']!=''){ echo date('d M Y h:i:A',strtotime($atten['attendace_date'].' '.$atten['clock_in']));
}else{ echo 'Punch In Time Missing';
} ?></h5>
                            </div>
                        </div>
                        <div class="text-center">
                            <div data-label=" <?php echo $workinghrs; ?> Hrs" class="radial-bar custom-radial-bar radial-bar-0 radial-bar-lg radial-bar-info f-16">
                            </div>
                        </div>
                        <div class="card proj-progress-card p-3 mt-4">
                            <div class="row">
                                <h6>Punch Out At</h6>
                                <h5 class="m-b-30 f-w-700"><?php if($atten['clock_out']!=''){ echo date('d M Y h:i:A',strtotime($atten['attendace_date'].' '.$atten['clock_out']));
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
            <div class="col-lg-3 col-md-12">
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
                                    <?php   }   ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
