<?php
if(isset($_POST['submit'])){
	//print_r($_POST);
	$emp_id  = $_POST['emp_id'];
	$user_id  = $_POST['user_id'];
	$user_data  = array('user_name'=>$_POST['emp_code'],'first_name'=>$_POST['first_name'],'middle_name'=>$_POST['middle_name'],'last_name'=>$_POST['last_name'],'display_name'=>$_POST['display_name'],'location_id'=>$_POST['location_id'],'cost_center'=>$_POST['cost_center_id'],'approval_manager'=>$_POST['approval_manager'],'reports_to'=>$_POST['reporting_manager_id'],'user_mobile'=>$_POST['phone'], 'role_id'=> json_encode($_POST['approval_role']));
	//print_r($user_data);
	$buddy_data = array('employee_id'=>$emp_id,'hr_buddy'=>$_POST['hr_buddy'],'finance_buddy'=>$_POST['finance_buddy'],'admin_buddy'=>$_POST['admin_buddy'],'loc_buddy'=>$_POST['loc_buddy']);

	$emp_id  = $_POST['emp_id'];
	$user_id  = $_POST['user_id'];
	$_POST['approval_manager_id'] = $_POST['reporting_manager_id'];
	$_POST['reporting_manager_id'] = $_POST['approval_manager'];
	unset($_POST['approval_manager']);
	//unset($_POST['reports_to']);
	unset($_POST['approval_role']);	
	unset($_POST['emp_id']);
	unset($_POST['user_id']);
	//unset($_POST['hr_buddy']);
	//unset($_POST['finance_buddy']);
	//unset($_POST['admin_buddy']);
	//unset($_POST['loc_buddy']);
    ///employee_buddies
    //echo $_POST['joining_date'];
	$_POST['joining_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['joining_date'])));
	if($_POST['first_working_date']!=""){ $_POST['first_working_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['first_working_date']))); }
	if ($_POST['date_of_leaving']!=""){  $_POST['date_of_leaving'] = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['date_of_leaving'])));	}
	if($_POST['date_of_birth']!=""){$_POST['date_of_birth'] = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['date_of_birth']))); }
	$_POST['billable_type'] = $_POST['billable']==1 ? $_POST['nonbillable_type'] : $_POST['billable_type'];
	unset($_POST['nonbillable_type']);
	//print_r($_POST);
	if($_POST['submit'] == 'Submit'){
		unset($_POST['submit']);
		$_POST['company_id'] = $_SESSION['company_id']; 
		$_POST['current_status'] = '0';
		$_POST['is_active'] = '0';
	//	print_r($_POST);
		$url = API."?mode=adddata&table=employees&method=post";
		$response = file_post_contents($url, $_POST, 'insert');
	//	echo $response;
		$resdata = json_decode($response,1);
		$buddy_data['employee_id'] = $resdata['LASTID'];
		$_GET['ECD'] = $_POST['emp_code'];
		
	}
	if($_POST['submit'] == 'Save'){
		unset($_POST['submit']);
		$url = API."?mode=updaterowtabledata&rid=".$emp_id."&table=employees&method=post";
		$response = file_post_contents($url, $_POST, 'update');
	}
	if($buddy_data['employee_id']!=""){
		$url = API."?mode=insertupdateemployeebuddies&method=post";
		$response = file_post_contents($url, $buddy_data, 'update');
	}	
	if($user_id !=""){
		$url = API."?mode=updaterowtabledata&rid=".$user_id."&table=application_users&method=post";
		$response = file_post_contents($url, $user_data, 'update');
	}
	if($_POST['emp_code']!=""){
		header("index.php?option=COM_VIEW_VMS_EMPLOYEE&ECD=".$_POST['emp_code']);
	} 
}
$emp_id ="";  $cc_val=""; 
if($_GET['Rid']!=""){
	$idvalue = explode(':',encodeId($_GET['Rid']));
	$emp_id = $idvalue[1];
	$cc_val=1;
}
if($_GET['ECD']!=""){
	$cc_val=1;
}
//$emp_id = "";
$employe_url  = API."?mode=togetvmsemployeedetails&employe_id=".$emp_id."&emp_code=".$_GET['ECD'];
$employe_data = callAPI($employe_url);
$employe_data = json_decode($employe_data, 1); 
$emp = $employe_data['employee'];
$emp_id  = $emp['id'];
$user = $employe_data['user'];
$buddy = $employe_data['employee_buddies'];
foreach($employe_data['esign'] as $esigdetails){
	$esig[$esigdetails['id']]  = $esigdetails;
}
foreach($employe_data['vrc'] as $vrcetails){
	$vrc[$vrcetails['id']]  = $vrcetails;
}
foreach($employe_data['mpc'] as $mpcdetails){
	$mpc[$mpcdetails['id']]  = $mpcdetails;
}
foreach($employe_data['msc'] as $mpcdetails){
	$msc[$mpcdetails['id']]  = $mpcdetails;
}
foreach($employe_data['vendors'] as $vendorsdetails){
	$vendors[$vendorsdetails['id']]  = $vendorsdetails;
}
foreach($employe_data['job_titles'] as $job_titlesdetails){
	$job_titles[$job_titlesdetails['id']]  = $job_titlesdetails;
}
foreach($employe_data['esign_items'] as $esign_items){
	$esign_item[$esign_items['id']]  = $esign_items;
	$sub_esign[$esign_items['transaction_id']][] = $esign_items;
}
foreach($employe_data['vrc_items'] as $vrc_items){
	$vrc_item[$vrc_items['id']]  = $vrc_items;
	$sub_vrc[$vrc_items['transaction_id']][] = $vrc_items;
}
foreach($employe_data['mpc_items'] as $mpc_items){
	$mpc_item[$mpc_items['id']]  = $mpc_items;
	$sub_mpc[$mpc_items['transaction_id']][] = $mpc_items;
}
foreach($employe_data['ms_items'] as $msc_items){
	$msc_item[$msc_items['id']]  = $msc_items;
	$sub_msc[$msc_items['transaction_id']][] = $msc_items;
}
$assets = $employe_data['assets'];
if($emp_id=="" && $_GET['ECD']!="" && COMPANY_LDAP=='Y' ){?>

	<script type="text/javascript">	
		swal.fire({        
			text:'<?php echo $_SESSION['error_message']['9042']; ?>',              
			type: "success",           
			buttonsStyling: false,
			confirmButtonText: "Yes, close! ",    
			confirmButtonClass: "btn btn-success",
			showCancelButton: true,     
			cancelButtonText: " No, continue",      
			cancelButtonClass: "btn btn-warning"        
		}).then(function(result) {
			if(!result.value) {
				window.location.href = 'index.php?option=COM_VIEW_VMS_EMPLOYEE';
			}
		}); 
	</script>

	<?php $ldap_dn = "CN=svc-idcvms,OU=Service Accounts,DC=mchp-main,DC=com";
//	$ldap_password = "2aRSp%5JyZQ.KUbF%8VqHbYQ2";
	$ldap_password = "mdss@axVcpgk%7c9S5OfLuRxc";
	$userid = $_GET['ECD'];
	$ldap_con = ldap_connect ("ldap://mchp-main.com:389");
	ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
	ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
	if(ldap_bind($ldap_con, $ldap_dn, $ldap_password)){
    //  echo "DONE";
		$result = ldap_search($ldap_con,"DC=mchp-main,DC=com","sAMAccountName=".$userid);
		$info = ldap_get_entries($ldap_con, $result);
		$emp['first_name'] = $info[0]['givenname']['0'];
		$emp['display_name'] = $info[0]['displayname']['0'];
		$emp['email_id'] = $info[0]['mail']['0'];
		$emp['emp_code'] = $info[0]['employeenumber']['0'];
	}
}

