<?php 
$_SESSION['apply_js']['recruitingmanager_dashboard']="1";
$reportstartdate = date("Y-m-d",strtotime("-5 month"));
$reportenddate = date("Y-m-d",strtotime("+2 month"));
$reportenddatetab = date("Y-m-d");
$roleshortcodes  = json_encode($_SESSION['role_ids']);
$url = API."?mode=getrecruitmentintegdashboard&reportstartdate=".$reportstartdate."&reportenddate=".$reportenddate."&roleshortcode=".$roleshortcodes;
$apires = callAPI($url);
$result = json_decode($apires,1);
//echo "<pre>"; print_r($result['job_posting_candidates_sql']);echo "</pre>";
$holidaydata = $result['holidays'];
$holidays = nearestDateFirstShow($holidaydata,'holiday_date');
$candidates = $result['candidates'];
$job_postings = $result['job_postings'];
$job_posting_candidates = $result['job_posting_candidates'];
$countcandidatenmonth = $result['countcandidatenmonth'];
$donutchart = $result['donutchart'];
$empty_table_alert = $result['empty_table_alert'];
$job_candidate_interview_schedules = $result['job_candidate_interview_schedules'];
$approved_job_candidate_interview_schedules =  $result['approved_job_candidate_interview_schedules'];
$montharray = array();
$startmonth0 = new DateTime($reportstartdate);
$endmonth0 = new DateTime($reportenddatetab);
while($startmonth0 <= $endmonth0){
	$data1 = array();
	$monthkey= $startmonth0->format('Ym');
	$monthval= $startmonth0->format('Y-m-01');
	$montharray[$monthkey] = $monthval;
	$startmonth0->modify('+1 month');
}
//echo "<pre>"; print_r($job_posting_candidates);echo "</pre>";
$montharray2 = array();
$startmonth02 = new DateTime(date("Y-m-d",strtotime("-3 month")));
$endmonth02 = new DateTime(date("Y-m-d",strtotime("+2 month")));
while($startmonth02 <= $endmonth02){
	$data1 = array();
	$monthkey= $startmonth02->format('Ym');
	$monthval= $startmonth02->format('Y-m-01');
	$montharray2[$monthkey] = $monthval;
	$startmonth02->modify('+1 month');
}
//echo "<pre>";print_r($montharray); print_r($job_posting_candidates);echo "</pre>";
//echo "<pre>"; print_r($job_posting_candidates);echo "</pre>";
$stausresult = ViewStatusData();
$lola = array('apshort','rhshort','hmshort','lorshort','logshort','loashort');
foreach($result['tabsdata'] as $tabsdata){
	if($tabsdata['current_status']==ST_ACTIVE){
		$tabsdata['view_status'] = "Applied";
		$tabdata['apshort'][] = $tabsdata;
	}
		
	if($tabsdata['current_status']==ST_RMSHORTLISTED){
		$tabsdata['view_status'] = "RM Short Listed";
		$tabdata['rhshort'][] = $tabsdata;
	}
	if($tabsdata['current_status']==ST_HMSHORTLISTED){
		$tabsdata['view_status'] = "HM Short Listed";
		$tabdata['hmshort'][] = $tabsdata;
	}
	if($tabsdata['current_status']==ST_LOIREQUESTED){
		$tabsdata['view_status'] = "LoI Requested";
		$tabdata['lorshort'][] = $tabsdata;
	}
	if($tabsdata['current_status']==ST_LOIGENERATED){
		$tabsdata['view_status'] = "LoI Generated";
		$tabdata['logshort'][] = $tabsdata;
	}
	if($tabsdata['current_status']==ST_LOIACCEPTED){
		$tabsdata['view_status'] = "LoI Accepted";
		$tabdata['loashort'][] = $tabsdata;
	}
}
$stausresult = ViewStatusData();
$statuslist  = getstatusprogresslist('job_postings');
foreach($statuslist as $statuslist){
	$statusnamelist[$statuslist['status']] = $statuslist['name'];	
}

