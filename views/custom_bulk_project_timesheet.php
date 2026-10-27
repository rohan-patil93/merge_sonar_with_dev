<?php
$post_vendor =0; $post_reports=0; $emp_name=0;  $approval_manager = 0; $lmonth =array();
	if(isset($_POST['search'])){
		$post_vendor = $_POST['vendor'];
		$emp_name= $_POST['emp_name']!=""?$_POST['emp_name']:'0';
		$approval_manager =  $_POST['approval_manager']!=""?$_POST['approval_manager']:'0';
	}
if($_GET['PAY_MONTH']==''){ $_GET['PAY_MONTH'] = date('m'); }
if($_GET['PAY_YEAR']==''){ $_GET['PAY_YEAR'] = date('Y');}
	//echo date('Y-m-d H:i:s')."in<br>"; //exit;
 $approval_url = API."?mode=toGetblukProjectTimesheet&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR']."&vendor_id=".$post_vendor."&emp_name=".$emp_name."&approval_manager=".$approval_manager;
//echo $approval_url;
$app_data = callAPI($approval_url);
$app_data = json_decode($app_data,1);
	//echo "<pre>"; print_r($app_data);echo "</pre>";//exit;
//$lastapproval_url = API."?mode=toGetblukProjectTimesheetlastmonths&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR'];
$getym = $_GET['PAY_YEAR']."-".$_GET['PAY_MONTH']."-01"; $lastdd = date("Y-m-d", strtotime('-1 months', strtotime($getym)));
$lastapproval_url =  API."?mode=toGetblukProjectTimesheet&month=".date('m',strtotime($lastdd))."&year=".date('Y',strtotime($lastdd))."&vendor_id=".$post_vendor."&emp_name=".$emp_name;
	//echo $lastapproval_url;
$last_data = callAPI($lastapproval_url);
$last_data = json_decode($last_data,1);
	//echo date('Y-m-d H:i:s')."in2<br>";;// exit;
	//echo "<pre>"; print_r($app_data); print_r($last_data); exit;
//$team_count_url = API."?mode=togetteamcount";
$team_count_url = API."?mode=togetteamcount&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR']."&vendor_id=".$post_vendor."&emp_name=".$emp_name."&approval_manager=".$approval_manager;
$team_count_data = callAPI($team_count_url);
$team_count_data = json_decode($team_count_data,1);
$gtnvk = $_GET['TNVAL']!="" ? $_GET['TNVAL'] : "2";
$count_billable =0;$count_nonbillable =0;
$approvevendor = array();
foreach((array)$team_count_data['data'] as $sbb ){
	$approvemanager[$sbb['approval_manager']] = $sbb['approval_manager'];
	$approvevendor[$sbb['vendor_id']] = $sbb['vendor_id'];
	if($sbb['emp_billable']!=$gtnvk){
		$team_count_data_sb[] = $sbb;
	}
	if($sbb['emp_billable']==0){
		$count_billable=$count_billable+1;
	}else{
		$count_nonbillable=$count_nonbillable+1;
	}
}
	$from_date = $_GET['PAY_YEAR']."-".$_GET['PAY_MONTH']."-01";
	$todate = date('Y-m-d', strtotime('last day of this month', strtotime($from_date)));
	$vurl = API."?mode=toretriveminhorsandweksanddayebasedondates&from_date=".$from_date."&to_date=".$todate; 
	$attdenace_data = callAPI($vurl);
	//$attdenace_data = json_decode($attdenace_data,1);
	$attdenace_data_location  = json_decode($attdenace_data,1);
	//print_r($attdenace_data);
$mulval=1;
$attdenace_data = $attdenace_data_location[1];
if($app_data['purchase_uom']=='PH'){
	$mulval = $attdenace_data['minimum_hours']*$attdenace_data['count_working_days'];
}
if($app_data['purchase_uom']=='PW'){
	$mulval = $attdenace_data['count_working_days']/$attdenace_data['count_week'];
}
if($app_data['purchase_uom']=='PD'){
	$mulval = $attdenace_data['count_working_days'];
}



