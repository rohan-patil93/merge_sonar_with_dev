<?php 

if($_GET['print'] =="Y"){
	include('../includes/session.php');
	include('../dbconfig.php');
	include('../comm.php');
	$callapi = "Y";
}else{
	include_once('dbconfig.php');
}
//print_r($_GET);
$transaction_table = $_GET['table']!=""?$_GET['table']:$_GET['transaction_table'];
if($_POST['callapi'] =='Y'){
	$config = $_POST['config'];
	$transaction = $_POST['rental_invoices'];
	$transaction_items = $_POST['rental_invoice_items'];
	$bill_by = $_POST['bill_by'];
	$bank_accounts = $_POST['bank_accounts'];
	$callapi = "Y";
	$transaction_table = $_POST['transaction_table'];
	$rental_invoice_item_taxes = $_POST['rental_invoice_item_taxes'];
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
	$rental_invoice_item_taxes = $result['rental_invoice_item_taxes'];
}

//echo "<pre>"; print_r($rental_invoice_item_taxes); echo "</pre>";
$party_additional_details = json_decode($transaction['party_additional_details'],1);
$owner_additional_details = json_decode($transaction['owner_additional_details'],1);
$rental_utilities = $result['rental_utilities'];

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


//echo "<pre>"; print_r($distax); echo "</pre>";




// function currencyFormat($number,$config){
// 	$num = $config['number_format'] !='' ? $config['number_format'] :2;
// 	$return = number_format($number, $num, '.', ','); 
// 	return $return!='0.00' ?$return : '-'; 
// }
$rental_invoice_item_taxes = array();
$totaltax = 0;
foreach((array)$result['rental_invoice_item_taxes'] as $riit){
	$rental_invoice_item_taxes[$riit['transaction_item_id']] = $riit;
	$totaltax +=  $riit['tax_value'];
}

