<?php
require_once "dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options; 
class employeeManagementUserClass extends userClass{
	function getEMAminDashboard($get){
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $finance_tax_year = $get['finance_tax_year'];
        if($finance_tax_year !=""){
            $startdate =date($finance_tax_year."-04-01");
            $enddate =date(($finance_tax_year+1)."-03-31");
        }
        $empsql1 = "SELECT id, user_id FROM employees WHERE company_id = '".$get['company_id']."' AND is_active = 0";
        $empres1 = $callConfig->getAllRows($empsql1);
        $empidwithuserid = array();
        foreach($empres1 as $empres0){
            $empidwithuserid[$empres0->id] = $empres0->user_id;
        }
        $return['empidwithuserid'] = $empidwithuserid;
       
        $employee_id = $get['employee_id'];
        $empsql = "SELECT id, image, emp_code, display_name, email_id, user_id, joining_date, date_of_leaving, finance_buddy FROM employees WHERE company_id = '".$get['company_id']."' AND joining_date <='".$startdate."' AND is_active = 0 AND (date_of_leaving >= '".$startdate."' OR date_of_leaving IS NULL) AND (('1' = '".$get['admin_type']."') OR (reporting_manager_id = '".$get['user_id']."') OR (hr_buddy = '".$employee_id."') OR (finance_buddy  = '".$employee_id."') OR (admin_buddy = '".$employee_id."') OR (project_buddy = '".$employee_id."') OR (loc_buddy = '".$employee_id."')) ORDER BY display_name ASC";
        $empres = $callConfig->getAllRows($empsql);
        $return['employees_sql'] = $empsql;
        $return['employees'] = $empres;
        $onboardempid = array();
        foreach($empres as $empres){
           // $empidwithuserid[$empres->id] = $empres->user_id;
            $onboardempid[$empres->id] = $empres->id;
        }
        $reviewtask[$get['user_id']] = $get['user_id'];
        //AND tas.assigned_to IN(".implode(',', (array)$reviewtask).")        
        $tasks_cntssql = "SELECT assigned_to, transaction_id, count(*) as total_task_cnt, sum(if((current_status=".ST_APPROVED." OR current_status=".ST_APPROVED1." OR current_status=4),1,0)) as approve_requist_cnt, sum(if(current_status=".ST_APPROVED.",1,0)) as approve_cnt, sum(if(current_status=".ST_COMPLETE.",1,0)) as review_cnt, sum(if(current_status=".ST_INPROGRESS.",1,0)) as inprogress_cnt, sum(if(current_status=".ST_ACTIVE.",1,0)) as yet_to_start_cnt, sum(if(action_date < completion_date,1,0)) as delayed_cnt FROM transaction_activity_schedule tas WHERE  tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'employees'  AND transaction_id IN(".implode(',', (array)$onboardempid).") AND tas.is_active = 0 AND action_date >='".$startdate." 00:00:00' AND action_date <='".$enddate." 23:59:59' GROUP BY tas.transaction_id";
        $tasks_cntsres = $callConfig->getAllRows($tasks_cntssql);
        $return['tasks_cntssql'] = $tasks_cntssql;
        $return['tasks_result'] = $tasks_cntsres;


        $action_transaction_activity_schedulesql = "SELECT tas.id, tas.transaction_table, tas.transaction_id, tas.task_id, tas.assigned_to, tas.approval_user_id, tas.action_date, tas.revised_action_date, tas.actual_action_date, tas.completion_date, tas.approval_date, tas.action_flag, tas.comments, tas.note, tas.check_list, tas.finish_date, tas.current_status, tas.task_name, tas.process_id, tal.request_date, tal.response_date, tal.comments, tal.response_type FROM transaction_activity_schedule tas, transaction_approval_logs tal WHERE tas.company_id = '".$get['company_id']."' AND tas.company_id = tal.company_id AND tas.transaction_table = 'employees' AND tas.transaction_id IN(".implode(',', (array)$onboardempid).") AND tas.id = tal.transaction_id AND tal.transaction_table ='transaction_activity_schedule' AND tal.user_id = '".$get['user_id']."' AND tal.response_type ='0' AND tal.call_back ='1' AND tas.is_active = 0";
        $action_transaction_activity_scheduleres = $callConfig->getAllRows($action_transaction_activity_schedulesql);
        $return['action_transaction_activity_schedule'] =$action_transaction_activity_scheduleres;


        return $return;
    }   
    function getEMTaskAssigneList($get){
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $finance_tax_year = $get['finance_tax_year'];
        if($finance_tax_year !=""){
            $startdate =date($finance_tax_year."-04-01");
            $enddate =date(($finance_tax_year+1)."-03-31");
        }
        //echo "<pre>";
        if(trim($get['taskassign']) == "Y"){
            $taslarraylist = array();
            $employeesquery = $callConfig->selectQuery('employees','*','id='.$get['employee_id'],'','','');
            $employees = $callConfig->getRow($employeesquery);
            $return['employees'] =  $employees;
            
            $connectdetails = $this->getempconnectwithempid(array('employee_id'=>$get['employee_id'],'company_id'=>$get['company_id']));
            $return['connectdetails'] = $connectdetails; 
            $whr1 = "company_id = '".$get['company_id']."' AND assigned_to = 'EMP' AND process_type='ET' AND is_active = 0";
            $task_query = $callConfig->selectQuery('process_task_settings','*',$whr1,'','','');
            $task_data = $callConfig->getAllRows($task_query);  
            $return['process_task_settings'] = $task_data; 
            foreach($task_data as $tasks1){
                $taskid[$tasks1->id] = $tasks1->id;
            }
            $taskfilterary = array();
            if(count((array)$taskid) != 0){ 
                $taskfiltersql = "SELECT * FROM filter_tables WHERE company_id = '".$get['company_id']."' AND transaction_id IN(".implode(',',$taskid).") AND is_active = 0";
                $taskfilterres = $callConfig->getAllRows($taskfiltersql);
                foreach($taskfilterres as $tasks2){
                    $taskfilterary[$tasks2->transaction_id] = $tasks2;
                }
            }
            foreach ($task_data as $key => $tasks){
                $task_array = array();
                // Start check employee validate or Not For this Task
                $filterres = $taskfilterary[$tasks->id];
                $assignempchek = 0;
                if($filterres->id !="" || $filterres->content !=""){
                    $show_filter_cond = $filterres->filter_condition;
                    $show_filter_cond = explode('&',$show_filter_cond);
                    foreach($show_filter_cond as $show_filter_co){
                        $fieldname = explode("=",$show_filter_co);
                        if($fieldname['0']!="check" AND $fieldname['0']!="table"){
                            $show_filter_condion[$fieldname['0']]=$fieldname['1'];
                        }
                    }
                    foreach($show_filter_condion as $keys=>$vale){
                        if($vale !=""){
                            $convertary = explode(",",$vale);
                            if(!in_array($employees->$keys,$convertary)){
                                $assignempchek = 1;
                            }
                        }
                    }
                }
                // End check employee validate or Not For this Task
                if($assignempchek == 0){
                    if($finance_tax_year !=""){
                        $days_ago = $startdate;
                    }else{
                        $days_ago = $employees->joining_date;
                    }
                    $approval_id = "";
                    if($tasks->approval_type == 'AB'){
                        $approval_id = $connectdetails['admin_connect'];
                    }elseif($tasks->approval_type == 'FB'){ 
                        $approval_id = $connectdetails['finance_connect'];
                    }elseif($tasks->approval_type == 'PB'){
                        $approval_id = $connectdetails['project_connect'];
                    }elseif($tasks->approval_type == 'OB'){
                        $approval_id =$task->approval_other_id;
                    }else{
                        $approval_id = $connectdetails['hr_connect'];
                    }
                    // if($tasks->on_the_day !=1){
                    //     if($tasks->action_frequency == 'DY'){   $day = $tasks->on_the_day !=""?$tasks->on_the_day:0;  }
                    //     if($tasks->action_frequency == 'WK'){   $day = 7;   }
                    //     if($tasks->action_frequency == 'MON'){  $day = 30;  }
                    //     if($tasks->action_frequency == ''){  $day = 0;  }
                    //     if($tasks->before_or_after == 'BF'){ $day = "-".$day; }else{ $day = "+".$day; }
                    //     echo $tasks->action_frequency." -- ".$employees->joining_date." ---  ".$day;
                    //     $days_ago = date('Y-m-d', strtotime($day.' days', strtotime($employees->joining_date)));
                    // }
                   // echo $days_ago." ---  ".$enddate;
                    $task_array = array('company_id'=>$get['company_id'],'transaction_table'=>'employees','transaction_id'=>$get['employee_id'],'task_id'=>$tasks->id,'assigned_to'=>$employees->user_id,'action_date'=>$days_ago,'action_flag'=>'0', 'check_list'=>$tasks->check_list,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'approval_user_id'=>$approval_id,'task_name'=>$tasks->task_name,'process_id'=>$tasks->process_id);

                    // if($get['submit'] != 'Y'){
                    //     $task_array['process_id'] = $tasks->process_id; ///extrea 
                    // }
                    $taslarraylist[] = $task_array;
                    if($get['submit'] == 'Y'){
                       // unset($task_array['task_name']);
                        $res = $callConfig->insertRecord('transaction_activity_schedule',$task_array);
                        $return['transaction_activity_schedule_insert'] = $res;
                    }
                }
            }
            $return['assign_task_list'] = $taslarraylist;
            if($get['submit'] == 'Y'){
                $notiarray = array('notification_message'=>'All Tax Task Assign '.$employees->display_name,'notification_users'=>array('0'=>$get['user_id'],'1'=>$employees->user_id),'id'=>$employees->id,'application_table_name'=>'transaction_activity_schedule','company_id'=>$get['company_id'],'option'=>'COM_VIEW_ONBOARDING_EMPLOYEE_DASHBOARDS','notification_datetime'=>$days_ago,'type'=>'INFORMATION');
                $this->tosendnotifiations($notiarray);
            }
        }elseif($get['dashboard_type'] != "HR"){
            if($get['admin_type']!='1'){
                $comdition = " AND tas.assigned_to = '".$get['user_id']."'";
            }
            $taskactivitysql = "SELECT tas.id,tas.action_date,tas.revised_action_date, tas.actual_action_date, tas.action_flag, tas.check_list, tas.current_status, tas.process_id, tas.task_name FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'employees'  AND transaction_id= '".$get['employee_id']."' ".$comdition." AND tas.is_active = 0";
            $taskactivityres = $callConfig->getAllRows($taskactivitysql);
            $return['sql'] = $taskactivitysql;
            $return['transaction_activity_schedule'] = $taskactivityres;
            $tasks_cntssql = "SELECT count(*) as total_task_cnt, sum(if(current_status=".ST_APPROVED.",1,0)) as approve_cnt, sum(if(current_status=".ST_COMPLETE.",1,0)) as review_cnt, sum(if(current_status=".ST_INPROGRESS.",1,0)) as inprogress_cnt, sum(if(current_status=".ST_ACTIVE.",1,0)) as yet_to_start_cnt FROM transaction_activity_schedule tas WHERE  tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'employees'  AND transaction_id= '".$get['employee_id']."' ".$comdition." AND tas.is_active = 0";
            $tasks_cntsres = $callConfig->getRow($tasks_cntssql);   
            $return['task_cnt'] = $tasks_cntsres;
            $employeesql = "SELECT * FROM employees WHERE company_id = '".$get['company_id']."' AND id= '".$get['employee_id']."' AND is_active = 0";
            $employeeres = $callConfig->getRow($employeesql);   
            $return['employees'] = $employeeres;
        }else{  
            $employeesql = "SELECT emp.id, au.id as user_id, emp.emp_code,  emp.display_name, emp.email_id, emp.joining_date FROM application_users au, employees emp WHERE au.company_id = emp.company_id AND au.company_id = '".$get['company_id']."' AND au.role_id LIKE '".'%"'.EMPLOYEE_ONBOARDING.'"%'."' AND au.id = emp.user_id AND au.is_active = emp.is_active AND au.is_active = 0";
            $employeeres = $callConfig->getAllRows($employeesql);   
            $onboarduserid = array();
            $onboardempid = array();
            foreach($employeeres as $empres){
                $onboarduserid[$empres->user_id] = $empres->user_id;
                $onboardempid[$empres->id] = $empres->id;
            }
            $employeecntsql = "SELECT MONTHNAME(emp.joining_date) as month, count(emp.id) as cnt FROM application_users au, employees emp WHERE au.company_id = emp.company_id AND au.company_id = '".$get['company_id']."' AND au.role_id LIKE '".'%"'.EMPLOYEE_ONBOARDING.'"%'."' AND emp.joining_date >='".$startdate."' AND emp.joining_date <= '".$enddate."' AND au.id = emp.user_id AND au.is_active = emp.is_active AND au.is_active = 0 group by month(emp.joining_date)";
            $employeecntres = $callConfig->getAllRows($employeecntsql);
            foreach($employeecntres as $cntn){
                $employeecnt[$cntn->month] = $cntn->cnt;
            }
            $return['monthlycnt'] = $employeecnt;
            $tasks_cntssql = "SELECT assigned_to, transaction_id, count(*) as total_task_cnt, sum(if(current_status=".ST_APPROVED.",1,0)) as approve_cnt, sum(if(current_status=".ST_COMPLETE.",1,0)) as review_cnt, sum(if(current_status=".ST_INPROGRESS.",1,0)) as inprogress_cnt, sum(if(current_status=".ST_ACTIVE.",1,0)) as yet_to_start_cnt, sum(if(action_date < completion_date,1,0)) as delayed_cnt FROM transaction_activity_schedule tas WHERE  tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'employees'  AND transaction_id IN(".implode(',', $onboardempid).") AND tas.assigned_to IN(".implode(',', $onboarduserid).") AND tas.is_active = 0 GROUP BY tas.assigned_to";
            $tasks_cntsres = $callConfig->getAllRows($tasks_cntssql);
            $return['tasks_cntssql'] = $tasks_cntssql;
            $return['tasks_result'] = $tasks_cntsres;
            $return['onboarduserid'] = $onboarduserid;
            $return['onboard_employees'] = $employeeres;

        }
        $tasks_categoriessql = "SELECT id,category_name FROM tasks_categories where company_id = '".$get['company_id']."' AND is_active = 0";
        $tasks_categoriesres = $callConfig->getAllRows($tasks_categoriessql);   
        $return['tasks_categories'] = $tasks_categoriesres; 
        //print_r($return);
        return $return;  
    }
    function assigntasktoemployeesmanagement($get, $post){
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $return['Post'] = $post;
        //$finance_tax_year = $get['finance_tax_year'];
        $get['taskassign'] = "Y";
        $get['submit'] = "Y";
        $get['finance_tax_year'] = $post['finance_tax_year'];
        $check_emp_id = explode(",",$post['check_emp_id']);
        //$check_emp_id = $post['check_emp_id'];
        foreach($check_emp_id as $empid){ 
            $get['employee_id'] = $empid;
            $return['res'][] = $this->getEMTaskAssigneList($get);
        }
        return $return;  
    }
    function getEMTaskSendForApprove($get){
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $finance_tax_year = $get['finance_tax_year'];
         $transaction_id = $get['transaction_id'];
        if($finance_tax_year !=""){
            $startdate =date($finance_tax_year."-04-01");
            $enddate =date(($finance_tax_year+1)."-03-31");
        }
        $taskactivitysql = "SELECT tas.id,tas.action_date,tas.revised_action_date, tas.actual_action_date, tas.action_flag, tas.check_list, tas.current_status, tas.process_id, tas.task_name, tas.approval_user_id,  tas.assigned_to  FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'employees' AND transaction_id= '".$transaction_id."' AND tas.is_active = 0 AND tas.action_date >='".$startdate." 00:00:00' AND tas.action_date <='".$enddate." 23:59:59'";
        $taskactivityres = $callConfig->getAllRows($taskactivitysql);
        $return['sql'] = $taskactivitysql;
        $return['transaction_activity_schedule'] = $taskactivityres;
        return $return;   
    }
    function updateSendVerificationDocuments($get,$post){
        global $callConfig;
        $return = array();
        $transaction_table = $post['transaction_table'];
        $transaction_id = $post['transaction_id'];
        //Start All task Sedn to Approve
        $tasquery = "SELECT id FROM `transaction_activity_schedule` WHERE company_id = '".$post['company_id']."' AND transaction_table = '".$transaction_table."' AND assigned_to = '".$post['user_id']."' AND transaction_id = '".$transaction_id."' AND current_status = '".ST_INPROGRESS."' AND is_active = 0";
        $tasres = $callConfig->getAllRows($tasquery);
        $i = 0;
        foreach($tasres as $tas){
            $post['transaction_table'] = 'transaction_activity_schedule';
            $post['current_status'] = ST_INPROGRESS;
            $post['transaction_no'] = $tas->id;
            $post['next_status'] = ST_APPROVED1;
            $post['comment'] = 'Please Approve';
            if($i != 0){
                $post['requester_send'] = "N";
            }
            $this->submitStatusDynamicWorkFlow($post);
            $i++;
        }
        //End All task Sedn to Approve
        return $post;
    }
    /**
    function updateOnBoardingTaskStatus($post){
        global $callConfig;
        $return = array();
        $return['get'] = $post;
        $taskactivitysql = "SELECT tas.task_name, tas.assigned_to, tas.transaction_id, tas.approval_user_id, tas.transaction_table FROM transaction_activity_schedule tas WHERE tas.company_id = '".$post['company_id']."' AND tas.transaction_table = '".$post['transaction_action_table']."' AND tas.id= '".$post['transaction_id']."' AND tas.is_active = 0";
        $taskactivityres = $callConfig->getRow($taskactivitysql);
        $return['taskactivityres'] = $taskactivitysql;
        $updatearray = array('current_status'=>$post['status'],'comments'=>$post['comment']);
        if($post['status'] == ST_APPROVED){ $updatearray['approval_date'] = date('Y-m-d H:i:s'); }
        $return['res'] = $callConfig->updateRecord('transaction_activity_schedule',$updatearray,'id',$post['transaction_id']);
        if($post['status'] == ST_APPROVED){
            $approvalmsg = $taskactivityres->task_name.' Task Successfully Approved';
        }else{
            $approvalmsg = $taskactivityres->task_name.' Task Is Rejetced';
        }
        $notiarray = array('notification_message'=>$approvalmsg,'notification_users'=>array('0'=>$taskactivityres->approval_user_id),'id'=>$taskactivityres->transaction_id,'application_table_name'=>'transaction_activity_schedule','company_id'=>$post['company_id'],'option'=>'COM_VIEW_ONBOARDING_EMPLOYEE_DASHBOARDS','notification_datetime'=>date('Y-m-d H:i'),'type'=>'INFORMATION');
        if($taskactivityres->transaction_table == "vendor_registers"){
            $notiarray['option'] = 'COM_INSERT_VENDORS&VTYPE=VR'; 
        }
        $return['notiarray'] = $notiarray;
        $this->tosendnotifiations($notiarray);
        
        if(isset($taskactivityres->assigned_to)){
            $notiarray['notification_users'] = array("0"=>$taskactivityres->assigned_to);
            $notiarray['type'] = "ACTION";
            $this->tosendnotifiations($notiarray);
        }
        
        return $return;
    }       
//End marge user class -- 2023-08-25
    function getVendorsDetails($get){
        global $callConfig;
        $return = array();
        $return['get']= $get;
        $vendorslistsql = "SELECT id,display_name FROM vendors WHERE company_id = '".$get['company_id']."' AND is_active = 0";
        $vendorslistres = $callConfig->getAllRows($vendorslistsql);
        $return['vendorslist'] = $vendorslistres;

        $vendorssql = "SELECT * FROM vendors WHERE company_id = '".$get['company_id']."' AND id = '".$get['id']."' AND is_active = 0";
        $vendorres = $callConfig->getRow($vendorssql);
        $return['vendors'] = $vendorres;
        if($get['transaction_table'] =="vendor_registers"){
            if($get['id'] !=""){
                $vendregcond= " AND id = '".$get['id']."' ";
            }else{
                $vendregcond= " AND user_id = '".$get['user_id']."' ";
            }
            $vendor_registerssql = "SELECT * FROM vendor_registers WHERE company_id = '".$get['company_id']."'  AND is_active = 0 ".$vendregcond;
            $vendorres = $callConfig->getRow($vendor_registerssql);

            $vendorssql = "SELECT id FROM vendors WHERE company_id = '".$get['company_id']."' AND email = '".$vendorres->email."' AND is_active = 0";
            $venid = $callConfig->getRow($vendorssql);
            $vendorres->created_vendor_id  =$venid->id; 
            $return['vendor_registers'] = $vendorres;
            $return['vendor_registers_sql'] = $vendor_registerssql;

            // if($get['admin_type']!='1'){
            //  $comdition = " AND tas.assigned_to = '".$get['user_id']."'";
            // }
            $taskactivitysql = "SELECT tas.id,tas.action_date,tas.revised_action_date, tas.actual_action_date, tas.action_flag, tas.check_list, tas.current_status, tas.process_id, tas.task_name, tas.approval_user_id,  tas.assigned_to  FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'vendor_registers' AND transaction_id= '".$vendorres->id."' ".$comdition." AND tas.is_active = 0";
            $taskactivityres = $callConfig->getAllRows($taskactivitysql);
            $return['sql'] = $taskactivitysql;
            $return['transaction_activity_schedule'] = $taskactivityres;
            
            $tasks_cntssql = "SELECT count(*) as total_task_cnt, sum(if(current_status=".ST_APPROVED.",1,0)) as approve_cnt, sum(if(current_status=".ST_COMPLETE.",1,0)) as review_cnt, sum(if(current_status=".ST_INPROGRESS.",1,0)) as inprogress_cnt, sum(if(current_status=".ST_ACTIVE.",1,0)) as yet_to_start_cnt FROM transaction_activity_schedule tas WHERE  tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'vendor_registers'  AND transaction_id= '".$vendor_registersres->id."'  ".$comdition." AND tas.is_active = 0";
            $tasks_cntsres = $callConfig->getRow($tasks_cntssql);   
            $return['task_cnt'] = $tasks_cntsres;
        }
        $vendor_agreementssql = "SELECT * FROM vendor_agreements WHERE company_id = '".$get['company_id']."' AND vendor_id = '".$vendorres->id."' AND is_active = 0";
        $vendor_agreementsres = $callConfig->getAllRows($vendor_agreementssql);
        $return['vendor_agreements'] = $vendor_agreementsres;

        $paymentmethodsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'PAYMETHOD' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $paymentmethodres=$callConfig->getAllRows($paymentmethodsql);
        foreach($paymentmethodres as $pm){
            $paymentmethod[$pm->genc_detail_code] = $pm->genc_detail_name;
        }
        $return['payment_method'] = $paymentmethod;

        $salestypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'SALESTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $salestyperes=$callConfig->getAllRows($salestypesql);
        foreach($salestyperes as $st){
            $salestype[$st->genc_detail_code] = $st->genc_detail_name;
        }
        $return['salestype'] = $salestype;

        $paymenttermsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'PAYMETERM' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $paymenttermres=$callConfig->getAllRows($paymenttermsql);
        foreach($paymenttermres as $pt){
            $pmtype[$pt->genc_detail_code] = $pt->genc_detail_name;
        }
        $return['paymenttype'] = $pmtype;

        $empsql = "SELECT 'display_name','email_id','billable','transaction_number' FROM employess WHERE company_id = '".$get['company_id']."' AND vendor_id = '".$get['id']."' AND is_active = 0";
        $empres = $callConfig->getAllRows($empsql);
        $return['employess'] = $empres;

        $whrcat ="company_id = '".$get['company_id']."' AND is_active=0";
        $query=$callConfig->selectQuery('countries','id,country_name',$whrcat,'','','');
        $components6= (array)$callConfig->getAllRows($query);
        $return['countries'] = (array)$components6;

        $statessql = "SELECT id, state_name FROM `states` WHERE company_id = '".$get['company_id']."' AND (country_id = '".$vendorres->billing_country."' || country_id = '".$vendorres->shipping_country."' || country_id = '".$vendorres->bank_country."') AND is_active=0";
        $statesres = $callConfig->getAllRows($statessql);
        $return['states'] = $statesres;

        $citiessql = "SELECT id, city_name FROM `cities` WHERE company_id = '".$get['company_id']."' AND (state_id = '".$vendorres->billing_state."' || state_id = '".$vendorres->shipping_state."' || state_id = '".$vendorres->bank_state."') AND is_active=0";
        $citiesres = $callConfig->getAllRows($citiessql);
        $return['cities'] = $citiesres;

        $tasks_categoriessql = "SELECT id,category_name FROM tasks_categories where company_id = '".$get['company_id']."' AND is_active = 0";
        $tasks_categoriesres = $callConfig->getAllRows($tasks_categoriessql);   
        $return['tasks_categories'] = $tasks_categoriesres; 

        $vendortypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'VENDORTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $vendortyperes=$callConfig->getAllRows($vendortypesql);
        foreach($vendortyperes as $vt){
            $vendor_type[$vt->genc_detail_code] = $vt->genc_detail_name;
        }
        $return['vendor_type'] = $vendor_type;

        $vcitsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'VENDORCONTACTINFOTYP' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $vcitres=$callConfig->getAllRows($vcitsql);
        foreach($vcitres as $pm){
            $vendorcontactinfotype[$pm->genc_detail_code] = $pm->genc_detail_name;
        }
        $return['vendor_contact_info_type'] = $vendorcontactinfotype;

        $company_typessql = "select id,company_type_name FROM company_types WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
        $company_typesres = $callConfig->getAllRows($company_typessql);
        $return['company_types'] = $company_typesres;   

        $incotermssql = "select id,incoterm_name FROM incoterms WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
        $incotermsres = $callConfig->getAllRows($incotermssql);
        $return['incoterms'] = $incotermsres;   

        $payment_termssql = "SELECT id, payment_term_name FROM payment_terms WHERE company_id='".$get['company_id']."' AND is_active=0"; 
        $payment_termsres = $callConfig->getAllRows($payment_termssql);
        $return['payment_terms'] = $payment_termsres;

        $batypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'BANKACCTTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $batyperes=$callConfig->getAllRows($batypesql);
        foreach($batyperes as $pm){
            $bankacoounttype[$pm->genc_detail_code] = $pm->genc_detail_name;
        }
        $return['bank_acoount_type'] = $bankacoounttype;

        $tasquery = "SELECT COUNT(id) as total_task, SUM(if(current_status = '".ST_APPROVED."',1,0)) as total_approved  FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."' AND approval_user_id = '".$get['user_id']."' AND is_active = 0";
        $tasres = $callConfig->getRow($tasquery);
        $return['transaction_activity_schedule_cnt'] = $tasres;

        $tasquery = "SELECT id, task_name, check_list, current_status, transaction_table, assigned_to FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."' AND is_active = 0";
        $tasres = $callConfig->getAllRows($tasquery);
        $return['transaction_activity_schedule_list'] = $tasres;

        if($get['edittype'] == "SendVerificationProfile"){
            $tasquery = "SELECT * FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."' AND is_active = 0";
            $tasres = $callConfig->getAllRows($tasquery);
            $return['transaction_activity_schedule'] = $tasres;
        }

        $spihitsql = "SELECT * FROM api_hits WHERE company_id = '".$get['company_id']."' AND application_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."'  AND is_active = 0 ORDER BY response_date_time DESC";
        $spihitres=$callConfig->getAllRows($spihitsql);
        $return['api_hits'] = $spihitres;

        $vendor_display_fieldssql = "SELECT id, field_list FROM vendor_display_fields WHERE company_id = '".$get['company_id']."' AND role_short_code = '".$get['working_role_short_code']."' AND is_active = 0";
        $vendor_display_fieldsres=$callConfig->getRow($vendor_display_fieldssql);
        $return['vendor_display_fields'] = $vendor_display_fieldsres;
        

        return $return;
    }

    function getVendorsDetails($get){
        global $callConfig;
        $return = array();
        $return['get']= $get;
        $vendorslistsql = "SELECT id,display_name FROM vendors WHERE company_id = '".$get['company_id']."' AND is_active = 0";
        $vendorslistres = $callConfig->getAllRows($vendorslistsql);
        $return['vendorslist'] = $vendorslistres;

        $vendorssql = "SELECT * FROM vendors WHERE company_id = '".$get['company_id']."' AND id = '".$get['id']."' AND is_active = 0";
        $vendorres = $callConfig->getRow($vendorssql);
        $return['vendors'] = $vendorres;
        if($get['transaction_table'] =="vendor_registers"){
            if($get['id'] !=""){
                $vendregcond= " AND id = '".$get['id']."' ";
            }else{
                $vendregcond= " AND user_id = '".$get['user_id']."' ";
            }
            $vendor_registerssql = "SELECT * FROM vendor_registers WHERE company_id = '".$get['company_id']."'  AND is_active = 0 ".$vendregcond;
            $vendorres = $callConfig->getRow($vendor_registerssql);

            $vendorssql = "SELECT id FROM vendors WHERE company_id = '".$get['company_id']."' AND email = '".$vendorres->email."' AND is_active = 0";
            $venid = $callConfig->getRow($vendorssql);
            $vendorres->created_vendor_id  =$venid->id; 
            $return['vendor_registers'] = $vendorres;
            $return['vendor_registers_sql'] = $vendor_registerssql;

            // if($get['admin_type']!='1'){
            //  $comdition = " AND tas.assigned_to = '".$get['user_id']."'";
            // }
            $taskactivitysql = "SELECT tas.id,tas.action_date,tas.revised_action_date, tas.actual_action_date, tas.action_flag, tas.check_list, tas.current_status, tas.process_id, tas.task_name, tas.approval_user_id,  tas.assigned_to  FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'vendor_registers' AND transaction_id= '".$vendorres->id."' ".$comdition." AND tas.is_active = 0";
            $taskactivityres = $callConfig->getAllRows($taskactivitysql);
            $return['sql'] = $taskactivitysql;
            $return['transaction_activity_schedule'] = $taskactivityres;
            
            $tasks_cntssql = "SELECT count(*) as total_task_cnt, sum(if(current_status=".ST_APPROVED.",1,0)) as approve_cnt, sum(if(current_status=".ST_COMPLETE.",1,0)) as review_cnt, sum(if(current_status=".ST_INPROGRESS.",1,0)) as inprogress_cnt, sum(if(current_status=".ST_ACTIVE.",1,0)) as yet_to_start_cnt FROM transaction_activity_schedule tas WHERE  tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'vendor_registers'  AND transaction_id= '".$vendor_registersres->id."'  ".$comdition." AND tas.is_active = 0";
            $tasks_cntsres = $callConfig->getRow($tasks_cntssql);   
            $return['task_cnt'] = $tasks_cntsres;
        }
        $vendor_agreementssql = "SELECT * FROM vendor_agreements WHERE company_id = '".$get['company_id']."' AND vendor_id = '".$vendorres->id."' AND is_active = 0";
        $vendor_agreementsres = $callConfig->getAllRows($vendor_agreementssql);
        $return['vendor_agreements'] = $vendor_agreementsres;

        $paymentmethodsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'PAYMETHOD' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $paymentmethodres=$callConfig->getAllRows($paymentmethodsql);
        foreach($paymentmethodres as $pm){
            $paymentmethod[$pm->genc_detail_code] = $pm->genc_detail_name;
        }
        $return['payment_method'] = $paymentmethod;

        $salestypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'SALESTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $salestyperes=$callConfig->getAllRows($salestypesql);
        foreach($salestyperes as $st){
            $salestype[$st->genc_detail_code] = $st->genc_detail_name;
        }
        $return['salestype'] = $salestype;

        $paymenttermsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'PAYMETERM' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $paymenttermres=$callConfig->getAllRows($paymenttermsql);
        foreach($paymenttermres as $pt){
            $pmtype[$pt->genc_detail_code] = $pt->genc_detail_name;
        }
        $return['paymenttype'] = $pmtype;

        $empsql = "SELECT 'display_name','email_id','billable','transaction_number' FROM employess WHERE company_id = '".$get['company_id']."' AND vendor_id = '".$get['id']."' AND is_active = 0";
        $empres = $callConfig->getAllRows($empsql);
        $return['employess'] = $empres;

        $whrcat ="company_id = '".$get['company_id']."' AND is_active=0";
        $query=$callConfig->selectQuery('countries','id,country_name',$whrcat,'','','');
        $components6= (array)$callConfig->getAllRows($query);
        $return['countries'] = (array)$components6;

        $statessql = "SELECT id, state_name FROM `states` WHERE company_id = '".$get['company_id']."' AND (country_id = '".$vendorres->billing_country."' || country_id = '".$vendorres->shipping_country."' || country_id = '".$vendorres->bank_country."') AND is_active=0";
        $statesres = $callConfig->getAllRows($statessql);
        $return['states'] = $statesres;

        $citiessql = "SELECT id, city_name FROM `cities` WHERE company_id = '".$get['company_id']."' AND (state_id = '".$vendorres->billing_state."' || state_id = '".$vendorres->shipping_state."' || state_id = '".$vendorres->bank_state."') AND is_active=0";
        $citiesres = $callConfig->getAllRows($citiessql);
        $return['cities'] = $citiesres;

        $tasks_categoriessql = "SELECT id,category_name FROM tasks_categories where company_id = '".$get['company_id']."' AND is_active = 0";
        $tasks_categoriesres = $callConfig->getAllRows($tasks_categoriessql);   
        $return['tasks_categories'] = $tasks_categoriesres; 

        $vendortypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'VENDORTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $vendortyperes=$callConfig->getAllRows($vendortypesql);
        foreach($vendortyperes as $vt){
            $vendor_type[$vt->genc_detail_code] = $vt->genc_detail_name;
        }
        $return['vendor_type'] = $vendor_type;

        $vcitsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'VENDORCONTACTINFOTYP' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $vcitres=$callConfig->getAllRows($vcitsql);
        foreach($vcitres as $pm){
            $vendorcontactinfotype[$pm->genc_detail_code] = $pm->genc_detail_name;
        }
        $return['vendor_contact_info_type'] = $vendorcontactinfotype;

        $company_typessql = "select id,company_type_name FROM company_types WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
        $company_typesres = $callConfig->getAllRows($company_typessql);
        $return['company_types'] = $company_typesres;   

        $incotermssql = "select id,incoterm_name FROM incoterms WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
        $incotermsres = $callConfig->getAllRows($incotermssql);
        $return['incoterms'] = $incotermsres;   

        $payment_termssql = "SELECT id, payment_term_name FROM payment_terms WHERE company_id='".$get['company_id']."' AND is_active=0"; 
        $payment_termsres = $callConfig->getAllRows($payment_termssql);
        $return['payment_terms'] = $payment_termsres;

        $batypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'BANKACCTTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $batyperes=$callConfig->getAllRows($batypesql);
        foreach($batyperes as $pm){
            $bankacoounttype[$pm->genc_detail_code] = $pm->genc_detail_name;
        }
        $return['bank_acoount_type'] = $bankacoounttype;

        $tasquery = "SELECT COUNT(id) as total_task, SUM(if(current_status = '".ST_APPROVED."',1,0)) as total_approved  FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."' AND approval_user_id = '".$get['user_id']."' AND is_active = 0";
        $tasres = $callConfig->getRow($tasquery);
        $return['transaction_activity_schedule_cnt'] = $tasres;

        $tasquery = "SELECT id, task_name, check_list, current_status, transaction_table, assigned_to FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."' AND is_active = 0";
        $tasres = $callConfig->getAllRows($tasquery);
        $return['transaction_activity_schedule_list'] = $tasres;

        if($get['edittype'] == "SendVerificationProfile"){
            $tasquery = "SELECT * FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."' AND is_active = 0";
            $tasres = $callConfig->getAllRows($tasquery);
            $return['transaction_activity_schedule'] = $tasres;
        }

        $spihitsql = "SELECT * FROM api_hits WHERE company_id = '".$get['company_id']."' AND application_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."'  AND is_active = 0 ORDER BY response_date_time DESC";
        $spihitres=$callConfig->getAllRows($spihitsql);
        $return['api_hits'] = $spihitres;

        $vendor_display_fieldssql = "SELECT id, field_list FROM vendor_display_fields WHERE company_id = '".$get['company_id']."' AND role_short_code = '".$get['working_role_short_code']."' AND is_active = 0";
        $vendor_display_fieldsres=$callConfig->getRow($vendor_display_fieldssql);
        $return['vendor_display_fields'] = $vendor_display_fieldsres;
        

        return $return;
    } **/


}

?>