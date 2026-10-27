<?php

$url = API . "?mode=getcontractamcfmdashboarddata&method=get";

$response = callAPI($url);

$data = json_decode($response, true);

$hasAccess = !empty($data['has_access']);

$previously = $data['previously'] ?? array('rows' => array(), 'total' => 0, 'page' => 1, 'limit' => 10, 'total_pages' => 0);

$thisMonth = $data['this_month'] ?? array('rows' => array(), 'total' => 0, 'page' => 1, 'limit' => 10, 'total_pages' => 0);

$nextComing = $data['next_coming'] ?? array('rows' => array(), 'total' => 0, 'page' => 1, 'limit' => 10, 'total_pages' => 0);

function contractAmcFmDashboardShowingText($sectionData)

{

    $total = (int) ($sectionData['total'] ?? 0);

    if ($total == 0) {

        return 'Showing 0 of 0';

    }

    $page = max(1, (int) ($sectionData['page'] ?? 1));

    $limit = (int) ($sectionData['limit'] ?? 10);

    $from = (($page - 1) * $limit) + 1;

    $to = min($page * $limit, $total);

    return 'Showing ' . $from . '–' . $to . ' of ' . $total;

}

function renderContractAmcFmDashboardRows($rows, $page = 1, $limit = 10)

{

    if (count((array)$rows) == 0) {

        echo '<tr><td colspan="7" class="text-center text-muted">No records</td></tr>';

        return;

    }

    $serial = (($page - 1) * $limit) + 1;

    foreach ($rows as $row) {

        if (is_object($row)) {

            $row = json_decode(json_encode($row), true);

        }

        $scheduleId = (int) ($row['id'] ?? 0);

        $vendorName = htmlspecialchars($row['vendor_name'] ?? '—', ENT_QUOTES, 'UTF-8');

        $assetLabel = htmlspecialchars($row['asset_label'] ?? '—', ENT_QUOTES, 'UTF-8');

        $plannedDisplay = htmlspecialchars($row['planned_display'] ?? '—', ENT_QUOTES, 'UTF-8');

        $completedDisplay = htmlspecialchars($row['completed_display'] ?? '—', ENT_QUOTES, 'UTF-8');

        $statusLabel = htmlspecialchars($row['fm_status_label'] ?? 'Due', ENT_QUOTES, 'UTF-8');

        $statusClass = htmlspecialchars($row['fm_status_class'] ?? 'bg-primary', ENT_QUOTES, 'UTF-8');

        $canConfirm = !empty($row['can_confirm']);

        ?>

        <tr>

            <td><?php echo $serial; ?></td>

            <td><?php echo $vendorName; ?></td>

            <td><?php echo $assetLabel; ?></td>

            <td><?php echo $plannedDisplay; ?></td>

            <td><?php echo $completedDisplay; ?></td>

            <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>

            <td>

                <?php if ($canConfirm) { ?>

                <button type="button" class="btn btn-sm btn-primary openContractAmcFmConfirmModal" data-schedule-id="<?php echo $scheduleId; ?>">Confirm</button>

                <?php } else { ?>

                <span class="text-muted">—</span>

                <?php } ?>

            </td>

        </tr>

        <?php

        $serial++;

    }

}

function renderContractAmcFmDashboardPagination($sectionKey, $sectionData)

