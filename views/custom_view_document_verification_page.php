<link rel="stylesheet" href="../assets/css/custom_view_document_verification_page.css">
<script defer src="../assets/js/custom_view_document_verification_page.js"></script>
<div class="page-body nx-dashboard-wrapper">
    <div class="row g-4">

        <!-- PAN -->
        <div class="col-xl-4 col-lg-6">
            <div class="nx-veri-card" id="card-pan">
                <div class="nx-card-loader">
                    <div class="nx-spinner"></div>
                    <div class="nx-loading-text"><?= __('Verifying PAN...'); ?></div>
                </div>
                <div class="nx-card-header">
                    <div class="nx-icon-container"><i class="fas fa-id-card"></i></div>
                    <div class="nx-card-title">
                        <h3><?= __('PAN'); ?></h3>
                    </div>
                </div>
                <div class="nx-form-group">
                    <span class="nx-label"><?= __('PAN Number'); ?></span>
                    <input aria-label="<?= htmlspecialchars(__('ABCDE1234F'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="nx_pan" class="nx-input text-uppercase" placeholder="<?= htmlspecialchars(__('ABCDE1234F'), ENT_QUOTES, 'UTF-8'); ?>"
                        maxlength="10">
                </div>
                <button class="nx-btn verifyPAN" ><?= __('Verify PAN'); ?></button>
                <div id="pan-feedback" class="mt-2 text-center small fw-bold"></div>
            </div>
        </div>

        <!-- GST -->
        <div class="col-xl-4 col-lg-6">
            <div class="nx-veri-card" id="card-gst">
                <div class="nx-card-loader">
                    <div class="nx-spinner"></div>
                    <div class="nx-loading-text"><?= __('Fetching GST Data...'); ?></div>
                </div>
                <div class="nx-card-header">
                    <div class="nx-icon-container" style="color: #0ea5e9;"><i class="fas fa-file"></i></div>
                    <div class="nx-card-title">
                        <h3><?= __('GST'); ?></h3>
                    </div>
                </div>
                <div class="nx-form-group">
                    <span class="nx-label"><?= __('GSTIN Number'); ?></span>
                    <input aria-label="<?= htmlspecialchars(__('22AAAAA0000A1Z5'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="nx_gst" class="nx-input text-uppercase" placeholder="<?= htmlspecialchars(__('22AAAAA0000A1Z5'), ENT_QUOTES, 'UTF-8'); ?>"
                        maxlength="15">
                </div>
                <button class="nx-btn verifyGST" style="background: #0ea5e9;"><?= __('Verify GST'); ?></button>
                <div id="gst-feedback" class="mt-2 text-center small fw-bold"></div>
            </div>
        </div>

        <!-- BANK -->
        <div class="col-xl-4 col-lg-6">
            <div class="nx-veri-card" id="card-bank">
                <div class="nx-card-loader">
                    <div class="nx-spinner"></div>
                    <div class="nx-loading-text"><?= __('Auditing Account...'); ?></div>
                </div>
                <div class="nx-card-header">
                    <div class="nx-icon-container" style="color: #f59e0b;"><i class="fas fa-university"></i></div>
                    <div class="nx-card-title">
                        <h3><?= __('Bank'); ?></h3>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <span class="nx-label"><?= __('Account No'); ?></span>
                        <input aria-label="<?= htmlspecialchars(__('Number'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="nx_bank_acc" class="nx-input" placeholder="<?= htmlspecialchars(__('Number'), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-5">
                        <span class="nx-label"><?= __('IFSC Code'); ?></span>
                        <input aria-label="<?= htmlspecialchars(__('IFSC'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="nx_bank_ifsc" class="nx-input text-uppercase" placeholder="<?= htmlspecialchars(__('IFSC'), ENT_QUOTES, 'UTF-8'); ?>"
                            maxlength="11">
                    </div>
                </div>

                <button class="nx-btn verifyBANK" style="background: #f59e0b;" ><?= __('Verify Bank'); ?></button>
                <div id="bank-feedback" class="mt-2 text-center small fw-bold"></div>
            </div>
        </div>

        <!-- MSME -->
        <div class="col-xl-4 col-lg-6">
            <div class="nx-veri-card" id="card-msme">
                <div class="nx-card-loader">
                    <div class="nx-spinner"></div>
                    <div class="nx-loading-text"><?= __('Searching Registry...'); ?></div>
                </div>
                <div class="nx-card-header">
                    <div class="nx-icon-container" style="color: #ec4899;"><i class="fas fa-industry"></i></div>
                    <div class="nx-card-title">
                        <h3><?= __('MSME'); ?></h3>
                    </div>
                </div>
                <div class="nx-form-group">
                    <span class="nx-label"><?= __('Udyam Number'); ?></span>
                    <input aria-label="<?= htmlspecialchars(__('UDYAM-XX-00-0000000'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="nx_msme" class="nx-input text-uppercase" placeholder="<?= htmlspecialchars(__('UDYAM-XX-00-0000000'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button class="nx-btn verifyMSME" style="background: #ec4899;"><?= __('Verify MSME'); ?></button>
                <div id="msme-feedback" class="mt-2 text-center small fw-bold"></div>
            </div>
        </div>

        <!-- TAN -->
        <div class="col-xl-4 col-lg-6">
            <div class="nx-veri-card" id="card-tan">
                <div class="nx-card-loader">
                    <div class="nx-spinner"></div>
                    <div class="nx-loading-text"><?= __('Validating TAN...'); ?></div>
                </div>
                <div class="nx-card-header">
                    <div class="nx-icon-container" style="color: #6366f1;"><i class="fas fa-credit-card"></i>
                    </div>
                    <div class="nx-card-title">
                        <h3><?= __('TAN'); ?></h3>
                    </div>
                </div>
                <div class="nx-form-group">
                    <span class="nx-label"><?= __('TAN Number'); ?></span>
                    <input aria-label="<?= htmlspecialchars(__('ABCD12345E'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="nx_tan" class="nx-input text-uppercase" placeholder="<?= htmlspecialchars(__('ABCD12345E'), ENT_QUOTES, 'UTF-8'); ?>"
                        maxlength="10">
                </div>
                <button class="nx-btn verifyTAN" style="background: #6366f1;" ><?= __('Verify TAN'); ?></button>
                <div id="tan-feedback" class="mt-2 text-center small fw-bold"></div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="nxResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content nx-modal">
            <div class="modal-header nx-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div id="modal-icon-box" class="nx-icon-container"
                        style="width:36px; height:36px; font-size:0.9rem;"></div>
                    <h5 class="modal-title fw-bold" id="modal-title"><?= __('Verification Report'); ?></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
            </div>
            <div class="modal-body nx-modal-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="nx-label m-0"><?= __('INTELLIGENCE REPORT'); ?></span>
                    <span class="nx-badge nx-badge-success"><?= __('Verified'); ?></span>
                </div>
                <div class="nx-data-grid" id="modal-grid"></div>
            </div>
            <div class="modal-footer nx-modal-footer">
                <button type="button" class="btn btn-dark fw-bold rounded-3 px-4" data-bs-dismiss="modal"><?= __('Close'); ?></button>
            </div>
        </div>
    </div>
</div>
