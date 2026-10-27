<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$admin_type = isset($_POST['admin_type']) ? $_POST['admin_type'] : '1';
$user_ids = isset($_POST['user_id']) ? $_POST['user_id'] : array();
$statuses = isset($_POST['status']) ? $_POST['status'] : array();

if (is_array($user_ids)) {
	$user_ids_string = implode(',', $user_ids);
} else {
	$user_ids_string = $user_ids;
}
if (is_array($statuses)) {
	$status_string = implode(',', $statuses);
} else {
	$status_string = $statuses;
}

$url = API . "?mode=getfieldworkoperationsreport&method=get"
	. "&from_date=" . urlencode($from_date)
	. "&to_date=" . urlencode($to_date)
	. "&userids=" . urlencode($user_ids_string)
	. "&status=" . urlencode($status_string)
	. "&admin_type=" . urlencode($admin_type);
$apires = callAPI($url);
$result = json_decode($apires, 1);
$rows = isset($result['rows']) ? $result['rows'] : array();
$summary = isset($result['summary']) ? $result['summary'] : array();
$planned = isset($summary['planned']) ? (int)$summary['planned'] : 0;
$visited = isset($summary['visited']) ? (int)$summary['visited'] : 0;
$pending = isset($summary['pending']) ? (int)$summary['pending'] : 0;
$extra = isset($summary['extra']) ? (int)$summary['extra'] : 0;

$_SESSION['download_data'] = array();
$cssdata = array();

function fworFormatDate($date) {
	if ($date == '' || $date == '0000-00-00' || $date == null) {
		return '-';
	}
	if (function_exists('fromDate')) {
		return fromDate($date);
	}
	return date('d M Y', strtotime($date));
}

function fworFormatTime($time) {
	if ($time == '' || $time == null || $time == '00:00:00') {
		return '-';
	}
	return date('h:i A', strtotime($time));
}

function fworStatusClass($status) {
	$status = strtolower((string)$status);
	if ($status == 'visited') {
		return 'fwor-status-visited';
	}
	if ($status == 'pending') {
		return 'fwor-status-pending';
	}
	if ($status == 'extra') {
		return 'fwor-status-extra';
	}
	return '';
}
?>
<div class="col-12">
	<div class="fwor-summary">
		<div class="fwor-chip fwor-chip-planned">
			<span class="fwor-chip-label">Planned</span>
			<span class="fwor-chip-value"><?php echo $planned; ?></span>
		</div>
		<div class="fwor-chip fwor-chip-visited">
			<span class="fwor-chip-label">Visited</span>
			<span class="fwor-chip-value"><?php echo $visited; ?></span>
		</div>
		<div class="fwor-chip fwor-chip-pending">
			<span class="fwor-chip-label">Pending</span>
			<span class="fwor-chip-value"><?php echo $pending; ?></span>
		</div>
		<div class="fwor-chip fwor-chip-extra">
			<span class="fwor-chip-label">Extra</span>
			<span class="fwor-chip-value"><?php echo $extra; ?></span>
		</div>
	</div>
</div>
<?php if (count((array)$rows) > 0) { ?>
<div class="col-12 table-responsive">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>Sl.No</th>
				<th>Date</th>
				<th>Time</th>
				<th>User</th>
				<th>Customer</th>
				<th>Contact</th>
				<th>Location</th>
				<th>Coordinates</th>
				<th>Geo Location</th>
				<th>Status</th>
				<th>Map</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$i = 1;
				if (count((array)$rows) > 0) {
					foreach ($rows as $row) {
						$visit_date = isset($row['visit_date']) ? $row['visit_date'] : '';
						$visit_time = isset($row['visit_time']) ? $row['visit_time'] : '';
						$user_name = isset($row['user_name']) ? $row['user_name'] : '';
						$customer = isset($row['customer']) ? $row['customer'] : '';
						$contact = isset($row['contact']) ? $row['contact'] : '';
						$location = isset($row['location']) ? $row['location'] : '';
						$lat = isset($row['lat']) ? $row['lat'] : '';
						$lng = isset($row['lng']) ? $row['lng'] : '';
						$geo_location = isset($row['geo_location']) ? $row['geo_location'] : '';
						$status = isset($row['status']) ? $row['status'] : '';

						$date_display = fworFormatDate($visit_date);
						$time_display = fworFormatTime($visit_time);
						$coords_csv = '';
						if ($lat != '' && $lng != '') {
							$coords = $lat . ', ' . $lng;
							$coords_csv = $lat . ' / ' . $lng;
						}
						$geo_csv = str_replace(array(',', '_'), ' ', (string)$geo_location);
						$tooltip = 'Person: ' . ($customer != '' ? $customer : '-')
							. ' | Date: ' . $date_display
							. ' | Time: ' . $time_display
							. ' | Employee: ' . ($user_name != '' ? $user_name : '-')
							. ' | Status: ' . ($status != '' ? $status : '-');
						$map_url = '';
						if ($lat != '' && $lng != '') {
							$map_url = 'https://www.google.com/maps?q=' . urlencode($lat . ',' . $lng);
						}

						$cssdata[] = array(
							'Sl.No' => $i,
							'Date' => $date_display,
							'Time' => $time_display,
							'User' => $user_name,
							'Customer' => $customer,
							'Contact' => $contact,
							'Location' => $location,
							'Coordinates' => $coords_csv,
							'Geo Location' => $geo_csv,
							'Status' => $status
						);
			?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo htmlspecialchars($date_display); ?></td>
					<td><?php echo htmlspecialchars($time_display); ?></td>
					<td><?php echo ($user_name != '') ? htmlspecialchars($user_name) : '-'; ?></td>
					<td><?php echo ($customer != '') ? htmlspecialchars($customer) : '-'; ?></td>
					<td><?php echo ($contact != '') ? htmlspecialchars($contact) : '-'; ?></td>
					<td><?php echo ($location != '') ? htmlspecialchars($location) : '-'; ?></td>
					<td><?php echo ($coords != '') ? htmlspecialchars($coords) : '-'; ?></td>
					<td class="text-wrap"><?php echo ($geo_location != '') ? htmlspecialchars($geo_location) : '-'; ?></td>
					<td>
						<span class="fwor-status-badge <?php echo fworStatusClass($status); ?>">
							<?php echo ($status != '') ? htmlspecialchars($status) : '-'; ?>
						</span>
					</td>
					<td>
						<?php if ($map_url != '') { ?>
							<a class="fwor-map-link" href="<?php echo htmlspecialchars($map_url); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($tooltip); ?>">
								<i class="fas fa-map-marker-alt"></i>
							</a>
						<?php } else { ?>
							<span class="fwor-map-disabled" title="<?php echo htmlspecialchars($tooltip); ?>">
								<i class="fas fa-map-marker-alt"></i>
							</span>
						<?php } ?>
					</td>
				</tr>
			<?php
						$i++;
					}
					$_SESSION['download_data'] = $cssdata;
				}
			?>
		</tbody>
	</table>
</div>
<?php } else { ?>
<div class="card-px text-center py-20 mt-2">
	<p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Field Work Operations</p>
	<div class="text-center px-4">
		<img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
	</div>
</div>
<?php } ?>
