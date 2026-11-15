<?php
$_SESSION['apply_js']['multiselest'] =1;
if(isset($_POST['submit_value'])){
	foreach ($_POST['filed'] as $key=>$filed) {
		if($_POST['select'][$key] != '0'){
			$infiels[$filed] = $_POST['select'][$key];
		}
	}
	$datacv['application_table'] = $_POST['table'];
	$datacv['filed_mapping'] = json_encode($infiels);
	$datacv['is_active'] =0;
	$datacv['current_status']	= 0;
	$datacv['created_date'] = nowDate();
	$datacv['created_by'] = $_SESSION['userid'];
	$datacv['company_id'] = $_SESSION['company_id'];
	//print_r($datacv); //exit;
	$url = API."?mode=adddata&table=api_masters&method=post";
	$response = file_post_contents($url, $datacv, 'insert');
	if ($response) {
		header("location:index.php?option=COM_LIST_API_MASTER_MAPPINGS");
	}
	//echo $response ; exit;
}
$rid = 10;
$vurl = API."?mode=togetapiidbasemapdetail&api_master_id=".$rid;
$configesponse = callAPI($vurl);
$result = json_decode($configesponse,1);
echo "<pre>"; print_r($result); echo "</pre>";
$all_api_masters = $result['all_api_masters'];
$amt = $result['api_masters'];
$edidumpsdata = json_decode($amt['api_response_details'],1);

// $vurl = API."?mode=togetapiidbasemapdetail&api_master_id=".$rid;
// $configesponse = callAPI($vurl);
// $data = json_decode($configesponse,1);
// echo "<pre>"; print_r($data); echo "</pre>";
// $amt = $data['master'];
// $edidumpsdata = json_decode($amt['api_response_details'],1);

//$edidumpsdata = $data['table_filed'];
//unset($selectdata['id']);
$selectdata = $data['table_filed'];
//print_r($selectdata); //exit;
?>
<form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
	<div class="card card-custom">
		<div class="card-header table-card-header">
			<h5><?php echo "Api Filed Mapping";  ?></h5>
			<div class="card-header-right table-card-header ">
				<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
					<i class="icofont icofont-reply" aria-hidden="true"></i>
					Back
				</a>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-3">
				<label class="form-label" for="transaction_table"><?= __('Transaction') ?></label>
				<select class="form-select js-example-basic-single" name="transaction_table" id="transaction_table" onchange="gettTransactionIDRecords()" aria-label="Transaction">
             		<option value=""><?= __('Select Any One') ?></option>
                	<?php foreach($all_api_masters as $dmt) {  ?>
                   <option value="<?php echo $dmt['id']; ?>"><?php echo $dmt['api_service_provider']." (".$dmt['api_name'].")";?></option>
                	<?php } ?>
              </select>
			</div>
			<div class="col-lg-2">
				<span class="form-label d-block" aria-hidden="true">&nbsp;</span>
				<button class="btn btn-primary form-control" onclick="filterSubmitForm()" type="submit" >
					<i class="ti-search m-r-2" aria-hidden="true"></i><?= __('View') ?>
				</button>
			</div>
		</div>
		<?php
		$idx = 0;
		if (is_array($edidumpsdata) || is_object($edidumpsdata)) {
		foreach ($edidumpsdata as $filed=>$edidumps) {
		$edidump = $filed;
		if($edidump!=""){
			$idx++;
			?>
			<div class="form-group row col-lg-12 mt-4">
				<div class="col-4">
					<label for="api_field_<?php echo $idx; ?>" class="visually-hidden"><?= __('API Field') ?> <?php echo htmlspecialchars($edidump); ?></label>
					<input class="form-control form-control-solid" type="text" id="api_field_<?php echo $idx; ?>" value="<?php echo htmlspecialchars($edidump); ?>" readonly aria-label="<?= __('API Field') ?> <?php echo htmlspecialchars($edidump); ?>" />
					<input class="form-control form-control-solid" type="hidden" name="filed[]" value="<?php echo htmlspecialchars($filed); ?>" />
				</div>
				<div class="col-4">
					<select class="form-select js-example-basic-single" name="select[]" id="select_field_<?php echo $idx; ?>" aria-label="Mapping for <?php echo htmlspecialchars($edidump); ?>">
						<option value="0">Select Any one</option>
						<?php foreach ($selectdata as  $fstt=>$select_list) {
							$select = strtolower(str_replace("_"," ",$select_list['Field']));
							if($select != 'id'){
								?>
								<option  <?php if(strtolower($edidump) ==strtolower($select)){ ?> selected <?php } ?> value="<?php echo $select;  ?>"><?php echo htmlspecialchars(__(viewTitle($select)));  ?></option>

							<?php } }  ?>
						</select>
					</div>
				</div>
			<?php } } } ?>
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					<input type="hidden" name="submit_map" value="Add">
					<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="exit"><i class="la la-check"></i><?= __('Save & exit') ?></button>
					<button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="continue"><i class="la la-check-circle"></i><?= __('Save & continue') ?></button>
					<button type="reset" class="btn btn-light-primary font-weight-bolder mr-2"><i class="la la-close"></i><?= __('Cancel') ?></button>
				</div>
				<div class="col-lg-3">		</div>
				<div class="col-lg-3 text-right">
					<button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i><?= __('Delete') ?></button>
				</div>
			</div>
		</div>
		<input type="hidden" name="table" value="<?php echo $amt['application_table']; ?>">
	</div>
</form>