$vdata1["where"] = array('table_name'=> "project_timesheets",'is_active'=>'0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=status_progress&data=".$vdata; 
$vdatas = callAPI($vurl);
$status_progresss = json_decode($vdatas,1);
foreach((array)$status_progresss as $status_progress){
	$statu_progress[$status_progress['status']]['name']=$status_progress['name'];
	$statu_progress[$status_progress['status']]['action_label']=$status_progress['action_label'];
}
	//echo date('Y-m-d H:i:s')."in3<br>";
	//print_r($team_count_data);
$la_billable_amount = 0;
	$la_budgeted_amount = 0;
	$la_employee = 0; 
	$last_pre_list = array();
foreach((array)$last_data  as $lastt){
	$la_billable_amount = $la_billable_amount+$lastt['billable_amount'];
	$la_budgeted_amount = $la_budgeted_amount+$lastt['budgeted_amount'];
	$la_vendor = $lastt['vendor_ids'];
	$la_employee = $la_employee+$lastt['employee_ids'];
	$last_pre_list[] = $lastt;
}
$lmonth['billable_amount'] = $la_billable_amount;
$lmonth['budgeted_amount'] = $la_budgeted_amount;
$lmonth['vendor_ids'] = $la_vendor ;
$lmonth['employee_ids'] = $la_employee;
$lmonth['period']=$key;
$last_pre_list = $last_pre_list;
$apv_billable_amount = 0;
$sba_billable_amount =0;
$total_budget_amount =0;
$total_billabe_hrs =0;
$total_budget_hrs =0;
$total_leave_days =0;
$btncheck =0;
$totalleaveallowvrc = 0; 
$totalleaveadaysemployee = 0; 
foreach((array)$app_data as $apps){
	$emp_sub[$apps['employee_id']] = $apps;
	$mulval=1;
	$attdenace_data = $attdenace_data_location[1];
	if($apps['purchase_uom']=='PH'){
		$mulval = $attdenace_data['minimum_hours']*$attdenace_data['count_working_days'];
	}
	if($apps['purchase_uom']=='PW'){
		$mulval = $attdenace_data['count_working_days']/$attdenace_data['count_week'];
	}
	if($apps['purchase_uom']=='PD'){
		$mulval = $attdenace_data['count_working_days'];
	}
	if($apps['pt_status_id']=="10"){
		if ($apps['emp_billable']!=$gtnvk) {
			$sba[]=$apps;
		}
		$fid = $apps['id'];
		$sba_billable_amount = $sba_billable_amount+$apps['billable_amount'];
		$s_status_id = $apps['pt_status_id'];
		$apv_list[] = $apps;
	}
	if($apps['pt_status_id']>"10" && $apps['pt_status_id']!=20){
		if ($apps['emp_billable']!=$gtnvk) {
			$apv[]=$apps;
		}

		$apv_billable_amount = $apv_billable_amount+$apps['billable_amount'];
		$capps = $apps; $capps['utilize'] = 'Approved';
		$apv_list[] = $capps;
		if($apps['pt_status_id']>ST_APPROVED1){
			$btncheck = 1;
			  		//echo $apps['employee_id'].$apps['current_status'];
		}
		$s_status_id = $apps['pt_status_id'];
	}

	$total_leave_days = $total_leave_days+$apps['leave_days'];
	$total_budget_amount = $total_budget_amount+($apps['budgeted_amount']*$mulval);
	$total_billabe_hrs = $total_billabe_hrs+$apps['billable_hrs'];
	$total_budget_hrs = $total_budget_hrs+176;
	if($apps['pt_status_id']>8){
		$employees[$apps['employee_id']] = $apps['employee_id'];
	}
	$vendors[$apps['vendor_id']] = $apps['vendor_id'];
	$vendor_list[$apps['vendor_id']]=$apps['vendor_name'];
	if($apps['vendor_id'] !="0"){
		$billables_employee[$apps['employee_id']]= $apps['employee_id'];
	}
	$totalleaveallowvrc += $apps['vrc_max_leave']; 
	$totalleaveadaysemployee += $apps['leave_days']; 

}
$billables_count = count((array)$billables_employee);
$employee_count = count((array)$employees);
$sba_count = count((array)$sba);
$apv_count = count((array)$apv);
$table = "project_timesheets";
$stausresult = ViewStatusData();
//krsort((array)$dismonth);
//krsort((array)$disuitlize);
$ttcount =  $apv_billable_amount+$sba_billable_amount;
$vdata1["where"] = array('table_name'=> $table,'is_active'=>0);
	$vdata = urlencode(json_encode($vdata1));
	$vurl = API."?mode=gettabledetailswithconditions&table=status_progress&data=".$vdata; 
	$configesponse = callAPI($vurl);
	$statusprogress = json_decode($configesponse,1);
//print_r($statusprogress);
foreach((array)$statusprogress as $statusprog){
	$st_prog[$statusprog['status']] = $statusprog['action_label'];
}
//print_r($st_prog);
$vdata1["where"] = array('is_active'=>0);
	$vdata = urlencode(json_encode($vdata1));
	$vurl = API."?mode=gettabledetailswithconditions&table=vendors&data=".$vdata; 
	$configesponse = callAPI($vurl);
	$allvendors_list = json_decode($configesponse,1);
//print_r($statusprogress);
$allvendors = array();
foreach((array)$allvendors_list as $allvendors_li){
	$allvendors[$allvendors_li['id']] = $allvendors_li['display_name'];
}
$erp_url = API."?mode=togetempvmstransactionslist&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR'];
//echo $erp_url;
$erp_data = callAPI($erp_url);
$erp = json_decode($erp_data,1);
//print_r($erp);
?>
<script type="text/javascript">
	function projecttimesheetsubmittohr(){
		//alert('Good');
		var favorite = [];
		var ick=0;
		$.each($("input[name='checkbox1']:not(:checked)"), function(){            
			favorite.push($(this).val());
			ick=ick+1;
		});
	//alert(ick+'uu'+favorite);
	if(ick>0){
		var msg = <?php echo "'".$_SESSION['error_message']['9023']."'" ?>;
		swal({
			title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: msg,
            type: "success",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm) {
            if (isConfirm) {
               projecttimesheetsubmittohrs();	
            } 
          });


		// swal.fire({              
		// 	title: msg,   
		// 	type: "success",           
		// 	buttonsStyling: false,
		// 	confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, Submit!",    
		// 	confirmButtonClass: "btn btn-success", 
		// 	showCancelButton: true,     
		// 	cancelButtonText: "<i class='la la-hand-o-right'></i> No, Stop",      
		// 	cancelButtonClass: "btn btn-danger"        
		// }).then(function(result) {
		// 	if (result.value) {
		// 		projecttimesheetsubmittohrs();	
		// 	}
		// });
	}else{
		projecttimesheetsubmittohrs();	
	}

        //alert(favorite);
        //alert("My favourite sports are: " + favorite.join(", "));

      }
      function projecttimesheetsubmittohrs(){
		//alert("Done");
		var table_name = 'project_timesheets';
		jQuery.ajax({
			url: "ajax.php",
			data:'page=updateprojecttimesheetsblukforhr&table='+table_name+'&status=102&extra='+$('#extra').val(),
			type: "POST",
			success:function(data){
				console.log(data);
				//var msg = <?php echo "'".$_SESSION['error_message']['9024']."'" ?>;
				var msg = "Timesheet approved successfully by Finance.";
				console.log(msg);

				swal({
					title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
	                text: msg,
	                type: "success",
	                showCancelButton: false,
	                confirmButtonClass: "btn-danger",
	                confirmButtonText: "Yes",
	                cancelButtonText: "No",
	                closeOnConfirm: true,
	                closeOnCancel: true
	              },
	              function(isConfirm) {
	                if (isConfirm) {
	                   $('#kt_modal_KTDatatable_remote').modal('hide');
						window.location.href=window.location.href;
	                } 
	              });


				// swal.fire({              
				// 	title: msg,              
				// 	type: "success",           
				// 	buttonsStyling: false,
				// 	confirmButtonText: "<i class='la la-hand-o-up'></i>OK",    
				// 	confirmButtonClass: "btn btn-info", 
				// 	showCancelButton: false,     
				// 	cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
				// 	cancelButtonClass: "btn btn-default"        
				// }).then(function(result) {
				// 	if (result.value) {
				// 		$('#kt_modal_KTDatatable_remote').modal('hide');
				// 		window.location.reload();
				// 	}
				// });
			},
			error:function (){}
		});
	}
	function statuspopup(status,id){
		var favorite = [];
		$.each($("input[name='checkbox1']:checked"), function(){            
			favorite.push($(this).val());
		});
		var table_name = 'project_timesheets';
		jQuery.ajax({
			url: "ajax.php",
			data:'page=getbugetamountesignvendorwise&table='+table_name+'&status='+status+'&rid='+favorite+'&extra='+$('#extra').val(),
			type: "POST",
			success:function(data){
				console.log(data);
				if(data==0){
					jQuery.ajax({
						url: "views/custom_insert_status_popup.php",
						data:'table='+table_name+'&status='+status+'&rid='+favorite+'&extra='+$('#extra').val(),
						type: "POST",
						success:function(data){
							$('#popup1').html(data);
							$('#kt_modal_KTDatatable_remote').modal("show");
							
						},
						error:function (){}
					});
				}else{
					var msg = <?php echo "'".$_SESSION['error_message']['9026']."'" ?>;
					swal.fire({              
						title: msg,              
						type: "success",           
						buttonsStyling: false,
						confirmButtonText: "<i class='la la-hand-o-up'></i>OK",    
						confirmButtonClass: "btn btn-info", 
						showCancelButton: false,     
						cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
						cancelButtonClass: "btn btn-default"  
					});
				}
			},
			error:function (){}
		});




	}
	function displaystausdrop(id,table,status_id){
		//alert("id");
		$.ajax({
			url: "ajax.php",
			data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
			type: "POST",
			success:function(data){
				console.log(data);
				$("#dropdownlist_"+id).html(data);
			},
			error:function (){}
		});    	
	}
	/*$(document).ready(function(){
		$('#select_all').on('click',function(){
			
	        if(this.checked){
	            $('.checkbox1').each(function(){
	                this.checked = true;
	            });
	        }else{
	             $('.checkbox1').each(function(){
	                this.checked = false;
	            });
	        }
	    });
	  });*/
	  function tocheckallboxes(){
	  	console.log("Data");
	  	var val = $('#val_select_all').val();
	  	console.log(val);
	  	if(val==0){
	  		$('#val_select_all').val('1');
	  		$('.checkbox1').each(function(){
	  			this.checked = true;
	  		});
	  	}else{
	  		$('#val_select_all').val('0');
	  		$('.checkbox1').each(function(){
	  			this.checked = false;
	  		});
	  	}
	  	tocheckdrodown();
	  }
	  function tocheckdrodown(){

	  	console.log("Data");
	  	var len=0;
	  	$.each($("input[name='checkbox1']:checked"), function(){            
	  		len=len+1;
	  	});
	  	if(len>0){
	  		$("#status_dropdown").show();

	  	}else{
	  		$("#status_dropdown").hide();
	  	}
	  	checkboxwisecheck();
	  }

	  function notifyuser(id){
      //alert("id");
      jQuery.ajax({
      	url: "views/custom_insert_user_notify.php",
      	data:'id='+id+'&comments=Please submit your timesheet ',
      	type: "POST",
      	success:function(data){
      		$('#popup1').html(data);
      		//$('#kt_modal_KTDatatable_remote').modal({ show: true});
		$("#kt_modal_KTDatatable_remote").modal("show");
                //$('#popup123').html(data);
                //$('#kt_modal_KTDatatable_remote123').modal({ show: true});
              },
              error:function (){}
            });
    }
    function checkboxwisecheck(){
    	//console.log("ravi");
    	var favorite = [];
    	$.each($("input[name='checkbox1']:checked"), function(){            
    		favorite.push($(this).val());
    	});
    	console.log(favorite);
    	var table_name = 'project_timesheets';
    	jQuery.ajax({
    		url: "ajax.php",
    		data:'page=getbugetamountesignvendorwise&table='+table_name+'&rid='+favorite+'&extra='+$('#extra').val(),
    		type: "POST",
    		success:function(data){
    			console.log("man"+data);
    			if(data==1){
    				var msg = <?php echo "'".$_SESSION['error_message']['9026']."'" ?>;
    				swal.fire({              
    					title: msg,              
    					type: "success",           
    					buttonsStyling: false,
    					confirmButtonText: "<i class='la la-hand-o-up'></i>OK",    
    					confirmButtonClass: "btn btn-info", 
    					showCancelButton: false,     
    					cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
    					cancelButtonClass: "btn btn-default"  
    				});
    			}
    		},
    		error:function (){}
    	});
    }
    function cvppopup(title,subtitle,list){
	//alert("Check");
    	 jQuery.ajax({
      	url: "views/custom_view_current_previous.php",
      	data:'title='+title+'&subtitle='+subtitle+'&list='+$('#'+list).val()+'&ths='+$('#'+list+'_ths').val(),
      	type: "POST",
      	success:function(data){
      		$('#dynamic_popup11').html(data);
      		//$('#kt_modal_KTDatatable_remote').modal({ show: true});
			$("#kt_modal_dynamic_popup2").modal("show");
                //$('#popup123').html(data);
	        //$('#kt_modal_KTDatatable_remote123').modal({ show: true});
	      },
	      error:function (){}
	    }); 
    }
 </script>
 <div class="d-flex flex-column-fluid">
  	<div class="container-fluid">
		<div class="row">
		<div class="col-lg-12">
			<div class="card">
				
            	<div class="card-header table-card-header">
              		<h5><?php echo "Bulk Time Sheet Approval";  ?></h5>
        		  	<div class="card-header-right table-card-header ">

            		 <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
	</div>
	</div>
  		<!--begin::Row-->
	<div class="p-3">
  		<div class="row">
  			<div class="col-lg-12 mb-4" >
  				<div class="card card-custom" style="margin-bottom: 0px !important;">
  					<div class="card-body" >
  						<div class="row">
  							<div class="row col-lg-12" style="padding:0px !important;">
  								<div class="col-lg-12">
  									<div class=" infisuite_tab ">
  										<?php
  										$month = strtotime(date('Y').'-'.date('m').'-01 - 11 months');
  										$end = strtotime(date('Y').'-'.date('m').'-'.date('j').' + 0 months');
  										while($month <= $end){
											//echo date("d-m-Y",$month);
  											$disss = date('M Y',$month);
  											$dis_year = date('Y',$month);
  											$dis_month = date('m',$month);
		                                //echo $dis_year."_".$dis_month.",";
  											if($_GET['PAY_YEAR'] == $dis_year && $_GET['PAY_MONTH'] == $dis_month ){
  												$class = 'nav-link bg-primary me-6 active';
  											}else{                  
		                                    //$class = 'text-dark text-hover-primary'; btn-light
  												$class = 'btn btn-light';
  											}
  											$month = strtotime("+1 month", $month);
  											?>
  											<div class="float-left m-r-5 b-radius-5 nav-item ">
  												<a class="<?php echo $class; ?> nav-link  me-6 active " style="font-size: 12px !important;" href="index.php?option=COM_BULK_PROJECT_TIMESHEET&PAY_YEAR=<?php echo $dis_year; ?>&PAY_MONTH=<?php echo $dis_month; ?>"> <?php echo $disss; ?></a>
  											</div>
  										<?php  } ?>                            
  									</div>
  								</div>
  								<input type="hidden" name="extra" id="extra" value="<?php echo $_GET['PAY_YEAR']."-".$_GET['PAY_MONTH']."-01" ?>">
  								
  							</div>
  						</div>
  					</div>
  				</div>
  				<?php echo  $dis_status=todisplaystatuslist(array('table'=>'project_timesheets','grand_total'=>'0','current_status'=>$s_status_id)); ?>
  			</div>
  			<div class="row col-lg-12 mb-4" >
<!------ start ------>
<div class="col-xl-3 col-md-12">
    <div class="card proj-t-card">
        <div class="card-body">
            <div class="row align-items-center m-b-30">
                <div class="col-auto">
                    <i class="fa fa-rupee text-c-blue f-30"></i>
                </div>
                <div class="col p-l-0">
                    <h6 class="m-b-5 f-14">Current Vs Previous</h6>
                    <h6 class="m-b-0 text-c-blue"><a onclick="cvppopup('Current',<?php echo "'".customeummarmonyformate($ttcount/100000)." Lac'"; ?>,'apv_list')" >
				<?php echo customeummarmonyformate($ttcount/100000); ?> L
				</a> Vs <a onclick="cvppopup('Previous',<?php echo "'".customeummarmonyformate(($lmonth['billable_amount']+$lmonth['nonbillable_amount'])/100000)." Lac'"; ?>,'last_pre_list')" > <?php echo customeummarmonyformate(($lmonth['billable_amount']+$lmonth['nonbillable_amount'])/100000); ?> L </a></h6>
			<input type="hidden" name="apv_list" id="apv_list" value='<?php echo json_encode($apv_list); ?>' >
			<input type="hidden"  id="apv_list_ths" value='<?php echo json_encode(array('employee_name'=>'employee_name','job_title'=>'job_title','billable_hrs'=>'billable_hrs','billable_amount'=>'billable_amount')); ?>' >
			<input type="hidden" id="last_pre_list" value='<?php echo json_encode((array)$last_pre_list); ?>' >
			<input type="hidden"  id="last_pre_list_thss" value='<?php echo json_encode( array( 'employee_ids'=>'No Of Employees', 'current_status'=>'Status', 'billable_hrs'=>'billable_hrs', 'billable_amount'=>'billable_amount')); ?>' >
			<input type="hidden"  id="last_pre_list_ths" value='<?php echo json_encode(array('employee_name'=>'employee_name','job_title'=>'job_title','billable_hrs'=>'billable_hrs','billable_amount'=>'billable_amount')); ?>' >
                </div>
            </div>
		<?php //$total_vs_p = number_format((($ttcount/((array)$lmonth['billable_amount']+(array)$lmonth['nonbillable_amount']) )*100),0); ?>
            <div class="row align-items-center m-b-15">
                <div class="col-auto">
                    <p class="m-b-0 text-muted">Utilization</p>
                </div>
                
            </div>
            <div class="progress" style="height:6px;">
                <div class="progress-bar bg-c-blue" style="width:<?php echo $total_vs_p; ?>%"></div>
            </div>
            <h6 class="pt-badge bg-c-blue"><?php echo $total_vs_p; ?>%</h6>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-12">
    <div class="card proj-t-card">
        <div class="card-body">
            <div class="row align-items-center m-b-30">
                <div class="col-auto">
                    <i class="fa fa-clock-o text-c-green f-30"></i>
                </div>
                <div class="col p-l-0">
                    <h6 class="m-b-5 f-14 ">Budget Vs Actuals</h6>
			<h6 class="m-b-0 text-c-green">
			<a onclick="cvppopup('Budget',<?php echo "'".$total_budget_hrs." Hrs'"; ?>,'bg_hr_list')" >
				<?php echo $total_budget_hrs; ?> H </a>
			 Vs <a onclick="cvppopup('Actuals',<?php echo "'".$total_billabe_hrs." Hrs'"; ?>,'bg_hr_list')" > 
				<?php echo $total_billabe_hrs; ?> H </a>
			</h6>
			<input type="hidden" name="bg_hr_list" id="bg_hr_list" value='<?php echo json_encode($emp_sub); ?>' >
			<input type="hidden"  id="bg_hr_list_ths" value='<?php echo json_encode(array('employee_name'=>'employee_name','job_title'=>'job_title','budget_hrs'=>'budget_hrs','billable_hrs'=>'billable_hrs')); ?>' >                  
 
                </div>
            </div>
<?php
$total_budget_hrs  = (float)($total_budget_hrs ?? 0);
$total_billabe_hrs = (float)($total_billabe_hrs ?? 0);

if ($total_budget_hrs > 0) {
    $total_hors_p = number_format((($total_billabe_hrs / $total_budget_hrs) * 100), 0);
} else {
    $total_hors_p = 0;
}
?>
            <div class="row align-items-center m-b-15">
                <div class="col-auto">
                    <p class="m-b-0 text-muted">Current Month Efforts</p>
                </div>
                
            </div>
            <div class="progress" style="height:6px;">
                <div class="progress-bar bg-c-green" style="width:<?php echo $total_hors_p; ?>%"></div>
            </div>
            <h6 class="pt-badge bg-c-green"><?php echo $total_hors_p; ?>%</h6>
        </div>
    </div>
</div>



<div class="col-xl-3 col-md-12">
    <div class="card proj-t-card">
        <div class="card-body">
            <div class="row align-items-center m-b-30">
                <div class="col-auto">
                    <i class="far fa fa-coffee text-c-red f-30"></i>
                </div>
                <div class="col p-l-0">
                    <h6 class="m-b-5 f-14">Budget Vs Utilization</h6>
                    <h6 class="m-b-0 text-c-red">
			 <a onclick="cvppopup('Budget',<?php echo "'".customeummarmonyformate($total_budget_amount/100000)." Lac'"; ?>,'bg_ut_list')" > 
			<?php echo customeummarmonyformate($total_budget_amount/100000); ?> L</a> Vs  <a onclick="cvppopup('Utilization',<?php echo "'".customeummarmonyformate($ttcount/100000)." Lac'"; ?>,'bg_ut_list')" >  <?php echo customeummarmonyformate($ttcount/100000); ?> L </a></h6>
			<input type="hidden" name="bg_hr_list" id="bg_ut_list" value='<?php echo json_encode($apv_list); ?>' >
			<input type="hidden"  id="bg_ut_list_ths" value='<?php echo json_encode(array('employee_name'=>'employee_name','job_title'=>'job_title','budgeted_amount'=>'budgeted_amount','billable_amount'=>'billable_amount')); ?>' >
                </div>
            </div>
	    <?php $total_p = ($total_budget_amount > 0) ? number_format((($ttcount / $total_budget_amount) * 100), 0) : 0; ?>
            <div class="row align-items-center m-b-15">
                <div class="col-auto">
                    <p class="m-b-0 text-muted">Current Month Utilized</p>
                </div>
                
            </div>
            <div class="progress" style="height:6px;">
                <div class="progress-bar bg-c-red" style="width:<?php echo $total_p; ?>%"></div>
            </div>
            <h6 class="pt-badge bg-c-red"><?php echo $total_p; ?>%</h6>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-12">
    <div class="card proj-t-card">
        <div class="card-body">
            <div class="row align-items-center m-b-30">
                <div class="col-auto">
                    <i class="fa fa-thumbs-up  text-c-yellow f-30"></i>
                </div>
                <div class="col p-l-0">
                    <h6 class="m-b-5 f-14">Utilization Vs Approved</h6>
                    <h6 class="m-b-0 text-c-yellow">
				 <a onclick="cvppopup('Utilization',<?php echo "'".customeummarmonyformate($ttcount/100000)." Lac'"; ?>,'ut_list')" > 
				<?php echo customeummarmonyformate($ttcount/100000); ?> L </a> Vs<a onclick="cvppopup('Approved',<?php echo "'".customeummarmonyformate($apv_billable_amount/100000)." Lac'"; ?>,'ut_list')" >   <?php echo $apv_billable_amount!="0"?customeummarmonyformate($apv_billable_amount/100000):0; ?> L </a></h6>
			<input type="hidden" name="bg_hr_list" id="ut_list" value='<?php echo json_encode($apv_list); ?>' >
			<input type="hidden"  id="ut_list_ths" value='<?php echo json_encode(array('employee_name'=>'employee_name','job_title'=>'job_title','billable_amount'=>'billable_amount','utilize'=>'Status')); ?>' >
                </div>
            </div>
            <div class="row align-items-center m-b-15">
                <div class="col-auto">
                    <p class="m-b-0 text-muted">Current Month Approved</p>
                </div>
                
            </div>
		<?php $total_apv_p = ($ttcount > 0) ? number_format((($apv_billable_amount / $ttcount) * 100), 0) : 0; ?>
            <div class="progress" style="height:6px;">
                <div class="progress-bar bg-c-yellow" style="width:<?php echo $total_apv_p!=""?$total_apv_p:0; ?>%"></div>
            </div>
            <h6 class="pt-badge bg-c-yellow"><?php echo $total_apv_p; ?>%</h6>
        </div>
    </div>
</div>
<!------ End ------>




  				
  				
  			</div>
  		</div>
  		<!--end::Row-->
  		<!--begin::Row-->
  		<div class="row col-lg-12">
  			<div class="col-lg-12">
  				<!--begin::Advance Table Widget 2-->
  				<div class="card card-custom card-stretch gutter-b">
  					<!--begin::Header-->
  					<div class="card-header border-0">
  						<h5 class="card-title align-items-start flex-column float-left">
  							Billing Info Detail - <?php if($gtnvk==2){ echo "All"; }elseif($gtnvk==1){ echo "Billable"; }elseif($gtnvk==0){ echo "Non-billable"; } ?>
  							<span		class="text-muted mt-3 font-weight-bold
  							font-size-sm">Billable : <?php echo 	$count_billable; ?> Members, Nonbillable : <?php echo $count_nonbillable; ?> Members</span> 
  						</h5>
  						<div class="radio-inline float-right infisuite_tab">
  							<?php $billist =""; if($_GET['PAY_YEAR']!=""){ $billist = "&PAY_YEAR=".$_GET['PAY_YEAR']."&PAY_MONTH=".$_GET['PAY_MONTH']; } ?>
  							<label class="radio nav-item"><a href="index.php?option=COM_BULK_PROJECT_TIMESHEET<?php echo $billist; ?>" class="btn btn-info waves-effect waves-light  <?php if($gtnvk==2){ echo 'active'; } ?>">
  								<?php if($gtnvk==2){ ?> <i class="icofont icofont-check-circled"></i> <?php } ?>
								<?php echo "All"; ?></a> </label> 
  							<label class="radio nav-item"><a href="index.php?option=COM_BULK_PROJECT_TIMESHEET<?php echo $billist."&TNVAL=1"; ?>" class="btn btn-success waves-effect waves-light <?php if($gtnvk==1){ echo 'active'; } ?>">
  								<?php if($gtnvk==1){ ?> <i class="icofont icofont-check-circled"></i> <?php } ?>
  								<?php echo "Billable"; ?></a> </label> 
  							<label class="radio nav-item"><a href="index.php?option=COM_BULK_PROJECT_TIMESHEET<?php echo $billist."&TNVAL=0"; ?>" class="btn btn-disabled  waves-effect waves-light <?php if($gtnvk==0){ echo 'active'; } ?>" style="cursor: pointer;">
  									<?php if($gtnvk==0){ ?> <i class="icofont icofont-check-circled"></i> <?php } ?>
  								<?php echo "Non-billable"; ?></a> </label> 
  						</div>
  					</div>

<div class="col-lg-12 p-20">
								<form method="post" action=""  name="myForm" enctype="multipart/form-data" class="row">

									<div class="col-lg-3">
										<div class="d-flex align-items-center">
											<label class="mr-3 mb-0 d-none d-md-block">Reporting Manager:&nbsp;</label>
											<select class="form-control form-select js-example-basic-single" id="kt_datatable_search_typew" name="approval_manager">
												<option value="0">All</option>
												<?php foreach((array)$approvemanager as $appkey=>$appm){ ?>
													<option  <?php echo ($approval_manager==$appkey) ? 'selected' :""; ?> value="<?php echo $appkey; ?>"><?php echo $_SESSION['users'][$appm]; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="d-flex align-items-center">
											<label class="mr-3 mb-0 d-none d-md-block">Vendor:&nbsp;</label>
											<select class="form-control form-select js-example-basic-single" id="kt_datatable_search_type" name="vendor">
												<option value="0">All</option>
												<?php foreach((array)$allvendors as $vkey =>$vlist){
													if(in_array($vkey,$approvevendor)){ ?>
													<option  <?php echo ($post_vendor==$vkey) ? 'selected' :""; ?> value="<?php echo $vkey; ?>"><?php echo $vlist; ?></option>
												<?php }} ?>
											</select>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="d-flex align-items-center">
											<label class="mr-3 mb-0 d-none d-md-block">Name:&nbsp;</label>
											<input type="text" class="form-control " name="emp_name" value="<?php echo $emp_name=='0' ? '' : $emp_name; ?>">
										</div>
									</div>
									
									<div class="col-lg-2 ">
										<button class="btn btn-primary px-6 font-weight-bold" name="search" value="search">Search</button>
									</div>
								</form>
							</div>
					

  					<div class="card-toolbar">
  						<div class="card-block">
                                                       
                <div class="col-lg-12 row">  
                	<div class="col-lg-7">                  
                    <ul class="nav  md-tabs infisuite_tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#home3" role="tab">Approved
                            		&nbsp;<span class="badge badge-warning"><?php echo $apv_count; ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#profile3" role="tab">Approvals Pending
                            	&nbsp;	<span class="badge badge-warning"><?php echo $sba_count; ?></span>
                            </a>
                           
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#messages3" role="tab">Yet to Submit
                            	&nbsp;<span class="badge badge-warning"><?php $yy=count((array)$team_count_data_sb)-($sba_count+$apv_count); 
			echo $yy>0?$yy:0;?></span></a>                        
                        </li>
                    </ul>
                	</div>
                    <div class="col-lg-3" style="padding:0px !important;">
						<div class="d-flex align-items-center flex-lg-fill mb-2 float-left">
							<div class="btn-group" id="status_dropdown" style="display:none;">
								<button class="btn btn-primary dropdown-toggle font-size-sm" style="color: <?php echo "#".$stausresult['10']['color']; ?>;    background:  <?php echo "#".strval($stausresult['10']['background_color']); ?>; border-color: <?php echo "#".strval($stausresult['10']['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $statu_progress['10']['name']; ?> </button>
								<div class="dropdown-menu" id="dropdownlist_<?php echo $fid; ?>">
									<div class="dropdown-item cursor-pointer" onclick="statuspopup('101','2')" name="status" id="status" value="101"><?php echo $statu_progress['101']['action_label']; ?>  </div>
									<div class="dropdown-item cursor-pointer" onclick="statuspopup('20','2')" name="status" id="status" value="20"><?php echo $statu_progress['20']['action_label']; ?>  </div>
								</div>

							</div>
						</div>
					</div>
  								
                    <!-- Tab panes -->
                    <div class="tab-content card-block">
                        <div class="tab-pane active" id="home3" role="tabpanel">
				
				<?php if($apv_count>0 ){?><div class="col-lg-12">
  									<div class="col-lg-2 float-right"  >
  									     <?php /* ?>	<button <?php $btnch=1;  if($btncheck!=1 ){?> onclick="projecttimesheetsubmittohr()" <?php }else{ echo "disabled"; } ?> class="btn btn-primary font-size-sm float-right <?php if($btncheck==1){ echo "disabled"; } ?>" ><?php echo $statu_progress['102']['name']; ?></button> <?php */ ?> 
								  <button  onclick="projecttimesheetsubmittohr()"  class="btn btn-primary font-size-sm float-right" ><?php echo $statu_progress['103']['name']; ?></button>

  									</div>
									</div>
  								<?php } ?>
                            <div class="table-responsive">
														<table class="table table-borderless table-vertical-center">
															<thead>
																<tr>
																	<th class=" w-40px">1 </th>
																	<th class=" min-w-200px">Employee Details</th>
																	<th class=" min-w-100px text-center">
<?php echo headerlables(array('name'=>'Rate Card Value')); ?>
</th>
																	<th class=" min-w-100px text-center"><?php echo headerlables(array('name'=>'PO Value')); ?></th>
																	<th class=" min-w-125px text-center">Billable Hours</th>
																	<th class=" min-w-110px text-center">Status</th>
																	<th class=" min-w-50px text-center">
																		<div class="checkbox-fade fade-in-primary">
																			<label class="checkbox checkbox-success">
																				<input type="checkbox" checked id="" disabled class="">
																				<span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
																			</label>
																				<input type="hidden" name="" id="val_select_all" value="0">
																			</div>
																		</th>
																	</tr>
																</thead>
																<tbody>
																	<?php $k=3; $td_bilable=0; $vrctd_bilable=0; $td_hrs=0;

		if(count((array)$apv)>0){																
																	 foreach((array)$apv as $sb ){ 
																		$mulval=1;
																		$attdenace_data = $attdenace_data_location[$sb['location_id']];
if($sb['purchase_uom']=='PH'){
	$mulval = $attdenace_data['minimum_hours']*$attdenace_data['count_working_days'];
}
if($sb['purchase_uom']=='PW'){
	$mulval = $attdenace_data['count_working_days']/$attdenace_data['count_week'];
}
if($sb['purchase_uom']=='PD'){
	$mulval = $attdenace_data['count_working_days'];
}
																		$vrcmulval=1;
if($sb['vrc_purchase_uom']=='PH'){
	$vrcmulval = $attdenace_data['minimum_hours']*$attdenace_data['count_working_days'];
}
if($sb['vrc_purchase_uom']=='PW'){
	$vrcmulval = $attdenace_data['count_working_days']/$attdenace_data['count_week'];
}
if($sb['vrc_purchase_uom']=='PD'){
	$vrcmulval = $attdenace_data['count_working_days'];
}
																		$sb_bp = number_format((($sb['billable_amount']/($sb['budgeted_amount']*$mulval))*100),2); 			
																		$sb['vrc_budgeted_amount'] = $sb['vrc_budgeted_amount']*$vrcmulval;
																		$bbg_color= lableandbgcolor($sb_bp);
																		//echo $sb['vrc_billable_amount']."___".$sb['vrc_budgeted_amount'];
																		$vrcsb_bp = number_format((($sb['vrc_billable_amount']/$sb['vrc_budgeted_amount'])*100),2);  
														$vrcbbg_color= lableandbgcolor($vrcsb_bp);		
																		$llablecolor = lableandleave($k);
																		if($sb['emp_billable']!=$gtnvk){
																			?>
																			<tr>
																				<td class="pl-0 py-4">
																					<?php if($sb['emp_image']!=""){ ?>
																						<div class="d-flex align-items-center w-40px">
																							<div class="symbol symbol-40">								
																								<img src="uploads/all/<?php echo $sb['emp_image']; ?>" class="img-radius img-50" alt=""  />
																							</div>
																						</div>
																					<?php }else{ ?>
																						<div class="img-radius img-50 align-top m-r-15 text-center userimageround"> <span class="symbol-label font-size-h5"><?php echo initials($sb['employee_name']); ?></span> </div>
																					<?php } ?>
																				</td>
																				<td class="pl-0">
																					<a href="index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>&EMP=<?php echo $sb['employee_id']; ?>" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-sm"><?php echo $sb['employee_name']; ?></a>
																					<?php if($sb['leave_days']!="" && $sb['leave_days']!="0"){ ?>
																			<span class="label label-<?php echo $llablecolor; ?> mr-2 label-sm "><?php echo $k>0 ? $k : 0; ?></span>
																			<?php } ?>
																					<div>
<a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $_SESSION['users'][$sb['approval_manager']]; ?></a> </br>
																						<a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $sb['job_title']; ?></a>
<?php echo custombuttonserp($erp,$sb['employee_id']); ?>
																					</div>
																				</td>


<td>
<div class="d-flex flex-column w-100 mr-2">
	<div class="d-flex align-items-center justify-content-between mb-2">
		<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['vrc_billable_amount'])); ?></span>
		<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['vrc_budgeted_amount'])); ?></span>
	</div>
	<div class="progress progress-xs w-100">
		<div class="progress-bar <?php echo "bg-".$vrcbbg_color; ?>" role="progressbar" style="width: <?php echo $vrcsb_bp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<div class="text-center"><?php echo $vrcsb_bp; ?>%</div>
</div>
</td>																				<td>
																					<div class="d-flex flex-column w-100 mr-2">
																						<div class="d-flex align-items-center justify-content-between mb-2">
																							<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['billable_amount'])); ?></span>
																							<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['budgeted_amount']*$mulval)); ?></span>
																						</div>
																						<div class="progress progress-xs w-100">
																							<div class="progress-bar <?php echo "bg-".$bbg_color; ?> " role="progressbar" style="width: <?php echo $sb_bp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
																						</div>
			<div class="text-center"><?php echo $sb_bp; ?>%</div>
																					</div>
																				</td>
																<!-- <td class="text-right">
																	<span class="text-dark-75 font-weight-500 d-block font-size-sm">
																		<?php echo customeummarmonyformate($sb['billable_amount']); ?>
																	</span>
																	
																</td> -->
																<td class="text-center">
																	<span class="text-muted font-weight-500"><?php echo $sb['billable_hrs']; ?></span>
																</td>
																<td class="text-center">
																	<span class="form-label label label-lg label-warning"  style="color: <?php echo "#".$stausresult[$sb['pt_status_id']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$sb['pt_status_id']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$sb['pt_status_id']]['background_color']); ?> "><?php echo $st_prog[$sb['pt_status_id']]; ?></span>
															</td>
