<?php
if($_GET['print'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $callapi = "Y";
}else{
    include_once 'dbconfig.php';
}
if($_POST['callapi'] =='Y'){
    $config = $_POST['config'];
    $transaction = $_POST['rental_invoices'];
    $transaction_items = $_POST['rental_invoice_items'];
    $bill_by = $_POST['bill_by'];
    $bank_accounts = $_POST['bank_accounts'];
    $items  = $_POST['items'];
    $callapi = "Y";
}else{
    $rid = $_GET['rid'];
    $table = $_GET['table'];
    $api = API."?mode=getdynamicinvoiceprint&transaction_id=".$rid."&transaction_table=".$table;
    $apicall = callAPI($api);
    $result = json_decode($apicall, 1);
    $config = $result['config'];
    $transaction = $result['rental_invoices'];
    $transaction_items = $result['rental_invoice_items'];
    $bill_by = $result['bill_by'];
    $bank_accounts = $result['bank_accounts'];
    $_SESSION['pdf_file_name'] = $table;
    $items  = $result['items'];
}
$party_additional_details = json_decode($transaction['party_additional_details'],1);
function currencyFormat($number,$config){
    if($number !=""){
        $expoldform = explode('.',$config['number_format']);
        if($expoldform[0]== 1){
            $formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
            $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $expoldform[1]);
            return $formatter->formatCurrency($number, 'INR');
        }else{
            return number_format($number, $expoldform[1], '.', ',');
        }
    }
}
function numberToWordFormate($number){

    $no = floor($number ?? 0);
    $point = (int) round((($number ?? 0) - $no) * 100);
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count((array)$str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
        } else $str[] = null;
    }
    $str = array_reverse((array)$str);
    $result = implode('', (array)$str);
    $points = ($point) ?
    ". " . $words[(int)($point / 10)] . " " . $words[$point = $point % 10] : '';
    if($result !=''){
        $result = $result . "Rupees  ";
    }
    if($points !=''){
        $result.= $points . " Paise  ";
    }
    return ucwords($result." only");
}
function emptyCheck($valu){
    $value = "";
    if($valu !=""){
        $value = $valu;
    }else{
        $value = " - ";
    }
    return $value;
}
?>
<script>
    function printDiv(divName) {
       var printContents = document.getElementById(divName).innerHTML;
       var originalContents = document.body.innerHTML;
                // document.getElementById('header').style.display = 'none';
                 //document.getElementById('footer').style.display = 'none';
       document.body.innerHTML = printContents;

       window.print();

       document.body.innerHTML = originalContents;
   }
</script>
<!-- [ navigation menu ] end -->



