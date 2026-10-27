<?php
$locstr = "";
if(in_array(REVIWER_ROLE,$_SESSION['role_ids'])){
    $vdata1["where"] = array('is_active'=> 0, 'user_id'=>$_SESSION['userid']);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=user_report_access_locations&data=".$vdata;
    $configesponse = callAPI($vurl);
    $locaccess = json_decode($configesponse,1);
    foreach($locaccess  as $access){
        $displaylocations[$access['location_id']] = $locations[$access['location_id']];
        $loca_ids[] = $access['location_id'];
    }
    $locstr= "&location_id=0&location_id_value=".implode(",",$loca_ids);
}
$url = API."?mode=bugetvendorrareport&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&emp_id=".$_SESSION['emp_id']."&check=1".$locstr;
$from_cur_year = date('Y');
$from_cur_month = date('m');
$expand = "";
if(isset($_POST['applyform']) || isset($_POST['expand'])) {
    $expand = $_POST['expand'];
    $url = API."?mode=bugetvendorrareport&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&fromdate=".$_POST['from_date']."&emp_id=".$_SESSION['emp_id']."&check=1".$locstr;
    $from_cur_year = date('Y', strtotime($_POST['from_date']));
    $from_cur_month = date('m', strtotime($_POST['from_date']));
}
$response = callAPI($url);
$roles = json_decode($response,1);

$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
$vdata = urlencode(json_encode($vdata1));
$menu = API."?mode=gettabledetailswithconditions&table=departments&data=".$vdata;
$responsess = callAPI($menu);
$rolesgg = json_decode($responsess,1);

foreach($rolesgg as $result){
    $dep[$result['id']]=$result['department_name'];
}

