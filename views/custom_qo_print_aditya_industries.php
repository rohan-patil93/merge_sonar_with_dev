<?php 
if($_GET['print'] =="Y"){
    include('../includes/session.php');
    include('../dbconfig.php');
    include('../comm.php');
    $callapi = "Y";
}else{
    include_once('dbconfig.php');
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
    $transaction_table = $result['transaction_table'];
    $unit_of_measurements = $result['unit_of_measurements'];
    $transaction_table_config = $result['transaction_table_config'];
   
   
}
$printuom = array();
foreach($unit_of_measurements as $uoom){
    $printuom[$uoom['id']] = $uoom['uom_name'];
}
//print_r($result['unit_of_measurements']);
$paymentshow = 'N';
$amountpartshowornot = 'Y';
$refinvoicenumberlabel = "Ref No./Date";
if($transaction_table == 'sales_orders'){
    $invoicenumberlabel = "SOB Number";
    $invoicedatelable = "SOB Date";
    $amountpartshowornot = 'N';
}elseif($transaction_table == 'purchase_orders'){
    $invoicenumberlabel = "PO Number";
    $invoicedatelable = "PO Date";
    $refinvoicenumberlabel = "SOB No./Date";
}else{
    $invoicenumberlabel = "Invoice Number";
    $invoicedatelable = "Invoice Date";
}
//if($transaction_table == 'purchase_invoices' OR $transaction_table == 'supplier_bills'){
if($transaction_table == 'sales_invoices' OR $transaction_table == 'sales_invoice_receipts'){
    $paymentshow = "Y";
}

//echo "<pre>"; print_r($bank_accounts); echo "</pre>";
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

//print_r($transaction);
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


