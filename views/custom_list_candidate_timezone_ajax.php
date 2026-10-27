<?php
include_once '../dbconfig.php';
include_once '../comm.php';
$candidate_details= json_decode(base64_decode($_POST['option']),1);
$url = API."?mode=getcandidateschedule&method=post";
$candidate_details['ajax'] ="Y";
$getresponse = file_post_contents($url, $candidate_details, 'update');
$getreturnresponse = json_decode($getresponse,1);
$cis_bookslot = $getreturnresponse['candidate_interview_schedule_all'];

foreach($cis_bookslot as $cisbookslot){
    $bookslot = gettTimeZone($cisbookslot['interviewer_schedule_datetime'],$_POST['timezone']);
    $booktimeslot[$bookslot['timezonedatetimecheck']] =$bookslot['timezonedatetimecheck'];
}

$minutslot = CANDIDATE_TIMESLOT_MINUTS*60;
if($_POST['schedule_date'] == date('Y-m-d')){
    $startdatetime = date('Y-m-d'.' 10:00:00');
    $enddatetime = date('Y-m-d'.' 20:00:00');
}else{
    $startdatetime = date($_POST['schedule_date'].' 10:00:00');
    $enddatetime = date($_POST['schedule_date'].' 20:00:00');
}
$starttmezone = gettTimeZone($startdatetime,$_POST['timezone']);
$endtmezone = gettTimeZone($enddatetime,$_POST['timezone']);
$currnttmezone = gettTimeZone('',$_POST['timezone']);
$open_time = strtotime($starttmezone['timezonedatetime']);
$close_time = strtotime($endtmezone['timezonedatetime']);
$now = strtotime($currnttmezone['timezonedatetime']);
$output = "";

for($i=$open_time; $i<$close_time; $i+=$minutslot){
    if (strtotime($_POST['schedule_date']) <= strtotime(date('d-m-Y')) && $i<=$now) { continue;
}
    $function = "timeSelection(".$i.")";
    if(!isset($booktimeslot[date('YmdHiA',$i)])){
        $output .= '<button type="button" class="btn p-0 border-0 bg-transparent form-label label label-lg label-primary mt-1 timeempty time-'.$i.'" onclick="'.$function.'" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"><span class="icon-'.$i.' emptyicon"></span>'.date("h:i A",$i).'</button>';
    }
}
$officetimeon = $output !=""?"0":"1";
$returnarray = array('starttimezone'=>$starttmezone,'endtimezone'=>$endtmezone,'currnttmezone'=>$currnttmezone,'timezonelabel'=>$output,'officetimeon'=>$officetimeon,'bookslot'=>$booktimeslot);
echo json_encode($returnarray);
