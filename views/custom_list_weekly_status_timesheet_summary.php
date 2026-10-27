<?php
if(isset($_POST['pay_month_year'])) {
    $pay_month_year = $_POST['pay_month_year'];
    $reproting_manager = $_POST['reproting_manager'];
} else {
    $pay_month_year = date('F, Y');
    $reproting_manager = 0;
}
$title = "Payslip";
$pay_year = date('Y',strtotime($pay_month_year));
$pay_month = date('m',strtotime($pay_month_year));
$_SESSION['download_data'] = "";
$cusurl = API."?mode=getweecklystatustimesheetsummary&month=".$pay_month."&year=".$pay_year."&reproting_manager=".$reproting_manager."&admin=".$_SESSION['admin_type'];
$cusresponse = callAPI($cusurl);
$result = json_decode($cusresponse,1);
function valueemtpydash($val){
    return ($val=="" || $val=="0.00")?" - ":$val;
}
$displayarray = array();
$totalempcnt = $totalhrs = $firstweek = $secondweek = $thirdweek = $fourthweek = $fiveweek = 0;
$reporting_tolist = array();
foreach((array)$result as $key=>$ressum){
    $reporting_tolist[] =$key;
    $displayarray[$key]['total_emp'] = 0;
    foreach((array)$ressum as $key2=>$ressums){
        foreach((array)$ressums as $key3=>$arr_value){
            $totalempcnt++;
            $displayarray[$key]['total_emp']++;
            $displayarray[$key]['total_hrs'] += $arr_value['total_hrs'];
            $displayarray[$key]['YS_week_1'] += $arr_value['YS_week_1'];
            $displayarray[$key]['RM_week_1'] += $arr_value['RM_week_1'];
            $displayarray[$key]['AM_week_1'] += $arr_value['AM_week_1'];
            $displayarray[$key]['HR_week_1'] += $arr_value['HR_week_1'];
            $displayarray[$key]['FN_week_1'] += $arr_value['FN_week_1'];
            $displayarray[$key]['AP_week_1'] += $arr_value['AP_week_1'];
            $displayarray[$key]['TOT_week_1'] += $arr_value['TOT_week_1'];
            $displayarray[$key]['YS_week_2'] += $arr_value['YS_week_2'];
            $displayarray[$key]['RM_week_2'] += $arr_value['RM_week_2'];
            $displayarray[$key]['AM_week_2'] += $arr_value['AM_week_2'];
            $displayarray[$key]['HR_week_2'] += $arr_value['HR_week_2'];
            $displayarray[$key]['FN_week_2'] += $arr_value['FN_week_2'];
            $displayarray[$key]['AP_week_2'] += $arr_value['AP_week_2'];
            $displayarray[$key]['TOT_week_2'] += $arr_value['TOT_week_2'];
            $displayarray[$key]['YS_week_3'] += $arr_value['YS_week_3'];
            $displayarray[$key]['RM_week_3'] += $arr_value['RM_week_3'];
            $displayarray[$key]['AM_week_3'] += $arr_value['AM_week_3'];
            $displayarray[$key]['HR_week_3'] += $arr_value['HR_week_3'];
            $displayarray[$key]['FN_week_3'] += $arr_value['FN_week_3'];
            $displayarray[$key]['AP_week_3'] += $arr_value['AP_week_3'];
            $displayarray[$key]['TOT_week_3'] += $arr_value['TOT_week_3'];
            $displayarray[$key]['YS_week_4'] += $arr_value['YS_week_4'];
            $displayarray[$key]['RM_week_4'] += $arr_value['RM_week_4'];
            $displayarray[$key]['AM_week_4'] += $arr_value['AM_week_4'];
            $displayarray[$key]['HR_week_4'] += $arr_value['HR_week_4'];
            $displayarray[$key]['FN_week_4'] += $arr_value['FN_week_4'];
            $displayarray[$key]['AP_week_4'] += $arr_value['AP_week_4'];
            $displayarray[$key]['TOT_week_4'] += $arr_value['TOT_week_4'];
            $displayarray[$key]['YS_week_5'] += $arr_value['YS_week_5'];
            $displayarray[$key]['RM_week_5'] += $arr_value['RM_week_5'];
            $displayarray[$key]['AM_week_5'] += $arr_value['AM_week_5'];
            $displayarray[$key]['HR_week_5'] += $arr_value['HR_week_5'];
            $displayarray[$key]['FN_week_5'] += $arr_value['FN_week_5'];
            $displayarray[$key]['AP_week_5'] += $arr_value['AP_week_5'];
            $displayarray[$key]['TOT_week_5'] += $arr_value['TOT_week_5'];

            $totalarray['total_emp']++;
            $totalarray['total_hrs'] += $arr_value['total_hrs'];
            $totalarray['YS_week_1'] += $arr_value['YS_week_1'];
            $totalarray['RM_week_1'] += $arr_value['RM_week_1'];
            $totalarray['AM_week_1'] += $arr_value['AM_week_1'];
            $totalarray['HR_week_1'] += $arr_value['HR_week_1'];
            $totalarray['FN_week_1'] += $arr_value['FN_week_1'];
            $totalarray['AP_week_1'] += $arr_value['AP_week_1'];
            $totalarray['TOT_week_1'] += $arr_value['TOT_week_1'];
            $totalarray['YS_week_2'] += $arr_value['YS_week_2'];
            $totalarray['RM_week_2'] += $arr_value['RM_week_2'];
            $totalarray['AM_week_2'] += $arr_value['AM_week_2'];
            $totalarray['HR_week_2'] += $arr_value['HR_week_2'];
            $totalarray['FN_week_2'] += $arr_value['FN_week_2'];
            $totalarray['AP_week_2'] += $arr_value['AP_week_2'];
            $totalarray['TOT_week_2'] += $arr_value['TOT_week_2'];
            $totalarray['YS_week_3'] += $arr_value['YS_week_3'];
            $totalarray['RM_week_3'] += $arr_value['RM_week_3'];
            $totalarray['AM_week_3'] += $arr_value['AM_week_3'];
            $totalarray['HR_week_3'] += $arr_value['HR_week_3'];
            $totalarray['FN_week_3'] += $arr_value['FN_week_3'];
            $totalarray['AP_week_3'] += $arr_value['AP_week_3'];
            $totalarray['TOT_week_3'] += $arr_value['TOT_week_3'];
            $totalarray['YS_week_4'] += $arr_value['YS_week_4'];
            $totalarray['RM_week_4'] += $arr_value['RM_week_4'];
            $totalarray['AM_week_4'] += $arr_value['AM_week_4'];
            $totalarray['HR_week_4'] += $arr_value['HR_week_4'];
            $totalarray['FN_week_4'] += $arr_value['FN_week_4'];
            $totalarray['AP_week_4'] += $arr_value['AP_week_4'];
            $totalarray['TOT_week_4'] += $arr_value['TOT_week_4'];
            $totalarray['YS_week_5'] += $arr_value['YS_week_5'];
            $totalarray['RM_week_5'] += $arr_value['RM_week_5'];
            $totalarray['AM_week_5'] += $arr_value['AM_week_5'];
            $totalarray['HR_week_5'] += $arr_value['HR_week_5'];
            $totalarray['FN_week_5'] += $arr_value['FN_week_5'];
            $totalarray['AP_week_5'] += $arr_value['AP_week_5'];
            $totalarray['TOT_week_5'] += $arr_value['TOT_week_5'];
        }
    }
}

