<?php 
$display_id = $_GET['NI']!="" ? $_GET['NI'] : '1';
//print_r($_SESSION['employee']); //exit;
$i=1; $k=1;
foreach ($_SESSION['employee'] as  $value) {
	if($value!=""){
		$k++;
	}	
$i++; }
$pp = round(($k/$i)*(100));

	$vdata1["where"] = array('id'=> $_SESSION['employee']['designation_id']);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=designations&data=".$vdata; 
    $configesponse = callAPI($vurl);
	$desig = json_decode($configesponse,1);
	$desig = $desig[0];

	$vdata1["where"] = array('id'=> $_SESSION['employee']['location_id']); 
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$vdata; 
    $configesponse = callAPI($vurl);
	$location = json_decode($configesponse,1);
	$location = $location[0];

	$vdata1["where"] = array('emp_id'=> $_SESSION['emp_id'],'is_active'=>'0'); 
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=emp_skills&data=".$vdata; 
    $configesponse = callAPI($vurl);
	$empskills = json_decode($configesponse,1);


    $vurl = API."?mode=gettabledetailswithconditions&table=skills"; 
    $configesponse = callAPI($vurl);
	$skills = json_decode($configesponse,1);
	
	foreach ($skills as  $value) {
		$skill[$value['id']] = $value;
	}
	$vurl = API."?mode=employeeshift&employee_id=".$_SESSION['emp_id']; 
	//echo $vurl;
    $configesponse = callAPI($vurl);
	$shift = json_decode($configesponse,1);
	$vdata1["where"] = array('reports_to'=> $_SESSION['userid'],'is_active'=> 0);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=application_users&data=".$vdata; 
    $res_team = callAPI($vurl);
  	$res_team = json_decode($res_team,1);
//	print_r($shift);// exit;
include("new_from_submit.php");
	//echo "<pre>"; print_r($skills); exit;
	$vdata1["where"] = array('emp_id'=> $_SESSION['emp_id'],'is_active'=>'0'); 
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=employee_food_requests&data=".$vdata; 
    $configesponse = callAPI($vurl);
	$emp_food_options = json_decode($configesponse,1);
	$emp_food = array();
	foreach($emp_food_options as $emp_food_option){
		$emp_food[] = $emp_food_option['food_options'];
	}
  // print_r($emp_food);
	$vdata1["where"] = array('location_id'=> $_SESSION['employee']['location_id'],'is_active'=>'0'); 
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=food_options&data=".$vdata; 
    $configesponse = callAPI($vurl);
	$food_options = json_decode($configesponse,1);
	
	$vdata1["where"] = array('employee_id'=> $_SESSION['emp_id'],'is_active'=>'0'); 
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=employee_transport_requests&data=".$vdata; 
    $configesponse = callAPI($vurl);
	$emptransport = json_decode($configesponse,1);
	$emptransport = $emptransport['0'];
	
	//print_r($emptransport);
?>
<script type="text/javascript">
    function checkemptransportrequest(emp_id,val){
        if(val !=""){
            $.ajax({
	        url: "ajax.php",
	        data:'page=updatedynamicfied&table=employee_transport_requests&hid='+val+'&fid=is_active&rid=1',
	        type: "POST",
	        success:function(data){
	        	console.log(data);
	           // $("#dropdownlist_"+id).html(data);
	        },
	        error:function (){}
    	});
        }else{
		//alert("insert");
    		jQuery.ajax({
                url: "views/new_from.php",
                data:'option=COM_INSERT_EMPLOYEE_TRANSPORT_REQUESTS&emp_id='+emp_id+'&location_id='+$('#location_id').val(),
                type: "POST",
                success:function(data){
                    $('#popup2').html(data);
                    $('#kt_modal_KTDatatable_remote1').modal({ show: true});
                },
                error:function (){}
            });
        }
	}
    function checkempfoodoption(val,emp_id){
        ///alert('Ravi');
        $.ajax({
	        url: "ajax.php",
	        data:'page=insertanupdateempfoodoption&food_option='+val+'&emp_id='+emp_id+'&location_id='+$('#location_id').val()+'&start_date='+$('#start_date').val(),
	        type: "POST",
	        success:function(data){
	        	console.log(data);
	           // $("#dropdownlist_"+id).html(data);
	        },
	        error:function (){}
    	});
        
    }
	function dynamicinsertpopup(option){
		//alert("insert");
		jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option+'&emp_id=1',
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true});
            },
            error:function (){}
        });
	}
