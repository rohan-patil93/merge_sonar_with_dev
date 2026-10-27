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
    $rental_invoice_item_taxes = $_POST['rental_invoice_item_taxes'];
    $bill_by = $_POST['bill_by'];
    $bank_accounts = $_POST['bank_accounts'];
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
    $rental_invoice_item_taxes = $result['rental_invoice_item_taxes'];
    $bill_by = $result['bill_by'];
    $bank_accounts = $result['bank_accounts'];
    $_SESSION['pdf_file_name'] = $table;
    $tax_authorities_rates_split = $result['tax_authorities_rates_split'];

}

$dutax = array();
$distax = array();
foreach ((array)$rental_invoice_item_taxes as  $taxvalue) {
   if(in_array(number_format($taxvalue['tax_value'],2), (array)$dutax)){
       $distax[$taxvalue['tax_value']] ['amount'] =  $distax[$taxvalue['tax_value']] ['amount'] + $taxvalue['amount'];
       $distax[$taxvalue['tax_value']] ['tax_amount'] =  $distax[$taxvalue['tax_value']] ['tax_amount'] +$taxvalue['tax_amount'];
   }else{
        $dutax []= number_format($taxvalue['tax_value'],2);
        $distax[$taxvalue['tax_value']] ['percentage'] =  $taxvalue['tax_value'];
        $distax[$taxvalue['tax_value']] ['amount'] =  $taxvalue['amount'];
        $distax[$taxvalue['tax_value']] ['tax_amount'] =  $taxvalue['tax_amount'];
   }
}

// Split Should Come for Tax Autharities split part
if($transaction['originator_state_id'] == $transaction['supplier_state_id']){
    $split_1_name = $tax_authorities_rates_split[0]['split_name'];
    $split_2_name = $tax_authorities_rates_split[1]['split_name'];
    $split_1 = $tax_authorities_rates_split[0]['tax_rate'];
    $split_2 = $tax_authorities_rates_split[1]['tax_rate'];
    $split_1_gl_account_code =  $tax_authorities_rates_split[0]['account_code'];
    $split_2_gl_account_code =  $tax_authorities_rates_split[1]['account_code'];
}else{
    $split_1_name = $tax_authorities_rates_split[2]['split_name'];
    $split_1 = $tax_authorities_rates_split[2]['tax_rate'];
    $split_1_gl_account_code =  $tax_authorities_rates_split[2]['account_code'];
}


$party_additional_details = json_decode($transaction['party_additional_details'],1);
function currencyFormat($number,$config){
    if($number !=""){
        $expoldform = explode('.',$config['number_format']);
        if($expoldform[0]== 1){
            $formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
            $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $expoldform[1]);
            $formattedNumber = $formatter->formatCurrency($number, 'INR');
            $formattedCurrencyWithoutSymbol = trim(str_replace($formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL), '', $formattedNumber));
            return $formattedCurrencyWithoutSymbol;
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
        } else {
            $str[] = null;
        }
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
    echo ucwords($result." only");
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
<?php if($callapi !='Y'){ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Quotation Print</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <style type="text/css">
        @media (orientation: portrait) {
            .container {width: 100%; border: none; padding: 0;}
        }
        @print{
            .container{100%;padding:0;border:none;}
        }
        td, th{

            font-size: 12px;
        }
        @media (max-width:772px){
            .scan{width: 36%;}
        }
        table{
            border-collapse : collapse;
        }
    </style>
    <script type="text/javascript">
        function printDiv(divId) {
           var printContents = document.getElementById(divId).innerHTML;
           var originalContents = document.body.innerHTML;
           document.body.innerHTML = "<html><head><title>Quotation Print</title></head><body>" + printContents + "</body>";
           window.print();
           document.body.innerHTML = originalContents;
        }
    function convertHTMLToPDF() {
            var  title ='<?php echo urldecode($_GET['title']); ?>';
            var htmlContent = $('#printDiv').html();
            $.ajax({
                url: 'convert_html_to_pdf.php',
                type: 'POST',
                data: { html_content: htmlContent,title:title },
                success: function(pdfdetail) {
                    var result = JSON.parse(pdfdetail);
                    var link = document.createElement('a');
                    link.href = result.pdf_url;
                    link.download =result.pdf_name;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    $.ajax({
                        url: 'convert_html_to_pdf.php',
                        type: 'POST',
                        data: 'removepdf='+result.pdf_name,
                        success: function(res) {
                            console.log(res);
                        }
                    });
                }
            });
        }
    </script>
</head>
<body style="margin: 0;padding: 0;font-family: Arial;font-size: 12px;">
    <div class="" style="width: 793px;margin: 0 auto;"> <?php } ?>
