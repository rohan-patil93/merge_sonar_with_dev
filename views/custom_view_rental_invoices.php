<?php
if($_GET['print'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $callapi = "Y";
}else{
    include_once 'dbconfig.php';
}
$transaction_table = $_GET['table']!=""?$_GET['table']:$_GET['transaction_table'];
if($_POST['callapi'] =='Y'){
    $config = $_POST['config'];
    $transaction = $_POST['rental_invoices'];
    $transaction_items = $_POST['rental_invoice_items'];
    $bill_by = $_POST['bill_by'];
    $bank_accounts = $_POST['bank_accounts'];
    $callapi = "Y";
    $transaction_table = $_POST['transaction_table'];
}else{
    $rid = $_GET['rid'];
    $table = $_GET['table'];
    $api = API."?mode=getrentalinvoiceprint&rid=".$rid."&transaction_table=".$transaction_table;
    $apicall = callAPI($api);
    $result = json_decode($apicall, 1);
    $config = $result['config'];
    $transaction = $result['rental_invoices'];
    $transaction_items = $result['rental_invoice_items'];
    $bill_by = $result['bill_by'];
    $bank_accounts = $result['bank_accounts'];
    $banks = $result['banks'];
    $account_types = $result['account_types'];
}

$party_additional_details = json_decode($transaction['party_additional_details'],1);
$owner_additional_details = json_decode($transaction['owner_additional_details'],1);
$rental_utilities = $result['rental_utilities'];

$rental_invoice_item_taxes = array();
$totaltax = 0;
foreach((array)$result['rental_invoice_item_taxes'] as $riit){
    $rental_invoice_item_taxes[$riit['transaction_item_id']] = $riit;
    $totaltax +=  $riit['tax_value'];
}

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
<?php if($callapi !='Y'){ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rental Invoices</title>
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
           document.body.innerHTML = "<html><head><title>Rental Invoices</title></head><body>" + printContents + "</body>";
           window.print();
           document.body.innerHTML = originalContents;
           }
    function convertHTMLToPDF() {
        var  title ='<?php echo urldecode($transaction_table); ?>';
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
        function editRentalInvoice(){
            var transaction_table = '<?php echo $transaction_table; ?>';
            var rid = '<?php echo $transaction['id']; ?>';
        }
    </script>
</head>
<body style="margin: 0;padding: 0;font-family: Arial;font-size: 12px;">
    <div class="" style="width: 793px;margin: 0 auto;"> <?php } ?>
        <div id="printDiv">
            <h1 style="font-size:30px;color: #000000;margin: 30px 0 50px 0;text-align: left;text-transform: uppercase;">
                <?php echo $transaction_table == "utility_rental_invoices"?"Reimbursement Bill":"Tax Invoice"; ?>
            <div class="" style="float: right;margin-right: 15px;">
                <img src="uploads/all/<?php echo $config['display_logo']; ?>" style="width:100px;" alt="Logo">
            </div>
            </h1>
            <div class="" style="width:49%;display: inline-block;vertical-align: middle;">
                <table style="width: 100%;">
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Invoice Number:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $transaction['transaction_number']; ?></td>
                    </tr>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Invoice Date:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo date("d M Y",strtotime($transaction['transaction_date'])); ?></td>
                    </tr>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Due Date:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo date("d M Y",strtotime($transaction['due_date'])); ?></td>
                    </tr>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Contact Person:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $owner_additional_details['owner_name']!=""?$owner_additional_details['owner_name']:"-"; ?></td>
                    </tr>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Email ID:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $owner_additional_details['email_id']!=""?$owner_additional_details['email_id']:"-"; ?></td>
                    </tr>
                    <tr>
                        <th scope="col" style="padding-top: 5px;text-align: left;font-size: 12px;">Phone Number:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $owner_additional_details['contact_no']!=""?$owner_additional_details['contact_no']:$bill_by['user_mobile']; ?></td>
                    </tr>
                </table>
            </div>
            <div class="" style="width:50%;display: inline-block;vertical-align: middle;">
                <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;margin-left: 5px;">
                    <span style="color: #333333;font-size: 12px;font-weight: 500;">Billed by:</span>
                    <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $owner_additional_details['owner_company_name']!=""?$owner_additional_details['owner_company_name']:$config['name']; ?></p>
                    <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo emptyCheck($owner_additional_details['address']!=""?$owner_additional_details['address']:$config['contact_address']); ?></p>
                    <table style="width: 100%;">
                        <tr>
                            <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">GSTIN:</th>
                            <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo emptyCheck($owner_additional_details['gst_no']!=""?$owner_additional_details['gst_no']:$config['gst_number']); ?></td>
                        </tr>
                        <tr>
                            <th scope="col" style="font-size: 12px;text-align: left;">PAN:</th>
                            <td style="text-align: right;font-size: 12px;"><?php echo emptyCheck($owner_additional_details['pan_no']!=""?$owner_additional_details['pan_no']:$config['pan_number']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <table role="presentation" style="width:100%;margin-top: 5px;">
                <tr>
                    <td class="" style="width:50%;">
                        <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
                            <span style="color: #333333;font-size: 12px;font-weight: 500;">Billed to</span>
                            <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $transaction['party_name']; ?></p>
                            <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo $transaction['party_address']; ?></p>
                            <table style="width: 100%;">
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">GSTIN :</th>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['gstin']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="font-size: 12px;text-align: left;">PAN:</th>
                                    <td style="text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['pan_no']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td class="" style="width:50%;">
                        <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
                            <span style="color: #333333;font-size: 12px;font-weight: 500;">Shipped to</span>
                            <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $transaction['party_name']; ?></p>
                            <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo emptyCheck($transaction['party_ship_adderss']); ?></p>
                            <table style="width: 100%;">
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">GSTIN:</th>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['gstin']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="font-size: 12px;text-align: left;">PAN:</th>
                                    <td style="text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['pan_no']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                <tr>
            </table>


            <table style="width:99%;margin:10px 0 0 0;">
                <?php if($transaction['ref_bill_type'] == "U"){
                        $childary = array();
                        foreach($transaction_items as $ta){
                            if($ta['parent_id'] == '0'){
                                $childary[$ta['item_id']] = $ta;
                            }else{
                                $childary[$ta['parent_id']]['CHILD'][] = $ta;
                            }
                        }
                        ?>
                    <tr style="background: #eeeeee;">
                        <th scope="col" style="padding:5px;text-align: left;font-size: 12px;">Particular</th>
                        <th scope="col" style="padding:5px;text-align: left;font-size: 12px;">Note</th>
                        <th scope="col" style="padding:5px;text-align: right;font-size: 12px;">Total</th>
                    </tr>
                    <?php foreach($childary as $parentkey=>$valu){
                            if(isset($valu['CHILD'])){
                                $totalamt = 0;
                                foreach($valu['CHILD'] as $vl){
                                    if($vl['total_amount'] !=""){
                                        $totalamt += $vl['total_amount'];
                                    }
                                }
                            ?>
                            <tr style="border: 1px solid #cccccc;">
                                <td style="padding:5px;font-size: 12px; word-break: break-word; white-space: initial;"><?php echo $rental_utilities[$parentkey]['type_name']; ?></td>
                                <td style="padding:5px;text-align: left;font-size: 12px; word-break: break-word; white-space: initial;"><?php echo emptyCheck($valu['CHILD'][0]['description']); ?></td>
                                 <td style="padding:5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($totalamt,$config); ?></td>
                            </tr>
                            <tr style="border: 1px solid #cccccc;">
                                <td colspan="3">
                                    <table role="presentation" style="width:65%;margin:10px  10px 10px;">
                                        <?php foreach($valu['CHILD'] as $ta){ ?>
                                        <tr style="border: 1px solid #cccccc;">
                                            <td style="padding:5px;font-size: 12px; word-break: break-word; white-space: initial;     font-weight: 100;"><?php print_r($perentchieldary); echo $ta['item_name']; ?></td>
                                            <td style="padding:5px;font-size: 12px;"><?php echo emptyCheck(round($ta['item_quantity'])); ?></td>
                                            <td style="padding:5px;font-size: 12px;"><?php echo emptyCheck($ta['purchase_uom'] !="UN"?$ta['purchase_uom']:"-"); ?></td>
                                            <td style="padding:5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($ta['item_price'],$config); ?></td>
                                            <td style="padding:5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($ta['total_amount'],$config); ?></td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                </td>
                            </tr>
                       <?php }else{ ?>
                           <tr style="border: 1px solid #cccccc;">
                                <td style="padding:5px;font-size: 12px; word-break: break-word; white-space: initial;"><?php echo $valu['item_name']; ?></td>
                                <td style="padding:5px;text-align: left;font-size: 12px; word-break: break-word; white-space: initial;"><?php echo emptyCheck($ta['description']); ?></td>
                                 <td style="padding:5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($valu['total_amount'],$config); ?></td>
                            </tr>
                       <?php } ?>

                <?php } ?>
                <?php } else { ?>
                <tr style="background: #eeeeee;">
                    <th scope="col" style="padding:5px;text-align: left;font-size: 12px;">Particular</th>
                    <th scope="col" style="padding:5px;text-align: left;font-size: 12px;">Note</th>
                     <th scope="col" style="padding:5px;font-size: 12px;">HSN/SAC</th>
                    <th scope="col" style="padding:5px;font-size: 12px;">Qty</th>
                    <th scope="col" style="padding:5px;font-size: 12px;">UOM</th>
                    <th scope="col" style="padding:5px;text-align: right;font-size: 12px;">Rate</th>
                    <th scope="col" style="padding:5px;text-align: right;font-size: 12px;">Taxable Amt</th>
                    <th scope="col" style="padding:5px;text-align: right;font-size: 12px;">Tax</th>
                    <th scope="col" style="padding:5px;text-align: right;font-size: 12px;">Total</th>
                </tr>
                <?php foreach($transaction_items as $ta){ ?>
                <tr style="border: 1px solid #cccccc;">
                    <td style="padding:5px;font-size: 12px; word-break: break-word; white-space: initial;"><?php echo $ta['item_name']; ?></td>
                    <td style="padding:5px;text-align: left;font-size: 12px; word-break: break-word; white-space: initial;"><?php echo emptyCheck($ta['description']); ?></td>
                    <td style="padding:5px;font-size: 12px;"><?php echo $rental_invoice_item_taxes[$ta['item_id']]['code']; ?></td>
                    <td style="padding:5px;font-size: 12px;"><?php echo emptyCheck(round($ta['item_quantity'])); ?></td>
                    <td style="padding:5px;font-size: 12px;"><?php echo emptyCheck($ta['purchase_uom']); ?></td>
                    <td style="padding:5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($ta['item_price'],$config); ?></td>
                    <td style="padding:5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($ta['item_amount'],$config); ?></td>
                    <td style="padding:5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($ta['tax_amount'],$config); ?></td>
                    <td style="padding:5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($ta['total_amount'],$config); ?></td>
                </tr>
                <?php } ?>
                <?php } ?>
            </table>


            <table role="presentation" style="width:99%;margin-top:5px;">
                <tr>
                    <td style="width:60%;vertical-align: top;">
                        <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
                            <h3 style="font-size: 12px;color:#333333;margin: 0;">Bank Detail</h3>
                            <table style="width: 100%; margin:10px 0 0 0;">
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Bank Name:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($owner_additional_details['bank_id']!=""?$banks[$owner_additional_details['bank_id']]:$banks[$bank_accounts['bank_name']]); ?></td>

                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Account Holder:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($owner_additional_details['owner_company_name']!=""?$owner_additional_details['owner_company_name']:$bank_accounts['bank_contact_person']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Account Number:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($owner_additional_details['bank_account_no']!=""?$owner_additional_details['bank_account_no']:$bank_accounts['bank_account_number']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">IFSC Number:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($owner_additional_details['ifsc_code']!=""?$owner_additional_details['ifsc_code']:$bank_accounts['ifsc_code']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Account Type:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;">
                                        <?php  echo $owner_additional_details['account_type']!=""?$account_types[$owner_additional_details['account_type']]:$account_types[$bank_accounts['account_type']]; ?>

                                    </td>

                            </table>
                        </div>
                    </td>
                    <td style="width:40%;vertical-align: top;">
                        <table style="width: 100%; margin:10px 0 0 0;">
                            <?php if($transaction['ref_bill_type'] != "U"){ ?>
                            <tr>
                                <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">Sub Total</th>
                                <td style="padding-top: 5px;padding-bottom: 5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($transaction['amount'],$config); ?></td>
                            </tr>


                                <?php if($transaction['originator_state_id'] == $transaction['supplier_state_id']){ ?>
                                 <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">IGST (<?php echo $totaltax."%"; ?>)</th>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($transaction['tax_amount'],$config); ?></td>
                                </tr>
                                   <?php }else{ ?>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">CGST (<?php echo ($totaltax/2)."%"; ?>)</th>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($transaction['tax_amount']/2,$config); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">SGST (<?php echo ($totaltax/2)."%"; ?>)</th>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($transaction['tax_amount']/2,$config); ?></td>
                                </tr>
                                   <?php }  ?>
                            <tr style="border-top:1px solid #444444;border-bottom: 1px solid #444444;">
                                <th scope="col" style="padding-top: 5px;text-align: left;border-top:1px solid #cccccc;font-weight: 500;"><span style="font-size: 12px;">Total</span> </th>
                                <th scope="col" style="padding-top: 5px;text-align: right;border-top:1px solid #cccccc;font-weight: 500"><span style="font-size:12px;"><?php echo currencyFormat($transaction['total_amount'],$config); ?></span> </th>
                            </tr>
                            <tr>
                                <th scope="col" style="padding-top: 5px;font-size: 12px;text-align: left;">Round Off</th>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo round(round($transaction['total_amount'])-$transaction['total_amount'],2); ?></td>
                            </tr>
                               <?php } ?>
                            <tr style="border-top:1px solid #444444;border-bottom: 1px solid #444444;">
                                <th scope="col" style="padding-top: 5px;text-align: left;border-top:1px solid #cccccc;font-weight: 500;"><span style="font-size: 21px;">Grand Total</span> </th>
                                <th scope="col" style="padding-top: 5px;text-align: right;border-top:1px solid #cccccc;font-weight: 500"><span style="font-size:21px;"><?php echo currencyFormat(round($transaction['total_amount']),$config); ?></span> </th>
                            </tr>

                        </table>
                        <div style="margin-top: 10px;vertical-align: top;">
                            <span style="font-size: 12px; color: #333333;font-weight: 600;">Invoice total in words</span>
                            <p style="font-size: 12px; color: #333333; margin-top: 10px;white-space:initial;"><?php echo numberToWordFormate(round($transaction['total_amount']));?> </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width:60%;vertical-align: top;">

                        <div class="" style="display: block; box-sizing:border-box;background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right: 10px; margin-top:10px;">
                                <h3 style="font-size: 12px;color:#333333;margin: 0;font-weight:bold;">Additional Notes</h3>
                                <p style="font-size:12px;color:#333333;"><?php echo emptyCheck($transaction['printing_notes']); ?></p>
                        </div>
                    </td>
                    <td style="width:40%;">
                        <div style="vertical-align: top;">
                            <span style="font-size: 12px;color:#333333;display: block;text-align: center;font-weight: 500;">For <?php echo $config['name']; ?></span>
                            <p style="background: #eeeeee;height:45px;margin-bottom: 0px;margin-top: 0;"></p>
                            <span style="font-size: 12px;color:#333333;display: block;text-align: center;font-weight: 500;">Authorised Signature</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <?php if($callapi !='Y'){ ?>
    </div>
    <div class="invoice-btn-section clearfix d-print-none">
        <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printDiv');">
            <i class="fa fa-print"></i> &nbsp Print Invoice
        </button>
        <button class="btn waves-effect waves-light btn-grd-info"  onclick="convertHTMLToPDF();">
            <i class="fa fa-download"></i> &nbsp Download Invoice
        </button>

    </div>
</body>
</html>
<?php } ?>