?>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
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
        .custom-size{ font-size: 8px !important; padding: 2px !important;}
</style>
<div class="page-body">
    <div class="card">
        <div class="card-body card-title">
            <form method="post" action="#">
                <div class="row mb-2">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 mb-3">
                        <div class="col bg-light-primary ">
                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                            </span>
                            <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="form-group">
                            <?php if($_SESSION['admin_type'] =='1'){ ?>
                            <span class="f-w-700 mb-1">Approving Manager</span>
                            <select aria-label="Reproting Manager" class="form-select js-example-basic-single p-2" name="reproting_manager">
                                <option value="0">All</option>
                                foreach((array)$_SESSION['users'] as $key=>$reporting_tolists){
                                ?>
                                    <option value="<?php echo $key; ?>" <?php echo $reproting_manager==$key?"selected":"";?>><?php echo $reporting_tolists; ?></option>
                                <?php }?>
                            </select>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="form-group">
                            <span class="f-w-700 mb-1">Month</span>
                            <div class="input-group input-group-button">
                                <input aria-label="Pay Month Year" type="month" name="pay_month_year" value="<?php echo $pay_year."-".$pay_month?>"  id="example-month-input"  class="form-control form-control-sm width1" style="">
                                <button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-lg-5 col-md-12 col-sm-12 col-12 mb-3">
                    <h4 class="card-label text-start f-w-700">Week Wise Status Wise Timesheet Summary  <?php echo date("M Y",strtotime($pay_month_year)); ?>
                        <small class="f-10 d-block">Name : <?php echo $_SESSION['display_name']; ?> | |  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small>
                    </h4>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12 col-12 text-right mb-3">
                    <div class="label-main f-12">
                          <span class="form-label label label-inverse-info-border">YS</span> Yet To Submit
                    </div>
                    <div class="label-main f-12">
                          <span class="form-label label label-inverse-info-border">RM</span> Reporting Manager
                    </div>
                    <div class="label-main f-12">
                          <span class="form-label label label-inverse-info-border">AM</span>Approving Manager
                    </div>
                    <div class="label-main f-12">
                          <span class="form-label label label-inverse-info-border">HR</span> Human Resources
                    </div>
                    <div class="label-main f-12">
                          <span class="form-label label label-inverse-info-border">FN</span> Finanace
                    </div>
                    <div class="label-main f-12">
                          <span class="form-label label label-inverse-info-border">AP</span> Approve
                    </div>
                    <div class="label-main f-12">
                          <span class="form-label label label-inverse-info-border">TH</span> Total hours
                    </div>
                </div>
                <div class="col-lg-2 col-md-12 col-sm-12 col-12 mb-3">
                    <a href="custom_view_download.php?page=Monthly_Timesheets_Status" class="btn btn-primary float-right" target="_blank"> Download </a>
                </div>
            </div>
            <div class="dt-responsive table-responsive mt-2">
                <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" >
                    <tr>
                        <th scope="col" rowspan="2"></th>
                        <th scope="col" rowspan="2" class="f-12">SL.NO.</th>
                        <th scope="col" rowspan="2" class="f-12">Approving Manager</th>
                        <th scope="col" class="text-right f-12" rowspan="2">Total Emp</th>
                        <th scope="col" class="text-right f-12" rowspan="2">Total Hours</th>
                        <th scope="col" class="text-center  f-12" colspan="7" rowspan="1">Week-1</th>
                        <th scope="col" class="text-center f-12" colspan="7" rowspan="1">Week-2</th>
                        <th scope="col" class="text-center f-12" colspan="7" rowspan="1"> Week-3</th>
                        <th scope="col" class="text-center f-12" colspan="7" rowspan="1">Week-4</th>
                        <th scope="col" class="text-center f-12" colspan="7" rowspan="1">Week-5</th>
                    </tr>
                    <tr style="position: sticky; top: 0; background-color: #ffffff;">
                        <th scope="col" class="text-right width4per f-12">YS</th>
                        <th scope="col" class="text-right width4per f-12">RM</th>
                        <th scope="col" class="text-right width4per f-12">AM</th>
                        <th scope="col" class="text-right  width4per f-12">HR</th>
                        <th scope="col" class="text-right width4per f-12">FN</th>
                        <th scope="col" class="text-right width4per f-12">AP</th>
                        <th scope="col" class="text-right width4per f-12">TH</th>
                        <th scope="col" class="text-right width4per f-12">YS</th>
                        <th scope="col" class="text-right width4per f-12">RM</th>
                        <th scope="col" class="text-right width4per f-12">AM</th>
                        <th scope="col" class="text-right width4per f-12">HR</th>
                        <th scope="col" class="text-right width4per f-12">FN</th>
                        <th scope="col" class="text-right width4per f-12">AP</th>
                        <th scope="col" class="text-right width4per f-12">TH</th>
                        <th scope="col" class="text-right width4per f-12">YS</th>
                        <th scope="col" class="text-right width4per f-12">RM</th>
                        <th scope="col" class="text-right width4per f-12">AM</th>
                        <th scope="col" class="text-right width4per f-12">HR</th>
                        <th scope="col" class="text-right width4per f-12">FN</th>
                        <th scope="col" class="text-right width4per f-12">AP</th>
                        <th scope="col" class="text-right width4per f-12">TH</th>
                        <th scope="col" class="text-right width4per f-12">YS</th>
                        <th scope="col" class="text-right width4per f-12">RM</th>
                        <th scope="col" class="text-right width4per f-12">AM</th>
                        <th scope="col" class="text-right width4per f-12">HR</th>
                        <th scope="col" class="text-right width4per f-12">FN</th>
                        <th scope="col" class="text-right width4per f-12">AP</th>
                        <th scope="col" class="text-right width4per f-12">TH</th>
                        <th scope="col" class="text-right width4per f-12">YS</th>
                        <th scope="col" class="text-right width4per f-12">RM</th>
                        <th scope="col" class="text-right width4per f-12">AM</th>
                        <th scope="col" class="text-right  width4per f-12">HR</th>
                        <th scope="col" class="text-right width4per f-12">FN</th>
                        <th scope="col" class="text-right width4per f-12">AP</th>
                        <th scope="col" class="text-right width4per f-12">TH</th>
                    </tr>
                    <?php $k=1;$lists = array(); foreach((array)$result as  $key5=>$rows){
                    $lists[$k] = array('approval_manager'=>$_SESSION['users'][$key5],'total_employees'=>$displayarray[$key5]['total_emp'],'total_hours'=>$displayarray[$key5]['total_hrs'],'YS_week_1'=>$displayarray[$key5]['YS_week_1'],'RM_week_1'=>$displayarray[$key5]['RM_week_1'],'AM_week_1'=>$displayarray[$key5]['AM_week_1'],'HR_week_1'=>$displayarray[$key5]['HR_week_1'],'FN_week_1'=>$displayarray[$key5]['FN_week_1'],'AP_week_1'=>$displayarray[$key5]['AP_week_1'],'total_week_1'=>$displayarray[$key5]['TOT_week_1'],'YS_week_2'=>$displayarray[$key5]['YS_week_2'],'RM_week_2'=>$displayarray[$key5]['RM_week_2'],'AM_week_2'=>$displayarray[$key5]['AM_week_2'],'HR_week_2'=>$displayarray[$key5]['HR_week_2'],'FN_week_2'=>$displayarray[$key5]['FN_week_2'],'AP_week_2'=>$displayarray[$key5]['AP_week_2'],'total_week_2'=>$displayarray[$key5]['TOT_week_2'],'YS_week_3'=>$displayarray[$key5]['YS_week_3'],'RM_week_3'=>$displayarray[$key5]['RM_week_3'],'AM_week_3'=>$displayarray[$key5]['AM_week_3'],'HR_week_3'=>$displayarray[$key5]['HR_week_3'],'FN_week_3'=>$displayarray[$key5]['FN_week_3'],'AP_week_3'=>$displayarray[$key5]['AP_week_3'],'total_week_3'=>$displayarray[$key5]['TOT_week_3'],'YS_week_4'=>$displayarray[$key5]['YS_week_4'],'RM_week_4'=>$displayarray[$key5]['RM_week_4'],'AM_week_4'=>$displayarray[$key5]['AM_week_4'],'HR_week_4'=>$displayarray[$key5]['HR_week_4'],'FN_week_4'=>$displayarray[$key5]['FN_week_4'],'AP_week_4'=>$displayarray[$key5]['AP_week_4'],'total_week_4'=>$displayarray[$key5]['TOT_week_4'],'YS_week_5'=>$displayarray[$key5]['YS_week_5'],'RM_week_5'=>$displayarray[$key5]['RM_week_5'],'AM_week_5'=>$displayarray[$key5]['AM_week_5'],'HR_week_5'=>$displayarray[$key5]['HR_week_5'],'FN_week_5'=>$displayarray[$key5]['FN_week_5'],'AP_week_5'=>$displayarray[$key5]['AP_week_5'],'total_week_5'=>$displayarray[$key5]['TOT_week_5']);
                    ?>
                    <tr class="main">
                        <td>

                            <i class="ti-angle-double-down" onclick="opensubtable(<?php echo $key5; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                            <input type="hidden" id="showhide<?php echo $key5; ?>" value="0">
                        </td>

                        <td class="f-12"><?php echo $k; ?></td>
                         <td style="width: 300px;" class="f-12"><?php echo $_SESSION['users'][$key5]; ?></td>
                         <td class="text-right f-12"><?php echo  customeummarmonyformateround($displayarray[$key5]['total_emp']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['total_hrs']); ?>
                        </td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['YS_week_1']); ?>
                        </td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['RM_week_1']); ?>
                        </td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AM_week_1']); ?>
                        </td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['HR_week_1']); ?>
                        </td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['FN_week_1']); ?>
                        </td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AP_week_1']); ?>
                        </td>
                        <td class="text-right width4per f-12 text-danger f-w-700"><?php echo customeummarmonyformateround($displayarray[$key5]['TOT_week_1']); ?>
                        </td>

                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['YS_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['RM_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AM_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['HR_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['FN_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AP_week_2']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700 "><?php echo customeummarmonyformateround($displayarray[$key5]['TOT_week_2']); ?></td>

                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['YS_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['RM_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AM_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['HR_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['FN_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AP_week_3']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700 "><?php echo customeummarmonyformateround($displayarray[$key5]['TOT_week_3']); ?></td>

                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['YS_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['RM_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AM_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['HR_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['FN_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AP_week_4']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700"><?php echo customeummarmonyformateround($displayarray[$key5]['TOT_week_4']); ?></td>

                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['YS_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['RM_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AM_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['HR_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['FN_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($displayarray[$key5]['AP_week_5']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700"><?php echo customeummarmonyformateround($displayarray[$key5]['TOT_week_5']); ?></td>
                    </tr>
                    <?php $m=1; $list_data= array();  foreach((array)$rows as $datas){
                    foreach((array)$datas as $data){
                     $list_data[$m] = array('employee_name'=>$data['display_name'],'total_hours'=>customeummarmonyformateround($data['total_hrs']), 'YS_week_1'=>customeummarmonyformateround($data['YS_week_1']),'RM_week_1'=>customeummarmonyformateround($data['RM_week_1']),'AM_week_1'=>customeummarmonyformateround($data['AM_week_1']),'HR_week_1'=>customeummarmonyformateround($data['HR_week_1']),'FN_week_1'=>customeummarmonyformateround($data['FN_week_1']),'AP_week_1'=>customeummarmonyformateround($data['AP_week_1']),'total_week_1'=>customeummarmonyformateround($data['TOT_week_1']),'YS_week_2'=>customeummarmonyformateround($data['YS_week_2']),'RM_week_2'=>customeummarmonyformateround($data['RM_week_2']),'AM_week_2'=>customeummarmonyformateround($data['AM_week_2']), 'HR_week_2'=>customeummarmonyformateround($data['HR_week_2']),'FN_week_2'=>customeummarmonyformateround($data['FN_week_2']),'AP_week_2'=>customeummarmonyformateround($data['AP_week_2']),'total_week_2'=>customeummarmonyformateround($data['TOT_week_2']),'YS_week_3'=>customeummarmonyformateround($data['YS_week_3']),'RM_week_3'=>customeummarmonyformateround($data['RM_week_3']),'AM_week_3'=>customeummarmonyformateround($data['AM_week_3']),'HR_week_3'=>customeummarmonyformateround($data['HR_week_3']),'FN_week_3'=>customeummarmonyformateround($data['FN_week_3']),'AP_week_3'=>customeummarmonyformateround($data['AP_week_3']),'total_week_3'=>customeummarmonyformateround($data['TOT_week_3']),'YS_week_4'=>customeummarmonyformateround($data['YS_week_4']),'RM_week_4'=>customeummarmonyformateround($data['RM_week_4']),'AM_week_4'=>customeummarmonyformateround($data['AM_week_4']),'HR_week_4'=>customeummarmonyformateround($data['HR_week_4']),'FN_week_4'=>customeummarmonyformateround($data['FN_week_4']),'AP_week_4'=>customeummarmonyformateround($data['AP_week_4']),'total_week_4'=>customeummarmonyformateround($data['TOT_week_4']),'YS_week_5'=>customeummarmonyformateround($data['YS_week_5']),'RM_week_5'=>customeummarmonyformateround($data['RM_week_5']),'AM_week_5'=>customeummarmonyformateround($data['AM_week_5']),'HR_week_5'=>customeummarmonyformateround($data['HR_week_5']),'FN_week_5'=>customeummarmonyformateround($data['FN_week_5']),'AP_week_5'=>customeummarmonyformateround($data['AP_week_5']),'total_week_5'=>customeummarmonyformateround($data['TOT_week_5']));
                        ?>
                    <tr class="data showclass<?php echo $key5; ?>">
                        <td></td>
                        <td class="f-12"> <?php echo $m; ?></td>

                          <td style="width: 300px;" class="f-12"><?php //echo $data['display_name']; ?>
                            <a target="_blank" class="text-primary d-block" href="index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=<?php echo $pay_year; ?>&PAY_MONTH=<?php echo $pay_month; ?>&EMP=<?php echo $data['emp_id']; ?>"><?php echo $data['display_name']; ?> </a>
                            <p class="f-10 text-primary1"> <?php echo $_SESSION['users'][$data['approval_manager']]; ?></p>
                        </td>
                        <td></td>
                        <td class="text-right f-12" style="width: 4%;"><?php echo customeummarmonyformateround($data['total_hrs']); ?>
                        </td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['YS_week_1']); ?></td>
                        <td class="text-right width4per f-12 f-12"><?php echo customeummarmonyformateround($data['RM_week_1']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AM_week_1']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['HR_week_1']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['FN_week_1']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AP_week_1']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700"><?php echo customeummarmonyformateround($data['TOT_week_1']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['YS_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['RM_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AM_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['HR_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['FN_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AP_week_2']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700"><?php echo customeummarmonyformateround($data['TOT_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['YS_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['RM_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AM_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['HR_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['FN_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AP_week_3']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700"><?php echo customeummarmonyformateround($data['TOT_week_3']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['YS_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['RM_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AM_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['HR_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['FN_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AP_week_4']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700"><?php echo customeummarmonyformateround($data['TOT_week_4']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['YS_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['RM_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AM_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['HR_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['FN_week_5']); ?></td>
                        <td class="text-right width4per f-12"><?php echo customeummarmonyformateround($data['AP_week_5']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700"><?php echo customeummarmonyformateround($data['TOT_week_5']); ?></td>
                    </tr>

                    <?php $m++;
}
}$lists[$k]['data']= $list_data; ?>
                    <?php $k++;
} ?>
                        <?php if($totalempcnt !="0"){ ?>
                    <tr>
                        <td colspan='3'><b class="f-12">Total</b></td>

                        <td class="text-right width4per f-12"><?php echo  customeummarmonyformateround($totalarray['total_emp']); ?> </td>

                        <td class="text-right width4per f-12"><?php echo  customeummarmonyformateround($totalarray['total_hrs']); ?> </td>
                        <td class="text-right width4per f-12"><?php echo  customeummarmonyformateround($totalarray['YS_week_2']); ?></td>
                        <td class="text-right width4per f-12"><?php echo  customeummarmonyformateround($totalarray['RM_week_1']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AM_week_1']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['HR_week_1']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['FN_week_1']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AP_week_1']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700">
                        <?php echo  customeummarmonyformateround($totalarray['TOT_week_1']); ?></td>
                        <td class="text-right width4per f-12"><?php echo  customeummarmonyformateround($totalarray['YS_week_2']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['RM_week_2']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AM_week_2']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['HR_week_2']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['FN_week_2']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AP_week_2']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700">
                        <?php echo  customeummarmonyformateround($totalarray['TOT_week_2']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['YS_week_3']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['RM_week_3']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AM_week_3']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['HR_week_3']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['FN_week_3']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AP_week_3']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700">
                        <?php echo  customeummarmonyformateround($totalarray['TOT_week_3']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['YS_week_4']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['RM_week_4']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AM_week_4']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['HR_week_4']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['FN_week_4']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AP_week_4']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700">
                        <?php echo  customeummarmonyformateround($totalarray['TOT_week_4']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['YS_week_5']); ?></td>
                        <td class="text-right width4per f-12" >
                        <?php echo  customeummarmonyformateround($totalarray['RM_week_5']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AM_week_5']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['HR_week_5']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['FN_week_5']); ?></td>
                        <td class="text-right width4per f-12">
                        <?php echo  customeummarmonyformateround($totalarray['AP_week_5']); ?></td>
                        <td class="text-right width4per f-12 text-danger f-w-700">
                        <?php echo  customeummarmonyformateround($totalarray['TOT_week_5']); ?></td>
                        <td class="text-right width4per f-12"></td>
                    </tr>
                    <?php }else{ ?>
                    <tr class="text-center">
                        <td colspan='40'><b>Record Not Founds</b></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <?php $_SESSION['download_data'] = $lists; //echo "<pre>"; print_r($_SESSION['download_data']); ?>
        </div>
    </div>
</div>




<script type="text/javascript">
    jQuery(document).ready(function() {
    //Hide table rows with class 'min', but appear when clicked.
    jQuery(".data").hide();
    jQuery(".main").click(function() {
    });
});
function opensubtable(id){
    var showhide = $("#showhide"+id).val();
    if(showhide=='0'){
        $(".showclass"+id).show();
        $("#showhide"+id).val('1');
    }else{
        $(".showclass"+id).hide();
        $("#showhide"+id).val('0');
    }
}
</script>
