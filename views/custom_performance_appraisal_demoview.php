

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
                                        <input aria-label="<?= htmlspecialchars(__('Search Friend'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="search-friends" name="footer-email" class="form-control" placeholder="<?= htmlspecialchars(__('Search Friend'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <button class="btn btn-primary waves-effect waves-light" type="button">
                                            <i class="feather icon-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="main-friend-list">
                                <div class="media userlist-box waves-effect waves-light" data-id="1" data-status="online" data-username="Josephin Doe">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius img-radius" src="../files/assets/images/avatar-3.jpg" alt="<?= htmlspecialchars(__('Generic placeholder'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="live-status bg-success"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="chat-header"><?= __('Josephin Doe'); ?></div>
                                    </div>
                                </div>
                                <div class="media userlist-box waves-effect waves-light" data-id="2" data-status="online" data-username="Lary Doe">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius" src="../files/assets/images/avatar-2.jpg" alt="<?= htmlspecialchars(__('Generic placeholder'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="live-status bg-success"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="f-13 chat-header"><?= __('Lary Doe'); ?></div>
                                    </div>
                                </div>
                                <div class="media userlist-box waves-effect waves-light" data-id="3" data-status="online" data-username="Alice">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius" src="../files/assets/images/avatar-4.jpg" alt="<?= htmlspecialchars(__('Generic placeholder'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="live-status bg-success"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="f-13 chat-header"><?= __('Alice'); ?></div>
                                    </div>
                                </div>
                                <div class="media userlist-box waves-effect waves-light" data-id="4" data-status="offline" data-username="Alia">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius" src="../files/assets/images/avatar-3.jpg" alt="<?= htmlspecialchars(__('Generic placeholder'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="live-status bg-default"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="f-13 chat-header"><?= __('Alia'); ?><small class="d-block text-muted"><?= __('10 min ago'); ?></small></div>
                                    </div>
                                </div>
                                <div class="media userlist-box waves-effect waves-light" data-id="5" data-status="offline" data-username="Suzen">
                                    <a class="media-left" href="#!">
                                                <img class="media-object img-radius" src="../files/assets/images/avatar-2.jpg" alt="<?= htmlspecialchars(__('Generic placeholder'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="live-status bg-default"></div>
                                            </a>
                                    <div class="media-body">
                                        <div class="f-13 chat-header"><?= __('Suzen'); ?><small class="d-block text-muted"><?= __('15 min ago'); ?></small></div>
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
                        <i class="feather icon-x"></i> <?= __('Josephin Doe'); ?>
                    </a>
                </div>
                <div class="main-friend-chat">
                    <div class="d-flex chat-messages">
                        <a class="media-left photo-table" href="#!">
                            <div class="flex-shrink-0">
                                <img class="media-object img-radius img-radius m-t-5" src="../files/assets/images/avatar-2.jpg" alt="<?= htmlspecialchars(__('Generic placeholder'), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </a>
                        <div class="flex-grow-1 chat-menu-content">
                            <div class="">
                                <p class="chat-cont"><?= __('I\'m just looking around. Will you tell me something about yourself?'); ?></p>
                            </div>
                            <p class="chat-time">8:20 a.m.</p>
                        </div>
                    </div>
                    <div class="d-flex chat-messages">
                        <div class="flex-grow-1 chat-menu-reply">
                            <div class="">
                                <p class="chat-cont"><?= __('Ohh! very nice'); ?></p>
                            </div>
                            <p class="chat-time">8:22 a.m.</p>
                        </div>
                    </div>
                    <div class="d-flex chat-messages">
                        <a class="media-left photo-table" href="#!">
                            <div class="flex-shrink-0">
                                <img class="media-object img-radius img-radius m-t-5" src="../files/assets/images/avatar-2.jpg" alt="<?= htmlspecialchars(__('Generic placeholder'), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </a>
                        <div class="media-body chat-menu-content">
                            <div class="">
                                <p class="chat-cont"><?= __('can you come with me?'); ?></p>
                            </div>
                            <p class="chat-time">8:20 a.m.</p>
                        </div>
                    </div>
                </div>
                <div class="chat-reply-box">
                    <div class="right-icon-control">
                        <div class="input-group input-group-button">
                            <input aria-label="<?= htmlspecialchars(__('Write hear . .'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="<?= htmlspecialchars(__('Write hear . .'), ENT_QUOTES, 'UTF-8'); ?> ">
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
                                                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Employee Basic Information'); ?>
                                                            <p class="f-w-400"><?= __('Lorem Ipsum is simply dummy text of the printing and typesetting industry.'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="form-group row">
                                                                <div class="col-lg-4">
                                                                   <span><?= __('Name'); ?></span>
                                                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span><?= __('Emp ID'); ?></span>
                                                                   <input aria-label="<?= htmlspecialchars(__('DGT-009'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="<?= htmlspecialchars(__('DGT-009'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span><?= __('RO\'s Name'); ?></span>
                                                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                </div>
                                                             </div>
                                                             <div class="form-group row">
                                                                <div class="col-lg-4">
                                                                   <span><?= __('Department'); ?></span>
                                                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span><?= __('Date of Join'); ?></span>
                                                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span><?= __('RO Designation'); ?>:</span>
                                                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                </div>
                                                             </div>
                                                             <div class="form-group row">
                                                                <div class="col-lg-4">
                                                                   <span><?= __('Designation'); ?></span>
                                                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span><?= __('Date of Confirmation'); ?></span>
                                                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                </div>
                                                             </div>
                                                             <div class="form-group row">
                                                                <div class="col-lg-4">
                                                                   <span><?= __('Qualification'); ?>:</span>
                                                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                </div>
                                                                <div class="col-lg-4">
                                                                   <span><?= __('Previous years of Exp'); ?></span>
                                                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                </div>
                                                             </div>
                                                        </div>
                                                        <a class="accordion-msg bg-dark-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Professional Excellence'); ?>
                                                            <p class="f-w-400"><?= __('Lorem Ipsum is simply dummy text of the printing and typesetting industry.'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                       <tr>
                                                                          <th scope="col">#</th>
                                                                          <th scope="col"><?= __('Key Result Area'); ?></th>
                                                                          <th scope="col"><?= __('Key Performance Indicators'); ?></th>
                                                                          <th scope="col"><?= __('Weightage'); ?></th>
                                                                          <th scope="col"><?= __('Percentage achieved'); ?><br /><?= __('(self Score)'); ?></th>
                                                                          <th scope="col"><?= __('Points Scored'); ?><br /><?= __('(self)'); ?></th>
                                                                          <th scope="col"><?= __('Percentage achieved'); ?><br /><?= __('(RO\'s Score)'); ?></th>
                                                                          <th scope="col"><?= __('Points Scored'); ?><br /><?= __('(RO)'); ?></th>
                                                                       </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                       <tr>
                                                                          <th scope="col" rowspan="2">1</th>
                                                                          <td rowspan="2"><?= __('Production'); ?></td>
                                                                          <td><?= __('Quality'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="30" type="text" class="form-control" disabled="disabled" placeholder="30">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td><?= __('TAT (turn around time)'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="30" type="text" class="form-control" disabled="disabled" placeholder="30">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>2</td>
                                                                          <td><?= __('Process Improvement'); ?></td>
                                                                          <td><?= __('PMS,New Ideas'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="10" type="text" class="form-control" disabled="disabled" placeholder="10">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>3</td>
                                                                          <td><?= __('Team Management'); ?></td>
                                                                          <td><?= __('Team Productivity,dynaics,attendance,attrition'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="5" type="text" class="form-control" disabled="disabled" placeholder="5">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>4</td>
                                                                          <td><?= __('Knowledge Sharing'); ?></td>
                                                                          <td><?= __('Sharing the knowledge for team productivity'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="5" type="text" class="form-control" disabled="disabled" placeholder="5">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>5</td>
                                                                          <td><?= __('Reporting and Communication'); ?></td>
                                                                          <td><?= __('Emails/Calls/Reports and Other Communication'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="5" type="text" class="form-control" disabled="disabled" placeholder="5">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <th scope="col" colspan="3" class="text-center"><?= __('Total'); ?></th>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="85" type="text" class="form-control" disabled="disabled" placeholder="85">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                    </tbody>
                                                                 </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Personal Excellence'); ?>
                                                            <p class="f-w-400"><?= __('Lorem Ipsum is simply dummy text of the printing and typesetting industry.'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                       <tr>
                                                                          <th scope="col">#</th>
                                                                          <th scope="col"><?= __('Personal Attributes'); ?></th>
                                                                          <th scope="col"><?= __('Key Indicators'); ?></th>
                                                                          <th scope="col"><?= __('Weightage'); ?></th>
                                                                          <th scope="col"><?= __('Percentage achieved'); ?><br /><?= __('(self Score)'); ?></th>
                                                                          <th scope="col"><?= __('Points Scored'); ?><br /><?= __('(self)'); ?></th>
                                                                          <th scope="col"><?= __('Percentage achieved'); ?><br /><?= __('(RO\'s Score)'); ?></th>
                                                                          <th scope="col"><?= __('Points Scored'); ?><br /><?= __('(RO)'); ?></th>
                                                                       </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                       <tr>
                                                                          <th scope="col" rowspan="2">1</th>
                                                                          <td rowspan="2"><?= __('Attendance'); ?></td>
                                                                          <td><?= __('Planned or Unplanned Leaves'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td><?= __('Time Consciousness'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <th scope="col" rowspan="2">2</th>
                                                                          <td rowspan="2"><?= htmlspecialchars(__('Attitude & Behavior'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                                          <td><?= __('Team Collaboration'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td><?= htmlspecialchars(__('Professionalism & Responsiveness'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>3</td>
                                                                          <td><?= htmlspecialchars(__('Policy & Procedures'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                                          <td><?= __('Adherence to policies and procedures'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>4</td>
                                                                          <td><?= __('Initiatives'); ?></td>
                                                                          <td><?= __('Special Efforts, Suggestions,Ideas,etc.'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td>5</td>
                                                                          <td><?= __('Continuous Skill Improvement'); ?></td>
                                                                          <td><?= htmlspecialchars(__('Preparedness to move to next level & Training utilization'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="2" type="text" class="form-control" disabled="disabled" placeholder="2">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <th scope="col" colspan="3" class="text-center"><?= __('Total'); ?></th>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="15" type="text" class="form-control" disabled="disabled" placeholder="15">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                          <td>
                                                                             <div class="">
                                                                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" disabled="disabled" placeholder="0">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <th scope="col" colspan="3" class="text-center"><?= __('Total Percentage(%)'); ?></th>
                                                                          <td colspan="5">
                                                                             <div class="">
                                                                                <input aria-label="15" type="text" class="form-control" disabled="disabled" placeholder="15">
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td colspan="8" class="text-center">
                                                                             <div class="grade-span">
                                                                                <h4><?= __('Grade'); ?></h4>
                                                                                <span class="label label-lg label-success label-inline"><b><?= __('Below 65 Poor'); ?></b></span>
                                                                                <span class="label label-lg label-danger label-inline"><b><?= __('65-74 Average'); ?></b></span>
                                                                                <span class="label label-lg label-info label-inline"><b><?= __('75-84 Satisfactory'); ?></b></span>
                                                                                <span class="label label-lg label-primary label-inline"><b><?= __('85-92 Good'); ?></b></span>
                                                                                <span class="label label-lg label-danger label-inline"><b><?= __('Above 92 Excellent'); ?></b></span>
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                    </tbody>
                                                                 </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Special Initiatives, Achievements, contributions if any'); ?>
                                                            <p class="f-w-400"><?= __('Lorem Ipsum is simply dummy text of the printing and typesetting industry.'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                       <tr>
                                                                          <th scope="col">#</th>
                                                                          <th scope="col"><?= __('By Self'); ?></th>
                                                                          <th scope="col"><?= __('RO\'s Comment'); ?></th>
                                                                          <th scope="col"><?= __('HOD\'s Comment'); ?></th>
                                                                       </tr>
                                                                    </thead>
                                                                    <tbody data-repeater-list="">
                                                                        <tr data-repeater-item="">
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <a href="" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger btn-primary">
                                                                                    <i class="fa fa-trash"></i><?= __('Delete'); ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <a href="" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary">
                                                                    <i class="fa fa-plus"></i><?= __('Add'); ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Comments on the role'); ?>
                                                            <p class="f-w-400"><?= __('alterations if any requirred like addition/deletion of responsibilities'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col"><?= __('By Self'); ?></th>
                                                                            <th scope="col"><?= __('RO\'s Comment'); ?></th>
                                                                            <th scope="col"><?= __('HOD\'s Comment'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                <div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary">
                                                                                    <i class="fa fa-plus"></i><?= __('Add More'); ?>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Comments on the role'); ?>
                                                            <p class="f-w-400"><?= __('alterations if any requirred like addition/deletion of responsibilities'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col"><?= __('Strengths'); ?></th>
                                                                            <th scope="col"><?= __('Area\'s for Improvement'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Appraisee\'s Strengths and Areas for Improvement perceived by the Reporting officer'); ?>
                                                            <p class="f-w-400"><?= __('Lorem ipsum dollar'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col"><?= __('Strengths'); ?></th>
                                                                            <th scope="col"><?= __('Area\'s for Improvement'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Personal Goals'); ?>
                                                            <p class="f-w-400"><?= __('Lorem ipsum dollar'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col"><?= __('Goal Achieved during last year'); ?></th>
                                                                            <th scope="col"><?= __('Goal set for current year'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Personal Updates'); ?>
                                                            <p class="f-w-400"><?= __('Lorem ipsum dollar'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col"><?= __('Last Year'); ?></th>
                                                                            <th scope="col"><?= __('Yes/No'); ?></th>
                                                                            <th scope="col"><?= __('Details'); ?></th>
                                                                            <th scope="col"><?= __('Current Year'); ?></th>
                                                                            <th scope="col"><?= __('Yes/No'); ?></th>
                                                                            <th scope="col"><?= __('Details'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <?= __('Married/Engaged?'); ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <?= __('Marriage Plans'); ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                 <?= __('Higher Studies/Certifications?'); ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <?= __('Plans For Higher Study'); ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <?= __('Health Issues?'); ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <?= __('Certification Plans'); ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <?= __('Others'); ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <?= __('Others'); ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Professional Goals Achieved for last year'); ?>
                                                            <p class="f-w-400"><?= __('Lorem ipsum dollar'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col"><?= __('By Self'); ?></th>
                                                                            <th scope="col"><?= __('RO\'s Comment'); ?></th>
                                                                            <th scope="col"><?= __('HOD\'s Comment'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary"><i class="fa fa-plus"></i><?= __('Add More'); ?></button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Professional Goals for the forthcoming year'); ?>
                                                            <p class="f-w-400"><?= __('Lorem ipsum dollar'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col"><?= __('By Self'); ?></th>
                                                                            <th scope="col"><?= __('RO\'s Comment'); ?></th>
                                                                            <th scope="col"><?= __('HOD\'s Comment'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary">
                                                                                    <i class="fa fa-plus"></i><?= __('Add More'); ?>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Training Requirements'); ?>
                                                            <p class="f-w-400"><?= __('if any to achieve the Performance Standard Targets completely'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col"><?= __('By Self'); ?></th>
                                                                            <th scope="col"><?= __('RO\'s Comment'); ?></th>
                                                                            <th scope="col"><?= __('HOD\'s Comment'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary">
                                                                                    <i class="fa fa-plus"></i><?= __('Add More'); ?>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Any other general comments, observations, suggestions etc.'); ?>
                                                            <p class="f-w-400"><?= __('Lorem ipsum dollar'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col"><?= __('Self'); ?></th>
                                                                            <th scope="col"><?= __('RO'); ?></th>
                                                                            <th scope="col"><?= __('HOD'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">1</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">2</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">3</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">4</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">5</th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <button class="btn btn-primary">
                                                                                    <i class="fa fa-plus"></i><?= __('Add More'); ?>
                                                                                </button>
                                                                            </td>
                                                                       </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('For RO\'s Use Only'); ?>
                                                            <p class="f-w-400"><?= __('Lorem ipsum dollar'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col"></th>
                                                                            <th scope="col"><?= __('Yes/No'); ?></th>
                                                                            <th scope="col"><?= __('If Yes - Details'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('The Team member has Work related Issues'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control border">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('The Team member has Work related Issues'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control border">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('The Team member has Work related Issues'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control border">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('The Team member has Work related Issues'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control border">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('The Team member has Work related Issues'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control border">
                                                                                        <option><?= __('Select'); ?></option>
                                                                                        <option><?= __('Yes'); ?></option>
                                                                                        <option><?= __('No'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('For HRD\'s Use Only'); ?>
                                                            <p class="f-w-400"><?= __('Lorem ipsum dollar'); ?></p>
                                                        </a>
                                                        <div class="accordion-desc">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col"><?= __('Overall Parameters'); ?></th>
                                                                            <th scope="col"><?= __('Available Points'); ?></th>
                                                                            <th scope="col"><?= __('Points Scored'); ?></th>
                                                                            <th scope="col"><?= __('RO\'s Comment'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('KRAs Target Achievement Points (will be considered from the overall score specified in this document by the Reporting officer)'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= htmlspecialchars(__('Professional Skills Scores(RO\'s Points furnished in the skill & attitude assessment sheet will be considered)'), ENT_QUOTES, 'UTF-8'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= htmlspecialchars(__('Personal Skills Scores(RO\'s Points furnished in the skill & attitude assessment sheet will be considered)'), ENT_QUOTES, 'UTF-8'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('Special Achievements Score (HOD to furnish)'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('Overall Total Score'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
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
                                                                            <th scope="col"><?= __('Name'); ?></th>
                                                                            <th scope="col"><?= __('Signature'); ?></th>
                                                                            <th scope="col"><?= __('Date'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('Employee'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('Reporting Officer'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('HOD'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">
                                                                                <?= __('HRD'); ?>
                                                                            </th>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="">
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

