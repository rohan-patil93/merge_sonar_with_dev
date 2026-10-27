<?php
// echo '<pre>'; print_r($_SESSION);
$category = array('1' => 'Survey', '2' => 'Poll');
if($_GET['CAT'] == ""){
	$_GET['CAT'] = 1;
}
$display_tag = $category[$_GET['CAT']];
$display_tag_lower = strtolower($display_tag);
$list_url = "index.php?option=COM_LIST_EMP_SURVEY&CAT=" . $_GET['CAT'];
$company_id = $_SESSION['company_id'];
$emp_id = $_SESSION['emp_id'];
if($emp_id == "" && isset($_SESSION['employee']['id']) && $_SESSION['employee']['id'] != ""){
	$emp_id = $_SESSION['employee']['id'];
}
$vurl = API . "?mode=getemployeesurveydashboard&method=get&company_id=" . $company_id . "&emp_id=" . $emp_id . "&category=" . $_GET['CAT'];
$configesponse = callAPI($vurl);
$dashData = json_decode($configesponse, true);
$dashData = is_array($dashData) ? $dashData : array();

$summary = isset($dashData['summary']) ? $dashData['summary'] : array();
$completionRate = isset($dashData['completion_rate']) ? $dashData['completion_rate'] : array();
$upcomingDeadlines = isset($dashData['upcoming_deadlines']) ? $dashData['upcoming_deadlines'] : array();
$recentActivity = isset($dashData['recent_activity']) ? $dashData['recent_activity'] : array();

$assignedCount = (int)($summary['assigned'] ?? 0);
$inProgressCount = (int)($summary['in_progress'] ?? 0);
$completedCount = (int)($summary['completed'] ?? 0);
$pendingCount = (int)($summary['pending'] ?? 0);

$completionPercent = (int)($completionRate['percent'] ?? 0);
$completionTitle = $completionRate['message_title'] != "" ? $completionRate['message_title'] : 'Great job!';
$completionText = $completionRate['message_text'] != "" ? $completionRate['message_text'] : "You've completed 0 of 0 required " . $display_tag_lower . "s.";

if($completionPercent > 100){
	$completionPercent = 100;
}
if($completionPercent < 0){
	$completionPercent = 0;
}

$myTitle = $_GET['CAT'] == '2' ? 'My Poll' : 'My Survey';
?>
<link rel="stylesheet" type="text/css" href="assets/css/custom_employee_survey_admin_dashboard.css">

