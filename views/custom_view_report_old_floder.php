<?php if(isset($_GET['TYPE'])){
	$_GET['TYPE'] = $_GET['TYPE'];
}else{
	$_GET['TYPE'] =  'MYR';
}
$groupmodulee = $_SESSION['default_module_group_id'];
if(isset($_GET['DGM']) AND $_GET['DGM'] != ""){
	$groupmodulee = $_GET['DGM'];
}
$fld = $_GET['FLD'];
$data12["where"]['is_active'] = '0';
if($fld!=""){
//$data12["where"]['parent_id'] = $fld;
}
$data = urlencode(json_encode($data12));
$itemurl = API."?mode=gettabledetailswithconditions&table=my_reports&data=".$data; 
$item_data = callAPI($itemurl);
$row = json_decode($item_data, 1);
	//print_r($row);
foreach($row as $rowdata){
	$list_breadcum[$rowdata['id']] = array('parent_id'=>$rowdata['parent_id'],'name'=>$rowdata['page_name'],'id'=>$rowdata['id'],'type'=>'MYR');
	if(($rowdata['parent_id'] == '0' && $fld=="") || ( $fld == $rowdata['parent_id'] && $rowdata['header'] == '')){
		$parent_data[] = $rowdata;
	}else{
		//if($fld!=""){
			$submain_data[$rowdata['parent_id']][] = $rowdata;
			//$main_data[] = $rowdata;
		//}
	}
}
$sub_data = array();
if($fld!=""){
$sub_data = $submain_data[$fld];
}
//$breads = array('id'=>'',name=>'My Report','type'=>'MYR');
if($fld!=""){
	$redid = $fld; 
	for($i=10;$i>0;$i--){
		$breadcum[$i] = $list_breadcum[$redid];
		
		if($list_breadcum[$redid]['parent_id'] == 0){
			break;
		}
		$redid = $list_breadcum[$redid]['parent_id'];
	}
}
$breadcum[0] = array('id'=>'',name=>'My Report','type'=>'MYR');
if($_GET['TYPE']=='SR'){
$breadcum[0] = array('id'=>'',name=>'Stantdard Report','type'=>'SR');
}
ksort($breadcum);
//print_r($breadcum);


$data15["where"] = array('is_active' =>'0','company_id'=>$_SESSION['company_id']);
$data15["field"] = "id,display_name,image";
$data5 = urlencode(json_encode($data15));
$itemurl5 = API."?mode=gettabledetailswithconditions&table=employees&data=".$data5; 
$item_data4 = callAPI($itemurl5);
$emplo5  = json_decode($item_data4, 1);

$employees_list = array();
foreach($emplo5 as $emplo){
	$employees_list[$emplo['id']] = $emplo;
}
//print_r($employees_list);
?>
<style>
.file-img {margin: 0px 0px 0px -18px; width: 15px;}
</style>
<script type="text/javascript">
	function deleteidrow(id,deleteid) {
		var delet = confirm('Are you sure want to delete report?');
		if(delet){
			$.ajax({
				url: "ajax.php",
				data:'page=deletemyreport&id='+id,
				type: "POST",
				success:function(data){
					$(".row"+deleteid).remove();
				}
			});
		}else{
			return false;
		}
	}
	function movecopyfloder(report_id){
	    jQuery.ajax({
	        url: "views/custom_list_report_save_popup.php",
	        data:'report_id='+report_id,
	        type: "POST",
	        success:function(data){
	            $('#popup1').html(data);
	            $('#kt_modal_KTDatatable_remote').modal("show");
	        },
	        error:function (){}
	    });
	}
	function changeflodername(report_id,floder_name){
		//$('#kt_modal_KTDatatable_remote').modal('hide');
		 jQuery.ajax({
	        url: "views/custom_report_change_floder_name.php",
	        data:'report_id='+report_id+'&floder_name='+floder_name,
	        type: "POST",
	        success:function(data){
	            $('#popup1').html(data);
	            $('#kt_modal_KTDatatable_remote').modal("show");
	        },
	        error:function (){}
	    });
	}
	function movetotrash(report_id){
		var  str = '';
 		str = 'fid=is_active&table=my_reports&hid='+report_id+'&rid=1';
  		console.log(str);
  		$.ajax({
	        url: "ajax.php",
	        type: "POST",
	        data: 'page=updatedynamicfied&'+str,
	        success: function(response) {
	        	console.log(response); 
	        	swal.fire({              
					title: "Report is move to trash successfully",        
					type: "success",           
					buttonsStyling: true,
					confirmButtonText: "<i class='la la-hand-o-up'></i>OK",    
					confirmButtonClass: "btn btn-success", 
					showCancelButton: false
					        
				}).then(function(result) {
					if (result.value) {
						$('#kt_modal_KTDatatable_remote').modal('hide');
						window.location.reload();
					}
				});
	        }
	    });
	}
