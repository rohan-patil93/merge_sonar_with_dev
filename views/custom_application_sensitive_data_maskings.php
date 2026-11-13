<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}
$_SESSION['apply_js']['multiselest'] = 1;
// 1. Authorization: Allow ERP administrator / user to configure policy
$isAdmin = true;
$companyId = !empty($_SESSION['company_id']) ? (int)$_SESSION['company_id'] : 1;
$userId    = !empty($_SESSION['userid']) ? (int)$_SESSION['userid'] : 1;
// CSRF token generation
if (empty($_SESSION['masking_csrf_token'])) {
    $_SESSION['masking_csrf_token'] = bin2hex(random_bytes(16));
}
$csrfToken = $_SESSION['masking_csrf_token'];
$successMessage = '';
$errorMessage   = '';
// 2. Handle POST Request to Save/Update Policy strictly via api.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_save_policy'])) {
    $targetCompanyId = !empty($_POST['company_id']) ? (int)$_POST['company_id'] : $companyId;
    $postUrl = API . "?mode=save_sensitive_data_policy&method=post";
    $postPayload = $_POST;
    $postPayload['company_id'] = $targetCompanyId;
    $postPayload['user_id']    = $userId;

    if (!empty($_POST['restrictions']) && is_array($_POST['restrictions'])) {
        $postPayload['restrictions_json'] = json_encode($_POST['restrictions']);
    }

    $apiResponse = file_post_contents($postUrl, $postPayload, 'update');
    $saveResult = json_decode($apiResponse, true);
    if (!empty($saveResult) && (!empty($saveResult['success']) || (isset($saveResult['status']) && $saveResult['status'] === 'success'))) {
        $successMessage = __('Sensitive data restriction policy successfully saved and applied via API!');
        if (isset($_SESSION['sensitive_data_policy'][$targetCompanyId])) {
            unset($_SESSION['sensitive_data_policy'][$targetCompanyId]);
        }
    } else {
        $errorMessage = $saveResult['message'] ?? __('Failed to save sensitive data restriction policy via API.');
    }
}

// 3. Handle Cache Refresh Action
if (isset($_GET['refresh_cache']) && $_GET['refresh_cache'] == 1) {
    if (isset($_SESSION['sensitive_data_policy'][$companyId])) {
        unset($_SESSION['sensitive_data_policy'][$companyId]);
    }
    $successMessage = __('Session policy cache refreshed successfully from API.');
}

// 4. Retrieve Active Policy & Metadata strictly via api.php (GET)
$getUrl = API . "?mode=get_sensitive_data_policy&company_id=" . $companyId . "&method=get";
$apiRes = callAPI($getUrl);
$currentPolicy = json_decode($apiRes, true);

if (empty($currentPolicy) || !isset($currentPolicy['success'])) {
    $currentPolicy = [
        'success' => true,
        'company_id' => $companyId,
        'policy_active' => false,
        'policy_id' => null,
        'policy_name' => 'Sensitive Data Restriction Policy',
        'from_date' => null,
        'to_date' => null,
        'restrictions' => []
    ];
}
$_SESSION['sensitive_data_policy'][$companyId] = $currentPolicy;

