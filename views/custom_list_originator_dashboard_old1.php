<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$table_name1 = 'csr_proposal_data';

$url = API . "?mode=getcsrProposalData&orgininaor=" . $_SESSION['userid'];
$csr_res = callAPI($url);
$csr_res = json_decode($csr_res, 1);
$csr_proposal_arr = $csr_res['csr_proposal_arr'];
$tracking_percentages = $csr_res['tracking_percent_arr'];
$tracking_inprogress = $csr_res['tracking_inprogress'];
$tracking_pending = $csr_res['tracking_pending'];
// print_r($tracking_percentages);


$total_count = count($csr_proposal_arr) ?? 0;
$completedCount = 0;
$approvedCount = 0;
$progressCount = 0;
$activeCount = 0;
$approved_inprogressCount = 0;


$csr_data = $active_data = $inprogress_data = $approved_data = $tracking_data = $completed_data = array();
foreach ($csr_proposal_arr as $row) {
	$csr_data[$row['id']] = $row;

	if (isset($row['current_status']) && $row['current_status'] == 0) {
		$activeCount++;
		$active_data[] = $row['id'];
	}
	if (isset($row['current_status']) && ($row['current_status'] > 0 && $row['current_status'] < 199)) {
		$progressCount++;
		$inprogress_data[] = $row['id'];
	}
	if (isset($row['current_status']) && $row['current_status'] == 199) {
		$approvedCount++;
		$approved_data[] = $row['id'];
	}
	if (isset($row['current_status']) && ($row['current_status'] == 199 && $row['tracking_status'] == 0)) {
		$approved_inprogressCount++;
		$tracking_data[] = $row['id'];
	}
	if (isset($row['current_status']) && ($row['current_status'] == 199 && $row['tracking_status'] == 1)) {
		$completedCount++;
		$completed_data[] = $row['id'];
	}
}

$progress_var =  gettableStatusArray($table_name1);

// echo '<pre>';
// print_r( $progress_var);
// echo 'close';



?>
<style>
	tr {
		height: 10px !important;

	}

	a label {
		padding: 5px, 15px !important;
	}
</style>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
		tooltipTriggerList.forEach(function(tooltipTriggerEl) {
			new bootstrap.Tooltip(tooltipTriggerEl);
		});
	});
