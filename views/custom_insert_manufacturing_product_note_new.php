<?php
$_SESSION['apply_js']['multiselest'] = 1;
if ($_POST['submit'] == 'save_status') {
  $rid = $_POST['work_order_id'];
  if ($_POST['action'] == 'start') {
    $_POST['work_log_status'] = '0';
  } elseif ($_POST['action'] == 'hold') {
    $_POST['work_log_status'] = '1';
  } elseif ($_POST['action'] == 'complete') {
    $_POST['work_log_status'] = '199';
  }
  unset($_POST['action'], $_POST['submit'], $_POST['work_order_id']);
  $_POST['updated_date'] = date('Y-m-d H:i:s');
  $_POST['updated_by'] = $_SESSION['userid'];
  $url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=manufacturing_work_orders&method=post";
  $response = file_post_contents($url, $_POST, 'update');
}

if ($_POST['save'] == 'save_logs') {
  $work_order_id = $_POST['work_order_id'];
  $return        = array();
  $softDeleteData = array(
    'is_active' => 1,
    'updated_by' => $_SESSION['userid'],
    'updated_date' => date('Y-m-d H:i:s')
  );
  $url = API . "?mode=updaterowtabledata&table=manufacturing_work_order_logs&method=post&rid={$work_order_id
}&fid=work_order_id";
  $response = file_post_contents($url, $softDeleteData, 'update');
  $return[] = json_decode($response, 1);
  $completed_qty = 0;
  if (count((array)$_POST['logs']) > 0) {
    foreach ($_POST['logs'] as $row) {
      $id = $row['id'] ?? '';
      $data = array(
        'work_order_id' => $work_order_id,
        'log_date' => $row['log_date'],
        'log_qty' => $row['log_qty'],
        'remarks' => $row['remarks'],
      );
      $completed_qty += $row['log_qty'] ?? 0;
      if ($id != '' && $id > 0) {
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $_SESSION['userid'];
        $data['is_active'] = '0';
        $url = API . "?mode=updaterowtabledata&rid={$id
}&table=manufacturing_work_order_logs&method=post&fid=id";
        $response = file_post_contents($url, $data, 'update');
        $return['update'][] = $response;
      } else {
        $data['current_status'] = '0';
        $data['is_active'] = '0';
        $data['status_date'] = date('Y-m-d H:i:s');
        $data['company_id'] = $_SESSION['company_id'];
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=manufacturing_work_order_logs&method=post";
        $response = file_post_contents($url, $data, 'insert');
        $return['insert'][] = $response;
      }
    }
  }
  $completed_array = array(
    'completed_qty' => $completed_qty,
    'updated_by' =>  $_SESSION['userid'],
    'updated_date' => date('Y-m-d H:i:s'),
  );

  $url = API . "?mode=updaterowtabledata&rid=".$work_order_id."&table=manufacturing_work_orders&method=post";
  $response = file_post_contents($url, $completed_array, 'update');
  $return[] = $response;

}

$work_order_id = '';
if ($_GET['work_order_id']) {
  $rid = encodeId($_GET['work_order_id']);
  $rid = urldecode($rid);
  $rid = explode(':', $rid);
  $work_order_id = $rid['1'];
}

// ===== FETCH LOGIC =====
$url = API . "?mode=getmanufacturingworkorderslogs&method=get&work_order_id=" . $work_order_id;
$result = json_decode(callAPI($url), true);
$workorders = $result['data'] ?? array();
$data = $result ?? array();
$plannedQty = floatval($data['work_order']['work_order_item_qty'] ?? 0);
?>

<link rel="stylesheet" href="assets/css/custom_insert_manufacturing_product_note_new.css">
<script src="assets/js/custom_insert_manufacturing_product_note_new.js" ></script>

