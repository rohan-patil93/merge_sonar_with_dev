<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$File_Uploads_Path = "uploads/assets/";
$rid = $_POST['asset_id'];
$url = API."?mode=getassetallocation&Rid=".$rid."&resultgettype=INSERTPAGE"; 
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$assetcategory = $details['asset_categories'];
//echo "<pre>"; print_r($details); echo "</pre>";
$locations = $details['locations'];
$departments = $details['departments'];
$users = $details['users'];
$assetsdeatil = $details['assets'];
$assets_list = $details['assets_list'];
$vendors = $details['vendors'];
$chart_of_accounts = $details['chart_of_accounts'];
$depreciation_method= $details['depreciation_method'];
$journals = $details['journals'];
$custom_field=json_decode(urldecode($assetsdeatil['custom_field']),1);
//$ass_status = array('1'=>'In Stock','2'=>'Out Stock','3'=>'Out For Repair','4'=>'Under Stock');
$ass_condition = $details['asset_condition_type'];
$allocation_type = array('1'=>'Create Assets','2'=>'Update Assets','3'=>'Allocation','4'=>'Return Assets','5'=>'Transefer');
foreach($details['asset_status'] as $asset_status){
	$ass_status[$asset_status['id']] = $asset_status['name'];
}
$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'category_id'=>$assetsdeatil['category_id']);
$wh1['field'] = "id,name";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=asset_attributes&data=".$data1; 
$data_details1 = callAPI($url1);
$attributes = json_decode($data_details1,1);

$asset_categories =array();
foreach($assetcategory as $acat){
	$asset_categories[$acat['id']] = $acat;
}

//print_r($assetsdeatil);
?>
<style>
	.drop-zone {
		height: 300px;
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
		height: 160px;
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
		width: 100%;
		padding: 5px 0;
		color: #ffffff;
		background: rgba(0, 0, 0, 0.75);
		font-size: 14px;
		text-align: center;
	}
</style>
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
	}else{
		thumbnailElement.style.backgroundImage = null;
	}
}
function selectAssetCategory(){
	var asset_category_id = $("#asset_category_id").val();
	jQuery.ajax({
        url: "ajax.php",
        data: 'page=selectAssetCategory&catgoryid='+asset_category_id+"&check=N",
        type: "POST",
        async:false,
        success:function(data){
        	console.log(data);
        	var res = JSON.parse(data);
        	console.log(res);
       		$("#asset_list_id").html(res.option);
       		if(res.asset_attributes !=""){
        		$("#listattributes").html(res.asset_attributes);
        	}else{
        		$("#listattributes").html("<span class='text-danger'><i class='ti-alert'></i>  Attributes Not Define</span>");
        	}
        	returnDepreciation();
        }
    });
}
	
