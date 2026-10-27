<?php
$_SESSION['apply_js']['multiselest'] = 1;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// echo '<pre>'; print_r($_POST); echo '</pre>';
// echo '<pre>'; print_r($_FILES); echo '</pre>';
if(isset($_POST['submit'])){
	$table_name = 'complaints';
    $last_id = '';
	if ($_POST['submit'] == 'insert') {
		unset($_POST['submit']);
		$data = $_POST;
		$data['current_status'] = '0';
		$data['is_active'] = '0';
		$data['status_date'] = date('Y-m-d H:i:s');
		$data['company_id'] = $_SESSION['company_id'];
		$data['source_type'] = !empty($data['source_type']) ? $data['source_type'] : 'adhoc';
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $_SESSION['userid'];
		$data['transaction_no'] = dyanamicserialnumber($table_name);
		$url = API . "?mode=adddata&table=" . $table_name . "&method=post";
		$response = file_post_contents($url, $data, 'insert');
		$response = json_decode($response, 1);
        $last_id = $response['LASTID'];

	}
	if ($_POST['submit'] == 'update') {
	    unset($_POST['submit']);
	    $rid = $_POST['rid'];
	    unset($_POST['rid']);
	    $_POST['updated_date'] = date('Y-m-d H:i:s');
	    $_POST['updated_by'] = $_SESSION['userid'];
	    $url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post&fid=id";
	    $response = file_post_contents($url, $_POST, 'update');
	    // $response = json_decode($response, 1);
	    // print_r($response);
	    $last_id = $rid;

	}

    //  MULTIPLE FILE UPLOAD & SAVE


    if (!empty($_FILES['complaint_images']['name'][0])) {
        $transaction_id = $last_id; // last inserted ID from complaint save
        $table_name = 'application_attachments';
        $company_id = $_SESSION['company_id'];
        $user_id = $_SESSION['userid'];
        $upload_path = 'all'; // folder name inside /uploads/

        foreach ($_FILES['complaint_images']['name'] as $key => $original_name) {
            if (empty($original_name)) continue;
            // Create a temporary $_FILES array for this file
            $_FILES['single_file']['name'] = $_FILES['complaint_images']['name'][$key];
            $_FILES['single_file']['type'] = $_FILES['complaint_images']['type'][$key];
            $_FILES['single_file']['tmp_name'] = $_FILES['complaint_images']['tmp_name'][$key];
            $_FILES['single_file']['error'] = $_FILES['complaint_images']['error'][$key];
            $_FILES['single_file']['size'] = $_FILES['complaint_images']['size'][$key];
            // Upload file
            $saved_file = imageupload($upload_path, 'single_file', '', 'complaint');
            $file_size_mb = round($_FILES['single_file']['size'] / (1024 * 1024), 2);
            // Save file info into application_attachments
            $file_data = array(
                'company_id'              => $company_id,
                'application_table_name'  => 'complaints',
                'transaction_id'          => $transaction_id,
                'temp_id'                 => '',
                'file_name'               => $saved_file,
                'actual_file_name'        => $original_name,
                'email_attachement'       => '0',
                'file_size_mb'            => $file_size_mb,
                'is_active'               => '0',
                'current_status'          => '0',
                'status_date'             => date('Y-m-d H:i:s'),
                'created_date'            => date('Y-m-d H:i:s'),
                'created_by'              => $user_id
            );
            $url_attach = API . "?mode=adddata&table=" . $table_name . "&method=post";
            $resp_attach = file_post_contents($url_attach, $file_data, 'insert');
        }
    }
    header("location:index.php?option=COM_LIST_AMC_COMPLAINTS");


}
$return = array();
if ($_GET['Rid']) {
	$rid = encodeId($_GET['Rid']);
	$rid = urldecode($rid);
	$rid = explode(':', $rid);
	$rid = $rid['1'];
	$table_name = 'complaints';
	$url = API . "?mode=singlerowtabledata&method=get&rid=$rid&table=$table_name";
	$che = callAPI($url);
	$return = json_decode($che, 1);
	$view  = $_GET['view'];
	$copy  = $_GET['copy'];
    // print_r($return);
}
$url = API . "?mode=getamccomplaintsdetails&method=get";
$result = callAPI($url);
$result = json_decode($result, 1);
// print_r($result['assets']);
$buildings = $items = $assets = array();
$buildings = $result['buildings'];
$items = $result['items'];
$assets = $result['assets'];
$locations = $result['locations'];
$contracts = $result['contracts'] ?? array();
?>