$candidate_dashboard = $result['candidate_dashboard'];
$candidate_dashboard_today = $result['candidate_dashboard_today'];
//echo "<pre>"; print_r($candidate_dashboard_today); echo "</pre>";
?>
<script type="text/javascript">
	function displaystausdrop(id,table,status_id,tab){
		//alert("id");
		$.ajax({
			url: "ajax.php",
			data:'page=current_status_drop_table&id='+id+'&current_status='+status_id+'&table='+table,
			type: "POST",
			success:function(data){
				console.log(data);
				$("#dropdownlist_"+tab+"_"+id).html(data);
			},
			error:function (){}
		});
	}
	function statuspopup(status,id,table_name){
      //alert("data"+id);
      //var table_name = 'job_posting_candidates';
      //var table_name = 'job_postings';
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
  	function publishdatapopup(job_post_id){
		jQuery.ajax({
			url: "views/custom_insert_job_hiring_source_popup.php",
			data:'job_post_id='+job_post_id,
			type: "POST",
			success:function(data){
				console.log(data);
				$('#dynamic_popup11').html(data);
				//$('#kt_modal_KTDatatable_remote').modal({ show: true});
				$("#kt_modal_dynamic_popup2").modal("show");
			},
			error:function (){}
		});
	}
function viewAllRecord(page,type){
		jQuery.ajax({
			url: "views/custom_list_view_all_dash_board.php",
			data:'TYPE='+type,
			type: "POST",
			success:function(data){
				console.log(data);
				$('#dynamic_popup1').html(data);
				//$('#kt_modal_KTDatatable_remote').modal({ show: true});
				$("#kt_modal_dynamic_popup").modal("show");
			},
			error:function (){}
		});
	}

function getJobPostingApplyCandidate(job_posting_id,field_name,value){
	jQuery.ajax({
		url: "views/custom_view_recruitingmanager_dashboard_popup.php",
		data:'job_posting_id='+job_posting_id+'&field_name='+field_name+'&value='+value,
		type: "POST",
		success:function(data){
			console.log(data);
			$('#dynamic_popup1').html(data);
			//$('#kt_modal_KTDatatable_remote').modal({ show: true});
			$("#kt_modal_dynamic_popup").modal("show");
		},
		error:function (){}
	});
}
// function sweetalertf(){
// 	swal({
// 			title: "Submission Successful",
// 			text: "ok",
// 			type: "success",
// 			showCancelButton: false,
// 			closeOnConfirm: true,
// 			showLoaderOnConfirm: true
// 		}, function () {
// 			//window.location.reload();
// 		});
// }
// function sweetalertf2(){
// 	swal({
// 			title: "Submission Successful",
// 			text: "Your will not be able to recover this imaginary file!",
// 			type: "success",
// 			showCancelButton: true,
// 			confirmButtonClass: "btn-danger",
// 			confirmButtonText: "Ok",
// 			closeOnConfirm: false
// 		},
// 		function(){
// 			//swal("Deleted!", "Your imaginary file has been deleted.", "success");
// 		});
// }

</script>
<!-- <button type="button" class="btn btn-success alert-success-msg m-b-10" onclick="sweetalertf();">click</button>
<button type="button" class="btn btn-success alert-success-msg m-b-10" onclick="sweetalertf2();">click2</button>
 -->
 <div class="row">
 	<?php 
 	$transactiontableary = array("job_categories"=>array("application_messages"=>"9097","option"=>"COM_LIST_JOB_CATEGORIES"),"job_descrption_categories"=>array("application_messages"=>"9105","option"=>"COM_LIST_JOB_DESCRPTION_CATEGORIES"),"job_descrptions"=>array("application_messages"=>"9098","option"=>"COM_LIST_JOB_DESCRPTIONS"),"job_descrption_categories"=>array("application_messages"=>"9099","option"=>"COM_LIST_JOB_DESCRPTION_CATEGORIES"),"job_selection_process"=>array("application_messages"=>"9100","option"=>"COM_LIST_JOB_SELECTION_PROCESS"),"job_selection_process_steps"=>array("application_messages"=>"9101","option"=>"COM_LIST_JOB_SELECTION_PROCESS_STEPS"),"candidates"=>array("application_messages"=>"9102","option"=>"COM_LIST_CANDIDATES"),"locations"=>array("application_messages"=>"9103","option"=>"COM_LIST_LOCATIONS"));
 	foreach($empty_table_alert as $eatfield=>$eta){ 
 		if($eta =='0'){
 		?>
		<div class="p-l-10 p-r-10 col-lg-6">
		    <div class="alert alert-info alert-dismissible">
		        <strong class="f-16"><i class="ti-alert "></i></strong> <a href="index.php?option=<?php echo $transactiontableary[$eatfield]['option']; ?>" class="text-primary"><?php echo ucwords($_SESSION['error_message'][$transactiontableary[$eatfield]['application_messages']]); ?></a>
		        <button type="button" class="btn-close text-danger" data-bs-dismiss="alert" aria-label="Close"></button>
		    </div>
		</div>
	<?php } } ?>
</div>

<ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
	<li class="nav-item">
		<a class="nav-link f-w-600 f-12 <?php echo (!isset($_GET['TAB']) || $_GET['TAB'] =="JP")?"active":"";?>" data-bs-toggle="tab" href="#jobposting-tab" role="tab" aria-selected="true" onclick=" updateUrlParameter('TAB','JP')">Job Postings </a>
	</li>
	<li class="nav-item">
		<a class="nav-link f-w-600 f-12 <?php echo ($_GET['TAB'] =="Cand")?"active":"";?>" onclick=" updateUrlParameter('TAB','Cand')" data-bs-toggle="tab" href="#Candidates-tab" role="tab" aria-selected="true">Candidates </a>
	</li>	
</ul>

<div class="tab-content tabs">
	<div class="tab-pane card-body <?php echo (!isset($_GET['TAB']) || $_GET['TAB'] =="JP")?"active":"";?>" id="jobposting-tab" role="tabpanel">
		


		<div class="row">
			<div class="col-xl-3 col-md-6">
				<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATES&JOBACT=-1">
					<div class="card ticket-card">
						<div class="card-body">
							<p class="m-b-30 bg-c-blue lbl-card"><i class="ti-briefcase"></i>&nbsp;&nbsp;Total Jobs</p>
							<div class="text-center">
								<h2 class="m-b-0 d-inline-block text-c-red"><?php echo customNanAndEmptyValidation($result['total_jobs']); ?></h2>
							<!-- 	<p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>From Previous Month</p>
							--></div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-xl-3 col-md-6">
				<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATES&JOBACT=0">
					<div class="card ticket-card">
						<div class="card-body">
							<p class="m-b-30 bg-c-green lbl-card"><i class="ti-briefcase"></i>&nbsp;&nbsp;Open Jobs</p>
							<div class="text-center">
								<h2 class="m-b-0 d-inline-block text-c-red"><?php echo customNanAndEmptyValidation($result['open_jobs']); ?></h2>
							</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-xl-3 col-md-6">
				<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATES&JOBACT=1">
					<div class="card ticket-card">
						<div class="card-body">
							<p class="m-b-30 bg-c-red lbl-card"><i class="ti-briefcase"></i>&nbsp;&nbsp;Close Jobs</p>
							<div class="text-center">
								<h2 class="m-b-0 d-inline-block text-c-red"><?php echo customNanAndEmptyValidation($result['close_jobs']); ?></h2>
							<!-- 	<p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>From Previous Month</p>
							--></div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-xl-3 col-md-6">
				<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATES&JOBACT=2">
					<div class="card ticket-card">
						<div class="card-body">
							<p class="m-b-30 bg-c-yellow lbl-card"><i class="ti-briefcase"></i>&nbsp;&nbsp;Hold Jobs</p>
							<div class="text-center">
								<h2 class="m-b-0 d-inline-block text-c-red"><?php echo customNanAndEmptyValidation($result['hold_jobs']); ?></h2>
							<!-- 	<p class="m-b-0 m-t-15"><i class="fas fa-caret-down m-r-10 f-18 text-c-red"></i>From Previous Month</p>
							--></div>
						</div>
					</div>
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-9" style="padding: 0px !important;">
				<div class="col-lg-12 row" style="padding: 0px !important; margin: 0px!important;">
					<div class="col-lg-3">
						<div class="card sale-card mb-2">
							<div class="card-header">
								<h5>Job Posting</h5>
							</div>
							<div class="card-block text-center">
								<img class="img-fluid" src="assets/images/122.png" alt="">
								<div class="row mt-2">
									<div class="col-lg-6 p-0">
										<a href="index.php?option=COM_INSERT_JOB_POSTINGS" class="btn btn-success "> <i class="icofont icofont-plus icon-white"></i> Job </a>
									</div>
									<div class="col-lg-6 p-0">
										<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATES" class="btn btn-warning "> <i class="ti ti-timer icon-white"></i>History</a>
									</div>	
								</div>
							</div>
						</div>
						<div class="card sale-card mb-2">
							<div class="card-header">
								<h5>Holidays</h5>
							</div>
							<div class="card-block custom-scroll" style=" height: 300px;">
							<?php 
							if(!empty($holidays)) {   
								foreach ($holidays as  $holiday) {

									$Notification_Message = $holiday['holiday_name'];
									$Notification_DateTime = fromDate($holiday['holiday_date']);
									?>
									<div class="align-middle m-b-10" style="border-bottom:1px dashed #cccccc;">
										<!-- <span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($Notification_Message); ?></span> -->
										<div class="d-inline-block">
											<a href="#!"><span><?php echo $Notification_Message; ?></span></a>
											<p class="text-muted m-b-0"><?php echo $Notification_DateTime; ?></p>
										</div>
									</div>
								<?php } }else{ 
									echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
								} ?>
							</div>
						</div>
						<div class="card sale-card mb-2">
							<div class="card-header">
								<h5> <i class="ti-thumb-up"></i> Interview</h5>
								<a href="#!"  onclick="viewAllRecord('COM_LIST_VIEW_ALL_DASH_BOARD','ACTIVE')" class=" b-b-primary text-primary float-right">View all</a>
							</div>
							<div class="card-block custom-scroll" style=" height: 300px;">
								<?php if(count((array)$approved_job_candidate_interview_schedules) !=0){ ?>
								<table class="table table-hover m-b-0" style="font-size:13px;">
									<body>
										<?php foreach($approved_job_candidate_interview_schedules as $jcis){ ?>
										<tr>
											<td class="w-b-w-s p-2">
												<a href="index.php?option=COM_JOB_CANDIDATE_POPUP&job_post_can_id=<?php echo $jcis['job_posing_candidate_id']; ?>">
													<span class="d-block"><i class="ti-user"></i> <?php echo $jcis['candidate_name']; ?></span>
													<span class="d-block"><i class="ti-alarm-clock"></i> <?php echo date('d-m-Y H:i A',strtotime($jcis['inerview_date_time']));?></span>
												</a>
											</td>
										</tr>
									<?php } ?>
									</body>
								</table>
							<?php  }else{  ?>
									<div class="card-px text-center py-20 m-t-30">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Schedule Interview </p>   
										<div class="text-center px-4">
											<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
									</div>
							<?php } ?>
							</div>
						</div>
						<div class="card sale-card mb-2">
							<div class="card-header">
								<h5>Interview</h5>
								<a href="#!"  onclick="viewAllRecord('COM_LIST_VIEW_ALL_DASH_BOARD','ACTIVE')" class=" b-b-primary text-primary float-right">View all</a>
							</div>
							<div class="card-block custom-scroll" style=" height: 300px;">
								<?php if(count((array)$job_candidate_interview_schedules) !=0){ ?>
								<table class="table table-hover m-b-0" style="font-size:13px;">
									<body>
										<?php foreach($job_candidate_interview_schedules as $jcis){ ?>
										<tr>
											<td class="w-b-w-s p-2">
												<a href="index.php?option=COM_JOB_CANDIDATE_POPUP&job_post_can_id=<?php echo $jcis['job_posing_candidate_id']; ?>">
													<span class="d-block"><i class="ti-user"></i> <?php echo $jcis['candidate_name']; ?></span>
													<span class="d-block"><i class="ti-alarm-clock"></i> <?php echo date('d-m-Y H:i A',strtotime($jcis['inerview_date_time']));?></span>
												</a>
											</td>
										</tr>
									<?php } ?>
									</body>
								</table>
							<?php  }else{  ?>
									<div class="card-px text-center py-20 m-t-30">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Schedule Interview </p>   
										<div class="text-center px-4">
											<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
									</div>
							<?php } ?>
							</div>
						</div>
					</div>
					<div class="col-lg-9">
						<div class="card mb-2">
							<div class="p-2">
								<ul class="nav md-tabs infisuite_tab" role="tablist">
								<?php $mk = 1; foreach($montharray as $key=>$ff){
									$jobpostingcandidates55 = $job_posting_candidates[$key];
									?>
									<li class="nav-item">
										<a class="nav-link f-w-600 f-12 <?php echo $mk == count((array)$montharray)?"active":""; ?>" data-bs-toggle="tab" href="#month<?php echo date("M",strtotime($ff));?>" role="tab" aria-selected="true"><?php echo date("F",strtotime($ff));?><label class="form-label label label-xs label-success m-l-10"><?php echo count((array)$jobpostingcandidates55); ?></label></a>
									</li>
									<?php $mk++; } ?>

								</ul>
							</div>
								<div class="tab-content tabs">
									<?php $mm = 1; foreach($montharray as $key1=>$datev){ 
										$jobpostingcandidates = $job_posting_candidates[$key1];
										?>
										<div class="tab-pane <?php echo $mm == count((array)$montharray)?"active":""; ?> card-body" id="month<?php echo date("M",strtotime($datev));?>" role="tabpanel">
											<?php if(count((array)$jobpostingcandidates)  !=0){ //print_r($jobpostingcandidates); ?>	
												<table class="table table-hover m-b-0" style="font-size:13px;">
													<thead>
														<tr style="border-bottom-style: hidden;">
															<th></th>
															<th class="text-center" ></th>

															<th class="text-center" ></th>

															<th class="text-center p-0"  colspan="2" >ShortListed</th>
															<th class="text-center p-0" colspan="4" >LoI</th>
														</tr>
														<tr >
															<th>Job Name</th>
															<th class="text-center" >No. Res.</th>

															<th class="text-center" >Applied</th>
															<th class="text-center"><label class="form-label label label-xs label-success m-l-10" style="background-color: <?php echo strval($stausresult[ST_HMSHORTLISTED]['background_color']);?> !important" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="RM ShotListed">RM</label></th>
															<th class="text-center"><label class="form-label label label-xs label-success m-l-10" style="background-color: <?php echo strval($stausresult[ST_RMSHORTLISTED]['background_color']);?> !important" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="HM ShortListed">HM</label></th>
															<th class="text-center"><label class="form-label label label-xs label-success m-l-10" style="background-color: <?php echo strval($stausresult[ST_LOIREQUESTED]['background_color']);?> !important" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Requested">Req</label></th>
															<th class="text-center"><label class="form-label label label-xs label-success m-l-10" style="background-color: <?php echo strval($stausresult[ST_LOIGENERATED]['background_color']);?> !important" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Generated">Gen</label></th>
															<th class="text-center"><label class="form-label label label-xs label-success m-l-10" style="background-color: <?php echo strval($stausresult[ST_LOIACCEPTED]['background_color']);?> !important" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Accepted">Acc</label></th>
															<th class="text-center"><label class="form-label label label-xs label-success m-l-10" style="background-color: <?php echo strval($stausresult[ST_LOIREJECTED]['background_color']);?> !important" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Rejected">Rej</label></th>
														</tr>
											
										</thead>
										<body>
											<?php foreach($jobpostingcandidates as $jobpostingcandidate){ 
												$statucolorname = $stausresult[$jobpostingcandidate['current_status']];
												?>
												<tr>
													<td class="w-b-w-s">
														<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo deocdeId("id:".$jobpostingcandidate['id']); ?>"><?php echo $jobpostingcandidate['job_name']; ?>
														<span class="f-10 d-block"><?php echo $_SESSION['users'][$jobpostingcandidate['requested_by']]; ?></span>
														</a>
													</td>

												<td class="text-center cursor-pointer" ><span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="No Of Resources"><?php echo $jobpostingcandidate['no_of_resources']; ?></span></td>

												<td class="text-center text-primary cursor-pointer" onclick="getJobPostingApplyCandidate('<?php echo $jobpostingcandidate['id']; ?>','All')"><span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Applied Candidates"><?php echo $jobpostingcandidate['apply_total']; ?></span></td>
												<td class="text-center text-primary cursor-pointer" onclick="getJobPostingApplyCandidate('<?php echo $jobpostingcandidate['id']; ?>','current_status','<?php echo ST_RMSHORTLISTED; ?>')"><span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="RM ShortListed"><?php echo $jobpostingcandidate['rm_shortlisted']; ?></span></td>
												<td class="text-center text-primary cursor-pointer" onclick="getJobPostingApplyCandidate('<?php echo $jobpostingcandidate['id']; ?>','current_status','<?php echo ST_HMSHORTLISTED; ?>')"><span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="HM ShortListed"><?php echo $jobpostingcandidate['hm_shortlisted']; ?></span></td>
												<td class="text-center text-primary cursor-pointer" onclick="getJobPostingApplyCandidate('<?php echo $jobpostingcandidate['id']; ?>','current_status','<?php echo ST_LOIREQUESTED; ?>')"><span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Requested"><?php echo $jobpostingcandidate['loi_requested']; ?></span></td>
												<td class="text-center text-primary cursor-pointer" onclick="getJobPostingApplyCandidate('<?php echo $jobpostingcandidate['id']; ?>','current_status','<?php echo ST_LOIGENERATED; ?>')"><span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Generated"><?php echo $jobpostingcandidate['loi_generated']; ?></span></td>
												<td class="text-center text-primary cursor-pointer"   onclick="getJobPostingApplyCandidate('<?php echo $jobpostingcandidate['id']; ?>','current_status','<?php echo ST_LOIACCEPTED; ?>')"><span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Accepted"><?php echo $jobpostingcandidate['loi_accepted']; ?></span></td>
												<td class="text-center text-primary cursor-pointer" onclick="getJobPostingApplyCandidate('<?php echo $jobpostingcandidate['id']; ?>','current_status','<?php echo ST_LOIREJECTED; ?>')"><span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Rejected"><?php echo $jobpostingcandidate['loi_rejected']; ?></span></td>
											</tr>
										<?php } ?>
									</body>
								</table>
							<?php }else{  ?>
								<div class="card-px text-center py-20 m-t-30">
									<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Jobs Posting </p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
									</div>   
								</div>

							<?php } ?>	
						</div>
						<?php $mm++; } ?>
					</div>
				</div>
				<!----- Start --->
				<div class="card">
					<div class="card-body">
						<div class="row ">
							<div class="col-lg-12">
								<ul class="nav md-tabs infisuite_tab" role="tablist">
									<li class="nav-item  m-1">
										<a class="nav-link f-w-600 f-12 active" style="padding: 0.5rem 0.5rem !important; " data-bs-toggle="tab" href="#apshort" role="tab" aria-selected="true">Applied <label class="form-label label label-xs label-success m-l-5"><?php echo count((array)$tabdata['apshort']); ?></label></a>
										<div class="slide"></div>
									</li>
									<li class="nav-item  m-1" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="RM ShortListed">
										<a class="nav-link  f-w-600 f-12" style="padding: 0.5rem 0.5rem !important; " data-bs-toggle="tab" href="#rhshort" role="tab" aria-selected="true">RM
											<label class="form-label label label-xs label-success m-l-5" style="background-color: <?php echo strval($stausresult[ST_RMSHORTLISTED]['background_color']);?> !important"><?php echo count((array)$tabdata['rhshort']); ?></label>
										</a>
										<div class="slide"></div>
									</li>
									<li class="nav-item  m-1" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="HM ShortListed">
										<a class="nav-link f-w-600 f-12" style="padding: 0.5rem 0.5rem !important; " data-bs-toggle="tab" href="#hmshort" role="tab" aria-selected="false">HM
											<label class="form-label label label-xs label-success m-l-5" style="background-color: <?php echo strval($stausresult[ST_HMSHORTLISTED]['background_color']);?> !important"><?php echo count((array)$tabdata['hmshort']); ?></label>
										</a>
										<div class="slide"></div>
									</li>
									<li class="nav-item  m-1" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Requested">
										<a class="nav-link f-w-600 f-12" style="padding: 0.5rem 0.5rem !important; " data-bs-toggle="tab" href="#lorshort" role="tab" aria-selected="false">LoI Req
											<label class="form-label label label-xs label-success m-l-5" style="background-color: <?php echo strval($stausresult[ST_LOIREQUESTED]['background_color']);?> !important"><?php echo count((array)$tabdata['lorshort']); ?></label>
										</a>
										<div class="slide"></div>
									</li>
									<li class="nav-item  m-1" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Generated">
										<a class="nav-link f-w-600 f-12" style="padding: 0.5rem 0.5rem !important; " data-bs-toggle="tab" href="#logshort" role="tab" aria-selected="false" >LoI Gen
											<label class="form-label label label-xs label-success m-l-5" style="background-color: <?php echo strval($stausresult[ST_LOIGENERATED]['background_color']);?> !important"><?php echo count((array)$tabdata['logshort']); ?></label>
										</a>
										<div class="slide"></div>
									</li>
									<li class="nav-item  m-1" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Accepted">
										<a class="nav-link f-w-600 f-12" style="padding: 0.5rem 0.5rem !important; " data-bs-toggle="tab" href="#loashort" role="tab" aria-selected="false">LoI Acc
											<label class="form-label label label-xs label-success m-l-5 " style="background-color: <?php echo strval($stausresult[ST_LOIACCEPTED]['background_color']);?> !important"><?php echo count((array)$tabdata['loashort']); ?></label>
										</a>
										<div class="slide"></div>
									</li>
									<li class="nav-item m-1" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="LoI Rejected">
										<a class="nav-link f-w-600 f-12" style="padding: 0.5rem 0.5rem !important; " data-bs-toggle="tab" href="#loashort" role="tab" aria-selected="false">LoI Rej
											<label class="form-label label label-xs label-success m-l-5" style="background-color: <?php echo strval($stausresult[ST_LOIREJECTED]['background_color']);?> !important"><?php echo count((array)$tabdata['loarejetced']); ?></label>
										</a>
										<div class="slide"></div>
									</li>
								</ul>
								<div class="tab-content tabs card-body custom-scroll" style=" height: 500px;">

									<?php $k=1; foreach($lola as $las){ ?>
										<div class="tab-pane <?php if($k==1){ echo "active"; } ?>" id="<?php echo $las; ?>" role="tabpanel">
											<div class="table-responsive">
												<table class="table table-hover m-b-0 " style="font-size:13px;">
													<thead>
                                                            <tr>
															<th scope="col">Candidate </th>
															<th scope="col">job Title</th>
															<th scope="col">Applied On</th>
															<th scope="col">Required By</th>
															<th scope="col">Status</th>
															<th scope="col">Action</th>
														</tr>
                                                        </thead>
													<tbody>
														<?php if(count((array)$tabdata[$las])>0){ 
															foreach ($tabdata[$las] as $tabval){
																?>
																<tr>
																	<td><a href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$tabval['candidate_id']);?>"><?php echo $tabval['candidate_name']; ?> </a></td>
																	<td><?php echo $tabval['job_name']; ?></td>
																	<td><?php echo fromDate($tabval['requested_date']); ?></td>
																	<td><?php echo $_SESSION['login_details']['allusers']['name'][$tabval['requested_by']] ; ?></td>
																	<td>
																		<label class="form-label label label-warning label-lg " style="background-color: <?php echo strval($stausresult[$tabval['current_status']]['background_color']);?> !important"><?php echo $tabval['view_status']; ?> </label>
																	</td> 
																	<td class="text-center">
																		<div class="btn-group">
																			<span class="cursor-pointer" onclick="displaystausdrop(<?php echo $tabval['candidate_id']; ?>,<?php echo "'candidates'"; ?>,<?php echo $tabval['current_status']; ?>,'tab_<?php echo $k;?>')" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" > <i class="ti-target"></i> </span>
																			<div class="dropdown-menu" id="dropdownlist_tab_<?php echo $k."_".$tabval['candidate_id']; ?>">
																			</div>
																		</div>
																	</td>
																</tr>
															<?php } }else{?>
																<tr>
																	<td colspan="5">
																		<div class="card-px text-center ">
																			<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Candidates Applied </p>   
																			<div class="text-center px-4">
																				<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
																			</div>   
																		</div>
																	</td>
																</tr>
															<?php } ?>
														</tbody>
													</table>
												</div>

											</div>
											<?php $k++; } ?>
										</div>

									</div>
								</div>
							</div>
						</div>
						<!------ end ---->

						<div class="card">
							<div class="card-body">
								<div class="row ">
									<div class="col-lg-6">
										<ul class="nav md-tabs infisuite_tab" role="tablist">
											<li class="nav-item">
												<a class="nav-link f-w-600 active" data-bs-toggle="tab" href="#current-quater" role="tab" aria-selected="true">Current Quater</a>
												<div class="slide"></div>
											</li>
											<li class="nav-item">
												<a class="nav-link f-w-600" data-bs-toggle="tab" href="#previous-quater" role="tab" aria-selected="false">Previous Quater</a>
												<div class="slide"></div>
											</li>
										</ul>
									</div>
									<div class="col-lg-6"></div>
								</div>
							</div>
							<div class="tab-content tabs card-body">
								<div class="tab-pane active" id="current-quater" role="tabpanel">
									<div class="row border-bottom pb-3 mb-3">
										<div class="col-lg-5">
											<?php 
											$dateloop = 0; $donutchartary = array();	foreach($montharray2 as $key2=>$date2){
												$jobpostingcandidates2 = $job_posting_candidates[$key2];
												//print_r($jobpostingcandidates2);
												if($dateloop > 2){
												//echo $jobpostingcandidates2['current_status']." ==ST_ACTIVE</br>";
													$donutchartlist = $donutchart[$key2];
													//print_r($donutchartlist);	
													$donutchartary[ST_ACTIVE] += $donutchartlist[ST_ACTIVE];
													$donutchartary[ST_INPROGRESS] += $donutchartlist[ST_INPROGRESS];
													$donutchartary[ST_HOLD] += $donutchartlist[ST_HOLD];
													$donutchartary[ST_COMPLETE] += $donutchartlist[ST_COMPLETE];

													$candidatecnt = $countcandidatenmonth[$key2] !=""?$countcandidatenmonth[$key2]:"0";	
													?>	
													<div class="p-3 b-radius-10 mb-3" style="border: 1px dashed #cccccc;">
														<div class="row align-items-center">
															<div class="col-lg-2">
																<button class="btn waves-effect waves-light bg-gray btn-icon">
																	<i class="ti-calendar text-primary f-w-600"></i>
																</button>
															</div>
															<div class="col-lg-5">
																<h5 class="f-w-600"><?php echo date('M',strtotime($date2));?></h5>
															</div>
															<div class="col-lg-5 text-right">
																<div class="d-flex align-items-center mt-3 float-right">
																	<span class="f-18 f-w-600 m-r-10"><?php echo count((array)$jobpostingcandidates2); ?> / <?php echo $candidatecnt; ?></span>
																	<!-- <label class="form-label label label-success">100%</label> -->
																</div>
															</div>
														</div>
													</div>
												<?php } $dateloop++; } ?>
											</div>
											<div class="col-lg-7">
												<input type="hidden" id="chart_data_2" class="chertvalues" data-id="chart_Donut0" value="Un-Published:<?php echo $donutchartary[ST_ACTIVE]; ?>||Published:<?php echo $donutchartary[ST_INPROGRESS]; ?>||Hold:<?php echo $donutchartary[ST_HOLD]; ?>||Closed:<?php echo $donutchartary[ST_COMPLETE]; ?>">
												<div class="card-header">
													<h5>Job Posting Chart</h5>
												</div>

												<div id="chart_Donut_2" style="width: 100%; height: 300px;"></div>
											</div>
										</div>
									</div>
									<div class="tab-pane " id="previous-quater" role="tabpanel">
										<div class="row border-bottom pb-3 mb-3">
											<div class="col-lg-5">
												<?php 
												$dateloop = 0;	$donutchartary = array(); foreach($montharray2 as $key2=>$date2){
													$jobpostingcandidates2 = $job_posting_candidates[$key2];
													if($dateloop == 3){
														Break;
													}
													$donutchartlist = $donutchart[$key2];
													$donutchartary[ST_ACTIVE] += $donutchartlist[ST_ACTIVE];
													$donutchartary[ST_INPROGRESS] += $donutchartlist[ST_INPROGRESS];
													$donutchartary[ST_HOLD] += $donutchartlist[ST_HOLD];
													$donutchartary[ST_COMPLETE] += $donutchartlist[ST_COMPLETE];

													$candidatecnt = $countcandidatenmonth[$key2] !=""?$countcandidatenmonth[$key2]:"0";	


													?>	
													<div class="p-3 b-radius-10 mb-3" style="border: 1px dashed #cccccc;">
														<div class="row align-items-center">
															<div class="col-lg-2">
																<button class="btn waves-effect waves-light bg-gray btn-icon">
																	<i class="ti-calendar text-primary f-w-600"></i>
																</button>
															</div>
															<div class="col-lg-5">
																<h5 class="f-w-600"><?php echo date('M',strtotime($date2));?></h5>
															</div>
															<div class="col-lg-5 text-right">
																<div class="d-flex align-items-center mt-3 float-right">
																	<span class="f-18 f-w-600 m-r-10"><?php echo count((array)$jobpostingcandidates2); ?> / <?php echo $candidatecnt; ?></span>
																	<!-- <label class="form-label label label-success">100%</label> -->
																</div>
															</div>
														</div>
													</div>

													<?php $dateloop++;} //print_r($donutchartary); ?>

												</div>
												<div class="col-lg-7">
													<input type="hidden" id="chart_data_1" class="chertvalues" data-id="chart_Donut0" value="Un-Published:<?php echo $donutchartary[ST_ACTIVE]; ?>||Published:<?php echo $donutchartary[ST_INPROGRESS]; ?>||Hold:<?php echo $donutchartary[ST_HOLD]; ?>||Closed:<?php echo $donutchartary[ST_COMPLETE]; ?>">
													<div class="card-header">
														<h5>Job Posting Chart</h5>
													</div>

													<div id="chart_Donut_1" style="width: 100%; height: 300px;"></div>
												</div>
											</div>
										</div>
										
									</div>   
								</div>
							</div>        
						</div>
					</div>
					<div class="col-lg-3">
						<?php if($_SESSION['role_ids'][0]==HIRINGMANAGER_ROLE){ ?>
							<div class="card mb-2">
								<div class="card-header">
									<h5>Active Jobs 
										<label class="form-label label label-xs label-success m-l-10" style="background-color: <?php echo strval($stausresult[$job_postings['0']['current_status']]['background_color']);?> !important" ><?php echo $result['job_postings_count']; ?></label>
									</h5>
									<?php // if($result['job_postings_count']>3){ ?>
										<!-- <a href="index.php?option=COM_LIST_VIEW_ALL_DASH_BOARD&TYPE=ACTIVE" class=" b-b-primary text-primary float-right">View all</a> -->
										<a href="#!"  onclick="viewAllRecord('COM_LIST_VIEW_ALL_DASH_BOARD','ACTIVE')" class=" b-b-primary text-primary float-right">View all</a>

										<!-- <a href="#" class=" b-b-primary text-primary float-right">View all</a> -->
										<?php // } ?>
									</div>
									<div class="card-body">
										<?php if(count((array)$job_postings)>0){
											foreach($job_postings as $jobpostings){ ?>
												<div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">

													<div class="col-auto">
														<div class="p-2 bg-primary b-radius-10 text-center left-icon-width" 
														style="background-color: <?php echo strval($stausresult[$jobpostings['current_status']]['background_color']);?> !important">
														<i class="ti-briefcase f-w-600"></i>
													</div>
												</div>
												<div class="col p-0 m-l-10">
													<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo deocdeId("id:".$jobpostings['id']); ?>"><h6 class="f-w-600"><?php echo $jobpostings['job_name']; ?></h6>
														<span class="text-muted f-12"><?php echo $jobpostings['job_number']." - ".fromDate($jobpostings['requested_date']); ?></span></a>
													</div>

													<div class="col-lg-3 text-right">
														<div class="btn-group">
															<span onclick="displaystausdrop(<?php echo $jobpostings['id']; ?>,<?php echo "'job_postings'"; ?>,<?php echo $jobpostings['current_status']; ?>,'act')" class="btn " style="color: <?php echo "#". $nmm['color']; ?>;    background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="ti-target"></i> </span>
															<div class="dropdown-menu" id="dropdownlist_act_<?php echo $jobpostings['id']; ?>">
															</div>
														</div>

													</div>
												</div>
											<?php } }else{?>
												<div class="card-px text-center ">
													<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Jobs Posting </p>   
													<div class="text-center px-4">
														<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
													</div>   
												</div>
											<?php } ?>
										</div>
									</div>
								<?php } ?>

								<div class="card mb-2">
									<div class="card-header">
										<h5><?php echo $result['job_post_status']['name']; ?>
										<label class="form-label label label-xs label-success m-l-10"style="background-color: <?php echo strval($stausresult[$result['job_post_status']['data']['0']['current_status']]['background_color']);?> !important" ><?php echo $result['job_post_status']['count']; ?></label>

									</h5>
									<a href="#!"  onclick="viewAllRecord('COM_LIST_VIEW_ALL_DASH_BOARD','YET')" class=" b-b-primary text-primary float-right">View all</a>
								</div>
								<div class="card-body">
									<?php if(count((array)$result['job_post_status']['data'])>0){
										foreach($result['job_post_status']['data'] as $jobpostings){ ?>
											<div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">

												<div class="col-auto">
													<div class="p-2 bg-primary b-radius-10 text-center left-icon-width" style="background-color: <?php echo strval($stausresult[$jobpostings['current_status']]['background_color']);?> !important">
														<i class=" ti-briefcase f-w-600"></i>
													</div>
												</div>
												<div class="col p-0 m-l-10">
													<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo deocdeId("id:".$jobpostings['id']); ?>">
														<h6 class="f-w-600"><?php echo $jobpostings['job_name']; ?></h6>
														<span class="text-muted f-12"><?php echo $jobpostings['job_number']." - ".fromDate($jobpostings['requested_date']); ?></span>
													</a>
												</div>

											<?php if($_SESSION['role_ids'][0]==HIRINGMANAGER_ROLE){ ?>
												<div class="col-lg-3">
													<span class="text-muted f-10">
														<i class="far fa-clock"></i>
														<?php echo  notificationdatesdiff($jobpostings['updated_date']);?>
													</span>
												</div>
											<?php }else{?> 
												<div class="col-lg-3 text-right">
													<div class="btn-group">
														<span  class="btn "  type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="ti-target"></i> </span>
														<div class="dropdown-menu">
															<a href="#!" class="dropdown-item waves-effect waves-light" onclick="publishdatapopup(<?php echo $jobpostings['id']; ?>)" name="status" id="status" value="">Publish</a>
														</div>
													</div>

												</div>
											<?php } ?>

										</div>
									<?php } }else{?>
										<div class="card-px text-center ">
											<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Jobs Posting </p>   
											<div class="text-center px-4">
												<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
											</div>   
										</div>
									<?php } ?>
								</div>
							</div>

							<div class="card mb-2">
								<div class="card-header">
									<h5><?php echo $result['recent_publish']['name']; ?>
									<label class="form-label label label-xs label-success m-l-10"style="background-color: <?php echo strval($stausresult[$result['recent_publish']['data']['0']['current_status']]['background_color']);?> !important" ><?php echo $result['recent_publish']['count']; ?></label>
								</h5>
								<a href="#!"  onclick="viewAllRecord('COM_LIST_VIEW_ALL_DASH_BOARD','PUBLISH')" class=" b-b-primary text-primary float-right">View all</a>
							</div>
							<div class="card-body">
								<?php  if(count((array)$result['recent_publish']['data'])>0){
									foreach($result['recent_publish']['data'] as $jobpostings){ ?>
										<div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">

											<div class="col-auto">
												<div class="p-2 bg-primary b-radius-10 text-center left-icon-width" style="background-color: <?php echo strval($stausresult[$jobpostings['current_status']]['background_color']);?> !important">
													<i class=" ti-briefcase f-w-600"></i>
												</div>
											</div>
											<div class="col p-0 m-l-10">
												<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo deocdeId("id:".$jobpostings['id']); ?>">
													<h6 class="f-w-600"><?php echo $jobpostings['job_name']; ?></h6>
													<span class="text-muted f-12"><?php echo $jobpostings['job_number']." - ".fromDate($jobpostings['requested_date']); ?></span>
												</a>
											</div>
										<?php if($_SESSION['role_ids'][0]==HIRINGMANAGER_ROLE){ ?>
											<div class="col-lg-3">
												<span class="text-muted f-10">
													<i class="far fa-clock"></i>
													<?php echo  notificationdatesdiff($jobpostings['updated_date']);?>
												</span>
											</div>
										<?php }else{?> 
											<div class="col-lg-3 text-right">
												<div class="btn-group">
													<span  class="btn "  type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="ti-target"></i> </span>
													<div class="dropdown-menu">
														<a href="#!" class="dropdown-item waves-effect waves-light" onclick="publishdatapopup(<?php echo $jobpostings['id']; ?>)" name="status" id="status" value="">Publish</a>
													</div>
												</div>

											</div>
										<?php } ?>
									</div>
								<?php }  }else{?>
									<div class="card-px text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Jobs Posting </p>   
										<div class="text-center px-4">
											<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
										</div>   
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="card mb-2">
							<div class="card-header">
								<h5><?php echo $result['recent_unpublish']['name']; ?>
								<label class="form-label label label-xs label-success m-l-10"style="background-color: <?php echo strval($stausresult[$result['recent_unpublish']['data']['0']['current_status']]['background_color']);?> !important" ><?php echo $result['recent_unpublish']['count']; ?></label>
							</h5>
							<a href="#!"  onclick="viewAllRecord('COM_LIST_VIEW_ALL_DASH_BOARD','UNPUBLISH')" class=" b-b-primary text-primary float-right">View all</a>
						</div>
						<div class="card-body">
							<?php if(count((array)$result['recent_unpublish']['data'])>0){
								foreach($result['recent_unpublish']['data'] as $jobpostings){ ?>
									<div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">

										<div class="col-auto">
											<div class="p-2 bg-primary b-radius-10 text-center left-icon-width" style="background-color: <?php echo strval($stausresult[$jobpostings['current_status']]['background_color']);?> !important">
												<i class=" ti-briefcase f-w-600"></i>
											</div>
										</div>
										<div class="col p-0 m-l-10">
											<a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo deocdeId("id:".$jobpostings['id']); ?>">
												<h6 class="f-w-600"><?php echo $jobpostings['job_name']; ?></h6>
												<span class="text-muted f-12"><?php echo $jobpostings['job_number']." - ".fromDate($jobpostings['requested_date']); ?></span>
											</a>
										</div>

									<div class="col-lg-3">
										<span class="text-muted f-10">
											<i class="far fa-clock"></i>
											<?php echo  notificationdatesdiff($jobpostings['updated_date']);?>
										</span>
									</div>

								</div>
							<?php } }else{?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Jobs Posting </p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
									</div>   
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane card-body <?php echo ($_GET['TAB'] =="Cand")?"active":"";?>" id="Candidates-tab" role="tabpanel">
			<?php include('custom_view_recruitingmanager_dashboard_candidates.php'); ?>
		</div>
	</div>
