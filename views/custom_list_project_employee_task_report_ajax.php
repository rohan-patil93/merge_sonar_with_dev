<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
$employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : '';
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';

$url = API . "?mode=getprojectemployeetaskreport&method=get&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&project_id=" . urlencode($project_id) . "&employee_id=" . urlencode($employee_id);
$apires = callAPI($url);
$result = json_decode($apires, 1);
$tasks = isset($result['tasks']) ? $result['tasks'] : array();
$progress_var = isset($result['project_status']) ? $result['project_status'] : array();
// print_r($progress_var);

$_SESSION['download_data'] = array();
$cssdata = array();
$today = date('Y-m-d');
?>
<div class="col-12 table-responsive">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>Sl.No</th>
				<th>Employee name</th>
				<th>Project name</th>
				<th>Task name</th>
				<th>Planned start</th>
				<th>Plan finish date</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count((array)$tasks) > 0){
				$i = 1;
				foreach($tasks as $row){
					$display_name = isset($row['display_name']) ? $row['display_name'] : '';
					$emp_code = isset($row['emp_code']) ? $row['emp_code'] : '';
					$image = isset($row['image']) ? $row['image'] : '';
					$project_name = isset($row['project_name']) ? $row['project_name'] : '';
					$task_name = isset($row['task_name']) ? $row['task_name'] : '';
					$planned_start = isset($row['planned_start']) ? $row['planned_start'] : '';
					$planned_finish = isset($row['planned_finish']) ? $row['planned_finish'] : '';
					$current_status = isset($row['current_status']) ? $row['current_status'] : '';

					$row_style = '';
					if($planned_finish != '' && $planned_finish != '0000-00-00' && $planned_finish < $today && $current_status < 199){
						$row_style = 'background-color:#f8d7da;';
					}

					$planned_start_display = ($planned_start != '' && $planned_start != '0000-00-00') ? fromDate($planned_start) : '-';
					$planned_finish_display = ($planned_finish != '' && $planned_finish != '0000-00-00') ? fromDate($planned_finish) : '-';

					$status_label = (isset($progress_var[$current_status]['name']) && $progress_var[$current_status]['name'] != '') ? $progress_var[$current_status]['name'] : $current_status;
					$status_color = isset($_SESSION['status'][$current_status]['color']) ? $_SESSION['status'][$current_status]['color'] : '';
					$status_bg = isset($_SESSION['status'][$current_status]['background_color']) ? $_SESSION['status'][$current_status]['background_color'] : '';

					$cssdata[] = array(
						'Sl.No' => $i,
						'Employee name' => $display_name,
						'Project name' => $project_name,
						'Task name' => $task_name,
						'Planned start' => $planned_start_display,
						'Plan finish date' => $planned_finish_display,
						'Status' => $status_label
					);
			?>
				<tr style="<?php echo $row_style; ?>">
					<td><?php echo $i; ?></td>
					<td>
						<div class="align-middle">
							<?php if($image != ""){ ?>
								<img src="uploads/all/<?php echo $image; ?>" class="img-radius img-40 align-top m-r-15" onerror="this.src='uploads/all/no-image.jpg';">
							<?php } else { ?>
								<span alt="user image" class="img-radius img-40 align-top m-r-10 custom_txt_round pt-1 f-12 text-center"><?php echo initials($display_name); ?></span>
							<?php } ?>
							<div class="d-inline-block width73 text-primary">
								<h6 class="mb-0"><?php echo ($display_name != '') ? $display_name : '-'; ?></h6>
								<p class="text-muted m-b-0"><?php echo $emp_code; ?></p>
							</div>
						</div>
					</td>
					<td><?php echo ($project_name != '') ? htmlspecialchars($project_name) : '-'; ?></td>
					<td><?php echo ($task_name != '') ? htmlspecialchars($task_name) : '-'; ?></td>
					<td><?php echo htmlspecialchars($planned_start_display); ?></td>
					<td><?php echo htmlspecialchars($planned_finish_display); ?></td>
					<td>
						<label class="form-label label label-md csr-status-label"
							data-color="<?php echo $status_color; ?>"
							data-bg="<?php echo $status_bg; ?>">
							<?php echo $status_label; ?>
						</label>
					</td>
				</tr>
			<?php
					$i++;
				}
				$_SESSION['download_data'] = $cssdata;
			}else{ ?>
				<tr>
					<td colspan="7" class="text-center text-muted">
					<div class="card-px text-center py-20 mt-2">
	<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Project Employee Tasks found </p>
	<div class="text-center px-4">
		<img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
	</div>
</div></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
