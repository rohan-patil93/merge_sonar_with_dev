<?php
/**********************************************************************

* Project           : INFIFIN

*

* Program name      : DBCONFIG.PHP

*

* Author            : INFISUITE TECHNOLOGIES LLP (INFISUITE)

*

* Date created      : 2020-JAN-27

*

* Purpose           : THIS FUNCTION IS USED FOR LISTING ALL THE TABLES WHICH ARE USED IN COMMAN NAMES

*

* Revision History  :

*

* Date          Author      Ref    Revision (Date in YYYY-MM-DD format)

* 2020-28-01    INFISUITE   1      INITIAL VERSION

*

**********************************************************************/

###################################################################

######### Data Base Details

###################################################################

ob_start();
//session_start(); 
ini_set("display_errors",0);
define("HOSTNAME",      "localhost");

define("USERNAME",       "root");

define("PASSWORD",       "");

define("PORT",       "3306");

$subdomain = '';
$host = $_SERVER['HTTP_HOST'];
$parts = explode('.', $host);
if (count($parts) > 2) {
  $subdomain = $parts[0];
  if($subdomain == "www"){
      $subdomain = $parts[1];
  }
}
$dbconfig_floder = "custom_code/".$subdomain;
$dbconfig_file = "custom_code/".$subdomain."/dbconfig.php";
if($subdomain!="" && is_dir($dbconfig_floder) && file_exists($dbconfig_file)){
	include($dbconfig_file);
}




define("MAINDB", 'infistaqin_infisuite_common');

define("DUMPTABLE", 'infistaqin_infisuite_super_admin');
 

//$db = $_SESSION['DB_NAME'].".";
//$db = '';



define("CURRENCY_ICON",'<i class="fa fa-rupee"></i>');

define("KRA_KPI_HEADERID",'5');

//define("CAPTCHA_YES_NO",'YES');

define("CAPTCHA_YES_NO",'NO');
 
define("DOCUMENT_PAYSLIP_RELEASE",'20');

define("MOBILE_ANNOUNCEMENT",'Y');

define("CONTRACT_CUSTOMERS","Y");

define("CONTRACT_VENDORS","Y");

###################### Same Invoice Same sku/part number code Settings #################################

define("SAMEPARTNUMBERALLOWED","Y");

define('JWT_SECRET_KEY', 'infisuite_technologies_jwt_secret_key');
define('JWT_ALGORITHM', 'HS256');
###################### Same Invoice Same sku/part number code Settings ##################################

###################### Default Accounts #################################


define("DEFAULTACCOUNTS",array('inventory_asset_account' => '1003809', 'inventory_adjustment_account'=>'50000106', 'cogs_account' => '50000151', 'purchase_expense_account'=>'20003507', 'sales_revenue_account'=>'40001101', 'sales_return_account'=>'10003700', 'ap_account'=>'20003501', 'ar_account'=>'1004001', 'goods_received_not_invoiced_account'=>'20003506', 'goods_sold_not_invoiced_account'=>'1004003','default_cash_acct'=>'10005101', 'default_purchase_discount_account'=>'40001109', 'jv_suspense_acc'=>'200037018', 'tds_clearing_account'=>'20003701', 'tds_payables'=>'20003702', 'tcs_clearing_account'=>'20003701', 'tcs_payables'=>'20003716', 'default_sales_discount_account'=>'50002714', 'default_round_off_account'=>'5002557', 'config_inventory_asset_account'=>'1003809','stock_transfer_account'=>'1003810')); 

###################### Default Accounts #################################


###################### Task Settings ####################################
define("SMS_TASK",array('type'=>'SMS','message'=>'Send Sms'));
define("CALL_TASK",array('type'=>'CALL','message'=>'Please Dial Call'));
define("EMAIL_TASK",array('type'=>'EMAIL','message'=>'Send Email'));
###################### Task Settings ####################################


###################### Expiry Display Settings ####################################
define("EXPIRY_DOCUMENT_DAYS",'5'); 
define("EXPIRY_DOCUMENT_LIST", '{"manpower_contracts":"Y","esgin_budget_approvals":"Y","vendor_rate_cards":"Y","vendor_agreements":"Y","emp_vpn":"Y","emp_visa":"Y","passport":"Y","driving_licence":"Y"}');
define("EXPIRY_DOCUMENT_DISPLAY", "Y");
###################### Expiry Display Settings ####################################

