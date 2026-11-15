<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;

$tracking_id = $_POST['traking_id'] ?? '';
$project_id = $_POST['project_id'] ?? '';

$table_name = 'csr_invoice_details';
$vdata1['order'] = "id ASC";
$vdata1["where"] = array(
    'company_id' => $_SESSION['company_id'],
    'tracking_id' => $tracking_id,
    'project_id' => $project_id,
    'is_active' => 0
);
$vdata = urlencode(json_encode($vdata1));
$vurl = API . "?mode=getCsrInvoiceDetails&tracking_id=$tracking_id&project_id=$project_id";
$che = callAPI($vurl);
$return = json_decode($che, true);
$invData = $return['csr_invoices'] ?? array();
$csr_pos = $return['csr_pos'] ?? array();
$fyRows = $return['fyRows'] ?? array();
?>
<link rel="stylesheet" href="assets/css/custom_csr_invoice_settlement.css">

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><?= __('Invoice Details') ?></h5>
        <?php if(in_array($_SESSION['working_role_short_code'], array('CSR_ADMIN', 'ADMIN'))){ ?>
            <div class="csr-invoice-header-actions">
                <button type="button" class="btn btn-sm btn-warning" id="invoiceExcelUploadBtn">
                    <i class="fa fa-file-excel-o"></i> <?= __('Excel Upload') ?>
                </button>
            <button type="button" class="btn btn-sm btn-success" id="addInvRowBtn">
                <i class="fa fa-plus"></i> <?= __('Row') ?>
            </button>
            </div>
        <?php } ?>

    </div>
    <div class="card-body p-3">
        <div id="csrData"
             data-csr_pos='<?php echo json_encode($csr_pos); ?>'
             data-fy_rows='<?php echo json_encode($fyRows); ?>'
             data-inv_row_count="<?php echo max(1, count((array)$invData)); ?>">
        </div>
        <form id="invoiceForm" method="post">
            <input type="hidden" name="tracking_id" value="<?php echo htmlspecialchars($tracking_id); ?>">
            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">
            <div id="invoiceRowsWrapper">
                <?php
                if(count((array)$invData) == '0'){
                    $invData[] = array();
                }
                if (count((array)$invData) > 0) {
                    foreach ($invData as $i => $row) {
                        $index = $i + 1;
                ?>
                <div class="row inv-row border-bottom mb-3 pb-2" data-row="<?php echo $index; ?>">
                    <input type="hidden" name="id[]" value="<?php echo $row['id']; ?>">
                    <div class="col-lg-2 form-group w-full">
                        <label for="fld_csr_po_details_id_<?php echo $index; ?>"><?= __('PO No') ?></label>
                        <select id="fld_csr_po_details_id_<?php echo $index; ?>" class="form-control js-example-basic-single" name="csr_po_details_id[]">
                            <option value=""><?= __('Select Any One') ?></option>
                            <?php foreach ((array)$csr_pos as $po) { ?>
                                <option value="<?php echo htmlspecialchars($po['id']); ?>" <?php if ($row['csr_po_details_id'] == $po['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($po['po_no']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-2 form-group">
                        <label for="fld_financial_year_<?php echo $index; ?>"><?= __('Financial Year') ?> <span class="text-danger">*</span></label>
                        <select id="fld_financial_year_<?php echo $index; ?>" name="financial_year[]" class="form-control form-select financial_year" required>
                            <option value=""><?= __('Select FY') ?></option>
                            <?php
                                $saved_fy = $row['financial_year'] ?? '';
                                if(!empty((array)$fyRows)){
                                    foreach($fyRows as $fyRow){
                                        $fy_value = $fyRow['genc_detail_code']; // example 2013-14
                                        $selected = ($saved_fy == $fy_value) ? 'selected' : '';
                                        echo "<option value='$fy_value' $selected>$fy_value</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-2 form-group">
                        <label for="fld_inv_no_<?php echo $index; ?>"><?= __('Invoice No') ?> <span class="text-danger">*</span></label>
                        <input id="fld_inv_no_<?php echo $index; ?>" class="form-control inv_no" type="text" name="inv_no[]" value="<?php echo htmlspecialchars($row['inv_no']); ?>">
                    </div>

                    <div class="col-lg-2 form-group">
                        <label for="fld_inv_date_<?php echo $index; ?>"><?= __('Invoice Date') ?> <span class="text-danger">*</span></label>
                        <input id="fld_inv_date_<?php echo $index; ?>" class="form-control inv_date" type="date" name="inv_date[]" value="<?php echo htmlspecialchars($row['inv_date']); ?>">
                    </div>

                    <div class="col-lg-2 form-group">
                        <label for="fld_taxable_value_<?php echo $index; ?>"><?= __('Taxable Value') ?> <span class="text-danger">*</span></label>
                        <input id="fld_taxable_value_<?php echo $index; ?>" class="form-control taxable_value" type="number" step="any" name="taxable_value[]" value="<?php echo htmlspecialchars($row['taxable_value']); ?>">
                    </div>
                     <div class="col-lg-2 form-group">
                        <label for="fld_gst_percent_<?php echo $index; ?>"><?= __('GST %') ?> <span class="text-danger">*</span></label>
                        <input id="fld_gst_percent_<?php echo $index; ?>" class="form-control gst_percent text-end" type="number" step="any" name="gst_percent[]" value="<?php echo htmlspecialchars($row['gst_percent']); ?>">
                    </div>


                    <div class="col-lg-2 form-group">
                        <label for="fld_gst_amount_<?php echo $index; ?>"><?= __('GST Amount') ?> <span class="text-danger">*</span></label>
                        <input id="fld_gst_amount_<?php echo $index; ?>" class="form-control gst_amount" type="number" min="0" step="any" name="gst_amount[]" value="<?php echo htmlspecialchars($row['gst_amount']); ?>">
                    </div>

                    <div class="col-lg-2 form-group">
                        <label for="fld_total_spent_<?php echo $index; ?>"><?= __('Total Spent') ?> <span class="text-danger">*</span></label>
                        <input id="fld_total_spent_<?php echo $index; ?>" class="form-control total_spent" type="number" step="any" name="total_spent[]" value="<?php echo htmlspecialchars($row['total_spent']); ?>">
                    </div>

                    <div class="col-lg-2 form-group">
                        <label for="fld_tds_deducted_<?php echo $index; ?>"><?= __('TDS Deducted') ?></label>
                        <input id="fld_tds_deducted_<?php echo $index; ?>" class="form-control tds_deducted" type="number" step="any" name="tds_deducted[]" value="<?php echo htmlspecialchars($row['tds_deducted']); ?>">
                    </div>

                    <div class="col-lg-2 form-group">
                        <label for="fld_net_payable_<?php echo $index; ?>"><?= __('Net Payable') ?> <span class="text-danger">*</span></label>
                        <input id="fld_net_payable_<?php echo $index; ?>" class="form-control net_payable" type="number" step="any" name="net_payable[]" value="<?php echo htmlspecialchars($row['net_payable']); ?>">
                    </div>
                    <div class="col-lg-2 form-group">
                        <label for="fld_paid_date_<?php echo $index; ?>"><?= __('Paid Date') ?> <span class="text-danger">*</span></label>
                        <input id="fld_paid_date_<?php echo $index; ?>" class="form-control paid_date" type="date" name="paid_date[]" value="<?php echo htmlspecialchars($row['paid_date']); ?>">
                    </div>

                    <div class="col-lg-2 form-group">
                        <label for="fld_paid_amount_<?php echo $index; ?>"><?= __('Paid Amount') ?> <span class="text-danger">*</span></label>
                        <input id="fld_paid_amount_<?php echo $index; ?>" class="form-control paid_amount" type="number" step="any" name="paid_amount[]" value="<?php echo htmlspecialchars($row['paid_amount']); ?>">
                    </div>

                    <div class="col-lg-2 form-group">
                        <label for="fld_paid_bank_<?php echo $index; ?>"><?= __('Paid Bank') ?> <span class="text-danger">*</span></label>
                        <input id="fld_paid_bank_<?php echo $index; ?>" class="form-control paid_bank" type="text" name="paid_bank[]" value="<?php echo htmlspecialchars($row['paid_bank']); ?>">
                    </div>
                    <?php if(in_array($_SESSION['working_role_short_code'], array('CSR_ADMIN', 'ADMIN'))){ ?>
                    <div class="col-lg-1 d-flex align-items-center">
                        <button type="button" class="btn btn-danger btn-sm removeInvRowBtn" aria-label="<?= __('Delete') ?>">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                    <?php } ?>
                </div>
                <?php }
                } ?>
            </div>

            <div class="pt-3">
                <?php if(in_array($_SESSION['working_role_short_code'], array('CSR_ADMIN', 'ADMIN'))){ ?>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <?= __('Save') ?>
                    </button>
                <?php } ?>
            </div>
        </form>
    </div>
</div>



<script src="assets/js/custom_csr_invoice_settlement_new.js"></script>
