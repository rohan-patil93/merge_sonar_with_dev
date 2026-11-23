<?php
class mobileUserClass extends userClass{
    private const KEY_COMPANY_ID = 'company_id';
    private const SQL_QUOTE_AND_IS_ACTIVE_EQ0 = "' AND is_active =0";
    private const KEY_USER_ID = 'user_id';
    private const SQL_AND_IS_ACTIVE_0_NO_SPACE = " AND is_active =0";
    private const DATE_FORMAT_YMD = 'Y-m-d';
    private const KEY_DATA_NAME = 'data_name';
    private const SQL_AND_USER_ID = " AND user_id = ";
    private const SQL_AND_IS_ACTIVE_EQ0 = " AND is_active=0";
    private const KEY_MODULE_ID = 'module_id';
    private const SQL_AND_BACKTICK_IS_ACTIVE_0 = " AND `is_active` = 0";
    private const KEY_BACKGROUND_COLOR = 'background_color';
    private const KEY_COLOR = 'color';
    private const KEY_TRANSACTION_TABLE = 'transaction_table';
    private const KEY_TRANSACTION_ID = 'transaction_id';
    private const KEY_TRANSACTION_NO = 'transaction_no';
    private const KEY_CURRENT_STATUS = 'current_status';
    private const KEY_ERROR = 'error';
    private const KEY_TABLE = 'table';
    private const KEY_TRANSACTION_NUMBER = 'transaction_number';
    private const KEY_TOTAL_AMOUNT = 'total_amount';
    private const KEY_TRANSACTION_DATE = 'transaction_date';
    private const SQL_QUOTE_AND_IS_ACTIVE_0 = "' AND is_active = 0";
    private const KEY_IS_ACTIVE = 'is_active';
    private const KEY_CREATED_BY = 'created_by';
    private const KEY_MESSAGE = 'message';
    private const KEY_CLOCK_IN = 'clock_in';
    private const KEY_CLOCK_OUT = 'clock_out';
    private const KEY_EMP_ID = 'emp_id';
    private const KEY_DATE = 'date';
    private const KEY_TYPE = 'type';
    private const TBL_EMPLOYEE_DAILY_ATTENDANCES = 'employee_daily_attendances';
    private const KEY_EMPLOYEE_ID = 'employee_id';
    private const SQL_AND_IS_ACTIVE_0 = " AND is_active = 0";
    private const TIME_ZERO_HM = '00:00';
    private const TIME_ZERO_HMS = '00:00:00';
    private const STR_MINUS_1_DAY = '-1 day';
    private const KEY_STATUS = 'status';
    private const KEY_PRESENT = 'present';
    private const KEY_LEAVE_TYPE_ID = 'leave_type_id';
    private const KEY_LEAVE_FROM = 'leave_from';
    private const KEY_LEAVE_TO = 'leave_to';
    private const KEY_SPECIAL_REQUEST = 'special_request';
    private const KEY_REF_ID = 'ref_id';
    private const KEY_LEAVE_DAYS = 'leave_days';
    private const KEY_UPDATED_BY = 'updated_by';
    private const TBL_EMPLOYEE_LEAVES = 'employee_leaves';
    private const KEY_INSERT = 'insert';
    private const KEY_MAP_DATE = 'map_date';
    private const KEY_REMARKS = 'remarks';
    private const KEY_ITEM_ID = 'item_id';
    private const KEY_TABLE_NAME = 'table_name';
    private const KEY_PARTY_ID = 'party_id';
    private const KEY_LASTID = 'LASTID';
    private const KEY_APPLICATION_TABLE_NAME = 'application_table_name';
    private const KEY_FIRST = 'first';
    private const KEY_VALUE = 'value';
    private const KEY_PERCENTAGE = 'percentage';
    private const KEY_SECOND = 'second';
    private const STR_PLUS_1_DAY = '+1 day';
    private const SQL_AND_ID = " AND id=";
    private const KEY_USER_EMAIL = 'user_email';
    private const KEY_SUCCESS = 'success';
    private const KEY_PLAN_DATE = 'plan_date';
    private const KEY_CREATED_DATE = 'created_date';
    private const DATETIME_FORMAT_YMD_HIS = 'Y-m-d H:i:s';
    private const KEY_TAX_AMOUNT = 'tax_amount';
    private const KEY_ACCOUNT_CODE = 'account_code';
    private const KEY_GETTYPE = 'gettype';
    private const KEY_PROJECT_ID = 'project_id';
    private const TBL_PROJECT_TASK_DETAILS = 'project_task_details';
    private const KEY_TASK_ID = 'task_id';
    private const KEY_LOCATION = 'location';
    private const TBL_TRANSPORT_PERSONAL_GATEWAY = 'transport_personal_gateway';
    private const KEY_UPDATED_DATE = 'updated_date';
    private const TBL_TRANSPORT_GATEWAY = 'transport_gateway';
    private const KEY_CLOSING_BALANCE = "closing_balance";
    private const KEY_PARENT_ACCOUNT_CODE = 'parent_account_code';
    private const KEY_START_DATE = 'start_date';
    private const KEY_END_DATE = 'end_date';
    private const KEY_START_BALANCE = 'start_balance';
    private const KEY_END_BALANCE = 'end_balance';
    private const KEY_PREV_START_BALANCE = 'prev_start_balance';
    private const KEY_PREV_END_BALANCE = 'prev_end_balance';
    private const KEY_PRE_START_BALANCE = 'pre_start_balance';
    private const KEY_CURRENT = "current";
    private const KEY_PREVIOUS = "previous";
    private const TBL_ASSETS = 'assets';
    private const KEY_LABEL = 'label';
    private const KEY_P_TOTAL_AMOUNT = 'p_total_amount';
    private const SQL_EMP_ACTIVE_CASE = "') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '";
    private const KEY_ADMIN_TYPE = 'admin_type';
    private const SQL_OR_REPORTING_MANAGER = "') OR (emp.reporting_manager_id = '";
    private const SQL_OR_HR_BUDDY = "') OR (emp.hr_buddy = '";
    private const SQL_OR_FINANCE_BUDDY = "') OR (emp.finance_buddy = '";
    private const SQL_EMP_VENDOR_EMAIL = "') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '";
    private const KEY_TODAY_ATTENDANTS = 'todayattendants';
    private const TBL_STATIONERY_REQUESTS = 'stationery_requests';
    private const TBL_ASSET_REQUESTS = 'asset_requests';
    private const KEY_FIELD = 'field';


    private function formatMobileConfigBase($companyId, $selectFields){
        global $callConfig;
        $configsql = "SELECT {$selectFields}  FROM config WHERE company_id = '".$companyId.self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $configres = $callConfig->getRow($configsql);
        $configres->date_format = strtolower($configres->date_format);
        $configres->date_format = str_replace('mm','MM', $configres->date_format);
        $configres->date_format = str_replace('-', $configres->date_separator, $configres->date_format);
        $configres->attendance_enable_clock = $configres->attendance_enable_clock!="" ? $configres->attendance_enable_clock : 1;
        return $configres;
    }

    private function resolveEmployeeLocation($companyId, $empData){
        global $callConfig;
        $location_id = $empData->location_id;
        if($empData->id!=""){
            $query = "SELECT location_id FROM emp_locations WHERE company_id =".$companyId." AND emp_id = ".$empData->id." AND CURDATE() BETWEEN start_date AND end_date AND is_active = 0";
            $emp_loc = $callConfig->getRow($query);
            if($emp_loc->location_id!=""){
                $location_id = $emp_loc->location_id;
            }
        }
        if($location_id !=""){
            $query = "SELECT id, loc_latitude as location_latitude, loc_longitude as location_longitude FROM locations WHERE company_id = '".$companyId."' AND id = ".$location_id     .self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
            return $callConfig->getRow($query);
        }
        $query = "SELECT id, location_latitude, location_longitude FROM companies WHERE company_id = '".$companyId.self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        return $callConfig->getRow($query);
    }

    private function applyMobileShiftConfig($configres, $companyId, $empData){
        global $callConfig;
        $clock_event_type = 'Y'; // clockin siwp check
        if(isset($empData->clock_event_type) && $empData->clock_event_type == 'N'){
            $clock_event_type = 'N';
        }
        $configres->clock_event_type = $clock_event_type;
        if(isset($empData->is_show_payshilp) && $empData->is_show_payshilp == 1){
            $configres->view_salary = "N";
        }
        if($configres->shift_mode != '' && $configres->shift_mode == '0'){
            $configres->emp_id = $empData->id;
            $configres->shift_name = '';
            $configres->start_time = '';
            $configres->end_time = '';
            $query = "SELECT id, shift_id FROM emp_shifts WHERE company_id =".$companyId." AND emp_id = ".$empData->id."  AND is_active = 0";
            $emp_shifts_data = $callConfig->getRow($query);
            if($emp_shifts_data->shift_id!=""){
                $query = "SELECT id, shift_name, clock_in AS start_time, clock_out AS end_time  FROM shifts WHERE company_id =".$companyId." AND id = ".$emp_shifts_data->shift_id."  AND is_active = 0";
                $shifts_data = $callConfig->getRow($query);
                $configres->shift_name = $shifts_data->shift_name;
                $configres->start_time = $shifts_data->start_time;
                $configres->end_time = $shifts_data->end_time;
            }
        }else{
            $configres->start_time = self::TIME_ZERO_HMS;
            $configres->end_time = self::TIME_ZERO_HMS;
        }
    }

    private function applyMobileAlerts($configres, $companyId){
        global $callConfig;
        $configres->alert_miss_punch = 'Y';
        $configres->alert_miss_punch_status = 'N';
        $configres->alert_miss_punch_message = '';
        // we check yesterday user missed the punch
        $yesterday = date(self::DATE_FORMAT_YMD, strtotime(self::STR_MINUS_1_DAY));
        if($configres->alert_miss_punch == 'Y'){
            $query = "SELECT id FROM employee_daily_attendances WHERE employee_id='{$configres->emp_id}' AND attendace_date='{$yesterday}' AND attendance_key_short_code='MP' AND company_id='{$companyId}' AND is_active=0 LIMIT 1";
            $miss_punch_data = $callConfig->getRow($query);
            if($miss_punch_data->id!=''){
                $configres->alert_miss_punch_status = 'Y';
                $configres->alert_miss_punch_message = 'Yesterday you missed your punch';
            }
        }

        $configres->alert_leaves = 'Y';
        $configres->alert_leaves_status = 'N';
        $configres->alert_leaves_message = '';
        // next five days have any leaves are not approved
        $from_date = date(self::DATE_FORMAT_YMD);
        $to_day = date(self::DATE_FORMAT_YMD, strtotime('+5 days'));
        if($configres->alert_leaves == 'Y'){
            $query = "SELECT id, leave_from, leave_to FROM employee_leaves WHERE employee_id='{$configres->emp_id}' AND current_status!=".ST_APPROVED." AND company_id='{$companyId}' AND is_active=0 AND ( (leave_from BETWEEN '{$from_date}' AND '{$to_day}') OR (leave_to BETWEEN '{$from_date}' AND '{$to_day}') OR (leave_from <= '{$from_date}' AND leave_to >= '{$to_day}') ) LIMIT 1";
            $leaves_data = $callConfig->getRow($query);
            if($leaves_data->id!=''){
                $configres->alert_leaves_status = 'Y';
                $configres->alert_leaves_message = 'You have leave requests pending approval in the next 5 days';
            }
        }
    }

    private function resolveLiveLocationSetting($configres, $companyId, $userId){
        global $callConfig;
        if(LIVE_LOCATION==2){
            $configres->live_location = 1;
            $query = "SELECT MAX(live_lcation) as live_lcation FROM application_roles WHERE company_id ='".$companyId."' AND role_short_code IN (SELECT role_short_code FROM application_user_module_roles WHERE company_id ='".$companyId."' AND user_id ='".$userId."' AND is_active =0) AND is_active =0";
            $role_data = $callConfig->getRow($query);
            if($role_data->live_lcation!=""){
                $configres->live_location = $role_data->live_lcation;
            }
        }
        if(LIVE_LOCATION==3){
            $configres->live_location = 1;
            $query = "SELECT live_lcation FROM application_users WHERE company_id ='".$companyId."' AND id ='".$userId.self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
            $role_data = $callConfig->getRow($query);
            if($role_data->live_lcation!=""){
                $configres->live_location = $role_data->live_lcation;
            }
        }
        $configres->driver_id = "";
        $query = "SELECT id FROM drivers WHERE company_id=".$companyId.self::SQL_AND_USER_ID.$userId.self::SQL_AND_IS_ACTIVE_EQ0;
        $dres = $callConfig->getRow($query);
        if($dres->id!=""){
            $configres->driver_id = $dres->id;
        }
    }

    public function togetconfigmobiledata($get){
        global $callConfig;

        $configres = $this->formatMobileConfigBase($get[self::KEY_COMPANY_ID], "name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, billing_address, contact_address, attendance_enable_clock, shift_mode");
        $configres->view_salary = "Y";
        if($get[self::KEY_USER_ID]!=""){
            $query = "SELECT * FROM employees WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND user_id = ".$get[self::KEY_USER_ID]."  AND is_active =0";
            $emp_data = $callConfig->getRow($query);
            if($emp_data->id!=""){
                $this->applyMobileShiftConfig($configres, $get[self::KEY_COMPANY_ID], $emp_data);
            }
            $com_data = $this->resolveEmployeeLocation($get[self::KEY_COMPANY_ID], $emp_data);
        }

        $configres->location_latitude = $com_data->location_latitude;
        $configres->location_longitude = $com_data->location_longitude;
        $configres->clock_with_in_time = CLOCK_WITH_IN_TIME;// clock out with out radius
        $configres->acess_of_pesonal_gateway = ACESS_OF_PESONAL_GATEWAY;// ACESS_OF_PESONAL_GATEWAY
        $configres->acess_of_transport_gateway = ACESS_OF_TRANSPORT_GATEWAY;

        $configres->employee_identifier = 'N';
        $configres->show_assets = 'N';
        $configres->show_inventory = 'N';
        $configres->show_wfh = 'Y';

        $this->applyMobileAlerts($configres, $get[self::KEY_COMPANY_ID]);

        if($get[self::KEY_DATA_NAME]=='infisuitein_suwalka'){
            $configres->clock_with_in_time = 'Y';
            $configres->acess_of_pesonal_gateway = 'Y';
            $configres->acess_of_transport_gateway = 'Y';
            $configres->employee_identifier = EMPLOYEE_IDENTIFIER;
            $configres->show_assets = SHOW_ASSETS;
            $configres->show_inventory = 'Y';
            $configres->show_wfh = 'N';
        }

        $configres->employee_identifier = 'Y';

        $attquery = "SELECT id, minimum_hours_required, location_radius FROM attendance_settings WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $att_data = $callConfig->getRow($attquery);
        $configres->day_hours = $att_data->minimum_hours_required;
        $configres->location_radius = $att_data->location_radius;

        $configres->location_radius_check = LOCATION_RADIUS_CHECK;
        $configres->live_location = LIVE_LOCATION;
        $configres->live_battery = LIVE_BATTERY;
        $configres->live_internet = LIVE_INTERNET;
        $configres->live_face_liveness= LIVE_FACE_LIVE_NESS;
        $configres->live_announcement= MOBILE_ANNOUNCEMENT;
        $configres->viewLeaveApproval ='Y';
        $configres->announcement_reslut_view ='Y';
        $configres->view_attendance = "Y";
        $configres->view_leave = "Y";
        $this->resolveLiveLocationSetting($configres, $get[self::KEY_COMPANY_ID], $get[self::KEY_USER_ID]);
        $configres->start_leave_date = '2026-02-01';
        $configres->work_on_duty = 'Y';

        $configres->admin_type = 'N';
        if(isset($get[self::KEY_MODULE_ID]) && $get[self::KEY_MODULE_ID]!="" && isset($get['role_short_code']) && $get['role_short_code']!="" ){
            $query ="SELECT id FROM module_setup WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND role_admin_short_code IN ('".$get['role_short_code']."') AND is_active =0";
            $row = $callConfig->getRow($query);
            if(count((array)$row)>0){
                $configres->admin_type = 'Y';
            }
        }
        $configres->manager_type = 'Y';
        if($get[self::KEY_DATA_NAME]=='infistaqin_infisuite_2'){
             $configres->live_face_liveness= 1;
        }

        $return['config'] = $configres;
        return $return;
    }
    public function togetmoduleuserroleslist($get){
        global $callConfig;
        $query = "SELECT aur.user_id, aur.role_short_code, ar.display_name AS role_display_name FROM application_user_module_roles aur INNER JOIN application_roles ar ON aur.company_id = ar.company_id AND aur.role_short_code = ar.role_short_code WHERE aur.company_id = ".$get[self::KEY_COMPANY_ID]." AND aur.user_id = ".$get[self::KEY_USER_ID]." AND aur.application_module_id = ".$get[self::KEY_MODULE_ID]." AND aur.is_active = 0 AND ar.is_active = 0";
        $return['data'] =$callConfig->getAllRows($query);
        return $return;
    }
    public function togetconfigmobiledataold($get){
        global $callConfig;
        $configres = $this->formatMobileConfigBase($get[self::KEY_COMPANY_ID], "name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, billing_address, contact_address, attendance_enable_clock");
        if($get[self::KEY_USER_ID]!=""){
            $query = "SELECT id, location_id FROM employees WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND user_id = ".$get[self::KEY_USER_ID]."  AND is_active =0";
            $emp_data = $callConfig->getRow($query);
            $com_data = $this->resolveEmployeeLocation($get[self::KEY_COMPANY_ID], $emp_data);
        }
        $attquery = "SELECT id, minimum_hours_required, location_radius FROM attendance_settings WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $att_data = $callConfig->getRow($attquery);
        $configres->day_hours = $att_data->minimum_hours_required;
        $configres->location_radius_check = LOCATION_RADIUS_CHECK;
        $configres->location_radius = $att_data->location_radius;
        $configres->location_latitude = $com_data->location_latitude;
        $configres->location_longitude = $com_data->location_longitude;
        $now_date = date(self::DATE_FORMAT_YMD);
        if($emp_data->id!=""){
            $work_query = "SELECT * FROM request_work_from_home WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp_id = '".$emp_data->id."' AND effective_from_date <= '".$now_date."' AND effective_to_date >= '".$now_date.self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
            $workhome_data = $callConfig->getRow($work_query);
            if(count((array)$workhome_data)>0){
                $configres->location_radius_check = 'N';
            }
        }

        $configres->live_location = LIVE_LOCATION;
        $configres->live_battery = LIVE_BATTERY;
        $configres->live_internet = LIVE_INTERNET;
        $configres->live_face_liveness= LIVE_FACE_LIVE_NESS;
        $configres->view_attendance = "Y";
        $configres->view_leave = "Y";
        $configres->view_salary = "Y";
        $this->resolveLiveLocationSetting($configres, $get[self::KEY_COMPANY_ID], $get[self::KEY_USER_ID]);

        $return['config'] = $configres;
        return $return;
    }

    public function mobileaprovelistwithmodle($get){
        global $callConfig;
        $query = "SELECT id,application_module_id,role_id, role_short_code FROM application_user_module_roles WHERE company_id = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_USER_ID.$get[self::KEY_USER_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $datas = $callConfig->getAllRows($query);
        $all = 'N';
        $modules = array();
        foreach($datas as $data){
            if($data->role_short_code == 'ADMIN'){
                $all = 'Y';
                break;
            }else{
                $modules[] = $data->application_module_id;
            }
        }
        if($all == 'Y'){
            $whr = "";
        }else{
            $whr = "AND module_id IN(".implode(',',$modules).")";
        }
        $module_query = "SELECT module_id,module_name as display_name, display_name as full_name, module_logo FROM module_setup WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND linking =1  AND is_active =0 ".$whr;
        $module_data = $callConfig->getAllRows($module_query);
        $return['modules'] = $module_data;
        $configsql = "SELECT name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, billing_address, contact_address  FROM config WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $configres=$callConfig->getRow($configsql);
        $configres->date_format = strtolower($configres->date_format);
        $configres->date_format = str_replace('mm','MM', $configres->date_format);
        $configres->date_format = str_replace('-',  $configres->date_separator, $configres->date_format);
        $return['config'] = $configres;
        return $return;
    }
    public function togetbaseonmoduluelistandapprovals($get){
        global $callConfig;
        $lists = array();

        $auquery = "SELECT id, display_name, user_photo FROM application_users WHERE company_id ='".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $aures = $callConfig->getAllRows($auquery);
        $application_users = array();
        foreach($aures as $userau){
            $application_users[$userau->id] = $userau;
        }


        $query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `aplication_module` = ".$get[self::KEY_MODULE_ID]." AND `dashboard` = 0 AND `is_active` = 0";
        $datas = $callConfig->getAllRows($query);
        $query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
        $data_status = $callConfig->getAllRows($query);
        $arr_status = array();
        foreach($data_status as $data_stat){
                $arr_status[$data_stat->name_value][self::KEY_BACKGROUND_COLOR] = "#".str_replace("#","",$data_stat->background_color);
                $arr_status[$data_stat->name_value][self::KEY_COLOR] = "#".str_replace("#","",$data_stat->color);
        }
        $str = "";
        foreach($datas as $data){
            $str.= "'".$data->page_name."',";
        }
        $sat  = rtrim($str,",");
        $ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
        $apdatas = $callConfig->getAllRows($ap_query);
        $transtables = array();
        foreach($apdatas as $apdata){
            $list_table[$apdata->list_page_name] = $apdata->application_table_name;
            $transtables[$apdata->application_table_name] = $apdata->application_table_name;

        }
        $return['list'] = $lists;


        foreach($transtables as $transtab){
            $approval_query = "SELECT  transtable.id, tal.request_date, tal.response_type, tal.send_back_flag, tal.call_back, transtable.transaction_number, transtable.transaction_date, transtable.originator, transtable.party_name, transtable.amount, transtable.tax_amount, transtable.discount_amount, transtable.total_amount,transtable.shipping_amount, transtable.pending_amount, transtable.cost_center_id, transtable.current_status FROM transaction_approval_logs tal, ".$transtab." transtable WHERE tal.transaction_table = '".$transtab."' AND tal.transaction_id = transtable.id AND  tal.company_id = '".$get[self::KEY_COMPANY_ID]."' AND tal.company_id = transtable.company_id AND tal.user_id = '".$get[self::KEY_USER_ID]."' AND tal.send_back_flag = 0 AND tal.call_back = 1 AND tal.response_type ='0' ORDER BY tal.request_date DESC";
            $app_data=$callConfig->getAllRows($approval_query);
            foreach($app_data as $app){
                $data=(array)$app;
                $data[self::KEY_TRANSACTION_TABLE] = $transtab;
                $data[self::KEY_TRANSACTION_ID] = $app->id;

                $get[self::KEY_TRANSACTION_NO] = $app->id;
                $get[self::KEY_TRANSACTION_TABLE] = $transtab;
                $get[self::KEY_CURRENT_STATUS] = $app->current_status;
                $getDynamicWorkFlowSteps = $this->getDynamicWorkFlowSteps($get);
                $status_progress = $getDynamicWorkFlowSteps['status_progress'];
                $nextstepkeyvalue = $getDynamicWorkFlowSteps['nextstepkeyvalue'];
                $data['approved_status']=$nextstepkeyvalue[$app->current_status];
                $data[self::KEY_ERROR] = $getDynamicWorkFlowSteps->error;
                $data['reject_status']="30";
                $data['status_name']=$status_progress[$app->current_status]->name;
                $data[self::KEY_BACKGROUND_COLOR]=$arr_status[$app->current_status][self::KEY_BACKGROUND_COLOR];
                $data['text_color']=$arr_status[$app->current_status][self::KEY_COLOR];
                $data['originator_name']= $application_users[$app->originator]->display_name;
                $data['originator_initial']= $this->mobileInitials($application_users[$app->originator]->display_name);
                $data['originator_image']= $application_users[$app->originator]->user_photo;
                $all_data[$transtab][] =$data;
            }
        }
        foreach($datas as $data){
            $retd['name'] = $data->menu_label;
            $retd[self::KEY_TABLE] = $list_table[$data->page_name];
            $retd['data'] = array();
            if(isset($all_data[$list_table[$data->page_name]])){
                $retd['data'] = $all_data[$list_table[$data->page_name]];
            }
            $res[] = $retd;
        }
        return array('approvals'=>$res);
    }
    public function togetuserdetailsbaseonuserid($get){
        global $callConfig;
        $row_db = (object) array('db_name' => '');
        $query = "SELECT * FROM application_users WHERE id=".$get[self::KEY_USER_ID];
        $row_data = $callConfig->getRow($query);
        $query_employee = "SELECT id FROM ".$row_db->db_name.".employees where user_id =".$row_data->id." AND is_active=0 ";
        $employee = $callConfig->getRow($query_employee);
        $row_data->emp_id = $employee->id;
        return $row_data;
    }
    public function togetdynamicinvoicedetails($get){
        global $callConfig;
        $return = array();
        $transaction_id = $get[self::KEY_TRANSACTION_ID];
        $transaction_table = $get[self::KEY_TRANSACTION_TABLE];

        $configsql = "SELECT name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, billing_address, contact_address  FROM config WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $configres=$callConfig->getRow($configsql);
        $configres->date_format = strtolower($configres->date_format);
            $configres->date_format = str_replace('mm','MM', $configres->date_format);
            $configres->date_format = str_replace('-',  $configres->date_separator, $configres->date_format);
        $return['config'] = $configres;

        $tables = $this->toGetInvoicetabledata($transaction_table);

        $rental_invoicessql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND id = '".$transaction_id."'  AND is_active =0";
        $rental_invoicesres=$callConfig->getRow($rental_invoicessql);
        $return['invoices'] = $rental_invoicesres;

        $rental_invoice_itemsaql = "SELECT id, item_id, item_name, (SELECT sku FROM    items itm WHERE item_id = itm.id) sku, description, purchase_uom, (SELECT uom_name FROM unit_of_measurements um    WHERE item_id = um.id) uom, item_price, item_quantity, item_amount, tax_amount, discount_amount,
              total_amount   FROM ".$tables['item']." WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND transaction_id = '".$transaction_id."'  AND is_active =0";
        $rental_invoice_items=$callConfig->getAllRows($rental_invoice_itemsaql);
        $return['invoice_items'] = $rental_invoice_items;


        $billbysql = "SELECT id, display_name, user_email, user_mobile FROM application_users WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND id = '".$rental_invoicesres->originator."'AND is_active =0";
        $billbyres=$callConfig->getRow($billbysql);
        $return['bill_by'] = $billbyres;

        $bank_accountssql = "SELECT ba.id, ba.account_code, ba.bank_account_name, ba.bank_account_number, ba.bank_contact_person, ba.bank_contact_no, ba.bank_address, ba.upi_id, ba.ifsc_code, ba.account_type, (SELECT bank.bank_name FROM banks bank WHERE ba.company_id = bank.company_id AND bank.id =ba.bank_id AND ba.is_active = bank.is_active) as bank_name FROM bank_accounts ba WHERE ba.company_id = '".$get[self::KEY_COMPANY_ID]."' AND ba.id = '".$configres->default_bank_account_id."' AND ba.is_active =0";
        $bank_accountsres=$callConfig->getRow($bank_accountssql);
        $return['bank_accounts'] = $bank_accountsres;


          // -------------------- Start project_task_details ---------------------------
        $tax_authorities_rates_splitsql = "SELECT id, split_name, (tax_rate/100) as tax_rate, account_code, split_type FROM `tax_authorities_rates_split` WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $tax_authorities_rates_splitres = $callConfig->getAllRows($tax_authorities_rates_splitsql);
        $return['tax_authorities_rates_split']= $tax_authorities_rates_splitres;
        // -------------------- End project_task_details --------------------------

        return $return;
    }
    public function togetrecallstatusdata($get){
        global $callConfig;
        $query = "UPDATE `transaction_approval_logs` SET `response_type` = '0' WHERE user_id = 377";
        $callConfig->executeQuery($query);
        $query = "UPDATE `requisitions` SET `current_status` = 101 WHERE id IN(411,412,413)";
        return $callConfig->executeQuery($query);
    }
    public function togetcreatedapproverejectinvoicedetails($get){
        global $callConfig;
        $lists = array();
        $query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `aplication_module` = ".$get[self::KEY_MODULE_ID]." AND `dashboard` = 0 AND `is_active` = 0";
        $datas = $callConfig->getAllRows($query);
        $query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
        $data_status = $callConfig->getAllRows($query);
        $arr_status = array();
        foreach($data_status as $data_stat){
                $arr_status[$data_stat->name_value][self::KEY_BACKGROUND_COLOR] = $data_stat->background_color;
                $arr_status[$data_stat->name_value][self::KEY_COLOR] = $data_stat->color;
        }
        $str = "";
        foreach($datas as $data){
            $str.= "'".$data->page_name."',";
        }
        $sat  = rtrim($str,",");
        $ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
        $apdatas = $callConfig->getAllRows($ap_query);

        foreach($apdatas as $apdata){
            $list_table[$apdata->list_page_name] = $apdata->application_table_name;
        }
        $return['list'] = $lists;
         $approval_query = "SELECT ta.id, ta.module_setup_id, ta.transaction_id, ta.transaction_table FROM transaction_approval_logs ta WHERE ta.company_id = ".$get[self::KEY_COMPANY_ID]." AND ta.module_setup_id = ".$get[self::KEY_MODULE_ID]." AND ta.user_id = ".$get[self::KEY_USER_ID]." AND ta.response_type != 0";
        $app_data = $callConfig->getAllRows($approval_query);
        foreach((array)$app_data as $app){
            $query = "SELECT transaction_number, total_amount, transaction_date,current_status FROM ".$app->transaction_table." WHERE id=".$app->transaction_id;
            $list_data = $callConfig->getRow($query);
            $data=(array)$app;
            $data[self::KEY_TRANSACTION_NUMBER]=$list_data->transaction_number;
            $data[self::KEY_TOTAL_AMOUNT]=$list_data->total_amount;
            $data[self::KEY_TRANSACTION_DATE]=$list_data->transaction_date;
            $data[self::KEY_CURRENT_STATUS]=$list_data->current_status;
            $data[self::KEY_BACKGROUND_COLOR]=$arr_status[$list_data->current_status][self::KEY_BACKGROUND_COLOR];
            $data['text_color']=$arr_status[$list_data->current_status][self::KEY_COLOR];
            $data['status_name']= "Process";
            $data['originator_name']= 'Pabitra Kumar Jena';
            $data['originator_initial']= 'PKJ';
            $data['originator_image']= '';
            $view = 'Y';
            if($list_data->current_status==30){
                $view = 'N';
            }
            $all_data[$app->transaction_table][$view][] =$data;
        }
        foreach($datas as $data){
            $talee = $list_table[$data->page_name];
            $cquery = "SELECT id,".$get[self::KEY_MODULE_ID]." module_setup_id ,id as transaction_id, '".$talee."' transaction_table ,transaction_number, total_amount, transaction_date, current_status, '#000000' background_color, '#FFFFFF' text_color, 'Pabitra Kumar Jena' originator_name, 'Process' status_name, 'PKJ' originator_initial, 'file_1689092069_64ad7fe56a8f4.jpg' originator_image FROM ".$list_table[$data->page_name]." WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND (created_by = ".$get[self::KEY_USER_ID]." OR originator = ".$get[self::KEY_USER_ID].") AND is_active =0";

            $create_data = $callConfig->getAllRows($cquery);
            $retd['name'] = $data->menu_label;
            $retd[self::KEY_TABLE] = $list_table[$data->page_name];
            $retd['data'] = array();
            if(isset($all_data[$list_table[$data->page_name]])){
                $retd['approved_data'] = $all_data[$list_table[$data->page_name]]['Y'];
                $retd['rejected_data'] = $all_data[$list_table[$data->page_name]]['Y'];
            }
            $retd['create_data'] = $create_data;
            $res[] = $retd;
        }
        return array('after_approval'=>$res);
    }
    public function togetcreatedinvoicedetails($get){
        global $callConfig;
        $query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `aplication_module` = ".$get[self::KEY_MODULE_ID]." AND `dashboard` = 0 AND `is_active` = 0";
        $datas = $callConfig->getAllRows($query);
        $query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
        $data_status = $callConfig->getAllRows($query);
        $arr_status = array();
        foreach($data_status as $data_stat){
                $arr_status[$data_stat->name_value][self::KEY_BACKGROUND_COLOR] = "#".str_replace("#","",$data_stat->background_color);
                $arr_status[$data_stat->name_value][self::KEY_COLOR] = "#".str_replace("#","",$data_stat->color);
        }
        $str = "";
        foreach($datas as $data){
            $str.= "'".$data->page_name."',";
        }
        $sat  = rtrim($str,",");
        $ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
        $apdatas = $callConfig->getAllRows($ap_query);

        foreach($apdatas as $apdata){
            $list_table[$apdata->list_page_name] = $apdata->application_table_name;
        }
        $create_da = array();
        $all_data = array();
        foreach($datas as $data){
            $talee = $list_table[$data->page_name];
            $cquery = "SELECT tb.id,".$get[self::KEY_MODULE_ID]." module_setup_id ,tb.id as transaction_id, '".$talee."' transaction_table ,tb.transaction_number, tb.total_amount, tb.transaction_date, tb.current_status,  au.display_name originator_name,  sp.name status_name,  au.user_photo  originator_image FROM ".$list_table[$data->page_name]." tb, application_users au, status_progress sp WHERE tb.company_id =".$get[self::KEY_COMPANY_ID]." AND (tb.created_by = ".$get[self::KEY_USER_ID]." OR tb.originator = ".$get[self::KEY_USER_ID].") AND tb.originator = au.id AND sp.table_name = '".$list_table[$data->page_name]."' AND tb.current_status = sp.status AND tb.is_active =0";
            $create_datas = $callConfig->getAllRows($cquery);
            foreach($create_datas as $crd){
                $create_data = (array)$crd;
                $create_data['originator_initial'] = $this->initials($crd->originator_name);
                $create_data[self::KEY_BACKGROUND_COLOR] =$arr_status[$crd->current_status][self::KEY_BACKGROUND_COLOR];
                $create_data['text_color'] = $arr_status[$crd->current_status][self::KEY_COLOR];
                $create_da[] = $create_data;
            }
            $all_data[$talee] = $create_da;
        }
        foreach($all_data as $table=>$data){
            $retd['name'] = ucwords(str_replace('_',' ',$table));
            $retd[self::KEY_TABLE] = $table;
            $retd['data'] = $data;
            $res[] = $retd;
        }
        return array('data_created'=>$res);
    }
    public function togetapproveinvoicedetails($get){
        global $callConfig;

        $auquery = "SELECT id, display_name, user_photo FROM application_users WHERE company_id ='".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $aures = $callConfig->getAllRows($auquery);
        $application_users = array();
        foreach($aures as $userau){
            $application_users[$userau->id] = $userau;
        }


        $query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `aplication_module` = ".$get[self::KEY_MODULE_ID]." AND `dashboard` = 0 AND `is_active` = 0";
        $datas = $callConfig->getAllRows($query);
        $query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
        $data_status = $callConfig->getAllRows($query);
        $arr_status = array();
        foreach($data_status as $data_stat){
                $arr_status[$data_stat->name_value][self::KEY_BACKGROUND_COLOR] = "#".str_replace("#","",$data_stat->background_color);
                $arr_status[$data_stat->name_value][self::KEY_COLOR] = "#".str_replace("#","",$data_stat->color);
        }
        $str = "";
        foreach($datas as $data){
            $str.= "'".$data->page_name."',";
        }
        $sat  = rtrim($str,",");
        $ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
        $apdatas = $callConfig->getAllRows($ap_query);
        $transtables = array();
        foreach($apdatas as $apdata){
            $list_table[$apdata->list_page_name] = $apdata->application_table_name;
            $transtables[$apdata->application_table_name] = $apdata->application_table_name;
        }




        foreach($transtables as $transtab){
            $approval_query = "SELECT  transtable.id, tal.request_date, tal.response_type, tal.send_back_flag, tal.call_back, transtable.transaction_number, transtable.transaction_date, transtable.originator, transtable.party_name, transtable.amount, transtable.tax_amount, transtable.discount_amount, transtable.total_amount,transtable.shipping_amount, transtable.pending_amount, transtable.cost_center_id, transtable.current_status FROM transaction_approval_logs tal, ".$transtab." transtable WHERE tal.transaction_table = '".$transtab."' AND tal.transaction_id = transtable.id AND  tal.company_id = '".$get[self::KEY_COMPANY_ID]."' AND tal.company_id = transtable.company_id AND tal.user_id = '".$get[self::KEY_USER_ID]."' AND tal.send_back_flag = 0 AND tal.response_type != 30 AND tal.call_back = 1 AND tal.response_type !='0' ORDER BY tal.request_date DESC";
            $app_data=$callConfig->getAllRows($approval_query);
            foreach($app_data as $app){
                $data=(array)$app;
                $data[self::KEY_TRANSACTION_TABLE] = $transtab;
                $data[self::KEY_TRANSACTION_ID] = $app->id;
                $data['status_name']=$arr_status[$app->current_status]['name'];
                $data[self::KEY_BACKGROUND_COLOR]=$arr_status[$app->current_status][self::KEY_BACKGROUND_COLOR];
                $data['text_color']=$arr_status[$app->current_status][self::KEY_COLOR];
                $data['originator_name']= $application_users[$app->originator]->display_name;
                $data['originator_initial']= $this->mobileInitials($application_users[$app->originator]->display_name);
                $data['originator_image']= $application_users[$app->originator]->user_photo;
                $all_data[$transtab][] =$data;
            }
        }

        foreach($all_data as $table=>$data){
            $retd['name'] = ucwords(str_replace('_',' ',$table));
            $retd[self::KEY_TABLE] = $table;
            $retd['data'] = $data;
            $res[] = $retd;
        }
        return array('data_approved'=>$res);
    }
    public function togetrecjectinvoicedetails($get){
        global $callConfig;
        $lists = array();
        $auquery = "SELECT id, display_name, user_photo FROM application_users WHERE company_id ='".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $aures = $callConfig->getAllRows($auquery);
        $application_users = array();
        foreach($aures as $userau){
            $application_users[$userau->id] = $userau;
        }

        $query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `aplication_module` = ".$get[self::KEY_MODULE_ID]." AND `dashboard` = 0 AND `is_active` = 0";
        $datas = $callConfig->getAllRows($query);
        $query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
        $data_status = $callConfig->getAllRows($query);
        $arr_status = array();
        foreach($data_status as $data_stat){
                $arr_status[$data_stat->name_value][self::KEY_BACKGROUND_COLOR] = "#".str_replace("#","",$data_stat->background_color);
                $arr_status[$data_stat->name_value][self::KEY_COLOR] = "#".str_replace("#","",$data_stat->color);
        }
        $str = "";
        foreach($datas as $data){
            $str.= "'".$data->page_name."',";
        }
        $sat  = rtrim($str,",");
        $ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
        $apdatas = $callConfig->getAllRows($ap_query);
        $transtables = array();
        foreach($apdatas as $apdata){
            $list_table[$apdata->list_page_name] = $apdata->application_table_name;
            $transtables[$apdata->application_table_name] = $apdata->application_table_name;
        }
        $return['list'] = $lists;

        foreach($transtables as $transtab){
            $approval_query = "SELECT  transtable.id, tal.request_date, tal.response_type, tal.send_back_flag, tal.call_back, transtable.transaction_number, transtable.transaction_date, transtable.originator, transtable.party_name, transtable.amount, transtable.tax_amount, transtable.discount_amount, transtable.total_amount,transtable.shipping_amount, transtable.pending_amount, transtable.cost_center_id, transtable.current_status FROM transaction_approval_logs tal, ".$transtab." transtable WHERE tal.transaction_table = '".$transtab."' AND tal.transaction_id = transtable.id AND  tal.company_id = '".$get[self::KEY_COMPANY_ID]."' AND tal.company_id = transtable.company_id AND tal.user_id = '".$get[self::KEY_USER_ID]."' AND tal.send_back_flag = 0 AND tal.response_type = 30 AND tal.call_back = 1 AND tal.response_type !='0' ORDER BY tal.request_date DESC";
            $app_data=$callConfig->getAllRows($approval_query);
            foreach($app_data as $app){
                $data=(array)$app;
                $data[self::KEY_TRANSACTION_TABLE] = $transtab;
                $data[self::KEY_TRANSACTION_ID] = $app->id;
                $data['status_name']=$arr_status[$app->current_status]['name'];
                $data[self::KEY_BACKGROUND_COLOR]=$arr_status[$app->current_status][self::KEY_BACKGROUND_COLOR];
                $data['text_color']=$arr_status[$app->current_status][self::KEY_COLOR];
                $data['originator_name']= $application_users[$app->originator]->display_name;
                $data['originator_initial']= $this->mobileInitials($application_users[$app->originator]->display_name);
                $data['originator_image']= $application_users[$app->originator]->user_photo;
                $all_data[$transtab][] =$data;
            }
        }
        foreach($all_data as $table=>$data){
            $retd['name'] = ucwords(str_replace('_',' ',$table));
            $retd[self::KEY_TABLE] = $table;
            $retd['data'] = $data;
            $res[] = $retd;
        }
        return array('data_rejected'=>$res);
    }
    public function toaddupdateusermobiledetails($get, $post){
        global $callConfig;
        $update = "update user_mobile_details set is_active = NULL, updated_by = ".$get[self::KEY_USER_ID]." where user_id ='".$get[self::KEY_USER_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $callConfig->executeQuery($update);
        $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_USER_ID=>$get[self::KEY_USER_ID],'fcm_token'=>$post['fcm_token'],self::KEY_IS_ACTIVE=>0,self::KEY_CURRENT_STATUS=>0,self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
        return $callConfig->insertRecord('user_mobile_details',$insert_array);
    }
    public function togenratefirebaseheaderauthentication($get){
        $url = SITEURL."/cfa_token.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        return curl_exec($ch);

    }
    public function verifyFirebaseToken($idToken) {
        $apiKey = "YOUR_FIREBASE_WEB_API_KEY"; // Replace with your Firebase API Key
        $url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key={$apiKey}";

        $postData = json_encode(["idToken" => $idToken]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $data = json_decode($response, true);

        if (isset($data['users'])) {
            return ['valid' => true, 'uid' => $data['users'][0]['localId']];
        } else {
            return ['valid' => false, self::KEY_ERROR => 'Invalid token'];

        }
    }
    public function generateNewToken($refreshToken) {
        $apiKey = "YOUR_FIREBASE_WEB_API_KEY"; // Replace with your Firebase API Key
        $url = "https://securetoken.googleapis.com/v1/token?key={$apiKey}";

        $postData = json_encode([
            "grant_type" => "refresh_token",
            "refresh_token" => $refreshToken
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $data = json_decode($response, true);

        if (isset($data['id_token'])) {
            return ['newToken' => $data['id_token']];
        } else {
            return [self::KEY_ERROR => $data[self::KEY_ERROR][self::KEY_MESSAGE] ?? 'Token generation failed'];
        }
    }
    public function initials($str) {
        $ret = '';
        foreach (explode(' ', $str) as $word) {
            $ret .= strtoupper($word[0]);
        }
        if(strlen($ret)>2){
            $ret = substr($ret, 0,1).substr($ret, -1);
        }
        return strtoupper($ret);
    }
    public function notificationsforuserid($get){
        global $callConfig;
        $whr = "";
        if($get[self::KEY_MODULE_ID]!=""){
            $whr = " AND  nu.module_setup_id=".$get[self::KEY_MODULE_ID];
        }
        $query = "SELECT nu.module_setup_id, nu.user_id, nu.notification_type, nu.notification_id, nu.read_status, nt.notification_message, UPPER(LEFT(nt.notification_message, 2)) AS short_code, nt.application_table_name, nt.transaction_number, nt.notification_datetime, nu.read_status, nu.read_datetime, nu.id as read_id FROM notification_user nu,notifications nt WHERE nu.user_id =".$get[self::KEY_USER_ID]." AND  nu.notification_id = nt.id ".$whr." ORDER BY nt.notification_datetime DESC ";
        $datas = $callConfig->getAllRows($query);
        foreach($datas as $dd){
            if($dd->read_status==0){
                $data[$dd->notification_type][] = $dd;
            }
            if($dd->read_status==1){
                $data['READ'][$dd->notification_type][] = $dd;
            }else{
                $data['UN_READ'][$dd->notification_type][] = $dd;
            }
        }

        return $data;
    }
    public function countLines() {
        $array = array('views', 'model', 'apis', 'cronjob', 'setups', 'phpqrcode', 'partials', 'includes','outsidefile');
        foreach($array as $arr){
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($arr));
            $totalLines = 0;

            foreach ($files as $file) {
                if ($file->isFile() && $file->getExtension() == 'php') {
                    $lines = count(file($file->getRealPath()));
                    $totalLines += $lines;
                }
            }
            $lines_array[$arr] = $totalLines;
            $valtotalLines = $valtotalLines+$totalLines;
        }
        $lines_array['Total'] = $valtotalLines;
        return $lines_array;
    }
    public function togetemployeeattendatailsbasedondate($get){
        global $callConfig;
        $return = array('attendace_date'=>$get['date'], 'clock_in_date'=>'', self::KEY_CLOCK_IN=>'', 'clock_out_date'=>'', self::KEY_CLOCK_OUT=>'', 'clock_type'=>'OUT');
        $query ="SELECT attendace_date, clock_in_date, clock_in, clock_out_date, clock_out FROM employee_daily_attendances WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND employee_id =".$get[self::KEY_EMP_ID]." AND attendace_date ='".$get['date'].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $data = $callConfig->getRow($query);
        if(count((array)$data)>0){
            $return[self::KEY_CLOCK_IN] = $data->clock_in;
            $return[self::KEY_CLOCK_OUT] = $data->clock_out;
            $return['clock_in_date'] = $data->clock_in_date;
            $return['clock_out_date'] = $data->clock_out_date;
            if($data->clock_in!=""&&$data->clock_out==""){
                $return['clock_type'] = "IN";
            }
        }
        return $return;
    }
    public function mobileemployeeclocks_old($get){
        global $callConfig;
        $query = $callConfig->selectQuery(self::TBL_EMPLOYEE_DAILY_ATTENDANCES,'*',"is_active=0 AND employee_id='".$get[self::KEY_EMP_ID]."' AND attendace_date='".$get['date']."'",'','','');
        $data = $callConfig->getRow($query);
        if($data->id ==""){
            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_EMPLOYEE_ID=>$get[self::KEY_EMP_ID],'attendace_date'=>$get['date'],self::KEY_CLOCK_IN=>$get['time'],'check_in_type'=>'1',self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'0','in_out'=>'1','device_type'=>'android');
            $callConfig->insertRecord(self::TBL_EMPLOYEE_DAILY_ATTENDANCES,$insert_array);
        }
        $queryd = $callConfig->selectQuery('employee_daily_attendance_details','*'," employee_id='".$get[self::KEY_EMP_ID]."' AND attendace_date='".$get['date']."' AND clock_out is NULL AND is_active=0",'','','');
        $datad = $callConfig->getRow($queryd);
        if(count((array)$datad) == "0"){
            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_EMPLOYEE_ID=>$get[self::KEY_EMP_ID],'attendace_date'=>$get['date'],self::KEY_CLOCK_IN=>$get['time'],'check_in_type'=>'1',self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'0','in_out'=>'1');
            $callConfig->insertRecord('employee_daily_attendance_details',$insert_array);
            if(count((array)$data)>"0"){
                $callConfig->updateRecord(self::TBL_EMPLOYEE_DAILY_ATTENDANCES, array(self::KEY_CLOCK_OUT=>''),'id',$data->id);
            }
        }else{
            $insert_array = array(self::KEY_CLOCK_OUT=>$get['time'],'check_out_type'=>'1','in_out'=>1);
            $callConfig->updateRecord('employee_daily_attendance_details',$insert_array,'id',$datad->id);
            $callConfig->updateRecord(self::TBL_EMPLOYEE_DAILY_ATTENDANCES,$insert_array,'id',$data->id);
        }
        return $this->togetemployeeattendatailsbasedondate($get);
    }
    public function mobileemployeeclocks($get){
        global $callConfig;
        $query = $callConfig->selectQuery(self::TBL_EMPLOYEE_DAILY_ATTENDANCES,'*',"is_active=0 AND employee_id='".$get[self::KEY_EMP_ID]."' AND attendace_date='".$get['date']."'",'','','');
        $data = $callConfig->getRow($query);
        if($data->id ==""){
            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_EMPLOYEE_ID=>$get[self::KEY_EMP_ID],'attendace_date'=>$get['date'],'clock_in_date'=>$get['date'],self::KEY_CLOCK_IN=>$get['time'],'check_in_type'=>'1',self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'0','in_out'=>'1','device_type'=>'android');
            $callConfig->insertRecord(self::TBL_EMPLOYEE_DAILY_ATTENDANCES,$insert_array);
        }
        $queryd = $callConfig->selectQuery('employee_daily_attendance_details','*'," employee_id='".$get[self::KEY_EMP_ID]."' AND attendace_date='".$get['date']."' AND clock_out is NULL AND is_active=0",'','','');
        $datad = $callConfig->getRow($queryd);
        if(count((array)$datad) == "0"){
            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_EMPLOYEE_ID=>$get[self::KEY_EMP_ID],'attendace_date'=>$get['date'],self::KEY_CLOCK_IN=>$get['time'],'check_in_type'=>'1',self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'0','in_out'=>'1', 'clock_in_lat' => $get['clock_in_lat'] ?? '','clock_in_long' => $get['clock_in_long'] ?? '','clock_out_lat' => $get['clock_out_lat'] ?? '','clock_out_long' => $get['clock_out_long'] ?? '');
            $callConfig->insertRecord('employee_daily_attendance_details',$insert_array);
            if(count((array)$data)>"0"){
                $callConfig->updateRecord(self::TBL_EMPLOYEE_DAILY_ATTENDANCES, array(self::KEY_CLOCK_OUT=>''),'id',$data->id);
            }
        }else{
            $insert_array = array(self::KEY_CLOCK_OUT=>$get['time'],'check_out_type'=>'1','in_out'=>1, 'clock_in_lat' => $get['clock_in_lat'] ?? '','clock_in_long' => $get['clock_in_long'] ?? '','clock_out_lat' => $get['clock_out_lat'] ?? '','clock_out_long' => $get['clock_out_long'] ?? '');
            $callConfig->updateRecord('employee_daily_attendance_details',$insert_array,'id',$datad->id);
            $insert_array = array(self::KEY_CLOCK_OUT=>$get['time'],'check_out_type'=>'1','in_out'=>1,'clock_out_date'=>$get['date']);
            $callConfig->updateRecord(self::TBL_EMPLOYEE_DAILY_ATTENDANCES,$insert_array,'id',$data->id);
        }
        return $this->togetemployeeattendatailsbasedondate($get);
    }
    private function getFullDaySeconds($companyId, $empId){
        global $callConfig;
        $query = "SELECT id, working_days, full_day_hours FROM shift_settings WHERE id IN (SELECT shift_setting_id FROM shifts WHERE id IN ( SELECT shift_id FROM `emp_shifts` WHERE company_id = ".$companyId."  AND emp_id = ".$empId." AND is_active=0))";
        $weak_off_arr = $callConfig->getRow($query);
        if($weak_off_arr->id==''){
            $qry = "SELECT id, working_days, full_day_hours FROM attendance_settings
                    WHERE company_id = {$companyId} AND is_active = 0 ORDER BY id ASC LIMIT 1";
            $weak_off_arr = $callConfig->getRow($qry);
        }
        $fullDaySeconds = 0;
        if(!empty($weak_off_arr->full_day_hours)){
            $parts = explode(':', $weak_off_arr->full_day_hours);
            $h = isset($parts[0]) ? intval($parts[0]) : 0;
            $m = isset($parts[1]) ? intval($parts[1]) : 0;
            $s = isset($parts[2]) ? intval($parts[2]) : 0;
            $fullDaySeconds = ($h * 3600) + ($m * 60) + $s;
        }
        return $fullDaySeconds;
    }

    private function calculateOvertimeUndertime($attRow, $fullDaySeconds){
        $otString = self::TIME_ZERO_HM;
        $utString = self::TIME_ZERO_HM;
        if(empty($attRow->clock_in) || empty($attRow->clock_out)){
            return [$otString, $utString];
        }
        $ci = strtotime($attRow->clock_in_date . ' ' . $attRow->clock_in);
        $co = strtotime($attRow->clock_out_date . ' ' . $attRow->clock_out);
        if(!$ci || !$co){
            return [$otString, $utString];
        }
        $workedSeconds = max(0, $co - $ci);
        if($fullDaySeconds > 0){
            if($workedSeconds > $fullDaySeconds){
                $otString = gmdate("H:i", $workedSeconds - $fullDaySeconds);
            } elseif($workedSeconds < $fullDaySeconds){
                $utString = gmdate("H:i", $fullDaySeconds - $workedSeconds);
            }
        }
        return [$otString, $utString];
    }

    private function resolveAttendanceDayCode($valda, $valday, $data, $working_days, $holidays, $today){
        if(isset($data[$valda]) && $data[$valda]->attendance_key_short_code!=""){
            return $data[$valda]->attendance_key_short_code;
        }
        if(!in_array($valday, $working_days)){
            return "O";
        }
        if(in_array($valda, (array)$holidays)){
            return "H";
        }
        if(strtotime($valda)<=strtotime($today)){
            return isset($data[$valda]) ? "P" : "A";
        }
        return "N";
    }

    public function listgetmonthattendancedetails($get){
        global $callConfig;
        $fromdate = $get[self::KEY_DATE]!=""? $get[self::KEY_DATE] : date('y-m-01');
        $fromdate = date('y-m-01', strtotime($fromdate));
        $todate = date('Y-m-t', strtotime($fromdate));
        if($get[self::KEY_EMP_ID]==""){
            $query = "SELECT id FROM employees WHERE user_id=".$get[self::KEY_USER_ID].self::SQL_AND_IS_ACTIVE_EQ0;
            $eedta = $callConfig->getRow($query);
            $get[self::KEY_EMP_ID] = $eedta->id;
        }
        $emp_id = $get[self::KEY_EMP_ID];

        $query = "SELECT id, working_days FROM attendance_settings WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $attendances = $callConfig->getRow($query);

        $query = "SELECT holiday_date FROM `holidays` WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND holiday_date >= '".$fromdate."' AND holiday_date <= '".$todate."' AND location_id IN (SELECT location_id FROM employees WHERE id=".$emp_id.") AND is_active =0";
        $listholidays = $callConfig->getAllRows($query);
        $holidays = array();
        foreach((array)$listholidays as $listholiday){
            $holidays[] = $listholiday->holiday_date;
        }
        $query = "SELECT key_name,key_bgcolor, key_short_code,  color_code, key_text_color, display_value FROM attendance_key_types WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0;
        $types = $callConfig->getAllRows($query);
        foreach($types as $ty){
            $type[$ty->key_short_code] = $ty->display_value;
            $return[self::KEY_TYPE][]=$ty;
        }
        $query = "SELECT attendace_date,clock_in_date , clock_out_date,  TIME_TO_SEC(TIMEDIFF( CONCAT(clock_in_date, ' ', clock_in), CONCAT(clock_out_date, ' ', clock_out) ))/3600 AS hour_difference, clock_in,  attendance_key_short_code ,clock_out, ROUND(((TIME_TO_SEC(TIMEDIFF(CONCAT(clock_in_date, ' ', clock_in), CONCAT(clock_out_date, ' ', clock_out)))-28800)/3600)*0.6 , 2) as extra_hours, '00:10' as absent_hours  FROM employee_daily_attendances WHERE company_id =".$get[self::KEY_COMPANY_ID]."  AND employee_id =".$get[self::KEY_EMP_ID]." AND attendace_date >= '".$fromdate."' AND attendace_date <= '".$todate."' AND is_active=0";
        $datas = $callConfig->getAllRows($query);
        $return['att_query'] = $query;
        foreach($datas as $dat){
            $dat->display_value = $type[$dat->attendance_key_short_code];
            $data[$dat->attendace_date] =  $dat;
            $return['attendance'][]=$dat;
        }

        $fullDaySeconds = $this->getFullDaySeconds($get[self::KEY_COMPANY_ID], $get[self::KEY_EMP_ID]);
        $working_days = json_decode($attendances->working_days,1);
        $datediff = floor((strtotime($todate) - strtotime($fromdate))/(60*60*24));
        $today = date(self::DATE_FORMAT_YMD);

        for($i = 0; $i < $datediff + 1; $i++){
            $valda =  date(self::DATE_FORMAT_YMD, strtotime($fromdate . ' + ' . $i . 'day'));
            $valday = ucwords(date("l", strtotime($fromdate . ' + ' . $i . 'day')));
            list($otString, $utString) = $this->calculateOvertimeUndertime($data[$valda], $fullDaySeconds);
            $dayCode = $this->resolveAttendanceDayCode($valda, $valday, $data, $working_days, $holidays, $today);

            $xyz = array(
            'attendace_date'=> $valda,
            'attendance_key_short_code'=> $dayCode,
            'hour_difference'=> "",
            self::KEY_CLOCK_IN=> self::TIME_ZERO_HM,
            self::KEY_CLOCK_OUT=> self::TIME_ZERO_HM,
            'extra_hours'=> self::TIME_ZERO_HM,
            'absent_hours'=> self::TIME_ZERO_HM);

            $array = isset($data[$valda]) ? (array)$data[$valda] : $xyz;
            $array['extra_hours'] =$otString;
            $array['absent_hours'] = $utString;
            $array[self::KEY_STATUS] = $dayCode;
            $array['display_value'] = $type[$dayCode];
            $array[self::KEY_CLOCK_IN] = $array[self::KEY_CLOCK_IN] !="" ? $array[self::KEY_CLOCK_IN] : self::TIME_ZERO_HM;
            $array[self::KEY_CLOCK_OUT] = $array[self::KEY_CLOCK_IN] !="" ? $array[self::KEY_CLOCK_OUT] : self::TIME_ZERO_HM;
            $return['data'][] = $array;
        }
        $return['attendance'] = $return['data'];

        return $return;
     }
    private function calculateOvertimeUndertimeOld($attRow, $fullDaySeconds){
        $otString = self::TIME_ZERO_HM;
        $utString = self::TIME_ZERO_HM;
        if(empty($attRow->clock_in) || empty($attRow->clock_out)){
            return [$otString, $utString];
        }
        $ci = strtotime($attRow->clock_in);
        $co = strtotime($attRow->clock_out);
        if($co > $ci && $fullDaySeconds > 0){
            $workedSeconds = $co - $ci;
            if($workedSeconds > $fullDaySeconds){
                $otString = gmdate("H:i", $workedSeconds - $fullDaySeconds);
            } elseif($workedSeconds < $fullDaySeconds){
                $utString = gmdate("H:i", $fullDaySeconds - $workedSeconds);
            }
        }
        return [$otString, $utString];
    }

    public function listgetmonthattendancedetails_old($get){
        global $callConfig;
        $fromdate = $get[self::KEY_DATE]!=""? $get[self::KEY_DATE] : date('y-m-01');
        $fromdate = date('y-m-01', strtotime($fromdate));
        $todate = date('Y-m-t', strtotime($fromdate));
        $emp_id = $get[self::KEY_EMP_ID];

        $query = "SELECT id, working_days FROM attendance_settings WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $attendances = $callConfig->getRow($query);

        $query = "SELECT holiday_date FROM `holidays` WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND holiday_date >= '".$fromdate."' AND holiday_date <= '".$todate."' AND location_id IN (SELECT location_id FROM employees WHERE id=".$emp_id.") AND is_active =0";
        $listholidays = $callConfig->getAllRows($query);
        $holidays = array();
        foreach((array)$listholidays as $listholiday){
            $holidays[] = $listholiday->holiday_date;
        }
        $query = "SELECT key_name,key_bgcolor, key_short_code,  color_code, key_text_color, display_value FROM attendance_key_types WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0;
        $types = $callConfig->getAllRows($query);
        foreach($types as $ty){
            $type[$ty->key_short_code] = $ty->display_value;
            $return[self::KEY_TYPE][]=$ty;
        }
        $query = "SELECT attendace_date, TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))/3600 AS hour_difference, clock_in,  attendance_key_short_code ,clock_out, ROUND(((TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))-28800)/3600)*0.6 , 2) as extra_hours, '00:10' as absent_hours  FROM employee_daily_attendances WHERE company_id =".$get[self::KEY_COMPANY_ID]."  AND employee_id =".$get[self::KEY_EMP_ID]." AND attendace_date >= '".$fromdate."' AND attendace_date <= '".$todate."' AND is_active=0";
        $datas = $callConfig->getAllRows($query);

        foreach($datas as $dat){
            $dat->display_value = $type[$dat->attendance_key_short_code];
            $data[$dat->attendace_date] =  $dat;
            $return['attendance'][]=$dat;
        }

        $fullDaySeconds = $this->getFullDaySeconds($get[self::KEY_COMPANY_ID], $get[self::KEY_EMP_ID]);
        $working_days = json_decode($attendances->working_days,1);
        $datediff = floor((strtotime($todate) - strtotime($fromdate))/(60*60*24));
        $today = date(self::DATE_FORMAT_YMD);

        for($i = 0; $i < $datediff + 1; $i++){
            $valda =  date(self::DATE_FORMAT_YMD, strtotime($fromdate . ' + ' . $i . 'day'));
            $valday = ucwords(date("l", strtotime($fromdate . ' + ' . $i . 'day')));
            list($otString, $utString) = $this->calculateOvertimeUndertimeOld($data[$valda], $fullDaySeconds);
            $dayCode = $this->resolveAttendanceDayCode($valda, $valday, $data, $working_days, $holidays, $today);

            $xyz = array(
            'attendace_date'=> $valda,
            'attendance_key_short_code'=> $dayCode,
            'hour_difference'=> "",
            self::KEY_CLOCK_IN=> self::TIME_ZERO_HM,
            self::KEY_CLOCK_OUT=> self::TIME_ZERO_HM,
            'extra_hours'=> self::TIME_ZERO_HM,
            'absent_hours'=> self::TIME_ZERO_HM);

            $array = isset($data[$valda]) ? (array)$data[$valda] : $xyz;
            $array['extra_hours'] =$otString;
            $array['absent_hours'] = $utString;
            $array[self::KEY_STATUS] = $dayCode;
            $array['display_value'] = $type[$dayCode];
            $array[self::KEY_CLOCK_IN] = $array[self::KEY_CLOCK_IN] !="" ? $array[self::KEY_CLOCK_IN] : self::TIME_ZERO_HM;
            $array[self::KEY_CLOCK_OUT] = $array[self::KEY_CLOCK_IN] !="" ? $array[self::KEY_CLOCK_OUT] : self::TIME_ZERO_HM;
            $return['data'][] = $array;
        }
        if($get[self::KEY_DATA_NAME]=='infisuitein_suwalka'){
            $return['attendance'] = $return['data'];
        }

        return $return;
     }
     public function togetyearlymonthwisedataperce($get){
         global $callConfig;
         $year = $get['year']!="" ? $get['year'] : date('Y');
         $query = "SELECT id, working_days FROM attendance_settings WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $attendances = $callConfig->getRow($query);
         $working_days = json_decode($attendances->working_days,1);
         $months = array( 1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
         $query = "SELECT count(id) as hday, month(holiday_date) hmonth FROM `holidays` WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND holiday_year = ".$year." AND is_active =0 GROUP BY hmonth";
         $hdatas = $callConfig->getAllRows($query);
         foreach($hdatas as $hdata){
             $holid[$hdata->hmonth] = $hdata->hday;
         }
         $equery = "SELECT MONTH(attendace_date) atmonth, SUM(if((TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))/3600)>0 , 1,0)) dayaaa FROM employee_daily_attendances WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND employee_id =".$get[self::KEY_EMP_ID]." AND YEAR(attendace_date) = ".$year." AND is_active=0 GROUP BY atmonth";
        $eatdatas = $callConfig->getAllRows($equery);
        foreach($eatdatas as $eatda){
            $empattm[$eatda->atmonth] =  $eatda->dayaaa;
        }
         $workingDaysByMonth = array();

        for ($month = 1; $month <= 12; $month++) {
            $workingDays = 0;
            $startDate = date(self::DATE_FORMAT_YMD, strtotime($year."-".$month."-01"))        ;
             $daysInMonth = date('Y-m-t', strtotime($startDate));
             $datediff = strtotime($daysInMonth) - strtotime($startDate);
            $datediff = floor($datediff/(60*60*24));
            $alldays =0; $week_off = array(); $week_work = array();
               for ($day = 0; $day <= $datediff; $day++) {
                   $valda =  date(self::DATE_FORMAT_YMD, strtotime($startDate . ' + ' . $day . 'day'));
                $valday = ucwords(date("l", strtotime($startDate . ' + ' . $day . 'day')));
                if(in_array($valday, $working_days)){
                    $workingDays++;
                    $week_work[]=$day."--".$valday."//".$valda;
                }else{
                    $week_off[]=$day."--".$valday."//".$valda;
                }
                $alldays++;
            }
            if(isset($holid[$month])){
                $workingDays = $workingDays-$holid[$month];
            }
            $workingDaysByMonth[$month]['month_name'] = $months[$month];
            $workingDaysByMonth[$month]['month'] = $month;



            $workingDaysByMonth[$month]['work_days'] = $workingDays;
            $workingDaysByMonth[$month][self::KEY_PRESENT] = 0;
            $workingDaysByMonth[$month]['absent'] = 0;
            $workingDaysByMonth[$month]['precentage'] = 0;
            $workingDaysByMonth[$month]['extra_hours'] = 0;
            if(isset($empattm[$month])){

                $workingDaysByMonth[$month][self::KEY_PRESENT] = intval($empattm[$month]);
                   $workingDaysByMonth[$month]['absent'] = $workingDays-$empattm[$month];
                $workingDaysByMonth[$month]['precentage'] = $workingDays != 0 ? round(($empattm[$month]/$workingDays)*100) : 0;
            }
            $return['month_data'][] = $workingDaysByMonth[$month];
        }
        return $return;
     }
     public function togetmobileleavedetails($get){
         global $callConfig;
         $query = "SELECT id, leave_type_name FROM leave_types WHERE company_id =".$get[self::KEY_COMPANY_ID]."  AND is_active = 0";
         $leaves = $callConfig->getAllRows($query);
         foreach($leaves as $leav){
             $leave[$leav->id] = $leav->leave_type_name;
             $leave_type[]=$leav;
         }
         $return['type'] = $leave_type;
         $query = "SELECT * FROM `status_progress` WHERE  company_id =".$get[self::KEY_COMPANY_ID]." AND table_name = 'employee_leaves' AND is_active = 0";
         $statusprocess = $callConfig->getAllRows($query);
         foreach($statusprocess as $statusproc){
             $status[$statusproc->status] = $statusproc->name;
         }
         $query = "SELECT * FROM emp_leave_balances WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND employee_id =".$get[self::KEY_EMP_ID].self::SQL_AND_IS_ACTIVE_0;
         $leave_balance = $callConfig->getAllRows($query);
         $leave_left = 0; $leave_used=0;
         $leave_type_data = array();
         $leftdd = array();
         foreach($leave_balance as $balance){

             $leavetypedate['name'] = $leave[$balance->leave_type];
             $leavetypedate['applicable_leaves'] = $balance->applicable_leaves-$balance->consumed_leaves;
             $leave_type_data[] = $leavetypedate;
             $leave_left = $leave_left+$balance->applicable_leaves;
             $leave_used = $leave_used+$balance->consumed_leaves;
             $leftdd[] = $balance->applicable_leaves."-".$balance->consumed_leaves;
         }
         $return['leave_type_data'] = $leave_type_data;
         $return['leave_total'] = $leave_left;
         $return['leave_left'] = $leave_left-$leave_used;
         $return['leave_used'] = $leave_used;
         $query = "SELECT * FROM employee_leaves WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND employee_id =".$get[self::KEY_EMP_ID].self::SQL_AND_IS_ACTIVE_0;
         $allleaves = $callConfig->getAllRows($query);
         foreach($allleaves as $allleav){
             $app_leave['id'] = $allleav->id;
             $app_leave[self::KEY_LEAVE_TYPE_ID] = $allleav->leave_type_id;
             $app_leave['no_of_days'] = $allleav->leave_days;
             $app_leave[self::KEY_LEAVE_FROM] = $allleav->leave_from;
             $app_leave[self::KEY_LEAVE_TO] = $allleav->leave_to;
             $app_leave['leave_name'] = $leave[$allleav->leave_type_id];
             $app_leave['from_time'] = $allleav->from_time;
             $app_leave['to_time'] = $allleav->to_time;
             $app_leave['leave_duration_type'] = $allleav->leave_duration_type;
             $app_leave[self::KEY_SPECIAL_REQUEST] = $allleav->special_request;
             $app_leave['applied_date'] = date(self::DATE_FORMAT_YMD, strtotime($allleav->application_date));
             $app_leave[self::KEY_STATUS] = $allleav->current_status;
             $app_leave['status_name'] = $status[$allleav->current_status];
             $all_app_leave[]=$app_leave;
         }
         $return['applied_leave'] = $all_app_leave;
         return $return;
     }
     public function toaddmobileempleave_old($get, $post){
         global $callConfig;
         $leave_from_at = str_replace('/"', '-', $post[self::KEY_LEAVE_FROM]);
        $leave_from_at =  date(self::DATE_FORMAT_YMD, strtotime($leave_from_at));
        $leave_to_at = str_replace('/"', '-', $post[self::KEY_LEAVE_TO]);
        $leave_to_at =  date(self::DATE_FORMAT_YMD, strtotime($leave_to_at));
        if($post[self::KEY_REF_ID]!=""){
            $insert_array = array(self::KEY_LEAVE_TYPE_ID=>$post[self::KEY_LEAVE_TYPE_ID],self::KEY_LEAVE_FROM=>$leave_from_at,self::KEY_LEAVE_TO=>$leave_to_at,self::KEY_LEAVE_DAYS=>$post[self::KEY_LEAVE_DAYS], self::KEY_SPECIAL_REQUEST=>$post[self::KEY_SPECIAL_REQUEST],self::KEY_CURRENT_STATUS=>'0',self::KEY_IS_ACTIVE=>'0',self::KEY_UPDATED_BY=>$get[self::KEY_USER_ID]);
            $res = $callConfig->updateRecord(self::TBL_EMPLOYEE_LEAVES,$insert_array,'id',$post[self::KEY_REF_ID]);
            $ret[self::KEY_INSERT] =$res;
        }else{
             $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_EMPLOYEE_ID=>$get[self::KEY_EMP_ID], self::KEY_SPECIAL_REQUEST=>$post[self::KEY_SPECIAL_REQUEST],self::KEY_LEAVE_TYPE_ID=>$post[self::KEY_LEAVE_TYPE_ID],self::KEY_LEAVE_FROM=>$leave_from_at,self::KEY_LEAVE_TO=>$leave_to_at,self::KEY_LEAVE_DAYS=>$post[self::KEY_LEAVE_DAYS],self::KEY_CURRENT_STATUS=>'0',self::KEY_IS_ACTIVE=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
            $insert = $callConfig->insertRecord(self::TBL_EMPLOYEE_LEAVES,$insert_array);

            $ret[self::KEY_INSERT] =$insert;
        }
        return $ret;
     }
     public function toaddmobileempleave($get, $post){
         global $callConfig;
         $leave_from_at = str_replace('/"', '-', $post[self::KEY_LEAVE_FROM]);
        $leave_from_at =  date(self::DATE_FORMAT_YMD, strtotime($leave_from_at));
        $leave_to_at = str_replace('/"', '-', $post[self::KEY_LEAVE_TO]);
        $leave_to_at =  date(self::DATE_FORMAT_YMD, strtotime($leave_to_at));
        if($post[self::KEY_REF_ID]!=""){
            $insert_array = array(self::KEY_LEAVE_TYPE_ID=>$post[self::KEY_LEAVE_TYPE_ID],self::KEY_LEAVE_FROM=>$leave_from_at,self::KEY_LEAVE_TO=>$leave_to_at,self::KEY_LEAVE_DAYS=>$post[self::KEY_LEAVE_DAYS], self::KEY_SPECIAL_REQUEST=>$post[self::KEY_SPECIAL_REQUEST],self::KEY_CURRENT_STATUS=>'0',self::KEY_IS_ACTIVE=>'0',self::KEY_UPDATED_BY=>$get[self::KEY_USER_ID],'from_time'=>$post['from_time'] ?? '','to_time'=>$post['to_time'] ?? '','leave_duration_type'=>$post['leave_duration_type'] ?? '');
            $res = $callConfig->updateRecord(self::TBL_EMPLOYEE_LEAVES,$insert_array,'id',$post[self::KEY_REF_ID]);
            $ret[self::KEY_INSERT] =$res;
        }else{
             $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_EMPLOYEE_ID=>$get[self::KEY_EMP_ID], self::KEY_SPECIAL_REQUEST=>$post[self::KEY_SPECIAL_REQUEST],self::KEY_LEAVE_TYPE_ID=>$post[self::KEY_LEAVE_TYPE_ID],self::KEY_LEAVE_FROM=>$leave_from_at,self::KEY_LEAVE_TO=>$leave_to_at,self::KEY_LEAVE_DAYS=>$post[self::KEY_LEAVE_DAYS],self::KEY_CURRENT_STATUS=>'0',self::KEY_IS_ACTIVE=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID],'from_time'=>$post['from_time'] ?? '','to_time'=>$post['to_time'] ?? '','leave_duration_type'=>$post['leave_duration_type'] ?? '');
            $insert = $callConfig->insertRecord(self::TBL_EMPLOYEE_LEAVES,$insert_array);

            $ret[self::KEY_INSERT] =$insert;
        }
        return $ret;
     }
     public function toupdatemobileempleave($get, $post){
         global $callConfig;
         $leave_from_at = str_replace('/"', '-', $post[self::KEY_LEAVE_FROM]);
        $leave_from_at =  date(self::DATE_FORMAT_YMD, strtotime($leave_from_at));
        $leave_to_at = str_replace('/"', '-', $post[self::KEY_LEAVE_TO]);
        $leave_to_at =  date(self::DATE_FORMAT_YMD, strtotime($leave_to_at));
         $insert_array = array(self::KEY_LEAVE_TYPE_ID=>$post[self::KEY_LEAVE_TYPE_ID],self::KEY_LEAVE_FROM=>$leave_from_at,self::KEY_LEAVE_TO=>$leave_to_at,self::KEY_LEAVE_DAYS=>$post[self::KEY_LEAVE_DAYS], self::KEY_SPECIAL_REQUEST=>$post[self::KEY_SPECIAL_REQUEST],self::KEY_CURRENT_STATUS=>'0',self::KEY_IS_ACTIVE=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
        $res = $callConfig->updateRecord(self::TBL_EMPLOYEE_LEAVES,$insert_array,'id',$post[self::KEY_REF_ID]);
        $ret['update'] =$res;
        return $ret;
     }
     public function todeletemobileempleave($get){
         global $callConfig;
         $update_query = "UPDATE employee_leaves SET is_active = NULL WHERE id =".$get[self::KEY_REF_ID];
         return $callConfig->executeQuery($update_query);
     }
     public function toinsertfieldmapuser($get,$post){
         global $callConfig;
         $current_status = $post[self::KEY_CURRENT_STATUS]!='' ? $post[self::KEY_CURRENT_STATUS] :'0';
         $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_USER_ID=>$get[self::KEY_USER_ID], 'map_title' => $post['map_title'], 'phone'=>$post['phone'], 'address'=>$post['address'] , 'map_latitude'=>$post['map_latitude'],'map_longitude'=>$post['map_longitude'], self::KEY_MAP_DATE=>$post[self::KEY_MAP_DATE],'map_time'=>$post['map_time'],'image'=>'file_1689092069_64ad7fe56a8f4.jpg', 'map_distance'=>$post['map_distance'],'comments'=>$post['comments'],'work_type'=>'ON', 'field_work_daily_plan_id'=> $post['field_work_daily_plan_id'], 'status_radius'=> $post['status_radius'], self::KEY_REMARKS=> $post[self::KEY_REMARKS] ,self::KEY_CURRENT_STATUS=>$current_status,self::KEY_IS_ACTIVE=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
        $insert = $callConfig->insertRecord('field_work_user_map_points',$insert_array);
        $ret[self::KEY_INSERT] =$insert;
        return $ret;
     }
     public function togetfieldworkusermappoints($get){
         global $callConfig;

         $query = "SELECT id,map_title, map_latitude, map_longitude, map_date, map_time,'file_1689092069_64ad7fe56a8f4.jpg' as image, map_distance, comments,status_radius, field_work_daily_plan_id, status_radius , remarks  FROM field_work_user_map_points WHERE company_id = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_USER_ID.$get[self::KEY_USER_ID]." AND map_date = '".$get['date'].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $datas = $callConfig->getAllRows($query);
        foreach($datas as $data){
            $ret[]=$data;
        }
        $return['data'] = $ret;

        return $return;
     }
     public function tocreatecomplanatedetails($get, $post){
         global $callConfig;
         $complaint_image = '';
         $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_USER_ID=>$get[self::KEY_USER_ID],'complaint_name'=>$post['complaint_name'],'complaint_type'=>$post['complaint_type'], self::KEY_TRANSACTION_DATE=>$post[self::KEY_TRANSACTION_DATE] ,'complaint_priority'=>$post['complaint_priority'],'building_id'=>$post['building_id'],'building_floor_id'=>$post['building_floor_id'],'building_floor_space_id'=>$post['building_floor_space_id'],self::KEY_ITEM_ID=>$post[self::KEY_ITEM_ID],'asset_id'=>$post['asset_id'],'complaint_image'=>$complaint_image, self::KEY_REMARKS=>$post[self::KEY_REMARKS], self::KEY_TABLE_NAME=>$post[self::KEY_TABLE_NAME],self::KEY_PARTY_ID=>$post[self::KEY_PARTY_ID], self::KEY_REF_ID=>$post[self::KEY_REF_ID], self::KEY_IS_ACTIVE=>0,self::KEY_CURRENT_STATUS=>0,self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
         $insert = $callConfig->insertRecord('complaints',$insert_array);
         $ret[self::KEY_INSERT] =$insert;
         $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_TRANSACTION_TABLE=>'complaints',self::KEY_TRANSACTION_ID=>$insert[self::KEY_LASTID],'previous_status'=>0, 'request_date'=>$post[self::KEY_TRANSACTION_DATE] ,'assigner_user_id'=>$get[self::KEY_USER_ID],self::KEY_USER_ID=>$get[self::KEY_USER_ID],'module_setup_id'=>$get[self::KEY_MODULE_ID]);
         $insert = $callConfig->insertRecord('transaction_approval_logs',$insert_array);

        $ret['trn_insert'] =$insert;
        $ret['post'] =$post;
        return $ret;
         //complainant_name, complainant_type, complaint_description, building_id, building_floor_id, building_floor_space_id, item_id, asset_id, complaint_image, remarks
     }
     public function multipleimagesapplicationattachments($get,$post){
        global $callConfig;
        foreach($post['images'] as $img){
            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],'file_name'=>$img,'actual_file_name'=>$img,'file_size_mb'=>0,self::KEY_APPLICATION_TABLE_NAME=>$post[self::KEY_APPLICATION_TABLE_NAME],self::KEY_TRANSACTION_ID=>$post[self::KEY_TRANSACTION_ID],self::KEY_IS_ACTIVE =>'0');
            $res = $callConfig->insertRecord('application_attachments',$insert_array);
        }
        return $res;
    }
     public function togetapproalcomplientdetails($get){
         global $callConfig;
         $query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
        $data_status = $callConfig->getAllRows($query);
        $arr_status = array();
        foreach($data_status as $data_stat){
                $arr_status[$data_stat->name_value][self::KEY_BACKGROUND_COLOR] = $data_stat->background_color;
                $arr_status[$data_stat->name_value][self::KEY_COLOR] = $data_stat->color;
        }
        $approval_query = "SELECT ta.id, ta.module_setup_id, ta.transaction_id, ta.transaction_table FROM transaction_approval_logs ta WHERE ta.company_id = ".$get[self::KEY_COMPANY_ID]." AND ta.transaction_table = 'complaints' AND ta.user_id = ".$get[self::KEY_USER_ID]." AND ta.response_type = 0";
        $app_data = $callConfig->getAllRows($approval_query);
        foreach((array)$app_data as $app){
            $query = "SELECT tb.id, tb.complaint_type, tb.transaction_date, tb.current_status, tb.complaint_name, au.display_name originator_name, au.user_photo originator_image, sp.name status_name FROM ".$app->transaction_table."  tb, application_users au, status_progress sp  WHERE tb.id=".$app->transaction_id." AND tb.created_by = au.id AND sp.table_name = '".$app->transaction_table."' AND tb.current_status = sp.status AND tb.is_active=0";
            $list_data = $callConfig->getRow($query);
                $data=(array)$list_data;
                $data[self::KEY_TRANSACTION_NUMBER]=$list_data->id;
                $data[self::KEY_BACKGROUND_COLOR]=$arr_status[$list_data->current_status][self::KEY_BACKGROUND_COLOR];
                $data['text_color']=$arr_status[$list_data->current_status][self::KEY_COLOR];
                $data['status_name']= $list_data->status_name;
                $data['originator_name']= $list_data->originator_name;
                $data['originator_initial']= $this->initials($list_data->originator_name);
                $data['originator_image']= $list_data->originator_image;
                $all_data['data'][] =$data;
            //}
        }
        return $all_data;
     }

     public function togetcreatecomplientdetails($get){
         global $callConfig;
         $query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
        $data_status = $callConfig->getAllRows($query);
        $arr_status = array();
        foreach($data_status as $data_stat){
                $arr_status[$data_stat->name_value][self::KEY_BACKGROUND_COLOR] = $data_stat->background_color;
                $arr_status[$data_stat->name_value][self::KEY_COLOR] = $data_stat->color;
        }
         $query = "SELECT tb.id, tb.complaint_type, tb.transaction_date, tb.current_status, tb.complaint_name, tb.remarks, tb.complaint_priority, au.display_name originator_name, au.user_photo originator_image, sp.name status_name FROM complaints tb, application_users au, status_progress sp  WHERE  tb.created_by = ".$get[self::KEY_USER_ID]." AND tb.user_id= au.id AND tb.current_status = sp.status  AND sp.table_name = 'complaints' AND tb.current_status = sp.status AND tb.is_active =0";
            $list_datas = $callConfig->getAllRows($query);
            foreach((array)$list_datas as $list_data){
                $data=(array)$list_data;
                $data[self::KEY_TRANSACTION_NUMBER]=$list_data->id;
                $data[self::KEY_BACKGROUND_COLOR]=$arr_status[$list_data->current_status][self::KEY_BACKGROUND_COLOR];
                $data['text_color']=$arr_status[$list_data->current_status][self::KEY_COLOR];
                $data['status_name']= $list_data->status_name;
                $data['originator_name']= $list_data->originator_name;
                $data['originator_initial']= $this->initials($list_data->originator_name);
                $data['originator_image']= $list_data->originator_image;
                $all_data['data'][] =$data;
            }
        return $all_data;
     }
     public function togenrateqrcodebasetype($get){

         $array = array('item'=>'Item', 'building'=> 'Building Location', 'asset'=> 'Asset');
         $i=10;
         foreach($array as $key=>$arr){
             $data['key'] = $key;
             $data['name'] = $arr;
             $data['priority'] = $i;
             $i = $i+10;
             $return['type'][]= $data;
         }
         return $return;
     }
     public function togenrateqrcodebaseontypelist($get){
         global $callConfig;
         if($get['type']=='item'){
             $query = "SELECT id, sku, item_name, sale_price FROM items WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
            $data = $callConfig->getAllRows($query);
         }
         if($get['type']=='building'){
             $query = "SELECT id, building_name FROM buildings WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
            $data = $callConfig->getAllRows($query);
         }
         if($get['type']=='asset'){
             $query = "SELECT id, asset_code, asset_description FROM assets WHERE `company_id` = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_BACKTICK_IS_ACTIVE_0;
            $data = $callConfig->getAllRows($query);
         }
         foreach((array)$data as $da){
             $dat = (array)$da;
             $return['data'][]=$dat;
         }
         return $return;
     }
     public function togetbulidingfloors($get){
         global $callConfig;
         $query = "SELECT id,  floor_name FROM building_floors WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND building_id =".$get['building_id'].self::SQL_AND_IS_ACTIVE_0;
        $data = $callConfig->getAllRows($query);
        foreach((array)$data as $da){
             $dat = (array)$da;
             $return['data'][]=$dat;
         }
         return $return;
     }
    private function buildCirclePair($firstName, $firstVal, $firstPct, $secondName, $secondVal, $secondPct){
        return array(
            self::KEY_FIRST => array(
                'name' => $firstName,
                self::KEY_VALUE => $firstVal,
                self::KEY_PERCENTAGE => $firstPct,
            ),
            self::KEY_SECOND => array(
                'name' => $secondName,
                self::KEY_VALUE => $secondVal,
                self::KEY_PERCENTAGE => $secondPct,
            ),
        );
    }

    private function getModuleCircleAttendance($companyId, $userId){
        global $callConfig;
        $month = date('m');
        $year = date('Y');
        $att = 0;
        $abs = 0;
        $total = 0;
        $attstc = "SELECT * FROM attendance_settings WHERE is_active=0 and company_id=".$companyId;
        $atsdata = $callConfig->getRow($attstc);
        $workingdays = json_decode($atsdata->working_days,1);
        $holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$companyId." AND MONTH(holiday_date) = '".$month."' AND YEAR(holiday_date) = '".$year."'";
        $holidaysdata = $callConfig->getAllRows($holidays); //holidays
        $holidaysdatas = [];
        foreach ($holidaysdata as $hvalue){
            $holidaysdatas[] = $hvalue->holiday_date;
        }
        $empattsql = "SELECT attendace_date FROM employee_daily_attendances WHERE is_active=0 and company_id=".$companyId." AND MONTH(attendace_date) = '".$month."' AND YEAR(attendace_date) = '".$year."' AND employee_id = (SELECT id FROM employees WHERE user_id=".$userId." AND is_active =0 ) ";
        $empattdata = $callConfig->getAllRows($empattsql); //holidays
        $attendace_dates = [];
        foreach ($empattdata as $attvalue){
            $attendace_dates[] = $attvalue->attendace_date;
        }
        $start_date = date('Y-m-01');
        $end_date = date(self::DATE_FORMAT_YMD);
        for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime(self::STR_PLUS_1_DAY, $i)) {
            $date = date(self::DATE_FORMAT_YMD,$i);
            $dy =  date('l',$i);
            if(!in_array($date,(array)$holidaysdatas) && (in_array($dy, (array)$workingdays))) {
                if(in_array($date,(array)$attendace_dates)){
                    $att = $att+1;
                }else{
                    $abs = $abs+1;
                }
                $total = $total+1;
            }
        }
        $attPct = $total != 0 ? round(($att/$total)*100) : 0;
        $absPct = $total != 0 ? round(($abs/$total)*100) : 0;
        return $this->buildCirclePair('Present Days', $att, $attPct, 'Absent  Days', $abs, $absPct);
    }

    private function getModuleCircleFieldWork($companyId, $userId){
        global $callConfig;
        $query = "SELECT id,user_field_work_type  FROM application_users WHERE id =".$userId;
        $user_data = $callConfig->getRow($query);
        if($user_data->user_field_work_type!=3){
            $ndate = date(self::DATE_FORMAT_YMD);
            $query = "SELECT count(id) as daily_plan FROM field_work_daily_plans WHERE company_id = ".$companyId.self::SQL_AND_USER_ID.$userId." AND plan_date = '".$ndate."' AND current_status=0 AND is_active = 0";
            $da_data = $callConfig->getRow($query);
            $query = "SELECT count(id) as work_plan FROM field_work_user_map_points WHERE company_id = ".$companyId." AND field_work_daily_plan_id IN (SELECT id FROM field_work_daily_plans WHERE company_id = ".$companyId.self::SQL_AND_USER_ID.$userId." AND plan_date = '".$ndate."' AND current_status=0 AND is_active = 0)  AND is_active = 0 ";
            $wk_data = $callConfig->getRow($query);
            $total = $da_data->daily_plan;
            $abs = $wk_data->work_plan;
            $att = $total- $abs;
            $visPct = $total >0 ? round(($abs/$total)*100):100;
            $penPct = $total >0 ? round(($att/$total)*100):0;
            return $this->buildCirclePair('Visited', $abs, $visPct, 'Pending', $att, $penPct);
        }
        $st = date('Y-m-01');
        $en = date('Y-m-t');
        $query = "SELECT COUNT(DISTINCT(map_date)) att_dal FROM field_work_user_map_points WHERE company_id =".$companyId." AND map_date >= '".$st."' AND map_date <= '".$en."' AND user_id = ".$userId.self::SQL_AND_IS_ACTIVE_0;
        $att_data = $callConfig->getRow($query);
        $total = date('t');
        $abs = $att_data->att_dal;
        $att = $total- $abs;
        return $this->buildCirclePair('Present Days', $abs, round(($abs/$total)*100), 'Absent  Days', $att, round(($att/$total)*100));
    }

    public function togetemloyeemodeleraiodcricle($get){
        global $callConfig;
        $chek = array('1'=>'quotations','2'=>'goods_receipt_notes','3'=>'sales_orders','26'=>'requisitions');
        $chek_mod = array('1','2','3','26');
        if(in_array($get[self::KEY_MODULE_ID],$chek_mod)){
            $query = "SELECT SUM(IF(response_type!=0,1,0)) approved, SUM(IF(response_type=0,1,0)) pending FROM transaction_approval_logs WHERE transaction_table IN ('".$chek[$get[self::KEY_MODULE_ID]]."') AND user_id = ".$get[self::KEY_USER_ID];
            $da_data = $callConfig->getRow($query);
            $att = $da_data->approved!="" ? $da_data->approved : 0;
            $abs = $da_data->pending!="" ? $da_data->pending : 0;
            $total = $att+$abs;
            $data = $this->buildCirclePair('Approved', $att, $total>0 ?round(($att/$total)*100):0, 'Processing', $abs, $total>0 ?round(($abs/$total)*100):0);
            $data['query'] = $query;
            return $data;
        }
        if($get[self::KEY_MODULE_ID]==7){
            return $this->getModuleCircleAttendance($get[self::KEY_COMPANY_ID], $get[self::KEY_USER_ID]);
        }
        if($get[self::KEY_MODULE_ID]==38){
            return $this->getModuleCircleFieldWork($get[self::KEY_COMPANY_ID], $get[self::KEY_USER_ID]);
        }
        if($get[self::KEY_MODULE_ID]==39){
            $query = "SELECT   d.user_id, COUNT(CASE WHEN ta.current_status = 4 THEN 1 END) AS complete, COUNT(CASE WHEN ta.current_status != 4 THEN 1 END) AS pending,COUNT(*) AS total_assignments FROM drivers d JOIN transport_assign ta ON d.id = ta.driver_id WHERE d.user_id =".$get[self::KEY_USER_ID]." AND ta.is_active = 0";
            $res= $callConfig->getRow($query);
            $total = $res->total_assignments;
            $abs = $res->complete;
            $att = $res->pending;
            return $this->buildCirclePair('Completed', $abs, $total >0 ? round(($abs/$total)*100):100, 'Pending', $att, $total != 0 ? round(($att/$total)*100) : 0);
        }
        if($get[self::KEY_MODULE_ID]==12){
            $query = "SELECT   ta.user_id, COUNT(CASE WHEN ta.current_status = 4 THEN 1 END) AS complete, COUNT(CASE WHEN ta.current_status != 4 THEN 1 END) AS pending,COUNT(*) AS total_assignments FROM  emp_expenses ta WHERE ta.company_id =".$get[self::KEY_COMPANY_ID]." AND ta.user_id =".$get[self::KEY_USER_ID]." AND ta.is_active = 0";
            $res= $callConfig->getRow($query);
            $total = $res->total_assignments;
            $abs = $res->complete;
            $att = $res->pending;
            return $this->buildCirclePair('Completed', $abs!=""?$abs:0, $total >0 ? round(($abs/$total)*100):100, 'Pending', $att!=""?$att:0, $total >0 ? round(($att/$total)*100):100);
        }
        return $this->buildCirclePair('Approved', 8, 73, 'Processing', 3, 27);
    }
     public function tocheckpermissiontoqrcodeapproval($get){
         global $callConfig;
         $list_status['genrate_qr_code'] = 'NO';
         $list_status['approval'] = 'NO';
             $roleshort = array('OFFICE_SECURITY_GUARD', 'OFFICE_USER');
             $query = "SELECT role_short_code FROM application_user_module_roles WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_USER_ID.$get[self::KEY_USER_ID].self::SQL_AND_IS_ACTIVE_EQ0;
             $data = $callConfig->getAllRows($query);
             $check = array();
             foreach($data as $da){
                 $check[] = $da->role_short_code;
                 if(!in_array($da->role_short_code,$roleshort)){
                     $list_status['genrate_qr_code'] = 'YES';
                     $list_status['approval'] = 'YES';
                 }
             }

         //}
         $return['data']=$list_status;
         return $return;
     }
     public function togetcomplientsbyiddetails($get){
         global $callConfig;
         $query = "SELECT * FROM complaints WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_USER_ID.$get[self::KEY_USER_ID].self::SQL_AND_ID.$get['complaint_id']."  AND is_active=0";
         $data = $callConfig->getRow($query);
         $return['data']=$data;
         $return['history'] = array();
         $query = "SELECT `transaction_date`, `complaint_image`, `remarks` FROM complaint_responses WHERE `company_id` = ".$get[self::KEY_COMPANY_ID]." AND complaint_id =".$get['complaint_id'].self::SQL_AND_IS_ACTIVE_0;
        $hdata = $callConfig->getAllRows($query);
        if(count((array)$hdata)>0){
            foreach((array)$hdata as $da){
                 $dat = (array)$da;
                 $return['history'][]=$dat;
             }
        }


         return $return;
     }
    public function todeletecomplaintbasedonid($get){
         global $callConfig;
         $upquery = "UPDATE complaints SET is_active = NULL, updated_by = ".$get[self::KEY_USER_ID]."  WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_ID.$get['complaint_id']."  AND is_active=0";
         return $callConfig->executeQuery($upquery);
     }
     public function toupdatecomplanatedetails($get, $post){
         global $callConfig;
         $complaint_image = '';
         $insert_array = array('complaint_name'=>$post['complaint_name'],'complaint_type'=>$post['complaint_type'], 'complaint_priority'=>$post['complaint_priority'],'building_id'=>$post['building_id'],'building_floor_id'=>$post['building_floor_id'],'building_floor_space_id'=>$post['building_floor_space_id'],self::KEY_ITEM_ID=>$post[self::KEY_ITEM_ID],'asset_id'=>$post['asset_id'],'complaint_image'=>$complaint_image, self::KEY_REMARKS=>$post[self::KEY_REMARKS],self::KEY_IS_ACTIVE=>0,self::KEY_CURRENT_STATUS=>0,self::KEY_UPDATED_BY=>$get[self::KEY_USER_ID]);
         $callConfig->updateRecord('complaints', $insert_array,'id',$get['complaint_id']);
        $ret['Update'] =$res;
        return $ret;
         //complaint_name, complaint_type, complaint_description, building_id, building_floor_id, building_floor_space_id, item_id, asset_id, complaint_image, remarks
     }
     public function toaddcomplaintshisorydetails($get, $post){
         global $callConfig;
         $complaint_image = '';
         if($post[self::KEY_REMARKS]!=""){
             $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],'complaint_id'=>$post['complaint_id'],self::KEY_USER_ID=>$get[self::KEY_USER_ID],self::KEY_TRANSACTION_DATE=>$post[self::KEY_TRANSACTION_DATE] ,'complaint_image'=>$complaint_image, self::KEY_REMARKS=>$post[self::KEY_REMARKS],self::KEY_IS_ACTIVE=>0,self::KEY_CURRENT_STATUS=>0,self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
             $insert = $callConfig->insertRecord('complaint_responses',$insert_array);
         }
         if($post['change_status']=='Y'){
             $callConfig->updateRecord('complaints', array(self::KEY_CURRENT_STATUS=>$post[self::KEY_CURRENT_STATUS],self::KEY_UPDATED_BY=>$get[self::KEY_USER_ID]),'id',$post['complaint_id']);
         }
        $ret[self::KEY_INSERT] =$insert;
        return $ret;
        //complaint_id,company_id, user_id, transaction_date, complaint_image, remarks, current_status,change_status
     }
     public function togetsalaryyearmonthlist($get){
         global $callConfig;
         $query = "SELECT pay_year, pay_month FROM pay_run_summary WHERE company_id=".$get[self::KEY_COMPANY_ID]." AND employee_id = ".$get[self::KEY_EMP_ID].self::SQL_AND_IS_ACTIVE_0;
         $datas = $callConfig->getAllRows($query);
         $months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
         $return['data'] = array();
         foreach((array)$datas as $da){
             $dat = (array)$da;
             $dat['msg'] = "Salary of ".$months[$da->pay_month]." ".$da->pay_year;
             $return['data'][]=$dat;
         }
         return $return;
     }
     public function togetsalarydetailsyearbasemonthlist($get){
         global $callConfig;

         $query ="SELECT id, emp_code, display_name, designation_id, (SELECT designation_name FROM designations WHERE id = designation_id) designation_name FROM employees WHERE company_id=".$get[self::KEY_COMPANY_ID]." AND id =".$get[self::KEY_EMP_ID];
         $emp_data = $callConfig->getRow($query);

        $whr1st="company_id= '".$get[self::KEY_COMPANY_ID]."' AND is_active = '0' and pay_month='".$get['month']."' and pay_year='".$get['year']."' and employee_id='".$get[self::KEY_EMP_ID]."' ";
        $query1st = $callConfig->selectQuery('pay_run_summary','*',$whr1st,'','','');
        $st1 = $callConfig->getRow($query1st);

        $query1stm= "SELECT * FROM pay_run_summary_components prsc, earnings ern WHERE prsc.pay_run_summary_id='".$st1->id."' AND prsc.component_id = ern.id AND ern.display_in_payslip = 0 ORDER BY ern.display_seq ASC";

        $stmp = $callConfig->getAllRows($query1stm);

        $earquery = $callConfig->selectQuery('earnings','id,name,payslip_name,code,display_in_payslip, calculation_mode','','','','');
        $earning = $callConfig->getAllRows($earquery);
        foreach ($earning as $earn) {
            $earns[$earn->id] = $earn->payslip_name;
            $allearns[$earn->id] = (array)$earn;
        }
        $actual_amount =0; $dedaction_amount = 0;
        $actual = array();
        $dedaction = array();
        foreach ($stmp as  $value) {
            if($allearns[$value->component_id]['display_in_payslip']==0){

                if($allearns[$value->component_id]['calculation_mode'] =='ADD'){
                    $actuals['name'] = $earns[$value->component_id];
                    $actuals['amount'] = $value->salary_component_actual;
                    $actuals['code'] = $allearns[$value->component_id]['code'];
                    $actual_amount = $actual_amount+$value->salary_component_actual;
                    $actual[] = $actuals;
                }
                if($allearns[$value->component_id]['calculation_mode'] =='MINUS'){
                    $dedactions['name'] = $earns[$value->component_id];
                    $dedactions['amount'] = $value->salary_component_actual;
                    $dedactions['code'] = $allearns[$value->component_id]['code'];

                    $dedaction_amount = $dedaction_amount+$value->salary_component_actual;
                    $dedaction[] = $dedactions;
                }
            }
        }
        $data['emp_data'] = $emp_data;
        $data['earning'] = $actual;
        $data['dedaction'] = $dedaction;
        $data['earning_amount'] = number_format($actual_amount,0);
        $data['dedaction_amount'] = number_format($dedaction_amount,0);
        $data['net'] = number_format( $actual_amount-$dedaction_amount,0);

         return $data;
     }
     public function mobileuserregistation($get, $post){
         global $callConfig;

         $otp = $this->generateOTP(4);
         $insert_array = array('user_name'=>$post['user_name'],self::KEY_USER_EMAIL=>$post[self::KEY_USER_EMAIL],'user_mobile'=>$post['user_mobile'] ,'user_password'=>$callConfig->passwordEncrypt($post['user_password']), 'remember_token'=>$otp, self::KEY_IS_ACTIVE=>0,self::KEY_CURRENT_STATUS=>0);
         $insert = $callConfig->insertRecord(MAINDB.'.mobile_user_registrations',$insert_array);
         $insert['token'] = $otp;
         if($insert[self::KEY_STATUS] == 'sucess'){
             $insert[self::KEY_STATUS]= self::KEY_SUCCESS;
         }
         // user_name, user_email, user_mobile, user_password
         return $insert;
     }
     public function mobileregistationcheckotp($get){
         global $callConfig;
         $query = "SELECT id FROM ".MAINDB.".mobile_user_registrations WHERE id=".$get[self::KEY_USER_ID]." AND remember_token='".$get['otp'].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
         $data = $callConfig->getRow($query);
         $return[self::KEY_STATUS] = 'fail';
         if($data->id!=""){
             $return[self::KEY_STATUS] = self::KEY_SUCCESS;
         }
         return $return;
     }
     public function toaddmobfielddailyplans($get, $post){
         global $callConfig;
         $msg = "The selected time slot is already occupied by another scheduled task. Please choose a different time or reschedule the existing task to avoid conflicts";
         $plan_date = str_replace('/"', '-', $post[self::KEY_PLAN_DATE]);
        $plan_date =  date(self::DATE_FORMAT_YMD, strtotime($plan_date));
         $check =1;
         if(isset($post['force']) && $post['force']==1){
             $check =0;
         }else{
             $query = "SELECT COUNT(id) cc FROM field_work_daily_plans WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND user_id =".$get[self::KEY_USER_ID]." AND plan_date = '".$plan_date."' AND plan_time = '".$post['plan_time']."' AND is_active=0";
             $ccdata = $callConfig->getRow($query);
             if($ccdata->cc >0){
                 $check =1;
             }else{
                 $check =0;
             }
         }
         if($check ==0){
             $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],'contact_person'=> $post['contact_person'], 'mobile'=> $post['mobile'], self::KEY_USER_ID=>$post[self::KEY_USER_ID], self::KEY_PLAN_DATE=>$plan_date, 'plan_time'=>$post['plan_time'], 'plan_longitude'=>$post['plan_longitude'], 'plan_latitude'=>$post['plan_latitude'], 'plan_distance'=>$post['plan_distance'], 'plan_location'=>$post['plan_location'], 'plan_address'=>$post['plan_address'], self::KEY_CURRENT_STATUS=>'0',self::KEY_IS_ACTIVE=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
            $insert = $callConfig->insertRecord('field_work_daily_plans',$insert_array);
            //`user_id`, `plan_date`, `plan_time`, `plan_longitude`, `plan_latitude`, `plan_distance`, `plan_location`, `plan_address`
            if($insert[self::KEY_STATUS] == 'sucess'){
                 $insert[self::KEY_STATUS]= self::KEY_SUCCESS;
             }
        }else{
            $insert[self::KEY_STATUS]= 'fail';
            $insert[self::KEY_MESSAGE]= $msg;
        }
         return $insert;
     }
     public function togetmobfielddailyplans($get){
         global $callConfig;
         $query = "SELECT id, user_id, contact_person,  mobile, plan_date, plan_time, plan_longitude, plan_latitude, plan_distance, plan_location, plan_address, plan_type, current_status    FROM field_work_daily_plans WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND user_id =".$get[self::KEY_USER_ID]." AND plan_date='".$get[self::KEY_PLAN_DATE].self::SQL_QUOTE_AND_IS_ACTIVE_0;
         $datas = $callConfig->getAllRows($query);

          $ddquery = "SELECT `company_id`, `map_title`, phone as mobile, address as plan_address, `map_date`, `map_time`, `map_longitude` as plan_longitude, `map_latitude` as  plan_latitude, `map_distance`,  `comments`, `field_work_daily_plan_id` , status_radius,  remarks, current_status  FROM field_work_user_map_points WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND user_id =".$get[self::KEY_USER_ID]." AND map_date='".$get[self::KEY_PLAN_DATE].self::SQL_QUOTE_AND_IS_ACTIVE_0;
         $wrdatas = $callConfig->getAllRows($ddquery);

         $workdd = array();
         $nonworkdd = array();
         foreach($wrdatas as $wrdat){
             if($wrdat->field_work_daily_plan_id!=""){
                 $workdd[$wrdat->field_work_daily_plan_id] = (array)$wrdat;
             }else{
                 $nonworkdd[] = (array)$wrdat;
             }

         }
         foreach((array)$datas as $da){
             $dat = (array)$da;
             $dat['check_status'] = 'N';
             if(isset($workdd[$da->id])){
                 $dat['check_status'] = 'Y';
                 $dat['client_name'] = $workdd[$da->id]['map_title'];
                 $dat['work_distance'] = $workdd[$da->id]['map_distance'];
                 $dat[self::KEY_REMARKS] = $workdd[$da->id][self::KEY_REMARKS];
                 $dat['status_radius'] = $workdd[$da->id]['status_radius'];
                 $dat['field_work_daily_plan_id'] = $workdd[$da->id]['field_work_daily_plan_id'];
                 $dat[self::KEY_CURRENT_STATUS] = $workdd[$da->id][self::KEY_CURRENT_STATUS];
             }
             $return['data'][]=$dat;
         }
         if(count((array)$nonworkdd)>0){
             foreach((array)$nonworkdd as $nonworkd){

                 $return['data'][]=$nonworkd;
             }
         }
         $return['radius']=WORK_RAIDUS;
         $query = "SELECT sp.status, sp.name, st.background_color, st.color FROM status_progress sp, status st WHERE sp.company_id = st.company_id AND sp.company_id =1 AND sp.status = st.name_value AND sp.table_name = 'field_work_daily_plans' ORDER BY sp.display_priority ASC";
        $status = $callConfig->getAllRows($query);
        $return[self::KEY_STATUS] = $status;

         return $return;
     }
     public function toaddmobfieldwork($get, $post){
         global $callConfig;
        $work_date =  date(self::DATE_FORMAT_YMD, strtotime($work_date));
         $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_USER_ID=>$post[self::KEY_USER_ID],'client_name'=>$post['client_name'], 'work_date'=>$work_date, 'work_time'=>$post['work_time'], 'work_longitude'=>$post['work_longitude'], 'work_latitude'=>$post['work_latitude'], 'work_location'=>$post['work_location'], 'work_address'=>$post['work_address'],'comments'=>$post['comments'],'field_work_daily_plan_id'=>$post['field_work_daily_plan_id'], self::KEY_CURRENT_STATUS=>'0',self::KEY_IS_ACTIVE=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
        $insert = $callConfig->insertRecord('field_work_details',$insert_array);
        // `company_id`, `user_id`, `client_name`, `work_date`, `work_time`, `work_longitude`, `work_latitude`, `work_distance`, `work_location`, `work_address`, `comments`, `field_work_daily_plan_id`
        if($insert[self::KEY_STATUS] == 'sucess'){
             $insert[self::KEY_STATUS]= self::KEY_SUCCESS;
         }
         return $insert;
     }
     public function togetmobfieldworkdetails($get){
         global $callConfig;
         $query = "SELECT `company_id`, `user_id`, `client_name`, `work_date`, `work_time`, `work_longitude`, `work_latitude`, `work_distance`, `work_location`, `work_address`, `comments`, `field_work_daily_plan_id`   FROM field_work_details WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND user_id =".$get[self::KEY_USER_ID]." AND work_date=".$get['work_date'].self::SQL_AND_IS_ACTIVE_0;
         $datas = $callConfig->getAllRows($query);
         foreach((array)$datas as $da){
             $dat = (array)$da;
             $return['data'][]=$dat;
         }
         return $return;
     }
     public function togetmobmanagerfieldworkdetails($get){
         global $callConfig;
         $query = "SELECT id, display_name, user_email, user_mobile, user_photo FROM application_users WHERE company_id = 1 AND reports_to = ".$get[self::KEY_USER_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
         $user_datas = $callConfig->getAllRows($query);
         if(count((array)$user_datas)>0){
             $user_ids = array();
             foreach((array)$user_datas as $user_dat){
                 $userda[$user_dat->id] = (array)$user_dat;
                 $userda[$user_dat->id]['child'] = array();
                 $user_ids[] = $user_dat->id;
             }
             $query = "SELECT `company_id`, `user_id`, `client_name`, `work_date`, `work_time`, `work_longitude`, `work_latitude`, `work_distance`, `work_location`, `work_address`, `comments`, `field_work_daily_plan_id`   FROM field_work_details WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND user_id IN (".implode(",",$user_ids).") AND work_date='".$get['work_date']."' AND is_active = 0 ORDER BY id DESC";
             $datas = $callConfig->getAllRows($query);
             foreach((array)$datas as $da){
                 $dat = (array)$da;
                 $userda[$da->user_id]['child'][]=$dat;
             }
         }
         foreach($userda as $dat){
             $return['data'][] = $dat;
         }
         return $return;
     }

     public function toinactiveasedontableid($get){
         global $callConfig;
         $upquery = "UPDATE ".$get[self::KEY_TABLE]." SET is_active = 1 WHERE company_id=".$get[self::KEY_COMPANY_ID].self::SQL_AND_ID.$get[self::KEY_REF_ID];
         return $callConfig->executeQuery($upquery);
     }

     public function toupdatedynamicfielddata($get,$post){
         global $callConfig;
         $table = $get[self::KEY_TABLE];
         $value = $post[self::KEY_REF_ID];
         unset($post[self::KEY_REF_ID]);
         if( isset($post['update_type']) && $post['update_type']!='' && $get[self::KEY_TABLE] = 'field_work_daily_plans' ){
             $query = " SELECT * FROM field_work_daily_plans WHERE id=".$value;
             $feilddata =  $callConfig->getRow($query);
             $user_id = $post[self::KEY_USER_ID] !="" ? $post[self::KEY_USER_ID] : $feilddata->user_id;
             $plan_date = $post[self::KEY_PLAN_DATE] !="" ? $post[self::KEY_PLAN_DATE] : $feilddata->plan_date;
             $plan_time = $post['plan_time'] !="" ? $post['plan_time'] : $feilddata->plan_time;
             $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], self::KEY_USER_ID=>$user_id, 'contact_person'=>$feilddata->contact_person, 'mobile'=>$feilddata->mobile, self::KEY_PLAN_DATE=>$plan_date, 'plan_time'=>$plan_time, 'plan_longitude'=>$feilddata->plan_longitude, 'plan_latitude'=>$feilddata->plan_latitude, 'plan_distance'=>$feilddata->plan_distance, 'plan_location'=>$feilddata->plan_location, 'plan_address'=>$feilddata->plan_address,  self::KEY_IS_ACTIVE=>'0', self::KEY_CURRENT_STATUS=>'0',self::KEY_CREATED_DATE=>date(self::DATETIME_FORMAT_YMD_HIS), self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
             $callConfig->insertRecord('field_work_daily_plans',$insert_array);
             $uparray = array('comments'=>$post['comments'], 'update_type'=>$post['update_type'], self::KEY_REF_ID=>$res[self::KEY_LASTID]);
             if($post['update_type']=='REASSIGN'){
                 $query = "SELECT display_name FROM application_users WHERE id=".$user_id;
                 $userdata =  $callConfig->getRow($query);
                 $uparray['re_value'] =$userdata->display_name;
                 $uparray[self::KEY_CURRENT_STATUS] ='28';
             }
             if($post['update_type']=='RESCHEDULE'){
                 $uparray['re_value'] = $plan_date." ".$plan_time;
                 $uparray[self::KEY_CURRENT_STATUS] ='25';
             }
             $callConfig->updateRecord($table,$uparray,'id',$value);
         }else{
             $callConfig->updateRecord($table,$post,'id',$value);
         }
         return $res;
     }
     public function tocheckuserfieldwortype($get){
         global $callConfig;
         //user_field_work_details
         // 1: Daily Plan 2 : Daily Work 3: security
         $get[self::KEY_PLAN_DATE] = $get[self::KEY_PLAN_DATE]!=""? $get[self::KEY_PLAN_DATE] :date(self::DATE_FORMAT_YMD);
         $data = array('todays_plan'=>'Y', 'schedule_work'=>'N','manager'=>'N','report'=>'Y');
         $query = "SELECT id, user_field_work_type  FROM application_users WHERE company_id=".$get[self::KEY_COMPANY_ID].self::SQL_AND_ID.$get[self::KEY_USER_ID];
         $user_data  = $callConfig->getRow($query);
         $data['field_map_auto_check_in_out'] = FIELD_AUTO_CHECK;
         $data['user_field_work_type'] = $user_data->user_field_work_type;
         if($user_data->user_field_work_type==1){
             $data['schedule_work'] = 'Y';
         }
         if($user_data->user_field_work_type==2){
             $data['schedule_work'] = 'N';
         }
         if($user_data->user_field_work_type==3){
             $data['schedule_work'] = 'N';
             $data['report'] = 'N';
             $query = " SELECT * FROM user_field_work_details WHERE company_id=".$get[self::KEY_COMPANY_ID]." AND user_id=".$get[self::KEY_USER_ID]." AND work_from_date <='".$get[self::KEY_PLAN_DATE]."' AND work_to_date >='".$get[self::KEY_PLAN_DATE].self::SQL_QUOTE_AND_IS_ACTIVE_0;
             $feilddata =  $callConfig->getRow($query);
             $data['user_data'] = (array)$feilddata;
         }
         $return['data'] = $data;
         return $return;
     }
     public function togetworkfielddataperdate($get){
         global $callConfig;
         $query = "SELECT count(id) count_dp FROM `field_work_daily_plans` WHERE company_id=".$get[self::KEY_COMPANY_ID]." AND plan_date = '".$get[self::KEY_PLAN_DATE]."' AND is_active=0";
         $dp_res = $callConfig->getRow($query);
         $count_dp = $dp_res->count_dp;
         $query = "SELECT count(id) count_dw, sum(IF(field_work_daily_plan_id!='',0,1)) count_dwp, sum(work_distance) work_distance  FROM `field_work_details` WHERE  company_id=".$get[self::KEY_COMPANY_ID]." AND work_date = '".$get[self::KEY_PLAN_DATE]."' AND is_active=0";
         $dw_res = $callConfig->getRow($query);
         $return['date']=$get[self::KEY_PLAN_DATE];
         $return['attend']=$dw_res->count_dw;
         $return['pending']=$count_dp>0 ? $count_dp-$dw_res->count_dwp:0;
         $return['distance']=$dw_res->work_distance;
         return $return;
     }
     public function togetworkfielddatapermonth($get){
         global $callConfig;
         $formdate = date('Y-m-01',strtotime($get[self::KEY_PLAN_DATE]));
         $todate = date('Y-m-t',strtotime($get[self::KEY_PLAN_DATE]));
         $query = "SELECT plan_date,count(id) count_dp FROM field_work_daily_plans WHERE company_id=".$get[self::KEY_COMPANY_ID]." AND user_id =".$get[self::KEY_USER_ID]." AND plan_date >= '".$formdate."' AND plan_date <= '".$todate."' AND current_status =0 AND is_active=0 GROUP BY plan_date";
         $dp_res = $callConfig->getAllRows($query);
         foreach((array)$dp_res as $dp_re ){
             $fwda[$dp_re->plan_date] = $dp_re->count_dp;
         }

         $query = "SELECT map_date as work_date, count(id) count_dw, sum(IF(field_work_daily_plan_id!='',0,1)) count_dwp, sum(map_distance) work_distance, status_radius,  min(if(map_title='Check-In',`map_time`, Null)) as clock_in, MAX(if(map_title='Check-Out',`map_time`, Null)) as clock_out FROM field_work_user_map_points WHERE company_id=".$get[self::KEY_COMPANY_ID]." AND user_id =".$get[self::KEY_USER_ID]." AND map_date >= '".$formdate."' AND map_date <= '".$todate."' AND is_active=0 GROUP BY map_date";
         $dw_ress = $callConfig->getAllRows($query);
         foreach($dw_ress as $dw_res){
             $return = array();
             $count_dp = isset($fwda[$dw_res->work_date]) ? $fwda[$dw_res->work_date] :0;
             $return['date']=$dw_res->work_date;
             $return['attend']=$dw_res->count_dw;
             $return['pending']= ( $count_dp-$dw_res->count_dwp)>0 ? $count_dp-$dw_res->count_dwp:0;
             $return['distance']=$dw_res->work_distance;
             $return['status_radius']=$dw_res->status_radius;
             $return[self::KEY_CLOCK_IN]=$dw_res->clock_in;
             $return[self::KEY_CLOCK_OUT]=$dw_res->clock_out;
             $data['data'][]=$return;
         }
         return $data;
     }
     public function toaddmobilehelpdetails($get, $post){
        global $callConfig;
        $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_USER_ID=>$get[self::KEY_USER_ID],'name'=>$post['name'], 'email'=>$post['email'], 'phone'=>$post['phone'], self::KEY_MESSAGE=>$post[self::KEY_MESSAGE],self::KEY_IS_ACTIVE=>0,self::KEY_CURRENT_STATUS=>0,self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
        // post : name, email, phone, message : get : company_id , user_id
        return $callConfig->insertRecord('mobile_help',$insert_array);
    }
    public function tomapouttimeallcase($get){
         global $callConfig;
         $upquery = "UPDATE field_work_user_map_points SET map_out_time = '".$get['map_out_time']."' WHERE company_id=".$get[self::KEY_COMPANY_ID].self::SQL_AND_ID.$get[self::KEY_REF_ID];
         return $callConfig->executeQuery($upquery);
     }
     public function togetsecurtydetails($get){
        global $callConfig;
        $return = array();
        $userid = $get[self::KEY_USER_ID];
        $map_date = $get[self::KEY_MAP_DATE] != "" ? $get[self::KEY_MAP_DATE] : date(self::DATE_FORMAT_YMD);
        $sev_map_date = date(self::DATE_FORMAT_YMD, strtotime('-7 day', strtotime($map_date)));
        $query = "SELECT user_id, map_date, min(`map_time`) clock_in, MAX(`map_time`) clock_out,  map_latitude, map_longitude  FROM `field_work_user_map_points` WHERE company_id =  ".$get[self::KEY_COMPANY_ID]."  AND `user_id` = ".$userid." AND `map_date`>= '".$sev_map_date."' AND `map_date`<= '".$map_date."' AND is_active =0 GROUP BY map_date";
        $data = $callConfig->getAllRows($query);
        $return['data'] = $data;
        return $return;
    }
    public function togetuserfieldworkdata($get){
        global $callConfig;
        $return = array();
        $userid = $get[self::KEY_USER_ID];
         $map_date = $get[self::KEY_MAP_DATE];
        $query = "SELECT user_id, map_date, min(if(map_title='Check-In',`map_time`, Null)) as clock_in, MAX(if(map_title='Check-Out',`map_time`, Null)) as clock_out, map_latitude, map_longitude FROM `field_work_user_map_points` WHERE company_id =  ".$get[self::KEY_COMPANY_ID]."  AND `user_id` = ".$userid." AND  `map_date`= '".$map_date."' AND is_active =0 ";
        $data = $callConfig->getRow($query);
        $return['data'] = array();
        if($data->user_id!=""){
            $return['data'] = $data;
        }

        return $return;
    }
    public function togetlistfiledworkdatabasedate($get){
        global $callConfig;
        $return = array();
        $userid = $get[self::KEY_USER_ID];
        $map_date = $get[self::KEY_MAP_DATE];
        $query = "SELECT au.display_name as user_name, fp.* FROM field_work_daily_plans fp, application_users au WHERE fp.company_id = au.company_id AND fp.company_id = ".$get[self::KEY_COMPANY_ID]." AND fp.user_id = au.id AND fp.user_id =".$userid."  AND fp.plan_date = '".$map_date."' AND fp.is_active =0";

        $datas = $callConfig->getAllRows($query);
        $ddquery = "SELECT `company_id`, `map_title`, `map_date`, `map_time`, `map_longitude` as plan_longitude, `map_latitude` as  plan_latitude, `map_distance`,  `comments`, `field_work_daily_plan_id` , status_radius,  remarks, current_status  FROM field_work_user_map_points WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND user_id =".$userid." AND map_date='".$map_date.self::SQL_QUOTE_AND_IS_ACTIVE_0;
         $wrdatas = $callConfig->getAllRows($ddquery);
         $workdd = array();
         $nonworkdd = array();
         foreach((array)$wrdatas as $wrdat){
             if($wrdat->field_work_daily_plan_id!=""){
                 $workdd[$wrdat->field_work_daily_plan_id] = (array)$wrdat;
             }else{
                 $nonworkdd[] = (array)$wrdat;
             }

         }
         foreach((array)$datas as $da){

             $dat = (array)$da;
             if(isset($workdd[$da->id])){

                 $dat[self::KEY_CURRENT_STATUS] = $workdd[$dat['id']][self::KEY_CURRENT_STATUS];
             }
             $return['data'][]=$dat;
         }
        $query = "SELECT sp.status, sp.name, st.background_color, st.color FROM status_progress sp, status st WHERE sp.company_id = st.company_id AND sp.company_id =1 AND sp.status = st.name_value AND sp.table_name = 'field_work_daily_plans' ORDER BY sp.display_priority ASC";
        $status = $callConfig->getAllRows($query);
        $return[self::KEY_STATUS] = $status;
        return $return;
    }
    public function togetholidayslistbaseyear($get){
        global $callConfig;
        $year = $get['year']!="" ? $get['year'] : date('Y');

        $query = "SELECT holiday_year, holiday_date, holiday_name FROM holidays WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND holiday_year = '".$year.self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $data = $callConfig->getAllRows($query);
        $return['data'] = $data;
        return $return;
    }
    public function togetweekdayslist($get){
        global $callConfig;
        $attquery = "SELECT id, working_days FROM attendance_settings WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $att_data = $callConfig->getRow($attquery);
        $return['data'] = json_decode($att_data->working_days,1);
        return $return;
    }
    public function togetfieldworkdailyplansfrmobile($get){
        global $callConfig;
        $query = "SELECT
                  au.id,
                  au.display_name,
                  au.user_email,
                  au.user_field_work_type,
                  '".$get['date']."' AS map_date,
                  count(
                    DISTINCT(fwdp.id)
                  ) AS total_plan,
                  count(
                    DISTINCT(fwump.id)
                  ) AS total_work,
                  SUM(
                    IF(
                      fwump.field_work_daily_plan_id = fwdp.id,
                      1, 0
                    )
                  ) AS visited,
                  count(
                    DISTINCT(fwump.id)
                  ) - SUM(
                    IF(
                      fwump.field_work_daily_plan_id = fwdp.id,
                      1, 0
                    )
                  ) AS extra,
                  count(
                    DISTINCT(fwdp.id)
                  ) - SUM(
                    IF(
                      fwump.field_work_daily_plan_id = fwdp.id,
                      1, 0
                    )
                  ) AS pending
                FROM
                field_work_daily_plans fwdp


                  LEFT JOIN  field_work_user_map_points fwump ON fwdp.user_id = fwump.user_id
                  AND fwdp.plan_date = fwump.map_date
                  AND fwdp.company_id = fwump.company_id
                  AND fwdp.is_active = '0'
                  LEFT JOIN application_users au ON ( fwdp.user_id = au.id OR  fwump.user_id = au.id)
                WHERE
                  fwdp.company_id = '" . $get[self::KEY_COMPANY_ID] . "'
                  AND fwdp.user_id = '".$get[self::KEY_USER_ID]."'
                  AND fwdp.plan_date = '".$get['date']."'
                  AND fwdp.is_active = '0'
                GROUP BY
                  fwump.user_id,
                  fwump.map_date
                ORDER BY
                  fwump.map_date";
        $data = $callConfig->getAllRows($query);
        $return['data'] = null;
        if(count((array)$data)>0){
            $return['data'] = $data;
        }

        return $return;
    }
    public function togetgeolocationresaonlist($get){
        global $callConfig;
        $query = "SELECT id, genc_detail_code, genc_detail_name FROM general_code_details where is_active='0' and company_id='".$get[self::KEY_COMPANY_ID]."' and genc_code='GEOLOCATIONREASON' order by id ASC";
        $display_details = $callConfig->getAllRows($query);
        $data['data'] = $display_details;
        return $data;
    }
    public function togetuserslistformobile($get){
        global $callConfig;
        $query = "SELECT id, display_name FROM application_users WHERE company_id=".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $data = $callConfig->getAllRows($query);
        $return['data'] = $data;
        return $return;
    }
    public function togetuserssecurtydaydetailswithhistory($get){
        global $callConfig;

        $userid = $get[self::KEY_USER_ID];
        $map_date = $get[self::KEY_MAP_DATE];
        $query = "SELECT  `map_title`, `map_date` as work_date , `map_time` as clock_in, Null as clock_out ,`map_longitude` as check_in_longitude, `map_latitude` as  check_in_latitude, `map_distance`,  `comments`, `field_work_daily_plan_id` , status_radius,  remarks, current_status  FROM field_work_user_map_points WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND user_id =".$userid." AND map_date='".$map_date."' AND is_active = 0 ORDER BY map_time ASC";
        $datas = $callConfig->getAllRows($query);
        $i=0;
        foreach((array)$datas as $dat){
            if( $dat->map_title== 'Check-In'){
                $da = (array)$dat;
                unset($da['map_title']);
                $da['check_out_longitude']=null;
                $da['check_out_latitude']=null;
                $data[$i]=$da;
            }else{
                $data[$i][self::KEY_CLOCK_OUT]=$dat->clock_in;
                $data[$i]['check_out_longitude']=$dat->check_in_longitude;
                $data[$i]['check_out_latitude']=$dat->check_in_latitude;
                $i++;
            }
        }
        if(count((array)$data)>0){
            $res=$data[$i];
            $res['details'] = $data;
        }
        $return['data'] = $res;
        return $return;
    }
    public function dynamicinsertformobile($get, $post){
        global $callConfig;
        $post[self::KEY_COMPANY_ID] = $get[self::KEY_COMPANY_ID];
        $post[self::KEY_IS_ACTIVE] = '0';
        $post[self::KEY_CURRENT_STATUS] = '0';
        $post[self::KEY_CREATED_BY] = $get[self::KEY_USER_ID];
        $post[self::KEY_CREATED_DATE] = date(self::DATETIME_FORMAT_YMD_HIS);
        return $callConfig->insertRecord($get[self::KEY_TABLE],$post);
    }
    public function togetpostvaldynmicdetials($get, $post){
        global $callConfig;
        $where = " company_id =".$get[self::KEY_COMPANY_ID];
        if(count((array)$post)>0){

            foreach ($post as $key => $value) {
                $where.= " AND ".$key." = '".$value."'";
            }
            $where = trim($where,'AND ');
        }
        $where.= self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $query = $callConfig->selectQuery($get[self::KEY_TABLE],'*',$where,'','','');
        return $callConfig->getAllRows($query);
    }
    public function togetrelatedworkusermapointmob($get){
        global $callConfig;
        $query="SELECT map_title, phone, address, map_latitude, map_longitude, map_date FROM `field_work_user_map_points` WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_USER_ID.$get[self::KEY_USER_ID]." AND is_active =0 GROUP BY phone";
        return $callConfig->getAllRows($query);
    }
    public function dyanamicgenccodedetailformobile($get){
        global $callConfig;
        $res = array();
        if($get['genc_code']!=""){
            $query="SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND genc_code = '".$get['genc_code'].self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
            $res = $callConfig->getAllRows($query);
        }
        return array('data' => $res);
    }

    public function togetproductformitemonmobile($get){
        global $callConfig;
        $query="SELECT id, item_name, item_description, sale_price, item_image FROM `items` WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $res = $callConfig->getAllRows($query);
        return array('data' => $res);
    }
    public function togetproductcustomeronmobile($get){
        global $callConfig;
        $query="SELECT id, display_name, phone, email, billing_address, map_latitude, map_longitude  FROM customers WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $res = $callConfig->getAllRows($query);
        return array('data' => $res);
    }
    public function toinsertpurchaseorderonmobile($get,$post){
        global $callConfig;
        $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], 'data'=> json_encode($post), 'get_data'=>json_encode($get),self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
        $callConfig->insertRecord('field_mobile_orders',$insert_array);
        return $this->toinsertpurchaseordermobile($get,$post);
    }
    private function resolveOrCreatePurchaseOrderCustomer($companyId, $userId, $post){
        global $callConfig;
        $customer_id = $post['customer_id'];
        if($customer_id=='' && $post['customer_ref_id']!=""){
            $quer = "SELECT * FROM field_work_daily_plans WHERE id=".$post['customer_ref_id'];
            $cuser = $callConfig->getRow($quer);
            $insert_array = array(self::KEY_COMPANY_ID=>$companyId, 'title'=>$cuser->contact_person, 'display_name'=>$cuser->contact_person, 'phone'=>$cuser->mobile, 'billing_address'=>$cuser->plan_address, 'customer_longitude'=>$cuser->plan_longitude, 'customer_latitude'=>$cuser->plan_latitude, self::KEY_CURRENT_STATUS=>'0', self::KEY_IS_ACTIVE=>'0', self::KEY_CREATED_BY=>$userId);
            $cus_res=$callConfig->insertRecord('customers',$insert_array);
            $customer_id = $cus_res[self::KEY_LASTID];
        }
        $customer_userssql = "SELECT id, display_name, email, customer_tax_reg_no, phone, billing_address, shipping_address  FROM customers WHERE company_id = '".$companyId."' AND id = '".$customer_id.self::SQL_QUOTE_AND_IS_ACTIVE_0;
        return [$customer_id, $callConfig->getRow($customer_userssql)];
    }

    private function calculatePurchaseOrderTotals($items, $entries){
        $maintotal_amount = $mainamount = $maintax_amount_total = 0;
        foreach($entries as $itmss){
            $pur = $items[$itmss[self::KEY_ITEM_ID]];
            $tax_percentage = $pur->gst;
            if($tax_percentage !=""){
                $tax_amount = $pur->sale_price*($tax_percentage/100);
                $maintax_amount_total +=$tax_amount;
                $maintotal_amount += $pur->sale_price+$tax_amount;
            }else{
                $maintotal_amount += $pur->sale_price;
            }
            $mainamount +=$pur->sale_price;
        }
        return [$mainamount, $maintax_amount_total, $maintotal_amount];
    }

    private function buildPurchaseOrderItemsAndTaxes($companyId, $transactionId, $taxState, $items, $entries){
        $transactionitemsql = [];
        $transactionitemtaxary = [];
        $total_amount = 0;
        foreach($entries as $itementry){
            $tax_amount = 0;
            $pur = $items[$itementry[self::KEY_ITEM_ID]];
            $item_total_amount = $pur->sale_price*$itementry['qty'];
            if(isset($items[$itementry[self::KEY_ITEM_ID]])){
                $item_id = $pur->id;
                $account_code = $pur->inventory_asset_account;
                $item_name = $pur->item_name;
                $description = $pur->item_description;
                $purchase_uom = $pur->purchase_uom;
                $tax_percentage = $pur->gst;
                if($tax_percentage !=""){
                    $tax_amount = $item_total_amount*($tax_percentage/100);
                    $total_amount = $total_amount+$tax_amount;
                    $transactionitemtaxary[] = array(self::KEY_COMPANY_ID=>$companyId, self::KEY_TRANSACTION_ID=>$transactionId, 'transaction_item_id'=>$item_id, 'tax_name'=>$pur->code, 'tax_value'=>$tax_percentage, 'tax_id'=>$pur->hsn_and_sac_code_id, 'amount'=>$item_total_amount, self::KEY_TAX_AMOUNT=>$tax_amount, self::KEY_TOTAL_AMOUNT=>$total_amount, 'tax_type'=>$taxState, 'tax_split_details'=>'', self::KEY_IS_ACTIVE=>0, self::KEY_CURRENT_STATUS=>0, self::KEY_CREATED_DATE=>date(self::DATETIME_FORMAT_YMD_HIS), self::KEY_CREATED_BY=>0);
                }
            }
            $transactionitemsql[] = array(self::KEY_COMPANY_ID=>$companyId, self::KEY_TRANSACTION_ID=>$transactionId, self::KEY_ITEM_ID=>$item_id, self::KEY_ACCOUNT_CODE=>$account_code, 'item_name'=>$item_name, 'description'=>$description, 'purchase_uom'=>$purchase_uom, 'item_quantity'=>$itementry['qty'], 'item_price'=>$pur->sale_price, 'item_amount'=>$item_total_amount, self::KEY_TAX_AMOUNT=>$tax_amount, 'discount_amount'=>'0', self::KEY_TOTAL_AMOUNT=>$total_amount, 'reason_id'=>'', 'item_st_cost'=>'0','item_fifo_cost'=>'0', 'item_lifo_cost'=>'0', 'item_wa_cost'=>'0', 'approved_quantity'=>$itementry['qty'], 'transaction_type'=>'', 'transaction_qty'=>$itementry['qty'], 'pending_qty'=>$itementry['qty'], 'item_details'=>'', 'tax_deducted_source_id'=>'', 'tds_amount'=>'0', self::KEY_IS_ACTIVE=>'0', self::KEY_CREATED_DATE=>date(self::DATETIME_FORMAT_YMD_HIS), self::KEY_CREATED_BY=>'1');
        }
        return [$transactionitemsql, $transactionitemtaxary];
    }

    public function toinsertpurchaseordermobile($get,$post){
        global $callConfig;

        $item_ids = [];
        foreach($post['data'] as $item){
            $item_ids[] = $item[self::KEY_ITEM_ID];
        }
        $company_id = $get[self::KEY_COMPANY_ID];
        $application_table = 'sales_orders';
        $application_userssql = "SELECT id,display_name, cost_center, location_id, default_warehouse_id, work_station_id, country_id, default_state_id, default_currency_id FROM application_users WHERE company_id = '".$company_id."' AND id = '".$get[self::KEY_USER_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $application_usersres =  $callConfig->getRow($application_userssql);

        list($customer_id, $customer) = $this->resolveOrCreatePurchaseOrderCustomer($company_id, $get[self::KEY_USER_ID], $post);

        $dmtsql = "SELECT id, page_title, application_table_name, display_table, item_table, tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND application_table_name = '".$application_table."' AND item_table !='' AND tax_table !='' AND is_active = 0";
        $dmtres=$callConfig->getRow($dmtsql);
        $itemssql = "SELECT itm.id, itm.sku, itm.item_name, itm.item_description, itm.purchase_uom, itm.sale_uom, itm.sale_price, itm.purchase_price ,itm.hsn_and_sac_code_id, itm.inventory_asset_account, hsnsac.gst, hsnsac.code, hsnsac.tax_authority_id FROM items itm LEFT JOIN hsn_and_sac_codes hsnsac ON itm.company_id = hsnsac.company_id AND itm.hsn_and_sac_code_id = hsnsac.id AND itm.is_active = hsnsac.is_active WHERE itm.company_id='".$company_id."' AND itm.id IN (".implode(",",$item_ids).") AND itm.is_active = '0'";
        $itemsres = $callConfig->getAllRows($itemssql);
        $items = [];
        foreach($itemsres as $itmre){
            $items[$itmre->id] =  $itmre; //marge two table data
        }
        $originator_currency_id = $application_usersres->default_currency_id;
        $originator_state_id = $application_usersres->default_state_id;
        $currency_id = "";
        list($mainamount, $maintax_amount_total, $maintotal_amount) = $this->calculatePurchaseOrderTotals($items, $post['data']);
        $tax_state = ($supplier_state_id == $originator_state_id) ? "S" : "I";
        $transaction_number=$this->toGetApplicationSerialNumber($application_table,$post);
        $maintransactionary = array(self::KEY_COMPANY_ID=>$company_id,self::KEY_TRANSACTION_NUMBER=>$transaction_number,self::KEY_TRANSACTION_DATE=>date(self::DATE_FORMAT_YMD),'cost_center_id'=>$application_usersres->cost_center,self::KEY_PARTY_ID=>$customer_id,'party_name'=>$customer->display_name,'party_email'=>$customer->email,'party_tax_number'=>$customer->customer_tax_reg_no,'party_phone'=>$customer->phone,'party_address'=>$customer->billing_address, 'party_ship_adderss    '=>$customer->shipping_address, 'amount'=>$mainamount,self::KEY_TAX_AMOUNT=>$maintax_amount_total,self::KEY_TOTAL_AMOUNT=>$maintotal_amount, 'order_required_date'=>$post['order_required_date'],'pending_amount'=>$maintotal_amount,'transaction_location_id'=>$application_usersres->location_id, 'transaction_warehouse_id'=>$application_usersres->default_warehouse_id, 'originator_currency_id'=>$originator_currency_id, 'currency_id'=>$currency_id, 'currency_rate'=>'1' ,'originator'=>$get[self::KEY_USER_ID], 'originator_state_id'=>$originator_state_id,'payment_terms'=>'','tax_state'=>$tax_state,self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'199',self::KEY_CREATED_BY=>1,self::KEY_CREATED_DATE=>date(self::DATETIME_FORMAT_YMD_HIS));
        $maintransactionaryres = $callConfig->insertRecord($dmtres->application_table_name,$maintransactionary);
        $transaction_id = $maintransactionaryres[self::KEY_LASTID];

        list($transactionitemsql, $transactionitemtaxary) = $this->buildPurchaseOrderItemsAndTaxes($company_id, $transaction_id, $tax_state, $items, $post['data']);
        foreach($transactionitemsql as $transitemarry){
            $transitemarry[self::KEY_TRANSACTION_ID] = $transaction_id;
            $callConfig->insertRecord($dmtres->item_table,$transitemarry);
        }
        foreach($transactionitemtaxary as $transitemtaxarry){
            $transitemtaxarry[self::KEY_TRANSACTION_ID] = $transaction_id;
            $callConfig->insertRecord($dmtres->tax_table,$transitemtaxarry);
        }
        $get[self::KEY_TRANSACTION_TABLE] = $dmtres->application_table_name;
        $apihitre = $callConfig->getRow("SELECT * FROM ".$dmtres->application_table_name." WHERE id=".$transaction_id);

        $this->updateTransactionItemsQty($get,$apihitre,"-");
        $this->tocreatejounalvoucherbaseallcases($dmtres->application_table_name,$transaction_id);

        return $maintransactionaryres;
    }
    public function getMobileReceipts($get){
        global $callConfig;
        $resciptlist = $this->getReceipts($get);
        return $resciptlist['payments'];
    }
    public function getMobileTransactionReceipts($get){
        global $callConfig;
        $transactionary = array();
        $get[self::KEY_PARTY_ID] = urlencode(json_encode($get[self::KEY_PARTY_ID]));
        $resultary = $this->toGetblancebill($get);
        $duemount_array =  $resultary['payment_term_transactions_due'];
        foreach($resultary['transactions'] as $transtable=>$listdata){
            foreach($listdata as $transdata){
                $transdata->payment_term_due_amount = $duemount_array[$transtable][$transdata->id]!=""?$duemount_array[$transtable][$transdata->id]:$transdata->pending_amount;
                $transactionary[$transtable][] = $transdata;
            }
        }
        return $transactionary;
    }
    public function insertMobileReceipts($get,$post){
        global $callConfig;

        $post['customer_id'] = array($post['customer_id']);
        $newary = $post;
        $paymentpostdata22 = $post['payment'];
        foreach($paymentpostdata22 as $res){
            $newary[self::KEY_TRANSACTION_TABLE][$res[self::KEY_TRANSACTION_ID]] = $res[self::KEY_TRANSACTION_TABLE];
            $newary[self::KEY_TRANSACTION_ID][$res[self::KEY_TRANSACTION_ID]] = $res[self::KEY_TRANSACTION_ID];
            $newary['invoice_number'][$res[self::KEY_TRANSACTION_ID]] = $res['invoice_number'];
            $newary['invoice_total'][$res[self::KEY_TRANSACTION_ID]] = $res['invoice_total'];
            $newary['pending_amount'][$res[self::KEY_TRANSACTION_ID]] = $res['pending_amount'];
            $newary['paid_amonut'][$res[self::KEY_TRANSACTION_ID]] = $res['paid_amount'];
            $newary['tds_paid_amonut'][$res[self::KEY_TRANSACTION_ID]] = $res['tds_paid_amount'];
            $newary['total_paid_amonut'][$res[self::KEY_TRANSACTION_ID]] = $res['total_paid_amount'];
            $newary['payment_allocation_id'][$res[self::KEY_TRANSACTION_ID]] = $res['payment_allocation_id'];
            $newary['transaction_party_id'][$res[self::KEY_TRANSACTION_ID]] = $res['transaction_party_id'];
            $newary['check'][$res[self::KEY_TRANSACTION_ID]] = $res['check'];

        }
        $this->insertReceipts($newary);
        return $newary;
    }
    public function getMoblieUserRelatedDetails($get){
        global $callConfig;
        $return = array();
        $userid = $get[self::KEY_USER_ID];
        $userdatatype = $get['userdatatype'];
        if($userdatatype == "bank_account_users"){
            $banksql = "SELECT ba.id, ba.account_code, ba.account_type, ba.bank_account_name FROM bank_account_users bau, bank_accounts ba WHERE bau.company_id = '".$get[self::KEY_COMPANY_ID]."' AND bau.company_id = ba.company_id AND bau.bank_account_id = ba.id AND bau.user_id = '".$userid."' AND bau.is_active =ba.is_active AND ba.is_active = 0";
            $resdata = $callConfig->getAllRows($banksql);
        }
        $return['result'] = $resdata;
        return $return;
    }
    public function tokengenrateformobile($get){
        global $callConfig;
        $token = hash('sha256', uniqid(random_bytes(8), true) . time());
        $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], self::KEY_USER_ID=>$get[self::KEY_USER_ID], 'type'=>'mobile_view', 'token'=>$token, self::KEY_CURRENT_STATUS=>'0', self::KEY_IS_ACTIVE=>'0', self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
        $callConfig->insertRecord('web_token',$insert_array);
        $web_page = $get['web_page'] !="" ? $get['web_page'] :'orders';
        $web_url = SITEURL."webview/index.php?token=".$token."&user_id=".$get[self::KEY_USER_ID]."&company_id=".$get[self::KEY_COMPANY_ID]."&type=".$get['type']."&webpage=".$web_page."&emp_id=".$get[self::KEY_EMP_ID]."&module_id=".$get[self::KEY_MODULE_ID]."&data_name=".$get[self::KEY_DATA_NAME]."&table_name=".$get[self::KEY_TABLE_NAME];
        if($get['rid'] !=""){
            $web_url .= "&Rid=".base64_encode(base64_encode("id:".$get['rid']));
        }
        $return[self::KEY_STATUS] = self::KEY_SUCCESS;
        $return['web_url'] = $web_url;
        return $return;
    }
    public function toretrivewebviewlogin($get){
        global $callConfig;
        $query = "SELECT * FROM application_users WHERE id =".$get[self::KEY_USER_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        return $callConfig->getRow($query);
    }
    public function getMobileExpenseDetails($get){
        global $callConfig;
        $return = array();
        $res = $this->getEmployeeExpencesDetails($get);
        if($get[self::KEY_GETTYPE]== 'expense_categories'){
            $return =$res['expense_categories'];
        }elseif($get[self::KEY_GETTYPE]== 'payment_modes'){
            $return =$res['general_code_details'];
        }elseif($get[self::KEY_GETTYPE]== 'expense_list'){
            $return =$res['expense_list'];
        }elseif($get[self::KEY_GETTYPE]== 'expense_details'){
            $return =$res['main_details'];
        }elseif($get[self::KEY_GETTYPE]== 'expense_mileages_perdiem'){
            $return =$res['expenses_mileages_perdiem'];
        }
        return $return;
    }
    public function getMobileOrders($get){
        global $callConfig;
        $table_name = "sales_orders";
        $sub_table = "so_items";
        $return = array();
        $originator = $get['id'];
        $query = "SELECT so.id, so.party_name, so.transaction_date, so.transaction_number, COALESCE(soi.items, 0) AS items  FROM  ". $table_name ." so
        LEFT JOIN (SELECT transaction_id, count(transaction_id) as items  FROM ".$sub_table." GROUP BY
        transaction_id) AS soi ON soi.transaction_id = so.id
        where so.originator = '".$originator."' AND  so.company_id = '" . $get[self::KEY_COMPANY_ID] . "' AND so.is_active=0";
        $query_res = $callConfig->getAllRows($query);
        $mobile_orders = array();
        foreach ($query_res as $row) {
            $mobile_orders[] = $row;
        }
        $return['mobile_orders'] = $mobile_orders;
        return $return;
    }
    public function getMobileOrderInfo($get){
        global $callConfig;
        $table_name = "sales_orders";
        $sub_table = "so_items";
        $return = array();
        $return['get'] = $get;
        $transaction_id = $get['id'];
        $query = "SELECT * FROM  ".$table_name." so
        where so.id=".$transaction_id."  AND  so.company_id = ".$get[self::KEY_COMPANY_ID]." AND so.is_active=0";

        $return['query'] = $query;
        $query_res = $callConfig->getRow($query);
        $return['order_res'] = $query_res;

        $query1 = "SELECT * FROM  ".$sub_table."
        where transaction_id = ".$transaction_id." AND company_id = " . $get[self::KEY_COMPANY_ID] . self::SQL_AND_IS_ACTIVE_EQ0;
        $return['query1'] = $query1;
        $query_res = $callConfig->getAllRows($query1);
        $mobile_orders = array();
        foreach ($query_res as $row) {
            $mobile_orders[] = $row;
        }
        $return['mobile_orders'] = $mobile_orders;
        return $return;
    }
    public function getMobileCRMDetails($get){
        global $callConfig;
        $return = array();
        if($get[self::KEY_GETTYPE]== 'countries'){
            $countriessql = "SELECT id,country_name FROM countries WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $return=$callConfig->getAllRows($countriessql);
        }elseif($get[self::KEY_GETTYPE]== 'states'){
            $statessql = "SELECT id,state_name FROM states WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND country_id = '".$get['country_id'].self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $return=$callConfig->getAllRows($statessql);
        }elseif($get[self::KEY_GETTYPE]== 'cities'){
            $citiessql = "SELECT id,city_name FROM cities WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND state_id = '".$get['state_id'].self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $return=$callConfig->getAllRows($citiessql);
        }elseif($get[self::KEY_GETTYPE]== 'crm_leads'){
            $crm_leadssql = "SELECT * FROM crm_leads WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND id = '".$get['rid'].self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $return=$callConfig->getRow($crm_leadssql);
        }
        return $return;
    }
    public function togetdeliveryschedulesdetails($get){
        global $callConfig;
        $query = "SELECT application_table_name as transaction_table, ref_transaction_number as transaction_number, transaction_id, planned_qty, planned_delivery_date, planned_delivery_time FROM delivery_schedules WHERE id=".$get[self::KEY_REF_ID];
        return $callConfig->getRow($query);
    }
    public function getFaceLivenessResponse($get){
        global $callConfig;
        $api_hit_id = $get['api_hit_id'];
        $query = "SELECT webhook_response FROM api_hits WHERE id=".$get['api_hit_id'];
        $res=$callConfig->getRow($query);
        $webhook_response = json_decode($res->webhook_response,1);
        $result = $webhook_response['result'];
        $return['liveness_prediction'] =$result['liveness']['prediction'];
        $return['liveness_face_match_score'] =$result['face_match']['match_score'];
        if($result['liveness']['prediction'] != "real"){
            $return['response_code'] = "102";
            $return['response_message'] = 'liveness Fake';
        }elseif((int)$result['face_match']['match_score'] <= 50){
            $return['response_code'] = "102";
            $return['response_message'] = 'Face Not Match';
        }else{
            $return['response_code'] = "200";
            $return['response_message'] = 'Transaction Successful';
        }
        $return['api_hit_id'] = $api_hit_id;
        return $return;
    }
    public function deliverdorderlistbasepartyid($get){
        global $callConfig;
        $query = "SELECT id, transaction_id, ref_transaction_number as transaction_number, application_table_name  FROM delivery_schedules WHERE company_id= ".$get[self::KEY_COMPANY_ID]." AND party_id = ".$get[self::KEY_PARTY_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;

        $return['data']=$callConfig->getAllRows($query);
        return $return;
    }
    public function getMobileAllEmployeeLeaves($get){
        global $callConfig;
        $query = "SELECT emp_code, display_name, leaves FROM `ai_employees` WHERE id < 5 AND company_id= ".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        return $callConfig->getAllRows($query);
    }
    public function getMobileSingleEmployeeLeaves($get){
        global $callConfig;
        $empname = $get['empname'];
        $query = "SELECT emp_code, display_name, leaves FROM `ai_employees` WHERE company_id= ".$get[self::KEY_COMPANY_ID]."  AND display_name LIKE '%".$empname."%'  AND is_active =0";
        return $callConfig->getRow($query);
    }
    public function insertMobileEmployeeLeaves($get,$post){
        global $callConfig;
        $datediff = strtotime($post[self::KEY_LEAVE_TO]) - strtotime($post[self::KEY_LEAVE_FROM]);
        $datediff = floor($datediff/(60*60*24));
        $leave_days = $datediff+1;
        $year = date("Y");
        $query = "SELECT id, (applicable_leaves-(consumed_leaves+unapproved_leaves)) as available_leave FROM emp_leave_balances WHERE leave_type='".$post[self::KEY_LEAVE_TYPE_ID]."' AND leave_year ='".$year."' AND employee_id = '".$post[self::KEY_EMPLOYEE_ID]."' AND is_active= '0'";
        $result=$callConfig->getRow($query);
        if($result->available_leave > 0){

            $levcatsql = "SELECT leave_category_id FROM leave_types WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND id = '".$post[self::KEY_LEAVE_TYPE_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $levcatres=$callConfig->getRow($levcatsql);

            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], self::KEY_EMPLOYEE_ID=>$post[self::KEY_EMPLOYEE_ID], self::KEY_LEAVE_TYPE_ID=>$post[self::KEY_LEAVE_TYPE_ID], self::KEY_LEAVE_FROM=>$this->dateconvertdbformat($post[self::KEY_LEAVE_FROM]), self::KEY_LEAVE_TO=>$this->dateconvertdbformat($post[self::KEY_LEAVE_TO]), self::KEY_LEAVE_DAYS=>$leave_days, 'application_date'=>date(self::DATETIME_FORMAT_YMD_HIS), self::KEY_SPECIAL_REQUEST=>$post[self::KEY_SPECIAL_REQUEST], self::KEY_CURRENT_STATUS=>'0', self::KEY_IS_ACTIVE=>'0', self::KEY_CREATED_BY=>$post[self::KEY_USER_ID],'leave_category_id'=>$levcatres->leave_category_id);
            $callConfig->insertRecord(self::TBL_EMPLOYEE_LEAVES,$insert_array);
            $transaction_id = $res[self::KEY_LASTID];
            $res[self::KEY_MESSAGE] = 'Record Inserted Successfully';
            $return  = $res;


            if($res[self::KEY_STATUS] == 'sucess'){
                $emp_leave_balancessql = "UPDATE `emp_leave_balances` SET `consumed_leaves`= consumed_leaves + ".$leave_days." WHERE `leave_type`='".$post[self::KEY_LEAVE_TYPE_ID]."' AND leave_year ='".$year."' AND employee_id = '".$post[self::KEY_EMPLOYEE_ID]."'  AND is_active= '0'";
                $callConfig->executeQuery($emp_leave_balancessql);


                $get[self::KEY_TRANSACTION_TABLE] = self::TBL_EMPLOYEE_LEAVES;
                $get[self::KEY_CURRENT_STATUS] = 0;
                $get[self::KEY_TRANSACTION_NO] = $transaction_id;
                $get['next_status'] = 101;
                $get['comment'] = "AI";
                $return['workflow'] = $this->submitstatusdynamicworkflow($get);
            }


        }else{
            $return[self::KEY_STATUS] = 'fail';
            $return['query'] = $query;
            $return[self::KEY_ERROR] = 'Leave Balance Not Available';
            $res[self::KEY_MESSAGE] = 'Leave Balance Not Available';
        }
        return $return;
    }
    public function getMobileReportingManagerEmployeeLeave($get){
        global $callConfig;
        $leave_from = $get[self::KEY_LEAVE_FROM] !=""?$get[self::KEY_LEAVE_FROM]:'2024-07-11';
        $leave_to = $get[self::KEY_LEAVE_TO] !=""?$get[self::KEY_LEAVE_TO]:'2024-07-11';
        $condition = "";
        if($get['getleaveapitype'] == "all_approval_pendding"){
            $condition = " AND empl.current_status NOT IN('".ST_ACTIVE."','".ST_DRAFT."','".ST_APPROVED."')";

        }elseif($get['getleaveapitype'] == "all_approval_pendding_with_date"){
            $condition .= " AND empl.current_status NOT IN('".ST_ACTIVE."','".ST_DRAFT."','".ST_APPROVED."')";
            $condition .= " AND ((leave_from >= '".$leave_from."' AND leave_from <= '".$leave_to."') OR (leave_to >= '".$leave_from."' AND leave_to <= '".$leave_to."'))";

        }elseif($get['getleaveapitype'] == "all_approved"){
            $condition .= " AND empl.current_status IN('".ST_APPROVED."')";


        }elseif($get['getleaveapitype'] == "all_approved_with_date"){
            $condition .= " AND empl.current_status IN('".ST_APPROVED."')";
                $condition .= " AND ((leave_from >= '".$leave_from."' AND leave_from <= '".$leave_to."') OR (leave_to >= '".$leave_from."' AND leave_to <= '".$leave_to."'))";
        }else{
            $condition = " AND empl.current_status NOT IN('".ST_ACTIVE."','".ST_DRAFT."')";
            $condition .= " AND ((leave_from >= '".$leave_from."' AND leave_from <= '".$leave_to."') OR (leave_to >= '".$leave_from."' AND leave_to <= '".$leave_to."'))";

        }
        $query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.reporting_manager_id, empl.id as employee_leave_id, empl.leave_type_id, empl.leave_from, empl.leave_to, empl.leave_days, empl.application_date , empl.current_status, (SELECT lc.name FROM leave_categories lc WHERE empl.company_id= lc.company_id AND empl.leave_type_id = lc.id AND lc.is_active =0) as leave_category_name FROM employees emp, employee_leaves empl WHERE emp.company_id= ".$get[self::KEY_COMPANY_ID]." AND emp.company_id= empl.company_id AND emp.id= empl.employee_id AND emp.reporting_manager_id = '".$useriid."' AND emp.is_active =0 ".$condition;
        $return=$callConfig->getAllRows($query);
        $returnres['data'] = $return;
        return $returnres;
    }
    public function getInsertEmployeeLeaves($get){
        global $callConfig;
        $emp_id = $get[self::KEY_EMP_ID];
        $returnres['get'] = $get;
        $year = date("Y");
        if($emp_id ==""){
            $getempsql = "SELECT id FROM employees WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND user_id = '".$get[self::KEY_USER_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $resemp = $callConfig->getRow($getempsql);
            $emp_id = $resemp->id;
        }
        if($get['resulttype'] == "LIST"){
            $get[self::KEY_TABLE] =self::TBL_EMPLOYEE_LEAVES;
            $status_progress = $this->getStatusProgressList($get);
            $returnres['status_progress'] = $status_progress;

            $statussql = "SELECT id,name,name_value,background_color,color,sequence FROM status WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $allstatus = $callConfig->getAllRows($statussql);
            foreach($allstatus as $value){
                    $status[$value->name_value]= $value;
            }
            $return[self::KEY_STATUS] = $status;

            $empleavequery = "SELECT el.id, el.leave_type_id, el.leave_from, el.leave_to, el.leave_days, el.application_date, el.current_status, lc.name FROM employee_leaves el, leave_categories lc WHERE el.company_id = '".$get[self::KEY_COMPANY_ID]."' AND el.company_id = lc.company_id AND el.leave_category_id = lc.id AND el.is_active = lc.is_active";
            $empleaveres=$callConfig->getAllRows($empleavequery);
            $returnres[self::TBL_EMPLOYEE_LEAVES] = $empleaveres;
        }elseif($get['resulttype'] == "APPROVEPENDINGLEAVE"){

            $get[self::KEY_TABLE] =self::TBL_EMPLOYEE_LEAVES;
            $status_progress = $this->getStatusProgressList($get);
            $returnres['status_progress'] = $status_progress;

            $statussql = "SELECT id,name,name_value,background_color,color,sequence FROM status WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $allstatus = $callConfig->getAllRows($statussql);
            foreach($allstatus as $value){
                    $status[$value->name_value]= $value;
            }
            $return[self::KEY_STATUS] = $status;


            $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, tal.send_back_flag, tal.call_back, el.id, el.leave_type_id, el.leave_from, el.leave_to, el.leave_days, el.application_date, el.current_status, lc.name FROM transaction_approval_logs tal, employee_leaves el, leave_categories lc WHERE tal.company_id = '".$get[self::KEY_COMPANY_ID]."' AND tal.company_id = el.company_id AND tal.transaction_table = 'employee_leaves' AND tal.user_id = '".$get[self::KEY_USER_ID]."' AND tal.transaction_id = el.id AND el.company_id = lc.company_id AND el.leave_category_id = lc.id AND el.is_active = lc.is_active AND el.is_active = 0";
            $transapprlogres = $callConfig->getAllRows($transapprlogsql);
            $returnres['transaction_approval_logs'] = $transapprlogres;
            $returnres['transaction_approval_logs_sql'] = $transapprlogsql;



        }elseif($get['resulttype'] == "SINGLELEAVE"){
            $get[self::KEY_TABLE] =self::TBL_EMPLOYEE_LEAVES;
            $status_progress = $this->getStatusProgressList($get);
            $returnres['status_progress'] = $status_progress;

            $statussql = "SELECT id,name,name_value,background_color,color,sequence FROM status WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $allstatus = $callConfig->getAllRows($statussql);
            foreach($allstatus as $value){
                    $status[$value->name_value]= $value;
            }
            $return[self::KEY_STATUS] = $status;

            $empleavequery = "SELECT el.id, el.leave_type_id, el.leave_from, el.leave_to, el.leave_days, el.application_date, el.current_status, lc.name, emp.display_name, emp.emp_code FROM employee_leaves el, leave_categories lc, employees emp WHERE el.company_id = '".$get[self::KEY_COMPANY_ID]."' AND el.company_id = lc.company_id AND el.company_id = emp.company_id AND el.id = '".$get['rid']."' AND el.leave_category_id = lc.id AND el.employee_id = emp.id AND el.is_active = lc.is_active AND el.is_active = emp.is_active AND el.is_active = 0";
            $empleaveres=$callConfig->getRow($empleavequery);
            $returnres[self::TBL_EMPLOYEE_LEAVES] = $empleaveres;
        }else{
            $query = "SELECT elb.id, elb.employee_id, elb.applicable_leaves, elb.consumed_leaves, elb.unapproved_leaves, (elb.applicable_leaves-(elb.consumed_leaves+elb.unapproved_leaves)) as available_leave, elb.leave_type as leave_type_id, lt.leave_type_name, lt.leave_category_id, lc.name, lc.description FROM emp_leave_balances elb, leave_types lt, leave_categories lc WHERE elb.company_id = '".$get[self::KEY_COMPANY_ID]."' AND   elb.company_id = lt.company_id AND  elb.company_id = lc.company_id AND elb.leave_type = lt.id AND lt.leave_category_id = lc.id AND elb.employee_id = '".$emp_id."' AND elb.leave_year = '".$year."' AND (elb.applicable_leaves-(elb.consumed_leaves+elb.unapproved_leaves)) > 0 AND elb.is_active = 0 AND elb.is_active = lt.is_active AND lt.is_active = lc.is_active";
            $returnres['emp_leave_balances_sql'] = $query;
            $return=$callConfig->getAllRows($query);
            $returnres['emp_leave_balances'] = $return;
        }
        return $returnres;
    }
    private function buildWorkflowActionUrl($get, $nextStatus, $currentStatus, $transactionNo, $tableName){
        return API."?mode=submitstatusdynamicworkflow&module_id=".$get[self::KEY_MODULE_ID]."&data_name=".$get[self::KEY_DATA_NAME]."&user_id=".$get[self::KEY_USER_ID]."&company_id=".$get[self::KEY_COMPANY_ID]."&comment=AI&next_status=".$nextStatus."&current_status=".$currentStatus."&transaction_no=".$transactionNo."&transaction_table=".$tableName;
    }

    public function checkWorkFlowStatusWithApprove($get){
        $transaction_no = $get[self::KEY_TRANSACTION_NO];
        $table_name = $get[self::KEY_TRANSACTION_TABLE];
        $current_status = $get[self::KEY_CURRENT_STATUS];
        $userid = $get[self::KEY_USER_ID];
        $result = $this->getDynamicWorkFlowSteps($get);
        $workflowsteps = $result['workflowsteps'];
        $workflow_details = $result['workflow_details'];
        $nextstepkeyvalue = $result['nextstepkeyvalue'];
        $transaction_owner_id = $result['transaction_owner_id'];
        $transaction_approval_logs = $result['transaction_approval_logs'];
        $errorstatus = null;
        $rejecturl = $sendbackurl = $workflow_btn = "";

        if(isset($result[self::KEY_ERROR])){
            if($result[self::KEY_ERROR] == '9061' && $transaction_owner_id == $userid && $current_status == ST_DRAFT){
                $errorstatus = self::KEY_SUCCESS;
                $workflow_btn = $this->buildWorkflowActionUrl($get, ST_ACTIVE, $current_status, $transaction_no, $table_name);
            }elseif($result[self::KEY_ERROR] == '9063' || $result[self::KEY_ERROR] == '9062' || $result[self::KEY_ERROR] == '9060'){
                $errorstatus = "fail";
                $workflow_btn = $result[self::KEY_ERROR];
            }
            return array(self::KEY_STATUS=>$errorstatus,'approve_url'=>$workflow_btn,'reject_url'=>$rejecturl,'send_back_url'=>$sendbackurl);
        }

        if($current_status != ST_APPROVED && $current_status != ST_REJECTED){
            if($current_status == ST_ACTIVE || $current_status == ST_SEND_BACK || $current_status == ST_CALLBACK || $workflow_details->workflow_trigger_status == $current_status){
                if($transaction_owner_id == $userid){
                    if($current_status == ST_SEND_BACK || $current_status == ST_CALLBACK){
                        $current_status = $workflow_details->workflow_trigger_status;
                    }
                    $nextstatus = $workflow_details->workflow_trigger_status;
                    $errorstatus = self::KEY_SUCCESS;
                    $workflow_btn = $this->buildWorkflowActionUrl($get, $nextstatus, $workflowsteps[$current_status]['workflow_level_status'], $transaction_no, $table_name);
                }else{
                    $errorstatus = "fail";
                    $workflow_btn .= "You are not authorized";
                }
            }elseif($transaction_owner_id != $userid){
                if(isset($transaction_approval_logs[$userid])){
                    $errorstatus = self::KEY_SUCCESS;
                    $workflow_btn = $this->buildWorkflowActionUrl($get, $nextstepkeyvalue[$current_status], $current_status, $transaction_no, $table_name);
                    $rejecturl = $this->buildWorkflowActionUrl($get, ST_REJECTED, $current_status, $transaction_no, $table_name);
                    $sendbackurl = $this->buildWorkflowActionUrl($get, ST_SEND_BACK, $current_status, $transaction_no, $table_name);
                }else{
                    $errorstatus = "fail";
                    $workflow_btn .= "You are not authorized / already approved";
                }
            }
        }
        return array(self::KEY_STATUS=>$errorstatus,'approve_url'=>$workflow_btn,'reject_url'=>$rejecturl,'send_back_url'=>$sendbackurl);
    }
    public function transportrequestcreate($get){
        global $callConfig;
        $query = "SELECT id, driver_name FROM drivers WHERE company_id = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $rows = $callConfig->getAllRows($query);
        $return['drivers'] = $rows;
        $query = "SELECT id, vehicle_name, registration_number FROM transports WHERE company_id = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $rows = $callConfig->getAllRows($query);
        $return['transports'] = $rows;
        return $return;
    }
    public function togetdrivelistalldata($get){
        global $callConfig;
        if($get['driver_id']!=""){
            $query = "SELECT
                    t.vehicle_name,
                    t.registration_number,
                    ta.*
                FROM
                    transport_assign ta
                JOIN
                    transports t ON ta.transport_id =  t.id
                WHERE
                    ta.company_id = ".$get[self::KEY_COMPANY_ID]." AND
                    ta.driver_id = ".$get['driver_id']." AND
                    ta.is_active = 0";
        }else{
            $query = "SELECT
                    ta.*
                FROM
                    transport_assign ta
                WHERE
                    ta.company_id = ".$get[self::KEY_COMPANY_ID]." AND
                    ta.user_id = ".$get[self::KEY_USER_ID]." AND
                    ta.is_active = 0";
        }

        $rows = $callConfig->getAllRows($query);
        $return['data'] = $rows;
        $query = "SELECT * FROM status WHERE name_value IN (SELECT status FROM status_progress WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND table_name = 'transport_assign' AND is_active =0) AND is_active =0";
        $rows = $callConfig->getAllRows($query);
        $return[self::KEY_STATUS] = $rows;
        return $return;
    }
    public function togetinprocesstripdetailswithexpencess($get){
        global $callConfig;
        $ress = null;
        $query = "SELECT id, expense_code, user_id, emp_id, expense_date, tax_amount, total_amount, transport_assign_id, client_reimbursement, description, pending_amount, current_status FROM emp_expenses WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND id =".$get['exp_id'].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        $row = $callConfig->getRow($query);

        if($row->id!=""){
            $data=(array)$ress;
            if($row->transport_assign_id!=""){
                $query = "SELECT id, `driver_id`,  `transport_id`, `source_place`, `source_latitude`, `source_longitude`, `destination_place`, `destination_latitude`, `destination_longitude`, `start_reading`, `stop_reading`, `start_reading_image`, `stop_reading_image`, `contact_details`, `contact_phone`, `start_date`, `start_time`, `actual_start_date`, `actual_start_time`, `departure_date`, `departure_time`, `remarks` FROM transport_assign WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND  id=".$row->transport_assign_id.self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
                $datares = $callConfig->getRow($query);
                $datares = (array)$datares;
                $data = array_merge($data,$datares);
                if($datares['transport_id'] !=""){
                    $query = "SELECT vehicle_name, registration_number, vehicle_capacity, vehicle_type  FROM transports WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND  id=".$row->transport_id.self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
                    $ttres = $callConfig->getRow($query);
                    $ttres = (array)$ttres;
                    $data = array_merge($data,$ttres);
                }
            }

        }
        $row = $callConfig->getRow($query);

        if($row->id!=""){
            $return['data'] = (array)$row;

            $query = "SELECT
                eed.id,
                eed.emp_expenses_id,
                eed.merchant_name,
                eed.bill_no,
                eed.bill_date,
                eed.bill_file,
                eed.description,
                eed.payment_mode,
                (SELECT ge.genc_detail_name FROM general_code_details as ge WHERE ge.genc_detail_code  = eed.payment_mode  AND ge.genc_code= 'PAYMETHOD') as payment_mode_name ,

                eed.tax_amount,
                eed.expense_amount
            FROM
                emp_expenses_details AS eed
            WHERE  eed.company_id = ".$get[self::KEY_COMPANY_ID]." AND eed.emp_expenses_id = ".$row->id." AND eed.is_active =0";
            $sdata = $callConfig->getAllRows($query);
            $return['data']['child'] =(array)$sdata;
        }
        return $return;
    }
    public function togetdynamicresaonlist($get){
        global $callConfig;
        $query = "SELECT id, genc_detail_code, genc_detail_name FROM general_code_details where is_active='0' and company_id='".$get[self::KEY_COMPANY_ID]."' and genc_code='".$get['code']."' order by id ASC";
        $display_details = $callConfig->getAllRows($query);
        $data['data'] = $display_details;
        return $data;
    }
    public function togetemployeeexpensesbyuser($get){
        global $callConfig;

        $billssql = "SELECT  id, expense_code, expense_date, amount, reference_no, current_status, reference_date FROM emp_expenses WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND is_active = 0 AND (emp_id='".$get[self::KEY_EMP_ID]."' OR user_id = '".$get[self::KEY_USER_ID]."') ORDER BY id DESC";
        $billsres = $callConfig->getAllRows($billssql);
        $return['emp_expenses'] = $billsres;

        $get[self::KEY_TABLE] ="trips";
        $status_progress = $this->getStatusProgressList($get);
        $return['status_progress'] = $status_progress;

        $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, trp.expense_code, trp.expense_date, trp.amount, trp.id
        FROM transaction_approval_logs tal, emp_expenses trp WHERE tal.company_id = ".$get[self::KEY_COMPANY_ID]." AND tal.company_id = trp.company_id AND  tal.transaction_table = 'emp_expenses' AND tal.user_id = '".$get[self::KEY_USER_ID]."' AND tal.transaction_id = trp.id AND trp.is_active = 0";
        $transapprlogres = $callConfig->getAllRows($transapprlogsql);
        $return['transaction_approval_logs'] = $transapprlogres;

        return $return;
    }
    public function togetemployeeexpensesbyuser_old($get){
        global $callConfig;
        $query = "SELECT id, expense_code, user_id, emp_id, expense_date, tax_amount, total_amount, transport_assign_id, client_reimbursement, description, pending_amount, current_status FROM emp_expenses WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND user_id =".$get[self::KEY_USER_ID]." AND is_active =0 ORDER BY id DESC";
        $ress = $callConfig->getAllRows($query);

        foreach($ress as $ee_res){
            $data=(array)$ee_res;
            if($ee_res->transport_assign_id!=""){
                $query = "SELECT  `driver_id`,  `transport_id`, `source_place`, `source_latitude`, `source_longitude`, `destination_place`, `destination_latitude`, `destination_longitude`, `start_reading`, `stop_reading`, `start_reading_image`, `stop_reading_image`, `contact_details`, `contact_phone`, `start_date`, `start_time`, `actual_start_date`, `actual_start_time`, `departure_date`, `departure_time`, `remarks` FROM transport_assign WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND  id=".$ee_res->transport_assign_id.self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
                $datares = $callConfig->getRow($query);
                $datares = (array)$datares;
                $data = array_merge($data,$datares);
                if($datares['transport_id'] !=""){
                    $query = "SELECT vehicle_name, registration_number, vehicle_capacity, vehicle_type  FROM transports WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND  id=".$ee_res->transport_id.self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
                    $ttres = $callConfig->getRow($query);
                    $ttres = (array)$ttres;
                    $data = array_merge($data,$ttres);
                }
            }
            $alldata[] = $data;
        }

        return $alldata;
    }
    public function togettaskdaworkspacenoprojectandtask($get){
        global $callConfig;
        $uwhr = ""; $cwhr ="";
        if( $get['role_short_code']!='TASKDA_ADMIN'){
            $uwhr = " LEFT JOIN project_task_teams ptt
                        ON ptt.project_task_id = pt.id
                       AND ptt.project_id = p.id
                       AND ptt.user_id = ".$get[self::KEY_USER_ID];
            $cwhr = "AND  ptt.user_id IS NOT NULL ";
        }
        $query = "SELECT
            pws.id AS workspace_id,
            pws.name AS workspace_name,
            pws.created_by as workspace_creater,
            COUNT( DISTINCT p.id) AS number_of_projects,
            COUNT(DISTINCT pt.id) AS number_of_tasks,
            ROUND(
                    (
                        COUNT(DISTINCT CASE
                            WHEN pt.current_status = 199 THEN pt.id
                        END) * 100.0
                    ) / NULLIF(COUNT(DISTINCT pt.id), 0),
                    2
                ) AS completion_percentage
        FROM
            project_work_spaces pws
        LEFT JOIN
            projects p ON p.project_work_space_id = pws.id AND p.is_active=0
        LEFT JOIN
            project_task_details pt ON pt.project_id = p.id AND pt.is_active=0
        ".$uwhr."
        WHERE
            pws.company_id = ".$get[self::KEY_COMPANY_ID]."
            ".$cwhr."
            AND pws.is_active=0
        GROUP BY
            pws.id, pws.name
        ORDER BY
            pws.name";
        return $callConfig->getAllRows($query);
    }
    public function togetprojecteditdetailsval($get){
        global $callConfig;
        $query = "SELECT id, type_name FROM project_type WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $type = $callConfig->getAllRows($query);
        $return['project_type'] = $type;
        if($get['rid']!=""){
            $query = "SELECT * FROM projects WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_ID.$get['rid'];
            $data = $callConfig->getRow($query);
            $return['data'] = $data;
        }
        return $return;
    }
    public function togetmobileprojectsview($get){
        global $callConfig;
        $query = "SELECT p.id, p.project_logo, p.project_name, p.description, p.planned_start, p.planned_finish, p.created_by as project_creater, COUNT(DISTINCT pt.id) AS number_of_project_teams, COUNT(DISTINCT t.id) AS number_of_project_tasks, CASE WHEN COUNT(t.id) = 0 THEN 0 ELSE ROUND( (SUM(CASE WHEN t.current_status = 199 THEN 1 ELSE 0 END) / COUNT(t.id)) * 100, 2 ) END AS percentage_of_tasks_completed FROM projects p LEFT JOIN project_teams pt ON p.id = pt.project_id AND pt.is_active = 0 LEFT JOIN project_task_details t ON p.id = t.project_id AND t.is_active = 0 WHERE p.company_id =".$get[self::KEY_COMPANY_ID]." AND p.project_work_space_id=".$get['work_space_id']." AND p.is_active =0 GROUP BY p.id";
        $works = $callConfig->getAllRows($query);
        $work_ids = array();
        foreach($works as $work){
            $work_ids[] = $work->id;
        }
        $return['get'] = $get;
        if(!empty($work_ids)){
            $query = "SELECT project_id, count(id) as cc FROM project_phases WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_id IN (".implode(",",$work_ids).") AND is_active =0 GROUP BY project_id";
            $project_phases = $callConfig->getAllRows($query);
            $query = "SELECT project_id,count(id) as cc FROM project_teams WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_id IN (".implode(",",$work_ids).") AND is_active =0 GROUP BY project_id";
            $project_teams = $callConfig->getAllRows($query);
            $query = "SELECT project_id,count(id) as cc FROM project_task_status WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_id IN (".implode(",",$work_ids).") AND is_active =0 GROUP BY project_id";
            $project_task_status = $callConfig->getAllRows($query);
            foreach($project_task_status as $project_status){
                if($project_status->cc==0){
                    $status = array('0'=>'To DO','101'=>'Doing','5'=>'On Hold','199'=>'Completed');
                    $k=10;
                    foreach($status  as $skey=>$sta){
                        $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], self::KEY_PROJECT_ID=> $project_status->cc, self::KEY_TABLE_NAME=>self::TBL_PROJECT_TASK_DETAILS, 'name'=>$sta, self::KEY_STATUS=>$skey, 'action_label'=>$sta,'display_priority'=>$k,self::KEY_IS_ACTIVE=>'0', self::KEY_CURRENT_STATUS=>'0', self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
                        $kresult[self::KEY_INSERT][] = $insert_array;
                        $kresult['res'][] = $callConfig->insertRecord('project_task_status',$insert_array);
                        $k = $k+10;
                    }
                }
            }
            foreach($project_phases as $project_phase){
                $phase[$project_phase->project_id] = $project_phase->cc;
            }
            foreach($project_teams as $project_team){
                $team[$project_team->project_id] = $project_team->cc;
            }
        }
        foreach($works as $work){
            $main =(array)$work;
            $main['projet_value'] = 'N';
            if($phase[$work->id]>0 && $team[$work->id]>0){
                $main['projet_value'] = 'Y';
            }
            $main['projet_team_cc'] = $team[$work->id];
            $main['project_phase_cc'] = $phase[$work->id];
            $pass[]= $main;
        }
        $return['data'] = $pass;
        return $return;
    }
    public function todisplaytasklistview($get){
        global $callConfig;
        $uwhr = "";
        if($get['role_short_code']!='TASKDA_ADMIN'){
            $uwhr = " AND ( p.id IN (SELECT `project_task_id` FROM `project_task_teams` WHERE `user_id` =".$get[self::KEY_USER_ID]." AND project_id =".$get[self::KEY_PROJECT_ID]." AND is_active=0 ) OR p.created_by = ".$get[self::KEY_USER_ID].")";
        }
        $query = "SELECT p.id, p.task_name, p.project_id, p.description, p.current_status, p.assigen_id,au.display_name,t.name as status_name,p.planned_start,p.planned_finish, p.created_by FROM project_task_details p LEFT JOIN application_users au ON p.assigen_id = au.id AND au.is_active = 0 LEFT JOIN project_task_status t ON p.current_status = t.status AND  table_name='project_task_details' AND t.is_active = 0 AND p.project_id=t.project_id  WHERE p.company_id =".$get[self::KEY_COMPANY_ID]." AND p.project_id=".$get[self::KEY_PROJECT_ID]." AND p.is_active =0 ".$uwhr." GROUP BY p.id";
        $return['data'] = $callConfig->getAllRows($query);
        return $return;
    }
    public function projecttaskstatuscreation($get){
        global $callConfig;
        $status = array('0'=>'To DO','101'=>'Doing','5'=>'On Hold','199'=>'Completed');
        $k=10;
        foreach($status  as $skey=>$sta){
            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], self::KEY_PROJECT_ID=> $get[self::KEY_PROJECT_ID], self::KEY_TABLE_NAME=>self::TBL_PROJECT_TASK_DETAILS, 'name'=>$sta, self::KEY_STATUS=>$skey, 'action_label'=>$sta,'display_priority'=>$k,self::KEY_IS_ACTIVE=>'0', self::KEY_CURRENT_STATUS=>'0', self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
            $kresult[self::KEY_INSERT][] = $insert_array;
            $kresult['res'][] = $callConfig->insertRecord('project_task_status',$insert_array);
            $k = $k+10;
        }
        return $kresult;
    }
    public function togettaskcreatelistdata($get){
        global $callConfig;
        $tt = array('1'=> 'Story', '2'=> 'Task', '3'=> 'Sub Task', '4'=>'Bug');
        foreach($tt as $tp=>$pp){
            $task_type[]= array('id'=>$tp,'type'=>$pp);
        }
        $return['task_type'] = $task_type;
        $query = "SELECT id, status, name as status_name FROM project_task_status WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND table_name='project_task_details' AND project_id=".$get[self::KEY_PROJECT_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $project_task_status = $callConfig->getAllRows($query);
        if(count((array)$project_task_status)<1){
            $this->projecttaskstatuscreation($get);
            $query = "SELECT id, status, name as status_name FROM project_task_status WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND table_name='project_task_details' AND project_id=".$get[self::KEY_PROJECT_ID].self::SQL_AND_IS_ACTIVE_EQ0;
            $project_task_status = $callConfig->getAllRows($query);
        }
        $return['project_task_status'] = $project_task_status;
        $query = "SELECT id, display_name FROM application_users WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND id IN ( SELECT user_id FROM project_teams WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_id=".$get[self::KEY_PROJECT_ID]." AND is_active=0 ) AND is_active=0";
        $project_teams = $callConfig->getAllRows($query);
        $return['project_teams'] = $project_teams;
        $query = "SELECT * FROM  application_labels WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND work_id IN (0,".$get[self::KEY_PROJECT_ID].") AND is_active = 0";
        $lableres = $callConfig->getAllRows($query);
        $lab = array();
        foreach($lableres as $lable){
            $lab[$lable->work_id][$lable->id] = $lable;
        }
        $return['labels'] = count((array)$lab[$get[self::KEY_PROJECT_ID]])>0 ? $lab[$get[self::KEY_PROJECT_ID]] : $lab['0'];
        $query = "SELECT id, project_phase_name FROM project_phases WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_id=".$get[self::KEY_PROJECT_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $project_phases = $callConfig->getAllRows($query);
        $return['project_phases'] = $project_phases;
        if($get['rid']!=""){
            // Intentionally empty
        }
        return $return;
    }
    public function togetmobiletaskdetailsall($get){
        global $callConfig;
        $query ="SELECT pt.*, (SELECT project_phase_name  FROM project_phases WHERE id=pt.project_phase_id AND is_active=0 ) as project_phase_name, (SELECT name  FROM project_task_status WHERE  table_name='project_task_details' AND project_id = pt.project_id AND status=pt.current_status AND is_active =0 ) as status_name  FROM project_task_details pt WHERE pt.company_id=".$get[self::KEY_COMPANY_ID]." AND pt.id=".$get[self::KEY_TASK_ID]." AND pt.is_active=0";
        $data = $callConfig->getRow($query);
        $get[self::KEY_PROJECT_ID] = $data->project_id;
        $return['taskdetils'] =$data;
        $query = "SELECT id,user_id, (SELECT au.display_name FROM application_users au  WHERE au.id = user_id) as display_name, project_role_id, (SELECT pr.role_name FROM project_roles pr WHERE pr.id=project_role_id) role_name FROM project_task_teams WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_task_id=".$get[self::KEY_TASK_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $project_task_teams = $callConfig->getAllRows($query);
        $return['project_task_teams'] = $project_task_teams;
        $query = "SELECT id, display_name FROM application_users WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND id IN ( SELECT user_id FROM project_teams WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_id=".$get[self::KEY_PROJECT_ID]." AND is_active=0 ) AND is_active=0";
        $project_teams = $callConfig->getAllRows($query);
        $return['project_teams'] = $project_teams;

        $query = "SELECT id, role_name FROM project_roles WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $project_roles = $callConfig->getAllRows($query);
        $return['project_team_roles'] = $project_roles;
        $query = "SELECT id, status, name as status_name FROM project_task_status WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND table_name='project_task_details' AND project_id=".$get[self::KEY_PROJECT_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $project_task_status = $callConfig->getAllRows($query);
        $return['project_task_status'] = $project_task_status;
        $query = "SELECT id,application_label_id, (SELECT al.label_code FROM application_labels al WHERE al.id=application_label_id) as label_code FROM  application_attach_labels WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id = ".$get[self::KEY_TASK_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $attachres = $callConfig->getAllRows($query);
        foreach($attachres as $attachr){
            $alab[] = $attachr;
        }
        $return['attach_labels'] = $alab;
        $query = "SELECT id, label_name, label_code,work_id FROM  application_labels WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND work_id IN (0,".$get[self::KEY_PROJECT_ID].") AND is_active = 0";
        $lableres = $callConfig->getAllRows($query);
        $lab = array();
        foreach($lableres as $lable){
            $lab[$lable->work_id][] = $lable;
        }
        $return['labels'] = count((array)$lab[$get[self::KEY_PROJECT_ID]])>0 ? $lab[$get[self::KEY_PROJECT_ID]] : $lab['0'];
        $query = "SELECT id, parent_id, check_name,assign_id, if(current_status!=4,'N','Y') check_status FROM  application_check_lists WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id =".$get[self::KEY_TASK_ID]." AND is_active = 0 ORDER BY parent_id";
        $checkeres = $callConfig->getAllRows($query);
        $i=0;
        foreach($checkeres as $cres){
            if($cres->parent_id==0){
                $main[$i] = (array)$cres;
                $val[$cres->id] =$i;
                $i++;
            }else{
                if(isset($main[$val[$cres->parent_id]])){
                    $main[$val[$cres->parent_id]]['child'][] = (array)$cres;
                }
            }

        }
        foreach($main as $ww){
            $pchl[] = $ww;
        }
        $return['checklist'] =$pchl;
        $query = "SELECT a.id, a.comments, a.created_date, u.display_name, CONCAT( UPPER(SUBSTRING(SUBSTRING_INDEX(u.display_name, ' ', 1), 1, 1)), UPPER(SUBSTRING(SUBSTRING_INDEX(u.display_name, ' ', -1), 1, 1)) ) AS initials FROM project_task_activities a LEFT JOIN application_users u ON a.created_by = u.id WHERE  a.company_id =".$get[self::KEY_COMPANY_ID]." AND a.activity_type = 'activity' AND a.application_table_name = '".TBL_PROJECT_TASK_DETAILS."' AND a.transaction_id = ".$get[self::KEY_TASK_ID]." AND a.is_active=0";
        $comments = $callConfig->getAllRows($query);
        $return['comments'] = $comments;

        $query = "SELECT id,file_name,actual_file_name FROM  application_attachments WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND application_table_name = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id = ".$get[self::KEY_TASK_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $attachres = $callConfig->getAllRows($query);
        $return['attachments'] = $attachres;
        $crow=$callConfig->getRow("SELECT id,task_start,task_id FROM project_task_time_log WHERE  company_id = ".$get[self::KEY_COMPANY_ID]." AND task_end is NULL AND is_active = 0 ORDER BY id DESC LIMIT 0,1");
        $return['project_task_time_status'] = "N";
        if(count((array)$crow)>0){
            $return['project_task_time_status'] = "Y";
        }
        $return['project_task_time_log'] = $crow;
        $xcrow=$callConfig->getAllRows("SELECT id,task_start,task_end,task_id FROM project_task_time_log WHERE  company_id = ".$get[self::KEY_COMPANY_ID]." AND  is_active = 0 ");
        $return['all_project_task_time_status'] = $xcrow;
        return $return;
    }
    public function addupdaterolememberdetailformobile($get,$post){
        global $callConfig;

        $query = "SELECT id FROM project_task_teams WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND project_id = ".$post[self::KEY_PROJECT_ID]."  AND project_task_id =".$post[self::KEY_TASK_ID].self::SQL_AND_USER_ID.$post['member_ids'].self::SQL_AND_IS_ACTIVE_0;
        $datas= $callConfig->getAllRows($query);
        $callConfig->executeQuery("UPDATE `project_task_details` SET  to_date= ".date(self::DATE_FORMAT_YMD)." WHERE id=".$post[self::KEY_TASK_ID]);
        if(count((array)$datas)>0){
            return array(self::KEY_STATUS=>'fail',self::KEY_MESSAGE=>'user already exits');
        }else{
            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_PROJECT_ID=>$post[self::KEY_PROJECT_ID], 'project_task_id'=>$post[self::KEY_TASK_ID], 'project_role_id'=>$post['role_id'],self::KEY_USER_ID=>$post['member_ids'],self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID],self::KEY_CREATED_DATE=>date(self::DATETIME_FORMAT_YMD_HIS));
            $callConfig->insertRecord('project_task_teams',$insert_array);

            $task_query = "SELECT id, task_name, project_id FROM project_task_details WHERE company_id = ".$get[self::KEY_COMPANY_ID]."   AND id =".$post[self::KEY_TASK_ID]." is_active = 0";
            $task_data = $callConfig->getRow($task_query);
            $query = "SELECT id, company_id,display_name, user_email, user_mobile FROM application_users WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND id = ".$get['member_ids'].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
            $user_data = $callConfig->getRow($query);
            $get['mobile'] = $user_data->user_mobile;

            $message = 'You have been assigned the task '.$task_data->task_name;

            $array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], 'mobile_no'=>$get['mobile'], self::KEY_MESSAGE=>$message, 'send_no'=> '8179947020', 'recivce_no'=> $get['mobile'],self::KEY_DATA_NAME=>$get[self::KEY_DATA_NAME]);




            $array['user_name'] = $user_data->display_name;
            $array['user_email_id'] = $user_data->user_email;
            $array['session_company_id'] = $user_data->company_id;
            $task_team_q = "SELECT id,  user_id FROM project_task_teams WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND  project_task_id=".$post[self::KEY_TASK_ID].self::SQL_AND_IS_ACTIVE_EQ0;
            $teams  = $callConfig->getAllRows($task_team_q);
            foreach($teams as $list){
                $mobilsendnotification = array();
                $mobilsendnotification = $get;
                $mobilsendnotification[self::KEY_MESSAGE] ='Someone has added a new user for this task. '.$task_data->task_name;
                $mobilsendnotification[self::KEY_USER_ID] =$list->user_id;
                $mobilsendnotification['title'] = 'Task Assign';
                $mobilsendnotification['data'] = array('module' => 'TASKDA', self::KEY_MODULE_ID => '25', 'click_action' => 'OPEN_TASK',self::KEY_TASK_ID =>$post[self::KEY_TRANSACTION_ID], self::KEY_PROJECT_ID =>$task_data->project_id, 'event_type' => 'task_assign', self::KEY_APPLICATION_TABLE_NAME => self::TBL_PROJECT_TASK_DETAILS);
                $this->tocallmobilenotificationmob($mobilsendnotification);
            }
            $res['msg_otp']['whatsup'] = $this->tosendwhatsappdata($array);
            $notarry = array();
            $this->sendNotificationAndEmails($notarry,$array,99);
        }

        return $insert;
    }
    public function togetmobileprojectdetails($get){

        global $callConfig;
        $project_id = $get[self::KEY_PROJECT_ID];

        $query = "SELECT * FROM projects WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_ID.$project_id;
        $data = $callConfig->getRow($query);
        $return['project_data'] = $data;

        $query = "SELECT id, type_name FROM project_type WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $type = $callConfig->getAllRows($query);
        $return['project_type'] = $type;

        $query = "SELECT id, project_phase_name FROM project_phases WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_id=".$project_id.self::SQL_AND_IS_ACTIVE_EQ0;
        $project_phases = $callConfig->getAllRows($query);
        $return['project_phases'] = $project_phases;


        $query = "SELECT id, role_name FROM project_roles WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $project_roles = $callConfig->getAllRows($query);
        $return['project_roles'] = $project_roles;

        $query = "SELECT id,  user_id, (SELECT au.display_name FROM application_users au WHERE au.id = user_id )   as display_name, role_id, (SELECT pr.role_name FROM project_roles pr WHERE pr.id = role_id ) as role_name FROM project_teams WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_id=".$project_id.self::SQL_AND_IS_ACTIVE_EQ0;
        $project_teams = $callConfig->getAllRows($query);
        $return['project_teams'] = $project_teams;

        $query = "SELECT id, status, name as status_name, action_label FROM project_task_status WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND table_name='project_task_details' AND project_id=".$project_id.self::SQL_AND_IS_ACTIVE_EQ0;
        $project_task_status = $callConfig->getAllRows($query);
        $return['project_task_status'] = $project_task_status;

        $query = "SELECT id, display_name FROM application_users WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND role_id LIKE '%TASKDA%'  AND is_active=0";
        $users = $callConfig->getAllRows($query);
        $return['users'] = $users;
        return $return;
    }
    public function touserwisetaskbaseoninputmobile($get){
        global $callConfig;
        $whr = "AND pt.planned_start <= CURDATE()";
        if($get['type']=='W'){
            $whr = "AND pt.planned_start BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND CURDATE()";
        }
        if($get['type']=='M'){
            $whr = "AND MONTH(pt.planned_start) = MONTH(CURDATE()) AND YEAR(pt.planned_start) = YEAR(CURDATE())";
        }
        $uwhr = "";
        if($get['role_short_code']!='TASKDA_ADMIN'){
            $uwhr = self::SQL_AND_USER_ID.$get[self::KEY_USER_ID];
        }
        $query  = "SELECT pt.id as task_id, pt.project_id , pt.task_name, pt.planned_start,pt.planned_finish, pt.current_status, (SELECT name FROM project_task_status pts WHERE pts.project_id = pt.project_id AND pts.table_name = 'project_task_details' AND pts.status = pt.current_status AND pts.is_active = 0 ) as status_name, CASE WHEN pt.planned_finish < CURDATE() THEN 'Y' ELSE 'N' END AS overdue FROM project_task_details pt WHERE pt.company_id = ".$get[self::KEY_COMPANY_ID]." AND pt.current_status < ".ST_APPROVED." AND (pt.id IN (SELECT project_task_id FROM `project_task_teams` WHERE company_id = ".$get[self::KEY_COMPANY_ID]." ".$uwhr."  AND is_active =0) OR pt.assigen_id = ".$get[self::KEY_USER_ID].")".$whr." AND pt.is_active = 0 ORDER BY pt.updated_date DESC";
        $data = $callConfig->getAllRows($query);
        $asign_query = "SELECT pt.id as task_id, pt.project_id , pt.task_name, pt.planned_start,pt.planned_finish, pt.current_status, (SELECT name FROM project_task_status pts WHERE pts.project_id = pt.project_id AND pts.table_name = 'project_task_details' AND pts.status = pt.current_status AND pts.is_active = 0 ) as status_name, CASE WHEN pt.planned_finish < CURDATE() THEN 'Y' ELSE 'N' END AS overdue FROM project_task_details pt WHERE pt.company_id = ".$get[self::KEY_COMPANY_ID]." AND pt.current_status < ".ST_APPROVED." AND pt.created_by=".$get[self::KEY_USER_ID]." AND pt.is_active=0  ORDER BY pt.updated_date DESC";
        $asign_data = $callConfig->getAllRows($asign_query);
        $return['data'] = $data;
        $return['create_data'] = $asign_data;
        return $return;
    }
    public function togetchatdatabasetableandtransactionid($get){
        global $callConfig;
        $query = "SELECT table_name, transaction_id, query_text, originator, created_date, (SELECT au.display_name FROM application_users au WHERE au.id =originator ) as display_name  FROM `transaction_queries` WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND  table_name = '".$get[self::KEY_TABLE_NAME]."' AND transaction_id =".$get[self::KEY_TRANSACTION_ID].self::SQL_AND_IS_ACTIVE_0_NO_SPACE;
        return $callConfig->getAllRows($query);
    }
    public function togetchatdiscussdatadetails($get){
        global $callConfig;

        $whr = " AND ( user_id =".$get[self::KEY_USER_ID]." or created_by = ".$get[self::KEY_USER_ID]." ) ";
        if($get['role_short_code']=='TASKDA_ADMIN'){
            $whr = "";
        }

        if($get['chart_type'] == 'project_work_spaces'){
            $query ="SELECT id, name FROM project_work_spaces WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND id IN (SELECT project_work_space_id FROM projects WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND id IN (SELECT project_id FROM project_teams WHERE company_id =".$get[self::KEY_COMPANY_ID]." ".$whr." AND is_active =0 ) AND is_active =0";
        }
        if($get['chart_type'] == 'projects'){
            $query ="SELECT id,  project_name as name FROM projects WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND id IN (SELECT project_id FROM project_teams WHERE company_id =".$get[self::KEY_COMPANY_ID]." ".$whr." AND is_active =0) AND is_active =0 ";
        }
        if($get['chart_type'] == self::TBL_PROJECT_TASK_DETAILS){
            $query ="SELECT id, task_name as name FROM project_task_details WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND id IN (SELECT project_task_id  FROM project_task_teams  WHERE company_id =".$get[self::KEY_COMPANY_ID]." ".$whr." AND is_active =0) AND is_active =0 ";
        }
        $datas = $callConfig->getAllRows($query);
        foreach($datas as $dat){
            $data = (array)$dat;
            $retur['id'] = $data['id'];
            $retur['name'] = $data['name'];
            $retur['data'] = array();
            $ids[] = $data['id'];
            $return[] = $retur;
        }
        if(count((array)$ids)>0){
            $lastchart = array();
            $query = "SELECT max(id) as id, query_text, transaction_id, originator, created_date, (SELECT au.display_name FROM application_users au WHERE au.id =originator ) as display_name FROM transaction_queries WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND table_name='".$get['chart_type']."' AND transaction_id in (".implode(",", $ids).") AND is_active =0";
            $lists = $callConfig->getAllRows($query);

            foreach((array)$lists as $list){
                $lastchart[$list->transaction_id] = $list;
            }
            foreach((array)$return as $ret){
                $pass = $ret;
                    $pass['data'] =$lastchart[$ret['id']];
                //}
                $res_pass[] = $pass;
            }
        }else{
            $res_pass = $return;
        }
        return $res_pass;
    }
    public function mobileInitials($str) {
        return $this->initials($str);
    }
    public function mobileuserlogout($get){
        global $callConfig;
        $update = "update user_mobile_details set is_active = NULL, updated_by = ".$get[self::KEY_USER_ID]." where user_id ='".$get[self::KEY_USER_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $callConfig->executeQuery($update);
        $update = "update user_mobile_details set is_active = NULL, updated_by = ".$get[self::KEY_USER_ID]." where user_id ='".$get[self::KEY_USER_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $callConfig->executeQuery($update);
        $upquery = "UPDATE appliccation_user_login_tokens SET s_active = NULL, updated_by = ".$get[self::KEY_USER_ID]." WHERE company_id= ".$get[self::KEY_COMPANY_ID].self::SQL_AND_USER_ID.$get[self::KEY_USER_ID]." AND login_type = 'Mobile' AND is_active=0";
        $callConfig->executeQuery($upquery);
        return $update_data;
    }
    public function togetpersonalgatewaydetails($get){
        global $callConfig;
        $return['get'] = $get;

        $query = "SELECT id, genc_detail_code, genc_detail_name FROM general_code_details where is_active='0' and company_id='".$get[self::KEY_COMPANY_ID]."' and genc_code='GATE_METHOD_TYPE'";
        $display_details = $callConfig->getAllRows($query);
        $return['method_type'] = $display_details;
        $query = "SELECT id, emp_code, CONCAT(emp_code, ' - ', display_name) as display_name, email_id, phone FROM employees WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $emp_data  = $callConfig->getAllRows($query);
        $return['emp_data'] = $emp_data;
        $query = "SELECT id, genc_detail_code, genc_detail_name FROM general_code_details where is_active='0' and company_id='".$get[self::KEY_COMPANY_ID]."' and genc_code='VISIT_PURPOSE'";
        $vis_details = $callConfig->getAllRows($query);
        $return['visit_purpose'] = $vis_details;

        $query = "SELECT id, vehicle_reg_no, model, model_group FROM transport_vehicle_master WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $vehicle_reg_no  = $callConfig->getAllRows($query);
        $return['vehicle_reg_no'] = $vehicle_reg_no;
        $query = "SELECT id, location_name FROM locations WHERE company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $location  = $callConfig->getAllRows($query);
        $return[self::KEY_LOCATION] = $location;
        $return['need_vehicle'] = 'N';
        $return['gateway_types'] = array(['id'=> 1, 'type_name' => 'One Way'],
                                    ['id'=> 2, 'type_name' => 'Return']);
        return $return;
    }
    public function toaddpersonaldataonmobile($get, $post){
        global $callConfig;

        $person_detai = array(self::KEY_EMPLOYEE_ID=>$post[self::KEY_EMP_ID] ?? null,'employee_name'=>$post['employee_name'] ?? null,'employee_contact'=>$post['employee_contact'] ?? null);
        $person_details = json_encode($person_detai,1);
        if($get['rid']!=""){
            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_USER_ID=>$post[self::KEY_EMP_ID] ?? null,'vehicle_number'=>$post['vehicle_number'] ?? null,'notes'=>$post['notes'] ?? null,'person_details'=>$person_details,'visit_purpose'=>$post['visit_purpose'] ?? null,self::KEY_LOCATION=>$post[self::KEY_LOCATION] ?? null,'exit_date'=>$post['entry_date'] ?? null,'exit_time'=>$post['entry_time'] ?? null,'method_type'=>$post['method_type'] ?? null,'authorized_by'=>$get[self::KEY_USER_ID], self::KEY_CREATED_BY=>$get[self::KEY_USER_ID],"gateway_type" => $post['gateway_type'],"process_val" => 1);

            // $callConfig->updateRecord('transport_personal_gateway',$insert_array,'id',$get['rid']); changed by koti
            $callConfig->updateRecord(self::TBL_TRANSPORT_PERSONAL_GATEWAY,$insert_array,'id',$get['rid']);
        }else{
        $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_USER_ID=>$post[self::KEY_EMP_ID] ?? null,'vehicle_number'=>$post['vehicle_number'] ?? null,'notes'=>$post['notes'] ?? null,'person_details'=>$person_details,'visit_purpose'=>$post['visit_purpose'] ?? null,self::KEY_LOCATION=>$post[self::KEY_LOCATION] ?? null,'exit_date'=>$post['entry_date'] ?? null,'exit_time'=>$post['entry_time'] ?? null,'method_type'=>$post['method_type'] ?? null,'authorized_by'=>$get[self::KEY_USER_ID], self::KEY_IS_ACTIVE=>0,self::KEY_CURRENT_STATUS=>0,self::KEY_CREATED_BY=>$get[self::KEY_USER_ID],"gateway_type" => $post['gateway_type'],"process_val" => 1);
            $callConfig->insertRecord(self::TBL_TRANSPORT_PERSONAL_GATEWAY,$insert_array);
        }
        // emp_id, employee_name, employee_contact, vehicle_number, visit_purpose, location, entry_date, entry_time, method_type,
        return $res;
    }
    public function toupdatepersonaldataonmobile($get, $post){
        global $callConfig;
        $person_detai = array(self::KEY_EMPLOYEE_ID=>$post[self::KEY_EMP_ID],'employee_name'=>$post['employee_name'],'employee_contact'=>$post['employee_contact']);
        $person_details = json_encode($person_detai,1);
        $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_USER_ID=>$post[self::KEY_EMP_ID],'vehicle_number'=>$post['vehicle_number'],'person_details'=>$person_details,'visit_purpose'=>$post['visit_purpose'],self::KEY_LOCATION=>$post[self::KEY_LOCATION],'entry_date'=>$post['entry_date'],'entry_time'=>$post['entry_time'],'method_type'=>$post['method_type'],'authorized_by'=>$get[self::KEY_USER_ID], self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);

        // emp_id, employee_name, employee_contact, vehicle_number, visit_purpose, location, entry_date, entry_time, method_type,
        return $callConfig->updateRecord(self::TBL_TRANSPORT_PERSONAL_GATEWAY,$insert_array,'id',$get['trn_id']);
    }
    public function getAttendanceRegularizationRequestData($get){
        global $callConfig;
        $return  = array();
        $company_id = $get[self::KEY_COMPANY_ID];
        $query ="select id, reason_name from attedenace_regularizaton_reasons where company_id={$company_id} AND is_active=0";
        $res = $callConfig->getAllRows($query);
        $return['reasons'] = $res;

        if($get['id']){
            $query ="select * from attendenace_regularization_requests where id={$get['id']} AND company_id={$company_id} AND is_active=0";
            $res = $callConfig->getRow($query);
            $return['request'] = $res;

        }
        return $return;
    }

    public function saveAttendanceRegularizationRequestData($get, $post){
        global $callConfig;
        $return  = array();
        $insertary = array(
            self::KEY_COMPANY_ID => $get[self::KEY_COMPANY_ID],
            self::KEY_EMPLOYEE_ID => $post[self::KEY_EMPLOYEE_ID],
            'request_date' => $post['request_date'],
            'regularization_date' => $post['regularization_date'],
            'regularization_of_checkin' => $post['regularization_of_checkin'],
            'regularization_of_checkout' => $post['regularization_of_checkout'],
            'regularization_reason' => $post['regularization_reason'],
            self::KEY_REMARKS => $post[self::KEY_REMARKS],
            'image' => $post['image'],
            'loc_latitude' => $post['loc_latitude'],
            'loc_longitude' => $post['loc_longitude']
            );
        if ($post['rid'] != "") {
            $insertary[self::KEY_UPDATED_DATE] = date(self::DATETIME_FORMAT_YMD_HIS);
            $insertary[self::KEY_UPDATED_BY] = $get[self::KEY_USER_ID];
            $res = $callConfig->updateRecord('attendenace_regularization_requests', $insertary, 'id', $post['rid']);
            $return['res'] = $res;
        } else {
            $insertary[self::KEY_IS_ACTIVE] = 0;
            $insertary[self::KEY_CURRENT_STATUS] = 0;
            $insertary['status_date'] = date(self::DATETIME_FORMAT_YMD_HIS);
            $insertary[self::KEY_CREATED_BY] = $get[self::KEY_USER_ID];
            $insertary[self::KEY_CREATED_DATE] = date(self::DATETIME_FORMAT_YMD_HIS);
            $res = $callConfig->insertRecord('attendenace_regularization_requests', $insertary);
            $return['res'] = $res;
        }
        return $return;

    }

    public function getTransportVehicleGatePassDeatails($get){
        global $callConfig;
        $return = array();
        $company_id = $get[self::KEY_COMPANY_ID];

        $statusQuery = "SELECT id, name, name_value, background_color, color FROM status
                        WHERE company_id = " . $company_id . "
                        AND is_active = 0";
        $statusList = $callConfig->getAllRows($statusQuery);
        $return['status_list'] = $statusList;



        $vehicle_id  = $get['vehicle_id'] ?? '';
        if($vehicle_id != ''){
            $vehicle = $callConfig->getRow("SELECT * FROM transport_vehicle_master WHERE id={$vehicle_id} AND company_id = {$company_id} AND is_active = 0 ");
            $return['vehicle'] = $vehicle;

            $employees = $callConfig->getAllRows("SELECT id, emp_code, CONCAT(emp_code, ' - ', display_name) as display_name, email_id, phone FROM employees WHERE company_id = {$company_id} AND is_active = 0 ");
            $return['drivers'] = $employees;

            $return['demo_types'] = array(
                'demo' => 'Demo Car',
                'car' => 'Car',
                'truck' => 'Truck',
                'van' => 'Van',
                'motorcycle' => 'Motorcycle',
                'bus' => 'Bus',
                'trailer' => 'Trailer',
                'other' => 'Other'
            );
            $return['gate_pass_types'] = array(
                'demo' => 'Demo ',
                'interbranch' => 'Interbranch',
                'event' => 'Event',
                'petrolpump' => 'Petrolpump',
                'replacement' => 'Peplacement',
                'testdrive_showroom' => 'Testdrive - Showroom',
                'testdrive_doorstep' => 'Testdrive - Doorstep',
                'customer_visit' => 'Customer - Visit',
                'other' => 'Other'
            );

        }else{
            $where = "" ;
            if (isset($get[self::KEY_STATUS]) && $get[self::KEY_STATUS] != '') {
                $where .= " AND m.current_status = " . intval($get[self::KEY_STATUS]);
            }
            if (!empty($get['from_date']) && !empty($get['to_date'])) {
                $from_date = $get['from_date'];
                $to_date = $get['to_date'];
                $where .= " AND m.DATE(created_date) BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
            }

            $vehicles = $callConfig->getAllRows("SELECT  m.* ,    IFNULL(
                (
                    SELECT
                        CASE
                            WHEN tg.process_val = 1 THEN 'OUT'
                            WHEN tg.process_val = 2 THEN 'IN'
                            ELSE 'IN'
                        END
                    FROM transport_gateway tg
                    WHERE tg.vehicle_number = m.vehicle_reg_no
                      AND tg.is_active = 0
                    ORDER BY tg.id DESC
                    LIMIT 1
                ),
                'IN'
            ) AS vehicle_status
         FROM transport_vehicle_master m WHERE m.company_id = " . $company_id. " AND  m.is_active = 0  AND m.vehicle_reg_no != '' AND m.vehicle_type = 'demo' $where  ORDER BY m.vehicle_reg_no ASC");

            $return['vehicles'] = $vehicles;
        }

        return $return;
    }

    public function savevehiclegatepass($get, $post){
        global $callConfig;

        $method_type = 'exit_first';

        $time_value = $post['out_time'] ?? date('H:i:s');
        $date_value = $post['out_date'] ?? date(self::DATE_FORMAT_YMD);

        $fieldnames = array(
            self::KEY_COMPANY_ID => $get[self::KEY_COMPANY_ID],
            self::KEY_USER_ID => $get[self::KEY_USER_ID],
            'vehicle_number' => $post['vehicle_number'],
            'vehicle_type' => $post['demo_type'] ?? '',
            'driver_name' => $post['customer_name'] ?? '',
            'driver_contact' => $post['customer_contact'] ?? '',
            self::KEY_EMPLOYEE_ID => $post[self::KEY_EMPLOYEE_ID] ?? '',
            'gate_pass' => $post['gate_pass'] ?? '',
            'enquriy_no' => $post['enquiry_no'] ?? '',
            'notes' => $post[self::KEY_REMARKS] ?? '',
            'emp_ids'=> $post['emp_ids']??'',
            self::KEY_LOCATION=> $post[self::KEY_LOCATION]??'',
            'method_type' => $method_type,
            "process_val" => 1
        );

        $fieldnames['exit_date'] = $date_value;
        $fieldnames['exit_time'] = $time_value;
        $fieldnames['exit_timestamp'] = date(self::DATETIME_FORMAT_YMD_HIS);

        if($get['rid'] != ''){
            $fieldnames[self::KEY_UPDATED_BY] = $get[self::KEY_USER_ID];
            $fieldnames[self::KEY_UPDATED_DATE] = date(self::DATETIME_FORMAT_YMD_HIS);
            $res = $callConfig->updateRecord(self::TBL_TRANSPORT_GATEWAY, $fieldnames, 'id', $get['rid']);

        }else{
            $fieldnames[self::KEY_IS_ACTIVE] = 0;
            $fieldnames[self::KEY_CURRENT_STATUS] = 0;
            $fieldnames[self::KEY_CREATED_BY] = $get[self::KEY_USER_ID];
            $fieldnames[self::KEY_CREATED_DATE] = date(self::DATETIME_FORMAT_YMD_HIS);
            $res = $callConfig->insertRecord(self::TBL_TRANSPORT_GATEWAY, $fieldnames);
        }

        return $res;
    }

    private function resolveDynamicPartyBilling($companyId, $partyId, $transactionTable, $placeOfSupply, $paymentTerms, $originatorStateId){
        global $callConfig;
        $display_attribute_typessql = "SELECT content FROM display_attribute_types WHERE company_id = '".$companyId."' AND application_table_name = '".$transactionTable."' AND field_name = 'party_id'";
        $parttable_name = $callConfig->getRow($display_attribute_typessql);
        $qquery = urldecode($parttable_name->content);
        $explosql = explode("FROM", $qquery);
        if(isset($explosql[1])){
            $explosql = explode(" ", trim($explosql[1]));
        }
        $partytable = trim($explosql[0]);
        $partyres = null;
        if($partytable !=""){
            $partysql = "SELECT * FROM ".$partytable." WHERE company_id = ".$companyId." AND id = ".$partyId.self::SQL_AND_IS_ACTIVE_0;
            $partyres = $callConfig->getRow($partysql);
        }

        if($placeOfSupply !=""){
            $sqlgstbill = "SELECT * FROM `customer_vendor_gst_billing_details` WHERE company_id = ".$companyId." AND id = '".$placeOfSupply.self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $gstbillres = $callConfig->getRow($sqlgstbill);
            $party_tax_number = $gstbillres->gstin!=""?$gstbillres->gstin:$partyres->gstin;
            $party_phone = $gstbillres->phone!=""?$gstbillres->phone:$partyres->phone;
            $party_address = $gstbillres->billing_address!=""?$gstbillres->billing_address:$partyres->billing_address;
            $party_ship_adderss = $gstbillres->shipping_address!=""?$gstbillres->shipping_address:$partyres->shipping_address;
            $supplier_state_id= $gstbillres->shipping_state!=""?$gstbillres->shipping_state:$partyres->shipping_state;
            $party_email= $gstbillres->email!=""?$gstbillres->email:$partyres->email;
        }else{
            $party_tax_number = $partyres->gstin;
            $party_phone = $partyres->phone;
            $party_address = $partyres->billing_address;
            $party_ship_adderss = $partyres->shipping_address;
            $supplier_state_id=  $partyres->shipping_state;
            $party_email = $partyres->email;
        }
        $tax_state = ($originatorStateId == $supplier_state_id) ? "S" : "I";
        $resolvedTerms = $paymentTerms!=""?$paymentTerms:$partyres->payment_terms;
        return [$partyres, $party_tax_number, $party_phone, $party_address, $party_ship_adderss, $supplier_state_id, $party_email, $tax_state, $resolvedTerms];
    }

    private function buildAdditionalChargesSummary($companyId, $additionalCharges){
        global $callConfig;
        $additional_charge = array();
        $shipping_tax = $shipping_amount = $shipping_tax_amount = 0;
        foreach($additionalCharges as $acharge){
            $adicharesql = "SELECT ac.id, ac.charge_name, hsn.gst FROM additional_charges ac LEFT JOIN hsn_and_sac_codes hsn ON ac.hsn_and_sac_code_id = hsn.id WHERE ac.company_id = '".$companyId."' AND ac.charge_name like '%".$acharge['charge_name']."%' AND ac.is_active = 0";
            $adichareres = $callConfig->getRow($adicharesql);
            $charge_name = $adichareres->charge_name!=""?$adichareres->charge_name:$acharge['charge_name'];
            $charge_id = $adichareres->id!=""?$adichareres->id:0;
            $shipping_tax_amount = $acharge[self::KEY_TAX_AMOUNT]+$acharge['charge_amount'];
            $additional_charge[] = array("shipping_tax"=>$adichareres->gst,"shipping_amount"=>$acharge['charge_amount'],"shipping_tax_amount"=>$acharge[self::KEY_TAX_AMOUNT],"charge_name"=>$charge_name,"charge_id"=>$charge_id);

            $shipping_tax +=$acharge[self::KEY_TAX_AMOUNT];
            $shipping_amount +=$acharge['charge_amount'];
            $shipping_tax_amount += $shipping_tax_amount;
        }
        return [json_encode($additional_charge), $shipping_tax, $shipping_amount, $shipping_tax_amount];
    }

    private function upsertDynamicTransactionItems($companyId, $insertId, $refTableDmtres, $post){
        global $callConfig;
        $itemInsertData = [];
        $taxInsertData = [];
        foreach($post['items'] as $itemsdetails){
            $itemfieldnames=array(self::KEY_COMPANY_ID=>$companyId,self::KEY_TRANSACTION_ID=>$insertId,self::KEY_ITEM_ID=>$itemsdetails[self::KEY_ITEM_ID],'item_name'=>$itemsdetails['item_name'],'description'=>$itemsdetails['description'],'purchase_uom'=>$itemsdetails['purchase_uom'],'item_quantity'=>$itemsdetails['item_quantity'],'item_price'=>$itemsdetails['item_price'],'item_amount'=>$itemsdetails['item_amount'],self::KEY_TAX_AMOUNT=>$itemsdetails[self::KEY_TAX_AMOUNT],self::KEY_TOTAL_AMOUNT=>$itemsdetails[self::KEY_TOTAL_AMOUNT],'pending_qty'=>$itemsdetails['item_quantity'],'transaction_qty'=>$itemsdetails['item_quantity'],self::KEY_IS_ACTIVE=>'0','discount_amount'=>$itemsdetails['discount_amount'],'ref_transaction_item_table_id'=>$itemsdetails['ref_transaction_item_table_id'],'ref_transaction_table_id'=>$itemsdetails['ref_transaction_table_id']);
            if($itemsdetails['rid'] !=""){
                $itemfieldnames[self::KEY_UPDATED_BY]=$post[self::KEY_USER_ID];
                $itemfieldnames[self::KEY_UPDATED_DATE]=date(self::DATETIME_FORMAT_YMD_HIS);
                $childtableres=$callConfig->updateRecord($refTableDmtres->item_table,$itemfieldnames,'id',$itemsdetails['rid']);
                $childtableres_id = $itemsdetails['rid'];
            }else{
                $itemfieldnames[self::KEY_CURRENT_STATUS]=0;
                $itemfieldnames[self::KEY_CREATED_BY]=$post[self::KEY_USER_ID];
                $itemfieldnames[self::KEY_CREATED_DATE]=date(self::DATETIME_FORMAT_YMD_HIS);
                $childtableres=$callConfig->insertRecord($refTableDmtres->item_table,$itemfieldnames);
                $childtableres_id = $childtableres[self::KEY_LASTID];
            }
            $itemInsertData[] = $childtableres;

            if($childtableres[self::KEY_STATUS] == 'sucess' && $itemsdetails[self::KEY_TAX_AMOUNT]!="0" && $itemsdetails[self::KEY_TAX_AMOUNT]!=""){
                $taxfield=array(self::KEY_COMPANY_ID=>$companyId,self::KEY_TRANSACTION_ID=>$insertId,'transaction_item_id'=>$itemsdetails[self::KEY_ITEM_ID],'tax_id'=>$itemsdetails['tax_id'],'tax_name'=>$itemsdetails['tax_name'],'amount'=>$itemsdetails['item_taxable_amount'],self::KEY_TAX_AMOUNT=>$itemsdetails[self::KEY_TAX_AMOUNT],self::KEY_TOTAL_AMOUNT=>$itemsdetails[self::KEY_TOTAL_AMOUNT],'tax_value'=>$itemsdetails[self::KEY_PERCENTAGE],self::KEY_IS_ACTIVE=>'0','transaction_item_row_id'=>$childtableres_id);

                if($itemsdetails['tax_row_rid'] !=""){
                    $taxfield[self::KEY_UPDATED_BY]=$post[self::KEY_USER_ID];
                    $taxfield[self::KEY_UPDATED_DATE]=date(self::DATETIME_FORMAT_YMD_HIS);
                    $childtabletaxres=$callConfig->updateRecord($refTableDmtres->tax_table,$taxfield,'id',$itemsdetails['tax_row_rid']);
                }else{
                    $taxfield[self::KEY_CURRENT_STATUS]=0;
                    $taxfield[self::KEY_CREATED_BY]=$post[self::KEY_USER_ID];
                    $taxfield[self::KEY_CREATED_DATE]=date(self::DATETIME_FORMAT_YMD_HIS);
                    $childtabletaxres=$callConfig->insertRecord($refTableDmtres->tax_table,$taxfield);
                }
                $taxInsertData[] = $childtabletaxres;
            }
        }
        return [$itemInsertData, $taxInsertData];
    }

    public function insertMobiDynamicMappingTransactions($get, $post){
        global $callConfig;
        $return = array('get' => $get, 'post' => $post);

        $transaction_table= $post[self::KEY_TRANSACTION_TABLE];
        $transaction_date = $this->dateconvertdbformat($post[self::KEY_TRANSACTION_DATE]);
        $originator= $post['originator'];
        $party_id= $post[self::KEY_PARTY_ID];
        $place_of_supply= $post['place_of_supply'];
        $amount= $post['amount'];
        $tax_amount= $post[self::KEY_TAX_AMOUNT];
        $total_amount= $_POST[self::KEY_TOTAL_AMOUNT];
        $doc_ref_no= $_POST['doc_ref_no'];
        $doc_ref_date= $this->dateconvertdbformat($_POST['doc_ref_date']);
        $ref_type= $_POST['ref_type'];
        $ref_id= $_POST[self::KEY_REF_ID];
        $ref_no= $_POST['ref_no'];
        $ref_date = $this->dateconvertdbformat($_POST['ref_date']);
        $payment_terms = $_POST['payment_terms'];

        $ref_table_dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$post[self::KEY_COMPANY_ID]."' AND application_table_name = '".$transaction_table."' AND is_active = 0 AND item_table !='' AND tax_table!=''";
        $ref_table_dmtres=$callConfig->getRow($ref_table_dmtsql);

        if($ref_table_dmtres->item_table !="" && $ref_table_dmtres->tax_table !=""){
            $originatorsql = "SELECT * FROM application_users WHERE company_id = '".$post[self::KEY_COMPANY_ID]."' AND id = '".$originator.self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $originatorres=$callConfig->getRow($originatorsql);

            list($partyres, $party_tax_number, $party_phone, $party_address, $party_ship_adderss, $supplier_state_id, $party_email, $tax_state, $payment_terms) = $this->resolveDynamicPartyBilling($post[self::KEY_COMPANY_ID], $party_id, $transaction_table, $place_of_supply, $payment_terms, $originatorres->default_state_id);

            $mainfieldsary=array(self::KEY_COMPANY_ID=>$post[self::KEY_COMPANY_ID],self::KEY_TRANSACTION_DATE=>$transaction_date,'transaction_location_id'=>$originatorres->location_id,'transaction_warehouse_id'=>$originatorres->default_warehouse_id,'currency_id'=>$originatorres->default_currency_id,'currency_rate'=>1,'originator'=>$originator,self::KEY_PARTY_ID=>$partyres->id,'party_name'=>$partyres->company,'party_tax_number'=>$party_tax_number,'party_email'=>$party_email,'party_phone'=>$party_phone,'party_address'=>$party_address,'party_ship_adderss'=>$party_ship_adderss,'place_of_supply'=>$place_of_supply,'payment_terms'=>$payment_terms,'amount'=>$amount,self::KEY_TAX_AMOUNT=>$tax_amount,self::KEY_TOTAL_AMOUNT=>$total_amount,'ref_type'=>$ref_type,self::KEY_REF_ID=>$ref_id,'ref_no'=>$ref_no,'ref_date'=>$ref_date,'cost_center_id'=>$originatorres->cost_center,'originator_currency_id'=>$originatorres->default_currency_id,'originator_state_id'=>$originatorres->default_state_id,'supplier_state_id'=>$supplier_state_id,'due_date'=>$transaction_date, 'pending_amount'=>$total_amount,'doc_ref_no'=>$doc_ref_no,'doc_ref_date'=>$doc_ref_date,'tax_state'=>$tax_state,self::KEY_CURRENT_STATUS=>ST_ACTIVE);

            if($post['discount_amount'] !="" && $post['discount_amount'] !="0"){
                $mainfieldsary['discount_type'] = 'A';
                $mainfieldsary['discount_value'] = $post['discount_amount'];
                $mainfieldsary['discount_amount'] = $post['discount_amount'];
            }
            if($post['rounding_off'] !="" && $post['rounding_off'] !="0"){
                $mainfieldsary['rounding_off'] = $post['rounding_off'];
            }
            if($post['additional_charges'] !=""){
                list($chargesJson, $shippingTax, $shippingAmount, $shippingTaxAmount) = $this->buildAdditionalChargesSummary($post[self::KEY_COMPANY_ID], $post['additional_charges']);
                $mainfieldsary['additional_charges'] = $chargesJson;
                $mainfieldsary['shipping_tax'] = $shippingTax;
                $mainfieldsary['shipping_amount'] = $shippingAmount;
                $mainfieldsary['shipping_tax_amount'] = $shippingTaxAmount;
            }

            if($post['rid'] !=""){
                $mainfieldsary[self::KEY_UPDATED_BY]=$post[self::KEY_USER_ID];
                $mainfieldsary[self::KEY_UPDATED_DATE]=date(self::DATETIME_FORMAT_YMD_HIS);
                $insert_data=$callConfig->updateRecord($transaction_table,$mainfieldsary,'id',$post['rid']);
                $insert_id = $post['rid'];
                $callConfig->updateRecord($ref_table_dmtres->item_table,array(self::KEY_IS_ACTIVE=>null),self::KEY_TRANSACTION_ID,$post['rid']);
                $callConfig->updateRecord($ref_table_dmtres->tax_table,array(self::KEY_IS_ACTIVE=>null),self::KEY_TRANSACTION_ID,$post['rid']);
            }else{
                $post[self::KEY_TRANSACTION_DATE] = '';
                $post[self::KEY_PARTY_ID] = $party_id;
                $post['transaction_type'] = '';
                $transaction_number_details = $this->generatingApplicationSerialNumberMasters($transaction_table,$post);
                $mainfieldsary[self::KEY_TRANSACTION_NUMBER] = $transaction_number_details['main_transaction_number'];
                $mainfieldsary['monthly_transaction_number'] = $transaction_number_details['monthly_transaction_number'];
                $mainfieldsary['yearly_transaction_number'] = $transaction_number_details['yearly_transaction_number'];
                $mainfieldsary['lifetime_transaction_number'] = $transaction_number_details['lifetime_transaction_number'];

                $mainfieldsary[self::KEY_IS_ACTIVE]=0;
                $mainfieldsary[self::KEY_CURRENT_STATUS]=0;
                $mainfieldsary[self::KEY_CREATED_BY]=$post[self::KEY_USER_ID];
                $mainfieldsary[self::KEY_CREATED_DATE]=date(self::DATETIME_FORMAT_YMD_HIS);
                $insert_data=$callConfig->insertRecord($transaction_table,$mainfieldsary);
                $insert_id = $insert_data[self::KEY_LASTID];
                $return['insert_data'] = $insert_data;
            }
        }

        if($insert_data[self::KEY_STATUS] == 'sucess'){
            list($itemInsertData, $taxInsertData) = $this->upsertDynamicTransactionItems($post[self::KEY_COMPANY_ID], $insert_id, $ref_table_dmtres, $post);
            $return['item_insert_data'] = $itemInsertData;
            if(!empty($taxInsertData)){
                $return['tax_insert_data'] = $taxInsertData;
            }
        }

        return $return;
    }
    public function getEmailWithDBAndCompanyID($get){
        global $callConfig;
        $user_email = $get[self::KEY_USER_EMAIL];
        $aiemailquery = "SELECT id,zqr_connect, email_id, is_active FROM ".MAINDB.".ai_email_configurations WHERE email_id= '".$user_email.self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $aiemailres = $callConfig->getRow($aiemailquery);
        if($aiemailres->is_active !='0'){
            return array(
                self::KEY_STATUS => self::KEY_ERROR,
                "msg" => "Email ID is not activated."
            );
        }elseif($aiemailres->zqr_connect =='0' || $aiemailres->zqr_connect ==''){
            return array(
                self::KEY_STATUS => self::KEY_ERROR,
                "msg" => "Database is not assigned. Please contact the administrator."
            );
        }elseif($aiemailres->id != ''){

            $dbaquery = "SELECT id,db_name FROM ".MAINDB.".".TBL_DATABASE_ALLOCATIONS." WHERE id= '".$aiemailres->zqr_connect."'";
            $dbares = $callConfig->getRow($dbaquery);
            if($dbares->db_name !=''){
                $db_name = $dbares->db_name;
                $returnary = array(self::KEY_COMPANY_ID=>'1',self::KEY_USER_ID=>'1','db_name'=>$db_name);
                return array(
                    self::KEY_STATUS => self::KEY_SUCCESS,
                    "msg" => "Record fetched successfully.",
                    "result"=>$returnary
                );
            }else{
                return array(
                    self::KEY_STATUS => self::KEY_ERROR,
                    "msg" => "Database not found. Please contact the administrator."
                );
            }
        }else{
            return array(
                self::KEY_STATUS => self::KEY_ERROR,
                "msg" => "Oops! Something went wrong. Please contact the administrator."
            );
        }
    }
    public function GETProcurementModulesDashboardSummaryStock($get){
        global $callConfig;
        $return = array();

        $limit_row = $get['limit_row']!=""?$get['limit_row']:100;
        $current_page = $get['current_page']!=""?$get['current_page']:0;
        $current_row = $limit_row * $current_page;
        $current_row = ($current_page != 0)?($current_row-1):0;


        $query = "SELECT id, sku, item_name, item_description, sale_price, purchase_uom, hsn_and_sac_code_id, minimum_level, warehouse_id, danger_level, reorder_qty, reorder_days, maximum_capacity FROM items WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND is_active = 0 LIMIT ".$current_row.",".$limit_row;
        $itemsres = $callConfig->getAllRows($query);
        $items = array();
        foreach($itemsres as $itm){
            $items[$itm->id] = $itm;
        }
        $return['items'] = $items;

        $balance_date = $this->dateconvertdbformat(date(self::DATE_FORMAT_YMD));
        $dmtsql = "SELECT itm.id item_id, itm.sku, itm.item_name, iwb.balance_date, iwb.opening_qty, iwb.in_qty, iwb.out_qty, iwb.closing_qty as balance_qty FROM items itm, item_wa_balances iwb WHERE itm.company_id = '".$get[self::KEY_COMPANY_ID]."' AND itm.company_id = iwb.company_id AND itm.id = iwb.item_id AND iwb.balance_date = (SELECT MAX(iwb1.balance_date) FROM item_wa_balances iwb1 WHERE itm.company_id = iwb1.company_id AND iwb1.balance_date <= '".$balance_date."' AND itm.id = iwb1.item_id )";
        $dmtres = $callConfig->getAllRows($dmtsql);
        foreach($dmtres as $itm){
            if(isset($items[$itm->item_id])){
                $items[$itm->item_id]->stock_list = $itm;
            }
        }

        $sql = "SELECT poi.item_id, poi.item_name, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN poi.item_quantity ELSE 0 END) AS order_completed_qty, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN poi.item_quantity ELSE 0 END) AS only_created_qty, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN poi.item_quantity ELSE 0 END) AS inprogress_qty FROM purchase_orders po, po_items poi WHERE po.company_id = '".$get[self::KEY_COMPANY_ID]."' AND po.company_id =poi.company_id AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N'  OR po.cancel_invoice = '') AND po.id = poi.transaction_id AND po.is_active = 0 AND po.is_active = poi.is_active GROUP BY poi.item_id, poi.item_name, po.current_status";
        $sqlres = $callConfig->getAllRows($sql);
        foreach($sqlres as $itm){
            if(isset($items[$itm->item_id])){
                $items[$itm->item_id]->Purchase_orders = $itm;
            }
        }

        $sql = "SELECT poi.item_id, poi.item_name, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN poi.item_quantity ELSE 0 END) AS order_completed_qty, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN poi.item_quantity ELSE 0 END) AS only_created_qty, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN poi.item_quantity ELSE 0 END) AS inprogress_qty FROM sales_orders po, so_items poi WHERE po.company_id = '".$get[self::KEY_COMPANY_ID]."' AND po.company_id =poi.company_id AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N'  OR po.cancel_invoice = '') AND po.id = poi.transaction_id AND po.is_active = 0 AND po.is_active = poi.is_active GROUP BY poi.item_id, poi.item_name, po.current_status";
        $sqlres = $callConfig->getAllRows($sql);
        foreach($sqlres as $itm){
            if(isset($items[$itm->item_id])){
                $items[$itm->item_id]->sales_orders = $itm;
            }
        }


        return $return;
    }



    public function GETProcurementModulesDashboardSummaryTest($get){
        global $callConfig;
        $return = array();
        $user_id= $get[self::KEY_USER_ID];
        $useridcheck = "Y";
        if($useridcheck == 'Y'){
            $notificationswhere = " AND notiusr.user_id = '".$user_id."'";
            $transaction_approval_logswhere = " AND user_id = '".$user_id."'";
            $transaction_activity_schedulewhere = " AND tas.transaction_id = '".$user_id."'";
        }


        $payment_termssql = "SELECT id, payment_term_name, content FROM payment_terms WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $payment_termsres = $callConfig->getAllRows($payment_termssql);
        $payment_terms = array();
        foreach($payment_termsres as $payterm){
            $payment_terms[$payterm->id] = $payterm->payment_term_name;
        }




        $paymentssql = "SELECT pay.transaction_type, pay.payment_date, paya.party_id, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.total_paid_amonut  FROM receipts pay, receipt_allocations paya WHERE pay.company_id = 1 AND pay.company_id = paya.company_id AND pay.id = paya.receipt_id AND pay.is_active = 0 AND pay.current_status = 199 AND paya.transaction_table IN('sales_invoices','purchase_invoices')";
        $paymentsres = $callConfig->getAllRows($paymentssql);
        $return['customer_payments'] = $paymentsres;

        $sql1 = "SELECT id, display_name FROM application_users WHERE company_id = '".$get[self::KEY_COMPANY_ID]."'  AND is_active =0";
        $data1 = $callConfig->getAllRows($sql1);
        $return['application_users'] = $data1;

        $sql = "SELECT notiusr.notification_id, notiusr.notification_type, notiusr.read_status, noti.notification_message, noti.notification_datetime, noti.application_table_name, noti.transaction_number, noti.notification_details FROM notification_user notiusr, notifications noti WHERE noti.company_id = notiusr.company_id ".$notificationswhere." AND noti.id = notiusr.notification_id AND noti.company_id = '".$get[self::KEY_COMPANY_ID]."' AND notiusr.notification_type = 'ACTION' AND notiusr.read_status =0 ORDER BY noti.notification_datetime DESC";
        $data = $callConfig->getAllRows($sql);
        $return['notifications'] = $data;

        $transaction_approval_logssql = "SELECT transaction_table,transaction_id, response_type,user_id,request_date,response_date FROM transaction_approval_logs WHERE send_back_flag = 0 AND call_back = 1 AND response_type ='0' ".$transaction_approval_logswhere;
        $transaction_approval_logsres=$callConfig->getAllRows($transaction_approval_logssql);
        $return['transaction_approval_logs'] = $transaction_approval_logsres;

        $taskactivitysql = "SELECT tas.id, tas.action_date, tas.task_name FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get[self::KEY_COMPANY_ID]."'  ".$transaction_activity_schedulewhere." AND tas.is_active = 0";
        $taskactivityres = $callConfig->getAllRows($taskactivitysql);
        $return['transaction_activity_schedule'] = $taskactivityres;
        //credit_limit

        $sql = "SELECT po.party_id, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN (po.amount-po.pending_amount) ELSE 0 END) AS inv_pending_amount, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS only_created_amount, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS inprogress_amount FROM purchase_invoices po WHERE po.company_id = '".$get[self::KEY_COMPANY_ID]."' AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N' OR po.cancel_invoice = '') AND po.is_active = 0 GROUP BY po.party_id";
        $sqlres = $callConfig->getAllRows($sql);
        $party_purchase_invoices = array();
        foreach($sqlres as $pires){
            $party_purchase_invoices[$pires->party_id] = $pires;
        }

        $sql = "SELECT po.party_id, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN (po.amount-po.pending_amount) ELSE 0 END) AS inv_pending_amount, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS only_created_amount, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS inprogress_amount FROM sales_invoices po WHERE po.company_id = '".$get[self::KEY_COMPANY_ID]."' AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N' OR po.cancel_invoice = '') AND po.is_active = 0 GROUP BY po.party_id";
        $sqlres = $callConfig->getAllRows($sql);
        $party_sales_invoices = array();
        foreach($sqlres as $sires){
            $party_sales_invoices[$pires->party_id] = $sires;
        }

        $vendorssql = "SELECT `id`, `company`, `display_name`, `billing_address`, `billing_city`, `billing_state`, `billing_post_code`, `billing_country`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_post_code`, `shipping_country`, `bank_account_name`, `bank_name`, `account_type`, `bank_account_no`, `payment_terms` ,`credit_limit` FROM `vendors` WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $vendorsres = $callConfig->getAllRows($vendorssql);
        $vendors = array();
        foreach($vendorsres as $vinres){
            $vinres->party_purchase_invoices = isset($party_purchase_invoices[$vinres->id])?$party_purchase_invoices[$vinres->id]:array();
            $vendors[] = $vinres;
        }
        $return['vendors'] = $vendorsres;

        $customerssql = "SELECT `id`, `company`, `display_name`, `billing_address`, `billing_city`, `billing_state`, `billing_post_code`, `billing_country`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_post_code`, `shipping_country`, `payment_terms` ,`credit_limit` FROM `customers` WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $customersres = $callConfig->getAllRows($customerssql);
        $customers = array();
        foreach($customersres as $custres){
            $custres->party_sales_invoices = isset($party_sales_invoices[$custres->id])?$party_sales_invoices[$custres->id]:array();
            $customers[] = $custres;
        }
        $return['customers'] = $customers;


        //purchase_invoices list
        $sql1 = "SELECT pi.id, pi.transaction_number, pi.transaction_date, pi.party_id, pi.payment_terms, pii.id pi_id, pii.item_id, pii.item_name FROM purchase_invoices pi, pi_items pii WHERE pi.company_id = '".$get[self::KEY_COMPANY_ID]."' AND pi.current_status = ".ST_APPROVED." AND  pi.id = pii.transaction_id AND  pi.is_active = 0 AND  pi.is_active = pii.is_active";
        $res1 = $callConfig->getAllRows($sql1);
        $purchase_invoices = array();
        foreach($res1 as $resr){
            $resr->payments = array();
            if(isset($payment_terms[$resr->payment_terms])){
                $resr->payment_term_name =$payment_terms[$resr->payment_terms];
            }else{
                $resr->payment_term_name =null;
            }
            $purchase_invoices[$resr->id] = $resr;
        }
        foreach($res1 as $resr){
            $purchase_invoices[$resr->id]->items[$resr->pi_id] = array('id' => $resr->pi_id, self::KEY_ITEM_ID => $resr->item_id,'item_name' => $resr->item_name);
        }

        //sales_invoices list
        $sql1 = "SELECT pi.id, pi.transaction_number, pi.transaction_date, pi.party_id, pi.payment_terms, pii.id pi_id, pii.item_id, pii.item_name FROM sales_invoices pi, si_items pii WHERE pi.company_id = '".$get[self::KEY_COMPANY_ID]."' AND pi.current_status = ".ST_APPROVED." AND  pi.id = pii.transaction_id AND  pi.is_active = 0 AND  pi.is_active = pii.is_active";
        $res1 = $callConfig->getAllRows($sql1);
        $sales_invoices = array();
        foreach($res1 as $resr){
            $resr->payments = array();
            if(isset($payment_terms[$resr->payment_terms])){
                $resr->payment_term_name =$payment_terms[$resr->payment_terms];
            }else{
                $resr->payment_term_name =null;
            }
            $sales_invoices[$resr->id] = $resr;
        }
        foreach($res1 as $resr){
            $sales_invoices[$resr->id]->items[$resr->pi_id] = array('id' => $resr->pi_id, self::KEY_ITEM_ID => $resr->item_id,'item_name' => $resr->item_name);
        }
        //sales_invoices list

        $payment_terms_transsql = "SELECT id, transaction_table, transaction_id, party_id, due_date, amount, tax_amount, due_amount, pending_amount FROM payment_term_transactions WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND is_active = 0 AND transaction_table IN('sales_invoices','purchase_invoices')";
        $payment_terms_transres = $callConfig->getAllRows($payment_terms_transsql);
        foreach($payment_terms_transres as $paytermtran){
            if($paytermtran->transaction_table == "purchase_invoices" && isset($purchase_invoices[$paytermtran->transaction_id])){
                $purchase_invoices[$paytermtran->transaction_id]->payment_term_transactions[$paytermtran->id] =  $paytermtran;
            }
            if($paytermtran->transaction_table == "sales_invoices" && isset($sales_invoices[$paytermtran->transaction_id])){
                $sales_invoices[$paytermtran->transaction_id]->payment_term_transactions[$paytermtran->id] =  $paytermtran;
            }
        }
        $paymentssql = "SELECT paya.id, pay.transaction_type, pay.payment_date, paya.party_id, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.total_paid_amonut  FROM payments pay, payment_allocations paya WHERE pay.company_id = 1 AND pay.company_id = paya.company_id AND pay.id = paya.payment_id AND pay.is_active = 0 AND pay.current_status = 199 AND paya.transaction_table IN('sales_invoices','purchase_invoices')";
        $paymentsres1 = $callConfig->getAllRows($paymentssql);
        foreach($paymentsres1 as $payterm){
            if($payterm->transaction_table == "purchase_invoices" && isset($purchase_invoices[$payterm->transaction_id])){
                $purchase_invoices[$payterm->transaction_id]->payments[$payterm->id] =  $payterm;
            }
            if($payterm->transaction_table == "sales_invoices" && isset($sales_invoices[$payterm->transaction_id])){
                $sales_invoices[$payterm->transaction_id]->payments[$payterm->id] =  $payterm;
            }
        }

        $return['purchase_invoices'] = $purchase_invoices;

        $return['sales_invoices'] = $sales_invoices;
        //purchase_invoices list


    return $return;

    }

public function GETProcurementModulesDashboardSummary($get){
        global $callConfig;
        $return = array();
        $user_id= $get[self::KEY_USER_ID];
        $useridcheck = "Y";
        if($useridcheck == 'Y'){
            $notificationswhere = " AND notiusr.user_id = '".$user_id."'";
            $transaction_approval_logswhere = " AND user_id = '".$user_id."'";
            $transaction_activity_schedulewhere = " AND tas.transaction_id = '".$user_id."'";
        }


        $payment_termssql = "SELECT id, payment_term_name, content FROM payment_terms WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $payment_termsres = $callConfig->getAllRows($payment_termssql);
        $payment_terms = array();
        foreach($payment_termsres as $payterm){
            $payment_terms[$payterm->id] = $payterm->payment_term_name;
        }



        $paymentssql = "SELECT pay.transaction_type, pay.payment_date, paya.party_id, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.total_paid_amonut  FROM receipts pay, receipt_allocations paya WHERE pay.company_id = 1 AND pay.company_id = paya.company_id AND pay.id = paya.receipt_id AND pay.is_active = 0 AND pay.current_status = 199 AND paya.transaction_table IN('sales_invoices','purchase_invoices')";
        $paymentsres = $callConfig->getAllRows($paymentssql);
        $return['customer_payments'] = $paymentsres;

        $sql1 = "SELECT id, display_name FROM application_users WHERE company_id = '".$get[self::KEY_COMPANY_ID]."'  AND is_active =0";
        $data1 = $callConfig->getAllRows($sql1);
        $return['application_users'] = $data1;

        $sql = "SELECT notiusr.notification_id, notiusr.notification_type, notiusr.read_status, noti.notification_message, noti.notification_datetime, noti.application_table_name, noti.transaction_number, noti.notification_details FROM notification_user notiusr, notifications noti WHERE noti.company_id = notiusr.company_id ".$notificationswhere." AND noti.id = notiusr.notification_id AND noti.company_id = '".$get[self::KEY_COMPANY_ID]."' AND notiusr.notification_type = 'ACTION' AND notiusr.read_status =0 ORDER BY noti.notification_datetime DESC";
        $data = $callConfig->getAllRows($sql);
        $return['notifications'] = $data;

        $transaction_approval_logssql = "SELECT transaction_table,transaction_id, response_type,user_id,request_date,response_date FROM transaction_approval_logs WHERE send_back_flag = 0 AND call_back = 1 AND response_type ='0' ".$transaction_approval_logswhere;
        $transaction_approval_logsres=$callConfig->getAllRows($transaction_approval_logssql);
        $return['transaction_approval_logs'] = $transaction_approval_logsres;

        $taskactivitysql = "SELECT tas.id, tas.action_date, tas.task_name FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get[self::KEY_COMPANY_ID]."'  ".$transaction_activity_schedulewhere." AND tas.is_active = 0";
        $taskactivityres = $callConfig->getAllRows($taskactivitysql);
        $return['transaction_activity_schedule'] = $taskactivityres;
        //credit_limit

        $sql = "SELECT po.party_id, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN (po.amount-po.pending_amount) ELSE 0 END) AS inv_pending_amount, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS only_created_amount, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS inprogress_amount FROM purchase_invoices po WHERE po.company_id = '".$get[self::KEY_COMPANY_ID]."' AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N' OR po.cancel_invoice = '') AND po.is_active = 0 GROUP BY po.party_id";
        $sqlres = $callConfig->getAllRows($sql);
        $party_purchase_invoices = array();
        foreach($sqlres as $pires){
            $party_purchase_invoices[$pires->party_id] = $pires;
        }

        $sql = "SELECT po.party_id, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN (po.amount-po.pending_amount) ELSE 0 END) AS inv_pending_amount, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS only_created_amount, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS inprogress_amount FROM sales_invoices po WHERE po.company_id = '".$get[self::KEY_COMPANY_ID]."' AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N' OR po.cancel_invoice = '') AND po.is_active = 0 GROUP BY po.party_id";
        $sqlres = $callConfig->getAllRows($sql);
        $party_sales_invoices = array();
        foreach($sqlres as $sires){
            $party_sales_invoices[$pires->party_id] = $sires;
        }

        $vendorssql = "SELECT `id`, `company`, `display_name`, `billing_address`, `billing_city`, `billing_state`, `billing_post_code`, `billing_country`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_post_code`, `shipping_country`, `bank_account_name`, `bank_name`, `account_type`, `bank_account_no`, `payment_terms` ,`credit_limit` FROM `vendors` WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $vendorsres = $callConfig->getAllRows($vendorssql);
        $vendors = array();
        foreach($vendorsres as $vinres){
            $vinres->party_purchase_invoices = isset($party_purchase_invoices[$vinres->id])?$party_purchase_invoices[$vinres->id]:array();
            $vendors[] = $vinres;
        }
        $return['vendors'] = $vendorsres;

        $customerssql = "SELECT `id`, `company`, `display_name`, `billing_address`, `billing_city`, `billing_state`, `billing_post_code`, `billing_country`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_post_code`, `shipping_country`, `payment_terms` ,`credit_limit` FROM `customers` WHERE company_id = '".$get[self::KEY_COMPANY_ID].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $customersres = $callConfig->getAllRows($customerssql);
        $customers = array();
        foreach($customersres as $custres){
            $custres->party_sales_invoices = isset($party_sales_invoices[$custres->id])?$party_sales_invoices[$custres->id]:array();
            $customers[] = $custres;
        }
        $return['customers'] = $customers;



        //purchase_invoices list
        $sql1 = "SELECT pi.id, pi.transaction_number, pi.transaction_date, pi.party_id, pi.payment_terms, pii.id pi_id, pii.item_id, pii.item_name FROM purchase_invoices pi, pi_items pii WHERE pi.company_id = '".$get[self::KEY_COMPANY_ID]."' AND pi.current_status = ".ST_APPROVED." AND  pi.id = pii.transaction_id AND  pi.is_active = 0 AND  pi.is_active = pii.is_active";
        $res1 = $callConfig->getAllRows($sql1);
        $purchase_invoices = array();
        foreach($res1 as $resr){
            $resr->payments = array();
            if(isset($payment_terms[$resr->payment_terms])){
                $resr->payment_term_name =$payment_terms[$resr->payment_terms];
            }else{
                $resr->payment_term_name =null;
            }
            $purchase_invoices[$resr->id] = $resr;
        }
        foreach($res1 as $resr){
            $purchase_invoices[$resr->id]->items[$resr->pi_id] = array('id' => $resr->pi_id, self::KEY_ITEM_ID => $resr->item_id,'item_name' => $resr->item_name);
        }

        //sales_invoices list
        $sql1 = "SELECT pi.id, pi.transaction_number, pi.transaction_date, pi.party_id, pi.payment_terms, pii.id pi_id, pii.item_id, pii.item_name FROM sales_invoices pi, si_items pii WHERE pi.company_id = '".$get[self::KEY_COMPANY_ID]."' AND pi.current_status = ".ST_APPROVED." AND  pi.id = pii.transaction_id AND  pi.is_active = 0 AND  pi.is_active = pii.is_active";
        $res1 = $callConfig->getAllRows($sql1);
        $sales_invoices = array();
        foreach($res1 as $resr){
            $resr->payments = array();
            if(isset($payment_terms[$resr->payment_terms])){
                $resr->payment_term_name =$payment_terms[$resr->payment_terms];
            }else{
                $resr->payment_term_name =null;
            }
            $sales_invoices[$resr->id] = $resr;
        }
        foreach($res1 as $resr){
            $sales_invoices[$resr->id]->items[$resr->pi_id] = array('id' => $resr->pi_id, self::KEY_ITEM_ID => $resr->item_id,'item_name' => $resr->item_name);
        }
        //sales_invoices list

        $payment_terms_transsql = "SELECT id, transaction_table, transaction_id, party_id, due_date, amount, tax_amount, due_amount, pending_amount, advance_offer_date, advance_offer_amount FROM payment_term_transactions WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND is_active = 0 AND transaction_table IN('sales_invoices','purchase_invoices')";
        $payment_terms_transres = $callConfig->getAllRows($payment_terms_transsql);
        foreach($payment_terms_transres as $paytermtran){
            if($paytermtran->transaction_table == "purchase_invoices" && isset($purchase_invoices[$paytermtran->transaction_id])){
                $purchase_invoices[$paytermtran->transaction_id]->payment_term_transactions[$paytermtran->id] =  $paytermtran;
            }
            if($paytermtran->transaction_table == "sales_invoices" && isset($sales_invoices[$paytermtran->transaction_id])){
                $sales_invoices[$paytermtran->transaction_id]->payment_term_transactions[$paytermtran->id] =  $paytermtran;
            }
        }
        $paymentssql = "SELECT paya.id, pay.transaction_type, pay.payment_date, paya.party_id, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.total_paid_amonut  FROM payments pay, payment_allocations paya WHERE pay.company_id = 1 AND pay.company_id = paya.company_id AND pay.id = paya.payment_id AND pay.is_active = 0 AND pay.current_status = 199 AND paya.transaction_table IN('sales_invoices','purchase_invoices')";
        $paymentsres1 = $callConfig->getAllRows($paymentssql);
        foreach($paymentsres1 as $payterm){
            if($payterm->transaction_table == "purchase_invoices" && isset($purchase_invoices[$payterm->transaction_id])){
                $purchase_invoices[$payterm->transaction_id]->payments[$payterm->id] =  $payterm;
            }
            if($payterm->transaction_table == "sales_invoices" && isset($sales_invoices[$payterm->transaction_id])){
                $sales_invoices[$payterm->transaction_id]->payments[$payterm->id] =  $payterm;
            }
        }

        $return['purchase_invoices'] = $purchase_invoices;

        $return['sales_invoices'] = $sales_invoices;
        //purchase_invoices list

        return $return;

    }


//profit and loss report
public function calculateBalances($parentCode, $map, &$total, $type){
    if(empty($map[$parentCode])) {
        return;
    }

    foreach($map[$parentCode] as $coa){
        $code = $coa[self::KEY_ACCOUNT_CODE];
        $bal  = $coa[self::KEY_CLOSING_BALANCE];

        $total[$code][$type] = ($total[$code][$type] ?? 0) + $bal;

        // add to parent chain
        if($parentCode != 0){
            $total[$parentCode][$type] = ($total[$parentCode][$type] ?? 0) + $bal;
        }

        // recursive call
        $this->calculateBalances($code, $map, $total, $type);
    }
}
public function processBalances($balances, &$total, $type){
    $map = [];

    foreach($balances as $row){
        $map[$row[self::KEY_PARENT_ACCOUNT_CODE]][] = $row;
    }

    $this->calculateBalances(0, $map, $total, $type);
}
public function GETProcurementModulesDashboardSummaryProfitLoass($get){

    $get[self::KEY_START_DATE] = $this->dateconvertdbformat($get[self::KEY_START_DATE]);
    $get[self::KEY_END_DATE]   = $this->dateconvertdbformat($get[self::KEY_END_DATE]);

    $get['prev_start_date'] = date(self::DATE_FORMAT_YMD, strtotime('-1 year', strtotime($get[self::KEY_START_DATE])));
    $get['prev_end_date']   = date(self::DATE_FORMAT_YMD, strtotime('-1 year', strtotime($get[self::KEY_END_DATE])));

    include_once "account_user.class.php";
    $accountUserClassonobj = new accountUserClass();

    $result1 = $accountUserClassonobj->getProfitAndLossReports($get);
    $result = json_decode(json_encode($result1), true);

    $totalcoa = [];

    // 🔥 Just 4 calls instead of 300+ lines
    $this->processBalances($result['start_balances'], $totalcoa, self::KEY_START_BALANCE);
    $this->processBalances($result['end_balances'], $totalcoa, self::KEY_END_BALANCE);
    $this->processBalances($result['prev_start_balances'], $totalcoa, self::KEY_PREV_START_BALANCE);
    $this->processBalances($result['prev_end_balances'], $totalcoa, self::KEY_PREV_END_BALANCE);

    // 🔹 Calculations (same logic, cleaner)
    $calc = function($code, $type1, $type2) use ($totalcoa){
        return ($totalcoa[$code][$type1] ?? 0) - ($totalcoa[$code][$type2] ?? 0);
    };

    return [
        "revenue" => $calc('50002700',self::KEY_END_BALANCE,self::KEY_START_BALANCE),
        "other_income" => $calc('40002000',self::KEY_END_BALANCE,self::KEY_START_BALANCE),
        "total_income" => $calc('50002700',self::KEY_END_BALANCE,self::KEY_START_BALANCE) + $calc('40002000',self::KEY_END_BALANCE,self::KEY_START_BALANCE),

        "material" => $calc('50000100',self::KEY_END_BALANCE,self::KEY_START_BALANCE),
        "employee_benefits_expense" => $calc('50002100',self::KEY_END_BALANCE,self::KEY_START_BALANCE),
        "finance_costs" => $calc('50003500',self::KEY_END_BALANCE,self::KEY_START_BALANCE),
        "depreciation_and_amortisation" => $calc('50003600',self::KEY_END_BALANCE,self::KEY_START_BALANCE),

        "current_tax" => $calc('50003451',self::KEY_END_BALANCE,self::KEY_START_BALANCE),
        "deferred_tax" =>
            $calc('50003452',self::KEY_END_BALANCE,self::KEY_START_BALANCE) +
            $calc('50003453',self::KEY_END_BALANCE,self::KEY_START_BALANCE),

        // previous
        "prev_revenue" => $calc('50002700',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE),
        "prev_other_income" => $calc('40002000',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE),
        "prev_total_income" => $calc('50002700',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE) + $calc('40002000',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE),

        "prev_material" => $calc('50000100',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE),
        "prev_employee_benefits_expense" => $calc('50002100',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE),
        "prev_finance_costs" => $calc('50003500',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE),
        "prev_depreciation_and_amortisation" => $calc('50003600',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE),

        "prev_current_tax" => $calc('50003451',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE),
        "prev_deferred_tax" =>
            $calc('50003452',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE) +
            $calc('50003453',self::KEY_PREV_END_BALANCE,self::KEY_PREV_START_BALANCE),
    ];
}
//profit and loss report




    private function accumulateHierarchyBalances($balances, &$totalcoa, $balanceKey){
        $parentcoa = array();
        $parentchild = array();
        foreach($balances as $coa){
            if($coa[self::KEY_PARENT_ACCOUNT_CODE] == 0){
                $parentcoa[] = $coa;
            }
            $parentchild[$coa[self::KEY_PARENT_ACCOUNT_CODE]][] = $coa;
        }
        $accumulate = function($nodes, $ancestors) use (&$accumulate, &$parentchild, &$totalcoa, $balanceKey){
            foreach($nodes as $node){
                $code = $node[self::KEY_ACCOUNT_CODE];
                $chain = array_merge($ancestors, [$code]);
                foreach($chain as $ancestorCode){
                    $totalcoa[$ancestorCode][$balanceKey] += $node[self::KEY_CLOSING_BALANCE];
                }
                if(!empty($parentchild[$code])){
                    $accumulate($parentchild[$code], $chain);
                }
            }
        };
        $accumulate($parentcoa, []);
    }

//start Balance Sheet
public function GETProcurementModulesDashboardSummaryBalanceSheets($get){

    // STEP 1: Get data (your existing function)
    $get['balance_date'] = date(self::DATE_FORMAT_YMD,strtotime($get[self::KEY_START_DATE]));
    $get['prev_balance_date']   = date(self::DATE_FORMAT_YMD,strtotime($get[self::KEY_END_DATE]));

    include_once "account_user.class.php";
    $accountUserClassonobj = new accountUserClass();
    $result1 = $accountUserClassonobj->getBalanceSheetReport($get);
    $result = json_decode(json_encode($result1), true);

    $totalcoa = array();
    $this->accumulateHierarchyBalances($result['balances'], $totalcoa, self::KEY_START_BALANCE);
    $sharecapital = ($totalcoa['30000210'][self::KEY_START_BALANCE]);
    $reservesandsurplus = ($totalcoa['30000250'][self::KEY_START_BALANCE]);
    $moneyreceivedagainstsharewarrants = ($totalcoa['30000400'][self::KEY_START_BALANCE]);

    $longtermborrowings = ($totalcoa['20001100'][self::KEY_START_BALANCE]);
    $deferredtaxliabilitiess = ($totalcoa['2002200'][self::KEY_START_BALANCE]);
    $otherlongtermliabilities = ($totalcoa['2002300'][self::KEY_START_BALANCE]);
    $longtermprovisions    = ($totalcoa['2002400'][self::KEY_START_BALANCE]);

    $shorttermborrowings = ($totalcoa['2003100'][self::KEY_START_BALANCE]);
    $tradepayables = ($totalcoa['20003500'][self::KEY_START_BALANCE]);
    $othercurrentliabilities = ($totalcoa['2004000'][self::KEY_START_BALANCE]);
    $shorttermprovisions = ($totalcoa['2004300'][self::KEY_START_BALANCE]);

    $propertyplantequipment= ($totalcoa['10001100'][self::KEY_START_BALANCE]);
    $intangibleassets= ($totalcoa['10001400'][self::KEY_START_BALANCE]+$totalcoa['10001500'][self::KEY_START_BALANCE]+$totalcoa['10001700'][self::KEY_START_BALANCE]);
    $capitalworkinprogress= ($totalcoa['10001600'][self::KEY_START_BALANCE]!=""?$totalcoa['10001600'][self::KEY_START_BALANCE]:0);
    $intangibleassetsunderdevelopment= ($totalcoa['10001700'][self::KEY_START_BALANCE] !=""?$totalcoa['10001700'][self::KEY_START_BALANCE]:0);

    $noncurrentinvestments= ($totalcoa['10001800'][self::KEY_START_BALANCE]!=""?$totalcoa['10001800'][self::KEY_START_BALANCE]:0);
    $deferredtaxassets= ($totalcoa['10002400'][self::KEY_START_BALANCE]!=""?$totalcoa['10002400'][self::KEY_START_BALANCE]:0);
    $longtermloansandadvances= ($totalcoa['10002300'][self::KEY_START_BALANCE]!=""?$totalcoa['10002300'][self::KEY_START_BALANCE]:0);
    $othernoncurrentassets= ($totalcoa['10002100'][self::KEY_START_BALANCE]!=""?$totalcoa['10002100'][self::KEY_START_BALANCE]:0);
    $currentinvestments= ($totalcoa['10003100'][self::KEY_START_BALANCE]!=""?$totalcoa['10003100'][self::KEY_START_BALANCE]:0);
    $inventories= ($totalcoa['10003400'][self::KEY_START_BALANCE]!=""?$totalcoa['10003400'][self::KEY_START_BALANCE]:0);
    $tradereceivables= ($totalcoa['10003900'][self::KEY_START_BALANCE]!=""?$totalcoa['10003900'][self::KEY_START_BALANCE]:0);
    $cashandbankbalances= ($totalcoa['10005000'][self::KEY_START_BALANCE]!=""?$totalcoa['10005000'][self::KEY_START_BALANCE]:0);
    $shorttermloansandadvances= ($totalcoa['10006000'][self::KEY_START_BALANCE]!=""?$totalcoa['10006000'][self::KEY_START_BALANCE]:0);
    $othercurrentassets= ($totalcoa['10008000'][self::KEY_START_BALANCE]!=""?$totalcoa['10008000'][self::KEY_START_BALANCE]:0);

    $totalcoa = array();
    $this->accumulateHierarchyBalances($result['prev_balances'], $totalcoa, self::KEY_PRE_START_BALANCE);
    //Start preview Balances
    $pre_sharecapital = ($totalcoa['30000210'][self::KEY_PRE_START_BALANCE]);
    $pre_reservesandsurplus = ($totalcoa['30000250'][self::KEY_PRE_START_BALANCE]);
    $pre_moneyreceivedagainstsharewarrants = ($totalcoa['30000400'][self::KEY_PRE_START_BALANCE]);

    $pre_longtermborrowings = ($totalcoa['20001100'][self::KEY_PRE_START_BALANCE]);
    $pre_deferredtaxliabilitiess = ($totalcoa['2002200'][self::KEY_PRE_START_BALANCE]);
    $pre_otherlongtermliabilities = ($totalcoa['2002300'][self::KEY_PRE_START_BALANCE]);
    $pre_longtermprovisions    = ($totalcoa['2002400'][self::KEY_PRE_START_BALANCE]);

    $pre_shorttermborrowings = ($totalcoa['2003100'][self::KEY_PRE_START_BALANCE]);
    $pre_tradepayables = ($totalcoa['20003500'][self::KEY_PRE_START_BALANCE]);
    $pre_othercurrentliabilities = ($totalcoa['2004000'][self::KEY_PRE_START_BALANCE]);
    $pre_shorttermprovisions = ($totalcoa['2004300'][self::KEY_PRE_START_BALANCE]);

    $pre_propertyplantequipment= ($totalcoa['10001100'][self::KEY_PRE_START_BALANCE]);
    $pre_intangibleassets= ($totalcoa['10001400'][self::KEY_PRE_START_BALANCE]+$totalcoa['10001500'][self::KEY_PRE_START_BALANCE]+$totalcoa['10001700'][self::KEY_PRE_START_BALANCE]);
    $pre_capitalworkinprogress= ($totalcoa['10001600'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10001600'][self::KEY_PRE_START_BALANCE]:0);
    $pre_intangibleassetsunderdevelopment= ($totalcoa['10001700'][self::KEY_PRE_START_BALANCE] !=""?$totalcoa['10001700'][self::KEY_PRE_START_BALANCE]:0);
    $pre_deferredtaxassets= ($totalcoa['10002400'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10002400'][self::KEY_PRE_START_BALANCE]:0);
    $pre_longtermloansandadvances= ($totalcoa['10002300'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10002300'][self::KEY_PRE_START_BALANCE]:0);
    $pre_othernoncurrentassets= ($totalcoa['10002100'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10002100'][self::KEY_PRE_START_BALANCE]:0);
    $pre_currentinvestments= ($totalcoa['10003100'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10003100'][self::KEY_PRE_START_BALANCE]:0);
    $pre_inventories= ($totalcoa['10003400'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10003400'][self::KEY_PRE_START_BALANCE]:0);
    $pre_tradereceivables= ($totalcoa['10003900'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10003900'][self::KEY_PRE_START_BALANCE]:0);
    $pre_cashandbankbalances= ($totalcoa['10005000'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10005000'][self::KEY_PRE_START_BALANCE]:0);
    $pre_shorttermloansandadvances= ($totalcoa['10006000'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10006000'][self::KEY_PRE_START_BALANCE]:0);
    $pre_othercurrentassets= ($totalcoa['10008000'][self::KEY_PRE_START_BALANCE]!=""?$totalcoa['10008000'][self::KEY_PRE_START_BALANCE]:0);
    //End preview Balances

    return [
        "equity_liabilities" => [
            "shareholders_funds" => [
                "share_capital" => [
                    self::KEY_CURRENT => $sharecapital,
                    self::KEY_PREVIOUS => $pre_sharecapital,
                ],
                "reserves_surplus" => [
                    self::KEY_CURRENT => $reservesandsurplus,
                    self::KEY_PREVIOUS => $pre_reservesandsurplus
                ],
                "Money_received_against_share_warrants" => [
                    self::KEY_CURRENT => $moneyreceivedagainstsharewarrants,
                    self::KEY_PREVIOUS => $pre_moneyreceivedagainstsharewarrants
                ]
            ],
            "non_current_liabilities" => [
                "long_term_borrowings" => [
                    self::KEY_CURRENT => $longtermborrowings,
                    self::KEY_PREVIOUS => $pre_longtermborrowings
                ],
                "deferred_tax_liabilities" => [
                    self::KEY_CURRENT => $deferredtaxliabilitiess,
                    self::KEY_PREVIOUS => $pre_deferredtaxliabilitiess
                ],
                "other_long_term_liabilities" => [
                    self::KEY_CURRENT => $otherlongtermliabilities,
                    self::KEY_PREVIOUS => $pre_otherlongtermliabilities
                ],
                "long_term_provisions" => [
                    self::KEY_CURRENT => $longtermprovisions,
                    self::KEY_PREVIOUS => $pre_longtermprovisions
                ]
            ],

            "current_liabilities" => [
                "short_term_borrowings" => [
                    self::KEY_CURRENT => $shorttermborrowings,
                    self::KEY_PREVIOUS => $pre_shorttermborrowings
                ],
                "trade_payables" => [
                    self::KEY_CURRENT => $tradepayables,
                    self::KEY_PREVIOUS => $pre_tradepayables
                ],
                "other_current_liabilities" => [
                    self::KEY_CURRENT => $othercurrentliabilities,
                    self::KEY_PREVIOUS => $pre_othercurrentliabilities
                ],
                "short_term_provisions" => [
                    self::KEY_CURRENT => $shorttermprovisions,
                    self::KEY_PREVIOUS => $pre_shorttermprovisions
                ]
            ]
        ],

        self::TBL_ASSETS => [

            "non_current_assets" => [
                "property_plant_equipment" => [
                    self::KEY_CURRENT => $propertyplantequipment,
                    self::KEY_PREVIOUS => $pre_propertyplantequipment
                ],
                "intangible_assets" => [
                    self::KEY_CURRENT => $intangibleassets,
                    self::KEY_PREVIOUS => $pre_intangibleassets

                ],
                "capital_work_in_progress" => [
                    self::KEY_CURRENT => $capitalworkinprogress,
                    self::KEY_PREVIOUS => $pre_capitalworkinprogress
                ],
                "intangible_under_dev" => [
                    self::KEY_CURRENT => $intangibleassetsunderdevelopment,
                    self::KEY_PREVIOUS => $pre_intangibleassetsunderdevelopment
                ],
                "non_current_investments" => [
                    self::KEY_CURRENT => $noncurrentinvestments,
                    self::KEY_PREVIOUS => $noncurrentinvestments
                ],
                "deferred_tax_assets" => [
                    self::KEY_CURRENT => $deferredtaxassets,
                    self::KEY_PREVIOUS => $pre_deferredtaxassets
                ],
                "long_term_loans" => [
                    self::KEY_CURRENT => $longtermloansandadvances,
                    self::KEY_PREVIOUS => $pre_longtermloansandadvances
                ],
                "other_non_current_assets" => [
                    self::KEY_CURRENT => $othernoncurrentassets,
                    self::KEY_PREVIOUS => $pre_othernoncurrentassets
                ]
            ],

            "current_assets" => [
                "current_investments" => [
                    self::KEY_CURRENT => $currentinvestments,
                    self::KEY_PREVIOUS => $pre_currentinvestments
                ],
                "inventories" => [
                    self::KEY_CURRENT => $inventories,
                    self::KEY_PREVIOUS => $pre_inventories
                ],
                "trade_receivables" => [
                   self::KEY_CURRENT => $tradereceivables,
                    self::KEY_PREVIOUS => $pre_tradereceivables
                ],
                "cash_bank_balance" => [
                    self::KEY_CURRENT => $cashandbankbalances,
                    self::KEY_PREVIOUS => $pre_cashandbankbalances
                ],
                "short_term_loans" => [
                    self::KEY_CURRENT => $shorttermloansandadvances,
                    self::KEY_PREVIOUS => $pre_shorttermloansandadvances
                ],
                "other_current_assets" => [
                    self::KEY_CURRENT => $othercurrentassets,
                    self::KEY_PREVIOUS => $pre_othercurrentassets
                ]
            ]
        ]
    ];
}


//End balance Sheets



//Start Cash Flow

public function GETProcurementModulesDashboardSummaryCashFlow($get){

    include_once "account_user.class.php";
    $obj = new accountUserClass();

    $get[self::KEY_START_DATE] = $get[self::KEY_START_DATE] !=""?$get[self::KEY_START_DATE]:date('Y-m-01');
    $get[self::KEY_END_DATE]   = $get[self::KEY_END_DATE] !=""?$get[self::KEY_END_DATE]:date('Y-m-t');
    $get['prev_start_date'] = date(self::DATE_FORMAT_YMD, strtotime('-1 month', strtotime($get[self::KEY_START_DATE])));
    $get['prev_end_date']   = date(self::DATE_FORMAT_YMD, strtotime('-1 month', strtotime($get[self::KEY_END_DATE])));
    $result1 = $obj->getcashflowreports($get);
    $result = json_decode(json_encode($result1), true);

    $totalcoa = array();
    $this->accumulateHierarchyBalances($result['start_balances'], $totalcoa, self::KEY_START_BALANCE);
    $this->accumulateHierarchyBalances($result['end_balances'], $totalcoa, self::KEY_END_BALANCE);

    $depreciation = (($totalcoa['10001300'][self::KEY_END_BALANCE]+$totalcoa['10001500'][self::KEY_END_BALANCE]+$totalcoa['50003600'][self::KEY_END_BALANCE])-($totalcoa['10001300'][self::KEY_START_BALANCE]+$totalcoa['10001500'][self::KEY_START_BALANCE]+$totalcoa['50003600'][self::KEY_START_BALANCE]));
    $baddebtswrittenoff = (($totalcoa['1004200'][self::KEY_END_BALANCE]+$totalcoa['50003300'][self::KEY_END_BALANCE])-($totalcoa['1004200'][self::KEY_START_BALANCE]+$totalcoa['50003300'][self::KEY_START_BALANCE]));

    $tradereceivables = (($totalcoa['10003900'][self::KEY_END_BALANCE])-($totalcoa['10003900'][self::KEY_START_BALANCE]));

    $shorttermloansandadvances = (($totalcoa['10006000'][self::KEY_END_BALANCE])-($totalcoa['10006000'][self::KEY_START_BALANCE]));

    $longtermloansandadvances = (($totalcoa['10002300'][self::KEY_END_BALANCE])-($totalcoa['10002300'][self::KEY_START_BALANCE]));

    $othercurrentassets  = (($totalcoa['10008000'][self::KEY_END_BALANCE])-($totalcoa['10008000'][self::KEY_START_BALANCE]));
    $tradepayables  = (($totalcoa['20003500'][self::KEY_END_BALANCE])-($totalcoa['20003500'][self::KEY_START_BALANCE]));

    $decriesincreaseinothercurrentliabilities = (($totalcoa['2004000'][self::KEY_END_BALANCE])-($totalcoa['2004000'][self::KEY_START_BALANCE]));

    $incometaxrefundpaid = (($totalcoa['2004401'][self::KEY_END_BALANCE]+$totalcoa['10002903'][self::KEY_END_BALANCE])-($totalcoa['2004401'][self::KEY_START_BALANCE]+$totalcoa['10002903'][self::KEY_START_BALANCE]));

    $purchaseofpropertyplantequipmentandintangibleassets = "-";
    $proceedsfromsaleofproperty = "-";
    $netproceedsfromissueofshares = "-";
    $netproceedsfromlongtermborrowings = (($totalcoa['20001100'][self::KEY_END_BALANCE])-($totalcoa['20001100'][self::KEY_START_BALANCE]));
    $netproceedsfromshorttermborrowings = (($totalcoa['2003100'][self::KEY_END_BALANCE])-($totalcoa['2003100'][self::KEY_START_BALANCE]));
    $cashandcashequivalentsduringtheyear = (($totalcoa['10005000'][self::KEY_END_BALANCE])-($totalcoa['10005000'][self::KEY_START_BALANCE]));
    $cashequivalentsatthebeginningoftheyear =($totalcoa['10005000'][self::KEY_START_BALANCE]);
    $cashequivalentsattheendoftheyear =($totalcoa['10005000'][self::KEY_END_BALANCE]);

    ///////////////////////////////////  previous year data ///////////////////////////////////////////////
    $this->accumulateHierarchyBalances($result['prev_start_balances'], $totalcoa, self::KEY_PREV_START_BALANCE);
    $this->accumulateHierarchyBalances($result['prev_end_balances'], $totalcoa, self::KEY_PREV_END_BALANCE);
    $prev_depreciation = (($totalcoa['10001300'][self::KEY_PREV_END_BALANCE]+$totalcoa['10001500'][self::KEY_PREV_END_BALANCE]+$totalcoa['50003600'][self::KEY_PREV_END_BALANCE])-($totalcoa['10001300'][self::KEY_PREV_START_BALANCE]+$totalcoa['10001500'][self::KEY_PREV_START_BALANCE]+$totalcoa['50003600'][self::KEY_PREV_START_BALANCE]));
    $prev_baddebtswrittenoff = (($totalcoa['1004200'][self::KEY_PREV_END_BALANCE]+$totalcoa['50003300'][self::KEY_PREV_END_BALANCE])-($totalcoa['1004200'][self::KEY_PREV_START_BALANCE]+$totalcoa['50003300'][self::KEY_PREV_START_BALANCE]));
    $prev_tradereceivables = (($totalcoa['10003900'][self::KEY_PREV_END_BALANCE])-($totalcoa['10003900'][self::KEY_PREV_START_BALANCE]));
    $prev_shorttermloansandadvances = (($totalcoa['10006000'][self::KEY_PREV_END_BALANCE])-($totalcoa['10006000'][self::KEY_PREV_START_BALANCE]));
    $prev_longtermloansandadvances = (($totalcoa['10002300'][self::KEY_PREV_END_BALANCE])-($totalcoa['10002300'][self::KEY_PREV_START_BALANCE]));
    $prev_othercurrentassets  = (($totalcoa['10008000'][self::KEY_PREV_END_BALANCE])-($totalcoa['10008000'][self::KEY_PREV_START_BALANCE]));
    $prev_tradepayables  = (($totalcoa['20003500'][self::KEY_PREV_END_BALANCE])-($totalcoa['20003500'][self::KEY_PREV_START_BALANCE]));
    $prev_decriesincreaseinothercurrentliabilities = (($totalcoa['2004000'][self::KEY_PREV_END_BALANCE])-($totalcoa['2004000'][self::KEY_PREV_START_BALANCE]));
    $prev_incometaxrefundpaid = (($totalcoa['2004401'][self::KEY_PREV_END_BALANCE]+$totalcoa['10002903'][self::KEY_PREV_END_BALANCE])-($totalcoa['2004401'][self::KEY_PREV_START_BALANCE]+$totalcoa['10002903'][self::KEY_PREV_START_BALANCE]));
    $prev_purchaseofpropertyplantequipmentandintangibleassets = "-";
    $prev_proceedsfromsaleofproperty = "-";
    $prev_netproceedsfromissueofshares = "-";
    $prev_netproceedsfromlongtermborrowings = (($totalcoa['20001100'][self::KEY_PREV_END_BALANCE])-($totalcoa['20001100'][self::KEY_PREV_START_BALANCE]));
    $prev_netproceedsfromshorttermborrowings = (($totalcoa['2003100'][self::KEY_PREV_END_BALANCE])-($totalcoa['2003100'][self::KEY_PREV_START_BALANCE]));
    $prev_cashandcashequivalentsduringtheyear = (($totalcoa['10005000'][self::KEY_PREV_END_BALANCE])-($totalcoa['10005000'][self::KEY_PREV_START_BALANCE]));
    $prev_cashequivalentsatthebeginningoftheyear =($totalcoa['10005000'][self::KEY_PREV_START_BALANCE]);
    $prev_cashequivalentsattheendoftheyear =($totalcoa['10005000'][self::KEY_PREV_END_BALANCE]);



    return [
        "operating_activities" => [
            [
                self::KEY_LABEL => "Profit/(Loss) before tax",
                self::KEY_CURRENT => 0,
                self::KEY_PREVIOUS => 0
            ],
            [
                self::KEY_LABEL => "Depreciation",
                self::KEY_CURRENT => $depreciation,
                self::KEY_PREVIOUS => $prev_depreciation
            ],
            [
                self::KEY_LABEL => "Bad debts written off",
                self::KEY_CURRENT => $baddebtswrittenoff,
                self::KEY_PREVIOUS => $prev_baddebtswrittenoff
            ],
            [
                self::KEY_LABEL => "Trade receivables",
                self::KEY_CURRENT => $tradereceivables,
                self::KEY_PREVIOUS => $prev_tradereceivables
            ],
            [
                self::KEY_LABEL => "Short-term loans and advances",
                self::KEY_CURRENT => $shorttermloansandadvances,
                self::KEY_PREVIOUS => $prev_shorttermloansandadvances
            ],
            [
                self::KEY_LABEL => "Long-term loans and advances",
                self::KEY_CURRENT => $longtermloansandadvances,
                self::KEY_PREVIOUS => $prev_longtermloansandadvances
            ],
            [
                self::KEY_LABEL => "Other current assets",
                self::KEY_CURRENT => $othercurrentassets,
                self::KEY_PREVIOUS => $prev_othercurrentassets
            ],
            [
                self::KEY_LABEL => "Trade payables",
                self::KEY_CURRENT => $tradepayables,
                self::KEY_PREVIOUS => $prev_tradepayables
            ],
            [
                self::KEY_LABEL => "Other current liabilities",
                self::KEY_CURRENT => $decriesincreaseinothercurrentliabilities,
                self::KEY_PREVIOUS => $prev_decriesincreaseinothercurrentliabilities
            ],
            [
                self::KEY_LABEL => "Income tax refund/(paid)",
                self::KEY_CURRENT => $incometaxrefundpaid,
                self::KEY_PREVIOUS => $prev_incometaxrefundpaid
            ],
        ],

        "investing_activities" => [
            [
                self::KEY_LABEL => "Purchase of property",
                self::KEY_CURRENT => $purchaseofpropertyplantequipmentandintangibleassets,
                self::KEY_PREVIOUS => $prev_purchaseofpropertyplantequipmentandintangibleassets
            ],
            [
                self::KEY_LABEL => "Sale of property",
                self::KEY_CURRENT => $proceedsfromsaleofproperty,
                self::KEY_PREVIOUS => $prev_proceedsfromsaleofproperty
            ]
        ],

        "financing_activities" => [
            [
                self::KEY_LABEL => "Issue of shares",
                self::KEY_CURRENT => $netproceedsfromissueofshares,
                self::KEY_PREVIOUS => $prev_netproceedsfromissueofshares
            ],
            [
                self::KEY_LABEL => "Long-term borrowings",
                self::KEY_CURRENT => $netproceedsfromlongtermborrowings,
                self::KEY_PREVIOUS => $prev_netproceedsfromlongtermborrowings
            ],
            [
                self::KEY_LABEL => "Short-term borrowings",
                self::KEY_CURRENT => $netproceedsfromshorttermborrowings,
                self::KEY_PREVIOUS => $prev_netproceedsfromshorttermborrowings
            ]
        ],

        "summary" => [
            "cash_change" => [
                self::KEY_CURRENT => $cashandcashequivalentsduringtheyear,
                self::KEY_PREVIOUS => $prev_cashandcashequivalentsduringtheyear
            ],
            "opening_cash" => [
                self::KEY_CURRENT => $cashequivalentsatthebeginningoftheyear,
                self::KEY_PREVIOUS => $prev_cashequivalentsatthebeginningoftheyear
            ],
            "closing_cash" => [
                self::KEY_CURRENT => $cashequivalentsattheendoftheyear,
                self::KEY_PREVIOUS => $prev_cashequivalentsattheendoftheyear
            ]
        ]
    ];
}

//End Cash Flow


//Start customer ledegr
public function GETProcurementCustomerLedger($get){

    include_once "account_user.class.php";
    $obj = new accountUserClass();

    $get['from_date'] = $get[self::KEY_START_DATE] !=""?$get[self::KEY_START_DATE]:date('Y-m-01');
    $get['to_date']   = $get[self::KEY_END_DATE] !=""?$get[self::KEY_END_DATE]:date('Y-m-t');
     $result1 = $obj->getVendorledger($get);
    $result = json_decode(json_encode($result1), true);
    $last_account_balances = $result['last_account_balances'];
    $customers = $result['customers'];
    $transactionwithdates = $result['transactionwithdates'];

    ksort($transactionwithdates);

    $debitside  = ['sales_invoices','debit_notes'];

    $closingbal = $last_account_balances[self::KEY_CLOSING_BALANCE];
    $transaction_amt = 0;

    // Calculate transaction amount
    foreach ($transactionwithdates as $transactions) {
        foreach ($transactions as $pi) {
            if (in_array($pi[self::KEY_TRANSACTION_TABLE], $debitside)) {
                $closingbal -= $pi[self::KEY_P_TOTAL_AMOUNT];
                $transaction_amt -= $pi[self::KEY_P_TOTAL_AMOUNT];
            } else {
                $closingbal += $pi[self::KEY_P_TOTAL_AMOUNT];
                $transaction_amt += $pi[self::KEY_P_TOTAL_AMOUNT];
            }
        }
    }

    // Reset closing balance for row-wise calculation
    $running_balance = $last_account_balances[self::KEY_CLOSING_BALANCE];

    $ledger = [];

    // Header Data
    $ledger['header'] = [
        "title" => "Customer Ledger",
        "from"  => date('M Y', strtotime($get['from_date'] )),
        "to"    => date('M Y', strtotime($get['to_date'] )),
        "company" => $customers[$post['vendor_id']]['company'] ?? "",
        "customer" => $customers[$post['customer_id']]['display_name'] ?? "",
        "opening_balance" => $last_account_balances[self::KEY_CLOSING_BALANCE],
        "transaction_amount" => $transaction_amt,
        self::KEY_CLOSING_BALANCE => $closingbal,
        "date" => date(self::DATE_FORMAT_YMD),
        "time" => date('H:i:s')
    ];

    // Opening Row
    $ledger['rows'][] = [
        "type" => "opening",
        "date" => $last_account_balances['balance_date'],
        self::KEY_TRANSACTION_NO => "Opening Balance",
        "name" => "-",
        "ref_no" => "-",
        "ref_date" => "-",
        "debit" => 0,
        "credit" => 0,
        "balance" => $running_balance
    ];

    // Transactions
    foreach ($transactionwithdates as $transactions) {
        foreach ($transactions as $pi) {

            $debit = 0;
            $credit = 0;

            if (in_array($pi[self::KEY_TRANSACTION_TABLE], $debitside)) {
                $running_balance -= $pi[self::KEY_P_TOTAL_AMOUNT];
                $debit = $pi[self::KEY_P_TOTAL_AMOUNT];
            } else {
                $running_balance += $pi[self::KEY_P_TOTAL_AMOUNT];
                $credit = $pi[self::KEY_P_TOTAL_AMOUNT];
            }

            $ledger['rows'][] = [
                "type" => "transaction",
                self::KEY_TRANSACTION_NO => $pi[self::KEY_TRANSACTION_NUMBER] . " - " . $pi[self::KEY_TRANSACTION_TABLE],
                "date" => $pi[self::KEY_TRANSACTION_DATE],
                "name" => $pi['party_name'],
                "ref_no" => $pi['ref_no'],
                "ref_date" => $pi['ref_date'],
                "debit" => $debit,
                "credit" => $credit,
                "balance" => $running_balance
            ];
        }
    }
    return $ledger;
}


public function GETProcurementVendorLedger($get){

    include_once "account_user.class.php";
    $obj = new accountUserClass();

    $get['from_date'] = $get[self::KEY_START_DATE] !=""?$get[self::KEY_START_DATE]:date('Y-m-01');
    $get['to_date']   = $get[self::KEY_END_DATE] !=""?$get[self::KEY_END_DATE]:date('Y-m-t');
    $get['customer_id']= $get['vendor_id'];
     $result1 = $obj->getVendorledger($get);
    $result = json_decode(json_encode($result1), true);
    $last_account_balances = $result['last_account_balances'];
    $customers = $result['customers'];
    $transactionwithdates = $result['transactionwithdates'];

    ksort($transactionwithdates);

  $debitside = array('supplier_bills','debit_notes');
    $closingbal = $last_account_balances[self::KEY_CLOSING_BALANCE];
    $transaction_amt = 0;

    // Calculate transaction amount
    foreach ($transactionwithdates as $transactions) {
        foreach ($transactions as $pi) {
            if (in_array($pi[self::KEY_TRANSACTION_TABLE], $debitside)) {
                $closingbal -= $pi[self::KEY_P_TOTAL_AMOUNT];
                $transaction_amt -= $pi[self::KEY_P_TOTAL_AMOUNT];
            } else {
                $closingbal += $pi[self::KEY_P_TOTAL_AMOUNT];
                $transaction_amt += $pi[self::KEY_P_TOTAL_AMOUNT];
            }
        }
    }

    // Reset closing balance for row-wise calculation
    $running_balance = $last_account_balances[self::KEY_CLOSING_BALANCE];

    $ledger = [];

    // Header Data
    $ledger['header'] = [
        "title" => "Customer Ledger",
        "from"  => date('M Y', strtotime($get['from_date'] )),
        "to"    => date('M Y', strtotime($get['to_date'] )),
        "company" => $customers[$post['vendor_id']]['company'] ?? "",
        "customer" => $customers[$post['vendor_id']]['display_name'] ?? "",
        "opening_balance" => $last_account_balances[self::KEY_CLOSING_BALANCE],
        "transaction_amount" => $transaction_amt,
        self::KEY_CLOSING_BALANCE => $closingbal,
        "date" => date(self::DATE_FORMAT_YMD),
        "time" => date('H:i:s')
    ];

    // Opening Row
    $ledger['rows'][] = [
        "type" => "opening",
        "date" => $last_account_balances['balance_date'],
        self::KEY_TRANSACTION_NO => "Opening Balance",
        "name" => "-",
        "ref_no" => "-",
        "ref_date" => "-",
        "debit" => 0,
        "credit" => 0,
        "balance" => $running_balance
    ];

    // Transactions
    foreach ($transactionwithdates as $transactions) {
        foreach ($transactions as $pi) {

            $debit = 0;
            $credit = 0;

            if (in_array($pi[self::KEY_TRANSACTION_TABLE], $debitside)) {
                $running_balance -= $pi[self::KEY_P_TOTAL_AMOUNT];
                $debit = $pi[self::KEY_P_TOTAL_AMOUNT];
            } else {
                $running_balance += $pi[self::KEY_P_TOTAL_AMOUNT];
                $credit = $pi[self::KEY_P_TOTAL_AMOUNT];
            }

            $ledger['rows'][] = [
                "type" => "transaction",
                self::KEY_TRANSACTION_NO => $pi[self::KEY_TRANSACTION_NUMBER] . " - " . $pi[self::KEY_TRANSACTION_TABLE],
                "date" => $pi[self::KEY_TRANSACTION_DATE],
                "name" => $pi['party_name'],
                "ref_no" => $pi['ref_no'],
                "ref_date" => $pi['ref_date'],
                "debit" => $debit,
                "credit" => $credit,
                "balance" => $running_balance
            ];
        }
    }
    return $ledger;
}

//Edn customer ledger
    public function getMobileProcurementModulesData($get){
        global $callConfig;
        $return = array();
        $gettype = $get[self::KEY_GETTYPE];
        $transaction_table = $get[self::KEY_TRANSACTION_TABLE];
        $company_id = $get[self::KEY_COMPANY_ID];
        if($gettype== "SINGLEITEM"){
            $itemid = $get[self::KEY_ITEM_ID];
            $return['SINGLEITEM'] = $this->togetitemvalues($itemid);
        }elseif($gettype== "GETEMAILUSERWITHCOMPANY"){
            return $this->getEmailWithDBAndCompanyID($get);

        }elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYSTOCK"){
            return $this->GETProcurementModulesDashboardSummaryStock($get);

        }elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARY"){
            return $this->GETProcurementModulesDashboardSummary($get);

        }elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYPROFITLOSS"){
            return $this->GETProcurementModulesDashboardSummaryProfitLoass($get);

        }elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYBALANCESHEET"){
            return $this->GETProcurementModulesDashboardSummaryBalanceSheets($get);
        }elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYCASHFLOW"){
            return $this->GETProcurementModulesDashboardSummaryCashFlow($get);

        }elseif($gettype== "GETPROCUREMENTCUSTOMERLEDGER"){
            return $this->GETProcurementCustomerLedger($get);


        }elseif($gettype== "GETPROCUREMENTVENDORLEDGER"){
            return $this->GETProcurementVendorLedger($get);


        }elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYTest"){
            return $this->GETProcurementModulesDashboardSummaryTest($get);


        }elseif($gettype== "GETALREADYADDEDREFRESH"){
            $doc_ref_no = $get['doc_ref_no'];
            $query = "SELECT id, transaction_date, transaction_number FROM $transaction_table  WHERE company_id=$company_id AND doc_ref_no = '".$doc_ref_no."' AND is_active=0 ";
            $transaction_details = $callConfig->getRow($query);
            $return['GETALREADYADDEDREFRESH'] = $transaction_details;

        }elseif($gettype== "getPaymentTermsResult"){
            // Intentionally empty
        }elseif($gettype== "REFTRANSACTIONSTABLES"){
            $query = "SELECT id, transaction_table, reference_table FROM `dynamic_reference_tables` WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND reference_table = '".$transaction_table.self::SQL_QUOTE_AND_IS_ACTIVE_0;
            $queryres = $callConfig->getAllRows($query);
            $return['REFTRANSACTIONSTABLES'] = $queryres;
        }elseif($gettype== "REFTRANSACTIONSLIST"){
            $ref_type = $get['ref_type'];
            $query = "SELECT id, transaction_date, transaction_number FROM $ref_type  WHERE company_id=$company_id AND current_status ='".ST_APPROVED."' AND is_active=0";
            $transaction_details = $callConfig->getAllRows($query);
            $return['REFTRANSACTIONSLIST'] = $transaction_details;

        }elseif($gettype== "REFTRANSACTIONS"){
            $ref_type = $get['ref_type'];
            $ref_id = $get[self::KEY_REF_ID];
            $sql = "SELECT id, page_title,item_table, tax_table FROM dynamic_mapping_tables WHERE company_id = $company_id AND application_table_name= '$ref_type' AND item_table !='' AND tax_table !='' AND is_active = 0 ";
                $res = $callConfig->getRow($sql);
                $child_table = $res->item_table ?? '';
                $tax_table = $res->tax_table ?? '';
                $query = "SELECT * FROM $ref_type t1 WHERE t1.id=$ref_id  AND t1.company_id=$company_id AND t1.is_active=0";
                $transaction_details = $callConfig->getRow($query);
                $return['transaction_details'] = $transaction_details;
                if($child_table != ''){
                    $query = "SELECT t2.*, concat(uoms.uom_short_code,'-',uoms.uom_name) unit_name FROM $child_table t2 JOIN  unit_of_measurements uoms ON uoms.id = t2.purchase_uom AND uoms.company_id=$company_id AND uoms.is_active=0 WHERE t2.transaction_id=$ref_id AND  t2.company_id=$company_id AND t2.is_active=0";
                    $data = $callConfig->getAllRows($query);
                    $return['transaction_items'] = $data;
                }
                if($tax_table != ''){
                    $query = "SELECT * FROM $tax_table t3 WHERE t3.transaction_id=$ref_id AND  t3.company_id=$company_id AND t3.is_active=0";
                    $taxes = $callConfig->getAllRows($query);
                    $return['transaction_item_taxes'] = $taxes;
                }
        }else{
            return $this->getProcurementFormMasterData($company_id, $transaction_table, $get['rid']);
        }
        return $return;
    }

    private function getProcurementFormMasterData($companyId, $transactionTable, $rid){
        global $callConfig;
        $return = array();
        $display_attribute_typessql = "SELECT content FROM display_attribute_types WHERE company_id = '".$companyId."' AND application_table_name = '".$transactionTable."' AND field_name = 'party_id'";
        $parttable_name = $callConfig->getRow($display_attribute_typessql);
        $qquery = urldecode($parttable_name->content);
        $explosql = explode("FROM", $qquery);
        if(isset($explosql[1])){
            $explosql = explode(" ", trim($explosql[1]));
        }
        $partytable = trim($explosql[0]);
        if($partytable == "vendors"){
            $query = "SELECT id, display_name, company FROM vendors WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
            $return['party'] = $callConfig->getAllRows($query);
        }else{
            $partytable = "customers";
            $query = "SELECT id, display_name, company FROM customers WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
            $return['party'] = $callConfig->getAllRows($query);
        }
        $return['party_table'] = $partytable;

        $uomquery = "SELECT id, uom_name, uom_short_code FROM unit_of_measurements WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
        $unit_of_measurements = array();
        foreach($callConfig->getAllRows($uomquery) as $uomre){
            $unit_of_measurements[$uomre->id] = $uomre;
        }
        $return['unit_of_measurements'] = $unit_of_measurements;

        $queryau = "SELECT id, display_name FROM application_users WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
        $return['application_users'] = $callConfig->getAllRows($queryau);

        $queryau = "SELECT id, code, code_description, IF(gst = '' OR gst IS NULL, 0, gst) AS gst FROM hsn_and_sac_codes WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
        $hsn_and_sac_codes = array();
        foreach($callConfig->getAllRows($queryau) as $hsnre){
            $hsn_and_sac_codes[$hsnre->id] = $hsnre;
        }
        $return['hsn_and_sac_codes'] = $hsn_and_sac_codes;

        $payment_termssql = "SELECT id, payment_term_name, content  FROM `payment_terms` WHERE company_id = '".$companyId.self::SQL_QUOTE_AND_IS_ACTIVE_EQ0;
        $payment_terms = array();
        foreach($callConfig->getAllRows($payment_termssql) as $payterm){
            $payment_terms[$payterm->id] = $payterm;
        }
        $return['payment_terms'] = $payment_terms;

        $query = "SELECT id, sku, item_name, item_description, sale_price, purchase_uom, hsn_and_sac_code_id FROM items WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
        $itemsres = $callConfig->getAllRows($query);
        foreach($itemsres as $itmres){
            $itmres->purchase_uom_name = $unit_of_measurements[$itmres->purchase_uom]->uom_name;
            $itmres->code = $hsn_and_sac_codes[$itmres->hsn_and_sac_code_id]->code;
            $itmres->code_description = $hsn_and_sac_codes[$itmres->hsn_and_sac_code_id]->code_description;
            $itmres->gst = $hsn_and_sac_codes[$itmres->hsn_and_sac_code_id]->gst;
        }
        $return['items'] = $itemsres;

        $adsql = "SELECT id, charge_name  FROM additional_charges WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
        $return['additional_charges'] = $callConfig->getAllRows($adsql);

        if(isset($rid) && $rid != ''){
            $sql = "SELECT id, page_title,item_table, tax_table FROM dynamic_mapping_tables WHERE company_id = $companyId AND application_table_name= '$transactionTable' AND is_active = 0 ";
            $res = $callConfig->getRow($sql);
            $return['mapping_table'] = $res;
            $child_table = $res->item_table ?? '';
            $tax_table = $res->tax_table ?? '';

            $query = "SELECT * FROM $transactionTable t1 WHERE t1.id=$rid  AND t1.company_id=$companyId AND t1.is_active=0";
            $transaction_details = $callConfig->getRow($query);
            $return['transaction_details'] = $transaction_details;
            if($child_table != ''){
                $query = "SELECT t2.*, concat(uoms.uom_short_code,'-',uoms.uom_name) unit_name FROM $child_table t2
                JOIN  unit_of_measurements uoms ON uoms.id = t2.purchase_uom AND uoms.company_id=$companyId AND uoms.is_active=0
                WHERE t2.transaction_id=$rid AND  t2.company_id=$companyId AND t2.is_active=0";
                $return['transaction_items'] = $callConfig->getAllRows($query);
            }
            if($tax_table != ''){
                $query = "SELECT * FROM $tax_table t3 WHERE t3.transaction_id=$rid AND  t3.company_id=$companyId AND t3.is_active=0";
                $return['transaction_item_taxes'] = $callConfig->getAllRows($query);
            }
            if($transaction_details->party_id !=""){
                $placeofsupplysql = "SELECT * FROM customer_vendor_gst_billing_details WHERE company_id = $companyId AND transaction_table= '$partytable' AND customer_vendor_id = '".$transaction_details->party_id."'  AND is_active = 0 ";
                $return['place_of_supply_list'] = $callConfig->getRow($placeofsupplysql);
            }
        }

        $adsql = "SELECT genc_detail_code, genc_detail_name  FROM  general_code_details WHERE company_id = ".$companyId." AND genc_code ='TRANSACTIONTYPE' AND is_active = 0 ORDER BY id ASC";
        $return[self::KEY_TYPE] = $callConfig->getAllRows($adsql);

        $adsql = "SELECT  id, cost_center_name  FROM  cost_centers WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
        $return['cost_centers'] = $callConfig->getAllRows($adsql);

        $adsql = "SELECT id, salesman_name  FROM  salesmen WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
        $return['salesmen'] = $callConfig->getAllRows($adsql);

        $adsql = "SELECT id, currency_name  FROM  currencies WHERE company_id = ".$companyId.self::SQL_AND_IS_ACTIVE_0;
        $return['currencies'] = $callConfig->getAllRows($adsql);

        $return['opex_cpex'] = array('0'=>'OPEX','1'=>'CAPEX');
        return $return;
    }
    public function togetmobileadminhrdashboard($get){
         global $callConfig;
         $endate = date('Y')."-12-31";
        //new
        if($get['dash_date']!=""){
            $today_date = $get['dash_date'];
        }else{
            $today_date = date(self::DATE_FORMAT_YMD);
        }

         // yesterday
        $yesterday = date(self::DATE_FORMAT_YMD, strtotime($today_date . " -1 day"));

         $division_whr = "";
        //Get Total Eployeees
         $sql ="SELECT  emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image,  emp.reporting_manager_id FROM `employees` emp WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END )";

         $empdatas = $callConfig->getAllRows($sql);
         $allemplist = array();
         $data[self::KEY_TODAY_ATTENDANTS]['total_emp'] =count((array)$empdatas);
         $male_count = 0;
        $female_count = 0;
        $manager_ids = array();
         foreach($empdatas as $empdatas1){
             $data[self::KEY_TODAY_ATTENDANTS]['total_emp_ids'][] =$empdatas1;
             $allemplist[$empdatas1->id] = $empdatas1;
             if($empdatas1->employee_type == '1'){
                 $data[self::KEY_TODAY_ATTENDANTS]['regular_emp_ids'][] =$empdatas1;
             }elseif($empdatas1->employee_type == '2'){
                 $data[self::KEY_TODAY_ATTENDANTS]['contract_emp_ids'][] =$empdatas1;
             }
             if($empdatas1->gender == 'M'){
                $male_count++;
            }elseif($empdatas1->gender == 'F'){
                $female_count++;
            }
            if(!empty($empdatas1->reporting_manager_id)){
                $manager_ids[$empdatas1->reporting_manager_id] = 1; // unique
            }

        }
        $total_emp_count = count((array)$empdatas);
        $manager_count = count((array)$manager_ids);
        $employee_count = $total_emp_count - $manager_count;

        if($total_emp_count > 0){
            $data[self::KEY_TODAY_ATTENDANTS]['manager_percentage'] = round(($manager_count / $total_emp_count) * 100, 2);
            $data[self::KEY_TODAY_ATTENDANTS]['employee_percentage'] = round(($employee_count / $total_emp_count) * 100, 2);
        }else{
            $data[self::KEY_TODAY_ATTENDANTS]['manager_percentage'] = 0;
            $data[self::KEY_TODAY_ATTENDANTS]['employee_percentage'] = 0;
        }

        $data[self::KEY_TODAY_ATTENDANTS]['male_count'] = $male_count;
        $data[self::KEY_TODAY_ATTENDANTS]['female_count'] = $female_count;

        if($total_emp_count > 0){
            $data[self::KEY_TODAY_ATTENDANTS]['male_percentage'] = round(($male_count / $total_emp_count) * 100, 2);
            $data[self::KEY_TODAY_ATTENDANTS]['female_percentage'] = round(($female_count / $total_emp_count) * 100, 2);
        }else{
            $data[self::KEY_TODAY_ATTENDANTS]['male_percentage'] = 0;
            $data[self::KEY_TODAY_ATTENDANTS]['female_percentage'] = 0;
        }
        //Get Today Attendance Eployeees
         $attendancequery = "SELECT emp.id, emp.display_name,emp.email_id from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$today_date."' AND eda.is_active = emp.is_active group by emp.id";
         $attendata = $callConfig->getAllRows($attendancequery);
         $today_count = count((array)$attendata);
         $data[self::KEY_TODAY_ATTENDANTS][self::KEY_PRESENT] =$today_count;
         $data[self::KEY_TODAY_ATTENDANTS]['present_ids'] = array();
         foreach($attendata as $empdatas2){
             $data[self::KEY_TODAY_ATTENDANTS]['present_ids'][] =$empdatas2;
             unset($allemplist[$empdatas2->id]);
        }
        foreach($allemplist as $adsaaa){
            $onads[] =  $adsaaa;
        }
         $data['totalpressent'] = count((array)$attendata);
         $data[self::KEY_TODAY_ATTENDANTS]['absent_ids'] = $onads;

         //Get Yesterday Attendance Eployeees
         $query = "SELECT emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, '' as other from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$yesterday."' AND eda.is_active = emp.is_active group by emp.id";
         $result = $callConfig->getAllRows($query);
         $yesterday_count = count((array)$result);
         $data['yesterdayattendants'][self::KEY_PRESENT] =$yesterday_count;
        if($yesterday_count > 0){
            $percent_change = (($today_count - $yesterday_count) / $yesterday_count) * 100;
        } else {
            $percent_change = ($today_count > 0) ? 100 : 0; // handle divide by zero
        }

        $data['attendance_change_percent'] = round($percent_change, 2);

        //Get Today Planed Leaves Eployeees
         $leaves = "SELECT emp.id, emp.display_name,emp.email_id FROM `employees` emp, employee_leaves el WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') )ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$today_date."' AND el.leave_to >= '".$today_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";

         $leavesdata = $callConfig->getAllRows($leaves);
         $data[self::KEY_TODAY_ATTENDANTS]['planned_leave_ids'] = array();
         foreach($leavesdata as $empdatas3){
             $empdatas3->type = self::TBL_EMPLOYEE_LEAVES;
             $data[self::KEY_TODAY_ATTENDANTS]['planned_leave_ids'][] =$empdatas3;
        }
         $empcnt = count((array)$leavesdata)!="0"?count((array)$leavesdata):0;
         $attendataempcnt =count((array)$attendata)!="0"?count((array)$attendata):0;
         $data[self::KEY_TODAY_ATTENDANTS]['planned_leave'] =$empcnt;
         $data[self::KEY_TODAY_ATTENDANTS]['absent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
         $data['totalabsent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);

        $pending_leave_query = "SELECT el.id AS emp_leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other, el.application_date FROM employees emp, employee_leaves el  WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1 ELSE 2 END ) = 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE]."')  OR (emp.reporting_manager_id = '".$get[self::KEY_USER_ID]."')  OR (emp.hr_buddy = '".$get[self::KEY_EMP_ID]."')  OR (emp.finance_buddy = '".$get[self::KEY_EMP_ID]."') ) ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND el.current_status < '".ST_APPROVED."' AND el.is_active = 0";

        $pending_leave_data = $callConfig->getAllRows($pending_leave_query);
        $data[self::KEY_TODAY_ATTENDANTS]['pending_leave_ids'] = array();
        foreach($pending_leave_data as $pending_emp){
            $pending_emp->type = self::TBL_EMPLOYEE_LEAVES;
            $data[self::KEY_TODAY_ATTENDANTS]['pending_leave_ids'][] = $pending_emp;
        }
        $data[self::KEY_TODAY_ATTENDANTS]['pending_leave_count'] = count((array)$pending_leave_data);

        $total_emp_count = count((array)$empdatas);
        $data['last_7_days'] = array();

        for($i = 1; $i <= 7; $i++){
            $trend_date = date(self::DATE_FORMAT_YMD, strtotime($today_date . " -".$i." day"));
            $trend_key  = "day".$i;
            // Present count
            $attendancequery = "SELECT emp.id FROM employees emp, employee_daily_attendances eda
                WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr
                AND (CASE
                        WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1
                        WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1
                        ELSE 2
                    END
                ) = 1
                AND (CASE
                        WHEN '3' <> '1' THEN (
                            ('1' = '".$get[self::KEY_ADMIN_TYPE]."')
                            OR (emp.reporting_manager_id = '".$get[self::KEY_USER_ID]."')
                            OR (emp.hr_buddy = '".$get[self::KEY_EMP_ID]."')
                            OR (emp.finance_buddy = '".$get[self::KEY_EMP_ID]."')
                        )ELSE (
                            emp.vendor_id = (
                                SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."'
                            )) END
                ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$trend_date."' AND eda.is_active = emp.is_active GROUP BY emp.id";

            $attendance_data = $callConfig->getAllRows($attendancequery);
            $present_count = count((array)$attendance_data);

            // On leave count for that date
            $next_day = date(self::DATE_FORMAT_YMD, strtotime($trend_date . ' +1 day'));
            $leavequery = "SELECT emp.id FROM employees emp, employee_leaves el
                WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr
                AND (CASE
                        WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1
                        WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1
                        ELSE 2
                    END
                ) = 1
                AND (CASE
                        WHEN '3' <> '1' THEN (
                            ('1' = '".$get[self::KEY_ADMIN_TYPE]."')
                            OR (emp.reporting_manager_id = '".$get[self::KEY_USER_ID]."')
                            OR (emp.hr_buddy = '".$get[self::KEY_EMP_ID]."')
                            OR (emp.finance_buddy = '".$get[self::KEY_EMP_ID]."')
                        )
                        ELSE (
                            emp.vendor_id = (
                                SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."'
                            )) END
                ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$next_day."' AND el.leave_to >= '".$trend_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";

            $leave_data = $callConfig->getAllRows($leavequery);
            $leave_count = count((array)$leave_data);

            $absent_count = $total_emp_count - ($present_count + $leave_count);
            if($absent_count < 0){
                $absent_count = 0;
            }

            $data['last_7_days'][] = array(
                'key'     => $trend_key,
                self::KEY_VALUE   => $trend_date,
                'total'   => $total_emp_count,
                self::KEY_PRESENT => $present_count,
                'absent'  => $absent_count,
                'on_leave'=> $leave_count
            );
        }

        $resigned_employees_query = "SELECT emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image, emp.reporting_manager_id, et.employee_leaving_date, COUNT(ea.id) AS asset_count
        FROM employees emp
        INNER JOIN employee_terminations et ON et.emp_id = emp.id AND et.company_id = emp.company_id AND et.is_active = 0
        LEFT JOIN employee_assets ea ON ea.emp_id = emp.id AND ea.company_id = emp.company_id AND ea.is_active = 0
        WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 GROUP BY emp.id,emp.display_name,emp.email_id,emp.gender,emp.employee_type,emp.emp_code,emp.phone,emp.image,emp.reporting_manager_id";

        $resigned_employees = $callConfig->getAllRows($resigned_employees_query);
        $data['resigned_employees'] = $resigned_employees;
        $data['resigned_employees_count'] = count((array)$resigned_employees);

        $is_holiday = 'N';
        $holiday_year = date('Y', strtotime($today_date));
        $holiday_query = "SELECT id FROM holidays  WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND holiday_year = '".$holiday_year."' AND holiday_date = '".$today_date.self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $holiday_result = $callConfig->getAllRows($holiday_query);
        if(count((array)$holiday_result)>0){
            $is_holiday = 'Y';
        }
        $data['is_holiday'] = $is_holiday;


         // Transport gateway
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway_ids'] = array();
         $query = "SELECT tpg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id,  emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tpg.visit_purpose) as other, GROUP_CONCAT(emp1.display_name) as emp_names FROM transport_gateway tpg
            LEFT JOIN employees emp ON (emp.id = tpg.driver_name OR emp.id=tpg.user_id) AND emp.company_id = tpg.company_id AND emp.is_active=0
            LEFT JOIN employees emp1 ON FIND_IN_SET(emp1.id, tpg.emp_ids) AND emp1.company_id = tpg.company_id  AND emp1.is_active = 0
            WHERE  tpg.company_id={$get[self::KEY_COMPANY_ID]} AND tpg.is_active=0 AND ( tpg.entry_date ='".$today_date."' OR tpg.exit_date ='".$today_date."' ) AND tpg.is_active = 0  GROUP BY tpg.id";
         $transport_getway_data = $callConfig->getAllRows($query);
         foreach($transport_getway_data as $transport_getway){
             $transport_getway->type = self::TBL_TRANSPORT_GATEWAY;
             $data[self::KEY_TODAY_ATTENDANTS]['transport_getway_ids'][] =$transport_getway;
        }
         $transport_getway_cnt = count((array)$transport_getway_data)!="0"?count((array)$transport_getway_data):0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway'] =$transport_getway_cnt;

         // Transport Personal  gateway
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_TRANSPORT_PERSONAL_GATEWAY] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_personal_gateway_ids'] = array();
         $query = "SELECT tg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id,  emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tg.visit_purpose) as other  FROM transport_personal_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]."  AND tg.entry_date ='".$today_date."' AND tg.is_active = 0";
         $transport_personal_gateway = $callConfig->getAllRows($query);
         foreach($transport_personal_gateway as $transport_getway){
             $transport_getway->type = self::TBL_TRANSPORT_PERSONAL_GATEWAY;
             $data[self::KEY_TODAY_ATTENDANTS]['transport_personal_gateway_ids'][] =$transport_getway;
        }
         $transport_personal_gateway_cnt = count((array)$transport_personal_gateway)!="0"?count((array)$transport_personal_gateway):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_TRANSPORT_PERSONAL_GATEWAY] =$transport_personal_gateway_cnt;

         // Leave
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_EMPLOYEE_LEAVES] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['employee_leaves_ids'] = array();
         $query = "SELECT tg.id, emp.id, emp.emp_code, emp.display_name, emp.email_id,  CONCAT(tg.leave_from, ' - ', tg.leave_to) as other, emp.phone, emp.image, emp.reporting_manager_id  FROM employee_leaves tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]." AND tg.leave_from ='".$today_date."' AND tg.is_active = 0";
         $employee_leaves_data = $callConfig->getAllRows($query);
         foreach($employee_leaves_data as $employee_leave){
             $employee_leave->type = self::TBL_EMPLOYEE_LEAVES;
             $data[self::KEY_TODAY_ATTENDANTS]['employee_leaves_ids'][] =$employee_leave;
        }
         $transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_EMPLOYEE_LEAVES] =$transport_personal_gateway_cnt;
         // stationery
         $data[self::KEY_TODAY_ATTENDANTS]['stationery'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['stationery_ids'] = array();

         $query = "SELECT sr.id stationery_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other, emp.phone, emp.image, emp.reporting_manager_id  FROM stationery_requests sr, employees emp WHERE emp.id = sr.emp_id AND sr.company_id = ".$get[self::KEY_COMPANY_ID]."  AND sr.request_date ='".$today_date."' AND sr.is_active = 0";
         $stationery_requests = $callConfig->getAllRows($query);
         foreach($stationery_requests as $row){
             $row->type = self::TBL_STATIONERY_REQUESTS;
             $data[self::KEY_TODAY_ATTENDANTS]['stationery_ids'][] =$row;
        }
         $stationery_requests_cnt = count((array)$stationery_requests)!="0"?count((array)$stationery_requests):0;
         $data[self::KEY_TODAY_ATTENDANTS]['stationery'] = $stationery_requests_cnt;

         // assets
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_ASSETS] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['assets_ids'] = array();

         $query = "SELECT ar.id asset_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other, emp.phone, emp.image, emp.reporting_manager_id  FROM asset_requests ar, employees emp WHERE emp.user_id = ar.originator AND ar.company_id = ".$get[self::KEY_COMPANY_ID]."  AND ar.asset_required_start_date ='".$today_date."' AND ar.is_active = 0";
         $asset_requests = $callConfig->getAllRows($query);
         foreach($asset_requests as $row){
             $row->type = self::TBL_ASSET_REQUESTS;
             $data[self::KEY_TODAY_ATTENDANTS]['assets_ids'][] =$row;
        }
         $asset_requests_cnt = count((array)$asset_requests)!="0"?count((array)$asset_requests):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_ASSETS] = $asset_requests_cnt;


         // attenmiss punch
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch_ids'] = array();
         $query = "SELECT tg.id as daily_attendances_id, emp.id, emp.emp_code, emp.display_name, emp.email_id,CONCAT('Clock : ', tg.clock_in, ' - ' , tg.clock_out ) as other, emp.phone, emp.image, emp.reporting_manager_id  FROM employee_daily_attendances tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]." AND tg.attendace_date ='".$today_date."' AND `attendance_key_short_code` IN ('Z','MP') AND tg.is_active = 0";
         $employee_miss_data = $callConfig->getAllRows($query);
         foreach($employee_miss_data as $employee_leave){
             $employee_leave->type = self::TBL_EMPLOYEE_DAILY_ATTENDANCES;
             $data[self::KEY_TODAY_ATTENDANTS]['miss_punch_ids'][] =$employee_leave;
        }
         $miss_cnt = count((array)$employee_miss_data)!="0"?count((array)$employee_miss_data):0;
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch'] =$miss_cnt;

         $approvals = array();

        $tables_array = array(self::TBL_TRANSPORT_GATEWAY,self::TBL_TRANSPORT_PERSONAL_GATEWAY,self::TBL_EMPLOYEE_LEAVES,self::TBL_EMPLOYEE_DAILY_ATTENDANCES,self::TBL_ASSET_REQUESTS,self::TBL_STATIONERY_REQUESTS);

        // make IN clause properly
        $table_list = "'" . implode("','", $tables_array) . "'";

        $query = "SELECT
            tal.id AS transaction_approval_log_id,
            tal.transaction_table AS type,
            tal.transaction_id AS rid,
            COALESCE(emp1.display_name, '-') AS requested,
            COALESCE(emp2.display_name, '-') AS pending_approval,
            tal.previous_status AS current_status
          FROM transaction_approval_logs tal
          LEFT JOIN employees emp1
                 ON tal.assigner_user_id = emp1.user_id
                AND emp1.company_id = tal.company_id
                AND emp1.is_active = 0
          LEFT JOIN employees emp2
                 ON tal.user_id = emp2.user_id
                AND emp2.company_id = tal.company_id
                AND emp2.is_active = 0
          WHERE tal.response_type = 0
            AND tal.company_id = {$get[self::KEY_COMPANY_ID]}
            AND tal.response_date IS NULL
            AND tal.transaction_table IN ($table_list)";

        $approval_data = $callConfig->getAllRows($query);
        // label mapping
        $labels = array(
            self::TBL_TRANSPORT_GATEWAY => 'Transport Gateway',
            self::TBL_TRANSPORT_PERSONAL_GATEWAY => 'Transport Personal Gateway',
            self::TBL_EMPLOYEE_LEAVES => 'Employee Leaves',
            self::TBL_EMPLOYEE_DAILY_ATTENDANCES => 'Employee Daily Attendances',
            self::TBL_ASSET_REQUESTS => 'Asset Requests',
            self::TBL_STATIONERY_REQUESTS => 'Stationery Requests'
        );

        foreach ($approval_data as $value) {
            $type = $value->type;   // because selected as AS type
            $rid  = $value->rid;

            // initialize key if not exists
            if (!isset($approvals[$type])) {
                $approvals[$type] = array();
            }

            $approvals[$type][] = array(
                self::KEY_LABEL => isset($labels[$type]) ? $labels[$type] : ucwords(str_replace('_', ' ', $type)),
                'requested' => $value->requested,
                'pending_approval' => $value->pending_approval,
                'approval_status' => $value->current_status, // or map separately if needed
                'rid' => $rid,
                'type' => $type,
                'transaction_approval_log_id' => $value->transaction_approval_log_id
            );
        }

        $data['ongoing_operations']['approval_data'] = $approvals;

        $query = "SELECT id,table_name,name,status,tigger,fixable,action_label,display_priority FROM status_progress WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0 AND table_name IN ($table_list)";
        $status_data = $callConfig->getAllRows($query);
        $status_arr = array();
        foreach ($status_data as $row) {
            $status_arr[$row->table_name][$row->status] = $row;
        }
        $data['ongoing_operations']['status_arr'] = $status_arr;

        //Get regular,contact,male,female,manger_count,employees Count Eployeees
         $query="SELECT SUM(if(emp.employee_type='1',1,0))as regular, SUM(if(emp.employee_type='2',1,0))as contact, COUNT(emp.id) as em_count FROM employees emp WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END )";
         $nempdata = $callConfig->getRow($query);
         $data['nemp'] = $nempdata;

        //Get Joing Employees
         $mequery= "SELECT count(emp.id) as joinn FROM employees emp WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND ( CASE WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID]."') OR (emp.hr_buddy) = '".$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) AND emp.joining_date >= '".$thismonthstartdate."' AND emp.joining_date <= '".$thismonthenddate."' AND is_active=0";
         $medata = $callConfig->getRow($mequery);
         $data['joining'] = $medata->joinn;

        //Get leaving Employees
         $mequery= "SELECT count(emp.id) as realving FROM employees emp WHERE emp.date_of_leaving >= '".$thismonthstartdate."' AND emp.date_of_leaving <= '".$thismonthenddate."' AND emp.date_of_leaving IS NOT NULL AND emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END ) AND  is_active=0";
         $medata1 = $callConfig->getRow($mequery);
         $data['relive'] = $medata1->realving;

         $query="SELECT SUM(if(emp.gender='M',1,0)) as male, SUM(if(emp.gender='F',1,0))as female, count(DISTINCT(emp.reporting_manager_id)) as manger_count, COUNT(emp.id) as em_count FROM employees emp WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END) ";
         //AND emp.joining_date >= '".$thismonthstartdate."' AND emp.joining_date <= '".$thismonthenddate."'
         $nempdata = $callConfig->getRow($query);
         $data['daterangenemp'] = $nempdata;
         $sqqlgraph = "SELECT sum(m12) as j12,
         sum(m11) as j11,
         sum(m10) as j10,
         sum(m9) as j9,
         sum(m8) as j8,
         sum(m7) as j7,
         sum(m6) as j6,
         sum(m5) as j5,
         sum(m4) as j4,
         sum(m3) as j3,
         sum(m2) as j2,
         sum(m1) as j1,
         sum(lm12) as r12,
         sum(lm11) as r11,
         sum(lm10) as r10,
         sum(lm9) as r9,
         sum(lm8) as r8,
         sum(lm7) as r7,
         sum(lm6) as r6,
         sum(lm5) as r5,
         sum(lm4) as r4,
         sum(lm3) as r3,
         sum(lm2) as r2,
         sum(lm1) as r1
         FROM    (
         SELECT    DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 12 MONTH), '%Y-%m' )curdt,
         DATE_FORMAT(emp.joining_date, '%Y-%m')dojm,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' )
         THEN 1
         END M12,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' )
         THEN 1
         END M11,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' )
         THEN 1
         END M10,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' )
         THEN 1
         END M9,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' )
         THEN 1
         END M8,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' )
         THEN 1
         END M7,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' )
         THEN 1
         END M6,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' )
         THEN 1
         END M5,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' )
         THEN 1
         END M4,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' )
         THEN 1
         END M3,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' )
         THEN 1
         END M2,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' )
         THEN 1
         END M1,
         DATE_FORMAT(emp.joining_date, '%Y-%m') monyr,
         DATE_FORMAT(emp.date_of_leaving, '%Y-%m')dolm,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' )
         THEN 1
         END LM12,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' )
         THEN 1
         END LM11,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' )
         THEN 1
         END LM10,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' )
         THEN 1
         END LM9,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' )
         THEN 1
         END LM8,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' )
         THEN 1
         END LM7,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' )
         THEN 1
         END LM6,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' )
         THEN 1
         END LM5,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' )
         THEN 1
         END LM4,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' )
         THEN 1
         END LM3,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' )
         THEN 1
         END LM2,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' )
         THEN 1
         END LM1,
         DATE_FORMAT(emp.date_of_leaving, '%Y-%m') lmonyrl
         FROM     employees emp
         WHERE    emp.company_id = 1
         AND       (
         (emp.joining_date >= '".$thisyearfirstmonthlastdate."' AND emp.joining_date <= '".$endate."')
         OR (emp.date_of_leaving >= '".$thisyearfirstmonthlastdate."' AND emp.date_of_leaving <= '".$endate."')
         )
         AND emp.is_active = 0 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') ) ELSE( emp.vendor_id = (SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."'))END))a";

         $sqqlgraphres = $callConfig->getRow($sqqlgraph);
         foreach($sqqlgraphres as $sqkey=>$sqq){
             $passsqqlgraphres[]=array('id'=>$sqkey,self::KEY_VALUE=>$sqq);
         }
         $data['graphjoinandleave'] = $passsqqlgraphres;
         $salarygraph ="SELECT sum(m12) as j12, sum(m11) as j11, sum(m10) as j10, sum(m9) as j9, sum(m8) as j8, sum(m7) as j7, sum(m6) as j6, sum(m5) as j5, sum(m4) as j4, sum(m3) as j3, sum(m2) as j2, sum(m1) as j1 FROM ( SELECT DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 12 MONTH), '%Y-%m' )curdt, DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m')dojm, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M12, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M11, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M10, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M9, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M8, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M7, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M6, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M5, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M4, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M3, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M2, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M1, DATE_FORMAT(emp.joining_date, '%Y-%m') monyr FROM employees emp, pay_run_summary prs, pay_run_summary_components prsc WHERE emp.company_id = 1 AND emp.id = prs.employee_id AND emp.company_id = prs.company_id AND prsc.company_id = prs.company_id AND prsc.pay_run_summary_id = prs.id AND prsc.employee_id = emp.id AND prsc.component_id = 1 AND (( DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') >= '".$thisyearfirstmonth."') AND ( DATE_FORMAT(CONCAT(prs.pay_year, '-',prs.pay_month, '-','01'), '%Y-%m') <= '".$thisyearfirstmonth."')) AND prsc.is_active = 0 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') )ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) ) a";
         $salarygraphres = $callConfig->getRow($salarygraph);
         foreach($salarygraphres as $sqkey=>$sqq){
             $passsalarygraphres[]=array('id'=>$sqkey,self::KEY_VALUE=>$sqq);
         }
         $data['salarygraphres'] = $passsalarygraphres;

         $query = "SELECT id,designation_name FROM designations WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0";
        $res = $callConfig->getAllRows($query);
        $designation_arr = array();
        foreach ($res as $row) {
            $designation_arr[$row->id] = $row;
        }
        $data['designation_arr'] = $designation_arr;

        $query = "SELECT id,department_name FROM departments WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0";
        $res = $callConfig->getAllRows($query);
        $department_arr = array();
        foreach ($res as $row) {
            $department_arr[$row->id] = $row;
        }
        $data['department_arr'] = $department_arr;
         return $data;
    }
    public function backviewtogetmobileadminhrdashboard($get){
         global $callConfig;
         $endate = date('Y')."-12-31";
        //new
         if($get['dash_date']!=""){
            $today_date = $get['dash_date'];
        }else{
            $today_date = date(self::DATE_FORMAT_YMD);
        }
        //Get Total Eployeees
         $sql ="SELECT id, display_name, email_id, employee_type, '' as other FROM `employees` emp WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END )";

         $empdatas = $callConfig->getAllRows($sql);
         $allemplist = array();
         $data[self::KEY_TODAY_ATTENDANTS]['total_emp'] =count((array)$empdatas);
         foreach($empdatas as $empdatas1){
             $data[self::KEY_TODAY_ATTENDANTS]['total_emp_ids'][] =$empdatas1;
             $allemplist[$empdatas1->id] = $empdatas1;
             if($empdatas1->employee_type == '1'){
                 $data[self::KEY_TODAY_ATTENDANTS]['regular_emp_ids'][] =$empdatas1;
             }elseif($empdatas1->employee_type == '2'){
                 $data[self::KEY_TODAY_ATTENDANTS]['contract_emp_ids'][] =$empdatas1;
             }

        }
        //Get Today Attendance Eployeees
         $attendancequery = "SELECT emp.id, emp.display_name,emp.email_id, '' as other from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$today_date."' AND eda.is_active = emp.is_active group by emp.id";
         $attendata = $callConfig->getAllRows($attendancequery);
         $data[self::KEY_TODAY_ATTENDANTS][self::KEY_PRESENT] =count((array)$attendata);
         $data[self::KEY_TODAY_ATTENDANTS]['present_ids'] = array();
         foreach($attendata as $empdatas2){
             $data[self::KEY_TODAY_ATTENDANTS]['present_ids'][] =$empdatas2;
             unset($allemplist[$empdatas2->id]);
        }
        foreach($allemplist as $adsaaa){
            $onads[] =  $adsaaa;
        }
         $data['totalpressent'] = count((array)$attendata);
         $data[self::KEY_TODAY_ATTENDANTS]['absent_ids'] = $onads;

        //Get Today Planed Leaves Eployeees
         $prsday = date(self::DATE_FORMAT_YMD, strtotime(self::STR_PLUS_1_DAY, strtotime($today_date)));
         $leaves = "SELECT el.id AS leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other FROM `employees` emp, employee_leaves el WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') )ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$prsday."' AND el.leave_to >= '".$today_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";

         $leavesdata = $callConfig->getAllRows($leaves);
         $data[self::KEY_TODAY_ATTENDANTS]['planned_leave_ids'] = array();
         foreach($leavesdata as $empdatas3){
             $empdatas3->type = self::TBL_EMPLOYEE_LEAVES;
             $data[self::KEY_TODAY_ATTENDANTS]['planned_leave_ids'][] =$empdatas3;
        }
         $empcnt = count((array)$leavesdata)!="0"?count((array)$leavesdata):0;
         $attendataempcnt =count((array)$attendata)!="0"?count((array)$attendata):0;
         $data[self::KEY_TODAY_ATTENDANTS]['planned_leave'] =$empcnt;
         $data[self::KEY_TODAY_ATTENDANTS]['absent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
         $data['totalabsent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);


         $pending_leave_query = "SELECT el.id AS emp_leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other, el.application_date FROM employees emp, employee_leaves el  WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0  AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1 ELSE 2 END ) = 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE]."')  OR (emp.reporting_manager_id = '".$get[self::KEY_USER_ID]."')  OR (emp.hr_buddy = '".$get[self::KEY_EMP_ID]."')  OR (emp.finance_buddy = '".$get[self::KEY_EMP_ID]."') ) ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND el.current_status < '".ST_APPROVED."' AND el.is_active = 0";

        $pending_leave_data = $callConfig->getAllRows($pending_leave_query);
        $data[self::KEY_TODAY_ATTENDANTS]['pending_leave_ids'] = array();
        foreach($pending_leave_data as $pending_emp){
            $pending_emp->type = self::TBL_EMPLOYEE_LEAVES;
            $data[self::KEY_TODAY_ATTENDANTS]['pending_leave_ids'][] = $pending_emp;
        }
        $data[self::KEY_TODAY_ATTENDANTS]['pending_leave_count'] = count((array)$pending_leave_data);

         // Transport gateway
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway_ids'] = array();
         $query = "SELECT tpg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id,  emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tpg.visit_purpose) as other, GROUP_CONCAT(emp1.display_name) as emp_names FROM transport_gateway tpg
            LEFT JOIN employees emp ON (emp.id = tpg.driver_name OR emp.id=tpg.user_id) AND emp.company_id = tpg.company_id AND emp.is_active=0
            LEFT JOIN employees emp1 ON FIND_IN_SET(emp1.id, tpg.emp_ids) AND emp1.company_id = tpg.company_id  AND emp1.is_active = 0
            WHERE  tpg.company_id={$get[self::KEY_COMPANY_ID]} AND tpg.is_active=0 AND ( tpg.entry_date ='".$today_date."' OR tpg.exit_date ='".$today_date."' ) AND tpg.is_active = 0  GROUP BY tpg.id";

         $transport_getway_data = $callConfig->getAllRows($query);
         foreach($transport_getway_data as $transport_getway){
             $transport_getway->type = self::TBL_TRANSPORT_GATEWAY;
             $data[self::KEY_TODAY_ATTENDANTS]['transport_getway_ids'][] =$transport_getway;
        }
         $transport_getway_cnt = count((array)$transport_getway_data)!="0"?count((array)$transport_getway_data):0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway'] =$transport_getway_cnt;

         // Transport Personal  gateway
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_TRANSPORT_PERSONAL_GATEWAY] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_personal_gateway_ids'] = array();
         $query = "SELECT tg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type,  emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tg.visit_purpose) as other FROM transport_personal_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]."  AND tg.entry_date ='".$today_date."' AND tg.is_active = 0";
         $transport_personal_gateway = $callConfig->getAllRows($query);
         foreach($transport_personal_gateway as $transport_getway){
             $transport_getway->type = self::TBL_TRANSPORT_PERSONAL_GATEWAY;
             $data[self::KEY_TODAY_ATTENDANTS]['transport_personal_gateway_ids'][] =$transport_getway;
        }
         $transport_personal_gateway_cnt = count((array)$transport_personal_gateway)!="0"?count((array)$transport_personal_gateway):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_TRANSPORT_PERSONAL_GATEWAY] =$transport_personal_gateway_cnt;

         // Leave
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_EMPLOYEE_LEAVES] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['employee_leaves_ids'] = array();
         $query = "SELECT tg.id, emp.id, emp.emp_code, emp.display_name, emp.email_id,  CONCAT(tg.leave_from, ' - ', tg.leave_to) as other FROM employee_leaves tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]." AND tg.leave_from ='".$today_date."' AND tg.is_active = 0";
         $employee_leaves_data = $callConfig->getAllRows($query);
         foreach($employee_leaves_data as $employee_leave){
             $employee_leave->type = self::TBL_EMPLOYEE_LEAVES;
             $data[self::KEY_TODAY_ATTENDANTS]['employee_leaves_ids'][] =$employee_leave;
        }
         $transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_EMPLOYEE_LEAVES] =$transport_personal_gateway_cnt;
        // stationery
         $data[self::KEY_TODAY_ATTENDANTS]['stationery'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['stationery_ids'] = array();

         $query = "SELECT sr.id stationery_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other FROM stationery_requests sr, employees emp WHERE emp.id = sr.emp_id AND sr.company_id = ".$get[self::KEY_COMPANY_ID]."  AND sr.request_date ='".$today_date."' AND sr.is_active = 0";
         $stationery_requests = $callConfig->getAllRows($query);
         foreach($stationery_requests as $row){
             $row->type = self::TBL_STATIONERY_REQUESTS;
             $data[self::KEY_TODAY_ATTENDANTS]['stationery_ids'][] =$row;
        }
         $stationery_requests_cnt = count((array)$stationery_requests)!="0"?count((array)$stationery_requests):0;
         $data[self::KEY_TODAY_ATTENDANTS]['stationery'] = $stationery_requests_cnt;

         // assets
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_ASSETS] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['assets_ids'] = array();

         $query = "SELECT ar.id asset_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other FROM asset_requests ar, employees emp WHERE emp.user_id = ar.originator AND ar.company_id = ".$get[self::KEY_COMPANY_ID]."  AND ar.asset_required_start_date ='".$today_date."' AND ar.is_active = 0";
         $asset_requests = $callConfig->getAllRows($query);
         foreach($asset_requests as $row){
             $row->type = self::TBL_ASSET_REQUESTS;
             $data[self::KEY_TODAY_ATTENDANTS]['assets_ids'][] =$row;
        }
         $asset_requests_cnt = count((array)$asset_requests)!="0"?count((array)$asset_requests):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_ASSETS] = $asset_requests_cnt;

         // attenmiss punch
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch_ids'] = array();
         $query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.email_id,CONCAT('Clock : ', tg.clock_in, ' - ' , tg.clock_out ) as other FROM employee_daily_attendances tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]." AND tg.attendace_date ='".$today_date."' AND `attendance_key_short_code` IN ('Z','MP') AND tg.is_active = 0";
         $employee_miss_data = $callConfig->getAllRows($query);
         foreach($employee_miss_data as $employee_leave){
             $employee_leave->type = self::TBL_EMPLOYEE_DAILY_ATTENDANCES;
             $data[self::KEY_TODAY_ATTENDANTS]['miss_punch_ids'][] =$employee_leave;
        }
         $miss_cnt = count((array)$employee_miss_data)!="0"?count((array)$employee_miss_data):0;
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch'] =$miss_cnt;

         $query = "SELECT id,designation_name FROM designations WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0";
        $res = $callConfig->getAllRows($query);
        $designation_arr = array();
        foreach ($res as $row) {
            $designation_arr[$row->id] = $row;
        }
        $data['designation_arr'] = $designation_arr;

        $query = "SELECT id,department_name FROM departments WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0";
        $res = $callConfig->getAllRows($query);
        $department_arr = array();
        foreach ($res as $row) {
            $department_arr[$row->id] = $row;
        }
        $data['department_arr'] = $department_arr;
         return $data;
    }
    public function togetmobileadminhrviewtopemplist($get){
        global $callConfig;
        $res = $this->backviewtogetmobileadminhrdashboard($get);

        $field = $get[self::KEY_FIELD];
        $resdata = $res[self::KEY_TODAY_ATTENDANTS][$field];
        $return['count'] = count($resdata);
        $return['data'] = array_slice($resdata, $get['limit'], $get['lenght']);
        $return['department_arr'] = $res['department_arr'] ?? array();
        $return['designation_arr'] = $res['designation_arr'] ?? array();
        return $return;
    }
    // inventory added by koti start
    /**
     * API 1: Get all categories, optionally get items for a specific category with cart status
     * @param array $get - Must contain: company_id, user_id (for cart join), optional: category_id
     * @return array - Returns categories array, and items array if category_id provided
     */
    public function getStationeryCategoriesWithItems($get) {
        global $callConfig;

        $company_id = $get[self::KEY_COMPANY_ID];
        $user_id = $get[self::KEY_USER_ID];

        // Always get all categories
        $cat_query = "SELECT id, name
                      FROM stationery_categories
                      WHERE company_id = '{$company_id}'
                        AND is_active = 0
                      ORDER BY name ASC";

        $categories = $callConfig->getAllRows($cat_query);

        $result = array(
            self::KEY_STATUS => self::KEY_SUCCESS,
            "categories" => $categories
        );

        // If category_id provided, get items with cart status
        if (isset($get['category_id']) && $get['category_id'] != '') {
            $category_id = $get['category_id'];

            $items_query = "SELECT
                    si.id,
                    si.item_name,
                    si.description,
                    si.image,
                    si.available_qty,
                    si.cost,
                    sc.id AS cart_id,
                    COALESCE(sc.qty, 0) AS cart_qty
                FROM stationery_items si
                LEFT JOIN stationery_cart sc
                    ON sc.item_id = si.id
                    AND sc.company_id = '{$company_id}'
                    AND sc.user_id = '{$user_id}'
                    AND sc.request_no IS NULL
                    AND sc.is_active = 0
                WHERE si.company_id = '{$company_id}'
                  AND si.category_id = '{$category_id}'
                  AND si.available_qty > 0
                  AND si.is_active = 0
                ORDER BY si.item_name ASC";

            $items = $callConfig->getAllRows($items_query);

            $result['items'] = $items;
            $result['category_id'] = $category_id;
        }

        return $result;
    }

    /**
     * API 2: Search stationery items by category and item name (LIKE search)
     * @param array $get - Must contain: company_id, user_id, category_id, item_name
     * @return array - Returns matching items with cart status
     */
    public function searchStationeryItemsByCategory($get) {
        global $callConfig;

        $company_id = $get[self::KEY_COMPANY_ID];
        $user_id = $get[self::KEY_USER_ID];
        $category_id = $get['category_id'];
        $item_name = $get['item_name'] ?? '';

        // Escape special characters for LIKE query
        $item_name = $callConfig->real_escape_string($item_name);

        $query = "SELECT
                si.id,
                si.item_name,
                si.description,
                si.image,
                si.available_qty,
                si.cost,
                sc.id AS cart_id,
                COALESCE(sc.qty, 0) AS cart_qty
            FROM stationery_items si
            LEFT JOIN stationery_cart sc
                ON sc.item_id = si.id
                AND sc.company_id = '{$company_id}'
                AND sc.user_id = '{$user_id}'
                AND sc.request_no IS NULL
                AND sc.is_active = 0
            WHERE si.company_id = '{$company_id}'
              AND si.category_id = '{$category_id}'
              AND si.item_name LIKE '%{$item_name}%'
              AND si.available_qty > 0
              AND si.is_active = 0
            ORDER BY si.item_name ASC";

        $items = $callConfig->getAllRows($query);

        return array(
            self::KEY_STATUS => self::KEY_SUCCESS,
            "items" => $items,
            "search_term" => $get['item_name'] ?? ''
        );
    }

    /**
     * API 3: Manage cart quantities - Add or Remove items from temporary cart
     * @param array $get - Must contain: company_id, user_id
     * @param array $post - Must contain: item_id, qty, cart_type (1=ADD, 0=REMOVE), optional: cart_id
     * @return array - Returns updated cart status
     */
    private function updateStationeryCartQty($cartId, $newQty, $userId){
        global $callConfig;
        $update_array = array(
            'qty' => $newQty,
            self::KEY_UPDATED_BY => $userId,
            self::KEY_UPDATED_DATE => date(self::DATETIME_FORMAT_YMD_HIS)
        );
        $callConfig->updateRecord('stationery_cart', $update_array, 'id', $cartId);
    }

    private function addStationeryCartItem($companyId, $userId, $itemId, $qty, $cartId){
        global $callConfig;
        if (!empty($cartId)) {
            $check_query = "SELECT id, qty FROM stationery_cart
                            WHERE id = '{$cartId}'
                              AND company_id = '{$companyId}'
                              AND user_id = '{$userId}'
                              AND item_id = '{$itemId}'
                              AND request_no IS NULL
                              AND is_active = 0
                            LIMIT 1";
            $existing = $callConfig->getRow($check_query);
            if ($existing && $existing->id) {
                $new_qty = intval($existing->qty) + $qty;
                $this->updateStationeryCartQty($existing->id, $new_qty, $userId);
                return [true, $existing->id, $new_qty, null];
            }
            return [false, $cartId, 0, "Cart item not found with provided cart_id"];
        }

        $check_query = "SELECT id, qty FROM stationery_cart
                        WHERE company_id = '{$companyId}'
                          AND user_id = '{$userId}'
                          AND item_id = '{$itemId}'
                          AND request_no IS NULL
                          AND is_active = 0
                        LIMIT 1";
        $existing = $callConfig->getRow($check_query);
        if ($existing && $existing->id) {
            $new_qty = intval($existing->qty) + $qty;
            $this->updateStationeryCartQty($existing->id, $new_qty, $userId);
            return [true, $existing->id, $new_qty, null];
        }

        $insert_array = array(
            self::KEY_COMPANY_ID => $companyId,
            self::KEY_ITEM_ID => $itemId,
            self::KEY_USER_ID => $userId,
            'qty' => $qty,
            'request_no' => null,
            self::KEY_IS_ACTIVE => 0,
            self::KEY_CURRENT_STATUS => 0,
            self::KEY_CREATED_BY => $userId,
            self::KEY_CREATED_DATE => date(self::DATETIME_FORMAT_YMD_HIS)
        );
        $result = $callConfig->insertRecord('stationery_cart', $insert_array);
        return [true, $result[self::KEY_LASTID], $qty, null];
    }

    private function removeStationeryCartItem($companyId, $userId, $qty, $cartId){
        global $callConfig;
        if (empty($cartId)) {
            return [false, $cartId, 0, "cart_id is required for decrease operation"];
        }
        $check_query = "SELECT qty FROM stationery_cart
                        WHERE id = '{$cartId}'
                          AND company_id = '{$companyId}'
                          AND user_id = '{$userId}'
                          AND request_no IS NULL
                          AND is_active = 0
                        LIMIT 1";
        $existing = $callConfig->getRow($check_query);
        if (!$existing) {
            return [false, $cartId, 0, "Cart item not found"];
        }
        $new_qty = max(0, intval($existing->qty) - $qty);
        $this->updateStationeryCartQty($cartId, $new_qty, $userId);
        return [true, $cartId, $new_qty, null];
    }

    public function manageStationeryCart($get, $post) {
        global $callConfig;

        $company_id = $get[self::KEY_COMPANY_ID];
        $user_id = $get[self::KEY_USER_ID];
        $item_id = $post[self::KEY_ITEM_ID];
        $qty = intval($post['qty']);
        $cart_type = intval($post['cart_type']);
        $cart_id = isset($post['cart_id']) ? intval($post['cart_id']) : 0;

        if (empty($item_id) || $qty <= 0) {
            return array(self::KEY_STATUS => self::KEY_ERROR, "msg" => "Invalid item_id or quantity");
        }

        if ($cart_type == 1) {
            list($ok, $final_cart_id, $new_qty, $errMsg) = $this->addStationeryCartItem($company_id, $user_id, $item_id, $qty, $cart_id);
        } elseif ($cart_type == 0) {
            list($ok, $final_cart_id, $new_qty, $errMsg) = $this->removeStationeryCartItem($company_id, $user_id, $qty, $cart_id);
        } else {
            return array(self::KEY_STATUS => self::KEY_ERROR, "msg" => "Invalid cart_type. Use 1 for ADD, 0 for REMOVE");
        }

        if (!$ok) {
            return array(self::KEY_STATUS => self::KEY_ERROR, "msg" => $errMsg);
        }

        $count_query = "SELECT SUM(qty) as total_items
                        FROM stationery_cart
                        WHERE company_id = '{$company_id}'
                          AND user_id = '{$user_id}'
                          AND request_no IS NULL
                          AND is_active = 0";
        $count_result = $callConfig->getRow($count_query);
        $total_cart_items = $count_result ? intval($count_result->total_items) : 0;

        return array(
            self::KEY_STATUS => self::KEY_SUCCESS,
            "cart_id" => $final_cart_id,
            "new_qty" => $new_qty,
            "total_cart_items" => $total_cart_items,
            "msg" => "Cart updated successfully"
        );
    }

    /**
     * API 4: Get User's Cart Items
     * @param array $get - Must contain: company_id, user_id
     * @return array - Returns all cart items with item details
     */
    public function getUserStationeryCart($get) {
        global $callConfig;

        $company_id = $get[self::KEY_COMPANY_ID];
        $user_id = $get[self::KEY_USER_ID];

        // Fetch all cart items with full item details
        $query = "SELECT
                sc.id AS cart_id,
                sc.item_id AS id,
                sc.qty AS cart_qty,
                sc.created_date,
                si.item_name,
                si.description,
                si.image,
                si.available_qty,
                si.cost,
                si.category_id,
                cat.name AS category_name
            FROM stationery_cart sc
            INNER JOIN stationery_items si
                ON si.id = sc.item_id
                AND si.company_id = sc.company_id
                AND si.is_active = 0
            LEFT JOIN stationery_categories cat
                ON cat.id = si.category_id
                AND cat.company_id = si.company_id
                AND cat.is_active = 0
            WHERE sc.company_id = '{$company_id}'
              AND sc.user_id = '{$user_id}'
              AND sc.qty>0
              AND sc.request_no IS NULL
              AND sc.is_active = 0
            ORDER BY sc.created_date DESC";

        $cart_items = $callConfig->getAllRows($query);

        // Get total cart count
        $count_query = "SELECT
                        COUNT(*) as total_items,
                        SUM(qty) as total_qty
                    FROM stationery_cart
                    WHERE company_id = '{$company_id}'
                      AND user_id = '{$user_id}'
                      AND request_no IS NULL
                      AND is_active = 0";

        $count_result = $callConfig->getRow($count_query);

        return array(
            self::KEY_STATUS => self::KEY_SUCCESS,
            "cart_items" => $cart_items,
            "total_items" => $count_result ? intval($count_result->total_items) : 0,
            "total_qty" => $count_result ? intval($count_result->total_qty) : 0
        );
    }

    /**
     * API 5: Create Stationery Request from temporary cart
     * @param array $get - Must contain: company_id, user_id
     * @param array $post - Must contain: request_date, require_date, st_emp_id, request_comment,
     *                      deliver_type, location_id, st_current_status, and optional:
     *                      building_id, building_floor_id, building_floor_space_id, other_name
     * @return array - Returns request details and status
     */
    public function createStationeryRequest($get, $post) {
        global $callConfig;
        $return = array();

        $return['post'] = $post;
        $return['get'] = $get;

        // From GET: company_id and user_id
        $company_id = $get[self::KEY_COMPANY_ID];
        $user_id = $get[self::KEY_USER_ID];

        // From POST: request details
        $request_date = date(self::DATE_FORMAT_YMD);
        $require_date = $post['require_date'] ?? date(self::DATE_FORMAT_YMD);
        $emp_id = $post['st_emp_id'] ?? 0;
        $request_comment = $post['request_comment'] ?? '';


        /* ===== 1. Generate Request Serial Number ===== */
        $get[self::KEY_TRANSACTION_DATE] = date(self::DATE_FORMAT_YMD);
        $rnd = $this->toGetApplicationSerialNumber(self::TBL_STATIONERY_REQUESTS,$get);

        if (!$rnd) {
            return array(
                self::KEY_STATUS => self::KEY_ERROR,
                "msg" => "Failed to generate serial number"
            );
        }

        /* ===== 2. Create stationery_request record ===== */
        $request_array = array(
            self::KEY_COMPANY_ID => $company_id,
            'request_no' => $rnd,
            'request_date' => $request_date,
            'require_date' => $require_date,
            self::KEY_EMP_ID => $emp_id,
            'request_comment' => $request_comment,
            self::KEY_USER_ID => $user_id,
            self::KEY_IS_ACTIVE => 0,
            self::KEY_CURRENT_STATUS => 0,
            self::KEY_CREATED_BY => $user_id,
            self::KEY_CREATED_DATE => date(self::DATETIME_FORMAT_YMD_HIS)
        );

        $request_res = $callConfig->insertRecord(self::TBL_STATIONERY_REQUESTS, $request_array);

        if (!isset($request_res[self::KEY_LASTID])) {
            return array(
                self::KEY_STATUS => self::KEY_ERROR,
                "msg" => "Failed to create stationery request",
                "debug" => $request_res
            );
        }

        $request_id = $request_res[self::KEY_LASTID];

        /* ===== 3. Get all temporary cart items (request_no IS NULL) ===== */
        $cart_query = "SELECT id, item_id, qty, color_id, size_id, uniform_type_id
                       FROM stationery_cart
                       WHERE company_id = '{$company_id}'
                         AND user_id = '{$user_id}'
                         AND request_no IS NULL
                         AND is_active = 0
                       ORDER BY id ASC";

        $cart_items = $callConfig->getAllRows($cart_query);

        if (empty($cart_items)) {
            return array(
                self::KEY_STATUS => self::KEY_ERROR,
                "msg" => "No items in cart to for this user"
            );
        }

        $processed_items = array();

        foreach ($cart_items as $item) {
            $cart_item_id = $item->id;

            // UPDATE existing cart item with request_no and request_id
            $update_array = array(
                'request_no' => $rnd,
                'request_id' => $request_id,
                self::KEY_UPDATED_BY => $user_id,
                self::KEY_UPDATED_DATE => date(self::DATETIME_FORMAT_YMD_HIS)
            );

            $callConfig->updateRecord('stationery_cart', $update_array, 'id', $cart_item_id);
            $processed_items[] = array(
                'cart_id' => $cart_item_id,
                'action' => 'updated'
            );
        }
        return array(
            self::KEY_STATUS => self::KEY_SUCCESS,
            "msg" => "Stationery request created successfully",
            "request_id" => $request_id,
            "request_no" => $rnd,
            "processed_items" => $processed_items,
        );
    }

    // inventory added by koti end
    public function togetemployeeidentitycarddetails($get){
        global $callConfig;
        $query = "SELECT id, emp_code, display_name,`pf_no`, `pf_uan`, `esi_no`, `driving_licence`, `driving_licence_expiry_date`, `passport`, `passport_expiry_date`, `adhar_no`, `pan_number`, attached_files FROM employees WHERE id=".$get[self::KEY_EMP_ID];
        $data = $callConfig->getRow($query);
        $return['identity']= $data;
        return $return;
    }

    public function togetassetsdetails($get){
        global $callConfig;
        $return = array();
        $query = "SELECT id,category_id,name FROM asset_attributes WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0";
        $data = $callConfig->getAllRows($query);
        $return['attributes'] = $data;
        $attributes = array();
        foreach ((array)$data as $row) {
            $attributes[$row->category_id][] = $row;
        }

        $query = "SELECT * FROM asset_categories WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0";
        $data = $callConfig->getAllRows($query);

        $return['categories'] = $data;

        $categories = array();

        foreach ((array)$data as $row) {
            $categories[$row->id] = (array)$row;
            $categories[$row->id]['child'] = 'N';
            $categories[$row->id]['child_data'] = array();

            if (isset($attributes[$row->id]) && count((array)$attributes[$row->id]) > 0) {
                $categories[$row->id]['child'] = 'Y';
                $categories[$row->id]['child_data'] = $attributes[$row->id];
            }
        }
        $return['data'] = $categories;

        $query = "SELECT * FROM locations WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0";
        $data = $callConfig->getAllRows($query);

        $return['locations'] = $data;

        return $return;
    }

    /**
     * Insert or Edit asset_requests
     * If $get['rid'] exists, performs UPDATE; otherwise performs INSERT
     */
    public function insertAssetRequests($get, $post){
        global $callConfig;
        $return = array();



        // Create common array for both insert and update
        $insertary = array(
            self::KEY_COMPANY_ID => $get[self::KEY_COMPANY_ID] ?? '',
            'category_id' => $post['category_id'] ?? '',
            'originator' => $post['originator'] ?? '',
            'attribute' => $post['attribute'] ?? '',
            'asset_required_start_date' =>  date(self::DATE_FORMAT_YMD),
            'asset_required_end_date' => $post['asset_required_end_date'] ?? null,
            'location_id' => $post['location_id'] ?? '',
            'delivery_type' => $post['delivery_type'] ?? 'S',
            'delivery_address' => $post['delivery_address'] ?? '',
            'quantity' => $post['quantity'] ?? 0,
            'description' => $post['description'] ?? '',
            'request_datetime' => date(self::DATETIME_FORMAT_YMD_HIS),
            'approval_user_id' => 610, // This should ideally come from the workflow configuration

            self::KEY_IS_ACTIVE => 0
        );

        // Check if editing (rid exists)
        if (isset($get['rid']) && $get['rid'] != '') {
            // UPDATE mode
            $insertary[self::KEY_UPDATED_DATE] = date(self::DATETIME_FORMAT_YMD_HIS);
            $insertary[self::KEY_UPDATED_BY] = $get[self::KEY_USER_ID];

            $res = $callConfig->updateRecord(self::TBL_ASSET_REQUESTS, $insertary, 'id', $get['rid']);
            $return['res'] = $res;
            $return[self::KEY_STATUS] = self::KEY_SUCCESS;
            $return[self::KEY_MESSAGE] = 'Asset request updated successfully';
            $return['id'] = $get['rid'];
        } else {
            // INSERT mode

            $get[self::KEY_TRANSACTION_DATE] = date(self::DATE_FORMAT_YMD);
            $rnd = $this->toGetApplicationSerialNumber(self::TBL_ASSET_REQUESTS,$get);

            $insertary[self::KEY_TRANSACTION_NO] = $rnd;
            $insertary[self::KEY_CURRENT_STATUS] = 0;
            $insertary['status_date'] = date(self::DATETIME_FORMAT_YMD_HIS);
            $insertary[self::KEY_CREATED_DATE] = date(self::DATETIME_FORMAT_YMD_HIS);
            $insertary[self::KEY_CREATED_BY] = $get[self::KEY_USER_ID];

            $res = $callConfig->insertRecord(self::TBL_ASSET_REQUESTS, $insertary);
            $return['res'] = $res;
            $return[self::KEY_STATUS] = self::KEY_SUCCESS;
            $return[self::KEY_MESSAGE] = 'Asset request created successfully';
            $return['id'] = $res[self::KEY_LASTID] ?? '';
        }

        return $return;
    }

    public function togetAssetRequestDetails($get){

        global $callConfig;
        $return = array();
        $rid = $get['rid'] ?? '';
        $company_id = $get[self::KEY_COMPANY_ID] ?? '';

        if ($rid == '' || $company_id == '') {
            $return[self::KEY_STATUS] = self::KEY_ERROR;
            $return[self::KEY_MESSAGE] = 'Request ID and Company ID are required';
            return $return;
        }

        // Get asset request details with related data
        $query = "SELECT ar.*,
            ac.category_description AS category_name,
            loc.location_name,
            au.display_name AS originator_name,
            au.user_email AS originator_email,
            apu.display_name AS approval_name,
            apu.user_email AS approval_email
        FROM asset_requests ar
        LEFT JOIN asset_categories ac ON ac.id = ar.category_id AND ac.company_id = ar.company_id AND ac.is_active=0
        LEFT JOIN locations loc ON loc.id = ar.location_id AND loc.company_id = ar.company_id AND loc.is_active=0
        LEFT JOIN application_users au ON au.id = ar.originator AND au.company_id = ar.company_id AND au.is_active=0
        LEFT JOIN application_users apu ON apu.id = ar.approval_user_id AND apu.company_id = ar.company_id AND apu.is_active=0
        WHERE ar.company_id = {$company_id} AND ar.id = {$rid} AND ar.is_active = 0";

        $data = $callConfig->getRow($query);

        if (!$data) {
            $return[self::KEY_STATUS] = self::KEY_ERROR;
            $return[self::KEY_MESSAGE] = 'Asset request not found';
            return $return;
        }

        $return['data'] = $data;
        $return[self::KEY_STATUS] = self::KEY_SUCCESS;

        // Get attributes if present
        $attributes = array();
        $attribute_ids = array();
        if (!empty($data->attribute)) {
            $attribute_ids = array_filter(array_map('trim', explode(',', (string)$data->attribute)));
        }

        if (count($attribute_ids) > 0) {
            $attribute_ids = array_map('intval', $attribute_ids);
            $attr_ids = implode(',', $attribute_ids);
            $attr_query = "SELECT id, name FROM asset_attributes
                WHERE company_id = {$company_id} AND is_active = 0 AND id IN ({$attr_ids})";
            $attributes = $callConfig->getAllRows($attr_query);
        }

        $return['attributes'] = $attributes;

        return $return;
    }
    public function insertWorkFromHomeRequests($get, $post){
        global $callConfig;
        $return = array();

        $company_id = $get[self::KEY_COMPANY_ID] ?? '';
        $user_id = $get[self::KEY_USER_ID] ?? '';
        $emp_id = $post[self::KEY_EMP_ID] ?? '';
        $request_type = $post['request_type'] ?? '';

        if ($company_id == '') {
            $return[self::KEY_STATUS] = self::KEY_ERROR;
            $return[self::KEY_MESSAGE] = 'Company ID is required';
            return $return;
        }

        if ($emp_id == '' || $request_type == '') {
            $return[self::KEY_STATUS] = self::KEY_ERROR;
            $return[self::KEY_MESSAGE] = 'emp_id and request_type are required';
            return $return;
        }

        $insertary = array(
            self::KEY_COMPANY_ID => $company_id,
            self::KEY_EMP_ID => $emp_id,
            'request_type' => $request_type,
            'effective_from_date' => $post['effective_from_date'] ?? null,
            'effective_to_date' => $post['effective_to_date'] ?? null,
            'number_days' => $post['number_days'] ?? 0,
            self::KEY_SPECIAL_REQUEST => $post[self::KEY_SPECIAL_REQUEST] ?? '',
            'from_date' => $post['from_date'] ?? null,
            'to_date' => $post['to_date'] ?? null,
            self::KEY_IS_ACTIVE => 0
        );

        if (isset($get['rid']) && $get['rid'] != '') {
            $insertary[self::KEY_UPDATED_BY] = $user_id;
            $insertary[self::KEY_UPDATED_DATE] = date(self::DATETIME_FORMAT_YMD_HIS);

            $res = $callConfig->updateRecord('request_work_from_home', $insertary, 'id', $get['rid']);
            $return['res'] = $res;
            $return[self::KEY_STATUS] = self::KEY_SUCCESS;
            $return[self::KEY_MESSAGE] = 'Work from home request updated successfully';
            $return['id'] = $get['rid'];
        } else {
            $get[self::KEY_TRANSACTION_DATE] = date(self::DATE_FORMAT_YMD);
            $rnd = $this->toGetApplicationSerialNumber('request_work_from_home',$get);

            $insertary[self::KEY_TRANSACTION_NO] = $rnd;
            $insertary[self::KEY_CURRENT_STATUS] = 0;
            $insertary['status_date'] = date(self::DATETIME_FORMAT_YMD_HIS);
            $insertary[self::KEY_CREATED_DATE] = date(self::DATETIME_FORMAT_YMD_HIS);
            $insertary[self::KEY_CREATED_BY] = $user_id;

            $res = $callConfig->insertRecord('request_work_from_home', $insertary);
            $return['res'] = $res;
            $return[self::KEY_STATUS] = self::KEY_SUCCESS;
            $return[self::KEY_MESSAGE] = 'Work from home request created successfully';
            $return['id'] = $res[self::KEY_LASTID] ?? '';
        }

        return $return;
    }

    public function togetWorkFromHomeRequestDetails($get){
        global $callConfig;
        $return = array();
        $rid = $get['rid'] ?? '';
        $company_id = $get[self::KEY_COMPANY_ID] ?? '';

        if ($rid == '' || $company_id == '') {
            $return[self::KEY_STATUS] = self::KEY_ERROR;
            $return[self::KEY_MESSAGE] = 'Request ID and Company ID are required';
            return $return;
        }

        $query = "SELECT rwfh.*,
            emp.emp_code,
            emp.display_name AS emp_name
        FROM request_work_from_home rwfh
        LEFT JOIN employees emp ON emp.id = rwfh.emp_id AND emp.company_id = rwfh.company_id AND emp.is_active=0
        WHERE rwfh.company_id = {$company_id} AND rwfh.id = {$rid} AND rwfh.is_active = 0";

        $data = $callConfig->getRow($query);

        if (!$data) {
            $return[self::KEY_STATUS] = self::KEY_ERROR;
            $return[self::KEY_MESSAGE] = 'Work from home request not found';
            return $return;
        }

        $return['data'] = $data;
        $return[self::KEY_STATUS] = self::KEY_SUCCESS;

        return $return;
    }

    public function getUserMobileStatusDashboard($get) {
        global $callConfig;

        try {
            $company_id = $get[self::KEY_COMPANY_ID] ?? '';

            if (empty($company_id)) {
                return [
                    self::KEY_STATUS => self::KEY_ERROR,
                    self::KEY_MESSAGE => 'Company ID is required',
                    'data' => null
                ];
            }

            // Query 1: Get all active users from application_users
            $users_query = "SELECT
                    id,
                    display_name,
                    user_email,
                    user_mobile,
                    role_id,
                    user_activated,
                    current_status,
                    last_logged_in_at,
                    user_type
                FROM " . TBL_USERS . "
                WHERE company_id = '" . $company_id . "'
                AND user_activated = 0
                AND is_active = 0
                ORDER BY display_name ASC";

            $all_users = (array)$callConfig->getAllRows($users_query);

            // Query 2: Get unique user IDs who have logged in via mobile
            $mobile_users_query = "SELECT DISTINCT user_id
                FROM user_mobile_details
                WHERE company_id = '" . $company_id . "'
                AND (is_active = 0 OR is_active IS NULL)
                AND user_id IS NOT NULL";

            $mobile_users_rows = (array)$callConfig->getAllRows($mobile_users_query);

            // Extract mobile user IDs into a simple array
            $mobile_user_ids = array();
            if (!empty($mobile_users_rows)) {
                foreach ($mobile_users_rows as $row) {
                    $mobile_user_ids[] = (int)$row->user_id;
                }
            }

            // Calculate counts
            $total_users = count($all_users);
            $mobile_users_count = count($mobile_user_ids);
            $needs_mobile_count = $total_users - $mobile_users_count;

            // Format user array with incremental keys (1-indexed for frontend compatibility)
            $formatted_users = array();
            $i = 1;
            foreach ($all_users as $user) {
                $formatted_users[$i] = array(
                    'id' => $user->id,
                    'display_name' => $user->display_name,
                    self::KEY_USER_EMAIL => $user->user_email,
                    'user_mobile' => $user->user_mobile,
                    'role_id' => $user->role_id,
                    'user_activated' => $user->user_activated,
                    self::KEY_CURRENT_STATUS => $user->current_status,
                    'last_logged_in_at' => $user->last_logged_in_at,
                    'user_type' => $user->user_type
                );
                $i++;
            }

            return [
                self::KEY_STATUS => self::KEY_SUCCESS,
                'data' => [
                    'all_users' => $formatted_users,
                    'mobile_user_ids' => $mobile_user_ids,
                    'total_users' => $total_users,
                    'mobile_users_count' => $mobile_users_count,
                    'needs_mobile_count' => $needs_mobile_count
                ],
                self::KEY_MESSAGE => 'Data retrieved successfully'
            ];

        } catch (Exception $e) {
            return [
                self::KEY_STATUS => self::KEY_ERROR,
                self::KEY_MESSAGE => 'Error retrieving data: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function togetmobilemanagerdashboard($get){
         global $callConfig;
         $endate = date('Y')."-12-31";
        //new
        if($get['dash_date']!=""){
            $today_date = $get['dash_date'];
        }else{
            $today_date = date(self::DATE_FORMAT_YMD);
        }
         $division_id = 1;
        //Get Total Eployeees
         $sql ="SELECT id, display_name, email_id, employee_type FROM `employees` emp WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND is_active = 0 AND division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END )";

         $empdatas = $callConfig->getAllRows($sql);
         $allemplist = array();
         $data[self::KEY_TODAY_ATTENDANTS]['total_emp'] =count((array)$empdatas);
         foreach($empdatas as $empdatas1){
             $data[self::KEY_TODAY_ATTENDANTS]['total_emp_ids'][] =$empdatas1;
             $allemplist[$empdatas1->id] = $empdatas1;
             if($empdatas1->employee_type == '1'){
                 $data[self::KEY_TODAY_ATTENDANTS]['regular_emp_ids'][] =$empdatas1;
             }elseif($empdatas1->employee_type == '2'){
                 $data[self::KEY_TODAY_ATTENDANTS]['contract_emp_ids'][] =$empdatas1;
             }

        }
        //Get Today Attendance Eployeees
         $attendancequery = "SELECT emp.id, emp.display_name,emp.email_id from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND emp.division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$today_date."' AND eda.is_active = emp.is_active group by emp.id";
         $attendata = $callConfig->getAllRows($attendancequery);
         $data[self::KEY_TODAY_ATTENDANTS][self::KEY_PRESENT] =count((array)$attendata);
         $data[self::KEY_TODAY_ATTENDANTS]['present_ids'] = array();
         foreach($attendata as $empdatas2){
             $data[self::KEY_TODAY_ATTENDANTS]['present_ids'][] =$empdatas2;
             unset($allemplist[$empdatas2->id]);
        }
        foreach($allemplist as $adsaaa){
            $onads[] =  $adsaaa;
        }
         $data['totalpressent'] = count((array)$attendata);
         $data[self::KEY_TODAY_ATTENDANTS]['absent_ids'] = $onads;
        //Get Today Planed Leaves Eployeees
         $leaves = "SELECT emp.id, emp.display_name,emp.email_id FROM `employees` emp, employee_leaves el WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND emp.division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') )ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$today_date."' AND el.leave_to >= '".$today_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";

         $leavesdata = $callConfig->getAllRows($leaves);
         $data[self::KEY_TODAY_ATTENDANTS]['planned_leave_ids'] = array();
         foreach($leavesdata as $empdatas3){
             $data[self::KEY_TODAY_ATTENDANTS]['planned_leave_ids'][] =$empdatas3;
        }
         $empcnt = count((array)$leavesdata)!="0"?count((array)$leavesdata):0;
         $attendataempcnt =count((array)$attendata)!="0"?count((array)$attendata):0;
         $data[self::KEY_TODAY_ATTENDANTS]['planned_leave'] =$empcnt;
         $data[self::KEY_TODAY_ATTENDANTS]['absent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
         $data['totalabsent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
         // Transport gateway
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway_ids'] = array();
         $query = "SELECT tg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type FROM transport_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]."  AND tg.entry_date ='".$today_date."' AND tg.is_active = 0 AND emp.division_id='{$division_id}'";
         $transport_getway_data = $callConfig->getAllRows($query);
         foreach($transport_getway_data as $transport_getway){
             $data[self::KEY_TODAY_ATTENDANTS]['transport_getway_ids'][] =$transport_getway;
        }
         $transport_getway_cnt = count((array)$transport_getway_data)!="0"?count((array)$transport_getway_data):0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway'] =$transport_getway_cnt;

         // Transport Personal  gateway
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_TRANSPORT_PERSONAL_GATEWAY] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_personal_gateway_ids'] = array();
         $query = "SELECT tg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type FROM transport_personal_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]."  AND tg.entry_date ='".$today_date."' AND tg.is_active = 0 AND emp.division_id='{$division_id}'";
         $transport_personal_gateway = $callConfig->getAllRows($query);
         foreach($transport_personal_gateway as $transport_getway){
             $data[self::KEY_TODAY_ATTENDANTS]['transport_personal_gateway_ids'][] =$transport_getway;
        }
         $transport_personal_gateway_cnt = count((array)$transport_personal_gateway)!="0"?count((array)$transport_personal_gateway):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_TRANSPORT_PERSONAL_GATEWAY] =$transport_personal_gateway_cnt;

         // Leave
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_EMPLOYEE_LEAVES] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['employee_leaves_ids'] = array();
         $query = "SELECT tg.id, emp.id, emp.emp_code, emp.display_name, emp.email_id FROM employee_leaves tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]." AND tg.leave_from ='".$today_date."' AND tg.is_active = 0 AND emp.division_id='{$division_id}'";
         $employee_leaves_data = $callConfig->getAllRows($query);
         foreach($employee_leaves_data as $employee_leave){
             $data[self::KEY_TODAY_ATTENDANTS]['employee_leaves_ids'][] =$employee_leave;
        }
         $transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_EMPLOYEE_LEAVES] =$transport_personal_gateway_cnt;
         // stationery
         $data[self::KEY_TODAY_ATTENDANTS]['stationery'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['stationery_ids'] = array();

         $query = "SELECT sr.id stationery_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other FROM stationery_requests sr, employees emp WHERE emp.id = sr.emp_id AND sr.company_id = ".$get[self::KEY_COMPANY_ID]."  AND sr.request_date ='".$today_date."' AND sr.is_active = 0 AND emp.division_id='{$division_id}'";
         $stationery_requests = $callConfig->getAllRows($query);
         foreach($stationery_requests as $row){
             $data[self::KEY_TODAY_ATTENDANTS]['stationery_ids'][] =$row;
        }
         $stationery_requests_cnt = count((array)$stationery_requests)!="0"?count((array)$stationery_requests):0;
         $data[self::KEY_TODAY_ATTENDANTS]['stationery'] = $stationery_requests_cnt;

         // assets
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_ASSETS] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['assets_ids'] = array();

         $query = "SELECT ar.id asset_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other FROM asset_requests ar, employees emp WHERE emp.user_id = ar.originator AND ar.company_id = ".$get[self::KEY_COMPANY_ID]."  AND ar.asset_required_start_date ='".$today_date."' AND ar.is_active = 0 AND emp.division_id='{$division_id}'";
         $asset_requests = $callConfig->getAllRows($query);
         foreach($asset_requests as $row){
             $data[self::KEY_TODAY_ATTENDANTS]['assets_ids'][] =$row;
        }
         $asset_requests_cnt = count((array)$asset_requests)!="0"?count((array)$asset_requests):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_ASSETS] = $asset_requests_cnt;


         // attenmiss punch
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch_ids'] = array();
         $query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.email_id FROM employee_daily_attendances tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]." AND tg.attendace_date ='".$today_date."' AND `attendance_key_short_code` IN ('Z','MP') AND tg.is_active = 0 AND emp.division_id='{$division_id}'";
         $employee_miss_data = $callConfig->getAllRows($query);
         foreach($employee_miss_data as $employee_leave){
             $data[self::KEY_TODAY_ATTENDANTS]['miss_punch_ids'][] =$employee_leave;
        }
         $miss_cnt = count((array)$employee_miss_data)!="0"?count((array)$employee_miss_data):0;
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch'] =$miss_cnt;


        //Get regular,contact,male,female,manger_count,employees Count Eployeees
         $query="SELECT SUM(if(emp.employee_type='1',1,0))as regular, SUM(if(emp.employee_type='2',1,0))as contact, COUNT(emp.id) as em_count FROM employees emp WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND emp.division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END )";
         $nempdata = $callConfig->getRow($query);
         $data['nemp'] = $nempdata;

        //Get Joing Employees
         $mequery= "SELECT count(emp.id) as joinn FROM employees emp WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.division_id='{$division_id}' AND ( CASE WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID]."') OR (emp.hr_buddy) = '".$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) AND emp.joining_date >= '".$thismonthstartdate."' AND emp.joining_date <= '".$thismonthenddate."' AND is_active=0";
         $medata = $callConfig->getRow($mequery);
         $data['joining'] = $medata->joinn;

        //Get leaving Employees
         $mequery= "SELECT count(emp.id) as realving FROM employees emp WHERE emp.date_of_leaving >= '".$thismonthstartdate."' AND emp.date_of_leaving <= '".$thismonthenddate."' AND emp.date_of_leaving IS NOT NULL AND emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.division_id='{$division_id}' AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END ) AND  is_active=0";
         $medata1 = $callConfig->getRow($mequery);
         $data['relive'] = $medata1->realving;

         $query="SELECT SUM(if(emp.gender='M',1,0)) as male, SUM(if(emp.gender='F',1,0))as female, count(DISTINCT(emp.reporting_manager_id)) as manger_count, COUNT(emp.id) as em_count FROM employees emp WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 AND emp.division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END) ";
         //AND emp.joining_date >= '".$thismonthstartdate."' AND emp.joining_date <= '".$thismonthenddate."'
         $nempdata = $callConfig->getRow($query);
         $data['daterangenemp'] = $nempdata;
         $sqqlgraph = "SELECT sum(m12) as j12,
         sum(m11) as j11,
         sum(m10) as j10,
         sum(m9) as j9,
         sum(m8) as j8,
         sum(m7) as j7,
         sum(m6) as j6,
         sum(m5) as j5,
         sum(m4) as j4,
         sum(m3) as j3,
         sum(m2) as j2,
         sum(m1) as j1,
         sum(lm12) as r12,
         sum(lm11) as r11,
         sum(lm10) as r10,
         sum(lm9) as r9,
         sum(lm8) as r8,
         sum(lm7) as r7,
         sum(lm6) as r6,
         sum(lm5) as r5,
         sum(lm4) as r4,
         sum(lm3) as r3,
         sum(lm2) as r2,
         sum(lm1) as r1
         FROM    (
         SELECT    DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 12 MONTH), '%Y-%m' )curdt,
         DATE_FORMAT(emp.joining_date, '%Y-%m')dojm,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' )
         THEN 1
         END M12,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' )
         THEN 1
         END M11,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' )
         THEN 1
         END M10,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' )
         THEN 1
         END M9,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' )
         THEN 1
         END M8,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' )
         THEN 1
         END M7,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' )
         THEN 1
         END M6,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' )
         THEN 1
         END M5,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' )
         THEN 1
         END M4,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' )
         THEN 1
         END M3,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' )
         THEN 1
         END M2,
         CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' )
         THEN 1
         END M1,
         DATE_FORMAT(emp.joining_date, '%Y-%m') monyr,
         DATE_FORMAT(emp.date_of_leaving, '%Y-%m')dolm,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' )
         THEN 1
         END LM12,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' )
         THEN 1
         END LM11,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' )
         THEN 1
         END LM10,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' )
         THEN 1
         END LM9,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' )
         THEN 1
         END LM8,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' )
         THEN 1
         END LM7,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' )
         THEN 1
         END LM6,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' )
         THEN 1
         END LM5,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' )
         THEN 1
         END LM4,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' )
         THEN 1
         END LM3,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' )
         THEN 1
         END LM2,
         CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' )
         THEN 1
         END LM1,
         DATE_FORMAT(emp.date_of_leaving, '%Y-%m') lmonyrl
         FROM     employees emp
         WHERE    emp.company_id = 1 AND emp.division_id='{$division_id}'
         AND       (
         (emp.joining_date >= '".$thisyearfirstmonthlastdate."' AND emp.joining_date <= '".$endate."')
         OR (emp.date_of_leaving >= '".$thisyearfirstmonthlastdate."' AND emp.date_of_leaving <= '".$endate."')
         )
         AND emp.is_active = 0 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') ) ELSE( emp.vendor_id = (SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."'))END))a";

         $sqqlgraphres = $callConfig->getRow($sqqlgraph);
         foreach($sqqlgraphres as $sqkey=>$sqq){
             $passsqqlgraphres[]=array('id'=>$sqkey,self::KEY_VALUE=>$sqq);
         }
         $data['graphjoinandleave'] = $passsqqlgraphres;
         $salarygraph ="SELECT sum(m12) as j12, sum(m11) as j11, sum(m10) as j10, sum(m9) as j9, sum(m8) as j8, sum(m7) as j7, sum(m6) as j6, sum(m5) as j5, sum(m4) as j4, sum(m3) as j3, sum(m2) as j2, sum(m1) as j1 FROM ( SELECT DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 12 MONTH), '%Y-%m' )curdt, DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m')dojm, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M12, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M11, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M10, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M9, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M8, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M7, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M6, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M5, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M4, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M3, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M2, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M1, DATE_FORMAT(emp.joining_date, '%Y-%m') monyr FROM employees emp, pay_run_summary prs, pay_run_summary_components prsc WHERE emp.company_id = 1 AND emp.id = prs.employee_id AND emp.company_id = prs.company_id AND prsc.company_id = prs.company_id AND prsc.pay_run_summary_id = prs.id AND prsc.employee_id = emp.id AND prsc.component_id = 1 AND emp.division_id='{$division_id}' AND (( DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') >= '".$thisyearfirstmonth."') AND ( DATE_FORMAT(CONCAT(prs.pay_year, '-',prs.pay_month, '-','01'), '%Y-%m') <= '".$thisyearfirstmonth."')) AND prsc.is_active = 0 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') )ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) ) a";
         $salarygraphres = $callConfig->getRow($salarygraph);
         foreach($salarygraphres as $sqkey=>$sqq){
             $passsalarygraphres[]=array('id'=>$sqkey,self::KEY_VALUE=>$sqq);
         }
         $data['salarygraphres'] = $passsalarygraphres;
         return $data;
    }
    public function backviewtogetmobilemanagerdashboard($get){
         global $callConfig;
         $endate = date('Y')."-12-31";
        //new
         if($get['dash_date']!=""){
            $today_date = $get['dash_date'];
        }else{
            $today_date = date(self::DATE_FORMAT_YMD);
        }
        // yesterday
        $yesterday = date(self::DATE_FORMAT_YMD, strtotime($today_date . " -1 day"));

         $query = "SELECT division_id FROM employees WHERE (id='{$get[self::KEY_EMP_ID]}' OR user_id='{$get[self::KEY_USER_ID]}') AND company_id='{$get[self::KEY_COMPANY_ID]}' AND is_active=0";

         $division_data = $callConfig->getRow($query);
         $division_whr = "";
         $division_id = 0;
         if($division_data->division_id!=''){
             $division_id = $division_data->division_id;
             $division_whr = " AND emp.division_id=".$division_data->division_id;
         }
        //Get Total Eployeees
         $sql ="SELECT emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image,  emp.reporting_manager_id FROM `employees` emp WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END )";

         $empdatas = $callConfig->getAllRows($sql);
         $allemplist = array();
         $data[self::KEY_TODAY_ATTENDANTS]['total_emp'] =count((array)$empdatas);
         $male_count = 0;
        $female_count = 0;
        $manager_ids = array();
         foreach($empdatas as $empdatas1){
             $data[self::KEY_TODAY_ATTENDANTS]['total_emp_ids'][] =$empdatas1;
             $allemplist[$empdatas1->id] = $empdatas1;
             if($empdatas1->employee_type == '1'){
                 $data[self::KEY_TODAY_ATTENDANTS]['regular_emp_ids'][] =$empdatas1;
             }elseif($empdatas1->employee_type == '2'){
                 $data[self::KEY_TODAY_ATTENDANTS]['contract_emp_ids'][] =$empdatas1;
             }

             if($empdatas1->gender == 'M'){
                $male_count++;
            }elseif($empdatas1->gender == 'F'){
                $female_count++;
            }
            if(!empty($empdatas1->reporting_manager_id)){
                $manager_ids[$empdatas1->reporting_manager_id] = 1; // unique
            }
        }
        $total_emp_count = count((array)$empdatas);
        $manager_count = count((array)$manager_ids);
        $employee_count = $total_emp_count - $manager_count;

        if($total_emp_count > 0){
            $data[self::KEY_TODAY_ATTENDANTS]['manager_percentage'] = round(($manager_count / $total_emp_count) * 100, 2);
            $data[self::KEY_TODAY_ATTENDANTS]['employee_percentage'] = round(($employee_count / $total_emp_count) * 100, 2);
        }else{
            $data[self::KEY_TODAY_ATTENDANTS]['manager_percentage'] = 0;
            $data[self::KEY_TODAY_ATTENDANTS]['employee_percentage'] = 0;
        }

        $data[self::KEY_TODAY_ATTENDANTS]['male_count'] = $male_count;
        $data[self::KEY_TODAY_ATTENDANTS]['female_count'] = $female_count;

        if($total_emp_count > 0){
            $data[self::KEY_TODAY_ATTENDANTS]['male_percentage'] = round(($male_count / $total_emp_count) * 100, 2);
            $data[self::KEY_TODAY_ATTENDANTS]['female_percentage'] = round(($female_count / $total_emp_count) * 100, 2);
        }else{
            $data[self::KEY_TODAY_ATTENDANTS]['male_percentage'] = 0;
            $data[self::KEY_TODAY_ATTENDANTS]['female_percentage'] = 0;
        }
        //Get Today Attendance Eployeees
         $attendancequery = "SELECT emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, '' as other from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$today_date."' AND eda.is_active = emp.is_active group by emp.id";
         $attendata = $callConfig->getAllRows($attendancequery);
         $today_count = count((array)$attendata);
         $data[self::KEY_TODAY_ATTENDANTS][self::KEY_PRESENT] =$today_count;
         $data[self::KEY_TODAY_ATTENDANTS]['present_ids'] = array();
         foreach($attendata as $empdatas2){
             $data[self::KEY_TODAY_ATTENDANTS]['present_ids'][] =$empdatas2;
             unset($allemplist[$empdatas2->id]);
        }
        foreach($allemplist as $adsaaa){
            $onads[] =  $adsaaa;
        }
         $data['totalpressent'] = count((array)$attendata);
         $data[self::KEY_TODAY_ATTENDANTS]['absent_ids'] = $onads;

         //Get Yesterday Attendance Eployeees
         $query = "SELECT emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, '' as other from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID].self::SQL_EMP_VENDOR_EMAIL.$get[self::KEY_USER_EMAIL]."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$yesterday."' AND eda.is_active = emp.is_active group by emp.id";
         $result = $callConfig->getAllRows($query);
         $yesterday_count = count((array)$result);
         $data['yesterdayattendants'][self::KEY_PRESENT] =$yesterday_count;
        if($yesterday_count > 0){
            $percent_change = (($today_count - $yesterday_count) / $yesterday_count) * 100;
        } else {
            $percent_change = ($today_count > 0) ? 100 : 0; // handle divide by zero
        }

        $data['attendance_change_percent'] = round($percent_change, 2);

        //Get Today Planed Leaves Eployeees
         $prsday = date(self::DATE_FORMAT_YMD, strtotime(self::STR_PLUS_1_DAY, strtotime($today_date)));
         $leaves = "SELECT el.id AS leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other FROM `employees` emp, employee_leaves el WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate.self::SQL_EMP_ACTIVE_CASE.$get[self::KEY_ADMIN_TYPE].self::SQL_OR_REPORTING_MANAGER.$get[self::KEY_USER_ID].self::SQL_OR_HR_BUDDY.$get[self::KEY_EMP_ID].self::SQL_OR_FINANCE_BUDDY.$get[self::KEY_EMP_ID]."') )ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$prsday."' AND el.leave_to >= '".$today_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";

         $leavesdata = $callConfig->getAllRows($leaves);
         $data[self::KEY_TODAY_ATTENDANTS]['planned_leave_ids'] = array();
         foreach($leavesdata as $empdatas3){
             $empdatas3->type = self::TBL_EMPLOYEE_LEAVES;
             $data[self::KEY_TODAY_ATTENDANTS]['planned_leave_ids'][] =$empdatas3;
        }
         $empcnt = count((array)$leavesdata)!="0"?count((array)$leavesdata):0;
         $attendataempcnt =count((array)$attendata)!="0"?count((array)$attendata):0;
         $data[self::KEY_TODAY_ATTENDANTS]['planned_leave'] =$empcnt;
         $data[self::KEY_TODAY_ATTENDANTS]['absent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
         $data['totalabsent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);

        $pending_leave_query = "SELECT el.id AS emp_leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other, el.application_date FROM employees emp, employee_leaves el  WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1 ELSE 2 END ) = 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get[self::KEY_ADMIN_TYPE]."')  OR (emp.reporting_manager_id = '".$get[self::KEY_USER_ID]."')  OR (emp.hr_buddy = '".$get[self::KEY_EMP_ID]."')  OR (emp.finance_buddy = '".$get[self::KEY_EMP_ID]."') ) ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND el.current_status < '".ST_APPROVED."' AND el.is_active = 0";

        $pending_leave_data = $callConfig->getAllRows($pending_leave_query);
        $data[self::KEY_TODAY_ATTENDANTS]['pending_leave_ids'] = array();
        foreach($pending_leave_data as $pending_emp){
            $pending_emp->type = self::TBL_EMPLOYEE_LEAVES;
            $data[self::KEY_TODAY_ATTENDANTS]['pending_leave_ids'][] = $pending_emp;
        }
        $data[self::KEY_TODAY_ATTENDANTS]['pending_leave_count'] = count((array)$pending_leave_data);

        $total_emp_count = count((array)$empdatas);
        $data['last_7_days'] = array();

        for($i = 1; $i <= 7; $i++){
            $trend_date = date(self::DATE_FORMAT_YMD, strtotime($today_date . " -".$i." day"));
            $trend_key  = "day".$i;
            // Present count
            $attendancequery = "SELECT emp.id FROM employees emp, employee_daily_attendances eda
                WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr
                AND (CASE
                        WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1
                        WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1
                        ELSE 2
                    END
                ) = 1
                AND (CASE
                        WHEN '3' <> '1' THEN (
                            ('1' = '".$get[self::KEY_ADMIN_TYPE]."')
                            OR (emp.reporting_manager_id = '".$get[self::KEY_USER_ID]."')
                            OR (emp.hr_buddy = '".$get[self::KEY_EMP_ID]."')
                            OR (emp.finance_buddy = '".$get[self::KEY_EMP_ID]."')
                        )ELSE (
                            emp.vendor_id = (
                                SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."'
                            )) END
                ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$trend_date."' AND eda.is_active = emp.is_active GROUP BY emp.id";

            $attendance_data = $callConfig->getAllRows($attendancequery);
            $present_count = count((array)$attendance_data);

            // On leave count for that date
            $next_day = date(self::DATE_FORMAT_YMD, strtotime($trend_date . ' +1 day'));
            $leavequery = "SELECT emp.id FROM employees emp, employee_leaves el
                WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 $division_whr
                AND (CASE
                        WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1
                        WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1
                        ELSE 2
                    END
                ) = 1
                AND (CASE
                        WHEN '3' <> '1' THEN (
                            ('1' = '".$get[self::KEY_ADMIN_TYPE]."')
                            OR (emp.reporting_manager_id = '".$get[self::KEY_USER_ID]."')
                            OR (emp.hr_buddy = '".$get[self::KEY_EMP_ID]."')
                            OR (emp.finance_buddy = '".$get[self::KEY_EMP_ID]."')
                        )
                        ELSE (
                            emp.vendor_id = (
                                SELECT id FROM vendors ven2 WHERE ven2.email = '".$get[self::KEY_USER_EMAIL]."'
                            )) END
                ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$next_day."' AND el.leave_to >= '".$trend_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";

            $leave_data = $callConfig->getAllRows($leavequery);
            $leave_count = count((array)$leave_data);

            $absent_count = $total_emp_count - ($present_count + $leave_count);
            if($absent_count < 0){
                $absent_count = 0;
            }

            $data['last_7_days'][] = array(
                'key'     => $trend_key,
                self::KEY_VALUE   => $trend_date,
                'total'   => $total_emp_count,
                self::KEY_PRESENT => $present_count,
                'absent'  => $absent_count,
                'on_leave'=> $leave_count
            );
        }

        $resigned_employees_query = "SELECT emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image, emp.reporting_manager_id, et.employee_leaving_date, COUNT(ea.id) AS asset_count
        FROM employees emp
        INNER JOIN employee_terminations et ON et.emp_id = emp.id AND et.company_id = emp.company_id AND et.is_active = 0
        LEFT JOIN employee_assets ea ON ea.emp_id = emp.id AND ea.company_id = emp.company_id AND ea.is_active = 0
        WHERE emp.company_id = '".$get[self::KEY_COMPANY_ID]."' AND emp.is_active = 0 GROUP BY emp.id,emp.display_name,emp.email_id,emp.gender,emp.employee_type,emp.emp_code,emp.phone,emp.image,emp.reporting_manager_id";

        $resigned_employees = $callConfig->getAllRows($resigned_employees_query);
        $data['resigned_employees'] = $resigned_employees;
        $data['resigned_employees_count'] = count((array)$resigned_employees);

        $is_holiday = 'N';
        $holiday_year = date('Y', strtotime($today_date));
        $holiday_query = "SELECT id FROM holidays  WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND holiday_year = '".$holiday_year."' AND holiday_date = '".$today_date.self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $holiday_result = $callConfig->getAllRows($holiday_query);
        if(count((array)$holiday_result)>0){
            $is_holiday = 'Y';
        }
        $data['is_holiday'] = $is_holiday;

         // Transport gateway
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway_ids'] = array();
         $query = "SELECT tpg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id, emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tpg.visit_purpose) as other, GROUP_CONCAT(emp1.display_name) as emp_names FROM transport_gateway tpg
            LEFT JOIN employees emp ON (emp.id = tpg.driver_name OR emp.id=tpg.user_id) AND emp.company_id = tpg.company_id AND emp.is_active=0 $division_whr
            LEFT JOIN employees emp1 ON FIND_IN_SET(emp1.id, tpg.emp_ids) AND emp1.company_id = tpg.company_id AND emp1.division_id = '{$division_id}' AND emp1.is_active = 0
            WHERE  tpg.company_id={$get[self::KEY_COMPANY_ID]} AND tpg.is_active=0 AND ( tpg.entry_date ='".$today_date."' OR tpg.exit_date ='".$today_date."' ) AND tpg.is_active = 0  GROUP BY tpg.id";
         $transport_getway_data = $callConfig->getAllRows($query);
         foreach($transport_getway_data as $transport_getway){
            $transport_getway->type = self::TBL_TRANSPORT_GATEWAY;
             $data[self::KEY_TODAY_ATTENDANTS]['transport_getway_ids'][] =$transport_getway;
        }
         $transport_getway_cnt = count((array)$transport_getway_data)!="0"?count((array)$transport_getway_data):0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_getway'] =$transport_getway_cnt;

         // Transport Personal  gateway
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_TRANSPORT_PERSONAL_GATEWAY] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['transport_personal_gateway_ids'] = array();
         $query = "SELECT tg.id transport_personal_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id, emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tg.visit_purpose) as other FROM transport_personal_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]."  AND (tg.entry_date ='".$today_date."'  OR tg.exit_date ='".$today_date."') AND tg.is_active = 0 $division_whr";
         $transport_personal_gateway = $callConfig->getAllRows($query);
         foreach($transport_personal_gateway as $transport_getway){
             $transport_getway->type = self::TBL_TRANSPORT_PERSONAL_GATEWAY;
             $data[self::KEY_TODAY_ATTENDANTS]['transport_personal_gateway_ids'][] =$transport_getway;
        }
         $transport_personal_gateway_cnt = count((array)$transport_personal_gateway)!="0"?count((array)$transport_personal_gateway):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_TRANSPORT_PERSONAL_GATEWAY] =$transport_personal_gateway_cnt;

         // Leave
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_EMPLOYEE_LEAVES] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['employee_leaves_ids'] = array();
         $query = "SELECT tg.id as employee_leave_id, emp.id, emp.emp_code, emp.display_name, emp.email_id,  CONCAT(tg.leave_from, ' - ', tg.leave_to) as other, emp.phone, emp.image, emp.reporting_manager_id FROM employee_leaves tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]." AND tg.leave_from ='".$today_date."' AND tg.is_active = 0 $division_whr";
         $employee_leaves_data = $callConfig->getAllRows($query);
         foreach($employee_leaves_data as $employee_leave){
             $employee_leave->type = self::TBL_EMPLOYEE_LEAVES;
             $data[self::KEY_TODAY_ATTENDANTS]['employee_leaves_ids'][] =$employee_leave;
        }
         $transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_EMPLOYEE_LEAVES] =$transport_personal_gateway_cnt;
        // stationery
         $data[self::KEY_TODAY_ATTENDANTS]['stationery'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['stationery_ids'] = array();

         $query = "SELECT sr.id stationery_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other, emp.phone, emp.image, emp.reporting_manager_id FROM stationery_requests sr, employees emp WHERE emp.id = sr.emp_id AND sr.company_id = ".$get[self::KEY_COMPANY_ID]."  AND sr.request_date ='".$today_date."' AND sr.is_active = 0 $division_whr";
         $stationery_requests = $callConfig->getAllRows($query);
         foreach($stationery_requests as $row){
             $row->type = self::TBL_STATIONERY_REQUESTS;
             $data[self::KEY_TODAY_ATTENDANTS]['stationery_ids'][] =$row;
        }
         $stationery_requests_cnt = count((array)$stationery_requests)!="0"?count((array)$stationery_requests):0;
         $data[self::KEY_TODAY_ATTENDANTS]['stationery'] = $stationery_requests_cnt;

         // assets
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_ASSETS] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['assets_ids'] = array();

         $query = "SELECT ar.id asset_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other, emp.phone, emp.image, emp.reporting_manager_id FROM asset_requests ar, employees emp WHERE emp.user_id = ar.originator AND ar.company_id = ".$get[self::KEY_COMPANY_ID]."  AND ar.asset_required_start_date ='".$today_date."' AND ar.is_active = 0 $division_whr";
         $asset_requests = $callConfig->getAllRows($query);
         foreach($asset_requests as $row){
             $row->type = self::TBL_ASSET_REQUESTS;
             $data[self::KEY_TODAY_ATTENDANTS]['assets_ids'][] =$row;
        }
         $asset_requests_cnt = count((array)$asset_requests)!="0"?count((array)$asset_requests):0;
         $data[self::KEY_TODAY_ATTENDANTS][self::TBL_ASSETS] = $asset_requests_cnt;

         // attenmiss punch
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch'] = 0;
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch_ids'] = array();
         $query = "SELECT tg.id as daily_attendances_id, emp.id, emp.emp_code, emp.display_name, emp.email_id,CONCAT('Clock : ', tg.clock_in, ' - ' , tg.clock_out ) as other, emp.phone, emp.image, emp.reporting_manager_id FROM employee_daily_attendances tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get[self::KEY_COMPANY_ID]." AND tg.attendace_date ='".$today_date."' AND `attendance_key_short_code` IN ('Z','MP') AND tg.is_active = 0 $division_whr";
         $employee_miss_data = $callConfig->getAllRows($query);
         foreach($employee_miss_data as $employee_leave){
             $employee_leave->type = self::TBL_EMPLOYEE_DAILY_ATTENDANCES;
             $data[self::KEY_TODAY_ATTENDANTS]['miss_punch_ids'][] =$employee_leave;
        }
         $miss_cnt = count((array)$employee_miss_data)!="0"?count((array)$employee_miss_data):0;
         $data[self::KEY_TODAY_ATTENDANTS]['miss_punch'] =$miss_cnt;


         $approvals = array();

        $tables_array = array(self::TBL_TRANSPORT_GATEWAY,self::TBL_TRANSPORT_PERSONAL_GATEWAY,self::TBL_EMPLOYEE_LEAVES,self::TBL_EMPLOYEE_DAILY_ATTENDANCES,self::TBL_ASSET_REQUESTS,self::TBL_STATIONERY_REQUESTS);

        // make IN clause properly
        $table_list = "'" . implode("','", $tables_array) . "'";

        $query = "SELECT
            tal.id AS transaction_approval_log_id,
            tal.transaction_table AS transaction_table,
            tal.transaction_id AS rid,
            COALESCE(emp1.display_name, '-') AS requested,
            COALESCE(emp2.display_name, '-') AS pending_approval,
            tal.previous_status AS current_status
          FROM transaction_approval_logs tal
          LEFT JOIN employees emp1
                 ON tal.assigner_user_id = emp1.user_id
                AND emp1.company_id = tal.company_id
                AND emp1.is_active = 0
          LEFT JOIN employees emp2
                 ON tal.user_id = emp2.user_id
                AND emp2.company_id = tal.company_id
                AND emp2.is_active = 0
          WHERE tal.response_type = 0
            AND tal.user_id = '{$get[self::KEY_USER_ID]}'
            AND tal.company_id = '{$get[self::KEY_COMPANY_ID]}'
            AND tal.response_date IS NULL
            AND tal.transaction_table IN ($table_list)";

        $approval_data = $callConfig->getAllRows($query);
        // label mapping
        $labels = array(
            self::TBL_TRANSPORT_GATEWAY => 'Transport Gateway',
            self::TBL_TRANSPORT_PERSONAL_GATEWAY => 'Transport Personal Gateway',
            self::TBL_EMPLOYEE_LEAVES => 'Employee Leaves',
            self::TBL_EMPLOYEE_DAILY_ATTENDANCES => 'Employee Daily Attendances',
            self::TBL_ASSET_REQUESTS => 'Asset Requests',
            self::TBL_STATIONERY_REQUESTS => 'Stationery Requests'
        );
        $types = array(
            self::TBL_TRANSPORT_GATEWAY => 'vehicle_gate_pass_info_by_id',
            self::TBL_TRANSPORT_PERSONAL_GATEWAY => 'transport_personal_gateway_info_by_id',
            self::TBL_EMPLOYEE_LEAVES => 'employee_leaves_info_by_id',
            self::TBL_EMPLOYEE_DAILY_ATTENDANCES => 'attendance_regularization_request_info_by_id',
            self::TBL_ASSET_REQUESTS => 'asset_request_info_by_id',
            self::TBL_STATIONERY_REQUESTS => 'stationery_request_info_by_id'
        );

        foreach ($approval_data as $value) {
            $transaction_table = $value->transaction_table;   // because selected as AS type
            $rid  = $value->rid;

            // initialize key if not exists
            if (!isset($approvals[$types[$transaction_table]])) {
                $approvals[$types[$transaction_table]] = array();
            }

            $approvals[$types[$transaction_table]][] = array(
                self::KEY_LABEL => isset($labels[$transaction_table]) ? $labels[$transaction_table] : ucwords(str_replace('_', ' ', $transaction_table)),
                'requested' => $value->requested,
                'pending_approval' => $value->pending_approval,
                'approval_status' => $value->current_status, // or map separately if needed
                'rid' => $rid,
                self::KEY_TRANSACTION_TABLE => $transaction_table,
                'type' => $types[$transaction_table] ?? '',
                'transaction_approval_log_id' => $value->transaction_approval_log_id
            );
        }

        $data['ongoing_operations']['approval_data'] = $approvals;

        $query = "SELECT id,table_name,name,status,tigger,fixable,action_label,display_priority FROM status_progress WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0 AND table_name IN ($table_list)";
        $status_data = $callConfig->getAllRows($query);
        $status_arr = array();
        foreach ($status_data as $row) {
            $status_arr[$types[$row->table_name]][$row->status] = $row;
        }
        $data['ongoing_operations']['status_arr'] = $status_arr;

        $query = "SELECT id,designation_name FROM designations WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0";
        $res = $callConfig->getAllRows($query);
        $designation_arr = array();
        foreach ($res as $row) {
            $designation_arr[$row->id] = $row;
        }
        $data['designation_arr'] = $designation_arr;

        $query = "SELECT id,department_name FROM departments WHERE company_id={$get[self::KEY_COMPANY_ID]} AND is_active=0";
        $res = $callConfig->getAllRows($query);
        $department_arr = array();
        foreach ($res as $row) {
            $department_arr[$row->id] = $row;
        }
        $data['department_arr'] = $department_arr;
         return $data;
    }
    public function togetmobilemanagerviewtopemplist($get){
        global $callConfig;
        $res = $this->backviewtogetmobilemanagerdashboard($get);

        $field = $get[self::KEY_FIELD];
        $resdata = $res[self::KEY_TODAY_ATTENDANTS][$field];
        $return['count'] = count($resdata);
        $return['data'] = array_slice($resdata, $get['limit'], $get['lenght']);
        return $return;
    }
    public function getPendingLeaveDetailsByEmpId($get){
        global $callConfig;
        $return = array();
        $rid = $get['rid'] ?? '';
        $table_name = $get['type'] ?? '';
        $company_id = $get[self::KEY_COMPANY_ID] ?? '';
        if($rid == '' || $table_name == ''){
            $return[self::KEY_STATUS] = 0;
            $return[self::KEY_MESSAGE] = 'rid or type missing';
            $return['data'] = array();
            return $return;
        }
        if($table_name == self::TBL_TRANSPORT_GATEWAY){
            $query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM transport_gateway t1, employees emp WHERE t1.id={$rid} AND t1.user_id=emp.user_id AND t1.company_id={$company_id} AND t1.is_active=0";
            $result = $callConfig->getRow($query);
            $return['data'] = $result ?? array();
        }
        if($table_name == self::TBL_TRANSPORT_PERSONAL_GATEWAY){
            $query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM transport_personal_gateway t1, employees emp WHERE t1.id={$rid} AND t1.user_id=emp.id AND t1.company_id={$company_id} AND t1.is_active=0";
            $result = $callConfig->getRow($query);
            $return['data'] = $result ?? array();
        }
        if($table_name == self::TBL_EMPLOYEE_LEAVES){
            $query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM employee_leaves t1, employees emp WHERE t1.id={$rid} AND t1.employee_id=emp.id AND t1.company_id={$company_id} AND t1.is_active=0";
            $result = $callConfig->getRow($query);
            $return['data'] = $result ?? array();
        }
        if($table_name == self::TBL_STATIONERY_REQUESTS){
            $query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM stationery_requests t1, employees emp WHERE t1.id={$rid} AND t1.emp_id=emp.id AND t1.company_id={$company_id} AND t1.is_active=0";
            $result = $callConfig->getRow($query);
            $return['data'] = $result ?? array();

            $query = "SELECT sc.id, sc.qty, si.id as st_item_id, si.item_name, si.image, si.available_qty, si.minmum_qty FROM  stationery_cart sc, stationery_items si WHERE sc.request_no = '{$result->request_no}' AND sc.item_id=si.id AND sc.company_id={$company_id} AND sc.is_active=0";
            $result = $callConfig->getAllRows($query);
            $return['items_data'] = $result ?? array();

        }
        if($table_name == self::TBL_ASSET_REQUESTS){
            $query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM asset_requests t1, employees emp WHERE t1.id={$rid} AND t1.originator=emp.user_id AND t1.company_id={$company_id} AND t1.is_active=0";
            $result = $callConfig->getRow($query);
            $return['data'] = $result ?? array();

            $query = "SELECT * FROM  asset_attributes att WHERE att.id IN ({$result->attribute}) AND att.company_id={$company_id} AND att.is_active=0";
            $result = $callConfig->getAllRows($query);
            $return['items_data'] = $result ?? array();

        }
        if($table_name == self::TBL_EMPLOYEE_DAILY_ATTENDANCES){
            $query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM employee_daily_attendances t1, employees emp WHERE t1.id={$rid} AND t1.employee_id=emp.id AND t1.company_id={$company_id} AND t1.is_active=0";
            $result = $callConfig->getRow($query) ?? array();
            $return['data'] = $result;

        }
        return $return;
    }

    public function getResignedEmployeeAssetDetailsByEmpId($get){
        global $callConfig;
        $return = array();
        $emp_id = $get[self::KEY_EMP_ID] ?? '';
        $company_id = $get[self::KEY_COMPANY_ID] ?? '';
        if($emp_id == '' || $company_id == ''){
            $return[self::KEY_STATUS] = 0;
            $return[self::KEY_MESSAGE] = 'emp_id or company_id missing';
            $return['employee'] = array();
            $return[self::TBL_ASSETS] = array();
            return $return;
        }
        $employee_query = "SELECT emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image, emp.reporting_manager_id, et.id as termination_id, et.termination_type, et.termination_reason, et.notice_period_required, et.effective_date, et.noticeperiod_days, et.shortfalldays_in_noticeperiod, et.employee_leaving_date, et.exclude_final_settlement, et.remarks_by_employee, et.current_status
            FROM employees emp
            INNER JOIN employee_terminations et ON et.emp_id = emp.id AND et.company_id = emp.company_id AND et.is_active = 0
            WHERE emp.company_id = '".$company_id."' AND emp.id = '".$emp_id."' AND emp.is_active = 0 ORDER BY et.id DESC LIMIT 1";
        $employee_result = $callConfig->getRow($employee_query);

        $assets_query = "SELECT ea.id, ea.emp_id, ea.asset_name, ea.description
                        FROM employee_assets ea WHERE ea.company_id = '".$company_id."'  AND ea.emp_id = '".$emp_id."' AND ea.is_active = 0 ORDER BY ea.id DESC";
        $assets_result = $callConfig->getAllRows($assets_query);
        $return[self::KEY_STATUS] = 1;
        $return[self::KEY_MESSAGE] = 'Resigned employee and asset details fetched successfully';
        $return['employee'] = $employee_result ? $employee_result : array();
        $return['asset_count'] = count((array)$assets_result);
        $return[self::TBL_ASSETS] = $assets_result;
        return $return;
    }
    public function togetjobpostingdetails($get){
        global $callConfig;
        $query = "SELECT jp.id, jp.job_code, jp.job_number, jp.job_name, CASE jp.job_status WHEN 0 THEN 'Open' WHEN 1 THEN 'Close' WHEN 2 THEN 'Hold' ELSE 'Unknown' END AS job_status, CASE jp.employment_mode WHEN 1 THEN 'Full Time' WHEN 2 THEN 'Part Time' WHEN 3 THEN 'Distance Learning Program' WHEN 4 THEN 'Executive Program' WHEN 5 THEN 'Certification' ELSE 'Unknown' END AS employment_mode, jp.location_id, (SELECT loc.location_name FROM locations loc WHERE loc.id = jp.location_id ) AS location_name, requested_date, (SELECT COUNT(jps.id) FROM job_posting_candidates jps WHERE jps.job_posting_id = jp.id AND jps.is_active = 0 ) AS applicant FROM job_postings jp WHERE jp.company_id =".$get[self::KEY_COMPANY_ID]." AND jp.is_active = 0";
        $rows = $callConfig->getAllRows($query);
        $return['data'] = $rows;
        return $return;
    }
    public function togetjobpostingdetailsbyid($get){
        global $callConfig;
        //CAST(FROM_BASE64(jp.job_desc) AS CHAR) AS`job_desc`,
        $query = "SELECT
                    jp.id,
                    jp.job_code,
                    jp.job_number,
                    jp.job_name,
                    jp.`min_exp`,
                    jp.`max_exp`,
                    jp.`no_of_resources`,
                    jp.`min_salary`,
                    jp.`max_salary`,
                    jp.`video_url`,
                    jp.`total_resource_cost`,
                    jp.`job_responsibility`,
                    jp.`job_desc`,
                    CASE jp.job_status
                        WHEN 0 THEN 'Open'
                        WHEN 1 THEN 'Close'
                        WHEN 2 THEN 'Hold'
                        ELSE 'Unknown'
                    END AS job_status,

                    CASE jp.employment_mode
                        WHEN 1 THEN 'Full Time'
                        WHEN 2 THEN 'Part Time'
                        WHEN 3 THEN 'Distance Learning Program'
                        WHEN 4 THEN 'Executive Program'
                        WHEN 5 THEN 'Certification'
                        ELSE 'Unknown'
                    END AS employment_mode,
                    jp.location_id,
                    (SELECT loc.location_name FROM locations loc WHERE loc.id = jp.location_id ) AS location_name,
                    requested_date,
                    (SELECT COUNT(jps.id) FROM job_posting_candidates jps WHERE jps.job_posting_id = jp.id AND jps.is_active = 0 )  AS applicant
                FROM job_postings jp WHERE jp.company_id =".$get[self::KEY_COMPANY_ID]."  AND jp.id=".$get['job_post_id']."  AND jp.is_active = 0 ";
        $rows = $callConfig->getRow($query);
        $return['data'] = $rows;
        $query = "SELECT
                jpc.id,
                jpc.`candidate_name`,
                jpc.`candidate_email`,
                jpc.`candidate_phone`,
                jpc.`current_location`,
                jpc.`preferred_location`,
                jpc.`experience`,
                jpc.`current_salary`,
                jpc.`candidate_id`,
                jpc.`created_date`,
                jpc.`job_selection_process_step_id`,

                CASE
                    WHEN jpc.candidate_id IS NOT NULL THEN 'Selected'
                    WHEN jpc.job_selection_process_step_id IS NOT NULL THEN
                        (SELECT jsps.select_name
                         FROM job_selection_process_steps jsps
                         WHERE jsps.id = jpc.job_selection_process_step_id)
                    ELSE 'Screening'
                END AS selection_status

            FROM job_posting_candidates jpc
            WHERE jpc.job_posting_id = ".$get['job_post_id']."  AND jpc.is_active = 0 ";

        $rows = $callConfig->getAllRows($query);
        $return['candidates'] = $rows;
        return $return;
    }
    public function tocandidatedetailsbyid($get){
        global $callConfig;

        $query = "SELECT
                jpc.`candidate_name`,
                jpc.`candidate_email`,
                jpc.`candidate_phone`,
                jpc.`current_location`,
                jpc.`preferred_location`,
                jpc.`experience`,
                jpc.`current_salary`,
                jpc.`candidate_id`,
                jpc.`created_date`,
                jpc.`job_selection_process_step_id`,

                CASE
                    WHEN jpc.candidate_id IS NOT NULL THEN 'Selected'
                    WHEN jpc.job_selection_process_step_id IS NOT NULL THEN
                        (SELECT jsps.select_name
                         FROM job_selection_process_steps jsps
                         WHERE jsps.id = jpc.job_selection_process_step_id)
                    ELSE 'Screening'
                END AS selection_status

            FROM job_posting_candidates jpc
            WHERE jpc.id = ".$get['candidate_id']."  AND jpc.is_active = 0 ";

        $rows = $callConfig->getRow($query);
        $return['candidates'] = $rows;
        return $return;
    }
    public function togettaskgrpsdetails($get){
        global $callConfig;
            $uewhr = "";
            if($get['role_short_code']!='TASKDA_ADMIN'){
                $uewhr = " AND id IN ( SELECT project_task_id FROM project_task_teams WHERE company_id = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_USER_ID.$get[self::KEY_USER_ID]." AND is_active =0) ";
            }
            $query ="SELECT
                COUNT(*) AS total_tasks,

                SUM(CASE
                        WHEN current_status = 199 THEN 1
                        ELSE 0
                    END) AS completed_tasks,

                SUM(CASE
                        WHEN current_status < 199 THEN 1
                        ELSE 0
                    END) AS pending_tasks,

                SUM(CASE
                        WHEN current_status < 199
                         AND planned_finish < NOW() THEN 1
                        ELSE 0
                    END) AS overdue_tasks

            FROM project_task_details
            WHERE company_id = ".$get[self::KEY_COMPANY_ID]."
              ".$uewhr."
              AND is_active = 0";
        $row['data'] = $callConfig->getRow($query);
        return $row;
    }
    public function tgetcurrenttasklogdata($get){
        global $callConfig;
        $query = "SELECT ptd.task_name, ptd.planned_finish, CASE WHEN CURDATE() > ptd.planned_finish THEN 'Y' ELSE 'N' END AS overdue FROM project_task_details ptd WHERE company_id =1 AND id IN ( SELECT DISTINCT task_id FROM project_task_time_log WHERE company_id =1 AND user_id = 620 AND is_active = 0 ) AND current_status <199 AND is_active = 0 ORDER BY id";
        $row['data'] = $callConfig->getAllRows($query);
        return $row;
    }
    public function toupdateprojecttaskteamremove($get, $post){

        global $callConfig;
        $callConfig->updateRecord($get[self::KEY_TABLE],$post,'id',$get['rid']);

        $task_query = "SELECT id, task_name FROM project_task_details WHERE company_id = ".$get[self::KEY_COMPANY_ID]."   AND id IN ( SELECT project_task_id FROM project_task_teams WHERE id=".$get['rid'].") AND  is_active = 0";
        $task_data = $callConfig->getRow($task_query);
        $query = "SELECT id, company_id,display_name, user_email, user_mobile FROM application_users WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND id IN ( SELECT user_id FROM project_task_teams WHERE id=".$get['rid'].") AND is_active =0";
            $user_data = $callConfig->getRow($query);
            $get['mobile'] = $user_data->user_mobile;

            $message = 'You have been unassigned from the task '.$task_data->task_name;

            $array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], 'mobile_no'=>$get['mobile'], self::KEY_MESSAGE=>$message, 'send_no'=> '8179947020', 'recivce_no'=> $get['mobile'],self::KEY_DATA_NAME=>$get[self::KEY_DATA_NAME]);

            $res['msg_otp']['whatsup'] = $this->tosendwhatsappdata($array);


            $array['user_name'] = $user_data->display_name;
            $array['user_email_id'] = $user_data->user_email;
            $array['session_company_id'] = $user_data->company_id;
            $notarry = array();
            $this->sendNotificationAndEmails($notarry,$array,99);
        return $res;
    }


    //trippppppppppppppppppppppppppppppp

    public function togetmobiletripdetails($get){
        global $callConfig;
        $return = array();
        $claimObj=new claimUserClass();
        $return = $claimObj->getTrevllingPlanData($get);
        $query ="SELECT id, city_name, state_id FROM cities WHERE company_id =  {$get[self::KEY_COMPANY_ID]} AND is_active =0";
        $data = $callConfig->getAllRows($query);

        $return['cities'] = $data;
        return $return;
    }
    public function toinserttripexpencesdetails($get, $post){
        global $callConfig;

        $claimObj=new claimUserClass();
        return $claimObj->insertAndUpdateTrevllingPlans($get, $post);
    }
    public function togettripsdetails($get){
        global $callConfig;
        $query = "SELECT tp.*, ( SELECT pt.project_name FROM projects pt WHERE pt.id = tp.project_id) as project_name, ( SELECT cs.display_name FROM customers cs WHERE cs.id = tp.customer_id) as client_name FROM trips tp WHERE tp.company_id =".$get[self::KEY_COMPANY_ID].self::SQL_AND_USER_ID.$get[self::KEY_USER_ID]." AND tp.is_active =0";
        $return['data'] = $callConfig->getAllRows($query);
        return $return;
    }
    public function togettripdetailsbasedid($get){
        global $callConfig;
        $return['get'] = $get;
        return $return;
    }
    public function toupdatetripexpencesdetails($get, $post){
        global $callConfig;

        $user_id = $get[self::KEY_USER_ID];
        $data = json_decode($post['data'],1);
        $trip_id = $get['trip_id'];
        $insert_trip = array('trip_name' => $data['trip_name'], 'trip_type' => $data['trip_type'], 'trip_purpose' => $data['work_description'], self::KEY_PROJECT_ID => $data[self::KEY_PROJECT_ID], 'customer_id' => $data['client_id'],  self::KEY_UPDATED_BY=>$get[self::KEY_USER_ID]);
        $callConfig->updateRecord('trips',$insert_trip,'id',$trip_id);
        $stops = $data['stops'];
        $array = array('flight_legs'=>'1','car_details'=>'2','bus_details'=>'3','own_vehicle_details'=>'4');
        $callConfig->updateRecord('domestic_round_trips',array(self::KEY_IS_ACTIVE=>1),'trip_id',$trip_id);
        $callConfig->updateRecord('local_trips',array(self::KEY_IS_ACTIVE=>1),'trip_id',$trip_id);
        $callConfig->updateRecord('trip_hotels',array(self::KEY_IS_ACTIVE=>1),'trip_id',$trip_id);

        foreach((array)$stops as $key=>$stop){
            $rr =$array[$key];
            foreach($stop as $val){
                if($key =='flight_legs' || ( isset($val['trip_type']) && $val['trip_type'] =='intercity') ){

                    $insert_trip_dom = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], 'trip_id' => $trip_id, self::KEY_USER_ID => $get[self::KEY_USER_ID], 'journey_type' => $rr, 'origin_location' => $val['from_city_id'], 'destination_location' => $val['to_city_id'],'departure_date' => $val['departure_date'], 'departure_time' => $val['departure_time'],'return_date' => $val['return_date'], 'travel_mode' => $val['travel_mode'], 'return_time' => $val['return_time'],   self::KEY_IS_ACTIVE=>0,self::KEY_CURRENT_STATUS=>0,self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
                    $res['dom_insert'][] = $insert_trip_dom ;
                    $res['dom'][]=$callConfig->insertRecord('domestic_round_trips',$insert_trip_dom);
                }else{
                    $departure_date = $val['departure_date'] !="" ? date(self::DATETIME_FORMAT_YMD_HIS, strtotime($val['departure_date']." ". $val['departure_time'])) : "";
                    $insert_trip_loc = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], 'journey_type'=> $rr,'trip_id' => $trip_id, self::KEY_USER_ID => $get[self::KEY_USER_ID], 'trip_purpose' => $val['trip_purpose'], 'trip_date' => $val['departure_date'], 'from_location' => $val['from_city_id'],'to_location' => $val['to_city_id'], 'pickup_at' => $departure_date,'dropoff_at' => $val['dropoff_at'], self::KEY_IS_ACTIVE=>0,self::KEY_CURRENT_STATUS=>0,self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
                    $res['loc_insert'][] = $insert_trip_loc ;
                    $res['loc'][]=$callConfig->insertRecord('local_trips',$insert_trip_loc);

                }
            }
            //}

        }
        foreach((array)$data['accommodations'] as $hotel){
            $rec = array(
                    self::KEY_COMPANY_ID     => $get[self::KEY_COMPANY_ID],
                    'trip_id'        => $trip_id,
                    'hotel_name'     => $hotel['hotel_name'] ?? null,
                    'check_in'            => $hotel['check_in'] ,
                    'check_out'           => $hotel['check_out'],
                    self::KEY_LOCATION            => $hotel['city_id'] ?? null,
                    'description'         => $hotel['description'] ?? null,
                    self::KEY_IS_ACTIVE      => 0,
                    self::KEY_CURRENT_STATUS => 0,
                    self::KEY_CREATED_BY     => $user_id
                );
            $res['hotel'][] = $callConfig->insertRecord('trip_hotels', $rec);

        }
        $return['estimation'] = '20000';
        return $return;
    }

    public function toaddprojecttaskdetails($get,$post){
        global $callConfig;
        $whatsup = '';
        $notuser2 = array(); $notuser1 = array();
        $post[self::KEY_IS_ACTIVE]=0;
        $post[self::KEY_CURRENT_STATUS]=0;
        $post[self::KEY_CREATED_BY]=$get[self::KEY_USER_ID];
        $res = $callConfig->insertRecord($get[self::KEY_TABLE],$post);
        $callConfig->executeQuery("UPDATE `project_task_details` SET  to_date= ".date(self::DATE_FORMAT_YMD)." WHERE id=".$post[self::KEY_TRANSACTION_ID]);
        if(!isset($post[self::KEY_PROJECT_ID])){
            $task_data = $callConfig->getRow("SELECT * FROM project_task_details WHERE id=".$post[self::KEY_TRANSACTION_ID]);
            $transaction_number =$task_data->project_id;
            $post[self::KEY_PROJECT_ID] = $task_data->project_id;
        }else{
            $transaction_number = $post[self::KEY_PROJECT_ID];
        }
        $assgnuser = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$get[self::KEY_USER_ID]);
        $msg1 = " Task ".$post['task_name']." is updated : Comments added  by ".$assgnuser->display_name;
        $notuser1[] = $get[self::KEY_USER_ID];
        $notuser1[] = $task_data->assigen_id;
        if(!isset($_POST['activity_type'])){
            $project = $callConfig->getRow("SELECT project_name,assigner_id FROM projects WHERE id=".$post[self::KEY_PROJECT_ID]);
            $transaction_number = $post[self::KEY_PROJECT_ID];
            $user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$post['assigen_id']);
            $insert_arr =  array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_PROJECT_ID=>$post[self::KEY_PROJECT_ID],'project_task_id'=>$res[self::KEY_LASTID], 'project_role_id'=>2,self::KEY_USER_ID=>$post['assigen_id'],self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
            $callConfig->insertRecord('project_task_teams',$insert_arr);
            $insert_arr['project_role_id'] = 1;
            $insert_arr[self::KEY_USER_ID] = $project->assigner_id;
            $callConfig->insertRecord('project_task_teams',$insert_arr);
            $msg1 = "Task ".$post['task_name']." is assigned to you";

        }
        $query = "SELECT user_id, ( SELECT user_mobile FROM application_users au WHERE au.id=user_id) as user_mobile FROM project_task_teams WHERE company_id =".$get[self::KEY_COMPANY_ID]." AND project_task_id = ".$post[self::KEY_TRANSACTION_ID]."  AND is_active = 0";

        $lists = $callConfig->getAllRows($query);
        foreach($lists as $list){
            $notuser2[] = $list->user_id;
        }
            foreach($lists as $list){

                if($post['activity_type']=='activity'){
                    $mobilsendnotification = array();
                    $mobilsendnotification = $get;
                    $mobilsendnotification[self::KEY_MESSAGE] ="Someone has added a comment to the task ".$task_data->task_name;
                    $mobilsendnotification[self::KEY_USER_ID] =$list->user_id;
                    $mobilsendnotification['title'] = 'Task Comment';
                    $mobilsendnotification['data'] = array('module' => 'TASKDA', self::KEY_MODULE_ID => '25', 'click_action' => 'OPEN_TASK',self::KEY_TASK_ID =>$post[self::KEY_TRANSACTION_ID], self::KEY_PROJECT_ID =>$transaction_number, 'event_type' => 'task_assign', self::KEY_APPLICATION_TABLE_NAME => self::TBL_PROJECT_TASK_DETAILS);
                    $this->tocallmobilenotificationmob($mobilsendnotification);

                }

            }
            $email_content_id =37;
            $notdata1 = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], 'notification_message'=> $msg1, self::KEY_TRANSACTION_NUMBER=>$transaction_number, self::KEY_APPLICATION_TABLE_NAME=>self::TBL_PROJECT_TASK_DETAILS, 'type'=>'INFORMATION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_PROJECT_KANBAN&TAB=3&RID='.$transaction_number,'module_setup_id'=>25, 'whatsup'=>$whatsup);

            $this->tosendnotifiations($notdata1);
            $senddata = array('session_company_id'=>$get[self::KEY_COMPANY_ID],'email_content_id'=>$email_content_id, 'project_name'=>$project->project_name,'user_display_name'=>$user->display_name,'cc_users'=>$notuser2,'to_users'=>$notuser1,'task_name'=>$post['task_name'],'assigners_name'=>$assgnuser->display_name);
            $message = "A new comment has been posted on the task: ".$task_data->task_name;
            $senddata['push_notiction'] = $mobres;
            return $senddata;

    }
    public function toinsertupdateprojecttasktimlog($get,$post){
        global $callConfig;
        $crow=$callConfig->getRow("SELECT id,task_start,task_id FROM project_task_time_log WHERE  company_id = ".$get[self::KEY_COMPANY_ID]." AND task_end is NULL AND is_active = 0 ORDER BY id DESC LIMIT 0,1");
        $callConfig->executeQuery("UPDATE `project_task_details` SET  to_date= ".date(self::DATE_FORMAT_YMD)." WHERE id=".$post[self::KEY_TASK_ID]);
        if($post['type'] == 'start'){
            // ------old data---- //

            if($crow->id !=""){
                $query = "UPDATE project_task_time_log SET task_end='".$post['starttime']."' WHERE  id=".$crow->id;
                $res['update_old_log']= $callConfig->executeQuery($query);
                $work_time = (strtotime($post['starttime'])-strtotime($crow->task_start) );
                $query = "UPDATE project_task_details SET work_time= work_time+".$work_time." WHERE company_id = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_ID.$crow->task_id.self::SQL_AND_IS_ACTIVE_0;
                $res['update_old_task']= $callConfig->executeQuery($query);
            }
            // -----old data end---- //
            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], self::KEY_TASK_ID => $post[self::KEY_TASK_ID], self::KEY_USER_ID => $get[self::KEY_USER_ID],'time_date'=>date(self::DATE_FORMAT_YMD),'task_start'=>$post['starttime'],self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
            $res = $callConfig->insertRecord('project_task_time_log',$insert_array);
        }
        if($post['type'] == 'pause'){
            $query = "UPDATE project_task_time_log SET task_end='".$post['endtime']."' WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND task_id=".$post[self::KEY_TASK_ID]." AND  task_end is NULL AND is_active = 0";
            $res['update_log_query'] = $query;
            $res['update_log']= $callConfig->executeQuery($query);
            $work_time = (strtotime($post['endtime']) - strtotime($crow->task_start));
            $res['data']= array('endtime'=>$post['endtime'],'task_start'=>$crow->task_start,'work_time'=>$work_time );
            $query = "UPDATE project_task_details SET work_time= work_time+".$work_time." WHERE company_id = ".$get[self::KEY_COMPANY_ID].self::SQL_AND_ID.$post[self::KEY_TASK_ID].self::SQL_AND_IS_ACTIVE_0;
            $res['update_tassk_query'] = $query;
            $res['update']= $callConfig->executeQuery($query);

        }
        $task_query = "SELECT id, project_id, task_name FROM project_task_details WHERE id=".$post[self::KEY_TASK_ID];
        $task_data  = $callConfig->getRow($task_query);
        $task_team_q = "SELECT id,  user_id FROM project_task_teams WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND  project_task_id=".$post[self::KEY_TASK_ID].self::SQL_AND_IS_ACTIVE_EQ0;
        $teams  = $callConfig->getAllRows($task_team_q);
        foreach($teams as $list){
        $mobilsendnotification = array();
        $mobilsendnotification = $get;
        $mobilsendnotification[self::KEY_MESSAGE] ="Someone has updated task time log ".$task_data->task_name;
        $mobilsendnotification[self::KEY_USER_ID] =$list->user_id;
        $mobilsendnotification['title'] = 'Task log';
        $mobilsendnotification['data'] = array('module' => 'TASKDA', self::KEY_MODULE_ID => '25', 'click_action' => 'OPEN_TASK',self::KEY_TASK_ID =>$post[self::KEY_TASK_ID], self::KEY_PROJECT_ID =>$task_data->project_id, 'event_type' => 'task_assign', self::KEY_APPLICATION_TABLE_NAME => self::TBL_PROJECT_TASK_DETAILS);
        $this->tocallmobilenotificationmob($mobilsendnotification);
        }
        return $res;
    }
     public function adddataprojecttask($get,$post){
        global $callConfig;

        $this->insertTablesdata($post, $get[self::KEY_TABLE]);
        $post[self::KEY_TASK_ID] = 100;

            $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_PROJECT_ID=>$post[self::KEY_PROJECT_ID], 'project_task_id'=>$post[self::KEY_TASK_ID], 'project_role_id'=>1,self::KEY_USER_ID=>$post['assigen_id'],self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID],self::KEY_CREATED_DATE=>date(self::DATETIME_FORMAT_YMD_HIS));
            $callConfig->insertRecord('project_task_teams',$insert_array);
            $task_query = "SELECT id, task_name, created_by FROM project_task_details WHERE company_id = ".$get[self::KEY_COMPANY_ID]."   AND id =".$post[self::KEY_TASK_ID]." AND  is_active = 0";
            $task_data = $callConfig->getRow($task_query);

            $mobilsendnotification = array();
            $mobilsendnotification = $get;
            $mobilsendnotification[self::KEY_MESSAGE] ="Someone has assign new task ".$post['task_name'];
            $mobilsendnotification[self::KEY_USER_ID] =$post['assigen_id'];
            $mobilsendnotification['title'] = 'New Task';
            $mobilsendnotification['data'] = array('module' => 'TASKDA', self::KEY_MODULE_ID => '25', 'click_action' => 'OPEN_TASK',self::KEY_TASK_ID =>$post[self::KEY_TASK_ID] , self::KEY_PROJECT_ID =>$post[self::KEY_PROJECT_ID] , 'event_type' => 'task_assign', self::KEY_APPLICATION_TABLE_NAME => self::TBL_PROJECT_TASK_DETAILS);
            $this->tocallmobilenotificationmob($mobilsendnotification);

            return $task_data;
    }
    public function updatedynamicfiedrojecttaskdetail( $get, $post){
        global $callConfig;
        if($post[self::KEY_FIELD] == "planned_start" || $post[self::KEY_FIELD]=="planned_finish"){
            $post['fielddata'] = date(self::DATE_FORMAT_YMD, strtotime(str_replace('/"', '-', $post['fielddata'])));
        }
        $query = "UPDATE ".$post[self::KEY_TABLE_NAME]." SET ".$post[self::KEY_FIELD]."='".$post['fielddata']."' WHERE id=".$post['update_id'];
        $callConfig->executeQuery($query);

        if($post[self::KEY_TABLE_NAME]==self::TBL_PROJECT_TASK_DETAILS){
            $task_query = "SELECT id, project_id, task_name FROM project_task_details WHERE id=".$post['update_id'];
            $task_data  = $callConfig->getRow($task_query);
            $task_team_q = "SELECT id,  user_id FROM project_task_teams WHERE company_id = ".$get[self::KEY_COMPANY_ID]." AND  project_task_id=".$post['update_id'].self::SQL_AND_IS_ACTIVE_EQ0;
            $teams  = $callConfig->getAllRows($task_team_q);
            foreach($teams as $list){
                $mobilsendnotification = array();
                $mobilsendnotification = $get;
                $mobilsendnotification[self::KEY_MESSAGE] ="Someone has udated  to the task details ".$task_data->task_name;
                $mobilsendnotification[self::KEY_USER_ID] =$list->user_id;
                $mobilsendnotification['title'] = 'Task Comment';
                $mobilsendnotification['data'] = array('module' => 'TASKDA', self::KEY_MODULE_ID => '25', 'click_action' => 'OPEN_TASK',self::KEY_TASK_ID =>$post[self::KEY_TRANSACTION_ID], self::KEY_PROJECT_ID =>$task_data->project_id, 'event_type' => 'task_assign', self::KEY_APPLICATION_TABLE_NAME => self::TBL_PROJECT_TASK_DETAILS);
                $this->tocallmobilenotificationmob($mobilsendnotification);
            }

        }
        return $res;
    }
    public function toinsertactivitydata($post,$get){
        global $callConfig;
        if($post[self::KEY_TABLE_NAME]=='projects'){
            // Intentionally empty
        }
        $comments = "Update this card ".str_replace("_"," ",$post[self::KEY_FIELD])." to be ".$post['fielddata'];
        $insert_array = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID],self::KEY_APPLICATION_TABLE_NAME=>self::TBL_PROJECT_TASK_DETAILS,self::KEY_TRANSACTION_ID=>$post['update_id'],'activity_type'=>'task_details','filed_name'=>$post[self::KEY_FIELD],'new_value'=>$post['fielddata'],'comments'=>$comments ,self::KEY_IS_ACTIVE=>'0',self::KEY_CURRENT_STATUS=>'0',self::KEY_CREATED_BY=>$get[self::KEY_USER_ID]);
        $callConfig->insertRecord('project_task_activities',$insert_array);



        //rohan added
        $passprojecttaskid = $this->passprojectTaskid($post, $get,$post['fielddata']);
        $taskowner = $passprojecttaskid['taskowner'];
        $projectteamuserlist = $passprojecttaskid['projectteamuserlist'];
        $taskfollower = $passprojecttaskid['taskfollower'];
        $taskdetails = $passprojecttaskid['taskdetails'];
        $projectteamuserowner =$projectteamuserlist['1'][0];
        $projectteamuserreviewer =$projectteamuserlist['3'];
        $status_list = $passprojecttaskid['status_list'];
        $statuslist = array();
        foreach($status_list as $status_lists){
            $statuslist[$status_lists->status] = $status_lists->name;
        }
        $notuser[$projectteamuserowner->id] = $projectteamuserowner->id; // projectowner
        foreach ($projectteamuserreviewer as $uid){
            $notuser[$uid->id] = $uid->id;
        }
        foreach ($taskfollower as $uid){
            $notuser[$uid->id] = $uid->id;
        }
        $notuser[$get[self::KEY_USER_ID]] = $get[self::KEY_USER_ID];
        $msg1 = "Task ".$taskdetails->task_name." is assigned to ".$taskowner->display_name;
        $notdata1 = array(self::KEY_COMPANY_ID=>$get[self::KEY_COMPANY_ID], 'notification_message'=> $msg1, self::KEY_TRANSACTION_NUMBER=>$taskdetails->id, self::KEY_APPLICATION_TABLE_NAME=>self::TBL_PROJECT_TASK_DETAILS, 'type'=>'INFORMATION', 'notification_users'=>$notuser,'option'=>'COM_VIEW_PROJECT_KANBAN&RID='.$taskdetails->project_id,'module_setup_id'=>25);
        $this->tosendnotifiations($notdata1);
        $owneuser[$taskowner->id] = $taskowner->id; // taskowner
        $notdata1['type'] = "ACTION";
        $notdata1['notification_users'] = $owneuser;
        $project = (object)array('project_name'=>'');
        $email_content_id =38;
        $senddata = array('session_company_id'=>$get[self::KEY_COMPANY_ID],'email_content_id'=>$email_content_id, 'project_name'=>$project->project_name,'user_display_name'=>$taskowner->display_name,'cc_users'=>$notuser,'to_users'=>$owneuser,'task_name'=>$taskdetails->task_name,'assigners_name'=>'','planned_start'=>$taskdetails->planned_start,'planned_finish'=>$taskdetails->planned_finish,self::KEY_CURRENT_STATUS=>$statuslist[$taskdetails->current_status]);
        //rohan added
        return $this->emailsendmediater($senddata);
    }
    public function passprojectTaskid($post,$get,$task_id){
        global $callConfig;
        $projectteams = array();
        $projectteamuerlist = array();
            $post[self::KEY_TABLE] =$post[self::KEY_TABLE_NAME];
            $return['status_list'] = $this->getStatusProgressList($post);

            $tasksql ="SELECT id, assigen_id, follower_ids, project_id, task_name, planned_start, planned_finish, current_status FROM project_task_details WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND id=".$post['update_id'].self::SQL_AND_IS_ACTIVE_0;
            $taskres = $callConfig->getRow($tasksql);
            $return['taskdetails'] = $taskres;
            $projectsql ="SELECT role_id,user_id FROM project_teams WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND project_id=".$taskres->project_id.self::SQL_AND_IS_ACTIVE_0;
            $projectres = $callConfig->getAllRows($projectsql);
            foreach($projectres as $projectres){
                $projectteams[$projectres->user_id] = $projectres->user_id;
                $projectteamsrole[$projectres->user_id] = $projectres->role_id;
            }
            $projectteamsql ="SELECT id,display_name, user_email FROM application_users WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND id IN(".implode(",", $projectteams).") AND is_active = 0";
            $projectteamres = $callConfig->getAllRows($projectteamsql);
            foreach ($projectteamres as $projectteamre) {
                $projectteamuerlist[$projectteamsrole[$projectteamre->id]][] = $projectteamre;
            }
            $return['projectteamuserlist'] = $projectteamuerlist;
            $taskownerusersql ="SELECT id,display_name, user_email FROM application_users WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND id=".$taskres->assigen_id.self::SQL_AND_IS_ACTIVE_0;
            $taskowneruser = $callConfig->getRow($taskownerusersql);
            $return['taskowner'] = $taskowneruser;

            $taskfollowerrusersql ="SELECT id,display_name, user_email FROM application_users WHERE company_id = '".$get[self::KEY_COMPANY_ID]."' AND id IN(".$taskres->follower_ids.") AND is_active = 0";
            $taskfollowerruserres = $callConfig->getAllRows($taskfollowerrusersql);
            $return['taskfollower'] = $taskfollowerruserres;
        //}
        return $return;
    }
}
