<?php 
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['dropzone'] = 1;
$page_details= toGetTableNameBaseApplicationPagesList('insert_page_name', $_GET['option']);
//print_r($page_details);
$File_Uploads_Path = "uploads/all/";
$content = "Help Text"; 
$add_url = $page_details['list_page_name'];
$view_url = $page_details['list_page_name'];
$table_name = $page_details['application_table_name'];
$table_name = "vendors";
//echo $table_name; exit;
if (isset($_POST['submit'])){
	$data = $_POST;
	$data_agreements = $_POST;
	$image = imageupload('customer','image',$_POST['hid_image']);
	//echo "<pre>".$image; print_r($_FILES); exit;
	unset($data['submit']);
	unset($data['submit_value']);
	unset($data['image']);
	unset($data['hid_image']);
	unset($data['hid']);
	unset($data['ag_type']);
	unset($data['ag_number']);
	unset($data['file_url']);
	unset($data['ag_ex_date']);
	unset($data['ag_sin_date']);
	unset($data['ag_rowid']);
	unset($data['ag_rowid']);
	unset($data['hidden_agreement_file']);
	$data['hiring_clause'] = $data['hiring_clause'] =='on'?'0':'1';
	$data['tds_apply'] = $data['tds_apply'] =='on'?'0':'1';
	
	$data['asof'] = dateconvertdbformat($data['asof']);
	$data['image'] = $image;
	if($_POST['submit'] == 'Add'){
		$data['created_date'] = nowDate();
		$data['created_by'] = $_SESSION['userid'];
		$data['company_id'] = $_SESSION['company_id'];
		$data['is_active'] = 0;
		$data['current_status'] = 0;
		$url = API."?mode=adddata&table=".$table_name."&method=post";
		//print_r($data); exit;
		//echo $url; exit;
		$response = file_post_contents($url, $data, 'insert');
		$postdata['vendor_id'] = $response['LASTID'];
		$postdata['company_id'] = $data['company_id'];
		$postdata['is_active'] = '0';
		$postdata['current_status'] = '0';
		//print_r($postdata);  exit;

		$count = count((array)$_FILES["agreement_file"]["name"]);
		for($f=0; $f<$count; $f++){
			$temp = explode(".", basename($_FILES["agreement_file"]["name"][$f]));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$data_agreements['file_url'][$f] = $newfilename;
			move_uploaded_file($_FILES["agreement_file"]["tmp_name"][$f], $File_Uploads_Path.$newfilename);
		}
		foreach ($data_agreements['ag_type'] as $agkey => $ag) { 
			$postdata['agreement_name'] = $data_agreements['ag_type'][$agkey];
			$postdata['agreement_ref_no'] = $data_agreements['ag_number'][$agkey];
			$postdata['signed_date'] = date('Y-m-d', strtotime($data_agreements['ag_sin_date'][$agkey]));

			$postdata['expiry_date'] = date('Y-m-d', strtotime($data_agreements['ag_ex_date'][$agkey]));
			$postdata['file_url'] = $data_agreements['file_url'][$agkey];
			if(!empty($data_agreements['ag_rowid'][$agkey]))
			{
	    		// update
				$row_id = $data_agreements['ag_rowid'][$agkey];
				$postdata['updated_date'] = $data['created_date'];
				$postdata['updated_by'] = $data['created_by'];
				$url = API."?mode=updaterowtabledata&rid=".$row_id."&table=".TBL_VENDOR_AGREEMENTS."&method=post";
				$response = file_post_contents($url, $postdata, 'update');
			}else {
	    		//insert
				//echo "<pre>";print_r($postdata);
				$postdata['created_date'] = $data['created_date'];
				$postdata['created_by'] = $data['created_by'];
				$url = API."?mode=adddata&table=".TBL_VENDOR_AGREEMENTS."&method=post";
				$response = file_post_contents($url, $postdata, 'insert');
			}

		}

		/*$urldata = urlencode(json_encode($data,true));
		$url = API."?mode=adddata&table=".$table_name."&data=$urldata";
		$response = callAPI($url);//file_get_contents($url, false, $context);*/
		if ($_POST['submit_value'] == 'continue'){
			//header("location:index.php?option=$add_url&error=3000");
		}else{
			//header("location:index.php?option=$view_url&error=3000");
		}
	}
	if($_POST['submit'] == 'Update'){	
		//$_SESSION['check'] = 1;
		$data_agreements = $_POST;
		$data_agreements['created_date'] = nowDate();
		$data_agreements['created_by'] = $_SESSION['userid'];
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$vendor_id1 = $_POST['vendor_id'];
		unset($data['vendor_id']);
		$data_agreements['vendor_id'] = $vendor_id1;
		//print_r($_POST); exit;
		// print_r($_FILES["agreement_file"]["name"]);
		
		$url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=".$table_name."&method=post";
		$response = file_post_contents($url, $data, 'update');
		$postdata['company_id'] = $_SESSION['company_id'];
		$postdata['is_active'] = '0';
		$postdata['current_status'] = '0';
		$postdata['vendor_id'] = $vendor_id1;

		$count = count((array)$_FILES["agreement_file"]["name"]);
		for($f=0; $f<$count; $f++) {
			$temp = explode(".", basename($_FILES["agreement_file"]["name"][$f]));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$data_agreements['file_url'][$f] = $newfilename;
			move_uploaded_file($_FILES["agreement_file"]["tmp_name"][$f], $File_Uploads_Path.$newfilename);
		}
		//print_r($postdata['file_url']);

		foreach ($data_agreements['ag_type'] as $agkey => $ag) { 
			$postdata['agreement_name'] = $data_agreements['ag_type'][$agkey];
			$postdata['agreement_ref_no'] = $data_agreements['ag_number'][$agkey];
			$postdata['signed_date'] = date('Y-m-d', strtotime($data_agreements['ag_sin_date'][$agkey]));
			$postdata['expiry_date'] = date('Y-m-d', strtotime($data_agreements['ag_ex_date'][$agkey]));
			$postdata['file_url'] = $data_agreements['file_url'][$agkey];
			
			if(!empty($data_agreements['ag_rowid'][$agkey]))
			{
	    		// update
				$row_id = $data_agreements['ag_rowid'][$agkey];
				$postdata['updated_date'] = $data_agreements['created_date'];
				$postdata['updated_by'] = $data_agreements['created_by'];
			//	print_r($postdata);
				$url = API."?mode=updaterowtabledata&rid=".$row_id."&table=".TBL_VENDOR_AGREEMENTS."&method=post";
				$response = file_post_contents($url, $postdata, 'update');
			}else {
	    		//insert
				//echo "<pre>";print_r($postdata);
				$postdata['created_date'] = $data['created_date'];
				$postdata['created_by'] = $data['created_by'];
				$url = API."?mode=adddata&table=".TBL_VENDOR_AGREEMENTS."&method=post";
				$response = file_post_contents($url, $postdata, 'insert');
			}

		}
		//exit;
		if ($response) {
			  //header("location:index.php?option=$view_url");
		}
	}
}
//if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ 
	//$rid =  encodeId($_GET['Rid']);
