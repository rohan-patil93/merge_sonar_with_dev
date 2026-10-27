<?php
if (!function_exists('getBarcodeSVG')) {
    function getBarcodeSVG($text)
    {
        $text = strtoupper(trim($text));
        $text = preg_replace('/[^0-9A-Z\-\.\ \$\/\+\%]/', '', $text);
        if ($text === '')
            return '';

        $fullText = '*' . $text . '*';

        $CODE39 = [
            '0' => '000110100',
            '1' => '100100001',
            '2' => '001100001',
            '3' => '101100000',
            '4' => '000110001',
            '5' => '100110000',
            '6' => '001110000',
            '7' => '000100101',
            '8' => '100100100',
            '9' => '001100100',
            'A' => '100001001',
            'B' => '001001001',
            'C' => '101001000',
            'D' => '000011001',
            'E' => '100011000',
            'F' => '001011000',
            'G' => '000001101',
            'H' => '100001100',
            'I' => '001001100',
            'J' => '000011100',
            'K' => '100000011',
            'L' => '001000011',
            'M' => '101000010',
            'N' => '000010011',
            'O' => '100010010',
            'P' => '001010010',
            'Q' => '000000111',
            'R' => '100000110',
            'S' => '001000110',
            'T' => '000010110',
            'U' => '110000001',
            'V' => '011000001',
            'W' => '111000000',
            'X' => '010010001',
            'Y' => '110010000',
            'Z' => '011010000',
            '-' => '010000101',
            '.' => '110000100',
            ' ' => '011000100',
            '*' => '010010100',
            '$' => '010101000',
            '/' => '010100010',
            '+' => '010001010',
            '%' => '000101010'
        ];

        $narrowWidth = 2;
        $wideWidth = 5;
        $gapWidth = 2;

        $x = 0;
        $svgPath = '';

        for ($i = 0; $i < strlen($fullText); $i++) {
            $char = $fullText[$i];
            $pattern = $CODE39[$char] ?? null;
            if ($pattern === null)
                continue;

            for ($j = 0; $j < 9; $j++) {
                $isBar = ($j % 2 === 0);
                $isWide = ($pattern[$j] === '1');
                $w = $isWide ? $wideWidth : $narrowWidth;

                if ($isBar) {
                    $svgPath .= "M {$x} 0 L {$x} 50 L " . ($x + $w) . " 50 L " . ($x + $w) . " 0 Z ";
                }
                $x += $w;
            }
            $x += $gapWidth;
        }

        $viewWidth = $x - $gapWidth;

        return '<svg width="100%" height="100%" viewBox="0 0 ' . $viewWidth . ' 50" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">' .
            '<path d="' . trim($svgPath) . '" fill="#000000" />' .
            '</svg>';
    }
}
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['custom_insert_visitor_logs'] = 1;
$message = '';

if (isset($_POST['submit']) && $_POST['submit'] === 'insert' && isset($_POST['visitor_log_id'])) {
    // Check-out processing
    $url = API . "?mode=saveVisitorOut&method=post";
    $resp = file_post_contents($url, $_POST, 'insert');
    $result = json_decode($resp, true);
    if (!empty($result) && ($result['status'] ?? '') === 'success') {
        $message = 'Checkout successfully';
        header("location:index.php?option=COM_INSERT_VISITOR_LOGS&active_tab=checkout");
        exit;
    } else {
        $message = 'Checkout failed';
    }
} elseif (isset($_POST['submit']) && in_array($_POST['submit'], ['insert', 'update'], true)) {

    if (!isset($_POST['in_gate_no']) || $_POST['in_gate_no'] == '0') {
        $message = 'Please select Gate No';
        echo "<script>alert('$message'); window.history.back();</script>";
        exit;
    }
    if (!isset($_POST['visit_type']) || $_POST['visit_type'] == '') {
        $message = 'Please select Visit Type';
        echo "<script>alert('$message'); window.history.back();</script>";
        exit;
    }

    // 1) Photo: Check webcam first, then file upload, then fallback to existing
    $existingImage = $_POST['existing_image'] ?? '';
    $webcamImage = $_POST['webcam_image'] ?? '';
    $newImageName = '';

    if (!empty($webcamImage) && strpos($webcamImage, 'data:image') === 0) {
        // Webcam capture processing
        $parts = explode(',', $webcamImage);
        if (isset($parts[1])) {
            $decodedData = base64_decode($parts[1]);
            $newImageName = 'webcam_' . time() . '_' . uniqid() . '.jpg';
            $targetDir = 'uploads/visitors/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            if (file_put_contents($targetDir . $newImageName, $decodedData) === false) {
                $newImageName = $existingImage;
            }
        } else {
            $newImageName = $existingImage;
        }
    } elseif (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $newImageName = imageupload('visitors', 'image_path', $_POST['image_path']);
    } else {
        $newImageName = $existingImage;
    }
    $_POST['image_path'] = $newImageName;

    // 2) Normalize visiting_company_id and process files for vendor_supplier
    $visit_type = $_POST['visit_type'] ?? '';
    if ($visit_type === 'delivery') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_delivery'] ?? '';
    } elseif ($visit_type === 'service') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_service'] ?? '';
    } elseif ($visit_type === 'employee' && ($_POST['emp_temp_enable'] ?? 0) == 1) {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_employee_temp'] ?? '';
    } elseif ($visit_type === 'doctor') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_doctor'] ?? '';
    } elseif ($visit_type === 'vendor_supplier') {
        $_POST['visiting_company_id'] = $_POST['vendor_supplier_transaction_no'] ?? '';

        $uploaded_proof_files = [];
        if (isset($_FILES['vendor_supplier_proof_document_file']['name']) && is_array($_FILES['vendor_supplier_proof_document_file']['name'])) {
            foreach ($_FILES['vendor_supplier_proof_document_file']['name'] as $idx => $fname) {
                if ($_FILES['vendor_supplier_proof_document_file']['error'][$idx] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($fname, PATHINFO_EXTENSION);
                    $newfilename = 'proof_' . time() . '_' . uniqid() . '.' . $ext;
                    $path = 'uploads/visitors/';
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    if (move_uploaded_file($_FILES['vendor_supplier_proof_document_file']['tmp_name'][$idx], $path . $newfilename)) {
                        $uploaded_proof_files[$idx] = $newfilename;
                    } else {
                        $uploaded_proof_files[$idx] = $_POST['existing_vendor_supplier_proof_document_file'][$idx] ?? '';
                    }
                } else {
                    $uploaded_proof_files[$idx] = $_POST['existing_vendor_supplier_proof_document_file'][$idx] ?? '';
                }
            }
        }

        $proof_types = isset($_POST['vendor_supplier_proof_type']) && is_array($_POST['vendor_supplier_proof_type']) ? implode(',', array_filter($_POST['vendor_supplier_proof_type'])) : ($_POST['vendor_supplier_proof_type'] ?? '');
        $proof_nos = isset($_POST['vendor_supplier_proof_document_no']) && is_array($_POST['vendor_supplier_proof_document_no']) ? implode(',', array_filter($_POST['vendor_supplier_proof_document_no'])) : ($_POST['vendor_supplier_proof_document_no'] ?? '');
        $proof_files = implode(',', array_filter($uploaded_proof_files));

        $_POST['vendor_supplier_proof_type'] = $proof_types;
        $_POST['vendor_supplier_proof_document_no'] = $proof_nos;
        $_POST['vendor_supplier_proof_document_file'] = $proof_files;

        $newDocFile = '';
        if (isset($_FILES['vendor_supplier_document_file']) && $_FILES['vendor_supplier_document_file']['error'] === UPLOAD_ERR_OK) {
            $newDocFile = imageupload('visitors', 'vendor_supplier_document_file', $_POST['existing_vendor_supplier_document_file'] ?? '');
        } else {
            $newDocFile = $_POST['existing_vendor_supplier_document_file'] ?? '';
        }
        $_POST['vendor_supplier_document_file'] = $newDocFile;
    } elseif ($visit_type === 'visitor' || $visit_type === 'others') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_visitor'] ?? '';
    }

    $type = $_POST['submit']; // 'insert' or 'update'
    $url = API . "?mode=insertVisitorBundle&method=post";
    $resp = file_post_contents($url, $_POST, $type);
    $result = json_decode($resp, true);

    if (!empty($result) && ($result['status'] ?? '') === 'success') {
        if ($type === 'update') {
            $message = 'Updated successfully';
            header("location:index.php?option=COM_LIST_VISITOR_LOGS");
            exit;
        } else {
            // For new inserts, redirect to print mode instead of list
            $newRid = $result['id'] ?? ($result['LASTID'] ?? ($result['rid'] ?? ''));
            header("location:index.php?option=COM_INSERT_VISITOR_LOGS&print_mode=1&print_rid=" . urlencode($newRid));
            exit;
        }
    } else {
        $message = ($type === 'update') ? 'Update failed' : 'Save failed';
    }
}

$rid = '';
$gate_id = $_SESSION['gate_id'] ?? 1;
if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
}
$url = API . "?mode=getVisitorInsertRelatedData&method=get&rid=$rid&gate_id=$gate_id&company_id=" . $_SESSION['company_id'] . "&location_id=" . $_SESSION['location_id'];

$result = callAPI($url);
$result = json_decode($result, 1);
$return = $result['visitor_logs_res'] ?? array();
$luggage_brands = $result['luggage_brand_res'] ?? [];
$editMode = ($rid !== '');
$type = strtolower($return['visit_type'] ?? '');
$purpose = strtolower($return['purpose'] ?? '');
$badge = $result['visitor_badge_res']['full_code'] ?? '';

$showVisitor = ($type === 'visitor' || $type === 'others' || $type === '');
$showDelivery = ($type === 'delivery');
$showService = ($type === 'service');
$showEmployee = ($type === 'employee');
$showDoctor = ($type === 'doctor');
$showVendorSupplier = ($type === 'vendor_supplier');

$service_tag_children = $result['service_tag_children_res'] ?? [];
$has_service_tags = !empty($service_tag_children);

$emp_temp_enable = !empty($return['emp_temp_enable']);
$emp_temp_reason = $return['emp_temp_reason'] ?? 'forgot';
$emp_temp_meet_id = (int) ($return['emp_temp_whom_to_meet'] ?? 0);

$showCompany = ($showVisitor && $purpose === 'official');
$showAddress = ($showVisitor && $purpose === 'personal');
$showPurposeOther = ($showVisitor && $purpose === 'others');
$showCard = ($type !== 'employee');

$employees_res = $result['employees_res'] ?? array();
$building_gates_res = $result['building_gates_res'] ?? array();
$visit_type_res = $result['visit_type_res'] ?? array();
$id_card_types_res = $result['id_card_types_res'] ?? array();
$delivery_company_res = $result['delivery_company_res'] ?? array();
$service_company_res = $result['service_company_res'] ?? array();
$vendors_res = $result['vendors_res'] ?? array();
$departments_res = $result['departments_res'] ?? array();

$materials_res = $result['materials_res'] ?? array(['id' => 1, 'name' => 'material_1'], ['id' => 2, 'name' => 'material_2']);
$visitor_luggages_res = $result['visitor_luggages_res'] ?? array();

// Fetch items and UOMs for No-Reference manual entry
$noref_items_res = (array) ($result['noref_items_res'] ?? []);
$noref_uom_res = (array) ($result['noref_uom_res'] ?? []);
?>

<?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', <?= json_encode($message) ?>, '');
        });
    </script>
<?php endif; ?>

<link rel="stylesheet" href="assets/css/custom_insert_visitor_logs.css">
<link rel="stylesheet" href="assets/icon/themify-icons/themify-icons.css">

<!-- JavaScript Runtime Error Banner -->
<div id="js-debug-errors" class="alert alert-danger m-3"
    style="display: none; border-left: 5px solid #d9534f; font-family: monospace;">
    <h5 class="alert-heading font-weight-bold mb-2"><i class="fa fa-exclamation-triangle"></i> JavaScript Runtime Error
        Detected</h5>
    <div id="js-error-msg"></div>
</div>

<div id="visitorConfig" data-edit-mode="<?php echo $editMode ? 1 : 0; ?>"
    data-next-badge='<?php echo htmlspecialchars(json_encode($result['next_badge'] ?? []), ENT_QUOTES); ?>'>
</div>

