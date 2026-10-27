<?php
$admin_type = 1;
if ($_SESSION['working_role_short_code'] == 'FIELD_EMPLOYEE') {
  $admin_type = 3;
}
if ($_SESSION['working_role_short_code'] == 'FIELD_MANAGER') {
  $admin_type = 2;
}

$dashboardUrl = API . "?mode=togetdailyoperationsdashboard&method=get&admin_type=" . $admin_type;
$dashboardRes = json_decode(callAPI($dashboardUrl), true);

$summary = is_array($dashboardRes) ? $dashboardRes : array();
$plannedVisits = $summary['planned_visits'] ?? array();
$completedVisits = $summary['completed_visits'] ?? array();
$extraVisits = $summary['extra_visits'] ?? array();
$weeklyVisits = $summary['weekly_visits'] ?? array();
$activeEmployees = $summary['active_employees'] ?? array();
$employeeSparkline = $summary['employee_sparkline'] ?? array();
$extraSparkline = $summary['extra_sparkline'] ?? array();
$mapData = $summary['map_data'] ?? array();
$mapRole = $mapData['role'] ?? 'admin';
$mapDefaultMode = $mapData['default_mode'] ?? 'last_visit';

$userName = $_SESSION['us_name'] ?? 'User';
$greeting = $summary['greeting'] ?? 'Good Morning';
$todayLabel = $summary['today_date_label'] ?? date('d M Y');

$employeesCount = (int)($summary['employees_count'] ?? 0);
$employeesGrowth = (float)($summary['employees_growth_pct'] ?? 0);
$plannedToday = (int)($summary['planned_visits_today'] ?? 0);
$remainingToday = (int)($summary['remaining_today'] ?? 0);
$completedToday = (int)($summary['completed_today'] ?? 0);
$extraToday = (int)($summary['extra_visits_today'] ?? 0);
$extraChangeToday = (int)($summary['extra_visits_change_today'] ?? 0);
$completionPct = (int) round($summary['completion_pct'] ?? 0);
$plannedProgressPct = (int) round($summary['planned_progress_pct'] ?? 0);
$successRate = (int) round($summary['success_rate'] ?? 0);
$successRateChange = (float)($summary['success_rate_change'] ?? 0);
$activeEmployeesToday = (int)($summary['active_employees_today'] ?? 0);
$pendingToday = (int)($summary['pending_today'] ?? 0);

$plannedVisitsCount = count((array)$plannedVisits);
$completedVisitsCount = count((array)$completedVisits);
$extraVisitsCount = count((array)$extraVisits);

$progressMessage = 'Keep going! Every visit counts.';
if ($completionPct >= 80) {
  $progressMessage = "Great job! You're on track.";
} elseif ($completionPct >= 50) {
  $progressMessage = 'Good progress so far today.';
}

