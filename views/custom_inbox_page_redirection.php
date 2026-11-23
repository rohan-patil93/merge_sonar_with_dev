<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_GET['option'] = $_POST['option'];
$_GET['Rid'] = $_POST['rid'];
$option = '';
if ($_GET['option']!="") {
$option = strtoupper($_GET['option']);
} else {
$option = "COM_MAIN";
}
$return=1;
$inn_check = array('COM_BULK_PROJECT_TIMESHEET','COM_LOGOUT','COM_LIST_NEW_EMPLOYEE','COM_MAIN','COM_INSERT_APPLICATION_USERS');
if(!in_array(ADMIN_ROLE,$_SESSION['role_ids']) && !in_array($option,$inn_check) && $_SESSION['config']['role_permission']==0 && !isset($_SESSION['application_no_permission_pages']['option'][$_GET['option']])){
    $return = checkPremission($_GET['option']);
}
if($_SESSION['user_type'] == 4){
     $return=1;
}
if($return == 0){
    $disptemp = "403.php";
}else{
    foreach ($_SESSION['application_pages'] as $pages){
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
        $disptemp = "custom_report.php";
    }elseif(in_array($option, (array)$inv_pages)){
        $display_invoice_page  = $inv_pages_list[$option];
        if($_SESSION['work_module_setup_id'] == 19){
            $disptemp = "custom_insert_quotation_vms.php";
        }else{
            $disptemp = $display_invoice_page['display_page'].".php";
        }
    }elseif(in_array($option, (array)$array_list)){
        $chosse_list=1;
    $disptemp = "view_roll_back1.php";
    }elseif(in_array($option, (array)$array_insert)){
        $disptemp = "new_from.php";
    }/*else if($option=='COM_INSERT_EMPLOYEES'){
        $disptemp = "views/new_from.php";
    }*/elseif(in_array($option, (array)$array_view)){
        $disptemp = "new_view.php";
    }else{
        $name = strtolower(str_replace("COM_", "CUSTOM_",$option));
        $disptemp = $name.".php";
    }
    if (!file_exists($disptemp) && $_GET['popup']!='Y'){
        $disptemp = "page_error.php";
    }
    $_SESSION['ajaxpage'] = $disptemp;
    echo '<input type="hidden" id="ajaxrefrashpagename" value="'.$option .'">';
    include_once $disptemp;

}
?>

