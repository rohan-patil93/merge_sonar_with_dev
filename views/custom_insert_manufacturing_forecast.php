<?php
$_SESSION['apply_js']['multiselest'] = 1;

$table_name = 'manufacturing_forecast';
$company_id = $_SESSION['company_id'] ?? 1;
$userid = $_SESSION['userid'] ?? 1;

$year = $_GET['year'] ?? '';
$data = [];
$forecastRows = [];
$toastMessage = '';
$error_message = '';
$workflowsteps = array();
$manufacturing_work_orders = array('current_status' => '');
$forecastStatus = '';
$forecastTransactionId = '';
$forecastNo = '';
$forecastDate = date('Y-m-d');
$disable = '';
$formdisable = '';
$remarks = '';

// ===== FETCH LOGIC =====
if ($year) {
    $url = API . "?mode=getmanufacturingforecastedata&method=get&year=" . $year;
    $result = json_decode(callAPI($url), true);
    $data = $result['data'] ?? [];
    $forecastRows = $result['forecast_rows'] ?? [];
    $forecastStatus = $result['forecast_status'] ?? '';
    $forecastTransactionId = $result['forecast_transaction_id'] ?? '';
    $forecastNo = $result['forecast_no'] ?? '';
    $forecastDate = $result['forecast_date'] ?? date('Y-m-d');
    $remarks = $result['remarks'] ?? '';
}

// ===== SAVE LOGIC =====
if (isset($_POST['submit']) && $_POST['submit'] == 'save') {

      $url = API."?mode=insertandupdatemanufacturingforecaste&method=post";
      $response1 = file_post_contents($url, $_POST, 'insert');
      $res = json_decode($response1, 1);
      if($res[0]['status'] == 'sucess'){
        header("Location: index.php?option=COM_INSERT_MANUFACTURING_FORECAST&year={$_POST['year']
}&saved=1");
        exit;
      }else{
          $error_message = $res[0]['message'] ?? 'Unable to save forecast.';
      }
}

if (!empty($_GET['saved'])) {
    $toastMessage = 'Forecast saved successfully!';
}
?>
<link rel="stylesheet" href="assets/css/custom_insert_manufacturing_forecast.css">
<script src="assets/js/custom_insert_manufacturing_forecast.js" ></script>
<?php
  if($forecastTransactionId != ''){
    $manufacturing_work_orders['id'] = $forecastTransactionId;
    $manufacturing_work_orders['current_status'] = $forecastStatus;
  }
  if( isset($manufacturing_work_orders['id']) && $manufacturing_work_orders['current_status'] !== '' && $manufacturing_work_orders['current_status'] !== null){
    $workflowsteps = getWorkflowStatus($table_name,$manufacturing_work_orders['id'],$manufacturing_work_orders['current_status']);
    echo $workflowsteps['current_status_ribbon'];
  }
  $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
  if(isset($manufacturing_work_orders['current_status']) && !in_array($manufacturing_work_orders['current_status'], (array)$check_array)){
    $disable = "disabled";
    $formdisable = "event_disabled";
  }
