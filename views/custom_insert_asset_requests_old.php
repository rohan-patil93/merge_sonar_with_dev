<?php //print_r($_POST); 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
//print_r($_POST);
$File_Uploads_Path = "uploads/reimbursement/";
$url = API."?mode=getassetallocation&request=Y&req_id=".$_POST['req_id']."&categroy_list=".$_POST['categroy_list']."&asset_status=".$_POST['asset_status']."&asset_condition=".$_POST['asset_condition']; 
$data_details = callAPI($url);
$details = json_decode($data_details,1);
//print_r($details['request_list_asset']);
$request = $details['request'];
$attributes = $details['attributes'];
$locations = $details['locations'];
$request_list_asset = $details['request_list_asset'];
//print_r($request_list_asset);
//$departments = $details['departments'];
//$costcenters = $details['costcenter'];
//$users = $details['users'];
$assets_list = $details['assets_list'];
//$employees = $details['employees'];
//$ass_status = array('1'=>'In Stock','2'=>'Out Stock','3'=>'Out For Repair','4'=>'Under Stock');
$ass_condition = array('1'=>'Used','2'=>'Good','3'=>'Poor','4'=>'Damaged','New');
foreach($details['asset_categories'] as $asset_category_list){
	$asset_category_lists[$asset_category_list['id']] = $asset_category_list['category_description'];
}
foreach($details['locations'] as $locations_list){
	$locations_lists[$locations_list['id']] = $locations_list['location_name'];
}
foreach($details['asset_status'] as $asset_status){
	$ass_status[$asset_status['id']] = $asset_status['name'];
}
//print_r($ass_status);
$filterassetcategory = array($request['asset_category_id']);
if(isset($_POST['categroy_list'])){
	$filterassetcategory =explode(",",$_POST['categroy_list']);
	$filterassetstatus =explode(",",$_POST['asset_status']);
	$filterassetcondition =explode(",",$_POST['asset_condition']);
}
//echo "fdvdfvgf";
//print_r($filterassetcategory);
?>
<script type="text/javascript">
	function submitdetailsRequest(){

   var postarray = $("#assetsrequestform").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data: postarray+'&page=insertassetrequest',
            type: "POST",
            success:function(data){
            	console.log(data);
            	var res = JSON.parse(data);
                if(res.error=="404"){
                	alert("Oops Admin buddy Not Found");
                }else{
                	$('#kt_modal_dynamic_popup2').modal("hide");
                	location.reload();
                }
            }
        });
	}

function selectAssetId(){
	//$("#kt_modal_KTDatatable_remote").animate({ scrollTop: $("#assetsallocateform").scrollTop() }, 1000);
	
	$("#kt_modal_KTDatatable_remote").animate({ scrollTop: $("#kt_modal_KTDatatable_remote")[0].scrollHeight }, 1000);
	var allocate_asset_id = $('input[name="allocate_asset_id"]:checked').val();
	var assetdet = allocate_asset_id.split("||");
	selectAssetCategory(assetdet[3]);
$("#txtassetname").text(assetdet[1]);
$("#txtassetcode").text(assetdet[2]);
//document.getElementById('allocatassetlocation').value = assetdet[3];
$("#allocatassetlocation").select2("val", assetdet[3]);
$("#allocatassetcandition").select2("val", assetdet[4]);
$("#asset_id").val(assetdet[0]);
		//alert(assetdet[0]);
}

