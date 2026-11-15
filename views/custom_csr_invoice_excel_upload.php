<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$tracking_id = $_POST['tracking_id'] ?? ($_POST['traking_id'] ?? '');
$project_id = $_POST['project_id'] ?? '';
$doc_type = $_POST['doc_type'] ?? 'INVOICE';

if (!$project_id || !$tracking_id || $doc_type !== 'INVOICE') {
    echo '<div class="alert alert-danger">' . __('Invalid upload request') . '</div>';
    exit;
}

$sample_headers = array(
    'PO No',
    'Financial Year',
    'Invoice No',
    'Invoice Date',
    'Taxable Value',
    'GST Amount',
    'Total Spent',
    'TDS Deducted',
    'Net Payable',
    'Paid Date',
    'Paid Amount',
    'Paid Bank'
);
?>
<link rel="stylesheet" href="assets/css/custom_csr_invoice_excel_upload.css">

<div class="card csr-invoice-upload-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><?= __('Invoice Excel Upload') ?></h5>
        <div class="d-flex gap-2 csr-invoice-upload-actions">
            <button type="button" class="btn btn-sm btn-primary" id="downloadInvoiceSampleBtn">
                <i class="fa fa-download"></i> <?= __('Sample Format Download') ?>
            </button>
            <button type="button" class="btn btn-sm btn-success" id="triggerInvoiceUploadBtn">
                <i class="fa fa-upload"></i> <?= __('Upload Excel/CSV') ?>
            </button>
        </div>
    </div>
    <div class="card-body p-3">
        <input aria-label="<?= htmlspecialchars(__('Invoice upload input'), ENT_QUOTES, 'UTF-8'); ?>" type="file" id="invoiceUploadInput" accept=".xlsx,.xls,.csv" hidden>

        <div
            id="invoiceUploadConfig"
            data-project-id="<?php echo htmlspecialchars($project_id); ?>"
            data-tracking-id="<?php echo htmlspecialchars($tracking_id); ?>"
            data-doc-type="<?php echo htmlspecialchars($doc_type); ?>"
            data-sample-headers='<?php echo htmlspecialchars(json_encode($sample_headers), ENT_QUOTES, 'UTF-8'); ?>'
            hidden>
        </div>

        <div class="alert alert-info mb-3" id="invoiceUploadInfo">
            <?= __('Upload an invoice file in') ?> <strong>.xlsx</strong>, <strong>.xls</strong>, <?= __('or') ?> <strong>.csv</strong> <?= __('format.') ?>
            <?= __('The preview will highlight required empty fields in red before submit.') ?>
        </div>

        <div class="d-none mb-3" id="invoiceUploadSummary"></div>
        <div class="d-none mb-3" id="invoiceUploadErrors"></div>
        <div id="invoiceUploadPreviewState">
            <div class="csr-invoice-upload-empty text-center">
                <p class="mb-2"><?= __('No file uploaded yet.') ?></p>
                <small class="text-muted"><?= __('Download the sample format, fill the rows, then upload to preview the data.') ?></small>
            </div>
        </div>

        <div class="pt-3 text-left">
            <button type="button" class="btn btn-primary" id="submitInvoiceUploadBtn">
                <?= __('Submit') ?>
            </button>
        </div>
    </div>
</div>

<script src="assets/pages/data-table/js/jszip.min.js"></script>
<script src="assets/js/custom_csr_invoice_excel_upload.js"></script>
