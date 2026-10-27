<?php
$category = array('1' => 'Survey', '2' => 'Poll');
if($_GET['CAT'] == ""){
	$_GET['CAT'] = 1;
}
$display_tag = $category[$_GET['CAT']];
$company_id = $_SESSION['company_id'];
$vurl = API . "?mode=getsurveyadmindashboard&method=get&company_id=" . $company_id . "&category=" . $_GET['CAT'];
$configesponse = callAPI($vurl);
$dashData = json_decode($configesponse, true);

$dashData = is_array($dashData) ? $dashData : array();

$summary = isset($dashData['summary']) ? $dashData['summary'] : array();
$completionRate = isset($dashData['completion_rate']) ? $dashData['completion_rate'] : array();
$recentSurveys = isset($dashData['recent_surveys']) ? $dashData['recent_surveys'] : array();
$departmentChartData = isset($dashData['responses_by_department']) ? $dashData['responses_by_department'] : array();
$monthlyTrendData = isset($dashData['monthly_response_trend']) ? $dashData['monthly_response_trend'] : array();

$totalSurveys = (int)($summary['total'] ?? 0);
$activeSurveys = (int)($summary['active'] ?? 0);
$completedSurveys = (int)($summary['completed'] ?? 0);
$draftSurveys = (int)($summary['draft'] ?? 0);

$completionPercent = (int) round($completionRate['percent'] ?? 0);
$completionCompletedCount = (int)($completionRate['completed_count'] ?? 0);
$completionTotalCount = (int)($completionRate['total_count'] ?? 0);
$trendPercent = (int) round($completionRate['trend_percent'] ?? 0);
$trendDirection = $completionRate['trend_direction'] != "" ? $completionRate['trend_direction'] : 'flat';

if($completionPercent > 100){
	$completionPercent = 100;
}
if($completionPercent < 0){
	$completionPercent = 0;
}

$trendIconClass = 'ti-minus';
$trendTextClass = 'text-muted';
if($trendDirection == 'up'){
	$trendIconClass = 'ti-arrow-up';
	$trendTextClass = 'text-success';
}else if($trendDirection == 'down'){
	$trendIconClass = 'ti-arrow-down';
	$trendTextClass = 'text-danger';
}

$createLabel = $_GET['CAT'] == '2' ? 'Create Poll' : 'Create Survey';
$overviewTitle = $display_tag . ' Overview';
?>
<!-- morris chart -->
<link rel="stylesheet" type="text/css" href="assets/bower_components/morris.js/css/morris.css">

<style>
	.survey_dashboard table tr th, .survey_dashboard table tr td{
		white-space: nowrap !important;
	}
</style>

