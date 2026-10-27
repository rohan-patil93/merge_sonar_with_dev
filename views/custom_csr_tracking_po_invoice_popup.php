<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');

$project_id  = $_POST['project_id'] ?? 0;
$tracking_id = $_POST['traking_id'] ?? 0;
$doc_type    = $_POST['doc_type'] ?? '';

if (!$project_id || !$tracking_id || !$doc_type) {
    echo '<div class="alert alert-danger">Invalid request</div>';
    exit;
}
?>

<?php
	if ($doc_type === 'PO') {
	    include 'custom_csr_po_details.php';
	} elseif ($doc_type === 'INVOICE') {
	    include 'custom_csr_invoice_settlement.php';
	} else {
	    echo '<div class="alert alert-warning">Invalid document type</div>';
	}
?>


