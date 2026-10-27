<?php
	if($_GET['PAY_MONTH']==''){ $_GET['PAY_MONTH'] = date('m'); }
	if($_GET['PAY_YEAR']==''){ $_GET['PAY_YEAR'] = date('Y');}
	$apv = array();
	$sba = array();
	$approval_url = API."?mode=toGetblukFinaceProjectTimesheet&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR']."&emp_id=".$_SESSION['emp_id'];
	$post_vendor =0; $post_reports=0;
	if(isset($_POST['search'])){
		/*$approval_url = API."?mode=toGetblukHrProjectTimesheet&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR']."&emp_id=".$_SESSION['emp_id']."&vendor=".$_POST['vendor']."&reports_to=".$_POST['reports_to'];*/
		$post_vendor = $_POST['vendor'];
		$post_reports = $_POST['reports_to'];
	}
	//echo $approval_url;
	$gtnvk = $_GET['TNVAL']!="" ? $_GET['TNVAL'] : "2";
	$app_data = callAPI($approval_url);
	$app_data = json_decode($app_data,1);

	$from_date = $_GET['PAY_YEAR']."-".$_GET['PAY_MONTH']."-01";
	$todate = date('Y-m-d', strtotime('last day of this month', strtotime($from_date)));
	$vurl = API."?mode=toretriveminhorsandweksanddayebasedondates&from_date=".$from_date."&to_date=".$todate; 
	$attdenace_data = callAPI($vurl);
	//$attdenace_data = json_decode($attdenace_data,1);
	$attdenace_data_location  = json_decode($attdenace_data,1);
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
	foreach($status_progresss as $status_progress){
		$statu_progress[$status_progress['status']]['name']=$status_progress['name'];
		$statu_progress[$status_progress['status']]['action_label']=$status_progress['action_label'];
		$st_prog[$status_progress['status']]=$status_progress['action_label'];
	}
	//$stausresult = ViewStatusData();
	//$statu_progress =$stausresult;
	//echo "<pre>"; print_r($statu_progress);
	$getym = $_GET['PAY_YEAR']."-".$_GET['PAY_MONTH']."-01"; $lastdd = date("Y-m-d", strtotime('-1 months', strtotime($getym)));