{

    $page = max(1, (int) ($sectionData['page'] ?? 1));

    $totalPages = (int) ($sectionData['total_pages'] ?? 0);

    if ($totalPages <= 1) {

        return;

    }

    $start = max(1, $page - 2);

    $end = min($totalPages, $page + 2);

    ?>

    <nav class="mt-2" aria-label="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?> pagination">

        <ul class="pagination pagination-sm mb-0 justify-content-end flex-wrap" id="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>_pager">

            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">

                <a class="page-link contract-amc-fm-section-page" href="#" data-section="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>" data-page="<?php echo max(1, $page - 1); ?>">Prev</a>

            </li>

            <?php if ($start > 1) { ?>

            <li class="page-item">

                <a class="page-link contract-amc-fm-section-page" href="#" data-section="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>" data-page="1">1</a>

            </li>

            <?php if ($start > 2) { ?>

            <li class="page-item disabled"><span class="page-link">…</span></li>

            <?php } ?>

            <?php } ?>

            <?php for ($p = $start; $p <= $end; $p++) { ?>

            <li class="page-item <?php echo ($p == $page) ? 'active' : ''; ?>">

                <a class="page-link contract-amc-fm-section-page" href="#" data-section="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>" data-page="<?php echo $p; ?>"><?php echo $p; ?></a>

            </li>

            <?php } ?>

            <?php if ($end < $totalPages) { ?>

            <?php if ($end < $totalPages - 1) { ?>

            <li class="page-item disabled"><span class="page-link">…</span></li>

            <?php } ?>

            <li class="page-item">

                <a class="page-link contract-amc-fm-section-page" href="#" data-section="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>" data-page="<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>

            </li>

            <?php } ?>

            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">

                <a class="page-link contract-amc-fm-section-page" href="#" data-section="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>" data-page="<?php echo min($totalPages, $page + 1); ?>">Next</a>

            </li>

        </ul>

    </nav>

    <?php

}

function renderContractAmcFmDashboardSectionCard($sectionKey, $title, $sectionData)

{

    $rows = $sectionData['rows'] ?? array();

    $page = (int) ($sectionData['page'] ?? 1);

    $limit = (int) ($sectionData['limit'] ?? 10);

    $showingText = contractAmcFmDashboardShowingText($sectionData);

    ?>

    <div class="col-md-6 mb-3">

        <div class="card h-100 border">

            <div class="card-header bg-light py-2">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">

                    <h6 class="mb-0"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h6>

                    <small class="text-muted contract-amc-fm-showing-text" id="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>_showing"><?php echo htmlspecialchars($showingText, ENT_QUOTES, 'UTF-8'); ?></small>

                </div>

                <input aria-label="Input field" type="text" class="form-control form-control-sm contract-amc-fm-section-search" id="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>_search" data-section="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search vendor, asset, contract, planned date...">

            </div>

            <div class="card-body p-0 d-flex flex-column">

                <div class="table-responsive">

                    <table class="table table-bordered table-sm f-12 mb-0">

                        <thead class="table-light">

                            <tr>

                                <th scope="col" style="width:50px;">S.No</th>

                                <th scope="col">Vendor</th>

                                <th scope="col">Asset</th>

                                <th scope="col">Planned</th>

                                <th scope="col">Completed</th>

                                <th scope="col">Status</th>

                                <th scope="col">Action</th>

                            </tr>

                        </thead>

                        <tbody id="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>_tbody">

                            <?php renderContractAmcFmDashboardRows($rows, $page, $limit); ?>

                        </tbody>

                    </table>

                </div>

                <div class="px-2 pb-2" id="<?php echo htmlspecialchars($sectionKey, ENT_QUOTES, 'UTF-8'); ?>_pager_wrap">

                    <?php renderContractAmcFmDashboardPagination($sectionKey, $sectionData); ?>

                </div>

            </div>

        </div>

    </div>

    <?php

}

?>

<div class="page-body">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>

                <h5 class="mb-0">Facility Manager Dashboard</h5>

                <small class="text-muted">Confirm vendor AMC visits after technician completion</small>

            </div>

            <div class="d-flex flex-wrap gap-2">

                <a href="index.php?option=COM_VIEW_AMC_PORTAL_HOME" class="btn btn-outline-info btn-sm">Portal Home</a>

                <button type="button" class="btn btn-secondary btn-sm">Back</button>

            </div>

        </div>

        <div class="card-body">

            <?php if (!$hasAccess) { ?>

                <div class="alert alert-info mb-0" role="alert">

                    No facility manager contracts are linked to your login. Contact admin to map your user to a customer record and assign you as Customer Connect on a vendor AMC contract.

                </div>

            <?php } else { ?>

                <div class="alert alert-info f-12 mb-3" role="alert">

                    <strong>Due</strong> — upcoming visit. <strong>Delayed</strong> — overdue, technician not finished.

                    <strong>Pending FM</strong> — technician finished; click Confirm to close with remarks and document.

                    <strong>Closed</strong> — you have confirmed the visit.

                </div>

                <div class="row">

                    <?php renderContractAmcFmDashboardSectionCard('previously', 'Previously', $previously); ?>

                    <?php renderContractAmcFmDashboardSectionCard('this_month', 'This Month', $thisMonth); ?>

                </div>

                <div class="row">

                    <?php renderContractAmcFmDashboardSectionCard('next_coming', 'Next Coming', $nextComing); ?>

                </div>

            <?php } ?>

        </div>

    </div>

