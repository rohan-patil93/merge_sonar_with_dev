<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$role_current_status = array(
	'CSR_COMMITTEE_GROUP' => '101',
	'CSR_COMMITTEE_MEMBER' => '102',
	'CSR_COMMITTEE_HEAD' => '103'
);

$table_name = 'csr_proposal_data';
$url = API . "?mode=getcsrdata&method=get&table=" . $table_name . "&id=" . $_SESSION['userid'] . "&current_status=" . $role_current_status[$_SESSION['working_role_short_code']];
// echo $url;
$progress_var =  gettableStatusArray($table_name);
$csr_response = callAPI($url);
$response = json_decode($csr_response, 1);
$pending_approvals_arr = $approved_by_me_arr = $approval_inprogress_arr = $approved_arr = array();


$pending_approvals_arr = $response['pending_approvals'];
$approved_by_me_arr = $response['approved_by_me'];
$approval_inprogress_arr = $response['approval_inprogress'];
$approved_arr = $response['approved'];

$tracking_pending_arr = $tracking_inprogress_arr = $tracking_completed_arr = array();
$tracking_pending_arr = $response['tracking_pending'];
$tracking_inprogress_arr = $response['tracking_inprogress'];
$tracking_completed_arr = $response['tracking_completed'];

$total_records = $response['total_records'];
// print_r($response);


// $table_name1 = 'application_users';
// $vdata1['order'] = "id ASC";
// // $vdata1['limit'] = "0,5";
// $vdata1["where"] = array('company_id' => $_SESSION['company_id'], 'is_active' => '0');
// $vdata = urlencode(json_encode($vdata1));
// $vurl = API . "?mode=gettabledetailswithconditions&table=" . $table_name1 . "&data=" . $vdata;
// $users_response = callAPI($vurl);
// $users_response = json_decode($users_response, 1);
// print_r($users_response);
// $users_array = array();
// foreach ($users_response as $row) {
// 	$users_array[$row['id']] = $row['display_name'];
// }
$length = count($total_records) ?? 0;
$completedCount = count($tracking_completed_arr) ?? 0;
$approvedCount = count($approved_arr) ?? 0;
$activeCount = count($pending_approvals_arr) ?? 0;
$progressCount = count($approval_inprogress_arr) ?? 0;
$trackingcount = $approvedCount - $completedCount;


// Initialize counters
$total_amount = 0;
foreach ($total_records as $row) {
	// echo 'amount: '.$row['total_budget_grand_total'];
	$total_amount += $row['total_budget_grand_total'];
}
$approved_amount = 0;
foreach ($approved_arr as $row) {
	$approved_amount += $row['total_budget_grand_total'];
}
$inprogress_amount = 0;
foreach ($approval_inprogress_arr as $row) {
	$inprogress_amount += $row['total_budget_grand_total'];
}
$completed_amount = 0;
foreach ($tracking_completed_arr as $row) {
	$completed_amount += $row['total_budget_grand_total'];
}
$active_amount = 0;
foreach ($pending_approvals_arr as $row) {
	$active_amount += $row['total_budget_grand_total'];
}



?>


<style>
	i {
		cursor: pointer;
	}

	.custom_txt_workentry {
		display: inline-block;
		border: 1px solid #cccccc;
		border-radius: 50px;
		width: 40px;
		height: 40px;
		line-height: 36px;
		font-size: 14px;
	}

	td {
		vertical-align: middle !important;
	}
