<?php
$_SESSION['apply_js']['multiselest'] = 1;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$message = '';
// print_r($_POST);
if (isset($_POST['submit']) && in_array($_POST['submit'], ['insert', 'update'], true)) {

    if (!isset($_POST['in_gate_no']) || $_POST['in_gate_no'] == '0') {
        $message = 'Please select Gate No';
        exit;
    }
    if (!isset($_POST['visit_type']) || $_POST['visit_type'] == '') {
        $message = 'Please select Visit Type';
        exit;
    }
    // 1) Photo (use existing image if no new upload)
    $existingImage = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $newImageName = imageupload('visitors', 'image_path', $_POST['image_path']);
    } else {
        $newImageName = $existingImage;
    }
    $_POST['image_path'] = $newImageName;

    // 2) Normalize visiting_company_id from the three inputs into ONE field
    $visit_type = $_POST['visit_type'] ?? '';
    if ($visit_type === 'delivery') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_delivery'] ?? '';
    } elseif ($visit_type === 'service') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_service'] ?? '';
    } elseif ($visit_type === 'employee' && $_POST['emp_temp_enable'] == 1) {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_employee_temp'] ?? '';
    } elseif ($visit_type === 'doctor') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_doctor'] ?? '';
    } elseif ($visit_type === 'visitor' || $visit_type === 'others') { // visitor / others / default
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_visitor'] ?? '';
    }

    //    The backend checks $_POST['rid'] to decide insert vs update.
    $type = $_POST['submit']; // 'insert' or 'update'
    $url  = API . "?mode=insertVisitorBundle&method=post";
    $resp = file_post_contents($url, $_POST, $type);
    $result = json_decode($resp, true);

    if (!empty($result) && ($result['status'] ?? '') === 'success') {
        $message = ($type === 'update') ? 'Updated successfully' : 'Saved successfully';
        header("location:index.php?option=COM_LIST_VISITOR_LOGS");
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
$url = API . "?mode=getVisitorInsertRelatedData&method=get&rid=$rid&gate_id=$gate_id&location_id=" . $_SESSION['location_id'];

$result = callAPI($url);
$result = json_decode($result, 1);
$return = $result['visitor_logs_res'] ?? array();
$editMode = ($rid !== '');
$type     = strtolower($return['visit_type'] ?? '');
$purpose  = strtolower($return['purpose'] ?? '');
$badge    = $result['visitor_badge_res']['full_code'] ?? '';
// echo $badge.'****';


$showVisitor  = ($type === 'visitor' || $type === 'others');
$showDelivery = ($type === 'delivery');
$showService  = ($type === 'service');
$showEmployee = ($type === 'employee');
$showDoctor   = ($type === 'doctor'); // NEW

// Service tag children returned by your GET API (if you expose them)
$service_tag_children = $result['service_tag_children_res'] ?? [];
$has_service_tags   = !empty($service_tag_children);

// Employee temp flags (persisted on edit)
$emp_temp_enable  = !empty($return['emp_temp_enable']);
$emp_temp_reason  = $return['emp_temp_reason'] ?? 'forgot';
$emp_temp_meet_id = (int)($return['emp_temp_whom_to_meet'] ?? 0);

$showCompany   = ($showVisitor && $purpose === 'official');
$showAddress   = ($showVisitor && $purpose === 'personal');
$showPurposeOther = ($showVisitor && $purpose === 'others');
$showCard      = !$showEmployee; // card for all but employee




$employees_res = $result['employees_res'] ?? array();
$building_gates_res = $result['building_gates_res'] ?? array();
$visit_type_res = $result['visit_type_res'] ?? array();
$id_card_types_res = $result['id_card_types_res'] ?? array();
$delivery_company_res = $result['delivery_company_res'] ?? array();
$service_company_res = $result['service_company_res'] ?? array();

$materials_res = $result['materials_res'] ?? array(['id' => 1, 'name' => 'material_1'], ['id' => 2, 'name' => 'material_2']);   // [{id, name} or your schema]
$visitor_luggages_res = $result['visitor_luggages_res'] ?? array(); // for edit prefilling
// print_r($visitor_luggages_res);


$vendors = $result['vendors'];
$departments = $result['departments'];
$items =$result['items'];
?>

<?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', <?= json_encode($message) ?>, '');
        });
    </script>
<?php endif; ?>

<!-- Minimal CSS for Swiggy-like row -->

<link rel="stylesheet" href="assets/css/custom_insert_visitor_logs.css">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div id="visitorConfig"
data-edit-mode="<?php echo $editMode ? 1 : 0; ?>"
data-next-badge='<?php echo htmlspecialchars(
    json_encode($result['next_badge'] ?? []),
    ENT_QUOTES
    ); ?>'>
</div>

