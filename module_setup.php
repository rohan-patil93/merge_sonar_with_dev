<?php
ini_set('display_errors' ,0);
include('includes/session.php');
include('dbconfig.php');
include('comm.php');

if(isset($_POST['submit'])){ 
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	if(count($_POST['module_id']) !=0){
		$url = API."?mode=updatemoduleselection&method=post";
		$response = file_post_contents($url, $_POST, 'insert');
		$response1 = json_decode($response,1);
		//echo "<pre>";print_r($response1);echo "</pre>";
		$_SESSION['sbt_message'] = "1";
		unset($_SESSION['login_details']);
		unset($_SESSION['application_menu']);
		header("location:infistaq_setup.php");

	}else{ ?>
		<script type="text/javascript">
			document.onreadystatechange = function () {
			  if (document.readyState == "complete") {
			    swal({
				  icon: 'warning',
				  title: 'Oops...',
				  text: '<?php echo $_SESSION['error_message']['9048'];?>'
				});
			  } 
			}
			</script>
<?php	}
}
$modulesetupwh["where"] = array('company_id'=>$_SESSION['company_id']);
$modlueencode = urlencode(json_encode($modulesetupwh));
$moduleurl = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$modlueencode;
$moduleapi = callAPI($moduleurl);
$modulelists = json_decode($moduleapi,1);

foreach($modulelists as $modulelist){
	//if($modulelist['module_url'] != '' AND $modulelist['id'] != '1'){
	if($modulelist['id'] != '1' AND $modulelist['linking'] == '1'){
		$allmodule[$modulelist['module_id']] = $modulelist;
	}
	if($modulelist['is_active'] == '0'){
		$activemodule[$modulelist['module_id']] = $modulelist['module_id'];
	} 
}

$modulesetuplinkwh["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$modulesetuplinkwh["field"] = "parent_module_id,module_id";
$modluelinkencode = urlencode(json_encode($modulesetuplinkwh));
$modulelinkurl = API."?mode=gettabledetailswithconditions&table=module_setup_linkings&data=".$modluelinkencode;
$modulelinkapi = callAPI($modulelinkurl);
$modulelinklists = json_decode($modulelinkapi,1);
foreach($modulelinklists as $linking){
	$linkingmodules[$linking['parent_module_id']][] = $linking;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Recruitstaq | Login</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="Bootstrap admin template made using Bootstrap 5 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
	<meta name="keywords" content="bootstrap, bootstrap admin template, admin theme, admin dashboard, dashboard template, admin template, responsive" />
	<meta name="author" content="colorlib" />
	<!-- Favicon icon -->

	<link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
	<!-- Google font-->     
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet"><link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
	<!-- Required Fremwork -->
	<link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
	<!-- waves.css -->
	<link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all"><!-- feather icon --> <link rel="stylesheet" type="text/css" href="assets/icon/feather/css/feather.css">
	<!-- themify-icons line icon -->
	<link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
	<!-- ico font -->
	<link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">
	<!-- Style.css -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css"><link rel="stylesheet" type="text/css" href="assets/css/pages.css">
	<link rel="stylesheet" type="text/css" href="assets/css/animate.css">

	<link rel="stylesheet" type="text/css" href="assets/css/custom.css">
	<link rel="stylesheet" type="text/css" href="assets/css/custom_mobile.css">
	  <!-- sweet alert framework -->
    <link rel="stylesheet" type="text/css" href="assets/bower_components/sweetalert/css/sweetalert.css">
  <!-- sweet alert framework -->
	<style>
		html {  height: 100%;}
		.fixheght{
			min-height: 130px;
			max-height: 130px;
		}
	</style>
</head>
<script type="text/javascript">
	function chieldModuleSelection(module_id){
		console.log(module_id);
		if ($("#optionget"+module_id).is(':checked')) {
			console.log('yes');
			jQuery.ajax({
				url: "views/module_setup_popup.php",
		 		data:'module_id='+module_id,
				type: "POST",
				success:function(data){
					$(".custom-popup-slide-"+module_id).html(data);
					$(".custom-popup-slide-"+module_id).show('slow');
				}
			});
		}else{
			closeModuleSelection();
		}
	}
	function closeModuleSelection(){
		$(".custom-popup-open").hide('slow');	
		$(".custom-popup-open").html('');	
	}
	// function submitModule(){
	// 	var modules = [];
	// 	$('.module_selections').each(function(){
	// 		if($(this).is(':checked')){
	// 			modules.push($(this).val());
	// 		}
	// 	});
	// 	if(modules.length === 0) {
	//     	swal({
	// 		  icon: 'warning',
	// 		  title: 'Oops...',
	// 		  text: '<?php echo $_SESSION['error_message']['9048'];?>'
	// 		});
	// 		//return false;
	// 	}else{

	// 	}
	// }
</script>
<div class="p-5">
	<form method="post" id="moduleform">
		<div class="card">
			<div class="card-header">
				<h5>Module Setup</h5>
				<div class="card-header-right">
				</div>
			</div>
			<div class="card-body">
				<div class="row">

					<?php 
					//echo "<pre>";  print_r($allmodule); echo "</pre>";
					foreach($allmodule as $modul){ if($modul['linking'] == 1){ ?>
						<div class="col-lg-2 position-relative" >
							<div class="card social-card">
								<div class="inputGroup text-center ">
			<input class="module_selections" id="optionget<?php echo $modul['module_id']; ?>" name="module_id[]" type="checkbox" <?php echo isset($activemodule[$modul['module_id']])?"checked":""; ?> value="<?php echo $modul['module_id']; ?>" <?php if(isset($linkingmodules[$modul['module_id']])){ ?> onclick="chieldModuleSelection('<?php echo $modul['module_id']; ?>')" <?php } ?>>
									<label for="optionget<?php echo $modul['module_id']; ?>" class="text-center fixheght">
										<span class="text-facebook">
											<?php if($modul['module_logo'] !=""){ ?>
												<img src='uploads/module/<?php echo $modul['module_logo']; ?>' class="img-60" style="height: auto;">
											<?php }else{ ?>
												
											<?php  } ?>
										</span>
										<h3 class="text-facebook f-w-700 f-16 mb-0 mt-2"><?php echo $modul['module_name']; ?></h3>
										<p class="f-10 mb-0"><?php echo $modul['display_name']; 
										//if(count($linkingmodules[$modul['module']])) ?></p>
									</label>
								</div>
							</div>
							<div class="custom-popup-open custom-popup-slide-<?php echo $modul['module_id']; ?> text-start" style="left:227px; top:0px; display: none; z-index: 999; width : 600px;">
							</div>
						</div>	
						<?php if(isset($linkingmodules[$modul['module_id']])){ ?>
						
					<?php } ?>
					<?php }  }?>
				</div>
			</div>
			<div class="card-footer">
				<button type="submit" class="btn btn-primary btn-shadow-hover font-weight-bold" name="submit" ><i class="la la-check"></i>Submit</button>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script>
<!-- waves js -->
<script src="assets/pages/waves/js/waves.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
<!-- modernizr js -->

<!-- sweet alert js Rohan Added-->
<script type="text/javascript" src="assets/bower_components/sweetalert/js/sweetalert.min.js"></script>
<script type="text/javascript" src="assets/js/modal.js"></script>
<!-- sweet alert  js Rohan Added-->
<script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
<script type="text/javascript" src="assets/js/common-pages.js"></script>
<script type="text/javascript" src="assets/js/script.js"></script>
<script>

</script>

</body>

</html>
