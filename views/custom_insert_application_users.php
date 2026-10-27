<?php 
$_SESSION['apply_js']['multiselest'] = 1;
$File_Uploads_Path = "uploads/all/";
if(isset($_POST['submit'])){
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$_POST['data_name']=$_SESSION['DB_NAME'];
	
	if($_FILES['image']['error'] != "4"){
		$temp = explode(".", $_FILES['image']['name']);
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		if(move_uploaded_file($_FILES['image']["tmp_name"], $File_Uploads_Path.$newfilename)){
			$_POST['image'] = $newfilename;
		}
	}else{
		$_POST['image'] = $_POST['hidimage'];
	}

if($_FILES['image1']['error'] != "4"){
		$temp = explode(".", $_FILES['image1']['name']);
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		if(move_uploaded_file($_FILES['image1']["tmp_name"], $File_Uploads_Path.$newfilename)){
			$_POST['display_logo'] = $newfilename;
		}
	}else{
		$_POST['display_logo'] = $_POST['hidimage1'];
	}
	$url = API."?mode=insertapplicationuser&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response = json_decode($response,1);
	$response = $response['application_users'];
	if($response['status'] == 'fail'){
		if(isset($_SESSION['error'][$response['error_no']])){
			$dis_err = explode(",", $response['other']);
			foreach ($dis_err as $va) {
				$dis_er [] = $dis_labels[$va];
			}
			$error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
		}else{
			$error_message = $response['message'];
		}
	}else{
		unset($_SESSION['login_details']);
		if($_POST['tab'] !='Y'){
			$_SESSION['user_activated'] = 'Y';
			header("location:index.php");
		}else{
			header("location:index.php?option=COM_LIST_APPLICATION_USER_INVITATIONS");
		}
		?>

<script type="text/javascript">
document.onreadystatechange = function(){
  if (document.readyState == "complete") {
    var mesg = "Verified User Successfully";
    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
  } 
}
</script>
<?php	}
}
if(isset($_POST['skip_compliance'])){
	$_SESSION['user_activated'] = 'Y';
	unset($_SESSION['login_details']);
	header("location:index.php");
	//header("location:index.php?option=COM_LIST_APPLICATION_USER_INVITATIONS");
}

//echo "fvdfvgf".$_SESSION['userid'];


// if(isset($_GET['Rid'])){
// 	$rid =  encodeId($_GET['Rid']);
// 	$rid = urldecode($rid);
// 	$rid = explode(':', $rid);
// 	$rid = $rid['1'];
// }else{
	$rid = $_SESSION['userid'];	
//}
$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$rid);
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data1; 
$data_details1 = callAPI($url1);
$application_users = json_decode($data_details1,1);
$application_users = $application_users[0];



