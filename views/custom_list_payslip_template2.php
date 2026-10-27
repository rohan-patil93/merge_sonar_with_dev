<?php
$month =$_GET['month'];
$year = $_GET['year'];
if($_GET['emp_id']){
    $employe_id = $_GET['employe_id'];
}
else{
    $employe_id = $_SESSION['emp_id'];
}
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

$cusurl = API."?mode=togetpayslipdetails&month=".$month."&year=".$year."&employe_id=".$employe_id;
$cusresponse = callAPI($cusurl);
$payslip_data_list = json_decode($cusresponse,1);
$dataorg["where"] = array('company_id'=> $_SESSION['company_id'], 'is_active'=>'0');
$datw = urlencode(json_encode($dataorg));
$curl1 = API."?mode=gettabledetailswithconditions&table=config&data=".$datw;
$cudata1 = callAPI($curl1);
$org_details = json_decode($cudata1,1);
$org_details = $_SESSION['is_data']['pay_area_config'] == 1 ? $_SESSION['pay_area_config'] : $org_details['0'];
$organization_name = $org_details['name'];
$organization_logo = $org_details['display_logo'];
$organization_address = $org_details['contact_address'];
$org_location =  $org_details['location'];
?>

<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
        <!-- begin::Card-->
        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <!-- begin: Invoice-->
                <!-- begin: Invoice header-->
                <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
                    <div class="col-md-9">
                        <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                            <h1 class="display-5 font-weight-boldest mb-10">PAYSLIP FOR <?php echo $payslip_data_list['month']." ".$payslip_data_list['year'];?></h1>
                            <div class="d-flex flex-column align-items-md-end px-0">
                                <!--begin::Logo-->
                                <a href="#" class="mb-5">
                                    <img src="uploads/all/<?php echo $organization_logo; ?>" style="width: 220px;" alt="" />
                                </a>
                                <!--end::Logo-->
                                <span class="d-flex flex-column align-items-md-end opacity-70">
                                    <span><?php echo $organization_nam; ?> </span>
                                    <span> <?php echo $organization_address; ?></span>
                                    <span><?php echo $org_location; ?></span>
                                </span>
                            </div>
                        </div>
                        <div class="border-bottom w-100"></div>
                        <div class="d-flex justify-content-between pt-6">
                            <div class="d-flex flex-column flex-root">
                                <span class="font-weight-bolder mb-2">EMPLOYEE</span>
                                <span><?php echo $payslip_data_list['emp_number']; ?> <?php echo "(". $payslip_data_list['emp_name'].")"; ?></span>
                                <span class="opacity-70"><?php echo $payslip_data_list['designation']; ?></span>
                            </div>
                            <div class="d-flex flex-column flex-root">
                                <span class="font-weight-bolder mb-2">STD DAYS</span>
                                <span class="opacity-70"><?php echo $payslip_data_list['std']; ?></span>
                            </div>
                            <div class="d-flex flex-column flex-root">
                                <span class="font-weight-bolder mb-2">WORKED DAYS</span>
                                <span class="opacity-70"><?php echo $payslip_data_list['work']; ?>
                            </div>

                            <div class="d-flex flex-column flex-root">
                                <span class="font-weight-bolder mb-2">LOP DAYS</span>
                                <span class="opacity-70"><?php echo $payslip_data_list['lop']; ?></span>
                            </div>
                            <div class="d-flex flex-column flex-root">
                                <span class="font-weight-bolder mb-2">DATE OF JOINING</span>
                                <span class="opacity-70"><?php
                                echo date('F d, Y',strtotime($payslip_data_list['doj'])); ?>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end: Invoice header-->
                <!-- begin: Invoice body-->
                <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                    <div class="col-md-9">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="pl-0 font-weight-boldest text-muted text-uppercase ">Earnings</th>
                                        <th scope="col" class="text-right font-weight-boldest text-muted text-uppercase">Amount</th>
                                        <th scope="col" class="pl-0 font-weight-boldest text-muted text-uppercase">Deductions</th>
                                        <th scope="col" class="text-right font-weight-boldest text-muted text-uppercase">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $actual_count= count((array)$payslip_data_list['actual']);
                                    $dedaction_count= count((array)$payslip_data_list['dedaction']);
                                    $count = ($actual_count>$dedaction_count) ? $actual_count : $dedaction_count;
                                    for($i=0; $i<$count;$i++) {?>
                                        <tr class="font-weight-normal font-size-lg">
                                            <td class="pl-0 pt-5">
                                                <?php echo  $payslip_data_list['actual'][$i]['name']; ?>
                                            </td>
                                            <td class="text-right pt-5">
                                                <?php
                                                $payslip_total_earnings+=$payslip_data_list['actual'][$i]['amount'];
                                                echo  $payslip_data_list['actual'][$i]['amount']; ?>
                                            </td>
                                            <td class="pl-0 pt-5">
                                                <?php
                                                $payslip_total_deductions+=$payslip_data_list['dedaction'][$i]['amount'];

                                                echo  $payslip_data_list['dedaction'][$i]['name']; ?>
                                            </td>
                                            <td class="text-right pt-5">
                                                <?php echo  $payslip_data_list['dedaction'][$i]['amount']; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                        <td class="pl-0 pt-5">GROSS EARNINGS</td>
                                        <td class="text-right pt-5 text-primary"><?php echo $payslip_data_list['actual_amount'];?></td>
                                        <td class="pl-0 pt-5">TOTAL DECUTIONS</td>
                                        <td class="text-right pt-5 text-danger"><?php echo $payslip_data_list['dedaction_amount'];?></td>
                                    </tr>
                                    <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                        <td class="pl-0 pt-7 text-muted">NET PAY</td>
                                        <td class="text-right pt-5"></td>
                                        <td class="pl-0 pt-5"></td>
                                        <td class="text-right pt-5 text-muted">AMOUNT</td>
                                    </tr>
                                    <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                        <td class="pl-0 pt-5">GROSS EARNINGS</td>
                                        <td class="text-right pt-5 text-primary"></td>
                                        <td class="pl-0 pt-5"></td>
                                        <td class="text-right pt-5 text-primary"><?php echo $payslip_data_list['actual_amount'];?></td>
                                    </tr>
                                    <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                        <td class="pl-0 pt-5">TOTAL DEDUCTIONS</td>
                                        <td class="text-right pt-5 text-primary"></td>
                                        <td class="pl-0 pt-5"></td>
                                        <td class="text-right pt-5 text-danger"><?php echo $payslip_data_list['dedaction_amount'];?></td>
                                    </tr>

                                    <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                        <td class="pl-0 pt-5">TOTAL NET PAYABLE</td>
                                        <td class="text-right pt-5 text-primary"></td>
                                        <td class="pl-0 pt-5"></td>
                                        <td class="text-right pt-5 text-primary">
                                            <?php echo $payslip_data_list['net'];?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end: Invoice body-->
            <!-- begin: Invoice footer-->
            <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-9">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" class="font-weight-bold text-muted text-uppercase">BANK</th>
                                    <th scope="col" class="font-weight-bold text-muted text-uppercase">ACC.NO.</th>

                                    <th scope="col" class="font-weight-bold text-muted text-uppercase">NET SALARY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="font-weight-bolder">
                                    <td><?php echo $payslip_data_list['bank']; ?></td>
                                    <td><?php echo $payslip_data_list['bank_account_no']; ?></td>

                                    <td class="text-danger font-size-h3 font-weight-boldest"><?php echo $payslip_data_list['net'];?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end: Invoice footer-->
            <!-- begin: Invoice action-->
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-9">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-light-primary font-weight-bold" onclick="window.print();">Download Payslip</button>
                        <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">Print Payslip</button>
                    </div>
                </div>
            </div>
            <!-- end: Invoice action-->
            <!-- end: Invoice-->
        </div>
    </div>
    <!-- end::Card-->
</div>
<!--end::Container-->
</div>

</div>
                    <!--end::Content--></div>
