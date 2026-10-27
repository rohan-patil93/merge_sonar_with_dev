<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$location_id = isset($_POST['location_id']) ? $_POST['location_id'] : '';
$employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : '';
$temp_reason = isset($_POST['temp_reason']) ? $_POST['temp_reason'] : '';
$card_return = isset($_POST['card_return']) ? $_POST['card_return'] : '';

$url = API . "?mode=getemployeetempidreport&method=get&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&location_id=" . urlencode($location_id) . "&employee_id=" . urlencode($employee_id) . "&temp_reason=" . urlencode($temp_reason) . "&card_return=" . urlencode($card_return);
$apires = callAPI($url);
$result = json_decode($apires, 1);
$rows = isset($result['rows']) ? $result['rows'] : array();

$_SESSION['download_data'] = array();
$cssdata = array();

if(count((array)$rows) > 0){
?>
<div class="col-12 table-responsive">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>Sl. No.</th>
				<th>Date</th>
				<!-- <th>ID No.</th> -->
				<th>Name</th>
				<th>Card NO</th>
				<th>Access NO</th>
				<th>In TIME</th>
				<th>Access Enable Date</th>
				<th>Out TIME</th>
				<th>Access Disable Date</th>
				<th>Card Submitted DATE</th>
				<th>SECURITY SIGN</th>
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$i = 1;
				foreach($rows as $row){
					$visit_date = isset($row['visit_date']) ? $row['visit_date'] : '';
					// $id_no = isset($row['id_no']) ? $row['id_no'] : '';
					$name = isset($row['name']) ? $row['name'] : '';
					$card_no = isset($row['card_no']) ? $row['card_no'] : '';
					$access_no = isset($row['card_no']) ? $row['card_no'] : '';
					$in_time = isset($row['in_time']) ? $row['in_time'] : '';
					$access_enable_date = isset($row['in_time']) ? $row['in_time'] : '';
					$out_time = isset($row['out_time']) ? $row['out_time'] : '';
					$access_disable_date = isset($row['out_time']) ? $row['out_time'] : '';
					$card_submitted_date = isset($row['card_submitted_date']) ? $row['card_submitted_date'] : '';
					$security_sign = isset($row['security_sign']) ? $row['security_sign'] : '';
					$remarks = isset($row['remarks']) ? $row['remarks'] : '';

					$visit_date_display = ($visit_date != '' && $visit_date != '0000-00-00') ? fromDate($visit_date) : '-';
					$in_display = ($in_time != '' && $in_time != '0000-00-00 00:00:00') ? formatAmcScheduleDateTime($in_time) : '-';
					$out_display = ($out_time != '' && $out_time != '0000-00-00 00:00:00') ? formatAmcScheduleDateTime($out_time) : '-';
					$access_enable_display = ($access_enable_date != '' && $access_enable_date != '0000-00-00' && $access_enable_date != '0000-00-00 00:00:00') ? fromDate($access_enable_date) : '';
					$access_disable_display = ($access_disable_date != '' && $access_disable_date != '0000-00-00' && $access_disable_date != '0000-00-00 00:00:00') ? fromDate($access_disable_date) : '';
					$card_sub_display = ($card_submitted_date != '' && $card_submitted_date != '0000-00-00 00:00:00') ? formatAmcScheduleDateTime($card_submitted_date) : '-';

					$cssdata[] = array(
						'Sl. No.' => $i,
						'Date' => $visit_date_display,
						// 'ID No.' => $id_no,
						'Name' => $name,
						'Card NO' => $card_no,
						'Access NO' => $access_no,
						'In TIME' => $in_display,
						'Access Enable Date' => $access_enable_display,
						'Out TIME' => $out_display,
						'Access Disable Date' => $access_disable_display,
						'Card Submitted DATE' => $card_sub_display,
						'SECURITY SIGN' => $security_sign,
						'Remarks' => $remarks
					);
			?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo htmlspecialchars($visit_date_display); ?></td>
				<?php /*	<td><?php echo ($id_no != '') ? htmlspecialchars($id_no) : '-'; ?></td> */ ?>
					<td><?php echo ($name != '') ? htmlspecialchars($name) : '-'; ?></td>
					<td><?php echo ($card_no != '') ? htmlspecialchars($card_no) : '-'; ?></td>
					<td><?php echo ($access_no != '') ? htmlspecialchars($access_no) : ''; ?></td>
					<td><?php echo htmlspecialchars($in_display); ?></td>
					<td><?php echo ($access_enable_display != '') ? htmlspecialchars($access_enable_display) : ''; ?></td>
					<td><?php echo htmlspecialchars($out_display); ?></td>
					<td><?php echo ($access_disable_display != '') ? htmlspecialchars($access_disable_display) : ''; ?></td>
					<td><?php echo htmlspecialchars($card_sub_display); ?></td>
					<td><?php echo ($security_sign != '') ? htmlspecialchars($security_sign) : '-'; ?></td>
					<td><?php echo ($remarks != '') ? htmlspecialchars($remarks) : '-'; ?></td>
				</tr>
			<?php
					$i++;
				}
				$_SESSION['download_data'] = $cssdata;
			?>
		</tbody>
	</table>
</div>
<?php }else{ ?>
<div class="card-px text-center py-20 mt-2">
	<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Employee Temp ID Records </p>
	<div class="text-center px-4">
		<img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
	</div>
</div>
<?php } ?>