function dodDashboardText($value) {
  $value = str_replace('DOD_TEST_', '', (string)$value);
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function dodDashboardInitials($name) {
  $parts = preg_split('/\s+/', trim((string)$name));
  $initials = '';
  if (count((array)$parts) > 0) {
    foreach ($parts as $part) {
      if ($part != '') {
        $initials .= strtoupper(substr($part, 0, 1));
      }
      if (strlen($initials) >= 2) {
        break;
      }
    }
  }
  return $initials != '' ? $initials : 'U';
}

function dodDashboardSparklinePoints($values, $width = 200, $height = 40) {
  $values = (array)$values;
  if (count($values) == 0) {
    $values = array(0, 0, 0, 0, 0, 0, 0);
  }
  $max = max($values);
  if ($max == 0) {
    $max = 1;
  }
  $points = array();
  $step = $width / (count($values) - 1);
  if ($step == 0) {
    $step = $width;
  }
  $index = 0;
  foreach ($values as $value) {
    $x = round($index * $step, 2);
    $y = round($height - (($value / $max) * ($height - 8)) - 4, 2);
    $points[] = $x . ',' . $y;
    $index++;
  }
  return implode(' ', $points);
}

function dodDashboardRingDash($percent, $radius) {
  $circumference = 2 * 3.1415926535 * $radius;
  $filled = round(($percent / 100) * $circumference, 1);
  $empty = round($circumference - $filled, 1);
  return $filled . ' ' . $empty;
}

function dodDashboardVisitDateTime($date, $time) {
  $dateLabel = $date != '' ? fromDate($date) : '';
  $timeLabel = $time != '' ? date('h:i A', strtotime($time)) : '';
  if ($dateLabel != '' && $timeLabel != '') {
    return $dateLabel . ', ' . $timeLabel;
  }
  return trim($dateLabel . ' ' . $timeLabel);
}

function dodDashboardEmployeeStatus($row) {
  $title = strtolower(trim((string)($row['map_title'] ?? '')));
  if ($title == 'check-in' || $title == 'check in' || strpos($title, 'dod_test_check-in') !== false) {
    $dateTime = trim((string)($row['map_date'] ?? '') . ' ' . (string)($row['map_time'] ?? ''));
    if ($dateTime != '') {
      $diffMins = floor((time() - strtotime($dateTime)) / 60);
      if ($diffMins < 1) {
        return 'Checked in just now';
      }
      if ($diffMins < 60) {
        return 'Checked in ' . $diffMins . ' mins ago';
      }
      $diffHours = floor($diffMins / 60);
      return 'Checked in ' . $diffHours . ' hrs ago';
    }
    return 'Checked in today';
  }
  if ($title != '') {
    return 'At ' . str_replace('dod_test_', '', $row['map_title']) . ' visit';
  }
  return 'Active in field';
}

function dodDashboardAvatarClass($index) {
  $classes = array('blue', 'purple', 'orange', 'green');
  return $classes[$index % count($classes)];
}

function dodDashboardVisitIconClass($index) {
  $classes = array('apple', 'google', 'amazon');
  return $classes[$index % count($classes)];
}

$employeeSparkPoints = dodDashboardSparklinePoints($employeeSparkline);
$extraSparkPoints = dodDashboardSparklinePoints($extraSparkline);
$plannedRingDash = dodDashboardRingDash($plannedProgressPct, 22);
$progressRingDash = dodDashboardRingDash($completionPct, 40);
$successRingDash = dodDashboardRingDash($successRate, 55);
$employeesGrowthLabel = ($employeesGrowth >= 0 ? '&#8593; ' : '&#8595; ') . abs($employeesGrowth) . '% from last month';
$extraChangeLabel = ($extraChangeToday >= 0 ? '&#8593; ' : '&#8595; ') . abs($extraChangeToday) . ' today';
$successChangeLabel = ($successRateChange >= 0 ? '&#8593; ' : '&#8595; ') . abs($successRateChange) . '% from last month';
?>
<link rel="stylesheet" href="assets/css/custom_view_daily_operations_dashboard.css">

<div class="daily-ops-dashboard">

  <div class="dod-header">
    <div>
      <h1><?php echo dodDashboardText($greeting); ?>, <?php echo dodDashboardText($userName); ?> &#128075;</h1>
      <p>Here's what's happening with your field operations today.</p>
    </div>
    <div class="dod-header-actions">
      <button class="dod-btn" type="button">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Today, <?php echo dodDashboardText($todayLabel); ?>
      </button>
    </div>
  </div>

  <div class="dod-stats-row">
    <div class="dod-card dod-stat-card">
      <div class="dod-stat-top">
        <div>
          <div class="dod-stat-label">Employees</div>
          <div class="dod-stat-value"><?php echo $employeesCount; ?></div>
          <div class="dod-stat-sub green"><?php echo $employeesGrowthLabel; ?></div>
        </div>
        <div class="dod-stat-icon blue">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
        </div>
      </div>
      <div class="dod-stat-chart">
        <svg width="100%" height="40" viewBox="0 0 200 40" preserveAspectRatio="none">
          <polyline fill="none" stroke="#2563eb" stroke-width="2" points="<?php echo $employeeSparkPoints; ?>"/>
          <polyline fill="rgba(37,99,235,0.1)" stroke="none" points="<?php echo $employeeSparkPoints; ?> 200,40 0,40"/>
        </svg>
      </div>
      <a href="index.php?option=COM_LIST_FIELD_WORK_DAILY_PLANS_NEW" class="dod-stat-footer">View Details &#8594;</a>
    </div>

    <div class="dod-card dod-stat-card">
      <div class="dod-stat-top">
        <div>
          <div class="dod-stat-label">Planned Visits</div>
          <div class="dod-stat-value"><?php echo $plannedToday; ?></div>
          <div class="dod-stat-sub muted"><?php echo $remainingToday; ?> remaining today</div>
        </div>
        <div class="dod-stat-icon blue">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/></svg>
        </div>
      </div>
      <div class="dod-ring-wrap">
        <svg width="56" height="56" viewBox="0 0 56 56">
          <circle cx="28" cy="28" r="22" fill="none" stroke="#e5e7eb" stroke-width="5"/>
          <circle cx="28" cy="28" r="22" fill="none" stroke="#2563eb" stroke-width="5" stroke-dasharray="<?php echo $plannedRingDash; ?>" stroke-linecap="round" transform="rotate(-90 28 28)"/>
          <text x="28" y="32" text-anchor="middle" font-size="12" font-weight="700" fill="#2563eb"><?php echo $plannedProgressPct; ?>%</text>
        </svg>
      </div>
      <a href="index.php?option=COM_LIST_FIELD_WORK_DAILY_PLANS_NEW" class="dod-stat-footer">View Details &#8594;</a>
    </div>

    <div class="dod-card dod-stat-card">
      <div class="dod-stat-top">
        <div>
          <div class="dod-stat-label">Completed</div>
          <div class="dod-stat-value"><?php echo $completedToday; ?></div>
          <div class="dod-stat-sub green"><?php echo $completionPct; ?>% completion</div>
        </div>
        <div class="dod-stat-icon green">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        </div>
      </div>
      <div class="dod-progress-bar">
        <div class="dod-progress-bar-fill" style="width: <?php echo $completionPct; ?>%;"></div>
      </div>
      <a href="index.php?option=COM_LIST_FIELD_WORK_USER_MAP_POINTS_NEW" class="dod-stat-footer">View Details &#8594;</a>
    </div>

    <div class="dod-card dod-stat-card">
      <div class="dod-stat-top">
        <div>
          <div class="dod-stat-label">Extra Visits</div>
          <div class="dod-stat-value"><?php echo $extraToday; ?></div>
          <div class="dod-stat-sub orange"><?php echo $extraChangeLabel; ?></div>
        </div>
        <div class="dod-stat-icon orange">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
        </div>
      </div>
      <div class="dod-stat-chart">
        <svg width="100%" height="40" viewBox="0 0 200 40" preserveAspectRatio="none">
          <polyline fill="none" stroke="#f59e0b" stroke-width="2" points="<?php echo $extraSparkPoints; ?>"/>
          <polyline fill="rgba(245,158,11,0.1)" stroke="none" points="<?php echo $extraSparkPoints; ?> 200,40 0,40"/>
        </svg>
      </div>
      <a href="index.php?option=COM_LIST_FIELD_WORK_USER_MAP_POINTS_NEW" class="dod-stat-footer">View Details &#8594;</a>
    </div>
  </div>

  <div class="dod-middle-row">
    <div class="dod-card">
      <h3 class="dod-card-title">Quick Actions</h3>
      <div class="dod-quick-grid">
        <a href="index.php?option=COM_INSERT_FIELD_WORK_DAILY_PLANS_NEW" class="dod-quick-item">
          <div class="dod-quick-circle blue">+</div>
          <span class="dod-quick-label">Plan Visit</span>
        </a>
        <a href="index.php?option=COM_LIST_FIELD_WORK_USER_MAP_POINTS_NEW" class="dod-quick-item">
          <div class="dod-quick-circle green">&#10003;</div>
          <span class="dod-quick-label">Check In</span>
        </a>
        <a href="index.php?option=COM_VIEW_MAP_POINTS_NEW" class="dod-quick-item">
          <div class="dod-quick-circle purple">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          </div>
          <span class="dod-quick-label">View Route</span>
        </a>
        <a href="index.php?option=COM_LIST_FIELD_WORK_DAILY_PLANS_NEW" class="dod-quick-item">
          <div class="dod-quick-circle orange">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
          </div>
          <span class="dod-quick-label">Reports</span>
        </a>
      </div>
    </div>

    <div class="dod-card dod-map-card">
      <div class="dod-map-header">
        <h3 class="dod-card-title">Today's Route</h3>
        <div class="dod-map-mode-toggle" id="dodMapModeToggle">
          <?php if ($mapRole == 'employee') { ?>
            <button type="button" class="dod-map-mode-btn<?php echo $mapDefaultMode == 'planned' ? ' active' : ''; ?>" data-map-mode="planned">Planned</button>
            <button type="button" class="dod-map-mode-btn<?php echo $mapDefaultMode == 'visits' ? ' active' : ''; ?>" data-map-mode="visits">Visits</button>
          <?php } else { ?>
            <button type="button" class="dod-map-mode-btn<?php echo $mapDefaultMode == 'next_planned' ? ' active' : ''; ?>" data-map-mode="next_planned">Next planned</button>
            <button type="button" class="dod-map-mode-btn<?php echo $mapDefaultMode == 'last_visit' ? ' active' : ''; ?>" data-map-mode="last_visit">Last visit</button>
          <?php } ?>
        </div>
      </div>
      <div class="dod-map-body">
        <div id="dodRouteMap"></div>
        <div class="dod-map-empty" id="dodMapEmpty" style="display:none;">No map points for this view.</div>
        <div class="dod-map-overlay">
          <div class="dod-map-stat">
            <span class="dod-map-stat-dot blue"></span>
            <?php echo $activeEmployeesToday; ?> Active Employees
          </div>
          <div class="dod-map-stat">
            <span class="dod-map-stat-dot green"></span>
            <?php echo $completedToday; ?> Completed
          </div>
          <div class="dod-map-stat">
            <span class="dod-map-stat-dot orange"></span>
            <?php echo $pendingToday; ?> Pending
          </div>
        </div>
        <?php if ($mapRole == 'employee') { ?>
          <a href="#" id="dodNavigateBtn" class="dod-navigate-btn d-none" target="_blank" rel="noopener noreferrer">
            <i class="fas fa-directions"></i> Navigate
          </a>
        <?php } ?>
      </div>
    </div>

    <div class="dod-card">
      <h3 class="dod-card-title">Today's Progress</h3>
      <div class="dod-progress-content">
        <svg width="100" height="100" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="40" fill="none" stroke="#e5e7eb" stroke-width="10"/>
          <circle cx="50" cy="50" r="40" fill="none" stroke="#2563eb" stroke-width="10" stroke-dasharray="<?php echo $progressRingDash; ?>" stroke-linecap="round" transform="rotate(-90 50 50)"/>
          <text x="50" y="55" text-anchor="middle" font-size="18" font-weight="700" fill="#2563eb"><?php echo $completionPct; ?>%</text>
        </svg>
        <div class="dod-progress-text">
          <h3><?php echo $completedToday; ?> / <?php echo $plannedToday; ?></h3>
          <p>Visits Completed</p>
          <p class="dod-progress-on-track"><?php echo dodDashboardText($progressMessage); ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="dod-bottom-row">
    <div class="dod-card custom-scroll" style="max-height: 300px;">
      <h3 class="dod-card-title">Today's Visits</h3>
      <div class="dod-tabs">
        <button class="dod-tab active" type="button" data-visit-tab="planned">
          Planned <span class="dod-tab-count"><?php echo $plannedVisitsCount; ?></span>
        </button>
        <button class="dod-tab" type="button" data-visit-tab="completed">
          Completed <span class="dod-tab-count"><?php echo $completedVisitsCount; ?></span>
        </button>
        <button class="dod-tab" type="button" data-visit-tab="extra">
          Extra <span class="dod-tab-count"><?php echo $extraVisitsCount; ?></span>
        </button>
      </div>

      <div class="dod-visit-panel active" id="dodVisitPlanned">
        <?php if (count((array)$plannedVisits) > 0) {
          $visitIndex = 0;
          foreach ($plannedVisits as $visit) {
            $visitTitle = ($visit['contact_person'] ?? '') != '' ? $visit['contact_person'] : 'Planned Visit';
            $visitLocation = ($visit['plan_location'] ?? '') != '' ? $visit['plan_location'] : '-';
            $visitEmployee = ($visit['employee_name'] ?? '') != '' ? $visit['employee_name'] : '-';
            $visitStatus = ($visit['visit_status'] ?? '') == 'completed' ? 'completed' : 'pending';
            $visitStatusLabel = $visitStatus == 'completed' ? 'Completed' : 'Pending';
            $visitInitial = strtoupper(substr($visitTitle, 0, 1));
            ?>
            <div class="dod-visit-item">
              <div class="dod-visit-icon <?php echo dodDashboardVisitIconClass($visitIndex); ?>"><?php echo dodDashboardText($visitInitial); ?></div>
              <div class="dod-visit-info">
                <h4><?php echo dodDashboardText($visitTitle); ?></h4>
                <span><?php echo dodDashboardText($visitLocation); ?></span>
                <span class="dod-visit-employee"><?php echo dodDashboardText($visitEmployee); ?></span>
              </div>
              <div class="dod-visit-meta">
                <div class="date"><?php echo dodDashboardText(dodDashboardVisitDateTime($visit['plan_date'] ?? '', $visit['plan_time'] ?? '')); ?></div>
                <span class="dod-badge <?php echo $visitStatus; ?>"><?php echo $visitStatusLabel; ?></span>
              </div>
            </div>
            <?php
            $visitIndex++;
          }
        } else { ?>
          <div class="dod-empty-state">No planned visits for today.</div>
        <?php } ?>
      </div>

      <div class="dod-visit-panel" id="dodVisitCompleted">
        <?php if (count((array)$completedVisits) > 0) {
          $visitIndex = 0;
          foreach ($completedVisits as $visit) {
            $visitTitle = ($visit['contact_person'] ?? '') != '' ? $visit['contact_person'] : 'Completed Visit';
            $visitLocation = ($visit['plan_location'] ?? '') != '' ? $visit['plan_location'] : '-';
            $visitEmployee = ($visit['employee_name'] ?? '') != '' ? $visit['employee_name'] : '-';
            $visitInitial = strtoupper(substr($visitTitle, 0, 1));
            ?>
            <div class="dod-visit-item">
              <div class="dod-visit-icon <?php echo dodDashboardVisitIconClass($visitIndex); ?>"><?php echo dodDashboardText($visitInitial); ?></div>
              <div class="dod-visit-info">
                <h4><?php echo dodDashboardText($visitTitle); ?></h4>
                <span><?php echo dodDashboardText($visitLocation); ?></span>
                <span class="dod-visit-employee"><?php echo dodDashboardText($visitEmployee); ?></span>
              </div>
              <div class="dod-visit-meta">
                <div class="date"><?php echo dodDashboardText(dodDashboardVisitDateTime($visit['plan_date'] ?? '', $visit['plan_time'] ?? '')); ?></div>
                <span class="dod-badge completed">Completed</span>
              </div>
            </div>
            <?php
            $visitIndex++;
          }
        } else { ?>
          <div class="dod-empty-state">No completed visits for today.</div>
        <?php } ?>
      </div>

      <div class="dod-visit-panel" id="dodVisitExtra">
        <?php if (count((array)$extraVisits) > 0) {
          $visitIndex = 0;
          foreach ($extraVisits as $visit) {
            $visitTitle = ($visit['contact_person'] ?? '') != '' ? $visit['contact_person'] : 'Extra Visit';
            $visitLocation = ($visit['plan_location'] ?? '') != '' ? $visit['plan_location'] : '-';
            $visitEmployee = ($visit['employee_name'] ?? '') != '' ? $visit['employee_name'] : '-';
            $visitInitial = strtoupper(substr($visitTitle, 0, 1));
            ?>
            <div class="dod-visit-item">
              <div class="dod-visit-icon <?php echo dodDashboardVisitIconClass($visitIndex); ?>"><?php echo dodDashboardText($visitInitial); ?></div>
              <div class="dod-visit-info">
                <h4><?php echo dodDashboardText($visitTitle); ?></h4>
                <span><?php echo dodDashboardText($visitLocation); ?></span>
                <span class="dod-visit-employee"><?php echo dodDashboardText($visitEmployee); ?></span>
              </div>
              <div class="dod-visit-meta">
                <div class="date"><?php echo dodDashboardText(dodDashboardVisitDateTime($visit['plan_date'] ?? '', $visit['plan_time'] ?? '')); ?></div>
                <span class="dod-badge pending">Extra</span>
              </div>
            </div>
            <?php
            $visitIndex++;
          }
        } else { ?>
          <div class="dod-empty-state">No extra visits for today.</div>
        <?php } ?>
      </div>

      <div class="dod-view-all-center">
        <a href="index.php?option=COM_LIST_FIELD_WORK_DAILY_PLANS_NEW" class="dod-link">View All Visits &#8594;</a>
      </div>
    </div>

    <div class="dod-card custom-scroll" style="max-height: 300px;">
      <div class="dod-card-title-row">
        <h3 class="dod-card-title">Visits This Week</h3>
        <select class="dod-select" disabled>
          <option>This Week</option>
        </select>
      </div>
      <div class="dod-bar-chart">
        <?php if (count((array)$weeklyVisits) > 0) {
          $todayDate = date('Y-m-d');
          foreach ($weeklyVisits as $weeklyItem) {
            $isToday = ($weeklyItem['date'] ?? '') == $todayDate;
            ?>
            <div class="dod-bar-col">
              <div class="dod-bar-track">
                <span class="dod-bar-count"><?php echo (int)($weeklyItem['count'] ?? 0); ?></span>
                <div class="dod-bar <?php echo $isToday ? 'highlight' : ''; ?>" style="height: <?php echo (int)($weeklyItem['height_pct'] ?? 0); ?>%;"></div>
              </div>
              <span class="dod-bar-label"><?php echo dodDashboardText($weeklyItem['label'] ?? ''); ?></span>
            </div>
            <?php
          }
        } ?>
      </div>
    </div>

    <div class="dod-card custom-scroll" style="max-height: 300px;">
      <div class="dod-card-title-row">
        <h3 class="dod-card-title">Visit Success Rate</h3>
        <select class="dod-select" disabled>
          <option>This Month</option>
        </select>
      </div>
      <div class="dod-success-wrap">
        <svg width="140" height="140" viewBox="0 0 140 140">
          <circle cx="70" cy="70" r="55" fill="none" stroke="#e5e7eb" stroke-width="12"/>
          <circle cx="70" cy="70" r="55" fill="none" stroke="#10b981" stroke-width="12" stroke-dasharray="<?php echo $successRingDash; ?>" stroke-linecap="round" transform="rotate(-90 70 70)"/>
          <text x="70" y="68" text-anchor="middle" font-size="26" font-weight="700" fill="#10b981"><?php echo $successRate; ?>%</text>
          <text x="70" y="88" text-anchor="middle" font-size="11" fill="#6b7280">Success Rate</text>
        </svg>
        <div class="dod-success-sub"><?php echo $successChangeLabel; ?></div>
      </div>
    </div>

    <div class="dod-card custom-scroll" style="max-height: 300px;">
      <div class="dod-card-title-row">
        <h3 class="dod-card-title">Active Employees</h3>
        <a href="index.php?option=COM_VIEW_MAP_POINTS_NEW" class="dod-link">View All</a>
      </div>

      <?php if (count((array)$activeEmployees) > 0) {
        $employeeIndex = 0;
        foreach ($activeEmployees as $employee) {
          $employeeName = ($employee['display_name'] ?? '') != '' ? $employee['display_name'] : 'Employee';
          $statusClass = strtolower(trim((string)($employee['map_title'] ?? ''))) == 'check-in' ? 'online' : 'away';
          ?>
          <div class="dod-employee-item">
            <div class="dod-employee-avatar <?php echo dodDashboardAvatarClass($employeeIndex); ?>"><?php echo dodDashboardText(dodDashboardInitials($employeeName)); ?></div>
            <div class="dod-employee-info">
              <h4><span class="dod-status-dot <?php echo $statusClass; ?>"></span> <?php echo dodDashboardText($employeeName); ?></h4>
              <span><?php echo dodDashboardText(dodDashboardEmployeeStatus($employee)); ?></span>
            </div>
            <span class="dod-chevron">&#8250;</span>
          </div>
          <?php
          $employeeIndex++;
        }
      } else { ?>
        <div class="dod-empty-state">No active employees for today.</div>
      <?php } ?>
    </div>
  </div>

  <!-- <a href="index.php?option=COM_INSERT_FIELD_WORK_DAILY_PLANS_NEW" class="dod-fab" title="Add New">+</a> -->

</div>

<script>
  window.dodMapData = <?php echo json_encode($mapData); ?>;
  window.dodGoogleMapsKeyConfigured = <?php echo (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY != '') ? 'true' : 'false'; ?>;
</script>
<?php if (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY != '') { ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode(GOOGLE_MAPS_API_KEY); ?>"></script>
<?php } ?>
<script src="assets/js/custom_view_daily_operations_dashboard.js"></script>
