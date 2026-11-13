<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">
                <!-- [ page content ] start -->
                <div class="row">

                    <!-- sale revenue card start -->
                    <div class="col-md-12 col-xl-8">
                        <div class="card sale-card">
                            <div class="card-header">
                                <h5><?= __('Deals Analytics'); ?></h5>
                            </div>
                            <div class="card-block">
                                <div id="sales-analytics" class="chart-shadow" style="height:380px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-xl-4">
                        <div class="card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-b-25"><?= __('Impressions'); ?></h6>
                                        <h3 class="f-w-700 text-c-blue">1,563</h3>
                                        <p class="m-b-0"><?= __('May 23 - June 01 (2017)'); ?></p>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-eye bg-c-blue"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-b-25"><?= __('Goal'); ?></h6>
                                        <h3 class="f-w-700 text-c-green">30,564</h3>
                                        <p class="m-b-0"><?= __('May 23 - June 01 (2017)'); ?></p>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-bullseye bg-c-green"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-b-25"><?= __('Impact'); ?></h6>
                                        <h3 class="f-w-700 text-c-yellow">42.6%</h3>
                                        <p class="m-b-0"><?= __('May 23 - June 01 (2017)'); ?></p>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-hand-paper bg-c-yellow"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- sale revenue card end -->

                    <!-- Project statustic start -->
                    <div class="col-xl-12">
                        <div class="card proj-progress-card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-xl-3 col-md-6">
                                        <h6><?= __('Published Project'); ?></h6>
                                        <h5 class="m-b-30 f-w-700">532<span class="text-c-green m-l-10">+1.69%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-c-red" style="width:25%"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
                                        <h6><?= __('Completed Task'); ?></h6>
                                        <h5 class="m-b-30 f-w-700">4,569<span class="text-c-red m-l-10">-0.5%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-c-blue" style="width:65%"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
                                        <h6><?= __('Successfull Task'); ?></h6>
                                        <h5 class="m-b-30 f-w-700">89%<span class="text-c-green m-l-10">+0.99%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-c-green" style="width:85%"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
                                        <h6><?= __('Ongoing Project'); ?></h6>
                                        <h5 class="m-b-30 f-w-700">365<span class="text-c-green m-l-10">+0.35%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-c-yellow" style="width:45%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Project statustic end -->

                    <!-- sale 2 card start -->
                    <div class="col-md-12 col-xl-4">
                        <div class="card card-blue text-white">
                            <div class="card-block p-b-0">
                                <div class="row m-b-50">
                                    <div class="col">
                                        <h6 class="m-b-5"><?= __('Sales In July'); ?></h6>
                                        <h5 class="m-b-0 f-w-700">$2665.00</h5>
                                    </div>
                                    <div class="col-auto text-center">
                                        <p class="m-b-5"><?= __('Direct Sale'); ?></p>
                                        <h6 class="m-b-0">$1768</h6>
                                    </div>

                                    <div class="col-auto text-center">
                                        <p class="m-b-5"><?= __('Referal'); ?></p>
                                        <h6 class="m-b-0">$897</h6>
                                    </div>
                                </div>

                                <div id="sec-ecommerce-chart-line" class="" style="height:60px"></div>
                                <div id="sec-ecommerce-chart-bar" style="height:195px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-12">
                        <div class="card latest-update-card">
                            <div class="card-header">
                                <h5><?= __('What’s New'); ?></h5>
                                <div class="card-header-right">
                                    <ul class="list-unstyled card-option">
                                        <li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
                                        <li><i class="feather icon-maximize full-card"></i></li>
                                        <li><i class="feather icon-minus minimize-card"></i></li>
                                        <li><i class="feather icon-refresh-cw reload-card"></i></li>
                                        <li><i class="feather icon-trash close-card"></i></li>
                                        <li><i class="feather icon-chevron-left open-card-option"></i></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-block">
                                <div class="scroll-widget">
                                    <div class="latest-update-box">
                                        <div class="row p-t-20 p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <img src="assets/images/avatar-4.jpg" alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-40 align-top m-r-15 update-icon">
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('Your Manager Posted.'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Jonny michel'); ?></p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="feather icon-briefcase bg-c-red update-icon"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('You have 3 pending Task.'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Hemilton'); ?></p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="feather icon-check f-w-600 bg-c-green update-icon"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('New Order Received.'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Hemilton'); ?></p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <img src="assets/images/avatar-4.jpg" alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-40 align-top m-r-15 update-icon">
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('Your Manager Posted.'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Jonny michel'); ?></p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="feather icon-briefcase bg-c-red update-icon"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('You have 3 pending Task.'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Hemilton'); ?></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="feather icon-check f-w-600 bg-c-green update-icon"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('New Order Received.'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Hemilton'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card latest-update-card">
                            <div class="card-header">
                                <h5><?= __('Latest Activity'); ?></h5>
                                <div class="card-header-right">
                                    <ul class="list-unstyled card-option">
                                        <li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
                                        <li><i class="feather icon-maximize full-card"></i></li>
                                        <li><i class="feather icon-minus minimize-card"></i></li>
                                        <li><i class="feather icon-refresh-cw reload-card"></i></li>
                                        <li><i class="feather icon-trash close-card"></i></li>
                                        <li><i class="feather icon-chevron-left open-card-option"></i></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-block">
                                <div class="scroll-widget">
                                    <div class="latest-update-box">
                                        <div class="row p-t-20 p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="b-primary update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= htmlspecialchars(__('Devlopment & Update'), ENT_QUOTES, 'UTF-8'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Lorem ipsum dolor sit amet,'); ?> <a href="#!" class="text-c-blue"> <?= __('More'); ?></a></p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="b-primary update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('Showcases'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Lorem dolor sit amet,'); ?> <a href="#!" class="text-c-blue"> <?= __('More'); ?></a></p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="b-success update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('Miscellaneous'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Lorem ipsum dolor sit ipsum amet,'); ?> <a href="#!" class="text-c-green"> <?= __('More'); ?></a></p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="b-danger update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('Your Manager Posted.'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Lorem ipsum dolor sit amet,'); ?> <a href="#!" class="text-c-red"> <?= __('More'); ?></a></p>
                                            </div>
                                        </div>
                                        <div class="row p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="b-primary update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('Showcases'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Lorem dolor sit amet,'); ?> <a href="#!" class="text-c-blue"> <?= __('More'); ?></a></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="b-success update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="#!"><h6><?= __('Miscellaneous'); ?></h6></a>
                                                <p class="text-muted m-b-0"><?= __('Lorem ipsum dolor sit ipsum amet,'); ?> <a href="#!" class="text-c-green"> <?= __('More'); ?></a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- sale 2 card end -->


                    <div class="col-md-12">
                        <div class="card table-card">
                            <div class="card-header">
                                <h5><?= __('New Products'); ?></h5>
                                <div class="card-header-right">
                                    <ul class="list-unstyled card-option">
                                        <li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
                                        <li><i class="feather icon-maximize full-card"></i></li>
                                        <li><i class="feather icon-minus minimize-card"></i></li>
                                        <li><i class="feather icon-refresh-cw reload-card"></i></li>
                                        <li><i class="feather icon-trash close-card"></i></li>
                                        <li><i class="feather icon-chevron-left open-card-option"></i></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-block p-b-0">
                                <div class="table-responsive">
                                    <table class="table table-hover m-b-0">
                                        <thead>
                                            <tr>
                                                <th scope="col"><?= __('Name'); ?></th>
                                                <th scope="col"><?= __('Product Code'); ?></th>
                                                <th scope="col"><?= __('Customer'); ?></th>
                                                <th scope="col"><?= __('Status'); ?></th>
                                                <th scope="col"><?= __('Rating'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= __('Sofa'); ?></td>
                                                <td><?= __('#PHD001'); ?></td>
                                                <td>abc@gmail.com</td>
                                                <td><span class="form-label label label-danger"><?= __('Out Stock'); ?></span></td>
                                                <td>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= __('Computer'); ?></td>
                                                <td><?= __('#PHD002'); ?></td>
                                                <td>cdc@gmail.com</td>
                                                <td><span class="form-label label label-success"><?= __('In Stock'); ?></span></td>
                                                <td>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= __('Mobile'); ?></td>
                                                <td><?= __('#PHD003'); ?></td>
                                                <td>pqr@gmail.com</td>
                                                <td><span class="form-label label label-danger"><?= __('Out Stock'); ?></span></td>
                                                <td>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= __('Coat'); ?></td>
                                                <td><?= __('#PHD004'); ?></td>
                                                <td>bcs@gmail.com</td>
                                                <td><span class="form-label label label-success"><?= __('In Stock'); ?></span></td>
                                                <td>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= __('Watch'); ?></td>
                                                <td><?= __('#PHD005'); ?></td>
                                                <td>cdc@gmail.com</td>
                                                <td><span class="form-label label label-success"><?= __('In Stock'); ?></span></td>
                                                <td>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= __('Shoes'); ?></td>
                                                <td><?= __('#PHD006'); ?></td>
                                                <td>pqr@gmail.com</td>
                                                <td><span class="form-label label label-danger"><?= __('Out Stock'); ?></span></td>
                                                <td>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-c-yellow"></i></a>
                                                    <a href="#!"><i class="fa fa-star f-12 text-default"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- lettest acivity and statustic card start -->

                    <!-- lettest acivity and statustic card end -->

                </div>
                <!-- [ page content ] end -->
            </div>
        </div>
    </div>
</div>