<div class="card" style="width: 793px; margin: 0 auto;">
    <div class="card-body" id="printableArea">

        <!-- Required Fremwork -->
        <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
        <style type="text/css">
            @media (orientation: portrait) {
                .container {width: 100%; border: none; padding: 0;}
            }
            @print{
                .container{100%;padding:0;border:none;}
            }
            td, th{font-size: 13px; padding: 5px !important; vertical-align: middle; white-space: normal !important;}
            @media (max-width:772px){
                .scan{width: 36%;}
            }
            table{
                border-collapse : collapse;
            }
            .swatches{width: 20px; height: 20px; border-radius: 50px; border: 1px solid #cccccc; display: inline-block;}
        </style>

        <div class="table-responsive">
            <table class="table table-card">
                <tr>
                    <td width="20%" class="p-1 border-0" style="vertical-align:top;">
                        <img src="uploads/all/file_1704095913_659270a975d4a.png" style="width:100px; height: 34px;" alt="" >
                    </td>
                    <td width="25%" align="center" class="p-1 border-0 f-12" style="vertical-align:top; padding-right: 40px !important;">
                        <span><?= __('THANK YOU FOR YOUR ORDER. THIS DOCUMENT IS A RECEIPT OF YOUR PURCHASE.'); ?></span>
                    </td>
                    <td width="55%" colspan="4" class="border-0 p-1">
                        <div class="table-responsive">
                            <table role="presentation" class="table">
                                <tr>
                                    <td class="f-12"><?= __('TO'); ?></td>
                                    <td class="f-12" align="right">
                                        <span><?= __('FIRST NAME'); ?></span>
                                        <span class="m-l-10"><?= __('LAST NAME'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="f-12"><?= __('CUSTOMER ID'); ?></td>
                                    <td class="f-12" align="right">
                                        <span>12345</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="f-12"><?= __('DATE'); ?></td>
                                    <td class="f-12" align="right">
                                        <span><?= __('DD-MM-YY'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="f-12"><?= __('ORDER NO'); ?></td>
                                    <td class="f-12" align="right">
                                        <span><?= __('T-TF'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="f-12"><?= __('NUMBER OF ITEMS'); ?></td>
                                    <td class="f-12" align="right">
                                        <span>3</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="p-1 border-0" style="vertical-align:bottom;">
                        <img src="uploads/all/file_1704095913_659270a975d4a.png" style="width:100px; height: 34px;" alt="" >
                    </td>
                    <td align="center" class="p-1 pt-5 border-0 f-12" style="vertical-align:bottom; padding-right: 40px !important;">
                        <span><?= __('SAR STUDIO WWW.SAR STUDIO.COM +91 000 000 0000'); ?></span>
                    </td>
                    <td colspan="4" class="p-2 border-0 f-12" style="vertical-align:bottom;">
                        <span><?= __('PRODUCT PURCHASED'); ?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">&nbsp</td>
                </tr>
                <tr bgcolor="#eeeeee">
                    <th scope="col" class="p-1 f-12"><?= __('ITEM'); ?></th>
                    <th scope="col" class="p-1 f-12"></th>
                    <th scope="col" class="p-1 f-12" width="20%"><?= __('PRODUCTION DESCRIPTION'); ?></th>
                    <th scope="col" class="p-1 text-end f-12" width="5%"><?= __('QTY'); ?></th>
                    <th scope="col" class="text-end p-1 f-12" width="12%"><?= __('UNIT PRICE'); ?> <span class="d-block"><?= __('(INR)'); ?></span></th>
                    <th scope="col" class="text-end p-1 f-12" width="13%"><?= __('AMOUNT'); ?> <span class="d-block"><?= __('(INR)'); ?></span></th>
                </tr>
                <tr>
                    <td class="p-1" style="vertical-align:top;">
                        <img style="width: 100px; height:85px;" class="img-fluid" src="uploads/all/file_1688986265_64abe2999f741.jpg" alt="<?= htmlspecialchars(__('item'), ENT_QUOTES, 'UTF-8'); ?>" >
                    </td>
                    <td align="" class="p-1" style="vertical-align:top;">
                    </td>
                    <td class="p-1" style="vertical-align:top;">
                        <span class="d-block f-12"><?= __('BLANKET SOFA'); ?></span>
                        <span class="d-block f-12"><?= __('S-15-F1-BL-15-15, BLANKET SOFA, Sofa 1 Seater, DM1 - Toak Natural, DM2 - Seat Nougat'); ?></span>
                    </td>
                    <td class="p-1 f-12" align="right" style="vertical-align:top;">15</td>
                    <td class="p-1" align="right" style="vertical-align:top;"><b class="f-w-600 f-12">40,000,0000/-</b></td>
                    <td align="right" style="vertical-align:top;"><b class="f-w-600 f-12">40,000,0000/-</b></td>
                </tr>
                <tr>
                    <td class="p-1" style="vertical-align:top;">
                        <img style="width: 100px;height: 85px;" class="img-fluid" src="uploads/all/file_1688986555_64abe3bb28d5f.jpg" alt="<?= htmlspecialchars(__('item'), ENT_QUOTES, 'UTF-8'); ?>" >
                    </td>
                    <td align="" class="p-1" style="vertical-align:top;">
                    </td>
                    <td class="p-1" style="vertical-align:top;">
                        <span class="d-block f-12"><?= __('BLANKET SOFA'); ?></span>
                        <span class="d-block f-12"><?= __('S-15-F1-BL-15-15, BLANKET SOFA, Sofa 1 Seater, DM1 - Toak Natural, DM2 - Seat Nougat'); ?></span>
                    </td>
                    <td class="p-1 f-12" align="right" style="vertical-align:top;">12</td>
                    <td class="p-1" align="right" style="vertical-align:top;"><b class="f-w-600 f-12">40,000/-</b></td>
                    <td align="right" style="vertical-align:top;"><b class="f-w-600 f-12">40,000/-</b></td>
                </tr>
                <tr>
                    <td class="p-1" style="vertical-align:top;">
                        <img style="width: 100px;" class="img-fluid" src="uploads/all/file_1688973992_64abb2a8a5325.jpg" alt="<?= htmlspecialchars(__('item'), ENT_QUOTES, 'UTF-8'); ?>" >
                    </td>
                    <td align="" class="p-1" style="vertical-align:top;">
                    </td>
                    <td class="p-1" style="vertical-align:top;">
                        <span class="d-block f-12"><?= __('BLANKET SOFA'); ?></span>
                        <span class="d-block f-12"><?= __('S-15-F1-BL-15-15, BLANKET SOFA, Sofa 1 Seater, DM1 - Toak Natural, DM2 - Seat Nougat'); ?></span>
                    </td>
                    <td class="p-1" align="right" style="vertical-align:top;">100</td>
                    <td class="p-1" align="right" style="vertical-align:top;"><b class="f-w-600 f-12">40,000/-</b></td>
                    <td class="p-1" align="right" style="vertical-align:top;"><b class="f-w-600 f-12">40,000/-</b></td>
                </tr>
                <tr>
                    <td colspan="2" align="center" class="p-1 border-0">
                        -
                    </td>
                    <td colspan="4" class="border-0 p-1">
                            <table class="table">
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1" width="50%"><?= __('TOTAL'); ?></th>
                                    <th scope="col" width="50%" class="text-right f-12 p-0 pt-1 pb-1">
                                        <?= __('INR 1,20,000/'); ?>-
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1">10% <?= __('DISCOUNT'); ?></th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1">
                                        <?= __('INR 12,000/'); ?>-
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1"><?= __('TAXABLE AMOUNT'); ?></th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1">
                                        <?= __('INR 1,08,000/'); ?>-
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1"><?= __('CGST 9% (HSN CODE)'); ?></th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1">
                                        <?= __('INR 10,000/'); ?>-
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1"><?= __('SGST 9% (HSN CODE)'); ?></th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1">
                                        <?= __('INR 10,000/'); ?>-
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1"><?= __('GRAND TOTAL'); ?></th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1">
                                        <?= __('INR 1,35,000/'); ?>-
                                    </th>
                                </tr>
                                <tr>
                                    <td class="f-12 p-0 pt-1 pb-1" colspan="2"><?= __('ONE LAKH THIRTY FIVE THOUSAND RUPEES ONLY'); ?></td>
                                </tr>
                            </table>
                    </td>
                </tr>
                <tr>
                    <td class="border-0"></td>
                    <td colspan="" class="text-center border-0 pt-4 f-12" style="padding-right: 40px !important; vertical-align: bottom;">
                        <?= __('REGISTERED OFFICE ADDRESS: 3RD FLOOR, PLOT NO-39 LEFT BHUSURI COLONY KOTHRUD, PUNE-411038'); ?>
                    </td>
                    <td colspan="4" class="border-0 pt-4" style=" vertical-align: bottom;">
                        <span class="f-12"><?= __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s'); ?></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="card-body">
        <button type="button" class="btn btn-primary font-weight-bolder mr-3 mr-sm-14 my-1" onclick="printDiv('printableArea')" ><?= __('Print Invoice'); ?></button>
    </div>
</div>

<!-- [ style Customizer ] start -->