</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<div class="card card-custom card-sticky" id="kt_page_sticky_card">
			<div class="card-header">
				<h5>Reports</h5>
				<div class="card-header-right">
					<a href="index.php?option=COM_INSERT_REPORT_ATTRIBUTES_NEW" class="btn btn-success ">
						<i class="icofont icofont-plus icon-white"></i>New Report 
					</a>
					<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
						<i class="icofont icofont-reply"></i>
						Back
					</a>
				</div>
			</div>
			<div class="card-body">
				<div class="row ">
					<div class="col-lg-12">
						<div class="form" id="kt_contact_add_form">
							<div class="pb-5 " data-wizard-type="step-content">
								<div class="card card-custom gutter-b example example-compact">
									<div class="card-body">
										<div class="form-group row">
											<div class="col-lg-3 col-md-3 col-sm-12">
												<ul class="nav  md-tabs tabs-left b-none mt-5 infisuite_tab" role="tablist">
													<li class="nav-item">
														<a class="nav-link <?php echo $_GET['TYPE']=='SR'?"active":""; ?>"  href="index.php?option=COM_VIEW_REPORT&TYPE=SR" role="tab">Stantdard Report</a>
													</li></a>
													<li class="nav-item">
														<a class="nav-link <?php echo $_GET['TYPE']=='MYR'?"active":"" ?>"  href="index.php?option=COM_VIEW_REPORT&TYPE=MYR" role="tab">My Report</a>
													</li>
												</ul>
											</div>
											<div class="col-lg-9 col-md-9 col-sm-12 prints displaye">
				<div class="col-lg-12 page-body breadcrumb-page">


	<div class="card borderless-card ">
		<div class="card-block1 primary-breadcrumb" style="border-radius:6px;">
		<div class="page-header-breadcrumb">
			<ul class="breadcrumb border-0" style="padding: 17px;color: #FFF;">
				<?php foreach($breadcum as $cum){ ?>
					<li class="breadcrumb-item">
						<a href="index.php?option=COM_VIEW_REPORT&TYPE=<?php echo $cum['type']; ?>&FLD=<?php echo $cum['id']; ?>" style="color: #FFF;"> <?php echo $cum['name']; ?> </a>
					</li>
				<?php } ?>
				
			</ul>
		</div>
	</div>