</div>

<div class="modal fade" id="kt_modal_contract_amc_fm_confirm" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow rounded-3" id="contract_amc_fm_confirm_modal_body"></div>

    </div>

</div>

<script>

window.addEventListener("load", function() {

    function escapeHtml(text) {

        if (text == null) {

            return '';

        }

        return String(text)

            .replace(/&/g, '&amp;')

            .replace(/</g, '&lt;')

            .replace(/>/g, '&gt;')

            .replace(/"/g, '&quot;');

    }

    function buildShowingText(sectionData) {

        var total = parseInt(sectionData.total || 0, 10);

        if (total == 0) {

            return 'Showing 0 of 0';

        }

        var page = Math.max(1, parseInt(sectionData.page || 1, 10));

        var limit = parseInt(sectionData.limit || 10, 10);

        var from = ((page - 1) * limit) + 1;

        var to = Math.min(page * limit, total);

        return 'Showing ' + from + '–' + to + ' of ' + total;

    }

    function buildRowsHtml(rows, page, limit) {

        if (!rows || rows.length == 0) {

            return '<tr><td colspan="7" class="text-center text-muted">No records</td></tr>';

        }

        var html = '';

        var serial = ((page - 1) * limit) + 1;

        rows.forEach(function(row) {

            var scheduleId = parseInt(row.id || 0, 10);

            var actionCell = row.can_confirm

                ? '<button type="button" class="btn btn-sm btn-primary openContractAmcFmConfirmModal" data-schedule-id="' + scheduleId + '">Confirm</button>'

                : '<span class="text-muted">—</span>';

            html += '<tr>'

                + '<td>' + serial + '</td>'

                + '<td>' + escapeHtml(row.vendor_name || '—') + '</td>'

                + '<td>' + escapeHtml(row.asset_label || '—') + '</td>'

                + '<td>' + escapeHtml(row.planned_display || '—') + '</td>'

                + '<td>' + escapeHtml(row.completed_display || '—') + '</td>'

                + '<td><span class="badge ' + escapeHtml(row.fm_status_class || 'bg-primary') + '">' + escapeHtml(row.fm_status_label || 'Due') + '</span></td>'

                + '<td>' + actionCell + '</td>'

                + '</tr>';

            serial++;

        });

        return html;

    }

    function buildPagerHtml(sectionKey, sectionData) {

        var page = Math.max(1, parseInt(sectionData.page || 1, 10));

        var totalPages = parseInt(sectionData.total_pages || 0, 10);

        if (totalPages <= 1) {

            return '';

        }

        var start = Math.max(1, page - 2);

        var end = Math.min(totalPages, page + 2);

        var html = '<nav class="mt-2" aria-label="' + escapeHtml(sectionKey) + ' pagination">'

            + '<ul class="pagination pagination-sm mb-0 justify-content-end flex-wrap" id="' + escapeHtml(sectionKey) + '_pager">';

        html += '<li class="page-item' + (page <= 1 ? ' disabled' : '') + '">'

            + '<a class="page-link contract-amc-fm-section-page" href="#" data-section="' + escapeHtml(sectionKey) + '" data-page="' + Math.max(1, page - 1) + '">Prev</a></li>';

        if (start > 1) {

            html += '<li class="page-item"><a class="page-link contract-amc-fm-section-page" href="#" data-section="' + escapeHtml(sectionKey) + '" data-page="1">1</a></li>';

            if (start > 2) {

                html += '<li class="page-item disabled"><span class="page-link">…</span></li>';

            }

        }

        for (var p = start; p <= end; p++) {

            html += '<li class="page-item' + (p == page ? ' active' : '') + '">'

                + '<a class="page-link contract-amc-fm-section-page" href="#" data-section="' + escapeHtml(sectionKey) + '" data-page="' + p + '">' + p + '</a></li>';

        }

        if (end < totalPages) {

            if (end < totalPages - 1) {

                html += '<li class="page-item disabled"><span class="page-link">…</span></li>';

            }

            html += '<li class="page-item"><a class="page-link contract-amc-fm-section-page" href="#" data-section="' + escapeHtml(sectionKey) + '" data-page="' + totalPages + '">' + totalPages + '</a></li>';

        }

        html += '<li class="page-item' + (page >= totalPages ? ' disabled' : '') + '">'

            + '<a class="page-link contract-amc-fm-section-page" href="#" data-section="' + escapeHtml(sectionKey) + '" data-page="' + Math.min(totalPages, page + 1) + '">Next</a></li>';

        html += '</ul></nav>';

        return html;

    }

    function loadContractAmcFmSection(section, page, search) {

        var $tbody = $('#' + section + '_tbody');

        $tbody.html('<tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>');

        $.ajax({

            url: 'ajax.php',

            type: 'POST',

            data: {

                page: 'getcontractamcfmdashboardsection',

                section: section,

                page_num: page,

                search: search

            },

            success: function(resp) {

                var data = typeof resp === 'string' ? JSON.parse(resp) : resp;

                var sectionData = data[section] || {};

                var rows = sectionData.rows || [];

                var currentPage = parseInt(sectionData.page || 1, 10);

                var limit = parseInt(sectionData.limit || 10, 10);

                $tbody.html(buildRowsHtml(rows, currentPage, limit));

                $('#' + section + '_showing').text(buildShowingText(sectionData));

                $('#' + section + '_pager_wrap').html(buildPagerHtml(section, sectionData));

            },

            error: function() {

                $tbody.html('<tr><td colspan="7" class="text-center text-danger">Unable to load records</td></tr>');

            }

        });

    }

    var fmSearchTimers = {};

    $(document).on('keyup', '.contract-amc-fm-section-search', function() {

        var section = $(this).data('section');

        var searchVal = $.trim($(this).val());

        if (fmSearchTimers[section]) {

            clearTimeout(fmSearchTimers[section]);

        }

        fmSearchTimers[section] = setTimeout(function() {

            loadContractAmcFmSection(section, 1, searchVal);

        }, 300);

    });

    $(document).on('click', '.contract-amc-fm-section-page', function(e) {

        e.preventDefault();

        var $item = $(this).closest('.page-item');

        if ($item.hasClass('disabled') || $item.hasClass('active')) {

            return;

        }

        var section = $(this).data('section');

        var page = parseInt($(this).data('page') || 1, 10);

        var searchVal = $.trim($('#' + section + '_search').val());

        loadContractAmcFmSection(section, page, searchVal);

    });

    $(document).on("click", ".openContractAmcFmConfirmModal", function(e) {

        e.preventDefault();

        var scheduleId = $(this).data("schedule-id");

        var loader = '<div class="text-center p-5"><img src="assets/images/spiner/spiner3.gif" style="width: 200px;" alt="Preview"></div>';

        $("#contract_amc_fm_confirm_modal_body").html(loader);

        $("#kt_modal_contract_amc_fm_confirm").modal("show");

        $.ajax({

            url: "views/custom_contract_amc_fm_confirm.php",

            type: "POST",

            data: { schedule_id: scheduleId },

            success: function(html) {

                $("#contract_amc_fm_confirm_modal_body").html(html);

            }

        });

    });

});

</script>

