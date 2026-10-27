window.addEventListener("load", function() {
	
	var js_tas_id =$("#js_tas_id").val();
	if(js_tas_id !=""){
		getCheckList(js_tas_id);	
	}

	$(document).on("click", ".getCheckList", function() {
		var dataid = $(this).data("id");
		getCheckList(dataid);
	});

	$(document).on("click", ".getSingleCheckList", function() {
		var task_id = $(this).data("task_id");
		var checklist_position = $(this).data("positionkey");
		getSingleCheckList(task_id,checklist_position);
	});


	$(document).on("click", ".dynamicFilePriviews", function() {
		var pathurl = $(this).data("pathurl");
		dynamicFilePriviews(pathurl);
	});
	

	$(document).on("click", ".UpdateTransactionActivitySchedule1", function() {
		var seqid = $(this).data("seqid");
		UpdateTransactionActivitySchedule1(seqid);
	});
	
	$(document).on("click", ".submitSingleCheckListBtn", function() {
		submitSingleCheckListBtn();
	});
	$(document).on("click", ".cancelSingleCheckListBtn", function() {
		cancelSingleCheckListBtn();
	});

	$(document).on("click", ".updateUrlParameter_task", function() {
		var param = $(this).data("param");
		var paramvalue = $(this).data("paramvalue");
		updateUrlParameter(param, paramvalue);
	});
	

	
	$(document).on("click", ".submitDynamicWorkflowPopUp", function() {
		var table_name = $(this).data("table_name");
		var transaction_id = $(this).data("transaction_id");
		var current_status = $(this).data("current_status");
		var next_status = $(this).data("next_status");
		submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status);
	});


});