######################  Login Settings ####################################

define("LOGIN_PAGE_ID",'9'); 
define("LOGIN_ARRAY",array('1'=>'corporate_sign_in.php','2'=>'overlay_sign_in.php','3'=>'fancy_sign_in.php','4'=>'creative_sign_in.php','5'=>'vendor_porter_sign_in.php','6'=>'csr_sign_in.php','7'=>'infisuite_sign_in.php','8'=>'infisuite_sign_in_2.php','9'=>'infisuite_sign_in_3.php'  ));
define("LOGIN_INFO",array('logo'=>'infistaq_logo.png','logo2'=>'billsstack.png','heding'=>'Streamline Your Processes And Transform Your Business','text'=>'Welcome to BillsStack, your solution for hassle-free Billing Portal. Simplify your payable operations with our intuitive and efficient system. Say goodbye to manual paperwork and complex calculations—empower your business with automation.</a>'));
 

###################### LOGIN Settings ####################################

define("RENTAL_INVOICE_SPLIT",'Y');
define("JOURNAL_TYPE_SALES",'1');
define("JOURNAL_TYPE_PURCHASE",'2');

define("DEBIT_TRANSACTION_WITH_PARTY",array('sales_invoices','debit_notes','supplier_bill_cancellations','rental_invoices'));
define("CREDIT_TRANSACTION_WITH_PARTY",array('sales_returns','supplier_bills','credit_notes','goods_receipt_notes'));

######################  Buddy Settings ####################################
define("ADMIN_BUDDY",'1');
define("HR_BUDDY",'2'); 
define("FINANACE_BUDDY",'10');
define("LOCATION_BUDDY",'11');
define("PROJECT_BUDDY",'12');
define("LOCATION_RADIUS_CHECK",'Y');

###################### Buddy Settings ####################################

define('EXITE_INTERVIEW_SURVEY_ID','1');
define('EXITE_INTERVIEW_TYPE','EES');


define('GROSS_SALARY_CODE','GS');

define('PF_CODE','PF');

define('ESI_CODE','ESI');

define('PT_CODE','PT');



define('LIVE_LOCATION',1);

define('LIVE_BATTERY',0);

define('LIVE_INTERNET',0);

define('LIVE_FACE_LIVE_NESS',0);


// 0: Yes , 1: No , 2: Role-Based Configuration, 3: Employee-Specific Configuration

define('OA','4');

define('TDS','16');

define('SHIFT_CHECK','0');

define('GROSS_SALARY','1');

define('EMPLOYEER_PF_CONTRIBUTION','13');

define('REIMBURSEMENT_COMPONENT','25');

define('EMPLOYEER_ESI_CONTRIBUTION','14');

define("EXPIRY_DOCUMENT_DAYS", '30');

define("EXPIRY_DOCUMENT_PRIEVES_DAYS", '15');

define("EXPIRY_DOCUMENT_LIST", '{"manpower_contracts":"Y","esgin_budget_approvals":"Y","vendor_rate_cards":"Y","vendor_agreements":"Y","emp_vpn":"Y","emp_visa":"Y","passport":"Y","driving_licence":"Y"}');

define("EXPIRY_DOCUMENT_DISPLAY", "Y");

define('OFF_CHECK','0');


define('EMPLOYEE_INSERT_FORM_BANK_DETAIL_INSERT','Y');

define('WORK_RAIDUS','1');


###################### COMMMAN DB Strat ####################################

define('TBL_USER_REGISTRATIONS', 'user_registrations');

define('TBL_REGISTIRED_GROUPS', 'registered_groups');

define('TBL_USER_REGISTIRED_GROUPS', 'user_registered_groups');

define('TBL_DATABASE_ALLOCATIONS', 'database_allocations');

#######################  COMMMAN DB END ############################

define("TBL_USERS",  'application_users');

define("TBL_STATUS", 'status');

define('TBL_DISPLAYATTRIBUTES_TYPE', 'display_attribute_types');

define('TBL_DISPLAYATTRIBUTES', 'display_attributes');

define('TBL_APPLICATION_PAGES', 'application_pages');

define('TBL_APPLICATION_MENU', 'application_menu');

