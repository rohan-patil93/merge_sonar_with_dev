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

$transaction_table = "requisitions";
$url = API . "?mode=togetpurchaserequisition&transaction_table=" . $transaction_table . "&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, 1);

// requisition_list is grouped by requisition_no
$requisitiontables = $result['requisition_list'] ?? array();
$status_progress   = $result['status_progress'] ?? array();
$statuse           = ViewStatusData();

$_SESSION['download_data'] = array();
?>

<div class="card">
  <div class="card-header">
    <h5>Purchase Requisition Report</h5>
    <div class="card-header-right">
      <div class="dropdown-primary dropdown open">
        <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
        <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
          <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=PurchaseRequisition&format=csv" target="_blank">CSV</a>
          <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=PurchaseRequisition&format=excel" target="_blank">Excel</a>
        </div>
      </div>
    </div>
  </div>

  <div class="card-body">

<?php include_once 'date_filter_common.php'; ?>

    <hr>

    <!-- Requisition Report Table -->
    <?php if (count((array)$requisitiontables) != 0) { ?>
      <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc">
        <thead>
          <tr>
            <th scope="col">Requisition Info</th>
            <th scope="col">Items</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $cssdata = array();
          $sl = 1;
           foreach ($requisitiontables as $req_no => $tableslist) {
            ?>
            <tr>
              <!-- Left Column: Header Info -->
              <td>
                <table role="presentation">
                  <tr>
                    <td class="p-1">Requisition No.</td>
                    <td class="p-1"><?php echo $tableslist[0]['transaction_number']; ?></td>
                  </tr>
                  <tr>
                    <td class="p-1">Request Date</td>
                    <td class="p-1"><?php echo $tableslist[0]['transaction_date']; ?></td>
                  </tr>
                  <tr>
                    <td class="p-1">Requested By</td>
                    <td class="p-1"><?php echo $tableslist[0]['party_name']; ?></td>
                  </tr>
                  <tr>
                    <td class="p-1">Status</td>
                    <td class="p-1">
                      <span class="form-label label label-md"
                        style="color:<?php echo $statuse[$tableslist[0]['current_status']]['color'] ?? 'white'; ?>;
                        background-color:<?php echo $statuse[$tableslist[0]['current_status']]['background_color'] ?? 'blue'; ?>;">
                        <?php echo $status_progress[$tableslist[0]['current_status']]['name']; ?>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td class="p-1"><?php if ($tableslist[0]['ref_type'] == "sales_orders") {  echo "SO";
} else {echo "Ref";
} ?> Number</td>
                    <td class="p-1 w-b-w-s "><?php echo $tableslist[0]['ref_no'] != "" ? $tableslist[0]['ref_no'] : "-"; ?></td>
                  </tr>
                </table>
              </td>

              <!-- Right Column: Items -->
              <td class="align-top">
                <table style="width: 100%;">
                  <thead>
                    <tr>
                      <th scope="col" class="p-1">Item</th>
                      <th scope="col" class="text-right p-1">Quantity</th>
                      <th scope="col" class="text-right p-1">Pending Quantity</th>
                      <th scope="col" class="text-right p-1">Amount</th>
                      <th scope="col" class="text-right p-1">Tax</th>
                      <th scope="col" class="text-right p-1">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($tableslist as $tableslist1) { ?>
                      <tr>
                        <td class="w-b-w-s p-1"><?php echo $tableslist1['item_name']; ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['item_quantity']); ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['pending_qty']); ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['atm_item_amount']); ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['itm_tax_amount']); ?></td>
                        <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['atm_total_amount']); ?></td>
                      </tr>
                    <?php
              $cssdata[] = array(
              'Sl No'            => $sl,
              'Requisition No.'  => $tableslist[0]['transaction_number'],
              'Request Date'     => $tableslist[0]['transaction_date'],
              'Requested By'     => $tableslist[0]['party_name'],
              'Status'           => $status_progress[$tableslist[0]['current_status']]['name'],
              'Ref Number'       => $tableslist[0]['ref_no'] != "" ? $tableslist1[0]['ref_no'] : "-",
              'Item'             => $tableslist1['item_name'],
              'Quantity'         => customeummarmonyformate($tableslist1['item_quantity']),
              'Pending Quantity' => customeummarmonyformate($tableslist1['pending_qty']),
              'Amount'           => customeummarmonyformate($tableslist1['atm_item_amount']),
              'Tax'              => customeummarmonyformate($tableslist1['itm_tax_amount']),
              'Total'            => customeummarmonyformate($tableslist1['atm_total_amount'])
          );
          $sl++;
                  } ?>
                  </tbody>
                </table>
              </td>
            </tr>
          <?php
        }
           $_SESSION['download_data'] = $cssdata; ?>
        </tbody>
      </table>
    <?php } else { ?>
      <tr>
              <td colspan="15">
                <div class="card-px text-center py-20 m-t-30">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
                </div>
              </td>
            </tr>
    <?php } ?>
  </div>
</div>
