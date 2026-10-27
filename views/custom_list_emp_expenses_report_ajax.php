<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$admin_type = isset($_POST['admin_type']) ? $_POST['admin_type'] : '1';
$emp_ids = isset($_POST['emp_id']) ? $_POST['emp_id'] : array();
$statuses = isset($_POST['status']) ? $_POST['status'] : array();
$expense_types = isset($_POST['expense_type']) ? $_POST['expense_type'] : array();

if (is_array($emp_ids)) {
	$emp_ids_string = implode(',', $emp_ids);
} else {
	$emp_ids_string = $emp_ids;
}
if (is_array($statuses)) {
	$status_string = implode(',', $statuses);
} else {
	$status_string = $statuses;
}
if (is_array($expense_types)) {
	$expense_type_string = implode(',', $expense_types);
} else {
	$expense_type_string = $expense_types;
}

$url = API . "?mode=getempexpensesreport&method=get"
	. "&from_date=" . urlencode($from_date)
	. "&to_date=" . urlencode($to_date)
	. "&empids=" . urlencode($emp_ids_string)
	. "&status=" . urlencode($status_string)
	. "&expense_type=" . urlencode($expense_type_string)
	. "&admin_type=" . urlencode($admin_type);
$apires = callAPI($url);
$result = json_decode($apires, 1);
$rows = isset($result['rows']) ? $result['rows'] : array();
$summary = isset($result['summary']) ? $result['summary'] : array();
$claims = isset($summary['claims']) ? (int)$summary['claims'] : 0;
$line_items = isset($summary['line_items']) ? (int)$summary['line_items'] : 0;
$total_amount = isset($summary['total_amount']) ? (float)$summary['total_amount'] : 0;
$tax_amount = isset($summary['tax_amount']) ? (float)$summary['tax_amount'] : 0;
$pending_amount = isset($summary['pending_amount']) ? (float)$summary['pending_amount'] : 0;

$_SESSION['download_data'] = array();
$cssdata = array();

function eerFormatDate($date) {
	if ($date == '' || $date == '0000-00-00' || $date == null) {
		return '-';
	}
	if (function_exists('fromDate')) {
		return fromDate($date);
	}
	return date('d M Y', strtotime($date));
}

function eerFormatMoney($amount) {
	return '₹' . number_format((float)$amount, 2);
}