<div class="card" style="width: 793px; margin: 0 auto;">
    <div class="card-body" id="printDiv">

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
                    <th scope="col" class="p-1 border-0" style="text-align:center; width: 100%;" colspan="12">
                        <h5><?php echo strtoupper($_GET['title']); ?></h5>
                        <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px;">
                            <?php echo $config['contact_address']; ?>
                        </div>
                    </th>
                </tr>

                <tr>
                    <td class="p-1 border-0" style="vertical-align:top; width: 20%;">
                        <img src="uploads/all/file_1704095913_659270a975d4a.png" style="width:100px; height: 34px;" alt="Attachment Preview">
                    </td>
                    <td class="p-1 border-0 f-12" style="vertical-align:top; padding-right: 40px !important; text-align: center; width: 25%;">

                    </td>
                    <td colspan="4" class="border-0 p-1" style="width: 55%;">
                        <div class="table-responsive">
                            <table role="presentation" class="table">
                                <tr>
                                    <td class="f-12">TO</td>
                                    <td class="f-12" style="text-align: right;">
                                        <?php echo ucwords($transaction['party_name']); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="f-12">DATE</td>
                                    <td class="f-12" style="text-align: right;">
                                        <span><?php echo date("d M Y",strtotime($transaction['transaction_date'])); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="f-12"><?php echo strtoupper($_GET['title']); ?> NO</td>
                                    <td class="f-12" style="text-align: right;">
                                        <span><?php echo $transaction['transaction_number']; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="f-12">NUMBER OF ITEMS</td>
                                    <td class="f-12" style="text-align: right;">
                                        <span><?php echo count((array)$transaction_items); ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="p-1 border-0" style="vertical-align:bottom;">
                        <img src="uploads/all/<?php echo $config['display_logo']; ?>" style="width:100px; height: 34px;" alt="Logo">
                    </td>
                    <td class="p-1 pt-5 border-0 f-12" style="vertical-align:bottom; padding-right: 40px !important; text-align: center;">
                        <span><?php echo $transaction['party_address']; ?></span>
                    </td>
                    <td colspan="4" class="p-2 border-0 f-12" style="vertical-align:bottom;">
                        <span>PRODUCT PURCHASED</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                </tr>
                <tr style="background-color: #eeeeee;">
                    <th scope="col" class="p-1 f-12">ITEM</th>
                    <th scope="col" class="p-1 f-12"></th>
                    <th scope="col" class="p-1 f-12" style="width: 20%;">PRODUCTION DESCRIPTION</th>
                    <th scope="col" class="p-1 text-end f-12" style="width: 5%;">QTY</th>
                    <th scope="col" class="text-end p-1 f-12" style="width: 12%;">UNIT PRICE <span class="d-block">(INR)</span></th>
                    <th scope="col" class="text-end p-1 f-12" style="width: 13%;">AMOUNT <span class="d-block">(INR)</span></th>
                </tr>
                <?php foreach($transaction_items as $ta){ ?>
                <tr>
                    <td class="p-1" style="vertical-align:top;">
                        <img style="width: 100px; height:85px;" class="img-fluid" src="uploads/all/<?php echo $items[$ta['item_id']]['item_image']; ?>" alt="item">
                    </td>
                    <td align="" class="p-1" style="vertical-align:top;">

                    </td>
                    <td class="p-1" style="vertical-align:top;">
                        <span class="d-block f-12"><?php echo $ta['item_name']; ?></span>
                        <span class="d-block f-12"><?php echo emptyCheck($ta['description']); ?></span>

                    </td>
                    <td class="p-1 f-12" style="vertical-align:top; text-align: right;"><?php echo $ta['item_quantity']; ?></td>
                    <td class="p-1" style="vertical-align:top; text-align: right;"><b class="f-w-600 f-12"><?php echo currencyFormat($ta['item_amount'],$config); ?>/-</b></td>
                    <td style="vertical-align:top; text-align: right;"><b class="f-w-600 f-12"><?php echo currencyFormat($ta['total_amount'],$config); ?>/-</b></td>
                </tr>
               <?php } ?>
                <tr>
                    <td colspan="2" style="text-align: center;" class="p-1 border-0">
                        -
                    </td>
                    <td colspan="4" class="border-0 p-1">
                            <table class="table">
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1" style="width: 50%;">TOTAL</th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1" style="width: 50%;">
                                        INR <?php echo currencyFormat($transaction['amount'],$config); ?>/-
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1">DISCOUNT</th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1">
                                        <?php echo currencyFormat($transaction['discount_amount'],$config); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1">Shipping Charges</th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1">
                                        <?php echo currencyFormat($transaction['shipping_amount'],$config); ?>
                                    </th>
                                </tr>


                            <?php  if(count((array)$distax)){
                                    foreach((array)$distax as $ktax){  $tattax +=$ktax['tax_amount']; ?>
                                        if($transaction['originator_state_id'] == $transaction['supplier_state_id']){ ?>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?php echo $split_1_name; ?>  @ <?php echo $ktax['percentage']*$split_1; ?>% on <?php echo currencyFormat($ktax['amount'],$config); ?>
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                            <span id="dis_taxRateid"><?php echo currencyFormat($ktax['tax_amount']*$split_1,$config); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?php echo $split_2_name; ?> @ <?php echo currencyFormat($ktax['percentage']*$split_2,$config); ?>% on <?php echo currencyFormat($ktax['amount'],$config); ?>
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                            <span id="dis_taxRateid_2"><?php echo currencyFormat($ktax['tax_amount']*$split_2,$config); ?></span>
                                        </td>
                                    </tr>
                                <?php } else{  ?>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?php echo $split_1_name; ?> @ <?php echo currencyFormat($ktax['percentage'],$config); ?>% on <?php echo currencyFormat($ktax['amount'],$config); ?>
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                            <span id="dis_taxRateid_3"><?php echo currencyFormat($ktax['tax_amount'],$config); ?></span>
                                        </td>
                                    </tr>
                                <?php }?>
                            <?php } ?>
                            <?php } ?>
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1">GRAND TOTAL</th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1">
                                         <?php echo currencyFormat($transaction['total_amount'],$config); ?>/-
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" class="f-12 p-0 pt-1 pb-1">Rounding Off</th>
                                    <th scope="col" class="text-right f-12 p-0 pt-1 pb-1">
                                        <?php echo currencyFormat($transaction['rounding_off'],$config); ?>/-
                                    </th>
                                </tr>
                                <tr>
                                    <td class="f-12 p-0 pt-1 pb-1" colspan="2"><?php echo numberToWordFormate($transaction['total_amount']);?></td>
                                </tr>
                            </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class=" border-0 pt-4 f-10" style="padding-right: 40px !important; text-align: left;">
                    Date And Time : <?php echo date('Y-m-d H:i:s');  ?>
                    </td>
                    <td colspan="3" class="border-0 pt-4" style="text-align: right;">
                        <span class="f-12"><?php echo emptyCheck($transaction['printing_notes']); ?></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

    <?php if($callapi !='Y'){ ?>
    </div>
    <div class="invoice-btn-section clearfix d-print-none" style="margin-top:5px;">
        <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printDiv');">
            <i class="fa fa-print"></i>  Print
        </button>
        <button class="btn waves-effect waves-light btn-grd-info"  onclick="convertHTMLToPDF();">
            <i class="fa fa-download"></i>  Download
        </button>
    </div>
</body>
</html>
<?php } ?>
<!-- [ style Customizer ] start -->
