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

$url = API . "?mode=togetmaterialshortagebasedonpurchaseorder&from_date=" . $from_date . "&to_date=" . $to_date;

$apires = callAPI($url);
$result = json_decode($apires, true);

$shortages = $result['material_shortage_list'] ?? [];
$_SESSION['download_data'] = [];

?>

<div class="card">
  <div class="card-header">
    <h5>Material Shortage Report - PO</h5>
    <div class="card-header-right">
      <div class="dropdown-primary dropdown open">
        <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
        <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
          <a class="dropdown-item waves-light waves-effect"
             href="custom_view_download.php?page=MaterialShortage&format=csv" target="_blank">CSV</a>
          <a class="dropdown-item waves-light waves-effect"
             href="custom_view_download.php?page=MaterialShortage&format=excel" target="_blank">Excel</a>
        </div>
      </div>
    </div>
  </div>

  <div class="card-body">

<?php include_once 'date_filter_common.php'; ?>

    <hr>

    <!-- Shortage Report Table -->
    <?php if (count((array)$shortages) != 0) { ?>
      <table class="table table-separate table-head-custom table-checkable data-table">
        <thead>
          <tr>
            <th scope="col" class="text-right">SL No</th>
            <th scope="col">Item</th>
            <th scope="col" class="text-right">Required Qty</th>
            <th scope="col" class="text-right">Available Qty</th>
            <th scope="col" class="text-right">Shortage Qty</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $cssdata = array();
          $sl = 1;
          foreach ($shortages as $item_id => $row) { ?>
              <tr>
                <td class="text-right p-1"><?php echo $sl; ?></td>
                <td class="p-1"><?php echo $row['item_name']; ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($row['required_qty']); ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($row['available_qty']); ?></td>
                <td class="text-right p-1 text-danger fw-bold"><?php echo customeummarmonyformate($row['shortage_qty']); ?></td>
              </tr>
              <?php
              $cssdata[] = array(
                'Sl No'        => $sl,
                'Item'         => $row['item_name'],
                'Required Qty' => customeummarmonyformate($row['required_qty']),
                'Available Qty'=> customeummarmonyformate($row['available_qty']),
                'Shortage Qty' => customeummarmonyformate($row['shortage_qty'])
              );
              $sl++;

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

