<?php
$insertOption = 'COM_INSERT_WORKFLOW_CONFIGURATIONS_NEW';

$data1 = array();
$data1['where'] = array(
	'process_name' => 'document_management',
	'company_id' => $_SESSION['company_id'],
	'is_active' => '0'
);
$data = urlencode(json_encode($data1));
$url = API . "?mode=gettabledetailswithconditions&table=workflow_configurations_new&data=" . $data;
$rows = json_decode(callAPI($url), 1);
if (!is_array($rows)) {
	$rows = array();
}
?>
<div class="page-body">
	<div class="card">
		<div class="card-header">
			<h5>Document Management Workflow Configurations</h5>
			<div class="card-header-right">
				<a href="index.php?option=<?php echo $insertOption; ?>&table_name=document_management" class="btn btn-success waves-effect waves-light mb-2 desktop-view">
					<i class="icofont icofont-plus"></i> Insert
				</a>
				<a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
					<i class="icofont icofont-reply"></i> Back
				</a>
			</div>
		</div>
		<div class="card-body p-4">
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>S.No</th>
							<th>Table Name</th>
							<th>Description</th>
							<th>Workflow Level</th>
							<th>Edit</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count((array)$rows) > 0) {
							$serial = 1;
							foreach ($rows as $row) {
								$row = is_array($row) ? $row : (array)$row;
								$editRid = deocdeId('id:' . ($row['id'] ?? 0));
						?>
							<tr>
								<td><?php echo $serial; ?></td>
								<td><?php echo htmlspecialchars((string)($row['process_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars((string)($row['process_description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars((string)($row['workflow_level'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
								<td>
									<a class="m-r-5" href="index.php?option=<?php echo $insertOption; ?>&Rid=<?php echo $editRid; ?>" title="Edit">
										<i class="fa fa-edit p-2 b-radius-5"></i>
									</a>
								</td>
							</tr>
						<?php
								$serial++;
							}
						} else { ?>
							<tr>
								<td colspan="5">
									<div class="text-center">
										<p class="text-muted f-16 fw-bold m-b-30">
											<i class="fa fa-warning"></i> No Workflow Configuration Records
										</p>
										<div class="text-center px-4">
											<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>
									</div>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
