<?php 
ini_set('display_errors' ,0);
include('includes/session.php');
include('dbconfig.php');
include('comm.php');
if(isset($_POST['submit'])){
	$File_Uploads_Path = "uploads/all/";
	if($_FILES["image"]["error"]=="0"){
		$temp = explode(".", $_FILES["image"]["name"]);
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		move_uploaded_file($_FILES["image"]["tmp_name"], $File_Uploads_Path.$newfilename); 
	}
	$_POST['display_logo'] = $newfilename;
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=insertsetupdetails&method=post";
  //$re = file_post_contents($url, $_POST, 'insert');
	$reponse = json_decode($re,1);
	$response = $reponse['insert_config'];
	if($response['status'] == 'sucess'){
		unset($_SESSION['login_details']);         
		?>
		<script type="text/javascript">
			document.onreadystatechange = function(){
				if(document.readyState == "complete") {
					swal({
						title: "Success",
						text: '<?php echo $_SESSION['error_message']['1102']; ?>',
						type: "success",
						showCancelButton: false,
						confirmButtonClass: "btn-danger",
						confirmButtonText: "Yes",
						cancelButtonText: "No",
						closeOnConfirm: true,
						closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							window.location.href = 'index.php';
						}
					});
					setTimeout(function() {window.location.href = 'index.php'; }, 2000);
				} 
			}
		</script>
		<?php
	}else{
        //($response['status'] == 'fail'){
		if(isset($_SESSION['error'][$response['error_no']])){
			$dis_err = explode(",", $response['other']);
			foreach ($dis_err as $va) {
				$dis_er [] = $dis_labels[$va];
			}
			$error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
		}else{
			$error_message = $response['message'];
		}
	}

  //echo "<pre>"; print_r($res); echo "</pre>";
    //header('Location:index.php?option=COM_VIEW_PROJECT_KANBAN&RID='.$res['project_id']);
}
$ptapi = API."?mode=getsetupdetails"; 
$callptapi = callAPI($ptapi);
$result = json_decode($callptapi,1);
//echo "<pre>"; print_r($result); echo "</pre>";
$countries = $result['countries'];
$company_types = $result['company_types'];
$companies = $result['companies'];
$config = $result['config'];
$industrylist = $result['industries'];
if($config['id'] !=""){
  // header("location:index.php");
}
$dateformatlist = array('YYYY-MM-DD','YYYY/MM/DD','DD-MM-YYYY','MM-DD-YYYY','MM/DD/YYYY','DD/MM/YYYY');
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Infistaq | Home</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="Unlock your business's full potential with affordable ERP software Infistaq. ERP that supports manufacturing, distribution, retail, trading, services, education, non profits and healthcare. Try our customizable ERP solutions today." />
	<meta name="keywords" content="ERP software, Enterprise Resource Planning, ERP system, Cloud ERP, On-premise ERP, ERP solutions, ERP implementation, ERP integration, Business process automation, Supply chain management, Inventory management, Financial management, Manufacturing ERP, Human resources management, Customer relationship management (CRM), Sales and marketing automation, ERP consulting, ERP customization, Enterprise software, ERP for small businesses, ERP for medium-sized businesses, ERP for large enterprises, Industry-specific ERP (e.g., ERP for manufacturing, retail, healthcare, etc.), ERP benefits, ERP features, ERP comparison, ERP reporting and analytics, ERP training, ERP support and maintenance, ERP scalability, ERP ROI (Return on Investment), Cloud-based ERP solutions, ERP for business growth, ERP best practices, ERP compliance, ERP case studies/testimonials" />
	<meta name="author" content="infisuite" />
	<!-- Favicon icon -->
	<link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
	<!-- Google font-->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
	<!-- Required Fremwork -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">

	<link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
	<link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
	<link rel="stylesheet" type="text/css" href="assets/css/font-awesome-n.min.css">
	<!-- Data Table Css -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="assets/pages/data-table/css/buttons.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="assets/pages/data-table/extensions/buttons/css/buttons.dataTables.min.css">
	<!-- Chartlist chart css -->
	<link rel="stylesheet" href="assets/bower_components/chartist/css/chartist.css" type="text/css" media="all">
	<!-- Bootstrap date-time-picker js -->	
	<link rel="stylesheet" type="text/css" href="assets/pages/advance-elements/css/bootstrap-datetimepicker.css">
	<link rel="stylesheet" type="text/css" href="assets/css/date-jquery-ui.css">
	<!-- Date-range picker css  -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-daterangepicker/css/daterangepicker.css" />
	<!-- Date-Dropper css -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/datedropper/css/datedropper.min.css" />
	<link rel="stylesheet" href="assets/bower_components/select2/css/select2.min.css" />
	<!-- Multi Select css -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-multiselect/css/bootstrap-multiselect.css" />
	<link rel="stylesheet" type="text/css" href="assets/bower_components/multiselect/css/multi-select.css" />
	<!-- Color Picker css -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/spectrum/css/spectrum.css" />
	<!-- Mini-color css -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-minicolors/css/jquery.minicolors.css" />
	<link rel="stylesheet" type="text/css" href="assets/pages/data-table/extensions/responsive/css/responsive.dataTables.css">
	<!-- Switch component css -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/switchery/css/switchery.min.css">
	<!-- Tags js -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />
	<!-- Style.css -->	
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/component.css">
	<link rel="stylesheet" type="text/css" href="assets/css/pages.css">	
	<link rel="stylesheet" type="text/css" href="assets/css/custom.css">
	<link rel="stylesheet" type="text/css" href="assets/css/custom_mobile.css">
	<link rel="stylesheet" href="assets/bower_components/chartist/css/chartist.css" type="text/css" media="all">
	<!----notification---->
	<link rel="stylesheet" type="text/css" href="assets/pages/notification/notification.css">
	<!-- sweet alert framework -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/sweetalert/css/sweetalert.css">
	<!-- sweet alert framework -->
	<link rel="stylesheet" type="text/css" href="assets/css/pages.css">