<td class="text-center pr-0">
	<?php if($sb['pt_status_id'] != "104"){ ?>	
	<div class="checkbox-fade fade-in-primary">
		<label class="checkbox checkbox-success">
			<input type="checkbox" class="" checked name="" disabled value="<?php echo $sb['employee_id']; ?>">
			<span class="cr">
			<i class="cr-icon icofont icofont-ui-check txt-primary"></i>
			</span>
		</label>
	</div>
	<?php } ?>	
</td>
																</tr>
																<?php $td_ra_budget = $td_ra_budget+$sb['vrc_budgeted_amount'];
												$td_ra_bilable = $td_ra_bilable+$sb['vrc_billable_amount'];
												
												$td_bilable = $td_bilable+$sb['billable_amount'];
												$td_budget = $td_budget+($sb['budgeted_amount']*$mulval);
												$td_hrs =$td_hrs+$sb['billable_hrs']; 
												$td_nonhrs =$td_nonhrs+$sb['nonbillable_hrs']; 
												$k= $k-1;} } 
												$td_ra_pre = number_format((($td_ra_bilable/$td_ra_budget)*100),2);
												$td_pre = number_format((($td_bilable/$td_budget)*100),2); 
												$vrcbbg_color = lableandbgcolor($td_ra_pre);
												$bbg_color = lableandbgcolor($td_pre); ?>
												<?php if(count((array)$apv)>0){ ?>
																	<tr>
														<td></td>
														<td ><span class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"> Total </span></td>
														
														<td class="text-right"> 
<div class="d-flex flex-column w-100 mr-2">
	<div class="d-flex align-items-center justify-content-between mb-2">
		<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($td_ra_bilable)); ?></span>
		<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($td_ra_budget)); ?></span>
	</div>
	<div class="progress progress-xs w-100">
		<div class="progress-bar <?php echo "bg-".$vrcbbg_color; ?>" role="progressbar" style="width: <?php echo $td_ra_pre; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<div class="text-center"><?php echo $td_ra_pre; ?>%</div>
