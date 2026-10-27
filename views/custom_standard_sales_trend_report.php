<?php
$_SESSION['apply_js']['multiselest'] = 1;
$result = array('data' => []);
$vendor_ids = '';
$vendor_ids = '';
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
  $category = $_POST['category'];
} else {
  $category = "monthly";
}
$vurl = API . "?mode=getsalestrendreport&method=get&category=" . $category;
$che = callAPI($vurl);
$return = json_decode($che, 1);
$vendors = $return;
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="">Sales Trend Report</h5>
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
            <span class="form-label">Category <label for="date" class="text-danger">*</span></label>
            <select aria-label="Category" class="form-select js-example-basic-single" name="category" id="category" required>
              <option value="monthly">Monthly</option>
              <option value="quaterly">Quaterly</option>
              <option value="yearly">Yearly</option>
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
  <div class="card-body">
    <div class="table-responsive mt-3">
      <table class="table table-hover table-bordered">
        <thead>
          <tr style="background-color: #eeeeee;">
            <th scope="col">#</th>
            <th scope="col">
              <?php
              switch ($category) {
                case 'monthly':
                  echo 'Month';
                  break;
                case 'quaterly':
                  echo 'Quarter';
                  break;
                case 'yearly':
                  echo 'Year';
                  break;
                default:
                  echo 'Period';
              }
              ?>
            </th>
            <th scope="col" class="text-end">Total Orders</th>
            <th scope="col" class="text-end">Total Sales (₹)</th>
            <th scope="col" class="text-end">Total Tax (₹)</th>
            <th scope="col" class="text-end">Total Discount (₹)</th>
            <th scope="col" class="text-end">Avg. Order Value (₹)</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sl = 1;
          if (!empty($vendors['sales_trend_report'])) {
            foreach ($vendors['sales_trend_report'] as $row) { ?>
              <tr>
                <td><?= $sl++; ?></td>
                <td>
                  <?php
                  if ($category == 'monthly') {
                    echo date('M-Y', strtotime($row['period'] . '-01'));
                  } else {
                    echo $row['period'];
                  }
                  ?>
                </td>
                <td class="text-end"><?= number_format($row['total_orders']); ?></td>
                <td class="text-end"><?= number_format($row['total_sales'], 2); ?></td>
                <td class="text-end"><?= number_format($row['total_tax'], 2); ?></td>
                <td class="text-end"><?= number_format($row['total_discount'], 2); ?></td>
                <td class="text-end"><?= number_format($row['avg_order_value'], 2); ?></td>
              </tr>
            <?php }
          } else { ?>
            <tr>
              <td colspan="7">
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
        </tbody>
      </table>
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
