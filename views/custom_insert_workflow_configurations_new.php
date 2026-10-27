<?php
//echo "<pre>";print_r($_POST);echo "</pre>";
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['inputmask'] = 1;
$roleprecesstypearray = array('user'=>'User','email_groups'=>'Email Group','access_levels'=>'Access Levels','manager'=>'Manager','individual_employee_role'=>'Individual Employee Role','buddy'=>'Buddy','individual_employee_role'=>'Individual Employee Role','custom_field'=>'Custome Field');
if(isset($_POST['submit'])){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	foreach($_POST['workflow_level_seq'] as $seq){
		$updatebtn = $_POST['updatebtn'][$seq] == '0'?"0":"1";
		$workflowlevel[$seq] = array("workflow_level_seq"=>$seq,"approval_type"=>$_POST['approval_type'][$seq],"sub_approve_type"=>$_POST['sub_approve_type'][$seq],"minimum"=>$_POST['minimum'][$seq],"approval_user_type"=>$_POST['approval_user_type'.$seq],'workflow_level_status'=>$_POST['workflow_level_status'][$seq],'updatebtn'=>$updatebtn);
		if($_POST['approval_type'][$seq] == 'location_wise'){
			$workflowlevel[$seq]['sub_approve_field'] = $_POST['sub_approve_field'][$seq];
			$location_approvers = array();
			if(count((array)$_POST['location_wise_location'][$seq]) > 0){
				foreach($_POST['location_wise_location'][$seq] as $rowIdx=>$locId){
					if($locId != ""){
						$userIds = $_POST['location_wise_users'][$seq][$rowIdx];
						if(!is_array($userIds)){
							$userIds = array();
						}
						$location_approvers[] = array("location_id"=>$locId,"user_ids"=>$userIds);
					}
				}
			}
			$workflowlevel[$seq]['location_approvers'] = $location_approvers;
		}
	}
	if($_POST['workflow_level'] == '0'){
		$workflowlevel[1] = array("workflow_level_seq"=>"1","approval_type"=>"","sub_approve_type"=>"","sub_approve_field"=>"","minimum"=>"","approval_user_type"=>"",'workflow_level_status'=>'199','updatebtn'=>1);
	}
	//echo "<pre>";print_r($workflowlevel);echo "</pre>";
	foreach($_POST['approval_level_criteria'] as $seq=>$vl){
		foreach($_POST['doa_amount'][$seq] as $keyseq=>$doa_amount){
			if($keyseq == 1){
				$_POST['doa_amount_convert'][$seq][$keyseq]['start_doa'] = 0;	
			}else{
				$_POST['doa_amount_convert'][$seq][$keyseq]['start_doa'] = $_POST['doa_amount_convert'][$seq][$keyseq-1]['end_doa'];
			}
			$_POST['doa_amount_convert'][$seq][$keyseq]['end_doa'] = convertamountnumber($doa_amount);	
		}
		//$workflowapprovallevel[$seq] = array("approval_level_criteria"=>$vl,"doa_amount"=>$_POST['doa_amount'][$seq],"final_approval"=>$_POST['final_approval_'.$seq]);
	}
	$notification_message = array('approve_message'=>$_POST['approve_message'],'approve_message_information'=>$_POST['approve_message_information'],'reject_message'=>$_POST['reject_message'],'sendback_message'=>$_POST['sendback_message'],'completed_workflow_message'=>$_POST['completed_workflow_message'],'callback'=>$_POST['callback'],'employee_message'=>$_POST['employee_message']);
	$_POST['notification_message'] = json_encode($notification_message);
	
	//echo "<pre>";print_r($_POST);echo "</pre>";
	//approval_level_criteria
	$_POST['approval_process'] = json_encode($workflowlevel);
	//$_POST['approval_level_criteria'] = json_encode($workflowapprovallevel);
	$url = API."?mode=insertworkflownew&method=post";
	$respon = file_post_contents($url,$_POST,'insert');
	$response = json_decode($respon,1);
	if($_POST['Rid']==""){
		header("location:index.php?option=COM_INSERT_WORKFLOW_CONFIGURATIONS_NEW&Rid=".deocdeId("id:".$response['res']['LASTID']));
	}else{
		//header("location:index.php?option=COM_INSERT_WORKFLOW_CONFIGURATIONS_NEW&Rid=".deocdeId("id:".$_POST['Rid']));
	}
}

//echo "<pre>";print_r($response);echo "</pre>";
//echo "<pre>";print_r($workflowapprovallevel);echo "</pre>";
$customselectfieldarray = array("location_id"=>"Locations","division_id"=>"Division","department_id"=>"Department","cost_center_id"=>"Cost Center","type"=>"Transacton Type","opex_cpex"=>"Opex Capex");

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(":",$rid);
$rid = $rid['1'];
$geturl = API."?mode=getworkflowconfiguration&rid=".$rid; 
$getapi = callAPI($geturl);
$getresult = json_decode($getapi,1);
//ho "<pre>";print_r($getresult); echo "</pre>";
$ownertype = $getresult['ownertype'];
$workflowconfiguration = $getresult['workflow_configuration'];
$workflowconfigurationdetails = $getresult['workflow_configuration_details'];
$documentTypes = $getresult['document_types'] ?? array();
$prefillProcess = $workflowconfiguration['process_name'];
if (($prefillProcess == '' || $prefillProcess == '0' || $prefillProcess == null) && isset($_GET['table_name']) && $_GET['table_name'] != '') {
	$prefillProcess = $_GET['table_name'];
}
$criteria = json_decode($workflowconfiguration['criteria'],1);
$getdoacriteria = $getresult['dynamic_doa_criteria'];
$transaction_message_res = json_decode($workflowconfiguration['notification_message'],1);
$email_content = $getresult['email_content'];

foreach($getdoacriteria as $getdoacriteria1){
	$uniquetablevaluefield = array();
	foreach($getdoacriteria1 as $getdoacriteria2){
		$tablevalue = explode("=",$getdoacriteria2);
		$uniquetablevaluefield[] .= $tablevalue[0]."_".$tablevalue[2];
	}
	$dynamicdoacriteria[implode($uniquetablevaluefield)] = $getdoacriteria1;	
}

