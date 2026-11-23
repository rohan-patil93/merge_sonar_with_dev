<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$tracking_id = $_POST['traking_id'] ?? '';
$project_id = $_POST['project_id'] ?? '';

$table_name = 'csr_po_details';
$vdata1['order'] = "id ASC";
$vdata1["where"] = array(
    'company_id' => $_SESSION['company_id'],
    'tracking_id' => $tracking_id,
    'project_id' => $project_id,
    'is_active' => 0
);
$vdata = urlencode(json_encode($vdata1));
$vurl = API . "?mode=gettabledetailswithconditions&table=$table_name&data=$vdata";
$che = callAPI($vurl);
$return = json_decode($che, true);
$poData = $return ?? array();


$table_name = 'general_code_details';
$vdata1['order'] = "id ASC";
$vdata1["where"] = array(
    'company_id' => $_SESSION['company_id'],
    'genc_code' => 'CSR_FINANCIAL_YEAR',
    'is_active' => 0
);
$vdata = urlencode(json_encode($vdata1));
$vurl = API . "?mode=gettabledetailswithconditions&table=$table_name&data=$vdata";
$che = callAPI($vurl);
$return = json_decode($che, true);
$fyRows = $return ?? array();

?>
<link rel="stylesheet" href="assets/css/custom_csr_po_details.css">

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><?= __('Purchase Order Details') ?></h5>
        <?php if(in_array($_SESSION['working_role_short_code'], array('CSR_ADMIN', 'ADMIN'))){ ?>
        <div class="csr-po-header-actions">
            <button type="button" class="btn btn-sm btn-warning" id="poExcelUploadBtn">
                <i class="fa fa-file-excel-o"></i> <?= __('Excel Upload') ?>
            </button>
            <button type="button" class="btn btn-sm btn-success" id="addRowBtn">
                <i class="fa fa-plus"></i> <?= __('Row') ?>
            </button>
        </div>
        <?php } ?>
    </div>
    <div class="card-body p-3">
        <form id="poForm" method="post">
            <input type="hidden" name="tracking_id" value="<?php echo htmlspecialchars($tracking_id); ?>">
            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">
            <div id="poRowsWrapper">
                <?php
                if(count((array)$poData) =='0'){
                        $poData[] = array();
                }
                if (count((array)$poData) > 0) {
                    foreach ($poData as $i => $row) {
                        $index = $i + 1;
                        $checkedYes = $row['po_no'] != 'NON PO' ? 'checked' : '';
                        $checkedNo  = $row['po_no'] == 'NON PO' ? 'checked' : '';
                        $poNoHidden = $row['po_no'] == 'NON PO' ? 'd-none' : '';
                ?>
                        <div class="row po-row border-bottom mb-3 pb-2" data-row="<?php echo $index; ?>">
                            <input type="hidden" name="id[]" value="<?php echo $row['id']; ?>">
                            <div class="col-lg-2 form-group">
                                <span class="form-label d-inline-block fw-bold"><?= __('PO Available?') ?> <span class="text-danger">*</span></span><br>
                                <div class="form-check form-check-inline">
                                    <input id="po_toggle_yes_<?php echo $index; ?>" class="form-check-input po_toggle" type="radio" name="po_toggle_<?php echo $index; ?>" value="0" <?php echo $checkedYes; ?>>
                                    <label class="form-check-label" for="po_toggle_yes_<?php echo $index; ?>"><?= __('Yes') ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="po_toggle_no_<?php echo $index; ?>" class="form-check-input po_toggle" type="radio" name="po_toggle_<?php echo $index; ?>" value="1" <?php echo $checkedNo; ?>>
                                    <label class="form-check-label" for="po_toggle_no_<?php echo $index; ?>"><?= __('No') ?></label>
                                </div>
                            </div>

                            <div class="col-lg-2 form-group">
                                <label for="fld_financial_year_<?php echo $index; ?>"><?= __('Financial Year') ?> <span class="text-danger">*</span></label>
                                <select id="fld_financial_year_<?php echo $index; ?>" name="financial_year[]" class="form-control form-select financial_year" required>
                                    <option value=""><?= __('Select FY') ?></option>
                                    <?php
                                    $saved_fy = $row['financial_year'] ?? '';

                                    if(!empty($fyRows)){
                                        foreach($fyRows as $fyRow){
                                            $fy_value = $fyRow['genc_detail_code']; // example 2013-14
                                            $selected = ($saved_fy == $fy_value) ? 'selected' : '';
                                            echo "<option value='$fy_value' $selected>$fy_value</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-lg-2 form-group po_no_div <?php echo $poNoHidden; ?>" >
                                <label for="fld_po_no_<?php echo $index; ?>"><?= __('PO No.') ?> <span class="text-danger">*</span></label>
                                <input id="fld_po_no_<?php echo $index; ?>" class="form-control po_no" type="text" name="po_no[]" value="<?php echo htmlspecialchars($row['po_no']); ?>">
                            </div>

                            <div class="col-lg-2 form-group">
                                <label for="fld_po_date_<?php echo $index; ?>"><?= __('PO Date') ?> <span class="text-danger">*</span></label>
                                <input id="fld_po_date_<?php echo $index; ?>" class="form-control po_date" type="date" name="po_date[]" step="any" value="<?php echo htmlspecialchars($row['po_date']); ?>">
                            </div>

                            <div class="col-lg-2 form-group">
                                <label for="fld_po_amount_<?php echo $index; ?>"><?= __('PO Amount') ?> <span class="text-danger">*</span></label>
                                <input id="fld_po_amount_<?php echo $index; ?>" class="form-control po_amount" type="number" step="any" name="po_amount[]" value="<?php echo htmlspecialchars($row['po_amount']); ?>">
                            </div>

                            <div class="col-lg-2 form-group">
                                <label for="fld_po_value_ex_<?php echo $index; ?>"><?= __('PO Value (Excl. Tax)') ?> <span class="text-danger">*</span></label>
                                <input id="fld_po_value_ex_<?php echo $index; ?>" class="form-control po_value_ex" type="number" step="any" name="po_value_ex[]" value="<?php echo htmlspecialchars($row['po_value_ex']); ?>">
                            </div>

                            <div class="col-lg-1 form-group">
                                <label for="fld_gst_<?php echo $index; ?>"><?= __('GST %') ?></label>
                                <input id="fld_gst_<?php echo $index; ?>" class="form-control gst" type="number" step="any" name="gst[]" value="<?php echo htmlspecialchars($row['gst']); ?>">
                            </div>

                            <div class="col-lg-2 form-group">
                                <label for="fld_po_value_in_<?php echo $index; ?>"><?= __('PO Value (Incl. Tax)') ?></label>
                                <input id="fld_po_value_in_<?php echo $index; ?>" class="form-control po_value_in" type="number" step="any" name="po_value_in[]" value="<?php echo htmlspecialchars($row['po_value_in']); ?>">
                            </div>

                            <div class="col-lg-2 form-group">
                                <label for="fld_business_code_<?php echo $index; ?>"><?= __('Business Code') ?></label>
                                <input id="fld_business_code_<?php echo $index; ?>" class="form-control business_code" type="text" name="business_code[]" value="<?php echo htmlspecialchars($row['business_code']); ?>">
                            </div>

                            <div class="col-lg-2 form-group">
                                <label for="fld_business_name_<?php echo $index; ?>"><?= __('Business Name') ?></label>
                                <input id="fld_business_name_<?php echo $index; ?>" class="form-control business_name" type="text" name="business_name[]" value="<?php echo htmlspecialchars($row['business_name']); ?>">
                            </div>

                            <div class="col-lg-2 form-group">
                                <label for="fld_po_details_<?php echo $index; ?>"><?= __('Other PO  Details') ?></label>
                                <textarea id="fld_po_details_<?php echo $index; ?>" class="form-control po_details" name="po_details[]" rows="1"><?php echo htmlspecialchars($row['po_details']); ?></textarea>
                            </div>

                            <div class="col-lg-2 form-group">
                                <label for="fld_remarks_<?php echo $index; ?>"><?= __('Remarks') ?></label>
                                <textarea id="fld_remarks_<?php echo $index; ?>" class="form-control remarks" name="remarks[]" rows="1"><?php echo htmlspecialchars($row['remarks']); ?></textarea>
                            </div>
                            <?php if(in_array($_SESSION['working_role_short_code'], array('CSR_ADMIN', 'ADMIN'))){ ?>
                            <div class="col-lg-1 d-flex align-items-center">
                                <button type="button" class="btn btn-danger btn-sm removeRowBtn" aria-label="<?= __('Delete') ?>">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            <?php } ?>
                        </div>
                    <?php }
                } else { ?>
                    <!-- if no data, start with one empty row -->
                <?php } ?>
            </div>


            <div class=" pt-3">
                <?php if(in_array($_SESSION['working_role_short_code'], array('CSR_ADMIN', 'ADMIN'))){ ?>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <?= __('Save') ?>
                    </button>
                <?php } ?>
            </div>
        </form>
    </div>
</div>
<div
    id="csr_config"
    data-po-row-count="<?= max(1, count((array)$poData)) ?>"
    data-fy-rows='<?php echo json_encode($fyRows); ?>'
    hidden>
</div>


<script src="assets/js/custom_csr_po_details_new.js"></script>
