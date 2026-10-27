<script>
function createFolder(){
	 $.ajax({
	    url: "ajax.php",
	    type: "POST",
	    data: 'page=createrecruitingmanagerdashboardcandidatesfolder',
	    success: function(response){
	    	var data = JSON.parse(response);
	    	if(data.status == "Success"){
	    		tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',data.message,'');
	       	 	location.reload();
	    	}else{
				tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',data.message,'');
	    	}
	    }
	});
}

function submitResumeFiles(){
	var formData = new FormData();
	formData.append('page', 'uploadresumefiles');
	var files = $('#resumes_files')[0].files;
	if (files && files.length > 0) {
		$.each(files, function (i, file) {
		    formData.append('resumes_files[]', file);
		});
		$.ajax({
		    url: 'ajax.php',
		    type: 'POST',
		    data: formData,
		    processData: false,
		    contentType: false,
		    success: function (response) {
		        console.log(response);
		        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','File uploaded successfully','');
		       	 location.reload();
		    }
		});
	} else {
		tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','Please select at least one file.','');
	    return false;
	}
}

function AddCandidate(){
    var extra_paramiter  = '{"option":"COM_LIST_CANDIDATES"}';
    var parent_option  = 'COM_LIST_CANDIDATES';
    insertadittrails(parent_option,'COM_INSERT_CANDIDATES_POPUP',extra_paramiter);
    var job_posting_id = $("#job_posting_id_ajax").val();
    jQuery.ajax({
        url: "views/custom_insert_candidates_popup.php",
        data:'job_posting_id='+job_posting_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
        },
        error:function (){}
    });
}

