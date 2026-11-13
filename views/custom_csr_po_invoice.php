<?php
$_SESSION['apply_js']['multiselest'] = 1;

// Fetch CSR Projects
$url = API . "?mode=getCSRProjects&method=get";
$result = callAPI($url);
$result = json_decode($result, true);
$projects = $result['table'] ?? array();
?>
<link rel="stylesheet" href="assets/css/custom_csr_po_invoice.css">

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5><?= __('CSR Project Tracking') ?></h5>
        </div>

        <div class="card-body p-4">
            <div class="row">

                <!-- Project -->
                <div class="form-group col-md-4">
                    <label for="project_id"><?= __('Project') ?> <span class="text-danger">*</span></label>
                    <select class="form-select js-example-basic-single" id="project_id" aria-label="<?= __('Project') ?>">
                        <option value="0"><?= __('Select Project') ?></option>
                        <?php foreach ($projects as $row): ?>
                            <option value="<?= $row['id']; ?>">
                                <?= htmlspecialchars($row['subject']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-danger d-none" id="project_err"></div>
                </div>

                <!-- Tracking -->
                <div class="form-group col-md-4">
                    <label for="tracking_id"><?= __('Tracking') ?> <span class="text-danger">*</span></label>
                    <select class="form-select js-example-basic-single" id="tracking_id" aria-label="<?= __('Tracking') ?>">
                        <option value="0"><?= __('Select Tracking') ?></option>
                    </select>

                </div>

                <!-- Radio Buttons -->
                <div class="form-group col-md-2">
                    <label for="doc_type_po"><?= __('Type') ?> <span class="text-danger">*</span></label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="doc_type" id="doc_type_po" value="PO" aria-label="<?= __('PO') ?>">
                        <label class="form-check-label" for="doc_type_po"><?= __('PO') ?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="doc_type" id="doc_type_invoice" value="INVOICE" aria-label="<?= __('Invoice') ?>">
                        <label class="form-check-label" for="doc_type_invoice"><?= __('Invoice') ?></label>
                    </div>
                </div>

                <!-- Search -->
                <div class="form-group col-md-2">
                    <label for="searchBtn" aria-label="<?= __('Search') ?>">&nbsp;</label>
                    <button type="button" class="btn btn-primary form-control" id="searchBtn">
                        <?= __('Search') ?>
                    </button>
                </div>

            </div>

            
        </div>
    </div>
    <!-- Result Table -->
    <div class="mt-4" id="TableContent"></div>
</div>

<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/js/custom_csr_po_invoice.js"></script>