?>
<!-- ===== UI SECTION ===== -->
<div class="page-body" data-config-name="<?= htmlspecialchars(ucwords($_SESSION['config']['name']), ENT_QUOTES, 'UTF-8'); ?>" data-error-9059="<?= htmlspecialchars($_SESSION['error_message']['9059'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-toast-message="<?= htmlspecialchars($toastMessage, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
      <strong><?php echo htmlspecialchars($error_message); ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php } ?>
  <div class="card shadow-sm border-0">
    <div class="card-header ">
      <h5 class="mb-0"> Manufacturing Forecast</h5>
      <div class="card-header-right">
             <?php
                echo $workflowsteps['workflow_btn'] ?? '';
            ?>
            <?php if($year != "" && $forecastStatus == ST_APPROVED){ ?>
              <a href="index.php?option=COM_INSERT_MANUFACTURING_FIRM_ORDERS&year=<?php echo htmlspecialchars($year); ?>" class="btn btn-primary waves-effect waves-light">
                Create Firm Order
              </a>
            <?php } ?>
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light m-r-50">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body p-4">


      <form method="get" action="index.php" class="row mb-4">
        <input type="hidden" name="option" value="COM_INSERT_MANUFACTURING_FORECAST">
        <div class="col-md-4 w-full">
          <span class="fw-semibold">Select Year:</span>
          <select aria-label="Year" name="year" class="js-example-basic-single js-forecast-year-select" data-auto-submit="1">
            <option value="">Select Year</option>
            <?php
            $currentYear = date('Y')-2;
            for ($i = 0; $i <= 5; $i++) {
                $y = $currentYear + $i;
                echo "<option value='$y' ".($year==$y?'selected':'').">$y</option>";
            }
            ?>
          </select>
        </div>
      </form>

      <?php if ($year): ?>
      <form method="post" class="<?php echo $formdisable; ?>" id="manufacturingForecastForm">
        <input type="hidden" name="year" value="<?= htmlspecialchars($year); ?>">
        <?php echo $workflowsteps['breadcrumb'] ?? ''; ?>
        <div class="row mb-4">
          <div class="col-md-4 form-group">
            <span class="form-label">Forecast No</span>
            <input aria-label="Input field" type="text" class="form-control" value="<?php echo htmlspecialchars($forecastNo != '' ? $forecastNo : 'Auto Generated'); ?>" readonly>
          </div>
          <div class="col-md-4 form-group">
            <span class="form-label">Forecast Date <span class="text-danger">*</span></span>
            <input aria-label="Forecast Date" type="date" name="forecast_date" class="form-control" value="<?php echo htmlspecialchars($forecastDate); ?>" required <?php echo $disable; ?>>
          </div>
        </div>
        <div class="forecast-table-wrapper">
          <table class="table table-bordered align-middle text-center forecast-table">
            <thead class="table-light ">
              <tr class="fw-semibold">
                <th scope=\"col\" rowspan="2" class="forecast-header-item">Item</th>
                <th scope=\"col\" colspan="4">Q1 (Jan-Mar)</th>
                <th scope=\"col\" colspan="4">Q2 (Apr-Jun)</th>
                <th scope=\"col\" colspan="4">Q3 (Jul-Sep)</th>
                <th scope=\"col\" colspan="4">Q4 (Oct-Dec)</th>
                <th scope=\"col\" rowspan="2">Total</th>
              </tr>
              <tr class="text-muted small">
                <?php
                $headers = [
                  ['Jan','Feb','Mar','Q1'],
                  ['Apr','May','Jun','Q2'],
                  ['Jul','Aug','Sep','Q3'],
                  ['Oct','Nov','Dec','Q4']
                ];
                foreach($headers as $group) {
                    foreach($group as $h) { echo "<th scope=\"col\">$h</th>";
}
                }
                ?>
              </tr>
            </thead>

            <tbody>
              <?php
              $rows = $data ?? [];
              $grandTotals = array_fill(1, 12, 0);
              $grandTotal = 0;

              foreach ($rows as $row):
                  $pid = $row['product_id'] ?? $row['id'];
                  $pname = $row['item_name'];
                  echo "<tr>";
                  echo "<td class='text-start fw-semibold'>".htmlspecialchars($pname)."</td>";
                  $sum = 0;

                  for ($i=1; $i<=12; $i++) {
                      $val = isset($row["forecast_month_$i"]) ? floatval($row["forecast_month_$i"]) : null;
                      //  Decide border color based on value
                      if ($val === null || $val === '' || $val == 0) {
                          $borderClass = 'border border-danger'; // not entered
                      }elseif ($val > 0) {
                          $borderClass = 'border border-primary'; // entered positive forecast
                      } else {
                          $borderClass = 'border border-danger'; // fallback
                      }
                      echo "<td><input aria-label='Rows' type='number' name='rows[$pid][forecast_month_$i]'
                               value='$val' step='any' min='0'
                               class='form-control form-control-sm text-end forecast-month-input $borderClass' required></td>";
                      $sum += $val;
                      $grandTotals[$i] += $val;

                      if ($i % 3 == 0) {
                          $q = ($row["forecast_month_".($i-2)] ?? 0) +
                               ($row["forecast_month_".($i-1)] ?? 0) +
                               ($row["forecast_month_".$i] ?? 0);
                          echo "<td class='fw-bold bg-light'>".round($q,2)."</td>";
                      }
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
                for ($i=1; $i<=12; $i++) {
                    echo "<td>".round($grandTotals[$i],2)."</td>";
                    if ($i % 3 == 0) {
                        $qTotal = $grandTotals[$i-2] + $grandTotals[$i-1] + $grandTotals[$i];
                        echo "<td class='bg-light'>".round($qTotal,2)."</td>";
                    }
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
            <textarea aria-label="Enter forecast remarks or assumptions" name="remarks" id="forecast_remarks" class="form-control" rows="3" placeholder="Enter forecast remarks or assumptions"><?php echo htmlspecialchars($remarks); ?></textarea>
          </div>
        </div>

        <div class="mt-4 text-start manufacturing-submit-row">
          <button type="submit" name="submit" value="save" class="btn btn-primary px-4" <?php echo $disable; ?>>
            Save           </button>
          <span class="manufacturing-approval-warning">
            <i class="icofont icofont-warning-alt"></i>
            <span>
              <strong>Review before approval.</strong>
              Once this forecast is approved, forecast quantities cannot be changed. Please update all item quantities before submitting for approval.
            </span>
          </span>
        </div>
      </form>
      <?php endif; ?>

    </div>
  </div>
</div>
