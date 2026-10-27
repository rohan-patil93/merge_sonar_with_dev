<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : date('Y-m-t');
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
}

$url = API . "?mode=togetpovsgrndata&transaction_table=purchase_orders&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);
$groups = $result['povsgrn_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>PO vs GRN Matching Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=POvsGRN&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=POvsGRN&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">

        <form method="post" action="">
            <div class="row col-lg-12 ">
                <?php /**/
                $shortcode = $_POST['shortcode_date'] ?? "TM";
                $pick_date = $_POST['pick_date'] ?? "This Month";
                $from_date = $_POST['from'] ?? fromDate(date("Y-m-01"));
                $to_date   = $_POST['to']   ?? fromDate(date("Y-m-t")); ?>
                <div class="col-lg-4 mb-2">
                    <input type="hidden" name="pick_date" id="pick_date" value="<?php echo $pick_date; ?>">
                    <input type="hidden" name="shortcode_date" id="shortcode_date" value="<?php echo $shortcode; ?>">
                    <div class="btn-group dropdown-split-primary" style="width: 100%;">
                        <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                            <span id="btn_date"><?php echo $pick_date; ?></span>
                        </button>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                            <span class="visually-hidden">Toggle primary</span>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TD" ? "active" : ""; ?> empty_active" id="TD" href="#!" onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                            <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TW" ? "active" : ""; ?> empty_active" id="TW" href="#!" onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week'))); ?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday '))); ?>')">This Week</a>
                            <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TM" ? "active" : ""; ?> empty_active" id="TM" href="#!" onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                            <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TY" ? "active" : ""; ?> empty_active" id="TY" href="#!" onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "YD" ? "active" : ""; ?> empty_active" id="YD" href="#!" onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>')">Yesterday</a>
                            <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PW" ? "active" : ""; ?> empty_active" id="PW" href="#!" onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous Week</a>
                            <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PM" ? "active" : ""; ?> empty_active" id="PM" href="#!" onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01", strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t", strtotime('last month'))); ?>')">Previous Month</a>
                            <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PY" ? "active" : ""; ?> empty_active" id="PY" href="#!" onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01", strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31", strtotime('last year'))); ?>')">Previous Year</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "CD" ? "active" : ""; ?> empty_active" id="CD" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
                        </div>
                    </div>
                    <div class="row mt-2" style="display: <?php echo $shortcode == 'CD' ? '' : 'none' ?>" id="custome_date">
                        <div class="col-lg-6">
                            <input aria-label="From" type="text" class="form-control filterdynamic datepicker" name="from" id="from_date" value="<?php echo $from_date; ?>">
                        </div>
                        <div class="col-lg-6">
                            <input aria-label="To" type="text" class="form-control filterdynamic datepicker" name="to" id="to_date" value="<?php echo $to_date; ?>">
                        </div>
                    </div>
                </div>

                <div class="col-lg-2">
                    <button type="submit" name="applyform" value="apply" class="btn btn-info">Search</button>
                </div>
            </div>
        </form>

        <hr>

        <!-- Report Table -->
        <?php if (count((array)$groups) != 0) { ?>
            <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc">
                <thead>
                    <tr>
                        <th scope="col">PO Info</th>
                        <th scope="col">GRNs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cssdata = array();
                    $sl = 1;
                    foreach ($groups as $po_id => $tableslist) { ?>
                        <tr>
                            <!-- Left Column: PO Header -->
                            <td>
                                <table role="presentation">
                                    <tr>
                                        <td class="p-1">PO No.</td>
                                        <td class="p-1 cursor-pointer text-primary"
                                        onclick="openBucketModal('<?= htmlspecialchars($tableslist[0]['purchase_id']) ?>>','PO','<?php echo $tableslist[0]['po_no']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $tableslist[0]['po_no']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">PO Date</td>
                                        <td class="p-1"><?php echo fromDate($tableslist[0]['po_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Vendor</td>
                                        <td class="p-1"><?php echo $tableslist[0]['party_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">PO Qty</td>
                                        <td class="p-1 text-right"><?php echo customeummarmonyformate($tableslist[0]['po_qty']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">PO Amount</td>
                                        <td class="p-1 text-right"><?php echo customeummarmonyformate($tableslist[0]['po_amount']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Requested By</td>
                                        <td class="p-1"><?php echo $_SESSION['users'][$tableslist[0]['po_requested']] ?? '-'; ?></td>
                                    </tr>
                                </table>
                            </td>


                            <td class="align-top">
                                <table style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="p-1">GRN No</th>
                                            <th scope="col" class="p-1">GRN Date</th>
                                            <th scope="col" class="p-1">GRN Party</th>
                                            <th scope="col" class="text-right p-1">GRN Qty</th>
                                            <th scope="col" class="text-right p-1">GRN Amount</th>
                                            <th scope="col" class="p-1">GRN Requested By</th>
                                            <th scope="col" class="text-right p-1">Qty Variance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tableslist as $row) { ?>
                                            <tr>
                                                <td class="p-1 cursor-pointer text-primary" onclick="openBucketModal('<?= htmlspecialchars($row['goods_id']) ?>','GRN', '<?php echo $row['grn_no']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $row['grn_no'] ?? "-"; ?></td>
                                                <td class="p-1"><?php echo fromDate($row['grn_date']) ?? "-"; ?></td>
                                                <td class="p-1"><?php echo $row['grn_party'] ?? "-"; ?></td>
                                                <td class="text-right p-1"><?php echo customeummarmonyformate($row['grn_qty']); ?></td>
                                                <td class="text-right p-1"><?php echo customeummarmonyformate($row['grn_amount']); ?></td>
                                                <td class="p-1"><?php echo $_SESSION['users'][ $row['grn_requested_by']] ?? "-"; ?></td>
                                                <td class="text-right p-1 <?php echo $row['qty_variance'] == 0 ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo customeummarmonyformate($row['qty_variance']); ?>
                                                </td>
                                            </tr>
                                        <?php
                                            $cssdata[] = array(
                                                'Sl No'        => $sl,
                                                'PO No'        => $tableslist[0]['po_no'],
                                                'PO Date'      => $tableslist[0]['po_date'],
                                                'Vendor'       => $tableslist[0]['party_name'],
                                                'PO Qty'       => customeummarmonyformate($tableslist[0]['po_qty']),
                                                'PO Amount'    => customeummarmonyformate($tableslist[0]['po_amount']),
                                                'Requested By' => $_SESSION['users'][$tableslist[0]['po_requested']] ?? '-',
                                                'GRN No'       => $row['grn_no'] ?? "-",
                                                'GRN Date'     => $row['grn_date'] ?? "-",
                                                'GRN Party'    => $row['grn_party'] ?? "-",
                                                'GRN Qty'      => customeummarmonyformate($row['grn_qty']),
                                                'GRN Amount'   => customeummarmonyformate($row['grn_amount']),
                                                'GRN Requested By' => $_SESSION['users'][$row['grn_requested_by']] ?? "-",
                                                'Qty Variance' => customeummarmonyformate($row['qty_variance'])
                                            );
                                            $sl++;
                                        } ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php }
                    $_SESSION['download_data'] = $cssdata; ?>
                </tbody>
            </table>
        <?php } else { ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
        <?php } ?>

    </div>
</div>

<script>
    function dynamicSelectDateRange(type, name, start = "", end = "") {
        $("#btn_date").text(name);
        $("#pick_date").val(name);
        $("#shortcode_date").val(type);
        $(".empty_active").removeClass("active");
        $("#" + type).addClass("active");
        if (name == "Custom Date") {
            $("#custome_date").show('slow');

        } else {
            $("#custome_date").hide();
            $("#from_date").val(start);
            $("#to_date").val(end);
        }
    }
</script>

<div class="modal fade" id="bucketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content"><!-- filled by PHP partial via .load() --></div>
    </div>
</div>

<script>
    function openBucketModal(Rid, type, transaction_no) {
        var url = 'views/custom_standard_report_po_vs_grn_matching_popup.php' +
            '?rid=' + encodeURIComponent(Rid) +
            '&type=' + encodeURIComponent(type) +
            '&transaction_no=' + encodeURIComponent(transaction_no);

        // show a quick loading state
        var $content = $('#bucketModal .modal-content');
        $content.html(
            '<div class="modal-header py-2">' +
            '<h6 class="modal-title">Loading…</h6>' +
            '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
            '</div>' +
            '<div class="modal-body py-4 text-center text-muted">Fetching records…</div>'
        );

        $content.load(url, function() {
            // when the HTML is in, show the modal
            var modal = new bootstrap.Modal(document.getElementById('bucketModal'));
            modal.show();
        });
    }
</script>
