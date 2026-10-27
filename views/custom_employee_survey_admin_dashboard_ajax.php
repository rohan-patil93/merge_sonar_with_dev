<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$category = array('1' => 'Survey', '2' => 'Poll');
$status_filter = isset($_POST['status_filter']) ? trim($_POST['status_filter']) : 'assigned';
$company_id = isset($_POST['company_id']) ? (int)$_POST['company_id'] : (int)($_SESSION['company_id'] ?? 0);
$emp_id = isset($_POST['emp_id']) && $_POST['emp_id'] != "" ? $_POST['emp_id'] : ($_SESSION['emp_id'] ?? '');
$cat = isset($_POST['category']) ? $_POST['category'] : '1';
if($cat == ""){
	$cat = '1';
}
$display_tag = $category[$cat];
$emp_id_encoded = deocdeId("emp_id:" . $emp_id);

$url = API . "?mode=getemployeesurveydashboard&method=get"
	. "&company_id=" . urlencode($company_id)
	. "&emp_id=" . urlencode($emp_id)
	. "&category=" . urlencode($cat)
	. "&status_filter=" . urlencode($status_filter)
	. "&response_mode=table";

$apiRes = callAPI($url);
$result = json_decode($apiRes, true);
$result = is_array($result) ? $result : array();
$rows = isset($result['rows']) ? $result['rows'] : array();

if(count((array)$rows) > 0) {
?>
<div class="table-responsive survey_dashboard">
	<table class="table table-bordered table-sm">
		<tr bgcolor="#eeeeee">
			<th><?php echo $display_tag; ?> Name</th>
			<th>Category</th>
			<th>Start Date</th>
			<th>End Date</th>
			<th>Status</th>
			<th>Action</th>
		</tr>
		<?php foreach($rows as $surveyRow) {
			$surveyRow = is_array($surveyRow) ? $surveyRow : (array)$surveyRow;
			$surveyId = deocdeId("id:" . $surveyRow['survey_id']);
			$statusColor = $surveyRow['status_color'] != "" ? $surveyRow['status_color'] : 'secondary';
			if($statusColor == 'muted'){
				$statusColor = 'secondary';
			}
			$responseUrl = "index.php?option=COM_INSERT_SURVEY_RESPONSE&Rid=" . $surveyId . "&CAT=" . $cat;
			$resultUrl = "index.php?option=COM_VIEW_SURVEY_RESULT&Pid=" . $surveyId . "&Eid=" . $emp_id_encoded . "&CAT=" . $cat;
		?>
		<tr>
			<td>
				<div class="row align-items-center feed-card">
					<div class="col-auto p-r-0">
						<div class="p-2 bg-green-100 b-radius-10 text-center left-icon-width text-success">
							<i class="fa fa-laptop f-w-600"></i>
						</div>
					</div>
					<div class="col">
						<h6 class="m-b-5"><?php echo htmlspecialchars($surveyRow['survey_name']); ?></h6>
					</div>
				</div>
			</td>
			<td><?php echo htmlspecialchars($surveyRow['category_label']); ?></td>
			<td><?php echo htmlspecialchars($surveyRow['survey_start_date']); ?></td>
			<td><?php echo htmlspecialchars($surveyRow['survey_end_date']); ?></td>
			<td>
				<button class="btn btn-sm card-info text-<?php echo $statusColor; ?> f-w-600 f-12 btn-outline-<?php echo $statusColor; ?> b-radius-5 mb-1"><?php echo htmlspecialchars($surveyRow['status_label']); ?></button>
			</td>
			<td>
				<?php if($surveyRow['action'] == 'view_result') { ?>
				<a href="<?php echo $resultUrl; ?>" class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
					<i class="fa fa-eye m-r-5"></i> <?php echo htmlspecialchars($surveyRow['action_label']); ?>
				</a>
				<?php } else if($surveyRow['action'] == 'expired') { ?>
				<button type="button" class="btn btn-sm btn-warning f-w-600 f-12 p-2 b-radius-5 mb-1" disabled><?php echo htmlspecialchars($surveyRow['action_label']); ?></button>
				<?php } else { ?>
				<a href="<?php echo $responseUrl; ?>" class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
					<i class="fa fa-play m-r-5"></i> <?php echo htmlspecialchars($surveyRow['action_label']); ?>
				</a>
				<?php } ?>
				<?php /*
				$curStatus = $surveyRow['current_status'] != "" ? $surveyRow['current_status'] : ST_ACTIVE;
				if($curStatus == ST_ACTIVE) { ?>
				<a href="<?php echo $responseUrl; ?>" class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
					<i class="fa fa-edit m-r-5"></i> Edit
				</a>
				<?php } else { ?>
				<a href="<?php echo $resultUrl; ?>" class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
					<i class="fa fa-eye m-r-5"></i> View
				</a>
				<?php }  */?>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>
<?php } else { ?>
<div class="card-px text-center py-20 mt-2">
	<p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No <?php echo $display_tag; ?>s Found</p>
	<div class="text-center px-4">
		<img class="mw-100" style="max-height: 400px;" alt="" src="assets/images/work.png">
	</div>
</div>
<?php } ?>