define('TBL_USER_NOTIFICATON', 'user_notification');

define('TBL_STATUSPROGRES', 'status_progress');

define('TBL_CHART_OF_ACCOUNTS','chart_of_accounts');

define('TBL_STATUSPROGRESACTIONS', 'status_progress_actions');	

define('TBL_ROLE_PERMISSIONS','role_permissions');

define('TBL_ROLES','application_roles');

define('TBL_CONFIG','config');

define('TBL_CUSTOMERS','customers');

define('TBL_COST_CENTERS','cost_centers');

define('TBL_ITEMS','items');
 
define('TBL_VENDOR_ITEMS','vendor_items');

define('TBL_VENDOR_SUMMARY','vendor_summary');

define('TBL_TAX_AUTHORITIES_RATES','tax_authorities_rates');

define('TBL_ITEM_LOCATIONS','item_locations');

define('TBL_LOCATIONS','locations');

define('TBL_APPLICATION_SERIAL_NUMBERS','application_serial_numbers');

define('TBL_DOCUMENT_TYPES', 'document_types');

define('TBL_DOCUMENT_MANAGEMENT', 'document_management');

define('TBL_DOCUMENT_MANAGEMENT_FILES', 'document_management_files');

define('TBL_VENDOR','vendors');

define('TBL_DYNAMIC_MAPPING_TABLES','dynamic_mapping_tables');

define('TBL_ITEM_ORDER_LIST','item_order_list');

define('TBL_CUSTOMER_SUMMARY','customer_summary');
	
define('TBL_REPORT_ATTRIBUTES','report_attributes');

define('TBL_ITEM_CATEGORIES','item_categories');

define('TBL_ACTION_LOG','action_log');

define('TBL_CURRENCY_CONVERTOR','currency_convertor');

define('TBL_USERS_COST_CENTERS','user_cost_centers');

define('TBL_DOA','doa');

define('TBL_EMAIL','email');  

define('TBL_COMPANIES','companies');

define('TBL_EMAIL_LOGS','email_logs');

define('TBL_APPLICATION_TAGS','application_tags'); 

define('TBL_TRANSACTION_APPROVAL_LOGS','transaction_approval_logs');

define('TBL_EMAIL_GROUP_EMAILS','email_group_emails');

define('TBL_NOTIFICATIONS','notifications');

define('TBL_NOTIFICATON_USER','notification_user');

define('TBL_CURRENCIES','currencies');

define('TBL_EMAILGROUPS','email_groups');

define('TBL_REPORT_FILTERS','report_filters');

define('TBL_EMAILGROUPSEMAIL','email_group_emails');

define('TBL_GENRAL_CODE_DETAILS', 'general_code_details');

define('TBL_PURCHASE_REQUISITIONS', 'purchase_requisitions');

define('TBL_PURCHASE_REQUISITIONS_ITEMS', 'pr_items');

define('TBL_PURCHASE_REQUISITIONS_ITEMS_TAXES', 'pr_item_taxes');

define('ADMINROOT','administrator');

define('TBL_ERRORMESSAGE', 'application_messages');

define('TBL_COUNREIES', 'countries');

define('TBL_EMAIL_CONTENT', 'email_content');


define('TBL_BULK_APPROVAL_BASE', 'bulk_approval_base');

define('TBL_TRANSACTION_QUERIES', 'transaction_queries');

define('TBL_TAX_AUTHORITIES_RATES', 'tax_authorities_rates');

define('TBL_TAX_AUTHORITIES_RATES_SPLIT', 'tax_authorities_rates_split');

define('TBL_TAX_TRANSACTION_QUERIES', 'tax_authorities');

define('TBL_APPLICATION_ATTACHMENTS', 'application_attachments');

define('TBL_PROCESS_TASK_SETTINGS','process_task_settings');	

define('TBL_FILTER_TABLES','filter_tables');	

define('OPTION_OTHERS', 'OB');	

define('ALLEMPLOYEES', 'All Employees');

define('TBL_DEPARTMENTS', 'departments');

 define('TBL_WORKFLOW_CONFIGURATION', 'workflow_configuration');

define('TBL_ITEM_ATTRIBUTES','item_attributes');

define('TBL_item_quality_check','item_quality_check');

define('TBL_ITEM_SPECIFICATIONS','item_specifications');

