
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  
<link rel="stylesheet" type="text/css" href="../assets/css/custom_view_procurement_dashboard.css">

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container-fluid mt-3 mob-padding">
  <div class="dashboard-container p-4">
    
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mob-heading">
      <div>
        <h1 class="h5 fw-bold text-dark mb-0">Procurement Intelligence Dashboard</h1>
        <p class="text-muted small mb-0 f-12">23 Apr 2026 • Last refreshed: 09:36 am[cite: 2]</p>
      </div>
      <button class="btn btn-primary btn-sm px-3 py-1 b-radius-5 f-14">
        <i class="bi bi-arrow-clockwise me-1"></i> Refresh[cite: 2]
      </button>
    </div>

    <!-- Custom Navigation Tabs[cite: 2] -->
    <div class="custom-tabs-container nav mb-4" id="dashboardTabs" role="tablist">
      <a class="custom-tab-link active" id="tab-notification" data-bs-toggle="tab" href="#content-notification" role="tab" aria-controls="content-notification" aria-selected="true">
        <div class="tab-icon-wrapper"><i class="bi bi-bell fs-4"></i></div>
        <div>
          <span class="tab-main-count">10</span>
          <span class="tab-title">Notification</span>
          <span class="tab-sub-text">15 Action</span>
        </div>
      </a>
      <!-- <a class="custom-tab-link" id="tab-approvals" data-bs-toggle="tab" href="#content-approvals" role="tab" aria-controls="content-approvals" aria-selected="false">
        <div class="tab-icon-wrapper"><i class="bi bi-clock text-primary fs-4"></i></div>
        <div>
          <span class="tab-main-count">5</span>
          <span class="tab-title">Pending Approvals</span>
          <span class="tab-sub-text text-danger fw-bold">4 Critical</span>
        </div>
      </a> -->
      <!-- <a class="custom-tab-link" id="tab-tasks" data-bs-toggle="tab" href="#content-tasks" role="tab" aria-controls="content-tasks" aria-selected="false">
        <div class="tab-icon-wrapper"><i class="bi bi-clipboard-check text-primary fs-4"></i></div>
        <div>
          <span class="tab-main-count">3</span>
          <span class="tab-title">Unread Tasks</span>
          <span class="tab-sub-text text-danger fw-bold">3 Critical</span>
        </div>
      </a> -->
      <a class="custom-tab-link" id="tab-issues" data-bs-toggle="tab" href="#content-issues" role="tab" aria-controls="content-issues" aria-selected="false">
        <div class="tab-icon-wrapper"><i class="bi bi-headset text-primary fs-4"></i></div>
        <div>
          <span class="tab-main-count">25</span>
          <span class="tab-title">Customer Issues</span>
          <span class="tab-sub-text">0 Breach</span>
        </div>
      </a>
      <a class="custom-tab-link" id="tab-ratings" data-bs-toggle="tab" href="#content-ratings" role="tab" aria-controls="content-ratings" aria-selected="false">
        <div class="tab-icon-wrapper"><i class="bi bi-star text-primary fs-4"></i></div>
        <div>
          <span class="tab-main-count">27</span>
          <span class="tab-title">Customer Ratings</span>
          <span class="tab-sub-text text-success">⭐ 4.8 Avg</span>
        </div>
      </a>
    </div>

    <!-- Tabs Content[cite: 2] -->
    <div class="tab-content pt-2" id="dashboardTabsContent">
      <div class="tab-pane fade show active" id="content-notification" role="tabpanel" aria-labelledby="tab-notification">
        <div class="d-flex align-items-center mb-4">
          <h2 class="h6 fw-bold mb-0 text-dark me-2">Module 1 — Notifications</h2>
          <span class="badge bg-light text-primary border border-primary px-2" style="font-size: 11px;">unread</span>
        </div>

        <!-- Metric Badges[cite: 2] -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="module-badge-box" style="background-color: #fff3cd; border-color: #ffecb5;">
              <div class="p-2 bg-white rounded shadow-sm text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-bell-fill fs-5"></i>
              </div>
              <div>
                <span class="fw-bold fs-4 d-block text-dark mb-0 lh-1">15</span>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 10px; letter-spacing: 0.5px;">ACTION UNREAD</span>
              </div>
            </div>
            <div class="table-responsive mt-3">
              <table class="table table-bordered table-sm">
                <tr>
                  <th>Details</th>
                </tr>
                <tr>
                  <td>
                    <div class="">
                      <span class="badge bg-danger text-white rounded-pill fw-bold text-center me-3 f-10" style="width: 70px;">CRITICAL</span>
                      <span class="text-dark fw-normal d-block mt-2">15 ACTION notification(s) unread for 3+ days</span>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="module-badge-box bg-light border-light">
              <div class="p-2 bg-white rounded shadow-sm text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-info-circle-fill fs-5"></i>
              </div>
              <div>
                <span class="fw-bold fs-4 d-block text-dark mb-0 lh-1">0</span>
                <span class="text-uppercase text-muted fw-bold d-block f-10" style="letter-spacing: 0.5px;">INFO UNREAD</span>
              </div>
            </div>
            <div class="table-responsive mt-3">
              <table class="table table-bordered table-sm">
                <tr>
                  <th>Details</th>
                </tr>
                <tr>
                  <td>
                    <div class="">
                      <span class="badge bg-danger text-white rounded-pill fw-bold text-center me-3 f-10" style="width: 70px;">CRITICAL</span>
                      <span class="text-dark fw-normal d-block mt-2">15 ACTION notification(s) unread for 3+ days</span>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="module-badge-box" style="background-color: #f8d7da; border-color: #f5c2c7;">
              <div class="p-2 bg-white rounded shadow-sm text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-calendar-event-fill fs-5"></i>
              </div>
              <div>
                <span class="fw-bold fs-4 d-block text-dark mb-0 lh-1">53</span>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 10px; letter-spacing: 0.5px;">OLDEST (DAYS)</span>
              </div>
            </div>
            <div class="table-responsive mt-3">
              <table class="table table-bordered table-sm">
                <tr>
                  <th>Details</th>
                </tr>
                <tr>
                  <td>
                    <div class="">
                      <span class="badge bg-danger text-white rounded-pill fw-bold text-center me-3 f-10" style="width: 70px;">CRITICAL</span>
                      <span class="text-dark fw-normal d-block mt-2">15 ACTION notification(s) unread for 3+ days</span>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="module-badge-box" style="background-color: #f3e8ff; border-color: #e9d5ff;">
              <div class="p-2 bg-white rounded shadow-sm text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-arrow-repeat fs-5"></i>
              </div>
              <div>
                <span class="fw-bold fs-4 d-block text-dark mb-0 lh-1">2</span>
                <span class="text-uppercase text-muted fw-bold d-block f-10" style="letter-spacing: 0.5px;">REPEATED</span>
              </div>
            </div>
            <div class="table-responsive mt-3">
              <table class="table table-bordered table-sm">
                <tr>
                  <th>Details</th>
                </tr>
                <tr>
                  <td>
                    <div class="">
                      <span class="badge bg-danger text-white rounded-pill fw-bold text-center me-3 f-10" style="width: 70px;">CRITICAL</span>
                      <span class="text-dark fw-normal d-block mt-2">15 ACTION notification(s) unread for 3+ days</span>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center mb-4">
              <h2 class="h6 fw-bold mb-0 text-dark me-2">Pending Approvals 5</h2>
              <span class="badge bg-danger text-white rounded-pill fw-bold text-center me-3 f-10">4 CRITICAL</span>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-12 col-sm-12 col-12 mb-3">
                <div class="ai-summary-box p-4">
                  <div class="d-flex gap-2 text-primary mb-2">
                    <i class="bi bi-stars fs-4"></i>
                    <h4 class="h6 fw-bold mb-0 pt-1">AI Summary</h4>
                  </div>
                  <p class="text-dark mb-3 f-12">
                    Critical leave requests 140-143 are most urgent for approval.
                  </p>
                </div>
              </div>
              <div class="col-lg-9 col-md-12 col-sm-12 col-12">
                <div class="table-responsive">
                  <table class="table table-bordered table-sm">
                    <tbody>
                      <tr bgcolor="#eeeeee">
                        <th>Transaction Table</th>
                        <th>Requested On</th>
                        <th>Days Pending</th>
                        <th>Severity</th>
                        <th>Issue</th>
                        <th>Review</th>
                      </tr>
                      <tr>
                        <td>Employee Leaves</td>
                        <td>2025-03-25 08:54:17</td>
                        <td class="text-danger">402</td>
                        <td><label class="form-label badge badge-inverse-danger">CRITICAL</label></td>
                        <td class="white-space-normal">Leave request 140 pending 402 days; leave cannot be granted until approved.</td>
                        <td class="white-space-normal">1. Review leave request 140. 2. Approve or reject request.</td>
                      </tr>
                      <tr>
                        <td>Employee Leaves</td>
                        <td>2025-03-25 10:36:50</td>
                        <td class="text-danger">402</td>
                        <td><label class="form-label badge badge-inverse-danger">CRITICAL</label></td>
                        <td class="white-space-normal">Leave request 141 pending 402 days; leave cannot be granted until approved.</td>
                        <td class="white-space-normal">1. Review leave request 141. 2. Approve or reject request.</td>
                      </tr>
                      <tr>
                        <td>Employee Leaves</td>
                        <td>2025-03-25 10:52:21</td>
                        <td class="text-danger">402</td>
                        <td><label class="form-label badge badge-inverse-danger">CRITICAL</label></td>
                        <td class="white-space-normal">Leave request 142 pending 402 days; leave cannot be granted until approved.</td>
                        <td class="white-space-normal">1. Review leave request 142. 2. Approve or reject request.</td>
                      </tr>
                      <tr>
                        <td>Employee Leaves</td>
                        <td>2025-03-25 11:02:29</td>
                        <td class="text-danger">402</td>
                        <td><label class="form-label badge badge-inverse-danger">CRITICAL</label></td>
                        <td class="white-space-normal">Leave request 143 pending 402 days; leave cannot be granted until approved.</td>
                        <td class="white-space-normal">1. Review leave request 143. 2. Approve or reject request.</td>
                      </tr>
                      <tr>
                        <td>Employee Leaves</td>
                        <td>2026-04-22 14:45:13</td>
                        <td class="text-danger">9</td>
                        <td><label class="form-label badge badge-inverse-danger">WARNING</label></td>
                        <td class="white-space-normal">Leave request 85 pending 9 days; leave cannot be granted until approved.</td>
                        <td class="white-space-normal">1. Review leave request 85. 2. Approve or reject request.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center mb-4">
              <h2 class="h6 fw-bold mb-0 text-dark me-2">Unread Tasks 3</h2>
              <span class="badge bg-danger text-white rounded-pill fw-bold text-center me-3 f-10">3 CRITICAL</span>
            </div>
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                <div class="ai-summary-box p-4">
                  <div class="d-flex gap-2 text-primary mb-2">
                    <i class="bi bi-stars fs-4"></i>
                    <h4 class="h6 fw-bold mb-0 pt-1">AI Summary</h4>
                  </div>
                  <p class="text-dark mb-3 f-12">
                    You have 3 pending tasks, all of which are overdue by 760 days, with no upcoming deadlines within the next 7 days. Immediate attention is required to address these significantly delayed items. Prioritize completing the Certificate of Incorporation first to avoid further compliance risks.
                  </p>
                </div>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                <div class="table-responsive">
                  <table class="table table-bordered table-sm">
                    <tbody>
                      <tr bgcolor="#eeeeee">
                        <th>Task Name</th>
                        <th>Overdue Day</th>
                        <th>Severity</th>
                      </tr>
                      <tr>
                        <td class="white-space-normal">
                          <h6 class="f-14 m-0">Certificate Of Incorporation</h6>
                        </td>
                        <td>760</td>
                        <td>
                          <label class="form-label badge badge-inverse-danger">CRITICAL</label>
                        </td>
                      </tr>
                      <tr>
                        <td class="white-space-normal">
                          <h6 class="f-14 m-0">MSME Certificate If Applicable</h6>
                        </td>
                        <td>760</td>
                        <td>
                          <label class="form-label badge badge-inverse-danger">CRITICAL</label>
                        </td>
                      </tr>
                      <tr>
                        <td class="white-space-normal">
                          <h6 class="f-14 m-0">Cancelled Cheque</h6>
                        </td>
                        <td>760</td>
                        <td>
                          <label class="form-label badge badge-inverse-danger">CRITICAL</label>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Critical Alerts Area[cite: 2] -->
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card">
              <div class="card-body">
                <div class="alert alert-light border p-3 rounded" style="min-height: 134px;">
                  <div class="d-flex align-items-center mb-2 action-mob">
                    <span class="badge bg-danger text-white rounded-pill fw-bold text-center me-3 f-10" style="width: 70px;">CRITICAL</span>
                    <span class="text-dark fw-normal" style="font-size: 13px;">15 ACTION notification(s) unread for 3+ days</span>
                  </div>
                  <div class="d-flex align-items-center mb-2 action-mob">
                    <span class="badge bg-warning text-dark rounded-pill fw-bold text-center me-3 f-10" style="width: 70px;">WARNING</span>
                    <span class="text-dark fw-normal" style="font-size: 13px;">Oldest unread notification is 53 days old</span>
                  </div>
                  <div class="d-flex align-items-center mb-2 action-mob">
                    <span class="badge bg-info text-dark rounded-pill fw-bold text-center me-3 f-10" style="width: 70px;">Info</span>
                    <span class="text-dark fw-normal" style="font-size: 13px;">Oldest unread notification is 53 days old</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-3">
              <div class="card-body">
                <h4 class="h6 fw-bold text-dark mb-3">Module Bar Breakdown</h4>
                <div class="chart-container-bar">
                  <canvas id="moduleBarChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- NEW FIXED CHARTS ROW -->
        <div class="row g-4 mb-4">
          <div class="col-lg-4 col-md-4 col-sm-12 col-12">
            <div class="chart-box-wrapper">
              <h3 class="h6 fw-bold text-dark mb-3">Notification Status</h3>
              <div class="chart-container-doughnut">
                <canvas id="priorityDoughnutChart" style="height:200px !important;"></canvas>
              </div>
            </div>
          </div>
          <div class="col-lg-5 col-md-5 col-sm-12 col-12">
            <div class="chart-box-wrapper">
              <h3 class="h6 fw-bold text-dark mb-3">Notification Trend (Last 7 Days)</h3>
              <div class="chart-container-line">
                <canvas id="notificationTrendChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-12">
            <h3 class="h6 fw-bold text-dark mb-3">AI Recommendations</h3>
            <div class="ai-summary-box p-4">
              <div class="d-flex gap-2 text-primary mb-2">
                <i class="bi bi-stars fs-4"></i>
                <h4 class="h6 fw-bold mb-0 pt-1">AI Summary</h4>
              </div>
              <p class="text-dark mb-3" style="font-size: 13px; line-height: 1.6;">
                There are 15 unread ACTION notifications requiring response, with the majority in goods_receipt_notes (8)[cite: 2]. Ten of these are older than 30 days[cite: 2]. Prioritize resolving the "Next Process Not Set" errors to clear the oldest blockers[cite: 2].
              </p>
            </div>
          </div>          
        </div>

        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card">
              <div class="card-body" style="height: 420px;">
                <h4 class="h6 fw-bold text-dark mb-3">Risk By Category</h4>
                <div class="ct-chart-horizontal ct-perfect-fourth" style="height: 300px;"></div>
              </div>
            </div>  
            <div class="card">
              <div class="card-body">
                <h3 class="h6 fw-bold text-dark mb-3">Action Items by Module</h3>
                <div class="table-responsive border rounded overflow-hidden mb-3">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted" style="font-size: 12px;">
                      <tr>
                        <th class="py-3 px-3">ERP MODULE</th>
                        <th class="text-end py-3 px-3">ACTION COUNT</th>
                      </tr>
                    </thead>
                    <tbody style="font-size: 13px;">
                      <tr><td class="px-3"><i class="bi bi-box-seam text-success me-2"></i> Goods Receipt Notes</td><td class="text-end fw-bold text-dark px-3">8</td></tr>
                      <tr><td class="px-3"><i class="bi bi-person-fill text-primary me-2"></i> Employee Leaves</td><td class="text-end fw-bold text-dark px-3">1</td></tr>
                      <tr><td class="px-3"><i class="bi bi-cart-fill text-info me-2"></i> Purchase Orders</td><td class="text-end fw-bold text-dark px-3">3</td></tr>
                      <tr><td class="px-3"><i class="bi bi-file-earmark-text text-warning me-2"></i> Quotations</td><td class="text-end fw-bold text-dark px-3">3</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card risk-card">
              <!-- Card Header -->
              <div class="card-header">
                <div class="ai-header">
                  <div class="ai-header-icon">
                    <i class="bi bi-shield-check"></i>
                  </div>
                  <h2 class="ai-title">Risk by Warehouse</h2>
                </div>
                <div class="card-header-right">
                  <a href="#" class="footer-link">
                    View Warehouse Details <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
              <div class="card-body">
                <!-- Main Map and List Row -->
                <div class="row g-4">
                  <!-- 1. MAP COLUMN (col-lg-8 ke andar Full Width) -->
                  <div class="col-lg-6">
                    <div class="map-card">
                      <div id="india-map" class="map-container"></div>
                    </div>
                  </div>

                  <!-- 2. RIGHT SIDE DYNAMIC LISTING (No Form UI) -->
                  <div class="col-lg-6">
                    <h6 class="mb-3 text-secondary fw-bold">State Risk & Sales Overview</h6>
                    <!-- JS se dynamic cards auto yahan attach honge -->
                    <div class="risk-list" id="risk-list-container"></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card ai-card">
              <div class="card-header">
                <!-- Card Header -->
                <div class="ai-header">
                  <div class="ai-header-icon">
                    <i class="bi bi-robot"></i>
                  </div>
                  <h2 class="ai-title">Ask AI Assistant</h2>
                </div>
              </div>
              <div class="card-body">
                <!-- Content Panel -->
                <div class="ai-content">
                  
                  <!-- Left: Dynamic Prompts -->
                  <div class="prompt-list">
                    <button class="prompt-button">
                      <span class="prompt-text">Why are wiring shortages increasing?</span>
                      <span class="prompt-arrow"><i class="bi bi-chevron-right"></i></span>
                    </button>
                    
                    <button class="prompt-button">
                      <span class="prompt-text">Which vendor is causing the most delays?</span>
                      <span class="prompt-arrow"><i class="bi bi-chevron-right"></i></span>
                    </button>
                    
                    <button class="prompt-button">
                      <span class="prompt-text">What is the impact if I don't reorder today?</span>
                      <span class="prompt-arrow"><i class="bi bi-chevron-right"></i></span>
                    </button>
                    
                    <button class="prompt-button">
                      <span class="prompt-text">Can any warehouse transfer stock?</span>
                      <span class="prompt-arrow"><i class="bi bi-chevron-right"></i></span>
                    </button>
                    
                    <button class="prompt-button">
                      <span class="prompt-text">Predict next month's inventory shortages.</span>
                      <span class="prompt-arrow"><i class="bi bi-chevron-right"></i></span>
                    </button>
                  </div>

                  <!-- Right: AI Mascot Image + Open Link -->
                  <div class="ai-graphic-panel">
                    <div class="mascot-container">
                      <div class="mascot-glow"></div>
                      
                      <!-- Modern SVG Illustration of Robot with Chat Bubbles -->
                      <svg class="mascot-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Background Chat Bubbles -->
                        <rect x="15" y="30" width="45" height="25" rx="6" fill="#eff6ff" stroke="#dbeafe" stroke-width="1.5" />
                        <line x1="25" y1="38" x2="45" y2="38" stroke="#bfdbfe" stroke-width="2" stroke-linecap="round" />
                        <line x1="25" y1="44" x2="38" y2="44" stroke="#bfdbfe" stroke-width="2" stroke-linecap="round" />
                        <path d="M50 55 L50 62 L44 55 Z" fill="#eff6ff" stroke="#dbeafe" stroke-width="1.5" />

                        <rect x="5" y="75" width="40" height="22" rx="6" fill="#eff6ff" stroke="#dbeafe" stroke-width="1.5" />
                        <line x1="12" y1="82" x2="30" y2="82" stroke="#bfdbfe" stroke-width="2" stroke-linecap="round" />
                        <line x1="12" y1="88" x2="22" y2="88" stroke="#bfdbfe" stroke-width="2" stroke-linecap="round" />
                        
                        <rect x="25" y="125" width="45" height="25" rx="6" fill="#eff6ff" stroke="#dbeafe" stroke-width="1.5" />
                        <line x1="35" y1="133" x2="55" y2="133" stroke="#bfdbfe" stroke-width="2" stroke-linecap="round" />
                        <line x1="35" y1="139" x2="48" y2="139" stroke="#bfdbfe" stroke-width="2" stroke-linecap="round" />

                        <!-- Main Robot Character -->
                        <!-- Torso -->
                        <rect x="100" y="145" width="60" height="50" rx="20" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="2"/>
                        <circle cx="130" cy="165" r="12" fill="#eff6ff" stroke="#93c5fd" stroke-width="1.5"/>
                        
                        <!-- Head & Face -->
                        <rect x="90" y="75" width="80" height="62" rx="28" fill="#ffffff" stroke="#cbd5e1" stroke-width="3" />
                        <rect x="98" y="83" width="64" height="42" rx="18" fill="#1e3a8a" />
                        
                        <!-- Glowing Eyes -->
                        <circle cx="116" cy="104" r="8" fill="#38bdf8" />
                        <circle cx="114" cy="102" r="3" fill="#ffffff" />
                        <circle cx="144" cy="104" r="8" fill="#38bdf8" />
                        <circle cx="142" cy="102" r="3" fill="#ffffff" />
                        
                        <!-- Smiling Mouth -->
                        <path d="M125 116 Q130 121 135 116" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" fill="none" />

                        <!-- Antenna -->
                        <rect x="128" y="58" width="4" height="18" rx="2" fill="#cbd5e1" />
                        <ellipse cx="130" cy="56" rx="10" ry="4" fill="#e2e8f0" />

                        <!-- Ear Cups / Headphones -->
                        <rect x="83" y="90" width="8" height="30" rx="4" fill="#93c5fd" />
                        <rect x="169" y="90" width="8" height="30" rx="4" fill="#93c5fd" />
                        
                        <!-- Floating Left/Right Arms -->
                        <rect x="78" y="152" width="14" height="40" rx="7" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="2" transform="rotate(-10 78 152)" />
                        <rect x="168" y="152" width="14" height="40" rx="7" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="2" transform="rotate(10 168 152)" />
                      </svg>
                    </div>

                    <!-- Action Trigger Link -->
                    <button class="open-assistant-btn">
                      Open AI Assistant <i class="bi bi-arrow-right"></i>
                    </button>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>  

        <!-- Bottom Grid: Modules and AI[cite: 2] -->
        <div class="row g-4 mb-4">
          <div class="col-lg-6 col-md-6 col-sm-12 col-12">
            <!-- ERP Visual Breakdown -->
            
          </div>

          <div class="col-lg-6 col-md-6 col-sm-12 col-12">
            
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12 col-12">
            
          </div>
        </div>
      </div>
      <!-- <div class="tab-pane fade show" id="content-approvals" role="tabpanel" aria-labelledby="tab-notification">
        <h2>Pending Approvals</h2>
      </div> -->
      <!-- <div class="tab-pane fade show" id="content-tasks" role="tabpanel" aria-labelledby="tab-notification">
        <h2>Unread Task</h2>
      </div> -->
      <div class="tab-pane fade show" id="content-issues" role="tabpanel" aria-labelledby="tab-notification">
        <div class="dashboard-card">
          <!-- Header Section -->
          <div class="d-flex align-items-center gap-2 mb-4">
            <h1 class="h5 fw-bold text-dark mb-0">Module 4 - Customer Data Check</h1>
            <span class="checked-badge">25 Checked</span>
          </div>

          <!-- AI Summary Callout -->
          <div class="ai-summary-box mb-4">
            <h2 class="ai-title">AI Summary</h2>
            <p class="ai-desc">
              Out of 27 customer records, only 2 are complete, with 25 missing key fields—primarily payment_terms and credit_limit, each absent in 25 records. Nine records have critical issues with 3+ missing fields, posing risks to credit and billing processes. We recommend prioritizing the update of payment_terms and credit_limit data to ensure accurate financial assessments and streamline operations.
            </p>
          </div>

          <!-- Record Completeness Section -->
          <div class="mb-4">
            <div class="section-title">
              <span class="title-underline">Record Completeness</span>
            </div>
            <div class="row g-3 mt-4">
              <div class="col-12 col-md-4">
                <div class="kpi-card kpi-complete">
                  <div class="kpi-number">2</div>
                  <div class="kpi-label">COMPLETE RECORDS</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="kpi-card kpi-incomplete">
                  <div class="kpi-number">25</div>
                  <div class="kpi-label">INCOMPLETE RECORDS</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="kpi-card kpi-critical-summary">
                  <div class="kpi-number">9</div>
                  <div class="kpi-label">CRITICAL ISSUES</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Missing Fields Breakdown Section -->
          <div class="mb-4">
            <div class="section-title">Missing Fields Breakdown</div>
            <div class="row g-3">
              <div class="col-6 col-md-3">
                <div class="field-card">
                  <div class="field-number">25</div>
                  <div class="field-label">PAYMENT TERMS</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="field-card">
                  <div class="field-number">25</div>
                  <div class="field-label">CREDIT LIMIT</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="field-card">
                  <div class="field-number">9</div>
                  <div class="field-label">SHIPPING ADDRESS</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="field-card">
                  <div class="field-number">7</div>
                  <div class="field-label">BILLING ADDRESS</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Data Table Section -->
          <div class="table-responsive border rounded">
            <table class="table custom-table table-hover mb-0">
              <thead>
                <tr>
                  <th style="width: 28%;">Customer</th>
                  <th style="width: 44%;">Missing Fields</th>
                  <th style="width: 18%;">Credit Limit Breach</th>
                  <th style="width: 10%;" class="text-end">Severity</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="fw-semibold">Kumar Sharma Enterprises</td>
                  <td>
                    <span class="field-tag">Shipping_address</span>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-critical">CRITICAL</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">Customer 27</td>
                  <td>
                    <span class="field-tag">Billing_address</span>
                    <span class="field-tag">Shipping_address</span>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-critical">CRITICAL</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">Customer 31</td>
                  <td>
                    <span class="field-tag">Billing_address</span>
                    <span class="field-tag">Shipping_address</span>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-critical">CRITICAL</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">Customer 33</td>
                  <td>
                    <span class="field-tag">Billing_address</span>
                    <span class="field-tag">Shipping_address</span>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-critical">CRITICAL</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">Customer 35</td>
                  <td>
                    <span class="field-tag">Billing_address</span>
                    <span class="field-tag">Shipping_address</span>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-critical">CRITICAL</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">INSTAKART SERVICES PRIVATE LIMITED</td>
                  <td>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-warning">WARNING</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">Mittal Enterprises</td>
                  <td>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-warning">WARNING</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">Delta Technologies Pvt Ltd</td>
                  <td>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-warning">WARNING</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">TCS</td>
                  <td>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-warning">WARNING</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">Testing company</td>
                  <td>
                    <span class="field-tag">Payment_terms</span>
                    <span class="field-tag">Credit_limit</span>
                  </td>
                  <td>-</td>
                  <td class="text-end"><span class="severity-pill severity-warning">WARNING</span></td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
      <div class="tab-pane fade show" id="content-ratings" role="tabpanel" aria-labelledby="tab-notification">
        <div class="dashboard-card">
          <!-- Header Section -->
          <div class="d-flex align-items-center gap-2 mb-3">
            <h1 class="h5 fw-bold text-dark mb-0">Module 7 - Customer Payment Rating</h1>
            <span class="checked-badge">25 Checked</span>
          </div>

          <!-- AI Summary Box -->
          <div class="ai-summary-box mb-4">
            <h2 class="ai-title">AI Summary</h2>
            <p class="ai-desc">
              Out of 27 customer records, payment delay data is available for only 2. The total revenue impact is ₹1,019,652.70 across 16 overdue invoices. We recommend prioritizing follow-ups for high-risk accounts and ensuring payment term agreements are updated to mitigate risk.
            </p>
          </div>

          <!-- 8 Metric Cards Grid -->
          <div class="row g-2 mb-4">
            <div class="col-6 col-md-3">
              <div class="kpi-card kpi-green">
                <div class="kpi-number">1019652.70</div>
                <div class="kpi-label">TOTAL OVERDUE AMOUNT</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="kpi-card kpi-red">
                <div class="kpi-number">1019652.70</div>
                <div class="kpi-label">OVERDUE AMOUNT &gt; 30 DAYS</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="kpi-card kpi-light-red">
                <div class="kpi-number">16</div>
                <div class="kpi-label">TOTAL OVERDUE INVOICES</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="kpi-card kpi-purple">
                <div class="kpi-number">0</div>
                <div class="kpi-label">TOTAL INVOICES &gt; 30 DAYS</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="kpi-card kpi-yellow">
                <div class="kpi-number">0</div>
                <div class="kpi-label">AVERAGE DELAY DAYS</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="kpi-card kpi-pink">
                <div class="kpi-number">0</div>
                <div class="kpi-label">BAD</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="kpi-card kpi-orange">
                <div class="kpi-number">0</div>
                <div class="kpi-label">POOR/FAIR</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="kpi-card kpi-blue">
                <div class="kpi-number">25</div>
                <div class="kpi-label">NO DATA</div>
              </div>
            </div>
          </div>

          <!-- Main Table Section -->
          <div class="table-responsive border rounded">
            <table class="table custom-table mb-0">
              <thead>
                <tr>
                  <th style="width: 4%;">#</th>
                  <th style="width: 22%;">Customer</th>
                  <th style="width: 12%;">Action</th>
                  <th style="width: 14%;">Rating</th>
                  <th style="width: 22%;">Critical Breakdown</th>
                  <th style="width: 8%;">Invoices</th>
                  <th style="width: 8%;">Disputed</th>
                  <th style="width: 10%;">Recommendations</th>
                </tr>
              </thead>
              <tbody>

                <!-- Row 1 (Master Row) -->
                <tr>
                  <td class="fw-semibold">1</td>
                  <td class="fw-bold text-dark">Kumar Sharma Enterprises</td>
                  <td>
                    <button class="btn btn-action-pink" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRow1">
                      Collect View
                    </button>
                  </td>
                  <td>
                    <div class="rating-stars mb-1">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                    </div>
                    <span class="score-badge">12/100</span>
                  </td>
                  <td>
                    <div class="fw-bold text-dark" style="font-size: 11px;"># Invoices Delay: 2</div>
                    <div class="text-danger fw-semibold" style="font-size: 10px;">Avg Delay: 0 Days</div>
                    <div class="text-muted" style="font-size: 10px;">Total Invoices: 2 | Total Invoices Delayed: 2</div>
                  </td>
                  <td class="fw-semibold">2</td>
                  <td class="text-danger fw-bold">1019652.70</td>
                  <td>-</td>
                </tr>

                <!-- Row 1 Collapsible Sub-Table -->
                <tr class="p-0 border-0">
                  <td colspan="8" class="p-0 border-0">
                    <div class="collapse show" id="collapseRow1">
                      <table class="w-100">
                        <tr class="subtable-header-row">
                          <td style="width: 25%;">Invoice No. / Date</td>
                          <td style="width: 25%;">Due Date / Ref</td>
                          <td style="width: 25%;">Total / Bal.</td>
                          <td style="width: 25%;">Status</td>
                        </tr>
                        <tr class="subtable-row">
                          <td>2024-25/110</td>
                          <td>2024-09-02</td>
                          <td class="text-danger fw-semibold">12.0</td>
                          <td class="text-status-pending">Pending</td>
                        </tr>
                        <tr class="subtable-row">
                          <td>2024-25/89</td>
                          <td>2024-08-20</td>
                          <td class="text-danger fw-semibold">12.0</td>
                          <td class="text-status-pending">Pending</td>
                        </tr>
                        <tr class="subtable-row">
                          <td>2024-25/74</td>
                          <td>2024-08-08</td>
                          <td class="text-danger fw-semibold">12.0</td>
                          <td class="text-status-pending">Pending</td>
                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>

                <!-- Row 2 -->
                <tr>
                  <td class="fw-semibold">2</td>
                  <td class="fw-bold text-dark">Customer 27</td>
                  <td>
                    <button class="btn btn-action-pink" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRow2">
                      Collect View
                    </button>
                  </td>
                  <td>
                    <div class="rating-stars mb-1">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                    </div>
                    <span class="score-badge">12/100</span>
                  </td>
                  <td>
                    <div class="fw-bold text-dark" style="font-size: 11px;"># Invoices Delay: 2</div>
                    <div class="text-danger fw-semibold" style="font-size: 10px;">Avg Delay: 0 Days</div>
                    <div class="text-muted" style="font-size: 10px;">Total Invoices: 2 | Total Invoices Delayed: 2</div>
                  </td>
                  <td class="fw-semibold">2</td>
                  <td>-</td>
                  <td>-</td>
                </tr>

                <!-- Row 2 Collapsible Sub-Table -->
                <tr class="p-0 border-0">
                  <td colspan="8" class="p-0 border-0">
                    <div class="collapse" id="collapseRow2">
                      <table class="w-100">
                        <tr class="subtable-header-row">
                          <td style="width: 25%;">Invoice No. / Date</td>
                          <td style="width: 25%;">Due Date / Ref</td>
                          <td style="width: 25%;">Total / Bal.</td>
                          <td style="width: 25%;">Status</td>
                        </tr>
                        <tr class="subtable-row">
                          <td>2024-25/115</td>
                          <td>2024-09-10</td>
                          <td class="text-danger fw-semibold">15.0</td>
                          <td class="text-status-pending">Pending</td>
                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>

                <!-- Row 3 -->
                <tr>
                  <td class="fw-semibold">3</td>
                  <td class="fw-bold text-dark">Customer 31</td>
                  <td>
                    <button class="btn btn-action-pink" type="button">Collect View</button>
                  </td>
                  <td>
                    <div class="rating-stars mb-1">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                    </div>
                    <span class="score-badge">12/100</span>
                  </td>
                  <td>
                    <div class="fw-bold text-dark" style="font-size: 11px;"># Invoices Delay: 2</div>
                    <div class="text-danger fw-semibold" style="font-size: 10px;">Avg Delay: 0 Days</div>
                  </td>
                  <td class="fw-semibold">2</td>
                  <td>-</td>
                  <td>-</td>
                </tr>

              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