if(count((array)$getdoacriteria) ==0){
	$dynamicdoacriteria['criteria_no'] = array('criteria'=>'criteria=amount=no');
}
//echo "<pre>";print_r($dynamicdoacriteria_nocriteria);echo "</pre>";
// $when_to_exicute =  json_decode($workflowlist['when_to_exicute'],1);
// $criteria = json_decode($workflowlist['criteria'],1);
// $transaction_message_res = json_decode($workflowlist['transaction_message'],1);
// $approvefield_list = json_decode($workflowlist['approve_field'],1);
// $listfields = json_decode($getresult['display_attributes']['list_attributes'],1);
// $updatefields = json_decode($workflowlist['approve_update_status'],1);
$pagenameres = $getresult['display_attributes_list'];
foreach($pagenameres as $pagenamesres){
	$pagenamesrow[$pagenamesres['application_table_name']] = $pagenamesres;
}
$listfields = json_decode($pagenamesrow[$workflowconfiguration['process_name']]['list_attributes'],1);
//$priorityresult= $getresult['priority'];
//$statusrow  = $getresult['status_progress'];
// $mopdulesetup = $getresult['module_setup'];
// $locations = $getresult['locations'];
// $divisions = $getresult['divisions'];
// $departments = $getresult['departments'];
// $cost_centers = $getresult['cost_centers'];
//echo "<pre>";print_r($listfields);echo "</pre>";

