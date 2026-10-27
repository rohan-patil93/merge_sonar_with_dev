<?php
$_SESSION['apply_js']['multiselest'] = 1;
$result = array('data' => []);
$vendor_ids = '';
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
  $date = $_POST['date'] ? date('Y-m-d', strtotime($_POST['date'])) : date('Y-m-01');
} else {
  $date = date('Y-m-01');
}
$vurl = API . "?mode=getvendorpaymentagingreport&method=get&date=" . $date;
$che = callAPI($vurl);
$return = json_decode($che, 1);
$vendors = $return;
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5>Vendor Payment Aging Report</h5>
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
            <th scope="col">Vendor Name</th>
            <th scope="col">Invoice No.</th>
            <th scope="col">Invoice Date</th>
            <th scope="col">Due Date</th>
            <th scope="col" class="text-end">Invoice Amount (₹)</th>
            <th scope="col" class="text-end">Paid (₹)</th>
            <th scope="col" class="text-end">Outstanding (₹)</th>
            <th scope="col" class="text-end">Days Overdue</th>
            <th scope="col" class="text-end">0–30 Days</th>
            <th scope="col" class="text-end">31–60 Days</th>
            <th scope="col" class="text-end">61–90 Days</th>
            <th scope="col" class="text-end">91–180 Days</th>
            <th scope="col" class="text-end">181+ Days</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sl = 1;
          if (!empty($vendors['vendor_payment_aging_report'])) {
            foreach ($vendors['vendor_payment_aging_report'] as $row) { ?>
              <tr>
                <td><?= $sl++; ?></td>
                <td><?= htmlspecialchars($row['party_name']); ?></td>
                <td><?= htmlspecialchars($row['invoice_no']); ?></td>
                <td><?= date('d-M-Y', strtotime($row['invoice_date'])); ?></td>
                <td><?= date('d-M-Y', strtotime($row['due_date'])); ?></td>
                <td class="text-end"><?= number_format($row['invoice_amount'], 2); ?></td>
                <td class="text-end"><?= number_format($row['paid_amount'], 2); ?></td>
                <td class="text-end fw-bold"><?= number_format($row['outstanding_amount'], 2); ?></td>
                <td class="text-end"><?= max(0, intval($row['days_overdue'])); ?></td>
                <td class="text-end"><?= number_format($row['days_0_30'], 2); ?></td>
                <td class="text-end"><?= number_format($row['days_31_60'], 2); ?></td>
                <td class="text-end"><?= number_format($row['days_61_90'], 2); ?></td>
                <td class="text-end"><?= number_format($row['days_91_180'], 2); ?></td>
                <td class="text-end"><?= number_format($row['days_181_plus'], 2); ?></td>
              </tr>
            <?php }
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
        </tbody>

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
