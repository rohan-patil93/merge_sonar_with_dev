<?php
date_default_timezone_set('Asia/Kolkata');
$selected_gate_id = $_SESSION['gate_id'] ?? 1;

if (isset($_GET['key']) && $_GET['key'] === 'AJAX_FETCH_VISITOR_OUT') {
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $type = isset($_GET['type']) ? trim($_GET['type']) : '';
    $id   = isset($_GET['id']) ? trim($_GET['id']) : '';
}

$message = '';
if (isset($_POST['submit']) && $_POST['submit'] === 'insert') {
    $url  = API . "?mode=saveVisitorOut&method=post";
    $resp = file_post_contents($url, $_POST, 'insert');
    $result = json_decode($resp, true);
    if (!empty($result) && ($result['status'] ?? '') === 'success') {
        $message = 'Checkout successfully';
        header("location:index.php?option=custom_insert_visitor_logs&active_tab=checkout");
        exit;
    } else {
        $message = 'Checkout failed';
    }
}

if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];

    $type = isset($_GET['type']) ? trim($_GET['type']) : '';
    $id   = $rid ?? '';
}

// Build query to API
$params = array(
    'type' => $type,
    'gate_id' => $selected_gate_id,
    'id' => $id,
    'company_id' => $_SESSION['company_id'] ?? 1
);
$url = API . "?mode=getVisitorPassDetails&" . http_build_query($params);

// Call API, get data
$resp = callAPI($url);
$data = json_decode($resp, true);
$row = $data['visitor_logs_res'] ?? array();
if (empty($type) && !empty($row['visit_type'])) {
    $type = strtolower($row['visit_type']);
}
$building_gates_res = $data['building_gates_res'] ?? array();
$visitor_luggages_res = $data['visitor_luggages_res'] ?? array();
$visitor_badges_res = $data['visitor_badges_res'] ?? array();
$service_company_res = $data['service_company_res'] ?? array();
$delivery_company_res = $data['delivery_company_res'] ?? array();
$employee = ($type === 'employee') ? ($data['employee_res'] ?? array()) : array();

$uom_rows = $data['uom_rows'] ?? [];
$uom_map = [];
foreach ((array)$uom_rows as $uom) {
    $uom = (array)$uom;
    $uom_map[$uom['id']] = $uom['uom_name'] ?? '';
}

$gates = array();
foreach ((array)$data['building_gates_res'] as $gate) {
    $gates[$gate['id']] = $gate['name'];
}
$_SESSION['apply_js']['multiselest'] = 1;

$has_pass = !empty($row['visitor_idcard_no']);
$total_steps = $has_pass ? 4 : 3;
$step1 = 1;
$step2 = $has_pass ? 2 : 0;
$step3 = $has_pass ? 3 : 2;
$step4 = $has_pass ? 4 : 3;

$has_vehicle = !empty($row['vehicle_number']) || !empty($row['vendor_supplier_vehicle_number']);
$vehicle_no_val = !empty($row['vehicle_number']) ? $row['vehicle_number'] : ($row['vendor_supplier_vehicle_number'] ?? '');
?>

<?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', <?= json_encode($message) ?>, '');
        });
    </script>
<?php endif; ?>

<link rel="stylesheet" href="assets/css/visitor_out_view.css">

