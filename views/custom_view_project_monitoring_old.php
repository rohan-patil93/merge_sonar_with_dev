 <?php
  $_SESSION['apply_js']['new_hr_payroll'] = 1;
  $pay_month = $_GET['pay_month'] ?? 3;
  $pay_year  = $_GET['pay_year'] ?? 2024;
  $url = API . "?mode=getPayrollDashboardSummary&pay_month=" . $pay_month . "&pay_year=" . $pay_year;
  $calurl = callAPI($url);
  $data = json_decode($calurl, 1);
  ?>
 <script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>

<div class="row mt-3">
  <div class="col-lg-7 col-md-12 col-sm-12 col-12">
    <h3 class="f-w-600 mb-0">Project Monitoring</h3>
  </div> 
  <div class="col-lg-5 col-md-12 col-sm-12 col-12 text-end">
    <button class="btn border f-w-600">View All Project</button>
  </div> 
</div>
<div class="row mt-4">
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h4 class="f-w-600 f-20">Project A</h4>
          <button class="btn btn-success btn-sm b-radius-30">On Track</button>
        </div>
        <div class="table-responsive mt-3">
          <table class="table table-borderless table-sm">
            <tr>
              <th>Type</th>
              <th>Ongoing/One Time</th>
            </tr>
            <tr>
              <th>Sector</th>
              <th>Education</th>
            </tr>
            <tr>
              <th>Location</th>
              <th>L1</th>
            </tr>
            <tr>
              <th>Amount</th>
              <th>₹ 1,00,00,000</th>
            </tr>
          </table>
        </div>
        <div class="mb-4">
          <div class="d-flex justify-content-between">
            <span class="f-w-600 f-14">Progress</span>
            <span class="f-w-600 f-14">75%</span>
          </div>
          <div class="progress progress-sm mt-1">
            <div class="progress-bar progress-bar-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <h3 class="f-w-600 f-14 mb-2">Planned Timeline</h3>
        <div class="mt-1">
          <div class="d-flex justify-content-between border-top border-bottom p-2">
            <div class="">
              <i class="fa fa-calendar me-2"></i>
              <span class="f-w-600 f-14">01-01-2026</span>
            </div>
            <span class="f-w-600 f-14">to</span>
            <div class="">
              <i class="fa fa-calendar me-2"></i>
              <span class="f-w-600 f-14">20-10-2026</span>
            </div>
          </div>
        </div>
        <div class="mt-4 mb-3 bg-gray p-3">
          <div class="d-flex justify-content-between">
            <div class="text-center">
              <p class="f-14 f-w-600 mb-0 text-muted">Spent (FY 23-24)</p>
              <span class="f-w-600 f-18">₹ 50,00,000</span>
            </div>
            <div class="text-center">
              <p class="f-14 f-w-600 mb-0 text-muted">Spent (FY 24-25)</p>
              <span class="f-w-600 f-18">₹ 10,00,000</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between">
                <h4 class="f-w-600 f-20">Project B</h4>
                <button class="btn btn-danger btn-sm b-radius-30">At Risk</button>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Type</th>
                        <th>One Time</th>
                    </tr>

                    <tr>
                        <th>Sector</th>
                        <th>Health</th>
                    </tr>

                    <tr>
                        <th>Location</th>
                        <th>L2</th>
                    </tr>

                    <tr>
                        <th>Amount</th>
                        <th>₹ 25,00,000</th>
                    </tr>
                </table>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between">
                    <span class="f-w-600 f-14">Progress</span>
                    <span class="f-w-600 f-14">25%</span>
                </div>

                <div class="progress progress-sm mt-1">
                  <div class="progress-bar progress-bar-danger" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <h3 class="f-w-600 f-14 mb-2">Planned Timeline</h3>

            <div class="mt-1">
                <div class="d-flex justify-content-between border-top border-bottom p-2">

                    <div>
                        <i class="fa fa-calendar me-2"></i>
                        <span class="f-w-600 f-14">01-01-2026</span>
                    </div>

                    <span class="f-w-600 f-14">to</span>

                    <div>
                        <i class="fa fa-calendar me-2"></i>
                        <span class="f-w-600 f-14">20-10-2026</span>
                    </div>

                </div>
            </div>

            <div class="mt-4 mb-3 bg-gray p-3">
                <div class="d-flex justify-content-between">

                    <div class="text-center">
                        <p class="f-14 f-w-600 mb-1 text-muted">Spent (FY 23-24)</p>
                        <span class="f-w-600 f-18">₹ 10,00,000</span>
                    </div>

                    <div class="text-center">
                        <p class="f-14 f-w-600 mb-1 text-muted">Spent (FY 24-25)</p>
                        <span class="f-w-600 f-18">₹ 0</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between">
                <h4 class="f-w-600 f-20">Project C</h4>
                <button class="btn btn-warning btn-sm b-radius-30">On Watch</button>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Type</th>
                        <th>One Time</th>
                    </tr>

                    <tr>
                        <th>Sector</th>
                        <th>Go Green</th>
                    </tr>

                    <tr>
                        <th>Location</th>
                        <th>L3</th>
                    </tr>

                    <tr>
                        <th>Amount</th>
                        <th>₹ 25,00,000</th>
                    </tr>
                </table>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between">
                    <span class="f-w-600 f-14">Progress</span>
                    <span class="f-w-600 f-14">33%</span>
                </div>

                <div class="progress progress-sm mt-1">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <h3 class="f-w-600 f-14 mb-2">Planned Timeline</h3>

            <div class="mt-1">
                <div class="d-flex justify-content-between border-top border-bottom p-2">

                    <div>
                        <i class="fa fa-calendar me-2"></i>
                        <span class="f-w-600 f-14">01-01-2026</span>
                    </div>

                    <span class="f-w-600 f-14">to</span>

                    <div>
                        <i class="fa fa-calendar me-2"></i>
                        <span class="f-w-600 f-14">20-10-2026</span>
                    </div>

                </div>
            </div>

            <div class="mt-4 mb-3 bg-gray p-3">
                <div class="d-flex justify-content-between">

                    <div class="text-center">
                        <p class="f-14 f-w-600 mb-1 text-muted">Spent (FY 23-24)</p>
                        <span class="f-w-600 f-18">₹ 50,000</span>
                    </div>

                    <div class="text-center">
                        <p class="f-14 f-w-600 mb-1 text-muted">Spent (FY 24-25)</p>
                        <span class="f-w-600 f-18">₹ 0</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
  </div>
