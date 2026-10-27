<?php
if(isset($_REQUEST['ajaxpage']) && $_REQUEST['ajaxpage']!="" && $_REQUEST['employe_id']!=""){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}
$month =$_REQUEST['month'];
$year = $_REQUEST['year'];
if(isset($_REQUEST['emp_id'])){
    $employe_id = $_REQUEST['emp_id'];
}elseif (isset($_REQUEST['employe_id'])) {
    $employe_id = $_REQUEST['employe_id'];
}else{
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
$organization_name  = $_SESSION['is_data']['pay_area_config'] == 1 ? $_SESSION['pay_area_config']['name'] : $_SESSION['config']['name'];

if($yaer=="" || $month ==""){
    $year = $payslip_data_list['year'];
    $month = $payslip_data_list['no_year'];
}
$emp_data = $payslip_data_list['emp_data'];
$join_date = $emp_data['joining_date'];
$leav_date = $emp_data['date_of_leaving'];
$slip_date = date('Y-m-d',strtotime($year."-".$month."-01"));
$slip_last_date = date('Y-m-t',strtotime($slip_date));
$no_lopdays =0; $mdays =0;

if(strtotime($join_date) > strtotime($slip_date)){
    $mdays = dateDiffInDays($slip_date,$join_date);
    $no_lopdays = $no_lopdays + $mdays;
}
$mdays =0;
if($leav_date !="" && strtotime($slip_last_date)> strtotime($leav_date)){
    $mdays = dateDiffInDays($leav_date,$slip_last_date);
    $no_lopdays = $no_lopdays + $mdays;
}
if($_GET['viewcheck']==1){
 echo "<pre>"; print_r($payslip_data_list); echo "</pre>";
}
?>

    <style type="text/css">
/*        td, th {white-space: normal;}*/
        /*td, th { border: #CCCCCC solid 3px;}*/
        @media (orientation: portrait) {
            .container {width: 100%; border: none; padding: 0;}
        }
        @print{
            .container{100%;padding:0;border:none;}
        }
        .payslip_table td, .payslip_table th{font-size: 13px; padding: 5px !important;}
        @media (max-width:772px){
            .scan{width: 36%;}
        }
        .payslip_table{
            border-collapse : collapse;
        }
        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 1cm; /* Adjust as needed */
            }

            header, footer {
              display: none !important;
            }


          }
    </style>

    <script>
        function printDiv(divName) {
             var printContents = document.getElementById(divName).innerHTML;
             var originalContents = document.body.innerHTML;
             document.body.innerHTML = printContents;

             window.print();

             document.body.innerHTML = originalContents;
        }
    </script>

