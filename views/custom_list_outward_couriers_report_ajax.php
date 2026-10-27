<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$location_id = isset($_POST['location_id']) ? $_POST['location_id'] : '';
$tracking_no = isset($_POST['tracking_no']) ? $_POST['tracking_no'] : '';
$courier_company = isset($_POST['courier_company']) ? $_POST['courier_company'] : '';
$taken_by = isset($_POST['taken_by']) ? $_POST['taken_by'] : '';
$current_status = isset($_POST['current_status']) ? $_POST['current_status'] : '';

$url = API . "?mode=getoutwardcouriersreport&method=get&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&location_id=" . urlencode($location_id) . "&tracking_no=" . urlencode($tracking_no) . "&courier_company=" . urlencode($courier_company) . "&taken_by=" . urlencode($taken_by) . "&current_status=" . urlencode($current_status);
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
				<th>Sl.No</th>
				<th>Date</th>
				<th>To Address</th>
				<th>From Address</th>
				<th>Tracking No</th>
				<th>Vehicle No</th>
				<th>Description of Materials</th>
				<th>Qty.</th>
				<th>Taken By Name</th>
				<th>Taken By Date</th>
				<th>Taken By Time</th>
				<th>Taken By Contact No</th>
				<th>Security Sign</th>
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$i = 1;
				foreach($rows as $row){
					$courier_date = isset($row['courier_date']) ? $row['courier_date'] : '';
					$to_address = isset($row['to_address']) ? $row['to_address'] : '';
					$from_address = isset($row['from_address']) ? $row['from_address'] : '';
					$tracking_no_val = isset($row['tracking_no']) ? $row['tracking_no'] : '';
					$vehicle_no = isset($row['vehicle_no']) ? $row['vehicle_no'] : '';
					$description = isset($row['description']) ? $row['description'] : '';
					$qty = isset($row['qty']) ? $row['qty'] : '';
					$taken_by_name = isset($row['taken_by_name']) ? $row['taken_by_name'] : '';
					$taken_by_date = isset($row['taken_by_date']) ? $row['taken_by_date'] : '';
					$taken_by_time = isset($row['taken_by_time']) ? $row['taken_by_time'] : '';
					$taken_by_contact = isset($row['taken_by_contact']) ? $row['taken_by_contact'] : '';
					$security_sign = isset($row['security_sign']) ? $row['security_sign'] : '';
					$remarks = isset($row['remarks']) ? $row['remarks'] : '';

					$courier_date_display = ($courier_date != '' && $courier_date != '0000-00-00') ? fromDate($courier_date) : '-';
					$taken_by_date_display = ($taken_by_date != '' && $taken_by_date != '0000-00-00') ? fromDate($taken_by_date) : '-';
					$taken_by_time_display = ($taken_by_time != '') ? $taken_by_time : '-';

					$cssdata[] = array(
						'Sl.No' => $i,
						'Date' => $courier_date_display,
						'To Address' => $to_address,
						'From Address' => $from_address,
						'Tracking No' => $tracking_no_val,
						'Vehicle No' => $vehicle_no,
						'Description of Materials' => $description,
						'Qty.' => $qty,
						'Taken By Name' => $taken_by_name,
						'Taken By Date' => $taken_by_date_display,
						'Taken By Time' => $taken_by_time_display,
						'Taken By Contact No' => $taken_by_contact,
						'Security Sign' => $security_sign,
						'Remarks' => $remarks
					);
			?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo htmlspecialchars($courier_date_display); ?></td>
					<td><?php echo ($to_address != '') ? htmlspecialchars($to_address) : '-'; ?></td>
					<td><?php echo ($from_address != '') ? htmlspecialchars($from_address) : '-'; ?></td>
					<td><?php echo ($tracking_no_val != '') ? htmlspecialchars($tracking_no_val) : '-'; ?></td>
					<td><?php echo ($vehicle_no != '') ? htmlspecialchars($vehicle_no) : ''; ?></td>
					<td><?php echo ($description != '') ? htmlspecialchars($description) : '-'; ?></td>
					<td><?php echo ($qty != '') ? htmlspecialchars($qty) : '-'; ?></td>
					<td><?php echo ($taken_by_name != '') ? htmlspecialchars($taken_by_name) : '-'; ?></td>
					<td><?php echo htmlspecialchars($taken_by_date_display); ?></td>
					<td><?php echo htmlspecialchars($taken_by_time_display); ?></td>
					<td><?php echo ($taken_by_contact != '') ? htmlspecialchars($taken_by_contact) : '-'; ?></td>
					<td><?php echo ($security_sign != '') ? htmlspecialchars($security_sign) : ''; ?></td>
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
	<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Outward Couriers </p>
	<div class="text-center px-4">
		<img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
	</div>
</div>
<?php } ?>
