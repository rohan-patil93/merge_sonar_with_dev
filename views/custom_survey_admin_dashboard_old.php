
<!-- morris chart -->
<link rel="stylesheet" type="text/css" href="assets/bower_components/morris.js/css/morris.css">
<div class="pcoded-inner-content">
  	<div class="main-body">
    		<div class="page-wrapper p-0">
     				<!-- Page-body start -->
     				<div class="page-body">
								<div class="card">   
										<div class="card-body p-4">
												<div class="row align-items-center">
														<div class="col-lg-9">
																<h2 class="f-22 f-w-600 m-0">Survey Overview</h2>
																<p class="text-muted mt-1">Admin Dashboard</p>
														</div>
														<div class="col-lg-3 text-end">
																<button type="button" class="btn btn-success waves-effect" data-bs-toggle="modal" data-bs-target="#create_survey"><i class="ti-plus m-r-5 text-white"></i> Create Survey</button>
														</div>
												</div>
												
												<div class="row survey_dashboard mt-4">
														<div class="col-lg-3 col-md-3 col-sm-6 col-12 mb-3">
																<div class="card prod-p-card card-blue mb-0 border b-radius-10">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="p-3 bg-blue-100 b-radius-10 text-center img-50 text-success">
                                                    <span class="fa fa-calculator f-w-600 f-18 text-primary"></span>
                                                </div>
                                            </div><div class="col close">
                                                <h6 class="m-b-5 text-dark f-w-600">Total Surveys</h6>
                                                <h3 class="m-b-0 f-w-700 text-primary f-34 mt-2">24</h3>
                                            </div>
                                        </div>
                                        <h6 class="pt-3 border-top mt-3 f-w-600">All Time</h6>
                                    </div>
                                </div>
														</div>
														<div class="col-lg-3 col-md-3 col-sm-6 col-12 mb-3">
																<div class="card prod-p-card card-info mb-0 border b-radius-10">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="p-3 bg-green-100 b-radius-10 text-center img-50 text-success">
                                                    <span class="ti-control-play f-w-600 f-18 text-success"></span>
                                                </div>
                                            </div><div class="col close">
                                                <h6 class="m-b-5 text-dark f-w-600">Active Surveys</h6>
                                                <h3 class="m-b-0 f-w-700 text-success f-34 mt-2">8</h3>
                                            </div>
                                        </div>
                                        <h6 class="pt-3 border-top mt-3 f-w-600">Currently Running</h6>
                                    </div>
                                </div>
														</div>
														<div class="col-lg-3 col-md-3 col-sm-6 col-12 mb-3">
																<div class="card prod-p-card card-purple mb-0 border b-radius-10">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="p-3 bg-purple-100 b-radius-10 text-center img-50 text-success">
                                                    <span class="fa fa-check f-w-600 f-18 text-purple"></span>
                                                </div>
                                            </div><div class="col close">
                                                <h6 class="m-b-5 text-dark f-w-600">Completed</h6>
                                                <h3 class="m-b-0 f-w-700 text-purple f-34 mt-2">12</h3>
                                            </div>
                                        </div>
                                        <h6 class="pt-3 border-top mt-3 f-w-600">All Completed Surveys</h6>
                                    </div>
                                </div>
														</div>
														<div class="col-lg-3 col-md-3 col-sm-6 col-12 mb-3">
																<div class="card prod-p-card card-yellow mb-0 border b-radius-10">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="p-3 bg-yellow-100 b-radius-10 text-center img-50 text-success">
                                                    <span class="ti-file f-w-600 f-18 text-warning"></span>
                                                </div>
                                            </div><div class="col close">
                                                <h6 class="m-b-5 text-dark f-w-600">Draft</h6>
                                                <h3 class="m-b-0 f-w-700 text-warning f-34 mt-2">4</h3>
                                            </div>
                                        </div>
                                        <h6 class="pt-3 border-top mt-3 f-w-600">Not Yet Launched</h6>
                                    </div>
                                </div>
														</div>
												</div>

												<div class="row mt-4">
														<div class="col-lg-3 col-md-3 col-sm-12 col-12">
																<div class="card">
																		<div class="card-header">
																				<h5>Survey Completion Rate <i class="ti-info-alt"></i></h5>
																		</div>
																		<div class="card-block mt-4 text-center">
																				<div data-label="40%" class="radial-bar radial-bar-40 radial-bar-lg .radial-bar-info"></div>
																				<h6>12 of 24 surveys completed</h6>
																				<p><i class="ti-arrow-up"></i> <span class="text-success f-w-600">8% </span>vs last 30 days</p>
																		</div>
																</div>
														</div>
														<div class="col-lg-4 col-md-4 col-sm-12 col-12">
																<div class="card">
																		<div class="card-header">
																				<h5>Responses by Department <i class="ti-info-alt"></i></h5>
																		</div>
																		<div class="card-block mt-4 text-center">
																				<div id="morris-bar-chart" style="height:190px;"></div>
																		</div>
																</div>
														</div>
														<div class="col-lg-5 col-md-5 col-sm-12 col-12">
																<div class="card">
																		<div class="card-header">
																				<h5>Monthly Response Trend <i class="ti-info-alt"></i></h5>
																		</div>
																		<div class="card-block mt-4 text-center">
																				<div id="line-example" style="height: 190px;"></div>
																		</div>
																</div>
														</div>
												</div>

												<div class="card mt-2">
														<div class="card-header">
																<h5>Recent Surveys</h5>
																<div class="card-header-right">
																		<a href="" class="text-primary f-w-600">View All</a>
																</div>
														</div>
														<div class="card-block">
																<div class="table-responsive survey_dashboard">
																		<table class="table table-bordered table-sm">
																				<tr bgcolor="#eeeeee">
																						<th>Survey Name</th>
																						<th>Category</th>
																						<th>Assigned To</th>
																						<th>Start Date</th>
																						<th>End Date</th>
																						<th>Status</th>
																				</tr>
																				<tr>
																						<td>
																							<div class="row align-items-center feed-card">
                                                  <div class="col-auto p-r-0">
                                                      <div class="p-2 bg-green-100 b-radius-10 text-center left-icon-width text-success">
                                                          <i class="fa fa-laptop f-w-600"></i>
                                                      </div>
                                                  </div>
                                                  <div class="col">
                                                      <a href="#!">
                                                      		<h6 class="m-b-5">Employee Engagement Survey 2024</h6>
                                                      </a>
                                                  </div>
                                              </div>
																						</td>
																						<td>Engagement</td>
																						<td>All Employees</td>
																						<td>May 15, 2024</td>
																						<td>May 31, 2024</td>
																						<td>
																								<button class="btn btn-sm card-info text-success f-w-600 f-12 btn-outline-success b-radius-5 mb-1">Active</button>
																								<button class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
																										<i class="fa fa-eye m-r-5"></i> View
																								</button>
																								<button class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
																										<i class="fa fa-edit m-r-5"></i> Edit
																								</button>
																								<button class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
																										<i class="ti-bar-chart-alt m-r-5"></i> Summary
																								</button>
																						</td>
																				</tr>
																				<tr>
																						<td>
																							<div class="row align-items-center feed-card">
                                                  <div class="col-auto p-r-0">
                                                      <div class="p-2 bg-green-100 b-radius-10 text-center left-icon-width text-success">
                                                          <i class="fa fa-laptop f-w-600"></i>
                                                      </div>
                                                  </div>
                                                  <div class="col">
                                                      <a href="#!">
                                                      		<h6 class="m-b-5">Workspace well-being survey</h6>
                                                      </a>
                                                  </div>
                                              </div>
																						</td>
																						<td>Well-being</td>
																						<td>All Employees</td>
																						<td>Apr 10, 2024</td>
																						<td>May 25, 2024</td>
																						<td>
																								<button class="btn btn-sm card-info text-success f-w-600 f-12 btn-outline-success b-radius-5 mb-1">Completed</button>
																								<button class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
																										<i class="fa fa-eye m-r-5"></i> View
																								</button>
																								<button class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
																										<i class="fa fa-edit m-r-5"></i> Edit
																								</button>
																								<button class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
																										<i class="ti-bar-chart-alt m-r-5"></i> Summary
																								</button>
																						</td>
																				</tr>
																		</table>
																</div>
														</div>
												</div>
										</div>
								</div>
 						</div>	
				</div>
 		</div>	
</div>



</div>



<div class="modal fade" id="create_survey" tabindex="-1" style="z-index: 1050; display: none;" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
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




<!-- Morris Chart js -->
<script src="assets/bower_components/raphael/js/raphael.min.js"></script>
<script src="assets/bower_components/morris.js/js/morris.js"></script>
<!-- Custom js -->
<script src="assets/pages/chart/morris/morris-custom-chart.js"></script>