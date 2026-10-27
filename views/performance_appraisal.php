<?php include_once 'header.php';?>

<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    <!-- [ Pre-loader ] end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <!-- [ Header ] start -->

            <?php include_once 'top-header.php';?>


            <div id="sidebar" class="users p-chat-user showChat">
                <div class="had-container">
                    <div class="p-fixed users-main">
                        <div class="user-box">
                            <div class="chat-search-box">
                                <a class="back_friendlist">
                                    <i class="feather icon-x"></i>
                                </a>
                                <div class="right-icon-control">
                                    <div class="input-group input-group-button">
                                        <input aria-label="Search Friend" type="text" id="search-friends" name="footer-email" class="form-control" placeholder="Search Friend">
                                        <button class="btn btn-primary waves-effect waves-light" type="button">
                                            <i class="feather icon-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="main-friend-list">
                                <div class="media userlist-box waves-effect waves-light" data-id="1" data-status="online" data-username="Josephin Doe">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius img-radius" src="../files/assets/images/avatar-3.jpg" alt="Generic placeholder">
                                                <div class="live-status bg-success"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="chat-header">Josephin Doe</div>
                                    </div>
                                </div>
                                <div class="media userlist-box waves-effect waves-light" data-id="2" data-status="online" data-username="Lary Doe">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder">
                                                <div class="live-status bg-success"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="f-13 chat-header">Lary Doe</div>
                                    </div>
                                </div>
                                <div class="media userlist-box waves-effect waves-light" data-id="3" data-status="online" data-username="Alice">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius" src="../files/assets/images/avatar-4.jpg" alt="Generic placeholder">
                                                <div class="live-status bg-success"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="f-13 chat-header">Alice</div>
                                    </div>
                                </div>
                                <div class="media userlist-box waves-effect waves-light" data-id="4" data-status="offline" data-username="Alia">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius" src="../files/assets/images/avatar-3.jpg" alt="Generic placeholder">
                                                <div class="live-status bg-default"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="f-13 chat-header">Alia<small class="d-block text-muted">10 min ago</small></div>
                                    </div>
                                </div>
                                <div class="media userlist-box waves-effect waves-light" data-id="5" data-status="offline" data-username="Suzen">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder">
                                                <div class="live-status bg-default"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="f-13 chat-header">Suzen<small class="d-block text-muted">15 min ago</small></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- [ chat message ] start -->
            <div class="showChat_inner">
                <div class="d-flex chat-inner-header">
                    <a class="back_chatBox">
                        <i class="feather icon-x"></i> Josephin Doe
                    </a>
                </div>
                <div class="main-friend-chat">
                    <div class="d-flex chat-messages">
                        <a class="media-left photo-table" href="#!">
                            <div class="flex-shrink-0">
                                <img class="media-object img-radius img-radius m-t-5" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder">
                            </div>
                        </a>
                        <div class="flex-grow-1 chat-menu-content">
                            <div class="">
                                <p class="chat-cont">I'm just looking around. Will you tell me something about yourself?</p>
                            </div>
                            <p class="chat-time">8:20 a.m.</p>
                        </div>
                    </div>
                    <div class="d-flex chat-messages">
                        <div class="flex-grow-1 chat-menu-reply">
                            <div class="">
                                <p class="chat-cont">Ohh! very nice</p>
                            </div>
                            <p class="chat-time">8:22 a.m.</p>
                        </div>
                    </div>
                    <div class="d-flex chat-messages">
                        <a class="media-left photo-table" href="#!">
                            <div class="flex-shrink-0">
                                <img class="media-object img-radius img-radius m-t-5" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder">
                            </div>
                        </a>
                        <div class="media-body chat-menu-content">
                            <div class="">
                                <p class="chat-cont">can you come with me?</p>
                            </div>
                            <p class="chat-time">8:20 a.m.</p>
                        </div>
                    </div>
                </div>
                <div class="chat-reply-box">
                    <div class="right-icon-control">
                        <div class="input-group input-group-button">
                            <input aria-label="Write hear . ." type="text" class="form-control" placeholder="Write hear . . ">
                            <button class="btn btn-primary waves-effect waves-light" type="button">
                                <i class="feather icon-message-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ chat message ] end -->
            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <!-- [ navigation menu ] start -->
                    <?php include_once 'sidebar.php';?>
                    <!-- [ navigation menu ] end -->
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <!-- Main-body start -->
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <!-- Page-body start -->
                                    <div class="page-body">
                                        <div class="card mt-4">
                                            <div class="card-body">
                                                <div class="accordion-block color-accordion-block">
                                                    <div class="color-accordion" id="color-accordion">
                                                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Employee Basic Information
                                                            <p class="f-w-400">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="form-group row">
                                                                <div class="col-lg-4">
                                                                   <span>Name</span>
                                                                   <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span>Emp ID</span>
                                                                   <input aria-label="DGT-009" type="text" class="form-control" placeholder="DGT-009">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span>RO's Name</span>
                                                                   <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                </div>
                                                             </div>
                                                             <div class="form-group row">
                                                                <div class="col-lg-4">
                                                                   <span>Department</span>
                                                                   <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span>Date of Join</span>
                                                                   <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span>RO Designation:</span>
                                                                   <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                </div>
                                                             </div>
                                                             <div class="form-group row">
                                                                <div class="col-lg-4">
                                                                   <span>Designation</span>
                                                                   <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span>Date of Confirmation</span>
                                                                   <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                </div>
                                                             </div>
                                                             <div class="form-group row">
                                                                <div class="col-lg-4">
                                                                   <span>Qualification:</span>
                                                                   <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span>Previous years of Exp</span>
                                                                   <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                </div>
                                                             </div>
                                                        </div>
                                                        <a class="accordion-msg bg-dark-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Professional Excellence
                                                            <p class="f-w-400">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                       <tr>
                                                                          <th scope="col">#</th>
                                                                          <th scope="col">Key Result Area</th>
                                                                          <th scope="col">Key Performance Indicators</th>
                                                                          <th scope="col">Weightage</th>
                                                                          <th scope="col">Percentage achieved<br />(self Score)</th>
                                                                          <th scope="col">Points Scored<br />(self)</th>
                                                                          <th scope="col">Percentage achieved<br />(RO's Score)</th>
                                                                          <th scope="col">Points Scored<br />(RO)</th>
                                                                       </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                       <tr>
                                                                          <th scope="col" rowspan="2">1</th>
                                                                          <td rowspan="2">Production</td>
                                                                          <td>Quality</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="30" type="text" class="form-control" disabled="disabled" placeholder="30">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>TAT (turn around time)</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="30" type="text" class="form-control" disabled="disabled" placeholder="30">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>2</td>
                                                                          <td>Process Improvement</td>
                                                                          <td>PMS,New Ideas</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="10" type="text" class="form-control" disabled="disabled" placeholder="10">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>3</td>
                                                                          <td>Team Management</td>
                                                                          <td>Team Productivity,dynaics,attendance,attrition</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="5" type="text" class="form-control" disabled="disabled" placeholder="5">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>4</td>
                                                                          <td>Knowledge Sharing</td>
                                                                          <td>Sharing the knowledge for team productivity</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="5" type="text" class="form-control" disabled="disabled" placeholder="5">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>5</td>
                                                                          <td>Reporting and Communication</td>
                                                                          <td>Emails/Calls/Reports and Other Communication</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="5" type="text" class="form-control" disabled="disabled" placeholder="5">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <th scope="col" colspan="3" class="text-center">Total</th>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="85" type="text" class="form-control" disabled="disabled" placeholder="85">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                    </tbody>
                                                                 </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Personal Excellence
                                                            <p class="f-w-400">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                       <tr>
                                                                          <th scope="col">#</th>
                                                                          <th scope="col">Personal Attributes</th>
                                                                          <th scope="col">Key Indicators</th>
                                                                          <th scope="col">Weightage</th>
                                                                          <th scope="col">Percentage achieved<br />(self Score)</th>
                                                                          <th scope="col">Points Scored<br />(self)</th>
                                                                          <th scope="col">Percentage achieved<br />(RO's Score)</th>
                                                                          <th scope="col">Points Scored<br />(RO)</th>
                                                                       </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                       <tr>
                                                                          <th scope="col" rowspan="2">1</th>
                                                                          <td rowspan="2">Attendance</td>
                                                                          <td>Planned or Unplanned Leaves</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>Time Consciousness</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <th scope="col" rowspan="2">2</th>
                                                                          <td rowspan="2">Attitude &amp; Behavior</td>
                                                                          <td>Team Collaboration</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>Professionalism &amp; Responsiveness</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>3</td>
                                                                          <td>Policy &amp; Procedures</td>
                                                                          <td>Adherence to policies and procedures</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>4</td>
                                                                          <td>Initiatives</td>
                                                                          <td>Special Efforts, Suggestions,Ideas,etc.</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>5</td>
                                                                          <td>Continuous Skill Improvement</td>
                                                                          <td>Preparedness to move to next level &amp; Training utilization</td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <th scope="col" colspan="3" class="text-center">Total</th>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="15" type="text" class="form-control" disabled="disabled" placeholder="15">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="Input field" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <th scope="col" colspan="3" class="text-center">Total Percentage(%)</th>
                                                                          <td colspan="5">
                                                                             <div class="">
                                                                                <input aria-label="15" type="text" class="form-control" disabled="disabled" placeholder="15">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td colspan="8" class="text-center">
                                                                             <div class="grade-span">
                                                                                <h4>Grade</h4>
                                                                                <span class="label label-lg label-success label-inline"><b>Below 65 Poor</b></span>
                                                                                <span class="label label-lg label-danger label-inline"><b>65-74 Average</b></span>
                                                                                <span class="label label-lg label-info label-inline"><b>75-84 Satisfactory</b></span>
                                                                                <span class="label label-lg label-primary label-inline"><b>85-92 Good</b></span>
                                                                                <span class="label label-lg label-danger label-inline"><b>Above 92 Excellent</b></span>
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                    </tbody>
                                                                 </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Special Initiatives, Achievements, contributions if any
                                                            <p class="f-w-400">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                       <tr>
                                                                          <th scope="col">#</th>
                                                                          <th scope="col">By Self</th>
                                                                          <th scope="col">RO's Comment</th>
                                                                          <th scope="col">HOD's Comment</th>
                                                                       </tr>
                                                                    </thead>
                                                                    <tbody data-repeater-list="">
                                                                        <tr data-repeater-item="">
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <a href="" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger btn-primary">
                                                                                    <i class="fa fa-trash"></i>Delete
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <a href="" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary">
                                                                    <i class="fa fa-plus"></i>Add
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Comments on the role
                                                            <p class="f-w-400">alterations if any requirred like addition/deletion of responsibilities</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">By Self</th>
                                                                            <th scope="col">RO's Comment</th>
                                                                            <th scope="col">HOD's Comment</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                <div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary">
                                                                                    <i class="fa fa-plus"></i>Add More
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Comments on the role
                                                            <p class="f-w-400">alterations if any requirred like addition/deletion of responsibilities</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">Strengths</th>
                                                                            <th scope="col">Area's for Improvement</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Appraisee's Strengths and Areas for Improvement perceived by the Reporting officer
                                                            <p class="f-w-400">Lorem ipsum dollar</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">Strengths</th>
                                                                            <th scope="col">Area's for Improvement</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Personal Goals
                                                            <p class="f-w-400">Lorem ipsum dollar</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">Goal Achieved during last year</th>
                                                                            <th scope="col">Goal set for current year</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Personal Updates
                                                            <p class="f-w-400">Lorem ipsum dollar</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">Last Year</th>
                                                                            <th scope="col">Yes/No</th>
                                                                            <th scope="col">Details</th>
                                                                            <th scope="col">Current Year</th>
                                                                            <th scope="col">Yes/No</th>
                                                                            <th scope="col">Details</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                Married/Engaged?
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                Marriage Plans
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                 Higher Studies/Certifications?
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                Plans For Higher Study
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                Health Issues?
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                Certification Plans
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                Others
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                Others
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Professional Goals Achieved for last year
                                                            <p class="f-w-400">Lorem ipsum dollar</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">By Self</th>
                                                                            <th scope="col">RO's Comment</th>
                                                                            <th scope="col">HOD's Comment</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary"><i class="fa fa-plus"></i>Add More</button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Professional Goals for the forthcoming year
                                                            <p class="f-w-400">Lorem ipsum dollar</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">By Self</th>
                                                                            <th scope="col">RO's Comment</th>
                                                                            <th scope="col">HOD's Comment</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary">
                                                                                    <i class="fa fa-plus"></i>Add More
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Training Requirements
                                                            <p class="f-w-400">if any to achieve the Performance Standard Targets completely</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">By Self</th>
                                                                            <th scope="col">RO's Comment</th>
                                                                            <th scope="col">HOD's Comment</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary">
                                                                                    <i class="fa fa-plus"></i>Add More
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Any other general comments, observations, suggestions etc.
                                                            <p class="f-w-400">Lorem ipsum dollar</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">Self</th>
                                                                            <th scope="col">RO</th>
                                                                            <th scope="col">HOD</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary">
                                                                                    <i class="fa fa-plus"></i>Add More
                                                                                </button>
                                                                            </td>
                                                                       </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">For RO's Use Only
                                                            <p class="f-w-400">Lorem ipsum dollar</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col"></th>
                                                                            <th scope="col">Yes/No</th>
                                                                            <th scope="col">If Yes - Details</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                The Team member has Work related Issues
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control border">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                The Team member has Work related Issues
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control border">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                The Team member has Work related Issues
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control border">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                The Team member has Work related Issues
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control border">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                The Team member has Work related Issues
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="Select option" class="form-control border">
                                                                                        <option>Select</option>
                                                                                        <option>Yes</option>
                                                                                        <option>No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">For HRD's Use Only
                                                            <p class="f-w-400">Lorem ipsum dollar</p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">Overall Parameters</th>
                                                                            <th scope="col">Available Points</th>
                                                                            <th scope="col">Points Scored</th>
                                                                            <th scope="col">RO's Comment</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                KRAs Target Achievement Points (will be considered from the overall score specified in this document by the Reporting officer)
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                Professional Skills Scores(RO's Points furnished in the skill &amp; attitude assessment sheet will be considered)
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                Personal Skills Scores(RO's Points furnished in the skill &amp; attitude assessment sheet will be considered)
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                Special Achievements Score (HOD to furnish)
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                Overall Total Score
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="table-responsive mt-3">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col"></th>
                                                                            <th scope="col">Name</th>
                                                                            <th scope="col">Signature</th>
                                                                            <th scope="col">Date</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                Employee
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                Reporting Officer
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                HOD
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                HRD
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="Input field" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                       </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Page-body start -->
                                </div>
                            </div>
                            <!-- Main-body start -->
                        </div>
                        <?php include_once 'footer.php';?>

