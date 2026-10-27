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
// echo $url; exit('test');
$progress_var =  gettableStatusArray($table_name);
$csr_response = callAPI($url);
$response = json_decode($csr_response, 1);
$pending_approvals_arr = $approved_by_me_arr = $approval_inprogress_arr = $approved_arr = array();

// echo $response['pending_approval_query'];
$pending_approvals_arr = $response['pending_approvals'];
$approved_by_me_arr = $response['approved_by_me'];
$approval_inprogress_arr = $response['approval_inprogress'];
$approved_arr = $response['approved'];

$tracking_pending_arr = $tracking_inprogress_arr = $tracking_completed_arr = array();
$tracking_pending_arr = $response['tracking_pending'];
$tracking_inprogress_arr = $response['tracking_inprogress'];
$tracking_completed_arr = $response['tracking_completed']; 
$tracking_percentage_arr = $response['tracking_percentage'];
// echo '<pre>';
// print_r($tracking_percentage_arr);
// echo '</pre>'; 

// echo " competed rows: </br>";
// print_r($tracking_completed_arr);

$total_records = $response['total_records'];


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
// $trackingcount = $approvedCount - $completedCount;
$trackingcount = count($tracking_inprogress_arr) ?? 0;


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
$tracking_amount = 0;
foreach ($tracking_inprogress_arr as $row) {
	$tracking_amount += $row['total_budget_grand_total'];
}


?>
<link rel="stylesheet" href="assets/css/custom_list_committee_review.css">


