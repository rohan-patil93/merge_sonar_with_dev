<?php 
if (isset($_POST['submit'])){
	//echo "<pre>"; print_r($_POST); exit;
	$tables = array_unique($_POST['table']);
	$post ['role']= $_POST['hid'];
	$post ['company_id']= $_SESSION['company_id'];
	$post ['submit']= $_POST['submit'];
	$post ['submit_value'] = $_POST['submit_value'];
	foreach ($tables as  $value) {
		$data[$value]['view'] = $_POST[$value."_view"] == 'on' ? 1: 0;
		$data[$value]['insert'] = $_POST[$value."_insert"]== 'on' ? 1: 0;
		$data[$value]['update'] = $_POST[$value."_update"]== 'on' ? 1: 0;
		$data[$value]['delete'] = $_POST[$value."_delete"]== 'on' ? 1: 0;
	}
	$post ['data'] = $data;
	$url = API."?mode=insertorupdatepermissions&method=post";
	$response = file_post_contents($url, $post, 'update');
	if ($response) {
		header("location:index.php?option=".$_GET['option']."&error=3010");
	}
}
$role_id = $_GET['Rid']!=''? $_GET['Rid'] : 1 ; 
$navlisturl = API."?mode=gettabledetails&fid=genc_code&rid=MENULOCATION&table=".TBL_GENRAL_CODE_DETAILS;
$navelistdata = callAPI($navlisturl); //file_get_contents($navlisturl, false, $context);	
$navelistdata = json_decode($navelistdata,1);

$data1["where"] = array('is_active'=> 0);
$data = urlencode(json_encode($data1));
$curl = API."?mode=gettabledetailswithconditions&table=".TBL_ROLES."&data=".$data; 
//$tdurl = API."?mode=gettabledetails&table=".TBL_ROLES;
$showtablesdata = callAPI($curl); // file_get_contents($tdurl, false, $context);
$show = json_decode($showtablesdata,1);
$roleurl = API."?mode=gettabledetails&fid=role_id&rid=$role_id&table=".TBL_ROLE_PERMISSIONS;
//echo $roleurl;
$roledata = callAPI($roleurl);//file_get_contents($roleurl, false, $context);	
$roledata = json_decode($roledata,1);
foreach ($roledata as $roles) {
	$role[$roles['table_name']]['view'] = $roles['permission_view']; 
	$role[$roles['table_name']]['insert'] = $roles['permission_insert']; 
	$role[$roles['table_name']]['update'] = $roles['permission_update']; 
	$role[$roles['table_name']]['delete'] = $roles['permission_delete']; 

}
//echo "<pre>";print_r($role); exit;
?>
<script type="text/javascript">
	function redirectpage(val){

			window.location.href = 'index.php?option='+val;
	}
	function GetSelectedTextValue(key) {
    	//alert(key);
        window.location.href = 'index.php?option=COM_ADMIN_MANAGEMENT&Rid='+key;
    }
</script>


<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
    	<div class="alert alert-warning alert-dismissible">
		            <strong><?php echo "This function is used to provide access"; ?></strong> 
		            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		        </div>

