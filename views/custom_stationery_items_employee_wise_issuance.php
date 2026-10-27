<?php
$_SESSION['apply_js']['multiselest'] = 1;

$url = API . "?mode=getEmployeeWiseIssuanceDetails";
$che = callAPI($url);
$data = json_decode($che, 1);

$employee_res = $data['employee_res'] ?? array();
?>
<script src="assets/js/custom_stationery_items_employee_wise_issuance.js"></script>
<div class="card">
    <div class="card-header">
        <h5> Stationery Employee-wise Issuance Details</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row align-items-end">

            <div class="col-lg-4">
                <span>Employees</span>
                <select aria-label="Employee Ids" class="form-control js-example-basic-single" multiple id="employee_ids">
                    <?php
                    if(!empty($employee_res)){
                        foreach($employee_res as $emp){
                            ?>
                            <option value="<?php echo $emp['id']; ?>">
                                <?php echo htmlspecialchars($emp['emp_code'] . ' - ' . $emp['display_name']); ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                    <option value="999999">Others</option>
                </select>
            </div>

            <div class="col-lg-2">
                <button type="button" class="btn btn-primary" id="search_employee_issuance">
                    Search
                </button>
            </div>

        </div>
    </div>
</div>

<div id="dynamic_table_page"></div>

