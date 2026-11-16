
<div class="row mt-3">
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Open Orders'); ?></p>
        <h3 class="f-w-600 f-50 mt-3 mb-3">842</h3>
        <p class="text-success mb-0 f-w-600"><?= __('Operations healthy'); ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Delayed Deliveries'); ?></p>
        <h3 class="f-w-600 f-50 mt-3 mb-3">26</h3>
        <p class="text-danger mb-0 f-w-600"><?= __('Needs immediate action'); ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('SLA Breaches'); ?></p>
        <h3 class="f-w-600 f-50 mt-3 mb-3">14</h3>
        <p class="text-warning mb-0 f-w-600"><?= __('3 since yesterday'); ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-dashboard-black infisuite-crm-dashboard b-radius-30">
      <div class="card-body table-card">
        <span class="text-white mb-0">
          <div class="p-status bg-c-green d-inline-block m-r-5 blink-fade"></div>
          <?= __('LIVE OPERATIONS ALERT'); ?>
        </span>
        <h6 class="f-w-600 f-28 mt-4 mb-2 text-white leading-relaxed"><?= __('Hyderabad warehouse may run out of stock in 5 days.'); ?></h6>
      </div>
    </div>
  </div>
  <div class="col-lg-8 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2 pipeline-flow coo">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Order Lifecycle Tracker'); ?></h4>
            <p class="f-w-600 f-14 text-muted"><?= __('Real-time process visibility'); ?></p>
          </div>
          <span class="bg-green-50 b-radius-20 pt-1 pb-1 border-green-100 p-l-10 p-r-10 text-center"><?= __('92% Process Efficiency'); ?></span>
        </div>
        <div class="pipeline-wrapper mt-4">
          <div class="pipeline">
            <div class="step">
                <div class="circle status-blue text-primary">420</div>
                <h3><?= __('Leads'); ?></h3>
            </div>
            <div class="line"></div>
            <div class="step">
                <div class="circle bg-green-50 text-success">280</div>
                <h3><?= __('Orders'); ?></h3>
            </div>
            <div class="line"></div>
            <div class="step">
                <div class="circle bg-yellow-50 text-warning">190</div>
                <h3><?= __('Purchase'); ?></h3>
            </div>
            <div class="line"></div>
            <div class="step">
                <div class="circle purple text-purple">148</div>
                <h3><?= __('Delivery'); ?></h3>
            </div>
            <div class="line"></div>
            <div class="step">
                <div class="circle bg-red-50 text-danger">112</div>
                <h3><?= __('Billing'); ?></h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body table-card">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Operations Health'); ?></h4>
          </div>
          <span class="f-14">
            <div class="p-status bg-c-green d-inline-block m-r-5 blink-fade"></div><?= __('Live'); ?>
          </span>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-success"><?= __('Inventory'); ?></span>
            <span class="f-16 text-success f-w-600"><?= __('Healthy'); ?></span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-danger"><?= __('Procurement'); ?></span>
            <span class="f-16 text-danger f-w-600"><?= __('Delayed'); ?></span>
          </div>
        </div>
        <div class="mt-4 mb-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-danger"><?= __('Support Tickets'); ?></span>
            <span class="f-16 text-danger f-w-600"><?= __('High Load'); ?></span>
          </div>
        </div>
        <div class="mt-4 mb-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-success"><?= __('Warehouse Ops'); ?></span>
            <span class="f-16 text-success f-w-600"><?= __('Stable Load'); ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body table-card">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Team Productivity'); ?></h4>
          </div>
          <span class="bg-blue-50 b-radius-20 pt-1 pb-1 border-blue-100 p-l-10 p-r-10 text-center"><?= __('Update Live'); ?></span>
        </div>
        <canvas id="barChart" width="400" height="400"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body table-card">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= htmlspecialchars(__('Inventory & Procurement'), ENT_QUOTES, 'UTF-8'); ?></h4>
          </div>
          <a href="" class="f-12 text-primary f-w-600 text-center"><?= __('View Details'); ?></a>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Stock-Out Risk'); ?></span>
            <span class="f-16 text-danger f-w-600"><?= __('12 Items'); ?></span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Delayed Purchase Orders'); ?></span>
            <span class="f-16 text-danger f-w-600">18</span>
          </div>
        </div>
        <div class="mt-4 mb-4">
          <div class="d-flex justify-content-between bg-blue-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-dark"><?= __('GRN Pending'); ?></span>
            <span class="f-16 text-primary f-w-600">24</span>
          </div>
        </div>
        <div class="mt-4 mb-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-dark"><?= __('Warehouse Accuracy'); ?></span>
            <span class="f-16 text-success f-w-600">94%</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-12 col-sm-12 col-12">
    <div class="card p-2 b-radius-30 bg-black">
      <div class="card-body mb-4">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30 text-white"><?= __('AI Operations Insights'); ?></h4>
          </div>
          <span class="bg-danger b-radius-20 pt-1 pb-1 p-l-10 p-r-10"><?= __('Live'); ?></span>
        </div>
        <div class="mt-4">
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Approval delays increased order cycle by 18%.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Support SLA breaches increasing in South region.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('2 warehouse teams operating below efficiency threshold.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Dispatch performance improved by 11% this week.'); ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card revenue-chart p-2 b-radius-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Critical Operations Issues'); ?></h4>
          </div>
          <a href="" class="f-14 f-w-600 text-primary"><?= __('View All'); ?></a>
        </div>
        <div class="table-responsive mt-5 mb-5">
          <table class="table">
            <tr>
              <th scope="col"><?= __('Issue'); ?></th>
              <th scope="col"><?= __('Department'); ?></th>
              <th scope="col"><?= __('Priority'); ?></th>
            </tr>
            <tr>
              <th scope="col"><?= __('Delivery Delay'); ?></th>
              <td><?= __('Logistics'); ?></td>
              <td class="text-danger"><?= __('High'); ?></td>
            </tr>
            <tr>
              <th scope="col"><?= __('PO Approval Pending'); ?></th>
              <td><?= __('Procurement'); ?></td>
              <td class="text-warning"><?= __('Medium'); ?></td>
            </tr>
            <tr>
              <th scope="col"><?= __('SLA Breach Risk'); ?></th>
              <td><?= __('Support'); ?></td>
              <td class="text-danger"><?= __('High'); ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body mb-4">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('HR Operations Metrics'); ?></h4>
          </div>
          <a href="" class="f-12 text-success b-radius-20 bg-green-50 pt-1 pb-1 p-l-10 p-r-10 text-center"><?= __('Stable Workforce'); ?></a>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-blue-50 b-radius-20 p-3 border-green-100">
            <span class="f-16"><?= __('Attendance Rate'); ?></span>
            <span class="f-16 text-primary f-w-600">96.2%</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-green-100">
            <span class="f-16"><?= __('Overtime Hours'); ?></span>
            <span class="f-16 text-danger f-w-600"><?= __('428 hrs'); ?></span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16"><?= __('Attrition Risk Employees'); ?></span>
            <span class="f-16 text-danger f-w-600">11</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16"><?= __('Hiring Pipeline'); ?></span>
            <span class="f-16 text-success f-w-600"><?= __('38 Candidates'); ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Team Productivity Chart js -->
<script type="text/javascript" src="assets/bower_components/chart.js/js/Chart.js"></script>
<script type="text/javascript" src="assets/pages/chart/chartjs/chartjs-custom.js"></script>

</body>

</html>