function submitdetailsAssetAllocate(){
	var formData = new FormData();
    formData.append("page", "insertassetallocationsingle"); 
    var dataArray = $("#assetsallocateform").serializeArray(),
    dataObj = {};
    $(dataArray).each(function(i, field){
      formData.append(field.name, field.value); 
    });
    var popattachment = document.getElementById('popattachment').files[0];
   formData.append("file",popattachment);
        jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
            	console.log(data);
              //  $('#kt_modal_KTDatatable_remote').modal("hide");
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"Record Successfully Insert",'');
            },
            error:function (){}
        });
	}



	function selectAssetCategory(cat_id =""){
		var asset_category_id = $("#asset_category_id").val();
		if(cat_id !=""){
			var asset_category_id = cat_id;
		}
		 jQuery.ajax({
            url: "ajax.php",
            data: 'page=selectAssetCategory&catgoryid='+asset_category_id,
            type: "POST",
            success:function(data){
            	//console.log(data);
            	var res = JSON.parse(data);
            	if(cat_id !=""){
					$("#listattributesshow").html(res.asset_attributes);
				}else{
					$("#listattributes").html(res.asset_attributes);
				}
           		$("#asset_list_id").html(res.option);
            	
            }
        });
	}


function filetrassett(){
	var req_id = $("#req_id").val();
	var categroy_list = [];
	$('.categroy_list').each(function(){
		if($(this).is(':checked')){
			categroy_list.push($(this).val());
		}
	});
	var asset_status = [];
	$('.asset_status').each(function(){
		if($(this).is(':checked')){
			asset_status.push($(this).val());
		}
	});
	var asset_condition = [];
	$('.asset_condition').each(function(){
		if($(this).is(':checked')){
			asset_condition.push($(this).val());
		}
	});




	jQuery.ajax({
		url: "views/custom_insert_asset_requests.php",
		data: "req_id="+req_id+"&categroy_list="+categroy_list+"&asset_status="+asset_status+"&asset_condition="+asset_condition,
		type: "POST",
		success:function(data){
				$('#dynamic_popup11').html(data);
				$('#kt_modal_dynamic_popup2').modal("show");
		}
	});
}
function getquontiy(valu){
	var requested_qty = $("#requested_qty").val();
	var penfingqty = requested_qty-valu;
	$("#pending_qty").val(penfingqty);
	$("#pendinreq").text(penfingqty);
}

</script>
<style>
	.drop-zone {
		height: 100px;
		padding: 25px;
		display: flex;
		align-items: center;
		justify-content: center;
		text-align: center;
		font-family: "Quicksand", sans-serif;
		font-weight: 500;
		font-size: 20px;
		cursor: pointer;
		color: #cccccc;
		border: 4px dashed #009578;
		border-radius: 10px;
	}

	.drop-zone--over {
		border-style: solid;
	}

	.drop-zone__input {
		display: none;
	}

	.drop-zone__thumb {
		width: 100%;
		height: 100px;
		border-radius: 10px;
		overflow: hidden;
		background-color: #cccccc;
		background-size: cover;
		position: relative;
	}

	.drop-zone__thumb::after {
		content: attr(data-label);
		position: absolute;
		bottom: 0;
		left: 0;
	/*	width: 100%;*/
		padding: 5px 0;
		color: #ffffff;
		background: rgba(0, 0, 0, 0.75);
		font-size: 14px;
		text-align: center;
	}
