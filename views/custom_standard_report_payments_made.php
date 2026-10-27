<?php
$_SESSION['apply_js']['multiselest'] = 1;
$result = array('data' => []);
$vendor_ids = '';

$shortcode = $_POST['shortcode_date'] ?? "TM";
$pick_date = $_POST['pick_date'] ?? "This Month";

if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date = $_POST['to'] ? date('Y-m-d', strtotime($_POST['to'])) : date('Y-m-t');

    if (!empty($_POST['vendor_ids'])) {
        $vendor_ids = implode(',', $_POST['vendor_ids']);
    }

    $url = API . "?mode=togetpaymentsmade&from_date=" . $from_date . "&to_date=" . $to_date . "&vendor_ids=" . $vendor_ids;
    $apires = callAPI($url);
    $result = json_decode($apires, 1);
}
$table_name = "vendors";
$vdata1['order'] = "id ASC";
$vdata1["field"] = "id,display_name,company";
$vdata1["where"] = array('company_id' => $_SESSION['company_id'], 'is_active' => '0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API . "?mode=gettabledetailswithconditions&table=$table_name&data=$vdata";
$che = callAPI($vurl);
$return = json_decode($che, 1);
$vendors = $return;

$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;
?>
<?php
$from_date = $from_date != "" ? fromDate($from_date) : "";
$to_date = $to_date != "" ? fromDate($to_date) : "";
?>
<script type="text/javascript">
  function enddatechange() {
    console.log("check date");
    var sd = $("#date_of_grant").val();
    $("#date_of_end").val(sd);
  }
</script>
<style type="text/css">
  .ontable tr td {
    vertical-align: middle;
    padding: 5px;
  }
</style>

<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h4 class="">Payments Made Report</h4>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
            data-dropdown-out="fadeOut">
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Payments Made"
              target="_blank">CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Payments Made"
              target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body">
      <form method="post" action="">
        <div class="row col-lg-12">

          <div class="col-lg-3">
            <span class="form-label">Select Vendors</span>
            <select aria-label="Vendor Ids[]" name="vendor_ids[]" class="form-control js-example-basic-multiple" multiple>
              <?php
              foreach ($vendors as $vendor) {
                $selected = in_array($vendor['id'], (array) $_POST['vendor_ids'] ?? []) ? 'selected' : '';
                echo '<option value="' . $vendor['id'] . '" ' . $selected . '>' . $vendor['company'] . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-lg-4 mb-2">
            <input type="hidden" name="pick_date" id="pick_date" value="<?php echo $pick_date; ?>">
            <input type="hidden" name="shortcode_date" id="shortcode_date" value="<?php echo $shortcode; ?>">
            <span class="form-label">Select Date Range</span>
            <div class="btn-group dropdown-split-primary" style="width: 100%;">
              <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;">
                <i class="ti-calendar"></i> <span id="btn_date"><?php echo $pick_date; ?></span>
              </button>
              <button type="button"
                class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                <span class="visually-hidden">Toggle</span>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item <?php echo $shortcode == "TD" ? "active" : ""; ?>" id="TD" href="#!"
                  onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date('Y-m-d')); ?>','<?php echo fromDate(date('Y-m-d')); ?>')">Today</a>
                <a class="dropdown-item <?php echo $shortcode == 'YD' ? 'active' : ''; ?>" id="YD" href="#!"
                  onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d', strtotime('-1 day'))); ?>','<?php echo fromDate(date('Y-m-d', strtotime('-1 day'))); ?>')">Yesterday</a>
                <a class="dropdown-item <?php echo $shortcode == 'TW' ? 'active' : ''; ?>" id="TW" href="#!"
                  onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date('Y-m-d', strtotime('monday this week'))); ?>','<?php echo fromDate(date('Y-m-d', strtotime('sunday this week'))); ?>')">This
                  Week</a>
                <a class="dropdown-item <?php echo $shortcode == 'TM' ? 'active' : ''; ?>" id="TM" href="#!"
                  onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date('Y-m-01')); ?>','<?php echo fromDate(date('Y-m-t')); ?>')">This
                  Month</a>
                <a class="dropdown-item <?php echo $shortcode == 'TY' ? 'active' : ''; ?>" id="TY" href="#!"
                  onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date('Y-01-01')); ?>','<?php echo fromDate(date('Y-12-31')); ?>')">This
                  Year</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item <?php echo $shortcode == 'PM' ? 'active' : ''; ?>" id="PM" href="#!"
                  onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date('Y-m-01', strtotime('last month'))); ?>','<?php echo fromDate(date('Y-m-t', strtotime('last month'))); ?>')">Previous
                  Month</a>
                <a class="dropdown-item <?php echo $shortcode == 'PY' ? 'active' : ''; ?>" id="PY" href="#!"
                  onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date('Y-01-01', strtotime('last year'))); ?>','<?php echo fromDate(date('Y-12-31', strtotime('last year'))); ?>')">Previous
                  Year</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item <?php echo $shortcode == 'CD' ? 'active' : ''; ?>" id="CD" href="#!"
                  onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
              </div>
            </div>


            <div class="row mt-2" style="display: <?php echo $shortcode == 'CD' ? '' : 'none'; ?>" id="custome_date">
              <div class="col-lg-6">
                <input aria-label="From" type="text" class="form-control datepicker" name="from" id="from_date"
                  value="<?php echo fromDate($from_date); ?>">
              </div>
              <div class="col-lg-6">
                <input aria-label="To" type="text" class="form-control datepicker" name="to" id="to_date"
                  value="<?php echo fromDate($to_date); ?>">
              </div>
            </div>
          </div>
          <div class="col-lg-2 mt-1">
            <span class="form-label"> &nbsp; </span>
            <button type="submit" name="applyform" value="apply"
              class="mt-3 btn btn-info waves-effect waves-light input-group-text mt-0">
              Apply
            </button>
            <input type="hidden" name="submit" value="applyform">
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php include_once "custom_standard_top_print.php"; ?>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive mt-3">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">#</th>
            <th scope="col">Payment Number</th>
            <th scope="col">Vendor Name</th>
            <th scope="col">Payment Date</th>
            <th scope="col" class="text-right">Amount Paid</th>
            <th scope="col">Payment Method</th>
            <th scope="col">Payment Status</th>
            <th scope="col">Reference Number</th>
            <th scope="col">Notes</th>
          </tr>

          <?php
          $sl = 1;
          if (!empty($result['data'])) {
            foreach ($result['data'] as $data) {
              $cssdata[] = array(
                'Sl No' => $sl,
                'Payment Number' => $data['reference_number'],
                'Vendor Name' => $data['vendor_name'],
                'Payment Date' => $data['payment_date'],
                'Amount Paid' => $data['amount_paid'],
                'Payment Method' => $data['payment_method'],
                'Payment Status' => $data['payment_status'],
                'Reference Number' => $data['ref_no'],
                'Notes' => '',
              );
              ?>
              <tr>
                <td><?php echo $sl; ?></td>
                <td><?php echo $data['reference_number']; ?></td>
                <td><?php echo $data['vendor_name']; ?></td>
                <td><?php echo fromDate($data['payment_date']); ?></td>
                <td class="text-right"><?php echo number_format($data['amount_paid'], 2); ?></td>
                <td><?php echo $data['payment_method']; ?></td>
                <td><?php echo $data['payment_status']; ?></td>
                <td><?php echo $data['ref_no']; ?></td>

                <td><?php echo ''; ?></td> <!-- No Notes column -->
              </tr>
              <?php
              $sl++;
            }
            $_SESSION['download_data'] = $cssdata;
          } else { ?>
            <tr>
              <td colspan="9">
                <div class="card-px text-center py-20 m-t-30">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>
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