?>
<style type="text/css">
.vcs{
	font-size: 18px;
	padding: 8.5px;
}
th{
	vertical-align: middle;
}
.accor-icon .tab-pane .accordion-msg span.ui-accordion-header-icon{position: relative;}
.accor-icon .tab-pane .accordion-msg span.ui-accordion-header-icon:after{content: "\e64b"; font-family: 'themify'; font-size: 16px;}
.accor-icon .tab-pane .accordion-msg.ui-state-active span.ui-accordion-header-icon:after{content: "\e648";}
.fs-200{ width: 250px !important; } 
.input-group span label {
	cursor: pointer;
}
</style>
<script type="text/javascript">
	function addeditassets(emp_id){
		jQuery.ajax({	
			url: "views/custom_insert_emp_assets_popup.php",	
			data:'emp_id='+emp_id,	
			type: "POST",	
			success:function(data){	
				$('#dynamic_popupxxl').html(data);	
				$('#kt_modal_dynamic_popupxxl').modal("show");	
			},	
			error:function (){}	
		});
	}
	function toinsertandpdateesignemployeeonlyone(emp_id,rid,eba){
		jQuery.ajax({	
			url: "views/custom_esign_empassign_popup.php",	
			data:'emp_id='+emp_id+'&rid='+rid+'&eba='+eba,	
			type: "POST",	
			success:function(data){	
				$('#popupm').html(data);	
				$('#kt_modal_KTDatatable_remotem').modal("show");	
			},	
			error:function (){}	
		});	

	}
	function createuser(emp_id){
		jQuery.ajax({
			url: "ajax.php",
			data:'page=createuser&emp_id='+emp_id,
			type: "POST",
			success:function(data){
				console.log(data);
            /* var msg = <?php echo "'".$_SESSION['error_message']['9026']."'" ?>;
                    swal.fire({              
                        title: msg,              
                        type: "success",           
                        buttonsStyling: false,
                        confirmButtonText: "<i class='la la-hand-o-up'></i>OK",    
                        confirmButtonClass: "btn btn-info", 
                        showCancelButton: false,     
                        cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
                        cancelButtonClass: "btn btn-default"  
                    });*/
                },
                error:function (){}
            });
	}
	function vrcandpoemployeeassign(emp_id,rid,item,type){
		jQuery.ajax({	
			url: "views/custom_esign_purchaseorder_empassign_popup.php",	
			data:'emp_id='+emp_id+'&rid='+rid+'&type='+type+'&item='+item,	
			type: "POST",	
			success:function(data){	
				$('#popupm').html(data);	
				$('#kt_modal_KTDatatable_remotem').modal("show");	
			},	
			error:function (){}	
		});
	}
	function ldadataconnect(){
		var ecd  = $('#emp_code').val();
		if(ecd!=""){
			window.location.href = 'index.php?option=COM_VIEW_VMS_EMPLOYEE&ECD='+ecd;
		}else{
			swal.fire({              
				title: "Please Enter Employee Code",              
				type: "success",           
				buttonsStyling: false,
				confirmButtonText: "<i class='la la-hand-o-up'></i>OK",    
				confirmButtonClass: "btn btn-info", 
				showCancelButton: false,     
				cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
				cancelButtonClass: "btn btn-default"  
			});
		}
	}
	function billablechange(val){
		$('#billable_type').hide();
		$('#nonbillable_type').hide();
		if(val==0){
			$('#billable_type').show();
		}
		if(val==1){
			$('#nonbillable_type').show();
		}
	}
	function milstonedetail(emp_id, transaction_id, item_id){
		$.ajax({
			url: "ajax.php",
			data:'page=addupdateempassignmilestone&transaction_id='+transaction_id+'&item_id='+item_id+'&emp_id='+emp_id,
			type: "POST",
			success:function(data){
				//console.log(data);
				var returnres = JSON.parse(data);
				//console.log(returnres.check);
				if(returnres.check ==0){
					tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success pt-4 pb-4','animated flipInX','animated flipOutX',' Added On Mile Stone','');
				}else{
					tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger pt-4 pb-4','animated flipInX','animated flipOutX',' Removed From  Mile Stone','');
				}
			},
			error:function (){}
		});
	}
