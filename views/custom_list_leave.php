<?php
/**
Purpose : Getting Consumed Leave details
**/

$result = date("Y",strtotime(date('Y-m-d')));
for ($m=1; $m<=12; $m++) {
    $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
    $fetch_date = $result.$month;
    $vurl = API."?mode=togetempleavebalances&employee_id=".$_SESSION['userid']."&fetch_date=".$fetch_date;
    $configesponse = callAPI($vurl);

    $leave_array = json_decode($configesponse,1);
    $leave_data = $leave_array[0];
    if(empty($leave_data['consumed'])){
        $leave_data['consumed'] = 0;
    }
    $consumed_days[] = $leave_data['consumed'];
}
$consumed_leaves_in_current_year = implode(',',(array)$consumed_days);

$year = date('Y', strtotime(date('Y-m-d')));
$vdata3["where"] = array('is_active'=>'0','employee_id'=>$_SESSION['userid'],'year(status_date)'=>$year);
$vdata_3 = urlencode(json_encode($vdata3));
$vurl_3 = API."?mode=gettabledetailswithconditions&table=emp_leave_balances&data=".$vdata_3;
$configesponse = callAPI($vurl_3);
$emp_leave_balances = json_decode($configesponse,1);
