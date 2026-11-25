 <?php
  $_SESSION['apply_js']['new_hr_payroll'] = 1;
  $pay_month = $_GET['pay_month'] ?? 3;
  $pay_year  = $_GET['pay_year'] ?? 2024;
  $url = API . "?mode=getPayrollDashboardSummary&pay_month=" . $pay_month . "&pay_year=" . $pay_year;
  $calurl = callAPI($url);
  $data = json_decode($calurl, 1);
  ?>
 <script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
 <div class="row mt-3">
   <div class="col-lg-7 col-md-12 col-sm-12 col-12">
     <h3 class="f-w-600 mb-0"><?= __('Payroll Dashboard'); ?></h3>
     <p><?= __('Overview of payroll operations and compliance'); ?></p>
   </div>

 </div>
 <div class="user-details-sec mt-3">
   <div class="details-box">
     <div class="card b-radius-10 mb-2">
       <div class="card-body">
         <div class="d-flex">
           <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
             <i class="far fa-user f-24 text-primary"></i>
           </span>
           <div class="d-inline-block">
             <span class="f-12 f-w-600"><?= __('Employees in Payroll'); ?></span>
             <h4 class="f-28 f-w-600"><?php echo $data['total_employees'] ?? 0; ?></h4>
             <i class="ti-arrow-up f-14 text-primary f-w-600"></i>
             <span class="f-14 text-dark f-w-600"> <?php echo $data['detail']['month'] . ' - ' . $data['detail']['year']; ?></span>
           </div>
         </div>
       </div>
     </div>
   </div>
   <div class="details-box">
     <div class="card b-radius-10 mb-2">
       <div class="card-body">
         <div class="d-flex">
           <span class="user-icon bg-green-50 p-2 b-radius-15 text-center me-3">
             <i class="fas fa-rupee-sign f-24 text-success"></i>
           </span>
           <div class="d-inline-block">
             <span class="f-12 f-w-600"><?= __('Gross Payroll (₹)'); ?></span>
             <h4 class="f-28 f-w-600">₹ <?php echo $data['gross_payroll'] ?? 0; ?></h4>
             <i class="ti-arrow-up f-14 text-primary f-w-600"></i>
             <span class="f-14 text-dark f-w-600"> <?php echo $data['detail']['month'] . ' - ' . $data['detail']['year']; ?></span>
           </div>
         </div>
       </div>
     </div>
   </div>
   <div class="details-box">
     <div class="card b-radius-10 mb-2">
       <div class="card-body">
         <div class="d-flex">
           <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center me-3">
             <i class="fas fa-briefcase f-24 text-purple"></i>
           </span>
           <div class="d-inline-block">
             <span class="f-12 f-w-600">Net Payroll ()</span>
             <h4 class="f-28 f-w-600">₹ <?php echo $data['net_payroll'] ?? 0; ?></h4>
             <i class="ti-arrow-up f-14 text-primary f-w-600"></i>
             <span class="f-14 text-dark f-w-600"> <?php echo $data['detail']['month'] . ' - ' . $data['detail']['year']; ?></span>
           </div>
         </div>
       </div>
     </div>
   </div>
   <div class="details-box">
     <div class="card b-radius-10 mb-2">
       <div class="card-body">
         <div class="d-flex">
           <span class="user-icon bg-red-50 p-2 b-radius-15 text-center me-3">
             <i class="fas fa-briefcase f-24 text-danger"></i>
           </span>
           <div class="d-inline-block">
             <span class="f-12 f-w-600"><?= __('Total Deductions (*)'); ?></span>
             <h4 class="f-28 f-w-600">₹ <?php echo $data['total_deductions'] ?? 0; ?></h4>
             <i class="ti-arrow-up f-14 text-primary f-w-600"></i>
             <span class="f-14 text-dark f-w-600"> <?php echo $data['detail']['month'] . ' - ' . $data['detail']['year']; ?></span>
           </div>
         </div>
       </div>
     </div>
   </div>
   <div class="details-box">
     <div class="card b-radius-10 mb-2">
       <div class="card-body">
         <div class="d-flex">
           <span class="user-icon bg-yellow-50 p-2 b-radius-15 text-center me-3">
             <i class="far fa-clock f-24 text-warning"></i>
           </span>
           <div class="d-inline-block">
             <span class="f-12 f-w-600"><?= __('Pending Approvals'); ?></span>
             <h4 class="f-28 f-w-600">0</h4>
             <span class="f-14 f-w-600"><?= __('Requires your action'); ?></span>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>
 <?php
  $show_data_all = 'Y'; // Y = all completed, N = current flow

  if ($show_data_all == 'Y') {
    $statusTitle = 'Completed';
    $statusDesc  = 'Payroll run has been completed successfully.';
    $buttonText  = 'Completed';
  } else {
    $statusTitle = 'Review & Approvals';
    $statusDesc  = 'Payroll has been processed. Please review the summary and approve to continue.';
    $buttonText  = 'Review Now';
  }
  ?>
 <div class="row mt-3">
   <div class="col-lg-6 col-md-12 col-sm-12 col-12 payroll-run-status">
     <div class="card">
       <div class="card-body">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600"><?= __('Payroll Run Status'); ?> <span class="f-10"><?= __('(May 20XX)'); ?></span></h3>
         </div>
         <div class="main-box mt-4">
           <!-- STEPS -->
           <div class="steps">

             <div class="step completed">
               <div class="circle"><i class="fa fa-check"></i></div>
               <h4><?= __('Attendance Locked'); ?></h4>
               <p><?= __('09 May 20XX'); ?></p>
             </div>

             <div class="step completed">
               <div class="circle"><i class="fa fa-check"></i></div>
               <h4><?= __('Payroll Processed'); ?></h4>
               <p><?= __('10 May 20XX'); ?></p>
             </div>

             <div class="step <?php echo ($show_data_all == 'Y') ? 'completed' : 'active'; ?>">
               <div class="circle">
                 <?php echo ($show_data_all == 'Y') ? '<i class="fa fa-check"></i>' : '3'; ?>
               </div>
               <h4><?= htmlspecialchars(__('Review & Approvals'), ENT_QUOTES, 'UTF-8'); ?></h4>
               <p><?php echo ($show_data_all == 'Y') ? 'Completed' : 'In Progress'; ?></p>
             </div>

             <div class="step <?php echo ($show_data_all == 'Y') ? 'completed' : ''; ?>">
               <div class="circle">
                 <?php echo ($show_data_all == 'Y') ? '<i class="fa fa-check"></i>' : '4'; ?>
               </div>
               <h4><?= __('Payslips Generation'); ?></h4>
               <p><?php echo ($show_data_all == 'Y') ? 'Completed' : '12 May 20XX'; ?></p>
             </div>

             <div class="step <?php echo ($show_data_all == 'Y') ? 'completed' : ''; ?>">
               <div class="circle">
                 <?php echo ($show_data_all == 'Y') ? '<i class="fa fa-check"></i>' : '5'; ?>
               </div>
               <h4><?= __('Bank Transfer'); ?></h4>
               <p><?php echo ($show_data_all == 'Y') ? 'Completed' : '13 May 20XX'; ?></p>
             </div>

             <div class="step <?php echo ($show_data_all == 'Y') ? 'completed' : ''; ?>">
               <div class="circle">
                 <?php echo ($show_data_all == 'Y') ? '<i class="fa fa-check"></i>' : '6'; ?>
               </div>
               <h4><?= __('Statutory Filing'); ?></h4>
               <p><?php echo ($show_data_all == 'Y') ? 'Completed' : '15 May 20XX'; ?></p>
             </div>

           </div>
           <!-- CONTENT -->
           <div class="content-box mb-4">
             <div class="left-content">
               <span><?= __('Current Status'); ?></span>
               <h2><?php echo $statusTitle; ?></h2>
               <p><?php echo $statusDesc; ?></p>
             </div>

             <button class="action-btn" id="actionBtn">
               <?php echo $buttonText; ?>
             </button>
           </div>

         </div>
       </div>
     </div>
   </div>
   <div class="col-lg-3 col-md-6 col-sm-6 col-12">
     <div class="card my-tasks">
       <div class="card-body">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600"><?= __('Payroll Summary'); ?> <span class="f-10"><?= __('(May 20XX)'); ?></span></h3>
         </div>
         <div class="table-responsive mt-1 height-380">
           <?php
            $componentSummary = $data['component_summary'] ?? [];
            ?>

           <table class="table table-borderless table-sm align-middle pedding-approvals f-12 mb-0">
             <?php foreach ($componentSummary as $row) { ?>
               <tr>
                 <th scope="col"><?php echo ucwords(strtolower($row['component_code'])); ?></th>
                 <th scope="col" class="text-end">₹ <?php echo number_format($row['amount'] ?? 0, 2); ?></th>
               </tr>
             <?php } ?>
           </table>
         </div>
       </div>
     </div>
   </div>
   <div class="col-lg-3 col-md-6 col-sm-6 col-12">
     <div class="card upcoming-events">
       <div class="card-body">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600"><?= __('Pending Actions'); ?></h3>
         </div>
         <div class="table-responsive mt-1 height-360">
           <div class="pedding-approvals d-flex flex-column gap-2">
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="fa fa-calculator f-16 text-success f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600"><?= __('Reimbursements'); ?></span>
                </div>
                <div>
                  <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">5</span>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="fa fa-calculator f-16 text-danger f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600"><?= htmlspecialchars(__('Full & Final Settlements'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div>
                  <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">3</span>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center mr-2">
                    <i class="fa fa-calculator f-16 text-success f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600"><?= __('Variable Pay / Bonus'); ?></span>
                </div>
                <div>
                  <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">2</span>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center mr-2">
                    <i class="fa fa-calculator f-16 text-primary f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600"><?= __('Payroll Approvals'); ?></span>
                </div>
                <div>
                  <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">6</span>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-purple-50 f-14 f-w-600 text-center mr-2">
                    <i class="fa fa-calculator f-16 text-purple f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600"><?= __('Loan / Advances'); ?></span>
                </div>
                <div>
                  <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">2</span>
                </div>
              </div>
            </div>
         </div>

       </div>
     </div>
   </div>
   <div class="col-lg-5 col-md-6 col-sm-6 col-12">
     <div class="card my-payslips">
       <div class="card-body mb-4">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600"><?= __('Statutory Compliance'); ?> <span class="f-10"><?= __('(May 20XX)'); ?></span></h3>
         </div>
         <div class="table-responsive mt-3 height-290">
           <?php
            $componentSummary = $data['component_summary'] ?? [];

            $components = [];
            foreach ($componentSummary as $row) {
              $components[$row['component_code']] = $row['amount'];
            }
            ?>

           <table class="table table-sm align-middle pedding-approvals f-12">
             <tr>
               <th scope="col"><?= __('Component'); ?></th>
               <th scope="col"><?= __('Amount (₹)'); ?></th>
               <th scope="col"><?= __('Status'); ?></th>
               <th scope="col"><?= __('Due Date'); ?></th>
               <th scope="col"><?= __('Paid On'); ?></th>
             </tr>

             <tr>
               <th scope="col"><?= __('Provident Fund (PF)'); ?></th>
               <td>₹ <?php echo number_format($components['PF'] ?? 0, 2); ?></td>
               <td><span class="form-label label label-success"><?= __('Paid'); ?></span></td>
               <td>-</td>
               <td>-</td>
             </tr>

             <tr>
               <th scope="col"><?= __('ESI'); ?></th>
               <td>₹ <?php echo number_format($components['ESI'] ?? 0, 2); ?></td>
               <td><span class="form-label label label-success"><?= __('Paid'); ?></span></td>
               <td>-</td>
               <td>-</td>
             </tr>

             <tr>
               <th scope="col"><?= __('Professional Tax (PT)'); ?></th>
               <td>₹ <?php echo number_format($components['PT'] ?? 0, 2); ?></td>
               <td><span class="form-label label label-success"><?= __('Paid'); ?></span></td>
               <td>-</td>
               <td>-</td>
             </tr>

             <tr>
               <th scope="col"><?= __('TDS'); ?></th>
               <td>₹ <?php echo number_format($components['TDS'] ?? 0, 2); ?></td>
               <td><span class="form-label label label-success"><?= __('Paid'); ?></span></td>
               <td>-</td>
               <td>-</td>
             </tr>

             <tr>
               <th scope="col"><?= __('Gratuity'); ?></th>
               <td>₹ <?php echo number_format($components['GRATUITY'] ?? 0, 2); ?></td>
               <td><span class="form-label label label-success"><?= __('Paid'); ?></span></td>
               <td>-</td>
               <td>-</td>
             </tr>
           </table>
         </div>
       </div>
     </div>
   </div>
   <div class="col-lg-4 col-md-6 col-sm-6 col-12">
     <div class="card my-payslips">
       <div class="card-body">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600"><?= __('Earnings vs Deductions Trend'); ?></h3>
         </div>
         <?php
