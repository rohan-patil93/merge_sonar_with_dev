<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_POST['getlistpage'] == "Y") {
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}
$from_date = $_POST['from_date'] != "" ? $_POST['from_date'] : date("Y-m-01");
$to_date = $_POST['to_date'] != "" ? $_POST['to_date'] : date("Y-m-t");
$transaction_table = $_POST['transaction_table'];
$menu = API . "?mode=getcrmleads&list=Y&transaction_table_list=" . $transaction_table . "&from_date=" . $from_date . "&to_date=" . $to_date . "&admin_type=" . $_SESSION['admin_type'];
$page_list = callAPI($menu);
$result = json_decode($page_list, 1);
$crm_leads = $result['crm_leads'];
$crm_histories = $result['crm_histories'];
foreach ($crm_histories as $crmhistry) {
    $lastactivity[$crmhistry['transaction_table']][$crmhistry['transaction_id']] = $crmhistry;
}
$crm_general_codes = $result['crm_general_codes'];
$crm_source_types = array();
foreach ($crm_general_codes as $key => $crmsource1) {
    foreach ($crmsource1 as $crmsource) {
        $crm_source_types[$key][$crmsource['value']] = $crmsource['label'];
    }
}
$project_task_status = $result['project_task_status'];

//Start graph crm leads
$crm_leads_graph = $result['crm_leads_graph'];
foreach ($crm_leads_graph as $crmstat) {
    $crm_leads_chart[] = array($project_task_status['crm_leads'][$crmstat['crm_status']]['name'], (int)$crmstat['cnt']);
}
$crm_feasibilities = $result['crm_feasibilities'];

$feasibility_open = [];
$feasibility_closed = [];
$so_not_generated = [];
$so_generated = [];

foreach ($crm_feasibilities as $item) {
    $status = $item['current_status'];
    $so_id = $item['sales_order_id'];

    if ($status == '0') {
        $feasibility_open[] = $item;
    } elseif ($status == '199') {
        $feasibility_closed[] = $item;

        if (empty($so_id)) {
            $so_not_generated[] = $item;
        } else {
            $so_generated[] = $item;
        }
    }
}