<div class="page-body">
	<!-- Pradeep HTML Start here -->
	<!-- <div class="card proj-progress-card">
		<div class="card-block"> -->
	<div class="row">
		<!-- xyz -->
		<?php
		$total_amount_val = formatCsrSummaryAmount($total_amount);
		$total_amount = $total_amount_val['value'];

		$active_amount_val = formatCsrSummaryAmount($active_amount);
		$active_amount = $active_amount_val['value'];

		$inprogress_amount_val = formatCsrSummaryAmount($inprogress_amount);
		$inprogress_amount = $inprogress_amount_val['value'];

		$approved_amount_val = formatCsrSummaryAmount($approved_amount);
		$approved_amount = $approved_amount_val['value'];

		$tracking_amount_val = formatCsrSummaryAmount($tracking_amount);
		$tracking_amount = $tracking_amount_val['value'];

		$completed_amount_val = formatCsrSummaryAmount($completed_amount);
		$completed_amount = $completed_amount_val['value'];

		$csr_amount_unit = $total_amount_val['label'];

		// $tracking_amount = $approved_amount - $completed_amount;

		?>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click"
     data-title="Total"
     data-count="<?= $length ?>"
     data-status="total"
     data-userid="<?php echo $_SESSION['userid']; ?>"
     data-current-status="<?php echo $role_current_status[$_SESSION['working_role_short_code']]; ?>" >
				<div class="card-body">
					<span class="m-b-30 bg-c-red lbl-card"><i class="fas fa-file-archive"></i> Total CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $length ?></span>
					<div class="text-center">
						<h3 class="m-b-0 d-inline-block text-c-red"><?php echo $total_amount; ?></h3>
						<p class="m-b-0 d-inline-block"><?php echo $csr_amount_unit; ?></p>
						
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click"
     data-title="Active"
     data-count="<?= $activeCount ?>"
     data-status="active"
     data-userid="<?php echo $_SESSION['userid']; ?>"
     data-current-status="<?php echo $role_current_status[$_SESSION['working_role_short_code']]; ?>" >
				<div class="card-body">
					<span class="m-b-30 bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> Active CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $activeCount ?></span>
					<div class="text-center">
						<h3 class="m-b-0 d-inline-block text-c-blue"><?php echo $active_amount; ?></h3>
						<p class="m-b-0 d-inline-block"><?php echo $csr_amount_unit; ?></p>
						
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click"
     data-title="Approval In Progress"
     data-count="<?= $progressCount ?>"
     data-status="progress"
     data-userid="<?php echo $_SESSION['userid']; ?>"
     data-current-status="<?php echo $role_current_status[$_SESSION['working_role_short_code']]; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-purple lbl-card"><i class="fas fa-file-archive"></i> In Progress CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $progressCount ?></span>
					<div class="text-center">
						<h3 class="m-b-0 d-inline-block text-c-purple"><?php echo $inprogress_amount; ?></h3>
						<p class="m-b-0 d-inline-block"><?php echo $csr_amount_unit; ?></p>
						
					</div>
				</div>
			</div>
		</div>


		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click"
     data-title="Approved"
     data-count="<?= $approvedCount ?>"
     data-status="approved"
     data-userid="<?php echo $_SESSION['userid']; ?>"
     data-current-status="<?php echo $role_current_status[$_SESSION['working_role_short_code']]; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-lite-green lbl-card"><i class="fas fa-file-archive"></i> Approved CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $approvedCount ?></span>
					<div class="text-center">
						<h3 class="m-b-0 d-inline-block text-c-lite-green"><?php echo $approved_amount; ?></h3>
						<p class="m-b-0 d-inline-block"><?php echo $csr_amount_unit; ?></p>
						
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click"
     data-title="Tracking"
     data-count="<?= $trackingcount ?>"
     data-status="tracking"
     data-userid="<?php echo $_SESSION['userid']; ?>"
     data-current-status="<?php echo $role_current_status[$_SESSION['working_role_short_code']]; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-yellow lbl-card"><i class="fas fa-file-archive"></i> Tracking CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $trackingcount ?></span>
					<div class="text-center">
						<h3 class="m-b-0 d-inline-block text-c-yellow"><?php echo $tracking_amount; ?></h3>
						<p class="m-b-0 d-inline-block"><?php echo $csr_amount_unit; ?></p>
						
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click"
     data-title="Completed"
     data-count="<?= $completedCount ?>"
     data-status="completed"
     data-userid="<?php echo $_SESSION['userid']; ?>"
     data-current-status="<?php echo $role_current_status[$_SESSION['working_role_short_code']]; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-green lbl-card"><i class="fas fa-file-archive"></i> Completed CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $completedCount ?></span>
					<div class="text-center">
						<h3 class="m-b-0 d-inline-block text-c-green"><?php echo $completed_amount; ?></h3>
						<p class="m-b-0 d-inline-block"><?php echo $csr_amount_unit; ?></p>

						
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
					<h5 class="d-flex align-itmes-center">CSR - Approvals</h5>
				</div>
				<div class="col-lg-4"></div>
				<div class="col-lg-6">
					<ul class="nav md-tabs infisuite_tab mt-2 ml-2 d-flex justify-content-end" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-bs-toggle="tab" href="#tab0" role="tab">Approval Pending</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab1" role="tab">Approved By Me</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab2" role="tab">Approval In Progress</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab3" role="tab">Approved By All</a>
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
											<th>Project Name</th>

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
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" >
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
													<td><label class='form-label label label-md csr-status-label'
															data-color="<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>"
      data-bg="<?php echo $_SESSION['status'][$row['current_status']]['background_color']; ?>"><?php echo $progress_var[$row['current_status']] ; ?> </label> </td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="6" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
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
											<th>Project Name</th>

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
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" >
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
													<td><label class='form-label label label-md csr-status-label'
															data-color="<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>"
      data-bg="<?php echo $_SESSION['status'][$row['current_status']]['background_color']; ?>"><?php echo $progress_var[$row['current_status']]; ?> </label> </td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="6" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
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
											<th>Project Name</th>

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
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" >
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
													<td><label class='form-label label label-md csr-status-label'
															data-color="<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>"
      data-bg="<?php echo $_SESSION['status'][$row['current_status']]['background_color']; ?>"><?php echo $progress_var[$row['current_status']]; ?>  </label> </td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="6" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
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
											<th>Project Name</th>

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
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" >
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
													<td><label class='form-label label label-md csr-status-label'
															data-color="<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>"
      data-bg="<?php echo $_SESSION['status'][$row['current_status']]['background_color']; ?>"><?php echo $progress_var[$row['current_status']]; ?> </label>
															</td><td>
	<a class="" href="index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?> ">
	<label class='btn form-label label label-md bg-primary cursor-pointer' >Tracking</label></a>
															 </td>
												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="7" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
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
							<a class="nav-link active" data-bs-toggle="tab" href="#tab4" role="tab">Yet to Start</a>
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
											<th>Project Name</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th></th>
										</tr>
									</thead>

									<tbody>
										<?php if (!empty($tracking_pending_arr)):
											foreach ($tracking_pending_arr as $key => $row):
												$percentage = $tracking_percentage_arr[$row['id']] ?? 0;

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
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" >
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
															<label class='form-label label <?php echo $labelClass; ?> cursor-pointer' > <?php echo  round($percentage); ?>% </label></a>
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
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
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
											<th>Project Name</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($tracking_inprogress_arr)){
											foreach ($tracking_inprogress_arr as $key => $row) {
												// $response1 = getTracckingPerentage($row['id']);
												$percentage = $tracking_percentage_arr[$row['id']] ?? 0;


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
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" >
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
															<label class='form-label label <?php echo $labelClass; ?> cursor-pointer' > <?php echo  round($percentage); ?>%</label></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a>
													</td>
												</tr>
										<?php
											}
											 }else{

											echo '<tr><td colspan="6" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
										</div>   
										</div></td></tr>';
										} ?>
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
											<th>Project Name</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th></th>
										</tr>
									</thead>
									<tbody>

										<?php if (!empty($tracking_completed_arr)):
											foreach ($tracking_completed_arr as $key => $row):
												$percentage = $tracking_percentage_arr[$row['id']] ?? 0;

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
															<img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" >
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
													<td>
														<a href="index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?> ">
															<label class='form-label label <?php echo $labelClass; ?> cursor-pointer'> <?php echo  round($percentage); ?>%</label></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['id']); ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a></td>

												</tr>
										<?php
											endforeach;
										else:

											echo '<tr><td colspan="6" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
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
<script src="assets/js/custom_list_committee_review.js"></script>