$allSummary = $data['all_summary'] ?? [];
$lastSixSummary = array_slice($allSummary, -6);

$earningsTrendData = [];
$deductionsTrendData = [];
$xTicks = [];
$maxValue = 0;

foreach ($lastSixSummary as $index => $row) {
  $monthName = date('F', mktime(0, 0, 0, $row['pay_month'], 1, $row['pay_year']));

  $grossSalary = (float)($row['gross_salary'] ?? 0);
  $netPayable  = (float)($row['net_payable'] ?? 0);
  $deductions  = max($grossSalary - $netPayable, 0);

  $earningL = round($grossSalary / 100000, 2);
  $deductL  = round($deductions / 100000, 2);

  $earningsTrendData[] = [$index - 0.15, $earningL];
  $deductionsTrendData[] = [$index + 0.15, $deductL];

  $xTicks[] = [$index, $monthName];

  $maxValue = max($maxValue, $earningL, $deductL);
}

$yMax = $maxValue > 0 ? ceil($maxValue / 5) * 5 : 5;
?>
         <div id="earningsDeductionsTrend" class="demo-placeholder mt-3" style="height:320px;"></div>

<script>
$(document).ready(function() {
  var earningsData = <?php echo json_encode($earningsTrendData); ?>;
  var deductionsData = <?php echo json_encode($deductionsTrendData); ?>;
  var xTicks = <?php echo json_encode($xTicks); ?>;
  var yMax = <?php echo json_encode($yMax); ?>;

  if ($("#earningsDeductionsTrend").length && $.plot) {
    $.plot("#earningsDeductionsTrend", [
      {
        label: "Earnings",
        data: earningsData,
        bars: {
          show: true,
          barWidth: 0.25,
          align: "center"
        }
      },
      {
        label: "Deductions",
        data: deductionsData,
        bars: {
          show: true,
          barWidth: 0.25,
          align: "center"
        }
      }
    ], {
      xaxis: {
        ticks: xTicks,
        tickLength: 0,
        min: -0.5,
        max: xTicks.length - 0.5
      },
      yaxis: {
        min: 0,
        max: yMax,
        tickSize: 5,
        tickFormatter: function(val) {
          return val + "L";
        }
      },
      grid: {
        borderWidth: 1,
        labelMargin: 8,
        minBorderMargin: 0
      },
      legend: {
        position: "ne",
        margin: [10, 5]
      }
    });
  }
});
</script>
       </div>
     </div>
   </div>
   <div class="col-lg-3 col-md-6 col-sm-6 col-12">
     <div class="card">
       <div class="card-body">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600"><?= __('Payroll Reports'); ?></h3>
         </div>
         <div class="table-responsive mt-2 height-290">
           <div class="payroll-reports d-flex flex-column gap-2 mb-0">
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600">
                    <a href="index.php?option=COM_VIEW_EMPLOYEE_CTC_REPORT" class="text-dark">
                      <?= __('Employee\'s CTC Summary'); ?>
                    </a>
                  </span>
                </div>
                <div>
                  <a href="index.php?option=COM_VIEW_EMPLOYEE_CTC_REPORT">
                    <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                  </a>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600">
                    <a href="index.php?option=COM_VIEW_EPF_ECR_REPORT" class="text-dark">
                      <?= __('EPF ECR Summary'); ?>
                    </a>
                  </span>
                </div>
                <div>
                  <a href="index.php?option=COM_VIEW_EPF_ECR_REPORT">
                    <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                  </a>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600">
                    <a href="index.php?option=COM_VIEW_EPF_SUMMARY" class="text-dark">
                      <?= __('EPF Summary'); ?>
                    </a>
                  </span>
                </div>
                <div>
                  <a href="index.php?option=COM_VIEW_EPF_SUMMARY">
                    <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                  </a>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600">
                    <a href="index.php?option=COM_VIEW_ESI_SUMMARY" class="text-dark">
                      <?= __('ESI Summary'); ?>
                    </a>
                  </span>
                </div>
                <div>
                  <a href="index.php?option=COM_VIEW_ESI_SUMMARY">
                    <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                  </a>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                  </span>
                  <span class="f-12 f-w-600">
                    <a href="index.php?option=COM_VIEW_PT_SUMMARY" class="text-dark">
                      <?= __('Professional Tax Summary'); ?>
                    </a>
                  </span>
                </div>
                <div>
                  <a href="index.php?option=COM_VIEW_PT_SUMMARY">
                    <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                  </a>
                </div>
              </div>
            </div>
         </div>

       </div>
     </div>
   </div>
   <div class="col-lg-8 col-md-12 col-sm-12 col-12">
     <div class="card my-payslips">
       <div class="card-body">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600"><?= __('Recent Payroll Runs'); ?></h3>
         </div>
         <div class="table-responsive mt-4 height-250">
           <?php
            $allSummary = $data['all_summary'] ?? [];
            ?>

           <table class="table table-sm align-middle pedding-approvals f-12">
             <tr>
               <th scope="col"><?= __('Payroll Month'); ?></th>
               <th scope="col"><?= __('Employees'); ?></th>
               <th scope="col"><?= __('Gross Payroll (₹)'); ?></th>
               <th scope="col"><?= __('Net Payroll (₹)'); ?></th>
               <th scope="col"><?= __('Status'); ?></th>
             </tr>

             <?php foreach ($allSummary as $row) { ?>
               <tr>
                 <td><?php echo date('M Y', mktime(0, 0, 0, $row['pay_month'], 1, $row['pay_year'])); ?></td>
                 <td><?php echo $row['employee_count'] ?? 0; ?></td>
                 <td>₹ <?php echo number_format($row['gross_salary'] ?? 0, 2); ?></td>
                 <td>₹ <?php echo number_format($row['net_payable'] ?? 0, 2); ?></td>
                 <td><span class="form-label label label-success"><?= __('Completed'); ?></span></td>
               </tr>
             <?php } ?>
           </table>
         </div>

       </div>
     </div>
   </div>
   <div class="col-lg-4 col-md-6 col-sm-6 col-12">
     <div class="card">
       <div class="card-body">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600"><?= __('Important Announcements'); ?></h3>
         </div>
         <div class="table-responsive mt-3 height-250">
           <div class="payroll-reports d-flex flex-column gap-2 mb-0 f-12">
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="fa fa-exclamation-triangle f-16 text-danger f-w-600"></i>
                  </span>
                  <div>
                    <h6 class="m-0 f-14"><?= __('3 Probation confirmations pending'); ?></h6>
                    <p class="p-0 f-12 text-muted"><?= __('Review and confirm'); ?></p>
                  </div>
                </div>
                <div class="text-primary f-w-600"><?= __('View'); ?></div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="fa fa-user f-16 text-success f-w-600"></i>
                  </span>
                  <div>
                    <h6 class="m-0 f-14"><?= __('5 Attendance regularizations pending'); ?></h6>
                    <p class="p-0 f-12 text-muted"><?= __('Review and approve'); ?></p>
                  </div>
                </div>
                <div class="text-primary f-w-600"><?= __('View'); ?></div>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                  <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center mr-2">
                    <i class="fa fa-exclamation-triangle f-16 text-warning f-w-600"></i>
                  </span>
                  <div>
                    <h6 class="m-0 f-14"><?= __('2 Documents expiring this month'); ?></h6>
                    <p class="p-0 f-12 text-muted"><?= __('Take necessary action'); ?></p>
                  </div>
                </div>
                <div class="text-primary f-w-600"><?= __('View'); ?></div>
              </div>
            </div>
         </div>

       </div>
     </div>
   </div>
 </div>