define('TBL_EDIMASTER','edi_master');

define('TBL_EDIREAD','edi_read');

define('TBL_EMP_WORK_EXPERIENCES','emp_work_experiences');

define('TBL_EMP_DEPENDENTS','emp_dependents');

define('TBL_EMP_CONTACTS','emp_contacts');

define('TBL_EMP_EDUCATIONS','emp_educations');

define('TBL_APPLICATION_TEMP','application_temp');

define('TBL_EMPLOYEES_DISPLAY_FIELDS','employees_display_fields');

define('TBL_EMPLOYEES','employees');

define('TBL_USER_SETTINGS', 'user_settings');

define('TBL_ATTENDANCE_SETTINGS', 'attendance_settings');

define('TBL_PROFESSIONAL_TAX', 'professional_tax');

define('TBL_ESI', 'emp_state_insaurance');

define('TBL_PROVIDENT_FUNDS', 'emp_provident_funds');

define('TBL_LABOUR_WELFARE_FUNDS', 'labour_welfare_fund');

define('TBL_EARNINGS', 'earnings');

define('TBL_OTHER_EARNINGS','other_earnings');

define('TBL_HOLIDAYS','holidays');

define('TBL_ESS_PROCESS','ess_process');

define('TBL_LEAVE_TYPE_LEVELS','leave_type_levels');

define('TBL_LEAVE_TYPES','leave_types');

define('TBL_SYSTEM_EARNINGS','system_earnings');

define('TBL_SYSTEM_OTHER_EARNINGS','system_other_earnings');

define("TBL_TRANSACTION_DOCUMENTS",'transaction_documents');

define("TBL_VENDOR_REGISTERS",'vendor_registers');

define("TBL_VENDORS", 'vendors');

define('TBL_SURVEY_SETUP', 'survey_setup');

define('TBL_SURVEY_RESPONSES', 'survey_responses');

define('TBL_SURVEY_RESPONSE_DETAILS', 'survey_responses_details');

define('TBL_SURVEY_SETUP_QUESTIONS', 'survey_setup_questions');

define('TBL_SURVEY_SETUP_QUESTION_OPTIONS', 'survey_setup_question_options');

define("TBL_EMPLOYEE_TERMINATIONS", 'employee_terminations');

define('TBL_EMPLOYEE_RECOGNITIONS', 'employee_recognitions');

define('TBL_COMPANY_ANNOUNCEMENTS', 'company_announcements');

define('TBL_TRANSFER_HISTORY_EMPLOYEES', 'emp_transfer_history');

define('TBL_BULK_TRANSFER_CATEGORIES', 'bulk_transfer_categories');

define('TBL_PROJECTS', 'projects');

define('TBL_PROJECT_TIMESHEETS', 'project_timesheets');

define('TBL_PROJECT_USERS', 'project_users');

define('TBL_PROJECT_TASKS', 'project_tasks');

define('TBL_EMP_CONTRACTS', 'emp_contracts');

define('TBL_EMP_WORK_SCHEDULES', 'emp_working_schedules');

define('TBL_ASSESSMENT_SETUP', 'assessment_setup');

define('TBL_ASSESSMENT_RESPONSES', 'assessment_responses');

define('TBL_ASSESSMENT_RESPONSE_DETAILS', 'assessment_responses_details');

define('TBL_ASSESSMENT_SETUP_QUESTIONS', 'assessment_setup_questions');

define('TBL_ASSESSMENT_SETUP_QUESTION_OPTIONS', 'assessment_setup_question_options');

define("TBL_VENDOR_AGREEMENTS", 'vendor_agreements');

define('TBL_EMP_PERFORMANCE_PEER_FEEDBACK', 'emp_performance_peer_feedback');

define('TBL_PERFORMANCE_SETUP', 'performance_setup');

define('TBL_APPLICATION_USERS', 'application_users');

define('TBL_PERFORMANCE_SETUP_QUESTIONS', 'performance_setup_questions');

define('TBL_PERFORMANCE_RESPONSES', 'performance_responses');

define('TBL_PERFORMANCE_RESPONSE_DETAILS', 'performance_responses_details');

define('TBL_PROJECT_TASK_DETAILS', 'project_task_details');

///////////////  Statc values ////////////

define('TIMESHEET_BILLABLE_CHECk', 'Y');

