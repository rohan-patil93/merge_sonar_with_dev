<?php
$_SESSION['apply_js']['multiselest'] = 1;

$table_name = 'manufacturing_firm_orders';
$company_id = $_SESSION['company_id'] ?? 1;
$userid = $_SESSION['userid'] ?? 1;

$year = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';

$data = [];
$toastMessage = '';
$error_message = '';
$info_message = '';
$workflowsteps = array();
$firm_order = array('current_status' => '');
$firmOrderId = '';
$firmOrderStatus = '';
$firmOrderNo = '';
$firmOrderDate = date('Y-m-d');
$remarks = '';
$disable = '';
$formdisable = '';

// ===== FETCH LOGIC =====
if ($year && $month) {
    $url = API . "?mode=getmanufacturingfirmordersdata&month=".$month."&year=" . $year;
    $result = json_decode(callAPI($url), true);
    $data = $result['data'] ?? [];
    $firmOrderId = $result['firm_order_id'] ?? '';
    $firmOrderStatus = $result['firm_order_status'] ?? '';
    $firmOrderNo = $result['firm_order_no'] ?? '';
    $firmOrderDate = $result['firm_order_date'] ?? date('Y-m-d');
    $remarks = $result['remarks'] ?? '';
    $info_message = $result['message'] ?? '';
}

// ===== SAVE LOGIC =====
if (isset($_POST['submit']) && $_POST['submit'] == 'save') {
      $url = API."?mode=insertandupdatemanufacturingfirmorder&method=post";
      $response1 = file_post_contents($url, $_POST, 'insert');
      $res = json_decode($response1, 1);
      if(($res['data']['0']['status'] ?? '') == 'sucess'){
        header("Location: index.php?option=COM_INSERT_MANUFACTURING_FIRM_ORDERS&year={$_POST['year']
}&month={$_POST['month']
}&saved=1");
        exit;
        }else{
          $error_message = $res['data']['0']['message'] ?? 'Unable to save firm order.';
        }
}

if (!empty($_GET['saved'])) {
    $toastMessage = 'Firm order saved successfully!';
}
?>
<link rel="stylesheet" href="assets/css/custom_insert_manufacturing_firm_orders.css">
<script src="assets/js/custom_insert_manufacturing_firm_orders.js" defer></script>

<!-- ===== UI SECTION ===== -->
<?php
  if($firmOrderId != ''){
    $firm_order['id'] = $firmOrderId;
    $firm_order['current_status'] = $firmOrderStatus;
  }
  if(isset($firm_order['id']) && $firm_order['current_status'] !== '' && $firm_order['current_status'] !== null){
    $workflowsteps = getWorkflowStatus($table_name,$firm_order['id'],$firm_order['current_status']);
    echo $workflowsteps['current_status_ribbon'];
  }
  $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK, ST_CALLBACK,'');
  if(isset($firm_order['current_status']) && !in_array($firm_order['current_status'], (array)$check_array)){
    $disable = "disabled";
    $formdisable = "event_disabled";
  }
