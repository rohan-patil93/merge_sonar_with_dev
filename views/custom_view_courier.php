<div class="row">
    <div class="col-lg-8 courier">
        <ul class="nav nav-tabs tabs row">
            <li class="nav-item col-lg-3 col-4">
                <a class="nav-link active" data-bs-toggle="tab" href="#courier" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                 <i class="ti-package f-30 text-info p-l-15"></i>
                 <span class="text-info f-18 f-w-700">Courier</span>
             </a>
         </li>
         <li class="nav-item col-lg-3 col-4">
            <a class="nav-link" data-bs-toggle="tab" href="#stationery" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <i class="ti-ruler-pencil f-30 text-info p-l-15"></i>
                <span class="text-info f-18 f-w-700">Stationery</span>
            </a>
        </li>
        <li class="nav-item col-lg-3 col-4">
            <a class="nav-link" data-bs-toggle="tab" href="#transport" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <i class="ti-car f-30 text-info p-l-15"></i>
                <span class="text-info f-18 f-w-700">Transport</span>
            </a>
        </li>
        <li class="nav-item col-lg-3 col-4">
            <a class="nav-link" data-bs-toggle="tab" href="#food" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <i class="ti-hand-open f-30 text-info p-l-15"></i>
                <span class="text-info f-18 f-w-700">Food</span>
            </a>
        </li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content tabs card-block card mt-3 p-3">
        <div class="tab-pane active" id="courier" role="tabpanel">
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                    <div class="card social-card">
                        <div class="card-body text-center courier_popup">
                            <div class="text-facebook m-b-20"><i class="ti-back-left"></i></div>
                            <h3 class="text-facebook f-w-700">250</h3>
                            <p>Likes</p>
                            <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
                            <button type="button" class="btn btn-primary waves-effect c_btn" data-bs-toggle="modal" data-bs-target="#courier-in-Modal">
                                <span class="ti-plus"></span>
                            </button>
                        </div>
                        <div class="modal fade" id="courier-in-Modal" tabindex="-1" style="z-index: 1050; display: none;" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content align-left">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Courier in</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <span>Date</span>
                                            <input aria-label="Select date" type="date" name="" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <span>Name</span>
                                            <input aria-label="Input field" type="text" name="" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <span>Moblie No.</span>
                                            <input aria-label="Input field" type="tel" name="" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary waves-effect waves-light ">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                    <div class="card social-card">
                        <div class="card-body text-center courier_popup">
                            <div class="text-facebook m-b-20"><i class="ti-back-right"></i></div>
                            <h3 class="text-facebook f-w-700">350</h3>
                            <p>Likes</p>
                            <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
                            <button type="button" class="btn btn-primary waves-effect c_btn" data-bs-toggle="modal" data-bs-target="#courier-out-Modal">
                                <span class="ti-plus"></span>
                            </button>
                        </div>
                        <div class="modal fade" id="courier-out-Modal" tabindex="-1" style="z-index: 1050; display: none;" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content align-left">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Courier in</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <span>Date</span>
                                            <input aria-label="Select date" type="date" name="" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <span>Courier Number</span>
                                            <input aria-label="Input field" type="text" name="" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <span>Moblie No.</span>
                                            <input aria-label="Input field" type="tel" name="" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary waves-effect waves-light ">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                    <div class="card social-card">
                        <div class="card-body text-center">
                            <div class="text-facebook m-b-20"><i class="ti-timer"></i></div>
                            <h3 class="text-facebook f-w-700">450</h3>
                            <p>Likes</p>
                            <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Latest Products</h5>
                    <div class="card-header-right">
                        <ul class="nav nav-tabs md-tabs m-r-20">
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link p-2 active" data-bs-toggle="tab" href="#month" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Month</a>
                                <div class="slide"></div>
                            </li>
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link p-2" data-bs-toggle="tab" href="#week" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Week</a>
                                <div class="slide"></div>
                            </li>
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link p-2" data-bs-toggle="tab" href="#day" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Day</a>
                                <div class="slide"></div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content card-block table-border-style">
                    <div class="tab-pane active" id="month" role="tabpanel">
                        <div class="table-responsive infisuite-hover">
                            <table class="table table-framed">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </th>
                                        <th scope="col">
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </th>
                                        <th scope="col">
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </th>
                                        <th scope="col">
                                            <span class="form-label label label-success">In Progress</span>
                                        </th>
                                        <th scope="col">
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-success">In Progress</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="week" role="tabpanel">
                        <div class="table-responsive infisuite-hover">
                            <table class="table table-framed">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </th>
                                        <th scope="col">
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme 2</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </th>
                                        <th scope="col">
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </th>
                                        <th scope="col">
                                            <span class="form-label label label-success">In Progress</span>
                                        </th>
                                        <th scope="col">
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-success">In Progress</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="day" role="tabpanel">
                        <div class="table-responsive infisuite-hover">
                            <table class="table table-framed">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </th>
                                        <th scope="col">
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme 3</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </th>
                                        <th scope="col">
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </th>
                                        <th scope="col">
                                            <span class="form-label label label-success">In Progress</span>
                                        </th>
                                        <th scope="col">
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-success">In Progress</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="assets/images/4.png" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="stationery" role="tabpanel">
            <p class="m-0">2.Cras consequat in enim ut efficitur. Nulla posuere elit quis auctor interdum praesent sit amet nulla vel enim amet. Donec convallis tellus neque, et imperdiet felis amet.</p>
        </div>
        <div class="tab-pane" id="transport" role="tabpanel">
            <p class="m-0">3. This is Photoshop's version of Lorem IpThis is Photoshop's version of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean mas Cum sociis natoque penatibus et magnis dis.....</p>
        </div>
        <div class="tab-pane" id="food" role="tabpanel">
            <p class="m-0">3. This is Photoshop's version of Lorem IpThis is Photoshop's version of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean mas Cum sociis natoque penatibus et magnis dis.....</p>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card latest-update-card">
            <div class="card-header">
                <h5>Activity</h5>
                <div class="card-header-right">
                    <button class="btn dropdown-toggle waves-effect waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                        <a class="dropdown-item waves-light waves-effect" href="#">Action</a>
                        <a class="dropdown-item waves-light waves-effect" href="#">Another action</a>
                        <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
                    </div>
                </div>
            </div>
            <div class="card-block mt-4">
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 290px;">
                    <div class="scroll-widget" style="overflow: hidden; width: auto; height: 290px;">
                        <div class="latest-update-box">
                            <div class="row p-t-20 p-b-30">
                                <div class="col-auto text-end update-meta p-r-0">
                                    <i class="b-primary update-icon ring"></i>
                                </div>
                                <div class="col p-l-5 m-l-10">
                                    <a href="#!"><h6>Devlopment &amp; Update</h6></a>
                                    <p class="text-muted m-b-0">Lorem ipsum dolor sit amet, <a href="#!" class="text-c-blue"> More</a></p>
                                </div>
                            </div>
                            <div class="row p-b-30">
                                <div class="col-auto text-end update-meta p-r-0">
                                    <i class="b-primary update-icon ring"></i>
                                </div>
                                <div class="col p-l-5 m-l-10">
                                    <a href="#!"><h6>Showcases</h6></a>
                                    <p class="text-muted m-b-0">Lorem dolor sit amet, <a href="#!" class="text-c-blue"> More</a></p>
                                </div>
                            </div>
                            <div class="row p-b-30">
                                <div class="col-auto text-end update-meta p-r-0">
                                    <i class="b-success update-icon ring"></i>
                                </div>
                                <div class="col p-l-5 m-l-10">
                                    <a href="#!"><h6>Miscellaneous</h6></a>
                                    <p class="text-muted m-b-0">Lorem ipsum dolor sit ipsum amet, <a href="#!" class="text-c-green"> More</a></p>
                                </div>
                            </div>
                            <div class="row p-b-30">
                                <div class="col-auto text-end update-meta p-r-0">
                                    <i class="b-primary update-icon ring"></i>
                                </div>
                                <div class="col p-l-5 m-l-10">
                                    <a href="#!"><h6>Showcases</h6></a>
                                    <p class="text-muted m-b-0">Lorem dolor sit amet, <a href="#!" class="text-c-blue"> More</a></p>
                                </div>
                            </div>
                            <div class="row p-b-30">
                                <div class="col-auto text-end update-meta p-r-0">
                                    <i class="b-success update-icon ring"></i>
                                </div>
                                <div class="col p-l-5 m-l-10">
                                    <a href="#!"><h6>Miscellaneous</h6></a>
                                    <p class="text-muted m-b-0">Lorem ipsum dolor sit ipsum amet, <a href="#!" class="text-c-green"> More</a></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-auto text-end update-meta p-r-0">
                                    <i class="b-danger update-icon ring"></i>
                                </div>
                                <div class="col p-l-5 m-l-10">
                                    <a href="#!"><h6>Your Manager Posted.</h6></a>
                                    <p class="text-muted m-b-0">Lorem ipsum dolor sit amet, <a href="#!" class="text-c-red"> More</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="slimScrollBar" style="background: rgb(0, 0, 0); width: 5px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 186.475px;">
                    </div>
                    <div class="slimScrollRail" style="width: 5px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="" style="background: #B0DC00 url(../files/assets/images/city.png);
    width:100%;height:250px;background-repeat:no-repeat;background-position:100% 50px;background-size: 500px auto;border-radius: 15px;">
    <h3 class="f-w-600 pt-5 f-30 m-l-50">We are working<br />to boost lovely mood</h3>
    <button class="btn btn-inverse waves-effect waves-light m-l-50 mt-4">Create a Store</button>
</div>

</div>
<div class="col-lg-6">


</div>
</div>