</div>													</td>
														<td class="text-right">

<div class="d-flex flex-column w-100 mr-2">
	<div class="d-flex align-items-center justify-content-between mb-2">
		<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($td_bilable)); ?></span>
		<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($td_budget)); ?></span>
	</div>
	<div class="progress progress-xs w-100">
		<div class="progress-bar <?php echo "bg-".$bbg_color; ?> " role="progressbar" style="width: <?php echo $td_pre; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<div class="text-center"><?php echo $td_pre; ?>%</div>
</div>
</td>
														<td class="text-center"><span class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"> <?php echo customeummarmonyformate($td_hrs); ?> </span></td>
														<td></td>
														<td></td>
													</tr>
																<?php } }?>
															</tbody>
														</table>
													</div>
                        </div>
                        <div class="tab-pane" id="profile3" role="tabpanel">
                            <div class="table-responsive">
										<table class="table table-borderless table-vertical-center">
											<thead>
												<tr>
													<th class=" w-40px"> 2</th>
													<th class=" min-w-200px">Employee Details</th>
													<th class=" min-w-100px text-center"><?php echo headerlables(array('name'=>'Rate Card Value')); ?></th>
													<th class=" min-w-100px text-center"><?php echo headerlables(array('name'=>'PO Value')); ?></th>
													<th class=" min-w-125px text-center">Billable Hours</th>
													<th class=" min-w-110px text-center">Status</th>
													<th class=" min-w-50px text-center">
														<div class="checkbox-fade fade-in-primary">
															<label class="checkbox checkbox-success">
																<input type="checkbox" id="select_all" class="itemRow" onclick="tocheckallboxes()">
																<span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
															</label>
																<input type="hidden" name="" id="val_select_all" value="0">
															</div>
														</th>
													</tr>
												</thead>
												<tbody>
													<?php $k=3;$td_bilable=0; $vrctd_bilable=0; $td_hrs=0;
													if(count((array)$sba)>0){
													 foreach((array)$sba as $sb ){  
													
	//print_r($sb);
														$mulval=1;
														$attdenace_data = $attdenace_data_location[$sb['location_id']];
if($sb['purchase_uom']=='PH'){
	$mulval = $attdenace_data['minimum_hours']*$attdenace_data['count_working_days'];
}
if($sb['purchase_uom']=='PW'){
	$mulval = $attdenace_data['count_working_days']/$attdenace_data['count_week'];
}
if($sb['purchase_uom']=='PD'){
	$mulval = $attdenace_data['count_working_days'];
}
														$vrcmulval=1;
if($sb['vrc_purchase_uom']=='PH'){
	$vrcmulval = $attdenace_data['minimum_hours']*$attdenace_data['count_working_days'];
}
if($sb['vrc_purchase_uom']=='PW'){
	$vrcmulval = $attdenace_data['count_working_days']/$attdenace_data['count_week'];
}
if($sb['vrc_purchase_uom']=='PD'){
	$vrcmulval = $attdenace_data['count_working_days'];
}
														$sb_bp = number_format((($sb['billable_amount']/($sb['budgeted_amount']*$mulval))*100),2);
														$sb['vrc_budgeted_amount'] = $sb['vrc_budgeted_amount']*$vrcmulval;
														//echo $sb['vrc_budgeted_amount']."----".$sb['vrc_billable_amount'];
$bbg_color= lableandbgcolor($sb_bp);
$vrcsb_bp = number_format((($sb['vrc_billable_amount']/$sb['vrc_budgeted_amount'])*100),2);  
$vrcbbg_color= lableandbgcolor($vrcsb_bp);
														$llablecolor = lableandleave($k);
														if($sb['emp_billable']!=$gtnvk){
															?>
															<tr>
																<td class="pl-0 py-4 w-50px">
																	<?php if($sb['emp_image']!=""){ ?>
																		<div class="d-flex align-items-center w-40px">
																			<div class="symbol symbol-40">								
																				<img src="uploads/all/<?php echo $sb['emp_image']; ?>" class="img-radius img-50" alt="" />
																			</div>
																		</div>
																	<?php }else{ ?>
																		<div class="img-radius img-50 align-top m-r-15 text-center userimageround"> <span class="symbol-label font-size-h5"><?php echo initials($sb['employee_name']); ?></span> </div>
																	<?php } ?>
																</td>
																<td class="pl-0">
																	<a href="index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>&EMP=<?php echo $sb['employee_id']; ?>" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-sm"><?php echo $sb['employee_name']; ?></a>
																			<?php if($sb['leave_days']!="" && $sb['leave_days']!="0"){ ?>
																			<span class="label label-<?php echo $llablecolor; ?> mr-2 label-sm "><?php echo $k>0 ? $k : 0; ?></span>
																		<?php } ?> </br>
<a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $_SESSION['users'][$sb['approval_manager']]; ?></a>																	<div>

																		<a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $sb['job_title']; ?></a>
<?php echo custombuttonserp($erp,$sb['employee_id']); ?>
																	</div>
																</td>
<td>
<div class="d-flex flex-column w-100 mr-2">
	<div class="d-flex align-items-center justify-content-between mb-2">
		<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['vrc_billable_amount'])); ?></span>
		<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['vrc_budgeted_amount'])); ?></span>
	</div>
	<div class="progress progress-xs w-100">
		<div class="progress-bar <?php echo "bg-".$vrcbbg_color; ?>" role="progressbar" style="width: <?php echo $vrcsb_bp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<div class="text-center"><?php echo $vrcsb_bp; ?>%</div>
