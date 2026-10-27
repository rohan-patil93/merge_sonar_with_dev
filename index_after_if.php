<?php 
//echo "Ravi"; exit;
date_default_timezone_set('Asia/Kolkata');
//echo date('Y-m-d H:i:s')."<br>"; exit;
include('includes/session.php');
//print_r($_SESSION); echo "matter"; exit;
include('dbconfig.php');
include('comm.php');
//print_r($_SESSION); exit;
$_SESSION['nav_type'] = "TOP";
if(count($_SESSION['user']) == 0){
    $user = toGetUserDetais();
    $_SESSION['user'] = $user;
}else{
    $user =  $_SESSION['user'];
}
//setcookie("user_email", $user['user_email'], time() + (86400 * 30), "/");   ///set coockies
setcookie("user_email", $user['user_email'], time() + (86400 * 30), "/", "", true, true);
$_SESSION['user_type'] = $user['user_type'];
$_SESSION['user_email'] = $user['user_email'];
$_SESSION['user_mobile'] = $user['user_mobile']; 
$_SESSION['display_name'] = $user['display_name'];
$_SESSION['company_id'] = $user['company_id'];
$_SESSION['admin_type'] = 0;
$user_roles = json_decode($user['role_id'],1);
$_SESSION['role_ids'] = $user_roles;
if(count($_SESSION['config']) == 0){
    $_SESSION['config'] = toGETConfigartion();
}
if(in_array(ADMIN_ROLE,$user_roles)){
	$_SESSION['admin_type'] = 1;
	$_SESSION['config']['role_permission']=1;
}
if($_SESSION['reset_password'] == 0){
    header("location:reset_password.php");
}
if(count($_SESSION['config']) == 0){
    header("location:hrsetup.php");
}
$date_format = str_replace('-',$_SESSION['config']['date_separator'], strtolower($_SESSION['config']['date_format']));