$supportedTypes = function_exists('getAllSupportedSensitiveTypes') ? getAllSupportedSensitiveTypes() : [
    'email' => [
        'name' => 'Email Address',
        'sample_original' => 'username@gmail.com',
        'show_first' => 1,
        'show_last' => 0,
        'mask_char' => '*',
        'preserve_domain' => 1,
        'preserve_separators' => 1
    ],
    'pan' => [
        'name' => 'PAN Number',
        'sample_original' => 'ABCDE1234F',
        'show_first' => 2,
        'show_last' => 2,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'gst' => [
        'name' => 'GST Number',
        'sample_original' => '27ABCDE1234F1Z5',
        'show_first' => 2,
        'show_last' => 3,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'mobile' => [
        'name' => 'Mobile Number',
        'sample_original' => '9876543210',
        'show_first' => 2,
        'show_last' => 2,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'aadhaar' => [
        'name' => 'Aadhaar Number',
        'sample_original' => '123456789012',
        'show_first' => 0,
        'show_last' => 4,
        'mask_char' => 'X',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'bank_account' => [
        'name' => 'Bank Account Number',
        'sample_original' => '1234567890123456',
        'show_first' => 0,
        'show_last' => 4,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'ifsc' => [
        'name' => 'IFSC Code',
        'sample_original' => 'HDFC0001234',
        'show_first' => 4,
        'show_last' => 0,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'passport' => [
        'name' => 'Passport Number',
        'sample_original' => 'A1234567',
        'show_first' => 1,
        'show_last' => 1,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'driving_licence' => [
        'name' => 'Driving Licence',
        'sample_original' => 'DL-1420110012345',
        'show_first' => 2,
        'show_last' => 4,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'pf_number' => [
        'name' => 'PF Number',
        'sample_original' => 'MH/BAN/0012345/000/0001234',
        'show_first' => 2,
        'show_last' => 4,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'uan_number' => [
        'name' => 'UAN Number',
        'sample_original' => '100912345678',
        'show_first' => 2,
        'show_last' => 2,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
    'gosi_number' => [
        'name' => 'GOSI Number',
        'sample_original' => '123456789',
        'show_first' => 2,
        'show_last' => 2,
        'mask_char' => '*',
        'preserve_domain' => 0,
        'preserve_separators' => 1
    ],
];

// Policy status calculation
$isPolicyActive = !empty($currentPolicy['policy_active']);
$today = date('Y-m-d');
$statusBadge = '<span class="badge bg-danger">Inactive</span>';
if ($isPolicyActive) {
    $statusBadge = '<span class="badge bg-success">Active & Effective</span>';
} elseif (!empty($currentPolicy['from_date']) && $today < $currentPolicy['from_date']) {
    $statusBadge = '<span class="badge bg-warning text-dark">Scheduled (Effective from ' . htmlspecialchars($currentPolicy['from_date']) . ')</span>';
} elseif (!empty($currentPolicy['to_date']) && $today > $currentPolicy['to_date']) {
    $statusBadge = '<span class="badge bg-danger">Expired (Ended on ' . htmlspecialchars($currentPolicy['to_date']) . ')</span>';
}
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <?php /** ?>
        <!-- Page Header -->
        <div class="page-header card mb-4 p-3 shadow-sm" style="border-radius: 8px; border-left: 5px solid #3699ff;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1 text-primary fw-bold">
                        <i class="feather icon-shield text-primary me-2"></i> Sensitive Data Restriction Policy Management
                    </h4>
                    <p class="text-muted mb-0">
                        Configure dynamic character-count masking, starting/ending visible characters, and domain visibility rules.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <span class="badge bg-light text-dark border me-2">Company ID: <?php echo htmlspecialchars($companyId); ?></span>
                    <?php echo $statusBadge; ?>
                    <a href="index.php?option=COM_application_SENSITIVE_DATA_MASKINGS&refresh_cache=1" class="btn btn-sm btn-outline-secondary ms-2" title="Flush & Refresh Session Cache">
                        <i class="feather icon-refresh-cw"></i> Refresh Cache
                    </a>
                </div>
            </div>
        </div>
        <?php **/ ?>

        <!-- Alert Notifications -->
        <div id="alertContainer">
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="feather icon-check-circle me-2"></i> <strong><?= __("Success:") ?></strong> <?php echo htmlspecialchars($successMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="feather icon-alert-triangle me-2"></i> <strong><?= __("Error:") ?></strong> <?php echo htmlspecialchars($errorMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$isAdmin): ?>
            <div class="alert alert-warning border-warning shadow-sm" role="alert">
                <h5 class="alert-heading fw-bold"><i class="feather icon-lock me-2"></i> <?= __("Read-Only View") ?></h5>
                <p class="mb-0"><?= __("You do not have administrator permissions to modify Sensitive Data Restriction policies. The settings below are displayed in read-only mode.") ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?option=COM_APPLICATION_SENSITIVE_DATA_MASKINGS" id="formSensitiveDataPolicy">
            <input type="hidden" name="action_save_policy" value="1">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="company_id" value="<?php echo htmlspecialchars($companyId); ?>">

            <div class="row">
                <!-- Master Policy Configuration Card -->
                <div class="card">
                    <div class="card-header">
                        <h5><?= __("Contracts") ?></h5>
                         <div class="card-header-right table-card-header">
                             <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 me-5 desktop-view"> <i class="icofont icofont-reply"></i><?= __("Back") ?></a>
                        </div>
                        <div class="card-body mt-2">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold"><?= __("Policy Name *") ?></label>
                                    <input type="text" class="form-control" name="policy_name" 
                                           value="<?php echo htmlspecialchars($currentPolicy['policy_name'] ?: 'Corporate Sensitive Data Policy'); ?>" 
                                           required <?php echo !$isAdmin ? 'disabled' : ''; ?>>
                                    <input type="hidden" class="form-control" name="is_active" value="0">
                                    
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold"><?= __("From Date") ?></label>
                                    <input type="date" class="form-control" name="from_date" id="from_date" 
                                           value="<?php echo htmlspecialchars($currentPolicy['from_date'] ?? date('Y-01-01')); ?>" 
                                           <?php echo !$isAdmin ? 'disabled' : ''; ?>>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold"><?= __("To Date") ?></label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" 
                                           value="<?php echo htmlspecialchars($currentPolicy['to_date'] ?? date('Y-12-31', strtotime('+4 years'))); ?>" 
                                           <?php echo !$isAdmin ? 'disabled' : ''; ?>>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label class="form-label fw-bold"><?= __("Policy Description / Business Notes") ?></label>
                                    <textarea class="form-control" name="description" rows="2" placeholder="<?= __("Explain the data protection compliance objectives...") ?>" <?php echo !$isAdmin ? 'disabled' : ''; ?>><?php echo htmlspecialchars($currentPolicy['description'] ?? 'Automated masking policy for sensitive personal and financial identifiers.'); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurable Dynamic Masking Rules Table -->
                <div class="">
                    <div class="card shadow-sm" style="border-radius: 8px;">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="feather icon-sliders text-primary me-2"></i> <?= __("Configurable Dynamic Masking Settings") ?>
                            </h5>
                            <?php if ($isAdmin): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddCustomType">
                                    <i class="feather icon-plus me-1"></i> <?= __("Add Custom Field") ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="tableSensitiveRules">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 18%;"><?= __("Sensitive Data Type") ?></th>
                                            <th style="width: 12%; text-align: center;"><?= __("Masking") ?></th>
                                            <th style="width: 10%;"><?= __("Show Start") ?></th>
                                            <th style="width: 10%;"><?= __("Show End") ?></th>
                                            <th style="width: 9%;"><?= __("Mask Char") ?></th>
                                            <th style="width: 13%;"><?= __("Preserve Domain / Sep") ?></th>
                                            <th style="width: 14%;"><?= __("Sample Original") ?></th>
                                            <th style="width: 14%;"><?= __("Restricted Display (Live Preview)") ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        foreach ($supportedTypes as $typeKey => $meta):
                                            $isRestricted = true;
                                            $showFirst = $meta['show_first'];
                                            $showLast  = $meta['show_last'];
                                            $maskChar  = $meta['mask_char'];
                                            $preserveDomain = $meta['preserve_domain'];
                                            $preserveSeparators = $meta['preserve_separators'];

                                            if (isset($currentPolicy['restrictions'][$typeKey])) {
                                                $cfg = $currentPolicy['restrictions'][$typeKey];
                                                $isRestricted = (bool)$cfg['is_restricted'];
                                                if (isset($cfg['show_first'])) $showFirst = (int)$cfg['show_first'];
                                                if (isset($cfg['show_last']))  $showLast  = (int)$cfg['show_last'];
                                                if (!empty($cfg['mask_char'])) $maskChar  = $cfg['mask_char'];
                                                if (isset($cfg['preserve_domain'])) $preserveDomain = (int)$cfg['preserve_domain'];
                                                if (isset($cfg['preserve_separators'])) $preserveSeparators = (int)$cfg['preserve_separators'];
                                            }

                                            $sampleMasked = maskValueDynamically($meta['sample_original'], $typeKey, [
                                                'show_first' => $showFirst,
                                                'show_last' => $showLast,
                                                'mask_char' => $maskChar,
                                                'preserve_domain' => $preserveDomain,
                                                'preserve_separators' => $preserveSeparators
                                            ]);
                                        ?>
                                        <tr data-type-row="<?php echo htmlspecialchars($typeKey); ?>">
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars(__($meta['name'] ?? '')) ?></div>
                                                <small class="text-muted font-monospace"><?php echo htmlspecialchars($typeKey); ?></small>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input rule-toggle-restricted" type="checkbox" role="switch"
                                                           id="switch_<?php echo $typeKey; ?>"
                                                           name="restrictions[<?php echo $typeKey; ?>][is_restricted]" value="1"
                                                           <?php echo $isRestricted ? 'checked' : ''; ?>
                                                           <?php echo !$isAdmin ? 'disabled' : ''; ?>
                                                           onchange="updateRowPreview('<?php echo $typeKey; ?>')">
                                                    <label class="form-check-label small ms-1" for="switch_<?php echo $typeKey; ?>">
                                                        <span class="status-label-<?php echo $typeKey; ?> badge <?php echo $isRestricted ? 'bg-danger' : 'bg-secondary'; ?>">
                                                            <?= $isRestricted ? __("Restricted") : __("Visible"); ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" min="0" max="10" class="form-control form-control-sm font-monospace rule-show-first" 
                                                       name="restrictions[<?php echo $typeKey; ?>][show_first]"
                                                       id="show_first_<?php echo $typeKey; ?>"
                                                       value="<?php echo htmlspecialchars($showFirst); ?>"
                                                       <?php echo !$isAdmin ? 'disabled' : ''; ?>
                                                       oninput="updateRowPreview('<?php echo $typeKey; ?>')">
                                            </td>
                                            <td>
                                                <input type="number" min="0" max="10" class="form-control form-control-sm font-monospace rule-show-last" 
                                                       name="restrictions[<?php echo $typeKey; ?>][show_last]"
                                                       id="show_last_<?php echo $typeKey; ?>"
                                                       value="<?php echo htmlspecialchars($showLast); ?>"
                                                       <?php echo !$isAdmin ? 'disabled' : ''; ?>
                                                       oninput="updateRowPreview('<?php echo $typeKey; ?>')">
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm font-monospace rule-mask-char js-example-basic-single"
                                                        name="restrictions[<?php echo $typeKey; ?>][mask_char]"
                                                        id="mask_char_<?php echo $typeKey; ?>"
                                                        <?php echo !$isAdmin ? 'disabled' : ''; ?>
                                                        onchange="updateRowPreview('<?php echo $typeKey; ?>')">
                                                    <option value="*" <?php echo $maskChar === '*' ? 'selected' : ''; ?>> <?= __("* (Star)") ?></option>
                                                    <option value="X" <?php echo $maskChar === 'X' ? 'selected' : ''; ?>> <?= __("X (Upper X)") ?></option>
                                                    <option value="x" <?php echo $maskChar === 'x' ? 'selected' : ''; ?>> <?= __("x (Lower x)") ?></option>
                                                    <option value="#" <?php echo $maskChar === '#' ? 'selected' : ''; ?>> <?= __("# (Hash)") ?></option>
                                                </select>
                                            </td>
                                            <td>
                                                <?php if ($typeKey === 'email'): ?>
                                                    <select class="form-select form-select-sm rule-preserve-domain js-example-basic-single"
                                                            name="restrictions[<?php echo $typeKey; ?>][preserve_domain]"
                                                            id="preserve_domain_<?php echo $typeKey; ?>"
                                                            <?php echo !$isAdmin ? 'disabled' : ''; ?>
                                                            onchange="updateRowPreview('<?php echo $typeKey; ?>')">
                                                        <option value="1" <?php echo $preserveDomain ? 'selected' : ''; ?>> <?= __("Yes (Keep @domain)") ?></option>
                                                        <option value="0" <?php echo !$preserveDomain ? 'selected' : ''; ?>> <?= __("No (Mask domain)") ?></option>
                                                    </select>
                                                <?php else: ?>
                                                    <select class="form-select form-select-sm rule-preserve-domain js-example-basic-single"
                                                            name="restrictions[<?php echo $typeKey; ?>][preserve_separators]"
                                                            id="preserve_domain_<?php echo $typeKey; ?>"
                                                            <?php echo !$isAdmin ? 'disabled' : ''; ?>
                                                            onchange="updateRowPreview('<?php echo $typeKey; ?>')">
                                                        <option value="1" <?php echo $preserveSeparators ? 'selected' : ''; ?>> <?= __("Yes (Keep spaces)") ?></option>
                                                        <option value="0" <?php echo !$preserveSeparators ? 'selected' : ''; ?>> <?= __("No (Compact)") ?></option>
                                                    </select>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-secondary border font-monospace rule-sample-orig">
                                                    <?php echo htmlspecialchars($meta['sample_original']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-monospace text-primary fw-bold rule-preview-box" id="preview_<?php echo $typeKey; ?>">
                                                    <?php echo htmlspecialchars($isRestricted ? $sampleMasked : $meta['sample_original']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>

                                        <!-- Container for Custom Sensitive Types -->
                                        <?php
                                        if (!empty($currentPolicy['restrictions'])) {
                                            foreach ($currentPolicy['restrictions'] as $cKey => $cData) {
                                                if (!isset($supportedTypes[$cKey])) {
                                                    $cRestricted = !empty($cData['is_restricted']);
                                                    $cFirst = $cData['show_first'] ?? 2;
                                                    $cLast  = $cData['show_last'] ?? 2;
                                                    $cChar  = $cData['mask_char'] ?? '*';
                                                    $cSample = 'CONFIDENTIAL-1234';
                                                    $cMasked = maskValueDynamically($cSample, $cKey, [
                                                        'show_first' => $cFirst,
                                                        'show_last'  => $cLast,
                                                        'mask_char'  => $cChar
                                                    ]);
                                                    ?>
                                                    <tr data-type-row="<?php echo htmlspecialchars($cKey); ?>" class="table-warning bg-opacity-25">
                                                        <td>
                                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $cKey))); ?> <span class="badge bg-info"><?= __("Custom") ?></span></div>
                                                            <input type="hidden" name="custom_type_key[]" value="<?php echo htmlspecialchars($cKey); ?>">
                                                            <small class="text-muted font-monospace"><?php echo htmlspecialchars($cKey); ?></small>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check form-switch d-inline-block">
                                                                <input class="form-check-input rule-toggle-restricted" type="checkbox" role="switch"
                                                                       name="custom_type_restricted[]" value="1"
                                                                       id="switch_<?php echo $cKey; ?>"
                                                                       <?php echo $cRestricted ? 'checked' : ''; ?>
                                                                       <?php echo !$isAdmin ? 'disabled' : ''; ?>
                                                                       onchange="updateRowPreview('<?php echo $cKey; ?>')">
                                                                <label class="form-check-label small ms-1" for="switch_<?php echo $cKey; ?>">
                                                                    <span class="status-label-<?php echo $cKey; ?> badge <?php echo $cRestricted ? 'bg-danger' : 'bg-secondary'; ?>">
                                                                        <?= $cRestricted ? __("Restricted") : __("Visible"); ?>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input aria-label="Show start characters" type="number" min="0" max="10" class="form-control form-control-sm font-monospace rule-show-first" 
                                                                   name="custom_type_show_first[]" value="<?php echo htmlspecialchars($cFirst); ?>"
                                                                   oninput="updateRowPreview('<?php echo $cKey; ?>')">
                                                        </td>
                                                        <td>
                                                            <input aria-label="Show end characters" type="number" min="0" max="10" class="form-control form-control-sm font-monospace rule-show-last" 
                                                                   name="custom_type_show_last[]" value="<?php echo htmlspecialchars($cLast); ?>"
                                                                   oninput="updateRowPreview('<?php echo $cKey; ?>')">
                                                        </td>
                                                        <td>
                                                            <select aria-label="Mask character" class="form-select form-select-sm font-monospace rule-mask-char js-example-basic-single"
                                                                    name="custom_type_mask_char[]"
                                                                    onchange="updateRowPreview('<?php echo $cKey; ?>')">
                                                                <option value="*" <?php echo $cChar === '*' ? 'selected' : ''; ?>>*</option>
                                                                <option value="X" <?php echo $cChar === 'X' ? 'selected' : ''; ?>>X</option>
                                                                <option value="#" <?php echo $cChar === '#' ? 'selected' : ''; ?>>#</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted small"><?= __("Standard") ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-secondary border font-monospace rule-sample-orig"><?php echo htmlspecialchars($cSample); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="font-monospace text-primary fw-bold rule-preview-box" id="preview_<?php echo $cKey; ?>">
                                                                <?php echo htmlspecialchars($cRestricted ? $cMasked : $cSample); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if ($isAdmin): ?>
                            <div class="card-footer bg-light d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <small class="text-muted">
                                        <i class="feather icon-info me-1"></i> Asterisk counts calculate dynamically: <code>Mask Count = Total Length - Show Start - Show End</code>.
                                    </small>
                                </div>
                                <div>
                                   
                                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnSavePolicy">
                                        <i class="feather icon-save me-1"></i> <?= __("Save & Apply Policy") ?>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

 <?php /** ?>
                <!-- Interactive Testing Sandbox Card -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm" style="border-radius: 8px;">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="feather icon-play-circle text-success me-2"></i> Live Policy Simulator & Verification Tool
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">
                                Test how arbitrary inputs of different lengths will be dynamically masked based on your current settings before saving.
                            </p>
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="test_data_type" class="form-label small fw-bold">Select Data Type</label>
                                    <select class="form-select" id="test_data_type" onchange="runSimulator()">
                                        <?php foreach ($supportedTypes as $typeKey => $meta): ?>
                                            <option value="<?php echo htmlspecialchars($typeKey); ?>"><?php echo htmlspecialchars($meta['name']); ?> (<?php echo htmlspecialchars($typeKey); ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="test_input_value" class="form-label small fw-bold">Test Value (Try Different Lengths)</label>
                                    <input type="text" class="form-control font-monospace" id="test_input_value" value="rohan@gmail.com" oninput="runSimulator()" placeholder="Enter test value...">
                                </div>
                                <div class="col-md-5">
                                    <span class="form-label small fw-bold d-inline-block">Masked Output (Dynamic Character-Count)</span>
                                    <div class="form-control font-monospace bg-light text-primary fw-bold" id="test_output_value" style="font-size: 1.05rem;">
                                        r****@gmail.com
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                            <?php **/ ?>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Adding Custom Sensitive Data Type -->
<div class="modal fade" id="modalAddCustomType" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="feather icon-plus-circle text-primary me-2"></i> <?= __("Add Custom Sensitive Field") ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold"><?= __("Field Name / Label") ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="custom_field_label" placeholder="<?= __("e.g. Salary, SSN, Driving License") ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold"><?= __("Field Key Identifier") ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-monospace" id="custom_field_key" placeholder="<?= __("e.g. salary, ssn, driving_license") ?>">
                    <small class="text-muted"><?= __("Unique lowercase identifier used in code calls") ?></small>
                </div>
                <div class="row g-2">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold"><?= __("Show Start Chars") ?></label>
                        <input type="number" min="0" max="10" class="form-control font-monospace" id="custom_field_show_first" value="2">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold"><?= __("Show End Chars") ?></label>
                        <input type="number" min="0" max="10" class="form-control font-monospace" id="custom_field_show_last" value="2">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __("Cancel") ?></button>
                <button type="button" class="btn btn-primary" id="btnConfirmAddCustomType"><?= __("Add Field") ?></button>
            </div>
        </div>
    </div>
</div>

<script>
// Universal dynamic character-count masking helper for JavaScript matching the PHP engine
function maskClientValue(val, type, config, isRestricted) {
    if (!val || val === '') return '';
    if (!isRestricted) return val;

    val = String(val);
    var sFirst = typeof config.show_first !== 'undefined' ? parseInt(config.show_first, 10) : 2;
    var sLast  = typeof config.show_last  !== 'undefined' ? parseInt(config.show_last, 10)  : 2;
    var maskChar = config.mask_char ? config.mask_char.charAt(0) : '*';
    var preserveDomain = typeof config.preserve_domain !== 'undefined' ? config.preserve_domain : true;
    var preserveSeparators = typeof config.preserve_separators !== 'undefined' ? config.preserve_separators : true;

    // 1. Specialized Email Handling
    if (type === 'email' && val.indexOf('@') !== -1) {
        var parts = val.split('@');
        var u = parts[0];
        var d = parts[1] || '';
        var uLen = u.length;

        var uFirst = Math.max(0, sFirst);
        var uLast  = Math.max(0, sLast);

        if (uFirst + uLast >= uLen) {
            uFirst = Math.min(uFirst, Math.max(1, uLen - 1));
            uLast  = Math.min(uLast, Math.max(0, uLen - uFirst - 1));
        }

        // Formula: maskCount = uLen - uFirst - uLast
        var uMaskCount = Math.max(1, uLen - uFirst - uLast);
        var prefix = u.substring(0, uFirst);
        var suffix = uLast > 0 ? u.substring(uLen - uLast) : '';
        var maskedU = prefix + maskChar.repeat(uMaskCount) + suffix;

        if (preserveDomain) {
            return maskedU + '@' + d;
        } else {
            if (d.indexOf('.') !== -1) {
                var lastDot = d.lastIndexOf('.');
                var dName = d.substring(0, lastDot);
                var dExt  = d.substring(lastDot);
                var dMaskCount = Math.max(3, dName.length);
                return maskedU + '@' + maskChar.repeat(dMaskCount) + dExt;
            } else {
                return maskedU + '@' + maskChar.repeat(d.length);
            }
        }
    }

    // 2. Aadhaar Formatted Handling
    if (type === 'aadhaar') {
        var clean = val.replace(/[^0-9]/g, '');
        if (clean.length === 12) {
            var first = Math.max(0, sFirst);
            var last  = Math.max(0, sLast);
            if (first + last >= 12) {
                first = Math.min(first, 11);
                last  = Math.min(last, Math.max(0, 12 - first - 1));
            }
            var maskCount = Math.max(0, 12 - first - last);
            var pre = first > 0 ? clean.substring(0, first) : '';
            var suf = last > 0 ? clean.substring(12 - last) : '';
            var masked12 = pre + maskChar.repeat(maskCount) + suf;

            if (preserveSeparators) {
                return masked12.substring(0, 4) + ' ' + masked12.substring(4, 8) + ' ' + masked12.substring(8, 12);
            } else {
                return masked12;
            }
        }
    }

    // 3. Universal Dynamic Formula for PAN, GST, Mobile, Bank Account, etc.
    var len = val.length;
    var first = Math.max(0, sFirst);
    var last  = Math.max(0, sLast);

    if (first + last >= len) {
        first = Math.min(first, Math.max(1, len - 1));
        last  = Math.min(last, Math.max(0, len - first - 1));
    }

    // Formula: Mask count = Total characters - Visible starting - Visible ending
    var maskCount = Math.max(0, len - first - last);
    var pre = first > 0 ? val.substring(0, first) : '';
    var suf = last > 0 ? val.substring(len - last) : '';

    return pre + maskChar.repeat(maskCount) + suf;
}

function getRowConfig(typeKey) {
    var row = document.querySelector('tr[data-type-row="' + typeKey + '"]');
    if (!row) return { show_first: 2, show_last: 2, mask_char: '*', preserve_domain: true, is_restricted: true };

    var toggle = row.querySelector('.rule-toggle-restricted');
    var isRestricted = toggle ? toggle.checked : true;

    var sFirstEl = row.querySelector('.rule-show-first');
    var sLastEl  = row.querySelector('.rule-show-last');
    var mCharEl  = row.querySelector('.rule-mask-char');
    var pDomEl   = row.querySelector('.rule-preserve-domain');
    var isPreserve = pDomEl ? (pDomEl.value === '1') : true;

    return {
        is_restricted: isRestricted,
        show_first: sFirstEl ? (sFirstEl.value !== '' ? parseInt(sFirstEl.value, 10) : 0) : 2,
        show_last:  sLastEl  ? (sLastEl.value !== '' ? parseInt(sLastEl.value, 10) : 2) : 2,
        mask_char:  mCharEl  ? mCharEl.value : '*',
        preserve_domain: isPreserve,
        preserve_separators: isPreserve
    };
}

function updateRowPreview(typeKey) {
    var row = document.querySelector('tr[data-type-row="' + typeKey + '"]');
    if (!row) return;

    var cfg = getRowConfig(typeKey);
    var origSampleBox = row.querySelector('.rule-sample-orig');
    var sampleVal = origSampleBox ? origSampleBox.innerText.trim() : 'SAMPLE1234';

    var statusBadge = row.querySelector('.status-label-' + typeKey);
    if (statusBadge) {
        if (cfg.is_restricted) {
            statusBadge.className = 'status-label-' + typeKey + ' badge bg-danger';
            statusBadge.innerText = 'Restricted';
        } else {
            statusBadge.className = 'status-label-' + typeKey + ' badge bg-secondary';
            statusBadge.innerText = 'Visible';
        }
    }

    var previewBox = document.getElementById('preview_' + typeKey);
    if (previewBox) {
        previewBox.innerText = maskClientValue(sampleVal, typeKey, cfg, cfg.is_restricted);
    }

    // Synchronize test simulator if currently viewing this type
    var currentTestType = document.getElementById('test_data_type');
    if (currentTestType && currentTestType.value === typeKey) {
        runSimulator();
    }
}

function runSimulator() {
    var typeSelect = document.getElementById('test_data_type');
    var valInput   = document.getElementById('test_input_value');
    var outBox     = document.getElementById('test_output_value');

    if (!typeSelect || !valInput || !outBox) return;

    var type = typeSelect.value;
    var val  = valInput.value;
    var cfg  = getRowConfig(type);

    outBox.innerText = maskClientValue(val, type, cfg, cfg.is_restricted);
}

// Auto-fill test value on type change and initialize all previews
document.addEventListener('DOMContentLoaded', function() {
    var typeSelect = document.getElementById('test_data_type');
    var valInput   = document.getElementById('test_input_value');

    var sampleDefaults = {
        'email': 'username@gmail.com',
        'pan': 'ABCDE1234F',
        'gst': '27ABCDE1234F1Z5',
        'mobile': '9876543210',
        'aadhaar': '123456789012',
        'bank_account': '1234567890123456',
        'ifsc': 'HDFC0001234',
        'passport': 'A1234567',
        'driving_licence': 'DL-1420110012345',
        'pf_number': 'MH/BAN/0012345/000/0001234',
        'uan_number': '100912345678',
        'gosi_number': '123456789'
    };

    if (typeSelect && valInput) {
        typeSelect.addEventListener('change', function() {
            if (sampleDefaults[this.value]) {
                valInput.value = sampleDefaults[this.value];
            }
            runSimulator();
        });
    }

    // Refresh all previews on initial load to match current settings
    document.querySelectorAll('tr[data-type-row]').forEach(function(row) {
        var typeKey = row.getAttribute('data-type-row');
        if (typeKey) updateRowPreview(typeKey);
    });
    runSimulator();

    // Modal add custom type
    var btnAdd = document.getElementById('btnAddCustomType');
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            var myModal = new bootstrap.Modal(document.getElementById('modalAddCustomType'));
            myModal.show();
        });
    }

    var btnConfirm = document.getElementById('btnConfirmAddCustomType');
    if (btnConfirm) {
        btnConfirm.addEventListener('click', function() {
            var label = document.getElementById('custom_field_label').value.trim();
            var key   = document.getElementById('custom_field_key').value.trim().toLowerCase().replace(/[^a-z0-9_]/g, '_');
            var sFirst = parseInt(document.getElementById('custom_field_show_first').value, 10) || 2;
            var sLast  = parseInt(document.getElementById('custom_field_show_last').value, 10) || 2;

            if (!key) {
                alert(<?= json_encode(__('Please provide a field key identifier.')) ?>);
                return;
            }

            var tbody = document.querySelector('#tableSensitiveRules tbody');
            var newRow = document.createElement('tr');
            newRow.setAttribute('data-type-row', key);
            newRow.className = 'table-warning bg-opacity-25';
            newRow.innerHTML = `
                <td>
                    <div class="fw-bold text-dark">\${label || key} <span class="badge bg-info"><?= __("Custom") ?></span></div>
                    <input type="hidden" name="custom_type_key[]" value="\${key}">
                    <small class="text-muted font-monospace">\${key}</small>
                </td>
                <td class="text-center">
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input rule-toggle-restricted" type="checkbox" role="switch"
                               name="custom_type_restricted[]" value="1" checked
                               id="switch_\${key}" onchange="updateRowPreview('\${key}')">
                        <label class="form-check-label small ms-1" for="switch_\${key}">
                            <span class="status-label-\${key} badge bg-danger">Restricted</span>
                        </label>
                    </div>
                </td>
                <td>
                    <input type="number" min="0" max="10" class="form-control form-control-sm font-monospace rule-show-first" 
                           name="custom_type_show_first[]" value="\${sFirst}"
                           oninput="updateRowPreview('\${key}')">
                </td>
                <td>
                    <input type="number" min="0" max="10" class="form-control form-control-sm font-monospace rule-show-last" 
                           name="custom_type_show_last[]" value="\${sLast}"
                           oninput="updateRowPreview('\${key}')">
                </td>
                <td>
                    <select class="form-select form-select-sm font-monospace rule-mask-char"
                            name="custom_type_mask_char[]" onchange="updateRowPreview('\${key}')">
                        <option value="*">*</option>
                        <option value="X">X</option>
                        <option value="#">#</option>
                    </select>
                </td>
                <td>
                    <span class="text-muted small"><?= __("Standard") ?></span>
                </td>
                <td>
                    <span class="badge bg-light text-secondary border font-monospace rule-sample-orig">CONFIDENTIAL-1234</span>
                </td>
                <td>
                    <span class="font-monospace text-primary fw-bold rule-preview-box" id="preview_\${key}">
                        \${maskClientValue('CONFIDENTIAL-1234', key, {show_first: sFirst, show_last: sLast, mask_char: '*'}, true)}
                    </span>
                </td>
            `;
            tbody.appendChild(newRow);

            // Add to simulator select
            var sel = document.getElementById('test_data_type');
            if (sel) {
                var opt = document.createElement('option');
                opt.value = key;
                opt.innerText = (label || key) + ' (' + key + ')';
                sel.appendChild(opt);
            }

            var modalEl = document.getElementById('modalAddCustomType');
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
        });
    }

    // Indicate saving state on submit
    var policyForm = document.getElementById('formSensitiveDataPolicy');
    if (policyForm) {
        policyForm.addEventListener('submit', function() {
            var btn = document.getElementById('btnSavePolicy');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving & Applying...';
            }
        });
    }
});
</script>