</div>

<!-- Scripts -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script> -->
<!-- Chartlist charts -->
<script src="assets/bower_components/chartist/js/chartist.js"></script>
<script src="assets/pages/chart/chartlist/js/chartist-plugin-threshold.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/chart/chartlist/chartlist-custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    
    // 1. DOUGHNUT CHART (Fixed Options for Overflow)
    const ctxDoughnut = document.getElementById('priorityDoughnutChart').getContext('2d');
    new Chart(ctxDoughnut, {
      type: 'doughnut',
      data: {
        labels: ['Critical', 'Warning', 'Info', 'Healthy'],
        datasets: [{
          data: [15, 8, 2, 53],
          backgroundColor: ['#dc3545', '#ffc107', '#0dcaf0', '#198754'],
          borderWidth: 2,
          borderColor: '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false, // Core fix: stops growing infinitely
        plugins: {
          legend: {
            position: 'right',
            labels: { boxWidth: 10, font: { size: 11 } }
          }
        },
        cutout: '75%'
      }
    });

    // 2. LINE CHART (Fixed Height & Responsive Scales)
    const ctxLine = document.getElementById('notificationTrendChart').getContext('2d');
    new Chart(ctxLine, {
      type: 'line',
      data: {
        labels: ['30 Apr', '1 May', '2 May', '3 May', '4 May', '5 May', 'Today'],
        datasets: [
          {
            label: 'Critical Items',
            data: [10, 15, 8, 12, 14, 9, 15],
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.05)',
            tension: 0.4,
            borderWidth: 2,
            fill: true
          },
          {
            label: 'Total Warnings',
            data: [5, 6, 9, 7, 10, 8, 8],
            borderColor: '#ffc107',
            backgroundColor: 'transparent',
            tension: 0.4,
            borderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false, // Prevents vertical spill
        plugins: {
          legend: {
            position: 'top',
            labels: { boxWidth: 10, font: { size: 11 } }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9' },
            ticks: { font: { size: 10 } }
          },
          x: {
            grid: { display: false },
            ticks: { font: { size: 10 } }
          }
        }
      }
    });

    // 3. HORIZONTAL BAR CHART (Action Breakdowns)
    const ctxBar = document.getElementById('moduleBarChart').getContext('2d');
    new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: ['Goods Receipt', 'Leaves', 'PO Orders', 'Quotations'],
        datasets: [{
          data: [8, 1, 3, 3],
          backgroundColor: ['#198754', '#0d6efd', '#0dcaf0', '#ffc107'],
          borderRadius: 4,
          barThickness: 10
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
          y: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
      }
    });

  });
