<?php
$_SESSION['apply_js']['MANUFACTURING1_DASHBOARD']=1;
$url = API . "?mode=getmfgdashboarddetails";
$apires = callAPI($url);
$result = json_decode($apires, true);
$workOrders = $result['work_orders'] ?? [];
$total = $result['work_orders_total'] ?? 0;
$summary = $result['summary'] ?? 0;
$shortages = $result['shortages'] ?? 0;
$product_notes = $result['product_notes'] ?? 0;
$chartData = [];
foreach ($product_notes as $year => $values) {
    $chartData[] = [
        'y' => $year,
        'request' => (float)$values['total_request_qty'],
        'produced' => (float)$values['total_produced_qty']
    ];
}
$chartjson = json_encode($chartData,1);
?>

<link rel="stylesheet" href="assets/bower_components/nvd3/css/nv.d3.css" type="text/css" media="all">

<link rel="stylesheet" href="assets/bower_components/chartist/css/chartist.css" type="text/css" media="all">

<link rel="stylesheet" type="text/css" href="../assets/css/custom_view_manufacturing_dashboard.css">

<div class="page-body">
  <div class="card">
    <div class="card-header bg-primary">
      <div class="row">
        <div class="col-lg-6">
          <h3 class="mb-0 f-w-600">Infisuite Manufacturing Dashboard</h3>
        </div>
        <div class="col-lg-2">
          <div class="form-group mb-0">
            <select aria-label="Select option" class="form-control border">
              <option>Plant A</option>
              <option>Plant B</option>
            </select>
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group mb-0">
            <input aria-label="Select date" type="date" class="form-control">
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group mb-0">
            <input aria-label="Select date" type="date" class="form-control">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6">
      <div class="row">
        <div class="col-xl-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="f-w-700 text-c-blue">2,960</h3>
                  <p class="m-b-0">Total Units</p>
                </div>

              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="f-w-700 text-c-green">92%</h3>
                  <p class="m-b-0">Efficiency</p>
                </div>

              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="f-w-700 text-c-yellow">87%</h3>
                  <p class="m-b-0">OEE</p>
                </div>

              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="f-w-700 text-c-red">5.6h</h3>
                  <p class="m-b-0">Downtime</p>
                </div>

              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="f-w-700 text-c-black">2.1%</h3>
                  <p class="m-b-0">Rejection</p>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5 class="text-danger"><i class="fa fa-bell m-r-10"></i>Alerts</h5>
        </div>
        <div class="card-block list-tag">
          <ul>
            <li><i class="icofont icofont-double-right text-success m-r-10"></i>WO-205 delayed 1 day</li>
            <li><i class="icofont icofont-double-right text-success m-r-10"></i>M-07 under maintenance</li>
            <li><i class="icofont icofont-double-right text-success m-r-10"></i>Quality deviation in Batch B-313</li>
            <li><i class="icofont icofont-double-right text-success m-r-10"></i>Steel rods below reorder level</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5>Production Trend</h5>
          <div class="card-header-right ml-5">
            <div class="legend-item">
              <span class="dot dot-one"></span>
              <span class="f-w-600" >Request</span>
            </div>
            <div class="legend-item">
              <span class="dot dot-two"></span>
              <span class="f-w-600" >Produced</span>
            </div>
          </div>
        </div>
        <div class="card-block">
          <div id="line-example" class="height-380"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5>Quality Defects</h5>
        </div>
        <div class="card-block p-0 mt-3">
          <div id="piechart" class="nvd-chart"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h5>Work Orders</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm" id="workOrdersTable">
              <tr style="background-color: #eeeeee;">
                <th scope="col">Order No</th>
                <th scope="col">Product</th>
                <th scope="col">Quantity</th>
                <th scope="col">Produced</th>
                <th scope="col">Status</th>
                <th scope="col">ETA</th>
              </tr>

              <?php foreach ($result['work_orders'] as $wo): ?>
                <tr class="work-order-row cursor-pointer" data-bs-toggle="modal" data-bs-target="#order-number"
                  data-workorder='<?php echo json_encode($wo); ?>'>

                  <td class="text-primary" ><?php echo $wo['transaction_no']; ?></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div>
                        <strong><?php echo $wo['product_name']; ?></strong><br>
                        <small class="text-muted"><?php echo $wo['product_sku']; ?></small>
                      </div>
                    </div>
                  </td>
                  <td><?php echo $wo['planned_qty']; ?></td>
                  <td><?php echo $wo['completed_qty']; ?></td>
                  <td>
                    <span class="badge" style="background-color: <?php echo $wo['status_bg'] ?: '#ccc'; ?>;
                     color: <?php echo $wo['status_text_color'] ?: '#000'; ?>;
                     font-weight: 600;">
                      <?php echo $wo['status_name'] ?: 'Unknown'; ?>
                    </span>
                  </td>
                  <td><?php echo date('d-m-Y', strtotime($wo['planned_end_date'])); ?></td>
                </tr>
              <?php endforeach; ?>

            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5>Machine & Line Status</h5>
        </div>
        <div class="card-block mt-3">
          <div class="row">
            <div class="col-xl-3 col-md-6">
              <div class="card prod-p-card card-green">
                <div class="card-body">
                  <div class="row align-items-center m-b-30">
                    <div class="col">
                      <h6 class="m-b-5">M-01 (A1)</h6>
                      <h3 class="m-b-0 f-w-700">Running</h3>
                    </div>

                  </div>
                  <p class="m-b-0 f-w-600">Utilization: 96%</p>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6">
              <div class="card prod-p-card card-green">
                <div class="card-body">
                  <div class="row align-items-center m-b-30">
                    <div class="col">
                      <h6 class="m-b-5">M-02 (A2)</h6>
                      <h3 class="m-b-0 f-w-700">Running</h3>
                    </div>

                  </div>
                  <p class="m-b-0 f-w-600">Utilization: 89%</p>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6">
              <div class="card prod-p-card card-red">
                <div class="card-body">
                  <div class="row align-items-center m-b-30">
                    <div class="col">
                      <h6 class="m-b-5">M-03 (B1)</h6>
                      <h3 class="m-b-0 f-w-700">Maintenance</h3>
                    </div>

                  </div>
                  <p class="m-b-0 f-w-600">Utilization: 0%</p>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6">
              <div class="card prod-p-card card-yellow">
                <div class="card-body">
                  <div class="row align-items-center m-b-30">
                    <div class="col">
                      <h6 class="m-b-5">M-04 (C1)</h6>
                      <h3 class="m-b-0 f-w-700">Idle</h3>
                    </div>

                  </div>
                  <p class="m-b-0 f-w-600">Utilization: 58%</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5>Material & Inventory</h5>
        </div>
        <div class="card-block">
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr>
                <td><?= $summary['raw']['type']; ?></td>
                <th scope="col" class="text-end"><?= number_format($summary['raw']['total']); ?> Units</th>
              </tr>
              <tr>
                <td><?= $summary['wip']['type']; ?></td>
                <th scope="col" class="text-end"><?= number_format($summary['wip']['total']); ?> Units</th>
              </tr>
              <tr>
                <td><?= $summary['fg']['type']; ?></td>
                <th scope="col" class="text-end"><?= number_format($summary['fg']['total']); ?> Units</th>
              </tr>
              <tr>
                <td class="text-danger">Shortages</td>
                <th scope="col" class="text-danger text-end"><?= $shortages; ?> Items</th>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5>Downtime Causes</h5>
        </div>
        <div class="card-block">
          <div class="ct-chart-horizontal ct-perfect-fourth"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5>Workforce Summary</h5>
        </div>
        <div class="card-block">
          <div class="table-responsive mt-4">
            <table class="table table-bordered">
              <tr>
                <td>Total Workforce</td>
                <th scope="col" class="text-end">152</th>
              </tr>
              <tr>
                <td>Attendance</td>
                <th scope="col" class="text-end">145 / 152</th>
              </tr>
              <tr>
                <td>Shift Efficiency</td>
                <th scope="col" class="text-end">90%</th>
              </tr>
              <tr>
                <td class="text-danger">Absent</td>
                <th scope="col" class="text-danger text-end">3</th>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="order-number" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Work Order Details</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h5 class="text-primary">WO-201 - Pump Shaft</h5>
        <div class="table-responsive mt-4">
          <table class="table  table-sm">
            <tr>
              <th scope="col">Line:</th>
              <td>A1</td>
              <th scope="col">Planned Qty:</th>
              <td>500</td>
            </tr>
          </table>
        </div>
        <div class="card-block list-tag">
          <div class="row">
            <div class="col-lg-6">
              <h5 class="f-w-600 mb-3 mt-3">Material Consumption</h5>
              <ul class="">
                <li><i class="icofont icofont-double-right text-success"></i>
                  Steel Rod - 44 kg used</li>
                <li><i class="icofont icofont-double-right text-success"></i>
                  Copper Coil - 11 kg used</li>
                <li><i class="icofont icofont-double-right text-success"></i>
                  Bearing Oil - 34 kg used</li>
                <li><i class="icofont icofont-double-right text-success"></i>
                  Washer Set - 47 kg used</li>
              </ul>
            </div>
            <div class="col-lg-6">
              <h5 class="f-w-600 mb-3 mt-3">Quality Summary</h5>
              <ul>
                <li><i class="icofont icofont-double-right text-success"></i>
                  Surface Scratch: 3 defects</li>
                <li><i class="icofont icofont-double-right text-success"></i>
                  Dimension Error: 1 defects</li>
                <li><i class="icofont icofont-double-right text-success"></i>
                  Others: 4 defects</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>

      </div>
    </div>
  </div>
