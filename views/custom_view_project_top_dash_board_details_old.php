<?php
$curl1 = API."?mode=togettopprojecttasklist&userid=".$_SESSION['userid']."&admin_type=".$_SESSION['admin_type']; 
//echo $curl1;
$cudata1 = callAPI($curl1);
$result = json_decode($cudata1,1);
//echo "<pre>"; print_r($result['tasks']); echo "</pre>";
$statuslist  = getstatusprogresslist('projects');
foreach($statuslist as $statuslist){
	$statusnamelist[$statuslist['status']] = $statuslist['name'];	
}
//echo "<pre>"; print_r($result); echo "</pre>"; 
$aciveid = $_GET['TAB']!="" ? $_GET['TAB'] : 0 ;
$tab_list = array('Total Projects', 'Yet To Start Projects', 'In-Progress Projects', 'Completed Projects');
$cctask = $result['tasks'];
?>

<div class="d-flex flex-column-fluid card mrgn-tp">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="" id="kt_page_sticky_card">
					<div class="card-header mb-3">
						<h5> Projects </h5>
						<div class="card-header-right">								
							<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
								<i class="icofont icofont-reply"></i>
								Back
							</a>
						</div>
					</div>
					<div class="card-body">						
						<div class="row"> 
							<div class="col-lg-12">
								<ul class="nav md-tabs infisuite_tab" role="tablist">
									<?php foreach($tab_list as $key=>$tabs){ ?>
										<li class="nav-item">
											<a class="nav-link f-w-600 <?php if($aciveid==$key){ echo "active"; } ?>" data-bs-toggle="tab" href="#tab<?php echo $key; ?>" role="tab" aria-selected="true"><?php echo $tabs; ?></a>
											<div class="slide"></div>
										</li>
									<?php } ?>
								</ul>
							</div>
							<div class="tab-content tabs card-body">
								<?php foreach($tab_list as $key=>$tabs){ ?>
									<div class="tab-pane <?php if($aciveid==$key){ echo "active"; } ?> " id="tab<?php echo $key; ?>" role="tabpanel">
										<div class="row">
											<?php 
											if(count($result) !=0){ $m=1; $tcchk = 0;
												foreach ($result['projects'] as  $res) {  $check=0;
													if($key == 0){ 
														$check=1;
													}
													if($key == 1){ 
														if($res['current_status'] == '0'){
															$check=1;
														}
													}
													if($key == 2){ 
														if(!in_array($res['current_status'],array('0','199') )){
															$check=1;
														}
													}
													if($key == 3){ 
														if($res['current_status'] == '199'){
															$check=1;
														}
													}
													$nridw = "id:".$res['id'];
													$uimg = $res['project_logo']!=""?$res['project_logo']:"no-image.jpg";
													$prencentage = 0;
													if($cctask[$res['id']]['total_project']>0 && $cctask[$res['id']]['complete']==0){
														$prencentage = 100;
													}
													if($cctask[$res['id']]['complete']!=0){

														 $prencentage = ($cctask[$res['id']]['complete']/$cctask[$res['id']]['total_project'])*100;
													}
													//echo "completed".$cctask[$res['id']]['complete'].", total".$cctask[$res['id']]['total_project'];
													$prencentage = round($prencentage / 10) * 10;
													if($prencentage <50){
														$prencentage = 100-$prencentage ;
													}
													

													if($check==1){  $tcchk=1;
														$statucolorname = $stausresult[$res['current_status']];
													?>
														<div class="col-xl-3 col-md-6">
															<div class="card sale-card ">
																<div class="ribbon ribbon-top-right" >
																	<span class="color-primary" style="color: <?php echo $_SESSION['status'][$res['current_status']]['color'] ?>;    background:  <?php echo $_SESSION['status'][$res['current_status']]['background_color']; ?>; border-color: <?php echo $_SESSION['status'][$res['current_status']]['background_color']; ?>;"><?php echo $statusnamelist[$res['current_status']]; ?>
																	</span>
																</div>			
																<div class="card-header mt-2">
																	<h5><?php echo $res['project_name']; ?></h5>
																</div>
																<div class="card-block text-center">
																	<div data-label="<?php echo $prencentage; ?>%" class="radial-bar radial-bar-<?php echo $prencentage; ?> radial-bar-lg radial-bar-danger">
																		<img src="uploads/all/<?php echo $uimg; ?>" alt="User-Image">
																	</div>
																	<div class="row">
																		<div class="col-6 ">
																			<a  <?php if(in_array('ADMIN',$_SESSION['role_ids']) || $res['project_role_id'] != 2 ){ ?> href="index.php?option=COM_VIEW_PROJECT&RID=<?php echo  $res['id']; ?>"
																			<?php }else{ ?> 
																				style ="background-color: #CCCCCC;cursor: not-allowed;"
																				<?php } ?> class="btn btn-success  waves-effect waves-light  f-12">Over View
																			</a>
																		</div>
																		<div class="col-6 b-l-default">
																			<a href="index.php?option=COM_VIEW_PROJECT_KANBAN&RID=<?php echo  $res['id']; ?>" class="btn btn-info waves-effect waves-light  f-12"> View Task</a>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													<?php } ?>
												<?php } ?>
											<?php }else{ ?>
												<div class="card-px text-center py-20 m-t-30">
													<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Data </p>   
													<div class="text-center px-4">
														<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
													</div>   
												</div>
											 <?php } ?>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>