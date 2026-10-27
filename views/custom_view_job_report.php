<?php

$cur_year = date('Y');
$cur_month = date('m');
$vurl = API."?mode=getjobsreport&table=job_postings&company_id=".$_SESSION['company_id']."&is_active=0";
$jsdetails = callAPI($vurl);
$jobreport = json_decode($jsdetails,1);
$jobstatus = $jobreport['job_status_report'];
$jobstatus_names = implode('|', (array)$jobstatus['statuslist']);
$jobstatus_count = implode('|', (array)$jobstatus['status_countlist']);
$jobdept_names = implode('|', (array)$jobstatus['deptlist']);
$jobdept_count = implode('|', (array)$jobstatus['dept_countlist']);

$jobloc_names = implode('|', (array)$jobstatus['loclist']);
$jobloc_count = implode('|', (array)$jobstatus['loc_countlist']);
$jobhire_names = implode('|', (array)$jobstatus['hirelist']);
$jobhire_count = implode('|', (array)$jobstatus['hire_countlist']);

$jobtitle_names = implode('|', (array)$jobstatus['jobtitlelist']);
$jobtitle_count = implode('|', (array)$jobstatus['jobtitle_countlist']);

$job_postings = $jobreport['job_postings'];
$departments = $jobreport['departments'];
$locations = $jobreport['locations'];
$job_titles = $jobreport['job_titles'];
$emplist = $jobreport['emplist'];
$statuses = $jobreport['statuses'];

?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">

                <!--begin::Charts Widget 2-->
                <div class="card card-custom bg-white-100 card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header h-auto border">
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <h6 class="m-10"> Job Report By </h6>
                            <ul class="nav nav-pills nav-pills-sm nav-dark-75">
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4 active" data-toggle="tab" href="#kt_charts_widget_2_chart_tab_1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-text font-size-sm">Status</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_charts_widget_2_chart_tab_2" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-text font-size-sm">Job Title</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_charts_widget_2_chart_tab_3" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-text font-size-sm">Location</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_charts_widget_2_chart_tab_4" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-text font-size-sm">Department</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_charts_widget_2_chart_tab_5" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-text font-size-sm">Hiring Manager</span>
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

                                <input type="hidden" id="status" value="<?php echo $jobstatus_names;?>">
                                <input type="hidden" id="count" value="<?php echo $jobstatus_count;?>">
                                <!--end::Chart-->
                                <div id="job_fullreport_status" class="d-flex justify-content-center"></div>

                            </div>
                            <div class="tab-pane fade" id="kt_charts_widget_2_chart_tab_2" role="tabpanel" aria-labelledby="kt_charts_widget_2_chart_tab_2">
                                <!--begin::Chart-->
                                <input type="hidden" id="jobtitle" value="<?php echo $jobtitle_names;?>">
                                <input type="hidden" id="jobtitlecount" value="<?php echo $jobtitle_count;?>">
                                <!--end::Chart-->
                                <div id="job_fullreport_jobtitle" class="d-flex justify-content-center"></div>

                                <!--end::Chart-->
                            </div>

                            <div class="tab-pane fade" id="kt_charts_widget_2_chart_tab_3" role="tabpanel" aria-labelledby="kt_charts_widget_2_chart_tab_3">

                                <!--begin::Chart-->

                                <input type="hidden" id="locs" value="<?php echo $jobloc_names;?>">
                                <input type="hidden" id="locscount" value="<?php echo $jobloc_count;?>">
                                <!--end::Chart-->
                                <div id="job_fullreport_locations" class="d-flex justify-content-center"></div>
                                <!--end::Chart-->
                            </div>

                            <div class="tab-pane fade" id="kt_charts_widget_2_chart_tab_4" role="tabpanel" aria-labelledby="kt_charts_widget_2_chart_tab_4">

                                <input type="hidden" id="dept" value="<?php echo $jobdept_names;?>">
                                <input type="hidden" id="deptcount" value="<?php echo $jobdept_count;?>">
                                <!--end::Chart-->
                                <div id="job_fullreport_dept" class="d-flex justify-content-center"></div>

                            </div>

                            <div class="tab-pane fade" id="kt_charts_widget_2_chart_tab_5" role="tabpanel" aria-labelledby="kt_charts_widget_2_chart_tab_5">

                                <input type="hidden" id="hires" value="<?php echo $jobhire_names;?>">
                                <input type="hidden" id="hirescount" value="<?php echo $jobhire_count;?>">
                                <!--end::Chart-->
                                <div id="job_fullreport_hire" class="d-flex justify-content-center"></div>

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

<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"  >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>

    <div class="card card-custom col-lg-12">
        <div class="card-body">

            <!--begin: Datatable -->
            <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">

                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Job Title </th>
                            <th scope="col">Status</th>
                            <th scope="col">Location</th>
                            <th scope="col">Department </th>
                            <th scope="col">Hiring Manager </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $serialno = 0;
                        foreach ($job_postings as $jobpostkey => $jobpostvalue) {
                            $serialno++;
                             ?>
                            <tr>
                                <td> <?php echo $serialno; ?> </td>
                                <td> <?php echo $job_titles[$jobpostvalue['job_code']]; ?> </td>
                                <td> <?php echo $statuses[$jobpostvalue['current_status']]; ?> </td>
                                <td> <?php echo $departments[$jobpostvalue['department_id']]; ?> </td>
                                <td> <?php echo $locations[$jobpostvalue['location_id']]; ?> </td>
                                <td> <?php echo $emplist[$jobpostvalue['recruitment_manager']]; ?> </td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</div>

<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>
<script src="assets/js/pages/myscrpit/vms_job_report_apex.js"></script>

