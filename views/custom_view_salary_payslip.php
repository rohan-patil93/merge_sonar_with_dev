<?php
$stpdata["where"] = array('is_active'=> '0');
$stpdata["order"] = "display_seq ASC";
$stpmdata = urlencode(json_encode($stpdata));
$stpurl = API."?mode=gettabledetailswithconditions&table=earnings&data=".$stpmdata;
$ee = callAPI($stpurl);
$earningdata = json_decode($ee,1);
foreach ($earningdata as  $earningdat) {
  $ernings[$earningdat['id']] = $earningdat;
}
$main_ids = encodeId($_GET['Rid']);
$main_id = explode(":",$main_ids);
$stpdata["where"] = array('is_active'=> '0','salary_template_id'=>$main_id['1']);
$stpdata["order"] = "component_cat_id ASC";
$stpmdata = urlencode(json_encode($stpdata));
$stpurl = API."?mode=gettabledetailswithconditions&table=salary_template_components&data=".$stpmdata;
 $salarytmpdata = callAPI($stpurl);
 $salarytmpdata = json_decode($salarytmpdata,1);
$payslip_data_list =array();
foreach ($salarytmpdata as  $salarytmpda) {

  $show_data['name'] = $ernings[$salarytmpda['component_id']]['payslip_name']!="" ? $ernings[$salarytmpda['component_id']]['payslip_name'] :  $salarytmpda['component_name'];
  $show_data['amount'] = $salarytmpda['monthly_amount'];
  if($ernings[$salarytmpda['component_id']]['calculation_mode']=='ADD'){
          $payslip_data_list['actual'][] = $show_data;
          $payslip_data_list['actual_amount'] = $payslip_data_list['actual_amount']+$salarytmpda['monthly_amount'];
  }
  if($ernings[$salarytmpda['component_id']]['calculation_mode']=='MINUS'){
          $payslip_data_list['dedaction'][] = $show_data;
          $payslip_data_list['dedaction_amount'] = $payslip_data_list['dedaction_amount']+$salarytmpda['monthly_amount'];
  }
}
$payslip_data_list['net'] = $payslip_data_list['actual_amount']-$payslip_data_list['dedaction_amount'];
if(empty($_SESSION['is_data']['pay_area_config'])){
    $employee_id = $_SESSION['emp_id'] ?? 0;
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

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-body p-0">
                    <!--begin::Invoice-->
                    <div class="row justify-content-center pt-8 px-8 pt-md-27 px-md-0">
                        <div class="col-md-9">
                            <!-- begin: Invoice header-->
                            <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                                <h1 class="display-5 font-weight-boldest mb-10">PAYSLIP FOR <?php echo $payslip_data_list['month']." ".$payslip_data_list['year'];?></h1>
                                <div class="d-flex flex-column align-items-md-end px-0">
                                    <!--begin::Logo-->
                                    <img src="uploads/all/<?php echo $organization_logo; ?>" style="width: 220px;" alt="" />
                                    <!--end::Logo-->
                                    <span class="d-flex flex-column align-items-md-end font-size-lg font-weight-bold text-muted">
                                        <span><?php echo $organization_nam; ?> </span>
                                        <span> <?php echo $organization_address; ?></span>
                                        <span><?php echo $org_location; ?></span>
                                    </span>
                                </div>
                            </div>

                            <!--end: Invoice header-->
                            <!--begin: Invoice body-->
                            <div class="row border-bottom pb-10">
                                <div class="col-md-9 py-md-10 pr-md-10">
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
                                                        <td class="pl-0">
                                                            <?php echo  $payslip_data_list['actual'][$i]['name']; ?>
                                                        </td>
                                                        <td class="text-right">
                                                            <?php
                                                            echo  $payslip_data_list['actual'][$i]['amount']; ?>
                                                        </td>
                                                        <td class="pl-0">
                                                            <?php
                                                            echo  $payslip_data_list['dedaction'][$i]['name']; ?>
                                                        </td>
                                                        <td class="text-right">
                                                            <?php echo  $payslip_data_list['dedaction'][$i]['amount']; ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>

                                                <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                                    <td class="pl-0 f-w-700">GROSS EARNINGS</td>
                                                    <td class="text-right text-primary"><?php echo $payslip_data_list['actual_amount'];?></td>
                                                    <td class="pl-0 f-w-700">TOTAL DEDUCTIONS</td>
                                                    <td class="text-right text-danger"><?php echo $payslip_data_list['dedaction_amount'];?></td>
                                                </tr>

                                                <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                                    <td class="pl-0 f-w-700">TOTAL NET PAYABLE</td>
                                                    <td class="text-right text-primary"></td>
                                                    <td class="pl-0"></td>
                                                    <td class="text-right text-primary"><?php echo $payslip_data_list['net'];?></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="border-bottom w-100 mt-7 mb-13"></div>

                                </div>
                                <div class="col-md-3 border-left-md pl-md-10 py-md-10 text-right">
                                    <!--begin::Total Amount-->
                                    <div class="font-size-h4 font-weight-bolder text-muted mb-3">NET SALARY</div>
                                    <div class="font-size-h1 font-weight-boldest">₹<?php echo $payslip_data_list['net'];?></div>
                                    <div class="text-muted font-weight-bold mb-16">(<?php echo  numberToWordFormateRupees($payslip_data_list['net']);  ?> )</div>
                                    <!--end::Total Amount-->
                                    <div class="border-bottom w-100 mb-16"></div>
                                    <!--begin::Invoice To-->
                                    <div class="text-dark-50 font-size-lg font-weight-bold mb-3">EMPLOYEE </div>
                                    <div class="font-size-lg font-weight-bold mb-10"><span><?php echo $payslip_data_list['emp_number']; ?> - <?php echo $payslip_data_list['emp_name']; ?></span>
                                        <span class="opacity-70"><?php echo $payslip_data_list['designation']; ?></span>
                                    </div>

                                    <div class="text-dark-50 font-size-lg font-weight-bold mb-3">DATE OF JOINING</div>
                                    <div class="font-size-lg font-weight-bold"><?php
                                    echo date('F d, Y',strtotime($payslip_data_list['doj'])); ?></div>
                                    <!--end::Invoice To-->
                                    <!--begin::Invoice No-->
                                    <br>


                                    <!--end::Invoice Date-->
                                </div>
                            </div>
                            <!--end: Invoice body-->
                        </div>
                    </div>
                    <!-- begin: Invoice action-->
                    <div class="row justify-content-center py-8 px-8 py-md-28 px-md-0">
                        <div class="col-md-9">
                            <div class="d-flex font-size-sm flex-wrap">
                                <button type="button" class="btn btn-primary font-weight-bolder py-4 mr-3 mr-sm-14 my-1" onclick="window.print();">Print Payslip</button>
                                <button type="button" class="btn btn-light-primary font-weight-bolder mr-3 my-1">Download</button>

                            </div>
                        </div>
                    </div>
                    <!-- end: Invoice action-->
                    <!--end::Invoice-->
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>

</div>
<!--end::Content-->
