<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
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
		<h5 class="modal-title"><?= __('Edit KRA') ?></h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= __('Close') ?>"></button>
	</div>
	<input type="hidden" name="pop_kra_id" id="pop_kra_id" value="<?php echo $_POST['id']; ?>">
	<div class="modal-body" > 
		<div class="row align-items-center" >
			<div class="row">
				<div class="col-lg-4 form-group">
					<label class="form-label" for="pop_area_name"><?= __('KRA Name') ?> <span class="text-danger">*</span></label>
					<input type="text" class="form-control text-capitalize" id="pop_area_name" name="pop_area_name" value="<?php echo $json['area_name']; ?>" aria-label="<?= __('KRA Name') ?>">
				</div>
				<div class="col-lg-4 form-group">
					<label class="form-label" for="pop_descrption"><?= __('Description') ?> <span class="text-danger">*</span></label>
					<textarea class="form-control" id="pop_descrption" name="pop_descrption" aria-label="<?= __('Description') ?>"><?php echo $json['description']; ?></textarea>
				</div>
				<div class="col-lg-2 form-group">
					<label class="form-label" for="pop_weightage"><?= __('Weightage') ?> <span class="text-danger">*</span></label>
					<input type="text" class="form-control text-capitalize" id="pop_weightage" name="pop_weightage" value="<?php echo $json['weightage']; ?>" aria-label="<?= __('Weightage') ?>">
				</div>
				<div class="col-lg-2 form-group w-full">
					<label class="form-label" for="pop_kra_type"><?= __('Type') ?> <span class="text-danger">*</span></label>
					<select class="form-control form-select js-example-basic-single" id="pop_kra_type" name="pop_kra_type" aria-label="<?= __('Type') ?>"><option value="1" <?php echo $json['kra_type'] == '1'?'selected':''; ?>><?= __('Professional') ?></option><option value="2" <?php echo $json['kra_type'] == '2'?'selected':''; ?>><?= __('Personal') ?></option></select>
				</div>

			</div>		
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" ><?= __('Cancel') ?></button>
		<button type="button" class="btn btn-primary " onclick="addrowskra('update')" ><?= __('Submit') ?></button>
	</div>
</div>
