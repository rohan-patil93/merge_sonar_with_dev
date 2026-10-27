<?php
$_SESSION['apply_js']['multiselect'] = 1; // enable select2 JS

$selected_items = $_GET['item_id'] ?? array();
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$queryData='';
// === when user clicks search ===
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
  $queryData = http_build_query($_GET);
}
$url = API . "?mode=getstationeryitemsreportdata&method=get&".$queryData;
$result = callAPI($url);
$result = json_decode($result, true);
$items = $result['items_res'] ?? array();
$opening = $result['opening_res'] ?? array();
$report  = $result['report_res'] ?? array();

?>

<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5>Stationery Items Report</h5>
      <div class="card-header-right">
        <a href="javascript:history.go(-1)" class="btn btn-light btn-sm">
          <i class="icofont icofont-reply"></i> Back
        </a>
      </div>
    </div>

    <div class="card-body p-4">
      <form method="get" id="filterForm">
        <input type="hidden" name="option" value="COM_LIST_AMC_STATIONERY_ITEMS_REPORT">

        <div class="row align-items-end">
          <div class="col-lg-4 col-md-6 form-group">
            <span>Item Name</span>
            <select aria-label="Item []" name="item_id[]" class="form-control js-example-basic-single" style="width:100%" multiple>
              <option value="">All Items</option>
              <?php foreach ($items as $it) {
               $selected = in_array($it['id'], $selected_items) ? 'selected' : '';  ?>
                <option value="<?php echo $it['id']; ?>" <?php echo $selected; ?>>
                  <?php echo htmlspecialchars($it['item_name']); ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="col-lg-3 col-md-4 form-group">
            <span>From Date</span>
            <input aria-label="From Date" type="date" name="from_date" id="from_date" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>" required>
          </div>

          <div class="col-lg-3 col-md-4 form-group">
            <span>To Date</span>
            <input aria-label="To Date" type="date" name="to_date" id="to_date" class="form-control"  value="<?php echo htmlspecialchars($to_date); ?>" required>
          </div>

          <div class="col-lg-2 col-md-3 form-group">
            <button type="submit" class="btn btn-primary w-100">
              <i class="icofont icofont-search"></i> Search
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

<div class="card mt-4">
    <div class="card-body table-responsive">

        <table class="table table-hover">
            <thead class="bg-primary">
                <tr>
                    <th scope="col">Transaction No</th>
                    <th scope="col">Transaction Date</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Item Name</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Amount</th>
                </tr>
            </thead>

            <tbody>

                <!-- ==========================
                     1️⃣ OPENING BALANCE
                =========================== -->
                <?php if (count((array)$opening)>0) { ?>


                    <?php foreach ($opening as $row) { ?>
                        <tr>
                            <td>-</td>
                            <td>-</td> <!-- empty date -->
                            <td>-</td>
                            <td><?php echo $row['item_name']; ?></td>
                            <td><?php echo $row['total_qty']; ?></td>
                            <td><?php echo $row['total_amount']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>

                <?php if (count((array)$report)>0) { ?>
                    <tr >
                        <td colspan="6" class="text-center">-</td>
                    </tr>

                    <?php foreach ($report as $row) { ?>
                        <tr>
                            <td><?php echo $row['transaction_no']; ?></td>
                            <td><?php echo $row['transaction_date']; ?></td>
                            <td><?php echo $row['supplier']; ?></td>
                            <td><?php echo $row['item_name']; ?></td>
                            <td><?php echo $row['qty']; ?></td>
                            <td><?php echo $row['amount']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>

</div>

<script>
$(document).ready(function() {
  $('.js-example-basic-single').select2();
});

$('#filterForm').on('submit', function(e) {
  const fromDate = new Date($('#from_date').val());
  const toDate = new Date($('#to_date').val());

  if (toDate < fromDate) {
    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','To Date cannot be earlier than From Date. ','')
    e.preventDefault();
    return false;
  }
});
</script>
