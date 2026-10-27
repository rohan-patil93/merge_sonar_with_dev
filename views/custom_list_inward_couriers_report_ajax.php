<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$location_id = isset($_POST['location_id']) ? $_POST['location_id'] : '';
$tracking_no = isset($_POST['tracking_no']) ? $_POST['tracking_no'] : '';
$courier_company = isset($_POST['courier_company']) ? $_POST['courier_company'] : '';
$received_by = isset($_POST['received_by']) ? $_POST['received_by'] : '';
$current_status = isset($_POST['current_status']) ? $_POST['current_status'] : '';

$url = API . "?mode=getinwardcouriersreport&method=get&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&location_id=" . urlencode($location_id) . "&tracking_no=" . urlencode($tracking_no) . "&courier_company=" . urlencode($courier_company) . "&received_by=" . urlencode($received_by) . "&current_status=" . urlencode($current_status);
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
				<th>Coming Date</th>
				<th>Coming Time</th>
				<th>Tracking No</th>
				<th>Vehicle No</th>
				<th>Coming From</th>
				<th>To Address</th>
				<th>Description of Materials</th>
				<th>Qty.</th>
				<th>Brought By Name</th>
				<th>Received By Name</th>
				<th>Received Date</th>
				<th>Received Time</th>
				<th>Security Sign</th>
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$i = 1;
				foreach($rows as $row){
					$coming_date = isset($row['coming_date']) ? $row['coming_date'] : '';
					$coming_time = isset($row['coming_time']) ? $row['coming_time'] : '';
					$tracking_no_val = isset($row['tracking_no']) ? $row['tracking_no'] : '';
					$vehicle_no = isset($row['vehicle_no']) ? $row['vehicle_no'] : '';
					$coming_from = isset($row['coming_from']) ? $row['coming_from'] : '';
					$to_address = isset($row['to_address']) ? $row['to_address'] : '';
					$description = isset($row['description']) ? $row['description'] : '';
					$qty = isset($row['qty']) ? $row['qty'] : '';
					$brought_by_name = isset($row['brought_by_name']) ? $row['brought_by_name'] : '';
					$received_by_name = isset($row['received_by_name']) ? $row['received_by_name'] : '';
					$received_date = isset($row['received_date']) ? $row['received_date'] : '';
					$received_time = isset($row['received_time']) ? $row['received_time'] : '';
					$security_sign = isset($row['security_sign']) ? $row['security_sign'] : '';
					$remarks = isset($row['remarks']) ? $row['remarks'] : '';

					$coming_date_display = ($coming_date != '' && $coming_date != '0000-00-00') ? fromDate($coming_date) : '-';
					$received_date_display = ($received_date != '' && $received_date != '0000-00-00') ? fromDate($received_date) : '-';
					$coming_time_display = ($coming_time != '') ? $coming_time : '-';
					$received_time_display = ($received_time != '') ? $received_time : '-';

					$cssdata[] = array(
						'Sl.No' => $i,
						'Coming Date' => $coming_date_display,
						'Coming Time' => $coming_time_display,
						'Tracking No' => $tracking_no_val,
						'Vehicle No' => $vehicle_no,
						'Coming From' => $coming_from,
						'To Address' => $to_address,
						'Description of Materials' => $description,
						'Qty.' => $qty,
						'Brought By Name' => $brought_by_name,
						'Received By Name' => $received_by_name,
						'Received Date' => $received_date_display,
						'Received Time' => $received_time_display,
						'Security Sign' => $security_sign,
						'Remarks' => $remarks
					);
			?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo htmlspecialchars($coming_date_display); ?></td>
					<td><?php echo htmlspecialchars($coming_time_display); ?></td>
					<td><?php echo ($tracking_no_val != '') ? htmlspecialchars($tracking_no_val) : '-'; ?></td>
					<td><?php echo ($vehicle_no != '') ? htmlspecialchars($vehicle_no) : ''; ?></td>
					<td><?php echo ($coming_from != '') ? htmlspecialchars($coming_from) : '-'; ?></td>
					<td><?php echo ($to_address != '') ? htmlspecialchars($to_address) : '-'; ?></td>
					<td><?php echo ($description != '') ? htmlspecialchars($description) : '-'; ?></td>
					<td><?php echo ($qty != '') ? htmlspecialchars($qty) : '-'; ?></td>
					<td><?php echo ($brought_by_name != '') ? htmlspecialchars($brought_by_name) : '-'; ?></td>
					<td><?php echo ($received_by_name != '') ? htmlspecialchars($received_by_name) : '-'; ?></td>
					<td><?php echo htmlspecialchars($received_date_display); ?></td>
					<td><?php echo htmlspecialchars($received_time_display); ?></td>
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
	<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Inward Couriers </p>
	<div class="text-center px-4">
		<img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
	</div>
</div>
<?php } ?>
