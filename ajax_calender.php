<?php 
include('includes/session.php');
//print_r($_SESSION); exit;	
include('dbconfig.php');
include('comm.php');
	// echo $_GET['category'];
	$categories = explode('-', $_GET['category']);
    $data11["where"] = array('company_id'=> $_SESSION['company_id']);
	$datw = urlencode(json_encode($data11));
	$curl1 = API."?mode=gettabledetailswithconditions&table=event_categories&data=".$datw; 
	$cudata1 = callAPI($curl1);
	$result = json_decode($cudata1,1);
	foreach ($result as $key => $cat) {
		$cate[$cat['id']]['category_name'] = $cat['category_name'];
		$cate[$cat['id']]['color'] = $cat['display_color'];
		$cate[$cat['id']]['bgcolor'] = $cat['display_background_color'];
		$cate[$cat['id']]['auto_manual'] = $cat['auto_manual']; // work flow or not
	}
	//print_r($cate);
	if(in_array('1', $categories)){ 
	    $category =1;
		//$_SESSION['emp_id'] =1;
		$arr = array();
		$empatt["where"] = array('company_id'=> $_SESSION['company_id'],'employee_id'=>$_SESSION['emp_id'],'is_active'=>'0');
		$empattcon = urlencode(json_encode($empatt));
		$empatturl = API."?mode=gettabledetailswithconditions&table=employee_daily_attendances&data=".$empattcon; 
		//echo $empatturl;
		$empattdata = callAPI($empatturl);
		//echo $empattdata; exit;
		$empattdata = json_decode($empattdata,1);
		//print_r($empattdata);
		foreach ($empattdata as  $empat) {
		    $arr = array();
			$arr['title'] = "Present";
			$arr['start'] = $empat['attendace_date']."T00:00:00";
		//	$arr['start'] = $empat['attendace_date'];
			$arr['end'] = $empat['attendace_date'];
			$arr['constraint'] = "Attendance";	
			//$arr['ispopup'] = 1;
		    //$arr['idvalue'] = 1;
	    	//$arr['popup_function'] = "popupempkpitask";
			$arr['backgroundColor'] = strtoupper($cate[$category]['bgcolor'])=="INFO"?'#00bcd4':strtoupper($cate[$category]['bgcolor']);
			$array_list[] = $arr;
		}

	}
	/*if(in_array('51', $categories)){ 
	    $category =1;
		//$_SESSION['emp_id'] =1;
		$arr = array();
		$empatt["where"] = array('company_id'=> $_SESSION['company_id'],'employee_id'=>$_SESSION['emp_id'],'is_active'=>'0');
		$empattcon = urlencode(json_encode($empatt));
		$empatturl = API."?mode=togetteammeberattdences"; 
		//echo $empatturl;
		$empattdata = callAPI($empatturl);
		//echo $empattdata; exit;
		$empattdata = json_decode($empattdata,1);
		//print_r($empattdata);
		foreach ($empattdata as  $empat) {
		    $arr = array();
			$arr['title'] = $empat['display_name']"-Present";
			$arr['start'] = $empat['attendace_date'];
			$arr['end'] = $empat['attendace_date'];
			$arr['description'] = "Colck-In:".$empat['clock_in'].",Clock-Out:".$empat['clock_out'];		
			$arr['backgroundColor'] = "fc-event-".strtoupper($cate[$category]['color'])." fc-event-solid-".strtoupper($cate[$category]['bgcolor']);
			$array_list[] = $arr;
		}

	}*/

	if(in_array('2', $categories)){
		$datass["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0,'user_id'=>$_SESSION['userid']);
		$datasw = urlencode(json_encode($datass));
		$curlss = API."?mode=gettabledetailswithconditions&table=employee_events&data=".$datasw; 
		$spdata = callAPI($curlss);
		$spdata = json_decode($spdata,1);
		foreach ($spdata as  $value) {
		    //print_r($value);
		    $arr = array();
			$start_date = ""; $end_date ="";
			$start_date = $value['start_date'].'T'.$value['start_time'];
			//if($value['start_time']!="00:00:00"){ $start_date.='T'.$value['start_time'];}
			$end_date = $value['end_date'];
			if($value['end_time']!="00:00:00"){ $end_date.='T'.$value['end_time'];}
			$arr['id'] = $value['id'];
			$arr['title'] = $cate[$value['event_category_id']]['category_name'];
			$arr['start'] = $start_date;
			$arr['end'] = $end_date;
			if($value['event_url'] !=""){
				$arr['url'] = $value['event_url'];
			}
			$arr['constraint'] = $value['event_name'];
			$arr['backgroundColor'] = strtoupper($cate[$category]['bgcolor']);
			$array_list[] = $arr;
		}
	}

	/***  Notification Start *****/
	if(in_array('5', $categories)){
	$notdata = toGetNotigficationListByUsingUserId($_SESSION['userid'],'all');
	$arr = array();
	foreach ($notdata as  $not) {
		$arr['title'] = $not['Notification_Message'];
		$arr['start'] = date('Y-m-d',strtotime($not['Notification_DateTime']))."T00:00:00";
		if($not['option'] !=""){
			$nrid = "id:".$value['option_id'];
			$link = "index.php?option=".$value['option']."&Rid=". deocdeId($nrid)."&noti_id=".deocdeId($value['id']);
			$arr['url'] = $link;
		}
		$arr['constraint'] = $not['Notification_Message'];
		if($not['Notification_Read'] ==1){
			
			$arr['backgroundColor'] = SUCCESS;
		}else{
			$arr['backgroundColor'] = DANGER;
		}
		$array_list[] = $arr;
	}
	}
	/***  Notification End *****/
 	//print_r($array_list);
	/***  Transaction Approval Log Start *****/
	if(in_array('3', $categories)){
	$arr = array(); //print_r($_SESSION['application_pages']);
	foreach ($_SESSION['application_pages'] as $app) {
		$table_insert[$app['application_table_name']] = $app['insert_page_name'];
	}
	$tranwr["where"] = array('company_id'=> $_SESSION['company_id'],'user_id'=>$_SESSION['userid']);
	$trancon = urlencode(json_encode($tranwr));
	$tranurl = API."?mode=gettabledetailswithconditions&table=transaction_approval_logs&data=".$trancon; 
	$trndata = callAPI($tranurl);
	$trndata = json_decode($trndata,1);
	foreach ($trndata as  $trn) {
		$arr['title'] = viewTitle($trn['transaction_table'])."-".$trn['transaction_number'];
		$arr['start'] = date('Y-m-d',strtotime($trn['request_date']))."T00:00:00";
		if($trn['transaction_id'] !=""){
			$nrid = "id:".$trn['transaction_id'];
			$link = "index.php?option=".$table_insert[$trn['transaction_table']]."&Rid=". deocdeId($nrid);
			$arr['url'] = $link;
		}
		$arr['constraint'] = $arr['title'];
		if($trn['response_type'] !=0){
			
		$arr['backgroundColor'] = SUCCESS;
		}else{
			$arr['backgroundColor'] = DANGER;
		}
		$array_list[] = $arr;
	}
	}
	//echo $category; print_r($arr);
	/***  Transaction Approval Log End *****/

	/***  Employee Task Start *****/
	if(in_array('4', $categories)){
	$arr = array();
	$empwr["where"] = array('company_id'=> $_SESSION['company_id'],'emp_id'=>$_SESSION['userid']);
	$empcon = urlencode(json_encode($empwr));
	$empurl = API."?mode=gettabledetailswithconditions&table=employee_kpi_task_histories&data=".$empcon; 
	$empdata = callAPI($empurl);
	$empdata = json_decode($empdata,1);
	foreach ($empdata as  $emp) {
		$arr['title'] = $emp['name'];
		$arr['start'] = $emp['task_date']."T00:00:00";
		$arr['constraint'] = $arr['title'];
		if($emp['comments'] !=""){
			
			$arr['backgroundColor'] = SUCCESS;
		}else{
			$arr['backgroundColor'] = DANGER;
		}
		$array_list[] = $arr;
	}
	}

			
	//print_r($arr);
	/***  Employee Task End *****/
/***   LEAVE  Start *****/
	if(in_array('6', $categories)){
        $arr = array();
        //array('company_id'=> $_SESSION['company_id'],'employee_id'=>$_SESSION['emp_id'],'is_active'=>'0');
        $empatturl = API."?mode=getleavecalender&table=employee_leaves&employee_id=".$_SESSION['emp_id'];
        // echo $empatturl;
		$empattdata = callAPI($empatturl);
		$array_lists = json_decode($empattdata,1);
		$array_list = array();
		foreach ($array_lists as  $array_lis){
			$arr['title'] = $array_lis['title'];
			$arr['start'] = $array_lis['start'];
            $arr['end'] = $array_lis['end'];  
            $arr['constraint'] = "LEAVES";   
            if($_SESSION['work_module_setup_id']==19){
            $arr['url'] = "index.php?option=COM_INSERT_VMS_LEAVE_REQUEST&Rid=".deocdeId("id:".$array_lis['id']);
        	}else{
        		 $arr['url'] = "index.php?option=COM_INSERT_LEAVE_REQUEST&Rid=".deocdeId("id:".$array_lis['id']);
        	}
            $array_list[] = $arr;
        }
		
        $arr['url'] = $link;
   //      $empatt["where"] = array('company_id'=> $_SESSION['company_id'],'employee_id'=>$_SESSION['emp_id'],'is_active'=>'0');
   //      $empattcon = urlencode(json_encode($empatt));
   //      $empatturl = API."?mode=gettabledetailswithconditions&table=employee_leaves&data=".$empattcon;
   //      $empattdata = callAPI($empatturl);
   //      $empattdata = json_decode($empattdata,1);
   //      $leave["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
   //      $leavecon = urlencode(json_encode($leave));
   //      $leaveurl = API."?mode=gettabledetailswithconditions&table=leave_types&data=".$leavecon;
   //      $leavedata = callAPI($leaveurl);
   //      $leavetypedata = json_decode($leavedata,1);
   //      foreach ($leavetypedata as $value) {
   //          $leave_data[$value['id']]=$value['back_ground_color'];
   //      }
   //      foreach ($empattdata as  $empat) {
			// $onedayadded  = date('Y-m-d', strtotime('+1 day', strtotime($empat['leave_to'])));
   //          $arr['title'] = "LEAVES";
   //          $arr['start'] = $empat['leave_from']."T00:00:00";
   //          $arr['end'] = $onedayadded;  // because the calendar Is Showing one day less so added one day in a array
   //          $arr['constraint'] = "LEAVES";   
   //          $arr['backgroundColor'] = strtoupper($cate[$category]['bgcolor']);
   //          $array_list[] = $arr;
   //      }
    }
 /*Team leaves */
    /*if(in_array('56', $categories)){
        $arr = array();
        $teamempurl = API."?mode=togetteammeberemployeeids";
        $teamdata = callAPI($teamempurl);
        $teamdata = json_decode($teamdata,1);
        foreach($teamdata as $team){
        	$emps[] = $team['emp_id'];
        }
        $empatt["where_in"]  = $emps;
        $empatt["whr_in_key"] = 'employee_id';
        $empatt["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
        $empattcon = urlencode(json_encode($empatt));
        $empatturl = API."?mode=gettabledetailswithconditions&table=employee_leaves&data=".$empattcon;
        $empattdata = callAPI($empatturl);
        $empattdata = json_decode($empattdata,1);
        $leave["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
        $leavecon = urlencode(json_encode($leave));
        $leaveurl = API."?mode=gettabledetailswithconditions&table=leave_types&data=".$leavecon;
        $leavedata = callAPI($leaveurl);
        $leavetypedata = json_decode($leavedata,1);
        foreach ($leavetypedata as $value) {
            $leave_data[$value['id']]=$value['back_ground_color'];
        }
        foreach ($empattdata as  $empat) {
            $arr['title'] = "LEAVES";
            $arr['start'] = $empat['leave_from'];
            $arr['end'] = $empat['leave_to'];
            $arr['description'] = "LEAVES";   
            $arr['backgroundColor'] = "fc-event-".strtoupper($leave_data[$empat['leave_type_id']])." fc-event-solid-".strtoupper($leave_data[$empat['leave_type_id']]);
            $array_list[] = $arr;
        }
    }*/
    if(in_array('11', $categories)){
		$arr = array();
		$data = json_decode($_GET['data'],1);
		$empatt["where"] = array('company_id'=> $_SESSION['company_id'],'employee_id'=>$data['employee_id'],'is_active'=>'0');
		$empattcon = urlencode(json_encode($empatt));
		$empatturl = API."?mode=gettabledetailswithconditions&table=employee_leaves&data=".$empattcon; 
		//echo $empatturl;
		$empattdata = callAPI($empatturl);
		$empattdata = json_decode($empattdata,1);
		foreach ($empattdata as  $empat) {
			$arr['title'] = "LEAVES";
			$arr['start'] = $empat['leave_from'].'T00:00:00';
			$arr['end'] = $empat['leave_to'].'T23:00:00';
			$arr['constraint'] = "LEAVES";		
			$arr['backgroundColor'] = strtoupper($cate[$category]['bgcolor']);
			$array_list[] = $arr;
		}
	}

	if(in_array('15', $categories)){
        $arr = array();
       // $empatt["where"] = array('company_id'=> $_SESSION['company_id'],'emp_id'=>$_SESSION['emp_id'],'is_active'=>'0');
        //$empattcon = urlencode(json_encode($empatt));
         $empatturl = API."?mode=gettimesheetscalendar&table=project_timesheets&emp_id=".$_SESSION['emp_id'];
        $empattdata = callAPI($empatturl);
        //echo $empattdata; exit;
        $empattdata = json_decode($empattdata,1);
        foreach ($empattdata as  $empat) {
            $arr = array();
            $arr['title'] = "TIMESHEETS";
            $arr['start'] = $empat['timesheet_date']."T00:00:00";
            $arr['end'] = $empat['timesheet_date'];
            $arr['description'] = $empat['description']; 
            $link = "index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=".date('d-m-Y',strtotime($empat['timesheet_date']));
			$arr['url'] = $link;
            if($empat['type']!="0"){
                if($empat['status']>10){
                    $arr['backgroundColor'] = SUCCESS;
                }else{
                    $arr['backgroundColor'] = DANGER;
                }
            }else{
                $arr['backgroundColor'] = SUCCESS;
            }
            $array_list[] = $arr;
        }
    }
	//print_r($arr);
	/***  LEAVE  Task End *****/


	if(in_array('60', $categories)){
		$curlss = API."?mode=getholidayscalendor"; 
		$spdata = callAPI($curlss);
		$spdata = json_decode($spdata,1);
		$holidays = $spdata['holidays'];
        foreach ($holidays as  $holidays) {
			$arr['title'] = $holidays['holiday_name']." - ".$holidays['location_name'];
            $arr['start'] = $holidays['holiday_date']."T00:00:00";
            $arr['end'] = $holidays['holiday_date'];
            $arr['description'] = $holidays['holiday_name']." - ".$holidays['location_name'];
            $link = "index.php?option=COM_INSERT_HOLIDAYS&Rid=".deocdeId("id:".$holidays['id']);
            $arr['url'] = $link;
            $array_list[] = $arr;
        }
	}

	if(in_array('101', $categories)){
		$arr = array();
		$data = json_decode($_GET['data'],1);
		$empatt["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
		$empattcon = urlencode(json_encode($empatt));
		$empatturl = API."?mode=togetleavelstemployeeajax"; 
		//echo $empatturl;
		$empattdata = callAPI($empatturl);
		$empattdata = json_decode($empattdata,1);
		foreach ($empattdata as  $empat) {
			$arr['title'] = $empat['display_name'];
			$arr['start'] = $empat['leave_from'].'T00:00:00';
			$arr['end'] = $empat['leave_to'].'T23:00:00';
			$arr['constraint'] = "LEAVES";		
			$arr['backgroundColor'] = strtoupper($empat['back_ground_color']);
			$arr['Color'] = strtoupper('#FFFFFF');
			$array_list[] = $arr;
		}
	}
	if(in_array('28', $categories)){
		$arr = array();
		$data = json_decode($_GET['data'],1);
		$empatt["where"] = array('company_id'=> $_SESSION['company_id'],'emp_id'=>$_SESSION['emp_id'],'is_active'=>'0');
		$empattcon = urlencode(json_encode($empatt));
		$empatturl = API."?mode=gettabledetailswithconditions&table=request_work_from_home&data=".$empattcon; 
		//echo $empatturl;
		$empattdata = callAPI($empatturl);
		$empattdata = json_decode($empattdata,1);
		foreach ($empattdata as  $empat) {
			$arr['title'] = "Work From Home";
			$arr['start'] = $empat['effective_from_date'].'T00:00:00';
			$arr['end'] = $empat['effective_to_date'].'T23:00:00';
			$arr['constraint'] = "LEAVES";		
			$arr['backgroundColor'] = strtoupper($cate[$category]['bgcolor']);
			$link = "index.php?option=COM_INSERT_REQUEST_WORK_FROM_HOME&Rid=".deocdeId("id:".$empat['id']);
            $arr['url'] = $link;
			$array_list[] = $arr;
		}
	}
	header('Content-Type: application/json');
	echo json_encode($array_list); exit;
?>