// echo "<pre>";print_r($distax); echo "</pre>";
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

    $no = floor($number);
    $point = round($number - $no, 2) * 100;
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
    ". " . $words[$point / 10] . " " . 
    $words[$point = $point % 10] : '';
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
// function dateFormat($from='',$config){
//  $configdate_format = str_replace('-',$config['date_separator'], strtolower($config['date_format']));
//  $date_formate = str_replace('yyyy', 'Y', str_replace('mm', 'm', str_replace('dd', 'd', $configdate_format)));
//  $from = $from!='0000-00-00' ? $from :'';
//  $dateformat = $date_formate!="0"?$date_formate: 'Y-m-d';
//     if($from != ""){
//      $from =  date($dateformat,strtotime($from));
//  }else{
//      $from = date($dateformat);
//  }
//  return $from; 
// }
?>
<?php if($callapi !='Y'){ ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    
    <script type="text/javascript">
        function printDiv(divId) {
           var printContents = document.getElementById(divId).innerHTML;
           var originalContents = document.body.innerHTML;
           document.body.innerHTML = "<html><head><title></title></head><body>" + printContents + "</body>";
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
<body>
    <div class="width-800 m-auto"> <?php } ?>



        <div id="printDiv">
            <link rel="stylesheet" type="text/css" href="../assets/css/custom_qo_print_aditya_industries.css">


            <table class="w-100 border-collapse border">
                <tr>
                    <td class="f-14" colspan="2">
                        <span class="d-block">GSTIN : <?php echo emptyCheck($config['gst_number']); ?></span>
                        <span class="d-block">PAN No : <?php echo emptyCheck($config['pan_number']); ?></span>
                    </td>
                    <td colspan="3" class="f-30 border-eeeeee text-center">
                        <h2 class="f-30"><?php echo $config['name']; ?></h2>
                    </td>
                    <td class="f-14" colspan="2">
                        <span class="d-block"><?php echo urldecode($transaction_table_config['page_title'] !=""?$transaction_table_config['page_title']:$_GET['title']); ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="f-14 text-center" colspan="7">
                        <span class="d-block">Office : <?php echo emptyCheck($config['billing_address']); ?></span>
                        <span class="d-block">Phone No. : <?php echo $bill_by['display_name']; ?></span>
                        <span class="d-block">Email : <?php echo emptyCheck($bill_by['user_email']); ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="f-24 text-center" colspan="7">
                        <span><?php echo urldecode($transaction_table_config['page_title'] !=""?$transaction_table_config['page_title']:$_GET['title']); ?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <span class="f-14 d-block">Bill To:</span>
                        <span class="d-block f-16"><?php echo emptyCheck($config['billing_address']); ?></span>
                        <span class="d-block f-14"><?php echo emptyCheck($config['gst_number']); ?></span>   
                    </td>
                    <td colspan="3" class="p-0">
                        <table class="w-100 border-collapse">
                            <tr>
                                <th class="f-14 text-end">Invoice No.</th>
                                <th class="f-14 text-start"><?php echo $transaction['transaction_number']; ?></th>
                            </tr>
                            <tr>
                                <th class="f-14 text-end">Invoice Date</th>
                                <th class="f-14 text-start"><?php echo date("d M Y",strtotime($transaction['transaction_date'])); ?></th>
                            </tr>
                            <tr>
                                <?php 
                                $transaction['doc_ref_date'] = ($transaction['doc_ref_date']!='0000-00-00' AND $transaction['doc_ref_date']!='1970-01-01')?date("d M Y",strtotime($transaction['doc_ref_date'])):"";
                                ?>
                                <th class="f-14 text-end">Buyer's Order No.</th>
                                <th class="f-14 text-start"><?php echo emptyCheck($transaction['doc_ref_no']); ?></th>
                            </tr>
                            <tr>
                                <th class="f-14 text-end">Order Date</th>
                                <th class="f-14 text-start"><?php echo emptyCheck($transaction['doc_ref_date']); ?></th>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <span class="f-14 d-block">Ship To:</span>
                        <span class="d-block f-16"><?php echo $transaction['party_name']; ?></span>
                        <span class="d-block f-14"><?php echo $transaction['party_address']; ?></span>   
                    </td>
                    <td colspan="3" class="p-0">
                        <table class="w-100 border-collapse">
                            <tr>
                                <th class="f-14 text-end"> Despatched Through</th>
                                <th class="f-14 text-start"> - </th>
                            </tr>
                            <tr>
                                <th class="f-14 text-end">Place of Supply</th>
                                <th class="f-14 text-start"> - </th>
                            </tr>
                            <tr>
                                <th class="f-14 text-end">STATE : -</th>
                                <th class="f-14 text-start">STATE CODE : - </th>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th class="f-14">SL.No.</th>
                    <th class="f-14">Item Name</th>
                    <th class="f-14">Description of Goods</th>
                    <th class="f-14">HSN/SAC Code</th>
                    <th class="f-14">Rate</th>
                    <th class="f-14">Qty</th>
                    <th class="f-14">Amount</th>
                </tr>

                 <?php $seq =1 ; foreach($transaction_items as $ta){ ?>
                  <tr class="text-center">
                    <td class="f-14"><?php echo $seq; ?></td>
                    <td class="f-14"><?php echo $ta['item_name']; ?></td>
                    <td class="f-14"> <?php echo emptyCheck($ta['description']); ?></td>
                    <td class="f-14"><?php echo emptyCheck($parentrowidwithtax[$ta['id']]['tax_name']); ?></td>
                    <td class="f-14"><?php echo currencyFormat($ta['item_price'],$config); ?></td>
                    <td class="f-14"><?php echo emptyCheck($ta['item_quantity']); ?></td>
                    <td class="f-14"><?php echo currencyFormat($ta['total_amount'],$config); ?></td>
                </tr>
                <?php $seq++; } ?>
                <tr>
                    <th colspan="4" class="text-start">
                        Amount In words(Rupees):- <?php echo numberToWordFormate($transaction['total_amount']);?> 
                    </th>
                    <td class="p-0" colspan="3">
                        <table class="w-100 border-collapse">
                            <tr>
                                <th class="f-14">Total</th>
                                <th class="f-14 text-end" colspan="2"><?php echo currencyFormat($transaction['amount'],$config); ?></th>
                            </tr>
                            <tr>
                                <td class="f-14">Less</td>
                                <td class="f-14">Discount</td>
                                <td class="f-14"><?php echo currencyFormat($transaction['discount_amount'],$config); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <span class="f-14 d-block">Insurance Declaration No.  - </span>
                        <span class="f-14 d-block">Insurance Under Policy No.  - </span>
                    </td>
                    <td colspan="3" class="f-14 p-0">
                        <table class="w-100 border-collapse">
                            <tr>
                                <th class="text-start">Other Charges</th>
                                <th><?php echo currencyFormat($transaction['shipping_amount'],$config); ?></th>
                            </tr>
                            <tr>
                                <th class="text-start">Total Taxable Value</th>
                                <th><?php echo currencyFormat($transaction['amount'],$config); ?></th>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="p-0" colspan="4">
                        <table class="w-100 border-collapse">
                            <tr>
                                <th class="f-14">L.R.No.</th>
                                <th class="f-14">-</th>
                                <th class="f-14">L.R.Date</th>
                                <th class="f-14">-</th>
                                <th class="f-14">Transport</th>
                                <th class="f-14">-</th>
                            </tr>
                            <tr>
                                <th colspan="6">
                                    <span class="f-14 d-block"><?php echo emptyCheck($bank_accounts['bank_name']); ?>, <?php echo emptyCheck($bank_accounts['bank_address']); ?>, CODE No. : -</span> 
                                    <span class="f-14 d-block m-b-20">A/C. No. :<?php echo emptyCheck($bank_accounts['bank_account_number']); ?> IFSC CODE: <?php echo emptyCheck($bank_accounts['ifsc_code']); ?></span>
                                </th>
                                <th colspan="2">
                                    <?php if($bank_accounts['upi_qr_code_image'] != ''){ ?>
                                    
                                        <h3 style="font-size: 12px;color:#333333;margin: 0;font-weight: 500;">Scan to pay via UPI</h3>
                                         <img src="uploads/all/<?php echo $bank_accounts['upi_qr_code_image']; ?>" style="width:80px;margin: 10px 0 0 0;"> 
                                        <?php if($bank_accounts['upi_id'] !=""){ ?>
                                        <p style="font-size: 12px;color:#333333;font-weight: bold;"><?php echo emptyCheck($bank_accounts['upi_id']); ?></p>
                                    <?php } ?>
                                    <?php } ?>
                                </th>
                            </tr>
                        </table>
                    </td>
                    <td class="p-0" colspan="3">
                        <table class="w-100 border-collapse">



                            <?php  if(count((array)$distax)){
                                    foreach((array)$distax as $ktax){  $tattax +=$ktax['tax_amount']; ?>
                                    <?php //if($table1['tax_state'] == 'S'){ 
                                        if($transaction['originator_state_id'] == $transaction['supplier_state_id']){ ?>
                                    <tr>
                                        <th class="f-14 text-start"><?php echo $split_1_name; ?>  @ <?php echo $ktax['percentage']*$split_1; ?>%</th>
                                        <th class="f-14 text-end"><?php echo currencyFormat($ktax['tax_amount']*$split_1,$config); ?></th>
                                    </tr>
                                    <tr>
                                        <th class="f-14 text-start"><?php echo $split_2_name; ?> @ <?php echo currencyFormat($ktax['percentage']*$split_2,$config); ?>%</th>
                                        <th class="f-14 text-end"><?php echo currencyFormat($ktax['tax_amount']*$split_2,$config); ?></th>
                                    </tr>
                            
                                <?php } else{  ?> 

                                    <tr>
                                        <th class="f-14 text-start"><?php echo $split_1_name; ?> @ <?php echo currencyFormat($ktax['percentage'],$config); ?></th>
                                        <th class="f-14 text-end"><?php echo currencyFormat($ktax['tax_amount'],$config); ?></th>
                                    </tr>
                                <?php }?>
                            <?php } }  ?>

                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="f-14" colspan="4">
                        <span class="d-block">Additional Notes</span>
                        <span class="d-block"><?php echo emptyCheck($transaction['printing_notes']); ?></span>
                    </td>
                    <td class="p-0" colspan="3">
                        <table class="w-100 border-collapse">
                            <tr>
                                <th class="f-14 text-start">TOTAL INVOICE VALUE:-</th>
                                <th class="f-14 text-end">Rs. <?php echo currencyFormat($transaction['total_amount'],$config); ?></th>
                            </tr>
                            <tr>
                                <td class="f-14" colspan="2">
                                    <span class="text-end d-block">For : <?php echo $config['name']; ?></span>
                                    <span class="text-start m-t-20 d-block">Checked By</span>
                                    <span class="text-end m-t-20 d-block">Authorised Signatory</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>



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