<?php
if ($_POST['ajaxcall'] == "Y") {
    include('../includes/session.php');
    include('../dbconfig.php');
    include('../comm.php');
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
?>

<style>
    .verification-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        padding: 0 20px;
    }

    .step-indicator .step {
        flex: 1;
        text-align: center;
        position: relative;
        padding: 10px;
        cursor: pointer;
    }

    .step-indicator .step:not(:last-child):after {
        content: '';
        position: absolute;
        top: 25px;
        right: -50%;
        width: 100%;
        height: 2px;
        background: #ddd;
        z-index: 1;
    }

    .step-indicator .step.completed:not(:last-child):after {
        background: #28a745;
    }

    .step-indicator .step .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ddd;
        color: #666;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 5px;
        position: relative;
        z-index: 2;
    }

    .step-indicator .step.active .step-number {
        background: #007bff;
        color: white;
    }

    .step-indicator .step.completed .step-number {
        background: #28a745;
        color: white;
    }

    .step-indicator .step .step-title {
        font-size: 12px;
        color: #666;
        font-weight: 500;
    }

    .step-indicator .step.active .step-title {
        color: #007bff;
        font-weight: 600;
    }

    .step-indicator .step.completed .step-title {
        color: #28a745;
        font-weight: 600;
    }

    .verification-step {
        display: none;
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .verification-step.active {
        display: block;
    }

    .step-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #dee2e6;
    }

    .input-group .status-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
    }

    .verification-fields {
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 5px;
        border-left: 4px solid #007bff;
    }

    .confirmation-section {
        display: none;
        margin-top: 20px;
        padding: 15px;
        background: #d4edda;
        border-radius: 5px;
        border-left: 4px solid #28a745;
    }

    .navigation-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .custom_readonly {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }

    .has-success .form-control {
        border-color: #28a745;
    }

    .has-error .form-control {
        border-color: #dc3545;
    }

    #notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }

    .verification-step.completed {
        background: #f8f9fa;
        border-left: 4px solid #28a745;
    }

    .verification-step.completed .step-header h3 {
        color: #28a745;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
        border-radius: 8px;
        padding: 20px;
    }

    .step.completed .step-number {
        background: #28a745 !important;
        color: white !important;
    }

    .step.completed .step-title {
        color: #28a745 !important;
        font-weight: 600 !important;
    }
</style>