$wh2["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);  
$wh2["field"] = "id,module_id,module_name";
$data2 = urlencode(json_encode($wh2));
$url2 = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$data2; 
$moduleres = callAPI($url2);
$moduleres = json_decode($moduleres,1);
foreach($moduleres as $moduleres){
  $module[$moduleres['module_id']] = $moduleres['module_name'];
}
$wh2["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);  
$wh2["field"] = "id,name,application_module,role_short_code";
$data2 = urlencode(json_encode($wh2));
$url2 = API."?mode=gettabledetailswithconditions&table=application_roles&data=".$data2; 
$rolesres = callAPI($url2);
$rolesres = json_decode($rolesres,1);
$role_idd  =array(SUPERADMIN_ROLE);
//print_r($role_idd);
foreach($rolesres as $rolesre){
    if(!in_array($rolesre['role_short_code'], (array)$role_idd)){
        $roles[$rolesre['application_module']][] = $rolesre;
    }
}



$wher1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=> '0');
$wher1["field"] = "id,location_name";
$encodeurl = urlencode(json_encode($wher1));
$urlcat = API."?mode=gettabledetailswithconditions&table=locations&data=".$encodeurl; 
$locationsress = callAPI($urlcat);
$locations   = json_decode($locationsress,1);


$countrieswher1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=> '0');
$countrieswher1["field"] = "id,country_name";
$countriesencodeurl = urlencode(json_encode($countrieswher1));
$countriesurlcat = API."?mode=gettabledetailswithconditions&table=countries&data=".$countriesencodeurl; 
$countriesres = callAPI($countriesurlcat);
$countriesres   = json_decode($countriesres,1);
?>
<div class="card">
	<?php if($error_message !=''){ ?>
		<div class="alert alert-danger background-danger alert-dismissible">
			<strong><?php echo $error_message; ?></strong>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>	
	<?php } ?>
	<div class="card-header table-card-header">
		<h5>Application User</h5>
		<?php if($tab !="Y"){ ?>
		<div class="card-header-right table-card-header ">
			<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
				<i class="icofont icofont-reply"></i>
				Back
			</a>
		</div>
		<?php } ?>
	</div>
		<div class="card-body p-l-50 l-r-50">
			<form class="kt-form" id="kt_form" method="post" action="" name="myForm" enctype="multipart/form-data">
				<div class="row">
					<div class="col-lg-10">
						<div class="row">
							<div class="form-group col-lg-4">
								<label class="">First Name&nbsp;<span class="requried">*</span></label>
								<input class="form-control" type="text" id="first_name" name="first_name" value="<?php echo $application_users['first_name']; ?>" required >
							</div>
							<div class="form-group col-lg-4">
								<label class="">Last Name</label>
								<input class="form-control" type="text" id="last_name" name="last_name" value="<?php echo $application_users['last_name']; ?>">
							</div>
							<div class="form-group col-lg-4">
								<label class="">Display Name</label>
								<input class="form-control" type="text" id="display_name" name="display_name" value="<?php echo $application_users['display_name']; ?>" required >
							</div>
							<div class="form-group col-lg-4">
								<label class="">User Email</label>
								<input class="form-control" type="text" id="user_email" name="user_email" value="<?php echo $application_users['user_email']; ?>"  <?php echo $rid!=""?"readonly":""; ?> required >
								<div style="display: none;color: red;" id="user_email_err"> Please enter User Email </div> 
							</div>
							<div class="form-group col-lg-4">
								<label class="">User Mobile</label>
								<input class="form-control" type="text" id="user_mobile" name="user_mobile" value="<?php echo $application_users['user_mobile']; ?>">
								<div style="display: none;color: red;" id="user_mobile_err"> Please enter User Mobile </div> 
							</div>
							<?php $disablerole= ""; 
							if($application_users['id'] == $_SESSION['userid']){  
								$disablerole ="pointer-events: none; opacity: 0.6;"; 
							} 
							/**$role_idd = json_decode($application_users['role_id']);
							?>
							<div class="form-group col-lg-4" style="<?php echo $disablerole; ?>">
								<label>User Role</label>
								<select class="form-select js-example-basic-multiple" multiple="multiple" name="role_id[]">
									<?php foreach($application_roles as $application_roled){ ?>
										<option value="<?php echo $application_roled['role_short_code']; ?>" <?php echo in_array($application_roled['role_short_code'],(array)$role_idd)?"selected":""; ?>><?php echo $application_roled['name']; ?></option>
									<?php  } ?>

									<?php foreach($roles as $key=>$rolesd){  ?>
			                              <optgroup label="<?php echo $module[$key]; ?>">
			                                <?php foreach($rolesd as $rolesr){  ?>
			                                <option value="<?php echo $rolesr['id']; ?>" <?php echo in_array($rolesr['role_short_code'],(array)$role_idd)?"selected":""; ?>><?php echo $rolesr['name']; ?></option>
			                                <?php } ?>
			                              </optgroup>
			                          <?php } ?>
								</select>
							</div>
							<?php **/ ?>
							<div class="form-group col-lg-4">
								<label>Country </label>
								<select class="form-select js-example-basic-single"  name="country_id">
									<option value="" > Select Any One Country</option>
									<?php foreach($countriesres as $countries){ ?>
										<option value="<?php echo  $countries['id']; ?>" <?php echo $application_users['country_id'] == $countries['id']?"selected":""; ?>> <?php echo  $countries['country_name']; ?> </option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group col-lg-4">
								<label>Location </label>
								<select class="form-select js-example-basic-single"  name="location_id">
									<option value="" > Select Any one Location</option>
									<?php foreach($locations as $location){ ?>
										<option value="<?php echo  $location['id']; ?>" <?php echo $application_users['location_id'] == $location['id']?"selected":""; ?>> <?php echo  $location['location_name']; ?> </option>
									<?php } ?>
								</select>
							</div>


<?php if(in_array(ADMIN_ROLE,(array)$_SESSION['role_ids']) AND $_SESSION['user_activated'] == 'N'){
	$dateformatlist = array('YYYY-MM-DD','YYYY/MM/DD','DD-MM-YYYY','MM-DD-YYYY','MM/DD/YYYY','DD/MM/YYYY');
 ?>

 				<div class="form-group col-lg-4">
					<label class="form-label">Config Name</label>
					<input class="form-control" type="text" id="config_name" name="config_name" value="<?php echo $_SESSION['company'][$application_users['company_id']]; ?>">
				</div>


			<div class="form-group col-lg-4">
				<label class="form-label">Date Format </label>
				<select class="form-select js-example-basic-single"  name="date_format">
			    <option value="">Select Any One </option>
			    <?php foreach($dateformatlist as $valuedates) { ?>
			      <option value="<?php echo $valuedates;?>"><?php echo $valuedates; ?></option>
			    <?php } ?>
			  </select> 
			</div>
			<div class="form-group col-lg-4">
				<label class="form-label">Logo </label>
				<div class="col-lg-9 col-xl-6">
							<div class="profile-pic">
								<label class="-label" for="image1">
									<span><i class="fa fa-camera"></i> &nbsp; Change Image</span>
								</label>
								<input type="file" name="image1" id="image1" accept=".png, .jpg, .jpeg"  onchange="loadFile(event,this.id)">
								
								<input type="hidden" name="hidimage" value="<?php echo $application_users['user_photo']; ?>">
								<img src="<?php echo $File_Uploads_Path.$application_users['display_logo'] ; ?>" id="outputimage1" width="200" onerror="this.src='uploads/all/no-image.jpg';" />
							</div>

							<div class="image-input image-input-outline" id="kt_image_61">
								<div class="image-input-wrapper" style="background-image: url('<?php echo $File_Uploads_Path.$application_roled['display_logo']; ?>')"></div>
								<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="opacity: 0;">
									<i class="fa fa-pen icon-sm text-muted"></i>
								</label>
								<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar"  style="opacity: 0;">
									<i class="ki ki-bold-close icon-xs text-muted"></i>
								</span>
							</div>
						</div>
				
			</div>
			

<?php } ?>



						</div>

					</div>
					<div class="form-group col-lg-2">
						<label>Image</label>
						<div class="col-lg-9 col-xl-6">
							<div class="profile-pic">
								<label class="-label" for="image">
									<span><i class="fa fa-camera"></i> &nbsp; Change Image</span>
								</label>
								<input type="file" name="image" id="image" accept=".png, .jpg, .jpeg"  onchange="loadFile(event,this.id)">
								<input type="hidden" name="hidimage1" value="<?php echo $application_users['user_photo']; ?>">
								<img src="<?php echo $File_Uploads_Path.$application_users['user_photo'] ; ?>" id="outputimage" width="200" onerror="this.src='uploads/all/no-image.jpg';" />
							</div>

							<div class="image-input image-input-outline" id="kt_image_61">
								<div class="image-input-wrapper" style="background-image: url('<?php echo $File_Uploads_Path.$application_roled['user_photo']; ?>')"></div>
								<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="opacity: 0;">
									<i class="fa fa-pen icon-sm text-muted"></i>
								</label>
								<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar"  style="opacity: 0;">
									<i class="ki ki-bold-close icon-xs text-muted"></i>
								</span>
							</div>
						</div>
					</div>
				</div>
				<?php //if($application_users['user_activated'] == 'N' || $_SESSION['user_activated'] == 'Y'){ ?>
				<div class="form-group mt-5">
					<input type="hidden" name="Rid" value="<?php echo $rid; ?>">
					<input type="hidden" name="tab" value="<?php echo $tab; ?>">
					
					<button type="submit" name="submit" class="btn btn-info btn-round waves-effect waves-lighttext-uppercase  mt-15 ml-3">Update</button>
				</div>
				<?php// } ?>
		</form>
		 <?php /**if($application_users['user_activated'] == 'Y' AND  $_SESSION['user_activated'] == 'N'){ ?>
                    <form method="post">
                        <button class="btn btn-info btn-round waves-effect waves-light" name="skip_compliance" type="submit"> Skip/Go Dashborad</button>   
                    </form>
                <?php } **/?>
                	</div>
	</div>


