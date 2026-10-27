<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$reviewurl = API."?mode=getriviewsdata&response_type=0";
$reviewdata = callAPI($reviewurl);
$reviewdata = json_decode($reviewdata, 1);
?>
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
	<!-- Tab panes -->
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



















		