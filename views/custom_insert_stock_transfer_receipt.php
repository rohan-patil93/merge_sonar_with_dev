<?php
$_SESSION['apply_js']['multiselest'] = 1;

// ===== HANDLE FORM SUBMISSION =====
if (isset($_POST['submit']) && $_POST['submit'] == 'save') {
  $url = API . "?mode=insertstocktransferreceipt&method=post";
  $response1 = file_post_contents($url, $_POST, 'insert');
  $res = json_decode($response1, true);

  $hasError = false;
  $errorMessages = array();
  $warningMessages = array();
  if (is_array($res)) {
    foreach ($res as $item) {
      if (is_array($item) && isset($item['status'])) {
        if ($item['status'] == 'warning') {
          if (isset($item['message'])) {
            $warningMessages[] = $item['message'];
          }
        } elseif ($item['status'] != 'sucess') {
          $hasError = true;
          if (isset($item['message'])) {
            $errorMessages[] = $item['message'];
          }
        }
      }
    }
  }

  if (!$hasError) {
    if (!empty($warningMessages)) {
      $_SESSION['stock_transfer_receipt_warning'] = implode(' | ', $warningMessages);
    }
    header("location:index.php?option=COM_INSERT_STOCK_TRANSFER_RECEIPT");
    exit;
  } else {
    $_SESSION['error'] = !empty($errorMessages) ? implode(' | ', $errorMessages) : 'Error saving stock transfer receipt. Please try again.';
  }
}

$stock_request_id = '';
$stock_transfer_id = '';

if (isset($_GET['stock_request_id']) && $_GET['stock_request_id'] != '') {
  $rid = encodeId($_GET['stock_request_id']);
  $rid = urldecode($rid);
  $rid = explode(':', $rid);
  $stock_request_id = $rid['1'];
}

if (isset($_GET['stock_transfer_id']) && $_GET['stock_transfer_id'] != '') {
  $tid = encodeId($_GET['stock_transfer_id']);
  $tid = urldecode($tid);
  $tid = explode(':', $tid);
  $stock_transfer_id = $tid['1'];
}

// ===== SINGLE API CALL - Gets stock transfers filtered by stock_request_id =====
$url = API . "?mode=getstocktransfers&method=get";
if ($stock_request_id != '') {
  $url .= "&stock_request_id=" . $stock_request_id;
}
if ($stock_transfer_id != '') {
  $url .= "&stock_transfer_id=" . $stock_transfer_id;
}
$result = json_decode(callAPI($url), true);

$stockRequests = $result['stock_requests'] ?? array();
$stockTransfers = $result['data'] ?? array();
$stockTransferDetails = $result['stock_transfer'] ?? array();
$stockTransferItems = $result['stock_transfer_items'] ?? array();
$totalAmount = $result['total_amount'] ?? 0;
?>

<link rel="stylesheet" href="assets/css/custom_insert_stock_transfer_receipt.css">
<script src="assets/js/custom_insert_stock_transfer_receipt.js"></script>

