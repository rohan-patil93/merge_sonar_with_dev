<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$dataurl = API."?mode=togetesignpolistpopup&type=".$_POST['type']."&rid=".$_POST['rid']."&item=".$_POST['item']."&emp_id=".$_POST['emp_id'];
$view = callAPI($dataurl);
$data = json_decode($view,1);
//echo "<pre>"; print_r($_POST);print_r($data);
$job  = $data['job'];
?>
<style type="text/css">
.vcs{
	font-size: 18px;
	padding: 8.5px;
}
</style>
<script type="text/javascript">
	function submitrom(){
		var vrc_id = $("#vrc_id").val();
  		var vrc_item_id = $("#vrc_item_id").val();
  		var mpc_id = $("#mpc_id").val();
 		var mpc_item_id = $("#mpc_item_id").val();
		if(vrc_id == "" || vrc_item_id == "" || mpc_id == "" || mpc_item_id == ""){
			swal.fire({                   
				text: "Please fill in required fields",              
				type: "danger",           
				buttonsStyling: false,
				confirmButtonText: "<i class='la la-hand-o-down'></i>Yes, close!",    
				confirmButtonClass: "btn btn-danger"				        
			});

		}else{
		$.ajax({
	        url: "ajax.php",
	        type: "POST",
	        data: 'page=insertupdatepovrcpopup&'+$("#insertstatsuform").serialize(),
	        success: function(response) {
	        	console.log(response);
				$('#kt_modal_KTDatatable_remote').modal('hide');
		 	//var msg = <?php echo "'".$_SESSION['error_message']['9025']."'" ?>;
				//window.location.reload();
			swal.fire({              
	title: "Record has been saved successfully ",              
	text: "Do you want to reload? ",              
	type: "success",           
	buttonsStyling: false,
	confirmButtonText: "Yes, Reload! ",    
	confirmButtonClass: "btn btn-success",
	showCancelButton: true,     
	cancelButtonText: " No, continue",      
	cancelButtonClass: "btn btn-warning"        
}).then(function(result) {
	if (result.value) {
		//$('#kt_modal_KTDatatable_remote').modal('hide');
		window.location.reload();
	}
});
	        }
	    });
		}
	}
</script>
<div class="modal-header">
	<h5 class="modal-title"> Assign Employee</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closebutnfunction()"></button>
	</div>
	<div class="modal-body" > 
		<form class="kt-form kt-form--label-right" action="" method="post" id="insertstatsuform">
			<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
				<div class="form-group row">
					<div class="row col-lg-12">
						<div class="col-lg-6 form-group">
							<label>Vendor Rate Card</label>
							<input type="text" name="vrc_name" id="vrc_name" readonly value="<?php echo $data['vrcmain']['transaction_number']; ?>" class="form-control vcs">
							<input type="hidden" name="vrc_id" id="vrc_id"  value="<?php echo $data['vrcmain']['id']; ?>" class="form-control vcs">
						</div>
						
						 <div class="col-lg-6 form-group">
							<label>Rate Card job Title</label>
							<?php if($_POST['type'] == 'vrc'){ ?>
								<input type="text" name="vrc_item_name" id="vrc_item_name" readonly value="<?php echo $job[$data['sub_vrc']['item_id']]." ( ".$data['sub_vrc']['min_exp']." - ".$data['sub_vrc']['max_exp']." ) - ".customeummarmonyformate($data['sub_vrc']['item_price']); ?>" class="form-control vcs">
							<input type="hidden" name="vrc_item_id" id="vrc_item_id" readonly value="<?php echo $data['sub_vrc']['id']; ?>" class="form-control ">
							<?php } ?>
							<?php if($_POST['type'] == 'mpc'){ ?>
								<select class="form-select js-example-basic-single vcs" name="vrc_item_id" id="vrc_item_id">
								<?php foreach($data['sub_vrc'] as $mpc_item ){ ?>
									<option value="<?php echo $mpc_item['id']; ?>"> <?php echo $job[$mpc_item['item_id']]." ( ".$mpc_item['min_exp']." - ".$mpc_item['max_exp']." ) - ".customeummarmonyformate($mpc_item['item_price']); ?></option>
								<?php } ?>
							</select>
							<?php } ?>		
						</div>
						<div class="col-lg-6 form-group">
							<label>Purchase order</label>
							<input type="text" name="mpc_name" id="mpc_name" readonly value="<?php echo $data['mpcmain']['transaction_number']; ?>" class="form-control vcs">
							<input type="hidden" name="mpc_id" id="mpc_id"  value="<?php echo $data['mpcmain']['id']; ?>" class="form-control">
						</div>
						 <div class="col-lg-6 form-group">
							<label>PO Job Title</label>
							<?php if($_POST['type'] == 'mpc'){ ?>
								<input type="text" name="mpc_item_name" id="mpc_item_name" readonly value="<?php echo $job[$data['sub_mpc']['item_id']]." ( ".$data['sub_mpc']['min_exp']." - ".$data['sub_mpc']['max_exp']." ) - ".customeummarmonyformate($data['sub_mpc']['item_price']); ?>" class="form-control vcs">
							<input type="hidden" name="mpc_item_id" id="mpc_item_id" readonly value="<?php echo $data['sub_mpc']['id']; ?>" class="form-control ">
							<?php } ?>
							 <?php if($_POST['type'] == 'vrc'){ ?>
							 		<select class="form-select js-example-basic-single vcs" name="mpc_item_id" id="mpc_item_id">
								<?php foreach($data['sub_mpc'] as $mpc_item ){ ?>
									<option value="<?php echo $mpc_item['id']; ?>"> <?php echo $job[$mpc_item['item_id']]." ( ".$mpc_item['min_exp']." - ".$mpc_item['max_exp']." ) - ".customeummarmonyformate($mpc_item['item_price']); ?></option>
								<?php } ?>
							</select>
							 <?php } ?>
						</div>
						<div class="col-lg-6 form-group">
							<label>Valid From</label>
							<input type="text" name="valid_from" id="valid_from"  value="<?php echo $data['mpcmain']['effective_from_date'];; ?>" class="form-control datepicker vcs">
							
						</div>	
						<div class="col-lg-6 form-group">
							<label>Valid To</label>
							<input type="text" name="valid_to" id="valid_to"  value="<?php echo $data['mpcmain']['effective_to_date']; ?>" class="form-control datepicker vcs">
						</div>
					</div>								
				</div>
			</div>
			<div class="modal-footer">
				 <input type="hidden" name="emp_id" value="<?php echo $_POST['emp_id']; ?>">
				<button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal" onclick="closebutnfunction()">Close</button>
				<button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Submit</button>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>