</script>


<!-- India map List JS -->
<script>
// 1. ALL INDIA STATES & UTS COORDINATES DATABASE (Complete List)
const stateCoordinates = {
  // STATES
  "AP": [15.9129, 79.7400], "ANDHRA PRADESH": [15.9129, 79.7400],
  "ARUNACHAL PRADESH": [28.2180, 94.7278], "AR": [28.2180, 94.7278],
  "ASSAM": [26.2006, 92.9376], "AS": [26.2006, 92.9376],
  "BIHAR": [25.0961, 85.3131], "BR": [25.0961, 85.3131],
  "CHHATTISGARH": [21.2787, 81.8661], "CG": [21.2787, 81.8661],
  "GOA": [15.2993, 74.1240], "GA": [15.2993, 74.1240],
  "GUJARAT": [22.2587, 71.1924], "GJ": [22.2587, 71.1924],
  "HARYANA": [29.0588, 76.0856], "HR": [29.0588, 76.0856],
  "HIMACHAL PRADESH": [31.1048, 77.1734], "HP": [31.1048, 77.1734],
  "JHARKHAND": [23.6102, 85.2799], "JH": [23.6102, 85.2799],
  "KARNATAKA": [15.3173, 75.7139], "KA": [15.3173, 75.7139], "BENGALURU": [12.9716, 77.5946],
  "KERALA": [10.8505, 76.2711], "KL": [10.8505, 76.2711],
  "MP": [23.2599, 77.4126], "MADHYA PRADESH": [23.2599, 77.4126],
  "MAHARASHTRA": [19.7515, 75.7139], "MH": [19.7515, 75.7139], "MUMBAI": [19.0760, 72.8777],
  "MANIPUR": [24.6637, 93.9063], "MN": [24.6637, 93.9063],
  "MEGHALAYA": [25.5705, 91.8800], "ML": [25.5705, 91.8800],
  "MIZORAM": [23.1645, 92.9376], "MZ": [23.1645, 92.9376],
  "NAGALAND": [26.1584, 94.5624], "NL": [26.1584, 94.5624],
  "ODISHA": [20.9517, 85.0985], "OR": [20.9517, 85.0985], "ORISSA": [20.9517, 85.0985],
  "PUNJAB": [31.1471, 75.3412], "PB": [31.1471, 75.3412],
  "RAJASTHAN": [27.0238, 74.2179], "RJ": [27.0238, 74.2179],
  "SIKKIM": [27.5330, 88.5122], "SK": [27.5330, 88.5122],
  "TAMIL NADU": [11.1271, 78.6569], "TN": [11.1271, 78.6569], "CHENNAI": [13.0827, 80.2707],
  "TELANGANA": [18.1124, 79.0193], "TS": [18.1124, 79.0193], "HYDERABAD": [17.3850, 78.4867],
  "TRIPURA": [23.9408, 91.9882], "TR": [23.9408, 91.9882],
  "UP": [26.8467, 80.9462], "UTTAR PRADESH": [26.8467, 80.9462],
  "UK": [30.0668, 79.0193], "UTTARAKHAND": [30.0668, 79.0193],
  "WEST BENGAL": [22.9868, 87.8550], "WB": [22.9868, 87.8550],

  // UNION TERRITORIES (UTs)
  "DELHI": [28.6139, 77.2090], "DL": [28.6139, 77.2090],
  "JAMMU AND KASHMIR": [33.7782, 76.5762], "JK": [33.7782, 76.5762],
  "LADAKH": [34.1526, 77.5771], "LA": [34.1526, 77.5771],
  "ANDAMAN AND NICOBAR": [11.7401, 92.6586], "AN": [11.7401, 92.6586],
  "CHANDIGARH": [30.7333, 76.7794], "CH": [30.7333, 76.7794],
  "PUDUCHERRY": [11.9416, 79.8083], "PY": [11.9416, 79.8083],
  "DADRA AND NAGAR HAVELI AND DAMAN AND DIU": [20.4283, 72.8397], "DN": [20.4283, 72.8397],
  "LAKSHADWEEP": [10.5667, 72.6417], "LD": [10.5667, 72.6417]
};