</head>
<style type="text/css">
	.tags_add .bootstrap-tagsinput{ width: 100%; }
	.heightcss{ max-height: 110px;min-height: 110px; } 
	.footer_btn{ 
		position: fixed;
		left: 0;
		bottom: 0px;
		padding: 15px;
		width: 100%;
		background-color: white;
		text-align: center; 
	}  
	body{background-color: transparent; background-image: none;}
</style>
<script type="text/javascript">
	function nextsetupview(page){
		for(var i=1; i<6; i++){
			$("#setup"+i).hide();
		}
		$("#setup"+page).show('slow');
	}
	function addMoreLocation(){
		var html = "";
		html = '<div class="row mt-3"><div class="col-lg-6"><input type="text" class="form-control" name="location_name[]"></div><div class="col-lg-6"><input type="text" class="form-control" name="short_name[]"></div></div>';
		$("#newaddmorelocation").append(html);
	}
	function selectCounty(countydetail){
		var countydetail = countydetail.split("||");
		console.log(countydetail);
		var c_id = countydetail[0];
		var timezone = JSON.parse(countydetail[1]);
		$.ajax({
			url: "ajax.php",
			data:'page=getState&c_id='+c_id,
			type: "POST",
			success: function(response) {
				console.log(response);
				$("#getstate").html(response);
			} 
		});
		var html ="";
		console.log(timezone);
		$.each(timezone, function( key, valu) {
			console.log(valu);
			html +="<option value='"+valu.zoneName+"'>"+valu.zoneName+"</option>";
		});
		$("#time_zone").html(html);
	}
	function selectSatate(c_id){
		$.ajax({
			url: "ajax.php",
			data:'page=getcity&c_id='+c_id,
			type: "POST",
			success: function(response) {
				console.log(response);
				$("#getcity").html(response);
			} 
		});
	}
</script>

