<?php
if($_POST['ajaxcallemployee'] =="Y"){
	$employee_id = $_POST['employee_id'];
	include_once('../includes/session.php');
	include_once('../dbconfig.php');
	include_once('../comm.php');
	$url = API."?mode=getemployees&employee_id=".$employee_id."&working_role_short_code=".$_SESSION['working_role_short_code']; 
	$response = callAPI($url);
	$result = json_decode($response,1);
	//echo "<pre>"; print_r($result); echo "</pre>";
	$data = $result['employees'];
	//echo "<pre>"; print_r($result); echo "</pre>";
}
$employees_display_fields = $result['employees_display_fields'];
$vafield = json_decode($employees_display_fields['field_list'],1);
$departments = $result['departments'];
$designations = $result['designations'];
$divisions     = $result['divisions'];
$locations = $result['locations'];
$employment_bands = $result['employment_bands'];
$employment_levels = $result['employment_levels'];
$employment_roles = $result['employment_roles'];
$application_roles = $result['application_roles'];
$marital_statuses = $result['marital_statuses'];
$blood_groups = $result['blood_groups'];
$verticals = $result['verticals'];
$gender_details = $result['gender_details'];
$employeement_types = $result['employeement_types'];
$education_type = $result['education_type'];
$banks = $result['banks'];
$emp_educations = $result['emp_educations'];
$emp_dependents = $result['emp_dependents'];
$emp_contacts = $result['emp_contacts'];
$emp_work_experiences = $result['emp_work_experiences'];
$relations = $result['relations'];
$application_users = $result['application_users'];
$all_employees = $result['all_employees'];
$countries = $result['countries'];
$emp_visa = $result['emp_visa'];
$emp_skills = $result['emp_skills'];
$skills = $result['skills'];
$teams =  $result['teams'];
$emp_banks = $result['emp_banks'];
$emp_bank_status = $result['emp_bank_status'];
$employee_access = $result['employee_access'];
$employee_other_access = $result['employee_other_access'];
$statuse = ViewStatusData();
//echo "<pre>"; print_r($emp_bank_status); echo "</pre>";

//$employment_bands_details = $employee_basic_details['employment_bands_details'];
//$verticals_details = $employee_basic_details['verticals_details'];

//$employee_type = array("1"=>"Permanent","2"=>"Contract"," "=>"Not Defined");
$billable = array("0"=>"Yes","1"=>"No"," "=>"Not Defined");
$yesno = array('0'=>'Yes','1'=>'No');

$birthDate = new DateTime($data['date_of_birth']);
$current = new DateTime(date('Y-m-d'));
$interval = $birthDate->diff($current);
$agemonth = ($interval->m < 10 && $interval->m >= 0) ? '0'.$interval->m:$interval->m;
$agecalc = $interval->y.".".$agemonth;
$bank_work_flow = "Y";
?>
<script type="text/javascript">
	function tochangedeparment(){
		
	}
	function employementviewhistoryppup(emp_id) {
		jQuery.ajax({
			url: "views/custom_view_employment_type_details.php",
			data:'emp_id='+emp_id,
			type: "GET",
			success:function(data){
				console.log("Chelll");
				$('#popupsq').html(data);
				$('#kt_modal_KTDatatable_remotesq').modal("show");
			},
			error:function (){}
		});
	}
	function employeementtypepopup(emp_id, val) {
		console.log('emp_id='+emp_id+'&type='+val);
		jQuery.ajax({
			url: "views/custom_insert_employment_type_details.php",
			data:'emp_id='+emp_id+'&type='+val,
			type: "GET",
			success:function(data){
				$('#popupsq').html(data);
				$('#kt_modal_KTDatatable_remotesq').modal("show");
			},
			error:function (){}
		});
	}