</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<div class="">
			<?php if($cc_val==""){ ?>
				<div class="row col-lg-12">
					<div class="col-lg-9">
						<div class="card card-custom card-sticky  ">
							<div class="table-responsive">
								<div class="card-header"><h5 class="text-primary">Basic Info</h5></div>
								
								<table class="table m-0">
									<tbody>

										<tr>
											<th scope="row">Employee Code</th>
											<td>
												<div class="input-group" style="margin-bottom: auto;">
													<input type="text" class="form-control vcs" id="emp_code" name="emp_code" value="<?php echo $emp['emp_code'] ?>">

													<span class="input-group-text addon-style" id="basic-addon3" onclick="ldadataconnect()">
														<label class="" onclick="ldadataconnect()"><i class="icon-magnifier"></i></label>
													</span>

												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="row col-lg-12" <?php if($cc_val==""){ ?> style="display:none;" <?php } ?> >
					<div class="col-lg-9">

						<form  id="kt_form" method="post" action="" name="myForm" enctype="multipart/form-data">
							<div class="row col-lg-12">
								<div class="card card-custom card-sticky  col-lg-6">
									<div class="table-responsive">
										<div class="card-header"><h5 class="text-primary">Basic Info</h5></div>
										<table class="table m-0">
											<tbody>

												<tr>
													<th scope="row">Employee Code</th>
													<td>
														<div class="input-group" style="margin-bottom: auto;">
															<input type="text" class="form-control vcs" id="emp_code" name="emp_code" value="<?php echo $emp['emp_code'] ?>">
															<?php //if(COMPANY_LDAP=='Y' && $emp_id ==""){?> 
																<span class="input-group-text addon-style" id="basic-addon3">
																	<label class="" onclick="ldadataconnect()"><i class="icon-magnifier"></i></label>
																</span>
																<?php //} ?>
															</div>
														</td>
													</tr>
													<tr>
														<th scope="row">Email Id</th>
														<td><input type="text" class="form-control vcs" name="email_id"  value="<?php echo $emp['email_id'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">First Name</th>
														<td><input type="text" class="form-control vcs" name="first_name"  value="<?php echo $emp['first_name'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">Middle Name</th>
														<td><input type="text" class="form-control vcs" name="middle_name"  value="<?php echo $emp['middle_name'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">Last Name</th>
														<td><input type="text" class="form-control vcs" name="last_name"  value="<?php echo $emp['last_name'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">Display Name</th>
														<td><input type="text" class="form-control vcs" name="display_name"  value="<?php echo $emp['display_name'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">Mobile Number</th>
														<td><input type="text" class="form-control vcs" name="phone"  value="<?php echo $emp['phone'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">Gender</th>
														<td>
															<div class="radio radio-outline vcs  mt-1">

																<label class="radio radio-inline"> <input type="radio" <?php if($emp['gender']=='F'){ echo "checked"; } ?> name="gender" value="F">	<span></span>FEMALE </label>

																<label class="radio radio-inline"> <input type="radio"  <?php if($emp['gender']=='M'){ echo "checked"; } ?> name="gender" value="M">	<span></span>MALE </label>
															</div>
														</td>
													</tr>
													<tr>
														<th scope="row">Date Of Birth </th>
														<td><input type="text" class="form-control datepicker vcs" name="date_of_birth" value="<?php echo $emp['date_of_birth'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">Total Experiance</th>
														<td><input type="text" class="form-control vcs" name="total_experiance"  value="<?php echo $emp['total_experiance'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">Skill Set</th>
														<td><input type="text" class="form-control vcs" name="skill_set"  value="<?php echo $emp['skill_set'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">Career Start Date</th>
														<td><input type="text" class="form-control datepicker vcs" name="first_working_date" value="<?php echo $emp['first_working_date'] ?>"></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
									<div class="card card-custom card-sticky   col-lg-6">
										<div class="table-responsive">
											<div class="card-header">
												<h5 class="text-primary">Work Info</h5>

												<?php if($emp['user_id']=="" && $emp_id !=""){ ?>
													<button type="" class="btn btn-warning font-weight-bolder text-uppercase float-right" onclick="createuser(<?php echo $emp['id']; ?>)" >Create User</button>  
												<?php } ?>
											</div>
											<table class="table m-0">
												<tbody>
													<tr>
														<th scope="row">Employee Type</th>
														<td>
															<div class="radio radio-outline vcs  mt-1">

																<label class="radio radio-inline"> <input type="radio" <?php if($emp['employee_type']=='1'){ echo "checked"; } ?> name="employee_type" value="1">     <span></span>Permanent </label>

																<label class="radio radio-inline"> <input type="radio"  <?php if($emp['employee_type']!='1'){ echo "checked"; } ?> name="employee_type" value="2">    <span></span>Contract </label>
															</div>
														</td>
													</tr>

													<tr>
														<th scope="row">Location</th>
														<td>
															<select class="form-select js-example-basic-single" name="location_id">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['locations'] as $location){ ?> 
																	<option <?php if($emp['location_id']== $location['id']){ echo "selected"; } ?>  value="<?php echo $location['id']; ?>"> <?php echo $location['location_name'];?></option>
																<?php } ?>
															</select>											
														</td>
													</tr>
													<tr>
														<th scope="row">Job Title</th>
														<td>
															<select class="form-select js-example-basic-single" name="job_title_id">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['job_titles'] as $job_title){ ?> 
																	<option <?php if($emp['job_title_id']== $job_title['id']){ echo "selected"; } ?>  value="<?php echo $job_title['id']; ?>"> <?php echo $job_title['job_title'];?></option>
																<?php } ?>
															</select>	
														</td>
													</tr>
													<tr>
														<th scope="row">Department</th>
														<td>
															<select class="form-select js-example-basic-single" name="department_id">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['departments'] as $department){ ?> 
																	<option <?php if($emp['department_id']== $department['id']){ echo "selected"; } ?>  value="<?php echo $department['id']; ?>"> <?php echo $department['department_name'];?></option>
																<?php } ?>
															</select>	
														</td>
													</tr>
													<tr>
														<th scope="row">Designation</th>
														<td>
															<select class="form-select js-example-basic-single" name="designation_id">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['designations'] as $designation){ ?> 
																	<option <?php if($emp['designation_id']== $designation['id']){ echo "selected"; } ?>  value="<?php echo $designation['id']; ?>"> <?php echo $designation['designation_name'];?></option>
																<?php } ?>
															</select>	
														</td>
													</tr>								
													<tr>
														<th scope="row">Cost Center</th>
														<td>
															<select class="form-select js-example-basic-single" name="cost_center_id">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['cost_centers'] as $designation){ ?> 
																	<option <?php if($emp['cost_center_id']== $designation['id']){ echo "selected"; } ?>  value="<?php echo $designation['id']; ?>"> <?php echo $designation['cost_center_name'];?></option>
																<?php } ?>
															</select>	
														</td>
													</tr>	
													<tr>
														<th scope="row">Vendor</th>
														<td>
															<select class="form-select js-example-basic-single" name="vendor_id">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['vendors'] as $vendor){ ?> 
																	<option <?php if($emp['vendor_id']== $vendor['id']){ echo "selected"; } ?>  value="<?php echo $vendor['id']; ?>"> <?php echo $vendor['display_name'];?></option>
																<?php } ?>
															</select>	
														</td>
													</tr>
													<tr>
														<th scope="row">Joining Date</th>
														<td><input type="text" class="form-control datepicker vcs" name="joining_date" value="<?php echo $emp['joining_date'] ?>"></td>
													</tr>
													<tr>
														<th scope="row">Date Of Leaving</th>
														<td><input type="text" class="form-control datepicker vcs" name="date_of_leaving" value="<?php echo $emp['date_of_leaving'] ?>"></td>
													</tr>