<div class="pcoded-inner-content">
	<div class="main-body">
		<div class="page-wrapper p-0">
			<div class="page-body">
				<div class="card">
					<div class="card-body p-4">
						<div class="row align-items-center">
							<div class="col-lg-9 col-md-9 col-sm-8 col-12">
								<h2 class="f-22 f-w-600 m-0"><?php echo $overviewTitle; ?></h2>
								<p class="text-muted mt-1 mb-0">Admin Dashboard</p>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-4 col-12 text-end">
								<a href="index.php?option=COM_INSERT_SURVEY_SETUP&CAT=<?php echo $_GET['CAT']; ?>" class="btn btn-success btn-sm waves-effect f-14">
									<i class="ti-plus m-r-5 text-white"></i> <?php echo $createLabel; ?>
								</a>
							</div>
						</div>

						<div class="row survey_dashboard mt-4">
							<div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
								<div class="card prod-p-card card-blue mb-0 border b-radius-10">
									<div class="card-body">
										<div class="row align-items-center">
											<div class="col-auto">
												<div class="p-3 bg-blue-100 b-radius-10 text-center img-50 text-success">
													<span class="fa fa-calculator f-w-600 f-18 text-primary"></span>
												</div>
											</div>
											<div class="col close">
												<h6 class="m-b-5 text-dark f-w-600">Total <?php echo $display_tag; ?>s</h6>
												<h3 class="m-b-0 f-w-700 text-primary f-34 mt-2"><?php echo $totalSurveys; ?></h3>
											</div>
										</div>
										<h6 class="pt-3 border-top mt-3 f-w-600">All Time</h6>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
								<div class="card prod-p-card card-info mb-0 border b-radius-10">
									<div class="card-body">
										<div class="row align-items-center">
											<div class="col-auto">
												<div class="p-3 bg-green-100 b-radius-10 text-center img-50 text-success">
													<span class="ti-control-play f-w-600 f-18 text-success"></span>
												</div>
											</div>
											<div class="col close">
												<h6 class="m-b-5 text-dark f-w-600">Running <?php echo $display_tag; ?>s</h6>
												<h3 class="m-b-0 f-w-700 text-success f-34 mt-2"><?php echo $activeSurveys; ?></h3>
											</div>
										</div>
										<h6 class="pt-3 border-top mt-3 f-w-600">Currently Running</h6>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
								<div class="card prod-p-card card-purple mb-0 border b-radius-10">
									<div class="card-body">
										<div class="row align-items-center">
											<div class="col-auto">
												<div class="p-3 bg-purple-100 b-radius-10 text-center img-50 text-success">
													<span class="fa fa-check f-w-600 f-18 text-purple"></span>
												</div>
											</div>
											<div class="col close">
												<h6 class="m-b-5 text-dark f-w-600">Completed</h6>
												<h3 class="m-b-0 f-w-700 text-purple f-34 mt-2"><?php echo $completedSurveys; ?></h3>
											</div>
										</div>
										<h6 class="pt-3 border-top mt-3 f-w-600">All Completed <?php echo $display_tag; ?>s</h6>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
								<div class="card prod-p-card card-yellow mb-0 border b-radius-10">
									<div class="card-body">
										<div class="row align-items-center">
											<div class="col-auto">
												<div class="p-3 bg-yellow-100 b-radius-10 text-center img-50 text-success">
													<span class="ti-file f-w-600 f-18 text-warning"></span>
												</div>
											</div>
											<div class="col close">
												<h6 class="m-b-5 text-dark f-w-600">Draft</h6>
												<h3 class="m-b-0 f-w-700 text-warning f-34 mt-2"><?php echo $draftSurveys; ?></h3>
											</div>
										</div>
										<h6 class="pt-3 border-top mt-3 f-w-600">Not Yet Launched</h6>
									</div>
								</div>
							</div>
						</div>

						<div class="row mt-4">
							<div class="col-lg-3 col-md-4 col-sm-12 col-12">
								<div class="card">
									<div class="card-header">
										<h5><?php echo $display_tag; ?> Completion Rate <i class="ti-info-alt"></i></h5>
									</div>
									<?php $roundedPercent = round($completionPercent / 10) * 10; ?>
									<div class="card-block mt-4 text-center">
										<div data-label="<?php echo $completionPercent; ?>%" class="radial-bar radial-bar-<?php echo $roundedPercent; ?> radial-bar-lg "></div>
										<h6><?php echo $completionCompletedCount; ?> of <?php echo $completionTotalCount; ?> <?php echo strtolower($display_tag); ?>s completed</h6>
										<p><i class="<?php echo $trendIconClass; ?>"></i> <span class="<?php echo $trendTextClass; ?> f-w-600"><?php echo $trendPercent; ?>% </span>vs last 30 days</p>
									</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-8 col-sm-12 col-12">
								<div class="card">
									<div class="card-header">
										<h5>Responses by Department <i class="ti-info-alt"></i></h5>
									</div>
									<div class="card-block mt-4 text-center">
										<div id="morris-bar-chart" style="height:190px;"></div>
										<div id="morris-bar-chart-empty" class="text-muted f-14" style="display:none;">No response data available.</div>
									</div>
								</div>
							</div>
							<div class="col-lg-5 col-md-12 col-sm-12 col-12">
								<div class="card">
									<div class="card-header">
										<h5>Monthly Response Trend <i class="ti-info-alt"></i></h5>
									</div>
									<div class="card-block mt-4 text-center">
										<div id="line-example" style="height: 190px;"></div>
										<div id="line-example-empty" class="text-muted f-14" style="display:none;">No response data available.</div>
									</div>
								</div>
							</div>
						</div>

						<div class="card mt-2">
							<div class="card-header">
								<h5>Recent(5) <?php echo $display_tag; ?>s </h5>
								<div class="card-header-right">
									<a href="index.php?option=COM_LIST_SURVEY_SETUP&CAT=<?php echo $_GET['CAT']; ?>" class="text-primary f-w-600">View All</a>
								</div>
							</div>
							<div class="card-block">
								<div class="table-responsive survey_dashboard">
									<table class="table table-bordered table-sm f-12">
										<tr bgcolor="#eeeeee">
											<th><?php echo $display_tag; ?> Name</th>
											<th>Category</th>
											<th>Assigned To</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
										<?php if(count((array)$recentSurveys) > 0) {
											foreach($recentSurveys as $surveyRow) {
												$surveyId = deocdeId("id:" . $surveyRow['survey_id']);
												$statusColor = $surveyRow['status_color'] != "" ? $surveyRow['status_color'] : 'secondary';
												$viewUrl = "index.php?option=COM_VIEW_SURVEY_SUMMARY&Rid=" . $surveyId . "&CAT=" . $_GET['CAT'];
												$editUrl = "index.php?option=COM_INSERT_SURVEY_SETUP&Rid=" . $surveyId . "&CAT=" . $_GET['CAT'];

										?>
										<tr>
											<td>
												<div class="d-flex align-items-center feed-card">
													<div class="p-2 bg-green-100 b-radius-10 text-center left-icon-width text-success m-r-10">
														<i class="fa fa-laptop f-w-600 m-t-5"></i>
													</div>
													<a href="<?php echo $viewUrl; ?>">
														<h6 class="m-b-5 f-12"><?php echo htmlspecialchars($surveyRow['survey_name']); ?></h6>
													</a>
												</div>
											</td>
											<td><?php echo htmlspecialchars($surveyRow['category_label']); ?></td>
											<td><?php echo htmlspecialchars($surveyRow['assigned_to_label']); ?></td>
											<td><?php echo htmlspecialchars($surveyRow['survey_start_date']); ?></td>
											<td><?php echo htmlspecialchars($surveyRow['survey_end_date']); ?></td>
											<td>
												<button class="btn btn-sm card-info text-<?php echo $statusColor; ?> f-w-600 f-12 btn-outline-<?php echo $statusColor; ?> b-radius-5 mb-1"><?php echo htmlspecialchars($surveyRow['status_label']); ?></button>
											</td>
											<td>
												<?php if($surveyRow['status'] != 'draft') { ?>
												<a href="<?php echo $editUrl; ?>" class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
													<i class="fa fa-eye m-r-5"></i> View
												</a>
												<?php } ?>
												<?php if($surveyRow['status'] == 'draft') { ?>
												<a href="<?php echo $editUrl; ?>" class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
													<i class="fa fa-edit m-r-5"></i> Edit
												</a>
												<?php } ?>
												<a href="<?php echo $viewUrl; ?>" class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
													<i class="ti-bar-chart-alt m-r-5"></i> Summary
												</a>
											</td>
										</tr>
										<?php }
										} else { ?>
										<tr>
											<td colspan="7" class="text-center text-muted">No <?php echo strtolower($display_tag); ?>s found.</td>
										</tr>
										<?php } ?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="survey_department_chart_data" value="<?php echo htmlspecialchars(json_encode($departmentChartData), ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" id="survey_monthly_trend_data" value="<?php echo htmlspecialchars(json_encode($monthlyTrendData), ENT_QUOTES, 'UTF-8'); ?>">

<!-- Morris Chart js -->
<script src="assets/bower_components/raphael/js/raphael.min.js"></script>
<script src="assets/bower_components/morris.js/js/morris.js"></script>
<script src="assets/js/custom_survey_admin_dashboard.js"></script>
