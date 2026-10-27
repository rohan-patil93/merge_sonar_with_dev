<?php
if (isset($_POST['submit'])){
		if($_POST['submit'] == 'Add'){
		$data= array();
		$data['EDI_Name'] = $_POST['EDI_Name'];
		$data['EDI_Type'] = $_POST['EDI_Type'];
		$data['EDI_File_Type'] = $_POST['EDI_File_Type'];
		$data['EDI_Delimiter'] = $_POST['EDI_Delimiter'];
		$data['EDI_String_Separator'] = $_POST['EDI_String_Separator'];
		$data['Status_Date'] = nowDate();
		$data['Created_Date'] = nowDate();
		$data['From_Date'] = dateFormate($_POST['From_Date']);
		$data['To_Date'] = dateFormate($_POST['To_Date']);
		$data['Created_By'] = $_SESSION['userid'];
		$url="";
		$urldata = urlencode(json_encode($data,true));
		$url = API."?mode=adddata&table=".TBL_EDIMASTER."&data=$urldata";

		$response = file_get_contents($url, false, $context);
		if ($response) {
			header("location:index.php?option=com_view_edi_master");
		}
	}
	if($_POST['submit'] == 'Update'){
			$data= array();
		$data['EDI_Name'] = $_POST['EDI_Name'];
		$data['EDI_Type'] = $_POST['EDI_Type'];
		$data['EDI_File_Type'] = $_POST['EDI_File_Type'];
		$data['EDI_Delimiter'] = $_POST['EDI_Delimiter'];
		$data['EDI_String_Separator'] = $_POST['EDI_String_Separator'];
		$data['Current_Status'] = $_POST['Current_Status'];
		$data['From_Date'] = dateFormate($_POST['From_Date']);
		$data['To_Date'] = dateFormate($_POST['To_Date']);
		$data['Updated_Date'] = nowDate();
		$data['Updated_By'] = $_SESSION['userid'];
		$urldata = urlencode(json_encode($data,true));
		$url = API."?mode=updaterowtabledata&rid=".$_POST['hid']."&table=".TBL_EDIMASTER."&data=$urldata";
		$response = file_get_contents($url, false, $context);
		if ($response) {
			header("location:index.php?option=com_view_edi_master");
		}
	}
}
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ 
	$rid =  encodeId($_GET['Rid']);
	$url = API."?mode=singlerowtabledata&rid=$rid&table=".TBL_EDIMASTER;
	$response = file_get_contents($url, false, $context);
	$role = json_decode($response,1);
	$permissions = json_decode($role['permissions'],1);
}
$title = viewTitle($_GET['option']);
$stausresult = statusData(TBL_EDIMASTER);
?>
<script type="text/javascript">
	function validateForm(){
		var error = <?php echo json_encode($error_data); ?>;
		var x = document.forms["myForm"]["EDI_Name"].value;
  		if (x == "") {
   			 alert(error[1401]);
   			 return false;
  		}
  		var y = document.forms["myForm"]["EDI_Type"].value;
  		if (y == "") {
   			 alert(error[1402]);
   			 return false;
  		}	
  		var z = document.forms["myForm"]["EDI_File_Type"].value;
  		if (z == "") {
   			alert(error[1403]);
   			 return false;
  		}
  				
	}

