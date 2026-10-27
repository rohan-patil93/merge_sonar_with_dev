<?php
if(isset($_GET['ajaxpage']) && $_GET['ajaxpage']!=""){
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
}
$month =$_GET['month'];
$year = $_GET['year'];
if($_GET['employe_id']){
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
function valueemtpydash($val){
    $val = $val ?? [];
    return $val==""?" - ":$val;
}
?>

<script>
function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;
     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>
<!--begin::Content-->
<?php if($payslip_data_list['actual']['0']['name']!=""){ ?>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container template3 p-1">
            <div class="card card-custom">
                <div class="card-body p-0">
                    <!--begin::Invoice-->
                    <div id="printableArea">
                    <div class="row justify-content-center p-4">
                        <div class="col-md-12">
                            <!-- begin: Invoice header-->
                            <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                                <table class="table table-responsive payslip-table">
                                    <tbody>
                                        <tr>
                                            <th scope="col" style="text-align: left;">
                                                <h3 class="font-weight-boldest m-t-30 text-primary m-r-50">PAYSLIP FOR  <?php echo $payslip_data_list['month']." ".$payslip_data_list['year'];?></h3>
                                                <p>  <?php echo $payslip_data_list['emp_name']; ?></p>
                                            </th>

                                            <th scope="col" style="text-align: right;">
                                                <img src="uploads/all/<?php echo $organization_logo; ?>" style="width: 150px;" alt="" />
                                                <!--end::Logo-->
                                                <span class="d-flex flex-column align-items-md-end font-size-lg font-weight-bold text-muted" style="word-break: break-word;white-space: initial;">
                                                    <span><?php echo $organization_nam; ?> </span>
                                                    <span> <?php echo $organization_address; ?></span>
                                                    <span><?php echo $org_location; ?></span>
                                                </span>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                            <!--end: Invoice header-->
                            <!--begin: Invoice body-->
                            <div class="border-bottom pb-10">
                                <table role="presentation" class="table table-responsive" style="width: 100%;">
                                    <tr>
                                        <td>
                                            <div class="col-md-12 py-md-10 pr-md-10">
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
                                                for($i=0; $i<$count;$i++) {  ?>
                                                    <tr class="font-weight-normal font-size-lg">
                                                        <?php if( $payslip_data_list['actual'][$i]['display_type'] ==0 || ($payslip_data_list['actual'][$i]['display_type']==2 && $payslip_data_list['display_type'][$i]['amount']!=0) ){ ?>
                                                        <td class="p-2">
                                                            <?php echo  $payslip_data_list['actual'][$i]['name']; ?>
                                                        </td>
                                                        <td class="text-right p-2">
                                                            <?php
                                                            echo  $payslip_data_list['display_type'][$i].valueemtpydash(customeummarmonyformateround($payslip_data_list['actual'][$i]['amount'])); ?>
                                                        </td>
                                                        <?php }
if( $payslip_data_list['display_type'][$i] ==0 || ($payslip_data_list['display_type'][$i]==2 && $payslip_data_list['display_type'][$i]!=0.00) ){ ?>
                                                        <td class="p-2">
                                                            <?php
                                                            echo  $payslip_data_list['dedaction'][$i]['name']; ?>
                                                        </td>
                                                        <td class="p-2 text-right">
                                                            <?php echo  $payslip_data_list['dedaction'][$i]['amount']!=""?valueemtpydash(customeummarmonyformateround($payslip_data_list['dedaction'][$i]['amount'])):""; ?>
                                                        </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>

                                                <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                                    <td class="p-2">GROSS EARNINGS</td>
                                                    <td class="text-right p-2 text-primary"><?php echo $payslip_data_list['actual_amount'];?></td>
                                                    <td class="p-2">TOTAL DEDUCTIONS</td>
                                                    <td class="text-right p-2 text-danger"><?php echo $payslip_data_list['dedaction_amount'];?></td>
                                                </tr>

                                                <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                                    <td class="p-2">TOTAL NET PAYABLE</td>
                                                    <td class="text-right p-2 text-primary"></td>
                                                    <td class="p-2"></td>
                                                    <td class="text-right p-2 text-primary"><?php echo $payslip_data_list['net'];?></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="border-bottom w-100 mt-7 mb-13"></div>
                                    <div class="d-flex flex-column flex-md-row">
                                        <div class="d-flex flex-column mb-10 mb-md-0">
                                            <div class="font-weight-bold font-size-h6 mb-3">BANK TRANSFER</div>
                                            <div class="d-flex justify-content-between font-size-lg mb-3">
                                                <span class="font-weight-bold mr-15">Bank Name:</span>
                                                <span class="text-right"><?php echo $payslip_data_list['bank']; ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between font-size-lg mb-3">
                                                <span class="font-weight-bold mr-15">Account Number:</span>
                                                <span class="text-right">
                                                    <?php $len = strlen($payslip_data_list['bank_account_no']);
                                                    echo "XXXXXXXXX".substr($payslip_data_list['bank_account_no'],$len-4,4); ?>
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span class="mr-15 font-weight-bold">PF A/C:</span>
                                                <span class="text-right"><?php echo $payslip_data_list['pf']; ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span class="mr-15 font-weight-bold">UAN:</span>
                                                <span class="text-right"><?php echo $payslip_data_list['pf_uan']; ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span class="mr-15 font-weight-bold">PAN:</span>
                                                <span class="text-right"><?php echo $payslip_data_list['pan_number']; ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span class="mr-15 font-weight-bold">ESI:</span>
                                                <span class="text-right"><?php echo $payslip_data_list['esi']; ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span class="mr-15 font-weight-bold">PRAN:</span>
                                                <span class="text-right"><?php echo $payslip_data_list['pran']; ?></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                </td>
                                <td style="">
                                    <div class="col-md-12 border-left-md pl-md-10 py-md-10 text-right word-break" style="">
                                        <!--begin::Total Amount-->
                                        <div class="font-size-h4 font-weight-bolder text-muted mb-3" style="margin-top: 23px;">NET SALARY</div>
                                        <div class="font-size-h1 font-weight-boldest">₹<?php echo $payslip_data_list['net'];?></div>
                                        <?php $netamountreplace =str_replace(',','',$payslip_data_list['net']); ?>
                                        <div class="text-muted font-weight-bold mb-16 p-b-35">(<?php echo  numberToWordFormateRupees($netamountreplace);  ?> )</div>
                                        <!--end::Total Amount-->
                                        <div class="border-bottom w-100 mb-16"></div>
                                        <!--begin::Invoice To-->
                                        <div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 p-t-35 font-weight-bolder">EMPLOYEE </div>
                                        <div class="font-size-lg font-weight-bold mb-10">
                                            <span><?php echo $payslip_data_list['emp_number']; ?> - <?php echo $payslip_data_list['emp_name']; ?></span></br>
                                            <span class="opacity-70"><?php echo $payslip_data_list['designation']; ?></span></br>
                                            <span class="opacity-70"><?php echo $payslip_data_list['department']; ?></span>
                                        </div>
                                        <div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 font-weight-bolder">DATE OF JOINING</div>
                                        <div class="font-size-lg font-weight-bold"><?php
                                        echo date('F d, Y',strtotime($payslip_data_list['doj'])); ?></div>
                                        <!--end::Invoice To-->
                                        <!--begin::Invoice No-->

                                        <div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 font-weight-bolder">STD DAYS</div>
                                        <div class="font-size-lg font-weight-bold mb-10"><?php echo $payslip_data_list['std']; ?></div>
                                        <!--end::Invoice No-->
                                        <!--begin::Invoice Date-->
                                        <div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 font-weight-bolder">WORKED DAYS</div>
                                        <div class="font-size-lg font-weight-bold"><?php echo $payslip_data_list['work']; ?></div>

                                        <div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 font-weight-bolder">LWP DAYS</div>
                                        <div class="font-size-lg font-weight-bold mb-10"><?php echo $payslip_data_list['lop']!=""?$payslip_data_list['lop']:"-"; ?></div>
                                        <!--end::Invoice Date-->
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!--end: Invoice body-->
                    </div>
                    </div>
                    </div>
                    <!-- begin: Invoice action-->
                    <div class="row justify-content-center">
                        <div class="col-md-9">
                            <div class="d-flex font-size-sm flex-wrap p-t-10 p-b-10">
                                <button type="button" class="btn btn-primary font-weight-bolder mr-3 mr-sm-14 my-1" onclick="printDiv('printableArea')" >Print Payslip</button>
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
<?php }else{ ?>
<div class="fs-30 text-center text-danger f-18 "> Payslip is not available </div>
<div class="text-center text-danger ">
    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
</div>
<?php } ?>