$_SESSION['date_formate'] = str_replace('yyyy', 'Y', str_replace('mm', 'm', str_replace('dd', 'd', $date_format)));
$date_format = str_replace('yyyy', 'yy',$date_format);
//echo $_SESSION['date_formate']; exit;
if(isset($_GET['noti_id'])){
    $not = NotificationRead($_GET['noti_id']);
}
$notificationlist = toGetNotigficationListByUsingUserId($_SESSION['userid']);
$action_log = toGETactionloglastetrecards();
$inboxcount = toGetInboxCountUsingUserId();
if(count($_SESSION['users']) == 0){
    $_SESSION['users'] = toGETUsersList();
}
//print_r($user); exit;
if($user['user_type'] ==4){

    $candidate = candidatedetais($user['user_email']);
    //print_r($candidate); exit;
    $Rid="";
    if($candidate['id']!=''){
        $_GET['Rid']= deocdeId("id:".$candidate['id']);
    }
    if($_GET['option']==''|| $_GET['option']=="COM_LIST_CANDIDATES"){
        $_GET['option'] = 'COM_INSERT_CANDIDATE';
    }
    $check = array('com_logout','COM_INSERT_CANDIDATE','COM_LIST_CANDIDATE_DOCUMENTS','COM_LIST_CANDIDATE_QUERY','COM_LIST_CANDIDATE_OFFER','COM_LIST_JOIN_REQUIRED_DOCUMENTS','COM_INSERT_EMPLOYEE_CONTRACT');
    if(!in_array($_GET['option'], $check)){
        include("includes/controller.php");
        $disptemp = "views/custom_logout.php";
    }else{
        //$_GET['option'] = 'COM_LIST_CANDIDATE_INFO';
        include("includes/controller.php");
    }
    $_SESSION['error_message']  =  toGetErrorMessages();
}else if($user['user_type'] ==3){
   // print_r($user); exit;
    $vendor = vendordetails($user['user_email']);
    //print_r($vendor);exit;
    $_SESSION['vendor_id'] = $vendor['id'];
    if($vendor['id']!=''){
         $_GET['Ridd']= deocdeId("id:".$vendor['id']);
    }else{
       header("location:vendorsetup.php");
    }
    if($_GET['option']==''){
        $_GET['option'] = 'COM_INSERT_VENDOR';
    }    

    $check = array('com_logout','COM_INSERT_VENDOR','COM_LIST_EMPLOYEES','COM_INSERT_VENDORS','COM_LIST_VENDOR_RATE_CARDS','COM_INSERT_VENDOR_RATE_CARDS','COM_LIST_JOB_POSTING_CANDIDATES','COM_LIST_CONTRACT_EMPLOYEES','COM_LIST_INBOX','COM_LIST_JOB_POSTING_CANDIDATE','COM_INSERT_CANDIDATE','COM_INSERT_EMP_CONTRACTS','COM_INSERT_EMPLOYEE_CONTRACT','COM_INSERT_CANDIDATES','COM_LIST_CANDIDATE_DOCUMENTS','COM_LIST_CANDIDATE_QUERY','COM_LIST_CANDIDATE_OFFER','COM_LIST_JOIN_REQUIRED_DOCUMENTS','COM_VIEW_PROJECT_TIMESHEETS');
    if(!in_array($_GET['option'], $check)){
        include("includes/controller.php");
        $disptemp = "views/custom_logout.php";
    }else{
        //$_GET['option'] = 'COM_LIST_CANDIDATE_INFO';
    
        include("includes/controller.php");
    }
    $_SESSION['error_message']  =  toGetErrorMessages();
    if(count($_SESSION['application_pages']) == 0){
        $_SESSION['application_pages'] = toGetApplicationPagesList();
    }
}else{
    //print_r($user);
    //if($_GET['option']=='' &&  $user['role_id'] == '["5"]' ){
      //  $_GET['option'] = 'COM_LIST_ONLY_EMPLOYEE';
    //}  
    if(isset($_POST['user_image']) && $_POST['user_image']=='user_image'){
        if($_FILES['profile_avatar']['name']!=""){
            $image_nname = imageupload('all','profile_avatar','');
            //echo $image_nname;
            $data = toupdateemployee($image_nname);
        }
    }
    if(count($_SESSION['employee']) == 0){
	//echo "Data employee".$_SESSION['userid'] ;
        $_SESSION['employee'] = toGetEmployeeData();
    }
    //print_r($_SESSION['employee']); exit;
    $_SESSION['emp_id'] = $_SESSION['employee']['id'];
    if($_SESSION['confirmed_basic_ino_val'] == 2){
        $_SESSION['confirmed_basic_ino'] = 10;	
    }
    if($_SESSION['emp_id']!="" && $_SESSION['user_type']==1 &&  $_SESSION['confirmed_basic_ino']==1 && $user['confirmed_basic_ino']=="1"){
        $_GET['option'] = "COM_INSERT_ONBOARDING_EMPLOYEES";
        $_SESSION['confirmed_basic_ino'] = 0;
        $_SESSION['confirmed_basic_ino_val'] = 2;
        //toupdateuserconfirmedbasicinfo();
    }
    if(count($_SESSION['application_pages']) == 0){
        $_SESSION['application_pages'] = toGetApplicationPagesList();
    }
    $role_data = toGetRolePermisssionsByUserId($_SESSION['userid']);
    $_SESSION['role'] = $role_data['role'];
    $_SESSION['role_nav']= json_decode($role_data['role']['view_nav'],1);//exit;
    $_SESSION['role_permission'] = $role_data['permission'];
    $_SESSION['role_page_permission'] = $role_data['page_permission'];
    $_SESSION['role_permission_pages'] = $role_data['permission'];
    if(count($_SESSION['application_menu']) == 0){
		$_SESSION['module_setp'] = toGETModuleList();
        $_SESSION['application_menu'] = toGetApplicationMenuList();
        $_SESSION['application_menu_LN'] = toGETNavDetails('LN');
        $_SESSION['application_menu_TN'] = toGETNavDetails('TN');
        $_SESSION['application_menu_TNL'] = toGETNavDetails('TNL');
        $_SESSION['application_menu_TNC'] = toGETNavDetails('TNC');
    }
    
    $saidnav = $_SESSION['application_menu_LN'];
    $topnav =  $_SESSION['application_menu_TN'];
    $toplistnav =   $_SESSION['application_menu_TNL']; 
    $topnavcenter =  $_SESSION['application_menu_TNC'];
	//print_r($saidnav);
    if(count($_SESSION['status']) == 0){
        $_SESSION['status'] = toGETStatuList();
    }
    //if(count($_SESSION['field_secruity']) == 0){
       $_SESSION['field_secruity'] = toGETFieldSecruityList();
    //}
    $_SESSION['check_value'] = "";
    $_SESSION['check_value'] = $_SESSION['check'];
    $_SESSION['check'] ='';
    $alertcheck =  $_SESSION['check_value']!="" ?$_SESSION['check_value'] : 0 ;
    
    if(count($_SESSION['error_message']) == 0){
        $_SESSION['error_message']  =  toGetErrorMessages();
    }
    $error = $_SESSION['error_message'][$_SESSION['check_value']];
    
    if(count($_SESSION['company']) == 0){
        $_SESSION['company'] = toGetCompanyList();
    }
    date_default_timezone_set('Asia/Kolkata');
    if(isset($_GET['noti_id'])){
        $not = NotificationRead($_GET['noti_id']);
    }
    if($_SESSION['emp_id']!=""){
        $clock_data =toGetclockdata();
    }
    include("includes/controller.php");
    
}
//echo date('Y-m-d H:i:s')."<br>";exit;
$_SESSION['apply_js'] = array();
if($_GET['NI']== '759' || $_GET['NI']=='560' || $_GET['NI']=='676' || $_GET['NI']=='566'){
	$_SESSION['apply_js']['calender']=1;
}

