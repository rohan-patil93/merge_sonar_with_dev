<?php
if (isset($_POST['submit'])){
	//print_r($_POST); exit;
	if($_POST['submit'] == 'Add'){
		$data= array();
		$data['company_id'] = $_POST['company_id'];
		$data['name'] = $_POST['name'];
		$data['sku'] = $_POST['sku'];
		$data['description'] = $_POST['description'];
		$data['sale_price'] = $_POST['sale_price'];
		$data['purchase_price'] = $_POST['purchase_price'];
		$data['quantity'] = $_POST['quantity'];
		$data['category_id'] = $_POST['category_id'];
		$data['tax_id'] = $_POST['tax_id'];
		$data['item_image'] = $_POST['item_image'];
		$data['item_image2'] = $_POST['item_image2'];
		$data['item_image3'] = $_POST['item_image3'];
		$data['Current_Status'] = $_POST['Current_Status'];
		$data['Status_Date'] = nowDate();
		$data['Created_Date'] = nowDate();
		$data['From_Date'] = dateFormate($_POST['From_Date']);
		$data['To_Date'] = dateFormate($_POST['To_Date']);
		$data['Created_By'] = $_SESSION['userid'];
		$url="";
		$urldata = urlencode(json_encode($data,true));
		$url = API."?mode=adddata&table=".TPREFIX.TBL_ITEMS."&data=$urldata";
		//echo $url; exit;
		$response = file_get_contents($url, false, $context);
		if ($response) {
			header("location:index.php?option=com_view_items");
		}
	}
	if($_POST['submit'] == 'Update'){
		$data= array();
		$data['company_id'] = $_POST['company_id'];
		$data['name'] = $_POST['name'];
		$data['sku'] = $_POST['sku'];
		$data['description'] = $_POST['description'];
		$data['sale_price'] = $_POST['sale_price'];
		$data['purchase_price'] = $_POST['purchase_price'];
		$data['quantity'] = $_POST['quantity'];
		$data['category_id'] = $_POST['category_id'];
		$data['tax_id'] = $_POST['tax_id'];
		$data['item_image'] = $_POST['item_image'];
		$data['item_image2'] = $_POST['item_image2'];
		$data['item_image3'] = $_POST['item_image3'];
		$data['Current_Status'] = $_POST['Current_Status'];
		$data['From_Date'] = dateFormate($_POST['From_Date']);
		$data['To_Date'] = dateFormate($_POST['To_Date']);
		$data['Updated_Date'] = nowDate();
		$data['Updated_By'] = $_SESSION['userid'];
		$urldata = urlencode(json_encode($data,true));
		$url = API."?mode=updaterowtabledata&rid=".$_POST['hid']."&table=".TPREFIX.TBL_ITEMS."&data=$urldata";
		//echo $url; exit;
		$response = file_get_contents($url, false, $context);
		if ($response) {
			header("location:index.php?option=com_view_items");
		}
	}
	
}
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ 
	$rid =  encodeId($_GET['Rid']);  
	$url = API."?mode=rowitems&rid=$rid";
	$response = file_get_contents($url, false, $context);
	//echo $response;
	$role = json_decode($response,1);
	//print_r($role);
	$permissions = json_decode($role['permissions'],1);
	//print_r($permissions); echo $permissions['add'];
}
$title = viewTitle($_GET['option']);
$stausresult = statusData(TPREFIX.TBL_ITEMS);	
?>
<script type="text/javascript">
	function validateForm(){
		var error = <?php echo json_encode($error_data); ?>;
		var x = document.forms["myForm"]["name"].value;
  		if (x == "") {
   			 alert(error[2401]);
   			 return false;
  		}
  		var y = document.forms["myForm"]["sku"].value;
  		if (y == "") {
   			 alert(error[2401]);
   			 return false;
  		}	
  		var z = document.forms["myForm"]["sale_price"].value;
  		//alert(z);
  		if (z == "") {
   			alert(error[2403]);
   			 return false;
  		}
  		var m = document.forms["myForm"]["quantity"].value;
  		if (m == "") {
   			 alert(error[2404]);
   			 return false;
  		}			
	}
