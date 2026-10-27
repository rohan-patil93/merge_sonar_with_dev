
<!-- morris chart -->
<link rel="stylesheet" type="text/css" href="assets/bower_components/morris.js/css/morris.css">

<style>
    .progress-circle {
        position: relative;
        width: 68px;
        height: 68px;
        flex: 0 0 68px;
    }

    .progress-circle svg {
        width: 68px;
        height: 68px;
        transform: rotate(-90deg);
    }

    .progress-bg {
	    fill: none;
	    stroke: #e5edf8;
	    stroke-width: 5;
	}

    .progress-value {
        fill: none;
	    stroke: currentColor;
	    stroke-width: 5;
	    stroke-linecap: round;
	    stroke-dasharray: 141.4 188.5;
    }

    /* Icon inside circle */
    .progress-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
		width: 28px;
        height: 28px;
		display: flex;
        align-items: center;
        justify-content: center;
		font-size: 20px;
    }

    /* Progress bar CSS */
    .progress-container {
	  	display: flex;
	 	align-items: center;
	  	gap: 12px;
	  	width: 100%;
	  	max-width: 600px;
	  	margin-bottom: 15px;
	}

	.progress-track {
	  	flex: 1;
	  	height: 6px;
	  	background-color: #e5e7eb;
	  	border-radius: 3px;
	  	overflow: hidden;
	}

	.progress-fill {
	  	height: 100%;
	  	border-radius: 3px;
	  	transition: width 0.3s ease;
	}

	.progress-text {
	  	font-size: 16px;
	  	font-weight: 600;
	}
	.para-limit{
		overflow: hidden;
	   display: -webkit-box;
	   -webkit-line-clamp: 2; /* number of lines to show */
	    line-clamp: 2; 
	   -webkit-box-orient: vertical;
	   min-height: 40px;
	}
    



	.completion-card {
	  width: 280px;
	  padding: 24px;
	  background: #ffffff;
	  border-radius: 8px;
	}

	.card-content {
	  display: flex;
	  align-items: center;
	  gap: 16px;
	  margin-bottom: 24px;
	}

	.progress-circle-rate {
	  position: relative;
	  width: 90px;
	  height: 90px;
	  border-radius: 50%;
	  display: flex;
	  justify-content: center;
	  align-items: center;
	  background: conic-gradient(#2563eb 0% var(--percent), #f1f5f9 var(--percent) 100%);
	  flex-shrink: 0;
	}

	.progress-inner {
	  width: 70px;
	  height: 70px;
	  background-color: #ffffff;
	  border-radius: 50%;
	  display: flex;
	  justify-content: center;
	  align-items: center;
	}

	.percentage {
	  font-size: 20px;
	  font-weight: 700;
	  color: #0f172a;
	}

	/* Text Content */
	.text-content {
	  display: flex;
	  flex-direction: column;
	}

	.title {
	  margin: 0 0 6px 0;
	  font-size: 15px;
	  font-weight: 700;
	  color: #1e293b;
	}

	.description {
	  margin: 0;
	  font-size: 13px;
	  color: #64748b;
	  line-height: 1.35;
	}

	/* Bottom Link */
	.details-link {
	  display: inline-flex;
	  align-items: center;
	  gap: 4px;
	  color: #2563eb;
	  font-size: 13px;
	  font-weight: 600;
	  text-decoration: none;
	}

	.details-link:hover {
	  text-decoration: underline;
	}

	.chevron {
	  font-size: 16px;
	  line-height: 1;
	}

</style>

<div class="pcoded-inner-content">
  	<div class="main-body">
    	<div class="page-wrapper p-0">
     		<!-- Page-body start -->
     		<div class="page-body">
     			<div class="row">
	     			<div class="col-lg-9">
	     				<div class="card">
	     					<div class="card-block p-4">
								<h2 class="f-22 f-w-600 m-0">My Survey</h2>
								<ul class="nav nav-tabs md-tabs infisuite_tab mt-4" role="tablist">
			                        <li class="nav-item">
			                            <a class="nav-link active f-16 f-w-600" data-bs-toggle="tab" href="#survey" role="tab">Survey</a>
			                            <div class="slide"></div>
			                        </li>
			                        <li class="nav-item">
			                            <a class="nav-link f-16 f-w-600" data-bs-toggle="tab" href="#poll" role="tab">Poll</a>
			                            <div class="slide"></div>
			                        </li>
			                    </ul>
			                </div>
			            </div>
			            <div class="tab-content">
		                    <div class="tab-pane active" id="survey" role="tabpanel">
		                        <div class="row">
		                        	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		                        		<div class="card prod-p-card">
		                                    <div class="card-body">
		                                        <div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="progress-circle">
															<svg viewBox="0 0 68 68">
											                    <circle
											                        class="progress-bg"
											                        cx="34"
											                        cy="34"
											                        r="30">
											                    </circle>

											                    <circle
											                        class="progress-value text-primary"
											                        cx="34"
											                        cy="34"
											                        r="30">
											                    </circle>
											                </svg>
															<div class="progress-icon text-primary">
											                    <span class="ti-clipboard"></span>
											                </div>
														</div>
		                                            </div>
		                                            <div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600">Assigned</h6>
		                                                <h3 class="m-b-0 f-w-700 f-34 mt-2">6</h3>
		                                            </div>
		                                        </div>
		                                        <h6 class="pt-3 border-top mt-3 f-w-600 f-14">Survey Assigned to you</h6>
		                                    </div>
		                                </div>
		                        	</div>
		                        	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		                        		<div class="card prod-p-card">
		                                    <div class="card-body">
		                                        <div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="progress-circle">
															<svg viewBox="0 0 68 68">
											                    <circle
											                        class="progress-bg"
											                        cx="34"
											                        cy="34"
											                        r="30">
											                    </circle>

											                    <circle
											                        class="progress-value text-warning"
											                        cx="34"
											                        cy="34"
											                        r="30">
											                    </circle>
											                </svg>
															<div class="progress-icon">
											                    <span class="ti-pencil-alt text-warning"></span>
											                </div>
														</div>
		                                            </div>
		                                            <div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600">In-Progress</h6>
		                                                <h3 class="m-b-0 f-w-700 f-34 mt-2">2</h3>
		                                            </div>
		                                        </div>
		                                        <h6 class="pt-3 border-top mt-3 f-w-600 f-14">Survey in progress</h6>
		                                    </div>
		                                </div>
		                        	</div>
		                        	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		                        		<div class="card prod-p-card">
		                                    <div class="card-body">
		                                        <div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="progress-circle">
															<svg viewBox="0 0 68 68">
											                    <circle
											                        class="progress-bg"
											                        cx="34"
											                        cy="34"
											                        r="30">
											                    </circle>

											                    <circle
											                        class="progress-value text-success"
											                        cx="34"
											                        cy="34"
											                        r="30">
											                    </circle>
											                </svg>
															<div class="progress-icon">
											                    <span class="fa fa-check text-success"></span>
											                </div>
														</div>
		                                            </div>
		                                            <div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600">Completed</h6>
		                                                <h3 class="m-b-0 f-w-700 f-34 mt-2">3</h3>
		                                            </div>
		                                        </div>
		                                        <h6 class="pt-3 border-top mt-3 f-w-600 f-14">Survey in progress</h6>
		                                    </div>
		                                </div>
		                        	</div>
		                        	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		                        		<div class="card prod-p-card">
		                                    <div class="card-body">
		                                        <div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="progress-circle">
															<svg viewBox="0 0 68 68">
											                    <circle
											                        class="progress-bg"
											                        cx="34"
											                        cy="34"
											                        r="30">
											                    </circle>

											                    <circle
											                        class="progress-value text-danger"
											                        cx="34"
											                        cy="34"
											                        r="30">
											                    </circle>
											                </svg>
															<div class="progress-icon">
											                    <span class="fa fa-check text-danger"></span>
											                </div>
														</div>
		                                            </div>
		                                            <div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600">Pending</h6>
		                                                <h3 class="m-b-0 f-w-700 f-34 mt-2">1</h3>
		                                            </div>
		                                        </div>
		                                        <h6 class="pt-3 border-top mt-3 f-w-600 f-14">Survey pending action</h6>
		                                    </div>
		                                </div>
		                        	</div>
		                        </div>
		                        <div class="mt-2">
			                        <h4 class="d-inline-block f-w-600">Assigned Surveys</h4>
			                        <a href="" class="f-w-600 float-end text-primary">View All</a>
			                    </div>
		                        <div class="row mt-3">
		                        	<div class="col-lg-4 col-md-4 col-sm-12 col-12">
		                        		<div class="card">
		                        			<div class="card-block">
		                        				<div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="p-3 bg-blue-100 b-radius-10 text-center img-50 text-success">
		                                                    <span class="fa fa-users f-w-600 f-18 text-primary"></span>
		                                                </div>
		                                            </div><div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600 para-limit">2024 Employee Engagement Survey</h6>
		                                                <p class="m-b-0 text-muted f-12 mt-2">
		                                                	<i class="fa fa-calendar"></i>
		                                                	Due: May 31, 2024
		                                                </p>
		                                            </div>
		                                        </div>
		                                        <h6 class="f-14 f-w-600 m-0 mt-3">Not Started</h6>
		                                        <div class="progress-container">
												  	<div class="progress-track">
												    	<div class="progress-fill bg-gray" style="width: 0%;"></div>
												  	</div>
												  	<span class="progress-text text-muted">0%</span>
												</div>
												<button class="btn btn-success mt-2 w-100">Start Survey</button>
		                        			</div>
		                        		</div>
		                        	</div>
		                        	<div class="col-lg-4 col-md-4 col-sm-12 col-12">
		                        		<div class="card">
		                        			<div class="card-block">
		                        				<div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="p-3 bg-blue-100 b-radius-10 text-center img-50 text-success">
		                                                    <span class="fa fa-users f-w-600 f-18 text-primary"></span>
		                                                </div>
		                                            </div><div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600 para-limit">Diversity, Equity & Inclusion Survey</h6>
		                                                <p class="m-b-0 text-muted f-12 mt-2">
		                                                	<i class="fa fa-calendar"></i>
		                                                	Due: May 25, 2024
		                                                </p>
		                                            </div>
		                                        </div>
		                                        <h6 class="f-14 f-w-600 m-0 mt-3">Not Started</h6>
		                                        <div class="progress-container">
												  	<div class="progress-track">
												    	<div class="progress-fill bg-primary" style="width: 45%;"></div>
												  	</div>
												  	<span class="progress-text text-primary">45%</span>
												</div>
												<button class="btn btn-success mt-2 w-100">Resume</button>
		                        			</div>
		                        		</div>
		                        	</div>
		                        	<div class="col-lg-4 col-md-4 col-sm-12 col-12">
		                        		<div class="card">
		                        			<div class="card-block">
		                        				<div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="p-3 bg-blue-100 b-radius-10 text-center img-50 text-success">
		                                                    <span class="fa fa-users f-w-600 f-18 text-primary"></span>
		                                                </div>
		                                            </div><div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600 para-limit">Health & Well-being Survey 2024</h6>
		                                                <p class="m-b-0 text-muted f-12 mt-2">
		                                                	<i class="fa fa-calendar"></i>
		                                                	Due: Jan 10, 2024
		                                                </p>
		                                            </div>
		                                        </div>
		                                        <h6 class="f-14 f-w-600 m-0 mt-3">Not Started</h6>
		                                        <div class="progress-container">
												  	<div class="progress-track">
												    	<div class="progress-fill bg-gray" style="width: 0%;"></div>
												  	</div>
												  	<span class="progress-text text-muted">0%</span>
												</div>
												<button class="btn btn-success mt-2 w-100">Start Survey</button>
		                        			</div>
		                        		</div>
		                        	</div>
		                        	<div class="col-lg-4 col-md-4 col-sm-12 col-12">
		                        		<div class="card">
		                        			<div class="card-block">
		                        				<div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="p-3 bg-blue-100 b-radius-10 text-center img-50 text-success">
		                                                    <span class="fa fa-users f-w-600 f-18 text-primary"></span>
		                                                </div>
		                                            </div><div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600 para-limit">Information Security Awarness Survey</h6>
		                                                <p class="m-b-0 text-muted f-12 mt-2">
		                                                	<i class="fa fa-calendar"></i>
		                                                	Due: May 25, 2024
		                                                </p>
		                                            </div>
		                                        </div>
		                                        <h6 class="f-14 f-w-600 m-0 mt-3">Not Started</h6>
		                                        <div class="progress-container">
												  	<div class="progress-track">
												    	<div class="progress-fill bg-primary" style="width: 30%;"></div>
												  	</div>
												  	<span class="progress-text text-primary">30%</span>
												</div>
												<button class="btn btn-success mt-2 w-100">Resume</button>
		                        			</div>
		                        		</div>
		                        	</div>
		                        	<div class="col-lg-4 col-md-4 col-sm-12 col-12">
		                        		<div class="card">
		                        			<div class="card-block">
		                        				<div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="p-3 bg-blue-100 b-radius-10 text-center img-50 text-success">
		                                                    <span class="fa fa-users f-w-600 f-18 text-primary"></span>
		                                                </div>
		                                            </div><div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600 para-limit">Workspace Facilities Feedback Survey</h6>
		                                                <p class="m-b-0 text-muted f-12 mt-2">
		                                                	<i class="fa fa-calendar"></i>
		                                                	Due: May 25, 2024
		                                                </p>
		                                            </div>
		                                        </div>
		                                        <h6 class="f-14 f-w-600 m-0 mt-3">Not Started</h6>
		                                        <div class="progress-container">
												  	<div class="progress-track">
												    	<div class="progress-fill bg-gray" style="width: 0%;"></div>
												  	</div>
												  	<span class="progress-text text-muted">0%</span>
												</div>
												<button class="btn btn-success mt-2 w-100">Resume</button>
		                        			</div>
		                        		</div>
		                        	</div>
		                        	<div class="col-lg-4 col-md-4 col-sm-12 col-12">
		                        		<div class="card">
		                        			<div class="card-block">
		                        				<div class="row align-items-center">
		                                            <div class="col-auto">
		                                                <div class="p-3 bg-blue-100 b-radius-10 text-center img-50 text-success">
		                                                    <span class="fa fa-users f-w-600 f-18 text-primary"></span>
		                                                </div>
		                                            </div><div class="col close">
		                                                <h6 class="m-b-5 text-dark f-w-600 para-limit">Leadership Effectiveness Survey</h6>
		                                                <p class="m-b-0 text-muted f-12 mt-2">
		                                                	<i class="fa fa-calendar"></i>
		                                                	Due: May 25, 2024
		                                                </p>
		                                            </div>
		                                        </div>
		                                        <h6 class="f-14 f-w-600 m-0 mt-3 text-danger">Pending</h6>
		                                        <div class="progress-container">
												  	<div class="progress-track">
												    	<div class="progress-fill bg-gray" style="width: 0%;"></div>
												  	</div>
												  	<span class="progress-text text-muted">0%</span>
												</div>
												<button class="btn btn-success mt-2 w-100">Resume</button>
		                        			</div>
		                        		</div>
		                        	</div>
		                        </div>
		                        <div class="card mt-2">
									<div class="card-header">
										<h5>Surveys History</h5>
									</div>
									<div class="card-block">
										<div class="table-responsive survey_dashboard">
											<table class="table table-bordered table-sm">
												<tr bgcolor="#eeeeee">
													<th>Survey Name</th>
													<th>Submitted Date</th>
													<th>Status</th>
													<th>View</th>
												</tr>
												<tr>
													<td>
														2023 Employee Engagement Survey
													</td>
													<td>Nov 20, 2023</td>
													<td>
														<button class="btn btn-sm card-info text-success f-w-600 f-12 btn-outline-success b-radius-5 mb-1">Completed</button>
													</td>
													<td>
														<button class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
																<i class="fa fa-eye m-r-5"></i> View Result
														</button>
													</td>
												</tr>
												<tr>
													<td>
														Hybird Work Feedback Survey
													</td>
													<td>Sep 15, 2023</td>
													<td>
														<button class="btn btn-sm card-info text-success f-w-600 f-12 btn-outline-success b-radius-5 mb-1">Completed</button>
													</td>
													<td>
														<button class="btn btn-sm btn-outline-primary f-w-600 f-12 text-primary p-2 b-radius-5 mb-1">
																<i class="fa fa-eye m-r-5"></i> View Result
														</button>
													</td>
												</tr>
											</table>
										</div>
										<a href="" class="f-w-600 text-primary text-center d-block mt-3">View All History</a>
									</div>
								</div>
		                    </div>
		                    <div class="tab-pane" id="poll" role="tabpanel">
		                        <p class="m-0">2.Cras consequat in enim ut efficitur. Nulla posuere elit quis auctor interdum praesent sit amet nulla vel enim amet. Donec convallis tellus neque, et imperdiet felis amet.</p>
		                    </div>
		                </div>
					</div>
					<div class="col-lg-3">
						<div class="card">
							<div class="card-header">
								<h5>Completion Rate</h5>
							</div>
							<div class="card-block">
								<div class="completion-card">
								  	<div class="card-content">
									    <div class="progress-circle-rate" style="--percent: 75%;">
									      <div class="progress-inner">
									        <span class="percentage">75%</span>
									      </div>
									    </div>
								    
									    <div class="text-content">
									      <h3 class="title">Great job!</h3>
									      <p class="description">You've completed<br>3 of 4 required<br>surveys.</p>
									    </div>
								  	</div>

									<a href="#" class="details-link">
									    View Completion Details <span class="chevron">&rsaquo;</span>
									</a>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-header">
								<h5>Upcoming Deadlines</h5>
								<div class="card-header-right">
									<a href="" class="f-w-600 float-end text-primary">View All</a>
								</div>
							</div>
							<div class="card-block">
								<div class="row align-items-center mb-3">
									<div class="col-lg-7">
										<h6 class="text-muted f-14 m-0 f-w-600">Diversity, Equity & Inclusion Survey</h6>
										<p class="m-0 mt-1 f-12 text-danger">May 25, 2024</p>
									</div>
									<div class="col-lg-5 text-end">
										<button class="btn p-1 bg-red-50 text-danger f-w-600 f-12 btn-outline-danger b-radius-5 mb-1">3 Days Left</button>
									</div>
								</div>
								<div class="row align-items-center mb-3">
									<div class="col-lg-7">
										<h6 class="text-muted f-14 m-0 f-w-600">Diversity, Equity & Inclusion Survey</h6>
										<p class="m-0 mt-1 f-12 text-danger">May 25, 2024</p>
									</div>
									<div class="col-lg-5 text-end">
										<button class="btn p-1 bg-red-50 text-danger f-w-600 f-12 btn-outline-danger b-radius-5 mb-1">3 Days Left</button>
									</div>
								</div>
								<div class="row align-items-center mb-3">
									<div class="col-lg-7">
										<h6 class="text-muted f-14 m-0 f-w-600">Diversity, Equity & Inclusion Survey</h6>
										<p class="m-0 mt-1 f-12 text-danger">May 25, 2024</p>
									</div>
									<div class="col-lg-5 text-end">
										<button class="btn p-1 bg-red-50 text-danger f-w-600 f-12 btn-outline-danger b-radius-5 mb-1">3 Days Left</button>
									</div>
								</div>
							</div>
							<div class="card-footer border-top">
								<a href="" class="f-w-600 text-primary text-center d-block">See All Deadlines</a>
							</div>
						</div>
						<div class="card latest-update-card">
							<div class="card-header">
								<h5>Recent Activity</h5>
							</div>
							<div class="card-block">
								<div class="scroll-widget mt-4">
                                    <div class="latest-update-box">
                                        <div class="row p-t-20 p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <img src="uploads/all/avatar-4.jpg" alt="user image" class="img-radius img-40 align-top m-r-15 update-icon">
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6>Your Manager Posted.</h6></a>
                                                <p class="text-muted m-b-0">Jonny michel</p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="fa fa-briefcase bg-c-red update-icon"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6>You have 3 pending Task.</h6></a>
                                                <p class="text-muted m-b-0">Hemilton</p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="fa fa-check f-w-600 bg-c-green update-icon"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6>New Order Received.</h6></a>
                                                <p class="text-muted m-b-0">Hemilton</p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="fa fa-briefcase bg-c-red update-icon"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6>You have 3 pending Task.</h6></a>
                                                <p class="text-muted m-b-0">Hemilton</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>
							<div class="card-footer border-top">
								<a href="" class="f-w-600 text-primary text-center d-block">View All Activity</a>
							</div>
						</div>
					</div>
				</div>
 			</div>	
		</div>
	</div>
</div>

</div>








<!-- Morris Chart js -->
<script src="assets/bower_components/raphael/js/raphael.min.js"></script>
<script src="assets/bower_components/morris.js/js/morris.js"></script>
<!-- Custom js -->
<script src="assets/pages/chart/morris/morris-custom-chart.js"></script>