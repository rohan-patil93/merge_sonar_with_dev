<?php //print_r($_POST); 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
//$url = API."?mode=getassetallocation&list=Y&assetid=".$_POST['assetid']; 
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=DIRECTASSETALLOCATE&assetid=".$_POST['assetid'];
$data_details = callAPI($url);
$details = json_decode($data_details,1);
//echo "<pre>"; print_r($details); echo "</pre>";
//$assetcategory = $details['asset_categories'];
$locations = $details['locations'];
$departments = $details['departments'];
$costcenters = $details['costcenter'];
//$users = $details['users'];
$list_assets = $details['list_assets'];
$employees = $details['employees'];
///$ass_status = array('1'=>'In Stock','2'=>'Out Stock','3'=>'Out For Repair','4'=>'Under Stock');
$ass_condition = array('1'=>'Used','2'=>'Good','3'=>'Poor','4'=>'Damaged','New');


foreach($details['asset_status'] as $asset_status){
	$ass_status[$asset_status['id']] = $asset_status['name'];
}
foreach($details['asset_categories'] as $asset_category_list){
	$asset_category_lists[$asset_category_list['id']] = $asset_category_list['category_description'];
}
foreach($details['locations'] as $locations_list){
	$locations_lists[$locations_list['id']] = $locations_list['location_name'];
}





?>
<script type="text/javascript">

function submitdetails(){
	var validations = ajaxFormSubmitValidation();
    if(validations == 0){
	  	var formData = new FormData();
	    formData.append("page", "insertassetreturn"); 
	    var dataArray = $("#assetsallocationform").serializeArray(),
	    dataObj = {};
	    $(dataArray).each(function(i, field){
	      formData.append(field.name, field.value); 
	    });
	  //  var popattachment = document.getElementById('popattachment').files[0];
	  // formData.append("file",popattachment);
	        jQuery.ajax({
	            url: "ajax.php",
	            data: formData,
	            type: "POST",
	            contentType: false,
	            processData: false,
	            success:function(data){
	            	var res = JSON.parse(data);
	                if(res.errer=="404"){
	                	alert("Oops admin Buddy Not Found");
	                }else{
	                	location.reload();
	                }
	            }
	        });
	    }
}

</script>

<div class="modal-header">
	<h4 class="modal-title">Asset Return</h4>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body j-pro">
	<div class="j-content">
	<form id="assetsallocationform" method="post" >	
		<input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
		<div class="row ">
			<div class="dt-responsive table-responsive">
	<table  class="table table-striped table-bordered nowrap"> 
		<thead>
			<tr>
				<th>Asset Code</th>
				<th>Asset Name</th>
				<th>Brand</th>
				<th>Model</th>
				<th>Serial Number</th>
				<th>Location </th>
				<th>Satus</th>
				<th>Condition</th> 
			</tr>
		</thead>
		<tbody>
			<?php foreach($list_assets as $list_asset){ ?>
					<tr class="<?php echo $varianc_class; ?>">
						<td>
							<?php echo $list_asset['asset_code']; ?>
							<input type="hidden" name="asset_id[]" value="<?php echo $list_asset['id']; ?>">
						</td>
						<td><?php echo $list_asset['asset_description']; ?></td>
						<td><?php echo $list_asset['brand']; ?></td>
						<td><?php echo $list_asset['model']; ?></td>
						<td><?php echo $list_asset['serial_no']; ?></td>
						<td>
							<?php echo $locations_lists[$list_asset['asset_location']]; ?></td>
						<td><?php echo $ass_status[$list_asset['asset_status']]; ?></td>
						<td>
							<select  class="form-select js-example-basic-single" name="conditions">
								<option value="">Select Any One</option>
								<?php foreach ($ass_condition as $ass_conditiokey=>$ass_conditio) { ?>					
									<option  value="<?php echo $ass_conditiokey; ?>" <?php echo $ass_conditiokey == $list_asset['conditions']?"selected":""; ?>><?php echo ucwords($ass_conditio); ?></option>
								<?php } ?>
							</select> 
						</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div> 
		</div>
	<div class="card p-2 mt-2">	
		<div class="row">	
			<div class="form-group col-lg-3">
				<label>Return date <span class="requried text-danger">*</span></label>
				<span class="d-block error-message text-danger"></span>
				<input type="text" class="form-control datepicker requried_field" id="return_date"  name="return_date" value="">

			</div>
			<div class="form-group col-lg-9">
				<label>Note <span class="requried text-danger">*</span></label>
				<span class="d-block error-message text-danger"></span>
				<textarea type="text" class="form-control requried_field" id="received_note" rows="4" cols="50" name="received_note" ></textarea>
			</div> 
		</div>
	</div>
	</form>
	</div>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
	<button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()">Return</button>
</div>

<!-- <script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script> -->
<!-- 
<script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script> -->