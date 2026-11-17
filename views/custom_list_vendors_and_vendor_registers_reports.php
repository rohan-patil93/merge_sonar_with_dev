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
		<h5 class="f-30"><?= htmlspecialchars(__('Vender & Vendor Register Report'), ENT_QUOTES, 'UTF-8'); ?></h5>
		<div class="card-header-right">
			<div class="dropdown-primary dropdown open">
				<button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= __('More'); ?></button>
				<div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
					<a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=<?php echo $transaction_table; ?>" target="_blank" ><?= __('CSV'); ?></a>
					<a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=<?php echo $transaction_table; ?>" target="_blank"><?= __('Excel'); ?></a>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body">
		<form method="post" action="" id="typeForm">
	        <div class="row ">
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label for="transaction_table" class="form-label d-block"><?= __('Transaction Type'); ?></label>
	            	<select id="transaction_table" class="form-select js-example-basic-single" name="transaction_table"  >
	              		<option value="vendors" <?php echo ($transaction_table == 'vendors') ? 'selected' : ''; ?>><?= __('Vendors'); ?></option>
	              		<option value="vendor_registers" <?php echo ($transaction_table == 'vendor_registers') ? 'selected' : ''; ?>>
	                <?= __('Vendor Registers'); ?></option>
	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label for="vendor_types_id" class="form-label d-block"><?= __('Vendor Types'); ?></label>
	            	<select id="vendor_types_id" class="form-select js-example-basic-single" name="vendor_types_id"  >
	            		<option value=""><?= __('All Vendor Types'); ?></option>
	              		<?php foreach($vendor_types as $vt){ ?>
	              		<option value="<?php echo $vt['id']; ?>" <?php echo ($_POST['vendor_types_id'] == $vt['id']) ? 'selected' : ''; ?>><?php echo $vt['type_name']; ?></option>
	              		<?php } ?>
	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label for="location_id" class="form-label d-block"><?= __('Locations'); ?></label>
	            	<select id="location_id" class="form-select js-example-basic-single" name="location_id"  >
	            		<option value=""><?= __('All Locations'); ?></option>
	            		<?php foreach($locations as $loc){ ?>
	              		<option value="<?php echo $loc['id']; ?>" <?php echo ($_POST['location_id'] == $loc['id']) ? 'selected' : ''; ?>><?php echo $loc['location_name']; ?></option>
	              		<?php } ?>

	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<label for="vendor_connect_user" class="form-label d-block"><?= __('User Connect'); ?></label>
	            	<select id="vendor_connect_user" class="form-select js-example-basic-single" name="vendor_connect_user"  >
	              		<option value=""><?= __('All User Connect'); ?></option>
	            		<?php foreach($application_users as $au){ ?>
	              		<option value="<?php echo $au['id']; ?>" <?php echo ($_POST['vendor_connect_user'] == $au['id']) ? 'selected' : ''; ?>><?php echo $au['display_name']; ?></option>
	              		<?php } ?>
	            	</select>
	          	</div>
	          	<div class="col-lg-2 mb-2 w-full">
	            	<button type="submit" class="btn btn-primary mt-4"><?= __('Filter Apply'); ?></button>
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
			            <th scope="col"><?= __('First Name'); ?></th>
						<th scope="col"><?= __('Middle Name'); ?></th>
						<th scope="col"><?= __('Last Name'); ?></th>
						<th scope="col"><?= __('Email'); ?></th>
						<th scope="col"><?= __('Phone'); ?></th>
						<th scope="col"><?= __('Mobile'); ?></th>
						<th scope="col"><?= __('Company'); ?></th>
						<th scope="col"><?= __('Location'); ?></th>
						<th scope="col"><?= __('Business Size'); ?></th>
						<th scope="col"><?= __('TIN Number'); ?></th>
						<th scope="col"><?= __('VAR Number'); ?></th>
						<th scope="col"><?= __('Display Name'); ?></th>
						<th scope="col"><?= __('Billing Address'); ?></th>
						<th scope="col"><?= __('Billing City'); ?></th>
						<th scope="col"><?= __('Billing State'); ?></th>
						<th scope="col"><?= __('Billing Post code'); ?></th>
						<th scope="col"><?= __('Billing Country'); ?></th>
						<th scope="col"><?= __('Shipping Address'); ?></th>
						<th scope="col"><?= __('Shipping City'); ?></th>
						<th scope="col"><?= __('Shipping State'); ?></th>
						<th scope="col"><?= __('Shipping Post code'); ?></th>
						<th scope="col"><?= __('Shipping Country'); ?></th>
						<th scope="col"><?= __('Bank Account Name'); ?></th>
						<th scope="col"><?= __('Bank Name'); ?></th>
						<th scope="col"><?= __('Bank Account No'); ?></th>
						<th scope="col"><?= __('IFSC Code'); ?></th>
						<th scope="col"><?= __('Bank Address'); ?></th>
						<th scope="col"><?= __('Website URL'); ?></th>
						<th scope="col"><?= __('Vendor PO Notes'); ?></th>
						<th scope="col"><?= __('Vendor Other Notes'); ?></th>
						<th scope="col"><?= __('Pan No'); ?></th>
						<th scope="col"><?= __('GST No'); ?></th>
						<th scope="col"><?= __('HR Connect'); ?></th>
						<th scope="col"><?= __('Finance Connect'); ?></th>
						<th scope="col"><?= __('Purchase Connect'); ?></th>
						<th scope="col"><?= __('Admin Connect'); ?></th>
						<th scope="col"><?= __('Project Connect'); ?></th>
						<th scope="col"><?= __('Location Connect'); ?></th>
						<th scope="col"><?= __('Legal Connect'); ?></th>
						<th scope="col"><?= __('Bank Country'); ?></th>
						<th scope="col"><?= __('Bank State'); ?></th>
						<th scope="col"><?= __('Bank City'); ?></th>
						<th scope="col"><?= __('Bank Post Code'); ?></th>
						<th scope="col"><?= __('Bank Beneficiary Name'); ?></th>
						<th scope="col"><?= __('TAN Number'); ?></th>
						<th scope="col"><?= __('MSME Number'); ?></th>
						<th scope="col"><?= __('Reference Vendor Code'); ?></th>
						<th scope="col"><?= __('Short Name'); ?></th>
						<th scope="col"><?= __('BP Code'); ?></th>
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
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['phone'], 'mobile'); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['mobile'], 'mobile'); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['company']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($locations[$venddetails['location_id']]['location_name']); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['business_size']); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['tin_number'], 'gst'); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['vat_number'], 'gst'); ?> </td>
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
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['bank_account_name'], 'text'); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['bank_name'], 'text'); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['bank_account_no'], 'bank_account'); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['ifsc_code'], 'ifsc'); ?> </td>
						<td class="p-2 w-b-w-s"><?php echo renderSensitiveDataWithToggle($venddetails['bank_address'], 'text'); ?> </td>
						<td class="p-2"><?php echo emptytextdashShow($venddetails['website_url']); ?> </td>
						<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($venddetails['vendor_po_notes']); ?> </td>
						<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($venddetails['vendor_other_notes']); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['pan_no'], 'pan'); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['gstin'], 'gst'); ?> </td>
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
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['bank_post_code'], 'text'); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['bank_beneficiary_name'], 'text'); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['tan_number'], 'gst'); ?> </td>
						<td class="p-2"><?php echo renderSensitiveDataWithToggle($venddetails['msme_number'], 'gst'); ?> </td>
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
			                	<p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> <?= __('No Data Available'); ?></p>
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