<div class="page-body">
  <div class="card shadow-sm border-0">
    <div class="card-header">
      <h5 class="mb-0">Stock Transfer Receipt</h5>
    </div>

    <div class="card-body p-4">
      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_SESSION['error']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>
      <?php if (!empty($_SESSION['stock_transfer_receipt_warning'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_SESSION['stock_transfer_receipt_warning']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['stock_transfer_receipt_warning']); ?>
      <?php endif; ?>


      <form method="get" action="index.php" class="row mb-4">
        <input type="hidden" name="option" value="COM_INSERT_STOCK_TRANSFER_RECEIPT">

        <div class="col-md-4 mb-3 w-full">
          <span class="fw-semibold">Stock Request</span>
          <select aria-label="Stock Request" name="stock_request_id" class="js-example-basic-single js-stock-request-select" data-auto-submit="1">
            <option value="">Select Stock Request</option>
            <?php
            if (count((array)$stockRequests) > 0) {
              foreach ($stockRequests as $row) {
                $selected = ($stock_request_id == $row['id']) ? 'selected' : '';
                $rid = deocdeId("id:".$row['id']);
                $displayText = htmlspecialchars($row['transaction_number']) . ' - ' . htmlspecialchars($row['ref_no']);
                echo "<option value='{$rid
}' {$selected
}>{$displayText
}</option>";
              }
            } else {
              echo "<option value=''>No Stock Requests Found</option>";
            }
            ?>
          </select>
        </div>

        <?php if ($stock_request_id != '') { ?>
        <div class="col-md-4 mb-3 w-full">
          <span class="fw-semibold">Stock Transfer</span>
          <select aria-label="Stock Transfer" name="stock_transfer_id" class="js-example-basic-single js-stock-transfer-select" data-auto-submit="1">
            <option value="">Select Stock Transfer</option>
            <?php
            if (count((array)$stockTransfers) > 0) {
              foreach ($stockTransfers as $row) {
                $selected = ($stock_transfer_id == $row['id']) ? 'selected' : '';
                $tid = deocdeId("id:".$row['id']);
                echo "<option value='{$tid
}' {$selected
}>{$row['transaction_number']
}</option>";
              }
            } else {
              echo "<option value=''>No Stock Transfers Found</option>";
            }
            ?>
          </select>
        </div>
        <?php } ?>
      </form>

      <?php if ($stock_transfer_id != '') { ?>
        <form method="POST" class="mt-4" id="stockTransferReceiptForm">
          <input type="hidden" name="stock_transfer_id" value="<?php echo htmlspecialchars($stock_transfer_id); ?>">
          <input type="hidden" name="stock_request_id" value="<?php echo htmlspecialchars($stock_request_id); ?>">
          <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($totalAmount); ?>">

          <div class="accordion mt-4" id="stockTransferReceiptAccordion">

          <div class="accordion-item mb-3 shadow-sm border-0">
            <h2 class="accordion-header" id="headingTransferDetails">
              <button class="accordion-button collapsed fw-bold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTransferDetails" aria-expanded="false" aria-controls="collapseTransferDetails">
                <i class="bi bi-file-earmark-text me-2"></i> Stock Transfer Details (<?php echo htmlspecialchars($stockTransferDetails['transaction_number'] ?? ''); ?>)
              </button>
            </h2>
            <div id="collapseTransferDetails" class="accordion-collapse collapse" aria-labelledby="headingTransferDetails">
              <div class="accordion-body">
                <div class="row mb-3">
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Transaction No</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars($stockTransferDetails['transaction_number'] ?? ''); ?></span>
                  </div>
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Transaction Date</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars(fromDate($stockTransferDetails['transaction_date'] ?? '')); ?></span>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Total Amount</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars($totalAmount); ?></span>
                  </div>
                  <div class="col-md-8">
                    <span class="text-muted fw-semibold d-block">Notes</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars($stockTransferDetails['notes'] ?? ''); ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="accordion-item shadow-sm border-0">
            <h2 class="accordion-header" id="headingTransferItems">
              <button class="accordion-button collapsed fw-bold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTransferItems" aria-expanded="false" aria-controls="collapseTransferItems">
                <i class="bi bi-boxes me-2"></i> Stock Transfer Items (<?php echo count($stockTransferItems); ?>)
              </button>
            </h2>
            <div id="collapseTransferItems" class="accordion-collapse collapse" aria-labelledby="headingTransferItems">
              <div class="accordion-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                      <tr>
                        <th scope="col" class="col-width-50">#</th>
                        <th scope="col" class="text-start">Item Name</th>
                        <th scope="col">UOM</th>
                        <th scope="col">Transfer Qty</th>
                        <th scope="col">Pending Qty</th>
                        <th scope="col">Price</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Receipt Qty</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($stockTransferItems)) { ?>
                        <?php $i = 1;
                        foreach ($stockTransferItems as $item) { ?>
                          <tr class="text-center">
                            <td><?php echo $i; ?></td>
                            <td class="text-start fw-semibold text-dark"><?php echo htmlspecialchars($item['item_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['purchase_uom'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['item_quantity'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars($item['pending_qty'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars($item['item_price'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars(floatval($item['pending_qty'] ?? 0) * floatval($item['item_price'] ?? 0)); ?></td>
                            <td>
                              <input type="hidden" name="items[<?php echo $i; ?>][item_id]" value="<?php echo htmlspecialchars($item['id']); ?>">
                              <input aria-label="Enter number" type="number" name="items[<?php echo $i; ?>][receipt_qty]" value="<?php echo htmlspecialchars($item['pending_qty'] ?? 0); ?>" step="any" min="0" max="<?php echo htmlspecialchars($item['pending_qty'] ?? 0); ?>" data-pending-qty="<?php echo htmlspecialchars($item['pending_qty'] ?? 0); ?>" class="form-control text-end receipt-qty-input" placeholder="0">
                            </td>
                          </tr>
                        <?php $i++;
                        }
                      } else { ?>
                        <tr>
                          <td colspan="8" class="text-center text-muted py-3">No pending receipt items found for this stock transfer.</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <?php if (!empty($stockTransferItems)) { ?>
            <div class="text-start mt-4">
              <button type="submit" name="submit" value="save" class="btn btn-primary px-4">
                <i class="ti ti-save me-2"></i>Submit
              </button>
            </div>
          <?php } else { ?>
            <div class="alert alert-info text-center py-3 mt-4" role="alert">
              <i class="bi bi-info-circle me-2"></i>
              <span>This stock transfer has no pending receipt quantity.</span>
            </div>
          <?php } ?>
        </form>
      <?php } else { ?>
        <div class="alert alert-info text-center py-4" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          <span>Select a Stock Request and Stock Transfer to begin the receipt process.</span>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<script src="assets/bower_components/sweetalert/js/sweetalert.min.js"></script>