<tr>
	<?php $allbillable = array('0'=>'Billable', '1'=>'Non-Billable', '2'=>'Mile Stone'); ?>
	<th scope="row">Billable</th>
	<td>
		<select class="form-select js-example-basic-single" onchange="billablechange(this.value)" name="billable" id="billable">
			<option value="">Select Any One</option> 
			<?php foreach($allbillable as $billkey=>$bill){ ?> 
				<option value="<?php echo $billkey; ?>" <?php if($emp['billable']==$billkey){ echo "selected"; } ?>> <?php echo $bill; ?></option>
			<?php } ?>
		</select>
		
	</td>
</tr>
<tr <?php if($emp['billable']!=0){ ?>  style="display: none;" <?php } ?> id="billable_type">
	<?php $arraybillable = array('1'=>'Fixed', '2'=>'Timeline Material' ); ?>
	<th scope="row">Billable Type </th>
	<td>
		<select class="form-select js-example-basic-single" name="billable_type" id="billable_type">
			<option <?php if($emp['billable_type']==''){ echo "selected"; } ?>  value="">Select Any One</option> 
			<?php foreach($arraybillable as $billkey=>$bill){ ?> 
				<option value="<?php echo $billkey; ?>" <?php if($emp['billable_type']==$billkey){ echo "selected"; } ?>> <?php echo $bill; ?></option>
			<?php } ?>
		</select>
		
	</td>
</tr>
<tr <?php if($emp['billable']!=1){ ?>  style="display: none;" <?php } ?>id="nonbillable_type">
	<?php $arraynonbillable = array('3'=>'Bufer', '4'=>'Non-Billable'); ?>
	<th scope="row">Non Billable Type</th>
	<td>
		<select class="form-select js-example-basic-single" name="nonbillable_type" id="nonbillable_type">
			<option value="">Select Any One</option> 
			<?php foreach($arraynonbillable as $billkey=>$bill){ ?> 
				<option value="<?php echo $billkey; ?>" <?php if($emp['billable_type']==$billkey){ echo "selected"; } ?>> <?php echo $bill; ?></option>
			<?php } ?>
		</select>
		
	</td>