?>
<div class="page-body" data-config-name="<?= htmlspecialchars(ucwords($_SESSION['config']['name']), ENT_QUOTES, 'UTF-8'); ?>" data-error-9059="<?= htmlspecialchars($_SESSION['error_message']['9059'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-toast-message="<?= htmlspecialchars($toastMessage, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
      <strong><?php echo htmlspecialchars($error_message); ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php } ?>
  <div class="card shadow-sm border-0">
    <div class="card-header ">
      <h5 class="mb-0"> Manufacturing Firm Order</h5>
      <div class="card-header-right table-card-header">
             <?php
                echo $workflowsteps['workflow_btn'] ?? '';
            ?>
            <?php if($year != "" && $month != "" && $firmOrderStatus == ST_APPROVED){ ?>
              <a href="index.php?option=COM_LIST_MANUFACTURING_WORK_ORDERS" class="btn btn-primary waves-effect waves-light">
                Work Orders
              </a>
            <?php } ?>
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light m-r-50">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body p-4">


      <form method="get" action="index.php" id="myForm" class="row mb-4 <?php echo $formdisable; ?>" >
        <input type="hidden" name="option" value="COM_INSERT_MANUFACTURING_FIRM_ORDERS">
        <div class="col-md-4 w-full">
          <span class="fw-semibold">Select Year <span class="text-danger">*</span></span>
          <select aria-label="Year" name="year" class="js-example-basic-single js-firm-orders-filter" data-auto-submit="1">
            <option value="">Select Year</option>
            <?php
            $currentYear = date('Y')-2;
            for ($i = 0; $i <= 10; $i++) {
                $y = $currentYear + $i;
                echo "<option value='$y' ".($year==$y?'selected':'').">$y</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-4 w-full">
          <span class="fw-semibold">Select Month <span class="text-danger">*</span></span>
          <select aria-label="Month" name="month" class="js-example-basic-single js-firm-orders-filter" data-auto-submit="1">

            <option value=""> Select Month</option>
            <?php
            for ($m = 1; $m <= 12; $m++) {
                $monthName = date('F', mktime(0, 0, 0, $m, 1));
                echo "<option value='$m' ".($month==$m?'selected':'').">$monthName</option>";
            }
            ?>
          </select>
        </div>
      </form>

      <?php if(!empty($data)){ ?>
      <form method="post" class="<?php echo $formdisable; ?>" id="manufacturingFirmOrderForm">
        <input type="hidden" name="year" value="<?= htmlspecialchars($year); ?>">
        <input type="hidden" name="month" value="<?= htmlspecialchars($month); ?>">
        <?php echo $workflowsteps['breadcrumb'] ?? ''; ?>
        <div class="row mb-4">
          <div class="col-md-4 form-group">
            <span class="form-label">Firm Order No</span>
            <input aria-label="Input field" type="text" class="form-control" value="<?php echo htmlspecialchars($firmOrderNo != '' ? $firmOrderNo : 'Auto Generated'); ?>" readonly>
          </div>
          <div class="col-md-4 form-group">
            <span class="form-label">Firm Order Date <span class="text-danger">*</span></span>
            <input aria-label="Firm Order Date" type="date" name="firm_order_date" class="form-control" value="<?php echo htmlspecialchars($firmOrderDate); ?>" required <?php echo $disable; ?>>
          </div>
        </div>
        <div class="forecast-table-wrapper">
          <table class="table table-bordered align-middle text-center forecast-table">
            <thead class="table-light ">
              <tr class="fw-semibold">
                <th scope=\"col\" rowspan="2" class="forecast-header-item">Item</th>
                <th scope=\"col\" colspan="4">Firm Orders</th>
              </tr>
              <tr class="text-muted small">
                <?php

                for ($m = 0; $m <= 2; $m++) {
                     echo "<th scope=\"col\" class='text-center'>".date('F Y', strtotime($year."-".$month."-01 +".$m." month"))."</th>";
                }
                ?>
                <th scope=\"col\">Total</th>
              </tr>
            </thead>
              <tbody>
              <?php
              $rows = $data ?? [];
              $grandTotals = array_fill(1, 3, 0);
              $grandTotal = 0;

              foreach ($rows as $row):
                  $pid = $row['product_id'];
                  $pname = $row['item_name'];
                  echo "<tr>";
                  echo "<td class='text-start fw-semibold'>".htmlspecialchars($pname)."</td>";
                  $sum = 0;

                  for ($i=1; $i<=3; $i++) {
                      $val = isset($row["firm_qty_$i"]) ? floatval($row["firm_qty_$i"]) : null;
                      //  Decide border color based on value
                      if ($val === null || $val === '' || $val == 0) {
                          $borderClass = 'border border-danger'; // not entered
                      }elseif ($val > 0) {
                          $borderClass = 'border border-primary'; // entered positive forecast
                      } else {
                          $borderClass = 'border border-danger'; // fallback
                      }
                      echo "<td><input aria-label='Rows' type='number' name='rows[$pid][firm_qty_$i]'
                               value='$val' step='any' min='0'
                               class='form-control form-control-sm text-end firm-order-input $borderClass' required></td>";
                      $sum += $val;
                      $grandTotals[$i] += $val;
                  }
                  echo "<td class='fw-bold bg-secondary bg-opacity-10'>".round($sum,2)."</td>";
                  echo "</tr>";
                  $grandTotal += $sum;
              endforeach;
              ?>
            </tbody>
            <tfoot class="fw-bold bg-light">
              <tr>
                <td>Total</td>
                <?php
                for ($i=1; $i<=3; $i++) {
                    echo "<td>".round($grandTotals[$i],2)."</td>";
                }
                echo "<td class='bg-secondary bg-opacity-10'>".round($grandTotal,2)."</td>";
                ?>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="row mt-4">
          <div class="col-md-12">
            <span class="form-label">Remarks</span>
            <textarea aria-label="Enter firm order remarks" name="remarks" id="firm_order_remarks" class="form-control" rows="3" placeholder="Enter firm order remarks"><?php echo htmlspecialchars($remarks); ?></textarea>
          </div>
        </div>

        <div class="mt-4 text-start manufacturing-submit-row">
          <button type="submit" name="submit" value="save" class="btn btn-primary px-4" <?php echo $disable; ?>>
            Save           </button>
          <span class="manufacturing-approval-warning">
            <i class="icofont icofont-warning-alt"></i>
            <span>
              <strong>Review before approval.</strong>
              Once this firm order is approved, firm order quantities cannot be changed. Please update all item quantities before submitting for approval.
            </span>
          </span>
        </div>
      </form>
       <?php }else{
         if($year != "" && $month != ""){
        ?>
          <div class="text-danger f-16 f-w-600 mt-5 text-center">
            <?php echo htmlspecialchars($info_message != '' ? $info_message : 'No firm order source data found for the selected period.'); ?>
          </div>
      <?php } ?>
      <?php } ?>

    </div>
  </div>
</div>

