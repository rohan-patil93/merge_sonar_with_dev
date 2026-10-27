 <?php
 $_SESSION['apply_js']['todo'] = '1';
 $data2["where"] = array('is_active'=> '0');
 $costdata = urlencode(json_encode($data2));
 $costurl = API."?mode=gettabledetailswithconditions&table=application_widgets&data=".$costdata;
 $costdata = callAPI($costurl);
 $costress = json_decode($costdata, 1);
 $appwizdata = callAPI($appwizurl);
 $appwizdata = json_decode($appwizdata, 1);
 foreach ($costress as  $value) {
 	$wizz[$value['id']] = $value['page_id'];
 }
 $stausresult = ViewStatusData();   
 $current_year = date('Y'); 
 $yesterday_date = date('Y-m-d', strtotime('-1 days'));

//echo $stausresult();
 $vurl = API."?mode=togetkpitaskstatus&company_id=".$_SESSION['company_id']."&is_active=0&emp_id=".$_SESSION['emp_id']."&balance_date=".$yesterday_date."&assesment_year=".$_SESSION['config']['assessment_year'];
          //  print_r($vurl); exit;
      		$configesponse = callAPI($vurl); //exit;
      		$kpi_tasks = json_decode($configesponse,1);
      		$total_tasks_achieve = 15;
      		$completed_tasks_achieve = 30;
      	   // echo "<pre>"; print_r($kpi_tasks); echo "</pre>";//exit;
      		/*$kpi_data = $kpi_tasks['kpi_task_status'];
      		$total_tasks_achieve = $kpi_data[0]['total_tasks']; 
      		if($total_tasks_achieve=='') {
      			$total_tasks_achieve = 0;
      		}
   
      		$completed_tasks_achieve = $kpi_data[0]['completed_tasks'];
      		if($completed_tasks_achieve=='') {
      			$completed_tasks_achieve = 0;
      		}
      		$highest_percent = $kpi_data[0]['highpercent'];
      		$average_percent = $kpi_data[0]['avgpercent'];*/
      		$employee_id = $kpi_data[0]['emp_id'];
      		$emp_dob_details = nearestDateFirstShow($kpi_tasks['emp_dob_details'],'date_of_birth');
      		$emp_wed_details = nearestDateFirstShow($kpi_tasks['emp_wed_details'],'wedding_date');
      		$wishes_count = count((array)$emp_dob_details) + count((array)$emp_wed_details);

      		
      		/* Review Data Start */
  /**    		$reviewwr["where"] = array('company_id'=>$_SESSION['company_id'],'response_type'=>0,'user_id'=>$_SESSION['userid']);
      		$reviewcon = urlencode(json_encode($reviewwr));
      		$reviewurl = API."?mode=gettabledetailswithconditions&table=transaction_approval_logs&data=".$reviewcon;
      		$reviewdata = callAPI($reviewurl);
      		$reviewdata = json_decode($reviewdata, 1);
      		$total_review_count= array();
      		foreach ($reviewdata as  $reviewda) {
      			if(date('Y-m-d') == date('Y-m-d',strtotime($reviewda['request_date']))){
      				$total_review_count['now'][]=$reviewda;
      			}else{
      				$total_review_count['due'][]=$reviewda;
      			}
      		}
      		**/

	//$reviewwr["where"] = array('company_id'=>$_SESSION['company_id'],'response_type'=>0,'user_id'=>$_SESSION['userid']);
      //		$reviewcon = urlencode(json_encode($reviewwr));
      		




      		$todate = date('Y-m-d', strtotime('+6 days'));
      		$fromdate = date('Y-m-d',strtotime('-120 days'));

      		$vurl = API."?mode=togetkpitaskhistories&company_id=".$company_id."&is_active=0&user_id=".$_SESSION['userid']."&task_date_from=".$fromdate."&task_date_to=".$todate;
      		//print_r($vurl); exit;
      		$configesponse = callAPI($vurl); //exit;
      		$kpi_data_list = json_decode($configesponse,1);

      		foreach ($kpi_data_list as  $task) {
      			if($task['task_date'] == date('Y-m-d')){
      				$count_task['now'][] = $task;
      			}elseif(strtotime($task['task_date']) < strtotime(date('Y-m-d'))){
      				$count_task['due'][] = $task;
      			}else{
      				$count_task['after'][] = $task;
      			}
      		}
      		
      //		echo "<pre>"; print_r($emp_data_list);exit;

      		$total_tasks = count((array)$kpi_data_list);




      		$holiday_list = array();
      		$holidaywr["where"] = array('company_id'=> $_SESSION['company_id'],'DATE_FORMAT(holiday_date,"%Y")'=>$current_year);
      	//sprint_r($holidaywr);	exit;
      		$holidaycon = urlencode(json_encode($holidaywr));
      		$holidayurl = API."?mode=gettabledetailswithconditions&table=holidays&data=".$holidaycon; 
      		$holidaydata = callAPI($holidayurl);
      		$holidaydata = json_decode($holidaydata,1);
			$holidaydata = nearestDateFirstShow($holidaydata,'holiday_date');
      		$total_holidays = count((array)$holidaydata);


      		$count_array['reviews']= $total_reviews;
      		$count_array['tasks']= $total_tasks;
      		$count_array['holidays']= $total_holidays;
      		$count_array['wishes']= $wishes_count;
      		$count_headings[] = $count_array;
   //print_r($count_headings);

      		/* Employee deatils */
      		$reviewwrw["where"] = array('company_id'=>$_SESSION['company_id'],'id'=>$_SESSION['emp_id']);
      		$reviewwrw["field"] = "image";
      		$reviewconw = urlencode(json_encode($reviewwrw));
      		$reviewurlw = API."?mode=gettabledetailswithconditions&table=employees&data=".$reviewconw;
      		$reviewdataw= callAPI($reviewurlw);
      		$reviewdataw = json_decode($reviewdataw, 1);
         // print_r($reviewdataw[0]['image']);

