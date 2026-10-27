<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$verification_type = isset($_GET['verification_type']) ? $_GET['verification_type'] : (isset($_POST['verification_type']) ? $_POST['verification_type'] : '');
$record_id = isset($_GET['record_id']) ? $_GET['record_id'] : (isset($_POST['record_id']) ? $_POST['record_id'] : '');
$action_type = isset($_GET['action_type']) ? $_GET['action_type'] : (isset($_POST['action_type']) ? $_POST['action_type'] : '');
$table_name = isset($_GET['table_name']) ? $_GET['table_name'] : (isset($_POST['table_name']) ? $_POST['table_name'] : '');
$field_data_json = isset($_POST['field_data']) ? $_POST['field_data'] : '{}';

if (empty($verification_type) || empty($record_id) || empty($action_type) || empty($table_name)) {
    echo '<div class="alert alert-danger">Missing required parameters for verification</div>';
    exit;
}

$field_data = json_decode($field_data_json, true);
if (!$field_data) {
    $field_data = array();
}

$api_url = API . "?mode=getverificationdetails&record_id=" . $record_id . "&table=" . $table_name;
$response = callAPI($api_url);
$record_data = json_decode($response, true);
$record = $record_data['data'];

foreach ($field_data as $key => $value) {
    if (!empty($value)) {
        $record[$key] = $value;
    }
}

$vendor_verifications = array();
if (isset($record['vendor_verifications']) && !empty($record['vendor_verifications'])) {
    $vendor_verifications = json_decode($record['vendor_verifications'], true);
}

$verification_config = array(
    'pan' => array(
        'title' => 'PAN Card Verification',
        'fields' => array(
            'pan_no' => array('label' => 'PAN Number', 'placeholder' => 'ABCDE1234F', 'required' => true),
            'company' => array('label' => 'Company Name', 'placeholder' => 'Enter company name', 'required' => true),
            'email' => array('label' => 'Email', 'placeholder' => 'Enter email address', 'required' => true, 'type' => 'email')
        )
    ),
    'gst' => array(
        'title' => 'GST Number Verification',
        'fields' => array(
            'gstin' => array('label' => 'GST Number', 'placeholder' => '22AAAAA0000A1Z5', 'required' => true)
        )
    ),
    'bank' => array(
        'title' => 'Bank Account Verification',
        'fields' => array(
            'ifsc_code' => array('label' => 'IFSC Code', 'placeholder' => 'SBIN0001234', 'required' => true),
            'bank_account_no' => array('label' => 'Bank Account Number', 'placeholder' => 'Enter account number', 'required' => true),
            'bank_name' => array('label' => 'Bank Name', 'placeholder' => 'Enter bank name (optional)', 'required' => false)
        )
    ),
    'tan' => array(
        'title' => 'TAN Number Verification',
        'fields' => array(
            'tan_number' => array('label' => 'TAN Number', 'placeholder' => 'ABCD12345E', 'required' => true)
        )
    ),
    'msme' => array(
        'title' => 'MSME/Udyam Registration Verification',
        'fields' => array(
            'msme_number' => array('label' => 'MSME/Udyam Number', 'placeholder' => 'UDYAM-XX-00-0000000', 'required' => true)
        )
    )
);

if (!isset($verification_config[$verification_type])) {
    echo '<div class="alert alert-danger">Invalid verification type</div>';
    exit;
}

$config = $verification_config[$verification_type];

$verification_key = strtoupper($verification_type);
if ($verification_type == 'gst') {
    $verification_key = 'GSTIN';
}

$current_verification = isset($vendor_verifications[$verification_key]) ? $vendor_verifications[$verification_key] : null;
$is_verified = $current_verification && isset($current_verification['status']) && $current_verification['status'] == 'Y';

$company_name = isset($record['company']) ? $record['company'] : (isset($record['display_name']) ? $record['display_name'] : 'Unknown Company');
?>

<link rel="stylesheet" href="../assets/css/custom_list_verification_vendors_and_vendor_registers_popup.css">