define("COMPANY_LDAP",'N');

//define('ADMIN_ROLE', '1');

define('REVIWER_ROLE', '8');

define('TOP_UP','1');

define('ST_COUNTRYCODE_INDIA', '101');

define('ST_VENDOR_REGISTRATION', '3');

define('ST_VENDOR_HRCONNECT', '1');

define('Income_Tax','19');

define('ST_ADMIN_ANNOUNCEMENT', '5');
 
define('NODATA','Previously Blank');

define('ST_RECOGINITION_ANNOUNCEMENT_CATEGORY_ID', '4');

define('ST_RECOGNITION_MSG','I`d like to recognize to');

define('ST_EFFECTIVE_TODATE', '3999-01-01');

//define('ADMIN_ROLE', '1');

define('ST_SURVEY_DURATION_IN_MINUTES','15');
	
define("TIMESHEET_RATE_CALCULATION_TYPE",'2'); 	//1)RATE CALCULATION CALANDER 2)WORKING DAY

define("SECURITYGUARD_ROLE",'21');

define('CSR_COMMITTEE_MEMBER', 'CSRCOMMITTEEMEMBER');

define('CSR_AMOUNT_DISPLAY_UNIT', 'L'); // L = Lakhs, Cr = Crores, M = Millions


#################Status Start##################################
 
/*define('ST_ACTIVE', '0');

define('ST_INACTIVE', '1');

define('ST_DRAFT', '2');

define('ST_APPROVED', '3');

define('ST_QUOTATION_GENERATED', '4');

define('ST_CANCEL', '5');

define('ST_APPROVAL_PENDING', '6');

define('ST_PURCHASE_ORDER_GENRATED', '7');

define('ST_INVOICE_GENERATED', '8');

define('ST_REJECTED', '9');

define('ST_PAYMENT_RECEIVED', '10');

define('ST_PARTIAL_PAYMENT', '11');

define('ST_PARTIAL_RECEIPT', '12');

define('ST_SEND_BACK', '13');


*/
define('COMPANY_SEND_NO', '8179947020');

define('NO_OF_NOTICE_DAYS', 60);

define('NO_OF_RECORDS_PER_PAGE', 5);

define('FISCALYEAR', '20202021');

define('ST_ACTIVE', '0');

define('ST_INACTIVE', '1');

define('ST_DRAFT', '2');

define('ST_APPROVAL_MANAGER', '8');

define('ST_APPROVAL_PENDING', '10');

define('ST_SEND_BACK', '20');

define('ST_REJECTED', '30');

define('ST_CANCEL', '40');

define('ST_TOBEPUBLISH', '51');

define('ST_PUBLISH', '52');

define('ST_UNPUBLISH', '53');

define('ST_RMSHORTLISTED', '54');

define('ST_HMSHORTLISTED', '55');

define('ST_LOIREQUESTED', '56');

define('ST_LOIGENERATED', '57');

define('ST_LOIACCEPTED', '58');

define('ST_LOIREJECTED', '59');


define('ST_APPROVED1', '101');
define('ST_APPROVED2', '102');
define('ST_APPROVED3', '103');
define('ST_APPROVED4', '104');
define('ST_APPROVED5', '105');
define('ST_APPROVED6', '106');
define('ST_APPROVED7', '107');
define('ST_APPROVED8', '108');
define('ST_APPROVED9', '109');
define('ST_APPROVED', '199');
define('ST_INPROGRESS', '3');
define('ST_COMPLETE', '4');
define('ST_HOLD', '5');
define('ST_CALLBACK', '60');


define('EMAILSTATICID', 'N'); //Email  send static emailid

define('ST_QUOTATION_GENERATED', '201');

define('ST_PURCHASE_ORDER_GENRATED', '202');

define('ST_INVOICE_GENERATED', '203');

define( "VMS_ROLES", [1 => "1", 2 => "5", 3 => "5", 4 => "5", 5 => "3", 6 => "5", 7 => "5", 8 => "4", 9 => "5", 10 => "5", 11 => "5", 12 => "5", 13 => "2", 14 => "5", 15 => "5", 16 => "5", 17 => "5", 18 => "5"]);

define('PRIMARY','#4099ff'); //primary
define('SUCCESS','#2ed8b6'); //success 
define('WARNING','#FFB64D'); //warning 
define('DANGER','#FF5370'); //warning 
define('INFO','#00bcd4'); //info 

