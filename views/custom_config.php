<?php
//print_r($_POST); exit;
if (isset($_POST['submit'])){
	// print_r($_POST); 
	$data = $_POST;
	unset($data['submit']);
	unset($data['hid']);
	//print_r($data);
	$url="";
	$urldata = urlencode(json_encode($data,true));
	$urldata = urlencode(json_encode($data,true));
	$url = API."?mode=updaterowtabledata&rid=".$_POST['hid']."&table=".TPREFIX.TBL_CONFIG."&data=$urldata";
	//echo $url; exit;
	$response = file_get_contents($url, false, $context);
	if ($response) {
		header("location:index.php?option=com_config&Rid=TVE9PQ==");
	}

 exit;
}
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ 
	$rid =  base64_decode(base64_decode($_GET['Rid'])); 
	$url = API."?mode=singlerowtabledata&rid=".$rid."&table=".TPREFIX.TBL_CONFIG;
	//echo $url; 
	$response = file_get_contents($url, false, $context);
	//echo $response;
	$data = json_decode($response,1);
}
$time_zones = timezoan();
$typeurl = API."?mode=gettabledetails&fid=genc_code&rid=INVOICEPREFIX&table=".TBL_GENRAL_CODE_DETAILS;
//echo $typeurl; exit;
$typeresponse = file_get_contents($typeurl, false, $context);	
$perfixresponse = json_decode($typeresponse,1);
$taxurl = API."?mode=gettabledetails&fid=company_id&rid=".$_SESSION['company_id']."&table=".TPREFIX.TBL_TAXES;
//echo $taxurl."|||".$_SESSION['company_id']; exit;
$taxres = file_get_contents($taxurl, false, $context);	
$taxres = json_decode($taxres,1);
$currencyurl = API."?mode=gettabledetails&table=".TBL_CURRENCIES;
//echo $currencyurl; exit;
$currencyres = file_get_contents($currencyurl, false, $context);	
$currencyres = json_decode($currencyres,1);
$languurl = API."?mode=gettabledetails&fid=genc_code&rid=LANGUAGE&table=".TBL_GENRAL_CODE_DETAILS;
//echo $typeurl; exit;
$languagedata = file_get_contents($languurl, false, $context);	
$languagedata = json_decode($languagedata,1);
$payurl = API."?mode=gettabledetails&fid=genc_code&rid=PAYMETHOD&table=".TBL_GENRAL_CODE_DETAILS;
//echo $typeurl; exit;
$paydata = file_get_contents($payurl, false, $context);	
$paydata = json_decode($paydata,1);
//print_r($languagedata); exit;
?>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="kt-portlet">
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-grid kt-wizard-v1 kt-wizard-v1--white" id="kt_wizard_v1" data-ktwizard-state="step-first">
				<div class="kt-grid__item">

					<!--begin: Form Wizard Nav -->
					<div class="kt-wizard-v1__nav">
						<div class="kt-wizard-v1__nav-items kt-wizard-v1__nav-items--clickable">

							<!--doc: Replace A tag with SPAN tag to disable the step link click -->
							<div class="kt-wizard-v1__nav-item" data-ktwizard-type="step" data-ktwizard-state="current">
								<div class="kt-wizard-v1__nav-body">
									<div class="kt-wizard-v1__nav-icon">
										<i class="flaticon2-user-1"></i>
									</div>
									<div class="kt-wizard-v1__nav-label">
										1. Sign information
									</div>
								</div>
							</div>
							<div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
								<div class="kt-wizard-v1__nav-body">
									<div class="kt-wizard-v1__nav-icon">
										<i class="flaticon-map-location"></i>
									</div>
									<div class="kt-wizard-v1__nav-label">
										2. Contact Details
									</div>
								</div>
							</div>
							<div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
								<div class="kt-wizard-v1__nav-body">
									<div class="kt-wizard-v1__nav-icon">
										<i class="flaticon-information"></i>
									</div>
									<div class="kt-wizard-v1__nav-label">
										3. Company information
									</div>
								</div>
							</div>
							<div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
								<div class="kt-wizard-v1__nav-body">
									<div class="kt-wizard-v1__nav-icon">
										<i class="flaticon-profile"></i>
									</div>
									<div class="kt-wizard-v1__nav-label">
										4. Profile Information
									</div>
								</div>
							</div>
							<div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
								<div class="kt-wizard-v1__nav-body">
									<div class="kt-wizard-v1__nav-icon">
										<i class="flaticon-interface-7"></i>
									</div>
									<div class="kt-wizard-v1__nav-label">
										5. Formaters
									</div>
								</div>
							</div>
							<div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
								<div class="kt-wizard-v1__nav-body">
									<div class="kt-wizard-v1__nav-icon">
										<i class="flaticon-globe"></i>
									</div>
									<div class="kt-wizard-v1__nav-label">
										6. Payment And Other Information
									</div>
								</div>
							</div>
						</div>
					</div>

					<!--end: Form Wizard Nav -->
				</div>
				<div class="kt-grid__item kt-grid__item--fluid kt-wizard-v1__wrapper">

					<!--begin: Form Wizard Form-->
					<form class="kt-form"  action="" method="post">
					<div id="kt_form" >
						<!--begin: Form Wizard Step 1-->
						<div class="kt-wizard-v1__content" data-ktwizard-type="step-content" data-ktwizard-state="current">
							<div class="kt-heading kt-heading--md">Sign information</div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
									<div class="form-group">
										<label>Name</label>
										<input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo $data['name']; ?>">
										<span class="form-text text-muted">Please enter your Name.</span>
									</div>
									<div class="form-group">
										<label>Email</label>
										<input type="text" class="form-control" name="admin_email" placeholder="Email" value="<?php echo $data['admin_email']; ?>">
										<span class="form-text text-muted">Please enter your Email Address.</span>
									</div>
									<div class="form-group">
										<label>Phone Number</label>
										<input type="text" class="form-control" name="phone_number" placeholder="phone_number" value="<?php echo $data['phone_number']; ?>">
										<span class="form-text text-muted">Please enter your Phone Number.</span>
									</div>
								</div>
							</div>
						</div>

						<!--end: Form Wizard Step 1-->

						<!--begin: Form Wizard Step 2-->
						<div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
							<div class="kt-heading kt-heading--md">Enter the Contact Details</div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
									<div class="form-group">
										<label>Contact Address</label>
										<input type="text" class="form-control" name="contact_address" placeholder="Contact Address" value="<?php echo $data['contact_address']; ?>">
										<span class="form-text text-muted">Please enter your Contact Address.</span>
									</div>
									<div class="form-group">
										<label>Billing Adderess</label>
										<input type="text" class="form-control" name="billing_address" placeholder="Package Weight" value="<?php echo $data['billing_address']; ?>">
										<span class="form-text text-muted">Please enter your Billing Adderess.</span>
									</div>
								</div>
							</div>
						</div>

						<!--end: Form Wizard Step 2-->

						<!--begin: Form Wizard Step 3-->
						<div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
							<div class="kt-heading kt-heading--md">Company information</div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
									<div class="form-group">
										<label>PAN Number</label>
										<input type="text" class="form-control" name="pan_number" placeholder="Pan Number" value="<?php echo $data['pan_number']; ?>">
									</div>
									<div class="form-group">
										<label>TAN Number</label>
										<input type="text" class="form-control" name="tax_number" placeholder="TAN Number"  value="<?php echo $data['tax_numberme']; ?>" >
									</div>
									<div class="form-group">
										<label>GST Number</label>
										<input type="text" class="form-control" name="gst_number" placeholder="GST Number" value="<?php echo $data['gst_number']; ?>" >
									</div>
									<div class="form-group">
										<label>CIN Number</label>
										<input type="text" class="form-control" name="cin_number" placeholder="CIN Number"  value="<?php echo $data['cin_number']; ?>" >
									</div>
								</div>
							</div>
						</div>

						<!--end: Form Wizard Step 3-->

						<!--begin: Form Wizard Step 4-->
						<div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
							<div class="kt-heading kt-heading--md">Setup Your Profile Information</div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
									<div class="form-group">
										<label>Liscense Number</label>
										<input type="text" class="form-control" name="liscense_number" placeholder="liscense number" value="<?php echo $data['liscense_number']; ?>" >
									</div>
									<div class="form-group">
										<label>Subscription Info</label>
										<input type="text" class="form-control" name="subscription_info" placeholder="Subscription Info" value="<?php echo $data['subscription_info']; ?>" >
									</div>	
								</div>
							</div>
						</div>

						<!--end: Form Wizard Step 4-->

						<!--begin: Form Wizard Step 4.5-->

						<div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
							<div class="kt-heading kt-heading--md">Setup Your Profile Formaters</div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
									<div class="form-group">
										<label>Time Zone </label>
										<select class="form-control" id="exampleSelect1"   name= "time_zone">
														<?php foreach ($time_zones as $key=>$time_zone) {?>

														<option <?php if($data['time_zone'] == $key){?> selected <?php } ?>  value = <?php echo $key; ?>  ><?php echo $time_zone; ?></option>
											<?php } ?>
										</select>
									</div>
									<div class="form-group">
										<label>Date Format</label>
										<!--  -->
										<select class="form-control" id="exampleSelect1"   name= "date_format">
														<?php $date_array = array('DD-MM-YYYY', 'MM-DD-YYYY','YYYY-MM-DD');
														foreach ($date_array as $formate) {?>

														<option <?php if($data['date_format'] == $formate){?> selected <?php } ?>  value = <?php echo $formate; ?>  ><?php echo $formate; ?></option>
											<?php } ?>
										</select>
									</div>
									<div class="form-group">
										<label>Date Separator</label>
										<input type="text" class="form-control" name="date_separator" placeholder="Date Separator" value="<?php echo $data['date_separator']; ?>" >
									</div>
									<div class="form-group">
										<label>Percentage Position</label>
										
										<select class="form-control" id="exampleSelect1"   name= "percentage_position">
											<option <?php if($data['percentage_position'] == 'R'){?> selected <?php } ?>  value = 'r'  > Right</option>
											<option <?php if($data['percentage_position'] == 'L'){?> selected <?php } ?>  value = 'r'  > Left</option>
										</select>
									</div>
									<div class="form-group">
										<label>Decimal places</label>
										
										<select class="form-control" id="exampleSelect1"   name= "number_format">
											<?php for($i=1; $i<5; $i++){?>
											<option <?php if($data['number_format'] == $i){?> selected <?php } ?>  value = <?php echo $i; ?>  > 
												<?php echo substr('999.999999', 0,4+$i); ?></option>
											<?php } ?>
										</select>
									</div>
									
									<div class="form-group">
										<label>Invoice PreFix</label>
										<select class="form-control" id="exampleSelect1"   name= "invoice_perfix">
														<?php foreach ($perfixresponse as $key=>$perfix) {?>

														<option <?php if($data['invoice_perfix'] == $perfix['code']){?> selected <?php } ?>  value = <?php echo $perfix['code']; ?>  ><?php echo $perfix['code']; ?></option>
											<?php } ?>
										</select>
									</div>
									<div class="form-group">
										<label>Invoice PostFix</label>
										<input type="text" class="form-control" name="invoice_postfix" placeholder="Invoice PostFix" value="<?php echo $data['invoice_postfix']; ?>" >
									</div>
								</div>
							</div>
						</div>


						<!--end: Form Wizard Step 4.5-->
						<!--begin: Form Wizard Step 5-->
						<div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
							<div class="kt-heading kt-heading--md">Setup Your Payment And Other Information</div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
									<div class="form-group">
										<label>Default Currency</label>
										<div class="col-lg-12">
										<select class="form-control" id="kt_select2_11"   name= "default_currency_id" style="width: 100%">
														<?php foreach ($currencyres as $currenc) {?>

														<option <?php if($data['default_currency_id'] == $currenc['id']){?> selected <?php } ?>  value = <?php echo $currenc['id']; ?>  >
														 <?php echo $currenc['currency']." [ ". $currenc['currency_short_code']." - ". $currenc['Symbol']." ]"; ?> 
													</option>
											<?php } ?>
										</select>
										</div>

									</div>
									<div class="form-group">
										<label>Currency Formate</label>
										<div class="col-lg-12">
											<div class="kt-radio-inline">
													<?php $currency_formate  = array('99,99,99,99,999','99,999,999,999' );
														foreach ($currency_formate as  $cu_formate) {  ?> 
														<label class="kt-radio">
															<input type="radio"  <?php if($data['currency_formate'] == $cu_formate){ ?>checked <?php } ?> name="currency_formate" value='<?php echo $cu_formate; ?>'><?php echo $cu_formate; ?>	
															 <span></span>
														</label>
													<?php } ?>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label>Default Language</label>
										<select class="form-control" id="exampleSelect1"   name= "default_language_id">
														<?php foreach ($languagedata as $lan) {?>

														<option <?php if($data['default_language_id'] == $lan['genc_detail_code']){?> selected <?php } ?>  value = <?php echo $lan['genc_detail_code']; ?>  ><?php echo $lan['description']; ?></option>
											<?php } ?>
										</select>
									</div>
									<div class="form-group">
										<label>Default Tax Rate</label>

										<select class="form-control" id="exampleSelect1"   name= "default_tax_rate">
														<?php foreach ($taxres as $key=>$tax) {?>

														<option <?php if($data['default_tax_rate'] == $tax['id']){?> selected <?php } ?>  value = <?php echo $tax['id']; ?>  ><?php echo $tax['name']; ?></option>
											<?php } ?>
										</select>
										
									</div>
									<div class="form-group">
										<label>Default Payment Mode</label>
										<select class="form-control" id="exampleSelect1"   name= "default_payment_mode">
														<?php foreach ($paydata as $lan) {?>

														<option <?php if($data['default_payment_mode'] == $lan['genc_detail_code']){?> selected <?php } ?>  value = <?php echo $lan['genc_detail_code']; ?>  ><?php echo $lan['description']; ?></option>
											<?php } ?>
										</select>
									</div>	
									<div class="form-group">
										<label>Copy Rights</label>
										<input type="text" class="form-control" name="copy_right" placeholder="Copy Rights" value="<?php echo $data['copy_right']; ?>" >
									</div>					
								</div>
							</div>
						</div>
						<!--end: Form Wizard Step 5-->

						<!--begin: Form Actions -->
						<div class="kt-form__actions">
							<button class="btn btn-secondary btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-prev">
								Previous
							</button>
							<input type="hidden" name="hid" value="<?php echo $rid; ?>" />
							<input type="hidden" name="submit" value="Add" />
							<button class="btn btn-success btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-submit">
								Submit
							</button>
							<button class="btn btn-brand btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-next">
								Next Step
							</button>
						</div>
					</div>
						<!--end: Form Actions -->
					</form>

					<!--end: Form Wizard Form-->
				</div>
			</div>
		</div>
	</div>
</div>