<?php
$dashUrl = API . "?mode=getdocumentmanagementdashboard&method=get"
    . "&company_id=" . urlencode($_SESSION['company_id'])
    . "&user_id=" . urlencode($_SESSION['userid']);
$dashRes  = json_decode(callAPI($dashUrl), true);
$dashRes  = is_array($dashRes) ? $dashRes : array();

$totalAll        = (int)($dashRes['total_all']        ?? 0);
$totalActive     = (int)($dashRes['total_active']     ?? 0);
$totalInprogress = (int)($dashRes['total_inprogress'] ?? 0);
$totalApproved   = (int)($dashRes['total_approved']   ?? 0);

$adminName   = isset($_SESSION['us_name'])          ? htmlspecialchars($_SESSION['us_name'])          : 'Admin';
$companyName = isset($_SESSION['config']['name'])   ? htmlspecialchars($_SESSION['config']['name'])   : '';
?>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Document Management Dashboard</h5>
            <div class="card-header-right">
                <a href="index.php?option=COM_LIST_DOCUMENT_MANAGEMENT_WORKFLOW_CONFIGURATIONS_NEW" class="btn btn-success mb-2 desktop-view">
                <i class="icofont icofont-settings"></i>
                    Workflow Configurations
                </a>
                <a href="index.php?option=COM_LIST_DOCUMENT_TYPES" class="btn btn-success mb-2 desktop-view">
                <i class="icofont icofont-list"></i>
                    Document Types            
                </a>

                <a href="index.php?option=COM_INSERT_DOCUMENT_MANAGEMENT" class="btn btn-success mb-2 desktop-view">
                <i class="icofont icofont-plus icon-white "></i>
                    Documents            
                </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body p-4">

            <!-- Admin welcome bar -->
            <div class="rounded p-3 mb-4 d-flex align-items-center justify-content-between text-white"
                 style="background: linear-gradient(135deg, #1e2a4a 0%, #2e3f6e 100%);">
                <div>
                    <p class="mb-0 f-18 f-w-600"><i class="icofont icofont-user-alt-3"></i> Welcome, <?php echo $adminName; ?></p>
                    <?php if ($companyName != '') { ?>
                        <p class="mb-0 f-13" style="opacity:0.8;"><?php echo $companyName; ?></p>
                    <?php } ?>
                </div>
                <div class="f-36" style="opacity:0.6;">
                    <i class="icofont icofont-files-stack"></i>
                </div>
            </div>

            <!-- Summary cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="kpi-card kpi-blue" style="cursor:pointer;" id="dmCardAll" onclick="loadDmTable('', this)">
                        <div class="fs-2 mb-2"><i class="icofont icofont-folder-open"></i></div>
                        <div class="kpi-number"><?php echo $totalAll; ?></div>
                        <div class="kpi-label">All Documents</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="kpi-card kpi-blue" style="cursor:pointer;" id="dmCardActive" onclick="loadDmTable('0', this)">
                        <div class="fs-2 mb-2"><i class="icofont icofont-check-circled"></i></div>
                        <div class="kpi-number"><?php echo $totalActive; ?></div>
                        <div class="kpi-label">Active</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="kpi-card kpi-orange" style="cursor:pointer;" id="dmCardInprogress" onclick="loadDmTable('inprogress', this)">
                        <div class="fs-2 mb-2"><i class="icofont icofont-clock-time"></i></div>
                        <div class="kpi-number"><?php echo $totalInprogress; ?></div>
                        <div class="kpi-label">In Progress</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="kpi-card kpi-green" style="cursor:pointer;" id="dmCardApproved" onclick="loadDmTable('199', this)">
                        <div class="fs-2 mb-2"><i class="icofont icofont-badge"></i></div>
                        <div class="kpi-number"><?php echo $totalApproved; ?></div>
                        <div class="kpi-label">Approved</div>
                    </div>
                </div>
            </div>

            <!-- Table content loaded by ajax -->
            <div id="dmTableContent" class="custom-scroll" style="max-height: 500px;">
                <div class="text-center py-5">
                    <img src="assets/images/spiner/spiner3.gif" style="width:200px;">
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    function loadDmTable(status, el) {
        var loader = '<div class="text-center py-5"><img src="assets/images/spiner/spiner3.gif" style="width:200px;"></div>';
        $('#dmTableContent').html(loader);
        $.ajax({
            url: 'views/custom_dashboard_document_management_ajax.php',
            type: 'POST',
            data: {
                status_filter: status,
                company_id: '<?php echo addslashes($_SESSION['company_id']); ?>',
                user_id: '<?php echo addslashes($_SESSION['userid']); ?>'
            },
            success: function (response) {
                $('#dmTableContent').html(response);
            },
            error: function () {
                $('#dmTableContent').html('<p class="text-danger p-3">Unable to load data. Please try again.</p>');
            }
        });
    }

    $(document).ready(function () {
        loadDmTable('', document.getElementById('dmCardAll'));
    });
</script>
