 <div class="row">
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="card-header">
                                                            <h5>Priority</h5>
                                                            <div class="card-header-right">
                                                                <i class="fa fa-check-square-o"></i>
                                                                <i class="fa fa-filter"></i>
                                                            </div>
                                                        </div> 
                                                        <div class="card-body">
                                                            <button class="btn bg-gray waves-effect waves-light btn-sm w-100 text-start f-w-600">High</button>
                                                            <button class="btn bg-gray waves-effect waves-light btn-sm w-100 text-start f-w-600">Low</button>
                                                            <button class="btn bg-gray waves-effect waves-light btn-sm w-100 text-start f-w-600">Regular</button>
                                                            <button class="btn bg-gray waves-effect waves-light btn-sm w-100 text-start f-w-600">Urgent</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <div class="card-header">
                                                            <h5>Order Status</h5>
                                                        </div> 
                                                        <div class="card-body">
                                                            <div id="chart_bar" style="width: 100%; height: 300px;"></div>     
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Top 10 Reqiured Skills</h5>
                                                </div> 
                                                <div class="card-block">
                                                    <div id="chart_Donut" style="width: 100%; height: 300px;"></div>
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Work Maintenance Manager</h5>
                                                </div> 
                                                <div class="card-block">
                                                    <img src="../files/assets/images/msg.png" class="w-100" alt="">
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Job Priorities By</h5>
                                                </div> 
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <h4 class="f-16">Urgent</h4>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <h4 class="f-16">75</h4>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <h4 class="f-16">High</h4>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <h4 class="f-16">75</h4>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <h4 class="f-16">Regular</h4>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <h4 class="f-16">74</h4>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <h4 class="f-16">Low</h4>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <h4 class="f-16">84</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-header">
                                                    <h5>Priority By Completion Date</h5>
                                                </div>
                                                <div class="card-body">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <ul class="nav nav-tabs md-tabs card-body" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-bs-toggle="tab" href="#home3" role="tab" aria-selected="true">Completed</a>
                                                        <div class="slide"></div>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="tab" href="#profile3" role="tab" aria-selected="false">In Process</a>
                                                        <div class="slide"></div>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="tab" href="#messages3" role="tab" aria-selected="false">Pending</a>
                                                        <div class="slide"></div>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="tab" href="#settings3" role="tab" aria-selected="false">Scheduled</a>
                                                        <div class="slide"></div>
                                                    </li>
                                                </ul>
                                                <div class="tab-content card-block">
                                                    <div class="tab-pane active" id="home3" role="tabpanel">
                                                        <div class="card-header pt-0">
                                                            <h5>Job Status By Technicians</h5>
                                                        </div>
                                                        <div class="card-block">
                                                            <canvas id="barChart" width="400" height="100"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="profile3" role="tabpanel">
                                                        <div class="card-body">
                                                            <p class="m-0">2.Cras consequat in enim ut efficitur. Nulla posuere elit quis auctor interdum praesent sit amet nulla vel enim amet. Donec convallis tellus neque, et imperdiet felis amet.</p>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="messages3" role="tabpanel">
                                                        <div class="card-body">
                                                            <p class="m-0">3. This is Photoshop's version of Lorem IpThis is Photoshop's version of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean mas Cum sociis natoque penatibus et magnis dis.....</p>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="settings3" role="tabpanel">
                                                        <div class="card-body">
                                                            <p class="m-0">4.Cras consequat in enim ut efficitur. Nulla posuere elit quis auctor interdum praesent sit amet nulla vel enim amet. Donec convallis tellus neque, et imperdiet felis amet.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>