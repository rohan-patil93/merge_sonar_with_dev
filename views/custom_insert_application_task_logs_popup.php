<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$rid = $_POST['rid'];
$apiurl = API."?mode=getprojecttaskdetailsfolloups&rid=".$rid;
$apicall = callAPI($apiurl);
$result = json_decode($apicall, 1);
$application_task_logs = $result['project_task_details']; 
$email_logs = $result['email_logs'];
$statuse = ViewStatusData();
//echo "<pre>"; print_r($result); echo "</pre>"; 
?>
<script type="text/javascript">
	function SubmitTaskDetails(){
		$("#popupshowerror").hide();
		var popup_comment = $("#popup_comment").val();
		if(popup_comment ==""){
			$("#popupshowerror").show();
		}else{
			var formdata = $("#tasksubmitform").serialize();
			$.ajax({
				url: "r_ajax_o.php",
				type: "POST",
				data: 'page=updateprojecttaskdetailsfollowup&'+formdata,
				success: function(response) {
					console.log(response);
					tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
					location.reload();
				}
			});
		}
	}
</script>
<div class="modal-header">
	<h4 class="modal-title">Task Details</h4>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-50 m-r-50" <?php if($application_task_logs['follow_up_type'] == EMAIL_TASK['type']){ ?>style="height: 450px;" <?php } ?>>
	<div class="row">
		<div class="form-group col-lg-3 text-center">
			<label class="form-label d-block">Task Name</label>
			<span><?php echo $application_task_logs['task_name'];?></span>
		</div>
		<div class="form-group col-lg-3 text-center">
			<label class="form-label d-block">Task Date</label>
			<span><?php echo fromDate($application_task_logs['planned_start']);?></span>
		</div>
		<div class="form-group col-lg-3  text-center">
			<label class="form-label d-block">Task Type</label>
			<span><?php echo $application_task_logs['follow_up_type'];?></span>
		</div>
		<div class="form-group col-lg-3  text-center">
			<label class="form-labe d-block">Status</label>
			<label class="form-label label label-md" style="color:<?php echo $statuse[$application_task_logs['current_status']]['color']; ?>; background-color:<?php echo $statuse[$application_task_logs['current_status']]['background_color']; ?>;">
                <?php echo $statuse[$application_task_logs['current_status']]['name']; ?>
            </label>
		</div>
	</div>
	<?php if($application_task_logs['follow_up_type'] == EMAIL_TASK['type']){ ?>
	<div class="row">
		<div class="col-lg1-12">
			<label class="form-label d-block">Attach Document</label>
			<a class="text-primary" href="uploads/invoices/<?php echo $email_logs['mail_attachments']; ?>" download><?php echo $email_logs['mail_attachments']; ?></a>
		</div>
		<div class="col-lg-12">
			<?php if($email_logs['content'] !=""){ ?>
				<embed  src="uploads/email/<?php echo $email_logs['content']; ?>" style="border: none; width: -webkit-fill-available; height: 300px;"></embed>
			<?php } ?>
		</div>
		
	</div>

	<?php }else{ ?>	
		<form id="tasksubmitform">
			<div class="row">
				<div class="form-group">
					<label class="form-label">Comment<span class="text-danger">*</span></label>
					<textarea  class="form-control" name="comment" id="popup_comment" ><?php echo $application_task_logs['description'];?></textarea>
				</div>
			</div>
			<input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>"> 
		</form>
	<?php } ?>
</div>
<div class="modal-footer" >
	<span class="text-danger f-w-700" id="popupshowerror" style="display: none">Please fill out all required fields</span>
	<button type="button" class="btn btn-primary waves-effect waves-light <?php if($application_task_logs['follow_up_type'] == EMAIL_TASK['type'] || $application_task_logs['current_status'] !='0'){ ?>disabled<?php } ?>" onclick="SubmitTaskDetails()" >Submit/Approved</button>
	<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>