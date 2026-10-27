<?php
$url = API."?mode=bugetvendorrareport&admin_type=".$admin_type."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&employee_id=".$employee_id."&application_user_id=".$application_user_id."&check=1".$locstr;
$from_cur_year = date('Y');
$from_cur_month = date('m');
$expand = "";
$_SESSION['download_data'] = "";
if(isset($_POST['applyform']) || isset($_POST['expand'])) {
    $expand = $_POST['expand'];
    $url = API."?mode=bugetvendorrareport&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&fromdate=".$_POST['from_date']."&emp_id=".$_SESSION['emp_id'];
    $from_cur_year = date('Y', strtotime($_POST['from_date']));
    $from_cur_month = date('m', strtotime($_POST['from_date']));
}
$response = callAPI($url);
$roles = json_decode($response,1);
$from_date = $from_cur_year."-".$from_cur_month."-01";
$todate = date('Y-m-d', strtotime('last day of this month', strtotime($from_date)));
$vurl = API."?mode=toretriveminhorsandweksanddayebasedondates&from_date=".$from_date."&to_date=".$todate;
$attdenace_data = callAPI($vurl);
$attdenace_data = json_decode($attdenace_data,1);
function uomcalculation($uom,$minimum_hours="",$count_working_days="",$count_week=""){
    $minimum_hours = ($minimum_hours!=""?$minimum_hours:8);
    $count_working_days = ($count_working_days!=""?$count_working_days:21);
    $count_week = ($count_week!=""?$count_week:4);
    $vrcmulval = 1;
    if($uom=='PH'){
        $vrcmulval = $minimum_hours*$count_working_days;
    }elseif($uom=='PW'){
        $vrcmulval = $count_working_days/$count_week;
    }elseif($uom=='PD'){
        $vrcmulval = $count_working_days;
    }
    return $vrcmulval;
}
foreach((array)$roles as $role){

    $vrcmulval = uomcalculation($role['vrc_uom'],$attdenace_data['minimum_hours'],$attdenace_data['count_working_days'],$attdenace_data['count_week']);
    $mpcuomcheck  = uomcalculation($role['mpc_uom'],$attdenace_data['minimum_hours'],$attdenace_data['count_working_days'],$attdenace_data['count_week']);
    $venders[$role['vendor_id']]['data'][]= $role;
    $venders[$role['vendor_id']]['id'] = $role['vendor_id'];
    $venders[$role['vendor_id']]['vendor_name'] = $role['vendor_name'];
    $venders[$role['vendor_id']]['billable_hrs'] = $venders[$role['vendor_id']]['billable_hrs']+$role['billable_hrs'];
    $venders[$role['vendor_id']]['nonbillable_hrs'] = $venders[$role['vendor_id']]['nonbillable_hrs']+$role['nonbillable_hrs'];
    $venders[$role['vendor_id']]['budgeted_amount'] = $venders[$role['vendor_id']]['budgeted_amount']+($role['budgeted_amount']*$mpcuomcheck);

    $venders[$role['vendor_id']]['vrc_budgeted_amount'] = $venders[$role['vendor_id']]['vrc_budgeted_amount']+($role['vrc_budgeted_amount']*$vrcmulval);

    $venders[$role['vendor_id']]['billable_amount'] = $venders[$role['vendor_id']]['billable_amount']+$role['billable_amount'];
    $venders[$role['vendor_id']]['vrc_billable_amount'] = $venders[$role['vendor_id']]['vrc_billable_amount']+$role['vrc_billable_amount'];
    $venders[$role['vendor_id']]['leave_days'] = $venders[$role['vendor_id']]['leave_days_from_to']+$role['leave_days_from_to'];
    $venders[$role['vendor_id']]['contact_person'] = $role['contact_person'];
    $venders[$role['vendor_id']]['billable'] = $venders[$role['vendor_id']]['billable'] + ($role['emp_billable']==0 ? '1':'0');
    $venders[$role['vendor_id']]['nonbillable'] = $venders[$role['vendor_id']]['nonbillable'] + ($role['emp_billable']==1 ? '1':'0');
    $venders[$role['vendor_id']]['nonbillable'] = $venders[$role['vendor_id']]['nonbillable'] + ($role['emp_billable']==1 ? '1':'0');

}

