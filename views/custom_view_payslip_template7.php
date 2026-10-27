<?php

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

$payslip_data_list = $_REQUEST['emp_data'];

$organization_name  =  $_SESSION['is_data']['pay_area_config'] == 1 ? $_SESSION['pay_area_config']['name'] : $_REQUEST['config']['name'];
function customePayslipNumberFormate($number){
    if($number!=0){
        $format = '0.00';
        if (!empty($_SESSION['is_data']['pay_area_config']) && $_SESSION['is_data']['pay_area_config'] == 1 && !empty($_SESSION['pay_area_config']['number_format'])) {
            $format = $_SESSION['pay_area_config']['number_format'];
        } elseif (!empty($_REQUEST['config']['number_format'])) {
            $format = $_REQUEST['config']['number_format'];
        }
        $expoldform = explode('.',$format);
        if($expoldform[0]== 1){
            $formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
            $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $expoldform[1]);
            $formattedNumber = $formatter->formatCurrency($number, 'INR');
            $formattedCurrencyWithoutSymbol = trim(str_replace($formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL), '', $formattedNumber));
             $res = $formattedCurrencyWithoutSymbol;
        }else{
             $res = number_format($number, $expoldform[1], '.', ',');
        }

    }else{
        $res = "-";
    }
    return $res;
}
?>

<div class="card">
    <div class="card-body" id="printableArea">
        <table  style="font-size: 13px; padding: 5px !important; border: 1px solid #cccccc; width: 100%;">
            <tr>
                <th scope="col" colspan="2" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; "><?php echo $organization_name; ?>

                </th>
                <th scope="col" colspan="2" style="text-align: right;padding: 5px !important;border: 1px solid #cccccc; ">
                    <img src="uploads/all/<?php echo  $_SESSION['is_data']['pay_area_config'] == 1 ? $_SESSION['pay_area_config']['display_logo'] : $_REQUEST['config']['display_logo'];  ?>" style="width: 125px;" alt="Logo">
                </th>
            </tr>
            <tr>
                <th scope="col" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; " colspan="3" >Salary Slip</th>
                <th scope="col" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; " colspan="3"  ><?php echo $payslip_data_list['month']." - ".$payslip_data_list['year'];?></th>
            </tr>
            <tr>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left; width: 20%;" >Employee Name</th>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left; width: 30%;"><?php echo $payslip_data_list['emp_name']; ?></td>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left; width: 20%;">Date of Joning</th>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: right; width: 30%;">
                    <?php echo date('F d, Y',strtotime($payslip_data_list['doj'])); ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Employee Code</th>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">
                    <?php echo $payslip_data_list['emp_number']; ?>
                </td>
                <th scope="col"style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Total working days</th>
                <th scope="col" class="text-end" style="padding: 5px !important;border: 1px solid #cccccc; ">
                    <?php echo $payslip_data_list['std']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Designation</th>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">
                <?php echo $payslip_data_list['designation']; ?>
                </td>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Working days Attended</th>
                <th scope="col" class="text-end" style="padding: 5px !important;border: 1px solid #cccccc; ">
                    <?php echo $payslip_data_list['std']-$payslip_data_list['lop']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">PAN</th>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">
                    <?php echo $payslip_data_list['pan_number']; ?>
                </td>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">LOP</th>
                <th scope="col" class="text-end" style="padding: 5px !important;border: 1px solid #cccccc; ">
                    <?php echo $payslip_data_list['lop']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Bank Account Number</th>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">
                    <?php echo $payslip_data_list['bank_account_no']; ?>
                </td>

                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">PF UAN</th>
                <th scope="col" class="text-end" style="padding: 5px !important;border: 1px solid #cccccc; ">
                    <?php echo $payslip_data_list['pf_uan']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Bank Name</th>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">
                    <?php echo $payslip_data_list['bank']; ?>
                </td>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">PF No</th>
                <th scope="col" class="text-end" style="padding: 5px !important;border: 1px solid #cccccc; ">
                    <?php echo $payslip_data_list['pf']; ?>
                </th>
            </tr>
            <tr>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Grade</th>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">
                    <?php echo $payslip_data_list['grade']; ?>
                </td>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Opted Under</th>
                <th scope="col" class="text-end" style="padding: 5px !important;border: 1px solid #cccccc; ">New Scheme</th>
            </tr>
            <tr>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Location</th>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">
                    <?php echo $payslip_data_list['location']; ?>
                </td>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;"></th>
                <th scope="col" class="text-end" style="padding: 5px !important;border: 1px solid #cccccc; "></th>
            </tr>

        </table>
        <table style="font-size: 13px; padding: 5px !important; border: 1px solid #cccccc;width: 100%;">
            <tr>
                <th scope="col" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; width: 50%;" colspan="3" >Income</th>
                <th scope="col" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; text-align: center; width: 50%;" colspan="3" >Deductions</th>
            </tr>
            <tr>
                <th scope="col" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; width: 18%;">Particulars</th>
                <th scope="col" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; width: 16%;">Amount</th>
                <th scope="col" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; width: 16%;">Annual</th>
                <th scope="col" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; width: 18%;">Particulars</th>
                <th scope="col"style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; width: 16%;">Amount</th>
                <th scope="col" style="text-align: center;padding: 5px !important;border: 1px solid #cccccc; width: 16%;">Annual</th>
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
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">
                    <?php echo  $payslip_data_list['actual'][$i]['name']; ?>
                </td>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['actual'][$i]['amount'],0)); ?>
                </td>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['actual'][$i]['st_amount']*12,0)); ?>
                </td>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">
                    <?php echo  $payslip_data_list['dedaction'][$i]['name']; ?>
                </td>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['dedaction'][$i]['amount'],0)); ?>
                </td>
                <td style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
                    <?php echo  customePayslipNumberFormate(round($payslip_data_list['dedaction'][$i]['st_amount']*12,0)); ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Total</th>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
                    <?php echo customePayslipNumberFormate($earnings); ?>
                </th>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
                    <?php echo customePayslipNumberFormate($st_earnings*12); ?>
                </th>
                <th scope="col"style="padding: 5px !important;border: 1px solid #cccccc; text-align: left;">Total</th>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
                    <?php echo customePayslipNumberFormate($dedaction); ?>
                </th>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
                    <?php echo customePayslipNumberFormate($st_dedaction*12); ?>
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="4" style="background-color: #cccccc; text-align: center;">Net Salary</th>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
                    <?php echo customePayslipNumberFormate($earnings-$dedaction); ?>
                </th>
                <th scope="col" style="padding: 5px !important;border: 1px solid #cccccc; text-align: right;">
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
</div>
</div>
