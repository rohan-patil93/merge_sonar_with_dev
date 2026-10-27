<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$wh1["where"] = array('table_name'=>$_POST['process_type'],'is_active'=>0,'company_id'=>$_SESSION['company_id']);
$wh1["field"] = "id,name,status";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=status_progress&data=".$data1; 
$statusapi = callAPI($url1);
$statusrow = json_decode($statusapi,1);

$field_list = getListAttributes($_POST['process_type']);

$workflowlist = getList('workflow_configuration',$_POST['rid']);
$workflowlist = json_decode($workflowlist,1);
if($_POST['approval_type'] =='custom_process'){
	$stepsapr  = json_decode($workflowlist['sub_approve_type'],1); 
	$_POST['sub_approve_type'] = count((array)$stepsapr);	
}

?>
<script type="text/javascript">
	function submitrom(val){

	var formdata = $("#"+val).serialize();
	jQuery.ajax({
			url: "ajax.php",
			data:formdata+'&page=commanworkflowsubmit',
			type: "POST",
			success:function(data){
				console.log(data);
				if($('#type').val() == 'aprove_updatefields'){
				$(".hidaprove_updatefields").val(data);
			}
				$('#kt_modal_dynamic_popup2').modal("hide");
				tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');
				//$('#kt_modal_dynamic_popup').modal("hide");
			},
			error:function (){}
		});	

}
</script>
<div class="modal-header">
	<h5 class="modal-title">
		Update Status
	</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<?php if($_POST['type'] == "aprove_updatefields"){ 

	$updatefields = json_decode($workflowlist['approve_update_status'],1); ?>
<form method="post" id="aprove_updatefields">	
<div class="modal-body j-pro" > 
	<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
		
<?php  if($_POST['approval_type'] =='role_levels' || $_POST['approval_type'] =='user_hierarchical' || $_POST['approval_type'] =='custom_process'){  $mk=0;
	$_POST['sub_approve_type'] = $_POST['sub_approve_type']+1;
	for($i=1;$i <= $_POST['sub_approve_type'];$i++){ 
		?>
		<div class="form-group row">
			<div class="col-lg-2 mt-4">Level <label class="form-label badge bg-info "><?php echo $i; ?></label></div>
			<div class="col-lg-3">
				<label class="form-label">Status</label>
				<select class="form-select js-example-basic-single" name="status[]" >
				<option value="">Select Any One</option>	
					<?php foreach($statusrow as $statusrows){ ?>
						<option value="<?php echo $statusrows['status']; ?>" <?php echo $statusrows['status']==$updatefields[$mk]['status']?"selected":""; ?>><?php echo $statusrows['name'];?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-lg-2">
				<label class="form-label">Followup Days</label>
				<input class="form-control"  type="number" name="followup_days[]" value="<?php echo  $updatefields[$mk]['followup_days']; ?>" placeholder="Eg.5">
			</div>
			<div class="col-lg-2">
				<label class="form-label">Auto Forward Days</label>
				<input class="form-control"  type="number" name="auto_forword_days[]" value="<?php echo  $updatefields[$mk]['auto_forword_days']; ?>" placeholder="Eg.2">
			</div>
			<div class="col-lg-3">
				<label class="form-label">Work Flow Field</label>
				<select class="form-select js-example-basic-single" name="work_flow_field[]" >
				<option value="">Select Any One</option>	
					<?php  foreach($field_list as $ros){  ?>
						<option value="<?php echo $ros['Field']; ?>" <?php echo $ros['Field']==$updatefields[$mk]['work_flow_field']?"selected":""; ?>><?php echo $ros['Field'];?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php $mk++;  } }else{  

for($i=0;$i <= 1;$i++){ 
		?>
		<div class="form-group row">
			<div class="col-lg-2 mt-4">Level <label class="form-label badge bg-info "><?php echo $mk; ?></label></div>
			<div class="col-lg-3">
				<label class="form-label">Status</label>
				<select class="form-select js-example-basic-single" name="status[]" >
				<option value="">Select Any One</option>	
					<?php foreach($statusrow as $statusrows){ ?>
						<option value="<?php echo $statusrows['status']; ?>" <?php echo $statusrows['status']==$updatefields[$i]['status']?"selected":""; ?>><?php echo $statusrows['name'];?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-lg-2">
				<label class="form-label">Followup Days</label>
				<input class="form-control"  type="number" name="followup_days[]" value="<?php echo  $updatefields[$i]['followup_days']; ?>" placeholder="Eg.5">
			</div>
			<div class="col-lg-2">
				<label class="form-label">Auto Forward Days</label>
				<input class="form-control"  type="number" name="auto_forword_days[]" value="<?php echo  $updatefields[$i]['auto_forword_days']; ?>" placeholder="Eg.2">
			</div>
			<div class="col-lg-3">
				<label class="form-label">Work Flow Field</label>
				<select class="form-select js-example-basic-single" name="work_flow_field[]" >
				<option value="">Select Any One</option>	
					<?php  foreach($field_list as $ros){  ?>
						<option value="<?php echo $ros['Field']; ?>" <?php echo $ros['Field']==$updatefields[$i]['work_flow_field']?"selected":""; ?>><?php echo $ros['Field'];?></option>
					<?php } ?>
				</select>
			</div>
		</div>

				<?php $mk++; } } ?>	
	</div>
</div>


<!-- approval update fields -->
<?php }else{ ?> 
<!-- Reject update fields -->

<form method="post" id="rejection_updatefields">	
<div class="modal-body j-pro" > 
	<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
		
<?php $updatefields = json_decode($workflowlist['rejection_update_status'],1);
 /** if($_POST['approval_type'] =='role_levels' || $_POST['approval_type'] =='user_hierarchical' || $_POST['approval_type'] =='custom_process'){   $mk=0;
	$_POST['sub_approve_type'] = $_POST['sub_approve_type']+1;
	for($i=1;$i <= $_POST['sub_approve_type'];$i++){ 
		?>
		<div class="form-group row">
			<div class="col-lg-2 mt-4">Level <label class="form-label badge bg-info "><?php echo $i; ?></label></div>
			<div class="col-lg-3">
				<label class="form-label">Status</label>
				<select class="form-select js-example-basic-single" name="status[]" >
				<option value="">Select Any One</option>	
					<?php foreach($statusrow as $statusrows){ ?>
						<option value="<?php echo $statusrows['status']; ?>" <?php echo $statusrows['status']==$updatefields[$mk]['status']?"selected":""; ?>><?php echo $statusrows['name'];?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-lg-2">
				<label class="form-label">Followup Days</label>
				<input class="form-control"  type="number" name="followup_days[]" value="<?php echo  $updatefields[$mk]['followup_days']; ?>" placeholder="Eg.5">
			</div>
			<div class="col-lg-2">
				<label class="form-label">Auto Forward Days</label>
				<input class="form-control"  type="number" name="auto_forword_days[]" value="<?php echo  $updatefields[$mk]['auto_forword_days']; ?>" placeholder="Eg.2">
			</div>
			<div class="col-lg-3">
				<label class="form-label">Work Flow Field</label>
				<select class="form-select js-example-basic-single" name="work_flow_field[]" >
				<option value="">Select Any One</option>	
					<?php  foreach($field_list as $ros){  ?>
						<option value="<?php echo $ros['Field']; ?>" <?php echo $ros['Field']==$updatefields[$mk]['work_flow_field']?"selected":""; ?>><?php echo $ros['Field'];?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php $mk++;  } }else{ **/  ?>
		<div class="form-group row">
			<div class="col-lg-2 mt-4">Level <label class="form-label badge bg-info ">1</label></div>
			<div class="col-lg-3">
				<label class="form-label">Status</label>
				<select class="form-select js-example-basic-single" name="status[]" >
				<option value="">Select Any One</option>	
					<?php foreach($statusrow as $statusrows){ ?>
						<option value="<?php echo $statusrows['status']; ?>" <?php echo $statusrows['status']==$updatefields[0]['status']?"selected":""; ?>><?php echo $statusrows['name'];?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-lg-2">
				<label class="form-label">Followup Days</label>
				<input class="form-control"  type="number" name="followup_days[]" value="<?php echo  $updatefields[0]['followup_days']; ?>" placeholder="Eg.5">
			</div>
			<div class="col-lg-2">
				<label class="form-label">Auto Forward Days</label>
				<input class="form-control"  type="number" name="auto_forword_days[]" value="<?php echo  $updatefields[0]['auto_forword_days']; ?>" placeholder="Eg.2">
			</div>
			<div class="col-lg-3">
				<label class="form-label">Work Flow Field</label>
				<select class="form-select js-example-basic-single" name="work_flow_field[]" >
				<option value="">Select Any One</option>	
					<?php  foreach($field_list as $ros){  ?>
						<option value="<?php echo $ros['Field']; ?>" <?php echo $ros['Field']==$updatefields[0]['work_flow_field']?"selected":""; ?>><?php echo $ros['Field'];?></option>
					<?php } ?>
				</select>
			</div>
		</div>

				<?php // } ?>	
	</div>
</div>


<?php } ?>
<div class="modal-footer">
	<input type="hidden" name="Rid" value="<?php echo $_POST['rid']; ?>">
<!-- 	<input type="hidden" name="type" value="<?php echo $_POST['type']; ?>">
 -->		<input type="hidden" name="type" id="type" value="<?php echo $_POST['type']; ?>">
	<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
	<button type="button" class="btn btn-primary mr-2" onclick="submitrom('<?php echo $_POST['type']; ?>')" >Submit</button>
</div>
</form>
<!-- Select 2 js -->
					<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
					<!-- Multiselect js -->
					<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
					</script>
					<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
					<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
					<!-- Custom js -->
					<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>