foreach($workflowconfigurationdetails as $warkfdetails){
	$decodewf = json_decode($warkfdetails['approval_level_criteria'],1);
	$uniqd = array();
	foreach($decodewf as $table=>$uniqid){
		$uniqd[] = $table."_".$uniqid;
	}
	$uniid = implode($uniqd);
	$wfdetails[$uniid] = $warkfdetails;
}
//echo "<pre>";print_r($wfdetails);echo "</pre>";
$stausresult = ViewStatusData();
?>
<script type="text/javascript">
	function slidePopUpApprovalType(class_name){
		$("."+class_name).show('slow');
	}
	function ClosedslidePopUpApprovalType(class_name){
		$(".custom-popup-open").hide();
	}
	function selectapprovetypefun(val,id,level){
		$("#minimums").val('');
		var process_name  = $("#process_name").val();
		var showsublist = ["user","email_groups","access_levels","role_levels","user_hierarchical","buddy","individual_employee_role","custom_field", "location_wise"];

		var showsublistanyall = ["user","manager","Managers_Manager","role_levels","user_hierarchical","custom_process","buddy","custom_field"];

		if(jQuery.inArray(val, showsublistanyall) != -1){ 
			$(".approval_order_process"+level).hide();
			$(".minimum"+level).hide();
			$("#minimums"+level).val("");
		}else{
			$(".approval_order_process"+level).show();
			$(".minimum"+level).hide();
			$("#minimums"+level).val("");
		}

		if(jQuery.inArray(val, showsublist) != -1) { 
			$("#sub_approve_type"+level).show();
		}else{
			$("#sub_approve_type"+level).hide();
		}
		if(val == 'location_wise'){
			$("#sub_approve_field"+level).show();
		}else{
			$("#sub_approve_field"+level).hide();
			$(".sub_approve_field"+level).html("<option value=''>Select Any</option>");
		}
	if(val == "custom_process"){
		custompopups('custom_process',id);
	}else{
		jQuery.ajax({
			url: "ajax.php",
			data:'page=getapprovetypelist&val='+val+'&process_name='+process_name,
			type: "POST",
			success:function(data){
				console.log(data);
				var data  = JSON.parse(data);
				$(".sub_approve_type"+level).html(data.option);
				if(val == 'location_wise'){
					$(".sub_approve_field"+level).html(data.field_option ? data.field_option : "<option value=''>Select Any</option>");
				}
			}
		});
	}
	toggleLocationWiseBlock(val, level);
		//updateaApproveStatus(id);
	}
	var locationWiseRowCounter = {};
	function toggleLocationWiseBlock(val, level){
		if(val == 'location_wise'){
			$(".location_wise_block"+level).show('slow', function(){
				initAllLocationWiseBlocks($(".location_wise_block"+level));
			});
		}else{
			$(".location_wise_block"+level).hide('slow');
		}
	}
	function locationWiseLocationChange(el){
		var $row = $(el).closest('.location_wise_row');
		if($row.length == 0){
			return;
		}
		refreshLocationWiseUsers($row, $(el).val(), []);
	}
	function destroySelect2IfNeeded($el){
		if($el.length && $el.hasClass('select2-hidden-accessible')){
			$el.select2('destroy');
		}
	}
	function initLocationWiseRow($row){
		var $loc = $row.find('.location_wise_location_select');
		var $users = $row.find('.location_wise_users_select');
		destroySelect2IfNeeded($loc);
		destroySelect2IfNeeded($users);
		$loc.select2();
		$users.select2();
	}
	function getLocationOptionsHtml(level){
		var $tpl = $(".location_wise_block"+level).find(".location_wise_location_options_tpl").first();
		if($tpl.length == 0){
			$tpl = $("#workflow_level").find(".location_wise_location_options_tpl").first();
		}
		return $tpl.length ? $tpl.html() : '<option value="">Select Location</option>';
	}
	function buildLocationWiseRowHtml(level, rowIdx, locationOptionsHtml){
		var html = '';
		html += '<div class="row mt-2 location_wise_row" data-level="'+level+'" data-row="'+rowIdx+'">';
		html += '<div class="col-lg-4 form-group">';
		html += '<select class="form-select js-example-basic-single location_wise_location_select" name="location_wise_location['+level+']['+rowIdx+']" data-level="'+level+'" data-row="'+rowIdx+'" onchange="locationWiseLocationChange(this)">';
		html += locationOptionsHtml;
		html += '</select></div>';
		html += '<div class="col-lg-6 form-group">';
		html += '<select class="form-select js-example-basic-multiple location_wise_users_select" name="location_wise_users['+level+']['+rowIdx+'][]" multiple="multiple" data-level="'+level+'" data-row="'+rowIdx+'" data-selected-users="[]"></select>';
		html += '</div>';
		html += '<div class="col-lg-2 form-group p-t-25"><i class="ti-trash text-danger f-18 remove_location_wise_row"></i></div>';
		html += '</div>';
		return html;
	}
	function refreshLocationWiseUsers($row, locationId, selectedIds){
		var $userSelect = $row.find('.location_wise_users_select');
		if($userSelect.length == 0){
			return;
		}
		destroySelect2IfNeeded($userSelect);
		if(locationId == '' || locationId == undefined || locationId == null){
			$userSelect.html('');
			$userSelect.select2();
			$userSelect.val(null).trigger('change');
			return;
		}
		jQuery.ajax({
			url: "ajax.php",
			data: { page: 'getlocationwiseusers', location_id: locationId },
			type: "POST",
			success: function(data){
				var res = {};
				try {
					res = JSON.parse(data);
				} catch(e) {
					res = { option: '' };
				}
				destroySelect2IfNeeded($userSelect);
				$userSelect.html(res.option ? res.option : '');
				$userSelect.select2();
				if(selectedIds != undefined && selectedIds != null && selectedIds.length > 0){
					$userSelect.val(selectedIds).trigger('change');
				}else{
					$userSelect.val(null).trigger('change');
				}
				$userSelect.attr('data-selected-users', '[]');
			},
			error: function(){
				destroySelect2IfNeeded($userSelect);
				$userSelect.html('');
				$userSelect.select2();
				$userSelect.val(null).trigger('change');
			}
		});
	}
	function parseSelectedUsersAttr($userSelect){
		var raw = $userSelect.attr('data-selected-users');
		var selectedIds = [];
		if(raw != undefined && raw != ''){
			try {
				selectedIds = JSON.parse(raw);
			} catch(e) {
				selectedIds = [];
			}
		}
		if(!$.isArray(selectedIds)){
			selectedIds = [];
		}
		return selectedIds;
	}
	function initAllLocationWiseBlocks($scope){
		if($scope == undefined || $scope == null || $scope.length == 0){
			$scope = $("#workflow_level");
		}
		$scope.find('.location_wise_row').each(function(){
			var $row = $(this);
			var $block = $row.closest('[class*="location_wise_block"]');
			if($block.length && !$block.is(':visible')){
				return;
			}
			var level = $row.data('level');
			var rowIdx = parseInt($row.data('row'), 10);
			if(locationWiseRowCounter[level] == undefined || locationWiseRowCounter[level] < rowIdx){
				locationWiseRowCounter[level] = rowIdx;
			}
			initLocationWiseRow($row);
			var locationId = $row.find('.location_wise_location_select').val();
			var $userSelect = $row.find('.location_wise_users_select');
			var selectedIds = parseSelectedUsersAttr($userSelect);
			if(selectedIds.length == 0){
				var currentVal = $userSelect.val();
				if(currentVal != null && currentVal.length > 0){
					selectedIds = currentVal;
				}
			}
			if(locationId != ''){
				refreshLocationWiseUsers($row, locationId, selectedIds);
			}
		});
	}
	function addLocationWiseRow(level){
		var $container = $("#location_wise_rows_"+level);
		if($container.length == 0){
			return;
		}
		var nextRow = parseInt($container.attr('data-next-row'), 10);
		if(isNaN(nextRow) || nextRow < 1){
			nextRow = $container.find('.location_wise_row').length + 1;
		}
		if(locationWiseRowCounter[level] != undefined && locationWiseRowCounter[level] >= nextRow){
			nextRow = locationWiseRowCounter[level] + 1;
		}
		locationWiseRowCounter[level] = nextRow;
		$container.attr('data-next-row', nextRow + 1);
		var locationOptionsHtml = getLocationOptionsHtml(level);
		var $row = $(buildLocationWiseRowHtml(level, nextRow, locationOptionsHtml));
		$container.append($row);
		initLocationWiseRow($row);
	}
	$(document).on("click", ".add_location_wise_row", function(){
		addLocationWiseRow($(this).data("level"));
	});
	$(document).on("click", ".remove_location_wise_row", function(){
		var $row = $(this).closest('.location_wise_row');
		var $container = $row.closest('.location_wise_rows');
		if($container.find('.location_wise_row').length > 1){
			$row.find('select').each(function(){
				destroySelect2IfNeeded($(this));
			});
			$row.remove();
		}
	});
	// function updateaApproveStatus(id=""){
	// 	var process_type  = $(".process_type").val();
	// 	var approval_type  = $(".approval_type").val();
	// 	var sub_approve_type  = $(".sub_approve_type").val();
	// 	jQuery.ajax({
	// 		url: "views/custom_insert_workflow_configurations_status_ajax.php",
	// 		data:'ajax=Y&process_type='+process_type+'&approval_type='+approval_type+'&sub_approve_type='+sub_approve_type+'&rid='+id,
	// 		type: "POST",
	// 		success:function(data){
	// 			$("#approvedstatusselect").html(data);
	// 		// $(".js-example-basic-multiple").select2();
	// 		// $(".js-example-basic-single").select2();
	// 		// $('#dynamic_popup11').html(data);
	// 		// $('#kt_modal_dynamic_popup2').modal("show");
	// 	},
	// 	error:function (){}
	// });	
	// }
	var criterianum = 1;
	function addMoreCriteria(){
		$('#setdoa').remove();
		var htmldiv = '';
		criterianum++;
		var ary = <?php echo json_encode($customselectfieldarray); ?>;
		$('.selectcriteria').each(function(){
			var thisvalue = $(this).val();
			if(thisvalue in ary){
				delete ary[thisvalue];
			}	
		});
		var cntarray = Object.getOwnPropertyNames(ary);
		if(cntarray !=0){
			htmldiv +='<div class="form-group col-lg-4" id="deletdiv_'+criterianum+'"><label class="form-label">Criteria - '+criterianum+'</label><span class="float-right"><i class="ti-close text-danger f-12" onclick="deletecriteria(\'deletdiv_'+criterianum+ '\')"></i></span><select class="form-select js-example-basic-single selectcriteria" name="criteria[]" >';
			$.each(ary, function(key, value){
				htmldiv +='<option value="'+key+'" >'+value+'</option>';	
			});
			htmldiv +='</select></div>';
		}
		$("#addmorecriterihtml").append(htmldiv);
		$(".js-example-basic-single").select2();		
	}
	function deletecriteria(id){
		$('#setdoa').remove();
		$("#"+id).remove();
	}
	function selectWorkflowLevel(refrash_doa){
		// $.ajax({
		// 	url:"views/custom_insert_workflow_configurations_new_ajax.php",
		// 	data:'workflowlevelcnt='+workflowlevelcnt,
		// 	type:'POST',
		// 	success:function(data){
		// 		console.log(data);
		// 		//$("#workflow_level").html(data);
		// 	},
		// });
		var workflowlevelcnt = $("#select_workflow_level").val();
		var work_level_type = $("input[name='work_level_type']:checked").val();
		if(refrash_doa =='remove'){
			$('#setdoa').remove();
		}
		jQuery.ajax({
			url: "views/custom_insert_workflow_configurations_new_ajax.php",
			data:'workflowlevelcnt='+workflowlevelcnt+'&ajax=Y&Rid=<?php echo $rid; ?>&work_level_type='+work_level_type,
			type: "POST",
			success:function(data){
				$("#workflow_level").html(data);
				$(".js-example-basic-single").select2();
				$(".js-example-basic-multiple").select2();
				initAllLocationWiseBlocks($("#workflow_level"));
				if($(".custome_js-switch").length  > 0){
					var elem = Array.prototype.slice.call(document.querySelectorAll('.custome_js-switch'));
					elem.forEach(function(html) {
						var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'medium' });
					});
				}
			},
			error:function (){}
		});	
	}
	function approvalProcess(val,workflowlevel){
		if(val == 'M'){	
			$(".minimum"+workflowlevel).show();
			$("#minimums"+workflowlevel).val('');
		}else if(val == 'E'){
			$(".minimum"+workflowlevel).hide();
			$("#minimums"+workflowlevel).val('');
		}else{
			$(".minimum"+workflowlevel).hide();
			$("#minimums"+workflowlevel).val('1');
		}
	}
	function finalApprovalstage(vertocle_no,horizontal_no,last_no){
		for(var i=1; i <= last_no; i++){
			$("#pendingcriteria_"+vertocle_no+i).prop( "disabled", false );
		}
		if ($("#pendingcriteriachek_"+vertocle_no+horizontal_no).is(":checked")){
			horizontal_no++;
			for(var i=horizontal_no; i <= last_no; i++){
				console.log(vertocle_no+i);
				//$("#pendingcriteria_"+vertocle_no+i).hide();
				
				$("#pendingcriteria_"+vertocle_no+i).prop( "disabled", true );
				// $("#pendingcriteria_"+vertocle_no+i).css({
				// 	"pointer-events": "none", 
				// 	"opacity"     : "0.6",
				// 	"background-color":"#a7fff6"
				// });
			}
			
		}
	}
	function selectProcessName(transactiontablename){
		if(transactiontablename == 'document_management'){
			$(".document_type_block").show();
		}else{
			$(".document_type_block").hide();
			$("#document_type_id").val('').trigger('change');
		}
		jQuery.ajax({
			url: "ajax.php",
			data:'page=displayattributesfieldoptionlist&transactiontablename='+transactiontablename,
			type: "POST",
			success:function(data){
				console.log(data);
				$(".fieldlist").html(data);
			},
			error:function (){}
		});	
	}
	function getfield(id){
		$("#process_fields").hide();
		$("#session_fields").hide();
		$("#employee_fields").hide();
		if(id == 'T'){
			$("#process_fields").show('slow');
		}else if(id == 'S'){
			$("#session_fields").show('slow');
		}else if(id == 'E'){
			$("#employee_fields").show('slow');
		}
	}
	function copyfiled(element) {
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val($(element).text()).select();
		document.execCommand("copy");
		$temp.remove();
	}
	function checkDOA(checkedtype){
		console.log(checkedtype);
		if (checkedtype){
			$(".doatriger").show('slow');
			console.log("checkd");
		}else{
			$(".doatriger").hide('slow');
			console.log("not checkd");
		}
	}
	function followUp(){
		if($("#follow_up").is(':checked')){
			$(".followup").show('slow');
		}else{
			$(".followup").hide('slow');
		}
	}
	function Escalation(){
		if($("#escalation").is(':checked')){
			$(".escalationclass").show('slow');
		}else{
			$(".escalationclass").hide('slow');
		}
	}
	function autoForward(){
		if($("#auto_forward").is(':checked')){
			$(".autoforward_class").show('slow');
		}else{
			$(".autoforward_class").hide('slow');
		}
	}
	$(document).on('click', 'a[href^="#approvaltab_"]', function(){
		var href = $(this).attr('href');
		setTimeout(function(){
			var $pane = $(href);
			if($pane.length){
				initAllLocationWiseBlocks($pane);
			}
		}, 50);
	});
	$(document).ready(function(){
		initAllLocationWiseBlocks($("#workflow_level"));
		<?php if (isset($_GET['table_name']) && $_GET['table_name'] != '' && ($workflowconfiguration['process_name'] == '' || $workflowconfiguration['process_name'] == '0' || $workflowconfiguration['process_name'] == null)) { ?>
		selectProcessName(<?php echo json_encode($_GET['table_name']); ?>);
		<?php } ?>
	});