$lastapproval_url =  API."?mode=toGetblukFinaceProjectTimesheet&month=".date('m',strtotime($lastdd))."&year=".date('Y',strtotime($lastdd))."&&emp_id=".$_SESSION['emp_id'];
	//$lastapproval_url = API."?mode=toGetblukProjectTimesheetlastmonths&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR']."&emp_id=".$_SESSION['emp_id'];
	//echo $lastapproval_url;
	$last_data = callAPI($lastapproval_url);
	$last_data = json_decode($last_data,1);
	//echo "<pre>"; print_r($app_data); print_r($last_data); exit;
	$apv_billable_amount = 0;
	$sba_billable_amount =0;
	$total_budget_amount =0;
	$total_billabe_hrs =0;
	$total_budget_hrs =0;
	$total_leave_days =0;
	$s_status_id=0;
	$totalleaveallowvrc = 0; 
	$totalleaveadaysemployee = 0; 
	foreach($app_data as $apps){
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
$emp_sub[$apps['employee_id']] = $apps;
if($apps['pt_status_id']==ST_APPROVED4){
        $apv_list[] = $apps;
}
if($apps['pt_status_id']>ST_APPROVED4){
      $capps = $apps; $capps['utilize'] = 'Approved';
		  $apv_list[] = $capps;
}
		if(($post_vendor!=0) || ($post_reports!=0)){
			//		echo "yes";
			if(($post_vendor !=0) && ($post_reports !=0)){
				if($apps['vendor_id'] == $post_vendor && $apps['reports_to']== $post_reports){
					if($apps['pt_status_id']==ST_APPROVED4){
						$sba[]=$apps;
						$fid = $apps['id'];
						$sba_billable_amount = $sba_billable_amount+$apps['billable_amount'];
					}
					if($apps['pt_status_id']>ST_APPROVED4){
						$apv[]=$apps;
						$apv_billable_amount = $apv_billable_amount+$apps['billable_amount'];
					}
				}
			}elseif($post_vendor !=0){
				if($apps['vendor_id'] == $post_vendor){
					if($apps['pt_status_id']==ST_APPROVED4){
						$sba[]=$apps;
						$fid = $apps['id'];
						$sba_billable_amount = $sba_billable_amount+$apps['billable_amount'];
					}
					if($apps['pt_status_id']>ST_APPROVED4){
						$apv[]=$apps;
						$apv_billable_amount = $apv_billable_amount+$apps['billable_amount'];
					}
				}
			}elseif($post_reports !=0){
				if($apps['reports_to']== $post_reports){
					if($apps['pt_status_id']==ST_APPROVED4){
						$sba[]=$apps;
						$fid = $apps['id'];
						$sba_billable_amount = $sba_billable_amount+$apps['billable_amount'];
					}
					if($apps['pt_status_id']>ST_APPROVED4){
						$apv[]=$apps;
						$apv_billable_amount = $apv_billable_amount+$apps['billable_amount'];
					}
				}
			}
		}else{

			if($apps['pt_status_id']==ST_APPROVED4){
				$sba[]=$apps;
				$fid = $apps['id'];
				$sba_billable_amount = $sba_billable_amount+$apps['billable_amount'];
			}
			if($apps['pt_status_id']>ST_APPROVED4){
				$apv[]=$apps;
				$apv_billable_amount = $apv_billable_amount+$apps['billable_amount'];
			}
		}
		$total_leave_days = $total_leave_days+$apps['leave_days'];
		$total_budget_amount = $total_budget_amount+($apps['budgeted_amount']*$mulval);
		$total_billabe_hrs = $total_billabe_hrs+$apps['billable_hrs'];
		$total_budget_hrs = $total_budget_hrs+176;
		$employees[$apps['employee_id']] = $apps['employee_id'];
		$vendors[$apps['vendor_id']] = $apps['vendor_id'];
		if($apps['vendor_id'] !="0"){
			$billables_employee[$apps['employee_id']]= $apps['employee_id'];
		}
		$vendor_list[$apps['vendor_id']]=$apps['vendor_name'];
		$reportto_list[$apps['reports_to']]=$apps['reports_to_name'];
		$s_status_id = $s_status_id < $apps['pt_status_id'] ?  $apps['pt_status_id'] : $s_status_id;
		$totalleaveallowvrc += $apps['vrc_max_leave']; 
		$totalleaveadaysemployee += $apps['leave_days']; 
	}
	foreach($apv as $apvc){
		if($apvc['emp_billable']!=$gtnvk){
			$apvcu[] =$apvc;
		}
	}
	foreach($sba as $sbac){
		if($sbac['emp_billable']!=$gtnvk){
			$sbacu[] =$sbac;
		}
	}
	$billables_count = count((array)$billables_employee);
	$employee_count = count((array)$employees);
	$sba_count = count((array)$sbacu);
	$apv_count = count((array)$apvcu);
	$table = "project_timesheets";
	$stausresult = ViewStatusData();
	$k=1; $ltotal=0;
	$la_billable_amount = 0;
	$la_budgeted_amount = 0;
	$la_employee = 0; 
	$last_pre_list = array();
foreach($last_data  as $lastt){
	if($lastt['pt_status_id']>=ST_APPROVED2){
	$la_billable_amount = $la_billable_amount+$lastt['billable_amount'];
	$la_budgeted_amount = $la_budgeted_amount+$lastt['budgeted_amount'];
	$la_vendor = $lastt['vendor_ids'];
	$la_employee = $la_employee+$lastt['employee_ids'];
	$last_pre_list[] = $lastt;
	}
}
$lmonth['billable_amount'] = $la_billable_amount;
$lmonth['budgeted_amount'] = $la_budgeted_amount;
$lmonth['vendor_ids'] = $la_vendor ;
$lmonth['employee_ids'] = $la_employee;
$lmonth['period']=$key;
$last_pre_list = $last_pre_list; 
/*	foreach($last_data as $key=>$last){
		$dismonth[] = date('M-Y',strtotime($key));
		$la_billable_amount = 0;
		$la_budgeted_amount = 0;
		$la_employee = 0; 
		foreach($last as $lastt){
			$la_billable_amount = $la_billable_amount+$lastt['billable_amount'];
			$la_budgeted_amount = $la_budgeted_amount+$lastt['budgeted_amount'];
			$la_vendor = $lastt['vendor_ids'];
			$la_employee = $la_employee+$lastt['employee_ids'];
		}
		if($k==1){
		 		$lmonth['billable_amount'] = $la_billable_amount;
		 		$lmonth['budgeted_amount'] = $la_budgeted_amount;
		 		$lmonth['vendor_ids'] = $la_vendor ;
		 		$lmonth['employee_ids'] = $la_employee;
		 		$lmonth['period']=$key;
		  }
		//$dismonth[] = $key;
		$disuitlize[] = $la_billable_amount!="" ? number_format($la_billable_amount/100000,2):'0' ;
		$ltotal =$ltotal+$la_budgeted_amount;
		$k++;
	}*/
	//echo "<pre>"; print_r($last_data); print_r($lmonth);print($disuitlize); exit;
	// krsort($dismonth);
	// krsort($disuitlize);
	$ttcount =  $apv_billable_amount+$sba_billable_amount;
	$erp_url = API."?mode=togetempvmstransactionslist";
