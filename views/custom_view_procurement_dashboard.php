<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Procurement Intelligence Dashboard</title>


  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">

  <style>
    body {
      background-color: #f4f7fc;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .dashboard-container {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.04);
      border: 1px solid #e2e8f0;
    }

    /* Tab Navigation Box Layout */
    .custom-tabs-container {
      display: flex;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
      background: #ffffff;
    }

    .custom-tab-link {
      flex: 1;
      background: none;
      border: none;
      border-right: 1px solid #e2e8f0;
      padding: 16px 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      text-align: left;
      transition: all 0.2s ease;
      text-decoration: none;
      color: #475569 !important;
    }

    .custom-tab-link:last-child {
      border-right: none;
    }

    /* Bootstrap 5.0.1 Active State Overrides */
    .custom-tabs-container .custom-tab-link.active {
      background-color: #0d6efd !important;
      color: #ffffff !important;
    }

    .custom-tabs-container .custom-tab-link.active .tab-title,
    .custom-tabs-container .custom-tab-link.active .tab-main-count,
    .custom-tabs-container .custom-tab-link.active .tab-sub-text,
    .custom-tabs-container .custom-tab-link.active i {
      color: #ffffff !important;
    }

    .tab-icon-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .tab-title {
      font-size: 13px;
      font-weight: 500;
      color: #64748b;
      line-height: 1.2;
    }

    .tab-main-count {
      font-size: 18px;
      font-weight: 700;
      color: #1e293b;
      display: block;
      line-height: 1.2;
      margin-top: 2px;
    }

    .tab-sub-text {
      font-size: 11px;
      color: #64748b;
      display: block;
      margin-top: 1px;
    }

    /* Cards Layout: Icon Left, Text & Numbers Right with Text-Align Left */
    .module-badge-box {
      border-radius: 8px;
      border: 1px solid #e2e8f0;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      gap: 16px;
      text-align: left;
    }

    /* AI Summary Gradient Box */
    .ai-summary-box {
      background: linear-gradient(135deg, #f0f7ff 0%, #e1effe 100%);
      border: 1px solid rgba(13, 110, 253, 0.1);
      border-radius: 12px;
    }

    .progress-sm {
      height: 8px;
      border-radius: 4px;
    }
    .ai-summary-box{
      min-height: 293px;
    }


    @media (max-width: 768px) {
      .custom-tabs-container {
        flex-direction: column;
      }
      .custom-tab-link {
        border-right: none;
        border-bottom: 1px solid #e2e8f0;
        justify-content: flex-start;
        padding: 12px 20px;
      }
    }
  </style>
</head>
<body>

<div class="mt-3">
  <div class="dashboard-container p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h5 fw-bold text-dark mb-0">Procurement Intelligence Dashboard</h1>
        <p class="text-muted small mb-0" style="font-size: 12px;">23 Apr 2026 • Last refreshed: 09:36 am</p>
      </div>
      <button class="btn btn-primary btn-sm px-3 py-1" style="border-radius: 6px; font-size: 14px;">
        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
      </button>
    </div>

    <div class="custom-tabs-container nav mb-4" id="dashboardTabs" role="tablist">

      <a class="custom-tab-link active" id="tab-notification" data-bs-toggle="tab" href="#content-notification" role="tab" aria-controls="content-notification" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
        <div class="tab-icon-wrapper"><i class="bi bi-bell fs-4"></i></div>
        <div>
          <span class="tab-main-count">10</span>
          <span class="tab-title">Notification</span>
          <span class="tab-sub-text">15 Action</span>
        </div>
      </a>

      <a class="custom-tab-link" id="tab-approvals" data-bs-toggle="tab" href="#content-approvals" role="tab" aria-controls="content-approvals" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
        <div class="tab-icon-wrapper"><i class="bi bi-clock text-primary fs-4"></i></div>
        <div>
          <span class="tab-main-count">5</span>
          <span class="tab-title">Pending Approvals</span>
          <span class="tab-sub-text text-danger fw-bold">4 Critical</span>
        </div>
      </a>

      <a class="custom-tab-link" id="tab-tasks" data-bs-toggle="tab" href="#content-tasks" role="tab" aria-controls="content-tasks" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
        <div class="tab-icon-wrapper"><i class="bi bi-clipboard-check text-primary fs-4"></i></div>
        <div>
          <span class="tab-main-count">3</span>
          <span class="tab-title">Unread Tasks</span>
          <span class="tab-sub-text text-danger fw-bold">3 Critical</span>
        </div>
      </a>

      <a class="custom-tab-link" id="tab-issues" data-bs-toggle="tab" href="#content-issues" role="tab" aria-controls="content-issues" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
        <div class="tab-icon-wrapper"><i class="bi bi-headset text-primary fs-4"></i></div>
        <div>
          <span class="tab-main-count">25</span>
          <span class="tab-title">Customer Issues</span>
          <span class="tab-sub-text">0 Breach</span>
        </div>
      </a>

      <a class="custom-tab-link" id="tab-ratings" data-bs-toggle="tab" href="#content-ratings" role="tab" aria-controls="content-ratings" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
        <div class="tab-icon-wrapper"><i class="bi bi-star text-primary fs-4"></i></div>
        <div>
          <span class="tab-main-count">27</span>
          <span class="tab-title">Customer Ratings</span>
          <span class="tab-sub-text text-success">⭐ 4.8 Avg</span>
        </div>
      </a>

    </div>

    <div class="tab-content pt-2" id="dashboardTabsContent">

      <div class="tab-pane fade show active" id="content-notification" role="tabpanel" aria-labelledby="tab-notification">

        <div class="d-flex align-items-center mb-4">
          <h2 class="h6 fw-bold mb-0 text-dark me-2">Module 1 — Notifications</h2>
          <span class="badge bg-light text-primary border border-primary px-2" style="font-size: 11px;">unread</span>
        </div>

        <div class="row g-3 mb-4">

          <div class="col-6 col-lg-3">
            <div class="module-badge-box" style="background-color: #fff3cd; border-color: #ffecb5;">
              <div class="p-2 bg-white rounded shadow-sm text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-bell-fill fs-5"></i>
              </div>
              <div>
                <span class="fw-bold fs-4 d-block text-dark mb-0 lh-1">15</span>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 10px; letter-spacing: 0.5px;">ACTION UNREAD</span>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <div class="module-badge-box bg-light border-light">
              <div class="p-2 bg-white rounded shadow-sm text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-info-circle-fill fs-5"></i>
              </div>
              <div>
                <span class="fw-bold fs-4 d-block text-dark mb-0 lh-1">0</span>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 10px; letter-spacing: 0.5px;">INFO UNREAD</span>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <div class="module-badge-box" style="background-color: #f8d7da; border-color: #f5c2c7;">
              <div class="p-2 bg-white rounded shadow-sm text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-calendar-event-fill fs-5"></i>
              </div>
              <div>
                <span class="fw-bold fs-4 d-block text-dark mb-0 lh-1">53</span>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 10px; letter-spacing: 0.5px;">OLDEST (DAYS)</span>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <div class="module-badge-box" style="background-color: #f3e8ff; border-color: #e9d5ff;">
              <div class="p-2 bg-white rounded shadow-sm text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-arrow-repeat fs-5"></i>
              </div>
              <div>
                <span class="fw-bold fs-4 d-block text-dark mb-0 lh-1">2</span>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 10px; letter-spacing: 0.5px;">REPEATED</span>
              </div>
            </div>
          </div>

        </div>

        <div class="alert alert-light border p-3 rounded mb-4">
          <div class="d-flex align-items-center mb-2">
            <span class="badge bg-danger text-white rounded-pill fw-bold text-center me-3" style="font-size: 10px; width: 68px;">CRITICAL</span>
            <span class="text-dark fw-normal" style="font-size: 13px;">15 ACTION notification(s) unread for 3+ days</span>
          </div>
          <div class="d-flex align-items-center mb-2">
            <span class="badge bg-warning text-dark rounded-pill fw-bold text-center me-3" style="font-size: 10px; width: 68px;">WARNING</span>
            <span class="text-dark fw-normal" style="font-size: 13px;">Oldest unread notification is 53 days old</span>
          </div>
          <div class="d-flex align-items-center">
            <span class="badge bg-warning text-dark rounded-pill fw-bold text-center me-3" style="font-size: 10px; width: 68px;">WARNING</span>
            <span class="text-dark fw-normal" style="font-size: 13px;">2 repeated alert pattern(s) detected — same issue triggered multiple times</span>
          </div>
        </div>

        <h3 class="h6 fw-bold text-dark mb-3">Age of unread notifications</h3>
        <div class="row g-3 mb-4 customer-issues">
          <div class="col-md-4">
            <div class="p-3 border rounded d-flex align-items-center justify-content-between purple-light">
              <div class="d-flex align-items-center gap-2"><i class="bi bi-calendar-check text-success fs-5"></i><span class="text-dark" style="font-size: 14px;">Today</span></div>
              <span class="fs-5 fw-bold text-dark">0</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 border rounded d-flex align-items-center justify-content-between blue-light">
              <div class="d-flex align-items-center gap-2"><i class="bi bi-calendar-week text-primary fs-5"></i><span class="text-dark" style="font-size: 14px;">This Week</span></div>
              <span class="fs-5 fw-bold text-dark">4</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 border rounded d-flex align-items-center justify-content-between green-light">
              <div class="d-flex align-items-center gap-2"><i class="bi bi-calendar-month text-danger fs-5"></i><span class="text-dark" style="font-size: 14px;">This Month</span></div>
              <span class="fs-5 fw-bold text-dark">1</span>
            </div>
          </div>
        </div>

        <div class="row g-4 mb-4">
          <div class="col-12 col-lg-7">
            <h3 class="h6 fw-bold text-dark mb-3">Action Items by Module (need your response)</h3>
            <div class="table-responsive border rounded overflow-hidden">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted" style="font-size: 12px;">
                  <tr>
                    <th scope="col" class="py-3 px-3">ERP MODULE</th>
                    <th scope="col" class="text-end py-3 px-3">ACTION COUNT</th>
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
          <div class="col-12 col-lg-5">
            <h3 class="h6 fw-bold text-dark mb-3">AI Recommendations</h3>
            <div class="ai-summary-box p-4 d-flex gap-3">
              <div class="text-primary"><i class="bi bi-stars fs-4"></i></div>
              <div>
                <h4 class="h6 fw-bold text-primary mb-2">AI Summary</h4>
                <p class="text-dark mb-2" style="font-size: 13px; line-height: 1.6;">There are 15 unread ACTION notifications requiring response, with the majority in goods_receipt_notes (8), followed by purchase_orders (3), quotations (3), and employee_leaves (1). Ten of these are older than 30 days, indicating significant backlog. A recurring issue is "Next Process Not Set," appearing 4 times each in goods_receipt_notes for codes -127004 and -PO-01073. To reduce delays, prioritize resolving the "Next Process Not Set" errors in goods_receipt_notes to clear the oldest and most frequent blockers.</p>
                <div class="p-2.5 bg-white border rounded text-dark fw-normal mt-2" style="font-size: 12px; padding: 10px;">
                  💡 <strong>Priority Action:</strong> To reduce delays, prioritize resolving the "Next Process Not Set" errors.
                </div>
              </div>
            </div>
          </div>
        </div>

        <h3 class="h6 fw-bold text-dark mb-3">Top Repeated Alert Patterns</h3>
        <div class="table-responsive border rounded overflow-hidden mb-4">
          <table class="table align-middle mb-0" style="font-size: 13px;">
            <thead class="table-light text-muted" style="font-size: 12px;">
              <tr><th scope="col" class="py-3 px-3">ERP REPEATED ALERTS</th><th scope="col" class="py-3 px-3">TIMES SEEN</th><th scope="col" class="py-3 px-3">SAMPLE MESSAGE</th></tr>
            </thead>
            <tbody>
              <tr><td class="px-3"><i class="bi bi-exclamation-triangle text-danger me-2"></i> Goods Receipt Notes</td><td class="px-3 fw-bold">4</td><td class="px-3 text-muted">Next Process Not Set -127004</td></tr>
              <tr><td class="px-3"><i class="bi bi-exclamation-triangle text-danger me-2"></i> Goods Receipt Notes</td><td class="px-3 fw-bold">4</td><td class="px-3 text-muted">Next Process Not Set -PO-01073</td></tr>
            </tbody>
          </table>
        </div>

        <h3 class="h6 fw-bold text-dark mb-3">ERP Breakdown</h3>
        <div class="p-3 border rounded bg-white">
          <div class="row align-items-center mb-3">
            <div class="col-4 col-md-3" style="font-size: 13px;"><i class="bi bi-box-seam text-success me-2"></i> Goods Receipt Notes</div>
            <div class="col-6 col-md-8"><div class="progress progress-sm bg-light"><div class="progress-bar bg-success" style="width: 80%;"></div></div></div>
            <div class="col-2 col-md-1 text-end fw-bold text-dark" style="font-size: 13px;">8</div>
          </div>
          <div class="row align-items-center mb-3">
            <div class="col-4 col-md-3" style="font-size: 13px;"><i class="bi bi-cart-fill text-info me-2"></i> Purchase Orders</div>
            <div class="col-6 col-md-8"><div class="progress progress-sm bg-light"><div class="progress-bar bg-info" style="width: 35%;"></div></div></div>
            <div class="col-2 col-md-1 text-end fw-bold text-dark" style="font-size: 13px;">3</div>
          </div>
        </div>

      </div>

      <div class="tab-pane fade" id="content-approvals" role="tabpanel" aria-labelledby="tab-approvals">
        <div class="p-5 text-center border rounded bg-light">
          <i class="bi bi-clock-history text-primary fs-1 mb-3 d-block"></i>
          <h5 class="fw-bold text-dark">Module 2 — Pending Approvals</h5>
          <p class="text-muted mb-0">Yahan aapka Pending Approvals module ka code layout parse hoga.</p>
        </div>
      </div>

      <div class="tab-pane fade" id="content-tasks" role="tabpanel" aria-labelledby="tab-tasks">
        <div class="p-5 text-center border rounded bg-light">
          <i class="bi bi-clipboard-check text-primary fs-1 mb-3 d-block"></i>
          <h5 class="fw-bold text-dark">Module 3 — Unread Tasks</h5>
          <p class="text-muted mb-0">Yahan aapka Unread Tasks component configure kiya jayega.</p>
        </div>
      </div>

      <div class="tab-pane fade" id="content-issues" role="tabpanel" aria-labelledby="tab-issues">
        <div class="p-5 text-center border rounded bg-light">
          <i class="bi bi-headset text-primary fs-1 mb-3 d-block"></i>
          <h5 class="fw-bold text-dark">Module 4 — Customer Issues</h5>
          <p class="text-muted mb-0">Customer ticket reporting pipeline.</p>
        </div>
      </div>

      <div class="tab-pane fade" id="content-ratings" role="tabpanel" aria-labelledby="tab-ratings">
        <div class="p-5 text-center border rounded bg-light">
          <i class="bi bi-star text-primary fs-1 mb-3 d-block"></i>
          <h5 class="fw-bold text-dark">Module 5 — Customer Ratings</h5>
          <p class="text-muted mb-0">System performance and feedback metrics summary panel.</p>
        </div>
      </div>

    </div>

  </div>
</div>

</body>
</html>
