
<div class="row mt-3">
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Bank Balance'); ?></p>
        <h3 class="f-w-600 f-50 mt-3 mb-3"><?= __('₹4.8 Cr'); ?></h3>
        <p class="text-success mb-0 f-w-600"><?= __('Healthy liquidity'); ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Receivables'); ?></p>
        <h3 class="f-w-600 f-50 mt-3 mb-3"><?= __('₹2.1 Cr'); ?></h3>
        <p class="text-danger mb-0 f-w-600"><?= __('Collections pending'); ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Payables'); ?></p>
        <h3 class="f-w-600 f-50 mt-3 mb-3"><?= __('₹1.2 Cr'); ?></h3>
        <p class="text-warning mb-0 f-w-600"><?= __('Vendor payments due'); ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-dashboard-black infisuite-crm-dashboard b-radius-30">
      <div class="card-body table-card">
        <span class="text-white mb-0">
          <div class="p-status bg-c-green d-inline-block m-r-5 blink-fade"></div>
          <?= __('LIVE CRM INSIGHT'); ?>
        </span>
        <h6 class="f-w-600 f-28 m-f-16 mt-4 mb-2 text-white leading-relaxed"><?= __('Cash shortfall likely in 14 days if collections slow further.'); ?></h6>
      </div>
    </div>
  </div>
  <div class="col-lg-8 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2 pipeline-flow">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Cashflow Command Center'); ?></h4>
            <p class="f-w-600 f-14 text-muted"><?= __('Inflow vs outflow trend analysis'); ?></p>
          </div>
          <button class="btn bg-green-50 b-radius-20 pt-1 pb-1 border-green-100 p-l-10 p-r-10 text-center"><?= __('Stable Cashflow'); ?></button>
        </div>
        <div id="line-example" style="min-height: 290px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Receivables Aging'); ?></h4>
          </div>
          <span class="f-12 text-danger b-radius-20 bg-red-50 pt-1 pb-1 p-l-10 p-r-10 text-center"><?= __('High Risk'); ?></span>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-success f-w-600"><?= __('0 - 30 Days'); ?></span>
            <span class="f-16 text-success f-w-600">₹82 L</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-danger f-w-600"><?= __('31 - 60 Days'); ?></span>
            <span class="f-16 text-danger f-w-600">₹44 L</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-danger f-w-600"><?= __('61 - 90 Days'); ?></span>
            <span class="f-16 text-danger f-w-600">₹22 L</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-red-100">
            <span class="f-16 text-danger f-w-600"><?= __('90+ Days'); ?></span>
            <span class="f-16 text-danger f-w-600">₹14 L</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Payables Management'); ?></h4>
          </div>
          <a href="" class="f-14 text-primary text-center"><?= __('View Vendors'); ?></a>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Overdue Vendor Payments'); ?></span>
            <span class="f-16 text-danger f-w-600">₹28 L</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Bills Pending Approval'); ?></span>
            <span class="f-16 text-danger f-w-600">38</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-blue-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-dark"><?= __('Payment Queue'); ?></span>
            <span class="f-16 text-primary f-w-600">₹82 L</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-red-100">
            <span class="f-16 text-dark"><?= __('Vendor SLA Compliance'); ?></span>
            <span class="f-16 text-success f-w-600">94%</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= htmlspecialchars(__('Compliance & Audit'), ENT_QUOTES, 'UTF-8'); ?></h4>
          </div>
          <button class="btn bg-green-50 b-radius-20 pt-1 pb-1 border-green-100 p-l-10 p-r-10 text-center">96% Compliant</button>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('GST Filing'); ?></span>
            <span class="f-16 text-success f-w-600"><?= __('Completed'); ?></span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('TDS Filing'); ?></span>
            <span class="f-16 text-danger f-w-600"><?= __('Due Tomorrow'); ?></span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-dark"><?= __('Audit Exceptions'); ?></span>
            <span class="f-16 text-danger f-w-600">6</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-blue-50 b-radius-20 p-3 border-red-100">
            <span class="f-16 text-dark"><?= __('Bank Reconciliation'); ?></span>
            <span class="f-16 text-primary f-w-600">92% Complete</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body table-card">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Live Finance Activity'); ?></h4>
          </div>
          <span class="f-14">
            <div class="p-status bg-c-green d-inline-block m-r-5 blink-fade text-center"></div><?= __('Live'); ?>
          </span>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Payment received from ABC Infra.'); ?></span>

          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-blue-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Vendor invoice approved for processing.'); ?></span>

          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-dark"><?= __('GST return filing reminder triggered.'); ?></span>

          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-red-100">
            <span class="f-16 text-dark"><?= __('Duplicate invoice risk detected for vendor payment.'); ?></span>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-12 col-sm-12 col-12">
    <div class="card p-2 b-radius-30 bg-black">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30 text-white"><?= __('Finance AI Insights'); ?></h4>
          </div>
          <span class="bg-danger b-radius-20 pt-1 pb-1 p-l-10 p-r-10 text-center"><?= __('Live'); ?></span>
        </div>
        <div class="mt-4">
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Vendor duplication risk detected for 2 invoices.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Customer XYZ likely to delay payment beyond 90 days.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Travel reimbursements unusually high this month.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Bank reconciliation mismatch identified in 4 entries.'); ?></p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Cashflow Command Center Chart CSS -->
<link rel="stylesheet" type="text/css" href="assets/bower_components/morris.js/css/morris.css">

<!-- Cashflow Command Center Chart JS -->
<script src="assets/bower_components/raphael/js/raphael.min.js"></script>
<script src="assets/bower_components/morris.js/js/morris.js"></script>
<script src="assets/assets/pages/chart/morris/morris-custom-chart.js"></script>
