<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Page body start -->
<link rel="stylesheet" href="assets/css/custom_view_visitors_report.css">
<script src="assets/bower_components/jquery/js/jquery-3.6.0.min.js"></script>

<div class="page-body report-container">
    <div class="report-header">
        <div>
            <h5><i class="fa fa-file-invoice"></i> Visitor Reports &amp; Analytics</h5>
            <p class="text-muted small mb-0">Query historical visitor check-ins, filter by date range, and export reports.</p>
        </div>
        <div>
            <div class="dropdown">
                <button class="btn-report btn-report-primary dropdown-toggle" type="button" id="dropdownMore" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-download"></i> Export Report
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMore">
                    <li><a class="dropdown-item" href="custom_view_download.php?page=Visitors_Report" target="_blank"><i class="fa fa-file-csv text-success me-2"></i> Download CSV</a></li>
                    <li><a class="dropdown-item" href="custom_view_download.php?page=Visitors_Report" target="_blank"><i class="fa fa-file-excel text-primary me-2"></i> Download Excel</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="card report-card">
        <div class="report-section-title">Query Filters</div>
        <div class="card-body p-0">
            <div class="filter-grid">
                <div>
                    <span class="form-label font-weight-bold">Visit Type</span>
                    <select aria-label="Visit Type" class="form-select" name="visit_type" id="report_visit_type">
                        <option value="all">All Types</option>
                        <option value="visitor">Visitor</option>
                        <option value="delivery">Delivery</option>
                        <option value="service">Service</option>
                        <option value="employee">Employee</option>
                        <option value="doctor">Doctor</option>
                        <option value="vendor_supplier">Vendor / Supplier</option>
                        <option value="others">Others</option>
                    </select>
                </div>
                <div>
                    <span class="form-label font-weight-bold">Date</span>
                    <input aria-label="Report Date" type="date" class="form-control" name="report_date" id="report_date" value="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <button class="btn-report btn-report-primary js-visitors-report" data-target=".VisitorsReportTable" type="button">
                        <i class="fa fa-filter"></i> Apply Query
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Injected Here -->
    <div class="VisitorsReportTable">
        <?php
        $visit_type = 'all';
        $date = date('Y-m-d');
        include_once 'custom_view_visitors_report_table.php';
        ?>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="visitorDetailsModal" tabindex="-1" aria-labelledby="visitorDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #334155); color: #ffffff; border-bottom: none; padding: 20px;">
                <h5 class="modal-title" id="visitorDetailsModalLabel" style="font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-info-circle"></i> Visitor Visit Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="visitorDetailsBody" style="padding: 24px; max-height: 80vh; overflow-y: auto; background: #f8fafc;">
                <!-- Content will be loaded dynamically here -->
            </div>
            <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #edf2f7; padding: 12px 20px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 9px 16px;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Page body end -->
<script src="assets/js/custom_view_visitors_report.js"></script>
