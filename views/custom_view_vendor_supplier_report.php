<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" integrity="sha384-aqK8GioSeku4LA3EdWsiN2vmOKlqIJUGyu3llzFeAhBz6lOqiBKzSaKmEfvTRm2v" crossorigin="anonymous">
<link rel="stylesheet" href="assets/css/custom_view_vendor_supplier_report.css">

<div class="page-body report-container">
    <!-- Header -->
    <div class="report-header">
        <div>
            <h5><i class="fa fa-truck-moving text-primary"></i> Vendor &amp; Supplier Materials Reconciliation Ledger</h5>
            <p class="text-muted small mb-0">Track inward vs. outward materials brought by vendors and suppliers grouped by Vendor / Supplier ID to analyze discrepancies and verify logs.</p>
        </div>

    </div>

    <!-- Filters Panel -->
    <div class="card report-card">
        <div class="report-section-title"><i class="fa fa-filter text-secondary"></i> Filter Options</div>
        <div class="card-body p-0">
            <div class="filter-grid" style="grid-template-columns: 2fr 1fr;">
                <div>
                    <span class="form-label font-weight-bold">Search Driver / Company / Mobile Number</span>
                    <input aria-label="Type driver name, mobile number, company name, or transaction code..." type="text" class="form-control-custom" id="vendor_search_query" placeholder="Type driver name, mobile number, company name, or transaction code...">
                </div>
                <div>
                    <button class="btn-report btn-report-primary js-vendor-query-btn" type="button" style="width: 100%;">
                        <i class="fa fa-search"></i> Apply Query
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Injected Here -->
    <div class="VendorSupplierTable">
        <?php
        $search = '';
        include_once 'views/custom_view_vendor_supplier_report_table.php';
        ?>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="vendorSupplierDetailsModal" tabindex="-1" aria-labelledby="vendorSupplierDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #334155); color: #ffffff; border-bottom: none; padding: 20px;">
                <h5 class="modal-title" id="vendorSupplierDetailsModalLabel" style="font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-balance-scale text-warning"></i> Vendor &amp; Supplier Materials Reconciliation Balance Sheet
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; color: #fff; font-size: 20px;">&times;</button>
            </div>
            <div class="modal-body" id="vendorDetailsBody" style="padding: 24px; max-height: 85vh; overflow-y: auto; background: #f8fafc;">
                <!-- Content loaded dynamically -->
            </div>
            <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #edf2f7; padding: 12px 20px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 9px 16px;">
                    Close Ledger
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/custom_view_vendor_supplier_report.js"></script>
