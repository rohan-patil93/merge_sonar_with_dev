<?php 
$timeprint = '0';
$maxtimeshow = 40;
$start =0;

date_default_timezone_set('Asia/Kolkata');
include('includes/session.php');
include('dbconfig.php');
include('comm.php');

//echo "-------------------------------------------".DUMPTABLE;
//include('views/custom_logout.php'); exit;
if(($timeprint == '1' AND $maxtimeshow >= '2') || $start ==1 ){
	echo "after include dbconfig - ".date('m/d/Y h:i:s a')."</br>";
}
if(isset($_GET['view_session'])){
    echo "<pre>"; print_r($_SESSION); echo "</pre>";
}
if($_GET['option'] =="COM_LOGOUT" || $_GET['option'] =="com_logout"){
        include("views/custom_logout.php"); exit;
}
if($_GET['check_m'] =="SSS"){
        echo customeummarmonyformateifinecheck('700000.00'); exit;
}
$_SESSION['nav_type'] = "TOP";
//if(!isset($_SESSION['login_details']) || $_SESSION['login_details'] == "" || count((array)$_SESSION['login_details']) == 0){
 
$login_details = togetlogindetails();

   
    //print_r($login_details['callConfig']); print_r($login_details['callConfig2']); 
    //print_r($login_details['application_menu']); 
    //print_r($login_details['application_pages']);
    //echo "</pre>";
  //echo "<pre>"; echo "default_module_id"; print_r($login_details['application_menu']);  echo "</pre>"; exit;
   $_SESSION['login_details'] = $login_details;
   $_SESSION['emp_pay_area'] = $login_details['emp_pay_area'];
   $_SESSION['pay_admin_emp'] = $login_details['pay_admin_emp']; 
   $_SESSION['allroles']  = $login_details['rolepermission']['all_roles']; 
   $_SESSION['user_clock_status'] = 'Y';
   if(count((array)$_SESSION['login_details']['emp_clock_status'])>0){
        $_SESSION['user_clock_status'] = 'Y';
   }
   //echo "<pre>"; print_r($_SESSION); exit;
//}else{
 //  $login_details = $_SESSION['login_details'];
//}

if(count((array)$_SESSION['modulewithrole']) ==0){
    if($timeprint == '1' AND $maxtimeshow >= '3'){
        echo "Call Main function- ".date('m/d/Y h:i:s a')."</br>";
    }
    $modulewithrole =$modulewithroledetails =  array();
    foreach($login_details['rolepermission']['role'] as $mwithrole){
        $modulewithrole[$mwithrole['application_module']][]= $mwithrole['role_short_code'];
        $_SESSION['modulewithroledetails'][$mwithrole['application_module']][]= $mwithrole;
        $_SESSION['module_code'][] = $mwithrole['role_short_code'];
        //$_SESSION['working_role_short_code'] = $mwithrole['role_short_code'];
    }
    $_SESSION['modulewithrole'] = $modulewithrole;  
    $_SESSION['module_setp'] = $login_details['module_setup'];
}
//echo "<pre>"; print_r($_SESSION['modulewithroledetails']); echo "</pre>";//print_r($_SESSION['working_role_short_code']);echo "</pre>";
//print_r($_SESSION['user']);  
if(!isset($_SESSION['user']) AND count((array)$_SESSION['user']) == 0){
//if(count((array)$_SESSION['user']) == 0){
    $user = $login_details['users'];
    $_SESSION['user'] = $user;
    $_SESSION['user_activated'] = $user['user_activated'];
}else{
    $user =  $_SESSION['user'];
}

if(count((array)$_SESSION['application_user_module_roles']) == 0){
    
    $_SESSION['application_user_module_roles'] = $login_details['application_user_module_roles'];
}
if($timeprint == '1' AND $maxtimeshow >= '3'){
	echo "Get user - ".date('m/d/Y h:i:s a')."</br>";
}
//setcookie("user_email", $user['user_email'], time() + (86400 * 30), "/");   ///set coockies
//setcookie("cookie_option", $_GET['option'], time() + (86400 * 30), "/");   ///set coockies
//setcookie("work_module_setup_id", $_SESSION['work_module_setup_id'], time() + (86400 * 30), "/");   ///set coockies
//setcookie("working_role_short_code", $_SESSION['working_role_short_code'], time() + (86400 * 30), "/");   ///set coockies
$_SESSION['user_type'] = $user['user_type'];
$_SESSION['user_email'] = $user['user_email'];
$_SESSION['user_mobile'] = $user['user_mobile']; 
$_SESSION['display_name'] = $user['display_name'];
$_SESSION['company_id'] = $user['company_id'];
$_SESSION['location_id'] = $user['location_id']; 
$_SESSION['admin_type'] = 0;
$user_roles = json_decode($user['role_id'],1);
$_SESSION['role_ids'] = $user_roles;

if(count((array)$_SESSION['config']) == 0){
    $_SESSION['config'] = $login_details['config'];
}
if($timeprint == '1' AND $maxtimeshow >= '4'){
	echo "config - ".date('m/d/Y h:i:s a')."</br>";
}
if(in_array(ADMIN_ROLES,(array)$user_roles)){
	$_SESSION['admin_type'] = 1;
	$_SESSION['config']['role_permission']=1;
}
//rohan added
if(!isset($_SESSION['work_module_setup_id'])){
    $_SESSION['work_module_setup_id'] = $login_details['default_module_id'];
    //$_SESSION['work_module_setup_id'] = $_SESSION['config']['application_module'];
}
if(!isset($_SESSION['working_role_short_code'])){
      $_SESSION['working_role_short_code'] = $login_details['working_role_short_code'];
}
$_SESSION['permission_check'] = 'N';
foreach($_SESSION['module_setp'] as $module_list12){ 
    if((in_array($module_list12['role_admin_short_code'],(array)$_SESSION['role_ids']) || in_array($module_list12['role_view_short_code'],(array)$_SESSION['role_ids'])) AND $_SESSION['work_module_setup_id'] == $module_list12['module_id']){
        $_SESSION['admin_type'] = 1;
        //$_SESSION['config']['role_permission']=1;
        if(in_array($module_list12['role_view_short_code'],(array)$_SESSION['role_ids'])){
            $_SESSION['permission_check'] = 'Y';
        }
    }
}
//echo "admin_type - ".$_SESSION['admin_type']." permission_check- ".$_SESSION['permission_check'];
//rohan added all admin show all Data
if(count((array)$_SESSION['users']) == 0){
    $_SESSION['users'] = $login_details['allusers']['name'];
    $_SESSION['user_image'] = $login_details['allusers']['image'];
}
$_SESSION['error_message']  = $login_details['error_message'];
if(count((array)$_SESSION['status']) == 0){
    $_SESSION['status'] = $login_details['status'];
}