</script>
<style type="text/css">
	.border-none tr td, .border-none tr{border: transparent;}
	.border-none tr td i{margin: 0 0 0 27%;}
</style>
<form class=""  method="post" action="" enctype="multipart/form-data">	
	<input type="hidden" name="Rid" value="<?php echo $rid; ?>">
	<div class="card">
		<?php if($error_message !=''){ ?>
			<div class="alert alert-danger background-danger alert-dismissible">
				<strong><?php echo $error_message; ?></strong>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>	
		<?php } ?>
		<div class="card-header">
			<h5>Workflow configuration</h5>
			<div class="card-header-right">
				<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
					<i class="icofont icofont-reply"></i>
					Back
				</a>
			</div>
		</div>
		<div class="card-body">
			<div class="row">	
				<div class="col-lg-4">	
					<div class="card">
						<div class="card-header">
							<h5>Process Details</h5>
						</div>
						<div class="card-body">
							<div class="row"> 
						<!-- <div class="form-group col-lg-3 w-full">
							<label class="form-label">Module</label>
							<select class="form-select js-example-basic-single" id="module_setup_id" name="module_setup_id">
								<?php foreach($mopdulesetup as $modulesetup){ ?>
									<option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $workflowlist['module_setup_id'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
								<?php } ?>
							</select>
						</div> -->
						<div class="form-group col-lg-12">
							<label class="form-label">Process Name</label>
							<select class="form-select js-example-basic-single process_name" name="process_name" id="process_name" onchange="selectProcessName(this.value)">
								<option value="0">Select Any One</option>
								<?php foreach($pagenamesrow as $process){ ?>
									<option value="<?php echo $process['application_table_name']; ?>" <?php echo $prefillProcess==$process['application_table_name']?"selected":"" ?> ><?php echo $process['page_title']==""?ucwords(str_replace("_"," ",($process['application_table_name']))):$process['page_title']; ?></option>
								<?php } ?>
							</select>    
						</div>
						<div class="form-group col-lg-12 document_type_block w-full" style="<?php echo ($prefillProcess == 'document_management') ? '' : 'display:none;'; ?>">
							<label class="form-label">Document Type</label>
							<select class="form-select js-example-basic-single" name="document_type_id" id="document_type_id">
								<!-- <option value="">Select Document Type</option> -->
								<?php if(count((array)$documentTypes) > 0){
									foreach($documentTypes as $docType){
										$docType = is_array($docType) ? $docType : (array)$docType;
										$docTypeId = $docType['id'] ?? '';
										$docTypeName = $docType['type_name'] ?? '';
								?>
									<option value="<?php echo htmlspecialchars((string)$docTypeId, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($workflowconfiguration['document_type_id'] != '' && $workflowconfiguration['document_type_id'] == $docTypeId) ? 'selected' : ''; ?>><?php echo htmlspecialchars((string)$docTypeName, ENT_QUOTES, 'UTF-8'); ?></option>
								<?php }
								} ?>
							</select>
						</div>
						<div class="form-group col-lg-12">
							<label class="form-label">Description</label>
							<textarea class="form-control " name="process_description"><?php echo $workflowconfiguration['process_description']; ?></textarea>   
						</div>
						<div class="form-group col-lg-12">
							<label class="form-label">Workflow Trigger Status</label>
							<select class="form-select js-example-basic-single" name="workflow_trigger_status" >
								<?php foreach($stausresult as $statukey=>$statusvalue){ ?>
									<option value="<?php echo $statukey; ?>" <?php echo $workflowconfiguration['workflow_trigger_status']==$statukey?"selected":"" ?> ><?php echo $statusvalue['name']; ?></option>
								<?php } ?>
							</select>    
						</div>
						<div class="col-lg-12">
							<div class=" form-radio">
								<div class="radio radio-inline">
									<label class="form-label">
										<input type="radio" name="work_level_type" value="user_hierarchical" onclick="selectWorkflowLevel()" <?php echo $workflowconfiguration['work_level_type'] =="user_hierarchical"?"checked":"";?>>
										<i class="helper"></i> Hierarchical 
									</label>
									<label class="form-label">
										<input type="radio" name="work_level_type" value="role_levels" onchange="selectWorkflowLevel()" <?php echo $workflowconfiguration['work_level_type'] =="role_levels"?"checked":"";?>>
										<i class="helper"></i> Role Level
									</label>
									<label class="form-label">
										<input type="radio" name="work_level_type" value="custom_process"  onchange="selectWorkflowLevel()" <?php echo ($workflowconfiguration['work_level_type'] =="custom_process" || $workflowconfiguration['work_level_type'] =="")?"checked":"";?>>
										<i class="helper"></i> Custom WorkFlow
									</label>
								</div> 
							</div>
						</div>
						<div class="form-group col-lg-12">
							<label class="form-label">Workflow Level</label>
							<select class="form-select js-example-basic-single" name="workflow_level" id="select_workflow_level" onchange="selectWorkflowLevel('remove')">
								<?php for($m=0; $m <=30; $m++){ ?>
									<option value="<?php echo $m;?>" <?php echo $workflowconfiguration['workflow_level']==$m?"selected":"" ?>> <?php echo $m."- Level"?></option>
								<?php } ?>

							</select>    
						</div>
						<div class="form-group col-lg-12" >
							<span class="form-label d-block">Transaction Owner</span>
							<select class="form-select js-example-basic-single fieldlist" name="transaction_owner_field">
								<option value="0" data-select2-id="2"> Select Any One</option>
								<?php foreach($listfields as $feldname=>$listfield){ ?>
									<option value="<?php echo $feldname; ?>" <?php echo $workflowconfiguration['transaction_owner_field']==$feldname?"selected":"" ?> > <?php echo $listfield['lable']; ?>  </option>
								<?php } ?>
							</select>    
						</div>
						<?php /**/?>
						<div class="form-group col-lg-12" >
							<span class="form-label d-block">Transaction Owner Type</span>
							<select class="form-select js-example-basic-single" name="transaction_owner_type">
								<?php foreach($ownertype as $keyval=>$values){ ?>
									<option value="<?php echo $keyval; ?>" <?php echo $workflowconfiguration['transaction_owner_type']==$keyval?"selected":"" ?> > <?php echo $values; ?>  </option>
								<?php } ?>
							</select>    
						</div>
						<?php /**/ ?>
						<!-- <div class="form-group col-lg-12">
							<label class="form-label">Email Template</label>
							<select class="form-select js-example-basic-single" name="email_content_ref_id">
								<option value="0" > Select Any One</option>
								<?php foreach($email_content as $emailcontent){ ?>
									<option value="<?php echo $emailcontent['template_reference_id']; ?>" <?php echo $workflowconfiguration['email_content_ref_id']==$emailcontent['template_reference_id']?"selected":"" ?> ><?php echo $emailcontent['template_name']; ?>  </option>
								<?php } ?>
							</select>    
						</div> -->
						<div class="form-group col-lg-12">
							<span class="form-label d-block">Follow Up</span>
							<input type="checkbox" class="js-switch" value="Y" name="follow_up" id="follow_up" <?php echo $workflowconfiguration['follow_up'] =="Y"?"checked":"";?> onchange="followUp()">
						</div>
						<div class="form-group col-lg-6 followup">
							<label class="form-label">Days</label>
							<input type="number" class="form-control" name="follow_up_day" placeholder="Day" value="<?php echo $workflowconfiguration['follow_up_day']; ?>">
	                    </div>
						<div class="form-group col-lg-6 followup">
							<label class="form-label">Follow Up Email</label>
							<select class="form-select js-example-basic-single" name="follow_up_email_content_ref_id">
								<option value="0" > Select Any One</option>
								<?php foreach($email_content as $emailcontent){ ?>
									<option value="<?php echo $emailcontent['template_reference_id']; ?>" <?php echo $workflowconfiguration['follow_up_email_content_ref_id']==$emailcontent['template_reference_id']?"selected":"" ?> ><?php echo $emailcontent['template_name']; ?>  </option>
								<?php } ?>
							</select>    
						</div>
						<div class="form-group col-lg-12">
							<span class="form-label d-block">Escalation</span>
							<input type="checkbox" class="js-switch" value="Y" name="escalation" id="escalation" <?php echo $workflowconfiguration['escalation'] =="Y"?"checked":"";?> onchange="Escalation()">
						</div>
						<div class="form-group col-lg-6 escalationclass">
							<label class="form-label">Days</label>
							<input type="number" class="form-control" name="escalation_day" placeholder="Day" value="<?php echo $workflowconfiguration['escalation_day']; ?>">
	                    </div>
						<div class="form-group col-lg-6 escalationclass">
							<label class="form-label">Escalation Email</label>
							<select class="form-select js-example-basic-single" name="escalation_email_content_ref_id">
								<option value="0" > Select Any One</option>
								<?php foreach($email_content as $emailcontent){ ?>
									<option value="<?php echo $emailcontent['template_reference_id']; ?>" <?php echo $workflowconfiguration['escalation_email_content_ref_id']==$emailcontent['template_reference_id']?"selected":"" ?> ><?php echo $emailcontent['template_name']; ?>  </option>
								<?php } ?>
							</select>    
						</div>
						<div class="form-group col-lg-4">
							<span class="form-label d-block">Auto Forward</span>
							<input type="checkbox" class="js-switch" value="Y" name="auto_forward" id="auto_forward" <?php echo $workflowconfiguration['auto_forward'] =="Y"?"checked":"";?> onchange="autoForward()">
						</div>
						<div class="form-group col-lg-8 autoforward_class">
							<label class="form-label">Days</label>
							<input type="number" class="form-control" name="auto_forward_day" placeholder="Day" value="<?php echo $workflowconfiguration['auto_forward_day']; ?>">
	                    </div>
					</div>
				</div>	
			</div>	
		</div>	
		<div class="col-lg-8">
			<div class="card">	
				<div class="card-header">
					<h5>Criteria Details</h5>
				</div>
				<div class="card-body">
					<div class="row" id="addmorecriterihtml"> 
						<?php 
						if(count((array)$criteria) ==0 && $_GET['Rid'] ==""){
							$criteria = array('-1');
						} $j=1; foreach($criteria as $criterias){ ?>
							<div class="form-group col-lg-4" id="deletdiv_<?php echo $j; ?>" >
								<label class="form-label">Criteria-<?php echo $j; ?> </label>
								<span class="float-right"><i class="ti-close text-danger f-12" onclick="deletecriteria('deletdiv_<?php echo $j; ?>')"></i></span>
								<div <?php echo $criterias !="-1"?'style="pointer-events: none; opacity: 0.6;"':""; ?>>
									<select class="form-select js-example-basic-single selectcriteria" name="criteria[]">
										<?php foreach($customselectfieldarray as $trans_table=>$customselectfield){ ?>
											<option value="<?php echo $trans_table; ?>" <?php echo $criterias ==$trans_table?"selected":"" ?>><?php echo $customselectfield; ?></option>
										<?php  } ?>
									</select> 
								</div>
							</div>
							<?php $j++; } ?>
						</div>
						<button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold" onclick="addMoreCriteria()"><i class="ti-plus"></i> Add More Criteria</button>

					</div>
				</div>
				<!-- <div class="card">	
					<div class="card-header">
						<h5>WorkFlow Levels</h5>
					</div>
					<div class="card-body" id="workflow_level"> -->
						<div id="workflow_level">
							<?php 
							$_POST['workflowlevelcnt'] =$workflowconfiguration['workflow_level'];
							$_POST['work_level_type'] =$workflowconfiguration['work_level_type'];
							include_once 'views/custom_insert_workflow_configurations_new_ajax.php'; ?>
						</div>
					<!-- </div>
					</div> -->
				</div>
			</div>
		</div>
	</div>

	<?php 
	$roleprecesstypearray['role_levels'] = "Role Leves";
	$roleprecesstypearray['user_hierarchical'] = "User Hierarchical";
	if($_GET['Rid'] !=""){
	?>
	<div class="card" id="setdoa">
		<div class="card-header">
			<h5>DOA Approval Levels</h5>
		</div>
		<div class="card-body" >
			<div class="row">
				<div class="form-group col-lg-1 text-center">
					<span class="form-label d-block">DOA</span>
					<input type="checkbox" class="js-switch" name="doa"  <?php echo $workflowconfiguration['doa']=='0' || $workflowconfiguration['doa'] ==''?"checked":"" ?> onchange="checkDOA(this.checked)" value="0"/> 
				</div>
				<div class="form-group col-lg-4 doatriger w-full" style="<?php echo $workflowconfiguration['doa']=='0' || $workflowconfiguration['doa'] ==''?"":"display:none" ?>">
					<span class="form-label d-block">DOA Trigger Field</span>
					<select class="form-select js-example-basic-single fieldlist" name="doa_trigger_field">
						<option value="0" > Select Any One</option>
						<?php foreach($listfields as $feldname=>$listfield){ ?>
							<option value="<?php echo $feldname; ?>" <?php echo $workflowconfiguration['doa_trigger_field']==$feldname?"selected":"" ?> > <?php echo $listfield['lable']; ?>  </option>
						<?php } ?>
					</select>    
				</div>
			</div>
			<div class="row">
				<div class="col-lg-3">
					<div class="table-responsive">
						<table class="table table-bordered table-sm border-none" style="border: 1px solid #dee2e6;">
							<tr align="center" style="border: 1px solid #dee2e6;"><th>Approval Levels</th></tr>
							<tr><td>&nbsp</td></tr>
							<tr><td>&nbsp</td></tr>
							<?php for($i=1; $i <= $workflowconfiguration['workflow_level']; $i++){  
								$approvalprocess = $approval_process[$i];
								$sublist = subProcessType($approvalprocess['approval_type'],$getresult);
								?>
								<tr align="center" style="height: 75px;">
									<td>
										<?php //print_r($sublist); ?>
										<label class="form-label label label-lg label-primary" style="width: -webkit-fill-available;"><span class="f-18"><?php echo $roleprecesstypearray[$approvalprocess['approval_type']]; ?></span>
											<span class="d-block tetx-dark mt-1"><?php echo $sublist[$approvalprocess['sub_approve_type']]; ?></span>
											</label>L<?php echo $i; ?>
										<?php if($i <$workflowconfiguration['workflow_level']){?>
										<i class="ti-arrow-down d-block m-0"></i>
									<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</table>
					</div>
				</div>
				<div class="col-lg-9">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-sm">
							<tr align="center">
								<th colspan="8"><?php echo ucwords(str_replace("_"," ",($workflowconfiguration['process_name']))); ?></th>
							</tr>
							<tr align="center">
							<?php $incnumber = 1; 
								foreach($dynamicdoacriteria as $unifieldkey =>$dynamicdoacriterias){ 
								$wfdetail = $wfdetails[$unifieldkey]; ?>
							<th>
								<input type="hidden" name="wfd_id[<?php echo $incnumber; ?>]" value="<?php echo $wfdetail['id']; ?>">
								<label class="form-label label label-inverse-primary">
									<?php $mk=1; foreach($dynamicdoacriterias as $fieldkey => $dynamicselection){
							//0 = table name , 1 = lable , 2 : value 
										$tablevalue = explode("=",$dynamicselection);
										$uniquetablevalue[$tablevalue[0]] =$tablevalue[2];
										$uniquetablevaluefield[$incnumber] .= $tablevalue[0]."_".$tablevalue[2];
										?>
										<span class="d-block mt-2  mb-2"><?php echo $mk." : ".$tablevalue[1]; ?></span>
										<?php $mk++; } ?>
									</label>
									<input type="hidden" name="approval_level_criteria[<?php echo $incnumber; ?>]" value='<?php echo json_encode($uniquetablevalue);?>'>	
								</th>
								<?php $incnumber++; } ?>
							</tr>
							<?php for($i=1; $i <= $workflowconfiguration['workflow_level']; $i++){  
								?>
							<tr>	
							<?php  $incnumber = 1;  foreach($dynamicdoacriteria as $unifieldkey => $dynamicdoacriterias){
								$wfdetail = $wfdetails[$unifieldkey];
								$doa_amount = json_decode($wfdetail['doa_amount'],1);
								?>
								<th align="center" >
									<div class="input-group input-group-button"> 
										<input class="form-control autonumber width1"  name="doa_amount[<?php echo $incnumber; ?>][<?php echo $i; ?>]"  type="text" value="<?php echo $doa_amount[$i]['end_doa']; ?>" placeholder="Eg. 0" data-a-sign="<?php echo $_SESSION['config']['currency_symbol']; ?>" id="pendingcriteria_<?php echo $incnumber.$i; ?>" <?php echo ($wfdetail['final_approval'] < $i && $wfdetail['final_approval'] !="")?"disabled":""; ?>>
										<span class="input-group-text form-radio p-0">
											<div class="radio radio-inline m-0">
												<label class="form-label">
													<input type="radio" name="final_approval_<?php echo $incnumber; ?>" id="pendingcriteriachek_<?php echo $incnumber.$i; ?>" onchange="finalApprovalstage(<?php echo $incnumber; ?>,<?php echo $i; ?>,<?php echo $workflowconfiguration['workflow_level']; ?>)" value="<?php echo $i; ?>"  <?php echo ($wfdetail['final_approval'] == $i || $wfdetail['final_approval'] =="")?"checked":""; ?>>
													<i class="helper"></i>
												</label>
											</div>
										</span>  
									</div>
								</th>
								<?php  $incnumber++;  } ?>
							</tr>
							<?php } ?>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-header">
			<h5>Notification Message</h5>
		</div>
		<div class="card-body">
			<div class="row mb-4">
				<div class="col-lg-4 text-center"><label class="form-label label label-lg label-primary" onclick="getfield('T')">Process Fields</label></div>
				<div class="col-lg-4 text-center"><label class="form-label label label-lg label-primary" onclick="getfield('S')">Session Fields</label></div>
				<div class="col-lg-4 text-center"><label class="form-label label label-lg label-primary" onclick="getfield('E')">Creater Fields</label></div>
			</div>  
			<div class="row mb-4 card">
				<div class="col-lg-8"  id="process_fields" style="display:none">
					<?php $listfields['process_name'] = array('lable'=>'Process Name -'.$workflowconfiguration['process_name']); 
					$listfields['transaction_owner_name'] = array('lable'=>'Transaction Owner Name');
					$listfields['transaction_status'] = array('lable'=>'Transaction Status'); 

					$nmt = 1; foreach($listfields as $keyy=>$aray){ 
						$valuefields = "{[".$workflowconfiguration['process_name'].".".$keyy."]}";
						?>
						<p id="fieldspro_<?php echo $nmt;?>" class="d-none"><?php echo $valuefields; ?></p>
						<label class="btn btn-info btn-mini waves-effect waves-light m-1 f-w-600" onclick="copyfiled('#fieldspro_<?php echo $nmt;?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Click Copy To used"><?php echo ucwords($aray['lable']); ?> <i class="ti-layers"></i></label>
						<?php $nmt++; }	?>
					</div>
					<div class="col-lg-12 "  id="employee_fields" style="display:none">
						<ul class="nav nav-tabs md-tabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-bs-toggle="tab" href="#homeuser" role="tab">User</a>
								<div class="slide"></div>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#profileemployee" role="tab">Employee</a>
								<div class="slide"></div>
							</li>
						</ul>
						<div class="tab-content card-block">
							<div class="tab-pane active" id="homeuser" role="tabpanel">
								<?php $nmt = 1; foreach($_SESSION['user'] as $keyy=>$aray){ 
									$valuefields = "{[application_users.".$keyy."]}";
									?>
									<p id="fieldsappuser_<?php echo $nmt;?>" class="d-none"><?php echo $valuefields; ?></p>
									<label class="btn btn-info btn-mini waves-effect waves-light m-1 f-w-600" onclick="copyfiled('#fieldsappuser_<?php echo $nmt;?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Click Copy To used"><?php echo ucwords($keyy); ?> <i class="ti-layers"></i></label>
									<?php $nmt++; }	?>
								</div>
								<div class="tab-pane" id="profileemployee" role="tabpanel">
									<?php $nmt = 1; foreach($_SESSION['employee'] as $keyy=>$aray){ 
										$valuefields = "{[employees.".$keyy."]}";
										?>
										<p id="fieldsemp_<?php echo $nmt;?>" class="d-none"><?php echo $valuefields; ?></p>
										<label class="btn btn-info btn-mini waves-effect waves-light m-1 f-w-600" onclick="copyfiled('#fieldsemp_<?php echo $nmt;?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Click Copy To used"><?php echo ucwords($keyy); ?> <i class="ti-layers"></i></label>
										<?php $nmt++; }	?>
									</div>
								</div>	
							</div>
							<div class="col-lg-12 " id="session_fields" style="display:none">
								<div class="col-lg-12 col-xl-12 mt-4">
									<ul class="nav nav-tabs md-tabs" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" data-bs-toggle="tab" href="#homeuser" role="tab">User</a>
											<div class="slide"></div>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#profileemployee" role="tab">Employee</a>
											<div class="slide"></div>
										</li>
									</ul>
									<div class="tab-content card-block">
										<div class="tab-pane active" id="homeuser" role="tabpanel">
											<?php $nmt = 1; foreach($_SESSION['user'] as $keyy=>$aray){ 
												$valuefields = "{[session_user.".$keyy."]}";
												?>
												<p id="fieldssesiuser_<?php echo $nmt;?>" class="d-none"><?php echo $valuefields; ?></p>
												<label class="btn btn-info btn-mini waves-effect waves-light m-1 f-w-600" onclick="copyfiled('#fieldssesiuser_<?php echo $nmt;?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Click Copy To used"><?php echo ucwords($keyy); ?> <i class="ti-layers"></i></label>
												<?php $nmt++; }	?>
											</div>
											<div class="tab-pane" id="profileemployee" role="tabpanel">
												<?php $nmt = 1; foreach($_SESSION['employee'] as $keyy=>$aray){ 
													$valuefields = "{[session_employee.".$keyy."]}";
													?>
													<p id="fieldssesiemp_<?php echo $nmt;?>" class="d-none"><?php echo $valuefields; ?></p>
													<label class="btn btn-info btn-mini waves-effect waves-light m-1 f-w-600" onclick="copyfiled('#fieldssesiemp_<?php echo $nmt;?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Click Copy To used"><?php echo ucwords($keyy); ?> <i class="ti-layers"></i></label>
													<?php $nmt++; }	?>
												</div>
											</div>
										</div>

									</div>
								</div>
								<div class="row form-group">
									<div class="col-lg-6">
										<div class="card">
											<div class="card-body">
												<div class="form-group">
													<label>Submit/Approve</label>
													<textarea class="form-control " name="approve_message"><?php echo $transaction_message_res['approve_message']; ?></textarea>   
												</div>
												<div class="form-group">
													<label>Submit/Approve Informations</label>
													<textarea class="form-control " name="approve_message_information"><?php echo $transaction_message_res['approve_message_information']; ?></textarea>   
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="card">
											<div class="card-body">
												<div class="form-group"> 
													<label>Reject</label> 
													<textarea class="form-control " name="reject_message"><?php echo $transaction_message_res['reject_message']; ?></textarea>   
												</div>
												<div class="form-group">
													<label>Send Back</label>
													<textarea class="form-control " name="sendback_message"><?php echo $transaction_message_res['sendback_message']; ?></textarea>   
												</div>
												<div class="form-group">
													<label>Completed Workflow</label>
													<textarea class="form-control " name="completed_workflow_message"><?php echo $transaction_message_res['completed_workflow_message']; ?></textarea>   
												</div>
												<div class="form-group">
													<label>Call Back</label>
													<textarea class="form-control " name="callback"><?php echo $transaction_message_res['callback']; ?></textarea>   
												</div>
												<div class="form-group ">
													<label>Information</label>
													<textarea class="form-control " name="employee_message"><?php echo $transaction_message_res['employee_message']; ?></textarea>   
												</div>
											</div>
										</div>
									</div>	
								</div>
							</div>
						</div>
					<?php } ?>	
						<div class="card">	
							<div class="card-footer">
								<button class="btn btn-primary mr-2" name="submit" value="submit"><i class="la la-check"></i>Save</button>
							</div>
						</div>	
					</form>