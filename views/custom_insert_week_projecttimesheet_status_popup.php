<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$nm = ViewStatusValue($_POST['status'] ?? 10);
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$emp_id = $_POST['emp_id'] ?? ($_SESSION['emp_id'] ?? 0);
?>
<script type="text/javascript">
	function submitWeeklyTimesheet(){
	  	var status = $('#status_value').val();
 		var str = 'table=' + encodeURIComponent($('#table').val()) +
 		          '&comment=' + encodeURIComponent($('#comment').val()) +
 		          '&status=' + encodeURIComponent(status) +
 		          '&start_date=' + encodeURIComponent($('#start_date').val()) +
 		          '&end_date=' + encodeURIComponent($('#end_date').val()) +
 		          '&emp_id=' + encodeURIComponent($('#emp_id').val());
  		console.log(str);
	    $.ajax({
	        url: "ajax.php",
	        type: "POST",
	        data: 'page=dynamicchangestatusweekprojecttimesheet&' + str,
	        success: function(response) {
	        	console.log(response);
				$('#kt_modal_KTDatatable_remote').modal('hide');
				window.location.reload();
	        },
	        error: function(){
	        	alert('Error submitting timesheet for approval.');
	        }
	    });
	}
</script>

<div class="modal-header">
	<h5 class="modal-title">
		<small>&nbsp;&nbsp; <b class="f-w-600">Transaction Type: </b>&nbsp; Project Timesheets &nbsp;&nbsp;
		 <b class="f-w-600">Status: </b>&nbsp; <?php echo $nm['name'] ?? 'Submit for Approval'; ?> </small>
	</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" style="overflow: unset;"> 
	<form class="kt-form kt-form--label-right" action="" method="post">
		<div class="alert alert-info">
			<strong>Week Period:</strong> <?php echo date('d M Y', strtotime($start_date)); ?> to <?php echo date('d M Y', strtotime($end_date)); ?>
		</div>
		<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
			<div class="form-group row">
				<div class="col-lg-12">
					<label class="form-label f-w-600">Comment / Note</label>
					<textarea class="form-control" name="comment" id="comment" rows="3" placeholder="Add any comments for the manager (optional)"></textarea>
				</div>								
			</div>
		</div>
		<div class="modal-footer"> 
			<input type="hidden" name="status" id='status_value' value="<?php echo $_POST['status'] ?? 10; ?>">
			<input type="hidden" name="table" id='table' value="<?php echo $_POST['table'] ?? 'project_timesheets'; ?>">
			<input type="hidden" name="start_date" id='start_date' value="<?php echo $start_date; ?>"> 
			<input type="hidden" name="end_date" id='end_date' value="<?php echo $end_date; ?>">
			<input type="hidden" name="emp_id" id='emp_id' value="<?php echo $emp_id; ?>">
			<button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Cancel</button>
			<button type="button" class="btn btn-primary mr-2" onclick="submitWeeklyTimesheet()">Submit</button>
		</div>
	</form>
</div>