if($_SESSION['reset_password'] == 0){
    header("location:reset_password.php"); exit;
}
if($_SESSION['login_details']['setup_page_logs']['current_status'] != ST_APPROVED AND isset($_SESSION['login_details']['setup_page_logs']['current_status'])){ /// hrstaq show only
   header("location:setup.php"); exit;
}

/**if($_SESSION['reset_password'] == 0){
    header("location:reset_password.php");
}**/
//echo "<pre>"; print_r($login_details);
// if(!isset($_SESSION['work_module_setup_id'])){
//     $_SESSION['work_module_setup_id'] = $login_details['default_module_id'];
//     //$_SESSION['work_module_setup_id'] = $_SESSION['config']['application_module'];
// }
// if(!isset($_SESSION['working_role_short_code'])){
//       $_SESSION['working_role_short_code'] = $login_details['working_role_short_code'];
// }
// echo 'moduleid'.$_SESSION['work_module_setup_id'];
// echo "<pre>";
// print_r($login_details['application_menu']);echo "</pre>";
// if(count($_SESSION['configsetornot']) == 'N'){ /// hrstaq show only
//    header("location:module_setup.php");
// }
if($_SESSION['configsetornot'] == 'N'){ /// hrstaq show only
    //header("location:infistaq_setup.php");
}
$date_format = str_replace('-',$_SESSION['config']['date_separator'], strtolower($_SESSION['config']['date_format']));
$_SESSION['date_formate'] = str_replace('yyyy', 'Y', str_replace('mm', 'm', str_replace('dd', 'd', $date_format)));
$date_format = str_replace('yyyy', 'yy',$date_format);
$_SESSION['system_date_formate'] = $date_format;
if(isset($_GET['noti_id'])){
    $not = NotificationRead($_GET['noti_id']);
}
if($timeprint == '1' AND $maxtimeshow >= '5'){
echo "Notifition - ".date('m/d/Y h:i:s a')."</br>";
}
//$notificationlist = $login_details['notification'];
//$action_log = $login_details['actionlog'];
//$inboxcount = toGetInboxCountUsingUserId();

if($timeprint == '1' AND $maxtimeshow >= '6'){
	echo "inbox count - ".date('m/d/Y h:i:s a')."</br>";
}