</script>
<div class="d-flex flex-column-fluid">
<div class="container-fluid">

	<!-- begin:: Content Head -->
	<div class="kt-subheader   kt-grid__item" id="kt_subheader">
		<div class="kt-container card card-custom gutter-b card-stretch  kt-container--fluid ">
			<div class="kt-subheader__main card-body">
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
		<div class="kt-portlet kt-portlet--tabs card card-custom gutter-b card-stretch">
			<div class="card-body">
				<form action="" method="post" name="myForm" onsubmit="return validateForm()">
					<div class="tab-content">
						<div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
							<div class="kt-form kt-form--label-right">
								<div class="kt-form__body">
									<div class="kt-section kt-section--first">
										<div class="kt-section__body">
											<div class="form-group  row">
												<span class="col-xl-3 col-lg-3 col-form-label">EDI Name <span class="requried">*</span></span>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-user" aria-hidden="true"></i></span></div>
														<input aria-label="EDI Name" class="form-control" type="text" name="EDI_Name" value=<?php echo $role['EDI_Name']; ?>>
													</div>
												</div>
											</div>

											<div class="form-group  row">
												<span class="col-xl-3 col-lg-3 col-form-label">Type<span class="requried">*</span></span>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-tasks" aria-hidden="true"></i></span></div>
														<input aria-label="EDI Type" class="form-control" type="text" name="EDI_Type" value=<?php echo $role['EDI_Type']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group row">
												<span class="col-3 col-form-label">EDI Type</span>
												<div class="col-9">
													<div class="kt-radio-inline">
														<label class="kt-radio">
															<input aria-label="EDI Type" type="radio" checked="checked" name="EDI_Type" value="Only Upload"> Only Upload
															<span></span>
														</label>
														<label class="kt-radio">
															<input aria-label="EDI Type" type="radio"  name="EDI_Type" value="Upload With Confirm"> Upload With Confirm
															<span></span>
														</label>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<span class="col-xl-3 col-lg-3 col-form-label">File Type <span class="requried">*</span></span>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-upload" aria-hidden="true"></i></span></div>
														<input aria-label="EDI File Type" class="form-control" type="text" name="EDI_File_Type" value=<?php echo $role['EDI_File_Type']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<span class="col-xl-3 col-lg-3 col-form-label">Delimiter</span>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-group" aria-hidden="true"></i></span></div>
														<input aria-label="EDI Delimiter" class="form-control" type="text" name="EDI_Delimiter" value=<?php echo $role['EDI_Delimiter']; ?>>
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<span class="col-xl-3 col-lg-3 col-form-label">String Separator</span>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group">
														<div class="input-group-prepend"><span class="input-group-text"><i class="la la-group" aria-hidden="true"></i></span></div>
														<input aria-label="EDI String Separator" class="form-control" type="text" name="EDI_String_Separator" value=<?php echo $role['EDI_String_Separator']; ?>>
													</div>
												</div>
											</div>
											<?php $from = fromDate();
												  $to= toDate(); ?>
											<div class="form-group  row">
												<span class="col-form-label col-lg-3 col-sm-12">From Date </span>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group date">
														<div class="input-group-append">
															<span class="input-group-text">
																<i class="la la-calendar-check-o"></i>
															</span>
														</div>
														<input aria-label="From Date" type="text" class="form-control" name="From_Date" value="<?php echo $from; ?>"  id="kt_datepicker_4_3" />
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<span class="col-form-label col-lg-3 col-sm-12">To Date </span>
												<div class="col-lg-9 col-xl-6">
													<div class="input-group date">
														<div class="input-group-append">
															<span class="input-group-text">
																<i class="la la-calendar-check-o"></i>
															</span>
														</div>
														<input aria-label="To Date" type="text" class="form-control"  name="To_Date" value="<?php echo $to; ?>"  id="kt_datepicker_4_3_1" />
													</div>
												</div>
											</div>
											<div class="form-group  row">
												<span class="col-xl-3 col-lg-3 col-sm-12">Status</span>
												<div class="col-lg-9 col-xl-6">
													<?php foreach($stausresult as $key=>$radio){ ?>
													<label class="kt-radio">
														<input aria-label="Radio option" type="radio" <?php if($role['Current_Status'] == $key){?> checked="checked" <?php } ?> value=<?php echo $key; ?>  name="Current_Status"> <?php echo $radio; ?>
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
													<input type="hidden" name="hid" value=<?php echo $rid; ?> />
													<input type="hidden" name="submit" value="Update" />
													<button id="kt_llogin_signup_submit" class="btn btn-brand btn-elevate btn-pill btn-elevate-air">Upadate</button>
													<button type="reset" class="btn btn-warning btn-elevate btn-pill btn-elevate-air">Cancel</button>
												<?php } else{ ?>
													<input type="hidden" name="submit" value="Add" />
													<button id="kt_llogin_signup_submit_2" class="btn btn-brand btn-elevate btn-pill btn-elevate-air">ADD</button>
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

</div>
	<!-- end:: Content -->
</div>
