<?php
$cur_year = date('Y');
$cur_month = date('m');

$selected_date = '2021-06-02';
$vurl = API."?mode=getvendorwisereport&table=employees&month=".$cur_month."&year=".$cur_year;
$hcdetails = callAPI($vurl);
$headcount_data = json_decode($hcdetails,1);
$curyear_monthwise_headcount = $headcount_data['curyear_monthwise_headcount'];
$prevyear_monthwise_headcount = $headcount_data['prevyear_monthwise_headcount'];
$Total_Head_Count = $headcount_data['Total_Head_Count'];
$years = $headcount_data['years'];
$years = implode('|', (array)$years);
$prev_year_monthly_headcount = implode('|', (array)$prevyear_monthwise_headcount);
$cur_year_monthly_headcount =implode('|', (array)$curyear_monthwise_headcount);
$empcategory_headcount = $headcount_data['empcategorywise_headcount'];
$applicantcategory_headcount = $headcount_data['applicantcategorywise_headcount'];
$div_headcount = $headcount_data['divisionwise_headcount'];
$divisionlist_data = $headcount_data['division_list'];

$totaldivision = count((array)$divisionlist_data);
$divisionlist = implode('|', (array)$divisionlist_data);
$loc_headcount = $headcount_data['locationwise_headcount'];
$dept_headcount_permanent = $headcount_data['departmentwise_headcount_permanent'];
$dept_headcount_contract = $headcount_data['departmentwise_headcount_contract'];
$vendor_names = $headcount_data['vendors'];
foreach ($div_headcount as $divdata => $deptlist) {
    $division_names[] = $divisionlist_data[$divdata];
    foreach ($deptlist as $deptname => $deptheadcount) {
        $dept_names[$deptname] = $deptname;
        $dept_headcount_data[$deptname][$divdata]=$deptheadcount;
    }
}
$divisionseries = '';

$wagescount = $headcount_data['count_list'];
$wagescount = implode('|', (array)$wagescount);

$wagesdata = $headcount_data['wages_list'];
$wagesdata = implode('|', (array)$wagesdata);