?>
<div class="card">
    <?php $current_date = date('Y-m-d'); // Get today's date
    if (strtotime($current_date) > strtotime($slip_last_date)) {?>
    <div class="card-body" id="printableArea">

        <table class="table table-hover table-bordered table-sm payslip_table"  style="width: 100%;">
            <tr>
                <th scope="col" class="text-center f-24 p-5" colspan="4" style="border: #CCCCCC solid 3px;">
                    <div style="width: 20%; float:left;">
                        &nbsp;
                    </div>
                    <div style="width: 60%; float:left; text-align: center;">
                        <?php echo $organization_name; ?> asasdasd
                    </div>
                    <div style="width: 20%; float:left; text-align: right;">
                        <img src="uploads/all/<?php echo $_SESSION['is_data']['pay_area_config'] == 1 ? $_SESSION['pay_area_config']['display_logo'] : $_SESSION['config']['display_logo'];  ?>" style="width: 125px;" alt="Logo">
                    </div>

                </th>
            </tr>
            <tr>
                <th scope="col" class="text-center" colspan="3" style="border: #CCCCCC solid 3px;">Salary Slip</th>
                <th scope="col" class="text-center"style="border: #CCCCCC solid 3px;" ><?php echo $payslip_data_list['month']." - ".$payslip_data_list['year'];?></th>
            </tr>
            <tr>
                <th scope="col"  style="border: #CCCCCC solid 3px; text-align: left; width: 20%;" >Employee Name</th>
                <td style="border: #CCCCCC solid 3px; text-align: left; width: 30%;"><?php echo $payslip_data_list['emp_name']; ?></td>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left; width: 20%;">Date of Joning</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px; width: 30%;">
                    <?php echo date('F d, Y',strtotime($payslip_data_list['doj'])); ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Employee Code</th>
                <td style="border: #CCCCCC solid 3px; text-align: left;">
                    <?php echo $payslip_data_list['emp_number']; ?>
                </td>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Total working days</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo $payslip_data_list['std']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Designation</th>
                <td style="border: #CCCCCC solid 3px; text-align: left;">
                <?php echo $payslip_data_list['designation']; ?>
                </td>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Working days Attended</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo $payslip_data_list['std']-$payslip_data_list['lop']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">PAN</th>
                <td style="border: #CCCCCC solid 3px; text-align: left;">
                    <?php echo $payslip_data_list['pan_number']; ?>
                </td>
                <th scope="col"style="border: #CCCCCC solid 3px; text-align: left;">LOP</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo $payslip_data_list['lop']-$no_lopdays; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Bank Account Number</th>
                <td style="border: #CCCCCC solid 3px; text-align: left;">
                    <?php echo $payslip_data_list['bank_account_no']; ?>
                </td>

                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">PF UAN</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo $payslip_data_list['pf_uan']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Bank Name</th>
                <td style="border: #CCCCCC solid 3px; text-align: left;">
                    <?php echo $payslip_data_list['bank']; ?>
                </td>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">PF No</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo $payslip_data_list['pf']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Grade</th>
                <td style="border: #CCCCCC solid 3px; text-align: left;">
                    <?php echo $payslip_data_list['grade']; ?>
                </td>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Opted Under</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">New Scheme</th>
            </tr>
            <tr>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Location</th>
                <td style="border: #CCCCCC solid 3px; text-align: left;">
                    <?php echo $payslip_data_list['location']; ?>
                </td>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;"></th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;"></th>
            </tr>

        </table>
        <table class="table table-hover table-bordered table-sm payslip_table mt-3" style="">
            <tr>
                <th scope="col" class="text-center" colspan="3" style="border: #CCCCCC solid 3px; width: 50%;">Income</th>
                <th scope="col" class="text-center" colspan="3" style="border: #CCCCCC solid 3px; text-align: center; width: 50%;">Deductions</th>
            </tr>
            <tr>
                <th scope="col" class="text-center" style="border: #CCCCCC solid 3px; width: 18%;">Particulars</th>
                <th scope="col" class="text-center" style="border: #CCCCCC solid 3px; width: 16%;">Amount</th>
                <th scope="col" class="text-center" style="border: #CCCCCC solid 3px; width: 16%;">Annual</th>
                <th scope="col" class="text-center" style="border: #CCCCCC solid 3px; width: 18%;">Particulars</th>
                <th scope="col" class="text-center" style="border: #CCCCCC solid 3px; width: 16%;">Amount</th>
                <th scope="col" class="text-center" style="border: #CCCCCC solid 3px; width: 16%;">Annual</th>
            </tr>
            <?php
                $actual_count= count((array)$payslip_data_list['actual']);
                $dedaction_count= count((array)$payslip_data_list['dedaction']);
                $count = ($actual_count>$dedaction_count) ? $actual_count : $dedaction_count;
                $earnings = 0;
                $dedaction = 0;
                $st_earnings = 0;
                $st_dedaction = 0;
                for($i=0; $i<$count;$i++) {
                    $earnings = $earnings+round($payslip_data_list['actual'][$i]['amount'],0);
                    $dedaction = $dedaction+ round($payslip_data_list['dedaction'][$i]['amount'],0);
                    $st_earnings = $st_earnings+round($payslip_data_list['actual'][$i]['st_amount'],0);
                    $st_dedaction = $st_dedaction+ round($payslip_data_list['dedaction'][$i]['st_amount'],0);
                ?>
            <tr>
                <td style="border: #CCCCCC solid 3px; text-align: left;">
                    <?php echo  $payslip_data_list['actual'][$i]['name']; ?>
                </td>
                <td style="border: #CCCCCC solid 3px; text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['actual'][$i]['amount'],0)); ?>
                </td>
                <td style="border: #CCCCCC solid 3px; text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['actual'][$i]['st_amount']*12,0)); ?>
                </td>
                <td style="border: #CCCCCC solid 3px; text-align: left;">
                    <?php echo  $payslip_data_list['dedaction'][$i]['name']; ?>
                </td>
                <td style="border: #CCCCCC solid 3px; text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['dedaction'][$i]['amount'],0)); ?>
                </td>
                <td style="border: #CCCCCC solid 3px; text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['dedaction'][$i]['st_amount']*12,0)); ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Total</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo customePayslipNumberFormate($earnings); ?>
                </th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo customePayslipNumberFormate($st_earnings*12); ?>
                </th>
                <th scope="col" style="border: #CCCCCC solid 3px; text-align: left;">Total</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo customePayslipNumberFormate($dedaction); ?>
                </th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo customePayslipNumberFormate($st_dedaction*12); ?>
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="4" style="background-color: #cccccc; border: #CCCCCC solid 3px; text-align: center;">Net Salary</th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo customePayslipNumberFormate($earnings-$dedaction); ?>
                </th>
                <th scope="col" class="text-end" style="border: #CCCCCC solid 3px;">
                    <?php echo customePayslipNumberFormate(($st_earnings-$st_dedaction)*12); ?>
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="3" style="height: 60px;border: #CCCCCC solid 3px; text-align: center;"></th>
                <th scope="col" colspan="3" style="height: 60px;border: #CCCCCC solid 3px; text-align: center;"></th>
            </tr>
            <tr>
                <th scope="col" colspan="3" style="border: #CCCCCC solid 3px; text-align: center;">Employee Signature</th>
                <th scope="col" colspan="3" style="border: #CCCCCC solid 3px; text-align: center;">Employer Signature</th>
            </tr>
        </table>
    </div>
    <div class="card-body">
        <button type="button" class="btn btn-primary font-weight-bolder mr-3 mr-sm-14 my-1" onclick="printDiv('printableArea')" >Print Payslip</button>
    </div>
<?php }else{ ?>

    <div class="text-center text-warning f-w-800">
        It is in process
    </div>
    <div class="text-center px-4">
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
<?php } ?>
</div>
</div>
