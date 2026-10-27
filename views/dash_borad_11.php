
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content" style="margin-top: 20px;">
                        <!-- begin:: Content -->
                        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

                            <!--Begin::Dashboard 6-->

                            <!--begin:: Widgets/Stats-->

                            <!--end:: Widgets/Stats-->
                            <!--Begin::Row-->

                                <div class="row">
                                    <div class="col-xl-4 col-lg-4">

                                        <!--begin:: Widgets/Daily Sales-->
                                        <div class="kt-portlet kt-portlet--height-fluid">
                                            <div class="kt-widget14">
                                                <div class="kt-widget14__header kt-margin-b-30">
                                                    <h3 class="kt-widget14__title">
                                                        <?php $lnurl =  API."?mode=dailypettycashdetails";
                                                              $att_list = callAPI($lnurl);

                                                             $att_list = json_decode($att_list,1);
                                                              foreach ($att_list as  $at_list) {
                                                                  $len_data[] =  (int)$at_list['pc_limit']/100;
                                                                  $len_data2[] =  (int)$at_list['consumed']/100;
                                                              }
                                                              $len_data = implode(",", (array)$len_data);
                                                              $len_data2 = implode(",", (array)$len_data2);
                                                         ?>
                                                         <input type="hidden" id="len_data" value='<?php echo $len_data; ?>'>
                                                          <input type="hidden" id="len_data2" value='<?php echo $len_data2; ?>'>

                                                        Daily Petty Cash Expenses
                                                    </h3>
                                                    <span class="kt-widget14__desc">
                                                        Comparision With Previous Month
                                                    </span>
                                                </div>
                                                <div class="kt-widget14__chart" style="height:120px;">
                                                    <canvas id="kt_chart_daily_sales"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <!--end:: Widgets/Daily Sales-->
                                    </div>
                                    <div class="col-xl-4 col-lg-4">

                                        <!--begin:: Widgets/Profit Share-->
                                        <div class="kt-portlet kt-portlet--height-fluid">
                                            <div class="kt-widget14">
                                                <div class="kt-widget14__header">
                                                    <?php $lnurl1 =  API."?mode=dailypettycashmonth";
                                                              $att_list1 = callAPI($lnurl1);
                                                               $att_list1 = json_decode($att_list1,1);
                                                               $avalprec =  ($att_list1['consumed']/$att_list1 ['pc_limit'])*100;
                                                              ?>
                                                    <input type="hidden" id="len_data_cri" value='<?php echo $att_list1['pc_limit'].",".$att_list1['consumed']; ?>'>
                                                    <h3 class="kt-widget14__title">
                                                        Cash Balance
                                                    </h3>
                                                    <span class="kt-widget14__desc">
                                                        Petty Cash Balance At Outlet
                                                    </span>
                                                </div>
                                                <div class="kt-widget14__content">
                                                    <div class="kt-widget14__chart">
                                                        <div class="kt-widget14__stat">100</div>
                                                        <canvas id="kt_chart_profit_share" style="height: 140px; width: 140px;"></canvas>
                                                    </div>
                                                    <div class="kt-widget14__legends">
                                                        <div class="kt-widget14__legend">
                                                            <span class="kt-widget14__bullet kt-bg-success"></span>
                                                            <span class="kt-widget14__stats"><?php echo 100-$avalprec; ?>% Available</span>
                                                        </div>
                                                        <div class="kt-widget14__legend">
                                                            <span class="kt-widget14__bullet kt-bg-danger"></span>
                                                            <span class="kt-widget14__stats"><?php echo $avalprec; ?>% Spent</span>
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--end:: Widgets/Profit Share-->
                                    </div>
                                    <div class="col-xl-4 col-lg-4">

                                        <!--begin:: Widgets/Revenue Change-->
                                        <div class="kt-portlet kt-portlet--height-fluid">
                                            <div class="kt-widget14">
                                                <div class="kt-widget14__header">
                                                    <?php
                                                    $dayurl =  API."?mode=dailypettycashdaydetails";
                                                              $day_list = callAPI($dayurl);

                                                             $color = array('success', 'danger', 'brand', 'primary');

                                                             $day_list = json_decode($day_list,1);
                                                             $day_total = 0;
                                                             foreach ($day_list as $day_value) {
                                                                 $day_total =  $day_total + $day_value['amount'];
                                                             }
                                                             $day_item = array();
                                                             $n=0;
                                                             foreach ($day_list as $day_value) {
    $day_item['name'][$n] =  $day_value['item_id'] != -1  ? $day_value['item_name'] : "Other";
                                                                 $day_item['percentage'][$n] = ($day_value['amount']/$day_total)*100;
                                                                 $day_item['color'][$n] = $color[$n];
                                                                 $day_items[] = array('name' =>$day_item['name'][$n] ,'percentage' =>$day_item['percentage'][$n],'color' =>$day_item['color'][$n]);
                                                                 $n++;
                                                             }
            $day_petty_item = implode(",", (array)$day_item['name']);
                                                            $day_petty_item_pre = implode(",", (array)$day_item['percentage']);
                                                            $day_petty_item_color = implode(",", (array)$day_item['color']);
                                                    ?>
                                                    <input type="hidden" id="day_petty_item" value="<?php echo $day_petty_item; ?>">
                                                    <input type="hidden" id="day_petty_item_pre" value="<?php echo $day_petty_item_pre; ?>">
                                                    <input type="hidden" id="day_petty_item_color" value="<?php echo $day_petty_item_color; ?>">
                                                    <h3 class="kt-widget14__title">
                                                        Spending Trend
                                                    </h3>
                                                    <span class="kt-widget14__desc">
                                                        Petty Cash Spending Trend
                                                    </span>
                                                </div>
                                                <div class="kt-widget14__content">
                                                    <div class="kt-widget14__chart">
                                                        <div id="kt_chart_revenue_change" style="height: 150px; width: 150px;"></div>
                                                    </div>
                                                    <div class="kt-widget14__legends">
                                                        <?php foreach ($day_items as $day_petty ){ ?>
                                                        <div class="kt-widget14__legend">
                                                            <span class="kt-widget14__bullet kt-bg-<?php echo  $day_petty['color']; ?>"></span>
                                                            <span class="kt-widget14__stats"><?php echo $day_petty['percentage'];?>% <?php echo $day_petty['name'];?></span>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!--Begin::Row-->

                            <!--- END Row  -->
                            <!--- Begin Row-->


                            <!--End::Row-->

                            <div class="row">
                                <div class="col-xl-6">
                                    <?php
                                        $monthurl =  API."?mode=montlypettycashdetails";
                                        $month_list = callAPI($monthurl);
                                        $month_list = json_decode($month_list,1);
                                    ?>
                                    <!--begin:: Earnings-->
                                    <div class="kt-portlet kt-portlet--height-fluid">
                                        <div class="kt-portlet__head">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                    Branch-Wise Details
                                                </h3>
                                            </div>

                                        </div>
                                        <div class="kt-portlet__body kt-portlet__body--fit">
                                            <div class="kt-widget30" id="kt_earnings_widget">
                                                <div class="kt-widget30__head">
                                                    <div class="owl-carousel">
                                                        <?php foreach ($month_list as  $month_lis) {?>


                                                        <div class="carousel"><span><?php echo $month_lis['name']; ?></span>
                                                            <span><?php echo $month_lis['description']; ?></span></div>
                                                        <?php } ?>

                                                    </div>
                                                </div>
                                                <div class="kt-widget30__body">
                                                    <div class="owl-carousel">
                                                        <?php foreach ($month_list as  $month_lis) {?>
                                                        <div class="carousel">
                                                            <div class="kt-widget30__items">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>

                                                                            <th scope="col" style="width:40%">Expenses </th>
                                                                            <th scope="col" style="width:14%">Limit</th>
                                                                            <th scope="col" style="width:14%">Consumed </th>
                                                                            <th scope="col" style="width:15%">Status</th>
                                                                            <th scope="col" style="width:15%" class="kt-align-right">Available </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($month_lis['data'] as $month_l) {
                                                                                $pre = ($month_l['consumed']/$month_l['pc_limit'])*100;
                                                                        ?>

                                                                        <tr>

                                                                            <td>
                                                                                <a href="#" class="kt-widget11__title"><?php echo $month_l['name']; ?></a>

                                                                            </td>
                                                                            <td><?php echo $month_l['pc_limit']; ?></td>
                                                                            <td><?php echo $month_l['consumed']; ?></td>
                                                                            <td>
                                                                                <div class="progress progress--sm">
                                                                                        <div class="progress-bar kt-bg-info" role="progressbar" style="width: <?php echo $pre; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="kt-align-right kt-font-brand kt-font-bold"><?php echo $month_l['pc_limit']-$month_l['consumed']; ?></td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--end:: Earnings-->
                                </div>

                                <div class="col-xl-6">
                                    <?php
                                        $anouncement =  API."?mode=togetlatestannouncement";
                                        $anouncement_list = callAPI($anouncement);
                                        $anouncement_list = json_decode($anouncement_list,1);
                                    ?>

                                    <!--begin:: Widgets/Announcements 2-->
                                    <div class="kt-portlet kt-bg-primary kt-portlet--skin-solid kt-portlet--height-fluid">
                                        <div class="kt-portlet__head kt-portlet__head--noborder">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                    Announcements
                                                </h3>
                                            </div>
                                            <div class="kt-portlet__head-toolbar">
                                                <a href="#" class="btn btn-icon" data-toggle="dropdown" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                    <i class="flaticon-more-1 kt-font-light"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">
                                                    <ul class="kt-nav">
                                                        <li class="kt-nav__section kt-nav__section--first">
                                                            <span class="kt-nav__section-text">Finance</span>
                                                        </li>
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-graph-1"></i>
                                                                <span class="kt-nav__link-text">Statistics</span>
                                                            </a>
                                                        </li>
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-calendar-4"></i>
                                                                <span class="kt-nav__link-text">Events</span>
                                                            </a>
                                                        </li>
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-layers-1"></i>
                                                                <span class="kt-nav__link-text">Reports</span>
                                                            </a>
                                                        </li>
                                                        <li class="kt-nav__section">
                                                            <span class="kt-nav__section-text">Customers</span>
                                                        </li>
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-calendar-4"></i>
                                                                <span class="kt-nav__link-text">Notifications</span>
                                                            </a>
                                                        </li>
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-file-1"></i>
                                                                <span class="kt-nav__link-text">Files</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="kt-portlet__body">

                                            <!--begin::Widget 7-->
                                            <div class="kt-widget7 kt-widget7--skin-light">
                                                <div class="kt-widget7__desc">
                                                    <?php echo substr($anouncement_list['announcement_message'],0,250);?>

                                                </div>
                                                <div class="kt-widget7__content">
                                                    <div class="kt-widget7__userpic">
                                                        <img src="assets/media/users/100_5.jpg" alt="">
                                                    </div>
                                                    <div class="kt-widget7__info">
                                                        <h3 class="kt-widget7__username">
                                                            <?php echo $_SESSION['users'][$anouncement_list['created_by']]; ?>
                                                        </h3>
                                                        <span class="kt-widget7__time">
                                                            6 hours ago
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="kt-widget7__button">
                                                    <a class="btn btn-brand" href="#" role="button">All Feeds</a>
                                                </div>
                                            </div>

                                            <!--end::Widget 7-->
                                        </div>
                                    </div>

                                    <!--end:: Widgets/Announcements 2-->
                                </div>
                            </div>

                            <!--End::Section-->

                            <!--Begin::Row-->


                            <!--End::Row-->

                            <!--Begin::Row-->
                            <div class="row">


                                <div class="col-xl-6 col-lg-6 order-lg-1 order-xl-1">

                                    <!--Begin::Portlet-->
                                    <div class="kt-portlet kt-portlet--height-fluid">
                                        <div class="kt-portlet__head">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                    Recent Activities
                                                </h3>
                                            </div>
                                            <div class="kt-portlet__head-toolbar">
                                                <div class="dropdown dropdown-inline">
                                                    <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="flaticon-more-1"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-fit dropdown-menu-md">

                                                        <!--begin::Nav-->
                                                        <ul class="kt-nav">
                                                            <li class="kt-nav__head">
                                                                Export Options
                                                                <span data-toggle="kt-tooltip" data-placement="right" title="Click to learn more...">
                                                                    <svg xmlns="http:
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                                            <rect fill="#000000" x="11" y="10" width="2" height="7" rx="1" />
                                                                            <rect fill="#000000" x="11" y="7" width="2" height="2" rx="1" />
                                                                        </g>
                                                                    </svg> </span>
                                                            </li>
                                                            <li class="kt-nav__separator"></li>
                                                            <li class="kt-nav__item">
                                                                <a href="#" class="kt-nav__link">
                                                                    <i class="kt-nav__link-icon flaticon2-drop"></i>
                                                                    <span class="kt-nav__link-text">Activity</span>
                                                                </a>
                                                            </li>
                                                            <li class="kt-nav__item">
                                                                <a href="#" class="kt-nav__link">
                                                                    <i class="kt-nav__link-icon flaticon2-calendar-8"></i>
                                                                    <span class="kt-nav__link-text">FAQ</span>
                                                                </a>
                                                            </li>
                                                            <li class="kt-nav__item">
                                                                <a href="#" class="kt-nav__link">
                                                                    <i class="kt-nav__link-icon flaticon2-telegram-logo"></i>
                                                                    <span class="kt-nav__link-text">Settings</span>
                                                                </a>
                                                            </li>
                                                            <li class="kt-nav__item">
                                                                <a href="#" class="kt-nav__link">
                                                                    <i class="kt-nav__link-icon flaticon2-new-email"></i>
                                                                    <span class="kt-nav__link-text">Support</span>
                                                                    <span class="kt-nav__link-badge">
                                                                        <span class="kt-badge kt-badge--success kt-badge--rounded">5</span>
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="kt-nav__separator"></li>
                                                            <li class="kt-nav__foot">
                                                                <a class="btn btn-clean btn-bold btn-sm" href="#" data-toggle="kt-tooltip" data-placement="right" title="Click to learn more...">Learn more</a>
                                                                <button type="button" class="btn btn-clean btn-bold btn-sm" data-toggle="kt-tooltip" data-placement="right" title="Click to learn more...">Learn more</button>
                                                            </li>
                                                        </ul>

                                                        <!--end::Nav-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="kt-portlet__body">


                                            <div class="kt-timeline-v2">
                                                <div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
                                                    <div class="kt-timeline-v2__item">
                                                        <span class="kt-timeline-v2__item-time">10:00</span>
                                                        <div class="kt-timeline-v2__item-cricle">
                                                            <i class="fa fa-genderless kt-font-danger"></i>
                                                        </div>
                                                        <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                                                            Lorem ipsum dolor sit amit,consectetur eiusmdd tempor<br>
                                                            incididunt ut labore et dolore magna
                                                        </div>
                                                    </div>
                                                    <div class="kt-timeline-v2__item">
                                                        <span class="kt-timeline-v2__item-time">12:45</span>
                                                        <div class="kt-timeline-v2__item-cricle">
                                                            <i class="fa fa-genderless kt-font-success"></i>
                                                        </div>
                                                        <div class="kt-timeline-v2__item-text kt-timeline-v2__item-text--bold">
                                                            AEOL Meeting With
                                                        </div>
                                                        <div class="kt-list-pics kt-list-pics--sm kt-padding-l-20">
                                                            <a href="#"><img src="assets/media/users/100_4.jpg" title="" alt="User Profile"></a>
                                                            <a href="#"><img src="assets/media/users/100_13.jpg" title="" alt="User Profile"></a>
                                                            <a href="#"><img src="assets/media/users/100_11.jpg" title="" alt="User Profile"></a>
                                                            <a href="#"><img src="assets/media/users/100_14.jpg" title="" alt="User Profile"></a>
                                                        </div>
                                                    </div>
                                                    <div class="kt-timeline-v2__item">
                                                        <span class="kt-timeline-v2__item-time">14:00</span>
                                                        <div class="kt-timeline-v2__item-cricle">
                                                            <i class="fa fa-genderless kt-font-brand"></i>
                                                        </div>
                                                        <div class="kt-timeline-v2__item-text kt-padding-top-5">
                                                            Make Deposit <a href="#" class="kt-link kt-link--brand kt-font-bolder">USD 700</a> To ESL.
                                                        </div>
                                                    </div>
                                                    <div class="kt-timeline-v2__item">
                                                        <span class="kt-timeline-v2__item-time">16:00</span>
                                                        <div class="kt-timeline-v2__item-cricle">
                                                            <i class="fa fa-genderless kt-font-warning"></i>
                                                        </div>
                                                        <div class="kt-timeline-v2__item-text kt-padding-top-5">
                                                            Lorem ipsum dolor sit amit,consectetur eiusmdd tempor<br>
                                                            incididunt ut labore et dolore magna elit enim at minim<br>
                                                            veniam quis nostrud
                                                        </div>
                                                    </div>
                                                    <div class="kt-timeline-v2__item">
                                                        <span class="kt-timeline-v2__item-time">17:00</span>
                                                        <div class="kt-timeline-v2__item-cricle">
                                                            <i class="fa fa-genderless kt-font-info"></i>
                                                        </div>
                                                        <div class="kt-timeline-v2__item-text kt-padding-top-5">
                                                            Placed a new order in <a href="#" class="kt-link kt-link--brand kt-font-bolder">SIGNATURE MOBILE</a> marketplace.
                                                        </div>
                                                    </div>
                                                    <div class="kt-timeline-v2__item">
                                                        <span class="kt-timeline-v2__item-time">16:00</span>
                                                        <div class="kt-timeline-v2__item-cricle">
                                                            <i class="fa fa-genderless kt-font-brand"></i>
                                                        </div>
                                                        <div class="kt-timeline-v2__item-text kt-padding-top-5">
                                                            Lorem ipsum dolor sit amit,consectetur eiusmdd tempor<br>
                                                            incididunt ut labore et dolore magna elit enim at minim<br>
                                                            veniam quis nostrud
                                                        </div>
                                                    </div>
                                                    <div class="kt-timeline-v2__item">
                                                        <span class="kt-timeline-v2__item-time">17:00</span>
                                                        <div class="kt-timeline-v2__item-cricle">
                                                            <i class="fa fa-genderless kt-font-danger"></i>
                                                        </div>
                                                        <div class="kt-timeline-v2__item-text kt-padding-top-5">
                                                            Received a new feedback on <a href="#" class="kt-link kt-link--brand kt-font-bolder">FinancePro App</a> product.
                                                        </div>
                                                    </div>
                                                    <div class="kt-timeline-v2__item">
                                                        <span class="kt-timeline-v2__item-time">15:30</span>
                                                        <div class="kt-timeline-v2__item-cricle">
                                                            <i class="fa fa-genderless kt-font-danger"></i>
                                                        </div>
                                                        <div class="kt-timeline-v2__item-text kt-padding-top-5">
                                                            New notification message has been received on <a href="#" class="kt-link kt-link--brand kt-font-bolder">LoopFin Pro</a> product.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    <!--End::Portlet-->
                                </div>
                                <div class="col-xl-6 col-lg-6 order-lg-1 order-xl-1">

                                    <!--Begin::Portlet-->
                                    <div class="kt-portlet kt-portlet--height-fluid">
                                        <div class="kt-portlet__head">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                    Recent Notifications
                                                </h3>
                                            </div>
                                            <div class="kt-portlet__head-toolbar">
                                                <ul class="nav nav-pills nav-pills-sm nav-pills-label nav-pills-bold">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-toggle="tab" href="#kt_widget3_tab1_content" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                            Today
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-toggle="tab" href="#kt_widget3_tab2_content" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                            Month
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="kt-portlet__body">
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="kt_widget3_tab1_content">


                                                    <div class="kt-timeline-v3">
                                                        <div class="kt-timeline-v3__items">
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--info">
                                                                <span class="kt-timeline-v3__item-time">09:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Bob
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--warning">
                                                                <span class="kt-timeline-v3__item-time">10:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Lorem ipsum dolor sit amit
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Sean
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--brand">
                                                                <span class="kt-timeline-v3__item-time">11:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Lorem ipsum dolor sit amit eiusmdd tempor
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By James
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--success">
                                                                <span class="kt-timeline-v3__item-time">12:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Lorem ipsum dolor
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By James
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--danger">
                                                                <span class="kt-timeline-v3__item-time">14:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Lorem ipsum dolor sit amit,consectetur eiusmdd
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Derrick
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--info">
                                                                <span class="kt-timeline-v3__item-time">15:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Lorem ipsum dolor sit amit,consectetur
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Iman
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--brand">
                                                                <span class="kt-timeline-v3__item-time">17:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Lorem ipsum dolor sit consectetur eiusmdd tempor
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Aziko
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                                <div class="tab-pane" id="kt_widget3_tab2_content">


                                                    <div class="kt-timeline-v3">
                                                        <div class="kt-timeline-v3__items">
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--info">
                                                                <span class="kt-timeline-v3__item-time kt-font-success">09:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Contrary to popular belief, Lorem Ipsum is not simply random text.
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Bob
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--warning">
                                                                <span class="kt-timeline-v3__item-time kt-font-warning">10:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        There are many variations of passages of Lorem Ipsum available.
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Sean
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--brand">
                                                                <span class="kt-timeline-v3__item-time kt-font-primary">11:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Contrary to popular belief, Lorem Ipsum is not simply random text.
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By James
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--success">
                                                                <span class="kt-timeline-v3__item-time kt-font-success">12:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        The standard chunk of Lorem Ipsum used since the 1500s is reproduced.
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By James
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--danger">
                                                                <span class="kt-timeline-v3__item-time kt-font-warning">14:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Latin words, combined with a handful of model sentence structures.
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Derrick
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--info">
                                                                <span class="kt-timeline-v3__item-time kt-font-info">15:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Contrary to popular belief, Lorem Ipsum is not simply random text.
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Iman
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="kt-timeline-v3__item kt-timeline-v3__item--brand">
                                                                <span class="kt-timeline-v3__item-time kt-font-danger">17:00</span>
                                                                <div class="kt-timeline-v3__item-desc">
                                                                    <span class="kt-timeline-v3__item-text">
                                                                        Lorem Ipsum is therefore always free from repetition, injected humour.
                                                                    </span><br>
                                                                    <span class="kt-timeline-v3__item-user-name">
                                                                        <a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
                                                                            By Aziko
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--End::Portlet-->
                                </div>
                            </div>

                            <!--End::Row-->

                            <!--End::Dashboard 6-->
                        </div>

                        <!-- end:: Content -->
                    </div>