// 2. Risk Config
const riskConfig = {
  "Low Risk": { color: "#22c55e", badgeClass: "risk-low" },
  "Medium Risk": { color: "#eab308", badgeClass: "risk-medium" },
  "High Risk": { color: "#f97316", badgeClass: "risk-high" },
  "Very High Risk": { color: "#ef4444", badgeClass: "risk-v-high" }
};

// 3. Map Initialize
const map = L.map('india-map').setView([22.5937, 78.9629], 4.8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 18,
  attribution: '© OpenStreetMap'
}).addTo(map);

setTimeout(() => { map.invalidateSize(); }, 200);

// 4. MAIN JS FUNCTION
function addStateToMap(stateName, riskLevel = "Low Risk", salesAmount = "₹0") {
  const key = stateName.trim().toUpperCase();
  
  if (stateCoordinates[key]) {
    const coords = stateCoordinates[key];
    const config = riskConfig[riskLevel] || riskConfig["Low Risk"];

    // A. Map Circle Dot
    const circle = L.circleMarker(coords, {
      color: '#ffffff',
      weight: 2,
      fillColor: config.color,
      fillOpacity: 0.95,
      radius: 9
    }).addTo(map);

    // B. Hover Popup
    const popupContent = `
      <div style="text-align: center; padding: 2px;">
        <strong style="font-size: 14px; color: #1e293b;">${stateName}</strong><br/>
        <span style="color: #64748b; font-size: 12px;">Total Sales: <strong>${salesAmount}</strong></span><br/>
        <span style="color: ${config.color}; font-size: 11px; font-weight: bold;">${riskLevel}</span>
      </div>
    `;

    circle.bindPopup(popupContent, { closeButton: false, offset: [0, -5] });

    circle.on('mouseover', function () { this.openPopup(); });
    circle.on('mouseout', function () { this.closePopup(); });

    // C. Right-Side UI Card
    const listContainer = document.getElementById('risk-list-container');
    const riskItemHTML = `
      <div class="risk-item">
        <div>
          <div class="d-flex align-items-center">
            <span class="status-dot" style="background-color: ${config.color};"></span>
            <strong class="city-name">${stateName}</strong>
          </div>
          <small class="sales-text ms-3">Sales: ${salesAmount}</small>
        </div>
        <span class="risk-label ${config.badgeClass}">${riskLevel}</span>
      </div>
    `;

    listContainer.insertAdjacentHTML('afterbegin', riskItemHTML);

  } else {
    console.error(`"${stateName}" ke coordinates stateCoordinates me missing hain.`);
  }
}

// ===============================================================
// BYDEFAULT ONLY 4 STATES RENDER HONGI
// ===============================================================
addStateToMap("UP", "Very High Risk", "₹26.9 Lakhs");
addStateToMap("MP", "Medium Risk", "₹9.2 Lakhs");
addStateToMap("Rajasthan", "Low Risk", "₹14.0 Lakhs");
addStateToMap("AP", "Low Risk", "₹30.0 Lakhs");
addStateToMap("Delhi", "Medium Risk", "₹50.0 Lakhs");
addStateToMap("MH", "Low Risk", "₹60.0 Lakhs");

// Future me koi naye state add karne ke liye aap JS me bas is tarah function call karenge:
// addStateToMap("UK", "Medium Risk", "₹50.9 Lakhs");
// addStateToMap("Bihar", "High Risk", "₹12.0 Lakhs");
</script>