<?php
$visitSections = array(
    'overdue' => array('title' => 'Overdue Visits', 'badge' => 'bg-danger', 'empty' => 'No overdue visits found'),
    'today' => array('title' => "Today's Visits", 'badge' => 'bg-primary', 'empty' => 'No visits scheduled for today'),
    'yesterday' => array('title' => 'Yesterday Visits', 'badge' => 'bg-warning', 'empty' => 'No visits found for yesterday'),
    'next_7_days' => array('title' => 'Next 7 Days Visits', 'badge' => 'bg-info', 'empty' => 'No upcoming visits in the next 7 days'),
);

$section = strtolower(trim((string) ($_GET['section'] ?? 'overdue')));
if ($section == '' || !isset($visitSections[$section])) {
    $section = 'overdue';
}
$sectionMeta = $visitSections[$section];

$locId = $_GET['LOCID'] ?? '';
$search = trim((string) ($_GET['search'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));

$listUrl = API . "?mode=getamcadminlandingvisitslist&method=get&admin_type=" . urlencode($_SESSION['admin_type'] ?? '')
    . "&section=" . urlencode($section)
    . "&page=" . $page
    . "&search=" . urlencode($search)
    . "&LOCID=" . urlencode($locId);
$listData = json_decode(callAPI($listUrl), true);
$listData = is_array($listData) ? $listData : array();
$rows = $listData['rows'] ?? array();
$total = (int) ($listData['total'] ?? 0);
$page = max(1, (int) ($listData['page'] ?? $page));
$limit = (int) ($listData['limit'] ?? 20);
$totalPages = (int) ($listData['total_pages'] ?? 0);

function amcVisitListPageUrl($section, $locId, $page, $search)
{
    $params = array(
        'option' => 'COM_LIST_AMC_SCHEDULE_VISITS',
        'section' => $section,
        'page' => max(1, (int) $page),
    );
    if ($search != '') {
        $params['search'] = $search;
    }
    if ($locId != '') {
        $params['LOCID'] = $locId;
    }
    return 'index.php?' . http_build_query($params);
}

function amcVisitListShowingText($total, $page, $limit)
{
    if ($total == 0) {
        return 'Showing 0 of 0';
    }
    $page = max(1, (int) $page);
    $limit = max(1, (int) $limit);
    $from = (($page - 1) * $limit) + 1;
    $to = min($page * $limit, $total);
    return 'Showing ' . $from . '–' . $to . ' of ' . $total;
}

function amcVisitListRenderActionIcons($row, $section)
{
    $contractId = (int) ($row['contract_id'] ?? 0);
    $scheduleUrl = 'index.php?option=COM_VIEW_CONTRACT_AMC_SCHEDULES&contract_id=' . $contractId;
    $assignUrl = 'index.php?option=COM_ASSIGN_CONTRACT_AMC_TECHNICIANS&contract_id=' . $contractId;
    $action = $row['action'] ?? 'view';
    ?>
    <a href="<?php echo $scheduleUrl; ?>" title="View Schedules" class="m-r-10"><i class="ti-eye text-info f-16"></i></a>
    <?php if ($section == 'today' || $section == 'next_7_days') { ?>
        <?php if ($action == 'assign') { ?>
<a href="<?php echo $assignUrl; ?>" title="Assign" class="m-r-10"><i class="ti-user text-primary f-16"></i></a>
<?php } ?>
        <?php if ($section == 'today' && $action == 'start') { ?>
        <a href="<?php echo $scheduleUrl; ?>" title="Start" class="m-r-10"><i class="ti-control-play text-warning f-16"></i></a>
        <?php } ?>
        <?php if ($section == 'today' && $action == 'finish') { ?>
        <a href="<?php echo $scheduleUrl; ?>" title="Finish" class="m-r-10"><i class="ti-check text-success f-16"></i></a>
        <?php } ?>
    <?php } ?>
    <?php
}

function amcVisitListRenderRows($rows, $page, $limit, $section, $emptyMessage)
{
    if (count((array)$rows) == 0) {
        echo '<tr><td colspan="7" class="text-center text-muted">' . htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8') . '</td></tr>';
        return;
    }
    $serial = (($page - 1) * $limit) + 1;
    foreach ($rows as $row) {
        $row = is_array($row) ? $row : (array) $row;
        $statusLabel = htmlspecialchars($row['status_label'] ?? '-', ENT_QUOTES, 'UTF-8');
        $statusClass = htmlspecialchars($row['status_class'] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8');
        ?>
        <tr>
            <td><?php echo $serial; ?></td>
            <td><?php echo htmlspecialchars($row['contract_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['party_label'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['asset_label'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo !empty($row['planned_date']) ? fromDate($row['planned_date']) : '-'; ?></td>
            <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
            <td class="text-nowrap"><?php amcVisitListRenderActionIcons($row, $section); ?></td>
        </tr>
        <?php
        $serial++;
    }
}

function amcVisitListRenderPagination($section, $locId, $page, $totalPages, $search)
{
    $page = max(1, (int) $page);
    $totalPages = (int) $totalPages;
    if ($totalPages <= 1) {
        return;
    }
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    ?>
    <nav class="mt-2" aria-label="Visit list pagination">
        <ul class="pagination pagination-sm mb-0 justify-content-end flex-wrap">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo htmlspecialchars(amcVisitListPageUrl($section, $locId, max(1, $page - 1), $search), ENT_QUOTES, 'UTF-8'); ?>">Prev</a>
            </li>
            <?php if ($start > 1) { ?>
            <li class="page-item"><a class="page-link" href="<?php echo htmlspecialchars(amcVisitListPageUrl($section, $locId, 1, $search), ENT_QUOTES, 'UTF-8'); ?>">1</a></li>
            <?php if ($start > 2) { ?>
            <li class="page-item disabled"><span class="page-link">…</span></li>
            <?php } ?>
            <?php } ?>
            <?php for ($p = $start; $p <= $end; $p++) { ?>
            <li class="page-item <?php echo ($p == $page) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo htmlspecialchars(amcVisitListPageUrl($section, $locId, $p, $search), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $p; ?></a>
            </li>
            <?php } ?>
            <?php if ($end < $totalPages) { ?>
            <?php if ($end < $totalPages - 1) { ?>
            <li class="page-item disabled"><span class="page-link">…</span></li>
            <?php } ?>
            <li class="page-item"><a class="page-link" href="<?php echo htmlspecialchars(amcVisitListPageUrl($section, $locId, $totalPages, $search), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $totalPages; ?></a></li>
            <?php } ?>
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo htmlspecialchars(amcVisitListPageUrl($section, $locId, min($totalPages, $page + 1), $search), ENT_QUOTES, 'UTF-8'); ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php
}
?>
<div class="page-body amc-visit-list-page">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0"><?php echo htmlspecialchars($sectionMeta['title'], ENT_QUOTES, 'UTF-8'); ?>
                    <span class="badge <?php echo htmlspecialchars($sectionMeta['badge'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo $total; ?></span>
                </h5>
                <small class="text-muted"><?php echo htmlspecialchars(amcVisitListShowingText($total, $page, $limit), ENT_QUOTES, 'UTF-8'); ?></small>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="get" class="d-flex flex-wrap align-items-center gap-2 mb-0">
                    <input type="hidden" name="option" value="COM_LIST_AMC_SCHEDULE_VISITS">
                    <input type="hidden" name="section" value="<?php echo htmlspecialchars($section, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if ($locId != '') { ?>
                    <input type="hidden" name="LOCID" value="<?php echo htmlspecialchars($locId, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php } ?>
                    <input aria-label="Search" type="text" name="search" class="form-control form-control-sm" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search contract, vendor/dept, asset, planned date..." style="min-width: 280px;">
                    <button type="submit" class="btn btn-sm btn-primary">Search</button>
                </form>
                <button type="button" class="btn btn-sm"><i class="icofont icofont-reply"></i> Back</button>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm f-12 mb-0">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Contract</th>
                        <th scope="col">Vendor/Dept</th>
                        <th scope="col">Asset</th>
                        <th scope="col">Planned Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php amcVisitListRenderRows($rows, $page, $limit, $section, $sectionMeta['empty']); ?>
                </tbody>
            </table>
            <?php amcVisitListRenderPagination($section, $locId, $page, $totalPages, $search); ?>
        </div>
    </div>
</div>
