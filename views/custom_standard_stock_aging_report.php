<?php
$_SESSION['apply_js']['multiselest'] = 1;
$result = array('data' => []);
$vendor_ids = '';
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
  $date = $_POST['date'] ? date('Y-m-d', strtotime($_POST['date'])) : date('Y-m-01');
  $category = $_POST['category'];
} else {
  $date = date('Y-m-01');
}
$vurl = API . "?mode=getstockagingreportdetails&method=get&date=" . $date . "&category=" . $category;
$che = callAPI($vurl);
$return = json_decode($che, 1);
$vendors = $return;
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5>Stock Aging Report</h5>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
            data-dropdown-out="fadeOut">
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=PO Details"
              target="_blank">CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=PO Details"
              target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body">
      <form method="post" action="">
        <div class="row col-lg-12 align-items-end">
          <div class="col-lg-6 mb-2">
            <span class="form-label">Date <label for="date" class="text-danger">*</span></label>
            <input type="date" class="form-control" name="date" id="date" required>
          </div>
          <div class="col-lg-6 mb-2">
            <span class="form-label">Category <label for="date" class="text-danger">*</span></label>
            <select aria-label="Category" class="form-select js-example-basic-single" name="category" id="category" required>
              <option value="">Select Category</option>
              <?php
              if (!empty($return['category']) && is_array($return['category'])) {
                foreach ($return['category'] as $cat) {
                  $selected = (isset($_POST['category']) && $_POST['category'] == $cat['id']) ? 'selected' : '';
                  echo "<option value='{$cat['id']
}' {$selected
}>{$cat['category_description']
}</option>";
                }
              }
              ?>
            </select>
          </div>
          <div class="col-lg-2">
            <button type="submit" name="applyform" value="apply" class="btn btn-info">Search</button>
          </div>
        </div>
      </form>
      <hr>
    </div>
  </div>
  <?php include_once "custom_standard_top_print.php"; ?>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive mt-3">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">#</th>
            <th scope="col">Item Code</th>
            <th scope="col">Item Name</th>
            <th scope="col">Category</th>
            <th scope="col">Warehouse</th>
            <th scope="col" class="text-right">Opening Qty</th>
            <th scope="col" class="text-right">Received Qty</th>
            <th scope="col" class="text-right">Issued Qty</th>
            <th scope="col" class="text-right">Closing Qty</th>
            <th scope="col" class="text-right">0–30 Days</th>
            <th scope="col" class="text-right">31–60 Days</th>
            <th scope="col" class="text-right">61–90 Days</th>
            <th scope="col" class="text-right">91–180 Days</th>
            <th scope="col" class="text-right">181+ Days</th>
          </tr>
          <?php
          $sl = 1;
          if (!empty($return['stock_aging_report']) && !empty($return['stock_aging_report'])) {
            foreach ($return['stock_aging_report'] as $data) {

              $cssdata[] = array(
                'Sl No' => $sl,
                'Item Code' => $data['product_code'],
                'Item Name' => $data['item_name'],
                'Category' => $data['category_name'] ?? '-',
                'Warehouse' => $data['warehouse_name'] ?? '-',
                'Opening Qty' => number_format($data['opening_quantity'], 2),
                'Received Qty' => number_format($data['total_in_qty'], 2),
                'Issued Qty' => number_format($data['total_out_qty'], 2),
                'Closing Qty' => number_format($data['closing_stock'], 2),
                '0–30 Days' => number_format($data['days_0_30'], 2),
                '31–60 Days' => number_format($data['days_31_60'], 2),
                '61–90 Days' => number_format($data['days_61_90'], 2),
                '91–180 Days' => number_format($data['days_91_180'] ?? 0, 2),
                '181+ Days' => number_format($data['days_181_plus'] ?? $data['days_90_plus'], 2),
                'Last Movement Date' => !empty($data['last_movement_date']) ? fromDate($data['last_movement_date']) : '-',
              );
              ?>
              <tr>
                <td><?= $sl; ?></td>
                <td><?= $data['product_code']; ?></td>
                <td><?= $data['item_name']; ?></td>
                <td><?= $data['category_name'] ?? '-'; ?></td>
                <td><?= $data['warehouse_name'] ?? '-'; ?></td>
                <td class="text-right"><?= number_format($data['opening_quantity'], 2); ?></td>
                <td class="text-right"><?= number_format($data['total_in_qty'], 2); ?></td>
                <td class="text-right"><?= number_format($data['total_out_qty'], 2); ?></td>
                <td class="text-right"><?= number_format($data['closing_stock'], 2); ?></td>
                <td class="text-right"><?= number_format($data['days_0_30'], 2); ?></td>
                <td class="text-right"><?= number_format($data['days_31_60'], 2); ?></td>
                <td class="text-right"><?= number_format($data['days_61_90'], 2); ?></td>
                <td class="text-right"><?= number_format($data['days_91_180'] ?? 0, 2); ?></td>
                <td class="text-right"><?= number_format($data['days_181_plus'] ?? $data['days_90_plus'], 2); ?></td>
              </tr>
              <?php
              $sl++;
            }
            $_SESSION['download_data'] = $cssdata;
          } else { ?>
            <tr>
              <td colspan="14">
                <div class="card-px text-center py-20 m-t-30">
                  <p class="text-muted f-16 fw-bold m-b-30">
                    <i class="ti-alert text-warning"></i> No Data Available
                  </p>
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
                </div>
              </td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </div>
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
