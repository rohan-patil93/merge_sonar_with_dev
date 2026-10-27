<!-- Page body start -->
<link rel="stylesheet" href="assets/css/custom_view_visitors_report.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php $_SESSION['apply_js']['multiselest'] = '1'; ?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h4 class="">Visitors</h4>
            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" fdprocessedid="l4ttgc">More</button>
                    <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" >
                        <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Bills Details" target="_blank">CSV</a>
                        <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Bills Details" target="_blank">Excel</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php
            $visit_types = array(
                'visitor' => 'Visitor',
                'delivery' => 'Delivery',
                'service' => 'Service',
                'employee' => 'Employee',
                'doctor' => 'Doctor',
                'others' => 'Others'
            );
            $selected_visit_type = $_GET['visit_type'] ?? '';
            ?>
            <div class="row">
                <div class="col-lg-3">
                    <label class="form-label">In Date Time</label>
                    <input type="datetime-local" class="form-control" name="in_datetime" id="in_datetime" value="<?= date('Y-m-d').' 00:00:00' ?>">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Out Date Time</label>
                    <input type="datetime-local" class="form-control" name="out_datetime" id="out_datetime" value="<?= (date('Y-m-d\TH:i')) ?>">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Visit Type</label>
                    <select class="form-control js-example-basic-single" name="visit_type" id="visit_type">
                        <option value="">All</option>
                        <?php foreach ($visit_types as $value => $label) { ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= $selected_visit_type == $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label d-block"> &nbsp; &nbsp; </label>
                    <button class="btn btn-primary js-visitors-report"   data-target=".VisitorsReportTable" type="button" >Apply</button>
                </div>
            </div>
        </div>
    </div>
    <div class="VisitorsReportTable">
        <?php 
        $in_datetime = date('Y-m-d').'\T00:00';
        $out_datetime = date('Y-m-d\TH:i');
        $visit_type = $selected_visit_type;
        
        include('custom_view_visitors_report_table.php'); ?>
    </div>

</div>
<!-- Page body end -->
<script src="assets/js/custom_view_visitors_report.js"></script>