</script>
<?php if($_POST['edittype'] =="VIEW"){ ?>
	<?php if($_POST['pagetype'] =="basic_information" || $_POST['showpagetype'] =="basic_information"){ ?>
		<?php 
	       	$fieldaccesright = $vafield['basic_information'];   
	    ?>
	    <div class="row">
	    	<?php if($fieldaccesright['field']['emp_code']!="NA"){  ?>
	    	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Employee Code'); ?> </span>
	          	<p> <?php echo emptytextdashShow($data['emp_code']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['email_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Email'); ?></span>
	          	<p> <?php echo emptytextdashShow(renderSensitiveDataWithToggle($data['email_id'], 'email')); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['first_name']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('First Name'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['first_name']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['middle_name']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Middle Name'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['middle_name']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['last_name']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Last Name'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['last_name']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['phone']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Contact'); ?></span>
	          	<p> <?php echo emptytextdashShow(renderSensitiveDataWithToggle($data['phone'], 'mobile')); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['address']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Permanent Address'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['address']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['contact_address']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Current Address'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['contact_address']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['post_code']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Postcode'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['post_code']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['fax']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Fax'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['fax']); ?></p>
	      	</div>
	      <?php } ?>
	    </div>
	<?php }elseif($_POST['pagetype'] =="work_details" || $_POST['showpagetype'] =="work_details"){ ?>
		<?php 
	       	$fieldaccesright = $vafield['work'];   
	    ?>
	    <div class="row">
	    	<?php if($fieldaccesright['field']['department_id']!="NA"){  ?>
	    	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Department'); ?> </span>
	          	<p> <?php echo emptytextdashShow($departments[$data['department_id']]['department_name']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['designation_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Designation'); ?></span>
	          	<p>  <?php echo emptytextdashShow($designations[$data['designation_id']]['designation_name']);?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['division_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Division'); ?></span>
	          	<p>  <?php echo emptytextdashShow($divisions[$data['division_id']]['division_name']);?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['band_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Band'); ?></span>
	          	<p> <?php echo emptytextdashShow($employment_bands[$data['band_id']]['band_name']);?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['level_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('level'); ?></span>
	          	<p>  <?php echo emptytextdashShow($employment_levels[$data['level_id']]['level_name']);?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['vertical_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Vertical'); ?></span>
	          	<p>  <?php echo emptytextdashShow($verticals[$data['vertical_id']]['vertical_name']);?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['hiring_source']!="NA"){ ?>
		  	<div class="col-xl-6 form-group" >
		        <span class="form-label d-block fw-bold"><?php echo __('Hiring Source'); ?>  </span>
		        <p><?php echo emptytextdashShow($data['hiring_source']); ?></p>
		    </div>
	      	<?php }if($fieldaccesright['field']['joining_date']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Date of Joining'); ?></span>
	          	<p>  <?php echo $data['joining_date'] !=""?fromDate($data['joining_date']):"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['experience_current_organization']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Current Organization Experience'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['experience_current_organization']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['total_experiance']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Total Experience'); ?></span>
	          	<p> <?php echo $data['total_experiance'] !=""?$data['total_experiance']."- Year(s)":"-"; ?> </p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['relevant_experience']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Relevant Experience'); ?></span>
	          	<p> <?php echo $data['relevant_experience'] !=""?$data['relevant_experience']."- Year(s)":"-"; ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['notice_period']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Notice Period'); ?></span>
	          	<p> <?php echo $data['notice_period'] !=""?$data['notice_period']."- Day(s)":" - "; ?> </p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['location_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Work Location'); ?></span>
	          	<p>  <?php echo emptytextdashShow($locations[$data['location_id']]['location_name']);?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['employee_type']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Employment Type'); ?></span>
	          	<p> <?php echo $data['employee_type']!=""?emptytextdashShow($employeement_types[$data['employee_type']]['genc_detail_name']):"-";?></p>
	      	</div> 
	      	<?php }if($fieldaccesright['field']['billable']!="NA"){  ?>
	      	<div class="col-xl-6 form-group"> 
	          	<span class="form-label d-block fw-bold"><?php echo __('Billable'); ?></span>
	          	<p>  <?php echo emptytextdashShow($billable[$data['billable']]); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['office_phone']!="NA"){  ?>
			<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Office Phone'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['office_phone']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['office_ext']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Extension'); ?></span>
	          	<p> <?php echo emptytextdashShow($data['office_ext']); ?></p>
	      	</div>
	      	<?php } ?>
	      	
	    </div>
	<?php }elseif($_POST['pagetype'] =="personal_details" || $_POST['showpagetype'] =="personal_details"){
		$fieldaccesright = $vafield['personal_details']; 
	?>
		<div class="row">
			<?php if($fieldaccesright['field']['date_of_birth']!="NA"){  ?>
	    	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Date of Birth'); ?> </span>
	          	<p> <?php echo dateFormate($data['date_of_birth']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['age']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Age'); ?> </span>
	          	<p> <?php echo $agecalc; ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['marital_status']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Marital Status'); ?> </span>
	          	<p> <?php echo emptytextdashShow($marital_statuses[$data['marital_status']]['marital_status_name']); ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['wedding_date']!="NA"){  ?>
	      	<div class="col-xl-6 form-group" style="<?php echo $data['marital_status'] =="2"?"":"display: none"; ?>">
	          	<span class="form-label d-block fw-bold"><?php echo __('Marrige Date'); ?></span>
	          	<p> <?php echo $data['wedding_date'] !=""?fromDate($data['wedding_date']):"-"; ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['gender']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Gender'); ?> </span>
	          	<p> <?php echo emptytextdashShow($gender_details[$data['gender']]['genc_detail_name']);?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['blood_group']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Blood Group'); ?> </span>
	          	<p> 	<?php echo $blood_groups[$data['blood_group']]['blood_group']!=""?$blood_groups[$data['blood_group']]['blood_group']:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['disability']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Disability'); ?> </span>
	          	<p> 	<?php echo $data['disability'] !=""?$yesno[$data['disability']]:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['disability_percentage']!="NA"){  ?>
	      	<div class="col-xl-6 form-group" style="<?php echo $data['disability'] =="0"?"":"display: none"; ?>">
	          	<span class="form-label d-block fw-bold"><?php echo __('Disability percentage'); ?> </span>
	          	<p> <?php echo $data['disability_percentage'] !=""?$data['disability_percentage']:"-"; ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['work_textile_industry']!="NA"){  ?>
	      	<div class="col-xl-6 form-group" >
	          	<span class="form-label d-block fw-bold"><?php echo __('Working In Textile Industry'); ?></span>
	          	<p><?php echo $data['work_textile_industry'] !=""?$yesno[$data['work_textile_industry']]:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['physically_challenged_children']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Physically Challenged Children'); ?></span>
	          	<p><?php echo $data['physically_challenged_children'] !=""?$yesno[$data['physically_challenged_children']]:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['soc_facebook']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Facebook Link'); ?> </span>
	          	<p> <?php  echo emptytextdashShow($data['soc_facebook']);  ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['soc_twitter']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Twitter Link'); ?> </span>
	          	<p> <?php  echo emptytextdashShow($data['soc_twitter']);  ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['soc_linkedin']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Linkedin Link'); ?> </span>
	          	<p> <?php  echo emptytextdashShow($data['soc_linkedin']);  ?></p>
	      	</div> 	
	      <?php } ?>
	    </div>
	<?php }elseif($_POST['pagetype'] =="bank_details" || $_POST['showpagetype'] =="bank_details"){
		$fieldaccesright = $vafield['bank_details']; 
	?>
		<div class="row">
			<?php 
			if($bank_work_flow == 'Y'){
			if(count((array)$emp_banks) !="0"){
				foreach ($emp_banks as $emp_bank) {  ?>
				<div class="col-xl-4 col-md-4" >
		            <div class="card social-res-card">
		                <div class="card-body">
		                    <label class="form-label d-block "><?php echo __('Bank'); ?> 
		                    	<?php if($fieldaccesright['access'] !="V" AND $fieldaccesright['field']['bank_details']!="V"){ ?>
		                    	<?php if($emp_bank['activation_status'] == 'Y'){ ?>
		                    		<span class="form-label label label-warning float-end d-inline-block fw-bold"><?php echo __('Active'); ?></span>
		                    	<?php }else{ 
		                    		$visiblebtnstatus =array(ST_ACTIVE,ST_SEND_BACK,ST_CALLBACK);
		                    		if(in_array($emp_bank['current_status'], (array)$visiblebtnstatus)){
		                    		?>
		                    			<span role="button" tabindex="0" aria-label="<?= htmlspecialchars(__('Add bank request'), ENT_QUOTES, 'UTF-8'); ?>" onkeydown="if(event.target===this&&(event.key==='Enter'||event.key===' ')){event.preventDefault();this.click();}" class="float-end cursor-pointer" onclick="addBankRequest('<?php echo $emp_bank['id']; ?>')"><i class="ti-pencil"></i></span>
		                    	<?php }else{ 
		                    		$stausresult = $statuse[$emp_bank['current_status']];
		                    		?>
		                    		<span class="form-label label float-end d-inline-block fw-bold" style="color: <?php echo "#".$stausresult['color']; ?>;    background:  <?php echo $stausresult['background_color']; ?>; border-color: <?php echo $stausresult['background_color']; ?>;" ><?php echo $emp_bank_status[$emp_bank['current_status']]['name']; ?></span>
		                    		
		                    	<?php } } }?>
		                    </label>
		                   	<p><?php echo $banks[$emp_bank['bank_id']]['bank_name']!=""?$banks[$emp_bank['bank_id']]['bank_name']:"-";?></p>
		                   	
		                   	<span class="form-label d-block fw-bold"><?php echo __('IFSC Code'); ?></span>
		                    <p> <?php  echo renderSensitiveDataWithToggle($data['bank_link'], 'ifsc', false);  ?></p>
		                    
		                    <span class="form-label d-block fw-bold"><?php echo __('Account No'); ?></span>
		                   	<p> <?php  echo renderSensitiveDataWithToggle($data['bank_account_no'], 'bank_account', false);  ?></p>

		                </div>
		            </div>
		        </div>
		    <?php } ?>
		    <?php }else{ ?>
				<div class="card-px text-center py-20 mt-2">
		            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> <?php echo __('No Employee Banks'); ?> </p>  
		            <div class="text-center px-4">
		                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
		            </div>   
		        </div>
			<?php } ?>	
			<?php }else{
			 if($fieldaccesright['field']['bank_id']!="NA"){  ?>
	    	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Bank'); ?> </span>
	          	<p> <?php echo $banks[$data['bank_id']]['bank_name']!=""?$banks[$data['bank_id']]['bank_name']:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['bank_link']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('IFSC Code'); ?> </span>
	          	<p> <?php  echo emptytextdashShow($data['bank_link']);  ?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['bank_account_no']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Account No'); ?> </span>
	          	<p> <?php  echo emptytextdashShow($data['bank_account_no']);  ?></p>
	      	</div>
	      <?php } } ?>
	    </div> 

	    <?php }elseif($_POST['pagetype'] =="proof_details" || $_POST['showpagetype'] =="proof_details"){
		$fieldaccesright = $vafield['proof_details'];
		$attached_files = json_decode($data['attached_files'],1);
	?>


		<table class="table">
           	<thead>
              	<tr>
                 	<th><?php echo __('Type'); ?></th>
                  	<th ><?php echo __('Number'); ?></th>
                 	<th><?php echo __('Expiry Date'); ?></th>
                 	<th><?php echo __('File'); ?></th>
              	</tr>
           	</thead>
           	<tbody>
              	<?php 
                	if($fieldaccesright['field']['adhar_no']!="NA"){  
              	?>
              	<tr>
                 	<th class="p-2"><?php echo __('Aadhar Card'); ?></th>
                 	<td class="p-2"><?php echo renderSensitiveDataWithToggle($data['adhar_no'], 'aadhaar'); ?></td>
                 	<td class="p-2"> - </td>
                 	<td class="p-2"> 
                 	<?php if($attached_files['aadhar_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['aadhar_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> <?php //echo $attached_files['aadhar_attachment']; ?></a>
                	<?php }else{ echo "-"; } ?>
                	 </td>
	            </tr>
	            <?php }if($fieldaccesright['field']['passport']!="NA"){  ?>
	            <tr>
	                <th class="p-2"><?php echo __('Passport'); ?></th>
	                <td class="p-2"><?php echo renderSensitiveDataWithToggle($data['passport'], 'passport'); ?></td>
	                <td class="p-2"><?php echo ($data['passport_expiry_date']!="0000-00-00" AND $data['passport_expiry_date']!="")?fromDate($data['passport_expiry_date']):" - "; ?></td>
	                <td class="p-2">
	                <?php if($attached_files['passport_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['passport_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> <?php //echo $attached_files['passport_attachment']; ?></a>
                	<?php }else{ echo "-"; } ?>
                	 </td>
	            </tr>
	            <?php }if($fieldaccesright['field']['driving_licence']!="NA"){  ?>
	            <tr>
	                <th class="p-2"><?php echo __('Driving Licence'); ?></th>
	                <td class="p-2"><?php echo renderSensitiveDataWithToggle($data['driving_licence'], 'driving_licence'); ?></td>
	                <td class="p-2"><?php echo ($data['driving_licence_expiry_date']!="0000-00-00" AND $data['driving_licence_expiry_date']!="")?fromDate($data['driving_licence_expiry_date']):" - "; ?></td>
	                <td class="p-2"> 
		                <?php if($attached_files['driving_licence_attachment'] !=""){ ?>
	                	<a href="uploads/all/<?php echo $attached_files['driving_licence_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> <?php //echo $attached_files['driving_licence_attachment']; ?></a>
	                	<?php }else{ echo "-"; } ?>
                	</td>
	             </tr>
	             <?php }if($fieldaccesright['field']['pan_number']!="NA"){  ?>
	             <tr>
	                <th class="p-2"><?php echo __('PAN Number'); ?></th>
	                <td class="p-2"><?php echo renderSensitiveDataWithToggle($data['pan_number'], 'pan'); ?> </td>
	                <td class="p-2"> - </td>
	                <td class="p-2"> 
	                	<?php if($attached_files['pan_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['pan_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> <?php //echo $attached_files['pan_attachment']; ?></a>
                	<?php }else{ echo "-"; } ?>
	                 </td>
	              </tr>
	              <?php }if($fieldaccesright['field']['pf_no']!="NA"){  ?>
	              <tr>
	                 <th class="p-2"><?php echo __('PF Number'); ?></th>
	                 <td class="p-2"><?php echo renderSensitiveDataWithToggle($data['pf_no'], 'pf_number'); ?> </td>
	                 <td class="p-2"> - </td>
	                 <td class="p-2"> 
	                 <?php if($attached_files['pf_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['pf_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> <?php //echo $attached_files['pf_attachment']; ?></a>
                	<?php }else{ echo "-"; } ?>
                	 </td>
	              </tr>
	              <?php }if($fieldaccesright['field']['pf_uan']!="NA"){  ?>
	               <tr>
	                 	<th class="p-2"><?php echo __('UAN Number'); ?></th>
	                  	<td class="p-2"><?php echo renderSensitiveDataWithToggle($data['pf_uan'], 'uan_number'); ?></td>
	                	<td class="p-2"> - </td>
	                 	<td class="p-2"> 
	                 	<?php if($attached_files['pf_uan_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['pf_uan_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> <?php //echo $attached_files['pf_uan_attachment']; ?></a>
                	<?php }else{ echo "-"; } ?>
                	 </td>
	              </tr>
            	<?php }if($fieldaccesright['field']['gosi_number']!="NA"){  ?>
	              <tr>
	                 <th class="p-2"><?php echo __('GOSI Number'); ?></th>
	                 <td class="p-2"><?php echo renderSensitiveDataWithToggle($data['gosi_number'], 'gosi_number'); ?> </td><td class="p-2"> - </td>
	                 <td class="p-2"> 
	                 <?php if($attached_files['gosi_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['gosi_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> <?php //echo $attached_files['pf_attachment']; ?></a>
                	<?php }else{ echo "-"; } ?>
                	 </td>
	              </tr>
	            <?php }?>
           </tbody>
        </table>
	<script type="text/javascript">
	if (typeof toggleSensitiveDataVisibility !== 'function') {
		function toggleSensitiveDataVisibility(btn) {
			var parent = btn.closest ? btn.closest('.sensitive-data-container') : btn.parentElement;
			if (!parent) return;
			var target = parent.querySelector ? parent.querySelector('.sensitive-data-text') : null;
			if (!target) return;

			var isMasked = target.getAttribute('data-state') !== 'unmasked';
			if (isMasked) {
				target.textContent = target.getAttribute('data-unmasked') || '';
				target.setAttribute('data-state', 'unmasked');
				btn.classList.remove('fa-eye', 'ti-eye');
				btn.classList.add('fa-eye-slash');
				btn.setAttribute('title', 'Click to hide sensitive information');
			} else {
				target.textContent = target.getAttribute('data-masked') || '';
				target.setAttribute('data-state', 'masked');
				btn.classList.remove('fa-eye-slash');
				btn.classList.add('fa-eye');
				btn.setAttribute('title', 'Click to view sensitive information');
			}
		}
	}
	</script>
   <?php }elseif($_POST['pagetype'] =="experience_details" || $_POST['showpagetype'] =="experience_details"){
		$fieldaccesright = $vafield['experience_details'];
	if(count((array)$emp_work_experiences) !="0"){		  	
	?>
	<div class="card-body border-bottom ">
        <div class="card-block">
			<div class="scroll-widget">
			  	<div class="latest-update-box">
			  	<?php 
			  	foreach ($emp_work_experiences as $expe) {  ?>	
			    	<div class="row p-t-20 p-b-30">
					    <div class="col-auto text-end update-meta p-r-0">
					        <i class="b-primary update-icon ring"></i>
					    </div>
					    <div class="col p-l-5">
					        <a href="#!"><h6><?php echo $expe['company_name']; ?></h6></a>
					        <p class="text-muted m-b-0"><?php echo fromDate($expe['work_from']); ?> <?php echo __('To'); ?> <?php echo fromDate($expe['work_to']); ?>	        
					        </p>
					    </div>
					</div>
				<?php } ?>
					
			  	</div>
			</div>
		</div>
    </div>
    <?php }else{ ?>
		<div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> <?php echo __('No Employment History'); ?> </p>   
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>   
        </div>
	<?php } ?>

   <?php }elseif($_POST['pagetype'] =="employee_qualifications" || $_POST['showpagetype'] =="employee_qualifications"){
		$fieldaccesright = $vafield['employee_qualifications'];
  		if(count((array)$emp_educations) !="0"){
	?>

	<div class="card-body border-bottom ">
        <div class="card-block">
			<div class="scroll-widget">
			  	<div class="latest-update-box"> 
			  		<?php 
			  		foreach ($emp_educations as $emp_edu) {  ?>
			    	<div class="row p-t-20 p-b-30">
					    <div class="col-auto text-end update-meta p-r-0">
					        <i class="b-primary update-icon ring"></i>
					    </div>
					    <div class="col p-l-5">
					        <h6 class="mb-1"><?php echo emptytextdashShow($emp_edu['institute_name']); ?></h6>
					        <span class="text-muted m-b-0 f-w-600 f-12"><?php echo $education_type[$emp_edu['education_type']]['genc_detail_name']; ?> - (<?php echo $emp_edu['score']; ?>) </span>
					        <p class="text-muted m-b-0"><?php echo fromDate($emp_edu['start_date']); ?> <?php echo __('To'); ?> <?php echo fromDate($emp_edu['end_date']); ?>       
					        
					        </p>
					    </div>
					</div>
				<?php } ?>	
			  	</div>
			</div>
		</div>
    </div>
    <?php }else{ ?>
		<div class="card-px text-center py-20 mt-2">
	        <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> <?php echo __('No Educational Details'); ?> </p>   
	        <div class="text-center px-4">
	            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
	        </div>   
	    </div>
	<?php } ?>
     <?php }elseif($_POST['pagetype'] =="employee_dependents" || $_POST['showpagetype'] =="employee_dependents"){
		$fieldaccesright = $vafield['employee_dependents'];
	?>
	<div class="row">
		<?php 
		if(count((array)$emp_dependents) !="0"){
		  	foreach ($emp_dependents as $emp_dep) {  ?>
		<div class="col-xl-6 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block fw-bold"><?php echo __('Name'); ?></span>
                    <p><?php echo emptytextdashShow($emp_dep['dependent_name']); ?></p>
                    <span class="form-label d-block fw-bold"><?php echo __('Relationship Type'); ?></span>
                   	<p><?php echo $relations[$emp_dep['dependent_relationship_type']]['relation_name']; ?></p>
                   	<span class="form-label d-block fw-bold"><?php echo __('Date Of Birth'); ?></span>
                   	<p><?php echo fromDate($emp_dep['dependent_date_of_birth']); ?></p>
                </div>
            </div>
        </div>
    	<?php } ?>
    	<?php }else{ ?>
			<div class="card-px text-center py-20 mt-2">
	            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> <?php echo __('No Dependents'); ?> </p>   
	            <div class="text-center px-4">
	                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
	            </div>   
	        </div>
		<?php } ?>	
	</div>
    <?php }elseif($_POST['pagetype'] =="employee_contacts" || $_POST['showpagetype'] =="employee_contacts"){
		$fieldaccesright = $vafield['employee_contacts'];
	?>
	<div class="row"> 
		<?php 
		if(count((array)$emp_contacts) !="0"){
			foreach ($emp_contacts as $emp_con) {  ?>
		<div class="col-xl-6 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block fw-bold"><?php echo __('Name'); ?></span>
                    <p><?php echo emptytextdashShow($emp_con['contact_name']); ?></p>
                    <span class="form-label d-block fw-bold"><?php echo __('Relationship Type'); ?></span>
                   	<p><?php echo emptytextdashShow($relations[$emp_con['contact_relationship']]['relation_name']); ?></p>
                   	<span class="form-label d-block fw-bold"><?php echo __('Home No'); ?></span>
                   	<p><?php echo emptytextdashShow($emp_con['contact_home_no']); ?></p>
                   	<span class="form-label d-block fw-bold"><?php echo __('Mobile No'); ?></span>
                   	<p><?php echo emptytextdashShow($emp_con['contact_mobile_no']); ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php }else{ ?>
		<div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> <?php echo __('No Emergency Contact'); ?> </p>   
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>   
        </div>
	<?php } ?>	
	</div>



	<?php }elseif($_POST['pagetype'] =="employee_buddies" || $_POST['showpagetype'] =="employee_buddies"){
		$fieldaccesright = $vafield['employee_buddies']; 
	?>
		<div class="row">
			<?php if($fieldaccesright['field']['role_id']!="NA"){  ?>
			<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Employment Role'); ?> </span>
	          	<p> <?php echo $employment_roles[$data['role_id']]['role_name']!=""?$employment_roles[$data['role_id']]['role_name']:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['reporting_manager_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Repoting To'); ?></span>
	          	<p> <?php echo $application_users[$data['reporting_manager_id']]['display_name']!=""?$application_users[$data['reporting_manager_id']]['display_name']:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['admin_buddy']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Admin Buddy'); ?> </span>
	          	<p> <?php echo $all_employees[$data['admin_buddy']]['display_name']!=""?$all_employees[$data['admin_buddy']]['display_name']:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['hr_buddy']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('HR Buddy'); ?></span>
	          	<p> <?php echo $all_employees[$data['hr_buddy']]['display_name']!=""?$all_employees[$data['hr_buddy']]['display_name']:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['finance_buddy']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Finance Buddy'); ?></span>
	          	<p> <?php echo $all_employees[$data['finance_buddy']]['display_name']!=""?$all_employees[$data['finance_buddy']]['display_name']:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['project_buddy']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Project Buddy'); ?></span>
	          	<p> <?php echo $all_employees[$data['project_buddy']]['display_name']!=""?$all_employees[$data['project_buddy']]['display_name']:"-";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['loc_buddy']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Location Buddy'); ?></span>
	          	<p> <?php echo $all_employees[$data['loc_buddy']]['display_name']!=""?$all_employees[$data['loc_buddy']]['display_name']:"-";?></p>
	      	</div>
	      	<?php } ?>
	    </div>

<!-- added by koti start -->
<?php }elseif($_POST['pagetype'] =="employee_access" || $_POST['showpagetype'] =="employee_access"){
		$fieldaccesright = $vafield['employee_access']; 
	?>
		<div class="row">
			<?php if($fieldaccesright['field']['cover_under_pf']!="NA"){  ?>
			<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Cover Under PF'); ?> </span>
	          	<p> <?php echo $data['cover_under_pf']!="" && $data['cover_under_pf'] == '0' ? "YES":"NO";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['cover_under_esi']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Cover Under ESI'); ?></span>
	          	<p>  <?php echo $data['cover_under_esi']!="" && $data['cover_under_esi'] == '0'?"YES":"NO";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['clock_event_type']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('ClockIn & ClockOut Access'); ?> </span>
	          	<p>  <?php echo $data['clock_event_type']!="" && $data['clock_event_type'] == 'Y'?"YES":"NO";?></p>
	      	</div>
	      	<?php }  if($fieldaccesright['field']['b_machine_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('B Machine Id'); ?> </span>
	          	<p>  <?php echo $data['b_machine_id']!="" ? $data['b_machine_id']:"-";?></p>
	      	</div>
	      	<?php } ?>
	    </div>
<!-- added by koti end -->

	<?php }elseif($_POST['pagetype'] =="employee_other_access" || $_POST['showpagetype'] =="employee_other_access"){
		$fieldaccesright = $vafield['employee_other_access'];  ?>
		<div class="row">
			
		    <?php if($fieldaccesright['field']['job_title_id']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Job Title'); ?> </span>
	          	<p>  <?php echo emptytextdashShow($designations[$data['job_title_id']]['job_title']);?></p>
	      	</div>
	      	<?php } if($fieldaccesright['field']['is_muslim']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Are You Muslim ?'); ?></span>
	          	<p>  <?php echo $data['is_muslim']!="" && $data['is_muslim'] == '0'?"YES":"NO";?></p>
	      	</div>
	      	<?php }if($fieldaccesright['field']['is_local']!="NA"){  ?>
	      	<div class="col-xl-6 form-group">
	          	<span class="form-label d-block fw-bold"><?php echo __('Are You Local ?'); ?> </span>
	          	<p>  <?php echo $data['is_local']!="" && $data['is_local'] == 'Y'?"YES":"NO";?></p>
	      	</div>
	      	<?php }?>
		</div>
	<?php }elseif($_POST['pagetype'] =="emp_visa" || $_POST['showpagetype'] =="emp_visa"){
		$fieldaccesright = $vafield['emp_visa'];
	?>
	<div class="row"> 
		<?php 
		if(count((array)$emp_visa) !="0"){
			foreach ($emp_visa as $emp_v) {  ?>
		<div class="col-xl-6 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block fw-bold"><?php echo __('VISA Number'); ?></span>
                    <p><?php echo emptytextdashShow($emp_v['visa_number']); ?></p>
                    <span class="form-label d-block fw-bold"><?php echo __('Country'); ?></span>
                   	<p><?php echo emptytextdashShow($countries[$emp_v['country_id']]['country_name']); ?></p>
                   	<span class="form-label d-block fw-bold"><?php echo __('Issue Date'); ?></span>
                   	<p><?php echo $emp_v['visa_issue_date'] !=""?fromDate($emp_v['visa_issue_date']):" - "; ?></p>
                   	<span class="form-label d-block fw-bold"><?php echo __('Expiry Date'); ?></span>
                   	<p><?php echo $emp_v['visa_expiry_date'] !=""?fromDate($emp_v['visa_expiry_date']):"-"; ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
	<?php }else{ ?>
		<div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> <?php echo __('No VISA Details'); ?> </p>   
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>   
        </div>
	<?php } ?>	
	</div>    

	<?php }elseif($_POST['pagetype'] =="emp_skills" || $_POST['showpagetype'] =="emp_skills"){
		$fieldaccesright = $vafield['emp_skills'];
	?>
	<div class="row"> 
		<?php 
		if(count((array)$emp_skills) !="0"){
			foreach ($emp_skills as $emp_skil) {  ?>
		<div class="col-xl-6 col-md-6" >
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block fw-bold"><?php echo __('Skill'); ?> <?php if($fieldaccesright['field']['emp_skills']!="V"){  ?><span role="button" tabindex="0" aria-label="<?= htmlspecialchars(__('Skillpopup new'), ENT_QUOTES, 'UTF-8'); ?>" onkeydown="if(event.target===this&&(event.key==='Enter'||event.key===' ')){event.preventDefault();this.click();}" class="float-end cursor-pointer" onclick="skillpopupNew('<?php echo $emp_skil['id']; ?>')"><i class="ti-pencil"></i></span><?php } ?></span>
                   	<p><?php echo emptytextdashShow($skills[$emp_skil['skill_id']]['skill']); ?></p>
                   	<span class="form-label d-block fw-bold"><?php echo __('Years Of Experience'); ?></span>
                    <p><?php echo emptytextdashShow($emp_skil['years_of_exp']); ?></p>
                    <span class="form-label d-block fw-bold"><?php echo __('Rating'); ?></span>
                   	<p><?php echo $skills[$emp_skil['skill_id']]['max_ratting']."/".emptytextdashShow($emp_skil['rating']); ?></p>
                   	<span class="form-label d-block fw-bold"><?php echo __('comments'); ?></span>
                   	<p><?php echo emptytextdashShow($emp_skil['comments']); ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php }else{ ?>
		<div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> <?php echo __('No Skills'); ?> </p>   
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>   
        </div>
	<?php } ?>	
	</div> 
	<?php } ?>


<?php } //View part End ?>


<?php if($_POST['edittype'] =="INSERT"){ ?>
	<?php 
	if($_POST['pagetype'] =="basic_information" || $_POST['showpagetype'] =="basic_information"){
       	$fieldaccesright = $vafield['basic_information'];   
    ?>
		<div class="row">  
		<?php if($fieldaccesright['field']['emp_code']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['emp_code'] !="E"?"custom_readonly":""; ?>
			<div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Employee Code'); ?> </span>
		        <input aria-label="<?= htmlspecialchars(__('Emp code'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" name="emp_code" value="<?php echo $data['emp_code']; ?>" readonly/>
		    </div>
		<?php }if($fieldaccesright['field']['email_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['email_id'] !="E"?"custom_readonly":""; ?>
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Email'); ?> </span>
		        <input aria-label="<?= htmlspecialchars(__('Email id'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg  <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter email'); ?>" name="email_id" value="<?php echo $data['email_id']; ?>" readonly/>
		    </div>
		<?php }if($fieldaccesright['field']['first_name']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['first_name'] !="E"?"custom_readonly":""; ?>    
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('First Name'); ?>  </span>
		        <input aria-label="<?= htmlspecialchars(__('First name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg  <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter first Name'); ?>" name="first_name" value="<?php echo $data['first_name']; ?>"/>
		    </div>
		<?php }if($fieldaccesright['field']['middle_name']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['middle_name'] !="E"?"custom_readonly":""; ?>   
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Middle Name'); ?> </span>
		        <input aria-label="<?= htmlspecialchars(__('Middle name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter middle Name'); ?>" name="middle_name" value="<?php echo $data['middle_name']; ?>"/>
		    </div>
		<?php }if($fieldaccesright['field']['last_name']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['last_name'] !="E"?"custom_readonly":""; ?>    
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Last Name'); ?> </span>
		        <input aria-label="<?= htmlspecialchars(__('Last name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter last Name'); ?>" name="last_name" value="<?php echo $data['last_name']; ?>"/>
		    </div>
		<?php }if($fieldaccesright['field']['phone']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['phone'] !="E"?"custom_readonly":""; ?>    
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Contact'); ?> </span>
		        <input aria-label="<?= htmlspecialchars(__('Phone'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter Contact'); ?>" name="phone" value="<?php echo $data['phone']; ?>"/>
		    </div>
		<?php }if($fieldaccesright['field']['address']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['address'] !="E"?"custom_readonly":""; ?>    
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Permanent Address'); ?> </span>
		        <textarea aria-label="<?= htmlspecialchars(__('Address'), ENT_QUOTES, 'UTF-8'); ?>" style="overflow:auto" class="form-control form-control-lg  <?php echo $readonlycheck; ?>" placeholder="<?php echo __('Enter your permanent address'); ?>" name="address"><?php echo $data['address'];?></textarea>
		    </div>
		 <?php }if($fieldaccesright['field']['contact_address']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['contact_address'] !="E"?"custom_readonly":""; ?>   
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Current Address'); ?> </span>
		        <textarea aria-label="<?= htmlspecialchars(__('Contact address'), ENT_QUOTES, 'UTF-8'); ?>" style="overflow:auto" class="form-control <?php echo $readonlycheck; ?>" placeholder="<?php echo __('Enter your current address'); ?>" name="contact_address"><?php echo $data['contact_address'];?></textarea>
		    </div>
		<?php }if($fieldaccesright['field']['post_code']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['post_code'] !="E"?"custom_readonly":""; ?>    
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Postcode'); ?> </span>
		        <input aria-label="<?= htmlspecialchars(__('Post code'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control <?php echo $readonlycheck; ?>" placeholder="<?php echo __('Enter your postcode'); ?>" name="post_code" value="<?php echo $data['post_code']; ?>"/>
		    </div>
		<?php }if($fieldaccesright['field']['fax']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['fax'] !="E"?"custom_readonly":""; ?>    
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Fax'); ?> </span>
		        <input aria-label="<?= htmlspecialchars(__('Fax'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter fax'); ?>" name="fax" value="<?php echo $data['fax']; ?>"/>
		    </div>
		<?php }if($fieldaccesright['field']['image']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['image'] !="E"?"custom_readonly":""; ?>    
			<div class="mt-2 col-lg-2 <?php echo $readonlycheck; ?>">
	            <span class="form-label d-inline-block fw-bold"><?php echo __('Profile Image'); ?></span>
	            <div class="col-lg-9 col-xl-6">
	                <div class="profile-pic">
	                    <label class="-label" for="profile_image">
	                        <span> <i class="fa fa-camera"></i> <?php echo __('&nbsp; Change Image'); ?></span>
	                    </label>
	                    <input type="file" name="profile_image" id="profile_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
	                    <img alt="" src="<?php echo "uploads/all/".$data['image'] ; ?>" id="outputprofile_image" width="200" onerror="this.src='uploads/all/no-image.jpg';"/>
	                    <input type="hidden" name="old_profile_image" value="<?php echo $data['image'] ;  ?>">
	                </div>
	            </div>
	        </div>
	    <?php } ?>


		</div>

	<?php }elseif($_POST['pagetype'] =="work_details" || $_POST['showpagetype'] =="work_details"){ ?>
		<?php 
	       	$fieldaccesright = $vafield['work'];   
	    ?>
	    <div class="row"> 
	    <?php if($fieldaccesright['field']['department_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['department_id'] !="E"?"custom_readonly":""; ?>   
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Department'); ?> </span>
		        <div class="<?php echo $readonlycheck; ?>">
			        <select aria-label="<?= htmlspecialchars(__('Department id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single"   name="department_id">
			          	<option value=""><?php echo __('Select Any One'); ?></option>
			          	<?php foreach ($departments as $departments) {
			            ?>
			            <option <?php if($data['department_id'] == $departments['id']){  ?>selected <?php } ?> value="<?php echo $departments['id']; ?>" ><?php echo __($departments['department_name']);?></option>
			          	<?php } ?> 
			        </select>
			    </div>
		    </div>
		    <?php }if($fieldaccesright['field']['designation_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['designation_id'] !="E"?"custom_readonly":""; ?>  
		   	<div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Designation'); ?> </span>
		        <div class="<?php echo $readonlycheck; ?>">
			        <select aria-label="<?= htmlspecialchars(__('Designation id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="designation_id">
					  	<option value=""><?php echo __('Select Any One'); ?></option>
					  	<?php foreach ($designations as $desigs) {
					    ?>
					    	<option <?php if($data['designation_id'] == $desigs['id']){ ?> selected <?php } ?> value="<?php echo $desigs['id']; ?>" >
					      	<?php echo __($desigs['designation_name']); ?>
					    	</option>
					  <?php } ?>  
					</select>
				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['division_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['division_id'] !="E"?"custom_readonly":""; ?>  
		   	<div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Division'); ?> </span>
		        <div class="<?php echo $readonlycheck; ?>">
			        <select aria-label="<?= htmlspecialchars(__('Designation id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" onclick="tochangedeparment(this.value)" name="designation_id">
					  	<option value=""><?php echo __('Select Any One'); ?></option>
					  	<?php foreach ($divisions as $desigs) {
					    ?>
					    	<option <?php if($data['division_id'] == $desigs['id']){ ?> selected <?php } ?> value="<?php echo $desigs['id']; ?>" >
					      	<?php echo __($desigs['division_name']); ?>
					    	</option>
					  <?php } ?>  
					</select>
				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['band_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['band_id'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Band'); ?> </span>
		        <div class="<?php echo $readonlycheck; ?>">
			        <select aria-label="<?= htmlspecialchars(__('Band id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="band_id" onchange="getBandid(this.value)">
					    <option value=""><?php echo __('Select Any One'); ?></option>
					    <?php foreach ($employment_bands as $employment_ban) { 
					      ?>
					      <option <?php if($data['band_id'] == $employment_ban['id']){ ?>selected  <?php } ?> value="<?php echo $employment_ban['id']; ?>" >
					        <?php echo __($employment_ban['band_name']); ?>
					      </option>
					    <?php } ?>  
					</select>
				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['level_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['level_id'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Level'); ?> </span>
		        <div class="<?php echo $readonlycheck; ?>">
			        <select aria-label="<?= htmlspecialchars(__('Level id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control js-example-basic-single" name="level_id" id="employeement_level">
				      	<option value=""><?php echo __('Select Any One'); ?></option>
				      	<?php foreach ($employment_levels as $employment_levl) { ?>
				       	<option <?php if($data['level_id'] == $employment_levl['id']){ ?> selected <?php } ?>  value="<?php echo $employment_levl['id']; ?>" >
				        <?php echo __($employment_levl['level_name']); ?>
				      	</option>
				    <?php } ?>   
				  </select>
				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['vertical_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['vertical_id'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Vertical'); ?> </span>
		        <div class="<?php echo $readonlycheck; ?>">
			        <select aria-label="<?= htmlspecialchars(__('Vertical id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="vertical_id">
				        <option value=""><?php echo __('Select Any One'); ?></option>
				        <?php foreach($verticals as $verticals){ ?>
				         <option <?php if($data['vertical_id'] == $verticals['id']){ ?>selected <?php } ?> value="<?php echo $verticals['id']; ?>" >
				          <?php echo __($verticals['vertical_name']); ?>
				        </option>
				      <?php } ?>  
				    </select>
				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['hiring_source']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['hiring_source'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Hiring Source'); ?>  </span>
		        <input aria-label="<?= htmlspecialchars(__('Hiring source'), ENT_QUOTES, 'UTF-8'); ?>"  class="form-control <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter Hiring Source'); ?>" name="hiring_source" value="<?php echo $data['hiring_source']; ?>"/>
		    </div>
		    <?php }if($fieldaccesright['field']['joining_date']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['joining_date'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Date of Joining'); ?> </span>
		        <div class="<?php echo $readonlycheck; ?>">
  					<input aria-label="<?= htmlspecialchars(__('Joining date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control datepicker" value="<?php echo ($data['joining_date']!="0000-00-00" AND $data['joining_date']!="")?fromDate($data['joining_date']):""; ?>"  name="joining_date" placeholder="<?php echo __('Enter Date of Joining'); ?>"/>
  				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['experience_current_organization']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['experience_current_organization'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		    	<span class="form-label d-inline-block fw-bold"><?php echo __('Experience in Current Organization'); ?> </span>
		        <input aria-label="<?= htmlspecialchars(__('Experience current organization'), ENT_QUOTES, 'UTF-8'); ?>" type="number" class="form-control <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Experience in Current Organization'); ?>" name="experience_current_organization" value="<?php echo $data['experience_current_organization']; ?>"/>
		    </div>
		    <?php }if($fieldaccesright['field']['total_experiance']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['total_experiance'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Total Experience'); ?> </span>
		        <div class="input-group input-group-button">
			        <input aria-label="<?= htmlspecialchars(__('Total experiance'), ENT_QUOTES, 'UTF-8'); ?>"  type="number" class="form-control form-control-lg width1 <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Total Experience'); ?>"  name="total_experiance" value="<?php echo $data['total_experiance']; ?>"/>
			        <span class="input-group-text addon-style"><span class=" d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Years'); ?></span></span>
		        </div>
		    </div>
		    <?php }if($fieldaccesright['field']['relevant_experience']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['relevant_experience'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Relevant Experience'); ?> </span>
		        <div class="input-group input-group-button">
		        	<input aria-label="<?= htmlspecialchars(__('Relevant experience'), ENT_QUOTES, 'UTF-8'); ?>"   type="number" class="form-control form-control-lg width1 <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Relevant Experience'); ?>"  name="relevant_experience" value="<?php echo $data['relevant_experience']; ?>"/>
		        	<span class="input-group-text addon-style"><span class=" d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Years'); ?></span></span>
		        </div>
		    </div>
		    <?php }if($fieldaccesright['field']['notice_period']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['notice_period'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Notice Period'); ?> </span>
		        <div class="input-group input-group-button">
				   <input aria-label="<?= htmlspecialchars(__('Notice period'), ENT_QUOTES, 'UTF-8'); ?>"  type="number" class="form-control width1 <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Notice Period'); ?>" name="notice_period" value="<?php echo $data['notice_period']; ?>" />
				   <span class="input-group-text addon-style"><span class=" d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Days'); ?></span></span>
				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['location_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['location_id'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Work Location'); ?> </span>
		        <div class="<?php echo $readonlycheck; ?>">
			        <select aria-label="<?= htmlspecialchars(__('Location id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control js-example-basic-single" name="location_id">
						<option value=""><?php echo __('Select Any One'); ?></option>
						  <?php foreach ($locations as $locations) { 
						    if($locations['parent_location_id'] =='') { ?>
						    <option <?php if($data['location_id'] == $locations['id']){ ?>selected <?php } ?> value="<?php echo $locations['id']; ?>" >
						      <?php echo $locations['location_name']; ?>
						    </option>
						<?php }  } ?>  
					</select>
				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['employee_type']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['employee_type'] !="E"?"custom_readonly":""; ?>  
		   <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Employment Type'); ?></span>
		        <span role="button" tabindex="0" onkeydown="if(event.target===this&&(event.key==='Enter'||event.key===' ')){event.preventDefault();this.click();}" class="float-end btn btn-success" onclick="employementviewhistoryppup(<?php echo $employee_id; ?>)"> <?php echo __('History'); ?> </span>
		        <div class="form-radio <?php echo $readonlycheck; ?>">
		        <?php
			     foreach ($employeement_types as $emptypes) {
			    ?>
			      <div class="radio radio-inline"> 
			       <label class="form-label <?php echo $text_color_ety; ?>">
			         <input type="radio" onclick="employeementtypepopup(<?php echo $employee_id.",".$emptypes['genc_detail_code'];?>)" name="employee_type" <?php if($data['employee_type'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php } ?> value="<?php echo $emptypes['genc_detail_code'];?>" style="display:none"/>
			         <i class="helper"></i><?php echo $emptypes['genc_detail_name'];?> </label>
			       </div>
			    <?php } ?>
				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['billable']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['billable'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Billable'); ?> </span>
		        <div class="form-radio <?php echo $readonlycheck; ?>">
				   <div class="radio radio-inline"> 
				       	<label class="form-label <?php echo $text_color_ebi; ?>">
				         <input type="radio" name="billable" <?php if($data['billable'] ==0) {?> checked="checked" <?php } ?> value="0" style="display:none" />
				         <i class="helper"></i><?php echo __('Yes'); ?> </label>
				    </div>
				    <div class="radio radio-inline">
				        <label class="form-label <?php echo $text_color_ebi; ?>">
				           <input type="radio" name="billable" <?php if($data['billable'] ==1) {?> checked="checked" <?php } ?> value="1" style="display:none" />
				           <i class="helper"></i><?php echo __('No'); ?> </label>
				    </div>
				</div>
		    </div>
		    <?php }if($fieldaccesright['field']['office_phone']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['office_phone'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Office Phone'); ?>  </span>
		        <input aria-label="<?= htmlspecialchars(__('Office phone'), ENT_QUOTES, 'UTF-8'); ?>"  type="number" class="form-control <?php echo $readonlycheck; ?>" name="office_phone" placeholder="<?php echo __('Enter Office Phone'); ?>" value="<?php echo $data['office_phone']; ?>"/>
		    </div>
		    <?php }if($fieldaccesright['field']['office_ext']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['office_ext'] !="E"?"custom_readonly":""; ?>  
		    <div class="col-xl-6 mt-2" >
		        <span class="form-label d-inline-block fw-bold"><?php echo __('Extension'); ?> </span>
		        <input aria-label="<?= htmlspecialchars(__('Office ext'), ENT_QUOTES, 'UTF-8'); ?>"  type="number" class="form-control <?php echo $readonlycheck; ?>" name="office_ext" placeholder="<?php echo __('Enter Extension'); ?>" value="<?php echo $data['office_ext']; ?>"/>
		    </div>
			<?php } ?>
		</div>
	<?php }elseif($_POST['pagetype'] =="personal_details" || $_POST['showpagetype'] =="personal_details"){
		$fieldaccesright = $vafield['personal_details']; 
	?>
	<div class="row">
	 <?php if($fieldaccesright['field']['date_of_birth']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['date_of_birth'] !="E"?"custom_readonly":""; ?>	
		<div class="col-xl-6 mt-2" >
		    <span class="form-label d-inline-block fw-bold"><?php echo __('Date of Birth'); ?> </span>
		    <div class="<?php echo $readonlycheck; ?>">
			    <input aria-label="<?= htmlspecialchars(__('Date of birth'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control form-control-lg datepicker"  value="<?php echo  ($data['date_of_birth']!="0000-00-00" AND $data['date_of_birth']!="")?fromDate($data['date_of_birth']):""; ?>"  name="date_of_birth" placeholder="<?php echo __('Enter Date of Birth'); ?>" onchange="selectdateOfBirth(this.value)" />
			</div>
	    </div>
	    <?php } /**if($fieldaccesright['field']['age']!="NA"){ 
			$readonlycheck = $fieldaccesright['field']['age'] !="E"?"custom_readonly":""; 

		  	?> 
	    <div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Age In Years'); ?> </label>
		    <input type="text" class="form-control <?php echo $readonlycheck; ?>"  value="<?php echo $agecalc; ?>" name="age" id="age" readonly/>
	    </div>
	    <?php } **/ if($fieldaccesright['field']['marital_status']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['marital_status'] !="E"?"custom_readonly":""; ?> 
	    <div class="col-xl-6 mt-2" >
		    <span class="form-label d-inline-block fw-bold"><?php echo __('Marital Status'); ?></span>
		    <div class="form-radio <?php echo $readonlycheck; ?>">
			<?php
			   	foreach ($marital_statuses as $marital_stat) {
			 ?>
			    <div class="radio radio-inline">
			     	<label class="form-label">
			       		<input onclick="showmarriagedate(this.value);" type="radio" name="marital_status" <?php if($data['marital_status'] == $marital_stat['id']) {?> checked="checked" <?php } ?> value="<?php echo $marital_stat['id']; ?>" style="display:none"/>
			       		<i class="helper"></i>
			       		<?php echo $marital_stat['marital_status_name']; ?>
			     	</label>
			   	</div>
			 <?php } ?>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['wedding_date']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['wedding_date'] !="E"?"custom_readonly":""; ?> 
		<div class="col-lg-6 " style="<?php echo $data['marital_status'] =="2"?"":"display: none"; ?>"  id="marriagedateshow">
			<div class="mt-2" id="marriagedateshow1">
			  	<span  class="form-label d-inline-block fw-bold"><?php echo __('Marrige Date'); ?></span>
			  	<div class="<?php echo $readonlycheck; ?>"> 
				  	<input aria-label="<?= htmlspecialchars(__('Wedding date'), ENT_QUOTES, 'UTF-8'); ?>" type="text"  class="form-control datepicker" name="wedding_date" id="wedding_date" value="<?php echo ($data['wedding_date']!="0000-00-00" AND $data['wedding_date']!="")?fromDate($data['wedding_date']):""; ?>" placeholder="<?php echo __('Enter Marrige Date'); ?>"/>
				</div>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['gender']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['gender'] !="E"?"custom_readonly":""; ?> 
	    <div class="col-xl-6 mt-2" >
		    <span class="form-label d-inline-block fw-bold"><?php echo __('Gender'); ?></span>
		    <div class="form-radio <?php echo $readonlycheck; ?>">
			   	<?php
			   		foreach ($gender_details as $gender_data) {
			    ?>
			    <div class="radio radio-inline">
			     	<label class="form-label <?php echo $text_color_gen; ?>">
			       	<input  type="radio" name="gender" <?php if($data['gender'] == $gender_data['genc_detail_code']){ ?>  checked="checked" <?php } ?> value="<?php echo $gender_data['genc_detail_code'];?>" style="display:none"/>
			       	<i class="helper"></i>
			       	<?php echo $gender_data['genc_detail_name']; ?>
			     	</label>
			   </div>
			 <?php } ?>
			</div>
	    </div>
	    <?php }if($fieldaccesright['field']['blood_group']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['blood_group'] !="E"?"custom_readonly":""; ?> 
	    <div class="col-xl-6 mt-2" >
		    <span class="form-label d-inline-block fw-bold"><?php echo __('Blood Group'); ?></span>
		    <div class="<?php echo $readonlycheck; ?>">
				<select aria-label="<?= htmlspecialchars(__('Blood group'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-select js-example-basic-single" name="blood_group">
				  	<option value=""><?php echo __('Select Any One'); ?></option>
				  	<?php foreach ($blood_groups as $blood_group) { ?>
				    <option <?php if($data['blood_group'] == $blood_group['id']){ ?>selected  <?php } ?> value="<?php echo $blood_group['id']; ?>" >
				      <?php echo $blood_group['blood_group']; ?>
				    </option>
				  <?php } ?>
				</select>
			</div>
		</div>
	    <?php }if($fieldaccesright['field']['disability']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['disability'] !="E"?"custom_readonly":""; ?> 
		<div class="col-xl-6 mt-2" >
		    <span class="form-label d-inline-block fw-bold"><?php echo __('Disability'); ?></span>
			<div  class="form-radio <?php echo $readonlycheck; ?>">
			   <?php
			   foreach ($yesno as $ynkey=>$yn) {
			    ?>
			    <div class="radio radio-inline">
			     	<label class="form-label" onclick="selectDisability()">
			       	<input type="radio" name="disability" id="disability" <?php if($data['disability'] == $ynkey OR ($data['disability'] =="" AND $ynkey=='1')) {?>  checked="checked" <?php } ?> value="<?php echo $ynkey; ?>" style="display:none"/>
			       	<i class="helper"></i>
			       	<?php echo $yn; ?>
			     	</label>
			   	</div>
			 <?php } ?>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['disability_percentage']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['disability_percentage'] !="E"?"custom_readonly":""; ?> 
		<div class="col-xl-6 mt-2" id="disabilitypercentage_id" style="<?php echo $data['disability'] =="0"?"":"display: none"; ?>">
		    <label class="form-label"><?php echo __('Disability percentage'); ?></label>
		    <input type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="" name="disability_percentage" value="<?php echo $data['disability_percentage']; ?>"/>
		</div>
		<?php }if($fieldaccesright['field']['work_textile_industry']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['work_textile_industry'] !="E"?"custom_readonly":""; ?> 
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Working In Textile Industry'); ?></label>
		    <div  class="form-radio <?php echo $readonlycheck; ?>">
			   <?php
			   	foreach ($yesno as $ynkey=>$yn) {
			    ?>
			    <div class="radio radio-inline">
			     	<label class="form-label">
			       	<input   type="radio" name="work_textile_industry" <?php if($data['work_textile_industry '] == $ynkey OR ($data['work_textile_industry'] =="" AND $ynkey=='1')) {?>  checked="checked" <?php } ?> value="<?php echo $ynkey; ?>" style="display:none"/>
			       	<i class="helper"></i>
			       	<?php echo $yn; ?>
			    	</label>
			   	</div>
			 	<?php } ?>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['physically_challenged_children']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['physically_challenged_children'] !="E"?"custom_readonly":""; ?> 
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Physically Challenged Children'); ?></label>
		    <div  class="form-radio <?php echo $readonlycheck; ?>">
			   <?php
			   foreach ($yesno as $ynkey=>$yn) {
			    ?>
			    <div class="radio radio-inline">
			     	<label class="form-label">
			       	<input   type="radio" name="physically_challenged_children" <?php if($data['physically_challenged_children'] == $ynkey OR ($data['work_textile_industry'] =="" AND $ynkey=='1')) {?>  checked="checked" <?php } ?> value="<?php echo $ynkey; ?>" style="display:none"/>
			       	<i class="helper"></i>
			       	<?php echo $yn; ?>
			     	</label>
			   </div>
			 <?php } ?>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['soc_facebook']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['soc_facebook'] !="E"?"custom_readonly":""; ?> 
		<div class="col-lg-6 mt-2" >
			<label  class="form-label"><?php echo __('Facebook Link'); ?></label>
			<input type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Facebook URL'); ?>" name="soc_facebook" value="<?php echo $data['soc_facebook']; ?>"/>
		</div>
		<?php }if($fieldaccesright['field']['soc_twitter']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['soc_twitter'] !="E"?"custom_readonly":""; ?> 
		<div class="col-lg-6 mt-2" >
			<label  class="form-label"><?php echo __('Twitter Link'); ?></label>
			<input type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Twitter URL'); ?>" name="soc_twitter" value="<?php echo $data['soc_twitter']; ?>"/>
		</div>
		<?php }if($fieldaccesright['field']['soc_linkedin']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['soc_linkedin'] !="E"?"custom_readonly":""; ?> 
		<div class="col-lg-6 mt-2" >
			<label  class="form-label"><?php echo __('Linkedin Link'); ?></label>
			<input type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter LinkedIn URL'); ?>" name="soc_linkedin" value="<?php echo $data['soc_linkedin']; ?>"/>
		</div>
		<?php } ?> 
	</div>
	<?php }elseif($_POST['pagetype'] =="bank_details" || $_POST['showpagetype'] =="bank_details"){
		$fieldaccesright = $vafield['bank_details']; 
	?>
	<div class="row">
		<?php if($bank_work_flow == 'N'){
		 if($fieldaccesright['field']['bank_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['bank_id'] !="E"?"custom_readonly":""; ?> 
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Bank'); ?></label>
		    <div class="<?php echo $readonlycheck; ?>">
				<select class ="form-control form-control-lg form-select js-example-basic-single"  name="bank_id">
		            <option value=""><?php echo __('Select Any One'); ?></option>
		            <?php foreach ($banks as $bnk) {  ?>
		               <option <?php if($data['bank_id'] == $bnk['id']){ ?>selected <?php } ?> value="<?php echo $bnk['id']; ?>" >
		                <?php echo $bnk['bank_name']; ?>
		              </option>
		            <?php } ?>
		        </select>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['bank_link']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['bank_link'] !="E"?"custom_readonly":""; ?> 
		<div class="col-lg-6 mt-2" >
			<label  class="form-label"><?php echo __('IFSC Code'); ?></label>
			<input type="text" class="form-control <?php echo $readonlycheck; ?>"  name="bank_link" value="<?php echo $data['bank_link']; ?>" placeholder="<?php echo __('Enter IFSC Code'); ?>"/>
		</div>
		<?php }if($fieldaccesright['field']['bank_account_no']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['bank_account_no'] !="E"?"custom_readonly":""; ?> 
		<div class="col-lg-6 mt-2" >
			<label  class="form-label"><?php echo __('Account No'); ?></label>
			<input type="text" class="form-control <?php echo $readonlycheck; ?>"  name="bank_account_no" value="<?php echo $data['bank_account_no']; ?>" placeholder="<?php echo __('Enter Account No'); ?>"/>
		</div>
		<?php } ?>
	</div>
	
	<?php } 
	}elseif($_POST['pagetype'] =="employee_buddies" || $_POST['showpagetype'] =="employee_buddies"){
		$fieldaccesright = $vafield['employee_buddies']; 
	?>
	<div class="row">
		<?php if($fieldaccesright['field']['role_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['role_id'] !="E"?"custom_readonly":""; ?>
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Employment Role'); ?></label>
		    <div class="<?php echo $readonlycheck; ?>">
				<select class ="form-control form-control-lg form-select js-example-basic-single" name="role_id">
				    <option value=""><?php echo __('Select Any One'); ?></option>
				    <?php foreach ($employment_roles as $employment_rol) {
				     ?>
				     <option <?php if($data['role_id'] == $employment_rol['id']){ ?>selected  <?php } ?> value="<?php echo $employment_rol['id']; ?>" >
				      <?php echo __($employment_rol['role_name']); ?>
				    </option>
				  <?php } ?>  
				</select>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['reporting_manager_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['reporting_manager_id'] !="E"?"custom_readonly":""; ?>
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Repoting to'); ?></label>
		    <div class="<?php echo $readonlycheck; ?>">
			    <select class ="form-control  js-example-basic-single" name="reporting_manager_id">
				    <option value=""><?php echo __('Select Any One'); ?></option>
				    <?php foreach($application_users as $application_users){ ?>
				     <option <?php if($data['reporting_manager_id'] == $application_users['id']){ ?>selected <?php } ?> value="<?php echo $application_users['id']; ?>" >
				      <?php echo $application_users['display_name']; ?>
				    </option>
				  <?php } ?>  
				</select>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['admin_buddy']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['admin_buddy'] !="E"?"custom_readonly":""; ?>
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Admin Buddy'); ?></label>
		    <div class="<?php echo $readonlycheck; ?>">
			    <select  class ="form-select js-example-basic-single required" <?php echo $read_only_ln; ?> name="admin_buddy">
				      <option value=""><?php echo __('Select Any One'); ?></option>
				      <?php foreach ($all_employees as $admin_buddy_res) { ?>
				       <option value="<?php echo $admin_buddy_res['id']; ?>"  <?php echo $data['admin_buddy'] == $admin_buddy_res['id']?"selected":""; ?>>
				        <?php echo $admin_buddy_res['display_name']; ?>
				      </option>
				    <?php } ?>
				 </select>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['hr_buddy']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['hr_buddy'] !="E"?"custom_readonly":""; ?>
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('HR Buddy'); ?></label>
		    <div class="<?php echo $readonlycheck; ?>">
			    <select  class ="form-select js-example-basic-single required" name="hr_buddy" <?php echo $read_only_ln; ?>>
				    <option value=""><?php echo __('Select Any One'); ?></option>
				    <?php foreach ($all_employees as $hr_buddy_res) { ?>
				     <option value="<?php echo $hr_buddy_res['id']; ?>" <?php echo $data['hr_buddy'] == $hr_buddy_res['id']?"selected":""; ?>>
				      <?php echo $hr_buddy_res['display_name']; ?>
				    </option>
				  <?php } ?>
				</select>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['finance_buddy']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['finance_buddy'] !="E"?"custom_readonly":""; ?>
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Finance Buddy'); ?> </label>
		    <div class="<?php echo $readonlycheck; ?>">
			    <select  class ="form-select js-example-basic-single required" name="finance_buddy" <?php echo $read_only_ln; ?>>
				    <option value=""><?php echo __('Select Any One'); ?></option>
				    <?php foreach ($all_employees as $finance_buddy_res) { ?>
				     <option value="<?php echo $finance_buddy_res['id']; ?>" <?php echo $data['finance_buddy'] == $finance_buddy_res['id']?"selected":""; ?>>
				      <?php echo $finance_buddy_res['display_name']; ?>
				    </option>
				  <?php } ?>
				</select>
			</div>
		</div>
		<?php }if($fieldaccesright['field']['project_buddy']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['project_buddy'] !="E"?"custom_readonly":""; ?>
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Project Buddy'); ?></label>
		    <div class="<?php echo $readonlycheck; ?>">
			    <select  class ="form-select js-example-basic-single required" name="project_buddy" >
			        <option value=""><?php echo __('Select Any One'); ?></option>
			        <?php foreach ($all_employees as $project_buddy_res) { ?>
			         <option value="<?php echo $project_buddy_res['id']; ?>" <?php echo $data['project_buddy'] == $project_buddy_res['id']?"selected":""; ?>>
			          <?php echo $project_buddy_res['display_name']; ?>
			        </option>
			      <?php } ?>
			    </select> 
			</div>
		</div>
		<?php }if($fieldaccesright['field']['loc_buddy']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['loc_buddy'] !="E"?"custom_readonly":""; ?>
		<div class="col-xl-6 mt-2" >
		    <label class="form-label"><?php echo __('Location Buddy'); ?></label>
		    <div class="<?php echo $readonlycheck; ?>">
			    <select  class ="form-select js-example-basic-single required" name="loc_buddy" >
				    <option value=""><?php echo __('Select Any One'); ?></option>
				    <?php foreach ($all_employees as $location_buddy_res) { ?>
				     <option value="<?php echo $location_buddy_res['id']; ?>"  <?php echo $data['loc_buddy'] == $location_buddy_res['id']?"selected":""; ?>>
				      <?php echo $location_buddy_res['display_name']; ?>
				    </option>
				  	<?php } ?>
				</select>
			</div>
		</div>
		<?php } ?>
	</div>

<!-- added by koti start -->
<?php 
	}elseif($_POST['pagetype'] =="employee_other_access" || $_POST['showpagetype'] =="employee_other_access"){
		$fieldaccesright = $vafield['employee_other_access']; ?>
		<div class="row">
		<?php if($fieldaccesright['field']['job_title_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['job_title_id'] !="E"?"custom_readonly":""; ?>  
		   	<div class="col-xl-6 mt-2" >
		        <label class="form-label"><?php echo __('Job Title'); ?> </label>
		        <div class="<?php echo $readonlycheck; ?>">
			       <select class ="form-control form-control-lg form-select js-example-basic-single" name="job_title_id">
					  	<option value=""><?php echo __('Select Any One'); ?></option>
					  	<?php foreach ((array)$job_titles as $job_title) {
					    ?>
					    	<option <?php if($data['job_title_id'] == $job_title['id']){ ?> selected <?php } ?> value="<?php echo $desigs['id']; ?>" >
					      	<?php echo __($job_title['job_title']); ?>
					    	</option>
					  <?php } ?>  
					</select>
				</div>
		    </div>
		    <?php }?>
		<?php if ($fieldaccesright['field']['is_muslim'] != "NA") { 
	        $readonlycheck = $fieldaccesright['field']['is_muslim'] != "E" ? "custom_readonly" : ""; ?>
	        
	        <div class="col-xl-6 mt-2">
	            <label class="form-label d-block"><?php echo __('Are You Muslim ?'); ?></label>

	            <div class="<?php echo $readonlycheck; ?>">
	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="cover_under_pf"
	                           value="0"
	                           <?php if (isset($data['is_muslim']) && $data['is_muslim'] === '0') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('YES'); ?></label>
	                </div>

	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="cover_under_pf"
	                           value="1"
	                           <?php if (isset($data['is_muslim']) && $data['is_muslim'] === '1') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('NO'); ?></label>
	                </div>
	            </div>
	        </div>
	    <?php } ?>
	    <?php if ($fieldaccesright['field']['is_local'] != "NA") { 
	        $readonlycheck = $fieldaccesright['field']['is_local'] != "E" ? "custom_readonly" : ""; ?>
	        
	        <div class="col-xl-6 mt-2">
	            <label class="form-label d-block"><?php echo __('Are You Local ?'); ?></label>

	            <div class="<?php echo $readonlycheck; ?>">
	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="cover_under_pf"
	                           value="0"
	                           <?php if (isset($data['is_local']) && $data['is_local'] === '0') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('YES'); ?></label>
	                </div>

	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="cover_under_pf"
	                           value="1"
	                           <?php if (isset($data['is_local']) && $data['is_local'] === '1') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('NO'); ?></label>
	                </div>
	            </div>
	        </div>
	    <?php } ?>
	    
	<?php }elseif($_POST['pagetype'] =="employee_access" || $_POST['showpagetype'] =="employee_access"){
		$fieldaccesright = $vafield['employee_access']; 

	?>
	<div class="row">
	    <!-- Cover Under PF -->
	    <?php if ($fieldaccesright['field']['cover_under_pf'] != "NA") { 
	        $readonlycheck = $fieldaccesright['field']['cover_under_pf'] != "E" ? "custom_readonly" : ""; ?>
	        
	        <div class="col-xl-6 mt-2">
	            <label class="form-label d-block"><?php echo __('Cover Under PF'); ?></label>

	            <div class="<?php echo $readonlycheck; ?>">
	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="cover_under_pf"
	                           value="0"
	                           <?php if (isset($data['cover_under_pf']) && $data['cover_under_pf'] === '0') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('YES'); ?></label>
	                </div>

	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="cover_under_pf"
	                           value="1"
	                           <?php if (isset($data['cover_under_pf']) && $data['cover_under_pf'] === '1') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('NO'); ?></label>
	                </div>
	            </div>
	        </div>
	    <?php } ?>


	    <!-- Cover Under ESI -->
	    <?php if ($fieldaccesright['field']['cover_under_esi'] != "NA") { 
	        $readonlycheck = $fieldaccesright['field']['cover_under_esi'] != "E" ? "custom_readonly" : ""; ?>
	        
	        <div class="col-xl-6 mt-2">
	            <label class="form-label d-block"><?php echo __('Cover Under ESI'); ?></label>

	            <div class="<?php echo $readonlycheck; ?>">
	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="cover_under_esi"
	                           value="0"
	                           <?php if (isset($data['cover_under_esi']) && $data['cover_under_esi'] === '0') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('YES'); ?></label>
	                </div>

	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="cover_under_esi"
	                           value="1"
	                           <?php if (isset($data['cover_under_esi']) && $data['cover_under_esi'] === '1') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('NO'); ?></label>
	                </div>
	            </div>
	        </div>
	    <?php } ?>


	    <!-- Clock In & Clock Out Access -->
	    <?php if ($fieldaccesright['field']['clock_event_type'] != "NA") { 
	        $readonlycheck = $fieldaccesright['field']['clock_event_type'] != "E" ? "custom_readonly" : ""; ?>
	        
	        <div class="col-xl-6 mt-2">
	            <label class="form-label d-block"><?php echo __('ClockIn & ClockOut Access'); ?></label>

	            <div class="<?php echo $readonlycheck; ?>">
	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="clock_event_type"
	                           value="Y"
	                           <?php if (isset($data['clock_event_type']) && $data['clock_event_type'] === 'Y') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('YES'); ?></label>
	                </div>

	                <div class="form-check form-check-inline">
	                    <input class="form-check-input"
	                           type="radio"
	                           name="clock_event_type"
	                           value="N"
	                           <?php if (isset($data['clock_event_type']) && $data['clock_event_type'] === 'N') echo __('checked'); ?>>
	                    <label class="form-check-label"><?php echo __('NO'); ?></label>
	                </div>
	            </div>
	        </div>
	    <?php } ?>

	    <?php if($fieldaccesright['field']['b_machine_id']!="NA"){ 
		  	$readonlycheck = $fieldaccesright['field']['b_machine_id'] !="E"?"custom_readonly":""; ?>
			<div class="col-xl-6 mt-2" >
		        <label class="form-label"><?php echo __('B Machine Id'); ?></label>
		        <input class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" name="b_machine_id" value="<?php echo $data['b_machine_id']; ?>" />
		    </div>
		<?php } ?>

	</div>



<!-- added by koti end -->

	<?php }elseif($_POST['pagetype'] =="proof_details" || $_POST['showpagetype'] =="proof_details"){
		$fieldaccesright = $vafield['proof_details'];
		$attached_files = json_decode($data['attached_files'],1);
	?>

	<table class="table table-vertical-center">
		<thead class="thead-light">
			<tr>
				<th class="text-center" scope="col"><?php echo __('Type'); ?></th>
				<th class="text-center"  scope="col"><?php echo __('Number'); ?></th>
				<th  class="text-center" scope="col"><?php echo __('Expiry Date'); ?></th>
				<th  class="text-center" scope="col"><?php echo __('File'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if($fieldaccesright['field']['adhar_no']!="NA"){
				$readonlycheck = $fieldaccesright['field']['adhar_no'] !="E"?"custom_readonly":"";
				 ?>
			<tr class="<?php echo $readonlycheck; ?>">
				<th class="p-2"><?php echo __('Aadhar Card'); ?></th>
				<td class="p-2">
					<input type="text" name="adhar_no" class="form-control" value="<?php echo dynamicDataDecode($data['adhar_no']); ?>" placeholder="<?php echo __('Enter Aadhar Card'); ?>">
				</td>
				<td class="p-2"> - </td>
				<td class="p-2"> 
					<input class="form-control" type="file" id="aadhar_attachment" value="" accept=".jpg,.png,.pdf">
                	<input class="form-control " type="hidden" name="old_aadhar_attachment" value="<?php echo $attached_files['aadhar_attachment']; ?>" >
                	<?php if($attached_files['aadhar_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['aadhar_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['aadhar_attachment']; ?></a>
                	<?php } ?>
				</td>
			</tr>
			<?php }if($fieldaccesright['field']['passport']!="NA"){ 
				$readonlycheck = $fieldaccesright['field']['passport'] !="E"?"custom_readonly":"";
				?>
			<tr class="<?php echo $readonlycheck; ?>">
				<th class="p-2"><?php echo __('Passport'); ?></th>
				<td class="p-2">
					<input type="text" name="passport" class="form-control" value="<?php echo dynamicDataDecode($data['passport']); ?>" placeholder="<?php echo __('Enter Passport No.'); ?>">
				</td>
				<td class="p-2">
					<input type="text" name="passport_expiry_date" class="form-control datepicker" value="<?php echo ($data['passport_expiry_date']!="0000-00-00" AND $data['passport_expiry_date']!="")?fromDate($data['passport_expiry_date']):""; ?>" placeholder="<?php echo __('Enter Expiry Date'); ?>">
				</td>
				<td class="p-2"> 
					<input class="form-control" type="file" id="passport_attachment" value="" accept=".jpg,.png,.pdf">
                	<input class="form-control " type="hidden" name="old_passport_attachment" value="<?php echo $attached_files['passport_attachment']; ?>" >
                	<?php if($attached_files['passport_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['passport_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['passport_attachment']; ?></a>
                	<?php } ?>
				</td>
			</tr>
			<?php }if($fieldaccesright['field']['driving_licence']!="NA"){
			$readonlycheck = $fieldaccesright['field']['driving_licence'] !="E"?"custom_readonly":"";
			 ?>
			<tr class="<?php echo $readonlycheck; ?>"> 
				<th class="p-2"><?php echo __('Driving Licence'); ?></th>
				<td class="p-2">
					<input type="text" name="driving_licence" class="form-control" value="<?php echo dynamicDataDecode($data['driving_licence']); ?>" placeholder="<?php echo __('Enter Driving Licence'); ?>">
				</td>
				<td class="p-2">
					<input type="text" name="driving_licence_expiry_date" class="form-control datepicker" value="<?php echo ($data['driving_licence_expiry_date']!="0000-00-00" AND $data['driving_licence_expiry_date']!="")?fromDate($data['driving_licence_expiry_date']):""; ?>" placeholder="<?php echo __('Enter Driving Licence Expiry Date'); ?>">
				</td>
				<td class="p-2"> 
					<input class="form-control" type="file" id="driving_licence_attachment" value="" accept=".jpg,.png,.pdf">
                	<input class="form-control " type="hidden" name="old_driving_licence_attachment" value="<?php echo $attached_files['driving_licence_attachment']; ?>" >
                
                	<?php if($attached_files['driving_licence_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['driving_licence_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['driving_licence_attachment']; ?></a>
                	<?php } ?>
				</td>
			</tr>
			<?php }if($fieldaccesright['field']['pan_number']!="NA"){ 
				$readonlycheck = $fieldaccesright['field']['pan_number'] !="E"?"custom_readonly":"";
				?>
			<tr class="<?php echo $readonlycheck; ?>">
				<th class="p-2"><?php echo __('PAN Number'); ?></th>
				<td class="p-2">
					<input type="text" name="pan_number" class="form-control" value="<?php echo dynamicDataDecode($data['pan_number']); ?>" placeholder="<?php echo __('Enter PAN Number'); ?>">
				</td>
				<td class="p-2"> - </td>
				<td class="p-2"> 
					<input class="form-control" type="file" id="pan_attachment" value="" accept=".jpg,.png,.pdf">
                	<input class="form-control " type="hidden" name="old_pan_attachment" value="<?php echo $attached_files['pan_attachment']; ?>" >
                	
                	<?php if($attached_files['pan_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['pan_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['pan_attachment']; ?></a>
                	<?php } ?>
				</td>
			</tr>
			<?php }if($fieldaccesright['field']['pf_no']!="NA"){ 
				$readonlycheck = $fieldaccesright['field']['pf_no'] !="E"?"custom_readonly":"";
				?>
			<tr class="<?php echo $readonlycheck; ?>">
				<th class="p-2"><?php echo __('PF Number'); ?></th>
				<td class="p-2">
					<input type="text" name="pf_no" class="form-control" value="<?php echo dynamicDataDecode($data['pf_no']); ?>" placeholder="<?php echo __('Enter PF Number'); ?>">
				</td>
				<td class="p-2"> - </td>
				<td class="p-2"> 
					<input class="form-control" type="file" id="pf_attachment" value="" accept=".jpg,.png,.pdf">
                	<input class="form-control " type="hidden" name="old_pf_attachment" value="<?php echo $attached_files['pf_attachment']; ?>" >
                	
                	<?php if($attached_files['pf_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['pf_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['pf_attachment']; ?></a>
                	<?php } ?>
				</td>
			</tr>
			<?php }if($fieldaccesright['field']['pf_uan']!="NA"){ 
				$readonlycheck = $fieldaccesright['field']['pf_uan'] !="E"?"custom_readonly":"";
				?>
			<tr class="<?php echo $readonlycheck; ?>">
				<th class="p-2"><?php echo __('UAN Number'); ?></th>
				<td class="p-2">
					<input type="text" name="pf_uan" class="form-control" value="<?php echo dynamicDataDecode($data['pf_uan']); ?>" placeholder="<?php echo __('Enter UAN Number'); ?>">
				</td>
				<td class="p-2"> - </td>
				<td class="p-2"> 
					<input class="form-control" type="file" id="pf_uan_attachment" value="" accept=".jpg,.png,.pdf">
                	<input class="form-control " type="hidden" name="old_pf_uan_attachment" value="<?php echo $attached_files['pf_uan_attachment']; ?>" >
                	<?php if($attached_files['pf_uan_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['pf_uan_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['pf_uan_attachment']; ?></a>
                	<?php } ?>
				</td>
			</tr>
			<?php }if($fieldaccesright['field']['gosi_number']!="NA"){ 
				$readonlycheck = $fieldaccesright['field']['gosi_number'] !="E"?"custom_readonly":"";
				?>
			<tr class="<?php echo $readonlycheck; ?>">
				<th class="p-2"><?php echo __('GOSI Number'); ?></th>
				<td class="p-2">
					<input type="text" name="gosi_number" class="form-control" value="<?php echo dynamicDataDecode($data['gosi_number']); ?>" placeholder="<?php echo __('Enter GOSI Number'); ?>">
				</td>
				<td class="p-2"> - </td>
				<td class="p-2"> 
					<input class="form-control" type="file" id="gosi_attachment" value="" accept=".jpg,.png,.pdf">
                	<input class="form-control " type="hidden" name="old_gosi_attachment" value="<?php echo $attached_files['gosi_attachment']; ?>" >
                	<?php if($attached_files['gosi_attachment'] !=""){ ?>
                	<a href="uploads/all/<?php echo $attached_files['gosi_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['gosi_attachment']; ?></a>
                	<?php } ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php }elseif($_POST['pagetype'] =="experience_details" || $_POST['showpagetype'] =="experience_details"){
		$fieldaccesright = $vafield['experience_details'];
		if($fieldaccesright['field']['employee_experiances']!="NA"){ 
			$readonlycheck = $fieldaccesright['field']['employee_experiances'] !="E"?"custom_readonly":"";
		}	
	?>
	<table class="table <?php echo $readonlycheck; ?>" id="extable">
		<tr>
			<th width="30%"><?php echo __('Company Name'); ?> <span class="text-danger">*</span></th>
			<th width="30%"><?php echo __('Job Description'); ?></th>
			<th width="16%"> <?php echo __('From Date'); ?> <span class="text-danger">*</span></th>
			<th width="16%"> <?php echo __('To Date'); ?></th>
			<th width="8%"> <?php echo __('File'); ?> <span class="f-10"><?php echo __('(jpg,pdf)'); ?></span></th>
		</tr> 
		<?php 
		$k=1;
		if(count((array)$emp_work_experiences)=='0'){ 
			$emp_work_experiences[] = array(); 
		} 
        foreach ($emp_work_experiences as $expe) { 
			?>
			<tr id="exp_details_<?php echo $k; ?>">
				<td class="p-2">
					<input type="hidden" name="expe_rowid[]" class="form-control" autocomplete="off" value="<?php echo $expe['id']; ?>">
					<input type="text" name="company_name[]" class="form-control " autocomplete="off" value="<?php echo $expe['company_name']; ?>" placeholder="<?php echo __('Enter Company Name'); ?>"> 
				</td>
				<td class="p-2">
					<input type="text" name="job_descrption[]" class="form-control" value="<?php echo $expe['job_descrption']; ?>" autocomplete="off" placeholder="<?php echo __('Enter Job Description'); ?>">  
				</td>
				<td class="p-2">
					<input type="text d-block" name="work_from[]" class="form-control datepicker"  value="<?php echo ($expe['work_from']!="0000-00-00" AND $expe['work_from']!="")?fromDate($expe['work_from']):""; ?>" autocomplete="off" placeholder="<?php echo __('Enter From Date'); ?>"> 
				</td>
				<td class="p-2"> 	
					<input type="txet" name="work_to[]" class="form-control datepicker" value="<?php echo ($expe['work_to']!="0000-00-00" AND $expe['work_to']!="")?fromDate($expe['work_to']):""; ?>"autocomplete="off" placeholder="<?php echo __('Enter To Date'); ?>">  
				</td> 
				<td class="p-2 w-b-w-s">
					<div class="row">
						<div class="col-lg-6">
							<div class="image-input image-input-empty image-input-outline" id="kt_image_5">
				             	<label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="<?php echo __('Upload'); ?>">
				              	<span><i class="fa fa-upload"></i></span>
				              	<input type="file" name="work_experience_attachment" class="form-control" onchange="getAttachedFileName(this,'workexperiencefilename_<?php echo $k; ?>')">
				              	<input type="hidden" name="old_work_experience_attachment[]" value="<?php echo $expe['file']; ?>">
				            	</label>
				            </div>
				        </div>	
				        <div class="col-lg-6 p-0">
				        	<?php if($expe['file']!=""){ ?>
								<i class="ti-eye cursor-pointer" onclick="dynamicFilePriviews('uploads/all/<?php echo $expe['file']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo __('Attachments Preview'); ?>"></i>
		          			<?php } ?>
		         		</div>
		         	</div>
		          	<span id="workexperiencefilename_<?php echo $k; ?>" class="cursor-pointer custom-limit-text" style="--linehide: 1;"></span>
				</td>
				<td class="p-2">		             
	            	<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('exp_details_<?php echo $k; ?>')" data-bs-original-title="<?php echo __('Remove'); ?>">  
	               <i class="ti-trash"></i>
	            	</span>
				</td>
			</tr>
		<?php $k++; }  ?>
		</table>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<button class="btn btn-success"  onclick="addexpreencerows()" id="addRows" type="button"><?php echo __('+ Add More'); ?></button>
			</div>
		</div>


   <?php }elseif($_POST['pagetype'] =="employee_qualifications" || $_POST['showpagetype'] =="employee_qualifications"){
		$fieldaccesright = $vafield['employee_qualifications'];
		if($fieldaccesright['field']['employee_qualifications']!="NA"){ 
			$readonlycheck = $fieldaccesright['field']['employee_qualifications'] !="E"?"custom_readonly":"";
		}	
	?>	

	<table class="table <?php echo $readonlycheck; ?>" id="edutable">
		<tr>
			<th class="" width="20%"><?php echo __('Institute Name'); ?> <span class="text-danger">*</span></th>
			<th class="thheader" width="20%"><?php echo __('Type'); ?> <span class="text-danger">*</span></th>
			<th class="thheader" width="15%"> <?php echo __('Start Date'); ?> <span class="text-danger">*</span></th>
			<th class="thheader" width="15%"> <?php echo __('End Date'); ?></th>
			<th class="thheader" width="10%"><?php echo __('Score'); ?></th>
			<th width="10%"> <?php echo __('File'); ?> <span class="f-10"><?php echo __('(jpg,pdf)'); ?></span></th>
			<th width="10%"> </th>
		</tr>
		<?php 
		if(count((array)$emp_educations)=='0'){ 
			$emp_educations[] = array(); 
		} 
		$mk = 1;
		foreach($emp_educations as $inst) { ?>
			<tr id="emp_educations_<?php echo $mk; ?>">
				<td class="p-2">
					<input type="hidden" name="institute_rowid[]" class="form-control " autocomplete="off" value="<?php echo $inst['id']; ?>">
					<input type="text" name="institute_name[]" class="form-control" value="<?php echo $inst['institute_name']; ?>" autocomplete="off" placeholder="<?php echo __('Enter Institute Name'); ?>"> 
				</td>
				<td class="w-full p-2">
					<select class="form-control form-select js-example-basic-single" name="education_type[]" >
												<option value=""> <?php echo __('Select Any One'); ?></option>
						<?php foreach ((array)$education_type as $edutype) { ?> 
							<option <?php if(($inst['education_type'] ?? '') == $edutype['genc_detail_code']){ ?> selected <?php } ?> value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo __($edutype['genc_detail_name']); ?></option>
						<?php } ?>
					</select>
				</td>
				<td class="p-2">
					<input type="text" name="start_date[]" class="form-control datepicker" value="<?php echo (!empty($inst['start_date']) && $inst['start_date']!="0000-00-00")?fromDate($inst['start_date']):""; ?>" autocomplete="off" placeholder="<?php echo __('Enter Start date'); ?>">
				</td>
				<td class="p-2">
					<input type="text" name="end_date[]" class="form-control datepicker" value="<?php echo (!empty($inst['end_date']) && $inst['end_date']!="0000-00-00")?fromDate($inst['end_date']):""; ?>" autocomplete="off" placeholder="<?php echo __('Enter End Date'); ?>">
				</td>
				<td class="p-2">
					<input type="text" name="score[]" class="form-control" value="<?php echo $inst['score'] ?? ''; ?>" autocomplete="off" placeholder="<?php echo __('Enter Score'); ?>">
				</td>
				<td class="p-2 w-b-w-s">
					<div class="row">
						<div class="col-lg-6">
							<div class="image-input image-input-empty image-input-outline" id="kt_image_5">
				             	<label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="<?php echo __('Upload'); ?>">
				              	<span><i class="fa fa-upload"></i></span>
				              	<input type="file" name="educations_attachment" class="form-control" onchange="getAttachedFileName(this,'educationsfilename_<?php echo $mk; ?>')">
				              	<input type="hidden" name="old_educations_attachment[]" value="<?php echo $inst['file'] ?? ''; ?>">
				            	</label>
				            </div>
				        </div>	
				        <div class="col-lg-6 p-0">
				        	<?php if(!empty($inst['file'])){ ?>
								<i class="ti-eye cursor-pointer" onclick="dynamicFilePriviews('uploads/all/<?php echo $inst['file']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo __('Attachments Preview'); ?>"></i>
		          			<?php } ?>
		         		</div>
		         	</div>
		          	<span id="educationsfilename_<?php echo $mk; ?>" class="cursor-pointer custom-limit-text" style="--linehide: 1;"></span>
				</td>
				<td class="p-2">		             
	            	<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('emp_educations_<?php echo $mk; ?>')" data-bs-original-title="<?php echo __('Remove'); ?>">  
	               <i class="ti-trash"></i>
	            	</span>
				</td>
			</tr>
		<?php $mk++; } ?>
	</table>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button class="btn btn-success" onclick="addEmpEducationsRows()" id="addRows" type="button"><?php echo __('+ Add More'); ?></button>
		</div>
	</div>

   <?php }elseif($_POST['pagetype'] =="employee_dependents" || $_POST['showpagetype'] =="employee_dependents"){
		$fieldaccesright = $vafield['employee_dependents'];
		$readonlycheck = "";
		if(isset($fieldaccesright['field']['employee_dependents']) && $fieldaccesright['field']['employee_dependents']!="NA"){ 
			$readonlycheck = $fieldaccesright['field']['employee_dependents'] !="E"?"custom_readonly":"";
		}	
	?>	
	<table class="table <?php echo $readonlycheck; ?>" id="deptable">
		<tr>
			<th width="30%"><?php echo __('Name'); ?> <span class="text-danger">*</span></th>
			<th width="30%"><?php echo __('Relationship Type'); ?> <span class="text-danger">*</span></th>
			<th width="30%"><?php echo __('Date Of Birth'); ?></th>
			<th width="10%"> </th>
		</tr>
		<?php 
		if(count((array)$emp_dependents)=='0'){ 
			$emp_dependents[] = array(); 
		} 
		$dk = 1;
		foreach($emp_dependents as $depe) { ?>
			<tr id="employee_dependents_<?php echo $dk; ?>">
				<td class="p-2">
					<input type="hidden" name="dependent_rowid[]" class="form-control" autocomplete="off" value="<?php echo $depe['id'] ?? ''; ?>">
					<input type="text" name="dependent_name[]" class="form-control" value="<?php echo $depe['dependent_name'] ?? ''; ?>" autocomplete="off" placeholder="<?php echo __('Enter Dependent Name'); ?>"> 
				</td>
				<td class="w-full p-2">
					<select class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" >
						<option value=""> <?php echo __('Select Any One'); ?></option>
						<?php foreach ((array)$relations as $reladata) { ?> 
							<option <?php if(($depe['dependent_relationship_type'] ?? '') == $reladata['id']){ ?> selected <?php } ?> value="<?php echo $reladata['id']; ?>"> <?php echo __(replaceNetTabAndsinglequote($reladata['relation_name'])); ?></option>
						<?php } ?>
					</select>
				</td>
				<td class="p-2">
					<input type="text" name="dependent_date_of_birth[]" class="form-control datepicker" value="<?php echo (!empty($depe['dependent_date_of_birth']) && $depe['dependent_date_of_birth']!="0000-00-00")?fromDate($depe['dependent_date_of_birth']):""; ?>" autocomplete="off" placeholder="<?php echo __('Enter Date Of Birth'); ?>">
				</td>
				<td class="p-2">		             
	            	<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('employee_dependents_<?php echo $dk; ?>')" data-bs-original-title="<?php echo __('Remove'); ?>">  
	               <i class="ti-trash"></i>
	            	</span>
				</td>
			</tr>
		<?php $dk++; } ?>
	</table>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button class="btn btn-success" onclick="adddeprows()" id="addRows" type="button"><?php echo __('+ Add More'); ?></button>
		</div>
	</div>

   <?php }elseif($_POST['pagetype'] =="employee_contacts" || $_POST['showpagetype'] =="employee_contacts"){
		$fieldaccesright = $vafield['employee_contacts'];
		$readonlycheck = "";
		if(isset($fieldaccesright['field']['employee_contacts']) && $fieldaccesright['field']['employee_contacts']!="NA"){ 
			$readonlycheck = $fieldaccesright['field']['employee_contacts'] !="E"?"custom_readonly":"";
		}	
	?>	
	<table class="table <?php echo $readonlycheck; ?>" id="emergetable">
		<tr>
			<th width="25%"><?php echo __('Name'); ?> <span class="text-danger">*</span></th>
			<th width="25%"><?php echo __('Relationship'); ?> <span class="text-danger">*</span></th>
			<th width="20%"><?php echo __('Home No'); ?></th>
			<th width="20%"><?php echo __('Mobile No'); ?></th>
			<th width="10%"> </th>
		</tr>
		<?php 
		if(count((array)$emp_contacts)=='0'){ 
			$emp_contacts[] = array(); 
		} 
		$ck = 1;
		foreach($emp_contacts as $eno) { ?>
			<tr id="emp_contacts_<?php echo $ck; ?>">
				<td class="p-2">
					<input type="hidden" name="contact_rowid[]" class="form-control" autocomplete="off" value="<?php echo $eno['id'] ?? ''; ?>">
					<input type="text" name="contact_name[]" class="form-control" value="<?php echo $eno['contact_name'] ?? ''; ?>" autocomplete="off" placeholder="<?php echo __('Enter Contact Name'); ?>"> 
				</td>
				<td class="w-full p-2">
					<select class="form-control form-select js-example-basic-single" name="contact_relationship[]" >
						<option value=""> <?php echo __('Select Any One'); ?></option>
						<?php foreach ((array)$relations as $reladata) { ?> 
							<option <?php if(($eno['contact_relationship'] ?? '') == $reladata['id']){ ?> selected <?php } ?> value="<?php echo $reladata['id']; ?>"> <?php echo __(replaceNetTabAndsinglequote($reladata['relation_name'])); ?></option>
						<?php } ?>
					</select>
				</td>
				<td class="p-2">
					<input type="text" name="contact_home_no[]" class="form-control" value="<?php echo $eno['contact_home_no'] ?? ''; ?>" autocomplete="off" placeholder="<?php echo __('Enter Contact Home No'); ?>">
				</td>
				<td class="p-2">
					<input type="text" name="contact_mobile_no[]" class="form-control" value="<?php echo $eno['contact_mobile_no'] ?? ''; ?>" autocomplete="off" placeholder="<?php echo __('Enter Contact Mobile No'); ?>">
				</td>
				<td class="p-2">		             
	            	<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('emp_contacts_<?php echo $ck; ?>')" data-bs-original-title="<?php echo __('Remove'); ?>">  
	               <i class="ti-trash"></i>
	            	</span>
				</td>
			</tr>
		<?php $ck++; } ?>
	</table>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button class="btn btn-success" onclick="addEmployeeContactsRows()" id="addRows" type="button"><?php echo __('+ Add More'); ?></button>
		</div>
	</div>

   <?php }elseif($_POST['pagetype'] =="emp_visa" || $_POST['showpagetype'] =="emp_visa"){
		$fieldaccesright = $vafield['emp_visa'];
		$readonlycheck = "";
		if(isset($fieldaccesright['field']['emp_visa']) && $fieldaccesright['field']['emp_visa']!="NA"){ 
			$readonlycheck = $fieldaccesright['field']['emp_visa'] !="E"?"custom_readonly":"";
		}	
	?>	
	<table class="table <?php echo $readonlycheck; ?>" id="emp_visatable">
		<tr>
			<th width="30%"><?php echo __('Visa Number'); ?> <span class="text-danger">*</span></th>
			<th width="30%"><?php echo __('Country'); ?> <span class="text-danger">*</span></th>
			<th width="15%"><?php echo __('Issue Date'); ?> <span class="text-danger">*</span></th>
			<th width="15%"><?php echo __('Expiry Date'); ?></th>
			<th width="10%"> </th>
		</tr>
		<?php 
		if(count((array)$emp_visa)=='0'){ 
			$emp_visa[] = array(); 
		} 
		$vk = 1;
		foreach($emp_visa as $visa) { ?>
			<tr id="emp_visa_<?php echo $vk; ?>">
				<td class="p-2">
					<input type="hidden" name="visa_rowid[]" class="form-control" autocomplete="off" value="<?php echo $visa['id'] ?? ''; ?>">
					<input type="text" name="visa_number[]" class="form-control" value="<?php echo $visa['visa_number'] ?? ''; ?>" autocomplete="off" placeholder="<?php echo __('Enter Visa Number'); ?>"> 
				</td>
				<td class="w-full p-2">
					<select class="form-control form-select js-example-basic-single" name="country_id[]" >
						<option value=""> <?php echo __('Select Any One'); ?></option>
						<?php foreach ((array)$countries as $countrys) { ?> 
							<option <?php if(($visa['country_id'] ?? '') == $countrys['id']){ ?> selected <?php } ?> value="<?php echo $countrys['id']; ?>"> <?php echo __(replaceNetTabAndsinglequote($countrys['country_name'])); ?></option>
						<?php } ?>
					</select>
				</td>
				<td class="p-2">
					<input type="text" name="visa_issue_date[]" class="form-control datepicker" value="<?php echo (!empty($visa['visa_issue_date']) && $visa['visa_issue_date']!="0000-00-00")?fromDate($visa['visa_issue_date']):""; ?>" autocomplete="off" placeholder="<?php echo __('Enter Visa Issue Date'); ?>">
				</td>
				<td class="p-2">
					<input type="text" name="visa_expiry_date[]" class="form-control datepicker" value="<?php echo (!empty($visa['visa_expiry_date']) && $visa['visa_expiry_date']!="0000-00-00")?fromDate($visa['visa_expiry_date']):""; ?>" autocomplete="off" placeholder="<?php echo __('Enter Visa Expiry Date'); ?>">
				</td>
				<td class="p-2">		             
	            	<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('emp_visa_<?php echo $vk; ?>')" data-bs-original-title="<?php echo __('Remove'); ?>">  
	               <i class="ti-trash"></i>
	            	</span>
				</td>
			</tr>
		<?php $vk++; } ?>
	</table>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button class="btn btn-success" onclick="addEmpVisatableRows()" id="addRows" type="button"><?php echo __('+ Add More'); ?></button>
		</div>
	</div>

<?php } ?>
<?php } //INSERT part End ?>