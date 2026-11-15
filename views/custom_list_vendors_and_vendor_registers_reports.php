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
	            	<label for="transaction_table" class="form-label d-block">Transaction Type</label>
	            	<select id="transaction_table" class="form-select js-example-basic-single" name="transaction_table"  >
	              		<option value="vendors" <?php echo ($transaction_table == 'vendors') ? 'selected' : ''; ?>>Vendors</option>
	              		<option value="vendor_registers" <?php echo ($transaction_table == 'vendor_registers') ? 'selected' : ''; ?>>
	                Vendor Registers</option>
	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label for="vendor_types_id" class="form-label d-block">Vendor Types</label>
	            	<select id="vendor_types_id" class="form-select js-example-basic-single" name="vendor_types_id"  >
	            		<option value="">All Vendor Types</option>
	              		<?php foreach($vendor_types as $vt){ ?>
	              		<option value="<?php echo $vt['id']; ?>" <?php echo ($_POST['vendor_types_id'] == $vt['id']) ? 'selected' : ''; ?>><?php echo $vt['type_name']; ?></option>
	              		<?php } ?>
	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label for="location_id" class="form-label d-block">Locations</label>
	            	<select id="location_id" class="form-select js-example-basic-single" name="location_id"  >
	            		<option value="">All Locations</option>
	            		<?php foreach($locations as $loc){ ?>
	              		<option value="<?php echo $loc['id']; ?>" <?php echo ($_POST['location_id'] == $loc['id']) ? 'selected' : ''; ?>><?php echo $loc['location_name']; ?></option>
	              		<?php } ?>

	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label for="vendor_connect_user" class="form-label d-block">User Connect</label>
	            	<select id="vendor_connect_user" class="form-select js-example-basic-single" name="vendor_connect_user"  >
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
 <?php include_once "custom_standard_top_print.php"; ?>

<div class="card">
    <div class="card-body">
      	<div class="table-responsive mt-3">
        	<table class="table table-hover">
          		<thead>
	          		<tr bgcolor="#eeeeee">
			            <th scope="col"># </th>
			            <th scope="col">First Name</th>
						<th scope="col">Middle Name</th>
						<th scope="col">Last Name</th>
						<th scope="col">Email</th>
						<th scope="col">Phone</th>
						<th scope="col">Mobile</th>
						<th scope="col">Company</th>
						<th scope="col">Location</th>
						<th scope="col">Business Size</th>
						<th scope="col">TIN Number</th>
						<th scope="col">VAR Number</th>
						<th scope="col">Display Name</th>
						<th scope="col">Billing Address</th>
						<th scope="col">Billing City</th>
						<th scope="col">Billing State</th>
						<th scope="col">Billing Post code</th>
						<th scope="col">Billing Country</th>
						<th scope="col">Shipping Address</th>
						<th scope="col">Shipping City</th>
						<th scope="col">Shipping State</th>
						<th scope="col">Shipping Post code</th>
						<th scope="col">Shipping Country</th>
						<th scope="col">Bank Account Name</th>
						<th scope="col">Bank Name</th>
						<th scope="col">Bank Account No</th>
						<th scope="col">IFSC Code</th>
						<th scope="col">Bank Address</th>
						<th scope="col">Website URL</th>
						<th scope="col">Vendor PO Notes</th>
						<th scope="col">Vendor Other Notes</th>
						<th scope="col">Pan No</th>
						<th scope="col">GST No</th>
						<th scope="col">HR Connect</th>
						<th scope="col">Finance Connect</th>
						<th scope="col">Purchase Connect</th>
						<th scope="col">Admin Connect</th>
						<th scope="col">Project Connect</th>
						<th scope="col">Location Connect</th>
						<th scope="col">Legal Connect</th>
						<th scope="col">Bank Country</th>
						<th scope="col">Bank State</th>
						<th scope="col">Bank City</th>
						<th scope="col">Bank Post Code</th>
						<th scope="col">Bank Beneficiary Name</th>
						<th scope="col">TAN Number</th>
						<th scope="col">MSME Number</th>
						<th scope="col">Reference Vendor Code</th>
						<th scope="col">Short Name</th>
						<th scope="col">BP Code</th>
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
	          		<?php $seq++;
	          		} $_SESSION['download_data'] = $cssdata;
	          		}else{ ?>
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
  		<?php ?>
	</div>
</div>