if($_GET['Rid'] !=""){
	$rid1 =$_GET['Rid'];
}else{
	$rid1 = deocdeId('id:'.$_SESSION['vendor_id']);
}
$rid =  encodeId($rid1);
$rid = urldecode($rid);
$vendorid = explode(":", $rid);
$vendor_id = $vendorid['1'];
/**
$url = API."?mode=togetdatabasepaimairy&rid=$rid&table=".$table_name;
	$response = callAPI($url);//file_get_contents($url, false, $context);
	$role = json_decode($response,1);

**/

	//print_r($role);
	//$url = API."?mode=togetdatabasepaimairy&rid=$rid&table=vendor_agreements";
	//echo $url;
/**
	$data11["where"] = array('vendor_id'=> $vendor_id[1]);
	$datw = urlencode(json_encode($data11));
	$curl1 = API."?mode=gettabledetailswithconditions&table=vendor_agreements&data=".$datw; 
	$cudata1 = callAPI($curl1);
	$aggrements = json_decode($cudata1,1);
	**/
	//print_r($role); exit;
//}	

//echo "<pre>"; print_r($response); echo "</pre>"; 

	$url = API."?mode=getvendorsdetails&id=".$vendor_id;
	$response = callAPI($url);//file_get_contents($url, false, $context);
	$result = json_decode($response,1);
	$role = $result['vendors'];
	$aggrements =$result['vendor_agreements'];
	$payment_method =$result['payment_method'];
	$salestype =$result['salestype'];
	$emplist = $result['employess'];
	$countrieslist = $result['countries'];
	$states = $result['states'];
	$cities = $result['cities'];
	$vendorslist = $result['vendorslist'];
	$paymenttype = $result['paymenttype'];
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


		



	if($role['image'] !=''){
		$image = $File_Uploads_Path.$role['image']; 
	}else{
		$image = $File_Uploads_Path."no-image.jpg";
	} 
	?>
	<script type="text/javascript">
		function addedurows(){
			var date_format = "yy-mm-dd";
			var count = $(".itemRow").length;
			count = count + 1;
			jQuery(document).ready(function() {    
				KTBootstrapDatepicker.init();
			});
			var htmlRows = '';
			htmlRows += '<tr class="">'; 

			htmlRows += '<td>'+count+'</td>'; 
			htmlRows += '<td><input type="text" name="ag_type[]"  class="form-control rowcount itemRow " autocomplete="off">  </td>'; 
			htmlRows += '<td><input type="text" name="ag_number[]"  class="form-control " autocomplete="off"> </td>'; 
			htmlRows += '<td><input type="text" name="ag_sin_date[]"  class="form-control datepicker" id="kt_datepicker_35" autocomplete="off">  </td>'; 
			htmlRows += '<td><input type="text" name="ag_ex_date[]"  class="form-control datepicker"  id="kt_datepicker_35" autocomplete="off">  </td>'; 

       //htmlRows += '<td><input type="text" name="score[]"  class="form-control " autocomplete="off">  </td>';   
       htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5" ><label class="btn-hover-text-primary mt-2" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" > <i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i></div></td>'

       htmlRows += '</tr>';
	//datepickercall();
	
	$('#edutable').append(htmlRows);
	$( ".datepicker" ).datepicker({
		showOn: "both",
		buttonText: "<i class='ti-calendar'></i>",
		dateFormat: date_format,
		changeMonth: true,
		changeYear: true
	});
}
function selectedcategory(selected_id,element_id,table_name,field_name,targetelement) {
  var selectedvalues = selected_id;
                //alert(selected_id);

                if(table_name=='states' || table_name=='cities'){
                 $.ajax({
                  url: "ajax.php",
                  data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalues+'&field='+field_name,
                  type: "POST",
                  success: function(response) {
                    var response = JSON.parse(response);
                    console.log(response.length);
                    console.log(response);
                    var select = document.getElementById(element_id); 
                    document.getElementById(element_id).innerHTML = "";
                    for (var i = 0; i < response.length; i++) 
                    {
                     var el = document.createElement("option");
                     if(table_name=='states'){
                      el.textContent = response[i].state_name;
                      el.value = response[i].id;

                    }
                    if(table_name=='cities'){
                      el.textContent = response[i].city_name;
                      el.value = response[i].id;

                    }
                    select.add(el);
                  }
                }
              });
               }

             }
