<?php
$_SESSION['apply_js']['multiselect'] = 1;

$filter_url = API . "?mode=getoutwardcouriersreport&method=get&filters_only=Y";
$filter_res = json_decode(callAPI($filter_url), true);
$locations = isset($filter_res['locations']) ? $filter_res['locations'] : array();
$statuses = isset($filter_res['statuses']) ? $filter_res['statuses'] : array();
$today = date('Y-m-d');
?>
<div class="page-body">
	<div class="card">
		<div class="card-header">
			<h5>Outward Couriers Report</h5>
			<div class="card-header-right">
				<div class="dropdown-primary dropdown open">
					<button class="btn btn-primary dropdown-toggle waves-light" type="button" id="outwardCouriersReportDownload" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
					<div class="dropdown-menu p-0" aria-labelledby="outwardCouriersReportDownload" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
						<a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Outward Couriers Report" target="_blank">CSV</a>
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
					<label>From Date <span class="text-danger">*</span></label>
					<input class="form-control" type="date" name="from_date" id="from_date" value="<?php echo $today; ?>">
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>To Date <span class="text-danger">*</span></label>
					<input class="form-control" type="date" name="to_date" id="to_date" value="<?php echo $today; ?>">
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>Location</label>
					<select class="form-control js-example-basic-single" id="location_id" name="location_id" style="width:100%">
						<option value="">All Locations</option>
						<?php if(count((array)$locations) > 0){
							foreach($locations as $loc_row){
								$lid = is_array($loc_row) ? $loc_row['id'] : $loc_row->id;
								$lname = is_array($loc_row) ? $loc_row['location_name'] : $loc_row->location_name;
						?>
								<option value="<?php echo $lid; ?>"><?php echo htmlspecialchars($lname); ?></option>
						<?php }
						} ?>
					</select>
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>Tracking No</label>
					<input class="form-control" type="text" name="tracking_no" id="tracking_no" value="">
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>Courier Company</label>
					<input class="form-control" type="text" name="courier_company" id="courier_company" value="">
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>Taken By</label>
					<input class="form-control" type="text" name="taken_by" id="taken_by" placeholder="Taken by name" value="">
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>Status</label>
					<select class="form-control js-example-basic-single" id="current_status" name="current_status" style="width:100%">
						<option value="">All</option>
						<?php if(count((array)$statuses) > 0){
							foreach($statuses as $status_row){
								$sid = is_array($status_row) ? $status_row['status'] : $status_row->status;
								$sname = is_array($status_row) ? $status_row['name'] : $status_row->name;
						?>
								<option value="<?php echo $sid; ?>"><?php echo htmlspecialchars($sname); ?></option>
						<?php }
						} ?>
					</select>
				</div>
				<div class="col-lg-3 col-md-6 form-group">
					<label>&nbsp;</label>
					<button type="button" class="btn btn-primary form-control" id="getOutwardCouriersReportBtn">Search</button>
				</div>
			</div>
			<div class="row mt-3 custom-scroll" id="tableContent" style="height:500px">
				<div class="col-12">
					<p class="text-muted mb-0">Select filters and click Search to view outward couriers.</p>
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

		$("#getOutwardCouriersReportBtn").on("click", function () {
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
				url: "views/custom_list_outward_couriers_report_ajax.php",
				type: "POST",
				data: {
					from_date: fromDate,
					to_date: toDate,
					location_id: $("#location_id").val(),
					tracking_no: $("#tracking_no").val(),
					courier_company: $("#courier_company").val(),
					taken_by: $("#taken_by").val(),
					current_status: $("#current_status").val()
				},
				success: function (response) {
					$("#tableContent").html(response);
				},
				error: function () {
					$("#tableContent").html('<div class="col-12"><p class="text-danger mb-0">Unable to load outward couriers report.</p></div>');
				}
			});
		});
	});
</script>
