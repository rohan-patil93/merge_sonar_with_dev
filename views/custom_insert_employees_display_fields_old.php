<?php
if($_POST['option']!=""){  
	include('../includes/session.php');
	include('../dbconfig.php');
	include('../comm.php');
}

if(isset($_GET['Rid']) && $_GET['Rid']!="") {
	$role_type = base64_decode($_GET['Rid']); 
}
//echo $role_type;
$title = viewTitle($_GET['option']);
$company_id = $_SESSION['company_id'];

$wherecon["where"] = array('is_active'=> '0','company_id'=>$company_id);
$data_con = urlencode(json_encode($wherecon));
//print_r($wherecon);
$url = API."?mode=gettabledetailswithconditions&table=application_roles&data=".$data_con; 
     // echo $url;
$response = callAPI($url);
$display_roles = json_decode($response,1);
//print_r($display_roles); //exit;

$field_names = array('EMPLOYEE_CODE','DISPLAY_NAME','EMAIL','USERNAME','FIRST_NAME','MIDDLE_NAME','LAST_NAME','CONTACT','FAX','ADDRESS','POSTCODE',/*'USER_GROUP',*/'DEPARTMENT','VERTICAL','REPOTING_TO','HIRING_SOURCE','DESIGNATION',/*'SEAT_LOCATION',*/'DATE_OF_JOINING','TOTAL_EXPERIENCE','RELEVANT_EXPERIENCE','EXPERIENCE_IN_CURRENT_ORGANIZATION','BAND','LEVEL','NOTICE_PERIOD','WORK_LOCATION','BILLABLE','ROLE','EMPLOYEMENT_TYPE','OFFICE_PHONE','EXTENSION','DATE_OF_BIRTH','AGE','GENDER','MARITAL_STATUS','DISABILITY', 'DISABILITY_PERCENTAGE', '	PHYSICALLY_CHALLENGED_CHILDREN', 'WORK_TEXTILE_INDUSTRY' ,'MARRIGE_DATE','BLOOD_GROUP','ADMIN_BUDDY','HR_BUDDY','FINANACE_BUDDY','PROJECT_BUDDY','LOC_BUDDY','FACEBOOK','TWITTER','LINKEDIN');

//$field_names_json = json_encode($field_names);

$wherecon["where"] = array('is_active'=> '0','company_id'=>$company_id,'role_id'=>$role_type);
$data_con = urlencode(json_encode($wherecon));
//print_r($wherecon);
$url = API."?mode=gettabledetailswithconditions&table=employees_display_fields&data=".$data_con; 
     // echo $url;
$response = callAPI($url);
$display_data = json_decode($response,1);

$display_record  = $display_data[0]['field_list'];
//print_r($display_record);
$rowid  = $display_data[0]['id']; 
$display_values = json_decode($display_record,1);
//print_r($display_values);
if ($_POST['submit_value'] == 'continue'){

	//echo "<pre>"; 
	//print_r($_POST); exit;
	foreach ($_POST['fieldlist'] as $key => $field) {
		$data[$field]['field'] = $field;
		$data[$field]['display'] = $_POST['displaylist'][$key]!=''?$_POST['displaylist'][$key]:0;
		$data[$field]['readonly'] = $_POST['readonlylist'][$key]!=''?$_POST['readonlylist'][$key]:0;
	}

	$role_type = base64_decode($_POST['role_type']); 
	$jsonfieldlist = json_encode($data,1);
	$postdata = array('company_id'=>$company_id,'is_active'=>0,'role_type'=> $role_type,'field_list'=>$jsonfieldlist,'rowid'=>$rowid,'user_id'=>$_SESSION['userid'],'updated_date'=>nowDate());
	$url = API."?mode=employeesdisplayfieldsset&method=post";
	$response = file_post_contents($url, $postdata, 'insert');
		//print_r($data);
		//	exit;

	header("location:index.php?option=COM_INSERT_EMPLOYEES_DISPLAY_FIELDS&Rid=".$_GET['Rid']);
}

?>
<script type="text/javascript">
	function GetSelectedTextValue(selected_user) { 
		var selected_user = $('#kt_select').val();
		window.location.href = 'index.php?option=COM_INSERT_EMPLOYEES_DISPLAY_FIELDS&Rid='+selected_user;
	}

</script>
<div class="flex-column-fluid ">
	<div class="container-fluid ">
		<div class=" flex-row ">
			<div class="alert alert-warning alert-dismissible">
				<?php echo "This function is used for maintaining <strong>Display Attributes</strong>. * represents mandatory fields."; ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="card card-custom card-sticky" id="kt_page_sticky_card">
						<div class="card-header">
							<h5><?php echo $title; ?></h5>

							<div class="card-header-right">
								<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
									<i class="icofont icofont-reply"></i>
									Back
								</a>
							</div>
						</div>
						<!--begin::Form-->
						<form class="kt-form kt-form--label-right j-pro" action="" method="post">
							<div class="card-body">
								<div class="form-group  row">
									<label class="col-xl-3 col-lg-3 col-form-label" >Role Type</label>
									<div class="col-lg-9 col-xl-6">
										<select id="kt_select" class="form-control form-select js-example-basic-single" onchange="GetSelectedTextValue(this.value)" name="role_type">
											<option> Select Any One</option>
											<?php foreach ($display_roles as $value) {  ?>

												<option <?php if($value['role_short_code'] == $role_type){ ?> selected <?php } ?> value="<?php echo base64_encode($value['role_short_code']); ?>"><?php echo $value['name']; ?></option>

											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-4">
										<label>Field Names </label>
									</div>
									<div class="col-lg-2">
										<label class=""> Display </label>
									</div>
									<div class="col-lg-2">
										<label class=""> Read Only</label>
									</div>
								</div>
								<?php
								$key = 0;
								foreach ($field_names as $value) { ?>
									<div class="form-group row">
										<div class="col-lg-4">
											<label> <input type="hidden" name="fieldlist[<?php echo $key?>]" value="<?php echo $value;?>"><?php echo viewTitle($value);?> </label>
										</div>
										<div class="col-lg-2">
											<input type="checkbox" value="1" <?php echo $display_values[$value]['display'] == 1 ? 'checked' : ''; ?> class="js-switch"  name="displaylist[<?php echo $key?>]" >
										</div>
										<div class="col-lg-2">
											<input type="checkbox" <?php echo $display_values[$value]['readonly'] == 1 ? 'checked' : ''; ?> value="1" class="js-switch" name="readonlylist[<?php echo $key?>]" >
										</div>
									</div>
									<?php 
									$key++;
								} ?>
								<div class="card-footer">
									<div class="row">
										<div class="col-lg-6">
											<input type="hidden" name="submit" value="Add" />
											<button id="kt_llogin_signup_submitt" class="btn btn-primary mr-2"   name="submit_value" value="continue"><i class="la la-check"></i>Save & exit</button>

											<button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>

										</div>
										<div class="col-lg-6 text-right">
											<button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
										</div> 
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>