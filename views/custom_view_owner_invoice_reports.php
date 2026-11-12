<?php 
$_SESSION['apply_js']['multiselest'] = "1";
$start_month = $_POST['start_month'] !=""?$_POST['start_month']:date('Y-01');
$end_month = $_POST['end_month'] !=""?$_POST['end_month']:date('Y-12');
$url = API."?mode=getownerinvoicereports&start_month=".$start_month."&end_month=".$end_month."&property_owner_id=".$_POST['property_owner_id']."&property_owner_detail_id=".$_POST['property_owner_detail_id'];
$api = callAPI($url);
$result = json_decode($api,1);  
$property_owners = $result['property_owners'];
$property_owner_details = $result['property_owner_details'];
//$rental_invoices = $result['rental_invoices'];
$customers = $result['customers'];


$invoices = $result['invoices'];
//filter
$all_property_owners = $result['all_property_owners'];
$all_property_owner_details = $result['all_property_owner_details'];
//filter
//echo "<pre>";print_r($result); echo "</pre>";
?>
<script type="text/javascript">
function printReport(divId) {
   	var printContents = $("#" + divId).html();
	var printWindow = window.open('', '_blank');
	printWindow.document.write('<html><head><title></title></head><body>' + printContents + '</body></html>');
	printWindow.document.close(); // close the document stream
	printWindow.print();
	printWindow.onafterprint = function() {
	    printWindow.close();
	};
}
function getOwnerProperty(valu){
	$.ajax({
      	url: "ajax.php",
      	type: "POST",
      	sync: false,
      	data: "page=getpropertyownerdetails&property_owner_id="+valu,
      	success: function(response){
        	$("#property_owner_detail_id").html(response);
      	}
  	});
}

</script>
<div class="card">
	<div class="card-header">
        <h5>Owner Invoice Reports</h5>
        <div class="card-header-right table-card-header ">
			<a href="javascript:history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i>Back</a>
    	</div>
    </div>
    <div class="card-body">
    	<form method="post">
	    	<div class="row">
				<div class="col-lg-3">
		            <div class=" form-group">
		                <label class="form-label"> Owner Name</label>
		                <select class="js-example-basic-single form-control" onchange="getOwnerProperty(this.value)" name="property_owner_id">
		                	<option value="">All Owner</option>
		                	<?php foreach($all_property_owners as $apo){ ?>
		                		<option value="<?php echo $apo['id'];?>" <?php echo $_POST['property_owner_id'] == $apo['id']?"selected":""; ?>><?php echo $apo['owner_name'];?></option>
		                	<?php } ?>
		                </select>
		            </div>
		        </div> 
		        <div class="col-lg-3">
		            <div class=" form-group">
		                <label class="form-label"> Property Name</label>
		                <select class="js-example-basic-single form-control " id="property_owner_detail_id" name="property_owner_detail_id">
		                	<option value="">All Property</option>
		                	<?php foreach($all_property_owner_details as $apo){ ?>
		                		<option value="<?php echo $apo['id'];?>" <?php echo $_POST['property_owner_detail_id'] == $apo['id']?"selected":""; ?>><?php echo $apo['property_name'];?></option>
		                	<?php } ?>
		                </select>
		            </div>
		        </div>
			    <div class="col-lg-2">    
		            <div class="form-group w-full table-card-header">
		                <label class="form-label ">Start Month<span class="text-danger">*</span></label>
						<input type="month" class="form-control" name="start_month"  value="<?php echo $start_month; ?>" >
		            </div>
		        </div>
		        <div class="col-lg-2">
		            <div class=" form-group">
		                <label class="form-label"> End Month</label>
		                <input type="month" class="form-control" name="end_month"  value="<?php echo $end_month; ?>" >
		            </div>
		        </div>
		        <div class="col-lg-1">
		             <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-t-30" name="searchfilter"> Search</button>
		        </div>
	    	</div>
	    </form>
    	<div id="print_report_div">




<!-- Google font-->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">

<!-- Required Fremwork -->
<link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/style.css">
<link rel="stylesheet" type="text/css" href="assets/css/custom.css">