</div>
<script>
    const lineChartData = <?php echo $chartjson; ?>;
    console.log("Line data"+lineChartData);
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll(".work-order-row");
    rows.forEach(row => {
      row.addEventListener("click", () => {
        const wo = JSON.parse(row.dataset.workorder);
        document.querySelector("#order-number .modal-title").textContent = "Work Order Details";
        document.querySelector("#order-number h5.text-primary").textContent = `${wo.transaction_no} -${wo.product_name}`;
        const infoTable = `
        <tr>
          <th scope="col">Line:</th><td>${wo.work_order_id}</td>
          <th scope="col">Planned Qty:</th><td>${wo.planned_qty}</td>
        </tr>
      `;
        document.querySelector("#order-number .table.table-sm").innerHTML = infoTable;
        let materials = [];
        try { materials = JSON.parse(wo.materials); } catch (e) { materials = []; }
        const matList = materials.length > 0
          ? materials.map(m => `
      <li>
        <i class="icofont icofont-double-right text-success"></i>
        ${m.item_name ? `<strong>${m.item_name}</strong>` : `Item #${m.item_id}`}
        — Quantity ${m.request_qty} ${m.uom})
      </li>
    `).join("")
          : '<li>No materials listed</li>';
        document.querySelector("#order-number .col-lg-6:first-child ul").innerHTML = matList;
        let operations = [];
        try { operations = JSON.parse(wo.operations); } catch (e) { operations = []; }

        const opList = operations.length > 0
          ? operations.map(o => `
      <li>
        <i class="icofont icofont-tools-alt-2 text-primary"></i>
        ${o.operation_name || `Operation #${o.operation_id}`}
        — ${o.operation_time} mins, manpower: ${o.operation_manpower}
      </li>
    `).join("")
          : '<li>No operations recorded</li>';

        document.querySelector("#order-number .col-lg-6:last-child ul").innerHTML = opList;
      });
    });
  });
</script>