</style>
<div class="modal-header">
	<h4 class="modal-title">Asset Requests</h4>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body j-pro">
	<div class="j-content">
	<div class="card p-2 mt-2">	
		<form id="assetsrequestform" method="post" >

	<input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">

	<input type="hidden" name="req_id" id="req_id" value="<?php echo $_POST['req_id']; ?>">		
	
		<div class="row">
			<div class="form-group col-lg-4 select_emp w-full" >
				<label>Asset Category<span class="requried">*</span></label>
				<select class="form-select js-example-basic-single " name="asset_category_id" id="asset_category_id" onchange="selectAssetCategory()">
						<option value="">Select Any One</option>
				<?php foreach ($asset_category_lists as $asset_category_id=>$asset_category_name){ ?>					
					<option  value="<?php echo $asset_category_id; ?>" <?php echo $request['asset_category_id']==$asset_category_id?"selected":"";?>><?php echo ucwords($asset_category_name); ?></option>
				<?php } ?>
				</select> 
			</div>
			<div class="form-group col-lg-4 w-full" >
				<label>Asset<span class="requried">*</span></label>
				<select class="form-select js-example-basic-single" name="asset_list_id" id="asset_list_id">
						<option value="">Select Any One</option>
				<?php foreach ($assets_list as $list_asset){ ?>					
					<option  value="<?php echo $list_asset['id']; ?>" <?php echo $request['asset_id']==$list_asset['id']?"selected":"";?>><?php echo ucwords($list_asset['asset_description']); ?></option>
				<?php } ?>
				</select> 
			</div>
			<div class="form-group col-lg-4 " >
				<label>Attributes<span class="requried">*</span></label>
				<div id="listattributes">
					<?php 
					$explodeattribute = explode(",",$request['attribute']);
					foreach($attributes as $asset_attributes){  
						$checked = in_array($asset_attributes['id'],(array)$explodeattribute)?"checked":"";
						echo $checkattributes='<div class="checkbox-fade fade-in-primary">
		                                      <label class="form-label">
		                                        <input type="checkbox" name="attribute[]" '.$checked.' value="'.$asset_attributes['id'].'" >
		                                        <span class="cr">
		                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
		                                        </span>
		                                        <span>'.$asset_attributes['name'].'</span>
		                                      </label>
		                                    </div>'; 
					}
				?>

				</div>
				
			</div>
			
			<div class="form-group col-lg-4">
				<label>Start date</label>
				<input type="text" class="form-control datepicker" id="start_date"  name="start_date" value="<?php echo fromDate($request['start_date']); ?>">
			</div>
			<div class="form-group col-lg-4">
				<label>End date</label>
				<input type="text" class="form-control datepicker" id="end_date" name="end_date" value="<?php echo $request['return_date']!=""?fromDate($request['return_date']):""; ?>">
			</div>
			<div class="form-group col-lg-4 w-full">
				<label>Location</label>
			<select  class="form-select js-example-basic-single " name="location_id">
										<option value="">Select Any One</option>
								<?php foreach ($locations as $location) { ?>					
									<option location value="<?php echo $location['id']; ?>"  <?php echo $request['location_id']==$location['id']?"selected":"";?>><?php echo ucwords($location['location_name']); ?></option>
								<?php } ?>
								</select> </div>
								<div class=" col-lg-5 " >
				<label>How Do You Want The Asset To be Delivered?<span class="requried">*</span></label>
				<div class="form-radio">
				<div class="radio radio-inline">
                      <label class="form-label">
                        <input type="radio" name="asset_deliver" value="S" style="display:none;" <?php echo ($request['asset_deliver']=="S" || $request['asset_deliver']=="")?"checked":"";?>>
                        <i class="helper"></i>Self Pickup
                      </label>
                    </div>
                    <div class="radio radio-inline">
                      <label class="form-label">
                        <input type="radio" name="asset_deliver" value="D" style="display:none" <?php echo $request['asset_deliver']=="D"?"checked":"";?>>
                        <i class="helper"></i>Deliver Through Logistics Partner
                      </label>
                    </div>
                 </div>   
			</div>
			<div class="form-group col-lg-2">
				<label>Quantity</label>
				<input type="text" class="form-control" id="requested_qty" name="requested_qty" value="<?php echo $request['requested_qty']; ?>">
			</div>
			<div class="form-group col-lg-5">
				<label>Description</label>
				<textarea  class="form-control" id="description" rows="4" cols="50" name="description" ><?php echo $request['description']; ?></textarea>
			</div> 
		</div>
	</form>




















	<?php if($request['admin_buddy']==$_SESSION['userid']){ ?>	
		<div class="row">
			<div class="col-lg-4  p-3">
				<label class="f-w-700 mb-2 col-lg-12">Catgeory</label></br>
				<?php foreach ($asset_category_lists as $asset_category_id=>$asset_category_name){ ?>					
					<div class="checkbox-fade fade-in-primary">
                        <label class="form-label">
                            <input type="checkbox" class="categroy_list"  value="<?php echo $asset_category_id;?>" <?php echo in_array($asset_category_id,(array)$filterassetcategory)?"checked":"";?> onclick="filetrassett()" >
                            <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                            <span><?php echo ucwords($asset_category_name); ?></span>
                        </label>
                    </div>
				<?php } ?>
				<label class="f-w-700 mb-2 col-lg-12">Status</label>
				<?php foreach ($ass_status as $ass_statukey=>$ass_statu){ ?>					
					<div class="checkbox-fade fade-in-primary">
                        <label class="form-label">
                            <input type="checkbox" class="asset_status" <?php echo in_array($ass_statukey,(array)$filterassetstatus)?"checked":"";?> value="<?php echo $ass_statukey;?>" onclick="filetrassett()">
                            <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                            <span><?php echo ucwords($ass_statu); ?></span>
                        </label>
                    </div> 

				<?php } ?>
				<label class="f-w-700 mb-2 col-lg-12">Condition</label>
				<?php foreach ($ass_condition as $ass_conditiokey=>$ass_conditio) { ?>					
					<div class="checkbox-fade fade-in-primary">
                        <label class="form-label">
                            <input type="checkbox" <?php echo in_array($ass_conditiokey,(array)$filterassetcondition)?"checked":"";?>  class="asset_condition" value="<?php echo $ass_conditiokey;?>" onclick="filetrassett()">
                            <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                            <span><?php echo ucwords($ass_conditio); ?></span>
                        </label>
                    </div>
				<?php } ?>
			</div>
			<div class="col-lg-8">
			<?php foreach ($request_list_asset as $requestasset){ 
				$bgcolor = $requestasset['allocation_status']=="Y"?"bg-info":"";
			 ?>	
				<div class="card">
					<div class="row p-1 <?php echo $bgcolor; ?>">
						
				<div class="col-lg-3"><img  class="img-120"  src="<?php echo $File_Uploads_Path.$requestasset['asset_img']; ?>" /></div>
				<div class="col-lg-8">
					<label class="col-lg-4 f-w-700">Asset Name  </label><label class="col-lg-8">: <?php echo $requestasset['asset_description']; ?></label>	
					<label class="col-lg-4 f-w-700">Category  </label><label class="col-lg-8">: <?php echo $asset_category_lists[$requestasset['category_id']]; ?></label>	
					<label class="col-lg-4 f-w-700">Brand  </label><label class="col-lg-8">  : <?php echo $requestasset['brand']; ?></label>	
					<label class="col-lg-4 f-w-700">Serial Number  </label><label class="col-lg-8">: <?php echo $requestasset['serial_no']; ?></label>	
					<label class="col-lg-4 f-w-700">Condition  </label><label class="col-lg-8">: <?php echo $ass_condition[$requestasset['conditions']]; ?></label>	
					<label class="col-lg-4 f-w-700">Status  </label><label class="col-lg-8">: <?php echo $ass_status[$requestasset['asset_status']]; ?></label>	
					<?php if($requestasset['end_date'] !=""){ ?>
						<div class="col-lg-12">
							<label class="form-label label label-danger">Return Date : <?php echo $requestasset['end_date']; ?></label>
						</div>	
					<?php } ?>
				</div>
				<div class="col-lg-1">
					<?php if($requestasset['allocation_status']=="N"){?>
					<div class="radio radio-inline">
                      <label class="form-label">
                        <input type="radio" name="allocate_asset_id"  value="<?php echo $requestasset['id']."||".$requestasset['asset_description']."||".$requestasset['asset_code']."||".$requestasset['asset_location']."||".$requestasset['conditions']; ?>"  style="display:none;" onclick="selectAssetId()">
                        <i class="helper"></i>
                      </label>
                    </div>
                    <?php } ?>	
				</div>
			</div> 
			</div>
				
		<?php	} ?>
			</div>	
		</div>
	<form id="assetsallocateform" method="post" >
		
		<input type="hidden" name="employee_id" value="<?php echo $request['required_by']; ?>">
		<input type="hidden" name="req_id" id="req_id" value="<?php echo $_POST['req_id']; ?>">		
		<input type="hidden" name="asset_id" id="asset_id" value="">		
	
		<div class="row">
			<div class="form-group col-lg-4">
				<label>Start date</label>
				<input type="text" class="form-control datepicker" id="start_date"  name="start_date" value="<?php echo fromDate($request['start_date']); ?>">
			</div>
			<div class="form-group col-lg-4">
				<label>End date</label>
				<input type="text" class="form-control datepicker" id="end_date" name="end_date" value="<?php echo fromDate($request['return_date']); ?>">
			</div>
			<div class="col-lg-4">
				<label class="col-lg-4 f-w-700">Asset Name :</label><label class="col-lg-8" id="txtassetname"> </label>	
					<label class="col-lg-4 f-w-700">Asset Code : </label><label class="col-lg-8" id="txtassetcode"> </label>
			</div>
				
			<div class="form-group col-lg-4 w-full">
				<label>Location  <?php if($requestasset['asset_location'] !=""){ ?><span class="f-12 text-primary">Employee Location : <?php echo $locations_lists[$requestasset['asset_location']];?></span><?php } ?></label>
				<select  class="form-select js-example-basic-single " name="location_id" id="allocatassetlocation">
						<option value="">Select Any One</option>
				<?php foreach ($locations as $location) { ?>					
					<option location value="<?php echo $location['id']; ?>"  <?php echo $request['location_id']==$location['id']?"selected":"";?>><?php echo ucwords($location['location_name']); ?></option>
				<?php } ?>
				</select> 
			</div>
			<div class="form-group col-lg-4 w-full">
					<label class="col-lg-12">Condition</label>
					<select  class="form-select js-example-basic-single " name="candition" id="allocatassetcandition" >
						<option value="">Select Any One</option>
				<?php foreach ($ass_condition as $ass_conditiokey=>$ass_conditio) { ?>					
					<option  value="<?php echo $ass_conditiokey; ?>"><?php echo ucwords($ass_conditio); ?></option>
				<?php } ?>
				</select> 
			</div>
			<div class="form-group col-lg-4 w-full">
					<label class="col-lg-12">Status</label>
					<select  class="form-select js-example-basic-single " name="status" id="allocatassetstatus" onchange="assetFilter()">
							<option value="">Select Any One</option>
					<?php foreach ($ass_status as $ass_statukey=>$ass_statu) { ?>					
						<option  value="<?php echo $ass_statukey; ?>"><?php echo ucwords($ass_statu); ?></option>
					<?php } ?>
					</select>  
			</div>
			<div class="form-group col-lg-4 " >
				<label>Attributes<span class="requried">*</span></label>
				<div id="listattributesshow">
					
				</div>
				
			</div>
			<div class="form-group col-lg-2">
				<label>Request Quantity</label>
				<input type="number" class="form-control" id="requested_qty" name="requested_qty" value="<?php echo $request['requested_qty']; ?>" readonly>
			</div>
			<div class="form-group col-lg-2">
				<label>Transfer Quantity</label>
				<input type="number" class="form-control" id="transferred_qty" name="transferred_qty" value=""  onblur="getquontiy(this.value)">
				<input type="hidden" class="form-control" id="pending_qty" name="pending_qty" value="">
			</div>
			<div class="form-group col-lg-4">
				<label class="f-w-700 col-lg-6">Pending Quantity</label>
				<label class="col-lg-6 pendinreq"></label>
			</div>
			<div class=" col-lg-5" >
				<label>How Do You Want The Asset To be Delivered?<span class="requried">*</span></label>
				<div class="form-radio">
				<div class="radio radio-inline">
                      <label class="form-label">
                        <input type="radio" name="asset_deliver" value="S" style="display:none;" <?php echo ($request['asset_deliver']=="S" || $request['asset_deliver']=="")?"checked":"";?>>
                        <i class="helper"></i>Self Pickup
                      </label>
                    </div>
                    <div class="radio radio-inline">
                      <label class="form-label">
                        <input type="radio" name="asset_deliver" value="D" style="display:none" <?php echo $request['asset_deliver']=="D"?"checked":"";?>>
                        <i class="helper"></i>Deliver Through Logistics Partner
                      </label>
                    </div>
                 </div>   
			</div><div class="form-group col-lg-3">
					<label class="col-lg-12">Asset Img</label>
					<div class="">
					<input type="hidden" id="oldattachment" name="oldattachment" value='<?php echo $assetsdeatil['asset_img']; ?>'>
					<div class="drop-zone">
						<?php if($assetsdeatil['asset_img'] !=""){ ?> 
							<div class="drop-zone__thumb"  style="background-image: url(<?php echo $File_Uploads_Path.$assetsdeatil['asset_img']; ?>)"></div>
						<?php  }else{ ?>
							<span class="drop-zone__prompt">Drop file here or click to upload</span>
						<?php } ?>
						<input type="file" name="asset_img" class="drop-zone__input" id="popattachment" >
					</div>
				</div>
			</div>
			<div class="form-group col-lg-12">
				<label>Remarks</label>
				<textarea type="text" class="form-control" id="remarks" rows="4" cols="50" name="remarks" ></textarea>
			</div>
		</div>
	</form>	


<?php } ?>	




































	</div>
	
	</div>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
	<!-- <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetailsRequest()" <?php echo $_POST['req_id']!=""?"disabled":""; ?>>Submit</button> -->
	<?php if($_POST['req_id']!="" AND $request['admin_buddy']==$_SESSION['userid']){ ?> 
		<button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetailsAssetAllocate()" >Asset Allocate?</button>
	<?php }else{ ?>
		<button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetailsRequest()" <?php echo $_POST['req_id']!=""?"disabled":""; ?>>Submit</button>
	<?php }  ?>
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
<script type="text/javascript">
	document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
		const dropZoneElement = inputElement.closest(".drop-zone");

		dropZoneElement.addEventListener("click", (e) => {
			inputElement.click();
		});

		inputElement.addEventListener("change", (e) => {
			if (inputElement.files.length) {
				updateThumbnail(dropZoneElement, inputElement.files[0]);
			}
		});

		dropZoneElement.addEventListener("dragover", (e) => {
			e.preventDefault();
			dropZoneElement.classList.add("drop-zone--over");
		});

		["dragleave", "dragend"].forEach((type) => {
			dropZoneElement.addEventListener(type, (e) => {
				dropZoneElement.classList.remove("drop-zone--over");
			});
		});

		dropZoneElement.addEventListener("drop", (e) => {
			e.preventDefault();

			if (e.dataTransfer.files.length) {
				inputElement.files = e.dataTransfer.files;
				updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
			}
			dropZoneElement.classList.remove("drop-zone--over");
		});
	});
	function updateThumbnail(dropZoneElement, file) {
		let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");
// First time - remove the prompt
if (dropZoneElement.querySelector(".drop-zone__prompt")) {
	dropZoneElement.querySelector(".drop-zone__prompt").remove();
}
if (!thumbnailElement) {
	thumbnailElement = document.createElement("div");
	thumbnailElement.classList.add("drop-zone__thumb");
	dropZoneElement.appendChild(thumbnailElement);
}
thumbnailElement.dataset.label = file.name;
if (file.type.startsWith("image/")) {
	const reader = new FileReader();
	reader.readAsDataURL(file);
	reader.onload = () => {
		thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
	};
} else {
	thumbnailElement.style.backgroundImage = null;
}
}
</script>
<!-- 
<script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script> -->