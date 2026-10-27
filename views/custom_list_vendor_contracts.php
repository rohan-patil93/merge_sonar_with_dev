<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
include_once __DIR__ . '/party_contracts_table_body.php';

$vendor_id = $_POST['vendor_id'] ?? '';
$url = API . "?mode=getvendorcontracts&method=get&vendor_id=" . $vendor_id;
$response = callAPI($url);
$result = json_decode($response, true);

$active_contracts = $result['active_contracts'] ?? array();
$inactive_contracts = $result['inactive_contracts'] ?? array();
$active_count = $result['active_count'] ?? 0;
$inactive_count = $result['inactive_count'] ?? 0;
?>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0">Vendor Contracts</h5>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <a href="index.php?option=COM_INSERT_CONTRACTS&party_type=vendor&party_id=<?php echo htmlspecialchars($vendor_id); ?>" class="btn btn-success btn-sm">
                <i class="icofont icofont-plus text-white"></i> Add Contract
            </a>
            <ul class="nav md-tabs infisuite_tab mb-0">
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#vendor_contracts_active" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        Active
                        <span class="form-label label label-xs label-success m-l-10"><?php echo $active_count; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#vendor_contracts_inactive" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        Inactive
                        <span class="form-label label label-xs label-success m-l-10"><?php echo $inactive_count; ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="tab-content tabs card-block mt-3">
        <div class="tab-pane active" id="vendor_contracts_active" role="tabpanel">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover table-sm f-12">
                        <thead>
                            <tr>
                                <th scope="col">SL No</th>
                                <th scope="col">Contract Name</th>
                                <th scope="col">Transaction No</th>
                                <th scope="col">AMC?</th>
                                <th scope="col">Next Visit</th>
                                <th scope="col">Overdue</th>
                                <th scope="col">Last Completed</th>
                                <th scope="col">Pending</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php party_contracts_render_rows($active_contracts); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="vendor_contracts_inactive" role="tabpanel">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover table-sm f-12">
                        <thead>
                            <tr>
                                <th scope="col">SL No</th>
                                <th scope="col">Contract Name</th>
                                <th scope="col">Transaction No</th>
                                <th scope="col">AMC?</th>
                                <th scope="col">Next Visit</th>
                                <th scope="col">Overdue</th>
                                <th scope="col">Last Completed</th>
                                <th scope="col">Pending</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php party_contracts_render_rows($inactive_contracts); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