<div class="verification-container">
    <!-- Notification Container -->
    <div id="notification-container"></div>
    <!-- Step Indicator -->
    <div class="step-indicator">
        <?php

        $visible_document_verifications = ['PAN', 'GSTIN', 'BANK', 'TAN', 'MSME'];
        $step_mapping = [
            'PAN' => ['title' => 'PAN Verification', 'icon' => 'fa-id-card'],
            'GSTIN' => ['title' => 'GST Verification', 'icon' => 'fa-building'],
            'BANK' => ['title' => 'Bank Verification', 'icon' => 'fa-university'],
            'TAN' => ['title' => 'TAN Verification', 'icon' => 'fa-file-text'],
            'MSME' => ['title' => 'MSME Verification', 'icon' => 'fa-industry']
        ];

        // Parse vendor verifications to check status
        $vendor_verifications = json_decode($vendordetail['vendor_verifications'], true);

        $step_counter = 1;
        $first_incomplete_step = null;

        foreach ($visible_document_verifications as $verification_type) {
            if (isset($step_mapping[$verification_type])) {
                // Check if this verification is already completed
                $is_verified = isset($vendor_verifications[$verification_type]['status']) &&
                    $vendor_verifications[$verification_type]['status'] == 'Y';

                // Determine step class
                $step_class = '';
                if ($is_verified) {
                    $step_class = 'completed';
                } elseif ($first_incomplete_step === null) {
                    $step_class = 'active';
                    $first_incomplete_step = $step_counter;
                }

                echo '<div class="step ' . $step_class . '" onclick="goToStep(' . $step_counter . ')">';
                echo '<div class="step-number">';
                if ($is_verified) {
                    echo '<i class="fa fa-check"></i>';
                } else {
                    echo $step_counter;
                }
                echo '</div>';
                echo '<div class="step-title">' . $step_mapping[$verification_type]['title'] . '</div>';
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

        if ($verification_type == 'PAN') {
            ?>
            <!-- Step <?php echo $step_counter; ?>: PAN Verification -->
            <div id="step-<?php echo $step_counter; ?>" class="verification-step <?php echo $active_class; ?>">
                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> PAN Card Verification - Completed</h3>
                        <p class="text-success">Your PAN has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['PAN']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-id-card text-primary"></i> PAN Card Verification</h3>
                        <p>Enter your PAN card number to verify your identity and business details</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>
                            <!-- Verified PAN Inputs (Non-editable) -->
                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">PAN Number</label>
                                    <input type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['pan_no'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Company Name</label>
                                    <input type="text" id="company_name" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['company'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>
                        <?php else: ?>
                            <!-- PAN Input Form (Editable) -->
                            <div class="form-group">
                                <label class="form-label">Company Name</label>
                                <input type="text" onblur="checkCompanyNameAllReadyAvilableOrNot()" class="form-control"
                                    id="company_name" placeholder="Enter company name"
                                    value="<?php echo $vendordetail['company'] ?? ''; ?>">
                                <span class="text-danger f-12" id="allreadycompanyname" style="display:none">
                                    Company Name Already Register
                                </span>
                            </div>

                            <div class="form-group">
                                <label class="form-label">PAN Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pan_number" placeholder="ABCDE1234F"
                                        oninput="formatPANInput(this)" maxlength="10" style="text-transform: uppercase;"
                                        value="<?php echo $vendordetail['pan_no'] ?? ''; ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">Format: ABCDE1234F (5 letters, 4 digits, 1 letter)</small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <button type="button" class="btn btn-secondary btn-sm" id="pan_edit_btn"
                                            onclick="toggleEdit('pan')">
                                            Change Info
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden verify button for internal use -->
                            <button type="button" class="btn btn-primary" id="verify_pan_btn" onclick="verifyPAN()"
                                style="display: none;">
                                <i class="fa fa-search"></i> Verify PAN
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="pan_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> PAN Verification Details</h5>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label class="form-label">Verified Name</label>
                                    <input type="text" class="form-control custom_readonly" id="pan_verified_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">PAN Type</label>
                                    <input type="text" class="form-control custom_readonly" id="pan_verified_type" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control custom_readonly" id="pan_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="pan_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> PAN Verification Completed</h6>
                            <p class="mb-0">PAN details have been successfully verified and match your records.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $step_counter++;
        } elseif ($verification_type == 'GSTIN') {
            ?>
            <!-- Step <?php echo $step_counter; ?>: GST Verification -->
            <div id="step-<?php echo $step_counter; ?>" class="verification-step">
                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> GST Number Verification - Completed</h3>
                        <p class="text-success">Your GST has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['GSTIN']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-building text-primary"></i> GST Number Verification</h3>
                        <p>Verify your GST registration details and business information</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>
                            <!-- Verified GST Inputs (Non-editable) -->
                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">GST Number</label>
                                    <input type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['gstin'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Verification Date</label>
                                    <input type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['GSTIN']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['GSTIN']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>

                        <?php else: ?>
                            <!-- GST Input Form (Editable) -->
                            <div class="form-group">
                                <label class="form-label">GST Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="gstin" placeholder="22AAAAA0000A1Z5"
                                        oninput="formatGSTInput(this)" maxlength="15" style="text-transform: uppercase;"
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
                                    <div
                                        class="col-lg-<?php echo in_array('GSTIN', $required_document_verifications) ? '12' : '6'; ?>">
                                        <button type="button" class="btn btn-secondary btn-sm" id="gst_edit_btn"
                                            onclick="toggleEdit('gst')">
                                            Change Info
                                        </button>
                                    </div>
                                    <?php if (!in_array('GSTIN', $required_document_verifications)): ?>
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm" onclick="skipStep('gst')">
                                                Skip This Step
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!in_array('GSTIN', $required_document_verifications)): ?>
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

                            <!-- Hidden verify button for internal use -->
                            <button type="button" class="btn btn-primary" id="verify_gst_btn" onclick="verifyGST()"
                                style="display: none;">
                                <i class="fa fa-search"></i> Verify GST
                            </button>

                            <!-- GST Address Options -->
                            <div id="gst_address_options" style="display: none;" class="mt-3">
                                <!-- Address options will be populated by JavaScript -->
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="gst_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> GST Verification Details</h5>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <label class="form-label">Legal Name</label>
                                    <input type="text" class="form-control custom_readonly" id="gstin_legal_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Trade Name</label>
                                    <input type="text" class="form-control custom_readonly" id="gstin_trade_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Registration Status</label>
                                    <input type="text" class="form-control custom_readonly" id="gstin_registration_status"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Taxpayer Type</label>
                                    <input type="text" class="form-control custom_readonly" id="gstin_taxpayer_type" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Business Nature</label>
                                    <input type="text" class="form-control custom_readonly" id="gstin_business_nature" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Registration Date</label>
                                    <input type="text" class="form-control custom_readonly" id="gstin_registration_date"
                                        readonly>
                                </div>
                                <div class="col-12 form-group">
                                    <label class="form-label">Business Address</label>
                                    <textarea class="form-control custom_readonly" id="gstin_business_address" rows="2"
                                        readonly></textarea>
                                </div>
                            </div>
                        </div>

                        <div id="gst_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> GST Verification Completed</h6>
                            <p class="mb-0">GST details have been successfully verified and business information retrieved.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $step_counter++;
        } elseif ($verification_type == 'BANK') {
            ?>
            <!-- Step <?php echo $step_counter; ?>: Bank Verification -->
            <div id="step-<?php echo $step_counter; ?>" class="verification-step">
                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> Bank Account Verification - Completed</h3>
                        <p class="text-success">Your Bank Account has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['BANK']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-university text-primary"></i> Bank Account Verification</h3>
                        <p>Verify your bank account details for secure transactions</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>
                            <!-- Verified Bank Inputs (Non-editable) -->
                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">IFSC Code</label>
                                    <input type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['ifsc_code'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Bank Account Number</label>
                                    <input type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['bank_account_no'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Verification Date</label>
                                    <input type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['BANK']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['BANK']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>

                        <?php else: ?>
                            <!-- Bank Input Form (Editable) -->
                            <div class="form-group mb-3">
                                <label class="form-label">IFSC Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ifsc_code" placeholder="SBIN0001234"
                                        oninput="formatIFSCInput(this)" maxlength="11" style="text-transform: uppercase;"
                                        value="<?php echo htmlspecialchars($vendordetail['ifsc_code'] ?? ''); ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">Format: ABCD0123456 (4 letters + 0 + 6 digits/letters)</small>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Bank Account Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="bank_account_no" placeholder="Enter account number"
                                        oninput="formatBankAccountInput(this)"
                                        value="<?php echo htmlspecialchars($vendordetail['bank_account_no'] ?? ''); ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div
                                        class="col-lg-<?php echo in_array('BANK', $required_document_verifications) ? '12' : '6'; ?>">
                                        <button type="button" class="btn btn-secondary btn-sm" id="bank_edit_btn"
                                            onclick="toggleEdit('bank')">
                                            Change Info
                                        </button>
                                    </div>
                                    <?php if (!in_array('BANK', $required_document_verifications)): ?>
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm" onclick="skipStep('bank')">
                                                Skip This Step
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!in_array('BANK', $required_document_verifications)): ?>
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

                            <!-- Hidden verify button for internal use -->
                            <button type="button" class="btn btn-primary" id="verify_bank_btn" onclick="verifyBank()"
                                style="display: none;">
                                <i class="fa fa-search"></i> Verify Bank Account
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="bank_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> Bank Verification Details</h5>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <label class="form-label">IFSC Code</label>
                                    <input type="text" class="form-control custom_readonly" id="bank_verified_ifsc" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" class="form-control custom_readonly" id="bank_verified_account" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Beneficiary Name</label>
                                    <input type="text" class="form-control custom_readonly" id="bank_verified_name" readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Verification Status</label>
                                    <input type="text" class="form-control custom_readonly" id="bank_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="bank_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> Bank Verification Completed</h6>
                            <p class="mb-0">Bank account details have been successfully verified.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $step_counter++;
        } elseif ($verification_type == 'MSME') {
            ?>
            <!-- Step <?php echo $step_counter; ?>: MSME Verification -->
            <div id="step-<?php echo $step_counter; ?>" class="verification-step">
                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> MSME/Udyam Registration Verification - Completed</h3>
                        <p class="text-success">Your MSME has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['MSME']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-industry text-primary"></i> MSME/Udyam Registration Verification</h3>
                        <p>Verify your MSME/Udyam registration details</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>
                            <!-- Verified MSME Inputs (Non-editable) -->
                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">MSME/Udyam Registration Number</label>
                                    <input type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['msme_number'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Verification Date</label>
                                    <input type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['MSME']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['MSME']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>

                        <?php else: ?>
                            <!-- MSME Input Form (Editable) -->
                            <div class="form-group">
                                <label class="form-label">MSME/Udyam Registration Number</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="msme_number" placeholder="UDYAM-XX-00-0000000"
                                        oninput="formatMSMEInput(this)" maxlength="19" style="text-transform: uppercase;"
                                        value="<?php echo $vendordetail['msme_number'] ?? ''; ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">Format: UDYAM-XX-00-0000000</small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div
                                        class="col-lg-<?php echo in_array('MSME', $required_document_verifications) ? '12' : '6'; ?>">
                                        <button type="button" class="btn btn-secondary btn-sm" id="msme_edit_btn"
                                            onclick="toggleEdit('msme')">
                                            Change Info
                                        </button>
                                    </div>
                                    <?php if (!in_array('MSME', $required_document_verifications)): ?>
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm" onclick="skipStep('msme')">
                                                Skip This Step
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!in_array('MSME', $required_document_verifications)): ?>
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

                            <!-- Hidden verify button for internal use -->
                            <button type="button" class="btn btn-primary" id="verify_msme_btn" onclick="verifyMSME()"
                                style="display: none;">
                                <i class="fa fa-search"></i> Verify MSME
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="msme_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> MSME Verification Details</h5>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <label class="form-label">Enterprise Name</label>
                                    <input type="text" class="form-control custom_readonly" id="msme_verified_enterprise_name"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Major Activity</label>
                                    <input type="text" class="form-control custom_readonly" id="msme_verified_major_activity"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Organization Type</label>
                                    <input type="text" class="form-control custom_readonly" id="msme_verified_organization_type"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Social Category</label>
                                    <input type="text" class="form-control custom_readonly" id="msme_verified_social_category"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Commencement Date</label>
                                    <input type="text" class="form-control custom_readonly" id="msme_verified_commencement_date"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">DIC Name</label>
                                    <input type="text" class="form-control custom_readonly" id="msme_verified_dic_name"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Registration Date</label>
                                    <input type="text" class="form-control custom_readonly" id="msme_verified_registration_date"
                                        readonly>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control custom_readonly" id="msme_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="msme_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> MSME Verification Completed</h6>
                            <p class="mb-0">MSME registration details have been successfully verified.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $step_counter++;
        } elseif ($verification_type == 'TAN') {
            ?>
            <!-- Step <?php echo $step_counter; ?>: TAN Verification -->
            <div id="step-<?php echo $step_counter; ?>" class="verification-step">
                <div class="step-header">
                    <?php if ($is_verified): ?>
                        <h3><i class="fa fa-check-circle text-success"></i> TAN Number Verification - Completed</h3>
                        <p class="text-success">Your TAN has been successfully verified on
                            <?php echo date('d M Y, h:i A', strtotime($vendor_verifications['TAN']['verification_datetime'])); ?>
                        </p>
                    <?php else: ?>
                        <h3><i class="fa fa-file-text text-primary"></i> TAN Number Verification</h3>
                        <p>Verify your Tax Deduction Account Number</p>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php if ($is_verified): ?>
                            <!-- Verified TAN Inputs (Non-editable) -->
                            <div class="verified-section border rounded p-3 bg-light shadow-sm">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">TAN Number</label>
                                    <input type="text" class="form-control custom_readonly"
                                        value="<?php echo htmlspecialchars($vendordetail['tan_number'] ?? ''); ?>" readonly
                                        tabindex="-1">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Verification Date</label>
                                    <input type="text" class="form-control custom_readonly" value="<?php echo !empty($vendor_verifications['TAN']['verification_datetime'])
                                        ? date('d M Y, h:i A', strtotime($vendor_verifications['TAN']['verification_datetime']))
                                        : ''; ?>" readonly tabindex="-1">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-lock"></i> This verification is completed and cannot be edited.
                                    Contact the administrator if changes are needed.
                                </small>
                            </div>

                        <?php else: ?>
                            <!-- TAN Input Form (Editable) -->
                            <div class="form-group">
                                <label class="form-label">TAN Number</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="tan_number" placeholder="ABCD12345E"
                                        oninput="formatTANInput(this)" maxlength="10" style="text-transform: uppercase;"
                                        value="<?php echo $vendordetail['tan_number'] ?? ''; ?>">
                                    <span class="input-group-text status-icon">
                                        <i class="fa fa-clock-o text-muted"></i>
                                    </span>
                                </div>
                                <small class="text-muted">Format: ABCD12345E (4 letters + 5 digits + 1 letter)</small>
                            </div>

                            <div class="form-group">
                                <div class="row mt-2">
                                    <div
                                        class="col-lg-<?php echo in_array('TAN', $required_document_verifications) ? '12' : '6'; ?>">
                                        <button type="button" class="btn btn-secondary btn-sm" id="tan_edit_btn"
                                            onclick="toggleEdit('tan')">
                                            Change Info
                                        </button>
                                    </div>
                                    <?php if (!in_array('TAN', $required_document_verifications)): ?>
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-info btn-sm" onclick="skipStep('tan')">
                                                Skip This Step
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!in_array('TAN', $required_document_verifications)): ?>
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

                            <!-- Hidden verify button for internal use -->
                            <button type="button" class="btn btn-primary" id="verify_tan_btn" onclick="verifyTAN()"
                                style="display: none;">
                                <i class="fa fa-search"></i> Verify TAN
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">
                        <div id="tan_verification_fields" class="verification-fields">
                            <h5><i class="fa fa-info-circle"></i> TAN Verification Details</h5>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" class="form-control custom_readonly" id="tan_verified_company" readonly>

                                </div>
                                <div class="col-12 form-group">
                                    <label class="form-label">Verification Status</label>
                                    <input type="text" class="form-control custom_readonly" id="tan_verified_status" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="tan_confirmation_section" class="confirmation-section">
                            <h6><i class="fa fa-check-circle text-success"></i> TAN Verification Completed</h6>
                            <p class="mb-0">TAN details have been successfully verified.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $step_counter++;
        }
    }
    ?>

    <!-- Navigation Buttons -->
    <div class="navigation-buttons">
        <button type="button" class="btn btn-secondary" id="prevBtn" onclick="prevStep()" style="display: none;">
            <i class="fa fa-arrow-left"></i> Previous
        </button>

        <div>
            <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">
                Next <i class="fa fa-arrow-right"></i>
            </button>
            <!-- <button type="button" class="btn btn-success" id="finishBtn" onclick="finishVerification()"
                style="display: none;">
                <i class="fa fa-check"></i> Complete Verification
            </button> -->
        </div>
    </div>
</div> 
<script type="text/javascript">
    var vendor_id = '<?php echo $vendor_id; ?>';
    var transaction_table = '<?php echo $transaction_table; ?>';

    var requiredVerifications = <?php echo json_encode($required_document_verifications); ?>;
    var visibleVerifications = <?php echo json_encode($visible_document_verifications); ?>;
    var vendorVerifications = <?php echo json_encode($vendor_verifications); ?>;
    
    console.log("vendor_id -",vendor_id,"   transaction_table -",transaction_table,"   requiredVerifications -",requiredVerifications,"   visibleVerifications -",visibleVerifications,"   vendorVerifications -",vendorVerifications);

    var verificationSteps = {};
    var stepMapping = {};
    var totalSteps = visibleVerifications.length;
    var currentStep = 1;

    if (!vendorVerifications || typeof vendorVerifications !== 'object') {
        vendorVerifications = {};
    }

    visibleVerifications.forEach(function (verification, index) {
        var stepNumber = index + 1;
        var verificationKey = verification.toLowerCase();

        var verificationData = (vendorVerifications && typeof vendorVerifications === 'object')
            ? vendorVerifications[verification] || {}
            : {};

        var isCompleted = verificationData.status === 'Y';

        verificationSteps[verificationKey] = {
            completed: isCompleted,
            data: isCompleted ? verificationData : null,
            required: (verification === 'PAN'),
            skipped: false,
            verified: isCompleted
        };

        stepMapping[verificationKey] = stepNumber;
        stepMapping[stepNumber] = verificationKey;

        if (isCompleted) {
            console.log('✅ Verification already completed for:', verification);
        } else {
            console.log('⏳ Verification pending for:', verification);
        }
    });

    function checkCompanyNameAllReadyAvilableOrNot() {
        var company_name = $("#company_name").val();
        if (company_name != "") {
            $.ajax({
                url: "ajax.php",
                data: 'page=checkcompanynameallreadyavilableornot&company_name=' + company_name,
                type: "POST",
                async: false,
                success: function (data) {
                    console.log(data);
                    var res = JSON.parse(data);
                    if (res.company_name == '1') {
                        console.log('already');
                        $("#allreadycompanyname").show();

                    } else {
                        console.log('frash');
                        $("#allreadycompanyname").hide();
                    }
                },
                error: function () { }
            });
        }
    }

    function testJQuery() {
        console.log("Testing jQuery...");
        if (typeof $ !== 'undefined') {
            console.log("jQuery is available");
            console.log("jQuery version:", $.fn.jquery);
            return true;
        } else {
            console.log("jQuery is NOT available");
            return false;
        }
    }

    function initializePreCompletedVerifications() {
        console.log("Initializing pre-completed verifications...");

        Object.keys(completedVerifications).forEach(function (verificationType) {
            var verificationKey = verificationType.toLowerCase();
            var stepNumber = getStepForSection(verificationKey);
            var verificationData = completedVerifications[verificationType];

            console.log("Setting up pre-completed verification for:", verificationType);

            $('.step-indicator .step').eq(stepNumber - 1).addClass('completed');

            if (verificationType === 'PAN') {
                setupPreCompletedPAN(verificationData);
            } else if (verificationType === 'GSTIN') {
                setupPreCompletedGST(verificationData);
            } else if (verificationType === 'BANK') {
                setupPreCompletedBank(verificationData);
            } else if (verificationType === 'MSME') {
                setupPreCompletedMSME(verificationData);
            } else if (verificationType === 'TAN') {
                setupPreCompletedTAN(verificationData);
            }
        });
    }

    function setupPreCompletedPAN(data) {
        var stepDiv = $('#step-' + getStepForSection('pan'));
        stepDiv.addClass('pre-completed-verification');

        $('#pan_number, #company_name').prop('readonly', true);
        $('#pan_verification_fields').show();
        $('#pan_confirmation_section').show();
        $('#verify_pan_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#pan_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#pan_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> PAN Already Verified');
        $('#pan_confirmation_section p').html('This PAN has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#pan_confirmation_section').after(restrictionMsg);
    }

    function setupPreCompletedGST(data) {
        var stepDiv = $('#step-' + getStepForSection('gstin'));
        stepDiv.addClass('pre-completed-verification');

        $('#gstin').prop('readonly', true);
        $('#gst_verification_fields').show();
        $('#gst_confirmation_section').show();
        $('#verify_gst_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#gst_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#gst_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> GST Already Verified');
        $('#gst_confirmation_section p').html('This GST has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#gst_confirmation_section').after(restrictionMsg);
    }

    function setupPreCompletedBank(data) {
        var stepDiv = $('#step-' + getStepForSection('bank'));
        stepDiv.addClass('pre-completed-verification');

        $('#ifsc_code, #bank_account_no').prop('readonly', true);
        $('#bank_verification_fields').show();
        $('#bank_confirmation_section').show();
        $('#verify_bank_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#bank_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#bank_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> Bank Already Verified');
        $('#bank_confirmation_section p').html('This bank account has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#bank_confirmation_section').after(restrictionMsg);
    }

    function setupPreCompletedMSME(data) {
        var stepDiv = $('#step-' + getStepForSection('msme'));
        stepDiv.addClass('pre-completed-verification');

        $('#msme_number').prop('readonly', true);
        $('#msme_verification_fields').show();
        $('#msme_confirmation_section').show();
        $('#verify_msme_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#msme_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#msme_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> MSME Already Verified');
        $('#msme_confirmation_section p').html('This MSME has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#msme_confirmation_section').after(restrictionMsg);
    }

    function setupPreCompletedTAN(data) {
        var stepDiv = $('#step-' + getStepForSection('tan'));
        stepDiv.addClass('pre-completed-verification');

        $('#tan_number').prop('readonly', true);
        $('#tan_verification_fields').show();
        $('#tan_confirmation_section').show();
        $('#verify_tan_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#tan_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#tan_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> TAN Already Verified');
        $('#tan_confirmation_section p').html('This TAN has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#tan_confirmation_section').after(restrictionMsg);
    }

    function initializeButtonStates() {
        console.log("Initializing button states...");

        var sections = visibleVerifications.map(function (verification) {
            return verification.toLowerCase();
        });

        sections.forEach(function (section) {
            var stepDiv = $('#step-' + getStepForSection(section));
            var inputs = stepDiv.find('input:not(.custom_readonly), textarea:not(.custom_readonly), select:not(.custom_readonly)');
            var button = $('#' + section + '_edit_btn');

            var hasReadonlyInputs = false;
            inputs.each(function () {
                if ($(this).prop('readonly') || $(this).prop('disabled')) {
                    hasReadonlyInputs = true;
                    return false;
                }
            });

            if (hasReadonlyInputs) {
                button.text('Change Info').removeClass('btn-warning').addClass('btn-success');
                console.log(section + ' button set to readonly state (success)');
            } else {
                button.text('Change Info').removeClass('btn-success').addClass('btn-warning');
                console.log(section + ' button set to editable state (warning)');
            }
        });

        $('.custom_readonly').prop('readonly', true);
        console.log("All verification details set to readonly");
    }

    function getStepForSection(section) {
        return stepMapping[section] || 1;
    }

    function getSectionForStep(step) {
        return stepMapping[step] || 'pan';
    }

    function nextStep() {
        if (!validateCurrentStep()) {
            return;
        }

        var currentSectionKey = getSectionForStep(currentStep);
        var currentVerificationStep = verificationSteps[currentSectionKey];
 
        if (currentVerificationStep && hasDataInCurrentStep() && !currentVerificationStep.completed && !currentVerificationStep.skipped) {
            var sectionName = currentSectionKey.toUpperCase();
            showNotification("Please complete " + sectionName + " verification before proceeding to the next step. If you don't want to verify now, please skip this step.", "warning");
            return;
        }

        if (currentVerificationStep && currentVerificationStep.data && currentVerificationStep.nameMatch === false && !currentVerificationStep.skipped) {
            var sectionName = currentSectionKey.toUpperCase();
            showNotification("Cannot proceed to next step. " + sectionName + " verification shows company name mismatch. Please correct the company name or contact support.", "error");
            return;
        }

        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
            updateStepIndicator();
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
            updateStepIndicator();
        }
    }

    function goToStep(step) { 
        if (step < currentStep) {
            currentStep = step;
            showStep(currentStep);
            updateStepIndicator();
            return;
        }
 
        for (let i = 1; i < step; i++) {
            if (!validateStep(i)) {
                var sectionName = getSectionForStep(i).toUpperCase();
                showNotification("Please complete " + sectionName + " verification or skip it before proceeding to step " + step, "warning");
                return;
            }
        }

        currentStep = step;
        showStep(currentStep);
        updateStepIndicator();
    }

    function validateCurrentStep() {
        return validateStep(currentStep);
    }

    function hasDataInCurrentStep() {
        var currentSectionKey = getSectionForStep(currentStep);

        switch (currentSectionKey) {
            case 'pan':
                return $('#pan_number').val().trim() !== '' || $('#company_name').val().trim() !== '';
            case 'gstin':
                return $('#gstin').val().trim() !== '';
            case 'bank':
                return $('#ifsc_code').val().trim() !== '' || $('#bank_account_no').val().trim() !== '';
            case 'msme':
                return $('#msme_number').val().trim() !== '';
            case 'tan':
                return $('#tan_number').val().trim() !== '';
            default:
                return false;
        }
    }

    function clearCurrentStepData() {
        var currentSectionKey = getSectionForStep(currentStep);

        switch (currentSectionKey) {
            case 'pan':
                $('#pan_number').val('');
                $('#company_name').val('');
                break;
            case 'gstin':
                $('#gstin').val('');
                break;
            case 'bank':
                $('#ifsc_code').val('');
                $('#bank_account_no').val('');
                break;
            case 'msme':
                $('#msme_number').val('');
                break;
            case 'tan':
                $('#tan_number').val('');
                break;
        }
 
        var stepDiv = $('#step-' + currentStep);
        stepDiv.find('.input-group .status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
        stepDiv.find('.input-group').removeClass('has-success has-error');
 
        resetCurrentStepVerification();
    }

    function validateStep(step) {
        var sectionKey = getSectionForStep(step);
        var verificationStep = verificationSteps[sectionKey];

        if (!verificationStep) {
            return true;
        }
 
        if (!verificationStep.completed && !verificationStep.skipped) {
            var sectionName = sectionKey.toUpperCase();
            showNotification("Please complete " + sectionName + " verification or skip this step before proceeding", "warning");
            return false;
        }

        if (verificationStep.data && verificationStep.nameMatch === false && !verificationStep.skipped) {
            var sectionName = sectionKey.toUpperCase();
            showNotification(sectionName + " verification shows company name mismatch. Please correct the company name or contact support before proceeding to the next step.", "error");
            return false;
        }

        if (verificationSteps.tan && verificationSteps.tan.data && verificationSteps.tan.nameMatch === false && !verificationSteps.tan.skipped) {
            showNotification("TAN verification shows company name mismatch. Please correct the company name or contact support before proceeding.", "error");
            return false;
        }
        return true;
    }

    function showStep(step) {
        $('.verification-step').hide();
        $('#step-' + step).show();

        $('#prevBtn').toggle(step > 1);
        $('#nextBtn').toggle(step < totalSteps);
        $('#finishBtn').toggle(step === totalSteps);

        var sectionKey = getSectionForStep(step);
        if (sectionKey) {
            console.log('Showing step ' + step + ' for section: ' + sectionKey);
        }
    }

    function updateStepIndicator() {
        $('.step-indicator .step').each(function (index) {
            var stepNum = index + 1;
            $(this).removeClass('active completed');

            var verification = visibleVerifications[index];
            var verificationKey = verification.toLowerCase();
            var isVerified = vendorVerifications[verification] &&
                vendorVerifications[verification].status === 'Y';
            var isCompleted = verificationSteps[verificationKey] && verificationSteps[verificationKey].completed;
            var isSkipped = verificationSteps[verificationKey] && verificationSteps[verificationKey].skipped;

            if (isVerified || isCompleted || (stepNum < currentStep && (isCompleted || isSkipped))) {
                $(this).addClass('completed');
                $(this).find('.step-number').html('<i class="fa fa-check"></i>');
            } else if (stepNum === currentStep) {
                $(this).addClass('active');
                $(this).find('.step-number').html(stepNum);
            } else {
                $(this).find('.step-number').html(stepNum);
            }
        });
    }

    function skipStep(stepType) { 
        if (hasDataInCurrentStep()) {
            clearCurrentStepData();
        }
 
        verificationSteps[stepType].skipped = true;
        verificationSteps[stepType].completed = false;
 
        var stepName = stepType.toUpperCase();
        var stepDiv = $('#step-' + currentStep);
        stepDiv.find('.verification-fields').hide();
        stepDiv.find('.confirmation-section').hide();

        var skipMessage = `
        <div class="alert alert-info mt-3" id="${stepType}_skip_message">
            <i class="fa fa-info-circle"></i> ${stepName} verification has been skipped. 
            You can complete this verification later if needed.
        </div>`;

        stepDiv.find(`#${stepType}_skip_message`).remove();
        stepDiv.find('.step-header').after(skipMessage);
 
        showNotification(stepName + " verification skipped automatically", "info");
 
        if (currentStep === totalSteps) {
            // finishVerification();
        } else {
            nextStep();
        }
    }


    function validatePAN(pan) {
        var panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        return panRegex.test(pan);
    }

    function validateGST(gst) {
        var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        return gstRegex.test(gst);
    }

    function validateIFSC(ifsc) {
        var ifscRegex = /^[A-Z]{4}0[A-Z0-9]{6}$/;
        return ifscRegex.test(ifsc);
    }

    function validateMSME(msme) {
        var msmeRegex = /^UDYAM-[A-Z]{2}-[0-9]{2}-[0-9]{7}$/;
        return msmeRegex.test(msme);
    }

    function validateTAN(tan) {
        var tanRegex = /^[A-Z]{4}[0-9]{5}[A-Z]{1}$/;
        return tanRegex.test(tan);
    }

    function formatPANInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 10) value = value.substring(0, 10);
        input.value = value;

        var isValid = validatePAN(value);
        updateInputStatus('pan_number', isValid, value.length === 10);

        if (isValid && value.length === 10) {
            setTimeout(function () {
                verifyPAN();
            }, 500);
        }
    }

    function formatGSTInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 15) value = value.substring(0, 15);
        input.value = value;

        var isValid = validateGST(value);
        updateInputStatus('gstin', isValid, value.length === 15);

        if (isValid && value.length === 15) {
            setTimeout(function () {
                verifyGST();
            }, 500);
        }
    }

    function formatIFSCInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 11) value = value.substring(0, 11);
        input.value = value;

        var isValid = validateIFSC(value);
        updateInputStatus('ifsc_code', isValid, value.length === 11);

        if (isValid && value.length === 11) {
            checkBankAutoVerification();
        }
    }

    function checkBankAutoVerification() {
        var ifsc = $("#ifsc_code").val();
        var account = $("#bank_account_no").val();

        if (validateIFSC(ifsc) && account && account.length >= 8) {
            setTimeout(function () {
                verifyBank();
            }, 500);
        }
    }

    function formatBankAccountInput(input) {
        var value = input.value.replace(/[^0-9]/g, '');
        input.value = value;

        if (value.length >= 8) {
            checkBankAutoVerification();
        }
    }

    function formatMSMEInput(input) {
        var value = input.value.toUpperCase();
        if (!value.startsWith('UDYAM-')) {
            value = 'UDYAM-' + value.replace(/[^A-Z0-9-]/g, '');
        }
        if (value.length > 19) value = value.substring(0, 19);
        input.value = value;

        var isValid = validateMSME(value);
        updateInputStatus('msme_number', isValid, value.length === 19);

        if (isValid && value.length === 19) {
            setTimeout(function () {
                verifyMSME();
            }, 500);
        }
    }

    function formatTANInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 10) value = value.substring(0, 10);
        input.value = value;

        var isValid = validateTAN(value);
        updateInputStatus('tan_number', isValid, value.length === 10);

        if (isValid && value.length === 10) {
            setTimeout(function () {
                verifyTAN();
            }, 500);
        }
    }

    function updateInputStatus(fieldId, isValid, isComplete) {
        var inputGroup = $('#' + fieldId).closest('.input-group');
        var statusIcon = inputGroup.find('.status-icon');

        if (isComplete) {
            if (isValid) {
                statusIcon.html('<i class="fa fa-check text-success"></i>');
                inputGroup.removeClass('has-error').addClass('has-success');
            } else {
                statusIcon.html('<i class="fa fa-times text-danger"></i>');
                inputGroup.removeClass('has-success').addClass('has-error');
            }
        } else {
            statusIcon.html('<i class="fa fa-clock-o text-muted"></i>');
            inputGroup.removeClass('has-success has-error');
        }
    }

    function toggleEdit(section) {
        console.log('toggleEdit called for section:', section);
        console.log('Current step:', currentStep);

        var stepDiv = $('#step-' + currentStep);
        var inputs = stepDiv.find('input:not(.custom_readonly), textarea:not(.custom_readonly), select:not(.custom_readonly)');
        var button = $('#' + section + '_edit_btn');

        console.log('Found editable inputs:', inputs.length);

        var hasReadonlyInputs = false;
        inputs.each(function () {
            if ($(this).prop('readonly') || $(this).prop('disabled')) {
                hasReadonlyInputs = true;
                return false;
            }
        });

        console.log('Has readonly inputs:', hasReadonlyInputs);

        if (hasReadonlyInputs) {
            console.log('Enabling editing mode...');
            inputs.each(function () {
                console.log('Enabling field:', $(this).attr('id') || $(this).attr('name'));
                $(this).prop('readonly', false).prop('disabled', false);
            });
 
            resetCurrentStepVerificationStatus();

            button.text('Change Info').removeClass('btn-success').addClass('btn-warning');
            console.log('Button changed to Change Info (editable mode)');
        } else {
            console.log('Disabling editing mode...');
            inputs.each(function () {
                console.log('Disabling field:', $(this).attr('id') || $(this).attr('name'));
                $(this).prop('readonly', true);
                if ($(this).is('select')) {
                    $(this).prop('disabled', true);
                }
            });
            button.text('Change Info').removeClass('btn-warning').addClass('btn-success');
            console.log('Button changed to Change Info (readonly mode)');
        }
    }

    function resetCurrentStepVerificationStatus() {
        console.log('Resetting verification status for step:', currentStep);

        var stepType = getSectionForStep(currentStep);

        if (stepType && verificationSteps[stepType]) {
            verificationSteps[stepType].completed = false;
            verificationSteps[stepType].skipped = false;
            verificationSteps[stepType].data = null;

            $('#' + stepType + '_skip_message').remove();
        }

        if (stepType === 'pan') { 
            $('#pan_confirmation_section').hide();
            $('#verify_pan_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
            $('#pan_verified_name').val('');
            $('#pan_verified_type').val('');
            $('#pan_verified_status').val('');
            $('#pan_number').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#pan_number').closest('.input-group').removeClass('has-success has-error');
            console.log('PAN verification status reset');
        } else if (stepType === 'gstin') { 
            $('#gst_confirmation_section').hide();
            $('#gst_address_options').hide();
            $('#verify_gst_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify GST').prop('disabled', false);
            $('#gstin_legal_name').val('');
            $('#gstin_trade_name').val('');
            $('#gstin_registration_status').val('');
            $('#gstin_taxpayer_type').val('');
            $('#gstin_business_nature').val('');
            $('#gstin_registration_date').val('');
            $('#gstin_business_address').val('');
            $('#gstin').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#gstin').closest('.input-group').removeClass('has-success has-error');
            console.log('GST verification status reset');
        } else if (stepType === 'bank') { 
            $('#bank_confirmation_section').hide();
            $('#verify_bank_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank Account').prop('disabled', false);
            $('#bank_verified_ifsc').val('');
            $('#bank_verified_account').val('');
            $('#bank_verified_name').val('');
            $('#bank_verified_status').val('');
            $('#ifsc_code').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#ifsc_code').closest('.input-group').removeClass('has-success has-error');
            $('#bank_account_no').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#bank_account_no').closest('.input-group').removeClass('has-success has-error');
            console.log('Bank verification status reset');
        } else if (stepType === 'msme') { 
            $('#msme_confirmation_section').hide();
            $('#verify_msme_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME').prop('disabled', false);
            $('#msme_verified_enterprise_name').val('');
            $('#msme_verified_major_activity').val('');
            $('#msme_verified_organization_type').val('');
            $('#msme_verified_social_category').val('');
            $('#msme_verified_commencement_date').val('');
            $('#msme_verified_dic_name').val('');
            $('#msme_verified_registration_date').val('');
            $('#msme_verified_status').val('');
            console.log('MSME verification status reset');
        } else if (stepType === 'tan') { 
            $('#tan_confirmation_section').hide();
            $('#verify_tan_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify TAN').prop('disabled', false);
            $('#tan_verified_company').val('');
            $('#tan_verified_status').val('');
            console.log('TAN verification status reset');
        }
    }

    function resetCurrentStepVerification() {
        console.log('Resetting verification for step:', currentStep);

        var stepType = getSectionForStep(currentStep);

        if (stepType && verificationSteps[stepType]) {
            verificationSteps[stepType].completed = false;
            verificationSteps[stepType].skipped = false;
            verificationSteps[stepType].data = null;

            $('#' + stepType + '_skip_message').remove();
        }

        if (stepType === 'pan') {
            $('#pan_verification_fields').hide();
            $('#pan_confirmation_section').hide();
            $('#verify_pan_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
            $('#pan_verified_name').val('');
            $('#pan_verified_type').val('');
            $('#pan_verified_status').val('');
            $('#pan_number').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#pan_number').closest('.input-group').removeClass('has-success has-error');
            console.log('PAN verification reset');
        } else if (stepType === 'gstin') {
            $('#gst_verification_fields').hide();
            $('#gst_confirmation_section').hide();
            $('#gst_address_options').hide();
            $('#verify_gst_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify GST').prop('disabled', false);
            $('#gstin_legal_name').val('');
            $('#gstin_trade_name').val('');
            $('#gstin_registration_status').val('');
            $('#gstin_taxpayer_type').val('');
            $('#gstin_business_nature').val('');
            $('#gstin_registration_date').val('');
            $('#gstin_business_address').val('');
            $('#gstin').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#gstin').closest('.input-group').removeClass('has-success has-error');
            console.log('GST verification reset');
        } else if (stepType === 'bank') {
            $('#bank_verification_fields').hide();
            $('#bank_confirmation_section').hide();
            $('#verify_bank_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank Account').prop('disabled', false);
            $('#bank_verified_ifsc').val('');
            $('#bank_verified_account').val('');
            $('#bank_verified_name').val('');
            $('#bank_verified_status').val('');
            $('#ifsc_code').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#ifsc_code').closest('.input-group').removeClass('has-success has-error');
            $('#bank_account_no').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#bank_account_no').closest('.input-group').removeClass('has-success has-error');
            console.log('Bank verification reset');
        } else if (stepType === 'msme') {
            $('#msme_verification_fields').hide();
            $('#msme_confirmation_section').hide();
            $('#verify_msme_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME').prop('disabled', false);
            $('#msme_verified_enterprise_name').val('');
            $('#msme_verified_major_activity').val('');
            $('#msme_verified_organization_type').val('');
            $('#msme_verified_social_category').val('');
            $('#msme_verified_commencement_date').val('');
            $('#msme_verified_dic_name').val('');
            $('#msme_verified_registration_date').val('');
            $('#msme_verified_status').val('');
            console.log('MSME verification reset');
        } else if (stepType === 'tan') {
            $('#tan_verification_fields').hide();
            $('#tan_confirmation_section').hide();
            $('#verify_tan_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify TAN').prop('disabled', false);
            $('#tan_verified_company').val('');
            $('#tan_verified_status').val('');
            console.log('TAN verification reset');
        }
    }

    function verifyPAN() {
        if (vendorVerifications.PAN && vendorVerifications.PAN.status === 'Y') {
            showNotification("PAN is already verified and cannot be modified", "warning");
            return;
        }

        var pan_no = $("#pan_number").val();
        var company_name = $("#company_name").val() || "Test Company";

        if (!validatePAN(pan_no)) {
            showNotification("Please enter a valid PAN number", "error");
            return;
        }

        if ($("#verify_pan_btn").prop('disabled') || verificationSteps.pan.completed) {
            return;
        }

        $("#pan_number, #company_name").prop('readonly', true);
        $("#verify_pan_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
        $("#pan_verification_fields").show();
        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'pan_holder_name': company_name,
                'customer_pan_number': pan_no,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'ZOOPONEPANLITE',
                'consent': 'Y',
                'consent_text': 'I hereby declare my consent agreement for fetching my information via ZOOP API',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);

                    if (result.success == true && result.result) {
                        $("#pan_verified_name").val(result.result.user_full_name || result.result.pan_holder_name || "N/A");
                        $("#pan_verified_type").val(result.result.pan_type || "N/A");
                        $("#pan_verified_status").val(result.result.pan_status || "N/A");

                        verificationSteps.pan.completed = true;
                        verificationSteps.pan.data = result;

                        $("#pan_confirmation_section").show();
                        $("#verify_pan_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
                        $("#pan_number, #company_name").prop('readonly', true);
                        $("#pan_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-success');
                        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").prop('readonly', true).addClass('custom_readonly');

                        var company_name_entered = company_name.toLowerCase().trim();
                        var pan_holder_name = (result.result.user_full_name || result.result.pan_holder_name || "").toLowerCase().trim();

                        if (company_name_entered === pan_holder_name) {
                            showNotification("PAN Details are Correct", "success");
                            verificationSteps.pan.nameMatch = true;

                            $.ajax({
                                url: "ajax.php",
                                type: "POST",
                                data: {
                                    page: 'insertvendorsandvendorregisters',
                                    company_name: company_name,
                                    display_name: pan_holder_name,
                                    pan_no: pan_no,
                                    transaction_table: transaction_table,
                                    vendor_id: vendor_id,
                                    verification_type: 'PAN',
                                    api_hit_id: result.api_hit_id,
                                },
                                async: false,
                                success: function (response) {
                                    try {
                                        console.log('pan data', response);
                                        var obj = JSON.parse(response);
                                        if (obj.all_result && obj.all_result.LASTID) {
                                            var lastID = obj.all_result.LASTID || '0';
                                            tostarnotify('top', 'right', 'fa fa-thumbs-up',
                                                'form-label label label-success',
                                                'animated flipInX', 'animated flipOutX',
                                                (lastID == '0'
                                                    ? '<?php echo $_SESSION['error_message']['3001']; ?>'
                                                    : '<?php echo $_SESSION['error_message']['3000']; ?>'),
                                                ''
                                            );

                                            if (lastID != '0') {
                                                updateUrlParameter('Rid', obj.rid);
                                            }
                                            location.reload();
                                        } else {
                                            tostarnotify('top', 'right', 'fa fa-thumbs-down',
                                                'form-label label label-danger',
                                                'animated flipInX', 'animated flipOutX',
                                                'Insert failed or response invalid', '');
                                        }
                                    } catch (err) {
                                        console.error('Insert vendor parse error:', err);
                                    }
                                },

                                error: function () { }
                            });

                        } else {
                            verificationSteps.pan.completed = false;
                            verificationSteps.pan.nameMatch = false;

                            $("#pan_confirmation_section").removeClass('confirmation-section').addClass('alert alert-warning');
                            $("#pan_confirmation_section h6").html('<i class="fa fa-exclamation-triangle text-warning"></i> PAN Verification Done - Data Mismatch');
                            $("#pan_confirmation_section p").html('PAN details have been retrieved but company name does not match. Please verify the information is correct before proceeding.');
                            showNotification("Verification completed but company name doesn't match with PAN holder name. Please check the data.", "warning");
                            $("#pan_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');
                            $("#verify_pan_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Data Mismatch');
                        }

                    } else {
                        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Verification Failed");
                        showNotification("PAN Verification Failed", "error");
                    }
                } catch (e) {
                    $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Parse Error");
                    showNotification("PAN Verification Parse Error", "error");
                }
            },
            error: function () {
                $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Error occurred");
                showNotification("PAN Verification Error", "error");
            }
        });
    }

    function verifyGST() {
        if (vendorVerifications.GSTIN && vendorVerifications.GSTIN.status === 'Y') {
            showNotification("GST is already verified and cannot be modified", "warning");
            return;
        }

        var gstin = $("#gstin").val();
        var company_name = $("#company_name").val();

        if (!validateGST(gstin)) {
            showNotification("Please enter a valid GST number", "error");
            return;
        }

        console.log('GST Verification: Locking input');
        $("#gstin").prop('readonly', true);
        $("#verify_gst_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

        $("#gst_verification_fields").show();
        $("#gstin_legal_name").val("Verifying...");
        $("#gstin_trade_name").val("Verifying...");
        $("#gstin_registration_status").val("Verifying...");
        $("#gstin_taxpayer_type").val("Verifying...");
        $("#gstin_business_nature").val("Verifying...");
        $("#gstin_registration_date").val("Verifying...");
        $("#gstin_business_address").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'business_gstin_number': gstin,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'GSTVERIFICATIONLITE',
                'consent': 'Y',
                'consent_text': 'I hear by declare my consent agreement for fetching my information via ZOOP AP',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);
                    console.log('GST', response);
                    if (result.success == true && result.result) {
                        console.log('success');
                        $("#gstin_legal_name").val(result.result.legal_name || "N/A");
                        $("#gstin_trade_name").val(result.result.trade_name || "N/A");
                        $("#gstin_registration_status").val(result.result.current_registration_status || "N/A");
                        $("#gstin_taxpayer_type").val(result.result.tax_payer_type || "N/A");
                        $("#gstin_business_nature").val(result.result.business_nature ? result.result.business_nature.join(', ') : "N/A");
                        $("#gstin_registration_date").val(result.result.register_date || "N/A");

                        var address = "N/A";
                        if (result.result.primary_business_address) {
                            var addr = result.result.primary_business_address; 
                            var addressParts = [];

                            var full_address = addr.full_address; 
                            var state = addr.state_code; 
                            var city = addr.district; 
                            var pincode = addr.pincode; 

                            if (addr.building_name) addressParts.push(addr.building_name);
                            if (addr.building_number) addressParts.push(addr.building_number);
                            if (addr.street) addressParts.push(addr.street);
                            if (addr.city) addressParts.push(addr.city);
                            if (addr.district) addressParts.push(addr.district);
                            if (addr.state) addressParts.push(addr.state);
                            if (addr.pincode) addressParts.push(addr.pincode);
                            address = addressParts.length > 0 ? addressParts.join(', ') : "";
                        }
                        $("#gstin_business_address").val(address);

                        $("#gstin").prop('readonly', true);
                        $("#gst_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-success');

                        $("#gstin_legal_name, #gstin_trade_name, #gstin_registration_status, #gstin_taxpayer_type, #gstin_business_nature, #gstin_registration_date, #gstin_business_address").prop('readonly', true).addClass('custom_readonly');

                        if (typeof showGSTAddressOptions === 'function') {
                            showGSTAddressOptions(result.result);
                        }

                        console.log('company_name', company_name);
                        console.log('result.result.trade_name', result.result.trade_name);
                        console.log('result.result.legal_name', result.result.legal_name);
                        var company_name_entered = company_name.toLowerCase().trim();
                        var gst_trade_name = (result.result.trade_name || "").toLowerCase().trim();
                        var gst_legal_name = (result.result.legal_name || "").toLowerCase().trim();

                        var isNameMatch = (company_name_entered === gst_trade_name || company_name_entered === gst_legal_name);

                        console.log('isNameMatch', isNameMatch);
                        if (isNameMatch) {result.api_hit_id
                            console.log('GST verification part');

                            $.ajax({
                                url: "ajax.php",
                                type: "POST",
                                data: {
                                    page: 'insertvendorsandvendorregisters',
                                    gstin: gstin,
                                    address: full_address,
                                    state: state,
                                    city: city,
                                    post_code: pincode,
                                    transaction_table: transaction_table,
                                    vendor_id: vendor_id,
                                    verification_type: 'GSTIN',
                                    api_hit_id: result.api_hit_id,
                                },
                                async: false,
                                success: function (response) {
                                    try {
                                        console.log("gstin response", response);
                                        location.reload();
                                    } catch (err) {
                                        console.error('Insert vendor parse error:', err);
                                    }
                                },
                                error: function () { }
                            });

                            console.log('GST AJAX end part');
                        } else {
                            console.log('gst not status part');
                            verificationSteps.gst.completed = false;
                            verificationSteps.gst.nameMatch = false;

                            $("#gst_confirmation_section").removeClass('confirmation-section').addClass('alert alert-warning');
                            $("#gst_confirmation_section h6").html('<i class="fa fa-exclamation-triangle text-warning"></i> GST Verification Done - Data Mismatch');
                            $("#gst_confirmation_section p").html('GST details have been retrieved but company name does not match. Please verify the information is correct before proceeding.');

                            showNotification("Verification completed but company name doesn't match with GST records. Please check the data.", "warning");
                            $("#gstin").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');

                            $("#verify_gst_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Data Mismatch');
                        }

                    } else {
                        $("#gstin_legal_name").val("Verification Failed");
                        $("#gstin_trade_name").val("Verification Failed");
                        $("#gstin_registration_status").val("Verification Failed");
                        $("#gstin_taxpayer_type").val("Verification Failed");
                        $("#gstin_business_nature").val("Verification Failed");
                        $("#gstin_registration_date").val("Verification Failed");
                        $("#gstin_business_address").val("Verification Failed");
                        showNotification("GST Verification Failed", "error");
                    }
                } catch (e) {
                    showNotification("GST Verification Parse Error", "error");
                }
            },
            error: function (xhr, status, error) {
                showNotification("GST Verification Error", "error");
            }
        });
    }

    function verifyBank() {
        if (vendorVerifications.BANK && vendorVerifications.BANK.status === 'Y') {
            showNotification("Bank Account is already verified and cannot be modified", "warning");
            return;
        }

        var ifsc = $("#ifsc_code").val();
        var account = $("#bank_account_no").val();

        if (!validateIFSC(ifsc)) {
            showNotification("Please enter a valid IFSC code", "error");
            return;
        }

        if (!account) {
            showNotification("Please enter bank account number", "error");
            return;
        }

        if ($("#verify_bank_btn").prop('disabled') || verificationSteps.bank.completed) {
            return;
        }

        console.log('Bank Verification: Locking inputs');
        $("#ifsc_code, #bank_account_no").prop('readonly', true);
        $("#verify_bank_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

        $("#bank_verification_fields").show();
        $("#bank_verified_ifsc").val("Verifying...");
        $("#bank_verified_account").val("Verifying...");
        $("#bank_verified_name").val("Verifying...");
        $("#bank_verified_status").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'account_number': account,
                'ifsc': ifsc,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'BANKACVERIFICATIONLITE',
                'consent': 'Y',
                'consent_text': 'I hear by declare my consent agreement for fetching my information via ZOOP AP',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);
                    console.log('bank result', result);
                    if (result && result.response_code == "200" && result.result && result.result.verification_status == 'VERIFIED') {

                        $("#bank_verified_ifsc").val(ifsc);
                        $("#bank_verified_account").val(account);
                        $("#bank_verified_name").val(result.result.beneficiary_name || "N/A");
                        $("#bank_verified_status").val("Verified Successfully");

                        verificationSteps.bank.completed = true;
                        verificationSteps.bank.data = result;

                        $("#bank_confirmation_section").show();
                        $("#verify_bank_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');

                        console.log('Bank verification successful - making input fields readonly');
                        $("#ifsc_code, #bank_account_no").prop('readonly', true);
                        $("#bank_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-success');

                        $("#bank_verified_ifsc, #bank_verified_account, #bank_verified_name, #bank_verified_status").prop('readonly', true).addClass('custom_readonly');

                        $.ajax({
                            url: "ajax.php",
                            type: "POST",
                            data: {
                                page: 'insertvendorsandvendorregisters',
                                ifsc_code: ifsc,
                                bank_account_no: account,
                                transaction_table: transaction_table,
                                vendor_id: vendor_id,
                                verification_type: 'BANK',
                                api_hit_id: result.api_hit_id,
                            },
                            async: false,
                            success: function (response) {
                                try {
                                    console.log("Bank response", response);
                                    location.reload();
                                } catch (err) {
                                    console.error('Insert vendor parse error:', err);
                                }
                            },
                            error: function () { }
                        });

                        showNotification("Bank Account Verified Successfully", "success");

                    } else {
                        $("#bank_verified_ifsc").val("Verification Failed");
                        $("#bank_verified_account").val("Verification Failed");
                        $("#bank_verified_name").val("Verification Failed");
                        $("#bank_verified_status").val(result.response_message || "Verification Failed");
                        showNotification("Bank Account Verification Failed", "error");
                    }
                } catch (e) {
                    showNotification("Bank Verification Parse Error", "error");
                }
            },
            error: function (xhr, status, error) {
                showNotification("Bank Verification Error", "error");
            }
        });
    }

    function verifyMSME() {
        if (vendorVerifications.MSME && vendorVerifications.MSME.status === 'Y') {
            showNotification("MSME is already verified and cannot be modified", "warning");
            return;
        }

        var msme_number = $("#msme_number").val();

        if (!validateMSME(msme_number)) {
            showNotification("Please enter a valid MSME/Udyam Registration number", "error");
            return;
        }

        $("#msme_number").prop('readonly', true);
        $("#verify_msme_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

        $("#msme_verification_fields").show();
        $("#msme_verified_enterprise_name").val("Verifying...");
        $("#msme_verified_major_activity").val("Verifying...");
        $("#msme_verified_organization_type").val("Verifying...");
        $("#msme_verified_social_category").val("Verifying...");
        $("#msme_verified_commencement_date").val("Verifying...");
        $("#msme_verified_dic_name").val("Verifying...");
        $("#msme_verified_registration_date").val("Verifying...");
        $("#msme_verified_status").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'udyog_aadhaar': msme_number,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'UDYOGAADHAAR',
                'consent': 'Y',
                'consent_text': 'I hear by declare my consent agreement for fetching my information via ZOOP AP',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);

                    if (result && result.response_code == "200" && result.result) {
                        $("#msme_verified_enterprise_name").val(result.result.name_of_enterprise || "N/A");
                        $("#msme_verified_major_activity").val(result.result.major_activity || "N/A");
                        $("#msme_verified_organization_type").val(result.result.organization_type || "N/A");
                        $("#msme_verified_social_category").val(result.result.social_category || "N/A");
                        $("#msme_verified_commencement_date").val(result.result.date_of_commencement || "N/A");
                        $("#msme_verified_dic_name").val(result.result.dic_name || "N/A");
                        $("#msme_verified_registration_date").val(result.result.applied_date || "N/A");
                        $("#msme_verified_status").val(result.response_message || "Verified");

                        verificationSteps.msme.completed = true;
                        verificationSteps.msme.data = result;

                        $("#msme_confirmation_section").show();
                        $("#verify_msme_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');

                        console.log('MSME verification successful');

                        $.ajax({
                            url: "ajax.php",
                            type: "POST",
                            data: {
                                page: 'insertvendorsandvendorregisters',
                                msme_number: msme_number,
                                transaction_table: transaction_table,
                                vendor_id: vendor_id,
                                verification_type: 'MSME',
                                api_hit_id: result.api_hit_id,
                            },
                            async: false,
                            success: function (response) {
                                try {
                                    console.log("msme response", response);
                                    location.reload();
                                } catch (err) {
                                    console.error('Insert vendor parse error:', err);
                                }
                            },
                            error: function () { }
                        });

                        showNotification("MSME Details Verified Successfully", "success");
                    } else {
                        $("#msme_verified_enterprise_name").val("Verification Failed");
                        $("#msme_verified_major_activity").val("Verification Failed");
                        $("#msme_verified_organization_type").val("Verification Failed");
                        $("#msme_verified_social_category").val("Verification Failed");
                        $("#msme_verified_commencement_date").val("Verification Failed");
                        $("#msme_verified_dic_name").val("Verification Failed");
                        $("#msme_verified_registration_date").val("Verification Failed");
                        $("#msme_verified_status").val(result.response_message || "Verification Failed");

                        showNotification("MSME Verification Failed", "error");
                    }
                } catch (e) {
                    showNotification("MSME Verification Parse Error", "error");
                }
            },
            error: function (xhr, status, error) {
                showNotification("MSME Verification Error", "error");
            }
        });
    }

    function verifyTAN() {
        if (vendorVerifications.TAN && vendorVerifications.TAN.status === 'Y') {
            showNotification("TAN is already verified and cannot be modified", "warning");
            return;
        }

        var tan_no = $("#tan_number").val();

        if (!validateTAN(tan_no)) {
            showNotification("Please enter a valid TAN number", "error");
            return;
        }

        $("#tan_number").prop('readonly', true);
        $("#verify_tan_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

        $("#tan_verification_fields").show();
        $("#tan_verified_company").val("Verifying...");
        $("#tan_verified_status").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'customer_tan_number': tan_no,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'ZOOPONETANLITE',
                'consent': 'Y',
                'consent_text': 'I hereby declare my consent agreement for fetching my information via ZOOP API',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);

                    if (result.success === true && result.result) {
                        $("#tan_verified_company").val(result.result.user_full_name || "N/A");
                        $("#tan_verified_status").val(result.success || "N/A");

                        verificationSteps.tan.completed = true;
                        verificationSteps.tan.data = result;

                        $("#tan_confirmation_section").show();
                        $("#verify_tan_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');

                        var company_name_entered = $("#company_name").val().toLowerCase().trim();
                        var tan_company_name = (result.result.user_full_name || "").toLowerCase().trim();

                        if (company_name_entered === tan_company_name) {
                            showNotification("TAN Details are Correct", "success");
                            verificationSteps.tan.nameMatch = true;

                            $.ajax({
                                url: "ajax.php",
                                type: "POST",
                                data: {
                                    page: 'insertvendorsandvendorregisters',
                                    tan_number: tan_no,
                                    transaction_table: transaction_table,
                                    vendor_id: vendor_id,
                                    verification_type: 'TAN',
                                    api_hit_id: result.api_hit_id,
                                },
                                async: false,
                                success: function (response) {
                                    try {
                                        console.log("Tan response", response);
                                        location.reload();
                                    } catch (err) {
                                        console.error('Insert vendor parse error:', err);
                                    }
                                },
                                error: function () { }
                            });

                        } else {
                            verificationSteps.tan.completed = false;
                            verificationSteps.tan.nameMatch = false;

                            $("#tan_confirmation_section").removeClass('confirmation-section').addClass('alert alert-warning');
                            $("#tan_confirmation_section h6").html('<i class="fa fa-exclamation-triangle text-warning"></i> TAN Verification Done - Data Mismatch');
                            $("#tan_confirmation_section p").html('TAN details have been retrieved but company name does not match. Please verify the information is correct before proceeding.');

                            showNotification("Verification completed but company name doesn't match with TAN company name. Please check the data.", "warning");
                            $("#tan_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');

                            $("#verify_tan_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Data Mismatch');
                        }

                    } else {
                        $("#tan_verified_company").val("Verification Failed");
                        $("#tan_verified_status").val("Verification Failed");
                        showNotification("TAN Verification Failed", "error");
                    }
                } catch (e) {
                    $("#tan_verified_company").val("Parse Error");
                    $("#tan_verified_status").val("Parse Error");
                    showNotification("TAN Verification Parse Error", "error");
                }
            },
            error: function (xhr, status, error) {
                $("#tan_verified_company").val("Error occurred");
                $("#tan_verified_status").val("Error occurred");
                showNotification("TAN Verification Error", "error");
            }
        });
    }

    function showNotification(message, type) {
        console.log("showNotification called:", message, type);
        var iconClass = '';
        var labelClass = '';

        switch (type) {
            case 'success':
                iconClass = 'fa fa-check';
                labelClass = 'label-success';
                break;
            case 'error':
                iconClass = 'fa fa-times';
                labelClass = 'label-danger';
                break;
            case 'warning':
                iconClass = 'fa fa-exclamation-triangle';
                labelClass = 'label-warning';
                break;
            default:
                iconClass = 'fa fa-info';
                labelClass = 'label-info';
        }

        var notification = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            '<i class="' + iconClass + '"></i> ' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>');

        $('#notification-container').append(notification);

        setTimeout(function () {
            notification.alert('close');
        }, 5000);
    }

    function showGSTAddressOptions(gstData) {
        var addressOptionsHtml = `
        <div class="row">
            <div class="col-lg-12">
                <h6><i class="fa fa-map-marker"></i> Address Options</h6>
                <p class="text-muted">Use the GST registered address for billing/shipping?</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6>Use for Billing Address</h6>
                        <button type="button" class="btn btn-primary btn-sm" onclick="useGSTAddress('billing')">
                            <i class="fa fa-copy"></i> Use GST Address
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6>Use for Shipping Address</h6>
                        <button type="button" class="btn btn-primary btn-sm" onclick="useGSTAddress('shipping')">
                            <i class="fa fa-copy"></i> Use GST Address
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        $("#gst_address_options").html(addressOptionsHtml);
    }

    function useGSTAddress(type) {
        var gstData = verificationSteps.gst.data;
        if (gstData && gstData.result && gstData.result.primary_business_address) {
            var addr = gstData.result.primary_business_address;
            var addressText = '';
            var addressParts = [];

            if (addr.building_name) addressParts.push(addr.building_name);
            if (addr.building_number) addressParts.push(addr.building_number);
            if (addr.street) addressParts.push(addr.street);
            if (addr.city) addressParts.push(addr.city);
            if (addr.district) addressParts.push(addr.district);
            if (addr.state) addressParts.push(addr.state);
            if (addr.pincode) addressParts.push(addr.pincode);

            addressText = addressParts.join(', ');

            window.gstAddress = {
                type: type,
                address: addressText,
                city: addr.city || '',
                state: addr.state || '',
                pincode: addr.pincode || ''
            };

            showNotification('GST address saved for ' + type + ' address', 'success');
        }
    }
 

    function showNotification(message, type) {
        var iconClass = '';
        var labelClass = '';

        switch (type) {
            case 'success':
                iconClass = 'fa fa-check';
                labelClass = 'label-success';
                break;
            case 'error':
                iconClass = 'fa fa-times';
                labelClass = 'label-danger';
                break;
            case 'warning':
                iconClass = 'fa fa-exclamation-triangle';
                labelClass = 'label-warning';
                break;
            default:
                iconClass = 'fa fa-info';
                labelClass = 'label-info';
        }

        var notification = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            '<i class="' + iconClass + '"></i> ' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>');

        $('#notification-container').append(notification);

        setTimeout(function () {
            notification.alert('close');
        }, 5000);
    }

    (function () {
        function waitForJQuery() {
            if (typeof $ !== 'undefined') {
                initVerificationSystem();
            } else {
                setTimeout(waitForJQuery, 50);
            }
        }

        waitForJQuery();

        function initializeVerificationStatus() {
            console.log("Initializing verification status...");
            console.log("Vendor verifications:", vendorVerifications);

            visibleVerifications.forEach(function (verification, index) {
                var stepNumber = index + 1;
                var isVerified = vendorVerifications[verification] &&
                    vendorVerifications[verification].status === 'Y';

                if (isVerified) {
                    var stepElement = $('.step-indicator .step').eq(stepNumber - 1);
                    stepElement.addClass('completed');
                    stepElement.find('.step-number').html('<i class="fa fa-check"></i>');
                    console.log('Marked step', stepNumber, 'as completed for', verification);
                }
            });
        }

        function getFirstIncompleteStep() {
            for (var i = 0; i < visibleVerifications.length; i++) {
                var verification = visibleVerifications[i];
                var isVerified = vendorVerifications[verification] &&
                    vendorVerifications[verification].status === 'Y';

                if (!isVerified) {
                    return i + 1;
                }
            }
            return 1;
        }

        function initVerificationSystem() {
            $(document).ready(function () {
                console.log("Document ready, initializing verification system");
                console.log("Change Info functionality has been updated with improved readonly handling");
                testJQuery();

                initializeVerificationStatus();

                updateStepIndicator();

                var firstIncompleteStep = getFirstIncompleteStep();
                currentStep = firstIncompleteStep;
                showStep(firstIncompleteStep);

                ensureVerificationDetailsReadonly();

                initializeButtonStates();

                console.log("Verification system initialized");
                console.log("All Change Info buttons should now work properly - check console for debugging info");
            });
        }

        function ensureVerificationDetailsReadonly() {
            console.log("Ensuring verification details are always readonly");

            $("#pan_verified_name, #pan_verified_type, #pan_verified_status").prop('readonly', true).addClass('custom_readonly');

            $("#gstin_legal_name, #gstin_trade_name, #gstin_registration_status, #gstin_taxpayer_type, #gstin_business_nature, #gstin_registration_date, #gstin_business_address").prop('readonly', true).addClass('custom_readonly');

            $("#bank_verified_ifsc, #bank_verified_account, #bank_verified_name, #bank_verified_status").prop('readonly', true).addClass('custom_readonly');

            $("#msme_verified_enterprise_name, #msme_verified_major_activity, #msme_verified_organization_type, #msme_verified_social_category, #msme_verified_commencement_date, #msme_verified_dic_name, #msme_verified_registration_date, #msme_verified_status").prop('readonly', true).addClass('custom_readonly');

            $("#tan_verified_company, #tan_verified_status").prop('readonly', true).addClass('custom_readonly');

            console.log("All verification details are now readonly and disabled");
        }
    })();

</script>