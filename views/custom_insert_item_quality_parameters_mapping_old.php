<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['jobposting'] = '1';
$table_name = "item_quality_parameters_mapping";
if (isset($_POST['submit'])){
	//echo "<pre>"; print_r($_POST); echo "</pre>";
	$data['company_id'] = $_SESSION['company_id'];
	$data['item_id'] = $_POST['item_id'];
	//$data['quality_type'] = $_POST['quality_type'];
	$jsqpm['group'] = array_keys($_POST['cat']);
	foreach($_POST['desc'] as $jskey=>$jspid){
		$jsp[$jskey]['id']= $jskey; 
		$jsp[$jskey]['mand']= $_POST['mand'][$jskey];
		$jsp[$jskey]['res']= $_POST['descpass'][$jskey];
	}
	$jsqpm['parameters'] = $jsp;
	$data['specifications'] = json_encode($jsqpm,1);
	if($_POST['submit'] == 'Add'){
		$data['current_status'] =  '0';
		$data['is_active'] =  '0';
		$data['created_date'] = nowDate();
		$data['created_by'] = $_SESSION['userid'];
		//echo "<pre>"; print_r($data); echo "</pre>";
		$url = API."?mode=adddata&table=".$table_name."&method=post";
		$response = file_post_contents($url, $data, 'insert');
	}
	if($_POST['submit'] == 'Update'){	
		$_SESSION['check'] = 1;
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=".$table_name."&method=post";
		$response = file_post_contents($url, $data, 'update');
	}
	$response = json_decode($response,1);
	 //echo "<pre>"; print_r($response); echo "</pre>";
	if($response['status'] == 'fail'){
		if(isset($_SESSION['error'][$response['error_no']])){
			$dis_err = explode(",", $response['other']);
			//print_r($dis_labels); exit;
			foreach ($dis_err as $va) {
				$dis_er [] = $dis_labels[$va];
			}
			$error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
		}else{
			$error_message = $response['message'];
		}
	}else{
		header("location:index.php?option=COM_LIST_ITEM_QUALITY_PARAMETERS_MAPPING");
	}
}
$dispay = array('GRN'=>'Goods Receipt Notes');
$vurl = API."?mode=togetqualitychecmappingindetails"; 
$vdata_details = callAPI($vurl);
$detail = json_decode($vdata_details,1);
$qcparameters = $detail['parameter'];

if((isset($_GET['Rid']) && $_GET['Rid'] !='') || ($_GET['ITEMID']!="")){ 
	$vdata_details = array();
  if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
		$rid =  encodeId($_GET['Rid']);
		$rid = urldecode($rid);
		$url = API."?mode=togetdatabasepaimairy&rid=".$rid."&table=".$table_name;
		$response = callAPI($url);
	$role = json_decode($response,1);
	}
	if($_GET['ITEMID']!=""){
		$vdata1["where"] = array('item_id'=> $_GET['ITEMID'],'is_active'=>'0');
		$vdata = urlencode(json_encode($vdata1));
		$url = API."?mode=gettabledetailswithconditions&table=".$table_name."&data=".$vdata; 
		$response = callAPI($url);
		$roles = json_decode($response,1);
		$role = $roles['0'];
		if(count((array)$role) == 0){
			$role['item_id'] = $_GET['ITEMID'];

		}
	}
	
	if($rid!=""){	$rid = "id:".$role['id']; }
	$jsdata = json_decode($role['specifications'],1);
	// print_r($role);
	$role_cat = $jsdata['group'];
	foreach($jsdata['parameters'] as $jdata){
		$role_des[] = $jdata['id'];
	}
}
?>
<script type="text/javascript">
	function jobcate(catid){
		if($("#cat_"+catid ).prop('checked')) {
			$( "#cate_"+catid ).show("slow");
		}else{
			$( "#cate_"+catid ).hide();
		}

	}
