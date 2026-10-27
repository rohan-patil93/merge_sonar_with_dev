<?php
$table_name1 = 'csr_proposal_data';

$url = API . "?mode=getcsrProposalData&orgininaor=" . $_SESSION['userid'];
$csr_res = callAPI($url);
$csr_res = json_decode($csr_res, 1);
$csr_proposal_arr = $csr_res['csr_proposal_arr'];
$tracking_percentages = $csr_res['tracking_percent_arr'];
$tracking_inprogress = $csr_res['tracking_inprogress'];
$tracking_pending = $csr_res['tracking_pending'];


$total_count = count($csr_proposal_arr) ?? 0;
$completedCount = 0;
$approvedCount = 0;
$progressCount = 0;
$activeCount = 0;
$approved_inprogressCount = 0;
$totalValue = $activeValue = $progressValue = $approvedValue = $trackingValue = $completedValue = 0;

$csr_data = $active_data = $inprogress_data = $approved_data = $tracking_data = $completed_data = array();
$rejected_data = array();
foreach ($csr_proposal_arr as $row) {
	$csr_data[$row['id']] = $row;
	$totalValue += $row['total_budget_grand_total'] ?? 0;

	if (isset($row['current_status']) && ($row['current_status'] == 0 || $row['current_status'] == 20)) {
		$activeCount++;
		$active_data[] = $row['id'];
		$activeValue += $row['total_budget_grand_total'] ?? 0;
	}
	if (isset($row['current_status']) && ($row['current_status'] == 30)) {
		$rejected_data[] = $row['id'];
	}
 
	if (isset($row['current_status']) && ($row['current_status'] > 0 && $row['current_status'] < 199) && $row['current_status'] != 20 && $row['current_status'] != 30) {
		$progressCount++;
		$inprogress_data[] = $row['id'];
        $progressValue += $row['total_budget_grand_total'] ?? 0;

	}
	if (isset($row['current_status']) && $row['current_status'] == 199) {
		$approvedCount++;
		$approved_data[] = $row['id'];
        $approvedValue += $row['total_budget_grand_total'] ?? 0;

	}
	if (isset($row['current_status']) && ($row['current_status'] == 199 && $row['tracking_status'] == 0)) {
		$approved_inprogressCount++;
		$tracking_data[] = $row['id'];
        $trackingValue += $row['total_budget_grand_total'] ?? 0;

	}
	if (isset($row['current_status']) && ($row['current_status'] == 199 && $row['tracking_status'] == 1)) {
		$completedCount++;
		$completed_data[] = $row['id'];
        $completedValue += $row['total_budget_grand_total'] ?? 0;

	}
}

$progress_var =  gettableStatusArray($table_name1);




?>

<link rel="stylesheet" href="assets/css/custom_list_originator_dashboard.css">

