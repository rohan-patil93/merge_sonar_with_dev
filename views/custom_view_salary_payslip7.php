<?php

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
$main_ids = encodeId($_GET['Rid']);
$main_id = explode(":",$main_ids);
$cusurl = API."?mode=togetpayslipdetailsforsalarytemplate&salary_template_id=".$main_id['1'];
$cusresponse = callAPI($cusurl);
$payslip_data_list = json_decode($cusresponse,1);
$dataorg["where"] = array('company_id'=> $_SESSION['company_id'], 'is_active'=>'0');
$datw = urlencode(json_encode($dataorg));
$organization_name  = $_SESSION['is_data']['pay_area_config'] == 1 ? $_SESSION['pay_area_config']['name'] : $_SESSION['config']['name'];

?>

 <style type="text/css">
/*        td, th {white-space: normal;}*/
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

<div class="card p-5">
    <div class="card-body" id="printableArea">
        <table class="table table-hover table-bordered table-sm payslip_table" style="">
            <tr>
                <th scope="col" class="text-center f-24 p-5" colspan="4" style="">
                    <div style="width: 20%; float:left;">
                        &nbsp;
                    </div>
                    <div style="width: 60%; float:left; text-align: center;" class="f-20">
                        <?php echo $organization_name; ?>
                    </div>
                    <div style="width: 20%; float:left; text-align: right;">
                        <img src="uploads/all/<?php echo $_SESSION['is_data']['pay_area_config'] ? $_SESSION['pay_area_config']['display_logo'] : $_SESSION['config']['display_logo'];  ?>" style="width: 125px;" alt="Logo">
                    </div>

                </th>
            </tr>
            <tr>
                <th scope="col" class="text-center" colspan="3" style="">Salary Slip</th>
                <th scope="col" class="text-center" ><?php echo "Month-Year"; ?></th>
            </tr>
            <tr>
                <th scope="col" style="text-align: left; width: 20%;">Employee Name</th>
                <td style="text-align: left; width: 30%;"><?php echo $payslip_data_list['emp_name']; ?></td>
                <th scope="col" style="text-align: left; width: 20%;">Date of Joning</th>
                <th scope="col" class="text-end" style="width: 30%;">
                    <?php echo date('F d, Y',strtotime($payslip_data_list['doj'])); ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="text-align: left;">Employee Code</th>
                <td style="text-align: left;">
                    <?php echo $payslip_data_list['emp_number']; ?>
                </td>
                <th scope="col" style="text-align: left;">Total working days</th>
                <th scope="col" class="text-end" style="">
                    <?php echo "-"; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="text-align: left;">Designation</th>
                <td style="text-align: left;">
                <?php echo $payslip_data_list['designation']; ?>
                </td>
                <th scope="col" style="text-align: left;">Working days Attended</th>
                <th scope="col" class="text-end" style="">
                    <?php echo "-"; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="text-align: left;">PAN</th>
                <td style="text-align: left;">
                    <?php echo $payslip_data_list['pan_number']; ?>
                </td>
                <th scope="col" style="text-align: left;">LOP</th>
                <th scope="col" class="text-end" style="">
                    <?php echo "-"; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="text-align: left;">Bank Account Number</th>
                <td style="text-align: left;">
                    <?php echo $payslip_data_list['bank_account_no']; ?>
                </td>

                <th scope="col" style="text-align: left;">PF UAN</th>
                <th scope="col" class="text-end" style="">
                    <?php echo $payslip_data_list['pf_uan']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="text-align: left;">Bank Name</th>
                <td style="text-align: left;">
                    <?php echo $payslip_data_list['bank']; ?>
                </td>
                <th scope="col" style="text-align: left;">PF No</th>
                <th scope="col" class="text-end" style="">
                    <?php echo $payslip_data_list['pf']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="text-align: left;">Grade</th>
                <td style="text-align: left;">
                    <?php echo $payslip_data_list['grade']; ?>
                </td>
                <th scope="col" style="text-align: left;">Opted Under</th>
                <th scope="col" class="text-end" style="">New Scheme</th>
            </tr>
            <tr>
                <th scope="col" style="text-align: left;">Location</th>
                <td style="text-align: left;">
                    <?php echo $payslip_data_list['location']; ?>
                </td>
                <th scope="col" style="text-align: left;"></th>
                <th scope="col" class="text-end" style=""></th>
            </tr>

        </table>
        <table class="table table-hover table-bordered mt-3 table-sm" style="">
            <tr>
                <th scope="col" class="text-center" colspan="3" style="width: 50%;">Income</th>
                <th scope="col" class="text-center" colspan="3" style="text-align: center; width: 50%;">Deductions</th>
            </tr>
            <tr>
                <th scope="col" class="text-center" style="width: 18%;">Particulars</th>
                <th scope="col" class="text-center" style="width: 16%;">Amount</th>
                <th scope="col" class="text-center" style="width: 16%;">Annual</th>
                <th scope="col" class="text-center" style="width: 18%;">Particulars</th>
                <th scope="col" class="text-center" style="width: 16%;">Amount</th>
                <th scope="col" class="text-center" style="width: 16%;">Annual</th>
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
                <td style="text-align: left;">
                    <?php echo  $payslip_data_list['actual'][$i]['name']; ?>
                </td>
                <td style="text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['actual'][$i]['amount'],0)); ?>
                </td>
                <td style="text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['actual'][$i]['st_amount']*12,0)); ?>
                </td>
                <td style="text-align: left;">
                    <?php echo  $payslip_data_list['dedaction'][$i]['name']; ?>
                </td>
                <td style="text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['dedaction'][$i]['amount'],0)); ?>
                </td>
                <td style="text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['dedaction'][$i]['st_amount']*12,0)); ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <th scope="col" style="text-align: left;">Total</th>
                <th scope="col" class="text-end" style="">
                    <?php echo customePayslipNumberFormate($earnings); ?>
                </th>
                <th scope="col" class="text-end" style="">
                    <?php echo customePayslipNumberFormate($st_earnings*12); ?>
                </th>
                <th scope="col" style="text-align: left;">Total</th>
                <th scope="col" class="text-end" style="">
                    <?php echo customePayslipNumberFormate($dedaction); ?>
                </th>
                <th scope="col" class="text-end" style="">
                    <?php echo customePayslipNumberFormate($st_dedaction*12); ?>
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="4" style="background-color: #cccccc; text-align: center;">Net Salary</th>
                <th scope="col" class="text-end" style="">
                    <?php echo customePayslipNumberFormate($earnings-$dedaction); ?>
                </th>
                <th scope="col" class="text-end" style="">
                    <?php echo customePayslipNumberFormate(($st_earnings-$st_dedaction)*12); ?>
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="3" style="height: 60px; text-align: center;"></th>
                <th scope="col" colspan="3" style="height: 60px; text-align: center;"></th>
            </tr>
            <tr>
                <th scope="col" colspan="3" style="text-align: center;">Employee Signature</th>
                <th scope="col" colspan="3" style="text-align: center;">Employer Signature</th>
            </tr>
        </table>
    </div>
    <div class="card-body">
        <button type="button" class="btn btn-primary font-weight-bolder mr-3 mr-sm-14 my-1" onclick="printDiv('printableArea')" >Print Payslip</button>
    </div>
</div>
</div>