function valueemtpydash($val){
    $val = $val ?? [];
    return $val==""?" - ":$val;
}

$vurl = API."?mode=gettabledetailswithconditions&table=cost_centers";
$configesponse = callAPI($vurl);
$cost_centers_data = json_decode($configesponse,1);

foreach((array)$cost_centers_data  as $cost_centers_da){
    $cost_center[$cost_centers_da['id']]  = $cost_centers_da['cost_center_name'];
}

$vurl = API."?mode=gettabledetailswithconditions&table=departments";
$configesponse = callAPI($vurl);
$department_data = json_decode($configesponse,1);

foreach((array)$department_data  as $department_da){
    $department[$department_da['id']]  = $department_da['department_name'];
}

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
<script type="text/javascript">
    function expands(){
        document.getElementById('kt_form').submit();
    }
</script>
<style>
tr{}
td, th{border-bottom: 1px solid #EBEDF3; padding: 10px;}
table.data-table tr th{color: #B5B5C3; font-weight: 500; font-size: 14px; letter-spacing: 1px;}
table.data-table .custom-check{position: relative;}
table.data-table .custom-check input[type="checkbox"]{width: 17px; height: 17px;}
table.data-table .dropdown .btn{padding: 0px 8px 2px 8px;}

span.online-text{width: 8px; height: 8px; display: inline-block; background: #dc3545; border-radius: 50%;}
span.direct-text{width: 8px; height: 8px; display: inline-block; background: #007bff; border-radius: 50%;}
table.data-table .dropdown-toggle:after{display: none;}
table.data-table a.edit{display: inline-block; vertical-align: middle;}
</style>

<div class="row">
    <div class="card card-custom">
        <div class="card-body">
            <div>
                 <button class="btn btn-info waves-effect float-right waves-light input-group-text mt-0">
                  <a href="index.php?option=COM_VENDOR_EMPLOYEE_DASH_BOARD&LOC=<?php echo $loc; ?>" target="_blank" > History </a>
                   </button>
            </div>
            <div class="row dt-responsive table-responsive">
                <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1">
                    <tr>
                        <th scope="col" style="width: 3%;"></th>
                        <th scope="col" style="width: 5%;">SL.NO.</th>
                        <th scope="col" style="width: 13%;">Vendor Name</th>
                        <th scope="col" style="width: 7%;" class="text-right">Employee</th>
                        <th scope="col" style="width: 10%;" class="text-right">Billable Days</th>
                        <th scope="col" style="width: 10%;" class="text-right">Billable Amount</th>
                    </tr>
                    <?php $k=1; $lists = array(); foreach((array)$venders as  $vender){
                        $billable_hrs += ($vender['billable_hrs']/8);
                        $nonbillable_hrs     += ($vender['nonbillable_hrs']/8);
                        $budgeted_amount    += $vender['budgeted_amount'];
                        $vrc_budgeted_amount += $vender['vrc_budgeted_amount'];
                        $billable_amount += $vender['billable_amount'];
                        $vrc_billable_amount +=     $vender['vrc_billable_amount'];
                        $bilableemp += $vender['billable'];
                        $nonbilableemp += $vender['nonbilable'];
                        $leavedays += $vender['leave_days'];
                        $empcnt += count((array)$vender['data']);
                        $lists[$k] = array('vendor_name'=>$vender['vendor_name'],'employee'=>count((array)$vender['data']),'billable_days'=>$vender['billable_hrs']/8,'nonbillable_days'=>$vender['nonbillable_hrs']/8,'po_amount'=>$vender['budgeted_amount'],'vrc_amount'=>$vender['vrc_budgeted_amount'],'billable_amount'=>$vender['billable_amount'],'vrc_billable_amount'=>$vender['vrc_billable_amount'],'billable/_nonbillable'=> $vender['nonbillable'],'leave_days'=>$vender['leave_days']);
                    ?>
                    <tr class="main">
                        <td>
                            <a href="javascript:void(0)" class="main">
                                <svg xmlns="http:
                                    <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                </svg>
                            </a>
                        </td>
                        <td><?php echo $k; ?></td>
                        <td><?php echo  $vender['vendor_name']; ?></td>
                        <td class="text-right"><?php echo count((array)$vender['data']); ?></td>
                        <td class="text-right"><?php echo (($vender['billable_hrs']/8)>0)?($vender['billable_hrs']/8):'0'; ?></td>
                    <td  class="text-right"><?php echo  customeummarmonyformateround($vender['vrc_billable_amount']); ?>    </td>
                       </tr>
                    <tr class="data<?php echo $expand; ?>">
                           <td colspan="15">
                               <table class="mb-3" style="width: 100%">
                                   <tr>
                                       <th scope="col" style="width: 3%;"></th>
                                       <th scope="col" style="width: 5%;">SL.NO</th>
                                       <th scope="col" style="width: 13%;">Employee Name</th>
                                    <th scope="col" style="width: 13%;">Cost Center</th>
                                    <th scope="col" style="width: 13%;">Department</th>
                                       <th scope="col" style="width: 10%;" class="text-right">Billable Days</th>
                                       <th scope="col" style="width: 10%;" class="text-right">Billable Amount</th>
                                   </tr>
                                   <?php $l=1; $list_data= array(); foreach((array)$vender['data'] as $data){
                                           $vrcmulval = uomcalculation($data['vrc_uom'],$attdenace_data['minimum_hours'],$attdenace_data['count_working_days'],$attdenace_data['count_week']);
                                           $mpcuomcheck  = uomcalculation($data['mpc_uom'],$attdenace_data['minimum_hours'],$attdenace_data['count_working_days'],$attdenace_data['count_week']);
                                           $list_data[$l] = array('employee_name'=>$data['employee_name'],'billable_days'=>$data['billable_hrs']/8,'nonbillable_days'=>$data['nonbillable_hrs']/8,'po_amount'=>$data['budgeted_amount'],'vrc_amount'=>$data['vrc_budgeted_amount'],'billable_amount'=>$data['billable_amount'],'vrc_billable_amount'=>$data['vrc_billable_amount'],'billable_status'=>$data['emp_billable']==0 ? 'Billable':'Non Billable','leave_days'=>$data['leave_days_from_to'],'job_tittle'=>$data['job_title'],'exp_year'=>$data['exp_joining_date']+$data['total_experiance'],'remarks'=>$data['experience_base'] == '0'?$data['remark']:'');
                                   ?>
                                   <tr>
                                       <td></td>
                                       <td><?php echo $l; ?></td>
                                       <td><?php echo $data['employee_name']; ?></td>
                                    <td><?php echo $cost_center[$data['cost_center_id']]; ?></td>
                                    <td><?php echo $department[$data['department_id']]; ?></td>
                                       <td class="text-right"><?php echo $data['billable_hrs']/8; ?></td>
                                       <td class="text-right"><?php echo customeummarmonyformateround($data['vrc_billable_amount']); ?></td>
                                   </tr>
                                   <?php $l++;
} $lists[$k]['data']= $list_data; ?>
                               </table>
                           </td>
                       </tr>
                       <?php $k++;
} ?>
                    <?php if(count((array)$venders) !="0"){ ?>
                <tr>
                    <td colspan='3'><b>Total</b></td>
                    <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Employees"><?php echo customeummarmonyformateround($empcnt); ?></span></td>
                    <td class="text-right" ><span data-bs-toggle="tooltip" data-placement="bottom" title=" Billable Days"><?php echo $billable_hrs; ?></span></td>

                    <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Billable Amount"><?php echo customeummarmonyformateround($vrc_billable_amount); ?></span></td>
                </tr>
            <?php } ?>

                   </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
    jQuery(".data").hide();
    jQuery(".main").click(function() {
        jQuery(this).parent().parent().next(".data").toggle("fast");
    });
});
</script>