<form method="post" enctype="multipart/form-data" id="visitorOutForm" data-name="visitorOutForm" class="checkout-wizard-form">

    <!-- Checkout Guided Wizard Progress Indicator -->
    <div class="vms-step-progress checkout-step-progress mb-4">
        <div class="step-progress-track">
            <div class="step-progress-bar-fill" id="checkoutStepProgressBarFill"></div>
        </div>
        <div class="step-progress-step active" data-step="1">
            <div class="step-icon">1</div>
            <div class="step-label">Exit Setup</div>
        </div>
        <?php if ($has_pass): ?>
        <div class="step-progress-step" data-step="2">
            <div class="step-icon">2</div>
            <div class="step-label">Badge Return</div>
        </div>
        <?php endif; ?>
        <div class="step-progress-step" data-step="<?= $step3 ?>">
            <div class="step-icon"><?= $step3 ?></div>
            <div class="step-label">Materials Log</div>
        </div>
        <div class="step-progress-step" data-step="<?= $step4 ?>">
            <div class="step-icon"><?= $step4 ?></div>
            <div class="step-label">Confirm &amp; Checkout</div>
        </div>
    </div>


    <div class="checkout-slides-wrapper">


        <div class="checkout-slide active" id="checkout-slide-1" data-step="1">
            <div class="card out-card">
                <div class="out-section-title">Gate Exit Settings</div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <span class="mb-3"><i class="fa fa-door-open text-primary me-1"></i> Select Exit Gate <b class="text-danger">*</b></span>

                            <!-- Modern Gate Cards Grid -->
                            <div class="checkout-gate-select-grid mb-3">
                                <?php foreach ((array)$building_gates_res as $g):
                                    $isActive = ($selected_gate_id == $g['id']);
                                ?>
                                    <div class="gate-select-card <?= $isActive ? 'active' : '' ?>" data-gate-id="<?= htmlspecialchars($g['id']) ?>">
                                        <div class="gate-card-icon"><i class="fa fa-door-open"></i></div>
                                        <div class="gate-card-info">
                                            <span class="gate-name"><?= htmlspecialchars($g['name']) ?></span>
                                        </div>
                                        <div class="gate-check-mark"><i class="fa fa-check-circle"></i></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>


                            <div class="d-none">
                                <select aria-label="Out Gate No" class="form-select js-example-basic-single" id="out_gate_no" name="out_gate_no">
                                    <option value="0">Select Exit Gate</option>
                                    <?php foreach ((array)$building_gates_res as $g): ?>
                                        <option value="<?= htmlspecialchars($g['id']) ?>" <?= ($selected_gate_id == $g['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($g['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="form-group col-md-6">
                            <span class="form-label font-weight-bold text-dark"><i class="fa fa-clock text-primary me-1"></i> Out Date &amp; Time</span>
                            <input aria-label="Out Datetime" type="datetime-local" class="form-control" id="out_datetime" name="out_datetime" value="<?= (date('Y-m-d\TH:i')) ?>" min="<?= (date('Y-m-d\TH:i')) ?>" required>
                        </div>


                        <div class="form-group col-md-6">
                            <?php if ($has_vehicle): ?>
                                <span class="form-label font-weight-bold text-dark"><i class="fa fa-shipping-fast text-primary me-1"></i> How are you exiting?</span>
                                <div class="exit-mode-grid">
                                    <div class="exit-mode-card active" data-mode="vehicle">
                                        <div class="exit-card-icon"><i class="fa fa-truck"></i></div>
                                        <div class="exit-card-label">Exiting with Vehicle</div>
                                        <div class="exit-check-mark"><i class="fa fa-check-circle"></i></div>
                                    </div>
                                    <div class="exit-mode-card" data-mode="person">
                                        <div class="exit-card-icon"><i class="fa fa-walking"></i></div>
                                        <div class="exit-card-label">Exiting as Person</div>
                                        <div class="exit-check-mark"><i class="fa fa-check-circle"></i></div>
                                    </div>
                                </div>
                                <input type="hidden" name="exit_mode" id="exit_mode" value="vehicle">
                            <?php else: ?>
                                <input type="hidden" name="exit_mode" id="exit_mode_2" value="person">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <?php if ($has_pass): ?>
        <div class="checkout-slide" id="checkout-slide-2" data-step="2">
            <div class="card out-card">
                <div class="out-section-title">Visitor Pass / ID Badge Return</div>
                <div class="card-body">
                    <div class="form-group col-md-12">
                        <span class="mb-3"><i class="fa fa-id-card text-primary me-1"></i> Did the visitor return their physical Pass / Badge? <b class="text-danger">*</b></span>
                        <div class="badge-return-grid mb-3">
                            <div class="badge-return-card active" data-returned="1">
                                <div class="badge-card-icon text-success"><i class="fa fa-check-circle"></i></div>
                                <div class="badge-card-label">Pass Returned</div>
                                <div class="badge-check-mark"><i class="fa fa-check-circle"></i></div>
                            </div>
                            <div class="badge-return-card" data-returned="0">
                                <div class="badge-card-icon text-danger"><i class="fa fa-times-circle"></i></div>
                                <div class="badge-card-label">Missing / Not Returned</div>
                                <div class="badge-check-mark"><i class="fa fa-check-circle"></i></div>
                            </div>
                        </div>


                        <input aria-label="Pass Return[returned Flag]" class="form-check-input d-none" type="checkbox" id="passReturnedChk" name="pass_return[returned_flag]" value="1" checked>

                        <div class="pass-return-details-wrap mt-3" id="passReturnDetailsWrap">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <span class="form-label">Return Date &amp; Time</span>
                                    <input aria-label="Pass Return[returned At]" type="datetime-local" class="form-control" name="pass_return[returned_at]" value="<?= (date('Y-m-d\TH:i')) ?>" min="<?= (date('Y-m-d\TH:i')) ?>">
                                </div>
                                <div class="col-md-6">
                                    <span class="form-label">Remarks (Optional)</span>
                                    <input aria-label="Remarks (optional)" type="text" class="form-control" name="pass_return[remarks]" placeholder="Remarks (optional)">
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="pass_return[visitor_log_id]" value="<?php echo (int)($row['id'] ?? 0); ?>">
                    <input type="hidden" name="pass_return[pass_no]" value="<?php echo htmlspecialchars($row['visitor_idcard_no']); ?>">
                </div>
            </div>
        </div>
        <?php endif; ?>


        <div class="checkout-slide" id="checkout-slide-3" data-step="<?= $step3 ?>">
            <?php if (!empty($visitor_luggages_res)): ?>

                <?php if ($type === 'vendor_supplier'): ?>
                    <div class="card out-card">
                        <div class="out-section-title"><i class="fa fa-boxes text-secondary me-1"></i> Log Materials Left in Vehicle</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="vendorSupplierLugList">
                                    <thead>
                                        <tr>
                                            <th scope="col">Item Name</th>
                                            <th scope="col">Description</th>
                                            <th scope="col" class="text-right">Checked-In Qty</th>
                                            <th scope="col">UOM</th>
                                            <th scope="col" style="width: 15%;">Left Qty <span class="text-danger">*</span></th>
                                            <th scope="col" style="width: 35%;">Left Details / Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($visitor_luggages_res as $L):
                                            $lid = (int)($L['id'] ?? 0);
                                            $name = $L['item_name'] ?? 'Item';
                                            $brand = $L['brand_name'] ?? ''; // UOM ID
                                            $qty = (float)($L['qty'] ?? 1);
                                            $ser = $L['serial_or_remarks'] ?? '';
                                            $uom_label = isset($uom_map[$brand]) ? $uom_map[$brand] : $brand;
                                        ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($name) ?></strong></td>
                                                <td><?= htmlspecialchars($ser) ?></td>
                                                <td class="text-right"><?= $qty ?></td>
                                                <td><?= htmlspecialchars($uom_label) ?></td>
                                                <td>
                                                    <input aria-label="Enter number" type="number" step="0.01" class="form-control form-control-sm text-right" name="vendor_supplier_luggage[<?= $lid ?>][left_qty]" value="<?= $qty ?>" min="0" max="<?= $qty ?>" required>
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" class="form-control form-control-sm" name="vendor_supplier_luggage[<?= $lid ?>][left_remarks]" placeholder="Remarks on quantity left">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card out-card">
                        <div class="out-section-title">Luggage &amp; Carrying Materials</div>
                        <div class="card-body">
                            <div class="text-muted small">No luggage recorded on entry.</div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>

                <?php $today = date('Y-m-d'); ?>
                <div class="card out-card">
                    <div class="out-section-title">Log Outgoing Items</div>
                    <div class="card-body">
                        <div class="form-group col-md-12">
                            <span class="mb-3"><i class="fa fa-sign-out-alt text-primary me-1"></i> Are you taking any new items OUT of the premises now? <b class="text-danger">*</b></span>
                            <div class="taking-out-grid mb-3">
                                <div class="taking-out-card" data-val="yes">
                                    <div class="taking-card-icon"><i class="fa fa-box-open"></i></div>
                                    <div class="taking-card-label">Yes, Logging Items OUT</div>
                                    <div class="taking-check-mark"><i class="fa fa-check-circle"></i></div>
                                </div>
                                <div class="taking-out-card active" data-val="no">
                                    <div class="taking-card-icon"><i class="fa fa-times"></i></div>
                                    <div class="taking-card-label">No Items to Log</div>
                                    <div class="taking-check-mark"><i class="fa fa-check-circle"></i></div>
                                </div>
                            </div>

                            <input aria-label="Taking Out" type="radio" class="d-none" name="taking_out" id="out_lug_yes" value="yes">
                            <input aria-label="Taking Out" type="radio" class="d-none" name="taking_out" id="out_lug_no" value="no" checked>
                            <span class="badge bg-primary ms-3 d-none" id="outLugCountBadge">0 items</span>
                        </div>

                        <div id="outLuggageSection" class="out-luggage-section mt-4" style="display:none;">
                            <p class="text-muted small">Select an item category pill to add item specifications:</p>
                            <div id="outLuggageScroller" class="luggage-scroller mb-3"></div>
                            <div>
                                <h6 class="font-weight-bold mb-3 text-dark"><i class="fa fa-shopping-bag text-secondary"></i> Items Logged to Take Out</h6>
                                <div id="outLuggageList" class="luggage-list"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>


        <div class="checkout-slide" id="checkout-slide-4" data-step="<?= $step4 ?>">
            <!-- Visitor Profile Info Card -->
            <div class="card out-card">
                <div class="out-section-title">Visitor Record Details</div>
                <div class="card-body p-0">
                    <div class="visitor-details">
                        <div class="visitor-row">
                            <div class="visitor-col">Name</div>
                            <div class="visitor-col"><strong>
                                <?php
                                    if ($type === 'delivery') {
                                        echo htmlspecialchars($delivery_company_res[$row['delivery_company_person'] ?? ''] ?? 'Not Specified');
                                    } elseif ($type === 'service') {
                                        echo htmlspecialchars($row['service_person'] ?? '—');
                                    } elseif ($type === 'employee') {
                                        echo htmlspecialchars($row['emp_temp_name'] ?? ($row['employee_name'] ?? '—'));
                                    } elseif ($type === 'doctor') {
                                        echo htmlspecialchars($row['doctor_name'] ?? '—');
                                    } elseif ($type === 'vendor_supplier') {
                                        echo htmlspecialchars($row['vendor_supplier_driver_name'] ?? '—');
                                    } else {
                                        echo htmlspecialchars($row['name'] ?? '—');
                                    }
                                ?>
                            </strong></div>
                            <div class="visitor-col">Visitor Pass No</div>
                            <div class="visitor-col"><?php echo htmlspecialchars($row['visitor_idcard_no'] ?? '—'); ?></div>
                        </div>
                        <div class="visitor-row">
                            <div class="visitor-col">Phone</div>
                            <div class="visitor-col">
                                <?php
                                    if ($type === 'delivery') { echo htmlspecialchars($row['delivery_phone'] ?? '—');
}
                                    elseif ($type === 'service') { echo htmlspecialchars($row['service_phone'] ?? '—');
}
                                    elseif ($type === 'employee') { echo htmlspecialchars($row['emp_temp_phone'] ?? '—');
}
                                    elseif ($type === 'doctor') { echo htmlspecialchars($row['doctor_phone'] ?? '—');
}
                                    elseif ($type === 'vendor_supplier') { echo htmlspecialchars($row['vendor_supplier_driver_mobile'] ?? '—');
}
                                    else {
                                        echo htmlspecialchars($row['phone'] ?? '—');
                                    }
                                ?>
                            </div>
                            <div class="visitor-col">Whom to Meet</div>
                            <div class="visitor-col">
                                <?php
                                    if ($type === 'delivery') {
                                        echo htmlspecialchars($_SESSION['employees'][$row['deliver_to'] ?? ''] ?? $_SESSION['users'][$row['deliver_to'] ?? ''] ?? '—');
                                    } elseif ($type === 'service') {
                                        echo htmlspecialchars($_SESSION['employees'][$row['authorized_employee'] ?? ''] ?? $_SESSION['users'][$row['authorized_employee'] ?? ''] ?? '—');
                                    } elseif ($type === 'vendor_supplier') {
                                        echo htmlspecialchars($data['vendor_name'] ?? '—');
                                    } else {
                                        echo htmlspecialchars($_SESSION['employees'][$row['whom_to_meet'] ?? ''] ?? $_SESSION['users'][$row['whom_to_meet'] ?? ''] ?? '—');
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="visitor-row">
                            <div class="visitor-col">Visit Type</div>
                            <div class="visitor-col"><span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($type)) ?></span></div>
                            <div class="visitor-col">In Gate No</div>
                            <div class="visitor-col"><?php echo htmlspecialchars($gates[$row['in_gate_no'] ?? ''] ?? '—'); ?></div>
                        </div>
                        <div class="visitor-row">
                            <div class="visitor-col">In Date Time</div>
                            <div class="visitor-col"><?= !empty($row['in_datetime']) ? date('d-M-Y h:i A', strtotime($row['in_datetime'])) : '—' ?></div>
                            <div class="visitor-col">Vehicle No</div>
                            <div class="visitor-col"><?= !empty($vehicle_no_val) ? htmlspecialchars($vehicle_no_val) : '—' ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Stay details inside review -->
            <?php if ($type === 'vendor_supplier'): ?>
                <div class="card out-card mt-3">
                    <div class="out-section-title"><i class="fa fa-clock text-secondary me-1"></i> Stay &amp; Loading Tracking</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <span class="form-label">Vehicle Stay Status</span>
                                <select aria-label="Vendor Supplier Vehicle Status" class="form-select js-example-basic-single" name="vendor_supplier_vehicle_status">
                                    <option value="Waiting" <?php echo ($row['vendor_supplier_vehicle_status'] ?? '') === 'Waiting' ? 'selected' : ''; ?>>Waiting</option>
                                    <option value="Loading" <?php echo ($row['vendor_supplier_vehicle_status'] ?? '') === 'Loading' ? 'selected' : ''; ?>>Loading</option>
                                    <option value="Unloading" <?php echo ($row['vendor_supplier_vehicle_status'] ?? '') === 'Unloading' ? 'selected' : ''; ?>>Unloading</option>
                                    <option value="Completed" <?php echo ($row['vendor_supplier_vehicle_status'] ?? '') === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <span class="form-label">Loading/Unloading Start Time</span>
                                <input aria-label="Vendor Supplier Loading Start" class="form-control" type="datetime-local" name="vendor_supplier_loading_start" value="<?php echo !empty($row['vendor_supplier_loading_start']) ? str_replace(' ', 'T', $row['vendor_supplier_loading_start']) : ''; ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <span class="form-label">Loading/Unloading End Time</span>
                                <input aria-label="Vendor Supplier Loading End" class="form-control" type="datetime-local" name="vendor_supplier_loading_end" value="<?php echo !empty($row['vendor_supplier_loading_end']) ? str_replace(' ', 'T', $row['vendor_supplier_loading_end']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Checkout Parameters Summary Box -->
            <div class="card out-card mt-3 border-primary">
                <div class="out-section-title bg-primary text-white"><i class="fa fa-check-double me-1"></i> Checkout Summary Review</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="summary-item">
                                <div class="summary-label">Exit Gate</div>
                                <div class="summary-val" id="summary-exit-gate">—</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <div class="summary-label">Exit Date &amp; Time</div>
                                <div class="summary-val" id="summary-exit-time">—</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <div class="summary-label">Exit Mode</div>
                                <div class="summary-val" id="summary-exit-mode">—</div>
                            </div>
                        </div>
                        <?php if ($has_pass): ?>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <div class="summary-label">Pass / Badge Returned</div>
                                <div class="summary-val" id="summary-badge-returned">—</div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-12">
                            <div class="summary-item">
                                <div class="summary-label">Materials Logged</div>
                                <div class="summary-val" id="summary-materials-logged">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <?php if (!empty($row['id'])) { ?>
                <div class="pt-3 mt-3">
                    <button type="submit" name="submit" value="insert" class="btn-vms btn-vms-primary w-100 py-3 font-weight-bold">
                        <i class="fa fa-sign-out-alt"></i> Complete Gate Checkout &amp; Exit
                    </button>
                    <input type="hidden" name="visitor_log_id" value="<?php echo (int)$row['id']; ?>">
                    <input type="hidden" name="visit_type" value="<?php echo htmlspecialchars($type); ?>">
                </div>
            <?php } ?>
        </div>

    </div>

    <!-- Bottom Action Row -->
    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-4">
        <button type="button" class="btn btn-vms-secondary py-2 px-4 js-checkout-prev" style="display: none;"><i class="fa fa-arrow-left me-2"></i> Back</button>
        <button type="button" class="btn btn-vms-primary py-2 px-4 ms-auto js-checkout-next">Next <i class="fa fa-arrow-right ms-2"></i></button>
    </div>

</form>

<!-- Modal dedicated to OUT -->
<div class="modal fade" id="luggageModalOut" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-3 border-bottom">
                <h6 class="modal-title font-weight-bold">Add Outgoing Item</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="lm_out_item_key">
                <div class="mb-3">
                    <span class="form-label">Item Category</span>
                    <input aria-label="Lm Out Item Label" type="text" class="form-control" id="lm_out_item_label" readonly>
                </div>
                <div class="mb-3">
                    <span class="form-label">Brand Name</span>
                    <select aria-label="Lm Out Brand Name" class="form-select" id="lm_out_brand_name">
                        <option value="">Select brand</option>
                        <option>Dell</option>
                        <option>HP</option>
                        <option>Apple</option>
                        <option>Lenovo</option>
                        <option>Samsung</option>
                        <option>Sony</option>
                        <option>LG</option>
                        <option>Canon</option>
                        <option>Nikon</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <span class="form-label">Quantity</span>
                    <input aria-label="Lm Out Qty" type="number" min="1" class="form-control" id="lm_out_qty" value="1">
                </div>
                <div class="mb-3">
                    <span class="form-label">Serial No / Remarks</span>
                    <input aria-label="Enter serial or description" type="text" class="form-control" id="lm_out_serial_or_remarks" placeholder="Enter serial or description">
                </div>
                <div class="mb-3">
                    <span class="form-label d-block">Returnable Item?</span>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="lm_out_returnable" id="lm_out_returnable_yes" value="yes" checked>
                        <label class="form-check-label" for="lm_out_returnable_yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="lm_out_returnable" id="lm_out_returnable_no" value="no">
                        <label class="form-check-label" for="lm_out_returnable_no">No</label>
                    </div>
                </div>
                <div class="mb-3 lm-out-return-date-wrap" id="lm_out_return_date_wrap">
                    <span class="form-label">Expected Return Date</span>
                    <input aria-label="Lm Out Return Date" type="date" class="form-control" id="lm_out_return_date" value="<?php echo $today; ?>">
                </div>
            </div>
            <div class="modal-footer py-2 border-top">
                <button type="button" class="btn btn-vms-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-vms-primary btn-sm" id="lm_out_save_btn">Add Item</button>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="visitor-out-luggage-config">
<?php echo json_encode([
    'today' => $today
]); ?>
</script>
<script src="../assets/js/visitor_out_view.js"></script>