</script>
<style type="text/css">
/* TOGGLE STYLING */
.toggle {
  margin: 0 0 0.2rem;
  box-sizing: border-box;
  font-size: 0;
  display: flex;
  flex-flow: row nowrap;
  justify-content: flex-start;
  align-items: stretch;
}
.toggle input {
  width: 0;
  height: 0;
  position: absolute;
  left: -9999px;
}
.toggle input + label {
  margin: 0;
  padding: 0.25rem 1rem;
  box-sizing: border-box;
  position: relative;
  display: inline-block;
  border: solid 1px #DDD;
  background-color: #FFF;
  font-size: 1rem;
  line-height: 140%;
  font-weight: 600;
  text-align: center;
  box-shadow: 0 0 0 rgba(255, 255, 255, 0);
  transition: border-color 0.15s ease-out, color 0.25s ease-out, background-color 0.15s ease-out, box-shadow 0.15s ease-out;
  /* ADD THESE PROPERTIES TO SWITCH FROM AUTO WIDTH TO FULL WIDTH */
  /*flex: 0 0 50%; display: flex; justify-content: center; align-items: center;*/
  /* ----- */
}
.toggle input + label:first-of-type {
  border-radius: 6px 0 0 6px;
  border-right: none;
}
.toggle input + label:last-of-type {
  border-radius: 0 6px 6px 0;
  border-left: none;
}
.toggle input:hover + label {
  border-color: #213140;
}
.toggle input:checked + label {
  background-color: #4B9DEA;
  color: #FFF;
  box-shadow: 0 0 10px rgba(102, 179, 251, 0.5);
  border-color: #4B9DEA;
  z-index: 1;
}
.toggle input:focus + label {
  outline: dotted 1px #CCC;
  outline-offset: 0.45rem;
}
@media (max-width: 800px) {
  .toggle input + label {
    padding: 0.75rem 0.25rem;
    flex: 0 0 50%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
}
</style>
<script type="text/javascript">
	function chagethealldaata(itemid){
			window.location = "index.php?option=COM_INSERT_ITEM_QUALITY_PARAMETERS_MAPPING&ITEMID="+itemid;
	}
</script>

<div class="card card-custom card-sticky" id="kt_page_sticky_card">
	<?php if($error_message !=''){ ?>
		<div class="alert alert-danger background-danger alert-dismissible">
			<strong><?php echo $error_message; ?></strong>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>	
	<?php } ?>
	<div class="card-header">
		<h5>Quality Parameters Mapping</h5>
		<div class="card-header-right">
			<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
				<i class="icofont icofont-reply"></i>
				Back
			</a>
		</div>
	</div>
	<div class="card-custom">
		<div class="card-body">
			<form class="" method="post" action="" >
				<div class="row p-4">
					<div class="form-group col-lg-6">
						<label>Item</label>		
						<div class="w-full">
							<select class="form-select js-example-basic-single" onchange="chagethealldaata(this.value)"  name="item_id" >
								<option>Select Any</option>
								<?php foreach($detail['items'] as $item){ ?>
									<option <?php if($role['item_id']==$item['id']){ echo "selected"; } ?> value="<?php echo $item['id']; ?>"><?php echo $item['item_name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<!-- <div class=" form-group col-lg-6">
						<label>Display Type</label>		
						<div class="w-full">
							<select class="form-select js-example-basic-single" onchange="dschangepart(this.value)" name="quality_type" >
								<option>Select Any</option>
								<?php foreach($dispay as $diskey=>$dis){ ?>
									<option <?php if($role['quality_type']==$diskey){ echo "selected"; } ?> value="<?php echo $diskey; ?>"><?php echo $dis; ?></option>
								<?php } ?>
							</select>
						</div>
					</div> -->
					<div class="col-lg-12 row">
						<div class="col-lg-7 f-w-600"> Quality Parameters</div>
						<div class="col-lg-3 f-w-600">
							Reference Interval with Quality 
						</div>
						
						<div class="col-lg-2 f-w-600">
							Mandatory
						</div>
					</div>
					<?php foreach ($detail['groups'] as $group){
						if(count((array)$qcparameters[$group['id']])>0){
							?>
							<div class="col-lg-12 row">
								<div class="col-lg-7 mt-3">
									<div class="checkbox-fade fade-in-primary">	
										<label class="form-label line-height24">
											<input type="checkbox" <?php if(in_array($group['id'],(array)$role_cat)){ echo "checked"; } ?>  name="cat[<?php echo $group['id']; ?>]" id="cat_<?php echo $group['id']; ?>" onclick="jobcate(<?php echo $group['id']; ?>)"  />
											<span class="cr float-left">
												<i class="cr-icon icofont icofont-ui-check txt-primary"></i>
											</span>
											<?php echo $group['group_name'];  ?>
										</label>
									</div> 
								</div>
								<div class="col-lg-5"></div>
							</div>
							<div id="cate_<?php echo $group['id']; ?>" <?php if(!in_array($group['id'],(array)$role_cat)){ ?> style="display: none;" <?php } ?>>
								<?php foreach ($qcparameters[$group['id']] as $par) { ?>
									<div class="col-lg-12 row ml-10 mt-2" >
										<div class="col-lg-7" >
											<div class="checkbox-fade fade-in-primary mt-2 p-l-20">
												<label class="form-label text-auto-hight line-height24">
													<input type="checkbox" <?php if(in_array($par['id'],(array)$role_des)){ echo "checked"; } ?>  name="desc[<?php echo $par['id']; ?>]" id="par_<?php echo $par['id']; ?>" />

													<span class="cr float-left">
														<i class="cr-icon icofont icofont-ui-check txt-primary"></i>
													</span>
													<?php echo $par['name'];  ?>
												</label>
											</div>
										</div>
										<div class="col-lg-3">
											<?php //print_r($par); ?>
<?php 
if($par['display_type']==1 || $par['display_type']==2){ 
		$selectdata = json_decode($par['quality_parameters'],1);
		$check = $jsdata['parameters'][$par['id']]['res']!=""? $jsdata['parameters'][$par['id']]['res'] :0;
?>
<div class="toggle" >
	<?php $mn=1;  foreach($selectdata as $selectkey => $selectvalue){
		$id = "check_".$par['id']."_".$mn;
	?> 
	<input  type="radio" <?php if($check==$selectkey){ echo "checked"; } ?> name="descpass[<?php echo $par['id']; ?>]" value='<?php echo $selectkey; ?>' id='<?php echo $id; ?>' />
	<label for="<?php echo $id; ?>"><?php echo $selectvalue; ?></label>
	<?php  $mn++;   } ?>
</div>
<?php  } ?>
<?php if($par['display_type']==3 || $par['display_type']==4){
	$selectdata = json_decode($par['quality_parameters'],1);
	$check = $jsdata['parameters'][$par['id']]['res']!=""? $jsdata['parameters'][$par['id']]['res'] :3;
?>
<div class="toggle " >
	<?php $mn=1; for($i=1;$i<=5;$i++){ 	
		$id = "check_".$par['id']."_".$mn;

	?> 
	<input  type="radio" <?php if($check==$i){ echo "checked"; } ?> name="descpass[<?php echo $par['id']; ?>]" value='<?php echo $i; ?>' id='<?php echo $id; ?>'  />
	<label for="<?php echo $id; ?>"  data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $selectdata[$i-1]; ?>" ><?php echo $i; ?></label>
	<?php  $mn++;  } ?>
</div>
<?php  } ?>
<?php if($par['display_type']==5){ 
	$selectdata = array('Yes','No');
	 $check = $jsdata['parameters'][$par['id']]['res']!=""? $jsdata['parameters'][$par['id']]['res'] :0;
?>
<div class="toggle" >
	<?php $mn=1;  foreach($selectdata as $selectkey => $selectvalue){
		$id = "check_".$par['id']."_".$mn;

	?> 
	<input  type="radio" <?php if($check ==$selectkey){ echo "checked"; } ?> name="descpass[<?php echo $par['id']; ?>]" value='<?php echo $selectkey; ?>' id='<?php echo $id; ?>' />
	<label for="<?php echo $id; ?>"><?php echo $selectvalue; ?></label>
	<?php  $mn++;   } ?>

</div>
<div class="f-12 text-muted"> With in range ?</div>
<?php  } ?>
										</div>
										
										<div class="col-lg-2">
											<div class="toggle" >
												<?php $selectdata = array('Yes','No'); $mn=1;
												 foreach($selectdata as $selectkey => $selectvalue){
													$id = "mand_".$par['id']."_".$mn;
													echo $mcheck = $jsdata['parameters'][$par['id']]['mand']!=""? $jsdata['parameters'][$par['id']]['mand'] :1;
													?> 
													<input  type="radio" <?php if($mcheck==$selectkey){ echo "checked"; } ?> name="mand[<?php echo $par['id']; ?>]" value='<?php echo $selectkey; ?>' id='<?php echo $id; ?>' />
													<label for="<?php echo $id; ?>"><?php echo $selectvalue; ?></label>
												<?php $mn++; } ?>

											</div>
										</div>
									</div>
								<?php } ?>
							</div>
						<?php } } ?>
					</div>
					<div class="card-footer mt-5 p-4">
						<div class="row">
							<?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
								<div class="col-lg-9 col-md-6 col-sm-6 col-6">
									<input type="hidden" name="hid" value=<?php echo $rid; ?> />
									<input type="hidden" name="submit" value="Update" />
									<button id="kt_llogin_signup_submit" <?php if($status_res == '1'){ ?> disabled="disabled" <?php } ?> class="btn btn-primary mr-2" ><i class="la la-check"></i>Upadate</button>

								<?php } else{ ?>
									<div class="col-lg-9">
										<input type="hidden" name="submit" value="Add" />
										<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10"   name="submit_value_exit" value="exit"><i class="la la-check"></i>Save & exit</button>

										<button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close"></i>Cancel</button>
									<?php } ?>	
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-6 text-right">
									<button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
								</div> 
							</div>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
	