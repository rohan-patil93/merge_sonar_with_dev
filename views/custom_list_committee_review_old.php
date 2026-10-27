<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$table_name = 'csr_proposal_data';
$url = API . "?mode=getcsrdata&method=get&table=" . $table_name . "&id=" . $_SESSION['userid'];
$csr_response = callAPI($url);
$response = json_decode($csr_response, 1);
$length = count($response) ?? 0;

$table_name1 = 'application_users';
$vdata1['order'] = "id ASC";
// $vdata1['limit'] = "0,5";
$vdata1["where"] = array('company_id' => $_SESSION['company_id'], 'is_active' => '0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API . "?mode=gettabledetailswithconditions&table=" . $table_name1 . "&data=" . $vdata;
$users_response = callAPI($vurl);
$users_response = json_decode($users_response, 1);
// print_r($users_response);
$users_array = array();
foreach ($users_response as $row) {
    $users_array[$row['id']] = $row['display_name'];
}

// if (!$response || !is_array($response)) {
// 	die("Invalid response or no data received.");
// }

// Initialize counters
$total_count = 0;
$approved_count = 0;
$inprogress_count = 0;
$completed_count = 0;

$completedCount = 0;
$approvedCount = 0;
$progressCount = 0;

$pendingTableRows = '';
$approvedTableRows = '';
$progressTableRows = '';
$approvedForAllTableRows = '';

$completedTableRows = '';
$approvedAndProgress = '';


if ($response) {
	foreach ($response as $row) {
		$total_count += $row['total_budget_grand_total'];
		///echo "IIIIII";
		if (isset($row['current_status']) && $row['current_status'] == ST_APPROVED2 && (!isset($row['tracking_status']) || $row['tracking_status'] == 0)) {

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
			$approved_count += $row['total_budget_grand_total'];
			$approvedCount++;
			$approvedTableRows .= "
            <tr>                
                <td><a class='text-primary m-r-5'
                        href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=" . deocdeId('id:' . $row['id']) . "'>
                       {$row['subject']} 
                    </a></td>
                <td>" . $users_array[$row['created_by']] . "</td>
                <td>" . fromDate($row['date']) . "</td>
				<td>" . fromDate($row['date']) . "</td>
				<td class='text-end'>" . customeummarmonyformate($row['total_budget_grand_total']) . "</td>
                
            </tr>";
		}

		if (isset($row['current_status']) && ($row['current_status'] != ST_APPROVED2 || $row['current_status'] != ST_ACTIVE || $row['current_status'] != ST_SEND_BACK || $row['current_status'] != ST_APPROVED ) ) {
			$progressCount++;
			$inprogress_count += $row['total_budget_grand_total'];
			///echo "UUUUUUUUU";
			$progressTableRows .= "
            <tr>
                <td><a class='text-primary m-r-5'
                        href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=" . deocdeId('id:' . $row['id']) . "'>
                       {$row['subject']} 
                    </a></td>
                <td>" . $users_array[$row['created_by']].$row['current_status'] . "</td>
                <td>" . fromDate($row['date']) . "</td>
				<td>" . fromDate($row['date']) . "</td>
				<td class='text-end'>" . customeummarmonyformate($row['total_budget_grand_total']) . "</td>
                
            </tr>";
		}

		if (isset($row['current_status']) && $row['current_status'] == ST_APPROVED1 && $row['tracking_status'] == 0) {

			$pendingTableRows .= "
            <tr>
                <td><a class='text-primary m-r-5'
                        href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=" . deocdeId('id:' . $row['id']) . "'>
                       {$row['subject']} 
                    </a></td>
                <td>" . $users_array[$row['created_by']]. $row['current_status'] . "</td> 
                <td>" . fromDate($row['date']) . "</td>
                <td>" . fromDate($row['date']) . "</td>
                <td class='text-end'>" . customeummarmonyformate($row['total_budget_grand_total']) . "</td>
                
            </tr>";
		}
		if (isset($row['current_status']) && $row['current_status'] == ST_APPROVED && $row['tracking_status'] == 0) {

			$approvedForAllTableRows .= "
            <tr>
                <td><a class='text-primary m-r-5'
                        href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=" . deocdeId('id:' . $row['id']) . "'>
                       {$row['subject']} 
                    </a></td>
                <td>" . $users_array[$row['created_by']] . "</td> 
                <td>" . fromDate($row['date']) . "</td>
                <td>" . fromDate($row['date']) . "</td>
                <td class='text-end'>" . customeummarmonyformate($row['total_budget_grand_total']) . "</td>
                
            </tr>";

            $approvedAndProgress .= "
            <tr>
                <td><a class='text-primary m-r-5'
                        href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=" . deocdeId('id:' . $row['id']) . "'>
                       {$row['subject']} 
                    </a></td>
                <td>" . $users_array[$row['created_by']] . "</td> 
                <td>" . fromDate($row['date']) . "</td>
                <td>" . fromDate($row['date']) . "</td>
                <td class='text-end'>" . customeummarmonyformate($row['total_budget_grand_total']) . "</td>
                <td >
					<a 	href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=" . deocdeId('id:' . $row['id']) . "'>
                    <label class='form-label label {$labelClass}'  style='cursor: pointer;'>" . round($percentage) . "%</label></a>
                </td>
                
            </tr>";
		}

		if (isset($row['tracking_status']) && $row['tracking_status'] == 1) {
			$completedCount++;
			$completed_count += $row['total_budget_grand_total'];
			$completedTableRows .= "
            <tr>
                <td><a class='text-primary m-r-5'
                        href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=" . deocdeId('id:' . $row['id']) . "'>
                       {$row['subject']} 
                    </a></td>
                <td>" . $users_array[$row['created_by']] . "</td> 
                <td>" . fromDate($row['date']) . "</td>
                <td>" . fromDate($row['date']) . "</td>
                <td class='text-end'>" . customeummarmonyformate($row['total_budget_grand_total']) . "</td>
                
            </tr>";
		}
		
	}
}
?>
<style>
	i {
		cursor: pointer;
	}
</style>
<div class="page-body">
	<!-- Pradeep HTML Start here -->
	<!-- <div class="card proj-progress-card">
		<div class="card-block"> -->
	<div class="row">
		<!-- xyz -->
		<?php 
			$total_count = $total_count / 100000;
			$total_count = number_format($total_count, 2);

			$approved_count = $approved_count / 100000;
			$approved_count = number_format($approved_count, 2);

			$inprogress_count = $inprogress_count / 100000;
			$inprogress_count = number_format($inprogress_count, 2);

			$completed_count = $completed_count / 100000;
			$completed_count = number_format($completed_count, 2);

 		?>

		<div class="col-xl-3 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-red lbl-card"><i class="fas fa-file-archive"></i> Total CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-red"><?php echo $total_count; ?></h2>
						<p class="m-b-0 d-inline-block">Lakhs</p>
						<p class="m-b-0 m-t-15">No. Of CSRs <i class="m-r-10 f-18 text-c-red " style="font-style: normal;!important "><?php echo $length ?></i></p>
					</div>
				</div>
			</div>
		</div>


		<div class="col-xl-3 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> Approved CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-blue"><?php echo $approved_count; ?></h2>
						<p class="m-b-0 d-inline-block">Lakhs</p>
						<p class="m-b-0 m-t-15">No. Of CSRs <i class="m-r-10 f-18 text-c-blue" style="font-style: normal;!important "> <?php echo $approvedCount ?></i></p>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-green lbl-card"><i class="fas fa-file-archive"></i> In Progress CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-green"><?php echo $inprogress_count; ?></h2>
						<p class="m-b-0 d-inline-block">Lakhs</p>
						<p class="m-b-0 m-t-15"> No. Of CSRs <i class=" m-r-10 f-18 text-c-green" style="font-style: normal;!important "><?php echo $progressCount ?></i></p>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-md-6">
			<div class="card ticket-card">
				<div class="card-body">
					<p class="m-b-30 bg-c-yellow lbl-card"><i class="fas fa-file-archive"></i> Completed CSRs</p>
					<div class="text-center">
						<h2 class="m-b-0 d-inline-block text-c-yellow"><?php echo $completed_count; ?></h2>
						<p class="m-b-0 d-inline-block">Lakhs</p>
						<p class="m-b-0 m-t-15"> No. Of CSRs <i class=" m-r-10 f-18 text-c-yellow" style="font-style: normal;!important "><?php echo $completedCount ?></i></p>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- xyz -->
	<div class="card proj-progress-card">
		<div class="card-block">
			<div class="row">
				<div class="col-lg-2 m-auto"><h5 class="d-flex align-itmes-center">CSR</h5></div>
				<div class="col-lg-5"></div>
				<div class="col-lg-5">
					<ul class="nav md-tabs infisuite_tab mt-2 ml-2 d-flex justify-content-end" role="tablist">
	                    <li class="nav-item">
	                        <a class="nav-link active" data-bs-toggle="tab" href="#tab0" role="tab">Pending</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link " data-bs-toggle="tab" href="#tab1" role="tab"> Approved</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link " data-bs-toggle="tab" href="#tab2" role="tab">In Progress</a>
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
											<th>CSRs Name</th>
											<th>Creator</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">

										<?php echo !empty($pendingTableRows) ? $pendingTableRows : '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>'; ?>
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
											<th>CSRs Name</th>
											<th>Creator</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											
										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">

										<?php echo !empty($approvedTableRows) ? $approvedTableRows : '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>'; ?>
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
											<th>CSRs Name</th>
											<th>Creator</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">

										<?php echo !empty($progressTableRows) ? $progressTableRows : '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>'; ?>
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
											<th>CSRs Name</th>
											<th>Creator</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">

										<?php echo !empty($approvedForAllTableRows) ? $approvedForAllTableRows : '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>'; ?>
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
				<div class="col-lg-2 m-auto"><h5 class="d-flex align-itmes-center">CSR Tracking</h5></div>
				<div class="col-lg-5"></div>
				<div class="col-lg-5">
					<ul class="nav md-tabs infisuite_tab mt-2 ml-2 d-flex justify-content-end" role="tablist">
	                    <li class="nav-item">
	                        <a class="nav-link active" data-bs-toggle="tab" href="#tab5" role="tab">In Progress</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link " data-bs-toggle="tab" href="#tab6" role="tab"> Completed</a>
	                    </li>
	                    
                	</ul>
				</div>
			</div>

			<div class="row">
				<div class="tab-content tabs mt-4">
			       	<div class="tab-pane active" id="tab5" role="tabpanel">
				       	<div class="card table-card">
				    		<div class="table-responsive">
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th>CSRs Name</th>
											<th>Creator</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											<th></th>
										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">

										<?php echo !empty($approvedAndProgress) ? $approvedAndProgress : '<tr><td colspan="6" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>'; ?>
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
											<th>CSRs Name</th>
											<th>Creator</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th class='text-center'>Cost</th>
											
										</tr>
									</thead>
									<tbody class="custom-scroll" style="height: 300px;">

										<?php echo !empty($completedTableRows) ? $completedTableRows : '<tr><td colspan="5" ><div class="text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
										<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
										</div></td></tr>'; ?>
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

</script>