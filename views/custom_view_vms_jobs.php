<?php
$vurl = API."?mode=getjobsreport&table=job_postings&company_id=".$_SESSION['company_id']."&is_active=0";
$jsdetails = callAPI($vurl);
$jobreport = json_decode($jsdetails,1);
$jobstatus = $jobreport['job_status_report'];
$jobstatus_names = implode('|', (array)$jobstatus['statuslist']);
$jobstatus_count = implode('|', (array)$jobstatus['status_countlist']);
$jobdept_names = implode('|', (array)$jobstatus['deptlist']);
$jobdept_count = implode('|', (array)$jobstatus['dept_countlist']);

?>

<div class="d-flex flex-column-fluid">

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">

                <!--begin::Card-->
                <div class="card card-custom gutter-b">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Job Report By Status</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <a target="_blank" href="index.php?option=COM_VIEW_JOB_REPORT" class="btn btn-primary offset-10">Full Report</a>
                        <!--begin::Chart-->
                        <input type="hidden" id="status" value="<?php echo $jobstatus_names;?>">
                        <input type="hidden" id="count" value="<?php echo $jobstatus_count;?>">
                        <div id="job_report_chart" class="d-flex justify-content-center"></div>
                        <!--end::Chart-->
                    </div>
                </div>
                <!--end::Card-->
            </div>
        </div>

    </div>
    <!--end::Container-->
</div>

<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>
<script src="assets/js/pages/myscrpit/vms_job_report_apex.js"></script>