function getCheckList(task_id){
		$('#tasklistdiv').removeClass('col-lg-12');
		$('#tasklistdiv').addClass('col-lg-6');
		$('#htmlchecklist').removeClass('col-lg-12');
		$('#htmlchecklist').addClass('col-lg-6');
		$('#htmlchecklist').show('slow');
		$('#htmlsinglechecklist').hide();

		$('.all_select_task').removeClass('b-success');
		$('.select_task_'+task_id).addClass('b-success');
		//$('#htmlsinglechecklist').show('slow');
		$('#htmlchecklist').removeClass('custom_readonly');
		var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
		$('#htmlchecklist').html(htmloader);
		//var nodata = '<div class="card"><div class="card-body"><div class="card-px text-center py-20 mt-2"><p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Check List Select  </p> <div class="text-center px-4"><img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;"></div></div></div></div> ';
		//$('#htmlsinglechecklist').html(nodata);
		var currentUrl = window.location.href;
		var urlParts = currentUrl.split('?');
		var transaction_table = $("#transaction_table_id").val();
		$.ajax({
	        url: "ajax.php",
	        data:'page=gettransactionactivityschedulecheckList&task_id='+task_id+'&transaction_table='+transaction_table+'&'+urlParts['1'],
	        type: "POST",
	        success:function(data){
	        	$("#htmlchecklist").html(data);
	        	$('[data-bs-toggle="tooltip"]').tooltip();
	        },
	        error:function (){}
    	});
	}
	function getSingleCheckList(task_id,checklist_position){
		//$('#htmlchecklist').addClass('custom_readonly');
		$('#tasklistdiv').removeClass('col-lg-6');
		$('#tasklistdiv').addClass('col-lg-4');
		$('#htmlchecklist').removeClass('col-lg-6');
		$('#htmlchecklist').addClass('col-lg-4');
		$('#htmlsinglechecklist').show('slow');

		var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
		$('#htmlsinglechecklist').html(htmloader);
		var nodata = '<div class="card"><div class="card-body"><div class="card-px text-center py-20 mt-2"><p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Check List Select  </p> <div class="text-center px-4"><img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;"></div></div></div></div> ';
		$('#htmlsinglechecklist').html(nodata);
		var transaction_table = $("#transaction_table_id").val();
		var currentUrl = window.location.href;
		var urlParts = currentUrl.split('?');
		if($('#checklist_checkbox_'+task_id+'_'+checklist_position).is(":checked")){
		//if($('#checklist_checkbox_'+task_id+'_'+checklist_position).get(0).checked){
			$('#checklist_label_'+task_id+'_'+checklist_position).addClass('check-task check-task done-task');
			$.ajax({
		        url: "ajax.php",
		        data:'page=gettransactionactivityschedulesinglechecklist&task_id='+task_id+'&transaction_table='+transaction_table+'&checklist_position='+checklist_position+'&'+urlParts['1'],
		        type: "POST",
				async: true,
		        success:function(data){
		        	$("#htmlsinglechecklist").html(data);
		        	$('[data-bs-toggle="tooltip"]').tooltip();
		        },
		        error:function (){}
	    	});
		}else{
			$('#checklist_label_'+task_id+'_'+checklist_position).removeClass('check-task check-task done-task');
			//console.log("Not chevked");
		}	
	}
	function submitSingleCheckListBtn(){
		var transaction_table = $("#transaction_table_id").val();
		var rid = $("#riddd").val();
		var attachment_type = $("#attachment_type").val();
		var old_attachments = $("#old_attachments").val();
		var popupfile = $("#popupfile").val();
		var comment = $("#comment").val();
		if(comment == ''){
			swal({
		            title: "",
		            text: "Please enter the details",
		            type: "error",
		            showCancelButton: true,
		            showConfirmButton: false,
		            cancelButtonText: "Ok",
		            confirmButtonClass: "btn-danger",
		            closeOnCancel: false
		        });
			return false;	
		}else if(attachment_type == 'Y' && popupfile =="" && old_attachments ==""){
			swal({
		            title: "",
		            text: "Its mandatory to upload the attachment",
		            type: "error",
		            showCancelButton: true,
		            showConfirmButton: false,
		            cancelButtonText: "Ok",
		            confirmButtonClass: "btn-danger",
		            closeOnCancel: false
		        });
			return false;	
		}else{
			$("#submittaskanswer").text("Please Wait........");
			$("#submittaskanswer").addClass("disabled");
		} 
		var formData = new FormData();
	    formData.append("page", "updatetransactionactivityschedule"); 
	    var dataArray = $("#submitsinglechecklistform").serializeArray(),
	    dataObj = {};
	    $(dataArray).each(function(i, field){
	      formData.append(field.name, field.value); 
	    });
	    var popattachment = document.getElementById('popupfile').files[0];
	   formData.append("file",popattachment);
		$.ajax({
			url: "ajax.php",
		    data: formData,
		    type: "POST",
		    async:true,
		    contentType: false,
		    processData: false,
		    success:function(response){
				console.log(response);
				var jsonres = JSON.parse(response);
				// if(jsonres.status == "<?php echo ST_INPROGRESS; ?>"){
				// 	$(".statusid_"+rid).html('<label class="form-label label" style="color: #FFFFFF;    background:  #f5b700; border-color: ##f5b700;">In Progress </label>');
				// }
				// if(jsonres.status == "<?php echo ST_COMPLETE; ?>"){
				// 	$('.sendapprove_'+rid).attr("disabled", true);
				// 	$(".statusid_"+rid).html('<label class="form-label label " style="color:#FFFFFF;background:#bc00dd;border-color:#bc00dd;">Completed</label>');
				// 	location.reload();
				// }
				if(transaction_table == 'candidates'){ 
					if(typeof loadDynamicPage == 'function'){
						loadDynamicPage('COM_INSERT_TRANSACTION_ACTIVITY_SCHEDULES','','','');	
					}else{
						location.reload();
					}
				}else{ 
					if(typeof getAjaxDynamicPage == 'function'){
						getAjaxDynamicPage('custom_insert_transaction_activity_schedules','task_tab','',rid);
					}else{
						location.reload();
					}
					
				}
				tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Updated Successfully','');
				
				//$('#kt_modal_KTDatatable_remote').modal("hide");
		 	}
		});
	}

	function cancelSingleCheckListBtn(){
		var rid = $("#riddd").val();
		getCheckList(rid);
	}

function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
	swal({
        title: "",
        text: "Please note the status of the document will change to "+new_status_name,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
    function(){
        console.log("dataa aa");
        jQuery.ajax({
            url: "ajax.php",
            data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(response){
                console.log(response);
                var res = JSON.parse(response);
                //console.log(res);
                window.location.reload();
                if(res.transaction_update.status=='sucess'){
                     window.location.reload();
                    swal({
                        title: "",
                        text: "Submission Successful",
                        type: "success",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function () {
                        window.location.reload();
                    });
                }else{
                    swal({
                        title: "Oops...",
                        text: res.transaction_update.message,
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "Ok",
                        confirmButtonClass: "btn-danger",
                        closeOnCancel: false
                    });
                }
            },
            error:function (){}
        });
    }); 
}