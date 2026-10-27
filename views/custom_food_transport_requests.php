<?php
$bgcolorlist = array('0'=>'success','1'=>'warning','2'=>'primary','3'=>'info','4'=>'dark','5'=>'secondary');
$current_from_date = date('Y-m-d');
$current_to_date = date('Y-m-d');

$data12["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$datw12 = urlencode(json_encode($data12));
$curl12 = API."?mode=gettabledetailswithconditions&table=food_options&data=".$datw12; 
$cudata12 = callAPI($curl12);
$foodoptions = json_decode($cudata12,1);

$data12["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$datw12 = urlencode(json_encode($data12));
$curl12 = API."?mode=gettabledetailswithconditions&table=locations&data=".$datw12; 
$cudata12 = callAPI($curl12);
$locations = json_decode($cudata12,1);

	//print_r($foodoptions);exit;
$vurl = API."?mode=getemprequests&company_id=".$_SESSION['company_id']."&is_active=0";
$configesponse = callAPI($vurl);
$requestData = json_decode($configesponse,1);
$total_requests = $requestData['summary'];
$foodoptiondata = $requestData['foodoptiondata'];
$locfooddata = $requestData['locfooddata'];
$transport = $requestData['transport'];
$transport_req = $requestData['transport_req'];


//echo "<pre>"; print_r(count((array)$locfooddata[2][1]));

if(isset($_POST['datefilter'])){
	$current_from_date = $_POST['fromdate'];
	$current_to_date = $_POST['todate'];
	$vurl = API."?mode=getemprequests&company_id=".$_SESSION['company_id']."&is_active=0&fromdate=".$current_from_date."&todate=".$current_to_date;
	$configesponse = callAPI($vurl);
	$requestData = json_decode($configesponse,1);
	$total_requests = $requestData['summary'];
	$foodoptiondata = $requestData['foodoptiondata'];
	$locfooddata = $requestData['locfooddata'];
	$transport = $requestData['transport'];
	$transport_req = $requestData['transport_req'];
	
//echo "<pre>"; print_r($transport[5]);

}

?>

<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
					<div class="card card-custom " id="kt_page_sticky_card">
						<div class="card-header">
							<div class="card-title">
								<h3 class="card-label">&nbsp; EMPLOYEE FOOD AND TRANSPORT REQUESTS </h3>
							</div>
							
						</div>
						<div class="card-body">		
							<div class=" card-custom gutter-b card-stretch">		
								<div class=" d-flex flex-column">
									<div class="row col-lg-12">
										<div class="col-lg-9">
											<div class="row col-lg-12">

												<?php 
												$colorindex = 0;
												if(count((array)$total_requests)>0) {
													
												foreach($total_requests as $rkey=>$request) { 
													$req_types_count_list[] = count((array)$request)==''?'0':count((array)$request);
													$req_types_names_list[] = $foodoptiondata[$rkey];
													?>
													<button type="button" class="btn circle-circle-xl border-<?php echo $bgcolorlist[$colorindex]; ?> m-l-5 "><div class="text-center font-size-h3"><?php echo count((array)$request)==''?'0':count((array)$request)==''?'0':count((array)$request);; ?></div> <div class="text-center font-size-h6"><?php echo $foodoptiondata[$rkey];?></div></button>
													<?php $colorindex++; }
												}else{ 
														foreach($foodoptions as $rkey) { 
													$req_types_names_list[] = $rkey['food_option'];
													$req_types_count_list[] = "0";
															?>
													<button type="button" class="btn circle-circle-xl border-<?php echo $bgcolorlist[$colorindex]; ?> m-l-5 "><div class="text-center font-size-h3"><?php echo "0"; ?></div> <div class="text-center font-size-h6"><?php echo $rkey['food_option'];?></div></button>
													<?php $colorindex++; }
												}  

												array_push($req_types_count_list,$transport_req);
												array_push($req_types_names_list,"Transport");
											

												?>
												<button type="button" class="btn circle-circle-xl border-<?php echo $bgcolorlist[$colorindex]; ?> m-l-5 "><div class="text-center font-size-h3"><?php echo $transport_req; ?></div> <div class="text-center font-size-h6"><?php echo "Transport";?></div></button>
												</div>
											</div>
											<div class="col-lg-3">
													<form method="post" action="#">
														<div class="form-group row">
															<label for="example-date-input" class="col-3 col-form-label">From</label>
															<div class="col-10">
																<input class="form-control" type="date" name="fromdate" value="<?php echo $current_from_date;?>" id="example-date-input" />
															</div>
															<label for="example-date-input" class="col-3 col-form-label">To</label>
															<div class="col-10">
																<input class="form-control" type="date" name="todate" value="<?php echo $current_to_date;?>" id="example-date-input" />
															</div>
														</div>

														<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="datefilter" value="exit">Apply</button>
													</form>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
							<div class="col-lg-8 form-group">

								<table class="table table-separate table-head-custom table-checkable bordered dataTable px-6 " id="kt_datatable1">
									<!-- <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1"> -->
										<thead>  
											<tr>
												<th>#</th>
												<th>Location </th>
												<?php foreach($foodoptions as $food_op){ ?>
													<th> <?php echo $food_op['food_option']; ?></th>
												<?php } ?>
												<th>Transport </th>
											</tr>
										</thead>
										<tbody> 
											<?php $sno= 0;
											foreach($locations as $loc) {  $sno++;   ?>
												<tr>
													<td> <?php echo $sno; ?> </td>
													<td> <?php echo $loc['location_name']; ?> </td>
													<?php foreach($foodoptions as $food_op){ $foodoptionval = $food_op['id']; $locval = $loc['id']; $emplist = json_encode(array_keys($locfooddata[$foodoptionval][$locval]),1); $decode_emplist = deocdeId(urlencode($emplist));  ?>
													<td>  <a href="index.php?option=COM_LIST_EMP_REPORT&EID=<?php echo $decode_emplist;?>" target="_blank"> <span class="font-weight-bolder label label-xl label-success "  data-toggle="tooltip" title="Number of Requests" style="vertical-align: middle;">  <?php echo count((array)$locfooddata[$foodoptionval][$locval]); ?> </span></a>  </td>
												<?php } ?>

												<?php  //foreach($transport as $trans){ 

													$locval = $loc['id']; $emplist = json_encode(array_keys($transport[$locval]),1); $decode_emplist = deocdeId(urlencode($emplist));  ?>
													<td>  <a href="index.php?option=COM_LIST_EMP_REPORT&EID=<?php echo $decode_emplist;?>" target="_blank"> <span class="font-weight-bolder label label-xl label-success "  data-toggle="tooltip" title="Number of Requests" style="vertical-align: middle;">  <?php echo count((array)$transport[$locval]); ?> </span></a>  </td>
												<?php //} ?>
											</tr>

										<?php }
										?>
									</tbody>
								</table>
									</div>
									<?php
										$req_types_count = implode('|',(array)$req_types_count_list);
										$req_types_names = implode('|',(array)$req_types_names_list);
									?>


								<div class="form-group col-lg-4" >	
										<input type="hidden" id="survey_qtypes" value="<?php echo $req_types_count; ?>">
										<input type="hidden" id="survey_qtype_names" value="<?php echo $req_types_names; ?>">
										<div id="survey_overview_chart" class="d-flex justify-content-center"></div>
										<h3 class="font-weight-bolder text-center"> <?php echo "Request Types"; ?></h3>

									</div>
									</div>


							
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

