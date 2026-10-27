<?php
$_SESSION['apply_js']['new_hr_admin'] = 1;
$date = $_POST['date'] ?? date('Y-m-d');

$url = API . "?mode=togetHRAdminDashboardData&date=" . $date;

$apires = callAPI($url);
$result = json_decode($apires, true);

?>

<link rel="stylesheet" href="../assets/bower_components/chartist/css/chartist.css" type="text/css" media="all">
<link rel="stylesheet" href="../assets/bower_components/nvd3/css/nv.d3.css" type="text/css" media="all">
<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<div class="row mt-3">
  <div class="col-lg-8">
    <h3 class="f-w-600 mb-0">HR Command Center</h3>
    <p>Welcome back Admin!</p>
  </div>

  <div class="col-lg-4 text-end">
    <div class="d-inline-block align-top me-3">
      <div class="form-group">
        <form method="post" id="dashboardDateForm">
          <input aria-label="Date" type="date"
            name="date"
            class="form-control"
            value="<?php echo htmlspecialchars($date); ?>">
        </form>
      </div>
    </div>
    <script>
      $(document).ready(function() {
        $('#dashboardDateForm input[name="date"]').on('change', function() {
          $('#dashboardDateForm').submit();
        });
      });
    </script>

    <div class="modal fade" id="large-Modal" tabindex="-1" style="z-index: 1050; display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg text-start" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <h5>Default Modal</h5>
            <p>This is Photoshop's version of Lorem IpThis is Photoshop's version of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary waves-effect waves-light ">Save changes</button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="col-lg-2">
    <div class="card b-radius-10 mb-3">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-users f-24 text-primary"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Total Employees</span>
            <h4 class="f-30 f-w-600 mt-3 mb-3"><?php echo $result['total_employees'] ?? 0; ?></h4>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2">
    <div class="card b-radius-10 mb-3">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-green-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-users f-24 text-success"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Persent Today</span>
            <h4 class="f-30 f-w-600 mt-3 mb-3"><?php echo $result['present'] ?? 0; ?></h4>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2">
    <div class="card b-radius-10 mb-3">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-red-50 p-2 b-radius-15 text-center me-3">
            <i class="far fa-user f-24 text-danger"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Absent Today</span>
            <h4 class="f-30 f-w-600 mt-3 mb-3"><?php echo $result['absent'] ?? 0; ?></h4>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2">
    <div class="card b-radius-10 mb-3">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-yellow-50 p-2 b-radius-15 text-center me-3">
            <i class="far fa-user f-24 text-warning"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">On Leave</span>
            <h4 class="f-30 f-w-600 mt-3 mb-3"><?php echo $result['on_leave'] ?? 0; ?></h4>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2">
    <div class="card b-radius-10 mb-3">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-users f-24 text-primary"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">New Joiners<span class="f-8">(This Month)</span></span>
            <h4 class="f-30 f-w-600 mt-3 mb-3"><?php echo $result['count_new_joiners'] ?? 0; ?></h4>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2">
    <div class="card b-radius-10 mb-3">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-red-50 p-2 b-radius-15 text-center me-3">
            <i class="far fa-user f-24 text-danger"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Attrition<span class="f-8">(This Month)</span></span>
            <h4 class="f-30 f-w-600 mt-3 mb-3"><?php echo $result['attendance_pct'] ?? 0; ?></h4>

          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="row mt-3">
  <div class="col-lg-9">
    <div class="row">
      <div class="col-lg-6">
        <div class="card mb-3">
          <div class="card-body" style="height:520px;">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="f-20 f-w-600">Headcount Trend</h3>

              <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                <a class="dropdown-item waves-light waves-effect" href="#">Action</a>
                <a class="dropdown-item waves-light waves-effect" href="#">Another action</a>
                <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
              </div>
            </div>
            <div class="mt-3">
              <?php
              $headcountTrend = $result['employee_headcount_last_6_months'] ?? [];

              $chartLabels = [];
              $chartSeries = [];

              if (count((array)$headcountTrend) > 0) {
                foreach ($headcountTrend as $trend) {
                  $chartLabels[] = $trend['month'] ?? '';
                  $chartSeries[] = (int)($trend['count'] ?? 0);
                }
              }
              ?>

              <div id="headcountTrendChart"></div>

              <script>
                document.addEventListener("DOMContentLoaded", function() {

                  var labels = <?php echo json_encode($chartLabels); ?>;
                  var series = <?php echo json_encode($chartSeries); ?>;

                  if (document.querySelector('#headcountTrendChart') && series.length > 0) {
                    new Chartist.Line('#headcountTrendChart', {
                      labels: labels,
                      series: [series]
                    }, {
                      fullWidth: true,
                      height: '420px',
                      low: 0,
                      showArea: true,
                      showPoint: true,
                      lineSmooth: true,
                      axisX: {
                        showGrid: true
                      },
                      axisY: {
                        onlyInteger: true,
                        offset: 45
                      },
                      chartPadding: {
                        top: 20,
                        right: 20,
                        bottom: 10,
                        left: 0
                      }
                    });
                  }

                });
              </script>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card mb-3">
          <div class="card-body" style="height:520px;">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="f-20 f-w-600">Department wise Headcount</h3>
            </div>
            <div class="mt-1">
              <?php
              $departmentWiseHeadcount = $result['department_wise_headcount'] ?? [];

              $departmentChartData = [];

              if (count((array)$departmentWiseHeadcount) > 0) {

                usort($departmentWiseHeadcount, function ($a, $b) {
                  return (int)$b['headcount'] - (int)$a['headcount'];
                });

                $othersCount = 0;

                foreach ($departmentWiseHeadcount as $key => $department) {

                  if ($key < 6) {

                    $departmentChartData[] = [
                      'label' => $department['department_name'] ?? '',
                      'value' => (int)($department['headcount'] ?? 0)
                    ];
                  } else {

                    $othersCount += (int)($department['headcount'] ?? 0);
                  }
                }

                if ($othersCount > 0) {
                  $departmentChartData[] = [
                    'label' => 'Others',
                    'value' => $othersCount
                  ];
                }
              }
              ?>

              <div id="departmentHeadcountChart" style="height:325px;">
                <svg></svg>
              </div>
              <?php
              $departmentWiseHeadcount = $result['department_wise_headcount'] ?? [];

              $totalHeadcount = 0;
              foreach ((array)$departmentWiseHeadcount as $department) {
                $totalHeadcount += (int)($department['headcount'] ?? 0);
              }
              ?>

              <div class="mt-2" style="max-height:120px; overflow-y:auto;">
                <table class="table table-borderless table-sm f-12 mb-0">
                  <?php if (count((array)$departmentWiseHeadcount) > 0) { ?>
                    <?php foreach ($departmentWiseHeadcount as $department) {
                      $departmentName = $department['department_name'] ?? '-';
                      $headcount = (int)($department['headcount'] ?? 0);
                      $percent = 0;

                      if ($totalHeadcount > 0) {
                        $percent = round(($headcount / $totalHeadcount) * 100, 1);
                      }
                    ?>
                      <tr>
                        <th scope="col"><?php echo htmlspecialchars($departmentName); ?></th>
                        <th scope="col" class="text-end f-w-600">
                          <?php echo $headcount; ?>
                          <span class="text-muted">(<?php echo $percent; ?>%)</span>
                        </th>
                      </tr>
                    <?php } ?>
                  <?php } else { ?>
                    <tr>
                      <td class="text-center text-muted">No department data found</td>
                    </tr>
                  <?php } ?>
                </table>
              </div>
              <script>
                document.addEventListener("DOMContentLoaded", function() {

                  var chartData = <?php echo json_encode($departmentChartData); ?>;

                  d3.select('#departmentHeadcountChart svg').html('');

                  nv.addGraph(function() {

                    var chart = nv.models.pieChart()
                      .x(function(d) {
                        return d.label;
                      })
                      .y(function(d) {
                        return d.value;
                      })
                      .donut(true)
                      .donutRatio(0.45)
                      .showLabels(true)
                      .labelType("percent")
                      .growOnHover(false);

                    chart.legend.maxKeyLength(20);

                    d3.select('#departmentHeadcountChart svg')
                      .datum(chartData)
                      .transition()
                      .duration(350)
                      .call(chart);

                    nv.utils.windowResize(chart.update);

                    return chart;
                  });

                });
              </script>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 mt-3">
        <div class="card mb-3"  style="min-height: 424px;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="f-20 f-w-600">Pending Approvals</h3>
            </div>
            <div class="table-responsive mt-3 height-300">
              <?php
              $pendingApprovals = $result['pending_approvals_by_table'] ?? [];

              function formatApprovalLabel($tableName)
              {
                if (empty($tableName)) {
                  return 'Unknown Approval';
                }

                $tableName = str_replace('attendenace', 'attendance', $tableName);

                // Convert table name to readable text
                $label = str_replace('_', ' ', $tableName);
                $label = ucwords($label);

                $removeWords = [
                  'Employee ',
                  'Emp ',
                  'Transaction ',
                  'Data',
                ];

                $label = str_replace($removeWords, '', $label);

                return trim($label);
              }

              $approvalStyles = [
                ['bg' => 'bg-green-50',  'text' => 'text-success'],
                ['bg' => 'bg-yellow-50', 'text' => 'text-warning'],
                ['bg' => 'bg-blue-50',   'text' => 'text-primary'],
                ['bg' => 'bg-purple-50', 'text' => 'text-purple'],
                ['bg' => 'bg-red-50',    'text' => 'text-danger'],
              ];
              ?>

              <table class="table table-borderless table-sm align-middle pedding-approvals" role="presentation">
                <?php if (count((array)$pendingApprovals) > 0) { ?>
                  <?php foreach ($pendingApprovals as $key => $approval) {
                    $tableName = $approval['transaction_table'] ?? '';
                    $pendingCount = $approval['pending_count'] ?? 0;
                    $label = formatApprovalLabel($tableName);
                    $style = $approvalStyles[$key % count($approvalStyles)];
                  ?>
                    <tr>
                      <td>
                        <span class="user-icon-40 b-radius-15 <?php echo $style['bg']; ?> f-14 f-w-600 text-center">
                          <i class="far fa-user f-16 <?php echo $style['text']; ?> f-w-600"></i>
                        </span>
                      </td>

                      <td class="f-12 f-w-600">
                        <?php echo htmlspecialchars($label); ?>
                      </td>

                      <td>
                        <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">
                          <?php echo (int)$pendingCount; ?>
                        </span>
                      </td>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted f-12">
                      No pending approvals found
                    </td>
                  </tr>
                <?php } ?>
              </table>
            </div>

          </div>
        </div>
      </div>
      <div class="col-lg-3 mt-3">
        <div class="card mb-3" style="min-height: 424px;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="f-20 f-w-600">Leave Summary <span class="f-10 f-w-600 text-muted">(May 20xx)</span></h3>
            </div>
            <div class="table-responsive mt-3 height-300">
              <?php
              $leaveSummary = $result['leave_summary'] ?? [];
              ?>

              <table class="table table-borderless table-sm align-middle pedding-approvals f-12">
                <tr class="border-bottom">
                  <th scope="col">Leave Type</th>
                  <th scope="col">Utilized</th>
                  <th scope="col">Balance</th>
                </tr>

                <?php if (count((array)$leaveSummary) > 0) { ?>
                  <?php foreach ($leaveSummary as $leave) {
                    $leaveTypeName = $leave['leave_type_name'] ?? '-';
                    $allowedDays = (float)($leave['allowed_leave_days'] ?? 0);
                    $utilized = (float)($leave['utilized'] ?? 0);
                    $balance = $allowedDays - $utilized;
                  ?>
                    <tr>
                      <th scope="col"><?php echo htmlspecialchars($leaveTypeName); ?></th>
                      <td><?php echo number_format($utilized, 2); ?></td>
                      <td><?php echo number_format($balance, 2); ?></td>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted">
                      No leave summary found
                    </td>
                  </tr>
                <?php } ?>
              </table>
            </div>

          </div>
        </div>
      </div>
      <div class="col-lg-3 mt-3">
        <div class="card mb-3" style="min-height: 424px;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="f-20 f-w-600">New Joiners</h3>
            </div>
            <div class="table-responsive mt-3 height-300">
              <?php
              $newJoiners = $result['new_joiners'] ?? [];
              ?>

              <table class="table table-borderless table-sm align-middle pedding-approvals f-12">
                <?php if (count((array)$newJoiners) > 0) { ?>
                  <?php foreach ($newJoiners as $joiner) {
                    $employeeName = $joiner['display_name'] ?? '-';
                    $joiningDate = $joiner['joining_date'] ?? '';
                    $image = $joiner['image'] ?? '';

                    if ($image != '') {
                      $imagePath = 'uploads/all/' . $image;
                    } else {
                      $imagePath = 'uploads/all/1.jpg';
                    }

                    $formattedJoiningDate = '-';
                    if ($joiningDate != '') {
                      $formattedJoiningDate = date('d M Y', strtotime($joiningDate));
                    }
                  ?>
                    <tr>
                      <td>
                        <img src="<?php echo $imagePath; ?>" alt="" class="img-radius img-30 align-top">
                      </td>

                      <td>
                        <h6 class="f-w-600 f-12 mb-0">
                          <?php echo htmlspecialchars($employeeName); ?>
                        </h6>
                        <span class="text-muted">
                          <?php echo htmlspecialchars($joiner['emp_code'] ?? ''); ?>
                        </span>
                      </td>

                      <th scope="col" class="white-space-nowrap">
                        <?php echo $formattedJoiningDate; ?>
                      </th>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted f-12">
                      No new joiners found
                    </td>
                  </tr>
                <?php } ?>
              </table>
            </div>

          </div>
        </div>
      </div>
      <div class="col-lg-3 mt-3">
        <div class="card mb-3" style="min-height: 424px;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="f-20 f-w-600">Upcoming Exits</h3>
            </div>
            <div class="table-responsive mt-3 height-300">
              <?php
              $upcomingExits = $result['upcoming_exits'] ?? [];
              ?>

              <table class="table table-borderless table-sm align-middle pedding-approvals f-12">
                <?php if (count((array)$upcomingExits) > 0) { ?>
                  <?php foreach ($upcomingExits as $exit) {
                    $employeeName = $exit['display_name'] ?? '-';
                    $exitDate = $exit['last_working_date'] ?? ($exit['resignation_date'] ?? '');
                    $image = $exit['image'] ?? '';

                    if ($image != '') {
                      $imagePath = 'uploads/all/' . $image;
                    } else {
                      $imagePath = 'uploads/all/1.jpg';
                    }

                    $formattedExitDate = '-';
                    if ($exitDate != '') {
                      $formattedExitDate = date('d M Y', strtotime($exitDate));
                    }
                  ?>
                    <tr>
                      <td>
                        <img src="<?php echo $imagePath; ?>" alt="" class="img-radius img-30 align-top">
                      </td>

                      <td>
                        <h6 class="f-w-600 f-12 mb-0">
                          <?php echo htmlspecialchars($employeeName); ?>
                        </h6>
                        <span class="text-muted">
                          <?php echo htmlspecialchars($exit['emp_code'] ?? ''); ?>
                        </span>
                      </td>

                      <th scope="col" class="white-space-nowrap">
                        <?php echo $formattedExitDate; ?>
                      </th>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted f-12">
                      No upcoming exits found
                    </td>
                  </tr>
                <?php } ?>
              </table>
            </div>

          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card recent-acknowledgement mb-0 mt-3">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="f-20 f-w-600">Recent Announcements</h3>
            </div>
            <div class="table-responsive mt-3 height-300">
              <?php
              $recentAnnouncements = $result['recent_announcements'] ?? [];
              ?>

              <table class="table table-borderless table-sm align-middle pedding-approvals f-12">
                <?php if (count((array)$recentAnnouncements) > 0) { ?>
                  <?php foreach ($recentAnnouncements as $announcement) {
                    $heading = $announcement['announcement_heading'] ?? '-';
                    $message = $announcement['announcement_message'] ?? '';
                    $startDate = $announcement['start_date'] ?? '';

                    $formattedDate = '-';
                    if ($startDate != '') {
                      $formattedDate = date('d M Y', strtotime($startDate));
                    }
                  ?>
                    <tr>
                      <td>
                        <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center me-3">
                          <i class="fa fa-volume-up f-16 text-primary f-w-600"></i>
                        </span>
                      </td>

                      <td>
                        <h6 class="f-w-600 f-12 mb-0">
                          <?php echo htmlspecialchars($heading); ?>
                        </h6>
                        <span class="text-muted">
                          <?php echo nl2br(htmlspecialchars($message)); ?>
                        </span>
                      </td>

                      <th scope="col" class="white-space-nowrap">
                        <?php echo $formattedDate; ?>
                      </th>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted f-12">
                      No announcements found
                    </td>
                  </tr>
                <?php } ?>
              </table>
            </div>

          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card mb-3 mt-3">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="f-20 f-w-600">Attendance Heatmap <span class="f-10 f-w-600 text-muted"> (<?php echo date('M Y'); ?>)</span></h3>

              <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                <a class="dropdown-item waves-light waves-effect" href="#">Action</a>
                <a class="dropdown-item waves-light waves-effect" href="#">Another action</a>
                <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
              </div>
            </div>
            <?php
            $attendanceLast6Months = $result['attendance_last_6_months'] ?? [];

            $attendanceChartData = [];
            $attendanceTicks = [];

            if (count((array)$attendanceLast6Months) > 0) {
              $i = 1;
              foreach ($attendanceLast6Months as $attendance) {
                $attendanceChartData[] = [$i, (int)($attendance['total_present'] ?? 0)];
                $attendanceTicks[] = [$i, $attendance['month'] ?? ''];
                $i++;
              }
            }
            ?>

            <div id="attendanceHeatmapChart" class="demo-placeholder mt-3" style="height:330px;"></div>

            <script>
              $(document).ready(function() {

                var attendanceData = <?php echo json_encode($attendanceChartData); ?>;
                var attendanceTicks = <?php echo json_encode($attendanceTicks); ?>;

                if ($("#attendanceHeatmapChart").length && attendanceData.length > 0) {
                  $.plot("#attendanceHeatmapChart", [{
                    data: attendanceData,
                    bars: {
                      show: true,
                      barWidth: 0.6,
                      align: "center",
                      fill: true,
                      fillColor: "rgba(255, 99, 99, 0.4)",
                      lineWidth: 2
                    },
                    color: "#ff4d4d"
                  }], {
                    xaxis: {
                      ticks: attendanceTicks,
                      tickLength: 0
                    },
                    yaxis: {
                      min: 0,
                      tickDecimals: 0
                    },
                    grid: {
                      borderWidth: 2,
                      borderColor: "#555",
                      tickColor: "#ddd",
                      hoverable: true,
                      clickable: true
                    },
                    legend: {
                      show: false
                    }
                  });
                }

              });
            </script>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Shortcuts</h3>
        </div>
        <div class="row mt-4 shortcuts">

          <div class="col-lg-4 text-center mb-3">
            <a href="index.php?option=COM_LIST_EMPLOYEES">
            <span class="user-icon bg-blue-50 p-2 b-radius-15 d-block">
              <i class="far fa-user f-20 text-primary"></i>
            </span>
            <strong class="d-block mt-2 f-12 f-w-600">Employee Directory</strong>
            </a>
          </div>
          <div class="col-lg-4 text-center mb-3">
            <a href="index.php?option=COM_LIST_REQUEST_WORK_FROM_HOME">
            <span class="user-icon bg-green-50 p-2 b-radius-15 d-block">
              <i class="fa fa-users f-20 text-success"></i>
            </span>
            <strong class="d-block mt-2 f-12 f-w-600">Work From Home</strong>
          </a>
          </div>
          <div class="col-lg-4 text-center mb-3">
            <a href="index.php?option=COM_EMPLOYEE_MONTHLY_ATTENDANCE_REPORT">
            <span class="user-icon bg-red-50 p-2 b-radius-15 d-block">
              <i class="fas fa-calendar-alt f-20 text-danger"></i>
            </span>
            <strong class="d-block mt-2 f-12 f-w-600">Attendance Sheet</strong>
          </a>
          </div>

          <div class="col-lg-4 text-center mb-4">
            <a href="index.php?option=COM_INSERT_EMPLOYEE_RECOGNITION">
            <span class="user-icon bg-red-50 p-2 b-radius-15 d-block">
              <i class="fas fa-calendar-alt f-20 text-danger"></i>
            </span>
            <strong class="d-block mt-2 f-12 f-w-600">Recognition</strong>
          </a>
          </div>

          <div class="col-lg-4 text-center mb-3">
            <a href="index.php?option=COM_LIST_MY_REPORTS">
            <span class="user-icon bg-blue-50 p-2 b-radius-15 d-block">
              <i class="far fa-file-alt f-20 text-primary"></i>
            </span>
            <strong class="d-block mt-2 f-12 f-w-600">Reports</strong>
          </a>
          </div>
          </a>

          <div class="col-lg-4 text-center mb-3">
            <a href="index.php?option=COM_LIST_HR_SERVICES">
            <span class="user-icon bg-green-50 p-2 b-radius-15 d-block">
              <i class="far fa-file-alt f-20 text-success"></i>
            </span>
            <strong class="d-block mt-2 f-12 f-w-600">HR Letters</strong>
          </a>
          </div>

          <div class="col-lg-4 text-center mb-3">
            <a href="index.php?option=COM_LIST_EMPLOYEE_LOANS">
            <span class="user-icon bg-purple-50 p-2 b-radius-15 d-block">
              <i class="fas fa-calendar-alt f-20 text-purple"></i>
            </span>
            <strong class="d-block mt-2 f-12 f-w-600">Loans & Advances</strong>
          </a>
          </div>

          <div class="col-lg-4 text-center mb-4">
            <a href="index.php?option=COM_LIST_PERSONAL_GATEWAY">
            <span class="user-icon bg-blue-50 p-2 b-radius-15 d-block">
              <i class="far fa-file-alt f-20 text-primary"></i>
            </span>
            <strong class="d-block mt-2 f-12 f-w-600">Personal Gateway</strong>
          </a>
          </div>

          <div class="col-lg-4 text-center mb-4">
            <a href="index.php?option=COM_LIST_TRANSPORT_GATEWAY">
            <span class="user-icon bg-green-50 p-2 b-radius-15 d-block">
              <i class="far fa-file-alt f-20 text-success"></i>
            </span>
            <strong class="d-block mt-2 f-12 f-w-600">Transport Gateway</strong>
          </a>
          </div>
         </a>
        </div>
      </div>
    </div>
    <div class="card m-t-40 mb-3">
      <div class="card-body ">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">HR Alerts</h3>
        </div>
        <div class="hr-alerts height-300">
          <div class="d-flex align-items-center mt-4">
            <span class="img-40 b-radius-15 bg-yellow-50 text-warning f-14 f-w-600 p-2 text-center">3</span>
            <h5 class="f-14 f-w-600 m-l-10">Probation confirmations pending</h5>
          </div>
          <div class="d-flex align-items-center mt-3">
            <span class="img-40 b-radius-15 bg-green-50 text-success f-14 f-w-600 p-2 text-center">5</span>
            <h5 class="f-14 f-w-600 m-l-10">Attendance Regularizations pending</h5>
          </div>
          <div class="d-flex align-items-center mt-3">
            <span class="img-40 b-radius-15 bg-red-50 text-danger f-14 f-w-600 p-2 text-center">2</span>
            <h5 class="f-14 f-w-600 m-l-10">Documents expiring this month</h5>
          </div>
          <div class="d-flex align-items-center mt-3">
            <span class="img-40 b-radius-15 bg-red-50 text-danger f-14 f-w-600 p-2 text-center">1</span>
            <h5 class="f-14 f-w-600 m-l-10">Policy Acknowledgement pending</h5>
          </div>
        </div>

      </div>
    </div>
    <div class="card m-t-35 mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Upcoming Birthdays</h3>
        </div>
        <div class="table-responsive mt-3 upcoming height-280">
          <?php
          $upcomingBirthdays = $result['upcoming_birthdays'] ?? [];
          ?>

          <table class="table table-borderless table-sm align-middle pedding-approvals f-12">
            <?php if (count((array)$upcomingBirthdays) > 0) { ?>
              <?php foreach ($upcomingBirthdays as $birthday) {
                $employeeName = $birthday['display_name'] ?? '-';
                $birthdayDate = $birthday['birthday'] ?? '';
                $image = $birthday['image'] ?? '';

                if ($image != '') {
                  $imagePath = 'uploads/all/' . $image;
                } else {
                  $imagePath = 'uploads/all/1.jpg';
                }

                if ($birthdayDate == '' && !empty($birthday['date_of_birth'])) {
                  $birthdayDate = date('d-M', strtotime($birthday['date_of_birth']));
                }
              ?>
                <tr>
                  <td>
                    <img src="<?php echo $imagePath; ?>" alt="" class="img-radius img-30 align-top">
                  </td>

                  <td>
                    <h6 class="f-w-600 f-12 mb-0">
                      <?php echo htmlspecialchars($employeeName); ?>
                    </h6>
                    <span class="text-muted">
                      <?php echo htmlspecialchars($birthday['emp_code'] ?? ''); ?>
                    </span>
                  </td>

                  <th scope="col" class="white-space-nowrap">
                    <?php echo htmlspecialchars($birthdayDate ?: '-'); ?>
                  </th>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="3" class="text-center text-muted f-12">
                  No upcoming birthdays found
                </td>
              </tr>
            <?php } ?>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