<div class="page-body">
  <div class="card shadow-sm border-0">
    <div class="card-header">
      <h5 class="mb-0">Manufacturing Product Note</h5>
    </div>

    <div class="card-body p-4">
      <?php if (empty($logs)) { ?>
      <form method="get" action="index.php" class="row mb-4">
        <input type="hidden" name="option" value="COM_INSERT_MANUFACTURING_PRODUCT_NOTE_NEW">

        <div class="col-md-6 mb-3 w-full">
          <span class="fw-semibold">Work Order</span>
          <select aria-label="Work Order" name="work_order_id" class="js-example-basic-single js-work-order-select" data-auto-submit="1" required>
            <option value="">Select Work Order</option>
            <?php
            if (count((array)$workorders) > 0) {
              foreach ($workorders as $row) {
                $selected = ($work_order_id == $row['id']) ? 'selected' : '';
                $rid = deocdeId("id:".$row['id']);
                echo "<option value='{$rid
}'  {$selected
}>{$row['transaction_no']
} </option>";
              }
            } else {
              echo "<option value=''>No Work Orders Found</option>";
            }
            ?>
          </select>
        </div>
      </form>
      <?php } ?>
      <?php if ($work_order_id != ''  && !empty($data['work_order'])) { ?>
        <div class="accordion mt-4" id="workOrderAccordion">
          <!-- WORK ORDER DETAILS -->
          <div class="accordion-item mb-3 shadow-sm border-0">
            <h2 class="accordion-header" id="headingDetails">
              <button class="accordion-button collapsed  fw-bold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">
                <i class="bi bi-gear-wide-connected me-2"></i> Work Order Details (<?php echo htmlspecialchars($data['work_order']['transaction_no']); ?>)
              </button>
            </h2>
            <div id="collapseDetails" class="accordion-collapse collapse " aria-labelledby="headingDetails">
              <div class="accordion-body">
                <div class="row mb-3">
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Transaction No</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars($data['work_order']['transaction_no']); ?></span>
                  </div>
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Item Name</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars($data['work_order']['item_name']); ?></span>
                  </div>
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Planned Quantity</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars(($data['work_order']['work_order_item_qty'])); ?></span>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Completed Quantity</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars(($data['work_order']['completed_qty'])); ?></span>
                  </div>
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Planned Start Date</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars(fromDate($data['work_order']['planned_start_date'])); ?></span>
                  </div>
                  <div class="col-md-4">
                    <span class="text-muted fw-semibold d-block">Planned End Date</span>
                    <span class="fs-6 text-dark"><?php echo htmlspecialchars(fromDate($data['work_order']['planned_end_date'])); ?></span>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- WORK ORDER ITEMS -->
          <div class="accordion-item shadow-sm border-0">
            <h2 class="accordion-header" id="headingItems">
              <button class="accordion-button collapsed fw-bold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseItems" aria-expanded="false" aria-controls="collapseItems">
                <i class="bi bi-box-seam me-2"></i> Work Order Items (<?php echo count($data['work_order_items']); ?>)
              </button>
            </h2>
            <div id="collapseItems" class="accordion-collapse collapse" aria-labelledby="headingItems">
              <div class="accordion-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                      <tr>
                        <th scope="col" class="col-width-50">#</th>
                        <th scope="col" class='text-start'>Item Name</th>
                        <th scope="col">UOM</th>
                        <th scope="col">Work Order Qty</th>
                        <th scope="col">Stock Request Qty</th>
                        <th scope="col">Stok Issued Qty</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($data['work_order_items'])) { ?>
                        <?php $i = 1;
                        foreach ($data['work_order_items'] as $item) { ?>
                          <tr class="text-center">
                            <td><?php echo $i; ?></td>
                            <td class="text-start fw-semibold text-dark"><?php echo $item['item_name']; ?></td>
                            <td><?php echo $item['unit']; ?></td>
                            <td><?php echo $item['request_qty']; ?></td>
                            <td><?php echo $item['stock_request_qty']; ?></td>
                            <td><?php echo $item['stock_receipts_qty']; ?></td>
                          </tr>
                        <?php $i++;
                        }
                      } else { ?>
                        <tr>
                          <td colspan="6" class="text-center text-muted py-3">No items found.</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- WORK ORDER OPERATIONS -->
          <div class="accordion-item shadow-sm border-0 mt-3">
            <h2 class="accordion-header" id="headingOperations">
              <button class="accordion-button collapsed fw-bold text-primary bg-light" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseOperations"
                aria-expanded="false" aria-controls="collapseOperations">
                <i class="bi bi-hammer me-2"></i> Work Order Operations (<?php echo count($data['work_order_operations'] ?? []); ?>)
              </button>
            </h2>

            <div id="collapseOperations" class="accordion-collapse collapse" aria-labelledby="headingOperations">
              <div class="accordion-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                      <tr>
                        <th scope="col" class="col-width-50">#</th>
                        <th scope="col" class="text-start">Operation Name</th>
                        <th scope="col">Time (mins)</th>
                        <th scope="col">Manpower</th>
                        <th scope="col" class="text-start">Notes</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $operations = $data['work_order_operations'] ?? array();
                      if (!empty($operations)) {
                        $i = 1;
                        foreach ($operations as $op) { ?>
                          <tr class="text-center">
                            <td><?php echo $i; ?></td>
                            <td class="text-start fw-semibold text-dark"><?php echo htmlspecialchars($op['operation_name']); ?></td>
                            <td><?php echo htmlspecialchars($op['operation_time']); ?></td>
                            <td><?php echo htmlspecialchars($op['operation_manpower']); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($op['operation_note']); ?></td>
                          </tr>
                        <?php
                          $i++;
                        }
                      } else { ?>
                        <tr>
                          <td colspan="5" class="text-center text-muted py-3">No operations found.</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>

      <!-- ===== WORK ORDER LOG SECTION ===== -->
      <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h6 class="fw-bold text-primary mb-0">
            <i class="bi bi-clipboard2-check me-2"></i> Manufacturing Product Note
          </h6>


          <div class="btn-group">
            <form method="post" action="">
              <input type="hidden" name="work_order_id" value="<?php echo $work_order_id; ?>">
              <?php
              $status = $data['work_order']['work_log_status'];
              if ($status === null || $status === '') {
                // Not Started
                echo '<button type="submit" name="action" value="start" class="btn btn-success btn-sm px-3">
                      <i class="bi bi-play-circle me-1"></i> Start
                    </button>';
              } elseif ($status == 0) {
                // Started
                echo '<button type="submit" name="action" value="hold" class="btn btn-warning btn-sm px-3 text-white">
                      <i class="bi bi-pause-circle me-1"></i> Hold
                    </button>
                    <button type="submit" name="action" value="complete" class="btn btn-primary btn-sm px-3">
                      <i class="bi bi-check2-circle me-1"></i> Complete
                    </button>';
              } elseif ($status == 1) {
                // On Hold
                echo '<button type="submit" name="action" value="start" class="btn btn-success btn-sm px-3">
                      <i class="bi bi-play-circle me-1"></i> Resume
                    </button>
                    <button type="submit" name="action" value="complete" class="btn btn-primary btn-sm px-3">
                      <i class="bi bi-check2-circle me-1"></i> Complete
                    </button>';
              } elseif ($status == 199) {
                // Completed → No buttons
                echo '<span class="badge bg-success p-2"><i class="bi bi-check2-circle me-1"></i> Work Completed</span>';
              }
              ?>
              <input type="hidden" name="submit" value="save_status">
            </form>
          </div>
        </div>

        <div class="card-body">
          <?php if ($status === null || $status === '') { ?>
            <div class="alert alert-info small mb-4 d-flex align-items-center" role="alert">
              <i class="bi bi-info-circle me-2 fs-5 text-primary"></i>
              <span>
                Click <strong>Start</strong> to begin logging production progress.
                Logs can only be entered or modified while the work order is <strong>in progress</strong>.
              </span>
            </div>
          <?php } ?>
          <?php if ($status != null && $status != '') { ?>

            <div class="<?php echo ($status != '' && $status > 0) ? 'work-order-logs-readonly' : ''; ?>">
              <form id="workOrderLogsForm" method="post" action="" data-planned-qty="<?php echo $plannedQty; ?>">
                <input type="hidden" name="work_order_id" value="<?php echo $work_order_id; ?>">
                <div class="table-responsive">
                  <table class="table table-bordered align-middle" id="logTable">
                    <thead class="table-light text-center">
                      <tr>
                        <th scope="col" class="col-width-50">#</th>
                        <th scope="col">Date</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Remarks</th>
                        <th scope="col" class="col-width-50">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $logs = $data['work_order_logs'] ?? array();
                      if (!empty($logs)) {
                        $i = 1;
                        foreach ($logs as $log) { ?>
                          <tr class="text-center">
                            <td><?php echo $i; ?></td>
                            <td>
                              <input type="hidden" name="logs[<?php echo $i; ?>][id]" value="<?php echo htmlspecialchars($log['id']); ?>">
                              <input aria-label="Select date" type="date" name="logs[<?php echo $i; ?>][log_date]" class="form-control"
                                value="<?php echo htmlspecialchars($log['log_date']); ?>" required>
                            </td>
                            <td>
                              <input aria-label="Enter number" type="number" name="logs[<?php echo $i; ?>][log_qty]"
                                class="form-control text-end" step="any" min="0"
                                value="<?php echo htmlspecialchars($log['log_qty']); ?>" required>
                            </td>
                            <td>
                              <textarea aria-label="Text area" name="logs[<?php echo $i; ?>][remarks]" class="form-control" placeholder="Enter remarks..."><?php echo htmlspecialchars($log['remarks']); ?></textarea>
                            </td>
                            <td class="text-center">
                              <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                <i class="ti ti-trash"></i>
                              </button>
                            </td>
                          </tr>
                        <?php
                          $i++;
                        }
                      } else { ?>
                        <tr class="text-center">
                          <td>1</td>
                          <td>
                            <input aria-label="Logs[1][log Date]" type="date" name="logs[1][log_date]" class="form-control" required>
                          </td>
                          <td>
                            <input aria-label="Logs[1][log Qty]" type="number" name="logs[1][log_qty]" class="form-control text-end" step="any" min="0" required>
                          </td>
                          <td>
                            <textarea aria-label="Enter remarks..." name="logs[1][remarks]" class="form-control" placeholder="Enter remarks..."></textarea>
                          </td>
                          <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                              <i class="ti ti-trash"></i>
                            </button>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>

                  </table>
                </div>

                <?php
                $isEditable = ($status != '' && $status == 0);
                $disabled = $isEditable ? '' : 'disabled';
                $tooltip = $isEditable ? '' : 'data-bs-toggle="tooltip" title="Logs editable only when work order is started"';
                ?>

                <div class="d-flex justify-content-between mt-3">
                  <input type="hidden" name="save" value="save_logs">
                  <button type="submit" class="btn btn-primary px-4" <?php echo $disabled . ' ' . $tooltip; ?>>
                    <i class="ti ti-save me-1"></i>Submit
                  </button>
                  <button type="button" id="addRow" class="btn btn-outline-primary btn-sm" <?php echo $disabled . ' ' . $tooltip; ?>><i class="ti ti-plus-circle me-1"></i> Add More
                  </button>

                </div>
              </form>
            </div>
          <?php } ?>
        </div>
      </div>

      <?php } ?>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
