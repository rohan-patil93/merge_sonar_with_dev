<?php
$_SESSION['apply_js']['multiselest'] = '1';

$curl = curl_init();

$postData = array('data_name' => 'infistaqin_infisuite_2_ticketing', 'email' => $_SESSION['email'], 'module_id' => '1', 'module_group_id' => '', 'ipaddress' => '::1', 'page_option' => '', 'role_ids' => '1,2,3', 'notdisplaycurrent_status' => '2,0,40', 'admin_type' => '1', 'client_id' => '', 'priority' => '', 'current_status' => '', 'filterfromdate' => '', 'filtertodate' => '');

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://dev.infistaq.in/ticketing/api.php?method=post&mode=getemployeetickets&data_name=infistaqin_infisuite_2_ticketing&email=anthony_johnston@live.infistaq.com',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $postData,
));

$response = curl_exec($curl);

$response = json_decode($response, true);

$ticketHistories = $response['tickets'];
$statusList = $response['table_statusList'];
$notifications = $response['notifications'] ?? [];
$totalNotifications = count($notifications);
$stausresultwithcolor = ViewStatusData();
$priorityCounts = ['1' => 0, '2' => 0, '3' => 0];
foreach ($ticketHistories as $ticket) {
    if (isset($priorityCounts[$ticket['priority']])) {
        $priorityCounts[$ticket['priority']]++;
    }
}
$statuses = [];
foreach ($statusList as $statusItem) {
    $id = $statusItem['status'];
    $statuses[$id] = [
        'id' => $id,
        'name' => $statusItem['name'],
        'action_label' => $statusItem['action_label'],
        'background_color' => $stausresultwithcolor[$id]['background_color'] ?? '#999',
        'color' => $stausresultwithcolor[$id]['color'] ?? '#fff'
    ];
}
$statusCounts = [];
foreach ($ticketHistories as $ticket) {
    $sid = $ticket['current_status'];
    if (!isset($statusCounts[$sid])) {
        $statusCounts[$sid] = 0;
    }
    $statusCounts[$sid]++;
}

$nonEmptyStatuses = [];
foreach ($statuses as $sid => $status) {
    $count = $statusCounts[$sid] ?? 0;
    $nonEmptyStatuses[$sid] = $status + ['count' => $count];
}
$totalCards = count($nonEmptyStatuses);
$colClass = $totalCards > 0 ? intval(12 / min($totalCards, 6)) : 12;