if($user['user_type'] ==4){  //db config get candidate type
     $candidate = candidatedetais($user['user_email']);
    //print_r($candidate); //exit;
    $Rid="";
    if($candidate['id']!=''){
        $_GET['Rid']= deocdeId("id:".$candidate['id']);
        $_REQUEST['Rid'] = deocdeId("id:".$candidate['id']);
    }
    if($_GET['option']==''|| $_GET['option']=="COM_LIST_CANDIDATES"){
        $_GET['option'] = 'COM_INSERT_CANDIDATE';
    } 
    $check = array('com_logout','COM_INSERT_CANDIDATE','COM_LIST_CANDIDATE_DOCUMENTS','COM_LIST_CANDIDATE_QUERY','COM_LIST_CANDIDATE_OFFER','COM_LIST_JOIN_REQUIRED_DOCUMENTS','COM_INSERT_EMPLOYEE_CONTRACT','COM_VIEW_CANDIDATE_DETAILS');
    if(!in_array($_GET['option'], (array)$check) AND $_GET['option'] !=""){
        include("includes/controller.php");
        $disptemp = "views/custom_logout.php";
    }else{
        //$_GET['option'] = 'COM_LIST_CANDIDATE_INFO';
        include("includes/controller.php");
    }
}else if($user['user_type'] ==3){ //db config get Vendor type
    if(count((array)$_SESSION['application_pages']) == 0){
        $_SESSION['application_pages'] = $login_details['application_pages'];
    }
    if($_SESSION['rolepermisionget'] != '1'){
        $_SESSION['rolepermisionget'] = '1';
        $role_data = $login_details['rolepermission'];
        $_SESSION['application_no_permission_pages'] = $role_data['application_no_permission_pages'];
        $_SESSION['role'] = $role_data['role'];
        $view_navary = array();
        foreach($_SESSION['role'] as $viewnav){
            $view_nav = json_decode($viewnav['view_nav'],1);
            foreach($view_nav as $view_nav1){
                $view_navary[$view_nav1] = $view_nav1;
            }
         }
        $_SESSION['role_nav']= $view_navary;//exit;
        $_SESSION['role_permission'] = $role_data['permission'];
        $_SESSION['role_page_permission'] = $role_data['page_permission'];
        $_SESSION['role_permission_pages'] = $role_data['permission'];
    }

   // print_r($user); exit;
    $vendor = vendordetails($user['user_email']);
    if($vendor['id']!=''){
         $_SESSION['vendor_id'] = $vendor['id'];
        $_GET['option'] = 'COM_INSERT_VENDORS';
        $_GET['Ridd']= deocdeId("id:".$vendor['id']);
        $_GET['VTYPE']  = 'V';
    }else{
        $vendor = vendorRegisterdetails($user['user_email']);
        $_SESSION['vendor_id'] = $vendor['id'];
        $_GET['option'] = 'COM_INSERT_VENDOR_REGISTERS';
        $_GET['VTYPE']  = 'VR';
        $_GET['Rid']= deocdeId("id:".$vendor['id']);
        //COM_INSERT_VENDORS
       //header("location:vendorsetup.php");
    }
    

    $return = checkPremission($_GET['option']); 
    if($_GET['option']==''){
        $_GET['option'] = 'COM_INSERT_VENDORS';
        //$_GET['option'] = 'COM_VIEW_DASHBOARD_VENDORS';
    }    

    //$check = array('com_logout','COM_INSERT_VENDOR','COM_LIST_EMPLOYEES','COM_INSERT_VENDORS','COM_LIST_VENDOR_RATE_CARDS','COM_INSERT_VENDOR_RATE_CARDS','COM_LIST_JOB_POSTING_CANDIDATES','COM_LIST_CONTRACT_EMPLOYEES','COM_LIST_INBOX','COM_LIST_JOB_POSTING_CANDIDATE','COM_INSERT_CANDIDATE','COM_INSERT_EMP_CONTRACTS','COM_INSERT_EMPLOYEE_CONTRACT','COM_INSERT_CANDIDATES','COM_LIST_CANDIDATE_DOCUMENTS','COM_LIST_CANDIDATE_QUERY','COM_LIST_CANDIDATE_OFFER','COM_LIST_JOIN_REQUIRED_DOCUMENTS','COM_VIEW_PROJECT_TIMESHEETS','COM_VIEW_DASHBOARD_VENDORS','COM_INSERT_PURCHASE_INVOICES','COM_LIST_TABLE_FILTERS');
    if(!in_array($_GET['option'], (array)$check)){
        include("includes/controller.php");
       // $disptemp = "views/custom_logout.php";
    }else{
        //$_GET['option'] = 'COM_LIST_CANDIDATE_INFO';
        include("includes/controller.php");
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

 if(count((array)$_SESSION['employee']) == 0){  //To Get Employee Details For The session
	   $_SESSION['employee'] =   $login_details['employees'];
    }	
if($timeprint == '1' AND $maxtimeshow >= '7'){
	echo "GEt employe details - ".date('m/d/Y h:i:s a')."</br>";
}
    $_SESSION['emp_id'] = $_SESSION['employee']['id'];
    if($_SESSION['confirmed_basic_ino_val'] == 2){
        $_SESSION['confirmed_basic_ino'] = 10;	
    }

    if($_SESSION['emp_id']!="" && $_SESSION['user_type']==1 &&  $_SESSION['confirmed_basic_ino']==1 && $user['confirmed_basic_ino']=="1"){
        //$_GET['option'] = "COM_INSERT_ONBOARDING_EMPLOYEES";  
        $_SESSION['confirmed_basic_ino'] = 0;
        $_SESSION['confirmed_basic_ino_val'] = 2;
        //toupdateuserconfirmedbasicinfo();
    }
 		
    if(count((array)$_SESSION['application_pages']) == 0){
        $_SESSION['application_pages'] = $login_details['application_pages'];
    }
if($timeprint == '1' AND $maxtimeshow >= '8'){	
	echo "Application Pages - ".date('m/d/Y h:i:s a')."</br>";
}	
if($_SESSION['rolepermisionget'] != '1'){
	$_SESSION['rolepermisionget'] = '1';
 	$role_data = $login_details['rolepermission'];
    $_SESSION['application_no_permission_pages'] = $role_data['application_no_permission_pages'];
	$_SESSION['role'] = $role_data['role'];
    $view_navary = array();
    foreach($_SESSION['role'] as $viewnav){
        $view_nav = json_decode($viewnav['view_nav'],1);
        foreach($view_nav as $view_nav1){
            $view_navary[$view_nav1] = $view_nav1;
        }
     }
    $_SESSION['role_nav']= $view_navary;//exit;
    $_SESSION['role_permission'] = $role_data['permission'];
    $_SESSION['role_page_permission'] = $role_data['page_permission'];
    $_SESSION['role_permission_pages'] = $role_data['permission'];
}
//echo "<pre>"; print_r($_SESSION['application_pages']);echo "<pre>"; 
if($timeprint == '1' AND $maxtimeshow >= '9'){
echo "page Permission - ".date('m/d/Y h:i:s a')."</br>";
}
if(count((array)$_SESSION['application_menu']) == 0 AND !isset($_SESSION['application_menu'])){

		$_SESSION['menu_role_id'] = $login_details['menurole'];
		$_SESSION['module_setp'] = $login_details['module_setup'];
		$_SESSION['module_list'] = $login_details['module_id'];
        $_SESSION['application_menu'] = $login_details['application_menu'];
        $_SESSION['application_menu_LN'] = toGETNavDetails('LN');
        $_SESSION['application_menu_TN'] = toGETNavDetails('TN');
        $_SESSION['application_menu_TNL'] = toGETNavDetails('TNL');
        $_SESSION['application_menu_TNC'] = toGETNavDetails('TNC');
        $_SESSION['application_menu_TNR'] = toGETNavDetails('TNR');
        $_SESSION['application_menu_TNC'] = resequenceMenuList($_SESSION['application_menu_TNC']);
 }
//echo "<pre>";print_r($_SESSION['application_menu_TNC']); echo "</pre>"; exit;
// $resequencemenu = resequenceMenuList($_SESSION['application_menu_TNC']);
//$_SESSION['application_menu_TNC'] = $resequencemenu;
//echo  "<pre>";print_r($resequencemenu); echo "</pre>";
// echo  "<pre>";print_r($_SESSION['application_menu_TNC']); echo "</pre>";
 //echo "<pre>";print_r($resequenceMenuList);echo "</pre>";


//$_SESSION['application_menu_LN'] = toGETNavDetails('LN');
	if($timeprint == '1' AND $maxtimeshow >= '10'){
		echo "application menu - ".date('m/d/Y h:i:s a')."</br>";
	}
    $saidnav = $_SESSION['application_menu_LN'];
    $topnav =  $_SESSION['application_menu_TN'];
    $toplistnav =   $_SESSION['application_menu_TNL']; 
    $topnavcenter =  $_SESSION['application_menu_TNC'];

    //echo "<pre>"; print_r($topnavcenter);// echo "</pre>";
    //print_r($saidnav);print_r($topnav);print_r($toplistnav);
    // echo "-----------"; print_r($_SESSION['role_nav']); echo "</pre>";
    
	if($timeprint == '1' AND $maxtimeshow >= '11'){
		echo "Status - ".date('m/d/Y h:i:s a')."</br>";
	}

   /* if(count($_SESSION['field_secruity']) == 0){
       $_SESSION['field_secruity'] = $login_details['field_secruity'];
    }*/ 
	if($timeprint == '1' AND $maxtimeshow >= '12'){
		echo "security - ".date('m/d/Y h:i:s a')."</br>";
	}
    $_SESSION['check_value'] = "";
    $_SESSION['check_value'] = $_SESSION['check'];
    $_SESSION['check'] ='';
    $alertcheck =  $_SESSION['check_value']!="" ?$_SESSION['check_value'] : 0 ;
   
    if(count((array)$_SESSION['error_message']) == 0){
        $_SESSION['error_message']  = $login_details['error_message']; 
    }
	if($timeprint == '1' AND $maxtimeshow >= '13'){
		echo "Errer - ".date('m/d/Y h:i:s a')."</br>";	
	}	
    $error = $_SESSION['error_message'][$_SESSION['check_value']];
    if(count((array)$_SESSION['company']) == 0){
        $_SESSION['company'] = $login_details['companies']; 
    } 
    if(count((array)$_SESSION['user_company']) == 0){
        $_SESSION['user_company'] = $login_details['user_company']; 
    }
	if($timeprint == '1' AND $maxtimeshow >= '14'){
		echo "Comoany details - ".date('m/d/Y h:i:s a')."</br>";
	}
   	//if(isset($_GET['noti_id'])){ // repeted used
      //  $not = NotificationRead($_GET['noti_id']);
    //} // repeted used
	 
    if($_SESSION['emp_id']!=""){
        $clock_data =toGetclockdata();
    }
	if($timeprint == '1' AND $maxtimeshow >= '15'){
		echo "Clock in out details - ".date('m/d/Y h:i:s a')."</br>";
	}
   include("includes/controller.php"); 
   if($timeprint == '1' AND $maxtimeshow >= '16'){
	   echo "control page include - ".date('m/d/Y h:i:s a')."</br>";
   }
}


//echo date('Y-m-d H:i:s')."<br>";exit;
$_SESSION['apply_js'] = array();


$wizardary = array('COM_INSERT_ONBOARDING_EMPLOYEES','COM_INSERT_EMPLOYEES','COM_INSERT_CANDIDATE','COM_VIEW_AUDITTRAILS','COM_INSERT_VENDOR_REGISTERS','COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS');
if(in_array($_GET['option'],(array)$wizardary) || in_array($_GET['PAGENAME'],(array)$wizardary) || $_SESSION['work_module_setup_id'] =='28'){	
	$_SESSION['apply_css']['wizardcss'] = 1;
}

$calendorarray = array('676','563','566','759','560');
$calendorpagearray = array('COM_INSERT_LEAVE_REQUEST','COM_INSERT_APPROVAL_LEAVE');
if(in_array($_GET['NI'],(array)$calendorarray) || in_array($_GET['option'],(array)$calendorpagearray)){
	$_SESSION['apply_js']['calender']='1';	
	$_SESSION['apply_css']['calendercss'] = '1';
}

$cropcss = array('COM_INSERT_BUILDING_FLOORS','COM_VIEW_OFFICE_MANAGEMENT');
if(in_array($_GET['option'],(array)$cropcss)){ 
    $_SESSION['apply_css']['crop_image'] = '1';
}


/*rohan added*/
$_SESSION['apply_js']['calender']='1';  
$_SESSION['apply_css']['calendercss'] = '1';
/*rohan added*/


	if($_GET['option'] == "" || $_GET['NI']=='555' ||  $pass_module['0']['page'] == 'hr_admin_dashboard' || $pass_module['0']['page'] == 'hr_employee_dashboard' || $_GET['option'] == "COM_INSERT_CANDIDATE" || $_GET['option'] == "COM_VIEW_ESOP_ADMIN_DASHBOARD"){
		$_SESSION['apply_css']['widgetcss'] = '1';
		$_SESSION['apply_css']['radialcss'] = '1';
	}
    if(!isset($_SESSION['admin_dash_list']) && count((array)$_SESSION['admin_dash_list'])<1){
     $_SESSION['admin_dash_list'] = dashviewlisthr();
    }

    insertAuditTrail(); 
    $_SESSION['apply_css']['kanban'] =0;
    if($_GET['option'] == "COM_VIEW_PROJECT_KANBAN"){
    $_SESSION['apply_css']['kanban'] =1;
    }
    if($_GET['option'] == "custom_view_crm_dashboards"){
        $_SESSION['apply_css']['starcss'] =1;
    }
//echo "<pre>"; print_r($_SESSION); echo "</pre>"; exit;
//echo "<pre>";print_r($_SESSION['application_menu_TNC']); echo "</pre>";
    /*if($_GET['DO'] == "111"){
        echo "<pre>";   print_r($_SESSION['application_menu_TNR']);echo "</pre>";
    }*/
if(isset($_SESSION['work_module_setup_id']) && $_SESSION['work_module_setup_id'] == POLL_MODULE_ID){
    $_GET['CAT'] = POLL_CAT_ID;
}

?>
<!DOCTYPE html>
<html lang="en">
<?php  include("partials/_head.php"); 
if($timeprint == '1' AND $maxtimeshow >= '17'){
	echo "CSS Ending - ".date('m/d/Y h:i:s a')."</br>";
}
?>
<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    <!-- [ Pre-loader ] end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
	 <?php  
		if($timeprint == '1' AND $maxtimeshow >= '18'){	echo "before Header - ".date('m/d/Y h:i:s a')."</br>"; }
		include("partials/_header.php"); 
               
		if($timeprint == '1' AND $maxtimeshow >= '18'){	echo "After Header - ".date('m/d/Y h:i:s a')."</br>"; }
	?>
	<script>
             var HOST_URL = "https://demo.hrstaq.com/";
          	  var DateFormate = <?php echo "'".$date_format."'" ?>;
         	  // console.log("DateFormate"); console.log(DateFormate);
           	//alert(HOST_URL);
        	</script>
	<div class="pcoded-main-container">
                <div class="pcoded-wrapper">
			<?php		$count_nav = count((array)$topnav); 
            			//$count_nav = 3;
            			 if($count_nav >0){
                                    if($_SESSION['nav_type'] == "LEFT" || $count_nav>8){ 
                                        include("partials/_side_nav.php");
                                        
                                     }else{
                                       include("partials/_side_top_nav.php");
                                       
                                    }
            			}
                        
            			if($timeprint == '1' AND $maxtimeshow >= '19'){
            					echo "After Side nav - ".date('m/d/Y h:i:s a')."</br>";
            				}
                    ?>
                    <div class="pcoded-content ">
					
<?php   
if($_SESSION['timesheetalertshow'] != '0' AND in_array('11',(array)$_SESSION['module_list'])){   
   $_SESSION['timesheetalertshow'] = '0'; 
   $getTimeSheetNotSubmitedDeatils = getTimeSheetNotSubmitedDeatils();
   // print_r($getTimeSheetNotSubmitedDeatils);
    if($getTimeSheetNotSubmitedDeatils['timesheetsmessagecode'] !='0' AND (in_array('3',(array)$_SESSION['role_ids']) || in_array('5',(array)$_SESSION['role_ids']))){
?>
    <div class="alert alert-warning border-warning alert-dismissible mt-2  m-r-20 m-l-20">
       <strong><a href="index.php?option=<?php echo $getTimeSheetNotSubmitedDeatils['option']; ?>" class="f-14"> <?php echo $getTimeSheetNotSubmitedDeatils['mes']; ?></a></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php }  } ?>
		
                        <!-- [ breadcrumb ] start -->

                        <!-- [ breadcrumb ] end -->


						 <div class="page-header card" style="margin: 15px !important;">


     <?php // include("partials/_sub_header.php"); 
if($_SESSION['user_activated'] == 'N' AND $_GET['option'] != "com_logout"){
     $disptemp = 'views/custom_insert_application_users.php';
}else{
    //getApplicationEmptyTransactions($_GET['option']);
}
                        ?>
                        <?php 
                            
                        //echo "&&&&&&&&&".$disptemp;
                        include($disptemp); //echo $count_nav; //include("views/new_hrms_dash_board.php");
                         ?>   
                        <?php 
						if($timeprint == '1' AND $maxtimeshow >= '20'){
							echo "Page End - ".date('m/d/Y h:i:s a')."</br>";
						} 
						?>
						</div>                       
                    </div>
                    <!-- [ style Customizer ] start -->
                    <div id="styleSelector">
                    </div>
                    <!-- [ style Customizer ] end -->
                </div>
            </div>
        </div>
	<?php include("partials/_footer.php"); 
	?>
    </div>
    <?php 
        if(!isset($_SESSION['expiryalert']) AND EXPIRY_DOCUMENT_DISPLAY=='Y'){  
            $_SESSION['expiryalert'] = '1';
            include('views/custom_list_expiry_documents.php'); 
        }
    ?>
    <!-- Warning Section Ends -->
<div id="kt_modal_dynamic_popup" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" <?php if($_GET['option']=="COM_LIST_JOB_POSTING_CANDIDATE" || $_GET['option']=="COM_INSERT_JOB_POSTING_CANDIDATES" ){ ?> style="max-width: 1200px !important;" <?php } ?> >
        <div class="modal-content"  id="dynamic_popup1" style="border:none;">
            
        </div>
    </div>
</div>
 <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg " >
        <div class="modal-content"  id="popup1" style="border:none;">
            
        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remotem" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg " >
        <div class="modal-content"  id="popupm" style="border:none;">
            
        </div>
    </div>
</div>
<div id="kt_modal_dynamic_popup2" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 1200px !important;">
        <div class="modal-content"  id="dynamic_popup11" style="border:none;">
            
        </div>
    </div>
</div>
<div id="kt_modal_dynamic_popupxxl" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl"  style="max-width: 98% !important;" >
        <div class="modal-content"  id="dynamic_popupxxl" style="border-radius: 20px;" style="border:none;">
            
        </div>
    </div>
</div>

<div id="kt_modal_KTDatatable_remotesq" class="modal fade" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-lg " >
        <div class="modal-content"  id="popupsq" style="border:none;">            

        </div>
    </div>
</div>

<input type="hidden" id="work_module_setup_id" value="<?php echo $_SESSION['work_module_setup_id']; ?>">
<input type="hidden" id="error_message_json" value='<?php echo json_encode($_SESSION['error_message'], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS); ?>'>
<input type="hidden" id="session_config_json" value='<?php echo json_encode($_SESSION['config'], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS); ?>'>
<input type="hidden" id="hide_config_date_format" value="<?php echo $date_format; ?>">


 <script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
    <script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script> 
    <script src="assets/pages/waves/js/waves.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <?php if($_SESSION['work_module_setup_id'] == 29){?>
     <!-- jquery CRM js -->
    <script type="text/javascript" src="assets/js/lead_script.js"></script>
    <!-- jquery CRM js -->
<?php } ?>
     <?php if($_SESSION['apply_js']['kanban'] == "1"){ ?>
        <script type="text/javascript" src="assets/js/kanban_script.js"></script>
    <?php } ?>
    <?php if($_SESSION['apply_js']['headcount'] == "1"){ ?>
     <!-- modernizr js -->
      <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr-custom.js"></script>
    <!-- <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script> -->
    
    <!-- peity Chart js -->
    <script src="assets//bower_components/peity/js/jquery.peity.js"></script>
    <script type="text/javascript" src="assets/pages/chart/peity/peity-custom-chart.js"></script>
    <!-- Custom js -->
    <?php } ?>
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
    <script type="text/javascript" src="assets/pages/widget/excanvas.js "></script>
    <!-- Float Chart js -->
    <script src="assets/pages/chart/float/jquery.flot.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
    <script src="assets/pages/chart/float/curvedLines.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.tooltip.min.js"></script>
   
    <!-- Chartlist charts -->
    <script src="assets/bower_components/chartist/js/chartist.js"></script>
	 <!-- Chart js Rohan Added Leave -->
    <script type="text/javascript" src="assets/bower_components/chart.js/js/Chart.js"></script>
	
    <?php } ?>
    <?php if($_SESSION['apply_js']['new_hr_admin']==1){ ?>
        <script src="assets/bower_components/chartist/js/chartist.js"></script>
<script src="/assets/pages/chart/chartlist/js/chartist-plugin-threshold.js"></script>
<script type="text/javascript" src="assets/pages/chart/chartlist/chartlist-custom.js"></script>
<script src="assets/bower_components/d3/js/d3.js"></script>
<script src="assets/bower_components/nvd3/js/nv.d3.js"></script>
<script src="assets/pages/chart/nv-chart/js/stream_layers.js"></script>
<script type="text/javascript" src="assets/pages/chart/nvd3/chart-nvd3.js"></script>
<script type="text/javascript" src="assets/pages/chart/float/float-chart-custom.js"></script>
<script src="assets/pages/chart/float/jquery.flot.js"></script>
<script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
<script src="assets/pages/chart/float/jquery.flot.pie.js"></script>
    <?php } ?>
      <?php if($_SESSION['apply_js']['new_hr_payroll']==1){ ?>
        <script type="text/javascript" src="assets/pages/chart/float/float-chart-custom.js"></script>
<script src="assets/pages/chart/float/jquery.flot.js"></script>
<script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
<script src="assets/pages/chart/float/jquery.flot.pie.js"></script>
       
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
   <?php if($_SESSION['apply_js']['table']==1){ ?>
    <!--------------  data table   --------------->
    <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr-custom.js"></script>
    <!-- <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script> -->


   
   <!-- data-table js check -->
<!-- <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/pages/data-table/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/pages/data-table/extensions/buttons/js/buttons.flash.min.js"></script>
<script src="assets/pages/data-table/extensions/buttons/js/jszip.min.js"></script>
<script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>
<script src="assets/js/vertical/vertical-layout.min.js"></script>
<script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="assets/js/script.js"></script> -->

<!-- data-table js end check -->
    <script src="assets/pages/data-table/js/jszip.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script> 
    <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/pages/data-table/js/pdfmake.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script>
    <?php } ?>
     <!--------- Start wizard js -------------------->
	<?php if($_SESSION['apply_js']['wizardjs'] == 1){ ?>
	<!--Forms - Wizard js-->
	<script src="assets/bower_components/jquery.cookie/js/jquery.cookie.js"></script>
	<script src="assets/bower_components/jquery.steps/js/jquery.steps.js"></script>
	<script src="assets/bower_components/jquery-validation/js/jquery.validate.js"></script>
	<script src="assets/pages/forms-wizard-validation/form-wizard.js"></script>
	<?php } ?>  
    <!--------- End wizard js -------------------->
	<!-- Select 2 js -->
	<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
	<?php if($_SESSION['apply_js']['multiselest'] == 1){ ?>
	<!-- Multiselect js -->
	<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
	<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
	<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
	<!-- Custom js -->
	<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
	<script type="text/javascript" src="assets/pages/advance-elements/moment-with-locales.min.js"></script>
	<?php } ?>  
	<!-- Date picker js -->
    <script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
	<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
	<?php if($_SESSION['apply_js']['colorpicker'] == 1){ ?>	  <!-- COlor Picker important -->	
	<!-- Date-range picker js -->
	<script type="text/javascript" src="assets/bower_components/bootstrap-daterangepicker/js/daterangepicker.js"></script>
<?php } ?>	
     	<!-- Date-dropper js -->
	<script type="text/javascript" src="assets/bower_components/datedropper/js/datedropper.min.js"></script>

	<?php if($_SESSION['apply_js']['colorpicker'] == '1'){ ?>	 <!--- Color Picker ---> 
		<!-- Color picker js -->
		<script type="text/javascript" src="assets/bower_components/spectrum/js/spectrum.js"></script>
		<script type="text/javascript" src="assets/bower_components/jscolor/js/jscolor.js"></script>
		<!-- Mini-color js -->
		<script type="text/javascript" src="assets/bower_components/jquery-minicolors/js/jquery.minicolors.min.js"></script>
		<script type="text/javascript" src="assets/pages/advance-elements/custom-picker.js"></script>
	<?php } ?>
	<?php if($_SESSION['apply_js']['textediter'] == 1){ ?>	  <!--- Text Editors ---> 
		<!-- tinymce js -->
		<script src="assets/pages/wysiwyg-editor/js/tinymce.min.js"></script> 
		<!-- Editor js -->
		<script src="assets/pages/wysiwyg-editor/wysiwyg-editor.js"></script>
		<script src="assets/pages/ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="assets/pages/ckeditor/ckeditor-custom.js"></script>
	<?php } ?>

	<?php if($_SESSION['apply_js']['inputmask'] == 1){ ?>   <!--- inputmask ---> 
	<!-- Masking js -->
	<script src="assets/pages/form-masking/inputmask.js"></script>
	<script src="assets/pages/form-masking/jquery.inputmask.js"></script>
	<script src="assets/pages/form-masking/autoNumeric.js"></script>
	<script src="assets/pages/form-masking/form-mask.js"></script>
	<?php } ?>
    <?php //if($_SESSION['apply_js']['Switch'] == '1'){ ?>
	<!-- Switch component js -->
	<script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>
	<script type="text/javascript" src="assets/bower_components/bootstrap-maxlength/js/bootstrap-maxlength.js"></script>
	<script type="text/javascript" src="assets/pages/advance-elements/swithces.js"></script>
    <?php //} ?>
<?php if($_SESSION['apply_js']['tagsinput'] == 1){ ?> <!--- To strick out the Task ---> 
    <!----- Todo Js --->
 <script type="text/javascript" src="assets/pages/advance-elements/tagsinput.js"></script>
    <?php } ?>

	<?php if($_SESSION['apply_js']['todo'] == 1){ ?> <!--- To strick out the Task ---> 
	<!----- Todo Js --->
	<script type="text/javascript" src="assets/pages/todo/todo.js"></script>
	<?php } ?>
	
	<?php if($_SESSION['apply_js']['dropzone'] == 1){ ?> <!--- File Uploading ---> 
	<!-- File Uploading Accordion js -->
	<script type="text/javascript" src="assets/js/dropzone.js"></script>
	<script type="text/javascript" src="assets/js/custom_file_uploading.js"></script>
	<?php } ?> 
	<?php if($_SESSION['apply_js']['tag']==1){ ?>  <!--console errer occure -->
	<!-- Tags js --->
	<script type="text/javascript" src="assets/bower_components/bootstrap-tagsinput/js/bootstrap-tagsinput.js"></script>
	<script src="/assets/js/moment/typeahead.bundle.min.js"></script>
	<?php } ?>
	<?php if($_SESSION['apply_js']['ratting']==1){ ?>  <!--ratting js  -->
	<!-- Star Rating theme js --> 
	<script type="text/javascript" src="assets/bower_components/jquery-bar-rating/js/jquery.barrating.js"></script>
	<script type="text/javascript" src="assets/js/rating.js"></script>
	<?php } ?>
	
    <?php if($_SESSION['apply_js']['rangeslide']==1){ ?>  <!--range slide js  -->
    <!-- Star Rating theme js --> 
     <script type="text/javascript" src="assets/js/custome_range_slider.js"></script>
    <?php } ?>
    <?php if($_SESSION['apply_js']['crop_image']==1){ ?>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script> -->
       <!--  <script src="assets/bower_components/cropper/js/cropper.js"></script> -->
        <script src="assets/bower_components/cropper/js/custom_cropper.js"></script>
    <?php } ?>
 	<!------- Genral item work ------------------------------>
	 <script> var table_name ='<?php echo $table_name; ?>';  </script>
	  <script type="text/javascript" src="assets/js/custom.js"></script> 
	
<!-- scrollbars modernizr js -->
 <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr-custom.js"></script>
	<!-- <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
	<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script> -->

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
<!-- 	<script type="text/javascript" src="assets/pages/widget/widget-chart.js"></script> -->
  <!---------------- Main Chart ---------------------------> 
	<?php if($_SESSION['apply_js']['knobchart']==1){ ?>
		<!-- Knob Chart js -->
   		<script type="text/javascript" src="assets/pages/chart/knob/knob-custom-chart.js"></script>
	<?php } ?>
	<?php //if($_GET['option']=='' && $pass_module['0']['page'] == 'vendorstaq_admin_dashboarad'){ 
		if($_SESSION['apply_js']['vendorstaq_admin_dashboarad_new']==1){ ?> 
            <script src="assets/pages/widget/amchart/amcharts.js"></script>
            <script src="assets/pages/widget/amchart/serial.js"></script>
            <script src="assets/bower_components/chartist/js/chartist.js"></script>
            <script type="text/javascript" src="assets/pages/widget/vendorstaq_admin_dashboarad.js"></script>
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
            <script src="assets/pages/widget/amchart/amcharts.js"></script>
            <script src="assets/pages/widget/amchart/serial.js"></script>
            <script src="assets/bower_components/chartist/js/chartist.js"></script>
    	   <script type="text/javascript" src="assets/pages/widget/hr_admin_dashboard.js"></script><!-- .js -->
	<?php } ?>
	<?php if($_SESSION['apply_js']['headcount']==1){ ?>
   		 <script type="text/javascript" src="assets/bower_components/chart.js/js/Chart.js"></script>
   		 <script type="text/javascript" src="assets/pages/chart/chartjs/chartjs-custom.js"></script>
	<?php } ?>
	<?php if($_SESSION['apply_js']['wavechart']==1){ ?>
		<script type="text/javascript" src="assets/pages/widget/wave/jquery.wavify.js"></script>
		<script type="text/javascript" src="assets/pages/widget/wave/TweenMax.min.js"></script>
	<?php } ?>
    <?php if($_SESSION['apply_js']['employee_satisfaction']==1){  ?>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/pages/chart/google/js/com_standard_report_employee_satisfaction_charts.js"></script>
    <?php } ?>

    <?php if($_SESSION['apply_js']['vms_employee_dashbard']==1) { ?>
<!-- google chart custom_list_candidate_offer-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="assets/pages/chart/google/js/com_view_dash_board_vendor_employee_charts.js"></script>
<!-- End google chart -->
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
    <?php if($_SESSION['apply_js']['project_dashboard']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/pages/chart/google/js/com_view_project_dash_board.js"></script>
        <!-- End google chart -->
    <?php } ?>
     <?php if($_SESSION['apply_js']['project_dashboard_task']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/pages/chart/google/js/com_view_project_dash_board_task.js"></script>
        <!-- End google chart -->
    <?php } ?>
    
    <?php if($_SESSION['apply_js']['project_dashboard_progress']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/pages/chart/google/js/com_view_project_bar_progress.js"></script>
        <!-- End google chart -->
    <?php } ?>
    <?php if($_SESSION['apply_js']['project_dashboard_workload']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/pages/chart/google/js/com_view_project_bar_workload.js"></script>
        <!-- End google chart -->
    <?php } ?>
    <?php if($_SESSION['apply_js']['project_dashboard_time']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/pages/chart/google/js/com_view_project_bar_time.js"></script>
        <!-- End google chart -->
    <?php } ?>
     <?php if($_SESSION['apply_js']['project_dashboard_cost']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/pages/chart/google/js/com_view_project_bar_cost.js"></script>
        <!-- End google chart -->
    <?php } ?>
    <?php  if($_SESSION['apply_js']['standart_report_wellbeing']==1){   ?>
     <!-- Chartlist charts -->
    <script src="assets/bower_components/chartist/js/chartist.js"></script>
    <script src="assets/pages/chart/chartlist/js/chartist-plugin-threshold.js"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="assets/pages/chart/chartlist/chartlist-custom.js"></script>
    <?php } ?>
    <?php  if($_SESSION['apply_js']['standart_report_hrr']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" 
        src="assets/pages/chart/google/js/custom_view_standared_age_report.js"></script>
        <!-- End google chart -->
    <?php } ?>
    <?php  if($_SESSION['apply_js']['standart_report_hr_gender']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" 
        src="assets/pages/chart/google/js/custom_view_standared_gender_report.js"></script>
        <!-- End google chart -->
    <?php } ?>
    <?php if($_SESSION['apply_js']['report_candidate_resoures']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/pages/chart/google/js/custom_report_candidate_sourcs.js"></script>
        <!-- End google chart -->
    <?php } ?>
	<?php if($_SESSION['apply_js']['demo_dashboard4']==1){ ?>
		<script type="text/javascript" src="assets/pages/widget/demo_dashboard4.js"></script>
	<?php } ?>

    <?php  if($_SESSION['apply_js']['standart_report_bill_vendor']==1){   ?>
        <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" 
        src="assets/pages/chart/google/js/custom_view_report_bill_vendor.js"></script>
         <!-- End google chart -->
    <?php } ?>
<!-- sweet alert js Rohan Added-->
<script type="text/javascript" src="assets/bower_components/sweetalert/js/sweetalert.min.js"></script>
<!-- <script type="text/javascript" src="assets/js/modal.js"></script> -->
<!-- sweet alert  js Rohan Added-->
<?php  if($_SESSION['apply_js']['Payroll_dashboard']==1){   ?>
    <!-- google chart custom_list_candidate_offer-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" 
    src="assets/js/custom_view_payroll_dashboard.js"></script>
    <!-- End google chart -->
<?php } ?>
<?php  if($_SESSION['apply_js']['office_management_transport']==1){   ?>
    <!-- google chart custom_list_candidate_offer-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="assets/pages/chart/google/js//com_view_office_management_transport.js"></script>
    <!-- End google chart -->
<?php } ?>
<?php  if($_SESSION['apply_js']['pms_dashboard']==1){   ?>
    <!-- google chart custom_list_candidate_offer-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="assets/js/custom_view_pms_dashboard_1.js"></script>
    <!-- End google chart -->
<?php } ?>
<?php  if($_SESSION['apply_js']['pms_dashboard2']==1){   ?>
    <!-- google chart custom_list_candidate_offer-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <!-- Float Chart js -->
    <script src="assets/pages/chart/float/jquery.flot.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.pie.js"></script>
    <!-- Custom js -->
<!--     <script type="text/javascript" src="assets/pages/chart/float/float-chart-custom.js"></script>
 -->
    <script type="text/javascript" src="assets/js/custom_view_pms_dashboard_2.js"></script>
    <!-- End google chart -->
<?php } ?>
<?php /** ?>
<!---Rohan Include validation testing---->
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/form-validation/validate.js"></script>
<script type="text/javascript" src="assets/pages/form-validation/form-validation.js"></script>
<!---Rohan Include validation testing---->
<?php **/ ?>

<?php  if($_SESSION['apply_js']['from-validation']==1){   ?>
<!---Rohan Include validation testing---->
<script src="assets/bower_components/underscore/js/underscore-min.js"></script>
<script src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/form-validation/validate.js"></script>
<script type="text/javascript" src="assets/pages/form-validation/form-validation.js"></script>
<!---Rohan Include validation testing---->
    
<?php } ?>
<!---Rohan Include Chart Not Ajx Show PaySlip---->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!---Rohan Include Chart Not Show PaySlip---->
<?php if($_SESSION['apply_js']['asset_allocations_js']==1){ ?>
    <script src="assets/pages/chart/float/jquery.flot.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.pie.js"></script>
    <script type="text/javascript" src="assets/js/custom_list_asset_allocations.js"></script>
<?php } ?>
<?php if($_SESSION['apply_js']['amc_dashboard_js']==1){ ?>
    <script src="assets/pages/chart/float/jquery.flot.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
   <!--  <script src="assets/pages/chart/float/jquery.flot.pie.js"></script> -->
    <script type="text/javascript" src="assets/js/custom_list_amc_allocations.js"></script>
<?php } ?>

<?php if($pass_module[0]['page']=="custom_view_billstack_admin_dashboard"){?>
 <script type="text/javascript" src="assets/js/custom_view_billstack_admin_dashboard.js"></script>
 <?php } ?>
 <?php if($pass_module[0]['page']=="custom_view_reimbursement_dashboard"){?>
 <script type="text/javascript" src="assets/js/custom_view_reimbursement_dashboard.js"></script>
 <?php } ?>
  <?php if($pass_module[0]['page']=="custom_view_rental_admin_dashboard" OR $_GET['option']=="COM_RENTAL_AGING_AND_PROJECTION_REPORTS"){?>
        <script type="text/javascript" src="assets/js/custom_view_rental_admin_dashboard.js"></script>
 <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" 
        src="assets/js/custom_rental_aging_and_projection_reports.js"></script>
         <!-- End google chart -->
 <?php } ?>
 <?php if($pass_module[0]['page']=="custom_view_petty_cash_dashboard" OR $_GET['option']=="COM_RENTAL_AGING_AND_PROJECTION_REPORTS"){ ?>
        <script type="text/javascript" src="assets/js/custom_view_petty_cash_dashboard.js"></script>
 <!-- google chart custom_list_candidate_offer -->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" 
        src="assets/js/custom_petty_cash_aging_and_projection_reports.js"></script>
         <!-- End google chart -->
 <?php } ?>
<?php if($_GET['option']=="COM_VIEW_REIMBURSEMENT_PAYMENT_DASHBOARD"){ ?>
    <script type="text/javascript" src="assets/js/custom_view_reimbursement_payments_dashboard.js"></script>
<?php } ?>
<?php if($_SESSION['apply_js']['recruitingmanager_dashboard']==1){ ?>
<script type="text/javascript" src="assets/pages/chart/google/js/custom_view_recruitingmanager_dashboard.js"></script>
<?php } ?>

<?php if($pass_module[0]['page']=="custom_view_esop_admin_dashboard" || $_GET['option']=="COM_VIEW_ESOP_ADMIN_DASHBOARD" || $_GET['option']=="custom_view_esop_admin_dashboard"){ ?>

     <script src="assets/pages/widget/amchart/amcharts.js"></script>
    <script src="assets/pages/widget/amchart/serial.js"></script>
    <script src="assets/pages/widget/amchart/light.js"></script>
    <script src="assets/pages/widget/amchart/gauge.js"></script>
    <script src="assets/pages/widget/amchart/pie.min.js"></script>
    <script src="assets/pages/widget/amchart/ammap.min.js"></script>
    <script src="assets/pages/widget/amchart/usaLow.js"></script>

    <script type="text/javascript" src="assets/pages/widget/wave/jquery.wavify.js"></script>
    <script type="text/javascript" src="assets/pages/widget/wave/TweenMax.min.js"></script>
        <!-- Chartlist charts -->
    <script src="assets/bower_components/chartist/js/chartist.js"></script>
    <script type="text/javascript" src="assets/js/custom_view_esop_admin_dashboard.js"></script>
<?php } ?>

<?php if($pass_module[0]['page']=="custom_view_pms_admin_dashboard"){ ?>
    <!-- Chart js -->
    <script type="text/javascript" src="assets/bower_components/chart.js/js/Chart.js"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="assets/js/custom_view_pms_admin_dashboard.js"></script>
<?php } ?>

<?php if($_SESSION['apply_js']['PURCHASE_INVOICE_AND_SUPPLIRE_INVOICE_DASHBOARD']==1){ ?>
    <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" 
        src="assets/js/custom_view_purchase_invoice_and_supplire_invoice_dashboard.js"></script>
         <!-- End google chart -->
<?php } ?>

<?php if($_SESSION['apply_js']['CRM_DASHBOARDS']==1){ ?>
    <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/js/custom_view_crm_dashboards.js"></script>
         <!-- End google chart -->
<?php } ?>

<?php if($_SESSION['apply_js']['FILE_TRACKER_DASHBOARDS']==1){ ?>
    <!-- google chart custom_list_candidate_offer-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="assets/js/custom_view_onboarding_admin_dashboards.js"></script>
         <!-- End google chart -->
<?php } ?>

<?php if($_SESSION['apply_js']['SORTABLE']==1){ ?>
<script type="text/javascript" src="assets/bower_components/Sortable/js/Sortable.js"></script>
<script type="text/javascript" src="assets/pages/sortable-custom.js"></script>
<?php } ?>

<?php if($_SESSION['apply_js']['CONTRACT_DASHBOARD']==1){ ?>
<script src="assets/bower_components/raphael/js/raphael.min.js"></script>
<script src="assets/bower_components/morris.js/js/morris.js"></script>
<!-- Custom js -->
<!-- <script src="assets/pages/chart/morris/morris-custom-chart.js"></script> -->
<?php } ?>

<?php if($_SESSION['apply_js']['MANUFACTURING1_DASHBOARD']==1){ ?>

 <!-- Morris Chart js -->
    <script src="assets/bower_components/raphael/js/raphael.min.js"></script>
    <script src="assets/bower_components/morris.js/js/morris.js"></script>
    <!-- Custom js -->
    <script src="assets/pages/chart/morris/morris-custom-chart.js"></script>

    <!-- NVD3 chart -->
    <script src="assets/bower_components/d3/js/d3.js"></script>
    <script src="assets/bower_components/nvd3/js/nv.d3.js"></script>
    <script src="assets/pages/chart/nv-chart/js/stream_layers.js"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="assets/pages/chart/nvd3/chart-nvd3.js"></script>

    <!-- Chartlist charts -->
    <script src="assets/bower_components/chartist/js/chartist.js"></script>
    <script src="assets/pages/chart/chartlist/js/chartist-plugin-threshold.js"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="assets/pages/chart/chartlist/chartlist-custom.js"></script>
<?php } ?>
<?php if($_SESSION['apply_js']['custom_insert_visitor_logs']==1){ ?>
<script src="assets/js/custom_insert_visitor_logs.js?v=<?= time() ?>"></script>
<?php } ?>
    <!-- Session timeout js 
    <script type="text/javascript" src="assets/pages/session-idle-Timeout/session_timeout.min.js"></script>
    <script type="text/javascript" src="assets/pages/session-idle-Timeout/session-idle-Timeout-custom.js"></script>
-->
	<script>
    	$(document).ready(function() {
       		 $('[data-bs-toggle="tooltip"]').tooltip();
            /*  var currenttimelogdash = $("#currenttimelogdash").val();
        if(currenttimelogdash!=""){
            startTimerdash();
        }*/
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

<?php
if($timeprint == '1' AND $maxtimeshow >= '21'){
	echo "Javascript End - ".date('m/d/Y h:i:s a')."</br>";
}
						?>
						

</body>

</html>