</div>

												
												<?php if($_GET['TYPE']=="SR"){ ?>
													<div class="card-body myreport" >
														<?php 
														$myopr=array('COM_VIEW_CHART_JOB_REPORT'=>'Job Activity Report','COM_VIEW_CHART_CANDIDED_REPORT'=>'Candidate Report');
														$myofr=array('COM_VIEW_HEAD_COUNT_CURRENT_REPORT'=>'Supplier Performance Report','COM_VIEW_TENUER_REPORT'=>'Tenure Report');

														?>
														<div class="dt-responsive table-responsive">
															<table id="new-cons" class="table table-striped table-bordered nowrap">
																<thead>
																	<tr>
																		<th>Sl.No.</th>
																		<th title="Field #2">Report name</th>	
																		<th>Action</th>
																	</tr>
																</thead>
																<tbody>
																	<tr>
																		<td  class="h6" colspan="3"> Operational Report</td>
																	</tr>
																	<?php $i=1; foreach($myopr as $key=>$dd){ ?> 
																	<tr>
																		<td><?php echo $i; ?></td>
																		<td><?php echo $dd; ?></td>
																		<td><a href="index.php?option=<?php echo $key;  ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="view Datails" target='_blank'> <i class="fa fa-eye"></i></a>
																		</td>
																	</tr>
																	<?php $i++; } ?>
																	<tr><td class="h6" colspan="3">Financial Reports</td></tr>
																	<?php $i=1; foreach($myofr as $key=>$dd){ ?> 
																		<tr>
																			<td><?php echo $i; ?></td>
																			<td><?php echo $dd; ?></td>
																			<td><a href="index.php?option=<?php echo $key;  ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="view Datails" target='_blank'><i class="fa fa-eye"></i></a>
																			</td>
																		</tr>
																	<?php $i++; } ?>
																</tbody>
															</table>
														</div>
													</div>
												<?php } elseif($_GET['TYPE']=="MYR"){ if(count((array)$parent_data)>0){  ?>
													
													<div class="row col-lg-12" >
															<?php foreach($parent_data as $pdata){ 
																$count = count((array)$submain_data[$pdata['id']]); ?>
																<div class="align-items-center col-lg-3 mt-3">
																	<div class="card custom_hover_card">
																		<div class="card-body">
																			<div class="float-right">
					                                                            <div class="dropdown-primary dropdown open">
					                                                                <button class="btn waves-effect waves-light p-0" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
					                                                                <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
					                                                                    <a class="dropdown-item waves-light waves-effect" href="#" onclick="changeflodername(<?php echo $pdata['id'].",'".$pdata['page_name']."'"; ?>)" ><?php echo $pdata['page_name']; ?><i class="ti-pencil m-l-10"></i></a>
					                                                                    <div class="dropdown-divider"></div>
					                                                                    <a class="dropdown-item waves-light waves-effect pt-3" href="#">Make a Copy</a>
					                                                                    <a onclick="movecopyfloder(<?php echo $pdata['id']; ?>)" class="dropdown-item waves-light waves-effect pt-3" href="#">Move to Folder</a>
					                                                                    <a class="dropdown-item waves-light waves-effect pt-3" href="#" onclick="movetotrash(<?php echo $pdata['id']; ?>)" >Move to Trash</a>
					                                                                </div>
					                                                            </div>
					                                                        </div>
																			<a href="index.php?option=COM_VIEW_REPORT&TYPE=MYR&FLD=<?php echo $pdata['id'] ?>">
																				<div class="d-block">
																					<img src="assets/images/folder.png" class="img-20" alt="">
																					<?php if($count > 0){ ?>
																						<!-- <img src="assets/images/file.png" class="file-img" alt=""> -->
																						<!-- <img src="assets/images/file.png" class="file-img" alt=""> -->
																					<?php } ?>
																				</div>
																				<div class="d-block mt-2">
																					<h4 class="f-14 m-0 text-primary"><?php echo $pdata['page_name']; ?>
																						<i class="ti-user f-14 text-dark"></i>
																					</h4>
																					<p class="m-0 mt-1 f-12 text-secondary">No of Files <?php echo $count; ?></p>

																				</div>
																			</a>
																			 <!-- <div onclick="movecopyfloder(<?php echo $pdata['id']; ?>)"><i class="fa fa-mail-forward"></i>  
																			</div>-->
																		</div>
																	</div>
																</div>
															<?php } ?>
													</div>

													<div class="mt-2">
														<div class="">
				                                            <div class="card-block">
				                                                <div class="table-responsive">
				                                                    
				                                                </div>
				                                            </div>
				                                        </div>
													</div>
													
												<?php } if(count((array)$sub_data)>0){ ?>
													<div class="card-body myreport" >
														<div class=""><!--dt-responsive table-responsive-->
															<table class="table table-hover m-b-0">
		                                                        <thead>
		                                                            <tr>
		                                                                <th>#</th>
		                                                                <th>Report Name</th>
		                                                                <th>Activity</th>
		                                                                <th>Created By</th>
		                                                                <th>Actions</th>
		                                                            </tr>
		                                                        </thead>
		                                                        <tbody>
		                                                        	<?php $i= 1; if(count((array)$sub_data)>0){  foreach($sub_data  as $result){  ?>
		                                                            <tr class="row<?php echo $i; ?>">
		                                                                <th><?php echo $i;?></th>
		                                                                <td><a href="index.php?option=COM_VIEW_MY_REPORT&Rid=<?php echo deocdeId("id:".$result['report_id']);  ?>"><?php echo $result['page_name']; ?></a></td>
																		<td><?php echo $result['updated_date']; ?></td>
		                                                                <td>
																			<div class="align-middle d-inline-block">
																				<?php if($employees_list[$result['emp_id']]['image']) {?>
																					<img src="uploads/all/<?php echo $employees_list[$result['emp_id']]['image']; ?>"  class="img-radius img-50 align-top m-r-15" onerror="this.src='uploads/all/no-image.jpg';">
																				<?php } else { ?>
																					<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($employees_list[$result['emp_id']]['display_name']); ?></span>
																				<?php } ?>
																				<div class="d-inline-block width73">
																					<h6><?php echo $employees_list[$result['emp_id']]['display_name']; ?></h6>
																				</div>
																			</div>
																			
																			
		                                                                </td>
		                                                                <td>
		                                                                    <div class="dropdown-primary dropdown open float-right">
		                                                                        <button class="btn waves-effect waves-light p-0" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
		                                                                        <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
		                                                                            <a class="dropdown-item waves-light waves-effect d-block" href="index.php?option=COM_INSERT_REPORT_ATTRIBUTES_NEW&Rid=<?php echo deocdeId("id:".$result['report_id']);  ?>">Edit</a>
		                                                                            <a class="dropdown-item waves-light waves-effect d-block" href="index.php?option=COM_VIEW_MY_REPORT&Rid=<?php echo deocdeId("id:".$result['report_id']);  ?>">View</a>
		                                                                            <div class="dropdown-divider"></div>
					                                                                    <a class="dropdown-item waves-light waves-effect pt-3" onclick="movecopyfloder(<?php echo $result['id']; ?>)" href="#">Make a Copy</a>
					                                                                    <a onclick="movecopyfloder(<?php echo $result['id']; ?>)" class="dropdown-item waves-light waves-effect pt-3" href="#">Move to Folder</a>
					                                                                    <a class="dropdown-item waves-light waves-effect pt-3" href="#">Move to Trash</a>
					                                                                </div>
		                                                                    </div>
		                                                                </td>
		                                                            </tr> 
		                                                            <?php $i++; } }else{ echo "<tr><td colspan='3'> No Reports Availabled</td></tr>"; }   ?>
		                                                        </tbody>
		                                                    </table>	


















<?php /** ?>
															<table id="new-cons" class="table table-striped table-bordered nowrap">
																<thead>
																	<tr>
																		<th>Report name</th>
																		<th>Last Modified</th>
																		<th>Action</th>
																	</tr>
																</thead>
																<tbody>
																	<?php $i= 0; if(count((array)$sub_data)>0){  foreach($sub_data  as $result){  ?>
																		<tr class="row<?php echo $i; ?>">
																			<td><?php echo $result['page_name']; ?></td>
																			<td><?php echo $result['updated_date']; ?></td>
																			<td>
																				<a href="index.php?option=COM_INSERT_REPORT_ATTRIBUTES_NEW&Rid=<?php echo deocdeId("id:".$result['report_id']);  ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="view Datails">                  
																					<i class="fa fa-edit"></i></a>
																					<a href="index.php?option=COM_VIEW_MY_REPORT&Rid=<?php echo deocdeId("id:".$result['report_id']);  ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="view Datails">
																					<i class="fa fa-eye"></i></a>
																			</td>
																		</tr>
																	<?php $i++; } }else{ echo "<tr><td colspan='3'> No Reports Availabled</td></tr>"; }   ?>
																</tbody>
															</table>  <?php **/ ?>
														</div>
													</div>
												<?php } } ?>
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