<div class="page-body">
	<div class="row">
		<!-- xyz -->
		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer  csr-click csr-card-9" data-title="Total"
     data-count="<?= $total_count ?>"
     data-status="total"
     data-userid="<?php echo $_SESSION['userid']; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-red lbl-card"><i class="fas fa-file-archive"></i> Total CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $total_count; ?></span>
					<div class="text-center">
						                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($totalValue); ?></p>

					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click csr-card-9" data-title="Active"
     data-count="<?= $activeCount ?>"
     data-status="active"
     data-userid="<?php echo $_SESSION['userid']; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> Active CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $activeCount; ?></span>
					<div class="text-center">
						<!-- <h2 class="m-t-20 m-b-0 d-inline-block text-c-blue cursor-pointer" ><?php // echo $activeCount; ?></h2> -->
                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($activeValue); ?></p>

					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click csr-card-9" data-title="Approval In Progress"
     data-count="<?= $progressCount ?>"
     data-status="progress"
     data-userid="<?php echo $_SESSION['userid']; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-purple lbl-card"><i class="fas fa-file-archive"></i> In Progress</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $progressCount; ?></span>
					<div class="text-center">
						<!-- <h2 class="m-b-0 d-inline-block text-c-purple cursor-pointer"><?php // echo $progressCount; ?></h2> -->
                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($progressValue); ?></p>

					</div>
				</div>
			</div>
		</div>


		<div class="col-xl-2 col-md-6 cursor-pointer">
			<div class="card ticket-card csr-click csr-card-9"data-title="Approved"
     data-count="<?= $approvedCount ?>"
     data-status="approved"
     data-userid="<?php echo $_SESSION['userid']; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-lite-green lbl-card"><i class="fas fa-file-archive"></i> Approved CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $approvedCount; ?></span>
					<div class="text-center">
						<!-- <h2 class="m-t-20 m-b-0 d-inline-block text-c-lite-green cursor-pointer"><?php // echo $approvedCount; ?></h2> -->
                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($approvedValue); ?></p>

					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click csr-card-9" data-title="Tracking"
     data-count="<?= $approved_inprogressCount ?>"
     data-status="tracking"
     data-userid="<?php echo $_SESSION['userid']; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-yellow lbl-card"><i class="fas fa-file-archive"></i> Tracking CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $approved_inprogressCount; ?></span>
					<div class="text-center">
						                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($trackingValue); ?></p>

					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card cursor-pointer csr-click csr-card-9" data-title="Completed"
     data-count="<?= $completedCount ?>"
     data-status="completed"
     data-userid="<?php echo $_SESSION['userid']; ?>">
				<div class="card-body">
					<span class="m-b-30 bg-c-green lbl-card"><i class="fas fa-file-archive"></i> Completed CSRs</span>
					<span class="form-label label label-success float-end f-w-600"><?php echo $completedCount; ?></span>
					<div class="text-center">
						                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($completedValue); ?></p>

					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="card proj-progress-card">
		<div class="card-block">
			<div class="row">
				<div class="col-lg-2 m-auto">
					<h5 class="d-flex align-itmes-center">CSR - Approvals</h5>
				</div>
				<div class="col-lg-5"></div>
				<div class="col-lg-5">
					<ul class="nav md-tabs infisuite_tab mt-2 ml-2 d-flex justify-content-end" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-bs-toggle="tab" href="#tab0" role="tab">Approval Pending </a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab1" role="tab"> Approval In Progress</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab2" role="tab">Approved</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-bs-toggle="tab" href="#tab3" role="tab">Rejected</a>
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
											<th scope="col">S.No.</th>
											<th scope="col">Creator</th>
											<th scope="col">Project Name</th>
											<th scope="col">Start Date</th>
											<th scope="col">End Date</th>
											<th scope="col" class='text-center'>Cost</th>
											<th scope="col">Status</th>
											<th scope="col"></th>
										</tr>
									</thead>

									<tbody class="custom-scroll" >
										<?php if (!empty($active_data)): 
											$i = 1;
										 foreach ($active_data as $value): ?>
												<tr class="csr-row-10">
													<td><?php echo $i++; ?></td>
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top' alt="User Profile"
																>
														<?php else: ?>
															<span alt="user"
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<span class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</span>
													</td>

													<td>
														<a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . $csr_data[$value]['id']); ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?php echo htmlspecialchars($csr_data[$value]['subject'], ENT_QUOTES, 'UTF-8'); ?>'>
															<?php echo htmlspecialchars(substr($csr_data[$value]['subject'], 0, 40), ENT_QUOTES, 'UTF-8') .
																(strlen($csr_data[$value]['subject']) > 40 ? '...' : ''); ?>
														</a>
													</td>

													<td><?php echo fromDate($csr_data[$value]['project_start_date']); ?></td>
													<td><?php echo fromDate($csr_data[$value]['project_end_date']); ?></td>
													<td class='text-end'><?php echo customeummarmonyformate($csr_data[$value]['total_budget_grand_total']); ?></td>

													<td>
														<span class="form-label label label-md csr-status-label"
      data-color="<?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['color']; ?>"
      data-bg="<?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['background_color']; ?>">
    <?php echo $progress_var[$csr_data[$value]['current_status']]; ?>