<!-- Page body start -->
<div class="page-body">
	<div class="card">
		<div class="card-header">
			<h5>AMC Complaint <?php echo $return['transaction_no'] != '' ? '('.$return['transaction_no'].')' : ''; ?></h5>
			<div class="card-header-right">
				<!-- <a href="index.php?option=COM_LIST_AMC_COMPLAINTS" class="btn btn-success mb-2 desktop-view">
					<i class="icofont icofont-plus icon-white "></i>
				AMC Complaints List</a> -->
				<a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
					<i class="icofont icofont-reply"></i>
					Back
				</a>
			</div>
		</div>
		<div class="card-infisuite mt-2">
			<div class="card-body p-4 ">
				<form id="complaintsForm" method="post" enctype="multipart/form-data">
					<input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['userid']; ?>">
					<div class="row">
						<div class="form-group col-lg-3 w-full">
							<label>Complaint priority <span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="complaint_priority" name="complaint_priority">
								<option value="0"> Select Any one </option>
								<option value="L" <?php echo $return['complaint_priority']=="L"? 'selected': '' ?>> Low</option>
								<option value="M" <?php echo $return['complaint_priority']=="M"? 'selected': '' ?>> Medium </option>
								<option value="H" <?php echo $return['complaint_priority']=="H"? 'selected': '' ?>> High </option>

							</select>
							<div style="display: none;color: red;" id="complaint_priority_err"></div>
						</div>
						<div class="col-lg-3 col-xs-6 form-group">
							<label>Date<span style="color:red;">*</span></label>
							<input class="form-control" type="date" name="transaction_date" id="transaction_date"   value="<?php echo isset($return['transaction_date']) && $return['transaction_date'] != '' ? date('Y-m-d', strtotime($return['transaction_date'])) :  date('Y-m-d'); ?>"/>
							<div style="display: none;color: red;" id="transaction_date_err"></div>
						</div>
						<div class="form-group col-lg-3 col-xs-6 ">
							<label>Complaint Name<span style="color:red;">*</span></label>
							<input class="form-control" placeholder="Enter Complaint Name" type="text" name="complaint_name" id="complaint_name" value="<?php echo $return['complaint_name'] ?? ''; ?>">
							<div style="display: none;color: red;" id="complaint_name_err"></div>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Complaint Type<span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="complaint_type" name="complaint_type" onchange="handleComplaintType()">
								<option value="0"> Select Any one </option>
								<option value="Item" <?php echo $return['complaint_type']=="Item"? 'selected': '' ?> >Item</option>
								<option value="Asset" <?php echo $return['complaint_type']=="Asset"? 'selected': '' ?> >Asset</option>
							</select>
							<div style="display: none;color: red;" id="complaint_type_err"></div>
						</div>

						<div class="col-md-3 w-full">
							<label class="form-label">Location <span class="text-danger">*</span></label>
							<select class="form-select js-example-basic-single" id="location_id" name="location_id" onchange="onLocationChange(this.value)">
								<option value="">Select Location</option>
								<?php foreach ($locations as $loc): ?>
								<option value="<?= $loc['id'] ?>" <?= ($return && $return['location_id']==$loc['id'])?'selected':''; ?>><?= htmlspecialchars($loc['short_name']) ?></option>
								<?php endforeach; ?>
							</select>
							<div style="display: none;color: red;" id="location_id_err"></div>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Building<span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="building_id" name="building_id">
								<option value="0"> Select Any one </option>

								<?php /* foreach ($buildings as $row): ?>
									<option value="<?php echo $row['id']; ?>" <?php echo $return['building_id']==$row['id']? 'selected': '' ?>  >
										<?php echo $row['building_name']; ?>
									</option>
								<?php endforeach; */?>
							</select>
							<div style="display: none;color: red;" id="building_id_err"></div>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Building Floor<span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="building_floor_id" name="building_floor_id">
								<option value="0"> Select Any one </option>
							</select>
							<div style="display: none;color: red;" id="building_floor_id_err"></div>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Building Floor Space<span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="building_floor_space_id" name="building_floor_space_id">
								<option value="0"> Select Any one </option>
							</select>
							<div style="display: none;color: red;" id="building_floor_space_id_err"></div>
						</div>
												<div class="form-group col-lg-3 w-full">
							<label>Asset<span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="asset_id" name="asset_id">
								<option value="0"> Select Any one </option>
								<?php foreach ($assets as $row): ?>
									<option value="<?php echo $row['id']; ?>"  <?php echo $return['asset_id']==$row['id']? 'selected': '' ?> >
										<?php echo ($row['asset_code'].'-'.$row['asset_description']); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div style="display: none;color: red;" id="asset_id_err"></div>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Item <span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="item_id" name="item_id">
								<option value="0"> Select Any one </option>
								<?php foreach ($items as $row): ?>
									<option value="<?php echo $row['id']; ?>" <?php echo $return['item_id']==$row['id']? 'selected': '' ?>  >
										<?php echo $row['item_name']; ?>
									</option>
								<?php endforeach; ?>

							</select>
							<div style="display: none;color: red;" id="item_id_err"></div>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>AMC Contract</label>
							<input type="hidden" name="contract_id" id="contract_id" value="<?php echo (int) ($return['contract_id'] ?? 0); ?>">
							<input type="text" class="form-control" id="contract_display" readonly value="<?php echo htmlspecialchars($return['contract_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Auto from asset + location">
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>ServiceNow Ref</label>
							<input type="text" class="form-control" name="servicenow_ref" id="servicenow_ref" value="<?php echo htmlspecialchars($return['servicenow_ref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Optional">
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Linked Schedule Visit</label>
							<select class="form-select js-example-basic-single" id="schedule_id" name="schedule_id">
								<option value="">None — Adhoc</option>
							</select>
						</div>
						<input type="hidden" name="source_type" id="source_type" value="<?php echo htmlspecialchars($return['source_type'] ?? 'adhoc', ENT_QUOTES, 'UTF-8'); ?>">

						<div class="col-lg-3 col-xs-6 form-group">
							<label>Remarks</label>
							<textarea class="form-control" placeholder="Enter Remarks" type="text" name="remarks" id="remarks"><?php echo $return['remarks'] ?? ''; ?></textarea>
						</div>

						<div class="col-lg-3 col-xs-6 form-group">
							<label>Complaint Image </label>
							<input class="form-control mb-1" type="file" name="complaint_images[]" id="complaint_images" multiple />
							<?php 
							if(!empty($rid)){
								$application_table_name  = 'complaints';
								$url = API."?mode=getImagesData&method=get&table=$application_table_name&id=$rid";
								$img_result = callAPI($url);
								$img_result = json_decode($img_result, 1);
								$img_result = $img_result['table'];
								foreach ($img_result as $row) {
									echo '<a href="uploads/all/'.$row['file_name'] . '" target="_blank" class="mx-2">
									<img src="uploads/all/'.$row['file_name'] . '" class="img-60 border" alt="">
									</a>';
								}
							}
							?>
						</div>
						
					</div>
					<div class="border-top">
						<?php if ($view != '1'):
							if ($rid != "" && $copy != '1') {  ?>
								<button name='submit' value='update' class="btn waves-effect waves-light btn-primary mt-2">
									Update
								</button>
								<input name="rid" type="hidden" value="<?php echo $rid; ?>">

							<?php } else {  ?>
								<button name='submit' value='insert' class="btn waves-effect waves-light btn-primary mt-2">
									Save
								</button>
							<?php }
						endif; ?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
		const preselectedLocationId = <?php echo json_encode($return['location_id']); ?>;
		const preselectedBuildingId = <?php echo json_encode($return['building_id']); ?>;
		const preselectedFloorId = <?php echo json_encode($return['building_floor_id']); ?>;
		const preselectedSpaceId = <?php echo json_encode($return['building_floor_space_id']); ?>;
	// Initialize the form with default visibility
	$(document).ready(function() {
		handleComplaintType(); // Set the initial visibility
		$("#complaint_type").change(handleComplaintType);

		$("#location_id, #asset_id").on('change', function () {
			resolveAmcComplaintContract();
		});

		$("#complaintsForm").submit(function(e) {
			if (!validateComplaintsForm()) {
				e.preventDefault(); // Prevent form submission if validation fails
			}
		});


		if (preselectedLocationId && preselectedLocationId != 0) {
		    onLocationChange(preselectedLocationId, preselectedBuildingId);

		    // After buildings load, auto select building
		    // setTimeout(() => {
		    //     $("#building_id").val(preselectedBuildingId).trigger('change');
		    // }, 300);
		}

	    // Handle building selection
		$("#building_id").change(function () {
			const buildingId = $(this).val();
			if (buildingId && buildingId !== "0") {
				loadBuildingFloors(buildingId);
			} else {
				$("#building_floor_id").empty().append('<option value="0"> Select Any one </option>');
				$("#building_floor_space_id").empty().append('<option value="0"> Select Any one </option>');
			}
		});

	    // Handle floor selection
		$("#building_floor_id").change(function () {
			const floorId = $(this).val();
			if (floorId && floorId !== "0") {
				loadFloorSpaces(floorId);
			} else {
				$("#building_floor_space_id").empty().append('<option value="0"> Select Any one </option>');
			}
		});
	});
		function onLocationChange(locId, preselectedBuildingId = 0) {
	        const buildingSel = $('#building_id');
	        buildingSel.empty().append ('<option value="">Select Building</option>');

	        if (!locId) return;
	        $.ajax({
	            url: "ajax.php",
	            type: "POST",
	            data: {
	                    page: 'getselectedlist',
	                    id: locId,
	                    field: 'location_id',
	                    table_name: 'buildings'
	                },
	            success: function (d) { 
	                // console.log("d", d);
	                try {
	                    if (d) {
	                        JSON.parse(d).forEach(function(r) {
	                        	var selected = (r.id == preselectedBuildingId) ? 'selected' : '';
	                            buildingSel.append('<option value="' + r.id + '"' +selected+'>' + r.building_name + '</option>');
	                        });
	                        // On page load, populate dropdowns if values are preselected
							if (preselectedBuildingId && preselectedBuildingId != "0") {
								loadBuildingFloors(preselectedBuildingId, preselectedFloorId);
							}
	                    }
	                } catch (e) {
	                    console.error("Invalid JSON from server", e, d);
	                }
	            } 
	        }); 
	        document.getElementById('building_floor_id').innerHTML = '<option value="">Select Floor</option>';
	        document.getElementById('building_floor_space_id').innerHTML = '<option value="">Select Space</option>'; 


	    }
	    	    // Function to populate floors based on building ID
		function loadBuildingFloors(buildingId, selectedFloorId) {
			$.ajax({
				url: 'ajax.php',
				type: 'POST',
				data: {
					page: 'gettableData',
					table: 'building_floors',
					id: buildingId,
					column: 'building_id',
				},
				success: function (response) {
					response = JSON.parse(response);
					const buildingFloorSelect = $("#building_floor_id");
					buildingFloorSelect.empty().append('<option value="0"> Select Any one </option>');
					const tableData = Object.values(response.table);
					if (tableData && tableData.length > 0) {
						tableData.forEach(function (floor) {
							buildingFloorSelect.append(
						`<option value="${floor.id}" ${selectedFloorId == floor.id ? 'selected' : ''}>${floor.floor_name}</option>`
						);
						});
						if (selectedFloorId) {
							loadFloorSpaces(selectedFloorId, preselectedSpaceId);
						}
					}
				},
				error: function (xhr, status, error) {
					console.error("Error fetching building floors:", error);
				},
			});
		}
			    // Function to populate spaces based on floor ID
		function loadFloorSpaces(floorId, selectedSpaceId) {
			$.ajax({
				url: 'ajax.php',
				type: 'POST',
				data: {
					page: 'gettableData',
					table: 'building_floor_spaces',
					id: floorId,
					column: 'building_floor_id',
				},
				success: function (response) {
					response = JSON.parse(response);
					const floorSpaceSelect = $("#building_floor_space_id");
					floorSpaceSelect.empty().append('<option value="0"> Select Any one </option>');
					const tableData = Object.values(response.table);
					if (tableData && tableData.length > 0) {
						tableData.forEach(function (space) {
							floorSpaceSelect.append(
						`<option value="${space.id}" ${selectedSpaceId == space.id ? 'selected' : ''}>${space.cabin_name}</option>`
						);
						});
					}
				},
				error: function (xhr, status, error) {
					console.error("Error fetching floor spaces:", error);
				},
			});
		}
	function handleComplaintType() {
		const complaintType = $("#complaint_type").val();
		// Hide all optional fields initially
		$("#item_id").closest(".form-group").hide();
		$("#asset_id").closest(".form-group").hide();
		if (complaintType === "Item") {
			$("#item_id").closest(".form-group").show();
		} else if (complaintType === "Asset") {
			$("#asset_id").closest(".form-group").show();
		}
	}

	function validateComplaintsForm() {
		let isValid = true;

		// Clear previous error messages
		$(".form-group div").hide();

		// Validate common mandatory fields
		const complaintType = $("#complaint_type").val();
		const complaintName = $("#complaint_name").val();
		const complaintPriority = $("#complaint_priority").val();
		const transactionDate = $("#transaction_date").val();

		if (!complaintType || complaintType === "0") {
			$("#complaint_type_err").text("Please select a complaint type").show();
			isValid = false;
		}
		if (!complaintName) {
			$("#complaint_name_err").text("Please enter the complaint name").show();
			isValid = false;
		}
		if (!complaintPriority || complaintPriority === "0") {
			$("#complaint_priority_err").text("Please select a complaint priority").show();
			isValid = false;
		}
		if (!transactionDate) {
			$("#transaction_date_err").text("Please select a transaction date").show();
			isValid = false;
		}

		// Validate fields specific to the complaint type
		if (complaintType === "Item") {
			const itemId = $("#item_id").val();
			if (!itemId || itemId === "0") {
				$("#item_id_err").text("Please select an item").show();
				isValid = false;
			}
			
		} else if (complaintType === "Asset") {
			const assetId = $("#asset_id").val();
			if (!assetId || assetId === "0") {
				$("#asset_id_err").text("Please select an asset").show();
				isValid = false;
			}

		}
        const locationId = $("#location_id").val();
        const buildingId = $("#building_id").val();
        const buildingFloorId = $("#building_floor_id").val();
        const buildingFloorSpaceId = $("#building_floor_space_id").val();

        if (!locationId || locationId === "0") {
            $("#location_id_err").text("Please select a Location").show();
            isValid = false;
        }
        if (!buildingId || buildingId === "0") {
            $("#building_id_err").text("Please select a Building").show();
            isValid = false;
        }
        if (!buildingFloorId || buildingFloorId === "0") {
            $("#building_floor_id_err").text("Please select a Building Floor").show();
            isValid = false;
        }
        if (!buildingFloorSpaceId || buildingFloorSpaceId === "0") {
            $("#building_floor_space_id_err").text("Please select a Building Floor Space").show();
            isValid = false;
        }
			
		

		return isValid;
	}

	function resolveAmcComplaintContract() {
		var locId = $('#location_id').val();
		var assetId = $('#asset_id').val();
		if (!locId || !assetId || assetId == '0') {
			$('#contract_id').val('');
			$('#contract_display').val('');
			$('#schedule_id').empty().append('<option value="">None — Adhoc</option>');
			return;
		}
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: { page: 'getamccontractfromassetlocation', location_id: locId, asset_id: assetId },
			success: function (resp) {
				var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
				if (r.found && r.contract) {
					$('#contract_id').val(r.contract.contract_id || '');
					$('#contract_display').val(r.contract.contract_name || '');
					loadComplaintSchedules(r.contract.contract_id, assetId);
				} else {
					$('#contract_id').val('');
					$('#contract_display').val('');
					$('#schedule_id').empty().append('<option value="">None — Adhoc</option>');
				}
			}
		});
	}
	function loadComplaintSchedules(contractId, assetId) {
		if (!contractId || !assetId) return;
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: { page: 'getamccontractschedulesforasset', contract_id: contractId, asset_id: assetId },
			success: function (resp) {
				var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
				var $sel = $('#schedule_id');
				var pre = <?php echo json_encode($return['schedule_id'] ?? ''); ?>;
				$sel.empty().append('<option value="">None — Adhoc</option>');
				if (r.schedules && r.schedules.length) {
					r.schedules.forEach(function (s) {
						var label = (s.planned_display || s.id) + ' (' + (s.status_label || '') + ')';
						$sel.append('<option value="' + s.id + '"' + (pre == s.id ? ' selected' : '') + '>' + label + '</option>');
					});
				}
				if ($sel.hasClass('select2-hidden-accessible')) {
					$sel.trigger('change.select2');
				}
			}
		});
	}

</script>