function checkOwnership(){
	var ownerships = $('.ownership1:checked').val();
	if(ownerships=="P"){
		$(".ownerships").show('slow');
	}else{
		$(".ownerships").hide('slow');
	}
}	
function getscrapPrice(){
	var asset_cost = $("#asset_cost").val();
	var disposal_proceeds = $("#disposal_proceeds").val();
	if(asset_cost =="" || asset_cost =="0"){
		swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Please Enter Purchase Price", "error");	
		return;
	}
	var grandtotal = 0;
	if(disposal_proceeds != '0' && asset_cost != '0' && disposal_proceeds != "" && asset_cost != ""){
		grandtotal = parseFloat(asset_cost) - parseFloat(disposal_proceeds);
		$("#depn_rate").val(grandtotal);
		console.log('deprate')
	}else{
		$("#depn_rate").val(asset_cost);
		console.log('else deprate')
	}
	returnDepreciation();
}
function getDepreciationMethod(){
	var capitalization_date = $("#capitalization_date").val();
	var purchase_date = $("#purchase_date").val();
	if(capitalization_date =="" || capitalization_date =="0"){
		$("#capitalization_date").val(purchase_date);
		//swal("<?php// echo ucwords($_SESSION['config']['name']); ?>", "Please Select Capitalization Date ", "error");	
		//return;
	}
	var depreciation_method = $("#depreciation_method").val();
	var duration = $("#duration").val();
	var end_of_life = $("#end_of_life").val();
	var depn_rate = $("#depn_rate").val();
	jQuery.ajax({
        url: "ajax.php",
        data: 'page=getdepreciationtable&depreciation_method='+depreciation_method+"&duration="+duration+"&capitalization_date="+capitalization_date+'&depn_rate='+depn_rate,
        type: "POST",
        success:function(data){
        	console.log(data);
        	var res = JSON.parse(data);
        	   //	console.log(res);
        	$("#depreciation_table").html(res.depreciation_table);
        	$("#end_of_life").val(res.end_of_life);
     
       		//$("#asset_list_id").html(res.option);
        }
    });
}
</script>
<div class="modal-header">
	<h4 class="modal-title">Add Assets</h4>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<div class="">
	<form id="assetsinsertform" method="post" >	
		<input type="hidden" name="Rid" value="<?php echo $rid; ?>">
		<div class="row ">
			<div class="col-lg-8">  
				<div class="row">
					<div class="form-group col-lg-6">
						<label class="form-label">Name<span class="requried text-danger">*</span></label>
						<span class="d-block error-message text-danger"></span>
						<input class="form-control requried_field"  type="text"  id="asset_description" name="asset_description"  value="<?php echo $assetsdeatil['asset_description']; ?>">
					</div> 
					<div class="form-group col-lg-6">
						<label class="form-label">Code</label>
						<input class="form-control"  type="text"  id="asset_code" name="asset_code"  value="<?php echo $assetsdeatil['asset_code']; ?>" readonly>
					</div> 
					<div class="form-group col-lg-6 w-full">
						<label class="form-label">Category<span class="requried text-danger">*</span></label>
						<span class="d-block error-message text-danger"></span>
						<select  class="form-select js-example-basic-single requried_field" name="category_id" id="asset_category_id" onchange="selectAssetCategory()">
							<option value="">Select Any One</option>
							<?php foreach ($assetcategory as $assetcategor) { ?>					
								<option value="<?php echo $assetcategor['id']; ?>" <?php echo $assetcategor['id'] == $assetsdeatil['category_id']?"selected":""; ?> ><?php echo ucwords($assetcategor['category_description']); ?></option>
							<?php } ?>
						</select> 
					</div> 
					<div class="form-group col-lg-6 w-full">
						<label class="form-label">Location <span class="requried text-danger">*</span></label>
						<span class="d-block error-message text-danger"></span>
						<select  class="form-select js-example-basic-single requried_field" name="asset_location">
							<option value="">Select Any One</option>
							<?php foreach ($locations as $location) { ?>					
								<option location value="<?php echo $location['id']; ?>" <?php echo $location['id'] == $assetsdeatil['asset_location']?"selected":""; ?>><?php echo ucwords($location['location_name']); ?></option>
							<?php } ?>
						</select> 
					</div> 
					<div class="form-group col-lg-6 w-full">
						<label class="form-label">Status <span class="requried text-danger">*</span></label>
						<span class="d-block error-message text-danger"></span>
						<select  class="form-select js-example-basic-single requried_field" name="status">
							<?php foreach ($ass_status as $ass_statukey=>$ass_statu) { ?>					
								<option  value="<?php echo $ass_statukey; ?>" <?php echo ($ass_statukey == $assetsdeatil['asset_status'] OR ($assetsdeatil['asset_status'] == '' AND $assetsdeatil['asset_status'] == '1'))?"selected":""; ?>><?php echo ucwords($ass_statu); ?></option>
							<?php } ?>
						</select> 
					</div> 
					<div class="form-group col-lg-6 w-full">
						<label class="form-label">Asset Custodian <span class="requried text-danger">*</span></label>
						<span class="d-block error-message text-danger"></span>
						<select  class="form-select js-example-basic-single requried_field" name="asset_custodian">
							<option value="">Select Any One</option>
							<?php foreach ($users as $userlist) { ?>					
							<option value="<?php echo $userlist['id']; ?>" <?php echo ($assetsdeatil['asset_custodian'] == $userlist['id'] || ($assetsdeatil['asset_custodian'] =="" AND $userlist['id'] == $_SESSION['userid']))?"selected":"";?>><?php echo ucwords($userlist['display_name']); ?></option>
							<?php } ?>
						</select> 
					</div>
					<div class="form-group col-lg-6">
						<label class="form-label">Warranty Expiry Date <span class="requried text-danger">*</span></label>
						<span class="d-block error-message text-danger"></span>
						<input class="form-control datepicker requried_field"  type="text"  id="warranty_expiry_date" name="warranty_expiry_date"  value="<?php echo  $assetsdeatil['warranty_expiry_date']!=""?fromDate($assetsdeatil['warranty_expiry_date']):""; ?>">
					</div>
					<div class="form-group col-lg-6 mb-0">
					<label>Attributes</label>
						<div id="listattributes">
							<?php 
							$explodeattribute = explode(",",$assetsdeatil['attribute']);
							foreach($attributes as $asset_attributes){  
								$checked = in_array($asset_attributes['id'],(array)$explodeattribute)?"checked disabled":"";
							?>	
							<div class="checkbox-fade fade-in-primary mt-2">
	                          	<label class="form-label">
	                            	<input type="checkbox" name="attribute[]" <?php echo $checked; ?> value="<?php echo $asset_attributes['id']; ?>" >
		                            <span class="cr">
		                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
		                            </span>
		                            <span><?php echo $asset_attributes['name']; ?></span>
	                          	</label>
	                        </div> 
						<?php } ?>
						</div>
					</div>
					<?php if($assetsdeatil['qr_code'] !=""){ ?>
					<div class=" form-group col-lg-6">
						<label class="form-label d-block">QR Code</label>
						<img src="<?php echo "uploads/equipments/qr_code/".$assetsdeatil['qr_code'] ; ?>" class="img-100"  width="200" onerror="this.src='uploads/all/no-image.jpg';"/>
					</div>
					<?php } if($assetsdeatil['id'] !=""){ ?>
					<div class=" form-group col-lg-6">
						<label class="form-label d-block">Barcode </label>

						<img src="<?php echo "barcode/barcode.php?text=".$assetsdeatil['asset_code'].'-'.$assetsdeatil['asset_description']; ?>" width="300" onerror="this.src='uploads/all/no-image.jpg';"/>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="form-group col-lg-4">
				<label class="ml-5">Asset Image</label>
				<div class="card-block">
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
	    </div>
		<div class="row ">
			<div class="col-lg-6">
				<div class="card">
				    <div class="card-header table-card-header">
				        <h5>Addition information</h5>
				        <div class="card-header-right"></div>
				    </div>
				    <div class="card-body">
						<div class="row">
		                	<div class="form-group col-lg-6">
								<label class="form-label">Brand Name</label>
								<input class="form-control"  type="text"  id="brand" name="brand"  value="<?php echo $assetsdeatil['brand']; ?>">
							</div> 
							<div class="form-group col-lg-6">
								<label class="form-label">Model Number</label>
								<input class="form-control"  type="text"  name="model"  value="<?php echo $assetsdeatil['model']; ?>">
							</div> 
							<div class="form-group col-lg-6">
								<label class="form-label">Serial Number</label>
								<input class="form-control"  type="text"  id="serial_no" name="serial_no"  value="<?php echo $assetsdeatil['serial_no']; ?>">
							</div>
							<div class="form-group col-lg-6 w-full">
								<label class="form-label">Condition</label>
								<select  class="form-select js-example-basic-single " name="conditions">
									<option value="">Select Any One</option>
								<?php foreach ($ass_condition as $ass_conditiokey=>$ass_conditio) { ?>					
									<option  value="<?php echo $ass_conditiokey; ?>" <?php echo $ass_conditiokey == $assetsdeatil['conditions']?"selected":""; ?>><?php echo ucwords($ass_conditio); ?></option>
								<?php } ?>
								</select> 
							</div> 
							<div class="form-group col-lg-6 w-full">
								<label class="form-label">Linked Asset</label>
								<select  class="form-select js-example-basic-single " name="Parent_asset">
										<option value="">Select Any One</option>
								<?php foreach ($assets_list as $assets_list) { ?>					
									<option  value="<?php echo $assets_list['id']; ?>" <?php echo $assets_list['id'] == $assetsdeatil['Parent_asset']?"selected":""; ?>><?php echo $assets_list['asset_code']." - ".ucwords($assets_list['asset_description']); ?></option>
								<?php } ?>
								</select> 
							</div>
							<div class="form-group col-lg-6">
								<label class="form-label">Description</label>
								<textarea type="text" class="form-control" id="long_description" rows="4" cols="50" name="long_description"  ><?php echo $assetsdeatil['long_description']; ?></textarea>
							</div> 
							<div class="col-lg-6">
								<label class="form-label">Owner Ship</label>
								<div class="form-radio">
								    <div class="radio radio-inline">
								      <label class="form-label">
								        <input type="radio" class="ownership1" <?php echo ($assetsdeatil['ownership']=="S" || $assetsdeatil['ownership']=="")?"checked":""; ?>  name="ownership" value="S" style="display: none;" onchange="checkOwnership()">
								        <i class="helper"></i>Self </label>
								    </div>
								    <div class="radio radio-inline">
								      <label class="form-label">
								        <input type="radio" class="ownership1" name="ownership" value="P" <?php echo $assetsdeatil['ownership']=="P"?"checked":""; ?> style="display: none;" onchange="checkOwnership()">
								        <i class="helper"></i>Partner
								      </label>
								    </div>
								</div>
							</div> 
							<div class="form-group col-lg-6 ownerships w-full" style="display:<?php echo ($assetsdeatil['ownership']=="S" || $assetsdeatil['ownership']=="")?"none":""; ?>">
								<label class="form-label">Partner Vendor</label>
								<select class="form-select js-example-basic-single " name="vendor">
									<option value="">Select Any One</option>
								<?php foreach ($vendors as $vendors1){ ?>					
									<option  value="<?php echo $vendors1['id']; ?>" <?php echo $vendors1['id'] == $assetsdeatil['rented_from']?"selected":""; ?>><?php echo ucwords($vendors1['display_name']); ?></option>
								<?php } ?>
								</select> 
							</div>
		                </div>
		            </div>
                </div>
			</div>
			<div class="col-lg-6">
				<div class="card">
				    <div class="card-header table-card-header">
				        <h5>Purchase information</h5>
				        <div class="card-header-right"></div>
				    </div>
				    <div class="card-body">
				    	<div class="row">
		                	<div class="form-group col-lg-6">
								<label class="form-label">PO Number</label>
								<input class="form-control"  type="text"  id="po_number" name="po_number"  value="<?php echo $assetsdeatil['po_number']; ?>">
							</div>
							<div class="form-group col-lg-6">
								<label class="form-label">PO Date</label>
								<input class="form-control datepicker"  type="text"  id="po_date" name="po_date"  value="<?php echo  $assetsdeatil['po_date']!=""?fromDate($assetsdeatil['po_date']):""; ?>">
							</div>
							<div class="form-group col-lg-6">
								<label class="form-label">Invoice Number</label>
								<input class="form-control"  type="text"  id="invoice_number" name="invoice_number"  value="<?php echo $assetsdeatil['invoice_number']; ?>">
							</div>
							<div class="form-group col-lg-6">
								<label class="form-label">Invoice Date</label>
								<input class="form-control datepicker"  type="text"  id="Invoice_date" name="Invoice_date" value="<?php echo  $assetsdeatil['Invoice_date']!=""?fromDate($assetsdeatil['Invoice_date']):""; ?>" >
							</div>
							<div class="form-group col-lg-6">
								<label class="form-label">Purchase Number</label>
								<input class="form-control "  type="text"  id="purchase_number" name="purchase_number"  value="<?php echo $assetsdeatil['purchase_number']; ?>">
							</div>
							<div class="form-group col-lg-6">
								<label class="form-label">Purchase Date <span class="requried text-danger">*</span></label>
								<span class="d-block error-message text-danger"></span>
								<input class="form-control datepicker requried_field"  type="text"  id="purchase_date" name="purchase_date"  value="<?php echo  $assetsdeatil['purchase_date']!=""?fromDate($assetsdeatil['purchase_date']):""; ?>">
							</div>
							<div class="form-group col-lg-6 w-full">
								<label class="form-label ">Vendor</label>
								<select class="form-select js-example-basic-single " name="vendor">
									<option value="">Select Any One</option>
								<?php foreach ($vendors as $vendors1) { ?>					
									<option  value="<?php echo $vendors1['id']; ?>" <?php echo $vendors1['id'] == $assetsdeatil['vendor']?"selected":""; ?>><?php echo ucwords($vendors1['display_name']); ?></option>
								<?php } ?>
								</select> 
							</div>
							<div class="form-group col-lg-6">
								<label class="form-label">Purchase Price <span class="requried text-danger">*</span></label>
								<span class="d-block error-message text-danger"></span>
								<input class="form-control text-right requried_field"  type="text"  id="asset_cost" name="asset_cost"  value="<?php echo $assetsdeatil['asset_cost']; ?>" onblur="getscrapPrice()">
							</div>
                    	</div>
				    </div>
				</div>
			</div>
		</div>
	     <div class="card-block accordion-block color-accordion-block">
            <div class="color-accordion" id="color-accordion">
                <a class="accordion-msg b-none waves-effect waves-light bg-gray">Finanacial information</a>
                <div class="accordion-desc">
                	<div class="row">
	                   	<!-- <div class="form-group col-lg-6">
							<label class="form-label">Capitalization Price</label>
							<input class="form-control text-right"  type="text"  id="capitalization_price" name="capitalization_price"  value="<?php //echo $assetsdeatil['capitalization_price']; ?>" onblur="returnDepreciation()">
						</div> -->
						<div class="form-group col-lg-6">
							<label class="form-label">Capitalization Date</label>
							<input class="form-control datepicker"  type="text"  id="capitalization_date" name="capitalization_date"  value="<?php echo $assetsdeatil['capitalization_date'] !=""?fromDate($assetsdeatil['capitalization_date']):""; ?>"  onchange="returnDepreciation()">
						</div>
						<div class="form-group col-lg-3">
							<label class="form-label">Scrap Price</label>
							<input class="form-control text-right"  type="text"  id="disposal_proceeds" name="disposal_proceeds"  value="<?php echo $assetsdeatil['disposal_proceeds']; ?>" onblur="getscrapPrice(this.value)">
						</div>
						<div class="form-group col-lg-3">
							<label class="form-label">Book Value</label>
							<input class="form-control text-right"  type="text"  id="depn_rate" name="depn_rate"  value="<?php echo $assetsdeatil['depn_rate']; ?>" readonly>
						</div>
						<div class="form-group col-lg-6 w-full" style="">
							<label class="form-label">Depreciation methods <span class="requried">*</span></label>
							<select class="form-select js-example-basic-single " name="depreciation_method"  id="depreciation_method" onchange="getDepreciationMethod()">
							<option value="">Select Any One</option>
							<?php foreach($depreciation_method as $dm) { 
								$disable =''; if($dm['genc_detail_code'] == 'PU'){ $disable = 'disabled'; } ?>
									<option value="<?php echo $dm['genc_detail_code']; ?>" <?php echo $assetsdeatil['depreciation_method'] ==$dm['genc_detail_code']?"selected":""; ?> <?php echo $disable;?>><?php echo $dm['genc_detail_name'];?></option>
								<?php } ?>
							</select> 
						</div> 
						<div class="form-group col-lg-3" style="pointer-events:none; opacity:0.7">
							<label class="form-label">Duration</label>
							<input class="form-control text-right"  type="number"  id="duration" name="duration"  value="<?php echo $assetsdeatil['duration']; ?>" onblur="getDepreciationMethod()">
						</div>
						<div class="form-group col-lg-3" style="pointer-events:none; opacity:0.7">
							<label class="form-label">End Of life</label>
							<input class="form-control datepicker"  type="text"  id="end_of_life" name="end_of_life"  value="<?php echo  $assetsdeatil['end_of_life']!=""?fromDate($assetsdeatil['end_of_life']):""; ?>" onblur="getDepreciationMethod()">
						</div>
						<?php /** ?>
						<div class="form-group col-lg-4 w-full" style="pointer-events:none; opacity:0.7">
							<label class="form-label d-block">Depreciation Account</label>
							<select name="depreciation_account" id="depreciation_account" class="form-control form-select js-example-basic-single" required>
								<option value="">Select Any One </option>
								<?php foreach($chart_of_accounts as $coa) {  ?>
									<option value="<?php echo $coa['account_code']; ?>" <?php echo $assetsdeatil['depreciation_account'] ==$coa['id']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
								<?php } ?>
							</select>	
						</div>
						<div class="form-group col-lg-4 w-full" style="pointer-events:none; opacity:0.7">
							<label class="form-label d-block">Expense Account</label>
							<select name="expense_account" id="expense_account" class="form-control form-select js-example-basic-single" required>
								<option value="">Select Any One </option>
								<?php foreach($chart_of_accounts as $coa) {  ?>
									<option value="<?php echo $coa['account_code']; ?>" <?php echo $assetsdeatil['expense_account'] ==$coa['id']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
								<?php } ?>
							</select>	
						</div>
						<div class="form-group col-lg-4 w-full" style="pointer-events:none; opacity:0.7">
							<label class="form-label d-block">Journal</label>
							<select name="journal_id"  id="journal_id" class="form-control form-select js-example-basic-single" required>
								<option value="">Select Any One </option>
								<?php foreach($journals as $jou) {  ?>
									<option value="<?php echo $jou['id']; ?>" <?php echo $assetsdeatil['journal_id'] ==$jou['id']?"selected":""; ?>><?php echo $jou['journal_name'];?></option>
								<?php } ?>
							</select>	
						</div> <?php **/ ?>
						<?php $depreciationtable = json_decode($assetsdeatil['depreciation_table'],1); ?>
						<div class="col-lg-12">
							<div class="card">
								<div class="card-body" id="depreciation_table">
									<table class="table table-hover m-b-0" style="font-size:13px;">
										<thead>
											<tr>
												<th>#</th>
												<th>Depreciation Date</th>
												<th class="text-right">Depreciation</th>
												<th class="text-right">Accumulated Depreciation</th>
												<th class="text-right">Book Value</th>
											</tr>
										</thead>
										<tbody>
											<?php $i=1;foreach($depreciationtable as $dp){?>
											<tr>
												<td class="p-2"><?php echo $i; ?></td>
												<td class="p-2"><?php echo fromDate($dp['depreciation_date']); ?></td>
												<td class="p-2 text-right" ><?php echo $dp['per_year_deprciation']; ?></td>
												<td class="p-2 text-right"><?php echo $dp['accumulated_depreciation']; ?></td>
												<td class="text-right p-2"><?php echo $dp['depreciation_price']; ?></td>
											</tr>
											<?php $i++; } ?>	
										</tbody>
									</table>
								</div>
							</div>
						</div>
                	</div>

                	<!-- <div class="row">
	                   	<div class="form-group col-lg-6">
							<label>Capitalization Price</label>
							<input class="form-control text-right"  type="text"  id="capitalization_price" name="capitalization_price"  value="<?php echo $assetsdeatil['capitalization_price']; ?>">
						</div>
						<div class="form-group col-lg-6">
							<label>Capitalization Date</label>
							<input class="form-control datepicker"  type="text"  id="capitalization_date" name="capitalization_date"  value="<?php echo $assetsdeatil['capitalization_date'] !=""?fromDate($assetsdeatil['capitalization_date']):""; ?>">
						</div>
						<div class="form-group col-lg-6">
							<label>End Of life</label>
							<input class="form-control datepicker"  type="text"  id="end_of_life" name="end_of_life"  value="<?php echo  $assetsdeatil['end_of_life']!=""?fromDate($assetsdeatil['end_of_life']):""; ?>">
						</div>
						<div class="form-group col-lg-6">
							<label>Scrap Price</label>
							<input class="form-control text-right "  type="text"  id="disposal_proceeds" name="disposal_proceeds"  value="<?php echo $assetsdeatil['disposal_proceeds']; ?>">
						</div>
						<div class="form-group col-lg-6">
							<label>Depreciation Rate(%)</label>
							<input class="form-control text-right"  type="text"  id="depn_rate" name="depn_rate"  value="<?php echo $assetsdeatil['depn_rate']; ?>">
						</div>
                	</div> -->


                </div>
                 <a class="accordion-msg b-none waves-effect waves-light bg-gray">Custom Fields</a>
                <div class="accordion-desc">

                	<div class="row newfield">
                		<?php foreach($custom_field as $custom_fields){ 
                			$clss = "";
                			 $types = "text";
                			if($custom_fields['fieldtype'] == "date"){
                				$clss = "datepicker";
                			}elseif($custom_fields['fieldtype'] == "number"){
                				$clss = "text-right";
                				$types = "number";
                			}
                		 ?>
                        <div class="form-group col-lg-6">
							<label>Manufacturing Date</label>
							<input class="form-control <?php echo $clss; ?>  fieldname"  type="<?php echo $types; ?>"   name="fieldvalue[]"  value="<?php echo $custom_fields['fieldvalue']; ?>">
							<input  type="hidden" class="fieldname"  name="fieldname[]"  value="<?php echo $custom_fields['fieldname']; ?>">
							<input  type="hidden" class="fieldname"  name="fieldtype[]"  value="<?php echo $custom_fields['fieldtype']; ?>">
						</div>
					<?php } ?>
				 	</div>
				 	<div class="row">
				 		<div class="col-lg-4 form-group">
					 		<label>Enter Field Name</label>
					 		<input class="form-control "  type="text"  id="ent_field_name" >
							<span class="text-danger" id="fileserrormess"></span>								
						</div>
						<div class="col-lg-4 form-group w-full">
					 		<label>Type</label>
							<select  class="form-select js-example-basic-single " id="slct_field_type">
									<option value="text">Text</option>
									<option value="number">Number</option>
									<option value="date">Date</option>
							</select> 	 
						</div>
						<div class="col-lg-2">
				 			<button type="button" class="btn btn-primary waves-effect waves-light mt-4" onclick="addnewfields()">Add Field</button>
				 		</div>
         			</div>
				</div>
				
			</div>	
        </div>
     </form>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
	<button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()"><?php echo $rid !=""?"Update":"Submit"; ?></button>
</div>
<!-- Accordion js -->
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
<script type="text/javascript">

function returnDepreciation(){
	var category_id = $("#asset_category_id").val();
	if(category_id =="" || category_id =="0"){
		swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Please Select Asset Category", "error");	
		return;
	}
	var capitalization_date = $("#capitalization_date").val();
	if(asset_cost =="" || asset_cost =="0"){
		swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Please Enter Purchase Price", "error");	
		return;
	}
	var depn_rate = $("#depn_rate").val();
	jQuery.ajax({
        url: "ajax.php",
        data: 'page=getassetcategorydetails&category_id='+category_id+'&capitalization_date='+capitalization_date+'&depn_rate='+depn_rate,
        type: "POST",
        async:false,
        success:function(data){
        	console.log(data);
            var res = JSON.parse(data);
        	$("#depreciation_table").html(res.depreciation_table);
        	$("#end_of_life").val(res.end_of_life);
        	$("#duration").val(res.duration);
        	$("#depreciation_method").val(res.default_depn_type).trigger('change');
        	//$("#depreciation_account").val(res.depreciation_account).trigger('change');
        	//$("#expense_account").val(res.disposal_account).trigger('change');
        	//$("#journal_id").val(res.Journal_id).trigger('change');
       },
        error:function (){}
    });
}
function submitdetails(){
	var validations = ajaxFormSubmitValidation();
    if(validations == 0){
	  	var formData = new FormData();
	    formData.append("page", "insertasset"); 
	    var dataArray = $("#assetsinsertform").serializeArray(),
	    dataObj = {};
	    $(dataArray).each(function(i, field){
	      formData.append(field.name, field.value); 
	    });
	    var popattachment = document.getElementById('popattachment').files[0];
	    console.log(popattachment);
	   	formData.append("file",popattachment);
        jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            async:false,
            success:function(data){
            	console.log(data);
            	result = JSON.parse(data);
            	console.log(result);
            	if(result.res.status == "sucess"){
            		$('#kt_modal_dynamic_popup2').modal("hide");
               		tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000'];?>','');
               		closeInsertEquipment();
               		//location.reload();
            	}else{
            		tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',result.res.message,'');
            	}
            },
            error:function (){}
        });
	}
}
function addnewfields(){
	var ent_field_name = $("#ent_field_name").val();
	var slct_field_type = $("#slct_field_type").val();
	$("#fileserrormess").text(" ");
	//alert(ent_field_name);
	if(ent_field_name==""){
		//alert("hi");
		$("#ent_field_name").val("");
		$("#fileserrormess").text("Please Enter Field Name");
		return false;
	}
// var newfield = "";
// 	var fieldnamess = [];
//    $(".fieldname").each(function(index) {
//    	fieldnamess.push($(this).val()); 
//        if ($.inArray($(this).val(), ent_field_name) != -1)
//        {
//          alert("already exist this mileage");
//          newfield = "N";
// 	}
	
//    });
//  if(newfield != "N"){
	if(slct_field_type == "number"){
		var html = '<div class="form-group col-lg-6"><label>'+ent_field_name+'</label><input class="form-control text-right"  type="number"  name="fieldvalue[]"  ><input  type="hidden" class="fieldname"  name="fieldname[]"  value="'+ent_field_name+'"><input  type="hidden" class="fieldname"  name="fieldtype[]"  value="number"></div>';
	}else if(slct_field_type == "date"){
		var html = '<div class="form-group col-lg-6"><label>'+ent_field_name+'</label><input class="form-control datepicker"  type="text"  name="fieldvalue[]"  ><input  type="hidden" class="fieldname"  name="fieldname[]"  value="'+ent_field_name+'"><input  type="hidden" class="fieldname"  name="fieldtype[]"  value="date"></div>';
	}else{
		var html = '<div class="form-group col-lg-6"><label>'+ent_field_name+'</label><input class="form-control "  type="text"  name="fieldvalue[]"  ><input  type="hidden" class="fieldname"  name="fieldname[]"  value="'+ent_field_name+'"><input  type="hidden" class="fieldname"  name="fieldtype[]"  value="text"></div>';
	}
	$(".newfield").append(html);
	$( ".datepicker" ).datepicker({
		showOn: "both", 
		buttonText: "Calendar",
		dateFormat: date_format,
		changeMonth: true,
		changeYear: true
	 }).next("button.ui-datepicker-trigger")
      .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

//}
}
function closeInsertEquipment(){
    var urlParams = new URLSearchParams(window.location.search);
    var param_value = "ASSETS";
    if (urlParams.has('TAB1')) {
        param_value = urlParams.get('TAB1');
        param_value = (param_value !=null || param_value !="")?param_value:"ASSETS";
    }
    if(typeof getBuildingDynamicPage == 'function'){
		getBuildingDynamicPage(param_value);	
    }else if(typeof assetFilter == 'function'){
		assetFilter();	
	}else{
		location.reload();
	}
}
</script>