//End graph crm leads
?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5>Feasibilities <small class="f-12 text-info">(<?php echo fromDate($from_date) . " To " . fromDate($to_date); ?>)</small></h5>

            </div>


            <div class="card-body custom-scroll" style="min-height: 1000px; max-height: 1000px;">
                <ul class="nav md-tabs infisuite_tab mb-3" id="feasibilityTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="open-tab" data-bs-toggle="tab" href="#open" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Feasibility Open
                            <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$feasibility_open); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="closed-tab" data-bs-toggle="tab" href="#closed" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Feasibility Closed
                            <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$feasibility_closed); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="so-not-gen-tab" data-bs-toggle="tab" href="#so-not-gen" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">SO Not Generated
                            <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$so_not_generated); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="so-gen-tab" data-bs-toggle="tab" href="#so-gen" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">SO Generated
                            <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$so_generated); ?></span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="feasibilityTabsContent">
                    <?php
                    $tabs = [
                        'open' => ['title' => 'Feasibility Open', 'data' => $feasibility_open],
                        'closed' => ['title' => 'Feasibility Closed', 'data' => $feasibility_closed],
                        'so-not-gen' => ['title' => 'SO Not Generated', 'data' => $so_not_generated],
                        'so-gen' => ['title' => 'SO Generated', 'data' => $so_generated],
                    ];

                    foreach ($tabs as $id => $tab):
                    ?>
                        <div class="tab-pane fade <?= $id === 'open' ? 'show active' : '' ?>" id="<?= $id ?>" role="tabpanel">
                            <?php if (!empty($tab['data'])): ?>
                                <table class="table  table-sm">
                                    <thead>
                                        <tr>
                                            <th scope="col">Feasibility Title</th>
                                            <th scope="col">Contact</th>
                                            <th scope="col">Last Action</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tab['data'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT',<?php echo $item['lead_id']; ?>)"><h6> <?php echo $item['display_name'].'-'.$item['feasibility_type']; ?></h6></button>
                                                </td>
                                                <td><?= htmlspecialchars($item['phone'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($item['updated_date']) ?></td>
                                                <td><?= $item['current_status'] == 0 ? 'OPEN' : 'CLOSED' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center">
                                    <p class="text-muted f-16 fw-bold m-b-30">
                                        <i class="fa fa-warning"></i> No Records Found
                                    </p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Status</h5>
                <div class="card-header-right"></div>
            </div>
            <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
                <input type="hidden" name="chart_data_lead_chart" id="chart_data_lead_chart2" value='<?php echo json_encode((array)$crm_leads_chart); ?>'>
                <?php if (count((array)$crm_leads_chart) == '0') { ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } else { ?>
                    <div id="chart_by_leads2" style="width: 100%; height: 400px;"></div>
                <?php } ?>
            </div>
        </div>
        <ul class="nav md-tabs infisuite_tab mt-2 mb-2">
            <?php $statseq = 1;
            foreach ($crm_leads_graph as $allstatuswithlist) { ?>
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12 <?php echo $statseq == 1 ? 'active' : ''; ?>" data-bs-toggle="tab" href="#statuswithlist_<?php echo $statseq; ?>" role="tab" aria-selected="true"><?php echo $project_task_status['crm_leads'][$allstatuswithlist['crm_status']]['name']; ?><span class="form-label label label-xs label-success m-l-10"><?php echo $allstatuswithlist['cnt']; ?></span></a>
                </li>
            <?php $statseq++;
            } ?>
        </ul>
        <div class="tab-content tabs ">
            <?php $statseq = 1;
            foreach ($crm_leads_graph as $allstatuswithlist) { ?>
                <div class="tab-pane card-body <?php echo $statseq == 1 ? 'active' : ''; ?> p-0" id="statuswithlist_<?php echo $statseq; ?>" role="tabpanel">
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5><?php echo $project_task_status['crm_leads'][$allstatuswithlist['crm_status']]['name']; ?>
                                <span class="form-label label label-xs label-success m-l-10" style="color:#FFFFFF; background-color:#ffe066;"><?php echo $allstatuswithlist['cnt']; ?></span>
                            </h5>
                        </div>
                        <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
                            <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $allreocrdconverttoarray = explode(",", $allstatuswithlist['group_values']);
                                    foreach ($allreocrdconverttoarray as $crmleadsval) {
                                        $reocrdarray = explode("|", $crmleadsval);
                                    ?>
                                        <tr>
                                            <td class="p-2">
                                                <div class="align-middle">
                                                    <?php if ($reocrdarray['3']) { ?>
                                                        <img src="uploads/all/<?php echo $reocrdarray['3']; ?>" class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                                    <?php } else { ?>
                                                        <span alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($reocrdarray['1']); ?></span>
                                                    <?php } ?>
                                                    <div class="d-inline-block width73">
                                                        <a href="#!" class="leadsidebar text-primary" onclick="leadPageCall('LEAD','<?php echo $reocrdarray['0']; ?>')">
                                                            <h6><?php echo $reocrdarray['1']; ?></h6>
                                                        </a>
                                                        <p class="text-muted m-b-0"><?php echo $reocrdarray['2'] != "" ? $reocrdarray['2'] : "-"; ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php $statseq++;
            } ?>
        </div>
    </div>
</div>
<!-- jquery CRM js -->
<script type="text/javascript" src="assets/js/lead_script.js"></script>
<!-- jquery CRM js -->
<script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js" integrity="sha384-Q4nTc23a1YNtnl17XDjJkYn/j5Ksb7rsGG1NTcIxbz6sTGfGXZJ8WdvzALeeuafr" crossorigin="anonymous"></script>
<script type="text/javascript" src="assets/js/custom_view_crm_dashboards.js"></script>
