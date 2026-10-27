<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['datepicker'] = '1';

if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date = $_POST['to'] ? date('Y-m-d', strtotime($_POST['to'])) : date('Y-m-t');
} else {
    $from_date = date('Y-m-01');
    $to_date = date('Y-m-t');
}

$url = API . "?mode=getfuelentrylist&method=get";
$url .= "&from_date=" . urlencode($from_date);
$url .= "&to_date=" . urlencode($to_date);

$response = callAPI($url);
$data = json_decode($response, true) ?: [];

$fuel_entries = [];
$total_entries = 0;
$total_fuel = 0;
$total_amount = 0;

if (isset($data['status']) && $data['status'] === 'success' && isset($data['fuel_entries'])) {
    $fuel_entries = $data['fuel_entries'];
    $total_entries = count($fuel_entries);

    foreach ($fuel_entries as $entry) {
        $total_fuel += floatval($entry['fuel_quantity']);
        $total_amount += floatval($entry['total_amount']);
    }
}
?>

<div class="page-body">
    <!-- Filter Card -->
    <div class="card">
        <div class="card-header">
            <h5><i class="ti-filter"></i> Fuel Track List</h5>
            <div class="card-header-right">
                <a href="index.php?option=custom_insert_transport_fuel" class="btn btn-primary waves-effect waves-light">
                    <i class="ti-plus"></i> Add New Entry
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <div class="row col-lg-12 align-items-end">
                    <?php
                    $shortcode = $_POST['shortcode_date'] ?? "TM";
                    $pick_date = $_POST['pick_date'] ?? "This Month";
                    $from_date_display = $_POST['from'] ?? fromDate(date("Y-m-01"));
                    $to_date_display = $_POST['to'] ?? fromDate(date("Y-m-t"));
                    ?>
                    <div class="col-lg-4">
                        <input type="hidden" name="pick_date" id="pick_date" value="This Month">
                        <input type="hidden" name="shortcode_date" id="shortcode_date" value="TM">
                        <div class="btn-group dropdown-split-primary" style="width: 100%;">
                            <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;">
                                <i class="ti-calendar"></i>
                                <span id="btn_date"><?php echo $pick_date; ?></span>
                            </button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                                <span class="visually-hidden">Toggle primary</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TD" ? "active" : ""; ?> empty_active"
                                    id="TD" href="#!" onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TW" ? "active" : ""; ?> empty_active"
                                    id="TW" href="#!" onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week'))); ?>','<?php echo fromDate(date("Y-m-d", strtotime('sunday this week'))); ?>')">This Week</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TM" ? "active" : ""; ?> empty_active"
                                    id="TM" href="#!" onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TY" ? "active" : ""; ?> empty_active"
                                    id="TY" href="#!" onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "YD" ? "active" : ""; ?> empty_active"
                                    id="YD" href="#!" onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>')">Yesterday</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PM" ? "active" : ""; ?> empty_active"
                                    id="PM" href="#!" onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01", strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t", strtotime('last month'))); ?>')">Previous Month</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "CD" ? "active" : ""; ?> empty_active"
                                    id="CD" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
                            </div>
                        </div>
                        <div class="row mt-2" style="display: <?php echo $shortcode == 'CD' ? '' : 'none' ?>" id="custome_date">
                            <div class="col-lg-6">
                                <input aria-label="From" type="text" class="form-control filterdynamic datepicker" name="from" id="from_date" value="<?php echo $from_date_display; ?>">
                            </div>
                            <div class="col-lg-6">
                                <input aria-label="To" type="text" class="form-control filterdynamic datepicker" name="to" id="to_date" value="<?php echo $to_date_display; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" name="applyform" value="apply" class="btn btn-info">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0"><?php echo $total_entries; ?></h4>
                            <p class="mb-0">Total Entries</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="ti-dropbox" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0"><?php echo number_format($total_fuel, 2); ?>L</h4>
                            <p class="mb-0">Total Fuel</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="ti-stats-up" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">₹<?php echo number_format($total_amount, 2); ?></h4>
                            <p class="mb-0">Total Amount</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="ti-wallet" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">₹<?php echo $total_entries > 0 ? number_format($total_amount / $total_fuel, 2) : '0.00'; ?></h4>
                            <p class="mb-0">Avg Price/L</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="ti-bar-chart" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card table-card">
        <div class="card-header table-card-header">
            <h5><i class="ti-dropbox"></i> Fuel Entries</h5>
            <div class="card-header-right">
                <span class="badge badge-primary text-white" id="table-count-badge">
                    <?php echo $total_entries; ?> entr<?php echo $total_entries !== 1 ? 'ies' : 'y'; ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="fuelEntriesTable">
                    <thead>
                        <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">Vehicle Number</th>
                            <th scope="col">Ownership</th>
                            <th scope="col">Driver Name</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Department</th>
                            <th scope="col">Fuel Type</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price/L</th>
                            <th scope="col">Total Amount</th>
                            <th scope="col">Payment Method</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($fuel_entries)): ?>
                            <?php foreach ($fuel_entries as $index => $entry): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($entry['vehicle_number']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $entry['vehicle_ownership'] === 'company' ? 'primary' : 'secondary'; ?>">
                                            <?php echo ucfirst(htmlspecialchars($entry['vehicle_ownership'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($entry['driver_name']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['contact_number']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['department']); ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo ucfirst(htmlspecialchars($entry['fuel_type'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($entry['fuel_quantity'], 2); ?>L</td>
                                    <td>₹<?php echo number_format($entry['price_per_liter'], 2); ?></td>
                                    <td>₹<?php echo number_format($entry['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $entry['payment_method'] === 'cash' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst(htmlspecialchars($entry['payment_method'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d-m-Y', strtotime($entry['fuel_date'])); ?></td>
                                    <td>
                                        <?php if ($entry['is_emergency'] == 1): ?>
                                            <span class="badge badge-danger">Emergency</span>
                                        <?php endif; ?>
                                        <?php if ($entry['is_advance'] == 1): ?>
                                            <span class="badge badge-warning">Advance</span>
                                        <?php endif; ?>
                                        <?php if ($entry['is_emergency'] == 0 && $entry['is_advance'] == 0): ?>
                                            <span class="badge badge-success">Regular</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="rows">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                    onclick="editFuelEntry(<?php echo $entry['id']; ?>)"
                                                    title="Edit Entry">
                                                <i class="ti-pencil"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="deleteFuelEntry(<?php echo $entry['id']; ?>)"
                                                    title="Delete Entry">
                                                <i class="ti-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="14" class="text-center p-4">
                                    <i class="ti-info-alt text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">No fuel entries found for the selected date range.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Refresh page when filter is applied
    function refreshPage() {
        document.getElementById('filterForm').submit();
    }

    function editFuelEntry(fuelId) {
        const idString = "id:" + fuelId;

        const firstEncode = btoa(idString);
        const secondEncode = btoa(firstEncode);
        const encodedId = encodeURIComponent(secondEncode);
        window.location.href = 'index.php?option=custom_insert_transport_fuel&Rid=' + encodedId;
    }

    function deleteFuelEntry(fuelId) {
        if (typeof swal === 'undefined') {
            if (!confirm('Are you sure you want to delete this fuel entry? This action cannot be undone.')) {
                return;
            }
            performDelete(fuelId);
            return;
        }

        swal({
            title: 'Are you sure?',
            text: 'You want to delete this fuel entry? This action cannot be undone.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }, function(isConfirm) {
            if (isConfirm) {
                performDelete(fuelId);
            }
        });
    }

    function performDelete(fuelId) {
        if (typeof jQuery === 'undefined') {
            if (typeof swal !== 'undefined') {
                swal('System Not Ready', 'Please wait a moment and try again.', 'warning');
            } else {
                alert('System not ready. Please wait a moment and try again.');
            }
            return;
        }

        if (typeof swal !== 'undefined') {
            swal({
                title: 'Deleting...',
                text: 'Please wait while we delete the fuel entry.',
                type: 'info',
                showConfirmButton: false,
                allowOutsideClick: false
            });
        }

        console.log('Attempting to delete fuel entry ID:', fuelId);

        $.post('ajax/employee_loan_ajax.php', {
            page: 'delete_fuel_entry',
            fuel_id: fuelId
        }, function(response) {
            console.log('Delete response:', response);

            try {
                const result = JSON.parse(response);
                console.log('Parsed result:', result);

                if (result.status === 'success' || result.status === 'sucess') {
                    if (typeof swal !== 'undefined') {
                        swal({
                            title: 'Deleted!',
                            text: 'Fuel entry has been deleted successfully.',
                            type: 'success',
                            confirmButtonText: 'OK'
                        }, function() {
                            window.location.reload();
                        });
                    } else {
                        alert('Fuel entry deleted successfully!');
                        window.location.reload();
                    }
                } else {
                    const errorMsg = 'Error deleting fuel entry: ' + (result.message || 'Unknown error');
                    if (typeof swal !== 'undefined') {
                        swal('Error!', errorMsg, 'error');
                    } else {
                        alert(errorMsg);
                    }
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                console.log('Raw response:', response);

                const errorMsg = 'Error processing delete request.';
                if (typeof swal !== 'undefined') {
                    swal('Error!', errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            }
        }).fail(function(xhr, status, error) {
            console.error('AJAX error:', {xhr, status, error});

            const errorMsg = 'Network error deleting fuel entry. Please check your connection.';
            if (typeof swal !== 'undefined') {
                swal('Network Error!', errorMsg, 'error');
            } else {
                alert(errorMsg);
            }
        });
    }

    document.getElementById('filterForm').addEventListener('submit', function(e) {
    });

    const company_id = <?php echo isset($_SESSION['company_id']) ? $_SESSION['company_id'] : '0'; ?>;
    const user_id = <?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : '0'; ?>;
</script>