//echo "<pre>"; print_r($rental_invoice_item_taxes); print_r($transaction_items); echo $totaltax;  echo "</pre>";
function currencyFormat($number,$config){
	//print_r($_SESSION['config']['number_format']);
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
// 	$configdate_format = str_replace('-',$config['date_separator'], strtolower($config['date_format']));
// 	$date_formate = str_replace('yyyy', 'Y', str_replace('mm', 'm', str_replace('dd', 'd', $configdate_format)));
// 	$from = $from!='0000-00-00' ? $from :'';
// 	$dateformat = $date_formate!="0"?$date_formate: 'Y-m-d';
//     if($from != ""){
// 		$from =  date($dateformat,strtotime($from));
// 	}else{
// 		$from = date($dateformat);
// 	}
// 	return $from; 
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
				<?php 
				if($transaction_table == "utility_rental_invoices"){
					echo __('Reimbursement Bill');
				}elseif($transaction_table == "credit_notes"){
					echo __('Credit Notes');
				}elseif($transaction_table == "utility_amc_rental_invoices"){
					echo __('AMC Invoice');
				}elseif($transaction_table == "debit_notes"){
					echo __('Debit Notes');
				}else{
					echo __('Tax Invoice');
				}
				?>
			<div class="" style="float: right;margin-right: 15px;">
	            <img src="uploads/all/<?php echo $config['display_logo']; ?>" style="width:100px;">
	        </div>
			</h1>
	        <div class="" style="width:49%;display: inline-block;vertical-align: middle;">
	            <table style="width: 100%;">
	                <tr>
	                    <th style="padding-top: 5px;text-align: left;font-size: 12px;"><?= __('Invoice Number'); ?>:</th>
	                    <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $transaction['transaction_number']; ?></td>
	                </tr>
	                <tr>
	                    <th style="padding-top: 5px;text-align: left;font-size: 12px;"><?= __('Invoice Date'); ?>:</th>
	                    <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo date("d M Y",strtotime($transaction['transaction_date'])); ?></td>
	                </tr>
	                <tr>
	                    <th style="padding-top: 5px;text-align: left;font-size: 12px;"><?= __('Due Date'); ?>:</th>
	                    <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo date("d M Y",strtotime($transaction['due_date'])); ?></td>
	                </tr>
	                <tr>
	                    <th style="padding-top: 5px;text-align: left;font-size: 12px;"><?= __('Contact Person'); ?>:</th>
	                    <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $owner_additional_details['owner_name']!=""?$owner_additional_details['owner_name']:"-"; ?></td>
	                </tr>
	                <tr>
	                    <th style="padding-top: 5px;text-align: left;font-size: 12px;"><?= __('Email ID'); ?>:</th>
	                    <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $owner_additional_details['email_id']!=""?$owner_additional_details['email_id']:"-"; ?></td>
	                </tr>
	                <tr>
	                    <th style="padding-top: 5px;text-align: left;font-size: 12px;"><?= __('Phone Number'); ?>:</th>
	                    <td style="padding-top: 5px;text-align: left;font-size: 12px;"><?php echo $owner_additional_details['contact_no']!=""?$owner_additional_details['contact_no']:$bill_by['user_mobile']; ?></td>
	                </tr>
	            </table>
	        </div> 
	        <div class="" style="width:50%;display: inline-block;vertical-align: middle;">
	            <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;margin-left: 5px;">
		            <span style="color: #333333;font-size: 12px;font-weight: 500;"><?= __('Billed by'); ?>:</span>
		            <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $owner_additional_details['owner_company_name']!=""?$owner_additional_details['owner_company_name']:$config['name']; ?></p>
		            <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo emptyCheck($owner_additional_details['address']!=""?$owner_additional_details['address']:$config['contact_address']); ?></p>
		            <table style="width: 100%;">
		                <tr>
		                    <th style="padding-top: 5px;font-size: 12px;text-align: left;"><?= __('GSTIN'); ?>:</th>
		                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php //echo emptyCheck($owner_additional_details['gst_no']!=""?$owner_additional_details['gst_no']:$config['gst_number']); 
		                   echo $owner_additional_details['gst_no']!=""?$owner_additional_details['gst_no']:"-"; ?></td>
		                </tr>
		                <tr>
		                    <th style="font-size: 12px;text-align: left;"><?= __('PAN'); ?>:</th>
		                    <td style="text-align: right;font-size: 12px;"><?php echo $owner_additional_details['pan_no']!=""?$owner_additional_details['pan_no']:"-"; ?></td>
		                </tr>
	            	</table>
	            </div>
	        </div>
	        <table style="width:100%;margin-top: 5px;">
	        	<tr>
			        <td class="" style="width:50%;">
			        	<div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
				            <span style="color: #333333;font-size: 12px;font-weight: 500;"><?= __('Billed to'); ?></span>
				            <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $transaction['party_name']; ?></p>
				            <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo $transaction['party_address']; ?></p>
				            <table style="width: 100%;">
				                <tr>
				                    <th style="padding-top: 5px;font-size: 12px;text-align: left;"><?= __('GSTIN'); ?> :</th>
				                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['gstin']); ?></td>
				                </tr>
				                <tr>
				                    <th style="font-size: 12px;text-align: left;"><?= __('PAN'); ?>:</th>
				                    <td style="text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['pan_no']); ?></td>
				                </tr>
			            	</table>
			            </div>
			        </td>
			        <td class="" style="width:50%;">
			        	<div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
				            <span style="color: #333333;font-size: 12px;font-weight: 500;"><?= __('Shipped to'); ?></span>
				            <p style="font-size: 12px;margin:5px 0 0 0;"><?php echo $transaction['party_name']; ?></p>
				            <p style="font-size: 12px;color:#333333;margin:5px 0 0 0;white-space:initial;min-height: 30px;"><?php echo emptyCheck($transaction['party_ship_adderss']); ?></p> 
				            <table style="width: 100%;">
				                <tr>
				                    <th style="padding-top: 5px;font-size: 12px;text-align: left;"><?= __('GSTIN'); ?>:</th>
				                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php echo emptyCheck($party_additional_details['gstin']); ?></td>
				                </tr>
				                <tr>
				                    <th style="font-size: 12px;text-align: left;"><?= __('PAN'); ?>:</th>
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
		                <th style="padding:5px;text-align: left;font-size: 12px;"><?= __('Particular'); ?></th>
		                <th style="padding:5px;text-align: left;font-size: 12px;"><?= __('Note'); ?></th>
						<th style="padding:5px;text-align: right;font-size: 12px;"><?= __('Total'); ?></th>
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
			            			<table style="width:65%;margin:10px  10px 10px;">
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

	        	<?php } }else{ ?>
	            <tr style="background: #eeeeee;">
	                <th style="padding:5px;text-align: left;font-size: 12px;"><?= __('Particular'); ?></th>
	                <th style="padding:5px;text-align: left;font-size: 12px;"><?= __('Note'); ?></th>
					<th style="padding:5px;font-size: 12px;"><?= __('HSN/SAC'); ?></th>
	                <th style="padding:5px;font-size: 12px;"><?= __('Qty'); ?></th> 
	                <th style="padding:5px;font-size: 12px;"><?= __('UOM'); ?></th> 
	                <th style="padding:5px;text-align: right;font-size: 12px;"><?= __('Rate'); ?></th>
	              	<th style="padding:5px;text-align: right;font-size: 12px;"><?= __('Total'); ?></th>
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
	            </tr>
	        	<?php } } ?>
	        </table>
	        

	        <table style="width:99%;margin-top:5px;">
	        	<tr>
			        <td style="width:60%;vertical-align: top;">
			            <div class="" style="background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right:10px;">
		                    <h3 style="font-size: 12px;color:#333333;margin: 0;"><?= __('Bank Detail'); ?></h3>
		                    <table style="width: 100%; margin:10px 0 0 0;">
		                        <tr>
		                            <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;"><?= __('Bank Name'); ?>:</th>
		                            <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($owner_additional_details['bank_id']!=""?$banks[$owner_additional_details['bank_id']]:$banks[$bank_accounts['bank_name']]); ?></td>
		                        </tr>
		                        <tr>
		                            <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;"><?= __('Account Holder'); ?>:</th>
		                            <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($owner_additional_details['owner_company_name']!=""?$owner_additional_details['owner_company_name']:$bank_accounts['bank_contact_person']); ?></td>
		                        </tr>
		                        <tr>
		                            <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;"><?= __('Account Number'); ?>:</th>
		                            <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($owner_additional_details['bank_account_no']!=""?$owner_additional_details['bank_account_no']:$bank_accounts['bank_account_number']); ?></td>
		                        </tr>
		                        <tr>
		                            <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;"><?= __('IFSC Number'); ?>:</th>
		                            <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;"><?php echo emptyCheck($owner_additional_details['ifsc_code']!=""?$owner_additional_details['ifsc_code']:$bank_accounts['ifsc_code']); ?></td>
		                        </tr>
		                        <tr>
		                            <th style="padding-top: 5px;font-size: 12px;text-align: left;width: 30%;"><?= __('Account Type'); ?>:</th>
		                            <td style="padding-top: 5px;text-align: left;font-size: 12px;width: 50%;">
		                            	<?php  echo $owner_additional_details['account_type']!=""?$account_types[$owner_additional_details['account_type']]:$account_types[$bank_accounts['account_type']]; ?>
		                            	
		                            </td>
		                        </tr>
		                    </table>
			            </div>
			        </td>
			        <td style="width:40%;vertical-align: top;">
			            <table style="width: 100%; margin:10px 0 0 0;">
			            	<?php //if($transaction['ref_bill_type'] != "U"){ ?>
			                <tr>
			                    <th style="padding-top: 5px;font-size: 12px;text-align: left;"><?= __('Sub Total'); ?></th>
			                    <td style="padding-top: 5px;padding-bottom: 5px;text-align:right;font-size: 12px;"><?php echo currencyFormat($transaction['amount'],$config); ?></td>
			                </tr>
                            <?php  if(count((array)$distax)){
                                    foreach((array)$distax as $ktax){  $tattax +=$ktax['tax_amount']; ?>
                                    <?php 
                                        if($transaction['originator_state_id'] == $transaction['supplier_state_id']){ ?>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?= __('CGST @'); ?> <?php echo $ktax['percentage']/2; ?>%                
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;"> 
                                            <span id="dis_taxRateid"><?php echo currencyFormat($ktax['tax_amount']/2,$config); ?></span> 
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?= __('SGST @'); ?> <?php echo $ktax['percentage']/2; ?>%               
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;"> 
                                            <span id="dis_taxRateid"><?php echo currencyFormat($ktax['tax_amount']/2,$config); ?></span> 
                                                     
                                        </td>
                                    </tr>
                                <?php } else{  ?> 
                                    <tr>
                                        <td style="padding-top: 5px;font-size: 12px;text-align: left;">  <?= __('IGST @'); ?> <?php echo currencyFormat($ktax['percentage'],$config); ?><?= __('% on'); ?> <?php echo currencyFormat($ktax['amount'],$config); ?>         
                                        </td>
                                        <td style="padding-top: 5px;text-align: right;font-size: 12px;">  
                                            <span id="dis_taxRateid"><?php echo currencyFormat($ktax['tax_amount'],$config); ?></span>
                                           
                                        </td>
                                    </tr>
                                <?php }?>
                            <?php } //}  ?>
			                <tr>
			                    <th style="padding-top: 5px;font-size: 12px;text-align: left;"><?= __('Round Off'); ?></th>
			                    <td style="padding-top: 5px;text-align: right;font-size: 12px;"><?php 
			                    echo $transaction['rounding_off'];
			                    //echo round(round($transaction['total_amount'])-$transaction['total_amount'],2); ?></td>
			                </tr>
			                   <?php } ?>
			                <tr style="border-top:1px solid #444444;border-bottom: 1px solid #444444;">
			                    <th style="padding-top: 5px;text-align: left;border-top:1px solid #cccccc;font-weight: 500;"><span style="font-size: 21px;"><?= __('Grand Total'); ?></span> </th>
			                    <th style="padding-top: 5px;text-align: right;border-top:1px solid #cccccc;font-weight: 500"><span style="font-size:21px;"><?php echo currencyFormat(round($transaction['total_amount']),$config); ?></span> </th>
			                </tr>

			            </table>
			            <div style="margin-top: 10px;vertical-align: top;">
			            	<span style="font-size: 12px; color: #333333;font-weight: 600;"><?= __('Invoice total in words'); ?></span>
				            <p style="font-size: 12px; color: #333333; margin-top: 10px;white-space:initial;"><?php echo numberToWordFormate(round($transaction['total_amount']));?> </p>
				        </div>
			        </td>
			    </tr>
			    <tr>
			    	<td style="width:60%;vertical-align: top;">
			            <div class="" style="display: block; box-sizing:border-box;background:#eeeeee;border:1px solid #cccccc;border-radius: 10px;padding: 10px; margin-right: 10px; margin-top:10px;">
			                    <h3 style="font-size: 12px;color:#333333;margin: 0;font-weight:bold;"><?= __('Additional Notes'); ?></h3>
			                    <p style="font-size:12px;color:#333333;"><?php echo emptyCheck($transaction['printing_notes']); ?></p>
			            </div>
			    	</td>
			    	<td style="width:40%;">
			    		<div style="vertical-align: top;">
			            	<span style="font-size: 12px;color:#333333;display: block;text-align: center;font-weight: 500;"><?= __('For'); ?> <?php echo $owner_additional_details['owner_company_name']!=""?ucwords($owner_additional_details['owner_company_name']):ucwords($config['name']); ?></span>
				            <p style="background: #eeeeee;height:45px;margin-bottom: 0px;margin-top: 0;"></p>
				            <span style="font-size: 12px;color:#333333;display: block;text-align: center;font-weight: 500;"><?= __('Authorised Signature'); ?></span>
			            </div>
			    	</td>
			    </tr>
			</table>
	    </div> 
	    <?php if($callapi !='Y'){ ?> 
	</div>
	<div class="invoice-btn-section clearfix d-print-none">
        <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printDiv');">
            <i class="fa fa-print"></i> &nbsp <?= __('Print Invoice'); ?>
        </button>
        <button class="btn waves-effect waves-light btn-grd-info"  onclick="convertHTMLToPDF();">
            <i class="fa fa-download"></i> &nbsp <?= __('Download Invoice'); ?>
        </button>
    </div>
</body>
</html>
<?php } ?>