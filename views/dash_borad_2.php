
<div class="row">
    <?php
                                $dashboard2_url=  API."?mode=dasboaradrequisition1details";
                                $dashboard2_list = callAPI($dashboard2_url);
                                $dashboard2_list = json_decode($dashboard2_list,1);
                            ?>
    <div class="col-xl-3">
        <!--begin: Stats Widget 19-->
        <div class="card card-custom card-stretch gutter-b">
            <!--begin::Body-->
            <div class="card-body my-3">
                <a href="#" class="card-title font-weight-bolder text-dark text-hover-state-dark font-size-h6 mb-4 d-block">Requsition Raised</a>
                <div class="font-weight-bold text-muted font-size-sm">
                <span class="text-dark-75 font-size-h2 font-weight-bolder mr-2">₹ <?php echo round($dashboard2_list['active']/100000,2); ?> </span>Lac</div>
                <div class="progress progress-xs mt-7 bg-success-o-60">
                    <?php $pr1 = ($dashboard2_list['active']/$dashboard2_list['budget'])*100; ?>
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $pr1; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <!--end:: Body-->
        </div>
        <!--end: Stats:Widget 19-->
    </div>
    <div class="col-xl-3">
                                        <!--begin::Stats Widget 23-->
                                        <div class="card card-custom b card-stretch gutter-b">
                                            <!--begin::Body-->
                                            <div class="card-body my-4">
                                                <a href="#" class="card-title font-weight-bolder text-dark  font-size-h6 mb-4 text-hover-state-dark d-block">Requsition Approved</a>
                                                <div class="font-weight-bold text-muted font-size-sm">
                                                <span class="text-dark-75 font-size-h2 font-weight-bolder mr-2">₹ <?php echo round($dashboard2_list['approve']/100000,2); ?></span>Lac</div>
                                                <div class="progress progress-xs mt-7 bg-dark-o-90">
                                                    <?php $pr2 = ($dashboard2_list['approve']/$dashboard2_list['budget'])*100; ?>
                                                    <div class="progress-bar bg-dark" role="progressbar" style="width: <?php echo $pr2; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <!--end::Stats Widget 23-->
                                    </div>
    <div class="col-xl-3">

                                            <!--begin::New Feedbacks-->
                                            <div class="card card-custom  card-stretch gutter-b">
                                                <div class="card-body my-4">
                                                <a href="#" class="card-title font-weight-bolder text-dark font-size-h6 mb-4 text-hover-state-dark d-block">
                                                    Active Requsitions
                                                    <span class="float-right">₹<?php echo customeummarmonyformate($dashboard2_list['active_only']); ?></span>
                                                </a>
                                                <a href="#" class="card-title font-weight-bolder text-dark font-size-h6 mb-4 text-hover-state-dark d-block">
                                                    In Progess Requsitions
                                                    <span class="float-right">₹<?php echo customeummarmonyformate($dashboard2_list['pending']); ?></span>
                                                </a>
                                                <a href="#" class="card-title font-weight-bolder text-dark font-size-h6 mb-4 text-hover-state-dark d-block">
                                                    Approved Requsitions
                                                    <span class="float-right">₹<?php echo customeummarmonyformate($dashboard2_list['approve']); ?></span>
                                                </a>
                                            </div>

                                            </div>

                                            <!--end::New Feedbacks-->
                                        </div>
    <div class="col-xl-3">

                                            <!--begin::New Feedbacks-->
                                            <div class="card card-custom  card-stretch gutter-b">
                                                <div class="card-body my-12">
                                                <a href="#" class="card-title font-weight-bolder text-dark font-size-h6 mb-4 text-hover-state-dark d-block">
                                                    Avalable Budget
                                                    <span class="float-right">₹<?php echo customeummarmonyformate($dashboard2_list['budget']); ?></span>
                                                </a>

                                            </div>

                                            </div>

                                            <!--end::New Feedbacks-->
                                        </div>
    <?php
                                        $anouncement =  API."?mode=togetlatestannouncement";
                                        $anouncement_list = callAPI($anouncement);
                                        $anouncement_list = json_decode($anouncement_list,1);
                                    ?>
    <div class="col-xl-4">
                                        <!--begin::Engage Widget 9-->
                                        <div class="card card-custom card-stretch gutter-b">
                                            <div class="card-body d-flex p-0">
                                                <div class="flex-grow-1 p-20 pb-40 card-rounded flex-grow-1 bgi-no-repeat" style="background-color: #1B283F; background-position: calc(100% + 0.5rem) bottom; background-size: 50% auto; background-image: url(assets/media/svg/humans/custom-10.svg)">
                                                    <h2 class="text-white pb-5 font-weight-bolder">Announcements</h2>
                                                    <p class="text-muted pb-5 font-size-h5"><?php echo substr($anouncement_list['announcement_message'],0,250);?></p>
                                                    <a href="#" class="btn btn-danger font-weight-bold py-2 px-6">Connect</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Engage Widget 9-->
        </div>
        <?php
                                        $monthurl =  API."?mode=dasboaradrequisition1coscenterdetails";
                                        $month_list = callAPI($monthurl);
                                        $month_list = json_decode($month_list,1);
                                    ?>
        <div class="col-xl-4">
                                        <!--begin::Base Table Widget 1-->
                                        <div class="card card-custom card-stretch gutter-b">
                                            <!--begin::Header-->
                                            <div class="card-header border-0 pt-5">
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label font-weight-bolder text-dark">Current Financial Year</span>

                                                </h3>
                                                <div class="card-toolbar">
                                                    <a href="#" class="btn btn-light-primary btn-md py-2 font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Branch</a>
                                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                        <!--begin::Navigation-->
                                                        <ul class="nav nav-pills nav-pills-sm nav-dark-75">
                                                            <?php $k=1; foreach ($month_list as  $month_lis) {?>
                                                                <li class="navi-item m-2">
                                                                    <a href="#kt_tab_pane_1_<?php echo $k; ?>" class="navi-linkpy-2 px-4 btn btn-primary" data-toggle="tab" >
                                                                        <span class="navi-text"> <?php echo $month_lis['cost_center_name']; ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php $k++;
} ?>
                                                        </ul>
                                                        <!--end::Navigation-->
                                                    </div>

                                                </div>
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Body-->
                                            <div class="card-body pt-2 pb-0 mt-n3" style="    max-height: 400px;  overflow: auto;">
                                                <div class="tab-content mt-5" id="myTabTables1">
                                                    <!--begin::Tap pane-->

                                                    <?php $k=1;
                                                    foreach ($month_list as  $month_lis) {?>
                                                    <div class="tab-pane fade <?php if($k==1) { echo " show active";
} ?> " id="kt_tab_pane_1_<?php echo $k; ?>" role="tabpanel" aria-labelledby="kt_tab_pane_1_<?php echo $k; ?>">
                                                        <!--begin::Table-->
                                                        <div class="table-responsive">
                                                            <table class="table table-borderless table-vertical-center">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col" class="p-0 w-50px"></th>
                                                                        <th scope="col" class="p-0 min-w-200px"></th>
                                                                        <th scope="col" class="p-0 min-w-100px"></th>
                                                                        <th scope="col" class="p-0 min-w-40px"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($month_lis['data'] as $month_l) {
                                                                                $pre = ($month_l['total_amount']/$month_l['budget'])*100;
                                                                        ?>
                                                                    <tr>
                                                                        <th scope="col" class="pl-0 py-5">
                                                                            <div class="symbol symbol-50 symbol-light mr-2">
                                                                                <span class="symbol-label">
                                                                                    <img src="assets/media/svg/misc/005-bebo.svg" class="h-50 align-self-center" alt="" />
                                                                                </span>
                                                                            </div>
                                                                        </th>
                                                                        <td class="py-6 pl-0">
                                                                            <a href="#" class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg"><?php echo $month_l['item_name']; ?></a>
                                                                            <span class="text-muted font-weight-bold d-block">Available amount <?php echo $month_l['budget']-$month_l['total_amount']; ?></span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex flex-column w-100 mr-2">
                                                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                                                    <span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo $pre; ?>%</span>
                                                                                    <span class="text-muted font-size-sm font-weight-bold">Progress</span>
                                                                                </div>
                                                                                <div class="progress progress-xs w-100">
                                                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $pre; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                                                </div>
                                                                            </div>
                                                                        </td>

                                                                    </tr>
                                                                    <?php  } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>
                                                    <?php $k++;
} ?>
                                                    <!--end::Tap pane-->
                                                    <!--begin::Tap pane-->


                                                    <!--end::Tap pane-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Base Table Widget 1-->
                                    </div>
</div>