$reviewurl = API."?mode=getriviewsdata&response_type=0";
$reviewdata = callAPI($reviewurl);
$reviewdata = json_decode($reviewdata, 1);
$stausresult = ViewStatusData();

$listcompliancesurl = API."?mode=getcompliancesdashbord&type=employeetask&emp_id=".$_SESSION['emp_id']."&date=".date('Y-m-d');
$listcompliancesres = callAPI($listcompliancesurl);
$listcompliances = json_decode($listcompliancesres, 1);
$compliancetaskhistory = $listcompliances['compliance_task_histories']; 
      		?>
      		<script type="text/javascript">
      			function displaystausdrop(id,table,status_id){
      				$.ajax({
      					url: "ajax.php",
      					data:'page=current_status_drop_table&id='+id+'&current_status='+status_id+'&table='+table,
      					type: "POST",
      					success:function(data){
      						console.log(data);
      						$("#dropdownlist_"+id).html(data);
      					},
      					error:function (){}
      				});

      			}
      			function statuspopup(status,id,table_name,auth,task=""){
      //alert("data");
     //var table_name = document.getElementById('table_name').value;
     
     

     if(auth == 0){
     	
     	if(task == "task"){
     		if($('#checkstausinput'+id).prop("checked") == true){
     			jQuery.ajax({
     				url: "views/custom_insert_status_popup_task.php",
     				data:'table='+table_name+'&status='+status+'&rid='+id,
     				type: "POST",
     				success:function(data){
     					$('#popup1125').html(data);
     					$("#kt_modal_KTDatatable_remotenewdemo").modal({
				            backdrop: 'static',
				            keyboard: false
				        });

     					$('#kt_modal_KTDatatable_remotenewdemo').modal("show");


     				},
     				error:function (){}
     			});
     		}	

     	}else{	
     		jQuery.ajax({
     			url: "views/custom_insert_status_popup.php",
     			data:'table='+table_name+'&status='+status+'&rid='+id,
     			type: "POST",
     			success:function(data){
     				$('#popup1').html(data);
     				$('#kt_modal_KTDatatable_remote').modal("show");
     			},
     			error:function (){}
     		});
     	}
     }else{
     	if(task == "task"){
     		$('#checkstausinput'+id).prop('checked', false);
     		$("#checkstausid"+id).removeClass("done-task");
     	}
     	Swal.fire({
     		icon: 'error',
     		title: 'Oops...',
     		text: auth	
     	})
     }
 }
 function popupfeedmsg(sender,receiver,msgtitle){
 	alert(sender);
   //sending_emp_id=&receiving_emp_id='+receiver+'&msg_title='+msgtitle;
   //alert(msgtitle);
   jQuery.ajax({
   	url: "views/new_from.php",
   	data:'option=COM_INSERT_FEEDS&display=1&sending_emp_id='+sender+'&receiving_emp_id='+receiver+'&msg_title='+msgtitle,
         //data:'option=COM_INSERT_FEEDS?display=1&sending_emp_id='+sender;
         type: "POST",
         success:function(data){
         	$('#dynamic_popup1').html(data);
         	$('#kt_modal_dynamic_popup').modal("show");
         },
         error:function (){}
     });
   
   
}

