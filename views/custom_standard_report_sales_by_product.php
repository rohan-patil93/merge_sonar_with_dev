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
$url = API . "?mode=togetsalesbyproduct&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['sales_by_product_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Sales by Product Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=SalesByProduct&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=SalesByProduct&format=excel" target="_blank">Excel</a>
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
                        <th scope="col">Product Code</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">UOM</th>
                        <th scope="col" class="text-right">Qty Sold</th>
                        <th scope="col" class="text-right">Total Sales</th>
                        <th scope="col" class="text-right">Discount</th>
                        <th scope="col" class="text-right">Tax</th>
                        <th scope="col" class="text-right">Net Sales</th>
                        <th scope="col" class="text-right">No. of Orders</th>
                        <th scope="col" class="text-right">Avg. Selling Price</th>
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
                            <td class="p-1"><?php echo $r['product_code']; ?></td>
                            <td class="p-1"><?php echo $r['product_name']; ?></td>
                            <td class="p-1"><?php echo $r['product_category']; ?></td>
                            <td class="p-1"><?php echo $r['uom']; ?></td>
                            <td class="text-right p-1"><?php echo $r['total_quantity_sold']; ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['total_sales_value']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['total_discount']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['total_tax']); ?></td>
                            <td class="text-right p-1 fw-bold"><?php echo customeummarmonyformate($r['net_sales_value']); ?></td>
                            <td class="text-right p-1"><?php echo $r['no_of_orders']; ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['avg_selling_price']); ?></td>
                        </tr>
                    <?php
                        $cssdata[] = [
                            'Sl No'               => $sl++,
                            'Product Code'        => $r['product_code'],
                            'Product Name'        => $r['product_name'],
                            'Category'            => $r['product_category'],
                            'UOM'                 => $r['uom'],
                            'Qty Sold'            => $r['total_quantity_sold'],
                            'Total Sales'         => customeummarmonyformate($r['total_sales_value']),
                            'Discount'            => customeummarmonyformate($r['total_discount']),
                            'Tax'                 => customeummarmonyformate($r['total_tax']),
                            'Net Sales'           => customeummarmonyformate($r['net_sales_value']),
                            'No. of Orders'       => $r['no_of_orders'],
                            'Avg. Selling Price'  => customeummarmonyformate($r['avg_selling_price'])
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
