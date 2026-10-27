                                        <!--begin::Base Table Widget 1-->
<?php $lnurl =  API."?mode=dailypettycashdetails";
                                      $att_list = callAPI($lnurl);

                                     $att_list = json_decode($att_list,1);
                                      foreach ($att_list as  $at_list) {
                                          $len_data[] =  round((int)$at_list['pc_limit']/100,2);
                                          $len_data2[] = round((int)$at_list['consumed']/100,2);
                                          $len_data3[] = $at_list['list'];
                                      }
                                      $len_data = implode(",", (array)$len_data);
                                      $len_data2 = implode(",", (array)$len_data2);
                                      $len_data3 = implode(",", (array)$len_data3);

                                 ?>
                                <input type="hidden" id="len_data" value='<?php echo $len_data; ?>'>
                                <input type="hidden" id="len_data2" value='<?php echo $len_data2; ?>'>
                                <input type="hidden" id="len_data3" value='<?php echo $len_data3; ?>'>


<div class="row">
    <div class="col-lg-4">
        <!--begin::Card-->
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Petty Cash Summary</h3>
                </div>
            </div>
            <div class="card-body">
                <!--begin::Chart-->
                <div id="chart_2"></div>
                <!--end::Chart-->
            </div>
        </div>
        <!--end::Card-->
    </div>
    <?php $lnurl1 =  API."?mode=dailypettycashmonth";
                                                              $att_list1 = callAPI($lnurl1);
                                                               $att_list1 = json_decode($att_list1,1);
                                                               $avalprec =  ($att_list1['consumed']/$att_list1 ['pc_limit']);
if($att_list1['pc_limit'] != 0 && $att_list1['pc_limit'] !=""){
 $len_data_cri = round(($att_list1['consumed']/$att_list1['pc_limit']),2).",".round((100-($att_list1['consumed']/$att_list1['pc_limit'])),2);
}
                                                              ?>
    <input type="hidden" id="len_data_cri" value='<?php echo $len_data_cri; ?>'>
    <input type="hidden" id="len_data_cri_labels" value='<?php echo "Available,Consumed" ; ?>' >
    <div class="col-lg-4">
        <!--begin::Card-->
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Petty Cash Balance</h3>
                </div>
            </div>
            <div class="card-body">
                <!--begin::Chart-->
                <div id="chart_11" class="d-flex justify-content-center"></div>
                <!--end::Chart-->
            </div>
        </div>
        <!--end::Card-->
    </div>
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
    $day_item['percentage'][$n] = ($day_total != 0 && $day_total !="")?round(($day_value['amount']/$day_total)*100,2):round($day_value['amount']);
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
    <div class="col-lg-4">
                                        <!--begin::Card-->
                                        <div class="card card-custom gutter-b">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Petty Cash Expenses</h3>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <!--begin::Chart-->
                                                <div id="chart_13"></div>
                                                <!--end::Chart-->
                                            </div>
                                        </div>
                                        <!--end::Card-->
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
                                                    <button type="button" class="btn btn-danger font-weight-bold py-2 px-6">Connect</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Engage Widget 9-->
        </div>
        <?php
                                        $monthurl =  API."?mode=montlypettycashdetails";
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
                                                    <button type="button" class="btn btn-light-primary btn-md py-2 font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Branch</button>
                                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                        <!--begin::Navigation-->
                                                        <ul class="nav nav-pills nav-pills-sm nav-dark-75">
                                                            <?php $k=1; foreach ($month_list as  $month_lis) {?>
                                                                <li class="navi-item m-2">
                                                                    <a href="#kt_tab_pane_1_<?php echo $k; ?>" class="navi-linkpy-2 px-4 btn btn-primary" data-toggle="tab" >
                                                                        <span class="navi-text"> <?php echo $month_lis['name']; ?></span>
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
                                                                                $pre = ($month_l['consumed']/$month_l['pc_limit'])*100;
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

        <div class="col-xl-4">
                                        <!--begin::Base Table Widget 1-->
                                        <div class="card card-custom card-stretch gutter-b">
                                            <!--begin::Header-->
                                            <div class="card-header border-0 pt-5">
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label font-weight-bolder text-dark">Branch-Wise Petty Cash Details</span>

                                                </h3>
                                                <div class="card-toolbar">
                                                    <button type="button" class="btn btn-light-primary btn-md py-2 font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Branch</button>
                                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                        <!--begin::Navigation-->
                                                        <ul class="nav nav-pills nav-pills-sm nav-dark-75">
                                                            <?php foreach ($month_list as  $month_lis) {?>
                                                                <li class="navi-item m-2">
                                                                    <a href="#kt_tab_pane_1_<?php echo $k; ?>" class="navi-linkpy-2 px-4 btn btn-primary" data-toggle="tab" >
                                                                        <span class="navi-text"> <?php echo $month_lis['name']; ?></span>
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
                                            <div class="card-body pt-2 pb-0 mt-n3">
                                                <div class="tab-content mt-5" id="myTabTables1_2">
                                                    <!--begin::Tap pane-->
                                                    <?php $k=1; foreach ($month_list as  $month_lis) {?>
                                                    <div class="tab-pane fade" id="kt_tab_pane_1_1" role="tabpanel" aria-labelledby="kt_tab_pane_1_<?php echo $k; ?>">
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
                                                                                $pre = ($month_l['consumed']/$month_l['pc_limit'])*100;
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
                                                                            <a href="#" class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg"><?php echo $month_l['name']; ?></a>
                                                                            <span class="text-muted font-weight-bold d-block">Available amount <?php echo customeummarmonyformate($month_l['pc_limit']-$month_l['consumed']); ?></span>
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
                                                                        <td class="text-right pr-0">
                                                                            <a href="#" class="btn btn-icon btn-light btn-sm">
                                                                                <span class="svg-icon svg-icon-md svg-icon-success">

                                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                                                            <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                                                                            <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                                                                        </g>
                                                                                    </svg>
                                                                                    <!--end::Svg Icon-->
                                                                                </span>
                                                                            </a>
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