</div>

<div class="user-details-sec mt-3">
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-user f-24 text-primary"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Total Project</span>
            <h4 class="f-30 f-w-600">3</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-green-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa fa-database f-24 text-success"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Total Approval Amount</span>
            <h4 class="f-24 f-w-600">₹ 1,50,00,000</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-database f-24 text-primary"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Total Spent (FY 23-24)</span>
            <h4 class="f-24 f-w-600">₹ 65,50,000</h4>
          </div>
        </div>
        
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-database f-24 text-purple"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Total Spent (FY 24-25)</span>
            <h4 class="f-24 f-w-600">₹ 10,00,000</h4>
          </div>
        </div>
        
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body sale-card">
        <div class="d-flex justify-content-between">
          <div data-label="60%" class="radial-bar radial-bar-40 radial-bar-md radial-bar-danger m-0">
              <img src="uploads/all/2.jpg" alt="User-Image">
          </div>
          <div class="d-inline-block mt-3">
            <span class="f-16 f-w-600 text-dark">Overall Progress</span>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <h3 class="f-w-600">Compliance & Risk Overview</h3>
        <div class="table-responsive mt-3 risk-overview">
          <table class="table table-bordered align-middle">
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center">
                  <i class="ti-user f-16 text-success f-w-600"></i>
                </span>
              </td>
              <th>CSR Obligation Due Date</th>
              <th>3 March 2025</th>
              <td>
                <label class="form-label label label-success">On Track</label>
              </td>
            </tr>
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center">
                  <i class="ti-user f-16 text-warning f-w-600"></i>
                </span>
              </td>
              <th>Board Approval Pending</th>
              <th>₹ 30,00,000</th>
              <td>
                <label class="form-label label label-warning">Pending</label>
              </td>
            </tr>
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center">
                  <i class="ti-user f-16 text-warning f-w-600"></i>
                </span>
              </td>
              <th>Project Delayed</th>
              <th>1</th>
              <td>
                <label class="form-label label label-warning">At Risk</label>
              </td>
            </tr>
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-red-50 f-14 f-w-600 text-center">
                  <i class="ti-user f-16 text-danger f-w-600"></i>
                </span>
              </td>
              <th>Unallocated CSr Funds</th>
              <th>₹ 50,00,000</th>
              <td>
                <label class="form-label label label-danger">High</label>
              </td>
            </tr>
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center">
                  <i class="ti-user f-16 text-warning f-w-600"></i>
                </span>
              </td>
              <th>Utilization Certificate Pending</th>
              <th>1</th>
              <td>
                <label class="form-label label label-warning">Pending</label>
              </td>
            </tr>
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-green-50 f-14 f-w-600 text-center">
                  <i class="ti-user f-16 text-success f-w-600"></i>
                </span>
              </td>
              <th>NGO Due Diligance Pending</th>
              <th>0</th>
              <td>
                <label class="form-label label label-success">Clear</label>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <h3 class="f-w-600">AI Insights</h3>
        <div class="table-responsive mt-3 ai-insights">
          <table class="table table-bordered align-middle">
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center">
                  <i class="fa fa-user f-16 text-primary f-w-600"></i>
                </span>
              </td>
              <th>CSR utilization is at 35.25%, which is below the ideal pace for this time of the year.</th>
            </tr>
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center">
                  <i class="fa fa-user f-16 text-primary f-w-600"></i>
                </span>
              </td>
              <th>Education sector consumes 66.7% of total approved budget.</th>
            </tr>
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center">
                  <i class="fa fa-user f-16 text-primary f-w-600"></i>
                </span>
              </td>
              <th>Project B is at risk of delay. Monitor to avoid timeline slippage.</th>
            </tr>
            <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center">
                  <i class="fa fa-user f-16 text-primary f-w-600"></i>
                </span>
              </td>
              <th>₹ 50,00,000 remains unallocated. Early planning recommended to ensure full utilization.</th>
            </tr>
          </table>
        </div>
        <a href="" class="bg-blue-50 p-2 b-radius-30 float-end f-w-600 text-primary">View Detailed Insights</a>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h3>Impact Snapshot (FY 2024-25)</h3>
    <div class="user-details-sec mt-3">
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
                <i class="fa fa-graduation-cap f-24 text-primary"></i>
              </span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Student Supported</span>
                <h4 class="f-30 f-w-600">12,500</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center me-3">
                <i class="fa fa-home f-24 text-purple"></i>
              </span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Villages Covered</span>
                <h4 class="f-24 f-w-600">48</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-red-50 p-2 b-radius-15 text-center me-3">
                <i class="far fa-heart f-24 text-danger"></i>
              </span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Health Camps Conducted</span>
                <h4 class="f-24 f-w-600">72</h4>
              </div>
            </div>
            
          </div>
        </div>
      </div>
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-green-50 p-2 b-radius-15 text-center me-3">
                <i class="fa fa-tree f-24 text-success"></i>
              </span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Trees Planted</span>
                <h4 class="f-24 f-w-600">25,000</h4>
              </div>
            </div>
            
          </div>
        </div>
      </div>
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center me-3">
                <i class="fas fa-female f-24 text-purple"></i>
              </span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Women Benefited</span>
                <h4 class="f-24 f-w-600">4,500</h4>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>