</script>
<script>
	function checkboxclick(){
	//$('input[name="hiring_cluse"]').click(function() { 
		if ($('input[name="hiring_clause"]').is(':checked')) {
			$(".coolingmonth").show();
		} else {
			$(".coolingmonth").hide();
		}
	}
</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<?php if($content !=''){ ?>
			<div class="alert alert-warning alert-dismissible">
				<strong><?php echo $content; ?></strong> 
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php } ?>
		<form class="" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
			<div class="card card-custom card-sticky">
				<div class="cover-profile" style="position: relative;">
					<div class="m-b-25">
						<img class="m-b-25 img-fluid" src="assets/images/user-profile/bg-img1.jpg" alt="bg-img" >
						<div class="card-block user-info d-flex justify-content-center">
							<div class="col-md-11">
								<div class="media-left">
									<a href="#" class="profile-image">
										<img src="<?php echo $image; ?>" class=" img-120 user-img b-radius-10" alt="User-Profile-Image" onerror="this.src='uploads/all/no-image.jpg';">
									</a>
								</div>
								<div class="media-body row">
									<div class="col-lg-12">
										<div class="user-title">
											<h4 class="text-white"><?php echo $role['display_name']; ?></h4>
											<span class="text-white"><?php echo $role['email']; ?></span>
										</div>
										<?php if($role['hiring_clause']==0){?> 
											<div>No Hiring Clause Applicable, Cooling off Period of <?php echo $role['cooling_months'];?> Months </div>
										<?php }else{ ?>
											<div>No Hiring Clause Not Applicable	</div>
										<?php } ?>
									</div>
									<div>
										<div class="float-right cover-btn">
											<input type="hidden" name="vendor_id" value=<?php echo $vendor_id; ?> />

											<input type="hidden" name="submit" value="Update" />
											<button  class="btn btn-primary m-r-20 m-b-20"><i class="icofont icofont-plus"></i>
											Update</button>
											<button type="button" class="btn btn-primary m-b-20"><i class="icofont icofont-ui-messaging"></i>
											Message</button>
											<input type="hidden" name="hid" value=<?php echo $rid; ?> /> 
											<input type="hidden" name="submit" value="Update" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row m-b-30 mt-5 card-body">
					<div class="col-lg-12 col-xl-12">
						<ul class="nav md-tabs infisuite_tab mt-2 ml-2" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-bs-toggle="tab" href="#overview" role="tab">Overview</a>
								<div class="slide"></div>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#basic-info" role="tab">Basic Info</a>
								<div class="slide"></div>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#contact-details" role="tab">Contact Details</a>
								<div class="slide"></div>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#note" role="tab">Note</a>
								<div class="slide"></div>
							</li>
							<li class="nav-item">
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
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#agreements" role="tab">Agreements</a>
								<div class="slide"></div>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#attachments" role="tab">Attachments</a>
								<div class="slide"></div>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#employees" role="tab">Employees</a>
								<div class="slide"></div>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#no-hiring" role="tab">No hiring clause</a>
								<div class="slide"></div>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content card-block">
							<div class="tab-pane active" id="overview" role="tabpanel">
								<div class="row">   
									<div class="col-lg-12 col-xl-6">
										<div class="table-responsive">
											<div class="card-header"><h5 class="text-primary">Basic Info</h5></div>
											<table class="table m-0">
												<tbody>
													<tr>
														<th scope="row">Title</th>
														<td><?php echo emptytextdashShow($role['title']); ?></td>
													</tr>
													<tr>
														<th scope="row">Name</th>
														<td><?php echo $role['first_name'].$role['middle_name']." ".$role['last_name']; ?></td>
													</tr>
													<tr>
														<th scope="row">Contact</th>
														<td><?php echo $role['mobile']; ?></td>
													</tr>
													<tr>
														<th scope="row">Email</th>
														<td><?php echo emptytextdashShow($role['email']); ?></td>
													</tr>
													<tr>
														<th scope="row">Phone</th>
														<td><?php echo emptytextdashShow($role['phone']); ?></td>
													</tr>

												</tbody>
											</table>
										</div>
									</div>
									<div class="col-lg-12 col-xl-6">
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="card-header"><h5 class="text-primary">Agreements</h5></div>
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
										<div class="col-lg-12 col-xl-6">
											<div class="table-responsive">
												<div class="card-header"><h5 class="text-primary">Shipping Details</h5></div>
												<table class="table m-0">
													<tbody>
														<tr>
															<th scope="row">Shipping  Adderss</th>
															<td class="address-basic"><?php if($role['shipping_address']!=""){ echo trim($role['shipping_address']).",".$role['shipping_city']; } ?></td>
														</tr>
														<tr>
															<th scope="row">Country</th>
															<td class="address-basic"><?php echo $countrylist[$role['shipping_country']]; ?></td>
														</tr>
														<tr>
															<th scope="row">State</th>
															<td class="address-basic"><?php echo $stateslist[$role['shipping_state']]; ?></td>
														</tr>
														<tr>
															<th scope="row">City</th>
															<td class="address-basic"><?php echo $citieslist[$role['shipping_city']]; ?></td>
														</tr>
														
														<tr>
															<th scope="row">Pincode</th>
															<td><?php echo $role['shipping_post_code']; ?></td>
														</tr>                                 
													</tbody>
												</table>
											</div>
										</div>
										<div class="col-lg-12 col-xl-6">
											<div class="table-responsive">
												<div class="card-header"><h5 class="text-primary">Tax Info</h5></div>
												<table class="table m-0">
													<tbody>
														<tr>
															<th scope="row">GST Registration type</th>
															<td><?php echo $role['gsttype']; ?></td>
														</tr>
														<tr>
															<th scope="row">GSTIN</th>
															<td><?php echo $role['gstin']; ?></td>
														</tr>
														<tr>
															<th scope="row">Tax Reg.No.</th>
															<td><?php echo $role['vendor_tax_reg_no']; ?></td>
														</tr>
														<tr>
															<th scope="row">CST Reg.No</th>
															<td><?php echo $role['cst_reg_no']; ?></td>
														</tr>
														<tr>
															<th scope="row">PAN</th>
															<td><?php echo $role['pan_no']; ?></td>
														</tr>                                 
													</tbody>
												</table>
											</div>
										</div>
										<div class="col-lg-12 col-xl-6">
											<div class="table-responsive">
												<div class="card-header"><h5 class="text-primary">Billing Adderss</h5></div>
												<table class="table m-0">
													<tbody>
														<tr>
															<th scope="row">Billing Adderss</th>
															<td class="address-basic"><?php echo $role['billing_address'].",".$role['billing_city']; ?></td>
														</tr>
														<tr>
															<th scope="row">Country</th>
															<td class="address-basic"><?php echo $countrylist[$role['billing_country']]; ?></td>
														</tr>
														<tr>
															<th scope="row">State</th>
															<td class="address-basic"><?php echo $stateslist[$role['billing_state']]; ?></td>
														</tr>
														<tr>
															<th scope="row">City</th>
															<td class="address-basic"><?php echo $citieslist[$role['billing_city']]; ?></td>
														</tr>
														<tr>
															<th scope="row">Pincode</th>
															<td><?php echo $role['billing_post_code']; ?></td>
														</tr>
														<tr>
															<th scope="row">Contact Person</th>
															<td><?php echo $role['contact_person']; ?></td> 
														</tr>
														<tr>
															<th scope="row">Contact Person Mobile</th>
															<td><?php echo $role['mobile']; ?></td>
														</tr>

													</tbody>
												</table>
											</div>
										</div>
										<div class="col-lg-12 col-xl-6">
											<div class="table-responsive">
												<div class="card-header"><h5 class="text-primary">Payment & Billing</h5></div>
												<table class="table m-0">
													<tbody>
														<tr>
															<th scope="row">Preferred Payment Method</th>
															<td><?php echo $role['pref_payment_method']; ?></td>
														</tr>
														<tr>
															<th scope="row">Payment Trems</th>
															<td><?php echo $role['payment_terms']; ?></td>
														</tr>
														<tr>
															<th scope="row">Preferred Delivery Method</th>
															<td><?php echo $role['preferred_deliver_method']; ?></td>
														</tr>
														<tr>
															<th scope="row">Opening Blance</th>
															<td><?php echo $role['opening_balance']; ?></td>
														</tr>
														<tr>
															<th scope="row">The Customer Pays Me With</th>
															<td><?php echo $role['pay_with_me']; ?></td>
														</tr>                                 
														<tr>
															<th scope="row">Credit Limit</th>
															<td><?php echo $role['credit_limit']; ?></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
										<div class="col-lg-12 col-xl-6">
											<div class="table-responsive">
												<div class="card-header"><h5 class="text-primary">Sale & Discount</h5></div>
												<table class="table m-0">
													<tbody>
														<tr>
															<th scope="row">Default Discount Percentage</th>
															<td><?php echo $role['default_discount_perc']; ?></td>
														</tr>
														<tr>
															<th scope="row">Payment Discount Percentage</th>
															<td><?php echo $role['payment_discount_perc']; ?></td>
														</tr>
														<tr>
															<th scope="row">Sales Type</th>
															<td><?php echo $role['sales_type']; ?></td>
														</tr>                              
													</tbody>
												</table>
											</div>
										</div>

									</div>
								</div>
								<div class="tab-pane" id="basic-info" role="tabpanel">
									<div class="row">
										<div class="form-group row">
											<div class="col-lg-3 p-r-5" style="float: left;">
												<label class="form-label">Title</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="title" name="title" value="<?php echo $role['title']; ?>">	
												</div>
												<div style="display: none;color: red;" id="title_err"> Please enter title </div> 
											</div>
											<div class="form-group col-lg-3 p-r-5" style="float: left;">
												<label class="form-label">First Name</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="first_name" name="first_name" value="<?php echo $role['first_name']; ?>" >
												</div>
												<div style="display: none;color: red;" id="first_name_err"> Please enter first_name </div> 
											</div>
											<div class="form-group col-lg-3 p-r-5" style="float: left;">
												<label class="form-label">Middle Name</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="middle_name" name="middle_name" value="<?php echo $role['middle_name']; ?>" >
												</div>
												<div style="display: none;color: red;" id="middle_name_err"> Please enter middle name </div> 
											</div>
											<div class="form-group col-lg-3 p-r-5" style="float: left;">
												<label class="form-label">Last Name</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="last_name" name="last_name" value="<?php echo $role['last_name']; ?>" >
												</div>
												<div style="display: none;color: red;" id="last_name_err"> Please enter last name </div> 
											</div>
											<div class="form-group col-lg-3">
												<label class="form-label">Email</label>
												<div class="kt-input-icon">
													<input class="form-control" id="email" name="email" type="text" value="<?php echo $role['email']; ?>" >
												</div>
												<div style="display: none;color: red;" id="email_err"> Please enter email </div> 
											</div>
											<div class="form-group col-lg-3">
												<label class="form-label">Phone</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="phone" name="phone" value="<?php echo $role['phone']; ?>" >	
												</div>
												<div style="display: none;color: red;" id="phone_err"> Please enter phone </div>  
											</div>
											<div class="form-group col-lg-3 p-r-5" style="float: left;">
												<label class="form-label">Suffix</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="suffix" name="suffix" value="<?php echo $role['suffix']; ?>" >
												</div>
												<div style="display: none;color: red;" id="suffix_err"> Please enter suffix name </div> 
											</div>
											<div class="form-group col-lg-3 ">
												<label class="form-label">Company</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="company" name="company" value="<?php echo $role['company']; ?>" >	
												</div>
												<div style="display: none;color: red;" id="company_err"> Please enter company </div>  
											</div>
											<div class="form-group col-lg-3 ">
												<label class="form-label">Display Name As</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="display_name" name="display_name" value="<?php echo $role['display_name']; ?>" >	
												</div>
												<div style="display: none;color: red;" id="display_name_err"> Please enter display name </div>
											</div>
											<div class="col-lg-3 p-r-5" style="float: left;">
												<label class="form-label">GST  Registration type</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="gsttype" name="gsttype" value="<?php echo $role['gsttype']; ?>" >	
												</div>
												<div style="display: none;color: red;" id="gsttype"> Please enter GST Registration type</div> 
											</div>
											<div class="form-group col-lg-3" style="float: left;">
												<label class="form-label">GSTIN</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="gstin" name="gstin" value="<?php echo $role['gstin']; ?>" >
												</div>
												<div style="display: none;color: red;" id="gstin_err"> Please enter GST IN </div> 
											</div>
											<div class="form-group col-lg-3 w-full">
												<label class="form-label">Parent Vendor</label>
				<select name="parent_vendor" class="form-select js-example-basic-single">
    <option value="">Select Any One</option>
     <?php foreach($vendorslist as $venlist) { ?>
          <option <?php echo $role['parent_vendor']==$venlist['id'] ? 'selected':'' ?> value="<?php echo $venlist['id'];?>"><?php echo $venlist['display_name']; ?></option>
        <?php } ?>
  </select>

												<div style="display: none;color: red;" id="email_err"> Please enter parent_customer </div> 
											</div>
											<div class="form-group col-lg-3">
												<label class="form-label">Bill With Parent</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="bill_with_parent" name="bill_with_parent" value="<?php echo $role['bill_with_parent']; ?>" >	
												</div>
												<div style="display: none;color: red;" id="company_err"> Please enter bill_with_parent </div>  
											</div>
											<div class="form-group col-lg-3">
												<label class="form-label">Language</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="language_id" name="language_id" value="<?php echo $role['language_id']; ?>" >	
												</div>
												<div style="display: none;color: red;" id="company_err"> Please enter bill_with_parent </div>  
											</div>
											<div class="col-lg-3" style="float: left;">
												<label class="form-label">Hold Reason</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="hold_reason" name="hold_reason" value="<?php echo $role['hold_reason']; ?>" >
												</div>
											</div>
											<div class="col-lg-3" style="float: left;">
												<label class="form-label">Photo</label>
												<div class="col-lg-9 col-xl-6">
													<div class="profile-pic">
														<label class="-label" for="display_logo">
															<span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
														</label>
														<input type="file" name="image" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
														<img src="<?php echo "uploads/all/".$role['image'] ; ?>" id="outputdisplay_logo" width="200" onerror="this.src='uploads/all/no-image.jpg';"/>
														<input type="hidden" name="hid_image" value="<?php echo $role['image'] ;  ?>">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="contact-details" role="tabpanel">
									<div class="from row ">
										<div class="col-lg-6 row" >
											<div class="form-group col-lg-12">
												<label class="form-label">Billing Adderss</label>
												<div class="kt-input-icon">
													<textarea type="text" class="form-control" placeholder="Billing Adderss" name="billing_address"><?php echo $role['billing_address']; ?></textarea>
												</div> 
											</div>
											<div class="form-group col-lg-6 p-r-5" >
												<label  class="form-label">Pincode</label>
												<input class="form-control" placeholder="Pincode" type="text" id="billing_post_code" name="billing_post_code" value="<?php echo $role['billing_post_code']; ?>" >

											</div>
											<div class="form-group col-lg-6 w-full" >
												<label class="form-label">Country</label>
												<select id="kt_select2_102" class="form-select js-example-basic-single" name="billing_country"  onchange="selectedcategory(this.value,'kt_select2_101','states','country_id','selectedbillcountry')">
											        <option value="">Select Any One</option>
											        <?php foreach($countrieslist as $countryvalue) { ?>
											          <option <?php echo $role['billing_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
											        <?php } ?>
											      </select>

											</div>
											
											<div class="form-group col-lg-6  w-full" >
												<label class="form-label">State</label>
												 <select id="kt_select2_101" name="billing_state" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'kt_select2_103','cities','state_id','selectedbillstate')">
      <option value="">Select Any One</option>
      <?php foreach($states as $states1) { ?>
          <option <?php echo $role['billing_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
        <?php } ?>
    </select>

											</div>
											<div class="form-group col-lg-6 p-r-5  w-full" >
												<label class="form-label">City/Town</label>
												<select id="kt_select2_103" name="billing_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
    <option value="">Select Any One</option>
     <?php foreach($cities as $cities1) { ?>
          <option <?php echo $role['billing_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
        <?php } ?>
  </select>
											</div>
											
											<div class="form-group col-lg-6 p-r-5" style="float: right;">
												<label class="form-label">Contact Person</label>
												<input class="form-control" placeholder="Contact Person" type="text" id="" name="contact_person" value="<?php echo $role['contact_person']; ?>" >

											</div>
											<div class="form-group col-lg-6 p-r-5" style="float: left;">
												<label class="form-label">Mobile</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="mobile" name="mobile" value="<?php echo $role['mobile']; ?>" >	
												</div>
												<div style="display: none;color: red;" id="mobile_err"> Please enter mobile </div>  
											</div>
											<div class="form-group col-lg-6 p-r-5" style="float: right;">
												<label class="form-label">Fax</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="fax" name="fax" value="<?php echo $role['fax']; ?>" >
												</div>
												<div style="display: none;color: red;" id="fax_err"> Please enter Fax </div>  
											</div>
											<div class="form-group col-lg-6 " style="float: left;">
												<label class="form-label">Others</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="other" name="other" value="<?php echo $role['other']; ?>" >	
												</div>
												<div style="display: none;color: red;" id="first_name_err"> Please enter first_name </div> 
											</div>
											<div class="form-group col-lg-6 p-r-5" style="float: right;">
												<label class="form-label">Website</label>
												<div class="kt-input-icon">
													<input class="form-control" type="text" id="website_url" name="website_url" value="<?php echo $role['other']; ?>" >
												</div>
												<div style="display: none;color: red;" id="fax_err"> Please enter Fax </div>  
											</div>
										</div>	
										<div class="col-lg-6 row" style="float: left;">
											<div class="form-group col-lg-12" >
												<label class="form-label">Shipping Adderss</label>
												<div class="kt-input-icon">
													<textarea type="text" class="form-control" placeholder="Street" name="shipping_address">
														<?php echo $role['shipping_address']; ?>
													</textarea>
												</div> 
											</div>
											<div class="form-group col-lg-6" >
												<label class="form-label">Pincode</label>
												<input class="form-control" placeholder="Pincode" type="text" id="shipping_post_code" name="shipping_post_code" value="<?php echo $role['shipping_post_code']; ?>" >

											</div>
											<div class="form-group col-lg-6  w-full" >
												<label class="form-label">Country</label>
												<select id="kt_select2_104" class="form-select js-example-basic-single"  name="shipping_country" onchange="selectedcategory(this.value,'kt_select2_105','states','country_id','selectedshipcountry')">
  <option value="">Select Any One</option>
  <?php foreach($countrieslist as $countryvalue) { ?>
    <option <?php echo $role['shipping_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id'];?>"><?php echo $countryvalue['country_name']; ?></option>
  <?php } ?>
</select>

											</div>
											
											<div class="form-group col-lg-6  w-full">
												<label class="form-label">State</label>
												<select id="kt_select2_105" name="shipping_state" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'kt_select2_106','cities','state_id','selectedshipstate')">
  <option value="">Select Any One</option>
    <?php foreach($states as $states1) { ?>
          <option <?php echo $role['shipping_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
        <?php } ?>
  
</select>

											</div>
											<div class="form-group col-lg-6 p-r-5 w-full">
												<label class="form-label">City/Town</label>
												<select id="kt_select2_106" name="shipping_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedshipcity')">
  <option value="">Select Any One</option>
     <?php foreach($cities as $cities1) { ?>
          <option <?php echo $role['shipping_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
        <?php } ?>
          </select>

											</div>
											
										</div>
									</div>
								</div>
								<div class="tab-pane" id="note" role="tabpanel">
									<div class="row ">
										<div class="col-lg-6 p-r-5" style="float: left;">
											<label class="form-label">PO Notes</label>
											<textarea type="text" name="vendor_po_notes"  class="form-control" style="height: 150px;" placeholder="Enter your Customer Statement Notes"><?php echo $role['vendor_po_notes']; ?></textarea>
										</div>
										<div class="col-lg-6" style="float: left;">
											<label class="form-label">Other Notes</label>
											<textarea type="text" name="vendor_other_notes"   class="form-control" style="height: 150px;" placeholder="Enter your Customer Invoice Notes"><?php echo $role['vendor_other_notes']; ?></textarea>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="tax-info" role="tabpanel">
									<div class="from row">
										<div class="col-lg-6" style="margin-bottom: 2rem;">
											<label class="form-label">Tax Reg.No.</label>
											<div class="kt-input-icon">
												<input class="form-control" type="text" id="vendor_tax_reg_no" name="vendor_tax_reg_no" value="<?php echo $role['vendor_tax_reg_no']; ?>" >
											</div> 
										</div>
										<div class="col-lg-6" style="margin-bottom: 2rem;">
											<label class="form-label">CST Reg.No</label>
											<div class="kt-input-icon">
												<input class="form-control" type="text" id="cst_reg_no" name="cst_reg_no" value="<?php echo $role['cst_reg_no']; ?>" >
											</div> 
										</div>
										<div class="col-lg-6" style="margin-bottom: 2rem;">
											<label class="form-label">PAN No.</label>
											<div class="kt-input-icon">
												<input class="form-control" type="text" id="pan_no" name="pan_no" value="<?php echo $role['pan_no']; ?>" >
											</div> 
										</div>
										<div class="col-lg-6" style="margin-bottom: 2rem;">
											<label class="form-label">  </label>
											<div class="kt-checkbox-inline">
												<label class="kt-checkbox">
													<input type="checkbox" name="tds_apply" class="js-switch" <?php echo $role['tds_apply'] == "0"?"checked":"";?>> Apply TDS for this vendor
													<span></span>
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="payment-billing" role="tabpanel">
									<div class="from row">
										<div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
											<label class="form-label">Preferred Payment Method</label>
											<select name="pref_payment_method" class="form-control form-select js-example-basic-single">
                <option value="">Select Any One</option>
                <?php foreach($payment_method as $key=>$pm) { ?>
                  <option value="<?php echo $key; ?>" <?php echo $role['pref_payment_method']==$key?'selected':'';?> ><?php echo $pm; ?></option>
                <?php } ?>
              </select>
										</div>
										<div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
											<label class="form-label">Terms</label>
											<select name="payment_terms" class="form-control form-select js-example-basic-single">
								                <option value="">Select Any One</option>
								                <?php foreach($paymenttype as $key=>$st) { ?>
								                  <option value="<?php echo $key; ?>" <?php echo $role['payment_terms']==$key?'selected':'';?> ><?php echo $st; ?></option>
								                <?php } ?>
								              </select>
										</div>
										<div class="col-lg-6" style="margin-bottom: 2rem;">
											<label class="form-label">Preferred Delivery Method</label>
											<div class="kt-input-icon">
												<input class="form-control" type="text" id="preferred_deliver_method" name="preferred_deliver_method" value="<?php echo $role['preferred_deliver_method']; ?>" >
											</div> 
										</div>
										<div class="col-lg-4" style="margin-bottom: 2rem;">
											<label class="form-label">Opening Blance</label>
											<div class="kt-input-icon">
												<input class="form-control text-right" type="text" id="opening_balance" name="opening_balance" value="<?php echo $role['opening_balance']; ?>" >
											</div>
										</div>
										<div class="col-lg-2" style="margin-bottom: 2rem;">
											<label class="form-label">As of</label>
											<div class="kt-input-icon">
												<input class="form-control datepicker" type="text" id="asof" name="asof" value="<?php echo fromDate($role['asof']); ?>" >
											</div>
										</div>
										<div class="col-lg-6" style="margin-bottom: 2rem;">
											<label class="form-label">The Vendor Pays Me With</label>
											<div class="kt-input-icon">
												<input class="form-control" type="text" id="pay_with_me" name="pay_with_me" value="<?php echo $role['pay_with_me']; ?>" >
											</div>
										</div>
										<div class="col-lg-4" style="margin-bottom: 2rem;">
											<label class="form-label">Credit Limit</label>
											<div class="kt-input-icon">
												<input class="form-control text-right" type="text" id="credit_limit" name="credit_limit" value="<?php echo $role['credit_limit']; ?>" >
											</div>
										</div>

									</div>
								</div>
								<div class="tab-pane" id="sale-discount" role="tabpanel">
									<div class="form-group row">
										<div class="col-lg-6" style="margin-bottom: 2rem;">
											<label class="form-label">Default Discount Percentage (%)</label>
											<div class="kt-input-icon">
												<input class="form-control text-right" type="text" id="default_discount_perc" name="default_discount_perc" value="<?php echo $role['default_discount_perc']; ?>" >
											</div> 
										</div>
										<div class="col-lg-6" style="margin-bottom: 2rem;">
											<label class="form-label">Payment Discount Percentage (%)</label>
											<div class="kt-input-icon">
												<input class="form-control text-right" type="text" id="payment_discount_perc" name="payment_discount_perc" value="<?php echo $role['payment_discount_perc']; ?>" >
											</div> 
										</div>
										<div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
											<label class="form-label">Sales Type</label>
											<select name="sales_type" class="form-control form-select js-example-basic-single">
								                <option value="">Select Any One</option>
								                <?php foreach($salestype as $key=>$st) { ?>
								                  <option value="<?php echo $key; ?>" <?php echo $role['sales_type']==$key?'selected':'';?> ><?php echo $st; ?></option>
								                <?php } ?>
								              </select>
								          </div>
									</div>
								</div>
								<div class="tab-pane" id="agreements" role="tabpanel">
									<div class="p-3">
										<h4 class="mb-10 font-weight-bold text-dark">Agreemets</h4>
										<div class="row">
											<div class="form-group col-lg-12">
												<table class="table table-bordered " id="edutable">

													<tr>
														<th class="" width="2%">Sl NO</th>
														<th class="" width="25%">Type</th>
														<th class="thheader" width="20%">Number</th>
														<th class="" width="20%"> Sign Date</th>
														<th class="thheader" width="20%">Expiry Date </th>
														<th class="thheader" width="15%">Files</th>
													</tr>

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
																		<input type="file" name="agreement_file<?php echo $dockey;?>[]" />
																		<input type="hidden" name="hidden_agreement_file<?php echo $dockey;?>" value="<?php echo $inst['file_url'][$dockey]; ?>"/>
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
														<!--  <button class="btn btn-danger float-left delete " id="removeRows" onclick="removerows()"  type="button">Delete</button> -->

														<button class="btn btn-success float-left" onclick="addedurows()" id="addRows" type="button">+ Add More</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="attachments" role="tabpanel">
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
								<div class="tab-pane" id="employees" role="tabpanel">
									<div class="card-body">
										<?php 
										if(count((array)$emplist) != 0){
										$i=1;
										// $rid =  encodeId($_GET['Rid']);
										// $rid = urldecode($rid);
										// $vendor_id = explode(":", $rid);
										// $url = API."?mode=emplist&vendarid=".$vendor_id ['1']; 
										// $collfunction = callAPI($url);
										// $emplist = json_decode($collfunction,1); ?>
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
															<input type="checkbox" onclick="checkboxclick()" <?php if($role['hiring_clause']==0){ echo "checked";  } ?> id="hiring_clause" name="hiring_clause" class="js-switch">
															<span></span>
														</label>
													</span>
													<label class="col-form-label">No Hiring Clause </label>
												</div>
											</div>
											<div class="col-lg-4 col-md-4 col-sm-12 coolingmonth">
												<label class="form-label">Cooling Period of Months</label>
												<div class="kt-input-icon">
													<input class="form-control"   type="text" id="cooling_months" name="cooling_months"   value="<?php echo $role['cooling_months']; ?>">
												</div>
											</div>
											<div class="col-lg-2 "></div>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>

				</div>
			</div></form></div>
