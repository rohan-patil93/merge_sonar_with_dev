<?php
if(isset($_POST['pay_month_year'])) {
    $pay_month_year = $_POST['pay_month_year'];
} else {
    $pay_month_year = date('F, Y');
}

$title = "Payslip";
$pay_year = date('Y',strtotime($pay_month_year));
$pay_month = date('m',strtotime($pay_month_year));
$cusurl = API."?mode=togetallpayslipdetails&month=".$pay_month."&year=".$pay_year."&company_id=".$_SESSION['company_id']."&is_active='0'";
$cusresponse = callAPI($cusurl);
$pay_return_list = json_decode($cusresponse,1);

if(empty($_SESSION['is_data']['pay_area_config'])){
    $employee_id = $employe_id ?? 0;
    $_SESSION['pay_area_config'] = array();
    if($employee_id>0){
        $url = API."?method=get&mode=togetpayareaconfigdetailsbasedonemp_id&employee_id=".$employee_id;
        $pay_area_config_res = callAPI($url);
        $pay_area_config_arr = json_decode($pay_area_config_res,1);
        if(count((array)$pay_area_config_arr)>0){
            $_SESSION['is_data']['pay_area_config'] = 1;
            $_SESSION['pay_area_config'] = $pay_area_config_arr;
        }
    }
}
?>

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card j-pro">
                <div class="card-title">
<div class="row">
                            <div class="col-lg-3">
                            <div class="col bg-light-primary ">
                                <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                    <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['is_data']['pay_area_config'] == 1 ? $_SESSION['pay_area_config']['display_logo'] : $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                                </span>
                                <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h4 class="card-label text-center f-w-700"><?php echo $title; ?> <?php echo date("M Y",strtotime($pay_month_year)); ?> </br><small class="f-10">Name : <?php echo $_SESSION['display_name']; ?> | |  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small></h4>

                        </div>
                        <div class="col-lg-3">
                            <form method="post" action="#">
                                <div class="input-group input-group-button">
                                    <input aria-label="Pay Month Year" type="month" name="pay_month_year" value="<?php echo $pay_year."-".$pay_month?>"  id="example-month-input"  class="form-control form-control-sm width1" style="font-size: 18px;height: 48px !important;padding: 10px 15px;">
                                    <button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                                        Submit
                                    </button> </div>
                                </form>
                            </div>

                        </div>

    <div class="dt-responsive table-responsive mt-2">
                    <table id="basic-btn" class="table table-striped table-bordered nowrap">
                        <thead>
                        <tr role="row" style="background-color: #f7f7f7;">
                            <th scope="col">Employee Id</th>
                            <th scope="col">Employee Code</th>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Date Of Joining</th>
                            <th scope="col">Date of Leaving</th>
                            <th scope="col">Number of Working Days</th>

                            <th scope="col">Total Gross</th>
                            <th scope="col">Total Deductions</th>
                            <th scope="col">Net Payable</th>
                            <th scope="col">View Details</th>
                            <th scope="col">View Payslip</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($pay_return_list)) {
                            foreach ($pay_return_list as $pys_data) { ?>
                                <tr role="row" class="odd">
                                <th scope="col"><?php echo $pys_data['employee_id']; ?></th>
                                <th scope="col"><?php echo $pys_data['emp_code']; ?></th>
                                    <th scope="col"><?php echo $pys_data['emp_name']; ?></th>
                                    <th scope="col"><?php echo $pys_data['date_of_joining']; ?></th>
                                    <th scope="col"><?php echo $pys_data['date_of_leaving']=='0000-00-00'?'-':$pys_data['date_of_leaving']; ?></th>
                                    <th scope="col"><?php echo $pys_data['total_no_of_working_days']; ?></th>
                                    <th scope="col" style="text-align: right;"><?php echo $pys_data['earning_amount']; ?></th>
                                    <th scope="col" style="text-align: right;"><?php echo $pys_data['deduction_amount']; ?></th>
                                    <th scope="col" style="text-align: right;"><?php echo $pys_data['net']; ?></th>

                                    <th scope="col"><a href="index.php?option=COM_INSERT_ONBOARDING_EMPLOYEES?Rid=empid:<?php echo $pys_data['employee_id']; ?>"  class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Employee Details" target="_blank"><i class="flaticon-eye"> </i> </a></th>
                                    <th scope="col">

<a class="btn btn-primary waves-effect waves-light" href="index.php?option=COM_LIST_PAYSLIP_TEMPLATE3&month=<?php echo $pay_month;?>&year=<?php echo $pay_year;?>&employe_id=<?php echo $pys_data['employee_id']; ?>" target="_blank">Payslip</a>

                                        </div>
                                    </th>
                                </tr>
                                <?php
                            }
                        } ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
