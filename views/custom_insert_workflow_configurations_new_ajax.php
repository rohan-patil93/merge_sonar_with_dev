<?php
if($_POST['ajax']  =="Y"){
	include_once '../includes/session.php';
	include_once '../dbconfig.php';
	include_once '../comm.php';
	$rid = $_POST['Rid'];
	$geturl = API."?mode=getworkflowconfiguration&rid=".$_POST['Rid']."&process_type=".$workflowlist['process_type']; 
	$getapi = callAPI($geturl);
	$getresult = json_decode($getapi,1);
	$workflowconfiguration = $getresult['workflow_configuration'];

}
//$roleprecesstypearray = array('user'=>'User','email_groups'=>'Email Group','access_levels'=>'Access Levels','manager'=>'Manager','Managers_Manager'=>'Managers Manager','individual_employee_role'=>'Individual Employee Role','buddy'=>'Buddy');	
$roleprecesstypearray = array('user'=>'User','email_groups'=>'Email Group','access_levels'=>'Access Levels','manager'=>'Manager','Managers_Manager'=>'Managers Manager','individual_employee_role'=>'Individual Employee Role','buddy'=>'Buddy','custom_field'=>'Custome Field','allbuddy'=>'All Buddy','location_wise'=>'Location Wise');	
function subProcessType($val,$getresult){
	$listval = array();
	//$option = "<option value=''>Select Any</option>";
	if($val=="user"){
		foreach($getresult['application_users'] as $lists){
			$listval[$lists['id']] =  $lists['display_name'];
		}
	}elseif($val == 'email_groups'){
		$listval = $getresult['email_groups'];
	}elseif($val == 'access_levels' || $val == 'location_wise'){
		$listval = $getresult['application_roles'];
	}elseif($val == 'role_levels' || $val =='user_hierarchical'){
		$listval = $getresult['role_levels'];
	}elseif($val == 'buddy'){
		$listval = $getresult['buddy'];
	}elseif($val == 'individual_employee_role'){
		$listval = $getresult['employment_roles'];
	}elseif($val == 'custom_field'){
		$listval = $getresult['custom_field'];
	}
	return $listval;
}
$approval_process = json_decode($workflowconfiguration['approval_process'],1);
$locationslist = $getresult['locations'];
$location_options_html = '<option value="">Select Location</option>';
if(count((array)$locationslist) > 0){
	foreach($locationslist as $loc){
		$locid = is_array($loc)?$loc['id']:$loc->id;
		$locname = is_array($loc)?$loc['location_name']:$loc->location_name;
		$location_options_html .= '<option value="'.$locid.'">'.$locname.'</option>';
	}
}


