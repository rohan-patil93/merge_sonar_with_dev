<?php
$_SESSION['apply_js']['multiselest'] = 1;

$admin_type = 1;
$role_code = isset($_SESSION['working_role_short_code']) ? $_SESSION['working_role_short_code'] : '';
if ($role_code == 'EMPLOYEE' || $role_code == 'FIELD_EMPLOYEE') {
	$admin_type = 3;
}
if ($role_code == 'MANAGER' || $role_code == 'FIELD_MANAGER') {
	$admin_type = 2;
}

$today = date('Y-m-d');
$filter_url = API . "?mode=getempexpensesreport&method=get&filters_only=Y&admin_type=" . $admin_type;
$filter_res = json_decode(callAPI($filter_url), true);
$employees = isset($filter_res['employees']) ? $filter_res['employees'] : array();

$self_emp_id = '';
if ($admin_type == '3') {
	if (isset($_SESSION['emp_id']) && $_SESSION['emp_id'] != '') {
		$self_emp_id = (int)$_SESSION['emp_id'];
	} elseif (count((array)$employees) > 0) {
		$first = $employees[0];
		$self_emp_id = is_array($first) ? (int)$first['id'] : (int)$first->id;
	}
}
?>
<style>
	.eer-summary { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
	.eer-chip {
		background: #fff;
		border: 1px solid #e5e7eb;
		border-radius: 8px;
		padding: 10px 14px;
		min-width: 130px;
	}
	.eer-chip-label { display: block; font-size: 12px; color: #6b7280; }
	.eer-chip-value { display: block; font-size: 18px; font-weight: 700; color: #111827; }
	.eer-chip-claims .eer-chip-value { color: #0d6efd; }
	.eer-chip-lines .eer-chip-value { color: #6f42c1; }
	.eer-chip-total .eer-chip-value { color: #198754; }
	.eer-chip-tax .eer-chip-value { color: #fd7e14; }
	.eer-chip-pending .eer-chip-value { color: #dc3545; }
	.eer-status-badge {
		display: inline-block;
		padding: 2px 8px;
		border-radius: 999px;
		font-size: 12px;
		font-weight: 600;
	}
	.eer-status-draft { background: #fff3cd; color: #664d03; }
	.eer-status-active { background: #cfe2ff; color: #084298; }
	.eer-status-approved { background: #d1e7dd; color: #0f5132; }
	.eer-bill-link { color: #0d6efd; font-size: 16px; }
	.eer-bill-disabled { color: #adb5bd; }
</style>
<div class="page-body">
	<div class="card">
		<div class="card-header">
			<h5>Employee Expenses Report</h5>
			<div class="card-header-right">
				<div class="dropdown-primary dropdown open">
					<button class="btn btn-primary dropdown-toggle waves-light" type="button" id="empExpensesReportDownload" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
					<div class="dropdown-menu p-0" aria-labelledby="empExpensesReportDownload" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
						<a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Employee Expenses Report" target="_blank">CSV</a>
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
					<label>Employees</label>
					<select class="form-control js-example-basic-multiple" id="emp_id" name="emp_id[]" multiple="multiple" style="width:100%">
						<?php if (count((array)$employees) > 0) {
							foreach ($employees as $emp_row) {
								$eid = is_array($emp_row) ? $emp_row['id'] : $emp_row->id;
								$ename = is_array($emp_row) ? $emp_row['display_name'] : $emp_row->display_name;
						?>
							<option value="<?php echo $eid; ?>"><?php echo htmlspecialchars($ename); ?></option>
						<?php }
						} ?>
					</select>
				</div>
				<?php } else { ?>
				<input type="hidden" id="emp_id" name="emp_id" value="<?php echo (int)$self_emp_id; ?>">
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
						<option value="2">Draft</option>
						<option value="0">Active</option>
						<option value="199">Approved</option>
					</select>
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>Expense Type</label>
					<select class="form-control js-example-basic-multiple" id="expense_type" name="expense_type[]" multiple="multiple" style="width:100%">
						<option value="0">Expense</option>
						<option value="1">Mileage</option>
						<option value="2">Per Diem</option>
					</select>
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>&nbsp;</label>
					<button type="button" class="btn btn-primary form-control" id="getEmpExpensesReportBtn">Search</button>
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

	function eerFilterIds(val) {
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

	function loadEmpExpensesReport() {
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

		var empIds = [];
		<?php if ($admin_type != '3') { ?>
		empIds = eerFilterIds($("#emp_id").val());
		<?php } else { ?>
		empIds = [String($("#emp_id").val())];
		<?php } ?>

		var statuses = eerFilterIds($("#status").val());
		var expenseTypes = eerFilterIds($("#expense_type").val());
		var loader = '<div class="col-12 text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
		$("#Tablecontent").html(loader);

		$.ajax({
			url: "views/custom_list_emp_expenses_report_ajax.php",
			type: "POST",
			data: {
				from_date: fromDate,
				to_date: toDate,
				emp_id: empIds,
				status: statuses,
				expense_type: expenseTypes,
				admin_type: "<?php echo (int)$admin_type; ?>"
			},
			success: function (response) {
				$("#Tablecontent").html(response);
			},
			error: function () {
				$("#Tablecontent").html('<div class="col-12"><p class="text-danger mb-0">Unable to load Employee Expenses Report.</p></div>');
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

		$("#getEmpExpensesReportBtn").on("click", function () {
			loadEmpExpensesReport();
		});

		loadEmpExpensesReport();
	});
</script>
