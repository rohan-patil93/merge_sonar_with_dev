<?php
if(isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
} else {
    $rid = '';
}
$table_name = 'csr_yearly_budget';

$url = API . "?mode=getCSRYearlyBudget&method=get&rid=" . $rid;
$csr_res_arr = callAPI($url);
$csr_res_arr = json_decode($csr_res_arr, 1);

$csr_proposal_arr = $csr_res_arr['csr_proposal_arr'];
$csr_yearly_budget = $csr_res_arr['csr_yearly_budget'];

$completedCount = 0;
$approvedCount = 0;
$progressCount = 0;
$activeCount = 0;
$trackingCount = 0;
$totalCount = count($csr_proposal_arr) ?? 0;

$totalValue = $approvedValue  = 0;

foreach ($csr_proposal_arr as $row) {
    $csr_data[$row['id']] = $row;
    $totalValue += $row['total_budget_grand_total'] ?? 0;

    if (isset($row['current_status']) && $row['current_status'] == 199) {
        $approvedCount++;
        $approved_data[] = $row['id'];
        $approvedValue += $row['total_budget_grand_total'] ?? 0;
    }
}
$progress_var =  gettableStatusArray($table_name);
?>

<link rel="stylesheet" href="assets/css/custom_list_csr_yearly_budget.css">

<div class="page-body">
    <div class="card">
        <div class="card-header" >
            <div>
                <?php if ($rid != ''): ?>
                    <?php $workflowsteps = getWorkflowStatus($table_name, $csr_yearly_budget['id'], $csr_yearly_budget['current_status']); ?>
                    <div class="text-left"><?php echo $workflowsteps['breadcrumb']; ?></div>
                    <div class="text-right m-r-50">
                        <?php
                        echo $workflowsteps['workflow_btn'];

                        ?>
                    </div>
                    <?php echo $workflowsteps['current_status_ribbon']; ?>
                <?php endif; ?>
            </div>
            <div class="card-header-right card-header-right-workflow" >

                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-xl-3 col-md-6">
            <div class="card ticket-card ticket-card-fixed" >
                <div class="card-body">
                    <p class="m-b-30 bg-c-red lbl-card"><i class="fas fa-file-archive"></i><?php echo $csr_yearly_budget['base_year'] ?> - Budget</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red "> <?php echo customeummarmonyformate($csr_yearly_budget['finance_budget']) ?? 0.00; ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card ticket-card ticket-card-fixed" >
                <div class="card-body">
                    <p class="m-b-30 bg-c-red lbl-card"><i class="fas fa-file-archive"></i> Total CSRs/<?php echo $totalCount; ?></p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red "> <?php echo customeummarmonyformate($totalValue); ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card ticket-card ticket-card-fixed" >
                <div class="card-body">
                    <p class="m-b-30 bg-c-lite-green lbl-card"><i class="fas fa-file-archive"></i> Approved CSRs/<?php echo $approvedCount; ?></p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-lite-green ">
                            <?php echo customeummarmonyformate($approvedValue); ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card ticket-card ticket-card-fixed" >
                <div class="card-body">
                    <p class="m-b-30 bg-c-green lbl-card"><i class="fas fa-file-archive"></i><?php echo $csr_yearly_budget['base_year'] ?> - Available Budget</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-green ">
                            <?php echo customeummarmonyformate($csr_yearly_budget['finance_budget'] - $approvedValue) ?? 0; ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script src="assets/js/custom_list_csr_yearly_budget.js"></script>
