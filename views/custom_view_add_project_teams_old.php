<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$vurl = API."?mode=togetprojecteamaddetails&userid=".$_POST['userid']; 
$userdataa = callAPI($vurl);
$data = json_decode($userdataa,1);
//echo "<pre>"; print_r($_POST); print_r($data); echo "</pre>";
?>
<script type="text/javascript">
	function submitform(){
  		jQuery.ajax({
  			url: "ajax.php",
  			data:'page=setupcomponents&table_name=project_teams&user_id='+$('#user_id').val()+'&project_id='+$('#project_id').val()+'&role_id='+$('#role_id').val(),
  			type: "POST",
  			success:function(data){
  				console.log(data);
  				tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','User Add Successfully','');
  				window.location.reload();
  			},
  			error:function (){}
  		});
  	}
</script>
<div class="modal-header">
	<h5 class="modal-title">Add Project to User</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<form id="assetsinsertform" method="post" >	
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $_POST['userid']; ?>">
		<div class="row ">
			<div class="col-lg-12 row">  
				<div class="form-group col-lg-12 w-full">
					<label>Project</label>
					<select class="form-select js-example-basic-single " name="project_id" id="project_id">
						<option value="">Select Any One</option>
						<?php foreach ($data['projects'] as $project) { ?>					
							<option value="<?php echo $project['id']; ?>" <?php if(in_array($project['id'],$data['exist_projects'])){ echo  "disabled"; } ?> ><?php echo ucwords($project['project_name']); ?></option>
						<?php } ?>
					</select> 
				</div> 
				<div class="form-group col-lg-12 w-full">
					<label>Roles</label>
					<select class="form-select js-example-basic-single " name="role_id" id="role_id">
						<option value="">Select Any One</option>
						<?php foreach ($data['roles'] as $roles) { ?>					
							<option value="<?php echo $roles['id']; ?>"><?php echo ucwords($roles['role_name']); ?></option>
						<?php } ?>
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