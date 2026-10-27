<?php
$_SESSION['apply_js']['multiselest'] = 1;
$transaction_table = $_POST['transaction_table']!=""?$_POST['transaction_table']:"vendors";
$title = ucwords(str_replace("_"," ",$transaction_table));
$vurl = API . "?mode=getvendorsdetailsreports&transaction_table=".$transaction_table."&vendor_types_id=".$_POST['vendor_types_id']."&location_id=".$_POST['location_id']."&vendor_connect_user=".$_POST['vendor_connect_user'];
$che = callAPI($vurl);
$result = json_decode($che, 1);
$vendorlist = $result['vendorlist'];
$locations = $result['locations'];
$application_users = $result['application_users'];
$vendor_types = $result['vendor_types'];
$states = $result['states'];
$countries = $result['countries'];
$cities = $result['cities'];
//echo "<pre>"; print_r($states); echo "</pre>"; 
$_SESSION['download_data'] = array();
?>

<div class="card">
	<div class="card-header">
		<h5 class="f-30">Vender & Vendor Register Report</h5>
		<div class="card-header-right">
			<div class="dropdown-primary dropdown open">
				<button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
				<div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
					<a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=<?php echo $transaction_table; ?>" target="_blank" >CSV</a>
					<a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=<?php echo $transaction_table; ?>" target="_blank">Excel</a>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body">
		<form method="post" action="" id="typeForm">
	        <div class="row ">
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label class="form-label d-block">Transaction Type</label>
	            	<select class="form-select js-example-basic-single" name="transaction_table"  >
	              		<option value="vendors" <?php echo ($transaction_table == 'vendors') ? 'selected' : ''; ?>>Vendors</option>
	              		<option value="vendor_registers" <?php echo ($transaction_table == 'vendor_registers') ? 'selected' : ''; ?>>
	                Vendor Registers</option>
	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label class="form-label d-block">Vendor Types</label>
	            	<select class="form-select js-example-basic-single" name="vendor_types_id"  >
	            		<option value="">All Vendor Types</option>
	              		<?php foreach($vendor_types as $vt){ ?>
	              		<option value="<?php echo $vt['id']; ?>" <?php echo ($_POST['vendor_types_id'] == $vt['id']) ? 'selected' : ''; ?>><?php echo $vt['type_name']; ?></option>
	              		<?php } ?>
	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label class="form-label d-block">Locations</label>
	            	<select class="form-select js-example-basic-single" name="location_id"  >
	            		<option value="">All Locations</option>
	            		<?php foreach($locations as $loc){ ?>
	              		<option value="<?php echo $loc['id']; ?>" <?php echo ($_POST['location_id'] == $loc['id']) ? 'selected' : ''; ?>><?php echo $loc['location_name']; ?></option>
	              		<?php } ?>
	              		
	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label class="form-label d-block">User Connect</label>
	            	<select class="form-select js-example-basic-single" name="vendor_connect_user"  >
	              		<option value="">All User Connect</option>
	            		<?php foreach($application_users as $au){ ?>
	              		<option value="<?php echo $au['id']; ?>" <?php echo ($_POST['vendor_connect_user'] == $au['id']) ? 'selected' : ''; ?>><?php echo $au['display_name']; ?></option>
	              		<?php } ?>
	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<button type="submit" class="btn btn-primary mt-4">Filter Apply</button>
	          	</div>
	        </div>
	    </form>

	</div>
</div>
 <?php include("custom_standard_top_print.php"); ?>