</script>
<div class="page-body">
	<div class="row">
		<!-- xyz -->
		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-red lbl-card"><i class="fas fa-file-archive"></i> Total CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-red"><?php echo $total_count; ?></h2>

					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> Active CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-blue"><?php echo $activeCount; ?></h2>

					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-purple lbl-card"><i class="fas fa-file-archive"></i>Inprogress CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-purple"><?php echo $progressCount; ?></h2>

					</div>
				</div>
			</div>
		</div>


		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-lite-green lbl-card"><i class="fas fa-file-archive"></i> Approved CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-lite-green"><?php echo $approvedCount; ?></h2>

					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-yellow lbl-card"><i class="fas fa-file-archive"></i> Tracking CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-yellow"><?php echo $approved_inprogressCount; ?></h2>

					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-2 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-green lbl-card"><i class="fas fa-file-archive"></i> Completed CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-green"><?php echo $completedCount; ?></h2>

					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="card proj-progress-card">
		<div class="card-block">
			<div class="row">
				<div class="col-lg-2 m-auto">
					<h5 class="d-flex align-itmes-center">CSR</h5>
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
											<th></th>
										</tr>
									</thead>

									<tbody class="custom-scroll" style="height: 300px;">
										<?php if (!empty($active_data)): ?>
											<?php foreach ($active_data as $value): ?>
												<tr style="height:10px !important;">
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top'
																onerror="this.src='uploads/all/no-image.jpg'">
														<?php else: ?>
															<span alt='user image'
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<label class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</label>
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
														<label class='form-label label label-md'
															style='color: <?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['color']; ?>; 
                                      background-color: <?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['background_color']; ?>;'>
															<?php echo $progress_var[$csr_data[$value]['current_status']]; ?>
														</label>
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
												<td colspan="7">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt=""
																src="assets/images/work.png"
																style="max-height: 300px!important;">
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
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>

											<th class='text-center'>Cost</th>
											<th>Status</th>

										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">
										<?php if (!empty($inprogress_data)): ?>
											<?php foreach ($inprogress_data as $value): ?>
												<tr style="height:10px !important;">
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top'
																onerror="this.src='uploads/all/no-image.jpg'">
														<?php else: ?>
															<span alt='user image'
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<label class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</label>
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
														<label class='form-label label label-md'
															style='color: <?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['color']; ?>; 
                                      background-color: <?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['background_color']; ?>;'>
															<?php echo $progress_var[$csr_data[$value]['current_status']]; ?>
														</label>
													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="6">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt=""
																src="assets/images/work.png"
																style="max-height: 300px!important;">
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
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>

											<th class='text-center'>Cost</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">
										<?php if (!empty($approved_data)): ?>
											<?php foreach ($approved_data as $value): ?>
												<tr style="height:10px !important;">
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top'
																onerror="this.src='uploads/all/no-image.jpg'">
														<?php else: ?>
															<span alt='user image'
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<label class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</label>
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
														<label class='form-label label label-md'
															style='color: <?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['color']; ?>; 
                                      background-color: <?php echo $_SESSION['status'][$csr_data[$value]['current_status']]['background_color']; ?>;'>
															<?php echo $progress_var[$csr_data[$value]['current_status']]; ?>
														</label>
													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="6">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt=""
																src="assets/images/work.png"
																style="max-height: 300px!important;">
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
							<a class="nav-link active" data-bs-toggle="tab" href="#tab4" role="tab">Pending</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-bs-toggle="tab" href="#tab5" role="tab">In Progress</a>
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
									<tbody class="custom-scroll" style="height: 300px;">
										<?php if (!empty($tracking_pending)): ?>
											<?php foreach ($tracking_pending as $value): ?>
												<tr style="height:10px !important;">
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top'
																onerror="this.src='uploads/all/no-image.jpg'">
														<?php else: ?>
															<span alt='user image'
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<label class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</label>
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
															<label class='form-label label <?= $labelClass ?>' style='cursor: pointer;'><?= $percentage ?>%</label></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$value]['id']) ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a>
													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="6">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt=""
																src="assets/images/work.png"
																style="max-height: 300px!important;">
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
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>

											<th></th>
										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">
										<?php if (!empty($tracking_inprogress)): ?>
											<?php foreach ($tracking_inprogress as $value): ?>
												<tr style="height:10px !important;">
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top'
																onerror="this.src='uploads/all/no-image.jpg'">
														<?php else: ?>
															<span alt='user image'
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<label class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</label>
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
															<label class='form-label label <?= $labelClass ?>' style='cursor: pointer;'><?= $percentage ?>%</label></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$value]['id']) ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a>
													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="6">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt=""
																src="assets/images/work.png"
																style="max-height: 300px!important;">
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
											<th>Creator</th>
											<th>CSRs Name</th>

											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th></th>

										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">
										<?php if (!empty($completed_data)): ?>
											<?php foreach ($completed_data as $value): ?>
												<tr style="height:10px !important;">
													<td>
														<?php if (!empty($_SESSION['user_image'][$csr_data[$value]['created_by']])): ?>
															<img src='uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$value]['created_by']]; ?>'
																class='img-radius img-40 align-top'
																onerror="this.src='uploads/all/no-image.jpg'">
														<?php else: ?>
															<span alt='user image'
																class='img-radius align-top custom_txt_workentry f-12 text-center'>
																<?php echo initials($_SESSION['users'][$csr_data[$value]['created_by']]); ?>
															</span>
														<?php endif; ?>
														<label class='mt-2 ml-2'>
															<?php echo $_SESSION['users'][$csr_data[$value]['created_by']]; ?>
														</label>
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
															<label class='form-label label <?= $labelClass ?>' style='cursor: pointer;'><?= $percentage ?>%</label></a>
														<a class='ml-2' href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$value]['id']) ?>'>
															<i class='ti-eye f-20 p-2 b-radius-5'></i>
														</a>
													</td>

												</tr>
											<?php endforeach; ?>
										<?php else: ?>
											<tr>
												<td colspan="6">
													<div class="text-center">
														<p class="text-muted f-16 fw-bold m-b-30">
															<i class="fa fa-warning"></i> No Records
														</p>
														<div class="text-center px-4">
															<img class="mw-100 mh-300px" alt=""
																src="assets/images/work.png"
																style="max-height: 300px!important;">
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