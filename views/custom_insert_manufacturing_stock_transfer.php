<?php
$_SESSION['apply_js']['multiselest'] = 1;

// ===== HANDLE FORM SUBMISSION =====
if (isset($_POST['action']) && $_POST['action'] == 'save') {
  $url = API . "?mode=insertstocktransfer&method=post";
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
      $_SESSION['stock_transfer_warning'] = implode(' | ', $warningMessages);
    }
    header("location:index.php?option=COM_INSERT_MANUFACTURING_STOCK_TRANSFER");
    exit;
  } else {
    $_SESSION['error'] = !empty($errorMessages) ? implode(' | ', $errorMessages) : 'Error saving stock transfer. Please try again.';
  }
}

$stock_request_id = '';

if (isset($_GET['stock_request_id']) && $_GET['stock_request_id'] != '') {
  $rid = encodeId($_GET['stock_request_id']);
  $rid = urldecode($rid);
  $rid = explode(':', $rid);
  $stock_request_id = $rid['1'];
}

$url = API . "?mode=getstockrequests&method=get";
if ($stock_request_id != '') {
  $url .= "&stock_request_id=" . $stock_request_id;
}
$result = json_decode(callAPI($url), true);

$stockRequests = $result['data'] ?? array();
$stockRequestDetails = $result['stock_request'] ?? array();
$stockRequestItems = $result['stock_request_items'] ?? array();
$totalAmount = $result['total_amount'] ?? 0;
$stockTransferHistory = $result['stock_transfer_history'] ?? array();
$workOrderNo = $result['work_order_no'] ?? ($stockRequestDetails['ref_no'] ?? '');
?>

<link rel="stylesheet" href="assets/css/custom_insert_manufacturing_stock_transfer.css">