function getCandidateList(candidate_ids){
	var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $('#dynamic_popup1').html(loader);
	$("#kt_modal_dynamic_popup").modal("show");
	jQuery.ajax({
		url: "views/custom_view_recruitingmanager_dashboard_candidate_popup.php",
		data:'candidate_ids='+candidate_ids,
		type: "POST",
		success:function(data){
			$('#dynamic_popup1').html(data);
		},
		error:function (){}
	});
}
function SynchCandidateFolder(){
	var company_id = '<?php echo $_SESSION['company_id']; ?>';
	var data_name = '<?php echo $_SESSION['DB_NAME']; ?>';
	var user_id = '<?php echo $_SESSION['userid']; ?>';

	$("#ai_synch_candidate_folder").text("Please Wait........");
    $("#ai_synch_candidate_folder").addClass("disabled");
    $.ajax({
        url: "apis/recruitstac_folder_uploaded_files_with_ai.php",
        type: "POST",
        data:'company_id='+company_id+'&data_name='+data_name+'&user_id='+user_id,
        success: function(response) {
        	console.log(response);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Folder files scanned successfully. Candidates created successfully.','');
            location.reload();
        }
    });





}
</script>
<div class="row">
	<div class="col-lg-7">
		<div class="card ">
			<div class="card-header">
				<h5>Candidates</h5>
			</div>
			<div class="card-body">
				<div class="row ">
					<div class="col-lg-4">
						<div class="card prod-p-card card-primary mb-0">
							<div class="card-body" onclick="getCandidateList('<?php echo $candidate_dashboard['total_candidate_ids']; ?>')">
								<div class="row align-items-center">
									<div class="col">
										<h7 class="m-b-5 text-white">Total Candidate</h7>
										<h3 class="m-b-0 f-w-700 text-white f-22 mt-3"><?php echo $candidate_dashboard['total_candidates']; ?></h3>
									</div>
									<div class="col-auto">
										<i class="fas fa-money-bill-alt text-c-blue f-18" style="width:30px;height:30px;padding:9px 0;font-size:12px;"></i>
									</div>
								</div>
							</div>
						</div>
						<button type="button" class="btn btn-success btn-sm  mt-2" onclick="AddCandidate()"> <i class="icofont icofont-plus icon-white"></i> Candidate </button>
					</div>
					<div class="col-lg-4">
						<div class="card prod-p-card card-success mb-0">
							<div class="card-body" onclick="getCandidateList('<?php echo $candidate_dashboard['linked_candidate_ids']; ?>')">
								<div class="row align-items-center">
									<div class="col">
										<h7 class="m-b-5 text-white">Linked Job Postings</h7>
										<h3 class="m-b-0 f-w-700 text-white f-22 mt-3"><?php echo $candidate_dashboard['linked_candidates']; ?></h3>
									</div>
									<div class="col-auto">
										<i class="fas fa-money-bill-alt text-c-blue f-18" style="width:30px;height:30px;padding:9px 0;font-size:12px;"></i>
									</div>
								</div>
							</div>
						</div>
						<a href="index.php?option=COM_LIST_CANDIDATES" class="btn btn-warning btn-sm  mt-2"> <i class="ti ti-timer icon-white"></i>History</a>
					</div>
					<div class="col-lg-4">
						<div class="card prod-p-card card-danger mb-0">
							<div class="card-body" onclick="getCandidateList('<?php echo $candidate_dashboard['unlinked_candidate_ids']; ?>')">
								<div class="row align-items-center">
									<div class="col">
										<h7 class="m-b-5 text-white">Un-link Job Posting</h7>
										<h3 class="m-b-0 f-w-700 text-white f-22 mt-3"><?php echo $candidate_dashboard['unlinked_candidates']; ?></h3>
									</div>
									<div class="col-auto">
										<i class="fas fa-money-bill-alt text-c-blue f-18" style="width:30px;height:30px;padding:9px 0;font-size:12px;"></i>
									</div>
								</div>
							</div>
						</div>
						<button type="button" class="btn btn-success btn-sm mt-2" onclick="SynchCandidateFolder()"> <i class="icofont icofont-plus icon-white"></i> <span id="ai_synch_candidate_folder">Synch Resume Fld</span></button>
					</div>
	            </div>
	        </div>
	    </div>
	</div>
	<div class="col-lg-5">
		<div class="card sale-card mb-2 ">
			<div class="card-header">
				<h5>Candidates</h5>
				<div class="card-header-right table-card-header ">
				<div class="input-group input-icon-right input-group-button datepickershow_hr_detailT" style=""> 
              		<input class="form-control width1" type="file" id="resumes_files" name="resumes_files[]" accept="image/*" multiple>
                	<span class="input-group-text addon-style" onclick="submitResumeFiles()">
                    	<i class="ti-upload"></i>
                  	</span>
                </div>
            </div>
			</div>

			<div class="card-body" >
				<div class=" custom-scroll" style=" height: 150px;">
				<?php 
				$folder = "uploads/candidate_resume_with_rm/resume_rm_" . $_SESSION['userid'] . "/";
				if (is_dir($folder)) {
				    $files = glob($folder . "*");
				    usort($files, function ($a, $b) {
				        return filemtime($b) - filemtime($a);
				    });
				?>
				<table>
				<?php
				$im=1;
				foreach ($files as $file) {
				    if (is_file($file)) {
				?>
				    <tr>
				        <td class="w-b-w-s p-1">
				            <a href="<?php echo $file; ?>" class="text-primary" download><span class="f-12"><?php echo $im.". ".htmlspecialchars(basename($file)); ?></span></a>
				        </td>
				        <td>
				        	<span class="f-12"><?php echo date("d-M-Y h:i:s A", filemtime($file)); ?></span>
				        </td>
				    </tr>
				<?php
				    }
				$im++; }
				?>
				</table>
				<?php 
				} else { 
				?>
				<div class="card-block text-center">
				    <p><i class="fa-alert text-danger"></i> Folder Not Created Please Create</p>
				    <img class="img-fluid" src="assets/images/122.png" alt="" />

				    <div class="row mt-2">
				        <div class="col-lg-12 p-0">
				            <button type="button" class="btn btn-warning" onclick="createFolder('100')">
				                <i class="ti ti-timer icon-white"></i> Folder
				            </button>
				        </div>
				    </div>
				</div>

				<?php } ?>
			</div>
			
		</div>
	</div>
