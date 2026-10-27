<?php 
$display_id = $_GET['NI']!="" ? $_GET['NI'] : '0';
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];
 //echo $candidate_id; //exit;
$table_name = "candidates";
 //$candidate_id = 2;
$whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'id'=>$candidate_id);
$table = 'candidates';
$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=gettabledetailswithconditions&table=".$table."&data=".$vdata; 
   //echo $url; exit;
$response = callAPI($url);
$data = json_decode($response,1);
$data = $data[0];
  // print_r($data); //print_r($whrcon);//exit;
$i=1; $k=1;
foreach ($data as  $value) {
	if($value!="" ){
		$k++;
	}	
	$i++; }
	//$pp = round(($k/$i)*(100));
	if($i !=0 AND $i!=""){
		$pp = round(($k/$i)*(100));
	}else{
		$pp =$k*100;
	}
   //print_r($data);// exit;
	$dispath = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$candidate_id."/".$data['image'];
	?>
	<script type="text/javascript">
		function displaystausdrop(id,table,status_id){
       //alert("id");
       $.ajax({
       	url: "ajax.php",
       	data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
       	type: "POST",
       	success:function(data){
       		console.log(data);
       		$("#dropdownlist").html(data);
       	},
       	error:function (){}
       });
       
   }
   function statuspopup(status,id){
     //alert("data");
    //var table_name = document.getElementById('duplicate_table_name').value;
    jQuery.ajax({
    	url: "views/custom_insert_status_popup.php",
    	data:'table=candidates'+'&status='+status+'&rid='+id,
    	type: "POST",
    	success:function(data){
    		$('#popup123').html(data);
    		$('#kt_modal_KTDatatable_remote123').modal({ show: true});
    	},
    	error:function (){}
    });
}
</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<div class="card mb-1 mb-xl-10">
<?php if((isset($data[id]) AND $_GET['Rid']!="") || !isset($_GET['Rid'])){ ?> 

			<div class="card-body pt-9 pb-0">
				<div class="d-flex flex-wrap flex-sm-nowrap mb-3">
					<div class="flex-shrink-0 mr-7 mt-lg-0 mt-3 mr-10">
						<?php if($data['image']!=""){ ?>
							<div class="symbol symbol-50 symbol-lg-120">
								<img src="<?php echo $dispath; ?>" class="img-120 brockenimage" alt="image"  onerror="this.src='uploads/all/no-image.jpg';"/>
							</div>
						<?php }else{?>
							<div class="symbol symbol-50 symbol-lg-120 symbol-primary ">
								<span class="font-size-h3 symbol-label font-weight-boldest img-120 p-20 f-40"><?php echo $data['display_name']!=""?initials($data['display_name']):"NA"; ?></span>
							</div>
						<?php } ?>
					</div>
					<!--begin: Info-->
					<div class="flex-grow-1">
						<!--begin::Title-->
						<div class="d-flex justify-content-between flex-wrap mt-1">
							<div class="d-flex mr-3">
								<a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-10 f-16 f-w-600"><?php echo $data['display_name'];?></a>
								<a href="#">
									<i class="flaticon2-correct text-success font-size-h5 mr-2"></i>
								</a>
								<a href="<?php echo $data['soc_facebook']; ?>" target="_blank" class="btn waves-effect waves-light btn-facebook btn-icon-30 m-l-5">
									<i class="icofont icofont-social-facebook"></i>
								</a>
								<a href="<?php echo $data['soc_twitter']; ?>" target="_blank" class="btn waves-effect waves-light btn-twitter btn-icon-30 m-l-5">
									<i class="icofont icofont-social-twitter"></i>
								</a>
								<a href="<?php echo $data['soc_linkedin']; ?>" target="_blank" class="btn waves-effect waves-light btn-linkedin btn-icon-30 m-l-5">
									<i class="icofont icofont-brand-linkedin "></i>
								</a>

							</div>

							<div class="my-lg-0 my-3">

								<?php if($_GET['Rid']!="") { $nmm= ViewStatusValue($data['current_status'],'candidates');  ?>
								<div class="btn-group">
									<button onclick="displaystausdrop(<?php echo $role['id']; ?>,<?php echo "'".$table_name."'"; ?>,<?php echo $data['current_status']; ?>)" class="btn btn-primary dropdown-bs-toggle" style="color: <?php echo "#". $nmm['color']; ?>;    background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $nmm['name']; ?> </button>
									<div class="dropdown-menu" id="dropdownlist">
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<!--end::Title-->
					<!--begin::Content-->
					<div class="d-flex flex-wrap justify-content-between mt-1">
						<div class="d-flex flex-column flex-grow-1 pr-8">
							<div class="d-flex flex-wrap mb-2 mt-2">
								<?php if($data['email_id']!=""){ ?>
									<a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
										<i class="ti-email f-14 text-primary"></i>&nbsp;<?php echo $data['email_id'];?></a>
									<?php } if($data['phone']!=""){  ?>	
										<a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
											<i class="fa fa-phone f-14 text-primary"></i>&nbsp;<?php echo $data['phone'];?></a>
										<?php } if($data['current_location']!=""){ ?>		
											<a href="#" class="text-dark-50 text-hover-primary font-weight-bold">
												<i class="ti-location-pin f-14 text-primary"></i>&nbsp;<?php echo $data['current_location'];?></a>
											<?php } ?>	
										</div>
										<?php if($data['address']!=""){ ?>
											<span class="font-weight-bold text-dark-50"><b class="f-w-600">Address: </b><?php echo $data['address']." ".$data['post_code'];?> 
										</span>
									<?php } ?>
									<span class="font-weight-bold text-dark-50">
										<?php if($data['total_experiance']!=""){  ?>
											<b class="f-w-600">Total Experiance : </b><?php echo $data['total_experiance']." Years";?>&nbsp;
										<?php } if($data['notice_period']!=""){  ?>
											<b class="f-w-600">Notice Period : </b><?php echo $data['notice_period']." Days";?>
										<?php } ?>
									</span>
								</div>

								<div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3" style="width: 20%;">
									<div class="d-flex justify-content-between w-100 mt-auto mb-2">
										<span class="fw-bold fs-6 text-gray-400">Profile Completion </span>
										<span class="fw-bolder fs-6"> &nbsp;&nbsp;<?php echo $pp; ?>%</span>
									</div>

									<div class="col-md-12">
										<div class="progress  progress-xs">
											<div class="progress-bar progress-bar-pink " role="progressbar" style="width: <?php echo $pp; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>	


								</div>
							</div>
							<!--end::Content-->
						</div>
						<!--end::Info-->
					</div>
					<!--end::Details-->
					
								</div>
							</div>
							<!--end::Navbar-->	

							<div class="card">
								<div class="card-body">
										<!--begin::Navs-->
									<div class="d-flex overflow-auto h-55px">

										<ul class="nav md-tabs nav-bold nav-tabs-line nav-line-tabs-3x border-transparent fs-5 fw-bolder flex-nowrap responsive-tab infisuite_tab">
											<!--begin::Nav item-->
											<?php 
											$display_array = array('COM_LIST_CANDIDATE_INFO','COM_INSERT_REQUIRED_DOCUMENTS','COM_LIST_CANDIDATE_DOCUMENTS','COM_INSERT_CANDIDATE_OFFER','COM_LIST_CANDIDATE_OFFER','COM_INSERT_JOIN_REQUIRED_DOCUMENTS','COM_LIST_JOIN_REQUIRED_DOCUMENTS','COM_TRANSACTION_ACTIVITY_SCHEDULES','COM_LIST_CANDIDATE_QUERY');
											$hr_only =  array('COM_INSERT_REQUIRED_DOCUMENTS', 'COM_INSERT_EMPLOYEE_CONTRACT', 'COM_INSERT_CANDIDATE_OFFER', 'COM_INSERT_JOIN_REQUIRED_DOCUMENTS', 'COM_TRANSACTION_ACTIVITY_SCHEDULES');
											$display_name = array('Overview','Required Documents','Documents','Genrate Offer','View Offer','Joining Required Documents','Joining  Documents','Process Tasks','Communications');

											?>

											<?php $option_name=""; foreach ($display_array as $key=>$disarray) { 
												$active =0; 
												if($display_id==$key){ $active =1; $option_name =$disarray; }
												if($_SESSION['user_type'] != 4 ){  ?>
													<li class="nav-item">
														<a class="nav-link text-active-primary me-6 <?php if($active==1){ echo "active"; } ?>"  href="index.php?option=COM_INSERT_CANDIDATE&NI=<?php echo $key; ?>&Rid=<?php echo $_GET['Rid'];?>">
															<?php echo $display_name[$key] ?></a>
														</li>
													<?php }else{
														if(!in_array($disarray, (array)$hr_only)){ ?>
															<li class="nav-item">
																<a class="nav-link text-active-primary me-6 <?php if($active==1){ echo "active"; } ?>"  href="index.php?option=COM_INSERT_CANDIDATE&NI=<?php echo $key; ?>&Rid=<?php echo $_GET['Rid'];?>">
																	<?php echo $display_name[$key] ?></a>

																</li>
															<?php } } } ?>

														</ul>
													</div>
													<!--begin::Navs-->
								</div>
							</div>


							<!--begin::Sign-in Method-->
							<div class="card mb-5 ">
								<?php $_GET['option'] = $option_name;
								if($_GET['option']!=""){
									include("includes/controller.php");
									$dispage = str_replace("views/", '', $disptemp);
						//echo $dispage;
									include($dispage);
								}else{
									include("custom_wizard_dash_board.php");
								}
								?>							
							</div>

<?php  }else{  ?>
					

<div class="card-body pt-9 pb-0 text-center">
	<h2 class="text-info"><i class='fa fa-warning'></i> Candidate Not Found! Please Contact Admin</h2>
</div>
<?php } ?>

						</div>
					</div>





					<div id="kt_modal_KTDatatable_remote123" class="modal fade" role="dialog" aria-hidden="true">
						<div class="modal-dialog " >
							<div class="modal-content"  id="popup123">
							</div>
						</div>
					</div>