function eerStatusClass($status_code) {
	$status_code = (string)$status_code;
	if ($status_code == '2') {
		return 'eer-status-draft';
	}
	if ($status_code == '0') {
		return 'eer-status-active';
	}
	if ($status_code == '199') {
		return 'eer-status-approved';
	}
	return '';
}
?>
<div class="col-12">
	<div class="eer-summary">
		<div class="eer-chip eer-chip-claims">
			<span class="eer-chip-label">Claims</span>
			<span class="eer-chip-value"><?php echo $claims; ?></span>
		</div>
		<div class="eer-chip eer-chip-lines">
			<span class="eer-chip-label">Line Items</span>
			<span class="eer-chip-value"><?php echo $line_items; ?></span>
		</div>
		<div class="eer-chip eer-chip-total">
			<span class="eer-chip-label">Total Amount</span>
			<span class="eer-chip-value"><?php echo eerFormatMoney($total_amount); ?></span>
		</div>
		<div class="eer-chip eer-chip-tax">
			<span class="eer-chip-label">Tax</span>
			<span class="eer-chip-value"><?php echo eerFormatMoney($tax_amount); ?></span>
		</div>
		<div class="eer-chip eer-chip-pending">
			<span class="eer-chip-label">Pending Reimburse</span>
			<span class="eer-chip-value"><?php echo eerFormatMoney($pending_amount); ?></span>
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
				<th>Code</th>
				<th>Employee</th>
				<th>Type</th>
				<th>Merchant</th>
				<th>Bill No</th>
				<th>Payment</th>
				<th>Location</th>
				<th>Tax</th>
				<th>Amount</th>
				<th>Status</th>
				<th>Bill</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$i = 1;
				if (count((array)$rows) > 0) {
					foreach ($rows as $row) {
						$expense_date = isset($row['expense_date']) ? $row['expense_date'] : '';
						$expense_code = isset($row['expense_code']) ? $row['expense_code'] : '';
						$employee_name = isset($row['employee_name']) ? $row['employee_name'] : '';
						$expense_type_name = isset($row['expense_type_name']) ? $row['expense_type_name'] : '';
						$merchant_name = isset($row['merchant_name']) ? $row['merchant_name'] : '';
						$bill_no = isset($row['bill_no']) ? $row['bill_no'] : '';
						$payment_mode_name = isset($row['payment_mode_name']) ? $row['payment_mode_name'] : '';
						$location = isset($row['location']) ? $row['location'] : '';
						$tax_val = isset($row['tax_amount']) ? $row['tax_amount'] : 0;
						$amount_val = isset($row['expense_amount']) ? $row['expense_amount'] : 0;
						$status_label = isset($row['status_label']) ? $row['status_label'] : '';
						$status_code = isset($row['status_code']) ? $row['status_code'] : '';
						$bill_file = isset($row['bill_file']) ? $row['bill_file'] : '';

						$date_display = eerFormatDate($expense_date);
						$tax_display = eerFormatMoney($tax_val);
						$amount_display = eerFormatMoney($amount_val);
						$location_csv = str_replace(array(',', '_'), ' ', (string)$location);
						$tax_csv = number_format((float)$tax_val, 2, '.', '');
						$amount_csv = number_format((float)$amount_val, 2, '.', '');
						$bill_url = '';
						if ($bill_file != '') {
							$bill_url = 'uploads/all/' . $bill_file;
						}

						$cssdata[] = array(
							'Sl.No' => $i,
							'Date' => $date_display,
							'Code' => $expense_code,
							'Employee' => $employee_name,
							'Type' => $expense_type_name,
							'Merchant' => $merchant_name,
							'Bill No' => $bill_no,
							'Payment' => $payment_mode_name,
							'Location' => $location_csv,
							'Tax' => $tax_csv,
							'Amount' => $amount_csv,
							'Status' => $status_label
						);
			?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo htmlspecialchars($date_display); ?></td>
					<td><?php echo ($expense_code != '') ? htmlspecialchars($expense_code) : '-'; ?></td>
					<td><?php echo ($employee_name != '') ? htmlspecialchars($employee_name) : '-'; ?></td>
					<td><?php echo ($expense_type_name != '') ? htmlspecialchars($expense_type_name) : '-'; ?></td>
					<td><?php echo ($merchant_name != '') ? htmlspecialchars($merchant_name) : '-'; ?></td>
					<td><?php echo ($bill_no != '') ? htmlspecialchars($bill_no) : '-'; ?></td>
					<td><?php echo ($payment_mode_name != '') ? htmlspecialchars($payment_mode_name) : '-'; ?></td>
					<td class="text-wrap"><?php echo ($location != '') ? htmlspecialchars($location) : '-'; ?></td>
					<td><?php echo htmlspecialchars($tax_display); ?></td>
					<td><?php echo htmlspecialchars($amount_display); ?></td>
					<td>
						<span class="eer-status-badge <?php echo eerStatusClass($status_code); ?>">
							<?php echo ($status_label != '') ? htmlspecialchars($status_label) : '-'; ?>
						</span>
					</td>
					<td>
						<?php if ($bill_url != '') { ?>
							<a class="eer-bill-link" href="<?php echo htmlspecialchars($bill_url); ?>" target="_blank" rel="noopener noreferrer" title="View bill">
								<i class="fas fa-paperclip"></i>
							</a>
						<?php } else { ?>
							<span class="eer-bill-disabled"><i class="fas fa-paperclip"></i></span>
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
	<p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Employee Expenses</p>
	<div class="text-center px-4">
		<img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
	</div>
</div>
<?php } ?>
