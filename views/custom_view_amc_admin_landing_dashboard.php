<?php
$locId = $_GET['LOCID'] ?? '';

$locationFilter = array(
    'order' => 'location_name ASC',
    'where' => array('company_id' => $_SESSION['company_id'], 'is_active' => 0),
);
$locationsUrl = API . "?mode=gettabledetailswithconditions&table=locations&data=" . urlencode(json_encode($locationFilter));
$locationsResponse = json_decode(callAPI($locationsUrl), true);
$locations = is_array($locationsResponse) ? $locationsResponse : array();

$url = API . "?mode=getamcadminlandingdashboarddata&method=get&admin_type=" . $_SESSION['admin_type']
    . "&LOCID=" . urlencode($locId);
$data = json_decode(callAPI($url), true);
$data = is_array($data) ? $data : array();

$kpis = $data['kpis'] ?? array('contracts' => 0, 'active' => 0, 'expired' => 0, 'customers' => 0, 'vendors' => 0);
$yearlyChart = $data['yearly_chart'] ?? array();

$contractTotals = array(
    'customer' => (int) ($data['contracts']['customer']['total'] ?? 0),
    'vendor' => (int) ($data['contracts']['vendor']['total'] ?? 0),
    'inhouse' => (int) ($data['contracts']['inhouse']['total'] ?? 0),
);
$contracts = array(
    'customer' => $data['contracts']['customer']['rows'] ?? array(),
    'vendor' => $data['contracts']['vendor']['rows'] ?? array(),
    'inhouse' => $data['contracts']['inhouse']['rows'] ?? array(),
);

$visitTotals = array(
    'overdue' => (int) ($data['visits']['overdue']['total'] ?? 0),
    'today' => (int) ($data['visits']['today']['total'] ?? 0),
    'yesterday' => (int) ($data['visits']['yesterday']['total'] ?? 0),
    'next_7_days' => (int) ($data['visits']['next_7_days']['total'] ?? 0),
);
$visits = array(
    'overdue' => $data['visits']['overdue']['rows'] ?? array(),
    'today' => $data['visits']['today']['rows'] ?? array(),
    'yesterday' => $data['visits']['yesterday']['rows'] ?? array(),
    'next_7_days' => $data['visits']['next_7_days']['rows'] ?? array(),
);

$complaintTotal = (int) ($data['complaints']['total'] ?? 0);
$complaints = $data['complaints']['rows'] ?? array();
$repairTotal = (int) ($data['repairs']['total'] ?? 0);
$repairs = $data['repairs']['rows'] ?? array();
$contractExpiry = $data['contract_expiry'] ?? array('expiring' => array(), 'expired' => array());

$quickActions = array(
    array('label' => 'New Contract', 'icon' => 'fa fa-plus', 'class' => 'amc-qa-primary', 'mode' => 'link', 'href' => 'index.php?option=COM_INSERT_CONTRACTS'),
    array('label' => 'Contract List', 'icon' => 'fa fa-list', 'class' => 'amc-qa-info', 'mode' => 'link', 'href' => 'index.php?option=COM_LIST_CONTRACTS'),
    array('label' => 'Assign Technicians', 'icon' => 'fa fa-user', 'class' => 'amc-qa-warning amc-landing-qa-contract-popup cursor-pointer', 'mode' => 'contract_popup', 'purpose' => 'assign', 'title' => 'Assign Technicians'),
    array('label' => 'Schedules', 'icon' => 'fa fa-calendar', 'class' => 'amc-qa-success amc-landing-qa-contract-popup cursor-pointer', 'mode' => 'contract_popup', 'purpose' => 'schedules', 'title' => 'Schedules'),
    array('label' => 'Reports', 'icon' => 'fa fa-bar-chart', 'class' => 'amc-qa-reports amc-landing-qa-reports-popup cursor-pointer', 'mode' => 'reports_popup', 'title' => 'Reports'),
);

