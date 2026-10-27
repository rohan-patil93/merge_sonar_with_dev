<?php
date_default_timezone_set('Asia/Kolkata');
$selected_gate_id   = $_SESSION['gate_id'] ?? 1;
if ($_GET['key'] == 'AJAX_FETCH_VISITOR_OUT') {
    include_once('../includes/session.php');
    include_once('../dbconfig.php');
    include_once('../comm.php');
    // Front-end sends these:
    $type    = isset($_GET['type'])    ? trim($_GET['type'])    : '';
    $id      = isset($_GET['id'])      ? trim($_GET['id'])      : '';
}
$message = '';
if ($_POST['submit'] == 'insert') {
    // echo '<pre>';
    // print_r($_POST);
    // echo '</pre>'; // debug

    $url  = API . "?mode=saveVisitorOut&method=post";
    $resp = file_post_contents($url, $_POST, 'insert');
    $result = json_decode($resp, true);
    // echo '<pre>'; print_r($result);  echo '</pre>'; // debug
    if (!empty($result) && ($result['status'] ?? '') === 'success') {
        $message = 'Checkout successfully';
        header("location:index.php?option=COM_LIST_VISITOR_LOGS");
    } else {
        $message =  'Checkout failed';
    }

}
// print_r($_GET);
if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];

    $type    = isset($_GET['type'])    ? trim($_GET['type'])    : '';
    $id      = $rid ?? '';
}

// Build query to API
$params = array('type' => $type, 'gate_id' => $selected_gate_id, 'id' => $id);
$url = API . "?mode=getVisitorPassDetails&" . http_build_query($params);

// Call API, get data
$resp = callAPI($url);
$data = json_decode($resp, true);
$row = $data['visitor_logs_res'] ?? array();
$building_gates_res = $data['building_gates_res'] ?? array();
$visitor_luggages_res = $data['visitor_luggages_res'] ?? array();
$visitor_badges_res = $data['visitor_badges_res'] ?? array();
$service_company_res = $data['service_company_res'] ?? array();
$delivery_company_res = $data['delivery_company_res'] ?? array();
$employee = ($type === 'employee') ? ($data['employee_res'] ?? array()) : array();

$gates = array();
foreach ((array)$data['building_gates_res'] as $gate) {
    $gates[$gate['id']] = $gate['name'];
}
$_SESSION['apply_js']['multiselest'] = 1;
?>
<?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', <?= json_encode($message) ?>, '');
        });
    </script>
<?php endif; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="assets/css/visitor_out_view.css">

