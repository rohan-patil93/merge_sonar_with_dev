<?php 
$_SESSION['apply_js']['multiselest'] = "1";
ini_set('display_errors' ,0);
include('includes/session.php');
include('dbconfig.php');
include('comm.php');
if($_GET['setup'] == 'F'){
   unset($_SESSION['login_details']);         
        ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
              swal({
                  title: "Success",
                  text: '<?php echo $_SESSION['error_message']['9096']; ?>',
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
               setTimeout(function() {window.location.href = 'index.php'; }, 3000);
            } 
        }
    </script>
    <?php
}else{

	$ptapi = API."?mode=getsetupdetails"; 
	$callptapi = callAPI($ptapi);
	$result = json_decode($callptapi,1);
	$countries = $result['countries'];
	$company_types = $result['company_types'];
	$companies = $result['companies'];
	$config = $result['config'];
	$industrylist = $result['industries'];
	$currencies = $result['currencies'];
	$modulelists =  $result['module_setup'];
	$states =  $result['states'];
	$cities =  $result['cities'];
	$setup_pages = $result['setup_pages'];
	$setup_page_logs = $result['setup_page_logs'];
	if($setup_page_logs['current_status'] == ST_APPROVED){
		header("Location: index.php");
	}
	$lastsetup_page_id = $setup_page_logs['setup_page_id'];
	$setuppages = array();
	$seqpageary = array();
	foreach((array)$modulelists as $modulelist){
		if($modulelist['id'] != '1' AND $modulelist['linking'] == '1'){
			$allmodule[$modulelist['module_id']] = $modulelist;
		}
		if($modulelist['is_active'] == '0'){
			$activemodule[$modulelist['module_id']] = $modulelist['module_id'];
			$setuppa = json_decode($modulelist['setup_pages'],1);
			if(count((array)$setuppa) !='0'){
				$seqpageary = array_unique(array_merge($seqpageary,$setuppa));		
			}
		} 
	}
	sort($seqpageary);
	$module_setup_linkings =  $result['module_setup_linkings'];
	foreach((array)$module_setup_linkings as $linking){
		$linkingmodules[$linking['parent_module_id']][] = $linking;
	}
	foreach((array)$setup_pages as $setpage){
		if($setpage['parent_sequence'] == '0'){
			$setuppages[$setpage['sequence']][] = $setpage;
		}else{
			$setuppages[$setpage['parent_sequence']][] = $setpage;
		}
	}
	$pagedetail = $setuppages[$seqpageary[0]][0];	
	$nextseqstartnumber = $pagedetail['sequence'];

	if($config['id'] !=""){
	  // header("location:index.php");
	}
	//echo "nextseqstartnumber   --  <pre>"; print_r($setup_page_logs); echo "</pre>";
	$lastsetupnumber = 6;  
	$dateformatlist = array('YYYY-MM-DD','YYYY/MM/DD','DD-MM-YYYY','MM-DD-YYYY','MM/DD/YYYY','DD/MM/YYYY');
	//$currency_format = array('L'=>'Lakhs (99,99,99,999.99)','M'=>'Million (999,999,999.99)');
	$currency_format = array('1'=>'Lakhs (0)','1.1'=>'Lakhs (0.0)','1.2'=>'Lakhs (0.00)','1.3'=>'Lakhs (0.000)','1.4'=>'Lakhs (0.0000)','2'=>'Millians (0)','2.1'=>'Millians (0.0)','2.2'=>'Millians (0.00)','2.3'=>'Millians (0.000)','2.4'=>'Millians (0.0000)');
	$system_message = array('welcome_tab'=>array('header'=>'COMPANY</br> INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'company_details'=>array('header'=>'COMPANY </br>INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'contact_details'=>array('header'=>'CONTACT </br>INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'tax_information'=>array('header'=>'TAX</br> INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'system_configuration'=>array('header'=>'LOCALIZATION </br>INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'locations'=>array('header'=>'Location </br>INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'));
	$date_format = str_replace('-',$_SESSION['config']['date_separator'], strtolower($_SESSION['config']['date_format']));
	$_SESSION['date_formate'] = str_replace('yyyy', 'Y', str_replace('mm', 'm', str_replace('dd', 'd', $date_format)));
	$date_format = str_replace('yyyy', 'yy',$date_format);
}


/**
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
} **//**
$ptapi = API."?mode=getsetupdetails"; 
$callptapi = callAPI($ptapi);
$result = json_decode($callptapi,1);
$countries = $result['countries'];
$company_types = $result['company_types'];
$companies = $result['companies'];
$config = $result['config'];
$industrylist = $result['industries'];
$currencies = $result['currencies'];
$modulelists =  $result['module_setup'];
$states =  $result['states'];
$cities =  $result['cities'];
$setup_pages = $result['setup_pages'];
$setup_page_logs = $result['setup_page_logs'];
if($setup_page_logs['current_status'] == ST_APPROVED){
	header("Location: index.php");
}
$lastsetup_page_id = $setup_page_logs['setup_page_id'];
$setuppages = array();
$seqpageary = array();
foreach($modulelists as $modulelist){
	if($modulelist['id'] != '1' AND $modulelist['linking'] == '1'){
		$allmodule[$modulelist['module_id']] = $modulelist;
	}
	if($modulelist['is_active'] == '0'){
		$activemodule[$modulelist['module_id']] = $modulelist['module_id'];
		$setuppa = json_decode($modulelist['setup_pages'],1);
		if(count((array)$setuppa) !='0'){
			$seqpageary = array_unique(array_merge($seqpageary,$setuppa));		
		}
	} 
}
//echo "<pre>"; print_r($seqpageary);echo "</pre>";

//asort($seqpageary);
//echo "<pre>"; print_r($seqpageary);echo "</pre>";

$module_setup_linkings =  $result['module_setup_linkings'];
foreach($module_setup_linkings as $linking){
	$linkingmodules[$linking['parent_module_id']][] = $linking;
}
foreach($setup_pages as $setpage){
	if($setpage['parent_id'] == '0'){
		$setuppages[$setpage['id']][] = $setpage;
	}else{
		$setuppages[$setpage['parent_id']][] = $setpage;
	}
}
$pagedetail = $setuppages[$seqpageary[0]][0];	
$nextseqstartnumber = $pagedetail['sequence'];

if($config['id'] !=""){
  // header("location:index.php");
}
//echo "nextseqstartnumber   --  <pre>"; print_r($setup_page_logs); echo "</pre>";
$lastsetupnumber = 6;
$dateformatlist = array('YYYY-MM-DD','YYYY/MM/DD','DD-MM-YYYY','MM-DD-YYYY','MM/DD/YYYY','DD/MM/YYYY');
$currency_format = array('99,99,99,999.99','999,999,999.99');
$system_message = array('welcome_tab'=>array('header'=>'COMPANY</br> INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'company_details'=>array('header'=>'COMPANY </br>INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'contact_details'=>array('header'=>'CONTACT </br>INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'tax_information'=>array('header'=>'TAX</br> INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'system_configuration'=>array('header'=>'LOCALIZATION </br>INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'),'locations'=>array('header'=>'Location </br>INFORMATION','description'=>'Enter your company details here. This information will be used across various modules and documents within the system, ensuring accurate branding and identification.'));
$date_format = str_replace('-',$_SESSION['config']['date_separator'], strtolower($_SESSION['config']['date_format']));
$_SESSION['date_formate'] = str_replace('yyyy', 'Y', str_replace('mm', 'm', str_replace('dd', 'd', $date_format)));
$date_format = str_replace('yyyy', 'yy',$date_format); **/
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
		z-index: 1;
	}  
	body{background-color: transparent; background-image: none;}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function nextsetupview(page,submit_field=""){
		/**if(submit_field !=""){
			$(".field_validate_"+submit_field).each(function() {
			    
			     console.log($(this).val());
			    var id = $(this).attr('id');
			    console.log(id);
			     if ($(this).val() !== ""){
			     		console.log("not emty");
			     	$('#'+id).prev('.error-message').text('please enter required fields');
			     }else{
			     	console.log("emty");
			     	//$('#'+id).next('.error-message').text(' ');
			     }
			});
			
		}else{
			$(".setuphide").hide('slow');
			$("#setup"+page).show('slow');
		} **//**
		console.log("#submit_form_id_"+submit_field);
		var formdetail = $("#submit_form_id_"+submit_field).serialize();
		console.log(formdetail);
		$.ajax({
			url: "ajax.php",
			data:'page=submitsetup&'+formdetail,
			type: "POST",
			success: function(response) {
				console.log(response);
				//$("#getstate").html(response);
			} 
		}); **/
	$("#ajaxloaderdiv").show();	
	if(submit_field !=""){
		var formData = new FormData();
	    formData.append("page", "submitsetup"); 
	    var dataArray = $("#submit_form_id_"+submit_field).serializeArray(),
	    dataObj = {};
	    $(dataArray).each(function(i, field){
	      formData.append(field.name, field.value); 
	    });
	    formData.append('company_id', '<?php echo $companies['id']; ?>');
	    formData.append('config_id', '<?php echo $config['id']; ?>');
	   	var display_logo = document.getElementById('display_logo').files[0];
	   	if(typeof display_logo === "undefined") {}else{
	   		formData.append("file",display_logo);
	   	}
	   	if(page ==""){
	   		formData.append("current_status",'199');
	   	}else{
	   		formData.append("current_status",'0');
	   	}
	   	jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
            	console.log(data);
               // $('#kt_modal_dynamic_popup2').modal("hide");
               tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
               	if(page !=""){
    				$(".setuphide").hide('slow');
					$("#setup"+page).show('slow');
					selectModule('','',page);
				}else{ 	
					window.location.href = "setup.php?setup=F";
				}
            },
            error:function (){}
        });

	}else{
		$(".setuphide").hide('slow');
		$("#setup"+page).show('slow');
		$("#ajaxloaderdiv").hide('slow','linear');
	} 
	
		//$(".setuphide").hide('slow');
		//$("#setup"+page).show('slow');
	}
	// function addMoreLocation(){
	// 	var html = "";
	// 	html = '<div class="row mt-3"><div class="col-lg-6"><input type="text" class="form-control" name="location_name[]"></div><div class="col-lg-6"><input type="text" class="form-control" name="short_name[]"></div></div>';
	// 	$("#newaddmorelocation").append(html);
	// }
	// function addMoreDivisions(){
	// 	var html = "";
	// 	html = '<div class="row mt-3"><div class="col-lg-12"><input type="text" class="form-control" name="location_name[]"></div></div>';
	// 	$("#newaddmoredivision").append(html);
	// }

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
	function selectModule(module_id ='', type ='', nextpageseq=''){
		var statuse = '';
		var msg = "<?php echo $_SESSION['error']['9087']; ?>";
		var onemodulecheck = 1;
		if(module_id !=""){
			$('.module_selections').each(function(){
				if($(this).is(':checked')){
					onemodulecheck = 0;
				}
			});
			if(onemodulecheck === 0) {
				$("#modulesubmitbtn").show();
			}else{
				$("#modulesubmitbtn").hide();
			}
			if($("#optionget"+module_id).is(':checked')) {
				statuse = 0;
				var msg = "<?php echo $_SESSION['error']['9088']; ?>";
			}
		}
		jQuery.ajax({
			url: "ajax.php",
	 		data:'page=updatemodulestatus&module_id='+module_id+'&statuse='+statuse+'&nextpageseq='+nextpageseq,
			type: "POST",
			success:function(data){
				console.log(data);
				//$("#nextpageadded").html(data);
			 	var res = JSON.parse(data);
				$("#nextpageadded").html(res.html);
				$("#modulesubmitbtn").attr("onclick","nextsetupview("+res.nextseqstartnumber+",6)");
				$(".js-example-basic-single").select2();
        		$(".js-example-basic-multiple").select2();
        		$("#ajaxloaderdiv").hide('slow','linear');	
				//tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
			}
		});
		if(type == "Y"){
			chieldModuleSelection(module_id);
		}
	}
	function chieldModuleSelection(module_id){
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
	function removeOrganizationSetup(idd){
		$("#"+idd).remove();
	}
</script>
<style>
.loaderajax {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid blue;
  border-right: 16px solid green;
  border-bottom: 16px solid red;
  width: 150px;
  height: 150px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
  margin: 16% 0 0 30%;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
#ajaxloaderdiv{
	min-height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    position: absolute;
    z-index: 2;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    background: #0000006e;
    color: #ffffff;
    display: none;
}
</style>
<body themebg-pattern="" class="position-relative">
		<?php if($error_message !=''){ ?>
			<div class="alert alert-danger background-danger alert-dismissible">
				<strong><?php echo $error_message; ?></strong>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>  
		<?php } ?>
		<div id="ajaxloaderdiv">
			<div class="loaderajax"></div>
		</div>
		<div class="text-right">
			<a href="index.php?option=COM_LOGOUT"><span class="f-18 m-r-50 m-t-15 text-danger">
			<i class="fa fa-power-off "></i> Log Out </span></a>
		</div>
	
		<!-- Pre- setup1  Start-->
		<div id="setup1" class="setuphide" <?php echo ($lastsetup_page_id == '1' || $lastsetup_page_id == '')?'':'style="display:none;"'; ?>>
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

									<span class="d-block mt-3">Feel free to explore our platform, and don't hesitate to reach out if you have any questions. Welcome aboard, and here's to a successful partnership with Infistaq! </span>
								</p>
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
						<h2 class="f-50 f-w-600"><?php echo $system_message['welcome_tab']['header']; ?></h2>
						<p class="f-14 f-w-600"><?php echo $system_message['welcome_tab']['description']; ?></p>
						<button class="btn btn-primary">infistaq.com</button>
					</div>
					<img class="" src="assets/images/setup_welcome_compliance.png">
				</div>
			</div>
		</div>
		<div id="setup2" class="setuphide" <?php echo ($lastsetup_page_id == '2')?'':'style="display:none;"'; ?> >
			<div class="taskda-bg" style="">
				<div class="row align-items-center">
					<div class="col-lg-6 p-l-50 p-r-50 bg-white">
						<div class="m-20">
							<h3 class="f-w-600 mb-5">Company Details</h3>
							<form id="submit_form_id_2" enctype='multipart/form-data'>
							<div class="row mb-5">
								<div class="col-lg-6">
									<div class="form-group">
										<label class="form-label">Company Name <span class="text-danger">*</span></label>
										<span class="error-message text-danger"></span>
										<input type="text" class="form-control field_validate_2" name="name" id="name" value="<?php echo $companies['name']; ?>" readonly>
									</div>
									<div class="form-group w-full">
										<label class="form-label">Type of Company <span class="text-danger">*</span></label>
										<span class="error-message text-danger"></span>
										<select name="company_type_id" class="form-control form-select js-example-basic-single field_validate_2" id="company_type_id">
											<option value="">Select Any One </option>
											<?php foreach((array)$company_types as $ct){ ?>
												<option value="<?php echo $ct['id']; ?>" <?php echo $companies['company_type_id'] == $ct['id']?"selected":""; ?>><?php echo $ct['company_type_name']; ?></option>
											<?php } ?>
										</select>
									</div>
									<div class="form-group  w-full">
										<label>Industry <span class="text-danger">*</span></label>
										<span class="error-message text-danger"></span>
										<select class="form-select js-example-basic-single field_validate_2"  name="industry" id="industry">
											<option value="">Select Any One </option>
											<?php foreach((array)$industrylist as $valueindustry) { ?>
												<option value="<?php echo $valueindustry['id'];?>" <?php echo $config['industry'] == $valueindustry['id']?"selected":""; ?>><?php echo $valueindustry['industries']; ?></option>
											<?php } ?>
										</select> 
									</div>
								</div>
								<div class="col-lg-6 text-center">
									<div class=" form-group">
										<label class="form-label">Company logo <span class="text-danger">*</span></label>
										<div class="profile-pic mt-2">
											<label class="-label" for="display_logo">
												<span> <i class="fa fa-camera"></i> &nbsp; Upload Logo</span>
											</label>
											<input type="file" class="field_validate_2" name="image" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
											<img src="<?php echo "uploads/all/".$config['display_logo'] ; ?>" id="outputdisplay_logo" width="200" onerror="this.onerror=null;this.src='uploads/all/no-image.jpg';"/>
											<input type="hidden" name="hid_image" value="<?php echo $config['display_logo'] ;  ?>">
										</div>
										<span class="error-message text-danger d-block"></span>
									</div>
								</div>
							</div>
							<input type="hidden" name="setup_page_id" value="2">
							</form>
							<div class="footer_btn">
								<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('1')"><i class="ti-arrow-left"></i> Previous Step</button>
								<button type="button"  class="btn btn-primary" onclick="nextsetupview('3','2')"><i class="ti-arrow-right"></i>  Next Step</button>
							</div>
						</div>
					</div>
					<div class="col-lg-6 text-center position-relative">
						<div class="top-logo p-2">
							<a href="">
								<img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">  
							</a>
						</div>
						<div class="text-position text-start" >
							<span class="f-30 f-w-600">Setup</span>
							<h2 class="f-50 f-w-600"><?php echo $system_message['company_details']['header']; ?></h2>
							<p class="f-14 f-w-600"><?php echo $system_message['company_details']['description']; ?></p>
							<button class="btn btn-primary">infistaq.com</button>
						</div>
						<img class="" src="assets/images/setup_welcome_compliance.png">
					</div>
				</div>
			</div>
		</div>

		<div id="setup3" class="setuphide"  <?php echo ($lastsetup_page_id == '3')?'':'style="display:none;"'; ?> >
			<div class="taskda-bg">
				<div class="row align-items-center">
					<div class="col-lg-6 p-l-50 p-r-50 bg-white">
						<div class="m-20">
							<h3 class="f-w-600 mb-5">Contact Details</h3>
							<form id="submit_form_id_3" enctype='multipart/form-data'>
							<div class="row mb-5">
								<div class="col-lg-6 form-group">
									<label class="form-label">Contact Person Name</label>
									<input type="text" class="form-control" name="incharge_name" value="<?php echo $companies['incharge_name']; ?>">
								</div>
								<div class="col-lg-6 form-group">
									<label class="form-label">Contact Number</label>
									<input type="number" class="form-control" name="telephone" value="<?php echo $companies['telephone'] ;  ?>">
								</div>
								<div class="col-lg-6 form-group">
									<label class="form-label">Email</label>
									<input type="email" class="form-control" name="email" value="<?php echo $companies['email'] ;  ?>">
								</div>
								<div class="col-lg-6 form-group">
									<label class="form-label">Address</label>
									<textarea type="text" class="form-control" name="address1" ><?php echo $companies['address1'] ;  ?></textarea>
								</div>
								<div class="col-lg-6 form-group w-full">
									<label class="form-label d-block">Country</label>
									<select name="country" class="form-control form-select js-example-basic-single" onchange="selectCounty(this.value)" value="<?php echo $config['display_logo'] ;  ?>">
										<option value="">Select Any One</option>
										<?php 
										$timezone = "";
										foreach((array)$countries as $countr) { 
											if($companies['country'] ==$countr['id']){
												$timezone = $countr['time_zones'];
											}
										?>
											<option value='<?php echo $countr['id']."||".$countr['time_zones']; ?>' <?php echo $companies['country'] == $countr['id']?"selected":""; ?> ><?php echo $countr['country_name']; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-lg-6 form-group  w-full">
									<label class="form-label d-block">State</label>
									<select name="state" class="form-control form-select js-example-basic-single" id="getstate" onchange="selectSatate(this.value)" >
										<option value="">Select Any One</option>
										<?php foreach((array)$states as $state) { ?>
											<option value='<?php echo $state['id']; ?>' <?php echo $companies['state'] == $state['id']?"selected":""; ?> ><?php echo $state['state_name']; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-lg-6 form-group  w-full">
									<label class="form-label d-block">City</label>
									<select name="city" class="form-control form-select js-example-basic-single" id="getcity">
										<option value="">Select Any One</option>
										<?php foreach((array)$cities as $city) { ?>
											<option value='<?php echo $city['id']; ?>' <?php echo $companies['city'] == $city['id']?"selected":""; ?> ><?php echo $city['city_name']; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-lg-6 form-group">
									<label class="form-label">Post Code</label>
									<input type="number" class="form-control" name="postcode" value="<?php echo $companies['postcode'] ;  ?>">
								</div>
							</div>
							<input type="hidden" name="setup_page_id" value="3">
							</form>
							<div class="footer_btn">
								<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('2')"><i class="ti-arrow-left"></i> Previous Step</button>
								<button type="button"  class="btn btn-primary" onclick="nextsetupview('4','3')"><i class="ti-arrow-right"></i>  Next Step</button>
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
							<span class="f-30 f-w-600">Setup</span>
							<h2 class="f-50 f-w-600"><?php echo $system_message['contact_details']['header']; ?></h2>
							<p class="f-14 f-w-600"><?php echo $system_message['contact_details']['description']; ?></p>
							<button class="btn btn-primary">infistaq.com</button>
						</div>
						<img class="" src="assets/images/setup_welcome_compliance.png">
					</div>
				</div>
			</div>
		</div>

		<div id="setup4"  class="setuphide"  <?php echo ($lastsetup_page_id == '4')?'':'style="display:none;"'; ?>  >
			<div class="taskda-bg">
				<div class="row align-items-center">
					<div class="col-lg-6 p-l-50 p-r-50 bg-white">
						<div class="m-20">
							<h3 class="f-w-600 mb-5">Tax Details</h3>
							<form id="submit_form_id_4" enctype='multipart/form-data'>
							<div class="row">
								<div class="form-group col-lg-6">
									<label class="form-label">License Number</label>
									<input type="text" class="form-control"  name="liscense_number" value="<?php echo $config['liscense_number'];?>">
								</div>
								<div class="form-group col-lg-6">
									<label class="form-label">PAN Number</label>
									<input type="text" class="form-control"  name="pan_number" value="<?php echo $config['pan_number'];?>">
								</div>
								<div class="form-group col-lg-6">
									<label class="form-label">TAN Number</label>
									<input type="text" class="form-control"  name="tan_number" value="<?php echo $config['tan_number'];?>">
								</div>
								<div class="form-group col-lg-6">
									<label class="form-label">GST Number</label>
									<input type="text" class="form-control"  name="gst_number" value="<?php echo $config['gst_number'];?>">
								</div>
								<div class="form-group col-lg-6">
									<label class="form-label">CIN Number</label>
									<input type="text" class="form-control"  name="cin_number" value="<?php echo $config['cin_number'];?>">
								</div>
							</div>
							<input type="hidden" name="setup_page_id" value="4">
							</form>
							<div class="footer_btn">
								<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('3')"><i class="ti-arrow-left"></i> Previous Step</button>
								<button type="button"  class="btn btn-primary" onclick="nextsetupview('5','4')"><i class="ti-arrow-right"></i>  Next Step</button>
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
							<span class="f-30 f-w-600">Setup</span>
							<h2 class="f-50 f-w-600"><?php echo $system_message['tax_information']['header']; ?></h2>
							<p class="f-14 f-w-600"><?php echo $system_message['tax_information']['description']; ?></p>
							<button class="btn btn-primary">infistaq.com</button>
						</div>
						<img class="" src="assets/images/setup_welcome_compliance.png">
					</div>
				</div>
			</div>
		</div>

		<div id="setup5" class="setuphide" <?php echo ($lastsetup_page_id == '5')?'':'style="display:none;"'; ?>>
			<div class="taskda-bg">
				<div class="row align-items-center">
					<div class="col-lg-6 p-l-50 p-r-50 bg-white">
						<div class="m-20">
							<h3 class="f-w-600 mb-5">Localization Settings</h3>
							<form id="submit_form_id_5" enctype='multipart/form-data'>
							<div class="row">
								<div class="col-lg-6 form-group w-full">
									<label class="form-label">Time Zone</label>
									<select name="time_zone" id="time_zone" class="form-control form-select js-example-basic-single">
										<option value="">Select Any One</option>
										<?php $timezones = json_decode($timezone, 1);
										foreach((array)$timezones as $tz) { ?>
											<option value='<?php echo $tz['zoneName']; ?>' <?php echo  $tz['zoneName'] ==$config['time_zone']?"selected":""; ?>><?php echo $tz['zoneName']; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-lg-6 form-group w-full">
									<label class="form-label">Date Format</label>
									<select name="date_format" class="form-control form-select js-example-basic-single">
										<?php foreach((array)$dateformatlist as $valuedates) { ?>
											<option value="<?php echo $valuedates;?>" <?php echo  $valuedates ==$config['date_format']?"selected":""; ?>><?php echo $valuedates; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group col-lg-6">
									<label class="form-label">Number Format</label>
									<select name="number_format" class="form-control form-select js-example-basic-single" >
										<option value="">Select Any One</option>
										<?php foreach((array)$currency_format as $keyu=>$cformat) { ?>
											<option value="<?php echo $keyu; ?>" <?php echo  $keyu ==$config['number_format']?"selected":""; ?>><?php echo $cformat; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group col-lg-6"> 
									<label class="form-label">Default Currency</label>
									<select name="default_currency_id" class="form-control form-select js-example-basic-single" >
										<option value="">Select Any One</option>
										<?php foreach((array)$currencies as $currency) { ?>
											<option value="<?php echo $currency['id']; ?>" <?php echo  $currency['id'] ==$config['default_currency_id']?"selected":""; ?>><?php echo $currency['currency_list']; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group col-lg-6"> 
									<label class="form-label">Currency Format</label>
									<select name="currency_formate" class="form-control form-select js-example-basic-single" >
										<option value="">Select Any One</option>
										<?php foreach((array)$currency_format as $keyu=>$cformat) { ?>
											<option value="<?php echo $keyu; ?>"  <?php echo  $keyu ==$config['currency_formate']?"selected":""; ?>><?php echo $cformat; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<input type="hidden" name="setup_page_id" value="5">

						</form>
							<div class="footer_btn">
								<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('4')"><i class="ti-arrow-left"></i> Previous Step</button>
								<button type="button"  class="btn btn-primary" onclick="nextsetupview('6','5')"><i class="ti-arrow-right"></i>  Next Step</button>
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
							<span class="f-30 f-w-600">Setup</span>
							<h2 class="f-50 f-w-600"><?php echo $system_message['system_configuration']['header']; ?></h2>
							<p class="f-14 f-w-600"><?php echo $system_message['system_configuration']['description']; ?></p>
							<button class="btn btn-primary">infistaq.com</button>
						</div>
						<img class="" src="assets/images/setup_welcome_compliance.png">
					</div>
				</div>
			</div>
		</div>
		<?php /**/ ?>
		<div id="setup6" class="setuphide" <?php echo ($lastsetup_page_id == '6')?'':'style="display:none;"'; ?> >
			<div class="taskda-bg">
				<div class="row align-items-center">
					<div class="col-lg-6 p-l-50 p-r-50 bg-white ">
						<div class="m-20">
							<h3 class="f-w-600 mb-5">Modul Setup</h3>
							<div class="row custom-scroll" style="max-height: 500px;">
								<?php 
								$minoneactive = 1; // not active
								foreach((array)$allmodule as $modul){ 
									if($modul['linking'] == 1){
										$childModul = "N"; 
										if(isset($linkingmodules[$modul['module_id']])){ 
											$childModul = "Y";
										}
										if(isset($activemodule[$modul['module_id']])){
											$minoneactive = 0; // active
										}
										
										?>
									<div class="col-lg-4 position-relative" >
										<div class="card social-card">
											<div class="inputGroup text-center ">
												<input class="module_selections" id="optionget<?php echo $modul['module_id']; ?>" name="module_id[]" type="checkbox" <?php echo isset($activemodule[$modul['module_id']])?"checked":""; ?> value="<?php echo $modul['module_id']; ?>" onclick="selectModule('<?php echo $modul['module_id']; ?>','<?php echo $childModul; ?>')">
												<label for="optionget<?php echo $modul['module_id']; ?>" class="text-center fixheght">
													<span class="text-facebook">
														<?php if($modul['module_logo'] !=""){ ?>
															<img src='uploads/all/<?php echo $modul['module_logo']; ?>' class="img-40" style="height: auto;">
														<?php }else{ ?>

														<?php  } ?>
													</span>
													<h3 class="text-facebook f-w-700 f-16 mb-0 mt-2"><?php echo $modul['module_name']; ?></h3>
													<p class="f-10 mb-0"><?php echo $modul['display_name']; ?></p>
												</label>
											</div>
										</div>
										<div class="custom-popup-open custom-popup-slide-<?php echo $modul['module_id']; ?> text-start" style="left:227px; top:0px; display: none; z-index: 999; width : 600px;">
										</div>
									</div>	
								<?php }  } ?>
							</div>
							<form id="submit_form_id_6" enctype='multipart/form-data'>
								<input type="hidden" name="setup_page_id" value="6">
							</form>
							<div class="footer_btn">
								<button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('5')"><i class="ti-arrow-left"></i> Previous Step</button>
								
								<button type="button" id="modulesubmitbtn"  class="btn btn-primary" onclick="nextsetupview('<?php echo $nextseqstartnumber; ?>','6')" <?php echo $minoneactive !=0?"style='display:none'":"";?>><i class="ti-arrow-right"></i>  Next Step</button>
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
							<span class="f-30 f-w-600">Setup <?php echo $minoneactive; ?></span>
							<h2 class="f-50 f-w-600"><?php echo $system_message['system_configuration']['header']; ?></h2>
							<p class="f-14 f-w-600"><?php echo $system_message['system_configuration']['description']; ?></p>
							<button class="btn btn-primary">infistaq.com</button>
						</div>
						<img class="" src="assets/images/setup_welcome_compliance.png">
					</div>
				</div>
			</div>
		</div>
		<div id="nextpageadded">
		<?php 
			/**

			$previousseqnumber = "6";
			$nextpagesqeq = "0";
			foreach($setuppage as $key=>$spage){
				//echo "setups/".$spage['page_name'].".php"; 
				$nextpagesqeq++;
				$nextseqstartnumber = $setuppage[$nextpagesqeq]['seq'][0];
				include("setups/".$spage['page_name'].".php");
				$previousseqnumber = end($spage['seq']);
			}  **/
			/**/

			$previousseqnumber = "6";
			foreach((array)$seqpageary as $nextkey=>$spage){
				$pagedetail = $setuppages[$spage];
				$nextkeys = $nextkey+1;
				if(isset($seqpageary[$nextkeys])){
					$nextseqstartnumber = $setuppages[$seqpageary[$nextkeys]][0]['sequence'];
				}else{
					$nextseqstartnumber = '';
				}
				include("setups/".$pagedetail[0]['page_name'].".php");
				$previousarray = end($pagedetail);
				$previousseqnumber = $previousarray['sequence'];
			}  /**/ 
		?>
		</div>
		<?php ///include("setups/product_and_service_setup.php"); ?>




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

			<script type="text/javascript" src="assets/js/bootstrap-growl.min.js"></script>
	<script type="text/javascript" src="assets/pages/notification/notification.js"></script>

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
		<script type="text/javascript">
			 var DateFormate = <?php echo "'".$date_format."'" ?>;
		</script>
			<!-- Date picker js -->
    <script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
	<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>

		<script type="text/javascript" src="assets/js/custom.js"></script>

		<!-- sweet alert js Rohan Added-->
		<script type="text/javascript" src="assets/bower_components/sweetalert/js/sweetalert.min.js"></script>

		<!-- Masking js -->
<script src="assets/pages/form-masking/inputmask.js"></script>
<script src="assets/pages/form-masking/jquery.inputmask.js"></script>
<script src="assets/pages/form-masking/autoNumeric.js"></script>
<script src="assets/pages/form-masking/form-mask.js"></script>


		<script type="text/javascript">
			$(document).ready(function() {
		       	$('[data-bs-toggle="tooltip"]').tooltip();
		    });

		    $(document).ready(function() {
		        $('[data-bs-toggle="popover"]').popover({
		            html: true,
		            content: function() {
		                return $('#primary-popover-content').html();
		            }
		        });
		    });
		</script>
	</body>

	</html>
