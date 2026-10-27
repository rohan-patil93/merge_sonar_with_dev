<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

// ===============================
// 📅 Date Filters
// ===============================
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : date('Y-m-t');
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
}

// ===============================
// 🌐 API Call
// ===============================
$url = API . "?mode=togetsalesbyregion&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['sales_by_region_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Sales by Region Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=SalesByRegion&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=SalesByRegion&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- 📅 Date Filter -->
        <?php include_once 'date_filter_common.php'; ?>
        <hr>

        <?php if (count((array)$list) != 0) { ?>
            <table class="table table-separate table-head-custom table-checkable data-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-right">SL No</th>
                        <th scope="col">Region</th>
                        <th scope="col" class="text-right">No. of Orders</th>
                        <th scope="col" class="text-right">Total Customers</th>
                        <th scope="col" class="text-right">Total Qty</th>
                        <th scope="col" class="text-right">Total Sales</th>
                        <th scope="col" class="text-right">Discount</th>
                        <th scope="col" class="text-right">Tax</th>
                        <th scope="col" class="text-right">Net Sales</th>
                        <th scope="col" class="text-right">Avg. Order Value</th>
                        <th scope="col">Top Product</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sl = 1;
                    $cssdata = [];
                    foreach ($list as $r) {
                    ?>
                        <tr>
                            <td class="text-right p-1"><?php echo $sl; ?></td>
                            <td class="p-1"><?php echo $r['region_name']; ?></td>
                            <td class="text-right p-1"><?php echo $r['no_of_orders']; ?></td>
                            <td class="text-right p-1"><?php echo $r['total_customers']; ?></td>
                            <td class="text-right p-1"><?php echo $r['total_quantity_sold']; ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['total_sales_value']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['total_discount']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['total_tax']); ?></td>
                            <td class="text-right p-1 fw-bold"><?php echo customeummarmonyformate($r['net_sales_value']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['avg_order_value']); ?></td>
                            <td class="p-1"><?php echo $r['top_product']; ?></td>
                        </tr>
                    <?php
                        $cssdata[] = [
                            'Sl No'              => $sl++,
                            'Region'             => $r['region_name'],
                            'No. of Orders'      => $r['no_of_orders'],
                            'Total Customers'    => $r['total_customers'],
                            'Total Qty'          => $r['total_quantity_sold'],
                            'Total Sales'        => customeummarmonyformate($r['total_sales_value']),
                            'Discount'           => customeummarmonyformate($r['total_discount']),
                            'Tax'                => customeummarmonyformate($r['total_tax']),
                            'Net Sales'          => customeummarmonyformate($r['net_sales_value']),
                            'Avg. Order Value'   => customeummarmonyformate($r['avg_order_value']),
                            'Top Product'        => $r['top_product']
                        ];
                    }
                    $_SESSION['download_data'] = $cssdata;
                    ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="text-center py-4">
                <p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Data Available</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