<form method="post" enctype="multipart/form-data" id="visitorOutForm" data-name="visitorOutForm">
<div class="card mt-5">
    <div class="card-body p-3">
        <div class="row g-5">
            <div class="col-lg-8">
                <label class="form-label">Gate No <b class="text-danger">*</b></label>
                <!-- <select class="form-select js-example-basic-single" id="out_gate_no" name="out_gate_no">
                    <option value="0"> Select Any one </option>
                    <?php foreach ((array)$building_gates_res as $g): ?>
                        <option value="<?= htmlspecialchars($g['id']) ?>"
                            <?= ($selected_gate_id == $g['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select> -->

                <div class="visitor-slider">
                    <button type="button" class="scroll-btn left-btn" onclick="scrollVisitors(-300,'visitorWrapperA')">❮</button>
                    <div class="visitor-wrapper mt-4 p-2" id="visitorWrapperA">
                        <label class="visitor-card">
                            <input type="radio" name="visitor" checked="">
                            <div class="icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                                </svg>
                            </div>
                            <span>Visitor 1</span>
                        </label>

                        <label class="visitor-card">
                            <input type="radio" name="visitor">
                            <div class="icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                                </svg>
                            </div>
                            <span>Visitor 2</span>
                        </label>

                        <label class="visitor-card">
                            <input type="radio" name="visitor">
                            <div class="icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                                </svg>
                            </div>
                            <span>Visitor 3</span>
                        </label>

                        <label class="visitor-card">
                            <input type="radio" name="visitor">
                            <div class="icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                                </svg>
                            </div>
                            <span>Visitor 4</span>
                        </label>

                        <label class="visitor-card">
                            <input type="radio" name="visitor">
                            <div class="icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                                </svg>
                            </div>
                            <span>Visitor 5</span>
                        </label>

                        <label class="visitor-card">
                          <input type="radio" name="visitor">
                          <div class="icon-box">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                  <circle cx="12" cy="8" r="4"></circle>
                                  <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                              </svg>
                          </div>
                          <span>Visitor 6</span>
                        </label>
                    </div>
                    <button type="button" class="scroll-btn right-btn" onclick="scrollVisitors(300,'visitorWrapperA')">❯</button>
                </div>

            </div>
        <?php
        $inDateTime = !empty($row['in_datetime']) ? date('Y-m-d\TH:i', strtotime($row['in_datetime'])) : date('Y-m-d\TH:i');
        ?>
        <div class="col-lg-4">
            <label class="form-label">Out Date &amp; Time</label>
            <input type="datetime-local" class="form-control" id="out_datetime" name="out_datetime" value="<?= $inDateTime ?>" min="<?= $inDateTime ?>" required>
        </div>

        </div>
    </div>
</div>

<?php if ($type === 'delivery'): ?>
    <div class="card">
        <div class="card-header">
            <h5>Delivery Details</h5>
        </div>
        <div class="card-body">
            <div class="visitor-details">
                <div class="visitor-row">
                    <div class="visitor-col">Delivery Company / Person</div>
                    <div class="visitor-col"><?php echo $delivery_company_res[$row['delivery_company_person']] ?? ''; ?></div>
                    <div class="visitor-col">Visitor Pass No</div>
                    <div class="visitor-col"><?php echo $row['visitor_idcard_no'] ?? ''; ?></div>

                </div>
                <div class="visitor-row">
                    <div class="visitor-col">Contact Number</div>
                    <div class="visitor-col"><?php echo $row['delivery_phone'] ?? ''; ?></div>
                    <div class="visitor-col">Whom to Deliver</div>
                    <div class="visitor-col"><?php echo $_SESSION['users'][$row['deliver_to'] ?? ''] ?? ''; ?></div>
                </div>
                <div class="visitor-row">
                    <div class="visitor-col">In Gate No</div>
                    <div class="visitor-col"><?php echo $gates[$row['in_gate_no'] ?? ''] ?? ''; ?></div>
                    <div class="visitor-col">In Date Time</div>
                    <div class="visitor-col"><?= !empty($row['in_datetime'])  ? date('d-M-Y h:i A', strtotime($row['in_datetime']))  : '' ?></div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($type === 'service'): ?>
    <div class="card">
        <div class="card-header">
            <h5>Service Details</h5>
        </div>
        <div class="card-body">
            <div class="visitor-details">
                <div class="visitor-row">
                    <div class="visitor-col">Service Company</div>
                    <div class="visitor-col"><?php echo $service_company_res[$row['service_company']] ?? ''; ?></div>
                    <div class="visitor-col">Visitor Pass No</div>
                    <div class="visitor-col"><?php echo $row['visitor_idcard_no'] ?? ''; ?></div>

                </div>
                <div class="visitor-row">
                    <div class="visitor-col">Service Person</div>
                    <div class="visitor-col"><?php echo $row['service_person'] ?? ''; ?></div>
                    <div class="visitor-col">Authorized By</div>
                    <div class="visitor-col"><?php echo $_SESSION['users'][$row['authorized_employee'] ?? ''] ?? ''; ?></div>
                </div>
                <div class="visitor-row">
                    <div class="visitor-col">ID Card No</div>
                    <div class="visitor-col"><?php echo $row['idcard_number_service'] ?? ''; ?></div>
                    <div class="visitor-col">In Gate No</div>
                    <div class="visitor-col"><?php echo $gates[$row['in_gate_no'] ?? ''] ?? ''; ?></div>
                </div>
                <div class="visitor-row">
                    <div class="visitor-col">In Date Time</div>
                    <div class="visitor-col"><?= !empty($row['in_datetime'])  ? date('d-M-Y h:i A', strtotime($row['in_datetime']))  : '' ?></div>
                    <div class="visitor-col"></div>
                    <div class="visitor-col"></div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($type === 'employee'): ?>
    <div class="card">
        <div class="card-header">
            <h5>Employee Visit Details</h5>
        </div>
        <div class="card-body">
            <div class="visitor-details">
                <div class="visitor-row">

                    <div class="visitor-col">Employee Name</div>
                    <div class="visitor-col"><?php echo ($row['emp_temp_name'] ?? ($row['employee_name'] ?? '')); ?></div>
                    <div class="visitor-col">Employee Code</div> 
                    <div class="visitor-col"><?php echo ($employee['emp_code'] ?? ($row['visitor_idcard_no'] ?? '')); ?></div>
                </div>
                <div class="visitor-row">
                    <div class="visitor-col">Department</div>
                    <div class="visitor-col"><?php echo ($employee['department'] ?? ($row['employee_department'] ?? '')); ?></div>
                    <div class="visitor-col">Phone</div>
                    <div class="visitor-col"><?php echo ($row['emp_temp_phone'] ?? ''); ?></div>
                </div>
                <div class="visitor-row">
                    <div class="visitor-col">In Gate No</div>
                    <div class="visitor-col"><?php echo $gates[$row['in_gate_no'] ?? ''] ?? ''; ?></div>
                    <div class="visitor-col">In Date Time</div>
                    <div class="visitor-col"><?= !empty($row['in_datetime'])  ? date('d-M-Y h:i A', strtotime($row['in_datetime']))  : '' ?></div>
                </div>
            </div>
        </div>
    </div>
<?php elseif ($type === 'doctor'): ?>
    <div class="card">
        <div class="card-header">
            <h5>Doctor Visit Details</h5>
        </div>
        <div class="card-body">
            <div class="visitor-details">
                <div class="visitor-row">

                    <div class="visitor-col">Doctor Name</div>
                    <div class="visitor-col"><?php echo ($row['doctor_name'] ??  ''); ?></div>
                    <div class="visitor-col">Visitor Pass No</div> 
                    <div class="visitor-col"><?php echo ($row['visitor_idcard_no'] ?? ($row['visiting_company_id'] ?? '')); ?></div>
                </div>
                <div class="visitor-row">
                    <div class="visitor-col">ID Card No</div>
                    <div class="visitor-col"><?php echo $row['doctor_idcard_number'] ?? ''; ?></div>
                    <div class="visitor-col">Phone</div>
                    <div class="visitor-col"><?php echo ($row['doctor_phone'] ?? ''); ?></div>
                </div>
                <div class="visitor-row">
                    <div class="visitor-col">In Gate No</div>
                    <div class="visitor-col"><?php echo $gates[$row['in_gate_no'] ?? ''] ?? ''; ?></div>
                    <div class="visitor-col">In Date Time</div>
                    <div class="visitor-col"><?= !empty($row['in_datetime'])  ? date('d-M-Y h:i A', strtotime($row['in_datetime']))  : '' ?></div>
                </div>
            </div>
        </div>
    </div>

<?php else: /* visitor + others */ ?>
    <div class="card">
        <div class="card-header">
            <h5>Visitor Details</h5>
        </div>
        <div class="card-body">
            <div class="visitor-details">
                <div class="visitor-row">
                    <div class="visitor-col">Name</div>
                    <div class="visitor-col"><?php echo $row['name'] ?? ''; ?></div>
                    <div class="visitor-col">Visitor Pass No</div>
                    <div class="visitor-col"><?php echo $row['visitor_idcard_no'] ?? ''; ?></div>

                </div>

                <div class="visitor-row">
                    <div class="visitor-col">Phone</div>
                    <div class="visitor-col"><?php echo $row['phone'] ?? ''; ?></div>
                    <div class="visitor-col">Whom to meet</div>
                    <div class="visitor-col"><?php echo $_SESSION['users'][$row['whom_to_meet'] ?? ''] ?? ''; ?></div>

                </div>

                <div class="visitor-row">
                    <div class="visitor-col">Email</div>
                    <div class="visitor-col"><?php echo $row['email'] ?? ''; ?></div>
                    <div class="visitor-col">Purpose</div>
                    <div class="visitor-col"><?php echo $row['purpose'] ?? ''; ?></div>
                </div>

                <div class="visitor-row">
                    <div class="visitor-col">Address</div>
                    <div class="visitor-col"><?php echo $row['address'] ?? ''; ?></div>
                    <div class="visitor-col">In Gate No</div>
                    <div class="visitor-col"><?php echo $gates[$row['in_gate_no'] ?? ''] ?? ''; ?></div>
                </div>

                <div class="visitor-row">
                    <div class="visitor-col">ID Card No</div>
                    <div class="visitor-col"><?php echo $row['idcard_number'] ?? ''; ?></div>
                    <div class="visitor-col">In Date Time</div>
                    <div class="visitor-col"><?= !empty($row['in_datetime'])  ? date('d-M-Y h:i A', strtotime($row['in_datetime']))  : '' ?></div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$lugs = $data['visitor_luggages_res'] ?? array();
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($_GET['Rid']) { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<?php }?>

<?php if (!empty($lugs)): ?>
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Luggage</h5>
            <div>
                <button type="button" class="btn btn-sm btn-soft" id="markAllReturnable">Mark all returnables as returned (today)</button>
            </div>
        </div>
        <div class="card-body">
            <div class="lug-list" id="lugList">
                <?php foreach ($lugs as $L):
                    $id   = (int)($L['id'] ?? 0);
                    $name = $L['item_name'] ?? 'Item';
                    $brand = $L['brand_name'] ?? '';
                    $qty  = (int)($L['qty'] ?? 1);
                    $ser  = $L['serial_or_remarks'] ?? ($L['description'] ?? '');
                    $ret  = (int)($L['returnable'] ?? 0); // 1=yes, 0=no
                    $plannedDate = trim($L['return_date'] ?? ''); // planned return date
                    // Pre-calc defaults for the UI
                    $isReturnable = ($ret === 1);
                    $avatar = strtoupper(substr($name, 0, 1));
                ?>
                    <div class="lug-item" data-lug-id="<?php echo $id; ?>" data-returnable="<?php echo $isReturnable ? '1' : '0'; ?>">
                        <div class="lug-left">
                            <div class="lug-avatar"><?php echo htmlspecialchars($avatar); ?></div>
                            <div class="lug-meta">
                                <div class="lug-title">
                                    <?php echo htmlspecialchars($name); ?>
                                    <?php if ($isReturnable): ?>
                                        <span class="badge-pill badge-returnable">Returnable<?php echo $plannedDate ? ' • by ' . htmlspecialchars($plannedDate) : ''; ?></span>
                                    <?php else: ?>
                                        <span class="badge-pill badge-nonret">Not returnable</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if ($brand) echo 'Brand: ' . htmlspecialchars($brand) . ' • '; ?>
                                    Qty: <?php echo $qty; ?>
                                    <?php if ($ser) echo ' • ' . htmlspecialchars($ser); ?>
                                </div>
                            </div>
                        </div>

                        <div class="lug-right">
                            <?php if ($isReturnable): ?>
                                <label class="form-check">
                                    <input class="form-check-input mark-returned" type="checkbox" name="luggage[<?php echo $id; ?>][returned_flag]" value="1">
                                    <span class="ms-1">Returned?</span>
                                </label>
                                <div class="lug-return-wrap">
                                    <div class="row g-2 mt-1">
                                        <div class="col">
                                            <input type="date" class="form-control returned-at" name="luggage[<?php echo $id; ?>][returned_at]" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="luggage[<?php echo $id; ?>][return_remarks]" placeholder="Remarks (optional)">
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>

                            <!-- Always post the basics you might need server-side -->
                            <input type="hidden" name="luggage[<?php echo $id; ?>][id]" value="<?php echo $id; ?>">
                            <input type="hidden" name="luggage[<?php echo $id; ?>][item_name]" value="<?php echo htmlspecialchars($name); ?>">
                            <input type="hidden" name="luggage[<?php echo $id; ?>][returnable]" value="<?php echo $ret; ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="lug-footer">
                <div id="lugSummary">Total: 0 • Returnable: 0 • Selected to return: 0</div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-soft btn-sm" id="clearReturnSelections">Clear selections</button>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Luggage</h5>
        </div>
        <div class="card-body">
            <div class="text-muted">No luggage recorded.</div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($row['visitor_idcard_no'])): ?>
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Visitor Pass / ID Card</h5>
            <span class="pass-badge">Required for exit</span>
        </div>
        <div class="card-body">
            <div class="" id="passBox" data-already-returned="<?php echo !empty($row['pass_returned_at']) ? '1' : '0'; ?>">
                <div class="lug-item">
                    <div class="lug-left">
                        <div class="lug-meta">
                            <div class="lug-title"><strong>Pass No:</strong> <?php echo htmlspecialchars($row['visitor_idcard_no']); ?></div>
                            <div class="text-muted small">Issued at: <?php echo htmlspecialchars($row['in_datetime'] ?? '—'); ?></div>
                        </div>

                    </div>
                    <div class="lug-right" style="text-align: left;">
                        <label class="form-check">
                            <input class="form-check-input js-pass-return-toggle" type="checkbox" id="passReturnedChk" data-name="passReturnedChk"
                                name="pass_return[returned_flag]" value="1"
                                <?php echo !empty($row['pass_returned_at']) ? 'checked' : ''; ?>>
                            <span class="ms-1">Pass Returned?</span>
                        </label>
                        <div class="pass-return-wrap js-pass-return-wrap" id="passReturnWrap">
                            <div class="row g-2">
                                <div class="col">
                                    <input type="datetime-local" class="form-control" name="pass_return[returned_at]" value="<?= $inDateTime ?>" min="<?= $inDateTime ?>">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="pass_return[remarks]" placeholder="Remarks (optional)">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- Hidden essentials -->
                <input type="hidden" name="pass_return[visitor_log_id]" value="<?php echo (int)($row['id'] ?? 0); ?>">
                <input type="hidden" name="pass_return[pass_no]" value="<?php echo htmlspecialchars($row['visitor_idcard_no']); ?>">
<?php $today = date('Y-m-d'); ?>
<!-- =========================  ADD LUGGAGE (OUT) – FULL BLOCK  ========================= -->
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Items being taken out now</h5>

        <div class="d-flex align-items-center gap-3">
            <div class="form-check form-check-inline m-0">
                <input class="form-check-input" type="radio" name="taking_out" id="out_lug_no" value="no" checked>
                <label class="form-check-label" for="out_lug_no">No</label>
            </div>
            <div class="form-check form-check-inline m-0">
                <input class="form-check-input" type="radio" name="taking_out" id="out_lug_yes" value="yes">
                <label class="form-check-label" for="out_lug_yes">Yes</label>
            </div>

            <span class="out-badge" id="outLugCountBadge">0 items</span>
        </div>
    </div>


    <div class="card-body">
        <div id="outLuggageSection" class="out-luggage-section" >
            <div class="mb-2 text-muted">Click a Items to add:</div>
            <div id="outLuggageScroller" class="luggage-scroller"></div>

            <div class="mt-4">
                <h6 class="mb-2">Taking Out</h6>
                <div id="outLuggageList" class="luggage-list col-md-6"></div>
            </div>
        </div>
    </div>

</div>

<!-- Modal dedicated to OUT to avoid conflicts with IN -->
<div class="modal fade" id="luggageModalOut" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Add Item (OUT)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-2">
                <input type="hidden" id="lm_out_item_key">
                <div class="mb-2">
                    <label class="form-label">Item</label>
                    <input type="text" class="form-control" id="lm_out_item_label" readonly>
                </div>

                <div class="mb-2">
                    <label class="form-label">Brand Name</label>
                    <select class="form-select" id="lm_out_brand_name">
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

                <div class="mb-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" min="1" class="form-control" id="lm_out_qty" value="1">
                </div>

                <div class="mb-2">
                    <label class="form-label">Serial No / Remarks</label>
                    <input type="text" class="form-control" id="lm_out_serial_or_remarks" placeholder="">
                </div>

                <div class="mb-2">
                    <label class="form-label me-3">Returnable</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="lm_out_returnable" id="lm_out_returnable_yes" value="yes" checked>
                        <label class="form-check-label" for="lm_out_returnable_yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="lm_out_returnable" id="lm_out_returnable_no" value="no">
                        <label class="form-check-label" for="lm_out_returnable_no">No</label>
                    </div>
                </div>

                <div class="mb-2 lm-out-return-date-wrap" id="lm_out_return_date_wrap" >
                    <label class="form-label">Expected Return Date</label>
                    <input type="date" class="form-control" id="lm_out_return_date" value="<?php echo $today; ?>">
                </div>
            </div>

            <div class="modal-footer py-2">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="lm_out_save_btn">Add</button>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($row['id'])) { ?>
    <div class="border-top mt-3 pt-3">
        <button type="submit" name='submit' value='insert' class="btn btn-sm  btn-primary mt-2">
            Check Out
        </button>
    </div>
<?php } ?>

</form>

<script type="application/json" id="visitor-out-luggage-config">
<?php echo json_encode([
    'today' => $today
]); ?>
</script>

<script src="assets/js/visitor_out_view.js"></script>