$wizardary = array('COM_INSERT_ONBOARDING_EMPLOYEES','COM_INSERT_EMPLOYEES');
if(in_array($_GET['option'],$wizardary) || in_array($_GET['PAGENAME'],$wizardary)){	
	$_SESSION['apply_css']['wizardcss'] = 1;
}
$calendorarray = array('676','563','566');
if(in_array($_GET['NI'],$calendorarray)){	
	$_SESSION['apply_css']['calendercss'] = '1';
}


?>
<!DOCTYPE html>
<html lang="en">
<?php  include("partials/_head.php"); ?>
<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    <!-- [ Pre-loader ] end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <?php //exit; 
			 include("partials/_header.php"); ?>
		<script>
            //var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";
          	  var HOST_URL = "https://demo.hrstaq.com/";
          	  var DateFormate = <?php echo "'".$date_format."'" ?>;
         	   console.log("DateFormate"); console.log(DateFormate);
           	//alert(HOST_URL);
        	</script>
            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <?php 
			$count_nav = count($topnav); 
			//$count_nav = 3;
			 if($count_nav >0){
                        if($_SESSION['nav_type'] == "LEFT" || $count_nav>8){ 
                            include("partials/_side_nav.php");
                         }else{
                           include("partials/_side_top_nav.php");
                        }
			}
                     ?>
                    <div class="pcoded-content ">
					
					
<?php 
if($_SESSION['timesheetalertshow'] != '0' AND in_array('11',$_SESSION['module_list'])){ 
   $_SESSION['timesheetalertshow'] = '0'; 
    $getTimeSheetNotSubmitedDeatils = getTimeSheetNotSubmitedDeatils();
   // print_r($getTimeSheetNotSubmitedDeatils);
    if($getTimeSheetNotSubmitedDeatils['timesheetsmessagecode'] !='0' AND (in_array('3',$_SESSION['role_ids']) || in_array('5',$_SESSION['role_ids']))){
?>
    <div class="alert alert-warning border-warning alert-dismissible mt-2  m-r-20 m-l-20">
       <strong><a href="index.php?option=<?php echo $getTimeSheetNotSubmitedDeatils['option']; ?>" class="f-14"> <?php echo $getTimeSheetNotSubmitedDeatils['mes']; ?></a></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php }  } ?>
					
					
					
                        <!-- [ breadcrumb ] start -->
                        <?php // include("partials/_sub_header.php"); ?>
                        <!-- [ breadcrumb ] end -->
						 <div class="page-header card" style="margin: 15px !important;">
                        <?php  include($disptemp); //echo $count_nav; //include("views/new_hrms_dash_board.php"); ?>   
                        </div>                       
                    </div>
                    <!-- [ style Customizer ] start -->
                    <div id="styleSelector">
                    </div>
                    <!-- [ style Customizer ] end -->
                </div>
            </div>
        </div>
	<?php  include("partials/_footer.php"); 
	?>
    </div>
	
	
