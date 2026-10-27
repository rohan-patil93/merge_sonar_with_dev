<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

	$data1["where"] = array('company_id'=> $_SESSION['company_id']);
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=projects&data=".$data; 
	$cudata = callAPI($curl);
	$projects = json_decode($cudata,1);
/*	$curl = API."?mode=gettabledetailswithconditions&table=project_roles&data=".$data; 
	$cudata = callAPI($curl);
	$project_roles = json_decode($cudata,1);*/
?>
<script type="text/javascript">
	function dispalyprojectwisetasks(project_id){
			jQuery.ajax({
  			url: "ajax.php",
  			data:'page=customunassignprjecttaskdetails&project_id='+project_id,
  			type: "POST",
  			success:function(data){
  				console.log(data);
  				document.getElementById('task_id').innerHTML = data;
  			},
  			error:function (){}
  		});
	}
	function submitform(){
  		jQuery.ajax({
  			url: "ajax.php",
  			data:'page=setupcomponents&table_name=project_task_details&id='+$('#task_id').val()+'&assigen_id='+$('#user_id').val(),
  			type: "POST",
  			success:function(data){
  				console.log(data);
  				tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Upadate Project Task  Successfully','');
  				window.location.reload();
  			},
  			error:function (){}
  		});
  	}
</script>
<div class="modal-header">
	<h5 class="modal-title">Add Project Task to User</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<form id="assetsinsertform" method="post" >	
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $_POST['userid']; ?>">
		<div class="row ">
			<div class="col-lg-12 row">  
				<div class="form-group col-lg-12 w-full">
					<label>Project</label>
					 <select class="form-select js-example-basic-single " name="project_id" id="project_id" onchange="dispalyprojectwisetasks(this.value)">
						<option value="">Select Any One</option>
						<?php foreach ((array)$projects as $project) { ?>					
							<option value="<?php echo $project['id']; ?>"  ><?php echo ucwords($project['project_name']); ?></option>
						<?php } ?>
					</select> 
				</div> 
				<div class="form-group col-lg-12 w-full">
					<label>Project Task</label>
					<select class="form-select js-example-basic-single " name="task_id" id="task_id">
						<option value="">Select Any One</option>
						
					</select> 
				</div> 
			</div>
		</div>
	</form>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
	<button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitform()">Submit</button>
</div>
<script type="text/javascript">
  $(".js-example-basic-single").select2();
  $(".js-example-basic-multiple").select2();
  $(".js-example-disabled-results").select2();
  $( ".datepicker" ).datepicker({
    showOn: "both", 
    buttonText: "<i class='ti-calendar'></i>",
    dateFormat: date_format,
    changeMonth: true,
    changeYear: true
  });
</script>
