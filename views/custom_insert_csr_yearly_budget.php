<?php

// Initialize variables

if ($_POST['submit']) {
    $table_name = 'csr_yearly_budget';
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        unset($_POST['rid']);

        $_POST['current_status'] = '0';
        $_POST['is_active'] = '0';
        $_POST['status_date'] = date('Y-m-d H:i:s');
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_date'] = date('Y-m-d H:i:s');
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        $response = json_decode($response, 1);
    }if ($_POST['submit'] == 'update') {
        unset($_POST['submit']);
        $rid = $_POST['rid'];
        unset($_POST['rid']);
        $_POST['updated_date'] = date('Y-m-d H:i:s');
        $_POST['updated_by'] = $_SESSION['userid'];
        $url = API . "?mode=updaterowbasepimarydata&rid=" . $rid . "&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'update');
        $response = json_decode($response, 1);
    }
}
if(isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
} else {
    $rid = '';
}

// Define the API endpoint
$url = API . "?mode=getCSRYearlyBudget&method=get";
$response = callAPI($url);
$filteredData = json_decode($response, true)['csr_yearly_budget'] ?? array();
$valueData = array();
// Filter the data based on the provided Rid
if ($rid != '') {
    $valueData =  $filteredData[$rid] ?? [];
}
?>

<link rel="stylesheet" href="assets/css/custom_insert_csr_yearly_budget.css">

<div class="page-body">
    <div class="card proj-progress-card">
        <div class="card-header">
            <h5>CSR Yearly Budget</h5>
            <div class="card-header-right">
                <a href="javascript:void(0);" id="showFormBtn" class="btn btn-success mb-2 desktop-view">
                    <i class="icofont icofont-plus icon-white"></i>
                    Budget
                </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-block">

            <form method="POST" action="" class="form-inline mb-3 d-none" id="budgetForm" autocomplete="off">
                <div class="row">
                    <div class="col-md-4 form-group mr-3">
                        <label for="base_year" class="mr-2">Base Year</label>
                        <input type="number" id="base_year" name="base_year" class="form-control js-base-year" required
                            value="<?php if(isset($rid) && $rid != ''){ echo $valueData['base_year'];
} ?>"
                            >
                        <span id="baseYearError" class="text-danger base-year-error">Base Year already exists!</span>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="finance_budget">Finance Budget</label>
                        <input type="number" step="any" id="finance_budget" name="finance_budget" class="form-control" required
                            value="<?php if(isset($rid) && $rid != ''){ echo $valueData['finance_budget'];
} ?>">
                    </div>
                    <div class="col-md-4 align-self-end form-group">
                        <input type="hidden" id="rid" name="rid" value="">
                        <button name="submit" value="insert" type="submit" class="btn btn-primary" id="budgetSubmitBtn">Submit</button>
                    </div>
                </div>
            </form>

            <!-- TABLE: always visible -->
            <div class="table-responsive" id="budgetTable">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Sr. No</th>
                            <th scope="col">Creator</th>
                            <th scope="col">Base Year</th>
                            <th scope="col">Finance Budget</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$filteredData) > 0) { ?>
                            <?php $i = 1;
                            foreach ($filteredData as $row) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <?php if ($_SESSION['user_image'][$row['created_by']] != ""): ?>
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                        <?php else: ?>
                                            <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
                                        <?php endif; ?>
                                        <span class='mt-2 ml-2'><?php echo $_SESSION['users'][$row['created_by']]; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['base_year'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end"><?php echo customeummarmonyformate($row['finance_budget']); ?></td>
                                    <td><?php echo fromDate($row['created_date']) ?? ''; ?></td>
                                    <td>
                                        <span class="form-label label label-md status-label"
      data-color="<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>"
      data-bg="<?php echo $_SESSION['status'][$row['current_status']]['background_color'] ?? 'blue'; ?>">
                                            <?php
                                            if ($row['current_status'] == 0) {
                                                echo "Active";
                                            } elseif ($row['current_status'] > 0 && $row['current_status'] < 199) {
                                                echo "In Progress";
                                            } elseif ($row['current_status'] == 199 && $row['tracking_status'] == 0) {
                                                echo "Tracking";
                                            } elseif ($row['current_status'] == 199 && $row['tracking_status'] == 1) {
                                                echo "Completed";
                                            } elseif ($row['current_status'] == 199) {
                                                echo "Approved";
                                            } else {
                                                echo "Unknown";
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($row['current_status'] != 199) {?>
                                        <a class="text-primary editBudget"
                                            href="javascript:void(0);"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-base_year="<?php echo htmlspecialchars($row['base_year'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-finance_budget="<?php echo htmlspecialchars($row['finance_budget'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                                        </a>
                                        <?php } ?>
                                        <a class="text-primary"
                                            href="index.php?option=COM_LIST_CSR_YEARLY_BUDGET&Rid=<?php echo deocdeId('id:' . $row['id']); ?>">
                                            <i class="fa fa-eye btn-primary p-2 b-radius-5"></i>
                                        </a>
                                    </td>
                                </tr>
                        <?php }
                        } else {
                            echo '<tr> <td colspan="7" class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px no-record-img" alt="" src="assets/images/work.png" >
                                        </div>
                                     </td> </tr> ';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

        <?php
        $years = [];
        foreach ($filteredData as $row) {
            $years[] = (int)$row['base_year'];
        }
        ?>

<div
    id="csr_config"
    data-existing-base-years='<?= htmlspecialchars(json_encode($years), ENT_QUOTES, "UTF-8") ?>'
    hidden>
</div>

<script src="assets/js/custom_insert_csr_yearly_budget.js"></script>
