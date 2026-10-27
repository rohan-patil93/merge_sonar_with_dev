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
 $vurl = API."?mode=togetkpitaskstatus&company_id=".$_SESSION['company_id']."&is_active=0&emp_id=".$_SESSION['emp_id']."&balance_date=".$yesterday_date."&assesment_year=".$_SESSION['config']['assessment_year'];

 $configesponse = callAPI($vurl); 
 $kpi_tasks = json_decode($configesponse,1);
 $total_tasks_achieve = 15;
 $completed_tasks_achieve = 30;
 $employee_id = $kpi_data[0]['emp_id'];
 $emp_dob_details = nearestDateFirstShow($kpi_tasks['emp_dob_details'],'date_of_birth');
 $emp_wed_details = nearestDateFirstShow($kpi_tasks['emp_wed_details'],'wedding_date');
 $wishes_count = count((array)$emp_dob_details) + count((array)$emp_wed_details);

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
   		

   		$total_tasks = count((array)$kpi_data_list);




   		$holiday_list = array();
   		$holidaywr["where"] = array('company_id'=> $_SESSION['company_id'],'DATE_FORMAT(holiday_date,"%Y")'=>$current_year);
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

   		$reviewwrw["where"] = array('company_id'=>$_SESSION['company_id'],'id'=>$_SESSION['emp_id']);
   		$reviewwrw["field"] = "image";
   		$reviewconw = urlencode(json_encode($reviewwrw));
   		$reviewurlw = API."?mode=gettabledetailswithconditions&table=employees&data=".$reviewconw;
   		$reviewdataw= callAPI($reviewurlw);
   		$reviewdataw = json_decode($reviewdataw, 1);

   		$reviewurl = API."?mode=getriviewsdata&response_type=0";
   		$reviewdata = callAPI($reviewurl);
   		$reviewdata = json_decode($reviewdata, 1);
   		$stausresult = ViewStatusData();

   		$listcompliancesurl = API."?mode=getcompliancesdashbord&type=employeetask&emp_id=".$_SESSION['emp_id']."&date=".date('Y-m-d');
   		$listcompliancesres = callAPI($listcompliancesurl);
   		$listcompliances = json_decode($listcompliancesres, 1);
   		$compliancetaskhistory = $listcompliances['compliance_task_histories']; 
   		$reviewurl = API."?mode=getriviewsdata&response_type=0";
   		$reviewdata = callAPI($reviewurl);
   		$reviewdata = json_decode($reviewdata, 1);
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
   		<?php $awdith =3; ?>
   		<div class="row col-lg-12" >
   		<div class="col-lg-12">
   			<div class="col-lg-12 row">
				<div class="col-lg-<?php echo $awdith; ?> ">
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
							<p class="m-b-0 m-t-15">
								<a  href="index.php?option=COM_VIEW_ANNOUNCEMENTS" class="btn btn-info waves-effect waves-light">
									<?php echo $anouncement_list['button_label']; ?>
								</a>
							</p>
						</div>
					</div>
				</div>
				<div class="col-lg-<?php echo $awdith; ?> ">
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
								<?php } 
							}else{ 
									echo "<h4><i class='fa fa-warning'></i> No Birthdays Available</h4>";
							} ?>				
						</div>
					</div>
				</div>
				<div class="col-lg-<?php echo $awdith; ?>">
					<div class="card new-cust-card custom_hover_card">
						<div class="card-header">
							<h5>Holidays List</h5>
							 <label class="form-label label label-success"><?php echo $total_holidays; ?></label>
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
								<?php } 
							}else{ 
								echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
							} ?>
						</div>
					</div>
				</div>
				<div class="col-lg-<?php echo $awdith; ?>">
					<div class="card new-cust-card custom_hover_card">
						<div class="card-header">
							<h5>On Leaves</h5>
							 <label class="form-label label label-success"><?php echo $total_holidays; ?></label>
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
								<?php } 
							}else{ 
								echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
							} ?>
						</div>
					</div>
				</div>
				<div class="col-lg-<?php echo $awdith; ?>">
					<div class="card new-cust-card custom_hover_card">
						<div class="card-header">
							<h5>Attedance</h5>
							 <label class="form-label label label-success"><?php echo $total_holidays; ?></label>
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
								<?php } 
							}else{ 
								echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
							} ?>
						</div>
					</div>
				</div>
				<div class="col-lg-<?php echo $awdith; ?>">
					<div class="card new-cust-card custom_hover_card">
						<div class="card-header">
							<h5>Recognition</h5>
							 <label class="form-label label label-success"><?php echo $total_holidays; ?></label>
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
								<?php } 
							}else{ 
								echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
							} ?>
						</div>
					</div>
				</div>
				<div class="col-lg-<?php echo $awdith; ?>">
					<div class="card new-cust-card custom_hover_card">
						<div class="card-header">
							<h5>Tasks</h5>
							 <label class="form-label label label-success"><?php echo $total_holidays; ?></label>
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
								<?php } 
							}else{ 
								echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
							} ?>
						</div>
					</div>
				</div>
				<div class="col-lg-<?php echo $awdith; ?>">
					<div class="card new-cust-card custom_hover_card">
						<div class="card-header">
							<h5>Welecome New Employee</h5>
							 <label class="form-label label label-success"><?php echo $total_holidays; ?></label>
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
								<?php } 
							}else{ 
								echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
							} ?>
						</div>
					</div>
				</div>

				</div>
			</div>
			<div class="col-lg-6">
				<div class="col-lg-12">
					<div class="card table-card">
						<div class="card-header">
							<h5>Reviews</h5>
						</div>
						<div class="card-block custom-scroll" style="height: 600px;">
							<div>
								<div class="col-lg-12 col-xl-12">
									<ul class="nav  md-tabs infisuite_tab mt-2 p-2" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" data-bs-toggle="tab" href="#home7pending" role="tab"><i class="fa fa-hourglass-2"></i>  Pending  <label class="form-label badge bg-danger"><?php echo count((array)$reviewdata['pending']); ?></label></a>
											<div class="slide"></div>
										</li>

										<li class="nav-item">
											<a class="nav-link" data-bs-toggle="tab" href="#profile7approve" role="tab"><i class="fa fa-thumbs-up"></i> Approve  <label class="form-label badge bg-warning ml-2"><?php echo count((array)$reviewdata['approve']); ?></label></a>
											<div class="slide"></div>
										</li>
									</ul>
									<div class="tab-content card-block">
										<div class="tab-pane active" id="home7pending" role="tabpanel">
											<table class="table table-hover m-b-0">
												<thead>
													<tr>
														<th></th>
														<th>Description</th>
														<th>Status</th>
														<!-- 	<th>Actions</th> -->
													</tr>
												</thead>
												<tbody>
													<?php 
													if(!empty($reviewdata['pending'])){ 	foreach ($reviewdata['pending'] as  $reviewda){
														$Notification_DateTime = notificationdatesdiff($reviewda['request_date']) ." ago";
														$Notification_Table = $reviewda['table'];
														$Notification_Table_Id = $reviewda['transaction_id'];
														if(date('Y-m-d') == date('Y-m-d',strtotime($reviewda['request_date']))){
															$class_text = 'text-warning';
														}else{
															$class_text = 'text-danger';
														}
														$getpagename = getPagenametotable($reviewda['table'],'approval_page_name');
														if($getpagename != ""){
															$pagename = $getpagename;
														}else{
															$pagename = "COM_INSERT_".ucwords($reviewda['table']);
														}			
														$nrid = deocdeId("id:".$reviewda['transaction_id']);
														$link = "index.php?option=".$pagename."&Rid=".$nrid;
														$icon_initials = initials($_SESSION['users'][$reviewda['created_by']]); 
														$icon_initials_name = $_SESSION['users'][$reviewda['created_by']];
														$current_status = $reviewda['current_status'];
														$Notification_Message = ucwords(str_replace("_"," ",($reviewda['table'])))." - ".$icon_initials_name;
														?>		
														<tr>
															<td>
																<span  alt="user image" class="img-radius img-50 align-top m-r-15 custom_txt_round p-2 f-12 text-center" ><?php echo $icon_initials; ?></span>
															</td>
															<td>
																<div class="d-inline-block align-middle display-flex">
																	<div class="d-inline-block">
																		<a href="<?php echo $link;?>" class="text-hover-primary mb-0 font-size-sm <?php echo $class_text; ?>"><h6><?php echo $Notification_Message; ?></h6></a>
																		<p class="text-muted m-b-0 <?php echo $class_text; ?>"><?php echo $Notification_DateTime; ?></p>
																	</div>
																</div>
															</td>
															<td>
																<div class="reviews_status">
																	<?php foreach($reviewda['status'] as $key => $status){ ?>
																		<label class="form-label badge" style="cursor: pointer; background-color: <?php echo $stausresult[$status['status']]['background_color'][0] == '#'?$stausresult[$status['status']]['background_color']:"#".$stausresult[$status['status']]['background_color']; ?>; color:<?php echo $stausresult[$status['status']]['color'][0] =='#'?$stausresult[$status['status']]['color']:'#'.$stausresult[$status['status']]['color']; ?>"  onclick="statuspopupnew(<?php echo $reviewda['transaction_id']; ?>,'<?php echo $reviewda['table']; ?>',<?php echo $status['status']; ?>,<?php echo $status['transaction_id']; ?>,<?php echo $status['workflow_id']; ?>,'<?php echo $key; ?>')"><?php echo  $status['action_label']; ?></label>
																	<?php } ?>
																	<label class="form-label badge" style="background-color: <?php echo $stausresult[ST_SEND_BACK]['background_color'] ?>; color: <?php echo $stausresult[ST_SEND_BACK]['color'] ?>; " onclick="statuspopupnew(<?php echo $reviewda['transaction_id']; ?>,'<?php echo $reviewda['table']; ?>',<?php echo ST_SEND_BACK; ?>,<?php echo $status['transaction_id']; ?>,<?php echo $status['workflow_id']; ?>,'<?php echo $key; ?>')"><?php echo $stausresult[ST_SEND_BACK]['name']; ?></label>

																</div>
															</td>
														</tr>
														<?php  $k++; } } else{ 
															echo "<tr><td colspan='3'><h4 class='p-40 text-center'><i class='fa fa-warning'></i> No Reviews Available</h4></td></tr>";
														} ?>
													</tbody>
												</table>
											</div>
											<div class="tab-pane" id="profile7approve" role="tabpanel">
												<table class="table table-hover m-b-0">
													<thead>
														<tr>
															<th></th>
															<th>Description</th>
															<th>Status</th>
															<!-- 	<th>Actions</th> -->
														</tr>
													</thead>
													<tbody>
														<?php 
														if(!empty($reviewdata['approve'])){  foreach ($reviewdata['approve'] as $reviewda){
															$Notification_DateTime = notificationdatesdiff($reviewda['request_date']) ." ago";
															$Notification_Table = $reviewda['table'];
															$Notification_Table_Id = $reviewda['transaction_id'];
															if(date('Y-m-d') == date('Y-m-d',strtotime($reviewda['request_date']))){
																$class_text = 'text-warning';
															}else{
																$class_text = 'text-danger';
															}
															$getpagename = getPagenametotable($reviewda['table'],'approval_page_name');
															if($getpagename != ""){
																$pagename = $getpagename;
															}else{
																$pagename = "COM_INSERT_".ucwords($reviewda['table']);
															}
															$nrid = deocdeId("id:".$reviewda['transaction_id']);
															$link = "index.php?option=".$pagename."&Rid=".$nrid;
															$icon_initials = initials($_SESSION['users'][$reviewda['created_by']]); 
															$icon_initials_name = $_SESSION['users'][$reviewda['created_by']];
															$current_status = $reviewda['current_status'];
															$Notification_Message = ucwords(str_replace("_"," ",($reviewda['table'])))." - ".$icon_initials_name;
															?>		
															<tr>
																<td>
																	<span  alt="user image" class="img-radius img-50 align-top m-r-15 custom_txt_round p-2 f-12 text-center" ><?php echo $icon_initials; ?></span>
																</td>
																<td>
																	<div class="d-inline-block align-middle display-flex">
																		<div class="d-inline-block">
																			<a href="<?php echo $link;?>" class="text-hover-primary mb-0 font-size-sm <?php echo $class_text; ?>"><h6><?php echo $Notification_Message; ?></h6></a>
																			<p class="text-muted m-b-0 <?php echo $class_text; ?>"><?php echo $Notification_DateTime; ?></p>
																		</div>
																	</div>
																</td>
																<td>
																	<div class="reviews_status">
																		<label class="form-label badge" style="background-color: <?php echo $stausresult[$reviewda['currnet_status']]['background_color'] ?>; color: <?php echo $stausresult[$reviewda['currnet_status']]['color'] ?>; " ><?php echo $stausresult[$reviewda['currnet_status']]['name']; ?></label>

																	</div>
																</td>
															</tr>
															<?php  $k++; } } else{ 
																echo "<tr><td colspan='3'><h4 class='p-40 text-center'><i class='fa fa-warning'></i> No Reviews Available</h4></td></tr>";
															} ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
		</div>
		</div>
		</div></div>
		</div>

</div>
		</div>




