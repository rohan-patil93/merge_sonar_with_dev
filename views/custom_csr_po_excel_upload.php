<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');

$tracking_id = $_POST['tracking_id'] ?? ($_POST['traking_id'] ?? '');
$project_id = $_POST['project_id'] ?? '';
$doc_type = $_POST['doc_type'] ?? 'PO';

if (!$project_id || !$tracking_id || $doc_type !== 'PO') {
    echo '<div class="alert alert-danger">Invalid upload request</div>';
    exit;
}

$sample_headers = array(
    'PO Available',
    'Financial Year',
    'PO No',
    'PO Date',
    'PO Amount',
    'PO Value (Excl. Tax)',
    'GST %',
    'PO Value (Incl. Tax)',
    'Business Code',
    'Business Name',
    'Other PO Details',
    'Remarks'
);
?>
<link rel="stylesheet" href="assets/css/custom_csr_po_excel_upload.css">

<div class="card csr-po-upload-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>PO Excel Upload</h5>
        <div class="d-flex gap-2 csr-po-upload-actions">
            <button type="button" class="btn btn-sm btn-primary" id="downloadPoSampleBtn">
                <i class="fa fa-download"></i> Sample Format Download
            </button>
            <button type="button" class="btn btn-sm btn-success" id="triggerPoUploadBtn">
                <i class="fa fa-upload"></i> Upload Excel/CSV
            </button>
        </div>
    </div>
    <div class="card-body p-3">
        <input type="file" id="poUploadInput" accept=".xlsx,.xls,.csv" hidden>

        <div
            id="poUploadConfig"
            data-project-id="<?php echo htmlspecialchars($project_id); ?>"
            data-tracking-id="<?php echo htmlspecialchars($tracking_id); ?>"
            data-doc-type="<?php echo htmlspecialchars($doc_type); ?>"
            data-sample-headers='<?php echo htmlspecialchars(json_encode($sample_headers), ENT_QUOTES, 'UTF-8'); ?>'
            hidden>
        </div>

        <div class="alert alert-info mb-3" id="poUploadInfo">
            Upload a PO file in <strong>.xlsx</strong>, <strong>.xls</strong>, or <strong>.csv</strong> format.
            The preview will highlight required empty fields in red before submit.
        </div>

        <div class="d-none mb-3" id="poUploadSummary"></div>
        <div class="d-none mb-3" id="poUploadErrors"></div>
        <div id="poUploadPreviewState">
            <div class="csr-po-upload-empty text-center">
                <p class="mb-2">No file uploaded yet.</p>
                <small class="text-muted">Download the sample format, fill the rows, then upload to preview the data.</small>
            </div>
        </div>

        <div class="pt-3 text-left">
            <button type="button" class="btn btn-primary" id="submitPoUploadBtn">
                Submit
            </button>
        </div>
    </div>
</div>

<script src="assets/pages/data-table/js/jszip.min.js"></script>
<script src="assets/js/custom_csr_po_excel_upload.js"></script>
