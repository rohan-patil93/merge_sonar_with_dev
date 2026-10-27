<?php //print_r($_POST); 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
//$url = API."?mode=getassetallocation&list=Y&assetid=".$_POST['assetid']; 
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=DISCARDASSET&assetid=".$_POST['assetid'];
$data_details = callAPI($url);
$details = json_decode($data_details,1);
//$assetcategory = $details['asset_categories'];
$locations = $details['locations'];
$departments = $details['departments'];
$costcenters = $details['costcenter'];
//$users = $details['users'];
$list_assets = $details['list_assets'];
$employees = $details['employees'];
// $ass_condition = array('1'=>'Used','2'=>'Good','3'=>'Poor','4'=>'Damaged','New');
// foreach($details['asset_categories'] as $asset_category_list){
// 	$asset_category_lists[$asset_category_list['id']] = $asset_category_list['category_description'];
// }
// foreach($details['locations'] as $locations_list){
// 	$locations_lists[$locations_list['id']] = $locations_list['location_name'];
// }
// foreach($details['asset_status'] as $asset_status){
// 	$ass_status[$asset_status['id']] = $asset_status['name'];
// }
?>
<script type="text/javascript">
	function selectAllocationType(){
		//$("#allocation_type).val();
			var selected = $("#allocation_type:checked").val();
			$(".select_caost_center").hide();
			$(".select_dept").hide();
			$(".select_emp").hide();
			if(selected=="CC"){
				$(".select_caost_center").show();
			}else if(selected=="D"){
				$(".select_dept").show();
			}else{
				$(".select_emp").show();
			}

	}
function submitdetails(){
	var validations = ajaxFormSubmitValidation();
    if(validations == 0){
	  	var formData = new FormData();
	    formData.append("page", "insertassetallocation"); 
	    formData.append("discardasset", "Y"); 
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
            	 $('#kt_modal_dynamic_popup2').modal("hide");
				tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"<?php echo $_SESSION['error_message'][3002]; ?>",'');
				location.reload();
            }
        });
	}        
}

</script>

<div class="modal-header">
	<h4 class="modal-title">Discard Or Sales</h4>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body j-pro">
	<div class="j-content">
	<form id="assetsallocationform" method="post" >	
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
				<!-- <th>Location </th> -->
				<!-- <th>Satus</th> -->
				<!-- <th>Condition</th> -->
				<!-- <th>Asset Value <span class="requried text-danger">*</span></th> --> 
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
						<!-- <td>
							<select  class="form-select js-example-basic-single " name="asset_location[]">
								<option value="">Select Any One</option>
								<?php foreach ($locations as $location) { ?>					
									<option location value="<?php echo $location['id']; ?>" <?php echo $location['id'] == $list_asset['asset_location']?"selected":""; ?>><?php echo ucwords($location['location_name']); ?></option>
								<?php } ?>
							</select>
						</td> -->
						<!-- <td><?php //echo $ass_status[$list_asset['status']]; ?>
							<select  class="form-select js-example-basic-single" name="asset_status[]">
								<option value="">Select Any One</option>
								<?php foreach ($ass_status as $ass_statuskey=>$ass_statuss){ ?>					
									<option  value="<?php echo $ass_statuskey; ?>" <?php echo $ass_statuskey == $list_asset['status']?"selected":""; ?>><?php echo ucwords($ass_statuss); ?></option>
								<?php }  ?>
							</select> 
						</td> -->
						<!-- <td>
							<select  class="form-select js-example-basic-single " name="conditions[]">
								<option value="">Select Any One</option>
								<?php foreach ($ass_condition as $ass_conditiokey=>$ass_conditio) { ?>					
									<option  value="<?php echo $ass_conditiokey; ?>" <?php echo $ass_conditiokey == $list_asset['conditions']?"selected":""; ?>><?php echo ucwords($ass_conditio); ?></option>
								<?php } ?>
							</select> 
						</td> -->
						<!-- <td>
							<input type="text" class="form-control text-right" name="asset_value[]" value="">
						</td> -->
				</tr>
			<?php } ?>
				
			</tbody>
		</table>
	</div> 
		</div>
	<div class="card p-2 mt-2">	
		<div class="row">
			
			<div class="form-group col-lg-4">
				<label>Discard/ Sales  date <span class="requried text-danger">*</span></label>
				<span class="d-block error-message text-danger"></span>
				<input type="text" class="form-control datepicker requried_field" id="discard_and_sales_date"  name="discard_and_sales_date" value="<?php echo fromDate(); ?>">
			</div>
			<div class="form-group col-lg-8">
				<label>Reason <span class="requried text-danger">*</span></label>
				<span class="d-block error-message text-danger"></span>
				<textarea type="text" class="form-control requried_field" id="remarks" rows="4" cols="50" name="discard_and_sales_remarks" ></textarea>
			</div> 
		</div>
	</div>

	</form>
	</div>
</div>

<div class="modal-footer">
	<p>Permanant Remove The Assets</p>
	<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
	<button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()">Submit</button>
</div>

<script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>
<script type="text/javascript">
	$(".js-example-basic-single").select2();
	$(".js-example-disabled-results").select2();
	$( ".datepicker" ).datepicker({
		showOn: "both", 
		buttonText: "<i class='ti-calendar'></i>",
		dateFormat: date_format,
		changeMonth: true,
		changeYear: true
	});
</script>
<!-- 
<script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script> -->