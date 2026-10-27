<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
	$kratable = $_POST['table'];
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$_POST['id']);
	$wh1['field'] = "id,area_name,description,weightage,kra_type";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=".$kratable."&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$json = $json[0];
?>
	<div class="modal-header">
		<h5 class="modal-title">Edit KRA</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	</div>
	<input type="hidden" name="pop_kra_id" id="pop_kra_id" value="<?php echo $_POST['id']; ?>">
	<div class="modal-body" > 
		<div class="row align-items-center" >
			<div class="row">
				<div class="col-lg-4 form-group">
					<label class="form-label">KRA Name <span class="text-danger">*</span></label>
					<input type="text"  class="form-control text-capitalize"  id="pop_area_name" value="<?php echo $json['area_name']; ?>" />
				</div>
				<div class="col-lg-4 form-group">
					<label class="form-label">Description <span class="text-danger">*</span></label>
					<textarea class="form-control"   id="pop_descrption"> <?php echo $json['description']; ?></textarea>
				</div>
				<div class="col-lg-2 form-group">
					<label class="form-label">Weightage<span class="text-danger">*</span></label>
					<input type="text"  class="form-control text-capitalize"  id="pop_weightage"  value="<?php echo $json['weightage']; ?>" />
				</div>
				<div class="col-lg-2 form-group w-full">
					<label class="form-label">Type<span class="text-danger">*</span></label>
					<select class="form-control  form-select js-example-basic-single"  id="pop_kra_type" ><option value="1" <?php echo $json['kra_type'] == '1'?'selected':''; ?>>Professional</option><option value="2" <?php echo $json['kra_type'] == '2'?'selected':''; ?>>Personal</option></select>
				</div>

			</div>		
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" >Cancel</button>
		<button type="button" class="btn btn-primary " onclick="addrowskra('update')" >Submit</button>
	</div>
</div>