<style type="text/css">
@media print {
    .heading{font-size: 30px;}
    .description{font-size: 16px;}
    th, td{font-size: 14px;}
}
</style>

	    	<?php foreach($property_owners as $powner){ ?>
	    	<div class="row mt-2">
	    		<h3 class="text-center text-primary heading">
	    			<?php echo ucwords($powner['owner_name']); ?>
	    			<span class="f-14 d-block text-dark mt-1 text-center description"><?php echo $powner['address']; ?></span>
	    			<span class="f-14 d-block text-dark mt-1 text-center description">GST : <?php echo $powner['gst_no']; ?></span>
	    			<span class="f-14 d-block text-dark mt-1 text-center description"><?php echo "From - ".date("Y M",strtotime($start_month))." To - ".date("Y M",strtotime($end_month)); ?></span>		
	    		</h3>
	    		<?php 
	    			$podetails = $property_owner_details[$powner['id']];
	    			foreach($podetails as $podetail){
	    		?>
	    		<h4 class="text-start text-primary mt-5 heading">
	    			<?php echo ucwords($podetail['property_name']); ?>
	    			<span class="f-14 d-block text-dark mt-1"><?php echo $podetail['property_address']; ?></span>
	    		</h4>
	    		<table class="table m-b-0" style="font-size:13px;"> 
		            <thead>
		                <tr> 
		                    <th rowspan="2">#</th>      
		                    <th rowspan="2">Tenant Name</th> 
		                    <th colspan="4" class="text-center">Rent</th>
		                    <th colspan="4" class="text-center">Utility</th> 
		     			    <th colspan="4" class="text-center">Total</th>
		                </tr>
		                <tr>
		                	<th class="text-right">Invoice Amt</th> 
		                    <th class="text-right">DN/CN</th> 
		                    <th class="text-right">Payments</th>
		                    <th class="text-right">Due Amt</th>
		                    <th class="text-right">Invoice Amt</th> 
		                    <th class="text-right">DN/CN</th> 
		                    <th class="text-right">Payments</th>
		                    <th class="text-right">Due Amt</th>
		                    <th class="text-right">Invoice</th>
		                    <th class="text-right">DN/CN</th>
		                    <th class="text-right">Payments</th>
		                    <th class="text-right">Due</th>
		                </tr>
		            </thead>
		            <tbody>
		            	<?php
		            		$t_rent_total_amount = $t_rent_payment =$t_rent_pending_amount =$t_utility_total_amoun = $t_utility_payment = $t_utility_pending_amount = $t_both_total = $t_both_payment = $t_both_due =$t_both_rent_dr=$t_both_rent_cr=$t_both_utility_dr=$t_both_utility_cr=$t_both_dr=$t_both_cr = 0;
		                	$rentinvoices = $invoices[$powner['id']][$podetail['id']]; // $rental_invoices[oenwerid][detailsid];
		                	$i=1;	
		                	foreach($rentinvoices as $custid =>$rentinvoice){
		                		$customer_detail = $customers[$custid];
		                		$rentpayment = $rentinvoice['rent']['total_amount']-$rentinvoice['rent']['pending_amount'];
		                		$utilitypayment = $rentinvoice['utility']['total_amount']-$rentinvoice['utility']['pending_amount'];
		                		$total_payment = $rentpayment+$utilitypayment;
		                		$total_amount = $rentinvoice['rent']['total_amount']+$rentinvoice['utility']['total_amount'];
		                		$total_due = $rentinvoice['rent']['pending_amount']+$rentinvoice['utility']['pending_amount'];

		                		$total_dr = $rentinvoice['rent']['debit_total_amount']+$rentinvoice['utility']['debit_total_amount'];
		                		$total_cr = $rentinvoice['rent']['credit_total_amount']+$rentinvoice['utility']['credit_total_amount'];

		                		$t_rent_total_amount += $rentinvoice['rent']['total_amount'];
		                		$t_rent_payment += $rentpayment;
		                		$t_rent_pending_amount += $rentinvoice['rent']['pending_amount'];
		                		$t_utility_total_amoun += $rentinvoice['utility']['total_amount'];
		                		$t_utility_payment += $utilitypayment;
		                		$t_utility_pending_amount += $rentinvoice['utility']['pending_amount'];
		                		$t_both_total += $total_amount;
		                		$t_both_payment += $total_payment;
		                		$t_both_due += $total_due;
		                		$t_both_rent_dr += $rentinvoice['rent']['debit_total_amount']; 
		                		$t_both_rent_cr += $rentinvoice['rent']['credit_total_amount'];
		                		$t_both_utility_dr += $rentinvoice['utility']['debit_total_amount']; 
		                		$t_both_utility_cr += $rentinvoice['utility']['credit_total_amount'];
		                		$t_both_dr += $total_dr;
		                		$t_both_cr += $total_cr;

		                ?>
		                <tr>
		                	<td class="p-2"><?php echo $i; ?></td>
		                	<td class="p-2 w-b-w-s"><?php echo $customer_detail['display_name']; ?>
		                		<!-- <p class="d-block text-start mt-1 mb-0 " >
		                			<span data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Opening Balance">O : <?php echo customeummarmonyformateifine($rentinvoice['rent']['ob_total_amount']); ?></span>
		                        <span class="m-l-5" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Payments">P : <?php echo customeummarmonyformateifine($rentinvoice['rent']['ob_total_amount']-$rentinvoice['rent']['ob_pending_amount']); ?></span>
		                        <span class="m-l-5" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Due Amount">D : <?php echo customeummarmonyformateifine($rentinvoice['rent']['ob_pending_amount']); ?></span></p> -->

		                	</td>
		                	
		                	<td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentinvoice['rent']['total_amount']); ?></td>
		                	<td class="p-2 text-right">
		                		<span class="d-block"><?php echo customeummarmonyformateifine($rentinvoice['rent']['debit_total_amount']-$rentinvoice['rent']['credit_total_amount']); ?></span>
		                	</td>
		                	<td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentpayment); ?></td>
		                	<td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentinvoice['rent']['pending_amount']); ?></td>
		                	<td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentinvoice['utility']['total_amount']); ?></td>
		                	<td class="p-2 text-right">
		                		<span class="d-block"><?php echo customeummarmonyformateifine($rentinvoice['utility']['debit_total_amount']-$rentinvoice['utility']['credit_total_amount']); ?></span>
		                	</td>
		                	<td class="p-2 text-right"><?php echo customeummarmonyformateifine($utilitypayment); ?></td>
		                	<td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentinvoice['utility']['pending_amount']); ?></td>
		                	<td class="p-2 text-right"><?php echo customeummarmonyformateifine($total_amount); ?></td>
		                	<td class="p-2 text-right">
		                		<span class="d-block"><?php echo customeummarmonyformateifine($total_dr-$total_cr); ?></span>
		                	</td>
		                	<td class="p-2 text-right"><?php echo customeummarmonyformateifine($total_payment); ?></td>
		                	<td class="p-2 text-right"><?php echo customeummarmonyformateifine($total_due); ?></td>
		                </tr>

		            	<?php $i++; } ?>
		            </tbody>
		            <tfoot>
		            	<tr>
		            		<th colspan="2" class="text-right">Total </th>
		            		<th class="text-right"><?php echo customeummarmonyformateifine($t_rent_total_amount); ?></th>
		            		<th class="text-right">
		            			<span class="d-block"><?php echo customeummarmonyformateifine($t_both_rent_dr-$t_both_rent_cr); ?></span>
		                	</th>
		            		<th class="text-right"><?php echo customeummarmonyformateifine($t_rent_payment); ?></th>
		            		<th class="text-right"><?php echo customeummarmonyformateifine($t_rent_pending_amount); ?></th>
		            		<th class="text-right"><?php echo customeummarmonyformateifine($t_utility_total_amoun); ?></th>
		            		<th class="text-right">
		            			<span class="d-block"><?php echo customeummarmonyformateifine($t_both_utility_dr-$t_both_utility_cr); ?></span>
		                	</th>
		            		<th class="text-right"><?php echo customeummarmonyformateifine($t_utility_payment); ?></th>
		            		<th class="text-right"> <?php echo customeummarmonyformateifine($t_utility_pending_amount); ?></th>
		            		<th class="text-right"><?php echo customeummarmonyformateifine($t_both_total); ?></th>
		            		<th class="text-right">
		            			<span class="d-block"><?php echo customeummarmonyformateifine($t_both_dr-$t_both_cr); ?></span>
		                	</th>
		            		<th class="text-right"><?php echo customeummarmonyformateifine($t_both_payment); ?></th>
		            		<th class="text-right"><?php echo customeummarmonyformateifine($t_both_due); ?></th>
		            	</tr>
		            </tfoot>
		        </table>
		    <?php } ?>
	    	</div>
	    <?php } ?>
		</div>
		<div class="row mt-5 m-l-5">
	  		<button class="btn waves-effect waves-light btn-grd-success col-lg-2" onclick="printReport('print_report_div');"><i class="fa fa-print"></i> &nbsp Print Report</button>
	  	</div>
    </div>
    
</div>