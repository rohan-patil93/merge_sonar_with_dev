<?php
$_SESSION['apply_js']['multiselest'] = 1;

$admin_type = 1;
if ($_SESSION['working_role_short_code'] == 'FIELD_EMPLOYEE') {
	$admin_type = 3;
}
if ($_SESSION['working_role_short_code'] == 'FIELD_MANAGER') {
	$admin_type = 2;
}

$today = date('Y-m-d');
$filter_url = API . "?mode=getfieldworkoperationsreport&method=get&filters_only=Y&admin_type=" . $admin_type;
$filter_res = json_decode(callAPI($filter_url), true);
$users = isset($filter_res['users']) ? $filter_res['users'] : array();
?>
<style>
	.fwor-summary { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
	.fwor-chip {
		background: #fff;
		border: 1px solid #e5e7eb;
		border-radius: 8px;
		padding: 10px 14px;
		min-width: 120px;
	}
	.fwor-chip-label { display: block; font-size: 12px; color: #6b7280; }
	.fwor-chip-value { display: block; font-size: 20px; font-weight: 700; color: #111827; }
	.fwor-chip-planned .fwor-chip-value { color: #0d6efd; }
	.fwor-chip-visited .fwor-chip-value { color: #198754; }
	.fwor-chip-pending .fwor-chip-value { color: #dc3545; }
	.fwor-chip-extra .fwor-chip-value { color: #0dcaf0; }
	.fwor-status-badge {
		display: inline-block;
		padding: 2px 8px;
		border-radius: 999px;
		font-size: 12px;
		font-weight: 600;
	}
	.fwor-status-visited { background: #d1e7dd; color: #0f5132; }
	.fwor-status-pending { background: #f8d7da; color: #842029; }
	.fwor-status-extra { background: #cff4fc; color: #055160; }
	.fwor-map-link { color: #0d6efd; font-size: 16px; }
	.fwor-map-disabled { color: #adb5bd; cursor: default; }
</style>
<div class="page-body">
	<div class="card">
		<div class="card-header">
			<h5>Field Work Operations Report</h5>
			<div class="card-header-right">
				<div class="dropdown-primary dropdown open">
					<button class="btn btn-primary dropdown-toggle waves-light" type="button" id="fieldWorkOperationsReportDownload" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
					<div class="dropdown-menu p-0" aria-labelledby="fieldWorkOperationsReportDownload" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
						<a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Field Work Operations Report" target="_blank">CSV</a>
					</div>
				</div>
				<a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
					<i class="icofont icofont-reply"></i>
					Back
				</a>
			</div>
		</div>
		<div class="card-body p-4">
			<div class="row">
				<?php if ($admin_type != '3') { ?>
				<div class="col-lg-3 col-md-6 form-group">
					<label>Users</label>
					<select class="form-control js-example-basic-multiple" id="user_id" name="user_id[]" multiple="multiple" style="width:100%">
						<?php if (count((array)$users) > 0) {
							foreach ($users as $user_row) {
								$uid = is_array($user_row) ? $user_row['id'] : $user_row->id;
								$uname = is_array($user_row) ? $user_row['display_name'] : $user_row->display_name;
						?>
							<option value="<?php echo $uid; ?>"><?php echo htmlspecialchars($uname); ?></option>
						<?php }
						} ?>
					</select>
				</div>
				<?php } else { ?>
				<input type="hidden" id="user_id" name="user_id" value="<?php echo (int)$_SESSION['userid']; ?>">
				<?php } ?>

				<div class="col-lg-3 col-md-6 form-group">
					<label>From Date <span class="text-danger">*</span></label>
					<input class="form-control" type="date" name="from_date" id="from_date" value="<?php echo $today; ?>">
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>To Date <span class="text-danger">*</span></label>
					<input class="form-control" type="date" name="to_date" id="to_date" value="<?php echo $today; ?>">
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>Status</label>
					<select class="form-control js-example-basic-multiple" id="status" name="status[]" multiple="multiple" style="width:100%">
						<option value="Visited">Visited</option>
						<option value="Pending">Pending</option>
						<option value="Extra">Extra</option>
					</select>
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>&nbsp;</label>
					<button type="button" class="btn btn-primary form-control" id="getFieldWorkOperationsReportBtn">Search</button>
				</div>
			</div>
			<div class="row mt-3 custom-scroll" id="Tablecontent" style="height:500px"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function toast(msg) {
		tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
			'animated flipInX', 'animated flipOutX', msg, '');
	}

	function fworFilterIds(val) {
		if (val == null || val == '') {
			return [];
		}
		if (!$.isArray(val)) {
			val = [val];
		}
		return val.filter(function (id) {
			return id != null && id != '' && id != '0';
		});
	}

	function loadFieldWorkOperationsReport() {
		var fromDate = $("#from_date").val();
		var toDate = $("#to_date").val();

		if (fromDate == "") {
			toast("Please select From Date");
			return;
		}
		if (toDate == "") {
			toast("Please select To Date");
			return;
		}
		if (toDate < fromDate) {
			toast("To Date should be greater than or equal to From Date");
			return;
		}

		var userIds = [];
		<?php if ($admin_type != '3') { ?>
		userIds = fworFilterIds($("#user_id").val());
		<?php } else { ?>
		userIds = [String($("#user_id").val())];
		<?php } ?>

		var statuses = fworFilterIds($("#status").val());
		var loader = '<div class="col-12 text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
		$("#Tablecontent").html(loader);

		$.ajax({
			url: "views/custom_list_field_work_operations_report_ajax.php",
			type: "POST",
			data: {
				from_date: fromDate,
				to_date: toDate,
				user_id: userIds,
				status: statuses,
				admin_type: "<?php echo (int)$admin_type; ?>"
			},
			success: function (response) {
				$("#Tablecontent").html(response);
			},
			error: function () {
				$("#Tablecontent").html('<div class="col-12"><p class="text-danger mb-0">Unable to load Field Work Operations Report.</p></div>');
			}
		});
	}

	$(document).ready(function () {
		$(".js-example-basic-multiple").select2({
			placeholder: "All",
			allowClear: true,
			width: "100%"
		});

		$("#from_date").on("change", function () {
			var fromDate = $(this).val();
			$("#to_date").attr("min", fromDate);
			if ($("#to_date").val() != "" && $("#to_date").val() < fromDate) {
				$("#to_date").val(fromDate);
			}
		});

		$("#getFieldWorkOperationsReportBtn").on("click", function () {
			loadFieldWorkOperationsReport();
		});

		loadFieldWorkOperationsReport();
	});
</script>