$kpiCards = array(
    array(
        'label' => 'Total Contracts',
        'value' => (int) $kpis['contracts'],
        'icon' => 'fa fa-file',
        'tone' => 'rose',
        'mode' => 'popup',
        'type' => 'total',
        'title' => 'Total Contracts',
    ),
    array(
        'label' => 'Active',
        'value' => (int) $kpis['active'],
        'icon' => 'fa fa-check-circle',
        'tone' => 'blue',
        'mode' => 'popup',
        'type' => 'active',
        'title' => 'Active',
    ),
    array(
        'label' => 'Expired',
        'value' => (int) $kpis['expired'],
        'icon' => 'fa fa-times-circle',
        'tone' => 'amber',
        'mode' => 'popup',
        'type' => 'expired',
        'title' => 'Expired',
    ),
    array(
        'label' => 'Customers',
        'value' => (int) $kpis['customers'],
        'icon' => 'fa fa-users',
        'tone' => 'teal',
        'mode' => 'link',
        'href' => 'index.php?option=COM_LIST_CUSTOMERS',
    ),
    array(
        'label' => 'Vendors',
        'value' => (int) $kpis['vendors'],
        'icon' => 'fa fa-briefcase',
        'tone' => 'indigo',
        'mode' => 'link',
        'href' => 'index.php?option=COM_LIST_VENDORS',
    ),
);

function amcLandingSliceRows($rows, $limit = 5)
{
    $rows = (array) $rows;
    if (empty($rows)) {
        return array();
    }
    return array_slice($rows, 0, $limit);
}

function amcLandingContractTypeBadge($contract)
{
    $isAmc = strtoupper($contract['is_amc'] ?? 'N') == 'Y';
    if ($isAmc) {
        return '<span class="badge bg-c-green">AMC</span>';
    }
    return '<span class="badge bg-secondary">Standard</span>';
}