<?php if(!isset($_SESSION['expiryalert']) AND EXPIRY_DOCUMENT_DISPLAY=='Y'){  $_SESSION['expiryalert'] = '1';
    include('views/custom_list_expiry_documents.php'); } ?>

	
    <!-- Warning Section Ends -->
<div id="kt_modal_dynamic_popup" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" <?php if($_GET['option']=="COM_LIST_JOB_POSTING_CANDIDATE" || $_GET['option']=="COM_INSERT_JOB_POSTING_CANDIDATES" ){ ?> style="max-width: 1400px !important;" <?php } ?> >
        <div class="modal-content"  id="dynamic_popup1">
            
        </div>
    </div>
</div>
 <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1">
            
        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remotem" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg " >
        <div class="modal-content"  id="popupm">
            
        </div>
    </div>
</div>
<div id="kt_modal_dynamic_popup2" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 1200px !important;">
        <div class="modal-content"  id="dynamic_popup11">
            
        </div>
    </div>
</div>
<div id="kt_modal_dynamic_popupxxl" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl"  style="max-width: 98% !important;" >
        <div class="modal-content"  id="dynamic_popupxxl" style="border-radius: 20px;">
            
        </div>
    </div>
</div>
  <!-- Required Jquery -->
    <script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
    <script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script>  
    <!-- waves js -->
    <script src="assets/pages/waves/js/waves.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <!-- Custom js menu js -->
    <script src="assets/js/pcoded.min.js"></script>
    <?php if($_SESSION['nav_type'] == "LEFT" || $count_nav>8){ ?>
    <script src="assets/js/vertical/vertical-layout.min.js"></script>
    <?php }else{ ?> 
    <script src="assets/js/vertical/horizontal-layout.min.js"></script>
    <?php } ?>
    <script type="text/javascript" src="assets/js/script.min.js"></script>

    <?php if($_SESSION['apply_js']['float']==1){ ?>
    <!-- Float Chart js -->
    <script src="assets/pages/chart/float/jquery.flot.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
    <script src="assets/pages/chart/float/curvedLines.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.tooltip.min.js"></script>
    <!-- Chartlist charts -->
    <script src="assets/bower_components/chartist/js/chartist.js"></script>
    <?php } ?>
    <?php if($_SESSION['apply_js']['amchart']==1){ ?>
    <!-- amchart js -->
    <script src="assets/pages/widget/amchart/amcharts.js"></script>
    <script src="assets/pages/widget/amchart/serial.js"></script>
    <script src="assets/pages/widget/amchart/light.js"></script>
    <script src="assets/pages/widget/amchart/gauge.js"></script>
    <script src="assets/pages/widget/amchart/pie.min.js"></script>
    <script src="assets/pages/widget/amchart/ammap.min.js"></script>
    <script src="assets/pages/widget/amchart/usaLow.js"></script>
    <script type="text/javascript" src="assets/pages/dashboard/custom-dashboard.min.js"></script>
    <?php } ?>
     <!--------------  data table   --------------->
    <!-- modernizr js -->
    <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
    <script src="assets/pages/data-table/js/jszip.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script> 
    <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script>
	 <!--------- Start wizard js -------------------->
	<?php if($_SESSION['apply_js']['wizardjs'] == 1){ ?>
	<!--Forms - Wizard js-->
	<script src="assets/bower_components/jquery.cookie/js/jquery.cookie.js"></script>
	<script src="assets/bower_components/jquery.steps/js/jquery.steps.js"></script>
	<script src="assets/bower_components/jquery-validation/js/jquery.validate.js"></script>
	<script src="assets/pages/forms-wizard-validation/form-wizard.js"></script>
	<!--------- End wizard js -------------------->
	<?php } ?>  
    <!--------- From Fileds -------------------->
    


	<!-- Select 2 js -->
	<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
	<!-- Multiselect js -->
	<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
	<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
	<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
	<!-- Custom js -->
	<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
	<script type="text/javascript" src="assets/pages/advance-elements/moment-with-locales.min.js"></script>
	
     	<!-- Date picker js -->
	<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
	<!-- Date-range picker js -->
	<script type="text/javascript" src="assets/bower_components/bootstrap-daterangepicker/js/daterangepicker.js"></script>
     	<!-- Date-dropper js -->
	<script type="text/javascript" src="assets/bower_components/datedropper/js/datedropper.min.js"></script>
	<!-- Color picker js -->
	<script type="text/javascript" src="assets/bower_components/spectrum/js/spectrum.js"></script>
	<script type="text/javascript" src="assets/bower_components/jscolor/js/jscolor.js"></script>
	<!-- Mini-color js -->
	<script type="text/javascript" src="assets/bower_components/jquery-minicolors/js/jquery.minicolors.min.js"></script>
	<script type="text/javascript" src="assets/pages/advance-elements/custom-picker.js"></script>
	<!-- tinymce js -->
	<script src="assets/pages/wysiwyg-editor/js/tinymce.min.js"></script> 
	<!-- Editor js -->
	<script src="assets/pages/wysiwyg-editor/wysiwyg-editor.js"></script>
	<script src="assets/pages/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="assets/pages/ckeditor/ckeditor-custom.js"></script>
	<!-- Masking js -->
	<script src="assets/pages/form-masking/inputmask.js"></script>
	<script src="assets/pages/form-masking/jquery.inputmask.js"></script>
	<script src="assets/pages/form-masking/autoNumeric.js"></script>
	<script src="assets/pages/form-masking/form-mask.js"></script>
	<!-- Switch component js -->
	<script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>
		<script type="text/javascript" src="assets/pages/advance-elements/swithces.js"></script>
	<!----- Todo Js --->
	<script type="text/javascript" src="assets/pages/todo/todo.js"></script>
	<!-- File Uploading Accordion js -->
	<script type="text/javascript" src="assets/js/dropzone.js"></script>
	<script type="text/javascript" src="assets/js/custom_file_uploading.js"></script>
	<?php if($_SESSION['apply_js']['tag']==1){ ?>
	<!-- Tags js -->
	<script type="text/javascript" src="assets/bower_components/bootstrap-tagsinput/js/bootstrap-tagsinput.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/typeahead.bundle.min.js"></script>
	<?php } ?>
	<?php if($_SESSION['apply_js']['tag']==1){ ?>
	<!-- Star Rating theme js --> 
	<script type="text/javascript" src="assets/bower_components/jquery-bar-rating/js/jquery.barrating.js"></script>
	<script type="text/javascript" src="assets/js/rating.js"></script>
	<?php } ?>
 	<!------- Genral item work ------------------------------>
	 <script> var table_name ='<?php echo $table_name; ?>'; </script>
	  <script type="text/javascript" src="assets/js/custom.js"></script>
	 <!-- scrollbars modernizr js -->
	<script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
	<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
	
	<!-- modal window POPUP  modalEffects js nifty modal window effects -->
	<script src="assets/js/classie.js"></script>
	<script type="text/javascript" src="assets/js/modalEffects.js"></script>
	<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
	<?php if($_SESSION['apply_js']['calender']==1){ ?>
	<!-- calender js -->
	<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
	<script type="text/javascript" src="assets/bower_components/fullcalendar/js/fullcalendar.min.js"></script>
	<script type="text/javascript" src="assets/pages/full-calender/calendar.js"></script>
	<?php } ?>
	<?php //if($_GET['option']=='COM_INSERT_APPLICATION_ROLE') {
		 if($_SESSION['apply_js']['role']==1){  ?> 
		<script src="assets/js/custom_insert_application_role.js"></script>
	<?php  } ?>
	<?php // if($_GET['option']=='COM_INSERT_JOB_POSTINGS') { 
		if($_SESSION['apply_js']['jobposting']==1){  ?> 
 		<script src="assets/js/custom_job_post.js"></script>
	<?php } ?>
	<?php ?>
	
	<script type="text/javascript" src="assets/js/bootstrap-growl.min.js"></script>
	<script type="text/javascript" src="assets/pages/notification/notification.js"></script>
	<?php if($_SESSION['apply_js']['tour']==1){ ?>
		<script type="text/javascript" src="assets/bower_components/intro.js/js/intro.js"></script>
	<?php } ?>
	<?php // if(strtoupper($_GET['option'])=='COM_INSERT_DISPLAY_ATTRIBUTES') { 
		if($_SESSION['apply_js']['custom_insert_display_attributes']==1){ ?>
	<script src="assets/js/custom_insert_display_attributes.js"></script>
	<?php } ?>
	<?php // if(strtoupper($_GET['option'])=="COM_INSERT_LETTERS"){ 
		if($_SESSION['apply_js']['custom_insert_letters']==1){ ?>
	<script src="assets/js/custom_insert_latter.js"></script>
	<?php } ?>
	<?php // if(strtoupper($_GET['option'])=="COM_INSERT_MY_REPORTS" || strtoupper($_GET['option'])=="COM_INSERT_REPORT_ATTRIBUTES_NEW"){ 
		if($_SESSION['apply_js']['custom_insert_my_reports']==1){ ?>
	<script src="assets/js/custom_insert_my_reports.js"></script>
	<?php } ?>
	
  <!---------------- Main Chart ---------------------------> 
	<?php if($_SESSION['apply_js']['knobchart']==1){ ?>
		<!-- Knob Chart js -->
   		<script type="text/javascript" src="assets/pages/chart/knob/knob-custom-chart.js"></script>
	<?php } ?>
	<?php //if($_GET['option']=='' && $pass_module['0']['page'] == 'vendorstaq_admin_dashboarad'){ 
		if($_SESSION['apply_js']['vendorstaq_admin_dashboarad']==1){ ?> 
    		<script type="text/javascript" src="assets/pages/widget/widget-chart.js"></script>
	<?php } ?> 
	<?php //if($_GET['option']=='COM_LIST_EMPLOYEE_LEAVES') { 
		if($_SESSION['apply_js']['employee_leaves']==1){ ?> 
	    <script type="text/javascript" src="assets/pages/widget/leaves_widget.js"></script>
	<?php } ?> 
	<?php //if($_GET['option']=='COM_BULK_PROJECT_TIMESHEET') { 
		if($_SESSION['apply_js']['bulk_project_timesheet']==1){ ?> 
 	    
	    <script type="text/javascript" src="assets/pages/widget/demo_dashboard4.js"></script>
	<?php } ?>
	<?php //if($pass_module['0']['page'] == 'hr_admin_dashboard' || $_GET['option']==''){
		if($_SESSION['apply_js']['hr_admin_dashboard']==1){ ?> 
    		<script type="text/javascript" src="assets/pages/widget/demo_dashboard4.js"></script>
		<script type="text/javascript" src="assets/pages/widget/widget-statistic.js"></script>
	<?php } ?>
	<?php if($_SESSION['apply_js']['wavechart']==1){ ?>
		<script type="text/javascript" src="assets/pages/widget/wave/jquery.wavify.js"></script>
		<script type="text/javascript" src="assets/pages/widget/wave/TweenMax.min.js"></script>
	<?php } ?>
	<?php //if($_GET['option']=='COM_LIST_CANDIDATE_OFFER') {
		if($_SESSION['apply_js']['googlechart']==1){  ?>
		<!-- google chart custom_list_candidate_offer-->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript" src="assets/pages/chart/google/js/custom-google-chart.js"></script><!-- End google chart --->
	<?php } ?>
	<?php //if($_GET['option']=='COM_INSERT_PAYSLIP' || in_array('8',$_SESSION['module_list'])) { 
		if($_SESSION['apply_js']['payslipchart']==1){ 	?>
		<!-- google chart custom_list_candidate_offer-->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript" src="assets/pages/chart/google/js/com_insert_payslip_charts.js"></script>
		<!-- End google chart -->
	<?php } ?>
	<?php if($_SESSION['apply_js']['demo_dashboard4']==1){ ?>
		<script type="text/javascript" src="assets/pages/widget/demo_dashboard4.js"></script>
	<?php } ?>
	<script>
    	$(document).ready(function() {
       		 $('[data-bs-toggle="tooltip"]').tooltip();
   	 });

      $(document).ready(function() {
        $('[data-bs-toggle="popover"]').popover({
            html: true,
            content: function() {
                return $('#primary-popover-content').html();
            }
        });
     });
     $(document).ready(function() {
	var alertcheck = <?php echo $alertcheck; ?>;
	var error =<?php echo json_encode($error); ?>;
	var title = ' Infistaq Glow';
	if(error != '' && error != null && alertcheck != 0){
	if(alertcheck!='3004'){
		tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',error,'');
	}else{
	tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',error,'');
	}
	}
     });

	 
</script>
    
</body>

</html>