</div>
</td>

																<td>
																	<div class="d-flex flex-column w-100 mr-2">
																		<div class="d-flex align-items-center justify-content-between mb-2">
																			<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['billable_amount'])); ?></span>
																			<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['budgeted_amount']*$mulval)); ?></span>
																		</div>
																		<div class="progress progress-xs w-100">
																			<div class="progress-bar <?php echo "bg-".$bbg_color; ?>" role="progressbar" style="width: <?php echo $sb_bp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
																		</div>
<div class="text-center"><?php echo $sb_bp; ?>%</div>
																	</div>
																</td>
												<!-- <td class="text-right">
													<span class="text-dark-75 font-weight-500 d-block font-size-sm">
														<?php echo customeummarmonyformate(round($sb['billable_amount'])); ?>
													</span>
													
												</td> -->
												<td class="text-center">
													<span class="text-muted font-weight-500"><?php echo $sb['billable_hrs']; ?></span>
												</td>
												<td class="text-center">
													<span class="form-label label label-lg label-warning" style="color: <?php echo "#".$stausresult[$sb['pt_status_id']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$sb['pt_status_id']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$sb['pt_status_id']]['background_color']); ?> "><?php echo $st_prog[$sb['pt_status_id']]; ?></span>
												</td>
												<td class="text-center pr-0">
													<div class="checkbox-fade fade-in-primary">
														<label class="checkbox checkbox-success">
															<input type="checkbox" onclick="tocheckdrodown()" class="itemRow checkbox1" name="checkbox1" value="<?php echo $sb['employee_id']; ?>">
															<span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
															</label>
														</div>
													</td>
												</tr>
												<?php 

												$k= $k-1;
												$td_ra_budget = $td_ra_budget+$sb['vrc_budgeted_amount'];
												$td_ra_bilable = $td_ra_bilable+$sb['vrc_billable_amount'];
												
												$td_bilable = $td_bilable+$sb['billable_amount'];
												$td_budget = $td_budget+($sb['budgeted_amount']*$mulval);
												$td_hrs =$td_hrs+$sb['billable_hrs']; 
												$td_nonhrs =$td_nonhrs+$sb['nonbillable_hrs']; 
												$k= $k-1;} } 
												$td_ra_pre = number_format((($td_ra_bilable/$td_ra_budget)*100),2);
												$td_pre = number_format((($td_bilable/$td_budget)*100),2); 
												$vrcbbg_color = lableandbgcolor($td_ra_pre);
												$bbg_color = lableandbgcolor($td_pre); ?>
												<?php if(count((array)$sba)>0){ ?>
													<tr>
														<td></td>
														<td ><span class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"> Total </span></td>
														
														<td class="text-right"> 
<div class="d-flex flex-column w-100 mr-2">
	<div class="d-flex align-items-center justify-content-between mb-2">
		<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($td_ra_bilable)); ?></span>
		<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($td_ra_budget)); ?></span>
	</div>
	<div class="progress progress-xs w-100">
		<div class="progress-bar <?php echo "bg-".$vrcbbg_color; ?>" role="progressbar" style="width: <?php echo $td_ra_pre; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<div class="text-center"><?php echo $td_ra_pre; ?>%</div>