</span>

													</td>

													<td>
														<a href='index.php?option=COM_INSERT_CSR&Rid=<?php echo deocdeId('id:' . $csr_data[$value]['id']); ?>'>
															<i class='fa fa-edit btn-warning p-2 b-radius-5'></i>
														</a>
													</td>
												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="8">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt="No Records"
																src="assets/images/work.png"
																>
														</div>
													</div>
												</td>
											</tr>
										<?php endif; ?>
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
											<th scope="col">S.No.</th>
											<th scope="col">Creator</th>
											<th scope="col">Project Name</th>

											<th scope="col">Start Date</th>
											<th scope="col">End Date</th>

											<th scope="col" class='text-center'>Cost</th>
											<th scope="col">Status</th>

										</tr>
									</thead>
									<tbody class="custom-scroll" >
										<?php if (!empty($inprogress_data)): 
											$i=1;
										 foreach ($inprogress_data as $value): ?>
												<tr class="csr-row-10">
													<td><?php echo $i++; ?></td>

													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top' alt="User Profile"
																>
														<?php else: ?>
															<span alt="user"
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<span class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</span>
													</td>

													<td>
														<a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . $csr_data[$value]['id']); ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?php echo htmlspecialchars($csr_data[$value]['subject'], ENT_QUOTES, 'UTF-8'); ?>'>
															<?php echo htmlspecialchars(substr($csr_data[$value]['subject'], 0, 40), ENT_QUOTES, 'UTF-8') .
																(strlen($csr_data[$value]['subject']) > 40 ? '...' : ''); ?>
														</a>
													</td>

													<td><?php echo fromDate($csr_data[$value]['project_start_date']); ?></td>
													<td><?php echo fromDate($csr_data[$value]['project_end_date']); ?></td>
													<td class='text-end'><?php echo customeummarmonyformate($csr_data[$value]['total_budget_grand_total']); ?></td>

													<td>
														<span class="form-label label label-md csr-status-label"
      data-color="<?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['color']; ?>"
      data-bg="<?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['background_color']; ?>">
    <?php echo $progress_var[$csr_data[$value]['current_status']]; ?>
</span>

													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="7">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt="No Records"
																src="assets/images/work.png"
																>
														</div>
													</div>
												</td>
											</tr>
										<?php endif; ?>
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
											<th scope="col">S.No.</th>
											<th scope="col">Creator</th>
											<th scope="col">Project Name</th>

											<th scope="col">Start Date</th>
											<th scope="col">End Date</th>

											<th scope="col" class='text-center'>Cost</th>
											<th scope="col">Status</th>
										</tr>
									</thead>
									<tbody class="custom-scroll" >
										<?php if (!empty($approved_data)): 
											$i=1;
											foreach ($approved_data as $value): ?>
												<tr class="csr-row-10">
													<td><?php echo $i++; ?></td>

													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top' alt="User Profile"
																>
														<?php else: ?>
															<span alt="user"
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<span class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</span>
													</td>

													<td>
														<a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . $csr_data[$value]['id']); ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?php echo htmlspecialchars($csr_data[$value]['subject'], ENT_QUOTES, 'UTF-8'); ?>'>
															<?php echo htmlspecialchars(substr($csr_data[$value]['subject'], 0, 40), ENT_QUOTES, 'UTF-8') .
																(strlen($csr_data[$value]['subject']) > 40 ? '...' : ''); ?>
														</a>
													</td>

													<td><?php echo fromDate($csr_data[$value]['project_start_date']); ?></td>
													<td><?php echo fromDate($csr_data[$value]['project_end_date']); ?></td>
													<td class='text-end'><?php echo customeummarmonyformate($csr_data[$value]['total_budget_grand_total']); ?></td>

													<td>
														<span class="form-label label label-md csr-status-label"
      data-color="<?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['color']; ?>"
      data-bg="<?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['background_color']; ?>">
    <?php echo $progress_var[$csr_data[$value]['current_status']]; ?>
