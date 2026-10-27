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
    $transaction_table = $_POST['transaction_table'];
    $unit_of_measurements = $_POST['unit_of_measurements'];
    $transaction_table_config = $_POST['transaction_table_config'];
   $customer_lut_bond_details = $_POST['customer_lut_bond_details'];
   $refrance_transaction_master = $_POST['refrance_transaction_master'];
   $party_table = $_POST['party_table'];
   $payment_terms = $_POST['payment_terms'];
    $party_sku_codes = $_POST['party_sku_codes'];
    $callapi = "Y";
     $items = $_POST['items'];
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
    $transaction_table = $result['transaction_table'];
    $unit_of_measurements = $result['unit_of_measurements'];
    $transaction_table_config = $result['transaction_table_config'];
   $customer_lut_bond_details = $result['customer_lut_bond_details'];
   $refrance_transaction_master = $result['refrance_transaction_master'];
   $party_table = $result['party_table'];
    $payment_terms = $result['payment_terms'];
    $party_sku_codes = $result['party_sku_codes'];
    $items = $result['items'];
}

$printuom = array();
foreach($unit_of_measurements as $uoom){
    $printuom[$uoom['id']] = $uoom['uom_name'];
}
$paymentshow = 'N';
$amountpartshowornot = 'Y';
$refinvoicenumberlabel = "Ref No./Date";
$billedbylable = "Billed by";
$billedtolable = "Billed to";
$shippedtolable = "Shipped to";
$invoicenumberlabel = $title!=''?$title."No":"Doc No";
$invoicedatelable = $title!=''?$title." Date":"Doc Date";
$party_code_field = "Y";
$paymenttermshow = "Y";
if($transaction_table == 'sales_orders'){
    $invoicenumberlabel = "SOB Number";
    $invoicedatelable = "SOB Date";
    $amountpartshowornot = 'N';
    $paymenttermshow = "N";
}elseif($transaction_table == 'purchase_orders'){
    $invoicenumberlabel = "PO Number";
    $invoicedatelable = "PO Date";
    $refinvoicenumberlabel = "SOB No./Date";
}elseif($transaction_table == 'quotations'){
    $invoicenumberlabel = "Enquiry number";
    $invoicedatelable = "Enquiry Date";
    $billedbylable = "Quoted by";
    $billedtolable = "Quoted to";
    $shippedtolable = "Delivery address";
    $amountpartshowornot = 'N';
    $party_code_field = "N";
}elseif($transaction_table == 'sales_invoices' || $transaction_table == 'purchase_invoices'){
    $invoicenumberlabel = "Invoice Number";
    $invoicedatelable = "Invoice Date";

}
if($transaction_table == 'goods_receipt_notes'){
    $refinvoicenumberlabel = "PO No/Date";
}
if($transaction_table == 'sales_invoices' || $transaction_table == 'sales_invoice_receipts'){
    $paymentshow = "Y";
}

