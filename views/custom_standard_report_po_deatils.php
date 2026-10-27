<?php
$_SESSION['apply_js']['multiselest'] = '1';
$result = array('data' => []);
$vendor_ids = '';

if (isset($_POST['applyform'])) {
    $postdata = $_POST;
    $from_date = str_replace('/', '-', $_POST['start_date']);
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date = str_replace('/', '-', $_POST['to_date']);
    $to_date = date('Y-m-d', strtotime($to_date));
  if($_POST['vendor_ids'] != ''){
    $vendor_ids = implode(',', $_POST['vendor_ids']);  // Get the selected vendor IDs as a comma-separated list
  }
    $url = API . "?mode=togetpodetails&from_date=" . $from_date . "&to_date=" . $to_date."&vendor_ids=" . $vendor_ids;
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

<style type="text/css">
  .ontable tr td {
    vertical-align: middle;
    padding: 5px;
  }
</style>
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
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h4 class="">PO Details Report</h4>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=PO Details" target="_blank">CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=PO Details" target="_blank">Excel</a>
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
                $selected = in_array($vendor['id'], (array)$_POST['vendor_ids'] ?? []) ? 'selected' : '';
                echo '<option value="' . $vendor['id'] . '" ' . $selected . '>' . $vendor['company'] . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-lg-3">
            <span class="form-label"> Start Date</span>
            <div class="input-group custom-date">
              <span class="input-group-text addon-style" id="basic-addon3">
                <span class=""><i class="fa fa-calendar"></i></span>
              </span>
              <input aria-label="Start Date" type="text" class="form-control datepicker" name="start_date" id="date_of_grant" onchange="enddatechange()" value="<?php echo $from_date; ?>" required>
            </div>
          </div>
          <div class="col-lg-3">
            <span class="form-label"> End Date</span>
            <div class="input-group custom-date">
              <span class="input-group-text addon-style" id="basic-addon3_2">
                <span class=""><i class="fa fa-calendar"></i></span>
              </span>
              <input aria-label="To Date" type="text" class="form-control datepicker" name="to_date" id="date_of_end" value="<?php echo $to_date; ?>" required>
            </div>
          </div>
          <div class="col-lg-2 mt-1">
            <span class="form-label"> &nbsp; </span>
            <button type="submit" name="applyform" value="apply" class="mt-3 btn btn-info waves-effect waves-light input-group-text mt-0">
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
            <th scope="col">PO Number</th>
            <th scope="col">PO Date</th>
            <th scope="col">Vendor Name</th>


            <th scope="col">Item Name</th>
            <th scope="col" class="text-right">Item Quantity</th>
            <th scope="col" class="text-right">Pending Quantity</th>
            <th scope="col" class="text-right">Received Quantity</th>
            <th scope="col" class="text-right">Item Price</th>
            <th scope="col" class="text-right">Item Total Amount</th>

          </tr>

          <?php
          $sl = 1;
          if (!empty($result['data'])) {
            foreach ($result['data'] as $data) {
              $cssdata[] = array(
                'Sl No' => $sl,
                'PO Number' => $data['po_number'],
                'PO Date' => $data['po_date'],
                'Vendor Name' => $data['vendor_name'],
                'Item Name' => $data['item_name'],
                'Item Quantity' => $data['item_quantity'],
                'Pending Quantity' => $data['pending_qty'],
                'Received Quantity' => $data['item_quantity']-$data['pending_qty'],
                'Item Price' => $data['item_price'],
                'Item Total Amount' => $data['item_total_amount'],
              );
              ?>
              <tr>
                <td><?php echo $sl; ?></td>
                <td><?php echo $data['po_number']; ?></td>
                <td><?php echo fromDate($data['po_date']); ?></td>
                <td><?php echo $data['vendor_name']; ?></td>


                <td><?php echo $data['item_name']; ?></td>
                <td class="text-right"><?php echo number_format($data['item_quantity']); ?></td>
                <td class="text-right"><?php echo number_format($data['pending_qty']); ?></td>
                <td class="text-right"><?php echo number_format($data['item_quantity']-$data['pending_qty']); ?></td>
                <td class="text-right"><?php echo number_format($data['item_price'], 2); ?></td>
                <td class="text-right"><?php echo number_format($data['item_total_amount'], 2); ?></td>

              </tr>
              <?php
              $sl++;
            }
            $_SESSION['download_data'] = $cssdata;
          } else { ?>
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
        </table>
      </div>
    </div>
  </div>
</div>