</span>

													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="7">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt="No Records"
																src="assets/images/work.png"
																>
														</div>
													</div>
												</td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="tab-pane " id="tab3" role="tabpanel">
						<div class="card table-card">
							<div class="table-responsive">
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th scope="col">S.No.</th>
											<th scope="col">Creator</th>
											<th scope="col">Project Name</th>
											<th scope="col">Start Date</th>
											<th scope="col">End Date</th>
											<th scope="col" class='text-center'>Cost</th>
											<th scope="col">Status</th>
											<!-- <th scope="col"></th> -->
										</tr>
									</thead>

									<tbody class="custom-scroll" >
										<?php if (count((array)$rejected_data)>0): 
											$i = 1;
										 foreach ($rejected_data as $value): ?>
												<tr class="csr-row-10">
													<td><?php echo $i++; ?></td>
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top' alt="User Profile"
																>
														<?php else: ?>
															<span alt="user"
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<span class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</span>
													</td>

													<td>
														<a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . $csr_data[$value]['id']); ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?php echo htmlspecialchars($csr_data[$value]['subject'], ENT_QUOTES, 'UTF-8'); ?>'>
															<?php echo htmlspecialchars(substr($csr_data[$value]['subject'], 0, 40), ENT_QUOTES, 'UTF-8') .
																(strlen($csr_data[$value]['subject']) > 40 ? '...' : ''); ?>
														</a>
													</td>

													<td><?php echo fromDate($csr_data[$value]['project_start_date']); ?></td>
													<td><?php echo fromDate($csr_data[$value]['project_end_date']); ?></td>
													<td class='text-end'><?php echo customeummarmonyformate($csr_data[$value]['total_budget_grand_total']); ?></td>

													<td>
														<span class="form-label label label-md csr-status-label"
														      data-color="<?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['color']; ?>"
														      data-bg="<?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['background_color']; ?>">
														    <?php echo $progress_var[$csr_data[$value]['current_status']]; ?>
														</span>

													</td>

												<?php  /*	<td>
														<a href='index.php?option=COM_INSERT_CSR&Rid=<?php echo deocdeId('id:' . $csr_data[$value]['id']); ?>'>
															<i class='fa fa-edit btn-warning p-2 b-radius-5'></i>
														</a>
													</td> */ ?>
												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="7">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt="No Records"
																src="assets/images/work.png"
																>
														</div>
													</div>
												</td>
											</tr>
										<?php endif; ?>
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
							<a class="nav-link" data-bs-toggle="tab" href="#tab5" role="tab">Work In Progress</a>
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
											<th scope="col">S.No.</th>
											<th scope="col">Creator</th>
											<th scope="col">Project Name</th>

											<th scope="col">Start Date</th>
											<th scope="col">End Date</th>
											<th scope="col" class='text-center'>Cost</th>
											<th scope="col"></th>
										</tr>
									</thead>
									<tbody class="custom-scroll" >
										<?php if (!empty($tracking_pending)): 
											$i=1;
										 foreach ($tracking_pending as $value): ?>
												<tr class="csr-row-10">
													<td><?php echo $i++; ?></td>

													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top' alt="User Profile"
																>
														<?php else: ?>
															<span alt="user"
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<span class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</span>
													</td>

													<td>
														<a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . $csr_data[$value]['id']); ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?php echo htmlspecialchars($csr_data[$value]['subject'], ENT_QUOTES, 'UTF-8'); ?>'>
															<?php echo htmlspecialchars(substr($csr_data[$value]['subject'], 0, 40), ENT_QUOTES, 'UTF-8') .
																(strlen($csr_data[$value]['subject']) > 40 ? '...' : ''); ?>
														</a>
													</td>

													<td><?php echo fromDate($csr_data[$value]['project_start_date']); ?></td>
													<td><?php echo fromDate($csr_data[$value]['project_end_date']); ?></td>
													<td class='text-end'><?php echo customeummarmonyformate($csr_data[$value]['total_budget_grand_total']); ?></td>

													<td class="text-center">
														<?php
														$percentage = $tracking_percentages[$csr_data[$value]['id']] ?? 0;
														if ($percentage < 50) {
															$labelClass = 'label-danger';
														} elseif ($percentage < 100) {
															$labelClass = 'label-warning';
														} else {
															$labelClass = 'label-success';
														} ?>
														<a href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$value]['id']) ?>'>
															<span class='form-label label <?= $labelClass ?> cursor-pointer' ><?= $percentage ?>%</span></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$value]['id']) ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a>
													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="7">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt="No Records"
																src="assets/images/work.png"
																>
														</div>
													</div>
												</td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tab5" role="tabpanel">
						<div class="card table-card">
							<div class="table-responsive">
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th scope="col">S.No.</th>
											<th scope="col">Creator</th>
											<th scope="col">Project Name</th>

											<th scope="col">Start Date</th>
											<th scope="col">End Date</th>
											<th scope="col" class='text-center'>Cost</th>

											<th scope="col"></th>
										</tr>
									</thead>
									<tbody class="custom-scroll" >
										<?php if (!empty($tracking_inprogress)): 
											$i=1;
										foreach ($tracking_inprogress as $value): ?>
												<tr class="csr-row-10">
													<td><?php echo $i++; ?></td>
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top' alt="User Profile"
																>
														<?php else: ?>
															<span alt="user"
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<span class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</span>
													</td>

													<td>
														<a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . $csr_data[$value]['id']); ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?php echo htmlspecialchars($csr_data[$value]['subject'], ENT_QUOTES, 'UTF-8'); ?>'>
															<?php echo htmlspecialchars(substr($csr_data[$value]['subject'], 0, 40), ENT_QUOTES, 'UTF-8') .
																(strlen($csr_data[$value]['subject']) > 40 ? '...' : ''); ?>
														</a>
													</td>

													<td><?php echo fromDate($csr_data[$value]['project_start_date']); ?></td>
													<td><?php echo fromDate($csr_data[$value]['project_end_date']); ?></td>
													<td class='text-end'><?php echo customeummarmonyformate($csr_data[$value]['total_budget_grand_total']); ?></td>

													<td class="text-center">
														<?php
														$percentage = $tracking_percentages[$csr_data[$value]['id']] ?? 0;
														if ($percentage < 50) {
															$labelClass = 'label-danger';
														} elseif ($percentage < 100) {
															$labelClass = 'label-warning';
														} else {
															$labelClass = 'label-success';
														} ?>
														<a href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$value]['id']) ?>'>
															<span class='form-label label <?= $labelClass ?> cursor-pointer' ><?= $percentage ?>%</span></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$value]['id']) ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a>
													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="7">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt="No Records"
																src="assets/images/work.png"
																>
														</div>
													</div>
												</td>
											</tr>
										<?php endif; ?>
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
											<th scope="col">S.No.</th>
											<th scope="col">Creator</th>
											<th scope="col">Project Name</th>

											<th scope="col">Start Date</th>
											<th scope="col">End Date</th>
											<th scope="col" class='text-center'>Cost</th>
											<th scope="col"></th>

										</tr>
									</thead>
									<tbody class="custom-scroll" >
										<?php if (!empty($completed_data)): 
											$i=1;
										foreach ($completed_data as $value): ?>
												<tr class="csr-row-10">
													<td><?php echo $i++; ?></td>
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top' alt="User Profile"
																>
														<?php else: ?>
															<span alt="user"
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<span class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</span>
													</td>

													<td>
														<a class='text-primary m-r-5'
															href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . $csr_data[$value]['id']); ?>'
															data-bs-toggle='tooltip'
															data-bs-placement='top'
															data-bs-html='true'
															title='<?php echo htmlspecialchars($csr_data[$value]['subject'], ENT_QUOTES, 'UTF-8'); ?>'>
															<?php echo htmlspecialchars(substr($csr_data[$value]['subject'], 0, 40), ENT_QUOTES, 'UTF-8') .
																(strlen($csr_data[$value]['subject']) > 40 ? '...' : ''); ?>
														</a>
													</td>

													<td><?php echo fromDate($csr_data[$value]['project_start_date']); ?></td>
													<td><?php echo fromDate($csr_data[$value]['project_end_date']); ?></td>
													<td class='text-end'><?php echo customeummarmonyformate($csr_data[$value]['total_budget_grand_total']); ?></td>

													<td class="text-center">
														<?php
														$percentage = $tracking_percentages[$csr_data[$value]['id']] ?? 0;
														if ($percentage < 50) {
															$labelClass = 'label-danger';
														} elseif ($percentage < 100) {
															$labelClass = 'label-warning';
														} else {
															$labelClass = 'label-success';
														} ?>
														<a href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$value]['id']) ?>'>
															<span class='form-label label <?= $labelClass ?> cursor-pointer' ><?= $percentage ?>%</span></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$value]['id']) ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a>
													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="7">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt="No Records"
																src="assets/images/work.png"
																>
														</div>
													</div>
												</td>
											</tr>
										<?php endif; ?>
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
<script src="assets/js/custom_list_originator_dashboard.js"></script>