function amcLandingRenderContractRows($rows, $limit = 5)
{
    $rows = amcLandingSliceRows($rows, $limit);
    if (empty($rows)) {
        echo '<tr><td colspan="8" class="text-center text-muted">No contracts found</td></tr>';
        return;
    }
    $serial = 1;
    foreach ($rows as $contract) {
        $contractId = (int) ($contract['id'] ?? 0);
        $editUrl = 'index.php?option=COM_INSERT_CONTRACTS&Rid=' . deocdeId('id:' . $contractId);
        $statusLabel = htmlspecialchars($contract['status_label'] ?? '-', ENT_QUOTES, 'UTF-8');
        $statusClass = htmlspecialchars($contract['status_class'] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8');
        $canEdit = !empty($contract['can_edit']);
        ?>
        <tr>
            <td><?php echo $serial; ?></td>
            <td><a class="text-primary" href="<?php echo $editUrl; ?>"><?php echo htmlspecialchars($contract['contract_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></a></td>
            <td><?php echo amcLandingContractTypeBadge($contract); ?></td>
            <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
            <td><?php echo !empty($contract['start_date']) ? fromDate($contract['start_date']) : '-'; ?></td>
            <td><?php echo !empty($contract['end_date']) ? fromDate($contract['end_date']) : '-'; ?></td>
            <td><?php echo htmlspecialchars($contract['transaction_no'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td class="text-nowrap text-center">
                <?php if ($canEdit) { ?>
                <a href="<?php echo $editUrl; ?>" title="Edit"><i class="ti-pencil text-primary"></i></a>
                <?php } else { ?>
                <span class="text-muted">—</span>
                <?php } ?>
            </td>
        </tr>
        <?php
        $serial++;
    }
}

function amcLandingRenderVisitRows($rows, $limit = 5, $section = '')
{
    $rows = amcLandingSliceRows($rows, $limit);
    if (empty($rows)) {
        echo '<tr><td colspan="7" class="text-center text-muted">No visits found</td></tr>';
        return;
    }
    $serial = 1;
    foreach ($rows as $row) {
        $contractId = (int) ($row['contract_id'] ?? 0);
        $scheduleUrl = 'index.php?option=COM_VIEW_CONTRACT_AMC_SCHEDULES&contract_id=' . $contractId;
        $assignUrl = 'index.php?option=COM_ASSIGN_CONTRACT_AMC_TECHNICIANS&contract_id=' . $contractId;
        $statusLabel = htmlspecialchars($row['status_label'] ?? '-', ENT_QUOTES, 'UTF-8');
        $statusClass = htmlspecialchars($row['status_class'] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8');
        $action = $row['action'] ?? 'view';
        ?>
        <tr>
            <td><?php echo $serial; ?></td>
            <td><?php echo htmlspecialchars($row['contract_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['party_label'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['asset_label'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo !empty($row['planned_date']) ? fromDate($row['planned_date']) : '-'; ?></td>
            <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
            <td class="text-nowrap">
                <a href="<?php echo $scheduleUrl; ?>" title="View Schedules" class="m-r-10"><i class="ti-eye text-info f-16"></i></a>
                <?php if ($action == 'assign') { ?>
                <a href="<?php echo $assignUrl; ?>" class="btn btn-sm btn-outline-primary">Assign</a>
                <?php } ?>
                <?php if ($section == 'today' && $action == 'start') { ?>
                <a href="<?php echo $scheduleUrl; ?>" class="btn btn-sm btn-warning">Start</a>
                <?php } ?>
                <?php if ($section == 'today' && $action == 'finish') { ?>
                <a href="<?php echo $scheduleUrl; ?>" class="btn btn-sm btn-success">Finish</a>
                <?php } ?>
            </td>
        </tr>
        <?php
        $serial++;
    }
}

function amcLandingRenderStatusBadge($row)
{
    $statusLabel = htmlspecialchars(trim((string) ($row['status_label'] ?? '-')), ENT_QUOTES, 'UTF-8');
    if ($statusLabel == '') {
        $statusLabel = '-';
    }
    $statusColor = htmlspecialchars($row['status_color'] ?? '#6c757d', ENT_QUOTES, 'UTF-8');
    $statusTextColor = htmlspecialchars($row['status_text_color'] ?? '#fff', ENT_QUOTES, 'UTF-8');
    echo '<span class="badge" style="background-color:' . $statusColor . ';color:' . $statusTextColor . ';">' . $statusLabel . '</span>';
}

function amcLandingRenderComplaintRows($rows, $limit = 5)
{
    $rows = amcLandingSliceRows($rows, $limit);
    if (empty($rows)) {
        echo '<tr><td colspan="8" class="text-center text-muted">No complaints found</td></tr>';
        return;
    }
    $serial = 1;
    foreach ($rows as $row) {
        $complaintId = (int) ($row['id'] ?? 0);
        $respondUrl = 'index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=' . deocdeId('id:' . $complaintId) . '&RS=1';
        ?>
        <tr>
            <td><?php echo $serial; ?></td>
            <td>#<?php echo $complaintId; ?></td>
            <td><?php echo htmlspecialchars($row['contract_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['vendor_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['asset_label'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo !empty($row['date']) ? fromDate($row['date']) : '-'; ?></td>
            <td><?php amcLandingRenderStatusBadge($row); ?></td>
            <td><a href="<?php echo htmlspecialchars($respondUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-primary">Respond</a></td>
        </tr>
        <?php
        $serial++;
    }
}

function amcLandingRenderRepairRows($rows, $limit = 5)
{
    $rows = amcLandingSliceRows($rows, $limit);
    if (empty($rows)) {
        echo '<tr><td colspan="8" class="text-center text-muted">No repair orders found</td></tr>';
        return;
    }
    $serial = 1;
    foreach ($rows as $row) {
        $repairId = (int) ($row['id'] ?? 0);
        $openUrl = 'index.php?option=COM_INSERT_AMC_REPAIR_ORDER&Rid=' . deocdeId('id:' . $repairId);
        ?>
        <tr>
            <td><?php echo $serial; ?></td>
            <td><?php echo htmlspecialchars($row['ro_no'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['contract_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['vendor_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['source'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo !empty($row['date']) ? fromDate($row['date']) : '-'; ?></td>
            <td><?php amcLandingRenderStatusBadge($row); ?></td>
            <td><a href="<?php echo htmlspecialchars($openUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-primary">Open</a></td>
        </tr>
        <?php
        $serial++;
    }
}

function amcLandingVisitViewAllUrl($section, $locId = '')
{
    $locQuery = ($locId != '') ? '&LOCID=' . urlencode($locId) : '';
    $allowed = array('overdue', 'today', 'yesterday', 'next_7_days');
    if (!in_array($section, $allowed)) {
        $section = 'overdue';
    }
    if ($section == 'overdue') {
        return 'index.php?option=COM_LIST_AMC_SCHEDULE_VISITS' . $locQuery;
    }
    return 'index.php?option=COM_LIST_AMC_SCHEDULE_VISITS&section=' . urlencode($section) . $locQuery;
}

function amcLandingRenderExpiryAccordionRows($rows, $mode)
{
    if (count((array)$rows) == 0) {
        if ($mode == 'expired') {
            echo '<tr><td colspan="3" class="text-center text-muted">No contracts expired in this range</td></tr>';
        } else {
            echo '<tr><td colspan="3" class="text-center text-muted">No contracts found</td></tr>';
        }
        return;
    }
    foreach ($rows as $row) {
        $row = is_array($row) ? $row : (array) $row;
        $contractId = (int) ($row['id'] ?? 0);
        $editUrl = 'index.php?option=COM_INSERT_CONTRACTS&Rid=' . deocdeId('id:' . $contractId);
        $displayName = htmlspecialchars($row['display_name'] ?? ($row['contract_name'] ?? '-'), ENT_QUOTES, 'UTF-8');
        ?>
        <tr>
            <td><a class="text-primary" href="<?php echo $editUrl; ?>"><?php echo $displayName; ?></a></td>
            <?php if ($mode == 'expired') { ?>
            <td><?php echo (int) ($row['days_expired'] ?? 0); ?> Days Ago</td>
            <?php } else { ?>
            <td><?php echo (int) ($row['days_left'] ?? 0); ?> Days Left</td>
            <?php } ?>
            <td><span class="badge bg-danger"><?php echo !empty($row['end_date']) ? fromDate($row['end_date']) : '-'; ?></span></td>
        </tr>
        <?php
    }
}

function amcLandingRenderContractExpiryPanel($contractExpiry)
{
    $expiringSections = $contractExpiry['expiring'] ?? array();
    $expiredSections = $contractExpiry['expired'] ?? array();
    ?>
    <div class="card h-100 amc-landing-expiry-card">
        <div class="card-header">
            <h5 class="mb-0">Expiring Contracts</h5>
        </div>
        <div class="card-body">
            <div id="amcLandingExpiringAccordion" class="amc-landing-expiry-accordion" role="tablist" aria-multiselectable="true">
                <?php if (count((array)$expiringSections) > 0) {
                    foreach ($expiringSections as $index => $section) {
                        $section = is_array($section) ? $section : (array) $section;
                        $collapseId = 'amcLandingExpiring' . ($index + 1);
                        $headingId = 'amcLandingExpiringHeading' . ($index + 1);
                        $isOpen = ($index == 0);
                        $title = htmlspecialchars($section['title'] ?? '', ENT_QUOTES, 'UTF-8');
                        $toggleClass = 'accordion-msg waves-effect waves-dark border-0' . ($isOpen ? '' : ' collapsed');
                        ?>
                        <div class="accordion-panel">
                            <div class="accordion-heading" role="tab" id="<?php echo $headingId; ?>">
                                <h3 class="card-title accordion-title p-0 bg-gray mb-0">
                                    <a class="<?php echo $toggleClass; ?>"
                                        data-bs-toggle="collapse"
                                        data-bs-parent="#amcLandingExpiringAccordion"
                                        href="#<?php echo $collapseId; ?>"
                                        aria-expanded="<?php echo $isOpen ? 'true' : 'false'; ?>"
                                        aria-controls="<?php echo $collapseId; ?>">
                                        <?php echo $title; ?>
                                    </a>
                                </h3>
                            </div>
                            <div id="<?php echo $collapseId; ?>"
                                class="panel-collapse collapse<?php echo $isOpen ? ' show' : ''; ?>"
                                role="tabpanel"
                                aria-labelledby="<?php echo $headingId; ?>">
                                <div class="accordion-content accordion-desc p-0 custom-scroll">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm f-12 mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Contract</th>
                                                    <th scope="col">Days Left</th>
                                                    <th scope="col">End Date</th>
                                                </tr>
                                            </thead>
                                            <tbody><?php amcLandingRenderExpiryAccordionRows($section['rows'] ?? array(), 'expiring'); ?></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>

            <h5 class="amc-landing-expired-title">Expired Contracts</h5>
            <div id="amcLandingExpiredAccordion" class="amc-landing-expiry-accordion" role="tablist" aria-multiselectable="true">
                <?php if (count((array)$expiredSections) > 0) {
                    foreach ($expiredSections as $index => $section) {
                        $section = is_array($section) ? $section : (array) $section;
                        $collapseId = 'amcLandingExpired' . ($index + 1);
                        $headingId = 'amcLandingExpiredHeading' . ($index + 1);
                        $isOpen = ($index == 0);
                        $title = htmlspecialchars($section['title'] ?? '', ENT_QUOTES, 'UTF-8');
                        $total = (int) ($section['total'] ?? 0);
                        $toggleClass = 'accordion-msg waves-effect waves-dark border-0' . ($isOpen ? '' : ' collapsed');
                        ?>
                        <div class="accordion-panel">
                            <div class="accordion-heading" role="tab" id="<?php echo $headingId; ?>">
                                <h3 class="card-title accordion-title p-0 bg-gray mb-0">
                                    <a class="<?php echo $toggleClass; ?>"
                                        data-bs-toggle="collapse"
                                        data-bs-parent="#amcLandingExpiredAccordion"
                                        href="#<?php echo $collapseId; ?>"
                                        aria-expanded="<?php echo $isOpen ? 'true' : 'false'; ?>"
                                        aria-controls="<?php echo $collapseId; ?>">
                                        <?php echo $title; ?> (<?php echo $total; ?>)
                                    </a>
                                </h3>
                            </div>
                            <div id="<?php echo $collapseId; ?>"
                                class="panel-collapse collapse<?php echo $isOpen ? ' show' : ''; ?>"
                                role="tabpanel"
                                aria-labelledby="<?php echo $headingId; ?>">
                                <div class="accordion-content accordion-desc p-0 custom-scroll">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm f-12 mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Contract</th>
                                                    <th scope="col">Days Expired</th>
                                                    <th scope="col">End Date</th>
                                                </tr>
                                            </thead>
                                            <tbody><?php amcLandingRenderExpiryAccordionRows($section['rows'] ?? array(), 'expired'); ?></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
    </div>
    <?php
}
?>
<link rel="stylesheet" href="assets/css/custom_view_amc_admin_landing_dashboard.css">

<div class="page-body amc-admin-landing-dashboard">
    <div class="row mb-3 align-items-center">
        <div class="col-lg-3 col-md-4">
            <select aria-label="LOCID" name="LOCID" id="amc_landing_loc_id" class="form-select js-example-basic-single">
                <option value="" <?php echo ((string) $locId == '') ? 'selected' : ''; ?>>All Locations</option>
                <?php if (count((array)$locations) > 0) {
                    foreach ($locations as $locn) { ?>
                        <option <?php echo ((string) ($locn['id'] ?? '') == (string) $locId) ? 'selected' : ''; ?> value="<?php echo htmlspecialchars($locn['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($locn['location_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php }
                } ?>
            </select>
        </div>
        <div class="col-lg-9 col-md-8 text-md-end">
            <span class="text-muted f-12"><?php echo date('l, d M Y'); ?></span>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-3 amc-landing-kpi-row">
        <?php if (count((array)$kpiCards) > 0) {
            foreach ($kpiCards as $kpi) {
                $tone = htmlspecialchars($kpi['tone'] ?? 'blue', ENT_QUOTES, 'UTF-8');
                $label = htmlspecialchars($kpi['label'] ?? '', ENT_QUOTES, 'UTF-8');
                $icon = htmlspecialchars($kpi['icon'] ?? 'fa fa-circle', ENT_QUOTES, 'UTF-8');
                $value = (int) ($kpi['value'] ?? 0);
                $mode = $kpi['mode'] ?? 'popup';
                $cardClass = 'amc-kpi-card amc-kpi-card--' . $tone;
                if ($mode == 'popup') {
                    $cardClass .= ' amc-landing-kpi-click';
                } else {
                    $cardClass .= ' amc-kpi-card--link';
                }
                $dataAttrs = '';
                if ($mode == 'popup') {
                    $dataAttrs = ' data-type="' . htmlspecialchars($kpi['type'] ?? '', ENT_QUOTES, 'UTF-8') . '"'
                        . ' data-title="' . htmlspecialchars($kpi['title'] ?? $label, ENT_QUOTES, 'UTF-8') . '"';
                }
                $cardInner = '<div class="amc-kpi-card__accent"></div>'
                    . '<div class="amc-kpi-card__body">'
                    . '<div class="amc-kpi-card__content">'
                    . '<span class="amc-kpi-card__label">' . $label . '</span>'
                    . '<span class="amc-kpi-card__value">' . $value . '</span>'
                    . '</div>'
                    . '<div class="amc-kpi-card__icon"><i class="' . $icon . '"></i></div>'
                    . '</div>';
                ?>
                <div class="col">
                    <?php if ($mode == 'link') { ?>
                    <a href="<?php echo htmlspecialchars($kpi['href'] ?? '#', ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $cardClass; ?>">
                        <?php echo $cardInner; ?>
                    </a>
                    <?php } else { ?>
                    <div class="<?php echo $cardClass; ?>"<?php echo $dataAttrs; ?>>
                        <?php echo $cardInner; ?>
                    </div>
                    <?php } ?>
                </div>
            <?php }
        } ?>
    </div>

    <div class="card mb-3 amc-landing-quick-actions-card">
        <div class="card-header border-0 pb-0"><h5 class="mb-0">Quick Actions</h5></div>
        <div class="card-body pt-2">
            <div class="amc-landing-quick-actions">
                <?php if (count((array)$quickActions) > 0) {
                    foreach ($quickActions as $action) {
                        $tileClass = 'amc-qa-tile ' . htmlspecialchars($action['class'], ENT_QUOTES, 'UTF-8');
                        $mode = $action['mode'] ?? 'link';
                        if ($mode == 'contract_popup') { ?>
                        <a href="#" class="<?php echo $tileClass; ?>"
                            data-purpose="<?php echo htmlspecialchars($action['purpose'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            data-title="<?php echo htmlspecialchars($action['title'] ?? 'Contracts', ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="amc-qa-icon"><i class="<?php echo htmlspecialchars($action['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i></span>
                            <span class="amc-qa-label"><?php echo htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                        <?php } elseif ($mode == 'reports_popup') { ?>
                        <a href="#" class="<?php echo $tileClass; ?>"
                            data-title="<?php echo htmlspecialchars($action['title'] ?? 'Reports', ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="amc-qa-icon"><i class="<?php echo htmlspecialchars($action['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i></span>
                            <span class="amc-qa-label"><?php echo htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                        <?php } else { ?>
                        <a href="<?php echo htmlspecialchars($action['href'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $tileClass; ?>">
                            <span class="amc-qa-icon"><i class="<?php echo htmlspecialchars($action['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i></span>
                            <span class="amc-qa-label"><?php echo htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                        <?php }
                    }
                } ?>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Yearly Overview</h5>
                    <small class="text-muted yearly-overview-subtitle">Customers · Vendors · In-house</small>
                </div>
                <div class="card-block">
                    <div id="amc-admin-landing-bar-chart" style="height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-8 col-md-12 mb-3 mb-lg-0">
    <div class="card mb-0 amc-landing-contracts-card h-100">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Contracts &amp; Approvals</h5>
            <div class="text-end">
                <span class="text-muted f-12 d-block amc-landing-contracts-total" data-tab-total="customer">Total: <?php echo (int) $contractTotals['customer']; ?></span>
                <a href="index.php?option=COM_LIST_CONTRACTS" class="b-b-primary text-primary f-12">View all</a>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav md-tabs infisuite_tab mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#amcLandingCustomers" role="tab" data-total="<?php echo (int) $contractTotals['customer']; ?>">Customers <span class="badge bg-primary"><?php echo (int) $contractTotals['customer']; ?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#amcLandingVendors" role="tab" data-total="<?php echo (int) $contractTotals['vendor']; ?>">Vendors <span class="badge bg-primary"><?php echo (int) $contractTotals['vendor']; ?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#amcLandingInhouse" role="tab" data-total="<?php echo (int) $contractTotals['inhouse']; ?>">In-house <span class="badge bg-primary"><?php echo (int) $contractTotals['inhouse']; ?></span></a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="amcLandingCustomers" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm f-12 mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Contract</th>
                                    <th scope="col">Contract Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Contract ID</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody><?php amcLandingRenderContractRows($contracts['customer']); ?></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="amcLandingVendors" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm f-12 mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Contract</th>
                                    <th scope="col">Contract Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Contract ID</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody><?php amcLandingRenderContractRows($contracts['vendor']); ?></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="amcLandingInhouse" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm f-12 mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Contract</th>
                                    <th scope="col">Contract Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Contract ID</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody><?php amcLandingRenderContractRows($contracts['inhouse']); ?></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <?php amcLandingRenderContractExpiryPanel($contractExpiry); ?>
        </div>
    </div>

    <?php
    $visitSections = array(
        array('key' => 'overdue', 'title' => 'Overdue Visits', 'subtitle' => '', 'card_class' => 'amc-visit-overdue'),
        array('key' => 'today', 'title' => "Today's Visits", 'subtitle' => '', 'card_class' => 'amc-visit-today'),
        array('key' => 'yesterday', 'title' => 'Yesterday Visits', 'subtitle' => '', 'card_class' => 'amc-visit-yesterday'),
        array('key' => 'next_7_days', 'title' => 'Next 7 Days Visits', 'subtitle' => 'Tomorrow + 6 days', 'card_class' => 'amc-visit-next7'),
    );
    ?>
    <div class="row mb-3">
        <?php if (count((array)$visitSections) > 0) {
            foreach ($visitSections as $section) {
                $key = $section['key'];
                $total = (int) ($visitTotals[$key] ?? 0);
                ?>
                <div class="col-lg-6 col-md-12 mb-3">
                    <div class="card h-100 <?php echo htmlspecialchars($section['card_class'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="mb-0"><?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                <?php if (!empty($section['subtitle'])) { ?>
                                <small class="text-muted"><?php echo htmlspecialchars($section['subtitle'], ENT_QUOTES, 'UTF-8'); ?></small>
                                <?php } ?>
                            </div>
                            <div class="text-end">
                                <span class="text-muted f-12 d-block">Total: <?php echo $total; ?></span>
                                <a href="<?php echo amcLandingVisitViewAllUrl($key, $locId); ?>" class="b-b-primary text-primary f-12">View all</a>
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
                                <tbody><?php amcLandingRenderVisitRows($visits[$key] ?? array(), 5, $key); ?></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php }
        } ?>
    </div>

    <div class="row mb-3">
        <div class="col-lg-6 col-md-12 mb-3">
            <div class="card h-100 amc-landing-complaints-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Open Complaints</h5>
                    <div class="text-end">
                        <span class="text-muted f-12 d-block">Total: <?php echo (int) $complaintTotal; ?></span>
                        <a href="index.php?option=COM_LIST_AMC_COMPLAINTS" class="b-b-primary text-primary f-12">View all</a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm f-12 mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Complaint No</th>
                                <th scope="col">Contract</th>
                                <th scope="col">Vendor</th>
                                <th scope="col">Asset</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody><?php amcLandingRenderComplaintRows($complaints); ?></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 mb-3">
            <div class="card h-100 amc-landing-repairs-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Repair Orders</h5>
                    <div class="text-end">
                        <span class="text-muted f-12 d-block">Total: <?php echo (int) $repairTotal; ?></span>
                        <a href="index.php?option=COM_LIST_AMC_REPAIR_ORDERS" class="b-b-primary text-primary f-12">View all</a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm f-12 mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">RO No</th>
                                <th scope="col">Contract</th>
                                <th scope="col">Vendor</th>
                                <th scope="col">Source</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody><?php amcLandingRenderRepairRows($repairs); ?></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var AmcAdminLandingBarData = <?php echo json_encode($yearlyChart); ?>;
</script>
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<script src="assets/bower_components/raphael/js/raphael.min.js"></script>
<script src="assets/bower_components/morris.js/js/morris.js"></script>
<script src="assets/js/custom_view_amc_admin_landing_dashboard.js"></script>
