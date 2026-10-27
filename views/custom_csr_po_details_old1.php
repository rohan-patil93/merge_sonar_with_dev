<?php
// print_r($_POST);
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
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

<div class="modal-header">
    <h5 class="modal-title">Purchase Order Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Purchase Order Details</h5>
                <?php if(in_array($_SESSION['working_role_short_code'], array('CSR_ADMIN', 'ADMIN'))){ ?>
                <button type="button" class="btn btn-sm btn-success" id="addRowBtn">
                    <i class="fa fa-plus"></i>Row
                </button>
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
                                        <label>PO Available? <span class="text-danger">*</span></label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input po_toggle" type="radio" name="po_toggle_<?php echo $index; ?>" value="0" <?php echo $checkedYes; ?>>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input po_toggle" type="radio" name="po_toggle_<?php echo $index; ?>" value="1" <?php echo $checkedNo; ?>>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 form-group">
                                        <label>Financial Year <span class="text-danger">*</span></label>
                                        <select name="financial_year[]" class="form-control form-select financial_year" required>
                                            <option value="">Select FY</option>
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
                                        <label>PO No. <span class="text-danger">*</span></label>
                                        <input class="form-control po_no" type="text" name="po_no[]" value="<?php echo htmlspecialchars($row['po_no']); ?>">
                                    </div>

                                    <div class="col-lg-2 form-group">
                                        <label>PO Date <span class="text-danger">*</span></label>
                                        <input class="form-control po_date" type="date" name="po_date[]" value="<?php echo htmlspecialchars($row['po_date']); ?>">
                                    </div>

                                    <div class="col-lg-2 form-group">
                                        <label>PO Amount <span class="text-danger">*</span></label>
                                        <input class="form-control po_amount" type="number" step="0.01" name="po_amount[]" value="<?php echo htmlspecialchars($row['po_amount']); ?>">
                                    </div>

                                    <div class="col-lg-2 form-group">
                                        <label>PO Value (Excl. Tax) <span class="text-danger">*</span></label>
                                        <input class="form-control po_value_ex" type="number" step="0.01" name="po_value_ex[]" value="<?php echo htmlspecialchars($row['po_value_ex']); ?>">
                                    </div>

                                    <div class="col-lg-1 form-group">
                                        <label>GST %</label>
                                        <input class="form-control gst" type="number" step="0.01" name="gst[]" value="<?php echo htmlspecialchars($row['gst']); ?>">
                                    </div>

                                    <div class="col-lg-2 form-group">
                                        <label>PO Value (Incl. Tax)</label>
                                        <input class="form-control po_value_in" type="number" step="0.01" name="po_value_in[]" value="<?php echo htmlspecialchars($row['po_value_in']); ?>" >
                                    </div>

                                    <div class="col-lg-2 form-group">
                                        <label>Business Code</label>
                                        <input class="form-control business_code" type="text" name="business_code[]" value="<?php echo htmlspecialchars($row['business_code']); ?>">
                                    </div>

                                    <div class="col-lg-2 form-group">
                                        <label>Business Name</label>
                                        <input class="form-control business_name" type="text" name="business_name[]" value="<?php echo htmlspecialchars($row['business_name']); ?>">
                                    </div>

                                    <div class="col-lg-2 form-group">
                                        <label>Other PO  Details</label>
                                        <textarea class="form-control po_details" name="po_details[]" rows="1"><?php echo htmlspecialchars($row['po_details']); ?></textarea>
                                    </div>

                                    <div class="col-lg-2 form-group">
                                        <label>Remarks</label>
                                        <textarea class="form-control remarks" name="remarks[]" rows="1"><?php echo htmlspecialchars($row['remarks']); ?></textarea>
                                    </div>
                                    <?php if(in_array($_SESSION['working_role_short_code'], array('CSR_ADMIN', 'ADMIN'))){ ?>
                                    <div class="col-lg-1 d-flex align-items-center">
                                        <button type="button" class="btn btn-danger btn-sm removeRowBtn">
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
                                Save
                            </button>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div
    id="csr_config"
    data-po-row-count="<?= max(1, count((array)$poData)) ?>"
    hidden>
</div>


<script src="assets/js/custom_csr_po_details.js"></script>