function validateForm12(){
	alert("header");
	var sku = document.getElementById('sku').value;
	var item_id = document.getElementById('item_id').value;
	if (sku == "") {
		alert('Please Enter sku value');
	}else {
		window.open('index.php?option=com_item_specifications&fid=1&sku='+sku+'&Rid='+item_id,'_blank');
	}
	
}
</script>
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

	<!-- begin:: Content Head -->
	<div class="kt-subheader   kt-grid__item" id="kt_subheader">
		<div class="kt-container  kt-container--fluid ">
			<div class="kt-subheader__main">
				<h3 class="kt-subheader__title">
					<?php echo $title; ?>
				</h3>
			</div>
			<div class="kt-subheader__toolbar">
			</div>
		</div>
	</div>

	<!-- end:: Content Head -->

	<!-- begin:: Content -->
	
	<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
		<div class="kt-portlet kt-portlet--tabs">
			<div class="kt-portlet__body">
				<form action="" method="post" name="myForm" onsubmit="return validateForm()" >
					<div class="tab-content">
						<div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
							<div class="kt-form kt-form--label-right">
								<div class="kt-form__body">
									<div class="kt-section kt-section--first">
										<div class="kt-section__body">
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Company </label>
												<div class="col-lg-9 col-xl-6">
													<select class="form-control" id="exampleSelect1" name= "company_id">
														<?php foreach ($company as $key=>$companyy) {?>

														<option <?php if($role['company_id'] == $key){?> selected<?php } ?>  value = <?php echo $key; ?>  ><?php echo $companyy; ?></option>
													<?php } ?>
													</select>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Name<span class="requried">*</span></label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-user" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="name" value=<?php echo $role['name']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">SKU<span class="requried">*</span></label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-code" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="sku" id='sku' value=<?php echo $role['sku']; ?>>
														<input type="hidden" name="item_id" id="item_id" value="<?php echo deocdeId($rid); ?> ">
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Description</label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-comment" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="description" value=<?php echo $role['description']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Sale Price<span class="requried">*</span></label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-money" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="sale_price" value=<?php echo $role['sale_price']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Purchase Price</label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-money" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="purchase_price" value=<?php echo $role['purchase_price']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Quantity<span class="requried">*</span></label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-tasks" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="quantity" value=<?php echo $role['quantity']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Category ID</label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-group" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="category_id" value=<?php echo $role['category_id']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Tax ID</label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-tasks" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="tax_id" value=<?php echo $role['tax_id']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Specifications</label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
													<!-- <a  href="http://localhost/project/index.php?option=com_item_specifications" target="_blank"> -->
													<span onclick="validateForm12();"  class="btn btn-warning btn-elevate btn-pill btn-elevate-air">Specifications</span><!--  </a> -->
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Item Image</label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-file-image-o"  aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="item_image" value=<?php echo $role['item_image']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Item Image 2</label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-file-image-o" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="item_image2" value=<?php echo $role['item_image2']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-form-label">Item Image 3</label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-file-image-o" aria-hidden="true"></i></span></div>
														<input class="form-control" type="text" name="item_image3" value=<?php echo $role['item_image3']; ?>>
													</div>
												</div>
											</div>
											<?php $from = fromDate();
												  $to= toDate(); ?>
											<div class="form-group  row">
												<label class="col-form-label col-lg-3 col-sm-12">From Date </label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group date">
														<div class="input-group-append">
															<span class="input-group-text">
																<i class="la la-calendar-check-o"></i>
															</span>
														</div>
														<input type="text" class="form-control" name="From_Date" value="<?php echo $from; ?>"  id="kt_datepicker_4_3" />
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-form-label col-lg-3 col-sm-12">To Date </label>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group date">
														<div class="input-group-append">
															<span class="input-group-text">
																<i class="la la-calendar-check-o"></i>
															</span>
														</div>
														<input type="text" class="form-control"  name="To_Date" value="<?php echo $to; ?>"  id="kt_datepicker_4_3_1" />
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<label class="col-xl-3 col-lg-3 col-sm-12">Status</label>
												<div class="col-lg-9 col-xl-6">
													<?php foreach($stausresult as $key=>$radio){ ?>
													<label class="kt-radio">
														<input type="radio" <?php if($role['Current_Status'] == $key){?> checked="checked" <?php } ?> value="<?php echo $key; ?>"  name="Current_Status"> <?php echo $radio; ?>
														<span></span>
													</label>
												<?php } ?>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-3 col-xl-3">
												</div>
												<div class="col-lg-9 col-xl-9">
													<?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
													<input type="hidden" name="hid" value="<?php echo $rid; ?>" />
													<input type="hidden" name="submit" value="Update" />
													<button id="kt_llogin_signup_submit" class="btn btn-brand btn-elevate btn-pill btn-elevate-air">Upadate</button>
													<button type="reset" class="btn btn-warning btn-elevate btn-pill btn-elevate-air">Cancel</button>
												<?php } else{ ?>
													<input type="hidden" name="submit" id="submit" value="Add" />
													<button id="kt_llogin_signup_submit" class="btn btn-brand btn-elevate btn-pill btn-elevate-air">ADD</button>
													<button type="reset" class="btn btn-warning btn-elevate btn-pill btn-elevate-air">Cancel</button>
												<?php } ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- end:: Content -->
</div>