<div class="page-body">
  <div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Manufacturing Stock Transfer</h5>
      <?php if ($stock_request_id != '') { ?>
        <div class="card-header-right">
          <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#stockTransferHistoryModal" title="Stock Transfer History" aria-label="Stock Transfer History">
            <i class="ti-time"></i>
          </button>
        </div>
      <?php } ?>
    </div>

    <div class="card-body p-4">
      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_SESSION['error']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>
      <?php if (!empty($_SESSION['stock_transfer_warning'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_SESSION['stock_transfer_warning']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['stock_transfer_warning']); ?>
      <?php endif; ?>

      <form method="get" action="index.php" class="row mb-4">
        <input type="hidden" name="option" value="COM_INSERT_MANUFACTURING_STOCK_TRANSFER">


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
      </form>

      <?php if ($stock_request_id != '') { ?>
        <form method="POST" class="mt-4" id="stockTransferForm">
          <input type="hidden" name="stock_request_id" value="<?php echo htmlspecialchars($stock_request_id); ?>">
          <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($totalAmount); ?>">

          <div class="accordion mt-4" id="stockTransferAccordion">
          <!-- STOCK REQUEST DETAILS -->
          <div class="accordion-item mb-3 shadow-sm border-0">
            <h2 class="accordion-header" id="headingRequestDetails">
              <button class="accordion-button collapsed fw-bold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRequestDetails" aria-expanded="false" aria-controls="collapseRequestDetails">
                <i class="ti ti-file me-2"></i> Stock Request Details (<?php echo htmlspecialchars($stockRequestDetails['transaction_number'] ?? ''); ?>)
              </button>
            </h2>
            <div id="collapseRequestDetails" class="accordion-collapse collapse" aria-labelledby="headingRequestDetails">
              <div class="accordion-body">
                <div class="row mb-3">
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Transaction No</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars($stockRequestDetails['transaction_number'] ?? ''); ?></span>
                  </div>
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Transaction Date</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars(fromDate($stockRequestDetails['transaction_date'] ?? '')); ?></span>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Total Amount</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars($totalAmount); ?></span>
                  </div>
                  <div class="col-md-8">
                    <span class="text-muted fw-semibold d-block">Notes</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars($stockRequestDetails['notes'] ?? ''); ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- STOCK REQUEST ITEMS -->
          <div class="accordion-item shadow-sm border-0">
            <h2 class="accordion-header" id="headingRequestItems">
              <button class="accordion-button collapsed fw-bold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRequestItems" aria-expanded="false" aria-controls="collapseRequestItems">
                <i class="ti ti-package  me-2"></i> Stock Request Items (<?php echo count($stockRequestItems); ?>)
              </button>
            </h2>
            <div id="collapseRequestItems" class="accordion-collapse collapse" aria-labelledby="headingRequestItems">
              <div class="accordion-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                      <tr>
                        <th scope="col" class="col-width-50">#</th>
                        <th scope="col" class="text-start">Item Name</th>
                        <th scope="col">SKU</th>
                        <th scope="col">UOM</th>
                        <th scope="col">Request Qty</th>
                        <th scope="col">Price</th>
                        <th scope="col">Amount</th>

                        <th scope="col">Pending Qty</th>
                        <th scope="col">Stock Transfer Qty</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($stockRequestItems)) { ?>
                        <?php $i = 1;
                        foreach ($stockRequestItems as $item) { ?>
                          <tr class="text-center">
                            <td><?php echo $i; ?></td>
                            <td class="text-start fw-semibold text-dark"><?php echo htmlspecialchars($item['item_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['sku'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($item['unit'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['item_quantity'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars($item['item_price'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars($item['item_amount'] ?? 0); ?></td>

                            <td><?php echo htmlspecialchars($item['pending_qty'] ?? 0); ?></td>
                            <td>
                              <input type="hidden" name="items[<?php echo $i; ?>][item_id]" value="<?php echo htmlspecialchars($item['id']); ?>">
                              <input aria-label="Enter number" type="number" name="items[<?php echo $i; ?>][stock_transfer_qty]" step="any" min="0" class="form-control text-end stock-transfer-qty-input" placeholder="0" data-item-qty="<?php echo htmlspecialchars($item['pending_qty'] ?? 0); ?>">
                            </td>
                          </tr>
                        <?php $i++;
                        }
                      } else { ?>
                        <tr>
                          <td colspan="8" class="text-center text-muted py-3">No items found.</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="text-start mt-4">
            <input type="hidden" name="action" value="save">
            <button type="submit"  value="save" class="btn btn-primary px-4">
              <i class="ti ti-save me-2"></i>Submit
            </button>
          </div>
        </form>

        <div class="modal fade" id="stockTransferHistoryModal" tabindex="-1" aria-labelledby="stockTransferHistoryModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="stockTransferHistoryModalLabel">
                  Stock Transfer History<?php echo $stockRequestDetails['transaction_number'] != '' ? ' - Request: ' . htmlspecialchars($stockRequestDetails['transaction_number']) : ''; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-2">
                <?php if (!empty($stockTransferHistory)) { ?>
                  <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                      <thead class="table-light text-center">
                        <tr>
                          <th scope="col" class="col-width-50">#</th>
                          <th scope="col" class="text-start">Transfer No</th>
                          <th scope="col">Date</th>
                          <th scope="col">Stock Request</th>
                          <th scope="col">Items</th>
                          <th scope="col">Total Transfer Qty</th>
                          <th scope="col">Total Amount</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $historyIndex = 1;
                        foreach ($stockTransferHistory as $transfer) {
                          $transferItems = $transfer['items'] ?? array();
                          $historyTotalAmount = floatval($transfer['calculated_total_amount'] ?? 0);
                          $transferIdForCollapse = intval($transfer['id'] ?? 0);
                          $historyRowId = 'stockTransferItems' . ($transferIdForCollapse > 0 ? $transferIdForCollapse : $historyIndex);
                          if ($historyTotalAmount <= 0) {
                            $historyTotalAmount = floatval($transfer['total_amount'] ?? 0);
                          }
                        ?>
                          <tr class="text-center table-secondary history-transfer-row" data-bs-toggle="collapse" data-bs-target="#<?php echo $historyRowId; ?>" aria-expanded="false" aria-controls="<?php echo $historyRowId; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <td><i class="ti ti-arrow-down me-1"></i><?php echo $historyIndex; ?></td>
                            <td class="text-start fw-semibold text-dark"><?php echo htmlspecialchars($transfer['transaction_number'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(fromDate($transfer['transaction_date'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($transfer['stock_request_no'] ?? ($transfer['ref_no'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($transfer['item_count'] ?? count($transferItems)); ?></td>
                            <td><?php echo htmlspecialchars($transfer['total_transfer_qty'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars($historyTotalAmount); ?></td>
                          </tr>
                          <tr class="collapse" id="<?php echo $historyRowId; ?>">
                            <td colspan="7" class="p-1">
                              <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                  <thead class="text-center">
                                    <tr>
                                      <th scope="col" class="text-start">Item Name</th>
                                      <th scope="col">SKU</th>
                                      <th scope="col">UOM</th>
                                      <th scope="col">Transfer Qty</th>

                                      <th scope="col">Price</th>
                                      <th scope="col">Amount</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php if (!empty($transferItems)) {
                                      foreach ($transferItems as $historyItem) { ?>
                                        <tr class="text-center">
                                          <td class="text-start"><?php echo htmlspecialchars($historyItem['item_name'] ?? ''); ?></td>
                                          <td><?php echo htmlspecialchars($historyItem['sku'] ?? '-'); ?></td>
                                          <td><?php echo htmlspecialchars($historyItem['unit'] ?? ($historyItem['purchase_uom'] ?? '')); ?></td>
                                          <td><?php echo htmlspecialchars($historyItem['item_quantity'] ?? 0); ?></td>

                                          <td><?php echo htmlspecialchars($historyItem['item_price'] ?? 0); ?></td>
                                          <td><?php echo htmlspecialchars($historyItem['item_amount'] ?? 0); ?></td>
                                        </tr>
                                      <?php }
                                    } else { ?>
                                      <tr>
                                        <td colspan="6" class="text-center text-muted py-2">No transfer items found.</td>
                                      </tr>
                                    <?php } ?>
                                  </tbody>
                                </table>
                              </div>
                            </td>
                          </tr>
                        <?php $historyIndex++;
                        } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } else { ?>
                  <div class="alert alert-info text-center m-3" role="alert">
                    <i class="fa fa-info-circle me-2"></i>
                    <span>No stock transfer history found for this stock request.</span>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      <?php } else { ?>
        <div class="alert alert-info text-center py-4" role="alert">
          <i class="fa fa-info-circle me-2"></i>
          <span>Select a Stock Request to begin the stock transfer process.</span>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<script src="assets/bower_components/sweetalert/js/sweetalert.min.js"></script>
<script src="assets/js/custom_insert_manufacturing_stock_transfer.js"></script>