$erp_data = callAPI($erp_url);
$erp = json_decode($erp_data,1);
?>
<script type="text/javascript">
	function projecttimesheetsubmittofinace(){
		//alert("Done");
        var table_name = 'project_timesheets';
      	jQuery.ajax({
            url: "ajax.php",
            data:'page=projecttimesheetsubmittofinace&table='+table_name+'&status=104&extra='+$('#extra').val(),
            type: "POST",
	        success:function(data){
	        	console.log(data);
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
					$('#kt_modal_KTDatatable_remote').modal('hide');
					window.location.reload();
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
				// 		$('#kt_modal_KTDatatable_remote').modal('hide');
				// 		window.location.reload();
				// 	}
				// });
	        },
	        error:function (){}
        });
	}
	function updateprojecttimesheetsblukapprovefinace(status){
		//alert("Done");
		var favorite = [];
        $.each($("input[name='checkbox1']:checked"), function(){            
            favorite.push($(this).val());
        });
        //alert(favorite);
        var table_name = 'project_timesheets';
      	jQuery.ajax({
            url: "ajax.php",
            data:'page=updateprojecttimesheetsblukapprovefinace&table='+table_name+'&status='+status+'&hid='+favorite+'&extra='+$('#extra').val(),
            type: "POST",
	        success:function(data){
	        	console.log(data);
	            var msg = <?php echo "'".$_SESSION['error_message']['9024']."'" ?>;

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
						window.location.reload();
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
			// 	}).then(function(result) {
			// 		if (result.value) {
			// 			$('#kt_modal_KTDatatable_remote').modal('hide');
			// 			window.location.reload();
			// 		}
			// 	}); 
	        },
	        error:function (){}
        });
	}
	function statuspopup(status,id){
		var favorite = [];
        $.each($("input[name='checkbox1']:checked"), function(){            
            favorite.push($(this).val());
        });
        //alert(favorite);
       // alert("My favourite sports are: " + favorite.join(", "));
		
      //alert("data");
     var table_name = 'project_timesheets';
      jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+favorite+'&extra='+$('#extra').val(),
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
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
						<h5><?php echo "Bulk Time Sheet Approval";  ?></h5> <i class="ti-info-alt f-20 text-primary" onclick="proceduressteps()" data-bs-toggle="tooltip" data-placement="bottom" title="Take A Tour"></i> 
						<div class="card-header-right table-card-header ">

							<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
								<i class="icofont icofont-reply"></i>
								Back
							</a>
						</div>
					</div>
					<div class="p-3">
						<div class="row">
							<div class="col-lg-12 mb-4" >
								<div class="card card-custom" style="margin-bottom: 0px !important;">
									<div class="card-body">
										<div class="row">
											<div class="row col-lg-12" style="padding:0px !important;">
												<div class="col-lg-12" name="select_month">
													<div class=" infisuite_tab" >
		                            <?php
		                            $month = strtotime(date('Y').'-'.date('m').'-'.date('j').' - 11 months');
		                              $end = strtotime(date('Y').'-'.date('m').'-'.date('j').' + 0 months');
		                              while($month <= $end){
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
		                            <div class="float-left m-r-5 b-radius-5 nav-item  ">
		                               <a class="<?php echo $class; ?>nav-link  me-6 active" style="font-size: 12px !important;"  href="index.php?option=COM_BULK_FINANCE_PROJECT_TIMESHEET&PAY_YEAR=<?php echo $dis_year; ?>&PAY_MONTH=<?php echo $dis_month; ?>"> <?php echo $disss; ?></a>
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
			<div class="row col-lg-12">

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
												<!--	<h6 class="m-b-0 text-c-blue"><?php echo customeummarmonyformate($ttcount/100000); ?> L Vs <?php echo customeummarmonyformate(($lmonth['billable_amount']+$lmonth['nonbillable_amount'])/100000); ?> L</h6> -->
												<h6 class="m-b-0 text-c-blue"><a onclick="cvppopup('Current',<?php echo "'".customeummarmonyformate($ttcount/100000)." Lac'"; ?>,'apv_list')" >
				<?php echo customeummarmonyformate($ttcount/100000); ?> L
				</a> Vs <a onclick="cvppopup('Previous',<?php echo "'".customeummarmonyformate(($lmonth['billable_amount']+$lmonth['nonbillable_amount'])/100000)." Lac'"; ?>,'last_pre_list')" > <?php echo customeummarmonyformate(($lmonth['billable_amount']+$lmonth['nonbillable_amount'])/100000); ?> L </a></h6>
			<input type="hidden" name="apv_list" id="apv_list" value='<?php echo json_encode($apv_list); ?>' >
			<input type="hidden"  id="apv_list_ths" value='<?php echo json_encode(array('employee_name'=>'employee_name','job_title'=>'job_title','billable_hrs'=>'billable_hrs','billable_amount'=>'billable_amount')); ?>' >
			<input type="hidden" id="last_pre_list" value='<?php echo json_encode($last_pre_list); ?>' >
			<input type="hidden"  id="last_pre_list_thss" value='<?php echo json_encode( array( 'employee_ids'=>'No Of Employees', 'current_status'=>'Status', 'billable_hrs'=>'billable_hrs', 'billable_amount'=>'billable_amount')); ?>' >
			<input type="hidden"  id="last_pre_list_ths" value='<?php echo json_encode(array('employee_name'=>'employee_name','job_title'=>'job_title','billable_hrs'=>'billable_hrs','billable_amount'=>'billable_amount')); ?>' >
												</div>
											</div>
											<?php $total_vs_p = ((($lmonth['billable_amount'] ?? 0) + ($lmonth['nonbillable_amount'] ?? 0)) > 0) ? number_format((($ttcount / (($lmonth['billable_amount'] ?? 0) + ($lmonth['nonbillable_amount'] ?? 0))) * 100), 0) : 0; ?>
											<div class="row align-items-center m-b-15">
												<div class="col-auto">
													<p class="m-b-0 text-muted">Utilizations</p>
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
													<h6 class="m-b-5 f-14">Budget Vs Actuals</h6>
												<!---	<h6 class="m-b-0 text-c-green"><?php echo $total_budget_hrs; ?> H Vs <?php echo $total_billabe_hrs; ?> H</h6> -->
											
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
											<?php $total_hors_p = (!empty($total_budget_hrs) && $total_budget_hrs > 0) ? number_format((($total_billabe_hrs / $total_budget_hrs) * 100), 0) : 0; ?>											<div class="row align-items-center m-b-15">
												<div class="col-auto">
													<p class="m-b-0 text-muted">Current Month Efforts</p>
												</div>

											</div>
											<div class="progress" style="height:6px;">
												<div class="progress-bar bg-c-green" style="width:<?php echo $total_hors_p; ?>%"></div>
											</div>
											<h6 class="pt-badge bg-c-green"><?php echo $total_hors_p !=""?$total_hors_p:0; ?>%</h6>
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
													<h6 class="m-b-5">Budget Vs Utilization</h6>
												<!--	<h6 class="m-b-0 text-c-red"><?php echo customeummarmonyformate($total_budget_amount/100000); ?> L Vs <?php echo customeummarmonyformate($ttcount/100000); ?> L</h6> --->
												<h6 class="m-b-0 text-c-red">
			 <a onclick="cvppopup('Budget',<?php echo "'".customeummarmonyformate($total_budget_amount/100000)." Lac'"; ?>,'bg_ut_list')" > 
			<?php echo customeummarmonyformate($total_budget_amount/100000); ?> L</a> Vs  <a onclick="cvppopup('Utilization',<?php echo "'".customeummarmonyformate($ttcount/100000)." Lac'"; ?>,'bg_ut_list')" >  <?php echo customeummarmonyformate($ttcount/100000); ?> L </a></h6>
			<input type="hidden" name="bg_ut_list" id="bg_ut_list" value='<?php echo json_encode($emp_sub); ?>' >
			<input type="hidden"  id="bg_ut_list_ths" value='<?php echo json_encode(array('employee_name'=>'employee_name','job_title'=>'job_title','budgeted_amount'=>'budgeted_amount','billable_amount'=>'billable_amount')); ?>' >
												</div>
											</div>
											<?php $total_p = ($total_budget_amount !="" AND $total_budget_amount !="0")?number_format((($ttcount/$total_budget_amount)*100),0):0; ?>

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
													<i class="fa fa-thumbs-up text-c-yellow f-30"></i>
												</div>
												<div class="col p-l-0">
													<h6 class="m-b-5">Utilization Vs Approved</h6>
												<!---	<h6 class="m-b-0 text-c-yellow"> <?php echo customeummarmonyformate($ttcount/100000); ?> L  Vs <?php echo customeummarmonyformate($apv_billable_amount/100000); ?> L</h6> -->
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
											<?php $total_apv_p = ($ttcount !="0" AND $ttcount !="")?number_format((($apv_billable_amount/$ttcount)*100),0):0; ?>
											<div class="progress" style="height:6px;">
												<div class="progress-bar bg-c-yellow" style="width:<?php echo $total_apv_p; ?>%"></div>
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
		<div class="row">
			<div class="col-lg-12">
				<!--begin::Advance Table Widget 2-->
				<div class="card card-custom card-stretch gutter-b ">
					<!--begin::Header-->
					<div class="row col-lg-12">
					<div class="card-header border-0 col-lg-3">
						<h5 class="card-title align-items-start flex-column float-left">
							Billing Info Details
							<span class="text-muted mt-3 font-weight-bold
											font-size-sm">Billable : <?php echo $billables_count; ?> Members, Nonbillable : <?php echo $employee_count-$billables_count; ?> Members</span>&nbsp; &nbsp; &nbsp; </h5>
					</div>
					<div class="col-lg-8 p-20">
							<form method="post" action=""  name="myForm" enctype="multipart/form-data" class="row">

								<div class="col-lg-4">
									<div class="d-flex align-items-center">
										<label class="mr-3 mb-0 d-none d-md-block">Manger:&nbsp;</label>
										<select class="form-control form-select js-example-basic-single" id="kt_datatable_search_status"  name="reports_to">
											<option value="0">All</option>

											<?php foreach($reportto_list as $rkey=>$rlist){  if($rlist!=""){  ?>
												<option <?php echo ($post_reports==$rkey) ? 'selected' :""; ?> value="<?php echo $rkey; ?>"
													data-content="&lt;span class='label label-danger label-inline label-rounded'&gt;<?php echo $rlist; ?>&lt;/span&gt;"><?php echo $rlist; ?>

													<span class="label label-success label-rounded mr-3"><?php echo "- ".count((array)$reportto_count[$rkey])." (".count((array)$team_report_count[$rkey]).")";  ?></span>
												</option>
											<?php } } ?>
										</select>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="d-flex align-items-center">
										<label class="mr-3 mb-0 d-none d-md-block">Vendor:&nbsp;</label>
										<select class="form-control form-select js-example-basic-single" id="kt_datatable_search_type" name="vendor">
											<option value="0">All</option>
											<?php foreach($vendor_list as $vkey=>$vlist){ ?>
												<option  <?php echo ($post_vendor==$vkey) ? 'selected' :""; ?> value="<?php echo $vkey; ?>"><?php echo $vlist; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-lg-2  p-t-10">
									<button class="btn btn-primary px-6 font-weight-bold" name="search" value="search">Search</button>
								</div>
							</form>
						</div>
					</div>
					<div class="row col-lg-12 p-3">
						
						<div class="col-lg-6 ">
							<div class=""  >
								<ul class="nav  md-tabs infisuite_tab" role="tablist"> 
									<li class="nav-item" name="approved_tab"> 
										<a class="nav-link py-2 px-4"
										data-bs-toggle="tab" href="#kt_tab_pane_11_2">Approved &nbsp;<span class="lable-round"><?php echo $apv_count; ?></span>
										</a>
									</li> 
									<li class="nav-item" name="approval_pending_tab"> <a class="nav-link py-2
										px-4 active" data-bs-toggle="tab"
										href="#kt_tab_pane_11_3">Approvals Pending &nbsp;<span class="lable-round"><?php echo $sba_count; ?></span></a> 
									</li> 
								</ul> 
							</div> 
						</div>
						<div class="col-lg-2" style="padding:0px !important;">
									<div class="d-flex align-items-center flex-lg-fill mb-2 float-left">
										<div class="btn-group" id="status_dropdown" style="display:none;" name="submit_for_fin_approval">
		                                     <button class="btn btn-primary dropdown-toggle font-size-sm" style="color: <?php echo "#".$stausresult[ST_APPROVED4]['color']; ?>;    background:  <?php echo "#".strval($stausresult[ST_APPROVED4]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[ST_APPROVED4]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $statu_progress[ST_APPROVED4]['name']; ?> </button>
		                                    <div class="dropdown-menu" id="dropdownlist_<?php echo $fid; ?>">
		                            			<div class="dropdown-item cursor-pointer" onclick="updateprojecttimesheetsblukapprovefinace(<?php echo ST_APPROVED ?>)" name="status" id="status" value="<?php echo ST_APPROVED ?>"><?php echo $statu_progress[ST_APPROVED]['action_label']; ?> </div>
		                                    </div>
		                                    
		                                </div>
									</div>
								</div>
						<div class="col-lg-4">
							<div class="radio-inline float-right infisuite_tab">
								<?php $billist =""; if($_GET['PAY_YEAR']!=""){ $billist = "&PAY_YEAR=".$_GET['PAY_YEAR']."&PAY_MONTH=".$_GET['PAY_MONTH']; } ?>
								<label class="radio nav-item" name="all_tab"><a href="index.php?option=COM_BULK_HR_PROJECT_TIMESHEET<?php echo $billist; ?>" class="btn btn-info waves-effect waves-light  <?php if($gtnvk==2){ echo 'active'; } ?>"><?php if($gtnvk==2){ ?> <i class="icofont icofont-check-circled"></i> <?php } ?> <?php echo "All"; ?></a> </label> 
								<label class="radio nav-item" name="billale_tab"><a href="index.php?option=COM_BULK_HR_PROJECT_TIMESHEET<?php echo $billist."&TNVAL=1"; ?>" class="btn btn-warning waves-effect waves-light <?php if($gtnvk==1){ echo 'active'; } ?>"><?php if($gtnvk==1){ ?> <i class="icofont icofont-check-circled"></i> <?php } ?><?php echo "Billable"; ?></a> </label> 
								<label class="radio nav-item" name="nonbillable_tab"><a href="index.php?option=COM_BULK_HR_PROJECT_TIMESHEET<?php echo $billist."&TNVAL=0"; ?>" class="btn btn-danger waves-effect waves-light <?php if($gtnvk==0){ echo 'active'; } ?>"><?php if($gtnvk==0){ ?> <i class="icofont icofont-check-circled"></i> <?php } ?><?php echo "Non-billable"; ?></a> </label> 
							</div>
						</div>
					</div>
					<div class="card-body pt-2 pb-0 mt-n3">
						<div class="tab-content mt-5" id="myTabTables11">
							<!--begin::Tap pane-->
							<div class="tab-pane fade" id="kt_tab_pane_11_2" role="tabpanel" aria-labelledby="kt_tab_pane_11_2">
								<!--begin::Table-->
								<div class="table-responsive">
									<table class="table table-borderless table-vertical-center">
										<thead>
											<tr>
												<th class="p-0 w-40px"> </th>
												<th class="p-0 min-w-200px">Employee Details</th>
												<th class="p-0 min-w-200px">Hiring Manger</th>
												<th class="p-0 min-w-200px">Vendor</th>
												<th class=" p-0 min-w-200px"><?php echo headerlables(array('name'=>'Rate Card Value')); ?></th>
												<th class=" p-0 min-w-200px"><?php echo headerlables(array('name'=>'PO Value')); ?></th>
												<!-- <th class="p-0 min-w-100px text-right">Billable Amount</th> -->
												<th class="p-0 min-w-125px text-center">Billable Hours</th>
											<!-- 	<th class="p-0 min-w-125px text-right">Nonbillable Hours</th> -->
												<th class="p-0 min-w-110px text-center">Status</th>
												
												<th class=" min-w-50px text-center">
													<div class="checkbox-fade fade-in-primary" name="approval_all_check_box">
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
											<?php $k=3; $td_bilable=0; $td_hrs=0; $td_nonhrs=0;
											if(count((array)$apv)>0){
											foreach($apv as $sb ){ 
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
																	<img src="uploads/all/<?php echo $sb['emp_image']; ?>" class="h-50 align-self-center" alt="" />
															</div>
														</div>
													<?php }else{ ?>
														<div class="img-radius img-50 align-top m-r-15 text-center userimageround"> <span class="symbol-label font-size-h5"><?php echo initials($sb['employee_name']); ?></span> </div>
														
													<?php } ?>
												</td>
												
												<td class="pl-0">
													<a href="index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>&EMP=<?php echo $sb['employee_id']; ?>" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"><?php echo $sb['employee_name']; ?></a>
													<?php if($sb['leave_days']!="" && $sb['leave_days']!="0"){ ?>
																<span class="label label-<?php echo $llablecolor; ?> mr-2 label-sm "><?php echo $k>0 ? $k : 0; ?></span>
																<?php } ?>
													<div>
														
														<a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $sb['job_title']; ?></a><?php echo custombuttonserp($erp,$sb['employee_id']); ?>
													</div>
												</td>
												<td><?php echo $sb['reports_to_name']; ?></td>
												<td><?php echo $sb['vendor_name']; ?> </td>
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
													<span class="text-dark-75 font-weight-500 d-block font-size-lg">
														<?php echo customeummarmonyformate($sb['billable_amount']); ?>
													</span>
													
												</td> -->
												<td class="text-center">
													<span class="text-muted font-weight-500"><?php echo $sb['billable_hrs']; ?></span>
												</td>
												<!-- <td class="text-right">
													<span class="text-muted font-weight-500"><?php echo $sb['nonbillable_hrs']; ?></span>
												</td> -->
												<td class="text-center">
<span class="form-label label label-lg label-warning"  style="color: <?php echo "#".$stausresult[$sb['pt_status_id']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$sb['pt_status_id']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$sb['pt_status_id']]['background_color']); ?> "><?php echo $st_prog[$sb['pt_status_id']]; ?></span>
												</td>
												
												<td class="text-center pr-0">
													<div class="checkbox-fade fade-in-primary" name="individual_approvalcheckbox">
														<label class="checkbox checkbox-success">
															<input type="checkbox" class="" checked name="" disabled value="<?php echo $sb['employee_id']; ?>">
															<span class="cr">
															  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
															</span>
														</label>
													</div>
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
												<?php if(count((array)$apv)>0){  ?>
													<tr>
														<td></td>
														<td ><span class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"> Total </span></td>
														<td></td>
														<td></td>
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
								<!--end::Table-->
							</div>
							<!--end::Tap pane-->
							<!--begin::Tap pane-->
							<div class="tab-pane fade show active" id="kt_tab_pane_11_3" role="tabpanel" aria-labelledby="kt_tab_pane_11_3">
								<!--begin::Table-->
								<div class="table-responsive">
									<table class="table table-borderless table-vertical-center">
										<thead>
											<tr>
												<th class="p-0 w-40px"> </th>
												<th class="p-0 min-w-200px">Employee Details</th>
												<th class="p-0 min-w-200px">Hiring Manger</th>
												<th class="p-0 min-w-200px">Vendor</th>
													<th class=" p-0 min-w-200px"><?php echo headerlables(array('name'=>'Rate Card Value')); ?></th>
												<th class=" p-0 min-w-200px"><?php echo headerlables(array('name'=>'PO Value')); ?></th>
												<th class="p-0 min-w-125px text-center">Billable Hours</th>
											<!-- 	<th class="p-0 min-w-125px text-right">Nonbillable Hours</th> -->
												<th class="p-0 min-w-110px text-center">Status</th>
												
												<th class=" min-w-50px text-center">
														<div class="checkbox-fade fade-in-primary" name="approval_pending_all_check_box">
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
											<?php $k=3; $td_bilable=0; $td_hrs=0; $td_nonhrs=0;
											if(count((array)$sba)>0){
											foreach($sba as $sb ){
$mulval = 1;
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
																	<img src="uploads/all/<?php echo $sb['emp_image']; ?>" class="h-50 align-self-center" alt="" />
															</div>
														</div>
													<?php }else{ ?>
														<div class="img-radius img-50 align-top m-r-15 text-center userimageround"> <span class="symbol-label font-size-h5"><?php echo initials($sb['employee_name']); ?></span> </div>
														
													<?php } ?>
												</td>
												
												<td class="pl-0">
													<a href="index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>&EMP=<?php echo $sb['employee_id']; ?>" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"><?php echo $sb['employee_name']; ?></a>
													<?php if($sb['leave_days']!="" && $sb['leave_days']!="0"){ ?>
																<span class="label label-<?php echo $llablecolor; ?> mr-2 label-sm "><?php echo $k>0 ? $k : 0; ?></span>
																<?php } ?>
													<div>
														
														<a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $sb['job_title']; ?></a><?php echo custombuttonserp($erp,$sb['employee_id']); ?>
													</div>
												</td>
												<td><?php echo $sb['reports_to_name']; ?></td>
												<td><?php echo $sb['vendor_name']; ?> </td>
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
												<!-- <td>
													<div class="d-flex flex-column w-100 mr-2">
														<div class="d-flex align-items-center justify-content-between mb-2">
															<span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo $sb_bp; ?>%</span>
															<span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate($sb['billable_amount']); ?></span>
														</div>
														<div class="progress progress-xs w-100">
															<div class="progress-bar <?php echo "bg-".$bbg_color; ?>" role="progressbar" style="width: <?php echo $sb_bp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
													</div>
												</td> -->
												<!-- <td class="text-right">
													<span class="text-dark-75 font-weight-500 d-block font-size-lg">
														<?php echo customeummarmonyformate($sb['billable_amount']); ?>
													</span>
													
												</td> -->
												<td class="text-right">
													<span class="text-muted font-weight-500"><?php echo $sb['billable_hrs']; ?></span>
												</td>
												<!-- <td class="text-center">
													<span class="label label-lg label-light-warning label-inline"><?php echo $sb['nonbillable_hrs']; ?></span>
												</td> -->
												<td class="text-center">
<span class="form-label label label-lg label-warning"  style="color: <?php echo "#".$stausresult[$sb['pt_status_id']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$sb['pt_status_id']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$sb['pt_status_id']]['background_color']); ?> "><?php echo $st_prog[$sb['pt_status_id']]; ?></span>
												</td>
												
												
												<td class="text-center pr-0">
													<div class="checkbox-fade fade-in-primary" name="individual_approval_pending_checkbox">
														<label class="checkbox checkbox-success">
															<input type="checkbox" onclick="tocheckdrodown()" class="itemRow checkbox1" name="checkbox1" value="<?php echo $sb['employee_id']; ?>">
															<span class="cr">
																<i class="cr-icon icofont icofont-ui-check txt-primary"></i>
															</span>
														</label>
													</div>
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
												<?php if(count((array)$sba)>0){ ?>
													<tr>
														<td></td>
														<td ><span class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"> Total </span></td>
														<td></td>
														<td></td>
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
								<!--end::Table-->
							</div>
							<!--end::Tap pane-->
							
						</div>
					</div>
					<!--end::Body-->
				</div>
				<!--end::Advance Table Widget 2-->
			</div>
			<!-- <div class="col-lg-4">
				<div class="card card-custom gutter-b card-stretch">
					
					<div class="card-header border-0 pt-5">
						<div class="card-title">
							<div class="card-label">
								<div class="font-weight-bolder">Last 6 Months Utilization</div>
								<div class="font-size-sm text-muted mt-2"><?php echo customeummarmonyformate($ltotal); ?></div>
							</div>
						</div>
						
					</div>
					
					<div class="card-body d-flex flex-column px-0">
						
						<input type="hidden" name="" id="lamonths" value="<?php /* echo implode(",",$dismonth); */ ?>">
						<input type="hidden" name="" id="lauitlize" value="<?php /* echo implode(",",$disuitlize); */ ?>" >
						<div id="kt_tiles_widget_1_chart" data-color="info" style="height: 125px"></div>
						
						<div class="flex-grow-1 card-spacer-x mt-10">
							
							<div class="d-flex align-items-center justify-content-between mb-10">
								<div class="d-flex align-items-center mr-2">
									
									<div>
										<a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder">Last Month Budget</a>
										<div class="font-size-sm text-muted font-weight-bold mt-1">
											<?php echo date('M-Y',strtotime($lmonth['period'])); ?>
										</div>
									</div>
								</div>
								<div class="label label-light label-inline font-weight-bold text-dark-50 py-4 px-3 font-size-base"><?php echo customeummarmonyformate($lmonth['budgeted_amount']); ?></div>
							</div>
							
							<div class="d-flex align-items-center justify-content-between mb-10">
								<div class="d-flex align-items-center mr-2">
								
									<div>
										<a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder">Last Month Approved Budget</a>
										<div class="font-size-sm text-muted font-weight-bold mt-1"><?php echo date('M-Y',strtotime($lmonth['period'])); ?></div>
									</div>
								</div>
								<div class="label label-light label-inline font-weight-bold text-dark-50 py-4 px-3 font-size-base"><?php echo customeummarmonyformate($lmonth['billable_amount']); ?></div>
							</div>
							
							<div class="d-flex align-items-center justify-content-between">
								<div class="d-flex align-items-center mr-2">
								
									<div>
										<a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder">Vendor - Resoureces</a>
										<div class="font-size-sm text-muted font-weight-bold mt-1"><?php echo $lmonth['vendor_ids']."-".$lmonth['employee_ids']; ?> </div>
									</div>
								</div>
							
							</div>
							
						</div>
						
					</div>
					
				</div>
				
			</div> -->
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
							<?php //$total_apv_p = number_format(((count((array)$employees)/$team_count_data['team'])*100),2); ?>
							<h3 class="text-facebook f-w-700"><?php echo count((array)$employees); ?></h3>

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
					<!-- <div class="col-lg-12 col-md-12">
						<div class="card o-hidden">
							<div class="card-header">
								<h5>Last 6 Months Utilization</h5>
								<span><?php echo customeummarmonyformate($ltotal); ?></span>
							</div>
							<div class="card-block">
								<div class="row">
									<div class="col-4">
										<p class="text-muted m-b-5">Last Month Assign Budget</p>
										<h6><?php echo customeummarmonyformate($lmonth['budgeted_amount']); ?></h6>
									</div>
									<div class="col-4">
										<p class="text-muted m-b-5">Last Month Approved Budget</p>
										<h6><?php echo customeummarmonyformate($lmonth['billable_amount']); ?></h6>
									</div>
									<div class="col-4">
										<p class="text-muted m-b-5">Vendor &nbsp;&nbsp;-&nbsp;&nbsp; Resoureces</p>
										<h6><?php echo $lmonth['vendor_ids']."-".$lmonth['employee_ids']; ?></h6>
									</div>
								</div>
							</div>
							<div id="income-analysis" style="height:100px"></div>
						</div>
					</div> -->
				</div>
			</div>
		</div>
		<!--end::Row-->
	</div>
</div>
<?php $table_name= 'COM_BULK_FINANCE_PROJECT_TIMESHEET';?>
