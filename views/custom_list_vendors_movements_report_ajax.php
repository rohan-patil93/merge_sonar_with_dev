<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$location_id = isset($_POST['location_id']) ? $_POST['location_id'] : '';
$department_id = isset($_POST['department_id']) ? $_POST['department_id'] : '';
$movement = isset($_POST['movement']) ? $_POST['movement'] : '';
$search = isset($_POST['search']) ? $_POST['search'] : '';

$url = API . "?mode=getvendorsmovementsreport&method=get&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&location_id=" . urlencode($location_id) . "&department_id=" . urlencode($department_id) . "&movement=" . urlencode($movement) . "&search=" . urlencode($search);
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
				<th scope="col"><?= __('Sl.No'); ?></th>
				<th scope="col"><?= __('Name'); ?></th>
				<th scope="col"><?= __('Contact No.'); ?></th>
				<th scope="col"><?= __('Government ID'); ?></th>
				<th scope="col"><?= __('Coming From'); ?></th>
				<th scope="col"><?= __('Purpose'); ?></th>
				<th scope="col"><?= __('Whom to Meet'); ?></th>
				<th scope="col"><?= __('Card No'); ?></th>
				<th scope="col"><?= __('In Time'); ?></th>
				<th scope="col"><?= __('Out Time'); ?></th>
				<th scope="col"><?= __('Security Sign'); ?></th>
				<th scope="col"><?= __('Remarks'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$i = 1;
				foreach($rows as $row){
					$name = isset($row['name']) ? $row['name'] : '';
					$contact_no = isset($row['contact_no']) ? $row['contact_no'] : '';
					$government_id = isset($row['government_id']) ? $row['government_id'] : '';
					$coming_from = isset($row['coming_from']) ? $row['coming_from'] : '';
					$purpose = isset($row['purpose']) ? $row['purpose'] : '';
					$whom = isset($row['whom_to_meet']) ? $row['whom_to_meet'] : '';
					$card_no = isset($row['card_no']) ? $row['card_no'] : '';
					$in_time = isset($row['in_time']) ? $row['in_time'] : '';
					$out_time = isset($row['out_time']) ? $row['out_time'] : '';
					$security_sign = isset($row['security_sign']) ? $row['security_sign'] : '';
					$remarks = isset($row['remarks']) ? $row['remarks'] : '';

					$in_display = ($in_time != '' && $in_time != '0000-00-00 00:00:00') ? formatAmcScheduleDateTime($in_time) : '-';
					$out_display = ($out_time != '' && $out_time != '0000-00-00 00:00:00') ? formatAmcScheduleDateTime($out_time) : '-';

					$cssdata[] = array(
						'Sl.No' => $i,
						'Name' => $name,
						'Contact No.' => $contact_no,
						'Government ID' => $government_id,
						'Coming From' => $coming_from,
						'Purpose' => $purpose,
						'Whom to Meet' => $whom,
						'Card No' => $card_no,
						'In Time' => $in_display,
						'Out Time' => $out_display,
						'Security Sign' => $security_sign,
						'Remarks' => $remarks
					);
			?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo ($name != '') ? htmlspecialchars($name) : '-'; ?></td>
					<td><?php echo ($contact_no != '') ? htmlspecialchars($contact_no) : '-'; ?></td>
					<td><?php echo ($government_id != '') ? htmlspecialchars($government_id) : '-'; ?></td>
					<td><?php echo ($coming_from != '') ? htmlspecialchars($coming_from) : '-'; ?></td>
					<td><?php echo ($purpose != '') ? htmlspecialchars($purpose) : '-'; ?></td>
					<td><?php echo ($whom != '') ? htmlspecialchars($whom) : '-'; ?></td>
					<td><?php echo ($card_no != '') ? htmlspecialchars($card_no) : '-'; ?></td>
					<td><?php echo htmlspecialchars($in_display); ?></td>
					<td><?php echo htmlspecialchars($out_display); ?></td>
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
	<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> <?= __('No Vendors Movements'); ?> </p>
	<div class="text-center px-4">
		<img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
	</div>
</div>
<?php } ?>
