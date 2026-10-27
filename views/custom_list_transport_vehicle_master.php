<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['datepicker'] = '1';

$from_date = $_POST['from'] ?? date("Y-m-01");
$to_date = $_POST['to'] ?? date("Y-m-t");

if (strpos($from_date, '-') !== false && strlen($from_date) == 10) {
    $parts = explode('-', $from_date);
    if (count($parts) == 3 && strlen($parts[0]) == 2) {
        $from_date = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
}
if (strpos($to_date, '-') !== false && strlen($to_date) == 10) {
    $parts = explode('-', $to_date);
    if (count($parts) == 3 && strlen($parts[0]) == 2) {
        $to_date = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
}

$url = API . "?mode=gettransportvehiclemasterlist&method=get&company_id=" . $_SESSION['company_id'] . "&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date);
$response = callAPI($url);
$data = json_decode($response, true) ?: [];
$vehicles = $data['vehicles'] ?? [];
$statusList = $data['status_list'] ?? [];

$currentStatuses = [];
$statusColors = [];
foreach ($statusList as $status) {
    $currentStatuses[$status['name_value']] = $status['name'];
    $statusColors[$status['name_value']] = [
        'bg' => $status['background_color'] ?? '#6c757d',
        'text' => $status['color'] ?? '#fff'
    ];
}

// Vehicle types
$vehicleTypes = [
    'demo' => 'Demo Car',
    'car' => 'Car',
    'truck' => 'Truck',
    'van' => 'Van',
    'motorcycle' => 'Motorcycle',
    'bus' => 'Bus',
    'trailer' => 'Trailer',
    'other' => 'Other'
];

// Fuel types
$fuelTypes = [
    'petrol' => 'Petrol',
    'diesel' => 'Diesel',
    'cng' => 'CNG',
    'lpg' => 'LPG',
    'electric' => 'Electric',
    'hybrid' => 'Hybrid'
];

function getInsuranceStatus($expDate) {
    if (empty($expDate)) { return ['status' => 'unknown', 'class' => ''];
}

    $today = new DateTime();
    $expiry = new DateTime($expDate);
    $diff = $today->diff($expiry);

    if ($expiry < $today) {
        return ['status' => 'Expired', 'class' => 'insurance-expired'];
    } elseif ($diff->days <= 30) {
        return ['status' => 'Expiring Soon', 'class' => 'insurance-expiring-soon'];
    } else {
        return ['status' => 'Valid', 'class' => 'insurance-valid'];
    }
}
?>

<div class="d-none transport-vehicle-master-list-php-data"
    data-company-id="<?php echo isset($_SESSION['company_id']) ? $_SESSION['company_id'] : '0'; ?>"
    data-user-id="<?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : '0'; ?>">
</div>

<!-- Include external CSS -->
<link rel="stylesheet" href="assets/css/custom_insert_transport_vehicle_master.css">

<div class="page-body">
    <div class="card">
        <div class="card-header table-card-header">
            <h5><i class="ti-car"></i> Vehicle Master List</h5>
            <div class="card-header-right">
                <a href="index.php?option=custom_insert_transport_vehicle_master" class="btn btn-primary waves-effect waves-light">
                    <i class="ti-plus"></i> Add New Vehicle
                </a>
            </div>
        </div>

        <div class="card-body">
            <?php include_once 'date_filter_common.php'; ?>
            <hr>
            <!-- Vehicle Table -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="vehicleMasterTable">
                    <thead>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">#</th>
                            <th scope="col">Reg. No.</th>
                            <th scope="col">Model</th>
                            <th scope="col">Type</th>
                            <th scope="col">Colour</th>
                            <th scope="col">Fuel</th>
                            <th scope="col">KM Driven</th>
                            <th scope="col">Insurance Exp.</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vehicleTableBody">
                        <?php
                        $sl = 1;
                        if (!empty($vehicles)) {
                            foreach ($vehicles as $vehicle) {
                                $status = $currentStatuses[$vehicle['current_status']] ?? 'Unknown';
                                $statusColor = $statusColors[$vehicle['current_status']] ?? ['bg' => '#6c757d', 'text' => '#fff'];
                                $insuranceStatus = getInsuranceStatus($vehicle['insurance_exp_date']);
                                $vehicleTypeName = $vehicleTypes[$vehicle['vehicle_type']] ?? ucfirst($vehicle['vehicle_type']);
                                $fuelTypeName = $fuelTypes[$vehicle['fuel_type']] ?? ucfirst($vehicle['fuel_type'] ?? '-');
                                ?>
                                <tr data-vehicle-id="<?php echo $vehicle['id']; ?>"
                                    data-status="<?php echo $vehicle['current_status']; ?>"
                                    data-vehicle-type="<?php echo $vehicle['vehicle_type']; ?>"
                                    data-is-active="<?php echo $vehicle['is_active']; ?>">
                                    <td><?php echo $sl++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($vehicle['vehicle_reg_no']); ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($vehicle['model'] ?? '-'); ?>
                                        <?php if (!empty($vehicle['model_group'])): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($vehicle['model_group']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $vehicleTypeName; ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['colour'] ?? '-'); ?></td>
                                    <td><?php echo $fuelTypeName; ?></td>
                                    <td><?php echo number_format($vehicle['km_driven'] ?? 0); ?></td>
                                    <td>
                                        <?php if (!empty($vehicle['insurance_exp_date'])): ?>
                                            <span class="<?php echo $insuranceStatus['class']; ?>">
                                                <?php echo date('d-M-Y', strtotime($vehicle['insurance_exp_date'])); ?>
                                            </span>
                                            <br><small class="<?php echo $insuranceStatus['class']; ?>"><?php echo $insuranceStatus['status']; ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="vehicle-status-badge" style="background-color: <?php echo $statusColor['bg']; ?>; color: <?php echo $statusColor['text']; ?>;">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info btn-round vehicle-action-btn view-vehicle-btn"
                                            data-vehicle-id="<?php echo $vehicle['id']; ?>" title="View Details">
                                            <i class="ti-eye"></i>
                                        </button>
                                        <a href="index.php?option=custom_insert_transport_vehicle_master&Rid=<?php echo deocdeId('id:' . $vehicle['id']); ?>"
                                            class="btn btn-sm btn-primary btn-round vehicle-action-btn" title="Edit">
                                            <i class="ti-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger btn-round vehicle-action-btn delete-vehicle-btn"
                                            data-vehicle-id="<?php echo $vehicle['id']; ?>"
                                            data-vehicle-reg="<?php echo htmlspecialchars($vehicle['vehicle_reg_no']); ?>" title="Delete">
                                            <i class="ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr id="noDataRow">
                                <td colspan="11">
                                    <div class="card-px text-center py-20 m-t-30">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="ti-alert text-warning"></i> No Vehicles Found
                                        </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png"
                                                style="max-height: 300px!important;">
                                        </div>
                                        <a href="index.php?option=custom_insert_transport_vehicle_master" class="btn btn-primary mt-3">
                                            <i class="ti-plus"></i> Add First Vehicle
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Vehicle Modal -->
<div class="modal fade" id="viewVehicleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti-car"></i> Vehicle Details</h4>
                <button type="button" class="close close-view-modal-btn" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewVehicleContent">
                <div class="text-center p-4">
                    <i class="fa fa-spinner fa-spin loading-spinner-icon"></i>
                    <p class="text-muted mt-2">Loading vehicle details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-view-modal-btn">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteVehicleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti-trash text-danger"></i> Confirm Delete</h4>
                <button type="button" class="close close-delete-modal-btn" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteVehicleId">
                <div class="alert alert-danger">
                    <i class="ti-alert"></i>
                    Are you sure you want to delete vehicle: <strong id="deleteVehicleReg" class="delete-confirm-vehicle-name"></strong>?
                </div>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-delete-modal-btn">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="ti-trash"></i> Delete Vehicle
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include external JavaScript -->
<script src="assets/js/custom_list_transport_vehicle_master.js"></script>
