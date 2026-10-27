<?php
$_SESSION['apply_js']['todo'] = 1;
$_SESSION['apply_js']['multiselest'] = 1;
$transaction_table = "vendor_registers";
$page_title = "Vendor Registers";
$page_option = "COM_INSERT_VENDOR_REGISTERS";
$_GET['VTYPE'] = "VR";
$required_document_verifications = ['PAN','GSTIN','BANK'];
include_once 'views/custom_insert_vendors_and_vendor_registers.php';
