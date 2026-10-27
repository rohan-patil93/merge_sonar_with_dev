 <?php
  $_SESSION['apply_js']['new_hr_payroll'] = 1;
  $pay_month = $_GET['pay_month'] ?? date('m', strtotime('first day of last month'));
  $pay_year  = $_GET['pay_year'] ?? date('Y', strtotime('first day of last month')) ;
  $url = API . "?mode=getPayrollDashboardSummary&pay_month=" . $pay_month . "&pay_year=" . $pay_year;
  $calurl = callAPI($url);
  $data = json_decode($calurl, 1);
  ?>
 <script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
 <div class="row mt-3">
   <div class="col-lg-7 col-md-12 col-sm-12 col-12">
     <h3 class="f-w-600 mb-0">Payroll Dashboard</h3>
     <p>Overview of payroll operations and compliance</p>
   </div>
   <div class="col-lg-5 text-end">
     <a href="index.php" class="btn btn-info"> Old DashBoard</a>
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
             <span class="f-12 f-w-600">Employees in Payroll</span>
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
             <span class="f-12 f-w-600">Gross Payroll (₹)</span>
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
             <span class="f-12 f-w-600">Total Deductions (*)</span>
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
             <span class="f-12 f-w-600">Pending Approvals</span>
             <h4 class="f-28 f-w-600">0</h4>
             <span class="f-14 f-w-600">Requires your action</span>
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
           <h3 class="f-20 f-w-600">Payroll Run Status <span class="f-10">(May 20XX)</span></h3>
         </div>
         <div class="main-box mt-4">
           <!-- STEPS -->
           <div class="steps">

             <div class="step completed">
               <div class="circle"><i class="fa fa-check"></i></div>
               <h4>Attendance Locked</h4>
               <p>09 May 20XX</p>
             </div>

             <div class="step completed">
               <div class="circle"><i class="fa fa-check"></i></div>
               <h4>Payroll Processed</h4>
               <p>10 May 20XX</p>
             </div>

             <div class="step <?php echo ($show_data_all == 'Y') ? 'completed' : 'active'; ?>">
               <div class="circle">
                 <?php echo ($show_data_all == 'Y') ? '<i class="fa fa-check"></i>' : '3'; ?>
               </div>
               <h4>Review & Approvals</h4>
               <p><?php echo ($show_data_all == 'Y') ? 'Completed' : 'In Progress'; ?></p>
             </div>

             <div class="step <?php echo ($show_data_all == 'Y') ? 'completed' : ''; ?>">
               <div class="circle">
                 <?php echo ($show_data_all == 'Y') ? '<i class="fa fa-check"></i>' : '4'; ?>
               </div>
               <h4>Payslips Generation</h4>
               <p><?php echo ($show_data_all == 'Y') ? 'Completed' : '12 May 20XX'; ?></p>
             </div>

             <div class="step <?php echo ($show_data_all == 'Y') ? 'completed' : ''; ?>">
               <div class="circle">
                 <?php echo ($show_data_all == 'Y') ? '<i class="fa fa-check"></i>' : '5'; ?>
               </div>
               <h4>Bank Transfer</h4>
               <p><?php echo ($show_data_all == 'Y') ? 'Completed' : '13 May 20XX'; ?></p>
             </div>

             <div class="step <?php echo ($show_data_all == 'Y') ? 'completed' : ''; ?>">
               <div class="circle">
                 <?php echo ($show_data_all == 'Y') ? '<i class="fa fa-check"></i>' : '6'; ?>
               </div>
               <h4>Statutory Filing</h4>
               <p><?php echo ($show_data_all == 'Y') ? 'Completed' : '15 May 20XX'; ?></p>
             </div>

           </div>
           <!-- CONTENT -->
           <div class="content-box mb-4">
             <div class="left-content">
               <span>Current Status</span>
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
           <h3 class="f-20 f-w-600">Payroll Summary <span class="f-10">(May 20XX)</span></h3>
         </div>
         <div class="table-responsive mt-1 height-380">
           <?php
            $componentSummary = $data['component_summary'] ?? [];
            ?>

           <table class="table table-borderless table-sm align-middle pedding-approvals f-12 mb-0">
             <?php foreach ($componentSummary as $row) { ?>
               <tr>
                 <th><?php echo ucwords(strtolower($row['component_code'])); ?></th>
                 <th class="text-end">₹ <?php echo number_format($row['amount'] ?? 0, 2); ?></th>
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
           <h3 class="f-20 f-w-600">Pending Actions</h3>
         </div>
         <div class="table-responsive mt-1 height-360">
           <table class="table table-borderless table-sm align-middle pedding-approvals">
             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="fa fa-calculator f-16 text-success f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">Reimbursements</td>
               <td>
                 <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">5
                 </span>
               </td>
             </tr>
             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="fa fa-calculator f-16 text-danger f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">Full & Final Settlements</td>
               <td>
                 <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">3
                 </span>
               </td>
             </tr>
             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center">
                   <i class="fa fa-calculator f-16 text-success f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">Variable Pay / Bonus</td>
               <td>
                 <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">2
                 </span>
               </td>
             </tr>
             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center">
                   <i class="fa fa-calculator f-16 text-primary f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">Payroll Approvals</td>
               <td>
                 <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">6
                 </span>
               </td>
             </tr>
             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-purple-50 f-14 f-w-600 text-center">
                   <i class="fa fa-calculator f-16 text-purple f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">Loan / Advances</td>
               <td>
                 <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">2
                 </span>
               </td>
             </tr>
           </table>
         </div>
         <!-- <a href="" class="text-primary f-14 f-w-600 d-block">View All</a> -->
       </div>
     </div>
   </div>
   <div class="col-lg-5 col-md-6 col-sm-6 col-12">
     <div class="card my-payslips">
       <div class="card-body mb-4">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600">Statutory Compliance <span class="f-10">(May 20XX)</span></h3>
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
               <th>Component</th>
               <th>Amount (₹)</th>
               <th>Status</th>
               <th>Due Date</th>
               <th>Paid On</th>
             </tr>

             <tr>
               <th>Provident Fund (PF)</th>
               <td>₹ <?php echo number_format($components['PF'] ?? 0, 2); ?></td>
               <td><label class="form-label label label-success">Paid</label></td>
               <td>-</td>
               <td>-</td>
             </tr>

             <tr>
               <th>ESI</th>
               <td>₹ <?php echo number_format($components['ESI'] ?? 0, 2); ?></td>
               <td><label class="form-label label label-success">Paid</label></td>
               <td>-</td>
               <td>-</td>
             </tr>

             <tr>
               <th>Professional Tax (PT)</th>
               <td>₹ <?php echo number_format($components['PT'] ?? 0, 2); ?></td>
               <td><label class="form-label label label-success">Paid</label></td>
               <td>-</td>
               <td>-</td>
             </tr>

             <tr>
               <th>TDS</th>
               <td>₹ <?php echo number_format($components['TDS'] ?? 0, 2); ?></td>
               <td><label class="form-label label label-success">Paid</label></td>
               <td>-</td>
               <td>-</td>
             </tr>

             <tr>
               <th>Gratuity</th>
               <td>₹ <?php echo number_format($components['GRATUITY'] ?? 0, 2); ?></td>
               <td><label class="form-label label label-success">Paid</label></td>
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
           <h3 class="f-20 f-w-600">Earnings vs Deductions Trend</h3>
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
           <h3 class="f-20 f-w-600">Payroll Reports</h3>
         </div>
         <div class="table-responsive mt-2 height-290">
           <table class="table table-borderless table-sm align-middle payroll-reports mb-0">

             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">
                 <a href="index.php?option=COM_VIEW_EMPLOYEE_CTC_REPORT" class="text-dark">
                   Employee's CTC Summary
                 </a>
               </td>
               <td>
                 <a href="index.php?option=COM_VIEW_EMPLOYEE_CTC_REPORT">
                   <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                 </a>
               </td>
             </tr>

             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">
                 <a href="index.php?option=COM_VIEW_EPF_ECR_REPORT" class="text-dark">
                   EPF ECR Summary
                 </a>
               </td>
               <td>
                 <a href="index.php?option=COM_VIEW_EPF_ECR_REPORT">
                   <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                 </a>
               </td>
             </tr>

             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">
                 <a href="index.php?option=COM_VIEW_EPF_SUMMARY" class="text-dark">
                   EPF Summary
                 </a>
               </td>
               <td>
                 <a href="index.php?option=COM_VIEW_EPF_SUMMARY">
                   <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                 </a>
               </td>
             </tr>

             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">
                 <a href="index.php?option=COM_VIEW_ESI_SUMMARY" class="text-dark">
                   ESI Summary
                 </a>
               </td>
               <td>
                 <a href="index.php?option=COM_VIEW_ESI_SUMMARY">
                   <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                 </a>
               </td>
             </tr>

             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="far fa-file-alt f-16 text-success f-w-600"></i>
                 </span>
               </td>
               <td class="f-12 f-w-600">
                 <a href="index.php?option=COM_VIEW_PT_SUMMARY" class="text-dark">
                   Professional Tax Summary
                 </a>
               </td>
               <td>
                 <a href="index.php?option=COM_VIEW_PT_SUMMARY">
                   <i class="ti-angle-right text-dark f-w-600 f-12"></i>
                 </a>
               </td>
             </tr>

           </table>
         </div>
         <!-- <a href="" class="text-primary f-14 f-w-600 d-block mt-2">View All Reports</a> -->
       </div>
     </div>
   </div>
   <div class="col-lg-8 col-md-12 col-sm-12 col-12">
     <div class="card my-payslips">
       <div class="card-body">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600">Recent Payroll Runs</h3>
         </div>
         <div class="table-responsive mt-4 height-250">
           <?php
            $allSummary = $data['all_summary'] ?? [];
            ?>

           <table class="table table-sm align-middle pedding-approvals f-12">
             <tr>
               <th>Payroll Month</th>
               <th>Employees</th>
               <th>Gross Payroll (₹)</th>
               <th>Net Payroll (₹)</th>
               <th>Status</th>
             </tr>

             <?php foreach ($allSummary as $row) { ?>
               <tr>
                 <td><?php echo date('M Y', mktime(0, 0, 0, $row['pay_month'], 1, $row['pay_year'])); ?></td>
                 <td><?php echo $row['employee_count'] ?? 0; ?></td>
                 <td>₹ <?php echo number_format($row['gross_salary'] ?? 0, 2); ?></td>
                 <td>₹ <?php echo number_format($row['net_payable'] ?? 0, 2); ?></td>
                 <td><label class="form-label label label-success">Completed</label></td>
               </tr>
             <?php } ?>
           </table>
         </div>
         <!-- <a href="" class="text-primary f-14 f-w-600 mt-3">View All Payroll Runs</a> -->
       </div>
     </div>
   </div>
   <div class="col-lg-4 col-md-6 col-sm-6 col-12">
     <div class="card">
       <div class="card-body">
         <div class="d-flex justify-content-between align-items-center">
           <h3 class="f-20 f-w-600">Important Announcements</h3>
         </div>
         <div class="table-responsive mt-3 height-250">
           <table class="table table-borderless table-sm align-middle payroll-reports mb-0 f-12">
             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="fa fa-exclamation-triangle f-16 text-danger f-w-600"></i>
                 </span>
               </td>
               <td>
                 <h6 class="m-0 f-14">3 Probation confirmations pending</h6>
                 <p class="p-0 f-12 text-muted">Review and confirm</p>
               </td>
               <td class="text-primary f-w-600">View</td>
             </tr>
             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="fa fa-user f-16 text-success f-w-600"></i>
                 </span>
               </td>
               <td>
                 <h6 class="m-0 f-14">5 Attendance regularizations pending</h6>
                 <p class="p-0 f-12 text-muted">Review and approve</p>
               </td>
               <td class="text-primary f-w-600">View</td>
             </tr>
             <tr>
               <td>
                 <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                   <i class="fa fa-exclamation-triangle f-16 text-warning f-w-600"></i>
                 </span>
               </td>
               <td class="f-12">
                 <h6 class="m-0 f-14">2 Documents expiring this month</h6>
                 <p class="p-0 f-12 text-muted">Take necessary action</p>
               </td>
               <td class="text-primary f-w-600">View</td>
             </tr>
           </table>
         </div>
         <!-- <a href="" class="text-primary f-14 f-w-600 d-block">View All Alerts</a> -->
       </div>
     </div>
   </div>
 </div>