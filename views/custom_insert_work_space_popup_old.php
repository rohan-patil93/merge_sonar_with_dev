<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
//$nm = ViewStatusValue($_POST['status']);
$toplist = array('Project', 'Team', 'Task');
$url = API."?mode=togetprojectinsertdata&project_id="; 
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$list_project = array('0'=>'Work Space','1'=>'Project Type', '2'=>'Projeject Settings', '3'=>'Project Team');
?>

<script type="text/javascript">
	function submitdrom(){
		console.log("Data");
		jQuery.ajax({
			url: "ajax.php",
			data:'page=setupcomponents&table_name=project_work_spaces&name='+$('#work_space').val(),
			type: "POST",
			success:function(data){
				console.log(data);
				tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Project Work Space Inserted Successfully','');
				window.location.reload();
			},
			error:function (){}
		});
	}

</script>

<div class="modal-header">
	<h5 class="modal-title"> Create Work Space</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="kt-form kt-form--label-right" action="" id="submitnextform" method="post">
	<div class="modal-body" > 
		<div class="pcoded-content">
			<div class="pcoded-inner-content">
				<div class="main-body">
					
					<div class="row " id="checknext0" >
						<div class="col-lg-5 mx-auto" >  
							<div class="form-group mt-4 w-full">
								<label class="f-w-600 mb-2">New Work Space</label></br>
								<input class="form-control " type="work_space" placeholder="" name="work_space" id="work_space">
							</div> 
							<button class="btn btn-primary waves-effect waves-light float-end p-2" type="button" onclick="submitdrom()" >Save</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>


