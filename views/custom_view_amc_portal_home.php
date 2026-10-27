<?php
include_once __DIR__ . '/party_contracts_table_body.php';
$url = API . "?mode=getamcportalhomedata&method=get";
$response = callAPI($url);
$data = json_decode($response, true);
$receive = $data['receive'] ?? array();
$provide = $data['provide'] ?? array();
$dashboard = $data['dashboard'] ?? array();
$hasReceive = (int) ($receive['active_count'] ?? 0) + (int) ($receive['inactive_count'] ?? 0) > 0;
$hasProvide = (int) ($provide['active_count'] ?? 0) + (int) ($provide['inactive_count'] ?? 0) > 0;
$hasFmDashboard = !empty($data['fm_has_access']);
?>
<div class="page-body">
    <div class="row mb-3">
        <div class="col-md-2"><div class="card bg-info text-white p-3 text-center"><h4><?php echo (int) ($dashboard['today_visits'] ?? 0); ?></h4><small>Today</small></div></div>
        <div class="col-md-2"><div class="card bg-primary text-white p-3 text-center"><h4><?php echo (int) ($dashboard['next_7_days'] ?? 0); ?></h4><small>Next 7 Days</small></div></div>
        <div class="col-md-2"><div class="card bg-danger text-white p-3 text-center"><h4><?php echo (int) ($dashboard['overdue'] ?? 0); ?></h4><small>Overdue</small></div></div>
        <div class="col-md-2"><div class="card bg-warning text-white p-3 text-center"><h4><?php echo (int) ($dashboard['in_progress'] ?? 0); ?></h4><small>In Progress</small></div></div>
        <div class="col-md-2"><div class="card bg-success text-white p-3 text-center"><h4><?php echo (int) ($dashboard['completed_today'] ?? 0); ?></h4><small>Done Today</small></div></div>
    </div>

    <?php if ($hasFmDashboard) { ?>
    <div class="card mb-3 border-primary">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1">Facility Manager</h5>
                <p class="text-muted mb-0 f-12">Confirm vendor AMC visits after technician completion</p>
            </div>
            <a href="index.php?option=COM_VIEW_CONTRACT_AMC_FM_DASHBOARD" class="btn btn-primary btn-sm">Open Dashboard</a>
        </div>
    </div>
    <?php } ?>

    <?php if ($hasReceive) { ?>
    <div class="card mb-3">
        <div class="card-header"><h5>Services I Receive</h5></div>
        <div class="card-body table-responsive">
            <table class="table table-sm f-12">
                <thead>
                    <tr>
                        <th scope="col">#</th><th scope="col">Contract</th><th scope="col">Txn</th><th scope="col">AMC?</th><th scope="col">Next</th><th scope="col">Overdue</th><th scope="col">Last Done</th><th scope="col">Pending</th><th scope="col">Start</th><th scope="col">End</th><th scope="col">Status</th><th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody><?php party_contracts_render_rows($receive['active_contracts'] ?? array()); ?></tbody>
            </table>
        </div>
    </div>
    <?php } ?>

    <?php if ($hasProvide) { ?>
    <div class="card mb-3">
        <div class="card-header"><h5>Services I Provide</h5></div>
        <div class="card-body table-responsive">
            <table class="table table-sm f-12">
                <thead>
                    <tr>
                        <th scope="col">#</th><th scope="col">Contract</th><th scope="col">Txn</th><th scope="col">AMC?</th><th scope="col">Next</th><th scope="col">Overdue</th><th scope="col">Last Done</th><th scope="col">Pending</th><th scope="col">Start</th><th scope="col">End</th><th scope="col">Status</th><th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody><?php party_contracts_render_rows($provide['active_contracts'] ?? array()); ?></tbody>
            </table>
        </div>
    </div>
    <?php } ?>

    <?php if (!$hasReceive && !$hasProvide) { ?>
    <div class="alert alert-info">No AMC contracts linked to your login. Contact admin to map your user to a customer or vendor record.</div>
    <?php } ?>
</div>
