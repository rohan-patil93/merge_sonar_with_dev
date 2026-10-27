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
            <h5>CSR Project Tracking</h5>
        </div>

        <div class="card-body p-4">
            <div class="row">

                <!-- Project -->
                <div class="form-group col-md-4">
                    <label>Project <span class="text-danger">*</span></label>
                    <select class="form-select js-example-basic-single" id="project_id">
                        <option value="0">Select Project</option>
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
                    <label>Tracking <span class="text-danger">*</span></label>
                    <select class="form-select js-example-basic-single" id="tracking_id">
                        <option value="0">Select Tracking</option>
                    </select>

                </div>

                <!-- Radio Buttons -->
                <div class="form-group col-md-2">
                    <label>Type <span class="text-danger">*</span></label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="doc_type" value="PO">
                        <label class="form-check-label">PO</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="doc_type" value="INVOICE">
                        <label class="form-check-label">Invoice</label>
                    </div>
                </div>

                <!-- Search -->
                <div class="form-group col-md-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary form-control" id="searchBtn">
                        Search
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

