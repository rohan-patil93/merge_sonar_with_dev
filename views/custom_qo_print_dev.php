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

//print_r($tax_authorities_rates_split);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
            <h1 style="font-size:30px;color: #000000;margin: 30px 0 50px 0;text-align: left;text-transform: uppercase;"><?php echo urldecode($_GET['title']); ?> 
            <div class="" style="float: right;margin-right: 15px;">
                <img src="uploads/all/<?php echo $config['display_logo']; ?>" style="width:100px;">
            </div>
            </h1>
            <div class="" style="width:49%;display: inline-block;vertical-align: middle;">
                <table style="width: 100%;">
                    <tr>
                        <th style="padding-top: 5px;text-align: left;font-size: 12px;">Invoice Number:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $transaction['transaction_number']; ?></td>
                    </tr>
                    <tr>
                        <th style="padding-top: 5px;text-align: left;font-size: 12px;">Invoice Date:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo date("d M Y",strtotime($transaction['transaction_date'])); ?></td>
                    </tr>
                    <tr>
                        <th style="padding-top: 5px;text-align: left;font-size: 12px;">Due Date:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo date("d M Y",strtotime($transaction['due_date'])); ?></td>
                    </tr>
                    <tr>
                        <th style="padding-top: 5px;text-align: left;font-size: 12px;">Contact Person:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $bill_by['display_name']; ?></td>
                    </tr>
                    <tr>
                        <th style="padding-top: 5px;text-align: left;font-size: 12px;">Email ID:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo emptyCheck($bill_by['user_email']); ?></td>
                    </tr>
                    <tr>
                        <th style="padding-top: 5px;text-align: left;font-size: 12px;">Phone Number:</th>
                        <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo emptyCheck($bill_by['user_mobile']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="" style="width:50%;display: inline-block;vertical-align: middle;">
                <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;margin-left: 5px;">
                    <span style="color: #333333;font-size: 12px;font-weight: 500;">Billed by:</span>
                    <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $bill_by['display_name']; ?></p>
                    <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo emptyCheck($transaction['party_address']); ?></p>
                    <table style="width: 100%;">
                        <tr>
                            <th style="padding-top: 5px;font-size: 12px;text-align: left;">GSTIN:</th>
                            <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo emptyCheck($config['gst_number']); ?></td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px;text-align: left;">PAN:</th>
                            <td style="text-align: right;font-size: 12px;"><?php echo emptyCheck($config['pan_number']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <table style="width:100%;margin-top: 5px;">
                <tr>
                    <td class="" style="width:50%;">
                        <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
                            <span style="color: #333333;font-size: 12px;font-weight: 500;">Billed to</span>
                            <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $bill_by['display_name']; ?></p>
                            <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo $transaction['party_address']; ?></p>
                            <table style="width: 100%;">
                                <tr>
                                    <th style="padding-top: 5px;font-size: 12px;text-align: left;">GSTIN:</th>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['gstin']); ?></td>
                                </tr>
                                <tr>
                                    <th style="font-size: 12px;text-align: left;">PAN:</th>
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
                                    <th style="padding-top: 5px;font-size: 12px;text-align: left;">GSTIN:</th>
                                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['gstin']); ?></td>
                                </tr>
                                <tr>
                                    <th style="font-size: 12px;text-align: left;">PAN:</th>
                                    <td style="text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['pan_no']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                <tr>
            </table>
            <table style="width:99%;margin:10px 0 0 0;">
                <tr style="background: #eeeeee;">
                    <th style="padding:5px;text-align: left;font-size: 12px;">Item Name</th>
                    <th style="padding:5px;text-align: left;font-size: 12px;">Note</th>
                    <!--<th style="padding:10px;text-align: right;">HSN/SAC</th>
                    <th style="padding:10px;text-align: right;">GST Rate</th>
                    <th style="padding:10px;text-align: right;">QTY.</th> -->
                    <th style="padding:5px;text-align: right;font-size: 12px;">Rate</th>
                    <th style="padding:5px;text-align: right;font-size: 12px;">Tax</th>
                    <!--<th style="padding:10px;text-align: right;">IGST</th> -->
                    <th style="padding:5px;text-align: right;font-size: 12px;">Total</th>
                </tr>
                <?php foreach($transaction_items as $ta){ ?>
                <tr style="border: 1px solid #cccccc;">
                    <td style="padding:5px;font-size: 12px; white-space: normal;"><?php echo $ta['item_name']; ?></td>
                    <td style="padding:5px;text-align: left;font-size: 12px; white-space: normal;"><?php echo emptyCheck($ta['description']); ?></td>
                    <td style="padding:5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($ta['item_amount'],$config); ?></td>
                    <td style="padding:5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($ta['tax_amount'],$config); ?></td>
                    <td style="padding:5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($ta['total_amount'],$config); ?></td>
                </tr>
                
                <?php } ?>
            </table>
            <table style="width:99%;margin-top:5px;">
                <tr>
                    <td style="width:60%;vertical-align: top;">
                        <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
                            <h3 style="font-size: 12px;color:#333333;margin: 0;">Bank & UPI Detail</h3>
                            <table style="width: 100%; margin:10px 0 0 0;">
                                <tr>
                                    <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Bank Name:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['bank_name']); ?></td>
                                    <td rowspan="6" align="center" style="width: 20%;">
                                        <h3 style="font-size: 12px;color:#333333;margin: 0;font-weight: 500;">Scan to pay via UPI</h3>
                                        <img src="uploads/all/upi.jpg" style="width:80px;margin: 10px 0 0 0;">
                                        <p style="font-size: 12px;color:#333333;font-weight: bold;"><?php echo emptyCheck($bank_accounts['upi_id']); ?></p>
                                        <!-- <p style="font-size:13px;color:#333333;white-space:initial;">Maximum of 1 lakh can be transfered via UPI in a single day</p> -->
                                    </td>
                                </tr>
                                <tr>
                                    <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Account Holder:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['bank_contact_person']); ?></td>
                                </tr>
                                <tr>
                                    <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Account Number:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['bank_account_number']); ?></td>
                                </tr>
                                <tr>
                                    <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">IFSC Number:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['ifsc_code']); ?></td>
                                </tr>
                                <tr>
                                    <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">Account Type:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php if($bank_accounts['account_type'] =="C"){
                                        echo "Current Account";
                                    }if($bank_accounts['account_type'] =="S"){
                                        echo "Saving Account";
                                    } ?>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;">UPI:</th>
                                    <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($bank_accounts['upi_id']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td style="width:40%;vertical-align: top;">
                        <table style="width: 100%; margin:10px 0 0 0;">
                            <tr>
                                <th style="padding-top: 5px;font-size: 12px;text-align: left;">Sub Total</th>
                                <td style="padding-top: 5px;padding-bottom: 5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($transaction['amount'],$config); ?></td>
                            </tr>
                            <tr>
                                <th style="padding-top: 5px;font-size: 12px;text-align: left;">Discount</th>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($transaction['discount_amount'],$config); ?></td>
                            </tr>
                            <tr>
                                <th style="padding-top: 5px;font-size: 12px;text-align: left;">Shipping Charges</th>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($transaction['shipping_amount'],$config); ?></td>
                            </tr>

                            <?php  if(count((array)$distax)){
                                    foreach((array)$distax as $ktax){  $tattax +=$ktax['tax_amount']; ?>
                                    <?php //if($table1['tax_state'] == 'S'){ 
                                        if($transaction['originator_state_id'] == $transaction['supplier_state_id']){ ?>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?php echo $split_1_name; ?>  @ <?php echo $ktax['percentage']*$split_1; ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?>                
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;"> 
                                            <span id="dis_taxRateid"><?php echo customeummarmonyformateifine($ktax['tax_amount']*$split_1); ?></span> 
                                            <input  value="<?php echo customeummarmonyformate($ktax['tax_amount']*$split_1); ?>" type="hidden" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?php echo $split_2_name; ?> @ <?php echo customeummarmonyformate($ktax['percentage']*$split_2); ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?>               
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;"> 
                                            <span id="dis_taxRateid"><?php echo customeummarmonyformateifine($ktax['tax_amount']*$split_2); ?></span> 
                                            <input  value="<?php echo customeummarmonyformate($ktax['tax_amount']*$split_2); ?>" type="hidden" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">           
                                        </td>
                                    </tr>
                                <?php } else{  ?> 
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?php echo $split_1_name; ?> @ <?php echo customeummarmonyformate($ktax['percentage']); ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?>         
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;">  
                                            <span id="dis_taxRateid"><?php echo customeummarmonyformateifine($ktax['tax_amount']); ?></span>
                                            <input  value="<?php echo customeummarmonyformate($ktax['tax_amount']); ?>" type="hidden" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">            
                                        </td>
                                    </tr>
                                <?php }?>
                            <?php } }  ?>

                            <?php /** if($transaction['originator_state_id'] == $transaction['supplier_state_id']){ ?>
                             <tr>
                                <th style="padding-top: 5px;font-size: 12px;text-align: left;">IGST</th>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($transaction['tax_amount'],$config); ?></td>
                            </tr>
                            <?php }else{ ?>
                            <tr>
                                <th style="padding-top: 5px;font-size: 12px;text-align: left;">CGST</th>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($transaction['tax_amount']/2,$config); ?></td>
                            </tr>
                            <tr>
                                <th style="padding-top: 5px;font-size: 12px;text-align: left;">SGST</th>
                                <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo currencyFormat($transaction['tax_amount']/2,$config); ?></td>
                            </tr>
                            <?php } **/ ?>
                            <tr style="border-top:1px solid #444444;border-bottom: 1px solid #444444;">
                                <th style="padding-top: 5px;text-align: left;border-top:1px solid #cccccc;font-weight: 500;"><span style="font-size: 21px;">Total</span> </th>
                                <th style="padding-top: 5px;text-align: right;border-top:1px solid #cccccc;font-weight: 500"><span style="font-size:21px;"><?php echo currencyFormat($transaction['total_amount'],$config); ?></span> </th>
                            </tr>
                        </table>
                        <div style="margin-top: 10px;vertical-align: top;">
                            <span style="font-size: 12px; color: #333333;font-weight: 600;">Invoice total in words</span>
                            <p style="font-size: 12px; color: #333333; margin-top: 10px;white-space:initial;"><?php echo numberToWordFormate($transaction['total_amount']);?> </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width:60%;vertical-align: top;">
                        <!-- <div class="" style="display: block; box-sizing:border-box;background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right: 10px;margin-top: 10px;">
                            <h3 style="font-size: 12px;color:#333333;margin: 0;font-weight:bold;">Term & Condition</h3>
                            <p style="font-size:12px;color:#333333;font-weight:500;white-space:initial;margin-top: 10px;">Please pay within 15 days from the date of invoice, overdue intrest @ 14% will be charged on display payment.</p>
                            <p style="font-size:13px;color:#333333;font-weight:500;white-space:initial;">Please quote invoice number when remaining fund.</p>
                        </div> -->
                        <div class="" style="display: block; box-sizing:border-box;background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right: 10px; margin-top:10px;">
                                <h3 style="font-size: 12px;color:#333333;margin: 0;font-weight:bold;">Additional Notes</h3>
                                <p style="font-size:12px;color:#333333;"><?php echo emptyCheck($transaction['printing_notes']); ?></p>
                        </div>
                    </td>
                    <td style="width:40%;">
                        <div style="vertical-align: top;">
                            <span style="font-size: 12px;color:#333333;display: block;text-align: center;font-weight: 500;">For infisuite product & services</span>
                            <p style="background: #eeeeee;height:45px;margin-bottom: 0px;margin-top: 0;"></p>
                            <span style="font-size: 12px;color:#333333;display: block;text-align: center;font-weight: 500;">Authorised Signature</span>
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