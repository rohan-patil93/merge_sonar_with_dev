
<?php
session_start();
$_SESSION['apply_js']['multiselest'] = 1;
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && $_POST['submit'] === 'exitonly') {
    $existingImage = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $newImageName = imageupload('visitors', 'image_path', $_POST['image_path']);
    } else {
        $newImageName = $existingImage;
    }
    $_POST['image_path'] = $newImageName;

    $url  = API . "?mode=employeeExitOnlyOut&method=post";
    $resp = file_post_contents($url, $_POST, 'exitonly');
    $result = json_decode($resp, true);
    if (!empty($result) && ($result['status'] ?? '') === 'success') {
        $message = 'Exit recorded successfully';
    } else {
        $message = 'Exit failed';
    }
}
$gate_id = $_SESSION['gate_id'] ?? 1;
$url = API . "?mode=getEmployeeListForExitOnly&method=get";
$result = callAPI($url);
$res = json_decode($result, true);
$employees = $res['employees'] ?? array();
$building_gates_res = $res['building_gates_res'] ?? array();

?>

    <link rel="stylesheet" href="assets/css/custom_visitor_exitonly_employee.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha384-PPIZEGYM1v8zp5Py7UjFb79S58UeqCL9pYVnVPURKEqvioPROaVAJKKLzvH2rDnI" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <div class="card">
        <div class="card-header">
            <h5><?= __('Employee Exit Only (Work-from-Home Asset Out)'); ?></h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i> <?= __('Back'); ?>
                </a>
            </div>
        </div>
        <div class="card-infisuite mt-2">
            <div class="card-body p-4 ">
                <?php if (!empty($message)): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', <?= json_encode($message) ?>, '');
                        });
                    </script>
                <?php endif; ?>
                <form id="employeeExitOnlyForm" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="form-group col-lg-6 col-xs-12">
                            <span><?= __('Employee'); ?> <b class="text-danger">*</b></span>
                            <select aria-label="<?= htmlspecialchars(__('Employee'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single" name="employee_id" id="employee_id" required>
                                <option value=""><?= __('Select Employee'); ?></option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= htmlspecialchars($emp['id']) ?>">
                                        <?= htmlspecialchars($emp['emp_code'] . ' - ' . $emp['display_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                                                <?php
                        $selected_gate_id = '0';
                        if (!empty($gate_id)) {
                            $selected_gate_id = $gate_id;
                        }
                        ?>

                        <!-- Gate (exact syntax you use) -->
                        <div class="form-group col-lg-6 w-full">
                            <span><?= __('Gate No'); ?> <b class="text-danger">*</b></span>
                            <select aria-label="<?= htmlspecialchars(__('Out Gate No'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single" id="out_gate_no" name="out_gate_no" required>
                                <option value="0"> <?= __('Select Any one'); ?> </option>
                                <?php if (count((array)$building_gates_res) > 0) {
                                    foreach ($building_gates_res as $value) {  ?>
                                        <option value="<?php echo $value['id']; ?>"
                                            <?php echo $selected_gate_id == $value['id'] ? 'selected' : '' ?>>
                                            <?php echo $value['name']; ?>
                                        </option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-6 col-xs-12">
                            <span><?= __('Photo'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Image Path'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="file" name="image_path" id="image_path" />
                        </div>
                    </div>
                    <!-- =========================    STEP 3: Luggage ========================= -->
                    <div class="row mt-4">
                        <div class="form-group col-lg-12">

                            <input aria-label="<?= htmlspecialchars(__('Visit Type'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visit_type" id="vt_employee" value="employee" checked style="display:none;">
                            <span><?= __('Carrying any items?'); ?></span><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_luggage" id="lug_no" value="no" checked>
                                <label class="form-check-label" for="lug_no"><?= __('No'); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_luggage" id="lug_yes" value="yes">
                                <label class="form-check-label" for="lug_yes"><?= __('Yes'); ?></label>
                            </div>
                        </div>
                    </div>
                    <!-- Swiggy-style item row (dynamic per visit_type) -->
                    <div id="luggageSection"  class="d-none mt-3">
                        <div id="luggageScroller" class="luggage-scroller"></div>
                        <div class="mt-4">
                            <h6 class="mb-2"><?= __('Carrying Items'); ?></h6>
                            <div id="luggageList" class="luggage-list col-md-6" data-has-prefill="0"></div>
                        </div>
                    </div>
                    <div class="border-top mt-4">
                        <button type="submit" name="submit" value="exitonly" class="btn btn-primary mt-2">
                            <?= __('Submit Exit Only'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- Modal: add luggage detail -->
<div class="modal fade" id="luggageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title"><?= __('Add Item'); ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
            </div>
            <div class="modal-body pt-2">
                <input type="hidden" id="lm_item_key">
                <div class="mb-2">
                    <span class="form-label"><?= __('Item'); ?></span>
                    <input aria-label="<?= htmlspecialchars(__('Lm Item Label'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" id="lm_item_label" readonly>
                </div>
                <div class="mb-2">
                    <span class="form-label "><?= __('Brand Name'); ?></span>
                    <select aria-label="<?= htmlspecialchars(__('Lm Brand Name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="lm_brand_name">
                        <option value=""><?= __('Select brand'); ?></option>
                        <option><?= __('Dell'); ?></option>
                        <option><?= __('HP'); ?></option>
                        <option><?= __('Apple'); ?></option>
                        <option><?= __('Lenovo'); ?></option>
                        <option><?= __('Samsung'); ?></option>
                        <option><?= __('Sony'); ?></option>
                        <option><?= __('LG'); ?></option>
                        <option><?= __('Canon'); ?></option>
                        <option><?= __('Nikon'); ?></option>
                        <option><?= __('Other'); ?></option>
                    </select>
                </div>
                <div class="mb-2">
                    <span class="form-label"><?= __('Quantity'); ?></span>
                    <input aria-label="<?= htmlspecialchars(__('Lm Qty'), ENT_QUOTES, 'UTF-8'); ?>" type="number" min="1" class="form-control" id="lm_qty" value="1">
                </div>
                <div class="mb-2">
                    <span class="form-label"><?= __('Serial No / Remarks'); ?></span>
                    <input aria-label="<?= htmlspecialchars(__('Lm Serial Or Remarks'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" id="lm_serial_or_remarks" placeholder="">
                </div>
                <!-- Returnable -->
                <div class="mb-2">
                    <span class="form-label me-3"><?= __('Returnable'); ?></span>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="lm_returnable" id="lm_returnable_yes" value="yes" checked>
                        <label class="form-check-label" for="lm_returnable_yes"><?= __('Yes'); ?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="lm_returnable" id="lm_returnable_no" value="no">
                        <label class="form-check-label" for="lm_returnable_no"><?= __('No'); ?></label>
                    </div>
                </div>
                <!-- Return Date (shown only when Returnable = Yes) -->
                <div class="mb-2 d-none" id="lm_return_date_wrap" >
                    <span class="form-label"><?= __('Return Date'); ?></span>
                    <input aria-label="<?= htmlspecialchars(__('Lm Return Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" class="form-control" id="lm_return_date" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?= __('Cancel'); ?></button>
                <button type="button" class="btn btn-primary" id="lm_save_btn"><?= __('Add'); ?></button>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/custom_visitor_exitonly_employee.js"></script>

