<?php
$_SESSION['apply_js']['multiselest'] = 1;
$result = array('data' => []);
$vendor_ids = '';
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
  $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
  $to_date = $_POST['to'] ? date('Y-m-d', strtotime($_POST['to'])) : date('Y-m-t');
} else {
  $from_date = date('Y-m-01');
  $to_date = date('Y-m-t');
}
$vurl = API . "?mode=getvendorreconciliationstatement&method=get&from_date=" . $from_date . "&to_date=" . $to_date;
$che = callAPI($vurl);
$return = json_decode($che, 1);
$vendors = $return;
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5>Vendor Reconciliation Report</h5>
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
          <?php
          $shortcode = $_POST['shortcode_date'] ?? "TM";
          $pick_date = $_POST['pick_date'] ?? "This Month";
          $from_date = $_POST['from'] ?? fromDate(date("Y-m-01"));
          $to_date = $_POST['to'] ?? fromDate(date("Y-m-t")); ?>
          <div class="col-lg-4 mb-2">
            <input type="hidden" name="pick_date" id="pick_date" value="Today">
            <input type="hidden" name="shortcode_date" id="shortcode_date" value="TD">
            <div class="btn-group dropdown-split-primary" style="width: 100%;">
              <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                <span id="btn_date"><?php echo $pick_date; ?></span>
              </button>
              <button type="button"
                class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                <span class="visually-hidden">Toggle primary</span>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TD" ? "active" : ""; ?> empty_active"
                  id="TD" href="#!"
                  onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TW" ? "active" : ""; ?> empty_active"
                  id="TW" href="#!"
                  onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week'))); ?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday '))); ?>')">This
                  Week</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TM" ? "active" : ""; ?> empty_active"
                  id="TM" href="#!"
                  onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This
                  Month</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TY" ? "active" : ""; ?> empty_active"
                  id="TY" href="#!"
                  onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This
                  Year</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "YD" ? "active" : ""; ?> empty_active"
                  id="YD" href="#!"
                  onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>')">Yesterday</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PW" ? "active" : ""; ?> empty_active"
                  id="PW" href="#!"
                  onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous
                  Week</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PM" ? "active" : ""; ?> empty_active"
                  id="PM" href="#!"
                  onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01", strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t", strtotime('last month'))); ?>')">Previous
                  Month</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PY" ? "active" : ""; ?> empty_active"
                  id="PY" href="#!"
                  onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01", strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31", strtotime('last year'))); ?>')">Previous
                  Year</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "CD" ? "active" : ""; ?> empty_active"
                  id="CD" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
              </div>
            </div>
            <div class="row mt-2" style="display: <?php echo $shortcode == 'CD' ? '' : 'none' ?>" id="custome_date">
              <div class="col-lg-6">
                <input aria-label="From" type="text" class="form-control filterdynamic datepicker" name="from" id="from_date"
                  value="<?php echo $from_date; ?>">
              </div>
              <div class="col-lg-6">
                <input aria-label="To" type="text" class="form-control filterdynamic datepicker" name="to" id="to_date"
                  value="<?php echo $to_date; ?>">
              </div>
            </div>
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
      <h5>Vendor Reconciliation Statement</h5>
      <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered">
          <thead>
            <tr style="background-color: #eeeeee;">
              <th scope="col">#</th>
              <th scope="col">PO No.</th>
              <th scope="col">PO Date</th>
              <th scope="col">Vendor Name</th>
              <th scope="col">PO Amount (₹)</th>
              <th scope="col">GRN No.</th>
              <th scope="col">GRN Date</th>
              <th scope="col">GRN Amount (₹)</th>
              <th scope="col">Invoice No.</th>
              <th scope="col">Invoice Date</th>
              <th scope="col">Invoice Amount (₹)</th>
              <th scope="col">Qty Ordered</th>
              <th scope="col">Qty Received</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sl = 1;
            if (!empty($return['vendor_reconciliation_statement'])) {
              foreach ($return['vendor_reconciliation_statement'] as $row) {
                ?>
                <tr>
                  <td><?= $sl++; ?></td>
                  <td><?= $row['po_no'] ?: '-'; ?></td>
                  <td><?= !empty($row['po_date']) ? date('d-M-Y', strtotime($row['po_date'])) : '-'; ?></td>
                  <td><?= $row['party_name'] ?: '-'; ?></td>
                  <td class="text-end"><?= number_format($row['po_amount'] ?: 0, 2); ?></td>
                  <td><?= $row['grn_no'] ?: '-'; ?></td>
                  <td><?= !empty($row['grn_date']) ? date('d-M-Y', strtotime($row['grn_date'])) : '-'; ?></td>
                  <td class="text-end"><?= number_format($row['grn_amount'] ?: 0, 2); ?></td>
                  <td><?= $row['invoice_no'] ?: '-'; ?></td>
                  <td><?= !empty($row['invoice_date']) ? date('d-M-Y', strtotime($row['invoice_date'])) : '-'; ?></td>
                  <td class="text-end"><?= number_format($row['invoice_amount'] ?: 0, 2); ?></td>
                  <td class="text-end"><?= number_format($row['qty_ordered'] ?: 0, 2); ?></td>
                  <td class="text-end"><?= number_format($row['qty_received'] ?: 0, 2); ?></td>
                  <td><?= $row['reconciliation_status'] ?: '-'; ?></td>
                </tr>
                <?php
              }
            } else {
              ?>
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
              <?php
            }
            ?>
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