$dutax = array();
$distax = array();
$parentrowidwithtax = array();
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
   $parentrowidwithtax[$taxvalue['transaction_item_row_id']] = $taxvalue;
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
if($party_table == "customers" || $party_table =""){
    $bill_by_name = $config['name'];
    $bill_by_billing_address = emptyCheck($config['billing_address']);
    $bill_by_gst_number = emptyCheck($config['gst_number']);
    $bill_by_pan_number = emptyCheck($config['pan_number']);

    $Billed_to_party_name = $transaction['party_name'];
    $Billed_to_party_address = $transaction['party_address'];
    $Billed_to_gstin = emptyCheck($party_additional_details['gstin']);
    $Billed_to_pan_no = emptyCheck($party_additional_details['pan_no']);

    $Shipped_to_party_name = $transaction['party_name'];
    $Shipped_to_party_ship_adderss = emptyCheck($transaction['party_ship_adderss']);
    $Shipped_to_gstin = emptyCheck($party_additional_details['gstin']);
    $Shipped_to_pan_no = emptyCheck($party_additional_details['pan_no']);
}else{

    $Billed_to_party_name = $Shipped_to_party_name= $config['name'];
    $Billed_to_party_address =$Shipped_to_party_ship_adderss= emptyCheck($config['billing_address']);
    $Billed_to_gstin = $Shipped_to_gstin= emptyCheck($config['gst_number']);
    $Billed_to_pan_no = $Shipped_to_pan_no =  emptyCheck($config['pan_number']);

    $bill_by_name = $transaction['party_name'];
    $bill_by_billing_address = $transaction['party_address'];
    $bill_by_gst_number = emptyCheck($party_additional_details['gstin']);
    $bill_by_pan_number = emptyCheck($party_additional_details['pan_no']);

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
                async: false,
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
        <div id="printDiv">
            <h1 style="font-size:30px;color: #000000;margin: 30px 0 50px 0;text-align: left;text-transform: uppercase;">
                <?php

                if($transaction_table == "purchase_orders" && ($config['name'] =="National Rubber Industries")){
                    echo "Work Order";
                }elseif($transaction_table == "quotations"){
                    echo "Enquiry";
                }elseif($transaction_table_config['page_title'] !=""){
                    echo $transaction_table_config['page_title'];

                }else{
                    echo $_GET['title'];
                }
                 ?>
            <div class="" style="float: right;margin-right: 15px;">
                <img src="uploads/all/<?php echo $config['display_logo']; ?>" style="width:80px;" alt="Logo">
            </div>
            </h1>
            <div class="" style="width:49%;display: inline-block;vertical-align: middle;">
                <table style="width: 100%;">
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $invoicenumberlabel; ?>:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $transaction['transaction_number']; ?></td>
                    </tr>


                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $invoicedatelable; ?>:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo date("d M Y",strtotime($transaction['transaction_date'])); ?></td>
                    </tr>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Due Date:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo date("d M Y",strtotime($transaction['due_date'])); ?></td>
                    </tr>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Contact Person:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $bill_by['display_name']; ?></td>
                    </tr>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Email ID:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo emptyCheck($bill_by['user_email']); ?></td>
                    </tr>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Phone Number:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo emptyCheck($bill_by['user_mobile']); ?></td>
                    </tr>
                    <?php if($transaction['ref_no'] !="" || $transaction['ref_date'] !=""){
                        $transaction['ref_date'] = ($transaction['ref_date']!='0000-00-00' && $transaction['ref_date']!='1970-01-01')?date("d M Y",strtotime($transaction['ref_date'])):"";
                        ?>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $refinvoicenumberlabel; ?>:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo emptyCheck($transaction['ref_no'])." / ".emptyCheck($transaction['ref_date']); ?></td>
                    </tr>
                <?php } if($transaction['doc_ref_no'] !="" || $transaction['doc_ref_date'] !=""){
                    $transaction['doc_ref_date'] = ($transaction['doc_ref_date']!='0000-00-00' && $transaction['doc_ref_date']!='1970-01-01')?date("d M Y",strtotime($transaction['doc_ref_date'])):"";
                    ?>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Doc Ref No./Date:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo emptyCheck($transaction['doc_ref_no'])." / ".emptyCheck($transaction['doc_ref_date']); ?></td>
                    </tr>
                <?php }
                if($refrance_transaction_master['transaction_number'] !="" && $transaction_table == "goods_receipt_notes"){ ?>

                   <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">SOB Number:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo emptyCheck($refrance_transaction_master['transaction_number']); ?></td>
                    </tr>
                <?php }

                if($customer_lut_bond_details['bond_no'] !="" && $paymentshow=="Y"){
                    ?>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">LUT/BOND:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo emptyCheck($customer_lut_bond_details['lut_no'])." / ".emptyCheck($customer_lut_bond_details['bond_no']); ?></td>
                    </tr>
                <?php } ?>



                </table>
            </div>
            <div class="" style="width:50%;display: inline-block;vertical-align: middle;">
                <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;margin-left: 5px;">
                    <span style="color: #333333;font-size: 12px;font-weight: 500;"><?php echo $billedbylable; ?>:</span>
                    <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $bill_by_name; ?></p>
                    <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo $bill_by_billing_address; ?></p>
                    <table style="width: 100%;">
                        <tr>
                            <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">GSTIN:</th>
                            <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo $bill_by_gst_number; ?></td>
                        </tr>
                        <tr>
                            <th scope="col" style="font-size: 12px;text-align: left;">PAN:</th>
                            <td style="text-align: right;font-size: 12px;"><?php echo $bill_by_pan_number; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <table role="presentation" style="width:100%;margin-top: 5px;">
                <tr>
                    <td class="" style="width:50%;">
                        <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
                            <span style="color: #333333;font-size: 12px;font-weight: 500;"><?php echo $billedtolable; ?></span>
                            <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $Billed_to_party_name; ?></p>
                            <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo $Billed_to_party_address; ?></p>
                            <table style="width: 100%;">
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">GSTIN:</th>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo $Billed_to_gstin; ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="font-size: 12px;text-align: left;">PAN:</th>
                                    <td style="text-align: right;font-size: 12px;"><?php echo $Billed_to_pan_no; ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>

                    <td class="" style="width:50%;">
                        <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
                            <span style="color: #333333;font-size: 12px;font-weight: 500;"><?php echo $shippedtolable; ?></span>
                            <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $Shipped_to_party_name; ?></p>
                            <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo $Shipped_to_party_ship_adderss; ?></p>
                            <table style="width: 100%;">
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">GSTIN:</th>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo $Shipped_to_gstin; ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="font-size: 12px;text-align: left;">PAN:</th>
                                    <td style="text-align: right;font-size: 12px;"><?php echo $Shipped_to_pan_no; ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                <tr>
            </table>
            <table style="width:99%;margin:10px 0 0 0;">
                <tr style="background: #eeeeee;">
                    <th scope="col" style="padding:5px;text-align: left;">#</th>
                    <th scope="col" style="padding:5px;text-align: left;">Item Name</th>
                    <th scope="col" style="padding:5px;text-align: left;">Description</th>
                    <?php if($party_code_field =="Y"){ ?>
                    <th scope="col" style="padding:5px;text-align: left;">Party Code</th>
                    <?php } ?>
                    <th scope="col" style="padding:10px;">HSN/SAC</th>
                    <th scope="col" style="padding:10px;">UOM</th>

                    <th scope="col" style="padding:10px;text-align: right;">QTY.</th>
                    <?php if($amountpartshowornot == 'Y'){ ?>
                    <th scope="col" style="padding:5px;text-align: right;">Rate</th>
                    <th scope="col" style="padding:5px;text-align: right;">Tax</th>
                    <th scope="col" style="padding:10px;text-align: right;">Disc</th>
                    <th scope="col" style="padding:5px;text-align: right;">Total</th>
                    <?php } ?>
                </tr>
                <?php $seq=1; foreach($transaction_items as $ta){ ?>
                <tr style="border: 1px solid #cccccc;">
                    <td style="padding:5px;font-size: 12px; white-space: normal;"><?php echo $seq; ?></td>

                    <td style="padding:5px;font-size: 12px; white-space: normal;"><?php echo $items[$ta['item_id']]['sku']; ?></td>


                    <td style="padding:5px;text-align: left;font-size: 12px; white-space: normal;">
                        <?= nl2br(wordwrap(htmlspecialchars($ta['description']), 50, "\n", true)); ?>
                    </td>
                    <?php if($party_code_field =="Y"){ ?>
                    <td style="padding:5px;text-align: left;font-size: 12px; white-space: normal;"><?php echo emptyCheck($party_sku_codes[$ta['item_id']]['party_sku_number']); ?></td>

                <?php } ?>
                    <td style="padding:5px;text-align: left;font-size: 12px; white-space: normal;"><?php echo emptyCheck($parentrowidwithtax[$ta['id']]['tax_name']); ?></td>
                    <td style="padding:5px;text-align: left;font-size: 12px; white-space: normal;"><?php echo emptyCheck($printuom[$ta['purchase_uom']]); ?></td>



                    <td style="padding:5px;text-align: right;font-size: 12px; white-space: normal;"><?php echo emptyCheck($ta['item_quantity']); ?></td>
                    <?php if($amountpartshowornot == 'Y'){ ?>
                    <td style="padding:5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($ta['item_price'],$config); ?></td>
                    <td style="padding:5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($ta['tax_amount'],$config); ?></td>
                    <td style="padding:5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($ta['discount_amount'],$config); ?></td>
                    <td style="padding:5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($ta['total_amount'],$config); ?></td>
                    <?php } ?>
                </tr>

                <?php  $seq++;
} ?>
            </table>
            <table role="presentation" style="width:99%;margin-top:5px;">
                <tr>
                    <td style="width:60%;vertical-align: top;">

                        <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
                            <h3 style="font-size: 12px;color:#333333;margin: 0; white-space: normal;">Bank & UPI Detail</h3>
                            <?php if($paymentshow == 'Y'){ ?>
                            <table style="width: 100%; margin:10px 0 0 0;">
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Bank Name:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['bank_name']); ?></td>
                                    <td rowspan="6" style="width: 20%; text-align: center;">
                                        <?php if($bank_accounts['upi_qr_code_image'] != ''){ ?>

                                        <h3 style="font-size: 12px;color:#333333;margin: 0;font-weight: 500;">Scan to pay via UPI</h3>
                                         <img src="uploads/all/<?php echo $bank_accounts['upi_qr_code_image']; ?>" style="width:80px;margin: 10px 0 0 0;" alt="QR Code">
                                        <?php if($bank_accounts['upi_id'] !=""){ ?>
                                        <p style="font-size: 12px;color:#333333;font-weight: bold;"><?php echo emptyCheck($bank_accounts['upi_id']); ?></p>
                                    <?php } ?>

                                    <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Account Holder:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['bank_contact_person']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Account Number:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['bank_account_number']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">IFSC Number:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['ifsc_code']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Account Type:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php if($bank_accounts['account_type'] =="C"){
                                        echo "Current Account";
                                    }if($bank_accounts['account_type'] =="S"){
                                        echo "Saving Account";
                                    } ?>

                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">UPI:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['upi_id']); ?></td>
                                </tr>
                            </table>
                            <?php } ?>
                        </div>

                    </td>
                    <td style="width:40%;vertical-align: top;">
                        <?php if($amountpartshowornot == 'Y'){ ?>
                        <table style="width: 100%; margin:10px 0 0 0;">
                            <tr>
                                <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">Sub Total</th>
                                <td style="padding-top: 5px;padding-bottom: 5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($transaction['amount'],$config); ?></td>
                            </tr>
                            <tr>
                                <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">Discount</th>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($transaction['discount_amount'],$config); ?></td>
                            </tr>


                            <?php  $additional_charges = json_decode($transaction['additional_charges'], 1);
                             if(count((array)$additional_charges) !="0"){
                                foreach($additional_charges as $adcharge){ ?>
                            <tr>
                                <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;"><?php echo $adcharge['charge_name']; ?></th>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat(($adcharge['shipping_amount']+$adcharge['shipping_tax_amount']),$config); ?></td>
                            </tr>
                            <?php } ?>
                            <?php } ?>


                            <?php  if(count((array)$distax)){
                                    foreach((array)$distax as $ktax){  $tattax +=$ktax['tax_amount']; ?>
                                        if($transaction['originator_state_id'] == $transaction['supplier_state_id']){ ?>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?php echo $split_1_name; ?>  @ <?php echo $ktax['percentage']*$split_1; ?>% on <?php echo currencyFormat($ktax['amount'],$config); ?>
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                            <span id="dis_taxRateid"><?php echo currencyFormat($ktax['tax_amount']*$split_1,$config); ?></span>
                                            <input aria-label="Input field"  value="<?php echo currencyFormat($ktax['tax_amount']*$split_1,$config); ?>" type="hidden" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?php echo $split_2_name; ?> @ <?php echo currencyFormat($ktax['percentage']*$split_2,$config); ?>% on <?php echo currencyFormat($ktax['amount'],$config); ?>
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                            <span id="dis_taxRateid_2"><?php echo currencyFormat($ktax['tax_amount']*$split_2,$config); ?></span>
                                            <input aria-label="Input field"  value="<?php echo currencyFormat($ktax['tax_amount']*$split_2,$config); ?>" type="hidden" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">
                                        </td>
                                    </tr>
                                <?php } else{  ?>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?php echo $split_1_name; ?> @ <?php echo currencyFormat($ktax['percentage'],$config); ?>% on <?php echo currencyFormat($ktax['amount'],$config); ?>
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                            <span id="dis_taxRateid_3"><?php echo currencyFormat($ktax['tax_amount'],$config); ?></span>
                                            <input aria-label="Input field"  value="<?php echo currencyFormat($ktax['tax_amount'],$config); ?>" type="hidden" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">
                                        </td>
                                    </tr>
                                <?php }?>
                            <?php } ?>
                            <?php } ?>

                            <?php  ?>
                            <?php if($transaction['security_deposit'] !="" && $transaction['security_deposit'] !="0"){ ?>
                            <tr>
                                <td style="padding-top: 5px;font-size: 12px;text-align: left;"> Security Deposit
                                </td>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                    <span><?php echo currencyFormat($transaction['security_deposit'],$config); ?></span>
                                </td>
                            </tr>
                        <?php } if($transaction['rounding_off'] !="" && $transaction['rounding_off'] !="0"){ ?>
                            <tr>
                                <td style="padding-top: 5px;font-size: 12px;text-align: left;">     Rounding Off
                                </td>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                    <span><?php echo currencyFormat($transaction['rounding_off'],$config); ?></span>
                                </td>
                            </tr>
                             <?php } ?>
                            <tr style="border-top:1px solid #444444;border-bottom: 1px solid #444444;">
                                <th scope="col" style="padding-top: 5px;text-align: left;border-top:1px solid #cccccc;font-weight: 500;"><span style="font-size: 21px;">Total</span> </th>
                                <th scope="col" style="padding-top: 5px;text-align: right;border-top:1px solid #cccccc;font-weight: 500"><span style="font-size:21px;"><?php echo currencyFormat($transaction['total_amount'],$config); ?></span> </th>
                            </tr>

                            <?php if($transaction_table == "purchase_orders"){ ?>
                                 <tr>
                                    <td style="padding-top: 5px;font-size: 12px;text-align: left;"> Delivery Schedule
                                    </td>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                        <span><?php echo emptyCheck($transaction['printing_notes']); ?></span>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php }else{ echo '<table role="presentation" style="width: 100%; margin:10px 0 0 0;">';
} ?>
                        <?php if($transaction['payment_terms'] !="0" && $transaction['payment_terms'] !="" && $paymenttermshow == "Y"){

                            ?>
                            <tr>
                                <td style="padding-top: 5px;font-size: 12px;text-align: left;"> Payment Terms
                                </td>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;">
                                    <span><?php echo $payment_terms[$transaction['payment_terms']]['payment_term_name']; ?></span>
                                </td>
                            </tr>

                            <tr class="p-0" >
                                <?php $payment_term_history = json_decode($transaction['payment_term_history'],1);
                                if(count((array)$payment_term_history) !='0'){
                                ?>
                                <td colspan="2" class="p-2">
                                    <table class=" table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="p-2">Due Date</th>
                                                <th scope="col" class="p-2 text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                <?php $mjk=1; foreach((array)$payment_term_history as $pth){?>
                                    <tr>
                                        <td class="p-2"><?php echo date("d M Y",strtotime($pth['due_date'])); ?> </td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformate($pth['amount']); ?>
                                        <?php if($pth['extra_date'] !="" && $pth['extra_amt'] !=""){?>
                                        <span class='d-block f-10 f-w-600'>Before Date <?php echo date("d M Y",strtotime($pth['extra_date']))." (".customeummarmonyformate($pth['extra_amt']).")"; ?></span> <?php
} ?></td>
                                    </tr>
                                    <?php $mjk++;
} ?>
                                    </tbody>
                                    </table>
                                </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>





                        </table>
                        <?php if($amountpartshowornot == "Y"){ ?>
                        <div style="margin-top: 10px;vertical-align: top;">
                            <span style="font-size: 12px; color: #333333;font-weight: 600;">Invoice total in words</span>
                            <p style="font-size: 12px; color: #333333; margin-top: 10px;white-space:initial;"><?php echo numberToWordFormate($transaction['total_amount']);?> </p>
                        </div>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td style="width:60%;vertical-align: top;">

                         <?php if($transaction_table != "purchase_orders"){

                            ?>
                        <div class="" style="display: block; box-sizing:border-box;background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right: 10px; margin-top:10px;">
                                <h3 style="font-size: 12px;color:#333333;margin: 0;font-weight:bold; white-space: normal;">Additional Notes</h3>
                                <p style="font-size:12px;color:#333333; white-space: normal;"><?php echo emptyCheck($transaction['printing_notes']); ?></p>
                        </div>
                    <?php } ?>
                    </td>
                    <td style="width:40%;">
                        <div style="vertical-align: top;">
                            <span style="font-size: 12px;color:#333333;display: block;text-align: center;font-weight: 500; white-space: normal;">For <?php echo $config['name']; ?></span>
                            <p style="background: #eeeeee;height:45px;margin-bottom: 0px;margin-top: 0; white-space: normal;">

                            </p>
                            <span style="font-size: 12px;color:#333333;display: block;text-align: center;font-weight: 500; white-space: normal;">Authorised Signature</span>
                            <span style="font-size: 12px;color:#333333;display: block;text-align: center;font-weight: 500; white-space: normal;">This is a computer generated invoice no signature required</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span style="float:inline-end;">E. & O.E.</span>
                    </td>
                </tr>
            </table>
        </div>
        <div>
            <?php
            ?>
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
