<?php
if ($_POST['ajaxcall'] == "Y") {
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}

function showNotificationIcon($status)
{
    if ($status == 'verified') {
        return '<i class="fa fa-check text-success"></i>';
    } elseif ($status == 'error') {
        return '<i class="fa fa-times text-danger"></i>';
    } elseif ($status == 'warning') {
        return '<i class="fa fa-exclamation-triangle text-warning"></i>';
    }
    return '<i class="fa fa-clock-o text-muted"></i>';
}

$visible_document_verifications = ['PAN', 'GSTIN', 'BANK', 'MSME', 'TAN'];

if (!isset($vendor_verifications)) {
    $vendor_verifications = [];
}

if (isset($vendordetail['vendor_verifications']) && !empty($vendordetail['vendor_verifications'])) {
    $vendor_verifications = json_decode($vendordetail['vendor_verifications'], true);
    if (!is_array($vendor_verifications)) {
        $vendor_verifications = [];
    }
} else {
    $vendor_verifications = [];
}
?>

<link rel="stylesheet" type="text/css" href="assets/css/custom_insert_vendors_and_vendor_registers_verifications.css">

<div class="verification-container">
    <!-- PHP Data Containers for JavaScript -->
    <div class="php-data-container d-none" >
        <div class="vendor-id-data"><?php echo $vendor_id ?? ''; ?></div>
        <div class="transaction-table-data"><?php echo $transaction_table ?? ''; ?></div>
        <div class="required-verifications-data"><?php echo json_encode($required_document_verifications ?? []); ?></div>
        <div class="visible-verifications-data"><?php echo json_encode($visible_document_verifications); ?></div>
        <div class="vendor-verifications-data"><?php echo json_encode($vendor_verifications); ?></div>
        <div class="vendor-detail-email-data"><?php echo $vendordetail['email'] ?? ''; ?></div>
        <div class="vendor-detail-company-data"><?php echo $vendordetail['company'] ?? ''; ?></div>
        <div class="session-error-3001-data"><?php echo $_SESSION['error_message']['3001'] ?? ''; ?></div>
        <div class="session-error-3000-data"><?php echo $_SESSION['error_message']['3000'] ?? ''; ?></div>
    </div>

    <!-- Notification Container -->
    <div id="notification-container"></div>
    <!-- Step Indicator -->
    <div class="step-indicator">
        <?php
        $step_mapping = [
            'PAN' => ['title' => 'PAN', 'icon' => 'fa-id-card'],
            'GSTIN' => ['title' => 'GST', 'icon' => 'fa-building'],
            'BANK' => ['title' => 'Bank', 'icon' => 'fa-university'],
            'MSME' => ['title' => 'MSME', 'icon' => 'check'],
            'TAN' => ['title' => 'TAN', 'icon' => 'fa-file-text']
        ];

        $step_counter = 1;
        $first_incomplete_step = null;

        foreach ($visible_document_verifications as $verification_type) {
            if (isset($step_mapping[$verification_type])) {
                $is_verified = isset($vendor_verifications[$verification_type]['status']) &&
                    $vendor_verifications[$verification_type]['status'] == 'Y';

                $step_class = '';
                if ($is_verified) {
                    $step_class = 'completed';
                } elseif ($first_incomplete_step === null) {
                    $step_class = 'active';
                    $first_incomplete_step = $step_counter;
                }

                echo '<div class="step ' . $step_class . ' step-indicator-item" data-step="' . $step_counter . '">';
                echo '<div class="step-number">';
                if ($is_verified) {
                    echo '<i class="fa fa-check"></i>';
                } else {
                    echo $step_counter;
                }
                echo '</div>';
                $mandatory_indicator = in_array($verification_type, $required_document_verifications) ? ' <span class="text-danger">*</span>' : '';
                echo '<div class="step-title">' . $step_mapping[$verification_type]['title'] . $mandatory_indicator . '</div>';
                echo '</div>';
                $step_counter++;
            }
        }
        ?>
    </div>

    <?php
    // Generate verification steps dynamically
    $step_counter = 1;
    $first_incomplete_step = null;

    // Find first incomplete step
    foreach ($visible_document_verifications as $verification_type) {
        $is_verified = isset($vendor_verifications[$verification_type]['status']) &&
            $vendor_verifications[$verification_type]['status'] == 'Y';
        if (!$is_verified && $first_incomplete_step === null) {
            $first_incomplete_step = $step_counter;
        }
        $step_counter++;
    }

    $step_counter = 1;
    foreach ($visible_document_verifications as $verification_type) {
        // Check verification status
        $is_verified = isset($vendor_verifications[$verification_type]['status']) &&
            $vendor_verifications[$verification_type]['status'] == 'Y';

        $active_class = '';
        if ($is_verified) {
            $active_class = 'completed';
        } elseif ($step_counter == $first_incomplete_step) {
            $active_class = 'active';
        }
        ?>
        <div id="step-<?php echo $step_counter; ?>" class="verification-step <?php echo $active_class; ?>">
        <?php
        if ($verification_type == 'PAN') {
            ?>

                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> <?= __('PAN Card Verification - Completed'); ?></h3>
                        <p class="text-success"><?= __('Your PAN has been successfully verified on'); ?>
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['PAN']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-id-card text-primary"></i> <?= __('PAN Card Verification'); ?> <?php echo in_array('PAN', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p><?= __('Enter your PAN card number to verify your identity and business details'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('PAN Number'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['pan_no'] ?? '')); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('Company Name'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Company Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="company_name" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['company'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> <?= __('This verification is completed and cannot be edited. Contact the administrator if changes are needed.'); ?>
                                </small>
                            </div>
                        <?php else: ?>

                            <div class="form-group w-full">
                                <span class="form-label"><?= __('Company Type'); ?> <span class="text-danger">*</span></span>
                                <select aria-label="<?= htmlspecialchars(__('Company Type'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single" id="company_type" name="company_type">
                                    <option value=""><?= __('Select Company Type'); ?></option>
                                    <?php
                                    if (isset($companytype) && is_array($companytype)) {
                                        foreach($companytype as $index => $ctype) {
                                            $value = $index + 1;
                                            $selected = (isset($vendordetail['business_type']) && $vendordetail['business_type'] == $value) ? 'selected' : '';
                                            echo '<option value="' . $value . '" ' . $selected . '>' . htmlspecialchars($ctype['company_type_name']) . '</option>';
                                        }
                                    } else {
                                        foreach($result['company_types'] as $ctype) {
                                            $selected = (isset($vendordetail['business_type']) && $vendordetail['business_type'] == $ctype['id']) ? 'selected' : '';
                                            echo '<option value="' . $ctype['id'] . '" ' . $selected . '>' . htmlspecialchars($ctype['company_type_name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group <?php echo $vendordetail['email']!=""?"event_disabled":""; ?>">
                                <span class="form-label"><?= __('Email'); ?> <span class="text-danger">*</span></span>
                                <input aria-label="<?= htmlspecialchars(__('Enter company email'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control company-email-input" id="company_email" placeholder="<?= htmlspecialchars(__('Enter company email'), ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo $vendordetail['email'] ?? ''; ?>" >
                            </div>

                            <div class="form-group <?php echo $vendordetail['company']!=""?"event_disabled":""; ?>">
                                <span class="form-label"><?= __('Company Name'); ?> <span class="text-danger">*</span></span>
                                <input aria-label="<?= htmlspecialchars(__('Enter company name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control company-name-input"
                                    id="company_name_2" placeholder="<?= htmlspecialchars(__('Enter company name'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $vendordetail['company'] ?? ''; ?>" >
                                <input type="hidden" id="company_check_status" value="">
                                <input type="hidden" id="verified_company_name" value="">
                                <span class="text-danger f-12 d-none" id="allreadycompanyname" >
                                    <?= __('Company Name Already Register'); ?>
                                </span>
                            </div>

                            <div class="form-group">
                                <span class="form-label"><?= __('PAN Number'); ?> <span class="text-danger">*</span></span>
                                <div class="input-group">
                                    <input aria-label="<?= htmlspecialchars(__('ABCDE1234F'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control pan-input text-uppercase" id="pan_number" placeholder="<?= htmlspecialchars(__('ABCDE1234F'), ENT_QUOTES, 'UTF-8'); ?>"
                                        maxlength="10"
                                        value="<?php echo dynamicDataDecode($vendordetail['pan_no'] ?? ''); ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted"><?= __('Format: ABCDE1234F (5 letters, 4 digits, 1 letter)'); ?></small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-secondary btn-sm toggle-edit-btn" id="pan_edit_btn"
                                            data-section="pan">
                                            <?= __('Change Info'); ?>
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_pan_btn" data-verification-type="pan">
                                            <i class="fa fa-search"></i> <?= __('Verify PAN'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <button type="button" class="btn btn-primary verify-btn d-none" id="verify_pan_btn_hidden" data-verification-type="pan">

                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="pan_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> <?= __('PAN Verification Details'); ?></h5>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <span class="form-label"><?= __('Verified Name'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Pan Verified Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="pan_verified_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('PAN Type'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Pan Verified Type'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="pan_verified_type" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Status'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Pan Verified Status'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="pan_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="pan_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> <?= __('PAN Verification Completed'); ?></h6>
                            <p class="mb-0"><?= __('PAN details have been successfully verified and match your records.'); ?></p>
                        </div>

                        <!-- PAN Company Name Confirmation Section -->
                        <div id="pan_company_confirmation_section" class="confirmation-section d-none pan-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> <?= __('Data Fetched Successfully - Please Verify'); ?></h6>
                            <p class="mb-3"><?= __('The PAN data has been fetched successfully, but there seems to be a mismatch in the company name. Please check the details below and confirm if they are correct'); ?>:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong><?= __('Note'); ?>:</strong> <?= __('The system has detected different company names. Please verify which one is correct for your business registration.'); ?>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><?= __('Your Entered Name'); ?>:</strong> <span id="confirm_user_company_name_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong><?= __('PAN Registered Name'); ?>:</strong> <span id="confirm_pan_company_name_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong><?= __('PAN Number'); ?>:</strong> <span id="confirm_pan_number_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-pan-company-btn">
                                    <i class="fa fa-check"></i> <?= __('Confirm - Use PAN Registered Name'); ?>
                                </button>
                                <button type="button" class="btn btn-secondary cancel-pan-company-btn">
                                    <i class="fa fa-edit"></i> <?= __('Let Me Correct the Details'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        } elseif ($verification_type == 'GSTIN') {
            ?>

                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> <?= __('GST Number Verification - Completed'); ?></h3>
                        <p class="text-success"><?= __('Your GST has been successfully verified on'); ?>
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['GSTIN']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-building text-primary"></i> <?= __('GST Number Verification'); ?> <?php echo in_array('GSTIN', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p><?= __('Verify your GST registration details and business information'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('GST Number'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['gstin'] ?? '')); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('Verification Date'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['GSTIN']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['GSTIN']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> <?= __('This verification is completed and cannot be edited. Contact the administrator if changes are needed.'); ?>
                                </small>
                            </div>

                        <?php else: ?>

                            <div class="form-group">
                                <span class="form-label"><?= __('GST Number'); ?> <span class="text-danger">*</span></span>
                                <div class="input-group">
                                    <input aria-label="<?= htmlspecialchars(__('22AAAAA0000A1Z5'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control gst-input text-uppercase" id="gstin" placeholder="<?= htmlspecialchars(__('22AAAAA0000A1Z5'), ENT_QUOTES, 'UTF-8'); ?>"
                                        maxlength="15"
                                        value="<?php echo dynamicDataDecode($vendordetail['gstin'] ?? ''); ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <?= __('Format: 15 characters (2 digits + 10 PAN + 1 digit + 1 letter + 1 digit/letter)'); ?>
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-secondary btn-sm toggle-edit-btn" id="gstin_edit_btn"
                                            data-section="gstin">
                                            <?= __('Change Info'); ?>
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_gst_btn_visible" data-verification-type="gst">
                                            <i class="fa fa-search"></i> <?= __('Verify GST'); ?>
                                        </button>
                                    </div>
                                </div>
                                <?php if (!in_array('GSTIN', $required_document_verifications)): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm skip-step-btn" data-step-type="gstin">
                                                <?= __('Skip This Step'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <small class="text-muted">
                                                <?= __('If you don\'t have GST registration, you can skip this step and continue with the verification process.'); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>


                            <button type="button" class="btn btn-primary verify-btn d-none" id="verify_gst_btn" data-verification-type="gst"
                                >

                            </button>


                            <div id="gst_address_options" class="mt-3 d-none">

                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="gst_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> <?= __('GST Verification Details'); ?></h5>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Legal Name'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Gstin Legal Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="gstin_legal_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Trade Name'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Gstin Trade Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="gstin_trade_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Registration Status'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Gstin Registration Status'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="gstin_registration_status"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Taxpayer Type'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Gstin Taxpayer Type'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="gstin_taxpayer_type" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Business Nature'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Gstin Business Nature'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="gstin_business_nature" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Registration Date'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Gstin Registration Date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="gstin_registration_date"
                                        readonly>
                                </div>
                                <div class="col-12 form-group">
                                    <span class="form-label"><?= __('Business Address'); ?></span>
                                    <textarea aria-label="<?= htmlspecialchars(__('Gstin Business Address'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control custom_readonly" id="gstin_business_address" rows="2"
                                        readonly></textarea>
                                </div>
                            </div>
                        </div>

                        <div id="gst_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> <?= __('GST Verification Completed'); ?></h6>
                            <p class="mb-0"><?= __('GST details have been successfully verified and business information retrieved.'); ?></p>
                        </div>

                        <!-- GST Manual Confirmation Section -->
                        <div id="gst_manual_confirmation_section" class="confirmation-section d-none gst-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> <?= __('GST Data Fetched - Please Verify'); ?></h6>
                            <p class="mb-3"><?= __('The GST data has been fetched successfully. Please review the information below and confirm if it matches your records'); ?>:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong><?= __('Note'); ?>:</strong> <?= __('Please verify that the business details match your GST registration.'); ?>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><?= __('Legal Name'); ?>:</strong> <span id="confirm_gst_legal_name_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong><?= __('Trade Name'); ?>:</strong> <span id="confirm_gst_trade_name_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong><?= __('GST Number'); ?>:</strong> <span id="confirm_gst_number_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-gst-btn">
                                    <i class="fa fa-check"></i> <?= __('Confirm - Details Are Correct'); ?>
                                </button>
                                <button type="button" class="btn btn-secondary cancel-gst-btn">
                                    <i class="fa fa-edit"></i> <?= __('Let Me Correct the Details'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        } elseif ($verification_type == 'BANK') {
            ?>

                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> <?= __('Bank Account Verification - Completed'); ?></h3>
                        <p class="text-success"><?= __('Your Bank Account has been successfully verified on'); ?>
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['BANK']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-university text-primary"></i> <?= __('Bank Account Verification'); ?> <?php echo in_array('BANK', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p><?= __('Verify your bank account details for secure transactions'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('IFSC Code'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['ifsc_code'] ?? '')); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('Bank Name'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['bank_name'] ?? '')); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('Account Type'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly"
                                        value="<?php
                                        $account_type_display = '';
                                        if (isset($vendordetail['account_type']) && !empty($vendordetail['account_type'])) {
                                            foreach($accounttype as $acc_type) {
                                                if ($acc_type['genc_detail_code'] == $vendordetail['account_type']) {
                                                    $account_type_display = $acc_type['genc_detail_name'];
                                                    break;
                                                }
                                            }
                                        }
                                        echo htmlspecialchars($account_type_display);
                                        ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('Bank Account Number'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['bank_account_no'] ?? '')); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('Verification Date'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['BANK']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['BANK']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> <?= __('This verification is completed and cannot be edited. Contact the administrator if changes are needed.'); ?>
                                </small>
                            </div>

                        <?php else: ?>

                            <div class="form-group mb-3 w-full">
                                <span class="form-label"><?= __('Account Type'); ?> <span class="text-danger">*</span></span>
                                <select aria-label="<?= htmlspecialchars(__('Account Type'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control account-type-select js-example-basic-single" id="account_type">
                                     <?php
                                        foreach($accounttype as $acc_type) {
                                            $selected = (isset($vendordetail['account_type']) && $vendordetail['account_type'] == $acc_type['genc_detail_code']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($acc_type['genc_detail_code']) . '" ' . $selected . '>' . htmlspecialchars($acc_type['genc_detail_name']) . '</option>';
                                        }

                                    ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <span class="form-label"><?= __('Bank Name'); ?> <span class="text-danger">*</span></span>
                                <input aria-label="<?= htmlspecialchars(__('Enter bank name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control bank-name-input" id="bank_name" placeholder="<?= htmlspecialchars(__('Enter bank name'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['bank_name'] ?? '')); ?>">
                            </div>

                            <div class="form-group mb-3">
                                <span class="form-label"><?= __('IFSC Code'); ?> <span class="text-danger">*</span></span>
                                <div class="input-group">
                                    <input aria-label="<?= htmlspecialchars(__('SBIN0001234'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control ifsc-input text-uppercase" id="ifsc_code" placeholder="<?= htmlspecialchars(__('SBIN0001234'), ENT_QUOTES, 'UTF-8'); ?>"
                                        maxlength="11"
                                        value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['ifsc_code'] ?? '')); ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted"><?= __('Format: ABCD0123456 (4 letters + 0 + 6 digits/letters)'); ?></small>
                            </div>

                            <div class="form-group mb-3">
                                <span class="form-label"><?= __('Bank Account Number'); ?> <span class="text-danger">*</span></span>
                                <div class="input-group">
                                    <input aria-label="<?= htmlspecialchars(__('Enter account number'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control bank-account-input" id="bank_account_no" placeholder="<?= htmlspecialchars(__('Enter account number'), ENT_QUOTES, 'UTF-8'); ?>"
                                        value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['bank_account_no'] ?? '')); ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-secondary btn-sm toggle-edit-btn" id="bank_edit_btn"
                                            data-section="bank">
                                            <?= __('Change Info'); ?>
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_bank_btn_visible" data-verification-type="bank">
                                            <i class="fa fa-search"></i> <?= __('Verify Bank Account'); ?>
                                        </button>
                                    </div>
                                </div>
                                <?php if (!in_array('BANK', $required_document_verifications)): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm skip-step-btn" data-step-type="bank">
                                                <?= __('Skip This Step'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <small class="text-muted">
                                                <?= __('If you don\'t have bank account details ready, you can skip this step and continue with the verification process.'); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>


                            <button type="button" class="btn btn-primary verify-btn d-none" id="verify_bank_btn" data-verification-type="bank"
                                >
                                <i class="fa fa-search"></i> <?= __('Verify Bank Account'); ?>
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="bank_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> <?= __('Bank Verification Details'); ?></h5>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('IFSC Code'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Bank Verified Ifsc'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="bank_verified_ifsc" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Account Number'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Bank Verified Account'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="bank_verified_account" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Beneficiary Name'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Bank Verified Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="bank_verified_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Verification Status'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Bank Verified Status'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="bank_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="bank_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> <?= __('Bank Verification Completed'); ?></h6>
                            <p class="mb-0"><?= __('Bank account details have been successfully verified.'); ?></p>
                        </div>

                        <!-- Manual Confirmation Section -->
                        <div id="bank_manual_confirmation_section" class="confirmation-section d-none bank-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> <?= __('Bank Data Fetched - Please Verify'); ?></h6>
                            <p class="mb-3"><?= __('The bank account details have been fetched successfully. Please review the information below and confirm if it matches your records'); ?>:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong><?= __('Note'); ?>:</strong> <?= __('Please verify that the account holder name matches your business registration details.'); ?>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><?= __('IFSC Code'); ?>:</strong> <span id="confirm_ifsc_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong><?= __('Account Number'); ?>:</strong> <span id="confirm_account_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong><?= __('Account Holder Name'); ?>:</strong> <span id="confirm_name_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-bank-btn">
                                    <i class="fa fa-check"></i> <?= __('Confirm - Details Are Correct'); ?>
                                </button>
                                <button type="button" class="btn btn-danger  cancel-bank-btn mt-3">
                                    <i class="fa fa-edit"></i> <?= __('Let Me Correct the Details'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        } elseif ($verification_type == 'MSME') {
            ?>

                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> <?= __('MSME/Udyam Registration Verification - Completed'); ?></h3>
                        <p class="text-success"><?= __('Your MSME has been successfully verified on'); ?>
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['MSME']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-check text-primary"></i> <?= __('MSME/Udyam Registration Verification'); ?> <?php echo in_array('MSME', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p><?= __('Verify your MSME/Udyam registration details'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('MSME/Udyam Registration Number'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['msme_number'] ?? '')); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('Verification Date'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['MSME']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['MSME']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> <?= __('This verification is completed and cannot be edited. Contact the administrator if changes are needed.'); ?>
                                </small>
                            </div>

                        <?php else: ?>

                            <div class="form-group">
                                <span class="form-label"><?= __('MSME/Udyam Registration Number'); ?></span>
                                <div class="input-group">
                                    <input aria-label="<?= htmlspecialchars(__('UDYAM-XX-00-0000000'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control msme-input text-uppercase" id="msme_number" placeholder="<?= htmlspecialchars(__('UDYAM-XX-00-0000000'), ENT_QUOTES, 'UTF-8'); ?>"
                                        maxlength="19"
                                        value="<?php echo dynamicDataDecode($vendordetail['msme_number'] ?? ''); ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted"><?= __('Format: UDYAM-XX-00-0000000'); ?></small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-secondary btn-sm toggle-edit-btn" id="msme_edit_btn"
                                            data-section="msme">
                                            <?= __('Change Info'); ?>
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_msme_btn_visible" data-verification-type="msme">
                                            <i class="fa fa-search"></i> <?= __('Verify MSME'); ?>
                                        </button>
                                    </div>
                                </div>
                                <?php if (!in_array('MSME', $required_document_verifications)): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm skip-step-btn" data-step-type="msme">
                                                <?= __('Skip This Step'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <small class="text-muted">
                                                <?= __('If you don\'t have MSME/Udyam registration, you can skip this step and continue with the verification process.'); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>


                            <button type="button" class="btn btn-primary verify-btn d-none" id="verify_msme_btn" data-verification-type="msme"
                                >
                                <i class="fa fa-search"></i> <?= __('Verify MSME'); ?>
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="msme_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> <?= __('MSME Verification Details'); ?></h5>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Enterprise Name'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Msme Verified Enterprise Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="msme_verified_enterprise_name"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Major Activity'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Msme Verified Major Activity'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="msme_verified_major_activity"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Organization Type'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Msme Verified Organization Type'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="msme_verified_organization_type"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Social Category'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Msme Verified Social Category'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="msme_verified_social_category"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Commencement Date'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Msme Verified Commencement Date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="msme_verified_commencement_date"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('DIC Name'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Msme Verified Dic Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="msme_verified_dic_name"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Registration Date'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Msme Verified Registration Date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="msme_verified_registration_date"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label"><?= __('Status'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Msme Verified Status'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="msme_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="msme_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> <?= __('MSME Verification Completed'); ?></h6>
                            <p class="mb-0"><?= __('MSME registration details have been successfully verified.'); ?></p>
                        </div>

                        <!-- MSME Manual Confirmation Section -->
                        <div id="msme_manual_confirmation_section" class="confirmation-section d-none msme-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> <?= __('MSME Data Fetched - Please Verify'); ?></h6>
                            <p class="mb-3"><?= __('The MSME registration data has been fetched successfully. Please review the information below and confirm if it matches your records'); ?>:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong><?= __('Note'); ?>:</strong> <?= __('Please verify that the enterprise details match your MSME registration.'); ?>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><?= __('Enterprise Name'); ?>:</strong> <span id="confirm_msme_enterprise_name_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong><?= __('Organization Type'); ?>:</strong> <span id="confirm_msme_org_type_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong><?= __('MSME Registration Number'); ?>:</strong> <span id="confirm_msme_reg_number_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-msme-btn">
                                    <i class="fa fa-check"></i> <?= __('Confirm - Details Are Correct'); ?>
                                </button>
                                <button type="button" class="btn btn-secondary cancel-msme-btn">
                                    <i class="fa fa-edit"></i> <?= __('Let Me Correct the Details'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        } elseif ($verification_type == 'TAN') {
            ?>

                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> <?= __('TAN Number Verification - Completed'); ?></h3>
                        <p class="text-success"><?= __('Your TAN has been successfully verified on'); ?>
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['TAN']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-file-text text-primary"></i> <?= __('TAN Number Verification'); ?> <?php echo in_array('TAN', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p><?= __('Verify your Tax Deduction Account Number'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('TAN Number'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars(dynamicDataDecode($vendordetail['tan_number'] ?? '')); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold"><?= __('Verification Date'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['TAN']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['TAN']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> <?= __('This verification is completed and cannot be edited. Contact the administrator if changes are needed.'); ?>
                                </small>
                            </div>

                        <?php else: ?>

                            <div class="form-group">
                                <span class="form-label"><?= __('TAN Number'); ?></span>
                                <div class="input-group">
                                    <input aria-label="<?= htmlspecialchars(__('ABCD12345E'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control tan-input text-uppercase" id="tan_number" placeholder="<?= htmlspecialchars(__('ABCD12345E'), ENT_QUOTES, 'UTF-8'); ?>"
                                        maxlength="10"
                                        value="<?php echo dynamicDataDecode($vendordetail['tan_number'] ?? ''); ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted"><?= __('Format: ABCD12345E (4 letters + 5 digits + 1 letter)'); ?></small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-secondary btn-sm toggle-edit-btn" id="tan_edit_btn"
                                            data-section="tan">
                                            <?= __('Change Info'); ?>
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_tan_btn_visible" data-verification-type="tan">
                                            <i class="fa fa-search"></i> <?= __('Verify TAN'); ?>
                                        </button>
                                    </div>
                                </div>
                                <?php if (!in_array('TAN', $required_document_verifications)): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm skip-step-btn" data-step-type="tan">
                                                <?= __('Skip This Step'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <small class="text-muted">
                                                <?= __('If you don\'t have a TAN number, you can skip this step and continue with the verification process.'); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>


                            <button type="button" class="btn btn-primary verify-btn d-none" id="verify_tan_btn" data-verification-type="tan"
                                >
                                <i class="fa fa-search"></i> <?= __('Verify TAN'); ?>
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="tan_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> <?= __('TAN Verification Details'); ?></h5>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <span class="form-label"><?= __('Company Name'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Tan Verified Company'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="tan_verified_company" readonly>

                                </div>
                                <div class="col-12 form-group">
                                    <span class="form-label"><?= __('Verification Status'); ?></span>
                                    <input aria-label="<?= htmlspecialchars(__('Tan Verified Status'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control custom_readonly" id="tan_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="tan_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> <?= __('TAN Verification Completed'); ?></h6>
                            <p class="mb-0"><?= __('TAN details have been successfully verified.'); ?></p>
                        </div>

                        <!-- TAN Manual Confirmation Section -->
                        <div id="tan_manual_confirmation_section" class="confirmation-section d-none tan-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> <?= __('TAN Data Fetched - Please Verify'); ?></h6>
                            <p class="mb-3"><?= __('The TAN data has been fetched successfully. Please review the information below and confirm if it matches your records'); ?>:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong><?= __('Note'); ?>:</strong> <?= __('Please verify that the TAN details match your tax registration.'); ?>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><?= __('Your Entered Name'); ?>:</strong> <span id="confirm_user_company_name_tan_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong><?= __('TAN Registered Name'); ?>:</strong> <span id="confirm_tan_company_name_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong><?= __('TAN Number'); ?>:</strong> <span id="confirm_tan_number_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-tan-btn">
                                    <i class="fa fa-check"></i> <?= __('Confirm - Details Are Correct'); ?>
                                </button>
                                <button type="button" class="btn btn-secondary cancel-tan-btn">
                                    <i class="fa fa-edit"></i> <?= __('Let Me Correct the Details'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        }
        ?>
        </div>
        <?php
        $step_counter++;
    }
    ?>


    <div class="navigation-buttons">
        <button type="button" class="btn btn-secondary prev-step-btn d-none" id="prevBtn" >
            <i class="fa fa-arrow-left"></i> <?= __('Previous'); ?>
        </button>

        <div>
            <button type="button" class="btn btn-primary next-step-btn" id="nextBtn">
                <?= __('Next'); ?> <i class="fa fa-arrow-right"></i>
            </button>
            <button type="button" class="btn btn-success complete-verification-btn d-none" id="completeBtn"
                >
                <i class="fa fa-check"></i> <?= __('Complete Verification'); ?>
            </button>
        </div>
    </div>
</div>

<script src="assets/js/custom_insert_vendor_and_vendor_registers_verifications.js"></script>