$from_date = $from_cur_year."-".$from_cur_month."-01";
$todate = date('Y-m-d', strtotime('last day of this month', strtotime($from_date)));
$vurl = API."?mode=toretriveminhorsandweksanddayebasedondates&from_date=".$from_date."&to_date=".$todate;
$attdenace_data = callAPI($vurl);
$attdenace_data = json_decode($attdenace_data,1);
function uomcalculation($uom,$minimum_hours,$count_working_days,$count_week){
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
foreach($roles as $role){

    $venders[$role['department_id']]['data'][]= $role;
    $venders[$role['department_id']]['id'] = $role['department_id'];
    $venders[$role['department_id']]['vendor_name'] = $dep[$role['department_id']];
    $venders[$role['department_id']]['billable_hrs'] = $venders[$role['department_id']]['billable_hrs']+$role['billable_hrs'];
    $venders[$role['department_id']]['nonbillable_hrs'] = $venders[$role['department_id']]['nonbillable_hrs']+$role['nonbillable_hrs'];
//change
    $vrcmulval = uomcalculation($role['vrc_uom'],$attdenace_data['minimum_hours'],$attdenace_data['count_working_days'],$attdenace_data['count_week']);
    $mpcuomcheck  = uomcalculation($role['mpc_uom'],$attdenace_data['minimum_hours'],$attdenace_data['count_working_days'],$attdenace_data['count_week']);
    $venders[$role['department_id']]['budgeted_amount'] = $venders[$role['department_id']]['budgeted_amount']+($role['budgeted_amount']*$mpcuomcheck);
    $venders[$role['department_id']]['vrc_budgeted_amount'] = $venders[$role['department_id']]['vrc_budgeted_amount']+($role['vrc_budgeted_amount']*$vrcmulval);
    $venders[$role['department_id']]['vrc_billable_amount'] = $venders[$role['department_id']]['vrc_billable_amount']+$role['vrc_billable_amount'];
//change

    $venders[$role['department_id']]['billable_amount'] = $venders[$role['department_id']]['billable_amount']+$role['billable_amount'];
    $venders[$role['department_id']]['leave_days'] = $venders[$role['department_id']]['leave_days_from_to']+$role['leave_days_from_to'];
    $venders[$role['department_id']]['contact_person'] = $role['contact_person'];

$venders[$role['department_id']]['billable'] = $venders[$role['department_id']]['billable'] + ($role['emp_billable']==0 ? '1':'0');
$venders[$role['department_id']]['nonbillable'] = $venders[$role['department_id']]['nonbillable'] + ($role['emp_billable']==1 ? '1':'0');

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
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="col bg-light-primary ">
                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                            </span>
                            <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $_SESSION['config']['name']; ?></span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
                        <h4 class="card-label text-center f-w-700">Department Wise Monthly Summary
                            <small class="f-10 d-block">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?>
                            </small>
                        </h4>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12">
                        <form class="kt-form" id="kt_form" method="post" action="" >
                            <div class="radio radio-outline  mt-1">
                                <label class="radio radio-inline" style="margin-right: 2px !important;">
                                    <input aria-label="Radio option" type="radio" <?php if($expand==''){ echo "checked";
} ?> name="expand" value="" onclick="expands()" onkeydown="">
                                    <span style="float: left;"></span>Collapse
                                </label>
                                <label class="radio radio-inline">
                                    <input aria-label="Radio option" type="radio" <?php if($expand=='E'){ echo "checked";
} ?> name="expand" value="E" onclick="expands()" onkeydown="">
                                    <span style="float: left;"></span>Expand All
                                </label>
                            </div>
                            <div class="input-group input-group-button">
                                <input aria-label="From Date" type="month" name="from_date" value="<?php echo $from_cur_year."-".$from_cur_month?>"  id="att_month_year"  class="form-control form-control-sm width1" style="font-size: 14px;height: 40px !important;padding: 10px 15px;">
                                <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom">
          <div class="card-body">
                <div class="row dt-responsive table-responsive">

                    <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1">
                        <tr>
                            <th scope="col" style="width: 3%;"></th>
                            <th scope="col" style="width: 5%;">SL.NO.</th>
                            <th scope="col" style="width: 13%;">Department Name</th>
                            <th scope="col" class="text-right" style="width: 7%;">Employee</th>
                            <th scope="col" class="text-right" style="width: 10%;">Billable Days</th>
                            <th scope="col" class="text-right" style="width: 12%;">Nonbillable Days</th>
                             <?php if($_SESSION['user_type']!=3){ ?>
                            <th scope="col" class="text-right" style="width: 10%;"> PO Amount</th>
                               <th scope="col" class="text-right" style="width: 10%;"> VRC Amount</th>
                            <?php } ?>
                            <th scope="col" class="text-right" style="width: 10%;">Billable Amount</th>
                            <th scope="col" class="text-right" style="width: 10%;"> VRC Billable Amount</th>
                            <th scope="col" class="text-right" style="width: 10%;">Billable/Non Billable</th>

                            <th scope="col" class="text-right" style="width: 8%;">Leave Days</th>


                        </tr>
                        <?php $k=1; foreach($venders as  $vender){
                                $billable_hrs += ($vender['billable_hrs']/8);
                        $nonbillable_hrs     += ($vender['nonbillable_hrs']/8);
                        $budgeted_amount    += $vender['budgeted_amount'];
                        $vrc_budgeted_amount += $vender['vrc_budgeted_amount'];
                        $billable_amount += $vender['billable_amount'];
                        $vrc_billable_amount +=     $vender['vrc_billable_amount'];
                        $bilableemp += $vender['billable'];
                        $nonbilableemp += $vender['nonbillable'];
                        $leavedays += $vender['leave_days'];
                        $empcnt += count($vender['data']);
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
                               <td class="text-right"><?php echo count($vender['data']); ?></td>
                            <td class="text-right"><?php echo $vender['billable_hrs']/8; ?>    </td>
                            <td class="text-right"><?php echo $vender['nonbillable_hrs']/8; ?></td>
                             <?php if($_SESSION['user_type']!=3){ ?>
                            <td class="text-right"><?php echo  customeummarmonyformateround($vender['budgeted_amount']); ?></td>
                            <td class="text-right"><?php echo  customeummarmonyformateround($vender['vrc_budgeted_amount']); ?></td>
                            <?php } ?>

                            <td class="text-right"><?php echo  customeummarmonyformateround($vender['billable_amount']); ?>    </td>
                            <td class="text-right"><?php echo  customeummarmonyformateround($vender['vrc_billable_amount']); ?>    </td>
                            <td class="text-right"><?php echo  $vender['billable']; ?>/<?php echo  $vender['nonbillable']; ?>    </td>
                            <td class="text-right"><?php echo customeummarmonyformateround($vender['leave_days']); ?></td>


                        </tr>
                        <tr class="data<?php echo $expand; ?>">
                            <td colspan="15">
                                <table class="mb-3" style="width: 100%">
                                    <tr>
                                        <th scope="col" style="width: 3%;"></th>
                                        <th scope="col" style="width: 5%;">SL.NO</th>
                                        <th scope="col" style="width: 13%;">Employee Name</th>
                                        <th scope="col" class="text-right" style="width: 10%;">Billable Days</th>
                                        <th scope="col" class="text-right" style="width: 12%;">Nonbillable Days</th>
                                         <?php if($_SESSION['user_type']!=3){ ?>
                                        <th scope="col" class="text-right" style="width: 10%;"> Budget Amount</th>
                                          <th scope="col" class="text-right" style="width: 10%;">VRC Budget Amount</th>
                                        <?php } ?>
                                        <th scope="col" class="text-right" style="width: 10%;">Billable Amount</th>
                                        <th scope="col" class="text-right" style="width: 10%;">VRC Billable Amount</th>
                                        <th scope="col" class="text-right" style="width: 10%;">Billable status</th>
                                               <th scope="col" class="text-right" style="width: 8%;">Leave Days</th>
                                                  <th scope="col" style="width: 15%;">Job Tittle</th>
                                        <th scope="col" class="text-right" style="width: 7%;">Exp-Year</th>
                                         <?php if($_SESSION['user_type']!=3){ ?>
                                        <th scope="col" style="width: 5%;">Rate Card</th>
                                        <th scope="col" style="width: 5%;">Remarks</th>
                                        <?php } ?>
                                    </tr>
                                    <?php $l=1; foreach($vender['data'] as $data){

                                    $vrcmulval = uomcalculation($data['vrc_uom'],$attdenace_data['minimum_hours'],$attdenace_data['count_working_days'],$attdenace_data['count_week']);
                                    $mpcuomcheck  = uomcalculation($data['mpc_uom'],$attdenace_data['minimum_hours'],$attdenace_data['count_working_days'],$attdenace_data['count_week']);
                                        ?>
                                    <tr>
                                        <td></td>
                                        <td><?php echo $l; ?></td>
                                        <td><a target="_blank" class="text-primary" href="index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=<?php echo $from_cur_year; ?>&PAY_MONTH=<?php echo $from_cur_month; ?>&EMP=<?php echo $data['employee_id']; ?>"><?php echo $data['employee_name']; ?> </a></td>

                                        <td class="text-right"><?php echo $data['billable_hrs']/8; ?></td>
                                        <td class="text-right"><?php echo $data['nonbillable_hrs']/8; ?></td>
                                        <?php if($_SESSION['user_type']!=3){ ?>
                                         <td class="text-right"><?php echo customeummarmonyformateround($data['budgeted_amount']*$mpcuomcheck); ?></td>
                                         <td class="text-right"><?php echo customeummarmonyformateround($data['vrc_budgeted_amount']*$vrcmulval); ?></td>
                                        <?php } ?>
                                        <td class="text-right"><?php echo customeummarmonyformateround($data['billable_amount']); ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformateround($data['vrc_billable_amount']); ?></td>
                                           <td class="text-right"><?php echo $data['emp_billable']==0 ? 'Billable':'<span style="color:red">Non Billable</span>' ?></td>

                                        <td class="text-right"><?php echo customeummarmonyformateround($data['leave_days_from_to']); ?></td>
                                           <td class=""><?php echo $data['job_title']; ?></td>
                                       <td class="text-right"><?php echo customeummarmonyformateround($data['exp_joining_date']+$data['total_experiance']); ?></td>
                                        <?php if($_SESSION['user_type']!=3){ ?>
                                        <td><?php  echo $data['min_exp']."-".$data['max_exp']; ?></td>
                                         <td  <?php  if($data['remark']!=""){ ?> class="text-danger" <?php
} ?>><?php  echo $data['experience_base'] == '0'?$data['remark']:""; ?></td>
                                        <?php } ?>
                                    </tr>
                                    <?php $l++;
} ?>
                                </table>
                            </td>
                        </tr>
                        <?php $k++;
} ?>
                        <?php if(count($venders) !="0"){ ?>
                        <tr>
                            <td colspan='3'><b>Total</b></td>
                            <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Employees"><?php echo customeummarmonyformateround($empcnt); ?></span></td>
                            <td class="text-right" ><span data-bs-toggle="tooltip" data-placement="bottom" title=" Billable Days"><?php echo $billable_hrs; ?></span></td>
                            <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Nonbillable Days"><?php echo $nonbillable_hrs; ?></span></td>
                            <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" PO Amount"><?php echo customeummarmonyformateround($budgeted_amount); ?></span></td>
                            <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" VRC Amount"><?php echo customeummarmonyformateround($vrc_budgeted_amount); ?></span></td>
                            <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Billable Amount"><?php echo customeummarmonyformateround($billable_amount); ?></span></td>
                            <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" VRC Billable Amount"><?php echo customeummarmonyformateround($vrc_billable_amount); ?></span></td>
                            <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Billable/Non Billable"><?php echo $bilableemp.'/'.$nonbilableemp; ?></span></td>
                            <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Leave Days"><?php echo customeummarmonyformateround($leavedays); ?></span></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
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