</style>
<div class="page-body">
	<!-- Pradeep HTML Start here -->
	<!-- <div class="card proj-progress-card">
		<div class="card-block"> -->
	<div class="row">
		<!-- xyz -->
		<?php
		$total_amount = $total_amount / 100000;
		$total_amount = number_format($total_amount, 2);

		$approved_amount = $approved_amount / 100000;
		$approved_amount = number_format($approved_amount, 2);

		$inprogress_amount = $inprogress_amount / 100000;
		$inprogress_amount = number_format($inprogress_amount, 2);

		$completed_amount = $completed_amount / 100000;
		$completed_amount = number_format($completed_amount, 2);

		$active_amount = $active_amount / 100000;
		$active_amount = number_format($active_amount, 2);

		$tracking_amount = $approved_amount - $completed_amount;

		?>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-red lbl-card"><i class="fas fa-file-archive"></i> Total CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-red"><?php echo $total_amount; ?></h2>
						<p class="m-b-0 d-inline-block">Lac</p>
						<p class="m-b-0 m-t-15">No. Of CSRs <i class="m-r-10 f-18 text-c-red " style="font-style: normal;!important "><?php echo $length ?></i></p>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> Active CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-blue"><?php echo $active_amount; ?></h2>
						<p class="m-b-0 d-inline-block">Lac</p>
						<p class="m-b-0 m-t-15"> No. Of CSRs <i class=" m-r-10 f-18 text-c-blue" style="font-style: normal;!important "><?php echo $activeCount ?></i></p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-purple lbl-card"><i class="fas fa-file-archive"></i> In Progress CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-purple"><?php echo $inprogress_amount; ?></h2>
						<p class="m-b-0 d-inline-block">Lac</p>
						<p class="m-b-0 m-t-15"> No. Of CSRs <i class=" m-r-10 f-18 text-c-purple" style="font-style: normal;!important "><?php echo $progressCount ?></i></p>
					</div>
				</div>
			</div>
		</div>


		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-lite-green lbl-card"><i class="fas fa-file-archive"></i> Approved CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-lite-green"><?php echo $approved_amount; ?></h2>
						<p class="m-b-0 d-inline-block">Lac</p>
						<p class="m-b-0 m-t-15">No. Of CSRs <i class="m-r-10 f-18 text-c-lite-green" style="font-style: normal;!important "> <?php echo $approvedCount ?></i></p>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-yellow lbl-card"><i class="fas fa-file-archive"></i> Tracking CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-yellow"><?php echo $tracking_amount; ?></h2>
						<p class="m-b-0 d-inline-block">Lac</p>
						<p class="m-b-0 m-t-15"> No. Of CSRs <i class=" m-r-10 f-18 text-c-yellow" style="font-style: normal;!important "><?php echo $trackingcount ?></i></p>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-green lbl-card"><i class="fas fa-file-archive"></i> Completed CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-green"><?php echo $completed_amount; ?></h2>
						<p class="m-b-0 d-inline-block">Lac</p>
						<p class="m-b-0 m-t-15"> No. Of CSRs <i class=" m-r-10 f-18 text-c-green" style="font-style: normal;!important "><?php echo $completedCount ?></i></p>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- xyz -->
	<div class="card proj-progress-card">
		<div class="card-block">
			<div class="row">
				<div class="col-lg-2 m-auto">
					<h5 class="d-flex align-itmes-center">CSR</h5>
				</div>
				<div class="col-lg-4"></div>
				<div class="col-lg-6">
					<ul class="nav md-tabs infisuite_tab mt-2 ml-2 d-flex justify-content-end" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-bs-toggle="tab" href="#tab0" role="tab">Pending Approval</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab1" role="tab"> Approved By Me</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab2" role="tab">Approval In Progress</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab3" role="tab">Approved For All</a>
						</li>

					</ul>
				</div>
			</div>

			<div class="row">
				<div class="tab-content tabs mt-4">
					<div class="tab-pane active" id="tab0" role="tabpanel">
						<div class="card table-card">
							<div class="table-responsive">
								<table class="table table-hover m-b-0">

									<thead>
										<tr>
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th>Status</th>
										</tr>
									</thead>

									<tbody>
										<?php if (!empty($pending_approvals_arr)):
											foreach ($pending_approvals_arr as $key => $row):
										?>
												<tr>
													<td>
														<?php if ($_SESSION['user_image'][$row['created_by']] != "") { ?>
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
														<?php } else { ?>
															<span alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
														<?php } ?>
														<label class='mt-2 ml-2'> <?php echo $_SESSION['users'][$row['created_by']]; ?> </label>
													</td>
													<td>
														<a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
															<?= htmlspecialchars(substr($row['subject'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 40 ? '...' : '') ?>
														</a>
													</td>

													<td><?php echo  fromDate($row['project_start_date']) ?? ''; ?> </td>
													<td><?php echo fromDate($row['project_end_date']) ?? ''; ?> </td>
													<td class='text-end'><?php echo customeummarmonyformate($row['total_budget_grand_total']) ?? 0; ?> </td>
													<td><label class='form-label label label-md'
															style="color:<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>; background-color:<?php echo $_SESSION['status'][$row['current_status']]['background_color']; ?>;"><?php echo $progress_var[$row['current_status']]; ?> </label> </td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>';
										endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tab1" role="tabpanel">
						<div class="card table-card">
							<div class="table-responsive">
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th>Status</th>

										</tr>
									</thead>
									<tbody>

										<?php if (!empty($approved_by_me_arr)):
											foreach ($approved_by_me_arr as $key => $row):
										?>
												<tr>
													<td>
														<?php if ($_SESSION['user_image'][$row['created_by']] != "") { ?>
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
														<?php } else { ?>
															<span alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
														<?php } ?>
														<label class='mt-2 ml-2'> <?php echo $_SESSION['users'][$row['created_by']]; ?> </label>
													</td>
													<td><a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
															<?= htmlspecialchars(substr($row['subject'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 40 ? '...' : '') ?>
														</a></td>
													<td><?php echo  fromDate($row['project_start_date']) ?? ''; ?> </td>
													<td><?php echo fromDate($row['project_end_date']) ?? ''; ?> </td>
													<td class='text-end'><?php echo customeummarmonyformate($row['total_budget_grand_total']) ?? 0; ?> </td>
													<td><label class='form-label label label-md'
															style='color:<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>; background-color:<?php echo $_SESSION['status'][$row['current_status']]['background_color']; ?>;'><?php echo $progress_var[$row['current_status']]; ?> </label> </td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>';
										endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tab2" role="tabpanel">
						<div class="card table-card">

							<div class="table-responsive">
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th>Status</th>

										</tr>
									</thead>
									<tbody>

										<?php if (!empty($approval_inprogress_arr)):
											foreach ($approval_inprogress_arr as $key => $row):
										?>
												<tr>
													<td>
														<?php if ($_SESSION['user_image'][$row['created_by']] != "") { ?>
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
														<?php } else { ?>
															<span alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
														<?php } ?>
														<label class='mt-2 ml-2'> <?php echo $_SESSION['users'][$row['created_by']]; ?> </label>
													</td>
													<td><a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
															<?= htmlspecialchars(substr($row['subject'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 40 ? '...' : '') ?>
														</a></td>
													<td><?php echo  fromDate($row['project_start_date']) ?? ''; ?> </td>
													<td><?php echo fromDate($row['project_end_date']) ?? ''; ?> </td>
													<td class='text-end'><?php echo customeummarmonyformate($row['total_budget_grand_total']) ?? 0; ?> </td>
													<td><label class='form-label label label-md'
															style='color:<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>; background-color:<?php echo $_SESSION['status'][$row['current_status']]['background_color']; ?>;'><?php echo $progress_var[$row['current_status']]; ?> </label> </td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>';
										endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tab3" role="tabpanel">
						<div class="card table-card">

							<div class="table-responsive">
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th>Status</th>
											<th></th>

										</tr>
									</thead>
									<tbody>

										<?php if (!empty($approved_arr)):
											foreach ($approved_arr as $key => $row):
										?>
												<tr>
													<td>
														<?php if ($_SESSION['user_image'][$row['created_by']] != "") { ?>
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
														<?php } else { ?>
															<span alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
														<?php } ?>
														<label class='mt-2 ml-2'> <?php echo $_SESSION['users'][$row['created_by']]; ?> </label>
													</td>
													<td><a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
															<?= htmlspecialchars(substr($row['subject'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 40 ? '...' : '') ?>
														</a></td>
													<td><?php echo  fromDate($row['project_start_date']) ?? ''; ?> </td>
													<td><?php echo fromDate($row['project_end_date']) ?? ''; ?> </td>
													<td class='text-end'><?php echo customeummarmonyformate($row['total_budget_grand_total']) ?? 0; ?> </td>
													<td><label class='form-label label label-md'
															style='color:<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>; background-color:<?php echo $_SESSION['status'][$row['current_status']]['background_color']; ?>;'><?php echo $progress_var[$row['current_status']]; ?> </label>
															</td><td>
	<a class="" href="index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?> ">
	<label class='btn form-label label label-md bg-primary' style='cursor: pointer;'>Tracking</label></a>
															 </td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>';
										endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="card proj-progress-card">
		<div class="card-block">
			<div class="row">
				<div class="col-lg-2 m-auto">
					<h5 class="d-flex align-itmes-center">CSR Tracking</h5>
				</div>
				<div class="col-lg-5"></div>
				<div class="col-lg-5">
					<ul class="nav md-tabs infisuite_tab mt-2 ml-2 d-flex justify-content-end" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-bs-toggle="tab" href="#tab4" role="tab">Pending</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab5" role="tab">In Progress</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab6" role="tab"> Completed</a>
						</li>

					</ul>
				</div>
			</div>

			<div class="row">
				<div class="tab-content tabs mt-4">
					<div class="tab-pane active" id="tab4" role="tabpanel">
						<div class="card table-card">
							<div class="table-responsive">
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>

											<th></th>
										</tr>
									</thead>
									<?php
									function getTracckingPerentage($value)
									{
										$url = API . "?mode=gettracckingperentage&method=get&id=" . $value;
										$response = callAPI($url);
										$response = json_decode($response, 1);
										return $response;
									}
									?>
									<tbody>
										<?php if (!empty($tracking_pending_arr)):
											foreach ($tracking_pending_arr as $key => $row):
												$response1 = getTracckingPerentage($row['id']);
												$percentage = ($response1['total_rows'] > 0) ? ($response1['completed_count'] / $response1['total_rows']) * 100 : 0;

												if ($percentage < 50) {
													$labelClass = 'label-danger';
												} elseif ($percentage < 100) {
													$labelClass = 'label-warning';
												} else {
													$labelClass = 'label-success';
												}
										?>
												<tr>
													<td>
														<?php if ($_SESSION['user_image'][$row['created_by']] != "") { ?>
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
														<?php } else { ?>
															<span alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
														<?php } ?>
														<label class='mt-2 ml-2'> <?php echo $_SESSION['users'][$row['created_by']]; ?> </label>
													</td>
													<td><a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
															<?= htmlspecialchars(substr($row['subject'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 40 ? '...' : '') ?>
														</a></td>

													<td><?php echo  fromDate($row['project_start_date']) ?? ''; ?> </td>
													<td><?php echo fromDate($row['project_end_date']) ?? ''; ?> </td>
													<td class='text-end'><?php echo customeummarmonyformate($row['total_budget_grand_total']) ?? 0; ?> </td>
													<td class="text-center">
														<a href="index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?> ">
															<label class='form-label label <?php echo $labelClass; ?>' style='cursor: pointer;'> <?php echo  round($percentage); ?>% </label></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a>
													</td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="6" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>';
										endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="tab-pane " id="tab5" role="tabpanel">
						<div class="card table-card">
							<div class="table-responsive">
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>

											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($tracking_inprogress_arr)):
											foreach ($tracking_inprogress_arr as $key => $row):
												$response1 = getTracckingPerentage($row['id']);
												$table_name2 = 'csr_project_tracking';
												$url = API . "?mode=gettracckingperentage&method=get&id=" . $row['id'];
												$response1 = callAPI($url);
												$response1 = json_decode($response1, 1);
												$percentage = ($response1['total_rows'] > 0) ? ($response1['completed_count'] / $response1['total_rows']) * 100 : 0;

												if ($percentage < 50) {
													$labelClass = 'label-danger';
												} elseif ($percentage < 100) {
													$labelClass = 'label-warning';
												} else {
													$labelClass = 'label-success';
												}
										?>
												<tr>
													<td>
														<?php if ($_SESSION['user_image'][$row['created_by']] != "") { ?>
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
														<?php } else { ?>
															<span alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
														<?php } ?>
														<label class='mt-2 ml-2'> <?php echo $_SESSION['users'][$row['created_by']]; ?> </label>
													</td>
													<td><a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
															<?= htmlspecialchars(substr($row['subject'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 40 ? '...' : '') ?>
														</a></td>

													<td><?php echo  fromDate($row['project_start_date']) ?? ''; ?> </td>
													<td><?php echo fromDate($row['project_end_date']) ?? ''; ?> </td>
													<td class='text-end'><?php echo customeummarmonyformate($row['total_budget_grand_total']) ?? 0; ?> </td>
													<td class="text-center">
														<a href="index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?> ">
															<label class='form-label label <?php echo $labelClass; ?>' style='cursor: pointer;'> <?php echo  round($percentage); ?>%</label></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a>
													</td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="6" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>';
										endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tab6" role="tabpanel">
						<div class="card table-card">
							<div class="table-responsive">
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th></th>
										</tr>
									</thead>
									<tbody>

										<?php if (!empty($tracking_completed_arr)):
											foreach ($tracking_completed_arr as $key => $row):
										?>
												<tr>
													<td>
														<?php if ($_SESSION['user_image'][$row['created_by']] != "") { ?>
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
														<?php } else { ?>
															<span alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
														<?php } ?>
														<label class='mt-2 ml-2'> <?php echo $_SESSION['users'][$row['created_by']]; ?> </label>
													</td>
													<td><a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
															<?= htmlspecialchars(substr($row['subject'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 40 ? '...' : '') ?>
														</a></td>
													<td><?php echo  fromDate($row['project_start_date']) ?? ''; ?> </td>
													<td><?php echo fromDate($row['project_end_date']) ?? ''; ?> </td>
													<td class='text-end'><?php echo customeummarmonyformate($row['total_budget_grand_total']) ?? 0; ?> </td>
													<td><a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a></td>

												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>';
										endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

</div>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
		tooltipTriggerList.forEach(function(tooltipTriggerEl) {
			new bootstrap.Tooltip(tooltipTriggerEl);
		});
	});
</script>