</div>
	<div class="col-lg-12">
		<div class="card ">
			<div class="card-header">
				<h5>AI Generated Candidates</h5>
			</div>
			<div class="card-body">
				<div class="row mt-4">
                    <div class="col-2 text-center cursor-pointer" onclick="getCandidateList('<?php echo $candidate_dashboard['total_candidate_ids']; ?>')">
                        <div class="s-caption  text-primary"><i class="ti-list f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $candidate_dashboard['total_candidates']; ?></h5>
                            <p class="m-b-0 f-12">Total</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center cursor-pointer" onclick="getCandidateList('<?php echo $candidate_dashboard['screening_started_ids']; ?>')">
                        <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $candidate_dashboard['screening_started']; ?></h5>
                            <p class="m-b-0 f-12">Screening Started</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center cursor-pointer" onclick="getCandidateList('<?php echo $candidate_dashboard['screening_not_started_ids']; ?>')">
                        <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $candidate_dashboard['screening_not_started']; ?></h5>
                            <p class="m-b-0  f-12">Screening Not Started</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center cursor-pointer" onclick="getCandidateList('<?php echo $candidate_dashboard['proceed_candidate_ids']; ?>')">
                        <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $candidate_dashboard['proceed_count']; ?></h5>
                            <p class="m-b-0 f-12">AI Approved</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center cursor-pointer" onclick="getCandidateList('<?php echo $candidate_dashboard['reject_candidate_ids']; ?>')">
                        <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $candidate_dashboard['reject_count']; ?></h5>
                            <p class="m-b-0 f-12">AI Rejected</p>
                        </div>
                    </div>
                     <div class="col-2 b-l-default text-center cursor-pointer" onclick="getCandidateList('<?php echo $candidate_dashboard['hold_candidate_ids']; ?>')">
                        <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $candidate_dashboard['hold_count']; ?></h5>
                            <p class="m-b-0 f-12">Decision Pending</p>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-3">
		<div class="card mb-2">
			<div class="card-header">
				<h5>Today Added Candidated <label class="form-label label label-xs label-primary text-white m-l-10" style="background-color:  !important"><?php echo count((array)$candidate_dashboard_today['created_today']); ?></label></h5>
			</div>
			<div class="card-body">
				<div class="input-group input-group-button mt-2">
		            <input type="text" id="dynamicfilterhideshow" data-id="candidatefilter" data-type="div" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search"> 
		            <span class="input-group-text addon-style">
		                <label class=""><span class="ti-search text-white mt-2"></span></label>
		            </span>
		        </div>
		        <div class="custom-scroll" style=" height: 450px;">
					<?php if(COUNT((array)$candidate_dashboard_today['created_today']) != 0){
							foreach($candidate_dashboard_today['created_today'] as $canres){
					?>
					<div class="align-middle p-1" id="candidatefilter">
	              		<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" data-toggle="tooltip" title="" data-original-title="<?php echo $canres['display_name']; ?>"><?php echo initials($canres['display_name']); ?></span>
	              		<div class="d-inline-block width73">
	              			<a href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$canres['id']); ?>" class="text-capitalize text-primary" onclick="getBasicInfoPopUp(<?php echo $canres['id']; ?>)" ><h6 class="mb-1"><?php echo $canres['display_name']; ?></h6></a>
	              			<p class="text-muted m-b-0"><?php echo $canres['email_id']!="" ? $canres['email_id'] : "&nbsp;"; ?></p>
	              			<p class="text-muted m-b-0"><?php echo $canres['created_date']; ?></p>
	              		</div>
	              	</div>
					<?php } ?>
					<?php }else{ ?>
					<div class="card-px text-center ">
						<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Candidates </p>   
						<div class="text-center px-4">
							<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
						</div>   
					</div>
					<?php } ?>
				</div>
			</div>

		</div>
	</div>
	<div class="col-lg-3">
		<div class="card mb-2">
			<div class="card-header">
				<h5>Today Screening Started<label class="form-label label label-xs label-primary  text-white m-l-10" style="background-color:  !important"><?php echo COUNT((array)$candidate_dashboard_today['screening_start_today']); ?></label>
				</h5>
			</div>
			<div class="card-body">
				<div class="input-group input-group-button mt-2">
		            <input type="text" id="dynamicfilterhideshow" data-id="candidatefilter1" data-type="div" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search"> 
		            <span class="input-group-text addon-style">
		                <label class=""><span class="ti-search text-white mt-2"></span></label>
		            </span>
		        </div>
		        <div class="custom-scroll" style=" height: 450px;">
				<?php if(COUNT((array)$candidate_dashboard_today['screening_start_today']) != 0){
						foreach($candidate_dashboard_today['screening_start_today'] as $canres){
				?>
				<div class="align-middle p-1" id="candidatefilter1">
              		<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" data-toggle="tooltip" title="" data-original-title="<?php echo $canres['display_name']; ?>"><?php echo initials($canres['display_name']); ?></span>
              		<div class="d-inline-block width73">
              			<a href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$canres['id']); ?>" class="text-capitalize text-primary" onclick="getBasicInfoPopUp(<?php echo $canres['id']; ?>)" ><h6 class="mb-1"><?php echo $canres['display_name']; ?></h6></a>
              			<p class="text-muted m-b-0"><?php echo $canres['email_id']!="" ? $canres['email_id'] : "&nbsp;"; ?></p>
              			<p class="text-muted m-b-0"><?php echo $canres['screening_start_datetime']; ?></p>
              		
              		</div>
              	</div>
				<?php } ?>
				<?php }else{ ?>
				<div class="card-px text-center ">
					<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Candidates </p>   
					<div class="text-center px-4">
						<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
					</div>   
				</div>
				<?php } ?>
			</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="card mb-2">
			<div class="card-header">
				<h5>Today Screening Completed<label class="form-label label label-xs label-primary  text-white m-l-10" style="background-color:  !important"><?php echo COUNT((array)$candidate_dashboard_today['screening_end_today']); ?></label>
				</h5>
			</div>
			<div class="card-body">
				<div class="input-group input-group-button mt-2">
		            <input type="text" id="dynamicfilterhideshow" data-id="candidatefilter2" data-type="div" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search"> 
		            <span class="input-group-text addon-style">
		                <label class=""><span class="ti-search text-white mt-2"></span></label>
		            </span>
		        </div>
		        <div class="custom-scroll" style=" height: 450px;">
				<?php if(COUNT((array)$candidate_dashboard_today['screening_end_today']) != 0){
						foreach($candidate_dashboard_today['screening_end_today'] as $canres){
				?>
				<div class="align-middle p-1" id="candidatefilter2">
              		<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" data-toggle="tooltip" title="" data-original-title="<?php echo $canres['display_name']; ?>"><?php echo initials($canres['display_name']); ?></span>
              		<div class="d-inline-block width73">
              			<a href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$canres['id']); ?>" class=" text-capitalize text-primary" onclick="getBasicInfoPopUp(<?php echo $canres['id']; ?>)" ><h6 class="mb-1"><?php echo $canres['display_name']; ?></h6></a>
              			<p class="text-muted m-b-0"><?php echo $canres['email_id']!="" ? $canres['email_id'] : "&nbsp;"; ?></p>
              			<p class="text-muted m-b-0"><?php echo $canres['screening_end_datetime']; ?></p>
              		
              		</div>
              	</div>
				<?php } ?>
				<?php }else{ ?>
				<div class="card-px text-center ">
					<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Candidates </p>   
					<div class="text-center px-4">
						<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
					</div>   
				</div>
				<?php } ?>
			</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="card mb-2">
			<div class="card-header">
				<h5>Today AI Approved<label class="form-label label label-xs label-primary  text-white m-l-10" style="background-color:  !important"><?php echo COUNT((array)$candidate_dashboard_today['ai_shortlisted_today']); ?></label>
				</h5>
			</div>
			<div class="card-body ">
				<div class="input-group input-group-button mt-2">
		            <input type="text" id="dynamicfilterhideshow" data-id="candidatefilter3" data-type="div" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search"> 
		            <span class="input-group-text addon-style">
		                <label class=""><span class="ti-search text-white mt-2"></span></label>
		            </span>
		        </div>
		        <div class="custom-scroll" style=" height: 450px;">
				<?php if(COUNT((array)$candidate_dashboard_today['ai_shortlisted_today']) != 0){
						foreach($candidate_dashboard_today['ai_shortlisted_today'] as $canres){
				?>
				<div class="align-middle p-1" id="candidatefilter3">
              		<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" data-toggle="tooltip" title="" data-original-title="<?php echo $canres['display_name']; ?>"><?php echo initials($canres['display_name']); ?></span>
              		<div class="d-inline-block width73">
              			<a href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$canres['id']); ?>" class="text-capitalize text-primary" onclick="getBasicInfoPopUp(<?php echo $canres['id']; ?>)" ><h6 class="mb-1"><?php echo $canres['display_name']; ?></h6></a>
              			<p class="text-muted m-b-0"><?php echo $canres['email_id']!="" ? $canres['email_id'] : "&nbsp;"; ?></p>
              			<p class="text-muted m-b-0"><?php echo $canres['ai_summary']!=""?$canres['ai_summary']:"-"; ?></p>
              	
              		</div>
              	</div>
				<?php } ?>
				<?php }else{ ?>
				<div class="card-px text-center ">
					<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Candidates </p>   
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



