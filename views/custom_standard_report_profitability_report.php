<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;

// 📅 Handle date filters
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : '';
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : '';
    $report_level = $_POST['report_level'] ?? 'customer';
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
    $report_level = 'customer';
}

// 🌐 Call API
$url = API . "?mode=togetprofitabilityreport&from_date=".$from_date."&to_date=".$to_date."&report_level=".$report_level;
$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['report_data'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Profitability Report (<?php echo ucfirst($report_level); ?> Level)</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    More
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=ProfitabilityReport&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=ProfitabilityReport&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" class="row g-3">
            <input type="hidden" name="applyform" value="apply">
                    <?php
        $shortcode = $_POST['shortcode_date'] ?? "TM";
        $pick_date = $_POST['pick_date'] ?? "This Month";
        $from_date = $_POST['from'] ?? fromDate(date("Y-m-01"));
        $to_date   = $_POST['to']   ?? fromDate(date("Y-m-t")); ?>
        <div class="col-lg-4 mb-2">
            <input type="hidden" name="pick_date" id="pick_date" value="<?php echo $pick_date; ?>">
            <input type="hidden" name="shortcode_date" id="shortcode_date" value="<?php echo $shortcode; ?>">
            <div class="btn-group dropdown-split-primary" style="width: 100%;">
                <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;">
                    <i class="ti-calendar"></i><span id="btn_date"><?php echo $pick_date; ?></span>
                </button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="width: 20%;height: 40px;">
                    <span class="visually-hidden">Toggle primary</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TD" ? "active" : ""; ?> empty_active"
                        id="TD" href="#!"
                        onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TW" ? "active" : ""; ?> empty_active"
                        id="TW" href="#!"
                        onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week'))); ?>','<?php echo fromDate(date("Y-m-d", strtotime('sunday this week'))); ?>')">This Week</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TM" ? "active" : ""; ?> empty_active"
                        id="TM" href="#!"
                        onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TY" ? "active" : ""; ?> empty_active"
                        id="TY" href="#!"
                        onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "YD" ? "active" : ""; ?> empty_active"
                        id="YD" href="#!"
                        onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>')">Yesterday</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PW" ? "active" : ""; ?> empty_active"
                        id="PW" href="#!"
                        onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous Week</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PM" ? "active" : ""; ?> empty_active"
                        id="PM" href="#!"
                        onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01", strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t", strtotime('last month'))); ?>')">Previous Month</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PY" ? "active" : ""; ?> empty_active"
                        id="PY" href="#!"
                        onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01", strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31", strtotime('last year'))); ?>')">Previous Year</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "CD" ? "active" : ""; ?> empty_active"
                        id="CD" href="#!"
                        onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
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
            <div class="col-md-3">
                <select aria-label="Report Level" name="report_level" class="form-control js-example-basic-single">
                    <option value="customer" <?php if($report_level=='customer') { echo 'selected';
} ?>>Customer Level</option>
                    <option value="product" <?php if($report_level=='product') { echo 'selected';
} ?>>Product Level</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </form>

        <hr>

        <?php if (count((array)$list) > 0) { ?>
            <table class="table table-separate table-head-custom table-checkable data-table">
                <thead>
                    <tr>
                        <th scope="col">Rank</th>
                        <th scope="col"><?php echo ($report_level == 'customer') ? 'Customer Name' : 'Product Name'; ?></th>
                        <?php if ($report_level == 'product') { ?><th scope="col" class="text-right">Total Qty</th><?php
} ?>
                        <th scope="col" class="text-right">Total Sales (₹)</th>
                        <th scope="col" class="text-right">Total Cost (₹)</th>
                        <th scope="col" class="text-right">Total Tax (₹)</th>
                        <th scope="col" class="text-right">Gross Profit (₹)</th>
                        <th scope="col" class="text-right">Profit %</th>
                        <th scope="col" class="text-right">No. of Invoices</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $csv_data = [];
                    foreach ($list as $row) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <?php if ($report_level == 'product') { ?><td class="text-right"><?php echo $row['total_qty']; ?></td><?php
} ?>
                            <td class="text-right"><?php echo customeummarmonyformate($row['total_sales']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['total_cost']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['total_tax']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['gross_profit']); ?></td>
                            <td class="text-right"><?php echo $row['profit_percentage']; ?>%</td>
                            <td class="text-right"><?php echo $row['total_invoices']; ?></td>
                        </tr>
                        <?php
                        $csv_row = [
                            'Rank' => $i++,
                            ($report_level == 'customer' ? 'Customer Name' : 'Product Name') => $row['name'],
                        ];
                        if ($report_level == 'product') { $csv_row['Total Qty'] = $row['total_qty'];
}
                        $csv_row['Total Sales (₹)'] = $row['total_sales'];
                        $csv_row['Total Cost (₹)'] = $row['total_cost'];
                        $csv_row['Total Tax (₹)'] = $row['total_tax'];
                        $csv_row['Gross Profit (₹)'] = $row['gross_profit'];
                        $csv_row['Profit %'] = $row['profit_percentage'];
                        $csv_row['No. of Invoices'] = $row['total_invoices'];
                        $csv_data[] = $csv_row;
                    }
                    $_SESSION['download_data'] = $csv_data;
                    ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="text-center py-4">
                <p class="text-muted f-16 fw-bold">
                    <i class="ti-alert text-warning"></i> No Data Available
                </p>
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
