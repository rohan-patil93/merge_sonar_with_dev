<?php
$_SESSION['apply_js']['todo'] = 1;
$_SESSION['apply_js']['multiselest'] = 1;
$transaction_table = "vendors";
$page_title = "Vendors";
$page_option = "COM_INSERT_VENDORS";
$_GET['VTYPE'] = "V";
include_once 'views/custom_insert_vendors_and_vendor_registers.php';
