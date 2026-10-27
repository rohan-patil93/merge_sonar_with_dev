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

$transaction_table = "sales_orders";
$url = API . "?mode=togetopensalesorders&transaction_table=" . $transaction_table . "&from_date=" . $from_date . "&to_date=" . $to_date . "&company_id=" . $_SESSION['company_id'];
$apires = callAPI($url);
$result = json_decode($apires, true);

$salesorders = $result['open_sales_orders'] ?? array();
$status_progress = $result['status_progress'] ?? array();
$statuse = ViewStatusData();

$_SESSION['download_data'] = array();
?>

<div class="card">
  <div class="card-header">
    <h5>Open Sales Orders (Backlog Report)</h5>
    <div class="card-header-right">
      <div class="dropdown-primary dropdown open">
        <button class="btn btn-primary dropdown-toggle waves-light" type="button" data-bs-toggle="dropdown">More</button>
        <div class="dropdown-menu p-0">
          <a class="dropdown-item" href="custom_view_download.php?page=OpenSalesOrders&format=csv" target="_blank">CSV</a>
          <a class="dropdown-item" href="custom_view_download.php?page=OpenSalesOrders&format=excel" target="_blank">Excel</a>
        </div>
      </div>
    </div>
  </div>

  <div class="card-body">
    <!-- ✅ Date Filter -->
    <?php include_once 'date_filter_common.php'; ?>

    <hr>

    <!-- ✅ Report Table -->
    <?php if (count((array)$salesorders) != 0) { ?>
      <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc">
        <thead>
          <tr>
            <th scope="col">Sales Order Info</th>
            <th scope="col">Items</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $cssdata = array();
          $sl = 1;
          foreach ($salesorders as $so_id => $tableslist) { ?>
            <tr>
              <td>
                <table role="presentation">
                  <tr><td class="p-1">SO No.</td><td class="p-1"><?php echo $tableslist[0]['so_no']; ?></td></tr>
                  <tr><td class="p-1">SO Date</td><td class="p-1"><?php echo fromDate($tableslist[0]['so_date']); ?></td></tr>
                  <tr><td class="p-1">Customer</td><td class="p-1"><?php echo $tableslist[0]['customer_name']; ?></td></tr>
                  <tr><td class="p-1">Reference No.</td><td class="p-1"><?php echo $tableslist[0]['reference_no'] ?? '-'; ?></td></tr>
                  <tr>
                    <td class="p-1">Status</td>
                    <td class="p-1">
                      <span class="form-label label label-md"
                        style="color:<?php echo $statuse[$tableslist[0]['status_id']]['color'] ?? 'white'; ?>;
                        background-color:<?php echo $statuse[$tableslist[0]['status_id']]['background_color'] ?? 'blue'; ?>;">
                        <?php echo $status_progress[$tableslist[0]['status_id']]['name'] ?? 'Pending'; ?>
                      </span>
                    </td>
                  </tr>
                  <tr><td class="p-1">Expected Delivery</td><td class="p-1"><?php echo $tableslist[0]['expected_delivery_date'] ?? '-'; ?></td></tr>
                </table>
              </td>

              <td class="align-top">
                <table style="width: 100%;">
                  <thead>
                    <tr>
                      <th scope="col" class="p-1">Item</th>
                      <th scope="col" class="text-right p-1">Ordered Qty</th>
                      <th scope="col" class="text-right p-1">Delivered Qty</th>
                      <th scope="col" class="text-right p-1">Pending Qty</th>
                      <th scope="col" class="text-right p-1">Amount</th>
                      <th scope="col" class="text-right p-1">Tax</th>
                      <th scope="col" class="text-right p-1">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($tableslist as $list1) {
                      $delivered_qty = $list1['item_quantity'] - $list1['pending_qty']; ?>
                      <tr>
                        <td class="w-b-w-s p-1"><?php echo $list1['item_name']; ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($list1['item_quantity']); ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($delivered_qty); ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($list1['pending_qty']); ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($list1['item_amount']); ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($list1['tax_amount']); ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($list1['total_amount']); ?></td>
                      </tr>
                    <?php
                      $cssdata[] = array(
                        'Sl No' => $sl,
                        'SO No' => $tableslist[0]['so_no'],
                        'SO Date' => fromDate($tableslist[0]['so_date']),
                        'Customer' => $tableslist[0]['customer_name'],
                        'Reference No' => $tableslist[0]['reference_no'],
                        'Status' => $status_progress[$tableslist[0]['status_id']]['name'] ?? 'Pending',
                        'Item' => $list1['item_name'],
                        'Ordered Qty' => customeummarmonyformate($list1['item_quantity']),
                        'Delivered Qty' => customeummarmonyformate($delivered_qty),
                        'Pending Qty' => customeummarmonyformate($list1['pending_qty']),
                        'Amount' => customeummarmonyformate($list1['item_amount']),
                        'Tax' => customeummarmonyformate($list1['tax_amount']),
                        'Total' => customeummarmonyformate($list1['total_amount'])
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
