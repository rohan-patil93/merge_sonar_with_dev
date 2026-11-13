
<div class="row mt-3">
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('New Leads'); ?></p>
        <h3 class="f-w-600 f-50 mt-2 mb-2">428</h3>
        <p class="text-success mb-0 f-w-600"><i class="ti-arrow-up"></i> 18% this month</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Active Opportunities'); ?></p>
        <h3 class="f-w-600 f-50 mt-2 mb-2">142</h3>
        <p class="text-primary mb-0 f-w-600"><?= __('Healthy Pipeline'); ?></p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Follow-ups Pending'); ?></p>
        <h3 class="f-w-600 f-50 mt-2 mb-2">38</h3>
        <p class="text-warning mb-0 f-w-600"><?= __('Action Required'); ?></p>
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
        <h6 class="f-w-600 f-30 m-f-16 mt-4 mb-2 text-white leading-relaxed"><?= __('Customer ABC engagement dropped by 42%.'); ?></h6>
      </div>
    </div>
  </div>

  <div class="col-lg-8 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2 pipeline-flow">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('CRM Pipeline Flow'); ?></h4>
            <p class="f-w-600 f-14 text-muted"><?= __('Lead to opportunity conversion tracking'); ?></p>
          </div>
          <span class="bg-green-50 b-radius-20 pt-1 pb-1 border-green-100 p-l-10 p-r-10 text-center">36% Conversion Rate</span>
        </div>
        <div class="pipeline-wrapper mt-4">
          <div class="pipeline">
            <div class="step">
                <div class="circle status-blue text-primary">428</div>
                <h3><?= __('Leads'); ?></h3>
            </div>
            <div class="line"></div>
            <div class="step">
                <div class="circle bg-green-50">318</div>
                <h3><?= __('Contacted'); ?></h3>
            </div>
            <div class="line"></div>
            <div class="step">
                <div class="circle bg-yellow-50">186</div>
                <h3><?= __('Qualified'); ?></h3>
            </div>
            <div class="line"></div>
            <div class="step">
                <div class="circle purple">94</div>
                <h3><?= __('Proposal'); ?></h3>
            </div>
            <div class="line"></div>
            <div class="step">
                <div class="circle bg-red-50">52</div>
                <h3><?= __('Won'); ?></h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('CRM Team'); ?></h4>
          </div>
          <span class="f-12 text-primary b-radius-20 status-blue pt-1 pb-1 p-l-10 p-r-10"><?= __('Updated Live'); ?></span>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-success"><?= __('Priya Sharma'); ?></span>
            <span class="f-16 text-success f-w-600">142%</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-success"><?= __('Rohit Mehta'); ?></span>
            <span class="f-16 text-success f-w-600">128%</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-warning"><?= __('Arjun Rao'); ?></span>
            <span class="f-16 text-warning f-w-600">84%</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-red-100">
            <span class="f-16 text-danger"><?= __('Vikram Singh'); ?></span>
            <span class="f-16 text-danger f-w-600">61%</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-12 col-sm-12 col-12">
    <div class="card revenue-chart p-2 b-radius-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Customer Engagement Analytics'); ?></h4>
            <p class="f-w-600 f-14 text-muted"><?= htmlspecialchars(__('Calls, emails & response tracking'), ENT_QUOTES, 'UTF-8'); ?></p>
          </div>
          <span class="bg-green-50 b-radius-20 pt-1 pb-1 border-green-100 p-l-10 p-r-10 text-center"><?= __('Engagement Improving'); ?></span>
        </div>
        <div id="line-example" style="min-height: 360px;"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2 campaign">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Campaign Performance'); ?></h4>
          </div>
          <span class="f-12 text-primary b-radius-20 status-blue pt-1 pb-1 p-l-10 p-r-10"><?= __('View Campaigns'); ?></span>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16"><?= __('LinkedIn Campaign ROI'); ?></span>
            <span class="f-16 text-success f-w-600">182%</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16"><?= __('Leads Generated'); ?></span>
            <span class="f-16 text-success f-w-600">1,248</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16"><?= __('Cost Per Lead'); ?></span>
            <span class="f-16 text-warning f-w-600">₹1,420</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-red-100">
            <span class="f-16"><?= __('Stale Opportunities'); ?></span>
            <span class="f-16 text-danger f-w-600">22</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body table-card">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Live CRM Activity'); ?></h4>
          </div>
          <span class="f-14">
            <div class="p-status bg-c-green d-inline-block m-r-5 blink-fade"></div><?= __('Live'); ?>
          </span>
        </div>
        <div class="mt-4">
          <p class="f-16 bg-green-50 b-radius-20 p-3 border-green-100"><?= __('New opportunity created for ABC Infra.'); ?></p>
          <p class="f-16 bg-blue-50 b-radius-20 p-3 border-blue-100"><?= __('Proposal shared with Nova Retail.'); ?></p>
          <p class="f-16 bg-yellow-50 b-radius-20 p-3 border-yellow-100"><?= __('Follow-up reminder triggered for customer XYZ.'); ?></p>
          <p class="f-16 bg-red-50 b-radius-20 p-3 border-red-100"><?= __('Campaign launched for networking products.'); ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card p-2 b-radius-30 bg-black">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30 text-white"><?= __('CRM AI Insights'); ?></h4>
          </div>
          <span class="bg-danger b-radius-20 pt-1 pb-1 p-l-10 p-r-10"><?= __('Live'); ?></span>
        </div>
        <div class="mt-4">
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('New opportunity created for ABC Infra.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Proposal shared with Nova Retail.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Follow-up reminder triggered for customer XYZ.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Campaign launched for networking products.'); ?></p>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Customer Engagement Analytics Chart CSS -->
<link rel="stylesheet" type="text/css" href="assets/bower_components/morris.js/css/morris.css">

<!-- Customer Engagement Analytics Chart JS -->
<script src="assets/bower_components/raphael/js/raphael.min.js"></script>
<script src="assets/bower_components/morris.js/js/morris.js"></script>
<script src="assets/assets/pages/chart/morris/morris-custom-chart.js"></script>

</body>

</html>
