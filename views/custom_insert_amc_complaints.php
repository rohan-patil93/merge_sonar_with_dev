<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit'])){
	$table_name = 'complaints';
    $last_id = '';
	if ($_POST['submit'] == 'insert') {
		unset($_POST['submit']);
		$data = $_POST;
		$data['complaint_type'] = 'Asset';
		$data['item_id'] = '';
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
	    $_POST['complaint_type'] = 'Asset';
	    $_POST['item_id'] = '';
	    $_POST['updated_date'] = date('Y-m-d H:i:s');
	    $_POST['updated_by'] = $_SESSION['userid'];
	    $url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post&fid=id";
	    $response = file_post_contents($url, $_POST, 'update');
	    $last_id = $rid;

	}

    if (!empty($_FILES['complaint_images']['name'][0])) {
        $transaction_id = $last_id;
        $table_name = 'application_attachments';
        $company_id = $_SESSION['company_id'];
        $user_id = $_SESSION['userid'];
        $upload_path = 'all';

        foreach ($_FILES['complaint_images']['name'] as $key => $original_name) {
            if (empty($original_name)) continue;
            $_FILES['single_file']['name'] = $_FILES['complaint_images']['name'][$key];
            $_FILES['single_file']['type'] = $_FILES['complaint_images']['type'][$key];
            $_FILES['single_file']['tmp_name'] = $_FILES['complaint_images']['tmp_name'][$key];
            $_FILES['single_file']['error'] = $_FILES['complaint_images']['error'][$key];
            $_FILES['single_file']['size'] = $_FILES['complaint_images']['size'][$key];
            $saved_file = imageupload($upload_path, 'single_file', '', 'complaint');
            $file_size_mb = round($_FILES['single_file']['size'] / (1024 * 1024), 2);
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
}
$url = API . "?mode=getamccomplaintsdetails&method=get&admin_type=".$_SESSION['admin_type'];
$result = callAPI($url);
$result = json_decode($result, 1);
$contracts = $result['contracts'] ?? array();
?>

<!-- Page body start -->
<div class="page-body">
	<div class="card">
		<div class="card-header">
			<h5>AMC Complaint <?php echo $return['transaction_no'] != '' ? '('.$return['transaction_no'].')' : ''; ?></h5>
			<div class="card-header-right">
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
					<input type="hidden" name="complaint_type" id="complaint_type" value="Asset">
					<input type="hidden" name="source_type" id="source_type" value="<?php echo htmlspecialchars($return['source_type'] ?? 'adhoc', ENT_QUOTES, 'UTF-8'); ?>">
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
							<input class="form-control" type="date" name="transaction_date" id="transaction_date" value="<?php echo isset($return['transaction_date']) && $return['transaction_date'] != '' ? date('Y-m-d', strtotime($return['transaction_date'])) :  date('Y-m-d'); ?>"/>
							<div style="display: none;color: red;" id="transaction_date_err"></div>
						</div>
						<div class="form-group col-lg-3 col-xs-6 ">
							<label>Complaint Name<span style="color:red;">*</span></label>
							<input class="form-control" placeholder="Enter Complaint Name" type="text" name="complaint_name" id="complaint_name" value="<?php echo $return['complaint_name'] ?? ''; ?>">
							<div style="display: none;color: red;" id="complaint_name_err"></div>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Approved Contract<span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="contract_id" name="contract_id">
								<option value="0"> Select Any one </option>
								<?php if (count((array)$contracts) > 0) {
									foreach ($contracts as $row) {
										$contractRow = is_array($row) ? $row : (array) $row;
										$vendorSuffix = !empty($contractRow['vendor_name']) ? ' (' . $contractRow['vendor_name'] . ')' : '';
									?>
									<option value="<?php echo (int) $contractRow['id']; ?>" <?php echo (int) ($return['contract_id'] ?? 0) == (int) $contractRow['id'] ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($contractRow['contract_name'] . $vendorSuffix, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php }
								} ?>
							</select>
							<div style="display: none;color: red;" id="contract_id_err"></div>
						</div>
						<div class="col-md-3 w-full">
							<label class="form-label">Location <span class="text-danger">*</span></label>
							<select class="form-select js-example-basic-single" id="location_id" name="location_id">
								<option value="">Select Location</option>
							</select>
							<div style="display: none;color: red;" id="location_id_err"></div>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Asset<span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="asset_id" name="asset_id">
								<option value="0"> Select Any one </option>
							</select>
							<div style="display: none;color: red;" id="asset_id_err"></div>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Linked Schedule Visit</label>
							<select class="form-select js-example-basic-single" id="schedule_id" name="schedule_id">
								<option value="">None — Adhoc</option>
							</select>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>ServiceNow Ref</label>
							<input type="text" class="form-control" name="servicenow_ref" id="servicenow_ref" value="<?php echo htmlspecialchars($return['servicenow_ref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Optional">
						</div>
						<div class="form-group col-lg-3 w-full">
							<label>Building<span style="color:red;">*</span></label>
							<select class="form-select js-example-basic-single" id="building_id" name="building_id">
								<option value="0"> Select Any one </option>
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
								if (count((array)$img_result) > 0) {
									foreach ($img_result as $row) {
										echo '<a href="uploads/all/'.$row['file_name'] . '" target="_blank" class="mx-2">
										<img src="uploads/all/'.$row['file_name'] . '" class="img-60 border" alt="">
										</a>';
									}
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
	var complaintContractAssets = [];
	var complaintContractLocations = [];
	var complaintBootstrapInProgress = false;

	const preselectedContractId = <?php echo json_encode((int) ($return['contract_id'] ?? 0)); ?>;
	const preselectedLocationId = <?php echo json_encode((int) ($return['location_id'] ?? 0)); ?>;
	const preselectedAssetId = <?php echo json_encode((int) ($return['asset_id'] ?? 0)); ?>;
	const preselectedScheduleId = <?php echo json_encode($return['schedule_id'] ?? ''); ?>;
	const preselectedBuildingId = <?php echo json_encode((int) ($return['building_id'] ?? 0)); ?>;
	const preselectedFloorId = <?php echo json_encode((int) ($return['building_floor_id'] ?? 0)); ?>;
	const preselectedSpaceId = <?php echo json_encode((int) ($return['building_floor_space_id'] ?? 0)); ?>;

	function complaintDestroySelect2($sel) {
		if ($sel.hasClass('select2-hidden-accessible')) {
			$sel.select2('destroy');
		}
	}

	function complaintInitSelect2($sel) {
		if (typeof $.fn.select2 == 'function' && $sel.length && !$sel.prop('disabled')) {
			$sel.select2({ width: '100%' });
		}
	}

	function complaintParseContractAssetsResponse(resp) {
		var r = resp;
		if (typeof resp == 'string') {
			try {
				r = JSON.parse(resp);
			} catch (e) {
				return { assets: [], locations: [] };
			}
		}
		if (r && r.assets) {
			return { assets: r.assets || [], locations: r.locations || [] };
		}
		if (r && r.data && (r.data.assets || r.data.locations)) {
			return { assets: r.data.assets || [], locations: r.data.locations || [] };
		}
		return { assets: [], locations: [] };
	}

	function complaintBuildLocationOptions(selectedId, locations) {
		selectedId = parseInt(selectedId, 10) || 0;
		var html = '<option value="">Select Location</option>';
		if (locations && locations.length) {
			locations.forEach(function (l) {
				var id = parseInt(l.location_id, 10) || 0;
				if (id <= 0) {
					return;
				}
				var label = l.location_label || ('Location ' + id);
				html += '<option value="' + id + '"' + (id == selectedId ? ' selected' : '') + '>' + $('<div>').text(label).html() + '</option>';
			});
		}
		return html;
	}

	function complaintBuildAssetOptions(locationId, selectedId, assets) {
		selectedId = parseInt(selectedId, 10) || 0;
		locationId = parseInt(locationId, 10) || 0;
		if (!locationId) {
			return '<option value="0">Select location first</option>';
		}
		var html = '<option value="0"> Select Any one </option>';
		var count = 0;
		if (assets && assets.length) {
			assets.forEach(function (a) {
				var locId = parseInt(a.location_id, 10) || 0;
				if (locId != locationId) {
					return;
				}
				count++;
				var id = parseInt(a.asset_id, 10) || 0;
				var label = ((a.asset_code || '') + '-' + (a.asset_description || a.asset_name || '')).trim();
				html += '<option value="' + id + '"' + (id == selectedId ? ' selected' : '') + '>' + $('<div>').text(label).html() + '</option>';
			});
		}
		if (count == 0) {
			return '<option value="0">No assets at this location</option>';
		}
		return html;
	}

	function complaintRefreshLocationAssetDropdowns(selectedLocationId, selectedAssetId) {
		var $loc = $('#location_id');
		var $asset = $('#asset_id');
		complaintDestroySelect2($loc);
		complaintDestroySelect2($asset);
		$loc.html(complaintBuildLocationOptions(selectedLocationId || 0, complaintContractLocations));
		$asset.html(complaintBuildAssetOptions(selectedLocationId || 0, selectedAssetId || 0, complaintContractAssets));
		complaintInitSelect2($loc);
		complaintInitSelect2($asset);
	}

	function complaintResetBuildingCascade() {
		$('#building_id').empty().append('<option value="0"> Select Any one </option>');
		$('#building_floor_id').empty().append('<option value="0"> Select Any one </option>');
		$('#building_floor_space_id').empty().append('<option value="0"> Select Any one </option>');
	}

	function complaintResetScheduleDropdown() {
		var $sel = $('#schedule_id');
		complaintDestroySelect2($sel);
		$sel.empty().append('<option value="">None — Adhoc</option>');
		complaintInitSelect2($sel);
	}

	function complaintLoadContractAssets(contractId, callback) {
		if (!contractId || contractId == '0') {
			complaintContractAssets = [];
			complaintContractLocations = [];
			complaintRefreshLocationAssetDropdowns(0, 0);
			complaintResetScheduleDropdown();
			complaintResetBuildingCascade();
			if (callback) {
				callback({ assets: [], locations: [] });
			}
			return;
		}
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			dataType: 'json',
			data: { page: 'getamcrepairordercontractassets', contract_id: contractId },
			success: function (resp) {
				var parsed = complaintParseContractAssetsResponse(resp);
				complaintContractAssets = parsed.assets;
				complaintContractLocations = parsed.locations;
				if (callback) {
					callback(parsed);
				}
			},
			error: function () {
				complaintContractAssets = [];
				complaintContractLocations = [];
				if (callback) {
					callback({ assets: [], locations: [] });
				}
			}
		});
	}

	function complaintOnContractChanged(contractId, selectedLocationId, selectedAssetId, onComplete) {
		complaintLoadContractAssets(contractId, function () {
			complaintRefreshLocationAssetDropdowns(selectedLocationId || 0, selectedAssetId || 0);
			if (!complaintBootstrapInProgress) {
				complaintResetBuildingCascade();
				complaintResetScheduleDropdown();
			}
			if (selectedLocationId && selectedLocationId != 0) {
				onLocationChange(selectedLocationId, preselectedBuildingId);
			}
			if (selectedAssetId && selectedAssetId != 0 && contractId && contractId != '0') {
				loadComplaintSchedules(contractId, selectedAssetId, preselectedScheduleId);
			}
			if (onComplete) {
				onComplete();
			}
		});
	}

	$(document).ready(function() {
		$("#complaintsForm").submit(function(e) {
			if (!validateComplaintsForm()) {
				e.preventDefault();
			}
		});

		$("#contract_id").on('change', function () {
			var contractId = $(this).val();
			complaintOnContractChanged(contractId, 0, 0);
		});

		$("#location_id").on('change', function () {
			var locId = $(this).val();
			var $asset = $('#asset_id');
			complaintDestroySelect2($asset);
			$asset.html(complaintBuildAssetOptions(locId, 0, complaintContractAssets));
			complaintInitSelect2($asset);
			complaintResetScheduleDropdown();
			if (!complaintBootstrapInProgress) {
				complaintResetBuildingCascade();
			}
			if (locId) {
				onLocationChange(locId, complaintBootstrapInProgress ? preselectedBuildingId : 0);
			} else {
				complaintResetBuildingCascade();
			}
		});

		$("#asset_id").on('change', function () {
			var assetId = $(this).val();
			var contractId = $('#contract_id').val();
			if (assetId && assetId != '0' && contractId && contractId != '0') {
				loadComplaintSchedules(contractId, assetId, complaintBootstrapInProgress ? preselectedScheduleId : '');
			} else {
				complaintResetScheduleDropdown();
			}
		});

		$("#building_id").change(function () {
			const buildingId = $(this).val();
			if (buildingId && buildingId !== "0") {
				loadBuildingFloors(buildingId);
			} else {
				$("#building_floor_id").empty().append('<option value="0"> Select Any one </option>');
				$("#building_floor_space_id").empty().append('<option value="0"> Select Any one </option>');
			}
		});

		$("#building_floor_id").change(function () {
			const floorId = $(this).val();
			if (floorId && floorId !== "0") {
				loadFloorSpaces(floorId);
			} else {
				$("#building_floor_space_id").empty().append('<option value="0"> Select Any one </option>');
			}
		});

		if (preselectedContractId && preselectedContractId != 0) {
			complaintBootstrapInProgress = true;
			complaintOnContractChanged(preselectedContractId, preselectedLocationId, preselectedAssetId, function () {
				complaintBootstrapInProgress = false;
			});
		}
	});

	function onLocationChange(locId, preselectedBuildingId = 0) {
		const buildingSel = $('#building_id');
		buildingSel.empty().append('<option value="">Select Building</option>');

		if (!locId) {
			return;
		}
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
				try {
					if (d) {
						JSON.parse(d).forEach(function(r) {
							var selected = (r.id == preselectedBuildingId) ? 'selected' : '';
							buildingSel.append('<option value="' + r.id + '"' + selected + '>' + r.building_name + '</option>');
						});
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
							'<option value="' + floor.id + '"' + (selectedFloorId == floor.id ? ' selected' : '') + '>' + floor.floor_name + '</option>'
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
							'<option value="' + space.id + '"' + (selectedSpaceId == space.id ? ' selected' : '') + '>' + space.cabin_name + '</option>'
						);
					});
				}
			},
			error: function (xhr, status, error) {
				console.error("Error fetching floor spaces:", error);
			},
		});
	}

	function validateComplaintsForm() {
		let isValid = true;
		$(".form-group div[id$='_err']").hide();

		const complaintName = $("#complaint_name").val();
		const complaintPriority = $("#complaint_priority").val();
		const transactionDate = $("#transaction_date").val();
		const contractId = $("#contract_id").val();
		const assetId = $("#asset_id").val();
		const locationId = $("#location_id").val();
		const buildingId = $("#building_id").val();
		const buildingFloorId = $("#building_floor_id").val();
		const buildingFloorSpaceId = $("#building_floor_space_id").val();

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
		if (!contractId || contractId === "0") {
			$("#contract_id_err").text("Please select an approved contract").show();
			isValid = false;
		}
		if (!locationId || locationId === "0") {
			$("#location_id_err").text("Please select a Location").show();
			isValid = false;
		}
		if (!assetId || assetId === "0") {
			$("#asset_id_err").text("Please select an asset").show();
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

	function loadComplaintSchedules(contractId, assetId, preselectedScheduleId) {
		if (!contractId || !assetId) {
			return;
		}
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: { page: 'getamccontractschedulesforasset', contract_id: contractId, asset_id: assetId },
			success: function (resp) {
				var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
				var $sel = $('#schedule_id');
				var pre = preselectedScheduleId || '';
				complaintDestroySelect2($sel);
				$sel.empty().append('<option value="">None — Adhoc</option>');
				if (r.schedules && r.schedules.length) {
					r.schedules.forEach(function (s) {
						var label = (s.planned_display || s.id) + ' (' + (s.status_label || '') + ')';
						$sel.append('<option value="' + s.id + '"' + (pre == s.id ? ' selected' : '') + '>' + label + '</option>');
					});
				}
				complaintInitSelect2($sel);
			}
		});
	}
</script>
