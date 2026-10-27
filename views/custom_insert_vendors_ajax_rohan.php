<?php
if($_POST['ajaxcall'] =="Y"){
	include('../includes/session.php');
	include('../dbconfig.php');
	include('../comm.php');
	$vendor_id = $_POST['vendor_id'];
	$transaction_table = $_POST['transaction_table'];
	$url = API."?mode=getvendorsdetails&transaction_table=".$transaction_table."&id=".$vendor_id."&admin_type=".$_SESSION['admin_type']."&edittype=".$_POST['edittype'];
	$response = callAPI($url);//file_get_contents($url, false, $context);
	$result = json_decode($response,1);
	//echo "<pre>"; print_r($result); echo "</pre>";
	$role = $result['vendors'];
	$vendor_registers = $result['vendor_registers'];
	$vendordetail = $vendor_registers;
	if($_POST['vtype'] !="VR"){
		$vendordetail = $result['vendors'];
	}else{
		$vendordetail = $result['vendor_registers'];
	}
	$vendor_id = $vendordetail['id'];
	$aggrements =$result['vendor_agreements'];
	$payment_method =$result['payment_method'];
	$salestype =$result['salestype'];
	$emplist = $result['employess'];
	$countrieslist = $result['countries'];
	$states = $result['states'];
	$cities = $result['cities'];
	foreach($result['vendorslist'] as $vend){
		$vendorslist[$vend['id']] = $vend;	
	}
	$paymenttype = $result['paymenttype'];
	$vendor_type = $result['vendor_type'];
	$countrylist =$stateslist = $citieslist =  array();
	foreach($countrieslist as $country_list){
		$countrylist[$country_list['id']] = $country_list['country_name'];
	}
	foreach($states as $states_list){
		$stateslist[$states_list['id']] = $states_list['state_name'];
	}
	foreach($cities as $cities_list){
		$citieslist[$cities_list['id']] = $cities_list['city_name'];
	}
	$vendor_contact_info_type = $result['vendor_contact_info_type'];
	//$company_types = $result['company_types'];
	//$incoterms  = $result['incoterms'];
	//$payment_terms = $result['payment_terms'];
	$bank_acoount_type = $result['bank_acoount_type'];
	$other_contact_information = json_decode($vendordetail['other_contact_information'],1);
	$vendor_questions = json_decode($vendordetail['vendor_questions'],1);
	//echo "<pre>"; print_r($vendor_questions); echo "</pre>";
	$company_types = array();
	foreach($result['company_types'] as $ctype) { 
		$company_types[$ctype['id']] = $ctype;
	}

	$payment_terms = array();
	foreach($result['payment_terms'] as $payt) { 
		$payment_terms[$payt['id']] = $payt; //payment_term_name
	}
	$incoterms = array();
	foreach($result['incoterms'] as $payt) { 
		$incoterms[$payt['id']] = $payt; //incoterm_name
	}
	$transaction_activity_schedule = $result['transaction_activity_schedule'];
	$vendor_verifications = json_decode($vendordetail['vendor_verifications'],1);
	$vendor_display_fields = $result['vendor_display_fields'];
	$vafield = json_decode($vendor_display_fields['field_list'],1);

	$item_categories = $result['item_categories'];
	$items = $result['items'];
} 
function answerYesOrNo($answ){
	$return = "<span class='text-danger'>Pending<span>";
	if($answ == "Y"){
		$return = "Yes";
	}elseif($answ == "N"){
		$return = "No";
	}
	return $return;
}
$profilereadiness = array("Business Information"=>array('company','website_url','company_trading_name','company_subsidiary_division','first_name','middle_name','last_name','phone','email','phone','billing_address','billing_country','billing_state','billing_city','billing_post_code','contact_person','mobile','shipping_address','shipping_country','shipping_state','shipping_city','shipping_post_code'),"Company Information"=>array('vendor_tax_reg_no','business_type','duns_no','company_registration_number','company_registration_country','payment_terms','gstin','pan_no','tan_number','incoterm_id','image','msme_number'),"Banking Information"=>array('bank_name','bank_address','bank_country','bank_state','bank_city','bank_post_code','bank_beneficiary_name','bank_account_no','account_type','routing_aba_dfi_ach','routing_wire_Code','swift_bic_code','iban_code','bank_query_person_name','bank_query_contact_no','bank_query_email_id','ifsc_code'),"Note"=>array('vendor_po_notes','vendor_other_notes'));
?>
<script type="text/javascript">
function checkVendorValidationEmpty(){
	// var checkval_1 = 1;
	// var checkval_2 = 1;
	// if($("#termsandcondition_1").is(":checked")) {
	// 	checkval_1 = 0;
	// }
	// if($("#termsandcondition_2").is(":checked")) {
	// 	checkval_2 = 0;
	// }
	// if(checkval_1 == 0 && checkval_2 == 0){
	// 	$( "#vendor-questions-id" ).removeClass("bg-danger");
	// 	$(".terms_conditions").hide();
	// 	$("#submitvendordetailform").submit();
	// }else{
	// 	$( "#vendor-questions-id" ).addClass("bg-danger");
	// 	$(".terms_conditions").show();
	// }
	$("#submitvendordetailform").submit();
}
function getmSMENumber(){
	if($("#msme_register_yes_no").is(":checked")) {
		$("#msme_number_id").show('slow');
	}else{
		$("#msme_number_id").hide('slow');
	}
}


