<?php
ini_set("display_errors",0);
include('includes/session.php');
include('dbconfig.php');
include('comm.php');
$transaction_table = $_GET['table']; 
$array = array();
if($transaction_table!=""){
	$filtercondition = "";
	$dynamictableurl = API."?mode=getdynamictabledetails&application_table_name=".$transaction_table."&curr_status=".$_GET['current_status']."&status=".$_GET['status']."&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&vendor_id=".$_SESSION['vendor_id']."&emp_id=".$_SESSION['emp_id']."&admin_access_employee=".$_SESSION['admin_access_employee']; 
	$dynamictableresult = callAPI($dynamictableurl);
	$dynamictableres = json_decode($dynamictableresult,1);
	$list = $dynamictableres['display_attributes'];
	$statusjsondecode = $dynamictableres['status_progress'];
	$title =  $menu_details['menu_label']!= '' ?  $menu_details['menu_label'] :  viewTitle($_GET['option']);
	$table = $list['application_table_name'];
	$insert = $page_details['insert_page_name'];
	$view_page =  $page_details['view_page_name'];
	$edit_insert =  $page_details['edit_page_name'];
	$content = $list['help_text']!='' ? $list['help_text'] : 'Help Test';
	$lists = json_decode($list['list_attributes'],1);
	$empty_data = $list['empty_table_message'];
	$type_array = array('select','multi_select','disable_select','radio','checklist', 'color_pick','image');
	$whrcon = array();
	$emp_array   = array();
	foreach ($lists as $key => $value){ //echo $key;
	  	if($value['display_list'] ==1 ){
	    	$dis[$value['display_postion']] ['name'] = $key;  
	    	$dis[$value['display_postion']] ['lable'] = $value['lable'];
	    	$dis[$value['display_postion']] ['type'] = $value['type'];
	    	$dis[$value['display_postion']] ['add_custom_field'] = $value['add_custom_field'];
	  	}
	}
	if(count((array)$dis) !=0){
	  	ksort($dis); 
	}
	$field =  $dynamictableres['field_result'];
	$primary = $dynamictableres['primarykey'];
	$stausresult = ViewStatusData();
	$bills = $dynamictableres['dynmictabler'];
	foreach ($bills as $bill) { 
		$date_array = array('status_date','from_date','to_date','created_date','updated_date');
		$status_array = array('is_active','status_name');
		$user_names = array('created_by','updated_by');
		$dynamicdata = array();
		foreach ($dis as $header) { 
			$after = isset($header['add_custom_field']) && $header['add_custom_field']!='' ? $header['add_custom_field'] :2;
		    if(in_array($header['type'], $type_array)){
		      	$str = $bill[$header['name']];
		      	$str =  json_decode($str);
		      	if(is_array($str)){
		        	foreach ($str as  $st) {
		          		$sts[] = $field[$header['name']][$st];
		        	}
		        	$str = implode(",", (array)$sts);
		      	}elseif($header['type'] == 'color_pick'){
		        	$str = "<div style='background: #".$bill[$header['name']].";width: 20px;height: 20px;'>&nbsp;</div>";
		      	}elseif( $header['type'] == 'image'){
		        	//$bimage = 'uploads/all/'.$bill[$header['name']];
		        	//$str = "<img src='$bimage' style='width:35px;'>";
		        	$str = '-';
		      	}elseif( $header['type'] == 'image'){
		        	$str =  $bill[$header['name']];
		        	$str = $field[$header['name']][$str];
		      	}else{
		        	$str =  $bill[$header['name']];
		        	$str = $field[$header['name']][$str];
		      	}
		        $dynamicdata[$header['name']] = todisplayviewfiled($str,$header['type']); //echo $field[$header['name']][$bill[$header['name']]]; 
		        //echo "111Ye1111s </br>";
		    }elseif($header['type'] == 'date_pick'){
		       $dynamicdata[$header['name']] = $bill[$header['name']] != "" ? fromDate($bill[$header['name']]) : ""; 
		    }else{
		    	if($header['name'] == 'company_id') { 
		          	$dynamicdata[$header['name']] = $_SESSION['company'][$bill['company_id']];
		    	}elseif($header['type'] == 'number_type'){
		       		$dynamicdata[$header['name']] = customeNumberFormate($bill[$header['name']]);
		  		}elseif($header['type'] == 'currency'){
		        	$dynamicdata[$header['name']] = customeummarmonyformate($bill[$header['name']]);
		  		}elseif(in_array($header['name'], $status_array)) { 
		        	$dynamicdata[$header['name']] = $stausresult[$bill[$header['name']]]['name']; 
		     	}elseif($header['name'] == 'current_status'){
		    		$dynamicdata[$header['name']] =$stausresult[$bill[$header['name']]]['name'];
				}elseif(in_array($header['name'], $user_names)) { 
		         	$dynamicdata[$header['name']] = todisplayviewfiled($_SESSION['users'][$bill[$header['name']]],$header['type']); 
		    	}elseif(in_array($header['name'], $date_array)){
		           	$dynamicdata[$header['name']] = dateformates($bill[$header['name']],$configdata['date_format'],$configdata['date_separator']); 
		      	}else{ 
		        	$dynamicdata[$header['name']] = todisplayviewfiled($bill[$header['name']],$header['type']); 
		       }
		  	}
		}
		  	$cssdata[] = $dynamicdata;
	}
	$array_data = $cssdata;
	$table = $transaction_table!="" ? $transaction_table : 'download';
	$data = "";
	$data .= strtoupper($_SESSION['config']['name'])."\n";
	$i=1; foreach($array_data as $key=>$array){
		$show_array = $array;
		unset($show_array['data']);
		if($i==1){	     
		     $data .= strtoupper(str_replace('_', ' ', implode(',', array_keys($show_array))));
		}
		$data .="\n";
		$data .= implode(',', $show_array );
		$m = 1;
		foreach($array['data'] as $listarray){
		    $data .="\n";
		    if($m==1){
		     $data .= " ,".strtoupper(str_replace('_', ' ', implode(',', array_keys($listarray))));
			$data .="\n";
		     }
		    $data .= " ,".implode(',', $listarray);
		$m++; }
		//$data .="\n";
		$i++;
	}
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="'.$table.'.csv"');
	echo $data; exit;
}
?>
