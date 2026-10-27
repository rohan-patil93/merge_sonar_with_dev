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
                        <h3><i class="fa fa-check-circle text-success"></i> PAN Card Verification - Completed</h3>
                        <p class="text-success">Your PAN has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['PAN']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-id-card text-primary"></i> PAN Card Verification <?php echo in_array('PAN', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p>Enter your PAN card number to verify your identity and business details</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">PAN Number</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['pan_no'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">Company Name</span>
                                    <input aria-label="Company Name" type="text" id="company_name" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['company'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>
                        <?php else: ?>

                            <div class="form-group w-full">
                                <span class="form-label">Company Type <span class="text-danger">*</span></span>
                                <select aria-label="Company Type" class="form-control js-example-basic-single" id="company_type" name="company_type">
                                    <option value="">Select Company Type</option>
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
                                <span class="form-label">Email <span class="text-danger">*</span></span>
                                <input aria-label="Enter company email" type="email" class="form-control company-email-input" id="company_email" placeholder="Enter company email" value="<?php echo $vendordetail['email'] ?? ''; ?>" >
                            </div>

                            <div class="form-group <?php echo $vendordetail['company']!=""?"event_disabled":""; ?>">
                                <span class="form-label">Company Name <span class="text-danger">*</span></span>
                                <input aria-label="Enter company name" type="text" class="form-control company-name-input"
                                    id="company_name_2" placeholder="Enter company name"
                                    value="<?php echo $vendordetail['company'] ?? ''; ?>" >
                                <input type="hidden" id="company_check_status" value="">
                                <input type="hidden" id="verified_company_name" value="">
                                <span class="text-danger f-12 d-none" id="allreadycompanyname" >
                                    Company Name Already Register
                                </span>
                            </div>

                            <div class="form-group">
                                <span class="form-label">PAN Number <span class="text-danger">*</span></span>
                                <div class="input-group">
                                    <input aria-label="ABCDE1234F" type="text" class="form-control pan-input text-uppercase" id="pan_number" placeholder="ABCDE1234F"
                                        maxlength="10"
                                        value="<?php echo $vendordetail['pan_no'] ?? ''; ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">Format: ABCDE1234F (5 letters, 4 digits, 1 letter)</small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-secondary btn-sm toggle-edit-btn" id="pan_edit_btn"
                                            data-section="pan">
                                            Change Info
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_pan_btn" data-verification-type="pan">
                                            <i class="fa fa-search"></i> Verify PAN
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
                            <h5><i class="fa fa-info-circle"></i> PAN Verification Details</h5>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <span class="form-label">Verified Name</span>
                                    <input aria-label="Pan Verified Name" type="text" class="form-control custom_readonly" id="pan_verified_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">PAN Type</span>
                                    <input aria-label="Pan Verified Type" type="text" class="form-control custom_readonly" id="pan_verified_type" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Status</span>
                                    <input aria-label="Pan Verified Status" type="text" class="form-control custom_readonly" id="pan_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="pan_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> PAN Verification Completed</h6>
                            <p class="mb-0">PAN details have been successfully verified and match your records.</p>
                        </div>

                        <!-- PAN Company Name Confirmation Section -->
                        <div id="pan_company_confirmation_section" class="confirmation-section d-none pan-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> Data Fetched Successfully - Please Verify</h6>
                            <p class="mb-3">The PAN data has been fetched successfully, but there seems to be a mismatch in the company name. Please check the details below and confirm if they are correct:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong>Note:</strong> The system has detected different company names. Please verify which one is correct for your business registration.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Your Entered Name:</strong> <span id="confirm_user_company_name_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>PAN Registered Name:</strong> <span id="confirm_pan_company_name_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>PAN Number:</strong> <span id="confirm_pan_number_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-pan-company-btn">
                                    <i class="fa fa-check"></i> Confirm - Use PAN Registered Name
                                </button>
                                <button type="button" class="btn btn-secondary cancel-pan-company-btn">
                                    <i class="fa fa-edit"></i> Let Me Correct the Details
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
                        <h3><i class="fa fa-check-circle text-success"></i> GST Number Verification - Completed</h3>
                        <p class="text-success">Your GST has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['GSTIN']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-building text-primary"></i> GST Number Verification <?php echo in_array('GSTIN', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p>Verify your GST registration details and business information</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">GST Number</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['gstin'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">Verification Date</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['GSTIN']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['GSTIN']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>

                        <?php else: ?>

                            <div class="form-group">
                                <span class="form-label">GST Number <span class="text-danger">*</span></span>
                                <div class="input-group">
                                    <input aria-label="22AAAAA0000A1Z5" type="text" class="form-control gst-input text-uppercase" id="gstin" placeholder="22AAAAA0000A1Z5"
                                        maxlength="15"
                                        value="<?php echo $vendordetail['gstin'] ?? ''; ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    Format: 15 characters (2 digits + 10 PAN + 1 digit + 1 letter + 1 digit/letter)
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-secondary btn-sm toggle-edit-btn" id="gstin_edit_btn"
                                            data-section="gstin">
                                            Change Info
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_gst_btn_visible" data-verification-type="gst">
                                            <i class="fa fa-search"></i> Verify GST
                                        </button>
                                    </div>
                                </div>
                                <?php if (!in_array('GSTIN', $required_document_verifications)): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm skip-step-btn" data-step-type="gstin">
                                                Skip This Step
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <small class="text-muted">
                                                If you don't have GST registration, you can skip this step and continue with the
                                                verification process.
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
                            <h5><i class="fa fa-info-circle"></i> GST Verification Details</h5>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <span class="form-label">Legal Name</span>
                                    <input aria-label="Gstin Legal Name" type="text" class="form-control custom_readonly" id="gstin_legal_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Trade Name</span>
                                    <input aria-label="Gstin Trade Name" type="text" class="form-control custom_readonly" id="gstin_trade_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Registration Status</span>
                                    <input aria-label="Gstin Registration Status" type="text" class="form-control custom_readonly" id="gstin_registration_status"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Taxpayer Type</span>
                                    <input aria-label="Gstin Taxpayer Type" type="text" class="form-control custom_readonly" id="gstin_taxpayer_type" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Business Nature</span>
                                    <input aria-label="Gstin Business Nature" type="text" class="form-control custom_readonly" id="gstin_business_nature" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Registration Date</span>
                                    <input aria-label="Gstin Registration Date" type="text" class="form-control custom_readonly" id="gstin_registration_date"
                                        readonly>
                                </div>
                                <div class="col-12 form-group">
                                    <span class="form-label">Business Address</span>
                                    <textarea aria-label="Gstin Business Address" class="form-control custom_readonly" id="gstin_business_address" rows="2"
                                        readonly></textarea>
                                </div>
                            </div>
                        </div>

                        <div id="gst_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> GST Verification Completed</h6>
                            <p class="mb-0">GST details have been successfully verified and business information retrieved.</p>
                        </div>

                        <!-- GST Manual Confirmation Section -->
                        <div id="gst_manual_confirmation_section" class="confirmation-section d-none gst-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> GST Data Fetched - Please Verify</h6>
                            <p class="mb-3">The GST data has been fetched successfully. Please review the information below and confirm if it matches your records:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong>Note:</strong> Please verify that the business details match your GST registration.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Legal Name:</strong> <span id="confirm_gst_legal_name_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Trade Name:</strong> <span id="confirm_gst_trade_name_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>GST Number:</strong> <span id="confirm_gst_number_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-gst-btn">
                                    <i class="fa fa-check"></i> Confirm - Details Are Correct
                                </button>
                                <button type="button" class="btn btn-secondary cancel-gst-btn">
                                    <i class="fa fa-edit"></i> Let Me Correct the Details
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
                        <h3><i class="fa fa-check-circle text-success"></i> Bank Account Verification - Completed</h3>
                        <p class="text-success">Your Bank Account has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['BANK']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-university text-primary"></i> Bank Account Verification <?php echo in_array('BANK', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p>Verify your bank account details for secure transactions</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">IFSC Code</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['ifsc_code'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">Bank Name</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['bank_name'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">Account Type</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly"
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
                                    <span class="form-label fw-bold">Bank Account Number</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['bank_account_no'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">Verification Date</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['BANK']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['BANK']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>

                        <?php else: ?>

                            <div class="form-group mb-3 w-full">
                                <span class="form-label">Account Type <span class="text-danger">*</span></span>
                                <select aria-label="Account Type" class="form-control account-type-select js-example-basic-single" id="account_type">
                                     <?php
                                        foreach($accounttype as $acc_type) {
                                            $selected = (isset($vendordetail['account_type']) && $vendordetail['account_type'] == $acc_type['genc_detail_code']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($acc_type['genc_detail_code']) . '" ' . $selected . '>' . htmlspecialchars($acc_type['genc_detail_name']) . '</option>';
                                        }

                                    ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <span class="form-label">Bank Name <span class="text-danger">*</span></span>
                                <input aria-label="Enter bank name" type="text" class="form-control bank-name-input" id="bank_name" placeholder="Enter bank name"
                                    value="<?php echo htmlspecialchars($vendordetail['bank_name'] ?? ''); ?>">
                            </div>

                            <div class="form-group mb-3">
                                <span class="form-label">IFSC Code <span class="text-danger">*</span></span>
                                <div class="input-group">
                                    <input aria-label="SBIN0001234" type="text" class="form-control ifsc-input text-uppercase" id="ifsc_code" placeholder="SBIN0001234"
                                        maxlength="11"
                                        value="<?php echo htmlspecialchars($vendordetail['ifsc_code'] ?? ''); ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">Format: ABCD0123456 (4 letters + 0 + 6 digits/letters)</small>
                            </div>

                            <div class="form-group mb-3">
                                <span class="form-label">Bank Account Number <span class="text-danger">*</span></span>
                                <div class="input-group">
                                    <input aria-label="Enter account number" type="text" class="form-control bank-account-input" id="bank_account_no" placeholder="Enter account number"
                                        value="<?php echo htmlspecialchars($vendordetail['bank_account_no'] ?? ''); ?>">
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
                                            Change Info
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_bank_btn_visible" data-verification-type="bank">
                                            <i class="fa fa-search"></i> Verify Bank Account
                                        </button>
                                    </div>
                                </div>
                                <?php if (!in_array('BANK', $required_document_verifications)): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm skip-step-btn" data-step-type="bank">
                                                Skip This Step
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <small class="text-muted">
                                                If you don't have bank account details ready, you can skip this step and continue with
                                                the verification process.
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>


                            <button type="button" class="btn btn-primary verify-btn d-none" id="verify_bank_btn" data-verification-type="bank"
                                >
                                <i class="fa fa-search"></i> Verify Bank Account
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="bank_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> Bank Verification Details</h5>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <span class="form-label">IFSC Code</span>
                                    <input aria-label="Bank Verified Ifsc" type="text" class="form-control custom_readonly" id="bank_verified_ifsc" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Account Number</span>
                                    <input aria-label="Bank Verified Account" type="text" class="form-control custom_readonly" id="bank_verified_account" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Beneficiary Name</span>
                                    <input aria-label="Bank Verified Name" type="text" class="form-control custom_readonly" id="bank_verified_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Verification Status</span>
                                    <input aria-label="Bank Verified Status" type="text" class="form-control custom_readonly" id="bank_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="bank_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> Bank Verification Completed</h6>
                            <p class="mb-0">Bank account details have been successfully verified.</p>
                        </div>

                        <!-- Manual Confirmation Section -->
                        <div id="bank_manual_confirmation_section" class="confirmation-section d-none bank-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> Bank Data Fetched - Please Verify</h6>
                            <p class="mb-3">The bank account details have been fetched successfully. Please review the information below and confirm if it matches your records:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong>Note:</strong> Please verify that the account holder name matches your business registration details.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>IFSC Code:</strong> <span id="confirm_ifsc_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Account Number:</strong> <span id="confirm_account_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>Account Holder Name:</strong> <span id="confirm_name_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-bank-btn">
                                    <i class="fa fa-check"></i> Confirm - Details Are Correct
                                </button>
                                <button type="button" class="btn btn-danger  cancel-bank-btn mt-3">
                                    <i class="fa fa-edit"></i> Let Me Correct the Details
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
                        <h3><i class="fa fa-check-circle text-success"></i> MSME/Udyam Registration Verification - Completed</h3>
                        <p class="text-success">Your MSME has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['MSME']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-check text-primary"></i> MSME/Udyam Registration Verification <?php echo in_array('MSME', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p>Verify your MSME/Udyam registration details</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">MSME/Udyam Registration Number</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['msme_number'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">Verification Date</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['MSME']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['MSME']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>

                        <?php else: ?>

                            <div class="form-group">
                                <span class="form-label">MSME/Udyam Registration Number</span>
                                <div class="input-group">
                                    <input aria-label="UDYAM-XX-00-0000000" type="text" class="form-control msme-input text-uppercase" id="msme_number" placeholder="UDYAM-XX-00-0000000"
                                        maxlength="19"
                                        value="<?php echo $vendordetail['msme_number'] ?? ''; ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">Format: UDYAM-XX-00-0000000</small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-secondary btn-sm toggle-edit-btn" id="msme_edit_btn"
                                            data-section="msme">
                                            Change Info
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_msme_btn_visible" data-verification-type="msme">
                                            <i class="fa fa-search"></i> Verify MSME
                                        </button>
                                    </div>
                                </div>
                                <?php if (!in_array('MSME', $required_document_verifications)): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm skip-step-btn" data-step-type="msme">
                                                Skip This Step
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <small class="text-muted">
                                                If you don't have MSME/Udyam registration, you can skip this step and continue with the
                                                verification process.
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>


                            <button type="button" class="btn btn-primary verify-btn d-none" id="verify_msme_btn" data-verification-type="msme"
                                >
                                <i class="fa fa-search"></i> Verify MSME
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="msme_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> MSME Verification Details</h5>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <span class="form-label">Enterprise Name</span>
                                    <input aria-label="Msme Verified Enterprise Name" type="text" class="form-control custom_readonly" id="msme_verified_enterprise_name"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Major Activity</span>
                                    <input aria-label="Msme Verified Major Activity" type="text" class="form-control custom_readonly" id="msme_verified_major_activity"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Organization Type</span>
                                    <input aria-label="Msme Verified Organization Type" type="text" class="form-control custom_readonly" id="msme_verified_organization_type"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Social Category</span>
                                    <input aria-label="Msme Verified Social Category" type="text" class="form-control custom_readonly" id="msme_verified_social_category"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Commencement Date</span>
                                    <input aria-label="Msme Verified Commencement Date" type="text" class="form-control custom_readonly" id="msme_verified_commencement_date"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">DIC Name</span>
                                    <input aria-label="Msme Verified Dic Name" type="text" class="form-control custom_readonly" id="msme_verified_dic_name"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Registration Date</span>
                                    <input aria-label="Msme Verified Registration Date" type="text" class="form-control custom_readonly" id="msme_verified_registration_date"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <span class="form-label">Status</span>
                                    <input aria-label="Msme Verified Status" type="text" class="form-control custom_readonly" id="msme_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="msme_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> MSME Verification Completed</h6>
                            <p class="mb-0">MSME registration details have been successfully verified.</p>
                        </div>

                        <!-- MSME Manual Confirmation Section -->
                        <div id="msme_manual_confirmation_section" class="confirmation-section d-none msme-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> MSME Data Fetched - Please Verify</h6>
                            <p class="mb-3">The MSME registration data has been fetched successfully. Please review the information below and confirm if it matches your records:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong>Note:</strong> Please verify that the enterprise details match your MSME registration.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Enterprise Name:</strong> <span id="confirm_msme_enterprise_name_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Organization Type:</strong> <span id="confirm_msme_org_type_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>MSME Registration Number:</strong> <span id="confirm_msme_reg_number_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-msme-btn">
                                    <i class="fa fa-check"></i> Confirm - Details Are Correct
                                </button>
                                <button type="button" class="btn btn-secondary cancel-msme-btn">
                                    <i class="fa fa-edit"></i> Let Me Correct the Details
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
                        <h3><i class="fa fa-check-circle text-success"></i> TAN Number Verification - Completed</h3>
                        <p class="text-success">Your TAN has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['TAN']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-file-text text-primary"></i> TAN Number Verification <?php echo in_array('TAN', $required_document_verifications) ? '<span class="text-danger">*</span>' : ''; ?></h3>
                        <p>Verify your Tax Deduction Account Number</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>

                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">TAN Number</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['tan_number'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <span class="form-label fw-bold">Verification Date</span>
                                    <input aria-label="Input field" type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['TAN']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['TAN']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>

                        <?php else: ?>

                            <div class="form-group">
                                <span class="form-label">TAN Number</span>
                                <div class="input-group">
                                    <input aria-label="ABCD12345E" type="text" class="form-control tan-input text-uppercase" id="tan_number" placeholder="ABCD12345E"
                                        maxlength="10"
                                        value="<?php echo $vendordetail['tan_number'] ?? ''; ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">Format: ABCD12345E (4 letters + 5 digits + 1 letter)</small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-secondary btn-sm toggle-edit-btn" id="tan_edit_btn"
                                            data-section="tan">
                                            Change Info
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-primary btn-sm verify-btn" id="verify_tan_btn_visible" data-verification-type="tan">
                                            <i class="fa fa-search"></i> Verify TAN
                                        </button>
                                    </div>
                                </div>
                                <?php if (!in_array('TAN', $required_document_verifications)): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm skip-step-btn" data-step-type="tan">
                                                Skip This Step
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <small class="text-muted">
                                                If you don't have a TAN number, you can skip this step and continue with the
                                                verification process.
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>


                            <button type="button" class="btn btn-primary verify-btn d-none" id="verify_tan_btn" data-verification-type="tan"
                                >
                                <i class="fa fa-search"></i> Verify TAN
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="tan_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> TAN Verification Details</h5>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <span class="form-label">Company Name</span>
                                    <input aria-label="Tan Verified Company" type="text" class="form-control custom_readonly" id="tan_verified_company" readonly>

                                </div>
                                <div class="col-12 form-group">
                                    <span class="form-label">Verification Status</span>
                                    <input aria-label="Tan Verified Status" type="text" class="form-control custom_readonly" id="tan_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="tan_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> TAN Verification Completed</h6>
                            <p class="mb-0">TAN details have been successfully verified.</p>
                        </div>

                        <!-- TAN Manual Confirmation Section -->
                        <div id="tan_manual_confirmation_section" class="confirmation-section d-none tan-manual-confirmation">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> TAN Data Fetched - Please Verify</h6>
                            <p class="mb-3">The TAN data has been fetched successfully. Please review the information below and confirm if it matches your records:</p>

                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i> <strong>Note:</strong> Please verify that the TAN details match your tax registration.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Your Entered Name:</strong> <span id="confirm_user_company_name_tan_display"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>TAN Registered Name:</strong> <span id="confirm_tan_company_name_display"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>TAN Number:</strong> <span id="confirm_tan_number_display"></span>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-success mr-2 confirm-tan-btn">
                                    <i class="fa fa-check"></i> Confirm - Details Are Correct
                                </button>
                                <button type="button" class="btn btn-secondary cancel-tan-btn">
                                    <i class="fa fa-edit"></i> Let Me Correct the Details
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
            <i class="fa fa-arrow-left"></i> Previous
        </button>

        <div>
            <button type="button" class="btn btn-primary next-step-btn" id="nextBtn">
                Next <i class="fa fa-arrow-right"></i>
            </button>
            <button type="button" class="btn btn-success complete-verification-btn d-none" id="completeBtn"
                >
                <i class="fa fa-check"></i> Complete Verification
            </button>
        </div>
    </div>
</div>

<script src="assets/js/custom_insert_vendor_and_vendor_registers_verifications.js"></script>