function reviewsrelods(){
	jQuery.ajax({
		url: "views/custom_dashboard_reviews.php",
		data:'',
		type: "GET",
		success:function(data){
			$('.reviewtableaax').html(data);
			
		},
		error:function (){}
	});
}
  function dynamicinsertpopup(option){
    //alert("insert");
    jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error:function (){}
        });
  }

  function getTaskCheckList(id,aryposition){
	  //alert(id+" - "+aryposition);
	  jQuery.ajax({
            url: "views/custom_insert_compliance_answer_popup.php",
            data:'id='+id+"&aryposition="+aryposition,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error:function (){}
        });
  }
  
  function statuspopupnew(id,table_name,status,transaction_id,workflow_id,type){
      //alert("data");
     //var table_name ="employee_leaves";
      jQuery.ajax({
            url: "views/custom_insert_status_popup_new.php",
            data:'table='+table_name+'&status='+status+'&rid='+id+'&transaction_id='+transaction_id+'&workflow_id='+workflow_id+"&type="+type,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
   }
</script>

<div class="">
	<div class="row">
		<div class="col-lg-6 col-xl-6 col-sm-12 col-12">
			<div class="row">
				<div class="col-lg-6 col-xl-6 col-sm-6 col-12">
					<div class="card social-card custom_hover_card" style="min-height: 288px;">
						<div class="card-body text-center">
							<h2 class="text-facebook m-b-20"><i class="ti-announcement "></i></h2>
							<h3 class="text-facebook f-w-700">Announcements</h3>
							<?php 
							$anouncement =  API."?mode=togetlatestannouncement";
							$anouncement_list = callAPI($anouncement);	
							$anouncement_list = json_decode($anouncement_list,1);
	                       						//print_r($anouncement_list);	  		
							?>
							<p><?php echo substr($anouncement_list['announcement_message'],0,250);?></p>
							<p class="m-b-0 m-t-15"><a  href="index.php?option=COM_VIEW_ANNOUNCEMENTS" class="btn btn-info waves-effect waves-light"><?php echo $anouncement_list['button_label']; ?></a></p>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-xl-6 col-sm-6 col-12">
					<div class="card sale-card custom_hover_card">
						<input type="hidden" id="total_tasks_achieved" value="<?php echo $total_tasks_achieve;?>"> 
						<input type="hidden" id="completed_tasks_achieved" value="<?php echo $completed_tasks_achieve;?>"> 
						<div class="card-header">
							<h5>Achievement Till Date</h5>
						</div>
						<div class="card-block text-center">
							<?php  $uimg = $reviewdataw[0]['image']!=""?$reviewdataw[0]['image']:"no-image.jpg"; ?>
							<div data-label="99%" class="radial-bar radial-bar-40 radial-bar-lg radial-bar-danger" >
								<img src="uploads/all/<?php echo $uimg; ?>" alt="User-Image">
							</div>
							<div class="row">
								<div class="col-6">
									<div class="s-caption bg-default"></div>
									<div class="s-cont d-inline-block">
										<h5 class="f-w-700 m-b-0"><?php echo $total_tasks_achieve; ?></h5>
										<p class="m-b-0">Total</p>
									</div>
								</div>
								<div class="col-6 b-l-default">
									<div class="s-caption bg-default"></div>
									<div class="s-cont d-inline-block">
										<h5 class="f-w-700 m-b-0"><?php echo $completed_tasks_achieve; ?></h5>
										<p class="m-b-0">Achieve</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-xl-6 col-sm-6 col-12">
					<div class="card new-cust-card custom_hover_card">
						<div class="card-header">
							<h5>Holidays List</h5> <label class="form-label label label-success"><?php echo $total_holidays; ?></label>

						</div>
						<div class="card-block custom-scroll" style=" height: 300px;">


							<?php 
							if(!empty($holidaydata)) {   
							foreach ($holidaydata as  $holiday) {

								$Notification_Message = $holiday['holiday_name'];
								$Notification_DateTime = $holiday['holiday_date'];
								?>
								<div class="align-middle m-b-35">
									<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($Notification_Message); ?></span>
									<div class="d-inline-block">
										<a href="#!"><h6><?php echo $Notification_Message; ?></h6></a>
										<p class="text-muted m-b-0"><?php echo $Notification_DateTime; ?></p>
									</div>
								</div>
							<?php } }else{ 

										echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
								} ?>


						</div>
					</div>
				</div>
				<div class="col-lg-6 col-xl-6 col-sm-6 col-12">
					<div class="card new-cust-card custom_hover_card">
						<div class="card-header">
							<h5> Birthdays & Anniversaries 
							</h5><span class="form-label label label-success"><?php echo $wishes_count; ?></span>
						</div>
						<div class="card-block custom-scroll" style=" height: 300px;">
							<?php
							if(!empty($emp_dob_details) AND !empty($emp_wed_details)) {           

								foreach ($emp_dob_details as  $dob) {

									$display_name = $dob['display_name'];
									$date_of_birth = $dob['date_of_birth'];
									$display_photo = $dob['image'];
									$receiver_emp_id = $dob['id'];
									$sender_emp_id = $employee_id;
									$dob = date('d/m',strtotime($date_of_birth))."/".date('y');
									$msg_title = 'Birthday on '.$dob;
									$disp_text = $display_name ."\n" . "Birthday on ".$date_of_birth;
									?>
									<div class="align-middle m-b-35">

										<?php if($display_photo) {?>
											<img src="uploads/all/<?php echo $display_photo; ?>"   class="img-radius img-50 align-top m-r-15" onclick="popupfeedmsg(<?php echo $sender_emp_id;?>,<?php echo $receiver_emp_id;?>,'<?php echo $msg_title;?>')">
										<?php } else { ?>
											<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" onclick="popupfeedmsg(<?php echo $sender_emp_id;?>,<?php echo $receiver_emp_id;?>,'<?php echo $msg_title;?>')"><?php echo initials($display_name); ?></span>
										<?php } ?>

										<div class="d-inline-block width73">
											<a href="#!" class="text-danger text-capitalize"><h6><i class="fa fa-birthday-cake float-right text-primary"></i> <?php echo $display_name; ?></h6></a>
											<p class="text-muted m-b-0"><?php echo $msg_title; ?></p>

										</div>
									</div>


									<?php  }   ?>

									<?php
									//print_r($emp_wed_details);
									//	if(!empty($emp_wed_details)) {
									
									foreach ($emp_wed_details as  $wed) {

										$display_name = $wed['display_name'];
										$wedding_date = $wed['wedding_date'];
										$display_photo = $wed['image'];
										$receiver_emp_id = $wed['id'];
										$sender_emp_id = $employee_id;
										$wed = date('d/m',strtotime($wedding_date))."/".date('y');
										$msg_title = 'Anniversary on '.$wed;
										$disp_text = $display_name ."\n" . "Birthday on ".$wedding_date;
										?>



										<div class="align-middle m-b-35">

											<?php if($display_photo) {?>
												<img src="uploads/all/<?php echo $display_photo; ?>"   class="img-radius img-50 align-top m-r-15" onclick="popupfeedmsg(<?php echo $sender_emp_id;?>,<?php echo $receiver_emp_id;?>,'<?php echo $msg_title;?>')">
											<?php } else { ?>
												<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" onclick="popupfeedmsg(<?php echo $sender_emp_id;?>,<?php echo $receiver_emp_id;?>,'<?php echo $msg_title;?>')"><?php echo initials($display_name); ?></span>
											<?php } ?>

											<div class="d-inline-block width73">
												<a href="#!" class="text-danger text-capitalize"><h6>  <i class="fa fa-mars-double float-right text-primary"></i>   <?php echo $display_name; ?></h6></a>
												<p class="text-muted m-b-0"><?php echo $msg_title; ?></p>

											</div>
										</div>


									<?php } }else{ 

										echo "<h4><i class='fa fa-warning'></i> No Birthdays Available</h4>";
								} ?>					

						</div>
					</div>
				</div>
			</div>
		</div>

				<div class="col-lg-6 col-xl-6 col-sm-12 col-12">
					<div class="col-lg-12">
						<div class="card table-card">
							<div class="card-header">
								<h5>Reviews</h5>
								<div class="card-header-right">
									<ul class="list-unstyled card-option">
										<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
										<li><i class="feather icon-maximize full-card"></i></li>
										<li><i class="feather icon-minus minimize-card"></i></li>
										<li><i class="feather icon-refresh-cw reload-card" onclick="reviewsrelods()"></i></li>
										
										<li><i class="feather icon-chevron-left open-card-option"></i></li>
									</ul>
								</div> 
							</div>
							<div class="card-block custom-scroll" style="height: 600px;">
								<div class="dt-responsive table-responsive reviewtableaax" >
									
									<?php include("views/custom_dashboard_reviews.php"); ?>
									<?php //print_r($reviewdata['data']); ?>

								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="row">
				<div class="col-xl-6">
					<!-- To Do List card start -->
					<div class="card">
						<div class="card-header">
							<h5>Tasks due in 7 days
							</h5>
							<label class="form-label label label-danger"><?php echo count((array)$count_task['due']); ?></label>
							<label class="form-label label label-warning"><?php echo count((array)$count_task['now']); ?></label>
							<label class="form-label label label-primary"><?php echo count((array)$count_task['after']); ?></label>
						</div>
						<div class="card-block  custom-scroll" style="height: 300px;">
							<div class="new-task">

								<?php 
//echo "<pre>";
//print_r($kpi_data_list);
if(!empty($kpi_data_list)){
								foreach ($kpi_data_list as  $task) {
									$rtext ='xyz';
									$Notification_Message = $task['task']['id']."-".$task['task']['name'];
									$Notification_Table_Id = $task['task']['id'];
									$Notification_Table = "employee_kpi_task_histories";
									$current_status = $task['task']['current_status'];
									$text_masege="";
									if($task['task']['task_date'] == date('Y-m-d')){
										$class_text = 'text-warning';

									}elseif(strtotime($task['task']['task_date']) < strtotime(date('Y-m-d'))){
										$class_text = 'text-danger';

									}else{
										$class_text = 'text-muted';
										$text_masege= "Due in next";
										$rtext ='ago';
									}


									$Notification_DateTime = notificationdatesdiff($task['task']['task_date']) ." ago";
                          		$created_userid = $task['task']['created_by']; //exit;
                          		$icon_initials = initials($_SESSION['users'][$created_userid]); 
                          		//onclick="statuspopup(10,22,'employee_kpi_task_histories')"
                          		//$task['view_status'] = 9001;
                          		?>

                          		<div class="to-do-list">
                          			<div class="checkbox-fade fade-in-primary" >
                          				<label class="form-label check-task " id="checkstausid<?php echo $Notification_Table_Id; ?>">
                          					<input type="checkbox"  value="" id="checkstausinput<?php echo $Notification_Table_Id; ?>" onclick="statuspopup('<?php echo $task['status_list'][0]['status']; ?>','<?php echo $Notification_Table_Id; ?>','<?php echo $Notification_Table; ?>','<?php echo $task['view_status']=="0"?"0":$_SESSION['error_message'][$task['view_status']]; ?>','task')">
                          					<span class="cr ">
                          						<i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                          					</span>
                          					<span class="<?php echo $class_text; ?>"><?php echo $Notification_Message; ?></br><small class="m-l-35 <?php echo $class_text; ?>"><?php echo $text_masege." ".str_replace($rtext,"",$Notification_DateTime); ?></small></span>
                          				</label>
                          			</div>
					</div>
				<?php } }else{ 

										echo "<h4 class='p-40 text-center'><i class='fa fa-warning'></i> No Task Available</h4>";
								} ?>
				
			</div>
		</div>
	</div>
	<!-- To Do List card end -->
</div>

<div class="col-xl-6">
	<!-- To Do List card start -->
	<div class="card">
		<div class="card-header">
			<h5>Compliance Tasks
			</h5>
			<label class="form-label label label-success">Today</label>
			
		</div>
		<div class="card-block custom-scroll" style="height: 300px;">
			<div class="new-task">
				<div class="card-block accordion-block color-accordion-block">
					<div class="color-accordion" id="color-accordion">
					<?php if(count((array)$compliancetaskhistory) !='0'){ foreach($compliancetaskhistory as $taskhistory){ 
						$check_list = json_decode($taskhistory['check_list'], 1);
						$alldone = 0;
						foreach($check_list as $check_lits){
							if($check_lits['check_value']!=''){
								$alldone++;
							}
						}
						
					?>
						<a class="accordion-msg b-none waves-effect waves-light bg-gray mb-2 f-w-600 f-16"><?php echo $taskhistory['name']; ?>  <span class="float-right" id="statusid_<?php echo $taskhistory['id']; ?>"><?php if(count((array)$check_list) == $alldone){ ?><label class="form-label label label-success">Completed</label><?php }else{ ?> <label class="form-label label label-warning">Pending</label> <?php } ?></span></a>
						<div class="accordion-desc">
							<?php foreach($check_list as $keyval=>$list){ 	
								$checkboxcl=$classcheck="";
							if($list['check_value']!=""){ 
								$taskstatus++; 
								$classcheck="check-task check-task done-task";
								$checkboxcl = "checked";
							} 
							?>
							<div class="to-do-list">
							<div class="checkbox-fade fade-in-primary" onchange="getTaskCheckList('<?php echo $taskhistory['id']; ?>','<?php echo $keyval; ?>')">
								<label class="form-label check-task <?php echo $classcheck; ?>">
									<input type="checkbox" value="" <?php echo $checkboxcl; ?>>
									<span class="cr">
										<i class="cr-icon icofont icofont-ui-check txt-primary"></i>
									</span>
									<span><?php echo $list['name'];?></span></br>
								</label>
							</div>
						</div>
						<!--	<div class="label-main">
                                <label class="form-label label label-primary"><?php echo $list['name'];?> <?php echo $list['check_value']!=""?'<i class="fa fa-thumbs-up "></i>':'<i class="fa fa-hourglass-2"></i>'; ?></label>
                            </div>  --> 
						<?php }  ?>
						</div>
					<?php } }else{ 

										echo "<h4 class='p-40 text-center'><i class='fa fa-warning'></i> No Compliances Available</h4>";
								} ?>	
					</div>
				</div>	
				
			</div>
		</div>
	</div>
	<!-- To Do List card end -->
</div>


	<!-- <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-facebook m-b-20"><i class="ti-cup"></i></h2>
                <h3 class="text-facebook f-w-700">Purchasing</h3>
                <p>Likes</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-twitter m-b-20"><i class="icon-basket-loaded"></i></h2>
                <h3 class="text-twitter f-w-700">Stores</h3>
                <p>Tweets</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="ti-crown"></i></h2>
                <h3 class="text-dribbble f-w-700">Sales</h3>
                <p>Followers</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-linkedin m-b-20"><i class="ti-briefcase"></i></h2>
                <h3 class="text-linkedin f-w-700">Budget</h3>
                <p>Followers</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>98 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-facebook m-b-20"><i class="ti-blackboard"></i></h2>
                <h3 class="text-facebook f-w-700">Assets Management</h3>
                <p>Likes</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-twitter m-b-20"><i class="fab fa-dribbble"></i></h2>
                <h3 class="text-twitter f-w-700">HR</h3>
                <p>Tweets</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="icon-wallet"></i></h2>
                <h3 class="text-dribbble f-w-700">Payroll</h3>
                <p>Followers</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-linkedin m-b-20"><i class="ti-alarm-clock"></i></h2>
                <h3 class="text-linkedin f-w-700">Attendance</h3>
                <p>Followers</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>98 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-facebook m-b-20"><i class="ti-face-smile"></i></h2>
                <h3 class="text-facebook f-w-700">Self Service</h3>
                <p>Likes</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-twitter m-b-20"><i class="ti-calendar"></i></h2>
                <h3 class="text-twitter f-w-700">Time Sheet</h3>
                <p>Tweets</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="ti-stack-overflow"></i></h2>
                <h3 class="text-dribbble f-w-700">Claims</h3>
                <p>Followers</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-linkedin m-b-20"><i class="ti-palette"></i></h2>
                <h3 class="text-linkedin f-w-700">Food Option</h3>
                <p>Followers</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>98 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-facebook m-b-20"><i class="ti-car"></i></h2>
                <h3 class="text-facebook f-w-700">Travel Option</h3>
                <p>Likes</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-twitter m-b-20"><i class="ti-notepad"></i></h2>
                <h3 class="text-twitter f-w-700">Survey</h3>
                <p>Tweets</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="ti-package"></i></h2>
                <h3 class="text-dribbble f-w-700">Polls</h3>
                <p>Followers</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-linkedin m-b-20"><i class="ti-panel"></i></h2>
                <h3 class="text-linkedin f-w-700">Assessment</h3>
                <p>Followers</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>98 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-facebook m-b-20"><i class="ti-thumb-up"></i></h2>
                <h3 class="text-facebook f-w-700">PMS</h3>
                <p>Likes</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-twitter m-b-20"><i class="ti-shine"></i></h2>
                <h3 class="text-twitter f-w-700">VMS</h3>
                <p>Tweets</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="ti-key"></i></h2>
                <h3 class="text-dribbble f-w-700">Petty Cash</h3>
                <p>Followers</p>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div> -->
</div>

<!-- <div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="text-center">
                <div class="inputGroup text-center">
                    <input id="option1" name="" type="checkbox" checked />
                    <label for="option1" class="text-center">
                        <h2 class="text-facebook m-b-20"><i class="ti-cup"></i></h2>
                        <h3 class="text-facebook f-w-700">Purchasing</h3>
                        <p>Likes</p>
                        <p class="m-b-0 m-t-15">
                            <i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days
                        </p>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="text-center">
            	<div class="inputGroup text-center">
            		<input id="option2" name="" type="checkbox" />
            		<label for="option2" class="text-center">
		                <h2 class="text-twitter m-b-20"><i class="icon-basket-loaded"></i></h2>
		                <h3 class="text-twitter f-w-700">Stores</h3>
		                <div class="infisuite-switch">
		                    <input type="checkbox" class="js-switch" checked />
		                </div>
		                <p>Likes</p>
		                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
	            	</label>
	            </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="ti-crown"></i></h2>
                <h3 class="text-dribbble f-w-700">Sales</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-linkedin m-b-20"><i class="ti-briefcase"></i></h2>
                <h3 class="text-linkedin f-w-700">Budget</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>98 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-facebook m-b-20"><i class="ti-blackboard"></i></h2>
                <h3 class="text-facebook f-w-700">Assets Management</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-twitter m-b-20"><i class="fab fa-dribbble"></i></h2>
                <h3 class="text-twitter f-w-700">HR</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="icon-wallet"></i></h2>
                <h3 class="text-dribbble f-w-700">Payroll</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-linkedin m-b-20"><i class="ti-alarm-clock"></i></h2>
                <h3 class="text-linkedin f-w-700">Attendance</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>98 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-facebook m-b-20"><i class="ti-face-smile"></i></h2>
                <h3 class="text-facebook f-w-700">Self Service</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-twitter m-b-20"><i class="ti-calendar"></i></h2>
                <h3 class="text-twitter f-w-700">Time Sheet</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="ti-stack-overflow"></i></h2>
                <h3 class="text-dribbble f-w-700">Claims</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-linkedin m-b-20"><i class="ti-palette"></i></h2>
                <h3 class="text-linkedin f-w-700">Food Option</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>98 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-facebook m-b-20"><i class="ti-car"></i></h2>
                <h3 class="text-facebook f-w-700">Travel Option</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-twitter m-b-20"><i class="ti-notepad"></i></h2>
                <h3 class="text-twitter f-w-700">Survey</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="ti-package"></i></h2>
                <h3 class="text-dribbble f-w-700">Polls</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-linkedin m-b-20"><i class="ti-panel"></i></h2>
                <h3 class="text-linkedin f-w-700">Assessment</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>98 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-facebook m-b-20"><i class="ti-thumb-up"></i></h2>
                <h3 class="text-facebook f-w-700">PMS</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-twitter m-b-20"><i class="ti-shine"></i></h2>
                <h3 class="text-twitter f-w-700">VMS</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card social-card">
            <div class="card-body text-center">
                <h2 class="text-dribbble m-b-20"><i class="ti-key"></i></h2>
                <h3 class="text-dribbble f-w-700">Petty Cash</h3>
                <div class="infisuite-switch">
                    <input type="checkbox" class="js-switch" checked />
                </div>
                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>623 from last 7 days</p>
            </div>
        </div>
    </div>

</div> -->


</div>

 <div id="kt_modal_KTDatatable_remotenewdemo" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"  aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1125">
            
        </div>
    </div>
</div>



<!-- <div class="dropdown-primary dropdown setting-dropdown">
    <div class="userlist-box dropdown-toggle" data-bs-toggle="dropdown">
        <i class="ti-settings"></i>
    </div>
</div>

<div class="showChat_inner">
<div class="d-flex chat-inner-header">
<a class="back_chatBox">
<i class="feather icon-x"></i> Josephin Doe
</a>
</div>
<div class="main-friend-chat">
<div class="d-flex chat-messages">
<a class="media-left photo-table" href="#!">
<div class="flex-shrink-0">
<img class="media-object img-radius img-radius m-t-5" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder image">
</div>
</a>
<div class="flex-grow-1 chat-menu-content">
<div class="">
<p class="chat-cont">I'm just looking around. Will you tell me something about yourself?</p>
</div>
<p class="chat-time">8:20 a.m.</p>
</div>
</div>
<div class="d-flex chat-messages">
<div class="flex-grow-1 chat-menu-reply">
<div class="">
<p class="chat-cont">Ohh! very nice</p>
</div>
<p class="chat-time">8:22 a.m.</p>
</div>
</div>
<div class="d-flex chat-messages">
<a class="media-left photo-table" href="#!">
<div class="flex-shrink-0">
<img class="media-object img-radius img-radius m-t-5" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder image">
</div>
</a>
<div class="media-body chat-menu-content">
<div class="">
<p class="chat-cont">can you come with me?</p>
</div>
<p class="chat-time">8:20 a.m.</p>
</div>
</div>
</div>
<div class="chat-reply-box">
<div class="right-icon-control">
<div class="input-group input-group-button">
<input type="text" class="form-control" placeholder="Write hear . . ">
<button class="btn btn-primary waves-effect waves-light" type="button">
<i class="feather icon-message-circle"></i>
</button>
</div>
</div>
</div>
</div> -->