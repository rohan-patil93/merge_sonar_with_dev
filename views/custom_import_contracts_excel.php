<?php
$_SESSION['apply_js']['multiselest'] = 1;
?>
<link rel="stylesheet" href="assets/css/custom_insert_create_contract.css?v=<?= @filemtime(__DIR__ . '/../assets/css/custom_insert_create_contract.css') ?: time() ?>">
<link rel="stylesheet" href="assets/css/custom_import_contracts_excel.css?v=<?= @filemtime(__DIR__ . '/../assets/css/custom_import_contracts_excel.css') ?: time() ?>">

<div class="card unified-contract-create-card">
    <div class="card-header">
        <h5><?php echo __('Contract Excel Upload'); ?></h5>
        <div class="card-header-right table-card-header">
            <a href="index.php?option=COM_LIST_CONTRACTS" class="btn waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply" aria-hidden="true"></i> <?php echo __('Back to List'); ?>
            </a>
        </div>
    </div>
    <div class="card-block">
        <div id="unified-contract-page" class="cex-import-page">
            <div class="page-body cc-wizard-page">
                <div class="cc-wizard-header">
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="mb-0"><?php echo __('Import Contracts from Excel'); ?></h5>
                    </div>
                    <div class="cc-wizard-header-actions">
                        <button type="button" class="btn btn-primary btn-sm" id="cex-download-template-btn">
                            <i class="fa fa-download" aria-hidden="true"></i> <?php echo __('Download Template'); ?>
                        </button>
                    </div>
                </div>

                <div class="cc-wizard-layout">
                    <aside class="cc-wizard-sidebar">
                        <div class="cc-wizard-steps-list">
                            <a href="#" class="cc-wizard-step active" data-cex-step="0">
                                <span class="cc-step-number">1</span>
                                <div class="cc-step-info">
                                    <div class="cc-step-title"><?php echo __('Download Template'); ?></div>
                                    <div class="cc-step-desc"><?php echo __('4 data sheets + Ref sheets'); ?></div>
                                </div>
                            </a>
                            <a href="#" class="cc-wizard-step" data-cex-step="1">
                                <span class="cc-step-number">2</span>
                                <div class="cc-step-info">
                                    <div class="cc-step-title"><?php echo __('Upload File'); ?></div>
                                    <div class="cc-step-desc"><?php echo __('Upload filled .xlsx'); ?></div>
                                </div>
                            </a>
                            <a href="#" class="cc-wizard-step" data-cex-step="2">
                                <span class="cc-step-number">3</span>
                                <div class="cc-step-info">
                                    <div class="cc-step-title"><?php echo __('Preview & Validate'); ?></div>
                                    <div class="cc-step-desc"><?php echo __('Review rows and errors'); ?></div>
                                </div>
                            </a>
                            <a href="#" class="cc-wizard-step" data-cex-step="3">
                                <span class="cc-step-number">4</span>
                                <div class="cc-step-info">
                                    <div class="cc-step-title"><?php echo __('Import Result'); ?></div>
                                    <div class="cc-step-desc"><?php echo __('Success / failed per ref'); ?></div>
                                </div>
                            </a>
                        </div>
                        <div class="cc-sidebar-box help-box">
                            <h6><i class="icofont icofont-question-circle" aria-hidden="true"></i> <?php echo __('Need Help?'); ?></h6>
                            <p class="mb-0"><?php echo __('Copy values from Ref_* sheets. Use billing_cycle for auto schedule, or milestones sheet for custom amounts. AMC needs Locations + Assets.'); ?></p>
                        </div>
                    </aside>

                    <div class="cc-wizard-main">
                        <div class="cc-section cex-step-panel" id="cex-step-1" data-cex-step="0">
                            <div class="cc-section-header">
                                <h6><span class="cc-section-num">1</span><i class="icofont icofont-file-excel" aria-hidden="true"></i> <?php echo __('Download Template'); ?></h6>
                            </div>
                            <div class="alert alert-info mb-3">
                                <?= __('Download the workbook with'); ?> <strong><?= __('Contracts'); ?></strong>, <strong><?= __('Locations'); ?></strong>, <strong><?= __('Assets'); ?></strong>, <strong><?= __('Milestones'); ?></strong><?= __(', and live'); ?> <strong><?= __('Ref_'); ?>*</strong> <?= __('sheets. Fill data sheets only; copy dropdown values from Ref sheets.'); ?>
                            </div>
                            <button type="button" class="btn btn-primary" id="cex-download-template-btn-2">
                                <i class="fa fa-download" aria-hidden="true"></i> <?php echo __('Download Template'); ?>
                            </button>
                        </div>

                        <div class="cc-section cex-step-panel d-none" id="cex-step-2" data-cex-step="1">
                            <div class="cc-section-header">
                                <h6><span class="cc-section-num">2</span><i class="icofont icofont-upload-alt" aria-hidden="true"></i> <?php echo __('Upload File'); ?></h6>
                            </div>
                            <label for="cex-excel-file" class="form-label d-block mb-2"><?php echo __('Upload Excel File (.xlsx)'); ?> <span class="text-danger">*</span></label>
                            <div class="cex-upload-zone" id="cex-upload-zone">
                                <i class="icofont icofont-cloud-upload" aria-hidden="true"></i>
                                <p class="mb-1"><?php echo __('Click to upload or drag and drop'); ?></p>
                                <small class="text-muted"><?php echo __('Accepted: .xlsx only'); ?></small>
                                <div class="cex-upload-filename text-primary mt-2" id="cex-upload-filename"></div>
                            </div>
                            <input type="file" id="cex-excel-file" name="cex_excel_file" class="d-none" accept=".xlsx" aria-label="<?php echo __('Upload Excel File (.xlsx)'); ?>">
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" id="cex-validate-btn" disabled>
                                    <?php echo __('Validate & Preview'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="cc-section cex-step-panel d-none" id="cex-step-3" data-cex-step="2">
                            <div class="cc-section-header">
                                <h6><span class="cc-section-num">3</span><i class="icofont icofont-eye" aria-hidden="true"></i> <?php echo __('Preview & Validate'); ?></h6>
                            </div>
                            <div class="row g-2 mb-3" id="cex-preview-stats"></div>
                            <div id="cex-preview-alerts"></div>
                            <ul class="nav md-tabs infisuite_tab mb-2">
                                <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#cex-tab-contracts"><?php echo __('Contracts'); ?></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#cex-tab-locations"><?php echo __('Locations'); ?></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#cex-tab-assets"><?php echo __('Assets'); ?></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#cex-tab-milestones"><?php echo __('Milestones'); ?></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#cex-tab-errors"><?php echo __('Errors'); ?></a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="cex-tab-contracts">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="cex-table-contracts">
                                            <thead>
                                                <tr>
                                                    <th scope="col">import_ref</th>
                                                    <th scope="col">contract_type</th>
                                                    <th scope="col">contract_name</th>
                                                    <th scope="col">party_type</th>
                                                    <th scope="col">department_name</th>
                                                    <th scope="col">payment_plan</th>
                                                    <th scope="col">billing_cycle</th>
                                                    <th scope="col">contract_amount</th>
                                                    <th scope="col"><?= __('ok'); ?></th>
                                                    <th scope="col"><?= __('error'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="cex-tab-locations">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="cex-table-locations">
                                            <thead>
                                                <tr>
                                                    <th scope="col">import_ref</th>
                                                    <th scope="col">location_name</th>
                                                    <th scope="col">service_start_date</th>
                                                    <th scope="col">service_end_date</th>
                                                    <th scope="col"><?= __('ok'); ?></th>
                                                    <th scope="col"><?= __('error'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="cex-tab-assets">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="cex-table-assets">
                                            <thead>
                                                <tr>
                                                    <th scope="col">import_ref</th>
                                                    <th scope="col">location_name</th>
                                                    <th scope="col">asset_code</th>
                                                    <th scope="col">service_frequency</th>
                                                    <th scope="col">visits_per_interval</th>
                                                    <th scope="col"><?= __('ok'); ?></th>
                                                    <th scope="col"><?= __('error'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="cex-tab-milestones">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="cex-table-milestones">
                                            <thead>
                                                <tr>
                                                    <th scope="col">import_ref</th>
                                                    <th scope="col">milestone_name</th>
                                                    <th scope="col">milestone_date</th>
                                                    <th scope="col">milestone_amount</th>
                                                    <th scope="col"><?= __('ok'); ?></th>
                                                    <th scope="col"><?= __('error'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="cex-tab-errors">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="cex-table-errors">
                                            <thead>
                                                <tr>
                                                    <th scope="col"><?php echo __('Message'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="cc-wizard-footer d-flex gap-2 mt-3">
                                <button type="button" class="btn btn-secondary" id="cex-reupload-btn"><?php echo __('Re-upload'); ?></button>
                                <button type="button" class="btn btn-primary" id="cex-import-btn" disabled>
                                    <?php echo __('Confirm Import'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="cc-section cex-step-panel d-none" id="cex-step-4" data-cex-step="3">
                            <div class="cc-section-header">
                                <h6><span class="cc-section-num">4</span><i class="icofont icofont-check-circled" aria-hidden="true"></i> <?php echo __('Import Result'); ?></h6>
                            </div>
                            <div class="row g-2 mb-3" id="cex-result-stats"></div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="cex-table-result">
                                    <thead>
                                        <tr>
                                            <th scope="col">import_ref</th>
                                            <th scope="col"><?php echo __('Status'); ?></th>
                                            <th scope="col"><?php echo __('Contract ID'); ?></th>
                                            <th scope="col"><?php echo __('Message'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="cc-wizard-footer d-flex gap-2 mt-3">
                                <a href="index.php?option=COM_LIST_CONTRACTS" class="btn btn-primary"><?php echo __('Go to Contracts List'); ?></a>
                                <button type="button" class="btn btn-outline-secondary" id="cex-view-audit-btn"><?php echo __('View Audit Batch'); ?></button>
                            </div>
                            <div class="mt-3 d-none" id="cex-audit-panel">
                                <div class="cc-section-header">
                                    <h6><i class="icofont icofont-history" aria-hidden="true"></i> <?php echo __('Audit Batch (source of truth)'); ?></h6>
                                </div>
                                <div id="cex-audit-content" class="small"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/custom_import_contracts_excel.js?v=<?= @filemtime(__DIR__ . '/../assets/js/custom_import_contracts_excel.js') ?: time() ?>"></script>