<div class="pcoded-inner-content">
	<div class="main-body">
		<div class="page-wrapper p-0">
			<div class="page-body">
				<div class="row">
					<div class="col-lg-9">
						<div class="card">
							<div class="card-block p-4">
								<h2 class="f-22 f-w-600 m-0"><?php echo $myTitle; ?></h2>
								<?php /* <p class="text-muted mt-1"><?php echo $display_tag; ?></p> */ ?>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-3 col-md-6 col-sm-6 col-12">
								<div class="card prod-p-card emp-survey-kpi-card emp-survey-kpi-active" id="empSurveyKpiAssigned" data-filter="assigned" style="cursor:pointer;">
									<div class="card-body">
										<div class="row align-items-center">
											<div class="col-auto">
												<div class="progress-circle">
													<svg viewBox="0 0 68 68">
														<circle class="progress-bg" cx="34" cy="34" r="30"></circle>
														<circle class="progress-value text-primary" cx="34" cy="34" r="30" data-progress="100"></circle>
													</svg>
													<div class="progress-icon text-primary">
														<span class="ti-clipboard"></span>
													</div>
												</div>
											</div>
											<div class="col close">
												<h6 class="m-b-5 text-dark f-w-600">Assigned</h6>
												<h3 class="m-b-0 f-w-700 f-34 mt-2"><?php echo $assignedCount; ?></h3>
											</div>
										</div>
										<h6 class="pt-3 border-top mt-3 f-w-600 f-14"><?php echo $display_tag; ?> assigned to you</h6>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-12">
								<div class="card prod-p-card emp-survey-kpi-card" id="empSurveyKpiInProgress" data-filter="in_progress" style="cursor:pointer;">
									<div class="card-body">
										<div class="row align-items-center">
											<div class="col-auto">
												<div class="progress-circle">
													<svg viewBox="0 0 68 68">
														<circle class="progress-bg" cx="34" cy="34" r="30"></circle>
														<circle class="progress-value text-warning" cx="34" cy="34" r="30" data-progress="<?php echo $assignedCount > 0 ? round(($inProgressCount / $assignedCount) * 100) : 0; ?>"></circle>
													</svg>
													<div class="progress-icon">
														<span class="ti-pencil-alt text-warning"></span>
													</div>
												</div>
											</div>
											<div class="col close">
												<h6 class="m-b-5 text-dark f-w-600">In-Progress</h6>
												<h3 class="m-b-0 f-w-700 f-34 mt-2"><?php echo $inProgressCount; ?></h3>
											</div>
										</div>
										<h6 class="pt-3 border-top mt-3 f-w-600 f-14"><?php echo $display_tag; ?> in progress</h6>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-12">
								<div class="card prod-p-card emp-survey-kpi-card" id="empSurveyKpiCompleted" data-filter="completed" style="cursor:pointer;">
									<div class="card-body">
										<div class="row align-items-center">
											<div class="col-auto">
												<div class="progress-circle">
													<svg viewBox="0 0 68 68">
														<circle class="progress-bg" cx="34" cy="34" r="30"></circle>
														<circle class="progress-value text-success" cx="34" cy="34" r="30" data-progress="<?php echo $assignedCount > 0 ? round(($completedCount / $assignedCount) * 100) : 0; ?>"></circle>
													</svg>
													<div class="progress-icon">
														<span class="fa fa-check text-success"></span>
													</div>
												</div>
											</div>
											<div class="col close">
												<h6 class="m-b-5 text-dark f-w-600">Completed</h6>
												<h3 class="m-b-0 f-w-700 f-34 mt-2"><?php echo $completedCount; ?></h3>
											</div>
										</div>
										<h6 class="pt-3 border-top mt-3 f-w-600 f-14"><?php echo $display_tag; ?>s completed</h6>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-12">
								<div class="card prod-p-card emp-survey-kpi-card" id="empSurveyKpiPending" data-filter="pending" style="cursor:pointer;">
									<div class="card-body">
										<div class="row align-items-center">
											<div class="col-auto">
												<div class="progress-circle">
													<svg viewBox="0 0 68 68">
														<circle class="progress-bg" cx="34" cy="34" r="30"></circle>
														<circle class="progress-value text-danger" cx="34" cy="34" r="30" data-progress="<?php echo $assignedCount > 0 ? round(($pendingCount / $assignedCount) * 100) : 0; ?>"></circle>
													</svg>
													<div class="progress-icon">
														<span class="ti-timer text-danger"></span>
													</div>
												</div>
											</div>
											<div class="col close">
												<h6 class="m-b-5 text-dark f-w-600">Pending</h6>
												<h3 class="m-b-0 f-w-700 f-34 mt-2"><?php echo $pendingCount; ?></h3>
											</div>
										</div>
										<h6 class="pt-3 border-top mt-3 f-w-600 f-14"><?php echo $display_tag; ?> pending action</h6>
									</div>
								</div>
							</div>
						</div>

						<div class="card mt-2">
							<div class="card-header">
								<h5> <?php echo $display_tag; ?>s</h5>
								<div class="card-header-right">
									<a href="<?php echo $list_url; ?>" class="text-primary f-w-600">View All</a>
								</div>
							</div>
							<div class="card-block custom-scroll" style="max-height: 500px;">
								<div id="empSurveyTableContent">
									<div class="text-center py-5">
										<img src="assets/images/spiner/spiner3.gif" style="width:200px;" alt="">
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-3">
						<div class="card">
							<div class="card-header">
								<h5>Completion Rate</h5>
							</div>
							<div class="card-block">
								<div class="completion-card">
									<div class="card-content">
										<div class="progress-circle-rate" data-percent="<?php echo $completionPercent; ?>" style="--percent: <?php echo $completionPercent; ?>%;">
											<div class="progress-inner">
												<span class="percentage"><?php echo $completionPercent; ?>%</span>
											</div>
										</div>
										<div class="text-content">
											<h3 class="title"><?php echo htmlspecialchars($completionTitle); ?></h3>
											<p class="description"><?php echo htmlspecialchars($completionText); ?></p>
										</div>
									</div>
									<a href="<?php echo $list_url; ?>" class="details-link">
										View Completion Details <span class="chevron">&rsaquo;</span>
									</a>
								</div>
							</div>
						</div>

						<div class="card">
							<div class="card-header">
								<h5>Upcoming Deadlines</h5>
								<div class="card-header-right">
									<a href="<?php echo $list_url; ?>" class="f-w-600 float-end text-primary">View All</a>
								</div>
							</div>
							<div class="card-block">
								<?php if(count((array)$upcomingDeadlines) > 0) {
									foreach($upcomingDeadlines as $deadlineRow) {
										$urgency = $deadlineRow['urgency'] != "" ? $deadlineRow['urgency'] : 'info';
										$dateClass = $urgency == 'danger' ? 'text-danger' : ($urgency == 'warning' ? 'text-warning' : 'text-muted');
								?>
								<div class="row align-items-center mb-3">
									<div class="col-lg-7">
										<h6 class="text-muted f-14 m-0 f-w-600"><?php echo htmlspecialchars($deadlineRow['survey_name']); ?></h6>
										<p class="m-0 mt-1 f-12 <?php echo $dateClass; ?>"><?php echo htmlspecialchars($deadlineRow['due_date']); ?></p>
									</div>
									<div class="col-lg-5 text-end">
										<button class="btn p-1 bg-red-50 text-<?php echo $urgency; ?> f-w-600 f-12 btn-outline-<?php echo $urgency; ?> b-radius-5 mb-1"><?php echo htmlspecialchars($deadlineRow['days_left_label']); ?></button>
									</div>
								</div>
								<?php }
								} else { ?>
								<p class="emp-survey-empty m-0">No upcoming deadlines.</p>
								<?php } ?>
							</div>
							<div class="card-footer border-top">
								<a href="<?php echo $list_url; ?>" class="f-w-600 text-primary text-center d-block">See All Deadlines</a>
							</div>
						</div>

						<div class="card latest-update-card">
							<div class="card-header">
								<h5>Recent Activity</h5>
							</div>
							<div class="card-block">
								<div class="scroll-widget mt-4">
									<div class="latest-update-box">
										<?php if(count((array)$recentActivity) > 0) {
											$activityIndex = 0;
											foreach($recentActivity as $activityRow) {
												$rowClass = $activityIndex == 0 ? 'p-t-20 p-b-30' : 'p-b-30';
												$activityIndex++;
										?>
										<div class="row <?php echo $rowClass; ?>">
											<div class="col-auto text-end update-meta p-r-0">
												<i class="<?php echo htmlspecialchars($activityRow['icon_class']); ?> update-icon"></i>
											</div>
											<div class="col p-l-5">
												<h6 class="m-b-5"><?php echo htmlspecialchars($activityRow['title']); ?></h6>
												<p class="text-muted m-b-0"><?php echo htmlspecialchars($activityRow['subtitle']); ?></p>
											</div>
										</div>
										<?php }
										} else { ?>
										<p class="emp-survey-empty m-0">No recent activity.</p>
										<?php } ?>
									</div>
								</div>
							</div>
							<div class="card-footer border-top">
								<a href="<?php echo $list_url; ?>" class="f-w-600 text-primary text-center d-block">View All Activity</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="emp_survey_dash_company_id" value="<?php echo htmlspecialchars((string)$company_id); ?>">
<input type="hidden" id="emp_survey_dash_emp_id" value="<?php echo htmlspecialchars((string)$emp_id); ?>">
<input type="hidden" id="emp_survey_dash_category" value="<?php echo htmlspecialchars((string)$_GET['CAT']); ?>">

<script src="assets/js/custom_employee_survey_admin_dashboard.js?v=20260302"></script>
