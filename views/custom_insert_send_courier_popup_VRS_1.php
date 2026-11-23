<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=togetcourier&id=".$_POST['id'];
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$emps = $data['employees'];
$row = $data['rowdata'];
?>
<script type="text/javascript">
	function submitincourierfrom(){
		var post = $("#submitformadditionalservice").serialize();
		jQuery.ajax({
			url: "ajax.php",
			data:'page=addcouriers&table_name=couriers&'+post,
			type: "POST",
			success:function(data){
				console.log(data);
				window.location.reload();
			},
			error:function (){}
		});
	}
</script>
<div class="modal-header">
	<h5 class="modal-title"><?php echo __('Add Out Courier'); ?>  </h5>
	<button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="<?php echo __('Close'); ?>"></button>
</div>
<div class="modal-body">
	<form class="kt-form " id="submitformadditionalservice" >
	<div  <?php if($row['created_by'] != $_SESSION['userid'] && $row['current_status']<11 && $_POST['id'] !=""  ){ ?>  style="pointer-events: none; opacity: 0.7;" <?php } ?>  >
		<div class="form-group">
			<label><?php echo __('Courier Name'); ?></label>
			<input type="text" name="courier_name" id="courier_name" class="form-control" value="<?php echo $row['courier_name']; ?>">
		</div>
		<div class="form-group">
			<label><?php echo __('Mobile'); ?></label>
			<input type="text" name="courier_mobile" id="courier_mobile" class="form-control" value="<?php echo $row['courier_mobile']; ?>">
		</div>
		<div class="form-group">
			<label><?php echo __('Address'); ?></label>
			<textarea class="form-control" name="courier_adderss" id="courier_adderss" ><?php echo $row['courier_adderss']; ?></textarea>
		</div>
			<div class="form-group">
			<label><?php echo __('Date'); ?></label>
			<input type="text" name="courier_date" class="form-control datepicker" value="<?php echo fromDate($row['courier_date']); ?>">
		</div>
		<div class="form-group w-full">
			<label for="requested_by" class="d-block"><?php echo __('Requested By'); ?> </label>
			<select  name="requested_by" id="requested_by" class="form-select js-example-basic-single">
				<option value=""><?php echo __('Select Any One'); ?></option>
				<?php foreach($emps as $emp){ ?>
					<option <?php if($row['requested_by']== $emp['id']){ echo __('selected');
					} ?>  value="<?php echo $emp['id']; ?>"><?php echo $emp['display_name']; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="form-group w-full">
				<label for="deliver_type" class="d-block"><?php echo __('Delivey Type'); ?> </label>
				<select  name="deliver_type" id="deliver_type" class="form-select js-example-basic-single">
					<option value=""><?php echo __('Select Any One'); ?></option>
					<?php foreach($data['deliver_type'] as $deliver_type){ ?>
						<option <?php if($row['deliver_type']== $deliver_type['genc_detail_code']){ echo __('selected');
						} ?> value="<?php echo $deliver_type['genc_detail_code']; ?>"><?php echo __($deliver_type['genc_detail_name']); ?></option>
					<?php } ?>
				</select>
		</div>
	</div>
	<?php
		if($row['id'] !=""){?>

		<div class="form-group">
				<label><?php echo __('Courier Company'); ?></label>
				<input type="text" name="courier_company" id="courier_company" class="form-control" value="<?php echo $row['courier_company']; ?>">
			</div>
			<div class="form-group">
				<label><?php echo __('Courier Reference Number'); ?></label>
				<input type="text" name="courier_ref_no" id="courier_ref_no" class="form-control" value="<?php echo $row['courier_ref_no']; ?>">
			</div>
		<div class="form-group">
				<label><?php echo __('Status'); ?> </label>
				<div class="radio radio-outline  mt-1">
					<?php foreach ($data['status'] as $selectkey => $selectvalue) {   ?>
						<label class="radio radio-inline"> <input type="radio"  <?php if($selectkey == $row['current_status']){ ?> checked <?php } ?> name="current_status" value='<?php echo $selectkey; ?>'>	<span></span><?php echo $selectvalue; ?> </label>
						<?php $sch++;
						} ?>
					</div>
				</div>
	<?php } ?>

		<div class="modal-footer">
			<input type="hidden" name="location_id" id="location_id" value="<?php echo $_SESSION['location_id']; ?>">
			<input type="hidden" name="courier_type" id="courier_type" value="1">
			<input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
			<?php if($row['id']==""){?>
				<input type="hidden" name="user_id" id="user_id" value="<?php echo $_SESSION['userid']; ?>">
			 <?php } ?>
			<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?php echo __('Close'); ?></button>
			<button type="button" class="btn btn-primary waves-effect waves-light " onclick="submitincourierfrom()"><?php echo __('Save'); ?></button>
		</div>
	</form>
</div>


<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
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
