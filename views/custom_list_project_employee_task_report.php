<?php
$_SESSION['apply_js']['multiselect'] = 1;

$filter_url = API . "?mode=getprojectemployeetaskreport&method=get&filters_only=Y";
$filter_res = json_decode(callAPI($filter_url), true);
$projects = isset($filter_res['projects']) ? $filter_res['projects'] : array();
$employees = isset($filter_res['employees']) ? $filter_res['employees'] : array();
?>
<div class="page-body">
	<div class="card">
		<div class="card-header">
			<h5>Project Employee Task Report</h5>
			<div class="card-header-right">
				<div class="dropdown-primary dropdown open">
					<button class="btn btn-primary dropdown-toggle waves-light" type="button" id="projectEmpTaskReportDownload" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
					<div class="dropdown-menu p-0" aria-labelledby="projectEmpTaskReportDownload" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
						<a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Project Employee Task Report" target="_blank">CSV</a>
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
				<div class="col-lg-3 col-md-6 form-group">
					<label>Project</label>
					<select class="form-control js-example-basic-single" id="project_id" name="project_id" style="width:100%">
						<option value="">All Projects</option>
						<?php if(count((array)$projects) > 0){
							foreach($projects as $project_row){
								$pid = is_array($project_row) ? $project_row['id'] : $project_row->id;
								$pname = is_array($project_row) ? $project_row['project_name'] : $project_row->project_name;
						?>
								<option value="<?php echo $pid; ?>"><?php echo htmlspecialchars($pname); ?></option>
						<?php }
						} ?>
					</select>
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>Employee</label>
					<select class="form-control js-example-basic-single" id="employee_id" name="employee_id" style="width:100%">
						<option value="">All Employees</option>
						<?php if(count((array)$employees) > 0){
							foreach($employees as $employee_row){
								$eid = is_array($employee_row) ? $employee_row['id'] : $employee_row->id;
								$ename = is_array($employee_row) ? $employee_row['display_name'] : $employee_row->display_name;
						?>
								<option value="<?php echo $eid; ?>"><?php echo htmlspecialchars($ename); ?></option>
						<?php }
						} ?>
					</select>
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>From Date <span class="text-danger">*</span></label>
					<input class="form-control" type="date" name="from_date" id="from_date" value="">
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>To Date <span class="text-danger">*</span></label>
					<input class="form-control" type="date" name="to_date" id="to_date" value="">
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>&nbsp;</label>
					<button type="button" class="btn btn-primary form-control" id="getProjectEmployeeTaskReportBtn">Search</button>
				</div>
			</div>
			<div class="row mt-3 custom-scroll" id="tableContent" style="height:500px">
				<div class="col-12">
					<p class="text-muted mb-0">Select a date range and click Search to view project employee tasks.</p>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function toast(msg) {
		tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
			'animated flipInX', 'animated flipOutX', msg, '');
	}

	$(document).ready(function () {
		$(".js-example-basic-single").select2();

		$("#from_date").on("change", function () {
			var fromDate = $(this).val();
			$("#to_date").attr("min", fromDate);
			if($("#to_date").val() != "" && $("#to_date").val() < fromDate){
				$("#to_date").val(fromDate);
			}
		});

		$("#getProjectEmployeeTaskReportBtn").on("click", function () {
			var fromDate = $("#from_date").val();
			var toDate = $("#to_date").val();

			if(fromDate == ""){
				toast("Please select From Date");
				return;
			}
			if(toDate == ""){
				toast("Please select To Date");
				return;
			}
			if(toDate < fromDate){
				toast("To Date should be greater than or equal to From Date");
				return;
			}

			var loader = '<div class="col-12 text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
			$("#tableContent").html(loader);

			$.ajax({
				url: "views/custom_list_project_employee_task_report_ajax.php",
				type: "POST",
				data: {
					project_id: $("#project_id").val(),
					employee_id: $("#employee_id").val(),
					from_date: fromDate,
					to_date: toDate
				},
				success: function (response) {
					$("#tableContent").html(response);
					$("#tableContent").find(".csr-status-label").each(function () {
						var color = $(this).attr("data-color");
						var bg = $(this).attr("data-bg");
						if(color){ $(this).css("color", color); }
						if(bg){ $(this).css("background-color", bg); }
					});
				},
				error: function () {
					$("#tableContent").html('<div class="col-12"><p class="text-danger mb-0">Unable to load project employee task report.</p></div>');
				}
			});
		});
	});
</script>
