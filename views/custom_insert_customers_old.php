<?php 
$page_details= toGetTableNameBaseApplicationPagesList('insert_page_name', $_GET['option']);

$content = "Help Text"; 
$add_url = $page_details['list_page_name'];
$view_url = $page_details['list_page_name'];
$table_name = $page_details['application_table_name'];

if (isset($_POST['submit'])){
	$data = $_POST;
	$image = imageupload('customer','image',$_POST['hid_image']);
	unset($data['submit']);
	unset($data['submit_value']);
	unset($data['image']);
	unset($data['hid_image']);
	unset($data['hid']);
	$data['company_id'] = $_SESSION['company_id'];
	$data['image'] = $image;
	if($_POST['submit'] == 'Add'){
		$data['Created_Date'] = nowDate();
		$data['Created_By'] = $_SESSION['userid'];
		$data['is_active'] = 0;

		//$urldata = urlencode(json_encode($data,true));
		//echo $url = API."?mode=adddata&table=".TBL_CUSTOMERS."&data=$urldata";
		$url = API."?mode=adddata&table=".TBL_CUSTOMERS."&method=post";
		$response = file_post_contents($url, $data, 'insert');
		//echo $response = callAPI($url);
		if ($_POST['submit_value'] == 'continue'){
			header("location:index.php?option=$add_url&error=3000");
		}else{
			header("location:index.php?option=$view_url&error=3000");
		}
	}
	if($_POST['submit'] == 'Update'){	
		//$_SESSION['check'] = 1;
		$data['Updated_Date'] = nowDate();
		$data['Updated_By'] = $_SESSION['userid'];
		//print_r($data); exit;
		//$urldata = urlencode(json_encode($data,true));
		// $url = API."?mode=updaterowtabledata&rid=".$_POST['hid']."&table=".$table_name."&data=$urldata";
		// $response = callAPI($url);//file_get_contents($url, false, $context);
		// unset($data['save']);
	 //    unset($data['hid']);
	    $url = API."?mode=updaterowtabledata&table=".TBL_CUSTOMERS."&rid=".$_POST['hid']."&method=post";
	    $response = file_post_contents($url, $data, 'update');
		if ($response) {
			header("location:index.php?option=$view_url&error=3010");
		}
	}
}
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ 
	$rid =  encodeId($_GET['Rid']);
	$url = API."?mode=togetdatabasepaimairy&rid=$rid&table=".$table_name;
	//echo $url;
	$response = callAPI($url);//file_get_contents($url, false, $context);
	$role = json_decode($response,1);
}

if($role['image'] !=''){
		$image = "uploads/customer/".$role['image']; 
	}else{
		$image = "uploads/customer/no-image.jpg";
	} 
?>

<div class="flex-column-fluid">

	<div class="container-fluid">
	<?php if($content !=''){ ?>
	<div class="alert alert-warning alert-dismissible">
		<strong><?php echo $content; ?></strong> 
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
	<?php } ?>
		<div class="card card-custom card-sticky" id="kt_page_sticky_card">
			<div class="card">
				<div class="card-header table-card-header">
					<h5><?php echo "Customer";  ?></h5>
					<div class="card-header-right table-card-header ">
						<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>

					</div>
				</div>