<!-- Page body start -->
<form id="visitorFom" method="post" enctype="multipart/form-data">
    <div class="card">
        <div class="card-header">
            <h5>Visitor Logs</h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Visit Type <b class="text-danger">*</b></label> <br>
                        <input type="hidden" name="location_id" id="location_id" value="<?php echo $_SESSION['location_id'] ?? 1; ?>">
                        <?php
                        $lockVisitType = $editMode; // true in edit, false in new
                        function vtAttrs($value, $type, $lockVisitType)
                        {
                            $checked  = ($type === $value) ? 'checked' : '';
                            $disabled = ($lockVisitType && $type !== $value) ? 'disabled' : '';
                            return trim("$checked $disabled");
                        }
                        ?>
                        <div class="visitor-slider">
                            <button type="button" class="scroll-btn left-btn" onclick="scrollVisitors(-300,'visitorWrapper')">&#10094;</button>
                            <div class="visitor-wrapper mt-4 p-2" id="visitorWrapper">
                                <label class="visitor-card">
                                    <input class="form-check-input" type="radio" name="visit_type" id="vt_visitor" value="visitor" <?= vtAttrs('visitor',  $type, $lockVisitType) ?>>
                                    <div class="icon-box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"></circle><path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path></svg>
                                    </div>
                                    <span>Visitor</span>
                                </label>
                                <label class="visitor-card">
                                    <input class="form-check-input  m-l-10" type="radio" name="visit_type" id="vt_delivery" value="delivery" <?= vtAttrs('delivery', $type, $lockVisitType) ?>>
                                    <div class="icon-box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <rect x="1" y="3" width="15" height="13"></rect> <polygon points="16,8 20,8 23,11 23,16 16,16"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle> <circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                    </div>
                                    <span>Delivery</span>
                                </label>

                                <label class="visitor-card">
                                    <input class="form-check-input  m-l-10" type="radio" name="visit_type" id="vt_service" value="service" <?= vtAttrs('service',  $type, $lockVisitType) ?>>
                                    <div class="icon-box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="M4.93 4.93l1.41 1.41"></path><path d="M17.66 17.66l1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="M4.93 19.07l1.41-1.41"></path><path d="M17.66 6.34l1.41-1.41"></path></svg>
                                    </div>
                                    <span>Service</span>
                                </label>

                                <label class="visitor-card">
                                    <input class="form-check-input  m-l-10" type="radio" name="visit_type" id="vt_employee" value="employee" <?= vtAttrs('employee', $type, $lockVisitType) ?>>
                                    <div class="icon-box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"></circle> <path d="M5 21c0-4 3-7 7-7s7 3 7 7"></path></svg>
                                    </div>
                                    <span>Employee</span>
                                </label>

                                <label class="visitor-card">
                                    <input class="form-check-input  m-l-10" type="radio" name="visit_type" id="vt_doctor" value="doctor" <?= vtAttrs('doctor',   $type, $lockVisitType) ?>>
                                    <div class="icon-box">
                                       <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <circle cx="12" cy="7" r="4"></circle> <path d="M5 21c0-4 3-7 7-7s7 3 7 7"></path> <path d="M18 4v4"></path><path d="M16 6h4"></path></svg>
                                    </div>
                                    <span>Doctor</span>
                               </label>

                               <label class="visitor-card">
                                    <input class="form-check-input m-l-10" type="radio" name="visit_type" id="vt_vendor_supplier" value="vendor_supplier" <?= vtAttrs('vendor_supplier',   $type, $lockVisitType) ?>>
                                    <div class="icon-box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="7" r="4"></circle><path d="M2 21c0-4 3-7 6-7"></path><rect x="14" y="10" width="8" height="8"></rect><path d="M14 14h8"></path></svg>
                                    </div>
                                    <span>Vendor/Supplier</span>
                                </label>
                                <label class="visitor-card">
                                    <input class="form-check-input m-l-10" type="radio" name="visit_type" id="vt_others" value="others" <?= vtAttrs('others',   $type, $lockVisitType) ?>>
                                    <div class="icon-box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <circle cx="5" cy="12" r="1"></circle><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle></svg>
                                    </div>
                                    <span>Others</span>
                                </label>
                            </div>
                            <button type="button" class="scroll-btn right-btn" onclick="scrollVisitors(300,'visitorWrapper')">&#10095;</button>
                        </div>
                    </div>    

                    <div class="form-group col-lg-6">
                        <?php
                                                // decide which gate should be preselected (no DB writes here)
                        $selected_gate_id = '0';
                        if (!empty($return['in_gate_no'])) {
                            $selected_gate_id = $return['in_gate_no'];
                        } elseif (!empty($gate_id)) {
                            $selected_gate_id = $gate_id;
                        }
                        ?>
                        <label>Gate No <b class="text-danger">*</b></label>
                        <div class="visitor-slider">
                            <button type="button" class="scroll-btn left-btn" onclick="scrollVisitors(-300,'visitorWrapperA')">&#10094;</button>
                            <div class="visitor-wrapper mt-4 p-2" id="visitorWrapperA">
                                <?php if (count((array)$building_gates_res) > 0) {
                                    foreach ($building_gates_res as $value) {  ?>
                                        <label class="visitor-card">
                                            <input type="radio" name="in_gate_no" checked="" value="<?php echo $value['id']; ?>">
                                            <div class="icon-box">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V7l8-4 8 4v14"></path><path d="M9 21V12h6v9"></path><path d="M4 7h16"></path></svg>
                                            </div>
                                            <span><?php echo $value['name']; ?></span>
                                        </label>
                                <?php } } ?>
                            </div>
                            <button type="button" class="scroll-btn right-btn" onclick="scrollVisitors(300,'visitorWrapperA')">&#10095;</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!-- ============ step 2 ============ -->


    <div class="card">
        <div class="card-body">
        <!-- ============ VISITOR ============ -->
        <div id="visitorDiv" class="row <?php echo $showVisitor ? 'd-view-block' : 'd-none'; ?>">
            <div class="form-group col-lg-6 col-xs-12">
                <label>Phone <b class="text-danger">*</b></label>
                <input class="form-control" type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($return['phone'] ?? ''); ?>">
            </div>

            <div class="form-group col-lg-6 col-xs-12">
                <label>Name</label>
                <input class="form-control" type="text" name="name" id="name" value="<?php echo htmlspecialchars($return['name'] ?? ''); ?>">
            </div>

            <div class="form-group col-lg-6 w-full">
                <label>Identity Card</label>
                <select class="form-select js-example-basic-single" id="idcard_type" name="idcard_type">
                    <option value="0"> Select Any one </option>
                    <?php if (count((array)$id_card_types_res) > 0) {
                        foreach ($id_card_types_res as $value) {
                            $sel = (!empty($return['idcard_type']) && $return['idcard_type'] == $value['genc_detail_code']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($value['genc_detail_code']) . '" ' . $sel . '>' . htmlspecialchars($value['genc_detail_name']) . '</option>';
                        }
                    } ?>
                </select>
            </div>

            <div class="form-group col-lg-6 col-xs-12">
                <label>Identity Card No.</label>
                <input class="form-control" type="text" name="idcard_number" id="idcard_number" value="<?php echo htmlspecialchars($return['idcard_number'] ?? ''); ?>">
            </div>

            <div class="form-group col-lg-6 w-full">
                <label>Whom To Meet <b class="text-danger">*</b></label>
                <select class="form-select js-example-basic-single" id="whom_to_meet" name="whom_to_meet">
                    <option value="0"> Select Any one </option>
                    <?php if (count((array)$employees_res) > 0) {
                        foreach ($employees_res as $e) {
                            $sel = (!empty($return['whom_to_meet']) && $return['whom_to_meet'] == $e['id']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                        }
                    } ?>
                </select>
            </div>

            <div class="form-group col-lg-6">
                <label>Purpose</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="purpose" id="purpose_official" value="official" <?php echo ($purpose === 'official' ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="purpose_official">Official</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="purpose" id="purpose_personal" value="personal" <?php echo ($purpose === 'personal' ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="purpose_personal">Personal</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="purpose" id="purpose_others" value="others" <?php echo ($purpose === 'others' ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="purpose_others">Others</label>
                </div>
            </div>

            <div class="form-group col-lg-6 col-xs-12">
                <label>Visiting Id Card No <b class="text-danger">*</b></label>
                <input class="form-control" type="text" name="visiting_company_id_visitor" id="visiting_company_id_visitor"
                value="<?php echo htmlspecialchars($badge ?: ($return['visiting_company_id'] ?? '')); ?>">
            </div>

            <div id="company_name_wrap" class="form-group col-lg-6 col-xs-12 <?php echo $showCompany ? 'd-view-block' : 'd-none'; ?>">
                <label>Company Name</label>
                <input class="form-control" type="text" name="company_name" id="company_name" value="<?php echo htmlspecialchars($return['company_name'] ?? ''); ?>">
            </div>

            <div id="address_wrap" class="form-group col-lg-6 col-xs-12 <?php echo $showAddress ? 'd-view-block' : 'd-none'; ?>">
                <label>Address</label>
                <textarea class="form-control" rows="5" cols="5" id="address" name="address"><?php echo htmlspecialchars($return['address'] ?? ''); ?></textarea>
            </div>

            <div id="purpose_other_wrap" class="form-group col-lg-6 mt-2 <?php echo $showPurposeOther ? 'd-view-block' : 'd-none'; ?>">
                <label>Visit Purpose</label>
                <input type="text" class="form-control" name="purpose_other" id="purpose_other" value="<?php echo htmlspecialchars($return['purpose_other'] ?? ''); ?>">
            </div>
        </div>

        <!-- ============ DELIVERY ============ -->
        <?php
                            // derive once, near your other flags
        $showDelivery = (strtolower($return['visit_type'] ?? '') === 'delivery');
        ?>
        <div id="deliveryDiv" class="row <?php echo $showDelivery ? 'd-view-block' : 'd-none'; ?>">

            <div class="form-group col-lg-6 col-xs-12 w-full">
                <label>Delivery Company / Person <b class="text-danger">*</b></label>
                <select class="form-select js-example-basic-single" id="delivery_company_person" name="delivery_company_person">
                    <option value=""> Select Any one </option>
                    <?php if (!empty($delivery_company_res)) {
                        foreach ($delivery_company_res as $e) {
                            $sel = (!empty($return['delivery_company_person']) && $return['delivery_company_person'] == $e['genc_detail_code']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($e['genc_detail_code']) . '" ' . $sel . '>' . htmlspecialchars($e['genc_detail_name']) . '</option>';
                        }
                    } ?>
                    <option value="__new__">+ Add New Company</option>
                </select>

                <div id="new_company_box" class="d-none mt-8">
                    <input type="text" class="form-control" name="new_delivery_company" id="new_delivery_company" placeholder="Enter new company name">
                </div>
            </div>

            <div class="form-group col-lg-6 col-xs-12">
                <label>Contact Number </label>
                <input class="form-control" type="text"
                name="delivery_phone" id="delivery_phone"
                value="<?php echo htmlspecialchars($return['delivery_phone'] ?? ''); ?>">
            </div>

            <div class="form-group col-lg-6 w-full">
                <label>Whom To Deliver <b class="text-danger">*</b></label>
                <select class="form-select js-example-basic-single" id="deliver_to" name="deliver_to">
                    <option value="0"> Select Any one </option>
                    <?php if (!empty($employees_res)) {
                        foreach ($employees_res as $e) {
                            $sel = (!empty($return['deliver_to']) && $return['deliver_to'] == $e['id']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                        }
                    } ?>
                </select>
            </div>
            <div class="form-group col-lg-6 col-xs-12">
                <label>AWB No.</label>
                <input class="form-control" type="text"
                name="delivery_awb_no" id="delivery_awb_no"
                value="<?php echo htmlspecialchars($return['delivery_awb_no'] ?? ''); ?>">
            </div>

            <!-- (6) Badge goes here for Delivery -->
            <div class="form-group col-lg-6 col-xs-12">
                <label>Visiting Id Card No <b class="text-danger">*</b></label>
                <input class="form-control" type="text"
                name="visiting_company_id_delivery" id="visiting_company_id_delivery"
                value="<?php
                                                // show existing badge from DB if present, else your NEXT_BADGE from API
                $badge = $result['visitor_badge_res']['full_code'] ?? ($result['next_badge']['full_code'] ?? '');
                echo htmlspecialchars($return['visiting_company_id'] ?? $badge);
            ?>">
        </div>


        </div>

        <!-- ============ SERVICE ============ -->
        <?php
        $showService = (strtolower($return['visit_type'] ?? '') === 'service');
        ?>
        <div id="serviceDiv" class="row <?php echo $showService ? 'd-view-block' : 'd-none'; ?>">

            <div class="form-group col-lg-6 col-xs-12 w-full">
                <label>Service Company <b class="text-danger">*</b></label>
                                    <!-- <input class="form-control" type="text"
                                        name="service_company" id="service_company"
                                        value="<?php echo htmlspecialchars($return['service_company'] ?? ''); ?>"> -->
                                        <select class="form-select js-example-basic-single" id="service_company" name="service_company">
                                            <option value=""> Select Any one </option>
                                            <?php if (!empty($service_company_res)) {
                                                foreach ($service_company_res as $e) {
                                                    $sel = (!empty($return['service_company']) && $return['service_company'] == $e['genc_detail_code']) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($e['genc_detail_code']) . '" ' . $sel . '>' . htmlspecialchars($e['genc_detail_name']) . '</option>';
                                                }
                                            } ?>
                                            <option value="__new__">+ Add New Company</option>
                                        </select>

                                        <div id="new_service_company_box" class="d-none mt-8">
                                            <input type="text" class="form-control" name="new_service_company" id="new_service_company" placeholder="Enter new company name">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6 col-xs-12">
                                        <label>Service Person Phone</label>
                                        <input class="form-control" type="text"
                                        name="service_phone" id="service_phone"
                                        value="<?php echo htmlspecialchars($return['service_phone'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group col-lg-6 col-xs-12">
                                        <label>Service Person Name</label>
                                        <input class="form-control" type="text"
                                        name="service_person" id="service_person"
                                        value="<?php echo htmlspecialchars($return['service_person'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group col-lg-6 w-full">
                                        <label>Identity Card</label>
                                        <select class="form-select js-example-basic-single" id="idcard_type_service" name="idcard_type_service">
                                            <option value="0"> Select Any one </option>
                                            <?php if (!empty($id_card_types_res)) {
                                                foreach ($id_card_types_res as $v) {
                                                    $sel = (!empty($return['idcard_type_service']) && $return['idcard_type_service'] == $v['genc_detail_code']) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($v['genc_detail_code']) . '" ' . $sel . '>' . htmlspecialchars($v['genc_detail_name']) . '</option>';
                                                }
                                            } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-6 col-xs-12">
                                        <label>Identity Card No.</label>
                                        <input class="form-control" type="text"
                                        name="idcard_number_service" id="idcard_number_service"
                                        value="<?php echo htmlspecialchars($return['idcard_number_service'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group col-lg-6 w-full">
                                        <label>Authorized By (Employee) <b class="text-danger">*</b></label>
                                        <select class="form-select js-example-basic-single" id="authorized_employee" name="authorized_employee">
                                            <option value="0"> Select Any one </option>
                                            <?php if (!empty($employees_res)) {
                                                foreach ($employees_res as $e) {
                                                    $sel = (!empty($return['authorized_employee']) && $return['authorized_employee'] == $e['id']) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                                                }
                                            } ?>
                                        </select>
                                    </div>

                                    <!-- (6) Badge goes here for Service -->
                                    <div class="form-group col-lg-6 col-xs-12">
                                        <label>Visiting Id Card No <b class="text-danger">*</b></label>
                                        <input class="form-control" type="text"
                                        name="visiting_company_id_service" id="visiting_company_id_service"
                                        value="<?php
                                        $badge = $result['visitor_badge_res']['full_code'] ?? ($result['next_badge']['full_code'] ?? '');
                                        echo htmlspecialchars($return['visiting_company_id'] ?? $badge);
                                    ?>">
                                </div>

                                <?php if ($rid != '' && $return['visit_type'] === 'service') { ?>
                                    <div class="form-group col-lg-2 col-xs-12 mt-2">
                                        <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        id="btnWorkPermit"
                                        data-url="index.php?option=COM_VIEW_VISITOR_WORK_PERMIT_PASS&Rid=<?php echo deocdeId('id:' . $rid); ?>">
                                        Work Permit
                                    </button>
                                </div>

                            </div>
                        <?php } ?>

                        <!-- Tag multiple visitors (Service only) -->
                        <div class="form-group col-lg-2 mt-2" id="serviceTagToggleWrap">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="service_tag_enable" name="service_tag_enable" value="1" <?php echo $has_service_tags ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="service_tag_enable">Tag multiple visitors</label>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 ">
                            <div class="row  <?php echo $has_service_tags ? 'd-view-block' : 'd-none'; ?>" id="serviceTagControls">
                                <div class="form-group col-lg-8 col-md-4 ">
                                    <label>How many?</label>
                                    <input type="number" min="1" step="1" class="form-control" name="service_tag_count" id="service_tag_count" placeholder="e.g., 3" value="<?php echo $has_service_tags ? count((array)$service_tag_children) : ''; ?>" <?php echo count((array)$service_tag_children) > 0 ? 'readonly' : ''; ?>>
                                </div>
                                <div class="form-group col-lg-4 col-md-4  d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-primary" id="service_tag_details_btn">Details</button>
                                    <span id="serviceTagSummary" class="badge bg-secondary ms-2 <?php echo $has_service_tags ? 'd-inline-show' : 'd-none'; ?>"> Saved: <?php echo $has_service_tags ? count((array)$service_tag_children) : 0; ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs generated after modal Save -->
                        <div id="serviceTagHiddenInputs">
                            <?php if ($has_service_tags): ?>
                                <?php foreach ($service_tag_children as $i => $t):
                                    $tName = htmlspecialchars($t['service_person'] ?? '');
                                    $tCode = htmlspecialchars($t['visiting_company_id'] ?? ($t['visitor_idcard_no'] ?? '')); ?>
                                    <input type="hidden" name="service_tags[<?php echo $i; ?>][name]" value="<?php echo $tName; ?>">
                                    <input type="hidden" name="service_tags[<?php echo $i; ?>][visitor_id]" value="<?php echo $tCode; ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>


                        <!-- Modal: Tag Multiple Visitors (Service) -->
                        <div class="modal fade" id="serviceTagModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header py-2">
                                        <h6 class="modal-title">Tag Visitors</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body pt-2">
                                        <p class="text-muted small mb-2">
                                            Base ID: <span id="stm_base_id">—</span> &nbsp; | &nbsp; Count: <span id="stm_count">0</span>
                                        </p>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle" id="serviceTagTableModal">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="minw-220">Name</th>
                                                        <th class="minw-180">Visitor ID</th>

                                                    </tr>
                                                </thead>
                                                <tbody><!-- rows injected by JS --></tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="modal-footer py-2">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="service_tag_save_btn">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- ============ EMPLOYEE ============ -->
                    <?php
                    $showEmployee = (strtolower($return['visit_type'] ?? '') === 'employee');
                    ?>
                    <div id="employeeDiv" class="row <?php echo $showEmployee ? 'd-view-block' : 'd-none'; ?>">

                        <div class="form-group col-lg-6 col-xs-12 w-full">
                            <label>Employee ID <b class="text-danger">*</b></label>
                            <select class="form-select js-example-basic-single" id="employee_code" name="employee_code">
                                <option value="0"> Select Any one </option>
                                <?php if (!empty($employees_res)) {
                                    foreach ($employees_res as $e) {
                                        $sel = (!empty($return['employee_code']) && $return['employee_code'] == $e['emp_code']) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($e['emp_code']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                                    }
                                } ?>
                            </select>


                        </div>

                        <div class="form-group col-lg-6 col-xs-12">
                            <label>Name </label>
                            <input class="form-control" type="text"
                            name="employee_name" id="employee_name"
                            value="<?php echo htmlspecialchars($return['employee_name'] ?? ''); ?>">
                        </div>

                        <div class="form-group col-lg-6 col-xs-12">
                            <label>Department </label>
                            <input class="form-control" type="text"
                            name="employee_department" id="employee_department"
                            value="<?php echo htmlspecialchars($return['employee_department'] ?? ''); ?>">
                        </div>

                        <?php if ($rid != '' && $return['visit_type'] === 'employee') { ?>
                            <div class="form-group col-lg-2 col-xs-12 mt-2 d-flex align-items-end">
                                <button
                                type="button"
                                class="btn btn-sm btn-primary"
                                id="btnMaterialGatePass"
                                data-url="index.php?option=COM_VIEW_VISITOR_MATERIAL_GATE_PASS&Rid=<?php echo deocdeId('id:' . $rid); ?>">
                                Material Gate Pass
                            </button>
                        </div>

                    </div>
                <?php } ?>

                <!-- Employee: Temp Visitor Pass toggle -->
                <div class="form-group col-lg-6  mt-2 d-flex align-items-center ">
                    <div class="form-check form-switch col-lg-6">
                        <input class="form-check-input" type="checkbox" id="emp_temp_enable" name="emp_temp_enable" value="1" <?php echo $emp_temp_enable ? 'checked' : ''; ?>>
                        <label class="form-check-label ms-1" for="emp_temp_enable">Issue Visitor Pass (Temp)</label>
                    </div>

                    <!-- Reason (tiles/radios) -->
                    <div id="empTempReasonWrap" class="col-lg-6 d-none">
                        <div class="form-group  mt-2">
                            <div class="form-check form-check-inline ">
                                <input class="form-check-input" type="radio" name="emp_temp_reason" id="emp_reason_forgot" value="forgot"
                                <?php echo ($emp_temp_reason == 'forgot') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="emp_reason_forgot">Forgot ID</label>
                            </div>
                            <div class="form-check form-check-inline ">
                                <input class="form-check-input" type="radio" name="emp_temp_reason" id="emp_reason_new" value="new"
                                <?php echo ($emp_temp_reason === 'new') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="emp_reason_new">New Employee</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="emp_temp_reason" id="emp_reason_xbranch" value="xbranch"
                                <?php echo ($emp_temp_reason === 'xbranch') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="emp_reason_xbranch">Other Branch</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Minimal details (always phone + name; + host only for xbranch) -->
                <div id="empTempFields" class="row d-none">
                    <div class="form-group col-lg-6 col-xs-12 mt-2">
                        <label>Phone <b class="text-danger">*</b></label>
                        <input class="form-control" type="text" name="emp_temp_phone" id="emp_temp_phone"
                        value="<?php echo $emp_temp_enable ? htmlspecialchars($return['emp_temp_phone'] ?? '') : ''; ?>">
                    </div>
                    <div class="form-group col-lg-6 col-xs-12 mt-2">
                        <label>Name <b class="text-danger">*</b></label>
                        <input class="form-control" type="text" name="emp_temp_name" id="emp_temp_name"
                        value="<?php echo $emp_temp_enable ? htmlspecialchars($return['emp_temp_name'] ?? '') : ''; ?>">
                    </div>
                    <div class="form-group col-lg-6 col-xs-12 mt-2 w-full d-none" id="empTempHostWrap">
                        <label>Whom To Meet <b class="text-danger">*</b></label>
                        <select class="form-select js-example-basic-single" id="emp_temp_whom_to_meet" name="emp_temp_whom_to_meet">
                            <option value="0"> Select Any one </option>
                            <?php if (!empty($employees_res)) {
                                foreach ($employees_res as $e) {
                                    $sel = ($emp_temp_meet_id && $emp_temp_meet_id == $e['id']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['display_name']) . '</option>';
                                }
                            } ?>
                        </select>

                    </div>

                    <!-- Visitor ID (drives the EXISTING Visitor Card UI) -->
                    <div class="form-group col-lg-6 col-xs-12 mt-2 d-none" id="empTempVisitorIdWrap">
                        <label>Visiting Id Card No <b class="text-danger">*</b></label>
                        <div class="input-group">
                            <input class="form-control" type="text"
                            name="visiting_company_id_employee_temp"
                            id="visiting_company_id_employee_temp"
                            value="<?php echo $emp_temp_enable ? htmlspecialchars($return['visiting_company_id'] ?? '') : ''; ?>">

                        </div>
                    </div>
                </div>

            </div>


            <!-- ============ DOCTOR ============ -->
            <div id="doctorDiv" class="row <?php echo $showDoctor ? 'd-view-block' : 'd-none'; ?>">

                <div class="form-group col-lg-6 col-xs-12">
                    <label>Phone <b class="text-danger">*</b></label>
                    <input class="form-control" type="text" name="doctor_phone" id="doctor_phone" value="<?php echo htmlspecialchars($return['doctor_phone'] ?? ''); ?>">
                </div>

                <div class="form-group col-lg-6 col-xs-12">
                    <label>Name <b class="text-danger">*</b></label>
                    <input class="form-control" type="text" name="doctor_name" id="doctor_name" value="<?php echo htmlspecialchars($return['doctor_name'] ?? ''); ?>">
                </div>

                <div class="form-group col-lg-6 w-full">
                    <label>Identity Card</label>
                    <select class="form-select js-example-basic-single" id="doctor_idcard_type" name="doctor_idcard_type">
                        <option value="0"> Select Any one </option>
                        <?php if (count((array)$id_card_types_res) > 0) {
                            foreach ($id_card_types_res as $value) {
                                $sel = (!empty($return['doctor_idcard_type']) && $return['doctor_idcard_type'] == $value['genc_detail_code']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($value['genc_detail_code']) . '" ' . $sel . '>' . htmlspecialchars($value['genc_detail_name']) . '</option>';
                            }
                        } ?>
                    </select>
                </div>

                <div class="form-group col-lg-6 col-xs-12">
                    <label>Identity Card No.</label>
                    <input class="form-control" type="text" name="doctor_idcard_number" id="doctor_idcard_number" value="<?php echo htmlspecialchars($return['doctor_idcard_number'] ?? ''); ?>">
                </div>

                <div class="form-group col-lg-6 col-xs-12">
                    <label>Visiting Id Card No <b class="text-danger">*</b></label>
                    <input class="form-control" type="text" name="visiting_company_id_doctor" id="visiting_company_id_doctor"
                    value="<?php echo htmlspecialchars($badge ?: ($return['visiting_company_id'] ?? '')); ?>">
                </div>

            </div>


            <!-- ============ Start Vendor Supplier ============ -->
            <?php $showvendor_supplier = (strtolower($return['visit_type'] ?? '') === 'vendor_supplier');?>
            <div id="vendor_supplierDiv" class="row <?php echo $showvendor_supplier ? 'd-view-block' : 'd-none'; ?>">
                <?php include('custom_insert_visitor_logs_vendor_suppliers.php'); ?>
            </div>
            <!-- ============ End Vendor Supplier ============ -->


            <!-- ======= SHARED: PHOTO (for all types) ======= -->
            <div id="photoBlock" class="row">
                <div class="form-group col-lg-3 col-xs-6">
                    <label>Photo</label>
                    <input class="form-control mb-1" type="file" name="image_path" id="image_path" />
                </div>
                <div class="form-group col-lg-3 col-xs-6">
                    <label>In Time</label>
                    <?php
                    $inDatetimeValue = !empty($return['in_datetime']) ? date('Y-m-d\TH:i', strtotime($return['in_datetime'])) : date('Y-m-d\TH:i');
                    ?>
                    <input class="form-control mb-1" type="datetime-local" name="in_datetime" id="in_datetime" value="<?php echo $inDatetimeValue; ?>" />
                </div>
                <div class="col-lg-6"></div>
                <div class="form-group col-lg-6 mt-2 row">
                    <!-- Person photo preview (no new input) -->
                    <div id="personPreviewWrap" class="form-group col-lg-6 mt-2 <?php echo $rid ? 'd-view-block' : 'd-none'; ?>">
                        <div class="photo-frame-inline">
                            <img id="personPreviewInline"
                            src="<?php echo !empty($return['image_path']) ? 'uploads/visitors/' . htmlspecialchars($return['image_path']) : ''; ?>"
                            alt="Person Photo"
                            class="<?php echo empty($return['image_path']) ? 'd-none' : 'd-view-block'; ?>">
                            <div class="photo-placeholder-inline <?php echo !empty($return['image_path']) ? 'd-none' : 'd-view-block'; ?>">
                                Person photo
                            </div>
                        </div>
                    </div>

                    <!-- Visitor Card (shared for visitor/delivery/service/others; hidden for employee) -->
                    <div id="visitorCardWrap" class="form-group col-lg-6 mt-2 <?php echo $showCard ? 'd-view-block' : 'd-none'; ?>">
                        <div class="visit-card-image">
                            <img src="assets/images/visit_card_temp.png" alt="Visitor Card" class="img-fluid" />
                            <div class="visit-card-code" id="visit_card_code"><?php echo $badge ? htmlspecialchars($badge) : '—'; ?></div>
                        </div>
                    </div>
                </div>
                <!-- Visitor history (hidden by default) -->
                <div id="visitorHistoryWrap" class=" col-lg-6 mt-4 d-none">
                    <h6 class="mb-2">Visitor History</h6>

                    <div class="table-responsive">
                        <table class="table table-sm  align-middle" id="visitorHistoryTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="minw-160">Name</th>
                                    <th class="minw-130">Phone</th>
                                    <th class="minw-180">Check-in</th>
                                    <th class="minw-180">Check-out</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!-- =========================    STEP 3: Luggage ========================= -->
            <div class="row">
                <div class="form-group col-lg-12">
                    <label>Carrying any items?</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="has_luggage" id="lug_no" value="no" checked>
                        <label class="form-check-label" for="lug_no">No</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="has_luggage" id="lug_yes" value="yes">
                        <label class="form-check-label" for="lug_yes">Yes</label>
                    </div>
                </div>
            </div>

            <!-- Swiggy-style item row (dynamic per visit_type) -->
            <div id="luggageSection" class="d-none">
                <div id="luggageScroller" class="luggage-scroller"></div>

                <!-- Selected items list -->
                <div class="mt-4">
                    <h6 class="mb-2">Carrying Items</h6>
                    <div id="luggageList" class="luggage-list col-md-6" data-has-prefill="<?php echo ($editMode && !empty($visitor_luggages_res)) ? '1' : '0'; ?>">
                        <?php if ($editMode && !empty($visitor_luggages_res)): ?>

                            <?php foreach ($visitor_luggages_res as $L):
                                $item_key    = htmlspecialchars($L['item_key'] ?? '');
                                $item_label  = htmlspecialchars($L['item_name'] ?? 'Item');
                                $brand       = htmlspecialchars($L['brand_name'] ?? '');
                                $qty         = htmlspecialchars($L['qty'] ?? '1');
                                $ser         = htmlspecialchars($L['description'] ?? '');
                                $retDate     = trim($L['return_date'] ?? '');
                                $returnable  = ($retDate && $retDate !== '0000-00-00') ? 'yes' : 'no';
                                $rowId       = 'lug_row_' . $L['id'];
                                ?>
                                <div class="lug-item" id="<?php echo $rowId; ?>" data-item-key="<?php echo $item_key; ?>">
                                    <div class="lug-meta">
                                        <strong><?php echo $item_label; ?></strong>
                                        <div>Brand: <?php echo $brand ?: '-'; ?></div>
                                        <div>Qty: <?php echo $qty; ?></div>
                                        <div>Serial/Remarks: <?php echo $ser ?: '-'; ?></div>
                                        <div>Returnable: <?php echo $returnable; ?><?php echo ($returnable === 'yes' && $retDate) ? ' — ' . htmlspecialchars($retDate) : ''; ?></div>
                                    </div>
                                    <div class="lug-actions">
                                        <button type="button" class="js-remove-luggage" data-name="removeLuggageRow" data-row-id="<?php echo $rowId; ?>" data-item-key="<?php echo $item_key; ?>">

                                            <i class="icofont icofont-trash"></i>
                                        </button>
                                    </div>
                                    <div class="d-none">
                                        <input type="hidden" name="luggage[<?php echo $L['id']; ?>][id]" value="<?php echo (int)$L['id']; ?>">
                                        <input type="hidden" name="luggage[<?php echo $L['id']; ?>][item_key]" value="<?php echo $item_key; ?>">
                                        <input type="hidden" name="luggage[<?php echo $L['id']; ?>][item_name]" value="<?php echo $item_label; ?>">
                                        <input type="hidden" name="luggage[<?php echo $L['id']; ?>][brand_name]" value="<?php echo $brand; ?>">
                                        <input type="hidden" name="luggage[<?php echo $L['id']; ?>][qty]" value="<?php echo $qty; ?>">
                                        <input type="hidden" name="luggage[<?php echo $L['id']; ?>][serial_or_remarks]" value="<?php echo $ser; ?>">
                                        <input type="hidden" name="luggage[<?php echo $L['id']; ?>][returnable]" value="<?php echo $returnable; ?>">
                                        <?php if ($returnable === 'yes'): ?>
                                            <input type="hidden" name="luggage[<?php echo $L['id']; ?>][return_date]" value="<?php echo htmlspecialchars($retDate); ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- Modal: add luggage detail -->
            <div class="modal fade" id="luggageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <h6 class="modal-title">Add Item</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body pt-2">
                            <input type="hidden" id="lm_item_key">
                            <div class="mb-2">
                                <label class="form-label">Item</label>
                                <input type="text" class="form-control" id="lm_item_label" readonly>
                            </div>


                            <div class="mb-2">
                                <label class="form-label ">Brand Name</label>
                                <select class="form-select" id="lm_brand_name">
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
                                <input type="number" min="1" class="form-control" id="lm_qty" value="1">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Serial No / Remarks</label>
                                <input type="text" class="form-control" id="lm_serial_or_remarks" placeholder="">
                            </div>

                            <!-- Returnable -->
                            <div class="mb-2">
                                <label class="form-label me-3">Returnable</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="lm_returnable" id="lm_returnable_yes" value="yes" checked>
                                    <label class="form-check-label" for="lm_returnable_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="lm_returnable" id="lm_returnable_no" value="no">
                                    <label class="form-check-label" for="lm_returnable_no">No</label>
                                </div>
                            </div>

                            <!-- Return Date (shown only when Returnable = Yes) -->
                            <div class="mb-2 d-none" id="lm_return_date_wrap">
                                <label class="form-label">Return Date</label>
                                <input type="date" class="form-control" id="lm_return_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>

                        </div>
                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="lm_save_btn">Add</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- =========================    STEP 3: Luggage END ========================= -->

            <div class="border-top">
                <?php
                if ($rid != "") {  ?>
                    <button type="submit" name='submit' value='update' class="btn  btn-primary mt-2">
                        Update
                    </button>
                    <input name="rid" type="hidden" value="<?php echo $rid; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo $return['image_path'] ?? ''; ?>">

                <?php } else {  ?>
                    <button type="submit" name='submit' value='insert' class="btn  btn-primary mt-2">
                        Save
                    </button>
                <?php } ?>
            </div>
        </div>
    </form>
   
        <script src="assets/js/custom_insert_visitor_logs.js"></script>

        <!-- Font Awesome (icons) + Bootstrap 5 modal deps (if not already on page) -->