define('SUPERADMIN_ROLE','SUPERADMIN');
define('ADMIN_ROLE','ADMIN');
define('ADMIN_ROLES','ADMIN');
define('RECRUITINGMANAGER_ROLE','RECRUITINGMANAGER');
define('RECRUITINGTEAM_ROLE','RECRUITINGTEAM');
define('CUSTOMER_ROLE','CUSTOMER');
define('VENDOR_ROLE','VENDOR');
define('CANDIDATE_ROLE','CANDIDATE');
define('HIRINGMANAGER_ROLE','HIRINGMANAGER');
define('EMPLOYEE_ONBOARDING','EMOPLOYEEONBOARDING');
define('ONBOARDING_HR','ONBOARDINGHR');

define('ASSET_EXECUTIVE_ROLE','ASSET_MANAGEMENT_EXECUTIVE');
define('ASSET_USER_ROLE','ASSET_MANAGEMENT_USER');
define('BILL_ADMIN_ROLE','BILL_ADMIN');

define('CLAIM_EXECUTIVE_ROLE','CLAIM_EXECUTIVE');

define('ESOP_ADMIN_ROLE','ESOP_ADMIN');
define('ESOP_USER_ROLE','ESOP_USER');
define('PMS_EMPLOYEE_ROLE','PMS_EMPLOYEE');

define('EMPLOYEE_ROLE','EMPLOYEE');

define('PETTY_CASH_ADMIN_ROLE','PETTY_CASH_ADMIN');
define('PETTY_CASH_USER_ROLE','PETTY_CASH_USER');

define('RENTAL_ADMIN_ROLE','RENTAL_ADMIN');
define('RENTAL_USER_ROLE','RENTAL_USER');

define('PAYROLL_ADMINISTRATOR_ROLE','PAYROLL_ADMINISTRATOR');
define('HR_ADMIN_ROLE','HR_ADMIN');

define('BILL_VENDOR_ROLE','BILL_VENDOR');
define('VMS_VENDOR_ROLE','VMS_VENDOR');
define('VMS_ADMIN_ROLE','VMS_ADMIN');
define('PURCHASE_VENDOR_ROLE','PURCHASE_VENDOR');

define('BUILDING_ADMIN_ROLE','BUILDING_ADMIN');
define('SPACE_LOCATION_ADMIN_ROLE','SPACE_LOCATION_ADMIN');


define('VENDOR_MODULE_ADMIN_ROLE','VENDOR_MODULE_ADMIN');
define('VENDOR_ADMIN_ROLE','VENDOR_ADMIN');
define('VENDOR_HR_ROLE','VENDOR_HR');
define('VENDOR_FINANCE_ROLE','VENDOR_FINANCE'); 
define('VENDOR_PROJECT_ROLE','VENDOR_PROJECT');
define('VENDOR_LOCATION_ROLE','VENDOR_LOCATION');
define('VENDOR_PURCHASE_ROLE','VENDOR_PURCHASE');
define('VENDOR_LEGAL_ROLE','VENDOR_LEGAL');




define('SUPERADMIN_COMPNAY_NAME','INFISUITE');
define('SUPERADMIN_EMAIL_ID','admin@infistaq.com');
define('SUPERADMIN_DISLAY_NAME','Infisuite');
define('SUPERADMIN_CONTACT_ADDRESS','Block 2,12th Floor, Juhu Complex, Bhubaneswar, Odisha ,752082');
define('SUPERADMIN_LOGO','file_1752306660_687213e435bbf.png');

//Hiring Sources


define('FIELD_AUTO_CHECK','1');

define('INFISUITESOURCE','4');

define('DEFAULT_PHONE_NUMBER_FORMAT',array("digit_format"=>"/^(\d{3})(\d{3})(\d{4})$/","view_format"=>"($1) $2-$3"));
#####################################candidate schedule insert entry email#################### 

define('CANDIDATE_TIMESLOT_MINUTS','45');
define('TELEPHONINC_INTERVIEW','12');
define('FACE_TO_FACE_INTERVIEW','13');
define('TELEPHONINC_INTERVIEW_SUBMIT_EMAIl','19');
define('FACE_TO_FACE_INTERVIEW_SUBMIT_EMAIl','20');
define('INSERT_CANDIDATE_INTERVIEW_SCHEDULE',array(TELEPHONINC_INTERVIEW,FACE_TO_FACE_INTERVIEW));
define('INTERVIEWER_TIMEZONE','Asia/Kolkata');


