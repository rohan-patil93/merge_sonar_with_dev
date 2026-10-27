<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
?>
<div class="modal-header">
    <h5 class="modal-title">Dashboard Summary</h5>
    <button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <ul class="nav md-tabs infisuite_tab mb-2">
        <li class="nav-item">
            <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#itemssummary" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Items Summary</a>
        </li>
        <li class="nav-item">
            <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#partysummary" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Dashboard Summary</a>
        </li>
    </ul>
    <div class="tab-content tabs">
        <div class="tab-pane card-body p-0 active" id="itemssummary" role="tabpanel">
            <?php
            $_GET['company_id'] = 1;
            $_GET['data_name'] = "infistaqin_infisuite_2";
            $_GET['user_id'] = 459;
            $_GET['work_module_setup_id'] = $_SESSION['work_module_setup_id'];
            include_once "../apis/custom_items_summary.php"; ?>
        </div>
        <div class="tab-pane card-body p-0 " id="partysummary" role="tabpanel">
            <?php
            $_GET['company_id'] = 1;
            $_GET['data_name'] = "infistaqin_infisuite_demo";
            $_GET['user_id'] = 459;
            $_GET['work_module_setup_id'] = $_SESSION['work_module_setup_id'];
            include_once "../apis/custom_vendor_customer_finance_summary.php"; ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>