<body themebg-pattern="">
	<form method="post" action="" enctype='multipart/form-data'>
		<?php if($error_message !=''){ ?>
			<div class="alert alert-danger background-danger alert-dismissible">
				<strong><?php echo $error_message; ?></strong>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>  
		<?php } ?>
    <!-- <div class="taskda-bg-top float-right">
      <div class="container-fluid text-right">
        <div class="top-logo p-2">
          <a href="">
            <img class="img-100" src="assets/images/sign-in/<?php //echo LOGIN_INFO['logo']; ?>">  
          </a>
        </div>
      </div>
  </div> -->
  <!-- Pre- setup1  Start-->
  <div id="setup1" style="">
  	<div class="row align-items-center">
  		<div class="col-lg-6 bg-white" style="">
  			<div class="center-sec" style="">
  				<div class="" style="vertical-align: middle;align-items: center;"> 
  					<div class="p-5">
  						<h2 class="f-w-600">Welcome to Infistaq</h2>
  						<p class="f-w-500 mt-4">
  							<span class="d-block"><span class="f-w-600 f-18 text-primary">Congratulations</span> on taking the first step towards streamlining your business operations and maximizing efficiency. With Infistaq, you're in for a game-changing experience.</span>

  							<span class="d-block mt-3">Get ready to unlock a world of possibilities where managing your enterprise becomes seamless and productive. Our intuitive interface, comprehensive features, and robust tools are designed to elevate your business to new heights.	</span>

  							<span class="d-block mt-3"> From finance to HR, inventory to analytics, Infistaq is your all-in-one solution, tailored to meet your specific needs. We're excited to embark on this journey with you and help your business thrive.</span>

  							<span class="d-block mt-3">Feel free to explore our platform, and don't hesitate to reach out if you have any questions. Welcome aboard, and here's to a successful partnership with Infistaq! </span></p>
  							<button class="btn btn-primary mt-3" type="button" onclick="nextsetupview(2)"><i class="ti-arrow-right"></i>Start To Setup</button>
  						</div>
  					</div>
  				</div>
  			</div>
  			<div class="col-lg-6 text-center position-relative">
  				<div class="top-logo p-2">
  					<a href="">
  						<img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">  
  					</a>
  				</div>
  				<div class="text-position text-start">
  						<span class="f-30 f-w-600">Welcome to</span>
  						<h2 class="f-50 f-w-600">Company<br />Information</h2>
  						<p class="f-14 f-w-600">Enter your company's details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.</p>
  						<button class="btn btn-primary">infistaq.com</button>
  				</div>
  				<img class="" src="assets/images/setup_welcome_compliance.png">
  			</div>
  		</div>
  	</div>
  	<!-- Pre- setup1  Start-->
  	<div id="setup2" style="display:none;" >
  		<div class="taskda-bg" style="">
  			<div class="row align-items-center">
  				<div class="col-lg-6 p-l-50 p-r-50 bg-white">
  					<div class="m-20">
  						<h3 class="f-w-600 mb-5">Company Details</h3>
  						<div class="row mb-5">
  							<div class="col-lg-6">
  								<div class="form-group">
  									<label class="form-label">Company Name</label>
  									<input type="text" class="form-control" name="name" value="<?php echo $companies['name']; ?>" readonly>
  								</div>
  								<div class="form-group w-full">
  									<label class="form-label">Type of Company</label>
  									<select name="company_type_id" class="form-control form-select js-example-basic-single">
  										<option value="">Select Any One </option>
  										<?php foreach($company_types as $ct){ ?>
  											<option value="<?php echo $ct['id']; ?>"><?php echo $ct['company_type_name']; ?></option>
  										<?php } ?>
  									</select>
  								</div>
  								<div class="form-group  w-full">
  									<label>Industry</label>
  									<select class="form-select js-example-basic-single"  name="industry">
  										<option value="">Select Any One </option>
  										<?php foreach($industrylist as $valueindustry) { ?>
  											<option value="<?php echo $valueindustry['id'];?>"><?php echo $valueindustry['industries']; ?></option>
  										<?php } ?>
  									</select> 
  								</div>
  							</div>
  							<div class="col-lg-6 text-center">
  								<div class=" form-group">
  									<label class="form-label">Company logo</label>
  									<div class="profile-pic mt-2">
  										<label class="-label" for="display_logo">
  											<span> <i class="fa fa-camera"></i> &nbsp; Upload Logo</span>
  										</label>
  										<input type="file" name="image" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
  										<img src="<?php echo "uploads/all/".$role['image'] ; ?>" id="outputdisplay_logo" width="200" onerror="this.src='uploads/all/no-image.jpg';"/>
  										<input type="hidden" name="hid_image" value="<?php echo $role['image'] ;  ?>">
  									</div>
  								</div>
  							</div>