define('CLOCKIN_CLOCKOUT_MODULE_USED',9);
define('FOOD_OPTION_MODULE_USED',13);
define('TRANSPOSRT_OPTION_MODULE_USED',14);
define('PETTY_CASH_MODULE',20);
define('ACCOUNT_MODULE',4);
define('MODUL_HR',7);
define('MODUL_PAYROLL',8);
define('MODUL_ATTENDANCE',9);





#####################################candidate schedule insert entry email#################### 


// define("SUBSCRIPTION_ROLE",'13');
// define("COMPANY_ADMIN_ROLE",'1');

##################Status END ####################################

define('STR_TO_TIME',strtotime(date("Y-m-d H:i:s")));

define('ONLY_DATE',date("m-d-Y"));

define('DATE_TIME',date("m-d-Y H:i:s"));

define('DATE_TIME_FORMAT',date("l dS F Y, H:i:s A", STR_TO_TIME));

define('DATETIMEFORMAT',date("l-dS-F-Y-H-i-s-A", STR_TO_TIME));

define('DBIN','INSERT INTO ');

define('DBUP','UPDATE ');

define('DBWHR',' WHERE ');

define('DBDEL','DELETE ');

define('DBFROM',' FROM ');

define('DBSELECT',' SELECT ');

define('DBSET',' SET ');

define('HEAD_LTN','location:');

define('DB_LMT',' LIMIT ');

define('DB_ORDER',' ORDER BY ');

define('DB_LIKE',' LIKE ');

###################################################################

######### Physical Path Constants 

###################################################################


// Ticketing Module Paramiters
define("TICKET_ASSIGNER_ROLE",'4');
define("TICKET_FIXER_ROLE",'5');
define("TICKET_TESTER_ROLE",'6');
define("TICKET_FOLLOWER_ROLE",'7');
define("TICKET_USER_ROLE",'3');


//define(SITEROOT, 				$_SERVER['DOCUMENT_ROOT']."/");

define("SITEROOT", 				$_SERVER['DOCUMENT_ROOT']."/");

/*define(LISTINGIMAGESROOT, 		SITEROOT."/images/listings");
*/
define("UPLOADSROOT", 			SITEROOT."/uploads/");

define("USER_IMAGE_ROOT",	        SITEROOT."/images/");

###################################################################

######### Url Constants 

###################################################################

//define(SITEURL, 				"http://".$_SERVER['HTTP_HOST']);

/*------------- DEFULT PROJECT FLODER ------------------ */
define("DYNAMIC_SITEURL", 		"http://www.{domain_name}.");


define("SITEURL", 				"http://".$_SERVER['HTTP_HOST']."/merge_sonar_with_dev/");

/*define("TEST", 				SITEURL."/api.php");*/

define("API", 				SITEURL."/api.php/");

if($_SESSION['userid'] == 726){
    
  define("API",         SITEURL."/token_api.php/");

}else{
  define("API",         SITEURL."/api.php/");  
}

define("CRMAPI", 			SITEURL."/crm_api.php/");

define("GOOGLE_MAPS_API_KEY", "AIzaSyB45MxMrICyOGQPR_4cmuB69EmynFPISog"); // paste your Google Maps JavaScript API key here


define("IMGURL", 				SITEURL."/images");

define("UPLOADIMGURL", 			"uploads");

// added by Koti start
define("STARTING_YEAR",2019);

define("CLOCK_WITH_IN_TIME",'Y');

define("ACESS_OF_PESONAL_GATEWAY",'Y');

define("ACESS_OF_TRANSPORT_GATEWAY",'Y');

define("STATIONERY_INVENTORY",'Y');

define("EMPLOYEE_IDENTIFIER",'Y');

define("SHOW_ASSETS",'Y');

define("MANAGER_TYPE",'Y');

define("POLL_MODULE_ID",16);

define("POLL_CAT_ID",2);

// added by koti end
//define(SITEPATH_URL,'http://'.$_SERVER['HTTP_HOST']);

?>