<div class="card">
    <div class="card-body">
      	<div class="table-responsive mt-3">
        	<table class="table table-hover">
          		<thead>
	          		<tr bgcolor="#eeeeee">
			            <th># </th>
			            <th>First Name</th>
						<th>Middle Name</th>
						<th>Last Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Mobile</th>
						<th>Company</th>
						<th>Location</th>
						<th>Business Size</th>
						<th>TIN Number</th>
						<th>VAR Number</th>
						<th>Display Name</th>
						<th>Billing Address</th>
						<th>Billing City</th>
						<th>Billing State</th>
						<th>Billing Post code</th>
						<th>Billing Country</th>
						<th>Shipping Address</th>
						<th>Shipping City</th>
						<th>Shipping State</th>
						<th>Shipping Post code</th>
						<th>Shipping Country</th>
						<th>Bank Account Name</th>
						<th>Bank Name</th>
						<th>Bank Account No</th>
						<th>IFSC Code</th>
						<th>Bank Address</th>
						<th>Website URL</th>
						<th>Vendor PO Notes</th>
						<th>Vendor Other Notes</th>
						<th>Pan No</th>
						<th>GST No</th>
						<th>HR Connect</th>
						<th>Finance Connect</th>
						<th>Purchase Connect</th>
						<th>Admin Connect</th>
						<th>Project Connect</th>
						<th>Location Connect</th>
						<th>Legal Connect</th>
						<th>Bank Country</th>
						<th>Bank State</th>
						<th>Bank City</th>
						<th>Bank Post Code</th>
						<th>Bank Beneficiary Name</th>
						<th>TAN Number</th>
						<th>MSME Number</th>
						<th>Reference Vendor Code</th>
						<th>Short Name</th>
						<th>BP Code</th>
	          		</tr>
	          	</thead>
	          	<tbody>
	          		<?php 
	          		$cssdata = array();
	          		if(count((array)$vendorlist) != 0){ 
	          			$seq=1;
	          			foreach($vendorlist as $venddetails){
	          				$cssdata[] = array('First Name'=>emptytextdashShow($venddetails['first_name']) ,'middle_name'=>emptytextdashShow($venddetails['middle_name']),'last_name'=>emptytextdashShow($venddetails['last_name']),'email'=>emptytextdashShow($venddetails['email']),'phone'=>emptytextdashShow($venddetails['phone']),'mobile'=>emptytextdashShow($venddetails['mobile']),'company'=>emptytextdashShow($venddetails['company']),'location_name'=>emptytextdashShow($locations[$venddetails['location_id']]['location_name']),'business_size'=>emptytextdashShow($venddetails['business_size']),'tin_number'=>emptytextdashShow($venddetails['tin_number']),'vat_number'=>emptytextdashShow($venddetails['vat_number']),'display_name'=>emptytextdashShow($venddetails['display_name']),'billing_address'=>emptytextdashShow($venddetails['billing_address']),'billing_city_name'=>emptytextdashShow($cities[$venddetails['billing_city']]['city_name']),'billing_state_name'=>emptytextdashShow($states[$venddetails['billing_state']]['state_name']),'billing_post_code'=>emptytextdashShow($venddetails['billing_post_code']),'billing_country_name'=>emptytextdashShow($countries[$venddetails['billing_country']]['country_name']),'shipping_address'=>emptytextdashShow($venddetails['shipping_address']),'shipping_city_name'=>emptytextdashShow($cities[$venddetails['shipping_city']]['city_name']),'shipping_state_name'=>emptytextdashShow($states[$venddetails['shipping_state']]['state_name']),'shipping_post_code'=>emptytextdashShow($venddetails['shipping_post_code']),'shipping_country_name'=>emptytextdashShow($countries[$venddetails['shipping_country']]['country_name']),'bank_account_name'=>emptytextdashShow($venddetails['bank_account_name']),'bank_name'=>emptytextdashShow($venddetails['bank_name']),'bank_account_no'=>emptytextdashShow($venddetails['bank_account_no']),'ifsc_code'=>emptytextdashShow($venddetails['ifsc_code']) ,'bank_address'=>emptytextdashShow($venddetails['bank_address']),'website_url'=>emptytextdashShow($venddetails['website_url']),'vendor_po_notes'=>emptytextdashShow($venddetails['vendor_po_notes']) ,'vendor_other_notes'=>emptytextdashShow($venddetails['vendor_other_notes']),'pan_no'=>emptytextdashShow($venddetails['pan_no']),'gstin'=>emptytextdashShow($venddetails['gstin']),'hr_connect'=>emptytextdashShow($application_users[$venddetails['hr_connect']]['display_name']),'finance_connect'=>emptytextdashShow($application_users[$venddetails['finance_connect']]['display_name']),'purchase_connect'=>emptytextdashShow($application_users[$venddetails['purchase_connect']]['display_name']),'admin_connect'=>emptytextdashShow($application_users[$venddetails['admin_connect']]['display_name']),'project_connect'=>emptytextdashShow($application_users[$venddetails['project_connect']]['display_name']),'location_connect'=>emptytextdashShow($application_users[$venddetails['location_connect']]['display_name']),'legal_connect'=>emptytextdashShow($application_users[$venddetails['legal_connect']]['display_name']),'bank_country_name'=>emptytextdashShow($countries[$venddetails['bank_country']]['country_name']),'bank_state'=>emptytextdashShow($states[$venddetails['bank_state']]['state_name']),'bank_city'=>emptytextdashShow($cities[$venddetails['bank_city']]['city_name']),'bank_post_code'=>emptytextdashShow($venddetails['bank_post_code']),'bank_beneficiary_name'=>emptytextdashShow($venddetails['bank_beneficiary_name']),'tan_number'=>emptytextdashShow($venddetails['tan_number']),'msme_number'=>emptytextdashShow($venddetails['msme_number']),'reference_vendor_code'=>emptytextdashShow($venddetails['reference_vendor_code']) ,'short_name'=>emptytextdashShow($venddetails['short_name']),'bp_code'=>emptytextdashShow($venddetails['bp_code']));

	        			?>
	          			<tr bgcolor="#eeeeee">
			            <td class="p-2"><?php echo $seq; ?> </td>
			            <td class="p-2"><?php echo emptytextdashShow($venddetails['first_name']); ?></td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['middle_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['last_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['email']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['phone']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['mobile']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['company']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($locations[$venddetails['location_id']]['location_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['business_size']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['tin_number']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['vat_number']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['display_name']); ?> </td>
						<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($venddetails['billing_address']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($cities[$venddetails['billing_city']]['city_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($states[$venddetails['billing_state']]['state_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['billing_post_code']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($countries[$venddetails['billing_country']]['country_name']); ?> </td>
						<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($venddetails['shipping_address']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($cities[$venddetails['shipping_city']]['city_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($states[$venddetails['shipping_state']]['state_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['shipping_post_code']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($countries[$venddetails['shipping_country']]['country_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['bank_account_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['bank_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['bank_account_no']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['ifsc_code']); ?> </td>
						<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($venddetails['bank_address']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['website_url']); ?> </td>
						<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($venddetails['vendor_po_notes']); ?> </td>
						<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($venddetails['vendor_other_notes']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['pan_no']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['gstin']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($application_users[$venddetails['hr_connect']]['display_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($application_users[$venddetails['finance_connect']]['display_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($application_users[$venddetails['purchase_connect']]['display_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($application_users[$venddetails['admin_connect']]['display_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($application_users[$venddetails['project_connect']]['display_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($application_users[$venddetails['location_connect']]['display_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($application_users[$venddetails['legal_connect']]['display_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($countries[$venddetails['bank_country']]['country_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($states[$venddetails['bank_state']]['state_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($cities[$venddetails['bank_city']]['city_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['bank_post_code']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['bank_beneficiary_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['tan_number']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['msme_number']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['reference_vendor_code']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['short_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['bp_code']); ?> </td>
	          		</tr>	
	          		<?php $seq++; } $_SESSION['download_data'] = $cssdata; }else{ ?>
	  				<tr>
			            <td colspan="50">
			              	<div class="card-px text-center py-20 m-t-30">
			                	<p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>   
			                	<div class="text-center px-4">
			                  		<img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
			                	</div>   
			              	</div>
			            </td>
			        </tr>
		  			<?php } ?>
	          		
	          	</tbody>
      		</table>
  		</div>
  		<?php //echo "<pre>"; print_r($cssdata); echo "</pre>"; ?>
	</div>
</div>