<!--                   <div class="col-lg-6 form-group w-full">
                      <label class="form-label">Number of Employee</label>
                      <input type="number" class="form-control" name="no_of_users" value="0">
                  </div> 
                  <div class="col-lg-12 form-group">
                      <label class="form-label">Revenue</label>
                      <div class="row">
                        <div class="col-lg-6">
                           <div class="input-group input-group-button "> 
                          <input type="number" class="form-control width1" name="min_revenue" value="">
                           <span class="input-group-text addon-style">
                                <label class="">Lac</label>
                            </span>
                        </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="input-group input-group-button "> 
                              <input type="number" class="form-control width1" name="max_revenue" value="0">
                              <span class="input-group-text addon-style">
                                <label class="">Lac</label>
                            </span>
                        </div>
                        </div>
                        
                      </div>
                  </div> -->
              </div>
              <div class="footer_btn">
              	<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(1)"><i class="ti-arrow-left"></i> Previous Step</button>
              	<button type="button"  class="btn btn-primary" onclick="nextsetupview(3)"><i class="ti-arrow-right"></i>  Next Step</button>
              </div>
          </div>
      </div>
      <div class="col-lg-6 text-center position-relative">
      	<div class="top-logo p-2">
      		<a href="">
      			<img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">  
      		</a>
      	</div>
      	<div class="text-position text-start">
  						<span class="f-30 f-w-600">Welcome to</span>
  						<h2 class="f-50 f-w-600">Localization<br />Settings</h2>
  						<p class="f-14 f-w-600">Choose your preferred currency and locale settings. This ensures that all financial transactions and system displays are aligned with your regional preferences.</p>
  						<button class="btn btn-primary">infistaq.com</button>
  				</div>
      	<img class="" src="assets/images/setup_welcome_compliance.png">
      </div>
  </div>
</div>
</div>