</script>
<?php if($_POST['edittype'] == 'SendVerificationProfile'){ $notsendtoapprove = 0; ?>
<div class="modal-header">
	<h5 class="modal-title">Vendor details</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
				    <h5>Profile Completion</h5>
				    <div class="card-header-right">

			        </div>
				</div>
				<div class="card-body">
	  				<table class="table table-hover table-md">
                  		<tbody>
	                    <?php foreach($profilereadiness as $keylabel=>$profilereadines){
	                      $complete_field =  $total_field = 0; 
	                      $total_field = count((array)$profilereadines);
	                      foreach($profilereadines as $profilereadines){
	                        if($vendordetail[$profilereadines] != ""){
	                          $complete_field++; 
	                        }
	                      }
	                      $percentage = (($complete_field/$total_field)*100);
	                      ?>
	                      	<tr>
		                        <td>
		                          <h6 class="mb-0 f-14"><?php echo $keylabel; ?></h6>
		                        </td>
		                        <td align="right">
		                          <div class="d-flex align-items-center w-100px w-sm-120px flex-column ">
		                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
		                              <span class="f-12 f-w-600">Completion </span>
		                              <span class="f-12 f-w-600"><?php echo round($percentage); ?>%</span>
		                            </div>
		                            <div class="col-md-12">
		                              <div class="progress progress-xs">
		                                <div class="progress-bar progress-bar-<?php echo round($percentage)>=50?"success":"danger"; ?>" role="progressbar" style="width:  <?php echo round($percentage); ?>%;" aria-valuenow=" 55" aria-valuemin="0" aria-valuemax="100"></div>
		                              </div>
		                            </div>
		                          </div>
		                        </td>
	                      	</tr>
                    	<?php } ?>
                  		</tbody>
              		</table>
				</div>
			</div>
		</div>
		<div class="col-lg-8">
			<?php

				$transactionactivityschedule = $pendingtask =  array();
				foreach($transaction_activity_schedule as $tas){
					if($tas['current_status'] == ST_ACTIVE){
						$notsendtoapprove = 1;
						$transactionactivityschedule[ST_ACTIVE][] = $tas;
						$pendingtask[] = $tas;
					}else{
						$check_list = json_decode($tas['check_list'], 1);
						$alldone = $requerdcnt =$mandatory = 0; 
						foreach($check_list as $check_lits){
							if($check_lits['checklist_completed'] =='Y'){
								$alldone++; // solve count
							}
							if($check_lits['mandatory_checklist'] =='Y'){
								$requerdcnt++; // mandatory count
							}
							if($check_lits['mandatory_checklist']=='Y' AND $check_lits['checklist_completed'] !='Y'){
								$mandatory++; // all mandatory solve requerd
							}
						}
						if($requerdcnt <= $alldone AND $mandatory == '0'){ 
							$transactionactivityschedule['SEND_FOR_APPROVE'][] = $tas;
						}else{
							$transactionactivityschedule['PENDDING_TASK'][] = $tas;
							$pendingtask[] = $tas;
							$notsendtoapprove = 1;
						}
					}
				}


				///echo "notapprive -- ".$notsendtoapprove;
				//echo "<pre>";  print_r($transactionactivityschedule); echo "</pre>";
			 ?>
			 <div class="row">
				<div class="col-3 b-l-default text-center">
		            <div class="s-caption  text-primary"><i class="ti-list f-20"></i></div>
		            <div class="s-cont d-inline-block">
		                <h5 class="f-w-700 m-b-0"><?php  echo count((array)$transaction_activity_schedule); ?></h5>
		                <p class="m-b-0 f-12">Total Task</p>
		            </div>
		        </div>
		        <div class="col-3 b-l-default text-center">
		            <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
		            <div class="s-cont d-inline-block">
		                <h5 class="f-w-700 m-b-0"><?php echo count((array)$transactionactivityschedule[ST_ACTIVE]); ?></h5>
		                <p class="m-b-0 f-12">Yet To Start</p>
		            </div>
		        </div>
		        <div class="col-3 b-l-default text-center">
		            <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
		            <div class="s-cont d-inline-block">
		                <h5 class="f-w-700 m-b-0"><?php echo count((array)$transactionactivityschedule['PENDDING_TASK']); ?></h5>
		                <p class="m-b-0  f-12">In Progress</p>
		            </div>
		        </div>
		        <div class="col-3 b-l-default text-center">
		            <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
		            <div class="s-cont d-inline-block">
		                <h5 class="f-w-700 m-b-0"><?php echo count((array)$transactionactivityschedule['SEND_FOR_APPROVE']); ?></h5>
		                <p class="m-b-0 f-12">Ready For Review</p>
		            </div>
		        </div>
			</div>
			<div class="">
				<div class="card">
					<div class="card-header">
					    <h5>Pending Tasks</h5>
					    <div class="card-header-right">

				        </div>
					</div>
					<div class="card-body">
						<?php 
						if($vendor_questions['terms_conditions']['1'] !="Y" || $vendor_questions['terms_conditions']['2'] !="Y"){
							$notsendtoapprove = 1;
							$pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Accept Terms And Conditions In Questions Tab');
						}
						if($vendordetail['pan_no'] ==""){
							$notsendtoapprove = 1;
							$pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide PAN No.');
						}
						if($vendordetail['gstin'] ==""){
							$notsendtoapprove = 1;
							$pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide GSTIN No.');
						}
						if($vendordetail['bank_account_no'] ==""){
							$notsendtoapprove = 1;
							$pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide Bank Account No.');
						}
						if($vendordetail['ifsc_code'] ==""){
							$notsendtoapprove = 1;
							$pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide IFSC Code');
						}
						if($vendordetail['msme_register_yes_no'] =="Y" AND $vendordetail['msme_number'] ==""){
							$notsendtoapprove = 1;
							$pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide MSME Number');
						}
				?>
						<?php if(count((array)$pendingtask) !=0){ ?>
						<table class="table table-hover m-b-0 " >
							<?php foreach($pendingtask as $pendingtask){ ?>
							<tr >
								<td class="p-2"><?php echo $pendingtask['task_name']; ?></td>
							</tr>
							<?php } ?>
						</table>
						<?php }else{ ?>
						<div class="card-px text-center py-20 mt-2">
		                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Pending Tasks </p>   
		                    <div class="text-center px-4">
		                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
		                    </div>   
		                </div>
		            	<?php } ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
	<?php if($notsendtoapprove == 0){ ?>
	<button type="button" class="btn btn-primary waves-effect waves-light" id="popsubmitforreview_btn" onclick="UpdateSendVerificationProfileStatus()">Submit For Review</button>
<?php }else{ echo "<span class='text-danger'><i class='ti-alert'></i>".$_SESSION['error_message']['9126']."</span>"; } ?>
</div>

<?php }elseif($_POST['edittype'] == 'EDIT'){ ?>
<div class="card">
	<form method="post" id="submitvendordetailform" enctype="multipart/form-data">
		<div class="text-right">
			<input type="hidden" name="vendor_user_id" value="<?php echo $vendordetail['user_id']; ?>" />
			<input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>" />
			<input type="hidden" name="submit_vendor" value="Update" />
			<input type="hidden" name="transaction_table" value="<?php echo $transaction_table; ?>" />
			<button type="button" class="btn btn-primary m-r-20 m-b-20" onclick="checkVendorValidationEmpty()"><i class="ti-check"></i>
			Update</button></br>
			<span class="text-danger terms_conditions" style="display: none">* Please Accept Terms And Conditions In Questions Tab</span>
			<input type="hidden" name="hid" value=<?php echo $vendor_id; ?> /> 
		</div>
		<ul class="nav md-tabs infisuite_tab mt-2 ml-2" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" data-bs-toggle="tab" href="#basic-info" role="tab">Business Information</a>
				<div class="slide"></div>
			</li>
			<?php 	
				$fieldaccesright = $vafield['company_information']; 
          		if($fieldaccesright['access'] !="NA"){ ?>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#company-information" role="tab">Company Info</a>
				<div class="slide"></div>
			</li>
			<?php } $fieldaccesright = $vafield['bank_information']; 
          		if($fieldaccesright['access'] !="NA"){ ?>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#company-bank-info" role="tab">Banking Info</a>
				<div class="slide"></div>
			</li>
			<?php } $fieldaccesright = $vafield['other_contact_information']; 
          		if($fieldaccesright['access'] !="NA"){ ?>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#other-contact-info" role="tab">Other Contact Info</a>
				<div class="slide"></div>
			</li>
			
			<!-- <li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#contact-details" role="tab">Contact Details</a>
				<div class="slide"></div>
			</li> -->
			<?php } $fieldaccesright = $vafield['note']; 
          		if($fieldaccesright['access'] !="NA"){ ?>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#note" role="tab">Note</a>
				<div class="slide"></div>
			</li>
		<?php } ?>
			<!-- <li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#tax-info" role="tab">Tax Info</a>
				<div class="slide"></div>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#payment-billing" role="tab">Payment and Billing</a>
				<div class="slide"></div>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#sale-discount" role="tab">Sale / Discount</a>
				<div class="slide"></div>
			</li> -->
			<?php if($transaction_table == "vendors"){ ?>
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#agreements" role="tab">Agreements</a>
					<div class="slide"></div>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#vendor_items" role="tab" onclick="getVendorCustomerItems()">Items</a>
					<div class="slide"></div>
				</li>
				
				<!-- <li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#attachments" role="tab">Attachments</a>
					<div class="slide"></div>
				</li> -->
				<!-- <li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#employees" role="tab">Employees</a>
					<div class="slide"></div>
				</li> -->
				<!-- <li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#no-hiring" role="tab">No hiring clause</a>
					<div class="slide"></div>
				</li> -->
			<?php } ?>
			<?php  $fieldaccesright = $vafield['other_information']; 
          		if($fieldaccesright['access'] !="NA"){ ?>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#vendor-other-info" role="tab">Other Info</a>
				<div class="slide"></div>
			</li>
			<?php } $fieldaccesright = $vafield['questions']; 
          		if($fieldaccesright['access'] !="NA"){ ?>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" id="vendor-questions-id" href="#vendor-questions" role="tab">Questions</a>
				<div class="slide"></div>
			</li>
			<?php } ?>

		</ul>
	<div class="tab-content card-block">
		<div class="tab-pane" id="vendor_items" role="tabpanel">

		</div>
		<div class="tab-pane active" id="basic-info" role="tabpanel">
			<div class="row">
				<?php 
					$fieldaccesright = $vafield['business_information'];
					if($fieldaccesright['field']['company']!="NA"){  
                ?>
				<div class="form-group  col-lg-6">
					<label class="form-label">Company Name</label>
					<input class="form-control" type="text" id="company" name="company" value="<?php echo $vendordetail['company']; ?>" autocomplete="off" readonly>	
				</div>
				<?php 
                   }if($fieldaccesright['field']['website_url']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['website_url'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-6 form-group ">
					<label class="form-label">Corporate Website</label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="website_url" name="website_url" value="<?php echo $vendordetail['website_url']; ?>" autocomplete="off" >	
				</div>
				<?php 
                   }if($fieldaccesright['field']['company_trading_name']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['company_trading_name'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-6 form-group ">
					<label class="form-label">DBA(Trading Name)</label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="company_trading_name" name="company_trading_name" value="<?php echo $vendordetail['company_trading_name']; ?>" autocomplete="off">	
				</div>
				<?php 
                   }if($fieldaccesright['field']['company_subsidiary_division']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['company_subsidiary_division'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-6 form-group ">
					<label class="form-label">Subsidiary / Division Of</label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="company_subsidiary_division" name="company_subsidiary_division" value="<?php echo $vendordetail['company_subsidiary_division']; ?>" autocomplete="off">
				</div>
				<?php 
                   }if($fieldaccesright['field']['first_name']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['first_name'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-2 " >
					<label class="form-label">First Name</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="first_name" name="first_name" value="<?php echo $vendordetail['first_name']; ?>" autocomplete="off">
					</div>
					<div style="display: none;color: red;" id="first_name_err"> Please enter first_name </div> 
				</div>
				<?php 
                   }if($fieldaccesright['field']['middle_name']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['middle_name'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-2" >
					<label class="form-label">Middle Name</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="middle_name" name="middle_name" value="<?php echo $vendordetail['middle_name']; ?>" autocomplete="off">
					</div>
					<div style="display: none;color: red;" id="middle_name_err"> Please enter middle name </div> 
				</div>
				<?php 
                   }if($fieldaccesright['field']['last_name']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['last_name'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-2">
					<label class="form-label">Last Name</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="last_name" name="last_name" value="<?php echo $vendordetail['last_name']; ?>" autocomplete="off">
					</div>
					<div style="display: none;color: red;" id="last_name_err"> Please enter last name </div> 
				</div>
				<?php 
                   }if($fieldaccesright['field']['email']!="NA"){ 
                ?>
				<div class="form-group col-lg-3">
					<label class="form-label">Email</label>
					<div class="kt-input-icon">
						<input class="form-control" id="email" name="email" type="text" value="<?php echo $vendordetail['email']; ?>" autocomplete="off" readonly>
					</div>
					<div style="display: none;color: red;" id="email_err"> Please enter email </div> 
				</div>
				<?php 
                   }if($fieldaccesright['field']['phone']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['phone'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-3">
					<label class="form-label">Phone</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="number" id="phone" name="phone" value="<?php echo $vendordetail['phone']; ?>" autocomplete="off">	
					</div>
					<div style="display: none;color: red;" id="phone_err"> Please enter phone </div>  
				</div>
			<?php } ?>
			</div>
			<div class="row ">
				<div class="col-lg-6" >
					<div class="row ">
						<?php 
		                   	if($fieldaccesright['field']['billing_address']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['billing_address'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-12">
							<label class="form-label">Billing Adderss</label>
							<textarea type="text" class="form-control <?php echo $accessreadonly; ?>" placeholder="Billing Adderss" name="billing_address" autocomplete="off"><?php echo $vendordetail['billing_address']; ?></textarea>
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['billing_country']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['billing_country'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6 w-full <?php echo $accessreadonly; ?>" >
							<label class="form-label">Country</label>
							<select id="kt_select2_102" class="form-select js-example-basic-single " name="billing_country"  onchange="selectedcategory(this.value,'kt_select2_101','states','country_id','selectedbillcountry')">
								<option value="">Select Any One</option>
								<?php foreach($countrieslist as $countryvalue) { ?>
									<option <?php echo $vendordetail['billing_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['billing_state']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['billing_state'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6  w-full <?php echo $accessreadonly; ?>" >
							<label class="form-label">State</label>
							<select id="kt_select2_101" name="billing_state" class="form-select js-example-basic-single " onchange="selectedcategory(this.value,'kt_select2_103','cities','state_id','selectedbillstate')">
								<option value="">Select Any One</option>
								<?php foreach($states as $states1) { ?>
									<option <?php echo $vendordetail['billing_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['billing_city']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['billing_city'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6 p-r-5  w-full <?php echo $accessreadonly; ?>" >
							<label class="form-label">City/Town</label>
							<select id="kt_select2_103" name="billing_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
								<option value="">Select Any One</option>
								<?php foreach($cities as $cities1) { ?>
									<option <?php echo $vendordetail['billing_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['billing_post_code']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['billing_post_code'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6 p-r-5" >
							<label  class="form-label">Pincode</label>
							<input class="form-control <?php echo $accessreadonly; ?>" placeholder="Pincode" type="number" id="billing_post_code" name="billing_post_code" value="<?php echo $vendordetail['billing_post_code']; ?>" autocomplete="off">
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['contact_person']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['contact_person'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6 p-r-5" style="float: right;">
							<label class="form-label">Contact Person</label>
							<input class="form-control <?php echo $accessreadonly; ?>" placeholder="Contact Person" type="text" id="" name="contact_person" value="<?php echo $vendordetail['contact_person']; ?>" autocomplete="off" >
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['mobile']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['mobile'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6 p-r-5" style="float: left;">
							<label class="form-label">Mobile</label>
							<input class="form-control <?php echo $accessreadonly; ?>" type="number" id="mobile" name="mobile" value="<?php echo $vendordetail['mobile']; ?>" autocomplete="off">	
						</div>
						<?php } ?>
					</div>
				</div>	
				<div class="col-lg-6 ">
					<div class="row">
						<?php 
		                   	if($fieldaccesright['field']['shipping_address']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['shipping_address'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-12" >
							<label class="form-label">Shipping Adderss</label>
							<div class="kt-input-icon">
								<textarea type="text" class="form-control <?php echo $accessreadonly; ?>" placeholder="Street" name="shipping_address" autocomplete="off"><?php echo $vendordetail['shipping_address']; ?></textarea>
							</div> 
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['shipping_country']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['shipping_country'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6  w-full <?php echo $accessreadonly; ?>" >
							<label class="form-label">Country</label>
							<select id="kt_select2_104" class="form-select js-example-basic-single "  name="shipping_country" onchange="selectedcategory(this.value,'kt_select2_105','states','country_id','selectedshipcountry')">
								<option value="">Select Any One</option>
								<?php foreach($countrieslist as $countryvalue) { ?>
									<option <?php echo $vendordetail['shipping_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id'];?>"><?php echo $countryvalue['country_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['shipping_state']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['shipping_state'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6  w-full <?php echo $accessreadonly; ?>">
							<label class="form-label">State</label>
							<select id="kt_select2_105" name="shipping_state" class="form-select js-example-basic-single " onchange="selectedcategory(this.value,'kt_select2_106','cities','state_id','selectedshipstate')">
								<option value="">Select Any One</option>
								<?php foreach($states as $states1) { ?>
									<option <?php echo $vendordetail['shipping_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
								<?php } ?>

							</select>
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['shipping_city']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['shipping_city'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6 p-r-5 w-full <?php echo $accessreadonly; ?>">
							<label class="form-label">City/Town</label>
							<select id="kt_select2_106" name="shipping_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedshipcity')">
								<option value="">Select Any One</option>
								<?php foreach($cities as $cities1) { ?>
									<option <?php echo $vendordetail['shipping_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<?php 
		                   	}if($fieldaccesright['field']['website_url']!="NA"){ 
		                       $accessreadonly = $fieldaccesright['field']['website_url'] !="E"?"custom_readonly":"";    
		                ?>
						<div class="form-group col-lg-6" >
							<label class="form-label">Pincode</label>
							<input class="form-control <?php echo $accessreadonly; ?>" placeholder="Pincode" type="number" id="shipping_post_code" name="shipping_post_code" value="<?php echo $vendordetail['shipping_post_code']; ?>" autocomplete="off">

						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="company-information" role="tabpanel">
			<div class="row ">
				<?php 
					$fieldaccesright = $vafield['company_information'];
					if($fieldaccesright['field']['vendor_tax_reg_no']!="NA"){ 
						$accessreadonly = $fieldaccesright['field']['vendor_tax_reg_no'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4" style="margin-bottom: 2rem;">
					<label class="form-label">Tax/Vat Reg.No.</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="vendor_tax_reg_no" name="vendor_tax_reg_no" value="<?php echo $vendordetail['vendor_tax_reg_no']; ?>" autocomplete="off">
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['business_type']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['business_type'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>" >
					<label class="form-label">Legal Structure</label>
					<select class="form-select js-example-basic-single" name="business_type" >
						<option value="">Select Any One</option>
						<?php foreach($company_types as $ctype) { ?>
							<option <?php echo $vendordetail['business_type']==$ctype['id'] ? 'selected':'' ?> value="<?php echo $ctype['id']; ?>"><?php echo $ctype['company_type_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['duns_no']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['duns_no'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">DUNS Id</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="duns_no" name="duns_no" value="<?php echo $vendordetail['duns_no']; ?>" autocomplete="off">
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['company_registration_number']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['company_registration_number'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4" style="margin-bottom: 2rem;">
					<label class="form-label">Company Registration Number</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" name="company_registration_number" value="<?php echo $vendordetail['company_registration_number']; ?>" autocomplete="off">
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['company_registration_country']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['company_registration_country'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>" >
					<label class="form-label">Company Registration Country</label>
					<select class="form-select js-example-basic-single" name="company_registration_country" >
						<option value="">Select Any One</option>
						<?php foreach($countrieslist as $countryvalue) { ?>
							<option <?php echo $vendordetail['company_registration_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['payment_terms']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['payment_terms'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 w-full <?php echo $accessreadonly; ?>">
					<label class="form-label">Payment Terms</label>
					<select name="payment_terms" class="form-control form-select js-example-basic-single">
						<!-- <option value="">Select Any One</option> -->
						<?php foreach($payment_terms as $payt) { ?>
							<option value="<?php echo $payt['id']; ?>" <?php echo $vendordetail['payment_terms']==$payt['id']?'selected':'';?> ><?php echo $payt['payment_term_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['gstin']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['gstin'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4" style="float: left;">
					<label class="form-label">GSTN No. <span class="text-danger">*</span></label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="gstin" name="gstin" value="<?php echo $vendordetail['gstin']; ?>" autocomplete="off">
						<span class="text-danger f-12"><?php echo $vendor_verifications['GSTIN']['status'] =="Y"?$_SESSION['error_message']['9137']:"GSTN verification is pending"; ?></span>
					</div>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['pan_no']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['pan_no'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">PAN No. <span class="text-danger">*</span></label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="pan_no" name="pan_no" value="<?php echo $vendordetail['pan_no']; ?>" autocomplete="off">
						<span class="text-danger f-12"><?php echo $vendor_verifications['PAN']['status'] =="Y"?$_SESSION['error_message']['9136']:"PAN verification is pending"; ?></span>
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['tan_number']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['tan_number'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">TAN No. </label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="tan_number" name="tan_number" value="<?php echo $vendordetail['tan_number']; ?>" autocomplete="off">
						<!-- <span class="text-danger f-12"><?php echo $vendor_verifications['TAN']['status'] =="Y"?$_SESSION['error_message']['9138']:"TAN verification is pending"; ?></span> -->
					</div> 
				</div>
				<?php 
                   	} ?>
               	<div class="col-lg-4 form-group" >
					<label class="form-label d-block">Subsidiary Vendor Check</label>
					<input type="checkbox" class="js-switch-vendor" value="Y" name="subsidiary_vendor_check " id="subsidiary_vendor_check " <?php echo $vendordetail['subsidiary_vendor_check '] =="Y"?"checked":""; ?> >
				</div>

                  <?php 	
                  if($fieldaccesright['field']['msme_number']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['msme_number'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group <?php echo $accessreadonly; ?>" >
					<label class="form-label d-block">Udyam Registration/MSME</label>
					<input type="checkbox" class="js-switch-vendor" value="Y" name="msme_register_yes_no" id="msme_register_yes_no" <?php echo $vendordetail['msme_register_yes_no'] =="Y"?"checked":""; ?> onchange="getmSMENumber()">
				</div>
				<?php 
                   	}if($fieldaccesright['field']['msme_number']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['msme_number'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" id="msme_number_id" <?php echo $vendordetail['msme_register_yes_no'] =="Y"?"":"style='display:none'"; ?>>
					<label class="form-label">Udyam Registration/MSME Number <span class="text-danger">*</span></label>
					<div class="kt-input-icon ">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="msme_number" name="msme_number" value="<?php echo $vendordetail['msme_number']; ?>" autocomplete="off">
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['incoterm_id']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['incoterm_id'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>" >
					<label class="form-label">ENCO Terms/ Shipping Terms</label>
					<select class="form-select js-example-basic-single" name="incoterm_id" >
						<option value="">Select Any One</option>
						<?php foreach($incoterms as $incotermvalue) { ?>
							<option <?php echo $vendordetail['incoterm_id']==$incotermvalue['id'] ? 'selected':'' ?> value="<?php echo $incotermvalue['id']; ?>"><?php echo $incotermvalue['incoterm_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['image']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['image'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 <?php echo $accessreadonly; ?>">
					<label class="form-label">Logo</label>
					<div class="col-lg-9 col-xl-6 ">
						<div class="profile-pic">
							<label class="-label" for="display_logo">
								<span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
							</label>
							<input type="file" name="image" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
							<img src="<?php echo "uploads/all/".$vendordetail['image'] ; ?>" id="outputdisplay_logo" width="200" onerror="this.src='uploads/all/no-image.jpg';"/>
							<input type="hidden" name="hid_image" value="<?php echo $vendordetail['image'] ;  ?>">
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="tab-pane" id="company-bank-info" role="tabpanel">
			<div class="row ">
				<?php 
					$fieldaccesright = $vafield['bank_information'];
					if($fieldaccesright['field']['bank_name']!="NA"){ 
						$accessreadonly = $fieldaccesright['field']['bank_name'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">Bank name <span class="text-danger">*</span></label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_name" name="bank_name" value="<?php echo $vendordetail['bank_name']; ?>" >
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_address']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_address'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-8">
					<label class="form-label">Bank Address</label>
					<textarea type="text" class="form-control <?php echo $accessreadonly; ?>" name="bank_address"><?php echo $vendordetail['bank_address']; ?></textarea>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_country']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_country'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-3 w-full <?php echo $accessreadonly; ?>">
					<label class="form-label">Country</label>
					<select class="form-select js-example-basic-single" name="bank_country" id="bank_country" onchange="selectedcategory(this.value,'bank_state','states','country_id','selectedshipcountry')">
						<option value="">Select Any One</option>
						<?php foreach($countrieslist as $countryvalue) { ?>
							<option <?php echo $vendordetail['bank_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_state']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_state'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-3  w-full <?php echo $accessreadonly; ?>" >
					<label class="form-label">State</label>
					<select  name="bank_state" id="bank_state" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'bank_city','cities','state_id','selectedbillstate')">
						<option value="">Select Any One</option>
						<?php foreach($states as $states1) { ?>
							<option <?php echo $vendordetail['bank_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_city']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_city'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-3 w-full <?php echo $accessreadonly; ?>" >
					<label class="form-label">City/Town</label>
					<select  name="bank_city"  id="bank_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
						<option value="">Select Any One</option>
						<?php foreach($cities as $cities1) { ?>
							<option <?php echo $vendordetail['bank_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_post_code']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_post_code'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-3" >
					<label  class="form-label">Pincode</label>
					<input class="form-control <?php echo $accessreadonly; ?>" placeholder="Pincode" type="text" id="bank_post_code" name="bank_post_code" value="<?php echo $vendordetail['bank_post_code']; ?>" >
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_beneficiary_name']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_beneficiary_name'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">Company Bank Beneficiary Name </label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_beneficiary_name" name="bank_beneficiary_name" value="<?php echo $vendordetail['bank_beneficiary_name']; ?>" >
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_account_no']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_account_no'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">Bank Account Number <span class="text-danger">*</span></label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="number" id="bank_account_no" name="bank_account_no" value="<?php echo $vendordetail['bank_account_no']; ?>" >
					<span class="text-danger f-12"><?php echo $vendor_verifications['BANK']['status'] =="Y"?$_SESSION['error_message']['9140']:"Bank Account verification is pending"; ?></span>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['account_type']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['account_type'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>">
					<label class="form-label">Type Of Bank Account</label>
					<select  name="account_type"  id="account_type" class="form-select js-example-basic-single">
						<option value="">Select Any One</option>
						<?php foreach($bank_acoount_type as $batypekey=>$batypeval) { ?>
							<option <?php echo $vendordetail['account_type']==$batypekey ? 'selected':'' ?> value="<?php echo $batypekey;?>"><?php echo $batypeval; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['ifsc_code']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['ifsc_code'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">IFSC Code <span class="text-danger">*</span></label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="ifsc_code" name="ifsc_code" value="<?php echo $vendordetail['ifsc_code']; ?>" autocomplete="off">
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['routing_aba_dfi_ach']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['routing_aba_dfi_ach'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">Routing-ABA/DFI/ACH</label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="routing_aba_dfi_ach" name="routing_aba_dfi_ach" value="<?php echo $vendordetail['routing_aba_dfi_ach']; ?>" >
				</div>
				<?php 
                   	}if($fieldaccesright['field']['routing_wire_Code']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['routing_wire_Code'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">Routing Wire Code</label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="routing_wire_Code" name="routing_wire_Code" value="<?php echo $vendordetail['routing_wire_Code']; ?>" >
				</div>
				<?php 
                   	}if($fieldaccesright['field']['swift_bic_code']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['swift_bic_code'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">SWIFT/BIC Code</label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="swift_bic_code" name="swift_bic_code" value="<?php echo $vendordetail['swift_bic_code']; ?>" >
				</div>
				<?php 
                   	}if($fieldaccesright['field']['iban_code']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['iban_code'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">IBAN</label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="iban_code" name="iban_code" value="<?php echo $vendordetail['iban_code']; ?>" >
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_query_person_name']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_query_person_name'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">Contact Person Name (Bank related queries)</label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_query_person_name" name="bank_query_person_name" value="<?php echo $vendordetail['bank_query_person_name']; ?>" >
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_query_contact_no']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_query_contact_no'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">Contact no</label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_query_contact_no" name="bank_query_contact_no" value="<?php echo $vendordetail['bank_query_contact_no']; ?>" >
				</div>
				<?php 
                   	}if($fieldaccesright['field']['bank_query_email_id']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['bank_query_email_id'] !="E"?"custom_readonly":"";    
                ?>
				<div class="form-group col-lg-4">
					<label class="form-label">Contact Email </label>
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_query_email_id" name="bank_query_email_id" value="<?php echo $vendordetail['bank_query_email_id']; ?>" >
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="tab-pane" id="vendor-other-info" role="tabpanel">
			<div class="row">
			<?php 
				$fieldaccesright = $vafield['other_information'];
				if($fieldaccesright['field']['vendor_type']!="NA"){ 
					$accessreadonly = $fieldaccesright['field']['vendor_type'] !="E"?"custom_readonly":"";    
            ?>
			<div class="form-group  col-lg-4 w-full <?php echo $accessreadonly; ?>" >
				<label class="form-label">Vendor Type</label>
				<select name="vendor_type" class="form-select js-example-basic-single">
					<?php foreach($vendor_type as $vtypekey=>$vtypeval) { ?>
						<option <?php echo ($vendordetail['vendor_type']==$vtypekey OR $vendordetail['vendor_type'] =="")?'selected':'' ?> value="<?php echo $vtypekey;?>"><?php echo $vtypeval; ?></option>
					<?php } ?>
				</select>
				<div style="display: none;color: red;" id="title_err"> Please enter title </div> 
			</div>
			<?php 
               	}if($fieldaccesright['field']['title']!="NA"){ 
                   $accessreadonly = $fieldaccesright['field']['title'] !="E"?"custom_readonly":"";    
            ?>
			<div class="col-lg-4 form-group " >
				<label class="form-label">Title</label>
				<div class="kt-input-icon">
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="title" name="title" value="<?php echo $vendordetail['title']; ?>">	
				</div>
				<div style="display: none;color: red;" id="title_err"> Please enter title </div> 
			</div>
			<?php 
               	}if($fieldaccesright['field']['suffix']!="NA"){ 
                   $accessreadonly = $fieldaccesright['field']['suffix'] !="E"?"custom_readonly":"";    
            ?>
			<div class="form-group col-lg-4" style="float: left;">
				<label class="form-label">Suffix</label>
				<div class="kt-input-icon">
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="suffix" name="suffix" value="<?php echo $vendordetail['suffix']; ?>" >
				</div>
				<div style="display: none;color: red;" id="suffix_err"> Please enter suffix name </div> 
			</div>
			<?php 
               	}if($fieldaccesright['field']['gsttype']!="NA"){ 
                   $accessreadonly = $fieldaccesright['field']['gsttype'] !="E"?"custom_readonly":"";    
            ?>
			<div class="col-lg-4 " style="float: left;">
				<label class="form-label">GST  Registration type</label>
				<div class="kt-input-icon">
					<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="gsttype" name="gsttype" value="<?php echo $vendordetail['gsttype']; ?>" >	
				</div>
				<div style="display: none;color: red;" id="gsttype"> Please enter GST Registration type</div> 
			</div>
			<?php 
               	}if($fieldaccesright['field']['parent_vendor']!="NA"){ 
                   $accessreadonly = $fieldaccesright['field']['parent_vendor'] !="E"?"custom_readonly":"";    
            ?>
			<div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>">
				<label class="form-label">Parent Vendor</label>
				<select name="parent_vendor" class="form-select js-example-basic-single">
					<option value="">Select Any One</option>
					<?php foreach($vendorslist as $venlist) { ?>
						<option <?php echo $vendordetail['parent_vendor']==$venlist['id'] ? 'selected':'' ?> value="<?php echo $venlist['id'];?>"><?php echo $venlist['display_name']; ?></option>
					<?php } ?>
				</select>

				<div style="display: none;color: red;" id="email_err"> Please enter parent Customer </div> 
			</div>
			<?php 
               	}if($fieldaccesright['field']['cst_reg_no']!="NA"){ 
                   $accessreadonly = $fieldaccesright['field']['cst_reg_no'] !="E"?"custom_readonly":"";    
            ?>
			<div class="col-lg-4 form-group" >
					<label class="form-label">CST Reg.No</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="cst_reg_no" name="cst_reg_no" value="<?php echo $vendordetail['cst_reg_no']; ?>" >
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['tds_apply']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['tds_apply'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group <?php echo $accessreadonly; ?>" >
					<label class="form-label">  </label>
					<div class="kt-checkbox-inline">
						<label class="kt-checkbox">
							<input type="checkbox" name="tds_apply" class="js-switch-vendor" <?php echo $vendordetail['tds_apply'] == "0"?"checked":"";?>> Apply TDS for this vendor
							<span></span>
						</label>
					</div>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['pref_payment_method']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['pref_payment_method'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 w-full form-group <?php echo $accessreadonly; ?>">
					<label class="form-label">Preferred Payment Method</label>
					<select name="pref_payment_method" class="form-control form-select js-example-basic-single">
						<option value="">Select Any One</option>
						<?php foreach($payment_method as $key=>$pm) { ?>
							<option value="<?php echo $key; ?>" <?php echo $vendordetail['pref_payment_method']==$key?'selected':'';?> ><?php echo $pm; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['preferred_deliver_method']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['preferred_deliver_method'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">Preferred Delivery Method</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="preferred_deliver_method" name="preferred_deliver_method" value="<?php echo $vendordetail['preferred_deliver_method']; ?>" >
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['opening_balance']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['opening_balance'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4" style="margin-bottom: 2rem;">
					<label class="form-label">Opening Blance</label>
					<div class="kt-input-icon">
						<input class="form-control text-right <?php echo $accessreadonly; ?>" type="text" id="opening_balance" name="opening_balance" value="<?php echo $vendordetail['opening_balance']; ?>" >
					</div>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['asof']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['asof'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">As of</label>
					<div class="kt-input-icon">
						<input class="form-control datepicker <?php echo $accessreadonly; ?>" type="text" id="asof" name="asof" value="<?php echo fromDate($vendordetail['asof']); ?>" >
					</div>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['pay_with_me']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['pay_with_me'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">The Vendor Pays Me With</label>
					<div class="kt-input-icon">
						<input class="form-control <?php echo $accessreadonly; ?>" type="text" id="pay_with_me" name="pay_with_me" value="<?php echo $vendordetail['pay_with_me']; ?>" >
					</div>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['credit_limit']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['credit_limit'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">Credit Limit</label>
					<div class="kt-input-icon">
						<input class="form-control text-right <?php echo $accessreadonly; ?>" type="text" id="credit_limit" name="credit_limit" value="<?php echo $vendordetail['credit_limit']; ?>" >
					</div>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['default_discount_perc']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['default_discount_perc'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">Default Discount Percentage (%)</label>
					<div class="kt-input-icon">
						<input class="form-control text-right <?php echo $accessreadonly; ?>" type="text" id="default_discount_perc" name="default_discount_perc" value="<?php echo $vendordetail['default_discount_perc']; ?>" >
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['payment_discount_perc']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['payment_discount_perc'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 form-group" >
					<label class="form-label">Payment Discount Percentage (%)</label>
					<div class="kt-input-icon">
						<input class="form-control text-right <?php echo $accessreadonly; ?>" type="text" id="payment_discount_perc" name="payment_discount_perc" value="<?php echo $vendordetail['payment_discount_perc']; ?>" >
					</div> 
				</div>
				<?php 
                   	}if($fieldaccesright['field']['sales_type']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['sales_type'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-4 w-full form-group <?php echo $accessreadonly; ?>">
					<label class="form-label">Sales Type</label>
					<select name="sales_type" class="form-control form-select js-example-basic-single">
						<option value="">Select Any One</option>
						<?php foreach($salestype as $key=>$st) { ?>
							<option value="<?php echo $key; ?>" <?php echo $vendordetail['sales_type']==$key?'selected':'';?> ><?php echo $st; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="tab-pane" id="other-contact-info" role="tabpanel">
			<?php 
				$fieldaccesright = $vafield['other_contact_information'];
				if($fieldaccesright['field']['other_contact_information']!="NA"){ 
					$accessreadonly = $fieldaccesright['field']['other_contact_information'] !="E"?"custom_readonly":"";    
            ?>
			<table class="table table-hover <?php echo $accessreadonly;?>">
				<head>
                    <tr>
                    	<th>Type</th>
                      	<th>Name</th>
                      	<th>Number</th>
                      	<th>Email Id</th>
                    </tr>
                </head>
                <tbody>
                	<?php foreach($vendor_contact_info_type as $vcikey=>$vcival){ ?>
                	<tr>
                		<td>
                			<?php echo $vcival; ?>
                		</td>
                		<td>
                			<input class="form-control" type="text" name="other_contact_information[<?php echo $vcikey; ?>][name]" value="<?php echo $other_contact_information[$vcikey]['name']; ?>">
                		</td>
                		<td>
                			<input class="form-control" type="number" name="other_contact_information[<?php echo $vcikey; ?>][number]" value="<?php echo $other_contact_information[$vcikey]['number']; ?>">
                		</td>
                		<td>
                			<input class="form-control" type="text" name="other_contact_information[<?php echo $vcikey; ?>][email_id]" value="<?php echo $other_contact_information[$vcikey]['email_id']; ?>">
                		</td>
                	</tr>
                	<?php } ?>
                </tbody>
            </table>
        <?php } ?>
		</div>
		<div class="tab-pane" id="note" role="tabpanel">
			<div class="row ">
				<?php 
					$fieldaccesright = $vafield['note'];
					if($fieldaccesright['field']['vendor_po_notes']!="NA"){ 
						$accessreadonly = $fieldaccesright['field']['vendor_po_notes'] !="E"?"custom_readonly":"";    
	            ?>
				<div class="col-lg-6 p-r-5" style="float: left;">
					<label class="form-label">PO Notes</label>
					<textarea type="text" name="vendor_po_notes"  class="form-control <?php echo $accessreadonly; ?>" style="height: 150px;" placeholder="Enter your Customer Statement Notes"><?php echo $vendordetail['vendor_po_notes']; ?></textarea>
				</div>
				<?php 
                   	}if($fieldaccesright['field']['vendor_other_notes']!="NA"){ 
                       $accessreadonly = $fieldaccesright['field']['vendor_other_notes'] !="E"?"custom_readonly":"";    
                ?>
				<div class="col-lg-6" style="float: left;">
					<label class="form-label">Other Notes</label>
					<textarea type="text" name="vendor_other_notes"   class="form-control <?php echo $accessreadonly; ?>" style="height: 150px;" placeholder="Enter your Customer Invoice Notes"><?php echo $vendordetail['vendor_other_notes']; ?></textarea>
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="tab-pane" id="agreements" role="tabpanel">
			<div class="p-3">
				<h4 class="mb-10 font-weight-bold text-dark">Agreemets</h4>
					<div class="form-group col-lg-12">
						<table class="table table-bordered " id="edutable">
							<tr>
								<th class="" width="2%">Sl NO</th>
								<th class="" width="25%">Name</th>
								<th class="thheader" width="20%">Number</th>
								<th class="" width="20%"> Sign Date</th>
								<th class="thheader" width="20%">Expiry Date </th>
								<th class="thheader" width="15%">Files</th>
							</tr>
						<?php $k=0;  if(count((array)$aggrements) != '0'){ $aggrements[] = array('agreement_name'=>''); }

						foreach ($aggrements as $inst) { $k++;?>
							<tr class="rowcount">
								<td><?php echo $k; ?>
								<input type="hidden" name="ag_rowid[]" class="form-control itemRow" value="<?php echo $inst['id']; ?>" autocomplete="off">
							</td>

							<td><input type="text" name="ag_type[]" class="form-control" value="<?php echo $inst['agreement_name']; ?>" autocomplete="off">  </td>
							<td>
								<input type="text" name="ag_number[]" class="form-control" value="<?php echo $inst['agreement_ref_no']; ?>" autocomplete="off">
							</td>
							<td><input type="text" name="ag_sin_date[]" class="form-control datepicker "  id="kt_datepicker_3<?php echo $k; ?>" value="<?php echo fromDate($inst['signed_date']); ?>"  autocomplete="off">  </td>
							<td><input type="text" name="ag_ex_date[]" class="form-control datepicker" id="kt_datepicker_3<?php echo $k; ?>"  value="<?php echo fromDate($inst['expiry_date']); ?>" autocomplete="off">  </td>
							<td>
								<div class="row m-t-15">
									<div class="image-input col-lg-3" id="kt_image_5" style="display: inherit; margin-top: -2px;">
										<label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" >
											<i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i>
											<input type="file" name="agreement_file[]" />
											<input type="hidden" name="hidden_agreement_file[]" value="<?php echo $inst['file_url'][$k]; ?>"/>
										</label>
									</div>
									<?php if($inst['file_url']!=''){ ?>
										<div class="col-lg-3">
											<a href="<?php echo $File_Uploads_Path."/".$inst['file_url']; ?>" data-toggle="tooltip" title="Download" target="_blank" >
												<i class="ti-cloud-down f-20 text-warning f-30 f-w-800"></i>
											</a></div>
										<?php } ?>
									</div>
								</td>

							</tr>
						<?php }  ?>
					</table>
					<div class="row">
						<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
							<button class="btn btn-success float-left" onclick="addedurows()" id="addRows" type="button">+ Add More</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="attachments" vendordetail="tabpanel">
			<div class="form-group row">
				<div class="col-lg-4 col-md-9 col-sm-12">
					<div class="dropzone dropzone-default dropzone-brand dz-clickable" id="kt_dropzone_2">
						<div class="dropzone-msg dz-message needsclick">
							<h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
							<span class="dropzone-msg-desc">Upload up to 10 files</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="employees" vendordetail="tabpanel">
			<div class="card-body">
				<?php 
				if(count((array)$emplist) != 0){
					$i=1; ?>
				<table class="table table-bordered" id="kt_datatable1">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Email Id</th>
							<th>Billable Status</th>
							<th>Esgin transaction</th>

						</tr>
					</thead>
					<tbody>
						<?php
						foreach($emplist as $emplist){ ?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $emplist['display_name']; ?> </td>
								<td><?php echo  $emplist['email_id']; ?> </td>
								<td><?php echo $emplist['billable']==0? "Billable":"Non Billable"; ?></td>
								<td><?php echo $emplist['transaction_number']; ?> </td>
							</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				<?php }else{ ?>

					<div class="card-px text-center ">
						<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Employees</p>   
						<div class="text-center px-4">
							<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
						</div>   
					</div>
				<?php } ?>
				<!--end: Datatable-->
			</div>
		</div>
		<div class="tab-pane" id="no-hiring" role="tabpanel">
			<h4 class="mb-10 font-weight-bold text-dark">No Hiring clause</h4>
			<div class="form-group row">
				<div class="col-lg-2 "></div>
				<div class="col-lg-4 col-md-6 col-sm-12 row mt-3">
					<div class="col-7">
						<span class="switch switch-outline switch-icon switch-info">
							<label>
								<input type="checkbox" onclick="checkboxclick()" <?php if($vendordetail['hiring_clause']==0){ echo "checked";  } ?> id="hiring_clause" name="hiring_clause" class="js-switch">
								<span></span>
							</label>
						</span>
						<label class="col-form-label">No Hiring Clause </label>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 coolingmonth">
					<label class="form-label">Cooling Period of Months</label>
					<div class="kt-input-icon">
						<input class="form-control"   type="text" id="cooling_months" name="cooling_months"   value="<?php echo $vendordetail['cooling_months']; ?>">
					</div>
				</div>
				<div class="col-lg-2 "></div>
			</div>
		</div>  
		<div class="tab-pane" id="vendor-questions" role="tabpanel">
			<?php 
				$fieldaccesright = $vafield['questions'];
				$accessreadonly = $fieldaccesright['field']['questions'] !="E"?"custom_readonly":"";    
            ?>
			<div class="card-body <?php echo $accessreadonly; ?>" >
				<div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Vendor Related Questions</div>
				<div class="row mt-2">
				    <h6>A) Previously have you or any of your subsidiaries ever associated with <?php echo ucwords($_SESSION['config']['name']); ?> (USA or any other region)?</h6>
	                <div class="form-radio">
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" name="vendor_questions[radio_associate]" value="Y"  onchange="onchangeAssociatedAnswer(this.value)" <?php echo $vendor_questions['radio_associate'] =="Y"?"checked":""; ?>>
	                        	<i class="helper"></i>Yes
	                      	</label>
	                    </div>
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" name="vendor_questions[radio_associate]" value="N" onchange="onchangeAssociatedAnswer(this.value)" <?php echo ($vendor_questions['radio_associate']=="N" || $vendor_questions['radio_associate']=="")?"checked":""; ?>>
	                        	<i class="helper"></i>No
	                      	</label>
	                    </div>
	                </div>
		       	</div>
		       	<div class="associated_team form-group mt-2" <?php echo $vendor_questions['radio_associate'] =="Y"?"":"style='display: none'"; ?> >
					<label class="form-label">Please provide details</label>
					<textarea type="text" name="vendor_questions[associated_team_detail]" class="form-control"><?php echo $vendor_questions['associated_team_detail']; ?></textarea>
				</div>
				<div class="row mt-2">
				    <h6>B) Does your company has a quality system ?(If Answer is Yes Then attachs the Quality Certificates)</h6>
	                <div class="form-radio">
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" name="vendor_questions[quality_system]" value="Y"  onchange="onchangeVRQAnswer(this.value)" <?php echo $vendor_questions['quality_system'] =="Y"?"checked":""; ?>>
	                        	<i class="helper"></i>Yes
	                      	</label>
	                    </div>
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" name="vendor_questions[quality_system]" value="N" onchange="onchangeVRQAnswer(this.value)" <?php echo ($vendor_questions['quality_system']=="N" || $vendor_questions['quality_system']=="")?"checked":""; ?>>
	                        	<i class="helper"></i>No
	                      	</label>
	                    </div>
	                </div>
		       	</div>
		       	<div class="vrq_extra mt-2" <?php echo $vendor_questions['quality_system'] =="Y"?"":"style='display: none'"; ?>>
			       	<div class="row">
			       		<div class="form-group col-lg-1">
			       			<label class="form-label">1</label>
			       		</div>
					    <div class="form-group col-lg-5">
							<label class="form-label">Certificate Name</label>
							<input class="form-control" type="text"  name="vendor_questions[quality_system_certificate][name][1]" value="<?php echo $vendor_questions['quality_system_certificate']['name']['1']; ?>">
						</div>
						<div class="form-group col-lg-3">
							<label class="form-label">Effective From Date</label>
							<input class="form-control datepicker" type="text" name="vendor_questions[quality_system_certificate][fromdate][1]" value="<?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['1']); ?>">
						</div>
						<div class="form-group col-lg-3">
							<label class="form-label">Effective To Date</label>
							<input class="form-control datepicker" type="text" name="vendor_questions[quality_system_certificate][todate][1]" value="<?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['1']); ?>">
						</div>
			       	</div>
			       	<div class="row">
			       		<div class="form-group col-lg-1">
			       			<label class="form-label">2</label>
			       		</div>
					    <div class="form-group col-lg-5">
							<label class="form-label">Certificate Name</label>
							<input class="form-control" type="text"  name="vendor_questions[quality_system_certificate][name][2]" value="<?php echo $vendor_questions['quality_system_certificate']['name']['2']; ?>">
						</div>
						<div class="form-group col-lg-3">
							<label class="form-label">Effective From Date</label>
							<input class="form-control datepicker" type="text" name="vendor_questions[quality_system_certificate][fromdate][2]" value="<?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['2']); ?>">
						</div>
						<div class="form-group col-lg-3">
							<label class="form-label">Effective To Date</label>
							<input class="form-control datepicker" type="text"  name="vendor_questions[quality_system_certificate][todate][2]" value="<?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['2']); ?>">
						</div>
			       	</div>
		       </div>

		       	<div class="f-15 f-w-600 text-info mb-2 mt-2" style="clear: both;">Small Business Registerd</div>
				<div class="row mt-2">
					<div class="col-lg-10">
						
						<?php $smallbussinesary = array('1'=>'Self Certified','2'=>'Sam Govt Registered','3'=>'Small Disadvantage','4'=>'Women Owned','5'=>'Veteran Owned','6'=>'Service - Disabled Veteran','7'=>'Small Business'); 
								foreach($smallbussinesary as $keybuss=>$smallbus){ 
							?>
		                    <div class="checkbox-fade fade-in-primary">
		                        <label class="form-label">
		                            <input type="checkbox" value="Y" name="vendor_questions[small_business_registerd][<?php echo $keybuss;?>]" <?php echo $vendor_questions['small_business_registerd'][$keybuss] =="Y"?"checked":""; ?>>
		                            <span class="cr">
		                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
		                            </span>
		                            <span><?php echo $smallbus; ?></span>
		                       </label>
		                    </div>
		                <?php } ?>
	                </div> 
	                <div class="col-lg-2">
	                	<div class="form-group">
							<label class="form-label">Others</label>
							<input class="form-control" type="text"  name="vendor_questions[small_business_registerd][100]" value="<?php echo $vendor_questions['small_business_registerd']['100']; ?>">
						</div> 
					</div>
	                  		 
		       	</div>
		       	<div class="row">
		       		<div class="col-lg-6">
	                	<div class="row">
		                	<div class="form-group col-lg-6">
								<label class="form-label">Unique Entity ID</label>
								<input class="form-control" type="text"  name="vendor_questions[small_business_registerd][101]" value="<?php echo $vendor_questions['small_business_registerd']['101']; ?>">
							</div>
							<div class="form-group col-lg-6">
								<label class="form-label">Cage Code</label>
								<input class="form-control" type="text"  name="vendor_questions[small_business_registerd][102]" value="<?php echo $vendor_questions['small_business_registerd']['102']; ?>">
							</div>
						</div> 
					</div> 
		       	</div>
		       	<div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Supply Chain Responsibility</div>
				<div class="row mt-2">
				    <h6>1) Is Your Company a Member of RBA ( Responsible Business Alliance)</h6>
	                <div class="form-radio">
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][1]" <?php echo $vendor_questions['supply_Chain_responsibility']['1'] =="Y"?"checked":""; ?>>
	                        	<i class="helper"></i>Yes
	                      	</label>
	                    </div>
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][1]" <?php echo ($vendor_questions['supply_Chain_responsibility']['1']=="N" || $vendor_questions['supply_Chain_responsibility']['1']=="")?"checked":""; ?>>
	                        	<i class="helper"></i>No
	                      	</label>
	                    </div>
	                </div>
		       	</div>
		       	<div class="row mt-2">
				    <h6>2) Are all employees of your company atleast 18 years of age</h6>
	                <div class="form-radio">
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][2]" <?php echo $vendor_questions['supply_Chain_responsibility']['2'] =="Y"?"checked":""; ?>>
	                        	<i class="helper"></i>Yes
	                      	</label>
	                    </div>
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][2]" <?php echo ($vendor_questions['supply_Chain_responsibility']['2']=="N" || $vendor_questions['supply_Chain_responsibility']['2']=="")?"checked":""; ?>>
	                        	<i class="helper"></i>No
	                      	</label>
	                    </div>
	                </div>
		       	</div>
		       	<div class="row mt-2">
				    <h6>3) Does Your Company has 1 Employee? If "Yes" Skip To No. 7 If "No" Go To Question No. 4</h6>
	                <div class="form-radio">
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][3]" <?php echo $vendor_questions['supply_Chain_responsibility']['2'] =="Y"?"checked":""; ?>   onchange="onchangeSupplyChainResponsibilityAnswer(this.value,3)" >
	                        	<i class="helper"></i>Yes
	                      	</label>
	                    </div>
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][3]" <?php echo ($vendor_questions['supply_Chain_responsibility']['3']=="N" || $vendor_questions['supply_Chain_responsibility']['3']=="")?"checked":""; ?> onchange="onchangeSupplyChainResponsibilityAnswer(this.value,3)">
	                        	<i class="helper"></i>No
	                      	</label>
	                    </div>
	                </div>
		       	</div>
		       	<div class="row supplychainresponsibility_4 mt-2">
				    <h6>4) Do you have hourly employees that works at manufacturer facility? For the purpose of this form, hourly employees are employees that are subject to local laws governing overtime pay or subject to hourly increased or decreased in compensation based on volume of product if Yes (Please answer a, b and c, if No then skip to Question no 5)</h6>
	                <div class="form-radio">
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][4]" onchange="onchangeWorkManufracturerFacilityAnswer(this.value)" <?php echo $vendor_questions['supply_Chain_responsibility']['4'] =="Y"?"checked":""; ?>>
	                        	<i class="helper"></i>Yes
	                      	</label>
	                    </div>
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][4]" onchange="onchangeWorkManufracturerFacilityAnswer(this.value)" <?php echo ($vendor_questions['supply_Chain_responsibility']['4']=="N" || $vendor_questions['supply_Chain_responsibility']['4']=="")?"checked":""; ?>>
	                        	<i class="helper"></i>No
	                      	</label>
	                    </div>
	                </div>
		       	</div>
		       	<div class="m-l-20 work_manufacturer_facility supplychainresponsibility_4 mt-2" <?php echo $vendor_questions['supply_Chain_responsibility'][4] =="Y"?"":"style='display: none'"; ?>>
			       	<div class="row">
					    <h6>a) Do all hourly workers work less than 60 hours per week?</h6>
		                <div class="form-radio">
		                    <div class="radio radio-inline">
		                      	<label class="form-label">
		                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][4_a]" <?php echo $vendor_questions['supply_Chain_responsibility']['4_a'] =="Y"?"checked":""; ?>>
		                        	<i class="helper"></i>Yes
		                      	</label>
		                    </div>
		                    <div class="radio radio-inline">
		                      	<label class="form-label">
		                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][4_a]" <?php echo ($vendor_questions['supply_Chain_responsibility']['4_a']=="N" || $vendor_questions['supply_Chain_responsibility']['4_a']=="")?"checked":""; ?>> 
		                        	<i class="helper"></i>No
		                      	</label>
		                    </div>
		                </div>
			       	</div>
			       	<div class="row mt-2">
					    <h6>b) Do you have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor?</h6>
		                <div class="form-radio">
		                    <div class="radio radio-inline">
		                      	<label class="form-label">
		                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][4_b]" <?php echo $vendor_questions['supply_Chain_responsibility']['4_b'] =="Y"?"checked":""; ?>>
		                        	<i class="helper"></i>Yes
		                      	</label>
		                    </div>
		                    <div class="radio radio-inline">
		                      	<label class="form-label">
		                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][4_b]" <?php echo ($vendor_questions['supply_Chain_responsibility']['4_b']=="N" || $vendor_questions['supply_Chain_responsibility']['4_b']=="")?"checked":""; ?>>
		                        	<i class="helper"></i>No
		                      	</label>
		                    </div>
		                </div>
			       	</div>
			       	<div class="row mt-2">
					    <h6>c) Do you expect your supplier to have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor</h6>
		                <div class="form-radio">
		                    <div class="radio radio-inline">
		                      	<label class="form-label">
		                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][4_c]" <?php echo $vendor_questions['supply_Chain_responsibility']['4_c'] =="Y"?"checked":""; ?>>
		                        	<i class="helper"></i>Yes
		                      	</label>
		                    </div>
		                    <div class="radio radio-inline">
		                      	<label class="form-label">
		                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][4_c]" <?php echo ($vendor_questions['supply_Chain_responsibility']['4_c']=="N" || $vendor_questions['supply_Chain_responsibility']['4_c']=="")?"checked":""; ?>>
		                        	<i class="helper"></i>No
		                      	</label>
		                    </div>
		                </div>
			       	</div>
		       	</div>
		       	<div class="row supplychainresponsibility_5 mt-2" <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="")?"":"style='display: none'"; ?>>
				    <h6>5) Do You retain or hold your employees original passport or other original personal identification documents?</h6>
	                <div class="form-radio">
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][5]" onchange="onchangeHoldOriginalPassportAnswer(this.value)" <?php echo $vendor_questions['supply_Chain_responsibility']['5'] =="Y"?"checked":""; ?>>
	                        	<i class="helper"></i>Yes
	                      	</label>
	                    </div>
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][5]" checked="checked" onchange="onchangeHoldOriginalPassportAnswer(this.value)" <?php echo ($vendor_questions['supply_Chain_responsibility']['5']=="N" || $vendor_questions['supply_Chain_responsibility']['5']=="")?"checked":""; ?>>
	                        	<i class="helper"></i>No
	                      	</label>
	                    </div>
	                </div>
		       	</div>
		       	<div class="m-l-20 hold_original_passport supplychainresponsibility_5 mt-2" <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="" AND $vendor_questions['supply_Chain_responsibility'][5] =="Y")?"":"style='display: none'"; ?> >
			       	<div class="row">
					    <h6>a) Do you return original passport or other original personal identification documents upon the employee's request?</h6>
		                <div class="form-radio">
		                    <div class="radio radio-inline">
		                      	<label class="form-label">
		                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][5_a]" <?php echo $vendor_questions['supply_Chain_responsibility']['5_a'] =="Y"?"checked":""; ?>>
		                        	<i class="helper"></i>Yes
		                      	</label>
		                    </div>
		                    <div class="radio radio-inline">
		                      	<label class="form-label">
		                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][5_a]" <?php echo ($vendor_questions['supply_Chain_responsibility']['5_a']=="N" || $vendor_questions['supply_Chain_responsibility']['5_a']=="")?"checked":""; ?>>
		                        	<i class="helper"></i>No
		                      	</label>
		                    </div>
		                </div>
			       	</div>
			    </div>
			    <div class="row supplychainresponsibility_6 mt-2" >
				    <h6>6) Do any employee have to reimburse recruitment fee to your company?</h6>
	                <div class="form-radio">
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][6]" <?php echo $vendor_questions['supply_Chain_responsibility']['6'] =="Y"?"checked":""; ?>>
	                        	<i class="helper"></i>Yes
	                      	</label>
	                    </div>
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][6]" <?php echo ($vendor_questions['supply_Chain_responsibility']['6']=="N" || $vendor_questions['supply_Chain_responsibility']['6']=="")?"checked":""; ?>>
	                        	<i class="helper"></i>No
	                      	</label>
	                    </div>
	                </div>
		       	</div>
		       	<div class="row supplychainresponsibility_7 mt-2" <?php echo $vendor_questions['supply_Chain_responsibility'][3] =="N"?"":"style='display: none'"; ?>>
				    <h6>7) Is your company (or your sub-contractor) in Xinjiang Uygur Autonomous Regision (XUAR)</h6>
	                <div class="form-radio">
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][7]" <?php echo $vendor_questions['supply_Chain_responsibility']['7'] =="Y"?"checked":""; ?>>
	                        	<i class="helper"></i>Yes
	                      	</label>
	                    </div>
	                    <div class="radio radio-inline">
	                      	<label class="form-label">
	                        	<input type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][7]" <?php echo ($vendor_questions['supply_Chain_responsibility']['7']=="N" || $vendor_questions['supply_Chain_responsibility']['7']=="")?"checked":""; ?>>
	                        	<i class="helper"></i>No
	                      	</label>
	                    </div>
	                </div>
		       	</div>
		       	<div class="f-15 f-w-600 text-info mb-2 text-center" style="clear: both;">Terms & Conditions</div>
				<span class="d-block">1. Providing information in the "Vendor Information Form" does not constitute acceptance as an approved & registered vendor. The registration of vendor is the sole discretion of AM/NS India.</span>
				<span class="d-block mt-2">2. Failure to enter all of the mandatory information will not enable us to post your vendor file to our database..</span>
				<span class="d-block mt-2">3. Being a registered vendor does NOT guarantee the receipt of notification for all applicable solicitations or invitation to offer or the award of any purchase contract or award of any invitation for bid or request for proposal or request for qualifications.</span>
				<span class="d-block mt-2">4. Vendor is responsible for the content of the information and its vendors responsibility to keep all information current and provide any subsequent up dates, additional information as and when required and when desired by AM/NS India.</span>
				<span class="d-block mt-2">5. AM/NS India reserves the rights to evaluate the vendor on periodical basis and decide the continuation / discontinuation of business with them based on the evaluation report.</span>
				<span class="d-block mt-2">6. Representative of AM/NS India OR their nominated agency has the right to visit vendors premises for the verification of information with a prior intimation to them.</span>
				<span class="d-block mt-2">7. At later date if it is found that the information furnished is untrue, inaccurate, not current, or is incomplete, we reserve the right to suspend or terminate the registration and recover the dues (if any) from you</span>

				<div class="border-checkbox-section mt-4" >
                    <div class="border-checkbox-group border-checkbox-group-primary">
                        <input class="border-checkbox" type="checkbox" <?php echo $vendor_questions['terms_conditions']['1'] =="Y"?"checked":""; ?> id="termsandcondition_1" value="Y" name="vendor_questions[terms_conditions][1]">
                        <label class="form-label border-checkbox-label f-w-600" for="termsandcondition_1">
                        	<span class="d-block">I / We have read the terms & conditions and privacy policy.</span> 
                        	<span class="d-block">I/We hereby declare my / our acceptance for the same and certify that the information submitted in this vendor registration application is true and correct to the best of my/our knowledge.</span>
                        	<span class="d-block">I / We further understand that any false statements observed at the later stage may result in denial or revocation of our vendor registration with AM/NS India group.</span></label>
                    </div>
        		</div>

        		<div class="border-checkbox-section mt-2" >
                    <div class="border-checkbox-group border-checkbox-group-primary">
                        <input class="border-checkbox" type="checkbox" <?php echo $vendor_questions['terms_conditions']['2'] =="Y"?"checked":""; ?> id="termsandcondition_2" value="Y" name="vendor_questions[terms_conditions][2]">
                        <label class="form-label border-checkbox-label f-w-600" for="termsandcondition_2">
                        	<span class="d-block">I / We agree, covenant and undertake that I / We have reviewed the policies of AMNSIL.</span>
                        <span class="d-block">I / We will comply with the principles contained in these Policies and these Policies will continue to be applicable and binding on the me/us, to the extent relevant.</span></label>
                    </div>
        		</div>
        		<?php  //echo "<pre>"; print_r($vendor_questions); echo "</pre>"; ?>
		    </div>



		</div>
	</div>
	<button type="button" class="btn btn-primary m-r-20 m-b-20" onclick="checkVendorValidationEmpty()"><i class="ti-check"></i> Update</button>
	<span class="text-danger terms_conditions" style="display: none">* Please Accept Terms And Conditions In Questions Tab</span>
		<!-- <button  class="btn btn-primary m-l-20 m-b-20"><i class="ti-check"></i>
			Update</button> -->
	</form>
</div>

<?php }else{ ?>	

<div class="row"> 
	<?php 
		$fieldaccesright = $vafield['business_information'];
		if($fieldaccesright['access'] !="NA"){  
    ?>  
	<div class="col-lg-12 col-xl-6">
		<div class="table-responsive">
			<div class="card-header"><h5 class="text-primary">Business Information</h5></div>
			<div class="card-body">
				<table class="table m-0">
					<tbody>
						<?php 
							if($fieldaccesright['field']['company']!="NA"){  
		                ?>
		                <tr>
							<th scope="row" class="p-2">Company Name</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['company']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['website_url']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Corporate Website</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['website_url']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['company_trading_name']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">DBA(Trading Name)</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['company_trading_name']); ?></td>
						</tr> 
						<?php 
		                   	}if($fieldaccesright['field']['company_subsidiary_division']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Subsidiary / Division Of</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['company_subsidiary_division']); ?></td>
						</tr> 
						<?php 
		                   	}if($fieldaccesright['field']['middle_name']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Contact Person Name</th>
							<td class="p-2"><?php echo $vendordetail['first_name'].$vendordetail['middle_name']." ".$vendordetail['last_name']; ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['phone']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Contact Person Contact</th>
							<td class="p-2"><?php echo $vendordetail['phone']; ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['email']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Contact Person Email</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['email']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['phone']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Phone</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['phone']); ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php 
		$fieldaccesright = $vafield['company_information'];
		if($fieldaccesright['access'] !="NA"){  
    ?>
	<div class="col-lg-12 col-xl-6">
		<div class="table-responsive">
			<div class="card-header"><h5 class="text-primary">Company Information</h5></div>
			<div class="card-body">
				<table class="table m-0">
					<tbody>
						<?php 
							
							if($fieldaccesright['field']['vendor_tax_reg_no']!="NA"){  
		                ?>
						<tr>
							<th scope="row" class="p-2">Vendor Tax Reg. No.</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['vendor_tax_reg_no']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['business_type']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Legal Structure</th>
							<td class="p-2"><?php echo emptytextdashShow($company_types[$vendordetail['business_type']]['company_type_name']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['duns_no']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">DUNS No.</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['duns_no']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['company_registration_number']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Company Registration Number</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['company_registration_number']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['company_registration_country']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Company Registration Country</th>
							<td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['company_registration_country']]); ?></td>
						</tr> 
						<?php 
		                   	}if($fieldaccesright['field']['payment_terms']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Payment Terms</th>
							<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($payment_terms[$vendordetail['payment_terms']]['payment_term_name']); ?></td>
						</tr> 
						<?php 
		                   	}if($fieldaccesright['field']['gstin']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">GSTN No.</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['gstin']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['pan_no']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">PAN No</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['pan_no']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['tan_number']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">TAN No</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['tan_number']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['incoterm_id']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Incoterm </th>
							<td class="p-2 "><?php echo emptytextdashShow($incoterms[$vendordetail['incoterm_id']]['incoterm_name']); ?></td>
						</tr>    
						<?php } ?>                              
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php 
		$fieldaccesright = $vafield['business_information'];
		if($fieldaccesright['access'] !="NA"){  
    ?>
	<div class="col-lg-12 col-xl-6">
		<div class="table-responsive">
			<div class="card-header"><h5 class="text-primary">Billing Adderss</h5></div>
			<div class="card-body">
				<table class="table m-0">
					<tbody>
						<?php 
							if($fieldaccesright['field']['billing_address']!="NA"){  
		                ?>
						<tr>
							<th scope="row" class="p-2">Billing Adderss</th>
							<td class="address-basic p-2 w-b-w-s"><?php echo $vendordetail['billing_address'].",".$vendordetail['billing_city']; ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['billing_country']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Country</th>
							<td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['billing_country']]); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['billing_state']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">State</th>
							<td class=" p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['billing_state']]); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['billing_city']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">City</th>
							<td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['billing_post_code']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Pincode</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['billing_post_code']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['contact_person']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Contact Person</th>
							<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['contact_person']); ?></td> 
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['mobile']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Contact Person Mobile</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['mobile']); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php 
		$fieldaccesright = $vafield['business_information'];
		if($fieldaccesright['access'] !="NA"){  
    ?>
	<div class="col-lg-12 col-xl-6">
		<div class="table-responsive">
			<div class="card-header"><h5 class="text-primary">Shipping Details</h5></div>
			<div class="card-body">
				<table class="table m-0">
					<tbody>
						<?php 
							if($fieldaccesright['field']['shipping_address']!="NA"){  
		                ?>
						<tr>
							<th scope="row" class="p-2">Shipping  Adderss</th>
							<td class="address-basic p-2 w-b-w-s"><?php if($vendordetail['shipping_address']!=""){ echo trim($vendordetail['shipping_address']).",".$vendordetail['shipping_city']; }else{ echo "-"; } ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['shipping_country']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Country</th>
							<td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['shipping_country']]); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['shipping_state']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">State</th>
							<td class=" p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['shipping_state']]); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['shipping_city']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">City</th>
							<td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['shipping_city']]); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['shipping_post_code']!="NA"){ 
		                ?>

						<tr>
							<th scope="row" class="p-2">Pincode</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['shipping_post_code']); ?></td>
						</tr>   
						<?php } ?>                              
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php 
		$fieldaccesright = $vafield['bank_information'];
		if($fieldaccesright['access'] !="NA"){  
    ?>
	<div class="col-lg-12 col-xl-6">
		<div class="table-responsive">
			<div class="card-header"><h5 class="text-primary">Banking Informations</h5></div>
			<div class="card-body">
				<table class="table m-0">
					<tbody>
						<?php 
							if($fieldaccesright['field']['bank_name']!="NA"){  
		                ?>
						<tr>
							<th scope="row" class="p-2">Bank name</th>
							<td class="address-basic p-2"><?php echo emptytextdashShow($vendordetail['bank_name']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['bank_address']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Bank Address</th>
							<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_address']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['bank_country']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Country</th>
							<td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['bank_country']]); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['bank_state']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">State</th>
							<td class=" p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['bank_state']]); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['bank_city']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">City</th>
							<td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['bank_city']]); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['bank_post_code']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Pincode</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['bank_post_code']); ?></td>
						</tr> 
						<?php 
		                   	}if($fieldaccesright['field']['bank_beneficiary_name']!="NA"){ 
		                ?> 
						<tr>
							<th scope="row" class="p-2">Company Bank Beneficiary Name</th>
							<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_beneficiary_name']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['bank_account_no']!="NA"){ 
		                ?> 
						<tr>
							<th scope="row" class="p-2">Bank Account Number</th>
							<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_account_no']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['account_type']!="NA"){ 
		                ?> 
						<tr>
							<th scope="row" class="p-2">Type Of Bank Account</th>
							<td class="p-2 w-b-w-s"><?php echo $bank_acoount_type[$vendordetail['account_type']]; ?></td>
						</tr> 
						<?php 
		                   	}if($fieldaccesright['field']['routing_aba_dfi_ach']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Routing-ABA/DFI/ACH</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['routing_aba_dfi_ach']); ?></td>
						</tr> 
						<?php 
		                   	}if($fieldaccesright['field']['routing_wire_Code']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Routing Wire Code</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['routing_wire_Code']); ?></td>
						</tr> 
						<?php 
		                   	}if($fieldaccesright['field']['swift_bic_code']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">SWIFT/BIC Code</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['swift_bic_code']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['iban_code']!="NA"){ 
		                ?>  
						<tr>
							<th scope="row" class="p-2">IBAN</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['iban_code']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['bank_query_person_name']!="NA"){ 
		                ?> 
						<tr>
							<th scope="row" class="p-2">Contact Person Name (Bank related queries)</th>
							<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_query_person_name']); ?></td>
						</tr> 
						<?php 
		                   	}if($fieldaccesright['field']['bank_query_contact_no']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Contact no</th>
							<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_query_contact_no']); ?></td>
						</tr>
						<?php 
		                   	}if($fieldaccesright['field']['bank_query_email_id']!="NA"){ 
		                ?>
						<tr>
							<th scope="row" class="p-2">Contact Email</th>
							<td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_query_email_id']); ?></td>
						</tr> 
						<?php } ?>                             
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php 
		$fieldaccesright = $vafield['other_contact_information'];
		if($fieldaccesright['field']['other_contact_information']!="NA"){  
    ?>
	<div class="col-lg-12 col-xl-6">
		<div class="table-responsive">
			<div class="card-header"><h5 class="text-primary">Other Contact Info</h5></div>
				<div class="card-body">
					<table class="table table-hover m-b-0" >
						<tr>
							<th align="left" >Type</th>
							<th align="left" >Name</th>
							<th align="left" >Number</th>
							<th align="left" >Email ID</th>
						</tr>
						<?php foreach($vendor_contact_info_type as $vcikey=>$vcival){ ?>
	                	<tr>
	                		<td >
	                			<?php echo $vcival; ?>
	                		</td>
	                		<td >
	                			<?php echo emptytextdashShow($other_contact_information[$vcikey]['name']); ?>
	                		</td>
	                		<td >
	                			<?php echo emptytextdashShow($other_contact_information[$vcikey]['number']); ?>
	                		</td>
	                		<td >
	                			<?php echo emptytextdashShow($other_contact_information[$vcikey]['email_id']); ?>
	                		</td>
	                	</tr>
	                	<?php } ?>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php $fieldaccesright = $vafield['note']; 
		if($fieldaccesright['access'] !="NA"){
    ?>
	<div class="col-lg-12 col-xl-6">
		<div class="table-responsive">
			<div class="card-header"><h5 class="text-primary">Notes</h5></div>
			<div class="card-body">
				<table class="table m-0">
					<tbody>
						<?php if($fieldaccesright['field']['vendor_po_notes']!="NA"){ ?>
						<tr>
							<th scope="row" class="p-2">PO Notes</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['vendor_po_notes']); ?></td>
						</tr>
						<?php }if($fieldaccesright['field']['vendor_po_notes']!="NA"){ ?>
						<tr>
							<th scope="row" class="p-2">Other Notes</th>
							<td class="p-2"><?php echo emptytextdashShow($vendordetail['vendor_other_notes']); ?></td>
						</tr>  
						<?php } ?>                          
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php if($transaction_table == "vendors"){ ?>
	<div class="col-lg-12 col-xl-6">
		<div class="row">
			<div class="col-lg-12">
				<div class="table-responsive">
					<div class="card-header"><h5 class="text-primary">Agreements</h5></div>
					<div class="card-body">
						<?php if(count((array)$aggrements)>0){  ?>
							<table class="table table-hover m-b-0">
								<thead>
									<tr>
										<th>Type</th>
										<th>Number</th>
										<th>Sign Date</th>
										<th>Expiry Date</th>
									</tr>
								</thead>
								<tbody>
									<?php $k=0;  
									foreach ($aggrements as $inst) { $k++;?>
										<tr>
											<td><?php echo $inst['agreement_name']; ?></td>
											<td><?php echo $inst['agreement_ref_no']; ?></td>
											<td><?php echo $inst['signed_date']; ?></td>
											<td><label class="label label-success"><?php echo $inst['expiry_date']; ?></label></td>
										</tr>
									<?php }  ?>
								</tbody>
							</table>
						<?php }else{ ?>
							<div class="card-px text-center ">
								<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Aggrements</p>   
								<div class="text-center px-4">
									<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
								</div>   
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

	
<?php $fieldaccesright = $vafield['questions']; 
      if($fieldaccesright['field']['questions']!="NA"){ ?>
<div class="col-lg-12 col-xl-12">
	<div class="card">
		<div class="card-header">
			<h5 class="text-primary">Questions</h5>
		</div>
		<div class="card-body">
					<div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Vendor Related Questions</div>
					<div class="row">
					    <h6>A) Previously have you or any of your subsidiaries ever associated with <?php echo ucwords($_SESSION['config']['name']); ?> (USA or any other region)?</h6>
					    <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['radio_associate']); ?></span>
			       	</div>
			       	<div class="associated_team form-group mt-3" <?php echo $vendor_questions['radio_associate'] =="Y"?"":"style='display: none'"; ?> >
						 <h6>a) Please provide details </h6>
						 <span> <span class="f-w-700">Answer :</span> <?php echo $vendor_questions['associated_team_detail']; ?></span>
					</div>
					<div class="row mt-3">
					    <h6>B) Does your company has a quality system ?(If Answer is Yes Then attachs the Quality Certificates)</h6>
					    <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['quality_system']); ?></span>
			       	</div>
			       	<div class="vrq_extra" <?php echo $vendor_questions['quality_system'] =="Y"?"":"style='display: none'"; ?>>
				       	<div class="row mt-3">
				       		<div class="form-group col-lg-1">
				       			<label class="form-label">1</label>
				       		</div>
						    <div class="form-group col-lg-5">
								<label class="form-label d-block">Certificate Name</label>
								<span><?php echo emptytextdashShow($vendor_questions['quality_system_certificate']['name']['1']); ?></span>
							</div>
							<div class="form-group col-lg-3">
								<label class="form-label d-block">Effective From Date</label>
								<span><?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['1']); ?></span>
							</div>
							<div class="form-group col-lg-3 ">
								<label class="form-label d-block">Effective To Date</label>
								<span><?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['1']); ?></span>
							</div>
				       	</div>
				       	<div class="row mt-2">
				       		<div class="form-group col-lg-1">
				       			<label class="form-label">2</label>
				       		</div>
						    <div class="form-group col-lg-5">
								<span><?php echo emptytextdashShow($vendor_questions['quality_system_certificate']['name']['2']); ?></span>
							</div>
							<div class="form-group col-lg-3">
								<span><?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['2']); ?></span>
							</div>
							<div class="form-group col-lg-3">
								<span><?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['2']); ?></span>
							</div>
				       	</div>
			       </div>

			       	<div class="f-15 f-w-600 text-info mb-2 mt-3" style="clear: both;">Small Business Registerd</div>
					<div class="row mt-3">
						<div class="col-lg-12" style="pointer-events: none;">
							<?php $smallbussinesary = array('1'=>'Self Certified','2'=>'Sam Govt Registered','3'=>'Small Disadvantage','4'=>'Women Owned','5'=>'Veteran Owned','6'=>'Service - Disabled Veteran','7'=>'Small Business'); 
								foreach($smallbussinesary as $keybuss=>$smallbus){ 
							?>
							<div class="checkbox-fade fade-in-primary">
		                        <label class="form-label">
		                            <input type="checkbox" value="Y" name="vendor_questions[small_business_registerd][<?php echo $keybuss; ?>]" <?php echo $vendor_questions['small_business_registerd'][$keybuss] =="Y"?"checked":""; ?>>
		                            <span class="cr">
		                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
		                            </span>
		                            <span><?php echo $smallbus; ?></span>
		                       </label>
		                    </div>
		                <?php } ?>
		                </div>
		            </div>
			       	<div class="row mt-3">
                		<div class="form-group col-lg-6">
							<label class="form-label d-block">Others</label>
							<span><?php echo emptytextdashShow($vendor_questions['small_business_registerd']['100']); ?></span>
						</div> 
	                	<div class="form-group col-lg-6">
							<label class="form-label d-block">Unique Entity ID</label>
							<span><?php echo emptytextdashShow($vendor_questions['small_business_registerd']['101']); ?></span>
						</div>
						<div class="form-group col-lg-6">
							<label class="form-label d-block">Cage Code</label>
							<span><?php echo emptytextdashShow($vendor_questions['small_business_registerd']['102']); ?></span>
						</div>
		      		</div>
			       	<div class="f-15 f-w-600 text-info mb-2 mt-3" style="clear: both;">Supply Chain Responsibility</div>
					<div class="row">
					    <h6>1) Is Your Company a Member of RBA ( Responsible Business Alliance)</h6>
					    <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['1']); ?></span>
			       	</div>
			       	<div class="row mt-3">
					    <h6>2) Are all employees of your company atleast 18 years of age</h6>
					    <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['2']); ?></span>
		           
			       	</div>
			       	<div class="row mt-3">
					    <h6>3) Does Your Company has 1 Employee? If "Yes" Skip To No. 7 If "No" Go To Question No. 4</h6>
					     <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['3']); ?></span>
			       	</div>
			       	<div class="row supplychainresponsibility_4 mt-3">
					    <h6>4) Do you have hourly employees that works at manufacturer facility? For the purpose of this form, hourly employees are employees that are subject to local laws governing overtime pay or subject to hourly increased or decreased in compensation based on volume of product if Yes (Please answer a, b and c, if No then skip to Question no 5)</h6>
					     <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4']); ?></span>
			       	</div>
			       	<div class="m-l-20 work_manufacturer_facility supplychainresponsibility_4 mt-3" <?php echo $vendor_questions['supply_Chain_responsibility'][4] =="Y"?"":"style='display: none'"; ?>>
				       	<div class="row mt-3">
						    <h6>a) Do all hourly workers work less than 60 hours per week?</h6>
						    <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_a']); ?></span>
				       	</div>
				       	<div class="row mt-3">
						    <h6>b) Do you have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor?</h6>
			                 <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_b']); ?></span>
				       	</div>
				       	<div class="row mt-3">
						    <h6>c) Do you expect your supplier to have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor</h6>
			                <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_c']); ?></span>
				       	</div>
			       	</div>
			       	<div class="row supplychainresponsibility_5 mt-3" <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="")?"":"style='display: none'"; ?>>
					    <h6>5) Do You retain or hold your employees original passport or other original personal identification documents?</h6>
		                 <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['5']); ?></span>
			       	</div>
			       	<div class="m-l-20 hold_original_passport supplychainresponsibility_5 mt-3" <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="" AND $vendor_questions['supply_Chain_responsibility'][5] =="Y")?"":"style='display: none'"; ?>>
				       	<div class="row">
						    <h6>a) Do you return original passport or other original personal identification documents upon the employee's request?</h6>
			                 <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['5_a']); ?></span>
				       	</div>
				    </div>
				    <div class="row supplychainresponsibility_6 mt-3" >
					    <h6>6) Do any employee have to reimburse recruitment fee to your company?</h6>
		                 <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['6']); ?></span>
			       	</div>
			       	<div class="row supplychainresponsibility_7 mt-3" <?php echo $vendor_questions['supply_Chain_responsibility'][3] =="N"?"":"style='display: none'"; ?>>
					    <h6>7) Is your company (or your sub-contractor) in Xinjiang Uygur Autonomous Regision (XUAR)</h6>
		                 <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['7']); ?></span>
			       	</div>
			    </div>
			</div>
		</div>
	<?php } ?>
</div>
<?php } ?>