function formatPriority($priority)
{
    switch ($priority) {
        case '1':
            return '<span class="px-2 py-1 badge bg-success">Low</span>';
        case '2':
            return '<span class="px-2 py-1 badge bg-warning">Medium</span>';
        case '3':
            return '<span class="px-2 py-1 badge bg-danger">High</span>';
        default:
            return '<span class="px-2 py-1 badge bg-secondary">Unknown</span>';
    }
}
function formatStatus($status)
{
    switch ($status) {
        case '0':
            return '<span class="badge bg-success">Active</span>';
        case '1':
            return '<span class="badge bg-failed">In Active</span>';
        default:
            return '<span class="badge bg-secondary">Status ' . $status . '</span>';
    }
}
function hasRecentNotification($ticketId, $notifications)
{
    if (empty($notifications))
        { return false;
}

    foreach ($notifications as $n) {
        $details = json_decode($n['details'], true);
        if (!empty($details['notification_message']) && strpos($details['notification_message'], $ticketId) !== false) {
            $notifTime = strtotime($details['notification_datetime']);
            if ($notifTime >= strtotime("-10 minutes")) {
                return true;
            }
        }
    }
    return false;
}
$totaltickets = $opentickets = $inprogress = $fixedtickets = $closetickets = $draftickets = $activetickets = 0;
$inprgresary = [ST_DRAFT, ST_ACTIVE, ST_APPROVED, ST_HOLD, ST_CANCEL, ST_APPROVAL_PENDING, ST_COMPLETE];
if (in_array(TICKET_ASSIGNER_ROLE, (array) $_SESSION['role_ids']) || in_array(1, (array) $_SESSION['role_ids'])) {
    $inprgresary = [ST_COMPLETE, ST_APPROVAL_PENDING, ST_APPROVED, ST_HOLD, ST_CANCEL];
} elseif (
    in_array(TICKET_FIXER_ROLE, (array) $_SESSION['role_ids'])
    || in_array(TICKET_TESTER_ROLE, (array) $_SESSION['role_ids'])
    || in_array(TICKET_FOLLOWER_ROLE, (array) $_SESSION['role_ids'])
) {
    $inprgresary = [ST_APPROVED1, ST_APPROVED3];
}
foreach ($ticketHistories as $ticket) {
    $status = $ticket['current_status'];
    $totaltickets++;
    if ($status == ST_APPROVAL_PENDING && !in_array(TICKET_USER_ROLE, (array) $_SESSION['role_ids'])) {
        $opentickets++;
    } elseif ($status == ST_COMPLETE) {
        $fixedtickets++;
    } elseif ($status == ST_APPROVED) {
        $closetickets++;
    } elseif ($status == ST_DRAFT) {
        $draftickets++;
    } elseif ($status == ST_ACTIVE) {
        $activetickets++;
    } elseif (!in_array($status, $inprgresary)) {
        $inprogress++;
    }
}
?>
<div class="container-fluid mt-4">
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                    <h6 class="text-uppercase fw-semibold text-primary">Open Tickets</h6>
                    <h2 class="fw-bold text-primary my-2"><?= $opentickets; ?></h2>
                    <div class="progress rounded-pill" style="height:6px;">
                        <div class="progress-bar bg-primary" role="progressbar"
                            style="width:<?= $totaltickets > 0 ? round(($opentickets / $totaltickets) * 100, 2) : 0; ?>%">
                        </div>
                    </div>
                    <span class="badge mt-3 rounded-pill bg-primary-subtle text-primary px-3 py-2">
                        <?= $totaltickets > 0 ? round(($opentickets / $totaltickets) * 100, 2) : 0; ?>%
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                    <h6 class="text-uppercase fw-semibold text-warning ">In Progress</h6>
                    <h2 class="fw-bold text-warning my-2"><?= $inprogress; ?></h2>
                    <div class="progress rounded-pill" style="height:6px;">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width:<?= $totaltickets > 0 ? round(($inprogress / $totaltickets) * 100, 2) : 0; ?>%">
                        </div>
                    </div>
                    <span class="badge mt-3 rounded-pill bg-warning-subtle text-warning px-3 py-2">
                        <?= $totaltickets > 0 ? round(($inprogress / $totaltickets) * 100, 2) : 0; ?>%
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                    <h6 class="text-uppercase fw-semibold text-success ">Fixed Tickets</h6>
                    <h2 class="fw-bold text-success my-2"><?= $fixedtickets; ?></h2>
                    <div class="progress rounded-pill" style="height:6px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width:<?= $totaltickets > 0 ? round(($fixedtickets / $totaltickets) * 100, 2) : 0; ?>%">
                        </div>
                    </div>
                    <span class="badge mt-3 rounded-pill bg-success-subtle text-success px-3 py-2">
                        <?= $totaltickets > 0 ? round(($fixedtickets / $totaltickets) * 100, 2) : 0; ?>%
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                    <h6 class="text-uppercase fw-semibold text-danger">Closed Tickets</h6>
                    <h2 class="fw-bold text-danger my-2"><?= $closetickets; ?></h2>
                    <div class="progress rounded-pill" style="height:6px;">
                        <div class="progress-bar bg-danger" role="progressbar"
                            style="width:<?= $totaltickets > 0 ? round(($closetickets / $totaltickets) * 100, 2) : 0; ?>%">
                        </div>
                    </div>
                    <span class="badge mt-3 rounded-pill bg-danger-subtle text-danger px-3 py-2">
                        <?= $totaltickets > 0 ? round(($closetickets / $totaltickets) * 100, 2) : 0; ?>%
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mt-3">
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm rounded-4 bg-success">
                <div class="card-body p-4">
                    <h6 class="fw-semibold">Low Priority</h6>
                    <div class="display-5 fw-bold"><?= $priorityCounts['1']; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm rounded-4 bg-warning">
                <div class="card-body p-4">
                    <h6 class="fw-semibold">Medium Priority</h6>
                    <div class="display-5 fw-bold"><?= $priorityCounts['2']; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm rounded-4 bg-danger">
                <div class="card-body p-4">
                    <h6 class="fw-semibold">High Priority</h6>
                    <div class="display-5 fw-bold"><?= $priorityCounts['3']; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-tabs" id="ticketTab">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="cards-tab" data-bs-toggle="tab" href="#cardsView" role="tab"
                    aria-controls="cardsView" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-grid-3x3-gap"></i> Grid View
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="table-tab" data-bs-toggle="tab" href="#tableView" role="tab"
                    aria-controls="tableView" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-table"></i> Table View
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="notifications-tab" data-bs-toggle="tab" href="#notificationsView" role="tab"
                    aria-controls="notificationsView" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-bell"></i> Notifications
                    <span class="badge bg-danger ms-1"><?= $totalNotifications ?></span>
                </a>
            </li>
        </ul>
        <div class="tab-content mt-3" id="ticketTabContent">
            <div class="tab-pane fade show active" id="cardsView" role="tabpanel" aria-labelledby="cards-tab">
                <div class="card card-custom card-sticky">
                    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Ticket Management</h5>
                        <div class="card-header-right">
                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                onclick="openTicketModal()">
                                <i class="icofont icofont-plus mr-1"></i> Add Ticket
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select aria-label="Grid Status Filter" id="gridStatusFilter" class="form-select js-example-basic-single" onchange="filterGrid()">
                                    <option value="">All Status</option>
                                    <?php foreach ($nonEmptyStatuses as $sid => $s): ?>
                                        <option value="<?= $sid ?>"><?= htmlspecialchars($s['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select aria-label="Grid Priority Filter" id="gridPriorityFilter" class="form-select js-example-basic-single" onchange="filterGrid()">
                                    <option value="">All Priorities</option>
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input aria-label="Search tickets..." type="search" id="gridSearch" class="form-control"
                                    placeholder="Search tickets...">
                            </div>
                        </div>

                        <div class="row">
                            <?php foreach ($nonEmptyStatuses as $sid => $status): ?>
                                <div class="col-md-4 col-lg-3 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-header d-flex justify-content-between align-items-center fs-20 p-3"
                                            style="background-color: <?= $status['background_color']; ?>; color: <?= $status['color']; ?>;">
                                            <span><?= htmlspecialchars($status['name']); ?></span>
                                            <span class="badge rounded-circle bg-light text-dark"
                                                style="width: 32px; height: 32px; line-height: 22px; font-size: 0.9rem;">
                                                <?= $status['count'] ?>
                                            </span>
                                        </div>
                                        <div class="card-body p-2" style="max-height:70vh; overflow-y:auto;">
                                            <?php foreach ($ticketHistories as $ticket):
                                                $ticketId = $ticket['id'];
                                                $notification_id = isset($response['notifications'][$ticketId]['not_id']) ? deocdeId($response['notifications'][$ticketId]['not_id']) : '';
                                                $url = "index.php?option=COM_VIEW_TICKETS&Rid=" . deocdeId("id:" . $ticketId) . "&noti_id=" . $notification_id;
                                                ?>
                                                <?php if ($ticket['current_status'] == $sid): ?>
                                                    <div class="card mb-3 shadow-sm border position-relative">
                                                        <div class="card-body p-2">
                                                            <h6 class="mb-1 text-truncate">
                                                                <?= htmlspecialchars($ticket['task_name']); ?>
                                                            </h6>
                                                            <p class="mb-1 small text-muted"><i class="ti-hash"></i>
                                                                <?= htmlspecialchars($ticket['ticket_id']); ?></p>
                                                            <p class="mb-1 small">
                                                                <i class="ti-calendar"></i> Req:
                                                                <?= date('M d, Y', strtotime($ticket['request_date'])); ?><br>
                                                                <i class="ti-clock-history"></i> Upd:
                                                                <?= $ticket['updated_date'] ? date('M d, Y', strtotime($ticket['updated_date'])) : 'N/A'; ?>
                                                            </p>
                                                            <div class="d-flex gap-1">
                                                                <div class="mb-1"><?= formatPriority($ticket['priority']); ?></div>
                                                                <?php if (hasRecentNotification($ticket['ticket_id'], $notifications)): ?>
                                                                    <span class="badge bg-danger p-2 rounded-circle blink-bell">
                                                                        <i class="ti-bell text-white"></i>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="position-absolute top-0 end-0 m-1 dropdown">
                                                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i
                                                                    class="ti-settings"></i></button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <?php
                                                                $ticketId = $ticket['id'];
                                                                $currentStatus = $ticket['current_status'];
                                                                $transactionLogId = $response['transaction_approval_logs'][$ticketId] ?? "";
                                                                if (!empty($response['status_progress_actions'][$currentStatus])) {
                                                                    foreach ($response['status_progress_actions'][$currentStatus] as $nextstatus) {
                                                                        $sendpost = [
                                                                            'current_status' => $currentStatus,
                                                                            'next_status' => $nextstatus['status'],
                                                                            'rid' => $ticketId,
                                                                            'ticket_id' => $ticket['ticket_id'],
                                                                            'holding_type' => 'Y'
                                                                        ]; ?>
                                                                         <li>
                                                                             <a class="dropdown-item" tabindex="0" role="button" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"
                                                                                 onclick="dynamicStatusUpdate('<?= urlencode(json_encode($sendpost)); ?>','','<?= $transactionLogId; ?>','Y','<?= $sendpost['next_status']; ?>')">
                                                                                 <?= htmlspecialchars($statuses[$nextstatus['status']]['action_label'] ?? 'Action'); ?>
                                                                             </a>
                                                                         </li>
                                                                    <?php }
                                                                } ?>
                                                                <?php if (!in_array($currentStatus, [ST_REJECTED, ST_CANCEL, ST_APPROVED])): ?>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <?php
                                                                    $holdType = ($ticket['hold_type'] === 'Y') ? 'N' : 'Y';
                                                                    $holdLabel = ($ticket['hold_type'] === 'Y') ? 'Unhold Ticket' : 'Hold Ticket';
                                                                    $nextStatus = ($ticket['hold_type'] === 'Y') ? 5 : 6;
                                                                    $sendpost = [
                                                                        'assigner_role' => $ticket['assigner_role'] ?? 0,
                                                                        'assigner_id' => $ticket['assigner_id'] ?? 0,
                                                                        'assignee_role' => $ticket['assignee_role'] ?? 0,
                                                                        'assignee_id' => $ticket['assignee_id'] ?? 0,
                                                                        'current_status' => $currentStatus,
                                                                        'next_status' => $nextStatus,
                                                                        'rid' => $ticketId,
                                                                        'ticket_id' => $ticket['ticket_id'],
                                                                        'holding_type' => $holdType
                                                                    ]; ?>
                                                                    <li>
                                                                         <a class="dropdown-item" tabindex="0" role="button" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"
                                                                             onclick="dynamicStatusUpdate('<?= urlencode(json_encode($sendpost)); ?>','','<?= $transactionLogId; ?>','<?= $holdType; ?>','<?= $sendpost['next_status']; ?>')">
                                                                             <?= $holdLabel; ?>
                                                                         </a>
                                                                     </li>
                                                                <?php endif; ?>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li><a class="dropdown-item" href="#" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"
                                                                        onclick="editTicket('<?= $ticketId ?>')">Edit</a></li>
                                                                <li><a class="dropdown-item" href="<?= $url ?>"
                                                                        target="_blank">View</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tableView" role="tabpanel" aria-labelledby="table-tab">
                <div class="card card-custom card-sticky">
                    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Ticket Management</h5>
                        <div class="card-header-right">
                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                onclick="openTicketModal()">
                                <i class="icofont icofont-plus mr-1"></i> Add Ticket
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select aria-label="Table Status Filter" id="tableStatusFilter" class="form-select js-example-basic-single" onchange="filterTable()">
                                    <option value="">All Status</option>
                                    <?php foreach ($nonEmptyStatuses as $sid => $s): ?>
                                        <option value="<?= $sid ?>"><?= htmlspecialchars($s['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select aria-label="Table Priority Filter" id="tablePriorityFilter" class="form-select js-example-basic-single" onchange="filterTable()">
                                    <option value="">All Priorities</option>
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input aria-label="Search tickets..." type="search" id="tableSearch" class="form-control"
                                    placeholder="Search tickets...">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="ticketHistoryTable">
                                <thead>
                                    <tr>
                                        <th scope="col">Ticket No.</th>
                                        <th scope="col">Ticket Subject</th>
                                        <th scope="col">Requested Date</th>
                                        <th scope="col">Last Updated</th>
                                        <th scope="col">Priority</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($ticketHistories)): ?>
                                        <?php foreach ($ticketHistories as $ticket):
                                            $ticketId = $ticket['id'];
                                            $notification_id = isset($response['notifications'][$ticketId]['not_id']) ? deocdeId($response['notifications'][$ticketId]['not_id']) : '';
                                            $url = "index.php?option=COM_VIEW_TICKETS&Rid=" . deocdeId("id:" . $ticketId) . "&noti_id=" . $notification_id;
                                            ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($ticket['ticket_id']) ?></strong></td>
                                                <td>
                                                    <?= htmlspecialchars($ticket['task_name']) ?>
                                                    <?php if (hasRecentNotification($ticket['ticket_id'], $notifications)): ?>
                                                        <span class="badge bg-danger top-0 start-0 p-2 rounded-circle blink-bell">
                                                            <i class="ti-bell text-white"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('Y-m-d H:i', strtotime($ticket['request_date'])) ?></td>
                                                <td><?= $ticket['updated_date'] ? date('Y-m-d H:i', strtotime($ticket['updated_date'])) : 'N/A' ?>
                                                </td>
                                                <td><?= formatPriority($ticket['priority']) ?></td>
                                                <td>
                                                    <?php
                                                    $currentStatusId = $ticket['current_status'];
                                                    if (isset($statuses[$currentStatusId])) {
                                                        $currentStatus = $statuses[$currentStatusId]; ?>
                                                        <span class="form-label label label-md"
                                                            style="background-color: <?= $currentStatus['background_color']; ?>; color: <?= $currentStatus['color']; ?>;">
                                                            <?= htmlspecialchars($currentStatus['name']); ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-secondary">Unknown (<?= $currentStatusId ?>)</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                                            data-bs-toggle="dropdown">
                                                            <i class="ti-settings"></i> Actions
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <?php
                                                            $ticketId = $ticket['id'];
                                                            $currentStatus = $ticket['current_status'];
                                                            $transactionLogId = $response['transaction_approval_logs'][$ticketId] ?? "";
                                                            if (!empty($response['status_progress_actions'][$currentStatus])) {
                                                                foreach ($response['status_progress_actions'][$currentStatus] as $nextstatus) {
                                                                    $sendpost = [
                                                                        'current_status' => $currentStatus,
                                                                        'next_status' => $nextstatus['status'],
                                                                        'rid' => $ticketId,
                                                                        'ticket_id' => $ticket['ticket_id'],
                                                                        'holding_type' => 'Y'
                                                                    ]; ?>
                                                                    <a class="dropdown-item" tabindex="0" role="button" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"
                                                                        onclick="dynamicStatusUpdate('<?= urlencode(json_encode($sendpost)); ?>','','<?= $transactionLogId; ?>','Y','<?= $sendpost['next_status']; ?>')">
                                                                        <?= htmlspecialchars($statuses[$nextstatus['status']]['action_label'] ?? 'Action'); ?>
                                                                    </a>
                                                                <?php }
                                                            } ?>
                                                            <?php if (!in_array($currentStatus, [ST_REJECTED, ST_CANCEL, ST_APPROVED])): ?>
                                                                <div class="dropdown-divider"></div>
                                                                <?php
                                                                $holdType = ($ticket['hold_type'] === 'Y') ? 'N' : 'Y';
                                                                $holdLabel = ($ticket['hold_type'] === 'Y') ? 'Unhold Ticket' : 'Hold Ticket';
                                                                $nextStatus = ($ticket['hold_type'] === 'Y') ? 5 : 6;
                                                                $sendpost = [
                                                                    'assigner_role' => $ticket['assigner_role'] ?? 0,
                                                                    'assigner_id' => $ticket['assigner_id'] ?? 0,
                                                                    'assignee_role' => $ticket['assignee_role'] ?? 0,
                                                                    'assignee_id' => $ticket['assignee_id'] ?? 0,
                                                                    'current_status' => $currentStatus,
                                                                    'next_status' => $nextStatus,
                                                                    'rid' => $ticketId,
                                                                    'ticket_id' => $ticket['ticket_id'],
                                                                    'holding_type' => $holdType
                                                                ]; ?>
                                                                <a class="dropdown-item" tabindex="0" role="button" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"
                                                                    onclick="dynamicStatusUpdate('<?= urlencode(json_encode($sendpost)); ?>','','<?= $transactionLogId; ?>','<?= $holdType; ?>','<?= $sendpost['next_status']; ?>')">
                                                                    <?= $holdLabel; ?>
                                                                </a>
                                                            <?php endif; ?>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" href="#" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"
                                                                onclick="editTicket('<?= $ticketId ?>')">Edit</a>
                                                            <a class="dropdown-item" href="<?= $url ?>" target="_blank">View</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="alert alert-info mb-0">
                                                    <i class="icofont icofont-info-circle"></i>
                                                    No ticket histories found. Click "Add Ticket" to create your first
                                                    ticket.
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="notificationsView" role="tabpanel" aria-labelledby="notifications-tab">
                <div class="card border-0 shadow-lg rounded-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center bg-white rounded-top-4 px-4 py-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="ti-bell-fill text-warning me-2"></i> Notifications
                        </h5>
                        <span class="badge bg-danger rounded-pill px-3 py-2 fs-6">
                            <?= $totalNotifications ?>
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <?php if (!empty($notifications)): ?>
                            <div class="row g-4">
                                <?php foreach ($notifications as $n):
                                    $details = json_decode($n['details'], true);
                                    $ticketId = $details['id'] ?? '';
                                    $taskName = $n['task_name'] ?? 'Ticket Update';
                                    $priority = $n['priority'] ?? '';
                                    $requestDate = $n['request_date'] ?? '';
                                    $notificationDate = $details['notification_datetime'] ?? '';
                                    $description = $n['description'] ?? '';
                                    $statusText = $n['message'];

                                    ?>
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card border-0 shadow-md rounded-4 h-100">
                                            <div class="card-body d-flex flex-column p-4">

                                                <!-- Title + Date -->
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-semibold text-primary mb-0 text-truncate">
                                                        <i class="ti-ticket-perforated-fill me-2 text-secondary"></i>
                                                        <?= htmlspecialchars($taskName) ?>
                                                    </h6>
                                                    <span class="badge bg-light text-dark border small">
                                                        <i class="ti-clock me-1"></i>
                                                        <?= $notificationDate ? date('M d, Y H:i', strtotime($notificationDate)) : 'N/A' ?>
                                                    </span>
                                                </div>

                                                <!-- Status -->
                                                <p class="mb-1 badge bg-success p-2">
                                                    <i class="ti-user me-1"></i>
                                                    <?= htmlspecialchars($statusText) ?>
                                                </p>

                                                <!-- Priority + Date -->
                                                <div class="pt-2 border-top mt-auto">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <?= $priority ? formatPriority($priority) : '' ?>
                                                        <small class="text-secondary">
                                                            <i class="ti-calendar-event me-1"></i>
                                                            <strong>Request:</strong>
                                                            <?= $requestDate ? date('M d, Y', strtotime($requestDate)) : 'N/A' ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0 text-center p-4 rounded-4">
                                <i class="ti-info-circle"></i> No notifications found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="kt_modal_insert_ticket" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="insertTicketContent"></div>
    </div>
</div>
<script>
    function applyFilter({ statusId, priorityId, searchId, container, selector, getTicketId }) {
        const statusVal = document.getElementById(statusId).value;
        const priorityVal = document.getElementById(priorityId).value;
        const searchVal = document.getElementById(searchId).value.toLowerCase();
        document.querySelectorAll(container + " " + selector).forEach(el => {
            const ticketId = getTicketId(el);
            const ticketData = ticketsData.find(t => t.ticket_id === ticketId);
            if (!ticketData) return;
            let show = true;
            if (statusVal && ticketData.current_status != statusVal) show = false;
            if (priorityVal && ticketData.priority != priorityVal) show = false;
            const combinedText = (ticketData.task_name + " " + ticketData.ticket_id).toLowerCase();
            if (searchVal && !combinedText.includes(searchVal)) show = false;
            el.style.display = show ? "" : "none";
        });
    }
    function filterGrid() {
        applyFilter({
            statusId: "gridStatusFilter",
            priorityId: "gridPriorityFilter",
            searchId: "gridSearch",
            container: "#cardsView",
            selector: ".card.mb-3.shadow-sm.border.position-relative",
            getTicketId: (card) => {
                const el = card.querySelector(".ti-hash");
                return el ? el.parentNode.textContent.trim().replace("#", "") : null;
            }
        });
    }
    function filterTable() {
        applyFilter({
            statusId: "tableStatusFilter",
            priorityId: "tablePriorityFilter",
            searchId: "tableSearch",
            container: "#ticketHistoryTable tbody",
            selector: "tr",
            getTicketId: (row) => row.querySelector("td:first-child").innerText.trim()
        });
    }

    const ticketsData = <?= json_encode($ticketHistories); ?>;
    function editTicket(ticketId) {
        const ticket = ticketsData.find(t => t.id === ticketId);
        if (!ticket) {
            alert("Ticket data not found.");
            return;
        }
        $('#popuprid').val(ticket.id);
        $('#popupticket_id').val(ticket.ticket_id);
        $('#popupproject_id').val(ticket.project_id);
        $('#pouptask_name').val(ticket.task_name);
        $('#popupcreated_by').val(ticket.client_id);
        $('#popuppriority').val(ticket.priority);
        $('#popupdescription').val(ticket.description);
        $('.modal-title').text('Edit Ticket');
        $('#saveTicketBtn').text('Update Ticket');
        $('#kt_modal_add_ticket').modal('show');
    }
    $('#kt_modal_add_ticket').on('hidden.bs.modal', function () {
        $('#ticketForm')[0].reset();
        $('.modal-title').text('Add Ticket');
        $('#saveTicketBtn').text('Save');
        $('#popuprid').val('');
    });
    function openTicketModal() {
        $('#kt_modal_add_ticket').modal('show');
        $('.js-example-basic-single').select2({
            dropdownParent: $('#kt_modal_add_ticket')
        });
    }
    document.getElementById('ticketForm').addEventListener('submit', function (e) {
        var projectId = document.getElementById('popupproject_id').value;
        var ticketSubject = document.getElementById('pouptask_name').value;
        if (!ticketSubject.trim()) {
            e.preventDefault();
            alert('Please enter ticket subject');
            return false;
        }
        document.getElementById('saveTicketBtn').innerHTML = '<div class="loading-spinner"></div> Saving...';
        document.getElementById('saveTicketBtn').disabled = true;
    });
    function dynamicStatusUpdate(post_array, select_user_id = "", transaction_log_id = "", notilog = "", status = "") {
        console.log(select_user_id);
        var notshowpopup = ['<?php echo ST_ACTIVE; ?>', '<?php echo ST_CANCEL; ?>'];
        var table = 'project_tasks';
        $.ajax({
            url: "<?php echo SITEURL; ?>/ticketing/ajax.php",
            type: "POST",
            data: 'page=checkalreadyassigned&table=' + table + '&post_array=' + post_array + '&select_user_id=' + select_user_id + '&notilog=' + notilog + '&transaction_log_id=' + transaction_log_id,
            success: function (response) {
                console.log(response);
                var result = JSON.parse(response);
                if (result.error != "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops',
                        text: '' + result.error + ''
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    })
                } else {
                    if (jQuery.inArray(status, notshowpopup) === -1) {
                        jQuery.ajax({
                            url: "views/custom_insert_ticket_status_popup.php",
                            data: 'page=dynamicupdatestatus&table=' + table + '&post_array=' + post_array + '&select_user_id=' + select_user_id + '&notilog=' + notilog + '&transaction_log_id=' + transaction_log_id + '&status=' + status,
                            type: "POST",
                            success: function (data) {
                                $('#popupm').html(data);
                                $('#kt_modal_KTDatatable_remotem').modal("show");
                                $(".datepicker").datepicker({
                                    showOn: "both",
                                    buttonText: "<i class='ti-calendar text-dark m-r-10'></i>",
                                    dateFormat: date_format,
                                    yearRange: "0:+10",
                                    setDate: new Date(),
                                    changeMonth: true,
                                    changeYear: true
                                });
                            },
                        });
                    } else {
                        $.ajax({
                            url: "<?php echo SITEURL; ?>/ticketing/ajax.php",
                            type: "POST",
                            data: 'page=dynamicupdatestatus&table=' + table + '&post_array=' + post_array + '&select_user_id=' + select_user_id + '&notilog=' + notilog + '&transaction_log_id=' + transaction_log_id,
                            success: function (response) {
                                console.log(response);
                                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message']['3001']; ?>', '');
                                location.reload();
                            }
                        });
                    }
                }
            }
        });
    }
    function openTicketModal() {
        $.ajax({
            url: "views/custom_insert_ticket_popup.php",
            type: "POST",
            data: { ticket_id: "" },
            success: function (data) {
                $("#insertTicketContent").html(data);
                $("#kt_modal_insert_ticket").modal("show");
            }
        });
    }
    function editTicket(ticketId) {
        $.ajax({
            url: "views/custom_insert_ticket_popup.php",
            type: "POST",
            data: { ticket_id: ticketId },
            success: function (data) {
                $("#insertTicketContent").html(data);
                $("#kt_modal_insert_ticket").modal("show");
            }
        });
    }
    document.addEventListener("DOMContentLoaded", function () {
        setInterval(() => {
            document.querySelectorAll(".blink-bell").forEach(el => {
                el.classList.toggle("d-none");
            });
        }, 600);
    });
</script>