foreach ($dept_headcount_data as $key => $value) {
    $headcount[] = implode(',', (array)$value);
    $div_deptnames[] = $key;
}
$divheadcount = implode('|', (array)$headcount);
$divdeptnames = implode('|', (array)$div_deptnames);
$totaldivdeptnames =  count((array)$div_deptnames);
?>
<script type="text/javascript">
    function showvendorreport(vendorid){


        jQuery.ajax({
            url: "views/custom_view_vendorcontract_popup.php",
            data: "vendorid="+vendorid,
            type: "GET",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true});
            },
            error:function (){}
        });
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">

                <!--begin::Charts Widget 2-->
                <div class="card card-custom bg-gray-100 card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header h-auto border-0">
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <ul class="nav nav-pills nav-pills-sm nav-dark-75">
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4 active" data-toggle="tab" href="#kt_charts_widget_2_chart_tab_1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-text font-size-sm">Department Wise Jobs</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_charts_widget_2_chart_tab_2" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-text font-size-sm">Vendor Wise Employees</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_charts_widget_2_chart_tab_3" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-text font-size-sm">Vendor Wise Applicants</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_charts_widget_2_chart_tab_4" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-text font-size-sm">Contracts Due</span>
                                    </a>
                                </li>


                            </ul>
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="tab-content mt-5" id="myTabLIist18">
                            <!--begin::Tap pane-->
                            <div class="tab-pane fade show active" id="kt_charts_widget_2_chart_tab_1" role="tabpanel" aria-labelledby="kt_charts_widget_2_chart_tab_1">
                                <!--begin::Chart-->

                                <input type="hidden" id="years" value="<?php echo $years;?>">

                                <input type="hidden" id="cur_year_monthly_headcount" value="<?php echo $cur_year_monthly_headcount;?>">
                                <input type="hidden" id="prev_year_monthly_headcount" value="<?php echo $prev_year_monthly_headcount;?>">

                                <!--end::Chart-->
                                <input type="hidden" id="totaldepartments" value="<?php echo count((array)$dept_headcount_permanent);?>">
                                <?php
                                foreach ($dept_headcount_permanent as $key_dp => $value_dp) {
                                    $dept_name_list = '';
                                    $dept_name_list .= $key_dp."|";
                                    $dept_headcount_list = '';
                                    $dept_headcount_list .= $value_dp."|";
                                }
                                $dept_name_list = substr($dept_name_list, 0, -1);
                                $dept_headcount_list = substr($dept_headcount_list, 0, -1);
                                ?>
                                <input type="hidden" id="dept_name_list" value="<?php echo $dept_name_list;?>">
                                <input type="hidden" id="dept_headcount_list" value="<?php echo $dept_headcount_list;?>">

                                <div id="department_headcount_chart_permanent" class="d-flex justify-content-center"></div>

                                <input type="hidden" id="totaldepartments_2" value="<?php echo count((array)$dept_headcount_contract);?>">
                                <?php
                                foreach ($dept_headcount_contract as $key_dp => $value_dp) {
                                    $dept_name_list2 = '';
                                    $dept_name_list2 .= $key_dp."|";
                                    $dept_headcount_list2 = '';
                                    $dept_headcount_list2 .= $value_dp."|";
                                }
                                $dept_name_list2 = substr($dept_name_list2, 0, -1);
                                $dept_headcount_list2 = substr($dept_headcount_list2, 0, -1);
                                ?>
                                <input type="hidden" id="dept_name_list2" value="<?php echo $dept_name_list2;?>">
                                <input type="hidden" id="dept_headcount_list2" value="<?php echo $dept_headcount_list2;?>">

                                <div id="department_headcount_chart_contract" class="d-flex justify-content-center"></div>
                            </div>
                            <div class="tab-pane fade" id="kt_charts_widget_2_chart_tab_2" role="tabpanel" aria-labelledby="kt_charts_widget_2_chart_tab_2">
                                <!--begin::Chart-->

                                <div id="vendorcategory_headcount_chart" class="d-flex justify-content-center"></div>
                                <input type="hidden" id="totalempcategory" value="<?php echo count((array)$empcategory_headcount);?>">
                                <?php
                                foreach ($empcategory_headcount as $key_ec => $value_ec) {
                                    $empcategory_name_list = '';
                                    $empcategory_name_list .= $key_ec."|";
                                    $empcategory_headcount_list = '';
                                    $empcategory_headcount_list .= $value_ec."|";
                                }
                                $empcategory_name_list = substr($empcategory_name_list, 0, -1);
                                $empcategory_headcount_list = substr($empcategory_headcount_list, 0, -1);
                                ?>
                                <input type="hidden" id="empcategory_name_list" value="<?php echo $empcategory_name_list;?>">
                                <input type="hidden" id="empcategory_headcount_list" value="<?php echo $empcategory_headcount_list;?>">

                                <!--end::Chart-->
                            </div>

                            <div class="tab-pane fade" id="kt_charts_widget_2_chart_tab_3" role="tabpanel" aria-labelledby="kt_charts_widget_2_chart_tab_3">

                                <!--begin::Chart-->
                                <div id="vendorapplicant_headcount_chart" class="d-flex justify-content-center"></div>
                                <input type="hidden" id="totalapplicantcategory" value="<?php echo count((array)$applicantcategory_headcount);?>">
                                <?php
                                foreach ($applicantcategory_headcount as $key_ec => $value_ec) {
                                    $applicantcategory_name_list = '';
                                    $applicantcategory_name_list .= $key_ec."|";
                                    $applicantcategory_headcount_list = '';
                                    $applicantcategory_headcount_list .= $value_ec."|";
                                }
                                $applicantcategory_name_list = substr($applicantcategory_name_list, 0, -1);
                                $applicantcategory_headcount_list = substr($applicantcategory_headcount_list, 0, -1);
                                ?>
                                <input type="hidden" id="applicantcategory_name_list" value="<?php echo $applicantcategory_name_list;?>">
                                <input type="hidden" id="applicantcategory_headcount_list" value="<?php echo $applicantcategory_headcount_list;?>">
                                <!--end::Chart-->
                            </div>

                            <div class="tab-pane fade" id="kt_charts_widget_2_chart_tab_4" role="tabpanel" aria-labelledby="kt_charts_widget_2_chart_tab_4">


                                <input type="hidden" id="countdata" value="<?php echo $wagesdata;?>">

                                <div id="kt_amcharts_vendorwage" style="height: 500px;"></div>

                            </div>

                            <div class="tab-pane fade" id="kt_charts_widget_2_chart_tab_5" role="tabpanel" aria-labelledby="kt_charts_widget_2_chart_tab_5">

                                <input type="hidden" id="totallocations" value="<?php echo count((array)$loc_headcount);?>">
                                <?php
                                foreach ($loc_headcount as $key_lc => $value_lc) {
                                    $loc_name_list = '';
                                    $loc_name_list .= $key_lc."|";
                                    $loc_headcount_list = '';
                                    $loc_headcount_list .= $value_lc."|";
                                }
                                $loc_name_list = substr($loc_name_list, 0, -1);
                                $loc_headcount_list = substr($loc_headcount_list, 0, -1);
                                ?>
                                <input type="hidden" id="loc_name_list" value="<?php echo $loc_name_list;?>">
                                <input type="hidden" id="loc_headcount_list" value="<?php echo $loc_headcount_list;?>">

                                <div id="location_headcount_chart" class="d-flex justify-content-center"></div>

                            </div>
                        </div>
                    </div>

                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 2-->
            </div>

        </div>
    </div>
</div>

<script src="//www.amcharts.com/lib/3/amcharts.js" integrity="sha384-UEqG4MxxojOqYymI4f4JMtQisP2oLC8Qv2T62fFLQWCY4QrfS+WpVBS+wLHSg0Mb" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/serial.js" integrity="sha384-aviC14tId/1xfGWLH+1T6A3as8A9WRxrwqDVuoCDuGdJuCjhWaFHusuRTUdVFgow" crossorigin="anonymous"></script>
<!--end::Page Vendors-->

<script src="assets/js/pages/features/charts/amcharts/charts.js"></script>
<script src="//www.amcharts.com/lib/3/themes/light.js" integrity="sha384-bAy0EqjOA5jCsPNjmqSa8YmefFrUauJx0jKLRdSMBsbVsZOMmIzTmqtnCuwwAsd3" crossorigin="anonymous"></script>

<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"  >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