</div>													</td>
														<td class="text-right">

<div class="d-flex flex-column w-100 mr-2">
	<div class="d-flex align-items-center justify-content-between mb-2">
		<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($td_bilable)); ?></span>
		<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($td_budget)); ?></span>
	</div>
	<div class="progress progress-xs w-100">
		<div class="progress-bar <?php echo "bg-".$bbg_color; ?> " role="progressbar" style="width: <?php echo $td_pre; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<div class="text-center"><?php echo $td_pre; ?>%</div>
</div>
</td>
														<td class="text-center"><span class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"> <?php echo customeummarmonyformate($td_hrs); ?> </span></td>
														<td></td>
														<td></td>
													</tr>
												<?php }  } ?>
											</tbody>
										</table>
									</div>
                        </div>
                        <div class="tab-pane" id="messages3" role="tabpanel">
                            <div class="table-responsive">
										<table class="table table-borderless table-vertical-center">
											<thead>
												<tr>
													<th class=" w-40px"> 3</th>
													<th class=" min-w-200px">Employee Details</th>
													<th class=" min-w-100px text-center"><?php echo headerlables(array('name'=>'Rate Card Value')); ?>	</th>
													<th class=" min-w-100px text-center"><?php echo headerlables(array('name'=>'PO Value')); ?></th>
													<th class=" min-w-125px text-center">Billable Hours</th>
													<th class=" min-w-110px text-center">Status</th>
													<th class=" min-w-50px text-center">

													</th>
												</tr>
											</thead>
											<tbody>
												<?php $k=3; 
												foreach((array)$team_count_data_sb as $sb ){ 
													if(!in_array($sb['id'],(array)$employees)){
														//print_r($sb);
														//$sb['budgeted_amount']= "65320";
														$mulval=1;
														$attdenace_data = $attdenace_data_location[$sb['location_id']];
														if($sb['purchase_uom']=='PH'){
															$mulval = $attdenace_data['minimum_hours']*$attdenace_data['count_working_days'];
														}
														if($sb['purchase_uom']=='PW'){
															$mulval = $attdenace_data['count_working_days']/$attdenace_data['count_week'];
														}
														if($sb['purchase_uom']=='PD'){
															$mulval = $attdenace_data['count_working_days'];
														}
																													$vrcmulval=1;
														if($sb['l_rate_card_item_uom']=='PH'){
															$vrcmulval = $attdenace_data['minimum_hours']*$attdenace_data['count_working_days'];
														}
														if($sb['l_rate_card_item_uom']=='PW'){
															$vrcmulval = $attdenace_data['count_working_days']/$attdenace_data['count_week'];
														}
														if($sb['l_rate_card_item_uom']=='PD'){
															$vrcmulval = $attdenace_data['count_working_days'];
														}
														$sb['billable_amount'] = $sb['mpc_billable_amount'];
														$sb['budgeted_amount'] = $sb['budgeted_amount']*$mulval;
														// step 2 -- $sb_bp = number_format((((array)$sb['billable_amount']/(array)$sb['budgeted_amount'])*100),2);
														$sb_bp = is_numeric($sb_bp) ? $sb_bp : '0.00';
														
														$sb['vrc_budgeted_amount'] = $sb['vrc_budgeted_amount']*$vrcmulval;
														$bbg_color= lableandbgcolor($sb_bp);
														// step 3 $vrcsb_bp = number_format((($sb['vrc_billable_amount']/$sb['vrc_budgeted_amount'])*100),2);
														$vrcsb_bp = is_numeric($vrcsb_bp) ? $vrcsb_bp  : '0.00';
														$vrcbbg_color= lableandbgcolor($vrcsb_bp);
														if($approval_manager == 0 || $approval_manager == $sb['approval_manager'] ){
															?>
														<tr>
															<td class="pl-0 py-4">
																<?php if($sb['image']!=""){ ?>
																	<div class="d-flex align-items-center w-40px">
																		<div class="symbol symbol-40">								
																			<img src="uploads/all/<?php echo $sb['image']; ?>" class="img-radius img-50" alt="" />
																		</div>
																	</div>
																<?php }else{ ?>
																	<div class="img-radius img-50 align-top m-r-15 text-center userimageround"> <span class="symbol-label font-size-h5"><?php echo initials($sb['display_name']); ?></span> </div>
																<?php } ?>
															</td>
															<td class="pl-0">
																<a href="index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>&EMP=<?php echo $sb['id']; ?>" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-sm"><?php echo $sb['display_name']; ?></a> <?php //echo $sb['emp_billable']; ?>
																<div>														
<a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $_SESSION['users'][$sb['approval_manager']]; ?></a>							</br>										<a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $sb['job_title']; ?></a>
<?php echo custombuttonserp($erp,$sb['id']); ?>
																</div>
															</td>
<td>
<div class="d-flex flex-column w-100 mr-2">
	<div class="d-flex align-items-center justify-content-between mb-2">
		<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['vrc_billable_amount'])); ?></span>
		<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate(round($sb['vrc_budgeted_amount'])); ?></span>
	</div>
	<div class="progress progress-xs w-100">
		<div class="progress-bar <?php echo "bg-".$vrcbbg_color; ?>" role="progressbar" style="width: <?php echo $vrcsb_bp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<div class="text-center"><?php echo $vrcsb_bp; ?>%</div>
</div>
</td>															
															<td>
																<div class="d-flex flex-column w-100 mr-2">
																	<div class="d-flex align-items-center justify-content-between mb-2">
																		<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo customeummarmonyformate($sb['billable_amount']); ?></span>
																		<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate($sb['budgeted_amount']*$mulval); ?></span>
																	</div>
																	<div class="progress progress-xs w-100">
																		<div class="progress-bar <?php echo "bg-".$bbg_color; ?>" role="progressbar" style="width: <?php echo $sb_bp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
																	</div>
<div class="text-center"><?php echo $sb_bp; ?>%</div>
																</div>
															</td>
												<!--  <td class="text-right">
													<span class="text-dark-75 font-weight-500 d-block font-size-sm">
														<?php //echo customeummarmonyformate('65320'); ?>
													</span>
													
												</td> -->
												<td class="text-center">
													<span class="text-muted font-weight-500"><?php echo $sb['billable_hrs']; ?></span>
												</td> 
												<td class="text-center">
													<span class="form-label label label-lg label-warning"><?php echo "Yet to Submit"; ?></span>
												</td>
												<td class=" pr-0">
													<button class="btn btn-success font-size-sm float-right" onclick="notifyuser(<?php echo $sb['user_id']; ?>)">Notify</button>
												</td>
											</tr>
											<?php $k= $k-1;
														}
													}
												}
												?>
										</tbody>
									</table>
								</div>
                        </div>
                    </div>
                </div>                                  
  					</div>
  				</div>
  				<!--end::Body-->
  			</div>
  			<!--end::Advance Table Widget 2-->
  		</div>
		<div class="col-lg-8">
				<?php include('custom_bulk_timesheet_chart.php'); ?>
			</div>
		<div class="col-lg-4 col-md-12">
		<div class="row col-lg-12">
			
			<div class="col-lg-4 card social-card">
    <div class="card-body text-center">
        <h2 class="text-facebook m-b-20">
		<i class="fa fa-group text-c-green f-30"></i>
	</h2>
        <h3 class="text-facebook f-w-700"><?php echo count((array)$vendors); ?></h3>
        <p class="m-b-0 m-t-15">Vendors</p>
    </div>
</div>
<div class="col-lg-4 card social-card">
    <div class="card-body text-center">
        <h2 class="text-facebook m-b-20">
		<i class="fa fa-mortar-board text-c-green f-30"></i>
	</h2>
<?php $total_apv_p = (!empty($team_count_data['team']) && $team_count_data['team'] > 0) ? number_format(((count((array)$employees) / $team_count_data['team']) * 100), 2) : '0.00'; ?>
        <h3 class="text-facebook f-w-700"><?php echo $team_count_data['team']; ?></h3>
        
        <p class="m-b-0 m-t-15">Resources</p>
    </div>
</div>
<div class="col-lg-4 card social-card">
    <div class="card-body text-center">
        <h2 class="text-facebook m-b-20">
		<i class="fa fa-bed text-c-green f-30"></i>
	</h2>
        <h3 class="text-facebook f-w-700"><?php echo $totalleaveallowvrc."/".$totalleaveadaysemployee; ?></h3>
        
        <p class="m-b-0 m-t-15">Leaves</p>
    </div>
</div>
  					
  		</div>
		</div>
			
		</div>		
	</div>
</div>
