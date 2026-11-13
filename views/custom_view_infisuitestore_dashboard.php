

<div class="row mt-3">
  <div class="col-lg-3 col-md-6 col-sm-12 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Inventory Value'); ?></p>
        <h3 class="f-w-600 f-50 mt-3 mb-3"><?= __('₹8.6 Cr'); ?></h3>
        <p class="text-success mb-0 f-w-600"><?= __('Healthy inventory levels'); ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-12 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Low Stock Items'); ?></p>
        <h3 class="f-w-600 f-50 mt-3 mb-3">42</h3>
        <p class="text-danger mb-0 f-w-600"><?= __('Reorder required'); ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-12 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-crm-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Pending GRNs'); ?></p>
        <h3 class="f-w-600 f-50 mt-3 mb-3">18</h3>
        <p class="text-warning mb-0 f-w-600"><?= __('Receiving bottleneck'); ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-12 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-dashboard-black infisuite-crm-dashboard b-radius-30">
      <div class="card-body table-card">
        <span class="text-white mb-0">
          <div class="p-status bg-c-green d-inline-block m-r-5 blink-fade"></div>
          <?= __('LIVE INVENTORY ALERT'); ?>
        </span>
        <h6 class="f-w-600 f-30 mt-4 mb-2 text-white leading-relaxed"><?= __('Hyderabad warehouse may stock out in 3 days.'); ?></h6>
      </div>
    </div>
  </div>
  <div class="col-lg-8 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2 pipeline-flow">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Stock Movement Analytics'); ?></h4>
            <p class="f-w-600 f-14 text-muted"><?= __('Inward vs outward inventory movement'); ?></p>
          </div>
          <span class="bg-green-50 b-radius-20 pt-1 pb-1 border-green-100 p-l-10 p-r-10 text-center">94% Inventory Accuracy</span>
        </div>
        <div id="line-example" style="height: 360px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body mb-5">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Warehouse Health'); ?></h4>
          </div>
          <span class="f-12 text-primary b-radius-20 status-blue pt-1 pb-1 p-l-10 p-r-10 text-center"><?= __('Updated Live'); ?></span>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-success"><?= __('Dispatch SLA'); ?></span>
            <span class="f-16 text-success f-w-600">96%</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-danger"><?= __('Picking Accuracy'); ?></span>
            <span class="f-16 text-danger f-w-600">89%</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-red-100">
            <span class="f-16 text-danger"><?= __('Inventory Variance'); ?></span>
            <span class="f-16 text-danger f-w-600">4.2%</span>
          </div>
        </div>
        <div class="mt-4 mb-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-success"><?= __('Warehouse Utilization'); ?></span>
            <span class="f-16 text-success f-w-600">82%</span>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body mb-5">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= __('Critical Low Stock'); ?></h4>
          </div>
          <a href="" class="f-14 text-primary f-w-600"><?= __('Generate PR'); ?></a>
        </div>
        <div class="table-responsive mt-5 mb-5">
          <table class="table">
            <tr>
              <th scope="col"><?= __('Item'); ?></th>
              <th scope="col"><?= __('Available'); ?></th>
              <th scope="col"><?= __('Days Left'); ?></th>
            </tr>
            <tr>
              <td><?= __('Router X120'); ?></td>
              <td>12</td>
              <td class="text-danger"><?= __('2 Days'); ?></td>
            </tr>
            <tr>
              <td><?= __('Fiber Cable'); ?></td>
              <td>42</td>
              <td class="text-danger"><?= __('5 Days'); ?></td>
            </tr>
            <tr>
              <td><?= __('ONT Device'); ?></td>
              <td>18</td>
              <td class="text-danger"><?= __('3 Days'); ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body mb-5">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30"><?= htmlspecialchars(__('Dead & Slow Moving Stock'), ENT_QUOTES, 'UTF-8'); ?></h4>
          </div>
          <span class="f-12 text-danger b-radius-20 bg-red-50 pt-1 pb-1 p-l-10 p-r-10 text-center"><?= __('Attention Required'); ?></span>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Dead Stock Value'); ?></span>
            <span class="f-16 text-danger f-w-600">₹18 L</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-dark"><?= __('Slow Moving Inventory'); ?></span>
            <span class="f-16 text-danger f-w-600">₹42 L</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-blue-50 b-radius-20 p-3 border-red-100">
            <span class="f-16 text-dark"><?= __('Blocked Inventory'); ?></span>
            <span class="f-16 text-primary f-w-600">₹8 L</span>
          </div>
        </div>
        <div class="mt-4 mb-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Inventory Turnover'); ?></span>
            <span class="f-16 text-success f-w-600">7.4x</span>
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
            <h4 class="f-w-600 f-30"><?= __('Live Dispatch Center'); ?></h4>
          </div>
          <span class="f-14">
            <div class="p-status bg-c-green d-inline-block m-r-5 blink-fade"></div><?= __('Live'); ?>
          </span>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-blue-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Pending Dispatches'); ?></span>
            <span class="f-16 text-primary f-w-600">34</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16 text-dark"><?= __('Delayed Dispatches'); ?></span>
            <span class="f-16 text-danger f-w-600">9</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-red-100">
            <span class="f-16 text-dark"><?= __('Shipment SLA Breaches'); ?></span>
            <span class="f-16 text-danger f-w-600">4</span>
          </div>
        </div>
        <div class="mt-4 mb-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16 text-dark"><?= __('Dispatch Success Rate'); ?></span>
            <span class="f-16 text-success f-w-600">96.2%</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card p-2 b-radius-30 bg-black">
      <div class="card-body mb-4">
        <div class="d-flex justify-content-between align-items-center m-d-flex">
          <div class="d-inline-block m-d-block">
            <h4 class="f-w-600 f-30 text-white"><?= __('Inventory AI Insights'); ?></h4>
          </div>
          <span class="bg-danger b-radius-20 pt-1 pb-1 p-l-10 p-r-10"><?= __('Live'); ?></span>
        </div>
        <div class="mt-4">
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Dead stock increasing in networking accessories category.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Dispatch delays increasing after evening shift.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Fast-moving routers may exceed forecast next week.'); ?></p>
          <p class="f-16 bg-black-100 b-radius-20 p-3 text-white"><?= __('Warehouse B inventory variance above acceptable threshold.'); ?></p>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Stock Movement Analytics chart CSS -->
<link rel="stylesheet" type="text/css" href="assets/bower_components/morris.js/css/morris.css">
<!-- Stock Movement Analytics Chart JS -->
<script src="assets/bower_components/raphael/js/raphael.min.js"></script>
<script src="assets/bower_components/morris.js/js/morris.js"></script>
<script src="assets/pages/chart/morris/morris-custom-chart.js"></script>

</body>

</html>