</tr>
<tr>
	<?php $skiltypes = array('1'=>'Technical', '2'=>'Non-Technical'); ?>
	<th scope="row">Skill</th>
	<td>
		<select class="form-select js-example-basic-single" name="skil_type" id="skil_type">
			<option value="">Select Any One</option> 
			<?php foreach($skiltypes as $billkey=>$bill){ ?> 
				<option value="<?php echo $billkey; ?>" <?php if($emp['skil_type']==$billkey){ echo "selected"; } ?>> <?php echo $bill; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>





													<?php if($emp['user_id']!=""){  ?>
														<tr>
															<th scope="row">Role</th>
															<td>
																<select class="form-select js-example-basic-multiple" name="approval_role[]">
																	<option value=""> Select Any One</option>
																	<?php  $userrole = json_decode($user['role_id'],1);
																	foreach($employe_data['application_roles'] as $aprole){ ?> 
																		<option <?php if(in_array($aprole['id'],$userrole)){ echo "selected"; } ?>  value="<?php echo $aprole['id']; ?>"> <?php echo $aprole['display_name'];?></option>
																	<?php } ?>
																</select>	
															</td>
														</tr>
														<tr>
															<th scope="row">Reporting Manager</th>
															<td>
																<select class="form-select js-example-basic-single" name="approval_manager">
																	<option value=""> Select Any One</option>
																	<?php foreach($employe_data['application_users'] as $apusers){ ?> 
																		<option <?php if($user['approval_manager']== $apusers['id']){ echo "selected"; } ?>  value="<?php echo $apusers['id']; ?>"> <?php echo $apusers['display_name'];?></option>
																	<?php } ?>
																</select>	
															</td>
														</tr>									
														<tr>
															<th scope="row">Approving Manager</th>
															<td>
																<select class="form-select js-example-basic-single" name="reporting_manager_id">
																	<option value=""> Select Any One</option>
																	<?php foreach($employe_data['application_users'] as $apusers){ ?> 
																		<option <?php if($user['reports_to']== $apusers['id']){ echo "selected"; } ?>  value="<?php echo $apusers['id']; ?>"> <?php echo $apusers['display_name'];?></option>
																	<?php } ?>
																</select>	
															</td>
														</tr>
													<?php } ?>
													<tr>
														<th scope="row">HR Buddy</th>
														<td>
															<select class="form-select js-example-basic-single" name="hr_buddy">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['employee_list'] as $apusers){ ?> 
																	<option <?php if($buddy['hr_buddy']== $apusers['id']){ echo "selected"; } ?>  value="<?php echo $apusers['id']; ?>"> <?php echo $apusers['display_name'];?></option>
																<?php } ?>
															</select>	
														</td>
													</tr>
													<tr>
														<th scope="row">Finance Buddy</th>
														<td>
															<select class="form-select js-example-basic-single" name="finance_buddy">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['employee_list'] as $apusers){ ?> 
																	<option <?php if($buddy['finance_buddy']== $apusers['id']){ echo "selected"; } ?>  value="<?php echo $apusers['id']; ?>"> <?php echo $apusers['display_name'];?></option>
																<?php } ?>
															</select>	
														</td>
													</tr>
													<tr>
														<th scope="row">Admin Buddy</th>
														<td>
															<select class="form-select js-example-basic-single" name="admin_buddy">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['employee_list'] as $apusers){ ?> 
																	<option <?php if($buddy['admin_buddy']== $apusers['id']){ echo "selected"; } ?>  value="<?php echo $apusers['id']; ?>"> <?php echo $apusers['display_name'];?></option>
																<?php } ?>
															</select>	
														</td>
													</tr>
													<tr>
														<th scope="row">Location  Buddy</th>
														<td>
															<select class="form-select js-example-basic-single" name="loc_buddy">
																<option value=""> Select Any One</option>
																<?php foreach($employe_data['employee_list'] as $apusers){ ?>
																	<option <?php if($buddy['loc_buddy']== $apusers['id']){ echo "selected"; } ?>  value="<?php echo $apusers['id']; ?>"> <?php echo $apusers['display_name'];?></option>
																<?php } ?>
															</select>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
									<div class=" text-right">
										<input type="hidden" name="emp_id" value="<?php echo $emp['id']; ?>">
										<input type="hidden" name="user_id" value="<?php echo $emp['user_id']; ?>">
										<?php if($emp_id != ""){ ?>
											<input type="submit" name="submit" value="Save" class="btn btn-primary text-rigt col-lg-2">
										<?php }else{ ?>
											<input type="submit" name="submit" value="Submit" class="btn btn-primary text-rigt col-lg-2">
										<?php } ?>
									</div>
								</form>
							</div>
							<div class="row col-lg-12 mt-5">						
								<div class="card card-custom card-sticky table-responsive">
									<div class="card-header"><h5 class="text-primary">Esign Details</h5></div>
									<table class="table">
										<tr>
											<thead>
												<td>#</td>
												<td>Esign Number</td>
												<td>Vendor Name</td>
												<td>Resources </td>
												<td>Base Rate </td>
												<td>Amount </td>
												<td>Valid  From</td>
												<td>Valid To</td>
											</thead>
										</tr>
										<tbody>
											<?php $i=1; foreach($employe_data['esgin_employees'] as $esgin_employees){ 
												$esign_eba[]= $esgin_employees['eba_vendors_allocation_id'];
												$check_esign[] = $esgin_employees['esgin_budget_approval_id']; ?>
												<tr>
													<td><?php echo $i; ?></td>
													<td><?php echo $esig[$esgin_employees['esgin_budget_approval_id']]['transaction_number']; ?></td>
													<td><?php echo $esgin_employees['eba_vendors_allocation_id']."-".$vendors[$esign_item[$esgin_employees['eba_vendors_allocation_id']]['item_id']]['display_name']; ?></td>
													<td><?php echo $esign_item[$esgin_employees['eba_vendors_allocation_id']]['item_quantity']; ?></td>
													<td><?php echo customeummarmonyformate($esign_item[$esgin_employees['eba_vendors_allocation_id']]['item_price']); ?></td>
													<td><?php echo customeummarmonyformate($esign_item[$esgin_employees['eba_vendors_allocation_id']]['total_amount']); ?></td>																			<td><?php echo $esig[$esgin_employees['esgin_budget_approval_id']]['effective_from_date']; ?></td>
													<td><?php echo $esig[$esgin_employees['esgin_budget_approval_id']]['effective_to_date']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="row col-lg-12 mt-5">
								<div class="card card-custom card-sticky table-responsive">
									<div class="card-header"><h5 class="text-primary">Vendor Rate Card Details</h5></div>
									<table class="table">
										<tr>
											<thead>
												<td>#</td>
												<td>VRC Number</td>
												<td>Job Title</td>
												<td>Resources </td> 
												<td> Unit </td>
												<td>Base Rate </td>
												<td>Amount </td>
												<td>Valid From</td>
												<td>Valid To</td>
											</thead>
										</tr>
										<tbody>
											<?php $i=1; foreach($employe_data['emp_work_order'] as $esgin_employees){ 
												$vrc_eba[]= $esgin_employees['vrc_item_id'];
												$check_vrc[] = $esgin_employees['vrc_id'];  ?>
												<tr>
													<td><?php echo $i; ?></td>
													<td><?php echo $vrc[$esgin_employees['vrc_id']]['transaction_number']; ?></td>
													<td><?php echo $job_titles[$vrc_item[$esgin_employees['vrc_item_id']]['item_id']]['job_title']."(".$vrc_item[$esgin_employees['vrc_item_id']]['min_exp']." - ".$vrc_item[$esgin_employees['vrc_item_id']]['max_exp'].")"; ?></td>
													<td><?php echo $vrc_item[$esgin_employees['vrc_item_id']]['item_quantity']; ?></td>
													<td><?php echo $vrc_item[$esgin_employees['vrc_item_id']]['purchase_uom']; ?></td>
													<td><?php echo customeummarmonyformate($vrc_item[$esgin_employees['vrc_item_id']]['item_price']); ?></td>
													<td><?php echo customeummarmonyformate($vrc_item[$esgin_employees['vrc_item_id']]['total_amount']); ?></td>
													<td><?php echo $esgin_employees['effective_from_date']; ?></td>
													<td><?php echo $esgin_employees['effective_to_date']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="row col-lg-12 mt-5">

								<div class="card card-custom card-sticky table-responsive">
									<div class="card-header"><h5 class="text-primary">Purchase Order Details</h5></div>
									<table class="table">
										<tr>
											<thead>
												<td>#</td>
												<td>Purchase Order Number</td>
												<td>Job Title</td>
												<td>Resources </td>
												<td>Base Rate </td>
												<td>Amount </td>
												<td>Valid From</td>
												<td>Valid To</td>
											</thead>
										</tr>
										<tbody>
											<?php $i=1; foreach($employe_data['emp_work_order'] as $esgin_employees){ 
												$mpc_eba[]= $esgin_employees['work_order_detail_id'];
												$check_mpc[] = $esgin_employees['work_order_id']; ?>
												<tr>
													<td><?php echo $i; ?></td>
												<td><?php echo $mpc[$esgin_employees['work_order_id']]['transaction_number']; ?></td>
													<td><?php echo $esgin_employees['work_order_detail_id']."-".$job_titles[$mpc_item[$esgin_employees['work_order_detail_id']]['item_id']]['job_title']; ?></td>
													<td><?php echo customeummarmonyformate($mpc_item[$esgin_employees['work_order_detail_id']]['item_quantity']); ?></td>
													<td><?php echo customeummarmonyformate($mpc_item[$esgin_employees['work_order_detail_id']]['item_price']); ?></td>
													<td><?php echo customeummarmonyformate($mpc_item[$esgin_employees['work_order_detail_id']]['total_amount']); ?></td>

													<td><?php echo $esgin_employees['effective_from_date']; ?></td>
													<td><?php echo $esgin_employees['effective_to_date']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							
							<div class="row col-lg-12 mt-5">

								<div class="card card-custom card-sticky table-responsive">
									<div class="card-header"><h5 class="text-primary">Milestone Details</h5></div>
									<table class="table">
										<tr>
											<thead>
												<td>#</td>
												<td>Milestone Number</td>
												<td>Phase</td>
												<td>Descrition </td>
												<td>Amount </td>
												<td>Valid From</td>
												<td>Valid To</td>
											</thead>
										</tr>
										<tbody>
<?php $i=1; foreach($employe_data['emp_stone'] as $esgin_employees){ 
												$msc_emp[]= $esgin_employees['transation_id'];
												$check_msc[] = $esgin_employees['id']; ?>
												<tr>
													<td><?php echo $i; ?></td>
													<td><?php echo $msc[$esgin_employees['transation_id']]['transaction_number']; ?></td>
													<td><?php echo $esgin_employees['item_id']; ?></td>
													<td><?php echo $msc_item[$esgin_employees['transation_id']]['account_code']; ?></td>
													<td><?php echo $msc_item[$esgin_employees['transation_id']]['descrption']; ?></td>
													<td><?php echo customeummarmonyformate($msc_item[$esgin_employees['transation_id']]['item_quantity']); ?></td>			
													<td><?php echo $esgin_employees['effective_from_date']; ?></td>
													<td><?php echo $esgin_employees['effective_to_date']; ?></td>
												</tr>
											<?php } ?>

										</tbody>
									</table>
								</div>
							</div>
							<?php if($_SESSION['config']['assets_mode']==0){ ?>
							<div class="row col-lg-12 mt-5">								
								<div class="card card-custom card-sticky table-responsive">
									<div class="card-header">
										<h5 class="text-primary">Assets</h5>
										 <a href="#" onclick="addeditassets(<?php echo $emp_id; ?>)" class="mb-2  ml-1 float-right"><button class="btn btn-primary  m-l-10">Add / Edit Asset</button> </a>
									</div>
									<table class="table">
										<tr>
											<thead>
												<td>#</td>
												<td>Asset Code</td>
												<td>Asset Type</td>
												<td>Allocate Date</td>
												<td>Return Date </td>
												<td>Status </td>
											</thead>
										</tr>
										<tbody>
											<?php $i=1; 
											$type = array('E'=>'Employees', 'CC'=>'Cost Cneters','D'=>'Departments');
											print_r($employe_data['emp_assets']);
												foreach($employe_data['emp_assets'] as $emp_assets){ ?>
												<tr>
													<td><?php echo $i; ?></td>
													<td><?php echo $assets[$emp_assets['asset_id']]['asset_code']; ?></td>
													<td><?php echo $type[$emp_assets['allocation_type']];  ?></td>
													<td><?php echo $emp_assets['start_date']; ?></td>
													<td><?php echo $emp_assets['end_date']; ?></td>
													<td><?php if($emp_assets['current_status']==31){ echo "Allocated"; } if($emp_assets['current_status']==32){ echo "Returned"; } ?> </td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							<?php } ?>

						</div>
						<div class=" col-lg-3">
							<div class="card">
								<div class="card-block">
									<ul class="nav nav-tabs tabs" role="tablist">

										<li class="nav-item">
											<a class="nav-link active " data-bs-toggle="tab" href="#block-tab" role="tab" aria-selected="false">Vendor Rate </a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#page-tab" role="tab" aria-selected="false">Purchase </a>
										</li>
										<li class="nav-item">
											<a class="nav-link " data-bs-toggle="tab" href="#services-tab" role="tab" aria-selected="true">LN Details</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#Milestone-tab" role="tab" aria-selected="false">Milestone </a>
										</li>	
									</ul>
									<div class="tab-content tabs mt-3 accor-icon">

										<div class="tab-pane " id="services-tab" role="tabpanel">
											<div class="row mb-2" >  	<a href="index.php?option=COM_INSERT_ESGIN_BUDGET_APPROVALS" target="_blank"> <button class="btn btn-success mb-2 desktop-view float-right">  <i class="icofont icofont-plus icon-white "></i> Esign</button></a></div>
											<div class="accordion-box" id="single-open">
												<?php foreach($employe_data['esign'] as $dis_esign){ ?>
													<a class="accordion-msg waves-effect waves-dark border-bottom <?php if(in_array($dis_esign['id'],$check_esign)){ echo "bg-success"; } ?> ">   <?php echo $dis_esign['transaction_number']; ?></a>
													<div class="accordion-desc mt-3">
														<?php foreach($sub_esign[$dis_esign['id']] as $sub){ ?>
															<div class="row">
																<div class="col-lg-2">
																	<div class="checkbox-fade fade-in-primary">
																		<label class="form-label"><label class="form-label">
																			<input class="border-checkbox1" <?php if(in_array($sub['id'],$esign_eba)){ echo "checked disabled"; } ?>  onclick="toinsertandpdateesignemployeeonlyone(<?php echo $emp_id.",".$dis_esign['id'].",".$sub['id']; ?>)" type="checkbox" id="checkbox1">
																			<span class="cr">
																				<i class="cr-icon icofont icofont-ui-check txt-primary"></i>
																			</span>  </label>
																		</div>
																	</div>
																	<div class="col-lg-10">
																		<p>
																			<span class="mytooltip tooltip-effect-5 ">
																				<span class=""><?php echo $vendors[$sub['item_id']]['display_name']; ?></span>
																				<span class="tooltip-content fs-200  clearfix">
																					<span class="tooltip-text">
																						Valid From : <?php echo $dis_esign['effective_from_date']; ?></br> 
																						Valid To: <?php echo $dis_esign['effective_to_date'] ?></br>
																						Base Rate : <?php echo customeummarmonyformate($sub['item_price']); ?>
																					</span>
																				</span>
																			</span>
																		</p>
																	</div>
																</div>
															<?php } ?>
														</div>
													<?php } ?>
												</div>
											</div>
											<div class="tab-pane active" id="block-tab" role="tabpanel">
												<div class="row mb-2" >  	<a href="index.php?option=COM_INSERT_VENDOR_RATE_CARDS" target="_blank"> <button class="btn btn-success mb-2 desktop-view float-right">  <i class="icofont icofont-plus icon-white "></i> Vendor Rate Card</button></a></div>
												<div class="accordion-box" id="sclae-accordion">
													<?php foreach($employe_data['vrc'] as $dis_esign){ ?>
														<a class="accordion-msg waves-effect waves-dark border-bottom <?php if(in_array($dis_esign['id'],$check_vrc)){ echo "bg-success"; } ?>"><?php echo $dis_esign['transaction_number']; ?></a>
														<div class="accordion-desc mt-3">
															<?php foreach($sub_vrc[$dis_esign['id']] as $sub){ ?>
																<div class="row">
																	<div class="col-lg-2">
																		<div class="border-checkbox-group border-checkbox-group-primary">
																			<input class="border-checkbox" <?php if(in_array($sub['id'],$vrc_eba)){ echo "checked disabled "; } ?>  onclick="vrcandpoemployeeassign(<?php echo $emp_id.",".$dis_esign['id'].",".$sub['id'].",'vrc'"; ?>)" type="checkbox" id="checkbox1">
																		</div>
																	</div>
																	<div class="col-lg-10">
																		<p>
																			<span class="mytooltip tooltip-effect-5">
																				<span class=""><?php echo $job_titles[$sub['item_id']]['job_title']." (".$sub['min_exp']." - ".$sub['max_exp'].")"; ?></span>
																				<span class="tooltip-content fs-200  clearfix">
																					<span class="tooltip-text">
																						Valid From : <?php echo $dis_esign['effective_from_date']; ?></br> 
																						Valid To: <?php echo $dis_esign['effective_to_date'] ?></br>
																						Base Rate : <?php echo customeummarmonyformate($sub['item_price']); ?></br>
																						Experience: <?php echo $sub['min_exp']." - ".$sub['max_exp']; ?>
																					</span>
																				</span>
																			</span>
																		</p>
																	</div> 
																</div>
															<?php } ?>
														</div>
													<?php } ?>
												</div>
											</div>
											<div class="tab-pane" id="page-tab" role="tabpanel">
												<div class="row mb-2" >  	<a href="index.php?option=COM_INSERT_MANPOWER_CONTRACTS" target="_blank"> <button class="btn btn-success mb-2 desktop-view float-right">  <i class="icofont icofont-plus icon-white "></i> Purchase Orders</button></a></div>
												<div class="accordion-box" id="color-accordion">
													<?php foreach($employe_data['mpc'] as $dis_esign){ ?>
														<a class="accordion-msg waves-effect waves-dark border-bottom <?php if(in_array($dis_esign['id'],$check_mpc)){ echo "bg-success"; } ?>"><?php echo $dis_esign['transaction_number']; ?></a>
														<div class="accordion-desc mt-3">
															<?php foreach($sub_mpc[$dis_esign['id']] as $sub){ ?>
																<div class="row">
																	<div class="col-lg-2">
																		<div class="border-checkbox-group border-checkbox-group-primary">
																			<input class="border-checkbox" <?php if(in_array($sub['id'],$mpc_eba)){ echo "checked disabled"; } ?>  onclick="vrcandpoemployeeassign(<?php echo $emp_id.",".$dis_esign['id'].",".$sub['id'].",'mpc'"; ?>)" type="checkbox" id="checkbox1">
																		</div>
																	</div>
																	<div class="col-lg-10">
																		<p>
																			<span class="mytooltip tooltip-effect-5 ">
																				<span class=""><?php echo $job_titles[$sub['item_id']]['job_title']." (".$sub['min_exp']." - ".$sub['max_exp'].")"; ?></span>
																				<span class="tooltip-content fs-200  clearfix">
																					<span class="tooltip-text">
																						Valid From : <?php echo $dis_esign['effective_from_date']; ?></br> 
																						Valid To: <?php echo $dis_esign['effective_to_date'] ?></br>
																						Base Rate : <?php echo customeummarmonyformate($sub['item_price']); ?></br>
																						Experience: <?php echo $sub['min_exp']." - ".$sub['max_exp']; ?>
																					</span>
																				</span>
																			</span>
																		</p>
																	</div>


																</div>
															<?php } ?>
														</div>
													<?php } ?>
												</div>
											</div>
<div class="tab-pane" id="Milestone-tab" role="tabpanel">
        <div class="row mb-2" >        
                <a href="index.php?option=COM_INSERT_MILESTONE_CONTRACTS" target="_blank">       <button class="btn btn-success mb-2 desktop-view float-right">  <i class="icofont icofont-plus icon-white "></i> Miles Stone</button>
                </a>
        </div>
        <div class="accordion-box" id="color-accordion">
                <?php foreach($employe_data['msc'] as $dis_esign){ ?>
                        <a class="accordion-msg waves-effect waves-dark border-bottom <?php if(in_array($dis_esign['id'],$check_mpc)){ echo "bg-success"; } ?>"><?php echo $dis_esign['transaction_number']; ?></a>
                        <div class="accordion-desc mt-3">
                        <?php foreach($sub_msc[$dis_esign['id']] as $sub){ ?>
                        <div class="row">
                                <div class="col-lg-2">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox" <?php if(in_array($sub['id'],$msc_emp)){ echo "checked disabled"; } ?> onclick="milstonedetail(<?php echo $emp_id.",".$dis_esign['id'].",".$sub['id']; ?>)" type="checkbox" id="checkbox1">
                                        </div>
                                </div>
                                <div class="col-lg-10">
                                <p>
                                <span class="mytooltip tooltip-effect-5 ">
                                        <span class=""><?php echo $sub['description']; ?></span>
                                        <span class="tooltip-content fs-200  clearfix">
                                                <span class="tooltip-text">
                                                Phase : <?php echo $sub['account_code']; ?>
                                                Description : <?php echo $sub['description']; ?>
                                                Valid From : <?php echo $dis_esign['effective_from_date']; ?></br> 
                                                Valid To: <?php echo $dis_esign['effective_to_date'] ?></br>
                                                Base Rate : <?php echo customeummarmonyformate($sub['item_price']); ?></br>
                                                </span>    
                                        </span>                                        
                                </p> 
                        </div>
                </div>
                <?php } ?>  
        </div>
        <?php } ?>
</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