<div class="card card-custom">
	<div class="card-body">
		<form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
					<div class="form-group row">
						<div class="col-lg-2" >
							<label class="form-label">Title</label>
							<div class="kt-input-icon">
								<input class="form-control text-capitalize"  type="text" id="title" name="title"  value="<?php echo $role['title']; ?>">	
							</div>
							<div style="display: none;color: red;" id="title_err"> Please enter title </div>
						</div>
						<div class="form-group col-lg-2" >
							<label class="form-label">First Name</label>
							<div class="kt-input-icon">
								<input class="form-control text-capitalize"  type="text" id="first_name" name="first_name"  value="<?php echo $role['first_name']; ?>">
							</div>
							<div style="display: none;color: red;" id="first_name_err"> Please enter first_name </div> 
						</div>
						<div class="form-group col-lg-2">
							<label class="form-label">Middle Name</label>
							<div class="kt-input-icon">
								<input class="form-control text-capitalize" type="text" id="middle_name" name="middle_name"  value="<?php echo $role['middle_name']; ?>">
							</div>
							<div style="display: none;color: red;" id="middle_name_err"> Please enter middle name </div> 
						</div>
						<div class="form-group col-lg-2">
							<label class="form-label">Last Name</label>
							<div class="kt-input-icon">
								<input class="form-control text-capitalize"  type="text" id="last_name" name="last_name"  value="<?php echo $role['last_name']; ?>">
							</div>
							<div style="display: none;color: red;" id="last_name_err"> Please enter last name </div> 
						</div>
						<div class="form-group col-lg-2">
							<label class="form-label">Suffix</label>
							<div class="kt-input-icon">
								<input class="form-control" id="suffix" name="suffix" type="text" value="<?php echo $role['suffix']; ?>">
							</div>
							<div style="display: none;color: red;" id="suffix_err"> Please enter suffix name </div> 
						</div>
						<div class="form-group col-lg-2">
							<label class="form-label">Company</label>
							<div class="kt-input-icon">
								<input class="form-control text-capitalize"  type="text" id="company" name="company"  value="<?php echo $role['company']; ?>">	
							</div>
							<div style="display: none;color: red;" id="company_err"> Please enter company </div>  
						</div>
						<div class="form-group col-lg-2">
							<label class="form-label">Display Name </label>
							<div class="kt-input-icon">
								<input class="form-control text-capitalize"  type="text" id="display_name" name="display_name"  value="<?php echo $role['display_name']; ?>">	
							</div>
							<div style="display: none;color: red;" id="display_name_err"> Please enter display name </div> 
						</div>
						<div class="form-group col-lg-2">
							<label class="form-label">Email</label>
							<div class="kt-input-icon">
								<input class="form-control" type="text" id="email" name="email"  value="<?php echo $role['email']; ?>">
							</div>
							<div style="display: none;color: red;" id="email_err"> Please enter email </div> 
						</div>
						<div class="form-group col-lg-2">
								<label class="form-label">Phone</label>
								<div class="kt-input-icon">
								<input class="form-control"  type="text" id="phone" name="phone"  value="<?php echo $role['phone']; ?>">	
							</div>
							<div style="display: none;color: red;" id="phone_err"> Please enter phone </div>  
						</div>
						<div class="form-group col-lg-6 row">
							<div class="col-lg-6 p-r-5" >
								<label class="form-label">GST  Registration type</label>
									<input class="form-control"  type="text" id="gsttype" name="gsttype"  value="<?php echo $role['gsttype']; ?>">	
								<div style="display: none;color: red;" id="gsttype"> Please enter GST Registration type</div> 
							</div>
							<div class="form-group col-lg-6" >
								<label class="form-label">GSTIN</label>
									<input class="form-control"  type="text" id="gstin" name="gstin"  value="<?php echo $role['gstin']; ?>">
								<div style="display: none;color: red;" id="gstin_err"> Please enter GST IN </div> 
							</div>
						</div>
						<div class="form-group col-lg-2">
							<label class="form-label">Parent Customer</label>
							<div class="kt-input-icon">
								<input class="form-control" id="parent_customer" name="parent_customer"  value="<?php echo $role['parent_customer']; ?>">
							</div>
							<div style="display: none;color: red;" id="email_err"> Please enter parent_customer </div> 
						</div>
						<div class="form-group col-lg-2">
							<label class="form-label">Bill With Parent</label>
							<div class="kt-input-icon">
								<input class="form-control"  type="text" id="bill_with_parent" name="bill_with_parent"  value="<?php echo $role['bill_with_parent']; ?>">	
							</div>
							<div style="display: none;color: red;" id="company_err"> Please enter bill_with_parent </div>  
						</div>
						<div class="form-group col-lg-2">
							<label class="form-label">Language</label>
							<div class="kt-input-icon">
								<input class="form-control"  type="text" id="bill_with_parent" name="bill_with_parent"  value="<?php echo $role['bill_with_parent']; ?>">	
							</div>
							<div style="display: none;color: red;" id="company_err"> Please enter bill_with_parent </div>  
						</div>
						<div class="col-lg-3" >
							<label class="form-label">Hold Reason</label>
							<div class="kt-input-icon">
								<input class="form-control"  type="text" id="hold_reason" name="hold_reason"  value="<?php echo $role['hold_reason']; ?>">
							</div>
						</div>
						<div class="col-lg-3">
							<label class="form-label">Profile Pc</label>
		                    <div class="profile-pic">
	                            <label class="-label" for="image">
	                              <span><i class="fa fa-camera"></i> &nbsp; Change Image</span>
	                            </label>
	                            <input type="file" name="image" id="image" accept=".png, .jpg, .jpeg"  onchange="loadFile(event,this.id)">
	                            <input type="hidden" name="image" value="<?php echo $role[$image]; ?>">
	                            <img src="<?php echo $image ; ?>" id="outputimage" width="200" onerror="this.src='uploads/all/no-image.jpg';" />
	                        </div>
		                </div>	
						<div class="form-group col-lg-10">
							<div class="kt-portlet">
								<div class="kt-portlet__body">
									<ul class="nav  md-tabs infisuite_tab" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" data-bs-toggle="tab" href="#kt_tabs_1_1" data-target="#kt_tabs_1_1">Address</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#kt_tabs_1_2" data-target="#kt_tabs_1_2">Note</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#kt_tabs_1_3">Tax Info</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#kt_tabs_1_4">Payment and Billing</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#kt_tabs_1_6">Sale / Discount</a>
										</li>
										<!-- <li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#kt_tabs_1_7">Ediformate</a>
										</li> -->
										<!-- <li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#kt_tabs_1_5">Attachments</a>
										</li> -->

									</ul>
									<div class="tab-content mt-5">
										<div class="tab-pane active" id="kt_tabs_1_1" role="tabpanel">
											<div class="from row">
												<div class="col-lg-6">
													<div class="row">
														<div class="form-group  col-lg-12" >
															<label class="form-label">Billing Adderss</label>
															<div class="kt-input-icon">
																<textarea type="text" class="form-control" placeholder="Billing Adderss" name="billing_address" ><?php echo $role['billing_address'];?></textarea>
															</div> 
														</div>
														<div class="form-group col-lg-6" >
																<label class="form-label">City/Town</label>
																<input class="form-control" placeholder="City/Town"  type="text" id="billing_city" name="billing_city"  value="<?php echo $role['billing_city']; ?>">
															
														</div>
														<div class="form-group col-lg-6" >
																<label class="form-label">State</label>
																<input class="form-control" placeholder="State"  type="text" id="billing_state" name="billing_state"  value="<?php echo $role['billing_state']; ?>">
															
														</div>
														<div class="form-group col-lg-6" >
																<label class="form-label">Pincode</label>
																<input class="form-control" placeholder="Pincode"  type="text" id="billing_post_code" name="billing_post_code"  value="<?php echo $role['billing_post_code']; ?>">
															
														</div>
														<div class="form-group col-lg-6" >
																<label class="form-label">Country</label>
																<input class="form-control" placeholder="Country"  type="text" id="" name="billing_country" id="billing_country" value="<?php echo $role['billing_country']; ?>">
															
														</div>
														<div class="form-group col-lg-6">
																<label class="form-label">Contact Person</label>
																<input class="form-control" placeholder="Contact Person"  type="text" id="" name="contact_person" id="contact_person" value="<?php echo $role['contact_person']; ?>">
															
														</div>
														<div class="form-group col-lg-6" >
															<label class="form-label">Mobile</label>
															<div class="kt-input-icon">
																<input class="form-control"  type="text" id="mobile" name="mobile"  value="<?php echo $role['mobile']; ?>">	
															</div>
															<div style="display: none;color: red;" id="mobile_err"> Please enter mobile </div>  
														</div>
														<div class="form-group col-lg-6" >
															<label class="form-label">Fax</label>
															<div class="kt-input-icon">
																<input class="form-control"  type="text" id="fax" name="fax"  value="<?php echo $role['fax']; ?>">
															</div>
															<div style="display: none;color: red;" id="fax_err"> Please enter Fax </div>  
														</div>
														<div class="form-group col-lg-6" >
															<label class="form-label">Others</label>
															<div class="kt-input-icon">
																<input class="form-control" type="text" id="other" name="other"  value="<?php echo $role['other']; ?>">	
															</div>
															<div style="display: none;color: red;" id="first_name_err"> Please enter first_name </div> 
														</div>
														<div class="form-group col-lg-6" >
															<label class="form-label">Website</label>
															<div class="kt-input-icon">
																<input class="form-control"  type="text" id="website_url" name="website_url"  value="<?php echo $role['website_url']; ?>">
															</div>
															<div style="display: none;color: red;" id="fax_err"> Please enter Fax </div>  
														</div>
													</div>
												</div>	
												
												<div class="col-lg-6" >
													<div class="row">
														<div class="form-group col-lg-12">
															<label class="form-label">Shipping Adderss</label>
															<textarea type="text" class="form-control" placeholder="Street" name="shipping_address"  ><?php echo $role['shipping_address'];?></textarea>
														</div>
														<div class="form-group col-lg-6" >
																<label class="form-label">City/Town</label>
																<input class="form-control" placeholder="City/Town"  type="text" id="shipping_city" name="shipping_city"  value="<?php echo $role['shipping_city']; ?>">
														</div>
														<div class="form-group col-lg-6">
																<label class="form-label">State</label>
																<input class="form-control" placeholder="State"  type="text" id="shipping_state" name="shipping_state"  value="<?php echo $role['shipping_state']; ?>">
														</div>
														<div class="form-group col-lg-6">
																<label class="form-label">Pincode</label>
																<input class="form-control" placeholder="Pincode"  type="text" id="shipping_post_code" name="shipping_post_code"  value="<?php echo $role['shipping_post_code']; ?>">
														</div>
														<div class="form-group col-lg-6">
																<label class="form-label">Country</label>
																<input class="form-control" placeholder="Country"  type="text" id="shipping_country" name="shipping_country"  value="<?php echo $role['shipping_country']; ?>">
														</div>
													</div>
												</div>


											</div>
										</div>
										<div class="tab-pane" id="kt_tabs_1_2" role="tabpanel">
											<div class="row">
												<div class="col-lg-6">
													<label class="form-label">Customer Statement Notes</label>
													<textarea type="text" name="customer_stmnt_notes"  class="form-control" style="height: 150px;" placeholder="Enter your Customer Statement Notes"><?php echo $role['customer_stmnt_notes']; ?></textarea>
												</div>
												<div class="col-lg-6">
													<label class="form-label">Customer Invoice Notes</label>
													<textarea type="text" name="customer_inv_notes" class="form-control" style="height: 150px;" placeholder="Enter your Customer Invoice Notes"><?php echo $role['customer_inv_notes']; ?></textarea>
												</div>
											</div>
										</div>
										<div class="tab-pane" id="kt_tabs_1_3" role="tabpanel">
											<div class="from row">
												<div class="form-group col-lg-6">
													<label class="form-label">Tax Reg.No.</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="customer_tax_reg_no" name="customer_tax_reg_no"  value="<?php echo $role['customer_tax_reg_no']; ?>">
													</div> 
												</div>
												<div class="form-group col-lg-6" >
													<label class="form-label">CST Reg.No</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="cst_reg_no" name="cst_reg_no"  value="<?php echo $role['cst_reg_no']; ?>">
													</div> 
												</div>
												<div class=" form-group col-lg-6" >
													<label class="form-label">PAN No.</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="pan_no" name="pan_no"  value="<?php echo $role['pan_no']; ?>">
													</div> 
												</div>
												<div class="form-group col-lg-6">
													<div class="checkbox-fade fade-in-primary mt-4">
				                                      <label class="form-label">
				                                        <input type="checkbox" name="tds_apply" value="1" <?php echo $role['tds_apply'] ==1?"checked":"";?>>
				                                        <span class="cr">
				                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
				                                        </span>
				                                        <span>Apply TDS for this customer</span>
				                                      </label>
				                                    </div>
												</div>
											</div>
										</div>
										<div class="tab-pane" id="kt_tabs_1_4" role="tabpanel">
											<div class="from row">
												<div class="form-group col-lg-6" >
													<label class="form-label">Preferred Payment Method</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="pref_payment_method" name="pref_payment_method"  value="<?php echo $role['pref_payment_method']; ?>">
													</div> 
												</div>
												<div class="form-group col-lg-6" >
													<label class="form-label">Trems</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text"  id="payment_terms" name="payment_terms"  value="<?php echo $role['payment_terms']; ?>">
													</div> 
												</div>
												<div class="form-group col-lg-6" >
													<label class="form-label">Preferred Delivery Method</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="preferred_deliver_method" name="preferred_deliver_method"  value="<?php echo $role['preferred_deliver_method']; ?>">
													</div> 
												</div>
												<div class="form-group col-lg-4" >
													<label class="form-label">Opening Blance</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="opening_balance" name="opening_balance"  value="<?php echo $role['opening_balance']; ?>">
													</div>
												</div>
												<div class="form-group col-lg-2" >
													<label class="form-label">As of</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="asof" name="asof"  value="<?php echo $role['asof']; ?>">
													</div>
												</div>
												<div class="form-group col-lg-6" >
													<label class="form-label">The Customer Pays Me With</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="pay_with_me" name="pay_with_me"  value="<?php echo $role['pay_with_me']; ?>">
													</div>
												</div>
												<div class="form-group col-lg-4" >
													<label class="form-label">Credi tLimit</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="credit_limit" name="credit_limit"  value="<?php echo $role['credit_limit']; ?>">
													</div>
												</div>
												
											</div>
										</div>
										<div class="tab-pane" id="kt_tabs_1_5" role="tabpanel">
											<div class="form-group row">
												<div class="col-lg-4 col-md-9 col-sm-12">
													<div class="dropzone dropzone-default dropzone-brand" id="kt_dropzone_2">
														<div class="dropzone-msg dz-message needsclick">
															<h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
															<span class="dropzone-msg-desc">Upload up to 10 files</span>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane" id="kt_tabs_1_6" role="tabpanel">
											<div class="form-group row">
												<div class="col-lg-6" style="margin-bottom: 2rem;">
													<label class="form-label">Default Discount Percentage</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="default_discount_perc" name="default_discount_perc"  value="<?php echo $role['default_discount_perc']; ?>">
													</div> 
												</div>
												<div class="col-lg-6" style="margin-bottom: 2rem;">
													<label class="form-label">Payment Discount Percentagec</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text"  id="payment_discount_perc" name="payment_discount_perc"  value="<?php echo $role['payment_discount_perc']; ?>">
													</div> 
												</div>
												<div class="col-lg-6" style="margin-bottom: 2rem;">
													<label class="form-label">Sales Type</label>
													<div class="kt-input-icon">
														<input class="form-control"   type="text" id="sales_type" name="sales_type"  value="<?php echo $role['sales_type']; ?>">
													</div>
												</div>
											</div>
										</div>
										<!-- <div class="tab-pane" id="kt_tabs_1_7" role="tabpanel">
											<div class="form-group row">
												<div class="col-lg-4 col-md-9 col-sm-12">
													<div class="dropzone dropzone-default dropzone-brand" id="kt_dropzone_2">
														<div class="dropzone-msg dz-message needsclick">
															<h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
															<span class="dropzone-msg-desc">Upload up to 10 files</span>
														</div>
													</div>
												</div>
											</div>
										</div> -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<div class="kt-form__actions">
						<div class="row">
							<div class="col-lg-6">
								<?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
										<input type="hidden" name="hid" value="<?php echo  $role['id']; ?>" />
										<input type="hidden" name="submit" value="Update" />
										<button id="kt_llogin_signup_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" ><i class="la la-check"></i>Upadate</button>
										<button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
								<?php } else{ ?>
										<input type="hidden" name="submit" value="Add" />
										<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
										<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
										<button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
								<?php } ?>	
							</div>
							<div class="col-lg-6 kt-align-right">
								<button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
							</div> 
						</div>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