</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<?php if (count($saidnav) >0 ){ ?>
								<!--begin::Navbar-->
								<div class="card  mb-xl-10">
									<div class="card-body pt-9 pb-0">
										<!--begin::Details-->
										<div class="d-flex flex-wrap flex-sm-nowrap mb-3">
											<div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
												<?php if( $_SESSION['employee']['image']!=""){ ?>
												<div class="symbol symbol-50 symbol-lg-120">
													<img src="uploads/all/<?php echo $_SESSION['employee']['image']; ?>" alt="image" />
												</div>
											<?php }else{ ?>
												<div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
													<span class="font-size-h3 symbol-label font-weight-boldest"><?php echo initials($_SESSION['employee']['display_name']); ?></span>
												</div>
											<?php } ?>
												<div class="d-flex align-items-center w-100px w-sm-120px flex-column mt-3 ">
														<div class="d-flex justify-content-between w-100 mt-auto mb-2">
															<span class="fw-bold fs-6 text-gray-400">Profile </span>
															<span class="fw-bolder fs-6"><?php echo $pp; ?>%</span>
														</div>
														<div class="h-5px mx-3 w-100 bg-light mb-3">
															<div class="bg-success rounded h-5px" role="progressbar" style="width: <?php echo $pp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
													</div>
											</div>
											<!--begin: Info-->
											<div class="flex-grow-1">
												<!--begin::Title-->
												<div class="d-flex justify-content-between flex-wrap mt-1">
													<div class="d-flex mr-3">
														<a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3"><?php echo $_SESSION['employee']['display_name'];?></a>
														<a href="#">
															<i class="flaticon2-correct text-success font-size-h5 mr-2"></i>
														</a>
														<a href="<?php echo $_SESSION['employee']['soc_facebook']; ?>" target="_blank" class="btn btn-icon btn-xs btn-circle btn-facebook mr-2">
													         <i class="socicon-facebook"></i>
													      </a>
													      <a href="<?php echo ($_SESSION['employee']['soc_twitter'])!='' ? $_SESSION['employee']['soc_twitter'] : '#'; ?>" target="_blank" class="btn btn-icon btn-xs btn-circle btn-twitter mr-2">
													         <i class="socicon-twitter"></i>
													      </a>
													      <a href="<?php echo $_SESSION['employee']['soc_linkedin']; ?>" target="_blank" class="btn btn-icon btn-xs btn-circle btn-linkedin">
													         <i class="socicon-linkedin"></i>
													      </a>

													</div>
													<div class="my-lg-0 my-3">
														
														<?php //echo "<pre>";print_r($clock_data); exit;
										$check_in_display = $clock_data['clock_in'] !="" ? $clock_data['clock_in'] : "00:00:00" ;
											$check_out_display = $clock_data['clock_out'] !="" ? $clock_data['clock_out'] : "00:00:00" ;
											if($clock_data['clock_out'] !='' || count($clock_data) == 0){
											
										?>
										<button type="button" onclick="checkinattendence('You have clocked in')" class="btn btn-primary font-weight-bold btn-pill" data-toggle="popover" data-placement="bottom"  data-html="true" data-content="<div class='p-4 font-weight-bold'> <div>  Check-in &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $check_in_display; ?>  </div> <div>Check-out &nbsp;-&nbsp;<?php echo $check_out_display; ?> </div> </div> "> Check-in &nbsp; <i class='icon-lg far fa-clock'></i></button>
										<?php }else{?> 
										<button type="button" onclick="checkinattendence('You have clocked out')" class="btn btn-success font-weight-bold btn-pill" data-toggle="popover" data-placement="bottom"  data-html="true" data-content="<div class='p-4 font-weight-bold'> <div>  Check-in &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $check_in_display; ?>  </div> <div>Check-out &nbsp;-&nbsp;00:00:00  </div> </div> "> Check-Out &nbsp; <i class='icon-lg far fa-clock'></i></button>
										<?php } ?>
														<!-- <a href="#" class="btn btn-sm btn-light-success font-weight-bolder text-uppercase mr-3">ask</a>
														<a href="#" class="btn btn-sm btn-info font-weight-bolder text-uppercase">hire</a> -->
														 
													</div>
												</div>
												<!--end::Title-->
												<!--begin::Content-->
											<div class="">
												<div class="d-flex flex-wrap justify-content-between mt-1 ">
													<div class="d-flex flex-column flex-grow-1 pr-8  ">
														<div class="d-flex flex-wrap mb-4">
															<a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
															<i class="flaticon2-new-email mr-2 font-size-lg"></i><?php echo $_SESSION['employee']['email_id'];?></a>
															<a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
															<i class="flaticon2-calendar-3 mr-2 font-size-lg"></i><?php echo $desig['designation_name'];?></a>
															<a href="#" class="text-dark-50 text-hover-primary font-weight-bold">
															<i class="flaticon2-placeholder mr-2 font-size-lg"></i><?php echo $location['location_name'];?></a>
														</div>
														<div>
														<div class="btn-group max-w-100px float-left">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Key Skills</button>
															<div class="dropdown-menu">
																<?php foreach ($empskills as $eskill) { 
																	$irat = $skill[$eskill['skill_id']]['max_ratting']/5;
																	$rate = $eskill['rating']/$irat;
																	?>
																	<a class="dropdown-item" href="#"><?php echo $skill[$eskill['skill_id']]['skill']; ?> &nbsp;
																	<?php for ($i=1; $i < 6 ; $i++) { 
																			if($rate >= $i){ ?>
																				<i class="fas fa-star  text-warning mt-3"></i>
																			<?php }else if ($rate >=($i-0.5)) { ?>
																				<i class="fas fa-star-half-alt  text-warning mt-3"></i>	
																			<?php }else{?>
																				<i class="far fa-star  text-warning mt-3"></i>	
																			<?php }
																			
																	} ?>
																</a>
																<?php } ?>
																<div class="dropdown-divider"></div>
																<a class="dropdown-item" href="#" onclick="dynamicinsertpopup('COM_INSERT_EMP_SKILLS')">+ Add New</a>
															</div>
														</div>
														<?php if($shift['shift_name']!=""){ ?>
														<div class="float-left mt-3">&nbsp;
														    <span classs="h4 font-weight-bolder">Shift : </span>
														    <span classs=" mr-2 font-size-lg"> </span><?php echo $shift['shift_name']; ?></span>
														    
														   
														</div>
														<?php } ?>
														
														</div>
														<?php if($shift['employee']['about_me']!=""){ ?>
														<span class="font-weight-bold text-dark-50 mt-1"><b>About Me : </b><?php echo  $_SESSION['employee']['about_me'];?></span>
														<?php } ?>
														<div class="col-lg-10 mt-2 row">

														<?php foreach($food_options as $food_option){ ?>
														        <div class="col-lg-2 float-left">
                                                            			<span class="switch switch-outline switch-icon switch-success">
                                                            				<label>
                                                            					<input type="checkbox" onclick="checkempfoodoption(<?php echo $food_option['id'].",".$_SESSION['emp_id']; ?>)" value="<?php echo $food_option['id']; ?>"  name="" <?php if(in_array($food_option['id'],$emp_food)){ ?> checked <?php } ?> >
                                                            					<span></span><label class="col-form-label"><?php echo $food_option['food_option']; ?></label>
                                                            				</label>
                                                            			</span>
                                                        		</div>
														    
														<?php } ?>
														<div class="col-lg-3 float-left">
														    <span class="switch switch-outline switch-icon switch-success">
                                                            				<label>
                                                            					
                                                            					<input type="checkbox" onclick="checkemptransportrequest(<?php echo $_SESSION['emp_id'].",".$emptransport['id']; ?>)" value="<?php echo $emptransport['id']; ?>"  name="" <?php if($emptransport['id']!=""){ ?> checked <?php } ?> >
                                                            					<span></span><label class="col-form-label">Transport Requests </label>
                                                            				</label>
                                                            			</span>
														</div>

														</div>
													</div>
													<input type="hidden" id="location_id" value="<?php echo $_SESSION['employee']['location_id']; ?>">
													<input type="hidden" id="start_date" value="<?php echo date('Y-m-d'); ?>">
													<!-- <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3 col-lg-4 float-left">
														<div class="d-flex justify-content-between w-100 mt-auto mb-2">
															<span class="fw-bold fs-6 text-gray-400">Profile Completion</span>
															<span class="fw-bolder fs-6"><?php echo $pp; ?>%</span>
														</div>
														<div class="h-5px mx-3 w-100 bg-light mb-3">
															<div class="bg-success rounded h-5px" role="progressbar" style="width: <?php echo $pp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
													</div> -->
												</div>
											</div>
												<!--end::Content-->
											</div>
											<!--end::Info-->
										</div>
										<!--end::Details-->
										<!--begin::Navs-->
									<?php if (count($saidnav) >0 && in_array('LN', $_SESSION['role_nav'])){ ?>
										<div class="d-flex mt-4 h-55px">
													   
											<ul class="nav nav-tabs nav-bold nav-tabs-line nav-line-tabs-3x border-transparent fs-5 fw-bolder flex-nowrap responsive-tab">
												
												<?php foreach($saidnav as $nav){
													$child = count($nav['child']);
													$link = $child >0 ?  'javascript:;' : 'index.php?option='.$nav['Page_Name'] ;
													$icon = ($nav['Menu_Icon'] == '') ?'' : $nav['Menu_Icon'];
													$toggle_name = $child > 0 ?  '1' : '0' ;
													if($display_id== $nav['id']){
															$option_name = $nav['Page_Name'];
													}
													if(count($nav['child'])>0){ 
												?>
												<li class="nav-item dropdown">
												        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
												            <?php echo $nav['Label']; ?>
												        </a>

												        <div class="dropdown-menu">
												        	 <?php  foreach ($nav['child'] as  $list) { 
												        	 if($display_id== $list['id']){ $option_name = $list['Page_Name']; } ?>
												            <a class="dropdown-item" href="index.php?option=COM_LIST_NEW_EMPLOYEE&NI=<?php echo $list['id']; ?>"><?php echo $list['Label']; ?></a>
												            <?php } ?>
												        </div>
												    </li>

											<?php }else{ ?>
												<li class="nav-item">
													<a class="nav-link text-active-primary me-6 <?php if($display_id== $nav['id']){ echo "active"; } ?>"  href="index.php?option=COM_LIST_NEW_EMPLOYEE&NI=<?php echo $nav['id']; ?>">
														<?php echo $nav['Label']; ?></a>
												</li>


												<?php } } ?>
												
												
												
											</ul>
										</div>
												<!--begin::Navs-->
										<?php } ?>
									</div>
								</div>
			<?php } ?>
								<!--end::Navbar-->						
								<!--begin::Sign-in Method-->
								<div class="card  ">
									<?php $_GET['option'] = $option_name;
					if($_GET['option']!=""){
						include("includes/controller.php");
						$dispage = str_replace("views/", '', $disptemp);
						include($dispage);
					}else{
						include("custom_list_dash_board.php");
						
					} 
					//include("custom_admin_dash_board.php");
									?>							
								</div>
								<!--end::Sign-in Method-->
							
	</div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">
            
        </div>
    </div>
</div>