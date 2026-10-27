<?php
$option = '';
if ($_GET['option']!="") {
$option = strtoupper($_GET['option']);
} else {
$option = "COM_MAIN";
}

$return=1;
$dynmiclistpage = array('COM_LIST_PURCHASE_INVOICES','COM_LIST_SUPPLIER_BILLS','COM_LIST_SALES_INVOICES');
$inn_check = array('COM_BULK_PROJECT_TIMESHEET','COM_LOGOUT','COM_LIST_NEW_EMPLOYEE','COM_MAIN','COM_INSERT_APPLICATION_USERS');

if(!in_array(ADMIN_ROLE,$_SESSION['role_ids']) && !in_array($option,(array)$inn_check) && $_SESSION['config']['role_permission']==0 && !isset($_SESSION['application_no_permission_pages']['option'][$_GET['option']])){
    $return = checkPremission($_GET['option'], $_GET['table'] ?? '');
}
if($_SESSION['user_type'] == 4){
     $return=1;
}
if($return == 0){
    $disptemp = "views/403.php";
}else{
    foreach($_SESSION['application_pages'] as $pages){
        if($pages['insert_page_type'] == 1){
            $array_insert[] = $pages['insert_page_name'];
        }
        if($pages['list_page_type'] == 1){
            $array_list[] =  $pages['list_page_name'];
        }
        if($pages['edit_page_type'] == 1){
            $array_edit[] = $pages['edit_page_name'];
        }
        if($pages['view_page_type'] == 1){
            $array_view[] = $pages['view_page_name'];
        }
    }
    $invoice_pages = toCreateInvoicePages();
    foreach($invoice_pages as $key_page => $pages){
        $inv_pages[] = $key_page;
        $inv_pages_list[$key_page] = $pages;
    }

    $chosse_list=0;
    if(strpos("data".$option,"COM_REPORT")){
        $disptemp = "views/custom_report.php";
    }elseif(in_array($option, (array)$inv_pages)){
        $display_invoice_page  = $inv_pages_list[$option];
        if($_SESSION['work_module_setup_id'] == 19){
            $disptemp = "views/custom_insert_quotation_vms.php";
        }else{
            $disptemp = "views/".$display_invoice_page['display_page'].".php";
        }
    }elseif(in_array($option, (array)$array_list)){
        $chosse_list=1;
        $disptemp = "views/view_roll_back1.php";
        if(in_array($option, (array)$dynmiclistpage)){
            $disptemp = "views/custom_list_dynamic_mapping_table_invoices.php";
        }
    }elseif(in_array($option, (array)$array_insert)){
        $disptemp = "views/new_from.php";
    }elseif(in_array($option, (array)$array_view)){
        $disptemp = "views/new_view.php";
    }else{
        $name = strtolower(str_replace("COM_", "CUSTOM_",$option));
        $disptemp = "views/".$name.".php";
        $domain_floder = "custom_code/".$_SESSION['domain_name']."/views";
        if($_SESSION['domain_name']!="" && is_dir($domain_floder)){
            $domain_view_file_name = $domain_floder."/".$name.".php";
            if(file_exists($domain_view_file_name)){
                $disptemp = $domain_view_file_name;
            }
        }
    }
    if (!file_exists($disptemp) && $_GET['popup']!='Y'){
        $disptemp = "views/page_error.php";
    }
}