<script type="application/json" id="noref-items-config">
<?php echo json_encode([
    'items' => array_map(function ($it) {
        $it = (array) $it;
        return ['id' => $it['id'], 'sku' => $it['sku'] ?? '', 'item_name' => $it['item_name'] ?? '', 'purchase_uom' => $it['purchase_uom'] ?? ''];
    }, $noref_items_res),
    'uoms' => array_map(function ($u) {
        $u = (array) $u;
        return ['id' => $u['id'], 'uom_name' => $u['uom_name'] ?? '', 'uom_short_code' => $u['uom_short_code'] ?? ''];
    }, $noref_uom_res)
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>
<link rel="stylesheet" href="assets/icon/themify-icons/themify-icons.css">

<div class="page-body vms-container">
    <div class="vms-header">
        <div>
            <h5><i class="fa fa-shield-alt"></i> Visitor Management Portal</h5>
            <div class="vms-header-desc">Log check-ins, issue badges, track materials, and manage check-outs.</div>
        </div>
        <div>
            <a href="index.php?option=COM_LIST_VISITOR_LOGS" class="btn-vms btn-vms-secondary">
                <i class="fa fa-list"></i> Visitor Logs
            </a>
        </div>
    </div>

    <?php if (!$editMode): ?>
        <!-- Navigation Tabs for IN/OUT -->
        <ul class="nav nav-pills custom-vms-tabs mb-4" id="vmsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="checkin-tab" data-bs-toggle="tab" data-bs-target="#checkin-pane"
                    type="button" role="tab" aria-controls="checkin-pane" aria-selected="true">
                    <i class="fa fa-sign-in-alt me-2"></i>Gate Check-in (IN)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="checkout-tab" data-bs-toggle="tab" data-bs-target="#checkout-pane"
                    type="button" role="tab" aria-controls="checkout-pane" aria-selected="false">
                    <i class="fa fa-sign-out-alt me-2"></i>Gate Check-out (OUT)
                </button>
            </li>
        </ul>
    <?php endif; ?>

    <div class="tab-content" id="vmsTabContent">
        <!-- Check-in Tab Pane -->
        <div class="tab-pane fade show active" id="checkin-pane" role="tabpanel" aria-labelledby="checkin-tab">
            <form id="visitorFom" method="post" enctype="multipart/form-data">
                <input type="hidden" name="location_id" id="location_id"
                    value="<?php echo $_SESSION['location_id'] ?? 1; ?>">

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Step Progress Indicator -->
                        <div class="vms-step-progress mb-4">
                            <div class="step-progress-track">
                                <div class="step-progress-bar-fill" id="stepProgressBarFill"></div>
                            </div>
                            <div class="step-progress-step active" data-step="1">
                                <div class="step-icon">1</div>
                                <div class="step-label">Visit Setup</div>
                            </div>
                            <div class="step-progress-step" data-step="2">
                                <div class="step-icon">2</div>
                                <div class="step-label">Visitor Profile</div>
                            </div>
                            <div class="step-progress-step" data-step="3">
                                <div class="step-icon">3</div>
                                <div class="step-label">Verification & Materials</div>
                            </div>
                        </div>

                        <!-- STEP 1: Visit Setup -->
                        <div class="vms-step-pane active" id="step-pane-1">
                            <!-- Visit Type Selection -->
                            <div class="vms-card">
                                <div class="vms-section-title">Select Visit Type</div>
                                <div class="visit-type-grid">
                                    <?php
                                    $lockVisitType = $editMode;
                                    function vtAttrs($value, $type, $lockVisitType)
                                    {
                                        $checked = ($type === $value || ($type === '' && $value === 'visitor')) ? 'checked' : '';
                                        $disabled = ($lockVisitType && $type !== $value) ? 'disabled' : '';
                                        return trim("$checked $disabled");
                                    }
                                    ?>
                                    <label
                                        class="visit-type-card <?php echo ($type === 'visitor' || $type === '') ? 'active' : ''; ?> <?php echo $lockVisitType && $type !== 'visitor' ? 'disabled' : ''; ?>"
                                        for="vt_visitor">
                                        <input class="form-check-input" type="radio" name="visit_type" id="vt_visitor"
                                            value="visitor" <?= vtAttrs('visitor', $type, $lockVisitType) ?>>
                                        <i class="fa fa-user"></i>
                                        <span>Visitor</span>
                                    </label>

                                    <label
                                        class="visit-type-card <?php echo ($type === 'delivery') ? 'active' : ''; ?> <?php echo $lockVisitType && $type !== 'delivery' ? 'disabled' : ''; ?>"
                                        for="vt_delivery">
                                        <input class="form-check-input" type="radio" name="visit_type" id="vt_delivery"
                                            value="delivery" <?= vtAttrs('delivery', $type, $lockVisitType) ?>>
                                        <i class="fa fa-truck"></i>
                                        <span>Delivery</span>
                                    </label>

                                    <label
                                        class="visit-type-card <?php echo ($type === 'service') ? 'active' : ''; ?> <?php echo $lockVisitType && $type !== 'service' ? 'disabled' : ''; ?>"
                                        for="vt_service">
                                        <input class="form-check-input" type="radio" name="visit_type" id="vt_service"
                                            value="service" <?= vtAttrs('service', $type, $lockVisitType) ?>>
                                        <i class="fa fa-wrench"></i>
                                        <span>Service</span>
                                    </label>

                                    <label
                                        class="visit-type-card <?php echo ($type === 'employee') ? 'active' : ''; ?> <?php echo $lockVisitType && $type !== 'employee' ? 'disabled' : ''; ?>"
                                        for="vt_employee">
                                        <input class="form-check-input" type="radio" name="visit_type" id="vt_employee"
                                            value="employee" <?= vtAttrs('employee', $type, $lockVisitType) ?>>
                                        <i class="fa fa-id-badge"></i>
                                        <span>Employee</span>
                                    </label>

                                    <label
                                        class="visit-type-card <?php echo ($type === 'doctor') ? 'active' : ''; ?> <?php echo $lockVisitType && $type !== 'doctor' ? 'disabled' : ''; ?>"
                                        for="vt_doctor">
                                        <input class="form-check-input" type="radio" name="visit_type" id="vt_doctor"
                                            value="doctor" <?= vtAttrs('doctor', $type, $lockVisitType) ?>>
                                        <i class="fa fa-user-md"></i>
                                        <span>Doctor</span>
                                    </label>

                                    <label
                                        class="visit-type-card <?php echo ($type === 'vendor_supplier') ? 'active' : ''; ?> <?php echo $lockVisitType && $type !== 'vendor_supplier' ? 'disabled' : ''; ?>"
                                        for="vt_vendor_supplier">
                                        <input class="form-check-input" type="radio" name="visit_type"
                                            id="vt_vendor_supplier" value="vendor_supplier"
                                            <?= vtAttrs('vendor_supplier', $type, $lockVisitType) ?>>
                                        <i class="fa fa-handshake"></i>
                                        <span>Vendor/Supplier</span>
                                    </label>

                                    <label
                                        class="visit-type-card <?php echo ($type === 'others') ? 'active' : ''; ?> <?php echo $lockVisitType && $type !== 'others' ? 'disabled' : ''; ?>"
                                        for="vt_others">
                                        <input class="form-check-input" type="radio" name="visit_type" id="vt_others"
                                            value="others" <?= vtAttrs('others', $type, $lockVisitType) ?>>
                                        <i class="fa fa-ellipsis-h"></i>
                                        <span>Others</span>
                                    </label>
                                </div>

                                <div id="visit_type_other_wrap"
                                    class="form-group col-md-6 <?php echo ($type === 'others') ? 'd-view-block' : 'd-none'; ?>">
                                    <label for="visit_type_other">Specify Other Visit Type</label>
                                    <input type="text" class="form-control" name="visit_type_other"
                                        id="visit_type_other"
                                        value="<?php echo htmlspecialchars($return['visit_type_other'] ?? ''); ?>"
                                        placeholder="Please specify">
                                </div>

                                <div class="row">
                                    <?php
                                    $selected_gate_id = '0';
                                    if (!empty($return['in_gate_no'])) {
                                        $selected_gate_id = $return['in_gate_no'];
                                    } elseif (!empty($gate_id)) {
                                        $selected_gate_id = $gate_id;
                                    }
                                    ?>
                                    <div class="form-group col-md-12">
                                        <label class="mb-3"><i class="fa fa-door-open text-primary me-1"></i> Select
                                            Gate <b class="text-danger">*</b></label>

                                        <!-- Modern Gate Cards Grid -->
                                        <div class="gate-select-grid mb-3">
                                            <?php if (count((array) $building_gates_res) > 0) {
                                                foreach ($building_gates_res as $value) {
                                                    $isActive = ($selected_gate_id == $value['id']);
                                                    ?>
                                                    <div class="gate-select-card <?= $isActive ? 'active' : '' ?>"
                                                        data-gate-id="<?= htmlspecialchars($value['id']) ?>">
                                                        <div class="gate-card-icon"><i class="fa fa-door-open"></i></div>
                                                        <div class="gate-card-info">
                                                            <span
                                                                class="gate-name"><?= htmlspecialchars($value['name']); ?></span>
                                                        </div>
                                                        <div class="gate-check-mark"><i class="fa fa-check-circle"></i></div>
                                                    </div>
                                                <?php }
                                            } ?>
                                        </div>

                                        <!-- Hidden select for compatibility -->
                                        <select aria-label="In Gate No" class="form-select d-none" id="in_gate_no" name="in_gate_no">
                                            <option value="0">Select Gate</option>
                                            <?php if (count((array) $building_gates_res) > 0) {
                                                foreach ($building_gates_res as $value) { ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $selected_gate_id == $value['id'] ? 'selected' : '' ?>>
                                                        <?php echo htmlspecialchars($value['name']); ?>
                                                    </option>
                                                <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: Conditional Forms based on Visit Type -->
                        <div class="vms-step-pane d-none" id="step-pane-2">
                            <div class="vms-card">
                                <div
                                    class="d-flex justify-content-between align-items-center mb-3 flex-wrap border-bottom pb-2">
                                    <div class="vms-section-title mb-0" id="formSectionTitle"
                                        style="border-bottom: none; padding-bottom: 0;">Visitor Details</div>
                                    <div class="guided-toggle-wrap">
                                        <button type="button" id="btnToggleGuided" class="active-mode">
                                            <i class="fa fa-magic me-1"></i> Guided Mode (Recommended)
                                        </button>
                                    </div>
                                </div>

                                <!-- Guided Wizard Container -->
                                <div id="guidedWizardContainer" class="guided-wizard-container d-none"></div>

                                <!-- Standard Form Layout Wrapper -->
                                <div id="standardFormLayout" class="standard-form-layout">

                                    <!-- Autocomplete Profile Search Bar -->
                                    <div class="row mb-3">
                                        <div class="form-group col-md-12">
                                            <label class="form-label font-weight-bold text-dark"><i
                                                    class="fa fa-search text-primary me-1"></i> Quick Profile
                                                Autofill</label>
                                            <div class="input-group" style="position: relative;">
                                                <span class="input-group-text bg-light border-end-0"><i
                                                        class="fa fa-search text-muted"></i></span>
                                                <input type="text" id="visitor_profile_search"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Search past records by Name, Phone, or Driver details..."
                                                    autocomplete="off">
                                                <div id="search_results_container"
                                                    class="search-results-dropdown d-none">
                                                </div>
                                            </div>
                                            <div class="form-text text-muted small mt-1">Type at least 3 characters to
                                                search past records for the selected visit type. Selecting a result will
                                                auto-populate all fields.</div>
                                        </div>
                                    </div>

                                    <!-- ============ VISITOR / OTHERS ============ -->
                                    <div id="visitorDiv"
                                        class="row <?php echo $showVisitor ? 'd-view-block' : 'd-none'; ?>">
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-phone text-muted me-1"></i> Phone Number <b
                                                    class="text-danger">*</b></label>
                                            <input class="form-control" type="text" name="phone" id="phone"
                                                value="<?php echo htmlspecialchars($return['phone'] ?? ''); ?>"
                                                placeholder="Enter Phone to auto-search"
                                                onblur="lookupVisitorByPhone()">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-user text-muted me-1"></i> Name</label>
                                            <input class="form-control" type="text" name="name" id="name"
                                                value="<?php echo htmlspecialchars($return['name'] ?? ''); ?>"
                                                placeholder="Visitor Name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-user-tie text-muted me-1"></i> Whom To Meet <b
                                                    class="text-danger">*</b></label>
                                            <select class="form-select js-example-basic-single" id="whom_to_meet"
                                                name="whom_to_meet">
                                                <option value="0">Select Employee</option>
                                                <?php if (count((array) $employees_res) > 0) {
                                                    foreach ($employees_res as $e) {
                                                        $sel = (!empty($return['whom_to_meet']) && $return['whom_to_meet'] == $e['id']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-bullseye text-muted me-1"></i> Purpose</label><br>
                                            <div id="purpose-radios-container" class="d-inline-flex flex-wrap align-items-center gap-2 w-100">
                                                <?php 
                                                $visit_purpose_res = $result['visit_purpose_res'] ?? [];
                                                if (count((array)$visit_purpose_res) > 0) {
                                                    foreach ($visit_purpose_res as $vp) {
                                                        $code = htmlspecialchars($vp['genc_detail_code']);
                                                        $name = htmlspecialchars($vp['genc_detail_name']);
                                                        $checked = ($purpose === $code || ($purpose === '' && $code === 'official')) ? 'checked' : '';
                                                        ?>
                                                        <div class="form-check form-check-inline mt-2">
                                                            <input class="form-check-input" type="radio" name="purpose" id="purpose_<?= $code ?>" value="<?= $code ?>" data-icon="<?= htmlspecialchars($vp['option_icon'] ?? '') ?>" <?= $checked ?>>
                                                            <label class="form-check-label" for="purpose_<?= $code ?>">
                                                                <?php if (!empty($vp['option_icon'])): ?>
                                                                    <i class="<?= htmlspecialchars($vp['option_icon']) ?> me-1"></i>
                                                                <?php endif; ?>
                                                                <?= $name ?>
                                                            </label>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="form-check form-check-inline mt-2">
                                                        <input class="form-check-input" type="radio" name="purpose" id="purpose_official" value="official" data-icon="fa fa-briefcase" <?php echo ($purpose === 'official' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="purpose_official">Official</label>
                                                    </div>
                                                    <div class="form-check form-check-inline mt-2">
                                                        <input class="form-check-input" type="radio" name="purpose" id="purpose_personal" value="personal" data-icon="fa fa-user" <?php echo ($purpose === 'personal' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="purpose_personal">Personal</label>
                                                    </div>
                                                    <div class="form-check form-check-inline mt-2">
                                                        <input class="form-check-input" type="radio" name="purpose" id="purpose_others" value="others" data-icon="fa fa-ellipsis-h" <?php echo ($purpose === 'others' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="purpose_others">Others</label>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                                <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger py-1 px-2 mt-1" data-type="visit_purpose" data-select-id="purpose" data-label="Purpose" style="height: auto; width: auto;"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div id="purpose_other_wrap"
                                            class="form-group col-md-6 <?php echo $showPurposeOther ? 'd-view-block' : 'd-none'; ?>">
                                            <label><i class="fa fa-comment-dots text-muted me-1"></i> Specify Visit
                                                Purpose</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="purpose_other"
                                                    id="purpose_other"
                                                    value="<?php echo htmlspecialchars($return['purpose_other'] ?? ''); ?>"
                                                    placeholder="Explain purpose">
                                                <button type="button" class="btn btn-primary btn-add-inline-other" data-type="visit_purpose" data-input-id="purpose_other" data-select-id="purpose"><i class="fa fa-plus"></i> Add</button>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 d-none">
                                            <label><i class="fa fa-id-badge text-muted me-1"></i> Visiting Id Card No <b
                                                    class="text-danger">*</b></label>
                                            <input class="form-control" type="hidden" name="visiting_company_id_visitor"
                                                id="visiting_company_id_visitor"
                                                value="<?php echo htmlspecialchars($badge ?: ($return['visiting_company_id'] ?? '')); ?>">
                                        </div>

                                        <div class="col-md-12 toggle-btn-wrapper my-2">
                                            <button type="button"
                                                class="btn btn-vms-secondary btn-sm w-100 py-2 toggle-additional-btn">
                                                <i class="fa fa-plus-circle me-1"></i> Add Optional Details (ID Proof,
                                                Vehicle, Gender...)
                                            </button>
                                        </div>

                                        <div class="additional-fields-section d-none row w-100 m-0 p-0">
                                            <div id="company_name_wrap"
                                                class="form-group col-md-6 <?php echo $showCompany ? 'd-view-block' : 'd-none'; ?>">
                                                <label><i class="fa fa-building text-muted me-1"></i> Company
                                                    Name</label>
                                                <input class="form-control" type="text" name="company_name"
                                                    id="company_name"
                                                    value="<?php echo htmlspecialchars($return['company_name'] ?? ''); ?>"
                                                    placeholder="Visitor's Company">
                                            </div>
                                            <div id="address_wrap"
                                                class="form-group col-md-6 <?php echo $showAddress ? 'd-view-block' : 'd-none'; ?>">
                                                <label><i class="fa fa-map-marker-alt text-muted me-1"></i>
                                                    Address</label>
                                                <textarea class="form-control" rows="3" id="address" name="address"
                                                    placeholder="Visitor's Address"><?php echo htmlspecialchars($return['address'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-id-card text-muted me-1"></i> Identity Card
                                                    Type</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <select class="form-select js-example-basic-single flex-grow-1" id="idcard_type"
                                                        name="idcard_type">
                                                        <option value="0">Select ID Card</option>
                                                        <?php if (count((array) $id_card_types_res) > 0) {
                                                            foreach ($id_card_types_res as $value) {
                                                                $sel = (!empty($return['idcard_type']) && $return['idcard_type'] == $value['genc_detail_code']) ? 'selected' : '';
                                                                $icon = !empty($value['option_icon']) ? htmlspecialchars($value['option_icon']) : '';
                                                                echo '<option value="' . htmlspecialchars($value['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($value['genc_detail_name']) . '</option>';
                                                            }
                                                        } ?>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger" data-type="identity_card" data-select-id="idcard_type" data-label="ID Card Type"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-hashtag text-muted me-1"></i> Identity Card
                                                    No.</label>
                                                <input class="form-control" type="text" name="idcard_number"
                                                    id="idcard_number"
                                                    value="<?php echo htmlspecialchars($return['idcard_number'] ?? ''); ?>"
                                                    placeholder="ID Card Number">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-venus-mars text-muted me-1"></i>
                                                    Gender</label><br>
                                                <div class="form-check form-check-inline mt-2">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="gender_male" value="Male" <?php echo (($return['gender'] ?? '') === 'Male' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="gender_male">Male</label>
                                                </div>
                                                <div class="form-check form-check-inline mt-2">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="gender_female" value="Female" <?php echo (($return['gender'] ?? '') === 'Female' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="gender_female">Female</label>
                                                </div>
                                                <div class="form-check form-check-inline mt-2">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="gender_other" value="Other" <?php echo (($return['gender'] ?? '') === 'Other' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="gender_other">Other</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-clock text-muted me-1"></i> Expected Duration of
                                                    Visit</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <select class="form-select js-example-basic-single flex-grow-1"
                                                        id="expected_duration" name="expected_duration">
                                                        <option value="">Select Duration</option>
                                                        <?php 
                                                         $expected_duration_res = $result['expected_duration_res'] ?? [];
                                                         if (count((array) $expected_duration_res) > 0) {
                                                             foreach ($expected_duration_res as $value) {
                                                                 $sel = (!empty($return['expected_duration']) && $return['expected_duration'] == $value['genc_detail_code']) ? 'selected' : '';
                                                                 $icon = !empty($value['option_icon']) ? htmlspecialchars($value['option_icon']) : '';
                                                                 echo '<option value="' . htmlspecialchars($value['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($value['genc_detail_name']) . '</option>';
                                                             }
                                                         } else {
                                                            ?>
                                                            <option value="1 hour" data-icon="ti-time" <?php echo (($return['expected_duration'] ?? '') === '1 hour' ? 'selected' : ''); ?>>1 Hour</option>
                                                            <option value="2 hours" data-icon="ti-time" <?php echo (($return['expected_duration'] ?? '') === '2 hours' ? 'selected' : ''); ?>>2 Hours</option>
                                                            <option value="4 hours" data-icon="ti-time" <?php echo (($return['expected_duration'] ?? '') === '4 hours' ? 'selected' : ''); ?>>4 Hours</option>
                                                            <option value="Full Day" data-icon="ti-time" <?php echo (($return['expected_duration'] ?? '') === 'Full Day' ? 'selected' : ''); ?>>Full Day</option>
                                                            <option value="Multi-day" data-icon="ti-time" <?php echo (($return['expected_duration'] ?? '') === 'Multi-day' ? 'selected' : ''); ?>>Multi-day</option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger" data-type="expected_duration" data-select-id="expected_duration" data-label="Expected Duration"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-car text-muted me-1"></i> Vehicle Type</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <select class="form-select js-example-basic-single flex-grow-1" id="vehicle_type"
                                                        name="vehicle_type">
                                                        <option value="">None / Walk-in</option>
                                                        <?php 
                                                        $vehicle_type_res = $result['vehicle_type_res'] ?? [];
                                                        if (count((array) $vehicle_type_res) > 0) {
                                                            foreach ($vehicle_type_res as $value) {
                                                                $sel = ((string)($return['vehicle_type'] ?? '') === (string)$value['genc_detail_code']) ? 'selected' : '';
                                                                $icon = !empty($value['option_icon']) ? htmlspecialchars($value['option_icon']) : '';
                                                                echo '<option value="' . htmlspecialchars($value['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($value['genc_detail_name']) . '</option>';
                                                            }
                                                        } else {
                                                            ?>
                                                            <option value="Two Wheeler" data-icon="ti-car" <?php echo (($return['vehicle_type'] ?? '') === 'Two Wheeler' ? 'selected' : ''); ?>>Two Wheeler</option>
                                                            <option value="Four Wheeler" data-icon="ti-car" <?php echo (($return['vehicle_type'] ?? '') === 'Four Wheeler' ? 'selected' : ''); ?>>Four Wheeler</option>
                                                            <option value="Heavy Vehicle" data-icon="ti-car" <?php echo (($return['vehicle_type'] ?? '') === 'Heavy Vehicle' ? 'selected' : ''); ?>>Heavy Vehicle</option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger" data-type="vehicle_type" data-select-id="vehicle_type" data-label="Vehicle Type"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-sort-numeric-up text-muted me-1"></i> Vehicle
                                                    Number</label>
                                                <input class="form-control" type="text" name="vehicle_number"
                                                    id="vehicle_number"
                                                    value="<?php echo htmlspecialchars($return['vehicle_number'] ?? ''); ?>"
                                                    placeholder="e.g. MH 12 AB 1234">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ============ DELIVERY ============ -->
                                    <div id="deliveryDiv"
                                        class="row <?php echo $showDelivery ? 'd-view-block' : 'd-none'; ?>">
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-truck text-muted me-1"></i> Delivery Company/Person
                                                <b class="text-danger">*</b></label>
                                            <div class="d-flex align-items-center gap-1">
                                                <select class="form-select js-example-basic-single flex-grow-1"
                                                    id="delivery_company_person" name="delivery_company_person">
                                                    <option value="">Select Company</option>
                                                    <?php if (!empty($delivery_company_res)) {
                                                        foreach ($delivery_company_res as $e) {
                                                            $sel = (!empty($return['delivery_company_person']) && $return['delivery_company_person'] == $e['genc_detail_code']) ? 'selected' : '';
                                                            $icon = !empty($e['option_icon']) ? htmlspecialchars($e['option_icon']) : '';
                                                            echo '<option value="' . htmlspecialchars($e['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($e['genc_detail_name']) . '</option>';
                                                        }
                                                    } ?>
                                                    <option value="__new__">+ Add New Company</option>
                                                </select>
                                                <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger" data-type="delivery_company" data-select-id="delivery_company_person" data-label="Delivery Partner"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <div id="new_company_box" class="d-none mt-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="new_delivery_company"
                                                        id="new_delivery_company" placeholder="Enter new company name">
                                                    <button type="button" class="btn btn-primary btn-add-inline-other" data-type="delivery_company" data-input-id="new_delivery_company" data-select-id="delivery_company_person"><i class="fa fa-plus"></i> Add</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-phone text-muted me-1"></i> Contact Number</label>
                                            <input class="form-control" type="text" name="delivery_phone"
                                                id="delivery_phone"
                                                value="<?php echo htmlspecialchars($return['delivery_phone'] ?? ''); ?>"
                                                placeholder="Delivery Phone">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-user-tie text-muted me-1"></i> Whom To Deliver <b
                                                    class="text-danger">*</b></label>
                                            <select class="form-select js-example-basic-single" id="deliver_to"
                                                name="deliver_to">
                                                <option value="0">Select Employee</option>
                                                <?php if (!empty($employees_res)) {
                                                    foreach ($employees_res as $e) {
                                                        $sel = (!empty($return['deliver_to']) && $return['deliver_to'] == $e['id']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-box text-muted me-1"></i> Delivery Type</label>
                                            <div class="d-flex align-items-center gap-1">
                                                <select class="form-select js-example-basic-single flex-grow-1" id="delivery_type"
                                                    name="delivery_type">
                                                    <option value="">Select Delivery Type</option>
                                                    <?php 
                                                    $delivery_type_res = $result['delivery_type_res'] ?? [];
                                                    if (count((array) $delivery_type_res) > 0) {
                                                        foreach ($delivery_type_res as $value) {
                                                            $sel = ((string)($return['delivery_type'] ?? '') === (string)$value['genc_detail_code']) ? 'selected' : '';
                                                            $icon = !empty($value['option_icon']) ? htmlspecialchars($value['option_icon']) : '';
                                                            echo '<option value="' . htmlspecialchars($value['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($value['genc_detail_name']) . '</option>';
                                                        }
                                                    } else {
                                                        ?>
                                                        <option value="Courier" data-icon="ti-package" <?php echo (($return['delivery_type'] ?? '') === 'Courier' ? 'selected' : ''); ?>>Courier / Package</option>
                                                        <option value="Food Delivery" data-icon="ti-package" <?php echo (($return['delivery_type'] ?? '') === 'Food Delivery' ? 'selected' : ''); ?>>Food / Beverage</option>
                                                        <option value="Office Supplies" data-icon="ti-package" <?php echo (($return['delivery_type'] ?? '') === 'Office Supplies' ? 'selected' : ''); ?>>Office Supplies</option>
                                                        <option value="Heavy Cargo" data-icon="ti-package" <?php echo (($return['delivery_type'] ?? '') === 'Heavy Cargo' ? 'selected' : ''); ?>>Heavy Cargo</option>
                                                        <option value="Miscellaneous" data-icon="ti-package" <?php echo (($return['delivery_type'] ?? '') === 'Miscellaneous' ? 'selected' : ''); ?>>Miscellaneous</option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger" data-type="delivery_type" data-select-id="delivery_type" data-label="Delivery Type"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-car text-muted me-1"></i> Delivery Vehicle
                                                Number</label>
                                            <input class="form-control" type="text" name="vehicle_number_delivery"
                                                id="vehicle_number_delivery"
                                                value="<?php echo htmlspecialchars($return['vehicle_number'] ?? ''); ?>"
                                                placeholder="e.g. MH 12 AB 1234">
                                        </div>

                                        <div class="form-group col-md-6 d-none">
                                            <label><i class="fa fa-id-badge text-muted me-1"></i> Visiting Id Card No <b
                                                    class="text-danger">*</b></label>
                                            <input class="form-control" type="hidden"
                                                name="visiting_company_id_delivery" id="visiting_company_id_delivery"
                                                value="<?php
                                                $badgeVal = $result['visitor_badge_res']['full_code'] ?? ($result['next_badge']['full_code'] ?? '');
                                                echo htmlspecialchars($return['visiting_company_id'] ?? $badgeVal);
                                                ?>">
                                        </div>

                                        <div class="col-md-12 toggle-btn-wrapper my-2">
                                            <button type="button"
                                                class="btn btn-vms-secondary btn-sm w-100 py-2 toggle-additional-btn">
                                                <i class="fa fa-plus-circle me-1"></i> Add Optional Details (AWB,
                                                Package,
                                                Security...)
                                            </button>
                                        </div>

                                        <div class="additional-fields-section d-none row w-100 m-0 p-0">
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-barcode text-muted me-1"></i> AWB No. (Tracking
                                                    Number)</label>
                                                <input class="form-control" type="text" name="delivery_awb_no"
                                                    id="delivery_awb_no"
                                                    value="<?php echo htmlspecialchars($return['delivery_awb_no'] ?? ''); ?>"
                                                    placeholder="AWB/Ref No.">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-boxes text-muted me-1"></i> Package Count</label>
                                                <input class="form-control" type="number" min="1" name="package_count"
                                                    id="package_count"
                                                    value="<?php echo htmlspecialchars($return['package_count'] ?? '1'); ?>">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-shield-alt text-muted me-1"></i> Security Checked
                                                    /
                                                    Cleared?</label><br>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="security_cleared" name="security_cleared" value="1" <?php echo (!empty($return['security_cleared']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label ms-2" for="security_cleared">Yes,
                                                        Cleared</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ============ SERVICE ============ -->
                                    <div id="serviceDiv"
                                        class="row <?php echo $showService ? 'd-view-block' : 'd-none'; ?>">
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-tools text-muted me-1"></i> Service Company <b
                                                    class="text-danger">*</b></label>
                                            <div class="d-flex align-items-center gap-1">
                                                <select class="form-select js-example-basic-single flex-grow-1" id="service_company"
                                                    name="service_company">
                                                    <option value="">Select Service Company</option>
                                                    <?php if (!empty($service_company_res)) {
                                                        foreach ($service_company_res as $e) {
                                                            $sel = (!empty($return['service_company']) && $return['service_company'] == $e['genc_detail_code']) ? 'selected' : '';
                                                            $icon = !empty($e['option_icon']) ? htmlspecialchars($e['option_icon']) : '';
                                                            echo '<option value="' . htmlspecialchars($e['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($e['genc_detail_name']) . '</option>';
                                                        }
                                                    } ?>
                                                    <option value="__new__">+ Add New Company</option>
                                                </select>
                                                <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger" data-type="service_company" data-select-id="service_company" data-label="Service Company"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <div id="new_service_company_box" class="d-none mt-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="new_service_company"
                                                        id="new_service_company" placeholder="Enter new company name">
                                                    <button type="button" class="btn btn-primary btn-add-inline-other" data-type="service_company" data-input-id="new_service_company" data-select-id="service_company"><i class="fa fa-plus"></i> Add</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-phone text-muted me-1"></i> Service Person
                                                Phone</label>
                                            <input class="form-control" type="text" name="service_phone"
                                                id="service_phone"
                                                value="<?php echo htmlspecialchars($return['service_phone'] ?? ''); ?>"
                                                placeholder="Phone">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-user text-muted me-1"></i> Service Person
                                                Name</label>
                                            <input class="form-control" type="text" name="service_person"
                                                id="service_person"
                                                value="<?php echo htmlspecialchars($return['service_person'] ?? ''); ?>"
                                                placeholder="Name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-user-check text-muted me-1"></i> Authorized By
                                                (Employee)
                                                <b class="text-danger">*</b></label>
                                            <select class="form-select js-example-basic-single" id="authorized_employee"
                                                name="authorized_employee">
                                                <option value="0">Select Employee</option>
                                                <?php if (!empty($employees_res)) {
                                                    foreach ($employees_res as $e) {
                                                        $sel = (!empty($return['authorized_employee']) && $return['authorized_employee'] == $e['id']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 d-none">
                                            <label><i class="fa fa-id-badge text-muted me-1"></i> Visiting Id Card No <b
                                                    class="text-danger">*</b></label>
                                            <input class="form-control" type="hidden" name="visiting_company_id_service"
                                                id="visiting_company_id_service" value="<?php
                                                $badgeVal = $result['visitor_badge_res']['full_code'] ?? ($result['next_badge']['full_code'] ?? '');
                                                echo htmlspecialchars($return['visiting_company_id'] ?? $badgeVal);
                                                ?>">
                                        </div>

                                        <div class="col-md-12 toggle-btn-wrapper my-2">
                                            <button type="button"
                                                class="btn btn-vms-secondary btn-sm w-100 py-2 toggle-additional-btn">
                                                <i class="fa fa-plus-circle me-1"></i> Add Optional Details (ID Proof,
                                                Group
                                                Size, Safety...)
                                            </button>
                                        </div>

                                        <div class="additional-fields-section d-none row w-100 m-0 p-0">
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-id-card text-muted me-1"></i> Identity Card
                                                    Type</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <select class="form-select js-example-basic-single flex-grow-1"
                                                        id="idcard_type_service" name="idcard_type_service">
                                                        <option value="0">Select ID Card</option>
                                                        <?php if (!empty($id_card_types_res)) {
                                                            foreach ($id_card_types_res as $v) {
                                                                $sel = (!empty($return['idcard_type_service']) && $return['idcard_type_service'] == $v['genc_detail_code']) ? 'selected' : '';
                                                                $icon = !empty($v['option_icon']) ? htmlspecialchars($v['option_icon']) : '';
                                                                echo '<option value="' . htmlspecialchars($v['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($v['genc_detail_name']) . '</option>';
                                                            }
                                                        } ?>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger" data-type="identity_card" data-select-id="idcard_type_service" data-label="ID Card Type"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-hashtag text-muted me-1"></i> Identity Card
                                                    No.</label>
                                                <input class="form-control" type="text" name="idcard_number_service"
                                                    id="idcard_number_service"
                                                    value="<?php echo htmlspecialchars($return['idcard_number_service'] ?? ''); ?>"
                                                    placeholder="ID Card Number">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-tags text-muted me-1"></i> Service
                                                    Classification</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <select class="form-select js-example-basic-single flex-grow-1"
                                                        id="service_classification" name="service_classification">
                                                        <option value="">Select Classification</option>
                                                        <?php 
                                                        $service_classification_res = $result['service_classification_res'] ?? [];
                                                        if (count((array) $service_classification_res) > 0) {
                                                            foreach ($service_classification_res as $value) {
                                                                $sel = ((string)($return['service_classification'] ?? '') === (string)$value['genc_detail_code']) ? 'selected' : '';
                                                                $icon = !empty($value['option_icon']) ? htmlspecialchars($value['option_icon']) : '';
                                                                echo '<option value="' . htmlspecialchars($value['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($value['genc_detail_name']) . '</option>';
                                                            }
                                                        } else {
                                                            ?>
                                                            <option value="IT Support" data-icon="ti-settings" <?php echo (($return['service_classification'] ?? '') === 'IT Support' ? 'selected' : ''); ?>>IT Support / Hardware</option>
                                                            <option value="HVAC Maintenance" data-icon="ti-support" <?php echo (($return['service_classification'] ?? '') === 'HVAC Maintenance' ? 'selected' : ''); ?>>HVAC Maintenance</option>
                                                            <option value="Electrical" data-icon="ti-bolt" <?php echo (($return['service_classification'] ?? '') === 'Electrical' ? 'selected' : ''); ?>>Electrical Works</option>
                                                            <option value="Plumbing" data-icon="ti-wand" <?php echo (($return['service_classification'] ?? '') === 'Plumbing' ? 'selected' : ''); ?>>Plumbing Works</option>
                                                            <option value="Cleaning" data-icon="ti-brush" <?php echo (($return['service_classification'] ?? '') === 'Cleaning' ? 'selected' : ''); ?>>Cleaning / Housekeeping</option>
                                                            <option value="General Maintenance" data-icon="ti-settings" <?php echo (($return['service_classification'] ?? '') === 'General Maintenance' ? 'selected' : ''); ?>>General Maintenance</option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger" data-type="service_classification" data-select-id="service_classification" data-label="Service Classification"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-file-contract text-muted me-1"></i> Work Permit
                                                    Number</label>
                                                <input class="form-control" type="text" name="work_permit_no"
                                                    id="work_permit_no"
                                                    value="<?php echo htmlspecialchars($return['work_permit_no'] ?? ''); ?>"
                                                    placeholder="Permit reference (optional)">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-hard-hat text-muted me-1"></i> Safety Briefing
                                                    Completed?</label><br>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" id="safety_briefing"
                                                        name="safety_briefing" value="1" <?php echo (!empty($return['safety_briefing']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label ms-2" for="safety_briefing">Yes,
                                                        Briefed</label>
                                                </div>
                                            </div>

                                            <?php if ($rid != '' && ($return['visit_type'] ?? '') === 'service') { ?>
                                                <div class="form-group col-md-12 mt-2">
                                                    <button type="button" class="btn btn-sm btn-primary" id="btnWorkPermit"
                                                        data-url="index.php?option=COM_VIEW_VISITOR_WORK_PERMIT_PASS&Rid=<?php echo encodeId('id:' . $rid); ?>">
                                                        <i class="fa fa-print"></i> Print Work Permit Pass
                                                    </button>
                                                </div>
                                            <?php } ?>

                                            <!-- Tag Multiple Visitors controls -->
                                            <div class="form-group col-md-12 mt-2">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="service_tag_enable" name="service_tag_enable" value="1"
                                                        <?php echo $has_service_tags ? 'checked' : ''; ?>>
                                                    <label class="form-check-label ms-2" for="service_tag_enable">Tag
                                                        Multiple Visitors (Group entry)</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="row <?php echo $has_service_tags ? 'd-view-block' : 'd-none'; ?>"
                                                    id="serviceTagControls">
                                                    <div class="form-group col-md-4">
                                                        <label><i class="fa fa-users text-muted me-1"></i> Group Size
                                                            (How
                                                            many visitors?)</label>
                                                        <input type="number" min="1" step="1" class="form-control"
                                                            name="service_tag_count" id="service_tag_count"
                                                            placeholder="e.g. 3"
                                                            value="<?php echo $has_service_tags ? count((array) $service_tag_children) : ''; ?>"
                                                            <?php echo count((array) $service_tag_children) > 0 ? 'readonly' : ''; ?>>
                                                    </div>
                                                    <div class="form-group col-md-4 d-flex align-items-end">
                                                        <button type="button" class="btn btn-vms-secondary btn-sm"
                                                            id="service_tag_details_btn"><i class="fa fa-list"></i>
                                                            Setup
                                                            Group Details</button>
                                                        <span id="serviceTagSummary"
                                                            class="badge bg-primary ms-3 <?php echo $has_service_tags ? 'd-inline-show' : 'd-none'; ?>">Saved:
                                                            <?php echo $has_service_tags ? count((array) $service_tag_children) : 0; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden inputs generated after Group Details Modal Save -->
                                        <div id="serviceTagHiddenInputs">
                                            <?php if ($has_service_tags): ?>
                                                <?php foreach ($service_tag_children as $i => $t):
                                                    $tName = htmlspecialchars($t['service_person'] ?? '');
                                                    $tCode = htmlspecialchars($t['visiting_company_id'] ?? ($t['visitor_idcard_no'] ?? '')); ?>
                                                    <input type="hidden" name="service_tags[<?php echo $i; ?>][name]"
                                                        value="<?php echo $tName; ?>">
                                                    <input type="hidden" name="service_tags[<?php echo $i; ?>][visitor_id]"
                                                        value="<?php echo $tCode; ?>">
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Modal: Group visitor setup -->
                                        <div class="modal fade" id="serviceTagModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header py-3 border-bottom">
                                                        <h6 class="modal-title font-weight-bold">Group Visitor Details
                                                            Setup
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-muted small mb-3">
                                                            Base Card ID: <span id="stm_base_id"
                                                                class="font-weight-bold text-dark">—</span> &nbsp; |
                                                            &nbsp;
                                                            Total Count: <span id="stm_count"
                                                                class="font-weight-bold text-dark">0</span>
                                                        </p>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm align-middle"
                                                                id="serviceTagTableModal">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th class="minw-220">Visitor Name</th>
                                                                        <th class="minw-180">Card ID Assigned</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer py-2 border-top">
                                                        <button type="button" class="btn btn-vms-secondary btn-sm"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="button" class="btn btn-vms-primary btn-sm"
                                                            id="service_tag_save_btn">Save Group</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- ============ EMPLOYEE ============ -->
                                    <div id="employeeDiv"
                                        class="row <?php echo $showEmployee ? 'd-view-block' : 'd-none'; ?>">
                                        <div class="form-group col-md-4">
                                            <label><i class="fa fa-id-card-alt text-muted me-1"></i> Employee ID <b
                                                    class="text-danger">*</b></label>
                                            <input class="form-control" type="text" name="employee_code"
                                                id="employee_code"
                                                value="<?php echo htmlspecialchars($return['employee_code'] ?? ''); ?>"
                                                placeholder="Enter Emp ID">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label><i class="fa fa-user text-muted me-1"></i> Name</label>
                                            <input class="form-control" type="text" name="employee_name"
                                                id="employee_name"
                                                value="<?php echo htmlspecialchars($return['employee_name'] ?? ''); ?>"
                                                placeholder="Employee Name">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label><i class="fa fa-building text-muted me-1"></i> Department</label>
                                            <input class="form-control" type="text" name="employee_department"
                                                id="employee_department"
                                                value="<?php echo htmlspecialchars($return['employee_department'] ?? ''); ?>"
                                                placeholder="Department">
                                        </div>

                                        <?php if ($rid != '' && ($return['visit_type'] ?? '') === 'employee') { ?>
                                            <div class="form-group col-md-12 mt-2">
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    id="btnMaterialGatePass"
                                                    data-url="index.php?option=COM_VIEW_VISITOR_MATERIAL_GATE_PASS&Rid=<?php echo encodeId('id:' . $rid); ?>">
                                                    <i class="fa fa-print"></i> Print Material Gate Pass
                                                </button>
                                            </div>
                                        <?php } ?>

                                        <div class="col-md-12 toggle-btn-wrapper my-2">
                                            <button type="button"
                                                class="btn btn-vms-secondary btn-sm w-100 py-2 toggle-additional-btn">
                                                <i class="fa fa-plus-circle me-1"></i> Add Optional Details (Temporary
                                                Pass, Reason...)
                                            </button>
                                        </div>

                                        <div class="additional-fields-section d-none row w-100 m-0 p-0">
                                            <!-- Issue temporary visitor pass for employee -->
                                            <div class="form-group col-md-12 mt-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="emp_temp_enable"
                                                        name="emp_temp_enable" value="1" <?php echo $emp_temp_enable ? 'checked' : ''; ?>>
                                                    <label class="form-check-label ms-2" for="emp_temp_enable">Issue
                                                        Visitor
                                                        Pass (Temp Pass / Forgot ID)</label>
                                                </div>
                                            </div>

                                            <div id="empTempReasonWrap" class="form-group col-md-12 d-none">
                                                <label><i class="fa fa-question-circle text-muted me-1"></i> Reason for
                                                    Temp
                                                    Pass</label><br>
                                                <div class="form-check form-check-inline mt-1">
                                                    <input class="form-check-input" type="radio" name="emp_temp_reason"
                                                        id="emp_reason_forgot" value="forgot" <?php echo ($emp_temp_reason == 'forgot') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="emp_reason_forgot"><i class="fa fa-id-card me-1"></i>Forgot ID
                                                        Card</label>
                                                </div>
                                                <div class="form-check form-check-inline mt-1">
                                                    <input class="form-check-input" type="radio" name="emp_temp_reason"
                                                        id="emp_reason_new" value="new" <?php echo ($emp_temp_reason === 'new') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="emp_reason_new"><i class="fa fa-user-plus me-1"></i>New Employee
                                                        (Onboarding)</label>
                                                </div>
                                                <div class="form-check form-check-inline mt-1">
                                                    <input class="form-check-input" type="radio" name="emp_temp_reason"
                                                        id="emp_reason_xbranch" value="xbranch" <?php echo ($emp_temp_reason === 'xbranch') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="emp_reason_xbranch"><i class="fa fa-building me-1"></i>Visiting
                                                        from
                                                        another Branch</label>
                                                </div>
                                            </div>

                                            <div id="empTempFields" class="row d-none w-100 m-0">
                                                <div class="form-group col-md-6 pl-0">
                                                    <label><i class="fa fa-phone text-muted me-1"></i> Phone <b
                                                            class="text-danger">*</b></label>
                                                    <input class="form-control" type="text" name="emp_temp_phone"
                                                        id="emp_temp_phone"
                                                        value="<?php echo $emp_temp_enable ? htmlspecialchars($return['emp_temp_phone'] ?? '') : ''; ?>"
                                                        placeholder="Emp Temp Phone">
                                                </div>
                                                <div class="form-group col-md-6 pr-0">
                                                    <label><i class="fa fa-user text-muted me-1"></i> Name <b
                                                            class="text-danger">*</b></label>
                                                    <input class="form-control" type="text" name="emp_temp_name"
                                                        id="emp_temp_name"
                                                        value="<?php echo $emp_temp_enable ? htmlspecialchars($return['emp_temp_name'] ?? '') : ''; ?>"
                                                        placeholder="Emp Temp Name">
                                                </div>
                                                <div class="form-group col-md-6 pl-0" id="empTempHostWrap">
                                                    <label><i class="fa fa-user-tie text-muted me-1"></i> Whom To Meet
                                                        (Host
                                                        Employee) <b class="text-danger">*</b></label>
                                                    <select class="form-select js-example-basic-single"
                                                        id="emp_temp_whom_to_meet" name="emp_temp_whom_to_meet">
                                                        <option value="0">Select Employee</option>
                                                        <?php if (!empty($employees_res)) {
                                                            foreach ($employees_res as $e) {
                                                                $sel = ($emp_temp_meet_id && $emp_temp_meet_id == $e['id']) ? 'selected' : '';
                                                                echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                                                            }
                                                        } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6 pr-0 d-none" id="empTempVisitorIdWrap">
                                                    <label><i class="fa fa-id-badge text-muted me-1"></i> Temp Card No
                                                        <b class="text-danger">*</b></label>
                                                    <input class="form-control" type="text"
                                                        name="visiting_company_id_employee_temp"
                                                        id="visiting_company_id_employee_temp"
                                                        value="<?php echo $emp_temp_enable ? htmlspecialchars($return['visiting_company_id'] ?? '') : ''; ?>"
                                                        placeholder="Temp Badge ID">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ============ DOCTOR ============ -->
                                    <div id="doctorDiv"
                                        class="row <?php echo $showDoctor ? 'd-view-block' : 'd-none'; ?>">
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-phone text-muted me-1"></i> Phone Number <b
                                                    class="text-danger">*</b></label>
                                            <input class="form-control" type="text" name="doctor_phone"
                                                id="doctor_phone"
                                                value="<?php echo htmlspecialchars($return['doctor_phone'] ?? ''); ?>"
                                                placeholder="Phone">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-user-md text-muted me-1"></i> Doctor Name <b
                                                    class="text-danger">*</b></label>
                                            <input class="form-control" type="text" name="doctor_name" id="doctor_name"
                                                value="<?php echo htmlspecialchars($return['doctor_name'] ?? ''); ?>"
                                                placeholder="Name">
                                        </div>

                                        <div class="form-group col-md-6 d-none">
                                            <label><i class="fa fa-id-badge text-muted me-1"></i> Visiting Id Card No <b
                                                    class="text-danger">*</b></label>
                                            <input class="form-control" type="hidden" name="visiting_company_id_doctor"
                                                id="visiting_company_id_doctor"
                                                value="<?php echo htmlspecialchars($badge ?: ($return['visiting_company_id'] ?? '')); ?>">
                                        </div>

                                        <div class="col-md-12 toggle-btn-wrapper my-2">
                                            <button type="button"
                                                class="btn btn-vms-secondary btn-sm w-100 py-2 toggle-additional-btn">
                                                <i class="fa fa-plus-circle me-1"></i> Add Optional Details
                                                (Specialization,
                                                ID Proof...)
                                            </button>
                                        </div>

                                        <div class="additional-fields-section d-none row w-100 m-0 p-0">
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-id-card text-muted me-1"></i> Identity Card
                                                    Type</label>
                                                <select class="form-select js-example-basic-single"
                                                    id="doctor_idcard_type" name="doctor_idcard_type">
                                                    <option value="0">Select ID Card</option>
                                                    <?php if (count((array) $id_card_types_res) > 0) {
                                                         foreach ($id_card_types_res as $value) {
                                                             $sel = (!empty($return['doctor_idcard_type']) && $return['doctor_idcard_type'] == $value['genc_detail_code']) ? 'selected' : '';
                                                             $icon = !empty($value['option_icon']) ? htmlspecialchars($value['option_icon']) : '';
                                                             echo '<option value="' . htmlspecialchars($value['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($value['genc_detail_name']) . '</option>';
                                                         }
                                                     } ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-hashtag text-muted me-1"></i> Identity Card
                                                    No.</label>
                                                <input class="form-control" type="text" name="doctor_idcard_number"
                                                    id="doctor_idcard_number"
                                                    value="<?php echo htmlspecialchars($return['doctor_idcard_number'] ?? ''); ?>"
                                                    placeholder="ID Card Number">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-stethoscope text-muted me-1"></i> Specialization
                                                    /
                                                    Department</label>
                                                <input class="form-control" type="text" name="doctor_specialization"
                                                    id="doctor_specialization"
                                                    value="<?php echo htmlspecialchars($return['doctor_specialization'] ?? ''); ?>"
                                                    placeholder="e.g. Cardiology, Emergency">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-exclamation-triangle text-muted me-1"></i>
                                                    Emergency
                                                    Visit?</label><br>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" id="is_emergency"
                                                        name="is_emergency" value="1" <?php echo (!empty($return['is_emergency']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label ms-2" for="is_emergency">Yes,
                                                        Emergency</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ============ VENDOR / SUPPLIER ============ -->
                                    <div id="vendor_supplierDiv"
                                        class="row <?php echo $showVendorSupplier ? 'd-view-block' : 'd-none'; ?>">
                                        <!-- 1. Department -->
                                        <div class="form-group col-md-4">
                                            <label><i class="fa fa-building text-muted me-1"></i> Department <b
                                                    class="text-danger">*</b></label>
                                            <select class="form-select js-example-basic-single"
                                                name="vendor_supplier_department_id" id="vendor_supplier_department_id">
                                                <option value=""> Select Any one </option>
                                                <?php foreach ($departments_res as $d): ?>
                                                    <option value="<?php echo $d['id']; ?>" <?php echo ($return['vendor_supplier_department_id'] ?? '') == $d['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($d['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- 2. Whom to Meet -->
                                        <div class="form-group col-md-4">
                                            <label><i class="fa fa-user-tie text-muted me-1"></i> Whom To Meet <b
                                                    class="text-danger">*</b></label>
                                            <select class="form-select js-example-basic-single" id="whom_to_meet_vendor"
                                                name="whom_to_meet_vendor">
                                                <option value="0">Select Employee</option>
                                                <?php if (count((array) $employees_res) > 0) {
                                                    foreach ($employees_res as $e) {
                                                        $sel = (!empty($return['whom_to_meet']) && $return['whom_to_meet'] == $e['id']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>

                                        <!-- 3. Entry Type -->
                                        <div class="form-group col-md-4">
                                            <label><i class="fa fa-sign-in-alt text-muted me-1"></i> Entry Type <b
                                                    class="text-danger">*</b></label>
                                            <div class="d-flex align-items-center gap-1">
                                                <select class="form-select js-example-basic-single flex-grow-1"
                                                    name="vendor_supplier_entry_type" id="vendor_supplier_entry_type">
                                                    <?php 
                                                    $vendor_supplier_entry_type_res = $result['vendor_supplier_entry_type_res'] ?? [];
                                                    if (count((array) $vendor_supplier_entry_type_res) > 0) {
                                                        foreach ($vendor_supplier_entry_type_res as $value) {
                                                            $sel = ((string)($return['vendor_supplier_entry_type'] ?? '') === (string)$value['genc_detail_code']) ? 'selected' : '';
                                                            $icon = !empty($value['option_icon']) ? htmlspecialchars($value['option_icon']) : '';
                                                            echo '<option value="' . htmlspecialchars($value['genc_detail_code']) . '" data-icon="' . $icon . '" ' . $sel . '>' . htmlspecialchars($value['genc_detail_name']) . '</option>';
                                                        }
                                                    } else {
                                                        ?>
                                                        <option value="IM" data-icon="ti-exchange-vertical" <?php echo ($return['vendor_supplier_entry_type'] ?? '') === 'IM' ? 'selected' : ''; ?>>Inward Material</option>
                                                        <option value="OM" data-icon="ti-exchange-vertical" <?php echo ($return['vendor_supplier_entry_type'] ?? '') === 'OM' ? 'selected' : ''; ?>>Outward Material</option>
                                                        <option value="RT" data-icon="ti-reload" <?php echo ($return['vendor_supplier_entry_type'] ?? '') === 'RT' ? 'selected' : ''; ?>>Returnable</option>
                                                        <option value="NR" data-icon="ti-reload" <?php echo ($return['vendor_supplier_entry_type'] ?? '') === 'NR' ? 'selected' : ''; ?>>Non Returnable</option>
                                                        <option value="EV" data-icon="ti-car" <?php echo ($return['vendor_supplier_entry_type'] ?? '') === 'EV' ? 'selected' : ''; ?>>Empty Vehicle</option>
                                                        <option value="MP" data-icon="ti-package" <?php echo ($return['vendor_supplier_entry_type'] ?? '') === 'MP' ? 'selected' : ''; ?>>Material Pickup</option>
                                                        <option value="MD" data-icon="ti-package" <?php echo ($return['vendor_supplier_entry_type'] ?? '') === 'MD' ? 'selected' : ''; ?>>Material Delivery</option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <button type="button" class="btn btn-outline-primary btn-sm add-option-trigger" data-type="vendor_supplier_entry_type" data-select-id="vendor_supplier_entry_type" data-label="Entry Type"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>

                                        <!-- 4. Reference Type -->
                                        <div class="form-group col-md-4">
                                            <label><i class="fa fa-file-invoice text-muted me-1"></i> Reference
                                                Type</label>
                                            <select class="form-select js-example-basic-single"
                                                name="vendor_supplier_document_type" id="vendor_supplier_document_type"
                                                onchange="getReferenceTransactions(); toggleVsDocNoWrap();">
                                                <option value="NOREF" <?php echo ($return['vendor_supplier_document_type'] ?? 'NOREF') === 'NOREF' ? 'selected' : ''; ?>>No Reference</option>
                                                <option value="purchase_orders" <?php echo ($return['vendor_supplier_document_type'] ?? '') === 'purchase_orders' ? 'selected' : ''; ?>>Purchase Order</option>
                                                <option value="returnable_gate_passes" <?php echo ($return['vendor_supplier_document_type'] ?? '') === 'returnable_gate_passes' ? 'selected' : ''; ?>>RGP</option>
                                                <option value="non_returnable_gate_passes" <?php echo ($return['vendor_supplier_document_type'] ?? '') === 'non_returnable_gate_passes' ? 'selected' : ''; ?>>NRGP
                                                </option>
                                                <option value="purchase_invoices" <?php echo ($return['vendor_supplier_document_type'] ?? '') === 'purchase_invoices' ? 'selected' : ''; ?>>Purchase Invoice
                                                </option>
                                            </select>
                                        </div>

                                        <!-- 5. Document No -->
                                        <div class="form-group col-md-4" id="vs_document_no_wrap" <?php echo (($return['vendor_supplier_document_type'] ?? 'NOREF') === 'NOREF') ? 'style="display:none"' : ''; ?>>
                                            <label><i class="fa fa-hashtag text-muted me-1"></i> Document No. <b
                                                    class="text-danger">*</b></label>
                                            <select class="form-select js-example-basic-single"
                                                id="vendor_supplier_transaction_id"
                                                name="vendor_supplier_transaction_id"
                                                onchange="goToAnotherPageAndPrint()">
                                                <option value="">Select Any One</option>
                                                <?php if (!empty($return['vendor_supplier_transaction_id'])): ?>
                                                    <option
                                                        value="<?php echo htmlspecialchars($return['vendor_supplier_transaction_id']); ?>"
                                                        selected>
                                                        <?php echo htmlspecialchars($return['vendor_supplier_transaction_id']); ?>
                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                            <small class="text-muted"><i class="fa fa-info-circle me-1"></i>Items
                                                auto-populate from selected document</small>
                                        </div>

                                        <!-- 6. Vendor/Supplier -->
                                        <div class="form-group col-md-4">
                                            <label><i class="fa fa-handshake text-muted me-1"></i> Vendor/Supplier <b
                                                    class="text-danger">*</b></label>
                                            <select class="form-select js-example-basic-single"
                                                id="vendor_supplier_vendor_id" name="vendor_supplier_vendor_id">
                                                <option value=""> Select Any one </option>
                                                <?php foreach ($vendors_res as $v): ?>
                                                    <option value="<?php echo $v['id']; ?>" <?php echo ($return['vendor_supplier_vendor_id'] ?? '') == $v['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($v['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- 7. Gate Pass Number (only shown in editMode) -->
                                        <div class="form-group col-md-4 <?php echo $editMode ? '' : 'd-none'; ?>"
                                            id="gatePassNumberWrap">
                                            <label><i class="fa fa-ticket-alt text-muted me-1"></i> Gate Pass
                                                Number</label>
                                            <input class="form-control" type="text"
                                                name="vendor_supplier_transaction_no"
                                                id="vendor_supplier_transaction_no"
                                                value="<?php echo htmlspecialchars($return['vendor_supplier_transaction_no'] ?? ''); ?>"
                                                readonly placeholder="Auto-generated on save">
                                            <small class="text-muted">Will be generated automatically on save</small>
                                        </div>

                                        <!-- Items table (always visible in edit mode) -->
                                        <div class="custom-scroll form-group col-md-12"
                                            style="max-height: 300px; overflow-y: auto;" id="printviewdocuments">
                                            <?php if ($editMode && $type === 'vendor_supplier' && !empty($visitor_luggages_res)): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-hover" id="invoiceItem">
                                                        <tbody>
                                                            <tr>
                                                                <th width="35%" class="p-1">Items/Service</th>
                                                                <th class="thheader p-1" width="30%">Description</th>
                                                                <?php if (($return['vendor_supplier_document_type'] ?? '') !== 'NOREF'): ?>
                                                                    <th class="thheader p-1 text-right" width="10%">QTY</th>
                                                                <?php endif; ?>
                                                                <th class="thheader p-1 text-right" width="15%">Received Qty
                                                                    <span class="requried">*</span></th>
                                                                <th class="thheader p-1" width="5%">UOM <span
                                                                        class="requried">*</span></th>
                                                            </tr>
                                                            <?php foreach ($visitor_luggages_res as $item): ?>
                                                                <tr>
                                                                    <td class="p-1 w-full">
                                                                        <select class="form-select js-example-basic-single"
                                                                            name="item_id[]" id="item_id[]">
                                                                            <option
                                                                                value="<?php echo htmlspecialchars($item['item_key']); ?>"
                                                                                selected>
                                                                                <?php echo htmlspecialchars($item['item_name']); ?>
                                                                            </option>
                                                                        </select>
                                                                        <input type="hidden" name="item_name[]"
                                                                            value="<?php echo htmlspecialchars($item['item_name']); ?>">
                                                                    </td>
                                                                    <td class="p-1">
                                                                        <input type="text" class="form-control"
                                                                            name="description[]"
                                                                            value="<?php echo htmlspecialchars($item['serial_or_remarks']); ?>">
                                                                    </td>
                                                                    <?php if (($return['vendor_supplier_document_type'] ?? '') !== 'NOREF'): ?>
                                                                        <td class="p-1">
                                                                            <input type="text" class="form-control"
                                                                                name="item_quantity[]"
                                                                                value="<?php echo htmlspecialchars($item['qty']); ?>"
                                                                                readonly>
                                                                        </td>
                                                                    <?php endif; ?>
                                                                    <td class="p-1">
                                                                        <input type="text" class="form-control"
                                                                            name="received_qty[]"
                                                                            value="<?php echo htmlspecialchars($item['qty']); ?>">
                                                                    </td>
                                                                    <td class="p-1">
                                                                        <input type="hidden" name="purchase_uom[]"
                                                                            value="<?php echo htmlspecialchars($item['brand_name']); ?>">
                                                                        <input type="text" class="form-control"
                                                                            value="<?php echo htmlspecialchars($item['brand_name']); ?>"
                                                                            readonly>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- ── Driver Information ─── -->
                                        <div class="col-md-12 mt-2 mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <hr class="flex-grow-1 my-0">
                                                <span class="text-muted small fw-semibold px-2"><i
                                                        class="fa fa-id-card me-1"></i>Driver Information</span>
                                                <hr class="flex-grow-1 my-0">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-user text-muted me-1"></i> Driver Name</label>
                                            <input class="form-control" type="text" name="vendor_supplier_driver_name"
                                                id="vendor_supplier_driver_name"
                                                value="<?php echo htmlspecialchars($return['vendor_supplier_driver_name'] ?? ''); ?>"
                                                placeholder="Full name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-phone text-muted me-1"></i> Driver Mobile</label>
                                            <input class="form-control" type="text" name="vendor_supplier_driver_mobile"
                                                id="vendor_supplier_driver_mobile"
                                                value="<?php echo htmlspecialchars($return['vendor_supplier_driver_mobile'] ?? ''); ?>"
                                                placeholder="Mobile number">
                                        </div>

                                        <!-- ── Vehicle Details ─── -->
                                        <div class="col-md-12 mt-2 mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <hr class="flex-grow-1 my-0">
                                                <span class="text-muted small fw-semibold px-2"><i
                                                        class="fa fa-truck me-1"></i>Vehicle Details</span>
                                                <hr class="flex-grow-1 my-0">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-truck text-muted me-1"></i> Vehicle Type</label>
                                            <select class="form-select js-example-basic-single"
                                                name="vendor_supplier_vehicle_type" id="vendor_supplier_vehicle_type">
                                                <option value="">None / Walk-in</option>
                                                <option value="TRK" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'TRK' ? 'selected' : ''; ?>>Truck</option>
                                                <option value="TMP" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'TMP' ? 'selected' : ''; ?>>Tempo</option>
                                                <option value="CNT" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'CNT' ? 'selected' : ''; ?>>Container</option>
                                                <option value="VAN" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'VAN' ? 'selected' : ''; ?>>Van</option>
                                                <option value="CAR" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'CAR' ? 'selected' : ''; ?>>Car</option>
                                                <option value="TRL" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'TRL' ? 'selected' : ''; ?>>Trailer</option>
                                                <option value="PUV" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'PUV' ? 'selected' : ''; ?>>Pickup Van</option>
                                                <option value="TNK" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'TNK' ? 'selected' : ''; ?>>Tanker</option>
                                                <option value="DMP" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'DMP' ? 'selected' : ''; ?>>Dumper</option>
                                                <option value="TWT" <?php echo ($return['vendor_supplier_vehicle_type'] ?? '') === 'TWT' ? 'selected' : ''; ?>>Tractor with Trolley</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label><i class="fa fa-sort-numeric-up text-muted me-1"></i> Vehicle
                                                Number</label>
                                            <input class="form-control" type="text"
                                                name="vendor_supplier_vehicle_number"
                                                id="vendor_supplier_vehicle_number"
                                                value="<?php echo htmlspecialchars($return['vendor_supplier_vehicle_number'] ?? ''); ?>"
                                                placeholder="e.g. MH 12 AB 1234">
                                        </div>

                                        <!-- ── Optional Details Toggle ─────────────────── -->
                                        <?php
                                        $vs_has_optional = !empty($return['vendor_supplier_purpose']) ||
                                            !empty($return['vendor_supplier_proof_type']) ||
                                            !empty($return['vendor_supplier_document_file']);
                                        ?>
                                        <div class="col-md-12 toggle-btn-wrapper my-2">
                                            <button type="button"
                                                class="btn btn-vms-secondary btn-sm w-100 py-2 toggle-additional-btn">
                                                <i class="fa fa-plus-circle me-1"></i> Add Optional Details (ID Proof,
                                                Documents, Purpose of Visit...)
                                            </button>
                                        </div>

                                        <div
                                            class="additional-fields-section <?php echo $vs_has_optional ? '' : 'd-none'; ?> row w-100 m-0 p-0">

                                            <!-- ── Document File ───── -->
                                            <div class="col-md-12 mt-1 mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <hr class="flex-grow-1 my-0">
                                                    <span class="text-muted small fw-semibold px-2"><i
                                                            class="fa fa-paperclip me-1"></i>Reference Document
                                                        File</span>
                                                    <hr class="flex-grow-1 my-0">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-file text-muted me-1"></i> Document File</label>
                                                <input class="form-control" type="file"
                                                    name="vendor_supplier_document_file">
                                                <?php if (!empty($return['vendor_supplier_document_file'])): ?>
                                                    <input type="hidden" name="existing_vendor_supplier_document_file"
                                                        value="<?php echo htmlspecialchars($return['vendor_supplier_document_file']); ?>">
                                                    <div class="small mt-1"><a
                                                            href="uploads/visitors/<?php echo htmlspecialchars($return['vendor_supplier_document_file']); ?>"
                                                            target="_blank"><i class="fa fa-eye me-1"></i>View Document</a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fa fa-comment-dots text-muted me-1"></i> Purpose of
                                                    Visit</label>
                                                <textarea class="form-control" rows="2" id="vendor_supplier_purpose"
                                                    name="vendor_supplier_purpose"
                                                    placeholder="Describe purpose of visit"><?php echo htmlspecialchars($return['vendor_supplier_purpose'] ?? ''); ?></textarea>
                                            </div>

                                            <!-- ── ID Proof ─── -->
                                            <div class="col-md-12 mt-2 mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <hr class="flex-grow-1 my-0">
                                                    <span class="text-muted small fw-semibold px-2"><i
                                                            class="fa fa-id-badge me-1"></i>ID Proof</span>
                                                    <hr class="flex-grow-1 my-0">
                                                </div>
                                            </div>
                                            <?php
                                            $proof_types_arr = explode(',', $return['vendor_supplier_proof_type'] ?? '');
                                            $proof_nos_arr = explode(',', $return['vendor_supplier_proof_document_no'] ?? '');
                                            $proof_files_arr = explode(',', $return['vendor_supplier_proof_document_file'] ?? '');
                                            $ptype = $proof_types_arr[0] ?? '';
                                            $pno = $proof_nos_arr[0] ?? '';
                                            $pfile = $proof_files_arr[0] ?? '';
                                            ?>
                                            <div class="form-group col-md-4">
                                                <label><i class="fa fa-id-card text-muted me-1"></i> Proof Type</label>
                                                <select class="form-select js-example-basic-single"
                                                    name="vendor_supplier_proof_type[]">
                                                    <option value="">Select Proof Type</option>
                                                    <option value="AAD" <?php echo $ptype === 'AAD' ? 'selected' : ''; ?>>
                                                        Aadhaar</option>
                                                    <option value="DL" <?php echo $ptype === 'DL' ? 'selected' : ''; ?>>
                                                        License</option>
                                                    <option value="PAN" <?php echo $ptype === 'PAN' ? 'selected' : ''; ?>>
                                                        PAN</option>
                                                    <option value="VID" <?php echo $ptype === 'VID' ? 'selected' : ''; ?>>
                                                        Voter ID</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label><i class="fa fa-hashtag text-muted me-1"></i> ID Proof
                                                    Number</label>
                                                <input class="form-control" type="text"
                                                    name="vendor_supplier_proof_document_no[]"
                                                    value="<?php echo htmlspecialchars($pno); ?>"
                                                    placeholder="Enter ID Proof number">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label><i class="fa fa-paperclip text-muted me-1"></i> Proof
                                                    Document</label>
                                                <input class="form-control" type="file"
                                                    name="vendor_supplier_proof_document_file[]">
                                                <?php if ($pfile): ?>
                                                    <input type="hidden"
                                                        name="existing_vendor_supplier_proof_document_file[]"
                                                        value="<?php echo htmlspecialchars($pfile); ?>">
                                                    <div class="small mt-1"><a
                                                            href="uploads/visitors/<?php echo htmlspecialchars($pfile); ?>"
                                                            target="_blank"><i class="fa fa-eye me-1"></i>View Proof</a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- ── Vehicle Stay & Loading Tracking ─── -->
                                            <div class="col-md-12 mt-2 mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <hr class="flex-grow-1 my-0">
                                                    <span class="text-muted small fw-semibold px-2"><i
                                                            class="fa fa-clock me-1"></i>Vehicle Stay &amp;
                                                        Loading</span>
                                                    <hr class="flex-grow-1 my-0">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Vehicle Stay Status</label>
                                                <select class="form-select js-example-basic-single"
                                                    name="vendor_supplier_vehicle_status"
                                                    id="vendor_supplier_vehicle_status">
                                                    <option value="Waiting" <?php echo ($return['vendor_supplier_vehicle_status'] ?? '') === 'Waiting' ? 'selected' : ''; ?>>Waiting</option>
                                                    <option value="Loading" <?php echo ($return['vendor_supplier_vehicle_status'] ?? '') === 'Loading' ? 'selected' : ''; ?>>Loading</option>
                                                    <option value="Unloading" <?php echo ($return['vendor_supplier_vehicle_status'] ?? '') === 'Unloading' ? 'selected' : ''; ?>>Unloading</option>
                                                    <option value="Completed" <?php echo ($return['vendor_supplier_vehicle_status'] ?? '') === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Loading/Unloading Start Time</label>
                                                <input class="form-control" type="datetime-local"
                                                    name="vendor_supplier_loading_start"
                                                    id="vendor_supplier_loading_start"
                                                    value="<?php echo !empty($return['vendor_supplier_loading_start']) ? str_replace(' ', 'T', $return['vendor_supplier_loading_start']) : ''; ?>">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Loading/Unloading End Time</label>
                                                <input class="form-control" type="datetime-local"
                                                    name="vendor_supplier_loading_end" id="vendor_supplier_loading_end"
                                                    value="<?php echo !empty($return['vendor_supplier_loading_end']) ? str_replace(' ', 'T', $return['vendor_supplier_loading_end']) : ''; ?>">
                                            </div>

                                        </div><!-- /additional-fields-section -->
                                    </div><!-- /vendor_supplierDiv -->
                                </div> <!-- Close standardFormLayout -->
                            </div>

                            <!-- Luggage & Carrying Items -->
                            <div class="vms-card">
                                <div class="vms-section-title">Carrying Luggage / Materials</div>
                                <div class="form-group">
                                    <label class="me-4"><i class="fa fa-suitcase text-muted me-1"></i> Is carrying any
                                        items / tools?</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="has_luggage" id="lug_no"
                                            value="no" checked>
                                        <label class="form-check-label" for="lug_no">No</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="has_luggage" id="lug_yes"
                                            value="yes">
                                        <label class="form-check-label" for="lug_yes">Yes, specify items</label>
                                    </div>
                                </div>

                                <div id="luggageSection" class="d-none border-top pt-3">
                                    <p class="text-muted small">Select an item pill to specify brand, serial number, and
                                        returnable status:</p>
                                    <div id="luggageScroller" class="luggage-scroller"></div>

                                    <!-- Selected items list -->
                                    <div class="mt-4">
                                        <h6 class="font-weight-bold mb-3 text-dark"><i
                                                class="fa fa-boxes text-secondary"></i> Items Logged</h6>
                                        <div id="luggageList" class="luggage-list"
                                            data-has-prefill="<?php echo ($editMode && !empty($visitor_luggages_res)) ? '1' : '0'; ?>">
                                            <?php if ($editMode && !empty($visitor_luggages_res)): ?>
                                                <?php foreach ($visitor_luggages_res as $L):
                                                    $item_key = htmlspecialchars($L['item_key'] ?? '');
                                                    $item_label = htmlspecialchars($L['item_name'] ?? 'Item');
                                                    $brand = htmlspecialchars($L['brand_name'] ?? '');
                                                    $qty = htmlspecialchars($L['qty'] ?? '1');
                                                    $ser = htmlspecialchars($L['description'] ?? '');
                                                    $retDate = trim($L['return_date'] ?? '');
                                                    $returnable = ($retDate && $retDate !== '0000-00-00') ? 'yes' : 'no';
                                                    $rowId = 'lug_row_' . $L['id'];
                                                    ?>
                                                    <div class="lug-item" id="<?php echo $rowId; ?>"
                                                        data-item-key="<?php echo $item_key; ?>">
                                                        <div class="lug-meta">
                                                            <strong><?php echo $item_label; ?></strong>
                                                            <div>Brand: <?php echo $brand ?: '-'; ?></div>
                                                            <div>Qty: <?php echo $qty; ?></div>
                                                            <div>Serial/Remarks: <?php echo $ser ?: '-'; ?></div>
                                                            <div>Returnable:
                                                                <?php echo $returnable; ?>
                                                                <?php echo ($returnable === 'yes' && $retDate) ? ' — ' . htmlspecialchars($retDate) : ''; ?>
                                                            </div>
                                                        </div>
                                                        <div class="lug-actions">
                                                            <button type="button" class="js-remove-luggage"
                                                                data-name="removeLuggageRow" data-row-id="<?php echo $rowId; ?>"
                                                                data-item-key="<?php echo $item_key; ?>">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        <div class="d-none">
                                                            <input type="hidden" name="luggage[<?php echo $L['id']; ?>][id]"
                                                                value="<?php echo (int) $L['id']; ?>">
                                                            <input type="hidden"
                                                                name="luggage[<?php echo $L['id']; ?>][item_key]"
                                                                value="<?php echo $item_key; ?>">
                                                            <input type="hidden"
                                                                name="luggage[<?php echo $L['id']; ?>][item_name]"
                                                                value="<?php echo $item_label; ?>">
                                                            <input type="hidden"
                                                                name="luggage[<?php echo $L['id']; ?>][brand_name]"
                                                                value="<?php echo $brand; ?>">
                                                            <input type="hidden" name="luggage[<?php echo $L['id']; ?>][qty]"
                                                                value="<?php echo $qty; ?>">
                                                            <input type="hidden"
                                                                name="luggage[<?php echo $L['id']; ?>][serial_or_remarks]"
                                                                value="<?php echo $ser; ?>">
                                                            <input type="hidden"
                                                                name="luggage[<?php echo $L['id']; ?>][returnable]"
                                                                value="<?php echo $returnable; ?>">
                                                            <?php if ($returnable === 'yes'): ?>
                                                                <input type="hidden"
                                                                    name="luggage[<?php echo $L['id']; ?>][return_date]"
                                                                    value="<?php echo htmlspecialchars($retDate); ?>">
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal: Luggage Details Input -->
                            <div class="modal fade" id="luggageModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header py-3 border-bottom">
                                            <h6 class="modal-title font-weight-bold">Item Specification</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" id="lm_item_key">
                                            <div class="mb-3">
                                                <label class="form-label">Item Category</label>
                                                <input type="text" class="form-control" id="lm_item_label" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Brand Name</label>
                                                <select class="form-select" id="lm_brand_name">
                                                    <option value="">Select brand</option>
                                                    <?php if (!empty($luggage_brands)): ?>
                                                        <?php foreach ($luggage_brands as $brand): ?>
                                                            <?php 
                                                                $val = is_object($brand) ? $brand->genc_detail_name : $brand['genc_detail_name'];
                                                                $code = is_object($brand) ? $brand->genc_detail_code : $brand['genc_detail_code'];
                                                                $icon = is_object($brand) ? ($brand->option_icon ?? '') : ($brand['option_icon'] ?? '');
                                                            ?>
                                                            <option value="<?php echo htmlspecialchars($code); ?>" data-icon="<?php echo htmlspecialchars($icon); ?>"><?php echo htmlspecialchars($val); ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                    <option value="Other">Other</option>
                                                </select>
                                                <div class="input-group mt-2">
                                                    <input type="text" id="lm_brand_name_other" class="form-control d-none" placeholder="Specify brand name">
                                                    <button type="button" class="btn btn-primary d-none btn-add-inline-other" id="btn_add_brand_other" data-type="luggage_brand" data-input-id="lm_brand_name_other" data-select-id="lm_brand_name"><i class="fa fa-plus"></i> Add</button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" min="1" class="form-control" id="lm_qty" value="1">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Serial No / Remarks</label>
                                                <input type="text" class="form-control" id="lm_serial_or_remarks"
                                                    placeholder="Enter serial or description">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label d-block">Returnable Item?</label>
                                                <div class="form-check form-check-inline mt-1">
                                                    <input class="form-check-input" type="radio" name="lm_returnable"
                                                        id="lm_returnable_yes" value="yes" checked>
                                                    <label class="form-check-label" for="lm_returnable_yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline mt-1">
                                                    <input class="form-check-input" type="radio" name="lm_returnable"
                                                        id="lm_returnable_no" value="no">
                                                    <label class="form-check-label" for="lm_returnable_no">No</label>
                                                </div>
                                            </div>
                                            <div class="mb-3" id="lm_return_date_wrap">
                                                <label class="form-label">Return Date</label>
                                                <input type="date" class="form-control" id="lm_return_date"
                                                    value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="modal-footer py-2 border-top">
                                            <button type="button" class="btn btn-vms-secondary btn-sm"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-vms-primary btn-sm"
                                                id="lm_save_btn">Add Item</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: Verification & Materials -->
                        <div class="vms-step-pane d-none" id="step-pane-3">
                            <!-- Photo Card -->
                            <div class="vms-card">
                                <div class="vms-section-title">Visitor Photo</div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="mb-2"><i class="fa fa-camera text-muted me-1"></i> Capture /
                                            Upload Photo</label>

                                        <!-- Hidden inputs -->
                                        <input type="file" name="image_path" id="image_path" accept="image/*"
                                            class="d-none">
                                        <input type="hidden" name="webcam_image" id="webcam_image" value="">

                                        <!-- Option A: Action Selection Cards (Visible when no photo is selected/captured) -->
                                        <div
                                            class="photo-actions-container <?php echo !empty($return['image_path']) ? 'd-none' : 'd-flex'; ?>">
                                            <div class="photo-action-card" id="btnUploadTrigger">
                                                <div class="photo-action-icon upload-icon">
                                                    <i class="fa fa-cloud-upload-alt"></i>
                                                </div>
                                                <div class="photo-action-title">Upload Photo</div>
                                                <div class="photo-action-desc">Select an image file from your device
                                                </div>
                                            </div>
                                            <div class="photo-action-card" id="btnSnapPhoto">
                                                <div class="photo-action-icon camera-icon">
                                                    <i class="fa fa-camera"></i>
                                                </div>
                                                <div class="photo-action-title">Capture Photo</div>
                                                <div class="photo-action-desc">Snap a photo using your webcam</div>
                                            </div>
                                        </div>

                                        <!-- Option B: Preview Frame (Visible when photo is selected/captured) -->
                                        <div
                                            class="photo-preview-container <?php echo !empty($return['image_path']) ? 'd-flex' : 'd-none'; ?>">
                                            <div class="photo-preview-frame">
                                                <img id="personPreviewInline"
                                                    src="<?php echo !empty($return['image_path']) ? 'uploads/visitors/' . htmlspecialchars($return['image_path']) : ''; ?>"
                                                    alt="Preview"
                                                    class="<?php echo empty($return['image_path']) ? 'd-none' : ''; ?>">
                                            </div>
                                            <div class="photo-preview-meta">
                                                <span class="photo-status-badge"><i class="fa fa-check-circle"></i>
                                                    Photo Selected</span>
                                                <div class="photo-meta-buttons">
                                                    <button type="button" class="btn-vms-secondary btn-sm"
                                                        id="btnChangePhoto">
                                                        <i class="fa fa-sync"></i> Change
                                                    </button>
                                                    <button type="button" class="btn-vms-danger btn-sm"
                                                        id="btnRemovePhoto">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Historical records for the visitor based on phone search -->
                            <div id="visitorHistoryWrap" class="mt-4 d-none border-top pt-4">
                                <h6 class="font-weight-bold mb-3 text-dark"><i class="fa fa-history text-secondary"></i>
                                    Visitor Check-in History</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-sm align-middle"
                                        id="visitorHistoryTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Visitor Details</th>
                                                <th>Phone</th>
                                                <th>Check-in DateTime</th>
                                                <th>Check-out DateTime</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <!-- Navigation buttons -->
                        <div class="vms-step-actions mt-4 pt-3 border-top d-flex justify-content-between">
                            <div>
                                <button type="button" id="prevBtn" class="btn-vms btn-vms-secondary d-none">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="index.php?option=COM_LIST_VISITOR_LOGS" class="btn-vms btn-vms-secondary"
                                    id="cancelBtn">
                                    Cancel
                                </a>
                                <button type="button" id="nextBtn" class="btn-vms btn-vms-primary">
                                    Next <i class="fa fa-arrow-right"></i>
                                </button>
                                <?php if ($rid != ""): ?>
                                    <button type="submit" name="submit" value="update" id="submitBtn"
                                        class="btn-vms btn-vms-primary d-none">
                                        <i class="fa fa-save"></i> Update Log
                                    </button>
                                    <input name="rid" type="hidden" value="<?php echo $rid; ?>">
                                    <input type="hidden" name="existing_image"
                                        value="<?php echo $return['image_path'] ?? ''; ?>">
                                <?php else: ?>
                                    <button type="submit" name="submit" value="insert" id="submitBtn"
                                        class="btn-vms btn-vms-primary d-none">
                                        <i class="fa fa-check-circle"></i> Complete Check-in
                                    </button>
                                    <input type="hidden" name="existing_image" value="">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Sticky Live Badge Preview -->
                    <div class="col-lg-4">
                        <div class="sticky-badge-container" id="visitorCardWrap">
                            <div class="preview-badge-card">
                                <div class="badge-card-header">
                                    <div class="badge-company">
                                        <?= htmlspecialchars($_SESSION['company_name'] ?? 'INFISUITE') ?>
                                    </div>
                                    <?php
                                    $titleText = 'VISITOR PASS';
                                    if ($type === 'delivery')
                                        $titleText = 'DELIVERY PASS';
                                    elseif ($type === 'service')
                                        $titleText = 'SERVICE PASS';
                                    elseif ($type === 'employee')
                                        $titleText = 'EMPLOYEE PASS';
                                    elseif ($type === 'doctor')
                                        $titleText = 'DOCTOR PASS';
                                    elseif ($type === 'vendor_supplier')
                                        $titleText = 'VENDOR/SUPPLIER PASS';
                                    elseif ($type === 'others') {
                                        $specify = trim($return['visit_type_other'] ?? '');
                                        $titleText = $specify ? strtoupper($specify) . ' PASS' : 'VISITOR PASS';
                                    }
                                    ?>
                                    <div class="badge-title" id="badgePreviewTitle">
                                        <?php echo htmlspecialchars($titleText); ?>
                                    </div>
                                </div>
                                <div class="badge-card-body">
                                    <div class="badge-photo-area">
                                        <img id="badgePreviewPhoto"
                                            src="<?php echo !empty($return['image_path']) ? 'uploads/visitors/' . htmlspecialchars($return['image_path']) : ''; ?>"
                                            class="<?php echo empty($return['image_path']) ? 'd-none' : ''; ?>">
                                        <div
                                            class="badge-photo-placeholder <?php echo !empty($return['image_path']) ? 'd-none' : ''; ?>">
                                            <i class="fa fa-camera"></i>
                                            <span>PHOTO</span>
                                        </div>
                                    </div>
                                    <div class="badge-name" id="badgePreviewName">
                                        <?= htmlspecialchars($return['name'] ?? 'Visitor Name') ?>
                                    </div>
                                    <div class="badge-visit-type-pill" id="badgePreviewPill">
                                        <?= strtoupper($type ?: 'visitor') ?>
                                    </div>
                                    <div class="badge-info-grid" id="badgePreviewGrid">
                                        <!-- Will be dynamically populated by JS based on Visit Type -->
                                    </div>
                                </div>
                                <div class="badge-card-footer" style="justify-content: center; padding: 12px 24px;">
                                    <div class="badge-date"><?= date('d M Y') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!$editMode): ?>
            <!-- Check-out Tab Pane -->
            <div class="tab-pane fade" id="checkout-pane" role="tabpanel" aria-labelledby="checkout-tab">
                <div class="row" id="checkout-layout-row">
                    <!-- Right side: Search Panel and Results Table (initially full-width) -->
                    <div class="col-lg-12" id="checkout-list-col">
                        <!-- Search Panel -->
                        <div class="out-card">
                            <div class="out-section-title">Search In-Premises Visitors</div>
                            <div class="card-body p-0">
                                <div class="search-grid">
                                    <div class="flex-grow-1">
                                        <label class="form-label font-weight-bold">Search Term</label>
                                        <input type="text" class="form-control" id="checkoutSearchText"
                                            placeholder="e.g. Name, Phone, Visitor Pass ID (VST-xxxx), ID Card No.">
                                    </div>
                                    <div>
                                        <label class="form-label font-weight-bold">Visit Type</label>
                                        <select class="form-select js-example-basic-single" id="checkoutSearchType"
                                            style="height: 42px;">
                                            <option value="">All Types</option>
                                            <option value="visitor">Visitor</option>
                                            <option value="delivery">Delivery</option>
                                            <option value="service">Service</option>
                                            <option value="employee">Employee</option>
                                            <option value="doctor">Doctor</option>
                                            <option value="vendor_supplier">Vendor/Supplier</option>
                                            <option value="others">Others</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label font-weight-bold">Check-in Date</label>
                                        <input type="date" class="form-control" id="checkoutSearchDate"
                                            value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <input type="hidden" name="location_id" id="location_id_checkout"
                                        value="<?php echo $_SESSION['location_id'] ?? 1; ?>">
                                    <div class="d-flex align-items-end">
                                        <button type="button" class="btn-vms btn-vms-primary" id="checkoutSearchBtn"
                                            style="height: 42px;">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    Tip: Enter a search term or select a date. Click Search to view list of visitors matching
                                    criteria.
                                </small>
                            </div>
                        </div>

                        <!-- Results Table (hidden by default) -->
                        <div id="checkoutHistoryWrap" class="visitor-history-wrap out-card d-none">
                            <div class="out-section-title">In-Premises Visitors Matching Search</div>
                            <div class="table-responsive visitor-history-table-wrap">
                                <table class="table table-hover align-middle mb-0" id="checkoutHistoryTable">
                                    <thead>
                                        <tr>
                                            <th class="th-visitor">Visitor Details</th>
                                            <th class="th-idcard">Pass ID / Code</th>
                                            <th class="th-phone">Contact Phone</th>
                                            <th class="th-visittype">Visit Type</th>
                                            <th class="th-checkin">Check-in Stamp</th>
                                            <th class="th-checkout">Check-out Stamp</th>
                                            <th class="th-action text-center">Checkout Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="no-records-row">
                                            <td colspan="7" class="py-5 text-center">
                                                <div class="text-center">
                                                    <p class="text-muted f-16 fw-bold mb-3">
                                                        <i class="fa fa-info-circle"></i> No Records Found
                                                    </p>
                                                    <div class="text-center px-4">
                                                        <img class="visitor-empty-img" alt="Empty list" src="assets/images/work.png"
                                                            onerror="this.style.display='none'">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Left side: Check-out Form Column (initially hidden) -->
                    <div class="col-lg-7 col-md-7 d-none" id="checkout-form-col">
                        <div id="checkoutResult"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div> <!-- Close tab-content -->
</div> <!-- Close vms-container -->
<?php
$printMode = isset($_GET['print_mode']) && $_GET['print_mode'] == '1';
$printRid = $_GET['print_rid'] ?? '';
if ($printMode && $printRid) {
    // Fetch the newly created record for print preview
    $printUrl = API . "?mode=getVisitorInsertRelatedData&method=get&rid=$printRid&company_id=" . $_SESSION['company_id'] . "&gate_id=$gate_id&location_id=" . $_SESSION['location_id'];
    $printResult = callAPI($printUrl);
    $printResult = json_decode($printResult, 1);
    $pr = $printResult['visitor_logs_res'] ?? [];
    $prType = strtolower($pr['visit_type'] ?? 'visitor');
    $prBadge = $printResult['visitor_badge_res']['full_code'] ?? ($pr['visiting_company_id'] ?? '');

    // Determine display values based on visit type
    $prName = '';
    $prPhone = '';
    $prHost = '—';
    $prCompany = '—';
    $prTitleText = 'VISITOR PASS';

    // Additional fields for the enhanced card layout
    $prPurpose = '—';
    $prVehicle = '—';
    $prDuration = '—';

    switch ($prType) {
        case 'visitor':
        case 'others':
            $prName = $pr['name'] ?? '';
            $prPhone = $pr['phone'] ?? '';
            $prHost = ''; // Will be resolved from employees if needed
            $prCompany = $pr['company_name'] ?? '—';

            $prPurpose = ucfirst($pr['purpose'] ?? '—');
            if (strtolower($prPurpose) === 'others' && !empty($pr['purpose_other'])) {
                $prPurpose = $pr['purpose_other'];
            }
            $prVehicle = $pr['vehicle_number'] ?? '—';
            if ($prVehicle !== '—' && $prVehicle !== '' && !empty($pr['vehicle_type'])) {
                $prVehicle .= ' (' . $pr['vehicle_type'] . ')';
            }
            $prDuration = $pr['expected_duration'] ?? '—';

            if ($prType === 'others') {
                $specify = trim($pr['visit_type_other'] ?? '');
                $prTitleText = $specify ? strtoupper($specify) . ' PASS' : 'VISITOR PASS';
            }
            break;
        case 'delivery':
            $prTitleText = 'DELIVERY PASS';
            $prName = $pr['delivery_company_person'] ?? '';
            $prPhone = $pr['delivery_phone'] ?? '';

            $prPurpose = 'Delivery';
            if (!empty($pr['delivery_type'])) {
                $prPurpose .= ' (' . $pr['delivery_type'] . ')';
            }
            if (!empty($pr['delivery_awb_no'])) {
                $prPurpose .= ' (AWB: ' . $pr['delivery_awb_no'] . ')';
            }
            $prVehicle = $pr['vehicle_number'] ?? '—';
            break;
        case 'service':
            $prTitleText = 'SERVICE PASS';
            $prName = $pr['service_person'] ?? '';
            $prPhone = $pr['service_phone'] ?? '';

            $prPurpose = $pr['service_classification'] ?? 'Service';
            if (!empty($pr['work_permit_no'])) {
                $prPurpose .= ' (Permit: ' . $pr['work_permit_no'] . ')';
            }
            break;
        case 'employee':
            $prTitleText = 'EMPLOYEE PASS';
            $prName = !empty($pr['emp_temp_enable']) ? ($pr['emp_temp_name'] ?? '') : ($pr['employee_name'] ?? '');
            $prPhone = $pr['emp_temp_phone'] ?? ($pr['phone'] ?? '');
            $prCompany = $pr['employee_department'] ?? '—';

            $prPurpose = 'Employee Check-in';
            if (!empty($pr['emp_temp_enable'])) {
                $prPurpose = 'Temp Pass';
                if (!empty($pr['emp_temp_reason'])) {
                    $reasons = ['forgot' => 'Forgot ID', 'new' => 'Onboarding', 'xbranch' => 'Cross Branch'];
                    $prPurpose .= ' (' . ($reasons[$pr['emp_temp_reason']] ?? $pr['emp_temp_reason']) . ')';
                }
            }
            $prVehicle = $pr['vehicle_number'] ?? '—';
            break;
        case 'doctor':
            $prTitleText = 'DOCTOR PASS';
            $prName = $pr['doctor_name'] ?? '';
            $prPhone = $pr['doctor_phone'] ?? '';
            $prCompany = $pr['doctor_specialization'] ?? '—';

            $prPurpose = $pr['doctor_specialization'] ?? 'Doctor Duty';
            if (!empty($pr['is_emergency'])) {
                $prPurpose .= ' (Emergency)';
            }
            break;
        case 'vendor_supplier':
            $prTitleText = 'VENDOR/SUPPLIER PASS';
            $prName = $pr['vendor_supplier_driver_name'] ?? '';
            $prPhone = $pr['vendor_supplier_driver_mobile'] ?? '';
            $prBadge = $pr['vendor_supplier_transaction_no'] ?? $prBadge;

            // Resolve Vendor Company Name
            $prVendorId = $pr['vendor_supplier_vendor_id'] ?? '';
            if ($prVendorId && !empty($vendors_res)) {
                foreach ($vendors_res as $v) {
                    if ($v['id'] == $prVendorId) {
                        $prCompany = $v['name'];
                        break;
                    }
                }
            }

            $prPurpose = 'Vendor/Supplier';
            if (!empty($pr['vendor_supplier_purpose'])) {
                $prPurpose = $pr['vendor_supplier_purpose'];
            } elseif (!empty($pr['vendor_supplier_entry_type'])) {
                $types = ['IM' => 'Inward Material', 'OM' => 'Outward Material', 'RT' => 'Returnable', 'NR' => 'Non Returnable', 'EV' => 'Empty Vehicle', 'MP' => 'Material Pickup', 'MD' => 'Material Delivery'];
                $prPurpose = $types[$pr['vendor_supplier_entry_type']] ?? $pr['vendor_supplier_entry_type'];
            }

            $prVehicle = $pr['vendor_supplier_vehicle_number'] ?? '—';
            if ($prVehicle !== '—' && $prVehicle !== '' && !empty($pr['vendor_supplier_vehicle_type'])) {
                $types = ['TRK' => 'Truck', 'TMP' => 'Tempo', 'CNT' => 'Container', 'VAN' => 'Van', 'CAR' => 'Car', 'TRL' => 'Trailer', 'PUV' => 'Pickup Van', 'TNK' => 'Tanker', 'DMP' => 'Dumper', 'TWT' => 'Tractor'];
                $prVehicle .= ' (' . ($types[$pr['vendor_supplier_vehicle_type']] ?? $pr['vendor_supplier_vehicle_type']) . ')';
            }

            // Resolve Department Name
            $prDeptId = $pr['vendor_supplier_department_id'] ?? '';
            if ($prDeptId && !empty($departments_res)) {
                foreach ($departments_res as $d) {
                    if ($d['id'] == $prDeptId) {
                        $prHost = $d['name'];
                        break;
                    }
                }
            }
            break;
    }

    // Get gate name
    $prGateName = '—';
    $prGateId = $pr['in_gate_no'] ?? '';
    if (!empty($building_gates_res)) {
        foreach ($building_gates_res as $g) {
            if ($g['id'] == $prGateId) {
                $prGateName = $g['name'];
                break;
            }
        }
    }

    // Get host name from employees
    $prHostName = '—';
    $prHostId = $pr['whom_to_meet'] ?? ($pr['deliver_to'] ?? ($pr['authorized_employee'] ?? ''));
    if (!empty($employees_res) && $prHostId) {
        foreach ($employees_res as $emp) {
            if ($emp['id'] == $prHostId) {
                $prHostName = $emp['display_name'];
                break;
            }
        }
    }
    if ($prType === 'employee' && empty($pr['emp_temp_enable']))
        $prHostName = 'Self Check-in';

    $prInTime = $pr['in_datetime'] ?? date('d M Y H:i');
    $prImage = $pr['image_path'] ?? '';

    // Type accent colors
    $accentColors = [
        'visitor' => ['gradient' => '#3b82f6, #1d4ed8', 'bg' => '#eff6ff', 'text' => '#1e3a8a', 'pill' => '#3b82f6'],
        'delivery' => ['gradient' => '#f59e0b, #b45309', 'bg' => '#fffbeb', 'text' => '#78350f', 'pill' => '#f59e0b'],
        'service' => ['gradient' => '#10b981, #047857', 'bg' => '#ecfdf5', 'text' => '#065f46', 'pill' => '#10b981'],
        'employee' => ['gradient' => '#8b5cf6, #5b21b6', 'bg' => '#f5f3ff', 'text' => '#5b21b6', 'pill' => '#8b5cf6'],
        'doctor' => ['gradient' => '#ef4444, #b91c1c', 'bg' => '#fef2f2', 'text' => '#991b1b', 'pill' => '#ef4444'],
        'vendor_supplier' => ['gradient' => '#f59e0b, #b45309', 'bg' => '#fffbeb', 'text' => '#78350f', 'pill' => '#f59e0b'],
        'others' => ['gradient' => '#64748b, #334155', 'bg' => '#f8fafc', 'text' => '#334155', 'pill' => '#64748b'],
    ];
    $accent = $accentColors[$prType] ?? $accentColors['visitor'];
    ?>

    <!-- Print Preview Overlay -->
    <div class="print-preview-overlay" id="printPreviewOverlay">
        <div class="print-preview-container">
            <!-- Success Header -->
            <div class="print-success-header">
                <div class="print-success-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <h4>Check-in Completed Successfully!</h4>
                <p class="text-muted">The visitor pass has been generated. You can print it and hand it to the visitor.</p>
            </div>

            <!-- Printable Badge Area -->
            <div class="print-badge-wrapper" id="printBadgeArea">
                <div class="print-badge-card">
                    <div class="print-badge-accent" style="background: linear-gradient(90deg, <?= $accent['gradient'] ?>);">
                    </div>
                    <div class="print-badge-header">
                        <div class="print-badge-company"><?= htmlspecialchars($_SESSION['company_name'] ?? 'INFISUITE') ?>
                        </div>
                        <div class="print-badge-title"><?= htmlspecialchars($prTitleText) ?></div>
                    </div>
                    <div class="print-badge-body">
                        <div class="print-badge-photo-area">
                            <?php if (!empty($prImage)): ?>
                                <img src="uploads/visitors/<?= htmlspecialchars($prImage) ?>" alt="Visitor Photo">
                            <?php else: ?>
                                <div class="print-badge-photo-placeholder">
                                    <i class="fa fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="print-badge-name"><?= htmlspecialchars($prName ?: 'Visitor') ?></div>
                        <div class="print-badge-type-pill" style="background-color: <?= $accent['pill'] ?>;">
                            <?= strtoupper($prType === 'vendor_supplier' ? 'Vendor/Supplier' : ($prType ?: 'Visitor')) ?>
                        </div>
                        <div class="print-badge-info-grid">
                            <div class="print-badge-info-item">
                                <span>Gate</span>
                                <strong><?= htmlspecialchars($prGateName) ?></strong>
                            </div>
                            <?php if ($prType === 'visitor' || $prType === 'others') { ?>
                                <div class="print-badge-info-item">
                                    <span>Host / Meet</span>
                                    <strong><?= htmlspecialchars($prHostName ?: '—') ?></strong>
                                </div>
                                <div class="print-badge-info-item">
                                    <span>Contact Phone</span>
                                    <strong><?= htmlspecialchars($prPhone ?: '—') ?></strong>
                                </div>
                                <div class="print-badge-info-item">
                                    <span>Purpose</span>
                                    <strong><?= htmlspecialchars($prPurpose ?: '—') ?></strong>
                                </div>
                            <?php } elseif ($prType === 'delivery') { ?>
                                <div class="print-badge-info-item">
                                    <span>Contact Phone</span>
                                    <strong><?= htmlspecialchars($prPhone ?: '—') ?></strong>
                                </div>
                                <div class="print-badge-info-item">
                                    <span>Whom To Deliver</span>
                                    <strong><?= htmlspecialchars($prHostName ?: '—') ?></strong>
                                </div>
                                <div class="print-badge-info-item">
                                    <span>Vehicle No</span>
                                    <strong><?= htmlspecialchars($prVehicle ?: '—') ?></strong>
                                </div>
                                <div class="print-badge-info-item">
                                    <span>Delivery Type</span>
                                    <strong><?= htmlspecialchars($pr['delivery_type'] ?: '—') ?></strong>
                                </div>
                            <?php } elseif ($prType === 'service') { ?>
                                <div class="print-badge-info-item">
                                    <span>Service Company</span>
                                    <strong><?= htmlspecialchars($service_company_map[$pr['service_company'] ?? ''] ?? ($pr['service_company'] ?? '—')) ?></strong>
                                </div>
                                <div class="print-badge-info-item">
                                    <span>Contact Phone</span>
                                    <strong><?= htmlspecialchars($prPhone ?: '—') ?></strong>
                                </div>
                                <div class="print-badge-info-item">
                                    <span>Authorized By</span>
                                    <strong><?= htmlspecialchars($prHostName ?: '—') ?></strong>
                                </div>
                            <?php } elseif ($prType === 'employee') { ?>
                                <?php if (!empty($pr['emp_temp_enable'])) { ?>
                                    <div class="print-badge-info-item">
                                        <span>Contact Phone</span>
                                        <strong><?= htmlspecialchars($prPhone ?: '—') ?></strong>
                                    </div>
                                    <div class="print-badge-info-item">
                                        <span>Host / Meet</span>
                                        <strong><?= htmlspecialchars($prHostName ?: '—') ?></strong>
                                    </div>
                                <?php } else { ?>
                                    <div class="print-badge-info-item">
                                        <span>Employee ID</span>
                                        <strong><?= htmlspecialchars($pr['employee_code'] ?: '—') ?></strong>
                                    </div>
                                    <div class="print-badge-info-item">
                                        <span>Department</span>
                                        <strong><?= htmlspecialchars($pr['employee_department'] ?: '—') ?></strong>
                                    </div>
                                <?php } ?>
                            <?php } elseif ($prType === 'doctor') { ?>
                                <div class="print-badge-info-item">
                                    <span>Contact Phone</span>
                                    <strong><?= htmlspecialchars($prPhone ?: '—') ?></strong>
                                </div>
                            <?php } elseif ($prType === 'vendor_supplier') { ?>
                                <div class="print-badge-info-item">
                                    <span>Vendor Company</span>
                                    <strong><?= htmlspecialchars($prCompany ?: '—') ?></strong>
                                </div>
                                <div class="print-badge-info-item">
                                    <span>Department</span>
                                    <strong><?= htmlspecialchars($prHost ?: '—') ?></strong>
                                </div>
                                <div class="print-badge-info-item">
                                    <span>Host / Meet</span>
                                    <strong><?= htmlspecialchars($prHostName ?: '—') ?></strong>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="print-badge-footer" style="justify-content: center; padding: 12px 24px;">
                        <div class="print-badge-date"><?= date('d M Y') ?></div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="print-action-buttons no-print">
                <button type="button" class="print-btn print-btn-primary" id="btnPrintBadge">
                    <i class="fa fa-print"></i> Print Visitor Pass
                </button>
                <a href="index.php?option=custom_insert_visitor_logs" class="print-btn print-btn-success">
                    <i class="fa fa-plus-circle"></i> Make New Entry
                </a>
                <a href="index.php?option=COM_LIST_VISITOR_LOGS" class="print-btn print-btn-secondary">
                    <i class="fa fa-list"></i> Go to Visitor Logs
                </a>
            </div>
        </div>
    </div>
<?php } ?>


<?php
$service_company_map = [];
if (!empty($service_company_res)) {
    foreach ($service_company_res as $val) {
        $code = is_array($val) ? ($val['genc_detail_code'] ?? '') : ($val->genc_detail_code ?? '');
        $name = is_array($val) ? ($val['genc_detail_name'] ?? '') : ($val->genc_detail_name ?? '');
        if ($code !== '')
            $service_company_map[$code] = $name;
    }
}
$delivery_company_map = [];
if (!empty($delivery_company_res)) {
    foreach ($delivery_company_res as $val) {
        $code = is_array($val) ? ($val['genc_detail_code'] ?? '') : ($val->genc_detail_code ?? '');
        $name = is_array($val) ? ($val['genc_detail_name'] ?? '') : ($val->genc_detail_name ?? '');
        if ($code !== '')
            $delivery_company_map[$code] = $name;
    }
}
?>
<script type="application/json" id="visitor-out-config">
<?php echo json_encode([
    'serviceCompanyRes' => $service_company_map,
    'deliveryCompanyRes' => $delivery_company_map
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<!-- WebCam Capture Modal Overlay -->
<div class="modal fade" id="webcamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-3 border-bottom">
                <h6 class="modal-title font-weight-bold"><i class="fa fa-camera"></i> Live WebCam Photo Capture</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center bg-dark p-0"
                style="position: relative; overflow: hidden; height: 320px;">
                <video id="webcamVideo" width="100%" height="100%" autoplay playsinline
                    style="object-fit: cover;"></video>
                <canvas id="webcamCanvas" width="640" height="480" class="d-none"></canvas>
            </div>
            <div class="modal-footer py-2 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-vms-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-vms-primary btn-sm" id="btnCaptureFrame"><i
                        class="fa fa-camera-retro"></i> Snap Picture</button>
            </div>
        </div>
    </div>
</div>

<!-- Host Approval Modal Overlay -->
<div class="modal fade" id="hostApprovalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="hostApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content"
            style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #1e293b, #334155); color: #ffffff; border-bottom: none; padding: 20px;">
                <h5 class="modal-title" id="hostApprovalModalLabel"
                    style="font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-user-shield"></i> Host Verification Pending
                </h5>
            </div>
            <div class="modal-body text-center" style="padding: 30px 30px 20px;">
                <div class="approval-spinner mb-3" style="margin-bottom: 20px;">
                    <div class="spinner-border text-primary" role="status"
                        style="width: 3rem; height: 3rem; border-width: 0.25em;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="approval-status-icon mb-3 d-none" style="margin-bottom: 20px; font-size: 56px;">
                    <i class="fa fa-check-circle text-success" id="statusSuccessIcon" style="display: none;"></i>
                    <i class="fa fa-times-circle text-danger" id="statusErrorIcon" style="display: none;"></i>
                </div>
                <h4 id="approvalStatusTitle" style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">Waiting for
                    Host Response</h4>
                <p id="approvalStatusDesc" style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 0;">
                    We have sent an entry authorization request email to the host. Please wait or confirm manually if
                    authorized verbally.
                </p>

                <!-- Host Info Card -->
                <div id="hostInfoCard" class="d-none mt-3"
                    style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 14px 18px; text-align: left;">
                    <div
                        style="font-size: 12px; font-weight: 700; color: #0369a1; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                        <i class="fa fa-user-tie me-1"></i> Authorization Sent To
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div
                            style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display:flex; align-items:center; justify-content:center; color:#fff; font-size:18px; flex-shrink:0;">
                            <i class="fa fa-user"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div id="hostDisplayName"
                                style="font-weight: 700; color: #1e293b; font-size: 15px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                —</div>
                            <div id="hostDisplayEmail"
                                style="font-size: 13px; color: #0369a1; margin-top: 2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <i class="fa fa-envelope me-1" style="opacity:0.7;"></i><span>—</span>
                            </div>
                            <div id="hostDisplayPhone" style="font-size: 13px; color: #475569; margin-top: 2px;">
                                <i class="fa fa-phone me-1" style="opacity:0.7;"></i><span>—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"
                style="background-color: #f8fafc; border-top: 1px solid #edf2f7; padding: 12px 20px; display: flex; justify-content: space-between; gap: 10px; width: 100%; flex-wrap: wrap;">
                <button type="button" class="btn btn-secondary" id="btnCancelApproval"
                    style="border-radius: 8px; font-weight: 600; padding: 9px 16px;">
                    Cancel
                </button>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn" id="btnResendEmail"
                        style="border-radius: 8px; font-weight: 600; padding: 9px 16px; background-color: #6366f1; color:#fff; border: none;">
                        <i class="fa fa-paper-plane me-1"></i> Resend Email
                    </button>
                    <button type="button" class="btn btn-warning" id="btnManualConfirm"
                        style="border-radius: 8px; font-weight: 600; color: #ffffff; padding: 9px 16px; background-color: #f59e0b; border: none;">
                        <i class="fa fa-phone-alt me-1"></i> Manual Confirm
                    </button>
                    <button type="button" class="btn btn-success d-none" id="btnGeneratePass"
                        style="border-radius: 8px; font-weight: 600; padding: 9px 16px; background-color: #10b981; border: none;">
                        <i class="fa fa-id-card me-1"></i> Generate ID Pass
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Option -->
<div class="modal fade" id="addOptionModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="modal-header py-3 border-bottom" style="background-color: #f8fafc; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h6 class="modal-title font-weight-bold" style="font-family: 'Outfit', sans-serif; color: #1e293b; font-size: 16px;"><i class="fa fa-plus text-primary me-1"></i> Add New <span id="modal_option_label_title">Option</span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <form id="addOptionForm">
                    <input type="hidden" id="modal_option_type" name="option_type">
                    <input type="hidden" id="modal_select_id" name="select_id">
                    <div class="form-group mb-0">
                        <label for="modal_option_value" class="form-label font-weight-bold" style="color: #475569; font-size: 14px;">Value/Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_option_value" name="option_value" placeholder="Enter option value" style="padding: 10px 14px; font-size: 14px; border-radius: 8px;" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2 border-top" style="background-color: #f8fafc; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="font-weight: 600; padding: 6px 12px; border-radius: 6px;">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn_save_modal_option" style="font-weight: 600; padding: 6px 12px; border-radius: 6px; background-color: #3b82f6; border: none;">Save Option</button>
            </div>
        </div>
    </div>
</div>
<!-- <script src="assets/js/custom_insert_visitor_logs.js?v="></script> -->