<div id="setup3" style="display:none;" >
	<div class="taskda-bg">
		<div class="row align-items-center">
			<div class="col-lg-6 p-l-50 p-r-50 bg-white">
				<div class="m-20">
					<h3 class="f-w-600 mb-5">Contact Details</h3>
					<div class="row mb-5">
						<div class="col-lg-6 form-group">
							<label class="form-label">Contact Person Name</label>
							<input type="text" class="form-control" name="incharge_name">
						</div>
						<div class="col-lg-6 form-group">
							<label class="form-label">Contact Number</label>
							<input type="text" class="form-control" name="telephone">
						</div>
						<div class="col-lg-6 form-group">
							<label class="form-label">Email</label>
							<input type="text" class="form-control" name="email">
						</div>
						<div class="col-lg-6 form-group">
							<label class="form-label">Address</label>
							<textarea type="text" class="form-control" name="address1"></textarea>
						</div>
						<div class="col-lg-6 form-group w-full">
							<label class="form-label d-block">Country</label>
							<select name="country" class="form-control form-select js-example-basic-single" onchange="selectCounty(this.value)">
								<option value="">Select Any One</option>
								<?php foreach($countries as $countr) { ?>
									<option value='<?php echo $countr['id']."||".$countr['time_zones']; ?>' ><?php echo $countr['country_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-lg-6 form-group  w-full">
							<label class="form-label d-block">State</label>
							<select name="state" class="form-control form-select js-example-basic-single" id="getstate" onchange="selectSatate(this.value)">
								<option value="">Select Any One</option>
							</select>
						</div>
						<div class="col-lg-6 form-group  w-full">
							<label class="form-label d-block">City</label>
							<select name="city" class="form-control form-select js-example-basic-single" id="getcity">
								<option value="">Select Any One</option>
							</select>
						</div>
						<div class="col-lg-6 form-group">
							<label class="form-label">Post Code</label>
							<input type="number" class="form-control" name="postcode">
						</div>
                  <?php /** ?>
                  <div class="col-lg-6 form-group w-full">
                      <label class="form-label">Time Zone</label>
                      <select name="time_zone" id="time_zone" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                     </select>
                  </div>
                  <div class="col-lg-6 form-group w-full">
                      <label class="form-label">Date Format</label>
                      <select name="date_format" class="form-control form-select js-example-basic-single">
                        <?php foreach($dateformatlist as $valuedates) { ?>
                          <option value="<?php echo $valuedates;?>"><?php echo $valuedates; ?></option>
                        <?php } ?>
                     </select>
                     </div> <?php **/ ?>
                 </div>
                 <div class="footer_btn">
                 	<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(2)"><i class="ti-arrow-left"></i> Previous Step</button>
                 	<button type="button"  class="btn btn-primary" onclick="nextsetupview(4)"><i class="ti-arrow-right"></i>  Next Step</button>
                 </div>
             </div>
         </div>
         <div class="col-lg-6 text-center position-relative"> 
         	<div class="top-logo p-2">
         		<a href="">
         			<img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">  
         		</a>
         	</div>
         	<div class="text-position text-start">
  						<span class="f-30 f-w-600">Welcome to</span>
  						<h2 class="f-50 f-w-600">Localization<br />Settings</h2>
  						<p class="f-14 f-w-600">Choose your preferred currency and locale settings. This ensures that all financial transactions and system displays are aligned with your regional preferences.</p>
  						<button class="btn btn-primary">infistaq.com</button>
  				</div>
         	<img class="" src="assets/images/setup_welcome_compliance.png">
         </div>
     </div>
 </div>
</div>


<div id="setup4" style="display:none;" >
	<div class="taskda-bg">
		<div class="row align-items-center">
			<div class="col-lg-6 p-l-50 p-r-50 bg-white">
				<div class="m-20">
					<h3 class="f-w-600 mb-5">Tax Information</h3>
					<div class="row">
						<div class="form-group col-lg-6">
							<label class="form-label">License Number</label>
							<input type="text" class="form-control"  name="license_number" placeholder="<?php echo $placeholder['licensenumber'];?>">
						</div>
						<div class="form-group col-lg-6">
							<label class="form-label">PAN Number</label>
							<input type="text" class="form-control"  name="pan_number" placeholder="<?php echo $placeholder['licensenumber'];?>">
						</div>
						<div class="form-group col-lg-6">
							<label class="form-label">TAN Number</label>
							<input type="text" class="form-control"  name="tan_number" placeholder="<?php echo $placeholder['licensenumber'];?>">
						</div>
						<div class="form-group col-lg-6">
							<label class="form-label">GST Number</label>
							<input type="text" class="form-control"  name="gst_number" placeholder="<?php echo $placeholder['licensenumber'];?>">
						</div>
						<div class="form-group col-lg-6">
							<label class="form-label">CIN Number</label>
							<input type="text" class="form-control"  name="cin_number" placeholder="<?php echo $placeholder['licensenumber'];?>">
						</div>
					</div>
					<div class="footer_btn">
						<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(3)"><i class="ti-arrow-left"></i> Previous Step</button>
						<button type="button"  class="btn btn-primary" onclick="nextsetupview(5)"><i class="ti-arrow-right"></i>  Next Step</button>
					</div>
				</div>
			</div>
			<div class="col-lg-6 text-center position-relative"> 
				<div class="top-logo p-2">
					<a href="">
						<img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">  
					</a>
				</div>
				<div class="text-position text-start">
  						<span class="f-30 f-w-600">Welcome to</span>
  						<h2 class="f-50 f-w-600">Localization<br />Settings</h2>
  						<p class="f-14 f-w-600">Choose your preferred currency and locale settings. This ensures that all financial transactions and system displays are aligned with your regional preferences.</p>
  						<button class="btn btn-primary">infistaq.com</button>
  				</div>
				<img class="" src="assets/images/setup_welcome_compliance.png">
			</div>
		</div>
	</div>
</div>

<div id="setup5" style="display:none;" >
	<div class="taskda-bg">
		<div class="row align-items-center">
			<div class="col-lg-6 p-l-50 p-r-50 bg-white">
				<div class="m-20">
					<h3 class="f-w-600 mb-5">System Configuration</h3>
					<div class="row">
						<div class="col-lg-6 form-group w-full">
		                      <label class="form-label">Time Zone</label>
		                      <select name="time_zone" id="time_zone" class="form-control form-select js-example-basic-single">
		                        <option value="">Select Any One</option>
		                     </select>
		                 </div>
		                <div class="col-lg-6 form-group w-full">
		                      <label class="form-label">Date Format</label>
		                      <select name="date_format" class="form-control form-select js-example-basic-single">
		                        <?php foreach($dateformatlist as $valuedates) { ?>
		                          <option value="<?php echo $valuedates;?>"><?php echo $valuedates; ?></option>
		                        <?php } ?>
		                     </select>
		                </div>
						<div class="form-group col-lg-6">
							<label class="form-label">Number Format</label>
							<select name="number_format" class="form-control form-select js-example-basic-single" >
								<option value="">Select Any One</option>
							</select>
						</div>
						<div class="form-group col-lg-6">
							<label class="form-label">Default Currency</label>
							<select name="default_currency_id" class="form-control form-select js-example-basic-single" >
								<option value="">Select Any One</option>
							</select>
						</div>
						<div class="form-group col-lg-6">
							<label class="form-label">Currency Format</label>
							<select name="currency_formate" class="form-control form-select js-example-basic-single" >
								<option value="">Select Any One</option>
							</select>
						</div>
					</div>
					<div class="footer_btn">
						<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(4)"><i class="ti-arrow-left"></i> Previous Step</button>
						<button type="button"  class="btn btn-primary" onclick="nextsetupview(6)"><i class="ti-arrow-right"></i>  Next Step</button>
					</div>
				</div>
			</div>
			<div class="col-lg-6 text-center position-relative"> 
				<div class="top-logo p-2">
					<a href="">
						<img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">  
					</a>
				</div>
				<div class="text-position text-start">
  						<span class="f-30 f-w-600">Welcome to</span>
  						<h2 class="f-50 f-w-600">Localization<br />Settings</h2>
  						<p class="f-14 f-w-600">Choose your preferred currency and locale settings. This ensures that all financial transactions and system displays are aligned with your regional preferences.</p>
  						<button class="btn btn-primary">infistaq.com</button>
  				</div>
				<img class="" src="assets/images/setup_welcome_compliance.png">
			</div>
		</div>
	</div>
</div>

<div id="setup6" style="display:none;" >
	<div class="taskda-bg">
		<div class="row align-items-center">
			<div class="col-lg-6 p-l-50 p-r-50 bg-white">
				<div class="m-20">
					<h3 class="f-w-600 mb-5">Locations</h3>
					<div class="row">
						<div class="col-lg-6">
							<label class="form-label">Locations Name</label>
							<input type="text" class="form-control" name="location_name[]">
						</div>
						<div class="col-lg-6">
							<label class="form-label">Locations Short Name</label>
							<input type="text" class="form-control" name="short_name[]">
						</div>
					</div>
					<div id="newaddmorelocation"></div>
					<div class="mt-3 mb-5">
						<button type="button"  class="btn btn-primary" onclick="addMoreLocation()"><i class="ti-plus"></i>  Add More</button>
					</div>
					<div class="footer_btn">
						<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(5)"><i class="ti-arrow-left"></i> Previous Step</button>
						<button type="submit" name="submit" class="btn btn-primary"><i class="ti-thumb-up"></i> Finished</button>
					</div>
				</div>
			</div>
			<div class="col-lg-6 text-center position-relative"> 
				<div class="top-logo p-2">
					<a href="">
						<img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">  
					</a>
				</div>
				<div class="text-position text-start">
  						<span class="f-30 f-w-600">Welcome to</span>
  						<h2 class="f-50 f-w-600">Localization<br />Settings</h2>
  						<p class="f-14 f-w-600">Choose your preferred currency and locale settings. This ensures that all financial transactions and system displays are aligned with your regional preferences.</p>
  						<button class="btn btn-primary">infistaq.com</button>
  				</div>
				<img class="" src="assets/images/setup_welcome_compliance.png">
			</div>
		</div>
	</div>
</div>

</form>


<!-- Warning Section Ends -->
<!-- Required Jquery -->
<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script>
<!-- waves js -->
<script src="assets/pages/waves/js/waves.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
<!-- modernizr js -->
<script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
<script type="text/javascript" src="assets/js/common-pages.js"></script>

<script type="text/javascript" src="assets/bower_components/bootstrap-tagsinput/js/bootstrap-tagsinput.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/typeahead.bundle.min.js"></script>

<!-- Select 2 js -->
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
<!-- Multiselect js -->
<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/moment-with-locales.min.js"></script>


<script type="text/javascript" src="assets/js/custom.js"></script>

<!-- sweet alert js Rohan Added-->
<script type="text/javascript" src="assets/bower_components/sweetalert/js/sweetalert.min.js"></script>
</body>

</html>