<!-- Hidden data containers for JavaScript -->
<div id="js-data-container" class="js-data-container">
    <div class="js-verification-type"><?= htmlspecialchars($verification_type); ?></div>
    <div class="js-record-id"><?= htmlspecialchars($record_id); ?></div>
    <div class="js-action-type"><?= htmlspecialchars($action_type); ?></div>
    <div class="js-table-name"><?= htmlspecialchars($table_name); ?></div>
    <div class="js-is-verified"><?= $is_verified ? 'true' : 'false'; ?></div>
    <div class="js-company-name"><?= htmlspecialchars($company_name); ?></div>
    <div class="js-company-name-from-record"><?= htmlspecialchars(isset($record['company']) ? $record['company'] : (isset($record['display_name']) ? $record['display_name'] : '')); ?></div>
</div>
<div class="verification-container">
    <!-- Page Header -->
    <div class="modal-header">
        <h4 class="modal-title">
            <?= htmlspecialchars($config['title']); ?>
        </h4>
        <button type="button" class="btn-close js-close-modal"></button>
    </div>

    <div class="modal-body">
        <div class="mb-3">
            <p class="text-muted mb-0">
                <strong>Company:</strong> <?= htmlspecialchars($company_name); ?> |
                <strong>Action:</strong> <?= htmlspecialchars(ucfirst($action_type)); ?>
                <?php if ($is_verified) { ?>
                    | <span class="badge bg-success"><i class="fa fa-check"></i> Already Verified</span>
                <?php } ?>
            </p>
        </div>

        <!-- Main Verification Section -->
        <div class="verification-step">
            <?php if ($is_verified && $action_type != 'add_new') { ?>
                <!-- Already Verified Section -->
                <div class="alert alert-success">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="alert-heading mb-1">
                                <i class="fa fa-check-circle"></i> Previous Completed Verification Data
                            </h5>
                            <p class="mb-0">
                                This <?= strtolower($config['title']); ?> was successfully verified on
                                <?= date('d M Y, h:i A', strtotime($current_verification['verification_datetime'])); ?>
                            </p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary btn-sm js-show-current-data">
                                <i class="fa fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>


                <div id="current_data_section" class="mt-3 current-data-section">
                    <div class="row">
                        <?php
                        if ($verification_type == 'gst' && $current_verification) {
                            if (isset($record['gstin']) && !empty($record['gstin'])) { ?>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label fw-bold">GST Number</span>
                                    <input aria-label="Input field" type="text" class="form-control" value="<?= htmlspecialchars($record['gstin']); ?>" readonly>
                                </div>
                            <?php }

                            // Show verification details
                            if (isset($current_verification['verification_datetime']) && !empty($current_verification['verification_datetime'])) { ?>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label fw-bold">Verification Date</span>
                                    <input aria-label="Input field" type="text" class="form-control" value="<?= htmlspecialchars(date('d M Y, h:i A', strtotime($current_verification['verification_datetime']))); ?>" readonly>
                                </div>
                            <?php }  ?>
                            <?php
                        } elseif ($verification_type == 'gst') {
                            foreach ($config['fields'] as $field_name => $field_config) {
                                $current_value = isset($record[$field_name]) ? $record[$field_name] : '';
                                if (!empty($current_value)) { ?>
                                    <div class="col-md-4 mb-3">
                                        <span class="form-label fw-bold"><?= htmlspecialchars($field_config['label']); ?></span>
                                        <input aria-label="Input field" type="text" class="form-control" value="<?= htmlspecialchars($current_value); ?>" readonly>
                                    </div>
                                <?php }
                            }
                        } else {
                            foreach ($config['fields'] as $field_name => $field_config) {
                                $current_value = isset($record[$field_name]) ? $record[$field_name] : '';
                                if (!empty($current_value)) { ?>
                                    <div class="col-md-4 mb-3">
                                        <span class="form-label fw-bold"><?= htmlspecialchars($field_config['label']); ?></span>
                                        <input aria-label="Input field" type="text" class="form-control" value="<?= htmlspecialchars($current_value); ?>" readonly>
                                    </div>
                                <?php }
                            }
                        } ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($action_type == 'insert') { ?>

                <form id="verificationForm" method="post">
                    <input type="hidden" name="verification_type" value="<?= htmlspecialchars($verification_type); ?>">
                    <input type="hidden" name="record_id" value="<?= htmlspecialchars($record_id); ?>">
                    <input type="hidden" name="action_type" value="<?= htmlspecialchars($action_type); ?>">
                    <input type="hidden" name="table_name" value="<?= htmlspecialchars($table_name); ?>">
                    <input type="hidden" id="company_check_status" name="company_check_status" value="">

                    <?php if ($verification_type == 'pan') { ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <span class="form-label">
                                    Company Email <span class="text-danger">*</span>
                                </span>
                                <input aria-label="Enter company email address" type="email"
                                       class="form-control"
                                       name="email"
                                       id="company_email"
                                       placeholder="Enter company email address"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">
                                    PAN Number <span class="text-danger">*</span>
                                </span>
                                <input aria-label="ABCDE1234F" type="text"
                                       class="form-control js-format-pan-input"
                                       name="pan_no"
                                       id="pan_number"
                                       placeholder="ABCDE1234F"
                                       maxlength="10"
                                       required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <span class="form-label">
                                    Company Name <span class="text-danger">*</span>
                                </span>
                                <input aria-label="Enter company name" type="text"
                                       class="form-control js-check-company-name"
                                       name="company"
                                       id="company_name"
                                       placeholder="Enter company name"
                                       required>
                                <div id="allreadycompanyname" class="alert alert-warning mt-2 company-name-warning">
                                    <i class="fa fa-exclamation-triangle"></i> Company name already exists. Please use a different name.
                                </div>
                            </div>
                        </div>
                    <?php } elseif ($verification_type == 'bank') { ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <span class="form-label">
                                    IFSC Code <span class="text-danger">*</span>
                                </span>
                                <input aria-label="SBIN0001234" type="text"
                                       class="form-control js-format-ifsc-input"
                                       name="ifsc_code"
                                       id="ifsc_code"
                                       placeholder="SBIN0001234"
                                       maxlength="11"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">
                                    Bank Account Number <span class="text-danger">*</span>
                                </span>
                                <input aria-label="Enter account number" type="text"
                                       class="form-control js-format-bank-account-input"
                                       name="bank_account_no"
                                       id="bank_account_no"
                                       placeholder="Enter account number"
                                       maxlength="18"
                                       required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <span class="form-label">
                                    Bank Name
                                </span>
                                <input aria-label="Enter bank name (optional)" type="text"
                                       class="form-control"
                                       name="bank_name"
                                       id="bank_name"
                                       placeholder="Enter bank name (optional)">
                            </div>
                        </div>
                    <?php } elseif ($verification_type == 'tan') { ?>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <span class="form-label">
                                    TAN Number <span class="text-danger">*</span>
                                </span>
                                <input aria-label="ABCD12345E" type="text"
                                       class="form-control js-format-tan-input"
                                       name="tan_number"
                                       id="tan_number"
                                       placeholder="ABCD12345E"
                                       maxlength="10"
                                       required>
                            </div>
                        </div>
                    <?php } elseif ($verification_type == 'msme') { ?>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <span class="form-label">
                                    MSME/Udyam Number <span class="text-danger">*</span>
                                </span>
                                <input aria-label="UDYAM-XX-00-0000000" type="text"
                                       class="form-control js-format-msme-input"
                                       name="msme_number"
                                       id="msme_number"
                                       placeholder="UDYAM-XX-00-0000000"
                                       maxlength="19"
                                       required>
                            </div>
                        </div>
                    <?php } elseif ($verification_type == 'gst') { ?>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <span class="form-label">
                                    GST Number <span class="text-danger">*</span>
                                </span>
                                <input aria-label="22AAAAA0000A1Z5" type="text"
                                       class="form-control js-format-gst-input js-check-gst-availability"
                                       name="gstin"
                                       id="gstin"
                                       placeholder="22AAAAA0000A1Z5"
                                       maxlength="15"
                                       required>
                            </div>
                        </div>
                    <?php } else { ?>

                        <div class="row">
                            <?php foreach ($config['fields'] as $field_name => $field_config) {
                                $current_value = isset($record[$field_name]) ? $record[$field_name] : '';
                                $field_type = isset($field_config['type']) ? $field_config['type'] : 'text';
                                ?>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label">
                                        <?= htmlspecialchars($field_config['label']); ?>
                                        <?php if ($field_config['required']) { ?>
                                            <span class="text-danger">*</span>
                                        <?php } ?>
                                    </span>
                                    <input aria-label="Input field" type="<?= $field_type; ?>"
                                           class="form-control"
                                           name="<?= $field_name; ?>"
                                           id="<?= $field_name; ?>"
                                           placeholder="<?= htmlspecialchars($field_config['placeholder']); ?>"
                                           value="<?= htmlspecialchars($current_value); ?>"
                                           <?= $field_config['required'] ? 'required' : ''; ?>>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </form>

            <?php } elseif ($action_type == 'change') { ?>

                <form id="verificationForm_2" method="post">
                    <input type="hidden" name="verification_type" value="<?= htmlspecialchars($verification_type); ?>">
                    <input type="hidden" name="record_id" value="<?= htmlspecialchars($record_id); ?>">
                    <input type="hidden" name="action_type" value="<?= htmlspecialchars($action_type); ?>">
                    <input type="hidden" name="table_name" value="<?= htmlspecialchars($table_name); ?>">
                    <input type="hidden" id="company_check_status_2" name="company_check_status" value="available">
                    <?php if ($verification_type == 'gst') { ?>
                        <input type="hidden" name="gst_type" value="old" id="gst_type">
                        <input type="hidden" name="gst_default" value="old" id="gst_default">
                    <?php } ?>

                    <?php if ($verification_type == 'pan') { ?>

                        <h6>Enter New PAN Number :</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <span class="form-label">
                                    Company Email <span class="text-danger">*</span>
                                </span>
                                <input aria-label="Email" type="email"
                                       class="form-control"
                                       name="email"
                                       id="company_email_2"
                                       value="<?= htmlspecialchars(isset($record['email']) ? $record['email'] : ''); ?>"
                                       readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">
                                    Company Name <span class="text-danger">*</span>
                                </span>
                                <input aria-label="Company" type="text"
                                       class="form-control"
                                       name="company"
                                       id="company_name_2"
                                       value="<?= htmlspecialchars(isset($record['company']) ? $record['company'] : ''); ?>"
                                       readonly>
                            </div>
                            <div class="col-md-12 mb-3">
                                <span class="form-label">
                                    New PAN Number <span class="text-danger">*</span>
                                </span>
                                <input aria-label="ABCDE1234F" type="text"
                                       class="form-control js-format-pan-input"
                                       name="pan_no"
                                       id="pan_number_2"
                                       placeholder="ABCDE1234F"
                                       maxlength="10"
                                       required>
                            </div>
                        </div>
                    <?php } else { ?>

                        <?php if ($verification_type == 'bank') { ?>
                            <h6>Enter New Bank Details:</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <span class="form-label">
                                        New Bank Name
                                    </span>
                                    <input aria-label="Enter bank name (optional)" type="text"
                                           class="form-control"
                                           name="bank_name"
                                           id="bank_name_2"
                                           placeholder="Enter bank name (optional)">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label">
                                        New IFSC Code <span class="text-danger">*</span>
                                    </span>
                                    <input aria-label="SBIN0001234" type="text"
                                           class="form-control js-format-ifsc-input"
                                           name="ifsc_code"
                                           id="ifsc_code_2"
                                           placeholder="SBIN0001234"
                                           maxlength="11"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label">
                                        New Bank Account Number <span class="text-danger">*</span>
                                    </span>
                                    <input aria-label="Enter account number" type="text"
                                           class="form-control js-format-bank-account-input"
                                           name="bank_account_no"
                                           id="bank_account_no_2"
                                           placeholder="Enter account number"
                                           maxlength="18"
                                           required>
                                </div>
                            </div>
                        <?php } elseif ($verification_type == 'tan') { ?>
                            <h6>Enter New TAN Number:</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <span class="form-label">
                                        New TAN Number <span class="text-danger">*</span>
                                    </span>
                                    <input aria-label="ABCD12345E" type="text"
                                           class="form-control js-format-tan-input"
                                           name="tan_number"
                                           id="tan_number_2"
                                           placeholder="ABCD12345E"
                                           maxlength="10"
                                           required>
                                </div>
                            </div>
                        <?php } elseif ($verification_type == 'msme') { ?>
                            <h6>Enter New MSME Number:</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <span class="form-label">
                                        New MSME/Udyam Number <span class="text-danger">*</span>
                                    </span>
                                    <input aria-label="UDYAM-XX-00-0000000" type="text"
                                           class="form-control js-format-msme-input"
                                           name="msme_number"
                                           id="msme_number_2"
                                           placeholder="UDYAM-XX-00-0000000"
                                           maxlength="19"
                                           required>
                                </div>
                            </div>
                        <?php } elseif ($verification_type == 'gst') { ?>
                            <h6>Enter New GST Number:</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <span class="form-label">
                                        New GST Number <span class="text-danger">*</span>
                                    </span>
                                    <input aria-label="22AAAAA0000A1Z5" type="text"
                                           class="form-control js-format-gst-input js-check-gst-availability"
                                           name="gstin"
                                           id="gstin_2"
                                           placeholder="22AAAAA0000A1Z5"
                                           maxlength="15"
                                           required>
                                </div>
                            </div>
                        <?php } else { ?>
                            <h6>Enter New Information:</h6>
                            <div class="row">
                                <?php foreach ($config['fields'] as $field_name => $field_config) {
                                    $field_type = isset($field_config['type']) ? $field_config['type'] : 'text';
                                    ?>
                                    <div class="col-md-6 mb-3">
                                        <span class="form-label">
                                            New <?= htmlspecialchars($field_config['label']); ?>
                                            <?php if ($field_config['required']) { ?>
                                                <span class="text-danger">*</span>
                                            <?php } ?>
                                        </span>
                                        <input aria-label="Input field" type="<?= $field_type; ?>"
                                               class="form-control"
                                               name="<?= $field_name; ?>"
                                               id="<?= $field_name; ?>"
                                               placeholder="<?= htmlspecialchars($field_config['placeholder']); ?>"
                                               <?= $field_config['required'] ? 'required' : ''; ?>>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </form>


                <?php if ($verification_type == 'pan') { ?>
                    <div id="pan_verification_fields" class="verification-fields mt-4 verification-fields-section">
                        <h6><i class="fa fa-search"></i> Verification Results</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <span class="form-label">Verified Name</span>
                                <input aria-label="Pan Verified Name" type="text" class="form-control" id="pan_verified_name" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <span class="form-label">PAN Type</span>
                                <input aria-label="Pan Verified Type" type="text" class="form-control" id="pan_verified_type" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <span class="form-label">PAN Status</span>
                                <input aria-label="Pan Verified Status" type="text" class="form-control" id="pan_verified_status" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation Section -->
                    <div id="pan_confirmation_section" class="confirmation-section mt-4 confirmation-section-hidden">
                        <h6><i class="fa fa-check-circle text-success"></i> PAN Verification Completed</h6>
                        <p>PAN details have been successfully verified and saved.</p>
                    </div>
                <?php } elseif ($verification_type == 'bank') { ?>
                    <div id="bank_verification_fields" class="verification-fields mt-4 verification-fields-section">
                        <h6><i class="fa fa-search"></i> Bank Verification Results</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Verified IFSC</span>
                                <input aria-label="Bank Verified Ifsc" type="text" class="form-control" id="bank_verified_ifsc" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Verified Account</span>
                                <input aria-label="Bank Verified Account" type="text" class="form-control" id="bank_verified_account" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Account Holder Name</span>
                                <input aria-label="Bank Verified Name" type="text" class="form-control" id="bank_verified_name" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Verification Status</span>
                                <input aria-label="Bank Verified Status" type="text" class="form-control" id="bank_verified_status" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Bank Name</span>
                                <input aria-label="Bank Verified Bank Name" type="text" class="form-control" id="bank_verified_bank_name" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Confirmation Section -->
                    <div id="bank_manual_confirmation_section" class="confirmation-section mt-4 manual-confirmation-section">
                        <h6><i class="fa fa-exclamation-triangle text-warning"></i> Confirm Bank Details</h6>
                        <p>Please confirm the bank account details are correct:</p>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>IFSC Code:</strong> <span id="confirm_ifsc_display"></span>
                            </div>
                            <div class="col-md-3">
                                <strong>Account Number:</strong> <span id="confirm_account_display"></span>
                            </div>
                            <div class="col-md-3">
                                <strong>Account Holder:</strong> <span id="confirm_name_display"></span>
                            </div>
                            <div class="col-md-3">
                                <strong>Bank Name:</strong> <span id="confirm_bank_name_display"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary js-cancel-bank-confirmation">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-success js-confirm-bank-details">
                                <i class="fa fa-check"></i> Confirm & Save
                            </button>
                        </div>
                    </div>

                    <!-- Confirmation Section -->
                    <div id="bank_confirmation_section" class="confirmation-section mt-4 confirmation-section-hidden">
                        <h6><i class="fa fa-check-circle text-success"></i> Bank Verification Completed</h6>
                        <p>Bank account details have been successfully verified and saved.</p>
                    </div>
                <?php } elseif ($verification_type == 'tan') { ?>
                    <div id="tan_verification_fields" class="verification-fields mt-4 verification-fields-section">
                        <h6><i class="fa fa-search"></i> TAN Verification Results</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Verified TAN Number</span>
                                <input aria-label="Tan Verified Number" type="text" class="form-control" id="tan_verified_number" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Company Name</span>
                                <input aria-label="Tan Verified Company" type="text" class="form-control" id="tan_verified_company" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">TAN Status</span>
                                <input aria-label="Tan Verified Status" type="text" class="form-control" id="tan_verified_status" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Verification Status</span>
                                <input aria-label="Tan Verification Status" type="text" class="form-control" id="tan_verification_status" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation Section -->
                    <div id="tan_confirmation_section" class="confirmation-section mt-4 confirmation-section-hidden">
                        <h6><i class="fa fa-check-circle text-success"></i> TAN Verification Completed</h6>
                        <p>TAN details have been successfully verified and saved.</p>
                    </div>
                <?php } elseif ($verification_type == 'msme') { ?>
                    <div id="msme_verification_fields" class="verification-fields mt-4 verification-fields-section">
                        <h6><i class="fa fa-search"></i> MSME Verification Results</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Enterprise Name</span>
                                <input aria-label="Msme Verified Enterprise Name" type="text" class="form-control" id="msme_verified_enterprise_name" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Major Activity</span>
                                <input aria-label="Msme Verified Major Activity" type="text" class="form-control" id="msme_verified_major_activity" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Organization Type</span>
                                <input aria-label="Msme Verified Organization Type" type="text" class="form-control" id="msme_verified_organization_type" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Social Category</span>
                                <input aria-label="Msme Verified Social Category" type="text" class="form-control" id="msme_verified_social_category" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Commencement Date</span>
                                <input aria-label="Msme Verified Commencement Date" type="text" class="form-control" id="msme_verified_commencement_date" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">DIC Name</span>
                                <input aria-label="Msme Verified Dic Name" type="text" class="form-control" id="msme_verified_dic_name" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Registration Date</span>
                                <input aria-label="Msme Verified Registration Date" type="text" class="form-control" id="msme_verified_registration_date" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Verification Status</span>
                                <input aria-label="Msme Verified Status" type="text" class="form-control" id="msme_verified_status" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- MSME Manual Confirmation Section -->
                    <div id="msme_manual_confirmation_section" class="confirmation-section mt-4 manual-confirmation-section">
                        <h6><i class="fa fa-exclamation-triangle text-warning"></i> MSME Verification Result</h6>
                        <div id="msme_comparison_content"></div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-secondary js-cancel-msme-confirmation">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-success js-proceed-msme-save">
                                <i class="fa fa-check"></i> Yes, Proceed
                            </button>
                        </div>
                    </div>

                    <!-- Confirmation Section -->
                    <div id="msme_confirmation_section" class="confirmation-section mt-4 confirmation-section-hidden">
                        <h6><i class="fa fa-check-circle text-success"></i> MSME Verification Completed</h6>
                        <p>MSME details have been successfully verified and saved.</p>
                    </div>
                <?php } elseif ($verification_type == 'gst') { ?>
                    <div id="gst_verification_fields" class="verification-fields mt-4 verification-fields-section">
                        <h6><i class="fa fa-search"></i> GST Verification Results</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Legal Name</span>
                                <input aria-label="Gstin Legal Name" type="text" class="form-control" id="gstin_legal_name" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Trade Name</span>
                                <input aria-label="Gstin Trade Name" type="text" class="form-control" id="gstin_trade_name" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Registration Status</span>
                                <input aria-label="Gstin Registration Status" type="text" class="form-control" id="gstin_registration_status" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Taxpayer Type</span>
                                <input aria-label="Gstin Taxpayer Type" type="text" class="form-control" id="gstin_taxpayer_type" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Business Nature</span>
                                <input aria-label="Gstin Business Nature" type="text" class="form-control" id="gstin_business_nature" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="form-label">Registration Date</span>
                                <input aria-label="Gstin Registration Date" type="text" class="form-control" id="gstin_registration_date" readonly>
                            </div>
                            <div class="col-md-12 mb-3">
                                <span class="form-label">Business Address</span>
                                <textarea aria-label="Gstin Business Address" class="form-control" id="gstin_business_address" rows="2" readonly></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- GST Company Comparison Section -->
                    <div id="gst_company_comparison_section" class="confirmation-section mt-4 manual-confirmation-section">
                        <h6><i class="fa fa-exclamation-triangle text-warning"></i> GST Verification Result</h6>
                        <div id="gst_comparison_content"></div>
                    </div>

                    <!-- Confirmation Section -->
                    <div id="gst_confirmation_section" class="confirmation-section mt-4 confirmation-section-hidden">
                        <h6><i class="fa fa-check-circle text-success"></i> GST Verification Completed</h6>
                        <p>GST details have been successfully verified and saved.</p>
                    </div>
                <?php } ?>


                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary js-close-modal">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <?php if ($verification_type == 'pan') { ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary js-verify-pan btn-hidden" id="verify_pan_btn">
                                        <i class="fa fa-search"></i> Verify PAN
                                    </button>
                                    <button type="button" class="btn btn-warning js-toggle-edit btn-hidden" id="pan_edit_btn" data-type="pan">
                                        <i class="fa fa-edit"></i> Change Info
                                    </button>
                                </div>
                            <?php } elseif ($verification_type == 'bank') { ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary js-verify-bank btn-hidden" id="verify_bank_btn">
                                        <i class="fa fa-search"></i> Verify Bank Account
                                    </button>
                                    <button type="button" class="btn btn-warning js-toggle-edit btn-hidden" id="bank_edit_btn" data-type="bank">
                                        <i class="fa fa-edit"></i> Change Info
                                    </button>
                                </div>
                            <?php } elseif ($verification_type == 'tan') { ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary js-verify-tan btn-hidden" id="verify_tan_btn">
                                        <i class="fa fa-search"></i> Verify TAN
                                    </button>
                                    <button type="button" class="btn btn-warning js-toggle-edit btn-hidden" id="tan_edit_btn" data-type="tan">
                                        <i class="fa fa-edit"></i> Change Info
                                    </button>
                                </div>
                            <?php } elseif ($verification_type == 'msme') { ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary js-verify-msme btn-hidden" id="verify_msme_btn">
                                        <i class="fa fa-search"></i> Verify MSME
                                    </button>
                                    <button type="button" class="btn btn-warning js-toggle-edit btn-hidden" id="msme_edit_btn" data-type="msme">
                                        <i class="fa fa-edit"></i> Change Info
                                    </button>
                                </div>
                            <?php } elseif ($verification_type == 'gst' && $action_type == 'add_new') { ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary js-verify-gst-new btn-hidden" id="verify_gst_new_btn">
                                        <i class="fa fa-search"></i> Verify New GST
                                    </button>
                                    <button type="button" class="btn btn-warning js-toggle-edit btn-hidden" id="gst_new_edit_btn" data-type="gst_new">
                                        <i class="fa fa-edit"></i> Change Info
                                    </button>
                                </div>
                            <?php } elseif ($verification_type == 'gst') { ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary js-verify-gst btn-hidden" id="verify_gst_btn">
                                        <i class="fa fa-search"></i> Verify GST
                                    </button>
                                    <button type="button" class="btn btn-warning js-toggle-edit btn-hidden" id="gst_edit_btn" data-type="gst">
                                        <i class="fa fa-edit"></i> Change Info
                                    </button>
                                </div>
                            <?php } else { ?>
                                <button type="button" class="btn btn-primary js-save-verification-data">
                                    <i class="fa fa-save"></i>
                                    <?php if ($action_type == 'insert') { ?>
                                        Save & Verify
                                    <?php } elseif ($action_type == 'change') { ?>
                                        Update & Re-verify
                                    <?php } else { ?>
                                        Save
                                    <?php } ?>
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            <?php } elseif ($action_type == 'add_new' && $verification_type == 'gst') { ?>

                <?php
                $existing_gst = isset($record['gstin']) ? $record['gstin'] : '';
                if (!empty($existing_gst)) { ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fa fa-list"></i> Current GST Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <span class="form-label fw-bold">Current GST Number</span>
                                    <div class="input-group">
                                        <input aria-label="Input field" type="text" class="form-control" value="<?= htmlspecialchars($existing_gst); ?>" readonly>
                                    </div>
                                </div>

                                <?php if ($is_verified && $current_verification) { ?>
                                    <div class="col-md-6 mb-3">
                                        <span class="form-label fw-bold">Verification Date</span>
                                        <input aria-label="Input field" type="text" class="form-control"
                                               value="<?= date('d M Y, h:i A', strtotime($current_verification['verification_datetime'])); ?>"
                                               readonly>
                                    </div>
                                <?php } ?>
                            </div>

                            <?php if ($is_verified) { ?>
                                <div class="alert alert-success mt-3">
                                    <i class="fa fa-info-circle"></i>
                                    <strong>Note:</strong> This GST number is already verified and active.
                                    You can add additional GST numbers for different business locations below.
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-warning mb-4">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>No existing GST number found.</strong> This will be your first GST number.
                    </div>
                <?php } ?>


                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fa fa-plus"></i> Add New GST Number</h6>
                    </div>
                    <div class="card-body">
                        <form id="verificationForm_3" method="post">
                            <input type="hidden" name="verification_type" value="<?= htmlspecialchars($verification_type); ?>">
                            <input type="hidden" name="record_id" value="<?= htmlspecialchars($record_id); ?>">
                            <input type="hidden" name="action_type" value="<?= htmlspecialchars($action_type); ?>">
                            <input type="hidden" name="table_name" value="<?= htmlspecialchars($table_name); ?>">
                            <input type="hidden" name="gst_type" value="new" id="gst_type_2">
                            <input type="hidden" name="gst_default" value="new" id="gst_default_2">

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <span class="form-label">
                                        New GST Number <span class="text-danger">*</span>
                                    </span>
                                    <input aria-label="22AAAAA0000A1Z5" type="text"
                                           class="form-control js-format-gst-input js-check-gst-availability"
                                           name="gstin"
                                           id="gstin_3"
                                           placeholder="22AAAAA0000A1Z5"
                                           maxlength="15"
                                           required>
                                </div>
                            </div>
                        </form>

                        <!-- GST Verification Fields for Add New -->
                        <div id="gst_verification_fields_new" class="verification-fields mt-4 gst-verification-fields-new">
                            <h6><i class="fa fa-search"></i> New GST Verification Results</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <span class="form-label">Legal Name</span>
                                    <input aria-label="Gstin Legal Name New" type="text" class="form-control" id="gstin_legal_name_new" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label">Trade Name</span>
                                    <input aria-label="Gstin Trade Name New" type="text" class="form-control" id="gstin_trade_name_new" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label">Registration Status</span>
                                    <input aria-label="Gstin Registration Status New" type="text" class="form-control" id="gstin_registration_status_new" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label">Taxpayer Type</span>
                                    <input aria-label="Gstin Taxpayer Type New" type="text" class="form-control" id="gstin_taxpayer_type_new" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label">Business Nature</span>
                                    <input aria-label="Gstin Business Nature New" type="text" class="form-control" id="gstin_business_nature_new" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="form-label">Registration Date</span>
                                    <input aria-label="Gstin Registration Date New" type="text" class="form-control" id="gstin_registration_date_new" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <span class="form-label">Business Address</span>
                                    <textarea aria-label="Gstin Business Address New" class="form-control" id="gstin_business_address_new" rows="2" readonly></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- GST Company Comparison Section for Add New -->
                        <div id="gst_company_comparison_section_new" class="confirmation-section mt-4 gst-comparison-section-new">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> New GST Verification Result</h6>
                            <div id="gst_comparison_content_new"></div>
                        </div>

                        <!-- Confirmation Section for Add New -->
                        <div id="gst_confirmation_section_new" class="confirmation-section mt-4 gst-confirmation-section-new">
                            <h6><i class="fa fa-check-circle text-success"></i> New GST Verification Completed</h6>
                            <p>New GST details have been successfully verified and saved.</p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script type="text/javascript" src="../assets/js/custom_list_verification_vendors_and_vendor_registers_popup.js"></script>