if($_POST['work_level_type'] =="custom_process"){
?>
<ul class="nav md-tabs infisuite_tab mt-2 ml-2" role="tablist">
	<?php for($i=1; $i <= $_POST['workflowlevelcnt']; $i++){ ?>
	<li class="nav-item">
		<a class="nav-link f-w-600 f-12 <?php echo $i==1?"active":""; ?>" data-bs-toggle="tab" href="#approvaltab_<?php echo $i; ?>" role="tab" aria-selected="true"><i class="fa fa-check-circle text-warning"></i> <i class="fa fa-hourglass-half text-danger"></i> Level <?php echo $i; ?></a>
	</li>
	<?php } ?>
</ul>
<div class="tab-content tabs mt-3">
	<?php $statusinc = 100; for($i=1; $i <= $_POST['workflowlevelcnt']; $i++){ 
		$approvalprocess = $approval_process[$i];
		$statusinc++;
		
		//print_r($approvalprocess);
		?>
	<input type="hidden" name="workflow_level_seq[]" value="<?php echo $i; ?>">	
	<input type="hidden" name="workflow_level_status[<?php echo $i; ?>]" value="<?php echo $statusinc; ?>">	
	<div class="tab-pane card <?php echo $i==1?"active":""; ?> p-0" id="approvaltab_<?php echo $i; ?>" role="tabpanel">
		<div class="card-body">
			<div class="row mt-4">
				<div class="col-lg-12 form-group"> 
					<span class="form-label d-block">Doc Update</span>
					<input aria-label="Checkbox" type="checkbox" class="custome_js-switch" name="updatebtn[<?php echo $i; ?>]" <?php echo $approvalprocess['updatebtn']=="0"?"checked":""; ?> value="0"/>
				</div>
				<div class="col-lg-4 form-group w-full">
					<label class="form-label d-block">Approval Type <?php echo $i; ?></label>
					<select aria-label="Select option" class="form-select js-example-basic-single approval_type" name="approval_type[<?php echo $i; ?>]" onchange="selectapprovetypefun(this.value,'<?php echo $rid; ?>','<?php echo $i; ?>')" >
						<option value="">Select Any</option>
						<?php foreach($roleprecesstypearray as $key=>$val){ ?>
							<option value="<?php echo $key; ?>" <?php echo $approvalprocess['approval_type']==$key?"selected":""; ?>> <?php echo $val; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php 
				$showsublist = ["user","email_groups","access_levels","buddy","individual_employee_role","custom_field", "location_wise"];
				?>	
				<?php // echo '<pre>'; print_r($approvalprocess); ?> 
				<div class="col-lg-4 form-group w-full" id="sub_approve_type<?php echo $i; ?>" style="display:<?php echo (in_array($approvalprocess['approval_type'], $showsublist) && $approvalprocess['approval_type']!="" )?"":"none"?>">
					<span class="form-label d-block">Sub Type </span>
					<select aria-label="Select option" class="form-select js-example-basic-single sub_approve_type<?php echo $i; ?>" name="sub_approve_type[<?php echo $i; ?>]">
						<?php $sublist = subProcessType($approvalprocess['approval_type'],$getresult);
						if(count((array)$sublist) > 0){
						foreach($sublist as $keyk=>$aray){  ?>
							<option value='<?php echo $keyk; ?>'   <?php echo $approvalprocess['sub_approve_type']==$keyk?"selected":""; ?>><?php echo $aray; ?></option>";

						<?php	} } ?>
					</select>
				</div>
				<div class="col-lg-4 form-group w-full" id="sub_approve_field<?php echo $i; ?>" style="display:<?php echo $approvalprocess['approval_type']=='location_wise'?'':'none'; ?>">
					<span class="form-label d-block">Sub Field </span>
					<select class="form-select js-example-basic-single sub_approve_field<?php echo $i; ?>" name="sub_approve_field[<?php echo $i; ?>]">
						<option value="">Select Any</option>
						<?php $fieldlist = $getresult['custom_field'];
						if(count((array)$fieldlist) > 0){
						foreach($fieldlist as $fieldkey=>$fieldlabel){  ?>
							<option value='<?php echo $fieldkey; ?>' <?php echo $approvalprocess['sub_approve_field']==$fieldkey?"selected":""; ?>><?php echo $fieldlabel; ?></option>
						<?php	} } ?>
					</select>
				</div>
			</div>
			<div class="row mt-4 ">
				<?php $showsublistanyall = ["user","manager","Managers_Manager","custom_process","buddy","custom_field"]; ?>
				<div class="col-lg-5 approval_order_process<?php echo $i; ?>" style="display:<?php echo (!in_array($approvalprocess['approval_type'], $showsublistanyall) && $approvalprocess['approval_type']!="")?"":"none"?>">
					<div class="form-radio">
						<div class="radio radio-inline">
							<label class="form-label">
								<input type="radio" name="approval_user_type<?php echo $i; ?>" value="A" class="d-none" <?php echo $approvalprocess['approval_user_type']=="A"?"checked":""; ?> onchange="approvalProcess(this.value,'<?php echo $i; ?>')">
								<i class="helper"></i>Anyone
							</label>
						</div>
						<div class="radio radio-inline">
							<label class="form-label">
								<input type="radio" name="approval_user_type<?php echo $i; ?>" value="E"  class="d-none" <?php echo $approvalprocess['approval_user_type']=="E" || $approvalprocess['approval_type']==""?"checked":""; ?> onchange="approvalProcess(this.value,'<?php echo $i; ?>')">
								<i class="helper"></i>Everyone
							</label>
						</div>
						<div class="radio radio-inline">
							<label class="form-label">
								<input type="radio" name="approval_user_type<?php echo $i; ?>" value="M" class="d-none" <?php echo $approvalprocess['approval_user_type']=="M"?"checked":""; ?> onchange="approvalProcess(this.value,'<?php echo $i; ?>')">
								<i class="helper"></i>Minimum
							</label>
						</div>
					</div>
				</div>
				<div class="col-lg-7 row" >
					<div class="col-lg-2 minimum<?php echo $i; ?>" style="display:<?php echo (!in_array($approvalprocess['approval_type'], $showsublistanyall) && $approvalprocess['approval_user_type']=='M')?"":"none"; ?>">
						<input class="form-control " id="minimums<?php echo $i; ?>" type="text" name="minimum[<?php echo $i; ?>]" value="<?php echo $approvalprocess['minimum']; ?>" placeholder="Eg. 11">
					</div>
				</div>
			</div>
			<div class="row mt-4 ">
				<?php
				$location_approvers = $approvalprocess['location_approvers'];
				if(count((array)$location_approvers) == 0){
					$location_approvers = array(array('location_id'=>'','user_ids'=>array()));
				}
				?>
				<div class="col-lg-12 location_wise_block<?php echo $i; ?>" style="display:<?php echo $approvalprocess['approval_type']=='location_wise'?'':'none'; ?>">
					<span class="form-label d-block">Location Wise Approvers</span>
					<script type="text/template" class="location_wise_location_options_tpl"><?php echo $location_options_html; ?></script>
					<div id="location_wise_rows_<?php echo $i; ?>" class="location_wise_rows" data-level="<?php echo $i; ?>" data-next-row="<?php echo count((array)$location_approvers) + 1; ?>">
						<?php $rowIdx = 0; if(count((array)$location_approvers) > 0){ foreach($location_approvers as $locApprover){
							$rowIdx++;
							$locId = $locApprover['location_id'];
							$savedUserIds = array();
							if(count((array)$locApprover['user_ids']) > 0){
								foreach($locApprover['user_ids'] as $savedUid){
									$savedUserIds[] = (string)$savedUid;
								}
							}
							$selectedUsersAttr = htmlspecialchars(json_encode($savedUserIds), ENT_QUOTES);
						?>
						<div class="row mt-2 location_wise_row" data-level="<?php echo $i; ?>" data-row="<?php echo $rowIdx; ?>">
							<div class="col-lg-4 form-group">
								<select class="form-select location_wise_location_select" name="location_wise_location[<?php echo $i; ?>][<?php echo $rowIdx; ?>]" data-level="<?php echo $i; ?>" data-row="<?php echo $rowIdx; ?>" onchange="locationWiseLocationChange(this)">
									<option value="">Select Location</option>
									<?php if(count((array)$locationslist) > 0){ foreach($locationslist as $loc){
										$locid = is_array($loc)?$loc['id']:$loc->id;
										$locname = is_array($loc)?$loc['location_name']:$loc->location_name;
									?>
									<option value="<?php echo $locid; ?>" <?php echo $locId==$locid?"selected":""; ?>><?php echo $locname; ?></option>
									<?php }} ?>
								</select>
							</div>
							<div class="col-lg-6 form-group">
								<select class="form-select location_wise_users_select" name="location_wise_users[<?php echo $i; ?>][<?php echo $rowIdx; ?>][]" multiple="multiple" data-level="<?php echo $i; ?>" data-row="<?php echo $rowIdx; ?>" data-selected-users="<?php echo $selectedUsersAttr; ?>"></select>
							</div>
							<div class="col-lg-2 form-group p-t-25">
								<i class="ti-trash text-danger f-18 remove_location_wise_row"></i>
							</div>
						</div>
						<?php }} ?>
					</div>
					<button type="button" class="btn btn-primary btn-sm mt-2 add_location_wise_row" data-level="<?php echo $i; ?>"><i class="ti-plus"></i> Add More</button>
				</div>
			</div>
		</div>
	</div>
<?php   } ?>
</div>
<?php 
	}else{ 
		echo "<div class='row'>";
		$statusinc = 100; for($i=1; $i <= $_POST['workflowlevelcnt']; $i++){ 
			$approvalprocess = $approval_process[$i];
			$statusinc++;
	?>
		<div class="col-lg-3">
			<div class="card">
				<div class="card-header">
					<h5>Level - <?php echo $i; ?></h5>
				</div>
				<div class="card-body">
					<div class="form-group"> 
						<span class="form-label d-block">Updatable Transactional</span>
						<input aria-label="Checkbox" type="checkbox" class="custome_js-switch" name="updatebtn[<?php echo $i; ?>]" <?php echo $approvalprocess['updatebtn']=="0"?"checked":""; ?> value="0"/>
					</div>
					<input type="hidden" name="workflow_level_seq[]" value="<?php echo $i; ?>">	
					<input type="hidden" name="workflow_level_status[<?php echo $i; ?>]" value="<?php echo $statusinc; ?>">
					<input type="hidden" name="approval_type[<?php echo $i; ?>]" value="<?php echo $_POST['work_level_type']; ?>">
					<!-- <input type="hidden" name="updatebtn[<?php echo $i; ?>]" value="1"> -->
				</div>
			</div>
		</div>	
		
	<?php	} 
	echo "</div>";
	}
   ?>
<?php  /**if($_POST['ajax']  =="Y"){ ?>
   <script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>
	<script type="text/javascript" src="assets/bower_components/bootstrap-maxlength/js/bootstrap-maxlength.js"></script>
	<script type="text/javascript" src="assets/pages/advance-elements/swithces.js"></script>
	<?php  } **/?>