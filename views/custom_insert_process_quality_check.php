<?php
$_SESSION['apply_js']['multiselest'] = 1;

$processUrl = API."?mode=getprocessqcprocesses";
$processResponse = callAPI($processUrl);
$processResult = json_decode($processResponse, true);
$processes = $processResult['processes'] ?? array();
$defaultProcess = $_GET['process'] ?? 'manufacturing_product_notes';
?>

<style>
    .process-qc-page { margin-left: 0 !important; width: 100% !important; }
    .process-qc-page .card { margin-left: 0 !important; border-radius: 8px; }
    .process-qc-toolbar label { font-weight: 600; color: #111827; }
    .process-qc-summary { display: grid; grid-template-columns: repeat(5, minmax(120px, 1fr)); gap: 12px; }
    .process-qc-kpi { border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; background: #fff; }
    .process-qc-kpi .label { color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0; }
    .process-qc-kpi .value { color: #111827; font-size: 26px; font-weight: 700; line-height: 1.1; }
    .process-qc-workspace { display: grid; grid-template-columns: minmax(330px, 420px) 1fr; gap: 16px; }
    .process-qc-list { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; background: #fff; }
    .process-qc-list-head { padding: 12px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; }
    .process-qc-items { max-height: 640px; overflow-y: auto; }
    .process-qc-item { width: 100%; border: 0; border-bottom: 1px solid #eef2f7; background: #fff; padding: 12px; text-align: left; }
    .process-qc-item:hover, .process-qc-item.active { background: #eef6ff; }
    .process-qc-product { border-bottom: 1px solid #e5e7eb; }
    .process-qc-product-head { padding: 12px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; display: grid; gap: 10px; }
    .process-qc-product-title { font-weight: 800; color: #111827; word-break: break-word; }
    .process-qc-product-counts { display: flex; flex-wrap: wrap; gap: 6px; }
    .process-qc-count-chip { border-radius: 6px; padding: 4px 7px; font-size: 11px; line-height: 1.1; border: 1px solid #e5e7eb; color: #374151; background: #fff; }
    .process-qc-count-chip strong { color: #111827; }
    .process-qc-count-total { background: #f9fafb; }
    .process-qc-count-passed { background: #ecfdf5; border-color: #bbf7d0; color: #047857; }
    .process-qc-count-failed { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
    .process-qc-count-draft { background: #fff7ed; border-color: #fed7aa; color: #9a3412; }
    .process-qc-count-pending { background: #f3f4f6; border-color: #e5e7eb; color: #374151; }
    .process-qc-child-item { padding-left: 18px; }
    .process-qc-item-title { font-weight: 700; color: #111827; word-break: break-word; }
    .process-qc-item-meta { color: #6b7280; font-size: 12px; margin-top: 4px; }
    .process-qc-status-pill { border-radius: 999px; padding: 3px 8px; font-size: 11px; font-weight: 700; display: inline-block; }
    .process-qc-status-0 { background: #f3f4f6; color: #374151; }
    .process-qc-status-1 { background: #fff7ed; color: #9a3412; }
    .process-qc-status-2 { background: #ecfdf5; color: #047857; }
    .process-qc-status-3 { background: #fef2f2; color: #b91c1c; }
    .process-qc-form-panel { border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; min-height: 520px; }
    .process-qc-form-head { padding: 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; }
    .process-qc-form-body { padding: 16px 16px 96px; max-height: 660px; overflow-y: auto; }
    .process-qc-param-group { margin-bottom: 18px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
    .process-qc-param-group-title { padding: 10px 14px; background: #06b6d4; color: #fff; font-weight: 700; }
    .process-qc-param-row { display: grid; grid-template-columns: 46px 1fr 260px 220px; gap: 14px; align-items: center; padding: 12px 14px; border-top: 1px solid #eef2f7; }
    .process-qc-param-row:first-child { border-top: 0; }
    .process-qc-choice { display: flex; gap: 6px; flex-wrap: wrap; }
    .process-qc-choice label { margin: 0; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 10px; cursor: pointer; background: #fff; }
    .process-qc-choice input { margin-right: 5px; }
    .process-qc-actions { position: sticky; bottom: 0; background: #fff; border-top: 1px solid #e5e7eb; padding: 12px 16px; display: flex; gap: 8px; justify-content: flex-end; z-index: 2; box-shadow: 0 -6px 16px rgba(15, 23, 42, 0.06); }
    .process-qc-locked-note { margin: 14px 16px 0; padding: 10px 12px; border: 1px solid #dbeafe; border-radius: 8px; background: #eff6ff; color: #1d4ed8; font-weight: 600; }
    .process-qc-lock-text { color: #4b5563; font-weight: 700; align-self: center; }
    .process-qc-empty { padding: 34px; text-align: center; color: #6b7280; }
    @media (max-width: 1100px) {
        .process-qc-summary { grid-template-columns: repeat(2, minmax(120px, 1fr)); }
        .process-qc-workspace { grid-template-columns: 1fr; }
        .process-qc-param-row { grid-template-columns: 36px 1fr; }
    }
</style>

<div class="page-body process-qc-page">
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Process Quality Check</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="form-group col-lg-4">
                    <span>Process</span>
                    <select aria-label="Process Qc Process" class="form-select js-example-basic-single" id="process_qc_process">
                        <option value="">Select Any One</option>
                        <?php foreach((array)$processes as $processKey => $processLabel){ ?>
                            <option value="<?php echo htmlspecialchars($processKey); ?>" <?php echo $processKey == $defaultProcess ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($processLabel); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-5">
                    <span>Transaction No</span>
                    <select aria-label="Process Qc Transaction" class="form-select js-example-basic-single" id="process_qc_transaction">
                        <option value="">Select process first</option>
                    </select>
                </div>
                <div class="form-group col-lg-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" id="process_qc_open_btn" disabled>
                        Open QC Check
                    </button>
                </div>
            </div>

            <div id="process_qc_status" class="mt-3"></div>
        </div>
    </div>

    <div id="process_qc_workspace" class="mt-3" style="display:none;">
        <div class="process-qc-summary mb-3">
            <div class="process-qc-kpi"><div class="label">Total</div><div class="value" id="pqc_total">0</div></div>
            <div class="process-qc-kpi"><div class="label">Passed</div><div class="value text-success" id="pqc_passed">0</div></div>
            <div class="process-qc-kpi"><div class="label">Failed</div><div class="value text-danger" id="pqc_failed">0</div></div>
            <div class="process-qc-kpi"><div class="label">In Progress</div><div class="value text-warning" id="pqc_draft">0</div></div>
            <div class="process-qc-kpi"><div class="label">Pending</div><div class="value text-secondary" id="pqc_pending">0</div></div>
        </div>
        <div class="process-qc-workspace">
            <div class="process-qc-list">
                <div class="process-qc-list-head">
                    <div class="row g-2">
                        <div class="col-7">
                            <input aria-label="Search item / serial / lot" type="text" class="form-control" id="process_qc_search" placeholder="Search item / serial / lot">
                        </div>
                        <div class="col-5">
                            <select aria-label="Process Qc Status Filter" class="form-select" id="process_qc_status_filter">
                                <option value="all">All</option>
                                <option value="0">Pending</option>
                                <option value="1">In Progress</option>
                                <option value="2">Passed</option>
                                <option value="3">Failed</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select aria-label="Process Qc Limit" class="form-select" id="process_qc_limit">
                                <option value="25">25 rows</option>
                                <option value="50">50 rows</option>
                                <option value="100">100 rows</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-primary w-100" id="process_qc_next_pending">Next Pending</button>
                        </div>
                    </div>
                </div>
                <div class="process-qc-items custom-scroll" id="process_qc_items"></div>
                <div class="p-2 d-flex justify-content-between align-items-center border-top">
                    <button type="button" class="btn btn-light btn-sm" id="process_qc_prev">Previous</button>
                    <span id="process_qc_page_text" class="text-muted small">Page 1</span>
                    <button type="button" class="btn btn-light btn-sm" id="process_qc_next">Next</button>
                </div>
            </div>
            <div class="process-qc-form-panel" id="process_qc_form_panel">
                <div class="process-qc-empty">Select a serial/lot row to start QC.</div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/custom_insert_process_quality_check.js"></script>