<div class="row">
<div class="col-lg-12">
	<form class="kt-form j-pro" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
	<div class="col-lg-6 mt-5">
		<label>Role </label>
		<select class="form-select js-example-basic-single" id="kt_select2_1" name="role" onchange="GetSelectedTextValue(this.value)">
			
		<?php foreach ($show as  $selectvalue) {  ?> 
				<option <?php if($selectvalue['id'] == $role_id){ ?> selected <?php } ?> value="<?php echo $selectvalue['id']; ?>"><?php echo $selectvalue['name']; ?></option>
			</optgroup>
		<?php  } ?>
		</select>
	</div>
	<ul class="nav md-tabs" role="tablist" style="margin-top: 20px ;">
			<?php 
				$i=1;
				foreach ($navelistdata as  $navdata) { ?>
				<li class="nav-item">
					<a class="nav-link <?php if ($i==1){ ?> active <?php } ?> " data-bs-toggle="tab" href="#kt_tabs_1_<?php echo $navdata['genc_detail_code']; ?>">
						<!-- <i class="la la-exclamation-triangle"></i> --> <?php echo $navdata['genc_detail_name']; ?>
					</a>
				</li>
			<?php $i++;  }	?>
			<li class="nav-item"> 
				<a class="nav-link" data-toggle="tab" onclick="redirectpage('COM_INSERT_ROLE_PERMISSIONS')" href=""> All </a>
			</li>

		</ul>
		<div class="tab-content">
			<?php 
				$j=1;
				foreach ($navelistdata as  $navdata) { ?>
			<div class="tab-pane <?php if ($j==1){ ?> active <?php } ?>" id="kt_tabs_1_<?php echo $navdata['genc_detail_code']; ?>" role="tabpanel">
				<?php $menunava = toGETNavDetails($navdata['genc_detail_code']);?>
				<div class="row" id="kt_sortable_portlets">
					<?php foreach ($menunava as  $menu) { 
							//if(count((array)$menu['child']) < 1 && $menu['page_name'] == ''){ continue; }
						?>

					<div class="col-lg-6 mt-5">
						<div class="card card-custom">
							<div class="card-header">
								<div class="kt-portlet__head-label">
									<h3 class="kt-portlet__head-title mt-5">
										<?php echo $menu['Label'];  //print_r($menu); ?>
									</h3>
								</div>
								
							</div>
							<?php if(count((array)$menu['child']) < 1){  ?>
								<?php $table_name = toGetTableNameUsingPageName($menu['page_name']); $value['display_list'] =1; ?>
								<div class="card-body">
									<div class="form-group row">
										<div class="col-lg-3">
											<label>Table Name</label>
										</div>
										<div class="col-lg-2">
											<label class="">  View</label>
										</div>
										<div class="col-lg-2">
											<label class="">   Insert</label>
										</div>
										<div class="col-lg-2">
											<label class=""> Update </label>
										</div>
										<div class="col-lg-2">
											<label class="">   Delete</label>
										</div>
									

												<div class="col-lg-3 p-1">
													<?php if($table_name != ''){ ?>
													<input type="hidden" class="form-control"  name="table[]" value="<?php echo $table_name;?>" >
												<?php } ?>
													<input type="text" class="form-control" readonly name="" value="<?php echo $menu['Label'];?>" >
												</div>
												<div class="col-lg-2 p-1">
													<span class="switch switch-outline switch-icon switch-primary">
														<label>
															<input type="checkbox" class="js-switch" name="<?php echo $table_name."_view"; ?>"  <?php if($role[$table_name]['view'] == 1){ ?> checked <?php } ?> >
															<span></span>
														</label>
													</span>
												</div>
												<div class="col-lg-2 p-1">
													<span class="switch switch-outline switch-icon switch-primary">
														<label>
															<input type="checkbox" class="js-switch" name="<?php echo $table_name."_insert"; ?>"  <?php if($role[$table_name]['insert'] == 1){ ?> checked <?php } ?> >
															<span></span>
														</label>
													</span>
												</div>
												<div class="col-lg-2 p-1">
													<span class="switch switch-outline switch-icon switch-primary">
														<label>
															<input type="checkbox" class="js-switch" name="<?php echo $table_name."_update"; ?>"  <?php if($role[$table_name]['update'] == 1){ ?> checked <?php } ?> >
															<span></span>
														</label>
													</span>
												</div>
												<div class="col-lg-2 p-1">
													<span class="switch switch-outline switch-icon switch-primary">
														<label>
															<input type="checkbox" class="js-switch" name="<?php echo $table_name."_delete"; ?>"  <?php if($role[$table_name]['delete'] == 1){ ?> checked <?php } ?> >
															<span></span>
														</label>
													</span>
												</div>
									</div>
								
								</div>
							<?php }else{ ?>
							<div class="card-body">
								<div class="kt-portlet__body">
									<ul class="nav nav-tabs" role="tablist">
										<?php $j=1; foreach ($menu['child'] as $key=>$child) { ?>
										<li class="nav-item">
											<a class="nav-link <?php if ($j==1){ ?> active <?php } ?> " data-bs-toggle="tab" href="#kt_tabs_2_<?php echo $key; ?>">
												<?php echo $child['Label']; ?></a>
										</li>
										<?php $j++; } ?>
									</ul>

									<div class="mt-5 tab-content">
										<?php $j=1; foreach ($menu['child'] as $key=>$child) { ?>
										<div class="tab-pane <?php if ($j==1){ ?> active <?php } ?> " id="kt_tabs_2_<?php echo $key; ?>" role="tabpanel">
											<div class="form-group row">
												<div class="col-lg-3">
													<label>Table Name</label>
												</div>
												<div class="col-lg-2">
													<label class="">  View</label>
												</div>
												<div class="col-lg-2">
													<label class="">   Insert</label>
												</div>
												<div class="col-lg-2">
													<label class=""> Update </label>
												</div>
												<div class="col-lg-2">
													<label class="">   Delete</label>
												</div>
												<?php 
												if(count((array)$child['child']) < 1){  $child['child'] = $arrayName = array($child);  }
												foreach ($child['child'] as  $value) { ?>
												<?php $table_name = toGetTableNameUsingPageName($value['Page_Name']); $value['display_list']=1; ?>
												<div class="col-lg-3 p-1">
													<?php if($table_name != ''){ ?>
													<input type="hidden" class="form-control"  name="table[]" value="<?php echo $table_name;?>" >
												<?php } ?>
													<input type="text" class="form-control" readonly name="" value="<?php echo $value['Label'];?>" >
												</div>
												<div class="col-lg-2 p-1">
													<span class="switch switch-outline switch-icon switch-primary">
														<label>
															<input type="checkbox" class="js-switch" name="<?php echo $table_name."_view"; ?>"  <?php if($role[$table_name]['view'] == 1){ ?> checked <?php } ?> > 
															<span></span>
														</label>
													</span>
												</div>
												<div class="col-lg-2 p-1">
													<span class="switch switch-outline switch-icon switch-primary">
														<label>
															<input type="checkbox" class="js-switch"  name="<?php echo $table_name."_insert"; ?>"  <?php if($role[$table_name]['insert'] == 1){ ?> checked <?php } ?> >
															<span></span>
														</label>
													</span>
												</div>
												<div class="col-lg-2 p-1">
													<span class="switch switch-outline switch-icon switch-primary">
														<label>
															<input type="checkbox" class="js-switch"  name="<?php echo $table_name."_update"; ?>"  <?php if($role[$table_name]['update'] == 1){ ?> checked <?php } ?> >
															<span></span>
														</label>
													</span>
												</div>
												<div class="col-lg-2 p-1">
													<span class="switch switch-outline switch-icon switch-primary">
														<label>
															<input type="checkbox" class="js-switch" name="<?php echo $table_name."_delete"; ?>"  <?php if($role[$table_name]['delete'] == 1){ ?> checked <?php } ?> >
															<span></span>
														</label>
													</span>
												</div>
												<?php } ?>
											</div>
										</div>
										<?php $j++; } ?>
									</div>
								</div>
							</div>
						<?php } ?>
						</div>
					</div>
					
				    <?php  }	?>
				</div>
			</div>
			<?php $j++;  }	?>
		</div>
		
	</div>

</div>
<div class="kt-portlet__foot">
	<div class="kt-form__actions">
		<div class="row">
			<div class="col-lg-6 mt-5 mb-5">

				<?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
						<input type="hidden" name="hid" value="<?php echo $role_id; ?>" />
						<input type="hidden" name="submit" value="Update" />
						<button id="kt_llogin_signup_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" ><i class="la la-check"></i>Save</button>
						<button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
				<?php } else{ ?>
						<input type="hidden" name="submit" value="Add" />
						<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
						<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
						<button type="reset" class="btn btn-light-primary font-weight-bolder mr-2 ><i class="la la-close"></i>Cancel</button>
				<?php } ?>	
			</div>
			<div class="col-lg-6 kt-align-right">
				<button type="reset" disabled="disabled" class="btn btn-danger float-right mt-5"><i class="la la-trash"></i>Delete</button>
			</div> 
		</div>
	</div>
</div>
</div>
</form>
</div>

</div>
</div>